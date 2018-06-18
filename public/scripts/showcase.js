var current_projectId = 0;
var lockdown = false;

// parses JSON data and adds to page
function loadData(data) {
	current_projectId = data["id"];		
	var title = data["title"];
	var author = data["author"];
	var downloads = data["downloads"].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	var description = data["description"];
	var image_path = data["image_path"];
	
	$("#showcase_image").attr("src", image_path);
	$("#project_description").text(description);
	
	$("#project_header").html("<span class='label'>" + title + 
		"</span>" + downloads + "<span style='font-size: 10pt'> downloads</span>");
	
	$.ajax({
		type: "POST",
		url: "showcase.php",
		data: { 
			requestComments: true,
			projectId: current_projectId,
		},
		success: function(data) {			
			appendComments(data);
		},
		error: function(xmlHttp) {
			console.log(xmlHttp.responseText);
		}
	});
}

/* adds html comments (section's) to comments_container */
function appendComments(data) {
	var container = $("#comments_container");
	container.empty(); // data will contain ALL comments, so each time a new comment is posted, the container should be emptied
	$("<div></div>").html(data).hide().prependTo(container).fadeIn("slow");
}

$(document).ready(function() {
	/*
	$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
		options.async = true;
	});
	*/
	
	$.ajax({
		type: "POST",
		url: "showcase.php",
		data: { requestProjectData: true },
		success: function(data) {
			if (data["id"]) {
				loadData(data);
				$("#unavailable").css("display", "none");
				$("#available").css("display", "initial");
			} else {
				$("#available").css("display", "none");
				$("#unavailable").css("display", "initial");
			}
		},
		error: function(xmlHttp) {
			console.log("error");
			console.log(xmlHttp.responseText);
		}
	});

    $("#like_button, #dislike_button").click(function(event) {
		if (lockdown) return false;
		lockdown = true;
		
		var liked = (event.target.id == "like_button");
	
		$.ajax({
			type: "POST",
			url: "showcase.php",			
			data: {
				postingLike: true,
				projectId: current_projectId,
				liked: liked
			},			
			success: function(data) {
				$(document.body).append(data);
				if (data["id"]) {
					$("#page").fadeOut("slow", function() {												
						loadData(data);						
						$("#page").fadeIn("slow", function() {
							$("#vote_area").removeClass("disabled");
							$("#add_comment_area").removeClass("disabled");	
							lockdown = false;
						});				
					});					
				}	else {
					$("#page").fadeOut("slow", function() {			
						$("#available").css("display", "none");
						$("#unavailable").css("display", "initial");
						$("#page").fadeIn("slow");
					});
				}		
			},
			error: function(jqXML, textStatus, errorThrown) {
				console.log(jqXML.responseText);
			}
		});
	
		$("#vote_area").addClass("disabled");
		$("#add_comment_area").addClass("disabled");	
		$("#page").fadeOut("slow");
	});	
	
	$("#comment_textbox").focus(function() { 
		$(this).css("border-color", "#005eff");
	});
});

function post_comment() {
	var text = $("#comment_textbox").val();
	if (text.length > 20) {
		$("#comment_textbox").val("");
		$.ajax({
			type: "POST",
			url: "showcase.php",
			data: {
				postingComment: true,
				projectId: current_projectId,
				comment_text: text,
			},
			success: function(data) {
				appendComments(data);
			},
			error: function(xmlHttp) {				
				console.log(xmlHttp.responseText);
			}
		});
	} else {
		$("#comment_textbox").css("border-color", "red");
		alert("Comment's must be at least 20 characters long!");
	}
}