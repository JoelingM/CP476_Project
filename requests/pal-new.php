<?php
    require_once("../utils/utils-profile.php");

    if (isset($_GET["uid"]) && !empty($_GET["uid"])){
        addPal($_GET["uid"]);
        header("Location: ../pages/profile.php?uid=".$_GET["uid"]);
    } else {
        header("Location: ../pages/home.php?error=The profile couldn't be loaded");
    }
