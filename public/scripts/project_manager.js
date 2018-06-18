var changes = []; // all changes caused by the user on the web page
var uploaded_images = {};
var uploaded_videos = {};
var install_step_data = {};
var project_manager;

// so far only works for images
function setTipFunctions(tip) {
	var image_delete_btn = tip.find("div.icon-close").eq(0);
	var image_mag_btn = tip.find("div.icon-mag").eq(0);

	image_delete_btn.on("click", function() {
		var container = tip.parent();
		var id = container.data("image_id");
		changes.push("delete_image_" + id);
		container.hide();
	});
	image_mag_btn.on("click", function() {
		var img = tip.parent().find("img").eq(0);
		window.open(img.attr("src"), "_tab");
	});
}

function ErrorDetection() {
	this.errors = [];	
	
	this.reportErrors = function() {
		var box = $("#errors_box");
		var list = box.find("ul").eq(0);		
		list.empty();		
		for (var i = 0; i < this.errors.length; i++) {
			list.append("<li>" + this.errors[i] + "</li>");
		}
		box.css("display", "block");
		window.scrollTo(0, 0);
	};	
	
	this.containsErrors = function() {
		return (this.errors.length > 0);
	};
	
	this.addError = function(error) {
		this.errors.push(error);
	};
	
	this.scan = function(name, value) {
		return; // breaks scan!
		var rules = ErrorDetection.preset_rules[name];
		if (rules == undefined) return;
		var result;
		if (value === undefined)
			result = name + " is required";
		else if (rules.hasOwnProperty("func"))
			result = rules.func(value);
		if (typeof result === "array") {
			var self = this;
			result.forEach(function (error) {
				self.addError(error);
			});
		} else {
			this.addError(result);
		}
	};
}

ErrorDetection.preset_rules = {
	project_title: {
		name: "Project title",
		min: 5, max: 50,
		func: function(value) {
			if (typeof value === "string") { value = value.length; }
			if (value < this.min || value > this.max) {
				return this.name + " must be " + this.min + " to " + this.max + " characters long";
			}
		}
	},	
	project_version: {
		name: "Project version name",
		max: 8,
		func: function(value) {
			if (typeof value === "string") { value = value.length; }
			if (value > this.max) {
				return this.name + " must be less than " + this.max + " characters long";
			}
		}
	},	
	new_project_version: {
		name: "New project version name",		
		max: 8,
		func: function(value) {
			if (typeof value === "string") { value = value.length; }
			if (value > this.max) {
				return this.name + " must be less than " + this.max + " characters long";
			}
		}
	},
	short_description: {
		name: "Short description",
		min: 10, max: 300,
		func: function(value) {
			if (typeof value === "string") { value = value.length; }
			if (value < this.min || value > this.max) {
				return this.name + " must be " + this.min + " to " + this.max + " characters long";
			}
		}
	},	
	banner: {		
		name: "Banner",
		maxWidth: 1200, maxHeight: 380,
		func: function() {
			var img = uploaded_images.banner;
			if (img.width > this.maxWidth || img.height > this.maxHeight) {
				return this.name + " cannot be larger than " + this.maxWidth + "px by " + this.maxHeight + "px";
			}
		}
	},
	add_video: {
		name: "Video URL",
		max: 20,
		func: function() {
			
		}
	},
	file_upload: {
		name: "New file upload",
		max: 52428800,
		func: function(value) {
			var errors = [];
			if (value.name === undefined)
				errors.push(this.name + " is required");
			else {
				if (value.size > this.max)
					errors.push(this.name + " cannot be larger than 50M");
				if (!value.name.endsWith(".zip"))
					errors.push(this.name + " must be a '.zip' file");
			}
			return errors;
		}
	}
};

function getElementValue(element) {
	if (!element) return;
	var name = element.attr("name");
	if (!name) return;
	
	var value;
	var tagName = element.prop("tagName");
	var type = element.attr("type");
		
	if (element[0].files && element[0].files.length > 0) {
		value = element[0].files[0];
	} else if (tagName === "INPUT" && type === "radio") {
		value = element.is(':checked');		
	} else {
		 value = element.val();
	}
	return value;
}

// also does error detection
function collectData(e) {
	var data = new FormData();
	for (var i = 0; i < changes.length; i++) {
		var name = changes[i];
		var value;
		if (name.includes("delete_image")) {
			value = true;
		} else {
			var element = $(document).find("input[name='" + name + "'], textarea[name='" + name + "']").eq(0);
			value = getElementValue(element);
			e.scan(name, value);
			if (uploaded_images[name]) {
				var img = uploaded_images[name];
				data.append(name + "_dimensions", JSON.stringify({
					width: img.width,
					height: img.height
				}));
			}
		}
		data.append(name, value);
	}
	var method = $("input[name='method']").val();
	if (method !== undefined)
		data.append("method", method);
	var project_id = $("input[name='project_id']").val();
	if (project_id !== undefined)
		data.append("project_id", project_id);
	data.append("project_data", true);
	return data;
}

