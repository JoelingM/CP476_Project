<?php
require_once("../utils/utils-home.php");

if (!isset($_POST["postID"])) {
    header("Location: ../pages/home.php?error=Content must be filled.");
    die();
} else {
    // Delete the post.
    likePostToggle($_POST["postID"]);
}
?>