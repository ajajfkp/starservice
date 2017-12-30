<div class="modal" style="display:block;" id="addservicepopup">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" id="closeservicepopup" onclick="closeservicepopup();"><span class="glyphicon glyphicon-remove" ></span></button>
				<h4 class="modal-title custom_align" id="Heading">Add Your Service</h4>
			</div>
			<div class="modal-body form-horizontal">
				<div class="form-group">
					<div class="col-sm-6">
						<label>Name</label>
						<input class="form-control" type="text" placeholder="Name" id="name" autocomplete="off">
						<span id="name_error"></span>
					</div>
					<div class="col-sm-6">
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
				<div class="form-group">
					<div class="col-sm-6">
						<label>Product Category.</label>
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
					<div class="col-sm-6">
						<label>Product Details.</label>
						<input type="text" class="form-control" placeholder="product Details" id="productdetails">
						<span id="productdetails_error"></span>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-4">
						<label>Purchase Date.</label>
						<input class="form-control" type="text" placeholder="Purchase Date" id="purchasedate">
						<span id="purchasedate_error"></span>
					</div>
					<div class="col-sm-4">
						<label>Number of Services</label>
						<input class="form-control" type="text" placeholder="No of service" id="noofservice" value="0">
						<span id="noofservice_error"></span>
					</div>
					<div class="col-sm-4">
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