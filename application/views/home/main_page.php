<div class="dash-main-body left">
	<div class="left tp-rw">
		<span class="tday-hdng left">Services: </span>
		<span class="dt-sel left" id="serviceDatecal">
			<span class="filtertxt left">Today</span>
			<span class="right">&#9660; </span>
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
	<div class="left grd-rw">
		<table border="0" cellpadding="0" cellspacing="0" class="grd-tabl">
			<thead>
				<tr class="grd-hdr">
					<th align="left" valign="top">Name</th>
					<th align="left" valign="top">Contact#</th>
					<th align="left" valign="top">Address</th>
					<th align="left" valign="top">Product</th>
					<th align="left" valign="top">Brand</th>
					<th align="left" valign="top">Model</th>
					<th align="left" valign="top">Purchase</th>
					<th align="left" valign="top">Warranty</th>
					<th align="left" valign="top">Service Due</th>
					<th align="left" valign="top">Action</th>	
				</tr>
			</thead>
			<tbody class="grid-bdy">
			<?php 
				if($getServiceData){
					foreach($getServiceData as $getService){
			?>
				<tr class="grd-bdy-rw">
					<td align="left" valign="top"><?php echo ucfirst($getService['custname']); ?></td>
					<td align="left" valign="top"><?php echo $getService['contact']; ?></td>
					<td align="left" valign="top"><?php echo $getService['address']; ?></td>
					<td align="left" valign="top"><?php echo ucfirst($getService['product']); ?></td>
					<td align="left" valign="top"><?php echo ucfirst($getService['brand']); ?></td>
					<td align="left" valign="top"><?php echo ucfirst($getService['modelnumber']); ?></td>
					<td align="left" valign="top"><?php echo $this->utilities->showDateForSpecificTimeZone($getService['purchase']); ?></td>
					<td align="left" valign="top"><?php echo $this->utilities->showDateForSpecificTimeZone($getService['warranty_exp']); ?></td>
					<td align="left" valign="top"><?php echo $this->utilities->showDateForSpecificTimeZone($getService['service_date']); ?></td>
					<td align="left" valign="top">
						<span class="cursor" onClick="viewDetail('<?php echo $getService['serId'];?>','<?php echo $getService['serDetId'];?>');">View |</span>
						<span class="cursor" onClick="editDetail('<?php echo $getService['serId'];?>','<?php echo $getService['serDetId'];?>');">Edit |</span>
						<span class="cursor" onClick="updateService('<?php echo $getService['serId'];?>','<?php echo $getService['serDetId'];?>');">Update | </span>
						<span class="cursor red" onClick="deleteRow('<?php echo $getService['serId'];?>','<?php echo $getService['serDetId'];?>');">&#10008;</span>
					</td>
				</tr>
			<?php
					}
				}
			?>
			</tbody>
		</table>
	</div>





	<!--<div class="menu">
		<div class="menu-frst-rw">
			<span class="serach-bar" onclick="searchPage();">
				<input type="text" class="serach-inpt">
			</span>
		</div>
		<div class="menu-frst-rw">
			<span class="provider-bar">
				<span class="plabel">Provider:</span>
				<span class="pvalue">John Brooks:</span>
				<span class="prfilter-blk">
				</span>
			</span>
		</div>
	</div>
	<div class="lower-sec">
		<span class="nav">Left Nav</span>
		<span class="section">Right</span>
	</div>-->
	
	
</div>