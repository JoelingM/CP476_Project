<?php
require_once("../utils/utils-profile.php");

if (isset($_GET["uid"]) && !empty($_GET["uid"])) {
    removePal($_GET["uid"]);
    header("Location: ../pages/profile.php?uid=" . $_GET["uid"]);
} else {
    header("Location: ../pages/home.php");
}
