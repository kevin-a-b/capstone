<?php

include("../classes/config.php");
$Config = new Config();

$data = json_decode(file_get_contents('php://input'), true);

if(isset($data['Recipient_Account_Username']) && isset($data['Sender_Account_ID'])
    && isset($data['Conversation_ID']) && isset($data['Conversation_Private_Key'])){
    $recipient_username = $data['Recipient_Account_Username'];
    $sender_account_id = $data['Sender_Account_ID'];
    $conversation_id = $data['Conversation_ID'];
    $conversation_private_key = $data['Conversation_Private_Key'];
}else{
    http_response_code(400); // Bad Request
    return;
}

$CheckParticipant = new MainData();
$CheckParticipant->CheckIfConversationParticipantExists($conversation_id, $recipient_username);

if($CheckParticipant->num_rows() > 0){
    http_response_code(409); // Conflict
    return;
}

$InviteParticipant = new MainData();
$InviteParticipant->InsertConversationInvitation($conversation_id, $recipient_username, $sender_account_id, $conversation_private_key);

http_response_code(200);  // OK
return;