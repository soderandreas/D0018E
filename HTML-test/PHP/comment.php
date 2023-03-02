<?php
    include "secret.php";
    include "functions.php";

    $conn = establishConnection($host, $dbname, $user, $pass);

    $assetID = $_GET["asset"];

    $resID = $_GET["comm"];

    session_start();

    $userID = $_SESSION["UserID"];

    $comment = $_POST['freeform'];

    $rating = $_POST['rating'];

    if($resID == NULL){
        $sql_insert = "INSERT INTO Comment(UserID, AssetID, CommentText) VALUES (:uid, :aid, :c)";

        if(isset($comment) && $assetID != NULL){
            $result = $conn->prepare($sql_insert);

            $result->bindValue(':uid', $userID, PDO::PARAM_STR);
            $result->bindValue(':aid', $assetID, PDO::PARAM_STR);
            $result->bindValue(':c', $comment, PDO::PARAM_STR);

            $result->execute();
            

            if($rating >= 1 || $rating <= 5){ // if rating is between 1 and 5
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

            header("Location: product.php?asset=" . $assetID);
            
        } else if ($assetID != NULL) {  // if no comment
            header("Location: product.php?asset=" . $assetID);
        } else {    // if no comment and no asset ID
            header("Location: ../index.php");
        }
    } else {
        $sql_insert = "INSERT INTO Comment(UserID, AssetID, CommentText, ResponseTo) VALUES (:uid, :aid, :c, :r)";
        if(isset($comment) && $assetID != NULL){
            $result = $conn->prepare($sql_insert);

            $result->bindValue(':uid', $userID, PDO::PARAM_STR);
            $result->bindValue(':aid', $assetID, PDO::PARAM_STR);
            $result->bindValue(':c', $comment, PDO::PARAM_STR);
            $result->bindValue(':r', $resID, PDO::PARAM_STR);

            $result->execute();

            header("Location: product.php?asset=" . $assetID);
            
        } else if ($assetID != NULL) {  // if no comment
            header("Location: product.php?asset=" . $assetID);
        } else {    // if no comment and no asset ID
            header("Location: ../index.php");
        }
    }
?>