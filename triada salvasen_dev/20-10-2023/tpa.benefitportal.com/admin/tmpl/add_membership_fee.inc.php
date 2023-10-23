<div class="panel panel-default panel-block add_fee_wrap Font_Roboto">
  <div class="panel-heading">
  <div class="panel-title">
      <p class="fs18"><strong class="fw500"><?= ($fee_id > 0 && $is_clone == 'N') ? 'Edit Fee' : 'Add Fee' ?></strong></p>
    </div>
  </div>
  <div class="panel-body ">
    <form name="membership_fee_frm" method="Post" id="membership_fee_frm" class="theme-form ">
     <p class="fs14"><strong class="fw500">Fee Information</strong></p>
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
              <input type="text" id="fee_name" name="fee_name" class="form-control <?= (!empty($fee_name)) ? 'has-value': '' ?>" value="<?= (!empty($fee_name)) ? $fee_name : '' ?>"/>
              <label>Fee Name</label>
              <p class="error" id="error_fee_name"></p>
          </div>
        </div>
          
        <div class="col-sm-4">
          <div class="form-group">
              <input type="text" id="fee_display_id" name="display_fee_id" class="form-control <?= (!empty($display_fee_id)) ? 'has-value': '' ?>" value="<?= (!empty($display_fee_id)) ? $display_fee_id : '' ?>"/>
              <label>Fee ID (Must be unique)</label>
              <p class="error" id="error_fee_display_id"></p>
          </div>
        </div>
          
        <div class="col-sm-4">
            <div class="form-group">
                <select class="form-control has-value" name="fee_type" id="fee_type">
                    <option value="Charged" <?= (!empty($fee_type) && $fee_type == 'Charged') ? 'selected' : '' ?>>Charged</option>
                    <option value="Admin Display" <?= (!empty($fee_type) && $fee_type == 'Admin Display') ? 'selected' : '' ?>>Display Only</option>
                </select>
                <label>Fee Type</label>
                <p class="error" id="error_fee_type"></p>
            </div>
        </div>  
        <div class="col-sm-6">
          <div class="form-group">
                  <select name="products[]" id="products" multiple="multiple" class="se_multiple_select" >        
                    <?php foreach ($company_arr as $key=>$company) { ?>
                          <optgroup label='<?= $key ?>'>
                            <?php    foreach ($company as $pkey =>$row) { ?>
                                <option value="<?= $row['id'] ?>" <?= (!empty($product_ids) && in_array($row['id'], $product_ids)) ? 'selected' : '' ?> ><?= $row['name'] .' ('.$row['product_code'].')'?></option>
                            <?php } ?>
                          </optgroup>
                  <?php } ?>
                 </select>
                  <label>Products</label>
                 <p class="error" id="error_products"></p>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <div class="input-group"> 
                  <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i> </span>
                  <div class="pr">
                  <input type="text" class="form-control" name="effective_date" id="effective_date"  value="<?= (!empty($effective_date)) ? $effective_date : '' ?>"/>
                  <label>Effective Date</label>
                </div>
                </div>
                <p class="error" id="error_effective_date"></p>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <div class="input-group"> 
                  <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i> </span>
                  <div class="pr">
                  <input type="text" class="form-control" name="termination_date" id="termination_date"  value="<?= (!empty($termination_date)) ? $termination_date : '' ?>"/>
                  <label>Termination Date</label>
                </div>
                </div>
                <p class="error" id="error_termination_date"></p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <p class="fs14 m-t-20"><strong class="fw500">Fee Structure</strong></p>
      <div class="row">
          <div class="table-responsive">
            <table cellpadding="0" cellspacing="0" width="100%" class="table table-striped">
              <tbody>
                <tr>
                  <td colspan="2">Is fee on new business?</td>
                  <td><label class="mn">
                      <input type="radio" name="initial_purchase" id="initial_purchase_N" value="N" class="initial_purchase" <?= (!empty($initial_purchase) && $initial_purchase == 'N') ? 'checked' : '' ?> />No</label></td>
                  <td><label class="mn">
                      <input type="radio" name="initial_purchase" id="initial_purchase_Y" value="Y" class="initial_purchase" <?= (!empty($initial_purchase) && $initial_purchase == 'Y') ? 'checked' : '' ?> />Yes</label></td>
                </tr>
                <tr class="lightblue_tr_bg">
                  <td colspan="2">Is fee on renewals?</td>
                  <td><label class="mn">
                          <input type="radio" name="is_fee_on_renewal" id="is_fee_on_renewal_N" value="N" class="is_fee_on_renewal" <?= (!empty($is_fee_on_renewal) && $is_fee_on_renewal == 'N') ? 'checked' : '' ?> />No</label></td>
                  <td><label class="mn">
                      <input type="radio" name="is_fee_on_renewal" id="is_fee_on_renewal_Y" value="Y" class="is_fee_on_renewal" <?= (!empty($is_fee_on_renewal) && $is_fee_on_renewal == 'Y') ? 'checked' : '' ?>  />Yes</label></td>
                </tr>
                <tr id="renewal_numbers_row" style="display:<?= (!empty($is_fee_on_renewal) && $is_fee_on_renewal == 'Y') ? '' : 'none' ?>">
                  <td><span class="p-l-40">Set the number of renewals: </span></td>
                  <td colspan="4">
                    <div class="form-inline text-right">
                          <div class="form-group height_auto mn">
                            <label class="mn p-r-15">
                              <input type="radio" name="fee_renewal_type" id="fee_renewal_type_Continuous" value="Continuous" class="fee_renewal_type" <?= (!empty($fee_renewal_type) && $fee_renewal_type == 'Continuous') ? 'checked' : '' ?>/>Continuous</label>
                            <label class="mn p-r-15">
                              <input type="radio" name="fee_renewal_type" id="fee_renewal_type_Renewals" value="Renewals" class="fee_renewal_type" <?= (!empty($fee_renewal_type) && $fee_renewal_type == 'Renewals') ? 'checked' : '' ?> />Renewals:</label>

                          </div>
                          <div id="fee_renewal_type_div" class="form-group  height_auto mn">
                              <select class="form-control max-w75" name="fee_renewal_count" id="fee_renewal_count" <?= (!empty($fee_renewal_type) && $fee_renewal_type == 'Renewals') ? '' : 'disabled' ?>>
                                  <?php for($i=0;$i<=12;$i++) { ?>
                                     <option value="<?= $i ?>" <?= (!empty($fee_renewal_count) && $i == $fee_renewal_count) ? 'selected=selected' : '' ?>><?= $i ?></option>
                                  <?php } ?>
                              </select>
                          </div>
                    </div>
                  </td> 
                </tr>

                <tr class="">
                  <td colspan="2">Is fee commissionable?</td>
                  <td><label class="mn">
                      <input type="radio" name="is_fee_commissionable" id="is_fee_commissionable_N" value="N" class="is_fee_commissionable" <?= (!empty($is_fee_commissionable) && $is_fee_commissionable == 'N') ? 'checked' : '' ?> />No</label></td>
                  <td><label class="mn">
                      <input type="radio" name="is_fee_commissionable" id="is_fee_commissionable_Y" value="Y" class="is_fee_commissionable" <?= (!empty($is_fee_commissionable) && $is_fee_commissionable == 'Y') ? 'checked' : '' ?> />Yes</label></td>
                </tr>
                <tr class="commission_div" style="display: <?=$is_fee_commissionable == 'Y' ? 'block' : 'none'?>">
                  <td colspan="4" class="bg_white">
                    <div class="row">
                      <div class="col-sm-4">                        
                        <div class="form-group">
                          <input type="text" name="fee_price" id="fee_price" class="form-control has-value priceControl" onkeypress="return isNumber(event);" value="<?=$price?>">
                          <label>Fee Price</label>
                          <p class="error" id="error_fee_price"></p>
                        </div>
                      </div>
                      <div class="col-sm-4">
                        <div class="form-group">
                          <input type="text" name="NC_amount" id="NC_amount" class="form-control has-value priceControl" onkeypress="return isNumber(event);" value="<?=$nc_amount?>">
                          <label>Non-Commissionable Amount ($0.00)</label>
                          <p class="error" id="error_NC_amount"></p>
                        </div>
                      </div>
                      <div class="col-sm-4">
                        <div class="form-group">
                          <input type="text" name="C_amount" id="C_amount" class="form-control has-value priceControl" readonly="" onkeypress="return isNumber(event);" value="<?=$c_amount?>">
                          <label>Commissionable Amount</label>
                          <p class="error" id="error_C_amount"></p>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
                <tr class="lightblue_tr_bg">
                  <td colspan="2">Does this membership assignment differ by state?</td>
                  <td><label class="mn">
                      <input type="radio" name="differ_by_state" id="differ_by_state_N" value="N" class="differ_by_state" <?= (!empty($differ_by_state) && $differ_by_state == 'N') ? 'checked' : '' ?> />No</label>
                  </td>
                  <td><label class="mn">
                      <input type="radio" name="differ_by_state" id="differ_by_state_Y" value="Y" class="differ_by_state" <?= (!empty($differ_by_state) && $differ_by_state == 'Y') ? 'checked' : '' ?> />Yes</label>
                  </td>
                </tr>
                <tr id="fee_price_row" style="<?= (!empty($is_fee_commissionable) && $is_fee_commissionable=='N')?'' : 'display:none'; ?>" >
                  <td colspan="3">
                    <div class="col-sm-6">
                      <label>Fee Price</label>
                      <div class="add_fee_addonwrap">
                        <div class="add_fee_addon">
                          <i class="fa fa-usd fee_calculated_type"></i>
                        </div>
                        <input type="text" id="retail_price" name="retail_price" onkeypress="return isNumber(event);" class="form-control priceControl" placeholder="10.00" value="<?= $retail_price; ?>" >
                        <p class="error" id="error_retail_price"></p>
                      </div>
                    </div>
                  </td>
                <td></td>
              </tr>
                
              </tbody>
            </table>
           
        </div>     
         <div class="differ_state_wrapper m-t-10">
                  <div id="assign_by_state_div" style="display:<?= ($differ_by_state=="Y") ? 'block' : 'none'; ?>">
                    <div id="association_state_main_div">
                      
                      <?php $count = 1; ?>
                      <?php if(count($associationStateRes) > 0) { ?>
                        <?php foreach ($associationStateRes as $key => $rows) { ?>
                          <?php
                            
                            $productArray = !empty($rows['product_id']) ? explode(",", $rows['product_id']) : array();
                            $statesArray  = !empty($rows['states']) ? explode(",", $rows['states']) : array();
                            $assign_by_state_id = $count;
                          ?>
                          <div id="association_state_inner_div_<?= $assign_by_state_id ?>" class="m-b-5"data-assign_by_state_id="<?= $assign_by_state_id ?>"> 
                            <div class="col-md-10 text-right" id="remove_association_state_inner_div_<?= $assign_by_state_id ?>" data-assign_by_state_id="<?= $assign_by_state_id ?>">
                              <a href="javascript:void(0)" class="remove_association_state_inner" data-assign_by_state_id="<?= $assign_by_state_id ?>"><i class="fa fa-times fa-lg text-danger"></i></a>
                            </div>
                            <?php
                            /*<div class="col-md-6">
                              <div class="form-group height_auto">
                                <select name="association_product[<?= $assign_by_state_id ?>][]" id="association_product_<?= $assign_by_state_id ?>" 
                                 class="association_product_select se_multiple_select" multiple="multiple" data-assign_by_state_id="<?= $assign_by_state_id ?>">
                                  <?php if(count($association_ids_products) > 0) { ?>
                                    <?php foreach ($association_ids_products as $key => $product_id) { ?>
                                      <?php 
                                        $productRes=$pdo->selectOne("SELECT id,name,product_code FROM prd_main where id=:id",array(":id"=>$product_id));
                                      ?>
                                      <option value="<?= $product_id ?>" <?= (!empty($productArray) && in_array($product_id,$productArray)) ? 'selected="selected"' : '' ?>><?= $productRes['name']. ' ('.$productRes['product_code'].')' ?></option>
                                    <?php } ?>
                                  <?php } ?>
                                </select>
                                <p class="error" id="error_association_product_<?= $assign_by_state_id ?>"></p>
                              </div>
                            </div>*/
                            ?>
                            <div class="col-md-6">
                              <div class="form-group">
                                <select name="association_state[<?= $assign_by_state_id ?>][]" id="association_state_<?= $assign_by_state_id ?>"  class="association_state_select" multiple="multiple" data-assign_by_state_id="<?= $assign_by_state_id ?>">
                                  <?php if(count($state_res) > 0){ ?>
                                    <?php foreach ($state_res as $key => $state) {
                                      $state_name=$state['name']; 
                                      $state_short_name=$state['short_name'];
                                    ?>
                                      <option value="<?= $state_name ?>" <?= (!empty($statesArray) && in_array($state_name, $statesArray)) ? 'selected="selected"' : '' ?>><?=  $state_short_name.', '.$state_name ?></option>
                                    <?php } ?>
                                  <?php } ?>
                                </select>
                                <p class="error" id="error_association_state_<?= $assign_by_state_id ?>"></p>
                              </div>
                            </div>
                            <div class="clearfix"></div>
                          </div>
                          <?php $count++; ?>
                        <?php } ?>
                      <?php } ?>
                    </div>
                    <div class="clearfix"></div>
                    <?php /*<div class="col-sm-12">
                      <a href="javascript:void(0);" class="pull-right btn red-link" id="add_association_state_div" style="display: none">+ Add Assignment</a>
                    </div>*/ ?>
                  </div>
                </div>
        <div class="clearfix"></div>
      </div>
      <div class="clearfix m-t-20 text-center">          
          <input type="button" name="add_fee" id="add_fee" class="btn btn-action" value="<?= ($fee_id > 0) ? 'Save Fee' : 'Add Fee' ?>"> 
          <a href="javascript:void(0);" class="btn red-link m-l-15" onclick="window.parent.$.colorbox.close()">Cancel</a> 
          <input type="hidden" name="is_back" id="is_back" value="<?= $is_back ?>">
          <input type="hidden" name="fee_id" id="fee_id" value="<?= $fee_id ?>">
          <input type="hidden" name="membership_id" id="membership_id" value="<?= $membership_id ?>">
          <input type="hidden" name="is_clone" id="is_clone" value="<?=$is_clone?>">
        </div>
    </form>
  </div>
