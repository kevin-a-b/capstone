<?php

include("../classes/config.php");
$Config = new Config();

$data = json_decode(file_get_contents('php://input'), true);

if(isset($data['Account_ID']) && isset($data['Conversation_ID'])
    && isset($data['StartingMessageNumberInclusive'])){
    $account_id = $data['Account_ID'];
    $conv_id = $data['Conversation_ID'];
    $start_num_inclusive = $data['StartingMessageNumberInclusive'];
}else{
    http_response_code(400); // Bad Request
    return;
}

$Messages = new MainData();
$Messages->GetNewMessages($conv_id, $start_num_inclusive);

$messages_array = array();
while($Messages->next_record()){
    $m = $Messages->f('message_cipher');
    $sender_username = $Messages->f('sender_username');
    $date_time = $Messages->f('date_time');
    $m_num = $Messages->f('message_num');
    array_push($messages_array, array(
        'Message_Number' => $m_num,
        'MessageSenderUsername' => $sender_username,
        'TimeAndDateMessageWasSent' => $date_time,
        'MessageBody' => $m
    ));
}

http_response_code(200); // OK

echo json_encode(
    array(
        'TaskRequested' => 'LOAD_ANY_NEW_MESSAGES',
        'ResultOfRequest' => 'SUCCESS',
        'Messages' => $messages_array
    ), JSON_UNESCAPED_SLASHES
);

return;