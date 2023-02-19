<?php
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

    session_start();

    if(!isset($_SESSION["UserID"]) || $_SESSION["UserID"] != $orderUserID){ // if not logged in or user ID different from the user ID on order
        header("Location: ../index.php");
        exit();
    } else {
        $uid = $_SESSION["UserID"];
    }

    $sql_assetInfo = "SELECT DISTINCT COUNT(*) AS Num, Assets.Name, Assets.ID AS AssetID, OrderProducts.Price, Orders.ID FROM OrderProducts INNER JOIN Orders ON Orders.UserID = :uid INNER JOIN Assets ON AssetID = Assets.ID WHERE Orders.ID = :oid AND OrderID = :oid GROUP BY Assets.Name, OrderProducts.Price ORDER BY Num";

    $result2 = $conn->prepare($sql_assetInfo);

    $result2->bindValue(':uid', $uid, PDO::PARAM_STR);
    $result2->bindValue(':oid', $orderID, PDO::PARAM_STR);

    $result2->execute();

    while($data = $result2->fetch(PDO::FETCH_ASSOC)){
        $output .= "<a href='product.php?asset=".$data['AssetID']."'><h1> ".$data['Name']." </h1></a>
                    <h4> Price: $".$data['Price']. " </h4>
                    <h4> Number: ".$data['Num']." </h4>";
    }

    $sql_totalCost = "SELECT SUM(OrderProducts.Price) AS TotalPrice FROM OrderProducts INNER JOIN Orders ON OrderProducts.OrderID = Orders.ID INNER JOIN Assets ON OrderProducts.AssetID = Assets.ID WHERE Orders.UserID = :uid AND OrderID = :oid";

    $result3 = $conn->prepare($sql_totalCost);

    $result3->bindValue(':uid', $uid, PDO::PARAM_STR);
    $result3->bindValue(':oid', $orderID, PDO::PARAM_STR);

    $result3->execute();

    $data = $result3->fetch(PDO::FETCH_ASSOC);

    $output .= "<hr><h3>Total Price: $".$data['TotalPrice']."</h3>";

?>

<html>
    <head>
        <title>
            Order <?php echo $orderID; ?>
        </title>
        <meta charset="UTF-8" />
        <link type="text/css" rel="Stylesheet" href="../style.css" />
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
        <a href="../index.php">Go back</a>
        <?php
            echo $output;
        ?>
    </body>
</html>