<div class="panel panel-default">
   <div class="panel-heading">
      <h4 class="mn">Regenerate Order - <span class="fw300"><?=$order_display_id?></span></h4>
   </div>
   <form action="" name="regenerate_frm" id="regenerate_frm">
      <input type="hidden" name="location_from" id="location_from" value="<?=$location_from?>">
      <input type="hidden" name="customer_id" id="customer_id" value="<?=md5($customer_id)?>">
      <input type="hidden" name="order_id" id="order_id" value="<?=$orderId?>">
      <input type="hidden" name="order_display_id" id="order_display_id" value="<?=$order_display_id?>">
      <div class="panel-body">
         <h4 class="fs16 mn"><?=$order_row['name']?></h4>
         <p class="m-b-20"><a href="javascript:void(0);" class="red-link pn fw500"><?=$order_row['rep_id']?></a></p>
         <div id="single_coverage">
            <div class="table-responsive">
               <table class="<?=$table_class?> table-small m-b-20">
                  <thead >
                     <tr class="bg_light_primary">
                        <th>
                           <!-- <div class="checkbox checkbox-custom mn">
                              <input type="checkbox" class="js-switch mn" name="selecte_all_product" id="selecte_all_product" value=""><label for="selecte_all_product"></label>
                           </div>  -->
                           No
                        </th>
                        <th>Product</th>
                        <th>Cost</th>
                        <th class="text-center" width="25%">Sale Type</th>
                        <th class="text-center" width="25%">Plan Period</th>
                        <th class="text-right">Total</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php if(!empty($od_res)){
                           $service_fee = 0;
                           $grand_total = 0;
                           $service_fee_id = 0;
                           $service_fee_app_id = 0;
                        foreach($od_res as $key =>$val){
                           $grand_total += $val['unit_price'];
                           ?>
                              <input type="hidden" name="product_plan_id[<?=$val['product_id']?>]" value="<?=checkIsset($val['plan_id'])?>">
                              <input type="hidden" name="member_payment_type[<?=$val['product_id']?>]" value="<?=checkIsset($val['payment_type_subscription'])?>">
                              <input type="hidden" name="start_coverage_date[<?=$val['product_id']?>]" value="<?=checkIsset($coverage_dates[$val['product_id']])?>">
                              <input type="hidden" name="fee_applied_for_product[<?=$val['product_id']?>]" value="<?=$val['fee_applied_for_product']?>">       
                           <?php
                           if($val['product_type'] == 'ServiceFee' && $val['type'] == 'Fees' ){
                              $service_fee += $val['unit_price'];
                              $service_fee_id = $val['product_id'];
                              $service_fee_app_id = $val['fee_applied_for_product'];
                              continue;
                           }
                           $allow_to_select = true;
                           if(in_array($val['fee_applied_for_product'] ,$product_list_normal)){
                              $allow_to_select = false;
                           }
                           ?>
                        <tr>
                           <td>
                              <div class="<?= $allow_to_select == false ? '' : 'checkbox checkbox-custom mn' ?>">
                                 <input type="checkbox" class="js-switch mn selected_product" name="selected_product[<?=$val['product_id']?>]" data-id="<?=$val['product_id']?>" data-price="<?=$val['unit_price']?>" id="selected_product_<?=$val['product_id']?>" data-fee_applied_for="<?=$val['fee_applied_for_product']?>" value="<?=$val['product_id']?>"
                                 <?= $allow_to_select == false ? 'onclick="return false;"' : '' ?>
                                 <?= $allow_to_select == false ? 'style="display:none"' : '' ?>
                                 >
                                 <label for="selected_product_<?=$val['product_id']?>"></label>
                              </div>
                           </td>
                           <td><?=$val['name']?></td>
                           <td class="text-center"><?=displayAmount($val['unit_price'])?></td>
                           <td class="text-center" id="">
                              <label class="<?=$val['type'] !='Fees' ? 'prd_label' : ''?>" id="prd_label_<?=$val['type'] !='Fees' && $is_renewal  == 'N'  ? $val['product_id'] : ''?>"><?=$is_renewal  == 'Y' ? 'Renewal' : 'New Business'?></label>
                              <?php
                              if($is_renewal == 'N'){
                                 if($ask_for_effactive_date) {

                                 $start_coverage_date = checkIsset($coverage_dates[$val['product_id']]);
                                 $member_payment_type = checkIsset($val['payment_type_subscription']);
                                 if($val['type'] == 'Fees'){ 
                                    $start_coverage_date = checkIsset($coverage_dates[$val['fee_applied_for_product']]);
                                 }
                                 $today = date('Y-m-d');
                                 $dateObj = get_product_effective_detail($val['product_id'], $today);
                                 if(strtotime(date("Y-m-d", strtotime("+1 days",strtotime(date('Y-m-d'))))) < strtotime(date('Y-m-d',strtotime($dateObj->default_effective_from)))){
                                    $effective_start_date = date('m/d/Y',strtotime($dateObj->default_effective_from));
                                  } else {
                                    if($dateObj->calender_type == 'monthly'){
                                      $effective_start_date = date("Y-m-d", strtotime("+1 days",strtotime($dateObj->default_effective_from)));
                                      $default_effective_from = new DateTime(date($effective_start_date));
                                      $default_effective_from->modify('first day of next month');
                                      $effective_start_date = $default_effective_from->format('Y-m-d');
                                    } else {
                                      $effective_start_date = date("Y-m-d", strtotime($dateObj->default_effective_from));
                                    }
                                  }

                                 if($val['type'] != 'Fees' ){
                                 ?>
                                 <div class="theme-form pr coverage_date_div" id="ord_regenerate_product_div_<?=$val['product_id']?>" style="display:none">
                                    <div class="col-sm-12">
                                       <div class="form-group height_auto mn">
                                       <input 
                                       type="text" 
                                       id="ord_regenerate_product_<?=$val['product_id']?>"
                                       name="ord_regenerate_product[<?=$val['product_id']?>]"
                                       class="form-control coverage_date_input datepicker has-value"
                                       data-effective_date="<?= $val['eligibility_date'] ?>"
                                       data-start_date ="<?= date('m/d/Y',strtotime($effective_start_date));?>" 
                                       data-start_date_sel ="<?= date('Y,m,d',strtotime($effective_start_date));?>" 
                                       data-effective_from="<?= $dateObj->effective_from;?>"
                                       data-calender_type="<?= $dateObj->calender_type;?>"
                                       data-id = "<?=$val['product_id']?>"
                                       data-member_payment_type = "<?= checkIsset($val['payment_type_subscription']) ?>"
                                       >
                                       <label for="ord_regenerate_product_<?=$val['product_id']?>">Effective Date</label>
                                       <p class="error error_ord_regenerate_product_<?=$val['product_id']?>"></p>
                                    </div>
                                 </div> </div>
                              <?php  } } ?>
                              <input type="hidden" name="product_type[<?=$val['product_id']?>]" value="<?=$val['type']?>">
                           </td>
                           <td class="text-center">
                              <?php  if($val['type'] != 'Fees' ){ ?>
                                 <label id="start_coverage_date_<?=$val['product_id']?>" class="start_coverage_date"></label> 
                                 - 
                                 <label id="end_coverage_date_<?=$val['product_id']?>" class="end_coverage_date"></label>
                              <?php }else echo '-'; ?>
                           </td>
                           <td class="text-right fw700"><?=displayAmount($val['unit_price'])?></td>
                        </tr>
                        <?php }else{
                           $end_cov_date = get_end_coverage_period($order_row['id'],$val['plan_id']); ?>
                           <input type="hidden" name="product_type[<?=$val['product_id']?>]" value="<?=$val['type']?>">
                           <?php if($val['type'] != 'Fees' ){
                           ?> 
                              <input 
                                 type="text" 
                                 id="ord_regenerate_product_<?=$val['product_id']?>"
                                 name="ord_regenerate_product[<?=$val['product_id']?>]"
                                 value=""
                                 data-value="<?=date('m/d/Y',strtotime($end_cov_date));?>"
                                 class="form-control coverage_date_input btn btn-white" readonly="readonly">
                        <?php }}  } } ?>
                  </tbody>
               </table>
              
               <table class="table table-small m-b-20 br-a">
                  <tbody>
                     <tr>
                           <td class="bg_light_gray">
                           <?php if($service_fee > 0) { 
                              $allow_to_select = true;
                              if(in_array($service_fee_app_id ,$product_list_normal)){
                                 $allow_to_select = false;
                              }
                              ?>
                           <div class="<?= $allow_to_select == false ? '' : 'checkbox checkbox-custom mn' ?>">
                              <input type="checkbox" class="js-switch mn selected_product" data-price="<?=$service_fee?>" id="service_fee_coverage" data-id="<?=$service_fee_id?>" name="selected_product[<?=$service_fee_id?>]" data-fee_applied_for="<?=$service_fee_app_id?>" value="<?=$service_fee_id?>" <?= $allow_to_select == false ? 'onclick="return false;"' : '' ?>  <?= $allow_to_select == false ? 'style="display:none"' : '' ?>>
                              <label for="service_fee_coverage" class="m-l-30 p-l-30">Service Fee (<?=displayAmount($service_fee)?>)</label>
                              <!-- <label for="service_fee_coverage" style="margin-left: 172px;"><?=displayAmount($service_fee)?></label> -->
                              
                           </div>
                           <input type="hidden" name="product_type[<?=$service_fee_id?>]" value="Fees">
                           <?php } ?>
                        </td>
                        <td class="text-right bg_light_gray"><span class="fw700">Total :</span> &nbsp;&nbsp; <label id="grand_total" data-price="0"><?=displayAmount(0)?></label>
                        <input type="hidden" name="grand_total_main" id="grand_total_main" value="<?=$grand_total?>">
                        </td>
                     </tr>
                  </tbody>
               </table>
               <p class="error error_selected_product"></p>
            </div>
            <div class="row theme-form" id="future_payment_section" style="display:none">
               <div class="col-sm-6">
                  <div class="form-group">
                     <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <div class="pr">
                           <input id="post_payment_date" type="text" class="form-control" name="post_payment_date">
                           <label>Payment Date (MM/DD/YYYY)</label>
                        </div>
                     </div>
                     <p class="error error_post_payment_date"></p>
                  </div>
               </div>
               <div class="col-sm-6">
                  <div class="form-group" id="payment_method_div">
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="panel-footer text-center">
         <a href="javascript:void(0);" class="btn btn-action" id="regenerate_order_btn">Regenerate</a>
         <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close();">Cancel</a>
      </div>
   </form>
