<?php
    include "secret.php";
    include "functions.php";

    $conn = establishConnection($host, $dbname, $user, $pass);

    $assetID = $_GET["asset"];

    session_start();

    $userID = $_SESSION["UserID"];

    $sql_cart = "INSERT INTO ShoppingBasket(UserID, AssetID) VALUES (:uid, :aid)";

    $result = $conn->prepare($sql_cart);

    $result->bindValue(':uid', $userID, PDO::PARAM_STR);
    $result->bindValue(':aid', $assetID, PDO::PARAM_STR);

    $result->execute();

    echo "done";
?>