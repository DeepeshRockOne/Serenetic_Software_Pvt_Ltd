<div class="panel panel-default panel-block theme-form prd_edit_popup">
  <div class="panel-heading">
    <div class="panel-title ">
      <h4 class="mn  text-black">Edit Product - <span class="fw300"><?=$products['name']?></span> </h4>
    </div>
  </div>
  <div class="panel-body">
    <form action="" name="product_edit" id="product_edit">
    <input type="hidden" name="agent_id" id="agent_id" value="<?=checkIsset($_GET['agent_id'])?>">
      <div class="row">
        <div class="col-sm-8">
          <div class="form-group m-t-7">
              <select class="form-control" name="cm_product" id="cm_product" data-product_id="<?=$_GET['product_id']?>">
                <?=$drop_down_html?>
              </select>
              <label>Select Commission Rule For Product<em>*</em></label>
            </div>
            <p class="error"><span id="err_cm_product"></span></p>
        </div>
        <div class="col-sm-4">
          <label class="fs12">
            <input type="checkbox" name="loa[cm]"/>
            Apply this update to LOA agents only</label>
          <label class="fs12">
            <input type="checkbox" name="downline[cm]"/>
            Apply this update to downline</label>
        </div>
      </div>

      <p class="fs16 fw600 m-b-20"><span class="text-red">Available</span> States <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" data-placement="right" title="" data-original-title="Select all states where this product is available to be sold."></i> </p>
      <div class="row m-b-20">
        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
          <div class="table-responsive">
            <table class="<?=$table_class?> spouse_detail_table">
              <thead>
                <tr>
                  <th colspan="2">Allowed</th>
                </tr>
              </thead>
              <tbody>
              <?php 
              if(!empty($allStateRes)){
                foreach ($allStateRes as $key => $state) {
                  if(in_array($state['name'],$available_state)){
                    array_push($availableCheckAll, 'Check');
                    $state_arr[$key] = $state;
                  }else{
                    array_push($availableCheckAll, 'unCheck');
                  }
                }
              } 
              // if(!empty($dbstate)){
              //   $availableCheckAll= array();
              //     foreach($state_arr as $state){
              //       if(in_array($state['name'],$dbstate)){
              //         array_push($availableCheckAll, 'Check');
              //       }else{
              //         array_push($availableCheckAll, 'unCheck');
              //         break;
              //       }
              //     }
              // }
              ?>
              <tr>
                <td width="20px">
                  <label class="red_checkbox"><input name="availableCheckAll" id="availableCheckAll" type="checkbox" value="availableCheckAll" <?=in_array('unCheck',$availableCheckAll) ? '' : 'checked'?> class="" data-match-on="Availability">
                  </label>
                </td>
                <td >Select All States</td>
              </tr>
              <?php $stateRowCount=1; ?>
              <?php if(!empty($state_arr)){ ?>
              <?php foreach ($state_arr as $key => $state) {
                
                  $checked ='';
                  if(!empty($dbstate)){                  
                    $checked = !empty($dbstate) && in_array($state['name'],$dbstate)?'':'checked';
                  }else{
                    $checked = !empty($available_state) && in_array($state['name'],$available_state)?'checked':'';
                  }
                ?>
              <?php if($stateRowCount!=1 && $stateRowCount%13 == 0){?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
        <div class="table-responsive">
          <table class="<?=$table_class?> spouse_detail_table">
            <thead>
              <tr>
                <th width="20px" >Allowed</th>
                <th ></th>
              </tr>
            </thead>
            <tbody>
              <?php } ?>
              <tr>
                <td width="20px" >
                  <div class="list_label" style="width:100%">
                    <label class="red_checkbox">
                      <input name="available_state[]" id="available_state_<?=$state['id']?>" class="available_state" data-match-on="Availability" <?=$checked?> type="checkbox" value="<?= $state['name'] ?>" data-state-id="<?= $state['id'] ?>" data-short-name="<?= $state['short_name'] ?>" data-name="<?= $state['name'] ?>">
                    </label>
                  </div>
                </td>
                <td><?= $state['short_name']. ', '.$state['name'] ?> </td>
              </tr>
              <?php $stateRowCount++;} ?>
              <?php } ?>
            </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="clearfix">
        <label class="fs12">
          <input type="checkbox" name="loa[state]"  />
          Apply this update to LOA agents only</label>
        <br />
        <label class="fs12">
          <input type="checkbox" name="downline[state]"/>
          Apply this update to downline</label>
      </div>
      <hr />
      <p class="fs16 fw600 m-b-5">Additional Product Combination Rules</p>
      <p class="fs12 m-b-20"><em>Enter any products that can not be purchased by a person who has purchased this product, or other product combination rules.</em></p>
      <div class="row theme-form">
         <div class="col-sm-6">
          <div class="form-group height_auto">
            <div class="row">
               <div class="col-sm-2">
                  <label class="p-t-7 fw500">Auto Assign</label>
               </div>
               <div class="col-sm-10">
                      <select  multiple="multiple" class="se_multiple_select" name="auto[]" id="autoAssignProduct">
                         <?php if(!empty($productArray)){ ?>
                         <?php foreach ($productArray as $categoryName => $productRow) { ?>
                         <optgroup label='<?= $categoryName; ?>'>
                            <?php foreach ($productRow as $key1 => $row1) { 
                               if($row1['pricing_model'] == 'FixedPrice'){ ?>
                            <option value="<?= $row1['id'] ?>" <?=!empty($autoAssignProduct) && in_array($row1['id'],$autoAssignProduct)?'selected':''?> <?= !in_array($row1['id'],$autoAssignProduct) && (in_array($row1['id'], $productCombinationRules)  || in_array($row1['id'], $productCombinationRulesArrmain))?'disabled':''?> ><?= $row1['name'] .' ('.$row1['product_code'].')' ?></option>
                            <?php } } ?>
                         </optgroup>
                         <?php } ?>
                         <?php } ?>
                      </select>
                      <label>Select</label>
               </div>
               <div class="col-sm-12">
                <p class="text-action fs12"><em>Products selected in this category will automatically be assigned but user may remove them from application</em></p>
              </div>
            </div>
          </div>
         </div>
         <div class="col-sm-6">
          <div class="form-group height_auto">
            <div class="row">
               <div class="col-sm-2">
                  <label class="p-t-7 fw500">Packaged</label>
               </div>
               <div class="col-sm-10">
                      <select  multiple="multiple" class="se_multiple_select" name="packaged[]" id="packagedProduct">
                         <?php if(!empty($productArray)){ ?>
                         <?php foreach ($productArray as $categoryName => $productRow) { ?>
                         <optgroup label='<?= $categoryName; ?>'>
                            <?php foreach ($productRow as $key1 => $row1) { ?>
                            <option value="<?= $row1['id'] ?>" <?=!empty($packagedProduct) && in_array($row1['id'],$packagedProduct)?'selected':''?> <?= !in_array($row1['id'],$packagedProduct) && (in_array($row1['id'], $productCombinationRules) || in_array($row1['id'], $productCombinationRulesArrmain))?'disabled':''?>><?= $row1['name'] .' ('.$row1['product_code'].')' ?></option>
                            <?php } ?>
                         </optgroup>
                         <?php } ?>
                         <?php } ?>
                      </select>
                      <label>Select</label>
               </div>
               <div class="col-sm-12">
                <p class="text-action fs12"><em>At least one product selected in this category will be required on application or active on an existing member for this product to be added</em></p>
              </div>
            </div>
          </div>
         </div>
         <div class="clearfix"></div>
         <div class="col-sm-6">
          <div class="form-group height_auto">
            <div class="row">
               <div class="col-sm-2">
                  <label class="p-t-7 fw500">Excludes</label>
               </div>
               <div class="col-sm-10">
                      <select  multiple="multiple" class="se_multiple_select" name="excludes[]" id="excludeProduct">
                         <?php if(!empty($productArray)){ ?>
                         <?php foreach ($productArray as $categoryName => $productRow) { ?>
                         <optgroup label='<?= $categoryName; ?>'>
                            <?php foreach ($productRow as $key1 => $row1) { ?>
                            <option value="<?= $row1['id'] ?>" <?=!empty($excludeProduct) && in_array($row1['id'],$excludeProduct)?'selected':''?> <?= !in_array($row1['id'],$excludeProduct) && (in_array($row1['id'], $productCombinationRules)  || in_array($row1['id'], $productCombinationRulesArrmain))?'disabled':''?>><?= $row1['name'] .' ('.$row1['product_code'].')' ?></option>
                            <?php } ?>
                         </optgroup>
                         <?php } ?>
                         <?php } ?>
                      </select>
                      <label>Select</label>
               </div>
               <div class="col-sm-12">
                <p class="text-action fs12"><em>Products selected in this category will not be allowed to be combined on application or to existing member if this product is currently active.</em></p>
              </div>
            </div>
          </div>
         </div>
         <div class="col-sm-6">
          <div class="form-group height_auto">
            <div class="row">
               <div class="col-sm-2">
                  <label class="p-t-7 fw500">Required</label>
               </div>
               <div class="col-sm-10">
                      <select  multiple="multiple" class="se_multiple_select" name="required[]" id="requiredProduct">
                         <?php if(!empty($productArray)){ ?>
                         <?php foreach ($productArray as $categoryName => $productRow) { ?>
                         <optgroup label='<?= $categoryName; ?>'>
                            <?php foreach ($productRow as $key1 => $row1) {
                               if($row1['pricing_model'] == 'FixedPrice'){ ?>
                            <option value="<?= $row1['id'] ?>" <?=!empty($requiredProduct) && in_array($row1['id'],$requiredProduct)?'selected':''?> <?= !in_array($row1['id'],$requiredProduct) && (in_array($row1['id'], $productCombinationRules) || in_array($row1['id'], $productCombinationRulesArrmain))?'disabled':''?>><?= $row1['name'] .' ('.$row1['product_code'].')' ?></option>
                            <?php } } ?>
                         </optgroup>
                         <?php } ?>
                         <?php } ?>
                      </select>
                      <label>Select</label>
               </div>
               <div class="col-sm-12">
                <p class="text-action fs12"><em>Products selected in this category are required to be assigned on application or active on an existing member for this product to be added</em></p>
              </div>
            </div>
          </div>
         </div>
      </div>
      <div class="clearfix">
        <label class="fs12">
          <input type="checkbox" name="loa[add]" />
        Apply this update to LOA agents only</label>
        <br />
        <label class="fs12">
          <input type="checkbox" name="downline[add]" />
        Apply this update to downline</label>
      </div>
      <div class="text-center "> 
      <a href="javascript:void(0);" id="prd_edit_popup" class="prd_edit_popup btn btn-action" >Save</a> 
      <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Close</a>
      <p class="m-t-20 m-b-0">Any rule changes made will take effect immediately on any new enrollments.</p> 
      <p class="error m-t-20 m-b-0"><span id="err_script_running"></span></p>
      </div>
    </div>
  </form>
