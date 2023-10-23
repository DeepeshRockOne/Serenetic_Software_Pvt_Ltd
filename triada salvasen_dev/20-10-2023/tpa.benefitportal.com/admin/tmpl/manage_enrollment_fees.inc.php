<div class="white-box manage_fees_wrap"> 
   <div class="panel panel-default panel-block">
      <div class="panel-heading">
         <h4 class="fees_bar_name mn"><?= ($resProduct) ? 'Manage Application Fees' :'New Application Fees ' ?></h4>
      </div>
      <div class="panel-body">
         <form  method="POST" id="manage_fees" enctype="multipart/form-data"  autocomplete="off">
            <input type="hidden" name="fee_id" id="fee_id" value="<?= $fee_id ?>">
            <div class="section_space">
               <h4 class="h4_title m-t-30 m-t-20">Fee Settings</h4>
               <div class="row">
                  <div class="col-sm-6">
                     <div class="form-group">
                        <label>Fee Name <span class="text-light-gray">(Must be unique)</span></label>
                        <input name="product_name" id="product_name" type="text"  class="form-control" value="<?= isset($product_name)?$product_name:""; ?>">
                        <p class="error" id="error_product_name"></p>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="form-group">
                        <label>Fee ID <span class="text-light-gray">(Must be unique)</span></label>
                        <input name="product_code" id="product_code" type="text" class="form-control" value="<?= isset($product_name)?$product_code:'';?>">
                        <p class="error" id="error_product_code"></p>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="form-is-this-fee clearfix">
                     <div class="col-sm-6 col-md-4">
                        <h5 class="h5_title">Is this fee on renewals?</h5>
                        <div class="radio-v">
                           <label>
                           <input  name="is_fee_on_renewal" <?= isset($is_fee_on_renewal) && $is_fee_on_renewal=="N" ? 'checked' : '' ?> type="radio" value="N"> No
                           </label>
                        </div>
                        <div class="radio-v">
                           <label>
                           <input  name="is_fee_on_renewal" <?= isset($is_fee_on_renewal) && $is_fee_on_renewal=="Y" ? 'checked' : '' ?> type="radio" value="Y" > Yes
                           </label>
                        </div>
                        <p class="error" id="error_is_fee_on_renewal"></p>
                        <div class="m-l-25" id="renewal_div" style="<?= isset($is_fee_on_renewal) && $is_fee_on_renewal=="Y" ? '' :'display:none' ?>">
                           <div class="radio-v">
                              <label>
                              <input name="fee_renewal_type" <?= isset($fee_renewal_type) && $fee_renewal_type=="Continuous" ? 'checked' : '' ?> type="radio" value="Continuous" > Continuous
                              </label>
                           </div>
                           <div class="radio-v">
                              <label>
                              <input name="fee_renewal_type" <?= isset($fee_renewal_type) && $fee_renewal_type=="Set Renewals" ? 'checked' : '' ?> type="radio" value="Set Renewals"> Set Numbers of Renewals
                              </label>
                           </div>
                           <p class="error" id="error_fee_renewal_type"></p>
                           <div class="form-group mn" id="fee_renewal_type_div" style="<?= isset($fee_renewal_type) && $fee_renewal_type=="Set Renewals" ? '' :'display:none' ?>">
                              <select class="form-control max-w50 m-l-25" name="fee_renewal_count">
                                 <?php for($i=0;$i<=12;$i++) { ?>
                                    <option value="<?= $i ?>" <?= isset($fee_renewal_count) && $i == $fee_renewal_count ? 'selected=selected' : '' ?>><?= $i ?></option>
                                 <?php } ?>
                              </select>
                              <p class="error" id="error_fee_renewal_count"></p>
                           </div>
                        </div>
                     </div>
                     <div class="col-sm-6 col-md-4">
                        <h5 class="h5_title">Is this fee commissionable to agents?</h5>
                        <div class="radio-v">
                           <label>
                           <input  name="is_fee_on_commissionable" <?= isset($is_fee_on_commissionable) && $is_fee_on_commissionable=="N" ? 'checked' : '' ?> type="radio" value="N"> No
                           </label>
                        </div>
                        <div class="radio-v">
                           <label>
                           <input  name="is_fee_on_commissionable" <?= isset($is_fee_on_commissionable) && $is_fee_on_commissionable=="Y" ? 'checked' : '' ?> type="radio" value="Y"> Yes
                           </label>
                        </div>
                        <p class="error" id="error_is_fee_on_commissionable"></p>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <h4 class="h4_title">Fee Structure</h4>
                  <div class="table-responsive">
                     <table class="<?=$table_class;?>  bg_odd_even_table">
                        <thead>
                           <tr>
                              <th width="20%">Amount of Fee</th>
                              <th width="20%" class="commissionable_div" style="<?= isset($is_fee_on_commissionable) && $is_fee_on_commissionable=="Y" ? '' :'display:none' ?>">Non Commissionable Amount</th>
                              <th width="20%" class="commissionable_div" style="<?= isset($is_fee_on_commissionable) && $is_fee_on_commissionable=="Y" ? '' :'display:none' ?>">Commissionable Amount</th>
                              <th>Product</th>
                           </tr>
                        </thead>
                        <tbody id="fee_structure_body">
                           <tr>
                              <td>
                                 <div class="add_fee_addonwrap">
                                    <input type="text" id="price" name="price" value="<?= isset($price)?$price:'';?>" class="form-control priceControl" placeholder="0.00" onkeypress="return isNumber(event)">
                                    <span class="add_fee_addon">$</span>
                                    <p class="error error_preview" id="error_price"></p>
                                 </div>
                              </td>
                              <td class="commissionable_div" style="<?= isset($is_fee_on_commissionable) && $is_fee_on_commissionable=="Y" ? '' :'display:none' ?>">
                                 <div class="add_fee_addonwrap">
                                    <input type="text" id="non_commissionable_price" name="non_commissionable_price" value="<?= isset($non_commissionable_price)?$non_commissionable_price:''; ?>" class="form-control priceControl" placeholder="0.00" onkeypress="return isNumber(event)">
                                    <span class="add_fee_addon">$</span>
                                    <p class="error error_preview" id="error_non_commissionable_price"></p>
                                 </div>
                              </td>
                              <td class="commissionable_div" style="<?= isset($is_fee_on_commissionable) && $is_fee_on_commissionable=="Y" ? '' :'display:none' ?>">
                                 <div class="add_fee_addonwrap">
                                    <input type="text" id="commissionable_price" name="commissionable_price" value="<?= isset($commissionable_price)? $commissionable_price:''; ?>" class="form-control priceControl" placeholder="0.00" onkeypress="return isNumber(event)" readonly="">
                                    <span class="add_fee_addon">$</span>
                                    <p class="error error_preview" id="error_commissionable_price"></p>
                                 </div>
                              </td>
                              <td>
                                 <div class="add_fee_addonwrap">
                                    <select name="product[]" id="product" multiple="multiple">  
                                        <?php if(!empty($productListRes)){?>
                                          <?php foreach ($productListRes as $key => $product) { ?>
                                              <option value="<?= $product['id'] ?>" <?=((!empty($enrollment_fee_ids_products) && in_array($product['id'],$enrollment_fee_ids_products)) || $product_id == $product['id'])?'selected="selected"':''?>><?= $product['name'] .' ('. $product['product_code'].')' ?></option>  
                                          <?php } ?>
                                        <?php } ?>
                                    </select>
                                    <p class="error error_preview" id="error_product"></p>
                                 </div>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                   <div class="step_btn_wrap m-t-25 text-right">
                      <a href="javascript:void(0);" id="save_fee" class="btn btn-info">Save</a>
                   </div>                  
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<script type="text/javascript">
   $(document).ready(function(){
      $iframe = '<?= $iframe ?>';
      $("#product").multipleSelect({
        filter: true,
				width: '100%',
        onOpen:function(){
           var $table = $('.table-responsive'),
            $menu = $('.ms-parent').find('.ms-drop'),
            tableOffsetHeight = $table.offset().top + $table.height(),
            menuOffsetHeight = $menu.offset().top + $menu.outerHeight(true);

            if (menuOffsetHeight > tableOffsetHeight)
            $table.css("padding-bottom", menuOffsetHeight - tableOffsetHeight + 25)
        },
        onClose:function(){
           $('.table-responsive').css("padding-bottom", 0);
        }
      });
   });
   $(document).on("click","input[name=is_fee_on_renewal]",function(){
      $val=$(this).val();

      $("#renewal_div").hide();
      if($val=="Y"){
         $("#renewal_div").show();
      }
   });
   $(document).on("click","input[name=fee_renewal_type]",function(){
      $val=$(this).val();

      $("#fee_renewal_type_div").hide();
      if($val=="Set Renewals"){
         $("#fee_renewal_type_div").show();
      }
   });
   $(document).on("click","input[name=is_fee_on_commissionable]",function(){
      $val=$(this).val();

      $(".commissionable_div").hide();
      if($val=="Y"){
         $(".commissionable_div").show();
      }
   });
   $(document).on("blur","#price",function(){
      $non_commissionable_price =$("#non_commissionable_price").val();
      $price = $("#price").val();

      $non_commissionable_price = $non_commissionable_price.replace(",", "");
      $price = $price.replace(",", "");

      $non_commissionable_price = parseFloat($non_commissionable_price);
      $price = parseFloat($price);
      $commissionable_price=($price - $non_commissionable_price).toFixed(2);

      if($commissionable_price<0){
        swal({   
            text: "Error: Please Enter Valid Price",   
            }).then(function(){   

        });
        $("#commissionable_price").val('0.00');
      }else{
        $("#commissionable_price").val($commissionable_price);
      }
      $('.priceControl').priceFormat({
              prefix: '',
              suffix: '',
              centsSeparator: '.',
              thousandsSeparator: ',',
              limit: false,
              centsLimit: 2,
      });
   });
   $(document).on("blur","#non_commissionable_price",function(){
      $non_commissionable_price =$("#non_commissionable_price").val();
      $price = $("#price").val();

      $non_commissionable_price = $non_commissionable_price.replace(",", "");
      $price = $price.replace(",", "");

      $non_commissionable_price = parseFloat($non_commissionable_price);
      $price = parseFloat($price);
      $commissionable_price=($price - $non_commissionable_price).toFixed(2);

      if($commissionable_price<0){
        swal({   
            text: "Error: Please Enter Valid Price",   
            }).then(function(){   

        });
        $("#commissionable_price").val('0.00');
      }else{
        $("#commissionable_price").val($commissionable_price);
      }
      $('.priceControl').priceFormat({
              prefix: '',
              suffix: '',
              centsSeparator: '.',
              thousandsSeparator: ',',
              limit: false,
              centsLimit: 2,
      });
   });
   $(document).on('click','#save_fee',function(){
      $("#manage_fees").submit();           
   });

   //******** Form Submit Code start *******************
      $('#manage_fees').ajaxForm({
         beforeSubmit:function(arr, $form, options){
             $("#ajax_loader").show();
                   
         },
         url: 'ajax_enrollment_fees_add.php',
         type: 'POST',
         dataType: 'json',
         success: function (res) {
            $("#ajax_loader").hide();
            $('.error').html('');
            if(res.status=="success"){
               if($iframe==1){
                  window.parent.$.colorbox.close();
               }else{
                  window.location.href="enrollment_fees.php";
               }
            } else if (res.status == 'fail') {
              var is_error = true;
              $.each(res.errors, function (index, error) {
                  $('#error_' + index).html(error);
                  if (is_error) {
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
   //******** Form Submit Code end *******************

   function isNumber(evt) {
       evt = (evt) ? evt : window.event;
       var charCode = (evt.which) ? evt.which : evt.keyCode;
       if (charCode > 31 && charCode != 46 && charCode != 47 && (charCode < 48 || charCode > 57)) {
           return false;
       }
       return true;
   }

   $('.priceControl').priceFormat({
      prefix: '',
      suffix: '',
      centsSeparator: '.',
      thousandsSeparator: ',',
      limit: false,
      centsLimit: 2,
   });
</script>