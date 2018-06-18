<?php
	require_once("../includes/toolkit.php");
	start_session();
	require_once("../includes/objects/LoginSystem.php");	
	
	if (LS::IsLoggedIn()) {
		LS::Logout();
		redirect();
	} else {
		echo "not even logged in wtf...";
	}
	
?>