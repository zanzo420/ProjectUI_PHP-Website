<div id="fullscreen_fixed_panel">
	<div id="fullscreen_flexbox">
	
	<div id="login_box" class="light_container">
		<div class="title_bar">
			<p>Login</p>
			<div class="icon-close" onclick="toggleLoginBox()"></div>
		</div>
		
		<div class="body">
			<form action="../public/index.php" method="post">			
				<p class="form-title">Username</p>
				<input class="form-field" type="text" name="username" placeholder="Username" value="">				
				<p class="form-title">Password</p>
				<input class="form-field" type="password" name="password" placeholder="Password" value="">
				<br />				
				<div>
					<input class="themed_button-light" style="margin-right: 10px;" type="submit" name="login" value="login">	
					<?php 
						$fb = new Facebook\Facebook([
							'app_id' => '236757293332894',
							'app_secret' => 'e10529583413ed7c57274dd271d2a0e5',
							'default_graph_version' => 'v2.2',
						]);
						$helper = $fb->getRedirectLoginHelper();
						$permissions = ['email']; // Optional permissions
						$loginUrl = $helper->getLoginUrl('http://localhost/ProjectUI/includes/fb-callback.php', $permissions);
						echo "<a class='blue' href='". htmlspecialchars($loginUrl) . "'>Or log in with Facebook!</a>";
					?>
				</div>				
			</form>			
		</div>		
	</div>	
	
	<?php 
	if (array_key_exists("fb_access_token", $_SESSION)) { 
		if (array_key_exists("fb_name", $_SESSION)) {	
			$fb_name = $_SESSION["fb_name"];
			$fb_name = str_replace(' ', '', $fb_name);
			unset($_SESSION["fb_name"]);
	?>
		<div id="choose_username" class="light_container">
			<div class="title_bar">
				<p>Choose a Username</p>
				<div class="icon-close" onclick="togglePanel('choose_username')"></div>
			</div>
			<div class="body">
				<form action="../public/index.php" method="post">
					<p class="form-title">Username</p>
					<ul>
						<li>Cannot contain whitespace</li>
						<li><?php
						$max = Constants::USERNAME_MAX_LEN;
						$min = Constants::USERNAME_MIN_LEN;
						echo "Must be between {$max} and {$min} characters in length";
						?></li>
					</ul>

					<input class="form-field" maxlength="<?php echo $max; ?>" type="text" name="username"
						   placeholder="Username" value="<?php echo $fb_name; ?>">
					<input class="themed_button-light" type="submit" name="register" value="complete registration">
				</form>
			</div>
		</div>
		<script type="text/javascript">
			$(document).ready(function() {
				togglePanel('choose_username');
			});
		</script>
	<?php 
		} else {
			unset($_SESSION["fb_access_token"]);
			unset($_SESSION["id"]);
		}
	}
	?>	
</div>
</div>

