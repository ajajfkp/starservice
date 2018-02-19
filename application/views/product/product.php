<?php
?>

<div class="dash-main-body left">
	<div class="left tp-rw">
		<!--<div class="left sel-dt-sectn">
			<span class="tday-hdng left">Date:</span>
			<span class="dt-sel left">
				<span class="filtertxt left">Today</span>
				<span class="right">&#9660; </span>
			</span>
			<span id="selCalVal" class="dd-list hide">
				<ul>
					<li class="dd-listing">This week</li>
					<li class="dd-listing">This Month</li>
					<li class="dd-listing">Last Month</li>
					<li class="dd-listing">This year</li>
				</ul>
			</span>
		</div>-->
		<div class="left prdt-dt-sectn">
			<span class="sel-tday-hdng left">Date:</span>
			<span class="fltr-val">
				<select class="filtr-optn" >
					<option value="">This week</option>
					<option value="">This Month</option>
					<option value="">Last Month</option>
					<option value="">This Year</option>
					<option value="">All</option>
				</select>
			</span>
		</div>
		<div class="left prdt-dt-sectn">
			<span class="sel-tday-hdng left">Product:</span>
			<span class="fltr-val">
				<select class="filtr-optn">
					<option value="">Battery</option>
					<option value="">Car Battery</option>
					<option value="">Inverter</option>
					<option value="">All</option>
				</select>
			</span>
		</div>
		<div class="left prdt-dt-sectn">
			<span class="sel-tday-hdng left">Brand:</span>
			<span class="fltr-val">
				<select class="filtr-optn">
					<option value="">Okaya</option>
					<option value="">Mickrotek</option>
					<option value="">Luminous</option>
					<option value="">Amaron</option>
					<option value="">Exide</option>
					<option value="">All</option>
				</select>
			</span>
		</div>
		<div class="left prdt-dt-sectn">
			<span class="sel-tday-hdng left">Total:</span>
			<span class="filtr-optn rslt-count">40</span>
		</div>
		<span class="ad-nw-btn right hide" id="addNewSer">Add new</span>
	</div>
	<div class="left grd-rw">
		<table border="0" cellpadding="0" cellspacing="0" class="grd-tabl">
			<tbody>
				<tr class="grd-hdr">
					<th align="left" valign="top">Product</th>
					<th align="left" valign="top">Brand</th>
					<th align="left" valign="top">Model</th>
					<th align="left" valign="top">Referral</th>
					<th align="left" valign="top">Date</th>
					<th align="left" valign="top">Guaranty</th>
					<th align="left" valign="top">Action</th>	
				</tr>
				<div class="grd-bdy">
					<tr class="grd-bdy-rw">
						<td align="left" valign="top">Battery</td>
						<td align="left" valign="top">Mickrotek</td>
						<td align="left" valign="top">Invatublar</td>
						<td align="left" valign="top">Just Dial</td>
						<td align="left" valign="top">02-02-2018</td>
						<td align="left" valign="top">36+12 (Active)</td>
						<td align="left" valign="top">
							<span class="cursor" onClick="viewDetail();">View |</span>
							<span class="cursor" onClick="editDetail();">Edit |</span>
							<span class="cursor" onClick="updateService();">Update | </span>
							<span class="cursor red" onClick="deleteRow();">&#10008;</span>
						</td>
					</tr>
					<tr class="grd-bdy-rw-evn">
						<td align="left" valign="top">Battery</td>
						<td align="left" valign="top">Mickrotek</td>
						<td align="left" valign="top">Invatublar</td>
						<td align="left" valign="top">Just Dial</td>
						<td align="left" valign="top">02-02-2018</td>
						<td align="left" valign="top">36+12 (Active)</td>
						<td align="left" valign="top">
							<span class="cursor" onClick="viewDetail();">View |</span>
							<span class="cursor" onClick="editDetail();">Edit |</span>
							<span class="cursor" onClick="updateService();">Update | </span>
							<span class="cursor red" onClick="deleteRow();">&#10008;</span>
						</td>
					</tr>
					<tr class="grd-bdy-rw">
						<td align="left" valign="top">Battery</td>
						<td align="left" valign="top">Mickrotek</td>
						<td align="left" valign="top">Invatublar</td>
						<td align="left" valign="top">Just Dial</td>
						<td align="left" valign="top">02-02-2018</td>
						<td align="left" valign="top">36+12 (Inactive)</td>
						<td align="left" valign="top">
							<span class="cursor" onClick="viewDetail();">View |</span>
							<span class="cursor" onClick="editDetail();">Edit |</span>
							<span class="cursor" onClick="updateService();">Update | </span>
							<span class="cursor red" onClick="deleteRow();">&#10008;</span>
						</td>
					</tr>
					<tr class="grd-bdy-rw-evn">
						<td align="left" valign="top">Battery</td>
						<td align="left" valign="top">Mickrotek</td>
						<td align="left" valign="top">Invatublar</td>
						<td align="left" valign="top">Just Dial</td>
						<td align="left" valign="top">02-02-2018</td>
						<td align="left" valign="top">36+12 (Active)</td>
						<td align="left" valign="top">
							<span class="cursor" onClick="viewDetail();">View |</span>
							<span class="cursor" onClick="editDetail();">Edit |</span>
							<span class="cursor" onClick="updateService();">Update | </span>
							<span class="cursor red" onClick="deleteRow();">&#10008;</span>
						</td>
					</tr>
					<tr class="grd-bdy-rw">
						<td align="left" valign="top">Battery</td>
						<td align="left" valign="top">Mickrotek</td>
						<td align="left" valign="top">Invatublar</td>
						<td align="left" valign="top">Just Dial</td>
						<td align="left" valign="top">02-02-2018</td>
						<td align="left" valign="top">36+12 (Active)</td>
						<td align="left" valign="top">
							<span class="cursor" onClick="viewDetail();">View |</span>
							<span class="cursor" onClick="editDetail();">Edit |</span>
							<span class="cursor" onClick="updateService();">Update | </span>
							<span class="cursor red" onClick="deleteRow();">&#10008;</span>
						</td>
					</tr>
					<tr class="grd-bdy-rw-evn">
						<td align="left" valign="top">Battery</td>
						<td align="left" valign="top">Mickrotek</td>
						<td align="left" valign="top">Invatublar</td>
						<td align="left" valign="top">Just Dial</td>
						<td align="left" valign="top">02-02-2018</td>
						<td align="left" valign="top">36+12 (Active)</td>
						<td align="left" valign="top">
							<span class="cursor" onClick="viewDetail();">View |</span>
							<span class="cursor" onClick="editDetail();">Edit |</span>
							<span class="cursor" onClick="updateService();">Update | </span>
							<span class="cursor red" onClick="deleteRow();">&#10008;</span>
						</td>
					</tr>
					<tr class="grd-bdy-rw">
						<td align="left" valign="top">Battery</td>
						<td align="left" valign="top">Mickrotek</td>
						<td align="left" valign="top">Invatublar</td>
						<td align="left" valign="top">Just Dial</td>
						<td align="left" valign="top">02-02-2018</td>
						<td align="left" valign="top">36+12 (Active)</td>
						<td align="left" valign="top">
							<span class="cursor" onClick="viewDetail();">View |</span>
							<span class="cursor" onClick="editDetail();">Edit |</span>
							<span class="cursor" onClick="updateService();">Update | </span>
							<span class="cursor red" onClick="deleteRow();">&#10008;</span>
						</td>
					</tr>
					<tr class="grd-bdy-rw-evn">
						<td align="left" valign="top">Battery</td>
						<td align="left" valign="top">Mickrotek</td>
						<td align="left" valign="top">Invatublar</td>
						<td align="left" valign="top">Just Dial</td>
						<td align="left" valign="top">02-02-2018</td>
						<td align="left" valign="top">36+12 (Active)</td>
						<td align="left" valign="top">
							<span class="cursor" onClick="viewDetail();">View |</span>
							<span class="cursor" onClick="editDetail();">Edit |</span>
							<span class="cursor" onClick="updateService();">Update | </span>
							<span class="cursor red" onClick="deleteRow();">&#10008;</span>
						</td>
					</tr>
				</div>
			</tbody>
		</table>
	</div>	
</div>