</div>
<script type="text/javascript">
$(document).ready(function(e){
   ajax_get_payment_method();
   <?php if($is_renewal == 'N') {
     if(!$ask_for_effactive_date) {?>
      $("#post_payment_date").datepicker({
         startDate: '<?=$from_date?>',
         endDate: '<?=$to_date?>',
         autoclose: true
      });
      $("#future_payment_section").show();
   <?php }else{ ?>
      $(".coverage_date_input").each(function() {
          var this_obj  = $(this);
          var product_id = this_obj.attr('data-id');
          var member_payment_type = this_obj.attr('data-member_payment_type');
          var start_date = this_obj.attr('data-start_date');
          var $endDate = "<?=date("m/d/Y", strtotime("+75 days"))?>";
          if(this_obj.attr("data-calender_type") == 'monthly'){
            this_obj.datepicker({
              startDate: moment(this_obj.attr('data-start_date')).format("MM/DD/Y"),
              endDate: $endDate,
              startView: 1,
              minViewMode: 1
            }).on('changeDate', function(ev){
               ajax_get_coverage_period(product_id,member_payment_type,this_obj.val());
               setPostDate();
            });
            this_obj.val(this_obj.attr('data-start_date')).change();
            ajax_get_coverage_period(product_id,member_payment_type,start_date)
            // setPostDate();
          } else {
            this_obj.datepicker({
              startDate: moment(this_obj.attr('data-start_date')).format("MM/DD/Y"),
              endDate: $endDate,
              startView: 0,
              minViewMode: 0
            }).on('changeDate', function(ev){
               ajax_get_coverage_period(product_id,member_payment_type,this_obj.val())
               setPostDate();
            });
            this_obj.val(this_obj.attr('data-start_date')).change();
            ajax_get_coverage_period(product_id,member_payment_type,start_date);
          }
      });
   <?php } } ?>

});

