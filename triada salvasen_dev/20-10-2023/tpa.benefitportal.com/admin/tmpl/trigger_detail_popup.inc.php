<div class="panel panel-default panel-popup">
    <div class="panel-heading">
        <div class="panel-title">
            <i class="icon-envelope"></i>       
            <span><?php echo $type == 'email' ? 'Email' : 'SMS' ?> Details: <?= $title; ?></span>       
        </div>
    </div>
    <div class="panel-body">
        <?php if ($type == 'email') { ?>
           
                <div class="col-md-12 text-center">  
                    <p><b>Email Subject:</b> <?= $email_subject ?> </p>              
                </div>
             
            <div class="clearfix"> </div>
            <div class="col-sm-12">
            	<div class="email_contant" style="display:none;">
                  <?= str_replace("[[fname]]", checkIsset($resC['fname']), htmlspecialchars_decode($email_content)) ?>
              </div>
              <div class="email_display"></div>
            </div>
        <?php } else { ?>
        		<ul class="chat-list custom-chat">
            		 <li class="odd">
                      <div class="chat-body">
                          <div class="chat-text">
                              <p class="p-t-0"><?= html_entity_decode($trigger_res['sms_content']); ?></p>
                           </div>
                      </div>
                  </li>                            
            </ul>
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
			obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
	}
</script>
 