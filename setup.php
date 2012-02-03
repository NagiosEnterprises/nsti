<?php
/**
 * alerts.php -  Nagios SNMP Trap Interface
 * 
 * PHP Version 5.2
 * 
 * @category SNMP_Management
 * @package  Nagios_SNMP_Trap_Interface
 * @author   Nicholas Scott <nscott@nagios.com>
 * @license  GNU - http://www.gnu.org/licenses/gpl-2.0.html
 * @version  SVN: RC1.3.2
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

require "./include/classes/class.setup.php";

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

$FRONTEND = new setup($configINI,$table);

$DATABASE = new database($configINI);
$DATABASE->connect();

    if (main::checkUser() == "0") {
        $FRONTEND->printError("AUTHENTIFICATION", null);
    }
    else {
            
        $FRONTEND->constructorHeader();
        //~ $FRONTEND->constructorMain();
        //~ $FRONTEND->constructorFooter();
        
	}
        
$FRONTEND->closeSite();
$FRONTEND->printSite();

?>
