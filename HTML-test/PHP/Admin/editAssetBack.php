<?php
    include "../secret.php";
    include "../functions.php";

    $conn = establishConnection($host, $dbname, $user, $pass);

    $usertype = checkUserType($conn);

    session_start();
    $id = $_SESSION["UserID"];

    $assetID = $_GET["asset"];

    if($usertype == 2 || $usertype == 3){ // If user is an admin
        if(isset($_POST['title']) && isset($_POST['description']) && isset($_POST['price']) && isset($_POST['stock']) && $_POST['price'] > 0 && $_POST['stock'] >= 0){
            if(strlen($_POST['description']) < 1 || strlen($_POST['title']) < 1){
                header("Location: editAsset.php?asset=".$assetID."&err=2");
                exit();
            } else if(strlen($_POST['description']) > 1000 || strlen($_POST['title']) > 60){
                header("Location: editAsset.php?asset=".$assetID."&err=3");
                exit();
            }

            $name = $_POST['title'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $stock = $_POST['stock'];

            $name = filter_var($name, FILTER_SANITIZE_STRING);
            $description = filter_var($description, FILTER_SANITIZE_STRING);

            $sql_update = "UPDATE Assets SET Name = :n, Description = :d, Price = :p, Stock = :s WHERE ID = :id";

            $result = $conn->prepare($sql_update);

            $result->bindValue(':n', $name, PDO::PARAM_STR);
            $result->bindValue(':d', $description, PDO::PARAM_STR);
            $result->bindValue(':p', $price, PDO::PARAM_STR);
            $result->bindValue(':s', $stock, PDO::PARAM_STR);
            $result->bindValue(':id', $assetID, PDO::PARAM_STR);

            $result->execute();
            header("Location: ../product.php?asset=".$assetID."");
        } else {
            header("Location: editAsset.php?asset=".$assetID."&err=1");
        }
    } else {
        header("Location: ../../index.php");
        exit();
    }
?>