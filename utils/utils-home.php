<?php
require_once("utils-db.php");
session_start();

function loadPosts($limit, $start, $targetId = NULL)
{

    $db = getDB();
    if (!isset($_SESSION["uid"])) {
        header("Location: ../pages/home.php?error=User not logged in.");
        die();
    } else {
        $query = mysqli_prepare(
            $db,
            "SELECT DISTINCT
                p.post_id, 
                p.date, 
                p.user_id, 
                p.content,
                contpi.path AS postPic, 
                u.fname, 
                u.lname,
                propi.path AS profilePic
            FROM 
                post AS p
                INNER JOIN user AS u ON p.user_id = u.user_id 
                INNER JOIN picture AS propi ON u.picture_id = propi.picture_id" .
                (isset($targetId) ? " " : " LEFT OUTER JOIN pal AS pl ON u.user_id = pl.pal_id ") .
                "LEFT OUTER JOIN picture AS contpi ON p.picture_id = contpi.picture_id
            WHERE 
                u.user_id = ?" .
                (isset($targetId) ? " " : " OR pl.user_id = ? ") .
                "ORDER BY
                p.date DESC,
                p.post_id DESC
            LIMIT ?,?
            ;"
        );

        if (isset($targetId)) {
            mysqli_stmt_bind_param($query, "ddd", $targetId, $start, $limit);
        } else {
            mysqli_stmt_bind_param($query, "dddd", $_SESSION["uid"], $_SESSION["uid"], $start, $limit);
        }

        if (!$query) {
            die(mysqli_error($db));
        }

        $results = execQueryStmt($query);

        //Loop through all results and echo them to be appended as data
        while ($row = mysqli_fetch_assoc($results)) {
?>
            <div class="post">
                <div class="post-wrapper">
                    <div class="poster-info">
                        <a href="../pages/profile.php?uid=<?php echo $row["user_id"]; ?>">
                            <img class="icon" src="../<?php echo $row["profilePic"]; ?>" alt="<?php echo $row["fname"] . " " . $row["lname"]; ?>">
                        </a>
                        <div class="namebox">
                            <a href="../pages/profile.php?uid=<?php echo $row["user_id"]; ?>">
                                <span class="nametag"><?php echo $row["fname"] . " " . $row["lname"]; ?></span>
                            </a>
                            <span class="timestamp"><?php echo date_format(date_create($row["date"]), "g:iA Y-m-d"); ?></span>
                        </div>
                        <?php
                        if ($row['user_id'] === $_SESSION["uid"]) {
                        ?>
                            <form class='delete-post-form' method='POST' action='../requests/post-delete.php'>
                                <input type='hidden' name='postID' value="<?php echo $row['post_id']; ?>">
                                <input type='submit' class='post-button delete-button' value='Delete'>
                            </form>
                        <?php
                        }
                        ?>
                    </div>
                    <div class="post-content">
                        <p class="post-text"><?php echo $row['content']; ?></p>
                        <?php
                        if (isset($row['postPic'])) {
                        ?>
                            <img class='post-img' src='../<?php echo $row['postPic']; ?>' alt='Upload'>
                        <?php
                        }
                        ?>
                    </div>
                </div>
                
                <div class="like-box">
                    <?php
                        loadLikes($row["post_id"]);
                    ?>
                </div>
                <label class="replies-label prompt-label">Replies</label>
                <div class="replies-wrapper">
                    <div class="post-replies">
                        <?php
                        //Get all replies
                        getReplies($row['post_id']);

                        ?>
                    </div>
                    <form class="reply-form" method="POST" , action="../requests/post-reply.php">
                        <textarea type="textarea" class="comment-box" name="replyContent" placeholder="Enter a comment..." require_onced></textarea>
                        <input type="hidden" name="postID" value='<?php echo $row['post_id']; ?>'>
                        <input type="hidden" name="userID" value='<?php echo $_SESSION["uid"]; ?>'>
                        <input type="submit" value="Reply">
                    </form>
                </div>
            </div>
        <?php
        }
    }
}

function replyToPost($postID, $content, $userID)
{
    // Prepare the statement.
    $db = getDB();
    $query = mysqli_prepare(
        $db,
        "
        INSERT INTO reply
            (reply_id, post_id, user_id, content) 
        VALUES
            (NULL, ?, ?, ?)
        ;"
    );

    if (!$query) {
        die(mysqli_error($db));
    }

    // Bind the params.
    mysqli_stmt_bind_param($query, "sss", $postID, $userID, $content);

    // Execute the statement.
    if (!execInsertStmt($query)) {
        header("Location: ../pages/home.php?error=A problem occurred during reply. Please report this.");
        die();
    } else {
        getReplies($postID);
    }
}

