<?php


class MainData extends DB_Sql
{

    function GetUsers(){
        $queryString = "select * from User_Account";
        $this->query($queryString);
    }

    function CheckUsernameExists($username){
        $queryString = "SELECT 
                            *
                        FROM
                            User_Account
                        WHERE
                            Account_Username = '$username';";
        $this->query($queryString);
    }

    function CreateNewUserAccount($username, $password_hash, $public_key, $private_key){
        $queryString = "INSERT INTO `User_Account`
                        (`Account_Username`, `Account_Password_Hashcode`, `Public_Key`, `Private_Key`)
                        VALUES
                        ('$username', '$password_hash', '$public_key', '$private_key');";
        $this->query($queryString);
    }

}