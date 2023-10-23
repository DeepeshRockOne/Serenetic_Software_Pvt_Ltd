<div class="white-box manage_fees_wrap">
   <div class="panel panel-default panel-block">
      <div class="panel-heading">
         <h4 class="mn"><?= ($resProduct) ? 'Manage Association' :'Add Association ' ?></h4>
      </div>
      <div class="panel-body theme-form">
         <form  method="POST" id="manage_fees" enctype="multipart/form-data"  autocomplete="off">
            <input type="hidden" name="fee_id" id="fee_id" value="<?= $fee_id ?>">
            <div class="section_space">
              <h4 class="h4_title">Association Settings</h4>
              <div class="row">
                <div class="col-sm-8">
                   <div class="form-group">
                      <input name="product_name" id="product_name" type="text"  class="form-control" value="<?= $product_name ?>">
                      <label>Association Name <span class="text-light-gray">(Must be unique)</span></label>
                      <p class="error" id="error_product_name"></p>
                   </div>
                </div>
                <div class="col-sm-4">
                   <div class="form-group">
                      <input name="product_code" id="product_code" type="text" class="form-control" value="<?= $product_code?>">
                      <label>Association ID <span class="text-light-gray">(Must be unique)</span></label>
                      <p class="error" id="error_product_code"></p>
                   </div>
                </div>
              </div>
              <div class="row">
                  <div class="form-is-this-fee clearfix">
                    <div class="col-sm-6 col-md-3">
                      <h5 class="h5_title">Is there a fee to this association?</h5>
                      <div class="radio-v">
                         <label>
                         <input  name="is_fee_to_association" <?= $is_fee_to_association=="N" ? 'checked' : '' ?> type="radio" value="N"> No
                         </label>
                      </div>
                      <div class="radio-v">
                         <label>
                         <input  name="is_fee_to_association" <?= $is_fee_to_association=="Y" ? 'checked' : '' ?> type="radio" value="Y"> Yes
                         </label>
                      </div>
                      <p class="error" id="error_is_fee_to_association"></p>
                    </div>
                    <div class="fee_to_association_div" style="<?= ($is_fee_to_association=="Y") ? '' : 'display: none'; ?>">
                      <div class="col-sm-6 col-md-3">
                        <h5 class="h5_title">Is this fee included in the price of the product?</h5>
                        <div class="radio-v">
                           <label>
                           <input  name="is_association_fee_included" <?= $is_association_fee_included=="N" ? 'checked' : '' ?> type="radio" value="N"> No
                           </label>
                        </div>
                        <div class="radio-v">
                           <label>
                           <input  name="is_association_fee_included" <?= $is_association_fee_included=="Y" ? 'checked' : '' ?> type="radio" value="Y"> Yes
                           </label>
                        </div>
                        <p class="error" id="error_is_association_fee_included"></p>
                      </div>
                      <div class="col-sm-6 col-md-3">
                          <h5 class="h5_title">Is this fee on renewals?</h5>
                          <div class="radio-v">
                             <label>
                             <input  name="is_fee_on_renewal" <?= $is_fee_on_renewal=="N" ? 'checked' : '' ?> type="radio" value="N"> No
                             </label>
                          </div>
                          <div class="radio-v">
                             <label>
                             <input  name="is_fee_on_renewal" <?= $is_fee_on_renewal=="Y" ? 'checked' : '' ?> type="radio" value="Y" > Yes
                             </label>
                          </div>
                          <p class="error" id="error_is_fee_on_renewal"></p>
                          <div class="m-l-25" id="renewal_div" style="<?= $is_fee_on_renewal=="Y" ? '' :'display:none' ?>">
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
                                   <?php for($i=1;$i<=12;$i++) { ?>
                                      <option value="<?= $i ?>" <?= isset($fee_renewal_count) && $i == $fee_renewal_count ? 'selected=selected' : '' ?>><?= $i ?></option>
                                   <?php } ?>
                                </select>
                                <p class="error" id="error_fee_renewal_count"></p>
                             </div>
                          </div>
                      </div>
                      <div class="col-sm-6 col-md-3">
                          <h5 class="h5_title">Is this fee commissionable to agents?</h5>
                          <div class="radio-v">
                             <label>
                             <input  name="is_fee_on_commissionable" <?= $is_fee_on_commissionable=="N" ? 'checked' : '' ?> type="radio" value="N"> No
                             </label>
                          </div>
                          <div class="radio-v">
                             <label>
                             <input  name="is_fee_on_commissionable" <?= $is_fee_on_commissionable=="Y" ? 'checked' : '' ?> type="radio" value="Y"> Yes
                             </label>
                          </div>
                          <p class="error" id="error_is_fee_on_commissionable"></p>
                      </div>
                    </div>
                  </div>
              </div>
              <div class="fee_to_association_div" style="<?= ($is_fee_to_association=="Y") ? '' : 'display: none'; ?>">
                 <div class="row ">
                    <h4 class="h4_title">Fee Structure</h4>
                    <div class="table-responsive ">
                       <table class="<?=$table_class;?> ">
                          <thead>
                             <tr>
                                <th width="20%">Amount of Fee</th>
                                <th width="20%" class="commissionable_div" style="<?= $is_fee_on_commissionable=="Y" ? '' :'display:none' ?>">Non Commissionable Amount</th>
                                <th width="20%" class="commissionable_div" style="<?= $is_fee_on_commissionable=="Y" ? '' :'display:none' ?>">Commissionable Amount</th>
                                <th>Product</th>
                             </tr>
                          </thead>
                          <tbody id="fee_structure_body">
                             <tr>
                                <td>
                                   <div class="fees_tbl_field">
                                      <input type="text" id="price" name="price" value="<?= $price ?>" class="form-control priceControl" placeholder="0.00" onkeypress="return isNumber(event)">
                                      <span class="feestbl_icon_left">$</span>
                                      <p class="error error_preview" id="error_price"></p>
                                   </div>
                                </td>
                                <td class="commissionable_div" style="<?= $is_fee_on_commissionable=="Y" ? '' :'display:none' ?>">
                                   <div class="fees_tbl_field">
                                      <input type="text" id="non_commissionable_price" name="non_commissionable_price" value="<?= $non_commissionable_price ?>" class="form-control priceControl" placeholder="0.00" onkeypress="return isNumber(event)">
                                      <span class="feestbl_icon_left">$</span>
                                      <p class="error error_preview" id="error_non_commissionable_price"></p>
                                   </div>
                                </td>
                                <td class="commissionable_div" style="<?= $is_fee_on_commissionable=="Y" ? '' :'display:none' ?>">
                                   <div class="fees_tbl_field">
                                      <input type="text" id="commissionable_price" name="commissionable_price" value="<?= $commissionable_price ?>" class="form-control priceControl" placeholder="0.00" onkeypress="return isNumber(event)" readonly="">
                                      <span class="feestbl_icon_left">$</span>
                                      <p class="error error_preview" id="error_commissionable_price"></p>
                                   </div>
                                </td>
                                <td>
                                   <div class="fees_tbl_field">
                                      <select name="product[]" id="product" multiple="multiple">
                                          <?php if(!empty($productListRes)){?>
                                            <?php foreach ($productListRes as $key => $product) { ?> 
                                                <option value="<?= $product['id'] ?>" <?=((!empty($association_ids_products) && in_array($product['id'],$association_ids_products)) || (!empty($product_id) && $product_id == $product['id']))?'selected="selected"':''?>><?= $product['name'] .' ('. $product['product_code'].')' ?></option>   
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
                 </div>
              </div>
              <div class="clearfix"></div>
              <div class="fee_to_association_div row m-t-20"  style="<?= ($is_fee_to_association=="Y") ? '' : 'display: none'; ?>">
                
                <div class="col-sm-12">
                  <h5 class="h5_title">Does a product membership/association assignment differ by state?</h5>
                  <div class="radio-v">
                     <label>
                     <input  name="is_assign_by_state" <?= $is_assign_by_state=="N" ? 'checked' : '' ?> type="radio" value="N"> No
                     </label>
                  </div>
                  <div class="radio-v">
                     <label>
                     <input  name="is_assign_by_state" <?= $is_assign_by_state=="Y" ? 'checked' : '' ?> type="radio" value="Y"> Yes
                     </label>
                  </div>
                  <p class="error" id="error_is_assign_by_state"></p>
                </div>
                
                <div id="assign_by_state_div" style="<?= ($is_assign_by_state=="Y") ? '' : 'display: none'; ?>">
                  <div id="association_state_main_div">
                    
                    <?php $count = 1; ?>
                    <?php if(!empty($associationStateRes)) { ?>
                      <?php foreach ($associationStateRes as $key => $rows) { ?>
                        <?php
                          
                          $productArray = !empty($rows['product_id']) ? explode(",", $rows['product_id']) : array();
                          $statesArray  = !empty($rows['states']) ? explode(",", $rows['states']) : array();
                          $assign_by_state_id = $count;
                        ?>
                        <div id="association_state_inner_div_<?= $assign_by_state_id ?>" class="m-b-5"data-assign_by_state_id="<?= $assign_by_state_id ?>"> 
                          <div class="col-md-12 text-right" id="remove_association_state_inner_div_<?= $assign_by_state_id ?>" data-assign_by_state_id="<?= $assign_by_state_id ?>">
                            <a href="javascript:void(0)" class="remove_association_state_inner" data-assign_by_state_id="<?= $assign_by_state_id ?>"><i class="fa fa-times fa-lg text-danger"></i></a>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <select name="association_product[<?= $assign_by_state_id ?>][]" id="association_product_<?= $assign_by_state_id ?>" 
                               class="association_product_select" multiple="multiple" data-assign_by_state_id="<?= $assign_by_state_id ?>">
                                <?php if(!empty($association_ids_products)) { ?>
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
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <select name="association_state[<?= $assign_by_state_id ?>][]" id="association_state_<?= $assign_by_state_id ?>"  class="association_state_select" multiple="multiple" data-assign_by_state_id="<?= $assign_by_state_id ?>">
                                <?php if(!empty($stateRes)){ ?>
                                  <?php foreach ($stateRes as $key => $state) {
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
                  <a href="javascript:void(0);" class="pull-right" id="add_association_state_div">+ Product</a>
                </div>
              </div>

              <div class="step_btn_wrap m-t-25 text-right">
                  <a href="javascript:void(0);" id="save_fee" class="btn btn-info">Save Fee</a>
              </div>
            </div>
         </form>
      </div>
   </div>
</div>

<div id="association_state_dynamic_div" style="display: none">
  <div id="association_state_inner_div_~number~" class="m-b-5" data-assign_by_state_id="~number~"> 
    <div class="col-md-12 text-right" id="remove_association_state_inner_div_~number~" data-assign_by_state_id="~number~" >
      <a href="javascript:void(0)" class="remove_association_state_inner" data-assign_by_state_id="~number~"><i class="fa fa-times fa-lg text-danger"></i></a>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <select name="association_product[-~number~][]" id="association_product_~number~" class="association_product_select" multiple="multiple" data-assign_by_state_id="~number~">
        </select>
        <p class="error" id="error_association_product_~number~"></p>
      </div>
    </div>

    <div class="col-md-6">
      <div class="form-group">
        <select name="association_state[-~number~][]" id="association_state_~number~" class="association_state_select" multiple="multiple" data-assign_by_state_id="~number~">
          <?php if(!empty($stateRes)){ ?>
            <?php foreach ($stateRes as $key => $state) {
              $state_name=$state['name']; 
              $state_short_name=$state['short_name'];
            ?>
              <option value="<?= $state_name ?>"><?=  $state_short_name.', '.$state_name ?></option>
            <?php } ?>
          <?php } ?>
        </select>
        <p class="error" id="error_association_state_~number~"></p>
      </div>
    </div>

    <div class="clearfix"></div>
  </div>
  
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $("#manage_fees .association_state_select").multipleSelect({
      filter: true,
      width: '100%',
      placeholder:"Select State(s)",
    });
    $("#manage_fees .association_product_select").multipleSelect({
      filter: true,
      width: '100%',
      placeholder:"Select Product(s)",
    });

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
      },
      onClick:function($product){
        $product_name=$product.label;
        $product_id=$product.value;
        if($product.checked){
          manage_association_state('Add',$product_id,$product_name);
          
        }else{
          manage_association_state('Remove',$product_id,$product_name);
          
        }
      },
      onCheckAll:function(){
       $selectedProduct = $("#product").multipleSelect('getSelects');
       $.each($selectedProduct,function($k,$v){
        $product_id=$v;
        $product_name=$("#product option[value='"+$product_id+"']").text();
        manage_association_state('Add',$product_id,$product_name);
       });
      },
      onUncheckAll:function(){
        manage_association_state('Remove All',0,'');
        
      }
      
    });
  });

  
  $(document).on("change","input[name=is_fee_on_renewal]",function(){
    $val=$(this).val();

    $("#renewal_div").hide();
    if($val=="Y"){
       $("#renewal_div").show();
    }
  });
  $(document).on("change","input[name=fee_renewal_type]",function(){
    $val=$(this).val();

    $("#fee_renewal_type_div").hide();
    if($val=="Set Renewals"){
       $("#fee_renewal_type_div").show();
    }
  });
  $(document).on("change","input[name=is_fee_on_commissionable]",function(){
    $val=$(this).val();

    $(".commissionable_div").hide();
    if($val=="Y"){
       $(".commissionable_div").show();
    }
  });
  $(document).on("change","input[name=is_fee_to_association]",function(){
    $val=$(this).val();

    $(".fee_to_association_div").hide();
    if($val=="Y"){
       $(".fee_to_association_div").show();
    }
  });
  $(document).on("change","input[name=is_assign_by_state]",function(){
    $val=$(this).val();

    $("#assign_by_state_div").hide();
    if($val=="Y"){
      add_association_state_div();
      $("#assign_by_state_div").show();
    }else{
      $("#association_state_main_div").html('');
    }
  });
  $(document).on("click","#add_association_state_div",function(){
    add_association_state_div();
  });
  $(document).on("click",".remove_association_state_inner",function(){
    $number = $(this).attr('data-assign_by_state_id');
    $("#association_state_inner_div_"+$number).remove();
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
       url: 'ajax_association_add.php',
       type: 'POST',
       dataType: 'json',
       success: function (res) {
          $("#ajax_loader").hide();
          $('.error').html('');
          if(res.status=="success"){
            setNotifySuccess('Association added successfully');
             if($iframe==1){
                window.parent.$.colorbox.close();
             }else{
                window.location.href="associations.php";
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

  function add_association_state_div(){
    $count=$("#manage_fees .association_product_select").length;
    $number=$count+1;
    
    
    html = $('#association_state_dynamic_div').html();
    html = html.replace(/~number~/g, $number);

    $('#association_state_main_div').append(html);
   
    $("#association_state_"+$number).multipleSelect({
      filter: true,
      width: '100%',
      placeholder:"Select State(s)",
    });

    $("#association_product_"+$number).multipleSelect({
      filter: true,
      width: '100%',
      placeholder:"Select Product(s)",
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
  $('.priceControl').priceFormat({
    prefix: '',
    suffix: '',
    centsSeparator: '.',
    thousandsSeparator: ',',
    limit: false,
    centsLimit: 2,
  });
   
      /* added a function to call after AJAX
*  added support for multiple tables
*/
function initResponsiveTables() {
 tables = document.querySelectorAll(".table-responsive")
   
  
  for (t = 0; t < tables.length; ++t) {
   
    var headertext = [],     
      headers = tables[t].querySelectorAll(".table-responsive table th, table.table-responsive th"),
      tablebody = tables[t].querySelector(".table-responsive table tbody, table.table-responsive tbody");
    
    for (var i = 0; i < headers.length; i++) {
      var current = headers[i];
      headertext.push(current.textContent.replace(/\r?\n|\r/, ""));
    }
    for (var i = 0, row; row = tablebody.rows[i]; i++) {
      for (var j = 0, col; col = row.cells[j]; j++) {
        col.setAttribute("data-th", headertext[j]);
      }
    }
  }
}
initResponsiveTables();
</script>