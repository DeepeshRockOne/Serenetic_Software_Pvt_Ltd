<!DOCTYPE html>
<html lang="en">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15">
   </head>
   <body>

     <body style="font-family:Roboto, Helvetica, sans-serif;background-color:#fff;">
      <div style="display:inline-block; text-align:right;">
          <h4>AGENT AGREEMENT</h4>
            <p><?=$agentInfo['fname'].' '.$agentInfo['lname'].' ('.$agentInfo['rep_id'].')'?></p>
            <p><a href="mailto:<?=$agentInfo['email']?>"><?=$agentInfo['email']?></a></p>
            <p><?=format_telephone($agentInfo['cell_phone'])?></p>
            <?php 
              if(!empty($agentInfo['ip_address'])){
                echo "<p>".$agentInfo['ip_address']."</p>";
              } 
            ?>
            <p><?=displayDate($agentInfo['joined_date'])?></p>
      </div>
      <?=$terms?>

      <br>
      <div class="bottom_info_wrap">
         <p class="common_title">Signature:</p>
         <div class="bottom_info_box">
            <?php if(!empty($signature_data)){ ?>
               <img src="<?= $signature_data ?>" style="height: 20px;">
            <?php } ?>
         </div>
      </div>
      <div class="bottom_info_wrap">
         <p class="common_title">Date:</p>
         <div class="bottom_info_box">
            <?=displayDate($agentInfo['joined_date'])?>
         </div>
      </div>
      <div class="bottom_info_wrap last_child">
         <p class="common_title">&nbsp;</p>
         <div class="bottom_info_box" style="line-height:inherit;">
            <strong>IP Address:</strong> <?=$agentInfo['ip_address']?><br>
            <strong>Enrollment Date:</strong> <?=displayDate($agentInfo['joined_date'])?>
         </div>
      </div>
   </body>
</html>