<?php
    include "../secret.php";
    include "../functions.php";

    function removeAsset($assetID, $conn){ // removes the asset from the database because of an error with the picture files
        #echo "<br> test4.5 ", $assetID;
        $sql_remove = "DELETE FROM Assets WHERE ID = :id";
        $result_remove = $conn->prepare($sql_remove);
        $result_remove->bindValue(':id', $assetID, PDO::PARAM_STR);
        $result_remove->execute();
    }

    $target_dir = "../../AssetPictures/"; // The target directory
    $result = "";

    $conn = establishConnection($host, $dbname, $user, $pass);

    $usertype = checkUserType($conn);

    if($usertype != 2 && $usertype != 3){
        header("Location: ../../index.php");
        exit();
    }

    if(isset($_POST['title']) && isset($_POST['description']) && isset($_POST['price']) && isset($_POST['stock']) && $_POST['price'] > 0 && $_POST['stock'] >= 0 && strlen($_POST['description']) > 0 && strlen($_POST['title']) > 0 && strlen($_POST['description']) < 1001 && strlen($_POST['title']) < 61) { // Asset information
        $name = $_POST['title'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];

        $name = filter_var($name, FILTER_SANITIZE_STRING);
        $description = filter_var($description, FILTER_SANITIZE_STRING);
        $price = filter_var($price, FILTER_SANITIZE_STRING);
        $stock = filter_var($stock, FILTER_SANITIZE_STRING);

        #echo $name, "<br>", $description, "<br>",  $price, "<br>", $stock;

        $sql_query = "INSERT INTO Assets(Name, Description, Price, Stock) VALUES (:n, :d, :p, :s)";

        $result = $conn->prepare($sql_query);

        $result->bindValue(':n', $name, PDO::PARAM_STR);
        $result->bindValue(':d', $description, PDO::PARAM_STR);
        $result->bindValue(':p', $price, PDO::PARAM_STR);
        $result->bindValue(':s', $stock, PDO::PARAM_STR);

    } else { // error in asset information
        header("Location: newAsset.php?err=1");
        exit();
    }

    $result->execute(); // finally update the asset table

    // need to get the ID for the new asset
    $sql_assetID = "SELECT ID FROM Assets WHERE Name = :n";
    $result2 = $conn->prepare($sql_assetID);
    $result2->bindValue(':n', $name, PDO::PARAM_STR);
    #echo "<br> test1 ", $name;
    $result2->execute();
    #echo "<br> test2 ", $fType;

    while($data = $result2->fetch(PDO::FETCH_ASSOC)){
        $id = $data['ID'];
    }
    #echo "<br> test3";

    $total_count = count($_FILES['upload']['name']);
    #echo "<br> Number of files: ", $total_count;
    // loop through the files
    for($i = 0; $i < $total_count; $i++) {
        $tmpFilePath = $_FILES["upload"]["tmp_name"][$i];
        $basename = basename($_FILES["upload"]["name"][$i]);
        $fType = strtolower(pathinfo($basename, PATHINFO_EXTENSION));
        #echo "<br>", $fType, " ", $basename;
        $newFileName = $id . "_" . $i;
        $targetPath = $target_dir . $newFileName . "." . $fType;
        #echo "<br>", $targetPath, " ", $tmpFilePath;

        if($fType != "jpg" && $fType != "png" && $fType != "jpeg" && $fType != "gif") { // if the file is not a picture
            #echo "<br> test4";
            removeAsset($id, $conn);
            #echo "<br> test5";
            #echo "<br> error in with file type";

			header("Location: newAsset.php?err=2");
			exit();
		}

        if(move_uploaded_file($tmpFilePath, $targetPath)) { // try uploading the picture
            #echo "test 4";
            $sql_query2 = "INSERT INTO AssetPictures(AssetID, PictureName) VALUES (:id, :n)"; // insert the name of the file into the database
            $result3 = $conn->prepare($sql_query2);
            $result3->bindValue(':id', $id, PDO::PARAM_STR);
            $result3->bindValue(':n', $newFileName . "." . $fType, PDO::PARAM_STR);
            $result3->execute();
        } else { // upload failed
            removeAsset($id, $conn);
            #echo "<br> upload failed ", $_FILES["upload"]["error"][$i];

            header("Location: newAsset.php?err=3");
			exit();
        }
    }
    header("Location: newAsset.php?succ=1");
?>