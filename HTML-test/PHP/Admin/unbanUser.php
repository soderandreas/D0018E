<?php
    include "../secret.php";
    include "../functions.php";

    $conn = establishConnection($host, $dbname, $user, $pass);

    $usertype = checkUserType($conn);

    if($usertype == 2 || $usertype == 3){ // If user is not an admin
        $sql2 = "DELETE FROM BanUser WHERE customerID = :i";
        $custID = $_GET["id"];

        $result2 = $conn->prepare($sql2);

        $result2->bindValue(':i', $custID, PDO::PARAM_STR);
        echo "test", $custID;
        $result2->execute();

        header("Location: handleUsers.php");
    } else {
        header("Location: ../../index.php");
    }
?>