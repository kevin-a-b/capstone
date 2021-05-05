<?php
//var_dump($_SERVER);

//$host = 'aa1g1tnfvy31umg.cqupwsiydadl.us-east-2.rds.amazonaws.com';
//$dbname = 'ebdb';
//$port = 3306;
//$username = 'capstone';
//$password = 'password';
//
//$link = new mysqli($host, $username, $password, $dbname, $port);
//
//if ($link -> connect_errno) {
//    echo "Failed to connect to MySQL: " . $link -> connect_error;
//    exit();
//}
//
//if ($result = $link -> query("select * from user_account")) {
//    echo "Returned rows are: " . $result -> num_rows;
//    // Free result set
//    $result -> free_result();
//}
//
//$link -> close();


include("classes/config.php");
$Config = new Config();

$Users = new MainData();
$Users->GetUsers();
//http_response_code(200);

?>

<html>
    <body>
        <h1>Hello World</h1>
        <h1>Luke's Edit</h1>
        <table>
            <thead>
                <th>ID</th>
                <th>user_name</th>
            </thead>
            <tbody>
            <? while($Users->next_record()){ ?>
                <tr>
                    <td> <?= $Users->f("Account_ID") ?> </td>
                    <td> <?= $Users->f("Account_Username") ?> </td>
                </tr>
            <? } ?>
            </tbody>
        </table>
    </body>
</html>
