<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<title>Start Services <?php echo $title_for_layout ?></title> 
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<script>
			var base_url = "<?php echo base_url(); ?>";
			var is_mobile = "<?php (preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"])?1:0); ?>";
		</script>
		<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" type="image/x-icon">
		<link rel="stylesheet" href="<?php echo base_url("assets/css/jquery-ui.css");?>" type="text/css">
		<link rel="stylesheet" href="<?php echo base_url("assets/css/am_style.css");?>" type="text/css">
		<link rel="stylesheet" href="<?php echo base_url("assets/css/toastr.min.css");?>" type="text/css">
		<link rel="stylesheet" href="<?php echo base_url("assets/css/validationEngine.jquery.css");?>" type="text/css"/>
		<script src="<?php echo base_url("assets/js/jquery/jquery.js");?>"></script>
		<script src="<?php echo base_url("assets/js/jquery/jquery-ui.js");?>"></script>
		<?php echo $this->layouts->print_includes(); ?>
	</head>
	<body>
		
		<?php $this->load->view('dblayouts/db_main_header'); ?>
			<?php echo $content_for_layout; ?> 
		<?php $this->load->view('dblayouts/db_main_footer'); ?>

		<script src="<?php echo base_url("assets/js/dbscript.js");?>"></script>
		<script src="<?php echo base_url("assets/js/common.js");?>"></script>
		<script src="<?php echo base_url("assets/js/jquery.validationEngine-en.js");?>"></script>
		<script src="<?php echo base_url("assets/js/jquery.validationEngine.js");?>"></script>
		<script src="<?php echo base_url("assets/js/validator.js");?>"></script>
		<script src="<?php echo base_url("assets/js/toastr.min.js");?>"></script>
		<script src="<?php echo base_url("assets/sweetalert/dist/sweetalert.min.js");?>"></script>
		<script><?php echo $extra_head; ?></script>
	</body> 
</html>
