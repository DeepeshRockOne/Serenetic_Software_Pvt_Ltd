<?php if($is_ajaxed){ ?>
<div class="panel">
   <div class="table-responsive">
      <table class="<?=$table_class?>">
         <thead>
            <tr>
               <th>Added Date</th>
               <th>ID/Group Name</th>
               <th>Cart Option</th>
               <th>Effective Date</th>
               <th>Termination Date</th>
               <th>Actions</th>
            </tr>
         </thead>
         <tbody>
            <?php if(!empty($total_rows)){
                  foreach($fetch_rows as $rows) {              
                  ?>
               <tr>
                  <td>
                     <?= displayDate($rows['created_at']); ?>
                  </td>
                  <td>
                     <a href='javascript:void(0);' class='text-action fw500'><?=$rows['rep_id']?></a><br>
                     <?= $rows['business_name'] ?>
                  </td>
                  <?php  $cart_type = "" ;
                     if($rows['cart_type'] == "cart_only"){
                        $cart_type = "Cart Only";  
                     }else if($rows['cart_type'] == "both"){
                        $cart_type = "Both";
                     }
                  ?>
                  <td><?= $cart_type ?></td>
                  <td><?= $rows['effective_date'] ?></td>
                  <td><?= $rows['termination_date'] ?></td>
                  <td class='icons'>
                     <a href="manage_cart_variation.php?id=<?=$rows['id']?>" data-toggle='tooltip' title='Edit'><i class='fa fa-edit' aria-hidden='true'></i></a>
                     <a href='javascript:void(0);' data-toggle='tooltip' data-id="<?= $rows['id'] ?>" title='Delete' id='deleteCart'><i class='fa fa-trash' aria-hidden='true'></i></a>
                  </td>
               </tr>
               <?php } ?>
            <?php }else{ ?>
               <tr>
                  <td colspan="6" class="text-center">No Record(s) found</td>
               </tr>
            <?php } ?>
         </tbody>
         <tfoot>
            <tr>
               <?php if ($total_rows > 0) {?> 
               <td colspan="6">
                  <?php echo $paginageLinks; ?>
               </td>
               <?php }?>
            </tr>
         </tfoot>
      </table>
   </div>
</div>
<?php }else{ ?>
<div class="panel panel-default">
   <div class="panel-heading">
      <h4 class="panel-title">Group Enrollment Settings</h4>
   </div>
   <ul class="nav nav-tabs tabs customtab">
      <li class="active"><a data-toggle="tab" href="#cart_option">Cart Options</a></li>
      <li><a data-toggle="tab" href="#category_enroll_order" data-tab_name="category_enroll_order">Category Enrollment Order</a></li>
      <li><a data-toggle="tab" href="#bundle_builder">Bundle Recommendation Builder</a></li>
   </ul>
   <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
   <input type="hidden" name="pages" id="perPages" value="<?=$per_page;?>" />
   <input type="hidden" name="global_id" id="global_id" value="<?=$global_id;?>" />
   <div class="tab-content">
      <div id="cart_option" class="tab-pane fade in active">
         <div class="panel-body">
            <h4 class="m-t-0">Global Cart Settings</h4>
            <div class="row theme-form">
               <div class="col-lg-3">
                  <p class="m-b-15"><i class="fa fa-check-circle fa-lg" aria-hidden="true"></i> Default Cart</p>
                  <div class="bg_light_gray p-10 text-center b-all">
                     <h4 class="m-t-20">Cart</h4>
                     <p class="m-b-30">Enrollee cost per day period (Bi-Monthly)</p>
                     <div class="form-group height_auto">
                        <select class="form-control">
                           <option>Member Only</option>
                        </select>
                        <label>Dental Elite 1500</label>
                     </div>
                     <h4 class="fs16 m-t-0"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i> Total</h4>
                     <h2>$18.95/<span class="fs14">pay period</span></h2>
                  </div>
               </div>
               <div class="col-lg-3">
                  <div class="clearfix m-b-10">
                     <div class="pull-left">
                        <label><input type="checkbox" name="pay_calc" id="pay_calc" value="<?= $pay_calc ?>" <?= ($pay_calc == 'Y') ? "checked" : ""; ?> >Take Home Pay Calculator</label>
                     </div>
                     <div class="pull-right">
                        <i class="fa fa-info-circle fa-lg" data-toggle="tooltip" data-container="body" title="Explanation for what the tax calculator is/does…"></i>
                     </div>
                  </div>
                  <div class="p-10 text-center b-all">
                     <h4 class="m-t-20 m-b-20">Take Home Pay</h4>
                     <div class="home_pay_amt">
                        <div class="div_table">
                           <div class="table_row">
                              <div class="table_cell">
                                 <div class="bg-success p-15 text-left">
                                    <h2 class="mn text-white">$3,400.00</h2>
                                 </div>
                              </div>
                              <div class="table_cell">
                                 <div class="bg_light_success p-20">
                                    <i class="fa fa-eye fs24" aria-hidden="true"></i>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div id="preview_div" style="display:none" class="clearfix m-t-15 text-center">
                     <a href="preview_details.php" class="btn btn-info preview_defaults" data-toggle="tooltip" data-placement="bottom" title="Preview/Set Defaults">Preview/Set Defaults</a>
                  </div>
               </div>
               <div id="calc_setting_div" style="display:none" class="col-lg-6">
                  <p>Take Home Pay Calculator Settings</p>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                              <div class="pr">
                                 <input  type="text" class="form-control date_picker" id="effectiveDate" name="effective_date" value="<?= $effective_date ?>">
                                 <label class="label-wrap">Effective Date (MM/DD/YYYY)</label>
                              </div>
                           </div>
                           <p class="error" id="error_effectiveDate"></p>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                              <div class="pr">
                                 <input  type="text" class="form-control date_picker" id="terminationDate" name="termination_date" value="<?= $termination_date ?>">
                                 <label class="label-wrap">Termination Date (MM/DD/YYYY)</label>
                              </div>
                           </div>
                           <p class="error" id="error_terminationDate"></p>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-sm-12">
                        <div class="form-inline">
                           <div class="form-group v-align-top height_auto">
                              <label class="mn">Cart Option </label>
                              <p class="error" id="error_cartType"></p>
                           </div>
                           <div class="form-group v-align-top height_auto">
                              <label class="mn">
                              <input type="radio" name="cart_type" value="cart_only" <?= ($cart_type == 'cart_only') ? "checked" : "" ?>>Cart Only
                              </label>
                           </div>
                           <div class="form-group v-align-top height_auto">
                              <label class="mn">
                              <input type="radio" name="cart_type" value="both" <?= ($cart_type == 'both') ? "checked" : "" ?>>Both
                              </label>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="text-center">
                     <a href="javascript:void(0);" id="save_btn" class="btn btn-action">Save</a>
                     <a href="javascript:void(0);" class="btn red-link" id="btn_cancel_cart_option">Cancel</a>
                  </div>
               </div>
            </div>
            <hr />
            <div class="clearfix tbl_filter">
               <div class="pull-left">
                  <h4 class="m-t-7">Variation Cart Settings</h4>
               </div>
               <div class="pull-right">
                  <a href="javascript:void(0);" class="search_btn" id="cart_search"><i class="fa fa-search fa-lg text-blue"></i></a>
                  <a href="manage_cart_variation.php" class="btn btn-action m-l-5" style="display:inline-block;" >+ Variation</a>
               </div>
               <div class="clearfix"></div>
                <div class="d-block m-b-10 m-t-10">
                  <div class="note_search_wrap  cart_search_div" id="cart_search_div" style="display: none; max-width: 100%;">
                     <div class="row theme-form">
                        <div class="col-md-1 text-right">
                           <div class="form-group height_auto">
                              <a href="javascript:void(0);" id="cart_search_close" class="search_close_btn text-light-gray">X</a>
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group height_auto mn">
                              <select class="form-control" id="cartType">
                                 <option value=""></option>
                                 <option value="cart_only">Cart Only</option>
                                 <option value="both">Both</option>
                              </select>
                              <label>Cart Option</label>
                           </div>
                        </div>
                        <div class="col-md-7">
                           <div class="row">
                              <div id="date_range" class="col-md-12">
                                 <div class="form-group height_auto mn">
                                    <select class="form-control" id="joinRange" name="join_range">
                                       <option value=""> </option>
                                       <option value="Range">Range</option>
                                       <option value="Exactly">Exactly</option>
                                       <option value="Before">Before</option>
                                       <option value="After">After</option>
                                     </select>
                                    <label>Added Date</label>
                                 </div>
                              </div>   
                              <div class="select_date_div col-md-9" style="display:none">
                                <div class="form-group">
                                  <div id="all_join" class="input-group"> 
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" name="added_date" id="addedDate" value="" class="form-control date_picker" />
                                  </div>
                                  <div  id="range_join" style="display:none;">
                                    <div class="phone-control-wrap">
                                      <div class="phone-addon">
                                        <label class="mn">From</label>
                                      </div>
                                      <div class="phone-addon">
                                        <div class="input-group"> 
                                          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                          <div class="pr">
                                             <input type="text" name="fromdate" id="fromDate" value="" class="form-control date_picker" />
                                          </div>
                                        </div>
                                      </div>
                                      <div class="phone-addon">
                                        <label class="mn">To</label>
                                      </div>
                                      <div class="phone-addon">
                                        <div class="input-group"> 
                                          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                          <div class="pr">
                                             <input type="text" name="todate" id="toDate" value="" class="form-control date_picker" />
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                           </div>   
                        </div>
                        <div class="col-md-2">
                           <div class="form-group height_auto">
                              <a href="javascript:void(0);" class="btn btn-info btn-block search_button" id="variation_search_button">Search</a>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
           
            <div id="ajax_data_variation"></div>
            
         </div>
      </div>
      <div id="category_enroll_order" class="tab-pane fade ">
         <div class="advance_info_div">
            <div class="panel-body">
               <div class="phone-control-wrap ">
                  <div class="phone-addon w-90">
                     <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="80px">
                  </div>
                  <div class="phone-addon text-left">
                     <div class="info_box">
                        <h4 class="m-t-0">Category Enrollment Order</h4>
                        <p class="fs14 mn">Drag records up and down using the action icon <i class="fa fa-ellipsis-v fs24 v-align-middle m-l-5"></i><i class="fa fa-ellipsis-v fs24 v-align-middle m-r-5"></i> to determine the product order of each category. The category in slot #1 will show up first on enrollment.</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>   
            <div class="panel-body">
               <div class="clearfix tbl_filter">
                  <div class="pull-left">
                     <h4 class="m-t-7">Categories</h4>
                  </div>
                  <div class="pull-right">
                     <div class="m-b-15">
                        <div class="note_search_wrap auto_size" id="cat_search_div" style="display: none; max-width: 100%;">
                           <div class="phone-control-wrap theme-form">
                              <div class="phone-addon">
                                 <div class="form-group height_auto mn">
                                    <a href="javascript:void(0);" id="cat_search_close" class="search_close_btn text-light-gray">X</a>
                                 </div>
                              </div>
                              <div class="phone-addon w-200">
                                 <div class="form-group height_auto mn">
                                    <input type="text" class="form-control" id="search_input">
                                    <label>Categories</label>
                                 </div>
                              </div>
                              <div class="phone-addon w-80">
                                 <div class="form-group height_auto mn">
                                    <a href="javascript:void(0);" class="btn btn-info search_button submit_search_btn">Search</a>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <a href="javascript:void(0);" class="search_btn" id="cat_search"><i class="fa fa-search fa-lg text-blue"></i></a>
                     </div>
                  </div>
               </div>
               <div class="category_order roboto_font">
                  <div class="category_block">
                     <div class="div_table " id="moveable"></div>
                     <div id="add_category_link_div" class="category_block_add table_dark_primary p-10">
                        <a href="javascript:void(0);" class="btn text-action" data-toggle="tooltip" id="btn_add_new_prd_cat" title="+ Product Category" data-placement="bottom">+ Product Category</a>
                     </div>
                     <div id="product_category_div" class="category_block_add table_dark_primary p-10" style="display:none;">
                        <div class="row theme-form">
                           <div class="col-sm-6">
                              <div class="form-group height_auto mn">
                                 <select id="product_category_select" class="form-control" data-live-search="true">
                                 </select>
                                 <label>Select Product Category</label>
                                 <p class="error" id="error_product_category_select"></p>
                              </div>
                           </div>
                           <div class="col-sm-6">
                              <div class="m-t-7">
                                 <a href="javascript:void(0);" class="m-r-10" id="btn_add_category" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Confirm"><i class="fa fa-check-circle-o fa-lg" aria-hidden="true"></i></a>
                                 <a href="javascript:void(0);" class="m-r-10" id="btn_cancel_add_category" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Remove"><i class="fa fa-times-circle-o fa-lg" aria-hidden="true"></i></a>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="m-t-20 text-center">
                  <a href="javascript:void(0);" class="btn btn-action" id="btn_save_category_order">Save</a>
                  <a href="javascript:void(0);" class="btn red-link" id="btn_cancel_save_category_order">Cancel</a>
               </div>
            </div>
      </div>
      <div id ="bundle_builder" class="tab-pane fade">
         <?php include ('bundle_recommendations_tab.inc.php'); ?>

      </div>
   </div>
</div>
<div id="template_category_row" class="hidden">
   <div class="~category_row~ table_row br-t br-b" id="category_row_~cat_id~">
      <div class="table_cell">
         <div class="bg_dark_primary p-10 text-center text-white category_order">~category_order~</div>
      </div>
      <div class="table_cell">
         <div class="p-5 w-30 text-center">
            <div class="move_controller text-nowrap" data-toggle="tooltip" data-placement="bottom" title="Move">
               <i class="fa fa-ellipsis-v fa-lg"></i>
               <i class="fa fa-ellipsis-v fa-lg"></i>
            </div>
         </div>
      </div>
      <div class="table_cell category_title">~category_title~</div>
      <div class="table_cell text-right">
         <div class="p-10">
            <a href="javascript:void(0);" class="text-action btn_delete_category" data-category_id="~cat_id~">
            <i class="fa fa-trash fa-lg" data-toggle="tooltip" data-placement="top" title="Delete"></i>
            </a>
         </div>
      </div>
   </div>
</div>
<script type="text/javascript">
$(document).off("click", "#cart_search, #cat_search, #bundle_search");
	$(document).on("click", "#cart_search, #cat_search, #bundle_search", function(e) {
	  e.preventDefault();
	  $(this).hide();
	  $("#cart_search_div, #cat_search_div, #bundle_search_div").css('display', 'inline-block');
});

$(document).off("click", "#cart_search_close, #bundle_search_close");
	$(document).on("click", "#cart_search_close, #bundle_search_close", function(e) {
	  e.preventDefault();
	  $("#cart_search_div, #cat_search_div, #bundle_search_div").hide();
	  $("#cart_search, #cat_search, #bundle_search").show();
     getVariationCartSettings();
});
var group_prd_categories = [];
var categories_res = [];
var category_order_data_fetched = false;
var updated_by = <?=$_SESSION['admin']['id']?>;
$(document).ready(function(){

   $(document).off("click", "#btn_add_new_prd_cat");
   $(document).on("click", "#btn_add_new_prd_cat", function(e) {
      $("#product_category_div").show();
      $("#add_category_link_div").hide();
   });

   $(document).off("click", "#btn_cancel_save_category_order");
   $(document).on("click", "#btn_cancel_save_category_order", function(e) {
      window.location.reload();
   });

   $(document).off("click", "#btn_cancel_cart_option");
   $(document).on("click", "#btn_cancel_cart_option", function(e) {
      window.location.reload();
   });

   $(document).off("click", "#btn_save_category_order");
   $(document).on("click", "#btn_save_category_order", function(e) {
      e.preventDefault();
      $("#ajax_loader").show();
      $("#error_product_category_select").html("");
      let formData = [];
      formData.push({'name':'api_key','value':'saveGroupProductCategoryOrder'});
      formData.push({'name':'updated_by','value':updated_by});
      var category_data = $("#moveable").sortable("toArray");
      category_data = category_data.toString();
      category_data = category_data.replace(/category_row_/g,'');
      formData.push({'name':'category_data','value':category_data});
      $.ajax({
         url: '<?=$HOST?>' + '/ajax_api_call.php',
         data:formData,
         dataType: 'json',
         type: 'POST',
         success: function(res) {
            $("#ajax_loader").hide();
            if(res.status == "Success") {
               setNotifySuccess(res.message);
            }
            if(res.status == "Error") {
               var error_data = res.data;
               $.each(error_data,function(field,field_errs){
                  $.each(field_errs,function(err_idx,err_str){
                     var error_html = err_str + "<br/>";
                     $("#error_product_category_select").append(error_html);
                  });
               });
            }
         }
      });
   });

   $(document).off("click", "#cat_search_close");
   $(document).on("click", "#cat_search_close", function(e) {
      e.preventDefault();
      $("#cat_search_div").hide();
      $("#cat_search").show();

      $("#search_input").val('');
      var value = $("#search_input").val();
      $("div#moveable .category_row").each(function(index,element){
         $(this).filter(function() {
            $(this).toggle($(this).find("div.category_title").text().toLowerCase().indexOf(value) > -1);
         });
      });
   });

   $(document).off("click", ".submit_search_btn");
   $(document).on("click", ".submit_search_btn", function(e) {
      var value = $("#search_input").val().toLowerCase();
      $("div#moveable .category_row").each(function(index,element){
         $(this).filter(function() {
            $(this).toggle($(this).find("div.category_title").text().toLowerCase().indexOf(value) > -1);
         });
      });            
   });

   $(document).off("click","#btn_add_category");
   $(document).on("click","#btn_add_category",function(){
      var category_id = $("#product_category_select").val();
      $("#error_product_category_select").html("");
      if(category_id == "") {
         $("#error_product_category_select").html("Select category");
         return false;
      }
      var category_title = $("select#product_category_select option[value='"+category_id+"']").html();
      add_category_row(category_id,category_title);
      category_id = parseInt(category_id);
      group_prd_categories.push(category_id);
      update_prd_category_select();
      $("#product_category_select").closest('.bs3.has-value').removeClass('has-value');
      $("#product_category_div").hide();
      $("#add_category_link_div").show();
      $('[data-toggle="tooltip"]').tooltip();
   });

   $(document).off("click","#btn_cancel_add_category");
   $(document).on("click","#btn_cancel_add_category",function(){
      $("#product_category_select").val("");
      $("#product_category_select").closest('.bs3.has-value').removeClass('has-value');
      $("#product_category_select").selectpicker('refresh');
      $("#product_category_div").hide();
      $("#add_category_link_div").show();
      $('[data-toggle="tooltip"]').tooltip();
   });

   $(document).off("click",".btn_delete_category");
   $(document).on("click",".btn_delete_category",function(){
      var category_id = $(this).attr('data-category_id');
      swal({
         text: "<br>Delete Category: Are you sure?",
         showCancelButton: true,
         confirmButtonText: "Confirm",
      }).then(function() {
         $("#category_row_" + category_id).remove();
         category_id = parseInt(category_id);
         group_prd_categories.splice($.inArray(category_id,group_prd_categories),1);
         update_prd_category_select();
         update_category_order();
      }, function(dismiss) {
      
      });
   });

   $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
      var tab_name = $(e.target).attr('data-tab_name');
      if(tab_name == "category_enroll_order") {
         if(category_order_data_fetched == false) {
            $("#ajax_loader").show();
            let formData = [];
            formData.push({'name':'api_key','value':'getGroupProductCategoryList'});
            $.ajax({
               url: '<?=$HOST?>' + '/ajax_api_call.php',
               data:formData,
               dataType: 'json',
               type: 'POST',
               success: function(res) {
                  category_order_data_fetched = true;
                  $("#ajax_loader").hide();
                  if(res.status == "Success") {
                     var data_obj = res.data;
                     var GroupProductCategoryRes = data_obj.GroupProductCategoryRes;
                     $.each(GroupProductCategoryRes,function(index,value){
                        add_category_row(value.category_id,value.category);
                     });
                     group_prd_categories = data_obj.GroupProductCategoryIDs;
                     categories_res = data_obj.ProductCategoryRes;
                     update_prd_category_select();
                  }
                  $('[data-toggle="tooltip"]').tooltip();
               }
            });
         }
      }
   });
   $('#moveable').sortable({
      axis: 'y',
      //helper: fixHelper,
      cursor: 'move',
      items: '.category_row',
      placeholder: '.category_row',
      handle: '.move_controller',
      update: function( event, ui ) {
         update_category_order();
      }
   });
  autoResizeNav();
  $(".date_picker").datepicker({
    changeDay: true,
    changeMonth: true,
    changeYear: true
  });

   if($("#pay_calc").val() == "Y"){
      $("#preview_div").show();
      $("#calc_setting_div").show();
   }else{
      $("#preview_div").hide();
      $("#calc_setting_div").hide();
   }

   $(document).off('change', '#joinRange');
   $(document).on('change', '#joinRange', function(e) {
       e.preventDefault();
       $('.date_picker').val('');
       if ($(this).val() == '') {
           $('.select_date_div').hide();
           $('#date_range').removeClass('col-md-3').addClass('col-md-12');
       } else {
           $('#date_range').removeClass('col-md-12').addClass('col-md-3');
           $('.select_date_div').show();
           if ($(this).val() == 'Range') {
               $('#range_join').show();
               $('#all_join').hide();
           } else {
               $('#range_join').hide();
               $('#all_join').show();
           }
       }
   });

   var cartType = $("#cartType").val();
   var join_range = $("#joinRange").val();
   var added_date = $("#addedDate").val();
   var fromdate = $("#fromDate").val();
   var todate = $("#toDate").val();
   var is_ajaxed = $('#is_ajaxed').val();
   var perPages = $("#perPages").val();

   data = {
      api_key : 'variationCartSettings',
      cartType : cartType,
      joinRange : join_range,
      addedDate : added_date,
      fromDate : fromdate,
      toDate : todate,
      is_ajaxed : is_ajaxed,
      perPages : perPages
   };
   
   api_dropdown_pagination('ajax_data_variation',data);


});
$(document).off('click', '.preview_defaults');
  $(document).on('click', '.preview_defaults', function (e) {
    e.preventDefault();
    $.colorbox({
      href: $(this).attr('href'),
      iframe: true, 
      width: '1050px', 
      height: '600px',
      closeButton: false
    });
  });
  function autoResizeNav(){
   if ($('.nav-tabs:not(.nav-noscroll)').length){
      ;(function() {
        'use strict';
         $(activate);
         function activate() {
         $('.nav-tabs:not(.nav-noscroll)')
           .scrollingTabs({
               scrollToTabEdge: true,
               enableSwiping: true  
            })
        }
      }());
   }
}

  $("#pay_calc").off("change");
  $("#pay_calc").on("change",function(){
      $("#preview_div").hide();
      $("#calc_setting_div").hide();
      if(this.checked){
         $("#preview_div").show();
         $("#calc_setting_div").show();
      }
  });

  $("#save_btn").off("click");
  $("#save_btn").on("click",function(){
      var global_id = $("#global_id").val();
      var effectiveDate = $("#effectiveDate").val();
      var terminationDate = $("#terminationDate").val();
      var cartType = $("input[name=cart_type]:checked").val();
      $('#ajax_loader').show();
      $(".error").html("");
      $.ajax({
         url : '<?=$HOST?>/ajax_api_call.php' ,
         type : 'POST',
         data : {
                  api_key : 'cartSetting',
                  effectiveDate : effectiveDate,
                  terminationDate : terminationDate,
                  id : global_id,
                  type : 'Global',
                  payCalc : 'Y',
                  cartType : cartType
                  },
         dataType : 'json',
         success: function(res){
            $('#ajax_loader').hide();
            if(res.status=="Success"){
               setNotifySuccess(res.message);
               window.location.reload();
            }else if(res.status == 'fail'){
               setNotifyError(res.message);
               window.location.reload();
            }else{
               var is_error = true;
               $.each(res.data, function (index, value) {
                $('#error_' + index).html(value).show();
                if(is_error){
                    var offset = $('#error_' + index).offset();
                    var offsetTop = offset.top;
                    var totalScroll = offsetTop - 150;
                    $('body,html').animate({scrollTop: totalScroll}, 1200);
                    is_error = false;
                }
               });
            }
         }
      })
  });

  $("#variation_search_button").off('click');
  $("#variation_search_button").on('click',function(){
      var cart_type = $("#cartType").val();
      var join_range = $("#joinRange").val();
      var added_date = $("#addedDate").val();
      var fromdate = $("#fromDate").val();
      var todate = $("#toDate").val();

      var search = false;
      if(cart_type != ""){
         search = true;
      }
      if(join_range != ""){
         if(join_range == "Range" &&  fromdate != "" && todate != ""){
            search = true;
         }
         if(join_range != "Range" && added_date != ""){
            search = true;
         }
      }

      if(search){
         getVariationCartSettings(cart_type,join_range,added_date,fromdate,todate);
      }else{
         swal({
           text: '<br>Error: Data entry missing',
           showCancelButton: true,
           showConfirmButton: false,
           cancelButtonText: 'Close'
         });
      }
  });