</div>
<!-- <div style='display:none'>
      <div id='inline_content' class="panel panel-default panel-block mn">
        <div class="panel-body text-center">
          <p class="fs20 m-b-15">Are you sure you want to change this products settings?</p>
          <p class="fs14 text-red">* Any rule changes made will take effect immediately on any new enrollments. Confirm that you want to make this change.</p>
          <div class="clearfix m-t-7">
            <a href="javascript:void(0);" class="btn btn-action" id="save_product">Yes</a>
              <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Cancel</a>
          </div>
        </div>
      </div>
</div> -->

<script type="text/javascript">
$(document).ready(function() {

      $("#excludeProduct").multipleSelect({
        selectAll: false,
        onClick:function(e){
          $text = e.text;
          $productName = e.value;
          if(e.selected){
            $("#autoAssignProduct [value='"+$productName+"']").prop('disabled',true);
            $("#requiredProduct [value='"+$productName+"']").prop('disabled',true);
            $("#packagedProduct [value='"+$productName+"']").prop('disabled',true);
          }else{
            $("#autoAssignProduct [value='"+$productName+"']").prop('disabled',false);
            $("#requiredProduct [value='"+$productName+"']").prop('disabled',false);
            $("#packagedProduct [value='"+$productName+"']").prop('disabled',false);
          }
          $("#autoAssignProduct").multipleSelect('refresh');
          $("#requiredProduct").multipleSelect('refresh');
          $("#packagedProduct").multipleSelect('refresh');
        },
        onOptgroupClick:function(e){
          $childRecords=e.children;

          $.each($childRecords,function($k,$v){
            $productName = $v.value;
            if(!$v.disabled){
              if(e.selected){
                $("#autoAssignProduct [value='"+$productName+"']").prop('disabled',true);
                $("#requiredProduct [value='"+$productName+"']").prop('disabled',true);
                $("#packagedProduct [value='"+$productName+"']").prop('disabled',true);
              }else{
                $("#autoAssignProduct [value='"+$productName+"']").prop('disabled',false);
                $("#requiredProduct [value='"+$productName+"']").prop('disabled',false);
                $("#packagedProduct [value='"+$productName+"']").prop('disabled',false);
              }
            }
          });
          $("#autoAssignProduct").multipleSelect('refresh');
          $("#requiredProduct").multipleSelect('refresh');
          $("#packagedProduct").multipleSelect('refresh');
        },
        onTagRemove:function(e){
          $productName = e.value;
          $("#autoAssignProduct [value='"+$productName+"']").prop('disabled',false);
          $("#requiredProduct [value='"+$productName+"']").prop('disabled',false);
          $("#packagedProduct [value='"+$productName+"']").prop('disabled',false);
          
          $("#autoAssignProduct").multipleSelect('refresh');
          $("#requiredProduct").multipleSelect('refresh');
          $("#packagedProduct").multipleSelect('refresh');
          
        }
      });
			$("#autoAssignProduct").multipleSelect({
				selectAll: false,
				onClick:function(e){
					$text = e.text;
					$productName = e.value;
					if(e.selected){
						$("#excludeProduct [value='"+$productName+"']").prop('disabled',true);
						$("#requiredProduct [value='"+$productName+"']").prop('disabled',true);
						$("#packagedProduct [value='"+$productName+"']").prop('disabled',true);
					}else{
						$("#excludeProduct [value='"+$productName+"']").prop('disabled',false);
						$("#requiredProduct [value='"+$productName+"']").prop('disabled',false);
						$("#packagedProduct [value='"+$productName+"']").prop('disabled',false);
					}
					$("#excludeProduct").multipleSelect('refresh');
					$("#requiredProduct").multipleSelect('refresh');
					$("#packagedProduct").multipleSelect('refresh');
				},
				onOptgroupClick:function(e){
					$childRecords=e.children;

					$.each($childRecords,function($k,$v){
						$productName = $v.value;
						if(!$v.disabled){
							if(e.selected){
								$("#excludeProduct [value='"+$productName+"']").prop('disabled',true);
								$("#requiredProduct [value='"+$productName+"']").prop('disabled',true);
								$("#packagedProduct [value='"+$productName+"']").prop('disabled',true);
							}else{
								$("#excludeProduct [value='"+$productName+"']").prop('disabled',false);
								$("#requiredProduct [value='"+$productName+"']").prop('disabled',false);
								$("#packagedProduct [value='"+$productName+"']").prop('disabled',false);
							}
						}
					});
					$("#excludeProduct").multipleSelect('refresh');
					$("#requiredProduct").multipleSelect('refresh');
					$("#packagedProduct").multipleSelect('refresh');
				},
				onTagRemove:function(e){
					$productName = e.value;
					$("#excludeProduct [value='"+$productName+"']").prop('disabled',false);
					$("#requiredProduct [value='"+$productName+"']").prop('disabled',false);
					$("#packagedProduct [value='"+$productName+"']").prop('disabled',false);

					$("#excludeProduct").multipleSelect('refresh');
					$("#requiredProduct").multipleSelect('refresh');
					$("#packagedProduct").multipleSelect('refresh');
					
				}
			});
			$("#requiredProduct").multipleSelect({
				selectAll: false,
				onClick:function(e){
					$text = e.text;
					$productName = e.value;
					if(e.selected){
						$("#excludeProduct [value='"+$productName+"']").prop('disabled',true);
						$("#autoAssignProduct [value='"+$productName+"']").prop('disabled',true);
						$("#packagedProduct [value='"+$productName+"']").prop('disabled',true);
					}else{
						$("#excludeProduct [value='"+$productName+"']").prop('disabled',false);
						$("#autoAssignProduct [value='"+$productName+"']").prop('disabled',false);
						$("#packagedProduct [value='"+$productName+"']").prop('disabled',false);
					}
					$("#excludeProduct").multipleSelect('refresh');
					$("#autoAssignProduct").multipleSelect('refresh');
					$("#packagedProduct").multipleSelect('refresh');
				},
				onOptgroupClick:function(e){
					$childRecords=e.children;

					$.each($childRecords,function($k,$v){
						$productName = $v.value;
						if(!$v.disabled){
							if(e.selected){
								$("#excludeProduct [value='"+$productName+"']").prop('disabled',true);
								$("#autoAssignProduct [value='"+$productName+"']").prop('disabled',true);
								$("#packagedProduct [value='"+$productName+"']").prop('disabled',true);
							}else{
								$("#excludeProduct [value='"+$productName+"']").prop('disabled',false);
								$("#autoAssignProduct [value='"+$productName+"']").prop('disabled',false);
								$("#packagedProduct [value='"+$productName+"']").prop('disabled',false);
							}
						}
					});
					$("#excludeProduct").multipleSelect('refresh');
					$("#autoAssignProduct").multipleSelect('refresh');
					$("#packagedProduct").multipleSelect('refresh');
				},
				onTagRemove:function(e){
					$productName = e.value;
					$("#excludeProduct [value='"+$productName+"']").prop('disabled',false);
					$("#autoAssignProduct [value='"+$productName+"']").prop('disabled',false);
					$("#packagedProduct [value='"+$productName+"']").prop('disabled',false);
					
					$("#excludeProduct").multipleSelect('refresh');
					$("#autoAssignProduct").multipleSelect('refresh');
					$("#packagedProduct").multipleSelect('refresh');
					
				}
			});
			$("#packagedProduct").multipleSelect({
				selectAll: false,
				onClick:function(e){
					$text = e.text;
					$productName = e.value;
					if(e.selected){
						$("#excludeProduct [value='"+$productName+"']").prop('disabled',true);
						$("#autoAssignProduct [value='"+$productName+"']").prop('disabled',true);
						$("#requiredProduct [value='"+$productName+"']").prop('disabled',true);
					}else{
						$("#excludeProduct [value='"+$productName+"']").prop('disabled',false);
						$("#autoAssignProduct [value='"+$productName+"']").prop('disabled',false);
						$("#requiredProduct [value='"+$productName+"']").prop('disabled',false);
					}
					$("#excludeProduct").multipleSelect('refresh');
					$("#autoAssignProduct").multipleSelect('refresh');
					$("#requiredProduct").multipleSelect('refresh');
				},
				onOptgroupClick:function(e){
					$childRecords=e.children;

					$.each($childRecords,function($k,$v){
						$productName = $v.value;
						if(!$v.disabled){
							if(e.selected){
								$("#excludeProduct [value='"+$productName+"']").prop('disabled',true);
								$("#autoAssignProduct [value='"+$productName+"']").prop('disabled',true);
								$("#requiredProduct [value='"+$productName+"']").prop('disabled',true);
							}else{
								$("#excludeProduct [value='"+$productName+"']").prop('disabled',false);
								$("#autoAssignProduct [value='"+$productName+"']").prop('disabled',false);
								$("#requiredProduct [value='"+$productName+"']").prop('disabled',false);
							}
						}
						
					});
					$("#excludeProduct").multipleSelect('refresh');
					$("#autoAssignProduct").multipleSelect('refresh');
					$("#requiredProduct").multipleSelect('refresh');
				},
				onTagRemove:function(e){
					$productName = e.value;
					$("#excludeProduct [value='"+$productName+"']").prop('disabled',false);
					$("#autoAssignProduct [value='"+$productName+"']").prop('disabled',false);
					$("#requiredProduct [value='"+$productName+"']").prop('disabled',false);
					
					$("#excludeProduct").multipleSelect('refresh');
					$("#autoAssignProduct").multipleSelect('refresh');
					$("#requiredProduct").multipleSelect('refresh');
					
				}
			});

});

