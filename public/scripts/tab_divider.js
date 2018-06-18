$(document).ready(function() {	
	$("ul.tabs li").on("click", function() {	
		if ($(this).hasClass("active")) return;
		var body;
		var type = $(this).attr("data-type");
		$(this).parent().parent().children("div").each(function () {
			if ($(this).attr("data-type") == type) {
				body = $(this);
			}
		});
		if (typeof body !== "undefined") {
			if (body.css("display") == "block") return;			
			$("div.tab-body").each(function() {
				$(this).css("display", "none");
			});
			$("ul.tabs").children("li").each(function() {				
				$(this).removeClass("active");
				$(this).addClass("inactive");	
			});			
			body.css("display", "block");	
			$(this).removeClass("inactive");
			$(this).addClass("active");	
		}
	});
	$("div.tab-body").first().css("display", "block");
});

function tab_onclick() {
	
}