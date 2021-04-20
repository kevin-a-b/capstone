<?php
/*
 * Session Management for PHP3
 *
 * Copyright (c) 1998-2000 NetUSE AG
 *                    Boris Erdmann, Kristian Koehntopp
 *
 * $Id: db_mysql.inc,v 1.11 2002/08/07 19:33:57 layne_weathers Exp $
 *
 */

class DB_Sql {

    /* public: connection parameters */
    var $Host     = "";
    var $Database = "";
    var $User     = "";
    var $Password = "";

    /* public: configuration parameters */
    var $Auto_Free     = 0;     ## Set to 1 for automatic mysqli_free_result()
    var $Debug         = 0;     ## Set to 1 for debugging messages.
    var $Halt_On_Error = "yes"; ## "yes" (halt with message), "no" (ignore errors quietly), "report" (ignore errror, but spit a warning)
    var $PConnect      = 0;     ## Set to 1 to use persistent database connections
    var $Seq_Table     = "db_sequence";

    /* public: result array and current row number */
    var $Fields   = array();  //exposes fields queried
    var $Record   = array();
    var $Row;

    /* public: current error number and error text */
    var $Errno    = 0;
    var $Error    = "";

    /* public: this is an api revision, not a CVS revision. */
    var $type     = "mysql";
    var $revision = "1.2";

    /* private: link and query handles */
    var $Link_ID  = null;
    var $Query_ID = null;

    var $locked   = false;      ## set to true while we have a lock

    /* public: constructor */
    function __construct($query = "") {

        $this->query($query);
    }

    /* public: some trivial reporting */
    function link_id() {
        return $this->Link_ID;
    }

    function query_id() {
        return $this->Query_ID;
    }

    function insert_id() { // NOTE: Custom function by Brett to counteract deprecated mysql_insert_id() method
        return mysqli_insert_id($this->Link_ID);
    }



    /* public: connection management */
    function connect($Database = "", $Host = "", $User = "", $Password = "") {
        /* Handle defaults */
        //require_once("../_config/config.php");
        $Config = new Config();
        $this->Host     = $Config->DatabaseServer;
        $this->Database = $Config->DatabaseName;
        $this->User     = $Config->DatabaseUsername;
        $this->Password = $Config->DatabasePassword;
        if ("" == $Database)
            $Database = $this->Database;
        if ("" == $Host)
            $Host     = $this->Host;
        if ("" == $User)
            $User     = $this->User;
        if ("" == $Password)
            $Password = $this->Password;


        /* establish connection, select database */
        if ( !isset( $this->Link_ID ) ) {

            if(!$this->PConnect) {
                $this->Link_ID = mysqli_connect($Host, $User, $Password, $Database);
            } else {
                $this->Link_ID = mysqli_pconnect($Host, $User, $Password, $Database);
            }
            if (!$this->Link_ID) {
                $this->halt("connect($Host, $User, \$Password) failed.");
                return 0;
            }

        }

        return $this->Link_ID;
    }

    /* public: discard the query result */
    function free() {
        @mysqli_free_result($this->Query_ID);
        $this->Query_ID = 0;
    }

    /* public: perform a query */
    function query($Query_String) {
        /* No empty queries, please, since PHP4 chokes on them. */
        if ($Query_String == "")
            /* The empty query string is passed on from the constructor,
             * when calling the class without a query, e.g. in situations
             * like these: '$db = new DB_Sql_Subclass;'
             */
            return 0;

        if (!$this->connect()) {
            return 0; /* we already complained in connect() about that. */
        };

        # New query, discard previous result.
        if ($this->Query_ID) {
            $this->free();
        }

        if ($this->Debug)
            printf("Debug: query = %s<br>\n", $Query_String);

        $this->Query_ID = @mysqli_query($this->Link_ID, $Query_String);
        $this->Row   = 0;
        $this->Errno = mysqli_errno($this->Link_ID);
        $this->Error = mysqli_error($this->Link_ID);
        if (!$this->Query_ID) {
            $this->halt("Invalid SQL: ".$Query_String);
        }

        # Will return nada if it fails. That's fine.
        return $this->Query_ID;
    }

    /* public: walk result set */
    function next_record() {
        if (!$this->Query_ID) {
            $this->halt("next_record called with no query pending.");
            return 0;
        }

        $this->Record = @mysqli_fetch_array($this->Query_ID);
        $this->Row   += 1;
        $this->Errno  = mysqli_errno($this->Link_ID);
        $this->Error  = mysqli_error($this->Link_ID);

        $stat = is_array($this->Record);
        if (!$stat && $this->Auto_Free) {
            $this->free();
        }
        return $stat;
    }

    /* public: position in result set */
    function seek($pos = 0) {
        $status = @mysqli_data_seek($this->Query_ID, $pos);
        if ($status)
            $this->Row = $pos;
        else {
            $this->halt("seek($pos) failed: result has ".$this->num_rows()." rows.");

            /* half assed attempt to save the day,
             * but do not consider this documented or even
             * desireable behaviour.
             */
            @mysqli_data_seek($this->Query_ID, $this->num_rows());
            $this->Row = $this->num_rows();
            return 0;
        }

        return 1;
    }

