<?php

/**
 * header.php -  Nagios SNMP Trap Interface
 * 
 * PHP Version 5.2
 * 
 * @category SNMP_Management
 * @package  Nagios_SNMP_Trap_Interface
 * @author   Nicholas Scott <nscott@nagios.com>
 * @license  GNU - http://www.gnu.org/licenses/gpl-2.0.html
 * @version  SVN: RC1.3.2
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

require "./include/defines/global.php";

require "./include/functions/functions.debug.php";
require "/usr/local/nagiosxi/html/includes/common.inc.php";
require "./include/functions/redbean/rb.php";

require "./include/classes/class.frontend.php";
require "./include/classes/class.main.php";
require "./include/classes/class.common.php";
require "./include/classes/class.mysqlDB.php";


// initialization stuff
pre_init();

// start session
init_session();

// grab GET or POST variables
grab_request_vars();

// check prereqs
check_prereqs();

// check authentication -- this is done in individual pages
check_authentication();


?>
