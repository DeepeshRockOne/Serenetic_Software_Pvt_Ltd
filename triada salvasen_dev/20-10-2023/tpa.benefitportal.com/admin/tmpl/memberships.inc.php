<script type="text/javascript">
  $(document).on('change','.fees_status',function (e) {
       e.stopPropagation();
       var id = $(this).attr('id').replace('fees_status_', '');
       var fees_status = $(this).val();
       var old_status = $(this).attr('data-old_status');
       swal({
           text: 'Change Status: Are you sure?',
           showCancelButton: true,
           confirmButtonText: "Confirm",
       }).then(function () {
           window.location = 'memberships.php?fee_id=' + id + '&fees_status=' + fees_status;
         },function (dismiss) {
         $("#fees_status_" + id).val(old_status);
         $('#fees_status_'+id).selectpicker('render');
       });
   });
   
</script>
<?php if ($is_ajaxed) {?>
<div class="table-responsive">
   <table class="<?=$table_class?>">
      <thead>
         <tr class="data-head">
            <th><a href="javascript:void(0);" data-column="id" data-direction="<?php echo $SortBy == 'id' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">ID / Added Date</a></th>
            <th><a href="javascript:void(0);" data-column="name" data-direction="<?php echo $SortBy == 'name' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Details</a></th>
            <th class="text-center"><a href="javascript:void(0);">Membership Fee</a></th>
            <th class="text-center"><a href="javascript:void(0);">Products #</a></th>
            <th class="text-center"><a href="javascript:void(0);">Members #</a></th>
            <th width="15%" ><a href="javascript:void(0);" data-column="status" data-direction="<?php echo $SortBy == 'status' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Status</a></th>
            <th width="130px">Actions</th>
         </tr>
      </thead>
      <tbody>
         <?php if ($total_rows > 0) { ?>
         <?php foreach ($fetch_rows as $rows) { ?>
         <tr>
            <td><a href="memberships_mange.php?id=<?php echo md5($rows['id']); ?>" class="text-action" target="_blank" id="links1">  <strong><?= $rows['display_id']; ?></strong></a> 
               <br><?php echo date('m/d/Y',strtotime($rows['created_at'])) ?> 
            </td>
            <td><?= $rows['name']; ?> </td>
            <td  class="text-center"><?= displayAmount($rows['total_fee'],2); ?> <?= ($rows['total_products'] > 1) ? '+' : ''; ?> </td>
            <td  class="text-center">
               <a href="view_fees_details.php?id=<?= md5($rows['id']); ?>&name=<?= $rows['name'] ?>&display_id=<?= $rows['display_id'] ?>&count=<?= $rows['total_products'] ?>&membership_id=<?=$rows['id']?>" class="fw600 text-red popup"><?php echo $rows['total_products']; ?></a>
            </td>
            <!-- <td  class="text-center"><a href="membership_prd_popup.php?id=<?=$rows['id'];?>" class="fw500 text-action popup"><?=$rows['total_products']; ?></a></td> -->
            <td  class="text-center"><?=!empty($rows['total_members'])?$rows['total_members']:0?></td>
            <td >
               <div class="theme-form w-130 pr">
               <select name="fees_status" data-old_status="<?=$rows['status']?>" class="form-control fees_status has-value" id="fees_status_<?=$rows['id'];?>">
                     <option value="Active" <?= ($rows['status'] == 'Active') ? "selected='selected'" : ''?>>Active</option>
                     <option value="Inactive" <?= ($rows['status'] == 'Inactive') ? "selected='selected'" : ''?>>Inactive </option>
                  </select>
                  <label>Select</label>
               </div>
            </td>
            <td class="icons">
               <a href="memberships_mange.php?id=<?php echo md5($rows['id']) ?>&is_clone=Y" data-toggle="tooltip" title="Duplicate" class="add_vendor_fee"><i class="fa fa-clone" aria-hidden="true"></i></a>
               <a href="memberships_mange.php?id=<?php echo md5($rows['id'])?>" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil-square-o"></i></a>
               <a href="javascript:void(0);" data-toggle="tooltip" title="Delete" onclick="delete_fee(<?=$rows['id']?>)"><i class="fa fa-trash"></i></a>
            </td>
         </tr>
         <?php } ?>
         <?php } else {?>
         <tr>
            <td colspan="7">No record(s) found</td>
         </tr>
         <?php }?>
      </tbody>
      <?php if ($total_rows > 0) { ?>
      <tfoot>
         <tr>
            <td colspan="7">
               <?php echo $paginate->links_html; ?>
            </td>
         </tr>
      </tfoot>
      <?php } ?>
   </table>
</div>
<?php } else {
   ?>
<?php include_once 'notify.inc.php';?>
<div class="panel panel-default panel-block panel-title-block">
   <div class="panel-left">
      <div class="panel-left-nav">
         <ul>
            <li class="active"><a href="javascript:void(0);"><i class="fa fa-search"></i></a></li>
         </ul>
      </div>
   </div>
   <div class="panel-right">
      <form id="frm_search" action="" method="GET" autocomplete="off">
         <div class="panel-heading">
            <div class="panel-search-title"><i class="fa fa-search clr-light-blk"></i> <span class="clr-light-blk">SEARCH</span></div>
         </div>
         <div class="panel-wrapper collapse in">
            <div class="panel-body theme-form">
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <select name="membership_display_id[]" id="membership_display_ids" class="se_multiple_select" multiple="multiple">
                          <?php if($membership_display_ids){
                            foreach ($membership_display_ids as $key => $value) { ?>
                              <option value="<?=$value['display_id']?>"><?=$value['display_id']?></option>
                            <?php }
                          } ?>
                        </select>
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
                              <div  id="range_join" style="display:none;">
                                 <div class="phone-control-wrap">
                                    <div class="phone-addon">
                                       <label class="mn">From</label>
                                    </div>
                                    <div class="phone-addon">
                                       <div class="input-group">
                                          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                          <input type="text" name="from_date" id="fromdate" value="" class="form-control date_picker" />
                                       </div>
                                    </div>
                                    <div class="phone-addon">
                                       <label class="mn">To</label>
                                    </div>
                                    <div class="phone-addon">
                                       <div class="input-group">
                                          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                          <input type="text" name="end_date" id="todate" value="" class="form-control date_picker" />
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
                        <input type="text" class="form-control listing_search" name="membership_name" value="<?php echo $membership_name ?>">
                        <label>Membership Name</label>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="form-group">
                        <input type="text" class="form-control listing_search" name="contact_name" value="">
                        <label>Contact Name</label>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="form-group ">
                           <select name="product[]" id="product" multiple="multiple" class="listing_search se_multiple_select">
                              <?php foreach ($company_arr as $key=>$company) { ?>
                              <optgroup label='<?= $key ?>'>
                                 <?php foreach ($company as $pkey =>$row) { ?>
                                 <option value="<?= $row['id'] ?>"><?= $row['name'] .' ('.$row['product_code'].')' ?></option>
                                 <?php } ?>
                              </optgroup>
                              <?php } ?>
                           </select>
                           <label>Products</label>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="form-group">
                        <select name="fee_status" class="form-control listing_search" id="fee_status" >
                           <option value=""></option>
                           <option value="Active" <?= ($fee_status == 'Active') ? "selected='selected'" : '' ?>>Active</option>
                           <option value="Inactive" <?= ($fee_status == 'Inactive') ? "selected='selected'" : '' ?>>Inactive</option>
                        </select>
                        <label>Status</label>
                     </div>
                  </div>
               </div>
               <div class="panel-footer clearfix">
                  <button type="button" class="btn btn-info" name="search" id="search" onclick="ajax_submit();" > <i class="fa fa-search"></i> Search </button>
                  <button type="button" class="btn btn-info btn-outline" name="viewall" id="viewall" onClick="window.location = 'memberships.php'"> <i class="fa fa-search-plus"></i> View All</button>
                  <button type="button" id="export" class="btn red-link" > <i class="fa fa-download"></i>  Export </button>
                  <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
                  <input type="hidden" class="listing_search" name="is_export" id="is_export" value="" />
                  <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
                  <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
                  <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
               </div>
            </div>
         </div>
      </form>
   </div>
   <div class="search-handle">
      <a href="#" data-perform="panel-collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></a>
   </div>
</div>
<div class="panel panel-default panel-block">
   <div class="panel-body">
      <div class="clearfix tbl_filter m-b-15">
      <div id="top_paginate_cont" class="pull-left">
         <div class="form-inline" id="DataTables_Table_0_length">
            <div class="form-group">
               <label for="user_type">Records Per Page </label>
            </div>
            <div class="form-group">
               <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);
                  ajax_submit();">
                  <option value="10" <?php echo (!empty($_GET['pages']) && $_GET['pages'] == 10) ? 'selected' : ''; ?>>10</option>
                  <option value="25" <?php echo (!empty($_GET['pages']) && $_GET['pages'] == 25) || empty($_GET['pages']) ? 'selected' : ''; ?>>25</option>
                  <option value="50" <?php echo (!empty($_GET['pages']) && $_GET['pages'] == 50) ? 'selected' : ''; ?>>50</option>
                  <option value="100" <?php echo (!empty($_GET['pages']) && $_GET['pages'] == 100) ? 'selected' : ''; ?>>100</option>
               </select>
            </div>
         </div>
      </div>
      <div class="pull-right">
      <a class="btn btn-action" href="memberships_mange.php">+ Membership</a>        
      </div>
   </div>
      <div id="ajax_loader" class="ajex_loader" style="display: none;">
         <div class="loader"></div>
      </div>
      <div id="ajax_data"></div>
