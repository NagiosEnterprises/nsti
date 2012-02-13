<?php
/**
 * Here are defined some constants for use in NSTI
 *
 * @author Michael Luebben <nagtrap@nagtrap.org>
 * @author Lars Michelsen <lars@vertical-visions.de>
 * @author Nicholas Scott <nscott@nagios.com>
 */

define('DEBUG',TRUE);
define('FROZEN',false);
/**
 * For wanted debug output summarize these possible options:
 * 1: function beginning and ending
 * 2: progres informations in the functions
 * 4: render time
 */
define('DEBUGLEVEL', 1);
define('DEBUGFILE', '/tmp/nsti-debug.log');

define('CONST_VERSION', 'RC2.0');
define('CONST_MAINCFG', './etc/config.ini.php');
define('RED', '#FF795F');
define('YELLOW','#FEFF5F');
define('GREEN','#B2FF5F');
define('ORANGE','#FFC45F');

?>
