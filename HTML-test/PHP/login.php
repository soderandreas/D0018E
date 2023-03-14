<?php
    include "secret.php";
    include "functions.php";

    if($_POST['username'] != null && $_POST['password'] != null){

        $conn = establishConnection($host, $dbname, $user, $pass);
        
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
        $banned = false;

        while($data = $result->fetch(PDO::FETCH_ASSOC)){
            $name = $data['Username'];
            $pass = $data['Password'];
            $id = $data['ID'];
        }

        $sql_query2 = "SELECT CustomerID FROM BanUser";
        $result2 = $conn->prepare($sql_query2);
        $result2->execute();

        while($data2 = $result2->fetch(PDO::FETCH_ASSOC)){
            if($data2['CustomerID'] == $id){
                $banned = true;
            }
        }

        session_start();

        if($_POST['username'] == $name && $_POST['password'] == $pass && $banned == false){ 
            #$_SESSION["username"] = $name;
            $_SESSION['UserID'] = $id;
            header("Location: ../index.php");
        } else if ($banned == false) {
            header("Location: ../index.php?err=3");
        } else if ($banned == true) {
            header("Location: ../index.php?err=2");
        }
        #echo "test4";	

        
        exit();

    } else {
        header("Location: ../index.php?err=1");
    }
?>