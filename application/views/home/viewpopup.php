	<div id="viewEntry" class="add-new-deatil-main-con view-popup">
		<div class="popu-header">
			<span class="heading">Customer & Product Details:</span>
			<span id="popupCloseCross" class="popup-Close" >X</span>
		</div>
		<div class="popup-body left">
			<div class="popup-body-inr-hdng">
				Customer Detail
			</div>
			
			<div class="popup-body-inner-rw">
				<span class="viewInfo-cstmrpic">
					<img src="<?php echo base_url("uploads/userimg/".$getServiceData['user_image']);?>" class="cstmr-pic-dsk">
				</span>
				<span class="viewInfo">
					<b><?php echo $getServiceData['custname']; ?></b></br>
					Mobile #: <?php echo $getServiceData['contact']; ?></br>
					<?php echo $getServiceData['address']; ?>
				</span>
			</div>
			
			<div class="popup-body-inr-hdng">
				Product Detail
			</div>
			<div class="popup-body-inner-rw-lt">
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Product:</span>
					<span class="inner-rw-val">
						<?php echo $getServiceData['brand']." ".$getServiceData['product']." (".$getServiceData['modelnumber'].")"; ?>
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Purchased on:</span>
					<span class="inner-rw-val">
						<?php echo $this->utilities->showDateForSpecificTimeZone($getServiceData['purchase']); ?>
					</span>
				</div>
			</div>
			<div class="popup-body-inner-rw-rt">
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Warranty:</span>
					<span class="inner-rw-val">
						<?php echo $getServiceData['guaranty']." + ".$getServiceData['warranty']; ?>
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Warranty Exp:</span>
					<span class="inner-rw-val">
						<?php echo $this->utilities->showDateForSpecificTimeZone($getServiceData['warranty_exp']); ?>
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
			<div class="view-servc-dtl">
			<?php
				$i=1;
				foreach($getAllSerDetArr as $service){
			?>
				<div class="popup-body-inner-rw <?php echo (($service['done_status']=='1') ? "red" : "" )?>">
					<div class="popup-body-inner-rw-lt">
						<span class="inner-rw-label">Service <?php echo $i; ?>:</span>
						<span class="inner-rw-val">
							<?php echo $this->utilities->showDateForSpecificTimeZone($service['service_date']); ?>
						</span>
					</div>
					<div class="popup-body-inner-rw-rt">
						<span class="inner-rw-label dsktp-hide">&nbsp;</span>
						<span class="inner-rw-val">
							Done by <b><?php echo$this->utilities->getUserDataById($getServiceData['id'],array("name")); ?></b> on <?php echo $this->utilities->showDateForSpecificTimeZone($service['done_status_date']); ?>
						</span>
					</div>
				</div>
			<?php $i++; }?>

			</div>
		
		</div>
		<div class="popup-body-inner-rw">
			<span class="btn-cancel right" id="popupCloseCancel">Close</span>
			<!--<span class="btn-save right">Save</span>-->
		</div>
	
	
	
	
	