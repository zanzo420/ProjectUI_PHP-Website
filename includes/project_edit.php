<div id="errors_box">
	<div class="errors_area">
		<p>Please fix the following errors before continuing:</p>
		<ul class="errors_list"></ul>
	</div>
</div>

<section class="page_section" data-type="about">	
	<!-- project_title -->
	<div class="dark_container flexbox-vertical"
		 style="padding: 10px; margin: 20px 10px 20px 0; width: 600px; display: inline-block;">
		<p class="label" style="width: 240px;">Project Title (50 limit):</p>
		<input type="text" name="project_title"
			   value="<?php echo $project->title; ?>" class="themed_textfield" maxlength="50">
	</div>
	<!-- project_version -->
	<div class="dark_container flexbox-vertical" style="padding: 10px; width: 120px; display: inline-block;">	
		<p class="label" style="width: 240px;">Version name:</p>
		<input type="text" name="project_version"
			   value="1" class="themed_textfield" maxlength="8">
	</div>	
	<!-- short_description -->
	<div class="dark_container" style="padding: 10px; margin-bottom: 20px;">	
		<p class="label">Short Description (300 limit):</p>
		<div class="textarea_dark" style="margin: 5px 40px;">
			<textarea rows="2" name="short_description"
					  maxlength="300"><?php echo $project->description; ?></textarea>
		</div>
	</div>
	<!-- main_description -->
	<div class="dark_container" style="padding: 10px; margin-bottom: 20px;">	
		<p class="label">Main Description:</p>
		<div class="textarea_dark" style="margin: 5px 40px;">	
			<textarea rows="20" name="main_description"><?php echo removeLineBreaks($project->content); ?></textarea>
		</div>
	</div>
	<!-- banner -->
	<div class="dark_container" style="width: 300px; padding: 10px;">			
		<p class="label-nopadding">Update Banner</p>
		<p style="margin-top: 0px; margin-bottom: 10px; font-size:
		10pt; font-style: italic;">Image size should ideally be 940px by 300px</p>
		<input type="file" name="banner" class="themed_fileupload">
	</div>	
</section>


<section class="page_section" data-type="install">
	<p class="heading">How to Install</p>
	<div class="dark_container text_container textarea_dark">	
		<!-- install_step_[n] -->
		<div class="install_step" data-step="1">
			<p>Step 1:</p>
			<textarea name="install_step_1" rows="10"></textarea>
			<div style="margin-left: 10px; margin-right: 50px;" class="plus_button install_step_button"></div>
		</div>
		<!-- install_step_[n] -->
		<div class="install_step" data-step="2">
			<p>Step 2:</p>
			<textarea name="install_step_2" rows="10"></textarea>
			<div style="margin-left: 10px; margin-right: 50px;" class="plus_button install_step_button"></div>
		</div>
	</div>
</section>


<section class="page_section" data-type="images">
	<p class="heading">Images (7)</p>		
	<!-- add_image_[n] -->
	<div class="dark_container text_container" style="width: 300px;">			
		<p class="label-nopadding">Add Image:</p>
		<input type="file" name="add_image_1" class="themed_fileupload">
		<!-- this will add a new <input type="file" class="themed_fileupload"> -->
		<!--<div class="themed_button-light"
			style="display: table; margin: 10px auto 0 auto;">Add another</div>-->
	</div>
	
	<!-- will have to add onClick script to delete button to form a list of images to delete in future -->
	<div class="images_section dark_container text_container textarea_dark">		
		<div id="image_tooltip">
			<p></p>
			<div class="icon-close"></div>
			<div class="icon-mag"></div>
		</div>
		<?php
		$result = DB::query("SELECT image_id, file_path FROM project_images WHERE project_id = ?;",
			array($project->id));
		while ($row = $result->fetch()) {?>
			<div data-image_id="<?php echo $row["image_id"]; ?>" class="image_container">
				<img src="<?php echo $row["file_path"]; ?>"
					 alt="pic<?php echo $row["image_id"]; ?>" height="200">
			</div>
		<?php } ?>
	</div>
</section>

