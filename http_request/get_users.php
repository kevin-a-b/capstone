<?php
error_reporting(E_ALL);
ini_set("display_errors","On");

include("../classes/config.php");
$Config = new Config();

$Users = new MainData();
$Users->GetUsers();

$json_users = [];

while($Users->next_record()){
    array_push($json_users, array('id' => $Users->f('Account_ID'), 'user_name' => $Users->f('Account_Username')));
}

http_response_code(202);    // OK

echo json_encode(
    array(
        "users" => $json_users
    )
);

//$arr = array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5);
//
//echo json_encode($arr);


?>

<html lang="en">
    <body>
        <h1>get_users.php</h1>
    </body>
</html>