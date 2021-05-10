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

    function CheckUsernameAndPassword($username, $password){
        $queryString = "SELECT 
                            *
                        FROM
                            `User_Account`
                        WHERE
                            `Account_Username` = '$username'
                                AND `Account_Password_Hashcode` = '$password';";
        $this->query($queryString);
        $this->next_record();
    }

    function GetConversationIDs($account_id){
        $queryString = "SELECT 
                            Conversation_ID
                        FROM
                            Conversation_Participant
                        WHERE
                            Account_ID = $account_id;";
        $this->query($queryString);
    }

    function GetUserConversationInvitations($account_id){
        $queryString = "SELECT 
                            inv.Conversation_ID, acc.Account_Username
                        FROM
                            Pending_Conversation_Invitation inv
                                JOIN
                            User_Account acc ON acc.Account_ID = inv.Sender_Account_ID
                        WHERE
                            inv.Recipient_Account_ID = $account_id;";
        $this->query($queryString);
    }

    function CreateNewConversation(){
        $queryString = "INSERT INTO `Conversation` VALUES ();";
        $this->query($queryString);

    }

}