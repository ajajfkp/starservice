<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<meta name="description" content="">
		<meta name="author" content="">

		<title>Star services <?php echo $title_for_layout ?></title> 
		<script>
			var base_url = "<?php echo base_url(); ?>";
		</script>
		<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/am_style.css" type="text/css">
		<script src="<?php echo base_url();?>assets/js/jquery/jquery.js"></script>
		<script type="text/javascript">
			var base_url = '<?php echo base_url(); ?>'
		</script>
		<?php echo $this->layouts->print_includes(); ?>
	</head>
	
	<body>
		<?php $this->load->view('layouts/main_header'); ?>
			<?php echo $content_for_layout; ?> 
		<?php $this->load->view('layouts/main_footer'); ?>
		<script src="<?php echo base_url();?>assets/js/common.js"></script>
		<script><?php echo $extra_head; ?></script>
	</body> 
</html>
