<?php
require_once("../includes/init.php");
$title = (array_key_exists("title", $_POST)) ? $_POST["title"] : "";
$content = (array_key_exists("content", $_POST)) ? $_POST["content"] : "";

if (array_key_exists("post_announcement", $_POST)) {
	// handle new post, return to
	$errors = array();
	if (strlen($title) < 5 || strlen($title) > 100) {
		$errors[] = "Title must be 5 to 100 characters long";
	}
	if (strlen($content) < 100) {
		$errors[] = "Content must be at least 100 characters long";
	}
	if (count($errors) === 0) {
		$username = LS::GetUsername();
		$content = nl2br($content); // preserve line breaks
		DB::query("INSERT INTO announcements (title, author, content) VALUES(?, ?, ?);",
			array($title, $username, $content));
		redirect("announcements.php");
	} else {
		require_once("../includes/header.php");
		echo "<div id='errors_box' style='display: block'>";
		echo 	"<div class='errors_area'>";
		echo 		"<p>Please fix the following errors before continuing:</p>";
		echo 		"<ul class='errors_list'>";
		foreach ($errors as $error) {
			echo "<li>{$error}</li>";
		}
		echo		"</ul>";
		echo	"</div>";
		echo "</div>";
	}
} else {
	require_once("../includes/header.php");
}
?>
<p class="heading">Post Announcement</p>
<br/>
<div class="news_entry">
	<form action="announcements_post.php" method="post">
		<div class="news_title_container light_container">
			<img class="profile_picture_icon" src="images/paris.jpg" alt="profile picture">
			<label class="label" for="title_textfield">Title*:</label>
			<input type="text" id="title_textfield" name="title"
				   value="<?php echo $title; ?>" maxlength="100">
		</div>
		<div class="dark_container">
			<div class="textarea_dark" style="margin: 20px 40px;">
				<p class="label">Content*: </p>
				<textarea name="content" rows="30"><?php echo $content; ?></textarea>
				<br /><br />
				<input type="submit" class="themed_button-light" name="posting" value="Post Announcement" />
				<!--<p style="display: inline-block; margin-left: 10px;">Send comments to inbox:</p>
				<input type="checkbox" name="notify" style="position: relative; top: 2px;"/>-->
			</div>
		</div>
		<input type="hidden" name="post_announcement" value="true">
	</form>
</div>
<?php require_once("../includes/footer.php"); ?>