<?php

include("db_mysqli.php");
include("main_data.php");

class Config
{
    var $DatabaseServer;
    var $DatabaseName;
    var $DatabaseUsername;
    var $DatabasePassword;

    public function __construct()
    {
        $this->DatabaseServer   = "aa1g1tnfvy31umg.cqupwsiydadl.us-east-2.rds.amazonaws.com";
        $this->DatabaseName = "ebdb";
        $this->DatabaseUsername = "capstone";
        $this->DatabasePassword = "password";
    }
}
