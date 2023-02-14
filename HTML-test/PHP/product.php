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
        <title>Register</title>
	    <link rel="stylesheet" type="text/css" href="../style.css">
    </head>
    <body>
        <div>
            <a href="../index.php">Go Back</a>
            <?php
                echo $assetHTML;
                echo $picturesHTML;
            ?>
        </div>
    </body>
</html>