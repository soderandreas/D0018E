<?php
    function getOrderInfo($conn){
        $sql_getOrder = "SELECT DISTINCT COUNT(*) AS Num, Name, OrderProducts.Price AS OriginalPrice, Assets.Price AS CurrentPrice, Stock, AssetID FROM OrderProducts INNER JOIN Orders ON Orders.ID = OrderID INNER JOIN Assets ON Assets.ID = AssetID WHERE Orders.ID = :oid GROUP BY Name, OrderProducts.Price ORDER BY AssetID";
        $sql_getImage = "SELECT PictureName FROM AssetPictures WHERE AssetID = :aid LIMIT 1";

        $orderID = $_GET['oid'];

        $result = $conn->prepare($sql_getOrder);

        $result->bindValue(':oid', $orderID, PDO::PARAM_STR);

        $result->execute();

        while($data = $result->fetch(PDO::FETCH_ASSOC)){
            $result2 = $conn->prepare($sql_getImage);

            $result2->bindValue(':aid', $data['AssetID'], PDO::PARAM_STR);

            $result2->execute();

            $data2 = $result2->fetch(PDO::FETCH_ASSOC);

            $output .= "
            <tr id=".$data['AssetID'].">
                <td class='col-sm-8 col-md-6'>
                    <div class='media'>
                        <a class='thumbnail pull-left' href='product.php?asset=".$data['AssetID']."'> <img class='media-object' src='../AssetPictures/".$data2['PictureName']."' style='width: 72px; height: 72px;'> </a>
                        <div class='media-body'>
                            <h4 class='media-heading'><a href='product.php?asset=".$data['AssetID']."'>".$data['Name']."</a></h4>
                            <h5 class='media-heading'> by <a href='#'>Samsung</a></h5>
                            <span>Status: </span><span class='text-success'><strong>".$data['Stock']." In Stock</strong></span>
                        </div>
                    </div>
                </td>
                <td class='col-sm-1 col-md-1' style='text-align: center'>
                    <strong>".$data['Num']."</strong>
                </td>
                <td class='col-sm-1 col-md-1 text-center'><strong>$".$data['CurrentPrice']."</strong></td>
                <td class='col-sm-1 col-md-1 text-center'><strong>$".$data['OriginalPrice']."</strong></td>
                <td class='col-sm-1 col-md-1 text-center'><strong>$".$data['OriginalPrice']*$data['Num']."</strong></td>
                <td class='col-sm-1 col-md-1'></td>
            </tr>";
        }
        return $output;
    }

    include "secret.php";
    include "functions.php";

    $conn = establishConnection($host, $dbname, $user, $pass);

    $orderID = $_GET["oid"];

    $sql_orderUserID = "SELECT UserID FROM Orders WHERE ID = :id";

    $result = $conn->prepare($sql_orderUserID);

    $result->bindValue(':id', $orderID, PDO::PARAM_STR);

    $result->execute();

    $data = $result->fetch(PDO::FETCH_ASSOC);

    $orderUserID = $data['UserID'];

    $usertype = checkUserType($conn);

    session_start();

    if(/*!isset($_SESSION["UserID"]) ||*/ $_SESSION["UserID"] != $orderUserID && $usertype != 3){ // if not logged in or user ID different from the user ID on order
        header("Location: ../index.php");
        exit();
    } else if ($_SESSION["UserID"] != $orderUserID && $usertype == 3) {
        $sql_otherUsername = "SELECT Username FROM Users WHERE ID = ".$orderUserID."";
        $result2 = $conn->prepare($sql_otherUsername);
        $result2->execute();
        $data = $result2->fetch(PDO::FETCH_ASSOC);

        $otherUsername = "<h1>Order by: ".$data['Username']."</h1>";
    } else {
        $uid = $orderUserID;
    }

    $sql_assetInfo = "SELECT DISTINCT COUNT(*) AS Num,
    CONVERT_TZ(OrderTime, '+00:00', CONCAT('+', CAST(DATE_FORMAT(SEC_TO_TIME(FLOOR(((SYSDATE() - UTC_TIMESTAMP())/10000)*3600)), '%H:%i') AS CHAR))) AS TimeOfOrder,
    Assets.Name, Assets.ID AS AssetID, OrderProducts.Price, Orders.ID FROM OrderProducts INNER JOIN Orders ON Orders.UserID = :uid INNER JOIN Assets ON AssetID = Assets.ID WHERE Orders.ID = :oid AND OrderID = :oid GROUP BY Assets.Name, OrderProducts.Price ORDER BY Num";

    $result2 = $conn->prepare($sql_assetInfo);

    $result2->bindValue(':uid', $uid, PDO::PARAM_STR);
    $result2->bindValue(':oid', $orderID, PDO::PARAM_STR);

    $result2->execute();

    $data = $result2->fetch(PDO::FETCH_ASSOC);

    $timeOfOrder = $data['TimeOfOrder'];

    /*while($data = $result2->fetch(PDO::FETCH_ASSOC)){
        $output .= "<a href='product.php?asset=".$data['AssetID']."'><h1> ".$data['Name']." </h1></a>
                    <h4> Price: $".$data['Price']. " </h4>
                    <h4> Number: ".$data['Num']." </h4>
                    <h4> Time of order: ".$data['TimeOfOrder']."</h4>";
    }*/


    $sql_totalCost = "SELECT SUM(OrderProducts.Price) AS TotalPrice FROM OrderProducts INNER JOIN Orders ON OrderProducts.OrderID = Orders.ID INNER JOIN Assets ON OrderProducts.AssetID = Assets.ID WHERE Orders.UserID = :uid AND OrderID = :oid";

    $result3 = $conn->prepare($sql_totalCost);

    $result3->bindValue(':uid', $uid, PDO::PARAM_STR);
    $result3->bindValue(':oid', $orderID, PDO::PARAM_STR);

    $result3->execute();

    $data = $result3->fetch(PDO::FETCH_ASSOC);

    $output .= "<hr><h3>Total Price: $".$data['TotalPrice']."</h3>";

    $basket = getOrderInfo($conn);

    $sql_totalPrice = "SELECT SUM(OrderProducts.Price) AS TotalPrice FROM OrderProducts INNER JOIN Orders ON Orders.ID = OrderID WHERE OrderID = :oid";

    $result = $conn->prepare($sql_totalPrice);

    $result->bindValue(':oid', $orderID, PDO::PARAM_STR);

    $result->execute();

    $data = $result->fetch(PDO::FETCH_ASSOC);

    $totalPrice = $data['TotalPrice'];
