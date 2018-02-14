<?php 
		if($getServiceData){
			foreach($getServiceData as $getService){
	?>
		<div class="cstmr-info-main-con">
			<div class="cstmr-detal">
				<span class="cstmr-pic">
					<img class="cstmr-pic-innr" src="<?php echo base_url("assets/images/penguins.jpg"); ?>">
				</span>
				<span class="name"><?php echo ucfirst($getService['custname']); ?></span>
				<span class="actn-rw-dp mblActnitem">
					<span class="dp-dt"></span>
					<span class="dp-dt"></span>
					<span class="dp-dt"></span>
				</span>
				<span class="mblActnlst hide">
					<ul>
						<li class="dd-listing">View</li>
						<li class="dd-listing">Edit</li>
						<li class="dd-listing">Delete</li>
					</ul>
				</span>
				<span class="cntc-no"><?php echo ucfirst($getService['contact']).", ".$getService['address']; ?></span>
				<span class="prdct"><?php echo ucfirst($getService['brand'])." ".$getService['product'],", "; ?> Guaranty: <?php echo $getService['guaranty']." + ".$getService['warranty']; ?></span>
				<span class="actn-rw">
					<span class="dn-chk">
						<input type="checkbox" <?php echo (($getService['done_status']=="1")?"checked":"");?>>
					</span>
					<span class="dn-txt">Done</span>
				</span>
			</div>
			<div class="info-main-btm">
				<span class="srvc-dtl <?php echo ((strtotime($this->utilities->showDateForSpecificTimeZone($getService['service_date'])) < strtotime(date("d-m-Y")))?"red":"");?>">Service Date:<?php echo $this->utilities->showDateForSpecificTimeZone($getService['service_date']);?></span>
				<!--<span class="srvc-dtl right">Next Service: <b><?php echo $this->utilities->getNextPrevService($getService['serId'],$getService['serDetId'],'next');?></b></span>-->
			</div>
		</div>
	<?php
			}
		}
	?>