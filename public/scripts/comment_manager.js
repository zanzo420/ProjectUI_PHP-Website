var announcement_id;
var comment_box;
function post_comment() {
    var text = comment_box.val();
    if (text.length > 20) {
        comment_box.val("");
        $.ajax({
            type: "POST",
            url: "view_announcement.php",
            data: {
                posting_comment: true,
                announcement_id: announcement_id,
                comment_text: text
            },
            success: function(data) {
                appendComments(data);
            },
            error: function(xmlHttp) {
                console.log(xmlHttp.responseText);
            }
        });
    } else {
        comment_box.css("border-color", "red");
        alert("Comment's must be at least 20 characters long!");
    }
}

/* adds html comments (section's) to comments_container */
function appendComments(data) {
    var container = $("#comments_container");
    container.empty(); // data will contain ALL comments, so each time a new comment is posted, the container should be emptied
    $("<div></div>").html(data).hide().prependTo(container).fadeIn("slow");
}

$(document).ready(function() {
    announcement_id = $("#comments_container").data("id");
    comment_box  = $("#comment_textbox");

    $.ajax({
        type: "POST",
        url: "view_announcement.php",
        data: {
            requestComments: true,
            announcement_id: announcement_id
        },
        success: appendComments
    });

    comment_box.focus(function() {
        $(this).css("border-color", "#005eff");
    });
});