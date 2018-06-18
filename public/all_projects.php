<?php require_once("../includes/header.php"); ?>

	<p class="heading">All Projects</p>
	<div id="news_options">
		<form action="all_projects.php" method="post">
			<!--
			<select name="type" id="type_dropdown">
				<option value="site news">All Types</option>
				<option value="general news">Graphical</option>
				<option value="general news">Minimalistic</option>
				<option value="Gaming News">Class Specific</option>
				<option value="Project News">DPS</option>
				<option value="Project News">Tank</option>
				<option value="Project News">Healer</option>
			</select>
			<input type="textfield" placeholder="search" id="search_box"/>
			<input type="submit" style="margin-left: 10px;" name="search" class="themed_button-light" value="Search"/>
			-->
			<?php
				if (LS::IsLoggedIn()) {
					echo "<a href='create_project.php'><div class='themed_button-light post_button'>Create Project</div></a>";					
				}
			?>
		</form>
	</div>
	
	<?php 
	$results = DB::query("SELECT id FROM projects;"); // TODO: WHERE verified = 1, and also featured should show as a different colour
	if ($results) {
		foreach ($results as $row) {
			$project = new Project($row["id"]);
			$image_path = $project->getImagePath(1);
	?>
	<section class="project_entry">		
		<div class="row-1">
			<h2><?php echo $project->title; ?></h2>
			<ul class="list_info">
				<li>Author: <?php echo $project->author; ?></li>
				<li>Last Updated: <?php echo $project->update_date; ?></li>
				<li>Downloads: <?php echo $project->downloads; ?></li>
			</ul>
		</div>
		<div class="row-2">			
			<h3>Short Description</h3>
			<p><?php echo $project->description; ?></p>
		</div>
		<div class="project_thumbnail" style="background-image: url('<?php echo $image_path; ?>'); position: relative; z-index: 2;"></div>
		<a href="project.php?id=<?php echo $project->id; ?>">
			<div style="position: absolute; top: 0px; left: 0px; right: 0px; bottom: 0px;"></div>
		</a>
	</section>	
	<?php
		}
	}
	?>	
	<div id="preview_window_frame">
		<div id="preview_window">
			<div id="preview_image">
				<img src="" alt="project screenshot">	
				<div id="preview_close_btn"></div>
			</div>
		</div>		
	</div>
	
	<script type="text/javascript">
		$(document).ready(function() {	
			var preview_frame = $("#preview_window_frame");
			var preview = $("#preview_window");
			var img = preview.find("img").eq(0);
			
			$(".project_thumbnail").on("click", function() {		
				var src = $(this).css("backgroundImage");
				var res = src.replace("url(\"",'').replace("\")", "");
				img.attr("src", res);
				preview_frame.fadeIn();
				preview.slideDown();
			});
			
			$("#preview_close_btn, #preview_window_frame").on("click", function(event) {
				if (!$(event.target).is("img")) {
					preview_frame.fadeOut();
					preview.slideUp();
				}
			});
			preview.slideUp();
		});
	</script>
<?php require_once("../includes/footer.php"); ?>