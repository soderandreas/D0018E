<?php
    include "../secret.php";
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

    session_start();

    if(isset($_SESSION["UserID"])){
        $loggedIn = true;
        $id = $_SESSION["UserID"];

        $sql = "SELECT Username, Type FROM Users INNER JOIN UserType WHERE Users.ID = UserType.ID AND Users.ID = :i LIMIT 1";

        $result = $conn->prepare($sql);

        $result->bindValue(':i', $id, PDO::PARAM_STR);
		$result->execute();

        $name = "";
        $type = "";

        while($data = $result->fetch(PDO::FETCH_ASSOC)){//Tar ut användarens namn, lösenord och id från talbellen. Kollar även om användaren är admin
			$name = $data['Username'];
            $type = $data['Type'];
		}

        $username = $name;
        $usertype = $type;
    }
?>

<html>
    <head>
        <title>
            All users
        </title>
        <meta charset="UTF-8" />
        <link type="text/css" rel="Stylesheet" href="../../style.css" />
        <script type="text/Javascript" src="../../javaScript.js"></script>
    </head>
    <body>
        <div>
            <h3><a href="../../index.php">Go Back</a></h3>
            <h1> All normal customers </h1>
            <?php
                if ($usertype != 2 && $usertype != 3){
                    header("Location: ../../index.php");
                } else {
                    $sql_users1test = "SELECT COUNT(*) AS Total FROM BanUser";
                    $result2test = $conn->prepare($sql_users1test);
                    $result2test->execute();
                    $count;

                    while($data2test = $result2test->fetch(PDO::FETCH_ASSOC)){ // get number of banned users
                        $count = $data2test["Total"];
                    }

                    if($count == 0){
                        $sql_users1 = 
                            "SELECT Users.ID, Username, Password FROM Users WHERE (SELECT COUNT(*) FROM BanUser) = 0 AND Users.ID != :i AND ID NOT IN (SELECT ID FROM UserType WHERE Type = 2 OR Type = 3)";
                    } else {
                        $sql_users1 = 
                            "SELECT ID, Username, Password FROM Users WHERE ID NOT IN (SELECT DISTINCT CustomerID FROM BanUser) AND ID != :i AND ID NOT IN (SELECT ID FROM UserType WHERE Type = 2 OR Type = 3)";
                    }

                    $result2 = $conn->prepare($sql_users1);

                    $result2->bindValue(':i', $id, PDO::PARAM_STR);
                    $result2->execute();

                    $sql_users2 = 
                        "SELECT Users.ID, Username, Password FROM Users INNER JOIN BanUser WHERE Users.ID != :i and Users.ID = customerID";
                    $result3 = $conn->prepare($sql_users2);

                    $result3->bindValue(':i', $id, PDO::PARAM_STR);
                    $result3->execute();
                    echo "<table>";
                    while($data2 = $result2->fetch(PDO::FETCH_ASSOC)){ // Fetch data for users
                        if ($usertype == 2){
                            echo "
                                <tr>
                                    <td>id: ". $data2["ID"]. "</td> 
                                    <td>username: ". $data2["Username"] . "</td>
                                    <td><a href=banUser.php?id=".$data2["ID"].">Ban this user</a></td>
                                </tr>";
                        } else if ($usertype == 3){
                            echo "
                                <tr>
                                    <td>id: ". $data2["ID"]. "</td> 
                                    <td>username: ". $data2["Username"] . "</td>
                                    <td>password: ". $data2["Password"] . "</td>
                                    <td><a href=banUser.php?id=".$data2["ID"].">Ban this user</a></td>
                                    <td><a href=makeAdmin.php?id=".$data2["ID"].">Make this user into an admin</a></td>
                                </tr>";
                        }
                    }
                    echo "</table>";
                    echo "<table>";
                    echo "<h1> Banned Customers </h1>";
                    while($data3 = $result3->fetch(PDO::FETCH_ASSOC)){ // Fetch data for banned users
                        if ($usertype == 2){
                            echo "
                                <tr>
                                    <td>id: ". $data3["ID"]. "</td> 
                                    <td>username: ". $data3["Username"] . "</td>
                                    <td><a href=unbanUser.php?id=".$data3["ID"].">Unban this user</a></td>
                                </tr>";
                        } else if ($usertype == 3) {
                            echo "
                                <tr>
                                    <td>id: ". $data3["ID"]. "</td> 
                                    <td>username: ". $data3["Username"] . "</td>
                                    <td>password: ". $data3["Password"] . "</td>
                                    <td><a href=unbanUser.php?id=".$data3["ID"].">Unban this user</a></td>
                                </tr>";
                        }
                    }
                    echo "</table>";
                    if($usertype == 3){
                        $sql_admins = "SELECT Users.ID, Username, Password FROM Users WHERE ID IN (SELECT ID FROM UserType WHERE Type = 2)";
                        $result4 = $conn->prepare($sql_admins);
                        $result4->execute();
                        echo "<h1> All Normal Admins </h1>";
                        echo "<table>";
                        while($data4 = $result4->fetch(PDO::FETCH_ASSOC)){
                            echo "
                                    <tr>
                                        <td>id: ". $data4["ID"]. "</td> 
                                        <td>username: ". $data4["Username"] . "</td>
                                        <td>password: ". $data4["Password"] . "</td>
                                        <td><a href=removeAdmin.php?id=".$data4["ID"].">Remove admin privileges</a></td>
                                    </tr>";
                        }
                        echo "</table>";
                    }
                }
            ?>
        </div>
    </body>
</html>