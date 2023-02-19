<?php
    include "secret.php";
    include "functions.php";

    function getAssets($conn){
        $assets = "";

        $sql_assets = "SELECT Assets.ID, Name, Price, Stock, PictureName FROM Assets INNER JOIN AssetPictures ON Assets.ID = AssetPictures.AssetID";
        $result = $conn->prepare($sql_assets);

        $result->execute();
        $currID = null;

        while($data = $result->fetch(PDO::FETCH_ASSOC)){
            if($currID == null || $currID != $data['ID']){ // Only add asset for first picture associated with asset
                $currID = $data['ID'];
                /*$assets .= "
                <a href = 'PHP/product.php?asset=".$currID."'>
                    <div class='product-container'>
                        <div>
                            <img src='/AssetPictures/".$data['PictureName']."' >
                        </div>
                        <div>
                            <h2>". $data['Name'] ."</h2>
                            <h3>". $data['Price'] ." $<h3>
                            <h4> Current Stock: ". $data['Stock'] ."</h4>  
                        </div>
                    </div>
                </a>";*/
                /*<img class='pic-1' src='/AssetPictures/".getPictures($conn)."'>*/
                $assets .= "
                    <div class='col-md-3 col-sm-6'>
                        <div class='product-grid3'>    
                            <div class='product-image3'>
                                <a href='PHP/product.php?asset=".$currID."'>
                                    ".getPictures($conn, $currID)."
                                </a>
                                <ul class='social' onclick='addToCart(".$currID.")'>
                                    <li><a href='#'><i class='fa fa-shopping-cart'></i></a></li>
                                </ul>
                                <span class='product-new-label'>New</span>
                            </div>
                            <div class='product-content'>
                                <h3 class='title'><a href='#'>".$data['Name']."</a></h3>
                                <div class='price'>
                                    $".$data['Price']."
                                </div>
                                <ul class='rating'>
                                    <li class='fa fa-star'></li>
                                    <li class='fa fa-star'></li>
                                    <li class='fa fa-star'></li>
                                    <li class='fa fa-star disable'></li>
                                    <li class='fa fa-star disable'></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    ";
            }
        }

        return $assets;
    }

    function getPictures($conn, $id){
        $sql_pictures = "SELECT PictureName FROM AssetPictures WHERE AssetID = :id LIMIT 2";

        $result = $conn->prepare($sql_pictures);
        $result->bindValue(':id', $id, PDO::PARAM_STR);
        $result->execute();

        $pics = "";
        $num = 1;

        while($data = $result->fetch(PDO::FETCH_ASSOC)){
            $pics .= "<img class='pic-".$num."' src='/AssetPictures/".$data['PictureName']."'>";
            $num++;
        }

        return $pics;
    }

    $conn = establishConnection($host, $dbname, $user, $pass);

    $username = "";
    session_start();

    if(isset($_SESSION["UserID"])){
        $loggedIn = true;
        $id = $_SESSION["UserID"];
        
        $sql = "SELECT Username FROM Users WHERE ID = :i LIMIT 1";
        $result = $conn->prepare($sql);
        
        $result->bindValue(':i', $id, PDO::PARAM_STR);
        $result->execute();
        
        $name = "";

        while($data = $result->fetch(PDO::FETCH_ASSOC)){
            $name = $data['Username'];
        }
        $username = $name;
    }

    $defaultHomePage = '
        <div class="loginbox">
            <img alt="" src="WebsitePictures/avatar.png" class="avatar">
            <h1>Sign in</h1>
            <form action="PHP/login.php" method="POST">
                <p>Username</p>
                <input type="text" name="username" placeholder="Enter Username"></input>
                <p>Password</p>
                <input type="password" name="password" placeholder="********"></input>
                <input type="submit" href="PHP/login.php" value="Login"></input>
                <br/> 
                <a href="PHP/createAcc.php">Create a new account</a>
            </form>
        </div>';

    $loggedInHomePage = "".getHeader(0)."
        <h1>You are logged in as ".$username." </h1>
        <div class='container'>
            <h3 class='h3'>Products </h3>
            <div class='row'>
                ".getAssets($conn)."
            </div>
        </div>
        <hr>";

    /*$loggedInHomePage = '<h1>You are logged in as '.$username.' </h1>
    <a href="PHP/Admin/handleUsers.php"> See all users </a><br>
    <a href="PHP/Admin/newAsset.php"> Add new asset </a><br>
    <a href="PHP/logout.php"> Log out </a><br>' . getAssets($conn);*/

    "<a href='PHP/Admin/handleUsers.php'> See all users </a><br>
    <a href='PHP/Admin/newAsset.php'> Add new asset </a><br>
    <a href='PHP/shoppingCart.php'> Shopping cart </a><br>
    <a href='PHP/currentOrders.php'> All current orders </a><br>
    <a href='PHP/logout.php'> Log out </a><br>"

    /*<div class='aa1'>
            <div class='smenu'>
                <ul>
                    <li>
                        <div id='sidebar'>   
                            <div class='myFunction' onclick='myFunction(this)'>
                                <div class='bar1'></div>
                                <div class='bar2'></div>
                                <div class='bar3'></div>
                            </div>
                        </div>
                        <script src='SAMBUNG.javascript.js'></script>
                        <script src='https://code.jquery.com/jquery-3.3.1.js'
                            integrity='sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60='
                            crossorigin='anonymous'></script>
                            <!-- script för hamburge meny-->
                        <script>
                            $(document).ready(function(){
                                $('#sidebar').click(function(){
                                $('.varför').toggleClass('toggle')
                                $('.TEST123').toggleClass('toggle1')
                                $('.test1').toggleClass('toggle2')
                                $('.la').toggleClass('toggle3')
                                $('.top').toggleClass('toggle4')
                                $('.myFunction').toggleClass('toggle5')
                                $('.ol').toggleClass('toggle6')
                                $('.smenu').toggleClass('toggle7')
                                $('body').toggleClass('toggle8')
                                $('.offers').toggleClass('toggle9')
                                $('.ssmenu').toggleClass('toggle10')
                                $('.pla').toggleClass('toggle11')
                                $('.footerbtw').toggleClass('toggle12')
                                $('.INDEX').toggleClass('toggle13')
                                $('.footers').toggleClass('toggle14')
                                $('.container').toggleClass('toggle15')
                                })
                            })    
                        </script>
                    </li>
                    <li>
                        <a href='#' class='slogo'>
                            <img src='https://cdn.discordapp.com/attachments/424262454915104771/526526923904647180/sambung.png' style='width: 80%' alt=''>
                        </a>
                    </li>
                    <li>
                        <a class='ol' href='#'>
                            <i class='fa fa-shopping-basket'></i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class='la'></div>
            <nav class='menu'>
                <ul>
                    <li>
                        <a href='#'>
                            <img src='https://cdn.discordapp.com/attachments/424262454915104771/526526923904647180/sambung.png' class='SAMBUNGlogo' alt=''>
                        </a>
                    </li>
                    <li>
                        <a href='PHP/Admin/handleUsers.php' class='menu-item'>Handle Users</a>
                    </li>
                    <li>
                        <a href='PHP/Admin/newAsset.php' class='menu-item'>Add New Asset</a>
                    </li>
                    <li>
                        <a href='PHP/currentOrders.php' class='menu-item'>Current Orders</a>
                    </li>
                    <li>
                        <a href='#' class='menu-item'>About Us</a>
                    </li>
                    <li>
                        <a href='PHP/logout.php' class='menu-item'>Log Out</a>
                    </li>
                    <li>
                        <a id='search'>
                            <i class='fa fa-search'></i>
                        </a>
                    </li>
                    <li>
                        <a href='PHP/shoppingCart.php'>
                            <i class='fa fa-shopping-basket'></i>
                        </a>
                    </li>
                    <li>
                        <a id='kakan' href='#' class='menu-item'>
                            <img src='https://media.discordapp.net/attachments/486549868723175426/511696515895656450/depositphotos_119671422-stock-illustration-human-man-avatar-person-profile_1.jpg' id='lola' alt=''>
                        </a>
                    </li>
                </ul>
                <div class='search-form'>
                    <form>
                        <input type='text' name='search' placeholder='Search' id='lalala'>
                    </form>
                </div>
                    <a class='stäng' style='margin-right: 1%;margin-top: 13px;'><i class='fa fa-times'></i></a>
            </nav>
        </div>
        <hr class='juu' style='clear:both; margin-bottom: 1px; margin-top:3px;'/>
        <script src='https://code.jquery.com/jquery-3.3.1.js'
    integrity='sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=' crossorigin='anonymous'></script>
        <!-- for big screen search -->
        <script>
        $(document).ready(function(){
            $('#search').click(function(){
                $('#search').addClass('hide-item')
                $('.menu-item').addClass('hide-item')
                $('.search-form').addClass('active')
                $('.stäng').addClass('active')
            })
            $('.stäng').click(function(){
                $('.menu-item').removeClass('hide-item')
                $('.search-form').removeClass('active')
                $('.stäng').removeClass('active')
                $('#search').removeClass('hide-item')
            })
        })    
        </script>
  
        <!---  Hamburger menu items -->
        <div class='varför'>
            <div class='q'>
                <form>
                    <input type='text' name='search2' placeholder='Search...'>
                </form>
            </div>
            <ul>
                <li><div class='split4'></div></li>
                <li class='q2'><a href='PHP/Admin/handleUsers.php'>Handle Users</a></li>
                <li><div class='split5'></div></li>
                <li><a href='PHP/currentOrders.php'>Current Orders</a></li>
                <li><div class='split6'></div></li>
                <li><a href='#'>About Us</a></li>
                <li><div class='split7'></div></li>
                <li><a href='PHP/Admin/newAsset.php'>Add New Asset</a></li>
                <li><div class='split8'></div></li>
                <li><a href='PHP/logout.php'>Log Out</a></li>
                <li><div class='split9'></div></li>
            </ul>
        </div>*/
?>