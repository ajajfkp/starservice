<div class="dash-main-body left">
	<div class="left tp-rw">
		<span class="tday-hdng left">Services: </span>
		<span class="dt-sel left cursor">
			&#9660; Today 
		</span>
		<span class="dd-list">
			<ul>
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
			<tbody>
				<tr class="grd-hdr">
					<th align="left" valign="top">Name</th>
					<th align="left" valign="top">Contact#</th>
					<th align="left" valign="top">Address</th>
					<th align="left" valign="top">Prodcut</th>
					<th align="left" valign="top">Brand</th>
					<th align="left" valign="top">Model</th>
					<th align="left" valign="top">Purchase</th>
					<th align="left" valign="top">Warranty</th>
					<th align="left" valign="top">Service Due</th>
					<th align="left" valign="top">Action</th>	
				</tr>
				<div class="grd-bdy">
					<tr class="grd-bdy-rw">
						<td align="left" valign="top">Mohd kashif</td>
						<td align="left" valign="top">9871902082</td>
						<td align="left" valign="top">M-292 Sector25, Noida</td>
						<td align="left" valign="top">Battery</td>
						<td align="left" valign="top">Mickrotek</td>
						<td align="left" valign="top">Invatublar</td>
						<td align="left" valign="top">02-01-2018</td>
						<td align="left" valign="top">01-06-2018</td>
						<td align="left" valign="top">02-15-2018</td>
						<td align="left" valign="top">
							<span class="cursor" onClick="viewDetail();">View |</span>
							<span class="cursor" onClick="editDetail();">Edit |</span>
							<span class="cursor" onClick="updateService();">Update | </span>
							<span class="cursor red" onClick="deleteRow();">&#10008;</span>
						</td>
					</tr>
					<tr class="grd-bdy-rw-evn">
						<td align="left" valign="top">Mohd kashif</td>
						<td align="left" valign="top">9871902082</td>
						<td align="left" valign="top">M-292 Sector25, Noida</td>
						<td align="left" valign="top">Battery</td>
						<td align="left" valign="top">Mickrotek</td>
						<td align="left" valign="top">MoonLight</td>
						<td align="left" valign="top">02-01-2018</td>
						<td align="left" valign="top">01-06-2018</td>
						<td align="left" valign="top">02-15-2018</td>
						<td align="left" valign="top">
							<span class="cursor" onClick="viewDetail();">View |</span>
							<span class="cursor" onClick="editDetail();">Edit |</span>
							<span class="cursor" onClick="updateService();">Update | </span>
							<span class="cursor red" onClick="deleteRow();">&#10008;</span>
						</td>
					</tr>
					<tr class="grd-bdy-rw">
						<td align="left" valign="top">Mohd kashif</td>
						<td align="left" valign="top">9871902082</td>
						<td align="left" valign="top">M-292 Sector25, Noida</td>
						<td align="left" valign="top">Battery</td>
						<td align="left" valign="top">Mickrotek</td>
						<td align="left" valign="top">Invatublar</td>
						<td align="left" valign="top">02-01-2018</td>
						<td align="left" valign="top">01-06-2018</td>
						<td align="left" valign="top">02-15-2018</td>
						<td align="left" valign="top">
							<span class="cursor" onClick="viewDetail();">View |</span>
							<span class="cursor" onClick="editDetail();">Edit |</span>
							<span class="cursor" onClick="updateService();">Update | </span>
							<span class="cursor red" onClick="deleteRow();">&#10008;</span>
						</td>
					</tr>
					<tr class="grd-bdy-rw-evn">
						<td align="left" valign="top">Mohd kashif</td>
						<td align="left" valign="top">9871902082</td>
						<td align="left" valign="top">M-292 Sector25, Noida</td>
						<td align="left" valign="top">Battery</td>
						<td align="left" valign="top">Mickrotek</td>
						<td align="left" valign="top">MoonLight</td>
						<td align="left" valign="top">02-01-2018</td>
						<td align="left" valign="top">01-06-2018</td>
						<td align="left" valign="top">02-15-2018</td>
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


