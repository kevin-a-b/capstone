<?php


class MainData extends DB_Sql
{

    function GetUsers(){
        $queryString = "select * from user_account";
        $this->query($queryString);
    }

}