</div>
<div id="association_state_dynamic_div" style="display: none">
  <div id="association_state_inner_div_~number~" class="m-b-5" data-assign_by_state_id="~number~"> 
    <!-- <div class="col-md-12 text-right" id="remove_association_state_inner_div_~number~" data-assign_by_state_id="~number~" >
      <a href="javascript:void(0)" class="remove_association_state_inner" data-assign_by_state_id="~number~"><i class="fa fa-times fa-lg text-danger"></i></a>
    </div> -->
    <?php /*<div class="col-sm-6">
      <div class="form-group height_auto">
        <div class="group_select">
            <select name="association_product[-~number~][]" id="association_product_~number~" multiple="multiple" class="select-multiselect .association_product_select se_multiple_select" style="width: 50%">                     
              <?php foreach ($company_arr as $key=>$company) { ?>
                    <optgroup label='<?= $key ?>'>
                      <?php    foreach ($company as $pkey =>$row) { ?>
                          <option value="<?= $row['id'] ?>" <?= (!empty($assoc_diff_product) && in_array($row['id'], $assoc_diff_product)) ? 'selected' : '' ?> ><?= $row['name'] .' ('.$row['product_code'].')'?></option>
                      <?php } ?>
                    </optgroup>
            <?php } ?>
                 
           </select>
            <label>Products</label>
           <p class="error" id="error_association_product_~number~"></p>
        </div>
      </div>
    </div>*/ ?>

    <div class="col-sm-6">
      <div class="form-group">
        <select name="association_state[-~number~][]" id="association_state_~number~" class="association_state_select se_multiple_select" multiple="multiple" data-assign_by_state_id="~number~" >
          <?php if(count($state_res) > 0){ ?>
            <?php foreach ($state_res as $key => $state) {
              $state_name=$state['name']; 
            ?>
              <option value="<?= $state_name ?>" <?= isset($assign_states) && in_array($state_name, $assign_states) ? "selected = 'selected'" : "" ?>><?= $state_name ?></option>
            <?php } ?>
          <?php } ?>
        </select>
        <label>States</label>
        <p class="error" id="error_association_state_~number~"></p>
      </div>
    </div>

  </div>
  <div class="clearfix"></div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $parent_fee_val=window.parent.$("#vendor_fee_id").val();
    <?php if($differ_by_state == 'Y'){ ?>
      add_association_state_div();
    <?php } ?>
    $("#vendor_fee_id").val($parent_fee_val);
    $("#effective_date").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true
    }).on("changeDate", function (selected) {
      var minDate = new Date(selected.date.valueOf());
      $('#termination_date').datepicker('setStartDate', minDate);
    });
    
    $("#termination_date").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true
    });
        
    $('.data_mask').inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
    $("#products").multipleSelect({
       
    });
    $(".association_product_select").multipleSelect({
       
    });

    $("#differ_products,#differ_state").multipleSelect({
      
    });
    
    $(document).on("change","input[name=differ_by_state]",function(){
      $val=$(this).val();

      $("#assign_by_state_div").hide();
      if($val=="Y"){
        add_association_state_div();
        $("#assign_by_state_div").show();
        //$("#add_association_state_div").show();
      }else{
        $("#association_state_main_div").html('');
        //$("#add_association_state_div").hide();
      }
    });
    /*$(document).on("click","#add_association_state_div",function(){
      add_association_state_div();
    });*/
    $(document).on("click",".remove_association_state_inner",function(){
      $number = $(this).attr('data-assign_by_state_id');
      $("#association_state_inner_div_"+$number).remove();
    });
});
function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && charCode != 46 && charCode != 47 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}
addPriceControl = function(){
  $('.priceControl').priceFormat({
      prefix: '',
          suffix: '',
          centsSeparator: '.',
          thousandsSeparator: ',',
          limit: false,
          centsLimit: 2,
  });
}
function manage_association_state($type,$product_id,$product_name){
    if($type=="Add"){
      $("#manage_fees .association_product_select").each(function($k,$v){
        $id = $(this).attr("data-assign_by_state_id");
        if($("#association_product_"+$id+" option[value='"+$product_id+"']").length <= 0){
          $optionHtml='<option value="'+$product_id+'">'+$product_name+'</option>';
          $("#association_product_"+$id).append($optionHtml);
          $("#association_product_"+$id).multipleSelect('refresh');
        }
      });
    
    }else if($type=="Remove"){
      $("#manage_fees .association_product_select").each(function($k,$v){
        $id = $(this).attr("data-assign_by_state_id");
        if($("#association_product_"+$id+" option[value='"+$product_id+"']").length > 0){
          $("#association_product_"+$id+" option[value='"+$product_id+"']").remove();
          $("#association_product_"+$id).multipleSelect('refresh');
        }
      });
    }else{
      $("#manage_fees .association_product_select").each(function($k,$v){
        $id = $(this).attr("data-assign_by_state_id");
        
        $("#association_product_"+$id).html('');
        $("#association_product_"+$id).multipleSelect('refresh');
        
      });
    }
  }