</div>
<script type="text/javascript">
   $(document).ready(function () {
    dropdown_pagination('ajax_data')
     $(".added_date").mask("99/99/9999");
     $("#product").multipleSelect({});
     $("#membership_display_ids").multipleSelect({});
     
   
     $("#fromdate").datepicker({
         changeDay: true,
         changeMonth: true,
         changeYear: true
     });
     $("#todate").datepicker({
         changeDay: true,
         changeMonth: true,
         changeYear: true
     });
     $("#added_date").datepicker({
         changeDay: true,
         changeMonth: true,
         changeYear: true
     });
   
     ajax_submit();
   
     $(document).on('keypress',function(e) {
       if(e.which == 13) {
           ajax_submit();
       }
     });
   
     $(document).on('click', '#export', function () {
         swal({
             text: 'Export to Excel All Memberships: Are you sure?',
             showCancelButton: true,
             confirmButtonText: 'Confirm',
             cancelButtonText: 'Cancel',
         }).then(function (e) {
             $('#is_ajaxed').val('0');
             $('#is_export').val('1');
             $('#frm_search').submit();
             $('#is_export').val('0');
         }, function (dismiss) {
             window.location.reload();
         })
     });
   
      $(document).off('change', '#join_range');
       $(document).on('change', '#join_range', function(e) {
         e.preventDefault();
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
   
    
   
     function disable_search(){
       if ($(".listing_search").filter(function() { 
         return $(this).val(); 
       }).length > 0) {
           if($(".listing_search").filter(function() { 
         return $(this).val(); 
       }).length == 1 && $('input[name="added_date"]').val() == '__/__/____'){
             swal('Oops!!','Please Enter Data To Search','error');          
           }else{
             ajax_submit();  
           }
           
       }else{
         swal('Oops!!','Please Enter Data To Search','error');          
       }
     }
   
     // $(document).off("submit","#frm_search");
     // $(document).on("submit","#frm_search",function(e){
     //     e.preventDefault();
     //     disable_search();            
     // });
   
     $(document).off('click', '#ajax_data tr.data-head a');
     $(document).on('click', '#ajax_data tr.data-head a', function (e) {
         e.preventDefault();
         $('#sort_by_column').val($(this).attr('data-column'));
         $('#sort_by_direction').val($(this).attr('data-direction'));
         ajax_submit();
     });
   
     $(document).off('click', '#ajax_data ul.pagination li a');
     $(document).on('click', '#ajax_data ul.pagination li a', function (e) {
         e.preventDefault();
         $('#ajax_loader').show();
         $('#ajax_data').hide();
         $.ajax({
             url: $(this).attr('href'),
             type: 'GET',
             success: function (res) {
                 $('#ajax_loader').hide();
                 $('[data-toggle="tooltip"]').tooltip();
                 $('#ajax_data').html(res).show();
            common_select();
             }
         });
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
           success: function (res) {
               $('#ajax_loader').hide();
               $('#ajax_data').html(res).show();
               $('[data-toggle="tooltip"]').tooltip();
           common_select();
   $(".popup").colorbox({iframe: true, width: '800px', height: '600px'});
           }
       });
       return false;
   }
   function delete_fee(fee_id) {
       swal({
           text: '<br>Delete Record: Are you sure?',
           showCancelButton: true,
           confirmButtonText: 'Confirm',
           cancelButtonText: 'Cancel',
       }).then(function () {
           $("#ajax_loader").show();
           $.ajax({
               url: "memberships.php",
               dataType:'JSON',
               type: 'GET',
               data: {fee_id: fee_id,delete:'Y'},
               success: function (res) {
                   $("#ajax_loader").hide();
                   if (res.status == 'success'){
                       setNotifySuccess(res.message);
                       window.location.reload();
                   }
               }
           });
       }, function (dismiss) {
           window.location.reload();
       })
   
   }
</script>
<?php }?>