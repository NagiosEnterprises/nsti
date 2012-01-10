<?php
###########################################################################
#
# class.common.php -  NagTrap class with functions to connect to the
#                      MySQL-Database
#
# Copyright (c) 2006 - 2007 Michael Luebben (nagtrap@nagtrap.org)
# Last Modified: 13.10.2007
#
# License:
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License version 2 as
# published by the Free Software Foundation.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
###########################################################################

/**
* This Class handles database-connection and - queries
*/
class database {
  
    /**
    * Constructor
    *
    * @param config $configINI
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    */  
    function database(&$configINI) {
       if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::contructor()');
       $this->configINI = &$configINI;
       if (DEBUG&&DEBUGLEVEL&1) debug('End method database::contructor()');
    }
    
    /* Escapes the passed value so it is ready to be inserted into the database. Takes magic quotes into
     * consideration as well.
     *
     * @param    string    parameter
     * @return    string    escaped parameter
     */
    /*
     * escape
     * 
     * Abstraction for properly escaping mysql strings.
     * 
     * @param $value = string to be escaped
     * @return escaped $value string
     * 
     * @author Michi Kono
     */
    function escape($value) {
        //stripslashes only if necessary
        if (get_magic_quotes_gpc()) {
            $value = stripslashes($value);
        }
        //if this fails ($newValue is false), we know we need to fall back on the PHP4 way
        $newValue = @mysql_real_escape_string($value);
        //if no connection handler can be found use this instead
        if(FALSE === $newValue) {
            $newValue = @mysql_escape_string($value);
        }
        return $newValue;
    }
    /**
    * Make a connection to the database
    *
    * @param array $configINI
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    */  
    function connect() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::connect()');
        global $configINI, $FRONTEND;
        $connect = @mysql_connect($configINI['database']['host'], $configINI['database']['user'], $configINI['database']['password']);
        $dbSelect['code'] = @mysql_select_db($configINI['database']['name'], $connect);

