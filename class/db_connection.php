<?php

include '../class/db_class.php';

$GLOBALS['db']=  $db = new db_class();
//hostname, dbusername, dbpassword, dbname
if($db->connect('localhost', 'root', 'admin123', 'nosql'))
{
	
}
else
    die('error is: '. $db->getError());
//require_once('db_class.php');
///$GLOBALS['db'] = $db = new db_class('connectSaveMulti');
//$GLOBALS['dbR'] = $dbR = new MysqliDb ('localhost', 'evds_read', '7w8xxDARLytN3w5B', 'evds');
?>