function UploadHandler() {
	this.UPLOAD_FINISHED = 1;
	this.SERVER_RESPONDED = 2;

	this.uploadFinished = false;
	this.servedResponded = false;
	this.data = true;

	this.setValue = function(type_id, data) {
		if (type_id === this.UPLOAD_FINISHED)
			this.uploadFinished = true;
		else if (type_id === this.SERVER_RESPONDED) {
			this.servedResponded = true;
			this.data = data;
		}
		if (this.uploadFinished && this.servedResponded && this.data) {
			setTimeout(function() {
				var redirect_url = $("input[name='redirect_url']").val();
				if (redirect_url === undefined)
					redirect_url = data["redirect_url"];
				window.location.href = redirect_url;
			}, 500);
		}
	}
}

function submitData(data, e) {
	var uploadHandler = new UploadHandler();
	$.ajax({
		type: "POST",
		url: "submit_project_data.php",
		data: data,
		contentType: false,
		processData: false,
		success: function(data) {
			console.log(data);
			if (data.errors && data.errors.length > 0) {
				data.errors.forEach(function(error) {
					e.addError(error);
				});
				e.reportErrors();
			} else if (data.success) {
				uploadHandler.setValue(uploadHandler.SERVER_RESPONDED, data);
				// probably should be this anyway..
				//$("<form action=''" + redirect_url + "' method='POST'></form>").submit();
			}
			//$("<div id='debugger'></div>").html(data).appendTo("body");
		},
		error: function(xmlHttp) {
			console.log("error:");
			console.log(xmlHttp.responseText);
			$("<div id='debugger'></div>").html(xmlHttp.responseText).appendTo("body");
		},
		xhr: function() {
			var xhr = new XMLHttpRequest();
			xhr.upload.addEventListener("progress", function(event) {
				if (event.lengthComputable) {
					var value = ((event.loaded / event.total) * 100).toFixed(1) + "%";
					$("#progressText").text("upload progress: " + value);
					$("#progressValue").stop().animate({ width: value }, "slow");
				}
		   });
			xhr.upload.addEventListener('loadend', function() {
				uploadHandler.setValue(uploadHandler.UPLOAD_FINISHED);
			});
		   return xhr;
		}
	});
	var upload_btn = $("#upload_button");
	var progressBar = $("#progressBar");
	if (upload_btn !== undefined) {
		upload_btn.attr('disabled', "disabled");
		upload_btn.addClass("disabled");
	}
	progressBar.css("display", "block");
	progressBar.animate({opacity: "1"});
}

function validateForm() {
	var e = new ErrorDetection();
	$("#errors_box").css("display", "none");

	if (changes.length === 0) { return true; }

	var data = collectData(e, changes);
	if (e.containsErrors()) {
		e.reportErrors();
	} else {
		submitData(data, e);
	}
	return false;
}

function scanForValues() {
	var name =  $(this).attr("name");

	if (typeof name === "undefined") return;

	if (changes.indexOf(name) === -1) {
		changes.push(name);
	}

	if ($(this)[0].files && $(this)[0].files.length > 0) {
		var file = $(this)[0].files[0];
		if (file.type.indexOf("image") > -1) {
			var reader = new FileReader();
			reader.onload = function(event) {
				var result = event.target.result;
				var img = new Image();
				img.onload = function() {
					uploaded_images[name] = img; // only works for banner..
				};
				img.src = result;
			};
			reader.readAsDataURL(file);
		}
	}
}

$(document).ready(function() {
	project_manager = $("#project_manager"); // should be a form

	var method = $("input[name='method']").val();
	var inputs = $("#project_manager input, #project_manager textarea");
	// form submit
	project_manager.on("submit", function() {
		if (method === "CREATE") {
			inputs.each(scanForValues);
		}
		return validateForm(changes); 
	});

	if (method === "UPDATE") {
		inputs.on("change", scanForValues);
	}

	// image and video effects
	var img_tip = $("#image_tooltip");
	var vid_tip = $("#video_tooltip");
	if (img_tip !== undefined && vid_tip !== undefined) {
		setTipFunctions(img_tip);
	}

	// install step buttons:
	$("div.install_step_button").on("click", addInstallStep);
});

function addInstallStep() {
	var parent = $(this).parent();
	var nextStep = parseInt(parent.data("step")) + 1;
	console.log(nextStep);
	var nextElement = $(
		"<div class='install_step'>" +
		"<p>Step " + nextStep + ":</p>" +
		"<textarea name='install_step_" + nextStep + "' rows='10'></textarea>" +
		"<div style='margin-left: 10px; margin-right: 50px;' " +
		"class='plus_button install_step_button'></div>" +
		"</div>"
	).insertAfter(parent.get(0));
	nextElement.data("step", nextStep);
	console.log(nextElement.data("step"));
	nextElement.on("click", addInstallStep);
}

function removeInstallStep() {

}