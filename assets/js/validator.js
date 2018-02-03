/**	Fucntion 	:	 	applyValidation
*	Author	 	: 		Rahul Anand<rahul.anand@greenapplestech.com>
*	Description	:		This is used to make fancy validation on fields
*/
function validateData(validArray,EvaluateMessage)
{
	var checkResult=true;
	var combinedValidStr='';
	if(typeof validArray=='undefined' || validArray==''){
		//then noo need to do anything just go back 
		return true;
	}
	
	for(inc=0;inc<validArray.length;inc++)
	{
		
		var validationType='';
		combinedValidStr='';
		var Mess='';
		if(typeof validArray[inc]=='undefined') //then just go back and continue
		continue;
		combinedValidStr=validArray[inc];
		arr=combinedValidStr.split(",");
		validationType=arr[0];
		//alert(validationType)
		FieldId=arr[1];
		//Apply Validation
		//Translate the validation type
		switch(validationType){
			case 'req':
			case 'required':
			validationType='required';
			break;
			case 'validValue':
			validationType='validValue';
			break;
			case 'email':
			validationType='custom[email]';
			break;
			case 'int':
			validationType='custom[integer]';
			break;
			case 'website':
			validationType='custom[url]';
			break;
			case 'landline':
			validationType='custom[phone]';
			break;
			case 'fax':
			validationType='custom[fax]';
			break;
			case 'mobile':
			validationType='custom[mobile]';
			break;
			case 'num':
			validationType='custom[num]';
			break;
			case 'ssnNumber':
			validationType='custom[ssnNumber]';
			break;
			case 'validateDrugNameOnlySelectedByUser':
			validationType='custom[validateDrugNameOnlySelectedByUser]';
			break;
            case 'validateDiagNameOnlySelectedByUser':
			validationType='custom[validateDiagNameOnlySelectedByUser]';
			break;
			case 'validateConSubOnlySelectedByUser':
			validationType='custom[validateConSubOnlySelectedByUser]';
			break;
			case 'float':
				validationType='custom[number]';
			break;
			case 'amount':
				validationType='custom[amount]';
			break;
			case 'alphanum':
				validationType='custom[alphanum]';
			break;
			case 'onlyNumberHypUnd':
				validationType='custom[onlyNumberHypUnd]';
			break;
			case 'strongPassword':
				validationType='custom[strongPassword]';
			break;
			case 'patientStrongPassword':
				validationType='custom[patientStrongPassword]';
			break;
			case 'externalId':
				validationType='custom[externalId]';
			break;
			case 'dateOfBirth':
			validationType='custom[dateOfBirth]';
			break;
			case 'onlyLetterNumberDot':
				validationType='custom[onlyLetterNumberDot]';
			break;
            case 'idleLockValidation':
			validationType='custom[idleLockValidation]';
			break;
			case 'paymentAmount':
			validationType='custom[paymentAmount]';
			break;
			case 'invalid_date':
			validationType='custom[invalid_date]';
			break;
			case 'date_not_greater_cur_date':
			validationType='custom[date_not_greater_cur_date]';
			break;
			case 'invalid_time':
			validationType='custom[invalid_time]';
			break;
			case 'max':
			var maxVal=arr[3];
            validationType='validate[max['+maxVal+']]';
            break;
            case 'min':
			var minVal=arr[3];
            validationType='validate[min['+minVal+']]';
            break;
			case 'str':
			validationType='custom[onlyLetterSp]';
			break;
			case 'minlength':
			case 'minLength':
			case "minlenth": 
			//This means user has passed the min length after the messsage
			if(typeof arr[3]!='undefined'){
				minLength=arr[3];
			}else{
				minLength=8;
			}
			validationType='validate[minSize['+minLength+']]';
			break;
			case 'checkUserExistence':
			validationType='validate[required,custom[checkUserExistence]]';
			break;
			case 'maxLength':
			case 'maxlength':
			//This means user has passed the min length after the messsage
			if(typeof arr[3]!='undefined'){
				maxLength=arr[3];
			}else{
				maxLength=8;
			}
			validationType='validate[maxSize['+maxLength+']]';
			break;
			case 'match':
			case 'matched':
			if(typeof arr[3]!='undefined'){
				matchedStr=arr[3];
				validationType='validate[equals['+matchedStr+']]';
			}
			break;
            case 'validateBp':
                validationType='custom[validateBp]';
            break;
            case 'check':
            break;
			default:
			validationType='custom['+validationType+']';
			break;
		}
		var oldAttr='';
		/* Just check that user want to evalute the messge varibale or not Added by Rahul anand*/
		/*
		if(typeof EvaluateMessage!='undefined' && EvaluateMessage==true){
			 Mess = eval(arr[2]);
		}else{
			 Mess = arr[2];
		}
		*/
		Mess = arr[2];
		if(typeof Mess=='undefined'){
			Mess='';
		}
		if($("#"+FieldId).attr('customerror')==1)	
		{
			if(typeof $("#"+FieldId).attr('customErrorMsg')!='undefined' && $("#"+FieldId).attr('customErrorMsg')!=''){
				
				 oldAttr=$("#"+FieldId).attr('customErrorMsg');
			}
			if(oldAttr.indexOf(validationType)==-1){
				$("#"+FieldId).attr('customErrorMsg',oldAttr+"~|~"+validationType+"|||"+Mess);
			}
		}
		$("#"+FieldId).addClass("validate["+validationType+"]");
		
		validateResult=$("#"+FieldId).validationEngine("validate");
		if(validateResult){
			checkResult=false;
		}
	}
	return checkResult;
	
}


