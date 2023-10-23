<?php if ($is_ajaxed) { ?>
<div class="clearfix tbl_filter">
   <?php if ($total_rows > 0) {?>
   <div class="pull-left">
      <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
         <div class="form-group mn">
            <label for="user_type">Records Per Page </label>
         </div>
         <div class="form-group mn">
            <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);ajax_submit();">
               <option value="10" <?php echo isset($_GET['pages']) && $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
               <option value="25" <?php echo (isset($_GET['pages']) && $_GET['pages'] == 25) || (isset($_GET['pages']) && empty($_GET['pages'])) ? 'selected' : ''; ?>>25</option>
               <option value="50" <?php echo isset($_GET['pages']) && $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
               <option value="100" <?php echo isset($_GET['pages']) && $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
            </select>
         </div>
      </div>
   </div>
   <?php } ?>
   <div class="pull-right">
      <div class="m-b-15"><a class="btn btn-action" href="add_providers.php">+ Provider</a></div>
   </div>
</div>
<div class="table-responsive">
   <table class="<?=$table_class?>">
      <thead>
         <tr class="data-head">
            <th>ID/Added Date</th>
            <th>Provider Name</th>
            <th class="text-center">Products #</th>
            <th width="15%">Status</th>
            <th width="90px">Actions</th>
         </tr>
      </thead>
      <tbody>
         <?php if ($total_rows > 0) { ?>
         <?php foreach ($fetch_rows as $rows) { ?>
         <tr>
            <td>
               <a href="add_providers.php?providers_id=<?=md5($rows['id'])?>" class="fw600 text-red"><?=$rows['display_id']?></a><br />
               <?=date($DATE_FORMAT, strtotime($rows['created_at']))?>
            </td>
            <td><strong><?=$rows['name']?></strong></td>
            <td class="text-center">
               <a href="providers_prd_popup.php?id=<?=md5($rows['id'])?>" class="fw600 text-red providers_prd_popup"><?=$rows['prd_total']?></a>
            </td>
            <td>
               <div class="theme-form w-130 pr">
                  <select id="is_active" class="status_s form-control has-value" name="is_active" onchange="confirm_active('<?= md5($rows['id']); ?>', this.value)">
                     <option value="Active" <?php echo ($rows['status'] == "Active")?'selected':''?>>Active</option>
                     <option value="Inactive" <?php echo ($rows['status'] == "Inactive")?'selected':''?>>Inactive</option>
                  </select>
                  <label>Select</label>
               </div>
            </td>
            <td class="icons ">
               <a href="add_providers.php?providers_id=<?=md5($rows['id'])?>" data-toggle="tooltip" data-trigger="hover" title="View Provider" data-placement="top"><i class="fa fa-edit"></i></a>
               <a href="javascript:void(0);" data-toggle="tooltip" data-trigger="hover" title="Delete" data-placement="top" onclick="delete_provider('<?= md5($rows['id']); ?>')"><i class="fa fa-trash"></i></a>
            </td>
         </tr>
         <?php } ?>
         <?php } else { ?>
         <tr>
            <td colspan="5">No record(s) found</td>
         </tr>
         <?php } ?>
      </tbody>
      <?php if ($total_rows > 0) { ?>
      <tfoot>
         <tr>
            <td colspan="5">
               <?php echo $paginate->links_html; ?>
            </td>
         </tr>
      </tfoot>
      <?php }?>
   </table>
