<?php
require_once("utils-db.php");

function logIn($email, $pass) {
    if (session_status() === PHP_SESSION_NONE)
        session_start(); 

    // Get the database.
    $db = getDB();

    // Get the stored password hash for this email.
    $query = mysqli_prepare($db, "
        SELECT
            user_id,
            password
        FROM
            user
        WHERE 
            email = ?
        ;
    ");
    mysqli_stmt_bind_param($query, "s", $email);

    $result = mysqli_fetch_assoc(execQueryStmt($query));
    if (!isset($result)) {
        header("Location: ../pages/login.php?error=Your account credentials couldn't be found.");
        die();
    }

    if (password_verify($pass, $result["password"])) {
        $_SESSION["uid"] = $result["user_id"];
    } else {
        header("Location: ../pages/login.php?error=Those credentials are incorrect.");
        die();
    }
}

function logOut() {
    if (session_status() === PHP_SESSION_NONE)
        session_start(); 
    
    unset($_SESSION["uid"]);
}

function isLoggedIn() {
    if (session_status() === PHP_SESSION_NONE)
        session_start(); 

    return isset($_SESSION["uid"]);
}

function forceLogIn() {
    if (!isLoggedIn()) {
        header("Location: ../pages/login.php");
    }
}

function checkUID($targetUID) {
    if (session_status() === PHP_SESSION_NONE)
        session_start(); 

    return isset($_SESSION["uid"]) && (strcmp($_SESSION["uid"], $targetUID) === 0);
}

?>