<?php


$Users = new MainData();
$Users->GetUsers();

$json_users = [];

while($Users->next_record()){
    array_push($json_users, array('id' => $Users->f('account_id'), 'user_name' => $Users->f('user_name')));
}

http_response_code(200);    // OK

echo json_encode(
    array(
        "users" => $json_users
    )
);


//account_id
//user_name