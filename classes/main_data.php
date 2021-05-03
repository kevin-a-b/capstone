<?php


class MainData extends DB_Sql
{

    function GetUsers(){
        $queryString = "select * from User_Account";
        $this->query($queryString);
    }

}