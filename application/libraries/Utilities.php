<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * booksrus Utilities Class
 *
 * It contain common functions that will be used in the whole product  
 *
 * @package		booksrus
 * @category	Libraries
 * @author		Aijaz Ahmad
 * @CDate		
 */
class Utilities {

    private $CI;
	
	public function __construct() {
        $this->CI = & get_instance();
		$this->CI->load->model('common/commonModel');
		$this->CI->load->helper('cookie');
    }
	
	/**
	 * 		Function 		setSession
	 * 		author			Aijaz Ahmad <aijaz@collegebooksrus.com>
	 * 		Description		This function will use to set session variables.
	 * 		@param			$sess_data
	 * 		@return 		none
	 */
	function setSession($sess_data) {
		$this->CI->session->set_userdata('userdata',$sess_data);
	}

	function setAccessSession($setAceess) {
		$this->CI->session->set_userdata('access',$setAceess);
	}
	
	function getUserTypeAndStatus(){
			if($this->getSessionUserData('user_type')=='1'){
				$return['user_type'] = 'admin';
			}elseif($this->getSessionUserData('user_type')=='0'){
				$return['user_type'] = 'user';
			}
			if($this->getSessionUserData('active_status')=='0'){
				$return['active_status'] = 'active';
			}elseif($this->getSessionUserData('active_status')=='1'){
				$return['active_status'] = 'inactive';
			}elseif($this->getSessionUserData('active_status')=='2'){
				$return['active_status'] = 'delete';
			}
		return $return;
	}
	
	public function validateSession() {
		if ($this->isAuth()) {
			if($_SERVER['REQUEST_URI']=='/index'){
				redirect('/home');
			}else if($_SERVER['REQUEST_URI']=='/home'){
				//redirect('/');
			}/* else if($_SERVER['REQUEST_URI']=='/auth/signupauth'){
				redirect('/');
			}else if($_SERVER['REQUEST_URI']=='/auth/signinauth'){
				redirect('dashboard');
			} */
        } else {
			$this->destroySession();
		}
	}
	
	function isAuth(){
		$sesdata = $this->CI->session->userdata('userdata');
		if (isset($sesdata) && $sesdata['login'] === true) {
			return true;
		}else{
			return false;
		}
	}
	function destroySession() {
        $this->CI->session->sess_destroy();
        redirect('auth/signin');
    }
	
	function getUserDataById($userId='0'){
		if(!empty($userId)){
			return $this->CI->commonModel->getUserDataById($userId);
		}else{
			return false;
		}
	}
	
	function getAllCountry(){
		$result = $this->CI->commonModel->getAllCountry();
		if($result){
			return $result;
		}else{
			return false;
		}
		
	}
	
	function getAllState($countryId='231',$stateId='0'){
		$result = $this->CI->commonModel->getAllState($countryId,$stateId);
		if($result){
			return $result;
		} else {
			return false;
		}
	}
	
	function getAllCity($stateId='3926'){ //colorado
		$result = $this->CI->commonModel->getAllCity($stateId);
		if($result){
			return $result;
		}else{
			return false;
		}
		
	}
	
	function getListUnivesityByStatteId($stateId='3926'){ //colorado
		$result = $this->CI->commonModel->getListUnivesityByStatteId($stateId);
		if($result){
			return $result;
		}else{
			return false;
		}
	}
	
	function getWrongPasswdAtempt(){
		return $this->CI->session->userdata('wrong_pass');
	}
	
	function setWrongPasswdAtempt($reset=false){
		$getCount = $this->CI->session->userdata('wrong_pass');
		$counter = $getCount+1;
		$this->CI->session->set_userdata('wrong_pass',$counter);
		
		if($reset){
			$this->CI->session->set_userdata('wrong_pass',0);
		}
	}
	
	function checkUnivApr(){
		$uid = $this->getSessionUserData('uid');
		$getuserdata = $this->getUserDataById($uid);
		if($getuserdata['uf']){
			return true;
		}else{
			redirect('common/commonctrl/adduseruniv');
		}
	}
	
	function getSessionUserData($key=""){
		$sesdata = $this->CI->session->userdata('userdata');
		if (isset($sesdata) && $sesdata['login'] === true) {
			if($key){
				return $sesdata[$key];
			}else{
				return $sesdata;
			}
		}else{
			return false;
		}
	}
	
	function getSessionData($type="",$key=""){
		$sesdata = $this->CI->session->userdata('userdata');
		if (isset($sesdata) && $sesdata['login'] === true) {
			if($type){
				$specData = $this->CI->session->userdata($type);
				if($key){
					return $specData[$key];
				}else{
					return $specData;
				}
			}else{
				return $this->CI->session->userdata();
			}
		}else{
			return false;
		}
	}
	
