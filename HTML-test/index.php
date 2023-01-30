<?php
    include "PHP/secret.php";
    include "PHP/version.php";

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
    $loggedIn = false;

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
?>

<html>
    <head>
        <title>
            <?php
                if($loggedIn){
                    echo "logged in as $username";
                } else {
                    echo "not logged in";
                }
            ?>
        </title>
        <meta charset="UTF-8" />
        <link type="text/css" rel="Stylesheet" href="style.css" />
        <script type="text/Javascript" src="javaScript.js"></script>
    </head>

    <body>
        <?php
            if(!$loggedIn){
                echo $defaultHomePage;
            } else {
                echo $loggedInHomePage;
            }
        ?>
    </body>
</html>