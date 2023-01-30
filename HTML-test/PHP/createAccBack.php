<?php
    include "secret.php";
    $conn;
	$mess = "ok";
	try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        #echo "Connected successfully";
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }

    if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['password-repeat']) && isset($_POST['email']) && isset($_POST['fName']) && isset($_POST['lName']) && isset($_POST['gender']) && isset($_POST['phone']) && isset($_POST['age'])){
        if($_POST['password'] != $_POST['password-repeat']){
            header("Location: createAcc.php?err=1");
        }

        $unsafeUsername = $_POST['username'];
        $unsafePassword = $_POST['password'];
        $unsafeEmail    = $_POST['email'];
        $unsafeFname    = $_POST['fName'];
        $unsafeLname    = $_POST['lName'];
        $unsafeGender   = $_POST['gender'];
        $unsafePhone    = $_POST['phone']; 
        $unsafeAge      = $_POST['age'];

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

        $conn = null;

		header("Location: ../index.php");

    } else {
        echo "ERROR";
    }
?>