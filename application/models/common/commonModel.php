<?php
/**
 * Product		booksrus
 * 		Common Tasks
 * File			commonModel
 * Author		Aijaz Ahmad <aijaz@collegebooksrus.com>
 * Copyright  	2017@booksrus.
 *
 * Short description for file
 *
 * History
 * Date 			? Author 			? Description
 *
 * */

/**
 * class			commonModel
 * author     		Aijaz Ahmad <aijaz@collegebooksrus.com>
 * Description	It uses for insert,update,fetch and delete the records.
 * */
class CommonModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
	}
	
	/**
     * function		getRecord
     * author     	Aijaz Ahmad <aijaz@collegebooksrus.com>
     * Description	It uses for  perticular number of record from the passed table.
     *
     * Parameters
     * 	$param  - $table  => name of the table
     *            $fields => list of the fields comma seprated
     *            $where  => where condition as associated array or well formatied condition string
     *            $orderBy => order by array eg. ('fieldName'=>'ASC/DESC')    
     *            $limit  => from where the indexiing will start
     *            $offset => no of records after the after the limit value
     *            $returnType => array/object    
     *            $formatOfRecordForSingle => 0/1 (0=>single,1=>array)  
     * 	Return	- return record
     * */
    
    function getRecord($table, $fields = "*", $where=null, $orderBy = array(), $limit = "", $offset = "", $returnType = "array",$formatOfRecordForSingle='0',$groupByArray=array()) {
        if (!empty($table)) {
            $this->db->select($fields,false);
            if (!empty($where)) {
                $this->db->where($where);
            }
            if (!empty($orderBy)) {
                foreach ($orderBy as $key => $value) {
					if($key=='custom'){
						$this->db->order_by($value);
					}else{
						$this->db->order_by($key, $value);
					}
                }
            }
            if ((!empty($limit)) && (empty($offset))) {
                $this->db->limit($limit);
            } else if ((!empty($limit)) && (!empty($offset))) {
                $this->db->limit($limit, $offset);
            }
            
            if(!empty($groupByArray)){
                $this->db->group_by($groupByArray); 
            }
            
            $query = $this->db->get($table);
            if ($query->num_rows() == 1) {
                if ($returnType == "array") {
                    if($formatOfRecordForSingle=='1'){
                        return $query->result_array();
                    } else {
                        return $query->row_array();
                    }
                } elseif ($returnType == "object") {
                    if($formatOfRecordForSingle=='1'){
                        return $query->result();
                    } else {
                        return $query->row();
                    }
                }
            } else if ($query->num_rows() > 1) {
                if ($returnType == "array") {
                    return $query->result_array();
                } elseif ($returnType == "object") {
                    return $query->result();
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
	
	/**
    * function		insertRecord
    * author     	Aijaz Ahmad <aijaz@collegebooksrus.com>
    * Description	It uses to insert the record
    *
    * Parameters
    * 	$param  - $table => name of the table
    *             $data = > associateive array (key=>value)    
    * 	Return	- true / false
    * */
    
    function insertRecord($table,$data) {
        if((!empty($table))&&(!empty($data))){
            $this->db->insert($table,$data);
            if ($this->db->affected_rows() > 0) {                
                return $this->db->insert_id();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
	
	function getLastInsertId() {
        return $this->db->insert_id();
    }
	
	/**
    * function		updateRecord
    * author     	Aijaz Ahmad <aijaz@collegebooksrus.com>
    * Description	It uses to update the record
    *
    * Parameters
    * 	$param  - $table => name of the table
    *             $data = > associateive array (key=>value)    
    *             $where => associative arary for the where condition  
    * 	Return	- true / false
    * */
    
    function updateRecord($table,$data=array(),$where=array()) {
		 if((!empty($table))&&(!empty($data))){
             if(!empty($where)){
				$this->db->where($where);
			 }
			 $this->db->update($table, $data);  
            return true;
        } else {
            return false;
        }
    }

    /**
    * function		deleteRecord
    * author     	Aijaz Ahmad <aijaz@collegebooksrus.com>
    * Description	It uses to delete the record
    *
    * Parameters
    * 	$param  - $table => name of the table
    *             $data = > associateive array (key=>value)    
    *             $where => associative arary for the where condition  or the condition string 
    * 	Return	- true / false
    * */
    
    function deleteRecord($table,$where=array()) {
        if(!empty($where)){
            $this->db->where($where);
        }
        if(!empty($table)){
             $this->db->delete($table);
            if ($this->db->affected_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
	
	/**
    * function		truncateTable
    * author     	Aijaz Ahmad <aijaz@collegebooksrus.com>
    * Description	It uses to truncate the table
    *
    * Parameters
    * 	$param  - $tableName => name of the table
    
    * 	Return	- true / false
    * */
    
    function truncateTable($tableName=""){
        if(!empty($tableName)){
            $this->db->truncate($tableName);
            return true;
        } else {
            return false;
        }
    }
	
	/*	
	Function		getStateName
	*	Author			Aijaz Ahmad <aijaz@collegebooksrus.com>
	*	Description		This will get and return the name of the state 
	*	@param			int(stateId)
	*	@return			String/false
	*/
	function getStateName($stateId=0) {
		if($stateId==0 or $stateId==''){
			return false;
		}
		$this->db->where('id',$stateId);
		$resultSet=$this->db->get(TBL_STATE);
		if($resultSet->num_rows()>0){
			return $resultSet->row()->state_name;
		}else{
			return false;
		}
		
	}
	
	
	/*	
	*	Function		getCountryName
	*	Author			Aijaz Ahmad <aijaz@collegebooksrus.com>
	*	Description		This will get and return the name of the state 
	*	@param			int(stateId)
	*	@return			String/false
	*/
	function getCountryName($countryId=0) {
		if($countryId==0 or $countryId==''){
			return false;
		}
		$this->db->where('id',$countryId);
		$resultSet=$this->db->get(TBL_COUNTRY);
		if($resultSet->num_rows()>0){
			return $resultSet->row()->country_name;
		}else{
			return false;
		}
		
	}
	
	/**
     * 	Function		getStateCountryByCity
     * 	Author			Aijaz Ahmad <aijaz@collegebooksrus.com>
     * 	Description		This Function will get all the country and state from the database on the bassis of city
     * 	@param			optionl/CityId/StateId
     * 	@return			Resultset/false
    */
	function getStateCountryByCity($cityId=0,$state=0) {
		$whereStr=' where 1=1 ';
		$groupBy='';
		if($cityId!=0 and $cityId!='')
		{
			$whereStr .=" and tbl1.id=$cityId";
		}
		if($state!=0 and $state!='')
		{
			$whereStr .=" and tbl2.id=$state";
		}
		
		$query="SELECT 
				tbl1.city_name,tbl1.id,tbl1.state_id,
				tbl2.id,tbl2.state_name,tbl2.country_id,
				tbl3.id,tbl3.country_name from ".TBL_CITY." tbl1 left join ".TBL_STATE." tbl2 on tbl1.state_id=tbl2.id left join ".TBL_COUNTRY." tbl3 on tbl2.country_id=tbl3.id" .$whereStr;
		$resultSet=$this->db->query($query);
		if($resultSet->num_rows()>0){
			return $resultSet->result();
		}else{
			return false;
		}
	}
	
	/**
     * 	Function		getCountryByState
     * 	Author			Aijaz Ahmad <aijaz@collegebooksrus.com>
     * 	Description		This Function will get all the country and state from the database on the bassis of city
     * 	@param			optionl/CityId/StateId
     * 	@return			Resultset/false
    */
	function getCountryByState($stateId=0) {
		$whereStr='';
		if($stateId!=0 and $stateId!=''){
			$whereStr =" where tbl2.id=$stateId";
		}
		$query="SELECT tbl2.id,tbl2.state_name,tbl2.country_id,tbl3.id,tbl3.country_name 
				from ".TBL_STATE." tbl2 left join ".TBL_COUNTRY." tbl3 on tbl2.country_id=tbl3.id " .$whereStr." group by tbl3.id";
		$resultSet=$this->db->query($query);
		if($resultSet->num_rows()>0){
			return $resultSet->result();
		}else{
			return false;
		}
	}
    /**
     * 	Function		getAllCountry
     * 	Author			Aijaz Ahmad <aijaz@collegebooksrus.com>
     * 	Description		This Function will get all the country from the database
     * 	@param			none
     * 	@return			Resultset/false
     */
    function getAllCountry() {
		$this->db->select('id,name',false);
        $this->db->where('active_flag', '1');
        $this->db->order_by('name','asc');
        $result_set = $this->db->get('countries');
        if ($result_set->num_rows() > 0){
            return $result_set->result();
        }else{
            return false;
		}
    }

    /**
     * 	Function		getAllState
     * 	Author			Aijaz Ahmad <aijaz@collegebooksrus.com>
     * 	Description		This Function will get all the state from the database
     * 	@param			optional (Country Id)
     * 	@return			Resultset/false
     */
    function getAllState($country_id = 0,$stateId=0) {
		$this->db->select('id,name',false);
        $this->db->where('active_flag', '1');
        $this->db->order_by('name','asc');
        if ($country_id != 0) {
            $this->db->where('country_id', $country_id);
        }
		if ($stateId != 0) {
            $this->db->where('id', $stateId);
        }
        $result_set = $this->db->get('states');
        if ($result_set->num_rows() > 0){
            return $result_set->result();
        }else{
            return false;
		}
    }

    /**
     * 	Function		getAllCity
     * 	Author			Aijaz Ahmad <aijaz@collegebooksrus.com>
     * 	Description		This Function will get all the City from the database
     * 	@param			optional (State Id)
     * 	@return			Resultset/false
     */
    function getAllCity($state_id = 0) {
		$this->db->select('id,name',false);
        $this->db->where('active_flag', '1');
		$this->db->order_by('name','asc');
        if ($state_id != 0) {
            $this->db->where('state_id', $state_id);
        }
        $result_set = $this->db->get('cities');
        if ($result_set->num_rows() > 0){
            return $result_set->result();
        }else{
            return false;
		}
    }
	
	/**
     * 	Function		getAllCity
     * 	Author			Aijaz Ahmad <aijaz@collegebooksrus.com>
     * 	Description		This Function will get all the City from the database
     * 	@param			optional (State Id)
     * 	@return			Resultset/false
     */
    function getListUnivesityByStatteId($state_id = 0) {
		$this->db->select('id,name',false);
        $this->db->where(array('approved'=>'1','active_flag'=>'1'));
		$this->db->order_by('name','asc');
        if ($state_id != 0) {
            $this->db->where('state', $state_id);
        }
        $result_set = $this->db->get('universities');
        if ($result_set->num_rows() > 0){
            return $result_set->result();
        }else{
            return false;
		}
    }
	
	function getservice($fromdate="",$todate=""){
		if(!empty($todate)&&!empty($fromdate)){
			$where = " and t2.service_date BETWEEN '".$fromdate."' and '".$todate."'";
		}else if(!empty($fromdate)){
			$where = " and t2.service_date <= '".$fromdate."'";
		}else{
			$where = "";
		}
		$query="select t1.id serId,t2.id serDetId,t2.done_status,t3.name custname,t3.mobile contact,t3.address,t4.name product,t5.name brand,t1.modelnumber,
			t1.sold_date purchase,t1.warranty,t1.guaranty,t2.service_date,t1.warranty_exp 
			from services t1 
			inner join service_details t2 on t1.id=t2.service_id 
			inner join customer t3 on t3.id=t1.customer_id 
			inner join product t4 on t4.id=t1.product_id
			inner join brand t5 on t5.id=t1.brand_id where status='1' and  warranty!='0' and  guaranty!='0' $where";
			//echo $query;
			$resultSet=$this->db->query($query);
		if($resultSet->num_rows()>0){
			return $resultSet->result_array();
		}else{
			return false;
		}
	}
	
	function getproduct($fromdate="",$todate="",$product="",$brand=""){
		$where = "";
		if(!empty($todate)&&!empty($fromdate)){
			$where = " and t1.sold_date BETWEEN '".$fromdate."' and '".$todate."'";
		}else if(!empty($fromdate)){
			$where = " and t1.sold_date <= '".$fromdate."'";
		}
		if($product){
			$where .= " and t2.id='".$product."'";
		}
		if($brand){
			$where .= " and t3.id='".$brand."'";
		}
		
		$query="select t1.id serId,t2.name product,t3.name brand,t1.modelnumber,
			t1.sold_date purchase,t1.warranty,t1.guaranty,t1.referral,t1.referral_other,
			(select count(id) as cnt From service_details where service_id=t1.id and done_status='0') as act
			from services t1 
			inner join product t2 on t2.id=t1.product_id
			inner join brand t3 on t3.id=t1.brand_id where status='1' $where";
			echo $query;
			$resultSet=$this->db->query($query);
		if($resultSet->num_rows()>0){
			return $resultSet->result_array();
		}else{
			return false;
		}
	}
	
	function getservicebyinput($inputval,$searchby){
		if($searchby=='1'){
			$wheresql = " and t3.mobile LIKE '%".$inputval."%'";
		}else if($searchby=='2'){
			$wheresql = " and t3.name LIKE '%".$inputval."%'";
		}else if($searchby=='3'){
			$wheresql = " and t3.address LIKE '%".$inputval."%'";
		}
		$query="select t1.id serId,t2.id serDetId,t2.done_status,t3.name custname,t3.mobile contact,t3.address,t4.name product,t5.name brand,t1.modelnumber,
			t1.sold_date purchase,t1.warranty,t1.guaranty,t2.service_date,t1.warranty_exp 
			from services t1 
			inner join service_details t2 on t1.id=t2.service_id 
			inner join customer t3 on t3.id=t1.customer_id 
			inner join product t4 on t4.id=t1.product_id
			inner join brand t5 on t5.id=t1.brand_id where status='1' and  warranty!='0' and  guaranty!='0' $wheresql";
			//echo $query;
			$resultSet=$this->db->query($query);
		if($resultSet->num_rows()>0){
			return $resultSet->result_array();
		}else{
			return false;
		}
	}
	
		
	function getServiceDataById($serId=0,$serDetId=0){
		if($serDetId){
			$query="select t1.id serId,t2.id serDetId,t2.done_status,t3.name custname,t3.mobile contact,t3.address,t4.name product,t5.name brand,t1.modelnumber,
			t1.sold_date purchase,t1.warranty,t1.guaranty,t2.service_date,t1.warranty_exp, t1.num_of_services,t1.duration,t3.user_image
			from services t1 
			inner join service_details t2 on t1.id=t2.service_id 
			inner join customer t3 on t3.id=t1.customer_id 
			inner join product t4 on t4.id=t1.product_id
			inner join brand t5 on t5.id=t1.brand_id where status='1' and t1.id=$serId and t2.id=$serDetId";
			$query = $this->db->query($query);
			if($query->num_rows() > 0){
				return $query->row_array();
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	
	function getUserDataById($id="",$fieldArr=array()){
		$retArr = array();
		if($id){
			$userData = $this->getRecord("userss","*",array("id"=>$id),"","","","array","0");
			print_r($userData);die;
			if($userData){
				if($fieldArr){
					foreach($fieldArr as $field){
						$retArr[$field] = $userData['$field'];
					}
					return $retArr;
				}else{
					return $userData;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	
	
	
}


?>