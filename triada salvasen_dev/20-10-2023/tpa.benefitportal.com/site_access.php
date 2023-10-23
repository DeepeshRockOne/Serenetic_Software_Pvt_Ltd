<?php
$site_access_page="Y";
include_once __DIR__ . '/includes/connect.php';
if (isset($_POST['submit'])) {

  $password = $_POST['access_password'];

  if (strcmp("operation2020", $password) == 0) {
    $_SESSION['site_access'] = "YES";
    redirect($HOST.'/index.php');
    
  } else {
    $error = "Sorry, your site access password is incorrect.<br>
            Please contact $SITE_NAME Administrators for details.";
  }
}
?>
<!DOCTYPE HTML>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $SITE_NAME; ?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet" />
   <style type="text/css">
	 body { font-family:Arial; }
.error{ color:#F00; font-size:13px !important;}
.offwhite_belt { background: #050606 ; width: 100%; min-height: 180px; }
.site_acc_area { background: #ffffff; padding: 5px; width: 340px; margin: 0 auto; margin-top: -100px; text-align: center; }
.site_acc_area img { display: inline-block; height: auto; max-width: 100%; padding: 15px 0 ; }
.site_acc_inner { background: #ffffff; border: 5px solid #5694cc; box-shadow: 1px 2px 1px rgba(0, 0, 0, 0.2); }
.site_form { text-align: left; overflow: hidden; }
.site_form_padd { padding: 10px 20px 15px; }
.site_form_padd label { color: #000000;  font-weight:normal;}
.site_form_padd .form-control {background: #fff; color: #000; height: 36px; padding:3px 17px; width: 100%; margin-bottom: 0px;  box-shadow:none;}
.site_form_padd .form-control:focus { box-shadow:none; border-color:#5694cc;}
.btn.btn-common { border-color: #5694cc; color: #ffd8be; padding: 10px 20px; }
.btn.btn-common:hover { background: #5694cc; border-color: #5694cc; color: #000000; }

@media  screen and (max-width: 320px) {
.site_acc_area { width:300px;}
}
.btn-blue-default,.btn-blue-default.active,.btn-blue-default:active,.btn-blue-default:focus{ background-color:#5694cc; color:#fff;border:1px solid #5694cc ;font-weight:normal;  transition: all 0.2s ease;-webkit-transition: all 0.2s ease; text-transform:none;box-shadow:none;}
.btn-blue-default:hover{ background-color:#fff;color:#5694cc; border-color:#5694cc;}
 </style> 
  </head>
  <body class="site_access" style="background:#ffffff;">
    <header class="offwhite_belt"> </header>
    <div class="container">
      <div class="site_acc_area">
        <div class="site_acc_inner"><img src="images/logo.svg" class="img-responsive" style="max-width:200px;" alt="CyberX Groups" style="max-width:80%;">
          <div class="row">
            <div class="col-sm-12">
              <form id="frm_site_secure" name="frm_site_secure" method="POST">           	
                <div class="form-group site_form height_auto">
                  <div class="site_form_padd">
                  	<div class="form-group">
                    <label>Site Access Password </label>
                    <input id="alteredpass" name="access_password" value="" class="form-control" placeholder="Site Access Password" type="password">
                    <?php if (isset($error) != "") { ?>
                      <p class="error"><?php echo $error; ?></p>
                    <?php } ?>
                    </div>
                    
                    <input id="alteredsbmt" name="submit" value="Submit"  class="btn btn-blue-default pull-right" type="submit">
                    <input id="http_referer" name="http_referer" value="" type="hidden">
                    <div class="clearfix"></div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--<script src="js/jquery.min.js" type="text/javascript"></script> 
    <script src="js/bootstrap.js" type="text/javascript"></script>    -->    
  </body>
</html>
