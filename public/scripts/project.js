// contains basic animations - no validation
var current_section = false;
function section_show(section) {
	section.stop().animate({
		opacity: 1,
		height: "show"
	}, "slow");
	current_section = section;
}

function deleteProject() {

}

$(document).ready(function() {
	// tooltip, image, and video effects
	var img_tip = $("#image_tooltip");
	var vid_tip = $("#video_tooltip");
	var image_delete_btn = img_tip.find("div.icon-close").eq(0);
	var image_mag_btn = img_tip.find("div.icon-mag").eq(0);
	var image_tooltip = img_tip.find("p").eq(0);

	image_mag_btn.on("click", function() {
		var img = img_tip.parent().find("img").eq(0);
		window.open(img.attr("src"), "_tab");
	});

	image_delete_btn.on("mouseenter", function () {
		image_tooltip.text("delete image");
		image_tooltip.stop().fadeIn();
	});
	image_delete_btn.on("mouseleave", function () {
		image_tooltip.stop().fadeOut();
	});

	image_mag_btn.on("mouseenter", function () {
		image_tooltip.text("enlarge image");
		image_tooltip.stop().fadeIn();
	});
	image_mag_btn.on("mouseleave", function () {
		image_tooltip.stop().fadeOut();
	});

	var video_delete_btn = vid_tip.find("div.icon-close").eq(0);
	var video_tooltip = vid_tip.find("p").eq(0);

	video_delete_btn.on("mouseenter", function () {
		video_tooltip.text("delete video");
		video_tooltip.stop().fadeIn();
	});
	video_delete_btn.on("mouseleave", function () {
		video_tooltip.stop().fadeOut();
	});

	$("div.video_container").each(function () {
		$(this).on("mouseenter", function () {
			vid_tip.stop();
			vid_tip.slideUp(1);

			vid_tip.css("display", "none");
			$(this).prepend(vid_tip);
			vid_tip.slideDown();
		});
		$(this).on("mouseleave", function () {
			vid_tip.slideUp();
		});
	});

	$("div.image_container").each(function () {
		$(this).on("mouseenter", function () {
			img_tip.stop();
			img_tip.slideUp(1);
			$("div.image_container").each(function () {
				var img = $(this).find("img").eq(0);
				$(this).css("width", $(this).width());
				img.stop().animate({opacity: "0.5" }, "normal");
			});
			var img = $(this).find("img").eq(0);
			img.stop().animate({opacity: "1"}, "normal", function () {
				img.parent().prepend(img_tip);
				img_tip.slideDown();
			});
		});
		$(this).on("mouseleave", function () {
			img_tip.slideUp(function () {
				$("div.image_container img").each(function () {
					if (!$(this).is(":hover")) {
						$(this).stop().animate({opacity: "1"}, "normal");
					}
				});
			});
		});
	});

	// tab animations
	var tabs = $('#banner_list').find("li");
	tabs.click(function() {
		var type = $(this).attr("data-type");
		var clicked_section = false;		
		$("section.page_section").each(function () {
			if ($(this).attr("data-type") == type) {
				clicked_section = $(this);
			}
		});
		if (current_section && clicked_section) {	
			if (current_section.is(clicked_section)) return; // jQuery object equality
			current_section.stop().animate({
				opacity: 0,
				height: "hide"
			}, "slow", function() {
				section_show(clicked_section);
			});
		} else if (clicked_section) {
			section_show(clicked_section);
		}
		tabs.css("background-color", "initial");
		tabs.css("cursor", "pointer");

		$(this).css("background-color", "#005eff");
		$(this).css("cursor", "initial");

		var arrow = $("#arrow_marker");
		arrow.css("display", "block");
		arrow.position({
			of: $(this),
			my: "center top",
			at: "center bottom",
			collision: "fit none"
		});			
	});
	tabs.first().click();
});