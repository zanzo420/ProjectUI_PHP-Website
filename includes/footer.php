			</div>
			<div id="footer">
				<p>Copyright &copy; 2016 "ProjectUI" by Michael Richards. All Rights Reserved.</p>
			</div>
		</div>
		<div id="bottom_bar">
			<div id="chat_template" class="chat_box chat_minimized">
				<div class="chat_frame dark_container">
					<div class="title_bar">
						<p class="title_data"></p>
						<div class="icon-close" onclick="toggle(this, false)"></div>
					</div>
					<div class="chat_area"></div>
				</div>
				<div class="chat_bar">
					<input class="chat_input_event" type="text"
						   maxlength="250" placeholder="send a message">
				</div>
				<div class="chat_toggle_btn" onclick="toggle(this, true)">
					<p class="min_data"></p>
				</div>
			</div>
		</div>
	</body>
</html>
<?php if (class_exists("Database", false)) Database::close(); ?>