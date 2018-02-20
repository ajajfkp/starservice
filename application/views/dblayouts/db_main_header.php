<header>
	<span class="welcomtext">
		<span class="frstLogo">Star</span>
		<span class="scndLogo">Services</span>
	</span>
	<?php if($this->utilities->isAuth()){?>
		<span id="signOut" class="signout ssFlg0" >Sign Out</span>
	<?php } ?>
	<span class="sign-name right">
		<span class="left">
		<?php $userdata = $this->utilities->getSessionUserData(); ?>
		<h6>Welcome!&nbsp;&nbsp; <?php echo ucfirst($this->utilities->getSessionUserData('user_name'));?></h6>
		</span>
		<span class="right prfl-pasrd-arw">&#9660; </span>
	</span>
	<span class="pswrd-list hide" id="changePaswrd">
		<ul>
			<li class="psrd-optn-listing">Change Password</li>
		</ul>
	</span>
</header>

<menu>
	<span class="top-tab ssFlg0">Admin</span>
	<span class="top-tab-sep ssFlg0">|</span>
	<span class="top-tab" id="product" ><a href="<?php echo base_url("product"); ?>">Product</a></span>
	<span class="top-tab-sep ssFlg0">|</span>
	<span class="top-tab" id="service"> <a href="<?php echo base_url("home"); ?>">Services</a></span>
	<span class="top-tab-sep ssFlg0">|</span>
</menu>


<!----------------Change Pssword Code Start here---------------->

	<div id="passwrdPopup" class="add-new-deatil-main-con changePaswrd-popup hide">
		<div class="popu-header">
			<span class="heading">Change Password:</span>
			<span id="popupCloseCross" class="popup-Close" >X</span>
		</div>
		<div class="popup-body left">
			<div class="popup-body-inner-rw">
				<span class="inner-rw-label">Old Passwrod:</span>
				<span class="inner-rw-val">
					<input type="text" name="dateSold" class="popup-inpt">
				</span>
			</div>
			<div class="popup-body-inner-rw">
				<span class="inner-rw-label">New Passwrod:</span>
				<span class="inner-rw-val">
					<input type="text" name="dateSold" class="popup-inpt">
				</span>
			</div>
			<div class="popup-body-inner-rw">
				<span class="inner-rw-label">Confirm New Passwrod:</span>
				<span class="inner-rw-val">
					<input type="text" name="dateSold" class="popup-inpt">
				</span>
			</div>
			<div class="popup-body-inner-rw">
				<span class="inner-rw-label wdthfl">
					<ul>
						<li>The password length must be at least 8 characters.</li>
						<li>The password must include at least one numeric character.</li>
						<li>The password must include at least one special character</li>
						<li>The password must contain both upper- and lowercase characters</li>
					</ul>
				</span>
			</div>
			
					
		</div>
		<div class="popup-body-inner-rw">
			<span class="btn-cancel right" id="popupCloseCancel">Cancel</span>
			<span class="btn-save right">Save</span>
		</div>
	</div>


<!----------------Change Pssword Code End here---------------->