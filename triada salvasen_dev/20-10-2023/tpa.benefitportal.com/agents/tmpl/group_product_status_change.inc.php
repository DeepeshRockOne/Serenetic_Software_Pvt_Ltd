<div class="panel panel-default panel-block panel-shadowless mn ">
   <div class="panel-body text-center">
      <h4 class="m-t-15 m-b-10" id="text_title">Are you sure you want to change the status for this product to <span class='text-blue'><?=$product_status?></span>?</h4>
      <p class="m-b-15" id="text_body"><?=$text?></p>
      <div class="theme-form">
        <form name="termination_form" id="termination_form" method="POST">
            <input type="hidden" name="location" value="<?=$location?>">
            <input type="hidden" name="group_id" value="<?=$group_id?>">
            <input type="hidden" name="product_ids" value="<?=$product_ids?>">
            <input type="hidden" name="product_status" value="<?=$product_status?>">
            <?php if($product_status == 'Extinct'){?>
              <div class="col-xs-10 col-xs-offset-1">
                <div class="form-group">
                <!-- <div class="input-group">
                  <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                  <div class="pr"> -->
                      <select class="form-control" name="termination_date">
                          <option data-hidden="true" selected="selected" value=""></option>
                          <option value="back_to_initial_start_period">Back to Initial Start Period</option>
                          <option value="end_of_month">End Of Month</option>
                      </select>
                      <label>Term Date</label>
                  <!-- </div>
                  </div>
                  </div> -->
                  <p class="error text-left"><span id="error_termination_date"></span></p>
                </div>
                
              </div>
              <div class="clearfix"></div>
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
     var params = $('#termination_form').serialize();
     $('#ajax_loader').show();
     $.ajax({
        url : 'ajax_group_product_status_change.php',
        data : params,
        type: 'POST',
        dataType: 'json',
        beforeSend:function(){
            $("#ajax_loader").show();
        },
        success: function(res) {
            $("#terminationDropDown .error").hide();
            $("#ajax_loader").hide();
            if(res.status == 'done'){
                parent.setNotifySuccess("Product Status Changed Successfully");
                parent.$.colorbox.close();
                parent.parent.group_product_ajax_submit();
                return false;
            }else if(res.status == 'fail'){
                $.each(res.errors,function(key,value){
                    $('#error_' + key).text(value).show();
                });
            }
        }
    });
  });
  $(document).ready(function(e){
    $(".closeColorbox").on('click',function(){
      parent.$.colorbox.close();
      parent.parent.group_product_ajax_submit();
    });
  });
  
</script>