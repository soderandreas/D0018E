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

    $defaultHomePage = 
    '<form action="PHP/login.php" method="POST">
        <h1>username</h1>
        <input type="text" name="username" placeholder="Andreas"></input>
        <h1>password</h1>
        <input type="password" name="password" placeholder="********"></input>
        <button type="submit" href="PHP/login.php">Login</button>
    </form>
    <a href="PHP/createAcc.php">Create a new account</a>
    ';

    $loggedInHomePage = '<h1>You are logged in as '.$username.' </h1>
    <a href="PHP/logout.php"> Log out </a>';
?>