<section class="page_section" data-type="videos">
	<p class="heading">Videos (7)</p>
	<!-- add_video_[n] -->
	<div class="dark_container text_container" style="width: 300px;">			
		<p class="label" style="margin: 0;">Add YouTube Video:</p>
		<input type="url" name="add_video_1" placeholder="Enter a YouTube video's URL here...">
		<!-- this will add a new <input type="url" placeholder="Enter a YouTube video's URL here..."> -->
		<div class="themed_button-light" style="display: table; margin: 10px auto 0 auto;">Add another</div>
	</div>
	
	<div class="videos_section dark_container text_container">	
		<div id="video_tooltip">
			<p></p>
			<div class="icon-close"></div>	
		</div>
		<div class="video_container">
			<iframe width="300" height="170" src="https://www.youtube.com/embed/UbN8098tg7U" frameborder="0" allowfullscreen></iframe>
		</div>		
		<div class="video_container">
			<iframe width="300" height="170" src="https://www.youtube.com/embed/UbN8098tg7U" frameborder="0" allowfullscreen></iframe>
		</div>
		<div class="video_container">
			<iframe width="300" height="170" src="https://www.youtube.com/embed/UbN8098tg7U" frameborder="0" allowfullscreen></iframe>
		</div>
		<div class="video_container">
			<iframe width="300" height="170" src="https://www.youtube.com/embed/UbN8098tg7U" frameborder="0" allowfullscreen></iframe>
		</div>
		<div class="video_container">
			<iframe width="300" height="170" src="https://www.youtube.com/embed/UbN8098tg7U" frameborder="0" allowfullscreen></iframe>
		</div>
		<div class="video_container">
			<iframe width="300" height="170" src="https://www.youtube.com/embed/UbN8098tg7U" frameborder="0" allowfullscreen></iframe>
		</div>
		<div class="video_container">
			<iframe width="300" height="170" src="https://www.youtube.com/embed/nfaE7NQhMlc" frameborder="0" allowfullscreen></iframe>
		</div>
	</div>	
</section>


<section class="page_section" data-type="changes">
	<p class="heading">Change Log</p>
	<div class="dark_container text_container textarea_dark">
	
		<div class="narrow_container text_container" style="margin: 0 auto">
			<div class="flexbox-vertical textarea_dark">
				<textarea rows="4" placeholder="log changes here" style="display: inline; width: 700px;"></textarea>
				<div style='margin: auto;'>
					<div class='themed_button-light'>Add Log Entry</div>
				</div>
			</div>
		</div>	
		
		<!-- logs added and removed will get placed in a list -->
		<div class='simple_container-light narrow_container'>
			<p class='label'>logged on: 27/02/16</p>
			<p>text to enter (log)</p>
			<div class="icon-close" style="position: absolute; top: 0px; right: 0px;"></div>
		</div>
	</div>
</section>

<section class="page_section" data-type="credits">
	<!-- credits -->
	<p class="heading">Credits</p>
	<div class="dark_container text_container textarea_dark">	
		<textarea rows="20" name="credits"><?php echo removeLineBreaks($project->credits); ?></textarea>
	</div>
</section>

<p class="heading">Additional Options</p>
<section class="dark_container" style="margin-top: 10px;">
	<div id="additional_options" style="display: flex; padding: 20px;">
	
		<!-- availability -->
		<div style="width: 600px;">
			<p class="label">Project Availability:</p>
			<input type="radio" name="public" value="public"> Public<br />
			<input type="radio" name="private" value="private"> Private<br />
			<br />
			<input type="button" class="themed_button-light" value="Delete Project" onclick="deleteProject()">
		</div>

		<div style="width: 100%;">
			<p class="label">All uploaded versions:</p>
			<div id="version_scrollbox-container" class="light_container">
				<div id="version_scrollbox">
					<ul>
						<li>Version 1</li>
					</ul>
				</div>
			</div>
		</div>		
		<div style="width: 700px;">
			<p class="label">Upload new version:</p>
			<!-- file_upload -->
			<input type="file" name="file_upload" accept=".zip" class="themed_fileupload">
				<!-- new_project_version -->	
			<p class="label" style="margin-top: 13px;">New version name:</p>
			<!-- new_project_version -->
			<input type="text" name="new_project_version" class="themed_textfield" style="width: 100px;" maxlength="8">
		</div>
	</div>	
	<input type="hidden" name="method" value="UPDATE"/>
	<input type="hidden" name="redirect_url" value="project.php?id=<?php echo $project->id; ?>"/>
	<input type="hidden" name="project_id" value="<?php echo $project->id; ?>"/>
</section>
<script src="scripts/project_manager.js"></script>