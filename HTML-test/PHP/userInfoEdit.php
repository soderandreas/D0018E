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
?>

<html>
    <head>
        <meta charset="utf-8">
        <title> Edit user information </title>
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
        ?>
        <div>
            <form action="userInfoEditBack.php" method="POST">
                <label>Username</label>
                <input type="text" placeholder="Enter Username" name="username" id="username" value="<?php echo $data['Username'] ?>" required><br>
                <label>Name</label>
                <input type="text" placeholder="Enter Name" name="fName" id="fName" value="<?php echo $data['FName'] ?>" required><br>
                <label>Surname</label>
                <input type="text" placeholder="Enter Surname" name="lName" id="lName" value="<?php echo $data['LName'] ?>" required><br>
                <label>Age</label>
                <input type="Age" id="age" name="age" placeholder="Enter Age" value="<?php echo $data['Age'] ?>" required><br>
                <label>Phone number</label>
                <input type="tel" placeholder="070xxxxxxx" name="phone" id="phone" value="0<?php echo $data['PhoneNum'] ?>" required><br>
                <label>Gender</label>
                <select name="gender" class="gender-input" required>
                    <option value="" selected>Gender</option>
                    <option value="M">Male</option>
                    <option value="F">Female</option>
                </select><br>
                <label>Email</label>
                <input type="Email" placeholder="Enter Email" name="email" id="email" value="<?php echo $data['Mail'] ?>" required><br>
                <button type="submit" href="userInfoEditBack.php">Submit</button>
            </form>
        </div>
    </body>
</html>