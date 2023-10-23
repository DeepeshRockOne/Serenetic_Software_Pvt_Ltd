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
               <option value="25" <?php echo (isset($_GET['pages']) && $_GET['pages'] == 25) || (isset($_GET['pages']) && $_GET['pages'] == "") ? 'selected' : ''; ?>>25</option>
               <option value="50" <?php echo isset($_GET['pages']) && $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
               <option value="100" <?php echo isset($_GET['pages']) && $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
            </select>
         </div>
      </div>
   </div>
   <?php }?>
   <div class="pull-right m-b-15">
      <a class="btn btn-action" href="manage_vendor.php">+ Vendor</a>
   </div>
</div>
<div class="table-responsive">
   <table class="<?=$table_class?>">
      <thead>
         <tr>
            <th>ID/Added Date</th>
            <th>Details</th>
            <th class="text-center">Products #</th>
            <th class="text-center">Members #</th>
            <th width="15%">Status</th>
            <th width="130px">Action</th>
         </tr>
      </thead>
      <tbody>
         <?php if($total_rows > 0) { ?>
         <?php foreach ($fetch_rows as $row) { ?>
         <tr>
            <td>
               <a href="manage_vendor.php?vendor_id=<?= $row['id']; ?>" class="fw600 text-red"><?php echo $row['display_id']; ?></a><br>
               <?= date('m/d/Y',strtotime($row['created_at']))?>
            </td>
            <td>
               <strong><?php echo $row['name']; ?></strong><br />
               <?php echo ($row['phone'] ? "(". substr($row['phone'], 0,3) .") ". substr($row['phone'], 3,3) ."-".substr($row['phone'], 6,4) : "-" ); ?><br />
               <?php echo $row['email']; ?>
            </td>
            <td class="text-center">
               <a href="view_fees_details.php?vendor_id=<?= $row['id']; ?>&name=<?= $row['name'] ?>&display_id=<?= $row['display_id'] ?>&count=<?= $row['total_products'] ?>" class="fw600 text-red vendor_product_popup"><?php echo $row['total_products']; ?></a>
            </td>
            <td class="text-center"><?=$row['total_customer']?></td>
              <?php /*<a href="member_fees_popup.php?id=<?= $row['id']; ?>&name=<?= $row['name'] ?>&display_id=<?= $row['display_id'] ?>&count=<?= $row['total_customer'] ?>" class="fw600 text-red vendor_product_popup"><?php echo $row['total_customer']; ?></a>*/ ?>
            <td>
               <div class="theme-form pr w-130">
                  <select name="is_active" id="vendor_status_<?= $row['id']; ?>" class="form-control has-value vendor_fee_status" data-id="<?= $row['id']; ?>" data-old_status='<?= $row['status']; ?>'  >
                     <option value="Active" <?php echo ($row['status'] == "Active")?'selected':''?> >Active</option>
                     <option value="Inactive" <?php echo ($row['status'] == "Inactive")?'selected':''?> >Inactive</option>
                  </select>
                  <label>Select</label>
               </div>
            </td>
            <td class="icons" >
               <a href="manage_vendor.php?vendor_id=<?= $row['id']?>&is_clone=Y" title="Clone Vendor" data-toggle="tooltip" data-trigger="hover"><i class="fa fa-clone" aria-hidden="true"></i></a>
               <a href="manage_vendor.php?vendor_id=<?= $row['id']; ?>" title="Edit Vendor" data-toggle="tooltip" data-trigger="hover"><i class="fa fa-edit"></i></a>
               <a href="javascript:void(0);" class="delete_vendor_fee" data-id="<?= $row['id']; ?>" title="Delete" data-toggle="tooltip" data-trigger="hover"><i class="fa fa-trash"></i></a>
            </td>
         </tr>
         <?php } ?>
         <?php } else { ?>
         <tr>
            <td colspan="6">No Records Found</td>
         </tr>
         <?php } ?>
      </tbody>
      <?php if ($total_rows > 0) { ?>
      <tfoot>
         <tr>
            <td colspan="6">
               <?php echo $paginate->links_html; ?>
            </td>
         </tr>
      </tfoot>
      <?php } ?>
   </table>