function add_association_state_div(){
    $count=$("#membership_fee_frm .association_state_select").length;
    $number=$count+1;
    html = $('#association_state_dynamic_div').html();
    html = html.replace(/~number~/g, $number);

    $('#association_state_main_div').append(html);
   
    $("#association_state_"+$number).multipleSelect({
      filter: true,
      width: '100%',
      // placeholder:"Select State(s)",
    });

    $("#association_product_"+$number).multipleSelect({
      filter: true,
      width: '100%',
    });

    $selectedProduct = $("#product").multipleSelect('getSelects');
    $.each($selectedProduct,function($k,$v){
      $product_id=$v;
      $product_name=$("#product option[value='"+$product_id+"']").text();
      manage_association_state('Add',$product_id,$product_name);
    });
}

function manage_association_state($type,$product_id,$product_name){
  if($type=="Add"){
    $("#membership_fee_frm .association_product_select").each(function($k,$v){
      $id = $(this).attr("data-assign_by_state_id");
      if($("#association_product_"+$id+" option[value='"+$product_id+"']").length <= 0){
        $optionHtml='<option value="'+$product_id+'">'+$product_name+'</option>';
        $("#association_product_"+$id).append($optionHtml);
        $("#association_product_"+$id).multipleSelect('refresh');
      }
    });
  
  }else if($type=="Remove"){
    $("#membership_fee_frm .association_product_select").each(function($k,$v){
      $id = $(this).attr("data-assign_by_state_id");
      if($("#association_product_"+$id+" option[value='"+$product_id+"']").length > 0){
        $("#association_product_"+$id+" option[value='"+$product_id+"']").remove();
        $("#association_product_"+$id).multipleSelect('refresh');
      }
    });
  }else{
    $("#membership_fee_frm .association_product_select").each(function($k,$v){
      $id = $(this).attr("data-assign_by_state_id");
      
      $("#association_product_"+$id).html('');
      $("#association_product_"+$id).multipleSelect('refresh');
      
    });
  }
}
$(document).off("change",".is_fee_commissionable");
$(document).on("change",".is_fee_commissionable",function(){
  $val=$(this).val();
  if($val=="Y"){
    $(".commission_div").show('slow');
    $("#fee_price_row").hide('slow');
  }else{
    $(".commission_div").hide('slow');
    $("#fee_price_row").show('slow');
  }
});
$(document).off("change",".differ_by_state");
$(document).on("change",".differ_by_state",function(){
  $val=$(this).val();
  if($val=="Y"){
    $(".state_div").show('slow');
  }else{
    $(".state_div").hide('slow');
  }
});

