<?php
    include "../secret.php";
    $conn;
	$mess = "ok";
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected successfully";
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }

    session_start();
    if(isset($_SESSION["UserID"])){
        $id = $_SESSION["UserID"];
        $adminID = $_GET["id"];

        $sql = "SELECT Type FROM UserType WHERE ID = :i LIMIT 1";

        $result = $conn->prepare($sql);

        $result->bindValue(':i', $id, PDO::PARAM_STR);
		$result->execute();

        $type = "";

        while($data = $result->fetch(PDO::FETCH_ASSOC)){
            $type = $data['Type'];
		}

        $usertype = $type;
    }

    if ($usertype == 3){
        $sql2 = "UPDATE UserType SET Type = 1 WHERE ID = :i";

        $result2 = $conn->prepare($sql2);

        $result2->bindValue(':i', $adminID, PDO::PARAM_STR);
        $result2->execute();

        header("Location: handleUsers.php");
    } else {
        header("Location: ../../index.php");
    }
?>