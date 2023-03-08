<?php

    function getBasket($conn){
        $sql_getBasket = "SELECT DISTINCT COUNT(*) AS Num, Name, Price, Stock, AssetID FROM ShoppingBasket INNER JOIN Assets ON ShoppingBasket.AssetID = Assets.ID WHERE UserID = :id GROUP BY Name ORDER BY AssetID";
        $sql_getImage = "SELECT PictureName FROM AssetPictures WHERE AssetID = :aid LIMIT 1";

        session_start();

        $userID = $_SESSION["UserID"];

        $result = $conn->prepare($sql_getBasket);

        $result->bindValue(':id', $userID, PDO::PARAM_STR);

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
                            <h5 class='media-heading'> by <a href='#'>Brand name</a></h5>
                            <span>Status: </span><span class='text-success'><strong>".$data['Stock']." In Stock</strong></span>
                        </div>
                    </div>
                </td>
                <td class='col-sm-1 col-md-1' style='text-align: center'>
                    <input type='number' class='form-control amountIn' id='".$data['AssetID']."' min='1', max='10' value='".$data['Num']."'>
                </td>
                <td class='col-sm-1 col-md-1 text-center'><strong id='priceFor".$data['AssetID']."'>$".$data['Price']."</strong></td>
                <td class='col-sm-1 col-md-1 text-center'><strong id='totalFor".$data['AssetID']."'>$".$data['Price']*$data['Num']."</strong></td>
                <td class='col-sm-1 col-md-1'>
                    <button type='button' class='btn btn-danger' onclick='removeFromCart(".$data['AssetID'].");'>
                        <span class='glyphicon glyphicon-remove'>Remove</span> 
                    </button>
                </td>
            </tr>";
        }
        return $output;
    }

    include "secret.php";
    include "functions.php";

    session_start();

    if(isset($_SESSION["UserID"])){
        $userID = $_SESSION["UserID"];

        $conn = establishConnection($host, $dbname, $user, $pass);

        // Gets all information about the products
        $sql_basket = "SELECT DISTINCT COUNT(*) AS Num, Name, Price, Stock, AssetID FROM ShoppingBasket INNER JOIN Assets ON ShoppingBasket.AssetID = Assets.ID WHERE UserID = :id GROUP BY Name ORDER BY AssetID";

        $result = $conn->prepare($sql_basket);

        $result->bindValue(':id', $userID, PDO::PARAM_STR);

        $result->execute();

        $basket = getBasket($conn);

        // Gets the total price of the products
        $sql_totPrice = "SELECT SUM(Price) AS PriceTotal FROM ShoppingBasket INNER JOIN Assets ON ShoppingBasket.AssetID = Assets.ID WHERE UserID = :id";

        $result = $conn->prepare($sql_totPrice);

        $result->bindValue(':id', $userID, PDO::PARAM_STR);

        $result->execute();

        $data = $result->fetch(PDO::FETCH_ASSOC);

        $totalPrice = $data['PriceTotal'];

    } else {
        header("Location: ../index.php");
        exit();
    }
?>

<html>
    <head>
        <meta charset="utf-8">
        <title>Shopping Cart</title>
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
        <?php echo getHeader(5); ?>
        <?php
            /*if ($basket != null){
                echo $basket . "<br> Total basket price: $" . $totalPrice . "<br>";
            } else {
                echo "<p> Your basket is currently empty </p>";
            }*/
        ?>

        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-10 col-md-offset-1">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Total</th>
                                <th> </th>
                            </tr>
                        </thead>
                        <tbody>
                            <form action="/action_page.php">
                                <?php
                                if ($basket != null){
                                    echo getBasket($conn); 
                                } else {
                                    echo "<th>Your basket is currently empty </th>";
                                }
                                ?>
                            </form>
                            <tr>
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
                                <td>
                                <button type="button" class="btn btn-default" onclick="location.href='../index.php'">
                                    <span class="glyphicon glyphicon-shopping-cart">Continue Shopping</span>
                                </button></td>
                                <td>
                                <button type="button" class="btn btn-success" onclick="location.href='placeOrder.php'">
                                    <span class="glyphicon glyphicon-play">Checkout</span>
                                </button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script type="text/Javascript" src="../javaScript.js"></script>
    </body>
</html>

<!--<tr>
                                <td class="col-md-6">
                                <div class="media">
                                    <a class="thumbnail pull-left" href="#"> <img class="media-object" src="http://icons.iconarchive.com/icons/custom-icon-design/flatastic-2/72/product-icon.png" style="width: 72px; height: 72px;"> </a>
                                    <div class="media-body">
                                        <h4 class="media-heading"><a href="#">Product name</a></h4>
                                        <h5 class="media-heading"> by <a href="#">Brand name</a></h5>
                                        <span>Status: </span><span class="text-warning"><strong>Leaves warehouse in 2 - 3 weeks</strong></span>
                                    </div>
                                </div></td>
                                <td class="col-md-1" style="text-align: center">
                                <input type="email" class="form-control" id="exampleInputEmail1" value="2">
                                </td>
                                <td class="col-md-1 text-center"><strong>$4.99</strong></td>
                                <td class="col-md-1 text-center"><strong>$9.98</strong></td>
                                <td class="col-md-1">
                                <button type="button" class="btn btn-danger">
                                    <span class="glyphicon glyphicon-remove"></span> Remove
                                </button></td>
                            </tr>-->
                            <!--<tr>
                                <td>   </td>
                                <td>   </td>
                                <td>   </td>
                                <td><h5>Subtotal</h5></td>
                                <td class="text-right"><h5><strong>$24.59</strong></h5></td>
                            </tr>
                            <tr>
                                <td>   </td>
                                <td>   </td>
                                <td>   </td>
                                <td><h5>Estimated shipping</h5></td>
                                <td class="text-right"><h5><strong>$6.94</strong></h5></td>
                            </tr>-->