function getPostLikeData($postID)
{
    $result = ["numLikes" => 0, "iLike" => false];

    // Find the number of users who have liked this post.
    $db = getDB();
    $query = mysqli_prepare(
        $db,
        "
            SELECT 
                COUNT(user_id) AS likes
            FROM 
                postlike
            WHERE 
                post_id = ?
            ;"
    );
    mysqli_stmt_bind_param($query, "d", $postID);
    $row = mysqli_fetch_assoc(execQueryStmt($query));
    if (isset($row)) {
        $result["numLikes"] = $row["likes"];
    }

    // Find whether or not the current user liked this post.
    $db = getDB();
    $query = mysqli_prepare(
        $db,
        "
            SELECT
                user_id
            FROM 
                postlike
            WHERE 
                post_id = ?
                AND user_id = ?
            ;"
    );
    mysqli_stmt_bind_param($query, "dd", $postID, $_SESSION["uid"]);
    $result["iLike"] = mysqli_fetch_assoc(execQueryStmt($query)) !== null;

    return $result;
}

function loadLikes($postID){
    // Get like information on this post.
    $likeData = getPostLikeData($postID);
    ?>
    <form class="like-post-form" method="POST" action="../requests/post-like.php">
        <input type='hidden' name='postID' value="<?php echo $postID;?>">
        <input type='submit' class='post-button like-button' value='<?php echo $likeData["iLike"] ? "Unlike" : "Like"; ?>'>
        <?php
        if ($likeData["numLikes"] > 0) {
        ?>
            <span><?php echo $likeData["numLikes"]; ?> <?php echo $likeData["numLikes"] === 1 ? "person" : "people"; ?> liked this.</span>
        <?php
        } else {
            ?>
            <span>Be the first to like this!</span>
            <?php
        }
        ?>
    </form>
    <?php
}

function getReplies($postID)
{
    $db = getDB();

    $query = mysqli_prepare(
        $db,
        "
            SELECT 
                r.user_id, 
                r.reply_id, 
                r.content, 
                r.date,
                u.fname, 
                u.lname, 
                pi.path 
            FROM 
                reply AS r
                INNER JOIN user AS u ON u.user_id = r.user_id
                INNER JOIN picture AS pi ON u.picture_id = pi.picture_id
            WHERE 
                post_id = ?
            ORDER BY 
                r.date ASC,
                r.reply_id ASC
            ;"
    );

    mysqli_stmt_bind_param($query, "d", $postID);

    if (!$query) {
        die(mysqli_error($db));
    }

    $replies = execQueryStmt($query);


    while ($replyRow = mysqli_fetch_assoc($replies)) {
        ?>
        <div class="reply">
            <a href="../pages/profile.php?uid=<?php echo $replyRow["user_id"]; ?>">
                <img class="icon" src="../<?php echo $replyRow["path"]; ?>" alt="<?php echo $replyRow["fname"] . " " . $replyRow["lname"]; ?>">
            </a>
            <div class="namebox">
                <a href="../pages/profile.php?uid=<?php echo $replyRow["user_id"]; ?>">
                    <span class="nametag"><?php echo $replyRow["fname"] . " " . $replyRow["lname"]; ?></span>
                </a>
                <span class="timestamp"><?php echo date_format(date_create($replyRow["date"]), "g:iA Y-m-d"); ?></span>
            </div>
            <?php
            if ($replyRow['user_id'] == $_SESSION["uid"]) {
            ?>
                <form class='delete-reply-form' method='POST' action='../requests/post-reply-delete.php'>
                    <input type='hidden' name='replyID' value="<?php echo $replyRow['reply_id']; ?>">
                    <input type='submit' class='delete-button' value='Delete'>
                </form>
            <?php
            }
            ?>
            <p class="reply-text"><?php echo $replyRow['content']; ?></p>
        </div>
<?php
    }
}

function makePost($userID, $content, &$picture = NULL)
{
    // Get the database.
    $db = getDB();

    // Upload the picture, if one is set.
    $query = NULL;
    if (isset($picture)) {
        $result = uploadImage($picture);

        // Create the query.
        $query = mysqli_prepare(
            $db,
            "INSERT INTO post
                (user_id, content, picture_id) 
            VALUES
                (?, ?, ?);"
        );
        mysqli_stmt_bind_param($query, "sss", $userID, $content, $result["picture_id"]);
    } else {
        // Create the query.
        $query = mysqli_prepare(
            $db,
            "INSERT INTO post
                (user_id, content) 
            VALUES
                (?, ?);"
        );
        mysqli_stmt_bind_param($query, "ss", $userID, $content);
    }

    // Execute the query.
    if (!execInsertStmt($query)) {
        header("Location: ../pages/home.php?error=A problem occurred during post. Please report this.");
        die();
    }
}

function deleteSpecificReply($replyID)
{
    $db = getDB();

    $query = mysqli_prepare(
        $db,
        "
        DELETE FROM reply
        WHERE
        reply_id = $replyID"
    );

    if (!$query) {
        die(mysqli_error($db));
    }

    // Execute the statement.
    if (!execInsertStmt($query)) {
        header("Location: ../pages/home.php?error=A problem occurred during reply. Please report this.");
        die();
    }
}

