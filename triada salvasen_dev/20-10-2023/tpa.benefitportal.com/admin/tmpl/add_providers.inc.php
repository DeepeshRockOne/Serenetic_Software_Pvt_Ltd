<?php include "notify.inc.php";?>
<div class="panel panel-default  panel-space">
  <form action="" role="form" method="post"  name="user_form" id="user_form" enctype="multipart/form-data">
  <div class="panel-heading">
    <div class="panel-title">
      <p class="fs16 mn"><strong class="fw500">Provider Settings</strong></p>
    </div>
  </div>
    <div class="panel-body theme-form">
      <input type="hidden" name="proId" value="<?=!empty($providers_res['id']) ? $providers_res['id'] : '' ?>">
      <input type="hidden" name="group_count" value="1" id="group_count">
      <input type="hidden" name="display_counter" value="0" id="display_counter">
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            <input type="text" class="form-control" name="provider_name" value="<?=!empty($provider_name) ? $provider_name : ''?>">
            <label>Provider Name*</label>
            <span class="error error_preview" id="error_provider_name"></span>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            <input type="text" class="form-control" name="display_id" value="<?=!empty($display_id) ? $display_id : ''?>">
            <label>Provider ID (Must Be Unique)*</label>
            <span class="error error_preview" id="error_display_id"></span>
          </div>
        </div>
        <?php if(!empty($sub_providers_res) && count($sub_providers_res) > 0) {
          foreach ($sub_providers_res as $key => $value) { 
            $product_arr = array();
            if(!empty($value['product_id'])) {
              $product_arr = explode(',', $value['product_id']); 
            }
            ?>
              <div id="innerProductDiv_<?=$value['group_id']?>" class="inner_product_div" data-id="<?=$value['group_id']?>">
                <div class="col-sm-12">
                  <hr class="m-t-n m-b-30" />
                </div>
                <div class="col-md-6 col-sm-12">
                  <div class="form-group">
                      <input type="hidden" name="dynamicFields[<?=$value['group_id']?>]">
                      <select name= "products[<?=$value['group_id']?>][]" class="se_multiple_select selProducts" multiple="multiple" id="productSelect_<?=$value['group_id']?>" data-id="<?=$value['group_id']?>">
                        <?php if(!empty($company_arr) && count($company_arr) > 0){
                          foreach ($company_arr as $key => $company) { ?>
                            <optgroup label="<?= $key ?>">
                              <?php foreach ($company as $pkey => $row) {
                                $has_value = '';
                                if(!empty($product_arr) && in_array($row['id'], $product_arr)){
                                  $has_value = 'selected="selected"';
                                } ?>
                                <option value="<?= $row['id'] ?>" data-name="<?= $row['name'] ?>" data-id="<?=$value['group_id']?>" <?=$has_value?>><?= $row['name'] . ' (' . $row['product_code'] . ') '?></option>
                              <?php } ?>
                            </optgroup>
                          <?php }
                        } ?> 
                      </select>
                      <label>Product(s)</label>
                      <span class="error error_preview" id="error_products_<?=$value['group_id']?>"></span>
                    <a class="add_url_close fs18 text-light-gray remove_product_group" href="javascript:void(0);" data-id="<?=$value['group_id']?>" id="remove_product_group_<?=$value['group_id']?>" style="<?=($value['group_id'] == 1) ? 'display: none' : ''; ?>">X</a>
                  </div>
                  <?php if(!empty($product_arr) && count($product_arr) > 0) { 
                    $counter = 0;
                    foreach ($product_arr as $index => $element) { 
                      $counter++;
                      $add_class = 'br-bottom';
                      $add_url_div = false;
                      if(count($product_arr) == $counter) {
                        $add_class = 'br-right';
                        $add_url_div = true;
                      }  ?>
                      <div class="clearfix"></div>
                      <div class="add_url_treewrap" id="main_tree_div_<?=$value['group_id']?>">
                          <div id="inner_tree_div_<?=$element?>_<?=$value['group_id']?>" class="inner_tree_div" data-productID="<?=$element?>" data-id="<?=$value['group_id']?>">
                            <div class="single_row">
                              <div class="phone-control-wrap">
                                <div class="phone-addon w-130 v-align-top">
                                  <label class="label label-blue topClass_<?=$value['group_id']?> <?=$add_class?>"><span><?=$productArray[$element]['name'] . ' (' . $productArray[$element]['product_code'] . ') ' ?></span></label>
                                </div>
                                <div class="rightClass_<?=$value['group_id']?> phone-addon">
                                  <?php if($add_url_div) { ?>
                                    <div data-id="<?=$value['group_id']?>">
                                      <div class="form-group ">
                                        <input type="text" class="form-control" data-id="<?=$value['group_id']?>" name="url_products[<?=$value['group_id']?>]" value="<?=$value['url']?>" id="url_products_<?=$value['group_id']?>"/><label>URL (http://www.url.com)</label>
                                        <span class="error error_preview" id="error_url_products_<?=$value['group_id']?>"></span>
                                      </div>
                                    </div>
                                  <?php } ?>
                                </div>
                              </div>
                            </div>
                          </div>
                      </div>
                    <?php } ?>
                  <?php }  ?>
                </div>
              </div>
        <?php
          }
        } ?>
        <div id="main_product_div">

        </div>
        <div class="clearfix"></div>
        <div class="col-sm-6">
          <div class="single_row text-right">
            <a href="javascript:void(0);" class="btn btn-info add_product_group" id="add_product_group" style="<?=!empty($providers_res['id']) ? '' : 'display: none' ?>">+ Product Group</a>
          </div>
        </div>
      </div>
      <div class="step_btn_wrap m-t-30 text-right">
        <input type="button" name="" id="btn_save" class="btn btn-action" value="Save">
        <input type="button" name="" id="" class="btn red-link" value="Cancel" onclick="window.location='providers.php'">
      </div>
    </div>
  </form>
</div>

<div id="dynamic_product_div" style="display: none">
  <div id="innerProductDiv_~number~" class="inner_product_div" data-id="~number~">
    <div class="col-sm-12">
      <hr class="m-t-n m-b-30" />
    </div>
    <div class="col-md-6 col-sm-12">
      <div class="form-group">
          <input type="hidden" name="dynamicFields[~number~]">
          <select name= "products[~number~][]" class="se_multiple_select" multiple="multiple" id="productSelect_~number~" data-id="~number~">
            <?php if(!empty($company_arr) && count($company_arr) > 0){
              foreach ($company_arr as $key=>$company) { ?>
                <optgroup label="<?= $key ?>">
                <?php foreach ($company as $pkey =>$row) { ?>
                  <option value="<?= $row['id'] ?>" data-name="<?= $row['name'] ?>" data-id="~number~"><?= $row['name'] . ' (' . $row['product_code'] . ') ' ?></option>
                <?php } ?>
                </optgroup>
              <?php }
            } ?> 
          </select>
          <label>Product(s)</label>
          <span class="error error_preview" id="error_products_~number~"></span>
        <a class="add_url_close fs18 text-light-gray remove_product_group" href="javascript:void(0);" data-id="~number~" id="remove_product_group_~number~" style="display: none;">X</a>
      </div>
      <div class="add_url_treewrap" id="main_tree_div_~number~"></div>
    </div>
  </div>
</div>

<div id="dynamic_tree_div" style="display: none;">
  <div id="inner_tree_div_~productID~_~number~" class="inner_tree_div" data-productID="~productID~" data-id="~number~">  
    <div class="single_row">
      <div class="phone-control-wrap">
        <div class="phone-addon w-130 v-align-top">
          <label class="label label-blue topClass_~number~ br-bottom"><span>~productName~</span></label>
        </div>
        <div class="rightClass_~number~"></div>
      </div>
    </div>
  </div>
</div>

<div id="dynamic_url_div" style="display:none">
  <div data-id="~number~">
    <div class="form-group ">
      <input type="text" class="form-control" data-id="~number~" name="url_products[~number~]" id="url_products_~number~" /><label>URL (http://www.url.com)</label>
      <span class="error error_preview" id="error_url_products_~number~"></span>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
    <?php if(empty($providers_res['id'])) { ?>
      loadProductDiv();
    <?php } else { ?>
       $('.selProducts').multipleSelect({selectAll: false,
         width:'100%',
          filter:true,
        onClick:function(e,e1){
          $sel_obj=e.instance.$el[0];
          $dataID=$($sel_obj).attr('data-id');
          $productName= e.label;
          $productID = e.value;

          if(e.checked){
            loadTreeDiv($dataID,$productID,$productName, 'old');
          }else{
            $("#inner_tree_div_"+$productID+"_"+$dataID).remove();
            loadTreeLink($dataID, 'old');
          }
        },
        onOptgroupClick:function(e){
          $childRecords=e.children;
          
          $sel_obj=e.instance.$el[0];
          $dataID=$($sel_obj).attr('data-id');
          $url_value = $("#url_products_" + $dataID).val();
          $.each($childRecords,function($k,$v){
            $productID = $v.value;
            $productName= $('#productSelect_'+$dataID + ' option[value="'+$productID+'"]').attr('data-name');
              if($v.checked){
                loadTreeDiv($dataID,$productID,$productName, 'old');
              }else{
                $("#inner_tree_div_"+$productID+"_"+$dataID).remove();
                loadTreeLink($dataID, 'old',$url_value);
              }
          });
        },
        onTagRemove:function(e){
          $sel_obj=e.instance.$el[0];
          $dataID=$($sel_obj).attr('data-id');
          $productName= e.label;
          $productID = e.value;

          $("#inner_tree_div_"+$productID+"_"+$dataID).remove();
          loadTreeLink($dataID, 'old');
        
        }
      });
    <?php } ?>
  });

  $(document).off('click', '#add_product_group');
  $(document).on('click', '#add_product_group', function(e){
    e.preventDefault();
    $add_counter = parseInt($("#group_count").val()) + 1;
    if($add_counter >= 10){
      $(this).hide();
    }
    $("#group_count").val($add_counter);
    loadProductDiv();
  });

  $(document).off('click', '.remove_product_group');
  $(document).on('click', '.remove_product_group', function(e){
    e.preventDefault();
    $add_counter = parseInt($("#group_count").val()) - 1;
    if($add_counter <= 10){
      $("#add_product_group").show();
    }
    $("#group_count").val($add_counter);
    $("#innerProductDiv_"+$(this).attr('data-id')).remove();
  });

  $(document).off("click", "#btn_save");
  $(document).on("click", "#btn_save", function(e){
    e.preventDefault();
    $(".error").html('').hide();
    $("#ajax_loader").show();
    $.ajax({
      url:"<?= $ADMIN_HOST ?>/ajax_add_provider.php",
      data: $("#user_form").serialize(),
      method: 'POST',
      dataType: 'json',
      success: function(res) {
        if (res.status == 'success') {
          $(".error").html('').hide();
          setNotifySuccess("Provider Added Successfully!");
          setTimeout(function(){ 
            window.location.href = 'providers.php';
          }, 1000);  
          $("#ajax_loader").hide();
        } else if (res.status == 'fail') {
          $("#ajax_loader").hide();
          $.each(res.errors, function (index, error) {
            $('#error_' + index).html(error).show();
          });
        }
      }
    });
  });

  loadProductDiv = function(){
    $count = $("#user_form .inner_product_div").length;
    $display_counter = parseInt($('#display_counter').val());
    $number=$count+1;
    if($display_counter > $count){
      $number = $display_counter + 1;
    }

    $neg_number = $number * -1;

    html = $('#dynamic_product_div').html();
    $('#main_product_div').append(html.replace(/~number~/g, $neg_number));
    $("#display_counter").val($number);
    if($number > 1){
      $("#remove_product_group_"+$neg_number).show();
    }

    $('#productSelect_'+$neg_number).multipleSelect({selectAll: false,
        width:'100%',
        filter:true,
        onClick:function(e){

          $dataID=$neg_number;
          $productName= e.label;
          $productID = e.value;

          if(e.checked){
            loadTreeDiv($dataID,$productID,$productName, 'new');
          }else{
            $("#inner_tree_div_"+$productID+"_"+$dataID).remove();
            loadTreeLink($dataID, 'new');
          }
        },
        onOptgroupClick:function(e){
          $sel_obj=e.instance.$el[0];
          $dataID=$($sel_obj).attr('data-id');
          $childRecords=e.children;

          $.each($childRecords,function($k,$v){
            $productID = $v.value;
            $productName= $('#productSelect_'+$dataID + ' option[value="'+$productID+'"]').attr('data-name');
              if($v.checked){
                loadTreeDiv($dataID,$productID,$productName, 'new');
              }else{
                $("#inner_tree_div_"+$productID+"_"+$dataID).remove();
                loadTreeLink($dataID, 'new');
              }
          });
        },
        onTagRemove:function(e){
          $dataID=$neg_number;
          $productName= e.label;
          $productID = e.value;

          $("#inner_tree_div_"+$productID+"_"+$dataID).remove();
          loadTreeLink($dataID, 'new');
        
        }
      });
  }

  loadTreeDiv = function($number,$productID,$productName,$str_type){
    $pos_number = Math.abs($number);

    if(!$("#inner_tree_div_"+$productID+"_"+$number).length > 0) {
      html = $('#dynamic_tree_div').html();

      if($str_type == 'new') {
        $pos_number = $pos_number * -1;
      }

      html = html.replace(/~number~/g, $pos_number);
      html = html.replace(/~productID~/g, $productID);
      html = html.replace(/~productName~/g, $productName);
      $("#add_product_group").show();
      $('#main_tree_div_'+$pos_number).append(html);
    }
    loadTreeLink($number, $str_type);
  }

  loadTreeLink = function($number, $str_type,$url_value){
    $pos_number = Math.abs($number);

    if($str_type == 'new') {
      $pos_number = $pos_number * -1;
    }

    if(typeof($url_value) === "undefined") {
        $url_value = $("#url_products_" + $pos_number).val();
    }

    $(".topClass_"+$pos_number).addClass('br-bottom').removeClass('br-right');
    $(".topClass_"+$pos_number).last().removeClass('br-bottom').addClass('br-right');

    $(".rightClass_"+$pos_number).html('');
    $(".rightClass_"+$pos_number).removeClass('phone-addon');

    html = $('#dynamic_url_div').html();
    html = html.replace(/~number~/g, $pos_number);

    $(".rightClass_"+$pos_number).last().html(html);
    $(".rightClass_"+$pos_number).last().addClass('phone-addon');
    if(typeof($url_value) !== "undefined" && $url_value != '') {
      $("#url_products_" + $pos_number).val($url_value).addClass("has-value");
    }

  }
</script> 
