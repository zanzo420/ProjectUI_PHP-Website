<?php
header('Content-Type: text/json');
if (!array_key_exists("project_data", $_POST)) exit();
require_once("../includes/init.php");
require_once("../includes/objects/Validator.php");

if (array_key_exists("file_upload", $_FILES)) {
    $error_code = $_FILES["file_upload"]["error"];
    $errors = array();
    switch ($error_code) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            array_push($errors, "File size must be less than 50 MB");
            break;
        case UPLOAD_ERR_PARTIAL:
            array_push($errors, "The uploaded file was only partially uploaded");
            break;
        case UPLOAD_ERR_NO_FILE:
            array_push($errors, "No file was uploaded");
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            array_push($errors, "Server missing temporary folder");
            break;
        case UPLOAD_ERR_CANT_WRITE:
            array_push($errors, "Failed to write file to server");
            break;
        case UPLOAD_ERR_EXTENSION:
            array_push($errors, "Sorry, an unknown server bug occurred!");
            break;
        default:
            break;
    }
    if (count($errors) > 0) {
        echo json_encode(array("errors" => $errors));
        exit();
    }
}

// merge file data with _dimensions data (for images)
$file_data = array();
foreach ($_FILES as $key => $value) {
    if (array_key_exists($key . "_dimensions", $_POST)) {
        $dimensions = (array) json_decode($_POST[$key . "_dimensions"]);
		unset($_POST[$key . "_dimensions"]);
        $file_data[$key] = array_merge($dimensions, $value);
    } else {
        $file_data[$key] = $value;
    }
}

$data = array_merge($file_data, $_POST);
// handle add_video_[n]


foreach ($data as $key => $value) {
    if (strpos($key, "add_image") !== false) {
        // handle add_image_[n]
        $result = preg_split("/(?<=add_image)_(?=\d+$)/", $key);
        $data[$key]["id"] = $result[1];
        $data['add_image'] = $data[$key];
        unset($data[$key]);
    } elseif (strpos($key, "delete_image") !== false) {
        // handle delete_image_[n]
        $result = preg_split("/(?<=delete_image)_(?=\d+$)/", $key);
        if (!array_key_exists("delete_image", $data)) {
            // turns delete_image_[n], to delete_image = array(1, 2, ..., n)
            $data["delete_image"] = array();
        }
        array_push($data["delete_image"], $result[1]);
        unset($data[$key]);
    }
}

// never done change_log_[n]! // TODO!
$validator = ProjectData\Validator::getInstance();
$validator->run($data);
if (!$validator->containsErrors())
    $validator->submitData();
$validator->sendResponse();
exit();
