var mongo = require('mongodb').MongoClient;
var server = require('socket.io').listen(8888).sockets;
var database;

// TODO: Chat history feature!
/*function findMessageToken(sender, receiver) {
	var tokens = database.collection("tokens");
	var token = tokens.find({
		$or: [
			{sender: sender, receiver: receiver},
			{sender: receiver, receiver: sender}
		]
	}).limit(1).next(function(err, doc){
		if (err) throw err;
		console.log("inside token:");
		console.log(doc.token);
		return doc.token;
	});

	console.log("outside token:");
	console.log(token);
}*/

function Socket(socket) {
	this.socket = socket;
	this.input = function(data) {
		if (data.receiver !== "General Chat") {
			// private chat
			// TODO: Chat history feature!
			/*var token = findMessageToken(data.sender, data.receiver);
			if (!token) {
				token = data.sender + ":" + data.receiver
				database.collection("tokens").insertOne({
					sender: data.sender,
					receiver: data.receiver,
					token: token
				});
			}
			database.collection(token).insertOne({
				owner: data.sender,
				message: data.message
			});*/
			database.collection("messages").insertOne(data);
			this.to(data.receiver).emit('input', data);
		} else {
			data.room = "General Chat";
			this.broadcast.emit("input", data);
		}
	};

	// register the chat room
	this.register = function(data) {
		var room = data.room; // username of client
		var results = database.collection('messages').find({ room: room });
		if (results.length > 0) {
			this.emit("history", { history: results });
		}
		this.join(room);
	};

	this.socket.on('input', this.input);
	this.socket.on("register", this.register);
}

// chat is the name of the database!
mongo.connect('mongodb://localhost/chat', function(error, db) {
	if (error) throw error;
	database = db;
	server.on('connection', function(socket) {
		console.log("user connected");
		new Socket(socket);
	});
	console.log("chat room online");
}); 


