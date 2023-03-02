<?php

    function printComments($firstComment, $replies, $assetID){
        $output = "";

        for($i = 0; $i < count($firstComment); $i++){
            $output .= "
            <div class='comment'>
                <h5> <b>".$firstComment[$i][1]."</b>: ".$firstComment[$i][2]."". showRating($firstComment[$i][4]) ." </h5><i>from ".$firstComment[$i][5]."</i>
                <a href='javascript:;'  onclick='replyComment(".$firstComment[$i][0].")'>reply</a>
                <div class='postComment' style='display: none;' id='".$firstComment[$i][0]."'>
                    <form action='comment.php?asset=".$assetID."&comm=".$firstComment[$i][0]."' method='POST'>
                        <label for='freeform'> Respond to ".$firstComment[$i][1].":</label><br>
                        <textarea id='freeform' name='freeform' rows='4' cols='50' maxlength='200'></textarea><br>
                        <input type='submit' value='Post Comment'>
                    </form>
                </div>";
            //echo $firstComment[$i][0];
            /*while ($replies[$i][3] == $firstComment[$i][0]){
                echo "test";
                array_splice($replies, 0, 1);
            }*/
            if($replies != null){
                $output .= subComment($firstComment[$i], $replies, $assetID);
            }
            $output .= "</div>";
        }
        return $output;
    }

    function showRating($rating){
        if($rating != null){
            $output = "<ul class='rating'>";

            for($i = 0; $i < 5; $i++){
                if ($i < $rating){
                    $output .= "<li class='fa fa-star star'></li>";
                } else {
                    $output .= "<li class='fa fa-star star disable'></li>";
                }
            }

            $output .= "</ul>";
        }
        return $output;
    }

    function subComment($firstComment, $replies, $assetID){
        $output = "";

        for($i = 0; $i < count($replies); $i++){
            if($replies[$i][3] == $firstComment[0]){
                $output .= "
                <div class='comment'>
                    <h5> <b>".$replies[$i][1]."</b>: ".$replies[$i][2]." </h5><a href='javascript:;'  onclick='replyComment(".$replies[$i][0].")'>reply</a>
                    <div class='postComment' style='display: none;' id='".$replies[$i][0]."'>
                        <form action='comment.php?asset=".$assetID."&comm=".$replies[$i][0]."' method='POST'>
                            <label for='freeform'> Respond to ".$replies[$i][1].":</label><br>
                            <textarea id='freeform' name='freeform' rows='4' cols='50' maxlength='200'></textarea><br>
                            <input type='submit' value='Post Comment'>
                        </form>
                    </div>";
                $output .= subComment($replies[$i], $replies, $assetID);
                $output .= "</div>";
            }
        }
        return $output;
    }

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

    $picturesHTML = "</div>";
    
    #$sql_firstComments = "SELECT Comment.ID, Username, CommentText, ResponseTo, Stars FROM Comment INNER JOIN Users ON Comment.UserID = Users.ID LEFT JOIN Rating ON CommentID = Comment.ID WHERE Comment.AssetID = :aid AND ResponseTo = 0 ORDER BY Comment.ID asc";
    $sql_firstComments = "SELECT Comment.ID, Username, CommentText, ResponseTo, Stars, IF(UserType.Type = 2 OR UserType.Type = 3, 'Admin', 'User') AS UserType FROM Comment INNER JOIN Users ON Comment.UserID = Users.ID LEFT JOIN Rating ON CommentID = Comment.ID INNER JOIN UserType ON Users.ID = UserType.ID WHERE Comment.AssetID = :aid AND ResponseTo = 0 ORDER BY Comment.ID asc";

    $result = $conn->prepare($sql_firstComments);

    session_start();

    $result->bindValue(':aid', $assetID, PDO::PARAM_STR);

    $result->execute();

    $comments = "";
    $count = 0;

    while($data = $result->fetch(PDO::FETCH_ASSOC)){
        $commID = $data['ID'];
        $username = $data['Username'];
        $comment = $data['CommentText'];
        $responseTo = $data['ResponseTo'];
        $rating = $data['Stars'];
        $usertype = $data['UserType'];

        $firstComment[$count][] = $commID;
        $firstComment[$count][] = $username;
        $firstComment[$count][] = $comment;
        $firstComment[$count][] = $responseTo;
        $firstComment[$count][] = $rating;
        $firstComment[$count][] = $usertype;

        $count++;

        /*if($responseTo == NULL){
            $comments .= "
            <div class='comment'>
                <h5> <b>".$username."</b>: ".$comment." </h5><a href='javascript:;'  onclick='replyComment(".$commID.")'>reply</a>
                <div class='postComment' style='display: none;' id='".$commID."'>
                    <form action='comment.php?asset=".$assetID."&comm=".$commID."' method='POST'>
                        <label for='freeform'> Respond to ".$username.":</label><br>
                        <textarea id='freeform' name='freeform' rows='4' cols='50' maxlength='200'></textarea><br>
                        <input type='submit' value='Post Comment'>
                    </form>
                </div>
            </div>";
        } else { // does not work
            $sql_respond = "SELECT Username FROM Comment INNER JOIN Users ON Comment.UserID = Users.ID WHERE Comment.ID = :r";
            $result = $conn->prepare($sql_comments);
            $result->bindValue(':r', $responseTo, PDO::PARAM_STR);
            $result->execute();

            $replyname = $result->fetch(PDO::FETCH_ASSOC);

            $comments .= "<div class='comment'><h5> <b>".$username." to ".$replyname."</b>: ".$comment." </h5><a href='#'>reply</a></div>";
        }*/
    }

    $count = 0;

    $sql_responses = "SELECT Comment.ID, Username, CommentText, ResponseTo FROM Comment INNER JOIN Users ON Comment.UserID = Users.ID WHERE Comment.AssetID = :aid AND ResponseTo != 0 ORDER BY Comment.ResponseTo asc, Comment.ID asc";

    $result = $conn->prepare($sql_responses);

    $result->bindValue(':aid', $assetID, PDO::PARAM_STR);

    $result->execute();

    while($data = $result->fetch(PDO::FETCH_ASSOC)){
        $commID = $data['ID'];
        $username = $data['Username'];
        $comment = $data['CommentText'];
        $responseTo = $data['ResponseTo'];

        $responses[$count][] = $commID;
        $responses[$count][] = $username;
        $responses[$count][] = $comment;
        $responses[$count][] = $responseTo;

        $count++;
    }

    //echo $responses[0][3];

    /*echo $firstComment[0][0];
    array_splice($firstComment, 0, 1);
    echo $firstComment[0][0];*/
