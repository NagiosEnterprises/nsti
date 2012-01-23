<?php
###########################################################################
#
# class.filters.php -  NagTrap class with functions to create the index
#
# Copyright (c) 2006 - 2007 Michael Luebben (nagtrap@nagtrap.org)
#               2011 - 2012 Nicholas Scott (nscott@nagios.com)
# Last Modified: 1.20.2012
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
* This Class creates the filters.php site
*/

class filters extends frontend {
    
    /**
    * Constructor
    *
    * @param config $configINI
    *
    * @author Nicholas Scott <nscott@nagios.com>
    * 
    */  
    function __construct(&$configINI) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method filters::__construct()');
        // Load frontend's constructor
        parent::__construct($configINI);
        // Draw index.php's logic
        if (DEBUG&&DEBUGLEVEL&1) debug('End method filters::__construct()');
    }

    /**
    * Draws head, which is just the main div
    *
    * @author Nicholas Scott <nscott@nagios.com>
    * 
    */      
    function constructorHeader() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method filters::constructorHeader()');
        $this->site[] = "<div id='filtermain'>";
        if (DEBUG&&DEBUGLEVEL&1) debug('End method filters::constructorHeader()');
    }
    
    /* ------------------- MODE: VIEW SECTION ------------------------*/
    
    /**
    * constructorMain - Draws main div, which includes the table
    *
    * @author Nicholas Scott <nscott@nagios.com>
    * 
    */      
    function constructorViewMain() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method filters::constructorMain()');
        $filters = database::getFilters();

        $this->site[] = "<div id='addnewfilter'>";
        $this->drawAddFilter();
        $this->site[] = "</div> <!--Closes addnewfilter -->";
        $this->site[] = "<div id='filtertable'>";
        $this->drawFilterTable($filters);
        $this->site[] = "</div> <!--Closes filtertable -->";

        if (DEBUG&&DEBUGLEVEL&1) debug('End method filters::constructorMain()');
    }
    
    /**
    * constructorFooter - Closes div
    *
    * @author Nicholas Scott <nscott@nagios.com>
    * 
    */      
    function constructorFooter() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method filters::constructorFooter()');
        $this->site[] = "</div> <!--Closes filtermain -->";
        if (DEBUG&&DEBUGLEVEL&1) debug('End method filters::constructorFooter()');
    }
    
    /**
    * drawAddFilter - Draws the 'Add Filter' link on the filters page.
    * 
    * @author Nicholas Scott <nscott@nagios.com>
    *
    **/
    function drawAddFilter() {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method filters::drawAddFilter()');
        $this->site[] = "<form name='addnew' method='post' action='filters.php' >";
        $this->site[] = "   <input type='hidden' name='mode' value='edit' />";
        $this->site[] = "   <input type='image' id='add' src='./images/webset/action_add.png' />";
        $this->site[] = "   <label class='control' for='add'>Add New Filter</label>";
        $this->site[] = "</form>";
        if (DEBUG&&DEBUGLEVEL&1) debug('End method filters::drawAddFilter');
    }
    
    /**
    *
    * @author Nicholas Scott <nscott@nagios.com>
    *
    **/
    function drawFilterTable($filters) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method filters::drawFilterTable($filters)');
        $this->site[] = "<table class='MainTable'>";
        $this->site[] = "   <thead>";
        $this->site[] = "       <tr>";
        $this->site[] = "           <th class='controls'>Controls</th>";
        $this->site[] = "           <th class='name'>Name</th>";
        $this->site[] = "           <th class='description'>Description</th>";
        $this->site[] = "       </tr>";
        $this->site[] = "   </thead>";
        $this->site[] = "   <tbody>";
        if(!$filters) $this->site[] = "   <tr><td colspan=4>No Filters In Database</td></tr>";
        else {
            $rowcount = 0;
            foreach($filters as $filter) {
                $rowclass = ($rowcount) ? 'odd' : 'even';
                $rowcount = !$rowcount;
                $this->site[] = "<tr class='$rowclass'>";
                $this->site[] = "   <td>";
                $this->drawViewControls($filter->id);
                $this->site[] = "</td>";
                $this->site[] = "   <td>{$filter->filtername}</td>";
                $this->site[] = "   <td>{$filter->description}</td>";
                $this->site[] = "</tr>";
            }
        }
        $this->site[] = "   </tbody>";
        $this->site[] = "</table>";
        if (DEBUG&&DEBUGLEVEL&1) debug('End method filters::drawFilterTable($filters)');
    }
    
    /**
    *
    * @author Nicholas Scott <nscott@nagios.com>
    *
    **/
    function drawViewControls($filterid) {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method filters::drawViewControls()');
        $this->site[] = "<div class='controlpiece'>";
        $this->site[] = "<div class='controls delete'>";
        $this->site[] = "<form method='post' action='filters.php'>";
        $this->site[] = "   <input type='hidden' name='fid' value='{$filterid}' />";
        $this->site[] = "   <input type='hidden' name='mode' value='delete' />";
        $this->site[] = "   <input type='image' src='images/webset/action_delete.png' />";
        $this->site[] = "</form>";
        $this->site[] = "</div>";
        $this->site[] = "<div class='controls viewfilter'>";
        $this->site[] = "<form method='post' action='filters.php'>";
        $this->site[] = "   <input type='hidden' name='fid' value='{$filterid}' />";
        $this->site[] = "   <input type='hidden' name='mode' value='viewfilter' />";
        $this->site[] = "   <input type='image' src='images/webset/search.png'>";
        $this->site[] = "</form>";
        $this->site[] = "</div>";
        $this->site[] = "<div class='controls edit'>";
        $this->site[] = "<form method='post' action='filters.php'>";
        $this->site[] = "   <input type='hidden' name='fid' value='{$filterid}' />";
        $this->site[] = "   <input type='hidden' name='mode' value='edit' />";
        $this->site[] = "   <input type='image' src='images/webset/application.png' />";
        $this->site[] = "</form>";
        $this->site[] = "</div>";
        $this->site[] = "</div>";
        if (DEBUG&&DEBUGLEVEL&1) debug('End method filters::drawViewControls()');
    }
    
    
    /* ------------------- MODE: ADD/EDIT SECTION --------------------*/
    
    /**
    *
    * @author Nicholas Scott <nscott@nagios.com>
    *
    * @param filter
    * The filter that will be edited/added/viewed
    * 
    * @param status
    * This variable will be injected into the input text box HTML in order to make this
    * a general function that will also allow the user to view (but not
    * edit) the filter.
    * 
    * I avoided using a for loop when listing the inputs to have control
    * over the presentation.
    * 
    **/
    function constructorEditMain($filter,$status = '') {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method filters::constructorEditMain($filter)');
        // Logic for setting the $mode variable with is used for title
        // purposes only.
        $mode = ($filter->id) ? ($status) ? 'View' : 'Edit' : 'Add';
        /* Lets make an array that contains real column names as keys
         * with their values being arrays that contain meta information
         * about them.
         * 
         * nice - Nice names that look pretty
         * type - Type of entry. Will be used for created the query type
         *        ( contains, does not contains etc ) defaults to text.
         */
        $filterinfo = array(    'eventname'     => array( 'nice' => 'Event Name' )
                            ,   'eventid'       => array( 'nice' => 'Event ID' )
                            ,   'trapoid'       => array( 'nice' => 'Trap OID' )
                            ,   'enterprise'    => array( 'nice' => 'Enterprise OID' )
                            ,   'hostname'      => array( 'nice' => 'Hostname' )
                            ,   'category'      => array( 'nice' => 'Category' )
                            ,   'severity'      => array( 'nice' => 'Severity' )
                            ,   'formatline'    => array( 'nice' => 'Message' ) );
        $this->site[] = "<h2>$mode Filter</h2>";
        $this->site[] = "<div id='filterinput'>";
        $this->site[] = "<form name='filterinput action='filters.php' method='post'>";
        // First we'll draw the meta fields, filtername and description
        $this->site[] = "   <h3 class='filtermeta'>Filter Meta</h3>";
        $this->site[] = "   <hr />";
        $this->site[] = "   <div class='filterinput'>";
        // If we're editing then we need to carry the filter's id
        if($filter->id)
            $this->site[] = "   <input type='hidden' name='fid' value='{$filter->id}' />";
        $this->site[] = "   <input type='hidden' name='filters[type]' value='filters' />";
        $this->site[] = "   <label for='filtername'><span class='filterlabel'>Filter Name: </span></h4></label><br />";
        $this->site[] = "   <input type='text' id='filtername' name='filters[filtername]' value='{$filter->filtername}' {$status} />";
        $this->site[] = "   </div>";
        $this->site[] = "   <div class='filterinput'>";
        $this->site[] = "   <label for='description'><span class='filterlabel'>Description: </span></label><br />";
        $this->site[] = "   <input type='text' id='description' name='filters[description]' value='{$filter->description}' {$status} /><br />";        
        $this->site[] = "   </div>";
        $this->site[] = "   <br />";
        // Now we'll loop through the $filterinfo variable to draw our forms
        $this->site[] = "   <h3 class='filtermeta'>Filter Specifics</h3>";
        $this->site[] = "   <hr />";
        foreach($filterinfo as $name => $info) {
            $nicename = $info['nice'];
            $this->site[] = "   <div class='filterinput'>";
            $this->site[] = "   <label for='{$name}'><span class='filterlabel'>{$nicename}: </span></label><br />";
            $this->drawSelectMenu($name,$status,$filter);
            $this->site[] = "   <input type='text' id='{$name}' name='filters[{$name}]' value='{$filter->$name}' {$status} />";
            $this->site[] = "   </div>";
        }
        // The data selector is irregular so we'll draw that manually
        // TAKEN OUT UNTIL I CAN GET SNMPTT TO START LOGGING DATES AS DATES
        // NOT STRINGS
        //~ $this->site[] = "   <div class='filterinput'>";
        //~ $this->site[] = "   <label for='traptime1'><span class='filterlabel'>Time Received 1</span></label><br />";
        //~ $this->drawSelectMenu('traptime1',$status,'date');
        //~ $this->site[] = "   <input type='text' class='timepicker' id='traptime1' name='filters[traptime1]' value='{$filter->traptime1}' {$status} />";
        //~ $this->site[] = "   </div>";
        //~ $this->site[] = "   <div class='filterinput'>";
        //~ $this->site[] = "   <label for='traptime2'><span class='filterlabel'>Time Received 2</span></label><br />";
        //~ $this->drawSelectMenu('traptime2',$status,'date');
        //~ $this->site[] = "   <input type='text' class='timepicker' id='traptime2' name='filters[traptime2]' value='{$filter->traptime2}' {$status} />";
        //~ $this->site[] = "   </div>";        
        $this->site[] = "   <br /><br />";
        $visible = (!$status) ? '' : 'style="visibility:hidden"';
        $this->site[] = "   <div class='filterinput' $visible>";
        $this->site[] = "   <input type='Submit' name='mode' value='Save' />";
        $this->site[] = "   <a href='filters.php'><input type='button' name='cancel' value='Cancel' /></a>";
        $this->site[] = "   </div>";
        $this->site[] = "</form>";
        $this->site[] = "</div>";
        if (DEBUG&&DEBUGLEVEL&1) debug('End method filters::constructorEditMain($filter)');
        return;
    }
    
    /**
    *
    * @author Nicholas Scott <nscott@nagios.com>
    * 
    * @param name
    * 
    * Name of the column the query type will be used on
    * 
    * @param type
    * 
    * Type of select box to draw, defaults to text. Different types will
    * require different entries in the select boxes. 
    * 
    * @param status
    * 
    * Whether or not the select box is enabled
    *
    **/
    function drawSelectMenu($name,$status,$filter,$type = 'text') {
        if (DEBUG&&DEBUGLEVEL&1) debug('Start method filters::drawSelectMenu()');
        $namequery = $name."query";
        switch($type) {
            case 'text':
                $this->site[] = "<select name='filters[{$namequery}]' $status>";
                $selected = ($filter->$namequery == 'contain') ? 'selected' : '';
                $this->site[] = "   <option value='contain' $selected>contains</option>";
                $selected = ($filter->$namequery == 'notcontain') ? 'selected' : '';
                $this->site[] = "   <option value='notcontain' $selected>does not contain</option>";
                $selected = ($filter->$namequery == 'exactly') ? 'selected' : '';
                $this->site[] = "   <option value='exactly' $selected>is exactly</option>";
                $selected = ($filter->$namequery == 'notexactly') ? 'selected' : '';
                $this->site[] = "   <option value='notexactly' $selected>not is exactly</option>";
                $this->site[] = "</select>";
                break;
            case 'date':
                $this->site[] = "<select name='filters[{$namequery}]' $status>";
                $this->site[] = "   <option value='before'>before</option>";
                $this->site[] = "   <option value='after'>after</option>";
                $this->site[] = "</select>";
                break;
            }
        if (DEBUG&&DEBUGLEVEL&1) debug('End method filters::drawSelectMenu()');
        return;
    }

}
