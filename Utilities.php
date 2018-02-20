<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Connect_health Utilities Class
 *
 * It contain common functions that will be used in the whole product  
 *
 * @package		Connect_Health
 * @category	Libraries
 * @author		Dev Team
 * @CDate		
 * @LUDate		21/10/2012	(Last Updated Date)	Abhishek Singh
 * @link		http://connect_health/application/libraries/utilities.php
 */
class Utilities {

    private $CI;
	//
	
	public function __construct() {
        $this->CI = & get_instance();
		$this->CI->load->model('common/manageCommonTaskModel');
    }
	
    /**
     * 		Function 		setSession
     * 		Author			Abhishek Singh<abhishek.singh@greenapplestech.com>
     * 		Description		This function will use to set session variables.
     * 		@param			$sess_data
     * 		@return 		none
     */
    function setSession($sess_data) {
		$this->CI->session->set_userdata($sess_data);
    }
	
	function setAccessSession($setAceess) {
        $this->CI->session->set_userdata('access',$setAceess);
    }
	
	
	function setClinicalAccessSession($setCliAceess) {
        $this->CI->session->set_userdata('clinicalAccess',$setCliAceess);
    }

    /**
     * 		Function 		validateSession
     * 		Author			Abhishek Singh<abhishek.singh@greenapplestech.com>
     * 		Description		This function will use to validating session.
     * 		@param			none
     * 		@return 		none
     */
    public function validateSession($sessType='') {
		$this->CI =& get_instance();

		

		if (!$this->CI->session->userdata('userName')) {
			
			if($sessType=='1')
				$this->destroySessionMobile();
			else if($sessType=='2')
				$this->destroySessionProcess();
			else
				$this->destroySession($this->CI);
        }else{
			$getSessionId=$this->CI->manageCommonTaskModel->getSessionId($this->CI->session->userdata('id'));
			if($getSessionId != $this->CI->session->userdata('session_id')){
				if($sessType=='1')
					$this->destroySessionMobile();
				else if($sessType=='2')
					$this->destroySessionProcess();
				else
				{
					$multiUserLoginConfig=$this->CI->config->item('multiUserLogin');
					if($multiUserLoginConfig =='0'){
						$this->destroySession($this->CI);
					}
				}
			}
		}
	}
	function checkPermission()
	{

		$CI =& get_instance();
		if($CI->session->userdata('userType')==7) return true;
		//First of all we will have to check that user has enabled this feture or not
		$chkValidation=$CI->config->item('enable_ValidationOnController');
		if(!$chkValidation)return true;
		$buffer = $CI->output->get_output();
		$chkPermission=$this->checkModuleAccessByUser('',array('dashboard','login'));
			if(!$chkPermission){
				//hmm means bad guy just kick the user by our redirect function..or show the message if we are on any widget..
				if($CI->input->is_ajax_request()){
					//Just check that this request from ajax if from ajax then show the message rather than redirect this...
					$CI->output->set_output('<span class="accessDenied">You are not Authorised to view this section</span>');
					$CI->output->_display();
					die;
				}else{//redirect user
					redirect('/doctor/dashboard/');
				}
				
				
			}
	}
	/**
     * 		Function 		checkModuleAccessByUser
     * 		Author			Rahul Anand<rahul.anand@greenapplestech.com>
     * 		Description		This function will use to check the access persmission on module..
     * 		@param			none
     * 		@return 		none
    */
	 function checkModuleAccessByUser($controllerName='',$exludeControllerName=array())
	 {
		$this->CI->load->model('common/manageCommonTaskModel');
		//Let's check that user is responsible or not..
		if(empty($controllerName)){
			//hmm user is not responsible very much ok we will have to do his work itself
			//first of all we will have extract the controller name from url..
			$controllerFolderName=$this->CI->router->directory;
			$controllerName=$this->CI->router->fetch_class();
			if(in_array($controllerName,$exludeControllerName)){
				//hmm means user wants to exclude some controller...
				return true;
			}
			//get action name
			$controllerActionName=$this->CI->router->fetch_method();
			
		}else{
			//Ok user has passed the controller name now no need to do our work..
			$controllerName=$controllerName;
		}
			//Now call our dbase functio that will check that permissiom on this folder...
			//Ok First of all we have to check that user has emergency persmissoin or not..if that has emergency permission then we will have to check in emergencey table...
			if($this->CI->session->userdata('emergency')=='1'){
				//Hmm he has login on emergency permission,For current controller we will have to check in emergency permission also...
				//First of all we will have to check on user level..
				$checkRow=$this->CI->manageCommonTaskModel->CheckAccessPermissionOnProcess($controllerName,true,$this->CI->session->userdata('id'));
				
				/* Check in Emergency */
				if($checkRow){
					//hmm entry found ok now check disable flag for this
					if($checkRow->read_write_flag==0){
						//means we have disabled for this user...
						return false;//hmm dirty guy want to exploit my system ..ok just kick him 
					}else if($checkRow->read_write_flag==1 || $checkRow->read_write_flag==2){
						//valid user found here so allow him to show selected screen
						return true;
					}
				}else{
					//hmm we haven't found any entry for this user then we are to check in to user type 
						$checkRow=$this->CI->manageCommonTaskModel->CheckAccessPermissionOnProcess($controllerName,true,0,$this->CI->session->userdata("userType"),true);
						if($checkRow){
							//hmm entry found ok now check disable flag for this
							if($checkRow->read_write_flag==0){
								//means we have disabled for this user...
								return false;//hmm dirty guy want to exploit my system ..ok just kick him 
							}else if($checkRow->read_write_flag==1 || $checkRow->read_write_flag==2){
								//valid user found here so allow him to show selected screen
								return true;
							}
						}
				}
				
				
				
				//now check for normal emergency aalso... on user level and usertypelevel
				/* Check in Normal */
				$checkRow=$this->CI->manageCommonTaskModel->CheckAccessPermissionOnProcess($controllerName,false,$this->CI->session->userdata('id'));
				if($checkRow){
					//hmm entry found ok now check disable flag for this
					if($checkRow->read_write_flag==0){
						//means we have disabled for this user...
						return false;//hmm dirty guy want to exploit my system ..ok just kick him 
					}else if($checkRow->read_write_flag==1 || $checkRow->read_write_flag==2){
						//valid user found here so allow him to show selected screen
						return true;
					}
				}else{
					//hmm we haven't found any entry for this user then we are to check in to user type   
						$checkRow=$this->CI->manageCommonTaskModel->CheckAccessPermissionOnProcess($controllerName,false,0,$this->CI->session->userdata("userType"),true);
						if($checkRow){
							//hmm entry found ok now check disable flag for this
							if($checkRow->read_write_flag==0){
								//means we have disabled for this user...
								return false;//hmm dirty guy want to exploit my system ..ok just kick him 
							}else if($checkRow->read_write_flag==1 || $checkRow->read_write_flag==2){
								//valid user found here so allow him to show selected screen
								return true;
							}
						}
				}
			}else{
				//For Normal user type..
				//Hmm he has login on emergency permission,For current controller we will have to check in emergency permission also...
				//First of all we will have to check on user level..
				$checkRow=$this->CI->manageCommonTaskModel->CheckAccessPermissionOnProcess($controllerName,false,$this->CI->session->userdata('id'));
				if($checkRow){
					//hmm entry found ok now check disable flag for this
					if($checkRow->read_write_flag==0){
						//means we have disabled for this user...
						return false;//hmm dirty guy want to exploit my system ..ok just kick him 
					}else if($checkRow->read_write_flag==1 || $checkRow->read_write_flag==2){
						//valid user found here so allow him to show selected screen
						return true;
					}
				}else{
					//hmm we haven't found any entry for this user then we are to check in to user type 
						$checkRow=$this->CI->manageCommonTaskModel->CheckAccessPermissionOnProcess($controllerName,false,0,$this->CI->session->userdata("userType"),true);
						if($checkRow){
							//hmm entry found ok now check disable flag for this
							if($checkRow->read_write_flag==0){
								//means we have disabled for this user...
								return false;//hmm dirty guy want to exploit my system ..ok just kick him 
							}else if($checkRow->read_write_flag==1 || $checkRow->read_write_flag==2){
								//valid user found here so allow him to show selected screen
								return true;
							}
						}
				}
				
			}
	
	 }
	
	
    /**
     * 		Function 		
     * 		Author			Author Name<Author@greenapplestech.com>
     * 		Description		
     * 		@param			none
     * 		@return 		none
     */
    public function validationGlobalSettingLogin() {//Remove this function from Here to common model.
        if (!$this->CI->session->userdata('GlobalPassword')) {
			$CI =& get_instance();
			$httpsaccess=$CI->utilities->getGlobalSettings(1,'ch_https_access',1);
			
			if($httpsaccess == 1){
				$CI->config->config['base_url'] = str_replace('http://', 'https://', $CI->config->config['base_url']);
			}else{
				if ($_SERVER['SERVER_PORT'] == 443)  {
					$CI->config->config['base_url'] = str_replace('http://', 'https://', $CI->config->config['base_url']);
				}
			}
			
            ?>
            <script>window.location.href="<?php echo $CI->config->config['base_url']; ?>globalSetting/<?php echo $this->CI->session->userdata['userMainType']; ?>/manageGlobalSettingLogin";</script>
            <?php
        }
    }

    /**
     * 		Function 		destroySession
     * 		Author			Abhishek Singh<abhishek.singh@greenapplestech.com>
     * 		Description		This function is use to destroy session data.
     * 		@param			none
     * 		@return 		none
     */
    function destroySession() {
        delete_cookie('userName', '', '/', 'blueberry_');
        delete_cookie('password', '', '/', 'blueberry_');
        delete_cookie("userName");
        delete_cookie("password");

        $this->CI->session->sess_destroy();
        $this->handleExit();
    }/**
     * 		Function 		handleExit
     * 		Author			Abhishek Singh<abhishek.singh@greenapplestech.com>
     * 		Description		This function will use to redirect to base url.
     * 		@param			none
     * 		@return 		none
     */
    function handleExit() {
        $CI =& get_instance();
		if ($_SERVER['SERVER_PORT'] == 443)  {
			$CI->config->config['base_url'] = str_replace('http://', 'https://', $CI->config->config['base_url']);
		}
		echo "<script>window.location.href='" . $CI->config->config['base_url'] . "';</script>";
        die;
    }

    /**
     * 		Function 		activationKey
     * 		Author			Abhishek Singh<Abhishek.singh@greenapplestech.com>
     * 		Description		This function will be use to generate activation key 
     * 						in case of forget password and user activation
     * 		@param			none
     * 		@return 		none
     */
    public function activationKey() {
        $chars = "234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $activeKeyCounter = 1;
        $activeKey = '';
        while ($activeKeyCounter <= 6) {
            $activeKey .= $chars{mt_rand(0, strlen($chars) - 1)};
            $activeKeyCounter++;
        }
        return $activeKey;
    }

    /**
     * Function		sendEmail
     * author     	Rahul Anand
     * Description	It is used to send the external email
     *
     * Parameters
     * 	$param ? None
     * 	Return	- None
     * */
    public function sendEmail($from = '', $to = '', $cc = '', $bcc = '', $subject = '', $message = '', $attachment = array(), $attachmentDir = '',$sendFromOnlySystem=false,$mailType='text',$dieFalse='') 
	{
        $this->CI->load->library('email');
		$this->CI->load->library('phimail');
		$userMainType=$this->CI->session->userdata('userMainType');
		if((isset($userMainType))&&!empty($userMainType)){
			$this->CI->load->model('communication/'.$this->CI->session->userdata('userMainType').'/manageExternalEmailInformationModel');
		} else {
			$this->CI->load->model('communication/staff/manageExternalEmailInformationModel');
		}
		//First of all get the SMPT setting for specific user or from global settings
		$mailSetting=$this->CI->manageExternalEmailInformationModel->getUserAttachedExternalEmail($this->CI->session->userdata('id'));
		if($mailSetting && $sendFromOnlySystem==false){
			foreach( $mailSetting as $singleSetting)
			{
				$smtpHost=$singleSetting->smtp_server_address;
				$smtpUser=$singleSetting->external_email;
				$smtpPwd=$singleSetting->password;
				$smtpPort=$singleSetting->smtp_port;
				if ($singleSetting->smtp_encryption == '1')
					$smtpEncr = "tls";
				else if ($singleSetting->smtp_encryption == '2')
					$smtpEncr = "ssl";
				else
					$smtpEncr = "";
				break;
			}
		}else{
		
			//NOw Try to REtrieve from global settings
			$this->CI->load->model('common/manageCommonTaskModel');
			
			
			$smtpSettings=$this->CI->manageCommonTaskModel->getSmtpSettings();
			
			if(!empty($smtpSettings)){
				$smtpHost=$smtpSettings['ch_smtp_server_hostname'];
				$smtpUser=$smtpSettings['ch_smtp_username'];
				$smtpPwd=$smtpSettings['ch_smtp_password'];
				$smtpPort=$smtpSettings['ch_smtp_port_number'];
				if ($smtpSettings['ch_smtp_encryption'] == '1')
					$smtpEncr = 'tls';
				else if ($smtpSettings['ch_smtp_encryption'] == '2')
					$smtpEncr = 'ssl';
				else
					$smtpEncr = "";
			}else{
				
				if($dieFalse=='1'){
					$returnMsg=array('Sorry..!! Unable to send mail.<br>Please contact with the Administrator for SMTP settings...!!','false');
					return $returnMsg;
				}else{
					echo "Sorry..!! Unable to send mail.<br>Please contact with the Administrator for SMTP settings...!!";
					die;
				}	
			}
		}
		$config['protocol'] = "smtp";
		$config['mailtype'] = $mailType;
		$config['smtp_host'] = $smtpHost;
		$config['smtp_user'] = $smtpUser;
		$config['smtp_pass'] = $smtpPwd;
		$config['smtp_port'] = $smtpPort;
		$config['smtp_crypto'] = $smtpEncr;
		if(empty($smtpHost) || empty($smtpUser) || empty($smtpPwd)){
		/*One more time to dig in to ugly code
			If we dont find these setting from any where then just do one more try from php.ini
			
		*/
            
			
			if($dieFalse=='1'){
				$returnMsg=array('Sorry..!! Unable to send mail.<br>Please contact with the Administrator for SMTP settings...!!','false');
				return $returnMsg;
			}else{
				echo "Sorry..!! Unable to send mail.<br>Please contact with the Administrator for SMTP settings...!!";
				die;
			}
			$config['protocol'] = 'mail';
			$config['smtp_host'] = '';
			$config['smtp_user'] ='';
			$config['smtp_pass'] = '';
			$this->CI->email->initialize($config);
			// $this->load->library('email', $config);  
		}else{
			//$this->load->library('email', $config); 
			$this->CI->email->initialize($config);			
		}
        //$this->CI->email->clear();
		
		//Fetch From settings from Global Settings.
		$globSetEmail= $this->getGlobalSettings('1', 'ch_default_email_id');
        $fromEmail=$globSetEmail['ch_default_email_id'];
		$globSetEmail= $this->getGlobalSettings('1', 'ch_default_email_name');
        $fromName=$globSetEmail['ch_default_email_name'];
		
		
		
		$this->CI->email->set_newline("\r\n");
		$mailFrom = $this->setFromMail($fromEmail, $fromName);
		if(empty($from)){
			$this->CI->email->from(trim($mailFrom['from_first']), trim($mailFrom['from_second']));
		}else{
			$this->CI->email->from(trim($from), trim($from));
		}
        $this->CI->email->to($to);
        $this->CI->email->cc($cc);
        $this->CI->email->bcc($bcc);
		$this->CI->email->subject($subject);
		//If Mail Type html then we will have to put html main tag itself otherwise our html will not work now check thiss...
		$emailDisclaimer= $this->getGlobalSettings('1', 'ch_email_disclaimer');
		$message= $message."\n\n".$emailDisclaimer['ch_email_disclaimer'];
		if($mailType=='html'){
			$message=$this->outlookfilter($message);
			$this->CI->email->message("<html><body>".$message."</body></html>");
		}else{
			$message=$this->outlookfilter($message);
			$this->CI->email->message($message);
		}
		
        //Just Check for attachment if message has the attachment the send that

        if (count($attachment) > 0 && $attachment) {
            foreach ($attachment as $singleAttachment) {
                if ($attachmentDir != '') {
                    $singleAttachment = "./" . $attachmentDir . "/" . $singleAttachment;
                }
                $this->CI->email->attach($singleAttachment);
            }
        }
		if (!@$this->CI->email->send()) {

		   //print_r($config);
		   //echo $this->CI->email->print_debugger();
		   echo "Sorry..!! Unable to send mail.<br>Please contact with the Administrator for SMTP settings...!!";

			$debugMessage= $this->CI->email->get_debugger_messages();
			$errMSG=$debugMessage[0].' '.$debugMessage[2];
			$this->CI->email->clear();
			if($dieFalse=='1'){
				$returnMsg=array($errMSG,'false');
				return $returnMsg;
			}else{
				return false;
			}
        }
        $this->CI->email->clear();
       //echo $this->CI->email->print_debugger();
        return true;
    }
	
	
	/**
     * Function		sendFaxByEmail
     * author     	Kuldeep Verma
     * Description	It is used to send the fax via email
     *
     * Parameters
     * 	$param ? None
     * 	Return	- None
     * */
    function sendFaxByEmail($from = '', $to = '', $cc = '', $bcc = '', $subject = '', $message = '', $attachment = array(), $attachmentDir = '',$sendFromOnlySystem=false,$mailType='text',$dieFalse='') 
	{
        $this->CI->load->library('email');
		$this->CI->load->library('phimail');
		$userMainType=$this->CI->session->userdata('userMainType');
		if((isset($userMainType))&&!empty($userMainType)){
			$this->CI->load->model('communication/'.$this->CI->session->userdata('userMainType').'/manageExternalEmailInformationModel');
		} else {
			$this->CI->load->model('communication/staff/manageExternalEmailInformationModel');
		}
		//First of all get the SMPT setting for fax from other master -> fax settings
		$faxSetting=$this->CI->manageCommonTaskModel->getRecord("ch_commu_externalfax_config","*",array('fax_type'=>'2'));
		if($faxSetting && $sendFromOnlySystem==false){
			$smtpHost=$faxSetting['smtp_server_address'];
			$smtpUser=$faxSetting['external_email'];
			$smtpPwd=$faxSetting['password'];
			$smtpPort=$faxSetting['smtp_port'];
			if ($faxSetting['smtp_encryption'] == '1')
				$smtpEncr = "tls";
			else if ($faxSetting['smtp_encryption'] == '2')
				$smtpEncr = "ssl";
			else
				$smtpEncr = "";
		}else{
		
			//NOw Try to REtrieve from global settings
			$this->CI->load->model('common/manageCommonTaskModel');
			
			
			$smtpSettings=$this->CI->manageCommonTaskModel->getSmtpSettings();
			
			if(!empty($smtpSettings)){
				$smtpHost=$smtpSettings['ch_smtp_server_hostname'];
				$smtpUser=$smtpSettings['ch_smtp_username'];
				$smtpPwd=$smtpSettings['ch_smtp_password'];
				$smtpPort=$smtpSettings['ch_smtp_port_number'];
				if ($smtpSettings['ch_smtp_encryption'] == '1')
					$smtpEncr = 'tls';
				else if ($smtpSettings['ch_smtp_encryption'] == '2')
					$smtpEncr = 'ssl';
				else
					$smtpEncr = "";
			}else{
				echo "Sorry..!! Unable to send fax mail.<br>Please contact with the Administrator for SMTP settings...!!";
				if($dieFalse=='1'){
					$returnMsg=array('Sorry..!! Unable to send fax  mail.<br>Please contact with the Administrator for SMTP settings...!!','false');
					return $returnMsg;
				}else{
					die;
				}	
			}
		}
		$config['protocol'] = "smtp";
		$config['mailtype'] = $mailType;
		$config['smtp_host'] = $smtpHost;
		$config['smtp_user'] = $smtpUser;
		$config['smtp_pass'] = $smtpPwd;
		$config['smtp_port'] = $smtpPort;
		$config['smtp_crypto'] = $smtpEncr;
		if(empty($smtpHost) || empty($smtpUser) || empty($smtpPwd)){
		/*One more time to dig in to ugly code
			If we dont find these setting from any where then just do one more try from php.ini
		*/
            
			echo "Sorry..!! Unable to send fax mail.<br>Please contact with the Administrator for SMTP settings...!!";
			if($dieFalse=='1'){
				$returnMsg=array('Sorry..!! Unable to send fax mail.<br>Please contact with the Administrator for SMTP settings...!!','false');
				return $returnMsg;
			}else{
				die;
			}
			$config['protocol'] = 'mail';
			$config['smtp_host'] = '';
			$config['smtp_user'] ='';
			$config['smtp_pass'] = '';
			$this->CI->email->initialize($config);
		}else{
			$this->CI->email->initialize($config);			
		}
		
		//Fetch From settings from Global Settings.
		$globSetEmail= $this->getGlobalSettings('1', 'ch_default_email_id');
        $fromEmail=$globSetEmail['ch_default_email_id'];
		$globSetEmail= $this->getGlobalSettings('1', 'ch_default_email_name');
        $fromName=$globSetEmail['ch_default_email_name'];
		
		
		
		$this->CI->email->set_newline("\r\n");
		$mailFrom = $this->setFromMail($fromEmail, $fromName);
		if(empty($from)){
			$this->CI->email->from(trim($mailFrom['from_first']), trim($mailFrom['from_second']));
		}else{
			$this->CI->email->from(trim($from), trim($from));
		}
        $this->CI->email->to($to);
        $this->CI->email->cc($cc);
        $this->CI->email->bcc($bcc);
		$this->CI->email->subject($subject);
		//If Mail Type html then we will have to put html main tag itself otherwise our html will not work now check thiss...
		$emailDisclaimer= $this->getGlobalSettings('1', 'ch_email_disclaimer');
		$message= $message."\n\n".$emailDisclaimer['ch_email_disclaimer'];
		if($mailType=='html'){
			$message=$this->outlookfilter($message);
			$this->CI->email->message("<html><body>".$message."</body></html>");
		}else{
			$message=$this->outlookfilter($message);
			$this->CI->email->message($message);
		}
		
        //Just Check for attachment if message has the attachment the send that

        if (count($attachment) > 0 && $attachment) {
            foreach ($attachment as $singleAttachment) {
                if ($attachmentDir != '') {
                    $singleAttachment = "./" . $attachmentDir . "/" . $singleAttachment;
                }
                $this->CI->email->attach($singleAttachment);
            }
        }
		if (!@$this->CI->email->send()) {
			//echo '<pre>';
		    // print_r($config);die;
		    //echo $this->CI->email->print_debugger();die;
		    echo "Sorry..!! Unable to send fax mail.<br>Please contact with the Administrator for SMTP settings...!!";

			$debugMessage= $this->CI->email->get_debugger_messages();
			$errMSG=$debugMessage[0].' '.$debugMessage[2];
			$this->CI->email->clear();
			if($dieFalse=='1'){
				$returnMsg=array($errMSG,'false');
				return $returnMsg;
			}else{
				return false;
			}
        }
        $this->CI->email->clear();
        //echo $this->CI->email->print_debugger();
        return true;
    }

    /**
     * 		Function 		mail
     * 		Author			Abhishek Singh<abhishek.singh@greenapplestech.com>
     * 		Description		This function will be use to send external mail.
     * 		@param			$to
     * 		@param			$from_first
     * 		@param			$from_second
     * 		@param			$subject
     * 		@param			$message
     * 		@param			$mailtype 0->Plain Text 1->HTML Content
     * 		@return 		True/False.
     */
    public function mail($to, $from_first, $from_second, $subject, $message, $mailtype = 1) {
        $this->CI->load->library('email');
		$this->CI->load->library('phimail');
        If (!empty($mailtype)) {
            $config = $this->setMailType($mailtype);
            $this->CI->email->initialize($config);
        }
		
		
        $dMessage = $this->appendMailDisclaimer($message); 
       $this->CI->email->set_newline("\r\n");
	   
        //echo $dMessage;die;
        $mailFrom = $this->setFromMail($from_first, $from_second);
        $this->CI->email->from($mailFrom['from_first'], $mailFrom['from_second']);
        $this->CI->email->to($to);
        $this->CI->email->subject($subject);
        $this->CI->email->message($dMessage);
        if ($this->CI->email->send()) {
            return TRUE;
        } else {
            return FALSE;
        }
		
    }
	/**
     * 		Function 		outlookfilter
     * 		Author			Rahul Anand
     * 		Description		This function will be use to Filter the text for special OutLook HTML content.
     * 		@param			$textToBeFiltered
     * 		@return 		Filtered Text
     */
	function outlookfilter($text)
	{    
        $text = str_replace("<br />","<br>",$text);
        $text = str_replace("&nbsp;","",$text);
        $text = str_replace("&#39;","'",$text);        
		return $text;
	}


    /**
     * 		Function 		setMailType
     * 		Author			Abhishek Singh<abhishek.singh@greenapplestech.com>
     * 		Description		This function will be use to set email configuration for HTML content.
     * 		@param			$mailtype
     * 		@return 		Mail configuration ir false.
     */
    protected function setMailType($mailtype) {
        If (!empty($mailtype)) {
            $config['charset'] = 'utf-8';
            $config['wordwrap'] = TRUE;
            $config['mailtype'] = 'html';
            return $config;
        } else {
            return false;
        }
    }

    /**
     * 		Function 		appendMailDisclaimer
     * 		Author			Abhishek Singh<abhishek.singh@greenapplestech.com>
     * 		Description		This function will be use to append disclaimer with message if set.
     * 		@param			$message
     * 		@return 		Appended Message with disclaimer or same mesasage.
     */
    protected function appendMailDisclaimer($message) {
        if (isset($message)) {
            $disclaimerMsg = $this->getGlobalSettings('1', 'ch_email_disclaimer', '1');
            if (isset($disclaimerMsg)) {
                $message = $message . "<br><br>" . $disclaimerMsg;
            }
            return $message;
        } else {
            return false;
        }
    }

    /**
     * 		Function 		setFromMail
     * 		Author			Rahul Anand<rahul.anand@greenapplestech.com>
     * 		Description		This function will be use to set from part of mail.
     * 		@param			$from_first
     * 		@param			$from_second
     * 		@return 		MailFrom array[From-first and from-second).
     */
    function setFromMail($from_first = '', $from_second = '') {
        $this->CI->load->model('common/manageCommonTaskModel');
        $clinic_name = '';
        $clinic_email = '';
        if ($from_first == '' or $from_first == null or !isset($from_first)) {
            $all_clinic_info = $this->CI->manageCommonTaskModel->showClinicDetails();
            if ($all_clinic_info) {
                $clinic_name = $all_clinic_info->clinic_name;
                $clinic_email = $all_clinic_info->email;
            }
            if ($clinic_name != '' and isset($clinic_name) and $clinic_name != null) {
                if ($clinic_email and isset($clinic_email) and $clinic_email != null) {
                    $from_first = $clinic_email;
                } else {
                    $from_first = 'admin@connect_health.com';
                }
                $from_second = $clinic_name;
            } else {
                $from_first = 'admin@connect_health.com';
                $from_second = 'Administrator Clinic';
            }
        }
        return $mailFromAddress = array('from_first' => $from_first,
            'from_second' => $from_second);
    }

    /**
     * 		Function 		debugLog
     * 		Author			Md. Masroor<md.masroor@greenapplestech.com>
     * 		Description		This function will be use to log messages for ajax call.
     * 		@param			$note
     * 		@param			$str
     * 		@return 		If true then write log in file otherwise return false.
     */
    function debugLog($note, $str) {
        if (!empty($note) && !empty($str)) {
            $this->CI->load->helper('file');
            $dirName = './';
            if (is_array($str)) {
                ob_start();
                print_r($str);
                $str = ob_get_contents();
                ob_end_clean();
            }
            $logData = "$note: $str\n";
            write_file($dirName . 'ajaxLog.txt', $logData, 'a+');
        } else {
            return false;
        }
    }

    /**
     * 		Function 		
     * 		Author						Author Name<Author@greenapplestech.com>
     * 		Description		
     * 		@param						none
     * 		@return 					none
     * 		Modified					25 Sep-2012
     * 		Modified By					Rahul Anand
     * 		Modification Description	there are two paramater added one is date on which 
     * 									User want to apply format, and second is valid 		
      php date format what user will pass. and the date will be foramted accroding to user supplied format
     */
    function convertDateFormat($date, $format = "m-d-Y") {
       /* If we have any general setting in which we have defined the date format
          or in session then we can extract that here and pass the following funtion
         */
		$dateFormat = $this->CI->manageCommonTaskModel->getGlobalSettings('1', 'ch_default_date_format');

		if($dateFormat['ch_default_date_format'] =='d-m-Y'){
			$dmystring = explode("-",$suppliedDate);
			$suppliedDate = $dmystring[1]."-".$dmystring[0]."-".$dmystring[2];
		}
		if($suppliedDate!=''){
			$suppliedDate = str_replace("-", "/", $suppliedDate);
			if (!isset($suppliedDate) or $suppliedDate == "") {
				$date = date("m-d-Y"); //put current date 
			}
			$dateObj = new DateTime($suppliedDate);
			$dateForDbase = $dateObj->format('Y-m-d');
			//return date('Y-m-d', strtotime($suppliedDate));
			return $dateForDbase;
		}else{
			$dateObj = new DateTime($date);
			$dateForDbase = $dateObj->format($format);
			//return date($format, strtotime($date));
			return $dateForDbase;
		}
    }

    /**
     * 		Function 					convertDateFormatForDbase
     * 		Author						Author Name<Author@greenapplestech.com>
     * 		Description		
     * 		@param						none
     * 		@return 					none
     * 		Modified					25 Sep-2012
     * 		Modified By					Rahul Anand
     * 		Modification Description	This funciton will return the date in database saving format
     */
    function convertDateFormatForDbase($suppliedDate, $format = "Y-m-d") {
        /* If we have any general setting in which we have defined the date format
          or in session then we can extract that here and pass the following funtion
         */
		 if((empty($suppliedDate) or $suppliedDate=='0000-00-00' or strlen($suppliedDate)<10)){
			return false; 
		 }
		$dateFormat = $this->CI->manageCommonTaskModel->getGlobalSettings('1', 'ch_default_date_format');
		if($dateFormat['ch_default_date_format'] =='d-m-Y'){
			$dmystring = explode("-",$suppliedDate);
		 $suppliedDate = $dmystring[1]."-".$dmystring[0]."-".$dmystring[2];
			
		}
		if($suppliedDate!=''){
			$suppliedDate = str_replace("-", "/", $suppliedDate);
			if (!isset($suppliedDate) or $suppliedDate == "") {
				$date = date("m-d-Y"); //put current date 
			}
			$dateObj = new DateTime($suppliedDate);
			$dateForDbase = $dateObj->format('Y-m-d' );
			//return date('Y-m-d', strtotime($suppliedDate));
			return $dateForDbase;
		}else{
			return false;
		}
    }
	
	function convertDateFormatForDbase4DMY($suppliedDate, $format = "Y-m-d") {
        /* If we have any general setting in which we have defined the date format
          or in session then we can extract that here and pass the following funtion
         */
		//$dateFormat = $this->CI->manageCommonTaskModel->getGlobalSettings('1', 'ch_default_date_format');
		//if($dateFormat['ch_default_date_format'] =='d-m-Y'){
		$dmystring = explode("-",$suppliedDate);
		$suppliedDate = $dmystring[1]."-".$dmystring[0]."-".$dmystring[2];
			
		//}
		if($suppliedDate!=''){
			$suppliedDate = str_replace("-", "/", $suppliedDate);
			if (!isset($suppliedDate) or $suppliedDate == "") {
				$date = date("m-d-Y"); //put current date 
			}
			$dateObj = new DateTime($suppliedDate);
			$dateForDbase = $dateObj->format('Y-m-d' );
			//return date('Y-m-d', strtotime($suppliedDate));
			return $dateForDbase;
		}else{
			return false;
		}
    }

    /**
     * 		Function 					showDateForSpecificTimeZone
     * 		Author						RAHUL aNAND<rahul.anand@greenapplestech.com>
     * 		Description		
     * 		@param						Valid PHp Date
     * 		@return 					none
     * 		Modified					30 Jan-2013
     * 		Modified By					Rahul Anand
     * 		Modification Description	This funciton will return the date after converting the user specified(from dbase) timezone
     */ 
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
		$timestamp = 0;
       /* $timestamp=strtotime($dateTime);
		
		if($timestamp===false){
			return null;
		}*/
        //First of all get the time zone from dbase
        
        $defaultDateFormat = '';
        if (empty($userDefinedDateFormat)) {
            $dateFormat = $this->CI->manageCommonTaskModel->getGlobalSettings('1', 'ch_default_date_format');

            if (!$dateFormat['ch_default_date_format']) {
                $defaultDateFormat = DATE_W3C; //set default 
            } else {
                //Le't check that dbase has date format setting or not
                $defaultDateFormat = $dateFormat['ch_default_date_format'];
            }
        } else {
            $defaultDateFormat = $userDefinedDateFormat;
        }
        
        if(!empty($userDefinedTimeZone)){
            $timezone=$userDefinedTimeZone;
        }else{
            $savedTimeZone = $this->CI->manageCommonTaskModel->getGlobalSettings('2', 'ch_default_time_zone');
            $timezone = $savedTimeZone['ch_default_time_zone'];        
        }
        
        if (!$timezone)
            $timezone = 'UTC';
        $daylight_saving = false;
        $returnedUnixTimeStamp = gmt_to_local($timestamp, $timezone, $daylight_saving);
        //echo $defaultDateFormat;
        $returnedDate=date($defaultDateFormat, $timestamp);
		//return date($defaultDateFormat, $timestamp);
		return $dt1->format($defaultDateFormat);
	}
	
	function showDateForDMYFormat($dateTime)
	{
		$explodedDate= explode('-', $dateTime);
		$newDate= $explodedDate['2'].'-'.$explodedDate['1'].'-'.$explodedDate['0'];
		return $newDate;
	}
    
    
    
    
    /**
     * 		Function 					showPatientNameSetAsGlobalSetting
     * 		Author						Prashant Sahu<prashant.sahu@greenapplestech.com>
     * 		Description		
     * 		@param						name formated according to global seting
     * 		@return 					none
     * 		Modified					24 Jan-2014
     * 		Modified By					Prashant Sahu
     * 		Modification Description	This funciton will return the Name as  specified in general setting
     */ 
    function showPatientNameSetAsGlobalSetting($patientId='') {
	
        $patientFormat = $this->CI->manageCommonTaskModel->getGlobalSettings('1', 'ch_patient_name_format');
        $patientNameFormat=$patientFormat['ch_patient_name_format'];        
        $patientFullName=  $this->userFullName($patientId,'psw');
		$patientFullNameArr=@explode(",",$patientFullName);
        $patientFullNameFormated='';        
        switch($patientNameFormat){            
            case "lf":
            $patientFullNameFormated=ucfirst(strtolower($patientFullNameArr['2'])).", ".ucfirst(strtolower($patientFullNameArr['0']));
            return ucwords(trim($patientFullNameFormated));
            break;
            case "lfm":
            $patientFullNameFormated= ucfirst(strtolower($patientFullNameArr['2']))." ".ucfirst(strtolower($patientFullNameArr['0']))." ".ucfirst(strtolower($patientFullNameArr['1']));
            return ucwords(strtolower(trim($patientFullNameFormated)));
            break;
            case "fml":
            $patientFullNameFormated= ucfirst(strtolower($patientFullNameArr['0']))." ".ucfirst(strtolower($patientFullNameArr['1']))." ".ucfirst(strtolower($patientFullNameArr['2']));
            return ucwords(strtolower(trim($patientFullNameFormated)));
            break;
            default:
            $patientFullNameFormated= ucfirst(strtolower($patientFullNameArr['0']))." ".ucfirst(strtolower($patientFullNameArr['2']));
            return ucwords(strtolower(trim($patientFullNameFormated)));
            }
            
            
       
        
    }

    /**
     * 		Function 		createRandomPassword
     * 		Author			Abhishek Singh<abhishek.singh@greenapplestech.com>
     * 		Description		This function will be use to create random password.
     * 		@param			none
     * 		@return 		random auto generted string.
     */
    function createRandomPassword() {
        $chars = "abcdefghijkmnopqrstuvwxyz023456789";
        srand((double) microtime() * 1000000);
        $i = 0;
        $pass = '';
        while ($i <= 7) {
            $num = rand() % 33;
            $tmp = substr($chars, $num, 1);
            $pass = $pass . $tmp;
            $i++;
        }
        return $pass;
    }

    /**
     * 		Function 		FgetSeleUserTypeDropDown
     * 		Author			Anshul Agrawal<anshul.agrawal@greenapplestech.com>
     * 		Description		This function will be use to generate dropdown of User Type.
     * 		@param			$notApplicalble
     * 		@param			$selectedUserType:  if you want any value selected in dropdown.
     * 		@return 		User Type Dropdown or false.
     */
    function getUserTypeDropDown($notApplicalble = '', $selectedUserType = '') {
        $this->CI->load->model('common/manageCommonTaskModel');
        if ($this->CI->session->userdata('userType')) {
            return $this->CI->manageCommonTaskModel->userTypeDropDown($notApplicalble, $this->CI->session->userdata('userType'), $selectedUserType);
        } else {
            return false;
        }
    }

    /**
     * 		Function 		getUserTypeKeyValueMapping
     * 		Author			Anshul Agrawal<anshul.agrawal@greenapplestech.com>
     * 		Description		This function will be use to generate key value mapping of the user types (id=>user_type_title).
     * 		
     * 		@return 		array,false
     */
    function getUserTypeKeyValueMapping() {
        $this->CI->load->model('common/manageCommonTaskModel');
        $userTypes = $this->CI->manageCommonTaskModel->getRecord(TBL_USER_TYPE, "id,user_type_title");
        if (!empty($userTypes)) {
            foreach ($userTypes as $type) {
                $userTypeArray[$type['id']] = $type['user_type_title'];
            }
            return $userTypeArray;
        } else {
            return false;
        }
    }

    /**
     * 		Function 		changePassword
     * 		Author			Author Name<Author@greenapplestech.com>
     * 		Description		This function will be used to redirect user to password setting page.
     * 		@param			none
     * 		@return 		none
     */
    function changePassword() {
        ?>
        <script type="text/javascript">window.location.href="<?php echo base_url(); ?>globalSetting/<?php echo $this->CI->session->userdata['userMainType']; ?>/manageGlobalSettingChangePassword";</script>
        <?php
    }

    /**
     * 		Function 		This Function need to be interlinked with IntraMessaging 
     * 						from communication.
     * 		Author			Author Name<Author@greenapplestech.com>
     * 		Description		
     * 		@param			subject
     * 		@param			mesaage
     * 		@param			sender
     * 		@param			receiver				
     * 		@return 		none
     */
    function sendIntraMessage($subject = '', $mesaage = '', $sender = '', $receiver = array(),$patientId="",$lab_result_id=0) {
        $this->CI->load->model('common/manageCommonTaskModel');
        if ($this->CI->session->userdata('userType')) {
            return $this->CI->manageCommonTaskModel->sendIntraMessage($subject, $mesaage, $sender, $receiver,$patientId,$lab_result_id);
        } else {
            return false;
        }
    }

    /**
     * 		Function 		createUserLoginLog
     * 		Author			Abhishek Singh<abhishek.singh@greenapplestech.com>
     * 		Description		This funtion will create a login log file.
     * 		@param			$user:	User Login Id
     * 		@Param			$Type:	1->Logged In,2->Logged Out		
     * 		@return 		Write in log file or return false.
     */
    function createUserLoginLog($user = '', $type = '1') {
        $this->CI->load->helper('file');
        $maxFileSize = $this->CI->manageCommonTaskModel->getGlobalSettings('2', 'ch_user_log_file_size');
        $dirName = LOG_PATH . 'userLog/';
        $fileName = $dirName . 'userLog';
        $fileExt = '.txt';
        $filepath = $fileName . $fileExt;
        if (file_exists($filepath)) {
            $fileActualSizeInMb = round((filesize($filepath) / (1024 * 1024)));
        } else {
            $fileActualSizeInMb = 0;
        }

        if ($fileActualSizeInMb >= $maxFileSize['ch_user_log_file_size']) {
            rename($filepath, $fileName . '_' . date("d-m-Y") . $fileExt);
        }
        if (!empty($user)) {
            if ($type == '1') {
                $logData = "LOG - " . date('Y-m-d H:i:s') . " " . $user . " Logged In" . "\r\n";
            } else {
                $logData = "LOG - " . date('Y-m-d H:i:s') . " " . $user . " Logged Out" . "\r\n";
            }
            if (!is_dir($dirName)) {
                if (!mkdir($dirName, 0777)) {
                    $dirName = './';
                }
            }
            write_file($filepath, $logData, 'a+');
        } else {
            return false;
        }
    }
	function a(){
		$this->CI->load->model('common/manageCommonTaskModel');
		return $this->CI->manageCommonTaskModel->getGlobalSettingCountryState();
	}

    /**
     * 		Function 		showCountryCityStateDropDown
     * 		Author			Rahul Anand<rahul.anand@greenapplestech.com>
     *       Modified        Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 		Description		This Function is being used to display the Country,City,State Drop Downs
     * 		@param			$selected_option
     * 		@return 		none
     */
    function showCountryCityStateDropDown($selected_option = array(),$dontFilterTheResult=false) {

        $this->CI->load->model('common/manageCommonTaskModel');
        $all_country = $this->CI->manageCommonTaskModel->getAllCountry();
		$countryState = $this->CI->manageCommonTaskModel->getGlobalSettingCountryState();
        $inital_country = 0;
        $inital_state = 0;
        $country_str = '';
        $currency_str = '';
        $state_str = '';
        $city_str = '';
        $selected_country = 0;
        $selected_city = 0;
        $selected_state = 0;
        $selected_currency = 0;
		if($dontFilterTheResult==true){
			$selected_option['selected_country']='';
            $selected_option['selected_city']='';
            $selected_option['selected_state']='';
		}
        if (count($selected_option) > 0 ) {
                    if (!isset($selected_option['selected_country'])) {
                            $selected_option['selected_country']=$countryState['ch_default_country'];
                    }
                    $selected_country = $selected_option['selected_country'];

                    $selected_city = $selected_option['selected_city'];
			
			
			if (!isset($selected_option['selected_state'])) {
				$selected_option['selected_state']=$countryState['ch_default_state'];
			}
                        $selected_state = $selected_option['selected_state'];
            
			if (!isset($selected_option['selected_currency'])) {
                        $selected_currency = $selected_option['selected_currency'];
            }
        }else{
			$selected_option['selected_country']=$countryState['ch_default_country'];
			$selected_country = $selected_option['selected_country'];
			$selected_option['selected_state']=$countryState['ch_default_state'];
			$selected_state = $selected_option['selected_state'];
		}        
        if ($all_country) {
            foreach ($all_country as $single_country) {
                if ($inital_country == 0 && count($selected_option) <= 0) {
                    $inital_country = $single_country->id;
                }
                if ($selected_country != 0 && $selected_country == $single_country->id) {
                    $country_str.="<option selected='selected' value='" . $single_country->id . "'>$single_country->country_name</option>";
                } else{
					$country_str.="<option value='" . $single_country->id . "'>$single_country->country_name</option>";
				}
                if($single_country->currency!='' && $single_country->currency_symbol!=''){
					if ($selected_currency != 0 && $selected_currency == $single_country->id) {
						$currency_str.="<option selected='selected' value='" . $single_country->id . "'>$single_country->currency-$single_country->currency_symbol</option>";
					} else{
						$currency_str.="<option value='" . $single_country->id . "'>$single_country->currency-$single_country->currency_symbol</option>";
					}
                }
            }
            $all_state = $this->CI->manageCommonTaskModel->getAllState($inital_country);
            if ($all_state) {

                foreach ($all_state as $single_state) {
                    if ($inital_country == 0 && count($selected_option) <= 0) {
                        $inital_state = $single_state->id;
                    }
                    if ($selected_state != 0 && $selected_state == $single_state->id) {
                        $state_str.="<option selected='selected' value='" . $single_state->id . "'>$single_state->iso_code</option>";
                    } else{
						$state_str.="<option value='" . $single_state->id . "'>$single_state->iso_code</option>";
					}
                }
            }

            $all_city = $this->CI->manageCommonTaskModel->getAllCity($inital_state);
            if ($all_city) {

                foreach ($all_city as $single_city) {
                    if ($selected_city != 0 && $selected_city == $single_city->id) {
                        $city_str.="<option selected='selected' value='" . $single_city->id . "'>$single_city->city_name</option>";
                    } else {
                        $city_str.="<option value='" . $single_city->id . "'>$single_city->city_name</option>";
                    }
                }
            }
        }

        return $CountryStateCity = array(
            'country_list' => $country_str,
            'state_list' => $state_str,
            'city_list' => $city_str,
            'currency_list' => $currency_str
        );
    }

    /**
     * 		Function 		showCityStateCountryDropDown
     * 		Author			Rahul Anand<rahul.anand@greenapplestech.com>
     *      
     * 		Description		This Function is being used to display the City,State,Country Drop Downs
     * 		@param			$selected_option
     * 		@return 		none
     */
    function showCityStateCountryDropDown($selected_option = array()) {

        $this->CI->load->model('common/manageCommonTaskModel');
        $all_country = $this->CI->manageCommonTaskModel->getAllCountry();
        $inital_country = 0;
        $inital_state = 0;
        $country_str = '';
        $currency_str = '';
        $state_str = '';
        $city_str = '';
        $selected_country = 0;
        $selected_city = 0;
        $selected_state = 0;
        $selected_currency = 0;
        $inital_city = 0;
        if (count($selected_option) > 0) {
            $selected_country = $selected_option['selected_country'];
            $selected_city = $selected_option['selected_city'];
            $selected_state = $selected_option['selected_state'];
            if (isset($selected_option['selected_currency'])) {
                $selected_currency = $selected_option['selected_currency'];
            }
        }
		$selected_option['selected_city']=0;
		$selected_option['selected_state']=0;
		$selected_option['selected_currency']=0;
        $all_city = $this->CI->manageCommonTaskModel->getAllCity();
		if ($all_city) {
		
            foreach ($all_city as $single_city) {
                if ($inital_city == 0 && count($selected_option) <= 0) {
                    $inital_city = $single_city->id;
                }
                if ($selected_city != 0 && $selected_city == $single_city->id) {
                    $city_str.="<option selected='selected' value='" . $single_city->id . "'>$single_city->city_name</option>";
                } else {
                    $city_str.="<option value='" . $single_city->id . "'>$single_city->city_name</option>";
                }
            }
            $all_state = $this->CI->manageCommonTaskModel->getStateCountryByCity($inital_city);
            if ($all_state) {

                foreach ($all_state as $single_state) {
                    if ($inital_state == 0 && count($selected_option) <= 0) {
                        $inital_state = $single_state->id;
                    }
                    if ($selected_state != 0 && $selected_state == $single_state->id) {
                        $state_str.="<option selected='selected' value='" . $single_state->id . "'>$single_state->state_name</option>";
                    } else {
                        $state_str.="<option value='" . $single_state->id . "'>$single_state->state_name</option>";
                    }
                }
            }

            $all_country = $this->CI->manageCommonTaskModel->getStateCountryByCity($inital_city, $inital_state);
			if ($all_country) {
				foreach ($all_country as $single_country) {
                    if ($selected_country != 0 && $selected_country == $single_country->id) {
                        $country_str.="<option selected='selected' value='" . $single_country->id . "'>$single_country->country_name</option>";
                    } else {
                        $country_str.="<option value='" . $single_country->id . "'>$single_country->country_name</option>";
                    }
                }
            }
        }

        return $CountryStateCity = array(
            'country_list' => $country_str,
            'state_list' => $state_str,
            'city_list' => $city_str
        );
    }

    /**
     * 		Function 		getStateList
     * 		Author			Rahul Anand<rahul.anand@greenapplestech.com>
     * 		Description		This Function is being used to display the State list
     * 		@param			country_id (By POST using Ajax)
     * 		@return 		State list Options.
     */
    function getStateList($flag='',$countryId='') {
        $this->CI->load->model('common/manageCommonTaskModel');
        $state_str = '';
        if(empty($countryId))
        $country_id = $this->input->post('country_id');
        else
        $country_id = $countryId;    
        $all_state = $this->CI->manageCommonTaskModel->getAllState($country_id);
        if ($all_state) {
            foreach ($all_state as $single_state) {
                $state_str.="<option value='" . $single_state->id . "'>$single_state->state_name</option>";
            }
        }else{
            $state_str="<option value='0'>--</option>";
        }
        if($flag==1){
            return $state_str;
        }else{
            echo $state_str;
        }
    }

    /**
     * 		Function 		getCityList
     * 		Author			Rahul Anand<rahul.anand@greenapplestech.com>
     * 		Description		This Function is being used to display the City list
     * 		@param			country_id (By POST using Ajax)
     * 		@return 		City List Options
     */
    function getCityList() {
        $this->CI->load->model('common/manageCommonTaskModel');
        $city_str = '';
        $state_id = $this->input->post('state_id');
        $all_city = $this->CI->manageCommonTaskModel->getAllCity($state_id);
        if ($all_city) {
            foreach ($all_city as $single_city) {
                $city_str.="<option value='" . $single_city->id . "'>$single_city->city_name</option>";
            }
        }
        echo $city_str;
    }

    /**
     * 		Function 		getMultiRoleHtml
     * 		Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 		Description		This Function is being used to get the html of multiple roles
     * 		@param			$id
     * 		@param			$userType
     * 		@return 		multiple role list html.
     */
    function getMultiRoleHtml($id, $userType) {
        if (!empty($id)) {
            $this->CI->load->model('userProfileManagement/' . $this->CI->session->userdata('userMainType') . '/manageUserModel');
            $multiRoles = $this->CI->manageUserModel->getMultipleRoles($id);
            $role_str = "";
            if (count($multiRoles) > 1) {
                $role_str = '<select name="changeRole" id="changeRole" onchange="javascript:switchRole(this.value);">';
                foreach ($multiRoles as $key => $role) {
                    if ($userType == $key) {
                        $role_str.="<option value='" . $key . "' selected>$role</option>";
                    } else {
                        $role_str.="<option value='" . $key . "'>$role</option>";
                    }
                }
                $role_str.='</select>';
            } else {
                foreach ($multiRoles as $key => $role) {
                    $role_str = '<span style="color:#ffffff;">' . $multiRoles[$key] . '</span>';
                }
            }
            return $role_str;
        } else {
            return false;
        }
    }

    /**
     * 		Function 		getPasswordHash
     * 		Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 		Description		This Function is being used to encrypt the password by hasing
     * 		@param			$password
     * 		@return 		will return encripted password or false.
     */
    function getPasswordHash($password) {
        if (!empty($password)) {
            return md5($password);
        } else {
            return false;
        }
    }

    /**
     * 		Function 		showLanguageDropDown
     * 		Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 		Description		This Function is being used to display the Language Drop Downs
     * 		@param			$selected_option
     * 		@return 		none
     */
    function showLanguageDropDown($selected_option = array()) {
        $this->CI->load->model('common/manageCommonTaskModel');
        $all_language = $this->CI->manageCommonTaskModel->getAllLanguages();
        $inital_language = 0;
        $language_str = '';
        $selected_language = 0;

        if (count($selected_option) > 0) {
            $selected_language = $selected_option['selected_language'];
        }
        if ($all_language) {
            foreach ($all_language as $single_language) {
                if ($inital_language == 0 && count($selected_option) <= 0) {
                    $inital_language = $single_language->id;
                }
                if ($selected_language != 0 && $selected_language == $single_language->id) {
                    $language_str.="<option selected='selected' value='" . $single_language->id . "'>$single_language->lang_name</option>";
                } else {
                    $language_str.="<option value='" . $single_language->id . "'>$single_language->lang_name</option>";
                }
            }
        }
        return $language = array('language_list' => $language_str);
    } 

    /**
     * 	Function		showImage
     * 	Author			Anshul Agarwak<anshul.agarwal@greenapplestech.com>
     * 	Description		this function is used to create a img source html
     * 	@param			$fileValue
     * 	@param			$folderName(Optional)
     * 	@return			image src code
     */
    function showImage($fileValue, $folderName = '') {
        $noImageSrc = base_url().NO_IMAGE_URL;
        $filePath = '';
        if (!empty($fileValue)) {
            if (!empty($folderName)) {
                $filePath = ATTACHMENT_UPLOAD . "\\" . $folderName . '\\' . $fileValue;
                $fileUrl = base_url().ATTACHMENT_URL . '/' . $folderName . '/' . $fileValue;
            } else {
                $filePath = ATTACHMENT_UPLOAD . "\\" . $fileValue;
                $fileUrl = base_url().ATTACHMENT_URL . '/' . $fileValue;
            }
        }
        if (file_exists($filePath)) {
            return $fileUrl;
        } else {
            return $noImageSrc;
        }
    }

    /**
     * 	Function		getGlobalSettings
     * 	Author			Anshul Agarwak<anshul.agarwal@greenapplestech.com>
     * 	Description		this function is used to return the global settings
     * 	@param			$setting_type_id
     * 	@param			$setting_name=>optionsl
     *                          $typeOfReturn = > 0=>array,1=>value    	
     * 	@return			setting_values or false;
     */
    function getGlobalSettings($setting_type_id, $setting_name = "", $typeOfReturn = "0") {
        if (!empty($setting_type_id)) {
            $this->CI->load->model('common/manageCommonTaskModel');
            $settings = $this->CI->manageCommonTaskModel->getGlobalSettings($setting_type_id, $setting_name);

            if ((empty($setting_name)) || ($typeOfReturn == '0')) {
                return $settings;
            } else if ((array_key_exists($setting_name, $settings)) && ($typeOfReturn == '1')) {
                return $settings[$setting_name];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 	Function		getBackButtonUrl
     * 	Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 	Description		this function is used to return the back button url
     * 	@param			$url(Optional)
     * 	@return			url
     */
    function getBackButtonUrl($backUrl = '') {
        if (empty($backUrl)) {
            if (!empty($_SERVER['HTTP_REFERER']))
                $backUrl = $_SERVER['HTTP_REFERER'];
        }
        return $backUrl;
    }

    /**
     * 	Function		getAllSettingsHtml
     * 	Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 	Description		this function is used to generate the Html of the all settings
     * 	@return			html
     */
    function getAllSettingsHtml() {
        $html = '';
        $html.='<div id="settingAccordion">';
        $html.='<h3 id="systemLabel" class="ui-accordion-header ui-helper-reset ui-state-default ui-state-active ui-corner-top"><a href="#">System Settings</a></h3>';
        $html.='<div class="systemSettings ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" style="background-color:#ffffff;">';
        $html.='</div>';
        $html.='<h3 id="logLabel" class="ui-accordion-header ui-helper-reset ui-state-default ui-state-active ui-corner-top"><a href="#">Log Settings</a></h3>';
        $html.='<div class="logSettings ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" style="background-color:#ffffff;">';
        $html.='</div>';
        $html.='<h3 id="backupLabel" class="ui-accordion-header ui-helper-reset ui-state-default ui-state-active ui-corner-top"><a href="#">Database And Backup Settings</a></h3>';
        $html.='<div class="backupSettings ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" style="background-color:#ffffff;">';
        $html.='</div>';
        $html.='<h3 id="generalLabel" class="ui-accordion-header ui-helper-reset ui-state-default ui-state-active ui-corner-top"><a href="#">General Settings</a></h3>';
        $html.='<div class="generalSettings ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" style="background-color:#ffffff;">';
        $html.='</div>';
        $html.='<h3 id="emailLabel" class="ui-accordion-header ui-helper-reset ui-state-default ui-state-active ui-corner-top"><a href="#">Email Settings</a></h3>';
        $html.='<div class="emailSettings ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" style="background-color:#ffffff;">';
        $html.='</div>';
        $html.='<h3 id="smsLabel" class="ui-accordion-header ui-helper-reset ui-state-default ui-state-active ui-corner-top"><a href="#">SMS Settings</a></h3>';
        $html.='<div class="smsSettings ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" style="background-color:#ffffff;">';
        $html.='</div>';
        $html.='<h3 id="faxLabel" class="ui-accordion-header ui-helper-reset ui-state-default ui-state-active ui-corner-top"><a href="#">FAX Settings</a></h3>';
        $html.='<div class="faxSettings ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" style="background-color:#ffffff;">';
        $html.='</div>';
        $html.='<h3 id="calendarAndAppointmentLabel" class="ui-accordion-header ui-helper-reset ui-state-default ui-state-active ui-corner-top"><a href="#">Calendar And Appointment Settings</a></h3>';
        $html.='<div class="calendarAndAppointmentSettings ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" style="background-color:#ffffff;">';
        $html.='</div>';
        $html.='</div>';
        return $html;
    }

    /**
     * 		Function 		getGlobalSetting
     * 		Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 		Description		This Function is being used to return the value of global setting
     *       @params         $globalSettingType = > type of global setting (general=>1,system=>2) 
     *       @params         $globalSettingName  => Name of the global setting
     * 		@return 		global setting value.
     */
    /* function getGlobalSetting($globalSettingType,$globalSettingName) {
      $this->CI->load->model('common/manageCommonTaskModel');
      if((!empty($globalSettingType))&&(!empty($globalSettingName))){
      $globalSettingValue = $this->CI->manageCommonTaskModel->getGlobalSettings($globalSettingType,$globalSettingName);
      if (array_key_exists($globalSettingName,$globalSettingValue)){
      return $globalSettingValue[$globalSettingName];
      }else{
      return false;
      }
      } else {
      return false;
      }
      } */

    /**
     * 		Function 		arrayToCsv
     * 		Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 		Description		This Function is being used to generate the CSV file
     *       @params         $array = > array of the data write in the csv file
     *       @params         $fileName  => name of the file in which to write
     */
    function arrayToCsv($array, $fileName = "") {
        ob_start();
        $filePointer = fopen($fileName, 'w') or show_error("Can't open php://output");
        $lineCounter = 0;
        foreach ($array as $line) {
            $lineCounter++;
            if (!fputcsv($filePointer, $line)) {
                show_error("Can't write line $lineCounter: $line");
            }
        }
        fclose($filePointer) or show_error("Can't close php://output");
        $str = ob_get_contents();
        ob_end_clean();

        if ($fileName == "") {
            return $str;
        } else {
            echo $str;
        }
    }

    /**
     * 	Function		showQueries
     * 	Author			Abhishek Singh<abhishek.singh@greenapplestech.com>
     * 	Description		this function will show all the queries executed on any particular action.
     * 	@param			$formated=TRUE(Optional)
     * 	@return			Formatted SQL Queries.
     */
    function showQueries($formated = TRUE) {
		$this->CI->load->library('SQLFormatter');
        $queries = $this->CI->db->queries;
        if (count($queries) == 0) {
            $output.=$this->CI->lang->line('chLogNoQueries');
        } else {
            $formatedQueries = '';
            foreach ($queries as $key => $val) {
                $formatedQueries.=SqlFormatter::format($val, $formated) . "\n";
            }
        }
        return $formatedQueries;
    }

    /**
     * 	Function		dataDropDown
     * 	Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 	Description		this function will used to create the dropdown in the form
     * 	@param			$tableName => name of the table
     *                          $where => array for the where condition
     *                          $selectedArray=>array for the selected values in the dropdown
     * 	@return			dropdown.
     */
    function masterDataDropDown($materTypeId = 0, $selectedArray = null) {

        $this->CI->load->model('common/manageCommonTaskModel');
        $dataRequired = $this->CI->manageCommonTaskModel->getRecord(TBL_OTHER_MASTER_DATA, "*", $materTypeId, array('title'=>'ASC'), "", "", 'object', '1');
        $inital_value = 0;
        $data_str = '';
        $selected_data = array();

        if (isset($selectedArray['element'])) {
            $selected_data[] = $selectedArray['element'];
        } else if (count($selectedArray) > 0) {
            $selected_data = $selectedArray;
        }
        if ($dataRequired) {
            foreach ($dataRequired as $dataRow) {
                if ($inital_value == 0 && count($selectedArray) <= 0) {
                    $inital_value = $dataRow->id;
                }
                $dataRow->title=  ucwords(strtolower($dataRow->title));
                if (!empty($selected_data) && in_array($dataRow->id, $selected_data)) {
                    $data_str.="<option selected='selected' value='" . $dataRow->id . "'>$dataRow->title</option>";
                } else {
                    $data_str.="<option value='" . $dataRow->id . "'>$dataRow->title</option>";
                }
            }
        }
        return $dataDrop = array('data_list' => $data_str);
    }

    /**
     * 	Function		shortenText
     * 	Author			Rahul Anand<rahul.anand@greenapplestech.com>
     * 	Description		This function is being used to shorten the text
     * 	@param1			String(String on which the operation will be preformed)
     * 	@param2			Int (After how many charcter cut the string)
     * 	@param3			Boolean Force cut off
     * 	@param4			Boolean Use Ellipsis (find stuff here 									http://en.wikipedia.org/wiki/Ellipsis)
     * 	@return			String.
     */
    function shortenText($text, $chars = 40, $ForceCutoff = false, $UseEllipsis = false,$stripHtmlTag=true) {
        // Shortens text if it's longer than $chars, and adds "..." (or "&hellip;" if $UseEllipsis is true)
        // If $ForceCutoff is true, ignores word boundaries and just cuts it
		if($stripHtmlTag){
			$text = strip_tags($text); // shortening can break html tags
		}
        $dots = ($UseEllipsis) ? '&hellip;' : '';

        if (strlen($text) > $chars) {
            $text = substr($text, 0, $chars);

            if (!$ForceCutoff) {
                // Find the last position of a space or hyphen (for shortening without breaking words)
                // Maybe we should be using regex \W (non-word character) here?
                $LastSpace = strrpos($text, ' ');
                if (false === $LastSpace) {
                    $LastSpace = 0;
                }
                $LastDash = strrpos($text, '-');
                if (false === $LastDash) {
                    $LastDash = 0;
                }
				$LastUnderscore = strrpos($text, '_');
				if (false === $LastUnderscore) {
                    $LastUnderscore = 0;
                }

                $EndPos = ($LastSpace > $LastDash) ? $LastSpace : $LastDash;
                $text = substr($text, 0, $EndPos);
            }

            $text.=$dots;
        }
        return $text;
    }

    /**
     * 	Function		masterDataKeyValue
     * 	Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 	Description		this function will used to create the dropdown in the form
     * 	@param			$tableName => name of the table
     *                          $where => array for the where condition
     *                          $selectedArray=>array for the selected values in the dropdown
     * 	@return			dropdown.
     */
    function masterDataKeyValue($materTypeId = 0) {

        $this->CI->load->model('common/manageCommonTaskModel');
        $dataRequired = $this->CI->manageCommonTaskModel->getRecord(TBL_OTHER_MASTER_DATA, "*", $materTypeId, null, "", "", 'object', '1');
        $returnArray = array();
        if ($dataRequired) {
            foreach ($dataRequired as $dataRow) {
                $returnArray[$dataRow->id] = $dataRow->title;
            }
        }
        return $returnArray;
    }

    /**
     * 	Function		languageKeyValue
     * 	Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 	Description		this function will used to create the key value of aray of language
     *                          $where => array for the where condition
     *                          $selectedArray=>array for the selected values in the dropdown
     * 	@return			array([id]=>['name']).
     */
    function languageKeyValue() {

        $this->CI->load->model('common/manageCommonTaskModel');
        $dataRequired = $this->CI->manageCommonTaskModel->getAllLanguages();
        $returnArray = array();
        if ($dataRequired) {
            foreach ($dataRequired as $dataRow) {
                $returnArray[$dataRow->id] = $dataRow->lang_name;
            }
        }
        return $returnArray;
    }

    /**
     * 	Function		countryKeyValue
     * 	Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 	Description		this function will used to create the key value of aray of country
     *                          $where => array for the where condition
     *                          $selectedArray=>array for the selected values in the dropdown
     * 	@return			array([id]=>['name']).
     */
    function countryKeyValue() {

        $this->CI->load->model('common/manageCommonTaskModel');
        $dataRequired = $this->CI->manageCommonTaskModel->getAllCountry();
        $returnArray = array();
        if ($dataRequired) {
            foreach ($dataRequired as $dataRow) {
                $returnArray[$dataRow->id] = $dataRow->country_name;
            }
        }
        return $returnArray;
    }

    /**
     * 	Function		stateKeyValue
     * 	Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 	Description		this function will used to create the key value of aray of state
     *                          $where => array for the where condition
     *                          $selectedArray=>array for the selected values in the dropdown
     * 	@return			array([id]=>['name']).
     */
    function stateKeyValue() {

        $this->CI->load->model('common/manageCommonTaskModel');
        $dataRequired = $this->CI->manageCommonTaskModel->getAllState();
        $returnArray = array();
        if ($dataRequired) {
            foreach ($dataRequired as $dataRow) {
                $returnArray[$dataRow->id] = $dataRow->state_name;
            }
        }
        return $returnArray;
    }

    /**
     * 	Function		cityKeyValue
     * 	Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 	Description		this function will used to create the key value of aray of city
     *                          $where => array for the where condition
     *                          $selectedArray=>array for the selected values in the dropdown
     * 	@return			array([id]=>['name']).
     */
    function cityKeyValue() {

        $this->CI->load->model('common/manageCommonTaskModel');
        $dataRequired = $this->CI->manageCommonTaskModel->getAllCity();
        $returnArray = array();
        if ($dataRequired) {
            foreach ($dataRequired as $dataRow) {
                $returnArray[$dataRow->id] = $dataRow->city_name;
            }
        }
        return $returnArray;
    }

    /**
     * 	Function		getPatientCardNumber
     * 	Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 	Description		this function will used to get the card number of the patient
     *  @param                  $patientId
     * 	@return			$chartNumber
     */
    function getPatientChartNumber($patientId) {

        $this->CI->load->model('common/manageCommonTaskModel');
        $dataRequired = $this->CI->manageCommonTaskModel->getRecord('ch_uaprm_patient_profile_data', "chart_number", array('user_id' => $patientId));
        return $dataRequired['chart_number'];
    }

    /**
     * 	Function		generateDropDown
     * 	Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 	Description		this function will used to create the dropdown in the form and retur the key value pair of the same
	 *	ModBy			Rahul Anand<rahul.anand@greenapplestech.com>
	 *	Modification	Now you can add any field value in to title of option 
     * 	@param			$tableName => name of the table.
     *                          $key => option value field
     *                          $title=>option title field
     *                          $where => array for the where condition
     *                          $selectedArray=>array for the selected values in the dropdown
     * 	@return			dropdown.
     */
    function generateDropDown($table, $key, $value, $where = null, $selectedArray = null,$check='0',$title='',$addfield='',$orderBy = array(),$addKey='') {
        $titleToAdd='';
		$keyValueArray = array();
        $this->CI->load->model('common/manageCommonTaskModel');
        if($check=='0'){
            $dataRequired = $this->CI->manageCommonTaskModel->getRecord($table, "*", $where,$orderBy, "", "", 'array', '1');
        }
        if($check=='1'){
            $dataRequired = $this->CI->manageCommonTaskModel->getRecord($table,$value, $where,$orderBy, "", "", 'array', '1');
        }
       // print_r($orderBy);die;
        $inital_value = 0;
        $data_str = '';
        $selected_data = array();

        if (isset($selectedArray['element'])) {
            $selected_data[] = $selectedArray['element'];
        } else if (count($selectedArray) > 0) {
            $selected_data = $selectedArray;
        }
        if ($dataRequired) {
            foreach ($dataRequired as $dataRow) {
				if(!empty($title)){
					if(array_key_exists($title,$dataRow)){
						$titleToAdd= $dataRow[$title];
					}
				}
				$extraString ='';
				if($table==TBL_PHARMACY_MASTER || $table=='ch_cinfs_pharmacy_master'){
					
					if(empty($title)){
						$titleToAdd='Codes:
									(E)		: Accepts electronic prescriptions
									(C)		: Accepts EPCS prescriptions    	
									24		: Is open 24 hours     	 
									(MO)	: Mail order    	
									(Elig)	: Requires eligibility 	 
									(R)		: Retail 	
									(LTC)	: Long Term Care 	
									(SP)	: Specialty';	
					}
					if(!empty($dataRow['is24hour']) && $dataRow['is24hour']=='y'){
						$extraString .=' [24]';
					}
					if(!empty($dataRow['level3']) && $dataRow['level3']=='y'){
						
					}
					if(!empty($dataRow['electronic']) && $dataRow['electronic']=='y'){
						$extraString .=' (E)';
					}
					if(!empty($dataRow['MailOrder']) && $dataRow['MailOrder']=='y'){
						$extraString .=' (MO)';
					}
					if(!empty($dataRow['RequiresEligibility']) && $dataRow['RequiresEligibility']=='y'){
						$extraString .=' (Elig)';
					}
					if(!empty($dataRow['Retail']) && $dataRow['Retail']=='y'){
						$extraString .=' (R)';
					}
					if(!empty($dataRow['LongTermCare']) && $dataRow['LongTermCare']=='y'){
						$extraString .=' (LTC)';
					}
					if(!empty($dataRow['Specialty']) && $dataRow['Specialty']=='y'){
						$extraString .=' (SP)';
					}
					if(!empty($dataRow['CanReceiveControlledSubstance']) && $dataRow['CanReceiveControlledSubstance']=='y'){
						$extraString .=' (C)';
					}
					 
				}
                if(!empty($addKey)){
                    $dataRow[$key]=$addKey."~".$dataRow[$key];
                }
                $keyValueArray[$dataRow[$key]] = trim($dataRow[$value]." ".$extraString);
                if(!empty($addfield)){
                    if(empty($dataRow[$value])){
                        $dataRow[$value]=$dataRow[$addfield];
                    }elseif(empty($dataRow[$addfield])){
                        $dataRow[$value]=ucwords(strtolower(trim($dataRow[$value])));
                    }else{
                        $dataRow[$value]=$dataRow[$addfield].' - '.  ucwords(strtolower(trim($dataRow[$value])));
                    }
					
				}
                if ($inital_value == 0 && count($selectedArray) <= 0) {
                    $inital_value = $dataRow[$key];
                }
                if (!empty($selected_data) && in_array($dataRow[$key], $selected_data)) {
                    $data_str.="<option title='".$titleToAdd."' selected='selected' value='" . $dataRow[$key] . "'>" . ucwords(strtolower(trim($this->shortenText($dataRow[$value], 40, false, true)." ".$extraString))) . "</option>";
                } else {
                    $data_str.="<option title='".$titleToAdd."' value='" . $dataRow[$key] . "'>" . ucwords(strtolower((trim($this->shortenText($dataRow[$value], 40, false, true))." ".$extraString))) . "</option>";
                }
            }
        }
        return $dataDrop = array('data_list' => $data_str, 'key_value' => $keyValueArray);
    }

	
	
	
	 /**
     * 	Function		generateFacilityDropDown
     * 	Author			Rahul Anand<rahul.anand@greenapplestech.com> :)
     * 	Description		this function will used to create the dropdown in the form and retur the key value pair of the same
     * 	@param			
							 $selectedArray=>array for the selected values in the dropdown
								$tableName => name of the table.
     *                          $key => option value field
     *                          $title=>option title field
     *                          $where => array for the where condition
     *                         $facilityTypeArr= This Array will have the types of all OPT Groups 
     * 	@return			dropdownString.
     */
	 
	 
    function generateFacilityGroupDropDown($selectedArray = null,$table=TBL_FACILITY_MASTER, $key='id', $value='facility_name', $where = null, $facilityTypeArr=array('1' => 'Hospital',
																																								'2'=>'Lab',
																																								'4'=>'Other')) {
        $keyValueArray = array();
        $this->CI->load->model('common/manageCommonTaskModel');
        $dataRequired = $this->CI->manageCommonTaskModel->getRecord($table, "*", $where, null, "", "", 'array', '1');
        $inital_value = 0;
        $data_str = '';
        $selected_data = array();
		
        if (isset($selectedArray['element'])) {
            $selected_data[] = $selectedArray['element'];
        } else if (count($selectedArray) > 0) {
            $selected_data = $selectedArray;
        }
		foreach($facilityTypeArr as $singleFacilityVar)
		{
			$$singleFacilityVar='';
		}
        if ($dataRequired) {
            foreach ($dataRequired as $dataRow) {
                $keyValueArray[$dataRow[$key]] = $dataRow[$value];
                if ($inital_value == 0 && count($selectedArray) <= 0) {
                    $inital_value = $dataRow[$key];
                }
                if (!empty($selected_data) && in_array($dataRow[$key], $selected_data)) {
                    $data_str="<option selected='selected' value='" . $dataRow[$key] . "'>" . $this->shortenText($dataRow[$value], 50, false, true) . "</option>";
                } else {
                    $data_str="<option value='" . $dataRow[$key] . "'>" . $this->shortenText($dataRow[$value], 50, false, true) . "</option>";
                }
			
			$facilityType=$dataRow['facility_type'];
			$facilityTypeStrVar=$facilityTypeArr[$facilityType];
			
			if(isset($$facilityTypeStrVar) && strpos($$facilityTypeStrVar,"optgroup label")===false){
				$$facilityTypeStrVar.='<optgroup label="'.$facilityTypeArr[$facilityType].'">';
			}
			
			$$facilityTypeStrVar.=$data_str;
			
            }
        }
		$combineStr='';
		foreach($facilityTypeArr as $singleFacilityVar)
		{
			$$singleFacilityVar.='</optgroup>';
		}
		foreach($facilityTypeArr as $singleFacilityVar)
		{
			$combineStr.=$$singleFacilityVar;
		}
		
	   return $combineStr;
    }
	
    function getFacilityName($facilityId) {
        $facilityName = "N/A";
		$where = "id = '" . $facilityId . "'";
        $this->CI->load->model('common/manageCommonTaskModel');
        $dataRequired = $this->CI->manageCommonTaskModel->getRecord(TBL_FACILITY_MASTER, "*", $where, null, "", "", 'array', '1');
		
        if ($dataRequired) 
		{
            foreach ($dataRequired as $dataRow) 
			{
				$facilityName = $dataRow["facility_name"];
			}
		}
		
	   return $facilityName;
    }
	
	function getEmployerName($id) {
        $employerName = "N/A";
		$where = "id = '" . $id . "'";
        $this->CI->load->model('common/manageCommonTaskModel');
        $dataRequired = $this->CI->manageCommonTaskModel->getRecord(TBL_EMPLOYER_MASTER, "*", $where, null, "", "", 'array', '1');
		
        if ($dataRequired) 
		{
            foreach ($dataRequired as $dataRow) 
			{
				$employerName = $dataRow["employer_name"];
			}
		}
		
	   return $employerName;
    }
	
	function getPharmacyName($id) {
        $pharmacyName = "N/A";
		$where = "id = '" . $id . "'";
        $this->CI->load->model('common/manageCommonTaskModel');
        $dataRequired = $this->CI->manageCommonTaskModel->getRecord(TBL_PHARMACY_MASTER, "*", $where, null, "", "", 'array', '1');
		
        if ($dataRequired) 
		{
            foreach ($dataRequired as $dataRow) 
			{
				$pharmacyName = $dataRow["pharmacy_name"];
			}
		}
		
	   return $pharmacyName;
    }
	
	function getInsuranceName($id) {
        $insuranceName = "N/A";
		$where = "id = '" . $id . "'";
        $this->CI->load->model('common/manageCommonTaskModel');
        $dataRequired = $this->CI->manageCommonTaskModel->getRecord(TBL_INSURANCE_MASTER, "*", $where, null, "", "", 'array', '1');
		
        if ($dataRequired) 
		{
            foreach ($dataRequired as $dataRow) 
			{
				$insuranceName = $dataRow["insurance_company_name"];
			}
		}
		
	   return $insuranceName;
    }
	
	function getfeferringDocName($id) {
        $referringName = "N/A";
		$where = "id = '" . $id . "'";
        $this->CI->load->model('common/manageCommonTaskModel');
        $dataRequired = $this->CI->manageCommonTaskModel->getRecord('ch_cinfs_referring_doctor', "*", $where, null, "", "", 'array', '1');
		
        if ($dataRequired) 
		{
            foreach ($dataRequired as $dataRow) 
			{
				$referringName = $dataRow["doctor_first_name"]." ".$dataRow["doctor_last_name"];
			}
		}
		
	   return $referringName;
    }
	
    /**
     * 	Function		getFacilityKeyValueArray
     * 	Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 	Description		this function will used to create the key value array of the facility
     * 	@param			$tableName => name of the table.
     *                          $facilityTypeId => facility type id
     *                          $procedureId=>Procedure Id
     * 	@return                 key value array
     */
    function getFacilityKeyValueArray($facilityTypeId, $procedureId = "") {
        $dataRequired = $this->CI->manageLabProceduresModel->generateFacilityKeyValueMap($facilityTypeId, $procedureId);
        return $dataRequired;
    }

    /**
     * 	Function		generateFullFacilityDropdown
     * 	Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 	Description		this function will used to create the dropdown of the facility
     * 	@param			none
     * 	@return                 dropdown
     */
    function generateFullFacilityDropdown() {
        $facilityGroup = $this->getFacilityKeyValueArray('0');
        $labGroup = $this->getFacilityKeyValueArray('1');
        $hospitalGroup = $this->getFacilityKeyValueArray('2');
        $optionStr = '';
        $optionStr.='<optgroup label="Facility">';
        foreach ($facilityGroup as $key => $value) {
            $optionStr.='<option value="0:' . $key . '">' . $value . '</option>';
        }
        $optionStr.='</optgroup><optgroup label="Laboratory">';
        foreach ($labGroup as $key => $value) {
            $optionStr.='<option value="1:' . $key . '">' . $value . '</option>';
        }
        $optionStr.='</optgroup><optgroup label="Hospital">';
        foreach ($hospitalGroup as $key => $value) {
            $optionStr.='<option value="2:' . $key . '">' . $value . '</option>';
        }
        $optionStr.='</optgroup>';
        return $optionStr;
    }

    /**
     * 	Function		getDiseaseKeyValuEArray
     * 	Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 	Description		this function will used to create the key value array of the diagnoisis master table data
     * 	@param			none
     * 	@return                 key value array
     */
    function getDiseaseKeyValuEArray() {
        $dataRequired = $this->CI->manageCommonTaskModel->getDiseasesList();
        $diseaseArray = array();
        if ($dataRequired) {
            foreach ($dataRequired as $data) {
                $diseaseArray[$data->id] = $data->diseases_name;
            }
            return $diseaseArray;
        } else {
            return false;
        }
    }

    /**
     * 	Function		getBaseDiseaseKeyValuEArray
     * 	Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 	Description		this function will used to create the key value array of the diseases master table data
     * 	@param			none
     * 	@return                 key value array
     */
    function getBaseDiseaseKeyValuEArray() {
        $dataRequired = $this->CI->manageCommonTaskModel->getDiseaseList();
        $diseaseArray = array();
        if (!empty($dataRequired)) {
            foreach ($dataRequired as $data) {
                $diseaseArray[$data->id] = $data->disease_name;
            }
            return $diseaseArray;
        } else {
            return false;
        }
    }

    /**
     * 	Function		
     * 	Author			Rahul Anand<rahul.anand@greenapplestech.com>
     * 	Description		This function will be used to get the next appointment status according to current status
     * 	
     */
    function getshownStatusAccordingCurrentStatus($currentStatus,$create_visit_y_n='0') {
        $this->CI->load->model('common/manageCommonTaskModel');
        $nextStatus = $currentStatus;
        $lastStatus = false;
        switch (trim($currentStatus)) {
			case 4:
                $nextStatus = 11;
                break;
            case 11:
                $nextStatus = 5;
                break;
            case 5:
				if($create_visit_y_n==0){
					$nextStatus = 6;
				}else{
					$nextStatus = 16;
				}
                break;
            case 6:
                $nextStatus = 7;
                break;
            case 7:
                $nextStatus = 9;
                break;
            case 9:
                $nextStatus = 8;
                break;
			case 14:
                $nextStatus = 12;
                break;
			case 12:
                $nextStatus = 11;
                break;
			case 13:
                $nextStatus = 13;
                break;
			case 16:
                $nextStatus = 9;
                break;
            case 8:
                $lastStatus = true;
                break;
        }
        //Now get the status TExt from dbase
        if ($lastStatus) {
            return false;
        } else {
            $nextStatusText = $this->CI->manageCommonTaskModel->getStatusText($nextStatus);
            return array('status_id' => $nextStatus,
                'statusText' => $nextStatusText);
        }
    }

    /**
     * 	Function		getTextOfStatus
     * 	Author			Rahul Anand<rahul.anand@greenapplestech.com>
     * 	Description		This function will be used to get the next appointment status 				accroding to current status
     * 	
     */
    function getTextOfStatus($status = '') {
        return $this->CI->manageCommonTaskModel->getStatusText($status);
    }

    /**
     * 	Function		getshownPreviousStatusAccordingCurrentStatus
     * 	Author			Rahul Anand<rahul.anand@greenapplestech.com>
     * 	Description		This function will be used to get the previous appointment status 				accroding to current status
     * 	
     */
    function getshownPreviousStatusAccordingCurrentStatus($currentStatus) {
        $this->CI->load->model('common/manageCommonTaskModel');
        $preStatus = '';
        $firstStatus = false;
        switch (trim($currentStatus)) {
            case 6:
                $preStatus = 5;
                break;
            case 7:
                $preStatus = 6;
                break;
            case 9:
                $preStatus = 7;
                break;
            case 8:
                $preStatus = 9;
                break;
			case 5:
                $preStatus = 11;
				break;
            case 11:
                $preStatus = 4;
                break;
			case 13:
                $preStatus = 14;
                break;
			case 12:
                $preStatus = 14;
                break;
			case 16:
                $preStatus = 5;
                break;
            case 4:
                $firstStatus = true;
                break;
        }
        //Now get the status TExt from dbase
        if ($firstStatus) {
            return false;
        } else {
            $preStatusText = $this->CI->manageCommonTaskModel->getStatusText($preStatus);
            return array('status_id' => $preStatus,
                'statusText' => $preStatusText);
        }
    }

    /**
     * 	Function		convertInchToCm
     * 	Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 	Description		This function will be used to convert the inches in to the centimeter
     * 	
     */
    function convertInchToCm($inches) {
        return ($inches * 2.54);
    }

    /**
     * 	Function		getMasterDataName
     * 	Author			Prashant Jain
     * 	Description		This function will be used to get the title of the master data
     * 	
     */
    function getMasterDataName($id = '') {
        $title = $this->CI->manageCommonTaskModel->getMasterDataName($id);
        return $title;
    }
	
	function getMasterDataDescription($id = '') {
        $disc = $this->CI->manageCommonTaskModel->getMasterDataDescription($id);
        return $disc;
    }

    /**
     * 	Function		getDrugName
     * 	Author			Prashant Jain
     * 	Description		This function will be used to get the name of the Drug from
     * 					the Drug Master Table data
     * 	
     */
    function getDrugName($id = '') {
        $drugName = $this->CI->manageCommonTaskModel->getDrugName($id);
        return $drugName;
    }

    /**
     * 	Function		distinctDrugTypeList
     * 	Author			Prashant Jain
     * 	Description		This function will be create a list for distinct Drug Type
     * 	
     */
    function distinctDrugTypeList() {
        $getDrugTypeList = $this->CI->manageCommonTaskModel->getDistinctDrugTypeName();
        return $getDrugTypeList;
    }
    
     /**
     * 	Function		patientHeaderInformation
     * 	Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 	Description		This function will be used to get the header infor of every patient on data widgets
     * 	
     */
    function patientHeaderInformation($patientId = "") {
        // these new fields are added due to guardian address and contact info required in patient report.
        //res_party_address1,res_party_address2,res_party_city,res_party_state,res_party_zip,res_party_home_phone,res_party_work_phone
        $sessUserType=$this->CI->session->userdata('userMainType');
        if (!empty($sessUserType))
			$this->CI->load->model('patientProfile/' . $this->CI->session->userdata('userMainType') . '/managePatientProfileModel');
		else
			$this->CI->load->model('patientProfile/staff/managePatientProfileModel');
        if (!empty($patientId)) {
            			$fildListArr=array('"first_name"',
								'"middle_name"',
								'"last_name"',
								'"chart_number"',
								'"date_of_birth"',
								'"ssn"',
								'"gender"',
								'"user_id"',
								'"fax"',
								'"email"',
								'"secure_email"',
								'"stats_race"',
								'"stats_ethincity"',
								'"stats_place_birth"',
								'"marital_status"',
								'"home_phone"',
								'"guardian_first_name"',
								'"guardian_last_name"',
								'"emergency_first_name"',
								'"emergency_last_name"',
								'"emergency_telephone"',
								'"emergency_relationship"',
								'"guardian_contact"',
								'"fax"',
								'"address"',
								'"correspondence_address"',
								'"zip"',
								'"city"',
								'"state"',
								'"country"',
								'"res_party_address1"',
								'"res_party_address2"',
								'"res_party_city"',
								'"res_party_state"',
								'"res_party_zip"',
								'"res_party_home_phone"',
								'"res_party_work_phone"',
								'"stats_language"',
								'"npi"',
								'"patient_title"',
								'"patient_salutation"',
								'"patient_religion"',
								'"blood_type"',
								'"blood_rh"',
								'"taxonomy_code"',
								'"photograph"');
			// get User type by id
			$userTypeId =$this->getUserTypeIdByUserId($patientId);
			if($userTypeId=='7'){
				$basicInforArray =$this->getPatientDatafromSepTbl($patientId,'4');
			}else{
			
				$getUserBasicInfo = $this->CI->managePatientProfileModel->getViewUserProfileFieldByGroup($patientId, $userTypeId, '',$fildListArr);
				$basicInforArray = array();
				if(is_array($getUserBasicInfo) && count($getUserBasicInfo)>0)
				{	
					foreach ($getUserBasicInfo as $info) {
						$basicInforArray[$info['field_name']] = $info['field_value'];
					}
				}				
			}
			return $basicInforArray;
        } else {
            return false;
        }
    }

    function computeAge($dateOfBirth) {

        /* $ageInDays=abs((time() - strtotime($dateOfBirth)));
          $years = floor($ageInDays / (365*60*60*24));
          $months = floor(($ageInDays - $years * 365*60*60*24) / (30*60*60*24));
          $days = floor(($ageInDays - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
          //Let'c combine all name
          $patientAge="";
          if($years>0 && $years!=''){
          $patientAge=$years." Year(s)";
          }
          if($patientAge=='' && $months>0 ){
          $patientAge=$months." Month(s) ";
          }if($patientAge==''){
          $patientAge=$days." day(s)";
          }
          return $patientAge; */

		if(($dateOfBirth=="0000-00-00")||(empty($dateOfBirth))){
			return '';
		}
//        $dateArray = explode('-', $dateOfBirth);
//        $ageTime = mktime(0, 0, 0, $dateArray[1], $dateArray[2], $dateArray[0]); // Get the person's birthday timestamp
//        $t = time(); // Store current time for consistency
//        $age = ($ageTime < 0) ? ( $t + ($ageTime * -1) ) : $t - $ageTime;
//        $year = 60 * 60 * 24 * 365;
//        $ageYears = $age / $year;
//
//        return floor($ageYears);
                
        $datetime1 = new DateTime($dateOfBirth);
        $datetime2 = new DateTime();
        $interval = $datetime1->diff($datetime2);
        return $interval->format('%y');      
    }

    /**
     * 	Function		getMasterDataNameForId
     * 	Author			Prashant Jain
     * 	Description		This function will return the Title for the passed id from the other Master Data
     * 	parm			$id=Id of the particular item of which name we want.
     */
    function getMasterDataNameForId($id = '') {
        if(!empty($id)){
            $masterIdTitle = $this->CI->manageCommonTaskModel->getMasterDataName($id);
            return $masterIdTitle;
        }else{
            return false;
        }
    }

    /**
     * 	Function		getChUserFieldValue
     * 	Author			Rahul Anand<rahul.anand@greenapplstech.com>
     * 	Description		This function will get the rcopi_userName
     * 	parm			$id=Id of the particular item of which name we want.
     */
    function getRcopiaUserName($id = '') {
        $record = $this->CI->manageCommonTaskModel->getRcopiaUserName($id);
        if ($record) {
            return $record->rcopia_user_name;
        } else {
            return false;
        }
    }

    /**
     * 	Function		getUserSpecifiedField
     * 	Author			Rahul Anand<rahul.anand@greenapplstech.com>
     * 	Description		This function will get the User specified field value
     * 	parm			$id=Id of the particular item of which name we want.
     */
    function getUserSpecifiedField($userId = 0, $userSuppliedArray = array('first_name', 'last_name'),$userType=7) {
        if (empty($userId))
            return false;
        $allResult = $this->CI->manageCommonTaskModel->getProfileField($userId, $userSuppliedArray,$userType);
        return $allResult;
    }

    /**
     * 	Function		checkDrugType
     * 	Author			Prashant Jain
     * 	Description		This function will check whether a drug type is OTC or not.
     * 	parm			$drugId=Id of the Drug.
     */
    function checkDrugType($drugId = '') {
        $otcFlag = '';
        $checkOTC = $this->CI->managePatientMedicationHistoryModel->checkOtcOrOther($drugId);
        if (!empty($checkOTC) && $checkOTC === 'HUMAN OTC DRUG') {
            $otcFlag = 2; //if Otc
        } else {
            $otcFlag = 1; //if not OTC
        }
        return $otcFlag;
    }

    /**
     * 	Function		edrugRouteMasterDropDown
     * 	Author			Prashant Jain
     * 	Description		This function will create a dropdown for route master of edrug
     * 	parm			$selectedId:The selected option if we want to set
     * 	return			The dropdown List of option
     */
    function edrugRouteMasterDropDown($selectedId = '') {
        $createDropDown = '';
        $routeList = $this->CI->manageCommonTaskModel->getEdrugRouteList();
        if ($routeList) {
            foreach ($routeList as $item) {
                if (!empty($selectedId) && $selectedId == $item->id) {
                    $createDropDown.="<option value='" . $item->id . "' selected>" . $item->route . "</option>";
                } else {
                    $createDropDown.="<option value='" . $item->id . "'>" . $item->route . "</option>";
                }
            }
        }
        return $createDropDown;
    }

    /**
     * 	Function		edrugFormMasterDropDown
     * 	Author			Prashant Jain
     * 	Description		This function will create a dropdown for Form master of edrug
     * 	parm			$selectedId:The selected option if we want to set
     * 	return			The dropdown List of option
     */
    function edrugFormMasterDropDown($selectedId = '') {
        $createDropDown = '';
        $formList = $this->CI->manageCommonTaskModel->getEdrugFormList();
        if ($formList) {
            foreach ($formList as $item) {
                if (!empty($selectedId) && $selectedId == $item->id) {
                    $createDropDown.="<option value='" . $item->id . "' selected>" . $item->form . "</option>";
                } else {
                    $createDropDown.="<option value='" . $item->id . "'>" . $item->form . "</option>";
                }
            }
        }
        return $createDropDown;
    }

    /**
     * 	Function		edrugCsMasterDropDown
     * 	Author			Prashant Jain
     * 	Description		This function will create a dropdown for Control substance master of edrug
     * 	parm			$selectedId:The selected option if we want to set
     * 	return			The dropdown List of option
     */
    function edrugCsMasterDropDown($selectedId = '') {
        $createDropDown = '';
        $csList = $this->CI->manageCommonTaskModel->getEdrugCsList();
        if ($csList) {
            foreach ($csList as $item) {
                if (!empty($selectedId) && $selectedId == $item->id) {
                    $createDropDown.="<option value='" . $item->id . "' selected>" . $item->control_sub . "</option>";
                } else {
                    $createDropDown.="<option value='" . $item->id . "'>" . $item->control_sub . "</option>";
                }
            }
        }
        return $createDropDown;
    }

    /**
     * 	Function		getSpecificFieldFromAnyTable
     * 	Author			Prashant Jain
     * 	Description		This function will fetch the particular field value from the passed information
     * 	parm			
     * 	return			
     */
    function getSpecificFieldFromAnyTable($table, $field, $id) {
        $getFieldValue = $this->CI->manageCommonTaskModel->getRecord($table, $field, array('id' => $id));
        return $getFieldValue[$field];
    }

    /**
     * 	Function		getChildListItemOfRosCategory
     * 	Author			Prashant Jain
     * 	Description		This function will fetch the list of all child of the particular Category Id
     * 	parm			$catId
     * 	return			
     */
    function getChildListItemOfRosCategory($catId) {
        $getChildList = $this->CI->managePatientROSHistoryModel->getChieldItemList($catId);
        return $getChildList;
    }

    /**
     * 	Function		checkExistencyOfRosForPatient
     * 	Author			Prashant Jain
     * 	Description		This function will check whether the ROS id Exist for the patient or not;
     * 	parm			$patientId,$rosId
     * 	return			
     */
    function checkExistencyOfRosForPatient($patientId, $rosId,$visitId) {
        $checkExistency = $this->CI->managePatientROSHistoryModel->checkROSValueExistForPatient($patientId, $rosId,$visitId);
        return $checkExistency;
    }

    /**
     * 	Function		checkRosHaveSubCat
     * 	Author			Prashant Jain
     * 	Description		This function will check whether the ROS id have any sub category or not;
     * 	parm			$rosId
     * 	return			
     */
    function checkRosHaveSubCat($rosId) {
        $checkExistency = $this->CI->managePatientROSHistoryModel->checkHaveSubCat($rosId);
        return $checkExistency;
    }

    /**
     * 	Function		getListOfSubItem
     * 	Author			Prashant Jain
     * 	Description		This function will check whether the ROS Item have any sub Item or not;
     * 	parm			$rosChildId
     * 	return			
     */
    function getListOfSubItem($rosChildId) {
        $checkExistency = $this->CI->managePatientROSHistoryModel->checkHaveSubItem($rosChildId);
        return $checkExistency;
    }

    /**
     * 	Function		staffDropDown
     * 	Author			Prashant Jain
     * 	Description		This function will create a dropdown of the all staff list
     * 	parm			$selectedId:The selected option if we want to set
	 *					$userType :like (doctor,nurse,..)
	 *					$dataFormFlag :What we want only data or dropdown.data-1,dropdown-0;
     * 	return			The dropdown List of option
     */
    function staffDropDown($selectedId = '',$userType="",$dataFormFlag='0') {
        $createDropDown = '';
        $doctorList = '';
        $nurseList = '';
        $frontDeskList = '';
        $adminList = '';
        $labTechList = '';
        $billingDeskList = '';
        $perAssList = '';
        $midWivesList = '';
        $staffList = $this->CI->manageCommonTaskModel->getAllStaffList($userType);
		$dataArr=array();
		if($dataFormFlag=='1'){
			if ($staffList) {
				foreach ($staffList as $item) {
					$dataArr[$item->user_id]=str_replace(","," ",$item->Name);
				}
			}
			return $dataArr;die;
		}
		if((!empty($userType))&&(!is_array($userType))){
            if ($staffList) {
				foreach ($staffList as $item) {
					if (!empty($selectedId) && $selectedId == $item->user_id) {
					$createDropDown.="<option value='" . $item->user_id . "' selected>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
					} else {
						$createDropDown.="<option value='" . $item->user_id . "'>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
					}
				}
			}
        }elseif((!empty($userType))&&(is_array($userType))){ 
                if ($staffList) {
                    foreach ($staffList as $item) {
                        if ($item->user_type == '1') {
                            if (!empty($selectedId) && $selectedId == $item->user_id) {
                                $doctorList.="<option value='" . $item->user_id . "' selected>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                            } else {
                                $doctorList.="<option value='" . $item->user_id . "'>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                            }
                        } elseif ($item->user_type == '2') {
                            if (!empty($selectedId) && $selectedId == $item->user_id) {
                                $nurseList.="<option value='" . $item->user_id . "' selected>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                            } else {
                                $nurseList.="<option value='" . $item->user_id . "'>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                            }
                        } elseif ($item->user_type == '3') {
                            if (!empty($selectedId) && $selectedId == $item->user_id) {
                                $frontDeskList.="<option value='" . $item->user_id . "' selected>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                            } else {
                                $frontDeskList.="<option value='" . $item->user_id . "'>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                            }
                        } elseif ($item->user_type == '4') {
                            if (!empty($selectedId) && $selectedId == $item->user_id) {
                                $adminList.="<option value='" . $item->user_id . "' selected>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                            } else {
                                $adminList.="<option value='" . $item->user_id . "'>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                            }
                        } elseif ($item->user_type == '5') {
                            if (!empty($selectedId) && $selectedId == $item->user_id) {
                                $labTechList.="<option value='" . $item->user_id . "' selected>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                            } else {
                                $labTechList.="<option value='" . $item->user_id . "'>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                            }
                        } elseif ($item->user_type == '6') {
                            if (!empty($selectedId) && $selectedId == $item->user_id) {
                                $billingDeskList.="<option value='" . $item->user_id . "' selected>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                            } else {
                                $billingDeskList.="<option value='" . $item->user_id . "'>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                            }
                        } elseif ($item->user_type == '8') {
                            if (!empty($selectedId) && $selectedId == $item->user_id) {
                                $perAssList.="<option value='" . $item->user_id . "' selected>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                            } else {
                                $perAssList.="<option value='" . $item->user_id . "'>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                            }
                        } elseif ($item->user_type == '9') {
                            if (!empty($selectedId) && $selectedId == $item->user_id) {
                                $midWivesList.="<option value='" . $item->user_id . "' selected>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                            } else {
                                $midWivesList.="<option value='" . $item->user_id . "'>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                            }
                        }
                    }
                    $createDropDown.="<optgroup label='Doctor'>
                    <b>" . $doctorList . "</b>
                    <optgroup label='Nurse'>
                    <b>" . $nurseList . "</b>
                    <optgroup label='Front Desk'>
                    <b>" . $frontDeskList . "</b>
                    <optgroup label='Administrator'>
                    <b>" . $adminList . "</b>
                    <optgroup label='Lab Technitians'>
                    <b>" . $labTechList . "</b>
                    <optgroup label='Billing Desk'>
                    <b>" . $billingDeskList . "</b>
                    <optgroup label='Personal Assistant'>
                    <b>" . $perAssList . "</b>
                    <optgroup label='Mid Wives'>
                    <b>" . $midWivesList . "</b>";
            }
        }else {
        if ($staffList) {
            foreach ($staffList as $item) {
                if ($item->user_type == '1') {
                    if (!empty($selectedId) && $selectedId == $item->user_id) {
                        $doctorList.="<option value='" . $item->user_id . "' selected>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                    } else {
                        $doctorList.="<option value='" . $item->user_id . "'>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                    }
                } elseif ($item->user_type == '2') {
                    if (!empty($selectedId) && $selectedId == $item->user_id) {
                        $nurseList.="<option value='" . $item->user_id . "' selected>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                    } else {
                        $nurseList.="<option value='" . $item->user_id . "'>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                    }
                } elseif ($item->user_type == '3') {
                    if (!empty($selectedId) && $selectedId == $item->user_id) {
                        $frontDeskList.="<option value='" . $item->user_id . "' selected>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                    } else {
                        $frontDeskList.="<option value='" . $item->user_id . "'>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                    }
                } elseif ($item->user_type == '4') {
                    if (!empty($selectedId) && $selectedId == $item->user_id) {
                        $adminList.="<option value='" . $item->user_id . "' selected>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                    } else {
                        $adminList.="<option value='" . $item->user_id . "'>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                    }
                } elseif ($item->user_type == '5') {
                    if (!empty($selectedId) && $selectedId == $item->user_id) {
                        $labTechList.="<option value='" . $item->user_id . "' selected>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                    } else {
                        $labTechList.="<option value='" . $item->user_id . "'>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                    }
                } elseif ($item->user_type == '6') {
                    if (!empty($selectedId) && $selectedId == $item->user_id) {
                        $billingDeskList.="<option value='" . $item->user_id . "' selected>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                    } else {
                        $billingDeskList.="<option value='" . $item->user_id . "'>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                    }
                } elseif ($item->user_type == '8') {
                    if (!empty($selectedId) && $selectedId == $item->user_id) {
                        $perAssList.="<option value='" . $item->user_id . "' selected>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                    } else {
                        $perAssList.="<option value='" . $item->user_id . "'>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                    }
                } elseif ($item->user_type == '9') {
                    if (!empty($selectedId) && $selectedId == $item->user_id) {
                        $midWivesList.="<option value='" . $item->user_id . "' selected>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                    } else {
                        $midWivesList.="<option value='" . $item->user_id . "'>" . ucwords(str_replace(","," ",$item->Name)) . "</option>";
                    }
                }
            }
            $createDropDown.="<optgroup label='Doctor'>
								<b>" . $doctorList . "</b>
								<optgroup label='Clinical'>
								<b>" . $nurseList . "</b>
								<optgroup label='Front Desk'>
								<b>" . $frontDeskList . "</b>
								<optgroup label='Administrator'>
								<b>" . $adminList . "</b>
								<optgroup label='Lab Technicians'>
								<b>" . $labTechList . "</b>
								<optgroup label='Billing Desk'>
								<b>" . $billingDeskList . "</b>
								<optgroup label='Personal Assistant'>
								<b>" . $perAssList . "</b>
								<optgroup label='Mid Wives'>
								<b>" . $midWivesList . "</b>";
        }
        }
        return $createDropDown;
    }

    function getStaffName($id = '') {
        $staffList = $this->CI->manageCommonTaskModel->getAllStaffList();
        if ($staffList) {
            foreach ($staffList as $item) {
                if ($item->user_id == $id) {
                    return str_replace(","," ",$item->Name);
                    die;
                }
            }
        }
    }
    
    
    function getUserProfileInformation($userId = "",$userType="",$groupId="",$selectedFields=null) {
        
        $this->CI->load->model('patientProfile/' . $this->CI->session->userdata('userMainType') . '/managePatientProfileModel');
        if (!empty($userId)) {
			if($userType=='7'){
				$basicInforArray=$this->getPatientDatafromSepTbl($userId,'4');
				return $basicInforArray;
			}else{
				$getUserBasicInfo = $this->CI->managePatientProfileModel->getViewUserProfileFieldByGroup($userId,$userType,$groupId,null);
				if(!empty($selectedFields)){
					$getUserBasicInfo = $this->CI->managePatientProfileModel->getViewUserProfileFieldByGroup($userId,$userType,'',$selectedFields);
				}
				$basicInforArray = array();
				foreach ($getUserBasicInfo as $info) {
					$basicInforArray[$info['field_name']] = $info['field_value'];
				}
				
				return $basicInforArray;
			}
        } else {
            return false;
        }
    }
	
	function patientSpecificVisitDateDropDown($patientId='',$selectedId='')
	{
		$visitDates=$this->CI->manageCommonTaskModel->getPatientVisitDates($patientId,'0');
		$createVisitDropDown='';
		if(!empty($visitDates)){
			foreach ($visitDates as $key => $visit) {
				if(!empty($selectedId) && $key==$selectedId){
					$createVisitDropDown.="<option value='".$key."' selected>".$this->showDateForSpecificTimeZone($visit)."</option>";
				}else{
					$createVisitDropDown.="<option value='".$key."'>".$this->showDateForSpecificTimeZone($visit)."</option>";
				}
			}
			return $createVisitDropDown;
		}else{
			return false;
		}
	}
    
    /**
     * 	Function		getKeyValue
     * 	Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 	Description		this function will used to get the value of the id
     * 	@param			$tableName => name of the table.
     *                          $id => unique value
     *                          $value=>field name whiich need to extract
     *                          
     * 	@return			dropdown.
     */
    function getKeyValue($table, $id, $value) {
        $this->CI->load->model('common/manageCommonTaskModel');
        $dataRequired = $this->CI->manageCommonTaskModel->getRecord($table,$value,array('id'=>$id));
        if((isset($dataRequired[$value]))&&(!empty($dataRequired[$value]))){
            return $dataRequired;
        } else {
            return false;
        }
    }

	
	 /**
     * 	Function		getReportSetting
     * 	Author			Rahul Anand<rahul.anand@greenapplestech.com>
     * 	Description		This function will be used to get the report setting from dbase
	 *
	 */
	 function getReportSetting($userId=0)
	 {
		if(empty($userId)){
			return false; //No need to do anything here
		}
		$this->CI->load->model('common/manageCommonTaskModel');
		return $dataRequired = $this->CI->manageCommonTaskModel->getRecord(TBL_PROVIDER_HEADER_FOOTER_SETTINGS,"*",array('provider_id'=>$userId),array(),"","","object",0);
	 }

    
    /**
     * 	Function		getRandomColorHex
     * 	Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 	Description		this function will used to generate the random color code
     * 	@param			$max_r = 255, $max_g = 255, $max_b = 255
     *                        
     *                          
     * 	@return			hex color code.
     */
    function getRandomColorHex($max_r = 255, $max_g = 255, $max_b = 255)
    {
        // ensure that values are in the range between 0 and 255
        $max_r = max(0, min($max_r, 255));
        $max_g = max(0, min($max_g, 255));
        $max_b = max(0, min($max_b, 255));
       
        // generate and return the random color
        return '#'.str_pad(dechex(rand(0, $max_r)), 2, '0', STR_PAD_LEFT) .
               str_pad(dechex(rand(0, $max_g)), 2, '0', STR_PAD_LEFT) .
               str_pad(dechex(rand(0, $max_b)), 2, '0', STR_PAD_LEFT);
    }
	
	/**
     * 	Function		getDiagnosisNameUsingDiagIdList
     * 	Author			Rahul Anand<rahul.anand@greenapplestech.coM>
     
     */
	function getDiagnosisCodeUsingDiagIdList($allIdWithCommaSeperated='')
	{
		if(empty($allIdWithCommaSeperated)){
			return false;
		}
		$allIdWithCommaSeperated=  trim($allIdWithCommaSeperated,",");
		$query="select group_concat(code) as diseases_code from ".TBL_DISEASES_MASTER." where id 
					in($allIdWithCommaSeperated)";
		$resultSet=$this->CI->db->query($query);
		if($resultSet->num_rows()>0){
			return $resultSet->row()->diseases_code;
		}else{
			return false;
		}
	}
	
	function getMaxVisitDate($userId=0)
	{
		if(empty($userId))return false;
		$rowData=$this->CI->manageCommonTaskModel->getMaxVisitDateOfUser($userId);
		if(!$rowData)return false;
		return $rowData;
		
	}

        
        
        function SentSMS($userArray=array(),$message='',$userIdArr=array(),$case='',$msgId='')
	{
		/** Applicable Mobile for testing Purpose ***/                 		
            
            if(count($userArray)>'0'){
                    $CI =& get_instance();
                    $CI->load->model('common/manageCommonTaskModel');
                    $SmsApiArr = $CI->manageCommonTaskModel->getSmsApiInfo();
                    //print_r($SmsApiArr);die;
                    
                    $userArrayMobile = $CI->manageCommonTaskModel->getUserNumberWithType($userIdArr,$case,$msgId);							                    	
                            $userId = $SmsApiArr['user_name'];
                            $senderId= $SmsApiArr['provider_name'];
                            $passWord= $SmsApiArr['password'];				                                                        
                            foreach($userArray as $value){                                                                                               
                                if($value!='' && $value == $userArrayMobile[$value]['mobileNumber']){                                
                                $messageSMS='';
                                $trimedNumber = ltrim($value,"+");
                                $trimedNumber = ltrim($trimedNumber,"0");
                                $messageSMS='Dear '.$userArrayMobile[$value]['userType'].' '.$userArrayMobile[$value]['userName'].', '.$message .' From MedGre';
                                $url = $SmsApiArr['sms_url'];
                                $request="usr=$userId&pwd=$passWord&ph=".$trimedNumber."&sndr=$senderId&text=$messageSMS";						                                                                                                                                                
                                                                
                                $ch = curl_init();						
                                //initialize curl handle
                                curl_setopt($ch, CURLOPT_URL, $url);
                                //set the url
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
                                //return as a variable
                                curl_setopt($ch, CURLOPT_POST, 1);
                                //set POST method
                                curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
                                //set the POST variables
                                $response = curl_exec($ch);
                                //run the whole process and return the response
                                curl_close($ch);				
                                //Get the response from provider
                                //return $response;                                                					
                                }
                            }
                  
                } 		 		                 
                return $response;
	}


	
	/**
     * 	Function		convertPhoneMobileFaxFormat
     * 	Author			Prashant Jain
     * 	Description		This function will convert the mobile/phone/fax number into the passed
	 *					format and by default(XXX-XXX-XXXX)
     * 	@param			$contactNumber: phone ,mobile,fax number
     *                  $format: the output format 
     */
	
	public function convertPhoneMobileFaxFormat($contactNumber='',$format='(XXX)-XXX-XXXX',$flag=true)
	{
		$contactNumber=str_replace('(','',$contactNumber);
		$contactNumber=str_replace(')','',$contactNumber);
		$contactNumber=str_replace('-','',$contactNumber);
		$contactNumber=str_replace(' ','',$contactNumber);
		$contactNumber=trim($contactNumber);
        if($flag===false){
            return $contactNumber;
        }
		$strignLength=strlen($contactNumber);
		if($contactNumber!='' && $strignLength==10){
			if($format=='XXX-XXX-XXXX'){
				$formatedNumber=substr($contactNumber,0,3)."-".substr($contactNumber,3,3)."-".substr($contactNumber,6,4);
			}elseif($format=='(XXX)-XXX-XXXX'){
				$formatedNumber="(".substr($contactNumber,0,3).")-".substr($contactNumber,3,3)."-".substr($contactNumber,6,4);
			}
			return $formatedNumber;
		}elseif($contactNumber!='' && $strignLength==11){
			if($format=='XXX-XXX-XXXX'){
				$formatedNumber=substr($contactNumber,0,1)."-".substr($contactNumber,1,3)."-".substr($contactNumber,4,3)."-".substr($contactNumber,7,4);
			}elseif($format=='(XXX)-XXX-XXXX'){
				$formatedNumber=substr($contactNumber,0,1)."(".substr($contactNumber,1,3).")-".substr($contactNumber,4,3)."-".substr($contactNumber,7,4);
			}
			return $formatedNumber;
		}else{
			return false;
		}
	}

	
	function getFirstLastName($userId)
	{
		$this->CI->load->model('common/manageCommonTaskModel');
		$fName= $this->CI->manageCommonTaskModel->getFLName($userId);
		return $fName['fname'];
	
	}

	
	/**
     * 	Function		convertSsnNumberFormat
     * 	Author			Prashant Jain
     * 	Description		This function will convert the SSN number into the passed
	 *					format and by default(XXX-XX-XXXX)
     * 	@param			$ssnNumber: SSN number
     *                  $format: the output format 
     */
	
	public function convertSsnNumberFormat($ssnNumber='',$format='XXX-XXX-XXXX')
	{
		$ssnNumber=str_replace('-','',$ssnNumber);
		$ssnNumber=trim($ssnNumber);
		$strignLength=strlen($ssnNumber);
		if($ssnNumber!='' && $strignLength==9){
			$formatedNumber=substr($ssnNumber,0,3)."-".substr($ssnNumber,3,2)."-".substr($ssnNumber,5,4);
			return $this->changeSsnFormatToHide($formatedNumber);
		}else{
			return false;
		}
	}
	
	public function convertSsnNumberForDatabase($ssnNumber)
	{
		$ssnNumber=str_replace('-','',$ssnNumber);
		$ssnNumber=trim($ssnNumber);
		$strignLength=strlen($ssnNumber);
		if($ssnNumber!='' && $strignLength==9){
			return $ssnNumber;
		}else{
			return false;
		}
	}

    
    public function getDoctorReferralDoctorlDropDown(){
        $CI =& get_instance();
                    $CI->load->model('common/manageCommonTaskModel');
        $getDoctorNameListOption='';
        $getDoctorNameList=$CI->manageCommonTaskModel->getDoctorNameList();
			if(!empty($getDoctorNameList)){
				foreach($getDoctorNameList as $item){
					if($refDocId==$item->id){
						$getDoctorNameListOption.="<option value='[R]-::-".$item->id."' selected='selected'>".trim($item->doctor_name)." [R]</option>";
					}else{
						$getDoctorNameListOption.="<option value='[R]-::-".$item->id."'>".trim($item->doctor_name)." [R]</option>";
					}
					
				}
			}
			$staffList = $CI->manageCommonTaskModel->getAllStaffList(1);
			if ($staffList) {
				foreach ($staffList as $item) {
					$getDoctorNameListOption.="<option value='[C]-::-".$item->user_id."'>".str_replace(","," ",trim($item->Name))." [C]</option>";
				}
			}
			return $getDoctorNameListOption;
    }

	
	/**
     * 	Function		getDataForPritnReportFormat
     * 	Author			Prashant Jain
     * 	Description		This function will convert the SSN number into the passed
	 *					format and by default(XXX-XX-XXXX)
     * 	@param			$dataFlag: This flag is to know that for what we want to fetch the data as
	 *								1-chief complaint
	 *								2-Vital Sign
	 *								3-Diagnosis
	 *								4-Medication
	 *								5-Allergy
	 *								6-Lab Procedure
	 *								7-Insurance Details
     *                  $patientId: Patiet Id
	 *					$visitId: Visit Id
	 *					$labProId: Lab Procedure Id in case of fetching the lab-procedure data
	 *					$insuranceId: Insurance Id in case of fetching the Insurance Details data
	 *	@return			$getData : The data object array of the required result / false;
     */
	function getDataForPritnReportFormat($dataFlag='',$patientId='',$visitId='',$labProId='',$insuranceId='')
	{	$CI =& get_instance();
		$CI->load->model('common/manageCommonTaskModel');
		$CI->load->model('patientEncounterData/'.$this->CI->session->userdata("userMainType"). '/manageLabProceduresModel');
		if($dataFlag==1){
			$getData=$CI->manageCommonTaskModel->getChiefComplaintDataForPrintReport($patientId,$visitId);
		}elseif($dataFlag==2){
			$getData=$CI->manageCommonTaskModel->getRecord(TBL_PATIENT_VITAL_SIGNS,'*',array('patient_id'=>$patientId,'visit_id'=>$visitId));
		}elseif($dataFlag==3){
			$getData=$CI->manageCommonTaskModel->getDiagnosisDataForPrintReport($patientId,$visitId);
		}elseif($dataFlag==4){
			$getData=$CI->manageCommonTaskModel->getMedicationDataForPrintReport($patientId,$visitId);
		}elseif($dataFlag==5){
			$getData=$CI->manageCommonTaskModel->getAllergyDataForPrintReport($patientId);
		}elseif($dataFlag==6){
			if($visitId!='' && $labProId!=''){
				$labScheduleId = $labProId;
				$getData['labRecord'] = $CI->manageLabProceduresModel->getLabRecordByRequestId($labScheduleId);
				$userSelectedField = array(
										'id',
										'first_name',
										'last_name',
										'chart_number',
										'date_of_birth',
										'state',
										'city',
										'country',
										'zip',
										'email',
										'gender',
										'address',
										'correspondence_address',
										'home_phone',
										'npi'
									);
				$getData['userInfo'] = $CI->manageCommonTaskModel->getProfileField($patientId, $userSelectedField);
				$getData['appointmentData'] = $CI->manageCommonTaskModel->getAppointmentDataByVisitId($patientId,$visitId);
				$providerId = $getData['appointmentData']->provider_id;
				$getData['providerInfo'] = $CI->manageCommonTaskModel->getProfileField($providerId, $userSelectedField, 1);
				
				$gatLabResultIds=$CI->manageCommonTaskModel->getRecord(TBL_PATIENT_LAB_RESULT,'id',array('lab_schedule_id'=>$labScheduleId,'manual_auto_flag'=>'1'),null,0,0,'array',1);
				if($gatLabResultIds){
					$i=0;
					foreach($gatLabResultIds as $item){
						$getData['labResultVal'][$i++]=$CI->manageLabProceduresModel->getLabResult($item['id']);
						
					}
				}
			}
		}elseif($dataFlag==7){
			$getData=$CI->manageCommonTaskModel->getPatientInsuranceDetailById($patientId,$insuranceId);
		}
		return $getData;
	}
	

	/**
     * 	Function		sendFax
     * 	Author			Md Masroor (Updated By Abhishek)
     * 	Description		This function will send fax.
     * 	@param			$documanetName: 
	 *					$title			
     *                  $patientId: Patiet Id
	 *					$CoverPageType: Visit Id
	 *					$CoverPagePath
	 *					$Subject
	 *					$Note
	 *					$Body
	 *					$RecipientsName
	 *					$RecipientsNumber
	 *	@return			result / false;
     */
	function sendFax($documanetName,$title, $CoverPageType=1,$CoverPagePath="" , $Subject, $Note, $Body, $RecipientsName, $RecipientsNumber){
		
		
		if(!empty($RecipientsNumber) && !empty($RecipientsName)){
			$faxserver = new COM ("FaxComEx.FaxServer");
			try {
				$faxserver->Connect ("");
			} catch (Exception $e) {
				echo 'Connect: ',  $e->getMessage(), "\n";
			}

			$faxdoc = new COM ("FaxComEx.FaxDocument" );

			$faxdoc->DocumentName = $documanetName; // This is the name that will appear in the Windows Fax Console
			$faxdoc->Sender->Title = $title; // This is a field defined in the .COV
			$faxdoc->CoverPageType = $CoverPageType; // This specifies the .COV location: 0 = No cover page, 1 = Local, 2 = Server
			$faxdoc->CoverPage = $CoverPagePath; // Don't forget double backslash
			$faxdoc->Subject = $Subject; // This is a field in the .COV and also shows in Fax Console
			$faxdoc->Note = $Note; // This is a field in the .COV
			$faxdoc->Body = $Body;//"C:\\xampp\\test.txt"; // The path to the file you want to fax
			$faxdoc->Recipients->Add ($RecipientsNumber, $RecipientsName);
			try {
				$faxdoc->ConnectedSubmit ($faxserver);
			} catch (Exception $e) {
				echo 'Send: ',  $e->getMessage(), "\n";
			}
			try {
				$faxserver->Disconnect ();
			} catch (Exception $e) {
				echo 'Disconnect: ',  $e->getMessage(), "\n";
			}
		}else{
			return flase;
		}
	}
/**
     * 	Function		getVisitTypeByVisitId
     * 	Author			Rahul Anand
     * 	Description		
     * 	@param			$ssnNumber: SSN number
     *                  $format: the output format 
     */
	
	public function getVisitTypeByVisitId($visitId='')
	{
		if(empty($visitId)){
			return false;
		}
		$row=$this->CI->manageCommonTaskModel->getVisitTypeByVisitId($visitId);
		if($row){
			return $row->visit_type;
		}
	}
	
	 /**
     * 	Function		getDataForRecentSearch
     * 	Author			Prashant Jain
     * 	Description		This function will fetch the data for the recent search dropdown;
     * 	parm			$id=Id of the login staff
     */
	 
	function getDataForRecentSearch($id='')
	{
		if($id!='' && $id!=0){
			$getData=$this->CI->manageCommonTaskModel->getDataForRecentSearch($id);
			if($getData){
				return $getData;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
    
    /**
     * 	Function		calculateNumberOfTimesLabs
     * 	Author			Anshul Agarwal
     * 	Description		This function will be used to count the number of times this test to be done
     * 	parm			$routine,$occurance,$occur_time,$occur_period,$total_time,$total_period
     *  Return          Num of lab procedures
     */
    function calculateNumberOfTimesLabs($routine,$occurance,$occur_time,$occur_period,$total_time,$total_period,$typeOfVisit){
        if($routine=='1'){
            return '1';
        }else if($occurance=='0'){
            //if(($occur_period=='5')||($occur_period=='6')||($occur_period=='7')){
                return 1;
            //}
           /* switch($total_period){
                case '1':
                    if($occur_period=='1'){
                        return (1*$total_time);
                    }
                    break;
                case '2':
                    if($occur_period=='1'){
                        return (1*7*$total_time);
                    }
                    if($occur_period=='2'){
                        return (1*$total_time);
                    }
                    break;
                case '3':
                    if($occur_period=='1'){
                        return (1*30*$total_time);
                    }
                    if($occur_period=='2'){
                        return (1*4*$total_time);
                    }
                    if($occur_period=='3'){
                        return (1*$total_time);
                    }
                    break;
                case '4':
                    if($occur_period=='1'){
                        return (1*365*$total_time);
                    }
                    if($occur_period=='2'){
                        return (1*52*$total_time);
                    }
                    if($occur_period=='3'){
                        return (1*12*$total_time);
                    }
                    if($occur_period=='4'){
                        return (1*$total_time);
                    }
                    break;
                case 'default':
                    return '1';
                    break;
                    
            }*/
        }elseif($occurance=='1'){
            switch($total_period){
                case '1':
                    if($occur_period=='1'){
                        return ($occur_time*$total_time);
                    } else {
                        return 0;
                    }
                    break;
                case '2':
                    if($occur_period=='1'){
                        return ($occur_time*7*$total_time);
                    }else if($occur_period=='2'){
                        return ($occur_time*$total_time);
                    } else {
                        return 0;
                    }
                    break;
                case '3':
                    if($occur_period=='1'){
                        return ($occur_time*30*$total_time);
                    } else if($occur_period=='2'){
                        return ($occur_time*4*$total_time);
                    } else if($occur_period=='3'){
                        return ($occur_time*$total_time);
                    } else {
                        return 0;
                    }
                    break;
                case '4':
                    if($occur_period=='1'){
                        return ($occur_time*365*$total_time);
                    } else if($occur_period=='2'){
                        return ($occur_time*52*$total_time);
                    } else if($occur_period=='3'){
                        return ($occur_time*12*$total_time);
                    } else if($occur_period=='4'){
                        return ($occur_time*$total_time);
                    } else {
                        return 0;
                    }
                    break;
                case 'default' :
                    return 0;
                    break;
            }
            return 0;
        }
    }
    
    public function getLabResult($labScheduleId,$patientId,$visitId){
        $CI =& get_instance();
        $CI->load->model('patientEncounterData/'.$this->CI->session->userdata("userMainType"). '/managePatientLabListModel');
        $labResult=$CI->managePatientLabListModel->getLabResult($labScheduleId,$patientId,$visitId);
        return $labResult;
    }
	
	/**
     * 	Function		executeModelFunctionAndGetTheResultBack
     * 	Author			Rahul Anand
     * 	Description		This function will call the dbase function defined in model magically
     * 	parm			$paramArray
     */
	 function executeModelFunctionAndGetTheResultBack($paramArray=array(),$functionName='')
	 {
		if(empty($functionName)){
			return false;
		}
		return $result=call_user_func_array( array( $this->CI->manageCommonTaskModel, $functionName ),$paramArray );
	 }
	  function calculateAgeByBirthComplete($dob='')
	 {
		if(empty($dob)){
			return '';
		}
		$patientAge='';
		$ageInSeconds=abs((time() - strtotime($dob)));
							$years = floor($ageInSeconds / (365*60*60*24));
							$months = floor(($ageInSeconds - $years * 365*60*60*24) / (30*60*60*24));
							$days = floor(($ageInSeconds - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
							//Let'c combine all name
							if($years>0 && $years!=''){
								$patientAge=$years." Year(s)";
							}
							if($patientAge=='' && $months>0 ){
								$patientAge=$months." Month(s) ";
							}if($patientAge==''){
								$patientAge=$days." day(s)";
							}
		return $patientAge;
	 }
     
     function setSessionTimeOut(){
         $CI =& get_instance();
         $CI->load->model('common/manageCommonTaskModel');
			$sessonGlobalSetting=$CI->manageCommonTaskModel->getGlobalSettings('1','ch_idle_session_timeout');
			
            if(!empty($sessonGlobalSetting['ch_idle_session_timeout'])){
                 $CI->session->sess_expiration=$sessonGlobalSetting['ch_idle_session_timeout'];
                 
                 $CI->session->sess_read();
                 //$CI->session->sess_update();
                 
            } 
     }
/***************************************************************************************
* 	Function		getProviderName
* 	Author			Prashant Jain
* 	Description		This Function used to get Provider name on the basis of patient and 
*					visit id
* 	@param			$patientId
*					$visitId
* 	@return			provider name string
***************************************************************************************/
	function getProviderName($patientId=0,$visitId=0)
	{
		$providerName='';
		if(!empty($patientId) && !empty($visitId)){
			$providerName=$this->CI->manageCommonTaskModel->getProviderName($patientId,$visitId);
		}else{
			return false;
		}
		return $providerName;
	}
	
	
/***************************************************************************************
* 	Function		getAccess
* 	Author			Kuldeep Verma
* 	Description		This Function used to get access permission
* 	@param			$userId,$userTypeId,$emergency
* 	@return			access Data
***************************************************************************************/
	function getAccess($userId,$userTypeId,$emergency)
	{
		$CI =& get_instance();
		$CI->load->model('common/manageCommonTaskModel');
		return $CI->manageCommonTaskModel->getAccess($userId,$userTypeId,$emergency);
	}
	
	
/***************************************************************************************
* 	Function		getClinicalAccess
* 	Author			Kuldeep Verma
* 	Description		This Function used to get access permission
* 	@param			$userId,$userTypeId,$emergency,$moduleName
* 	@return			access Data
***************************************************************************************/
	function getClinicalAccess($userId,$userTypeId,$emergency,$moduleName)
	{
		$CI =& get_instance();
		$CI->load->model('common/manageCommonTaskModel');
		return $CI->manageCommonTaskModel->getClinicalAccess($userId,$userTypeId,$emergency,$moduleName);
	}

	
/***************************************************************************************
* 	Function		getModuleAccess
* 	Author			Kuldeep Verma
* 	Description		This Function used to get access permission
* 	@param			$moduleName
* 	@return			access Data
***************************************************************************************/
	function getModuleAccess($moduleName)
	{
		$CI =& get_instance();
		$CI->load->model('common/manageCommonTaskModel');
		
		$userId=$this->CI->session->userdata('id');
		$userTypeId=$this->CI->session->userdata('userType');
		$emergency=$this->CI->session->userdata('emergency');
		return $CI->manageCommonTaskModel->getModuleAccess($moduleName,$userId,$userTypeId,$emergency);
	}
	
/**************************************************************************************************
* 	Function		getVisitIdsWithInTheRange
* 	Author			Prashant Jain
* 	Description		This function is used to fetch the visit ids and visit dates of any particular 
*					patient with in the given range.
* 	parm			$patientId 	: Patient Id
*					$dateFrom	: Date range strat form.(valid date)
*					$dateTo		: Date range end to.(valid date)
*************************************************************************************************/
	function getVisitIdsWithInTheRange($patientId=0,$dateFrom='',$dateTo='')
	{
		if(!empty($patientId) && !empty($dateFrom) && !empty($dateTo)){
			//first convert the dates into valid database format i.e. (Y-m-d).
			$dateFrom=$this->convertDateFormatForDbase($dateFrom);
			$dateTo=$this->convertDateFormatForDbase($dateTo);
			//echo $patientId."<br>".$dateFrom."<br>".$dateTo;die;
			$getVisitIds=$this->CI->manageCommonTaskModel->getVisitIdsWithInTheRange($patientId,$dateFrom,$dateTo);
			$visitData=array();
			if($getVisitIds){
				foreach($getVisitIds as $item){
					$visitData[$item->visitId]=$this->showDateForSpecificTimeZone($item->visitDate);
				}
			}
			return $visitData;
		}
	}
	/**
     * 	Function		masterDataValue
     * 	Author			Rahul
     * 	Description		this function will used to create the dropdown in the form
     * 	@return			dropdown.
     */
    function masterDataValue($materTypeId = 0) {
		$this->CI->load->model('common/manageCommonTaskModel');
        $dataRequired = $this->CI->manageCommonTaskModel->getRecord(TBL_OTHER_MASTER_DATA, "*", array('id'=>$materTypeId), null, "", "", 'object', '0');
        $returnArray = array();
	   if( $dataRequired){
			return $dataRequired->title;
		}
		return false;
    }
	
/*************************************************************************************
 * 	function	getAgeInDays
 * 	author     	Rahul Anand<rahul.anand@greenapplestech.com>
 * 	Description	This function will get teh age of patient in days
 *
 * *********************************************************************************** */
	function getAgeInDays($dobYMD, $nowYMD=null) {
		$age = -1;

		// strip any dashes from the DOB
		$dobYMD = preg_replace("/-/", "", $dobYMD);
		$dobDay = substr($dobYMD,6,2); $dobMonth = substr($dobYMD,4,2); $dobYear = substr($dobYMD,0,4);
		
		// set the 'now' date values
		if ($nowYMD == null) {
			$nowDay = date("d");
			$nowMonth = date("m");
			$nowYear = date("Y");
		}
		else {
			$nowDay = substr($nowYMD,6,2);
			$nowMonth = substr($nowYMD,4,2);
			$nowYear = substr($nowYMD,0,4);
		}

		// do the date math
		$dobtime = strtotime($dobYear."-".$dobMonth."-".$dobDay);
		$nowtime = strtotime($nowYear."-".$nowMonth."-".$nowDay);
		$timediff = $nowtime - $dobtime;
		$age = $timediff / 86400;

		return $age;
	}

	/**
	* 	Function		getallPages
	* 	Author			Kuldeep verma
	* 	Description		this function will used to pagination
	* 	@return			pagination.
	*/
	function getallPages($totalRecords='',$recordsPerPages='',$paginationId='defaultId')
	{
		$CI =& get_instance();
		$pagination='<div class="pagenation-cliRem" id="'.$paginationId.'">';
		if($totalRecords!='' && $recordsPerPages!=''){
			 $pages = ceil($totalRecords/$recordsPerPages);			 
			 if($pages>'1')
			 {
			 if($pages>'4'){
				if($CI->session->userdata['userType']=='3')
				$pagination .='<span class="next-pre-img" id="page-pre-link"><img src="'.base_url().'images/pagination-pre.png" border="0"></span>';
				if($CI->session->userdata['userType']=='4')
				$pagination .='<span class="next-pre-img" id="page-pre-link">Pre</span>';
				if($CI->session->userdata['userType']=='1' || $CI->session->userdata['userType']=='2')
				$pagination .='<span class="next-pre-img" id="page-pre-link"><img src="'.base_url().'images/student/pagination-pre.png" border="0"></span>';
			 
				$pagination .='<span class="pagenation-padd" id="pre-blank-space">...</span>';
			 }
			 
			 for($i=1;$i<$pages;$i++){
				$pagination .='<span class="pagenation-number" id="'.$i.'">'.$i.'</span>';
			 }
			 
			 if($pages>'4')
			 $pagination .='<span class="pagenation-padd" id="next-blank-space">...</span>';
			 $pagination .='<span class="pagenation-number" id="'.$pages.'">'.$pages.'</span>'; 		
			 
			 if($pages>'4'){
				if($CI->session->userdata['userType']=='3')
				$pagination .='<span class="next-pre-img" id="next-pre-link"><img src="'.base_url().'images/pagination-next.png" border="0" ></span>';
				if($CI->session->userdata['userType']=='4')
				$pagination .='<span class="next-pre-img" id="next-pre-link">Next</span>';
				if($CI->session->userdata['userType']=='1' || $CI->session->userdata['userType']=='2')
				$pagination .='<span class="next-pre-img" id="next-pre-link"><img src="'.base_url().'images/student/pagination-next.png" border="0" ></span>';
			 }
			 
			 }
		}
		
		$pagination .='</div>';                
		return $pagination;
	}

/***************************************************************************************
* 	Function		remStatus
* 	Author			Kuldeep Verma
* 	Description		This Function used to get patient reminder status
* 	@param			$mode,$remId
* 	@return			true/false
***************************************************************************************/
	function remStatus($mode,$remId)
	{
		$CI =& get_instance();
		$CI->load->model('common/manageCommonTaskModel');
		return $CI->manageCommonTaskModel->remStatus($mode,$remId);
	}
	
	
/***************************************************************************************
* 	Function		reminderSentDate
* 	Author			Kuldeep Verma
* 	Description		This Function used to get patient reminder status
* 	@param			$remId
* 	@return			Data
***************************************************************************************/
	function reminderSentDate($remId)
	{
		$CI =& get_instance();
		$CI->load->model('common/manageCommonTaskModel');
		return $CI->manageCommonTaskModel->reminderSentDate($remId);
	}
	

/***************************************************************************************
* 	Function		remStatusDate
* 	Author			Kuldeep Verma
* 	Description		This Function used to get patient reminder status
* 	@param			$remId
* 	@return			Data
***************************************************************************************/
	function remStatusDate($remId)
	{
		$CI =& get_instance();
		$CI->load->model('common/manageCommonTaskModel');
		return $CI->manageCommonTaskModel->remStatusDate($remId);
	}
	
/***************************************************************************************
* 	Function		preferredMode
* 	Author			Kuldeep Verma
* 	Description		This Function used to get patient reminder status
* 	@param			$remId
* 	@return			Data
***************************************************************************************/
	function preferredMode($remId)
	{
		$CI =& get_instance();
		$CI->load->model('common/manageCommonTaskModel');
		return $CI->manageCommonTaskModel->preferredMode($remId);
	}
	
	
/***************************************************************************************
* 	Function		getReferralDoctorlDropDown
* 	Author			Prashant Jain
* 	Description		This Function used to get referring doctor dropdown
* 	@param			$remId
* 	@return			Data
***************************************************************************************/
	public function getReferralDoctorlDropDown($refDocId=''){
        $CI =& get_instance();
        $CI->load->model('common/manageCommonTaskModel');
        $getDoctorNameListOption='';
        $getDoctorNameList=$CI->manageCommonTaskModel->getDoctorNameList();
			if(!empty($getDoctorNameList)){
				foreach($getDoctorNameList as $item){
					if(!empty($refDocId)){
						if($refDocId==$item->id){
							$getDoctorNameListOption.="<option value='".$item->id."' selected='selected'>".$item->doctor_name."</option>";
						}else{
							$getDoctorNameListOption.="<option value='".$item->id."'>".$item->doctor_name."</option>";
						}
					}else{
						$getDoctorNameListOption.="<option value='".$item->id."'>".$item->doctor_name."</option>";
					}
				}
			}
			return $getDoctorNameListOption;
    }
	
/************************************************************************************************
* 		Function 		xmlStringParser
* 		Author			Prashant Jain
* 		Description		The xml string may be have some special characters which XML doesn't support like
*						(>),(<),(&),('),("). so to avoide these we have to replce these with its specific 
*						string parser
***********************************************************************************************/
	function xmlStringParser($xmlString='',$replaceFlag=true)
	{
		//$this->CI->load->helper('xml');
        $this->CI->load->helper('myxml');
		$xmlString=xml_convert($xmlString,FALSE,$replaceFlag);
		return $xmlString;
	}
	
/************************************************************************************************
* 		Function 		removeSpecialCharacter
* 		Author			Kuldeep Verma
* 		Description		remove the special character from the string
***********************************************************************************************/
	function removeSpecialCharacter($string)
	{
		$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
		return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
	}

    
/************************************************************************************************
* 		Function 		getModuleAccessBunchArray
* 		Author			Anshul Agarwal
* 		Description		For permission for the scheduler and appointment links
***********************************************************************************************/
    function getModuleAccessBunchArray(){
        $permissionArray=array();
        $perm=$this->getModuleAccess('patient_chart');
        $permissionArray['patient_chart']=$perm['read_write_flag'];
        
        $perm=$this->getModuleAccess('patient_profile');
        $permissionArray['patient_profile']=$perm['read_write_flag'];
        
        $perm=$this->getModuleAccess('insurance');
        $permissionArray['insurance']=$perm['read_write_flag'];
        
        $perm=$this->getModuleAccess('clinical_history');
        $permissionArray['clinical_history']=$perm['read_write_flag'];
        
        $perm=$this->getModuleAccess('clinical');
        $permissionArray['clinical']=$perm['read_write_flag'];
        
        $perm=$this->getModuleAccess('nursing_st');
        $permissionArray['nursing_st']=$perm['read_write_flag'];
        
        $perm=$this->getModuleAccess('diagnosis');
        $permissionArray['diagnosis']=$perm['read_write_flag'];
        
        $perm=$this->getModuleAccess('prescription');
        $permissionArray['prescription']=$perm['read_write_flag'];
        
        $perm=$this->getModuleAccess('lab_proced');
        $permissionArray['lab_proced']=$perm['read_write_flag'];
        
        $perm=$this->getModuleAccess('track_cli_rem');
        $permissionArray['track_cli_rem']=$perm['read_write_flag'];
        
        $perm=$this->getModuleAccess('soap_note');
        $permissionArray['soap_note']=$perm['read_write_flag'];
        
        $perm=$this->getModuleAccess('reports');
        $permissionArray['reports']=$perm['read_write_flag'];
        
        $perm=$this->getModuleAccess('renewal_req');
        $permissionArray['renewal_req']=$perm['read_write_flag'];
        
        $perm=$this->getModuleAccess('scheduler');
        $permissionArray['scheduler']=$perm['read_write_flag'];
        
        $perm=$this->getModuleAccess('documents');
        $permissionArray['documents']=$perm['read_write_flag'];
        $finalArray=array();
        
        /*status set for the checkin*/
        if(($permissionArray['patient_profile']=='2')&&($permissionArray['insurance']=='2')){
            $finalArray['check_in']='1';
        } else {
            $finalArray['check_in']='0';
            $msg='';
            if($permissionArray['patient_profile']!='2'){
                $msg.="<span>You dont have the permission to perform this action.</span><br>";
            }
            if($permissionArray['insurance']!='2'){
                $msg="<span>You dont have the permission to perform this action.</span>";
            }
            $finalArray['check_in_message']=$msg;
            
        }
		
		if(($permissionArray['patient_profile']=='2')&&($permissionArray['insurance']=='2')){
            $finalArray['counseling']='1';
        } else {
            $finalArray['counseling']='0';
            $msg='';
            if($permissionArray['patient_profile']!='2'){
                $msg.="<span>You dont have the permission to perform this action.</span><br>";
            }
            if($permissionArray['insurance']!='2'){
                $msg="<span>You dont have the permission to perform this action.</span>";
            }
            $finalArray['counseling_message']=$msg;
            
        }
        /*status set for screening*/
        if(($permissionArray['clinical_history']=='2')&&($permissionArray['nursing_st']=='2')){
            $finalArray['screen']='1';
        } else {
            $finalArray['screen']='0';
            $msg='';
            if($permissionArray['clinical_history']!='2'){
                $msg="<span>You dont have the permission to perform this action.</span><br>";
            }
            if($permissionArray['nursing_st']!='2'){
                $msg="<span>You dont have the permission to perform this action.</span>";
            }
            $finalArray['screen_message']=$msg;
        }
        
        /*status set for examine*/
        if(($permissionArray['patient_chart']!='0')&&($permissionArray['nursing_st']!='0')&&($permissionArray['diagnosis']=='2')&&($permissionArray['prescription']=='2')&&($permissionArray['lab_proced']=='2')&&($permissionArray['track_cli_rem']=='2')&&($permissionArray['soap_note']=='2')&&($permissionArray['reports']=='2')&&($permissionArray['renewal_req']=='2')){
            $finalArray['examine']='1';
        } else {
            $finalArray['examine']='0';
            $msg='';
            if($permissionArray['patient_chart']=='0'){
                $msg="<span>You dont have the permission to perform this action.</span><br>";
            }
            if($permissionArray['nursing_st']=='0'){
                $msg="<span>You dont have the permission to perform this action.</span><br>";
            }
            if($permissionArray['diagnosis']!='2'){
                $msg="<span>You dont have the permission to perform this action.</span><br>";
            }
            if($permissionArray['prescription']!='2'){
                $msg="<span>You dont have the permission to perform this action.</span><br>";
            }
            if($permissionArray['lab_proced']!='2'){
                $msg="<span>You dont have the permission to perform this action.</span><br>";
            }
            if($permissionArray['track_cli_rem']!='2'){
                $msg="<span>You dont have the permission to perform this action.</span><br>";
            }
            if($permissionArray['soap_note']!='2'){
                $msg="<span>You dont have the permission to perform this action.</span><br>";
            }
            if($permissionArray['reports']!='2'){
                $msg="<span>You dont have the permission to perform this action.</span><br>";
            }
            if($permissionArray['renewal_req']!='2'){
                $msg="<span>You dont have the permission to perform this action.</span>";
            }
            $finalArray['examine_message']=$msg;
        }
        
        /*status set for checkout*/
        if(($permissionArray['scheduler']=='2')&&($permissionArray['patient_chart']=='2')&&($permissionArray['clinical']=='1')){
            $finalArray['checkout']='1';
        } else {
            $finalArray['checkout']='0';
            $msg='';
            if($permissionArray['scheduler']!='2'){
                $msg="<span>You dont have the permission to perform this action.</span><br>";
            }
            if($permissionArray['patient_chart']!='2'){
                $msg="<span>You dont have the permission to perform this action.</span><br>";
            }
            if($permissionArray['clinical']!='1'){
                $msg="<span>You dont have the permission to perform this action.</span>";
            }
            $finalArray['checkout_message']=$msg;
        }
        
        /*status set for transcribe*/
        if(($permissionArray['patient_chart']!='0')&&($permissionArray['soap_note']=='2')){
            $finalArray['transcribe']='1';
        } else {
            $finalArray['transcribe']='0';
            $msg='';
            if($permissionArray['patient_chart']=='0'){
                $msg="<span>You dont have the permission to perform this action.</span><br>";
            }
            if($permissionArray['soap_note']!='2'){
                $msg="<span>You dont have the permission to perform this action.</span>";
            }
            $finalArray['transcribe_message']=$msg;
        }
        
        $this->CI->session->set_userdata('access_check',$finalArray);
        
    }

	
/************************************************************************************************
* 		Function 		checkMasterDataDeletePermission
* 		Author			Prashant Jain
* 		Description		This function will check whether the record which you want to deleted is being
*						used in the appliation or not
***********************************************************************************************/
	function checkMasterDataDeletePermission($dataTableName='',$otherDataId=0,$masterType=0)
	{
		$CI =& get_instance();
        $CI->load->model('common/manageCommonTaskModel');
        //$getDoctorNameList=$CI->manageCommonTaskModel->getDoctorNameList();
		$count=0;
		if(!empty($dataTableName) && !empty($otherDataId)){
			$childTblArr=array();
			switch($dataTableName){
				case TBL_LANG_MASTER:
				break;
				
				case TBL_PATIENT_ONSULT_LEVEL_MASTER:
				$childTblArr[TBL_PATIENT_CONSULT_LEVEL]="patient_code_label";
				break;
				
				case TBL_APSCH_TYPE_OF_VISIT:
				$childTblArr[TBL_APSCH_APPOINTMENT]="type_of_visit";
				break;
				
				case TBL_UNIT_MASTER:
				$childTblArr[TBL_LOINC_MASTER]="unit";
				break;
				
				case TBL_PATIENT_SMOKING_STATUS_MASTER:
				$childTblArr[TBL_PATIENT_SMOKING_STATUS]="smoking_status";
				break;
				
				case TBL_EDRUG_DOSE_VALUE:
				$childTblArr[TBL_CURRENT_MED_INFO]="dose";
				break;
				
				case TBL_EDRUG_DOSE_TIMING:
				$childTblArr[TBL_CURRENT_MED_INFO]="dose_timing";
				break;
				
				case TBL_EDRUG_DOSEOTHER_MASTER:
				$childTblArr[TBL_CURRENT_MED_INFO]="dose_other";
				break;
				
				case TBL_EDRUG_DISPENSE_MASTER:
				$childTblArr[TBL_CURRENT_MED_INFO]="display_no";
				$childTblArr[TBL_PATIENT_LAB_SCHEDULE]="period";
				break;
				
				case TBL_NEXT_VISIT_MASTER:
				$childTblArr[TBL_APSCH_APPOINTMENT_VISIT]="next_visit_id";
				break;
				
				case TBL_EDRUG_DOSE_UNIT:
				//$childTblArr[TBL_CURRENT_MED_INFO]="dose_unit,quantity_unit";
				$childTblArr[TBL_CURRENT_MED_INFO]="dose_unit";
				$childTblArr[TBL_CURRENT_MED_INFO]="quantity_unit";
				break;
				
				case TBL_EDRUG_REFILL_MASTER:
				$childTblArr[TBL_CURRENT_MED_INFO]="refill";
				break;
				
				case TBL_EDRUG_ROUTE_MASTER:
				$childTblArr[TBL_CURRENT_MED_INFO]="route";
				break;
				
				case TBL_WOMAN_MASTER:
				$childTblArr[TBL_WOMAN_RELATED_INFO]="question_id";
				break;
				
				case TBL_ROS_MASTER:
				$childTblArr[TBL_ROS_INFO]="ros_id";
				break;
				
				case TBL_SPECIMEN_MASTER:
				$childTblArr[TBL_LOINC_MASTER]="specimen";
				break;
				
				case TBL_INSURANCE_MASTER:
				
				break;
				
				case TBL_OTHER_MASTER_DATA:
				if(!empty($masterType)){
					$profileFieldId=0;
					switch($masterType){
						case 2:
						$childTblArr[TBL_PATIENT_SOCIAL_HISTORY]="temprament";
						break;
						
						case 3:
						$childTblArr[TBL_PATIENT_SOCIAL_HISTORY]="alocoholic_type";
						break;
						
						case 4:
						$childTblArr[TBL_PATIENT_IMMUNIZATION_AND_SHOTS]="route";
						break;
						
						case 5:
						//$childTblArr[TBL_REFERRING_DOCTOR]="site_of_injection";
						$childTblArr["ch_ptcld_immunization_shots"]="site_of_injection";
						break;
						
						case 7:
						$childTblArr[TBL_PATIENT_IMMUNIZATION_AND_SHOTS]="speciality";
						break;
						
						case 9:
						$childTblArr[TBL_PATIENT_ALERTTRACK_DATA]="track_title";
						break;
						
						case 10:
						$childTblArr[TBL_APSCH_APPOINTMENT]="payment_type";
						break;
						
						case 15:
						$childTblArr[TBL_INJURY_INFO]="hospitalization_id";
						break;
						
						case 16:
						$childTblArr[TBL_SURGERY_INFO]="operation_id";
						break;
						
						case 17:
						$profileFieldId="68";
						break;
						
						case 18:
						$profileFieldId="67";
						break;
						
						case 19:
						$profileFieldId="72";
						break;
						
						case 23:
						$profileFieldId="35";
						$childTblArr[TBL_PATIENT_SOCIAL_HISTORY]="marital_status";
						break;
						
						case 31:
						$childTblArr[TBL_PATIENT_PROFILE_INSURANCE_DETAILS]="payer_type";
						break;
						
						case 33:
						$childTblArr[TBL_PATIENT_FAMILY_HISTORY]="relative_type";
						$childTblArr[TBL_PATIENT_PROFILE_INSURANCE_DETAILS]="subscriber_relationship";
						break;
						
						case 35:
						$profileFieldId="35";
						//$childTblArr[TBL_PATIENT_SOCIAL_HISTORY]="previous_occupation,current_occupation";
						$childTblArr[TBL_PATIENT_SOCIAL_HISTORY]="previous_occupation";
						$childTblArr[TBL_PATIENT_SOCIAL_HISTORY]="current_occupation";
						break;
						
						case 36:
						$childTblArr[TBL_PATIENT_SOCIAL_HISTORY]="education";
						break;
						
						case 37:
						$childTblArr[TBL_PATIENT_SOCIAL_HISTORY]="re_creation";
						break;
						
						case 38:
						$childTblArr[TBL_PATIENT_SOCIAL_HISTORY]="alcoholic_habbit";
						break;
						
						case 39:
						$childTblArr[TBL_PATIENT_SOCIAL_HISTORY]="std_id";
						break;
						
						case 46:
						$childTblArr[TBL_ALLERGY_INFO]="allergy_level";
						break;
						
						/*case 47:
						$childTblArr[TBL_ALLERGY_INFO]="resulting_in";
						break;*/
						
						case 48:
						$childTblArr[TBL_PATIENT_FAMILY_HISTORY]="health_status";
						break;

						case 65:
						$profileFieldId="65";
						break;
						
						//case 65:
						//$childTblArr[TBL_DISEASE_INFO]="severity_id";
						//$childTblArr[TBL_PATIENT_CHIEF_COMPLIANT]="severity_id";
						//break;
						
						case 66:
						$childTblArr[TBL_PATIENT_CHIEF_COMPLIANT]="status_id";
						break;
						
						case 69:
						$childTblArr[TBL_APSCH_APPOINTMENT_VISIT]="billed_status";
						break;
						
						case 70:
						$childTblArr[TBL_PATIENT_HEALTH_MAINTENANCE]="process_id";
						break;
						
						case 71:
						//$childTblArr[TBL_APSCH_APPOINTMENT]="track_level ";
						$childTblArr["ch_ptedt_patient_alerttrack_data"]="track_level ";
						break;
						
						case 72:
						$childTblArr[TBL_PATIENT_DOC]="status";
						break;
						
						case 75:
						$childTblArr[TBL_PATIENT_ADVANCE_DIRECTIVES]="adv_dir_id";
						break;
						
						case 76:
						$childTblArr[TBL_PATIENT_SOCIAL_HISTORY]="abused_drug_id";
						break;
						
						case 78:
						$profileFieldId="71";
						break;
						
						case 79:
						//$childTblArr[TBL_X12_PARTNER]="receiver_id_qualifier,sender_id_qualifier";
						$childTblArr[TBL_X12_PARTNER]="receiver_id_qualifier";
						$childTblArr[TBL_X12_PARTNER]="sender_id_qualifier";
						break;
						
						case 84:
						$childTblArr[TBL_PATIENT_CONSULT_LEVEL]="modifier";
						break;
						
					}
				}
				//$childTblArr[TBL_ALLERGY_INFO]="allergy_level,resulting_in";
				//$childTblArr[TBL_PATIENT_ADVANCE_DIRECTIVES]="adv_dir_id";
				//$childTblArr[TBL_DISEASE_INFO]="severity_id";
				//$childTblArr[TBL_PATIENT_CONSULT_LEVEL]="modifier";
				//$childTblArr[TBL_PATIENT_SOCIAL_HISTORY]="marital_status,previous_occupation,current_occupation,education,temprament,re_creation,alocoholic_type,alcoholic_habbit,std_id,abused_drug_id";
				//$childTblArr[TBL_PATIENT_FAMILY_HISTORY]="relative_type,health_status";
				//$childTblArr[TBL_PATIENT_HEALTH_MAINTENANCE]="process_id";
				//$childTblArr[TBL_PATIENT_IMMUNIZATION_AND_SHOTS]="site_of_injection,route";
				//$childTblArr[TBL_INJURY_INFO]="hospitalization_id";
				//$childTblArr[TBL_SURGERY_INFO]="operation_id";
				//$childTblArr[TBL_PATIENT_DOC]="status";
				//$childTblArr[TBL_PATIENT_PROFILE_INSURANCE_DETAILS]="payer_type,subscriber_relationship";
				break;
			}
			if($childTblArr){
				foreach($childTblArr as $tableName=>$fieldList){
					$check=$CI->manageCommonTaskModel->checkMasterDataDeletePermission($tableName,$fieldList,$otherDataId);
					if($dataTableName==TBL_OTHER_MASTER_DATA && !empty($profileFieldId)){
						$checkProfile=$CI->manageCommonTaskModel->checkMasterDataDeletePermissionInProfile($otherDataId,$profileFieldId);
					}
					if($check==true || $checkProfile==true){
						$count=1;
						break;
					}else{
						continue;
					}
				}
			}elseif($dataTableName==TBL_LANG_MASTER){
				$checkProfile=$CI->manageCommonTaskModel->checkMasterDataDeletePermissionInProfile($otherDataId,'66');
				if($checkProfile==true){
					$count=1;
				}
			}
		}
		return $count;
	}
	/************************************************************************************************
* 		Function 		float2rat
* 		Author			Rahul Anand
* 		Description		This function will change Decimal to Fraction..
*						used in the appliation or not
******************************************************************************************/

	function float2rat($n, $tolerance = 1.e-6) {
		$h1=1; $h2=0;
		$k1=0; $k2=1;
		$b = 1/$n;
		do {
		$b = 1/$b;
		$a = floor($b);
		$aux = $h1; $h1 = $a*$h1+$h2; $h2 = $aux;
		$aux = $k1; $k1 = $a*$k1+$k2; $k2 = $aux;
		$b = $b-$a;
		} while (abs($n-$h1/$k1) > $n*$tolerance);

		return "$h1/$k1";
	}
	
		
/************************************************************************************************
* 		Function 		fractionToDecimal
* 		Author			Rahul Anand
* 		Description		This function will change Fraction to Decimal..
*						used in the appliation or not
******************************************************************************************/
	function fractionToDecimal($fraction)
	{
		$numerator=0;
		$fraction= preg_replace('/\s+/', ' ',$fraction);
		$space =  strpos("$fraction", " ");
		if ($space >= 1) {
			$wholenumber= preg_split('/ /', $fraction);
			$fractionnew = $wholenumber[1]; //a/b
			$wholenumber = $wholenumber[0]; //whole number
			$fractionsplit= preg_split('/\//', $fractionnew);
			$numerator = (array_key_exists(0,$fractionsplit)?$fractionsplit[0]:0);
			$denominator = (array_key_exists(1,$fractionsplit)?$fractionsplit[1]:0);
		}else if ($space == 0){
			$fractionsplit= preg_split('/\//', $fraction);
			$numerator = $fractionsplit[0];
			$denominator = $fractionsplit[1];
		}
		if($denominator==0){
			$denominator=1;
		}
		$result=((($wholenumber*$denominator)+$numerator)/$denominator);
		return round($result,2);
	}
	
/************************************************************************************************
* 		Function 		userFullName
* 		Author			Shailesh Soni
* 		Description		This function return user full name 
*       parameter		UserId  
******************************************************************************************/	
		function userFullName($userId='',$case='')
		{	
			$CI =& get_instance();
			$CI->load->model('common/manageCommonTaskModel');			
			if($userId!='' && $userId!='0')
			{
                            if($case=='psw')
                                return $CI->manageCommonTaskModel->getUserFullName($userId,$case);                            	
                            else
                                return str_replace(","," ",$userFullNameVal=$CI->manageCommonTaskModel->getUserFullName($userId,''));
                        }else{
				return false;
			}
		
		}
/************************************************************************************************
* 		Function 		checkOperatingSystemName
* 		Author			Shailesh Soni
* 		Description		This function will return the name of the operating system the application is running
*       parameter		UserId  
******************************************************************************************/		
	function checkOperatingSystemName()
	{
		$uagent = $_SERVER['HTTP_USER_AGENT'];
		// the order of this array is important
		$oses   = array(
			'Win311' => 'Win16',
			'Win95' => '(Windows 95)|(Win95)|(Windows_95)',
			'WinME' => '(Windows 98)|(Win 9x 4.90)|(Windows ME)',
			'Win98' => '(Windows 98)|(Win98)',
			'Win2000' => '(Windows NT 5.0)|(Windows 2000)',
			'WinXP' => '(Windows NT 5.1)|(Windows XP)',
			'WinServer2003' => '(Windows NT 5.2)',
			'WinVista' => '(Windows NT 6.0)',
			'Windows 7' => '(Windows NT 6.1)',
			'Windows 8' => '(Windows NT 6.2)',
			'WinNT' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
			'OpenBSD' => 'OpenBSD',
			'SunOS' => 'SunOS',
		   'Ubuntu' => 'Ubuntu',
			'Android' => 'Android',
			'Linux' => '(Linux)|(X11)',
			'iPhone' => 'iPhone',
			'iPad' => 'iPad',
			'MacOS' => '(Mac_PowerPC)|(Macintosh)',
			'QNX' => 'QNX',
			'BeOS' => 'BeOS',
			'OS2' => 'OS/2',
			'SearchBot' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(MSNBot)|(Ask Jeeves/Teoma)|(ia_archiver)'
		);
		$uagent = strtolower($uagent ? $uagent : $_SERVER['HTTP_USER_AGENT']);
		foreach ($oses as $os => $pattern)
			if (preg_match('/' . $pattern . '/i', $uagent))
				return $os;
		return 'Unknown';
	}
        
        /**
     * 	Function		getChildTotalCount
     * 	Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 	Description		this function will used to gat total child of the parent lab
     
     */
    function getChildTotalCount($labScheduleId) {
        $dataRequired = $this->CI->manageLabProceduresModel->getTotalChildOfParentLab($labScheduleId);
        return $dataRequired;
    }
	
	
	function getPatientDeatil($userId='')
	{
			$CI =& get_instance();
			$CI->load->model('common/manageCommonTaskModel');			
			if($userId!='' && $userId!='0')
			{
				return $CI->manageCommonTaskModel->getPatientDetail($userId);	
			}else{
				return false;
			}	
	}
	
	function getPatientDeatilCC($userId='',$mrn='',$searchCritaria='')
	{
			$CI =& get_instance();
			$CI->load->model('common/manageCommonTaskModel');			
			if($userId!='' && $userId!='0')
			{
				return $CI->manageCommonTaskModel->getPatientDetailCC($userId,$mrn,$searchCritaria);	
			}else{
				return false;
			}	
	}
	
	function getExPatientDeatil($userId='')
	{
			$CI =& get_instance();
			$CI->load->model('common/manageCommonTaskModel');			
			if($userId!='' && $userId!='0')
			{
				return $CI->manageCommonTaskModel->getExPatientDetail($userId);	
			}else{
				return false;
			}	
	}
	
	function getPatientLatestVisit($userId='')
	{
		$CI =& get_instance();
		$CI->load->model('common/manageCommonTaskModel');			
		if($userId!='' && $userId!='0')
		{
			return $CI->manageCommonTaskModel->latestVisitDatePatient($userId);	
		}else{
			return false;
		}	
	}
	
	function getPatientNextVisit($userId='')
	{
		$CI =& get_instance();
		$CI->load->model('common/manageCommonTaskModel');			
		if($userId!='' && $userId!='0'){
			return $CI->manageCommonTaskModel->getPatientNextVisit($userId);	
		}else{
			return false;
		}	
	}
	
	function getPatientNextNumberVisit($userId='',$visitId='',$befireAfter='u')
	{	//$befireAfter represent one visit up or down
		// u => one visit up from selected visit
		// d => one visit down from selected visit
		$CI =& get_instance();
		$CI->load->model('common/manageCommonTaskModel');			
		if($userId && $visitId){
			return $CI->manageCommonTaskModel->getPatientNextNumberVisit($userId,$visitId,$befireAfter);	
		}else{
			return false;
		}	
	}
	function getUserProfileId($userId='')
	{
		$CI =& get_instance();
		$CI->load->model('common/manageCommonTaskModel');			
		if($userId!='' && $userId!='0'){
			return $CI->manageCommonTaskModel->getUserProfileId($userId);	
		}else{
			return false;
		}	
	}
	
    
    public function getLabSpecimen($labScheduleId,$patientId,$visitId,$widgetFlag='0'){
        $CI =& get_instance();
        $CI->load->model('patientEncounterData/'.$this->CI->session->userdata("userMainType"). '/managePatientLabListModel');
        $labResult=$CI->managePatientLabListModel->getLabResult($labScheduleId,$patientId,$visitId,$widgetFlag);
        return $labResult;
    }

	
	function getFaxStatusNameById($statusId){
	
		if(!isset($statusId)) return false;
		$faxStatusName = '';
		switch($statusId){
			
			case '0':
			$faxStatusName='Sending';
			break;
			
			case '1':
			$faxStatusName='Failed';
			break;

			case '2':
			$faxStatusName='Success';
			break;			
			
			case '4':
			$faxStatusName='Pending';
			break;
			
			case '5':
			$faxStatusName='In-progress';
			break;
			
			case '6':
			$faxStatusName='Retrying';
			break;
		
			case '7':
			$faxStatusName='Cancelled';
			break;
			
			case '8':
			$faxStatusName='Unknown';
			break;
		}
		return $faxStatusName;
	}

    
    

	
	/**
     * This function will be used to get the provider name just pass the visit id.
     * Function Name		:getProviderNamebyVisitId
	 *	Author			:kuldeep Verma
     */
	function getProviderNamebyVisitId($visitId)
	{
		$CI =& get_instance();
		$CI->load->model('common/manageCommonTaskModel');			
		if($visitId!='' && $visitId!='0'){
			$appointmentId=$this->getAppointmentIdbyVisitId($visitId);	
			if($appointmentId!='' && $appointmentId!='0'){
				$providerId=$this->getProviderIdbyAppointmentId($appointmentId);
				if($providerId!='' && $providerId!='0'){
					return $this->getProviderNamebyProviderId($providerId);
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}	
	}
	
/***************************************************************************************
* 	Function		getResourceName
* 	Author			Aditya Guglani
* 	Description		This Function used to get Resource name on the basis of patient and 
*					visit id
* 	@param			$patientId
*					$visitId
* 	@return			resource name string
***************************************************************************************/
	function getResourceName($patientId=0,$visitId=0)
	{
		$providerName='';
		if(!empty($patientId) && !empty($visitId)){
			$providerName=$this->CI->manageCommonTaskModel->getResourceName($patientId,$visitId);
		}else{
			return false;
		}
		return $providerName;
	}
	

		/**
     * This function will be used to get the provider name just pass the visit id.
     * Function Name		:getResourceNamebyVisitId
	 *	Author			:kuldeep Verma
     */
	function getResourceNamebyVisitId($visitId)
	{
		$CI =& get_instance();
		$CI->load->model('common/manageCommonTaskModel');			
		if($visitId!='' && $visitId!='0'){
			$appointmentId=$this->getAppointmentIdbyVisitId($visitId);	
			if($appointmentId!='' && $appointmentId!='0'){
				$resourceId=$this->getResourceIdbyAppointmentId($appointmentId);
				if($resourceId!='' && $resourceId!='0'){
					return $this->getProviderNamebyProviderId($resourceId);
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}	
	}
	
	/**
     * This function will be used to get the appointmentId by visit id.
     * Function Name		:getAppointmentIdbyVisitId
	 *	Author			:kuldeep Verma
     */
	function getAppointmentIdbyVisitId($visitId)
	{
		$CI =& get_instance();
		$CI->load->model('common/manageCommonTaskModel');
		if($visitId!='' && $visitId!='0'){
			$get=$CI->manageCommonTaskModel->getAppointmentIdbyVisitId($visitId);
			return $get->appointment_id;
		}else{
			return false;
		}
	}
	
	/**
     * This function will be used to get the appointmentId by visit id.
     * Function Name		:getVisitIdByAppointmentId
	 *	Author			:kuldeep Verma
     */
	function getVisitIdByAppointmentId($appId)
	{
		$CI =& get_instance();
		$CI->load->model('common/manageCommonTaskModel');
		if($appId!='' && $appId!='0'){
			$get=$CI->manageCommonTaskModel->getVisitIdByAppointmentId($appId);
			return $get->id;
		}else{
			return false;
		}
	}
	
	/**
     * This function will be used to get the appointmentId by visit id.
     * Function Name		:getProviderIdbyAppointmentId
	 *	Author			:kuldeep Verma
     */
	function getProviderIdbyAppointmentId($appointmentId)
	{
		$CI =& get_instance();
		$CI->load->model('common/manageCommonTaskModel');
		if($appointmentId!='' && $appointmentId!='0'){
			return $CI->manageCommonTaskModel->getProviderIdbyAppointmentId($appointmentId);
		}else{
			return false;
		}
	}
	
	/**
     * This function will be used to get the appointmentId by visit id.
     * Function Name		:getProviderIdbyPatientId
	 *	Author			:kuldeep Verma
     */
	function getProviderIdbyPatientId($patientId)
	{
		$CI =& get_instance();
		$CI->load->model('common/manageCommonTaskModel');
		if($patientId!='' && $patientId!='0'){
			return $CI->manageCommonTaskModel->getProviderIdbyPatientId($patientId);
		}else{
			return false;
		}
	}
	
	/**
     * This function will be used to get the appointmentId by visit id.
     * Function Name		:getProviderNamebyProviderId
	 *	Author			:kuldeep Verma
     */
	function getProviderNamebyProviderId($providerId, $suffix='0')
	{
		$CI =& get_instance();
		$CI->load->model('common/manageCommonTaskModel');
		if($providerId!='' && $providerId!='0'){
			return $CI->manageCommonTaskModel->getProviderNamebyProviderId($providerId,$suffix);
		}else{
			return false;
		}
	}
	
	function getProviderNameInGlobalSettingbyProviderId($providerId) {
		$CI =& get_instance();
		$CI->load->model('common/manageCommonTaskModel');
		if($providerId!='' && $providerId!='0'){
			return $CI->manageCommonTaskModel->getProviderNameInGlobalSettingbyProviderId($providerId);
		}else{
			return false;
		}
	}
	
	function getProviderNamebyProviderIdForLab($providerId)
	{
		$CI =& get_instance();
		$CI->load->model('common/manageCommonTaskModel');
		if($providerId!='' && $providerId!='0'){
			return $CI->manageCommonTaskModel->getProviderNamebyProviderIdForLab($providerId);
		}else{
			return false;
		}
	}
	
	/**
     * This function will be used to get the appointmentId by visit id.
     * Function Name		:getProviderIdbyVisitId
	 *	Author			:kuldeep Verma
     */
	function getProviderIdbyVisitId($visitId)
	{
		$CI =& get_instance();
		$CI->load->model('common/manageCommonTaskModel');
		if($visitId!='' && $visitId!='0'){
			return $CI->manageCommonTaskModel->getProviderId($visitId);
		}else{
			return false;
		}
	}
	
/**
     * This function will be used to get the appointmentId by visit id.
     * Function Name		:getProviderIdbyAppointmentId
	 *	Author			:kuldeep Verma
     */
	function getResouceIdbyAppointmentId($appointmentId)
	{
		$CI =& get_instance();
		$CI->load->model('common/manageCommonTaskModel');
		if($appointmentId!='' && $appointmentId!='0'){
			return $CI->manageCommonTaskModel->getResourceIdbyAppointmentId($appointmentId);
		}else{
			return false;
		}
	}
	
	/**
     * This function will be used to get the appointmentId by visit id.
     * Function Name		:getResourceIdbyVisitId
	 *	Author			:kuldeep Verma
     */
	function getResourceIdbyVisitId($visitId)
	{
		$CI =& get_instance();
		$CI->load->model('common/manageCommonTaskModel');
		if($visitId!='' && $visitId!='0'){
			return $CI->manageCommonTaskModel->getResourceId($visitId);
		}else{
			return false;
		}
	}
	
	
	/**
     * This function will be used to get the provider info by visit Id, or Patient Id
     * Function Name		:getProviderInfoByVisitIdOrPatient
	 *	Author				Rahul Anand<rahul.anand@greenapplestech.com>
     */
	 function getProviderInfoByVisitIdOrPatient($visitId=0,$patientId=0)
	 { 
		if(!empty($visitId)){
			$query="select tblApp.provider_id,tblApp.resource_id,
						concat(IFNULL((select field_value from ch_uaprm_user_profile_data where user_id=tblApp.provider_id
				and user_field_id=3),''),' ',
				IFNULL((select field_value from ch_uaprm_user_profile_data 		where 	user_id=tblApp.provider_id
				and user_field_id=5),'')
				)provider_name,
				concat(IFNULL((select field_value from ch_uaprm_user_profile_data where user_id=tblApp.resource_id
				and user_field_id=3),''),' ',
				IFNULL((select field_value from ch_uaprm_user_profile_data 		where 	user_id=tblApp.resource_id
				and user_field_id=5),'')
				)resource_name
				from ch_apsch_appointment_visit tblAppVisit 
					inner join  ch_apsch_appointment tblApp on
					tblApp.id=tblAppVisit.appointment_id and tblAppVisit.id=$visitId";
		}else if(!empty($patientId)){
			$query="select tblApp.provider_id,tblApp.resource_id,
						concat(IFNULL((select field_value from ch_uaprm_user_profile_data where user_id=tblApp.provider_id
				and user_field_id=3),''),' ',
				IFNULL((select field_value from ch_uaprm_user_profile_data 		where 	user_id=tblApp.provider_id
				and user_field_id=5),'')
				)provider_name,
				concat(IFNULL((select field_value from ch_uaprm_user_profile_data where user_id=tblApp.resource_id
				and user_field_id=3),''),' ',
				IFNULL((select field_value from ch_uaprm_user_profile_data 		where 	user_id=tblApp.resource_id
				and user_field_id=5),'')
				)resource_name
				from ch_apsch_appointment_visit tblAppVisit 
					inner join  ch_apsch_appointment tblApp on
					tblApp.id=tblAppVisit.appointment_id and tblAppVisit.patient_id=$patientId and tblAppVisit.id in (select max(id) from ch_apsch_appointment_visit where patient_id=$patientId)"; 
		}
		$allResultSet= $this->CI->manageCommonTaskModel->executeQuery($query,true);
		if($allResultSet)
		return $allResultSet;
		return false;
	 }
	 
	 /**
     * This function will be used to get the provider info by visit Id, or Patient Id
     * Function Name		:getUserNameOnly
	 *	Author				Rahul Anand<rahul.anand@greenapplestech.com>
     */
	 
	function getUserNameOnly($userId=0)
	{
	
		if(!empty($userId)){
			$userType=$this->getUserTypeIdByUserId($userId);
			if($userType=='7'){
				$query="select concat(IFNULL(first_name,''),' ',IFNULL(last_name,'')) as provider_name
				from ch_uaprm_patient_profile_data where user_id=$userId";
			}else{
				$query="select concat(IFNULL((select field_value from ch_uaprm_user_profile_data where user_id=$userId
				and user_field_id=3),''),' ',
				IFNULL((select field_value from ch_uaprm_user_profile_data 		where 	user_id=$userId
				and user_field_id=5),'')
				) as provider_name";
			}
		}
		$allResultSet= $this->CI->manageCommonTaskModel->executeQuery($query,true);
		if($allResultSet)
		return $allResultSet->provider_name;
		return false;
	}
	
	/*for charge capture to login all type of users*/
	function getUserNameOnlyCc($userId=0)
	{
		if($userId != 'all'){
			$whereCon = " user_id=".$userId;
		}else{
			$whereCon="";
		}
	
		if(!empty($userId)){
			$userType=$this->getUserTypeIdByUserId($userId);
			if($userType=='7'){
				$query="select concat(IFNULL(first_name,''),' ',IFNULL(last_name,'')) as provider_name
				from ch_uaprm_patient_profile_data where user_id=$userId";
			}else{
				$query="select concat(IFNULL((select field_value from ch_uaprm_user_profile_data where user_id=$userId
				and user_field_id=3),''),' ',
				IFNULL((select field_value from ch_uaprm_user_profile_data 		where 	user_id=$userId
				and user_field_id=5),'')
				) as provider_name";
			}
		}
		$allResultSet= $this->CI->manageCommonTaskModel->executeQuery($query,true);
		if($allResultSet)
		return $allResultSet->provider_name;
		return false;
	}
	 
	 /**
     * 
     * Function Name		:getUserNameOnly
	 *	Author				Rahul Anand<rahul.anand@greenapplestech.com>
     */
	 function getProviderInfoList()
	 {
		
			$query="select id,
			concat(IFNULL((select field_value from ch_uaprm_user_profile_data where user_id=tbl1.id
				and user_field_id=3),''),' ',
				IFNULL((select field_value from ch_uaprm_user_profile_data 		where 	user_id=tbl1.id
				and user_field_id=5),'')
				) as provider_name
				 from ch_user tbl1
				where user_type='1' and 
			delete_flag!='1' and active_status='1'
			order by provider_name asc";
		

		$allResultSet= $this->CI->manageCommonTaskModel->executeQuery($query,false);
		if($allResultSet)
			return $allResultSet;
			return false;
	 }
	 
	 function getProviderAndStaffList($userTppeIdArr=array(1))
	 {
			
			$query="select id,
			concat(IFNULL((select field_value from ch_uaprm_user_profile_data where user_id=tbl1.id
				and user_field_id=3),''),' ',
				IFNULL((select field_value from ch_uaprm_user_profile_data 		where 	user_id=tbl1.id
				and user_field_id=5),'')
				) as provider_name
				 from ch_user tbl1
				where user_type IN(".implode(',',$userTppeIdArr).") and 
			delete_flag!='1' and active_status='1'
			order by provider_name asc";
		

		$allResultSet= $this->CI->manageCommonTaskModel->executeQuery($query,false);
		if($allResultSet)
			return $allResultSet;
			return false;
	 }

	
	/**
	* 	This function will be used to get the index of the 2d array for alert track data tab;
	* 	Function Name		:	getIndexOf2DArrayOnValueForAlertTrackTab
	*	Author				:	Prashant Jain
	*/
	function getIndexOf2DArrayOnValueForAlertTrackTab($singleval,$arr)
	{
		foreach($arr as $keyV=>$val)
		{
			if($val['track_title']==$singleval)return $keyV;
		}
	}
	
	
	function getFaciltyName($facilId='')
	{
			$CI =& get_instance();
			$CI->load->model('common/manageCommonTaskModel');			
			if($facilId!='' && $facilId!='0')
			{
				return $CI->manageCommonTaskModel->getFaciltyName($facilId);	
			}else{
				return false;
			}	
	}
	
	function getPatientCodeLevelName($facilId='')
	{
			$CI =& get_instance();
			$CI->load->model('common/manageCommonTaskModel');			
			if($facilId!='' && $facilId!='0')
			{
				return $CI->manageCommonTaskModel->getPatientCodeLevelName($facilId);	
			}else{
				return false;
			}	
	}

        /**
	* 	This function will be used to get loinc code on behalf of lab Id;This code is passed in infobutton.
	* 	Function Name		:	getLoincCode
        *       Parameter               :       lab id
	*	Author			:	Prashant Sahu
	*/
        function getLoincCode($loincId='')
        {            
            $CI =& get_instance();
            $CI->load->model('common/manageCommonTaskModel');
            if(!empty($loincId))
            {
                return $CI->manageCommonTaskModel->getPatientLoincCode($loincId);	
            }else{
                return false;
            }
        }
        

	
	function destroySessionMobile() {
        $CI =& get_instance();		
		delete_cookie('userName', '', '/', 'blueberry_');
        delete_cookie('password', '', '/', 'blueberry_');
        delete_cookie("userName");
        delete_cookie("password");
        $this->CI->session->sess_destroy();
		if ($_SERVER['SERVER_PORT'] == 443)  {
			$CI->config->config['base_url'] = str_replace('http://', 'https://', $CI->config->config['base_url']);
		}
		echo "<script>window.location.href='" . $CI->config->config['base_url'] . "mlogin';</script>";
        die;
    }
	
	function fetchTemplate($moduleId, $templateName)
	{
			$CI =& get_instance();
			$CI->load->model('common/manageCommonTaskModel');			
			if($moduleId!='' && $templateName!='')
			{
				return $CI->manageCommonTaskModel->getTemplateInfo($moduleId, $templateName);	
			}else{
				return false;
			}
	}
	
	function getAllServiceList($billId='')
	{
		if($billId!='' && $billId!='0')
		{
			$CI =& get_instance();
			$CI->load->model('common/manageCommonTaskModel');	
			return $CI->manageCommonTaskModel->getAllServiceList($billId);	
		}else{
			return false;
		}	
	}
	
	
	function getAlldiagnosisDetailCC($codeStr='')
	{
		if($codeStr!='' && $codeStr!='0')
		{
			$CI =& get_instance();
			$CI->load->model('common/manageCommonTaskModel');	
			return $CI->manageCommonTaskModel->getAlldiagnosisDetailCC($codeStr);	
		}else{
			return false;
		}
	}
	
	function getAllprocedureDetailCC($codeStr='')
	{
		if($codeStr!='' && $codeStr!='0')
		{
			$CI =& get_instance();
			$CI->load->model('common/manageCommonTaskModel');	
			return $CI->manageCommonTaskModel->getAllprocedureDetailCC($codeStr);	
		}else{
			return false;
		}
	}

	function getBillDiseseDetail($billId='',$Serviceid='',$codeId='')
	{
		$CI =& get_instance();
		$CI->load->model('chargecapture/chargecaptureModel');	
		return $CI->chargecaptureModel->getBillDiseseDetail($billId,$Serviceid,$codeId);	
	}
	
	function changeTimeFormatAsGloSet($time='')
	{	
		$gloSet= $this->getGlobalSettings('1', 'ch_default_time_format');
		if($time!=''){
			if($gloSet['ch_default_time_format']=='24 Hours'){
				return date('H:i:s', strtotime($time));
			}else if($gloSet['ch_default_time_format']=='12 Hours'){
				return date('h:i a', strtotime($time));
			}else{
				return $time;
			}
		}else{
			return false;
		}
	}

	/**
     * 
     * Function Name		changeSsnFormatToHide
	 *	Author				Rahul Anand<rahul.anand@greenapplestech.com>
     */
	 function changeSsnFormatToHide($formatedSsnNo='')
	 {
		if(empty($formatedSsnNo)) return;
		$replacableStr=substr_replace($formatedSsnNo,"xxx",0,3);
		$replacableStr=substr_replace($replacableStr,"xx",4,2);
		return $replacableStr;
	 }
	 
	 /**
     * 
     * Function Name		showPopupOrNotForMedRecon
	 *	Author				Rahul Anand<rahul.anand@greenapplestech.com>
     */
	 function showPopupOrNotForMedRecon($patientId = 0, $visitId = 0,$dontAddIgnorePopUpcondition=0)
	 {
		if(empty($visitId)){
			$maxVisitId=$this->CI->manageCommonTaskModel->getMaxVisitIdOfUser($patientId);
		}else{
			$maxVisitId=$visitId;
		}
		if($maxVisitId){
			$conditionWhere='';
			//now get the appointment id for this visit id
			$appintmentInfo=$this->CI->manageCommonTaskModel->getAppointmentIdByVisitId($maxVisitId);
			if($appintmentInfo){
				$appintmentId=$appintmentInfo->appointment_id;
				$queryToGetAndCheckMedRePer=" 
				select
				recon.appointment_id,
				recon.recon_done,
				recon.ignore_show_confirmation 
				from ch_ptcld_medication_recon_perform recon
				inner join  ch_apsch_appointment tblApp
				on recon.appointment_id=tblApp.id
				inner join  ch_apsch_appointment_visit tblAppVisit
				on recon.appointment_id=tblAppVisit.appointment_id
				where 
				tblAppVisit.patient_id=$patientId
				and recon.appointment_id<=$appintmentId 
				and  tblApp.referral_flag='1'
				order by recon.id 
				desc
				";
				
				$checkQuery=$this->CI->manageCommonTaskModel->executeQuery($queryToGetAndCheckMedRePer,false);
				if($checkQuery){
					//hmm record not found found...
					foreach($checkQuery as $singleRec)
					{
						if($singleRec->recon_done==1){
							return false;
						}else if($singleRec->recon_done==0){
							if($singleRec->ignore_show_confirmation==0){
								return true;
							}else{
								if($dontAddIgnorePopUpcondition==0)
								return false;
								else 
								return true;
							}
						}
					}
				}else{
					return false;
				}
			}
		}
	}
	
	/**
     * 
     * Function Name		showItemForProviderOnly
	 *	Author				Rahul Anand<rahul.anand@greenapplestech.com>
     */ 
	 function showItemForProviderOnly($currentSelectedProviderId=0,$itemStr='',$allowForNurse=true,$returnTrueFalse=false,$visitId=0)
	 {	
		if(empty($currentSelectedProviderId) && empty($visitId)) return false;
		if(empty($currentSelectedProviderId) && $visitId!='all' && $visitId!='active'){
			$query="select provider_id from 
				ch_apsch_appointment 
				where id=(select appointment_id from 
				ch_apsch_appointment_visit where id=$visitId)";
			$checkQuery=$this->CI->manageCommonTaskModel->executeQuery($query,true);
			if($checkQuery){
				$currentSelectedProviderId=$checkQuery->provider_id;
			}
		}
		if(empty($currentSelectedProviderId))return false;
		$userloginId=$this->CI->session->userdata("id"); 
		$userTypeLogin=$this->CI->session->userdata("userType");

		//if((	$userTypeLogin==1 && $currentSelectedProviderId==$userloginId) 
		if((	$userTypeLogin==1)

			|| ($userTypeLogin==2 && $allowForNurse==true)){
			//means give access for this provider
			return $returnTrueFalse==true?true:$itemStr;
		}else{
			return $returnTrueFalse==true?false:'';
		}
	 }
	
	/**
     * 
     * Function Name		showPopupOrNotForMedRecon
	 *	Author				Rahul Anand<rahul.anand@greenapplestech.com>
     */
	 function showCheckBoxInCheckModeOrNot($patientId = 0, $visitId = 0)
	 {
		if(empty($visitId)){
			$maxVisitId=$this->CI->manageCommonTaskModel->getMaxVisitIdOfUser($patientId);
		}else{
			$maxVisitId=$visitId;
		}
		if($maxVisitId){
			//now get the appointment id for this visit id
			$appintmentInfo=$this->CI->manageCommonTaskModel->getAppointmentIdByVisitId($maxVisitId);
			if($appintmentInfo){
				$appintmentId=$appintmentInfo->appointment_id;
				$queryToGetAndCheckMedRePer=" 
				select
				recon.appointment_id,
				recon.recon_done,
				recon.ignore_show_confirmation 
				from ch_ptcld_medication_recon_perform recon
				inner join  ch_apsch_appointment tblApp
				on recon.appointment_id=tblApp.id
				inner join  ch_apsch_appointment_visit tblAppVisit
				on recon.appointment_id=tblAppVisit.appointment_id
				where recon.recon_done='1' and
				tblAppVisit.patient_id=$patientId
				and recon.appointment_id_on_which_recon_done>=$appintmentId 
				and recon.appointment_id<=$appintmentId
				and  tblApp.referral_flag='1'
				order by recon.id 
				desc 
				";
				
				$checkQuery=$this->CI->manageCommonTaskModel->executeQuery($queryToGetAndCheckMedRePer,false);
				if($checkQuery){
					//hmm record not found found...
					return true;
				}else{
					return false;
				}
			}
		}
	}
	/**
     * 
     * Function Name		makeFormatOfPatientNameAccGlst
	 *	Author				Rahul Anand<rahul.anand@greenapplestech.com>
     */
	function makeFormatOfPatientNameAccGlst($patientNameFormat='',$patientFullNameArr=array())
	{

		 switch($patientNameFormat){            
            case "lf":
            $patientFullNameFormated=$patientFullNameArr['2'].", ".$patientFullNameArr['0'];
            return ucwords(strtolower(trim($patientFullNameFormated)));
            break;
            case "lfm":
            $patientFullNameFormated= $patientFullNameArr['2'].", ".$patientFullNameArr['0']." ".$patientFullNameArr['1'];
            return ucwords(strtolower(trim($patientFullNameFormated)));
            break;
            case "fml":
            $patientFullNameFormated= $patientFullNameArr['0']." ".$patientFullNameArr['1']." ".$patientFullNameArr['2'];
            return ucwords(strtolower(trim($patientFullNameFormated)));
            break;
            default:
            $patientFullNameFormated= $patientFullNameArr['0']." ".$patientFullNameArr['2'];
            return ucwords(strtolower(trim($patientFullNameFormated)));
            }
	}
	/**
     * 
     * Function Name		checkOnEncounterVisit
	 *	Author				Rahul Anand<rahul.anand@greenapplestech.com>
     */
	 function checkOnEncounterVisit($bySession=0)
	 {
		if($bySession==1){
			if($this->CI->session->flashdata('encounter') && $this->CI->session->flashdata('encounter')==1){
				return true;
			}else{
				return false;
			}
			
		}else{
			$cncounterStr=$this->CI->uri->segment($this->CI->uri->total_segments());
			if(strpos($cncounterStr,'encounter')!==false){
				return true;
			}
			return false;
		}
	 }
	 
	 
	 /**
	 *	Function Name		sendClinicalReminder
	 *	Author				Shailesh Soni<shailesh.soni@greenapplestech.com>
	 *
	 */
	 
	 function sendClinicalReminder($remId='')
	 {
		if($remId!='' && $remId!='0')	
		{	
		
			
			$this->CI = & get_instance();
			$getRP = $this->CI->manageCommonTaskModel->getCDSRulePatientId($remId);
			
			if($getRP)
			{
			//let's create the message for the patient..
			$providerName='';
			$providerInfo=$this->getProviderInfoByVisitIdOrPatient($getRP['visit_id']);
			if($providerInfo){
				 $providerName=$providerInfo->provider_name;
			}
			$clinicInfo = $this->CI->manageCommonTaskModel->getRecord('ch_cinfs_clinic_profile', "*");
			
			$userSelectedFieldForP = array(
										'first_name',
										'middle_name',
										'last_name',
										'state',
										'city',
										'country',
										'zip',
										'email',
										'gender',
										'address',
										'correspondence_address',
										'home_phone'
									); 
		$patientInfo = $this->CI->manageCommonTaskModel->getProfileField($getRP['patient_id'], $userSelectedFieldForP);
		if($patientInfo){
			$flipeedArr=array_flip($userSelectedFieldForP);
			//array_replace($flipeedArr,)
		}
			$templateForSendingMail="
			Hello {first_name} {middle_name} {last_name}<br/>
			{address}<br/>
			{correspondence_address}<br/>
			{city},
			{state}-{zip}<br/>
			Telephone: {home_phone}<br/>
			<p>
			Greetings!! From {clinicname}<br/><br/>
			Our records show that your {caresuggestion} review with {providername} has fallen due.<br/><br/>
			You are requested to Contact {cliniccontact} to schedule an appointment.<br/><br/>		
			Thanks & Regards<br/>
			{providername}<br/>
			{clinicname}<br/>
			{address1} 
			{address2}<br/>
			{cliniccity},
			{clinicstate}-{cliniczip}<br/>
			Telephone: {cliniccontact} <br/>
			{clinicwebsite}</p>";
			$patientInfo['providername']=$providerName;
			if($clinicInfo){
				$clinicName=$clinicInfo['clinic_name'];
				$clinicAddress1=$clinicInfo['address_1'];
				$clinicAddress2=$clinicInfo['address_2'];
				$clinicCity=$clinicInfo['city'];
				$clinicState=$clinicInfo['state'];
				$clinicZip=$clinicInfo['zip'];
				$clinicContact=$clinicInfo['phone_l'];
				$clinicWebsite=$clinicInfo['website_url'];
				
			}
			$patientInfo['clinicname']=$clinicName;
			$patientInfo['address1']=$clinicAddress1;
			$patientInfo['address2']=$clinicAddress2;
			$patientInfo['cliniccity']=$clinicCity;
			$patientInfo['clinicstate']=$clinicState;
			$patientInfo['cliniczip']=$clinicZip;
			$patientInfo['cliniccontact']=$clinicContact;
			$patientInfo['clinicwebsite']=$clinicWebsite;
			$patientInfo['caresuggestion']=$getRP['care_suggestion'];
			foreach($patientInfo as $key=>$singleField)
			{
				!empty($singleField)?"<br/>".$singleField:$singleField;
				
				$templateForSendingMail=str_replace("{".$key."}",$singleField,$templateForSendingMail);
			}
			$message =$templateForSendingMail;
			
			$from=$this->CI->manageCommonTaskModel->getUserEmail($this->CI->session->userdata('id'));
			$PatientEmail = $this->CI->manageCommonTaskModel->getUserEmail($getRP['patient_id']);
			if($PatientEmail){
				$to=$PatientEmail;
				if($patientInfo['middle_name']!='')
					$subject = "Clinical Reminder: ".$patientInfo['first_name']." ".$patientInfo['middle_name']." ".$patientInfo['last_name'];
				else
					$subject = "Clinical Reminder: ".$patientInfo['first_name']." ".$patientInfo['last_name'];
				//$message=$getRP['rule_title'];
				$check=$this->sendEmail($from,$to,'','',$subject,$message,array(),'',false,'html');
				if($check==true){
					$this->CI->manageCommonTaskModel->UpdateReminderStat($remId);
					echo $this->CI->lang->line('success');
				}else{
					echo $this->CI->lang->line('error');
				}
			}else{
				//echo $this->CI->lang->line('error');
			}
			
			}
		}
	 }
	 
	  /**
	 *	Function Name		checkCheckInLogin
	 *	Author				Rahul Anand<rahul.anand@greenaplestech.com>
	 *
	 */
	 function validateCheckInLogin($fl=0)
	 {
		if($fl==1){//means user login page...
			if($this->CI->session->userdata('cInPat')){
			//ok now get the user id from session and send him on a url...
			$allD=$this->CI->session->userdata('cInPat');
			$userId=$allD['uId'];
			if(!empty($userId)){
				//echo "<script type='text/javascript'>window.location.href='".base_url()."checkIn/index/".$userId."'</script>";
			}
		}
		}else{
			if(!$this->CI->session->userdata('cInPat')){
				echo "<script type='text/javascript'>window.location.href='".base_url()."checkIn'</script>";
			}
		}
	 }

	function validateProcessLogin($fl=0)
	 {
		if($flow==1)
		{
			$userId=$this->CI->session->userdata('userName');
			if(!empty($userId)){
				//echo "<script type='text/javascript'>window.location.href='".base_url()."process/process" . "'</script>";
			}
		}
		else
		{
			if(!$this->CI->session->userdata('cInPat')){
				echo "<script type='text/javascript'>window.location.href='".base_url()."process/process'</script>";
			}
		}
	 }

	  /**
	 *	Function Name		destroySessionCheckIn
	 *	Author				Rahul Anand<rahul.anand@greenaplestech.com>
	 *
	 */
	 function destroySessionCheckIn()
	 {
		 $this->CI->session->sess_destroy();

	 }
	 
	 /**
	 *	Function Name		getAvailableDateForP
	 *	Author				Rahul Anand<rahul.anand@greenaplestech.com>
	 *
	 */
	 function getAvailableDateForP($patientId,$startDate,$endDate,$ignoreAppId='')
	 {
		$whereCod='';
		if(!empty($ignoreAppId)){
			$whereCod=" and tbl1.id not in ($ignoreAppId) ";
		}
		$mainArrtoReturn=array();
		$startDateStr=strtotime($startDate);
		$endDateStr=strtotime($endDate);
		while($startDateStr<=$endDateStr){
			//get the one by one date
			$indDate=date('Y-m-d',$startDateStr);
		$query=" select count(*) as num from 
				ch_apsch_appointment tbl1 
				inner join ch_apsch_patient_appointment_status tbl2 
				on tbl1.id=tbl2.appointment_id and tbl2.is_current_status='1'
				where tbl1.user_profile_id=$patientId 
				and tbl1.start_time between '$indDate 00:00:00' and '$indDate 23:59:59' $whereCod and tbl2.appointment_status in(4,5,6,7,11) ";
			$resultSetRow=$this->CI->manageCommonTaskModel->executeQuery($query,true);
			$mainArrtoReturn[date('Ymd',$startDateStr)]=$resultSetRow->num;
			$startDateStr=$startDateStr+86400;//add one day ahead..
		}
		return $mainArrtoReturn;
	 }
     
     
     
     function getSpecimenFormat($specimen,$text,$widgetFlag){
         if($widgetFlag=='0'){
             return $specimen.'<br>'.$text;
         } else {
             $speCount=strlen($specimen);
             if($speCount>=21){
                 return $this->shortenText($specimen,21,true,true);
             } else {
                 return $specimen.'<br>'.$this->shortenText($text,(21-$speCount),true,true);
             }
         }
     }

	function getSMSTemplateWithValues($templateType='',$userPatientId, $password='',$url='')
	{	//echo $userPatientId; die;
		$getTemplateTags=$this->CI->manageCommonTaskModel->getRecord(TBL_GLOBAL_SETTING_TAMPLATE_TAG,'tag',array('template_type_id'=>$templateType,'active_flag'=>'1'));
		$getTemplate=$this->CI->manageCommonTaskModel->getRecord(TBL_GLOBAL_SETTING_TAMPLATE,'sms_message_content',array('id'=>$templateType,'active_flag'=>'1'));
		$getTemplateString=$getTemplate['sms_message_content'];
		if($getTemplateTags && !empty($getTemplateString)){
			foreach($getTemplateTags as $tag){
				$tagVariable=$tag['tag'];
				if(strpos($getTemplateString,$tagVariable)!==false){
					$variableText=$this->getVariableValue($tagVariable,$userPatientId, $password,$url,'sms');
					if($tagVariable=='{{address_1}}' || $tagVariable=='{{address_2}}'){
						if($variableText!=''){
							$getTemplateString=str_replace($tagVariable,$variableText,$getTemplateString);
						}else{
							$getTemplateString=str_replace($tagVariable."<br />",$variableText,$getTemplateString);
						}
					}elseif($tagVariable=='{{city}}'){
						if($variableText!=''){
							$getTemplateString=str_replace($tagVariable,$variableText,$getTemplateString);
						}else{
							$getTemplateString=str_replace($tagVariable.",",$variableText,$getTemplateString);
						}
					}elseif($tagVariable=='{{zip}}'){
						if($variableText!=''){
							$getTemplateString=str_replace($tagVariable,$variableText,$getTemplateString);
						}else{
							$getTemplateString=str_replace("&ndash;".$tagVariable,$variableText,$getTemplateString);
						}
					}elseif($tagVariable=='{{url}}'){
						if($variableText!=''){
							$getTemplateString=str_replace($tagVariable,$variableText,$getTemplateString);
						}else{
							$getTemplateString=str_replace("&ndash;".$tagVariable,$variableText,$getTemplateString);
						}
					}else{
						$getTemplateString=str_replace($tagVariable,$variableText,$getTemplateString);
					}
				}
			}
		}
		return $getTemplateString;
	}

	 
	function getEmailTemplateWithValues($templateType='',$userPatientId, $password='',$url='',$resourceId='',$reminderData='',$type='0',$appId='0')
	{	//echo $userPatientId; die;
		/* Fetch 0->Mail 1->SMS Content*/
		$contentType='';
		if($type=='0'){
			$contentType='email_message_content';
		}if($type=='1'){
			$contentType='sms_message_content';
		}
		$getTemplateTags=$this->CI->manageCommonTaskModel->getRecord(TBL_GLOBAL_SETTING_TAMPLATE_TAG,'tag',array('template_type_id'=>$templateType,'active_flag'=>'1'));
		$getTemplate=$this->CI->manageCommonTaskModel->getRecord(TBL_GLOBAL_SETTING_TAMPLATE,$contentType,array('id'=>$templateType,'active_flag'=>'1'));
		$getTemplateString=$getTemplate[$contentType];
		if($getTemplateTags && !empty($getTemplateString)){
			foreach($getTemplateTags as $tag){
				$tagVariable=$tag['tag'];
				if(strpos($getTemplateString,$tagVariable)!==false){
					$variableText=$this->getVariableValue($tagVariable,$userPatientId, $password,$url,'email',$templateType,$resourceId,$reminderData,$appId);
					if($tagVariable=='{{address_1}}' || $tagVariable=='{{address_2}}'){
						if($variableText!=''){
							$getTemplateString=str_replace($tagVariable,$variableText,$getTemplateString);
						}else{
							$getTemplateString=str_replace($tagVariable."<br />",$variableText,$getTemplateString);
						}
					}elseif($tagVariable=='{{city}}'){
						if($variableText!=''){
							$getTemplateString=str_replace($tagVariable,$variableText,$getTemplateString);
						}else{
							$getTemplateString=str_replace($tagVariable.",",$variableText,$getTemplateString);
						}
					}elseif($tagVariable=='{{zip}}'){
						if($variableText!=''){
							$getTemplateString=str_replace($tagVariable,$variableText,$getTemplateString);
						}else{
							$getTemplateString=str_replace("&ndash;".$tagVariable,$variableText,$getTemplateString);
						}
					}elseif($tagVariable=='{{url}}'){
						if($variableText!=''){
							$getTemplateString=str_replace($tagVariable,$variableText,$getTemplateString);
						}else{
							$getTemplateString=str_replace("&ndash;".$tagVariable,$variableText,$getTemplateString);
						}
					}else{
						$getTemplateString=str_replace($tagVariable,$variableText,$getTemplateString);
					}
				}
			}
		}
		return $getTemplateString;
	}
	 
	function getVariableValue($variableName='',$userPatientId='', $password='',$url='',$sendType,$templateType='',$resourceId='',$reminderData,$appId='0')
	{
		if(!empty($variableName)){
			$basicInfo=$this->patientHeaderInformation($userPatientId);
			if($sendType == 'email'){
                if($templateType=='22'){
                    $clinicInfo=$this->CI->manageCommonTaskModel->getRecord('ch_cinfs_clinic_profile','*',array());
                }else{
                   if($resourceId == ''){
						$clinicInfo=$this->CI->manageCommonTaskModel->getRecord('ch_cinfs_clinic_profile','*',array());
				   }else{
					   if($appId){
							$clinicId=$this->CI->manageCommonTaskModel->getRecord('ch_apsch_appointment','*',array('id'=>$appId));
							$clinicInfo=$this->CI->manageCommonTaskModel->getRecord('ch_cinfs_clinic_profile','*',array('id'=>$clinicId['clinic_id']));
					   }else{
							$clinicInfo=$this->getReportSetting($resourceId);
						}
				   }
                }
			}else{
				$clinicInfo=$this->CI->manageCommonTaskModel->getRecord('ch_cinfs_clinic_profile','*',array());
			}

			$getActKeyUserId=$this->CI->manageCommonTaskModel->getRecord(CH_USER_TABLE,'user_id,id,activation_key',array('id'=>$userPatientId));
			$userId=$getActKeyUserId['user_id'];
			$activationKey=$getActKeyUserId['activation_key'];
			
			switch($variableName){
				case '{{first_name}}':
				$variableText=$basicInfo['first_name'];
				break;
				
				case '{{last_name}}':
				$variableText=$basicInfo['last_name'];
				break;
				
				case '{{address_1}}':
				$variableText=$basicInfo['address'];
				break;
				
				case '{{address_2}}':
				$variableText=$basicInfo['correspondence_address'];
				break;
				
				case '{{city}}':
				$variableText=$basicInfo['city'];
				break;
				
				case '{{state}}':
				$variableText=$this->getSpecificFieldFromAnyTable(TBL_STATE,'state_name',$basicInfo['state']);
				break;
				
				case '{{zip}}':
				$variableText=$basicInfo['zip'];
				break;
				
				case '{{telephone}}':
				$variableText=$this->convertPhoneMobileFaxFormat($basicInfo['home_phone']);
				break;
				
				case '{{clinic_name}}':
					if($sendType == 'email'){
						if($appId){
							 $variableText=$clinicInfo['clinic_name'];
						}else{
							$variableText=$clinicInfo->header_line_1." ".$clinicInfo->header_line_2;
						}
                        if($templateType=='22'){
                            $variableText=$clinicInfo['clinic_name'];
                        }
					}else{
						$variableText=$clinicInfo['clinic_name'];
					}
					break;
				
				case '{{clinic_address_1}}':
					if($sendType == 'email'){
                        if($appId){
							 $variableText=$clinicInfo['address_1'];
						}else{
							$variableText=$clinicInfo->address_1;
						}
						if($templateType=='22'){
                            $variableText=$clinicInfo['address_1'];
                        }
					}else{
						$variableText=$clinicInfo['address_1'];
					}
					break;
				
				case '{{clinic_address_2}}':
					if($sendType == 'email'){
                        if($appId){
							 $variableText=$clinicInfo['address_2'];
						}else{
							$variableText=$clinicInfo->address_2;
						}
						if($templateType=='22'){
                            $variableText=$clinicInfo['address_2'];
                        }
					}else{
						$variableText=$clinicInfo['address_2'];
					}
					break;
				
				case '{{clinic_city}}':
					if($sendType == 'email'){
                        if($appId){
							 $variableText=$clinicInfo['city'];
						}else{
							$variableText=$clinicInfo->city;
						}
						if($templateType=='22'){
                            $variableText=$clinicInfo['city'];
                        }
					}else{
						$variableText=$clinicInfo['city'];
					}
					break;
				
				case '{{clinic_state}}':
					if($sendType == 'email'){
                        if($appId){
							 $variableText=$clinicInfo['state'];
						}else{
							$variableText=$clinicInfo->state;
						}
						if($templateType=='22'){
                            $variableText=$clinicInfo['state'];
                        }
					}else{
						$variableText=$clinicInfo['state'];
					}
                    $variableText=$this->getStateNameById($variableText);
					break;
				
				case '{{clinic_zip}}':
					if($sendType == 'email'){
                        if($appId){
							 $variableText=$clinicInfo['zip'];
						}else{
							$variableText=$clinicInfo->zip;
						}
						if($templateType=='22'){
                            $variableText=$clinicInfo['zip'];
                        }
					}else{
						$variableText=$clinicInfo['zip'];
					}
					break;
				
				case '{{clinic_telephone}}':
					if($sendType == 'email'){
                        if($appId){
							 $variableText=$this->convertPhoneMobileFaxFormat($clinicInfo['phone_l']);
						}else{
							$variableText=$this->convertPhoneMobileFaxFormat($clinicInfo->phone_1);
						}
						if($templateType=='22'){
                            $variableText=$this->convertPhoneMobileFaxFormat($clinicInfo['phone_l']);
                        }
					}else{
						$variableText=$this->convertPhoneMobileFaxFormat($clinicInfo['phone_l']);
					}
					break;
				
				case '{{clinic_email}}':
					if($sendType == 'email'){
                        if($appId){
							 $variableText=$clinicInfo['email'];
						}else{
							$variableText=$clinicInfo->email;
						}
						if($templateType=='22'){
                            $variableText=$clinicInfo['email'];
                        }
					}else{
						$variableText=$clinicInfo['email'];
					}
					break;
				
				case '{{clinic_website}}':
					if($sendType == 'email'){
                        if($appId){
							 $variableText=$clinicInfo['website_url'];
						}else{
							$variableText=$clinicInfo->website;
						}
						if($templateType=='22'){
                            $variableText=$clinicInfo['website_url'];
                        }
					}else{
						$variableText=$clinicInfo['website_url'];
					} 
					break;
				
				case '{{pis_api_url}}':
				$variableText="<a href='".PIS_APIURL."'>".trim(PIS_APIURL,'/')."</a>";
				break;
				
				case '{{api_url}}':
				$variableText="<a href='".base_url()."'>".trim(base_url(),'/')."</a>";
				break;
				
				case '{{activation_key}}':
				case '{{user_activation_key}}':
				$variableText=$activationKey;
				break;
				
				case '{{user_id}}':
				$variableText=$userId;
				break;
				
				case '{{password}}':
				$variableText=$password;
				break;
				
				case '{{url_link}}':
				$variableText=$url;
				break;
				
				case '{{activation_link}}':
				$variableText="<a target='_blank' href='".PIS_APIURL."mpis/".$activationKey."/".$userId."'>".trim(PIS_APIURL."mpis/".$activationKey."/".$userId,'/')."</a>";
				break;
				case '{{qr}}':
					$this->CI->load->library('ciqrcode');
					$params['data'] = PIS_APIURL."mpis/".$activationKey."/".$userId;
					$params['level'] = 'H';
					$params['size'] = 5;
					$params['savename'] = 'uploads/qrcodes/'.$activationKey.'_'.$userId.'.png';
					$this->CI->ciqrcode->generate($params);
					$variableText='<img src="'.base_url().$params['savename'].'" />';
				break;
				
				case '{{user_activation_link}}':
				$variableText=BASE_URL."login/login/verifyLogin/".$activationKey."/".$userId;
				break;
				
				case '{{direction}}':
				$variableText='';
				break;
				
				case '{{rule_name}}':
				$ruleTitle=$this->CI->manageCommonTaskModel->getRecord('ch_clinical_rules','rule_title',array('id'=>$reminderData->rule_id));
				$variableText=$ruleTitle['rule_title'];
				break;
				
				case '{{care_suggestions}}':
				$careSuggestMapping=$this->CI->manageCommonTaskModel->getRecord('ch_cds_rule_caresuggestion_mapping','care_suggestion_id',array('rule_id'=>$reminderData->rule_id));
				$variableText='';
				if($careSuggestMapping){
					foreach($careSuggestMapping as $suggestion){
						$suggestionText=$this->CI->manageCommonTaskModel->getRecord('ch_cds_caresuggestion_master','care_suggestion',array('id'=>$suggestion['care_suggestion_id']));
						$variableText.='<br>'.$suggestionText['care_suggestion'];
					}
				}
				break;
				
				case '{{patient_message}}':
				$patientMessage=$this->CI->manageCommonTaskModel->getRecord('ch_clinical_rules','patient_message',array('id'=>$reminderData->rule_id));
				$variableText=$patientMessage['patient_message'];
				break;
				
				default:
				$variableText=$variableName;
				break;
				
			}
			
			if(!empty($variableText)){
				return $variableText;
			}else{
				return false;
			}
		}
	}
	
	function getPatientRaceString($raceIds=null,$codeNameArr=false)
	{
		if($raceIds && $codeNameArr==false){
			$raceArr=explode(",",$raceIds);
			$raceStr='';
			if($raceArr){
				foreach($raceArr as $race){
					$raceStr.=$this->getMasterDataName($race)." and ";
				}
				$raceStr=trim($raceStr);
				$raceStr=trim($raceStr,"and");
			}
			return $raceStr;
		}elseif($raceIds && $codeNameArr==true){
			$raceArr=explode(",",$raceIds);
			$codeNameRaceArr=array();
			if($raceArr){
				foreach($raceArr as $race){
					$raceStr=$this->CI->manageCommonTaskModel->getRecord(TBL_OTHER_MASTER_DATA,'code,title',array('id'=>$race));
					if($raceStr){
						$codeNameRaceArr[$raceStr['code']]=$raceStr['title'];
					}
				}
			}
			return $codeNameRaceArr;
		}
	}
	
	function responsiblePartyDropdown($selectedId=0)
	{
		$result=$this->CI->manageCommonTaskModel->getRecord(TBL_RESPONSIBLE_PARTY_MASTER,'id,first_name,last_name',array('active_flag'=>'1'),array('first_name'=>'asc'),"","","array",1);
		$drop='';
		if($result){
			foreach($result as $resParty){
				$drop.="<option value='".$resParty['id']."' ".((!empty($selectedId) && $selectedId==$resParty['id'])?'selected':'').">".$resParty['first_name'].' '.$resParty['last_name']."</option>";
			}
			echo $dropl;
		}
		return $drop;
	}
	
	function getDateOfVisitByVisitId($visitId=0)
	{
		if(!empty($visitId)){
			$getVisitDate=$this->CI->patientCommonTaskModel->getDateForVisitId(0,$visitId);
			if($getVisitDate){
				return $this->showDateForSpecificTimeZone($getVisitDate);
			}
		}
	}
	
	function updatePatientActivityLog($moduleId=0,$actionType=0,$patientId=0,$accessorId=0)
	{
		if(!empty($moduleId) && !empty($actionType) && !empty($patientId) && !empty($accessorId)){
			$infoArr=array(
						'patient_id'=>$patientId,
						'access_user_id'=>$accessorId,
						'action_type'=>$actionType,
						'module_id'=>$moduleId,
						'added_by'=>$this->CI->session->userdata('id')
					);
			$updateLog=$this->CI->manageCommonTaskModel->insertRecord(TBL_PIS_ACTIVITY_LOG,$infoArr);
			if($updateLog){
				return true;
			}else{
				return false;
			}
		}
	}
	
	function generatePasswordForAuthParty($firstString='',$LastString='',$middleString='')
	{
		$password='';
		if(!empty($firstString) && !empty($LastString) && !empty($middleString)){
			$password=substr($firstString,0,1);
			$password.=substr(md5($middleString),rand(1,10),6);
			$password.=substr($LastString,0,1);
		}
		return strtolower($password);
	}
	
	function generateUsernameForAuthParty($firstString='',$LastString='')
	{
		$username='';
		if(!empty($firstString) && !empty($LastString)){
			$username=substr($firstString,0,1);
			$username.=rand(100000,999999);
			$username.=substr($LastString,0,1);
		}
		$check=$this->CI->manageCommonTaskModel->getRecord(TBL_PAT_AUTH_MASTER,'id',array('username'=>$username));
		if($check){
			$this->generateUsernameForAuthParty($firstString='',$LastString='');
		}else{
			return strtolower($username);
		}
	}
	
	function nextVisit($nextVisitId)
	{
		$get=$this->CI->manageCommonTaskModel->getRecord(TBL_NEXT_VISIT_MASTER,'visit_value,visit_text',array('id'=>$nextVisitId));
		//$startDate=date('Y-m-d');
		//$date=date('Y-m-d',strtotime('+'. $get['visit_value'].'day',(strtotime($startDate))));
		//return $this->showDateForSpecificTimeZone($date);
		return $get['visit_text'];
	}
	
	/*************************************************************************
	*	Function Name		createActivityLogMessageString
	*	Author				Prashant Jain
	*	Parm				$moduleName : Name of the module
	*						$itemId		: Id of particulear item which is going to 
	*									  be Added/changed/deleted.
	*						$actionId	:1-Add, 
										 2-Select,
										 3-Delete, 
										 4-Update, 
										 5-Login, 
										 6-Logout
										 7-Download,
										 8-Print
										 9-Email
										 10-Fax
	**************************************************************************/
	 
	function createActivityLogMessageString($moduleName='',$itemId='',$actionId='',$extraInfo=null)
	{
		$message='';
		if(!empty($moduleName) && !empty($actionId)){
			if($actionId==1){
				$message.="Recorded";
			}elseif($actionId==2){
				$message.="Accessed";
			}elseif($actionId==3){
				$message.="Deleted";
			}elseif($actionId==4){
				$message.="Changed";
			}elseif($actionId==7){
				$message.="Downloaded";
			}elseif($actionId==8){
				$message.="Printed";
			}elseif($actionId==9){
				$message.="Emailed";
			}elseif($actionId==10){
				$message.="Faxed";
			}elseif($actionId==11){
				$message.="Transmitted";
			}
			switch($moduleName){
				case 'diagnosis':
					$table=TBL_DISEASES_MASTER;
					$selectFields='code as itemCode,diseases_name as itemName';
					$message.=' <b>Patient Problem </b>';
				break;
				
				case 'congFun':
					$table=TBL_DISEASES_MASTER;
					$selectFields='code as itemCode,diseases_name as itemName';
					$message.=' <b>Patient Cognitive/Functional Status </b>';
				break;
								
				case 'allergies':
					if(isset($extraInfo['allergyType']) && $extraInfo['allergyType']==1){
						$table=TBL_EDRUG_DRUG_MASTER;
						$selectFields='concat(\'Drug\') as itemCode,drug_name as itemName';
					}elseif(isset($extraInfo['allergyType']) && $extraInfo['allergyType']==2){
						$table=TBL_GROUP_ALR_MASTER;
						$selectFields='concat(\'Group\') as itemCode,group_allergy_name as itemName';
					}elseif(isset($extraInfo['allergyType']) && ($extraInfo['allergyType']==44 || $extraInfo['allergyType']==45)){
						$table=TBL_OTHER_MASTER_DATA;
						$selectFields='if(master_type=44,\'Food\',\'Environment\') as itemCode,title as itemName';
					}
					$message.=' <b>Patient Allergies </b>';
				break;
				
				case 'immunization':
					$table=TBL_VACCINES_MASTER;
					$selectFields='vac_gen_name as itemName';
					$message.=' <b>Patient Immunization & Shots </b>';
				break;
				
				case 'labOrder':
					if(isset($extraInfo['cpt_code_type']) && isset($extraInfo['base_table_id'])){
						if($extraInfo['cpt_code_type']=='3' && $extraInfo['base_table_id']=='1'){
							$table=TBL_LOINC_MASTER;
							$selectFields='loinc_code as itemCode,loinc_name as itemName';
						}else{
							$table=TBL_CLMDT_PROCEDURE_CODE;
							$selectFields='(select group_concat(proc_code) 
											from '.TBL_PROC_CODE_SPC.' 
											where code_type='.$extraInfo['cpt_code_type'].' 
											and procedure_id='.$itemId.') as itemCode, 
											procedure_name as itemName';
						}
					}
					$message.=' <b>Patient Lab Orders </b>';
				break;
				
				case 'labResult':
					$table=TBL_PATIENT_LAB_RESULT;
					$selectFields='cpt_code as itemCode,procedure_name as itemName';
					$message.=' <b>Patient Lab Results </b>';
				break;
				
				case 'labRecon':
					$table=TBL_PATIENT_LAB_RECON;
					$selectFields='cpt_code as itemCode,procedure_name as itemName';
					$message.=' <b>Patient Lab Reconciliation </b>';
				break;
				
				case 'prescription':
					$table=TBL_EDRUG_DRUG_MASTER;
					$medStrengthId='';
					if(isset($extraInfo['medStrengthId'])){
						$medStrengthId=$extraInfo['medStrengthId'];
					}
					$selectFields='drug_name as itemCode,(select dosages_details 
														from '.TBL_EDRUG_DRUG_DOSAGES_MASTER.' 
														where id='.$medStrengthId.') as itemName';
					$message.=' <b>Patient Prescription </b>';
				break;
				
				case 'phy_exam':
				$message.=' <b>Patient Physical Examination </b>';
				break;
				
				case 'patient_plan':
				$message.=' <b>Patient Plan </b>';
				break;
				
				case 'appointments':
				$message.=' <b>Patient Appointment </b>';
				break;
				
				case 'billing':
				$message.=" <b>Patient Bill</b>";
				break;
				
				case 'commonRecon':
				$message.=" <b>Common Reconciliation</b>";
				break;
				
			}
			if(isset($extraInfo['customLogString']) && !empty($extraInfo['customLogString'])){
				$message.="&nbsp;".$extraInfo['customLogString'];
			}
			$condArr=array('id'=>$itemId);
			if(!empty($itemId)){
                
                if($actionId=="4"){
                    $message.="<b>(Original-</b>";
                } else {
                    $message.="<b>(</b>";
                }
				
				$getMessage=$this->CI->manageCommonTaskModel->getRecord($table,$selectFields,$condArr);
				if($getMessage){
					if(isset($getMessage['itemCode'])){
						$message.=$getMessage['itemCode'].": ";
					}
					if(isset($getMessage['itemName'])){
						$message.=$getMessage['itemName'];
					}
					trim($message);
					trim($message,': ');
					trim($message);
				}
				$message.="<b>)</b>";
			}
		}
		return $message;
	}
	

	function getDovVisitId($visitId=0)
	{
		if(!empty($visitId)){
			$getVisitDate=$this->CI->manageCommonTaskModel->getDateForVisitId(0,$visitId);
			if($getVisitDate){
				return ($getVisitDate);
			}
		}
	}
	function getPatAuthRep($patientId,$mode='')
	{
		$get=$this->CI->manageCommonTaskModel->getPatAuthRep($patientId);
		if($mode=='intra'){
			return $get['id'];
		}else{
			if($get['first_name'] || $get['last_name']){
				return $get['first_name'].' '.$get['last_name'];
			}else{
				return false;
			}
		}
	}
	
	
	/**
     * This function will be used to send the External Email ( Secure Email/ Normal Email).
     * 
	 * @author 	Abhishek Singh<abhishek.singh@greenapplestech.com>
	 * @param	int $senderId Sender id
	 * @param	string $senderEmail Sender Email Id
	 * @param	string $to Recipient Email Id (Comma Separated)
	 * @param	string $ccAddress CC Email Address (Comma Separated)
	 * @param	string $bccAddredss Bcc Email Address (Comma Separated)
	 * @param	string $subject Mail Subject
	 * @param	string $messageContent Mail Content
	 * @param	string $uploadedFile Attached Files Name (Comma Separated) [ All files should be uploaded in Uploads folder ]
	 * @param	string $directFlag 1->Direct/Secure Email,0->External Email		 
     * @return 	string Success/Error.
     */
    function sendExternalMailMessage($senderId='',$senderEmail='',$to='',$ccAddress='',$bccAddredss='',$subject,$messageContent,$uploadedFile,$directFlag='1',$patientId=0)
	{
		$CI =& get_instance();
		$this->CI->load->library('phimail');
		// Sender Id, Sender Email and To Email is mandatory without these three we will not entertain Mail.
		if($senderId =='' || $senderEmail=='' || $to=='' ) return false;
		//let's get the post variable
		$messageId=0;
		$mailAttachmentAll=array();
		$toSendEmail=array();
		$ccSendEmail=array();
		$bccSendEmail=array();
		$to=trim($to,","); // Comma Separated multiple list.
		
		if($directFlag=='1'){
		// Not entertaining CC and BCC in Direct Mail Messaging(Secure Messaging).
			$ccAddress='';
			$bccAddredss='';
		}else{
			$ccAddress=trim($ccAddress,","); // Comma Separated multiple list.
			$bccAddredss=trim($bccAddredss,","); // Comma Separated multiple list.
		}
		//$subject=$subject;
		//$messageContent=$messageContent;
		$messageStatus= '1';
		
		// Checking It is direct mail or External One.
		if($directFlag=='1')
			$isDirectMail='1';
		else
			$isDirectMail='0';
			
		// Getting uploaded file array.
		$mailAttachment=trim($uploadedFile,",");
		if(empty($patientId)){
			$patientId=0;
		}
		$mainMessageInfo=array(		
							'parent_id'=>0,
							'attached_patient'=>$patientId,
							'user_id'=>$senderId,
							'subject'=>$subject,
							'content'=>$messageContent,
							'message_status'=>$messageStatus,
							'active_flag'=>'1',
							'msg_compose_type'=>'1',
							'is_direct_mail'=>$isDirectMail,
							'date_added'=>date('Y-m-d H:i:s'),
							'added_by'=>$CI->session->userdata('id')
							);
			
		if($to!=''){
			$to=array_flip(explode(",",$to));
			$toSendEmail=array_flip($to);
		}
		if($ccAddress!=''){
			$ccAddress=array_flip(explode(",",$ccAddress));
			$ccSendEmail=array_flip($ccAddress);
		}
		if($bccAddredss!=''){
			$bccAddredss=array_flip(explode(",",$bccAddredss));
			$bccSendEmail=array_flip($bccAddredss);
		}
			
		
		//NOw Send the email
		if($mailAttachment!=''){
			$mailAttachmentAll=explode(",",$mailAttachment);
		}
		if($directFlag=='1'){
			// Call Direct Mail Function Here.
			$prefix ="./uploads/";
			
			function text_alter(&$item1, $key, $prefix)
			{
				$item1 = "$prefix$item1";
			}
			if($mailAttachment!=''){
				array_walk($mailAttachmentAll, 'text_alter', $prefix);
			}

			$chkStatus = $CI->phimail->phiMailOutgoing($toSendEmail,$senderEmail,$subject,$messageContent,$mailAttachmentAll);
			if($chkStatus['status']){
				$mailMsgId=$this->saveExternalEmailMessage($mainMessageInfo,$to,$ccAddress,$bccAddredss,$senderId,$mailAttachment,false);
		
				$log_message = $CI->lang->line('externalMailSentSuccess');
				$log_comment = 'Secure Email Sent Successfully';
				$statusReturn['status'] = true;
				$CI->my_log->generateLog(1,'9','Sent Secure Email',$log_comment,1, $patientId, 'comm');
				return $statusReturn;
			}else{
				$log_message = $CI->lang->line('externalMailSentError');
				$log_comment = $chkStatus['error'];
				$statusReturn['status'] = false;
				$statusReturn['error'] = $chkStatus['error'];
				$CI->my_log->generateLog(2,'9','Sending Secure Email Failed',$log_comment,1, $patientId, 'comm');
				return $statusReturn;
			}	

		}else{
			$chkStatus=$this->sendEmail($senderEmail,$toSendEmail,$ccSendEmail,$bccSendEmail,$subject,$messageContent,$mailAttachmentAll,'uploads',false,'html');
			if($chkStatus){
				$mailMsgId=$this->saveExternalEmailMessage($mainMessageInfo,$to,$ccAddress,$bccAddredss,$senderId,$mailAttachment,false);
		
				$log_message = $CI->lang->line('externalMailSentSuccess');
				$log_comment = '';
				$statusReturn['status'] = true;
				$CI->my_log->generateLog(1,'1','Sent External Email',$log_comment,1, $patientId, 'comm');
				return $statusReturn;
			}else{
				$log_message = $CI->lang->line('externalMailSentError');
				$log_comment = '';
				$statusReturn['status'] = false;
				$statusReturn['error'] = 'Sending External Email Failed';
				$CI->my_log->generateLog(2,'1','Sending External Email Failed',$log_comment,1, $patientId, 'comm');
				return $statusReturn;
			}	
		}		
	}
	
 	
	/**
     * This function will be used to Save the External Email ( Secure Email/ Normal Email).
     * 
	 * @author 	Abhishek Singh<abhishek.singh@greenapplestech.com>
	 * @param	array $mainMessageInfo Message Array
	 * @param	string $to Recipient Email Id (Comma Separated)
	 * @param	string $ccAddress CC Email Address (Comma Separated)
	 * @param	string $bccAddredss Bcc Email Address (Comma Separated)
	 * @param	int $senderId Sender Id
	 * @param	string $mailAttachment attachments string(Comma Separated files name)
     * @return 	int/false Message Id or False.
     */
	function saveExternalEmailMessage($mainMessageInfo=array(),$to,$ccAddress,$bccAddredss,$senderId,$mailAttachment='',$fromExtracting=true)
	{
		$CI =& get_instance();
		$CI->load->model('communication/'.$CI->session->userdata('userMainType').'/manageExternalEmailInformationModel');
		if(count($mainMessageInfo)<=0){
			return;
		}
		//let's get the post variable
		$messageId=0;
		
		$messageId=$CI->manageExternalEmailInformationModel->saveEmailMessage($mainMessageInfo);

		if($messageId){//So We have got the message id now save the sender receiver mapping
		//Send Email for To address
			if(isset($to) && $to!=''){
				foreach($to as $toKey=>$singleToAddress)
				{
					$sendDataTime=date('Y-m-d H:i:s');
					$reciever=$toKey;

					$senderRecieverInfo=array(	'msg_id'=>$messageId,
												'sender_id'=>$senderId,
												'receiver_id'=>$reciever,
												'sender_delete'=>'0',
												'receiver_delete'=>'0',
												'send_date_time'=>$sendDataTime,
												'read_unread'=>'0',
												'msg_receiving_type'=>'0',
												'active_flag'=>'1',
												'added_by'=>$CI->session->userdata('id'));

					$checkStatus=$CI->manageExternalEmailInformationModel->saveEmailMessageForSenderReciever($senderRecieverInfo);					
				}
			}else{
				echo "I am Not in too section";
			}
			
			//Send Email for CC address
			if(isset($ccAddress) && $ccAddress!=''){
				foreach($ccAddress as $ccKey=>$singleToAddress)
				{
					if($fromExtracting){
						$reciever=$CI->manageExternalEmailInformationModel->getUniqueIdByEmailAddress($ccKey);
						if(!$reciever && !isset($reciever)){
							/*We don't have a reciever thne what to do.
							*/
							continue;
						}
					}else{
						$reciever=$ccKey;
					}
					$senderRecieverInfo=array(	'msg_id'=>$messageId,
												'sender_id'=>$senderId,
												'receiver_id'=>$reciever,
												'sender_delete'=>'0',
												'receiver_delete'=>'0',
												'msg_receiving_type'=>'1',
												'send_date_time'=>$sendDataTime,
												'read_unread'=>'0',
												'active_flag'=>'1',
												'added_by'=>$CI->session->userdata('id'));
					$CI->manageExternalEmailInformationModel->saveEmailMessageForSenderReciever($senderRecieverInfo);
				}
			}
			//Send Email for CC address

			if(isset($bccAddredss) && $bccAddredss!=''){
				foreach($bccAddredss as $bccAddressKey=>$singleToAddress)
				{
					if($fromExtracting){
						$reciever=$CI->manageExternalEmailInformationModel->getUniqueIdByEmailAddress($bccAddressKey);
						if(!$reciever && !isset($reciever)){
							/*We don't have a reciever thne what to do.
							*/
							continue;
						}
					}else{
							$reciever=$bccAddressKey;
					}
					$senderRecieverInfo=array(	'msg_id'=>$messageId,
												'sender_id'=>$senderId,
												'receiver_id'=>$reciever,
												'sender_delete'=>'0',
												'receiver_delete'=>'0',
												'msg_receiving_type'=>'2',
												'send_date_time'=>$sendDataTime,
												'read_unread'=>'0',
												'active_flag'=>'1',
												'added_by'=>$CI->session->userdata('id'));
					$CI->manageExternalEmailInformationModel->saveEmailMessageForSenderReciever($senderRecieverInfo);
				}
			}
				
				
			//Send Attachment if there are any
			if($mailAttachment && $mailAttachment!=''){
				$attachmentInfo=array(	'msg_id'=>$messageId,
											'attachment_name'=>$mailAttachment,
											'attachment_type'=>'xx',
											'attachment_date'=>$sendDataTime,
											'added_by'=>$CI->session->userdata('id'));
				$checkStatus=$CI->manageExternalEmailInformationModel->saveMailAttachment($attachmentInfo);

			}					
			if($messageId){
				return $messageId;
			}else{
				return false;
			}			
		}
    }

	function sendPatientData($dataArray, $tableName){

		$tableRecord = array();
		$tableRecord['userName'] = PIS_USER_NAME;
		$tableRecord['pass'] = md5(PIS_USER_PASS);
		$tableRecord['machineHostName'] = gethostbyname(gethostname());
		$tableRecord['dbVerDoctorPortal']=$this->getDatabaseVersion();
		$tableRecord['dbHostName'] = CH_DB_HOST;
		$tableRecord['dbDatabaseName'] = CH_DB_NAME;
		$tableRecord['tableName'] = $tableName;
		$tableRecord['timestampField'] = '';
		$tableRecord['RecordCount'] = count($dataArray);
		$tableRecord['data'] = $dataArray;
		
		$inputData = 'postinputdata='.json_encode($tableRecord);
		$inputData=$this->encodeSpecialCharacter($inputData);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, PIS_APIURL."webservice.php");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $inputData);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		
		$data = curl_exec($ch);
		$dataArray = json_decode($data, true);

		return $dataArray;
	}

	function getDatabaseVersion()
	{
		$query="SELECT `version`
				FROM `ch_version`
				WHERE `id`=(select max(id) from `ch_version`) 
				";
		$resultdata=mysqli_query($this->CI->db->conn_id, $query);
		if(mysqli_num_rows($resultdata)>0){
			while($row=mysqli_fetch_assoc($resultdata)){
				$dbVersion=$row['version'];
				break;
			}
			if(!empty($dbVersion)){
				return $dbVersion;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	function encodeSpecialCharacter($inputData)
	{
		$inputData=str_replace('&','__AND__',$inputData);
		$inputData=str_replace(':','__COLON__',$inputData);
		$inputData=str_replace('?','__QUESTION__',$inputData);
		return $inputData;
	}
	
	function getSecureEmailDetail($userId)
	{
		if($userId){
			$CI =& get_instance();
			$CI->load->model('common/manageCommonTaskModel');	
			return $CI->manageCommonTaskModel->getSecureEmailDetail($userId);
		}else{
			return false;
		}
	}
	/*
	*	function :		removeCl
	*	Description:	Remove or add class based on certain condation (AMC)
	*/
	function removeCl($chld,$ignoreFor='')
	{
		if($ignoreFor=='170.314b2') return '';
		if(count($chld)==3){
			return 'mrgn-lbl-nw';
		}else{
			return '';
		}
	}
	
	function decToFraction($float) {
		// 1/2, 1/4, 1/8, 1/16, 1/3 ,2/3, 3/4, 3/8, 5/8, 7/8, 3/16, 5/16, 7/16,
		// 9/16, 11/16, 13/16, 15/16
		$whole = floor ( $float );
		$decimal = $float - $whole;
		$leastCommonDenom = 48; // 16 * 3;
		$denominators = array (2, 3, 4, 8, 16, 24, 48 );
		$roundedDecimal = round ( $decimal * $leastCommonDenom ) / $leastCommonDenom;
		if ($roundedDecimal == 0)
			return $whole;
		if ($roundedDecimal == 1)
			return $whole + 1;
		foreach ( $denominators as $d ) {
			if ($roundedDecimal * $d == floor ( $roundedDecimal * $d )) {
				$denom = $d;
				break;
			}
		}
		return ($whole == 0 ? '' : $whole) . " " . ($roundedDecimal * $denom) . "/" . $denom;
	}
		
	function getUserNameAddressContactStr($userId=0,$fieldIdsStr='*')
	{
		if(!empty($userId)){
			$getProviderInfo=$this->CI->manageCommonTaskModel->getSelectedProfileFields($userId,$fieldIdsStr);
			if($getProviderInfo && count($getProviderInfo)!=0){
				$guardianFirstName=(isset($getProviderInfo[3])?$getProviderInfo[3]:'');
				$guardianLastName=(isset($getProviderInfo[5])?$getProviderInfo[5]:'');
				$guardianAdd1=(isset($getProviderInfo[7])?$getProviderInfo[7]:'');
				$guardianAdd2=(isset($getProviderInfo[8])?$getProviderInfo[8]:'');
				$guardianCity=(isset($getProviderInfo[80])?$getProviderInfo[80]:'');
				$guardianState=(isset($getProviderInfo[81])?$this->getSpecificFieldFromAnyTable(TBL_STATE,'state_name',$getProviderInfo[81]):'');
				$guardianCountry=(isset($getProviderInfo[83])?$this->getSpecificFieldFromAnyTable(TBL_COUNTRY,'country_name',$getProviderInfo[83]):'');
				
				$guardianZip=(isset($getProviderInfo[82])?$getProviderInfo[82]:'');
				$guardianPhone=(isset($getProviderInfo[11])?$this->convertPhoneMobileFaxFormat($getProviderInfo[11]):'');
				$patientAddress=$guardianFirstName." ".$guardianLastName."<br/>";
				
				if(!empty($guardianAdd1)){
					$patientAddress.=$guardianAdd1.",";
				}
				if(!empty($guardianAdd2)){
					$patientAddress.=$guardianAdd2."<br/>";
				}
				if(!empty($guardianCity)){
					$patientAddress.=$guardianCity;
				}
				if(!empty($guardianState)){
					$patientAddress.="(".$guardianState."),";
				}
				if(!empty($guardianCountry)){
					$patientAddress.=$guardianCountry;
				}
				if(!empty($guardianZip)){
					$patientAddress.="-".$guardianZip;
				}
				$patientAddress=str_replace(',,',',',$patientAddress);
				$patientAddress=trim($patientAddress,',');
				$patientAddress.="<br/>Tel :".$guardianPhone;
				return $patientAddress;
			}
		}
	}


	function getUserNameAddressContactStrRef($userId=0,$userType)
	{
		if(!empty($userId)){
			$refDoctor=$this->CI->manageCommonTaskModel->getRecord('ch_cinfs_referring_doctor','*',array('id'=>$userId));
			if(!empty($refDoctor['doctor_first_name'])){
				$patientAddress.=$this->xmlStringParser($refDoctor['doctor_first_name']);
			}
			if(!empty($refDoctor['doctor_last_name'])){
				$patientAddress.=" ".$this->xmlStringParser($refDoctor['doctor_last_name']);
			}
			$patientAddress.="<br/>";
			
			if(!empty($refDoctor['address_1'])){
				$patientAddress.=$this->xmlStringParser($refDoctor['address_1']).",";
			}
			if(!empty($refDoctor['address_2'])){
				$patientAddress.=$this->xmlStringParser($refDoctor['address_2']).",";
			}
			if(!empty($refDoctor['city'])){
				$patientAddress.=$this->xmlStringParser($refDoctor['city']).",";
			}
			
			if(!empty($refDoctor['state'])){
				$patientAddress.=$this->xmlStringParser($this->getSpecificFieldFromAnyTable(TBL_STATE,'state_name',$refDoctor['state'])).",";
				
			}
			if(!empty($refDoctor['country'])){
				$guardianCountry=$this->xmlStringParser($this->getSpecificFieldFromAnyTable(TBL_COUNTRY,'country_name',$refDoctor['country'])).",";
			}
			
			$patientAddress=trim($patientAddress,',');
			$patientAddress.="<br/>Tel :";
			if(!empty($refDoctor['phone_l'])){
				$patientAddress.=$this->xmlStringParser($refDoctor['phone_l']);
			}
			return $patientAddress;
				
			
		}
	}


	/*CQM important Functions Dont change and use .otherwise this will destroy complete CQM....*/
	
	/*
	*	Function 	convertAndRemoveEmpty
	*	Author		Rahul
	*	Description	Just check the empty vaslue in array and remove those...
	*
	*/
	function convertAndRemoveEmpty($chkVal=0)
	{
			if($chkVal==0 && !is_array($chkVal)){
				//means we have found the blank value so we will have to give the blank array for this becuase we are using the count function for count the record in this array and count will consider length 1 if we pass the 0 in to that...
				return array();
			}
			return $chkVal;
	}
	/*
	*	Function 	countMine
	*	Author		Rahul
	*	Description	Just add some more functinality in the php count function...
	*
	*/
	function countMine($countArr=array())
	{
		//ok here we are to check that incoming value is array or not becuase php count function return length 1 incase we pass the 0 in to that...
		if(is_array($countArr)){
			//ok we have found the arryt thn implement the count for like php function...
			//one more time check that this do we have some not null value in this array..
			foreach($countArr as $key=>$singleVal){
				//ok now check for the blank value for this array..
				if(empty($singleVal)){
					unset($countArr[$key]);//just unset the value..
				}
			}
			return count($countArr);//return simple count...
			
		}else{
			return 0;
		}
	}
	
	function getMedicationValUnit($medStrength='')
	{
			$medVal="";
			$medUnit="";
			$optVal=array();
			$medStrength=str_replace('(','&#',$medStrength);
			$medStrength=str_replace(')','$#',$medStrength);
		
			
			if(preg_match("[ /]", $medStrength)){
				$optVal=explode(' /',$medStrength);
			}elseif(preg_match("( &#)",$medStrength)){
				$optVal=explode(" &#",$medStrength);
			}
			
			
			
			if((isset($optVal[0]))&&(preg_match("[/]", $optVal[0]))){
				$medUnit=str_replace(',','',$optVal[0]);
					
			}else{
				$medUnit=$medStrength;
					if(preg_match("[%]", $optVal[0])&& (preg_match("[/]", $optVal[1]) || preg_match("[ ]", $optVal[1]))){
						$medUnit = str_replace('&#','',$optVal[1]);
						$medUnit = str_replace('$#','',$optVal[1]);
						//$medUnit = "%"; 
					}else{
						if(preg_match("[%]", $optVal[0])){
							$medVal = str_replace('%','',$optVal[0]);
							$medUnit = "%"; 
						}
					}
					
					if(preg_match("[%]", $medUnit)){
						$medVal = str_replace('%','',$medUnit);
						$medUnit = "%"; 
					}
				if(preg_match("[-]", $medUnit)){
					$firstMedVal = explode('-',$medStrength);
					$medVal = $firstMedVal[0];
					for($i=1;$i<count($firstMedVal);$i++){
						if(preg_match("[ ]", $firstMedVal[$i])){
							$unitVal=explode(' ',$firstMedVal[$i]);
						}
					}
					
						//$medUnit=$medVal.' '.$unitVal[1];
						$medUnit=$unitVal[1];
				}
				
			}
			
			
			if(preg_match("[/]", $medUnit) && preg_match("[ ]", $medUnit)){
				$medUnitVal=explode(' ',$medUnit);
				$medVal = $medUnitVal[0];
				if(count($medUnitVal)>=3){
					$medUnit=$medUnitVal[1].$medUnitVal[2];
				}else{
				$medUnit = $medUnitVal[1];
				}
				
			}
				
			if((!empty($medVal))&&($medVal!="")){
				$medVal = preg_replace("([a-zA-Z%-]+)", "", $medVal);
				$medUnit=$medVal.' '.$medUnit;
			}
			
			$medUnit=str_replace('&#','(',$medUnit);
			$medUnit=str_replace('$#',')',$medUnit);
			return $medUnit;
			
	}
	
	function getLabResultUnit($labUnit='')
	{
			$allUnit = array("mm/h",
							 "g/mL",
							 "%",
							 "fL",
							 "g/dL",
							 "mg/dL",
							 "ug/mL",
							 "mmol/L"
							);
							
						if(in_array($labUnit,$allUnit)){
							$labResultUnit = $labUnit;
						}else{
							$labResultUnit = "";
						}						
						return $labResultUnit;
	}
	
	
	/**
    * function		generateChartNumber
    * author     	Anshul Agarwal <anshul.agarwal@greenapplestech.com>
	* Mod			Rahul anand
    * Description	It is used to generate the chart number.
    *
    * Parameters
    *	$param - None 
    *	Return	- None
    **/
    function generateChartNumber($return=false)
    {
		/** Update chart_int value with chart_number **/
		$updateChartInt="update ch_uaprm_patient_profile_data set chart_int=chart_number ";
		$queryResult=$this->CI->manageCommonTaskModel->excecuteQuery($updateChartInt);
		
        $startChartNumber=0;
		$startChartNumber=$this->CI->manageCommonTaskModel->getGlobalSettings('1','ch_chart_number_start_number');
        
		if(!empty($startChartNumber['ch_chart_number_start_number'])){
            $startChartNumber=$startChartNumber['ch_chart_number_start_number'];
        } else {
            $startChartNumber=0;
        }
		//echo $startChartNumber;die;

		$maxRecord=$this->CI->manageCommonTaskModel->getMaxChartNumber($startChartNumber);
		
        if((isset($maxRecord['maxChartNumber']))&&(!empty($maxRecord['maxChartNumber']))){
			$infoToBeInserted=array('ch_chart_number_start_number'=>$maxRecord['maxChartNumber']);
			$this->CI->manageCommonTaskModel->setGlobalSettings(1, $infoToBeInserted);					
            if($return==true){
				return $maxRecord['maxChartNumber'];
			}else{
				echo $maxRecord['maxChartNumber'];die;
			}
        }  else {
			$infoToBeInserted=array('ch_chart_number_start_number'=>$startChartNumber+1);
			$this->CI->manageCommonTaskModel->setGlobalSettings(1, $infoToBeInserted);						
			if($return==true){
				return $startChartNumber+1;
			}else{
				echo $startChartNumber+1; die;
			}
        }
    }
	
	function generateCustomizeChartNumber($firstName,$lastName,$manualChart='')
	{
		$nameSetting=$this->getGlobalSettings('1', 'ch_chart_number_format');
		$nameFormat=$nameSetting['ch_chart_number_format'];
		if($nameFormat=='lf'){
			$finalFname=$lastName;
			$finalLname=$firstName;
		}else if($nameFormat=='fl'){
			$finalFname=$firstName;
			$finalLname=$lastName;
		}
		
		// get chart number gobal setting...
		$getFnameLength= $this->getGlobalSettings('1', 'ch_chart_number_first_name_length');
		$fnameLength=$getFnameLength['ch_chart_number_first_name_length'];
		if(empty($fnameLength)){
			$fnameLength=1;
		}
		
		$getLastnameLength= $this->getGlobalSettings('1', 'ch_chart_number_last_name_length');
		$lastnameLength=$getLastnameLength['ch_chart_number_last_name_length'];
		
		if(empty($lastnameLength)){
			$lastnameLength=1;
		}
		$getLength= $this->getGlobalSettings('1', 'ch_chart_number_total_length');
		$length=$getLength['ch_chart_number_total_length'];
		if(empty($length)){
			$length=8;
		}
		
		$finalChart='';
		$firstLastString ='';
		if(!empty($finalFname)){
			if($fnameLength=='1'){
				$firstLastString.=substr($finalFname, 0, 1);
			}elseif($fnameLength=='3'){
							
				if(strlen($finalFname) >=3)
					$firstLastString.=substr($finalFname, 0, 3);
				else
					$firstLastString.=str_pad($finalFname, 3, $finalFname, STR_PAD_RIGHT);
											
			}else{
				$firstLastString.=substr($finalFname, 0, 1);
			}
		}                              
		if(!empty($finalLname)){
			if($fnameLength=='1'){
				$firstLastString.=substr($finalLname, 0, 1);
			}elseif($fnameLength=='3'){
				if(strlen($finalLname) >=2)
					$firstLastString.=substr($finalLname, 0, 2);
				else
					$firstLastString.=str_pad($finalLname, 2, $finalLname, STR_PAD_RIGHT);
			}else{
				$firstLastString.=substr($finalLname, 0, 1);
			}
		}
		if($manualChart==1){
			//it is used for create the patient  and genrate the chart number maunal and while setting is custom...
			return strtoupper($firstLastString);
		}
		$chartExistArray =$this->searchChartnumberByLike($firstLastString);
		if($chartExistArray){  
                    foreach($chartExistArray as $chartExistBlock){
                        $chartExist=$chartExistBlock['chart_number'];
			if($fnameLength=='1'){
				$incrString =substr($chartExist, 2, 7);
				$incrStripString = ltrim($incrString, '0');
				$incrFinalString = $incrStripString+1;
				$finalIntString =  str_pad($incrFinalString, 6, "0", STR_PAD_LEFT);
			}elseif($fnameLength=='3'){
				$incrString =substr($chartExist, 5, 7);
				$incrStripString = ltrim($incrString, '0');
				$incrFinalString = $incrStripString+1;
				$finalIntString =  str_pad($incrFinalString, 3, "0", STR_PAD_LEFT);
			}else{
				$incrString =substr($chartExist, 2, 7);
				$incrStripString = ltrim($incrString, '0');
				$incrFinalString = $incrStripString+1;
				$finalIntString =  str_pad($incrFinalString, 6, "0", STR_PAD_LEFT);
			}
                        $finalChart=$firstLastString.$finalIntString;
                        $chartNumExist=$this->CI->manageCommonTaskModel->getRecord("ch_uaprm_patient_profile_data","count(*) as num",array('chart_number'=>strtoupper($finalChart)));
                        if($chartNumExist['num'] == 0){
                            break;
                        }
                    }
		}else{
			if($fnameLength=='1'){
				$incrFinalString = '1';
				$finalIntString =  str_pad($incrFinalString, 6, "0", STR_PAD_LEFT);
			}elseif($fnameLength=='3'){
				$incrFinalString = '1';
				$finalIntString =  str_pad($incrFinalString, 3, "0", STR_PAD_LEFT);
			}else{
				$incrFinalString = '1';
				$finalIntString =  str_pad($incrFinalString, 6, "0", STR_PAD_LEFT);
			}
                        $finalChart=$firstLastString.$finalIntString;
		}
		
		
		return  strtoupper($finalChart);
	}
	
	function getMaxlengthCustomizedChartNo()
	{
		$getFnameLength= $this->getGlobalSettings('1', 'ch_chart_number_first_name_length');
		$fnameLength=$getFnameLength['ch_chart_number_first_name_length'];
		if(empty($fnameLength)){
			$fnameLength=1;
		}
		
		$getLnameLength= $this->getGlobalSettings('1', 'ch_chart_number_last_name_length');
		$lnameLength=$getLnameLength['ch_chart_number_last_name_length'];
		
		if(empty($lnameLength)){
			$lnameLength=1;
		}
		
		$getLength= $this->getGlobalSettings('1', 'ch_chart_number_total_length');
		$length=$getLength['ch_chart_number_total_length'];
		if(empty($length)){
			$length=8;
		}
		
		if($fnameLength > 2){
			$lnameLength=2;
		}
		return $inputMaxLength = $length-($fnameLength+$lnameLength);
	}

	function searchChartnumberByLike($searchString='')
	{
		if($searchString!=''){   
			return $this->CI->manageCommonTaskModel->getChartNo($searchString);
		}else{
			return false;
		}
	}
	
	function checkExistencyForMaster($patientId='',$catIdArr='',$visitId=''){
		$rosValueMaster= $this->CI->manageCommonTaskModel->getRecord('ch_ptcld_ros',"ros_value",array('patient_id'=>$patientId,'visit_id'=>$visitId,'ros_id'=>$catIdArr));
		return $rosValueMaster;
	}

	
	function generateProviderOrderDropDown($providerId,$catId,$procCodeType, $patientId = 0,$facilityId=0,$facOrderType='0') {
		$this->CI->load->model('common/manageCommonTaskModel');
        $OrderData = $this->CI->manageCommonTaskModel->getProviderOrder($providerId, $catId, $procCodeType, $patientId,$facilityId,$facOrderType);
        $data_str ='<option data-placeholder="Select from preferred list"></option>';
		if ($OrderData) {
            foreach ($OrderData as $dataRow) {
			if (!empty($dataRow['last_date']))
					$procName = " (".$dataRow['abbr'].") ". $dataRow['procedureName'] . " (" . $dataRow['last_date'] . ")";
				else
					$procName = " (".$dataRow['abbr'].") ". $dataRow['procedureName'];
                $data_str.="<option value='" . $dataRow['id']."~".$dataRow['code_type'] . "'>".$procName."</option>";
            }
        }
        return $data_str;
    }
	
	function generateProviderOrderWidgetList($providerId,$catId,$procCodeType, $patientId = 0,$facilityId=0,$facOrderType='0') {
		$this->CI->load->model('common/manageCommonTaskModel');
        $OrderData = $this->CI->manageCommonTaskModel->getProviderOrder($providerId, $catId, $procCodeType, $patientId,$facilityId,$facOrderType);
		$data_str='';
		$listCounter=0;
		if ($OrderData) {
            foreach ($OrderData as $dataRow) {
                if(empty($dataRow['abbr'])){
                    $data_str.="<li id='prefOrderListElement_".$listCounter."' class='prfrdName cursor pref-order-list'>";
			if (!empty($dataRow['last_date'])){
					$procName = $dataRow['procedureName'] . " (" . $dataRow['last_date'] . ")";
			}else{
				$procName = $dataRow['procedureName'];
			}
                }else{
			$data_str.="<li id='prefOrderListElement_".$listCounter."' class='prfrdName cursor pref-order-list'>";
			if (!empty($dataRow['last_date'])){
					$procName = "(".$dataRow['abbr'].") ".$dataRow['procedureName'] . " (" . $dataRow['last_date'] . ")";
			}else{
				$procName = "(".$dataRow['abbr'].") ".$dataRow['procedureName'];
			}
                }
            $data_str.="<input id='prefOption_".$listCounter."' class='prefrd-list-chkbx pref-option' type='checkbox' value='" . $dataRow['id']."~".$dataRow['code_type'] . "'><span class='left lbNmetext' id='prefOrderListValue_".$listCounter."'>".$procName."</span>";
			$data_str.="</li>";
			$listCounter++;
            }
        }
        return $data_str;
    }




	/**
     * 	Function		generateProviderDiagnosisDropDown
     * 	Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 	Description		this function will used to create the dropdown in the form
     * 	@param			$tableName => name of the table
     *                          $where => array for the where condition
     *                          $selectedArray=>array for the selected values in the dropdown
     * 	@return			dropdown.
     */
    function generateProviderDiagnosisDropDown($providerId,$codetype, $defaultDaigId='',$inclusiveFreeText='0',$billFlag='0') {

        $this->CI->load->model('common/manageCommonTaskModel');
        $diagData = $this->CI->manageCommonTaskModel->getProviderDiagnosis($providerId, $codetype,$inclusiveFreeText,$billFlag);
		$data_str ='';
        if ($diagData) {
			  foreach ($diagData as $dataRow) {
				 if (!empty($defaultDaigId) && ($dataRow['diagnosis_id'] == $defaultDaigId)) {
                     if(empty($dataRow['code'])){
                        $data_str.="<option selected='selected' value='".$dataRow['diagnosis_id']. "'> (".$dataRow['abbr'].") ".$dataRow['code']." - ".$dataRow['diseases_name']."</option>";
                     }else{
                         $data_str.="<option selected='selected' value='".$dataRow['diagnosis_id']. "'> (".$dataRow['abbr'].") ".$dataRow['diseases_name']."</option>";
                     }
                } else {
                    if(empty($dataRow['code'])){
                        $data_str.="<option value='" . $dataRow['diagnosis_id'] . "'> (".$dataRow['abbr'].") ".$dataRow['diseases_name']."</option>";
                    }else{
                        $data_str.="<option value='" . $dataRow['diagnosis_id'] . "'> (".$dataRow['abbr'].") ".$dataRow['code']." - ".$dataRow['diseases_name']."</option>";
                    }
                }
            }
        }
		
        return $data_str;
    }
    
    function getProviderStatus($providerId){
        $this->CI->load->model('common/manageCommonTaskModel');
        $provStatus = $this->CI->manageCommonTaskModel->getRecord("ch_user","active_status",array('id'=>$providerId,'user_type'=>'1'));
		if($provStatus['active_status']=='1'){
            return true;
        }else{
            return false;
        }
    }

	/*Giridhar Shukla: to get data from ros table*/
	function getItemNoteValue($patientId,$visitId,$rosId){

		$this->CI->load->model('common/manageCommonTaskModel');

		$note = $this->CI->manageCommonTaskModel->getRecord("ch_ptcld_ros","*",array('ros_id'=>$rosId,'patient_id'=>$patientId,'visit_id'=>$visitId));

		if(!$note) 

			return false;

		else 
			return $note;
	}
	
	function checkDiagnosisExistDSM($dsmId){
		$this->CI->load->model('common/manageCommonTaskModel');
		$diagData = $this->CI->manageCommonTaskModel->getRecord("ch_ptedt_patient_dsm_diagnosis", "*", array('dsm_id'=>$dsmId), null, "", "", 'array', '1');
		if(!$diagData) 
			return false;
        else  
			return $diagData;
	}

	/**Giridhar Shukla: to get diagnosis details by its id **/
	function getDiagnosisNameById($id){
		$this->CI->load->model('common/manageCommonTaskModel');
		$diagAllData = $this->CI->manageCommonTaskModel->getRecord("ch_cinfs_diagnosis_master","*",array('id'=>$id));
		if(!$diagAllData) 
			return false;
        else  
			return $diagAllData;
	}

	function getMonthName($monthNumber){
		if($monthNumber == '1' || $monthNumber == '01'){
			$monthName ='January';
		}else if($monthNumber == '2' || $monthNumber == '02'){
			$monthName ='February';
		}else if($monthNumber == '3' || $monthNumber == '03'){
			$monthName ='March';
		}else if($monthNumber == '4' || $monthNumber == '04'){
			$monthName ='April';
		}else if($monthNumber == '5' || $monthNumber == '05'){
			$monthName ='May';
		}else if($monthNumber == '6' || $monthNumber == '06'){
			$monthName ='June';
		}else if($monthNumber == '7' || $monthNumber == '07'){
			$monthName ='July';
		}else if($monthNumber == '8' || $monthNumber == '08'){
			$monthName ='August';
		}else if($monthNumber == '9' || $monthNumber == '09'){
			$monthName ='September';
		}else if($monthNumber == '10' || $monthNumber == '10'){
			$monthName ='October';
		}else if($monthNumber == '11' || $monthNumber == '11'){
			$monthName ='November';
		}else if($monthNumber == '12' || $monthNumber == '12'){
			$monthName ='December';
		}else{
			$monthName ='';
		}
		
		return $monthName;

	}
	
	function setTimeZone($zone='')
    {
        if($zone!=''){
        }else{
            $zone='Asia/Calcutta';
        }
        $chk=date_default_timezone_set($zone);
        if($chk){
            return true;
        }else{
            return false;
        }
    }
    
    
    	
	function subCatArtery($catid){
		$this->CI->load->model('common/manageCommonTaskModel');

		$subCat = $this->CI->manageCommonTaskModel->getRecord("ch_ptedt_lab_result_left_right_artery_master","*",array('child_of'=>$catid),'','','','array',1);

		if(!$subCat) 

			return false;

		else 
			return $subCat;
	}
	
	function getaddata($labResultId,$leftRightFlag,$arteryid){
		$this->CI->load->model('common/manageCommonTaskModel');
		
		$addata=$this->CI->manageCommonTaskModel->getDopplerArteryRecord($labResultId,$leftRightFlag,$arteryid);
			
			if(!$addata) 

			return false;

		else 
			return $addata;
	}

	function getClinicalHistoryWidgetContainer($widgetId, $page, $gender='male', $patient_id='0'){

		$fromWidget = 4;
		$maxVisitID=0;
		if(isset($patient_id) && !empty($patient_id) ){
			$maxVisitId=$this->executeModelFunctionAndGetTheResultBack(array($patient_id),'getMaxVisitIdOfUser');
			if($maxVisitId){
				$maxVisitID=$maxVisitId;
			}
		}

		$WidgetDashArray = array(
			'medication-Other-Otc-data'=>'<div class="left chistry-medication-cntnr one-edge-shadow" id="medication-Other-Otc-data"><div class="chistory-yelw-hdr"><span class="chistory-hdr-heading ">Medication-Other Physician/OTC</span>
		
			<form method="post" id="lanunchErxSystem" action="'.base_url().'patientClinicalData/staff/managePatientCurMedHistory/launchPatientErx">

    <input type="hidden" value="manage_medications" name="screen">
    <input type="hidden" name="service" value="rcopia">
    <input type="hidden" name="username" value="'.$this->getRcopiaUserName($this->CI->session->userdata("id")).'">
    <input type="hidden" name="redirect"  value="'.base_url().'patientClinicalData/staff/managePatientCurMedHistory/launchPatientErxSendBack/'.$patient_id . '/' . $maxVisitID.'/done/'.$fromWidget.'">
    <input type="hidden" id="patient_id" name="patient_id" value="'.$patient_id. '">
	<input type="hidden" name="allow_popup_screens" value="n">
</form>
		
		<?php if($returnAssessPMedication==2){?><span class="medication-add-edit social-history-recrd-change">Record/<br>Change</span><span class="erx" onclick="launchRcopia();"><a href="#">eRx</a></span><?php }?></div><div class="medication-data-block" id="medicationOtherOtcView"></div></div>',
			'immunization-shot-data'=>'<div class="left chistry-immun-cntnr one-edge-shadow" id="immunization-shot-data">
			<div class="chistory-yelw-hdr">
				<span class="chistory-hdr-heading ">Immunization & Shots</span>
				<?php if($returnAssessPImmun==2){?>
						<span class="immunization-add-edit social-history-recrd-change" id="immunizationPopUpOpen">Record/<br>Change</span>
						<?php }?>
			</div>
			<div class="immunization-data-block" id="patient-immunization-history-view">
			</div>
		</div>',
			'social-psychiatry-data'=>'<div class="left chistry-social-cntnr one-edge-shadow" id="social-psychiatry-data">
			<div class="chistory-yelw-hdr">
				<span class="chistory-hdr-heading ">Social History</span>
				<?php if($returnAssessPFamilySt==2){?>
				<span class="social-add-edit social-history-recrd-change" id="socialPopUpOpen">Record/<br>Change</span>
				<?php }?>
			</div>
			<div class="social-data-block social-data-block-2" id="patient-social-history-view">
			</div>
		</div>',
			'family-history-data'=>'<div class="left chistry-famly-cntnr one-edge-shadow" id="family-history-data">
			<div class="chistory-yelw-hdr">
				<span class="chistory-hdr-heading ">Family</span>
					<?php if($returnAssessPFamilySt==2){?>
				<span class="family-add-edit social-history-recrd-change" id="familyPopUpOpen">Record/<br>Change</span>
					<?php }?>
			</div>
			<div class="family-data-block" id="patient-family-history-view">
			</div>
		</div>',
			'general-history-data'=>'<div class="medical-general-history-data medical-profile-widget med-main-bg one-edge-shadow" id="general-history-data">
		</div>',
			'medical-history-data'=>'<div class="medical-medical-history-data medical-profile-widget med-main-bg one-edge-shadow" id="medical-history-data">
		</div>',
			'surgery-history-data'=>'<div class="medical-surgery-history-data medical-profile-widget med-main-bg one-edge-shadow" id="surgery-history-data">
		</div>',
			'woman-history-data'=>'<div class="medical-woman-history-data medical-profile-widget med-main-bg one-edge-shadow" id="woman-history-data">
		</div>',
			'medical-maintenance-data'=>'<div class="medical-health-maintenance-data medical-profile-widget med-main-bg one-edge-shadow" id="medical-maintenance-data">
			<div class="health-maintenance-data-row">                    
						<span class="header-menu-text-add-edit" id="medical-health-add-edit-open">Record/Change</span>                    
			</div>
		</div>',
			'breast-history-data'=>'<div class="medical-general-history-data medical-profile-widget med-main-bg one-edge-shadow" id="breast-history-view">
		</div>',
		'podiatry-history-data'=>'<div class="medical-general-history-data medical-profile-widget med-main-bg one-edge-shadow" id="podiatry-history-data">
		</div>'
			);
		
		$WidgetSettingArray = array(
			'medication-Other-Otc-data'=>'<div id="medication-Other-Otc-data" class="dashPos medication-Other-Otc-pos lft-pnl-wdgt-con-nw"></div>',
			'immunization-shot-data'=>'<div id="immunization-shot-data" class="dashPos immunization-shot-pos lft-pnl-wdgt-con-nw"></div>',
			'social-psychiatry-data'=>'<div id="social-psychiatry-data" class="dashPos social-psychiatry-pos lft-pnl-wdgt-con-nw"></div>',
			'family-history-data'=>'<div id="family-history-data" class="dashPos family-history-pos lft-pnl-wdgt-con-nw"></div>',
			'general-history-data'=>'<div id="general-history-data" class="dashPos general-history-pos lft-pnl-wdgt-con-nw"></div>',
			'medical-history-data'=>'<div id="medical-history-data" class="dashPos medical-history-pos lft-pnl-wdgt-con-nw"></div>',
			'surgery-history-data'=>'<div id="surgery-history-data" class="dashPos surgery-history-pos lft-pnl-wdgt-con-nw"></div>',
			'woman-history-data'=>'<div id="woman-history-data" class="dashPos woman-history-pos lft-pnl-wdgt-con-nw"></div>',
			'medical-maintenance-data'=>'<div id="medical-maintenance-data" class="dashPos medical-maintenance-pos lft-pnl-wdgt-con-nw"></div>',
			'breast-history-data'=>'<div id="breast-history-data" class="dashPos breast-maintenance-pos lft-pnl-wdgt-con-nw"></div>',
			'podiatry-history-data'=>'<div id="podiatry-history-data" class="dashPos podiatry-maintenance-pos lft-pnl-wdgt-con-nw"></div>'
			);

		if($widgetId !=''){
			if($page == 'dashboard'){
				
				if((strtolower($gender)!='female' && $widgetId == 'woman-history-data')){
					return '';
				}else{
					return $WidgetDashArray[$widgetId];
				}
				/*
				if($this->getClientPracticeId() == '5' || $this->getClientPracticeId() == '3'){
					if((strtolower($gender)!='female' && $widgetId == 'woman-history-data')){
						return '';
					}else{
						return $WidgetDashArray[$widgetId];
					}
				}else{
					if(strtolower($gender)!='female' && $widgetId == 'woman-history-data'){
						return '';
					}elseif($widgetId == 'breast-history-data'){
						return '';
					}else{
						return $WidgetDashArray[$widgetId];
					}
				}
				*/
			}
			if($page == 'setting'){
				/*
				if($this->getClientPracticeId() != '5' && $widgetId == 'breast-history-data'){
					if($this->getClientPracticeId() == '3' && $widgetId == 'breast-history-data'){
						$wedData = str_replace('breast-maintenance-pos', 'podiatry-maintenance-pos', $WidgetSettingArray[$widgetId]);
						return $wedData;
					}else{
						return '';
					}
				}else{
					return $WidgetSettingArray[$widgetId];
				}*/

				return $WidgetSettingArray[$widgetId];
			}

		}else{
			return '';
		}
	}

	function getWidgetContainer($widgetId, $page){

		
		$WidgetDashArray = array(
			'widget-diagnosis'=>'<div class="widget-diagnosis one-edge-shadow" id="widget-diagnosis"></div>',
			'widget-lab-order'=>'<div class="widget-lab-order one-edge-shadow" id="widget-lab-order"></div>',
			'widget-track-data'=>'<div class="widget-track-data widget-clinical one-edge-shadow hide" id="widget-track-data"></div><div class="widget-clinical one-edge-shadow" id="widget-clinical"></div>',
			'widget-prescription'=>'<div class="widget-prescription one-edge-shadow" id="widget-prescription"></div>',
			'widgetPhyExamAndPlan'=>'<div class="widget-phy-exm-plan one-edge-shadow" id="widgetPhyExamAndPlan"></div>',
			'widget-visit'=>'<div class="widget-visit one-edge-shadow" id="widget-visit"></div>',
			'widget-demographics'=>'<div class="widget-demographics one-edge-shadow" id="widget-demographics"></div>',
			'widget-vital'=>'<div class="widget-vital one-edge-shadow" id="widget-vital"></div>',
			'widget-nurses'=>'<div class="widget-nurses one-edge-shadow" id="widget-nurses"></div>',
			'widget-social'=>'<div class="widget-social one-edge-shadow" id="widget-social"></div>',
			'pcpAndRefProvider'=>'<div class="widget-pcp-referral one-edge-shadow" id="pcpAndRefProvider"></div>',
			'widget-pharmacy'=>' <div class="widget-pharmacy one-edge-shadow" id="widget-pharmacy"></div>'
			);
		
		$WidgetSettingArray = array(
			'widget-diagnosis'=>'<div id="widget-diagnosis" class="dashPos widget-diagnosis-pos"></div>',
			'widget-lab-order'=>'<div id="widget-lab-order" class="dashPos widget-lab-order-pos"></div>',
			'widget-track-data'=>'<div id="widget-track-data" class="dashPos widget-track-data-pos"></div>',
			'widget-prescription'=>'<div id="widget-prescription" class="dashPos widget-prescription-pos"></div>',
			'widgetPhyExamAndPlan'=>'<div id="widgetPhyExamAndPlan" class="dashPos widgetPhyExamAndPlan-pos"></div>',
			'widget-visit'=>'<div id="widget-visit" class="dashPos widget-visit-pos"></div>',
			'widget-demographics'=>'<div id="widget-demographics" class="dashPosRight widget-demographics-pos"></div>',
			'widget-vital'=>'<div id="widget-vital" class="dashPosRight widget-vital-pos"></div>',
			'widget-nurses'=>'<div id="widget-nurses" class="dashPosRight widget-nurses-pos"></div>',
			'widget-social'=>'<div id="widget-social" class="dashPosRight widget-social-pos"></div>',
			'pcpAndRefProvider'=>'<div id="pcpAndRefProvider" class="dashPosRight pcpAndRefProvider-pos"></div>',
			'widget-pharmacy'=>' <div id="widget-pharmacy" class="dashPosRight widget-pharmacy-pos"></div>'
			);

		if($widgetId !=''){
			if($page == 'dashboard'){
				return $WidgetDashArray[$widgetId];
			}
			if($page == 'setting'){
				return $WidgetSettingArray[$widgetId];
			}

		}else{
			return $WidgetArray['widget-diagnosis'];
		}
	}
	
	function getchargeCaptureAdmitDate($userid){
        /* $this->CI->load->model('common/manageCommonTaskModel');
        $admitDate = $this->CI->manageCommonTaskModel->getRecord("ch_charge_capture_bills","date_of_admission",array('cc_patient_id'=>$userid)); */
		
		$query="select max(date_of_admission) as admitdt from ch_charge_capture_bill where cc_patient_id=".$userid;

		$resultSet=$this->CI->db->query($query);

		if($resultSet){
            return $resultSet->row();
        }else{
            return false;
        }
    }

	function destroySessionProcess() {
        $CI =& get_instance();		
		delete_cookie('userName', '', '/', 'blueberry_');
        delete_cookie('password', '', '/', 'blueberry_');
        delete_cookie("userName");
        delete_cookie("password");
        $this->CI->session->sess_destroy();
		if ($_SERVER['SERVER_PORT'] == 443)  {
			$CI->config->config['base_url'] = str_replace('http://', 'https://', $CI->config->config['base_url']);
		}
		echo "<script>window.location.href='" . $CI->config->config['base_url'] . "process/login';</script>";
        die;
    }

	function getProviderTemplate($dataTableName,$otherDataId, $visitType){
        $this->CI->load->model('common/manageCommonTaskModel');
        $providerList = $this->CI->manageCommonTaskModel->getAllProviderList($visitType);
		$tempStr = '';
		if($providerList){
			if($visitType=='1'){
				foreach($providerList as $dataRow) {
					$tempStr .= '<form id="allData"><div class="providerTemplateRow">';
					
					$tempStr .= '<span class="providerLabel">Provider Name</span><span id="provider-'.$dataRow['id'].'"  class="providerName">';
					$tempStr .= $dataRow['firstName'].' '.$dataRow['lastName'];
					$tempStr .= '</span>';

					/*
					
					$tempStr .= '<span class="TemplateLabel">Template Name</span><span class="TemplateName"><select id="template-'.$dataRow['id'].'">';
					$providerTemplateList = $this->CI->manageCommonTaskModel->getProviderSoapTemplateList($dataRow['id']);
					$visitTemplateList = $this->CI->manageCommonTaskModel->getRecord("ch_ptedt_soap_visit_provider_template","template_id",array('provider_id'=>$dataRow['id'],'visit_type_id'=>$otherDataId));

					//echo "aaa==".$visitTemplateList['template_id'];
					// die;
					if($providerTemplateList){
						foreach($providerTemplateList as $tempRow) {
							if($tempRow['id'] == $visitTemplateList['template_id']){
								$tempStr .='<option value="'.$tempRow['id'].'" Selected="selected" >';
							}else{
								$tempStr .='<option value="'.$tempRow['id'].'">';
							}
							$tempStr .=$tempRow['template_name'];
							$tempStr .='</option>';
						}
					}
					$tempStr .= '</select></span>'
					*/
					$tempStr .= '<span class="durationLabel">Duration</span><span class="durationName"><select class="durationBox" id="duration-'.$dataRow['id'].'">';
					
					$allDurationList = $this->getAllDurationList();
					$resourceVisitDuration = $this->CI->manageCommonTaskModel->getRecord("ch_apsch_resource_visit_duration","duration_id",array('resource_id'=>$dataRow['id'],'visit_type_id'=>$otherDataId));
					
					if($allDurationList){
						foreach($allDurationList as $durationRow) {
							if($durationRow['id'] == $resourceVisitDuration['duration_id']){
								$tempStr .='<option value="'.$durationRow['id'].'" Selected="selected" >';
							}else{
								$tempStr .='<option value="'.$durationRow['id'].'">';
							}
							$tempStr .=$durationRow['duration'];
							$tempStr .='</option>';
						}
					}

					$tempStr .= '</select></span></div></form>';
				}
			}else{
				
				foreach($providerList as $dataRow) {
					$tempStr .= '<form id="allData"><div class="providerTemplateRow"><span class="providerLabel">Provider Name</span><span id="provider-'.$dataRow['id'].'"  class="providerName">';
					$tempStr .= $dataRow['firstName'].' '.$dataRow['lastName'];
					$tempStr .= '</span><span class="TemplateLabel">Template Name</span><span class="TemplateName"><select class="tempBox" id="template-'.$dataRow['id'].'">';
					$providerTemplateList = $this->CI->manageCommonTaskModel->getProviderSoapTemplateList($dataRow['id']);
					$visitTemplateList = $this->CI->manageCommonTaskModel->getRecord("ch_ptedt_soap_visit_provider_template","template_id",array('provider_id'=>$dataRow['id'],'visit_type_id'=>$otherDataId));

					//echo "aaa==".$visitTemplateList['template_id'];
					// die;
					if($providerTemplateList){
						foreach($providerTemplateList as $tempRow) {
							if($tempRow['id'] == $visitTemplateList['template_id']){
								$tempStr .='<option value="'.$tempRow['id'].'" Selected="selected" >';
							}else{
								$tempStr .='<option value="'.$tempRow['id'].'">';
							}
							$tempStr .=$tempRow['template_name'];
							$tempStr .='</option>';
						}
					}
					$tempStr .= '</select></span><span class="durationLabel">Duration</span><span class="durationName"><select class="durationBox" id="duration-'.$dataRow['id'].'">';
					
					$allDurationList = $this->getAllDurationList();
					$resourceVisitDuration = $this->CI->manageCommonTaskModel->getRecord("ch_apsch_resource_visit_duration","duration_id",array('resource_id'=>$dataRow['id'],'visit_type_id'=>$otherDataId));
					
					if($allDurationList){
						foreach($allDurationList as $durationRow) {
							if($durationRow['id'] == $resourceVisitDuration['duration_id']){
								$tempStr .='<option value="'.$durationRow['id'].'" Selected="selected" >';
							}else{
								$tempStr .='<option value="'.$durationRow['id'].'">';
							}
							$tempStr .=$durationRow['duration'];
							$tempStr .='</option>';
						}
					}

					$tempStr .= '</select></span></div></form>';
				}
			}
           
        }

		return $tempStr;
		die;
    }

	function getProviderVisitTemplate($templateType){
		if($templateType=='subjective'){
			$data =$this->CI->manageCommonTaskModel->getRecord("ch_ptedt_soap_template_details","subjective_note",array('provider_id'=>'1','active_flag'=>'1','template_name'=>'default'));
			if($data['subjective_note']) $tempStr =$data['subjective_note'];
			else $tempStr ='';
		}else if ($templateType=='objective'){
			$data =$this->CI->manageCommonTaskModel->getRecord("ch_ptedt_soap_template_details","objective_note",array('provider_id'=>'1','active_flag'=>'1','template_name'=>'default'));
			if($data['objective_note']) $tempStr =$data['objective_note'];
			else $tempStr ='';
		}else if ($templateType=='assessment'){
			$data =$this->CI->manageCommonTaskModel->getRecord("ch_ptedt_soap_template_details","assessment_note",array('provider_id'=>'1','active_flag'=>'1','template_name'=>'default'));
			if($data['assessment_note']) $tempStr =$data['assessment_note'];
			else $tempStr ='';
		}else if ($templateType=='plan'){
			$data =$this->CI->manageCommonTaskModel->getRecord("ch_ptedt_soap_template_details","plan_note",array('provider_id'=>'1','active_flag'=>'1','template_name'=>'default'));
			if($data['plan_note']) $tempStr =$data['plan_note'];
			else $tempStr ='';
		}else{
			$tempStr ='';
		}
		return $tempStr;
	}
	
	function ChangeMedGreStatusAsPerDrFirst($medgrestatus,$drfirstStatus)
	{
		if(!empty($medgrestatus)){
		
			if(!empty($drfirstStatus)){
				if($medgrestatus=='Signed & Sent' && $drfirstStatus=='printed'){
					$finalStatus='Printed';
				}else if($medgrestatus=='Signed & Sent' && $drfirstStatus=='saved'){
					$finalStatus='Saved';
				}else if($medgrestatus=='Signed & Sent' && $drfirstStatus=='printed,signed'){
					$finalStatus='Signed & Printed';
				}else if($medgrestatus=='Signed & Sent' && $drfirstStatus=='saved,signed'){
					$finalStatus='Signed & Saved';
				}else{
					$finalStatus=$medgrestatus;
				}
			}else{
				$finalStatus=$medgrestatus;
			}
            return $finalStatus;
        }else{
            return false;
        }
    }
	
	function getDiagnosisIdFromDsmId($id){
		$this->CI->load->model('common/manageCommonTaskModel');
		$diagAllData = $this->CI->manageCommonTaskModel->getRecord("ch_ptedt_patient_dsm_diagnosis","diagnosis_id",array('dsm_id'=>$id),'','','','array',1);
		if(!$diagAllData) 
			return false;
        else  
			return $diagAllData;
	}
    
    function getClientPracticeId($id='0', $type='0', $valueType='0'){
		// id:-	appointmentid, visitId, userId, patientId
		// Type:- 0=>defaultPacticeId, 1=>AppointmentId, 2=>visitId, 3=>userId , 4=>all distinct specility
		// valueType:- 0=> return value, 1=>return array value
		return $this->CI->manageCommonTaskModel->getParcticeId($id, $type,$valueType);
	}
	
	function getDefaultSpecialityOfPatient($id='0'){
		// id:-	appointmentid, visitId, userId, patientId
		// Type:- 0=>defaultPacticeId, 1=>AppointmentId, 2=>visitId, 3=>userId , 4=>all distinct specility
		// valueType:- 0=> return value, 1=>return array value
		return $this->CI->manageCommonTaskModel->getDefaultSpecialityOfPatient($id);
	}

	function processStatus($statusName){
		if($statusName =='Screening'){
			$currentProcessStatus ='Screened';
		}else if($statusName =='Check-in'){
			$currentProcessStatus ='Checked-in';
		}else if($statusName =='Examine'){
			$currentProcessStatus ='Examined';
		}else if($statusName =='Check out'){
			$currentProcessStatus ='Checked out';
		}else if($statusName =='Approve'){
			$currentProcessStatus ='Approved';
		}else if($statusName =='Progress Note'){
			$currentProcessStatus ='Progress Noted';
		}else{
			$currentProcessStatus =$statusName;
		}
		return $currentProcessStatus;
	}
	
	/*Giridhar Shukla*/
	function getPreviousVisitId($patientId,$visitId){
		$query="SELECT * FROM ch_apsch_appointment_visit
				WHERE `patient_id`='".$patientId."' AND `id` < '".$visitId."' 
				ORDER BY id DESC LIMIT 0,1";
		$resultSet=$this->CI->db->query($query);
		if($resultSet){
            return $resultSet->row_array();
        }else{
            return false;
        }
	}
	
	/**
     * This function will be used to get the appointmentId by visit id.
     * Function Name		:getResourceIdbyAppointmentId
	 *	Author			:kuldeep Verma
     */
	function getResourceIdbyAppointmentId($appointmentId)
	{
		$CI =& get_instance();
		$CI->load->model('common/manageCommonTaskModel');
		if($appointmentId!='' && $appointmentId!='0'){
			return $CI->manageCommonTaskModel->getResourceIdbyAppointmentId($appointmentId);
		}else{
			return false;
		}
	}

	function getVisitType($appointmentId){
		$query="select t1.appointment_type, t2.id,t2.visit_type from ch_apsch_appointment as t1 join ch_apsch_type_of_visit as t2 on t1.type_of_visit = t2.id where t1.id ='".$appointmentId."'";
		$resultSet=$this->CI->db->query($query);
		if($resultSet){
            return $resultSet->row_array();
        }else{
            return false;
        }
	}
    
    function getArrayOfDatesForAppOccur($passArray){
        $dayArray=array(
                '0'=>'SUNDAY',
                '1'=>'MONDAY',
                '2'=>'TUESDAY',
                '3'=>'WEDNESDAY',
                '4'=>'THURSDAY',
                '5'=>'FRIDAY',
                '6'=>'SATURDAY'
                );
        $monthArray=array(
            '1'=>'January',
            '2'=>'February',
            '3'=>'March',
            '4'=>'April',
            '5'=>'May',
            '6'=>'June',
            '7'=>'July',
            '8'=>'August',
            '9'=>'September',
            '10'=>'October',
            '11'=>'November',
            '12'=>'December'
            );
        $dateArray=array();
        switch($passArray['occur_type']){
            case '0'://week
                $oldDate=$this->convertDateFormatForDbase($passArray['app_date']);
                $dateFlag=true;
                $dateCount=0;
                $oldTs=strtotime($oldDate);
                $dayOfOldDate = date("w",$oldTs);
                 $recurrDayArray=array();
                 
                    if($passArray['chckMon']=='1'){
                        $recurrDayArray[]='1';
                    }
                    if($passArray['chckTue']=='1'){
                        $recurrDayArray[]='2';
                    }
                    if($passArray['chckWed']=='1'){
                        $recurrDayArray[]='3';
                    }
                    if($passArray['chckThu']=='1'){
                        $recurrDayArray[]='4';
                    }
                    if($passArray['chckFri']=='1'){
                        $recurrDayArray[]='5';
                    }
                    if($passArray['chckSat']=='1'){
                        $recurrDayArray[]='6';
                    }
                    if($passArray['chckSun']=='1'){
                        $recurrDayArray[]='0';
                    }
                    $key='';
                    foreach($recurrDayArray as $checkDay){
                        if($dayOfOldDate <= $checkDay){
                            $key = array_search($checkDay,$recurrDayArray);
                            break;
                        }
                    }
                while($dateFlag){
                    $j=0;
                    if($key!=''){
                        $j=$key;
                        $key='';
                    }
                    for($i=$j;$i < count($recurrDayArray);$i++){
                        $oldTs=strtotime($oldDate);
                        $dayOfOldDate = date("w",$oldTs);
                        if($dayOfOldDate == $recurrDayArray[$i]){
                            $dateArray[$dateCount]=$oldDate;
                            $oldDate= date('Y-m-d', strtotime('+1 day', strtotime($dateArray[$dateCount])));
                        }elseif($dayOfOldDate < $recurrDayArray[$i]){
                            $dateArray[$dateCount]=date('Y-m-d',strtotime("next ".$dayArray[$recurrDayArray[$i]],$oldTs));
                            $oldDate=date('Y-m-d', strtotime('+1 day', strtotime($dateArray[$dateCount])));

                        }else{
                            $dateArray[$dateCount]=date('Y-m-d',strtotime('+'.($passArray['recur_value']-1).' week',strtotime("next ".$dayArray[$recurrDayArray[$i]],$oldTs)));
                            $oldDate=date('Y-m-d', strtotime('+1 day', strtotime($dateArray[$dateCount])));
                        }
                        $dateCount++;
                        if($dateCount==$passArray['occur_end_after']){
                            $dateFlag=false;
                            break;
                        }
                    }
                }
            break;
            case '1'://month
                $oldDate=$this->convertDateFormatForDbase($passArray['app_date']);
                $dateFlag=true;
                $dateCount=0;
                switch($passArray['sub_occur_type']){
                    case '0':
                        while($dateFlag){
                                $oldTs=strtotime($oldDate);
                                $dateOfOldDate = date("j",$oldTs);
                                if($dateOfOldDate == $passArray['monthly_day']){
                                    $dateArray[$dateCount]=$oldDate;
                                    $oldDate= date('Y-m-d', strtotime('+1 day', strtotime($dateArray[$dateCount])));
                                }elseif($dateOfOldDate < $passArray['monthly_day']){
                                    $dateArray[$dateCount]=date('Y-m-'.$passArray['monthly_day']);
                                    $oldDate=date('Y-m-d', strtotime('+1 day', strtotime($dateArray[$dateCount])));
                                }else{
                                    $dateArray[$dateCount]=date('Y-m-'.$passArray['monthly_day'],strtotime('+'.($passArray['recur_value']).' month',$oldTs));
                                    $oldDate=date('Y-m-d', strtotime('+1 day', strtotime($dateArray[$dateCount])));
                                }
                                $dateCount++;
                                if($dateCount==$passArray['occur_end_after']){
                                    $dateFlag=false;
                                    break;
                                }

                        }
                        break;
                    case '1':
                        while($dateFlag){
                            $oldTs=strtotime($oldDate);
                            $dateOfOldDate = date("j",$oldTs);
                            $strTimeForSearch=strtotime(date('Y-m',$oldTs));
                            $dateOfSearch=date('j',strtotime($passArray['week_day']." ".$dayArray[$passArray['monthly_day']]." of ".date('F',$strTimeForSearch),$strTimeForSearch));
                            if($dateOfOldDate == $dateOfSearch){
                                $dateArray[$dateCount]=$oldDate;
                                $oldDate= date('Y-m-d', strtotime('+1 day', strtotime($dateArray[$dateCount])));
                            }elseif($dateOfOldDate < $dateOfSearch){
                                $dateArray[$dateCount]=date('Y-m-d',strtotime(date('Y-m-d',strtotime($passArray['week_day']." ".$dayArray[$passArray['monthly_day']]." of ".date('F',$oldTs),strtotime(date('Y-m',$oldTs))))));
                                $oldDate=date('Y-m-d', strtotime('+1 day', strtotime($dateArray[$dateCount])));
                            }else{
                                $strTime=strtotime(date('Y-m',strtotime('+'.($passArray['recur_value']).' month',strtotime(date('Y-m',$oldTs)))));
                                $dateArray[$dateCount]=date('Y-m-d',strtotime(date('Y-m-d',strtotime($passArray['week_day']." ".$dayArray[$passArray['monthly_day']]." of ".date('F',$strTime),$strTime))));
                                $oldDate=date('Y-m-d', strtotime('+1 day', strtotime($dateArray[$dateCount])));
                            }
                            $dateCount++;
                            if($dateCount==$passArray['occur_end_after']){
                                $dateFlag=false;
                                break;
                            }

                        }
                        break;
                }

            break;
            case '2'://yearly
                $oldDate=$this->convertDateFormatForDbase($passArray['app_date']);
                $dateFlag=true;
                $dateCount=0;
                switch($passArray['sub_occur_type']){
                    case '0':
                        while($dateFlag){
                                $oldTs=strtotime($oldDate);
                                $dateOfOldDate = date("j",$oldTs);
                                $monthOfOldDate = date("n",$oldTs);
                                if(($dateOfOldDate == $passArray['yearly_day'])&&(($monthOfOldDate == $passArray['yearly_month']))){
                                    $dateArray[$dateCount]=$oldDate;
                                    $oldDate= date('Y-m-d', strtotime('+1 day', strtotime($dateArray[$dateCount])));
                                }elseif(($dateOfOldDate < $passArray['yearly_day'])&&($monthOfOldDate < $passArray['yearly_month'])){
                                    $dateArray[$dateCount]=date('Y-m-d',strtotime(date('Y-'.$passArray['yearly_month'].'-'.$passArray['yearly_day'])));
                                    $oldDate=date('Y-m-d', strtotime('+1 day', strtotime($dateArray[$dateCount])));
                                }elseif(($dateOfOldDate < $passArray['yearly_day'])&&($monthOfOldDate == $passArray['yearly_month'])){
                                    $dateArray[$dateCount]=date('Y-m-d',strtotime(date('Y-'.$passArray['yearly_month'].'-'.$passArray['yearly_day'])));
                                    $oldDate=date('Y-m-d', strtotime('+1 day', strtotime($dateArray[$dateCount])));
                                }elseif(($dateOfOldDate == $passArray['yearly_day'])&&($monthOfOldDate < $passArray['yearly_month'])){
                                    $dateArray[$dateCount]=date('Y-m-d',strtotime(date('Y-'.$passArray['yearly_month'].'-'.$passArray['yearly_day'])));
                                    $oldDate=date('Y-m-d', strtotime('+1 day', strtotime($dateArray[$dateCount])));
                                }else{
                                    $dateArray[$dateCount]=date('Y-m-d',strtotime(date('Y-'.$passArray['yearly_month'].'-'.$passArray['yearly_day'],strtotime('+'.($passArray['recur_value']).' year',$oldTs))));
                                    $oldDate=date('Y-m-d', strtotime('+1 day', strtotime($dateArray[$dateCount])));
                                }
                                $dateCount++;
                                if($dateCount==$passArray['occur_end_after']){
                                    $dateFlag=false;
                                    break;
                                }

                        }
                        break;
                    case '1':
                        while($dateFlag){
                                $oldTs=strtotime($oldDate);
                                $dateOfOldDate = date("j",$oldTs);
                                $monthOfOldDate = date("n",$oldTs);
                                $yearOfOldDate = date("Y",$oldTs);

                                $dateOfSearch=date('j',strtotime($passArray['week_day']." ".$dayArray[$passArray['yearly_day']]." of ".$monthArray[$passArray['yearly_month']],strtotime(date('Y-m',$oldTs))));
                                $monthOfSearch=date('n',strtotime($passArray['week_day']." ".$dayArray[$passArray['yearly_day']]." of ".$monthArray[$passArray['yearly_month']],strtotime(date('Y-m',$oldTs))));
                                if(($dateOfOldDate == $dateOfSearch)&&(($monthOfOldDate == $monthOfSearch))){
                                    $dateArray[$dateCount]=$oldDate;
                                    $oldDate= date('Y-m-d', strtotime('+1 day', strtotime($dateArray[$dateCount])));
                                }elseif(($dateOfOldDate < $dateOfSearch)&&(($monthOfOldDate < $monthOfSearch))){
                                    $dateArray[$dateCount]=date('Y-m-d', strtotime(date('Y-'.$passArray['yearly_month'].'-'.$dateOfSearch),strtotime(date('Y-m'),$oldTs)));
                                    $oldDate=date('Y-m-d', strtotime('+1 day', strtotime($dateArray[$dateCount])));
                                }elseif(($dateOfOldDate < $dateOfSearch)&&(($monthOfOldDate == $monthOfSearch))){
                                    $dateArray[$dateCount]=date('Y-m-d', strtotime(date('Y-'.$passArray['yearly_month'].'-'.$dateOfSearch),strtotime(date('Y-m'),$oldTs)));
                                    $oldDate=date('Y-m-d', strtotime('+1 day', strtotime($dateArray[$dateCount])));
                                }elseif(($dateOfOldDate == $dateOfSearch)&&(($monthOfOldDate < $monthOfSearch))){
                                    $dateArray[$dateCount]=date('Y-m-d', strtotime(date('Y-'.$passArray['yearly_month'].'-'.$dateOfSearch),strtotime(date('Y-m'),$oldTs)));
                                    $oldDate=date('Y-m-d', strtotime('+1 day', strtotime($dateArray[$dateCount])));
                                }else{
                                    $dateArray[$dateCount]=date('Y-m-d',strtotime($passArray['week_day']." ".$dayArray[$passArray['yearly_day']]." of ".$monthArray[$passArray['yearly_month']],strtotime('+'.($passArray['recur_value']).' year',strtotime(date('Y-m',$oldTs)))));
                                    $oldDate=date('Y-m-d', strtotime('+1 day', strtotime($dateArray[$dateCount])));
                                }
                                $dateCount++;
                                if($dateCount==$passArray['occur_end_after']){
                                    $dateFlag=false;
                                    break;
                                }

                        }
                        break;
                }

            break;
        }
        return $dateArray;
    }
	
	/**
     * Function Name		:getFieldsFromAnyTable
	 *	Author			:Giridhar Shukla
	 *  $table -name of table
	 *  $fields - comma separated fields
	 *  $where - array of where condition 	
    **/
	function getFieldsFromAnyTable($table, $fields, $where) {
        $resultArray = $this->CI->manageCommonTaskModel->getRecord($table, $fields, $where);
        return $resultArray;
    }
	
	function getProcedureNamewithCode($procedureId,$codeType){
		if($codeType==3){
			$procedureRes= $this->getFieldsFromAnyTable('ch_clmdt_loinc_master','loinc_code,loinc_name',array('id'=>$procedureId));
			$procedureCode= $procedureRes['loinc_code'].' - '.$procedureRes['loinc_name'];
		}else{
			$procName=$this->getSpecificFieldFromAnyTable('ch_clmdt_procedures_code','procedure_name',$procedureId);
			$procCode=$this->CI->manageCommonTaskModel->getRecord('ch_clmdt_specific_procedures_code', 'group_concat(proc_code) as procCode', array('procedure_id'=>$procedureId,'code_type'=>$codeType));
			$procedureCode=$procCode['procCode'].' - '.$procName;
		}
		return $procedureCode;
	}
	
	function facilitySetiing()
	{	
		$resultArray = $this->CI->manageCommonTaskModel->getRecord('ch_apsch_facility_settings', '*');
		return $resultArray;
	}

	function providerFacilityHours($resource,$location,$selectdDate){
		$selectedDate = strtotime($selectdDate);
		$day = date('w', $selectedDate);
		$resultArray = $this->CI->manageCommonTaskModel->getRecord('ch_apsch_provider_hours', '*',array('provider_id'=>$resource,'clinic_id'=>$location,'day_id'=>$day));
		return $resultArray;
	}

	
	function getCityNameById($cityid)
	{	
		$resultArray = $this->CI->manageCommonTaskModel->getRecord('ch_cinfs_city', 'city_name',array('id'=>$cityid));
		return $resultArray['city_name'];
	}
	
	function getStateNameById($stateId)
	{	
		$resultArray = $this->CI->manageCommonTaskModel->getRecord('ch_cinfs_state', 'state_name',array('id'=>$stateId));
		return $resultArray['state_name'];
	}
	
	function getStateCodeById($stateId)
	{	
		$resultArray = $this->CI->manageCommonTaskModel->getRecord('ch_cinfs_state', 'iso_code',array('id'=>$stateId));
		return $resultArray['iso_code'];
	}
	
	
	function patientDrugAllergy($patientId)
	{
		
		 $patientAllergies =  $this->CI->manageCommonTaskModel->getRecord(TBL_ALLERGY_INFO, "*", array('patient_id' => $patientId,'active_flag'=>'1','allergy_type'=>'1'), null, "", "", 'array', '1');
		
		// Finding all the DrugAllergy Drug list for validation/Reminder for Doctor.
		$drugDataArray = $this->CI->manageCommonTaskModel->getPatientDrugAllergies($patientId);
		$allergyList1 = $drugDataArray['key_value']; //for the drugs based allergies

		$typeOfAllergies = array('1' => 'Drug Allergy','2' => 'Drug Group Allergy', '3' => 'Misc Allergy', '44' => 'Food Allergy', '45' => 'Enviroment Allergy');
		$allergiesArray = array();
		if(empty($patientAllergies) || count($patientAllergies)<=0){
			return false;
		}else{
			foreach ($patientAllergies as $allergy) {
				$allergyList = 'allergyList' . $allergy['allergy_type'];
				$allergyListFull = $$allergyList;
				$allergiesArray[] = $allergyListFull[$allergy['allergy_id']];
			}
			$data['allergiesStr'] = implode(', ', $allergiesArray);
			return 	 $data['allergiesStr'];
		}
	}

	function getAllDurationList(){
		return $this->CI->manageCommonTaskModel->getAllDurationList();
	}

	function getDefaultDurationList($resourceId, $visitTypeId){

		$visitTypeDuration=$this->CI->manageCommonTaskModel->getRecord('ch_apsch_resource_visit_duration','duration_id',array('resource_id'=>$resourceId, 'visit_type_id'=>$visitTypeId, 'active_flag'=>'1'));
		return $visitTypeDuration['duration_id'];
	}

	function getAllTypeOfVisit($resourceId,$groupApp,$patientType){
		$userDatial=$this->CI->manageCommonTaskModel->getRecord(CH_USER_TABLE,'user_type,schedule_appointment',array('id'=>$resourceId));
		$userType = $userDatial['user_type'];
		return $this->CI->manageCommonTaskModel->getAllVisitTypeList($userType,$groupApp,$patientType);
	}


    function generateProviderPrefMedDropDown($providerId,$defaultPrescId='') {

        $this->CI->load->model('common/manageCommonTaskModel');
        $medData = $this->CI->manageCommonTaskModel->getProviderPrefMedicationPref($providerId);
		$data_str ='';
        if ($medData) {
			  foreach ($medData as $dataRow) {
				  $dataValue=$dataRow['drug_id']."|@@@|".$dataRow['id'];
				 if (!empty($defaultDaigId) && ($dataRow['id'] == $defaultPrescId)) {
                    $data_str.="<option selected='selected' value='".$dataValue. "'>".$dataRow['pref_med_name']."</option>";
                } else {
                    $data_str.="<option value='" . $dataValue . "'>".$dataRow['pref_med_name']."</option>";
                }
            }
        }		
        return $data_str;
    }

	function generateProviderPrefListOrderWzd($providerId,$defaultPrescId='') {

        $this->CI->load->model('common/manageCommonTaskModel');
        $medData = $this->CI->manageCommonTaskModel->getProviderPrefMedicationPref($providerId);
		$data_str ='';
		$vowels = array(".", "#",'/');
        if ($medData) {
			  foreach ($medData as $dataRow) {
				  $dataValue=$dataRow['drug_id']."|@@@|".$dataRow['id'];
				/*if (!empty($defaultDaigId) && ($dataRow['id'] == $defaultPrescId)) {
                    $data_str.="<option selected='selected' value='".$dataValue. "'>".$dataRow['pref_med_name']."</option>";
                } else {*/
                    //$data_str.="<option value='" . $dataValue . "'>".$dataRow['pref_med_name']."</option>";
					$data_str.="<li class='prfrdName prfrdNameMed cursor' datasearchtermmed='".str_replace($vowels,'',strtolower($dataRow['pref_med_name']))."' medId='".$dataRow['drug_id']."_".$dataRow['id']."' mcode='".$dataRow['code']."'>".$dataRow['pref_med_name']."</li>";
                //}
            }
        }		
        return $data_str;
    }
	
	function progressNotedDone($appointmentId,$lastStatusId)
	{
		$CI =& get_instance();
		$insertDataArray=array(
						'appointment_id'=> $appointmentId,
						'appointment_status'=> '8',
						'last_status'=> $lastStatusId,
						'is_current_status'=> '1',
						'status_done'=>'1',
						'date_added'=> date('Y-m-d H:i:s'),
						'added_by'=> $CI->session->userdata('id')
		);
		$this->CI->manageCommonTaskModel->insertRecord('ch_apsch_patient_appointment_status',$insertDataArray);
		$updateArray=array(
						'is_current_status'=> '0',
						'date_modified'=> date('Y-m-d H:i:s'),
						'updated_by'=> $CI->session->userdata('id')
		);
		$this->CI->manageCommonTaskModel->updateRecord('ch_apsch_patient_appointment_status',$updateArray,array('id'=>$lastStatusId));
		$insertProcessDataArray=array(
						'appointment_id'=> $appointmentId,
						'appointment_status'=> '8',
						'done_flag'=> '1',
						'date_added'=> date('Y-m-d H:i:s'),
						'added_by'=> $CI->session->userdata('id')
		);
		$this->CI->manageCommonTaskModel->insertRecord('ch_apsch_appointment_process_done',$insertProcessDataArray);
		
		$updateFilterArray=array('progress_note'=>'1');
		$this->CI->manageCommonTaskModel->updateRecord('ch_apsch_filter_status',$updateFilterArray,array('appointment_id'=>$appointmentId));
	}
	
	function getProcessDoneFlag($appointmentId,$statusId)
	{
		$CI =& get_instance();
		return $this->CI->manageCommonTaskModel->getProcessDoneFlag($appointmentId,$statusId);
	}
	
	function getInProcess($appointmentId,$statusId)
	{
		$CI =& get_instance();
		return $this->CI->manageCommonTaskModel->getInProcess($appointmentId,$statusId);
	}


	function checkBookAppPermission($userId)
	{
		$CI =& get_instance();
		$get=$this->CI->manageCommonTaskModel->getRecord('ch_user','schedule_appointment',array('id'=>$userId,'active_status'=>'1'));
		if($get['schedule_appointment']==1){
			return '1';
		}else{
			return '0';
		}
	}
	
	function getProcessProgressNoteDone($appointmentId,$statusActionId )
	{
		$CI =& get_instance();
		$get=$this->CI->manageCommonTaskModel->getRecord('ch_apsch_appointment_process','check_uncheck',array('appointment_id'=>$appointmentId,'status_action_id'=>$statusActionId,'check_uncheck'=>'1'));
		if($get['check_uncheck']==1){
			return '1';
		}else{
			return '0';
		}
	}
	
	function getMaxStatusDone($appointmentId)
	{
		$CI =& get_instance();
		return $this->CI->manageCommonTaskModel->getMaxStatusDone($appointmentId);
	}
	
	function getStatusForProgressNoteDone($appointmentId)
	{
		$CI =& get_instance();
		return $this->CI->manageCommonTaskModel->getStatusForProgressNoteDone($appointmentId);
	}


	/**
     * This function will be used to save/update patient all data
     * 
	 * @author 	Abhishek Singh<abhishek.singh@greenapplestech.com>
     * @param 	int $patientId
     * @param 	array $patientDataArray		 
     * @return 	NA.
     */	
	function savePatientDataInSepTable($patientId,$patientDataArray)
	{
		if(empty($patientId)) return false;
		if(empty($patientDataArray)) return false;
		
		// Preparing demographic data Array.

		if(isset($patientDataArray['first_name'])) {$patientDemographicArray['first_name'] = $patientDataArray['first_name'];}
		
		if(isset($patientDataArray['patient_title'])) {$patientDemographicArray['patient_title'] = $patientDataArray['patient_title'];}
		if(isset($patientDataArray['patient_salutation'])) {$patientDemographicArray['patient_salutation'] = $patientDataArray['patient_salutation'];}
		if(isset($patientDataArray['blood_rh'])) {$patientDemographicArray['blood_rh'] = $patientDataArray['blood_rh'];}
		if(isset($patientDataArray['blood_type'])) {$patientDemographicArray['blood_type'] = $patientDataArray['blood_type'];}
		
		if(isset($patientDataArray['middle_name'])) {$patientDemographicArray['middle_name'] = $patientDataArray['middle_name'];}
		if(isset($patientDataArray['last_name'])) {$patientDemographicArray['last_name'] = $patientDataArray['last_name'];}
		if(isset($patientDataArray['gender'])) {$patientDemographicArray['gender'] = $patientDataArray['gender'];}
		if(isset($patientDataArray['date_of_birth'])) {
			$patientDemographicArray['date_of_birth']= $this->convertDateFormatForDbase($patientDataArray['date_of_birth']);
		}
		if(isset($patientDataArray['ssn'])) {$patientDemographicArray['ssn']= str_replace("-","",$patientDataArray['ssn']);}
		if(isset($patientDataArray['preferred_mode'])) {$patientDemographicArray['preferred_mode']= $patientDataArray['preferred_mode'];}
		if(isset($patientDataArray['home_phone'])) {$patientDemographicArray['home_phone']= $patientDataArray['home_phone'];}
		if(isset($patientDataArray['mobile'])) {$patientDemographicArray['mobile']= $patientDataArray['mobile'];}
		if(isset($patientDataArray['fax'])) {$patientDemographicArray['fax']= $patientDataArray['fax'];}
		if(isset($patientDataArray['office_desk_telephone'])) {$patientDemographicArray['office_desk_telephone'] = $patientDataArray['office_desk_telephone'];}
		if(isset($patientDataArray['other_contact'])) {$patientDemographicArray['other_contact'] = $patientDataArray['other_contact'];}
		if(isset($patientDataArray['email'])) {$patientDemographicArray['email'] = $patientDataArray['email'];}
		if(isset($patientDataArray['secure_email'])) {$patientDemographicArray['secure_email'] = $patientDataArray['secure_email'];}
		if(isset($patientDataArray['address'])) {$patientDemographicArray['address']= $patientDataArray['address'];}
		if(isset($patientDataArray['correspondence_address'])) {$patientDemographicArray['correspondence_address']= $patientDataArray['correspondence_address'];}
		if(isset($patientDataArray['country'])) {$patientDemographicArray['country']= $patientDataArray['country'];}
		if(isset($patientDataArray['state'])) {$patientDemographicArray['state']= $patientDataArray['state'];}
		if(isset($patientDataArray['city'])) {$patientDemographicArray['city']= $patientDataArray['city'];}
		if(isset($patientDataArray['zip'])) {$patientDemographicArray['zip'] = $patientDataArray['zip'];}
		if(isset($patientDataArray['stats_language'])) {$patientDemographicArray['stats_language']= $patientDataArray['stats_language'];}
		if(isset($patientDataArray['stats_race'])) {$patientDemographicArray['stats_race']= $patientDataArray['stats_race'];}
		if(isset($patientDataArray['stats_ethincity'])) {$patientDemographicArray['stats_ethincity']= $patientDataArray['stats_ethincity'];}
		if(isset($patientDataArray['chart_number'])) {$patientDemographicArray['chart_number']= $patientDataArray['chart_number'];}
		if(isset($patientDataArray['emergency_first_name'])) {$patientDemographicArray['emergency_first_name']= $patientDataArray['emergency_first_name'];}
		if(isset($patientDataArray['emergency_middle_name'])) {$patientDemographicArray['emergency_middle_name']= $patientDataArray['emergency_middle_name'];}
		if(isset($patientDataArray['emergency_last_name'])) {$patientDemographicArray['emergency_last_name']= $patientDataArray['emergency_last_name'];}
		if(isset($patientDataArray['emergency_email_id'])) {$patientDemographicArray['emergency_email_id']= $patientDataArray['emergency_email_id'];}
		if(isset($patientDataArray['emergency_telephone'])) {$patientDemographicArray['emergency_telephone']= $patientDataArray['emergency_telephone'];}
		if(isset($patientDataArray['emergency_relationship'])) {$patientDemographicArray['emergency_relationship']= $patientDataArray['emergency_relationship'];}

		if(isset($patientDataArray['stats_place_birth'])) {$patientDemographicArray['stats_place_birth']= $patientDataArray['stats_place_birth'];}
		if(isset($patientDataArray['interpreter_required'])) {$patientDemographicArray['interpreter_required']= $patientDataArray['interpreter_required'];}
		if(isset($patientDataArray['referral_source_name'])) {$patientDemographicArray['referral_source_name']= $patientDataArray['referral_source_name'];}	
		if(isset($patientDataArray['stats_family_size'])) {$patientDemographicArray['stats_family_size']= $patientDataArray['stats_family_size'];}
		if(isset($patientDataArray['marital_status'])) {$patientDemographicArray['marital_status']= $patientDataArray['marital_status'];}
		if(isset($patientDataArray['stats_yearly_income'])) {$patientDemographicArray['stats_yearly_income']= $patientDataArray['stats_yearly_income'];}
		if(isset($patientDataArray['stats_referral_source'])) {$patientDemographicArray['stats_referral_source']= $patientDataArray['stats_referral_source'];}			
		if(isset($patientDataArray['contact'])) {$patientDemographicArray['contact']= $patientDataArray['contact'];}
        if(isset($patientDataArray['patient_religion'])) {$patientDemographicArray['patient_religion']= $patientDataArray['patient_religion'];}
        if(isset($patientDataArray['patient_fin_class'])) {$patientDemographicArray['patient_fin_class']= $patientDataArray['patient_fin_class'];}
		
		if(isset($patientDataArray['mother_name'])) {$patientDemographicArray['mother_name']= $patientDataArray['mother_name'];}
		if(isset($patientDataArray['date_of_death'])) {
			$patientDemographicArray['date_of_death']= $this->convertDateFormatForDbase($patientDataArray['date_of_death']);
			}
		if(isset($patientDataArray['patient_reason'])) {$patientDemographicArray['patient_reason']= $patientDataArray['patient_reason'];}
		if(isset($patientDataArray['photograph'])) {$patientDemographicArray['photograph']= $patientDataArray['photograph'];}
		// Saving patient demographics in sep table.
		$this->savePatientDemographicData($patientId,$patientDemographicArray);

		// Preparing responsible party data Array.
		if(isset($patientDataArray['responsible_party_id'])){$patientRespPartyArray['responsible_party_id'] = $patientDataArray['responsible_party_id'];}
		if(isset($patientDataArray['res_party_external_id'])){$patientRespPartyArray['res_party_external_id'] = $patientDataArray['res_party_external_id'];}
		if(isset($patientDataArray['guardian_first_name'])){$patientRespPartyArray['guardian_first_name'] = $patientDataArray['guardian_first_name'];}
		if(isset($patientDataArray['guardian_middle_name'])){$patientRespPartyArray['guardian_middle_name'] =$patientDataArray['guardian_middle_name'];}
		if(isset($patientDataArray['guardian_last_name'])){$patientRespPartyArray['guardian_last_name'] = $patientDataArray['guardian_last_name'];}
		if(isset($patientDataArray['res_party_dob'])){
			$patientRespPartyArray['res_party_dob'] = $this->convertDateFormatForDbase($patientDataArray['res_party_dob']);
			}
		if(isset($patientDataArray['res_party_gender'])){$patientRespPartyArray['res_party_gender'] =$patientDataArray['res_party_gender'];}
		if(isset($patientDataArray['guardian_email_id'])){$patientRespPartyArray['guardian_email_id'] = $patientDataArray['guardian_email_id'];}
		if(isset($patientDataArray['res_party_ssn'])){$patientRespPartyArray['res_party_ssn'] = str_replace("-","",$patientDataArray['res_party_ssn']);}
		if(isset($patientDataArray['res_party_relationship'])){$patientRespPartyArray['res_party_relationship'] = $patientDataArray['res_party_relationship'];}
		if(isset($patientDataArray['res_party_home_phone'])){$patientRespPartyArray['res_party_home_phone'] = $patientDataArray['res_party_home_phone'];}
		if(isset($patientDataArray['res_party_work_phone'])){$patientRespPartyArray['res_party_work_phone'] = $patientDataArray['res_party_work_phone'];}
		if(isset($patientDataArray['res_party_address1'])){$patientRespPartyArray['res_party_address1'] = $patientDataArray['res_party_address1'];}
		if(isset($patientDataArray['res_party_address2'])){$patientRespPartyArray['res_party_address2'] = $patientDataArray['res_party_address2'];}
		if(isset($patientDataArray['res_party_city'])){$patientRespPartyArray['res_party_city'] = $patientDataArray['res_party_city'];}
		if(isset($patientDataArray['res_party_state'])){$patientRespPartyArray['res_party_state'] = $patientDataArray['res_party_state'];}
		if(isset($patientDataArray['res_party_zip'])){$patientRespPartyArray['res_party_zip'] = $patientDataArray['res_party_zip'];}
		if(isset($patientDataArray['res_party_country'])){$patientRespPartyArray['res_party_country'] = $patientDataArray['res_party_country'];}

		// Saving patient responsible party in sep table.
		$this->savePatientResponsiblePartyData($patientId,$patientRespPartyArray);
		
		// Preparing patient Employer data Array.

		if(isset($patientDataArray['employer_occupation'])){$patientEmployerArray['employer_occupation'] = $patientDataArray['employer_occupation'];}
		if(isset($patientDataArray['employer_name'])){$patientEmployerArray['employer_name'] = $patientDataArray['employer_name'];}
		if(isset($patientDataArray['employer_name_label'])){$patientEmployerArray['employer_name_label'] = $patientDataArray['employer_name_label'];}
		if(isset($patientDataArray['emp_external_id'])){$patientEmployerArray['emp_external_id'] =$patientDataArray['emp_external_id'];}
		if(isset($patientDataArray['employer_email_id'])){$patientEmployerArray['employer_email_id'] = $patientDataArray['employer_email_id'];}
		if(isset($patientDataArray['employer_phone_1'])){$patientEmployerArray['employer_phone_1'] = $patientDataArray['employer_phone_1'];}
		if(isset($patientDataArray['employer_phone_2'])){$patientEmployerArray['employer_phone_2'] =$patientDataArray['employer_phone_2'];}
		if(isset($patientDataArray['employer_fax'])){$patientEmployerArray['employer_fax'] = $patientDataArray['employer_fax'];}
		if(isset($patientDataArray['employer_website'])){$patientEmployerArray['employer_website'] = $patientDataArray['employer_website'];}
		if(isset($patientDataArray['employer_contact_person'])){$patientEmployerArray['employer_contact_person'] = $patientDataArray['employer_contact_person'];}
		if(isset($patientDataArray['employer_address_1'])){$patientEmployerArray['employer_address_1'] = $patientDataArray['employer_address_1'];}
		if(isset($patientDataArray['employer_address_2'])){$patientEmployerArray['employer_address_2'] = $patientDataArray['employer_address_2'];}
		if(isset($patientDataArray['employer_city'])){$patientEmployerArray['employer_city'] = $patientDataArray['employer_city'];}
		if(isset($patientDataArray['employer_state'])){$patientEmployerArray['employer_state'] = $patientDataArray['employer_state'];}
		if(isset($patientDataArray['employer_zip'])){$patientEmployerArray['employer_zip'] = $patientDataArray['employer_zip'];}
		if(isset($patientDataArray['employer_country'])){$patientEmployerArray['employer_country'] = $patientDataArray['employer_country'];}

		// Saving patient Employer party in sep table.
		$this->savePatientEmployerData($patientId,$patientEmployerArray);
	}
	
	/**
     * This function will be used to save patient demographic data
     * 
	 * @author 	Abhishek Singh<abhishek.singh@greenapplestech.com>
     * @param 	int $patientId
     * @param 	array $patientDataArray		 
     * @return 	NA.
     */	
	function savePatientDemographicData($patientId,$patientDataArray)
	{
		if(empty($patientId)){
			return false;
		}else{
			if(empty($patientDataArray)) return false;
			// Checking Patient row exist or not.
			$checkRowflag=$this->checkPatientRowExistorNot('ch_uaprm_patient_profile_data',$patientId);
			$CI =& get_instance();
			
			if($checkRowflag){
				$patientDataArray['date_modified']=date('Y-m-d H:i:s');
				$patientDataArray['updated_by']=$CI->session->userdata('id');
				$this->CI->manageCommonTaskModel->updateRecord('ch_uaprm_patient_profile_data',$patientDataArray,array('user_id'=>$patientId));
			}else{
				$patientDataArray['user_id']=$patientId;
				$patientDataArray['date_added']=date('Y-m-d H:i:s');
				$patientDataArray['added_by']=$CI->session->userdata('id');
				$patientDataArray['date_modified']=date('Y-m-d H:i:s');
				$patientDataArray['updated_by']=$CI->session->userdata('id');
				
				$this->CI->manageCommonTaskModel->insertRecord('ch_uaprm_patient_profile_data',$patientDataArray);

				// Updating Chart_Int field
				$tempChart=$patientDataArray[chart_number];
				$this->updatePatientChartInt($patientId,$tempChart);
			}			
		}
	}
	
	/**
     * This function will be used to save patient demographic data through MBS 
     * 
	 * @author 	Shailesh Soni <shailesh.soni@greenapplestech.com>
     * @param 	int $patientId
     * @param 	array $patientDataArray		 
     * @return 	NA.
     */	
	function savePatientDemographicDataMbs($patientId,$patientDataArray)
	{
		if(empty($patientId)){
			return false;
		}else{
			if(!empty($patientDataArray))
			{
				foreach($patientDataArray as $key=>$value)
				{
					if($value=='NULL' || $value=='')
					unset($patientDataArray[$key]);
				}
				
			}
			
			
			if(empty($patientDataArray)) return false;
			// Checking Patient row exist or not.
			
			
			
			$checkRowflag=$this->checkPatientRowExistorNot('ch_uaprm_patient_profile_data',$patientId);
			$CI =& get_instance();
			
			if($checkRowflag){
				$patientDataArray['date_modified']=date('Y-m-d H:i:s');
				//$patientDataArray['updated_by']=$CI->session->userdata('id');
				
				$this->CI->manageCommonTaskModel->updateRecord('ch_uaprm_patient_profile_data',$patientDataArray,array('user_id'=>$patientId));
			}else{
				$patientDataArray['user_id']=$patientId;
				$patientDataArray['date_added']=date('Y-m-d H:i:s');
				//$patientDataArray['added_by']=$CI->session->userdata('id');
				$patientDataArray['date_modified']=date('Y-m-d H:i:s');
				//$patientDataArray['updated_by']=$CI->session->userdata('id');
				
				$this->CI->manageCommonTaskModel->insertRecord('ch_uaprm_patient_profile_data',$patientDataArray);
			}			
		}
	}

	/**
     * This function will be used to save patient responsible party data
     * 
	 * @author 	Abhishek Singh<abhishek.singh@greenapplestech.com>
     * @param 	int $patientId
     * @param 	array $patientDataArray		 
     * @return 	NA.
     */		
	function savePatientResponsiblePartyData($patientId,$patientDataArray)
	{
		if(empty($patientId)){
			return false;
		}else{
			if(empty($patientDataArray)) return false;
			// Checking Patient row exist or not.
			$checkRowflag=$this->checkPatientRowExistorNot('ch_uaprm_patient_responsible_party',$patientId);
			$CI =& get_instance();
			
			if($checkRowflag){
				$patientDataArray['date_modified']=date('Y-m-d H:i:s');
				$patientDataArray['updated_by']=$CI->session->userdata('id');
				
				$this->CI->manageCommonTaskModel->updateRecord('ch_uaprm_patient_responsible_party',$patientDataArray,array('user_id'=>$patientId));
			}else{
				$patientDataArray['user_id']=$patientId;
				$patientDataArray['date_added']=date('Y-m-d H:i:s');
				$patientDataArray['added_by']=$CI->session->userdata('id');
				$patientDataArray['date_modified']=date('Y-m-d H:i:s');
				$patientDataArray['updated_by']=$CI->session->userdata('id');
				
				$this->CI->manageCommonTaskModel->insertRecord('ch_uaprm_patient_responsible_party',$patientDataArray);
			}	
		}
	}

	/**
     * This function will be used to save patient employer party data
     * 
	 * @author 	Abhishek Singh<abhishek.singh@greenapplestech.com>
     * @param 	int $patientId
     * @param 	array $patientDataArray		 
     * @return 	NA.
     */	
	function savePatientEmployerData($patientId,$patientDataArray)
	{
		if(empty($patientId)){
			return false;
		}else{
			if(empty($patientDataArray)) return false;
			// Checking Patient row exist or not.
			$checkRowflag=$this->checkPatientRowExistorNot('ch_uaprm_patient_employer',$patientId);
			$CI =& get_instance();
			
			if($checkRowflag){
				$patientDataArray['date_modified']=date('Y-m-d H:i:s');
				$patientDataArray['updated_by']=$CI->session->userdata('id');
				
				$this->CI->manageCommonTaskModel->updateRecord('ch_uaprm_patient_employer',$patientDataArray,array('user_id'=>$patientId));
			}else{
				$patientDataArray['user_id']=$patientId;
				$patientDataArray['date_added']=date('Y-m-d H:i:s');
				$patientDataArray['added_by']=$CI->session->userdata('id');
				$patientDataArray['date_modified']=date('Y-m-d H:i:s');
				$patientDataArray['updated_by']=$CI->session->userdata('id');
				
				$this->CI->manageCommonTaskModel->insertRecord('ch_uaprm_patient_employer',$patientDataArray);
			}	
		}
	}

	/**
     * This function will be used to check record exist or not in respective table.
     * 
	 * @author 	Abhishek Singh<abhishek.singh@greenapplestech.com>
     * @param 	string $tableName
     * @param 	int $patientId		 
     * @return 	True/False.
     */		
	function checkPatientRowExistorNot($tableName,$patientId)
	{
		if(empty($patientId) || empty($tableName)) return false;
		$CI =& get_instance();
		$get=$this->CI->manageCommonTaskModel->getRecord($tableName,'id',array('user_id'=>$patientId));
		if(isset($get['id']) && !empty($get['id'])){
			return true;
		}else{
			return false;
		}
	}
	
	/**
     * This function will be used to get Patient all data from all new tables.
     * 
	 * @author 	Abhishek Singh<abhishek.singh@greenapplestech.com>
     * @param 	int $patientId
     * @param 	int $dataFlag 1->demo,2->responsible,3->employer,4->All	 	 
     * @return 	array $patientArray.
     */		
	function getPatientDatafromSepTbl($patientId,$dataFlag=4)
	{
		if(empty($patientId)) return false;
		$allPatientData = $this->CI->manageCommonTaskModel->getPatientAllDatafromSepTbl($patientId,$dataFlag);
        return $allPatientData[0];
	}
        
	function getPatientNameByAppntId($appId){
		$query1="select appointment_type,user_profile_id from ch_apsch_appointment where id='$appId'";
		$sql1=$this->CI->db->query($query1);
		if($sql1->num_rows() > 0){
			$res1=$sql1->row();
			$query2="select first_name,last_name, id from ch_user where id='$res1->user_profile_id'";
			$sql2=$this->CI->db->query($query2);
			if($sql2->num_rows() > 0){
				return $sql2->row();
			}
		}
	}
	
	function generateCcbillById($billId,$serviceId='',$oldBillData=array()) {
		$returnarray['status']=1;
		$returnarray['stmsg']='Bill generated Successfully';
		
		$this->CI =& get_instance();
		$this->CI->load->model('medisoft/MedisoftModel');
		$ccBillData=$this->CI->manageCommonTaskModel->getRecord("ch_charge_capture_bill",'*',array('id'=>$billId),'','','','array',0);

		$checkCodeDiagFlag = $this->checkServiceCodeDiagExistOrNot($billId,$serviceId);
		if($checkCodeDiagFlag){
			if(!empty($serviceId)){
				$ccServiceData=$this->CI->manageCommonTaskModel->getRecord("ch_charge_capture_service",'id,patient_code_level,date_of_service,sch_dept,sch_loc,ar_dept,ar_loc,apt_identifier,phy_ext_id,resorce_name,provider_id,resorce_id,procedure_flag',array('id'=>$serviceId,'bill_id'=>$ccBillData['id'],'delete_flag'=>'0'),'','','','array',1);
			}else{
				$ccServiceData=$this->CI->manageCommonTaskModel->getRecord("ch_charge_capture_service",'id,patient_code_level,date_of_service,sch_dept,sch_loc,ar_dept,ar_loc,apt_identifier,phy_ext_id,resorce_name,provider_id,resorce_id,procedure_flag',array('bill_id'=>$ccBillData['id'],'delete_flag'=>'0'),'','','','array',1);
			}
			 $facilityArr=$this->CI->manageCommonTaskModel->getRecord(TBL_FACILITY_MASTER,"*", array('id'=>$ccBillData['facility']),'','','','array','0');

			 $facilityName 	= $facilityArr['facility_name'];
			 $facilityId 	= $facilityArr['id'];
			 $facilityExtId = $facilityArr['external_id'];
			 
			 if(empty($facilityExtId)){
				$facilityExtId = str_pad($facilityId,'5','0', STR_PAD_LEFT);
			 }else{
				$facilityExtId = str_pad($facilityExtId,'5','0', STR_PAD_LEFT);
			 }
			 
            $diagGlobalSetting=$this->CI->manageCommonTaskModel->getGlobalSettings('1','ch_default_diagnosis_search_code');
            $diagCodetype=$diagGlobalSetting['ch_default_diagnosis_search_code'];
            if($diagCodetype=='2'){$diagCodetype='1';}
			$nomInc='1';
			
			foreach($ccServiceData as $key => $value){
				$checkInterfaceType = $this->checkCcBillGenerationEnableOrNot();
				if($checkInterfaceType['interfaceType'] == 'mckesson'){
					$chkApt = $this->checkInterFaceAndAppid($billId,$value['id']);
					if(!$chkApt){
						if($nomInc=='1'){
							$returnarray['stmsg']='Bill can not be generated becouse this encounter have not appointment mapping';
						}else{
							$returnarray['stmsg']='Bill can not be generated for all encounters becouse some of encounter dose not have appointment mapping';
						}
						$nomInc++;
						continue;
					}
				}
				

				$billData=$this->CI->manageCommonTaskModel->getRecord("ch_exint_xlink_bill",'*',array('cc_service_id'=>$value['id'],'billing_status'=>'2','active_flag !='=>'2'),'','','','array',1);
				if(!empty($billData)){
					$oldBillDataArr = $this->CI->manageCommonTaskModel->getRecord("ch_exint_xlink_bill",'*',array('cc_service_id'=>$value['id'],'billing_status'=>'2','active_flag !='=>'2','parent_id'=>'0'));
					$billGenDate = $oldBillDataArr['billing_date'];
					foreach($billData as $singleBillData){
						$this->CI->manageCommonTaskModel->updateRecord('ch_exint_xlink_bill',array('active_flag'=>'2'),array('id'=>$singleBillData['id']));
					}
				}
				
				if($oldBillData){
					$billGenDate = $oldBillData['billing_date'];
				}
				
				$serviceDataArr= $this->CI->manageCommonTaskModel->getCCprocidureIdByServiceId($value['id']);
				if($serviceDataArr){
					$parentId=0;
					foreach($serviceDataArr as $sKey =>$sVal){
						$billingData=array();
						if(($sVal['recordType']=='0')&&(!empty($sVal['code']))){
							/*data from the consult level master*/
							$patientCodeInfo = $this->CI->utilities->getPatientCodeLevelDetails($sVal['code']);
							$patientCodeName=$patientCodeInfo['consult_level'];
							$patientCodeLevel=$sVal['code'];
							$modId1 = $patientCodeInfo['mod1'];
							$modId2 = $patientCodeInfo['mod2'];
							$modId3 = $patientCodeInfo['mod3'];
							$modId4 = $patientCodeInfo['mod4'];
							$modName1 = $patientCodeInfo['mod_name1'];
							$modName2 = $patientCodeInfo['mod_name2'];
							$modName3 = $patientCodeInfo['mod_name3'];
							$modName4 = $patientCodeInfo['mod_name4'];
							$posVal = $patientCodeInfo['pos'];
							$tosVal = $patientCodeInfo['tos'];
							$billingData['patient_code_level']=$patientCodeName;
							$billingData['patient_code_level_id']=$patientCodeLevel;
							$billingData['procedure_id']='';
							$billingData['procedure_code']='';
							$billingData['procedure_name']='';
						}else{
							/*data from procedure master*/
							$procedureDataArr = $this->CI->manageCommonTaskModel->getCptCodeForLProcedureId($sVal['code'],$sVal['proce_code_type']);
							$modId1 = $procedureDataArr['pmod1'];
							$modId2 = $procedureDataArr['pmod2'];
							$modId3 = $procedureDataArr['pmod3'];
							$modId4 = $procedureDataArr['pmod4'];
							$modName1 = $procedureDataArr['mod_name1'];
							$modName2 = $procedureDataArr['mod_name2'];
							$modName3 = $procedureDataArr['mod_name3'];
							$modName4 = $procedureDataArr['mod_name4'];
							$tosVal = $procedureDataArr['ptos'];
							$posVal = $procedureDataArr['ppos'];
							$billingData['patient_code_level']='';
							$billingData['patient_code_level_id']='';
							$billingData['procedure_id']=$procedureDataArr['procedureId'];
							$billingData['procedure_code']=$procedureDataArr['procedureCode'];
							$billingData['procedure_name']=$procedureDataArr['procedureName'];
						}

						$billingData['procedure_flag']=$sVal['recordType'];
						$billingData['ccServiceId']=$value['id'];
						$billingData['provider_id']=$value['provider_id'];
						$billingData['resource_id']=$value['resorce_id'];
						$billingData['provider_suffix'] = '';
						$billingData['providercode']=str_replace('MG||||','',$this->CI->MedisoftModel->getProviderExternalIdByproviderId($value['provider_id']));
						if(empty($billingData['providercode'])){
							$billingData['providercode']= $value['provider_id'];
						}
						$billingData['visit_date'] = $value['date_of_service'];
						$billingData['patient_id'] = $ccBillData['patient_id'];
						$billingData['facilityName'] = $facilityName;
						$billingData['facilityId'] = $facilityId;
						$billingData['facilityExtId'] = $facilityExtId;
						$billingData['sch_dept'] = $value['sch_dept'];
						$billingData['sch_loc'] = $value['sch_loc'];
						$billingData['ar_dept'] = $value['ar_dept'];
						$billingData['ar_loc'] = $value['ar_loc'];
						$billingData['phy_ext_id'] = $value['phy_ext_id'];
						$billingData['resorce_name'] = $value['resorce_name'];
						
						$billingData['patient_code_modifier_id']=$modId1;
						$billingData['patient_code_modifier_two_id']=$modId2;
						$billingData['patient_code_modifier_three_id']=$modId3;
						$billingData['patient_code_modifier_four_id']=$modId4;
						$billingData['patient_code_modifier']=$modName1;
						$billingData['patient_code_modifier_two']=$modName2;
						$billingData['patient_code_modifier_three']=$modName3;
						$billingData['patient_code_modifier_four']=$modName4;
						$billingData['POS_id']=$posVal;
						$billingData['TOS_id']=$tosVal;
						$billingData['billing_date']=(($billGenDate)?$billGenDate:date("Y-m-d H:i:s"));
						
						$diagnosisVisitDataTemp = $sVal['problemArray'];
						$dcounter =0;
						unset($diagnosisArray);
						$diagnosisVisitData="";
						if($diagnosisVisitDataTemp){
							if(count($diagnosisVisitDataTemp) >0 ){
								foreach($diagnosisVisitDataTemp as $key1=>$value1){	
									$valuecodeNameArray = $this->CI->manageCommonTaskModel->getCorresDiagMapCode($value1,$diagCodetype);
									if($valuecodeNameArray){
										foreach($valuecodeNameArray as $code){
											$valuecode=$code['code'];     
											$diagnosisArray[$dcounter]['disease_id']=$code['id'];
											$diagnosisArray[$dcounter]['diseases_name']=$code['diseases_name'];
											$diagnosisArray[$dcounter]['code']=$valuecode;
											if($dcounter >=12) break;
											$dcounter++;
										}
									}
								}
								$diagnosisVisitData=$diagnosisArray;
							}else{
								$diagnosisVisitData='';
							}			
						}else{
							$diagnosisVisitData='';
						}
						$billingData['diagnosisData'] = $diagnosisVisitData;
						$validChk = $this->validateProciduresAndDiag($patientCodeLevel,$diagnosisVisitData,$billingData['procedure_id'],$value['procedure_flag'],$sVal['recordType']);

						if($validChk){
							$billingData['parent_id']=$parentId;
							$returnBillIdArray = $this->saveBillofChargeCapture($billingData);
							$returnBillId=$returnBillIdArray['bill_id'];
							if($parentId==0){
								$parentId = $returnBillIdArray['group_id'];
							}
							if($returnBillId){
								$this->CI->manageCommonTaskModel->updateRecord('ch_charge_capture_service',array('billing_status'=>'1'),array('id'=>$value['id']));
							}
							//Auto synching check 
							$checkAutoSent = $this->checkCcBillGenerationEnableOrNot();
							if($checkAutoSent['CCBillAutoSynchFlag']=='0'){
								$getBillRecord=$this->CI->manageCommonTaskModel->getRecord(TBL_EXINT_XLINK_BILL,'id,billing_type_p_use,bill_generate_through,billing_status', array('id' => $returnBillId)); 
								$this->resendBillInfo($returnBillId,$getBillRecord['billing_type_p_use'],$getBillRecord['billing_status'],$getBillRecord['bill_generate_through']);
							}
						}else{
							$returnarray['status']=0;
							$returnarray['stmsg']='Bills can not be generated! Some of the encounter do not have consult level/diagnosis code being recorded, please record and try again.';
						}
					}
				}else{
					$returnarray['status']=0;
					$returnarray['stmsg']='Bills can not be generated! Some of the encounter do not have consult level/diagnosis or procedure code being recorded, please record and try again.';
				}
			}
		}else{
			$returnarray['status']=0;
			$returnarray['stmsg']='Bills can not be generated! Some of the encounter do not have consult level/diagnosis or procedure code being recorded, please record and try again.';
		}
	return $returnarray;		
	}
	
	function saveBillofChargeCapture($billingData) {
		$this->CI =& get_instance();
		$this->CI->load->model('medisoft/MedisoftModel');
		$patientId =$billingData['patient_id'];
		$patientData=$this->CI->MedisoftModel->getPatientNode($patientId);
		$billTypeConfig = $this->checkCcBillGenerationEnableOrNot();
		if(empty($billingData['parent_id'])){
			$grpArray=array('patient_id'=>$patientId,
					'date_of_service'=>date("Y-m-d",strtotime($billingData['visit_date'])),
					'group_status'=>'0',
					'active_flag'=>'1',
					'cc_flag'=>'1',
					'date_added'=>date("Y-m-d H:i:s"),
					'added_by'=>$this->CI->session->userdata('id'));
			$grpId=$this->CI->manageCommonTaskModel->insertRecord("ch_xlink_bill_group",$grpArray);
		}else{
			$grpId=$billingData['parent_id'];
		}
		$patientBillingData = array(
			'bill_generate_through'=>$this->checkBillingInterfaceFlag(),
			'cc_bill_falg'=>'1',
			'cc_service_id'=>$billingData['ccServiceId'],
			'chart_number'=>$patientData['chart_number_org'],
			'patient_id'=>$patientId,
			'patient_external_id'=>$patientData['chart_number'],
			'patient_fname'=>$patientData['first_name'],
			'patient_mname'=>$patientData['middle_name'],
			'patient_lname'=>$patientData['last_name'],
			'patient_ssn'=>$patientData['ssn'],
			'patient_sex'=>$patientData['gender'],
			'patient_dob'=>date('Y-m-d',strtotime($patientData['date_of_birth'])),
			'patient_address1'=>$patientData['address'],
			'patient_address2'=>$patientData['correspondence_address'],
			'patient_city'=>$patientData['city'],
			'patient_state'=>$patientData['state'],
			'patient_state_short'=>$patientData['stateiso2'],
			'patient_zip_code'=>$patientData['zip'],
			'patient_work_phone'=>$patientData['office_desk_telephone'],
			'patient_home_phone'=>$patientData['home_phone'],
			'appointment_id'=>'',
			'visit_id'=>'',
			'date_of_visit'=>date("Y-m-d",strtotime($billingData['visit_date'])),
			'place_of_service_id'=>'21',
			'place_of_service_name'=>$billingData['facilityName'],
			'facility_id'=>$billingData['facilityId'],
			'facility_ext_id'=>$billingData['facilityExtId'],
			'procedure_flag'=>$billingData['procedure_flag'],
			'procedure_id'=>$billingData['procedure_id'],
			'procedure_code'=>$billingData['procedure_code'],
			'procedure_name'=>$billingData['procedure_name'],
			'patient_code_level_id'=>$billingData['patient_code_level_id'],
			'patient_code_level'=>$billingData['patient_code_level'],
			'patient_code_modifier_id'=>$billingData['patient_code_modifier_id'],
			'patient_code_modifier_two_id'=>$billingData['patient_code_modifier_two_id'],
			'patient_code_modifier_three_id'=>$billingData['patient_code_modifier_three_id'],
			'patient_code_modifier_four_id'=>$billingData['patient_code_modifier_four_id'],
			'patient_code_modifier'=>$billingData['patient_code_modifier'],
			'patient_code_modifier_two'=>$billingData['patient_code_modifier_two'],
			'patient_code_modifier_three'=>$billingData['patient_code_modifier_three'],
			'patient_code_modifier_four'=>$billingData['patient_code_modifier_four'],
			'POS_id'=>$billingData['POS_id'],
			'TOS_id'=>$billingData['TOS_id'],
			'provider_id'=>$billingData['provider_id'],
			'resource_id'=>$billingData['resource_id'],
			'provider_suffix'=>$billingData['provider_suffix'],
			'provider_external_id'=>$billingData['providercode'],
			'billing_type'=>'2',
			'billing_type_p_use'=>$billTypeConfig['billing_type_p_use'],
			'billing_date'=>$billingData['billing_date'],
			'billing_status'=>'2',
			'xml_file_name'=>'',
			'active_flag'=>'0',
			'sch_res_ex_id'=>$billingData['resorce_name'],
			'phy_ex_id'=>$billingData['phy_ext_id'],
			'sch_loc_ex_id'=>$billingData['sch_loc'],
			'sch_dept_ex_id'=>$billingData['sch_dept'],
			'ar_loc_ex_id'=>$billingData['ar_loc'],
			'ar_dept_ex_id'=>$billingData['ar_dept'],
                        'parent_id'=>$grpId,
			'added_by'=>  $this->CI->session->userdata('id'),
			'date_added'=>date("Y-m-d H:i:s"),
			'updated_by'=>'',
			'date_modified'=>''        			
		);  		
		$insertId=$this->CI->MedisoftModel->savePatientBillingInfo($patientBillingData);
		
		if($insertId && $billingData['diagnosisData']){
			$diagnosisVisitData=$billingData['diagnosisData'];
			$insertDiagnisisArray=array();
			$poolDignosis=array();
			if($diagnosisVisitData){
				if(!empty($diagnosisVisitData)){
					foreach ($diagnosisVisitData as $key => $value) {
						$insertDiagnisisArray[$key]['bill_id']=$insertId;
						$insertDiagnisisArray[$key]['diagnosis_id']=$value['disease_id'];
						$insertDiagnisisArray[$key]['diagnosis_code']=$value['code'];
						$insertDiagnisisArray[$key]['diagnosis_name']=$value['diseases_name'];
						$insertDiagnisisArray[$key]['diagnosis_order']=$key;
						$insertDiagnisisArray[$key]['date_added']=date("Y-m-d H:i:s");
						$insertDiagnisisArray[$key]['added_by']=$this->CI->session->userdata('id');
						
						$poolDignosis[$key]['bill_id']=$grpId;
						$poolDignosis[$key]['diag_id']=$value['disease_id'];
						$poolDignosis[$key]['diag_code']=$value['code'];
						$poolDignosis[$key]['diag_name']=$value['diseases_name'];
					}                                    
				}
			}
		    $this->saveCcdiaginPoolData($grpId,$poolDignosis);
		    $this->CI->MedisoftModel->savePatientDisseasesInfo($insertDiagnisisArray);
			//saveinsurance data
			$this->savePatientPrimaryInsurance($patientId,$insertId);
			$this->savePatientSecondaryInsurance($patientId,$insertId);
		}
			return array('bill_id'=>$insertId,'group_id'=>$grpId);
	}
	
	function savePatientPrimaryInsurance($patientId,$billId){

		$getPrimaryInsurance= $this->CI->manageCommonTaskModel->getPTPROInsuranceDetails($patientId,'0');
		if(!empty($getPrimaryInsurance)){
			if(empty($getPrimaryInsurance['payer_provider_carrier'])){
				return false;
			}
			$primaryArr=array(
				'bill_id'=>$billId,
				'payer_provider_carrier'=>$getPrimaryInsurance['payer_provider_carrier'],
				'insurance_payer_name'=>$getPrimaryInsurance['insurance_company_name'],
				'account_plan_name'=>$getPrimaryInsurance['account_plan_name'],
				'payer_type'=>$getPrimaryInsurance['payer_type_id'],
				'policy_number'=>$getPrimaryInsurance['policy_number'],
				'group_number'=>$getPrimaryInsurance['group_number'],
				'start_date'=>$getPrimaryInsurance['start_date'],
				'end_date'=>$getPrimaryInsurance['end_date'],
				'co_pay'=>$getPrimaryInsurance['co_pay'],
				'deductible'=>$getPrimaryInsurance['deductible'],
				'date_added'=>date("Y-m-d H:i:s"),
				'date_modified'=>date("Y-m-d H:i:s"),
				'added_by'=>$this->CI->session->userdata('id'),
				'updated_by'=>$this->CI->session->userdata('id'),	
				'active_flag'=>'0'
			);
			$this->CI->manageCommonTaskModel->insertRecord('ch_exint_xlink_bill_primary_insurance',$primaryArr);
		}else{
			return false;
		}
	}
	
	function savePatientSecondaryInsurance($patientId,$billId){

		$getSecondaryInsurance= $this->CI->manageCommonTaskModel->getPTPROInsuranceDetails($patientId,'1');
		if(!empty($getSecondaryInsurance)){
			if(empty($getSecondaryInsurance['payer_provider_carrier'])){
				return false;
			}
			$secondaryArr=array(
				'bill_id'=>$billId,
				'payer_provider_carrier'=>$getSecondaryInsurance['payer_provider_carrier'],
				'insurance_payer_name'=>$getSecondaryInsurance['insurance_company_name'],
				'account_plan_name'=>$getSecondaryInsurance['account_plan_name'],
				'payer_type'=>$getSecondaryInsurance['payer_type_id'],
				'policy_number'=>$getSecondaryInsurance['policy_number'],
				'group_number'=>$getSecondaryInsurance['group_number'],
				'start_date'=>$getSecondaryInsurance['start_date'],
				'end_date'=>$getSecondaryInsurance['end_date'],
				'co_pay'=>$getSecondaryInsurance['co_pay'],
				'deductible'=>$getSecondaryInsurance['deductible'],
				'date_added'=>date("Y-m-d H:i:s"),
				'date_modified'=>date("Y-m-d H:i:s"),
				'added_by'=>$this->CI->session->userdata('id'),
				'updated_by'=>$this->CI->session->userdata('id'),	
				'active_flag'=>'0'
			);
				$this->CI->manageCommonTaskModel->insertRecord('ch_exint_xlink_bill_secondary_insurance',$secondaryArr);
		}else{
			return false;
		}
	}
	
	function resendBillInfo($billId,$billingType,$billing_status,$billGenerateThrough)
	{
		$this->CI =& get_instance();
		$this->CI->load->library('xLink/xlink');
		$this->CI->load->model('medisoft/MedisoftModel');
		$this->CI->load->library('MedisoftInt/medisoftint');
		$this->CI->load->library('Kareo/kareo');
		$this->CI->load->library('Mbs/mbs');
		$this->CI->load->library('Mmd/mmd');
		$this->CI->load->library('MedgreBill/medgrebill');
		$this->CI->load->library('McKesson/mckesson');
		$getBillRecord=$this->CI->manageCommonTaskModel->getRecord(TBL_EXINT_XLINK_BILL,'*', array('id' => $billId));
		$appointmentId=$getBillRecord['appointment_id'];
		$patientId=$getBillRecord['patient_id'];
		$visitId=$getBillRecord['visit_id'];
		$lab_schedule_id=$getBillRecord['lab_schedule_id'];
		$getData['patient_code_label']=$getBillRecord['patient_code_level_id'];
		/* if(!empty($getData['patient_code_label'])){
		
			$updateBillArray['patient_code_level_id']=$getData['patient_code_label'];
			
			if(empty($getBillRecord['patient_code_level'])){
				$patientConsultMasterData = $this->CI->manageCommonTaskModel->getRecord(TBL_PATIENT_ONSULT_LEVEL_MASTER,"consult_level",array('id'=>$getData['patient_code_label']));
				if(!empty($patientConsultMasterData)){
					$updateBillArray['patient_code_level']=$patientConsultMasterData['consult_level'];
				}
			}else{
				$updateBillArray['patient_code_level']=$getBillRecord['patient_code_level'];
			}
			
			$this->CI->manageCommonTaskModel->updateRecord(TBL_EXINT_XLINK_BILL,$updateBillArray,array('id'=>$billId));
		} */
		if(empty($getData['patient_code_label'])){
			$returnError['status']='0';
			$returnError['status_msg']="Consult code is missing";
			echo json_encode($returnError);
			die;
		}
		$returnError['status']='1';
		$returnError['status_msg']='Bill has been send successfully.';
		if($billGenerateThrough=='1')
		{
			// for xlink
			$xLinkConfig = $this->CI->xlink->xLinkConfiguration();
			if($xLinkConfig['xlink_enable_flag']=='1'){
				if($billingType==0){
					
					if($xLinkConfig['medisoft_interface_flag']=='0'){
						$medisoftStatus=$this->CI->medisoftint->addPatientBillInMedisoft('',$billId,2);
						$returnError['status']=$medisoftStatus['status'];
						$returnError['status_msg']="Medisoft Sync:- ".$medisoftStatus['status_msg'];
					}else{
						$this->CI->xlink->createPatientBillXml($billId);
					}
				}else if($billingType==1){
					
					if($xLinkConfig['medisoft_interface_flag']=='0'){
						$medisoftStatus=$this->CI->medisoftint->addPatientBillInMedisoft('',$billId,2);
						$returnError['status']=$medisoftStatus['status'];
						$returnError['status_msg']="Medisoft Sync:- ".$medisoftStatus['status_msg'];
					}else{
						$this->CI->xlink->createPatientBillWithPiggyBackXml($billId);
					}
				}else if($billingType==2){
					
					if($xLinkConfig['medisoft_interface_flag']=='0'){
						$medisoftStatus=$this->CI->medisoftint->addPatientBillInMedisoft('',$billId,2);
						$returnError['status']=$medisoftStatus['status'];
						$returnError['status_msg']="Medisoft Sync:- ".$medisoftStatus['status_msg'];
					}else{
						$this->CI->xlink->createLabPatientBillXml($billId);
					}
				}
			}
		}
		else if($billGenerateThrough=='2')
		{
			//for kareo
			if($billingType=='0'){
				$resopnse=$this->CI->kareo->createBill($appointmentId,$action='Add',$billId);
			}else {
				$resopnse=$this->CI->kareo->createLabBill($lab_schedule_id,$action='Add',$billId);
			}
		
			if(empty($resopnse)){
                $updateStatus='1';
			}else{
				$updateStatus='0';
			}
            $kareoSyncFlag=$this->getSpecificFieldFromAnyTable("ch_user","kareo_sync_flag",$patientId);
            if($kareoSyncFlag=="0"){
                $updateStatus='0';
            }
            $consultLevelCount = $this->CI->manageCommonTaskModel->getRecord(TBL_PATIENT_CONSULT_LEVEL, "count(*) as num", array('chart_no' => $patientId, 'visit_id' => $visitId));
            if($consultLevelCount['num']=='0'){
                $updateStatus='0';
            }
			$updateArray['billing_status']=$updateStatus;
			$this->CI->manageCommonTaskModel->updateRecord(TBL_EXINT_XLINK_BILL,$updateArray,array('id'=>$billId));
			if($updateStatus=='1'){
				$returnError['status']='1';
				$logMessage ="Resend Patient Bill through Kareo";
				$this->CI->my_log->generateLog(1, '4', $logMessage, $log_comment, 1,$patientId, 'billing');
			}else{
				$returnError['status']='0';
				//$returnError['status_msg']=$this->lang->line('billNotGen');
			}
		}
		else if($billGenerateThrough=='3')
		{ 
			//for Mbs
			if($billingType=='0'){
                $billflagdata = $this->CI->manageCommonTaskModel->getRecord('ch_exint_xlink_bill',"cc_bill_falg",array("id"=>$billId));
				if($billflagdata['cc_bill_falg']=='1'){
                    $resopnse=$this->CI->mbs->createCcrBill($action='Add',$billId);
                }else{
                    $resopnse=$this->CI->mbs->createBill($appointmentId,$action='Add',$billId);
                }   
			}else {				
				$resopnse=$this->CI->mbs->createLabBill($appointmentId,$action='Add',$billId);
			}
		
			if(empty($resopnse)){
                $updateStatus='1';
			}else{
				$updateStatus='0';
			}
            $mbsSyncFlag=$this->getSpecificFieldFromAnyTable("ch_user","mbs_sync_flag",$patientId);
            if($mbsSyncFlag=="0"){
                $updateStatus='0';
            }
            $consultLevelCount = $this->CI->manageCommonTaskModel->getRecord(TBL_PATIENT_CONSULT_LEVEL, "count(*) as num", array('chart_no' => $patientId, 'visit_id' => $visitId));
            if($consultLevelCount['num']=='0'){
                $updateStatus='0';
            }
			$updateArray['billing_status']=$updateStatus;
			$this->CI->manageCommonTaskModel->updateRecord(TBL_EXINT_XLINK_BILL,$updateArray,array('id'=>$billId));
			if($updateStatus=='1'){
				$returnError['status']='1';
				$logMessage ="Resend Patient Bill through Mbs";
				$this->CI->my_log->generateLog(1, '4', $logMessage, $log_comment, 1,$patientId, 'billing');
			}else{
				$returnError['status']='0';
				//$returnError['status_msg']=$this->lang->line('billNotGen');
			}
		}

		else if($billGenerateThrough=='5')
		{ 
			//for Micro MD
			if($billingType=='0'){
                $billflagdata = $this->CI->manageCommonTaskModel->getRecord('ch_exint_xlink_bill',"cc_bill_falg",array("id"=>$billId));
				if($billflagdata['cc_bill_falg']=='1'){
                    $resopnse=$this->CI->mmd->createCcrBill($action='Add',$billId);
                }else{
                    $resopnse=$this->CI->mmd->createBill($appointmentId,$action='Add',$billId);
                }   
			}else {				
				$resopnse=$this->CI->mmd->createLabBill($appointmentId,$action='Add',$billId);
			}
		
			if(empty($resopnse)){
                $updateStatus='1';
			}else{
				$updateStatus='0';
			}
            $mbsSyncFlag=$this->getSpecificFieldFromAnyTable("ch_user","mbs_sync_flag",$patientId);
            if($mbsSyncFlag=="0"){
                $updateStatus='0';
            }
            $consultLevelCount = $this->CI->manageCommonTaskModel->getRecord(TBL_PATIENT_CONSULT_LEVEL, "count(*) as num", array('chart_no' => $patientId, 'visit_id' => $visitId));
            if($consultLevelCount['num']=='0'){
                $updateStatus='0';
            }
			$updateArray['billing_status']=$updateStatus;
			$this->CI->manageCommonTaskModel->updateRecord(TBL_EXINT_XLINK_BILL,$updateArray,array('id'=>$billId));
			if($updateStatus=='1'){
				$returnError['status']='1';
				$logMessage ="Resend Patient Bill through Mbs";
				$this->CI->my_log->generateLog(1, '4', $logMessage, $log_comment, 1,$patientId, 'billing');
			}else{
				$returnError['status']='0';
				//$returnError['status_msg']=$this->lang->line('billNotGen');
			}
		}


		else if($billGenerateThrough=='4')
		{ 
			// for McKession
			$mckessonStatus=$this->CI->mckesson->generatePatientBillForMcKesson('',$billId,2);
			$returnError['status']=$mckessonStatus['status'];
			$returnError['status_msg']="McKesson Sync:- ".$mckessonStatus['status_msg'];
		}
		else
		{ 
			$returnError['status']='0';
			$returnError['status_msg']='Bill was generated for Generic, which could not be Send.';
		}
		return $returnError;
	}
	
	function checkCcBillGenerationEnableOrNot()
	{
		$this->CI->load->model('xLink/xLinkModel');
		$this->CI->load->model('Medisoft/medisoftModel');
		$this->CI->load->model('Kareo/KareoModel');
		$this->CI->load->model('Mbs/MbsModel');
		$this->CI->load->model('mcKesson/mcKessonModel');
		
		$returnarray['CCBillEnable']='1';
		$returnarray['CCBillSynchFlag']='0';
		$returnarray['CCBillAutoSynchFlag']='1';
		$returnarray['billing_type_p_use']='0';
		$returnarray['interfaceType']='';
		// Xlink Configuration
		$xLinkConfig = $this->CI->xLinkModel->getXlinkConfiguration();
		// Medisoft Configuration
		$mediSoftConfig = $this->CI->medisoftModel->getXlinkConfiguration();
		// Kareo Confioguration.
		$KareoConfig = $this->CI->KareoModel->getKareoConfiguration();
		// MBS Configuration.
		$MbsConfig = $this->CI->MbsModel->getMbsConfiguration();
		// mcKesson Configuration.
		$mcKessonConfig = $this->CI->mcKessonModel->getMcKessonConfiguration();
		if($xLinkConfig['xlink_enable_flag']=='1'){
			$returnarray['CCBillEnable']='1';
			if($xLinkConfig['xlink_billing_sync_flag']=='1'){
				$returnarray['CCBillSynchFlag']='1';
			}
			if($xLinkConfig['xlink_billing_sync_manual_auto_flag']=='0'){
				$returnarray['CCBillAutoSynchFlag']='0';
			}
			if($xLinkConfig['xlink_demogra_other_sync_flag']=='1'){
				if(($xLinkConfig['xlink_demographic_synch_way']=='2' || $xLinkConfig['xlink_demographic_synch_way']=='3')){
					$returnarray['billing_type_p_use'] ="1";
				}else{
					$returnarray['billing_type_p_use'] ="0";
				}
			}else{
				$returnarray['billing_type_p_use'] ="0";
			}
			$returnarray['interfaceType']="xlink";
		}elseif($mediSoftConfig['xlink_enable_flag']=='1'){
			$returnarray['CCBillEnable']='1';
			if($mediSoftConfig['xlink_billing_sync_flag']=='1'){
				$returnarray['CCBillSynchFlag']='1';
			}
			if($mediSoftConfig['xlink_billing_sync_manual_auto_flag']=='0'){
				$returnarray['CCBillAutoSynchFlag']='0';
			}
			if($mediSoftConfig['xlink_demogra_other_sync_flag']=='1'){
				if(($mediSoftConfig['xlink_demographic_synch_way']=='2' || $mediSoftConfig['xlink_demographic_synch_way']=='3')){
					$returnarray['billing_type_p_use'] ="1";
				}else{
					$returnarray['billing_type_p_use'] ="0";
				}
			}else{
				$returnarray['billing_type_p_use'] ="0";
			}
			$returnarray['interfaceType']="medisoft";
		}elseif($KareoConfig['kareo_enable_flag']=='1'){
			$returnarray['CCBillEnable']='1';
			if($KareoConfig['kareo_billing_sync_flag']=='1'){
				$returnarray['CCBillSynchFlag']='1';
			}
			if($KareoConfig['kareo_billing_sync_manual_auto_flag']=='0'){
				$returnarray['CCBillAutoSynchFlag']='0';
			}
			if($KareoConfig['kareo_demogra_other_sync_flag']=='1'){
				if(($KareoConfig['kareo_demographic_synch_way']=='2' || $KareoConfig['kareo_demographic_synch_way']=='3')){
					$returnarray['billing_type_p_use'] ="1";
				}else{
					$returnarray['billing_type_p_use'] ="0";
				}
			}else{
				$returnarray['billing_type_p_use'] ="0";
			}
			$returnarray['interfaceType']="kareo";
		}elseif($MbsConfig['mbs_enable_flag']=='1'){
			$returnarray['CCBillEnable']='1';
			if($MbsConfig['mbs_billing_sync_flag']=='1'){
				$returnarray['CCBillSynchFlag']='1';
			}
			if($MbsConfig['mbs_billing_sync_manual_auto_flag']=='0'){
				$returnarray['CCBillAutoSynchFlag']='0';
			}
			if($MbsConfig['mbs_demogra_other_sync_flag']=='1'){
				if(($MbsConfig['mbs_demographic_synch_way']=='2' || $MbsConfig['mbs_demographic_synch_way']=='3')){
					$returnarray['billing_type_p_use'] ="1";
				}else{
					$returnarray['billing_type_p_use'] ="0";
				}
			}else{
				$returnarray['billing_type_p_use'] ="0";
			}
			$returnarray['interfaceType']="mbs";
		}elseif($mcKessonConfig['mckesson_enable_flag']=='1'){
			$returnarray['CCBillEnable']='1';
			if($mcKessonConfig['mckesson_billing_sync_flag']=='1'){
				$returnarray['CCBillSynchFlag']='1';
			}
			if($mcKessonConfig['mckesson_billing_sync_manual_auto_flag']=='0'){
				$returnarray['CCBillAutoSynchFlag']='0';
			}
			if($mcKessonConfig['mckesson_demogra_other_sync_flag']=='1'){
				if(($mcKessonConfig['mckesson_demographic_synch_way']=='2' || $mcKessonConfig['mckesson_demographic_synch_way']=='3')){
					$returnarray['billing_type_p_use'] ="1";
				}else{
					$returnarray['billing_type_p_use'] ="0";
				}
			}else{
				$returnarray['billing_type_p_use'] ="0";
			}
			$returnarray['interfaceType']="mckesson";
		}
		/* if($xLinkConfig['xlink_enable_flag']=='1' ||$mediSoftConfig['xlink_enable_flag']=='1' || $KareoConfig['kareo_enable_flag']=='1' || $MbsConfig['mbs_enable_flag']=='1' || $mcKessonConfig['mckesson_enable_flag']=='1'){
			$returnarray['CCBillEnable']='1';
			if($xLinkConfig['xlink_billing_sync_flag']=='1' ||$mediSoftConfig['xlink_billing_sync_flag']=='1' || $KareoConfig['kareo_billing_sync_flag']=='1' || $MbsConfig['mbs_billing_sync_flag']=='1' || $mcKessonConfig['mckesson_billing_sync_flag']=='1'){
				$returnarray['CCBillSynchFlag']='1';
				if($xLinkConfig['xlink_billing_sync_manual_auto_flag']=='0' ||$mediSoftConfig['xlink_billing_sync_manual_auto_flag']=='0' || $KareoConfig['kareo_billing_sync_manual_auto_flag']=='0' || $MbsConfig['mbs_billing_sync_manual_auto_flag']=='0' || $mcKessonConfig['mckesson_billing_sync_manual_auto_flag']=='0'){
					$returnarray['CCBillAutoSynchFlag']='0';
				}
				if($xLinkConfig['xlink_demogra_other_sync_flag']=='1' ||$mediSoftConfig['xlink_demogra_other_sync_flag']=='1' || $KareoConfig['kareo_demogra_other_sync_flag']=='1' || $MbsConfig['mbs_demogra_other_sync_flag']=='1' || $mcKessonConfig['mckesson_demogra_other_sync_flag']=='1'){	
					if(($xLinkConfig['xlink_demographic_synch_way']=='2' || $xLinkConfig['xlink_demographic_synch_way']=='3') || ($mediSoftConfig['xlink_demographic_synch_way']=='2' || $mediSoftConfig['xlink_demographic_synch_way']=='3') || ($KareoConfig['kareo_demographic_synch_way']=='2' || $KareoConfig['kareo_demographic_synch_way']=='3') || ($MbsConfig['mbs_demographic_synch_way']=='2' || $MbsConfig['mbs_demographic_synch_way']=='3') || ($mcKessonConfig['mckesson_demographic_synch_way']=='2' || $mcKessonConfig['mckesson_demographic_synch_way']=='3')){
						$returnarray['billing_type_p_use'] ="1";
					}else{
						$returnarray['billing_type_p_use'] ="0";
					}
				}else{
					$returnarray['billing_type_p_use'] ="0";
				}
				
			}				
			
		} */
		return $returnarray;
	}
	
	function checkServiceCodeDiagExistOrNot($billId,$serviceId)
	{
		$returnFlag = true;
		if(!empty($serviceId)){
			$ccServiceData=$this->CI->manageCommonTaskModel->getRecord("ch_charge_capture_service",'id,patient_code_level,date_of_service,procedure_flag',array('id'=>$serviceId,'bill_id'=>$billId,'delete_flag'=>'0'),'','','','array',1);
		}else{
			$ccServiceData=$this->CI->manageCommonTaskModel->getRecord("ch_charge_capture_service",'id,patient_code_level,date_of_service,procedure_flag',array('bill_id'=>$billId,'delete_flag'=>'0'),'','','','array',1);
		}
		
		
		foreach($ccServiceData as $key => $value){
			$patientCodeInfo='';
			if($value['procedure_flag']=='1'){
				$patientCodeInfo=$this->CI->manageCommonTaskModel->getRecord('ch_charge_capture_bill_code','id',array('service_id'=>$value['id'],'code_type'=>'2'),'','','','array',0);
				if(!$patientCodeInfo){
					return $returnFlag = false;
				}
			}else{
				if(empty($value['patient_code_level'])){
					return $returnFlag = false;
				}
				$patientCodeInfo=$this->CI->manageCommonTaskModel->getRecord('ch_charge_capture_bill_code','id',array('service_id'=>$value['id']),'','','','array',0);
				if(CH_CCR_DIG_DISABL=='0'){
					if(!$patientCodeInfo){
						return $returnFlag = false;
					}
				}
			}
		}
		return $returnFlag;
	}
	
	function checkBillingInterfaceFlag()
	{
		$this->CI->load->model('xLink/xLinkModel');
		$this->CI->load->model('Medisoft/medisoftModel');
		$this->CI->load->model('Kareo/KareoModel');
		$this->CI->load->model('Mbs/MbsModel');
		$this->CI->load->model('mcKesson/mcKessonModel');
		
		$billingInterface='0';

		// Xlink Configuration
		$xLinkConfig = $this->CI->xLinkModel->getXlinkConfiguration();
		// Medisoft Configuration
		$mediSoftConfig = $this->CI->medisoftModel->getXlinkConfiguration();
		// Kareo Confioguration.
		$KareoConfig = $this->CI->KareoModel->getKareoConfiguration();
		// MBS Configuration.
		$MbsConfig = $this->CI->MbsModel->getMbsConfiguration();
		// MBS Configuration.
		$mcKessonConfig = $this->CI->mcKessonModel->getMcKessonConfiguration();

		if($xLinkConfig['xlink_enable_flag']=='1'){
			$billingInterface='1';			
		}elseif($mediSoftConfig['xlink_enable_flag']=='1'){
			$billingInterface='1';
		}elseif($KareoConfig['kareo_enable_flag']=='1'){
			$billingInterface='2';
		}elseif($MbsConfig['mbs_enable_flag']=='1'){
			$billingInterface='3';
		}elseif($mcKessonConfig['mckesson_enable_flag']=='1'){
			$billingInterface='4';
		}
		
		return $billingInterface;
	}
	
	
	function getUserTypeIdByUserId($userId){
		if(empty($userId)) return false;
		$queryUserType="select user_type  from ch_user where  id='".$userId."'";
		$resultSetUserType=$this->CI->db->query($queryUserType);
		$userType = $resultSetUserType->row()->user_type;
		return $userType;
	}
	
	function createPatientInmedgre($dataArr = array()){
		
		$this->CI->load->model('userProfileManagement/' . $this->CI->session->userdata('userMainType') . '/manageUserModel');
		$this->CI->load->model('common/manageCommonTaskModel');
		
		$loginIdDataArray['first_name'] = $dataArr['first_name'];
		$loginIdDataArray['last_name'] = $dataArr['last_name'];
		$loginIdDataArray['email'] = '';
		
	// genrate chart number
		$settingType= $this->getGlobalSettings('1', 'ch_chart_number_setting_type');
        $externalChartId="";
		if($settingType['ch_chart_number_setting_type']=='0' || $settingType['ch_chart_number_setting_type']==''){
			//default setting 
			$chartNumber = $this->generateChartNumber(true);
			//$externalChartId="EX_".$chartNumber;
		}else if($settingType['ch_chart_number_setting_type']=='1'){
			// custom setting
			$chartNumber = $this->generateCustomizeChartNumber($loginIdDataArray['first_name'],$loginIdDataArray['last_name']);
			//$externalChartId="EX_".$chartNumber;
		}
		$loginIdDataArray['fixedId'] = $chartNumber;
		$loginIdDataArray['user_type_id'] = '7';	
		
		$data['max'] = $this->CI->manageUserModel->getMaxUserId(CH_USER_TABLE, '7', '1');
		$userId = $this->CI->manageUserModel->generateLoginId('1', $loginIdDataArray, $data['max']);

		$userPassword = $this->createRandomPassword();	
		
		$chdataArr['first_name']=$dataArr['first_name'];
		$chdataArr['last_name']=$dataArr['last_name'];
		$chdataArr['user_pass']=$userPassword;
		$chdataArr['user_type']='7';
		$chdataArr['user_id']=$userId;
		$chdataArr['external_id']=$externalChartId;
		$chdataArr['activation_key']='';
		$chdataArr['change_password_status']='0';
		$chdataArr['active_status']='1';
		$chdataArr['patient_type']='5';//for charge capture
		$chdataArr['last_profile_update']=date('Y-m-d H:i:s');
		$chdataArr['added_date']=date('Y-m-d H:i:s');
		$chdataArr['added_by']=date('Y-m-d H:i:s');
		
		$userCheck=$this->CI->manageCommonTaskModel->insertRecord(CH_USER_TABLE,$chdataArr);
		$insertId = $this->CI->db->insert_id();
		
		// Updating Profile Data in new tables.
		$profileDataArr = array(
			'patient_salutation'	=>  $dataArr['patient_salutation'],
			'first_name'	=>  $dataArr['first_name'],
			'middle_name' 	=>  $dataArr['middle_name'],
			'last_name'		=>  $dataArr['last_name'],
			'date_of_birth'	=>  $dataArr['date_of_birth'],
			'gender'  		=>  $dataArr['gender'],
			'chart_number'  =>  $chartNumber,
			'responsible_party_id'  =>'0',
			'employer_occupation'  =>'0'
		);
		$profileDataOtherArr = array(
			'127'	=>  $dataArr['patient_salutation'],
			'3'		=>  $dataArr['first_name'],
			'4' 	=>  $dataArr['middle_name'],
			'5'		=>  $dataArr['last_name'],
			'6'		=>  $dataArr['date_of_birth'],
			'29'  	=>  $dataArr['gender'],
			'93'  	=>  $chartNumber
		);
		if($insertId){
			$this->savePatientDataInSepTable($insertId,$profileDataArr);
			$this->CI->manageCommonTaskModel->updateRecord('ch_uaprm_user_type_login_id_format',array('max_inc_value'=>$data['max']),array('type_of_user'=>'1'));
		
		
			foreach($profileDataOtherArr as $key=>$val){
				$userDataArr = array(
					'user_id'=>$insertId,
					'user_field_id'=>$key,
					'field_value'=>$val,
					'date_added'=>date('Y-m-d H:i:s')
				);
				$this->CI->manageCommonTaskModel->insertRecord('ch_uaprm_user_profile_data',$userDataArr);
				$userDataArr = array();
			}
			$setingDataArr = array(
				'patient_id'=>$insertId,
				'preffered_pharmacy'=>'0',
				'appointment_reminder_flag'=>'1',
				'alert_reminder_email'=>'1',
				'alert_reminder_sms'=>'0',
				'alert_reminder_voice_mail'=>'0',
				'alert_reminder_postal_mail'=>'0',
                'clinical_reminder_flag'=>'0',
				'alert_clinical_reminder_email'=>'0',
				'alert_clinical_reminder_sms'=>'0',
				'alert_clinical_reminder_voice_mail'=>'0',
				'alert_clinical_reminder_postal_mail'=>'0',
				'immunization_registry_use'=>'1',
				'immunization_registry_sharing'=>'0',
				'health_information_exchang'=>'0',
				'patient_portal_enabled'=>'1',
				'date_added'=>now(),
				'date_modified'=>now(),
				'added_by'=>'0',
				'updated_by'=>'0'
			);
			$this->CI->manageCommonTaskModel->insertRecord('ch_ptpro_settings',$setingDataArr);
		}
		return $insertId;
	}
	
	function numberFormatter($number)
	{
		/*$formatter = new NumberFormatter('en_US', NumberFormatter::SPELLOUT);
		$formatter->setTextAttribute(NumberFormatter::DEFAULT_RULESET,'%spellout-ordinal');
		switch($number){
			case 1:
			$val='Primary';
			break;
			
			case 2:
			$val='Secondary';
			break;
			
			case 3:
			$val='Tertiary';
			break;
			
			default:
			$val=ucfirst($formatter->format($number));
			break;
		}
		return $val;*/
		$numberArray=array(0=>'Primary',
		1=>'Secondary',
		2=>'Tertiary',
		3=>'Fourth',
		4=>'Fifth',
		5=>'sixth',
		6=>'Seventh',
		7=>'Eighth',
		8=>'Ninth',
		9=>'Tenth',
		10=>'Eleventh',
		11=>'Twelfth',
		12=>'Thirteenth',
		13=>'Fourteenth',
		14=>'Fifteenth',
		15=>'Sixteenth',
		16=>'Seventeenth',
		17=>'Eighteenth',
		18=>'Nineteenth',
		19=>'Twentieth',
		20=>'Twenty First',
		21=>'Twenty Second',
		22=>'Twenty Third',
		23=>'Twenty Fourth',
		24=>'Twenty Fifth',
		25=>'Twenty Sixth',
		26=>'Twenty Seventh',
		27=>'Twenty Eighth',
		28=>'Twenty Ninth',
		29=>'Thirtieth');
		return $numberArray[$number];
	}

	function PatientFrom($patientType) 
	{  
	  if($patientType =='0'){
		  return 'Medgre';
	  }elseif($patientType =='1'){
		  return 'ARH';
	  }elseif($patientType =='2'){
		  return 'RGH';
	  }elseif($patientType =='3'){
		  return 'MBS';
	  }elseif($patientType =='4'){
		  return 'Medimobile';
	  }elseif($patientType =='5'){
		  return 'CCR';
	  }elseif($patientType =='6'){
		  return 'Medisoft';
	  }elseif($patientType =='7'){
		  return 'Kareo';
	  }elseif($patientType =='8'){
		  return 'Mckesson';
	  }elseif($patientType =='9'){
		  return 'Micro MD';
	  }else{
		  return '';
	  }

	}

	/**
     * This function will be used to get the patient type by patient Id.
     * 
	 * @author 	Abhishek Singh<abhishek.singh@greenapplestech.com>
     * @param 	int $patientId		 
     * @return 	string $patientType.
     */		
	function getPatientTypeByPatientId($patientId){
		
		if(empty($patientId)) return false;
		$patientTypeArray = $this->CI->manageCommonTaskModel->getRecord('ch_user', "patient_type", array('id' => $patientId));
		if(empty($patientTypeArray['patient_type']) && $patientTypeArray['patient_type']!='0'){
			$returnType=$this->PatientFrom('');
		}else{
			$returnType=$this->PatientFrom($patientTypeArray['patient_type']);
		}
		return $returnType;
	}

    
    public function getVisitTypeByAppId($appId='')
	{
		if(empty($appId)){
			return false;
		}
		$row=$this->CI->manageCommonTaskModel->getVisitTypeByAppId($appId);
		if($row){
			return $row->visit_type_text;
		}
	}
	
	function subgridCount($billId='')
	{
		$this->CI->load->model('chargecapture/chargecaptureModel');
		$userCheck=$this->CI->chargecaptureModel->subgridCount($billId);
		if($userCheck){
			return $userCheck;
		}else{
			return false;
		}
	}
	
	function updatePatientChartInt($patientId,$chartNumber)
	{
		if(empty($patientId) && empty($chartNumber)) return false;
		
		$patientDataArray['chart_int']=$chartNumber;
		$this->CI->manageCommonTaskModel->updateRecord('ch_uaprm_patient_profile_data',$patientDataArray,array('user_id'=>$patientId));
	}
	
	
	function getPerBillStatus($billId){
		 $dataRequired = $this->CI->manageCommonTaskModel->getRecord('ch_charge_capture_service', "billing_status", array("bill_id"=>$billId,'delete_flag'=>'0'), "", "", "", 'array', '1');
		 //print_r($billId);die;
		$dataArr = array();
		if($dataRequired){
			 foreach($dataRequired as $newarrData){
				$dataArr[] = $newarrData['billing_status'];
			} 
			$arrUni = array_unique($dataArr);
			if(count($arrUni)=='1' && $arrUni[0]=='1'){
				return "green";
			}else{
				return "gray";
			}
		}
	}
	
	function ecrypt_message($msg, $key)
	{
		$key = $key . "_connect_health-2012";
		$key = md5 ($key);
		
		# create a random IV to use with CBC encoding
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		
		# creates a cipher text compatible with AES (Rijndael block size = 128)
		# to keep the text confidential 
		# only suitable for encoded input that never ends with value 00h
		# (because of default zero padding)
		$ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $msg, MCRYPT_MODE_CBC, $iv);

		# prepend the IV for it to be available for decryption
		$ciphertext = $iv . $ciphertext;
		
		# encode the resulting cipher text so it can be represented by a string
		$ciphertext_base64 = base64_encode($ciphertext);

		return  $ciphertext_base64;
	}

	function decrypt_message($msg, $key)
	{
		$key = $key . "_connect_health-2012";
		$key = md5 ($key);

		$ciphertext_dec = base64_decode($msg);
		
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		# retrieves the IV, iv_size should be created using mcrypt_get_iv_size()
		$iv_dec = substr($ciphertext_dec, 0, $iv_size);
		
		# retrieves the cipher text (everything except the $iv_size in the front)
		$ciphertext_dec = substr($ciphertext_dec, $iv_size);

		# may remove 00h valued characters from end of plain text
		$plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);
		
		return  rtrim($plaintext_dec, "\0");
	}
	function getPayerNameByPatientId($patientId,$type)
	{
		$row=$this->CI->manageCommonTaskModel->getPayerNameByPatientId($patientId,$type);
		if($row){
			return $row->insurance_company_name;
		}
	}
	
	function getAppIdByPatId($patId,$date)
	{
		$row=$this->CI->manageCommonTaskModel->getAppIdByPatId($patId,$date);
		if($row){
			return $row;
		}
	}
	
	function referralDocumentSent($planId)
	{
		$row=$this->CI->manageCommonTaskModel->referralDocumentSent($planId);
		if($row){
			return $row;
		}
	}
    
    function getSpecimenForApptLabReport($labId){
        $row=$this->CI->manageGeneralReportsModel->getLabResult($labId);
		return $row;
    }
	
	/**
     * This function will be used to decrypt the license data using public key
     * Giridhar Shukla<giridhar.shukla@greenapplestech.com>
     * @param 	Encrypted data		 
     * @return 	decrypted data array
     */	
	function decryptLicenseDataByPublicKey($encrypted_key){
		if($encrypted_key){	
			$license_public_key = $this->getGlobalSettings('1', 'ch_license_public_key');
			
			$resPubKey = base64_decode($license_public_key['ch_license_public_key']);
			$license = base64_decode($encrypted_key);
			openssl_public_decrypt($license,$newsource,$resPubKey);
			$finalSource = json_decode(unserialize(gzuncompress($newsource)),true);
			return $finalSource;
		}else{
			return false;
		}
	}

	/**
     * 	Function		generateImmunizationDropDown
     * 	Author			Anshul Agarwal<anshul.agarwal@greenapplestech.com>
     * 	Description		this function will used to create the Immunization dropdown in the form and retur the key					   value pair of the same
	 *	ModBy			Rahul Anand<rahul.anand@greenapplestech.com>
	 *	Modification	Now you can add any field value in to title of option 
     * 	@param			$tableName => name of the table.
     *                          $key => option value field
     *                          $title=>option title field
     *                          $where => array for the where condition
     *                          $selectedArray=>array for the selected values in the dropdown
     * 	@return			dropdown.
     */
    function generateImmunizationDropDown($table, $key, $value, $where = null, $selectedArray = null,$check='0',$title='',$addfield='',$orderBy = array(),$addKey='') {
        $titleToAdd='';
		$keyValueArray = array();
        $this->CI->load->model('common/manageCommonTaskModel');
        if($check=='0'){
            $dataRequired = $this->CI->manageCommonTaskModel->getRecord($table, "*", $where,$orderBy, "", "", 'array', '1');
        }
        if($check=='1'){
            $dataRequired = $this->CI->manageCommonTaskModel->getRecord($table,$value, $where,$orderBy, "", "", 'array', '1');
        }
      
        $inital_value = 0;
        $data_str = '';
        $selected_data = array();

        if (isset($selectedArray['element'])) {
            $selected_data[] = $selectedArray['element'];
        } else if (count($selectedArray) > 0) {
            $selected_data = $selectedArray;
        }
        if ($dataRequired) {
            foreach ($dataRequired as $dataRow) {
				if(!empty($title)){
					if(array_key_exists($title,$dataRow)){
						$titleToAdd= $dataRow[$title];
					}
				}
				$extraString ='';
				
                if(!empty($addKey)){
                    $dataRow[$key]=$addKey."~".$dataRow[$key];
                }
                $keyValueArray[$dataRow[$key]] = trim($dataRow[$value]." ".$extraString);
                if(!empty($addfield)){
                    $dataRow[$value]=$dataRow[$addfield].' - '.ucfirst(trim($dataRow[$value]));
					
				}
                if ($inital_value == 0 && count($selectedArray) <= 0) {
                    $inital_value = $dataRow[$key];
                }
                if (!empty($selected_data) && in_array($dataRow[$key], $selected_data)) {
                    $data_str.="<option title='".$titleToAdd."' selected='selected' value='" . $dataRow[$key] . "'>" . trim($dataRow[$value]) . "</option>";
                } else {
                    $data_str.="<option title='".$titleToAdd."' value='" . $dataRow[$key] . "'>" . trim($dataRow[$value]) . "</option>";
                }
            }
        }
        return $dataDrop = array('data_list' => $data_str, 'key_value' => $keyValueArray);
    }
	
	function getCcBillDetailBySuperBill($sbillId){
		return $this->CI->manageCommonTaskModel->getCcBillDetailBySuperBill($sbillId);
	}
	
	function individuallyProcessDone($appId,$status)
	{
		$get= $this->CI->manageCommonTaskModel->individuallyProcessDone($appId,$status);
		$statusName=$this->processStatus($get['status_name']);
		if($get){
			if($get['added_by']==$this->CI->session->userdata('id')){
				$by=' by You';
			}else{
				$by=' by '.$get['first_name'].' '.$get['last_name'];
			}
			$at=$this->changeTimeFormatAsGloSet($get['date_added'],'H:i:s');
			$title=$statusName.' at '.$at.$by;
		}else{
			$title='';
		}
		return $title;
	}
	
	function getStatusInProcess($appId,$status)
	{
		$get= $this->CI->manageCommonTaskModel->getStatusInProcess($appId,$status);
		$statusName=$this->processStatus($get['status_name']);
		if($get){
			if($get['added_by']==$this->CI->session->userdata('id')){
				$by=' by You';
			}else{
				$by=' by '.$get['first_name'].' '.$get['last_name'];
			}
			$at=$this->changeTimeFormatAsGloSet($get['date_added'],'H:i:s');
			$title=$statusName.' started at '.$at.$by;
		}else{
			$title='';
		}
		return $title;
	}
	
	function getRescheduleDate($autoIncrIdStatus)
	{
		return $this->CI->manageCommonTaskModel->getRescheduleDate($autoIncrIdStatus);
	}
	
	function accessEditModule($accessType='default')
	{
		switch($accessType){
			case 'appt': return $this->getGlobalSettings('1', 'ch_access_appt','1'); break;
			case 'ins': return $this->getGlobalSettings('1', 'ch_access_ins','1'); break;
			case 'demo': return $this->getGlobalSettings('1', 'ch_access_demo','1'); break;
			default: return '1';
		}
	}

	function getClinicDetail($clinicId){
		$clinicInfo = $this->CI->manageCommonTaskModel->getRecord('ch_cinfs_clinic_profile', "*", array('id'=>$clinicId));
		return $clinicInfo;
	}

	function getPatientVisitDetail($patientId,$curVisitId){
		if($patientId =='0' || $patientId =='' || empty($patientId)){
			return false;
		}else{
			$patientVisit = $this->CI->manageCommonTaskModel->getRecord('ch_apsch_appointment_visit', "*", array('patient_id'=>$patientId), array('date_added'=>'DESC'));
			 if (!empty($patientVisit)) {
				$visitStr="<option value=''></option>";
				foreach ($patientVisit as $visitDetail) {
					if($curVisitId==$visitDetail['id']){
						continue;
					}
					$resourceId=$this->getResourceIdbyVisitId($visitDetail['id']);
					$visitStr .='<option value="'.$visitDetail['id'].'|'.$resourceId.'">'.date_format(date_create($visitDetail['date_added']),'m-d-Y')." - ".$this->shortenText($this->getProviderNamebyProviderId($resourceId), 10,true).'</option>';
				}
				return $visitStr;
				
			}else{
				return false;
			}
		}
	}
	
	function getLocation($all='')
	{
		return $this->CI->manageCommonTaskModel->getLocation($all);
	}
	
	function getAssocUserLocation($userId)
	{
		return $this->CI->manageCommonTaskModel->getAssocUserLocation($userId);
	}
	
	function getLocationName($locationId)
	{
		return $this->CI->manageCommonTaskModel->getLocationName($locationId);
	}
	
	function providerHoursListingDayWise($providerId,$dayId)
	{
		return $this->CI->manageCommonTaskModel->providerHoursListingDayWise($providerId,$dayId);
	}

	function getProviderList()
	{
		return $this->CI->manageCommonTaskModel->getProviderList();
	
	}
	
	function getOnlyResourecList()
	{
		$res = $this->CI->manageCommonTaskModel->getOnlyResourecList();
		$resourceArr = array();
		foreach($res as $resource){
			$resourceArr[$resource->id]=$resource->first_name ." ".$resource->last_name;
		}
		return $resourceArr;
	}

	function getClinicName($clinicId,$type){
		if($type=='0'){
			$clinicName = $this->CI->manageCommonTaskModel->getRecord('ch_cinfs_clinic_profile', "clinic_name", array('id'=>$clinicId));
			return $clinicName['clinic_name'];
		}else{
			$facName = $this->CI->manageCommonTaskModel->getRecord('ch_cinfs_facility_master', "facility_name", array('id'=>$clinicId));
			return $facName['facility_name'];
		}
	}
	
	function getFacilitiesList(){
		$facilityList = $this->CI->manageCommonTaskModel->getFacilitiesLists();
		return $facilityList;
	}
	
	function getPatientCodeLevelDetails($id){
		$pclData = $this->CI->manageCommonTaskModel->getPatientCodeLevelDetails($id);
		return $pclData;
	}
	
	function updateBillDiag($xlinkBillId,$servicId,$digwithProc){
		$delRec = $this->CI->manageCommonTaskModel->deleteRecord('ch_exint_xlink_bill_diagnosis',array('bill_id'=>$xlinkBillId));
			$diagnosisVisitDataTemp = $digwithProc;
			$diagGlobalSetting=$this->CI->manageCommonTaskModel->getGlobalSettings('1','ch_default_diagnosis_search_code');
            $diagCodetype=$diagGlobalSetting['ch_default_diagnosis_search_code'];
            if($diagCodetype=='2'){$diagCodetype='1';}
			$dcounter =0;
			unset($diagnosisArray);
			$diagnosisVisitData="";
			if($diagnosisVisitDataTemp){
				if(count($diagnosisVisitDataTemp) >0 ){
					foreach($diagnosisVisitDataTemp as $key1=>$value1){	
						$valuecodeNameArray = $this->CI->manageCommonTaskModel->getCorresDiagMapCode($value1,$diagCodetype);
						if($valuecodeNameArray){
							foreach($valuecodeNameArray as $code){
								$valuecode=$code['code'];
								$diagnosisArray[$dcounter]['disease_id']=$code['id'];
								$diagnosisArray[$dcounter]['diseases_name']=$code['diseases_name'];
								$diagnosisArray[$dcounter]['code']=$valuecode;
								if($dcounter >=12) break;
								$dcounter++;
							}
						}
					}
					$diagnosisVisitData=$diagnosisArray;
				}else{
					$diagnosisVisitData='';
				}			
			}else{
				$diagnosisVisitData='';
			}
			$insertDiagnisisArray=array();
			$poolDignosis=array();
			$grpIdArr = $this->CI->manageCommonTaskModel->getRecord('ch_exint_xlink_bill','parent_id',array('id'=>$xlinkBillId));
			if($diagnosisVisitData){
				if(!empty($diagnosisVisitData)){
					
					foreach ($diagnosisVisitData as $key => $value) {
						$insertDiagnisisArray[$key]['bill_id']=$xlinkBillId;
						$insertDiagnisisArray[$key]['diagnosis_id']=$value['disease_id'];
						$insertDiagnisisArray[$key]['diagnosis_code']=$value['code'];
						$insertDiagnisisArray[$key]['diagnosis_name']=$value['diseases_name'];
						$insertDiagnisisArray[$key]['diagnosis_order']=$key;
						$insertDiagnisisArray[$key]['date_added']=date("Y-m-d H:i:s");
						$insertDiagnisisArray[$key]['added_by']=$this->CI->session->userdata('id');
						
						$poolDignosis[$key]['bill_id']=$grpIdArr['parent_id'];
						$poolDignosis[$key]['diag_id']=$value['disease_id'];
						$poolDignosis[$key]['diag_code']=$value['code'];
						$poolDignosis[$key]['diag_name']=$value['diseases_name'];
						
					}
				}
			}
			$this->saveCcdiaginPoolData($grpIdArr['parent_id'],$poolDignosis);						
			$this->CI->medisoftModel->savePatientDisseasesInfo($insertDiagnisisArray);
		
	}
    
    public function formatLabel($text=""){
        return ucwords(strtolower(trim($text)));
    }
	
	function checkInterFaceAndAppid($billId,$serviceId){
		$chkAptIdentifire=$this->CI->manageCommonTaskModel->getRecord("ch_charge_capture_service",'id,apt_identifier',array('id'=>$serviceId,'bill_id'=>$billId,'delete_flag'=>'0'),'','','','array',1);
		if($chkAptIdentifire[0]['apt_identifier']){
			return true;
		}else{
			return false;
		}
	}

	function getDataFromRecon($reconId){
		$reconData=$this->CI->manageCommonTaskModel->getRecord("ch_pdedt_patient_details_adt_recon",'*',array('adt_file_id'=>$reconId),array('id'=>'desc'),'1','0','array',0);
		if($reconData){
			return $reconData;
		}else{
			return false;
		}
	}

	function getPatientIdByAppointmentId($appointmentId){
		$chk=$this->CI->manageCommonTaskModel->getRecord("ch_apsch_appointment",'user_profile_id',array('id'=>$appointmentId));
		if($chk['user_profile_id']){
			return $chk['user_profile_id'];
		}else{
			return false;
		}
	}

	/**
     * 	Function		generateProviderDiagnosisForWizard
     * 	Author			Shailesh Soni<shailesh.soni@greenapplestech.com>
     * 	Description		this function will used to create the preferred diagnosis list in the problem section of wizard
     * 	@return			Ul/LI List.
    */
	
    function generateProviderDiagnosisForWizard($providerId,$codetype, $defaultDaigId='',$inclusiveFreeText='0',$billFlag='0') {

        $this->CI->load->model('common/manageCommonTaskModel');
        $diagData = $this->CI->manageCommonTaskModel->getProviderDiagnosis($providerId, $codetype,$inclusiveFreeText,$billFlag);
		$data_str ='';
		$data_str.='<ul class="prfrdlistndform"  style="min-height: 300px;">';
        if ($diagData) {
			
			foreach ($diagData as $dataRow)
			{
				if($dataRow['is_billed'] == '1')
				{
					$billedStr='<span class="bold">$ - </span>';
					$isBilled='1';
				}	
				else
				{
					$billedStr='';
					$isBilled='0';
				}	
				
				$codeTypeStr='';
				if($dataRow['code_type']=='0')
					$codeTypeStr='ICD9';
				if($dataRow['code_type']=='1')
					$codeTypeStr='ICD10';
				if($dataRow['code_type']=='2')
					$codeTypeStr='SNOMEDCT';
		    if(empty($dataRow['abbr'])){
                        if(empty($dataRow['code']))
                        {
                            $data_str.='<li class="prfrdName cursor prfrdNameDig" codeType="'.$codeTypeStr.'" data-search-term="'.strtolower($dataRow['diseases_name']).'" id="'.$dataRow['diagnosis_id'].'" is-billed="'.$isBilled.'">'.$billedStr.''.$dataRow['diseases_name'].'</li>';
                        }
                        else
                        {

                            //$data_str.="<option value='" . $dataRow['diagnosis_id'] . "'>".$dataRow['code']." - ".$dataRow['diseases_name']."</option>";
                            $data_str.='<li class="prfrdName cursor prfrdNameDig" codeType="'.$codeTypeStr.'"    
                            data-search-term="' .strtolower($dataRow['code'].' - '.$dataRow['diseases_name']).'" id="'.$dataRow['diagnosis_id'].'" is-billed="'.$isBilled.'">'.$billedStr.''.$dataRow['code'].' - '.$dataRow['diseases_name'].'</li>';
                        }
                    }else{
                        if(empty($dataRow['code']))
                                            {
                            //$data_str.="<option value='" . $dataRow['diagnosis_id'] . "'>".$dataRow['diseases_name']."</option>";
                                                    $data_str.='<li class="prfrdName cursor prfrdNameDig" codeType="'.$codeTypeStr.'" data-search-term="('.strtolower($dataRow['abbr']).')' .strtolower($dataRow['diseases_name']).'" id="'.$dataRow['diagnosis_id'].'" is-billed="'.$isBilled.'">'.$billedStr.''.'('.$dataRow['abbr'].') '.$dataRow['diseases_name'].'</li>';
                        }
                                            else
                                            {

                            //$data_str.="<option value='" . $dataRow['diagnosis_id'] . "'>".$dataRow['code']." - ".$dataRow['diseases_name']."</option>";
                                                    $data_str.='<li class="prfrdName cursor prfrdNameDig" codeType="'.$codeTypeStr.'"    
                                                    data-search-term="('.strtolower($dataRow['abbr']).')' .strtolower($dataRow['code'].' - '.$dataRow['diseases_name']).'" id="'.$dataRow['diagnosis_id'].'" is-billed="'.$isBilled.'">'.$billedStr.''.'('.$dataRow['abbr'].') '.$dataRow['code'].' - '.$dataRow['diseases_name'].'</li>';
                        }
                }
                
            }
			
        }
		$data_str.='</ul>';
        return $data_str;
    }

	
	function getPatientResourceVisitDetail($patientId,$curVisitId){
		if($patientId =='0' || $patientId =='' || empty($patientId)){
			return false;
		}else{
			$patientVisit = $this->CI->manageCommonTaskModel->getRecord('ch_apsch_appointment_visit', "*", array('patient_id'=>$patientId), array('date_added'=>'DESC'));
			 if (!empty($patientVisit)) {
				$visitStr='';
				foreach ($patientVisit as $visitDetail) {
					if($curVisitId==$visitDetail['id']){
						continue;
					}
					$resourceId=$this->getResourceIdbyVisitId($visitDetail['id']);
					$visitStr .='<option value="'.$visitDetail['id'].'|'.$resourceId.'">'.date_format(date_create($visitDetail['date_added']),'m-d-Y')." - ".$this->shortenText($this->getResourceNameByVisitId($visitDetail['id']), 10,true).'</option>';
				}
				return $visitStr;
				
			}else{
				return false;
			}
		}
	}
	function getCatherMasterDesc($tableName,$masterId)
	{
		$description=$this->CI->manageCommonTaskModel->getRecord($tableName,'description',array('id'=>$masterId));
		if($description){
			return $description['description'];
		}
		else{
			return false;
		}
	}
	
	function saveCcdiaginPoolData($xlinkBillId,$diagArray){
		$this->CI->manageCommonTaskModel->saveBillPoolDiagnosis($xlinkBillId,$diagArray);
	}
	
	function getRvusForCc($billId,$serviceId=0){
		if($billId){
			return $this->CI->manageCommonTaskModel->getRvusForCc($billId,$serviceId);
		}else{
			return false;
		}
	}
	
	function getChargesForCc($billId,$serviceId=0){
		if($billId){
			return $this->CI->manageCommonTaskModel->getChargesForCc($billId,$serviceId);
		}else{
			return false;
		}
	}
	
	function formatPhoneNumerForDataBase($phoneNumber){
		if(!empty($phoneNumber))
			return $rPhone = str_replace(" ","",(str_replace("-","",(str_replace(")","",(str_replace("(","",$phoneNumber)))))));
		return false;
	}
	
	function getLastNextAppointment($userId,$mode){
		if($mode=='next'){
			$nextAppTime=$this->CI->manageCommonTaskModel->getRecord('ch_apsch_appointment','visit_date,user_profile_id',array('start_time >'=>date('Y-m-d H:i:s'),'user_profile_id'=>$userId),array('start_time'=>'asc'),'1');
			if($nextAppTime){
				return $nextAppTime['visit_date'];
			}else{
				return '---';
			}
		}
		if($mode=='last'){
			$lastDate=$this->CI->manageCommonTaskModel->getLastNextAppointment($userId);
			if($lastDate){
				return $lastDate;
			}else{
				return '---';
			}
		}
	}

	/**
     * 
     * Function Name		:getUserNameOnly
	 *	Author				Rahul Anand<rahul.anand@greenapplestech.com>
     */
	 function getResourceInfoList()
	 {
		
			$query="select id,
			concat(IFNULL((select field_value from ch_uaprm_user_profile_data where user_id=tbl1.id
				and user_field_id=3),''),' ',
				IFNULL((select field_value from ch_uaprm_user_profile_data 		where 	user_id=tbl1.id
				and user_field_id=5),'')
				) as provider_name
				 from ch_user tbl1
				where user_type!='7' and schedule_appointment='1' and 
			delete_flag!='1' and active_status='1'
			order by provider_name asc";
		

		$allResultSet= $this->CI->manageCommonTaskModel->executeQuery($query,false);
		if($allResultSet)
			return $allResultSet;
			return false;
	 }

	function getAppointmentIdByPatientId($patientId)
	{
		if($patientId!='' && $patientId!='0'){
			return $this->CI->manageCommonTaskModel->getAppointmentIdByPatientId($patientId);
		}else{
			return false;
		}
	}
	
	function getRosPosNeg($patientId,$childId,$visitId)
	{
		$getPosNeg = $this->CI->managePatientROSHistoryModel->getRosPosNeg($patientId,$childId,$visitId);
        return $getPosNeg;
	}
	
	function validateProciduresAndDiag($patientCodeLevel,$diagnosis,$procedure,$flag,$recordType){
		$validateChk=false;
		$rvuStatus = $this->getGlobalSettings(1, "ch_cc_rvu",1);
		if($rvuStatus=='1' || $rvuStatus=='2'){
			if(!empty($flag) && CH_CCR_DIG_DISABL=='1'){
				if(!empty($procedure)){
					$validateChk = true;
				}
			}if(!empty($flag) && CH_CCR_DIG_DISABL=='0'){
				if(!empty($procedure)){
					$validateChk = true;
				}
			}else if(empty($flag) && CH_CCR_DIG_DISABL=='1'){
				if((!empty($patientCodeLevel))){
					$validateChk = true;
				}
			}else if(empty($flag) && CH_CCR_DIG_DISABL=='0'){
				if($recordType == '0'){
					if(!empty($patientCodeLevel) && (isset($diagnosis) && $diagnosis!='')){
						$validateChk = true;
					}
				}else{
					if((!empty($procedure))){
						$validateChk = true;
					}
				}
			}
		}else{
			if($recordType == '0'){
				if((!empty($patientCodeLevel) && (isset($diagnosis) && $diagnosis!='')) || $procedure){
					$validateChk = true;
				}
			}else{
				if((!empty($procedure))){
					$validateChk = true;
				}
			}
		}
		return $validateChk;
	}
	
	function getStartEndDateLastTwoMonth(){
		$monthOnly = date('Y-m', strtotime(date('Y-m-d')));
		$lastMonth = date('Y-m', strtotime($monthOnly . ' -1 month'));
		$last2month = date('Y-m', strtotime($monthOnly . ' -2 month'));
		$dateArr = explode('-',$last2month);
		$lmdateArr = explode('-',$lastMonth);
		return array(
			'startdate'=>date('Y-m-d',strtotime("$dateArr[0]-$dateArr[1]")),
			'enddate'=>date('Y-m-t',strtotime("$lmdateArr[0]-$lmdateArr[1]"))
		);
	}
	
	function getLastVisitForOnlyEncounter($patientId,$currentVisitId){
		if($patientId && $currentVisitId){
			$appId=$this->CI->manageCommonTaskModel->getAppointmentIdByVisitId($currentVisitId);
			$query="select t1.id as prevVisitId,t2.id as prevApptId, t2.start_time
					from ch_apsch_appointment_visit t1
					join ch_apsch_appointment t2
					on t1.appointment_id=t2.id
					where patient_id='".$patientId."'
					and start_time<(select start_time from ch_apsch_appointment where id='".$appId->appointment_id."')
					order by start_time desc limit 1";
			$resultSet=$this->CI->db->query($query);
			if($resultSet->num_rows()>0){
				return $resultSet->row_array();
			}else{
				return false;
			}
		}
	}
	
	function generatePdfFile($clinicalSummaryReport, $otherDetail){
		$xml = new DOMDocument;
		$xml->loadXML($clinicalSummaryReport);
		$xsl = new DOMDocument;
		$xsl->load('uploads/reports/patientTransOfCareReport/CDA.xsl');
		$proc = new XSLTProcessor;
		$proc->importStyleSheet($xsl); // attach the xsl rules
		$strContent =  $proc->transformToXML($xml);
		$providerId=$otherDetail['providerId'];
		$patientId=$otherDetail['patientId'];
		$path=$otherDetail['path'];
		$pdfFileName=$otherDetail['pdfFileName'];
		$reportType=$otherDetail['reportType'];
		
		
		$userSelectedReportType=$reportType;
		//NOw get the setting of this type of report from dbase
		$paramArr=array($providerId,$userSelectedReportType);
		$result=$this->executeModelFunctionAndGetTheResultBack($paramArr,'getReportFormat');
		if($result){
			$headerEnable=$result->header;
			$footerEnable=$result->footer;
			$SignatureEnable=$result->signature;
		}
		
		$pdfHeader='';
		if(isset($headerEnable) && $headerEnable==1){
			$pdfHeader=$this->getPdfHeader($providerId,$patientId);
		}
		$pdfFooter='';
		if(isset($footerEnable) && $footerEnable==1){
			$pdfFooter=$this->getPdfFooter($providerId,$SignatureEnable);
		}
		
		$strContent = str_replace('<body>', $pdfHeader,$strContent);
		$strContent = str_replace('</body>', $pdfFooter,$strContent);
		$this->CI->load->library('mPDF');
		$mpdf=new mPDF(); 
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->WriteHTML($strContent);
		$mpdf->Output(ATTACHMENT_UPLOAD.$path.$pdfFileName,'F');
	}
	
	function getPdfHeader($providerId,$patientId)
	{
		$userInfo=$this->getReportSetting($providerId);
		$basicPatientInfo = $this->patientInfo($patientId);
		$clinicName='';
		$address='';
		$phone='';
		$email='';
		$city='';
        $header_line_2='';
		$fax='';
		$faxStr='';
		if($userInfo){
			$header_line_2=$userInfo->header_line_2;
			$address=$userInfo->address_1."&nbsp;".$userInfo->address_2.", ".$userInfo->city;
			if($userInfo->state){
				$address.=", ".$this->getSpecificFieldFromAnyTable(TBL_STATE,'state_name',$userInfo->state);
			}
			$phone =$this->convertPhoneMobileFaxFormat($userInfo->phone_1);
			$email=$userInfo->email;
			$doctorNameForSig=$userInfo->Signature_name;
			$fax =$this->convertPhoneMobileFaxFormat($userInfo->fax);
			
			if($fax){$faxStr= ", Fax: ".$fax;}
		}
		
		$header ="<body><table class='header_table'>
                <tr>
					<td width='20%' height='76px'>
						<img width='160px;' height='76px' src='".base_url().'uploads/reports/logos/'.$userInfo->header_image."'>
					</td>
					<td width='80%'>
						<table>
							<tr>
								<td width='40%'>
								</td>
								<td  width='60%'>
									<div style='float:left; width:100%; margin-top:5px;'>".$userInfo->header_line_1."<br>".$header_line_2."</div>
									<div style='float:left; width:100%; margin-top:5px;'>".$address."</div>
									<div style='float:left; width:100%; margin-top:5px;'>PH: ".$phone.$faxStr." </div>
									<div style='float:left; width:100%; margin-top:5px;'>".$email."</div>
								</td>
							</tr>
						</table>
					</td>
                </tr>
			</table>
			<table>
				<tr>
					<td  style='width:70%; height:30px; background-color:#ffffff; margin-top:5px;'>".
						$this->showPatientNameSetAsGlobalSetting($patientId)."
						(Chart # ".$basicPatientInfo['chart_number'].") - ".$this->computeAge($basicPatientInfo['date_of_birth']).", ".$basicPatientInfo['gender']."
					</td>
					<td  style='width:30%; height:30px; background-color:#ffffff; margin-top:5px; text-align:right;'>
						Date : ".$this->showDateForSpecificTimeZone(date('Y-m-d'))."
					</td>
				</tr>
			</table>";
		return $header;
	}
	
	function getPdfFooter($providerId,$SignatureEnable)
	{
		$userInfo=$this->getReportSetting($providerId);
		$footer='';
		if($userInfo){
			$footer=$userInfo->footer_1."<br>".$userInfo->footer_2;
			$footer=trim($footer,",");
			$signature_text=$userInfo->signature_text;
		}
		
		$footer ="<table class='header_table'>
			<tr>
				<td style='width:50%; background-color:#ffffff; text-align:left;'>".$footer."</td>
				<td style='width:50%; height:66px; background-color:#ffffff; text-align:right;'>";
				if(isset($SignatureEnable) && $SignatureEnable==1){
					$footer.="<img src='".base_url().'uploads/reports/signatures/'.$userInfo->signature_image."' width='280px;' height='66px'>";
				}
				$footer.="</td>
			</tr>
			<tr>
				<td style='width:70%; background-color:#ffffff;'>";
				if(isset($SignatureEnable) && $SignatureEnable==1){
					if(!isset($signatureTextDisable) && !$signatureTextDisable){
						$footer.=$signature_text;
					}
				}
				$footer.="</td>
				<td style='width:30%; vertical-align:middle; text-align:right; font-weight:bold; font-size:14px;'>".$userInfo->Signature_name."
				</td>
			</tr>
			<tr>
				<td style='width:100%; text-align:center;' colspan='2' >Powered By MedGre, www.medgre.com</td>
			</tr>
		</table></body>";
		
		return $footer;
	}
	
	function patientInfo($patientId)
	{
		$getInfo=$this->CI->manageCommonTaskModel->getRecord('ch_uaprm_patient_profile_data','chart_number,date_of_birth,gender',array('user_id'=>$patientId));
		return $getInfo;
	}
}
?>