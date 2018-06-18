<?php
namespace ProjectData;

require_once("Database.php");
require_once("LoginSystem.php");
use ArrayObject;
use DB;
use LS;

class Rule extends ArrayObject {
    public function onSuccess($func) {
        $this->success = $func;
    }

    public function onValidate($func) {
        $this->validate = $func;
    }

    public function submit(&$value) {
        call_user_func($this->success, $value);
    }
}

class RuleSet extends ArrayObject {
    public function createRule($key) {
        $rule = new Rule();
        $this[$key] = $rule;
        return $rule;
    }

    // @param data - an array (key and value pairs)
    public function scan(&$data) {
        $errors = array();
        foreach ($this as $key => $rule) {
            if (array_key_exists($key, $data) && $rule instanceof ArrayObject) {
                if (property_exists($rule, "validate")) {
                    $error = call_user_func_array($rule->validate, array($rule, $data[$key]));

                    // bad code!:
                    //$error = $rule->validate($rule, $data[$key]);

                    if ($error) {
                        if (gettype($error) === "array") {
                            $errors = array_merge($errors, $error);
                        } else {
                            $errors[] = $error;
						}
                    }
                }
            }
        }
        return $errors;
    }
}

// singleton class
class Validator {
    private static $instance;
    private $errors;
    private $ruleSet;
    private $data;
    private $method;
    private $project_id;
    private $username;
    const CREATE = "CREATE";
    const UPDATE = "UPDATE";

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Validator();
        }
        return self::$instance;
    }

    // requires user ID and username!
    // @param data - an array (key and value)
    public function run(&$data) {
        if (array_key_exists("method", $data))
            $this->method = $data["method"];
        if (array_key_exists("project_id", $data)) // does not work with CREATE
            $this->project_id = $data["project_id"];
        unset($data["project_data"]);
		unset($data["project_id"]);
        unset($data["method"]);
        $this->data = $data;
        $this->errors = $this->ruleSet->scan($data);
    }

    public function containsErrors() {
        return count($this->errors);
    }

    public function getErrors() {
        return $this->errors;
    }

    // can potentially generate errors as well!
    // Does not require RuleSet, just puts custom errors in manually
    public function submitData() {
        if ($this->containsErrors() || !isset($this->data)) return false;
        $successful = true;

        if ($this->method == self::UPDATE) {
            try {
                DB::beginTransaction();
                foreach ($this->ruleSet as $key => $rule) {
                    if (array_key_exists($key, $this->data)) {
                        $rule->submit($this->data[$key]); // executes a bunch of queries
                    }
                }
                // set project to unverified!
                DB::commit();
            } catch (\PDOException $e) {
                $this->errors[] = $e->getMessage();
                DB::rollBack();
            }
        } elseif ($this->method == self::CREATE) {
            $error = $this->createProject();
            if ($error) {
                $this->errors[] = $error;
            }
        }

        unset($this->data);
        return $successful;
    }

    private function createProject() {
        $title = $this->data["project_title"];
        $description = $this->data["short_description"];
        $content = $this->data["main_description"];
        $file = $this->data["file_upload"];

        try {
            DB::beginTransaction();
            DB::query("INSERT INTO projects
                (title, author, content, description) VALUES (?, ?, ?, ?)",
                array($title, $this->username, $content, $description));

            $result = DB::query("SELECT id FROM projects WHERE title = ?", array($title));
            if ($result && $row = $result->fetch(\PDO::FETCH_ASSOC)) {
                $this->project_id = $row["id"];
            } else {
                return "Failed to get project ID from database";
            }

            DB::query("INSERT INTO project_images
                (image_id, project_id) VALUES (?, ?)",
                array(1, $this->project_id));

            $storage_folder = "../uploads/unverified_files/{$this->project_id}";
            $path = $storage_folder . "/" . basename($file["name"]);

            DB::query("INSERT INTO project_versions
                (project_id, file_path) VALUES (?, ?)",
                array($this->project_id, $path));
            DB::commit();

            // move file
            if (!file_exists($storage_folder)) {
                mkdir($storage_folder);
            }
            $success = move_uploaded_file($file["tmp_name"], $path);
            if (!$success) return "failed to transfer file";
        } catch (\PDOException $e) {
            DB::rollBack();
            return $e->getMessage();
        }
        return false;
    }

    public function sendResponse() {
        if ($this->containsErrors()) {
            echo json_encode(array("errors" => $this->getErrors()));
        } else {
            $response = array("success" => true);
            if ($this->method == self::CREATE)
                $response = array_merge($response, array("redirect_url" => "project.php?id={$this->project_id}"));
            echo json_encode($response);
        }
    }

    public function setMethod($method) {
        $this->method = $method;
    }

    // loads rules
    private function __construct() {
        $this->ruleSet = new RuleSet();
        $this->method = self::UPDATE;
        $this->project_id = 0;
        $this->username = LS::GetUsername();
        $rs = $this->ruleSet;

        /***************************************/
        $rule = $rs->createRule("project_title");
        $rule->onSuccess(function($value) {
            DB::query("UPDATE projects SET title = ?
              WHERE id = ? AND author = ?",
                array($value, $this->project_id, $this->username));
        });
        $rule->onValidate(function($rule, $value){
            if (gettype($value) === "string") { $value = strlen($value); }
            if ($value < $rule->min || $value > $rule->max) {
                return "{$rule->name} must be {$rule->min} to {$rule->max} characters long";
            }
            return false;
        });
        $rule->name = "Project title";
        $rule->min = 5; $rule->max = 50;

        /***************************************/
        $rule = $rs->createRule("project_version");
        $rule->onSuccess(function($value){
            DB::query("UPDATE project_versions SET version_name = ?
                WHERE project_id = ? AND current_version = 1", array($value, $this->project_id));
        });
        $rule->onValidate(function($rule, $value){
            if (gettype($value) === "string") { $value = strlen($value); }
            if ($value > $rule->max) {
                return "{$rule->name} must be less than {$rule->max} characters long";
            }
            return false;
        });
        $rule->name = "Project version name";
        $rule->max = 8;

        /***************************************/
        $rule = $rs->createRule("new_project_version");
        $rule->onSuccess(function($value){
            // TODO Never called because it uses method CREATE...
            // TODO Still can be verified though??
        });
        // same as project_version function (need to do jQuery matching)
        $rule->onValidate(function($rule, $value){
            if (gettype($value) === "string") { $value = strlen($value); }
            if ($value > $rule->max) {
                return "{$rule->name} must be less than {$rule->max} characters long";
            }
            return false;
        });
        $rule->name = "New project version name";
        $rule->max = 8;

        /***************************************/
        $rule = $rs->createRule("short_description");
        $rule->onSuccess(function($value){
            DB::query("UPDATE projects SET description = ?
              WHERE id = ? AND author = ?",
                array($value, $this->project_id, $this->username));
        });
        // same as project_title function
        $rule->onValidate(function($rule, $value){
            if (gettype($value) === "string") { $value = strlen($value); }
            if ($value < $rule->min || $value > $rule->max) {
                return "{$rule->name} must be {$rule->min} to {$rule->max} characters long";
            }
            return false;
        });
        $rule->name = "Short description";
        $rule->min = 10; $rule->max = 300;

        /***************************************/
        $rule = $rs->createRule("banner");
        $rule->onSuccess(function($img){
            // Move File:
            $img_name = urlencode($img["name"]);
            $storage_folder = "images/projects/{$this->project_id}";
            $path = $storage_folder . "/" . $img_name;

            if (!file_exists($storage_folder)) {
                mkdir($storage_folder);
            }
            if (move_uploaded_file($img["tmp_name"], $path)) {
                DB::query("UPDATE projects SET banner_path = ?
                  WHERE id = ? AND author = ?",
                    array($path, $this->project_id, $this->username));
            }
        });
        $rule->onValidate(function($rule, $img){
            if ($img["width"] > $rule->maxWidth || $img["height"] > $rule->maxHeight) {
                return "{$rule->name} cannot be larger than {$rule->maxWidth}px by {$rule->maxHeight}px";
            }
            // TODO ALSO need to check if it's an actual image!!!!
            return false;
        });
        $rule->name = "Banner";
        $rule->maxWidth = 1200; $rule->maxHeight = 380;

        /***************************************/
        $rule = $rs->createRule("add_video");
        $rule->onSuccess(function($value){

        });
        $rule->onValidate(function($rule, $value){
            // TODO
            return false;
        });
        $rule->name = "Video URL";
        $rule->max = 20;

        /***************************************/
        $rule = $rs->createRule("file_upload");
        $rule->onSuccess(function($value){

        });
        $rule->onValidate(function($rule, $file){
            $errors = array();
            if (gettype($file) === "string")
                $errors[] = "{$rule->name} is required";
            else {
                if ($file["size"] > $rule->max)
                    $errors[] = "{$rule->name} cannot be larger than 50M";
                if (!(strlen($file["name"]) - strlen(".zip") === strpos($file["name"], ".zip")))
                    $errors[] = "{$rule->name} must be a '.zip' file";
            }
            if (count($errors) === 0) return false; else return $errors;
        });
        $rule->name = "New file upload";
        $rule->max = 52428800;

        /***************************************/
        $rule = $rs->createRule("main_description");
        $rule->onSuccess(function($value){
            $value = nl2br($value);
            DB::query("UPDATE projects SET content = ?
              WHERE id = ? AND author = ?",
                array($value, $this->project_id, $this->username));
        });

        /***************************************/
        $rule = $rs->createRule("install_step"); // _[n]
        $rule->onSuccess(function($value){

        });

        /***************************************/
        $rule = $rs->createRule("add_image");
        $rule->onSuccess(function($img){
            // TODO image_id will ALWAYS be one until fixed in the future
            // TODO so for now I've set it to be COUNT(*):
            // $image_id = $img["id"];
            $image_id = DB::query("SELECT COUNT(*) FROM project_images
                WHERE project_id = ?", array($this->project_id));
            $image_id = $image_id->fetchColumn();
            $image_id = ((integer) $image_id) + 1;

            // move file
            $storage_folder = "images/projects/{$this->project_id}";
            $path = moveFile($img, $storage_folder);
            if ($path) {
                DB::query("INSERT INTO project_images (file_path, image_id, project_id)
                          VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE
                          file_path = ?, image_id = ?, project_id = ?",
                    array($path, $image_id, $this->project_id, $path, $image_id, $this->project_id));
            }
        });

        /***************************************/
        $rule = $rs->createRule("delete_image");
        $rule->onSuccess(function($images) {
            foreach ($images as $image_id) {
                $file_path = DB::query("SELECT file_path FROM project_images
                WHERE image_id = ? AND project_id = ?",
                    array($image_id, $this->project_id));
                $file_path = $file_path->fetchColumn();
                unlink($file_path);

                DB::query("DELETE FROM project_images
                WHERE image_id = ? AND project_id = ?",
                    array($image_id, $this->project_id));
            }
        });
        /***************************************/
        $rule = $rs->createRule("add_video");
        $rule->onSuccess(function($value){

        });

        /***************************************/
        $rule = $rs->createRule("credits");
        $rule->onSuccess(function($value){
            $value = nl2br($value);
            DB::query("UPDATE projects SET credits = ?
              WHERE id = ? AND author = ?",
                array($value, $this->project_id, $this->username));
        });

        /***************************************/
        $rule = $rs->createRule("public private"); // both?
        $rule->onSuccess(function($value, $name){

        });
    }
}