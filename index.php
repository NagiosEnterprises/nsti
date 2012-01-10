<?php
/**
 * index.php -  Nagios Trap Interface
 * 
 * PHP Version 5.2
 * 
 * @category SNMP_Management
 * @package  Nagios_Trap_Interface
 * @author   Nicholas Scott <nscott@nagios.com>
 * @license  GNU - http://www.gnu.org/licenses/gpl-2.0.html
 * @version  SVN: Alpha2
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

require "./include/defines/global.php";

require "./include/functions/functions.debug.php";

require "./include/classes/class.main.php";
require "./include/classes/class.frontend.php";
require "./include/classes/class.common.php";
require "./include/classes/class.mysqlDB.php";

require_once("/usr/local/nagiosxi/html/includes/common.inc.php");
pre_init();

// start session
init_session();

// grab GET or POST variables 
grab_request_vars();

// check prereqs
check_prereqs();

// check authentication -- this is done in individual pages
check_authentication();

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

$FRONTEND = new frontend($configINI);

$FRONTEND->openSite();

$FRONTEND->constructorHeader();


if ($MAIN->checkUser() == "0") {
    $FRONTEND->printError("AUTHENTIFICATION", null);
} else {
    $DATABASE = new database($configINI);
    $DATABASE->connect();

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
    print_r($_SESSION);
    $FRONTEND->constructorMain();
    $FRONTEND->constructorFooter();
}

$FRONTEND->closeSite();
$FRONTEND->printSite();
?>
