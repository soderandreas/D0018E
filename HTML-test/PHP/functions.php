<?php

    function establishConnection($host, $dbname, $user, $pass){
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    function checkUserType($conn){
        $usertype = 0;

        session_start();

        if(isset($_SESSION["UserID"])){
            $id = $_SESSION["UserID"];

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

        return $usertype;
    }

?>