</div>
<?php }else{ ?>
<div class="panel panel-default panel-block panel-title-block">
   <form id="frm_search" action="providers.php" method="GET" autocomplete="off">
      <div class="panel-left">
         <div class="panel-left-nav">
            <ul>
               <li class="active"><a href="javascript:void(0);"><i class="fa fa-search"></i></a></li>
            </ul>
         </div>
      </div>
      <div class="panel-right">
         <div class="panel-heading">
            <div class="panel-search-title">
               <span class="clr-light-blk">SEARCH</span>
            </div>
         </div>
         <div class="panel-wrapper collapse in">
            <div class="panel-body theme-form">
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <input type="text" class="form-control listing_search" name="provider_id" value="<?=!empty($provider_id) ? $provider_id : '' ?>"/>
                        <label>ID Number(s)</label>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="row">
                        <div id="date_range" class="col-md-12">
                           <div class="form-group">
                              <select class="form-control" id="join_range" name="join_range">
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
                                 <input type="text" name="added_date" id="added_date" value="" class="form-control date_picker" />
                              </div>
                              <div id="range_join" style="display:none;">
                                 <div class="phone-control-wrap">
                                    <div class="phone-addon">
                                       <label class="mn">From</label>
                                    </div>
                                    <div class="phone-addon">
                                       <div class="input-group"> <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                          <input type="text" name="fromdate" id="fromdate" value="" class="form-control date_picker" />
                                       </div>
                                    </div>
                                    <div class="phone-addon">
                                       <label class="mn">To</label>
                                    </div>
                                    <div class="phone-addon">
                                       <div class="input-group"> <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                          <input type="text" name="todate" id="todate" value="" class="form-control date_picker" />
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="form-group">
                        <input type="text" class="form-control listing_search" name="provider_name" value="<?=!empty($provider_name)?>" />
                        <label>Provider Name</label>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="form-group ">
                           <select name="provider_product[]" class="se_multiple_select" multiple="multiple">
                              <?php if(!empty($company_arr) && count($company_arr) > 0){
                                 foreach ($company_arr as $key=>$company) { ?>
                              <optgroup label="<?= $key ?>">
                                 <?php foreach ($company as $pkey =>$row) { ?>
                                 <option value="<?= $row['id'] ?>" <?=(!empty($provider_product) && in_array($row['id'], $provider_product)) ? 'selected="selected"' : '' ?>><?= $row['name'] . ' (' . $row['product_code'] . ')' ?></option>
                                 <?php } ?>
                              </optgroup>
                              <?php }
                                 } ?> 
                           </select>
                           <label>Products</label>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="form-group">
                        <select  class="form-control listing_search" name="provider_status" >
                           <option value=""></option>
                           <option value="Active" <?=(!empty($provider_status) && $provider_status == 'Active') ? 'selected="selected"' : '' ?>>Active</option>
                           <option value="Inactive" <?=(!empty($provider_status) && $provider_status == 'Inactive') ? 'selected="selected"' : '' ?>>Inactive</option>
                        </select>
                        <label>Status </label>
                     </div>
                  </div>
               </div>
               <div class="panel-footer clearfix">
                  <button type="button" class="btn btn-info" onclick="ajax_submit();"> <i class="fa fa-search"></i> Search </button>
                  <button type="button" class="btn btn-info btn-outline" onclick="window.location = 'providers.php'"> <i class="fa fa-search-plus"></i> View All</button>
                  <button type="button" class="btn red-link" name="provider_export" id="provider_export"> <i class="fa fa-download"></i> Export</button>
                  <input type="hidden" name="export" id="export" value=""/>
                  <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
                  <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
                  <input type="hidden" name="page" id="nav_page" value="" />
                  <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
                  <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
               </div>
            </div>
         </div>
      </div>
   </form>
   <div class="search-handle">
      <a href="#" data-perform="panel-collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></a>
   </div>
</div>
<div class="panel panel-default panel-block">
   <div class="list-group">
      <div id="ajax_loader" class="ajex_loader" style="display: none;">
         <div class="loader"></div>
      </div>
      <div id="ajax_data" class="list-group-item"> </div>
   </div>
</div>
<script type="text/javascript">
   $(document).ready(function() {
      dropdown_pagination('ajax_data')
     $(".date_picker").datepicker({
       changeDay: true,
       changeMonth: true,
       changeYear: true
     });
   
     $(".se_multiple_select").multipleSelect({
    
    });
   
     $(document).keypress(function (e) {
       if (e.which == 13) {
         ajax_submit();
       }
     });
   
     ajax_submit();
   });
   
   $(document).off('change', '#join_range');
   $(document).on('change', '#join_range', function(e) {
     e.preventDefault();
     $('.date_picker').val('');
     if($(this).val() == ''){
       $('.select_date_div').hide();
       $('#date_range').removeClass('col-md-3').addClass('col-md-12');
     }else{
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

   $(document).off('click', '#provider_export');
   $(document).on('click', '#provider_export', function(e) {
      confirm_export_data(function() {
          $("#export").val('provider_export');
          $('#ajax_loader').show();
          $('#is_ajaxed').val('1');
          var params = $('#frm_search').serialize();
          $.ajax({
              url: $('#frm_search').attr('action'),
              type: 'GET',
              data: params,
              dataType: 'json',
              success: function(res) {
                  $('#ajax_loader').hide();
                  $("#export").val('');
                  if(res.status == "success") {
                      confirm_view_export_request();
                  } else {
                      setNotifyError(res.message);
                  }
              }
          });
      });
   });
   
   $(document).off("submit", "#frm_search");
   $(document).on("submit", "#frm_search", function(e) {
     $('#nav_page').val(1);
     e.preventDefault();
     disable_search();
   });
   
   $(document).off('click', '#ajax_data ul.pagination li a');
   $(document).on('click', '#ajax_data ul.pagination li a', function(e) {
     e.preventDefault();
     $('#ajax_loader').show();
     $('#ajax_data').hide();
     $.ajax({
       url: $(this).attr('href'),
       type: 'GET',
       success: function(res) {
         $('#ajax_loader').hide();
         $('#ajax_data').html(res).show();
         common_select();
       }
     });
   });
   
   $(document).off('click', '.providers_prd_popup');
   $(document).on('click', '.providers_prd_popup', function (e) {
     e.preventDefault();
     $.colorbox({
       href: $(this).attr('href'),
       iframe: true, 
       width: '770px', 
       height: '500px'
     });
   });
   
   
   // $(document).off('click', '#export_admin');
   // $(document).on('click', '#export_admin', function(e) {
   //   swal({
   //     title: 'Are you sure?',
   //     text: 'You want to export to Excel All Admins',
   //     type: 'success',
   //     showCancelButton: true,
   //     confirmButtonColor: "#0088cc",
   //     confirmButtonText: 'Yes, export it!',
   //     cancelButtonText: 'No, cancel!',
   //   }).then(function() {
   //     $("#export").val('export_admin');
   //     $('#ajax_loader').show();
   //     $('#is_ajaxed').val('1');
   //     var params = $('#frm_search').serialize();
   //     $.ajax({
   //       url: $('#frm_search').attr('action'),
   //       type: 'GET',
   //       data: params,
   //       dataType: 'json',
   //       success: function() {
   //         $('#ajax_loader').hide();
   //         $("#export").val('');
   //       }
   //     }).done(function(data) {
   //       var $a = $("<a>");
   //       $a.attr("href", data.file);
   //       $("body").append($a);
   //       $a.attr("download", "Admins_Record.xls");
   //       $a[0].click();
   //       $a.remove();
   //       $('#ajax_loader').hide();
   //     });
   //   }, function(dismiss) {
   
   //   })
   // });
   
   
   function disable_search() {
     if ($(".listing_search").filter(function() {
         return $(this).val();
       }).length > 0 || ($("#join_range").val() != '' && $(".date_picker").filter(function() {
         return $(this).val();
         0
       }).length > 0)) {
       ajax_submit();
     } else {
       swal('Oops!!', 'Please Enter Data To Search', 'error');
     }
   }
   
   function ajax_submit() {
     $('#ajax_loader').show();
     $('#ajax_data').hide();
     $('#is_ajaxed').val('1');
     $("#export").val('');
     var params = $('#frm_search').serialize();
     var cpage = $('#nav_page').val();
     $.ajax({
       url: $('#frm_search').attr('action'),
       type: 'GET',
       data: params,
       success: function(res) {
         $('#ajax_loader').hide();
         $('#ajax_data').html(res).show();
         $('[data-toggle="tooltip"]').tooltip();
         common_select();
       }
     });
     return false;
   }
   
   function confirm_active(id, val) {
     swal({
       text: '<br>Change Status: Are you sure?',
       showCancelButton: true,
       confirmButtonText: 'Confirm',
       cancelButtonText: 'Cancel',
      
     }).then(function() {
       window.location = "providers.php?pro_id=" + id + "&status=" + val;
     }, function(dismiss) {
       window.location.reload();
     });
   }
   
   function delete_provider(id){
     swal({
       text: '<br>Delete Record: Are you sure?',
       showCancelButton: true,
       confirmButtonText: 'Confirm',
       cancelButtonText: 'Cancel',
     }).then(function() {
       window.location = "providers.php?pro_id=" + id + "&is_deleted=N";
     }, function(dismiss) {
     });
   }
   
</script>
<?php }?>