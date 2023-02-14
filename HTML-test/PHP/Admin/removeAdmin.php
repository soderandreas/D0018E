<?php
    include "../secret.php";
    include "../functions.php";
    
    $conn = establishConnection($host, $dbname, $user, $pass);

    $usertype = checkUserType($conn);

    session_start();
    $adminID = $_GET["id"];

    if ($usertype == 3){
        $sql2 = "UPDATE UserType SET Type = 1 WHERE ID = :i";

        $result2 = $conn->prepare($sql2);

        $result2->bindValue(':i', $adminID, PDO::PARAM_STR);
        $result2->execute();

        header("Location: handleUsers.php");
    } else {
        header("Location: ../../index.php");
    }
?>