$(document).off('click','#regenerate_order_btn');
$(document).on('click','#regenerate_order_btn',function() {
   $.ajax({
      url:"ajax_regenerate_order.php",
      data: $("#regenerate_frm").serialize(),
      type: 'POST',
      dataType: 'JSON',
      beforeSend :function(){
         $("#regenerate_frm .error").html('');
         $('#ajax_loader').show();
      },
      success: function(res) {
      $('#ajax_loader').hide();
      var location_from = $("#location_from").val();
      if (res.status == 'success') {
         parent.$.colorbox.close();
         parent.setNotifySuccess("Order Regenerated Successfully!",true);
         if(location_from === undefined || location_from === ''){
            parent.window.location.reload();
         }else{
            parent.get_orders_history();
            parent.get_transactions_history();
         }
      } else if (res.status == 'fail') {
         $.each(res.errors, function(key, value) {
            $('.error_' + key).parent("p.error").show();
            $('.error_' + key).html(value).show();
         });
      }else if(res.status === 'not_found'){
         parent.$.colorbox.close();
         parent.setNotifyError("Order Not found!",true);
         if(location_from === undefined || location_from === ''){
            parent.window.location.reload();
         }else{
            parent.get_orders_history();
            parent.get_transactions_history();
         }
      }
      return false;
      }
   });
});

