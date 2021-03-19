<?php
require_once("utils-login.php");

function signUp($email, $pass, $fname, $lname) {
    // Hash the password.
    $passHash = password_hash($pass, PASSWORD_DEFAULT);

    // Prepare the statement.
    $db = getDB();
    $query = mysqli_prepare($db, "
        INSERT INTO user
            (email, password, fname, lname, picture_id)
        VALUES
            (?, ?, ?, ?, 1)
        ;");
    if (!$query) {
        die(mysqli_error($db));
    }

    // Bind the params.
    mysqli_stmt_bind_param($query, "ssss", $email, $passHash, $fname, $lname);

    // Execute the statement.
    if (execInsertStmt($query) === false) {
        echo "<script type='text/javascript'>alert('A problem occurred during signup. Please report this.');</script>";
        header("Location: ../pages/signup.php");
    }

    // Log in.
    logIn($email, $pass);
}

?>