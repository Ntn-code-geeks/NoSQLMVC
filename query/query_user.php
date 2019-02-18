<?php
require_once '../class/db_connection.php';

class query_user {
	private $db;
	public function __construct(){
		$this->db = $GLOBALS['db'];
	}


	public function addUserdata($data){
        $f_Name = isset($data['Username']) ? $data['Username'] : null;
        $userEmail = isset($data['email']) ? $data['email'] : null;
        $userMobile = isset($data['Mobile']) ? $data['Mobile'] : null;
        $userPassword = isset($data['Paswword']) ? $data['Paswword'] : null;
        $date = date('Y-m-d H:i:s');
        $ip =   getenv('HTTP_CLIENT_IP')?:
				getenv('HTTP_X_FORWARDED_FOR')?:
				getenv('HTTP_X_FORWARDED')?:
				getenv('HTTP_FORWARDED_FOR')?:
				getenv('HTTP_FORWARDED')?:
				getenv('REMOTE_ADDR');
        $dataUser =array(
            'username' => $f_Name,
            'email'     => $userEmail,
            'mobile_no'  => $userMobile,
            'password'   => md5($userPassword),
            'created_on' => $date,
            'ip'   => $ip,
			'is_blocked' => 0

        );
        $msg= $this->db->insert('user', $dataUser);
        //	echo $this->db->getLastQuery();
        if ($msg == '1') {
            return 2;
        } else {
            return 3;
        }

	}

	public function userLogged($data){
//		print_r($data);
//      $userData= json_decode(file_get_contents($GLOBALS['script_path']['MCVB'] . 'user_data.json'), true);
//		print_r($userData);
        $uName = isset($data['Username']) ? $data['Username'] : null;
        $uPwd  = isset($data['Paswword']) ? $data['Paswword'] : null;
        $pwdU= md5($uPwd);

		$this->db->Where('email', $uName);
		$this->db->Where('password', $pwdU);
		$msg = $this->db->getOne('user');
        $count=$this->db->getcount();
		//echo $this->db->getLastQuery();

		if ($count == '1') {
            $_SESSION['email']=$msg[4]['email'];
            $_SESSION['Username']=$msg[1]['username'];
            return 2;
        } else {
            return 3;
        }



	}



}

?> 

		