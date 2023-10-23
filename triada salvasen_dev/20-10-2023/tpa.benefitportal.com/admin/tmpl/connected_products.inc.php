  <div class="panel-group enroll_accordion" id="accordion">
    <div id="connection_main_div" class="connection_product_wrap">
    <?php if(!empty($categoryArr)) { ?>
        <?php foreach ($categoryArr as $catId => $catRow) { 
          $catName = !empty($catId) ? getname('prd_category',$catId,'title','id') : '';
        ?>
        <div class="panel panel-default enroll_plan_wrap">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" class="collapsed categoryTitle" data-parent="#accordion" data-catId="<?=$catId?>" href="#collapse<?=$catId?>">
              <?=$catName?></a>
            </h4>
          </div>
          <div id="collapse<?=$catId?>" class="panel-collapse collapse">
            <div class="panel-body">
              <div id="catBody_<?=$catId?>">
                
              </div>
            </div>
          </div>
      </div>
  <?php }
    }
  ?>
    </div>
</div>

<div id="no_records_div" style="<?=empty($categoryArr) ? '' : 'display: none' ?>">
    <p class="text-center">No Records Found</p>
</div>
    

<div class="clearfix m-b-5"></div>

<div id="connection_dynamic_div" style="display: none">
  <div class="connection_div" id="connection_div_~number~" data-id="~number~">
  <div class="panel panel-default enroll_plan_wrap ">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" class="collapsed" data-parent="#accordion" href="#collapse~number~">
          + Connection</a>
        </h4>
      </div>
      <div id="collapse~number~" class="panel-collapse collapse in">
      <div class="panel-body">
        <form id="frm_connection_~number~" method="post">
            <input type="hidden" name="connection_id[~number~]" id="connection_id_~number~" value="0">
            <div class="row">
            <div class="col-md-offset-1">
                <div class="col-md-6 ">
                    <h3 class="font-light mn fs22 pull-left">+ Connection </h3>
                    <a href="javascript:void(0);" class="pull-right m-t-10 red-link remove_connection_div" id="remove_connection_div_~number~" data-id="~number~">Cancel</a>
                </div>
                
                <div class="clearfix m-b-10"></div>
                
                <div class="theme-form">
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <select class="add_control_~number~ connection_category" id="connection_category_~number~" name="connection_category[~number~]" data-id="~number~">
                                <option value="" selected="selected" hidden></option>
                                <?php if(!empty($resCategory)) { ?>
                                    <?php foreach ($resCategory as $key => $value) { ?>
                                        <option value="<?= $value['id'] ?>"><?= $value['title'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            <label>Select Product Category</label>
                            <p class="error" id="error_connection_category_~number~"></p>
                        </div>
                    </div>
                    <div class="clearfix"></div>

                    <div class="clearfix m-l-30">
                       <p class="m-b-5"><strong>Upgrade Plan Rules:</strong></p><p> Option after a Plan has become effective, if the member can upgrade the Plan.</p>
                       <div class="row">
                         <div class="col-sm-6" >
                            <div class="form-group">
                                <select class="add_control_~number~ upgrade_option" name="upgrade_option[~number~]" id="upgrade_option_~number~" data-id="~number~">
                                    <option value=""></option>
                                    <option value="Available Without Restrictions">Available Without Restrictions</option>
                                    <option value="Available Within Specific Time Frame">Available Within Specific Time Frame</option>
                                </select>
                                <label for="upgrade_option_~number~">Select Upgrade Option</label>
                                <p class="error" id="error_upgrade_option_~number~"></p>
                            </div>
                        </div>
                        <div class="col-sm-4" >
                            <div class="form-inline">
                                <div class="form-group upgrade_within_div_~number~" style="display: none;">
                                    <select name="upgrade_within[~number~]" id="upgrade_within_~number~" class="add_control_~number~ input-sm">
                                      <?php
                                        $end_range = 365;
                                      ?>
                                      <?php for($i=0;$i<=$end_range;$i++){ ?>
                                      <option value="<?= $i ?>" <?= isset($upgrade_within) && $i==$upgrade_within ? 'selected' :'' ?>><?= $i ?></option>
                                      <?php } ?>
                                    </select>
                                    <select name="upgrade_within_type[~number~]" id="upgrade_within_type_~number~" class="add_control_~number~ input-sm upgrade_within_type" data-id="~number~">
                                      <option value="Days">Days</option>
                                      <option value="Weeks">Weeks</option>
                                      <option value="Months">Months</option>
                                      <option value="Years">Years</option>
                                    </select>
                                    <p class="error" id="error_upgrade_within_~number~"></p>
                                </div>
                            </div>
                        </div>
                       </div>

                       <p class="m-b-5"><strong>Life Event Rules : </strong></p>
                       <div><label class="label-input" for="is_allow_upgrade_life_event_~number~"> <input type="checkbox" name="is_allow_upgrade_life_event[~number~]" id="is_allow_upgrade_life_event_~number~" class="is_allow_upgrade_life_event" data-id="~number~" value="Y"> Allow upgrade if primary Plan holder has specific life event?</label></div>
                       <div class="row">
                         <div class="col-sm-6 upgrade_life_event_options_div_~number~" style="display: none;">
                            <p>Select all that apply: </p>
                            <div class="form-group">
                                <select name="upgrade_life_event_options[~number~][]" class="se_multiple_select" id="upgrade_life_event_options_~number~" multiple="multiple">
                                    <?php 
                                      if(!empty($LifeEvents)) {
                                        foreach ($LifeEvents as $LifeEventKey => $LifeEventLabel) {
                                          ?>
                                          <option value="<?=$LifeEventKey?>"><?=$LifeEventLabel?></option>
                                          <?php
                                        }
                                      }
                                    ?>
                                </select>
                                <label for="upgrade_life_event_options_~number~">Life Event</label>
                                <p class="error" id="error_upgrade_life_event_options_~number~"></p>
                            </div>
                        </div>
                       </div>
                       <p class="m-b-5"><strong>Downgrade Plan Rules:</strong></p>
                       <p>Option after a Plan has become effective, if the member can downgrade the Plan.</p>
                       <div class="row">
                         <div class="col-sm-6" >
                            <div class="form-group">
                                <select class="add_control_~number~ downgrade_option" name="downgrade_option[~number~]" id="downgrade_option_~number~" data-id="~number~">
                                    <option value=""></option>
                                    <option value="Available Without Restrictions">Available Without Restrictions</option>
                                    <option value="Available Within Specific Time Frame">Available Within Specific Time Frame</option>
                                </select>
                                <label for="downgrade_option_~number~">Select Downgrade Option</label>
                                <p class="error" id="error_downgrade_option_~number~"></p>
                            </div>
                        </div>
                        <div class="col-sm-6" >
                            <div class="form-inline">
                                <div class="form-group downgrade_within_div_~number~" style="display: none;">
                                    <select name="downgrade_within[~number~]" id="downgrade_within_~number~" class="add_control_~number~ input-sm">
                                      <?php
                                      $end_range = 365;
                                      ?>
                                      <?php for($i=0;$i<=$end_range;$i++){ ?>
                                      <option value="<?= $i ?>"><?= $i ?></option>
                                      <?php } ?>
                                    </select>
                                    <select name="downgrade_within_type[~number~]" id="downgrade_within_type_~number~" class="add_control_~number~ input-sm downgrade_within_type" data-id="~number~">
                                      <option value="Days">Days</option>
                                      <option value="Weeks">Weeks</option>
                                      <option value="Months">Months</option>
                                      <option value="Years">Years</option>
                                    </select>
                                    <p class="error" id="error_downgrade_within_~number~"></p>
                                </div>
                            </div>
                        </div>
                       </div>
                       <p class="m-b-5"><strong>Life Event Rules:</strong></p>
                       <div> <label class="label-input" for="is_allow_downgrade_life_event_~number~"> <input type="checkbox" name="is_allow_downgrade_life_event[~number~]" id="is_allow_downgrade_life_event_~number~" class="is_allow_downgrade_life_event" data-id="~number~" value="Y">  Allow downgrade if primary Plan holder has specific life event?</label></div>
                       <div class="row">
                         <div class="col-sm-6 downgrade_life_event_options_div_~number~" style="display: none;">
                            <p>Select all that apply:</p> 
                            <div class="form-group">
                                <select name="downgrade_life_event_options[~number~][]" class="se_multiple_select" id="downgrade_life_event_options_~number~" multiple="multiple">
                                    <?php 
                                      if(!empty($LifeEvents)) {
                                        foreach ($LifeEvents as $LifeEventKey => $LifeEventLabel) {
                                          ?>
                                          <option value="<?=$LifeEventKey?>"><?=$LifeEventLabel?></option>
                                          <?php
                                        }
                                      }
                                    ?>
                                </select>
                                <label for="downgrade_life_event_options_~number~">Life Event</label>
                                <p class="error" id="error_downgrade_life_event_options_~number~"></p>
                            </div>
                        </div>
                       </div>
                    </div>
                    <div class="clearfix m-t-20"></div>
                    <p><span class="fw500">Select products : </span> First product is the highest offered product and the last product is the lowest offered product in relation to display and upgrade/downgrade rules.</p>
                    <div class="connection_products_main_div_~number~ connected_prd_up_down" id="connection_products_main_div_~number~" data-id="~number~"></div>
                    <div class="col-md-6 ">
                        <p class="error" id="error_connection_products_~number~"></p>
                        <div class="form-group text-right">
                            <button type="button" class="btn btn-primary  addConnection" data-id="~number~" id="addConnection_~number~" disabled>Connect Product</button>
                            <button type="button" class="btn btn-primary saveConnection" data-id="~number~" id="saveConnection_~number~" style="display: none">Save</button>
                            <hr />
                        </div>
                    </div>
                </div>
                
            </div>
            </div>
        </form>
      </div>
      </div>
  </div>
   </div>
</div>
</div>
<div id="connection_products_dynamic_div" style="display: none">
  <div class="connected_grade_arrow"></div>
  <div class="col-sm-6" >
      <div class="connection_products_div_~div_id~" id="connection_products_div_~div_id~_~number~" data-div-id="~div_id~" data-id="~number~">
          <div class="form-group">
              <div class="row m-l-30">
                   <div class="col-xs-2 text-right" >
                      <div class="search_btn font-bold fs18 text-blue display_number" id="display_number_~div_id~_~number~" data-display-number="~connection_product_count~">~connection_product_count~</div>
                  </div>
                  <div class="col-xs-9 pr">
                      <select class="add_control_~div_id~_~number~ category_products" id="connection_products_~div_id~_~number~" name="connection_products[~div_id~][~number~]" data-id="~number~" data-div-id="~div_id~" data-prev="">
                          <option value=""></option>
                          <?php if(!empty($resProduct)){
                              foreach ($resProduct as $key => $value) {?>
                                  <?php if(!empty($value['product_id']) && !empty($value['category_id'])){ ?>
                                      <option value="<?= $value['product_id']?>" class="option_cat_<?= $value['category_id'] ?> option_product_<?= $value['product_id'] ?>" style="display: none" <?= in_array($value['product_id'], $allConnectedProduct)?'disabled':'' ?>><?= $value['name'].' ('.$value['product_code'].')' ?></option>
                                  <?php }                                
                              }
                          } ?>
                      </select>
                      <label>Select Product</label>
                  </div>
                  <div class="col-xs-1" >
                      <a href="javascript:void(0);" class="fs16 font-bold text-light-gray remove_connection_products_div" data-div-id="~div_id~" data-id="~number~">X</a>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <div class="clearfix"></div>
</div>

<script type="text/javascript">
    var $categoryCount=1;
    var $productCount=1;

    $(document).ready(function(){
        $(".upgrade_life_event_options, .downgrade_life_event_options").multipleSelect({ 
            
        });

        setTimeout(function(){
            fRefresh();
        },2000);

        $(".form-control.category_products").selectpicker({ 
            container: 'body', 
            style:'btn-select',
            noneSelectedText: '',
            dropupAuto:false,
        }).on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
            $previousValue = previousValue;
            $val = $(this).val();
            $div_id=$(this).attr('data-div-id');
            $id=$(this).attr('data-id');         
            $selectID=$(this).attr('id');
            if($previousValue){
                enableDisableProduct($previousValue,false,$selectID);
            }  
            if($val){
                enableDisableProduct($val,true,$selectID);
            }
        });
    });
    
    addConnectionDiv = function(){
        $count=$("#connection_main_div .connection_div").length;
        $number=$count+1;
        $connection_product_count = $number;
        $number = "-"+$categoryCount;

        html = $('#connection_dynamic_div').html();
        html = html.replace(/~number~/g,$number)
        html = html.replace(/~connection_product_count~/g,$connection_product_count);
        $('#connection_main_div').append(html);
        $('.is_allow_downgrade_life_event, .is_allow_upgrade_life_event').uniform();
        $("#no_records_div").hide();

        $(".add_control_"+$number).addClass('form-control');
        $("#connection_category_"+$number).selectpicker({ 
                container: 'body', 
                style:'btn-select',
                noneSelectedText: '',
                dropupAuto:false,
        });
        $("#upgrade_option_"+$number+", #upgrade_within_"+$number+", #upgrade_within_type_"+$number+", #downgrade_option_"+$number+", #downgrade_within_"+$number+", #downgrade_within_type_"+$number).selectpicker({ 
            container: 'body', 
            style:'btn-select',
            noneSelectedText: '',
            dropupAuto:false,
        });
        $("#upgrade_life_event_options_"+$number+", #downgrade_life_event_options_"+$number).multipleSelect({ 
            
        });

        $categoryCount = $categoryCount+1;
        $("#addNewConnection").show();
    }

    addConnectedProducts = function($div_id){
        $category_id=$("#connection_category_"+$div_id).val();

        $count=$("#connection_main_div .connection_products_div_"+$div_id).length;
        $number=$count+1;
        $connection_product_count = $number;
        $number = "-"+$productCount;

        html = $('#connection_products_dynamic_div').html();
        html = html.replace(/~number~/g,$number)
        html = html.replace(/~div_id~/g,$div_id)
        html = html.replace(/~connection_product_count~/g,$connection_product_count);
        $('#connection_products_main_div_'+$div_id).append(html);

        if(($("#connection_products_"+$div_id+"_"+$number+" .option_cat_"+$category_id).length) > 0){
            $("#connection_products_"+$div_id+"_"+$number+" .option_cat_"+$category_id).show();
        }
        $("#connection_products_"+$div_id+"_"+$number+" option").each(function() {
            if($(this).css('display') == 'none'){
                $(this).remove();
            }
        });

        $("#addConnection_"+$div_id).prop('disabled',false);

        $(".add_control_"+$div_id+"_"+$number).addClass('form-control');

        $("#connection_products_"+$div_id+"_"+$number).selectpicker({ 
            container: 'body', 
            style:'btn-select',
            noneSelectedText: '',
            dropupAuto:false,
        }).on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
            $previousValue = previousValue;
            $val = $(this).val();
            $div_id=$(this).attr('data-div-id');
            $id=$(this).attr('data-id');         
            $selectID=$(this).attr('id');
            if($previousValue){
                enableDisableProduct($previousValue,false,$selectID);
            }  
            if($val){
                enableDisableProduct($val,true,$selectID);
            }
        });


        $productCount = $productCount+1;

        saveConnectionButton($div_id);
        fRefresh();
    }

    $(document).off("click",".categoryTitle");
    $(document).on("click",".categoryTitle",function(e){
      var category_id = $(this).attr('data-catId');
      if($('#catBody_'+category_id +' .connection_div').length > 0){

      }else{
        $('#catBody_'+category_id).hide();
        $.ajax({
          url: 'get_connected_category_products.php',
          type: 'GET',
          data: {category_id : category_id},
          beforeSend:function(){
            $("#ajax_loader").show();
          },
          success: function(res) {
            $('#ajax_loader').hide();
            $('#catBody_'+category_id).html(res).show();
            common_select();
            fRefresh();
            $(".upgrade_life_event_options, .downgrade_life_event_options").multipleSelect('refresh');
            $('.is_allow_downgrade_life_event, .is_allow_upgrade_life_event').uniform();
          }
        });
      }
    });

    $(document).off("click",".remove_connection_div");
    $(document).on("click",".remove_connection_div",function(e){
        e.stopPropagation();
        $removeID=$(this).attr('data-id');
        $connectionID = $("#connection_id_"+$removeID).val();

        if($connectionID > 0){
            swal({
                text: "you want to remove this connected products?",
                showCancelButton: true,
                confirmButtonColor: "#bd4360",
                confirmButtonText: "Confirm",
            }).then(function() {
                $("#ajax_loader").show();
                $.ajax({
                  url: '<?= $ADMIN_HOST ?>/ajax_delete_connected_product.php',
                  data: {connection_id:$connectionID},
                  dataType:'JSON',
                  method: 'POST',
                  success: function(res) {
                    $("#ajax_loader").hide();
                    if(res.status == 'success'){
                        $catSelectID=$("#connection_category_"+$removeID).attr('id');
                        $("#connection_products_main_div_"+$removeID+" .category_products option:selected").each(function(){
                            $val = $(this).val();
                            if($val > 0){
                                enableDisableProduct($val,false,'');
                            }
                        });

                        $("#connection_div_"+$removeID).remove();
                        setTimeout(function(){
                          connected_products();
                        },1000);
                    }
                  }
                });
            });
            
        }else{
            $catSelectID=$("#connection_category_"+$removeID).attr('id');
            $("#connection_products_main_div_"+$removeID+" .category_products option:selected").each(function(){
                $val = $(this).val();
                if($val > 0){
                    enableDisableProduct($val,false,'');
                }
            });
            $("#connection_div_"+$removeID).remove();
        }
       
    });

    $(document).off("change",".connection_category");
    $(document).on("change",".connection_category",function(e){
        e.stopPropagation();
        $catID=$(this).attr('data-id');
              
        $("#connection_products_main_div_"+$catID+" .category_products option:selected").each(function(){
            $val = $(this).val();
            if($val > 0){
                enableDisableProduct($val,false,'');
            }
        });

        $("#connection_products_main_div_"+$catID).html('');
        addConnectedProducts($catID);
        
    });

    $(document).off("click",".addConnection");
    $(document).on("click",".addConnection",function(e){
        e.stopPropagation();
        $id=$(this).attr('data-id');
        addConnectedProducts($id);
    });

    $(document).off("change",".upgrade_option");
    $(document).on("change",".upgrade_option",function(e){
        e.stopPropagation();
        $div_id=$(this).attr('data-id');
        if($(this).val() == "Available Within Specific Time Frame") {
            $(".upgrade_within_div_" + $div_id).show();
        } else {
            $(".upgrade_within_div_" + $div_id).hide();
            
        }
    });

    $(document).off("change",".upgrade_within_type");
    $(document).on("change",".upgrade_within_type",function(){
        $div_id=$(this).attr('data-id');
        $val=$(this).val();
        if($val=="Weeks"){
          $end_range=52;
        }else if($val=="Months"){
          $end_range=24;
        }else if($val=="Years"){
          $end_range=10;
        }else{
          $end_range=365;
        }
        $("#upgrade_within_" + $div_id).html('');
        for($i=0;$i<=$end_range;$i++){
          $option_html='<option value="'+$i+'">'+$i+'</option>';
          $("#upgrade_within_" + $div_id).append($option_html);
        }
        $('#upgrade_within_' + $div_id).selectpicker('refresh');
    });

    $(document).off("change",".downgrade_within_type");
    $(document).on("change",".downgrade_within_type",function(){
        $div_id=$(this).attr('data-id');
        $val=$(this).val();
        if($val=="Weeks"){
          $end_range=52;
        }else if($val=="Months"){
          $end_range=24;
        }else if($val=="Years"){
          $end_range=10;
        }else{
          $end_range=365;
        }
        $("#downgrade_within_" + $div_id).html('');
        for($i=0;$i<=$end_range;$i++){
          $option_html='<option value="'+$i+'">'+$i+'</option>';
          $("#downgrade_within_" + $div_id).append($option_html);
        }
        $('#downgrade_within_' + $div_id).selectpicker('refresh');
    });

    $(document).off("change",".is_allow_upgrade_life_event");
    $(document).on("change",".is_allow_upgrade_life_event",function(e){
        e.stopPropagation();
        $div_id=$(this).attr('data-id');
        if($(this).is(":checked")) {
            $(".upgrade_life_event_options_div_" + $div_id).show();
        } else {
            $(".upgrade_life_event_options_div_" + $div_id).hide();
        }
    });

    $(document).off("change",".downgrade_option");
    $(document).on("change",".downgrade_option",function(e){
        e.stopPropagation();
        $div_id=$(this).attr('data-id');
        if($(this).val() == "Available Within Specific Time Frame") {
            $(".downgrade_within_div_" + $div_id).show();
        } else {
            $(".downgrade_within_div_" + $div_id).hide();
            
        }
    });

    $(document).off("change",".is_allow_downgrade_life_event");
    $(document).on("change",".is_allow_downgrade_life_event",function(e){
        e.stopPropagation();
        $div_id=$(this).attr('data-id');
        if($(this).is(":checked")) {
            $(".downgrade_life_event_options_div_" + $div_id).show();
        } else {
            $(".downgrade_life_event_options_div_" + $div_id).hide();
        }
    });

    $(document).off("click",".remove_connection_products_div");
    $(document).on("click",".remove_connection_products_div",function(e){
        e.stopPropagation();
        $div_id=$(this).attr('data-div-id');
        $id=$(this).attr('data-id');

        $selectProductID=$("#connection_products_"+$div_id+"_"+$id).val();

        $removed_display_number = parseInt($("#display_number_"+$div_id+"_"+$id).attr('data-display-number'));
        $("#connection_products_"+$div_id+"_"+$id).selectpicker('destroy');
        $("#connection_products_div_"+$div_id+"_"+$id).remove();

        $('.connection_products_div_'+$div_id+' .display_number').each(function(){
            $display_number = parseInt($(this).attr('data-display-number'));
            if($display_number > $removed_display_number){
                $display_number = $display_number - 1;
                $(this).attr('data-display-number',$display_number);
                $(this).html($display_number);
            }
        });

        saveConnectionButton($div_id);
        enableDisableProduct($selectProductID,false,'');
    });

    saveConnectionButton = function($div_id){
        $count=$("#connection_main_div .connection_products_div_"+$div_id).length;
        $("#saveConnection_"+$div_id).hide();
        if($count>1){        
            $("#saveConnection_"+$div_id).show();
        }
    }
    enableDisableProduct = function($product_id,$type,$selectID){
      if($product_id > 0){
        $(".category_products option[value=" + $product_id + "]").each(function(){
            $id=$(this).parent().attr('id');
            
            if($id!=$selectID){
                
                $(this).prop('disabled',$type);
                $("#"+$id).selectpicker('refresh');
            }
        });
      }
    }

    $(document).off("click",".saveConnection");
    $(document).on("click",".saveConnection",function(e){
        e.stopPropagation();
        $id=$(this).attr('data-id');
        $("#ajax_loader").show();
        $(".error").html('');
        $.ajax({
            url:'<?=$ADMIN_HOST?>/ajax_manage_connected_product.php',
            dataType:'JSON',
            data:$("#frm_connection_"+$id).serialize(),
            type:'POST',
            success:function(res){
                $("#ajax_loader").hide();
                if(res.status=="success"){
                    setNotifySuccess('Connected Products Updated');
                    if(res.connection){
                        $.each(res.connection,function(key,value){
                            $("#connection_id_"+key).val(value);
                            $("#remove_connection_div_"+key).html('Remove');
                        });
                    }
                    setTimeout(function(){
                      connected_products();
                    },1000);
                }else{
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
</script>