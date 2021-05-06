<?php

include("../classes/config.php");
$Config = new Config();

$data = json_decode(file_get_contents('php://input'), true);

var_dump($data);

if(isset($data['Account_Username']) && isset($data['Account_Password_Hashcode'])
    && isset($data['Public_Key']) && isset($data['Private_Key'])){
    $username = $data['Account_Username'];
    $password_hash = $data['Account_Password_Hashcode'];
    $public_key = $data['Public_Key'];
    $private_key = $data['Private_Key'];
}else{
    http_response_code(400); // Bad Request
    return;
}

$CreateUser = new MainData();
$CreateUser->CheckUsernameExists($username);
if($CreateUser->num_rows() == 0){
    $CreateUser->CreateNewUserAccount($username, $password_hash, $public_key, $private_key);
}else{
    http_response_code(409); // Conflict
    return;
}

$arr = array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5);
echo json_encode($arr);

http_response_code(201); //Accepted
return;