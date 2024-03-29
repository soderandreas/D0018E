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
    $sortType = $_GET['sort'];

    $username = "";
    $loggedIn = false;

    session_start();

    if(isset($_SESSION["UserID"]) && $error != 2){
        $loggedIn = true;
        $id = $_SESSION["UserID"];
        
        $sql = "SELECT Username FROM Users WHERE ID = :i LIMIT 1";
		$result = $conn->prepare($sql);
		
		$result->bindValue(':i', $id, PDO::PARAM_STR);
		$result->execute();

        $data = $result->fetch(PDO::FETCH_ASSOC);
		$username = $data['Username'];

        if($error == 3){
            echo "You have tried to order more products than there are in stock";
        }

    } else if ($error == 2){
        //echo "You have been banned!";
    }
?>

<html>
    <head>
        <?php
            if($loggedIn){
                $style = "
                <link rel='stylesheet' type='text/css' href='products.css'>
                <script type='text/javascript' src='products.js'></script>
                <script src=”https://code.jquery.com/jquery-3.6.0.min.js” integrity=”sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=” crossorigin=”anonymous”></script>
                <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js'></script>
                <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
                <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css' integrity='sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T' crossorigin='anonymous'>
                <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js' integrity='sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM' crossorigin='anonymous'></script>
                <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js' integrity='sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1' crossorigin='anonymous'></script>
                <link href='https://raw.githubusercontent.com/daneden/animate.css/master/animate.css' rel='stylesheet'>
                    ";
            } else {
                $style = "<link rel='stylesheet' href='styleLogin.css' /> ";
            }
        ?>
        <?php echo $style ?>
        <meta charset="UTF-8" />
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

    <body onload="startValue('<?php echo $sortType ?>')">
        <?php
            if(!$loggedIn){
                echo $notification;
                echo $defaultHomePage;
            } else {
                echo $loggedInHomePage;
            }
        ?>
        <script type="text/Javascript" src="javaScript.js"></script>
    </body>
</html>