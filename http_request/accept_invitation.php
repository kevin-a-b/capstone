<?php

include("../classes/config.php");
$Config = new Config();

$data = json_decode(file_get_contents('php://input'), true);

if(isset($data['Account_ID']) && isset($data['Conversation_ID'])){
    $account_id = $data['Account_ID'];
    $conv_id = $data['Conversation_ID'];
}else{
    http_response_code(400); // Bad Request
    return;
}

$AddConversationParticipant = new MainData();
$AddConversationParticipant->AddConversationParticipantWithoutPrivateKeyParameter($conv_id, $account_id);

$DeleteOldPendingInvitation = new MainData();
$DeleteOldPendingInvitation->DeleteOldPendingInvitation($conv_id, $account_id);

http_response_code(200); // OK
return;