$(document).on("click","#prd_edit_popup",function(){
  $.ajax({
    url: "ajax_agent_prd_edit.php",
    type: "POST",
    dataType:"json",
    success: function(res){
      if(res.status == 'running'){
        parent.$.colorbox.close();
        parent.setNotifyError("Agent's LOA/Downline update process is in progress.");
      }else{
        updateCommissionRule();
      }
    }
  });
});

// $(document).off('click',"#save_product");
// $(document).on('click',"#save_product",function(e){
//   updateCommissionRule('save_product');
// });

$(document).on("click",".available_state",function(){
			$selectedState=$(this).val();
			$state_id=$(this).attr('data-state-id');

    if($('.available_state:checked').length == $('.available_state').length){
        $('#availableCheckAll').prop('checked',true);
        $("#availableCheckAll").uniform();
    }else{
        $('#availableCheckAll').prop('checked',false);
        $("#availableCheckAll").uniform();
    }
    $(".available_state").uniform();
});

$(document).off('click','#availableCheckAll');
$(document).on('click','#availableCheckAll',function(e){
    if($(this).is(":checked")){
        $(".available_state").prop('checked',true);
    }else{
        $(".available_state").prop('checked',false);
    }
    $(".available_state").uniform();
    $("#availableCheckAll").uniform();
});

