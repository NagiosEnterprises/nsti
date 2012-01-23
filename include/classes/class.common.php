<?php

/**
 * class.common.php -  NagTrap class with functions for the frontend
 * Copyright (c) 2006 - 2007 Michael Luebben (nagtrap@nagtrap.org)
 * Last Modified: 16.12.2007
 * License:
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

class common {
  
    /**
    * Check the Request (OK, WARNING, ......)
    *
    * @param string $request
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    */
    function checkRequest($request) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::readRequest('.$request.')');
        if (!isset($request) or $request == "") {
            $retRequest = 'All';
        } else {
            $retRequest = $request;
        }
        if (DEBUG&&DEBUGLEVEL&1) debug('End method common::readRequest(): '.$retRequest);
        return($retRequest);  
    }
    
    /**
     * Helper function to draw the controls on the left column
     * 
     * @param trap
     * 
     * @author Nicholas Scott <nscott@nagios.com>
     * 
     */
    function doTrapControls($trap) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::doTrapControls');
        $retstr  = "<div class='controls'>\n";
        $retstr .= "<input type='checkbox' class ='selectors' name='trapIDs[]' value='{$trap['id']}' ".grab_request_var('sel').">\n";
        $retstr .= "</div>\n";
        $retstr .= "<div class='controls mark'>\n";
        // Mark a trap
        $retstr .= "    ".common::showTrapMenuIcons("mark",$trap['id'],grab_request_var('severity'),grab_request_var('hostname'));
        $retstr .= "</div>\n";
        $retstr .= "<div class='controls delete' >\n";
        // Delete a trap
        $retstr .= "    ".common::showTrapMenuIcons("delete",$trap['id'],grab_request_var('severity'),grab_request_var('hostname'));
        $retstr .= "</div>\n";
        $retstr .= "<div class='controls archive' >\n";
        // Archive a trap
        $retstr .= "    ".common::showTrapMenuIcons("archive",$trap['id'],grab_request_var('severity'),grab_request_var('hostname'));
        $retstr .= "</div>\n";
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::doTrapControls');
        return $retstr;
    }
    
    function doTrapsHeader() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::doTrapsHeader');
        global $languageXML;
        $state   = (empty($_SESSION['state'])) ? 'init' : $_SESSION['state'];
        $retstr  = "<thead>\n";
        // Draw titles and headings
        $retstr .= "    <tr>\n";
        $retstr .= "        <input type='hidden' id='state' value='{$state}' />\n";
        $retstr .= "        <th class='checkbox'><button id='initiate'>&#9660;</button><button id='minimize'>&#9658</button></th>\n";
        $retstr .= "        <th class='time'>{$languageXML['LANG']['MAIN']['TRAPTABLEHEADER']['TRAPTIME']}</th>\n";
        $retstr .= "        <th class='trapoid'>{$languageXML['LANG']['MAIN']['TRAPTABLEHEADER']['TRAPOID']}</th>\n";
        $retstr .= "        <th class='host'>{$languageXML['LANG']['MAIN']['TRAPTABLEHEADER']['HOST']}</th>\n";
        // If we are not in the UNKNOWN table then show all, otherwise skip
        if (grab_request_var('trapSelect') != 'UNKNOWN') {
        $retstr .= "        <th class='category'>{$languageXML['LANG']['MAIN']['TRAPTABLEHEADER']['CATEGORY']}</th>\n";
        $retstr .= "        <th class='severity'>{$languageXML['LANG']['MAIN']['TRAPTABLEHEADER']['SEVERITY']}</th>\n";
        }
        $retstr .= "        <th class='message'>{$languageXML['LANG']['MAIN']['TRAPTABLEHEADER']['MESSAGE']}</th>\n";
        $retstr .= "    </tr>\n";
        // Draw search boxes
        $retstr .= "    <tr id='searchrow'>\n";
        $retstr .= "        <form method='get' action='./index.php'>\n";
        $retstr .= "            <th><input type='submit' value='Search' class='searches' /></th>\n";
        $retstr .= "            <th><input type='submit' value='Reset' onclick='clearForm(this.form);' class='searches' /></th>\n";
        $retstr .= "            <th><input type='text' name='searchTrapoid' value='".grab_request_var('searchTrapoid')."' class='searches' /></th>\n";
        $retstr .= "            <th><input type='text' name='searchHostname' value='".grab_request_var('searchHostname')."' class='searches' /></th>\n";
        if (grab_request_var('trapSelect') != 'UNKNOWN') {
            $retstr .= "            <th><input type='text' name='searchCategory' value='".grab_request_var('searchCategory')."' class='searches' /></th>\n";
            $retstr .= "            <th><input type='text' name='searchSeverity' value='".grab_request_var('searchSeverity')."' class='searches' /></th>\n";
        }
        $retstr .= "            <th><input type='text' name='searchMessage' value='".grab_request_var('searchMessage')."' class='searches' /></th>\n";
        $retstr .= "        <input id='state' type='hidden' name='state' value='".grab_request_var('state')."' />\n";
        $retstr .= "        </form>\n";
        $retstr .= "    </tr>\n";
        $retstr .= "</thead>\n";
        if (DEBUG&&DEBUGLEVEL&1) debug('End method common::doTrapsHeader');
        return $retstr;
    }
    
    function doTrapsBody($traps) {
        global $configINI;
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::doTrapsHeader');
        $retstr  = "<tbody>\n";
        $retstr .= "    <form name='selectors' action='./index.php' method='post'>\n";
        if ($traps) {
            $rowbool = true;
            foreach ($traps as $trap) {
                $rowread  = ($trap['trapread'])    ? 'read': 'unread';
                $rowclass = ($rowbool = !$rowbool) ? 'odd' : 'even';
                $hlink    = "./index.php?trapSelect=".grab_request_var('trapSelect')."&severity=".grab_request_var('severity')."&category=".rawurlencode(grab_request_var('category'))."&hostname={$trap['hostname']}";
                switch(strtolower($trap['severity'])){
                    case 'normal':
                        $bg = GREEN;
                        break;
                    case 'ok':
                        $bg = GREEN;
                        break;
                    case 'warning':
                        $bg = YELLOW;
                        break;
                    case 'critical':
                        $bg = RED;
                        break;
                    default:
                        $bg = ORANGE;
                        break;
                    }
                // Save the Trap-Message and delete " from Trap-Output
                $trap['orgFormatline'] = str_replace('"',"",$trap['formatline']);
                $arrIllegalCharJavabox = explode(",",$configINI['global']['illegalCharJavabox']);
                foreach  ($arrIllegalCharJavabox as $illegalChar) {
                    $trap['orgFormatline'] = str_replace($illegalChar,"",$trap['orgFormatline']);
                }
                // Cut Trap-Message if that set in the Configurationfile
                if ($configINI['global']['cutTrapMessage'] != "") {
                    if (strlen($trap['formatline']) > $configINI['global']['cutTrapMessage']) {
                    $trap['formatline'] = substr($trap['formatline'],0,$configINI['global']['cutTrapMessage']).'.....';
                    }
                }
                $retstr .= "        <tr class='{$rowclass} {$rowread}'>\n";
                $retstr .= "            <td class='checkbox'>".common::doTrapControls($trap)."</td>\n";
                $retstr .= "            <td class='time'>{$trap['traptime']}</td>\n";
                $retstr .= "            <td class='trapoid'>{$trap['trapoid']}</td>\n";
                $retstr .= "            <td class='host'><a href='{$hlink}'>{$trap['hostname']}</a></td>\n";
                if (grab_request_var('trapSelect') != 'UNKNOWN') {
                    $retstr .= "            <td class='category'>{$trap['category']}</a></td>\n";
                    $retstr .= "            <td class='severity' style='background-color:{$bg}'>{$trap['severity']}</a></td>\n";
                }
                $retstr .= "            <td class='message'>{$trap['formatline']}</a></td>\n";
                $retstr .= "        </tr>\n";
                
            }
        }
        else {
            $retstr .= "        <tr><td colspan='10'>No traps found in database.</td></tr>\n";
        }
        $retstr .= "</tbody>\n";
        if (DEBUG&&DEBUGLEVEL&1) debug('End method common::doTrapsHeader');
        return $retstr;
    }
                
            
    /**
    * Check if the Option selected
    *
    * @param string $optionValue
    * @param string $type
    * @param string $sel
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    */
    function selected($optionValue,$type,$sel) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::selected('.$optionValue.','.$type.','.$sel.')');
        $state = "";
        if($optionValue == $type) {
            $state = $sel;
        }
        if (DEBUG&&DEBUGLEVEL&1) debug('End method common::selected(): '.$state);
        return($state);
    }

    /**
    * Read Trap-Information from database
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    */
    function readTrapInfo() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::readTrapInfo()');
        global $table;
        $DATABASE = new database($configINI);
        $DATABASE->connect();
        $trapInfo = $DATABASE->infoTrap($table['name']);
        if(!isset($trapInfo['first'])) {
            $trapInfo['first'] = $trapInfo['last'];  
        }
        if (DEBUG&&DEBUGLEVEL&1) debug('End method common::readTrapInfo(): Array(...)');
        return($trapInfo);
    }
    
    /**
    * Check if use unknown-Traps in the Database
    *
    * @param boolean $useUnknownTraps
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    */
    function checkIfEnableUnknownTraps($useUnknownTraps) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::checkIfEnableUnknownTraps('.$useUnknownTraps.')');
        global $languageXML;
        unset($option);
        if($useUnknownTraps == "1") {
            $retstr = "<option value='UNKNOWN' ".common::selected("UNKNOWN",grab_request_var('trapSelect'),'selected="selected"')." >{$languageXML['LANG']['HEADER']['OPTBOX']['SELECTTRAPVALUE']['TRAPUNKNOWN']}</option>\n";
        }
        if (DEBUG&&DEBUGLEVEL&1) debug('End method common::checkIfEnableUnknownTraps()');
        return $retstr;   
    }
    
    /**
    * Print error-lines
    *
    * @param string $lines
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @author Nicholas Scott <nscott@nagios.com
    */

    function printErrorLines($errorLines,$systemError) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::printErrorLines('.$errorLines.','.$systemError.')');
        $this->site[] = "   <div class='errorDescription'>";
        foreach ($errorLines as $lines)
            $this->site[] = "   ".$lines."<br />";  
        if ($systemError)
            $this->site[] = "      Error: <i>".$systemError."</i>";
        $this->site[] = "   </div>";
        if (DEBUG&&DEBUGLEVEL&1) debug('End method common::printErrorLines()');
    }

    /**
    * Create-Link (Icon) in the frontend to delete, mark or archive one trap
    *
    * @params string $menuIcon 
    * @params string $trapID
    * @params string $severity
    * @params string $hostname  
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @author Nicholas Scott <nscott@nagios.com>
    */
    function showTrapMenuIcons($menuIcon,$trapID,$severity,$hostname) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::showTrapMenuIcons('.$menuIcon.','.$trapID.','.$severity.','.$hostname.')');
        global $configINI,$languageXML;
        if (grab_request_var('trapSelect') == "ARCHIVED" && $menuIcon != "delete") {
            $empty_string = '';
            return $empty_string;
        }
        if ($menuIcon == "mark") {
            if (grab_request_var('trapSelect') == "" or grab_request_var('trapSelect') == "all") {
                $imgsrc = $configINI['global']['images'].$configINI['global']['iconStyle'].'/mark.png';
                $title  = $languageXML['LANG']['MAIN']['TRAPTABLEENTRY']['OPTIONREAD'];
                $action = 'mark';
            }
        } 
        elseif ($menuIcon == "archive") {
            if (grab_request_var('trapSelect') == "" or grab_request_var('trapSelect') == "all") {
                $imgsrc = $configINI['global']['images'].$configINI['global']['iconStyle'].'/archive.png';
                $title  = $languageXML['LANG']['MAIN']['TRAPTABLEENTRY']['OPTIONARCHIVE'];                
                $action = 'archive';
            }
        } 
        elseif ($menuIcon == "delete") {
                $imgsrc = $configINI['global']['images'].$configINI['global']['iconStyle'].'/delete.png';
                $title  = $languageXML['LANG']['MAIN']['TRAPTABLEENTRY']['OPTIONDELETE'];            
                $action = 'delete';
        }
        $retstr  = "<a href =";
        $retstr .= "'./index.php?action={$action}";
        $retstr .= "&trapSelect=".grab_request_var('trapSelect');
        $retstr .= "&trapID={$trapID}";
        $retstr .= "&severity={$severity}";
        $retstr .= "&hostname={$hostname}";
        $retstr .= "'>";
        $retstr .= "<img src='{$imgsrc}' ";
        $retstr .= "title='{$title}'";
        $retstr .= "></a>\n";
        if (DEBUG&&DEBUGLEVEL&1) debug('End method common::showTrapMenuIcons()');
        return $retstr;
    }

    /**
    * Create-Link (Icon) in the frontend to delete, mark or archive more as one trap
    *
    * @params string $menuIcon 
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @author Nicholas Scott <nscott@nagios.com>
    * 
    */
    function showTrapMenuIconFooter($menuIcon) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::showTrapMenuIconFooter('.$menuIcon.')');
        global $configINI, $languageXML;
        $trapSelect = grab_request_var('trapSelect');
        $onlydelete = ( $trapSelect == 'UNKNOWN' || $trapSelect == 'ARCHIVED' ) ? 1 : 0;
        if ($menuIcon == "mark" && !$onlydelete) {
            if (grab_request_var('trapSelect') == "" or grab_request_var('trapSelect') == "all") {
                $src    = $configINI['global']['images'].$configINI['global']['iconStyle'].'/mark.png';
                $name   = 'markTraps[0]';
                $title  = $languageXML['LANG']['MAIN']['TRAPTABLEENTRY']['OPTIONREAD'];
                $alt    = 'Mark';
            }     
        } elseif ($menuIcon == "archive" && !$onlydelete) {
            if (grab_request_var('trapSelect') == "" or grab_request_var('trapSelect') == "all") {
                $src    = $configINI['global']['images'].$configINI['global']['iconStyle'].'/archive.png';
                $name   = 'archiveTraps[0]';
                $title  = $languageXML['LANG']['MAIN']['TRAPTABLEENTRY']['OPTIONARCHIVE'];
                $alt    = 'Archive';
            }   
        } elseif ($menuIcon == "delete") {
            $src    = $configINI['global']['images'].$configINI['global']['iconStyle'].'/delete.png';
            $name   = 'deleteTraps[0]';
            $title  = $languageXML['LANG']['MAIN']['TRAPTABLEENTRY']['OPTIONDELETE'];
            $alt    = 'Delete';
        }
        else {
            return;
        }
        $this->site[] = "<input type='image' src='{$src}' name='{$name}' title='{$title}' />"; 
        if (DEBUG&&DEBUGLEVEL&1) debug('End method common::showTrapMenuIcons()');
    }   

    /**
    * Read Traps from Database and create Buttons for pages with limited trap entrys
    *
    * @author Joerg Linge
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @author Nicholas Scott <nscott@nagios.com>
    */
    function readTraps() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::readTraps()');
        global $configINI, $hostname, $FRONTEND;
        $step = $_SESSION['perpage'];
        if (!grab_request_var('site')){
            $site = 0;
            $from = 0;
            $to = $step;
            $limit = "0,$step";
        } 
        else {
            $site = grab_request_var('site');
            $from = ($site*$step);
            $to = (($site*$step)+$step);
            $limit = ($site*$step).",".$step;
        }
      
        $DATABASE = new database($configINI);
        $DATABASE->connect();

        // Read traps from database
        $traps = $DATABASE->readTraps($limit);
        $total = $DATABASE->countTraps();

        $count = sizeof($traps);
        
        $type = (!grab_request_var('type')) ? 'all' : grab_request_var('type'); 
        //~ if (!isset(grab_request_var('type'))) {
            //~ $type = "all";
        //~ }
        //~ else {
            //~ $type = grab_request_var('type');
        

        $this->site[] = '<div id="navigation">';
        $this->site[] = '	<span id="leftarrow">';
        if ($site != 0)
            $this->site[] = '   <a href="index.php?site='.($site-1).'&trapSelect='.grab_request_var('trapSelect').'&severity='.grab_request_var('severity').'&category='.rawurlencode(grab_request_var('category')).'&hostname='.grab_request_var('hostname').'&searchTrapoid='.grab_request_var('searchTrapoid').'&searchHostname='.grab_request_var('searchHostname').'&searchCategory='.grab_request_var('searchCategory').'&searchSeverity='.grab_request_var('searchSeverity').'&searchMessage='.grab_request_var('searchMessage').'&state='.grab_request_var('state').'"><img src="'.$configINI['global']['images'].$configINI['global']['iconStyle'].'/previous.png" /></a>'; 
        else
            $this->site[] = '   <img src="'.$configINI['global']['images'].$configINI['global']['iconStyle'].'/previousgray.png" />'; 
        $this->site[] = '	</span>';
        $this->site[] = '   <span id="pageindex">';
        $this->site[] = ( $to < $total ) ? "<b>{$from} - {$to}</b>" : "<b>{$from} - {$total}</b>"; 
        $this->site[] = '	</span>';
        $this->site[] = '	<span id="rightarrow">';
        if ($to < $total)
            $this->site[] = '  	<a href="index.php?site='.($site+1).'&trapSelect='.grab_request_var('trapSelect').'&severity='.grab_request_var('severity').'&category='.rawurlencode(grab_request_var('category')).'&hostname='.grab_request_var('hostname').'&searchTrapoid='.grab_request_var('searchTrapoid').'&searchHostname='.grab_request_var('searchHostname').'&searchCategory='.grab_request_var('searchCategory').'&searchSeverity='.grab_request_var('searchSeverity').'&searchMessage='.grab_request_var('searchMessage').'&state='.grab_request_var('state').'"><img src="'.$configINI['global']['images'].$configINI['global']['iconStyle'].'/next.png" /></a>';
        else
            $this->site[] = '  	<img src="'.$configINI['global']['images'].$configINI['global']['iconStyle'].'/nextgray.png" />';
        $this->site[] = '   </span>';
        $this->site[] = '</div>';	   
        if (DEBUG&&DEBUGLEVEL&1) debug('End method common::readTraps(): Array(...)');
        return($traps);
    }
    

    /**
    * Create entry for Category, if selected table not "unknown"
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    *
    */
    function createCategoryEntry() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::createCategoryEntry()');
        global $table,$languageXML;
        if ($table['name'] != "snmptt_unknown") {
            $this->site[] = '						<td>';
            $this->site[] = '                     	<tr>';
            $this->site[] = '                        		<TD VALIGN="top" ALIGN="left" CLASS="filterName">'.$languageXML['LANG']['HEADER']['FILTER']['CATEGORY'].':</TD>';
            $this->site[] = '                        		<TD VALIGN="top" ALIGN="left" CLASS="filterName">';
            $this->site[] = '                            		'.common::checkRequest(rawurldecode(grab_request_var('category')));
            $this->site[] = '                     	</tr>';
            $this->site[] = '                     </td>';		  
        }
        if (DEBUG&&DEBUGLEVEL&1) debug('End method common::createCategoryEntry()');
    }

    /**
    * Create filter menu for categories
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    * @auther Nicholas Scott <nscott@nagios.com>
    */
    function createCategoryFilter() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::createCategoryFilter()');
        global $table,$languageXML;
        $retstr = "";
        if ($table['name'] != "snmptt_unknown") {
            $retstr .= "<td class='left'>{$languageXML['LANG']['HEADER']['OPTBOX']['CATEGORY']}:</td>\n";
            $retstr .= "<td class='right'>\n";
            $retstr .= "    <select name='category'>\n";
            $retstr .= "        <option value='' ".common::selected("",grab_request_var('category'),"selected")." >{$languageXML['LANG']['HEADER']['OPTBOX']['OPTION']['VALUEALL']}</option>\n";
            $DATABASE = new database($configINI);
            $DATABASE->connect();
            $allCategory = $DATABASE->readCategory($table['name']);
            if (isset($allCategory)) {
                foreach ($allCategory as $category) {
                    $retstr .= "        <option value=".rawurlencode($category)." ".common::selected($category,rawurldecode(grab_request_var('category')),'selected="selected"').">{$category}</option>\n"; 
                }
            }
            $retstr .= "    </select>";
            $retstr .= "</td>";
        }
        if (DEBUG&&DEBUGLEVEL&1) debug('End method common::createCategoryFilter()');
        return $retstr;
    }

    /**
    * Create box for attentions
    *
    * @param string $attentionMsg
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    */
    function printAttention($attentionMsg) {
        global $configINI;
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::printAttention('.$attentionMsg.')');
        $this->site[] = '<TABLE BORDER="0" WIDTH="100%" CELLPADDING="0" CELLSPACING="0">';
        $this->site[] = '   <TR>';
        $this->site[] = '      <TD WIDTH="35%" ALIGN="right"><IMG SRC="'.$configINI['global']['images'].$configINI['global']['iconStyle'].'/attention.png" BORDER="0"></TD>';
        $this->site[] = '      <TD ALIGN="center">'.$attentionMsg.'</TD>';
        $this->site[] = '      <TD WIDTH="35%" ALIGN="left"><IMG SRC="'.$configINI['global']['images'].$configINI['global']['iconStyle'].'/attention.png" BORDER="0"></TD>';
        $this->site[] = '   </TR>';
        $this->site[] = '</TABLE>';
        if (DEBUG&&DEBUGLEVEL&1) debug('End  method common::printAttention()');
    }
    /**
    * Check for any attentions
    *
    * 1. Debugging enabled
    * 2. ...
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    */
    function checkForAttentions() {
        global $languageXML;
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::checkForAttentions()');
        //if (DEBUG) common::printAttention($languageXML['LANG']['HEADER']['ATTENTION']['ENABLEDDEBUG']);
        if (DEBUG&&DEBUGLEVEL&1) debug('End method common::checkForAttentions()');
    }
    
    /**
     * determinePageMenu
     * 
     * Added option select menu for traps per page. To add options
     * to page add to the $step_list
     * 
     * @author Nicholas Scott <nscott@nagios.com>
     **/
    function determinePageMenu() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method main::determinePageMenu()');
        global $configINI;
        $retstr = "";
        $_SESSION['perpage'] = (grab_request_var('perpage')) ? grab_request_var('perpage') : $configINI['global']['step'];
        $step = $_SESSION['perpage'];
        $step_list = array( '10' , '20' , '30' , '40' , '50' , '100' );
        foreach( $step_list as $step_option ) {
            $retstr .= ( $step == $step_option ) ? "<option value='{$step_option}' selected='selected'>{$step_option}</option>" : "<option value='{$step_option}'>{$step_option}</option>'";
        }
        return $retstr;
    }
}
?>
