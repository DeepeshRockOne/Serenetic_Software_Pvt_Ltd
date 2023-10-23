<div class="container m-t-30">
   <div class="panel panel-default panel-block">
      <div class="panel-body">
         <div class="clearfix">
            <h4 class="pull-left m-b-0 m-t-7">
              Support
            </h4>
            <div class="pull-right">
               <a href="training_manuals.php" class="btn btn-action-o">Training Manuals</a>
               <!-- <a href="api_integrations.php" class="btn btn-info">API Integrations</a> -->
            </div>
         </div>
      </div>
   </div>
   <div class="panel panel-default panel-block" style="overflow: hidden;">
      <div class="panel-body">
         <div class="row">
            <div class="col-sm-4">
               <div class="assistance_box m-t-20">
                  <p class="text-white fs16">Have a concern? Idea? Request? Submit an eTicket to our agent support team below.</p>
                  <a href="javascript:void(0);" data-href="add_support_ticket.php" class="btn btn-white-o add_support_ticket">+ eTicket</a>
               </div>
            </div>
            <div class="col-sm-3 col-sm-offset-1">
               <p class="m-b-20 m-t-20 fs16">Have an immediate need that cannot wait? Start a Live Chat session with our agent support team below.</p>
                <a href="javascript:void(0);" class="btn btn-info btn_open_chat" id="btn_open_chat">Go Live</a>
            </div>
            <div class="col-sm-4">
               <div class="support_thumb">
                  <img src="<?=$AGENT_HOST?>/images/support-thumb.png" class="img-responsive">
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="panel panel-default panel-block">
      <div class="panel-body">
         <div class="clearfix">
            <?php include_once 'agent_portal_resources.inc.php'; ?>
         </div>
      </div>
   </div>
</div>
<script type="text/javascript">
   $(document).ready(function(){
      var not_win = '';
      $(".add_support_ticket").on('click',function(){
         $href = $(this).attr('data-href');
         var not_win = window.open($href, "myWindow", "width=768,height=615");
         if(not_win.closed) {  
            alert('closed');  
         } 
      });

      /*$(document).off("click",".btn_open_chat");
      $(document).on("click",".btn_open_chat", function () {
         $("#ajax_loader").show();
         $.ajax({
              method: 'GET',
              url: '<?=$HOST?>/login_chat_account.php?action=login_chat_account&location=agent',
              dataType : 'json',
          }).done((response) => {
               if(response.status == "success") {
                  SBChat.initChat();
                  $(".sb-chat-btn").show();
                  $(".btn_open_chat").hide();
                  setTimeout(function () {
                       SBChat.open();
                       $("#ajax_loader").hide();
                  }, 500);
               } else {
                  $("#ajax_loader").hide();
                  setNotifyError("Sorry, Please try after sometime.");
               }
          });             
      });

      $(document).on("click",".sb-chat:not(.sb-active) .sb-chat-btn", function () {
          $(this).hide();
          $(".btn_open_chat").show();
      });*/
    $(document).off('click','.view_resources');
    $(document).on('click','.view_resources',function(e){
       e.preventDefault();
       $href = $(this).attr('data-href');
       $.colorbox({
          href:$href,
          iframe: true, 
          width: '515px', 
          height: '300px'
       });
    });   
});   
</script>