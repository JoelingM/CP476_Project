<?php
// Get the profile associated with the logged in user to fill the profile page.
function getProfile($uid)
{
    require_once("utils-db.php");

    // Get the database.
    $db = getDB();

    // Retrieve the user's data.
    $query = mysqli_prepare($db, "
            SELECT
                u.user_id,
                u.fname,
                u.lname,
                p.path AS profile_picture,
                u.birthday,
                u.city,
                u.education,
                u.hobbies 
            FROM
                user AS u
                INNER JOIN picture AS p ON u.picture_id = p.picture_id
            WHERE
                u.user_id = ?
        ;");
    mysqli_stmt_bind_param($query, "d", $uid);

    $result = mysqli_fetch_assoc(execQueryStmt($query));
    if (is_null($result)) {
        header("Location: ../pages/login.php?error=Your account credentials couldn't be found. Please log in again.");
    }

    // Return an assoc array containing this user's data.
    return $result;
}

// Determine whether the current user is pals with a target user.
function isPal($targetUid)
{
    require_once("utils-login.php");
    require_once("utils-db.php");

    // Ensure the user is logged in.
    forceLogin();

    // Get the database.
    $db = getDB();

    // Get the user's id.
    $uid = $_SESSION["uid"];

    // Get any record which shows the current user has the target user as a pal.
    $query = mysqli_prepare($db, "
        SELECT
            pal_id
        FROM
            pal
        WHERE
            user_id = ?
            AND pal_id = ?
        LIMIT 1
        ;
    ");
    mysqli_stmt_bind_param($query, "dd", $uid, $targetUid);

    return !is_null(mysqli_fetch_assoc(execQueryStmt($query)));
}

function addPal($targetUid)
{
    require_once("utils-login.php");
    require_once("utils-db.php");

    // Ensure the user is logged in.
    forceLogin();

    // Get the database.
    $db = getDB();

    // Get the user's id.
    $uid = $_SESSION["uid"];

    // Get any record which shows the current user has the target user as a pal.
    $query = mysqli_prepare($db, "
        INSERT INTO
            pal (user_id, pal_id)
        VALUES (?, ?)
        ;
    ");
    mysqli_stmt_bind_param($query, "dd", $uid, $targetUid);

    try {
        execInsertStmt($query);
    } catch(Exception $e) {
    }
}

function removePal($targetUid)
{
    require_once("utils-login.php");
    require_once("utils-db.php");

    // Ensure the user is logged in.
    forceLogin();

    // Get the database.
    $db = getDB();

    // Get the user's id.
    $uid = $_SESSION["uid"];

    // Get any record which shows the current user has the target user as a pal.
    $query = mysqli_prepare($db, "
        DELETE FROM
            pal
        WHERE
            user_id = ?
            AND pal_id = ?
        ;
    ");
    mysqli_stmt_bind_param($query, "dd", $uid, $targetUid);

    try {
        execQueryStmt($query);
    } catch(Exception $e) {
    }
}

// Get the post IDs for the posts of the target user and their pals.
function getFeed($targetId, $onlyMe, $offset)
{
    require_once("utils-login.php");
    require_once("utils-db.php");

    // Ensure the user is logged in.
    forceLogin();

    // Get the database.
    $db = getDB();

    // Get the user's id.
    $uid = $_SESSION["uid"];

    // Get all posts belonging to this user or a pal of this user.
    $query = mysqli_prepare($db, "
        SELECT
            post.post_id
        FROM
            post
            INNER JOIN user ON post.user_id = user.user_id "
        . ($onlyMe ? "" : "INNER JOIN pal ON pal.pal_id = user.user_id") . "
        WHERE
            user.user_id = ? "
        . ($onlyMe ? "" : "OR pal.pal_id = ?") . "
        ORDER BY
            post.date DESC
        LIMIT ?,20
        ;
    ");
    if ($onlyMe) {
        mysqli_stmt_bind_param($query, "dd", $targetId, $offset);
    } else {
        mysqli_stmt_bind_param($query, "ddd", $targetId, $targetId, $offset);
    }

    // Execute the query and format.
    try {
        $rows = execQueryStmt($query);
        $rawResult = mysqli_fetch_assoc($rows);
        $result = [];
        while ($rawResult) {
            $result[] = $rawResult["post_id"];
            $rawResult = mysqli_fetch_assoc($rows);
        }

        return $result;
    } catch (Exception $e) {
    }
}

function _getPostData($db, $postId)
{
    // Get the post's data.
    $query = mysqli_prepare($db, "
        SELECT
            po.post_id,
            po.date,
            po.content,
            u.user_id,
            u.fname,
            u.lname,
            propi.path AS poster_picture,
            contpi.path AS content_picture,
            r.reply_id
        FROM
            post AS po
            INNER JOIN user AS u ON po.user_id = u.user_id 
            INNER JOIN picture AS propi ON u.picture_id = propi.picture_id
            LEFT OUTER JOIN picture AS contpi ON po.picture_id = contpi.picture_id
            LEFT OUTER JOIN reply AS r ON po.post_id = r.post_id
        WHERE
            po.post_id = ?
        ORDER BY
            r.date ASC    
        ; 
    ");
    mysqli_stmt_bind_param($query, "d", $postId);

    try {
        // Retrieve and format the content and reply ids.
        $rows = execQueryStmt($query);
        $rawResult = mysqli_fetch_assoc($rows);
        $result = $rawResult;
        $result["reply_ids"] = [];
        if (isset($rawResult["reply_id"])) {
            $result["reply_ids"][] = $rawResult["reply_id"];
        }
        unset($result["reply_id"]);

        $rawResult = mysqli_fetch_assoc($rows);
        while ($rawResult) {
            $result["reply_ids"][] = $rawResult["reply_id"];
            $rawResult = mysqli_fetch_assoc($rows);
        }

        return $result;
    } catch (Exception $e) {
    }
}

function outputPost($db, $postId)
{
    $postData = _getPostData($db, $postId);
?>
    <!DOCTYPE html>
    <html lang="en">

    <div class="post">
        <div class="post-wrapper">
            <div class="poster-info">
                <a href="../pages/profile.php?uid=<?php echo $postData["user_id"]; ?>">
                    <img class="icon" src="../../<?php echo $postData["poster_picture"]; ?>" alt="<?php echo $postData["fname"]." ".$postData["lname"]; ?>">
                </a>
                <div class="namebox">
                    <a href="../pages/profile.php?uid=<?php echo $postData["user_id"]; ?>">
                        <span class="nametag"><?php echo $postData["fname"] . " " . $postData["lname"]; ?></span>
                    </a>
                    <span class="timestamp">at <?php echo date_format(date_create($postData["date"]), "g:i a M j, Y"); ?></span>
                </div>
            </div>
            <div class="post-content">
                <?php
                if (isset($postData["content"]) && !empty($postData["content"])) {
                ?>
                    <span class="post-text"><?php echo $postData["content"]; ?></span>
                <?php
                } else if (isset($postData["content_picture"]) && !empty($postData["content_picture"])) {
                ?>
                    <img class="post-img" src="../<?php echo $postData["content_picture"]; ?>" alt="Post image.">
                <?php
                }
                ?>
            </div>
        </div>
        <label class="prompt-label">Replies</label>
        <div class="replies-wrapper">
            <?php
            if (sizeof($postData["reply_ids"]) > 0) {
            ?>
                <div class="post-replies">
                    <?php
                    foreach ($postData["reply_ids"] as $replyId) {
                        // Output the reply.
                        outputReply($db, $replyId);
                    }
                    ?>
                </div>
            <?php
            }
            ?>
            <form class="comment-area" method="POST" action="../requests/profile-form.php?postId=<?php echo $postId; ?>">
                <textarea type="textarea" class="comment-box" name="comment" placeholder="Enter a comment..." required></textarea>
                <input type="submit" value="Post">
            </form>
        </div>
    </div>
<?php
}

function _getReplyData($db, $replyId)
{
    // Get the reply's data.
    $query = mysqli_prepare($db, "
        SELECT
            r.reply_id,
            r.user_id,
            r.date,
            r.content,
            u.user_id,
            u.fname,
            u.lname,
            p.path AS replier_picture
        FROM
           reply AS r
           INNER JOIN user AS u ON r.user_id = u.user_id
           INNER JOIN picture AS p ON u.picture_id = p.picture_id
        WHERE
            r.reply_id = ?
        ;
    ");
    mysqli_stmt_bind_param($query, "d", $replyId);

    try {
        // Retrieve the content.
        return mysqli_fetch_assoc(execQueryStmt($query));
    } catch (Exception $e) {
    }
}

function outputReply($db, $replyId)
{
    $replyData = _getReplyData($db, $replyId);
?>
    <!DOCTYPE html>
    <html lang="en">

    <div class="reply">
        <a href="../pages/profile.php?uid=<?php echo $replyData["user_id"]; ?>">
            <img class="icon" src="../../<?php echo $replyData["replier_picture"]; ?>" alt="<?php echo $replyData["fname"] . " " . $replyData["lname"]; ?>">
        </a>
        <div class="namebox">
            <a href="../pages/profile.php?uid=<?php echo $replyData["user_id"]; ?>">
                <span class="nametag"><?php echo $replyData["fname"] . " " . $replyData["lname"]; ?></span>
            </a>
            <span class="timestamp">at <?php echo date_format(date_create($replyData["date"]), "g:i a M j, Y"); ?></span>
        </div>
        <span class="reply-text"><?php echo ($replyData["content"]); ?></span>
    </div>
<?php
}

function postReply()
{
    require_once("utils-login.php");
    require_once("utils-db.php");

    // Ensure the user is logged in.
    forceLogin();

    // Get the database.
    $db = getDB();

    // Get the user's id.
    $uid = $_SESSION["uid"];

    // Get the GET arguments.
    $postId = $_GET["postId"];

    // Get the POST arguments.
    $comment = $_POST["comment"];

    // Create the query dynamically based on which fields have been updated.
    $query = mysqli_prepare($db, "
            INSERT INTO 
                reply (post_id, user_id, content)
            VALUES
                (?, ?, ?)
            ;
        ");

    // Prepare the statement.
    mysqli_stmt_bind_param($query, "dds", $postId, $uid, $comment);

    try {
        execInsertStmt($query);
    } catch (Exception $e) {
    }
}
?>