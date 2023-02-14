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
    </head>
    <body>
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