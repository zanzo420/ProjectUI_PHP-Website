<?php
	require_once("objects/Database.php"); 
	require_once("objects/LoginSystem.php"); 
	require_once("objects/Project.php"); 
	require_once("objects/User.php"); 
	require_once("toolkit.php");
	$_USER = null;

	define('FACEBOOK_SDK_V4_SRC_DIR', __DIR__ . '/facebook-sdk-v5/');
	require_once __DIR__ . '/facebook-sdk-v5/autoload.php';
	
	start_session();	
	DB::connect();
	
	if (!LS::IsLoggedIn()) {
		if (array_key_exists("login", $_POST)) {
			LS::Login($_POST["username"], $_POST["password"]);	
		} elseif (array_key_exists("register", $_POST)) {
			if (array_key_exists("fb_access_token", $_SESSION)) {
				LS::Register($_POST["username"], $_SESSION["fb_id"], true);				
			} else {
				// register the normal way - TODO!
				//LS::Register($_POST["username"], $_POST["password"]);
			}
		}
	} else {
		$_USER = new User(LS::GetID(), LS::GetUsername());
	}
?>