function validateDataOld(dataArray,dontEvaluateMessage){ 
    
	var isFieldSet = false;
	var strMessage = '';
	var return_val = true;
	var focusId = '';

	for(i=0;i<dataArray.length;i++){
        
		result = dataArray[i].split(',');
		var validator = result[0];
		var	fieldId = result[1];
		var Mess='';
	    var objValue = $("#"+result[1]);
    /* Just check that user want to evalute the messge varibale or not Added by Rahul anand*/
		if(typeof dontEvaluateMessage!='undefined' && dontEvaluateMessage==true){
			 Mess = result[2];
		}else{
			 Mess = eval(result[2]);
		}
		if(validator == 'match'){
			var compFieldValue = $("#"+result[3]);
		}
                if(validator == 'minlenth'){
                        if((result[3])&&(result[3]!='undefined')){
                            var minLen = result[3];
                        } else {
                            var minLen = 8;
                        }
		}
		if(validator == 'check'){
			var checkedVal = $("input[name='"+result[1]+"']").serializeArray(); 
		}
        if(validator == 'reqBoard'){
			var checkedVal = $("#"+result[1]); 
		}
		if(validator=='specialReq'){
			var checkedVal = $("#"+result[1]); 
		}
		
	switch(validator){ 
			case "req": 
			case "required":{ 
                              
				 if(trimString(objValue.val())== null || trimString(objValue.val())== ""){
                 	strMessage += Mess+"<br>";
					return_val = false;
					if(isFieldSet == false){
						focusId = fieldId;
						isFieldSet = true;
					}
				} 
				break;             
			}                                                
			
			case "check":
			case "checked": { 			 
                                
				 if(checkedVal.length== 0){
                 	strMessage += Mess+"<br>";
					return_val = false;
					if(isFieldSet == false){
						focusId = fieldId;
						isFieldSet = true;
					}
				} 
				break;             
			}
            
            case "fckEditor": {
                    var PageValue=FCKeditorAPI.GetInstance(result[1]).GetXHTML(true);
                    if(PageValue == null || PageValue == ""){
                 	strMessage += Mess+"<br>";
					return_val = false;
					if(isFieldSet == false){
						focusId = fieldId;
						isFieldSet = true;
					}
				} 
				break;  
            }
                   
             case "reqBoard":{ 		 
				 if(checkedVal.val()!=1){	
                 	strMessage += Mess+"<br>";
					return_val = false;
					if(isFieldSet == false){
						focusId = fieldId;
						isFieldSet = true;
					}
				} 
				break;             
			}
			
			case "specialReq":{ 		 
				 if(checkedVal.val()==2){
				 return true;
                 	/*strMessage += Mess+"<br>";
					return_val = false;
					if(isFieldSet == false){
						focusId = fieldId;
						isFieldSet = true;}*/
					
				} 
				break;             
			}
               
                        
                       /* case "checkedRadio": { 
                            var chks = document.getElementsByName('workingDay[]');
                            var checkCount = 0;
                            for (var workingDayCounter = 0; workingDayCounter < chks.length; workingDayCounter++){
                                if (chks[workingDayCounter].checked){
                                    checkCount++;
                                }
                            }
                            if (checkCount < 1)
                            {
                                strMessage += Mess+"<br>";
                                return_val = false;
                                if(isFieldSet == false){
                                    focusId = fieldId;
                                    isFieldSet = true;
                                }
                                        }                            
                                        break;             
                            }*/
                        
                        case "checkedCheckBox": { 
                            var chks = document.getElementsByName('workingDay[]');
                            var checkCount = 0;
                            for (var workingDayCounter = 0; workingDayCounter < chks.length; workingDayCounter++){
                                if (chks[workingDayCounter].checked){
                                    checkCount++;
                                }
                            }
                            if (checkCount < 1)
                            {
                                strMessage += Mess+"<br>";
                                return_val = false;
                                if(isFieldSet == false){
                                    focusId = fieldId;
                                    isFieldSet = true;
                                }
                                        }                            
                                        break;             
                            }
                            
                    case "checkedPlateform" :{
                            var chks = document.getElementsByName('template[]');
                            var checkCount = 0;
                            for (var workingDayCounter = 0; workingDayCounter < chks.length; workingDayCounter++){
                                if (chks[workingDayCounter].checked){
                                    checkCount++;
                                }
                            }
                            if (checkCount < 1)
                            {
                                strMessage += Mess+"<br>";
                                return_val = false;
                                if(isFieldSet == false){
                                    focusId = fieldId;
                                    isFieldSet = true;
                                }
                                        }                            
                                        break;     
                            
                    }        
                            
                    case "checknext": { 
                        var sessionStart = objValue.val();
                        var sessionStart_next=eval(sessionStart)+1;               
                        var sessionEnd = $("#"+result[3]);
                        var sessionEndVal = sessionEnd.val();                			
                        if( (sessionEndVal > sessionStart) && (sessionEndVal == sessionStart_next)){
                        }else{
                            strMessage += Mess+"<br>";
                            return_val = false;
                            if(isFieldSet == false){
                                focusId = fieldId;
                                isFieldSet = true;
                            }
                        }    
                        break;
                    }                                                
            case "match":
            case "matched": { 
				var password = objValue.val();
				var confirm_password = compFieldValue.val();
				if(password != confirm_password){ 
					strMessage += Mess+"<br>";
					return_val = false;
					if(isFieldSet == false){
						focusId = fieldId;
						isFieldSet = true;
					}
				} 
				break;             
			}
                        
            case "minlenth": {
				var minlenth=(objValue.val().length);
                if(minlenth< minLen){
                    strMessage += Mess+"<br>";
					return_val = false; 
					if(isFieldSet == false){
						focusId = fieldId;
						isFieldSet = true;
					}
				}
				break;    
            }
            
            case "maxlenthone": {
				var maxlenth=(objValue.val().length);
                                
                if(maxlenth>1){
                    strMessage += Mess+"<br>";
					return_val = false; 
					if(isFieldSet == false){
						focusId = fieldId;
						isFieldSet = true;
					}
				}
				break;    
            }
			
			
			case "minItem": {
				var getItem=$("input[name="+fieldId+"]:checked").map(function () {return this.value;}).get().join(",");
				var countItem=getItem.split(',');
				var total=countItem.length;
				if((total< 2 && getItem!='') ||  total> 2){
                    strMessage += Mess+"<br>";
					return_val = false; 
					if(isFieldSet == false){
						focusId = fieldId;
						isFieldSet = true;
					}
				}
				break;    
            }
			
            case "zipcode": {
				var lenth=(objValue.val().length);
                if(lenth!=6){
                    strMessage += Mess+"<br>";
					return_val = false; 
					if(isFieldSet == false){
						focusId = fieldId;
						isFieldSet = true;
					}
				}
				break;    
            }
			
			case "num": 
			case "numeric": { 
				var charpos = objValue.val().search("[^0-9]"); 
               // alert(charpos);
                //alert(objValue.val());
                if(objValue.val().length > 0 &&  charpos >= 0){ 
					strMessage += Mess+"<br>";
                    return_val = false;
					if(isFieldSet == false){
						focusId = fieldId;
						isFieldSet = true;
					}
				}
				break;               
			}
			
			case "specialNum": 
			case "specialNumeric": { 
				var charpos = objValue.val().search("[^0-9]"); 
					if((objValue.val()!='' &&  objValue.val() > 6) || (objValue.val()!='' && objValue.val()< 4) || (objValue.val()!='' && charpos>=0)){
						strMessage += Mess+"<br>";
						return_val = false;
						if(isFieldSet == false){
							focusId = fieldId;
							isFieldSet = true;
						}
					}
				break;               
			}
			
            case "str": 
			case "string": { 
                    var charpos = objValue.val().search("[^A-Za-z ]"); 
                    if(objValue.val().length > 0 &&  charpos >= 0){ 

					strMessage += Mess+"<br>";
                    return_val = false;
					if(isFieldSet == false){
						focusId = fieldId;
						isFieldSet = true;
					}
				}
				break;             
			}

           
            case "password": { 
			var re = /^([a-zA-Z0-9]{6,10})$/;
                               // var re=/(?!^[0-9]*$)(?!^[.|*+(){}\[\]]*$)(?!^[a-zA-Z]*$)^([a-zA-Z0-9.|*+(){}\[\]]{6,})$/


				var newstr = trimAll(objValue.val());
    			if(!re.test(newstr)){
					strMessage += Mess+"<br>";
					return_val = false;	
					if(isFieldSet == false){
						focusId = fieldId;
						isFieldSet = true;
					}
				}
				break; 
			}
            
            
            case "alnum": 
			case "alphanumeric": { 
				var charpos = objValue.val().search("[^A-Za-z0-9]"); 
				if(objValue.val().length > 0 &&  charpos >= 0){
					strMessage += Mess+"<br>";
					return_val = false;	
					if(isFieldSet == false){
						focusId = fieldId;
						isFieldSet = true;
					}
				}
				break; 
			}
			
			case "float": { 
				var re = /^\d[0-9]*(\.\d{1,2})?$/;
				var newstr = trimAll(objValue.val());
    			if(!re.test(newstr)){
					strMessage += Mess+"<br>";
					return_val = false;	
					if(isFieldSet == false){
						focusId = fieldId;
						isFieldSet = true;
					}
				}
				break; 
			}

			case "isDate": { 
				re=/^\d{2}\-\d{2}\-[12][90][0-9][0-9]$/;
				if(objValue.val().length != 0){
					if( objValue.val() == objValue.val().match(re)){
						/*
						var dateCheck = dateValidate(objValue.val());
						if(!dateCheck){
							strMessage += Mess+"<br>";
							return_val = false;
						}*/
					}else{
						strMessage += Mess+"<br>";
						return_val = false;
						if(isFieldSet == false){
							focusId = fieldId;
							isFieldSet = true;
						}
					}
				}
				break;               
			}

			case "dateComp": { 
			 
			  var startDateArray = ((objValue.val()).split('-'));
			  var startDate = new Date(startDateArray[2], (startDateArray[1]-1), startDateArray[0]);			 
			  if(result[3] == 'currDate'){
				  var endDate = new Date();
			  }else{
				var endDateArray = (($("#"+result[3]).val()).split('-'));
				var endDate = new Date(endDateArray[2], (endDateArray[1]-1), endDateArray[0]);
			  }
				
			  if(result[4] == 'gret'){
				  if(startDate > endDate){
					strMessage += Mess+"<br>";
					if(isFieldSet == false){
						focusId = fieldId;
						isFieldSet = true;
					}
					return_val = false;
				  }
			  }
                          
              if(result[4] == 'less'){
				  if(startDate < endDate){
					strMessage += Mess+"<br>";
					if(isFieldSet == false){
						focusId = fieldId;
						isFieldSet = true;
					}
					return_val = false;
				  }
			  }
			   break;       
			               
			}

            case "dateCompTwoDate": { 			 
			var startDateTwoArray = ((objValue.val()).split('-'));
			var startDateTwo = new Date(startDateTwoArray[2], (startDateTwoArray[1]-1), startDateTwoArray[0]);			 			  
            var endDateTwoArray = (($("#"+result[3]).val()).split('-'));
            var endDateTwo = new Date(endDateTwoArray[2], (endDateTwoArray[1]-1), endDateTwoArray[0]);			 						  
                  if(startDateTwo > endDateTwo){
					strMessage += Mess+"<br>";
					if(isFieldSet == false){
						focusId = fieldId;
						isFieldSet = true;
					}
					return_val = false;
				  }
			   break;       			               
			}

            case "timeCompTwoTime": { 			 
			var startTimeTwoArray   = ((objValue.val()).split(':'));
			var startTimeHH         = eval(startTimeTwoArray[0]);
            var startTimeMM         = eval(startTimeTwoArray[1]);
            var startTime           = startTimeHH*60+startTimeMM;            
            var endTimeTwoArray     = (($("#"+result[3]).val()).split(':'));
            var endTimeTwoHH        = eval(endTimeTwoArray[0]);
            var endTimeTwoMM        = eval(endTimeTwoArray[1]);
            var endTime             = endTimeTwoHH*60+endTimeTwoMM;                 
                  if(endTime <= startTime){                      
					strMessage += Mess+"<br>";
					if(isFieldSet == false){
						focusId = fieldId;
						isFieldSet = true;
					}
					return_val = false;
				  }
			   break;       			               
			}
            
            case "campareValue": { 			 
			var ValueFrom      = eval(objValue.val());           
            var ValueTo        = eval($("#"+result[3]).val());                                  
                  if( ValueTo<=ValueFrom ){                      
					strMessage += Mess+"<br>";
					if(isFieldSet == false){
						focusId = fieldId;
						isFieldSet = true;
					}
					return_val = false;
				  }
			   break;       			               
			}
            
            case "campareTwoValue": { 			 
			var updatedTotalSubUnitPeriodOtherThanId      = eval(objValue.val());           
            var totalOfUnitPeriod        = eval($("#"+result[3]).val());
            var updateFieldSubUnitPeriod        = eval($("#"+result[4]).val());
                  var totalUpdationOfsubunitPeriod=updatedTotalSubUnitPeriodOtherThanId+updateFieldSubUnitPeriod;                  
                  if( totalUpdationOfsubunitPeriod > totalOfUnitPeriod ){                      
					strMessage += Mess+"<br>";
					if(isFieldSet == false){
						focusId = fieldId;
						isFieldSet = true;
					}
					return_val = false;
				  }
			   break;       			               
			}
            
			case "calDate": { 
				//re=/^\d{2}\/[12][90][0-9][0-9]$/;
				re=/^\d{1,2}\/\d{2,4}$/;
				if(objValue.val().length != 0){
					if( objValue.val() == objValue.val().match(re)){
						var sepDate = objValue.val().split('/');
						if(sepDate[0] < 1 || sepDate[0] > 12 || sepDate[1].length== 3 || sepDate[1]== 0){
							strMessage += Mess+"<br>";
							return_val = false;
						}
						if((sepDate[1].length== 2 && sepDate[1] > 8) || (sepDate[1].length== 4 && sepDate[1] > 2008)){
							strMessage += Mess+"<br>";
							return_val = false;
						}
						
					}else{
						strMessage += Mess+"<br>";
						return_val = false;
						if(isFieldSet == false){
							focusId = fieldId;
							isFieldSet = true;
						}
					}
				}
				break;               
			}

			case "email": { 
			var reg_exp = /^([_a-zA-Z0-9-]+)(\.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,3})$/;
				
				if (reg_exp.test(objValue.val()) == false && objValue.val()!=''){
					strMessage += Mess+"<br>";
					return_val = false;
					if(isFieldSet == false){
						focusId = fieldId;
						isFieldSet = true;
					}
				}
				break;               
			}
            
            
            case "mobile": { 
			var reg_exp_mobile = /^[789]\d{9}$/;
				
				if (reg_exp_mobile.test(objValue.val()) == false && objValue.val()!='' ){
					strMessage += Mess+"<br>";
					return_val = false;
					
				}
				break;               
			}
            
            case "landline": {
                    
           
			var reg_exp_landline = /^[0-9]\d{2,4}-\d{6,8}$/;
				 
				if (reg_exp_landline.test(objValue.val()) == false && objValue.val()!=''){
					strMessage += Mess+"<br>";
					return_val = false;
					if(isFieldSet == false){
						focusId = fieldId;
						isFieldSet = true;
					}
				}
				break;               
			}
            
            case "website": {
                    
           
			var reg_exp_website = /^(http\:\/\/)?www.[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}$/;
				 
				if (reg_exp_website.test(objValue.val()) == false && objValue.val()!=''){
					strMessage += Mess+"<br>";
					return_val = false;
					if(isFieldSet == false){
						focusId = fieldId;
						isFieldSet = true;
					}
				}
				break;               
			}
            
          

                                                               

		
		} // Switch Close
		
	} // For close
	
	if(return_val == false){
		setUIMessage('err', strMessage, focusId);
		return false
	}
	return true;
}



