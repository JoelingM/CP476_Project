<?php
require_once("../utils/utils-signup.php");

if (!isset($_POST["fname"]) || empty($_POST["fname"])) {
    header("Location: ../pages/signup.php?error=A first name is required.");
    die();
} else if (!isset($_POST["lname"]) || empty($_POST["lname"])) {
    header("Location: ../pages/signup.php?error=A last name is required.");
    die();
} else if (!isset($_POST["email"]) || empty($_POST["email"])) {
    header("Location: ../pages/signup.php?error=An email address is required.");
    die();
} else if (!isset($_POST["password"]) || empty($_POST["password"]) || !isset($_POST["password2"]) || empty($_POST["password2"])) {
    header("Location: ../pages/signup.php?error=A password is required.");
    die();
} else if (strcmp($_POST["password"], $_POST["password2"]) !== 0) {
    header("Location: ../pages/signup.php?error=The passwords are not equal.");
    die();
} else {
    // Do the signup.
    try {
    signUp($_POST["email"], $_POST["password"], $_POST["fname"], $_POST["lname"]);
    } catch (Exception $e) {
        // Determine what went wrong.
        $message = $e->getMessage();
        if (preg_match("/^Duplicate entry.*for key 'email'.*/", $message)) {
            header("Location: ../pages/signup.php?error=The selected email address is already registered to another user. Your account wasn't created.");
            die();
        } else {
            header("Location: ../pages/signup.php?error=An unknown error occurred.");
            die();
        }
    }

    // Redirect to the home page.
    header("Location: ../pages/home.php");
}
