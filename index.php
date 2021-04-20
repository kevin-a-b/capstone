<?php
//var_dump($_SERVER);
//$dbhost = $_SERVER['aa1g1tnfvy31umg.cqupwsiydadl.us-east-2.rds.amazonaws.com'];
//$dbport = $_SERVER['3306'];
//$dbname = $_SERVER['ebdb'];
//$charset = 'utf8' ;
//
//$dsn = "mysql:host={$dbhost};port={$dbport};dbname={$dbname};charset={$charset}";
//$username = $_SERVER['capstone'];
//$password = $_SERVER['password'];
//
//$pdo = new PDO($dsn, $username, $password);
$host = 'aa1g1tnfvy31umg.cqupwsiydadl.us-east-2.rds.amazonaws.com';
$dbname = 'ebdb';
$port = 3306;
$username = 'capstone';
$password = 'password';

$link = new mysqli($host, $username, $password, $dbname, $port);

if ($link -> connect_errno) {
    echo "Failed to connect to MySQL: " . $link -> connect_error;
    exit();
}

if ($result = $link -> query("select * from user_account")) {
    echo "Returned rows are: " . $result -> num_rows;
    // Free result set
    $result -> free_result();
}

$link -> close();

?>

<html>
    <body>
        <h1>Hello World</h1>
    </body>
</html>
