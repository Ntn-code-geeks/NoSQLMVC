<?php
error_reporting(0);
$config = dirname(__FILE__).'/json_path/script_path.json';
$GLOBALS['script_path'] = json_decode(file_get_contents($config), true);
//print_r($GLOBALS['script_path']);

?>
