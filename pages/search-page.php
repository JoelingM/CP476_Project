<?php
require_once("../utils/utils-db.php");
require_once("../utils/utils-login.php");

// This page can only be seen when someone is logged in.
forceLogIn();

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
    <link href="../styles/search-page.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <?php require_once("fragments/header.php"); ?>
    <main>
        <?php require_once("fragments/sidenav.php"); ?>
        <div id="content">
            <form id="search" action="search-page.php" method="get">
                <input type="text" name="search-text" id="search-text">
                <button type="submit" id="search-button" class="sidenav-option">
                    <i class="fa fa-search" aria-hidden="true"></i> Search</button>
            </form>

            <div id="results" class="search-result-section" >
                <div id="friend-section">
                    <h2>Pals</h2>
                    <hr>
                    <?php
                    $db = getDB();
                    ?>

                    <div class="user-list">
                        <?php
                        $result = "";
                        $query = "";
                        $uid = $_SESSION["uid"];
                        if (isset($_GET["search-text"]) && !empty($_GET["search-text"])) {
                            $searchStrings = explode(" ", $_GET["search-text"]);
                            $query = "
                                SELECT 
                                    user.user_id, 
                                    user.fname, 
                                    user.lname, 
                                    picture.path
                                FROM 
                                    user
                                    INNER JOIN pal ON user.user_id = pal.pal_id
                                    LEFT OUTER JOIN picture ON user.picture_id = picture.picture_id
                                WHERE 
                                    pal.user_id = ? AND (
                            ";

                            $conditions = [];
                            $args = [$uid];
                            foreach ($searchStrings as $str) {
                                $conditions[] = " fname LIKE CONCAT('%', ?, '%') OR lname LIKE CONCAT('%', ?, '%')";
                                $args[] = $str;
                                $args[] = $str;
                            }
                            $query .= implode(" OR ", $conditions) . ") LIMIT 20;";
                            $query = mysqli_prepare($db, $query);
                            mysqli_stmt_bind_param($query, "d" . str_repeat("s", sizeof($args) - 1), ...$args);
                            $result = execQueryStmt($query);
                        } else {
                            $query = mysqli_prepare($db, "
                                SELECT 
                                    user.user_id, 
                                    user.fname, 
                                    user.lname, 
                                    picture.path
                                FROM 
                                    user
                                    INNER JOIN pal ON user.user_id = pal.pal_id
                                    LEFT OUTER JOIN picture ON user.picture_id = picture.picture_id
                                WHERE 
                                    pal.user_id = ?
                                LIMIT 20;
                            ");
                            mysqli_stmt_bind_param($query, "d", $uid);
                            $result = execQueryStmt($query);
                        }
                        $items = mysqli_fetch_all($result);


                        if (!isset($items) || sizeof($items) === 0) {
                        ?>
                            <span>No results!</span>
                            <?php
                        } else {
                            for ($i = 0; $i < sizeof($items); $i++) {
                            ?>
                                <div class="user-result">
                                    <a href="../pages/profile.php?uid=<?php echo $items[$i][0]; ?>">
                                        <img class="icon" src="../<?php echo $items[$i][3]; ?>" alt="<?php echo $items[$i][1] . " " . $items[$i][2]; ?>">
                                    </a>
                                    <div class="namebox">
                                        <a href="../pages/profile.php?uid=<?php echo $items[$i][0]; ?>">
                                            <span class="nametag"><?php echo $items[$i][1] . " " . $items[$i][2]; ?></span>
                                        </a>
                                    </div>
                                </div>
                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>

                <div id="user-section" class="search-result-section" >
                    <h2>All</h2>
                    <hr>
                    <div class="user-list">
                        <?php
                        $result = "";
                        $query = "";
                        if (isset($_GET["search-text"]) && $_GET["search-text"] !== "") {
                            $searchStrings = explode(" ", $_GET["search-text"]);
                            $query = "
                                SELECT 
                                    user.user_id, 
                                    user.fname, 
                                    user.lname, 
                                    picture.path
                                FROM 
                                    user
                                    LEFT OUTER JOIN picture ON user.picture_id = picture.picture_id
                                WHERE
                            ";

                            $conditions = [];
                            $args = [];
                            foreach ($searchStrings as $str) {
                                $conditions[] = " fname LIKE CONCAT('%', ?, '%') OR lname LIKE CONCAT('%', ?, '%')";
                                $args[] = $str;
                                $args[] = $str;
                            }
                            $query .= implode(" OR ", $conditions) . " LIMIT 20;";
                            $query = mysqli_prepare($db, $query);
                            if (sizeof($conditions) > 0)
                                mysqli_stmt_bind_param($query, str_repeat("s", sizeof($args)), ...$args);
                            $result = execQueryStmt($query);
                        } else {
                            $query = mysqli_prepare($db, "
                                SELECT 
                                    user.user_id, 
                                    user.fname, 
                                    user.lname, 
                                    picture.path
                                FROM 
                                    user
                                    LEFT OUTER JOIN picture ON user.picture_id = picture.picture_id
                                LIMIT 20;
                            ");
                            $result = execQueryStmt($query);
                        }
                        $items = mysqli_fetch_all($result);

                        if (!isset($items) || sizeof($items) === 0) {
                        ?>
                            <span>No results!</span>
                            <?php
                        } else {
                            for ($i = 0; $i < sizeof($items); $i++) {
                            ?>
                                <div class="user-result">
                                    <a href="../pages/profile.php?uid=<?php echo $items[$i][0]; ?>">
                                        <img class="icon" src="../<?php echo $items[$i][3]; ?>" alt="<?php echo $items[$i][1] . " " . $items[$i][2]; ?>">
                                    </a>
                                    <div class="namebox">
                                        <a href="../pages/profile.php?uid=<?php echo $items[$i][0]; ?>">
                                            <span class="nametag"><?php echo $items[$i][1] . " " . $items[$i][2]; ?></span>
                                        </a>
                                    </div>
                                </div>
                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php require_once("fragments/footer.php"); ?>
</body>

</html>