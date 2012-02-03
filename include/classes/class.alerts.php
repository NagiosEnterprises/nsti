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
* This Class is the template for all pages created for NSTI.
* 
* When adding a new page, simply extend this class, put your functions
* for creating content in functions and call them in the constructor for
* the class. Obviously, you can do it your own way, but thats they way
* I'm trying for this project. See index.php and includes/classes/class.index.php
* for examples.
* 
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
    function __construct(&$configINI) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::__construct()');
        $this->configINI = &$configINI;
        $this->openSite();
        if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::__construct()');
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
        $this->site[] = "       <script type='text/javascript' src='./include/js/jquery.js'></script>";
        $this->site[] = "       <script type='text/javascript' src='./include/js/anytimec.js'></script>";
        $this->site[] = "       <script type='text/javascript' src='./include/js/start.js'></script>";
        $this->site[] = "       <script type='text/javascript' src='./include/js/nsti.js'></script>";
        $this->site[] = "       <link href='./include/css/nsti.css' rel='stylesheet' type='text/css' />";
        $this->site[] = "       <link href='./include/css/anytimec.css' rel='stylesheet' type='text/css' />";                
        $this->site[] = "   </head>";
        $this->site[] = "   <body>";
        $this->site[] = "       <div id='logo'>NSTI v".CONST_VERSION."</div>";
        $this->site[] = "       <div id='all'>";
        $this->site[] = "           <div id='topmenu'>";
        $this->site[] = "               <div id='topmenuleft'>";
        $this->drawTopLeftMenu();
        $this->site[] = "               </div>";
        $this->site[] = "           </div>";
        if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::openSite()');
    }
    /**
     * Draws top left menu links.
     * 
     * @author Nicholas Scott
     */
    function drawTopLeftMenu() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::drawTopLeftMenu()');
        $this->site[] = "<a href='./index.php'>Traps</a>";
        $this->site[] = "<a href='./filters.php'>Filters</a>";
        if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::drawTopLeftMenu()');
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

}
?>
