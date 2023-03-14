<?php
    include "functions.php";
    if($_GET['err'] == 1){
        $notification = notification("You must fill all the fields!", 3);
    } else if ($_GET['err'] == 2){
        $notification = notification("Your information (username/password/mail) is already in use", 3);
    }
?>

<!--<html>
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
</html>-->

<html>
<head>
	<meta charset="utf-8">
	<link rel="icon" type="image/png" href="https://cdn.discordapp.com/attachments/424262454915104771/526526923904647180/sambung.png">
	<title>Register</title>
	<link rel="stylesheet" type="text/css" href="../createAcc.css">

</head>
<body>
    <?php echo $notification ?>
	<div class="loginbox">
		<img alt="" src="../WebsitePictures/avatar.png" class="avatar">
		<h1>Register</h1>
		<form action="createAccBack.php" method="POST">
			<div class="logincredentials">
				<div class="left">
					<p>Name</p>
                    <input type="text" placeholder="Enter Name" name="fName" id="fName" minlength="1" maxlength="45" required>
					<p>Age</p>
                    <input type="number" id="age" name="age" min="13" max="120" placeholder="Enter Age" required>
					<p>Phone number</p>
                    <input type="tel" placeholder="070xxxxxxx" name="phone" id="phone" min="4" max="13" required>
					<p>Username</p>
                    <input type="text" placeholder="Enter Username" name="username" id="username" required>
				</div>
				<div class="right">
					<p>Surname</p>
                    <input type="text" placeholder="Enter Surname" name="lName" id="lName" required>
					<p>Gender</p>
					<select name="gender" class="gender-input" required>
						<option value="" selected>Gender</option>
						<option value="M">Male</option>
						<option value="F">Female</option>
					</select>
					<p>Email</p>
                    <input type="Email" placeholder="Enter Email" name="email" id="email" required>
					<p>Password</p>
                    <input type="password" placeholder="Enter Password" name="password" id="psw" required>
				</div>
				<div class="btns">
					<button onclick="button()" type="submit" id="submit" class="button" value="Register">Submit</button>
				    <a href="../index.php" style="margin-left: 30%;">Already have a account?</a>
			    </div>
		    </div>
		</form>
	</div>

</body>
</html>