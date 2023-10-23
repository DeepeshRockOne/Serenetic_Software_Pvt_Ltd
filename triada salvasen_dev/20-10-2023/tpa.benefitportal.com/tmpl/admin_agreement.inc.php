<!DOCTYPE html>
<html>
  <head>
    <title></title>
  </head>
  <body style="font-family:Roboto, Helvetica, sans-serif;background-color:#fff;">
    <div style="display:inline-block; text-align:right;">
      <h4>ADMIN AGREEMENT</h4>
      <p><?php echo $admin_row['fname'].' '.$admin_row['lname'].' ('.$admin_row['display_id'].') ' ?></p>
      <p><a href="mailto:<?php echo $admin_row['email'] ?>"><?php echo $admin_row['email'] ?></a></p>
      <p><?php echo format_telephone($admin_row['phone']) ?></p>
      <?php echo (!empty($admin_row['ip_address']) ?'<p>'.$admin_row['ip_address'].'</p>': '') ?>
      <p><?php echo date('m/d/Y',strtotime($admin_row['created_at'])) ?></p>
    </div>
    <?php echo $terms; ?>
  </body>
</html>