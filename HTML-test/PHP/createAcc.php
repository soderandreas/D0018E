<?php

?>

<html>
    <head>
        <title>
            Create a new Account
        </title>
        <meta charset="UTF-8" />
        <link type="text/css" rel="Stylesheet" href="../style.css" />
        <script type="text/Javascript" src="../javaScript.js"></script>
    </head>
    <body>
        <div>
            <form action="createAccBack.php" method="POST">
                <h1>Register</h1>
                <p>Please fill in this form to create an account.</p>
                <hr>

                <label for="username"><b>Username</b></label>
                <input type="text" placeholder="myUsername123" name="username" id="username" required>
                <br>
                <label for="psw"><b>Password</b></label>
                <input type="password" placeholder="********" name="password" id="psw" required>
                <br>
                <label for="psw-repeat"><b>Repeat Password</b></label>
                <input type="password" placeholder="********" name="password-repeat" id="psw-repeat" required>
                <br>
                <label for="email"><b>Email</b></label>
                <input type="email" placeholder="myemailaccount@gmail.com" name="email" id="email" required>
                <br>
                <label for="fName"><b>First Name</b></label>
                <input type="text" placeholder="Andreas" name="fName" id="fName" required>
                <br>
                <label for="lName"><b>Last Name</b></label>
                <input type="text" placeholder="Söderman" name="lName" id="lName" required>
                <br>
                <input type="radio" id="male" name="gender" value="M">
                <label for="male">Male</label>
                <br>
                <input type="radio" id="female" name="gender" value="F">
                <label for="female">Female</label>
                <br>
                <label for="phone"><b>Telephone number</b></label>
                <input type="tel" placeholder="070xxxxxxx" name="phone" id="phone" required>
                <br>
                <label for="age"><b>Age</b></label>
                <input type="number" id="age" name="age">


                <hr>
                <button type="submit" href="createAccBack.php">Create Account</button>
            </form>
        </div>

        <div class="container signin">
            <p>Already have an account? <a href="../index.php">Sign in</a>.</p>
        </div>
    </body>
</html>