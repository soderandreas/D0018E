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

    if(($num > 0 && $num < 11) || $num == null){
        if($num == null){
            $num = 1;
        }
        if(($num+$data['NumOfProd']) > 10){
            $num = 10 - $data['NumOfProd'];
        }
    } else {
        echo "error";
        exit();
    }

    $sql_cart = "INSERT INTO ShoppingBasket(UserID, AssetID) VALUES (:uid, :aid)";

    $result = $conn->prepare($sql_cart);

    $result->bindValue(':uid', $userID, PDO::PARAM_STR);
    $result->bindValue(':aid', $assetID, PDO::PARAM_STR);

    for($i = 0; $i < $num; $i++){
        $result->execute();
    }

    echo "done";
?>