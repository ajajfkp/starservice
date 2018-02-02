<!doctype>
<html>
<head>
<meta name="viewport" content="width=device-width, height=device-height" >
<title><?php echo $title ?></title>
<link rel="stylesheet" type="text/css" a href="<?php echo $this->config->base_url();?>css/am_style.css">

<script type="text/javascript" src="<?php echo base_url();?>js/jquery.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/common/common.js"></script>
<script>
	var base_url ='<?php echo base_url();?>';
</script>
</head>

<body>

<div class="main-cont">
	<?php Include 'header.php'; ?>
	<div class="mainBody">
		<?php echo $content_for_layout;?>
	</div>
	<?php Include 'footer.php'; ?>
</div>
</body>
</html>