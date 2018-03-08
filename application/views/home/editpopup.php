	<div id="addNewEntry" class="add-new-deatil-main-con">
		<div class="popu-header">
			<span class="heading">Add New Entry:</span>
			<span id="popupCloseCross" class="popup-Close" >X</span>
		</div>
		<!--<div class="cstmr-srch-sectn">
			<span class="srch-lbl">Customer Search:</span>-->
			<!--<span class="srch-val">
				<input type="radio" class="cstmd-src-optn" name="cstmrSrch" id="" checked="">
				<span class="srch-lbl">New</span>
			</span>
			<span class="srch-val">
				<input type="radio" class="cstmd-src-optn" name="cstmrSrch" id="">
				<span class="srch-lbl">Existing</span>
			</span>-->
			<!--<span class="srch-val">
				<input type="text" class="cstmd-inpt-src-optn" name="cstmrSrch" id="cstmrSrch" placeholder="Search here...">
			</span>
			<span class="srch-val">
				<select class="serach-val-cstmr" id="cstmrSrchBy">
					<option value="1">Mobile No.</option>
					<option value="2">Name</option>
					<option value="3">Address</option>
				</select>
			</span>
		</div>-->
		
		<div class="cstmr-srch-rslt-sec" id="cstmrList">
			
		</div>
		<div class="popup-body left">
			<div class="popup-body-inr-hdng">
				Customer Detail
			</div>
			<div class="popup-body-inner-rw-lt">
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Name:</span>
					<span class="inner-rw-val">
						<input type="text" name="dateSold" class="popup-inpt" id="name" value="<?php echo  $getServiceData['custname']; ?>">
						<input type="hidden" name="custId" id="custId" value="<?php echo  $getServiceData['custid']; ?>">
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Address:</span>
					<span class="inner-rw-val">
						<textarea class="popup-txtarea" id="addr"><?php echo  $getServiceData['address']; ?></textarea>
					</span>
				</div>
			</div>
			<div class="popup-body-inner-rw-rt">
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Mobile #:</span>
					<span class="inner-rw-val">
						<input type="text" name="dateSold" class="popup-inpt" id="mobile" value="<?php echo  $getServiceData['contact']; ?>">
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Note:</span>
					<span class="inner-rw-val">
						<textarea class="popup-txtarea" id="note"><?php echo  $getServiceData['notes']; ?></textarea>
					</span>
				</div>
			</div>
			<div class="popup-body-inner-rw">
				<span class="inner-rw-label">Pic:</span>
				<span class="inner-rw-val">
					<input type="file" name="cstmrEditImg" class="popup-inpt" id="cstmrEditImg">
					<input type="hidden" name="userEditImg" id="userEditImg" value="<?php echo  $getServiceData['user_image']; ?>">
				</span>
				<span class="inner-rw-val">
						<span class="inner-rw-label">&nbsp;</span>
						<div id="showUserEditImg">
							<img src="<?php echo base_url('uploads/userimg/'.$getServiceData['user_image']);?>" alt="Customer image" width="100px;"/>
						</div>
					</span>
			</div>
			
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
							<option value="<?php echo $product['id'];?>" <?php echo (($product['id']==$getServiceData['productid'])?"selected":""); ?>><?php echo $product['name'];?></option>
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
							<?php 
							if($brandList){
								foreach($brandList as $brand){
							?>
							<option value="<?php echo $brand['id'];?>" <?php echo (($brand['id']==$getServiceData['brandid'])?"selected":""); ?>><?php echo $brand['name'];?></option>
							<?php 
								}
							}
							?>
						</select>
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Model No:</span>
					<span class="inner-rw-val">
						<input type="text" name="modelNum" id="modelNum" class="popup-inpt" maxlength="255" value="<?php echo  $getServiceData['modelnumber']; ?>">
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Sell Date:</span>
					<span class="inner-rw-val">
						<input type="text" name="dateSold" id="dateSold" class="popup-inpt" value="<?php echo  $getServiceData['purchase']; ?>" <?php echo (($serDone) ? "disabled":"");?>>
					</span>
				</div>
			</div>
			<div class="popup-body-inner-rw-rt">
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Guaranty:</span>
					<span class="inner-rw-val">
						<select class="popup-slectBx" id="guaranty" <?php echo (($serDone) ? "disabled":"");?>>
							<option value=""></option>
							<option value="0" <?php echo (($getServiceData['guaranty']=="0")?"selected":""); ?>>No guaranty</option>
							<option value="3" <?php echo (($getServiceData['guaranty']=="3")?"selected":""); ?>>3 Months</option>
							<option value="6" <?php echo (($getServiceData['guaranty']=="6")?"selected":""); ?>>6 Months</option>
							<option value="12" <?php echo (($getServiceData['guaranty']=="12")?"selected":""); ?>>12 Months</option>
						</select>
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Warranty:</span>
					<span class="inner-rw-val">
						<select class="popup-slectBx" id="warranty" <?php echo (($serDone) ? "disabled":"");?>>
							<option value=""></option>
							<option value="0" <?php echo (($getServiceData['warranty']=="0")?"selected":""); ?>>No Warranty</option>
							<option value="3" <?php echo (($getServiceData['warranty']=="3")?"selected":""); ?>>3 Months</option>
							<option value="6" <?php echo (($getServiceData['warranty']=="6")?"selected":""); ?>>6 Months</option>
							<option value="12" <?php echo (($getServiceData['warranty']=="12")?"selected":""); ?>>12 Months</option>
						</select>
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Services:</span>
					<span class="inner-rw-val">
						<select class="popup-slectBx" id="services" <?php echo (($serDone) ? "disabled":"");?>>
							<option value=""></option>
							<option value="1" <?php echo (($getServiceData['num_of_services']=="1")?"selected":""); ?>>1</option>
							<option value="2" <?php echo (($getServiceData['num_of_services']=="2")?"selected":""); ?>>2</option>
							<option value="3" <?php echo (($getServiceData['num_of_services']=="3")?"selected":""); ?>>3</option>
						</select>
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Duration:</span>
					<span class="inner-rw-val">
						<select class="popup-slectBx" id="duration" <?php echo (($serDone) ? "disabled":"");?>>
							<option value=""></option>
							<option value="1" <?php echo (($getServiceData['duration']=="1")?"selected":""); ?>>Monthly</option>
							<option value="3" <?php echo (($getServiceData['duration']=="3")?"selected":""); ?>>Quarterly</option>
							<option value="6" <?php echo (($getServiceData['duration']=="6")?"selected":""); ?>>Half yearly</option>
							<option value="12" <?php echo (($getServiceData['duration']=="12")?"selected":""); ?>>yearly</option>
						</select>
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
							<option value="1" <?php echo (($getServiceData['referral']=="1")?"selected":""); ?>>Whatsapp</option>
							<option value="2" <?php echo (($getServiceData['referral']=="1")?"selected":""); ?>>Facebook</option>
							<option value="3" <?php echo (($getServiceData['referral']=="1")?"selected":""); ?>>Just Dial</option>
							<option value="4" <?php echo (($getServiceData['referral']=="1")?"selected":""); ?>>Friends</option>
							<option value="5" <?php echo (($getServiceData['referral']=="1")?"selected":""); ?>>Other</option>
						</select>
					</span>
				</div>
			</div>
			<div class="popup-body-inner-rw-rt">
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Other:</span>
					<span class="inner-rw-val">
						<textarea class="popup-txtarea" id="referralotr"><?php echo  $getServiceData['referral_other']; ?></textarea>
					</span>
				</div>
			</div>
			<div class="popup-body-inr-hdng">
				Services Details:
			</div>
			<div class="view-servc-smry">
				<div class="popup-body-inner-rw-lt">
					<div class="popup-body-inner-rw">
						<span class="inner-rw-label">Total Services:</span>
						<span class="inner-rw-val">
							<?php 
								$durationArr = array("1"=>"Monthly","3"=>"Quarterly","6"=>"Half yearly","12"=>"yearly");
								echo $getServiceData['num_of_services']." (".$durationArr[$getServiceData['duration']].")"; 
							?>
						</span>
					</div>
				</div>
				<div class="popup-body-inner-rw-lt">
					<div class="popup-body-inner-rw">
						<span class="inner-rw-label">Services left:</span>
						<span class="inner-rw-val">
							<?php
								$count=0;
								foreach($getAllSerDetArr as $getAllSerDet){
									if($getAllSerDet['done_status']=="0"){
										$count++;
									}
								}
								echo $count;
							?>
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="popup-body-inner-rw">
			<span class="btn-cancel right" id="popupCloseCancel">Cancel</span>
			<span class="btn-save right" onclick="editservicedetail();">Save</span>
			<input type="hidden" name="serId" id="serId" value="<?php echo $getServiceData['serId']; ?>"/>
		</div>
	</div>
	
	