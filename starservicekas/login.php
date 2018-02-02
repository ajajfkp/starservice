<link rel="stylesheet" type="text/css" a href="<?php echo $this->config->base_url();?>css/am_style.css">

<script type="text/javascript" src="<?php echo base_url();?>js/jquery.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/common/common.js"></script>
<script>
	var base_url ='<?php echo base_url();?>';
</script>

	<div class="loginbox">
		<div class="loginHeader">
			AdvMed - Login 
		</div>
		<div class="loginBody">
			<div class="loginUser">
				<span class="label">Username:</span>
				<span class="lableInput">
					<input type="text" name="userName" class="usewrInptbx">
				</span>
			</div>
			<div class="loginUser">
				<span class="label">Password:</span>
				<span class="lableInput">
					<input type="passwrod" name="userName" class="usewrInptbx">
				</span>
			</div>
		</div>
		<div class="logibBtnrw">
			<input type="button" value="SignIn" class="loginBtn" onclick="Mydashboard();">
		</div>
	</div>