$(document).off('click', '#ajax_data_variation ul.pagination li a');
$(document).on('click', '#ajax_data_variation ul.pagination li a', function(e) {
   e.preventDefault();
   $('#ajax_loader').show();
   var cartType = $("#cartType").val();
   var join_range = $("#joinRange").val();
   var added_date = $("#addedDate").val();
   var fromdate = $("#fromDate").val();
   var todate = $("#toDate").val();

      var is_ajaxed = $('#is_ajaxed').val();
      var perPages = $("#perPages").val();
      var page = $(this).attr('data-page');
      $("#page").val(page);
      $.ajax({
         url: $(this).attr('href'),
         data:{
            api_key : 'variationCartSettings',
            cartType : cartType,
            joinRange : join_range,
            addedDate : added_date,
            fromDate : fromdate,
            toDate : todate,
            is_ajaxed : is_ajaxed,
            perPages : perPages,
            page : page
         },
         type: 'POST',
         success: function(res) {
            $('#ajax_loader').hide();
            $('#ajax_data_variation').html(res).show();
            $('[data-toggle="tooltip"]').tooltip();
            common_select();
            $("input[type='checkbox']").not('.js-switch').uniform();
         }
      });
   });
function update_prd_category_select()
{
   var cat_html = '<option value=""></option>';
   if(categories_res.length > 0){
      $.each(categories_res,function(index,value){
         var category_id = parseInt(value.category_id); 
         if($.inArray(category_id,group_prd_categories) === -1) {
            cat_html += '<option value="' + category_id + '">' + value.category + '</option>'; 
         }
      });
   }
   $("#product_category_select").html(cat_html);
   $("#product_category_select").selectpicker('refresh');
   $("#product_category_select").closest('.bs3.has-value').removeClass('has-value');
   $("#product_category_select").selectpicker('refresh');
}
function add_category_row(category_id,category_title)
{
   var category_order = $(".category_row").length + 1;
   var category_row_html = $('#template_category_row').html();
   category_row_html = category_row_html.replace(/~category_row~/g,'category_row');
   category_row_html = category_row_html.replace(/~cat_id~/g, category_id);
   category_row_html = category_row_html.replace(/~category_title~/g, category_title);
   category_row_html = category_row_html.replace(/~category_order~/g, category_order);
   $("#moveable").append(category_row_html);
}

function update_category_order() 
{
   var category_order = 1;
   $("div.category_row").each(function(index,ele){
      $(this).find('div.category_order').html(category_order);
      category_order++;
   });
}  
</script>
<?php } ?>