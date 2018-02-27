$( document ).ready(function(){
	$(document.body).on('change','#cstmrImg', function(){
		var property = this.files[0];
		var imgName = property.name;
		var img_ext = imgName.split(".").pop().toLowerCase();
		if($.inArray(img_ext,['png','jpg','jpeg'])== -1){
			alert('Invalid image file');
			return false;
		}
		if(property.sige > 1024*1024){
			alert('Image file sige is very big');
			return false;
		}
		var form_data = new FormData();
		form_data.append("cstmrImg",property);
		$.ajax({
			type: "POST",
			url: base_url+'home/uploadUserImg',
			cache: false,
			contentType: false,
			processData: false,
			data: form_data,
			success: function(msg){
				var jsonObj = $.parseJSON(msg);
				if("error" == jsonObj.error){
					alert("Image not upload");
					$("#cstmrImg").val('');
				}else{
					$("#userImg").val(jsonObj.file_name);
				}
			},
			error : function(XMLHttpRequest, textStatus, errorThrown) {
				setUiMessege('err',errorThrown);
			}
		});
		
	});
	
	$("#addNewSer").click(function(){
		$.ajax({
			type: "POST",
			url: base_url+'home/openaddservice',
			data: {},
			success: function(msg){
				$("body").append(msg);
				$("#dateSold,#warrantyend").datepicker({
					changeMonth: true,
					changeYear: true,
					dateFormat:'dd-mm-yy'
				});
			},
			error : function(XMLHttpRequest, textStatus, errorThrown) {
				setUiMessege('err',errorThrown);
			}
		});
	});
	
	$(document.body).on('click', '#popupCloseCancel,#popupCloseCross', function(event) {
		$("body #addNewEntry").remove();
	});

	$(document.body).on('click', '#popupCloseCancel,#popupCloseCross', function(event) {
		$("body #viewEntry").remove();
	});
	
	$(document.body).on('click', '#popupCloseCancel,#popupCloseCross', function(event) {
		$('#passwrdPopup').hide();
	});
	
	$(document.body).on('keyup', '#cstmrSrch', function(event) {
		var inputval = $(this).val();
		var searchby = $("#cstmrSrchBy").val();
		setTimeout(function(){
			if(!!inputval){
				$.ajax({
					type: "POST",
					url: base_url+'home/getCstmerList',
					data: {
						inputval:inputval,
						searchby:searchby
					},
					success: function(msg){
						var retStr = "";
						var jsonObjArr = $.parseJSON(msg);
						if(!jQuery.isEmptyObject(jsonObjArr)){
							for( var i in jsonObjArr){
								var jsonObj = jsonObjArr[i];
								retStr+='<div class="cstmr-detal" onclick="javascript:getCstmrDetail(\''+jsonObj.id+'\',\''+jsonObj.name+'\',\''+jsonObj.mobile+'\',\''+jsonObj.address+'\')">';
								retStr+='<div class="cstmr-detal-lft">';
								retStr+='<span class="cstmr-pic">';
								retStr+='<img class="cstmr-pic-innr" src="'+ base_url + 'uploads/userimg/'+ (jsonObj.user_image ? jsonObj.user_image : '') +'">';
								retStr+='</span>';
								retStr+='<span class="cstmr-info">';
								retStr+='<span class="name">'+ (jsonObj.name ? jsonObj.name : '') +'</span>';
								retStr+='<span class="cntc-no">'+ (jsonObj.mobile ? jsonObj.mobile : '') +'</span>';
								retStr+='<span class="prdct">'+ (jsonObj.address ? jsonObj.address : '') +'</span>';
								retStr+='</span>';
								retStr+='</div>';
								retStr+='</div>';
							}
							$("#cstmrList").html(retStr);
						}else{
							$("#cstmrList").html("");
						}
					},
					error : function(XMLHttpRequest, textStatus, errorThrown) {
						setUiMessege('err',errorThrown);
					}
				});
			}
		}, 500);
	});
	
	
	$('#serviceDatecal').click(function(){
		$('#serviceDatelst').slideToggle('slow');	
	});
	
	$('.dt-sel').click(function(){
		$('#selCalVal').slideToggle('slow');	
	});
	
	$('.dd-listing').click(function(){
		var listVal = $(this).html();
		$('.filtertxt').html('');
		$('.filtertxt').text(listVal);
		$('#serviceDatelst').slideUp('slow');	
	});
	
	$('.prfl-pasrd-arw').click(function(){
		$('#changePaswrd').slideToggle('slow');	
	});
	
	$('.psrd-optn-listing').click(function(){
		$('#passwrdPopup').show();
		$('#changePaswrd').hide();	
	});
	
	$('.mblActnitem').click(function(){
		$('.mblActnlst').slideToggle('slow');	
	});
	$("#searchinput").on("keyup",function(){
		var inputval = $(this).val();
		var searchby = $(".searchby:radio:checked").val();
		setTimeout(function(){
			if(!!inputval){
				$.ajax({
					type: "POST",
					url: base_url+'home/getServiceListByInput',
					data: {
						inputval:inputval,
						searchby:searchby
					},
					success: function(msg){
						$("#defaultDataView").html(msg);
					},
					error : function(XMLHttpRequest, textStatus, errorThrown) {
						setUiMessege('err',errorThrown);
					}
				});
			}else{
				getDefaultData();
			}
		}, 500);
	});
	
	$("#signOut").click(function(){
		$.ajax({
			type: "POST",
			url: base_url+'home/signOut',
			data: {},
			success: function(msg){
				window.location = base_url;
			},
			error : function(XMLHttpRequest, textStatus, errorThrown) {
				setUiMessege('err',errorThrown);
			}
		});
	});
});



