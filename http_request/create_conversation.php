<?php

include("../classes/config.php");
$Config = new Config();

$data = json_decode(file_get_contents('php://input'), true);

if(isset($data['Account_ID']) && isset($data['Conversation_Private_Key'])){
    $account_id = $data['Account_ID'];
    $conv_private_key = $data['Conversation_Private_Key'];
}else{
    http_response_code(400); // Bad Request
    return;
}

// create conversation

$CreateConversation = new MainData();
$CreateConversation->CreateNewConversation();

$conversation_id = $CreateConversation->f('Conversation_ID');