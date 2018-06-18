function ChatRoom(receiver) {
	this.sender = Server.getUsername();
	this.receiver = receiver;
	this.chatBox = getChatBox(receiver);
	this.isMinimized = true;
	this.numUnread = 0;

	Server.chatRooms[receiver] = this;

	this.attachMessage = function(sender, message, general) {
		if (this.isMinimized) {
			this.numUnread += 1;
			var tag = (general) ? "General Chat" : sender;
			this.chatBox.find("p.min_data").text(tag + " (" + this.numUnread + ")");
		}
		var para = document.createElement('p');
		if (sender) {
			para.innerHTML = "<span style='color: #4da6ff'>[" + sender + "]:</span> ";
		}
		para.appendChild(document.createTextNode(message));
		$(para).hide();
		var chat_area = this.chatBox.find("div.chat_area").get(0);
		chat_area.appendChild(para);
		$(para).fadeIn("fast");
		chat_area.scrollTop = chat_area.scrollHeight;
	};

	this.sendMessage = function(message) {
		Server.socket.emit("input", {
			sender: this.sender,
			receiver: this.receiver,
			message: message
		});
	};

	this.toggle = function(show) {
		var chatBox = this.chatBox;
		var frame = chatBox.find("div.chat_frame");
		this.numUnread = 0;
		if (show && this.isMinimized) {
			chatBox.find("p.min_data").text(this.receiver + " (0)");
			chatBox.removeClass("chat_minimized");
			chatBox.addClass("chat_maximized");
			frame.css("height", "0px");
			frame.stop().animate({
					height: 180
				}, 'normal',
				function() {
					var chat_area = chatBox.find("div.chat_area").get(0);
					chat_area.scrollTop = chat_area.scrollHeight;
			});
			this.isMinimized = false;
		} else if (!show && !this.isMinimized) {
			var chatBar = chatBox.find("div.chat_bar");
			frame.stop().animate({
					height: "0px",
					width: (chatBar.width() + 15) + "px"
				}, 'normal', function() {
					chatBox.removeClass("chat_maximized");
					chatBox.addClass("chat_minimized");
				}
			);
			this.isMinimized = true;
		}
	};

	var self = this;
	// receive the chosen room name
	Server.socket.on("history", function(data) {
		self.attachMessage(data.history); // eventually call it unpackHistory()
	});

	// send the preferred room name
	Server.socket.emit("register", { room: this.sender });
}

Server = {};

// listens out for any messages (both public and private)
// all socket input from the server goes here
Server.input = function(data) {
	var sender = data.sender;
	var message = data.message;
	var receiver = data.receiver; // who is receiving it? General Chat is!
	var isGeneral = (receiver === "General Chat");
	var token = (isGeneral) ? receiver : sender;
	var chatRoom = Server.getChatRoomByReceiver(token);

	if (!chatRoom) {
		chatRoom = new ChatRoom(sender);
	}
	chatRoom.attachMessage(sender, message, isGeneral);
};

Server.getChatRoomByReceiver = function(receiver) {
	for (var key in Server.chatRooms) {
		if (Server.chatRooms.hasOwnProperty(key) && key === receiver) {
			return Server.chatRooms[key];
		}
	}
};
Server.getUsername = function() {
	if (this.username) return this.username;
	this.username = $("#username_hidden");
	if (this.username) {
		this.username = this.username.attr("data-username");
		return this.username;
	}
};

$(document).ready(function() {
	Server.socket = io.connect('http://localhost:8888');
	Server.socket.on('input', Server.input);

	Server.chatRooms = [];
	var generalChat = new ChatRoom("General Chat");
	generalChat.toggle(true);
});

function focusHandler() {
	var colour = ($(this).is(":focus")) ? "white" : "#001133";
	var bar = $(this).parent();
	$(bar).animate({
		backgroundColor: colour
	}, "fast");
}

function onKeyDown(event) {
	if (event.keyCode === 13) {
		var input = event.currentTarget;
		var message = input.value;
		if (message.length === 0) return;
		input.value = "";
		var chatBox = $(this).closest("div.chat_box");
		var chatRoom = Server.getChatRoomByReceiver(chatBox.attr("data-name"));

		if (Server.getUsername() === undefined) {
			chatRoom.attachMessage(false, "You must be logged in to send messages.");
		} else {
			chatRoom.attachMessage(Server.getUsername(), message);
			chatRoom.sendMessage(message);
		}
	}
}

// name of the target (receiver)
function getChatBox(name) {
	var bar = $("#bottom_bar");
	var chatBox = $("div[data-name='" + name +"']");
	if (chatBox.length == 0) {
		// create chatbox
		var template = $("#chat_template");
		chatBox = template.clone();
		chatBox.removeAttr('id');
		chatBox.attr("data-name", name);
		chatBox.find("p.title_data").text("To: " + name);
		chatBox.find("p.min_data").text(name + " (0)");
		bar.append(chatBox);
		chatBox.css("display", "inline-block");

		var input = $("input.chat_input_event");
		input.on("keydown", onKeyDown);
		input.on("focus", focusHandler);
		input.on("focusout", focusHandler);

		// controls resizing of the chat box
		$('.chat_frame').resizable({
			handles: 'n, e, ne',
			minWidth: 300,
			minHeight: 100,
			maxWidth: 700,
			maxHeight: 500,
			resize: function(event, ui) {
				// fixes resize problem!
				ui.helper.css('top', '');
			}
		});
		return chatBox;
	}
	return chatBox;
}

// start a new private message chat
function privateMessage(receiver) {
	var chatRoom = Server.getChatRoomByReceiver(receiver);
	if (chatRoom === undefined) {
		chatRoom = new ChatRoom(receiver);
	}
	chatRoom.toggle(true);
}

function toggle(element, show) {
	var chatBox = $(element).closest("div.chat_box");
	var receiver = chatBox.attr("data-name");
	Server.getChatRoomByReceiver(receiver).toggle(show);
}