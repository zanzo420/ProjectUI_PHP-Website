<?php require_once("../includes/header.php");
	if (!LS::IsLoggedIn()) redirect();
?>
<p class="heading">Create and Upload a Project</p>

<div id="errors_box">
	<div class="errors_area">
	<p>Please fix the following errors before continuing:</p>
	<ul class="errors_list"></ul>
	</div>
</div>

<br/>
<form action="" id="project_manager" method="POST">
	<div class="tab_divider">
		<ul class="h_btn_list tabs">
			<li class="active" data-type="Basic Info">Basic Info</li>
			<li class="inactive" data-type="Images">Images</li>
			<li class="inactive" data-type="Videos">Videos</li>
		</ul>
		
		<div class="tab-body" data-type="Basic Info"><!-- make it flexbox-vertical if locked -->		
			<!--<img class="locked" src="images/locked.png" alt="feature locked icon"/>-->
			<div class="dark_container flexbox-vertical" style="padding: 10px; margin-bottom: 20px; width: 600px;">	
				<p class="label-nopadding" style="width: 240px;">Project Title* (50 limit):</p>
				<input type="text" name="project_title" class="themed_textfield" maxlength="50" autofocus required>
			</div>
			
			<div class="dark_container" style="padding: 10px; margin-bottom: 20px;">	
				<p class="label">Short Description* (300 limit):</p>
				<div class="textarea_dark" style="margin: 5px 40px;">
					<textarea rows="2" name="short_description" maxlength="300" required></textarea>
				</div>
			</div>
				
			<div class="dark_container" style="padding: 10px; margin-bottom: 20px;">	
				<p class="label">Main Description (optional):</p>
				<div class="textarea_dark" style="margin: 5px 40px;">	
					<textarea rows="20" name="main_description"></textarea>
				</div>
			</div>
				
			<div class="dark_container" style="padding: 10px; margin-bottom: 20px; width: 300px;">	
				<p class="label">File Upload* (50 MB max):</p>
				<input type="file" class="themed_fileupload"
					   name="file_upload" value="Choose file" accept=".zip"  required>
			</div>
				
			<div class="dark_container" style="padding: 10px; margin-bottom: 20px;">	
				<p class="label">Terms and Conditions:</p>				
				<div class="scrollframe entrybox">
					<ul>
						<li>Only zip files are allowed (rar files are <b>not</b> accepted).</li>
						<li>You must include an informative and accurate description of what the file does.</li>
						<li>Executable files are not allowed.</li>
						<li>AddOns found to include wowmatrix.dat files are not allowed.</li>
						<li>Do not include additional zip files inside the main zip file.</li>
						<li>Additional compilation rules:</li>
						<ul>
							<li>Compilations must include a list of the AddOns used in the description.</li>
							<li>If the WTF is included, all personal information must be changed to generic terms.</li>
						</ul>
					</ul>					
					By uploading this file you certify that to the best of your knowledge:
					<ul>
						<li>The file and its contents are free of viruses, trojans, spyware, malware, and worms. If anything malicious is found you will be banned and your account information handed over to proper authorities.</li>
						<li>This file conforms to Blizzard's EULA, ToU and UI Add-On Development Policy.</li>
						<li>This file is offered free of charge and is not available elsewhere for a fee.</li>
						<li>You certify that either you are the copyright holder of all documents being submitted or have permission to submit them.</li>
						<li>This file, or the contents thereof, are not harmful or offensive in any way.</li>
						<li>This file is free from advertisements or promotions or nag screens.</li>
						<li>This file has no time limit and is free to use. (Paid or Trial-ware are not accepted here)</li>
						<li>The description that you are providing to this file accurately reflects the contents of the file.</li>
						<li>Your upload file conforms to the rules listed above.</li>
						<li>All uploads are posted pending verification by an admin or file moderator.</li>
					</ul>
				</div>		
				<p style="display: inline-block; margin: 20px 10px 10px 10px; color: #80bfff;">I have read, understand, and agree to these terms:</p>				
				<input type="checkbox" name="terms_checkbox" style="position: relative; top: 2px;"  required>
			</div>
			
			<div id="progressBar" style="display: block;">
				<div id="progressBackground">				
					<div id="progressValue"></div>
				</div>
				<p id="progressText">download progress: 0%</p>
			</div>
			
			<input type="submit" id="upload_button" class="themed_button-dark" style="margin-top: 20px;" value="Upload">
		</div>
		
		<div class="tab-body" data-type="Images">
			<img class="locked flexbox-vertical" src="images/locked.png" alt="feature locked icon"/>
		</div>
		
		<div class="tab-body" data-type="Videos">
			<img class="locked flexbox-vertical" src="images/locked.png" alt="feature locked icon"/>
		</div>
	</div>
	<input type="hidden" name="method" value="CREATE"/>
	<!-- TODO DO NOT KNOW THE PROJECT ID! -->
</form>
<script type="text/javascript" src="scripts/tab_divider.js"></script>
<script src="scripts/project_manager.js"></script>
<?php require_once("../includes/footer.php"); ?>