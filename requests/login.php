<?php
    require_once("../utils/utils-login.php");

    if (!isset($_POST["email"], $_POST["password"])) {
        header("Location: ../pages/login.php?error=The email and password need to be filled out.");
        die();
    }

    logIn(trim($_POST["email"]), trim($_POST["password"]));
    header("Location: ../pages/home.php");

?>