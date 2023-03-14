<?php
    function getBasketInfo($conn){
        $sql_getOrder = "SELECT COUNT(*) AS NumOfProd, Assets.ID, Assets.Name, Assets.Price, Assets.Stock FROM ShoppingBasket INNER JOIN Assets ON Assets.ID = AssetID WHERE UserID = :uid GROUP BY Assets.ID";
        $sql_getImage = "SELECT PictureName FROM AssetPictures WHERE AssetID = :aid LIMIT 1";

        $userID = $_GET['user'];

        $result = $conn->prepare($sql_getOrder);

        $result->bindValue(':uid', $userID, PDO::PARAM_STR);

        $result->execute();

        while($data = $result->fetch(PDO::FETCH_ASSOC)){
            $result2 = $conn->prepare($sql_getImage);

            $result2->bindValue(':aid', $data['ID'], PDO::PARAM_STR);

            $result2->execute();

            $data2 = $result2->fetch(PDO::FETCH_ASSOC);

            $output .= "
            <tr id=".$data['ID'].">
                <td class='col-sm-8 col-md-6'>
                    <div class='media'>
                        <a class='thumbnail pull-left' href='product.php?asset=".$data['ID']."'> <img class='media-object' src='../../AssetPictures/".$data2['PictureName']."' style='width: 72px; height: 72px;'> </a>
                        <div class='media-body'>
                            <h4 class='media-heading'><a href='product.php?asset=".$data['ID']."'>".$data['Name']."</a></h4>
                            <h5 class='media-heading'> by <a href='#'>Samsung</a></h5>
                            <span>Status: </span><span class='text-success'><strong>".$data['Stock']." In Stock</strong></span>
                        </div>
                    </div>
                </td>
                <td class='col-sm-1 col-md-1' style='text-align: center'>
                    <strong>".$data['NumOfProd']."</strong>
                </td>
                <td class='col-sm-1 col-md-1 text-center'><strong>$".$data['Price']."</strong></td>
                <td class='col-sm-1 col-md-1 text-center'><strong>$".$data['Price']*$data['NumOfProd']."</strong></td>
                <td class='col-sm-1 col-md-1'></td>
            </tr>";
        }
        return $output;
    }

    include "../secret.php";
    include "../functions.php";

    $conn = establishConnection($host, $dbname, $user, $pass);

    $usertype = checkUserType($conn);

    if($usertype != 3){ // if not super admin
        header("Location: ../../index.php");
        exit();
    }

    $otherUserID = $_GET['user'];

    $sql_nameOfUser = "SELECT Username FROM Users WHERE ID = :id";

    $result = $conn->prepare($sql_nameOfUser);

    $result->bindValue(':id', $otherUserID, PDO::PARAM_STR);

    $result->execute();

    $data = $result->fetch(PDO::FETCH_ASSOC);

    $username = $data['Username'];

    $sql_otherUserCart = "SELECT COUNT(*) AS NumOfProd, Assets.ID, Assets.Name, Assets.Price, Assets.Stock FROM ShoppingBasket INNER JOIN Assets ON Assets.ID = AssetID WHERE UserID = :uid GROUP BY Assets.ID";

    $result = $conn->prepare($sql_otherUserCart);

    $result->bindValue(':uid', $otherUserID, PDO::PARAM_STR);

    $result->execute();

    while($data = $result->fetch(PDO::FETCH_ASSOC)){
        $output .= "<a href='../product.php?asset=".$data['ID']."'><h1> ".$data['Name']." </h1></a>
                    <h4> Price: $".$data['Price']. " </h4>
                    <h4> Current Stock: ".$data['Stock']." </h4>
                    <h4> Number: ".$data['NumOfProd']." </h4>";
    }

    $basket = getBasketInfo($conn);

    $sql_totalPrice = "SELECT SUM(Assets.Price) AS TotalPrice FROM ShoppingBasket INNER JOIN Assets ON Assets.ID = AssetID WHERE UserID = :uid";

    $result = $conn->prepare($sql_totalPrice);

    $result->bindValue(':uid', $otherUserID, PDO::PARAM_STR);

    $result->execute();

    $data = $result->fetch(PDO::FETCH_ASSOC);

    $totalPrice = $data['TotalPrice'];
?>

<html>

    <head>
        <meta charset="utf-8">
        <title>Shopping basket of <?php echo $username; ?></title>
        <!--<link rel="stylesheet" type="text/css" href="../../style.css">-->
        <script type="text/Javascript" src="../../javaScript.js"></script>
        <link rel='stylesheet' type='text/css' href='../../products.css'>
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
            echo getHeader(6);
        ?>
        <h1>Shopping basket of <?php echo $username; ?></h1>

        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-10 col-md-offset-1">
                    <form action='editOrderBack.php?order=<?php echo $orderID ?>' method='POST' enctype='multipart/form-data' id="form1">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th class="text-center">Price</th>
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
                                    <td><h3>Total:</h3></td>
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