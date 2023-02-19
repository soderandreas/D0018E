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
            $basket .= "<div id=".$data['AssetID']."> <p> ".$data['Name'].", Price: $".$data['Price'].", Stock: ".$data['Stock'].", amount in basket: ".$data['Num']." </p>
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
        <a href="../index.php">Go back</a> <br>
        <?php
            if ($basket != null){
                echo $basket . "<br> Total basket price: $" . $totalPrice . "<br>";
            } else {
                echo "<p> Your basket is currently empty </p>";
            }
        ?>
        <a href="placeOrder.php">Place Order</a>
        <script type="text/Javascript" src="../javaScript.js"></script>
    </body>
</html>