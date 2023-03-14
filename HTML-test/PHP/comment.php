<?php
    include "secret.php";
    include "functions.php";

    $conn = establishConnection($host, $dbname, $user, $pass);

    $assetID = $_GET["asset"];

    $resID = $_GET["comm"];

    session_start();

    $userID = $_SESSION["UserID"];

    $comment = $_POST['freeform'];

    $comment = filter_var($comment, FILTER_SANITIZE_STRING);

    $rating = $_POST['rating'];

    if(strlen($comment) > 200){
        header("Location: product.php?asset=" . $assetID."&err=1");
        exit();
    } else if (strlen($comment) < 1){
        header("Location: product.php?asset=" . $assetID."&err=2");
        exit();
    }

    if($resID == NULL){

        $sql_insert = "INSERT INTO Comment(UserID, AssetID, CommentText, PostTime) VALUES (:uid, :aid, :c, UTC_TIMESTAMP)";

        if($comment != null && $assetID != NULL){
            $conn->beginTransaction();

            $result = $conn->prepare($sql_insert);

            $result->bindValue(':uid', $userID, PDO::PARAM_STR);
            $result->bindValue(':aid', $assetID, PDO::PARAM_STR);
            $result->bindValue(':c', $comment, PDO::PARAM_STR);
            echo "test1 ", $comment;

            $result->execute();

            echo "test2";
            

            if($rating >= 1 && $rating <= 5){ // if rating is between 1 and 5
                echo $rating;
                $sql_commID = "SELECT ID FROM Comment ORDER BY ID desc LIMIT 1";
                $result = $conn->prepare($sql_commID);
                $result->execute();
                $data = $result->fetch(PDO::FETCH_ASSOC);

                $commID = $data['ID'];

                $sql_rating = "INSERT INTO Rating(CommentID, Stars) VALUES (:cid, :s)";
                echo "test";

                $result = $conn->prepare($sql_rating);

                $result->bindValue(':cid', $commID, PDO::PARAM_STR);
                $result->bindValue(':s', $rating, PDO::PARAM_STR);

                $result->execute();
            }

            $conn->commit();

            header("Location: product.php?asset=" . $assetID);
            
        } else if ($assetID != NULL) {  // if no comment
            header("Location: product.php?asset=" . $assetID."&err=2");
        } else {    // if no comment and no asset ID
            header("Location: ../index.php");
        }
    } else {
        $sql_insert = "INSERT INTO Comment(UserID, AssetID, CommentText, ResponseTo, PostTime) VALUES (:uid, :aid, :c, :r, UTC_TIMESTAMP)";
        if(isset($comment) && $assetID != NULL){
            $result = $conn->prepare($sql_insert);

            $result->bindValue(':uid', $userID, PDO::PARAM_STR);
            $result->bindValue(':aid', $assetID, PDO::PARAM_STR);
            $result->bindValue(':c', $comment, PDO::PARAM_STR);
            $result->bindValue(':r', $resID, PDO::PARAM_STR);

            echo "test1";

            $result->execute();

            echo "test2";

            header("Location: product.php?asset=" . $assetID);
            
        } else if ($assetID != NULL) {  // if no comment
            header("Location: product.php?asset=" . $assetID);
        } else {    // if no comment and no asset ID
            header("Location: ../index.php");
        }
    }
?>