<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
        <link rel="shortcut icon" href="<?= $HOST ?>/images/favicon.ico<?=$cache?>">
        <title><?php echo $SITE_NAME; ?></title> 
        <link type="text/css" rel="stylesheet" href="<?php echo $HOST; ?>/css/bootstrap.min.css">
        <link type="text/css" rel="stylesheet" href="<?php echo $HOST; ?>/css/landing.css<?=$cache?>">

        <?php if (isset($exStylesheets)): ?>
            <?php foreach ($exStylesheets as $styleSheet): ?>
                <link rel="stylesheet" type="text/css" href="<?= $HOST ?>/<?php echo $styleSheet ?>">
            <?php endforeach; ?>
        <?php endif; ?>

        <?php 
            if((strpos($_SERVER['HTTP_REFERER'],'/admin/') == false) && (strpos($_SERVER['HTTP_REFERER'],'/groups/') == false) && (strpos($_SERVER['HTTP_REFERER'],'/agents/') == false)){
                include_once 'bablic.inc.php';
            }
        ?>
        <script type="text/javascript" src="<?php echo $HOST; ?>/js/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo $HOST; ?>/js/bootstrap.min.js<?=$cache?>"></script>
        <?php if (isset($exJs)): ?>
            <?php foreach ($exJs as $js): ?>
                <script src="<?= $HOST ?>/<?php echo $js ?>" type="text/javascript"></script>
            <?php endforeach; ?>
        <?php endif; ?>
        <!-- lucky orange tag EL8-1140 -->
        <?php include_once dirname(__DIR__) . '/includes/lucky_orange_tracking_tag.php';?>
    </head>
    <body>
            <?php include_once 'landing_header.inc.php'; ?>
            <?php include_once dirname(__DIR__) . '/tmpl/notify.inc.php';?> 
            <?php include_once dirname(__DIR__) . '/tmpl/' . $template; ?>
            <?php include_once 'landing_footer.inc.php'; ?>
    </body>
</html>