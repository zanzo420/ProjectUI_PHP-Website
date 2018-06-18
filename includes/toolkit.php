<?php	
	class Constants {
		const PASSWORD_MIN_LEN = 6;
		const USERNAME_MIN_LEN = 4;
		const USERNAME_MAX_LEN = 20;
	}

	function start_session() {
		session_set_cookie_params(60 * 60 * 24 * 7 * 10);
		session_start();
	}
	
	function redirect($new_location = "../public/index.php") {
		session_set_cookie_params(60 * 60 * 24 * 7 * 10);
		header("Location: " . $new_location);
		exit();
	}
	
	// debugging:
	function print_array($arr) {
		echo "<pre>";
		echo print_r($arr);
		echo "</pre>";
	}
	
	function is_admin() {
		return false; // TODO
	}

	function moveFile($file, $storage_folder, $file_name = null) {
		if ($file_name) {
			$ext = pathinfo($file["name"], PATHINFO_EXTENSION);
			$file_name = "{$file_name}.{$ext}";
		} else { $file_name = $file["name"]; }
		$file_name = urlencode($file_name);
		$path = $storage_folder . "/" . $file_name;
		if (!file_exists($storage_folder)) {
			mkdir($storage_folder);
		}
		if (move_uploaded_file($file["tmp_name"], $path))
			return $path;
		return false;
	}

	function getUnviewedProject() {
		if (!LS::IsLoggedIn()) return false;
		
		// selects all id's from projects if they have
		// not been viewed (stored inside projects_viewed)
		$result = DB::query("
			SELECT p.id FROM projects p
			LEFT JOIN projects_viewed pv ON
				p.id = pv.project_id AND pv.username = ?
			WHERE pv.project_id IS NULL
		", array(LS::GetUsername()));

		// PDO driver rowCount does not work correctly so 2nd query is needed
		$num_rows = DB::query("
			SELECT count(*) FROM projects p
			LEFT JOIN projects_viewed pv ON
				p.id = pv.project_id AND pv.username = ?
			WHERE pv.project_id IS NULL
		", array(LS::GetUsername()));
		$num_rows = $num_rows->fetchColumn();		
		
		if ($num_rows > 0) {
			$random_index = mt_rand(1, $num_rows);
			
			for ($i = 1; $i <= $num_rows; $i++) {
				$row = $result->fetch(PDO::FETCH_ASSOC);
				if ($i == $random_index) {
					return new Project($row['id']);
				}					
			}
		}
		return false;
	}

	function removeLineBreaks(&$str) {
		$str = str_replace('<br />', "", $str);
		return $str;
	}
?>