<?php
    include "../secret.php";
    include "../functions.php";

    $conn = establishConnection($host, $dbname, $user, $pass);

    $usertype = checkUserType($conn);

    session_start();
    $id = $_SESSION["UserID"];
    $custID = $_GET["id"];

    if($usertype == 2 || $usertype == 3){ // If user is an admin
        $sql2 = "INSERT INTO BanUser(CustomerID, AdminID) VALUES (:ci, :ai)";

        $result2 = $conn->prepare($sql2);

        $result2->bindValue(':ci', $custID, PDO::PARAM_STR);
        $result2->bindValue(':ai', $id, PDO::PARAM_STR);
        $result2->execute();

        header("Location: handleUsers.php");
    } else {
        header("Location: ../../index.php");
    }
?>