<!DOCTYPE html>
<html lang="en">
<head>	
   	    <meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="shortcut icon" href="<?=$HOST?>/images/favicon.ico<?=$cache?>">
		<title><?php echo $SITE_NAME; ?></title>
		<link rel="stylesheet" type="text/css" href="<?=$HOST?>/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="<?=$HOST?>/thirdparty/bootstrap-select/css/bootstrap-select.css" />
		<link rel="stylesheet" type="text/css" href="<?=$HOST?>/css/theme/theme.css<?=$cache?>" />
		<link rel="stylesheet" type="text/css" href="<?=$HOST?>/css/theme/style.css<?=$cache?>" />
        <link rel="stylesheet" type="text/css" href="<?=$GROUP_HOST?>/css/groups.css<?=$cache?>" /> 
		<?php if (isset($exStylesheets)): ?>
			<?php foreach ($exStylesheets as $styleSheet): ?>
				<link rel="stylesheet" type="text/css" href="<?=$HOST?>/<?php echo $styleSheet ?>" />
			<?php endforeach;?>
		<?php endif;?>
        <script type="text/javascript" src="<?=$HOST?>/js/jquery.min.js"></script>
		<?php if (isset($exJs)): ?>
			<?php foreach ($exJs as $js): ?>
				<script src="<?=$HOST?>/<?php echo $js ?>" type="text/javascript"></script>
			<?php endforeach;?>
		<?php endif;?>
		<script type="text/javascript" src="<?=$HOST?>/thirdparty/simscroll/jquery.slimscroll.min.js<?=$cache?>"></script>
	  	<script type="text/javascript" src="<?= $HOST ?>/thirdparty/jquery_custom.js<?=$cache?>"></script>
		<script type="text/javascript" src="<?=$HOST?>/js/bootstrap.min.js<?=$cache?>"></script>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/bootstrap-select/js/bootstrap-select.min.js"></script>
		<script type="text/javascript" src="<?=$GROUP_HOST?>/js/common.js"></script>
		<script type="text/javascript" src="<?=$HOST?>/js/custom.min.js<?=$cache?>"></script>
        <!-- lucky orange tag EL8-1140 -->
        <?php include_once dirname(__DIR__) . '/../includes/lucky_orange_tracking_tag.php';?>
	</head>
    <body class="login-page">
    	<div id="ajax_loader" class="ajex_loader fixed" style="display: none;"></div>
        <?php include_once dirname(__DIR__) . '/tmpl/' . $template; ?>
      	<?php //include_once 'footer.inc.php';?>
        
    </body>
</html>