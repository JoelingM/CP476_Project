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
            <form method="POST" action="#">
                <label class="prompt-label">Reset Password</label>
                <div>
                    <input id="password-1" type="password" name="password-1" placeholder="Password" required><br>
                    <input id="password-2" type="password" name="password-2" placeholder="Confirm Password" required><br>
                    <input type="submit" value="Reset Password">
                </div>
            </form>
        </div>
    </main>
    </main>
    <?php require_once("fragments/footer.php"); ?>
</body>