<?php
    function getOrder($conn){
        $sql_getOrder = "SELECT DISTINCT COUNT(*) AS Num, Name, OrderProducts.Price AS OriginalPrice, Assets.Price AS CurrentPrice, Stock, AssetID FROM OrderProducts INNER JOIN Orders ON Orders.ID = OrderID INNER JOIN Assets ON Assets.ID = AssetID WHERE Orders.ID = :oid GROUP BY Name, OrderProducts.Price ORDER BY AssetID";
        $sql_getImage = "SELECT PictureName FROM AssetPictures WHERE AssetID = :aid LIMIT 1";

        $orderID = $_GET['order'];

        $result = $conn->prepare($sql_getOrder);

        $result->bindValue(':oid', $orderID, PDO::PARAM_STR);

        $result->execute();

        $i = 0;

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
                    <input type='number' class='form-control amountInOrder' id='".$i."' name='id".$data['AssetID']."p".$data['OriginalPrice']."' min='0', max='10' value='".$data['Num']."'>
                </td>
                <td class='col-sm-1 col-md-1 text-center'><strong id='currPriceFor".$i."'>$".$data['CurrentPrice']."</strong></td>
                <td class='col-sm-1 col-md-1 text-center'><strong id='oriPriceFor".$i."'>$".$data['OriginalPrice']."</strong></td>
                <td class='col-sm-1 col-md-1 text-center'><strong id='totalFor".$i."'>$".$data['OriginalPrice']*$data['Num']."</strong></td>
                <td class='col-sm-1 col-md-1'></td>
            </tr>";
            $i++;
        }

        return $output;
    }

    include "secret.php";
    include "functions.php";

    $conn = establishConnection($host, $dbname, $user, $pass);

    session_start();

    $userID = $_SESSION["UserID"];

    $orderID = $_GET['order'];

    $sql_orderUserID = "SELECT DISTINCT Users.ID FROM Users INNER JOIN Orders ON Users.ID = UserID WHERE Orders.ID = :oid";

    $result = $conn->prepare($sql_orderUserID);

    $result->bindValue(':oid', $orderID, PDO::PARAM_STR);

    $result->execute();

    $data = $result->fetch(PDO::FETCH_ASSOC);

    $usertype = checkUserType($conn); 

    if($data['ID'] != $userID && $usertype != 3){ // if the user trying to access the page is not the person who made the order and they are not a super admin
        header("Location: ../index.php");
        exit();
    } else if ($data['ID'] != $userID && $usertype == 3) {
        $sql_otherUsername = "SELECT Username FROM Users WHERE ID = ".$data['ID']."";
        $result2 = $conn->prepare($sql_otherUsername);
        $result2->execute();
        $data = $result2->fetch(PDO::FETCH_ASSOC);

        $otherUsername = "<h1>Order by: ".$data['Username']."</h1>";
    }

    $basket = getOrder($conn);

    $sql_totalPrice = "SELECT SUM(OrderProducts.Price) AS TotalPrice FROM OrderProducts INNER JOIN Orders ON Orders.ID = OrderID WHERE OrderID = :oid";

    $result = $conn->prepare($sql_totalPrice);

    $result->bindValue(':oid', $orderID, PDO::PARAM_STR);

    $result->execute();

    $data = $result->fetch(PDO::FETCH_ASSOC);

    $totalPrice = $data['TotalPrice'];

    if($_GET['succ'] == 1){
        $notification = notification("The changes has been saved!", 1);
    } else if ($_GET['err'] == 1){
        $notification = notification("ERROR", 3);
    } else if ($_GET['err'] == 2){
        $notification = notification("Trying to add more assets to order than there are available", 3);
    } else if ($_GET['err'] == 3){
        $notification = notification("You can not change your order when its status is 'sent'", 3);
    } else if ($_GET['err'] == 4){
        $notification = notification("You can not add more than 10 assets", 3);
    }
?>

<html>
    <head>
        <meta charset="utf-8">
        <title>Edit Order </title>
	    <!--<link rel="stylesheet" type="text/css" href="../style.css">--->
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
            echo $notification;
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
                                    <td>   </td>
                                    <td>   </td>
                                    <td>   </td>
                                    <td>   </td>
                                    <td><h3>Total</h3></td>
                                    <td class="text-right"><h3><strong id="totalPrice">$<?php echo $totalPrice ?></strong></h3></td>
                                </tr>
                                <tr>
                                    <td>   </td>
                                    <td>   </td>
                                    <td>   </td>
                                    <td>   </td>
                                    <td>
                                    <button type="button" class="btn btn-default" onclick="location.href='currentOrders.php'">
                                        <span class="glyphicon glyphicon-shopping-cart">Back to Orders</span>
                                    </button></td>
                                    <td>
                                    <button type="submit" form="form1" value="Submit" class="btn btn-success">
                                        <span class="glyphicon glyphicon-play">Save Changes</span>
                                    </button></td>
                                </tr>
                            </tbody>
                        </table>
                    </from>
                </div>
            </div>
        </div>

        <script type="text/Javascript" src="../javaScript.js"></script>
    </body>
</html>