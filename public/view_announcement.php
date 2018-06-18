<?php
require_once("../includes/init.php");

function printComments($id) {
    $comments = DB::query("SELECT * FROM announcement_comments WHERE announcement_id = ?",
        array($id));
    if ($comments) {
        while ($comment = $comments->fetch()) {
            $user = new User(User::findID($comment["commenter"]), $comment["commenter"]);
            ?>
            <section class="comment_section">
                <div class="profile_container">
                    <img class="profile_icon"
                         src="<?php echo $user->profile_picture; ?>" alt="profile picture">
                </div>
                <div class="comment_container">
                    <span class="username"><?php User::printAsLink($user); ?></span>
                    <span class="post_date"><?php echo $comment["comment_date"]; ?></span>
                    <div class="box_padding"><?php echo $comment["comment_text"]; ?></div>
                </div>
            </section>
            <?php
        }
    } else {
        echo "<div id='no_comments_message'>No comments yet!</div>";
    }
    exit();
}

if (array_key_exists("requestComments", $_POST)) {
    printComments($_POST["announcement_id"]);
} elseif (array_key_exists("posting_comment", $_POST)) {
    $announcement_id = $_POST["announcement_id"];
    $comment_text = nl2br($_POST["comment_text"]);
    DB::query("INSERT INTO announcement_comments (announcement_id, commenter, comment_text)
                VALUES (?, ?, ?)", array($announcement_id, $_USER->username, $comment_text));

    printComments($announcement_id);
}
if (!array_key_exists("id", $_GET))
    redirect("announcements.php");

require_once("../includes/header.php");
$id = $_GET["id"];
$result = DB::query("SELECT * FROM announcements WHERE id = ?", array($id));
if (!$result)
    redirect("announcements.php");
$row = $result->fetch();
$user = new User(USER::findID($row["author"]), $row["author"]);
$post_date = $row["post_date"];
$content = $row["content"];
$title = $row["title"];
?>

<div class="simple_container-dark text_container">
    <div class="news_title_container light_container">
        <img class="profile_picture_icon" src="<?php echo $user->profile_picture; ?>" alt="profile picture">
        <p class="news_title"><?php echo $row["title"]; ?></p>
        <p class="news_info">posted by
            <?php USER::printAsLink($user); ?>, <?php echo $row["post_date"]; ?></p>
    </div>
    <div class="box_padding dark_container">
        <?php echo $row["content"]; ?>
    </div>
</div>

<!-- leave a comment: -->
<div class="narrow_container simple_container-dark">
    <div id="add_comment_area" class="textarea_dark flexbox-vertical">
        <textarea id="comment_textbox" rows="4" maxlength="2000" placeholder="leave a comment"
                  style="display: inline; width: 800px;"></textarea>
        <div style='margin: auto;'>
            <div onclick="post_comment('<?php echo $id; ?>')" class='themed_button-light'>Post</div>
        </div>
    </div>
</div>

<p class="heading">Comments:</p>

<!-- comment: -->
<div id="comments_container" data-id="<?php echo $id; ?>"></div>
<script type="text/javascript" src="scripts/comment_manager.js"></script>

<?php require_once("../includes/footer.php"); ?>