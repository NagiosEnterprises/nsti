<?php
###########################################################################
#
# class.nagios.php -  NSTI class with functions to create the nagios setup
#
# Copyright (c) 2006 - 2007 Michael Luebben (nagtrap@nagtrap.org)
# Copyright (c) 2011 - 2012 Nicholas Scott (nscott@nagios.com)
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

class nagios extends setup {
    var $site;
    /**
    * Constructor
    *
    * @param config $configINI
    *
    * @author Nicholas Scott <nscott@nagios.com>
    */  
    function __construct(&$configINI) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::__construct()');
        parent::__construct($configINI);
        if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::__construct()');
    }

    // ==================================== Functions to create the page ====================================
    
    /**
    *
    * @author Nicholas Scott <nscott@nagios.com>
    *
    **/
    function constructorHeader() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method nagios::constructorHeader()');
        $this->site[] = "<div class='secondarycontent'>";
        if (DEBUG&&DEBUGLEVEL&1) debug('End method nagios::constructorHeader()');
    }
    
    /**
    *
    * @author Nicholas Scott <nscott@nagios.com>
    *
    **/
    function constructorViewMain() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method nagios::constructorMain()');
        $instances = database::getType('instances');
        $this->site[] = "<div id='addnewsecondary'>";
        $this->site[] = "   <form name='addnew' method='post' action='filters.php' >";
        $this->site[] = "       <input type='hidden' name='mode' value='edit' />";
        $this->site[] = "       <input type='image' id='add' src='./images/webset/action_add.png' />";
        $this->site[] = "       <label class='control' for='add'>Add New Filter</label>";
        $this->site[] = "   </form>";
        $this->site[] = "</div> <!--Closes addnewsecondary -->";
        $this->site[] = "<div id='secondarytable'>";
        $this->drawInstanceTable($instances);
        $this->site[] = "</div> <!--Closes secondarytable -->";       
        if (DEBUG&&DEBUGLEVEL&1) debug('End method nagios::constructorMain()');
    }
    
    /**
    *
    * @author Nicholas Scott <nscott@nagios.com>
    *
    **/
    function constructorFooter() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method nagios::constructorFooter');
        $this->site[] = "</div>";
        if (DEBUG&&DEBUGLEVEL&1) debug('End method nagios::constructorFooter');
    }
    
    
    
}
?>
