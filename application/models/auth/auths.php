<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auths extends CI_Model {
	
	public function __construct() {
        parent::__construct();
		$this->load->model('common/commonModel');
	}
	
	function login($userId,$paswd){
		$where = "(username = '".$userId."' or  email='".$userId."') and passwd='".md5($paswd)."'";
		$res  = $this->commonModel->getRecord('users','*',$where,'','','','array',0);
		if($res){
			return $res;
		}else{
			return false;
		}
	}
	
	function insert_user($data){
		$data['date_added'] = date('Y-m-d H:i:s');
		$res  = $this->commonModel->insertRecord('users',$data);
		if($res){
			$profile_data = array(
				'users_id' => $res,
				'first_name' => $data['first_name'],
				'last_name' => $data['last_name'],
				'email_univ' => $data['email'],
				'added_by' => $res,
				'date_added' => date('Y-m-d H:i:s'),
			);
			$up_res = $this->commonModel->insertRecord('user_profile',$profile_data);
		}
		
		if($res && $up_res){
			return $res;
		}else{
			return false;
		}
	}
	
		
}
?>
