<form method="POST" action="" name="frmPayment" id="frmPayment">
  <input type="hidden" name="step" id="step" value="1">
  <input type="hidden" name="customer_id" id="customer_id" value="<?=$customer_id?>">
  <input type="hidden" name="location" id="location" value="<?=$location?>">
  <input type="hidden" name="is_subscription_changed" id="is_subscription_changed" value="Y">
   <div class="panel panel-default reinstate_panel">
      <div class="cust_tab_ui cust_tab_ui_sm">
         <ul class="nav nav-tabs nav-justified nav-noscroll make_payment">
            <li class="active">
               <a data-toggle="tab" href="#product_tab" class="btn_step_heading" data-step="1">
                  <div class="column-step ">
                     <div class="step-number">1</div>
                     <div class="step-title">Choose Product(s)</div>
                  </div>
               </a>
            </li>
            <li>
               <a data-toggle="tab" href="#coverage_tab" class="btn_step_heading" data-step="2">
                  <div class="column-step ">
                     <div class="step-number">2</div>
                     <div class="step-title">Details/Coverage Period(s)</div>
                  </div>
               </a>
            </li>
            <li>
               <a data-toggle="tab" href="#reinstate_tab" class="btn_step_heading" data-step="3">
                  <div class="column-step ">
                     <div class="step-number">3</div>
                     <div class="step-title">Summary/Charge</div>
                  </div>
               </a>
            </li>
         </ul>
      </div>
      <div class="tab-content">
         <!-- Choose Product Code Start -->
            <div id="product_tab" class="tab-pane fade in active">
               <div class="panel-body">
                  <div class="text-center">
                     <h4 class="m-b-30">Make payment for ID:  <span class="text-action"><?=$custInfo['rep_id']?></span></h4>
                     <p class="fs16 m-b-30">Please choose which product(s) the member wants make a payment for.</p>
                     <div class="form-group">
                        <div class="btn-group colors" data-toggle="buttons">
                           <?php 
                           $totalProducts = 0;
                           if(!empty($resPrdPayment)){
                              foreach($resPrdPayment as $key => $subs){ 
                                 $totalProducts++; 
                           ?>
                           <label class="btn btn-info btn-outline m-b-5">
                              <input type="checkbox" name="payment_products[<?=$subs['id']?>]" class="js-switch payment_products" autocomplete="off" ><?=$subs['name'].' '.$subs['product_code']?>
                              <br><span class="text-action"><strong><?=$subs['website_id']?></strong></span>
                           </label>
                           <?php  } } ?>
                        </div>
                            <p class="error"><span id="error_payment_products"></span></p>
                     </div>
                     <?php if($totalProducts > 0) { ?>
                     <p class="fs10"><label id="count_products">0</label> of <?=$totalProducts?> Selected</p>
                     <?php } ?>
                  </div>
               </div>
               <div class="panel-footer text-right">
                  <button type="button" class="btn btn-action btn_submit" data-step="1">Next</button>
                  <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close()">Cancel</a>
               </div>
            </div>
         <!-- Choose Product Code Ends -->
         <!-- Select Covarage Code Start -->
            <div id="coverage_tab" class="tab-pane fade ">
               <div class="panel-body">
                  <div class="row theme-form">
                     <div class="col-sm-6">
                        <div class="form-group">
                          <select class="se_multiple_select" name="coverage_payments[]" id="coverage_payments" multiple="multiple" >
                          </select>
                          <label>Select</label>
                          <p class="error"><span id="error_coverage_payments"></span></p>
                        </div>
                     </div>
                  </div>
                  <h4 class="m-t-0 m-b-20">Details/Coverage Period(s)</h4>
                  <div class="coverage_periods_section"></div>
               </div>
               <div class="panel-footer text-right">
                  <button type="button" class="btn btn-action btn_submit" data-step="2">Next</button>
                  <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close()">Cancel</a>
               </div>
            </div>
         <!-- Select Covarage Code Ends -->
         <!-- Summary Tab Code Start -->
            <div id="reinstate_tab" class="tab-pane fade ">
               <div class="panel-body">
                  <h4 class="m-t-0">Summary</h4>
                  <div id="payment_billing_summary" class="m-b-40">
                  </div>
                  <h4 class="m-t-0">Next Billing Date</h4>
                  <div id="payment_next_billing_summary">
                  </div>
               </div>
               <div class="panel-footer text-right">
                  <button type="button" class="btn btn-action btn_submit" data-step="3">Charge</button>
                  <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close()">Cancel</a>
               </div>
            </div>
         <!-- Summary Tab Code Ends -->
      </div>
   </div>
</form>

