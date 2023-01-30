<?php
    /*include "secret.php";
    $conn;
	$mess = "ok";
	try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        #echo "Connected successfully";
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }*/

    session_start();

    if(isset($_SESSION["UserID"])){
        $_SESSION = array();
	    session_destroy();
        header("Location:../index.php");
    } else {
        echo "Not logged in!";
    }

?>