    /* public: table locking */
    function lock($table, $mode = "write") {
        $query = "lock tables ";
        if(is_array($table)) {
            while(list($key,$value) = each($table)) {
                // text keys are "read", "read local", "write", "low priority write"
                if(is_int($key)) $key = $mode;
                if(strpos($value, ",")) {
                    $query .= str_replace(",", " $key, ", $value) . " $key, ";
                } else {
                    $query .= "$value $key, ";
                }
            }
            $query = substr($query, 0, -2);
        } elseif(strpos($table, ",")) {
            $query .= str_replace(",", " $mode, ", $table) . " $mode";
        } else {
            $query .= "$table $mode";
        }
        if(!$this->query($query)) {
            $this->halt("lock() failed.");
            return false;
        }
        $this->locked = true;
        return true;
    }

    function unlock() {

        // set before unlock to avoid potential loop
        $this->locked = false;

        if(!$this->query("unlock tables")) {
            $this->halt("unlock() failed.");
            return false;
        }
        return true;
    }

    /* public: evaluate the result (size, width) */
    function affected_rows() {
        return @mysqli_affected_rows($this->Link_ID);
    }

    function num_rows() {
        return @mysqli_num_rows($this->Query_ID);
    }

    function num_fields() {
        return @mysqli_num_fields($this->Query_ID);
    }

    /* public: shorthand notation */
    function nf() {
        return $this->num_rows();
    }

    function np() {
        print $this->num_rows();
    }

    function f($Name) {
        if (isset($this->Record[$Name])) {
            return $this->Record[$Name];
        }
    }

    function  html($Name)  //Returns urldecoded
    {
        //Debug("db_mysql::html($Name)");
        if (isset($this->Record[$Name])) {
            return stripcslashes(urldecode($this->Record[$Name]));
        }else{
            trigger_error("Field '$Name' Does Not Exist:".DebugObject($this->Record), E_USER_ERROR);
        }

    }

    function p($Name) {
        if (isset($this->Record[$Name])) {
            print $this->Record[$Name];
        }
    }

    /* public: sequence numbers */
    function nextid($seq_name) {
        /* if no current lock, lock sequence table */
        if(!$this->locked) {
            if($this->lock($this->Seq_Table)) {
                $locked = true;
            } else {
                $this->halt("cannot lock ".$this->Seq_Table." - has it been created?");
                return 0;
            }
        }

        /* get sequence number and increment */
        $q = sprintf("select nextid from %s where seq_name = '%s'",
            $this->Seq_Table,
            $seq_name);
        if(!$this->query($q)) {
            $this->halt('query failed in nextid: '.$q);
            return 0;
        }

        /* No current value, make one */
        if(!$this->next_record()) {
            $currentid = 0;
            $q = sprintf("insert into %s values('%s', %s)",
                $this->Seq_Table,
                $seq_name,
                $currentid);
            if(!$this->query($q)) {
                $this->halt('query failed in nextid: '.$q);
                return 0;
            }
        } else {
            $currentid = $this->f("nextid");
        }
        $nextid = $currentid + 1;
        $q = sprintf("update %s set nextid = '%s' where seq_name = '%s'",
            $this->Seq_Table,
            $nextid,
            $seq_name);
        if(!$this->query($q)) {
            $this->halt('query failed in nextid: '.$q);
            return 0;
        }

        /* if nextid() locked the sequence table, unlock it */
        if($locked) {
            $this->unlock();
        }

        return $nextid;
    }

    /* public: return table metadata */
    function metadata($table = "", $full = false) {
        $count = 0;
        $id    = 0;
        $res   = array();
        return $res;
    }

    /* public: find available table names */
    function table_names() {
        $this->connect();
        $h = @mysqli_query("show tables", $this->Link_ID);
        $i = 0;
        while ($info = @mysqli_fetch_row($h)) {
            $return[$i]["table_name"]      = $info[0];
            $return[$i]["tablespace_name"] = $this->Database;
            $return[$i]["database"]        = $this->Database;
            $i++;
        }

        @mysqli_free_result($h);
        return $return;
    }

    /* private: error handling */
    function halt($msg) {
        $this->Error = @mysqli_error($this->Link_ID);
        $this->Errno = @mysqli_errno($this->Link_ID);

        if ($this->locked) {
            $this->unlock();
        }

        if ($this->Halt_On_Error == "no")
            return;

        $this->haltmsg($msg);

        if ($this->Halt_On_Error != "report")
            die("Session halted.");
    }

    function haltmsg($msg) {
        printf("</td></tr></table><b>Database error:</b> %s<br>\n", $msg);
        printf("<b>MySQL Error</b>: %s (%s)<br>\n",
            $this->Errno,
            $this->Error);
    }

    function sql_escape($string) {
        return mysqli_real_escape_string($this->Link_ID, $string);
    }

}
