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

    $error = $_GET["err"];

    $username = "";
    $loggedIn = false;

    session_start();

    if(isset($_SESSION["UserID"]) && $error == null){
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
    } else if ($error == 2){
        echo "You have been banned!";
    }
?>

<html>
    <head>
        <?php
            if($loggedIn){
                $style = "style.css";
            } else {
                $style = "styleLogin.css";
            }
        ?>
        <link rel="stylesheet" href=<?php echo $style ?> />
        <meta charset="UTF-8" />
        <script type="text/Javascript" src="javaScript.js"></script>
        <title>
            <?php
                if($loggedIn){
                    echo "logged in as $username";
                } else {
                    echo "not logged in";
                }
            ?>
        </title>
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