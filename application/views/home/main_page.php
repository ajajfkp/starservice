<div class="dash-main-body left">
	<div class="left tp-rw">
		<span class="tday-hdng left">Services: </span>
		<span class="dt-sel left" id="serviceDatecal">
			<span class="filtertxt left" id="dateFltrText">Today</span>
			<span class="right">&#9660; </span>
		</span>
		<span class="dd-list hide" id="serviceDatelst">
			<ul>
				<li class="dd-listing" onclick="getServiceList('TD','Today');">Today</li>
				<li class="dd-listing" onclick="getServiceList('TR','Tomorrow');">Tomorrow</li>
				<li class="dd-listing" onclick="getServiceList('TW','This week');">This week</li>
				<li class="dd-listing" onclick="getServiceList('TM','This Month');">This Month</li>
				<li class="dd-listing" onclick="getServiceList('LM','Last Month');">Last Month</li>
				<li class="dd-listing" onclick="getServiceList('TY','This year');">This year</li>
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
			<tbody class="grid-bdy" id="defaultDataView">
			
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