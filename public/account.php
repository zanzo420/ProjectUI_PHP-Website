<?php require_once("../includes/header.php");
$different_user = false;
$username = false;
if (array_key_exists("id", $_GET)) {
	$user_id = $_GET["id"];
	$found_username = User::findUsername($user_id);
	if (!$found_username) redirect();

	if ($found_username != LS::GetUsername()) {
		$username = $found_username;
		$user = new User($user_id, $username);
		$different_user = true;
	}
}
if (!$different_user && LS::IsLoggedIn()) {
	// user's own profile
	$username = LS::GetUsername();
	if (array_key_exists("finish", $_POST)) {
		$about_me = $_POST["about_me"];
		$about_me = nl2br($about_me);

		$profile_pic = $_FILES["profile_pic"];
		$file_safe_username = urlencode($username);
		$storage_folder = "images/accounts/{$file_safe_username}";
		$path = moveFile($profile_pic, $storage_folder, "profile_picture");
		if ($path) {
			DB::query("UPDATE profiles SET profile_picture = ?
              WHERE username = ?",
				array($path, $username));
		}
		DB::query("UPDATE profiles SET about_me = ?
              WHERE username = ?",
			array($about_me, $username));
		redirect("account.php");
	}
	if (array_key_exists("edit_profile", $_POST)) $edit = true;
	$user = $_USER;
}
if (!$username) {
	$user = ""; // to stop phpstorm warnings
	redirect();
}
?>
<form action="account.php" method="POST" enctype="multipart/form-data">
<section class="narrow_container">
	<div class="simple_container-dark text_container flexbox" style="width: 600px;">
		<div style="width: 100%; margin-right: 15px;">
			<ul class="profile_list">
				<li><span class="label">Username:</span><?php echo $user->username; ?></li>
				<li><span class="label">Join Data:</span></li>
				<br />
				<li><span class="label">Blogs:</span>0</li>
				<li><span class="label">Announcements:</span>0</li>
				<li><span class="label">Uploads:</span>0</li>
			</ul>

			<?php if (isset($edit)) { ?>
				<input type="submit" name="finish" value="Finish Editing" class="themed_button-light"
					   style="display: inline-block; margin-top: 20px;">
			<?php } elseif ($user === $_USER) { ?>
				<input type="submit" name="edit_profile" value="Edit Profile" class="themed_button-light"
				   style="display: inline-block; margin-top: 20px;">
			<?php } ?>
			<?php if ($different_user) { ?>
				<input type="button" onclick="privateMessage('<?php echo $username; ?>');" name="private_message" value="Private Message" class="themed_button-light"
					   style="display: inline-block; margin-top: 20px;">
			<?php } ?>
			<?php if (isset($edit)) { ?>
			<br/><br/>
			<p class="label-nopadding">Change Picture</p>
				<p style="margin-top: 0; margin-bottom: 10px; font-size: 10pt; font-style: italic;">
					Image size should ideally be 400px by 400px</p>
			<input type="file" accept=".jpg" name="profile_pic">
			<?php } ?>
		</div>
		<div>
			<p><span class="label">Profile Picture:</span></p> 
			<img class="profile_picture_display" src="<?php echo $user->profile_picture; ?>" alt="profile picture">
		</div>
	</div>

	<div class="simple_container-dark">
		<p class="label">About You:</p>
		<?php if (isset($edit)) {

			?>
			<div class="textarea_dark">
				<textarea name="about_me" rows="20"
						  class="box_padding dark_container"><?php echo $user->about_me; ?></textarea>
			</div>
		<?php } else { ?>
			<div class="box_padding dark_container"><?php echo $user->about_me; ?></div>
		<?php } ?>

		<p class="label" style="margin-top: 20px;">Activity History:</p>
		<div class="tab_divider">
			<ul class="h_btn_list tabs">
				<li class="active" data-type="blogs">Blogs</li>
				<li class="inactive" data-type="announcements">Announcements</li>
				<li class="inactive" data-type="uploads">Uploads</li>
			</ul>
			<div class="tab-body" data-type="blogs">none</div>
			<div class="tab-body" data-type="announcements">none</div>
			<div class="tab-body" data-type="uploads">
				<?php
				$result = DB::query("SELECT id FROM projects WHERE author = ?", array($user->username));
				foreach ($result as $row) {
					$project = new Project($row["id"]);
					echo "<a href='project.php?id={$project->id}'><p>{$project->title}</p></a>";
				}
				?>
			</div>
		</div>
	</div>
</section>
</form>
<script src="scripts/tab_divider.js"></script>
<?php require_once("../includes/footer.php"); ?>