<!DOCTYPE html>
<html lang="en">
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
        <link rel="stylesheet" type="text/css" href="<?=$AGENT_HOST?>/css/agents.css<?=$cache?>">
        <link rel="stylesheet" type="text/css" href="<?=$HOST?>/thirdparty/colorbox/colorbox.css<?=$cache?>">
   		<link rel="stylesheet" type="text/css" href="<?=$HOST?>/thirdparty/jquery_uniform/themes/default/css/uniform.default.css">
        <link rel="stylesheet" type="text/css" href="<?=$HOST?>/thirdparty/bootstrap-datepicker-master/css/datepicker.css<?=$cache?>">
        <link rel="stylesheet" type="text/css" href="<?=$HOST?>/thirdparty/sweetalert2/sweetalert2.css<?=$cache?>">
        <?php if (isset($exStylesheets)): ?>
		  <?php foreach ($exStylesheets as $styleSheet): ?>
          	<link rel="stylesheet" type="text/css" href="<?=$HOST?>/<?php echo $styleSheet ?>" />
          <?php endforeach;?>
        <?php endif;?>
        <script type="text/javascript" src="<?=$HOST?>/js/jquery.min.js<?=$cache?>"></script>
        <script type="text/javascript">
            var $AGENT_HOST="<?=$AGENT_HOST?>";
            var $HOST="<?=$HOST?>";
        </script>
        <?php if (isset($tmpExJs)): ?>
        <?php foreach ($tmpExJs as $tmpJS): ?>
        <script src="<?=$HOST?>/<?php echo $tmpJS ?>" type="text/javascript"></script>
        <?php endforeach;?>
        <?php endif;?>
        <script type="text/javascript" src="<?=$HOST?>/js/bootstrap.min.js<?=$cache?>"></script>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/bootstrap-select/js/bootstrap-select.min.js"></script>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/simscroll/jquery.slimscroll.min.js<?=$cache?>"></script>
        <script type="text/javascript" src="<?=$HOST?>/js/notification.js<?=$cache?>"></script>
        <script type="text/javascript" src="<?=$AGENT_HOST?>/js/common.js<?=$cache?>"></script>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/jquery_uniform/jquery.uniform.js<?=$cache?>"></script>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/colorbox/jquery.colorbox.js<?=$cache?>"></script>
        <script type="text/javascript" src="<?= $HOST ?>/thirdparty/jquery_custom.js<?=$cache?>"></script>
        <?php if (isset($exJs)): ?>
		  <?php foreach ($exJs as $js): ?>
            <script src="<?=$HOST?>/<?php echo $js ?>" type="text/javascript"></script>
          <?php endforeach;?>
        <?php endif;?>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/bootstrap-datepicker-master/js/bootstrap-datepicker.js<?=$cache?>"></script>
        <!-- lucky orange tag EL8-1140 -->
        <?php include_once dirname(__DIR__) . '/../includes/lucky_orange_tracking_tag.php';?>
    </head>
    <?php 
        if(isset($_SESSION['agents']['id'])) {
            $theme_color = $pdo->selectOne("SELECT theme_color from customer_settings where id = :id",array(":id" => $_SESSION['agents']['id']));
        } else {
            $theme_color = array();
        }
        
    ?>
    <body class="iframe <?=isset($theme_color['theme_color']) && !empty($theme_color['theme_color']) ? $theme_color['theme_color'] : "skin-default" ?>">
    <div id="ajax_loader" class="ajex_loader fixed" style="display: none;"></div>
    <script>
      $(document).ready(function() {
            //start set Cookie for check login activity
                var iFramedt = new Date();
                var month = iFramedt.getMonth()+1;
                var day = iFramedt.getDate();
                var fullDate =  iFramedt.getFullYear()+ '-' + (month<10 ? '0' : '') +month + '-' + (day<10 ? '0' : '') + day ;

                var iFrameRefreshTime = fullDate + ' ' + iFramedt.getHours() + ":" + iFramedt.getMinutes();
                localStorage.setItem("AgentraLoginTime", iFrameRefreshTime);

            //end coockie for check login activity
             	$("input[type='radio'], input[type='checkbox']").not('.js-switch').uniform();
            });
    </script>
        <div id="wrapper">
            
                  <?php include_once dirname(__DIR__) . '/tmpl/notify.inc.php'; ?>
                <?php include_once dirname(__DIR__) . '/tmpl/' . $template;?>
            
        </div>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/scroll-tabs/dist/jquery.scrolling-tabs.min.js"></script>
        <script type="text/javascript" src="<?=$HOST?>/thirdparty/sweetalert2/sweetalert2.min.js"></script>
        <script type="text/javascript" src="<?=$HOST?>/js/custom.min.js<?=$cache?>"></script>
    </body>
</html>