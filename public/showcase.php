<?php
	require_once("../includes/init.php");	
	
	if (LS::IsLoggedIn()) {		
		function returnProjectData() {
			header('Content-Type: text/json');
			$project = getUnviewedProject();
			if ($project) {
				$response = $project->packData(false);
				echo json_encode($response);
			} else { 
			//	echo "not found";
				echo json_encode(array());
			}
			exit();
		}
		
		function returnComments() {
			$project = new Project($_POST["projectId"]);
			$comments = $project->getComments();
			
			if ($comments) {
				foreach ($comments as $comment => $data) {	
					$comment_text = stripslashes($data["comment_text"]);
					echo	"<section class='comment_section'>";
					echo		"<div class='profile_container'>";
					echo			"<img class='profile_icon' src='{$data["profile_picture"]}' alt='profile picture'>";
					echo		"</div>";
					echo		"<div class='comment_container'>";
					echo			"<span class='username'>{$data["commenter"]}
										</span><span class='post_date'>{$data["comment_date"]}</span>";
					echo			"<div class='comment_body'>{$comment_text}</div>";
					echo		"</div>";
					echo	"</section>";
				}
			} else {				
				echo "<div id='no_comments_message'>No comments yet!</div>";
			}
			exit();
		}

		if (array_key_exists("postingLike", $_POST)) {	
			$projectId = (int) $_POST["projectId"];			
			$username = LS::GetUsername();
			$liked = (strtolower($_POST["liked"]) == "true") ? 1 : 0;
			$successful = DB::query("INSERT INTO projects_viewed (project_id, username, liked) VALUES (?, ?, ?)",
				array($projectId, $username, $liked));
			
			if ($successful) { returnProjectData(); }
			exit();
		} 	elseif (array_key_exists("postingComment", $_POST)) {
			$successful = DB::query("INSERT INTO showcase_comments
									(project_id, commenter, comment_text) VALUES (?, ?, ?)",
				array($_POST["projectId"], LS::GetUsername(), $_POST["comment_text"]));

			if ($successful) { returnComments(); }
			exit();
		} 	elseif (array_key_exists("requestProjectData", $_POST)) {
			returnProjectData();
		}	elseif  (array_key_exists("requestComments", $_POST)) {	
			returnComments();			
		}
	}
	
	// load normal page
	require_once("../includes/header.php"); 
?>

<div id="available" style="display: none;">
	<div class="narrow_container simple_container-dark">
		<img id="showcase_image" src="" alt="project screenshot">	
		<p style="margin: 10px 0px;" id="project_header"></p>
		<p id="project_description"></p>
	</div>
	
	<div id="vote_area">
		<div id='like_button'  class='link'>Like</div>
		<div id='dislike_button' class='link'>Dislike</div>
	</div>
		
	<!-- leave a comment: -->
	<div class="narrow_container simple_container-dark">
		<div id="add_comment_area" class="textarea_dark flexbox-vertical">
			<textarea  id="comment_textbox" rows="4"
					   maxlength="2000" placeholder="leave a comment" style="display: inline; width: 800px;"></textarea>
			<div style='margin: auto;'>
				<div onclick="post_comment()" class='themed_button-light'>Post</div>
			</div>
		</div>
	</div>
	
	<div class="narrow_container">
		<p class="heading">Comments:</p>
	</div>
	
	<!-- comment: -->
	<div id="comments_container" class="narrow_container"></div>	
	
</div>
<?php if (!LS::IsLoggedIn()) { ?>
	<div id="unavailable">
		<div class="simple_container-dark">
			<p class='no_new_projects'>Sorry, you must be logged in to use this feature.</p>
		</div>
	</div>	
<?php } else { ?>
	<div id="unavailable" style="display: none;">
		<div class="simple_container-dark">
			<p class='no_new_projects'>Sorry, no new projects are currently available for review.<br/>Make sure to check back at a later date.</p>
		</div>
	</div>
<?php 
		echo "<script src='scripts/showcase.js'></script>"; 
	}
	require_once("../includes/footer.php"); 
?>