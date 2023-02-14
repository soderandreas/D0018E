<?php
    include "secret.php";
    include "functions.php";

    session_start();

    if(isset($_SESSION["UserID"])){
        $userID = $_SESSION["UserID"];

        $conn = establishConnection($host, $dbname, $user, $pass);

        // Gets all information about the products
        $sql_basket = "SELECT DISTINCT COUNT(*) AS Num, Name, Price, Stock, AssetID FROM ShoppingBasket INNER JOIN Assets ON ShoppingBasket.AssetID = Assets.ID WHERE UserID = :id GROUP BY Name ORDER BY COUNT(*)";

        $result = $conn->prepare($sql_basket);

        $result->bindValue(':id', $userID, PDO::PARAM_STR);

        $result->execute();

        $basket = "";

        while($data = $result->fetch(PDO::FETCH_ASSOC)){
            $basket .= "<div id=".$data['AssetID']."> <p> ".$data['Name'].", Price: ".$data['Price']." $, Stock: ".$data['Stock'].", amount in basket: ".$data['Num']." </p>
                        <a href=# onclick='removeFromCart(".$data['AssetID'].")'> remove </a> <br> </div> ";
        }

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
	    <link rel="stylesheet" type="text/css" href="../style.css">
    </head>
    <body>
        <a href="../index.php">Go back</a> <br>
        <?php
            if ($basket != null){
                echo $basket . "<br> Total basket price: " . $totalPrice . "$ <br>";
            } else {
                echo "<p> Your basket is currently empty </p>";
            }
        ?>
        <a href="placeOrder.php">Place Order</a>
        <script type="text/Javascript" src="../javaScript.js"></script>
    </body>
</html>