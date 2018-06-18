<?php
require_once("../includes/header.php"); 
if (array_key_exists("id", $_GET)) {
	$id = $_GET["id"];	
	$project = new Project($id);
	if (!$project->exists()) {
		redirect();
	}	
	$edit = array_key_exists("edit_mode", $_POST);
} else {
	redirect();
}
?>
<?php 
	if (LS::IsLoggedIn() && LS::GetUsername() == $project->author) {
		$author = true;
?>
	<form action="" id="project_manager" method="POST" novalidate>
	
	<div style="position: absolute;">
		<div id="progressBar">
			<div id="progressBackground">				
				<div id="progressValue"></div>
			</div>
			<p id="progressText">download progress: 0%</p>
		</div>
	</div>
		
	<div style="height: 40px;">
		<div style="float: right;">			
			<?php if (isset($edit) && $edit) { ?>
				<p class="label" style="display: inline">Apply Changes?:</p>
				<input class="themed_button-light" style="margin-right: 10px;" type="submit" value="Apply"/>
				<input class="themed_button-light" type="submit" value="Cancel"/>
			<?php } else { ?>
				<input class="themed_button-light" type="submit" name="edit_mode" value="Edit Project" />
			<?php } ?>		
		</div>
	</div>
<?php } ?>

<div id="project_banner" class="light_container">
	<img id="project_banner_image" src=<?php echo $project->banner_path; ?> />
	<?php $currentVersion = $project->getCurrentVersion(); ?>
	<a href="<?php echo $currentVersion->file_path; ?>"><div id="download_button">Download</div></a>
</div>
<div class="light_container" style="width: 100%; position: relative; padding: 0; box-sizing: border-box;">
	<ul id="banner_list" class="h_btn_list">
		<li data-type="about">About</li>
		<li data-type="install">How to Install</li>
		<li data-type="images">Images</li>
		<li data-type="videos">Videos</li>
		<li data-type="changes">Change Log</li>
		<li data-type="credits">Credits</li>
		
		<!--
		Additional Features that the author can enable:
		<li data-type="todo">To-Do's</li>
		<li data-type="news">News</li>
		<li data-type="guides">Guides</li>
		<li data-type="plugins">Plugins</li>
		<li data-type="donate">Donate</li>
		<li data-type="faqs">F.A.Q's</li>
		-->
	</ul>
	<div id="arrow_marker"></div>
</div>
<script src="scripts/project.js"></script>
<?php 
	if (isset($edit) && $edit) {
		require_once("../includes/project_edit.php"); 
	} else { 
		require_once("../includes/project_standard.php");
	}
	if (isset($author)) echo "</form>";
	require("../includes/footer.php"); 
?>