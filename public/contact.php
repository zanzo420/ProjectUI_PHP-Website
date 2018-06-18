<?php
require_once("../includes/init.php");
$title = (array_key_exists("title", $_POST)) ? $_POST["title"] : "";
$email = (array_key_exists("email", $_POST)) ? $_POST["email"] : "";
$message = (array_key_exists("message", $_POST)) ? $_POST["message"] : "";

if (array_key_exists("post_announcement", $_POST)) {
    // handle new post, return to
    $errors = array();
    if (strlen($title) < 5 || strlen($title) > 50) {
        $errors[] = "Title must be 5 to 100 characters long";
    }
    if (strlen($message) < 100) {
        $errors[] = "Content must be at least 50 characters long";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address";
    }
    if (count($errors) === 0) {
        $username = LS::GetUsername();
        $message = nl2br($message); // preserve line breaks
        DB::query("INSERT INTO contact_messages (sender, email, title, message) VALUES(?, ?, ?, ?);",
           array($username, $email, $title, $message));
        redirect("contact.php?success=true");
    } else {
        require_once("../includes/header.php");
        echo "<div id='errors_box' style='display: block'>";
        echo 	"<div class='errors_area'>";
        echo 		"<p>Please fix the following errors before continuing:</p>";
        echo 		"<ul class='errors_list'>";
        foreach ($errors as $error) {
            echo "<li>{$error}</li>";
        }
        echo		"</ul>";
        echo	"</div>";
        echo "</div>";
    }
} else {
    require_once("../includes/header.php");
    if (array_key_exists("success", $_GET)) {
        echo "<div id='success_box' style='display: block'>";
        echo 	"<div class='success_area'>";
        echo     "<p>Successfully submitted!</p>";
        echo     "<p></p>";
        echo     "<p>redirecting...</p>";
        echo	"</div>";
        echo "</div>";
        header( "refresh:1;url=contact.php" );
    }
}
?>
<div class="narrow_container">
    <p class="heading">Contact Us</p>
    <br/>
    <div class="news_entry">
        <form action="contact.php" method="POST">
            <div class="dark_container">
                <div class="textarea_dark" style="margin: 20px 40px;">
                    <p class="label">Title*</p>
                    <input class="basic_field" type="text" name="title" value="<?php echo $title; ?>">
                    <p class="label">Email Address*</p>
                    <input class="basic_field" type="email" name="email" value="<?php echo $email; ?>">
                    <p class="label">Message*</p>
                    <textarea name="message" rows="10"><?php echo $message; ?></textarea>
                    <br /><br />
                    <input type="submit" class="themed_button-light" name="posting" value="Send Message" />
                </div>
            </div>
            <input type="hidden" name="post_announcement" value="true">
        </form>
    </div>
</div>
<?php require("../includes/footer.php"); ?>