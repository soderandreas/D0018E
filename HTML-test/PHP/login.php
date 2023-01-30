<?php
    include "secret.php";

    if(isset($_POST['username']) && isset($_POST['password'])){
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        $name = $_POST['username']; 
        $pass = $_POST['password'];
        #echo $name, $pass;
        
        $sql_query = "SELECT * FROM Users WHERE Username = :name AND Password = :pass";		
        $result = $conn->prepare($sql_query);

        $result->bindValue(':name', $name, PDO::PARAM_STR);
        $result->bindValue(':pass', $pass, PDO::PARAM_STR);
        
        $result->execute();

        $name = "";
        $pass = "";

        while($data = $result->fetch(PDO::FETCH_ASSOC)){
            $name = $data['Username'];
            $pass = $data['Password'];
            $id = $data['ID'];
        }

        session_start();

        if($_POST['username'] == $name && $_POST['password'] == $pass){ // Kollar så lösenord och användarnamn matchar, om de gör det sätts användarnamn och användarens ID för sessionen
            #$_SESSION["username"] = $name;
            $_SESSION['UserID'] = $id;
            #setcookie("userID", $id, time() + (86400 * 30), "/");
        }
        #echo "test4";	

        header("Location: ../index.php");
        exit();

    } else {
        echo "Account not found";
    }
?>