<div class="modal" style="display:block;" id="addservicepopup">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" id="closeservicepopup" onclick="closeservicepopup();"><span class="glyphicon glyphicon-remove" ></span></button>
				<h4 class="modal-title custom_align" id="Heading">Update customer & purchase details</h4>
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
						<option value="">Select Category</option>
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
					<!--<div class="col-sm-6">
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
				<div class="ad-pup-hdng"><b>Services Info:</b></div>
				<div class="form-group">
					<ul class="nav nav-tabs ">
					<?php
						if($getServiceDataArr){
							foreach($getServiceDataArr as $getServiceData){
					?>
							<li class="">
								<a href="#tab_default_<?php echo $getServiceData['serviceid']; ?>" data-toggle="tab">
								<?php echo $this->utilities->showDateForSpecificTimeZone($getServiceData['service_date'],'d-m-Y');?></a>
							</li>
					<?php
							}
						}
					?>
					</ul>
				</div>
				
				
			</div>
			
				<!--<div class="form-group">
					<input class="form-control" type="text" placeholder="Name" id="name" value="<?php echo $getServiceData['name'];?>">
					<span id="name_error"></span>
				</div>
				<div class="form-group">
					<input class="form-control" type="text" placeholder="Contact" id="contact" value="<?php echo $getServiceData['contact'];?>">
					<span id="contact_error"></span>
				</div>
				<div class="form-group">
					<input class="form-control" type="text" placeholder="Date" id="sdate" value="<?php echo $this->utilities->showDateForSpecificTimeZone($getServiceData['service_date'],'d-m-Y');?>">
					<span id="sdate_error"></span>
				</div>
				<div class="form-group">
					<textarea rows="2" class="form-control" placeholder="Address" id="address"><?php echo $getServiceData['address'];?></textarea>
					<span id="address_error"></span>
				</div>
			</div>-->
			<div class="modal-footer ">
				<button type="button" id="updateservicepopup" onclick="updateservicepopup('');"class="btn btn-warning btn-lg" style="width: 100%;"><span class="glyphicon glyphicon-ok-sign"></span>Update</button>
			</div>
		</div>