<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body>
    <?php
        $servername = "localhost";
        $database = "sambot";
        $username = "root";
        $password = "root";

    try {

        $connection = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $user_code = $_GET["code"];

        $query_verification = $connection->prepare("SELECT * FROM tokens WHERE code = '$user_code'");
        $query_verification->execute();
        $row = $query_verification->fetch(PDO::FETCH_ASSOC);

        $message =  "Copy and past the following code to continue : ".$user_code;

        if(!$row) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"https://www.eventbrite.com/oauth/token");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "code=".$user_code."&client_secret=".QVP5XY3LVVNAALAKYCMBYHRKBPVHBWAR2BPXEPTD63N6GIA7CN."&client_id=".WLLFMI5NGFKQ755TDH."&grant_type=authorization_code");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close ($ch);
            $user_token = json_decode($result, true)["access_token"];
            $query_insert_code = $connection->prepare( "INSERT INTO tokens (code, hash) VALUES ('".$user_code."','".$user_token."')");
            if ($query_insert_code->execute()) {
                echo $message;
            } else{
                echo "Sorry an error occured, please try again";
            }
        } else {
            echo $message;
        }
    }
    catch(PDOException $e)
    {
        echo "Connection failed: " . $e->getMessage();
    }
    ?>
</body>
</html>