<?php
###########################################################################
#
# class.common.php -  NagTrap class with functions to connect to the
#                      MySQL-Database
#
# Copyright (c) 2006 - 2007 Michael Luebben (nagtrap@nagtrap.org)
#               2011 - 2012 Nicholas Scott (nscott@nagios.com)
# Last Modified: 1.17.2012
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
    
    /**
    * Make a connection to the database
    *
    * Rewrote to use the redbean ORM -NS 1/17/12
    * Removed unecessary variable declarations. -NS 1/17/12
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @author Nicholas Scott <nscott@nagios.com>
    * 
    */  
    function connect() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::connect()');
        global $configINI, $FRONTEND;
        
        // Rename host, user, database and password for clarity
        $hostname = $configINI['database']['host'];
        $username = $configINI['database']['user'];
        $password = $configINI['database']['password'];
        $db_table = $configINI['database']['name'];
        $db_type  = $configINI['database']['type'];
        
        // Create connection string to give to redbean
        $conn_str = "$db_type:host=$hostname;dbname=$db_table";
        if (DEBUG&&DEBUGLEVEL&1) debug('Connecting to database with string '.$conn_str);
        
        // Attempt to create database
        try {
            R::setup($conn_str , $username , $password);
            R::count($db_table);
            //~ R::freeze(true);
        }
        // If the database opening fails, catch exception and close site:
        catch(Exception $e) {
            // Give printError the exception string
            frontend::printError("DBCONNECTION",$e->getMessage());
            frontend::closeSite();
            frontend::printSite();
            if (DEBUG&&DEBUGLEVEL&1) debug('End method database::connect(): FALSE -'.$e->getMessage());
            exit;
        }
        if (DEBUG&&DEBUGLEVEL&1) debug('End method database::connect(): TRUE');
        return($dbSelect);
    }

    /*
    * Cache all Traps from database in a array
    *
    * Refactored to use Redbean -NS 1/17/12
    * 
    * @param array $table
    * @param array $type
    * @param array $search
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @author Nicholas Scott <nscott@nagios.com>
    */
    function search($type,$search) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::search()');
        global $table;
        
        // Search in the database
        try {
            $searchResult = R::find($table['name'],"$type LIKE '%$search%'");
        }
        // On error, create a array entry with the mysql error
        catch(Exception $e) {
            if (DEBUG&&DEBUGLEVEL&1) debug('End method database::search(): FALSE - '.$e->getMessage());
            exit;
        }
        // For legacy reasons, turn them all back into arrays
        $searchResult = R::exportAll($searchResult);
        if (DEBUG&&DEBUGLEVEL&1) debug('End method database::search(): Array(...)');
        return($searchResult);
    }
    
    /**
    * Count traps in a given table
    *
    * Refactored to use Redbean -NS
    * 
    * @param array $table
    * 
    * @return integer $count
    *
    * @author Nicholas Scott <nscott@nagios.com>
    */
    function countTraps() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::countTraps()');
        global $table;
        $count = R::count($table['name']);
        if (DEBUG&&DEBUGLEVEL&2) debug('End method database::countTraps()');

        return $count;
    }
 
    /**
    * Read Traps from database
    * 
    * Added filter processing this function. 1/23/12
    * Refactored variables names. -NS 1/17/12
    * Refactored if statement. -NS 1/17/12
    * Refactored to use Redbean. -NS 1/17/12
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
        // Read data from POST and GET
        $severity       = grab_request_var('severity');
        $hostname       = grab_request_var('hostname');
        $category       = grab_request_var('category');
        $searchOID      = grab_request_var('searchTrapoid');
        $searchHostname = grab_request_var('searchHostname');
        $searchCategory = rawurldecode(grab_request_var('searchCategory'));
        $searchSeverity = grab_request_var('searchSeverity');
        $searchMessage = grab_request_var('searchMessage'); 
        
        $filterlist     = $_SESSION['applied_filters'];
        // Determine how the ALL the filter will be combined
        $boolean        = (isset($_SESSION['boolean_combiner'])) ? $_SESSION['boolean_combiner'] : 'AND';
        // ftQuery is the query string made from filters
        $ftQuery = array();
        // If filter list has contents, make an array of all of the filter ids
        if($filterlist) {
            foreach($filterlist as $id => $name)
                $filterids[] = $id;
            // Grab all matching beans
            $filters = R::batch('filters',$filterids);
            // Make an array of column names to iterate through
            $colname = array(   'eventname'
                            ,    'eventid'
                            ,    'trapoid'
                            ,    'enterprise'
                            ,    'hostname'
                            ,    'category'
                            ,    'severity'
                            ,    'formatline' );
            foreach($filters as $filter) {
                /* For each filter applied, check to see which field are
                 * not blank. For those that are not blank, formulate 
                 * the SQL query for it. */
                $tmpQuery    = array();
                foreach($colname as $filterfield) {
                    if($filter[$filterfield]) {
                        $columnquery = $filterfield.'query';
                        $querytype   = $filter[$columnquery];
                        switch($querytype) {
                            case 'exactly':
                                $tmpQuery[] = "{$filterfield} in ({$filter[$filterfield]})";
                                break;
                            case 'notexactly':
                                $tmpQuery[] = "{$filterfield} not in ({$filter[$filterfield]})";
                                break;
                            case 'contain':
                                $tmpQuery[] = "{$filterfield} LIKE '%{$filter[$filterfield]}%'";
                                break;
                            case 'notcontain':
                                $tmpQuery[] = "{$filterfield} NOT LIKE '%{$filter[$filterfield]}%'";
                                break;
                            default:
                                break;
                        }
                        
                    }
                }
                $ftQuery[] = '('.implode($tmpQuery," AND ").')';

            }
        }
        
        /* Create WHERE clause by checking each search variable from
         * the server variables and adding the SQL if any of the server
         * variables exist 
         */
        if($searchOID)
            $dbQuery[] = "trapoid LIKE '%{$searchOID}%'"; 
        if($searchHostname)
            $dbQuery[] = "hostname LIKE '%{$searchHostname}%'"; 
        if($hostname)
            $dbQuery[] = "hostname = '{$hostname}'"; 
        if($searchCategory)
            $dbQuery[] = "category LIKE '%{$searchCategory}%'"; 
        if($category)
            $dbQuery[] = "category = '{$category}'"; 
        if($searchSeverity)
            $dbQuery[] = "severity LIKE '%{$searchSeverity}%'"; 
        if($severity)
            $dbQuery[] = "severity = '{$severity}'"; 
        if($searchMessage)
            $dbQuery[] = "formatline LIKE '%{$searchMessage}%'"; 
        
        $logicQuery = '';
        /* Combine all items created in the above if statements together
         * with a space in the front (required for redbean) with an AND
         */
        if (count($dbQuery) != 0) 
            $logicQuery .= " (".implode($dbQuery," AND ").") ";
        if (count($ftQuery) != 0) {
            if($logicQuery != "")
                $logicQuery .= " AND ";
            $logicQuery .= " (".implode($ftQuery," {$boolean} ").") ";
        }
        if ($logicQuery != "")
            $logicQuery = "WHERE ".$logicQuery;
        //~ print "Total: $logicQuery\n";
        // Set which trap must read first from database
        $sort = (grab_request_var('oldestfirst') == "on") ? "ASC" : "DESC";
 
        // Read traps from database
        $query = "SELECT * FROM ".$table['name']." ".$logicQuery." ORDER BY id ".$sort." LIMIT ".$limit;
        if (DEBUG&&DEBUGLEVEL&2) debug('Method database::readTraps()-> query: '.$query);
        
        try {
            $traps = R::getAll($query);
        }
        // On error, create a array entry with the mysql error
        catch(Exception $e) {
            frontend::printError("DBTABLE",$e->getMessage());
            frontend::closeSite();
            frontend::printSite(); 
            if (DEBUG&&DEBUGLEVEL&1) debug('End method database::readTraps(): FALSE - '.$e->getMessage());
            exit; 
        }
        if (DEBUG&&DEBUGLEVEL&1) debug('End method database::readTraps(): Array(...)');
        return($traps);
    }
    
    /**
    * Handle a Traps in the database
    *
    * Refactored to use Redbean ORM. -NS 1/17/12
    * 
    * TODO: Catch exceptions in a better way.
    * 
    * @param string $handle
    * @param boolean $trapID
    * @param string $tableName
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @author Nicholas Scott <nscott@nagios.com>
    */  
    function handleTrap($handle,$trapID,$tablename) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::handleTrap('.$handle.','.$trapID.','.$tablename.')');
        /* Derive our bean from the database using the $trapID */
        $trap = R::load($tablename,$trapID);
        /* If our trap has a non-zero ID, then we can continue
         * However if it has a zero ID then we must give an error.
         * This logic is handled using an if/else statement based off
         * $trap->id
         */
        if ($trap->id || 1) {
            switch($handle) {
                case 'mark':
                    // Toggle value for trapRead
                    $trap->trapRead = !$trap->trapRead;
                    R::store($trap);
                    break;
                case 'delete':
                    // Simply trash the trap
                    R::trash($trap);
                    break;
                case 'archive':
                    /* Create new archive trap and delete from snmptt
                     * table.
                     * 
                     * This is achieved by dumping the trap into an array
                     * and unsetting the id and trapRead keys of the array. */
                    $arch_trap = R::dispense('snmptt_archive');
                    $archive_array = $trap->export();
                    unset($archive_array['id']);
                    unset($archive_array['trapRead']);
                    $arch_trap->import($archive_array);
                    R::store($arch_trap);
                    break;
                default:
                    break;
            }
        }     
        else {
            frontend::printError("DBHANDLETRAP","Unable to read database.");
            frontend::closeSite();
            frontend::printSite(); 
            if (DEBUG&&DEBUGLEVEL&1) debug('End method database::handleTrap(): FALSE - Unable to read database.');
            exit; 
        }
        if (DEBUG&&DEBUGLEVEL&1) debug('End method database::handleTrap(): '.$result);
        return($result);
    }
    
    /**
    * Read Trap-Infromation from the database
    *
    * Refactored to use redbean ORM -NS 1/17/12
    * 
    * @param string $tableName
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @author Nicholas Scott <nscott@nagios.com>
    */  
    function infoTrap($tableName) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::infoTrap('.$tableName.')');
        /* Determine time of the first and last trap, return as tuple */
        try {
            $trap['last']= R::getCell("select traptime from $tableName ORDER BY id DESC LIMIT 1");
            $trap['first']= R::getCell("select traptime from $tableName ORDER BY id ASC LIMIT 1");
            //~ print_r($trap);
            //~ die;
        }
        catch(Exception $e) {
            frontend::printError("DBREADTRAP",mysql_error());
            frontend::closeSite();
            frontend::printSite(); 
            if (DEBUG&&DEBUGLEVEL&1) debug('End method database::infoTrap(): FALSE - '.$e->getMessage());
            exit; 
        }
        if (DEBUG&&DEBUGLEVEL&1) debug('End method database::infoTrap(): Array(...)');
        return($trap);
    }

    /**
    * Read category from database
    *
    * Refactored to use Redbean ORM -NS 1/17/12
    * 
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @author Nicholas Scott <nscott@nagios.com>
    */
    function readCategory($tableName) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::readCategory('.$tableName.')');
        try{
            /* getCol only returns column as array, not as multidimensional
             * array. 
             */
            $category = R::getCol("SELECT DISTINCT category FROM $tableName");
        }
        catch(Exception $e) {
            frontend::printError("DBREADCATEGORY",$e->getMessage());
            frontend::closeSite();
            frontend::printSite(); 
            if (DEBUG&&DEBUGLEVEL&1) debug('End method database::readCategory(): FALSE - '.$e->getMessage());
            exit; 
        }
        
        if (DEBUG&&DEBUGLEVEL&1) debug('End method database::readCategory(): Array(...)');
        return($category);
    } 
    
    /**
     * Get list of all filters
     * 
     * @author Nicholas Scott <nscott@nagios.com>
     */
    function getFilters() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::getFilters()');
        try {
            $filters = R::find('filters');
        }
        catch(Exception $e) {
            frontend::printError("DBREADCATEGORY",$e->getMessage());
            frontend::closeSite();
            frontend::printSite(); 
            if (DEBUG&&DEBUGLEVEL&1) debug('End method database::getFilters(): FALSE - '.$e->getMessage());
            exit; 
        }
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::getFilters()');
        return $filters;
    }
    
    /**
    * getItem - Grabs filter of that id
    * 
    * @TODO - Generalize these database functions so I don't have to write
    * one for each database access. 
    * 
    * 1/21/12 - First attempt at testing getItem as the general.
    * 
    * @author Nicholas Scott <nscott@nagios.com>
    *
    **/
    function getItem($table,$id) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::getItem('.$table.','.$id.')');
        try {
            $item = R::load($table,$id);
        }
        catch(Exception $e) {
            frontend::printError("DBREADCATEGORY",$e->getMessage());
            frontend::closeSite();
            frontend::printSite(); 
            if (DEBUG&&DEBUGLEVEL&1) debug('End method database::getItem(): FALSE - '.$e->getMessage());
            exit; 
        }
        if (DEBUG&&DEBUGLEVEL&1) debug('End method database::getItem()');
        return $item;
    }
    
    /**
    * deleteItem - Deletes item of id from table
    * 
    * @param id
    * 
    * id of the item in the table
    * 
    * @param table
    * 
    * table that item exists in
    * 
    * @author Nicholas Scott <nscott@nagios.com>
    *
    **/
    function deleteItem($table,$id) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::deleteItem()');
        try {
            $item = R::load($table,$id);
            R::trash($item);
        }
        catch(Exception $e) {
            frontend::printError("DBREADCATEGORY",$e->getMessage());
            frontend::closeSite();
            frontend::printSite(); 
            if (DEBUG&&DEBUGLEVEL&1) debug('End method database::deleteItem(): FALSE - '.$e->getMessage());
            exit; 
        }
        if (DEBUG&&DEBUGLEVEL&1) debug('End method database::deleteItem()');
        return;
        if (DEBUG&&DEBUGLEVEL&1) debug('End method database::deleteItem()');
    }
    
    
    /**
    * saveForm - Saves form into a bean of the type determined by the form
    * 
    * @author Nicholas Scott <nscott@nagios.com>
    *
    * @param formarray
    * 
    * The $_REQUEST array associated with the form.
    * 
    **/
    function saveForm($formarray,$filterid = 0) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::saveForm()');
        // If an id was passed, we need to update the bean with that id
        if($filterid) {
            $updatebean = $this->getItem('filters',$filterid);
            foreach($formarray as $column => $value)
                $updatebean->$column = $value;
            R::store($updatebean);
        }
        // Otherwise create a new bean and use that
        else {
            $newbean = R::graph($formarray);
            //~ print $newbean;
            R::store($newbean);
        }
        if (DEBUG&&DEBUGLEVEL&1) debug('End method database::saveForm()');
    }
    
    

}

?>
