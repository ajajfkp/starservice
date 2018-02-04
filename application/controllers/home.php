<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
	function __construct(){
		parent::__construct();
		//$this->utilities->validateSession();
		$this->load->library('Layouts');
		$this->load->model('auth/auths');
	}
	
	
	public function index() {
		//$extraHead = "activateHeadMeanu('topsignin')";
		//$this->layouts->set_extra_head($extraHead);
		$this->layouts->set_title('Home');
		
		$data['getServiceData'] = $this->commonModel->getservice(Date("2018-02-04"));
		/* echo "<pre>";
		print_r($data['getServiceData']);die; */
		//$this->layouts->add_include('assets/js/main.js')->add_include('assets/css/coustom.css')->add_include('https://www.google.com/recaptcha/api.js',false);
		$this->layouts->dbview('home/main_page',$data);
		
	}
	
	public function openaddservice(){
		$data['getServiceData'] = "";
		$data['productList'] = $this->utilities->getProduct();
		echo $this->load->view('home/addpopup',$data,true);
	}
	
	public function editservice(){
		$id=$this->input->post('id');
		$data['getServiceDataArr'] = $this->auths->getServiceSetails($id);
		/* echo "<pre>";
		print_r($data['getServiceDataArr']);die;
		echo "</pre>"; */
		//$data['getServiceData'] =  $this->commonModel->getRecord('services','*',array('id'=>$id),array(),"","","array","0");
		echo $this->load->view('home/updatepopup',$data,true);
	}
	
	public function viewDetail(){
		$data['Kashif']="My Name";
		/*$id=$this->input->post('id');
		$data['getServiceDataArr'] = $this->auths->getServiceSetails($id);
		 echo "<pre>";
		print_r($data['getServiceDataArr']);die;
		echo "</pre>"; */
		//$data['getServiceData'] =  $this->commonModel->getRecord('services','*',array('id'=>$id),array(),"","","array","0");
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
			"date_added"=>date("Y-m-d H:i:s"),
			"added_by"=>$this->utilities->getSessionUserData('uid')
		);
		
		$custId = $this->commonModel->insertRecord('customer',$custArr);
		
		if(1==1){
			$serDataArr = Array();
			$serDataArr['customer_id'] = "";//$custId;
			$serDataArr['product_id'] = $this->input->post('product');
			$serDataArr['brand_id'] = $this->input->post('brand');
			$serDataArr['modelnumber'] = $this->input->post('modelNum');
			$serDataArr['warranty'] = $this->input->post('warranty');
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
	
	public function isMobile() {
		return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
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
}
