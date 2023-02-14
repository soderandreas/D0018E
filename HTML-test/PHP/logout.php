<?php
    session_start();

    if(isset($_SESSION["UserID"])){
        $_SESSION = array();
	    session_destroy();
        header("Location:../index.php");
    } else {
        echo "Not logged in!";
    }

?>