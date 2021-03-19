<?php
require_once("../utils/utils-db.php");
require_once("../utils/utils-login.php");

// This page can only be seen when someone is logged in.
forceLogIn();

if (isset($_GET["error"])) {
?>
    <script type="text/javascript">
        alert("<?php echo $_GET["error"]; ?>");
    </script>
<?php
}

// Get the user's name.
$db = getDB();
$query = mysqli_prepare(
    $db,
    "SELECT
        fname
    FROM
        user
    WHERE
        user_id = ?;"
);
mysqli_stmt_bind_param($query, "d", $_SESSION['uid']);
$row = mysqli_fetch_assoc(execQueryStmt($query));
$usersname = $row['fname'];
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
    <link href="../styles/home.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script type="text/javascript" src="../javascript/home.js"></script>
</head>

<body>
    <?php require_once("fragments/header.php"); ?>
    <main>
        <?php require_once("fragments/sidenav.php"); ?>
        <div id="content">
            <div id="new-post-area">
                <div class="prompt">
                    Create A Post
                </div>
                <form class="post-form" method="POST" action="../requests/post-new.php" enctype="multipart/form-data">
                    <textarea type="textarea" id="post-box" class="comment-box" name="postContent" placeholder="What's on your mind, <?php echo ($usersname . "?") ?>" required></textarea>
                    <input type="hidden" name="userID" value=<?php echo ($_SESSION["uid"]); ?>>
                    <input type="button" id="joke-button" value="Get a Joke">
                    <input type="submit" value="Post">
                    <div class="picturebox">
                        <img id="preview-img" src="" alt="Uploaded image">
                        <label id="pic-upload-label" for="pic-upload" class="file-upload">
                            <i class="fa fa-picture-o"></i>Add Image
                        </label>
                        <input id="pic-upload" name="picture" type="file" />
                    </div>
                </form>
            </div>
            <div id="post-area">
                <!-- Posts are loaded dynamically through the loadAllPosts function of home.js -->
            </div>
            <div id="load-message">
            </div>
        </div>
    </main>
    <?php require_once("fragments/footer.php"); ?>
</body>