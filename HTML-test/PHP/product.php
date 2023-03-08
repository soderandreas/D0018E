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
                <h5> <b>".$firstComment[$i][1]."</b>: ".$firstComment[$i][2]."". showRating($firstComment[$i][4]) ." </h5><i>from ".$firstComment[$i][5]."</i><br><i>posted ".postTime($conn, $firstComment[$i][0])."</i><br>
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
                    <h5> <b>".$replies[$i][1]."</b>: ".$replies[$i][2]." </h5><i>from ".$replies[$i][5]."</i><br><i>posted ".postTime($conn, $replies[$i][0])."</i><br>
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
            Current Stock: ".$stock."
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
        <?php echo getHeader(5); ?>
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
        

                    <!---<div class="section">
                        <h6 class="title-attr" style="margin-top:15px;" ><small>Color</small></h6>                    
                        <div>
                            <div class="attr" style="width:25px;background:#5a5a5a;"></div>
                            <div class="attr" style="width:25px;background:white;"></div>
                        </div>
                    </div>
                    <div class="section" style="padding-bottom:5px;">
                        <h6 class="title-attr"><small>Memory</small></h6>                    
                        <div>
                            <div class="attr2">69 GB</div>
                            <div class="attr2">420 GB</div>
                        </div>
                    </div>--->   
                    <div class="section" style="padding-bottom:20px;">
                        <h6 class="title-attr" style="margin-top:15px;" ><small>Antal</small></h6>                    
                        <div>
                            <div class="btn-minus"><span class="glyphicon glyphicon-minus"></span></div>
                            <input id="amountProd" value="1" />
                            <div class="btn-plus"><span class="glyphicon glyphicon-plus"></span></div>
                        </div>
                    </div>                
        

                    <div class="section" style="padding-bottom:20px;">
                        <button class="btn btn-success" onclick="addToCartProduct(<?php echo $assetID ?>)"><span style="margin-right:0px" class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Add to cart</button>
                    </div>                                        
                </div>                              
        
                <div class="col-xs-9 about">
                    <ul class="menu-items">
                        <li id="Produkt-specifikationer" class="activee">Product Specifications</li>
                        <li id ="Recentioner">Discussion</li>
                    </ul>
                    <div class= "specifikationerd" style="width:100%;border-top:1px solid silver">
                        <!---<p style="padding:15px;">
                            <small>
                            Stay connected either on the phone or the Web with the Galaxy S4 I337 from Samsung. With 16 GB of memory and a 4G connection, this phone stores precious photos and video and lets you upload them to a cloud or social network at blinding-fast speed. With a 17-hour operating life from one charge, this phone allows you keep in touch even on the go. 
        
                            With its built-in photo editor, the Galaxy S4 allows you to edit photos with the touch of a finger, eliminating extraneous background items. Usable with most carriers, this smartphone is the perfect companion for work or entertainment.
                            </small>
                        </p>
                        <small>
                            <ul>
                                <li>Super AMOLED capacitive touchscreen display with 16M colors</li>
                                <li>Available on GSM, AT T, T-Mobile and other carriers</li>
                                <li>Compatible with GSM 850 / 900 / 1800; HSDPA 850 / 1900 / 2100 LTE; 700 MHz Class 17 / 1700 / 2100 networks</li>
                                <li>MicroUSB and USB connectivity</li>
                                <li>Interfaces with Wi-Fi 802.11 a/b/g/n/ac, dual band and Bluetooth</li>
                                <li>Wi-Fi hotspot to keep other devices online when a connection is not available</li>
                                <li>SMS, MMS, email, Push Mail, IM and RSS messaging</li>
                                <li>Front-facing camera features autofocus, an LED flash, dual video call capability and a sharp 4128 x 3096 pixel picture</li>
                                <li>Features 16 GB memory and 2 GB RAM</li>
                                <li>Upgradeable Jelly Bean v4.2.2 to Jelly Bean v4.3 Android OS</li>
                                <li>17 hours of talk time, 350 hours standby time on one charge</li>
                                <li>Available in white or black</li>
                                <li>Model I337</li>
                                <li>Package includes phone, charger, battery and user manual</li>
                                <li>Phone is 5.38 inches high x 2.75 inches wide x 0.13 inches deep and weighs a mere 4.59 oz </li>
                            </ul>  
                        </small>--->
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
                        <script type='text/javascript' src='../javaScript.js'></script>
                        <?php echo printComments($conn, $firstComment, $responses, $assetID); ?>
                        <!---<p style="padding:15px;">
                            <small>
        
                            BOIIIIIIIII to edit photos with the touch of a finger, eliminating extraneous background items. Usable with most carriers, this smartphone is the perfect companion for work or entertainment.
                            </small>
                        </p>
                        <small>
                            <ul>
                                <li>Super AMOLED capacitive touchscreen display with 16M colors</li>
                                <li>Available on GSM, AT T, T-Mobile and other carriers</li>
                                <li>Compatible with GSM 850 / 900 / 1800; HSDPA 850 / 1900 / 2100 LTE; 700 MHz Class 17 / 1700 / 2100 networks</li>
                                <li>MicroUSB and USB connectivity</li>
                                <li>Interfaces with Wi-Fi 802.11 a/b/g/n/ac, dual band and Bluetooth</li>
                                <li>Wi-Fi hotspot to keep other devices online when a connection is not available</li>
                                <li>SMS, MMS, email, Push Mail, IM and RSS messaging</li>
                                <li>Front-facing camera features autofocus, an LED flash, dual video call capability and a sharp 4128 x 3096 pixel picture</li>
                                <li>Features 16 GB memory and 2 GB RAM</li>
                                <li>Upgradeable Jelly Bean v4.2.2 to Jelly Bean v4.3 Android OS</li>
                                <li>17 hours of talk time, 350 hours standby time on one charge</li>
                                <li>Available in white or black</li>
                                <li>Model I337</li>
                                <li>Package includes phone, charger, battery and user manual</li>
                                <li>Phone is 5.38 inches high x 2.75 inches wide x 0.13 inches deep and weighs a mere 4.59 oz </li>
                            </ul>  
                        </small>--->
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