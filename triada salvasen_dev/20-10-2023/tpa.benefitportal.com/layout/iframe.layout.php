<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
        <link rel="shortcut icon" href="<?= $HOST ?>/images/favicon.ico<?=$cache?>">
        <title><?php echo $SITE_NAME; ?></title> 
        <link type="text/css" rel="stylesheet" href="<?php echo $HOST; ?>/css/bootstrap.min.css" >
        <link rel="stylesheet" type="text/css" href="<?=$HOST?>/thirdparty/bootstrap-select/css/bootstrap-select.css">
        <link rel="stylesheet" type="text/css" href="<?=$HOST?>/thirdparty/colorbox/colorbox.css">
        <link rel="stylesheet" type="text/css" href="<?=$HOST?>/thirdparty/jquery_uniform/themes/default/css/uniform.default.css">
        <link rel="stylesheet" type="text/css" href="<?=$HOST?>/css/theme/theme.css<?=$cache?>">
        <link rel="stylesheet" type="text/css" href="<?=$HOST?>/css/theme/style.css<?=$cache?>">
        <link rel="stylesheet" type="text/css" href="<?=$HOST?>/css/landing.css<?=$cache?>">
        <link rel="stylesheet" type="text/css" href="<?=$HOST?>/thirdparty/bootstrap-datepicker-master/css/datepicker.css<?=$cache?>">
        <link rel="stylesheet" type="text/css" href="<?=$HOST?>/thirdparty/sweetalert2/sweetalert2.css<?=$cache?>"> 

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
        <script type="text/javascript">
            $HOST='<?= $HOST ?>';
        </script>
        <?php if (isset($tmpExJs)): ?>
        <?php foreach ($tmpExJs as $tmpJS): ?>
        <script src="<?=$HOST?>/<?php echo $tmpJS ?>" type="text/javascript"></script>
        <?php endforeach;?>
        <?php endif;?>
        <script type="text/javascript" src="<?php echo $HOST; ?>/js/bootstrap.min.js<?=$cache?>"></script>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/bootstrap-select/js/bootstrap-select.min.js"></script>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/colorbox/jquery.colorbox.js"></script>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/simscroll/jquery.slimscroll.min.js<?=$cache?>"></script>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/sidebar-nav/src/metisMenu.js"></script>
        <script type="text/javascript" src="<?=$HOST?>/js/notification.js<?=$cache?>"></script>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/jquery_uniform/jquery.uniform.js"></script>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/sweetalert2/sweetalert2.min.js"></script>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/scroll-tabs/dist/jquery.scrolling-tabs.min.js"></script>
        <script type="text/javascript" src="<?=$HOST?>/js/waves.js"></script>
        <script type="text/javascript" src="<?=$HOST?>/js/custom.min.js<?=$cache?>"></script>
        <script type="text/javascript" src="<?= $HOST ?>/thirdparty/jquery_custom.js<?=$cache?>"></script>
        
        <script type="text/javascript">
            $(document).ready(function() {  
                    $("input[type='radio'], input[type='checkbox']").not('.js-switch').uniform();
            });
        </script>
        <?php if (isset($exJs)): ?>
            <?php foreach ($exJs as $js): ?>
                <script src="<?= $HOST ?>/<?php echo $js ?>" type="text/javascript"></script>
            <?php endforeach; ?>
        <?php endif; ?>
        <script type="text/javascript" src="<?=$ADMIN_HOST?>/js/bootstrap-datetimepicker.js"></script>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/bootstrap-datepicker-master/js/bootstrap-datepicker.js<?=$cache?>"></script>
        <!-- lucky orange tag EL8-1140 -->
        <?php include_once dirname(__DIR__) . '/includes/lucky_orange_tracking_tag.php';?>
    </head>
    <?php 
        $theme_color = '';
        if(!empty($_REQUEST['location']) && in_array($_REQUEST['location'],array("agent","group"))) {
            if($_REQUEST['location'] == "agent") {
                $user_id = $_SESSION['agents']['id'];
            } else {
                $user_id = $_SESSION['groups']['id'];
            }
            
            $cs_row = $pdo->selectOne("SELECT theme_color from customer_settings where customer_id = :id",array(":id" => $user_id));
            if(!empty($cs_row)) {
                $theme_color = $cs_row['theme_color'];
            }
        } elseif(!empty($_REQUEST['user_type']) && in_array($_REQUEST['user_type'],array("Agent","Group")) && !empty($_REQUEST['user_id'])){
            $cs_row = $pdo->selectOne("SELECT theme_color from customer_settings where md5(customer_id) = :id",array(":id" => $_REQUEST['user_id']));
            if(!empty($cs_row)) {
                $theme_color = $cs_row['theme_color'];
            }
        }
        
    ?>
   <body class="iframe <?=!empty($theme_color) ? $theme_color : "skin-default" ?> <?php echo checkIsset($body_class) ?>">
        <div id="ajax_loader" class="ajex_loader fixed" style="display: none;"></div>
        <div id="wrapper">            
            <?php include_once dirname(__DIR__) . '/tmpl/notify.inc.php';?>
            <?php include_once dirname(__DIR__) . '/tmpl/' . $template;?>            
        </div>
    </body>
</html>