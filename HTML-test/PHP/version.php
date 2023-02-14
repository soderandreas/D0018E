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
                                    <li><a href='#'><i class='fa fa-shopping-bag'></i></a></li>
                                    <li><a href='#'><i class='fa fa-shopping-cart'></i></a></li>
                                </ul>
                                <span class='product-new-label'>New</span>
                            </div>
                            <div class='product-content'>
                                <h3 class='title'><a href='#'>".$data['Name']."</a></h3>
                                <div class='price'>
                                    ".$data['Price']." $
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

    $loggedInHomePage = "
        <h1>You are logged in as ".$username." </h1>
        <a href='PHP/Admin/handleUsers.php'> See all users </a><br>
        <a href='PHP/Admin/newAsset.php'> Add new asset </a><br>
        <a href='PHP/shoppingCart.php'> Shopping cart </a><br>
        <a href='PHP/logout.php'> Log out </a><br>
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
?>