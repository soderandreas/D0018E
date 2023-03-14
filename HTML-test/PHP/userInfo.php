<?php
    include "secret.php";
    include "functions.php";

    $conn = establishConnection($host, $dbname, $user, $pass);

    session_start();

    if(!isset($_SESSION["UserID"])){
        header("Location: ../index.php?err=5");
        exit();
    }

    $sql_allInfo = "SELECT * FROM Users WHERE ID = :id LIMIT 1";

    $result = $conn->prepare($sql_allInfo);

    $result->bindValue(':id', $_SESSION["UserID"], PDO::PARAM_STR);
    $result->execute();

    $data = $result->fetch(PDO::FETCH_ASSOC);

    if($_GET['info'] == 1){
        $notification = notification("Your password has been changed", 1);
    } else if ($_GET['info'] == 2){
        $notification = notification("Your personal information has been changed", 1);
    }
?>

<html>
    <head>
        <meta charset="utf-8">
        <title> User information: <?php echo $data['Username']; ?></title>
	    <link type="text/css" rel="Stylesheet" href="../style.css" />
        <link rel='stylesheet' type='text/css' href='../products.css'>
        <script src=”https://code.jquery.com/jquery-3.6.0.min.js” integrity=”sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=” crossorigin=”anonymous”></script>
        <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js'></script>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
        <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css' integrity='sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T' crossorigin='anonymous'>
        <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js' integrity='sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM' crossorigin='anonymous'></script>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js' integrity='sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1' crossorigin='anonymous'></script>
        <link href='https://raw.githubusercontent.com/daneden/animate.css/master/animate.css' rel='stylesheet'>
    </head>
    <body>
        <?php
            echo getHeader(5);
            echo $notification
        ?>
        <div>
            <h4>Username: <?php echo $data['Username'] ?></h4>
            <h4>First Name: <?php echo $data['FName'] ?></h4>
            <h4>Last Name: <?php echo $data['LName'] ?></h4>
            <h4>Mail: <?php echo $data['Mail'] ?></h4>
            <h4>Gender: <?php if($data['Gender'] == 'M'){
                                echo "Male";
                            } else if($data['Gender'] == 'F'){
                                echo "Female";
                            } ?></h4>
            <h4>Phone Number: 0<?php echo $data['PhoneNum'] ?></h4>
            <h4>Age: <?php echo $data['Age'] ?></h4>
        </div>
        <div>
            <a href='userInfoEdit.php'><h5>Edit personal information</h5></a>
            <a href='userInfoEditPass.php'><h5>Change password</h5></a>
        </div>
    </body>
</html>