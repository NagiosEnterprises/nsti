<?php
/**
 * index.php -  Nagios SNMP Trap Interface
 * 
 * PHP Version 5.2
 * 
 * @category SNMP_Management
 * @package  Nagios_SNMP_Trap_Interface
 * @author   Nicholas Scott <nscott@nagios.com>
 * @license  GNU - http://www.gnu.org/licenses/gpl-2.0.html
 * @version  SVN: RC1.4
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
require "./include/functions/request.inc.php";
require "./include/functions/redbean/rb.php";

require "./include/classes/class.frontend.php";
require "./include/classes/class.main.php";
require "./include/classes/class.filters.php";
require "./include/classes/class.common.php";
require "./include/classes/class.mysqlDB.php";


// Start our session
session_start();

grab_request_vars();

if (grab_request_var('state')){
    $_SESSION['state'] = grab_request_var('state');
    die();
}
// Create a new page
$MAIN = new main();

// Read config.ini.php
$configINI = $MAIN->readConfig(CONST_MAINCFG);

// Read error.xml for error-messages
$errorXML = $MAIN->readXML("./include/xml/language/".$configINI['global']['language']."/error.xml");

// Read language 
$languageXML = $MAIN->readXML("./include/xml/language/".$configINI['global']['language']."/main.xml");

$DATABASE = new database($configINI);
$DATABASE->connect();

$FRONTEND = new filters($configINI);

if ($MAIN->checkUser() == "0") {
    $FRONTEND->printError("AUTHENTIFICATION", null);
} 
else {
    $FRONTEND->constructorHeader();
    // Lets grab our server variables so they are more readable
    $mode       = grab_request_var('mode','view');
    $filterid   = grab_request_var('fid',0);
    // Based on the mode variable, draw filters.php
    switch($mode) {
        case 'delete':
            /* Check to see if the filter we are deleting is currently in
             * use on the filter page. If it is, unset it. */
            if(isset($_SESSION['applied_filters'][$filterid]))
                unset($_SESSION['applied_filters'][$filterid]);
            $DATABASE->deleteItem('filters',$filterid);
            $FRONTEND->constructorViewMain();
            break;
        case 'viewfilter':
            $status = 'disabled';
            $filter = $DATABASE->getItem('filters',$filterid);
            $FRONTEND->constructorEditMain($filter,$status);
            break;            
        case 'Save':
            $formarray = grab_request_var('filters');
            $DATABASE->saveForm($formarray,$filterid);
            $FRONTEND->constructorViewMain();
            break;
        case 'view':
            $FRONTEND->constructorViewMain();
            break;
        case 'edit':
            $filter = $DATABASE->getItem('filters',$filterid);
            $FRONTEND->constructorEditMain($filter,$status);
            break;
        default:
            break;
        }
            
    $FRONTEND->constructorFooter();
}

$FRONTEND->closeSite();
$FRONTEND->printSite();

?>