$(document).off("change",".fee_renewal_type");
$(document).on("change",".fee_renewal_type",function(){
  $val=$(this).val();
  if($val=="Renewals"){
    $("#fee_renewal_count").prop("disabled",false);
  }else{
    $("#fee_renewal_count").val(0);
    $("#fee_renewal_count").prop("disabled",true);
  }
  $('#fee_renewal_count').selectpicker('refresh');
});
$(document).on("change",".is_fee_on_renewal",function(){
  $val=$(this).val();
  $("#renewal_numbers_row").hide();
  if($val=="Y"){
    $("#renewal_numbers_row").show();
  }
});

$(document).on("click","#add_fee",function(){
  $("#ajax_loader").show();
  $.ajax({
    url:'ajax_add_membership_fee.php',
    dataType:'JSON',
    data:$("#membership_fee_frm").serialize(),
    type:'POST',
    success:function(res){
      $("#ajax_loader").hide();
      $('.error').html('');
      if(res.status=="success"){
         $is_back = $("#is_back").val();

         if($is_back){
          window.parent.$.colorbox.close();
         }else{
          $("#fee_id").val(res.fee_id);
          window.parent.$("#vendor_fee_id").val(res.vendor_fee_id);
          window.parent.load_membership_fee_div();
          window.parent.display_message(res.message,'success');
          window.parent.$.colorbox.close();
         }
          
      } else{
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
});

$(document).on("blur keyup","#NC_amount",function(){

    $non_commissionable_price =$(this).val();
    $price = $("#fee_price").val();

    $non_commissionable_price = $non_commissionable_price.replace(",", "");
    $price = $price.replace(",", "");

    $non_commissionable_price = parseFloat($non_commissionable_price);
    $price = parseFloat($price);
    $commissionable_price=($price - $non_commissionable_price).toFixed(2);

    if($commissionable_price<0){
      swal({   
          text: 'Error: Please enter valid price',
          showCancelButton: true,
          confirmButtonText: 'Confirm',
          cancelButtonText: 'Cancel',
          showCloseButton: true, 
          }).then(function(){   

      });
      $("#C_amount").val('0.00');
    }else{
      $("#C_amount").val($commissionable_price);
    }
    $("#fee_price").priceFormat({
      prefix: '',
          suffix: '',
          centsSeparator: '.',
          thousandsSeparator: ',',
          limit: false,
          centsLimit: 2,
    });
    $("#NC_amount").priceFormat({
        prefix: '',
            suffix: '',
            centsSeparator: '.',
            thousandsSeparator: ',',
            limit: false,
            centsLimit: 2,
    });
    $("#C_amount").priceFormat({
        prefix: '',
            suffix: '',
            centsSeparator: '.',
            thousandsSeparator: ',',
            limit: false,
            centsLimit: 2,
    });
});
$(document).on("blur keyup","#fee_price",function(){

    $non_commissionable_price =$("#NC_amount").val();
    $price = $("#fee_price").val();

    $non_commissionable_price = $non_commissionable_price.replace(",", "");
    $price = $price.replace(",", "");

    $non_commissionable_price = parseFloat($non_commissionable_price);
    $price = parseFloat($price);
    $commissionable_price=($price - $non_commissionable_price).toFixed(2);

    if($commissionable_price<0){
      swal({   
          text: 'Error: Please enter valid price',
          showCancelButton: true,
          confirmButtonText: 'Confirm',
          cancelButtonText: 'Cancel',
          showCloseButton: true,  
        }).then(function(){   

      });
      $("#C_amount").val('0.00');
    }else{
      $("#C_amount").val($commissionable_price);
    }

    $("#fee_price").priceFormat({
        prefix: '',
            suffix: '',
            centsSeparator: '.',
            thousandsSeparator: ',',
            limit: false,
            centsLimit: 2,
    });
    $("#C_amount").priceFormat({
        prefix: '',
            suffix: '',
            centsSeparator: '.',
            thousandsSeparator: ',',
            limit: false,
            centsLimit: 2,
    });
    $("#NC_amount").priceFormat({
        prefix: '',
            suffix: '',
            centsSeparator: '.',
            thousandsSeparator: ',',
            limit: false,
            centsLimit: 2,
    });
});

$(document).on("blur keyup","#retail_price",function(){
  $("#retail_price").priceFormat({
        prefix: '',
            suffix: '',
            centsSeparator: '.',
            thousandsSeparator: ',',
            limit: false,
            centsLimit: 2,
    });
});

addPriceControl = function(){
  
  $('.priceControl').priceFormat({
      prefix: '',
      suffix: '',
      centsSeparator: '.',
      thousandsSeparator: '',
      limit: false,
      centsLimit: 2,
  });
}
PercentagePriceControl = function(){
  
  $('.priceControl').priceFormat({
      prefix: '',
      suffix: '',
      thousandsSeparator: '',
      limit: 3,
      centsLimit: 0,
  });
}
removePriceControl = function(){
  $('.priceControl').unpriceFormat();
}
</script> 
