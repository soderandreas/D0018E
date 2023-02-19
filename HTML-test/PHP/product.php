<?php
    include "secret.php";
    include "functions.php";

    $conn = establishConnection($host, $dbname, $user, $pass);

    $usertype = checkUserType($conn);

    $assetID = $_GET["asset"];

    $sql_asset = "SELECT DISTINCT Name, Description, Price, Stock FROM Assets INNER JOIN AssetPictures ON Assets.ID = AssetPictures.AssetID WHERE Assets.ID = :id";
    $result = $conn->prepare($sql_asset);

    $result->bindValue(':id', $assetID, PDO::PARAM_STR);

    $result->execute();

    // used to store globals values
    $name = "";
    $description = "";
    $price = "";
    $stock = "";

    while($data = $result->fetch(PDO::FETCH_ASSOC)){
        $name = $data['Name'];
        $description = $data['Description'];
        $price = $data['Price'];
        $stock = $data['Stock'];
    }

    $assetHTML = "
        <div>
            <h1>".$name."</h1>
            <h2> Description: ".$description."</h2>
            <h4> Price: ".$price." $</h4>
            <h4> Current Stock: ".$stock."</h4>
        </div>";


    $sql_pictures = "SELECT PictureName FROM AssetPictures WHERE AssetID = :id";

    $result = $conn->prepare($sql_pictures);

    $result->bindValue(':id', $assetID, PDO::PARAM_STR);

    $result->execute();

    $picturesHTML = "<div>";

    while($data = $result->fetch(PDO::FETCH_ASSOC)){
        $picturesHTML .= "
            ";
    }

    $picturesHTML = "</div>"
?>

<html>
    <head>
        <meta charset="utf-8">
        <title><?php echo $name;?></title>
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
        <div>
            <?php echo getHeader(5); ?>
            <a href="../index.php">Go Back</a>
            <?php
                echo $assetHTML;
                echo $picturesHTML;
            ?>
        </div>
    </body>
</html>