function trimAll(sString){
	while (sString.substring(0,1) == ' '){
		sString = sString.substring(1, sString.length);
	}
	while (sString.substring(sString.length-1, sString.length) == ' '){
		sString = sString.substring(0,sString.length-1);
	}
	return sString;
}


function dateValidate(date){  
	var MonthDays = Array();  
		MonthDays[0] = 31;  
		MonthDays[1] = 0;  
		MonthDays[2] = 31;  
		MonthDays[3] = 30;  
		MonthDays[4] = 31;  
		MonthDays[5] = 30;  
		MonthDays[6] = 31;  
		MonthDays[7] = 31;  
		MonthDays[8] = 30;  
		MonthDays[9] = 31;  
		MonthDays[10] = 30;  
		MonthDays[11] = 31;  
		
	var daysInMonth;  
	var aData = date.split('/');  
	
	var monthSelected = parseInt(aData[0], 10);  
	var daySelected = parseInt(aData[1], 10);  
	var yearSelected = parseInt(aData[2], 10);  
	
	if (monthSelected == 2)  
	daysInMonth = (((yearSelected % 4 == 0) && ((!(yearSelected % 100 == 0)) || (yearSelected % 400 == 0))) ? 29 : 28);  
	else  
	daysInMonth = MonthDays[monthSelected - 1];  
	
	if (daySelected < 1 || daySelected > daysInMonth)  
		return false;  
	if (monthSelected < 1 || monthSelected > 12)  
		return false;  
	if (yearSelected < 1)  
		return false;  
		
	return true;  
} 



