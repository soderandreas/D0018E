<?php
    include "secret.php";
    include "functions.php";

    $conn = establishConnection($host, $dbname, $user, $pass);

    $assetID = $_GET["asset"];
    $num = $_GET["num"];

    session_start();
    if($num < 11){ // number will only be changed if 
        $userID = $_SESSION["UserID"];
        
        $sql_numOfProd = "SELECT COUNT(*) AS NumOfProd FROM ShoppingBasket WHERE UserID = :uid AND AssetID = :aid";

        $result = $conn->prepare($sql_numOfProd);

        $result->bindValue(':uid', $userID, PDO::PARAM_STR);
        $result->bindValue(':aid', $assetID, PDO::PARAM_STR);

        $result->execute();

        $data = $result->fetch(PDO::FETCH_ASSOC);

        if($data['NumOfProd'] < $num){
            $loops = $num - $data['NumOfProd'];

            $sql_addToCart = "INSERT INTO ShoppingBasket(UserID, AssetID) VALUES (:uid, :aid)";
            $result = $conn->prepare($sql_addToCart);
        } else if ($data['NumOfProd'] > $num){
            $loops = $data['NumOfProd'] - $num;

            $sql_removeFromCart = "DELETE FROM ShoppingBasket WHERE UserID = :uid AND AssetID = :aid LIMIT 1";
            $result = $conn->prepare($sql_removeFromCart);
        } else {
            exit();
        }

        $result->bindValue(':uid', $userID, PDO::PARAM_STR);
        $result->bindValue(':aid', $assetID, PDO::PARAM_STR);

        for($i = 0; $i < $loops; $i++){
            $result->execute();
        }
    }

    echo $assetID, $num;
?>