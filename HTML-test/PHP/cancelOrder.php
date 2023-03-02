<?php
    include "secret.php";
    include "functions.php";

    $conn = establishConnection($host, $dbname, $user, $pass);

    session_start();

    if(!isset($_SESSION["UserID"])){ // if not logged in
        header("Location: ../index.php");
        exit();
    } else {
        $id = $_SESSION["UserID"];
    }

    $orderID = $_GET["order"];

    // Make sure user making the request is the user that made the order
    $sql_userCheck = "SELECT UserID FROM Orders WHERE ID = :id";
    $result = $conn->prepare($sql_userCheck);
    $result->bindValue(':id', $orderID, PDO::PARAM_STR);
    $result->execute();
    $data = $result->fetch(PDO::FETCH_ASSOC);

    if($data['UserID'] == $_SESSION["UserID"]){
        // Add items back to stock
        $sql_numOfStock = "SELECT COUNT(*) AS NumOfProd, AssetID FROM OrderProducts WHERE OrderID = :oid GROUP BY AssetID ORDER BY NumOfProd";
        $sql_addToStock = "UPDATE Assets SET Stock = Stock + 1 WHERE ID = :aid";

        $result = $conn->prepare($sql_numOfStock);
        $result->bindValue(':oid', $orderID, PDO::PARAM_STR);
        $result->execute();

        while($data = $result->fetch(PDO::FETCH_ASSOC)){
            echo "test";
            for($i = 0; $i < $data['NumOfProd']; $i++){
                $result2 = $conn->prepare($sql_addToStock);
                $result2->bindValue(':aid', $data['AssetID'], PDO::PARAM_STR);
                $result2->execute();
            }
        }

        // Remove order from OrderProducts and Orders
        $sql_deleteOP = "DELETE FROM OrderProducts WHERE OrderID = :aid";
        $sql_deleteOrder = "DELETE FROM Orders WHERE ID = :id";

        $result = $conn->prepare($sql_deleteOP);
        $result->bindValue(':aid', $orderID, PDO::PARAM_STR);
        $result->execute();
        
        $result2 = $conn->prepare($sql_deleteOrder);
        $result2->bindValue(':id', $orderID, PDO::PARAM_STR);
        $result2->execute();

        header("Location: ../index.php");
    } else {
        header("Location: ../index.php?err=4");
    }
?>