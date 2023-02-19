<?php
    include "secret.php";
    include "functions.php";

    $conn = establishConnection($host, $dbname, $user, $pass);

    // Get user ID
    session_start();
    $userID = $_SESSION['UserID']; 

    // Create new input into Orders
    $sql_order = "INSERT INTO Orders(UserID) VALUES (:uid)";

    $result = $conn->prepare($sql_order);

    $result->bindValue(':uid', $userID, PDO::PARAM_STR);

    $result->execute();

    // Get current number of orderIDs
    $sql_num = "SELECT ID FROM Orders WHERE UserID = :uid ORDER BY ID DESC LIMIT 1";

    $result2 = $conn->prepare($sql_num);

    $result2->bindValue(':uid', $userID, PDO::PARAM_STR);

    $result2->execute();

    $data_num = $result2->fetch(PDO::FETCH_ASSOC);

    $orderID = $data_num['ID'];

    echo $orderID;

    // Get current basket
    $sql_basket = "SELECT AssetID, Price FROM ShoppingBasket INNER JOIN Assets ON ShoppingBasket.AssetID = Assets.ID WHERE UserID = :id";

    $result3 = $conn->prepare($sql_basket);

    $result3->bindValue(':id', $userID, PDO::PARAM_STR);

    $result3->execute();

    // Insert into OrderProducts, decrease stock for product (ADD LATER)
    $sql_products = "INSERT INTO OrderProducts(OrderID, AssetID, Price) Values (:oid, :aid, :p)";

    $result4 = $conn->prepare($sql_products);

    $result4->bindValue(':oid', $orderID, PDO::PARAM_STR);

    while($data = $result3->fetch(PDO::FETCH_ASSOC)){
        $result4->bindValue(':aid', $data['AssetID'], PDO::PARAM_STR);
        $result4->bindValue(':p', $data['Price'], PDO::PARAM_STR);
        $result4->execute();
    }

    // Clear users shopping cart
    $sql_clear = "DELETE FROM ShoppingBasket WHERE UserID = :uid";

    $result5 = $conn->prepare($sql_clear);

    $result5->bindValue(':uid', $userID, PDO::PARAM_STR);

    $result5->execute();

    // Send user to correct order page
    header("Location: order.php?oid=" . $orderID);

    //echo $orderID . " " . $userID;
?>