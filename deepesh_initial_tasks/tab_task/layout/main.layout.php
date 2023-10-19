<!DOCTYPE html>
<html lang="en"><head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="shortcut icon" href="<?=$HOST?>/images/favicon.ico<?=$cache?>"> 
		<title><?php echo $SITE_FAVICON_TEXT; ?></title> 
	
		<?php if (isset($exStylesheets)): ?>
		  <?php foreach ($exStylesheets as $styleSheet): ?>
            <link rel="stylesheet" type="text/css" href="<?=$HOST?>/<?php echo $styleSheet ?>" />
          <?php endforeach;?>
        <?php endif;?>
   
		<!-- <script type="text/javascript" src="<?=$HOST?>/js/jquery.min.js"></script> -->
		<script type="text/javascript" src="js/jquery.min.js"></script>
	
		<?php if (isset($exJs)): ?>
				<?php foreach ($exJs as $js): ?>
					<script src="<?=$HOST?>/<?php echo $js ?>" type="text/javascript"></script>
      		  	<?php endforeach;?>
		<?php endif;?>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
	</head>
	<body>
	
		<div id="wrapper">
			<?php include_once 'header.inc.php';?>
        		
			<?php include_once 'left.inc.php';?>

			<div id="page-wrapper">
			    <div class="container-fluid">
					<?php include_once dirname(__DIR__) . '/tmpl/' . $template;?>
	            </div>
				<?php include_once 'footer.inc.php';?>
	   	 	</div>
		</div>
	</body>
</html>