$(document).off('change click','#payment_method');
$(document).on('change click','#payment_method',function(e){
   if($(this).val() === 'add_new_payment_method'){
      $.colorbox({
         href :'<?=$HOST?>/add_billing_profile.php?location=Admin&id=<?=md5($customer_id)?>&action=Add' ,
         iframe: true,
         width: '768px', 
         height: '675px',
         onClosed : function(e){
            ajax_get_payment_method();
         }
      });
   }
});

function ajax_get_payment_method(){
   $.ajax({
      url : "regenerate_order.php",
      data : {get_payent_method:1,customer_id:'<?=md5($customer_id)?>'},
      type:"POST",
      dataType : "json",
      beforeSend :function(){
         $("#ajax_loader").show();
         $("#payment_method_div").html("");
      },
      success : function(res){
         $("#ajax_loader").hide();
         $("#payment_method_div").html(res.bill_desc);
         common_select();
      }
   });
}

function ajax_get_coverage_period(product_id,member_payment_type,start_date){
   $.ajax({
      url : "regenerate_order.php",
      data : {get_coverage_period:1,customer_id:'<?=md5($customer_id)?>',product_id:product_id,member_payment_type:member_payment_type,start_date:start_date},
      type:"POST",
      dataType : "json",
      beforeSend :function(){
         $("#ajax_loader").show();
         $("#start_coverage_date_"+product_id).html("");
         $("#end_coverage_date_"+product_id).html("");
      },
      success : function(res){
         $("#ajax_loader").hide();
         $("#start_coverage_date_"+product_id).html(res.start_coverage_date);
         $("#end_coverage_date_"+product_id).html(res.end_coverage_date);
         common_select();
      }
   });
}
// $(document).off('click','#selecte_all_product');
// $(document).on('click','#selecte_all_product',function(e){
//     if($(this).is(":checked")){
//         $(".coverage_date_div").show();
//         $(".prd_label").hide();
//         $(".selected_product").prop('checked',true);
//         $total = parseFloat($("#grand_total_main").val()).toFixed(2);
//         $("#grand_total").text('$'+$total);
//         $("#grand_total").attr('data-price',$total);
//     }else{
//        $(".coverage_date_div").hide();
//        $(".prd_label").show();
//         $(".selected_product").prop('checked',false);
//         $("#grand_total").text('$0');
//         $("#grand_total").attr('data-price','0');
//     }
// });

