<?php
//var_dump($_SERVER);

include("classes/config.php");
$Config = new Config();

$Users = new MainData();
$Users->GetUsers();
http_response_code(202);

?>

<html lang="en">
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
