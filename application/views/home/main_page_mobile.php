<div class="dash-main-body left">
	<div class="left tp-rw">
		<span class="tday-hdng left">Services: </span>
		<span class="dt-sel left" id="serviceDatecal">
			<span class="filtertxt left">Today</span>
			<span class="right filterarrow">&#9660; </span>
		</span>
		<span class="dd-list hide" id="serviceDatelst">
			<ul>
				<li class="dd-listing">Today</li>
				<li class="dd-listing">Tomorrow</li>
				<li class="dd-listing">This week</li>
				<li class="dd-listing">This Month</li>
				<li class="dd-listing">Last Month</li>
				<li class="dd-listing">This year</li>
			</ul>
		</span>
		<span class="ad-nw-btn right" id="addNewSer">Add new</span>
	</div>
	<div class="menu-srch-rw">
		<div class="srch-opt-blk">
			<span class="search-optn">
				<input type="radio" name="srchOption" checked="">
			</span>
			<span class="search-optn-txt">Mobile</span>
		</div>
		<div class="srch-opt-blk">
			<span class="search-optn">
				<input type="radio" name="srchOption">
			</span>
			<span class="search-optn-txt">Name</span>
		</div>
		<div class="srch-opt-blk">
			<span class="search-optn">
				<input type="radio" name="srchOption">
			</span>
			<span class="search-optn-txt">Add.</span>
		</div>
		<span class="serach-bar">
			<input type="text" class="serach-inpt" placeholder="Search here...">
		</span>
	</div>
	<div class="left grd-rw">
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
				<span class="prdct"><?php echo ucfirst($getService['brand'])." ".$getService['product'],", "; ?> Guaranty: <?php echo $getService['warranty']." + ".$getService['guaranty']; ?></span>
				<span class="actn-rw">
					<span class="dn-chk">
						<input type="checkbox">
					</span>
					<span class="dn-txt">Done</span>
				</span>
			</div>
			<div class="info-main-btm">
				<span class="srvc-dtl">Last Service:<?php echo $this->utilities->getNextPrevService($getService['serId'],$getService['serDetId'],'prev');?></span>
				<span class="srvc-dtl right">Next Service: <b><?php echo $this->utilities->getNextPrevService($getService['serId'],$getService['serDetId'],'next');?></b></span>
			</div>
		</div>
	<?php
			}
		}
	?>
	</div>
</div>