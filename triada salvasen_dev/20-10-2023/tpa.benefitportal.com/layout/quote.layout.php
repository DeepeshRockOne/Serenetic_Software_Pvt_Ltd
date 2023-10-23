<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
        <link rel="shortcut icon" href="<?=$HOST?>/images/favicon.ico<?=$cache?>">
        <title><?php echo $SITE_NAME; ?></title>
        <link rel="stylesheet" type="text/css" href="<?=$HOST?>/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="<?=$HOST?>/thirdparty/bootstrap-select/css/bootstrap-select.css">
        <link rel="stylesheet" type="text/css" href="<?=$HOST?>/css/theme/theme.css<?=$cache?>">
        <link rel="stylesheet" type="text/css" href="<?=$HOST?>/css/theme/style.css<?=$cache?>">
        <link rel="stylesheet" type="text/css" href="<?=$HOST?>/thirdparty/colorbox/colorbox.css">
        <link rel="stylesheet" type="text/css" href="<?=$HOST?>/thirdparty/jquery_uniform/themes/default/css/uniform.default.css">
        <link rel="stylesheet" type="text/css" href="<?=$HOST?>/thirdparty/bootstrap-datepicker-master/css/datepicker.css<?=$cache?>">
        <link rel="stylesheet" type="text/css" href="<?=$HOST?>/thirdparty/sweetalert2/sweetalert2.css<?=$cache?>"> 
        <link rel="stylesheet" type="text/css" href="<?=$HOST?>/thirdparty/malihu_scroll/css/jquery.mCustomScrollbar.css">
        <?php if (isset($exStylesheets)): ?>
            <?php foreach ($exStylesheets as $styleSheet): ?>
                    <link rel="stylesheet" type="text/css" href="<?=$HOST?>/<?php echo $styleSheet ?>">
            <?php endforeach;?>
        <?php endif;?>
        <?php include_once 'bablic.inc.php'; ?>
        <script type="text/javascript" src="<?=$HOST?>/js/jquery.min.js"></script>
        <?php if (isset($tmpExJs)): ?>
        <?php foreach ($tmpExJs as $tmpJS): ?>
        <script src="<?=$HOST?>/<?php echo $tmpJS ?>" type="text/javascript"></script>
        <?php endforeach;?>
        <?php endif;?>
        <script type="text/javascript" src="<?=$HOST?>/js/bootstrap.min.js<?=$cache?>"></script>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/malihu_scroll/js/jquery.mCustomScrollbar.concat.min.js"></script>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/bootstrap-select/js/bootstrap-select.min.js"></script>
        <script type="text/javascript" src="<?=$HOST?>/js/waves.js"></script>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/simscroll/jquery.slimscroll.min.js<?=$cache?>"></script>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/sidebar-nav/src/metisMenu.js"></script>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/jquery_uniform/jquery.uniform.js"></script>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/sweetalert2/sweetalert2.min.js"></script>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/colorbox/jquery.colorbox.js"></script>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/scroll-tabs/dist/jquery.scrolling-tabs.min.js"></script>
        <script type="text/javascript" src="<?= $HOST ?>/thirdparty/jquery_custom.js<?=$cache?>"></script>

        <?php if (isset($exJs)): ?>
            <?php foreach ($exJs as $js): ?>
                <script src="<?=$HOST?>/<?php echo $js ?>" type="text/javascript"></script>
            <?php endforeach;?>
        <?php endif;?>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/bootstrap-datepicker-master/js/bootstrap-datepicker.js<?=$cache?>"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $("input[type='radio'], input[type='checkbox']").not('.js-switch').uniform();
            });
        </script>
        <!-- lucky orange tag EL8-1140 -->
        <?php include_once dirname(__DIR__) . '/includes/lucky_orange_tracking_tag.php';?> 
    </head>
    <body>
        <div id="ajax_loader" class="ajex_loader fixed" style="display: none;"></div>
        <div id="wrapper">            
            <?php include_once dirname(__DIR__) . '/tmpl/notify.inc.php';?>
            <?php include_once 'quote_header.inc.php'; ?>
            <?php include_once dirname(__DIR__) . '/tmpl/' . $template; ?>
        </div>
        <?php include_once 'quote_footer.inc.php'; ?>  
        <script type="text/javascript" src="<?=$HOST?>/js/custom.min.js<?=$cache?>"></script>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/styleswitcher/jQuery.style.switcher.js"></script>
    </body>
</html>