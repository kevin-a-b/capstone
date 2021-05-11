<?php

include("../classes/config.php");
$Config = new Config();

$data = json_decode(file_get_contents('php://input'), true);

if(isset($data['Account_Username']) && isset($data['Account_Password_Hashcode'])){
    $username = $data['Account_Username'];
    $password_hash = $data['Account_Password_Hashcode'];
}else{
    http_response_code(400); // Bad Request
    return;
}

$CheckUsername = new MainData();
$CheckUsername->CheckUsernameExists($username);
if($CheckUsername->num_rows() == 0){
    http_response_code(404); // Not Found
    echo json_encode(
        array(
            'TaskRequested' => 'LOG_INTO_ACCOUNT',
            'ResultOfRequest' => 'USERNAME_DOES_NOT_EXIST',
            'Account_ID' => 0,
            'Public_Key' => NULL,
            'Private_Key' => NULL,
            'IDsOfConversationsUserIsParticipating' => NULL,
            'ConversationInvitations' => NULL
        )
    );
    return;
}

$CheckPassword = new MainData();
$CheckPassword->CheckUsernameAndPassword($username, $password_hash);

if($CheckPassword->num_rows() == 0){
    http_response_code(401); // Unauthorized
    echo json_encode(
        array(
            'TaskRequested' => 'LOG_INTO_ACCOUNT',
            'ResultOfRequest' => 'PASSWORDS_DO_NOT_MATCH',
            'Account_ID' => 0,
            'Public_Key' => NULL,
            'Private_Key' => NULL,
            'IDsOfConversationsUserIsParticipating' => NULL,
            'ConversationInvitations' => NULL
        )
    );
}else{
    $account_id = $CheckPassword->f('Account_ID');
    $public_key = $CheckPassword->f('Public_Key');
    $private_key = $CheckPassword->f('Private_Key');

    $GetConversations = new MainData();
    $GetConversations->GetConversationIDs($account_id);
    $conversation_ids = array();
    while($GetConversations->next_record()){
        $conv_id = $GetConversations->f('Conversation_ID');
        array_push($conversation_ids, $conv_id);
    }

    $GetInvitations = new MainData();
    $GetInvitations->GetUserConversationInvitations($account_id);
    $invitations = array();
    while($GetInvitations->next_record()){
        $inv = array(
            'Conversation_ID' => $GetInvitations->f('Conversation_ID'),
            'Account_Username' => $GetInvitations->f('Account_Username')
        );
        array_push($invitations, $inv);
    }

    http_response_code(200); // OK
    echo json_encode(
        array(
            'TaskRequested' => 'LOG_INTO_ACCOUNT',
            'ResultOfRequest' => 'LOGIN_SUCCESSFUL',
            'Account_ID' => $account_id,
            'Public_Key' => $public_key,
            'Private_Key' => $private_key,
            'IDsOfConversationsUserIsParticipantIn' => $conversation_ids,
            'ConversationInvitations' => $invitations
        )
    );
}
return;

