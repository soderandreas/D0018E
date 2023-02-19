<?php

    function establishConnection($host, $dbname, $user, $pass){
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    function checkUserType($conn){
        $usertype = 0;

        session_start();

        if(isset($_SESSION["UserID"])){
            $id = $_SESSION["UserID"];

            $sql = "SELECT Type FROM UserType WHERE ID = :i LIMIT 1";

            $result = $conn->prepare($sql);

            $result->bindValue(':i', $id, PDO::PARAM_STR);
            $result->execute();

            $type = "";

            while($data = $result->fetch(PDO::FETCH_ASSOC)){
                $type = $data['Type'];
            }

            $usertype = $type;
        }

        return $usertype;
    }

/*-----Functions for handeling headers-----*/

    // Generates the header for desktop page
    function deskHeader($title, $url, $page){
        $output = "";

        for($i = 0; $i < count($title[$page]); $i++){
            $output .= "
            <li>
                <a href='".$url[$page][$i]."' class='menu-item'>".$title[$page][$i]."</a>
            </li>";
        }

        $output .= "
        <li>
            <a href='".$url[$page][$i]."' class='menu-item'>Log Out</a>
        </li>
        <li>
            <a id='search'>
                <i class='fa fa-search'></i>
            </a>
        </li>
        <li>
            <a href='".$url[$page][$i+1]."'>
                <i class='fa fa-shopping-basket'></i>
            </a>
        </li>";
        return $output;
    }

    // Generates the header for mobile page
    function mobileHeader($title, $url, $page){
        return 0;
    }

    // Handles the header for different pages
    function getHeader($page){
        // 0 = homepage, 1 = Handle Users, 2 = Add New Asset, 3 = Current Orders, 4 = About Us, 5 = Order, Product or Shopping Cart
        $whatPage = [["Handle Users","Add New Asset","Current Orders", "About Us"],
                     ["Products","Add New Asset","Current Orders","About Us"], 
                     ["Products","Handle Users","Current Orders","About Us"], 
                     ["Products","Handle Users","Add New Asset","About Us"], 
                     ["Products","Handle Users", "Add New Asset", "Current Orders"],
                     ["Products", "Handle Users", "Add New Asset", "Current Orders", "About Us"]];

        $urlPage = [["PHP/Admin/handleUsers.php", "PHP/Admin/newAsset.php", "PHP/currentOrders.php", "#", "PHP/logout.php", "PHP/shoppingCart.php"],
                    ["../../index.php", "newAsset.php", "../currentOrders.php", "#", "../logout.php", "../shoppingCart.php"],
                    ["../../index.php", "handleUsers.php", "../currentOrders.php", "#", "../logout.php", "../shoppingCart.php"],
                    ["../index.php", "Admin/handleUsers.php", "Admin/newAsset.php", "#", "logout.php", "shoppingCart.php"],
                    ["", "", "", ""],
                    ["../index.php", "Admin/handleUsers.php", "Admin/newAsset.php", "currentOrders.php", "#", "logout.php", "shoppingCart.php"]];

        $output = "
        <div class='aa1'>
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
                    ". deskHeader($whatPage, $urlPage, $page) ."
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
                <li class='q2'><a href='".$urlPage[$page][0]."'>".$whatPage[$page][0]."</a></li>
                <li><div class='split5'></div></li>
                <li><a href='".$urlPage[$page][1]."'>".$whatPage[$page][1]."</a></li>
                <li><div class='split6'></div></li>
                <li><a href='".$urlPage[$page][2]."'>".$whatPage[$page][2]."</a></li>
                <li><div class='split7'></div></li>
                <li><a href='".$urlPage[$page][3]."'>".$whatPage[$page][3]."</a></li>
                <li><div class='split8'></div></li>
                <li><a href='".$urlPage[$page][4]."'>Log Out</a></li>
                <li><div class='split9'></div></li>
            </ul>
        </div>";
    
    return $output;
    }

?>