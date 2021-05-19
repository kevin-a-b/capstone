<?php

include("../classes/config.php");
$Config = new Config();

$data = json_decode(file_get_contents('php://input'), true);

if(isset($data['Account_ID']) && isset($data['Conversation_ID'])
    && isset($data['StartingMessageNumberInclusive']) && isset($data['EndingMessageNumberInclusive'])){
    $account_id = $data['Account_ID'];
    $conv_id = $data['Conversation_ID'];
    $start_num_inclusive = $data['StartingMessageNumberInclusive'];
    $end_num_inclusive = $data['EndingMessageNumberInclusive'];
}else{
    http_response_code(400); // Bad Request
    return;
}

$Messages = new MainData();
$Messages->GetMessages($conv_id, $start_num_inclusive, $end_num_inclusive);

$messages_array = array();
while($Messages->next_record()){
    $m = $Messages->f('message_cipher');
    $sender_username = $Messages->f('sender_username');
    $date_time = $Messages->f('date_time');
    array_push($messages,
                                array(
                                    'MessageSenderUsername' => $sender_username,
                                    'TimeAndDateMessageWasSent' => $date_time,
                                    'MessageBody' => $m
                                ));
}

http_response_code(200); // OK

echo json_encode(
    array(
        'TaskRequested' => 'LOAD_SPECIFIED_MESSAGE_RANGE',
        'ResultOfRequest' => 'SUCCESS',
        'Messages' => $messages_array
    ), JSON_UNESCAPED_SLASHES
);

return;