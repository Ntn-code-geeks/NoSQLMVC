<?php

require_once 'logic_global_class.php';
require_once 'jsonCreation.php';
require_once '../query/query_user.php';

class logic_user extends logicGlobal{
	private $query_object;
    public function __construct(){
		$this->query_object = new query_user;
	}

    public function userLogged($data){
        $msg = $this->query_object->userLogged($data);
        if($msg=='2'){
            $this->msg = "Login Successful";
            $this->result = '21';
        }else{
            $this->msg = "Invalid Login Details";
            $this->result = '22';
        }
        $this->getOutput();
    }


    public function addNewUser($data){
	$msg = $this->query_object->addUserdata($data);
	if($msg == '2'){
		$json_object = new jsonCreation();
        $json_object-> mainJson('user');
        $this->msg = 'Register successfully';
        $this->result = '500';
	} else if($msg == '3'){
		$this->msg = 'Something Wrong.';
        $this->result = '600';
	}
	$this->getOutput();

}



}	

	 