</div>
<?php } else { ?> 
<div class="panel panel-default panel-block panel-title-block">
   <form id="frm_search" action="vendors.php" method="GET" autocomplete="off">
      <div class="panel-left">
         <div class="panel-left-nav">
            <ul>
               <li class="active"><a href="javascript:void(0);"><i class="fa fa-search"></i></a></li>
            </ul>
         </div>
      </div>
      <div class="panel-right">
         <div class="panel-heading">
            <div class="panel-search-title"> <span class="clr-light-blk">SEARCH</span></div>
         </div>
         <div class="panel-wrapper collapse in">
            <div class="panel-body theme-form">
               <div class="row">
                 <div class="col-md-6">
                     <div class="form-group ">
                           <select name="vendor_ids[]" id="vendor_ids" multiple="multiple" class="se_multiple_select listing_search">
                              <?php if(!empty($resVendor)){
                                 foreach ($resVendor as $row) {
                              ?>
                                 <option value="<?= $row['id'] ?>" <?=(!empty($vendor_ids) && in_array($row['id'], $vendor_ids)) ? 'selected="selected"' : ''?>><?=$row['display_id']?></option>
                              <?php 
                                  }
                                }
                              ?> 
                           </select>
                           <label>ID Number(s)</label>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="row" id="show_date">
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
                              <div  id="range_join" style="display:none;">
                                 <div class="phone-control-wrap">
                                    <div class="phone-addon">
                                       <label class="mn">From</label>
                                    </div>
                                    <div class="phone-addon">
                                       <div class="input-group">
                                          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                          <input type="text" name="fromdate" id="fromdate" value="" class="form-control date_picker" />
                                       </div>
                                    </div>
                                    <div class="phone-addon">
                                       <label class="mn">To</label>
                                    </div>
                                    <div class="phone-addon">
                                       <div class="input-group">
                                          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
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
                        <input type="text" class="form-control listing_search" name="vendor_name" id="vendor_name" value="">
                        <label>Vendor Name</label>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="form-group">
                        <input type="text" class="form-control listing_search" name="member_id" id="member_id" value="">
                        <label>Member ID</label>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="form-group ">
                           <select name="products[]" id="products" multiple="multiple" class="se_multiple_select listing_search">
                              <?php if(!empty($productRes)){ ?>
                              <?php foreach ($productRes as $key=>$category) { ?>
                              <?php if(!empty($category)){ ?>
                              <optgroup label='<?= $key ?>'>
                                 <?php    foreach ($category as $pkey =>$row) { ?>
                                 <option value="<?= $row['id'] ?>">
                                    <?= $row['name'].' ('.$row['product_code'].')' ?>
                                 </option>
                                 <?php } ?>
                              </optgroup>
                              <?php } ?>
                              <?php } ?>
                              <?php } ?>
                           </select>
                           <label>Products</label>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="form-group">
                        <select name="status" class="form-control listing_search" id="status" >
                           <option value=""></option>
                           <option value="Active" >Active</option>
                           <option value="Inactive" >Inactive</option>
                        </select>
                        <label>Status </label>
                     </div>
                  </div>
               </div>
               <div class="panel-footer clearfix">
                  <button type="submit" class="btn btn-info" name="search" id="search" > <i class="fa fa-search"></i> Search </button>
                  <button type="button" class="btn btn-info btn-outline" name="viewall" id="viewall" onClick="window.location = 'vendors.php'"> <i class="fa fa-search-plus"></i> View All</button>
                  <button type="button" name="vendor_export" id="vendor_export" class="btn red-link"> <i class="fa fa-download"></i> Export</button>
                  <input type="hidden" name="export" id="export" value="" />
                  <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
                  <input type="hidden" name="pages" id="per_pages" value="<?= $per_page; ?>" />
                  <input type="hidden" name="sort_by" id="sort_by_column" value="<?= $SortBy; ?>" />
                  <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?= $SortDirection; ?>" />
               </div>
            </div>
         </div>
      </div>
   </form>
   <div class="search-handle"> <a href="#" data-perform="panel-collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></a> </div>
</div>
<div class="panel panel-default panel-block">
   <div class="panel-body">
      <div id="ajax_loader" class="ajex_loader" style="display: none;">
         <div class="loader"></div>
      </div>
      <div id="ajax_data"> </div>
   </div>
</div>
<script language="javascript" type="text/javascript">
   $(document).ready(function() {
    dropdown_pagination('ajax_data')
     $("#srch_vendorJoindate,.date_picker").datepicker({
       changeDay: true,
       changeMonth: true,
       changeYear: true
     });
   
     $("#products,#vendor_ids").multipleSelect({});
   
     $(document).keypress(function(e) {
       if (e.which == 13) {
         ajax_submit();
       }
     });
   
     ajax_submit();
   
     var auto_complete_address = {
       autoFocus: true,
       source: function(request, response) {
         $.ajax({
           url: "ajax_get_auto_complete_data.php?action=getmembers",
           type: "POST",
           dataType: "json",
           data: {
             query: request.term
           },
           success: function(data) {
             response(data);
           }
         });
       },
       minLength: 2,
       open: function() {}
     };
     $("#member_id").autocomplete(auto_complete_address);
     $(".ui-helper-hidden-accessible").remove();
   
   });
   
   $(document).off('click', '.vendor_member_fees_popup');
   $(document).on('click', '.vendor_member_fees_popup', function(e) {
     e.preventDefault();
     $.colorbox({
       href: $(this).attr('href'),
       iframe: true,
       width: '800px',
       height: '800px'
     });
   });
   
   $(document).off('click', '.vendor_product_popup');
   $(document).on('click', '.vendor_product_popup', function(e) {
     e.preventDefault();
     $.colorbox({
       href: $(this).attr('href'),
       iframe: true,
       width: '800px',
       height: '450px'
     });
   });

   $(document).off('click', '#vendor_export');
   $(document).on('click', '#vendor_export', function(e) {
      confirm_export_data(function() {
          $("#export").val('vendor_export');
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
   
   // $(document).off('click', '#export');
   // $(document).on('click', '#export', function(e) {
   //   e.preventDefault();
   //   swal({
   //     title: 'Are you sure?',
   //     text: 'You want to export to Excel All Members',
   //     type: 'success',
   //     showCancelButton: true,
   //     confirmButtonColor: "#0088cc",
   //     confirmButtonText: 'Yes, export it!',
   //     cancelButtonText: 'No, cancel!',
   //   }).then(function() {
   //     $('#is_ajaxed').val('0');
   //     $('#is_export').val('1');
   //     $('#frm_search').submit();
   //   }, function(dismiss) {
   //     window.location.reload();
   //   })
   // });
   
   $(document).off('change', '#join_range');
   $(document).on('change', '#join_range', function(e) {
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
   
   $(document).off("submit", "#frm_search");
   $(document).on("submit", "#frm_search", function(e) {
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
         $('[data-toggle="tooltip"]').tooltip();
       }
     });
   });
   
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
   
   $(document).off('change', '.vendor_fee_status');
   $(document).on("change", ".vendor_fee_status", function(e) {
     e.stopPropagation();
     var status = $(this).val();
     var id = $(this).attr("data-id");
     var old_status = $(this).attr('data-old_status');
   
     if (status == "Active") {
       $message = '<p class="fs14 fw400 m-t-20 m-b-0">Active, is used when a vendor is set-up and applied to new sales and renewals based on the rules of the product.</p>';
     } else if (status == "Inactive") {
       $message = '<p class="fs14 fw400 m-t-20 m-b-0">Inactive, is used when a vendor is no longer applied to new sales but renewals continue based on the rules of the product.</p>';
     }
   
     if (status != "") {
       swal({
         text: 'Change Vendor Status to <strong class="text-blue">' + status + '</strong>: Are you sure?' + $message,
         showCancelButton: true,
         confirmButtonText: "Confirm",
       }).then(function() {
         $("#ajax_loader").show();
         $.ajax({
           url: 'ajax_change_fees_status.php',
           data: {
             id: id,
             status: status
           },
           type: 'POST',
           dataType: 'json',
           success: function(res) {
             $("#ajax_loader").hide();
             if (res.status == "success") {
               $("#vendor_status_" + id).attr('data-old_status', status);
               setNotifySuccess(res.msg);
             } else {
               setNotifyError(res.msg);
               $("#vendor_status_" + id).val(old_status);
               $('select.form-control').selectpicker('refresh');
             }
           }
         });
       }, function(dismiss) {
         $("#vendor_status_" + id).val(old_status);
         $('select.form-control').selectpicker('refresh');
       })
     }
   });
   
   $(document).off('click', '.delete_vendor_fee');
   $(document).on("click", ".delete_vendor_fee", function(e) {
     e.stopPropagation();
     var id = $(this).attr('data-id');
     swal({
       text: '<br>Delete Record: Are you sure?',
       showCancelButton: true,
       confirmButtonText: 'Confirm',
     }).then(function() {
       $("#ajax_loader").show();
       $.ajax({
         url: 'ajax_delete_vendor_fee.php',
         dataType: 'JSON',
         type: 'POST',
         data: {
           id: id
         },
         success: function(res) {
           $("#ajax_loader").hide();
           if (res.status == "success") {
             setNotifySuccess(res.message);
             ajax_submit();
           } else {
             setNotifyError(res.message);
           }
         }
       })
     }, function(dismiss) {
   
     });
   });
   
   function ajax_submit() {
     $('#ajax_loader').show();
     $('#ajax_data').hide();
     $('#is_ajaxed').val('1');
     var params = $('#frm_search').serialize();
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
   
</script>
<?php } ?>