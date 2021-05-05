<?php

include("classes/config.php");
$Config = new Config();

$Users = new MainData();
$Users->GetUsers();

//$json_users = [];

//while($Users->next_record()){
//    array_push($json_users, array('id' => $Users->f('Account_ID'), 'user_name' => $Users->f('Account_Username')));
//}

http_response_code(202);    // OK

//echo json_encode(
//    array(
//        "users" => $json_users
//    )
//);

$arr = array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5);

echo json_encode($arr);


?>

<html>
    <body>
        <h1>Hello World</h1>
    </body>
</html>