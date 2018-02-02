$(document).ready(function(){
	
	
});

function Mydashboard(){
	window.location.href=base_url+"dashboard/dashboard/amdashboard";
};

function signout(){
	window.location.href=base_url+"login/login/";
};

function searchPage(){
	//alert('aaa');
	//window.location.href=base_url+"dashboard/dashboard/searchPage";
	$.ajax({
		url		:base_url+"dashboard/dashboard/searchPage",
		type	:	'POST',
		Sucess	:	function(msg){
			
		},

		
	});
};

/* function backToPrvious(){
	$.ajax({
		url:base_url+"dashboard/dashboard/searchPage"
		
	})
} */

