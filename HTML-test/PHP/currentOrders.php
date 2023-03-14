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

    $output = "";

    $sql_orders = "SELECT DISTINCT OrderID, 
    CONVERT_TZ(OrderTime, '+00:00', CONCAT('+', CAST(DATE_FORMAT(SEC_TO_TIME(FLOOR(((SYSDATE() - UTC_TIMESTAMP())/10000)*3600)), '%H:%i') AS CHAR))) AS TimeOfOrder,
    SUM(OrderProducts.Price) AS TotalPrice, Status FROM OrderProducts INNER JOIN Orders ON OrderProducts.OrderID = Orders.ID INNER JOIN Assets ON OrderProducts.AssetID = Assets.ID WHERE Orders.UserID = :id GROUP BY OrderID ORDER BY OrderID";

    $result = $conn->prepare($sql_orders);

    $result->bindValue(':id', $id, PDO::PARAM_STR);

    $result->execute();

    $usertype = checkUserType($conn);

    while($data = $result->fetch(PDO::FETCH_ASSOC)){

        if($data['Status'] != 1){
            $cancel = "<a href=cancelOrder.php?order=".$data['OrderID']."><h3>Cancel Order</h3></a><br><br>";
            $edit = "<a href=editOrder.php?order=".$data['OrderID']."><h3>Edit Order</h3></a><br><br>";
            $status = "<h4 style='color: #a2a200;'> Status: In Progess</h4>";
        } else {
            $cancel = "";
            $edit = "";
            $status = "<h4 style='color: #14a200;'> Status: Sent</h4>";
        }

        $output .= "<div class='order-container'>
                        <a href='order.php?oid=".$data['OrderID']."'>
                            <div class='order_info'>
                                <h2> Order: ".$data['OrderID']." </h2>
                                <h3> Total price: $".$data['TotalPrice']." </h3> <br>
                                <h3> Time: ".$data['TimeOfOrder']." </h3> <br>
                                ".$status."
                            </div>
                        </a>
                        <div class='order_cancel'>
                            ". $edit ."
                            ". $cancel ."
                        </div>
                        
                    </div>";

        if($_GET['succ']){
            $notification = notification("Your order has been canceled!", 1);
        }
    }

?>

<html>
    <head>
        <meta charset="utf-8">
        <title>Current Orders</title>
	    <link rel="stylesheet" type="text/css" href="../style.css">
        <script type="text/Javascript" src="../javaScript.js"></script>
        <link rel='stylesheet' type='text/css' href='../products.css'>
        <script type='text/javascript' src='products.js'></script>
        <script src=”https://code.jquery.com/jquery-3.6.0.min.js” integrity=”sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=” crossorigin=”anonymous”></script>
        <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js'></script>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
        <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css' integrity='sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T' crossorigin='anonymous'>
        <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js' integrity='sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM' crossorigin='anonymous'></script>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js' integrity='sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1' crossorigin='anonymous'></script>
        <link href='https://raw.githubusercontent.com/daneden/animate.css/master/animate.css' rel='stylesheet'>
    </head>
    <body>
        <?php
            echo getHeader(3);
            echo $notification;
        ?>
        <h1>All your orders:</h1>
        <hr>
        <?php
            if($output == ""){
                echo "You currently have no orders";
            } else {
                echo $output;
            }
        ?>
        <hr>
        <?php
            if($usertype == 3){
                $sql_otherOrders = "SELECT DISTINCT Orders.UserID, Username,
                CONVERT_TZ(OrderTime, '+00:00', CONCAT('+', CAST(DATE_FORMAT(SEC_TO_TIME(FLOOR(((SYSDATE() - UTC_TIMESTAMP())/10000)*3600)), '%H:%i') AS CHAR))) AS TimeOfOrder,
                OrderID, Status, SUM(OrderProducts.Price) AS TotalPrice FROM OrderProducts INNER JOIN Orders ON OrderProducts.OrderID = Orders.ID INNER JOIN Assets ON OrderProducts.AssetID = Assets.ID INNER JOIN Users ON Orders.UserID = Users.ID WHERE Orders.UserID != :id GROUP BY OrderID ORDER BY OrderID";

                $result = $conn->prepare($sql_otherOrders);

                $result->bindValue(':id', $id, PDO::PARAM_STR);

                $result->execute();
                
                echo "<h1>Other users orders</h1>";

                while($data = $result->fetch(PDO::FETCH_ASSOC)){
                    if($data['Status'] != 1){
                        $cancel = "<a href=cancelOrder.php?order=".$data['OrderID']."><h3>Cancel Order</h3></a><br><br>";
                        $edit = "<a href=editOrder.php?order=".$data['OrderID']."><h3>Edit Order</h3></a><br><br>";
                        $status = "<h4 style='color: #a2a200;'> Status: In Progess</h4>";
                    } else {
                        $cancel = "";
                        $edit = "";
                        $status = "<h4 style='color: #14a200;'> Status: Sent</h4>";
                    }

                    echo "
                    <div class='order-container'>
                        <a href='order.php?oid=".$data['OrderID']."'>
                            <div class='order_info'>
                                <h2> Order: ".$data['OrderID']." </h2>
                                <h3> Total price: $".$data['TotalPrice']." </h3> <br>
                                <h3> Time: ".$data['TimeOfOrder']." </h3> <br>
                                <h4> User: ".$data['Username']."</h4>
                                ".$status."
                            </div>
                        </a>
                        <div class='order_cancel'>
                            ".$edit."
                            ".$cancel."
                        </div>
                    </div>";
                }

                echo "<h1>Other users shopping basket</h1>";

                $sql_otherBasket = "SELECT DISTINCT SUM(Price) AS PriceTotal, Username, Users.ID FROM ShoppingBasket INNER JOIN Users ON Users.ID = UserID INNER JOIN Assets ON Assets.ID = AssetID WHERE UserID != :uid GROUP BY Users.ID";

                $result = $conn->prepare($sql_otherBasket);

                $result->bindValue(':uid', $id, PDO::PARAM_STR);

                $result->execute();

                while($data = $result->fetch(PDO::FETCH_ASSOC)){
                    echo "
                    <div class='order-container'>
                        <a href='Admin/otherBasket.php?user=".$data['ID']."'>
                            <div class='order_info'>
                                <h2> User: ".$data['Username']." </h2>
                                <h3> Total price: $".$data['PriceTotal']." </h3> <br>
                            </div>
                        </a>
                    </div>";
                }
            }
        ?>
    </body>
</html>