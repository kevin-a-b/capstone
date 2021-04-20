<?php


class MainData extends DB_Sql
{

    function GetUsers(){
        $queryString = "select * from users";
        $this->query($queryString);
    }

}