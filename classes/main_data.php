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

    function LastInsertID(){
        $queryString = "SELECT LAST_INSERT_ID();";
        $this->query($queryString);
        $this->next_record();
    }

    function ViewMessages($conversation_id, $num_messages){
        $queryString = "SELECT 
                           acc.Account_Username,
                           m.Sent_Date_And_Time,
                           m.Message_Ciphertext
                        FROM
                            Message m
                                JOIN
                            User_Account acc ON acc.Account_ID = m.Sender_Account_ID
                        WHERE
                            m.Conversation_ID = $conversation_id
                        ORDER BY m.Sent_Date_And_Time DESC
                        LIMIT $num_messages;";
        $this->query($queryString);
    }

    function GetConversationPrivateKey($conversation_id, $account_id){
        $queryString = "SELECT 
                            Conversation_Private_Key
                        FROM
                            Conversation_Participant
                        WHERE
                            Conversation_ID = $conversation_id 
                            AND Account_ID = $account_id;";
        $this->query($queryString);
        $this->next_record();
    }

    function GetPublicKeyByUsername($username){
        $queryString = "SELECT 
                            Public_Key
                        FROM
                            User_Account
                        WHERE
                            Account_Username = '$username';";
        $this->query($queryString);
        $this->next_record();
    }

    function AddConversationParticipant($conversation_id, $account_id, $conv_private_key){
        $queryString = "INSERT INTO `Conversation_Participant`
                        (`Conversation_ID`, `Account_ID`, `Conversation_Private_Key`)
                        VALUES
                        ($conversation_id, $account_id, '$conv_private_key');";
        $this->query($queryString);
    }

    function CheckIfConversationParticipantExists($conversation_id, $recipient_username){
        $queryString = "SELECT 
                            *
                        FROM
                            Conversation_Participant conv_part
                                JOIN
                            User_Account ua ON ua.Account_ID = conv_part.Account_ID
                        WHERE
                            conv_part.Conversation_ID = $conversation_id
                                AND ua.Account_Username = '$recipient_username';";
        $this->query($queryString);
    }

    function InsertConversationInvitation($conversation_id, $recipient_username, $sender_account_id, $conversation_private_key){
        $queryString = "INSERT INTO `Pending_Conversation_Invitation`
                        (`Conversation_ID`,
                        `Recipient_Account_ID`,
                        `Sender_Account_ID`,
                        `Conversation_Private_Key`)
                        VALUES
                        ($conversation_id, (SELECT Account_ID FROM User_Account WHERE Account_Username = '$recipient_username'), $sender_account_id,
                         '$conversation_private_key');";
        $this->query($queryString);
    }

    function GetNewConversationInvitations($conv_id_start, $recipient_account_id){
        $queryString = "SELECT 
                            inv.Conversation_ID conv_id,
                            ua.Account_Username sender_username
                        FROM
                            Pending_Conversation_Invitation inv
                                JOIN
                            User_Account ua ON ua.Account_ID = inv.Sender_Account_ID
                        WHERE
                            inv.Recipient_Account_ID = $recipient_account_id
                                AND inv.Conversation_ID >= $conv_id_start;";
        $this->query($queryString);
    }

    function AddConversationParticipantWithoutPrivateKeyParameter($conv_id, $account_id){
        $queryString = "INSERT INTO `Conversation_Participant`
                        (`Conversation_ID`,
                        `Account_ID`,
                        `Conversation_Private_Key`)
                        VALUES
                        ($conv_id, $account_id, 
                        (SELECT Conversation_Private_Key
                        FROM
                            Pending_Conversation_Invitation
                        WHERE
                            Conversation_ID = $conv_id
                                AND Recipient_Account_ID = $account_id));";
        $this->query($queryString);
    }

    function DeleteOldPendingInvitation($conv_id, $account_id){
        $queryString = "DELETE FROM `Pending_Conversation_Invitation` 
                        WHERE
                            Conversation_ID = $conv_id
                            AND Recipient_Account_ID = $account_id;";
        $this->query($queryString);
    }

    function IncreaseConversationMessageCount($conv_id){
        $queryString = "UPDATE `Conversation` 
                        SET 
                            `Total_Number_Of_Messages` = `Total_Number_Of_Messages` + 1
                        WHERE
                            `Conversation_ID` = $conv_id;";
        $this->query($queryString);
    }

    function GetCurrentMessageCount($conv_id){
        $queryString = "SELECT 
                            Total_Number_Of_Messages num_messages
                        FROM
                            Conversation
                        WHERE
                            Conversation_ID = $conv_id;";
        $this->query($queryString);
        $this->next_record();
    }

    function InsertNewMessage($message_count, $conv_id, $message_cipher, $account_id, $date_time){
        $queryString = "INSERT INTO `Message`
                        (`Message_Number`, `Conversation_ID`, `Message_Ciphertext`,
                        `Sender_Account_ID`, `Sent_Date_And_Time`)
                        VALUES
                        ($message_count, $conv_id, '$message_cipher', $account_id, '$date_time');";
        $this->query($queryString);
    }

    function GetMessagesInRange($conv_id, $start_num_inclusive, $end_num_inclusive){
        $queryString = "SELECT 
                            m.Message_Number,
                            m.Message_Ciphertext message_cipher,
                            ua.Account_Username sender_username,
                            m.Sent_Date_And_Time date_time
                        FROM
                            Message m
                                JOIN
                            User_Account ua ON ua.Account_ID = m.Sender_Account_ID
                        WHERE
                            m.Conversation_ID = $conv_id
                                AND m.Message_Number >= $start_num_inclusive
                                AND m.Message_Number <= $end_num_inclusive
                        ORDER BY m.Message_Number ASC;";
        $this->query($queryString);
    }

    function GetNewMessages($conv_id, $start_num_inclusive){
        $queryString = "SELECT 
                            m.Message_Number,
                            m.Message_Ciphertext message_cipher,
                            ua.Account_Username sender_username,
                            m.Sent_Date_And_Time date_time
                        FROM
                            Message m
                                JOIN
                            User_Account ua ON ua.Account_ID = m.Sender_Account_ID
                        WHERE
                            m.Conversation_ID = $conv_id
                                AND m.Message_Number >= $start_num_inclusive
                        ORDER BY m.Message_Number ASC;";
        $this->query($queryString);
    }



}