<script type="text/javascript">
  $(document).ready(function() {
      $("#coverage_payments").multipleSelect({selectAll: false,
          onClick:function(e){
            $text = e.text;
            $coverage = e.value;
            if(e.selected){
              $(".coverage_"+$coverage).show();
            }else{
              $(".coverage_"+$coverage).hide();
            }
          },
          onTagRemove:function(e){
            $coverage = e.value;
            $(".coverage_"+$coverage).hide();
          }
      });
      $('a[data-toggle="tab"]').off('shown.bs.tab');
      $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
          var new_step = $(e.target).data("step");
          var current_step = $(e.relatedTarget).data("step");
          if(new_step < current_step) {

          } else {
              payment_product($('.btn_submit[data-step="'+current_step+'"]'));    
          }         
      });
      $(document).off('change','.payment_products');
      $(document).on('change','.payment_products',function(e){
         var total_product = 0;
         var selected_products_count = 0;
          $('.payment_products').each(function () {
            if($(this).parents('label').hasClass('active') === true){
               var total_product = $(this).parents('label').length;
               selected_products_count++; 
            }else{
               var total_product = $(this).parents('label').length;
            }
            $("#count_products").text(selected_products_count);
            $("#is_subscription_changed").val("Y");
         });
      });
      $(document).off("click",".btn_submit");
      $(document).on("click",".btn_submit",function(){
        payment_product($(this));
      });
      $(document).off('click','.chk_service_fee');
      $(document).on('click','.chk_service_fee',function(){
         var price = $(this).data('service_fee');
         var coverage = $(this).data('coverage');
         var total_amount = $('.total_amount_'+coverage).text().replace('$',"");
         if(this.checked) {
            total_amount = parseFloat(total_amount) + parseFloat(price);
            $('.total_amount_'+coverage).text('$' + (total_amount).toFixed(2));
         }else{
            total_amount = parseFloat(total_amount) - parseFloat(price);
            $('.total_amount_'+coverage).text('$' + (total_amount).toFixed(2));
         }
      });
      $(document).off('click','.chk_enrollment_fee');
      $(document).on('click','.chk_enrollment_fee',function(){
         var price = $(this).data('enrollment_fee');
         var coverage = $(this).data('coverage');
         var total_amount = $('.total_amount_'+coverage).text().replace('$',"");
         if(this.checked) {
            total_amount = parseFloat(total_amount) + parseFloat(price);
            $('.total_amount_'+coverage).text('$' + (total_amount).toFixed(2));
         }else{
            total_amount = parseFloat(total_amount) - parseFloat(price);
            $('.total_amount_'+coverage).text('$' + (total_amount).toFixed(2));
         }
      });
  });




function payment_product(btn_obj) {
	   parent.disableButton(btn_obj);
	   var params = $('#effective_form').serialize();
     $("#step").val(btn_obj.attr('data-step'));
   //   btn_obj.prop('disabled',true);
     $("#ajax_loader").show();
     $('.error span').html('');

     $.ajax({
         url: '<?= $HOST ?>/ajax_make_payment.php',
         data: $("#frmPayment").serialize(),
         type: 'POST',
         dataType: "json",
         success: function(res) {
            parent.enableButton($(".btn_submit"));
            //  btn_obj.prop('disabled',false);
             $("#ajax_loader").hide();
          
            if(typeof(res.options_html) !== 'undefined') {
             $("#coverage_payments").html(res.options_html);
             $("#coverage_payments").multipleSelect('refresh');
            }

            if(typeof(res.coverage_periods_html) !== 'undefined') {
              $(".coverage_periods_section").html(res.coverage_periods_html);
              $(".payment_date").val(res.payment_post_date);
              $(".payment_date").each(function(index,element){
                  $(this).datepicker({
                       startDate: res.payment_post_date,
                       endDate: $(this).attr('data-max_payment_date'),
                       orientation: "bottom",
                  });
              });
              
              $("#is_subscription_changed").val("N");
              $('.payment_method').selectpicker('refresh');
              $('.billing_profile').selectpicker('destroy');
            }

            common_select();
            fRefresh();
            DropdownAlternative();


             if(typeof(res.payment_billing_summary) !== 'undefined') {
                 $("#payment_billing_summary").html(res.payment_billing_summary);
             }

             if(typeof(res.payment_next_billing_summary) !== 'undefined') {
                 $("#payment_next_billing_summary").html(res.payment_next_billing_summary);
             }

             if (res.status == "success") {
                 $(".make_payment [data-toggle='tab'][data-step='"+(parseFloat($("#step").val())+1)+"']").trigger("click");
                    $('.payment_method').selectpicker('refresh');
                 $('.billing_profile').selectpicker('destroy');
                   common_select();
                   fRefresh();
                   DropdownAlternative();

             } else if(res.status=="error") {
                 var is_error = true;
                 $.each(res.errors, function (index, value) {
                     $('#error_' + index).html(value).show();

                     if (is_error) {
                         $("[href='#"+$('#error_' + index).parents(".tab-pane").attr("id")+"']").trigger("click");
                         is_error = false;

                         var offset = $('#error_' + index).offset();
                         var offsetTop = offset.top;
                         var totalScroll = offsetTop + 350;
                         $('.reinstate-tab-content').animate({
                             scrollTop: totalScroll
                         }, 1200);
                     }
                 });
             
             } else if(res.status=="payment_error" && res.attempt_over == false) {
                 parent.swal({
                     title: "Payment Failed",
                     html: res.payment_error,
                     type: "error",
                     showCancelButton: true,
                     cancelButtonText: "Update Billing",
                     confirmButtonText: "Cancel",
                     allowOutsideClick: false,
                 }).then(function () {
                     parent.$.colorbox.close();
                 }, function (dismiss) {
                     $(".make_payment [data-toggle='tab'][data-step='2']").trigger("click");
                 });
             } else if(res.status=="payment_success") {
                 window.parent.location.reload();
             } else if(res.attempt_over == true) {
                 window.parent.location.reload();
             } else {
                 //
             }
         }
     });
}
function DropdownAlternative(){
   $(function() {
    //$(document).off('shown.bs.select','select.form-control');
    //$(document).on('shown.bs.select','select.form-control',function(e, clickedIndex, isSelected, previousValue) {
    $('select.form-control').on('shown.bs.select', function(e) {
        //$(".se_multiple_select .ms-drop").hide();
        //$(".se_multiple_select").multipleSelect('close');
        $(".se_multiple_select").each(function() {
            $id = $(this).attr('name');
            //console.log($id);
            $("select[name='" + $id + "']").multipleSelect('close');
        });
    });
});
}
</script>