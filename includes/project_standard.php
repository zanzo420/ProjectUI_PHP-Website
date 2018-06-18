<section class="page_section" data-type="about">
	<p class="heading">About <?php echo $project->title; ?></p>
	<div class="dark_container text_container">	
		<p><?php echo $project->content; ?></p>
	</div>
</section>

<section class="page_section" data-type="install">
	<p class="heading">How to Install</p>
	<div class="dark_container text_container">
		<p>TODO: Videos</p>
	</div>
</section>

<section class="page_section" data-type="images">
	<p class="heading">Images (7)</p>
	<div class="images_section dark_container text_container textarea_dark">		
		<div id="image_tooltip">
			<p></p>
			<div class="icon-mag"></div>
		</div>
		<?php
		$result = DB::query("SELECT image_id, file_path FROM project_images WHERE project_id = ?;",
			array($project->id));
		while ($row = $result->fetch()) {?>
			<div class="image_container">
				<img src="<?php echo $row["file_path"]; ?>"
					 alt="pic<?php echo $row["image_id"]; ?>" height="200">
			</div>
		<?php } ?>
	</div>
</section>

<section class="page_section" data-type="videos">
	<p class="heading">Videos</p>
	<div class="dark_container text_container">	
		<p>TODO: Videos</p>
	</div>
</section>

<section class="page_section" data-type="changes">
	<p class="heading">Change Log</p>
	<div class="dark_container text_container">	
		<p>TODO: Change Log</p>
	</div>
</section>

<section class="page_section" data-type="credits">
	<p class="heading">Credits</p>
	<div class="dark_container text_container">	
		<p><?php echo $project->credits; ?></p>
	</div>
</section>