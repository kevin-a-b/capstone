<?php

include("../classes/config.php");
$Config = new Config();

$data = json_decode(file_get_contents('php://input'), true);

if(isset($data['Sender_Account_ID']) && isset($data['Conversation_ID'])
    && isset($data['Sent_Date_And_Time']) && isset($data['Message_Ciphertext'])){
    $account_id = $data['Sender_Account_ID'];
    $conv_id = $data['Conversation_ID'];
    $date_time = $data['Sent_Date_And_Time'];
    $message_cipher = $data['Message_Ciphertext'];
}else{
    http_response_code(400); // Bad Request
    return;
}

$IncreaseMessageCount = new MainData();
$IncreaseMessageCount->IncreaseConversationMessageCount($conv_id);

$IncreaseMessageCount->GetCurrentMessageCount($conv_id);
$message_count = $IncreaseMessageCount->f('Total_Number_Of_Messages');

$InsertMessage = new MainData();
$InsertMessage->InsertNewMessage($message_count, $conv_id, $message_cipher, $account_id, $date_time);

http_response_code(200); // OK
return;