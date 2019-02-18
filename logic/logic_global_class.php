<?php
abstract class logicGlobal{
	protected $e_code = '';
	protected $e_msg = '';
	protected $e_msg1 = '';
	protected $msg = '';
	protected $msg1 = '';
	protected $result = '';
	
	final private function getSuccess(){
		echo json_encode(array('m_code' => '2', 'message' => $this->msg, 'message1'=>$this->msg1, 'result' => $this->result), true);
		//die;	
	}
	final private function getSuccess1($result){
		$this->result = $result;
		$this->getSuccess();
	}
	final protected function getSuccess2($result, $msg){
		$this->result = $result;
		$this->msg = $msg;
		$this->getSuccess();	
	}
	final protected function getSuccess3($result, $msg, $msg1){
		$this->result = $result;
		$this->msg = $msg;
		$this->msg1 = $msg1;
		$this->getSuccess();
	}
	final private function getError(){
		echo json_encode(array('m_code' => '1', 'message' => $this->e_msg, 'message1'=>$this->e_msg1, 'result' =>$this->e_code));
		//die;
	}
	final protected function getError1($e_msg){
		$this->e_msg = $e_msg;
		$this->getError();
	}
	final protected function getError2($e_msg, $e_code){
		$this->e_msg = $e_msg;
		$this->e_code = $e_code;
		$this->getError();
	}
	final protected function getError3($e_msg, $e_msg1, $result){
		$this->e_msg = $e_msg;
		$this->e_msg1 = $e_msg1;
		$this->result = $result;
		$this->getError();
	}
	final protected function getOutput(){
		if($this->e_msg !='' || $this->e_code !='')
			$this->getError();
		else
			$this->getSuccess();
	}
}

?>

