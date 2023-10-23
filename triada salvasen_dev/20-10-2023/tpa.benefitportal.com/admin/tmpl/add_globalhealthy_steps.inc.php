<div class="panel panel-default panel-block panel-space">
   <div class="panel-heading">
      <div class="panel-title">
         <h4 class="mn">+ Healthy Step</h4>
      </div>
   </div>
   <div class="panel-body">
      <form action="healthy_product.php" name="healthy_steps" id="healthy_steps">
         <input type="hidden" name="product_id" value="<?=checkIsset($resource_res['id'])?>">
         <input type="hidden" name="health_id" value="<?=checkIsset($resource_res['health_id'])?>">
         <input type="hidden" name="is_clone" value="<?=$is_clone?>">
         <div class="theme-form">
            <div class="row">
               <div class="col-md-4 col-sm-6">
                  <div class="form-group ">
                     <input type="text" name="step_name" id="step_name" class="form-control" value="<?=checkIsset($resource_res['name'])!='' && $is_clone=='N' ? checkIsset($resource_res['name']) : '' ?>">
                     <label>Name</label>
                     <p class="error" id="error_name"></p>
                  </div>
               </div>
               <div class="col-md-4 col-sm-6">
                  <div class="form-group ">
                     <input type="text" name="display_id" id="display_id" class="form-control" value="<?= $display_id ?>">
                     <label>Healthy Step ID (Must be Unique)</label>
                     <p class="error" id="error_display_id"></p>
                  </div>
               </div>
               <div class="col-md-4 col-sm-6">
                  <div class="form-group ">
                        <select class="se_multiple_select" name="products[]"  id="products" multiple="multiple" >
                           <?php if(!empty($productRes)){ ?>
                                 <?php foreach ($productRes as $key=> $category) { ?>
                                    <?php if(!empty($category)){ ?>
                              <optgroup label='<?= $key ?>'>
                                 <?php foreach ($category as $pkey => $row) { ?>
                                 <option value="<?= $row['id'] ?>" <?= (!empty($product_ids) && in_array($row['id'], $product_ids)) ? 'selected="selected"' : '' ?> >
                                    <?= $row['name'] .' ('.$row['product_code'].')'?>    
                                 </option>
                                 <?php } ?>
                              </optgroup>
                                 <?php } ?>
                              <?php } ?>
                           <?php } ?>
                        </select>
                        <label>Products</label>
                     <p class="error" id="error_products"></p>
                  </div>
               </div>
               <div class="col-md-4 col-sm-6">
                  <div class="form-group ">
                        <select class="se_multiple_select" name="states[]"  id="states" multiple="multiple" >
                           <?php if(!empty($allStateRes)){ ?>
                              <?php foreach ($allStateRes as $key=> $state) { ?>
                                 <option value="<?= $state['name'] ?>" <?= (!empty($state_names) && in_array($state['name'], $state_names)) ? 'selected="selected"' : '' ?> >
                                    <?= $state['name'] ?>    
                                 </option>
                              <?php } ?>
                           <?php } ?>
                        </select>
                        <label>States</label>
                     <p class="error" id="error_states"></p>
                  </div>
               </div>
               <div class="first_group" style="<?=checkIsset($resource_res['pricing_effective_date']) !='' ? '' : 'display:none' ?>">
                  <div class="col-md-4 col-sm-6">
                     <div class="form-group">
                        <div class="input-group">
                           <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                           <div class="pr">
                              <input name="effective_date" id="effective_date" value="<?=getCustomDate($resource_res['pricing_effective_date']) !='-' ? getCustomDate($resource_res['pricing_effective_date']) : '' ?>"  type="text" class="form-control">
                              <label>Effective Date (MM/DD/YYYY)</label>
                           </div>
                        </div>
                        <p class="error" id="error_effective_date"></p>
                     </div>
                  </div>
                  <div class="col-md-4 col-sm-6">
                     <div class="form-group">
                        <div class="input-group">
                           <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                           <div class="pr">
                              <input name="termination_date" id="termination_date" value="<?=!empty($resource_res['pricing_termination_date']) && getCustomDate($resource_res['pricing_termination_date']) !='-' ? getCustomDate($resource_res['pricing_termination_date']) : '' ?>" type="text" class="form-control">
                              <label>Termination Date (MM/DD/YYYY)</label>
                           </div>
                        </div>
                        <p class="error" id="error_termination_date"></p>
                     </div>
                  </div>
               </div>
            </div>
            <div class="first_group" style="<?=checkIsset($resource_res['price']) !='' ? '' : 'display:none' ?>">
               <p class="fw500">Healthy Step Fee</p>
               <div class="row">
                  <div class="col-md-4">
                     <div class="form-group">
                        <div class="input-group">
                           <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                           <input name="step_fee" id="step_fee" type="text" value="<?=checkIsset($resource_res['price'])?>" class="form-control caculatePricing formatPricing" >
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6 healthy_group" style="<?=checkIsset($resource_res['is_fee_on_commissionable']) =='Y' ? '' : 'display:none' ?>">
                     <div class="row">
                        <div class="col-sm-6">
                           <div class="form-group">
                              <input type="text" name="non_commission_price"  value="<?=checkIsset($resource_res['non_commission_amount'])?>" id="non_commission_price"  class="form-control  formatPricing caculatePricing" >
                              <label>Non-Commissionable Price</label>
                           </div>
                        </div>
                        <div class="col-sm-6">
                           <div class="form-group">
                              <input type="text" name="commission_price" value="<?=checkIsset($resource_res['commission_amount'])?>" id="commission_price" class="form-control  formatPricing" readonly >
                              <label>Commissionable Price</label>
                           </div>
                        </div>
                     </div>
                     <p class="text-action fs12 m-b-15">*Setup the commission structure for this healthy step inside the commission builder.</p>
                  </div>
               </div>
               <div class="m-b-25">
                  <p class="fw500">Is fee commissionable?</p>
                  <label class="m-t-0 m-b-10"><input type="radio" value="Y" <?=checkIsset($resource_res['is_fee_on_commissionable']) == 'Y' ? 'checked="true"' : ''?> name="is_commissionable" class="is_commissionable"> Yes</label><br>
                  <label class="mn"><input type="radio" value="N" <?=checkIsset($resource_res['is_fee_on_commissionable']) == 'N' ? 'checked="true"' : ''?> name="is_commissionable" class="is_commissionable"> No</label>
                  <p class="error" id="error_is_commissionable"></p>
               </div>
               <div class="m-b-25">
                  <p class="fw500">Does this healthy step include member benefits?</p>
                  <label class="m-t-0 m-b-10"><input type="radio" value="Y" <?=checkIsset($resource_res['is_member_benefits']) == 'Y' ? 'checked="true"' : ''?> name="is_member_benefits" class="is_member_benefits"> Yes</label><br>
                  <label class="mn"><input type="radio" value="N" <?=checkIsset($resource_res['is_member_benefits']) == 'N' ? 'checked="true"' : ''?> name="is_member_benefits" class="is_member_benefits"> No</label>
                  <p class="error" id="error_is_member_benefits"></p>
               </div>
            </div>
            <div id="member_portal" style="<?=checkIsset($resource_res['is_member_benefits']) =='Y' ? '' : 'display:none' ?>">
               <div class="m-b-25">
                  <div class="m-b-15 text-right">
                     <label class="mn"><input type="checkbox" value='Y' <?=checkIsset($resource_res['is_member_portal']) == 'Y' ? 'checked' : ''?> name="is_member_portal" id="is_member_portal">Display in Member Portal?</label>
                  </div>
                  <textarea class="summernote" name="description" id="description"><?=checkIsset($resource_res['description'])?></textarea>
                  <p class="error" id="error_description"></p>
               </div>
               <div class="m-b-25">
                  <p ><strong>Benefits will continue for what set period of time? </strong> <i class="text-light-gray">(*Benefits will begin immediately upon successful payment.)</i></p>
                  <label class="m-t-0 m-b-10"><input type="radio" value="Renewals" <?=checkIsset($resource_res['fee_renewal_type']) == 'Renewals' ? 'checked="true"' : ''?>  name="benifit_period" class="benifit_period"> Number of Plan Periods</label><br>
                  <label class="m-t-0 m-b-10 label-input"><input type="radio" value="Continuous" <?=checkIsset($resource_res['fee_renewal_type']) == 'Continuous' ? 'checked="true"' : ''?> name="benifit_period" class="benifit_period"> Remain Active with an Active Plan</label>
                  <p class="error" id="error_benifit_period"></p>
                  <div class="row p-t-20" id="number_of_month" style="<?=checkIsset($resource_res['fee_renewal_type']) == 'Renewals' ? '' : 'display:none'?>">
                     <div class="col-sm-4">
                        <div class="form-group ">
                           <select class="form-control" name="select_month" id="select_month">
                              <option value=""></option>
                              <?php for($i=1;$i<=12;$i++) { ?>
                                 <option value="<?=$i?>" <?=checkIsset($resource_res['fee_renewal_count']) == $i ? 'selected="selected"' : ''?>><?=$i?></option>
                              <?php } ?>
                           </select>
                           <label># of Months</label>
                           <p class="error" id="error_select_month"></p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="text-center clearfix m-b-30">
            <a href="javascript:void(0);" class="btn btn-action" id="save">Save</a>
            <a href="healthy_steps.php" class="btn red-link">Cancel</a>
         </div>
      </form>
   </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
   initCKEditor('description');
   $("#products,#states").multipleSelect({
    width: '100%',
    selectAll: true,
  });
  formatPricing();
  $("#effective_date").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true,
        startDate:new Date()
    }).on("changeDate", function (selected) {
      var minDate = new Date(selected.date.valueOf());
      $('#termination_date').datepicker('setStartDate', minDate);
    });
    
    $("#termination_date").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true
    });
 
});

