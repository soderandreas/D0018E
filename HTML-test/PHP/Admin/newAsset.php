<?php
    include "../secret.php";
    include "../functions.php";

    $conn = establishConnection($host, $dbname, $user, $pass);

    $usertype = checkUserType($conn);

    if($usertype != 2 && $usertype != 3){
        header("Location: ../../index.php");
    }

    $error = $_GET["err"];

    switch ($error){
        case 1:
            echo "Error in the asset information. Please try again!";
            break;
        case 2:
            echo "The file(s) entered where not pictures of any of the following formats: 'jpg', 'png', 'jpeg' and 'gif'.";
            break;
        case 3:
            echo "Upload of files failed. Please try again!";
            break;
    }       
?>

<html>
    <head>
        <title>
            Add a new asset to the website
        </title>
        <meta charset="UTF-8" />
        <link type="text/css" rel="Stylesheet" href="../../style.css" />
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
            echo getHeader(2);
        ?>
        <div>
            <form action="newAssetBack.php" method="POST" enctype="multipart/form-data">
                <h3><a href="../../index.php">Go Back</a></h3>
                <label for="title">Title:</label>
                <input type="text" id="assetTitle" name="title" required><br>
                <span id="titleChars">60 characters remaining.</span><br>
                <label for="description">Description:</label>
                <input type="text" id="assetDescription" name="description" required><br>
                <span id="descriptionChars">1000 characters remaining.</span><br>
                <label for="price">Price:</label>
                <input type="number" id="price" name="price" required><br>
                <label for="stock">Stock:</label>
                <input type="number" id="stock" name="stock" required><br>
                <label for="image">Select image:</label>
                <input type="file" multiple="multiple" name="upload[]" id="fileToUpload" required><br>
                <button type="submit" href="newAssetBack.php">Submit</button>
            </form>
        </div>
        <script type="text/Javascript" src="../../javaScript.js"></script>
    </body>
</html>