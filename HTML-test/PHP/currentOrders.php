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

    $sql_orders = "SELECT DISTINCT OrderID, SUM(OrderProducts.Price) AS TotalPrice FROM OrderProducts INNER JOIN Orders ON OrderProducts.OrderID = Orders.ID INNER JOIN Assets ON OrderProducts.AssetID = Assets.ID WHERE Orders.UserID = :id GROUP BY OrderID ORDER BY OrderID";

    $result = $conn->prepare($sql_orders);

    $result->bindValue(':id', $id, PDO::PARAM_STR);

    $result->execute();

    while($data = $result->fetch(PDO::FETCH_ASSOC)){
        $output .= "<div class='order-container'>
                        <a href='order.php?oid=".$data['OrderID']."'>
                            <div class='order_info'>
                                <h2> Order: ".$data['OrderID']." </h2>
                                <h3> Total price: $".$data['TotalPrice']." </h3>
                            </div>
                        </a>
                        <a href=cancelOrder.php?order=".$data['OrderID'].">
                            <div class='order_cancel'>
                                <h3>Cancel Order</h3>
                            </div>
                        </a>
                    </div>";
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
        ?>
        <h2><a href="../index.php">Go back</a></h2>
        <h1>All current orders</h1>
        <hr>
        <?php
            if($output == ""){
                echo "You currently have no orders";
            } else {
                echo $output;
            }
        ?>
    </body>
</html>