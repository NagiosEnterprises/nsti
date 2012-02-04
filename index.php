<?php
/**
 * index.php -  Nagios SNMP Trap Interface
 * 
 * PHP Version 5.2
 * 
 * @category SNMP_Management
 * @package  Nagios_SNMP_Trap_Interface
 * @author   Nicholas Scott <nscott@nagios.com>
 * @author   Michael Luebben <nagtrap@nagtrap.com>
 * @license  GNU - http://www.gnu.org/licenses/gpl-2.0.html
 * @link     http://exchange.nagios.org/nagiostrapinterface
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 as
 * published by the Free Software Foundation.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 */

// Disable error-message
error_reporting(E_ALL ^ E_NOTICE);

include("header.php");

require "./include/classes/class.index.php";

if (grab_request_var('state')){
    $_SESSION['state'] = grab_request_var('state');
    die();
}
// Create a new page
$MAIN = new main();

// Read config.ini.php
$configINI = $MAIN->readConfig(CONST_MAINCFG);

// Set variables in configuration
$configINI['database']['tableSnmpttArchive'] = "snmptt_archive";

// Read error.xml for error-messages
$errorXML = $MAIN->readXML("./include/xml/language/".$configINI['global']['language']."/error.xml");

// Read language 
$languageXML = $MAIN->readXML("./include/xml/language/".$configINI['global']['language']."/main.xml");

// Set table
$table = $MAIN->setTable($tableName, grab_request_var('severity'), grab_request_var('trapSelect'));

$FRONTEND = new index($configINI,$table);

$DATABASE = new database($configINI);
$DATABASE->connect();

    if (main::checkUser() == "0") {
        $FRONTEND->printError("AUTHENTIFICATION", null);
    } 
    else {
            
        // If set action, then mark, delete or archive a trap in the database
        if (grab_request_var('action') == "mark" or grab_request_var('action') == "delete" or grab_request_var('action') == "archive") {
            $DATABASE->handleTrap(grab_request_var('action'), grab_request_var('trapID'), $table['name']); 
        }

        // Mark more as one trap 
        if (grab_request_var('markTraps') AND grab_request_var('trapIDs')) {
            foreach (grab_request_var('trapIDs') as $trapID) {
                $DATABASE->handleTrap("mark", $trapID, $table['name']); 
            }
        }

        // Delete more as one trap 
        if (grab_request_var('deleteTraps') AND grab_request_var('trapIDs')) {
            foreach (grab_request_var('trapIDs') as $trapID) {
                $DATABASE->handleTrap("delete", $trapID, $table['name']);
            }
        }

        // Delete more as one trap 
        if (grab_request_var('archiveTraps') AND grab_request_var('trapIDs')) {
            foreach (grab_request_var('trapIDs') as $trapID ) {
                $DATABASE->handleTrap("archive", $trapID, $table['name']);
            }
        }
        
        // Add a filter given by $_POST
        if (grab_request_var('updatefilter') != NULL) {
            $_SESSION['applied_filters'] = array();
            $filter_id = grab_request_var('updatefilter');
            //~ print_r($filterid);
            foreach($filter_id as $id){
                if($id != 'empty') {
                    $requested_filter = $DATABASE->getItem('filters',$id);
                    $_SESSION['applied_filters'][$id] = $requested_filter['filtername'];
                }
            }
        }
        
        if (grab_request_var('boolean')) {
            $boolean = grab_request_var('boolean');
            if ($boolean == 'AND' || $boolean == 'OR')
                $_SESSION['boolean_combiner'] = $boolean;
        }
        
        if (grab_request_var('perpage')) {
            $_SESSION['perpage'] = grab_request_var('perpage');
        }

        $FRONTEND->constructorHeader();
        $FRONTEND->constructorMain();
        $FRONTEND->constructorFooter();
        }
        
$FRONTEND->closeSite();
$FRONTEND->printSite();

?>
