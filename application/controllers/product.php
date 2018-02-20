<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends CI_Controller {
	function __construct(){
		parent::__construct();
		//$this->utilities->validateSession();
		$this->load->library('Layouts');
		$this->load->model('auth/auths');
	}
	
	
	public function index() {
		$extraHead = "activateHeadMeanu('product');getDefaultProductData();";
		$this->layouts->set_extra_head($extraHead);
		$this->layouts->set_title('Product');
		
		$data['getServiceData'] = $this->commonModel->getservice(Date("Y-m-d"),"","","");
		//$this->layouts->add_include('assets/js/main.js')->add_include('assets/css/coustom.css')->add_include('https://www.google.com/recaptcha/api.js',false);
		$this->layouts->dbview('product/product',$data);
		
	}
	
	function getDefaultProductData(){
		$data['getProductData'] = $this->commonModel->getproduct(Date("Y-m-d"));
		if(!$this->utilities->isMobile()){
			echo $this->load->view('product/product_data',$data,true);
		}else{
			echo $this->load->view('product/product_mobile_data',$data,true);
		}
	}
	
	
	
}
