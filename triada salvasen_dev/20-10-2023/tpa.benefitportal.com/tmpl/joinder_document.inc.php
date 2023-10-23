<!DOCTYPE html>
<html lang="en">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15">
   </head>
   <body>
      <center>
         <div class="policy_doc_wrap">
            <div class="term_info">
               <p class="common_title">Joinder Agreement</p>
                <div>
                  <?php echo $agreementContent; ?>
                </div>
            </div>
            <br>
            <div class="bottom_info_wrap">
               <p class="common_title">Signature:</p>
               <div class="bottom_info_box">
               <?php if($resAgreement["application_type"] == "voice_verification"){ ?>
                     Voice Verification
               <?php }else{ ?>
                  <?php if(!empty($signature_data)){ ?>
                     <img src="<?= $signature_data ?>" style="height: 20px;">
                  <?php } ?>
               <?php } ?>
               </div>
            </div>
            <div class="bottom_info_wrap">
               <p class="common_title">Date:</p>
               <div class="bottom_info_box">
                  <?=displayDate($resAgreement['created_at'])?>
               </div>
            </div>
            <div class="bottom_info_wrap last_child">
               <p class="common_title">&nbsp;</p>
               <div class="bottom_info_box" style="line-height:inherit;">
                  <strong>IP Address:</strong> 
                  <?=$resAgreement['ip_address']?><br>
                  <strong>Enrollment Date:</strong> 
                  <?=$resAgreement['created_at']?>
               </div>
            </div>
         </div>
      </center>
   </body>
</html>