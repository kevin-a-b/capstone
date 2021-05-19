<?php

include("../classes/config.php");
$Config = new Config();

$data = json_decode(file_get_contents('php://input'), true);

if(isset($data['Account_ID']) && isset($data['StartingConversationIdInclusive'])){
    $account_id = $data['Account_ID'];
    $conv_id_start = $data['StartingConversationIdInclusive'];
}else{
    http_response_code(400); // Bad Request
    return;
}

$NewInvitations = new MainData();
$NewInvitations->GetNewConversationInvitations($conv_id_start, $account_id);

$invitations = array();
while($NewInvitations->next_record()){
    $inv = array(
        'Conversation_ID' => $NewInvitations->f("conv_id"),
        'Account_Username' => $NewInvitations->f("sender_username")
    );
    array_push($invitations, $inv);
}

http_response_code(200); // OK
echo json_encode(
    array(
        'TaskRequested' => 'LOAD_ANY_NEW_INVITATIONS',
        'ResultOfRequest' => 'SUCCESS',
        'ConversationInvitations' => $invitations
    ), JSON_UNESCAPED_SLASHES
);
return;

