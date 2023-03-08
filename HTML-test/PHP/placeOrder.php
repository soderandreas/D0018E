<?php
    include "secret.php";
    include "functions.php";

    $conn = establishConnection($host, $dbname, $user, $pass);

    // Get user ID
    session_start();
    $userID = $_SESSION['UserID']; 

    // Get current basket
    $sql_basket = "SELECT DISTINCT COUNT(*) AS NumOfProd, AssetID, Price FROM ShoppingBasket INNER JOIN Assets ON ShoppingBasket.AssetID = Assets.ID WHERE UserID = :id GROUP BY AssetID ORDER BY NumOfProd";

    $result = $conn->prepare($sql_basket);

    $result->bindValue(':id', $userID, PDO::PARAM_STR);

    $result->execute();
    $data = $result->fetch(PDO::FETCH_ASSOC);

    if($data['NumOfProd'] == null){ // if basket empty
        header("Location: ../index.php?err=4");
        exit();
    }

    // Check if all asset are in stock

    $sql_stock = "SELECT Stock FROM Assets WHERE ID = :id";

    $result2 = $conn->prepare($sql_stock);

    while($data = $result->fetch(PDO::FETCH_ASSOC)){
        $result2->bindValue(':id', $data['AssetID'], PDO::PARAM_STR);
        $result2->execute();
        $data2 = $result2->fetch(PDO::FETCH_ASSOC);
        if(($data2['Stock'] - $data['NumOfProd']) < 0 && $data['NumOfProd'] != null){
            header("Location: ../index.php?err=3");
            exit();
        }
    }

    // Create new input into Orders

    $conn->beginTransaction();

    $sql_order = "INSERT INTO Orders(UserID) VALUES (:uid)";

    $result3 = $conn->prepare($sql_order);

    $result3->bindValue(':uid', $userID, PDO::PARAM_STR);

    $result3->execute();

    // Get current number of orderIDs
    $sql_num = "SELECT ID FROM Orders WHERE UserID = :uid ORDER BY ID DESC LIMIT 1";

    $result4 = $conn->prepare($sql_num);

    $result4->bindValue(':uid', $userID, PDO::PARAM_STR);

    $result4->execute();

    $data_num = $result4->fetch(PDO::FETCH_ASSOC);

    $orderID = $data_num['ID'];

    echo $orderID;

    // Insert into OrderProducts and lower stock

    $sql_products = "INSERT INTO OrderProducts(OrderID, AssetID, Price) Values (:oid, :aid, :p)";
    $sql_stock = "UPDATE Assets SET Stock = Stock - 1 WHERE ID = :id";

    $result5 = $conn->prepare($sql_products);
    $result6 = $conn->prepare($sql_stock);

    $result5->bindValue(':oid', $orderID, PDO::PARAM_STR);

    $result->execute();
    while($data = $result->fetch(PDO::FETCH_ASSOC)){

        for($i = 0; $i < $data['NumOfProd']; $i++){
            $result5->bindValue(':aid', $data['AssetID'], PDO::PARAM_STR);
            $result5->bindValue(':p', $data['Price'], PDO::PARAM_STR);
            $result5->execute();
            $result6->bindValue(':id', $data['AssetID'], PDO::PARAM_STR);
            $result6->execute();
        }
    }

    // Clear users shopping cart
    $sql_clear = "DELETE FROM ShoppingBasket WHERE UserID = :uid";

    $result6 = $conn->prepare($sql_clear);

    $result6->bindValue(':uid', $userID, PDO::PARAM_STR);

    $result6->execute();

    $conn->commit();

    // Send user to correct order page
    header("Location: order.php?oid=" . $orderID);
?>