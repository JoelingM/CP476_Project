<?php
require_once("../utils/utils-login.php");

// Check if the user is already logged in.
if (isLoggedIn()) {
    header("Location: ../pages/home.php");
}

if (isset($_GET["error"])) {
?>
    <script type="text/javascript">
        alert("<?php echo $_GET["error"]; ?>");
    </script>
<?php
}

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
    <link href="../styles/login.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>

<body>
    <?php require_once("fragments/header.php"); ?>
    <main>
        <div id="loginbox">
            <form method="POST" action="../requests/post-signup.php">
                <label class="prompt-label">Signup</label>
                <div>
                    <input id="fname" type="text" name="fname" placeholder="First Name" require_onced><br>
                    <input id="lname" type="text" name="lname" placeholder="Last Name" require_onced><br>
                    <input id="email" type="email" name="email" placeholder="Email" require_onced><br>
                    <input id="password" type="password" name="password" placeholder="Password" require_onced><br>
                    <input id="password2" type="password" name="password2" placeholder="Confirm Password" require_onced><br>
                    <a id="login-link" href="login.php">Have an account?</a>
                    <input type="submit" value="Sign Up">
                </div>
            </form>
        </div>
    </main>
    <?php require_once("fragments/footer.php"); ?>
</body>