<div class="panel panel-default">
   <div class="panel-heading">
      <h4 class="mn">+Term - <span class="fw300">Multiple Products</span></h4>
   </div>
   <div class="panel-body">
      <div class="theme-form">
        <form name="term_form" id="term_form">

        <?php if($active_products){ ?>
          <?php foreach ($active_products as $k => $v) { ?>
            
          
         <div class="form-group height_auto">
            <p>
               <label class="mn label-input"><input type="checkbox" class="chk_term_prd" value="<?=$v['plan_id']?>" name="chk[<?=$v['plan_id']?>]" id=""><?=$v['name'] . " [".$v['website_id']."]"?></label>
            </p>
            <div class="pr term_reason_section_<?=$v['plan_id']?>" style="display: none;">
              <?php $coverage_periods = get_termination_date_selection_options($v['w_id']); ?>
               <select class="form-control" name="termination_date[<?=$v['plan_id']?>]">
                  <option data-hidden="true"></option>
                  <?php if($coverage_periods){
                    foreach ($coverage_periods as $coverage) { ?>
                        <option value="<?=$coverage['value']?>"><?=$coverage['text']?></option>
                  <?php }
                  } ?>
               </select>
               <label>Term Date</label>
            </div>
         <p class="error"><span id="err_product_<?=$v['plan_id']?>"></span></p>
         <p class="error"><span id="err_common"></span></p>
         </div>
         <?php } ?>
         <?php } ?>
         <div class="form-group">
          <select class="form-control" name="reason">
             <option data-hidden="true"></option>
             <?php if($reasons){
                foreach ($reasons as $key => $value) { ?>
                  <option value="<?=$value['name']?>"><?=$value['name']?></option>
              <?php }
             } ?>
          </select>
          <label>Reason</label>
         <p class="error"><span id="err_reason"></span></p>
         </div>
         <div class="text-center">
            <input type="hidden" name="form_submit" value="Y">
            <input type="hidden" name="member_id" value="<?=$member_id?>">
            <button type="submit" name="submit" value="submit" class="btn btn-action">Confirm</button>
            <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close()">Cancel</a>
         </div>
       </form>
      </div>
   </div>
</div>
<script type="text/javascript">
 $(document).ready(function() {
  $(document).off('click',".chk_term_prd");
    $(document).on('click',".chk_term_prd",function(){
        var plan_id = $(this).val();
        if($(this).is(":checked")) {
          $(".term_reason_section_"+plan_id).show();
        } else {
          $(".term_reason_section_"+plan_id).hide();
        }
    });
    
   $('select.form-control').selectpicker({ 
    container: 'body', 
    style:'btn-select',
    noneSelectedText: '',
    dropupAuto:true
  });
  $('select.form-control').selectpicker('refresh');

    $('#term_form').on('submit',function(e){
      e.preventDefault();
      $('#ajax_loader').show();
      $('#ajax_data').hide();
      $('.error span').text('');
      var params = $('#term_form').serialize();
      $.ajax({
        url: $('#term_form').attr('action'),
        type: 'POST',
        data: params,
        dataType: 'JSON',
        success: function(res) {
          if(res.status == 'success'){
            $('#is_ajax').val('');
            $('#ajax_loader').hide();
            parent.$.fn.colorbox.close();
            parent.location.reload();
          }else{
            $('#ajax_loader').hide();
            var is_error = true;
            $.each(res.errors, function(index, error) {
              $('#err_' + index).html(error);
              if (is_error) {
                var offset = $('#err_' + index).offset();
                if (typeof(offset) === "undefined") {
                  console.log("Not found : " + index);
                } else {
                  var offsetTop = offset.top;
                  var totalScroll = offsetTop - 195;
                  $('body,html').animate({
                    scrollTop: totalScroll
                  }, 1200);
                  is_error = false;
                }
              }
            });
          }
          
        }
      });
  });

  });
</script>