	function listuserad($userId=""){
		if($userId){
			$coun = $this->CI->commonModel->gettotaluserad($userId);
			if($coun){
				return $coun;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	function getDiscountPrice($price=0,$dis=0){
		if($price){
			if($dis){
				$price = $price - (($price*$dis)/100);
				return number_format((float)$price, 2, '.', '');
			}else{
				return number_format((float)$price, 2, '.', '');
			}
		}else{
			return  number_format((float)$price, 2, '.', '');
		}
	}
	
	function getFormatedPrice($price=0){
		if($price){
			return number_format((float)$price, 2, '.', '');
		}else{
			return  number_format((float)$price, 2, '.', '');
		}
	}
	
	function convertDateFormatForDbase($suppliedDate, $format = "Y-m-d") {
        /* If we have any general setting in which we have defined the date format
          or in session then we can extract that here and pass the following funtion
        */
		 if((empty($suppliedDate) or $suppliedDate=='0000-00-00' or strlen($suppliedDate)<10)){
			return false; 
		}
		if(DEFAULT_DATE_FORMATE =='d-m-Y'){
			$dmystring = explode("-",$suppliedDate);
			$suppliedDate = $dmystring[1]."-".$dmystring[0]."-".$dmystring[2];
		}
		if($suppliedDate!=''){
			//$suppliedDate = str_replace("-", "/", $suppliedDate);
			if (!isset($suppliedDate) or $suppliedDate == "") {
				$date = date("m-d-Y"); //put current date 
			}
			$dateObj = new DateTime($suppliedDate);
			$dateForDbase = $dateObj->format('Y-m-d');
			return $dateForDbase;
		}else{
			return false;
		}
    }
	
	function showDateForSpecificTimeZone($dateTime, $userDefinedDateFormat = '',$mdyTo=false,$userDefinedTimeZone='') {
		if($dateTime=='0000-00-00' || $dateTime=='0000-00-00 00:00:00' || $dateTime=='' || $dateTime=='0'){
			return "";
		}
		if($mdyTo){
			$dateTime=str_replace('-','/',$dateTime);
		}else{
			$dateTime=str_replace('/','-',$dateTime);
		}
        $this->CI->load->helper('date');
		$dt1 = new DateTime($dateTime);
        $defaultDateFormat = '';
        if (empty($userDefinedDateFormat)) {
            if (!DEFAULT_DATE_FORMATE) {
                $defaultDateFormat = DATE_W3C; //set default 
            } else {
                $defaultDateFormat = DEFAULT_DATE_FORMATE;
            }
        } else {
            $defaultDateFormat = $userDefinedDateFormat;
        }
        if(!empty($userDefinedTimeZone)){
            $timezone=$userDefinedTimeZone;
        }else{
            $timezone = 'UTC';        
        }
		return $dt1->format($defaultDateFormat);
	}
	
	function cleanurl($input){
		if($input){
			return preg_replace('/[ ,!@#$%^&*()+<>?\/\~"\']+/', '-', trim($input));
		}else{
			return false;
		}
	}
	
	function rempveSpecial($input){
		if($input){
			return preg_replace('/[,!@#$%^&*()+<>?\/\~"\']+/', '', trim($input));
		}else{
			return false;
		}
	}
	
	function setserchurl($url,$reset=false){
		if($reset){
			$cookie= array(
				'name'   => 'seturl',
				'value'  => '',
				'expire' => '0'
			);
			delete_cookie($cookie);
		}else{
			$cookie= array(
				'name'   => 'seturl',
				'value'  => $url,
				'expire' => '3600'
			);
			set_cookie($cookie);
		}
	}
	function getserchurl(){
		if(get_cookie('seturl')){
			return get_cookie('seturl');
		}else{
			return false;
		}
	}
	
	function getpagination($url,$total,$limit=20,$numlink=5){
		$config = array();
			$config["base_url"] = $url;
			$config['page_query_string'] = TRUE;
			$config["total_rows"] = $total;
			$config["per_page"] = $limit;
			$config['display_pages'] = True;
			$config['num_links'] = 2;
			//$config['use_page_numbers'] = FALSE;
			$config['full_tag_open'] = '<div class="pull-right"> <ul class="pagination">';
			$config['full_tag_close'] = '</div></ul>';
			$config['num_tag_open'] = '<li>';
			$config['num_tag_close'] = '</li>';
			$config['next_link'] = '→';
			$config['next_tag_open'] = '<li>';
			$config['next_tag_close'] = '</li>';
			$config['prev_link'] = '←';
			$config['cur_tag_open'] = '<li class="active"><a>';
			$config['cur_tag_close'] = '</li></a>';
			$config['prev_tag_open'] = '<li>';
			$config['prev_tag_close'] = '</li>';
			$config['last_link'] = '»';
			$config['last_tag_open'] = '<li>';
			$config['last_tag_close'] = '</li>';
			$config['first_link'] = '«';
			$config['first_tag_open'] = '<li>';
			$config['first_tag_close'] = '</li>';
			$config['first_link'] = false;
			$config['last_link'] = false;
			$this->CI->pagination->initialize($config);
	}
	
	function getProduct($id=""){
		if($id){
			return $this->commonModel->getRecord("product","*",array("id"=>$id));
		}else{
			return $this->commonModel->getRecord("product","*",array(),"","","","array","1");
		}
		
	}
	
	
	
	
	
	
	
	
	
	
}