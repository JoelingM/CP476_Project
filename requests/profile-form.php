<?php
    require_once("../utils/utils-profile.php");
    postReply();
    header("Location: ../pages/profile.php?uid=".$_SESSION["uid"]);
?>