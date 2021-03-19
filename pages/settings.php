<?php
require_once("../utils/utils-db.php");
require_once("../utils/utils-login.php");
require_once("../utils/utils-settings.php");
require_once("../utils/utils-profile.php");

// This page can only be seen when someone is logged in.
forceLogIn();

// Get the current settings.
$userData = getProfile($_SESSION["uid"]);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OkBoomer</title>

    <link href="../styles/reset.css" rel="stylesheet">
    <link href="../styles/boomer.css" rel="stylesheet">
    <link href="../styles/color.css" rel="stylesheet">
    <link href="../styles/settings.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="../javascript/settings.js"></script>

</head>

<body>
    <?php require_once("fragments/header.php"); ?>
    <main>
        <?php require_once("fragments/sidenav.php"); ?>
        <div id="content">
            <form id="settings-form" method="post" action="../requests/settings-form.php" enctype="multipart/form-data">
                <div class="form-section">
                    <h2 class="section-label">Profile Settings</h2>
                    <hr>
                    <div class="section-content">
                        <div class="prompt m-prompt">
                            <label class="prompt-label" for="first-name">First name</label>
                            <input id="first-name" type="text" name="first-name" value="<?php echo $userData["fname"]; ?>" required>
                        </div>
                        <div class="prompt m-prompt">
                            <label class="prompt-label" for="last-name">Last name</label>
                            <input id="last-name" type="text" name="last-name" value="<?php echo $userData["lname"]; ?>" required>
                        </div>
                        <div class="prompt l-prompt" id="profile-pic-prompt">
                            <label class="prompt-label" for="profile-pic">Profile picture</label>
                            <img id="profile-pic" src="<?php echo "../" . $userData["profile_picture"]; ?>">
                            <label for="profile-pic-upload" class="file-upload">
                                <i class="fa fa-picture-o"></i>Change profile picture
                            </label>
                            <input id="profile-pic-upload" name="profile-picture" type="file" />
                        </div>
                        <div class="prompt m-prompt">
                            <label class="prompt-label" for="birthday">Birthday</label>
                            <input id="birthday" type="date" name="birthday" value="<?php echo $userData["birthday"]; ?>">
                        </div>
                        <div class="prompt m-prompt">
                            <label class="prompt-label" for="city">City</label>
                            <input id="city" type="text" name="city" value="<?php echo $userData["city"]; ?>">
                        </div>
                        <div class="prompt l-prompt">
                            <label class="prompt-label" for="education">Education</label>
                            <input id="education" type="text" name="education" value="<?php echo $userData["education"]; ?>">
                        </div>
                        <div class="prompt l-prompt">
                            <label class="prompt-label" for="hobbies">Hobbies</label>
                            <input id="hobbies" type="textarea" name="hobbies" value="<?php echo $userData["hobbies"]; ?>">
                        </div>
                    </div>
                </div>
                <div class="form-section">
                    <h2 class="section-label">Account Settings</h2>
                    <hr>
                    <div class="section-content">
                        <div class="prompt l-prompt">
                            <label class="prompt-label" for="current-password">Current password</label>
                            <input id="current-password" type="password" name="current-password">
                        </div>
                        <div class="prompt m-prompt">
                            <label class="prompt-label" for="change-email-1">New email</label>
                            <input id="change-email-1" type="email" name="email1">
                        </div>
                        <div class="prompt m-prompt">
                            <label class="prompt-label" for="change-email-2">Confirm email</label>
                            <input id="change-email-2" type="email" name="email2">
                        </div>
                        <div class="prompt m-prompt">
                            <label class="prompt-label" for="change-password-1">New password</label>
                            <input id="change-password-1" type="password" name="password1">
                        </div>
                        <div class="prompt m-prompt">
                            <label class="prompt-label" for="change-password-2">Confirm password</label>
                            <input id="change-password-2" type="password" name="password2">
                        </div>
                    </div>
                </div>
                <input id="submit" type="submit" value="Submit">
            </form>
        </div>
    </main>
    <?php require_once("fragments/footer.php"); ?>
</body>