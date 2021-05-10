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

$Messages = new MainData();
$Messages->ViewMessages($conversation_id, $num_messages);

$messages = array();
while($Messages->next_record()){
    $sender = $Messages->f('Account_Username');
    $datetime = $Messages->f('Sent_Date_And_Time');
    $message_ciphertext = $Messages->f('Message_Ciphertext');
    array_push($messages,
    array(
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
    )
);

return;