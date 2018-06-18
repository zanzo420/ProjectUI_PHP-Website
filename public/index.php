<?php require_once("../includes/header.php"); ?>

<div id="banner_container" class="light_container" style="background-color: rgba(0, 34, 102, 1)">
	<div id="banner_image">
		<div class="front_image"></div>
		<div class="back_image"></div>
	</div>
	<div id="banner_description">
		<a class="project_title" href=""></a>
		<div class="project_description"></div>
		<div class="project_downloads"></div>
	</div>
</div>

<table class="recent_entries_container" style="width: 100%;">
	<tr>					
		<td class="dark_container" style="width: 400px;" valign="top">
			<table style="width: 100%; border-collapse: collapse;">
				<tr>								
					<div class="recent_entries_header">
						<h3 class="latest_btn">Latest Blogs</h3>
						<h3 class="top_btn">Top Blogs</h3>
					</div>
				</tr>
				<tr class="entry">
					<td class="entry_info">Random Blog</td>
					<td class="entry_date">5 mins ago</td>
				</tr>
				<tr class="entry">
					<td class="entry_info">Another Blog</td>
					<td class="entry_date">7 mins ago</td>
				</tr>
				<tr class="entry">
					<td class="entry_info">Something New Blog</td>
					<td class="entry_date">2 day ago</td>
				</tr>
				<tr class="entry">
					<td class="entry_info">A blog about life</td>
					<td class="entry_date">21 day ago</td>
				</tr>
				<tr class="entry" style="border: none">
					<td class="entry_info">Do you like my UI?</td>
					<td class="entry_date">3 day ago</td>
				</tr>
			</table>
		</td>
		<td style="width: 5px"></td>
		<td class="dark_container" style="width: 400px;" valign="top">
			<table style="width: 100%; border-collapse: collapse;">
				<tr>								
					<div class="recent_entries_header">
						<h3 class="latest_btn">Latest Project Updates</h3>
						<h3 class="top_btn">Top Projects</h3>
					</div>
				</tr>
				<tr class="entry">
					<td class="entry_info">Something just happened..</td>
					<td class="entry_date">25 mins ago</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<p class="heading">Latest Announcements</p>

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
<script type="text/javascript" src="scripts/banner.js"></script>
<?php require("../includes/footer.php"); ?>