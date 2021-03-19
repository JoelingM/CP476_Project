<nav id="sidenav">
    <a id="home-button" class="sidenav-option" href="home.php"><i class="fa fa-home" aria-hidden="true"></i>Home</a>
    <a class="sidenav-option" href="search-page.php"><i class="fa fa-users" aria-hidden="true"></i>Search</a>
    <a class="sidenav-option" href="profile.php?uid=<?php echo $_SESSION["uid"]; ?>"><i class="fa fa-user" aria-hidden="true"></i>My Account</a>
    <a class="sidenav-option" href="settings.php"><i class="fa fa-cog" aria-hidden="true"></i>Settings</a>
    <a class="sidenav-option" href="../requests/logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a>
</nav>