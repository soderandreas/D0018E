<?php
    include "secret.php";
    include "functions.php";

    $conn = establishConnection($host, $dbname, $user, $pass);

    $assetID = $_GET["asset"];

    $num = $_GET["num"];

    session_start();

    $userID = $_SESSION["UserID"];

    $sql_numOfProd = "SELECT COUNT(*) AS NumOfProd FROM ShoppingBasket WHERE UserID = :uid AND AssetID = :aid";

    $result = $conn->prepare($sql_numOfProd);

    $result->bindValue(':uid', $userID, PDO::PARAM_STR);
    $result->bindValue(':aid', $assetID, PDO::PARAM_STR);

    $result->execute();

    $data = $result->fetch(PDO::FETCH_ASSOC);

    if($num != null){
        $currNum = $data['NumOfProd'] + $num;
    } else {
        $currNum = $data['NumOfProd'];
        $num = 1;
    }

    if($currNum < 10){
        $sql_cart = "INSERT INTO ShoppingBasket(UserID, AssetID) VALUES (:uid, :aid)";

        $result = $conn->prepare($sql_cart);

        $result->bindValue(':uid', $userID, PDO::PARAM_STR);
        $result->bindValue(':aid', $assetID, PDO::PARAM_STR);

        for($i = 0; $i < $num; $i++){
            $result->execute();
        }
    }

    echo "done";
?>