function getBrandList(prodId){
	$.ajax({
		type: "POST",
		url: base_url+'home/getBrandList',
		data: {
			prodId:prodId
		},
		success: function(msg){
			$("#brand").html(msg);
		},
		error : function(XMLHttpRequest, textStatus, errorThrown) {
			setUiMessege('err',errorThrown);
		}
	});
}

function saveeservicepopup(){
	 
	var validateArray = Array();
		validateArray.push("req,product");		
		/* validateArray.push("req,brand");		
		validateArray.push("req,warranty");		
		validateArray.push("req,dateSold");		
		validateArray.push("req,services");		
		validateArray.push("req,duration");
		validateArray.push("req,name");		 */
	if(validateData(validateArray)){
		return false;
	}else{
		$.ajax({
			type: "POST",
			url: base_url+'home/service',
			data: {
				product:$("#product").val(),
				brand:$("#brand").val(),
				modelNum:$("#modelNum").val(),
				dateSold:$("#dateSold").val(),
				guaranty:$("#guaranty").val(),
				warranty:$("#warranty").val(),
				services:$("#services").val(),
				duration:$("#duration").val(),
				name:$("#name").val(),
				addr:$("#addr").val(),
				mobile:$("#mobile").val(),
				note:$("#note").val(),
				userImg:$("#userImg").val(),
				referral:$("#referral").val(),
				referralotr:$("#referralotr").val()
			},
			success: function(msg){
				var jsonObj = $.parseJSON(msg);
				if(jsonObj.status=="success"){
					closeservicepopup();
					//setUiMessege('suc',jsonObj.msg);
					window.location = base_url+"home";
				}else{
					//setUiMessege('err',jsonObj.msg);
				}
			},
			error : function(XMLHttpRequest, textStatus, errorThrown) {
				setUiMessege('err',errorThrown);
			}
		});
	}
}

function closeservicepopup(){
	$("body #addNewEntry").remove();
}

function viewservic(id){
	$.ajax({
		type: "POST",
		url: base_url+'home/viewservice',
		data: {
			id:id
		},
		success: function(msg){
			$("body").append(msg);
			$("#sdate").datepicker({
				changeMonth: true,
				changeYear: true,
				dateFormat:'dd-mm-yy'
			});
		},
		error : function(XMLHttpRequest, textStatus, errorThrown) {
			setUiMessege('err',errorThrown);
		}
	});
}

function editservic(id){
	$.ajax({
		type: "POST",
		url: base_url+'home/editservice',
		data: {
			id:id
		},
		success: function(msg){
			$("body").append(msg);
			$("#sdate").datepicker({
				changeMonth: true,
				changeYear: true,
				dateFormat:'dd-mm-yy'
			});
		},
		error : function(XMLHttpRequest, textStatus, errorThrown) {
			setUiMessege('err',errorThrown);
		}
	});
}

function updateservicepopup(id){
	var validateArray = Array();
		validateArray.push("name");
		validateArray.push("contact");
		validateArray.push("sdate");
		validateArray.push("address");
	if(validateinput(validateArray)){
		return false;
	}else{
		$.ajax({
			type: "POST",
			url: base_url+'home/updateservice',
			data: {
				id:id,
				name:$("#name").val(),
				contact:$("#contact").val(),
				sdate:$("#sdate").val(),
				address:$("#address").val()
			},
			success: function(msg){
				var jsonObj = $.parseJSON(msg);
				if(jsonObj.status=="success"){
					closeservicepopup();
					setUiMessege('suc',jsonObj.msg);
					window.location = base_url+"home";
				}else{
					setUiMessege('err',jsonObj.msg);
				}
			},
			error : function(XMLHttpRequest, textStatus, errorThrown) {
				setUiMessege('err',errorThrown);
			}
		});
	}
}

