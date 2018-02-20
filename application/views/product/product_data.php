<?php 
	if($getProductData){
		foreach($getProductData as $getProduct){
?>
	<tr class="grd-bdy-rw">
		<td align="left" valign="top"><?php echo ucfirst($getProduct['product']); ?></td>
		<td align="left" valign="top"><?php echo ucfirst($getProduct['brand']); ?></td>
		<td align="left" valign="top"><?php echo ucfirst($getProduct['modelnumber']); ?></td>
		<td align="left" valign="top"><?php echo ucfirst($getProduct['referral']); ?></td>
		<td align="left" valign="top"><?php echo $this->utilities->showDateForSpecificTimeZone($getProduct['purchase']); ?></td>
		<td align="left" valign="top"><?php echo $getProduct['guaranty']." + ".$getProduct['warranty'] ." (".(!empty($getProduct['act'])?"Active":"Inactive").")"; ?></td>
		<td align="left" valign="top">
			<span class="cursor" onClick="viewDetail();">View |</span>
			<span class="cursor" onClick="editDetail();">Edit |</span>
			<span class="cursor" onClick="updateService();">Update | </span>
			<span class="cursor red" onClick="deleteRow();">&#10008;</span>
		</td>
	</tr>
<?php
		}
	}
?>