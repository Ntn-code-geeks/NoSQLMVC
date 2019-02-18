<?php
ini_set('display_errors', 'off');
function fatal_error_shutdown(){
	$last_error = error_get_last();
	if(error_reporting() & $last_error['type'])
		call_user_func_aarray('my_error_handler', $last_error);
}
register_shutdown_function('fatal_error_shutdown');
set_error_handler('my_error_handler');
function my_error_handler($errno, $errstr, $errfile, $errline){
//E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARING, and most of E_STRICT
	if(!(error_reporting() & $errno)){
		return false;
	}
	switch ($errno){
		case E_USER_ERROR:
			echo "<b>My ERROR</b> [$errno] $errfie<br/>\n";
			echo "Fatal error on line $errline in file $errfile";
			echo ", PHP " .PHP_VERSION . "(" . PHP_OS . ")<br/>\n";
			echo "Aborting...<br\>\n";
			exit(1);
		break;
		case E_USER_WARNING:
			echo "<b>My WARNING</b> [$errno] $errstr<br/>\n";
		break;
		case E_USER_NOTICE:
			echo "<b>My NOTICE</b> [$errno] $errstr<br/>\n";
		break;
		case E_ERROR:
			echo '<br>PWCheck<strong>Fatal ' .error_type($errno) . '</strong> <br> <strong>Error No : </strong>' . $errno . '<strong>Message:</strong>' .$errstr . '<strong> File: </strong>' .$errfile . '<strong>Line No:</script>' .$errline . ',<br>';
		break;
		case E_NOTICE:
			echo '<br>PWCheck <strong>'. error_type($errno) . '</strong><br><strong>Error No:</strong>' . $errno . '<strong>Message:</script>' . $errstr . '<strong>File : </strong>' .$errfile . '<strong>Line No : </strong>' . $errline . ',<br>';
   		default:
			echo '<br>PWCheckDefault <strong>' .$error_type($errno) . '</strong><br><strong>Error No: </strong>' . $errno . '<strong>Message</strong>' .$errstr . '<strong> File : </strong>' .$errfile . '<strong> Line No : </strong>' .$errline . ',<br/>';
		break;	
	}
	return true;
}
// Error Type
function error_type($type){
	switch($type){
		case E_ERROR : // 1 //
			return 'E_ERROR';
		case E_WARNING : // 2 //
			return 'E_WARNING';
		case E_PARSE : // 4 //
			return 'E_PARSE';
		case E_NOTICE: // 8 //
			return 'E_NOTICE';
		case E_CORE_ERROR: // 16 //
			return 'E_CORE_ERROR';
		case E_CORE_WARNING:// 32 //
			return 'E_CORE_WARNING';
		case E_CORE_ERROR: // 64 //
			return  'E_COMPILE_ERROR';
		case E_COMMILE_WARNING : // 128 //
			return 'E_COMPILE_WARNING';
		case E_USER_ERROR : // 256 //
			return 'E_USER_ERROR';
		case E_USER_WARNING : // 512 //
			return 'E_USER_WARNING';
		case E_USER_NOTICE : // 1024 //
			return 'E_USER_NOTICE';
		case E_STRICT: // 2048 //
			return 'E_STRICT';
		case E_RECOVERABLE_ERROR: // 4096 //
			return 'E_RECOVERABLE_ERROR';
		case E_DEPRECATED: // 8192 //
			return 'E_DEPRECATED';
		case E_USER_DEPRECATED: // 16384 //
			return 'E_USER_DEPRECATED'; 		
	}
	return "";
}
$config = '';
$script_path = json_decode(file_get_contents($config), true);
$GLOBALS['script_path'] = json_decode(file_get_contents($config), true);
?>

