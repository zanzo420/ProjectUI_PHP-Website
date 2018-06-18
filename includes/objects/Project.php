<?php 
require_once("Database.php");

class ProjectVersion {
	public $file_path;
	public $version_name;
	public $verified;

	function __construct($version_num, $current_version = false) {
		if ($current_version) {
			$result = DB::query("SELECT * FROM project_versions WHERE current_version = 1");
		} else {
			$result = DB::query("SELECT * FROM project_versions WHERE version_id = ?;",
				array($version_num));
		}
		if ($result && $row = $result->fetch(PDO::FETCH_ASSOC)) {
			$this->file_path = $row["file_path"];
			$this->version_name = $row["version_name"];
			$this->verified = $row["verified"];
		}
	}
}

class Project {
	public $id;
	public $title;
	public $author;
	public $downloads;
	public $description;
	public $content;
	public $credits;
	public $update_date;
	public $created_date;
	public $banner_path;
	public $availability;
	public $verified;
	public $featured;
	public $request_removal;
	
	private $exists;
	
	public function __construct($id) {
		$this->id  = $id;
		$result = DB::query("SELECT * FROM projects WHERE id = ?", array($id));
		if ($result && $row = $result->fetch(PDO::FETCH_ASSOC)) {	
			$this->title = $row["title"];
			$this->author = $row["author"];
			$this->downloads = $row["downloads"];
			$this->description = $row["description"];
			$this->content = $row["content"];
			$this->credits = $row["credits"];
			$this->update_date = $row["update_date"];
			$this->created_date = $row["created_date"];
			$this->banner_path = $row["banner_path"];
			$this->availability = $row["availability"];
			$this->verified = $row["verified"];
			$this->featured = $row["featured"];
			$this->request_removal = $row["request_removal"];	
			$this->exists = true;	
		} else {
			$this->exists = false;
		}
	}

	public function getComments() {
		$result = DB::query("SELECT * FROM showcase_comments
			WHERE project_id = {$this->id}		
			ORDER BY comment_date DESC;");	

		if ($result) {			
			$comments = array();
			foreach ($result as $row) {
				$profile_picture_result = DB::query("SELECT profile_picture FROM profiles 
					WHERE username = '{$row["commenter"]}';");				
				if ($profile_picture_result && $profile_picture = $profile_picture_result->fetch(PDO::FETCH_ASSOC)) {
					$row["profile_picture"] = $profile_picture["profile_picture"];
				} else {
					$row["profile_picture"] = "images/unknown_profile_pic.jpg";
				}				
				$comments[] = $row;
			}
			return $comments;
		}	else {
			return false;
		}		
	}

	public function getCurrentVersion() {
		return new ProjectVersion(0, true);
	}

	public function getVersion($version_id) {
		return new ProjectVersion($version_id);
	}
	
	public function exists() { return $this->exists; }
	
	public function packData($complete = false) {
		$data = array(
			'id' => $this->id,				
			'title' => $this->title,
			'author' => $this->author,
			'downloads' => $this->downloads,
			'description' => $this->description,
			'banner_path' => $this->banner_path,
			'image_path' => $this->getImagePath(1),
		);
		if ($complete) {
			$data["file_path"] = $this->file_path;
			$data["update_date"] = $this->update_date;
			$data["created_date"] = $this->created_date;
			// also other images... TODO
		}		
		return $data;
	}
	
	// returns image file paths attached to the project 
	public function getImagePath($image_index = 0) {
		if ($image_index > 0 && $image_index <= 5) {
			$result = DB::query("SELECT file_path FROM project_images
					WHERE project_id = {$this->id} AND image_id = {$image_index};");
			if ($result) {
				return $result->fetch(PDO::FETCH_ASSOC)["file_path"];
			}
		}
	}
}
?>