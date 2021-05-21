<?php

include("../classes/config.php");
$Config = new Config();

$data = json_decode(file_get_contents('php://input'), true);

if(isset($data['Account_ID']) && isset($data['Conversation_ID']) && isset($data['NumberOfMessagesToLoad'])){
    $account_id = $data['Account_ID'];
    $conversation_id = $data['Conversation_ID'];
    $num_messages = $data['NumberOfMessagesToLoad'];
}else{
    http_response_code(400); // Bad Request
    return;
}

$GetMessageCount = new MainData();
$GetMessageCount->GetCurrentMessageCount($conversation_id);
$message_count = $GetMessageCount->f('num_messages');

$message_start = $message_count - 25;

$Messages = new MainData();
$Messages->GetNewMessages($conversation_id, $message_start);

//$Messages = new MainData();
//$Messages->ViewMessages($conversation_id, $num_messages);

$messages = array();
while($Messages->next_record()){
    $num = $Messages->f('message_num');
    $sender = $Messages->f('sender_username');
    $datetime = $Messages->f('date_time');
    $message_ciphertext = $Messages->f('message_cipher');
    array_push($messages,
    array(
        'Message_Number' => $num,
        'MessageSenderUsername' => $sender,
        'TimeAndDateMessageWasSent' => $datetime,
        'MessageBody' => $message_ciphertext
    ));
}

$ConversationPrivateKey = new MainData();
$ConversationPrivateKey->GetConversationPrivateKey($conversation_id, $account_id);
$conv_private_key = $ConversationPrivateKey->f('Conversation_Private_Key');

http_response_code(200); // OK

echo json_encode(
    array(
        'TaskRequested' => 'LOAD_MOST_RECENT_MESSAGES_AND_PRIVATE_KEY',
        'ResultOfRequest' => 'SUCCESS',
        'Account_ID' => $account_id,
        'Messages' => $messages,
        'Conversation_Private_Key' => $conv_private_key
    ), JSON_UNESCAPED_SLASHES
);

return;