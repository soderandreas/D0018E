<?php
    include "secret.php";
    $conn;
	$mess = "ok";
	try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        #echo "Connected successfully";
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }

    $username = "";
    session_start();

    if(isset($_SESSION["UserID"])){
        $loggedIn = true;
        $id = $_SESSION["UserID"];
        
        $sql = "SELECT Username FROM Users WHERE ID = :i LIMIT 1";
		$result = $conn->prepare($sql);
		
		$result->bindValue(':i', $id, PDO::PARAM_STR);
		$result->execute();
		
		$name = "";

        while($data = $result->fetch(PDO::FETCH_ASSOC)){//Tar ut användarens namn, lösenord och id från talbellen. Kollar även om användaren är admin
			$name = $data['Username'];
		}
		$username = $name;
    }

    $defaultHomePage = '
        <div class="loginbox">
            <h1>Sign in</h1>
            <form action="PHP/login.php" method="POST">
                <p>Username</p>
                <input type="text" name="username" placeholder="Enter Username"></input>
                <p>Password</p>
                <input type="password" name="password" placeholder="********"></input>
                <input type="submit" href="PHP/login.php" value="Login"></input>
                <br/> 
                <a href="PHP/createAcc.php">Create a new account</a>
            </form>
        </div>';

    $loggedInHomePage = '<h1>You are logged in as '.$username.' </h1>
    <a href="PHP/Admin/handleUsers.php"> See all users </a><br>
    <a href="PHP/logout.php"> Log out </a>';
?>