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
$CreateConversation->LastInsertID();
$conversation_id = $CreateConversation->f('LAST_INSERT_ID()');

$CreateConversationParticipant = new MainData();
$CreateConversationParticipant->AddConversationParticipant($conversation_id, $account_id, $conv_private_key);

http_response_code(201); // Created

json_encode(
    array(
        'TaskRequested' => 'CREATE_NEW_CONVERSATION',
        'ResultOfRequest' => 'CONVERSATION_CREATION_SUCCESSFUL',
        'Account_ID' => $account_id,
        'Conversation_Private_Key' => $conv_private_key,
        'Conversation_ID' => $conversation_id
    )
);

return;