<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends CI_Controller {

	function __construct(){
		parent::__construct();
		//$this->utilities->validateSession();
		$this->load->library('Layouts');
		$this->load->model('auth/auths');
	}

	public function index() {
		if($this->utilities->isAuth()){
			redirect("/home");
		}else{
			//$extraHead = "activateHeadMeanu('topdashboard,topdashboardhead,intopdashboard')";
			//$this->layouts->set_extra_head($extraHead);
			$this->layouts->set_title('welcome');
			$this->layouts->add_include('assets/js/main.js')->add_include('assets/css/coustom.css');
			$this->layouts->view('index');
		}
	}
	
	public function signin() {
		$user = $this->input->post('user');
		$passwd = $this->input->post('passwd');
		$userData = $this->auths->login($user,$passwd);

		if($userData){
			if( $userData['active_flag']=='0' ) {
				echo json_encode(array("status"=>"error","msg"=>"User not active"));
			} else if( $userData['active_flag']=='2' ) {
				echo json_encode(array("status"=>"error","msg"=>"User not found"));
			} else {
			// set session
				$sess_data = array('login' => TRUE, 'uname' => $userData['first_name'], 'uid' =>$userData['id'], 'active_status' => $userData['active_flag'], 'email' => $userData['email'], 'mobile' => $userData['mobile'], 'user_name' => $userData['username']);
				$this->utilities->setSession($sess_data);
				echo json_encode(array("status"=>"success","msg"=>"Login successful","data"=>$sess_data));
			}
		}else{
			echo json_encode(array("status"=>"error","msg"=>"Please enter correct user and password"));
		}
	}

}