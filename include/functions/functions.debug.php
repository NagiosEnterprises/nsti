<?php
###########################################################################
#
# functions.debug.php - Functions for debugging NagTrap
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
* Functions for debugging NagTrap
*/
define('DEBUGSTART',microtime_float());

/**
* Write debug message to file
*
* @param string $debugMsg
*
* @author Michael Luebben <nagtrap@nagtrap.org>
*/  
function debug($debugMsg) {
   $fh=fopen(DEBUGFILE,'a');
   fwrite($fh,utf8_encode(microtime_float().' '.$debugMsg."\n"));
   fclose($fh);
}

/**
* Finalize debugging
*
* @author Michael Luebben <nagtrap@nagtrap.org>
*/ 
function debugFinalize() {
   debug('###########################################################');
   debug('Render Time: '.(microtime_float()-DEBUGSTART));
   debug('###########################################################');
}

/**
* Set micro time
*
* @author Michael Luebben <nagtrap@nagtrap.org>
*/ 
function microtime_float() {
   list($usec, $sec) = explode(' ', microtime());
   return ((float)$usec + (float)$sec);
}
?>
