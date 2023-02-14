<?php
    include "secret.php";
    include "functions.php";

    $conn = establishConnection($host, $dbname, $user, $pass);

    if(isset($_POST['username']) && isset($_POST['password']) /*&& isset($_POST['password-repeat'])*/ && isset($_POST['email']) && isset($_POST['fName']) && isset($_POST['lName']) && isset($_POST['gender']) && isset($_POST['phone']) && isset($_POST['age'])){
        /*if($_POST['password'] != $_POST['password-repeat']){
            header("Location: createAcc.php?err=1");
        }*/

        $unsafeUsername = $_POST['username'];
        $unsafePassword = $_POST['password'];
        $unsafeEmail    = $_POST['email'];
        $unsafeFname    = $_POST['fName'];
        $unsafeLname    = $_POST['lName'];
        $unsafeGender   = $_POST['gender'];
        $unsafePhone    = $_POST['phone']; 
        $unsafeAge      = $_POST['age'];

        #echo $unsafeUsername, " ", $unsafePassword, " ", $unsafeEmail, " ", $unsafeFname, " ", $unsafeLname, " ", $unsafeGender, " ", $unsafePhone, " ", $unsafeAge;

        $safeUsername = filter_var($unsafeUsername, FILTER_SANITIZE_STRING);
        $safePassword = filter_var($unsafePassword, FILTER_SANITIZE_STRING);
        $safeEmail    = filter_var($unsafeEmail, FILTER_SANITIZE_STRING);
        $safeFname    = filter_var($unsafeFname, FILTER_SANITIZE_STRING);
        $safeLname    = filter_var($unsafeLname, FILTER_SANITIZE_STRING);
        $safeGender   = filter_var($unsafeGender, FILTER_SANITIZE_STRING);
        $safePhone    = filter_var($unsafePhone, FILTER_SANITIZE_STRING);
        $safeAge      = filter_var($unsafeAge, FILTER_SANITIZE_STRING);

        #echo "test", $safeUsername, " ", $safePassword, " ", $safeEmail, " ", $safeFname, " ", $safeLname, " ", $safeGender, " ", $safePhone, " ", $safeAge;

        $sql_query = "INSERT INTO Users(Username, Password, FName, LName, Mail, Gender, PhoneNum, Age) VALUES (:u, :pw, :f, :l, :m, :g, :p, :a)";

		$result = $conn->prepare($sql_query);

        $result->bindValue(':u', $safeUsername, PDO::PARAM_STR);
        $result->bindValue(':pw', $safePassword, PDO::PARAM_STR);
        $result->bindValue(':f', $safeFname, PDO::PARAM_STR);
        $result->bindValue(':l', $safeLname, PDO::PARAM_STR);
        $result->bindValue(':m', $safeEmail, PDO::PARAM_STR);
        $result->bindValue(':g', $safeGender, PDO::PARAM_STR);
        $result->bindValue(':p', $safePhone, PDO::PARAM_STR);
        $result->bindValue(':a', $safeAge, PDO::PARAM_STR);

        $result->execute();

        $sql_get_id = "SELECT ID FROM Users WHERE Username = :name AND Password = :pass";		
        $result2 = $conn->prepare($sql_get_id);

        $result2->bindValue(':name', $safeUsername, PDO::PARAM_STR);
        $result2->bindValue(':pass', $safePassword, PDO::PARAM_STR);
        
        $result2->execute();

        while($data = $result2->fetch(PDO::FETCH_ASSOC)){
            $id = $data['ID'];
        }

        $sql_query2 = "INSERT INTO UserType(ID, Type) VALUES (:id, 1)";
        $result3 = $conn->prepare($sql_query2);

        $result3->bindValue(':id', $id, PDO::PARAM_STR);

        $result3->execute();

        $conn = null;

		header("Location: ../index.php");

    } else {
        echo "ERROR";
    }
?>