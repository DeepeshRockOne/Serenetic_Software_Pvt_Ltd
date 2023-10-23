<div class="panel panel-default">
   <div class="panel-heading">
      <h4 class="mn">+ Term - <span class="fw300"><?=getname('prd_main',$product_id,'name','id')?></span></h4>
   </div>
   <div class="panel-body">
      <div class="theme-form">
        <form name="effective_form" id="effective_form" action="" method="POST">
            <input type="hidden" name="is_submit" value="Y">
            <input type="hidden" name="ws_id" value="<?=$ws_id?>">
            <input type="hidden" name="customer_id" value="<?=$customer_id?>">
            <input type="hidden" name="product_id" value="<?=$product_id?>">
            <input type="hidden" name="plan_id" value="<?=$plan_id?>">
           <div class="form-group">
              <div class="input-group">
                 <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                 <div class="pr">
                    <?php $coverage_periods = get_termination_date_selection_options($ws_row['id']); ?>
                     <select class="form-control" name="termination_date">
                        <option data-hidden="true"></option>
                        <?php if($coverage_periods){
                          foreach ($coverage_periods as $coverage) { ?>
                              <option value="<?=$coverage['value']?>"><?=$coverage['text']?></option>
                        <?php }
                        } ?>
                     </select>
                    <label>Term Date (MM/DD/YYYY)</label>
                    <p class="error"><span id="err_effective_date"><?=$error?></span></p>
                 </div>
              </div>
           </div>
           <div class="form-group">
            <select class="form-control" name="reason" id="reason">
              <?php
                foreach ($reasons as $value) { ?>
                  <option value="<?=$value['name']?>"><?=$value['name']?></option>
                <?php }
              ?>
            </select>
            <label>Reason</label>
           </div>

         <div class="text-center">
            <button id="submit" class="btn btn-action">Save</button>
            <a href="javascript:void(0);" class="btn red-link">Cancel</a>
         </div>
       </form>
      </div>
   </div>
</div>
<script type="text/javascript">

  $(document).on('click','#submit',function(e){
     e.preventDefault();
     var params = $('#effective_form').serialize();
     $('#ajax_loader').show();
     $.ajax({
       url: 'add_term_date.php',
       type: 'POST',
       data: params,
       dataType: 'JSON',
       success: function(res) {
         $('#ajax_loader').hide();
         if(res.status == 'fail'){
             $('#err_effective_date').text(res.error);
         }else{
          window.parent.$('.termination_date_td').html(res.termination_date);
          window.parent.$('.termination_date_td').show();
          window.parent.$('#btn_cancel_termination').show();
          window.parent.$('.add_term_date').hide();
          parent.setNotifySuccess(res.message);
          parent.$.colorbox.close();
         }
       }
    });
  });

</script>