function updateCommissionRule(){
  parent.$("#ajax_loader").show();
  var $data = $("#product_edit :not(.available_state)").serializeArray();
  $(".available_state").each(function(e){
    if(!$(this).is(":checked")){
      $data.push({name:"available_state[]",value:$(this).val()});
    }
  });
  $data.push({name:"product_id",value:$("#cm_product").attr('data-product_id')});
  $.ajax({
    url:"ajax_agents_product_edit.php",
    type:"POST",
    dataType:"json",
    data :$data,
    success:function(res){
      parent.$("#ajax_loader").hide();
      // if(res.status=='popup'){
      //     $.colorbox({href:'#inline_content',inline: true, width: '390px', height: '260px', closeButton:false});
      // }else 
      if(res.status == 'success'){
        parent.window.location="agent_detail_v1.php?id=<?=$_GET['agent_id']?>";
      }else if (res.status == 'fail') {
        var is_error = true;
        $('.error span').html('');
        $.each(res.errors, function (index, value) {
            $('#err_' + index).html(value).show();
            if(is_error){
                var offset = $('#err_' + index).offset();
                var offsetTop = offset.top;
                var totalScroll = offsetTop - 50;
                $('body,html').animate({scrollTop: totalScroll}, 1200);
                is_error = false;
            }
        });
      }
    }
  });
}
</script>