       // On error, create a array entry with the mysql error
        if(!$dbSelect['code']) {
            $FRONTEND->printError("DBCONNECTION",mysql_error());
            $FRONTEND->closeSite();
            $FRONTEND->printSite();
            if (DEBUG&&DEBUGLEVEL&1) debug('End method database::connect(): FALSE -'.mysql_error());
            exit;
        }
        if (DEBUG&&DEBUGLEVEL&1) debug('End method database::connect(): TRUE');
        return($dbSelect);
    }

        /**
        * Cache all Traps from database in a array
        *
        * @param array $table
        * @param array $type
        * @param array $search
        *
        * @author Michael Luebben <nagtrap@nagtrap.org>
        */
        function search($type,$search) {
            if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::search()');
            global $table;

            // Search in the database

            $query = "SELECT DISTINCT ".$type." FROM ".$table['name']." WHERE ".$type." LIKE '%".$safe_search."%'";
            $result = @mysql_query($query);
            $safe_search = escape( $search );
            // On error, create a array entry with the mysql error
            if(!$result) {
                if (DEBUG&&DEBUGLEVEL&1) debug('End method database::search(): FALSE - '.mysql_error());
                exit;
            }

            while($line = @mysql_fetch_array($result)) {
                $searchResult[] = $line[$type];
            }
            if (DEBUG&&DEBUGLEVEL&1) debug('End method database::search(): Array(...)');
            return($searchResult);
        }
    
    /**
    * Count traps in a given table
    *
    * @param string $hostname
    * @param array $table
    *
    * @author Nicholas Scott <nscott@nagios.com>
    */
    function countTraps() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::countTraps()');
        global $table;
        $query = "select count(*) from `".$table['name']."`;";
        $result = @mysql_query($query);
        $temp = @mysql_fetch_array($result);
        $count = $temp[0];
        if (DEBUG&&DEBUGLEVEL&2) debug('Method database::countTraps()-> query: '.$query.' result: '.$result);

        return $count;
    }
 
    /**
    * Read Traps from database
    *
    * @param string $sort
    * @param boolean $limit
    * @param string $hostname
    * @param array $table
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @author Nicholas Scott <nscott@nagios.com>
    */      
    function readTraps($limit) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::readTraps('.$limit.')');
        global $hostname, $table, $FRONTEND;

        // Create WHERE clause
        if(grab_request_var('severity') == "" and grab_request_var('hostname') == "" and grab_request_var('category') == "" and grab_request_var('searchTrapoid') == "" and grab_request_var('searchHostname') == "" and grab_request_var('searchCategory') == "" and grab_request_var('searchSeverity') == "" and grab_request_var('searchMessage') == "" or grab_request_var('severity') == "UNKNOWN") {
            $dbQuery = "";
        } else {
            if(grab_request_var('searchTrapoid') != "") {
                $dbQuerySet[] = "trapoid LIKE '%".database::escape(grab_request_var('searchTrapoid'))."%'"; 
            }
            if(grab_request_var('searchHostname') != "") {
                $dbQuerySet[] = "hostname LIKE '%".database::escape(grab_request_var('searchHostname'))."%'"; 
            } elseif(grab_request_var('hostname') != "") {
                $dbQuerySet[] = "hostname = '".grab_request_var('hostname')."'"; 
            }
            if(grab_request_var('searchCategory') != "") {
                $dbQuerySet[] = "category LIKE '%".database::escape(rawurldecode(grab_request_var('searchCategory')))."%'"; 
            } elseif(grab_request_var('category') != "") {
                $dbQuerySet[] = "category = '".rawurldecode(grab_request_var('category'))."'"; 
            }
            if(grab_request_var('searchSeverity') != "") {
                $dbQuerySet[] = "severity LIKE '%".database::escape(grab_request_var('searchSeverity'))."%'"; 
            } elseif(grab_request_var('severity') != "") {
                $dbQuerySet[] = "severity = '".grab_request_var('severity')."'"; 
            }
            if(grab_request_var('searchMessage') != "") {
                $dbQuerySet[] = "formatline LIKE '%".database::escape(grab_request_var('searchMessage'))."%'"; 
            }
            $dbQuery = "WHERE ".implode($dbQuerySet," AND ");
        }

        // Set which trap must read first from database
        if (grab_request_var('oldestfirst') == "on") {
            $sort = "ASC";
        } else {
            $sort = "DESC";
        }
 
        // Read traps from database
        $query = "SELECT * FROM ".$table['name']." ".$dbQuery." ORDER BY id ".$sort." LIMIT ".$limit;
        if (DEBUG&&DEBUGLEVEL&2) debug('Method database::readTraps()-> query: '.$query);
        $result = @mysql_query($query);

        // On error, create a array entry with the mysql error
        if(!$result) {
            $FRONTEND->printError("DBTABLE",mysql_error());
            $FRONTEND->closeSite();
            $FRONTEND->printSite(); 
            if (DEBUG&&DEBUGLEVEL&1) debug('End method database::readTraps(): FALSE - '.mysql_error());
            exit; 
        }
   
        while($line = @mysql_fetch_array($result)) {      
            $traps[] = $line;
        }
        if (DEBUG&&DEBUGLEVEL&1) debug('End method database::readTraps(): Array(...)');
        return($traps);
    }
    
    /**
    * Handle a Traps in the database
    *
    * @param string $handle
    * @param boolean $trapID
    * @param string $tableName
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    */  
    function handleTrap($handle,$trapID,$tableName) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::handleTrap('.$handle.','.$trapID.','.$tableName.')');
        global $FRONTEND;
        if($handle == "mark") {
            $query = "UPDATE $tableName SET trapread = 1 WHERE id = $trapID";
            $result = mysql_query($query);
        } elseif($handle == "delete") {
            $query = "DELETE FROM $tableName WHERE id = $trapID";
            $result = mysql_query($query);
        } elseif($handle == "archive") {
            $result = mysql_query("SELECT * FROM $tableName WHERE id = $trapID");
            $trap = mysql_fetch_array($result);
            $query = "INSERT INTO snmptt_archive (snmptt_id, eventname, eventid, trapoid, enterprise, community,
                                                hostname, agentip, category, severity, uptime, traptime,formatline, trapread) 
                    VALUES ('$trap[id]', '$trap[eventname]', '$trap[eventid]', '$trap[trapoid]', '$trap[enterprise]', '$trap[community]',
                                '$trap[hostname]','$trap[agentip]', '$trap[category]', '$trap[severity]', '$trap[uptime]', '$trap[traptime]',
                            '$trap[formatline]', '$trap[trapread]')";
            $result = mysql_query($query);
            $query = "DELETE FROM $tableName WHERE id = $trapID";
            $result = mysql_query($query);
        }     
        if(!$result) {
            $FRONTEND->printError("DBHANDLETRAP",mysql_error());
            $FRONTEND->closeSite();
            $FRONTEND->printSite(); 
            if (DEBUG&&DEBUGLEVEL&1) debug('End method database::handleTrap(): FALSE - '.mysql_error());
            exit; 
        }
        if (DEBUG&&DEBUGLEVEL&1) debug('End method database::handleTrap(): '.$result);
        return($result);
    }
    
    /**
    * Read Trap-Infromation from the database
    *
    * @param string $tableName
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    */  
    function infoTrap($tableName) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::infoTrap('.$tableName.')');
        global $FRONTEND;
        $query = "SELECT id,traptime FROM $tableName ORDER BY id";
        $result = mysql_query($query);
        if(!$result) {
            $FRONTEND->printError("DBREADTRAP",mysql_error());
            $FRONTEND->closeSite();
            $FRONTEND->printSite(); 
            if (DEBUG&&DEBUGLEVEL&1) debug('End method database::infoTrap(): FALSE - '.mysql_error());
            exit; 
        }
        while($line = mysql_fetch_array($result)) {
            $trapTime[] = $line['traptime']; 
        }
        if($trapTime[0] != "") {
            $trap[last] = array_pop($trapTime);
            $trap[first] = array_pop(array_reverse($trapTime));
        }
        if (DEBUG&&DEBUGLEVEL&1) debug('End method database::infoTrap(): Array(...)');
        return($trap);
    }

    /**
    * Read category from database
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    *
    */
    function readCategory($tableName) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::readCategory('.$tableName.')');
        global $FRONTEND;
        $query = "SELECT DISTINCT category FROM $tableName";
        $result = mysql_query($query);
        if(!$result) {
            $FRONTEND->printError("DBREADCATEGORY",mysql_error());
            $FRONTEND->closeSite();
            $FRONTEND->printSite(); 
            if (DEBUG&&DEBUGLEVEL&1) debug('End method database::readCategory(): FALSE - '.mysql_error());
            exit; 
        }
        while ($line = mysql_fetch_array($result)) {
            $category[] = $line['category'];
        }
        if (DEBUG&&DEBUGLEVEL&1) debug('End method database::readCategory(): Array(...)');
        return($category);
    } 

}

?>