?>

<html>
    <head>
        <title>
            Order <?php echo $orderID; ?>
        </title>
        <meta charset="UTF-8" />
        <!--<link type="text/css" rel="Stylesheet" href="../style.css" />--->
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
        echo getHeader(5);
        ?>
        <div class="container">
            <?php echo $otherUsername; ?>
            <div class="row">
                <div class="col-sm-12 col-md-10 col-md-offset-1">
                    <form action='editOrderBack.php?order=<?php echo $orderID ?>' method='POST' enctype='multipart/form-data' id="form1">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th class="text-center">Price</th>
                                    <th class="text-center">Original Price</th>
                                    <th class="text-center">Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($basket != null){
                                    echo $basket; 
                                } else {
                                    echo "<th> There was an error loading this order </th>";
                                }
                                ?>
                                <tr>
                                    <td><h3>Time of order: <?php echo $timeOfOrder ?></h3></td>
                                    <td>   </td>
                                    <td>   </td>
                                    <td>   </td>
                                    <td><h3>Total</h3></td>
                                    <td class="text-right"><h3><strong id="totalPrice">$<?php echo $totalPrice ?></strong></h3></td>
                                </tr>
                            </tbody>
                        </table>
                    </from>
                </div>
            </div>
        </div>
    </body>
</html>