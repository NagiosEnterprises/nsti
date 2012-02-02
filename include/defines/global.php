<?php
/**
 * Here are defined some constants for use in NagTrap
 *
 * @author Michael Luebben <nagtrap@nagtrap.org>
 * @author Lars Michelsen <lars@vertical-visions.de>
 */

define('DEBUG',TRUE);
/**
 * For wanted debug output summarize these possible options:
 * 1: function beginning and ending
 * 2: progres informations in the functions
 * 4: render time
 */
define('DEBUGLEVEL', 1);
define('DEBUGFILE', '/tmp/nsti-debug.log');

define('CONST_VERSION', 'RC1.3.2');
define('CONST_MAINCFG', './etc/config.ini');
define('RED', '#FF795F');
define('YELLOW','#FEFF5F');
define('GREEN','#B2FF5F');
define('ORANGE','#FFC45F');
?>
