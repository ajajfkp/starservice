	<div id="addNewEntry" class="add-new-deatil-main-con">
		<div class="popu-header">
			<span class="heading">Add New Entry:</span>
			<span id="popupCloseCross" class="popup-Close" >X</span>
		</div>
		<div class="popup-body left">
			<div class="popup-body-inr-hdng">
				Product Detail
			</div>
			<div class="popup-body-inner-rw-lt">
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Product:</span>
					<span class="inner-rw-val">
						<select class="validate[required] popup-slectBx" id="product" onchange="getBrandList(this.value)">
							<option value=""></option>
							<?php 
							if($productList){
								foreach($productList as $product){
							?>
							<option value="<?php echo $product['id'];?>"><?php echo $product['name'];?></option>
							<?php 
								}
							}
							?>
						</select>
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Brand:</span>
					<span class="inner-rw-val">
						<select class="popup-slectBx" id="brand">
							<option value=""></option>
						</select>
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Model No:</span>
					<span class="inner-rw-val">
						<input type="text" name="modelNum" id="modelNum" class="popup-inpt" maxlength="255">
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Warranty:</span>
					<span class="inner-rw-val">
						<select class="popup-slectBx" id="warranty">
							<option value=""></option>
							<option value="0">No Warranty</option>
							<option value="3">3 Months</option>
							<option value="6">6 Months</option>
							<option value="12">12 Months</option>
						</select>
					</span>
				</div>
			</div>
			<div class="popup-body-inner-rw-rt">
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Sell Date:</span>
					<span class="inner-rw-val">
						<input type="text" name="dateSold" id="dateSold" class="popup-inpt">
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Services:</span>
					<span class="inner-rw-val">
						<select class="popup-slectBx" id="services">
							<option value=""></option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
						</select>
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Duration:</span>
					<span class="inner-rw-val">
						<select class="popup-slectBx" id="duration">
							<option value=""></option>
							<option value="1">Monthly</option>
							<option value="3">Quarterly</option>
							<option value="6">Half yearly</option>
							<option value="12">yearly</option>
						</select>
					</span>
				</div>
				<!--<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Warranty End:</span>
					<span class="inner-rw-val">
						<input type="text" name="warrantyend" id="warrantyend" class="popup-inpt">
					</span>
				</div>-->
			</div>
			<div class="popup-body-inr-hdng">
				Customer Detail
			</div>
			<div class="popup-body-inner-rw-lt">
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Name:</span>
					<span class="inner-rw-val">
						<input type="text" name="dateSold" class="popup-inpt" id="name">
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Address:</span>
					<span class="inner-rw-val">
						<textarea class="popup-txtarea" id="addr"></textarea>
					</span>
				</div>
			</div>
			<div class="popup-body-inner-rw-rt">
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Mobile #:</span>
					<span class="inner-rw-val">
						<input type="text" name="dateSold" class="popup-inpt" id="mobile">
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Note:</span>
					<span class="inner-rw-val">
						<textarea class="popup-txtarea" id="note"></textarea>
					</span>
				</div>
			</div>
			<div class="popup-body-inr-hdng">
				Additional Detail
			</div>
			<div class="popup-body-inner-rw-lt">
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Referral:</span>
					<span class="inner-rw-val">
						<select class="popup-slectBx" id="referral">
							<option value=""></option>
							<option value="1">Whatsapp</option>
							<option value="2">Facebook</option>
							<option value="3">Just Dial</option>
							<option value="4">Friends</option>
							<option value="5">Other</option>
						</select>
					</span>
				</div>
			</div>
			<div class="popup-body-inner-rw-rt">
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Other:</span>
					<span class="inner-rw-val">
						<textarea class="popup-txtarea" id="referralotr"></textarea>
					</span>
				</div>
			</div>
		</div>
		<div class="popup-body-inner-rw">
			<span class="btn-cancel right" id="popupCloseCancel">Cancel</span>
			<span class="btn-save right" onclick="saveeservicepopup();">Save</span>
		</div>
	</div>
	
	