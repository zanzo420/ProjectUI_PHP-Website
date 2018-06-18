<?php require_once("init.php"); ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">		
		<!-- styles -->
		<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
		<link href="styles/theme.css" media="all" rel="stylesheet" type="text/css"/>
		<?php if (LS::IsLoggedIn()) { ?>
		<div id="username_hidden" data-username="<?php echo $_USER->username; ?>" style="display: none"></div>
		<?php } ?>
		<?php	
			$pathinfo = pathinfo($_SERVER['SCRIPT_FILENAME']);
			$css_file = "styles/" . $pathinfo["filename"] . ".css";
			if (file_exists ($css_file)) {
				echo "<link href='{$css_file}' media='all' rel='stylesheet' type='text/css'/>";
			}
		?>		
		<!-- scripts -->
		<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
		<script type="text/javascript" src="https://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
		<script type="text/javascript" src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
		<script type="text/javascript" src="scripts/animations.js"></script>
		
		<!-- only works if node is running the server -->
		<!-- connecting to our socket.io server (no need to include a local version)-->
		<!-- this is the socket.io dependency required for the chat_client.js to connect to chat_server.js -->

		<!-- same as using require("socket.io") on the server side -->
		<script src="http://localhost:8888/socket.io/socket.io.js"></script>
		<script src="scripts/chat_client.js"></script>
	</head>
	<body>
		<div id="background_artwork"></div>		
		
		<?php if (!LS::IsLoggedIn()) { require_once("login_box.php"); } ?>
		
		<div id="header">
			<div id="top_bar">
				<div id="tools_area">
					<?php 
						if (LS::IsLoggedIn()) {
							echo "<ul class='h_btn_list light_hover'>";							
							echo "<li class='dropdown'>contacts (4)</li>";
							echo "<li class='dropdown'>inbox (0)</li>";
							echo "</ul>";
							}
						?>
				</div>
			
				<div id="login_area">
					<ul class="h_btn_list light_hover">
						<?php 
						if (LS::IsLoggedIn()) {
							echo "<li class='link' onclick=\"window.location='account.php'\">" . LS::GetUsername() . "</li>";
							echo "<li class='link' onclick=\"window.location='logout.php'\">logout</li>";
						} else {
							echo "<li class='link' onclick='toggleLoginBox()'>login</li>";
							echo "<li class='link'>register</li>";
						}
						?>
					</ul>
				</div>
			</div>
			<div id="navbar_container">					
				<ul id="navbar" class="h_btn_list">					
					<?php
						$result = DB::query("SELECT * FROM nav_items;");
						if ($result) {
							foreach($result as  $row) {
								$nav_id = $row[0];
								$name = $row[1];
								$sublist = $row[2];								
								$link = $row[3];								
								if (isset($link))	
									echo "<a href='{$link}'>"; 
								if ($sublist) {												
									echo "<li class='nav_item expandable' data-nav_id='{$nav_id}'><span class='nulp'>{$name}</span></li>";									
								} else {									
									echo "<li class='nav_item dark_hover'>{$name}</li>";
								}
								if (isset($link))
									echo "</a>";                     
							}
							if (LS::IsLoggedIn() && is_admin()) {
								echo "<a href='admin_area.php'><li class='nav_item dark_hover admin'><span class='admin'>Admin Area</span></li></a>";
							}
						}
					?>
					<div class="shadow_effect"></div>
				</ul>				
				<div id="nav_sublist_container" class="light_container">
					<div class="arrow_down"></div>
					<?php
						$result = DB::query("SELECT * FROM nav_items;");
						if ($result) {							
							foreach($result as  $row) {
								$nav_id = $row[0];
								$name = $row[1];
								$sublist = $row[2];								
								if ($sublist) {
									// create the sublist:
									echo "<ul class='nav_sublist' data-parent_id='{$nav_id}'>";
									$nav_id = (int) $nav_id;
									$items = DB::query("
										SELECT name, link
										FROM sub_nav_items
										WHERE parent_id = ?", array($nav_id)
									);
									if ($items) {
										foreach($items as  $item) {
											$name = $item[0];
											$link = $item[1];
											if (!(!LS::IsLoggedIn() && $name == "Create Project")) {
												if (isset($link))
													echo "<a href='{$link}'>";
												echo "<li onclick=''>{$name}</li>"; // repeat;
												if (isset($link))
													echo "</a>";
											}
										}
									} else {
										echo "error header.php?e=001";
									}							
									echo "</ul>";
								}
							}
						}						
					?>
				</div>				
			</div>
		</div>
		<div id="main">
			<div id="website_name">
				<p>ProjectUI<span style="color: lightgreen; font-size: 18pt;"> beta</span></p>				
			</div>
			<p id="slogan">The Social Home for UI Enthusiasts</p>
			<div id="page">