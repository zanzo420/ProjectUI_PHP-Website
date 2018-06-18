$(document).ready(function() {
	 // navigation animation
	var entered = false;
	var expanded = false;
	$('.expandable').mouseenter(function() {
		entered = true;		
		var listNode = $(this);
		var container = $('#nav_sublist_container');		
		
		// clear all child elements
		for (var i = 0; i < container.children().length; i++) {
			var child = $(container.children()[i]);
			if (child.attr("class") == "nav_sublist") {
				child.css("display", "none");
			}
		}
		
		// find and show sublist
		var nav_id = listNode.attr("data-nav_id");		
		var sublist = $(document).find("[data-parent_id='" + nav_id + "']");
		sublist.css("display", "block");
		
		if (container.is(":animated")) {
			// reset height to avoid animation problems
			container.stop();
			container.css("height", "auto");			
		} else {		
			// must be called first before the position call
			container.css("height", "auto");
			if (!expanded) {
				container.stop().slideDown('normal');		
			}			
		}		
		expanded = true;		
		container.position({
			of: listNode,
			my: "center top",
			at: "center bottom",
			collision: "fit none"
		});	
		container.css("top", "70px");
	});
	
	$('#nav_sublist_container').mouseleave(function() {
		entered = false; 
		$('#nav_sublist_container').stop().slideUp('normal');
		expanded = false;
	});
	
	$('.nav_item').mouseleave(function() {	
		entered = false; 
		window.setTimeout(function() {
			var container = $('#nav_sublist_container');
			if (!container.is(':hover') && !entered)	{
				container.stop().slideUp('normal');
				expanded = false;
			}
		}, 240);
	});

	var header = $("#header");
	var background = $("#background_artwork");	
	var scrollTimer;

	function scrollHandler() {
		scrollTimer = null;
		var value = -(($(this).scrollTop() * 0.2) + 200);
		background.stop().animate({ bottom: value }, "slow", "easeOutCirc");
	}
	
	$(document).on("scroll", function() {		
		if (scrollTimer) { clearTimeout(scrollTimer); }
		scrollTimer = setTimeout(scrollHandler, 10);
		
		if ($(this).scrollTop() >= 36) {	
			$(header).css('position', 'fixed');
			$(header).css('top', '-36px');
		} else {
			$(header).css('position', 'absolute');
			$(header).css('top', '0px');
		}		
	});
});

// can add this stuff in jquery: on("click", function() {});
function toggleLoginBox() {
	var fixedPanel = $("#fullscreen_fixed_panel");
	var loginBox = $("#login_box");
	if (fixedPanel.is(":visible")) {
		loginBox.fadeOut("normal", function() {
			fixedPanel.fadeOut("normal");
		});
	} else {
		fixedPanel.fadeIn("normal", function() {
			loginBox.fadeIn( "normal");
		});
	}
}

function togglePanel(id) {
	var fixedPanel = $("#fullscreen_fixed_panel");
	if (fixedPanel.is(":visible")) {
		$("#" + id).fadeOut("normal", function() {
			fixedPanel.fadeOut("normal");
		});
	} else {
		fixedPanel.fadeIn("normal", function() {
			$("#" + id).fadeIn( "normal");
		});
	}
}










