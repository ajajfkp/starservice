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