<!---------------------------Add New Deatil popup Code Start here--------------------------->


	<div id="addNewEntry" class="add-new-deatil-main-con hide">
		<div class="popu-header">
			<span class="heading">Add New Entry:</span>
			<span id="popupClose" class="popup-Close">X</span>
		</div>
		<div class="popup-body left">
			<div class="popup-body-inr-hdng">
				Prodcut Detail
			</div>
			<div class="popup-body-inner-rw-lt">
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Product:</span>
					<span class="inner-rw-val">
						<select class="popup-slectBx">
							<option value=""></option>
							<option value="">Battery</option>
							<option value="">Inverter</option>
							<option value="">Car battery</option>
						</select>
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Brand:</span>
					<span class="inner-rw-val">
						<select class="popup-slectBx">
							<option value=""><option>
							<option value="">Battery<option>
							<option value="">Inverter<option>
							<option value="">Car battery<option>
						</select>
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Model No:</span>
					<span class="inner-rw-val">
						<input type="text" name="modelNum" class="popup-inpt">
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Warranty:</span>
					<span class="inner-rw-val">
						<select class="popup-slectBx">
							<option value=""><option>
							<option value="">Battery</option>
							<option value="">Inverter</option>
							<option value="">Car battery</option>
						</select>
					</span>
				</div>
			</div>
			<div class="popup-body-inner-rw-rt">
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Date Sold:</span>
					<span class="inner-rw-val">
						<input type="text" name="dateSold" class="popup-inpt">
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Services:</span>
					<span class="inner-rw-val">
						<select class="popup-slectBx">
							<option value=""><option>
							<option value="">Battery</option>
							<option value="">Inverter</option>
							<option value="">Car battery</option>
						</select>
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Duration:</span>
					<span class="inner-rw-val">
						<select class="popup-slectBx">
							<option value=""><option>
							<option value="">Battery</option>
							<option value="">Inverter</option>
							<option value="">Car battery</option>
						</select>
					</span>
				</div>
				<div class="popup-body-inner-rw">
					<span class="inner-rw-label">Warranty End:</span>
					<span class="inner-rw-val">
						<input type="text" name="dateSold" class="popup-inpt">
					</span>
				</div>
			</div>
			<div class="popup-body-inr-hdng">
				Customer Detail
			</div>
			
			<div class="popup-body-inner-rw">
				<span class="inner-rw-label">Name:</span>
				<span class="inner-rw-val">
					<input type="text" name="dateSold" class="popup-inpt">
				</span>
			</div>
			<div class="popup-body-inner-rw">
				<span class="inner-rw-label">Address:</span>
				<span class="inner-rw-val">
					<input type="text" name="dateSold" class="popup-inpt">
				</span>
			</div>
			<div class="popup-body-inner-rw">
				<span class="inner-rw-label">Mobile #:</span>
				<span class="inner-rw-val">
					<input type="text" name="dateSold" class="popup-inpt">
				</span>
			</div>
			<div class="popup-body-inner-rw">
				<span class="inner-rw-label">Note:</span>
				<span class="inner-rw-val">
					<textarea class="popup-txtarea"></textarea>
				</span>
			</div>
			<div class="popup-body-inr-hdng">
				Additional Detail
			</div>
			<div class="popup-body-inner-rw">
				<span class="inner-rw-label">Where you heard about my shop:</span>
				<span class="inner-rw-val">
					<select class="popup-slectBx">
						<option value=""><option>
						<option value="">Battery</option>
						<option value="">Inverter</option>
						<option value="">Car battery</option>
					</select>
				</span>
			</div>
		</div>
		<div class="popup-body-inner-rw wdthfl">
			<span class="btn-cancel right">Cancel</span>
			<span class="btn-save right">Save</span>
		</div>
	</div>









<!---------------------------Add New Deatil popup Code End here--------------------------->























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