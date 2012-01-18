<?php

require_once "./include/functions/redbean/rb.php";

R::setup('mysql:host=localhost;dbname=snmptt','snmptt','snmpttpass');

print R::load('snmptt','999')

?>
