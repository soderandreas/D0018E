<?php

    function getSlides($picturesHTML){
        $output = "";

        for($i = 0; $i < count($picturesHTML); $i++){
            $output .= "
            <div class='col-xs-4 item-photo'>
                <img style='max-width:80%;object-fit: scale-down;height: 100%;' src='../AssetPictures/". $picturesHTML[$i] ."' />
            </div>";
        }
        
        return $output;
    }

    function postTime($conn, $ID){
        $sql_time = "SELECT TIMESTAMPDIFF(SECOND, PostTime, UTC_TIMESTAMP) AS Difference FROM Comment WHERE ID = :id";

        $result = $conn->prepare($sql_time);
        
        $result->bindValue(':id', $ID, PDO::PARAM_STR);

        $result->execute();

        $data = $result->fetch(PDO::FETCH_ASSOC);

        if($data['Difference'] < 60){ // less then a minute
            $time = $data['Difference'] . " seconds ago";
        } else if ($data['Difference'] < 3600){ // less than an hour
            $time = floor($data['Difference']/60) . " minutes ago";
        } else if ($data['Difference'] < 86400){ // less than a day
            $time = floor($data['Difference']/3600) . " hours ago";
        } else if ($data['Difference'] < 31536000){ // less than a year
            $time = floor($data['Difference']/86400) . " days ago";
        } else { // more than a year
            $time = floor($data['Difference']/31536000) . " years ago";
        }

        return $time;
    }

    function printComments($conn, $firstComment, $replies, $assetID){
        $output = "";

        for($i = 0; $i < count($firstComment); $i++){
            $output .= "
            <div class='comment'>
                <h5> <b>".$firstComment[$i][1]."</b>: <p class='commentText'> ".$firstComment[$i][2]."</p>". showRating($firstComment[$i][4]) ." </h5><i>from ".$firstComment[$i][5]."</i><br><i>posted ".postTime($conn, $firstComment[$i][0])."</i><br>
                <a href='javascript:;'  onclick='replyComment(".$firstComment[$i][0].")'>reply</a>
                <div class='postComment' style='display: none;' id='".$firstComment[$i][0]."'>
                    <form action='comment.php?asset=".$assetID."&comm=".$firstComment[$i][0]."' method='POST'>
                        <label for='freeform'> Respond to ".$firstComment[$i][1].":</label><br>
                        <textarea id='freeform' name='freeform' rows='4' cols='50' maxlength='200'></textarea><br>
                        <input type='submit' value='Post Comment'>
                    </form>
                </div>";
            if($replies != null){
                $output .= subComment($conn, $firstComment[$i], $replies, $assetID);
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

    function subComment($conn, $firstComment, $replies, $assetID){
        $output = "";

        for($i = 0; $i < count($replies); $i++){
            if($replies[$i][3] == $firstComment[0]){
                $output .= "
                <div class='comment'>
                    <h5> <b>".$replies[$i][1]."</b>: <p class='commentText'>".$replies[$i][2]."</p> </h5><i>from ".$replies[$i][5]."</i><br><i>posted ".postTime($conn, $replies[$i][0])."</i><br>
                    <a href='javascript:;'  onclick='replyComment(".$replies[$i][0].")'>reply</a>
                    <div class='postComment' style='display: none;' id='".$replies[$i][0]."'>
                        <form action='comment.php?asset=".$assetID."&comm=".$replies[$i][0]."' method='POST'>
                            <label for='freeform'> Respond to ".$replies[$i][1].":</label><br>
                            <textarea id='freeform' name='freeform' rows='4' cols='50' maxlength='200'></textarea><br>
                            <input type='submit' value='Post Comment'>
                        </form>
                    </div>";
                $output .= subComment($conn, $replies[$i], $replies, $assetID);
                $output .= "</div>";
            }
        }
        return $output;
    }

    function purchased($conn){
        session_start();

        $assetID = $_GET["asset"];

        $sql_purchased = "SELECT CASE WHEN COUNT(AssetID) > 0 THEN 1 WHEN COUNT(AssetID) < 1 THEN 0 END AS Result FROM OrderProducts INNER JOIN Orders ON Orders.ID = OrderID WHERE AssetID = :aid AND Orders.UserID = :uid";

        $result = $conn->prepare($sql_purchased);

        $result->bindValue(':aid', $assetID, PDO::PARAM_STR);
        $result->bindValue(':uid', $_SESSION['UserID'], PDO::PARAM_STR);

        $result->execute();

        $data = $result->fetch(PDO::FETCH_ASSOC);

        return $data['Result'];
    }

    include "secret.php";
    include "functions.php";

    $conn = establishConnection($host, $dbname, $user, $pass);

    $userTypeNum = checkUserType($conn);

    $assetID = $_GET["asset"];

    session_start();

    if(!isset($_SESSION['UserID'])){
        header("Location: ../index.php");
        exit();
    }

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
            Description: ".$description."<br>
        ";


    $sql_pictures = "SELECT PictureName FROM AssetPictures WHERE AssetID = :id";

    $result = $conn->prepare($sql_pictures);

    $result->bindValue(':id', $assetID, PDO::PARAM_STR);

    $result->execute();

    while($data = $result->fetch(PDO::FETCH_ASSOC)){
        $picturesHTML[] = $data['PictureName'];
    }
    
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
    }

    $count = 0;

    $sql_responses = "SELECT Comment.ID, Username, CommentText, ResponseTo, Stars, IF(UserType.Type = 2 OR UserType.Type = 3, 'Admin', 'User') AS UserType FROM Comment INNER JOIN Users ON Comment.UserID = Users.ID LEFT JOIN Rating ON CommentID = Comment.ID INNER JOIN UserType ON Users.ID = UserType.ID WHERE Comment.AssetID = :aid AND ResponseTo != 0 ORDER BY Comment.ResponseTo asc, Comment.ID asc";

    $result = $conn->prepare($sql_responses);

    $result->bindValue(':aid', $assetID, PDO::PARAM_STR);

    $result->execute();

    while($data = $result->fetch(PDO::FETCH_ASSOC)){
        $commID = $data['ID'];
        $username = $data['Username'];
        $comment = $data['CommentText'];
        $responseTo = $data['ResponseTo'];
        $rating = $data['Stars'];
        $usertype = $data['UserType'];

        $responses[$count][] = $commID;
        $responses[$count][] = $username;
        $responses[$count][] = $comment;
        $responses[$count][] = $responseTo;
        $responses[$count][] = $rating;
        $responses[$count][] = $usertype;

        $count++;
    }

    if($_GET['err'] == 1){
        $notification = notification("Your comment/review can not be longer than 200 characters!", 3);
    } else if ($_GET['err'] == 2){
        $notification = notification("Your comment/review must be longer than 0 characters!", 3);
    }
?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../product.css">
        <script src=”https://code.jquery.com/jquery-3.6.0.min.js” integrity=”sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=” crossorigin=”anonymous”></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>

        <script>
        $(document).ready(function(){
                //-- Click on detail
                $("ul.menu-items > li").on("click",function(){
                    $("ul.menu-items > li").removeClass("activee");
                    $(this).addClass("activee");
                })

                $(".attr,.attr2").on("click",function(){
                    var clase = $(this).attr("class");

                    $("." + clase).removeClass("activee");
                    $(this).addClass("activee");
                })

                //-- Click on QUANTITY
                $(".btn-minus").on("click",function(){
                    var now = $(".section > div > input").val();
                    if ($.isNumeric(now)){
                        if (parseInt(now) -1 > 0){ now--;}
                        $(".section > div > input").val(now);
                    }else{
                        $(".section > div > input").val("1");
                    }
                })
                $("#Produkt-specifikationer").click(function() { 
                    // hide/shows Produkt-specifikationer and Recentioner              
                    $(".specifikationerd").css('display','block');
                    $(".Recentionerd").css('display','none');

                })
                $("#Recentioner").click(function() { 
                    // assumes element with id='button'
                    $(".specifikationerd").css('display','none');
                    $(".Recentionerd").css('display','block');
                })                 
                $(".btn-plus").on("click",function(){
                    var now = $(".section > div > input").val();
                    if ($.isNumeric(now)){
                        $(".section > div > input").val(parseInt(now)+1);
                    }else{
                        $(".section > div > input").val("1");
                    }
                })                        
            })
        </script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <meta charset="utf-8">
        <title><?php echo $name;?></title>
	    <link type="text/css" rel="Stylesheet" href="../style.css" />
        <link rel='stylesheet' type='text/css' href='../products.css'>
        <script src=”https://code.jquery.com/jquery-3.6.0.min.js” integrity=”sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=” crossorigin=”anonymous”></script>
        <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js'></script>
        <link href='https://raw.githubusercontent.com/daneden/animate.css/master/animate.css' rel='stylesheet'>
    </head>
    <body onload="showSlides(1);">
        <?php echo getHeader(5);
              echo $notification;
        ?>
        <div class="container">
            <div class="row">
                <div class="slideshow-container">
                    <!---<div class="col-xs-4 item-photo">
                        <img style="max-width:80%;object-fit: scale-down;height: 100%;" src="../AssetPictures/<?php echo $picturesHTML[0]; ?>" />
                    </div>--->
                    <?php echo getSlides($picturesHTML); ?>
                    <a class="prev" onclick="changeSlides(-1)">❮</a>
                    <a class="next" onclick="changeSlides(1)">❯</a>
                </div>
                <div class="col-xs-5" style="border:0px solid gray">

                    <h3><?php echo $name?></h3>    
                    <h5 style="color:#337ab7">Brand: <a href="#">Samsung</a></h5>
        

                    <h6 class="title-price"><small>Price</small></h6>
                    <h3 style="margin-top:0px;">$<?php echo $price ?></h3>

                    <iframe name="dummyframe" id="dummyframe" style="display: none;"></iframe>

                    <form id="form1" target="dummyframe">
                        <div class="section" style="padding-bottom:20px;">
                            <h6 class="title-attr" style="margin-top:15px;" ><small>Amount</small></h6>                    
                            <div>
                                <div class="btn-minus"><span class="glyphicon glyphicon-minus"></span></div>
                                    <input type="number" id="amountProd" value="1" min="1" max="10" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"/>
                                <div class="btn-plus"><span class="glyphicon glyphicon-plus"></span></div>
                            </div>
                            <h6 class="title-attr" style="margin-top:15px;" ><small>Stock: <?php echo $stock ?></small></h6>
                        </div>
                    </form>                
        

                    <div class="section" style="padding-bottom:20px;">
                        <button form="form1" class="btn btn-success" onclick="addToCartProduct(<?php echo $assetID ?>)"><span style="margin-right:0px" class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Add to cart</button>
                    </div>                                        
                </div>                              
        
                <div class="col-xs-9 about">
                    <ul class="menu-items">
                        <li id="Produkt-specifikationer" class="activee">Product Specifications</li>
                        <li id ="Recentioner">Discussion</li>
                    </ul>
                    <div class= "specifikationerd" style="width:100%;border-top:1px solid silver">
                        <p style="padding:15px;">
                            <?php if($userTypeNum != 1){
                                echo "<a href='Admin/editAsset.php?asset=".$assetID."'>Edit asset info</a><br>";
                            }?>
                            <small>
                                <?php
                                    echo $assetHTML;
                                ?>
                            </small>
                        </p>
                    </div>
                    <div class= "Recentionerd" style="width:100%;border-top:1px solid silver">
                        <div class="postComment">
                            <form action="comment.php?asset=<?php echo $assetID?>" method="POST">

                                <?php
                                    if(purchased($conn)){
                                        echo "
                                        <div class='stars'>
                                            <input type='number' name='rating' hidden>
                                            <ul class='rating'>
                                                <li class='fa fa-star star disable'></li>
                                                <li class='fa fa-star star disable'></li>
                                                <li class='fa fa-star star disable'></li>
                                                <li class='fa fa-star star disable'></li>
                                                <li class='fa fa-star star disable'></li>
                                            </ul>
                                        </div>";
                                    }
                                ?>
                                <label for="freeform">Comment on <?php echo $name ?>:</label><br>
                                <textarea id="freeform" name="freeform" rows="4" cols="50" maxlength="200"></textarea><br>
                                <input type="submit" value="Post Comment">
                            </form>
                        </div>
                        <script type='text/javascript' src='../javaScript.js'></script>
                        <?php echo printComments($conn, $firstComment, $responses, $assetID); ?>
                    </div>
                </div>
                </div>     
            </div>
        </div>
    </body>
</html>

<?php
/*  <div>
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
    <?php echo printComments($conn, $firstComment, $responses, $assetID); ?>*/
?>