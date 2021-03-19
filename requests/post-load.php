<?php
require_once("../utils/utils-home.php");

if (!isset($_POST["limit"], $_POST["start"])) {
?>
    <script type="text/javascript">
        alert("Error loading posts.");
    </script>
<?php
    die();
} else {
    if (isset($_POST["uid"])) {
        loadPosts($_POST["limit"], $_POST["start"], $_POST["uid"]);
    } else {
        loadPosts($_POST["limit"], $_POST["start"]);
    }
}

?>