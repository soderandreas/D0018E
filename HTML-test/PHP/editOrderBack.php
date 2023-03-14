<?php
    include "secret.php";
    include "functions.php";

    $conn = establishConnection($host, $dbname, $user, $pass);

    session_start();

    $userID = $_SESSION["UserID"];

    $orderID = $_GET['order'];

    $sql_status = "SELECT Status FROM Orders WHERE ID = :oid";

    $result = $conn->prepare($sql_status);

    $result->bindValue(':oid', $orderID, PDO::PARAM_STR);

    $result->execute();

    $data = $result->fetch(PDO::FETCH_ASSOC);

    if($data['Status'] == 1){
        header("Location: editOrder.php?order=".$orderID."&err=3");
        exit();
    }

    $sql_AssetIDs = "SELECT DISTINCT AssetID FROM OrderProducts WHERE OrderID = :oid ORDER BY AssetID";
    $sql_pricesInOrder = "SELECT DISTINCT Price, AssetID FROM OrderProducts WHERE OrderID = :oid AND AssetID = :aid ORDER BY AssetID";
    $sql_currentStock = "SELECT Stock, COUNT(*) AS PrevAmount FROM Assets INNER JOIN OrderProducts ON AssetID = Assets.ID WHERE AssetID = :aid AND OrderID = :oid";
    $sql_prevOrderAmount = "SELECT COUNT(*) AS PrevAmount FROM OrderProducts WHERE OrderID = :oid AND AssetID = :aid";

    $result = $conn->prepare($sql_AssetIDs);

    $result->bindValue(':oid', $orderID, PDO::PARAM_STR);

    $result->execute();

    $i = 0;

    while($data = $result->fetch(PDO::FETCH_ASSOC)){
        $result2 = $conn->prepare($sql_pricesInOrder);
        $result2->bindValue(':oid', $orderID, PDO::PARAM_STR);
        $result2->bindValue(':aid', $data['AssetID'], PDO::PARAM_STR);

        $result2->execute();
        $sameAssetAmount = 0;
        $lastID = null;

        while($data2 = $result2->fetch(PDO::FETCH_ASSOC)){
            //echo $_POST["id" . $data['AssetID'] . "p" . $data2['Price']];
            $amountOfProd[$i] += $_POST["id" . $data['AssetID'] . "p" . $data2['Price']];
            $result3 = $conn->prepare($sql_currentStock);
            $result3->bindValue(':aid', $data['AssetID'], PDO::PARAM_STR);
            $result3->bindValue(':oid', $orderID, PDO::PARAM_STR);
            $result3->execute();
            $data3 = $result3->fetch(PDO::FETCH_ASSOC);

            if($amountOfProd[$i] > 10){
                header("Location: editOrder.php?order=".$orderID."&err=4&amount=".$sameAssetAmount."");
                exit();
            }

            if(($data3['Stock'] - ($amountOfProd[$i] - $data3['PrevAmount']) < 0)){
                header("Location: editOrder.php?order=".$orderID."&err=2");
                exit();
            }
            $lastID = $data['AssetID'];
        }

        echo "amount: ", $amountOfProd[$i];

        $i++;

        if($data['AssetID'] != null){
            echo " test ", $data['AssetID'], " ";
            $assetIDs[] = $data['AssetID'];
        } else {
            header("Location: editOrder.php?order=".$orderID."&err=1");
            exit();
        }
    }

    $sql_currentOrder = "SELECT COUNT(*) AS NumOfProd, Assets.ID, Assets.Price FROM OrderProducts INNER JOIN Assets ON Assets.ID = AssetID WHERE OrderID = :oid GROUP BY Assets.Name ORDER BY AssetID";
    $sql_insert = "INSERT INTO OrderProducts(OrderID, AssetID, Price) VALUES (:oid, :aid, :p)";
    $sql_assetDec = "UPDATE Assets SET Stock = Stock-1 WHERE ID = :aid";
    $sql_delete = "DELETE FROM OrderProducts WHERE OrderID = :oid AND AssetID = :aid ORDER BY Price DESC LIMIT 1";
    $sql_assetInc = "UPDATE Assets SET Stock = Stock+1 WHERE ID = :aid";


    $result = $conn->prepare($sql_currentOrder);

    $result->bindValue(':oid', $orderID, PDO::PARAM_STR);

    $result->execute();

    $i = 0;

    while($data = $result->fetch(PDO::FETCH_ASSOC)){
        if($data['NumOfProd'] < $amountOfProd[$i]){
            for($i2 = 0; $i2 < ($amountOfProd[$i]-$data['NumOfProd']); $i2++){
                echo $data['NumOfProd'], " ", $assetIDs[$i], " ", $amountOfProd[$i], " ";
                echo "ID: ", $data['ID'], " Price: ", $data['Price'], " ";
                $result2 = $conn->prepare($sql_insert);

                $result2->bindValue(':oid', $orderID, PDO::PARAM_STR);
                $result2->bindValue(':aid', $data['ID'], PDO::PARAM_STR);
                $result2->bindValue(':p', $data['Price'], PDO::PARAM_STR);

                $result2->execute();

                $result3 = $conn->prepare($sql_assetDec);
                $result3->bindValue(':aid', $data['ID'], PDO::PARAM_STR);

                $result3->execute();
                echo "test1";
            }
        } else if ($data['NumOfProd'] > $amountOfProd[$i]){
            for($i2 = 0; $i2 < ($data['NumOfProd']-$amountOfProd[$i]); $i2++){
                //echo $data['NumOfProd'], " ", $assetIDs[$i], " ", $amountOfProd[$i]], " ";
                $result2 = $conn->prepare($sql_delete);

                $result2->bindValue(':oid', $orderID, PDO::PARAM_STR);
                $result2->bindValue(':aid', $data['ID'], PDO::PARAM_STR);

                $result2->execute();

                $result3 = $conn->prepare($sql_assetInc);
                $result3->bindValue(':aid', $data['ID'], PDO::PARAM_STR);

                $result3->execute();
                echo "test2";
            }
        }
        $i++;
    }
    header("Location: editOrder.php?order=".$orderID."&succ=1");
?>