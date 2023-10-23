<?php include("notify.inc.php"); ?>
<form action="" role="form" method="post" name="user_form" id="user_form" enctype="multipart/form-data">
  <div class="panel panel-default panel-block">
    <?php if(checkIsset($_GET['iframe'])=='true') { ?>
    <div class="panel-heading">
      <div class="panel-title"><h4 class="h4">Generic Agent Self Application Link's - <span class="fw300"><?=checkIsset($sponsor_detail["agentName"])?></span></h4></div>
    </div>
    <?php }?>
    <div class="panel-body">
      <div class="generic_url_box">
         <?php if(count($coded_res)>0){
      foreach($coded_res as $key=>$val){
      ?>
        <div class="input-group">
          <span class="input-group-addon"><?=$val['level_heading']?></span>
          <?php $link=$HOST."/".md5($val['level_unique'])."/".$sponsor_detail["user_name"]; ?>
          <input type="text" class="form-control" placeholder="<?=$link?>" readonly="readonly">
          <span class="input-group-addon clone_link"  data-toggle="modal" data-target="#copy_alert" data-link='<?=$link?>' title="Copy Link">Copy Link</span>
        </div>
        <?php
        }
        } else { ?><p class='mn text-center'>No Record Found</p><?php } ?>
      </div>
    </div>
  </div>
</form>
<!-- Modal -->
<div id="copy_alert" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-body theme-form">
            <?php 
        $string = "";
        if(isset($_SESSION['agents']['fname']) && isset($_SESSION['agents']['lname'])){
            $string = ucwords($_SESSION['agents']['fname']) . " " .ucwords($_SESSION['agents']['lname'])."'s";
        }else{
            $string = "Your";
        } 
      ?>
      <div class="text-center">
    <h4><?php echo $string ?> Self Application Links</h4>
    <p class="m-b-20">Click "COPY LINK" below to share</p>
  </div>
    <div class="form-group">
       <input type="text"  id="copytext" readonly="readonly" data-clipboard-text="1111" tabindex=""
                   placeholder="display link here" class="form-control" value=""/>
      <textarea id="holdtext" style="display:none;"></textarea>
    </div>
    <div class="text-center">
      <button class="btn btn-action" id="copyingg" data-clipboard-target="#copytext" tabindex="1">Copy Link</button>
      <button class="btn red-link" data-dismiss="modal" tabindex="2">Cancel</button>
    </div>
      </div>
    </div>
  </div>
</div>


<div class="popover popover-custom" style="display: none">
  <div class="popover-content"></div>
</div>
<script type="text/javascript">

    $(function(){
        $(document).on("click",".clone_link",function(e){
            val=$(this).attr("data-link");
            $('#copytext').val(val);
            $('#copy_alert').show();
        });
        var check_disable = $('#copyingg').attr('disabled');
        if(check_disable != 'disabled'){
         var clipboard = new Clipboard('#copyingg');
        // Copy invitation link in clipboard
         clipboard.on('success', function (e) {
                // $('#copy_alert').hide();
                parent.setNotifySuccess("Link Copied!");
                 $("#copy_alert").modal("hide");
            });
        }

		    $('[data-toggle="popover"]').popover({        
          container: 'body',
          placement: 'top',
          trigger :'hover',
          html: true,
          content: function () {
              var agent_id = <?=$sponsor_id?>;
              var level = $(this).data('level');
              $.ajax({
                  url: "agent_level_details_popup.php",
                  type: 'GET',
                  data: {agent_id: agent_id,level: level},
                  success: function(res){
                    var data = res;                    
                    $('.popover-content').html(data);
                    $('.popover-custom').show();
                  }
              });
              }
        }).click(function(e) {
            e.preventDefault();
        })
    });
    $('body').on('click', function (e) {
      $('[data-toggle="popover"]').each(function () {
          if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
              $(this).popover('hide');
              $('.popover-custom').hide();
          }
      });
    });
    function close_popover() {
        $('[data-toggle="popover"]').popover('hide');
        $('.popover-custom').hide();
    }
</script>