function deleteservic(id){	
	swal({
	  title: "Are you sure?",
	  text: "Do you want to delete this service!",
	  type: "warning",
	  showCancelButton: true,
	  confirmButtonColor: "#DD6B55",
	  confirmButtonText: "Yes, delete it!",
	  closeOnConfirm: false
	},
	function(){
		$.ajax({
			type: "POST",
			url: base_url+'home/deleteservic',
			data: {
				id:id
			},
			success: function(msg){
				var jsonObj = $.parseJSON(msg);
				if(jsonObj.status=="success"){
					setUiMessege('suc',jsonObj.msg);
					swal("Deleted!", "Service has been deleted.", "success");
					window.location = base_url+"home";
				}else{
					setUiMessege('err',jsonObj.msg);
				}
			},
			error : function(XMLHttpRequest, textStatus, errorThrown) {
				setUiMessege('err',errorThrown);
			}
		});
	});
}


 function viewDetail(serId,serDetId){
	$.ajax({
		type: "POST",
		url: base_url+'home/viewDetail',
		data: {
			serId:serId,
			serDetId:serDetId
		},
		success: function(msg){
			$("body").append(msg);
		},
		error : function(XMLHttpRequest, textStatus, errorThrown) {
			setUiMessege('err',errorThrown);
		}
	});
 }

 function updateService(){
	$.ajax({
		type: "POST",
		url: base_url+'home/updateEntry',
		data: {
		},
		success: function(msg){
			$("body").append(msg);
		},
		error : function(XMLHttpRequest, textStatus, errorThrown) {
			setUiMessege('err',errorThrown);
		}
	});
 }
 
function getServiceList(val,text){
	$("#dateFltrText").text(text);
	$.ajax({
		type: "POST",
		url: base_url+'home/getServiceList',
		data: {
			type:val
		},
		success: function(msg){
			$("#defaultDataView").html(msg);
		},
		error : function(XMLHttpRequest, textStatus, errorThrown) {
			setUiMessege('err',errorThrown);
		}
	});
}

function getDefaultData(){
	$.ajax({
		type: "POST",
		url: base_url+'home/getDefaultData',
		data: {},
		success: function(msg){
			$("#defaultDataView").html(msg);
		},
		error : function(XMLHttpRequest, textStatus, errorThrown) {
			setUiMessege('err',errorThrown);
		}
	});
}

function getDefaultProductData(){
	$.ajax({
		type: "POST",
		url: base_url+'product/getDefaultProductData',
		data: {},
		success: function(msg){
			$("#defaultProductView").html(msg);
		},
		error : function(XMLHttpRequest, textStatus, errorThrown) {
			setUiMessege('err',errorThrown);
		}
	});
}

function activateHeadMeanu(idArr){
	$("#"+idArr).addClass("tb-actv");
}

function getCstmrDetail(cstmrId,name,mobile,addr){
	$("#name").val( !!name ? name : '' );
	$("#mobile").val( !!mobile ? mobile : '' );
	$("#addr").val( !!addr ? addr : '' );
}

function deleteSerRow(serId,SerDetId){
	createConfirmAlert("Are you sure?","Once deleted, you will not be able to recover this service!",deleteRowConfirm,[serId,SerDetId])
}

function deleteRowConfirm(argsArr){
	
	$.ajax({
		type: "POST",
		url: base_url+'home/deleteSerRow',
		data: {
			serId:argsArr[0],
			serDetId:argsArr[1]
		},
		success: function(msg){
			var jsonObj = $.parseJSON(msg);
			if(jsonObj.status =="0"){
				setUiMessege('err',errorThrown);
			}else{
				setUiMessege('suc',jsonObj.msg);
				location.reload(true);
			}
		},
		error : function(XMLHttpRequest, textStatus, errorThrown) {
			setUiMessege('err',errorThrown);
		}
	});
}

function createConfirmAlert(title,text,ok,okags,cancle,cancleargs){
	swal({
		title: title,
		text: text,
		icon: "warning",
		buttons: true,
		dangerMode: true,
	}).then(function(willDelete){
		if (willDelete) {
			if(typeof ok != undefined && Object.prototype.toString.call(ok) == '[object Function]'){
				ok(okags);
			}
		} else {
			if(typeof cancle != undefined && Object.prototype.toString.call(cancle) == '[object Function]'){
			cancle(cancleargs);
			}
		}
	});
}

function setUiMessege(type,message,title){
	switch (type){
		case 'err':
			toastr.error(message, title, {
				"timeOut": "0",
				"extendedTImeout": "0",
				"positionClass": "toast-top-center",
			});
		break;
		
		case 'suc':
		toastr.success(message);
		break;
		
		case 'inf':
		toastr.info(message, title);
		break;
		
		case 'war':
		toastr.warning(message);
		break;
	}
}