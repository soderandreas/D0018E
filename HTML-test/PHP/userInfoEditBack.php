<?php
    include "secret.php";
    include "functions.php";

    $conn = establishConnection($host, $dbname, $user, $pass);

    session_start();

    if(!isset($_SESSION["UserID"])){
        header("Location: ../index.php?err=5");
        exit();
    }

    if($_POST['username'] != null && $_POST['fName'] != null && $_POST['lName'] != null && $_POST['age'] != null && $_POST['phone'] != null && $_POST['gender'] != null && $_POST['email'] != null){
        $unsafeUsername = $_POST['username'];
        $unsafeFName = $_POST['fName'];
        $unsafeLName = $_POST['lName'];
        $unsafeAge = $_POST['age'];
        $unsafePhone = $_POST['phone'];
        $unsafeGender = $_POST['gender'];
        $unsafeEmail = $_POST['email'];

        $safeUsername = filter_var($unsafeUsername, FILTER_SANITIZE_STRING);
        $safeFName = filter_var($unsafeFName, FILTER_SANITIZE_STRING);
        $safeLName = filter_var($unsafeLName, FILTER_SANITIZE_STRING);
        $safeAge = filter_var($unsafeAge, FILTER_SANITIZE_STRING);
        $safePhone = filter_var($unsafePhone, FILTER_SANITIZE_STRING);
        $safeGender = filter_var($unsafeGender, FILTER_SANITIZE_STRING);
        $safeEmail = filter_var($unsafeEmail, FILTER_SANITIZE_STRING);

        if($safeAge < 13 || $safeAge > 120){
            header("Location: userInfoEdit.php?err=2");
            exit();
        }

        $sql_edit = "UPDATE Users SET Username = :u, FName = :fn, LName = :ln, Mail = :m, Gender = :g, PhoneNum = :pn, Age = :a WHERE ID = :id";

        $result = $conn->prepare($sql_edit);

        $result->bindValue(':u', $safeUsername, PDO::PARAM_STR);
        $result->bindValue(':fn', $safeFName, PDO::PARAM_STR);
        $result->bindValue(':ln', $safeLName, PDO::PARAM_STR);
        $result->bindValue(':m', $safeEmail, PDO::PARAM_STR);
        $result->bindValue(':g', $safeGender, PDO::PARAM_STR);
        $result->bindValue(':pn', $safePhone, PDO::PARAM_STR);
        $result->bindValue(':a', $safeAge, PDO::PARAM_STR);
        $result->bindValue(':id', $_SESSION['UserID'], PDO::PARAM_STR);

        $result->execute();

        header("Location: userInfo.php?info=2");
    } else {
        header("Location: userInfoEdit.php?err=1");
    }
?>