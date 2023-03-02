<?php
    include "secret.php";
    include "functions.php";

    $conn = establishConnection($host, $dbname, $user, $pass);

    session_start();

    if(!isset($_SESSION["UserID"])){
        header("Location: ../index.php?err=5");
        exit();
    }

    if(isset($_POST['oldPassword']) && isset($_POST['newPassword']) && isset($_POST['newPasswordAgain'])) {
        echo $_POST['oldPassword'], $_POST['newPassword'], $_POST['newPasswordAgain'], "\n";
        echo "works";

        // Make sure old password matches
        $sql_oldpass = "SELECT Password FROM Users WHERE ID = :id";
        
        $result = $conn->prepare($sql_oldpass);

        $result->bindValue(':id', $_SESSION["UserID"], PDO::PARAM_STR);

        $result->execute();
        echo "test";

        $data = $result->fetch(PDO::FETCH_ASSOC);

        if($data['Password'] != $_POST['oldPassword']){ // if they dont match, exit with error
            header("Location: userInfoEditPass.php?err=2");
            exit();
        }

        // Make sure new password matches
        if($_POST['newPassword'] == $_POST['newPasswordAgain']){
            $sql_changePass = "UPDATE Users SET Password = :p WHERE ID = :id";

            $result = $conn->prepare($sql_changePass);

            $result->bindValue(':p', $_POST['newPassword'], PDO::PARAM_STR);
            $result->bindValue(':id', $_SESSION['UserID'], PDO::PARAM_STR);

            $result->execute();

            header("Location: userInfo.php?info=1");
        }
    } else {
        header("Location: userInfoEditPass.php?err=1");
    }
?>