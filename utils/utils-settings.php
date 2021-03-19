<?php
// Get the password hash associated with the current user and check if it equals the provided password.
function _checkPassword($password)
{
    require_once("utils-login.php");
    require_once("utils-db.php");

    // Ensure the user is logged in.
    forceLogin();

    // Get the database.
    $db = getDB();

    // Get the user's id.
    $uid = $_SESSION["uid"];

    // Get this user's password hash.
    $query = mysqli_prepare($db, "
        SELECT
            password
        FROM 
            user
        WHERE
            user_id = ?
    ;");
    mysqli_stmt_bind_param($query, "d", $uid);

    // Check the password.
    $result = mysqli_fetch_assoc(execQueryStmt($query));
    if (is_null($result)) {
        return NULL;
    } else {
        return password_verify($password, $result["password"]);
    }
}

// Update the user's account settings.
function postSettings()
{
    require_once("utils-login.php");
    require_once("utils-db.php");
    require_once("utils-home.php");

    // Ensure the user is logged in.
    forceLogin();

    // Get the database.
    $db = getDB();

    // Get the user's id.
    $uid = $_SESSION["uid"];

    // Get the POST arguments.
    $fname = $_POST["first-name"];
    $lname = $_POST["last-name"];
    $birthday = $_POST["birthday"];
    $city = $_POST["city"];
    $education = $_POST["education"];
    $hobbies = $_POST["hobbies"];
    $currentPassword = $_POST["current-password"];
    $email1 = $_POST["email1"];
    $email2 = $_POST["email2"];
    $password1 = $_POST["password1"];
    $password2 = $_POST["password2"];

    // Check if the profile picture has been updated.
    $pictureId = NULL;
    if (is_uploaded_file($_FILES["profile-picture"]["tmp_name"])) {
        $result = uploadImage($_FILES["profile-picture"]);
        $pictureId = $result["picture_id"];
    }

    // Ensure that the user entered the correct password.
    if (!empty($password1) || !empty($email1)) {
        if (!_checkPassword($currentPassword)) {
            header("Location: ../pages/home.php?error=The provided password didn't match our records. No changes were made.");
            die();
        }
    }

    // Create the query dynamically based on which fields have been updated.
    $query = "
            UPDATE user
            SET 
        ";
    $args = [];
    if (!empty($fname)) {
        $assignmentList[] = "fname = ?";
        $args[] = $fname;
    }
    if (!empty($lname)) {
        $assignmentList[] = "lname = ?";
        $args[] = $lname;
    }
    if (!empty($birthday)) {
        $assignmentList[] = "birthday = ?";
        $args[] = $birthday;
    }
    if (!empty($city)) {
        $assignmentList[] = "city = ?";
        $args[] = $city;
    }
    if (!empty($education)) {
        $assignmentList[] = "education = ?";
        $args[] = $education;
    }
    if (!empty($hobbies)) {
        $assignmentList[] = "hobbies = ?";
        $args[] = $hobbies;
    }
    if (!empty($email1) && !empty($email2)) {
        if ($email1 === $email2) {
            $assignmentList[] = "email = ?";
            $args[] = $email1;
        } else {
            header("Location: ../pages/home.php?error=The selected email addresses are not equal. No changes were made.");
            die();
        }
    }
    if (!empty($password1) && !empty($password2)) {
        if ($password1 === $password2) {
            $assignmentList[] = "password = ?";

            // Hash the password.
            $passHash = password_hash($password1, PASSWORD_DEFAULT);
            $args[] = $passHash;
        } else {
            header("Location: ../pages/home.php?error=The selected passwords are not equal. No changes were made.");
            die();
        }
    }
    if (isset($pictureId)) {
        $assignmentList[] = "picture_id = ?";
        $args[] = $pictureId;
    }

    // Prepare the statement.
    $query .= join(", ", $assignmentList) . "
            WHERE user_id = ?
        ;";
    $query = mysqli_prepare($db, $query);
    $args[] = $uid;
    mysqli_stmt_bind_param($query, str_repeat("s", sizeof($args)), ...$args);

    try {
        execInsertStmt($query);
    } catch (Exception $e) {
        // Determine what went wrong.
        $message = $e->getMessage();
        if (preg_match("/^Duplicate entry.*for key 'email'.*/", $message)) {
            header("Location: ../pages/home.php?error=The selected email address is already registered to another user. No changes were made.");
            die();
        } else {
            header("Location: ../pages/home.php?error=An unknown error occurred. No changes were made.");
            die();
        }
    }
}

?>