$(document).off('change',".selected_product");
$(document).on('change',".selected_product",function(e){
   e.preventDefault();
   var $id = $(this).attr('data-id');
   if($('.selected_product:checked').length == $('.selected_product').length){
      $('#selecte_all_product').prop('checked',true);       
   }else{
      $('#selecte_all_product').prop('checked',false);
   }
   var $grand_total = parseFloat($("#grand_total").attr('data-price'));
   var price = parseFloat($(this).attr('data-price'));
   if($(this).is(":checked") === true){
      if($('#ord_regenerate_product_'+$id).prop('readonly')){
         $('#ord_regenerate_product_'+$id).val($('#ord_regenerate_product_'+$id).attr('data-value'));
      }else{

         var member_payment_type = $('#ord_regenerate_product_'+$id).attr('data-member_payment_type');
         var start_date = $('#ord_regenerate_product_'+$id).attr('data-start_date');
         $('#ord_regenerate_product_'+$id).val(start_date);

         ajax_get_coverage_period($id,member_payment_type,start_date);
      }
      $("#ord_regenerate_product_div_"+$id).show();
      $("#prd_label_"+$id).hide();
      $grand_total = $grand_total+price;
      $("#grand_total").attr('data-price',$grand_total);
      $("#grand_total").text('$'+parseFloat($grand_total).toFixed(2));

      $('input.selected_product').each(function(index,element) {
         var $fee_applied_for = $(this).attr("data-fee_applied_for");
         if ($fee_applied_for === $id) {

            var price = parseFloat($(this).attr('data-price'));

            $grand_total = $grand_total+price;
            $("#grand_total").attr('data-price',$grand_total);
            $("#grand_total").text('$'+parseFloat($grand_total).toFixed(2));
            
               $(this).prop('checked', true);
               $.uniform.update();
         }
      });  

   }else if($(this).is(":checked") === false){
      $("#ord_regenerate_product_" + $id).val('');
      $("#ord_regenerate_product_div_"+$id).hide();
      $("#prd_label_"+$id).show();
      $grand_total = $grand_total-price;
      $("#grand_total").attr('data-price',$grand_total);
      $("#grand_total").text('$'+parseFloat($grand_total).toFixed(2));

      $('input.selected_product').each(function(index,element) {
         var $fee_applied_for = $(this).attr("data-fee_applied_for");
         if ($fee_applied_for === $id) {
            var price = parseFloat($(this).attr('data-price'));
            $grand_total = $grand_total-price;
            $("#grand_total").attr('data-price',$grand_total);
            $("#grand_total").text('$'+parseFloat($grand_total).toFixed(2));
            $(this).prop('checked', false);
            $.uniform.update();
         }
      });  
   }
   setPostDate();
});

setPostDate = function(){
	var effective_dates = [];
	var cnt = 0;

   $("input.coverage_date_input").each(function(index,element){
     
      $product_id = $(this).attr('id').replace('ord_regenerate_product_','');
      if($(this).val() != "" && $("#selected_product_"+$product_id).is(":checked") === true) {
         effective_dates[cnt] = $(this).val();
         cnt++;
        if(!$("#future_payment_section").is(':visible')){
          $("#future_payment_section").show();
        }
      } 
    });
   if(effective_dates.length > 0) {
      lowest_effective_date = '';
      $.each(effective_dates, function(index, element){
        if(element != ''){
          if(lowest_effective_date != ''){
            if(new Date(element) <= new Date(lowest_effective_date)) {
              lowest_effective_date = element;
            }
          } else {
            lowest_effective_date = element;
          }
        }
      });

      try{ $('#post_payment_date').data('datepicker').remove(); }catch(e){}
      $("#post_payment_date").datepicker({
        startDate: "<?=date("m/d/Y")?>",
        endDate: moment(lowest_effective_date).add(-1,'d').format("MM/DD/YYYY"),
        orientation: "bottom",enableOnReadonly: true,
        autoclose: true
      });
    }
}
</script>