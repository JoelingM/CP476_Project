<?php
require_once("../utils/utils-db.php");
require_once("../utils/utils-login.php");
require_once("../utils/utils-profile.php");

// This page can only be seen when someone is logged in.
forceLogIn();

// Determine the page offset (for the feed).
$page = (isset($_GET["page"]) &&  !is_nan(intval($_GET["page"]))) ? max(intval($_GET["page"]), 1) : 1;

// Get the database.
$db = getDB();

// Get this user's id.
$uid = $_SESSION["uid"];

// Determine what user is being seeked.
if (!isset($_GET["uid"]) || is_nan(intval($_GET["uid"]))) {
    header("Location: ../pages/home.php?error=Couldn't load profile.");
    die();
}
$targetId = intval($_GET["uid"]);

// Get the user's profile information.
$userData = getProfile($targetId);

// Get the user's feed.
$feed = getFeed($targetId, true, $page - 1);
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
    <link href="../styles/profile.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script type="text/javascript" src="../javascript/home.js"></script>

</head>

<body>
    <?php require_once("fragments/header.php"); ?>
    <main>
        <?php require_once("fragments/sidenav.php"); ?>
        <div id="content">
            <div id="profile-overview">
                <div id="img-box">
                    <img id="profile-picture" src="../<?php echo $userData["profile_picture"]; ?>" alt="<?php echo $userData["fname"] . " " . $userData["lname"]; ?>">
                    <?php
                    if ($userData["user_id"] !== $uid) {
                        if (!isPal($targetId)) {
                    ?>
                            <a href="../requests/pal-new.php?uid=<?php echo $userData["user_id"]; ?>">
                                <button id="pal-button">Add Pal</button>
                            </a>

                        <?php
                        } else {
                        ?>
                            <a href="../requests/pal-delete.php?uid=<?php echo $userData["user_id"]; ?>">
                                <button id="pal-button">Remove Pal</button>
                            </a>
                    <?php
                        }
                    }
                    ?>
                </div>
                <div id="profile-details">
                    <h2 id="name"><?php echo $userData["fname"] . " " . $userData["lname"]; ?></h2>
                    <hr>
                    <table id="details-table">
                        <?php
                        if (isset($userData["birthday"]) && !empty($userData["birthday"])) {
                        ?>
                            <tr>
                                <th>Birthday</th>
                                <td><?php echo date_format(date_create($userData["birthday"]), "M j, Y"); ?></td>
                            </tr>
                        <?php
                        }
                        if (isset($userData["city"]) && !empty($userData["city"])) {
                        ?>
                            <tr>
                                <th>City</th>
                                <td><?php echo $userData["city"]; ?></td>
                            </tr>
                        <?php
                        }
                        if (isset($userData["education"]) && !empty($userData["education"])) {
                        ?>
                            <tr>
                                <th>Education</th>
                                <td><?php echo $userData["education"]; ?></td>
                            </tr>
                        <?php
                        }
                        if (isset($userData["hobbies"]) && !empty($userData["hobbies"])) {
                        ?>
                            <tr>
                                <th>Hobbies</th>
                                <td><?php echo $userData["hobbies"]; ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </table>
                </div>
            </div>
            <div id="wall">
                <hr>
                <span id="wall-label"><?php echo $userData["fname"] . "'" . (strtolower($userData["fname"][-1]) === "s" ? "" : "s"); ?> Wall</span>
                <div id="post-area" class="only-me">
                    <!-- Posts are loaded dynamically through the loadAllPosts function of home.js -->
                </div>
            </div>
        </div>
    </main>
    <?php require_once("fragments/footer.php"); ?>
</body>