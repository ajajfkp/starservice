	<div id="addNewEntry" class="add-new-deatil-main-con">
		<div class="popu-header">
			<span class="heading">Add New Entry:</span>
			<span id="popupCloseCross" class="popup-Close" >X</span>
		</div>
		<div class="popup-body left">
			<div class="popup-body-inr-hdng">
				Prodcut Detail
			</div>
			<div class="popup-body-inner-rw-lt">
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Product:</span>
					<span class="inner-rw-val">
						<select class="popup-slectBx" id="product" onchange="getBrandList(this.value)">
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
						<input type="text" name="modelNum" class="popup-inpt">
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Warranty:</span>
					<span class="inner-rw-val">
						<select class="popup-slectBx">
							<option value=""><option>
							<option value="">3 Months</option>
							<option value="">6 Months</option>
							<option value="">12 Months</option>
						</select>
					</span>
				</div>
			</div>
			<div class="popup-body-inner-rw-rt">
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Sell Date:</span>
					<span class="inner-rw-val">
						<input type="text" name="dateSold" class="popup-inpt">
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Services:</span>
					<span class="inner-rw-val">
						<select class="popup-slectBx">
							<option value=""><option>
							<option value="">1</option>
							<option value="">2</option>
							<option value="">3</option>
						</select>
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Duration:</span>
					<span class="inner-rw-val">
						<select class="popup-slectBx">
							<option value=""><option>
							<option value="">15 days</option>
							<option value="">30 days</option>
							<option value="">60 days</option>
							<option value="">90 days</option>
						</select>
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Warranty End:</span>
					<span class="inner-rw-val">
						<input type="text" name="dateSold" class="popup-inpt">
					</span>
				</div>
			</div>
			<div class="popup-body-inr-hdng">
				Customer Detail
			</div>
			<div class="popup-body-inner-rw-lt">
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Name:</span>
					<span class="inner-rw-val">
						<input type="text" name="dateSold" class="popup-inpt">
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Address:</span>
					<span class="inner-rw-val">
						<textarea class="popup-txtarea"></textarea>
					</span>
				</div>
			</div>
			<div class="popup-body-inner-rw-rt">
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Mobile #:</span>
					<span class="inner-rw-val">
						<input type="text" name="dateSold" class="popup-inpt">
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Note:</span>
					<span class="inner-rw-val">
						<textarea class="popup-txtarea"></textarea>
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
						<select class="popup-slectBx">
							<option value=""><option>
							<option value="">Whatsapp</option>
							<option value="">Facebook</option>
							<option value="">Just Dial</option>
							<option value="">Friends</option>
							<option value="">Other</option>
						</select>
					</span>
				</div>
			</div>
			<div class="popup-body-inner-rw-rt">
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Other:</span>
					<span class="inner-rw-val">
						<textarea class="popup-txtarea"></textarea>
					</span>
				</div>
			</div>
		</div>
		<div class="popup-body-inner-rw">
			<span class="btn-cancel right" id="popupCloseCancel">Cancel</span>
			<span class="btn-save right">Save</span>
		</div>
	</div>
	
	