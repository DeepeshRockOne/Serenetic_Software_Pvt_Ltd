<div class="panel panel-default panel-popup">
  <div class="panel-heading">
    <div class="panel-title">
      <i class="icon-envelope"></i>       
      <span>Trigger Details: <?= $title; ?></span>       
    </div>
  </div>
  <div class="panel-body">
    <div class="col-sm-10">
      <label for="description" class=" col-md-2 "><b>Description:</b></label>
      <div class="col-md-10">          
        <p><?= $description ?>               </p>
      </div>
    </div>  
    <?php if ($type == 'SMS' || $type == 'Both') { ?>
      <div class="col-sm-10">
        <label for="sms_content" class=" col-md-2 "><b>SMS Content:</b></label>
        <div class="col-md-10">       
          <?= $sms_content ?>               
        </div>
      </div>
    <?php } ?>
    <?php if ($type == 'Email' || $type == 'Both') { ?>
      <div class="col-sm-10">
        <label for="email_subject" class=" col-md-2 "><b>Email Subject:</b></label>
        <div class="col-md-10">  
          <?= $email_subject ?>               
        </div>
      </div> 
      <div class="clearfix">
      </div>
      <div class="col-sm-12">
        <div class="email_contant" style="display:none;">
					<?= htmlspecialchars_decode($email_content) ?>
        </div>
        <div class="email_display"></div>
      </div>
    <?php } ?>
  </div>
</div>
<script type="text/javascript">

$(function() {
		var $frame = $("<iframe style='width:100%;  border:none;' border='0'>");
		$('.email_display').html( $frame );
		setTimeout( function() {
				var doc = $frame[0].contentWindow.document;
				var $body = $('body',doc);
				$body.html($('.email_contant').html());
				resizeIframe($(".email_display iframe")[0]);
		}, 1 );
		
});

	function resizeIframe(obj) {
			obj.style.height = (obj.contentWindow.document.body.scrollHeight+20) + 'px';

	}
</script>
            
