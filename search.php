<?php
require("./include/defines/global.php");
require("./include/functions/functions.debug.php");
require("./include/classes/class.main.php");
require("./include/classes/class.mysqlDB.php");

$searchType = $_REQUEST['searchType'];

$MAIN = new main();

// Read config.ini.php
$configINI = $MAIN->readConfig(CONST_MAINCFG);

// Set table
$table = $MAIN->setTable($tableName,$_REQUEST['severity'],$_REQUEST['trapSelect']);

if (DEBUG&&DEBUGLEVEL&1) debug('Start search: '.$searchType);
$DATABASE = new database($configINI);
$DATABASE->connect();

if ($searchType == 'trapoid') {
   $searchTrapoid = $_POST['searchTrapoid'];
   $result = $DATABASE->search($searchType,$searchTrapoid);
} elseif ($searchType == 'hostname') {
   $searchHostname = $_POST['searchHostname'];
   $result = $DATABASE->search($searchType,$searchHostname);
} elseif ($searchType == 'category') {
   $searchCategory = $_POST['searchCategory'];
   $result = $DATABASE->search($searchType,$searchCategory);
} elseif ($searchType == 'severity') {
   $searchSeverity = $_POST['searchSeverity'];
   $result = $DATABASE->search($searchType,$searchSeverity);
} else {
   if (DEBUG&&DEBUGLEVEL&1) debug('Start search: FALSE - Search type '.$searchType.' not defined!');
}

//print list
if($result) {
   echo "<ul>\n";
   foreach($result as $key => $possible_host) {
      echo "<li>".$possible_host."</li>\n";
   }
   echo "</ul>\n";
} else {
   echo "<ul>\n";
   echo "<li></li>\n";
   echo "</ul>\n";
} 

if (DEBUG&&DEBUGLEVEL&1) debug('END search');
?>

