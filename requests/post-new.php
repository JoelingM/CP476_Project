<?php
require_once("../utils/utils-home.php");

if (!isset($_POST["userID"]) || empty($_POST["postContent"])) {
    header("Location: ../pages/home.php?error=Post content must be filled.");
    die();
} else {
    // Make the post.
    if (is_uploaded_file($_FILES["picture"]["tmp_name"])) {
        makePost($_POST["userID"], $_POST["postContent"], $_FILES["picture"]);
    } else {
        makePost($_POST["userID"], $_POST["postContent"]);
    }
    
    // Redirect
    header("Location: ../pages/home.php");
}
?>