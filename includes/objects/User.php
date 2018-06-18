<?php 
require_once("Database.php");

class User {
	public $username;
	public $isAdmin;
	public $about_me;
	public $profile_picture;
	public $id;

	function __construct($id, $username) {
		$this->id = $id;
		$this->username = $username;

		$result = DB::query("
			SELECT about_me, profile_picture FROM profiles
            WHERE username = ?", array($this->username)
		);
		if ($result && $row = $result->fetch()) {
			$this->about_me = str_replace('<br />', "", $row["about_me"]);
			$this->profile_picture = $row["profile_picture"];
		}
	}

	public static function findUsername($id) {
		$result = DB::query("
			SELECT username FROM users
            WHERE id = ?", array($id)
		);
		if ($result && $row = $result->fetch()) {
			return $row["username"];
		} else return false;
	}

	public static function findID($username) {
		$result = DB::query("
			SELECT id FROM users
            WHERE username = ?", array($username)
		);
		if ($result && $row = $result->fetch()) {
			return $row["id"];
		} else return false;
	}

	public static function printAsLink($user) {
		echo "<a class='blue' href='account.php?id={$user->id}'>{$user->username}</a>";
	}
}