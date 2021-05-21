<?php

include("../classes/config.php");
$Config = new Config();

$data = json_decode(file_get_contents('php://input'), true);

if(isset($data['Account_Username'])){
    $username = $data['Account_Username'];
}else{
    http_response_code(400); // Bad Request
    return;
}

$GetPublicKey = new MainData();
$GetPublicKey->GetPublicKeyByUsername($username);

if($GetPublicKey->num_rows() == 0){
    http_response_code(404); // Not Found
    echo json_encode(
        array(
            'TaskRequested' => 'LOOKUP_PUBLIC_KEY',
            'ResultOfRequest' => 'USERNAME_NOT_FOUND',
            'Account_Username' => $username,
            'Public_Key' => NULL
        ), JSON_UNESCAPED_SLASHES
    );
}else{
    $public_key = $GetPublicKey->f('Public_Key');

    http_response_code(200); // OK
    echo json_encode(
        array(
            'TaskRequested' => 'LOOKUP_PUBLIC_KEY',
            'ResultOfRequest' => 'SUCCESS',
            'Account_Username' => $username,
            'Public_Key' => $public_key
        ), JSON_UNESCAPED_SLASHES
    );
}
return;