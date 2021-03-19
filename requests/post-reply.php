<?php
require_once("../utils/utils-home.php");
    if (!isset($_POST["postID"]) || empty($_POST["replyContent"]) || empty($_POST["userID"])) {
        header("Location: ../pages/home.php?error=Content must be filled.");
        die();
    } else {
        // Do the reply.
        replyToPost($_POST["postID"], $_POST["replyContent"], $_POST["userID"]);
    }
?>