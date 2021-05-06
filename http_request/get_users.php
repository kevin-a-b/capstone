<?php
//error_reporting(E_ALL);
//ini_set("display_errors","On");

include("../classes/config.php");
$Config = new Config();

$Users = new MainData();
$Users->GetUsers();

$json_users = [];

while($Users->next_record()){
    array_push($json_users, array('id' => $Users->f('Account_ID'), 'user_name' => $Users->f('Account_Username')));
}

http_response_code(202);    // accepted

echo json_encode(
    array(
        "users" => $json_users
    )
);