$(document).off('click','#save');
$(document).on('click','#save',function(e){
   $('.error').html("");
   $("#ajax_loader").show();
   $("#description").val(CKEDITOR.instances.description.getData());
   var $data = $("#healthy_steps").serialize();
   $.ajax({
      url : "ajax_add_globalhealthy_steps.php",
      data : $data,
      type : "POST",
      dataType : 'json',
      success : function(res){
         $("#ajax_loader").hide();

         if(res.status == 'success'){
            // setNotifySuccess(res.message);
            window.location = res.redirect_url;
         }else if(res.status == 'fail'){
            $('.error').show();
            var is_error = true;
            $.each(res.errors,function(index,error){
               $('#error_' + index).html(error);
               if(is_error){
                  var offset = $('#error_' + index).offset();
                  if(typeof(offset) === "undefined"){
                      console.log("Not found : "+index);
                  }else{
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

$(document).off('change','#products');
$(document).on('change','#products',function(e){
   var $val = $(this).val();
   if($val !== undefined && $val !=='' &&  $val !==null ){
      $(".first_group").show();
   }
});

$(document).off('change','.is_commissionable');
$(document).on('change','.is_commissionable',function(e){
   var $val = $(this).val();
   if($val !== undefined && $val ==='Y' &&  $val !==null ){
      $(".healthy_group").show();
      $("#non_commission_price").val('');
      $("#commission_price").val('');
      fRefresh();
   }else{
      $(".healthy_group").hide();
   }
});

$(document).off('change','.is_member_benefits');
$(document).on('change','.is_member_benefits',function(e){
   var $val = $(this).val();
   if($val !== undefined && $val ==='Y' &&  $val !==null ){
      $("#member_portal").show();
   }else{
      $("#member_portal").hide();
      $("#number_of_month").css("display","none");
      $(".benifit_period").prop("checked",false);
      $('input[type="radio"]').uniform();
   }
});

$(document).off('change','.benifit_period');
$(document).on('change','.benifit_period',function(e){
   var $val = $(this).val();
   if($val !== undefined && $val ==='Renewals' &&  $val !==null ){
      $("#number_of_month").show();
   }else{
      $("#number_of_month").hide();
   }
});

$(document).on("blur",".caculatePricing",function(){
         $Retail = $("#step_fee").val();
         $NonCommissionable = $("#non_commission_price").val();

         $NonCommissionable = parseFloat($NonCommissionable);
         $Retail = parseFloat($Retail);
         $Commissionable=($Retail - $NonCommissionable).toFixed(2);

         if($Commissionable<0){
            $Commissionable = '0.00';
         }
         $("#commission_price").val($Commissionable);
         formatPricing();
         fRefresh();
   });

formatPricing = function(){
$("#healthy_steps .formatPricing").priceFormat({
      prefix: '',
      suffix: '',
      centsSeparator: '.',
      thousandsSeparator: ',',
      limit: false,
      centsLimit: 2,
});
}
</script>