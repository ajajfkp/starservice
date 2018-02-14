$( document ).ready(function(){
	getDefaultData();
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

	jQuery(document.body).on('click', '#popupCloseCancel,#popupCloseCross', function(event) {
		$("body #viewEntry").remove();
	});
	jQuery(document.body).on('click', '#popupCloseCancel,#popupCloseCross', function(event) {
		$('#passwrdPopup').hide();
	});
	
	
	$('#serviceDatecal').click(function(){
		$('#serviceDatelst').slideToggle('slow');	
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


 function viewDetail(){
	$.ajax({
		type: "POST",
		url: base_url+'home/viewDetail',
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