?>

<html>
    <head>
        <meta charset="utf-8">
        <title><?php echo $name;?></title>
	    <link type="text/css" rel="Stylesheet" href="../style.css" />
        <link rel='stylesheet' type='text/css' href='../products.css'>
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
            <a href="Admin/editAsset.php?asset=<?php echo $assetID;?>">Edit asset info</a>
            <hr>
            <div class="postComment">
                <form action="comment.php?asset=<?php echo $assetID?>" method="POST">
                    <div class="stars">
                        <input type="number" name="rating" hidden>
                        <ul class='rating'>
                            <li class='fa fa-star star disable'></li>
                            <li class='fa fa-star star disable'></li>
                            <li class='fa fa-star star disable'></li>
                            <li class='fa fa-star star disable'></li>
                            <li class='fa fa-star star disable'></li>
                        </ul>
                    </div>
                    <label for="freeform">Comment on <?php echo $name ?>:</label><br>
                    <textarea id="freeform" name="freeform" rows="4" cols="50" maxlength="200"></textarea><br>
                    <input type="submit" value="Post Comment">
                </form>
            </div>
            <hr>
        </div>
        <script type='text/javascript' src='../javaScript.js'></script>
        <?php /*echo $comments;*/echo printComments($firstComment, $responses, $assetID); ?>
    </body>
</html>