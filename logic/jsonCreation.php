<?php

require_once '../class/db_connection.php';

class jsonCreation{
	private $db;
//	private $Idb;
//	private $Mdb;
	private $script_path;
	public function __construct(){
		$this->db = $GLOBALS['db'];
//		$this->Idb = $GLOBALS['Idb'];
//		$this->Mdb = $GLOBALS['Mdb'];
		$this->script_path = $GLOBALS['script_path'];
	}
	public function allJson(){
		$reponse = '';
		$reponse .=$this->mainJson('all');
		$reponse .= ' | ';
		$reponse .= $this->internalJson('all');
		$reponse .= ' | ';
		$reponse .= $this->masterJson('all');
		return trim($reponse, ' | ');
	}
	public function mainJson($table_name){
		$table_array = array('user');
		if($table_name == 'all'){
			$reponse = '';
			foreach($table_array as $val){
				$fn ='main_' . $val;
				$reponse .= $this->$fn();
				$reponse .= ' | ';
			}
			return trim($reponse, ' | ' );
		}else if(in_array($table_name, $table_array)){
			$fn = 'main_'.$table_name;
			return $this->$fn();
		}else
			return 'error: not configure';
	}

	//////////////////////////////////Table Json Creation//////////////////////////////////
	private function main_user(){
	$this->db->where('is_blocked', 0);
		$data = $this->db->get('user', null, array('username', 'password', 'is_blocked', 'email', 'mobile_no', 'created_on', 'ip'));
		if(!empty($data) && is_array($data)){
			$json_data = $this->convertIntoJson($data);
			$file = $this->script_path['MCVB'] . 'user_data.json';
			return $final = $this->gernateJsonFile($file, $json_data, 'user');
		} else
			return "Error: No data is found for table. <br>";
			
	}


	private function gernateJsonFile($file, $json_data, $table_name = ''){
		$fh = fopen($file, 'w');
		if($fh !=='false'){
			$fw = fwrite($fh, $json_data);
			if($fw !== 'false')
				return 'File' . $table_name . '(' . $file . ') has been Created Sucessfully. <br>';
			else
				return 'Error: Unable to open or create the file' .$table_name . ' .<br>';
		}
	}
	private function convertIntoJson($data, $value = '', $separator = ''){
		$temp_data = '';
		if($value == ''){
			$temp_data = $data;
		} else {
			foreach($data as $val){
				if($val == '')
					$temp_data[$val[$value]] = $val;
				else
					$temp_data[$val[$value] . $separator . $val[$value]] = $val;
			}
		}
		return json_encode($temp_data);
	}
}
?>

 
  
