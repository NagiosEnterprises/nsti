<?php
###########################################################################
#
# class.index.php -  NSTI class with functions to create the index
#
# Copyright (c) 2006 - 2007 Michael Luebben (nagtrap@nagtrap.org)
#               2011 - 2012 Nicholas Scott (nscott@nagios.com)
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
* This Class creates the index.php site
*/

class index extends frontend {
    var $site;
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
        // Load frontend's constructor
        parent::__construct($configINI);
        // Make sure necessary SESSION vars are declared
        if(!array_key_exists('perpage',$_SESSION))
            $_SESSION['perpage'] = $configINI['global']['step'];
        // Draw index.php's logic
        if (DEBUG&&DEBUGLEVEL&1) debug('End method index::__construct()');
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
        $trapInfo = common::readTrapInfo();
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
        $this->site[] = "<thead>";
        $this->site[] = "    <tr>";
        $this->site[] = "        <td colspan='2'>{$languageXML['LANG']['HEADER']['FILTER']['DISPLAYFILTERS']}:</td>";    
        $this->site[] = "    </tr>";
        $this->site[] = "</thead>";
        $this->site[] = "<tbody>";
        $this->site[] = "<tr class='even'>";
        $this->site[] = "    <td class='left'>{$languageXML['LANG']['HEADER']['FILTER']['HOST']}:</td>";
        $this->site[] = "    <td class='right'>".common::checkRequest(grab_request_var('hostname'))."</td>";
        $this->site[] = "</tr>";
        $this->site[] = "<tr class='odd'>";
        $this->site[] = "    <td class='left'>{$languageXML['LANG']['HEADER']['FILTER']['SEVERITYLEVEL']}:</td>";
        $this->site[] = "    <td class='right'>".common::checkRequest(grab_request_var('severity'))."</td>";
        $this->site[] = "</tr>";
        $this->site[] = "<tr class='even'>";
        $this->site[] = "    <td class='left'>{$languageXML['LANG']['HEADER']['FILTER']['CATEGORY']}:</td>";
        $this->site[] = "    <td class='right'>".common::checkRequest(rawurldecode(grab_request_var('category')))."</td>";
        $this->site[] = "</tr>";
        $this->site[] = "<div id='filterbox'>";
        $this->createFilterSelectBox($applied_filters);
        //~ $this->createRadioBoolean();
        $this->site[] = "</tbody>";
        $this->site[] = "<!-- Closes filterbox -->";       
        if (DEBUG&&DEBUGLEVEL&1) debug('End method index::createFilter()');
    }

    /**
    * creatRadioBoolean
    * 
    * Determines which boolean combiner will be used for the filters
    * 
    * @author Nicholas Scott <nscott@nagios.com>
    *
    **/
    function createRadioBoolean() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method index::createRadioBoolean()');
        if(!isset($_SESSION['boolean_combiner'])) $_SESSION['boolean_combiner'] = 'OR';
        $this->site[] = "<tr class='odd'>";
        $this->site[] = "   <td class='left'>";
        $this->site[] = "       <label for='boolean'>Boolean Combine</label>";
        $this->site[] = "   </td>";
        $this->site[] = "   <td class='right'>";
        $this->site[] = "       <form name='booleancombine' method='post' action=''>";
        $select = ($_SESSION['boolean_combiner'] == 'AND') ? 'checked' : '';
        $this->site[] = "       <input type='radio' name='boolean' value='AND' onClick='this.form.submit();' {$select} />AND";
        $select = ($_SESSION['boolean_combiner'] == 'OR') ? 'checked' : '';
        $this->site[] = "       <input type='radio' name='boolean' value='OR' onClick='this.form.submit();' {$select} />OR&nbsp;";
        $this->site[] = "       </form>";
        $this->site[] = "   </td>";
        $this->site[] = "</tr>";
        if (DEBUG&&DEBUGLEVEL&1) debug('End method index::createRadioBoolean()');
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
        $all_filters     = database::getType('filters');
        // Begin drawing our HTML
        $enable_form  = ($all_filters) ? '' : 'disabled'; 
        $this->site[] = "<tr class='odd'>";
        $this->site[] = "   <td class='left' colspan='2' >";
        $this->site[] = "       <form method='post' action='./index.php'>";
        $this->site[] = "       <select multiple size='6' name='updatefilter[]' id='updatefilter' {$enable_form}>";
        // If there are any filters left in $all_filters, draw them
        if($all_filters)
            foreach($all_filters as $id => $array) {
                if(array_key_exists($id,$applied_filters))
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
        $this->site[] = "   </td>";
        $this->site[] = "</tr>";
        $this->site[] = "<tr class='odd'>";
        $this->site[] = "   <td class='left'>";
        $this->site[] = "       <input type='Submit' value='Update' />";
        $this->site[] = "       </form>";
        $this->site[] = "   </td>";
        $this->site[] = "   <td class='right'>";
        $this->site[] = "       <form name='booleancombine' method='post' action=''>";
        $select = ($_SESSION['boolean_combiner'] == 'AND') ? 'checked' : '';
        $this->site[] = "       <input type='radio' name='boolean' value='AND' onClick='this.form.submit();' {$select} />AND";
        $select = ($_SESSION['boolean_combiner'] == 'OR') ? 'checked' : '';
        $this->site[] = "       <input type='radio' name='boolean' value='OR' onClick='this.form.submit();' {$select} />OR&nbsp;";
        $this->site[] = "       </form>";
        $this->site[] = "   </td>";
        $this->site[] = "   <tr class='even'>";
        $this->site[] = "       <td colspan='2' class='left'><a href='./index.php?updatefilter[]=empty'>Remove All</a>";
        $this->site[] = "   </tr>";
        $this->site[] = "</tr>";
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
        $view   = true;
        $total = database::countTraps();
        database::readTraps($view);
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
       $this->site[] = "      </td>";      
       $this->site[] = "   </tr>";
       $this->site[] = "</table>";
       $this->site[] = "</form>";
       if (DEBUG&&DEBUGLEVEL&1) debug('End method index::constructorFooter()');
    }
}
?>