function getPostPicture($postID)
{
    $db = getDB();

    // Find the path and picture id of any picture corresponding to this post.
    $query = mysqli_prepare(
        $db,
        "SELECT 
            picture.picture_id,
            picture.path
        FROM 
            picture
            INNER JOIN post ON post.picture_id = picture.picture_id
        WHERE
            post_id = ?
        ;"
    );
    mysqli_stmt_bind_param($query, "d", $postID);
    return mysqli_fetch_assoc(execQueryStmt($query));
}

function deletePicture($pictureId, $path)
{
    $db = getDB();

    // Delete the picture from the database.
    $query = mysqli_prepare(
        $db,
        "DELETE FROM 
                picture
            WHERE
                picture_id = ?
            ;"
    );
    mysqli_stmt_bind_param($query, "d", $pictureId);
    execQueryStmt($query);

    // Delete from the file structure.
    if (file_exists("../" . $path)) {
        unlink("../" . $path);
    }
}

function deleteAllReplies($postID)
{
    $db = getDB();

    $query = mysqli_prepare(
        $db,
        "
        DELETE FROM reply
        WHERE
        post_id = ?;"
    );
    mysqli_stmt_bind_param($query, "d", $postID);

    // Execute the statement.
    execQueryStmt($query);
}

function deleteAllLikes($postID) {
    $db = getDB();

    $query = mysqli_prepare(
        $db,
        "
        DELETE FROM postlike
        WHERE
        post_id = ?;"
    );
    mysqli_stmt_bind_param($query, "d", $postID);

    // Execute the statement.
    execQueryStmt($query);
}

function deletePost($postID)
{
    deleteAllReplies($postID);
    deleteAllLikes($postID);

    $postPicture = getPostPicture($postID);

    $db = getDB();
    $query = mysqli_prepare(
        $db,
        "
        DELETE FROM post
        WHERE
        post_id = ?"
    );
    mysqli_stmt_bind_param($query, "d", $postID);
    execQueryStmt($query);

    if (isset($postPicture)) {
        deletePicture($postPicture["picture_id"], $postPicture["path"]);
    }
}

function likePostToggle($postID)
{
    $db = getDB();
    $query = mysqli_prepare(
        $db,
        "
        SELECT
            user_id
        FROM
            postlike
        WHERE
            post_id = ?
            AND user_id = ?
        ;"
    );
    mysqli_stmt_bind_param($query, "dd", $postID, $_SESSION["uid"]);
    $result = mysqli_fetch_assoc(execQueryStmt($query));

    if (isset($result)) {
        $query = mysqli_prepare(
            $db,
            "
            DELETE FROM
                postlike
            WHERE
                post_id = ?
                AND user_id = ?
            ;"
        );
        mysqli_stmt_bind_param($query, "dd", $postID, $_SESSION["uid"]);
        execQueryStmt($query);
    } else {
        $query = mysqli_prepare(
            $db,
            "
            INSERT INTO postlike(post_id, user_id)
            VALUES (?, ?)
            ;"
        );
        mysqli_stmt_bind_param($query, "dd", $postID, $_SESSION["uid"]);
        execQueryStmt($query);
    }

    loadLikes($postID);
}

function uploadImage(&$file)
{
    require_once("../utils/utils-db.php");

    // Get the database variable.
    $db = getDB();

    // Confirm that the file is an image.
    if (!getimagesize($file["tmp_name"])) {
        header("Location: ../pages/home.php?error=The provided profile picture is not an image file. No changes were made.");
        die();
    }

    // Confirm that the file's type is valid.
    $fileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    if (!in_array($fileType, ["jpg", "png", "jpeg", "bmp"])) {
        header("Location: ../pages/home.php?error=The provided picture is not an accepted file type. Please select a file of type jpg, jpeg, png, or bmp. No changes were made.");
        die();
    }

    // Insert the file into the database to get an ID.
    $query = mysqli_prepare($db, "INSERT INTO picture () VALUES ();");
    try {
        execInsertStmt($query);
    } catch (Exception $e) {
        header("Location: ../pages/home.php?error=The picture couldn't be added to the database. No changes were made.");
        die();
    }
    $pictureId = mysqli_insert_id($db);

    // Move the file to the pictures folder and give it an abitrary and unpredictable but unique name.
    $hash = hash("md2", $pictureId . date("Y M d, Y G: i"));
    $path = "pictures/" . $hash . "." . $fileType;
    while (file_exists($path)) {
        $hash = $hash[0] . (hash("md2", $path . date("Y M d, Y G: i")));
        $path = "pictures/" . $hash . "." . $fileType;
    }
    if (!move_uploaded_file($file["tmp_name"], "../" . $path)) {
        header("Location: ../pages/home.php?error=The picture couldn't be saved to the server. No changes were made.");
        die();
    }

    // Update the path in the database record.
    $query = mysqli_prepare($db, "
        UPDATE
            picture
        SET
            path = ?
        WHERE
            picture_id = ?;
    ");
    mysqli_stmt_bind_param($query, "sd", $path, $pictureId);

    try {
        execInsertStmt($query);

        return [
            "path" => $path,
            "picture_id" => $pictureId
        ];
    } catch (Exception $e) {
        header("Location: ../pages/home.php?error=The profile picture couldn't be registered to the database. No changes were made.");
        die();
    }
}

?>