function isEmail( strValue ){

	if ( strValue.length < 1 ){
		return true;
	}
	if ( strValue.length > 127 ){
		return false;
	}

	if (strValue.substr(strValue.length-1,1)==';'){
		strValue = strValue.substr(0,strValue.length-1);
	}

	list = strValue.split(";");

	if(strValue.indexOf(String.fromCharCode(47)) > -1){
		return false;
	}

	if(strValue.indexOf(String.fromCharCode(92)) > -1){
		return false;
	}

	for (var a = 1; a <= list.length; a++){
		strValue = list[a-1];
		dup_cnt = 0;
		for(cnt=0; cnt<strValue.length; cnt++){
			if(strValue.substr(cnt,1)=='@'){
				dup_cnt++;
			}
			if(dup_cnt > 1){
				return false;
			}
		}

		// Check if other characters are present on prefix or suffix.
		OtherChars = '@_%-.;';
		for(cnt=0; cnt<OtherChars.length; cnt++){
			char_to_check = OtherChars.substr(cnt,1);

			// Check if any character is prefixed to 'Other' character.
			for(_cnt=0; _cnt<OtherChars.length; _cnt++){
				if(strValue.indexOf(char_to_check + OtherChars.substr(_cnt,1),0) > -1){
					return false;
				}
			}

			// Check if any character is suffixed to 'Other' character.
			for(_cnt=0; _cnt<OtherChars.length; _cnt++){
				if(strValue.indexOf(OtherChars.substr(_cnt,1) + char_to_check,0) > -1){
					return false;
				}
			}

			// Check if any character is suffixed to Entered value.
			for(_cnt=0; _cnt<OtherChars.length; _cnt++){
				if(strValue.substr(0,1) == OtherChars.substr(_cnt,1) || strValue.substr(strValue.length-1,1) == OtherChars.substr(_cnt,1)){
					return false;
				}
			}
		}
		var reg_exp = /^[a-zA-Z]*([\.\-_]?\w+)+\@[\a-zA-Z0-9-_]+(\.[\a-zA-Z-_]+)+$/;
		if (!reg_exp.test(strValue)){
			return false;
		}
	}
	return true;
}


/* To trim the string*/

function trimString(inputString)
{

   if (typeof inputString != "string") { return inputString; }
   var retValue = inputString;
   var ch = retValue.substring(0, 1);
   while (ch == " ") {
      retValue = retValue.substring(1, retValue.length);
      ch = retValue.substring(0, 1);
   }
   ch = retValue.substring(retValue.length-1, retValue.length);
   while (ch == " ") {
      retValue = retValue.substring(0, retValue.length-1);
      ch = retValue.substring(retValue.length-1, retValue.length);
   }
   while (retValue.indexOf("  ") != -1) {
      retValue = retValue.substring(0, retValue.indexOf("  ")) + retValue.substring(retValue.indexOf("  ")+1, retValue.length);
   }
   return retValue;

}


//blank the input field of the from...
function blankData(getArr)
{
	for(i=0;i<getArr.length;i++){
		document.getElementById(getArr[i]).value='';
	}
}