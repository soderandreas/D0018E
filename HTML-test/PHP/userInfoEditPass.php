<?php
    include "secret.php";
    include "functions.php";

    $conn = establishConnection($host, $dbname, $user, $pass);

    session_start();

    if(!isset($_SESSION["UserID"])){
        header("Location: ../index.php?err=5");
        exit();
    }
?>

<html>
    <head>
        <meta charset="utf-8">
        <title> Change password </title>
	    <link type="text/css" rel="Stylesheet" href="../style.css" />
        <link rel='stylesheet' type='text/css' href='../products.css'>
        <script src=”https://code.jquery.com/jquery-3.6.0.min.js” integrity=”sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=” crossorigin=”anonymous”></script>
        <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js'></script>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
        <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css' integrity='sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T' crossorigin='anonymous'>
        <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js' integrity='sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM' crossorigin='anonymous'></script>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js' integrity='sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1' crossorigin='anonymous'></script>
        <link href='https://raw.githubusercontent.com/daneden/animate.css/master/animate.css' rel='stylesheet'>
    </head>
    <body>
        <?php
            echo getHeader(5);
        ?>

        <div>
            <form action="userInfoEditPassBack.php" method="POST">
                <label>Old Password: </label>
                <input type="password" placeholder="Enter Old Password" name="oldPassword" id="oldpsw" autocomplete="new-password" required><br>
                <label>New Password: </label>
                <input type="password" placeholder="Enter New Password" name="newPassword" id="newpsw" autocomplete="new-password" required><br>
                <label>Repeat New Password: </label>
                <input type="password" placeholder="Enter New Password Again" name="newPasswordAgain" id="oldpswAgain" autocomplete="new-password" required><br>
                <button type="submit" href="userInfoEditPassBack.php">Submit</button>
            </form>
        </div>
    </body>
</html>