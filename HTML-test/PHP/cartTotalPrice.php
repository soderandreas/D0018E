<?php
    include "secret.php";
    include "functions.php";

    $conn = establishConnection($host, $dbname, $user, $pass);

    session_start();

    $userID = $_SESSION["UserID"];

    $sql_totPrice = "SELECT SUM(Price) AS PriceTotal FROM ShoppingBasket INNER JOIN Assets ON ShoppingBasket.AssetID = Assets.ID WHERE UserID = :id";

    $result = $conn->prepare($sql_totPrice);

    $result->bindValue(':id', $userID, PDO::PARAM_STR);

    $result->execute();

    $data = $result->fetch(PDO::FETCH_ASSOC);

    $totalPrice = $data['PriceTotal'];
?>