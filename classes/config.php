<?php
echo "\n\ntop of config file\n\n";
include("classes/db_mysqli.php");
echo "\n\nafter db_mysqli include\n\n";
include("classes/main_data.php");
echo "\n\nafter main_data include\n\n";

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
