<?php require("../includes/header.php"); ?>
<p class="heading">Announcements</p>
<div id="news_options">
	<form action="announcements_post.php" method="post">
		<!--<select name="type" id="type_dropdown">
			<option value="Site">Site Announcements</option>
			<option value="Project Announcements">Project Announcements</option>
		</select>
		<input type="textfield" placeholder="search" id="search_box"/>
		<input type="submit" style="margin-left: 10px;" name="search" class="themed_button-light" value="Search"/>
		-->
		<?php
			if (LS::IsLoggedIn()) {
				echo "<input type='submit' name='post' class='themed_button-light post_button' value='Post Announcement'/>";
			}
		?>
	</form>
</div>
<?php
$result = DB::query("SELECT * FROM announcements;");
$num_rows = DB::query("SELECT COUNT(*) FROM announcements;");
$num_rows = $num_rows->fetchColumn();

for ($i = 1; $i <= $num_rows; $i++) {
	if ($result && $row = $result->fetch(PDO::FETCH_ASSOC)) {
		$num_comments = DB::query("SELECT COUNT(*)
			  FROM announcement_comments WHERE announcement_id = ?;", array($row["id"]));
		$num_comments = $num_comments->fetchColumn();
		$user = new User(User::findID($row["author"]), $row["author"]);
		?>
		<div class="simple_container-dark text_container">
			<div class="news_title_container light_container">
				<img class="profile_picture_icon" src="<?php echo $user->profile_picture; ?>" alt="profile picture">
				<p class="news_comments"><?php echo $num_comments; ?> comments</p>
				<a class="blue" href="view_announcement.php?id=<?php echo $row["id"]?>">
					<p class="news_title"><?php echo $row["title"]; ?></p>
				</a>
				<p class="news_info">posted by
					<?php USER::printAsLink($user); ?>, <?php echo $row["post_date"]; ?></p>
			</div>
			<div class="box_padding dark_container">
				<?php echo $row["content"]; ?>
			</div>
		</div>
		<?php
	}
}
?>

<?php require("../includes/footer.php"); ?>