<?php include("notify.inc.php"); ?>
<div class="view_template">
  <div class="tabbing-tab">
    <ul class="nav nav-tabs customtab nav-noscroll">
       <?php
      $trigger = "active";
      include("br_broadcaster_tabs.inc.php");
      ?> 
    </ul>
  </div>
  <div class="panel panel-default panel-block">
    <div class="panel-heading">
      <div class="pull-left">
        <i class="fa fa-envelope"></i>
        <h1><span>View Trigger Template</span></h1>
      </div>
      <div class="pull-right">
      	<a class="btn btn-default" href="trigger_template.php">Back</a>
      </div>
    </div>
  </div>
  <div class="tabbing-tab">
    <ul class="nav nav-tabs customtab">
      <?php $trigger_template = 'active'; ?>
      <?php include_once('triggers_tabs.inc.php'); ?> 
       
    </ul>
  </div>	
  <div class="panel panel-default panel-block ">
    <div class="panel-body">
          <div class="row">            
              <div class="col-md-12">  
              		<div class="email_contant" style="display:none;">              
                    <?php echo html_entity_decode($template_data); ?>        
                  </div>
                   <div class="email_display"></div>          
              </div>
          </div>
    </div>
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
            