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

// Simple AJAX catcher, needs to be moved somewhere reasonable
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

// Set table
$table = $MAIN->setTable($tableName, grab_request_var('severity'), grab_request_var('trapSelect'));

class index extends frontend {
    
    public $site;
    public $DATABASE;
    /**
    * Constructor
    *
    * @param config $configINI
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @author Nicholas Scott <nscott@nagios.com>
    */  
    function __construct(&$configINI,$table) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method index::__construct()');
        // Make sure necessary SESSION vars are declared
        if(!array_key_exists('perpage',$_SESSION))
            $_SESSION['perpage'] = $configINI['global']['step'];
        // Load frontend's constructor
        parent::__construct($configINI);
        $_SESSION['applied_servers'] = array_intersect($_SESSION['applied_servers'],$this->DATABASE->dbconnections);
        if(!array_key_exists('applied_servers',$_SESSION))
            $_SESSION['applied_servers'] = $this->dbconnections;
        if (DEBUG&&DEBUGLEVEL&1) debug('End method index::__construct()');
    }
    
    /**
    *
    * Function is called by the index constructor. Handles are REQUEST
    * checks and then calls constructHeader, Body and Footer.
    * 
    * @author Nicholas Scott <nscott@nagios.com>
    *
    **/
    function route_request() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method index::route_request()');
        global $table;
        
        // If set action, then mark, delete or archive a trap in the database
        if (grab_request_var('action') == "mark" or grab_request_var('action') == "delete" or grab_request_var('action') == "archive") {
            $this->DATABASE->handleTrap(grab_request_var('action'), grab_request_var('trapID'), $table['name'], grab_request_var('bank')); 
        }
        
        // Mark more as one trap 
        if (grab_request_var('markTraps') AND grab_request_var('trapIDs')) {
            foreach (grab_request_var('trapIDs') as $trapID) {
                $this->COMMON->explodedelimited($trapID,&$trapid,&$bank);
                $this->DATABASE->handleTrap("mark", $trapid, $table['name'],$bank); 
            }
        }
        
        // Delete more as one trap 
        if (grab_request_var('deleteTraps') AND grab_request_var('trapIDs')) {
            foreach (grab_request_var('trapIDs') as $trapID) {
                $this->COMMON->explodedelimited($trapID,&$trapid,&$bank);
                $this->DATABASE->handleTrap("delete", $trapID, $table['name'],$bank);
            }
        }

        // Delete more as one trap 
        if (grab_request_var('archiveTraps') AND grab_request_var('trapIDs')) {
            foreach (grab_request_var('trapIDs') as $trapID ) {
                $this->COMMON->explodedelimited($trapID,&$trapid,&$bank);
                $this->DATABASE->handleTrap("archive", $trapID, $table['name'],$bank);
            }
        }
            
        // Add a filter given by $_POST
        if (grab_request_var('updatefilter') != NULL) {
            $_SESSION['applied_filters'] = array();
            $filter_id = grab_request_var('updatefilter');
            foreach($filter_id as $id){
                if($id != 'empty') {
                    $requested_filter = $this->DATABASE->getItem('filters',$id);
                    $_SESSION['applied_filters'][$id] = $requested_filter['filtername'];
                }
            }
        }
        
        if (grab_request_var('updateserver') != NULL) {
            $server_request = $this->COMMON->removeFromArray(grab_request_var('updateserver'),'empty');
            $_SESSION['applied_servers'] = array_intersect($server_request,$this->DATABASE->dbconnections);
        }
            
        if (grab_request_var('boolean')) {
            $boolean = grab_request_var('boolean');
            if ($boolean == 'AND' || $boolean == 'OR')
                $_SESSION['boolean_combiner'] = $boolean;
        }
            
        if (grab_request_var('perpage')) {
            $_SESSION['perpage'] = grab_request_var('perpage');
        }
            
        if (DEBUG&&DEBUGLEVEL&1) debug('End method route::request()');
    }
    
    /**
    * Constructor for the header
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @auther Nicholas Scott <nscott@nagios.com>
    */
    function constructorHeader() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method index::constructorHeader()');
        $trapinfo = common::readTrapInfo();
        $this->site[] = "<div id='header'>";
        $this->createInfoBox();     // Current Trap Log Box
        $this->createOptBox($trapinfo);      // Trap Selection Box #trapselect
        $this->site[] = "</div> <!-- closes header -->";
        if (DEBUG&&DEBUGLEVEL&1) debug('End method index::constructorHeader()');
    }
    
    /**
    * Create a Info-Box
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @author Nicholas Scott <nscott@nagios.com>
    */
    function createInfoBox() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method index::createInfoBox()');
        global $languageXML;
        $this->site[] = "<div id='infobox'>";
        $this->site[] = "    <table class='OptionsTable' id='InfoTable'>";
        // Create the filter section of the table
        $this->createFilter();
        $this->site[] = "    </table>";
        $this->site[] = "</div>";
        $this->site[] = "<!-- Closes Info Box -->";       
        if (DEBUG&&DEBUGLEVEL&1) debug('End method index::createInfoBox()');
    }
    
        
    
    /**
    * Create a Filter-Box
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @author Nicholas Scott <nscott@nagios.com>
    */
    function createFilter() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method index::createFilter()');
        global $hostname, $languageXML, $configINI;
        $applied_filters = $_SESSION['applied_filters'];
        $applied_servers = $_SESSION['applied_servers'];
        $this->createServerSelectBox($applied_servers);
        $this->createFilterSelectBox($applied_filters);
        //~ $this->createRadioBoolean();
        $this->site[] = "</tbody>";
        $this->site[] = "<!-- Closes filterbox -->";       
        if (DEBUG&&DEBUGLEVEL&1) debug('End method index::createFilter()');
    }
    
    /**
    *
    * @author Nicholas Scott <nscott@nagios.com>
    *
    **/
    function createServerSelectBox($applied_servers) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method index::createServerSelectBox()');
        $databases = $this->DATABASE->dbconnections;
        $this->site[] = "<thead>";
        $this->site[] = "<tr class='odd'>";
        $this->site[] = "   <form method='post' name='updateserver' action='./index.php'>";
        $this->site[] = "   <td class='left'>Server Select</td>";
        $this->site[] = "   <td class='right'><a href='#' onclick='document.forms[0].submit()'>Update</a></td> ";
        $this->site[] = "</tr>";
        $this->site[] = "</thead>";
        $this->site[] = "   <td class='left' colspan='2'>";
        $this->site[] = "       <form method='post' action='./index.php'>";
        $this->site[] = "       <input type='hidden' name='updateserver[]' value='empty' />";
        $this->site[] = "       <select multiple size='5' name='updateserver[]' class='updateselect' id='updateserver'>";
        foreach($databases as $database) {
            if($applied_servers && in_array($database,$applied_servers))
                $selected = "selected";
            else
                $selected = "";
            $this->site[] = "<option value='{$database}' {$selected}>{$database}</option>";
        }
        $this->site[] = "       </select>";
        $this->site[] = "    </td>";
        $this->site[] = "</tr>";
        if (DEBUG&&DEBUGLEVEL&1) debug('End method index::createServerSelectBox()');
    }
    
    
    
    /**
    * Is called to populate the select list located in the filter menu.
    *
    * @author Nicholas Scott <nscott@nagios.com>
    * 
    * @param applied_filters
    * 
    * List of filters (from $_SESSION) being applied to query results.
    *
    **/
    function createFilterSelectBox($applied_filters) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method index::createFilterSelectBox');
        // Set variables to nicer, easier to type names
        $all_filters     = $this->DATABASE->getType('filters');
        // Begin drawing our HTML
        $enable_form  = ($all_filters) ? '' : 'disabled';
        $and          = ($_SESSION['boolean_combiner'] == 'AND') ? "<u>AND</u>" : "<a href='./index.php?boolean=AND'>AND</a>";
        $or           = ($_SESSION['boolean_combiner'] == 'OR') ? "<u>OR</u>" : "<a href='./index.php?boolean=OR'>OR</a>";
        $this->site[] = "<thead>";
        $this->site[] = "<tr class='odd'>";
        $this->site[] = "   <td class='left'>Filters | {$and} {$or}</td>";
        $this->site[] = "   <td class='right'><a href='#' onclick='document.forms[0].submit()'>Update</a></td> ";
        $this->site[] = "</tr>";
        $this->site[] = "</thead>";
        $this->site[] = "<tr class='odd'>";
        $this->site[] = "   <td class='left' colspan='2' >";
        $this->site[] = "       <form method='post' action='./index.php'>";
        $this->site[] = "       <select multiple size='5' name='updatefilter[]' class='updateselect' id='updatefilter' {$enable_form}>";
        // If there are any filters left in $all_filters, draw them
        if($all_filters)
            foreach($all_filters as $id => $array) {
                if($applied_filters && array_key_exists($id,$applied_filters))
                    $selected = "selected";
                else
                    $selected = "";
                $this->site[] = "<option value='{$id}' {$selected}>{$array['filtername']}</option>";
            }
        // Otherwise just say there are no filters.
        else
            $this->site[] = "<option>No Filters Created</option>";
        // Finish off the form and finish
        $this->site[] = "       </select>";
        $this->site[] = "       <input type='hidden' name='updatefilter[]' value='empty' />";
        $this->site[] = "       </form>";
        $this->site[] = "   </td>";
        $this->site[] = "</tr>";
        $this->site[] = "   <tr class='even'>";
        $this->site[] = "       <td colspan='2' class='left'><a href='./index.php?updatefilter[]=empty'>Remove All</a>";
        $this->site[] = "   </tr>";
        if (DEBUG&&DEBUGLEVEL&1) debug('End method index::createFilterSelectBox');
    }
    
    
    /**
    * Create a Date Information
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @author Nicholas Scott <nscott@nagios.com>
    * 
    * This function is called to populate the upper right hand corner
    * "nav
    */
    function createDateInfoBox($trapInfo) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method index::createNavBox()');
        global $languageXML;
        // If trap table is empty, set dates to be displayed to None.
        $first   = (isset($trapInfo['first'])) ? $trapInfo['first'] : "None";
        $last    = (isset($trapInfo['last'])) ? $trapInfo['last'] : "None";
        $this->site[] = "<thead>";
        $this->site[] = "    <tr>";
        $this->site[] = "        <td colspan='5'>{$languageXML['LANG']['HEADER']['NAVBOX']['DATES']}</td>";
        $this->site[] = "    </tr>";
        $this->site[] = "</thead>";       
        $this->site[] = "<tr class='odd'>";    
        $this->site[] = "    <td class='left'>{$languageXML['LANG']['HEADER']['NAVBOX']['BEGIN']}</td>";   
        $this->site[] = "    <td class='right'>{$first}</td>";
        $this->site[] = "</tr>";   
        $this->site[] = "<tr class='even'>";
        $this->site[] = "    <td class='left'>{$languageXML['LANG']['HEADER']['NAVBOX']['LAST']}</td>";   
        $this->site[] = "    <td class='right'>{$last}</td>";
        $this->site[] = "</tr>";    
        $this->site[] = "<!-- Closes navbox -->";
        if (DEBUG&&DEBUGLEVEL&1) debug('End method index::createNavBox()');
    }
      
    /**
    * Create a Box for Options
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @author Nicholas Scott <nscott@nagios.com>
    */
    function createOptBox($trapInfo) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method index::createOptBox()');
        global $languageXML;
        $view   = 0;
        $total = $this->DATABASE->countTraps();
        $this->DATABASE->readTraps($view);
        $this->site[] = "<div id='trapselect'>";
        $this->site[] = "     <table class='OptionsTable id='OptTable'>";
        $this->createDateInfoBox($trapInfo);
        $this->site[] = "        <thead>";
        $this->site[] = "            <tr>";
        $this->site[] = "                <td colspan='2'>{$languageXML['LANG']['HEADER']['INFOBOX']['CURRENTTRAPLOG']}</td>";    
        $this->site[] = "            </tr>";
        $this->site[] = "        </thead>";    
        $this->site[] = "        <tbody>";     
        $this->site[] = "            <tr class='odd'>";    
        $this->site[] = "                <td class='left'>{$languageXML['LANG']['HEADER']['INFOBOX']['TOTALFILTER']} / {$languageXML['LANG']['HEADER']['INFOBOX']['TOTALTRAPS']}</td>";
        $this->site[] = "                <td class='right'>{$view} / {$total}</td>";     
        $this->site[] = "            </tr>";
        $this->site[] = "       </tbody>";
        $this->site[] = "         <thead>";
        $this->site[] = "             <tr>";
        $this->site[] = "                 <td colspan='5'>Trap Selection</td>";   
        $this->site[] = "             </tr>";
        $this->site[] = "         </thead>";   
        $this->site[] = "         <form method='get' action='./index.php'>";
        $this->site[] = "         <tr class='even'>";
        $this->site[] = "             <td class='title'> {$languageXML['LANG']['HEADER']['OPTBOX']['SELECTTRAP']} :</td>";
        $this->site[] = "             <td class='right'>";
        $this->site[] = "                 <select name='trapSelect'>";
        $this->site[] = "                     <option value='all' ".common::selected('all',grab_request_var('trapSelect'),'selected="selected"')." > {$languageXML['LANG']['HEADER']['OPTBOX']['SELECTTRAPVALUE']['TRAPACTUEL']}</option>";
        $this->site[] = "                     <option value='ARCHIVED' ".common::selected('ARCHIVED',grab_request_var('trapSelect'),"selected='selected'")." > {$languageXML["LANG"]["HEADER"]["OPTBOX"]["SELECTTRAPVALUE"]["TRAPARCHIVED"]}</option>";
        $this->site[] = "                     ". common::checkIfEnableUnknownTraps($this->configINI['global']['useUnknownTraps']);
        $this->site[] = "                 </select>";
        $this->site[] = "             </td>";
        $this->site[] = "         </tr>";
        $this->site[] = "         <tr class='odd'>";
        $this->site[] = "             <td>{$languageXML['LANG']['HEADER']['OPTBOX']['SEVERITYDETAIL']}:</td>";
        $this->site[] = "             <td class='right'>";
        $this->site[] = "                 <select name='severity'>";
        $this->site[] = "                     <option value='' ".common::selected('',grab_request_var('severity'),"selected='selected'")." > {$languageXML["LANG"]["HEADER"]["OPTBOX"]["OPTION"]["VALUEALL"]}</option>";
        $this->site[] = "                     <option value='OK' ".common::selected('OK',grab_request_var('severity'),"selected='selected'")." >Traps OK</option>";
        $this->site[] = "                     <option value='WARNING' ".common::selected('WARNING',grab_request_var('severity'),"selected='selected'")." >Traps Warning</option>";
        $this->site[] = "                     <option value='CRITICAL' ".common::selected('CRITICAL',grab_request_var('severity'),"selected='selected'")." >Traps Critical</option>";
        $this->site[] = "                 </select>";
        $this->site[] = "             </td>";
        $this->site[] = "         </tr>";
        $this->site[] = "         <tr class='even'>";
        $this->site[] = "             <td>{$languageXML['LANG']['HEADER']['OPTBOX']['PERPAGE']}</td>";
        $this->site[] = "             <td class='right'>";
        $this->site[] = "                 <select name='perpage'>";
        $this->site[] = "                     ".common::determinePageMenu();
        $this->site[] = "                 </select>";
        $this->site[] = "             </td>";
        $this->site[] = "         </tr>";
        $this->site[] = "         <tr class='odd'>";      
        $this->site[] = "             ".common::createCategoryFilter();
        $this->site[] = "         </tr>";     
        $this->site[] = "         <tr class='even'>";
        $this->site[] = "             <td class='left'>{$languageXML['LANG']['HEADER']['OPTBOX']['OLDERENTRIESFIRST']}:</td>";
        $this->site[] = "             <td class='right'><input type='checkbox' name='oldestfirst' ".common::selected('on',grab_request_var('oldestfirst'),"checked")." ></td>";
        $this->site[] = "         </tr>";
        $this->site[] = "         <tr class='odd'>";
        $this->site[] = "             <td class='left'></td>";
        $this->site[] = "             <td class='right'><input type='submit' value='{$languageXML['LANG']['HEADER']['OPTBOX']['UPDATEBUTTON']}' ></td>";
        $this->site[] = "             <input type='hidden' name='hostname' value='".grab_request_var('hostname')."'>";
        $this->site[] = "         </tr>";
        $this->site[] = "         </form>";
        $this->site[] = "     </table>";
        $this->site[] = "</div> <!-- closes trapselect -->";
        if (DEBUG&&DEBUGLEVEL&1) debug('End method index::createOptBox()');

    }


    // ======================== Contructor and functions for the main of the  =======================
    
    /**
    * Constructor for the main
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @auther Nicholas Scott <nscott@nagios.com>
    */
    function constructorMain() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method index::contructorMain()');
        global $languageXML, $traps, $hostname, $configINI;;

        // Check database connection and read traps from database
        $this->site[] = "<div id='trapbody'>";
        $this->site[] = "   <div id='trapbodyheader'>";
        $traps = common::readTraps();
        $this->site[] = "   </div>";
        $this->site[] = "<!-- Closes trapbodyheader -->";
        $this->site[] = "   <div id='traps'>";
        $this->site[] = "       <table class='MainTable'>";
        $this->site[] = "           ".common::doTrapsHeader();
        $this->site[] = "           ".common::doTrapsBody($traps);
        $this->site[] = "       </table>";
        $this->site[] = "   </div>";
        $this->site[] = "   <!-- closes traps -->";
        $this->site[] = "</div>";
        $this->site[] = "<!-- closes trapbody -->";     
        if (DEBUG&&DEBUGLEVEL&1) debug('End method index::contructorMain()');
    }
    
    // ======================= Contructor and functions for the footer of the index ====================== 
    
    /**
    * Constructor for the main
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @author Nicholas Scott <nscott@nagios.com>
    */
    function constructorFooter() {
       if (DEBUG&&DEBUGLEVEL&1) debug('Start method index::constructorFooter()');
       global $configINI, $languageXML, $hostname, $table;
       $this->site[] = "<table width='100%' border='0'>";
       $this->site[] = "  <tr>";
       $this->site[] = "     <td class='checkbox'>";
       $this->site[] = "        <div class='controls'>";
       $this->site[] = "        <img src='{$configINI['global']['images']}{$configINI['global']['iconStyle']}/arrow.png' border='0'>";
       $this->site[] = "        <input type='checkbox' name='checkbox' value='checkbox' class='bigone' > &nbsp; (Mark all)";
       $this->site[] = "        </div>";
       $this->site[] = "        <div class='controls mark'>";
       common::showTrapMenuIconFooter('mark');
       $this->site[] = "        </div>";
       $this->site[] = "        <div class='controls delete'>";
       common::showTrapMenuIconFooter('delete');
       $this->site[] = "        </div>";
       $this->site[] = "        <div class='controls archive'>";
       common::showTrapMenuIconFooter('archive');
       $this->site[] = "        </div>";
       $this->site[] = "        <input type='hidden' name='oldestfirst' value='".grab_request_var('oldestfirst')."' />";
       $this->site[] = "        <input type='hidden' name='severity' value='".grab_request_var('severity')."' />";
       $this->site[] = "        <input type='hidden' name='category' value='".grab_request_var('category')."' />";
       $this->site[] = "        <input type='hidden' name='hostname' value='".grab_request_var('hostname')."' />";
       $this->site[] = "        <input type='hidden' name='trapSelect' value='".grab_request_var('trapSelect')."' />";
       $this->site[] = "        <input type='hidden' name='bank' value='".grab_request_var('bank')."' />";
       $this->site[] = "      </td>";      
       $this->site[] = "   </tr>";
       $this->site[] = "</table>";
       $this->site[] = "</form>";
       if (DEBUG&&DEBUGLEVEL&1) debug('End method index::constructorFooter()');
    }
}

$FRONTEND = new index($configINI,$table);
$FRONTEND->route_request();
$FRONTEND->constructorHeader();
$FRONTEND->constructorMain();
$FRONTEND->constructorFooter();
$FRONTEND->closeSite();
$FRONTEND->printSite();

?>
