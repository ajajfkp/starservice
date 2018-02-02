$( document ).ready(function(){
	
	$("#addNewSer").click(function(){
		$.ajax({
			type: "POST",
			url: base_url+'home/openaddservice',
			data: {},
			success: function(msg){
				$("body").append(msg);
				$("#sdate,#purchasedate").datepicker({
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
function closeservicepopup(){
	alert()
	//$("body #addservicepopup").remove();
}

function saveeservicepopup(){
	var validateArray = Array();
		validateArray.push("name");
		validateArray.push("contact");
		validateArray.push("address");
		validateArray.push("productcat");
		validateArray.push("productdetails");
		validateArray.push("purchasedate");
		validateArray.push("noofservice");
		if($("#noofservice").val() >0){
			validateArray.push("sdate");
		}

	if(validateinput(validateArray)){
		return false;
	}else{
		$.ajax({
			type: "POST",
			url: base_url+'home/service',
			data: {
				name:$("#name").val(),
				contact:$("#contact").val(),
				address:$("#address").val(),
				productcat:$("#productcat").val(),
				productdetails:$("#productdetails").val(),
				purchasedate:$("#purchasedate").val(),
				noofservice:$("#noofservice").val(),
				serDuration:$("#serDuration").val(),
				sdate:$("#sdate").val(),
				notes:$("#notes").val()
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
