<div class="panel panel-default panel-block panel-shadowless mn ">
   <div class="panel-body text-center">
      <h4 class="m-t-15 m-b-10" id="text_title">Are you sure you want to change the status for this Group to <span class='text-blue'><?=$disp_status?></span>?</h4>
      <p class="m-b-15" id="text_body"><?=$text?></p>
      <div class="theme-form">
        <form name="statusForm" id="statusForm" method="POST">
            <input type="hidden" name="location" value="<?=$location?>">
            <input type="hidden" name="id" value="<?=$group_id?>">
            <input type="hidden" name="status" value="<?=$new_status?>">
            <input type="hidden" name="old_status" value="<?=$old_status?>">
            <?php if($new_status == 'Terminated'){ ?>
              <div class="col-xs-12">
                <div class="form-group">
                      <select class="form-control" name="termination_date">
                          <option data-hidden="true" selected="selected" value=""></option>
                          <option value="back_to_initial_start_period">Back to Initial Start Period</option>
                          <option value="end_of_month">End Of Month</option>
                      </select>
                      <label>Term Date</label>
                  <p class="error text-left" id="error_termination_date"></p>
                </div>
              </div>
              <div class="clearfix"></div>
            <?php } ?>
            <?php if(in_array($new_status,array('Terminated','Suspended'))){?>
                <div class="col-xs-12">
                    <div class="form-group height_auto m-b-15">
                        <textarea id="reason" name="reason" class="form-control" value="" rows="3" placeholder="Enter Reason for change" /></textarea>
                        <p class="error text-left" id="error_reason"></p>
                    </div>
                </div>
            <?php } ?>
         <div class="text-center">
            <button id="submit" class="btn btn-primary">Confirm</button> &nbsp;&nbsp;&nbsp;&nbsp;
            <a href="javascript:void(0);" class="btn red-link closeColorbox">Cancel</a>
         </div>
       </form>
      </div>
   </div>
</div>
<script type="text/javascript">
    $(document).off('click','#submit');
    $(document).on('click','#submit',function(e){
     e.preventDefault();
     var params = $('#statusForm').serialize();
     $('#ajax_loader').show();
     $.ajax({
        url : 'ajax_change_group_status.php',
        data : params,
        type: 'POST',
        dataType: 'json',
        beforeSend:function(){
            $("#ajax_loader").show();
        },
        success: function(res) {
            $(".error").hide();
            $("#ajax_loader").hide();
            if(res.status == 'success'){
                parent.setNotifySuccess(res.msg);
                <?php if($from == 'detail_page'){ ?>
                    parent.window.location.reload();
                <?php }else{ ?>
                    parent.refreshGroupStatus("<?=$group_id?>","<?=$new_status?>");
                <?php } ?>
                parent.$.colorbox.close();
                return false;
            }else if(res.status == 'fail'){
                $.each(res.error,function(key,value){
                    $('#error_' + key).text(value).show();
                });
            }
        }
    });
  });
  $(document).ready(function(e){
    $(".closeColorbox").on('click',function(){
      <?php if($from == 'listing_page'){ ?>
        parent.refreshGroupStatus("<?=$group_id?>","<?=$old_status?>");
      <?php } ?>
      parent.$.colorbox.close();
    });
  });
  
</script>