
<?php
//print_r($getServiceData);die;
?>

<div class="container" style="margin-top: 50px;">
	<div class="panel-heading">
		<div class="row">
			<nav class="navbar navbar-default" role="navigation" style="min-height: 0;border-radius: 10px;">
				<a class="btn btn-lg btn-success" href="javascript:void(0);" id="addNewSer">
					<i class="glyphicon glyphicon-plus"></i>&nbsp;Add New Service
				</a>
				<div class="dropdown" style="float: right;margin-top: 5px;">
					<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Select Date
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						<li><a href="#">Today</a></li>						
						<li><a href="#">Tomorrow</a></li>
						<li><a href="#">This week</a></li>
						<li><a href="#">This Month</a></li>
					</ul>
				</div>
			</nav>
			
		</div>
	</div>
	<div class="row">
        <div class="col-md-12">
			<div class="table-responsive">   
				<table id="mytable" class="table table-bordred table-striped">
					<thead>
						<th>Name</th>
						<th>Contact</th>
						<th>Address</th>
						<th>Product</th>
						<th>Purchase date</th>
						<th>#Services</th>
						<th>Service</th>
						<th>Pending </th>
						<th>View</th>
						<th>Edit</th>
						<th>Delete</th>
					</thead>
					<tbody>
					<?php
					if($getServiceData){
						foreach($getServiceData as $serviceData){
					?>
						<tr>
							<td><?php echo ucfirst($serviceData['name']);?></td>
							<td><?php echo "+91 ".$serviceData['contact'];?></td>
							<td><?php echo $serviceData['address'];?></td>
							<td><?php echo 'Inverter' ?></td>
							<td><?php echo '05-12-2017' ?></td>
							<td><?php echo '3' ?></td>
							<td><?php echo date("d-M-Y",strtotime($serviceData['service_date']));?></td>
							<td><?php echo '2' ?></td>
							<td>
								<p title="Edit">
									<button class="btn btn-primary btn-xs" onclick="editservic('<?php echo $serviceData['id']?>');">
										<span class="glyphicon glyphicon-pencil">View</span>
									</button>
								</p>
							</td>
							<td>
								<p title="Edit">
									<button class="btn btn-primary btn-xs" onclick="editservic('<?php echo $serviceData['id']?>');">
										<span class="glyphicon glyphicon-pencil"></span>
									</button>
								</p>
							</td>
							<td>
								<p title="Delete">
									<button class="btn btn-danger btn-xs" onclick="deleteservic('<?php echo $serviceData['id']?>');">
										<span class="glyphicon glyphicon-trash"></span>
									</button>
								</p>
							</td>
						</tr>
					<?php
						}
					}
					?>
					</tbody>
				</table>
			</div>
        </div>
	</div>
</div>
    
    
    
    <div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
      <div class="modal-dialog">
    <div class="modal-content">
          <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
        <h4 class="modal-title custom_align" id="Heading">Delete this entry</h4>
      </div>
          <div class="modal-body">
       
       <div class="alert alert-danger"><span class="glyphicon glyphicon-warning-sign"></span> Are you sure you want to delete this Record?</div>
       
      </div>
        <div class="modal-footer ">
        <button type="button" class="btn btn-success" ><span class="glyphicon glyphicon-ok-sign"></span> Yes</button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> No</button>
      </div>
        </div>
    <!-- /.modal-content --> 
  </div>
      <!-- /.modal-dialog --> 
    </div>