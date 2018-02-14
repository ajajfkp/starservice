<div class="dash-main-body left">
	<div class="left tp-rw">
		<span class="tday-hdng left">Services: </span>
		<span class="dt-sel left" id="serviceDatecal">
			<span class="filtertxt left">Today</span>
			<span class="right filterarrow">&#9660; </span>
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
	<div class="menu-srch-rw">
		<div class="srch-opt-blk">
			<span class="search-optn">
				<input type="radio" name="srchOption" class="searchby" id="searchby" checked="" value="1">
			</span>
			<span class="search-optn-txt">Mobile</span>
		</div>
		<div class="srch-opt-blk">
			<span class="search-optn">
				<input type="radio" name="srchOption" class="searchby" id="byname" value="2">
			</span>
			<span class="search-optn-txt">Name</span>
		</div>
		<div class="srch-opt-blk">
			<span class="search-optn">
				<input type="radio" name="srchOption" class="searchby" id="byaddr" value="3">
			</span>
			<span class="search-optn-txt">Add.</span>
		</div>
		<span class="serach-bar">
			<input type="text" class="serach-inpt" placeholder="Search here..." id="searchinput">
		</span>
	</div>
	<div class="left grd-rw" id="defaultDataView">
	
	</div>
</div>