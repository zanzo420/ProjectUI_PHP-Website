<?php
class LS {	
	// returns true if user logs in
	public static function IsLoggedIn() {
		if (!isset($_SESSION)) return false;
		return array_key_exists("username", $_SESSION);
	}	

	public static function Login($username = "", $passwordOrId = "", $facebook = false) {		
		if ($facebook) {
			$result = DB::query("SELECT username FROM users WHERE fb_id=? LIMIT 1;", array($passwordOrId));
		} else {
			$result = DB::query("
				SELECT username FROM users
				WHERE username=? AND password=? LIMIT 1;",
				array($username, $passwordOrId)
			);
		}
		if ($result) {				
			$row = $result->fetch(); // fetch returns false if no row
			if ($row) {
				$_SESSION["username"] = $row["username"];				
			}
			redirect();
		}
		return false;
	}
	
	public static function Register($username = "", $passwordOrId = "", $facebook = false) {
		$username = trim($username);
		if (preg_match("/\s/", $username))
			return false;
		if (strlen($username) < Constants::USERNAME_MIN_LEN) {			
			return false;
		} else if (strlen($username) > Constants::USERNAME_MAX_LEN) {
			return false;
		}
		if ($facebook) {
			$result = DB::query("
				INSERT INTO users (username, fb_id)
				VALUES (?, ?);", array($username, $passwordOrId)
			);
		} else {
			if (strlen($passwordOrId) < Constants::PASSWORD_MIN_LEN) {
				return false; 
			}
			$result = DB::query("INSERT INTO users (username, password)
				VALUES (?, ?);", array($username, $passwordOrId));
		}
		if ($result) {
			DB::query("INSERT INTO profiles (username)
				VALUES (?);", array($username));
			return self::Login($username, $passwordOrId, $facebook);			
		} else {
			// DB::print_errors();
			return false;
		}
	}
	
	public static function Logout() {
		session_destroy();
	}
	
	public static function GetUsername() {
		if (isset($_SESSION) && array_key_exists("username", $_SESSION)) {
			return htmlentities($_SESSION["username"]);
		} else {
			self::Logout();
			return false;
		}
	}

	public static function GetID() {
		if (isset($_SESSION) && array_key_exists("username", $_SESSION)) {
			$username = htmlentities($_SESSION["username"]);
			$result = DB::query("
				SELECT id FROM users
            	WHERE username = ?", array($username));
			if ($result && $row = $result->fetch()) return $row["id"];
		}
		self::Logout();
		return false;
	}
}