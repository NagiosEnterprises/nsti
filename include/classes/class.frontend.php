<?php
###########################################################################
#
# class.common.php -  NagTrap class with functions to create the frontend
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
* This Class creates the Web-Frontend for the Nagtrap Frontend
*/

class frontend {
    var $site;
    /**
    * Constructor
    *
    * @param config $configINI
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    */  
    function frontend(&$configINI) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::contructor()');
        $this->configINI = &$configINI;
        if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::contructor()');
    }

    // ==================================== Functions to create the page ====================================
    
    /**
    * Open a Web-Site in a Array site[].
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @auther Nicholas Scott <nscott@nagios.com>
    */
    function openSite() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::openSite()');
        $this->site[] = "<html>";
        $this->site[] = "   <head>";
        $this->site[] = "       <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>";
        $this->site[] = "       <title>Nagios SNMP Trap Interface - ".CONST_VERSION."</title>";
        $this->site[] = "       <script type='text/javascript' src='./include/js/nagtrap.js'></script>";
        $this->site[] = "       <script type='text/javascript' src='./include/js/overlib.js'></script>";
        $this->site[] = "       <script type='text/javascript' src='./include/js/prototype.js'></script>";
        $this->site[] = "       <script type='text/javascript' src='./include/js/scriptaculous.js'></script>";
        $this->site[] = "       <script type='text/javascript' src='./include/js/jquery.js'></script>";
        $this->site[] = "       <script type='text/javascript' src='./include/js/start.js'></script>";
        $this->site[] = "       <link href='./include/css/nagtrap.css' rel='stylesheet' type='text/css'>";                
        $this->site[] = "   </head>";
        $this->site[] = "   <body>";
        $this->site[] = "       <div id='all'>";
        if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::openSite()');
    }
    
    /**
    * Closed a Web-Site in the Array site[]
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @author Nicholas Scott <nscott@nagios.com>
    */
    function closeSite() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::closeSite()');
        $this->site[] = "       </div> <!-- Closes all -->";
        $this->site[] = "   </body>";
        $this->site[] = "</html>";
        if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::closeSite()');
    }
    
    /**
    * Create a Web-Side from the Array site[].
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    */
    function printSite() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::printSite()');
        foreach ($this->site as $row)
            echo $row."\n";
        if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::printSite()');
        if (DEBUG&&DEBUGLEVEL&4) debugFinalize();
    }
    
    // ======================= Contructor and functions for the header of the frontend ======================
    
    /**
    * Constructor for the header
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @auther Nicholas Scott <nscott@nagios.com>
    */
    function constructorHeader() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::constructorHeader()');
        global $table;
        $retstr  = "<div id='header'>";
        $retstr .= "    ".$this->createInfoBox();     // Current Trap Log Box
        $retstr .= "    ".$this->createDescribe();  // Show Description/Title
        $retstr .= "    ".$this->createOptBox();      // Trap Selection Box #trapselect
        $retstr .= "</div> <!-- closes header -->";
        $this->site[] = $retstr;
        if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::constructorHeader()');
    }
    
    function createDescribe() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::createDescribe()');
        $retstr  = "<div id='description'>\n";
        $retstr .= "    <p class='nstititle'>>Nagios SNMP Trap Interface<</p>\n";
        $retstr .= "    <p class='info'>Version: ".CONST_VERSION."</p>\n";
        $retstr .= "    <p class='info'>Total Traps: ".database::countTraps()."</p>\n";
        $retstr .= "</div>\n";
        if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::createDescribe()');
        return $retstr;
    }
    /**
    * Create a Info-Box
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @author Nicholas Scott <nscott@nagios.com>
    */
    function createInfoBox() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::createInfoBox()');
        global $languageXML;
        $trapInfo = common::readTrapInfo();
        $retstr  = "<div id='infobox'>\n";
        $retstr .= "    <table class='OptionsTable'>\n";
        $retstr .= "        <thead>\n";
        $retstr .= "            <tr>\n";
        $retstr .= "                <td colspan='2'>{$languageXML['LANG']['HEADER']['INFOBOX']['CURRENTTRAPLOG']}</td>\n";    
        $retstr .= "            </tr>\n";
        $retstr .= "        </thead>\n";    
        $retstr .= "        <tbody>\n";     
        $retstr .= "            <tr class='odd'>\n";    
        $retstr .= "                <td class='left'> {$languageXML['LANG']['HEADER']['INFOBOX']['LASTUPDATE']}</td>\n";
        $retstr .= "                <td class='right'>{$trapInfo['last']}</td>\n";     
        $retstr .= "            </tr>\n";
        // Creates date box
        $retstr .= "            ".frontend::createDateInfoBox($trapInfo);
        // Create the filter section of the table
        $retstr .= "            ".frontend::createFilter();
        $retstr .= "        </tbody>\n";
        $retstr .= "    </table>\n";
        $retstr .= "</div>\n";
        $retstr .= "<!-- Closes Info Box -->\n";       
        if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::createInfoBox()');
        return $retstr;
    }
    
    /**
    * Create a Filter-Box
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @author Nicholas Scott <nscott@nagios.com>
    */
    function createFilter() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::createFilter()');
        global $hostname, $languageXML, $configINI;
        $retstr  = "";
        $retstr .= "<thead>\n";
        $retstr .= "    <tr>\n";
        $retstr .= "        <td colspan='2'>{$languageXML['LANG']['HEADER']['FILTER']['DISPLAYFILTERS']}:</td>\n";    
        $retstr .= "    </tr>\n";
        $retstr .= "</thead>\n";
        $retstr .= "<tr class='even'>\n";
        $retstr .= "    <td class='left'>{$languageXML['LANG']['HEADER']['FILTER']['HOST']}:</td>\n";
        $retstr .= "    <td class='right'>".common::checkRequest(grab_request_var('hostname'))."</td>\n";
        $retstr .= "</tr>\n";
        $retstr .= "<tr class='odd'>\n";
        $retstr .= "    <td class='left'>{$languageXML['LANG']['HEADER']['FILTER']['SEVERITYLEVEL']}:</td>\n";
        $retstr .= "    <td class='right'>".common::checkRequest(grab_request_var('severity'))."</td>\n";
        $retstr .= "</tr>\n";
        $retstr .= "<tr class='even'>\n";
        $retstr .= "    <td class='left'>{$languageXML['LANG']['HEADER']['FILTER']['CATEGORY']}:</td>\n";
        $retstr .= "    <td class='right'>".common::checkRequest(rawurldecode(grab_request_var('category')))."</td>\n";
        $retstr .= "</tr>\n";
        $retstr .= "<tr class='odd'>\n";
        $retstr .= "    <td class='left'><a href='./index.php'><b><i>{$languageXML['LANG']['HEADER']['FILTER']['RESET']}</i></b></a></td>\n";
        $retstr .= "</tr>\n";
        $retstr .= "<!-- Closes filterbox -->\n";       
        if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::createFilter()');
        return $retstr;
    }  
    
    /**
    * Create a Date Information
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * 
    * This function is called to populate the upper right hand corner
    * "nav
    */
    function createDateInfoBox($trapInfo) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::createNavBox()');
        global $languageXML;
        $retstr  = "";
        $retstr .= "<thead>\n";
        $retstr .= "    <tr>\n";
        $retstr .= "        <td colspan='5'>{$languageXML['LANG']['HEADER']['NAVBOX']['DATES']}</td>\n";
        $retstr .= "    </tr>\n";
        $retstr .= "</thead>\n";       
        $retstr .= "<tr class='odd'>\n";    
        $retstr .= "    <td class='left'>{$languageXML['LANG']['HEADER']['NAVBOX']['BEGIN']}</td>\n";   
        $retstr .= "    <td class='right'>{$trapInfo['first']}</td>\n";
        $retstr .= "</tr>\n";   
        $retstr .= "<tr class='even'>\n";
        $retstr .= "    <td class='left'>{$languageXML['LANG']['HEADER']['NAVBOX']['LAST']}</td>\n";   
        $retstr .= "    <td class='right'>{$trapInfo['last']}</td>\n";
        $retstr .= "</tr>\n";    
        $retstr .= "<!-- Closes navbox -->\n";
        if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::createNavBox()');
        return $retstr;
    }
      
    /**
    * Create a Box for Options
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @author Nicholas Scott <nscott@nagios.com>
    */
    function createOptBox() {
       if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::createOptBox()');
       global $languageXML;
       $retstr  = "";
       $retstr .= "<div id='trapselect'>\n";
       $retstr .= "     <table class='OptionsTable'>\n";
       $retstr .= "         <thead>\n";
       $retstr .= "             <tr>\n";
       $retstr .= "                 <td colspan='5'>Trap Selection</td>\n";   
       $retstr .= "             </tr>\n";
       $retstr .= "         </thead>\n";   
       $retstr .= "         <form method='get' action='./index.php'>\n";
       $retstr .= "         <tr class='even'>\n";
       $retstr .= "             <td class='title'> {$languageXML['LANG']['HEADER']['OPTBOX']['SELECTTRAP']} :</td>\n";
       $retstr .= "             <td class='right'>\n";
       $retstr .= "                 <select name='trapSelect'>\n";
       $retstr .= "                     <option value='all' ".common::selected('all',grab_request_var('trapSelect'),'selected="selected"')." > {$languageXML['LANG']['HEADER']['OPTBOX']['SELECTTRAPVALUE']['TRAPACTUEL']}</option>\n";
       $retstr .= "                     <option value='ARCHIVED' ".common::selected('ARCHIVED',grab_request_var('trapSelect'),"selected='selected'")." > {$languageXML["LANG"]["HEADER"]["OPTBOX"]["SELECTTRAPVALUE"]["TRAPARCHIVED"]}</option>\n";
       $retstr .= "                     ". common::checkIfEnableUnknownTraps($this->configINI['global']['useUnknownTraps']);
       $retstr .= "                 </select>\n";
       $retstr .= "             </td>\n";
       $retstr .= "         </tr>\n";
       $retstr .= "         <tr class='odd'>\n";
       $retstr .= "             <td>{$languageXML['LANG']['HEADER']['OPTBOX']['SEVERITYDETAIL']}:</td>\n";
       $retstr .= "             <td class='right'>\n";
       $retstr .= "                 <select name='severity'>\n";
       $retstr .= "                     <option value='' ".common::selected('',grab_request_var('severity'),"selected='selected'")." > {$languageXML["LANG"]["HEADER"]["OPTBOX"]["OPTION"]["VALUEALL"]}</option>\n";
       $retstr .= "                     <option value='OK' ".common::selected('OK',grab_request_var('severity'),"selected='selected'")." >Traps OK</option>\n";
       $retstr .= "                     <option value='WARNING' ".common::selected('WARNING',grab_request_var('severity'),"selected='selected'")." >Traps Warning</option>\n";
       $retstr .= "                     <option value='CRITICAL' ".common::selected('CRITICAL',grab_request_var('severity'),"selected='selected'")." >Traps Critical</option>\n";
       $retstr .= "                 </select>\n";
       $retstr .= "             </td>\n";
       $retstr .= "         </tr>\n";
       $retstr .= "         <tr class='even'>\n";
       $retstr .= "             <td>{$languageXML['LANG']['HEADER']['OPTBOX']['PERPAGE']}</td>\n";
       $retstr .= "             <td class='right'>\n";
       $retstr .= "                 <select name='perpage'>\n";
       $retstr .= "                     ".common::determinePageMenu();
       $retstr .= "                 </select>\n";
       $retstr .= "             </td>\n";
       $retstr .= "         </tr>\n";
       $retstr .= "         <tr class='odd'>\n";      
       $retstr .= "             ".common::createCategoryFilter();
       $retstr .= "         </tr>\n";     
       $retstr .= "         <tr class='even'>\n";
       $retstr .= "             <td class='left'>{$languageXML['LANG']['HEADER']['OPTBOX']['OLDERENTRIESFIRST']}:</td>\n";
       $retstr .= "             <td class='right'><input type='checkbox' name='oldestfirst' ".common::selected('on',grab_request_var('oldestfirst'),"checked")." ></td>\n";
       $retstr .= "         </tr>\n";
       $retstr .= "         <tr class='odd'>\n";
       $retstr .= "             <td class='left'></td>\n";
       $retstr .= "             <td class='right'><input type='submit' value='{$languageXML['LANG']['HEADER']['OPTBOX']['UPDATEBUTTON']}' ></td>\n";
       $retstr .= "             <input type='hidden' name='hostname' value='".grab_request_var('hostname')."'>\n";
       $retstr .= "         </tr>\n";
       $retstr .= "         </form>\n";
       $retstr .= "     </table>\n";
       $retstr .= "</div> <!-- closes trapselect -->\n";
       if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::createOptBox()');
       return $retstr;
    }

    /**
    * Create a error-message
    *
    * @param string $error
    * @param string $systemError
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @author Nicholas Scott <nscott@nagios.com>
    */ 
    function printError($error,$systemError) {
       if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::printError('.$error.','.$systemError.')');
       global $errorXML;
       $this->site[] = "<hr>";
       $this->site[] = "   <div class='errorMessage'>{$errorXML['ERROR'][$error]['MESSAGE']}</div>";
       common::printErrorLines($errorXML['ERROR'][$error]['DESCRIPTION'],$systemError);
       $this->site[] = "</hr>";
       if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::printError()');
    }

    // ======================== Contructor and functions for the main of the frontend =======================
    
    /**
    * Constructor for the main
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @auther Nicholas Scott <nscott@nagios.com>
    */
    function constructorMain() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::contructorMain()');
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
        if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::contructorMain()');
    }
    
    /**
    * Create a Java Infobox
    *
    * @param string $formatline
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    */ 
    function javaInfoBox($formatline) {
       if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::javaInfoBox('.$formatline.')');
       $infoBox = 'onmouseover="return overlib(\'';
       $infoBox .= $formatline;
       $infoBox .= '\', CAPTION, \'Trap-Message\', VAUTO);" onmouseout="return nd();" ';
       if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::javaInfoBox(): '.$infoBox);
       return($infoBox);
    }
    
    /**
    * Show traps
    *
    * @param array  $trap
    * @param string $rowColor
    * @param string $styleLine
    * 
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @auther Nicholas Scott <nscott@nagios.com>
    */ 
    //~ function showTrap($trap,$rowColor,$styleLine) {
        //~ if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::javaInfoBox('.$formatline.')');
        //~ global $configINI, $languageXML, $hostname;
        //~ $this->site[] = "<tr class='{$rowColor}'>";
        //~ $this->site[] = "<td class='checkbox'>";
        //~ $this->site[] = common::doTrapControls($trap);
        //~ $this->site[] = "</td>";
        //~ $this->site[] = "<td width='7%' class='{$rowColor}'><p {$styleLine}>{$trap['traptime']}</p></td>";
        //~ $this->site[] = '        <td width="10%" class="'.$rowColor.'"><p '.$styleLine.'>'.$trap['trapoid'].'</p></td>';
        //~ // Select host
        //~ $this->site[] = '      <td width="10%" class="'.$rowColor.'"><a href="./index.php?trapSelect='.$_REQUEST['trapSelect'].'&severity='.$_REQUEST['severity'].'&category='.rawurlencode($_REQUEST['category']).'&hostname='.$trap['hostname'].'"><P '.$styleLine.'>'.$trap['hostname'].'</p></a></td>';
        //~ common::showTrapFields("entry",$trap,$rowColor,$styleLine);
        //~ $this->site[] = '      <td width="*" class="'.$rowColor.'"><p '.$styleLine.' '.$this->javaInfoBox($trap['orgFormatline']).'class="formatline">'.htmlentities($trap['formatline']).'</p></td>';
        //~ $this->site[] = '   </tr>';
        //~ if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::javaInfoBox()');
    //~ }
    
    // ======================= Contructor and functions for the footer of the frontend ====================== 
    
    /**
    * Constructor for the main
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @author Nicholas Scott <nscott@nagios.com>
    */
    function constructorFooter() {
       if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::constructorFooter()');
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
       $this->site[] = "      </td>";      
       $this->site[] = "   </tr>";
       $this->site[] = "</table>";
       $this->site[] = "</form>";
       if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::constructorFooter()');
    }
}
?>
