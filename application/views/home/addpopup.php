<div class="modal" style="display:block;" id="addservicepopup">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" id="closeservicepopup" onclick="closeservicepopup();"><span class="glyphicon glyphicon-remove" ></span></button>
				<h4 class="modal-title custom_align" id="Heading">Add Your Service</h4>
			</div>
			<div class="modal-body form-horizontal">
				<div class="ad-pup-hdng"><b>Customer Info:</b></div>
				<div class="form-group">
					<div class="col-sm-8">
						<label>Name</label>
						<input class="form-control" type="text" placeholder="Name" id="name" autocomplete="off">
						<span id="name_error"></span>
					</div>
					<div class="col-sm-4">
						<label>Mobile No.</label>
						<input class="form-control" type="text" placeholder="Contact" id="contact" maxlength="10">
						<span id="contact_error"></span>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-12">
						<label>Address.</label>
						<textarea rows="2" class="form-control" placeholder="Address" id="address"></textarea>
						<span id="address_error"></span>
					</div>
				</div>
				<div class="ad-pup-hdng"><b>Purchase Info:</b></div>
				<div class="form-group">
					<div class="col-sm-3">
						<label>Product</label>
						<select class="form-control" id="productcat">
						<option value="">Select</option>
						<?php
							if($catddArr){
								foreach($catddArr as $catdd){
						?>
							<option value="<?php echo $catdd['id']; ?>"><?php echo $catdd['name']; ?></option>
						<?php
								}
							}
						?>
						</select>
						<span id="productcat_error"></span>
					</div>
					<div class="col-sm-3">
						<label>Brand</label>
						<select class="form-control" id="productcat">
						<option value="">Select</option>
						<option value="">Microtek</option>
						<option value="">Sukam</option>
						<option value="">Okaya</option>
						<option value="">Honda</option>
						</select>
						<span id="productcat_error"></span>
					</div>
					<div class="col-sm-3">
						<label>Model No.</label>
						<input type="text" class="form-control" placeholder="Model No." id="productdetails">
						<span id="productdetails_error"></span>
					</div>
					<div class="col-sm-3">
						<label>Warranty</label>
						<select class="form-control" id="productcat">
						<option value="1">Select</option>
						<option value="2">3 Months</option>
						<option value="3">6 Months</option>
						<option value="4">9 Months</option>
						<option value="5">12 Months</option>
						<option value="6">18 Months</option>
						<option value="7">24 Months</option>
						<option value="8">30 Months</option>
						<option value="9">36 Months</option>
						<option value="10">42 Months</option>
						<option value="11">48 Months</option>
						</select>
						<span id="productcat_error"></span>
					</div>
					<!--<div class="col-sm-3">
						<label>Product Details.</label>
						<input type="text" class="form-control" placeholder="product Details" id="productdetails">
						<span id="productdetails_error"></span>
					</div>-->
				</div>
				<div class="form-group">
					<div class="col-sm-3">
						<label>Purchase Date.</label>
						<input class="form-control" type="text" placeholder="Purchase Date" id="purchasedate">
						<span id="purchasedate_error"></span>
					</div>
					<div class="col-sm-3">
						<label>Warranty expired</label>
						<input class="form-control" type="text" placeholder="Expiry Date" id="purchasedate">
						<span id="purchasedate_error"></span>
					</div>
					<div class="col-sm-3">
						<label>Services</label>
						<input class="form-control" type="text" placeholder="No of service" id="noofservice" value="0">
						<span id="noofservice_error"></span>
					</div>
					<div class="col-sm-3">
						<label>Duration</label>
						<select class="form-control" id="serDuration">
							<option value="15d">15 Days</option>
							<option value="mon" selected >Monthly</option>
							<option value="3mon">Quarterly</option>
							<option value="6mon">half yearly</option>
							<option value="12mon">yearly</option>
						</select>
						<span id="noofservice_error"></span>
					</div>
					<!--<div class="col-sm-4">
						<label>Next service.</label>
						<input class="form-control" type="text" placeholder="Next service" id="sdate">
						<span id="sdate_error"></span>
					</div>-->
				</div>
				<div class="form-group">
					<div class="col-sm-12">
						<label>Notes.</label>
						<textarea rows="2" class="form-control" placeholder="Notes" id="notes"></textarea>
						<span id="notes_error"></span>
					</div>
				</div>
			</div>
			<div class="modal-footer ">
				<button type="button" id="saveeservicepopup" onclick="saveeservicepopup();"class="btn btn-warning btn-lg" style="width: 100%;"><span class="glyphicon glyphicon-ok-sign"></span>Save</button>
			</div>
		</div>
	</div>
</div>


<!---------------------------Add New Deatil popup Code Start here--------------------------->


	<div id="addNewEntry" class="add-new-deatil-main-con">
		<div class="popu-header">
			<span class="heading">Add New Entry:</span>
			<span id="popupClose" class="popup-Close">X</span>
		</div>
		<div class="popup-body left">
			<div class="popup-body-inr-hdng">
				Prodcut Detail
			</div>
			<div class="popup-body-inner-rw-lt">
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Product:</span>
					<span class="inner-rw-val">
						<select class="popup-slectBx">
							<option value=""></option>
							<option value="">Battery</option>
							<option value="">Inverter</option>
							<option value="">Car battery</option>
						</select>
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Brand:</span>
					<span class="inner-rw-val">
						<select class="popup-slectBx">
							<option value=""></option>
							<option value="">Mickrotek</option>
							<option value="">Luminous</option>
							<option value="">Okaya</option>
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
						<input type="text" name="dateSold" class="popup-inpt">
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
			<span class="btn-cancel right">Cancel</span>
			<span class="btn-save right">Save</span>
		</div>
	</div>


<!---------------------------Add New Deatil popup Code End here--------------------------->

