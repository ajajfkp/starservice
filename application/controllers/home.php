<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->utilities->validateSession();
		$this->load->library('Layouts');
		$this->load->model('auth/auths');
	}
	
	
	public function index() {
		$extraHead = "activateHeadMeanu('service');getDefaultData();";
		$this->layouts->set_extra_head($extraHead);
		$this->layouts->set_title('Home');
		$data['data'] = "";	
		/* echo "<pre>";
		print_r($data['getServiceData']);die; */
		//$this->layouts->add_include('assets/js/main.js')->add_include('assets/css/coustom.css')->add_include('https://www.google.com/recaptcha/api.js',false);
		if(!$this->utilities->isMobile()){
			$this->layouts->dbview('home/main_page',$data);
		}else{
			$this->layouts->dbview('home/main_page_mobile',$data);
		}
	}
	
	function getDefaultData(){
		$data['getServiceData'] = $this->commonModel->getservice(Date("Y-m-d"));
		if(!$this->utilities->isMobile()){
			echo $this->load->view('home/main_page_data',$data,true);
		}else{
			echo $this->load->view('home/main_page_mobile_data',$data,true);
		}
	}
	
	function getServiceList(){
		$date=$this->input->post('type');
		$dateArr = $this->getDateBySerch($date);
		$data['getServiceData'] = $this->commonModel->getservice($dateArr['fromDate'],$dateArr['toDate']);
		if(!$this->utilities->isMobile()){
			echo $this->load->view('home/main_page_data',$data,true);
		}else{
			echo $this->load->view('home/main_page_mobile_data',$data,true);
		}
	}
	
	function getServiceListByInput(){
		$inputval=$this->input->post('inputval');
		$searchby=$this->input->post('searchby');
		$data['getServiceData'] = $this->commonModel->getservicebyinput($inputval,$searchby);
		if(!$this->utilities->isMobile()){
			echo $this->load->view('home/main_page_data',$data,true);
		}else{
			echo $this->load->view('home/main_page_mobile_data',$data,true);
		}
	}
	
	public function uploadUserImg(){
		//print_r($_FILES['cstmrImg']);
		$config['upload_path']          = './uploads/userimg/';
		$config['allowed_types']        = 'jpeg|JPEG|jpg|JPG|png|PNG';
		$config['max_size']             = '*';
		$config['max_width']            = '*';
		$config['max_height']           = '*';
		$config['encrypt_name'] 		= TRUE;
		$this->load->library('upload', $config);
		if ( ! $this->upload->do_upload('cstmrImg')) {
			$error = array('error' => $this->upload->display_errors());
			echo json_encode($error);
		} else {
			$data = $this->upload->data();
			if($data){
				echo json_encode($data);
			}else{
				echo json_encode( array('error' =>'File not upload'));
			}
        }
	}
	
	public function openaddservice(){
		$data['getServiceData'] = "";
		$data['productList'] = $this->utilities->getProduct();
		echo $this->load->view('home/addpopup',$data,true);
	}
	
	public function editservice(){
		$id=$this->input->post('id');
		//$data['getServiceDataArr'] = $this->auths->getServiceSetails($id);
		/* echo "<pre>";
		print_r($data['getServiceDataArr']);die;
		echo "</pre>"; */
		//$data['getServiceData'] =  $this->commonModel->getRecord('services','*',array('id'=>$id),array(),"","","array","0");
		echo $this->load->view('home/updatepopup',$data,true);
	}
	
	public function viewDetail(){
		$serId=$this->input->post('serId');
		$serDetId=$this->input->post('serDetId');
		$data['getServiceData'] =  $this->commonModel->getServiceDataById($serId,$serDetId);
		$data['getAllSerDetArr'] =  $this->commonModel->getRecord('service_details','*',array('service_id'=>$serId),array(),"","","array","1");
		echo $this->load->view('home/viewpopup',$data,true);
	}
	
	public function updateEntry(){
		$data['Kashif']="My Name";
		/*$id=$this->input->post('id');
		$data['getServiceDataArr'] = $this->auths->getServiceSetails($id);
		 echo "<pre>";
		print_r($data['getServiceDataArr']);die;
		echo "</pre>"; */
		//$data['getServiceData'] =  $this->commonModel->getRecord('services','*',array('id'=>$id),array(),"","","array","0");
		echo $this->load->view('home/updateService',$data,true);
	}
	
	public function service() {
		
		$custArr = Array(
			"name"=>$this->input->post('name'),
			"mobile"=>$this->input->post('mobile'),
			"address"=>$this->input->post('addr'),
			"user_image"=>$this->input->post('userImg'),
			"date_added"=>date("Y-m-d H:i:s"),
			"added_by"=>$this->utilities->getSessionUserData('uid')
		);
		
		$custId = $this->commonModel->insertRecord('customer',$custArr);
		
		if($custId){
			$serDataArr = Array();
			$serDataArr['customer_id'] = $custId;
			$serDataArr['product_id'] = $this->input->post('product');
			$serDataArr['brand_id'] = $this->input->post('brand');
			$serDataArr['modelnumber'] = $this->input->post('modelNum');
			$serDataArr['warranty'] = $this->input->post('warranty');
			$serDataArr['guaranty'] = $this->input->post('guaranty');
			$serDataArr['sold_date'] = $this->utilities->convertDateFormatForDbase($this->input->post('dateSold'));
			$serDataArr['num_of_services'] = $this->input->post('services');
			$serDataArr['duration'] = $this->input->post('duration');
			$serDataArr['warranty_exp'] = $this->calcWarExp($serDataArr['sold_date'],$serDataArr['warranty']);
			$serDataArr['notes'] = $this->input->post('note');
			$serDataArr['referral'] = $this->input->post('referral');
			$serDataArr['referral_other'] = $this->input->post('referralotr');
			
			$serId = $this->commonModel->insertRecord('services',$serDataArr);
			
			if($serId){
				$calcSerDateArr = $this->calcSerDate($serDataArr['sold_date'],$serDataArr['num_of_services'],$serDataArr['duration']);
				
				if($calcSerDateArr){
					foreach($calcSerDateArr as $serArr){
						$this->commonModel->insertRecord('service_details',array("service_id"=>$serId,"service_date"=>$serArr,"added_by"=>$this->utilities->getSessionUserData('uid'),"date_added"=>date("Y-m-d H:i:s")));
					}
				}else{
						$this->commonModel->insertRecord('service_details',array("service_id"=>$serId,"service_date"=>$serId,"done_status"=>"1","service_completed_by"=>$this->utilities->getSessionUserData('uid'),"added_by"=>$this->utilities->getSessionUserData('uid'),"date_added"=>date("Y-m-d H:i:s")));
				}
			}
		}
		echo json_encode(array("status"=>"success","msg"=>"Service recorded successfully."));
	}
	
	public function updateservice() {
		$postArr = Array();
		$postArr['name'] = $this->input->post('name');
		$postArr['contact'] = $this->input->post('contact');
		$postArr['service_date'] = $this->utilities->convertDateFormatForDbase($this->input->post('sdate'));
		$postArr['address'] = $this->input->post('address');
		$postArr['updated_by'] = $this->utilities->getSessionUserData('uid');
		$postArr['date_modified'] = date("Y-m-d H:i:s");
		
		$up_res = $this->commonModel->updateRecord('services',$postArr,array("id"=>$this->input->post('id')));
		if($up_res){
			echo json_encode(array("status"=>"success","msg"=>"Service record update successfully.","data"=>$up_res));
		}else{
			echo json_encode(array("status"=>"error","msg"=>"Service not updated.","data"=>""));
		}
	}
	
	public function deleteservic(){
		$dl_res = $this->commonModel->deleteRecord('services',array("id"=>$this->input->post('id')));
		if($dl_res){
			echo json_encode(array("status"=>"success","msg"=>"Service record delete successfully.","data"=>$dl_res));
		}else{
			echo json_encode(array("status"=>"success","msg"=>"Delete failed.","data"=>$dl_res));
		}
	}
	
	public function getBrandList() {
		$prodId = $this->input->post('prodId');
		$str ="<option value=''></option>";
		if($prodId){
			$brandList = $this->utilities->getBrand($prodId);
			if($brandList){
				foreach($brandList as $brand){
					$str .="<option value='".$brand['id']."'>".$brand['name']."</option>";
				}
			}
		}
		echo $str;
	}
	
	function calcWarExp($sellDate="",$warranty=""){
		if($warranty=="0" || $warranty==""){
			return $sellDate;
		}else{
			switch ($warranty){
				case "3" :
				$warrantyExpDate = date("Y-m-d",strtotime("$sellDate +3 month"));
				break;
				case "6" :
				$warrantyExpDate = date("Y-m-d",strtotime("$sellDate +6 month"));
				break;
				case "12" :
				$warrantyExpDate = date("Y-m-d",strtotime("$sellDate +12 month"));
				break;
			}
			return $warrantyExpDate;
		}
	}
	
	function calcSerDate($sellDate="",$numSer="",$interval=""){
		$retArr=Array();
		if($numSer){
			for($i=1;$i<=$numSer;$i++){
				switch ($interval){
					case "1" :
					$newSerDate = date( "Y-m-d", strtotime( "$sellDate +1 month" ));
					break;
					case "3" :
					$newSerDate = date("Y-m-d",strtotime("$sellDate +3 month"));
					break;
					case "6" :
					$newSerDate = date("Y-m-d",strtotime("$sellDate +6 month"));
					break;
					case "12" :
					$newSerDate = date("Y-m-d",strtotime("$sellDate +12 month"));
					break;
				}
				$retArr[$i] = $newSerDate;
				$sellDate = $newSerDate;
			}
			return $retArr;
		}else{
			return false;
		}
	}
	
	function getDateBySerch($type="TD"){
		$retArr = array();
		switch($type){
			case "TD" :
				$retArr['fromDate'] = Date('Y-m-d');
				$retArr['toDate'] ="";
			break;
			case "TR" :
				$retArr['fromDate'] = date( "Y-m-d", strtotime("tomorrow"));
				$retArr['toDate'] ="";
			break;
			case "TW" :
				$retArr['fromDate'] = date("Y-m-d",strtotime('monday this week'));
				$retArr['toDate'] = date("Y-m-d",strtotime("sunday this week"));
			break;
			case "TM" :
				$retArr['fromDate'] = date('Y-m-01');
				$retArr['toDate'] = date('Y-m-t');
			break;
			case "LM" :
				$retArr['fromDate'] = date('Y-m-d', strtotime('first day of last month'));
				$retArr['toDate'] = date('Y-m-d', strtotime('last day of last month'));
			break;
			case "TY" :
				$retArr['fromDate'] = date('Y-m-d', strtotime('first day of january this year'));
				$retArr['toDate'] = date('Y-m-d', strtotime('last day of december this year'));
			break;
			default:
				$retArr['fromDate'] = Date('Y-m-d');
				$retArr['toDate'] ="";
			break;
		}
		return $retArr;
	}
	
	function signOut(){
		$this->utilities->destroySession();
	}
	
	function getCstmerList(){
		$inputval = $this->input->post('inputval');
		if($inputval){
			$searchby = $this->input->post('searchby');
			if($searchby=="1"){
				$whrCon = " mobile LIKE '%".$inputval."%'";
			}else if($searchby=="2"){
				$whrCon = " name LIKE '%".$inputval."%'";
			}else if($searchby=="3"){
				$whrCon = " address LIKE '%".$inputval."%'";
			}else {
				$whrCon = "";
			}
			$cstmrDataArr = $this->commonModel->getRecord("customer","*",$whrCon,"","","","array","1");
			if($cstmrDataArr){
				echo json_encode($cstmrDataArr);
			}else{
				echo json_encode(array());
			}
		}else{
			echo json_encode(array());
		}
	}
}
