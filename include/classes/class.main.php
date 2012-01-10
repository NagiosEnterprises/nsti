<?php
###########################################################################
#
# class.common.php -  NagTrap class with main functions
#
# Copyright (c) 2006 - 2007 Michael Luebben (nagtrap@nagtrap.org)
# Last Modified: 16.12.2007
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
* This Class with funtions
*/
class main {
  
    /**
    * Read the Config-File and return a array
    *
    * @param string $configFile
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    */
    function readConfig($configFile) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method main::readConfig('.$configFile.')');
        $config = @parse_ini_file($configFile, TRUE) or die ("Could not open Configuration-File $configFile !");
        if (DEBUG&&DEBUGLEVEL&1) debug('End method GlobalBackendndomy::GlobalBackendndomy(): TRUE');
        return($config);
    }
    
    /**
    * Replace characters
    *
    * @param string $String
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
     */
    function replaceCharacters($string) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method main::replaceCharacters('.$string.')');
        $searchChr = array('', '', '', '', '', '', '');
        $searchChr = array('_a_', '_u_', '_o_', '_A_', '_U_', '_O_', '_sz_');
        $replaceChr = array('&auml;', '&uuml;', '&ouml;', '&Auml;', '&Uuml;', '&Ouml;', '&szlig;');
        foreach($searchChr as $key=>$character) {
            $string = str_replace($searchChr[$key], $replaceChr[$key], $string); 
        } 
        if (DEBUG&&DEBUGLEVEL&1) debug('End method main::replaceCharacters(): '.$string.'');
        return($string); 
    }
    
    /**
    * Read a XML-File and return a array
    *
    * @param string $XMLFile
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    */
    function readXML($xmlFile) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method main::readXML('.$xmlFile.')');
        $xml_parser = xml_parser_create();

        if(!($fp = fopen($xmlFile, "r"))) {
            die("Could not open XML-File $xmlFile !");
        }

        $data = fread($fp, filesize($xmlFile));
        fclose($fp);
        xml_parse_into_struct($xml_parser, $data, $vals, $index);
        xml_parser_free($xml_parser);

        $params = array();
        $level = array();
       
        foreach($vals as $xml_elem) {
            if($xml_elem['type'] == 'open') {
                if(array_key_exists('attributes',$xml_elem)) {
                    list($level[$xml_elem['level']],$extra) = array_values($xml_elem['attributes']);
                } else {
                $level[$xml_elem['level']] = $xml_elem['tag'];
                }
            }
            if($xml_elem['type'] == 'complete') {
                $start_level = 1;
                $php_stmt = '$params';
                while($start_level < $xml_elem['level']) {
                    $php_stmt .= '[$level['.$start_level.']]';
                    $start_level++;
                }
                $xml_elem['value'] = $this->replaceCharacters($xml_elem['value']);
                $php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];';
                eval($php_stmt);
            }
        }
        if (DEBUG&&DEBUGLEVEL&1) debug('End method main::readXML(): Array(...)');
        return($params);
    }
    
    /**
    * Check which table was used
    *
    * @param string $tableName
    * @param string $optionSeverity
    * @param string $selectTrap
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    */
    function setTable($tableName,$optionSeverity,$selectTrap) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method main::setTable('.$tableName.','.$optionSeverity.','.$selectTrap.')');
        global $configINI;
        if(!isset($tableName)) {
            $table['name'] = $configINI['database']['tableSnmptt'];
        }
       
        if($selectTrap == "UNKNOWN") {
            $table['name'] = $configINI['database']['tableSnmpttUnk'];
            $table['severity'] = "all";
        } elseif($optionSeverity == "OK" or $optionSeverity == "WARNING" or $optionSeverity == "CRITICAL") {
            $table['severity'] = $optionSeverity;
        } else {
            $table['severity'] = "all";
        }
       
        if($selectTrap == "ARCHIVED") {
            $table['name'] = $configINI['database']['tableSnmpttArchive'];
            $table['severity'] = "all";
        }  
        if (DEBUG&&DEBUGLEVEL&1) debug('End method main::setTable(): Array(...)');
        return($table);
    }
    
    /**
    * Checked logged in User, when authentification was enabled
    *
    * @param string $useAuthenfication
    * @param string $loggedInUser
    *
    * @author Michael Luebben <nagtrap@nagtrap.org>
    */
    function checkUser() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method main::checkUser()');
        global $configINI;
        $userAllowed = "0";
        if($configINI['global']['useAuthentification'] == "0") {
            $userAllowed = "1";  
            if (DEBUG&&DEBUGLEVEL&2) debug('Authentification was disabled in the config!');
        } else {
            $authorized = explode(",",$configINI['global']['allowedUser']);
            if (in_array($_SERVER['PHP_AUTH_USER'],$authorized)) {
                $userAllowed ="1";
                if (DEBUG&&DEBUGLEVEL&2) debug('Authentification ok! User '.$_SERVER['PHP_AUTH_USER'].' allowed!');
            } else {      
                if (DEBUG&&DEBUGLEVEL&2) debug('Authentification failed! User '.$_SERVER['PHP_AUTH_USER'].' not allowed!');
            }     
        }
        if (DEBUG&&DEBUGLEVEL&1) debug('End method main::checkUser(): '.$userAllowed);
        return($userAllowed);
    }
        
}
php?>
