<?php if($is_ajaxed) {?>
<div class="panel panel-default panel-block panel-title-block">
   <div class="panel-body">
      <div class="clearfix tbl_filter">
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
         <div class="pull-right m-b-15"> 
            <a class="btn btn-action" href="resource_add.php">+ Resource</a> 
         </div>
      </div>
      <div class="table-responsive">
         <table class="<?=$table_class?>">
            <thead>
               <tr>
                  <th>ID/Added Date</th>
                  <th>Resource Name</th>
                  <th>User Group</th>
                  <th>Effective Date</th>
                  <th>Termination Date</th>
                  <th class="text-center">Products #</th>
                  <th width="15%">Resource Type</th>
                  <th width="15%">Status</th>
                  <th width="100px">Actions</th>
               </tr>
            </thead>
            <tbody>
               <?php if($total_rows > 0) {?>
               <?php foreach($fetch_rows as $row) { ?>
               <tr>
                  <td>
                     <a href="resource_add.php?resource_id=<?=$row['id']?>" target="_blank" class="fw600 text-red"><?=$row['display_id']?></a><br />
                     <?=date('m/d/Y',strtotime($row['created_at']))?>
                  </td>
                  <td><strong><?=$row['name']?></strong></td>
                  <td><?=$row['user_group']?></td>
                  <td><?=date('m/d/Y',strtotime($row['active_date']))?></td>
                  <td class="text-center"><?=(strtotime($row['termination_date']) > 0 ? date('m/d/Y',strtotime($row['termination_date'])) : "-")?></td>
                  <td class="text-center"><a href="javascript:void(0);"  data-href="resource_popup.php?res_id=<?=$row['id']?>" class="fw600 text-red resource_prd_popup">
                     <?= $row['prd_total'] ?>
                     </a>
                  </td>
                  <td><?=$row['type'] == 'id_card' ? "ID Card" :$row['type'] ?></td>
                  <td>
                     <div class="theme-form pr w-130">
                        <select id="is_active" class="status_s form-control has-value" name="is_active" onchange="confirm_active('<?= $row['id']; ?>', this.value ,'<?=$row['status']?>')">
                           <option value="Active" <?php echo ($row['status'] == "Active")?'selected':''?>>Active</option>
                           <option value="Inactive" <?php echo ($row['status'] == "Inactive")?'selected':''?>>Inactive</option>
                        </select>
                        <label>Select</label>
                     </div>
                  </td>
                  <td class="icons text-right"  >
                     <a href="resource_add.php?resource_id=<?=$row['id']?>&is_clone=Y" data-toggle="tooltip" data-trigger="hover" title="Clone Resource" data-placement="top"><i class="fa fa-clone"></i></a>
                     <a href="resource_add.php?resource_id=<?=$row['id']?>" data-toggle="tooltip" data-trigger="hover" title="View Resource" data-placement="top"><i class="fa fa-edit"></i></a>
                     <a href="javascript:void(0);" data-id="<?=$row['id']?>" onclick="delete_resource('<?= $row['id'] ?>')" data-toggle="tooltip" data-trigger="hover" title="Delete" data-placement="top"><i class="fa fa-trash"></i></a>
                  </td>
               </tr>
               <?php }
                  } else { ?>
               <tr>
                  <td colspan="9">No record(s) found</td>
               </tr>
               <?php } ?>
            </tbody>
            <?php if ($total_rows > 0) { ?>
            <tfoot>
               <tr>
                  <td colspan="9">
                     <?php echo $paginate->links_html; ?>
                  </td>
               </tr>
            </tfoot>
            <?php }?>
         </table>
      </div>
   </div>
</div>
<?php }else{ ?>
<div class="panel panel-default panel-block panel-title-block">
   <form id="frm_search" action="products_resource.php" method="GET" autocomplete="off">
      <div class="panel-left">
         <div class="panel-left-nav">
            <ul>
               <li class="active"><a href="javascript:void(0);"><i class="fa fa-search"></i></a></li>
            </ul>
         </div>
      </div>
      <div class="panel-right">
         <div class="panel-heading">
            <div class="panel-search-title"> <span class="clr-light-blk">SEARCH</span> </div>
         </div>
         <div class="panel-wrapper collapse in">
            <div class="panel-body theme-form">
               <div class="row">
                 <div class="col-md-6">
                     <div class="form-group">
                           <select name="resource_ids[]" id="resource_ids" multiple="multiple" class="se_multiple_select" >
                              <?php if(!empty($resResource)){
                                 foreach ($resResource as $row) {
                              ?>
                                 <option value="<?= $row['id'] ?>" <?=(!empty($resource_ids) && in_array($row['id'], $resource_ids)) ? 'selected="selected"' : ''?>><?=$row['display_id']?></option>
                              <?php 
                                  }
                                }
                              ?> 
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
                              <div id="all_join" class="input-group"> <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                 <input type="text" name="added_date" id="added_date" value="" class="form-control date_picker" />
                              </div>
                              <div  id="range_join" style="display:none;">
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
                        <input type="text" class="form-control" name="resource_name" />
                        <label>Resource Name</label>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="form-group">
                        <select  class="form-control" name="user_group">
                           <option></option>
                           <option value="Agent">Agent</option>
                           <option value="Group">Group</option>
                           <option value="Member">Member</option>
                        </select>
                        <label>User Group </label>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="form-group ">
                           <select name="resource_product[]" id="resource_product" multiple="multiple" class=" se_multiple_select" >
                              <?php foreach ($product_res as $key=>$company) { ?>
                              <optgroup label='<?= $key ?>'>
                                 <?php foreach ($company as $pkey =>$row) { ?>
                                 <option value="<?= $row['id'] ?>"><?= $row['name'] .' ('.$row['product_code'].')' ?></option>
                                 <?php } ?>
                              </optgroup>
                              <?php } ?>
                           </select>
                           <label for="resource_product">Products</label>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="form-group ">
                        <select class="se_multiple_select" name="resource_type[]" id="resource_type" multiple="multiple">
                           <option value="Certificate">Certificate</option>
                           <option value="Collateral">Collateral</option>
                           <option value="id_card">ID Card</option>
                        </select>
                        <label>Resource Type </label>
                     </div>
                  </div>
                  <div class="clearfix"></div>
                  <div class="col-sm-6">
                     <div class="form-group">
                        <select  class="form-control" name="resource_status">
                           <option></option>
                           <option value="Active">Active</option>
                           <option value="Inactive">Inactive</option>
                        </select>
                        <label for="resource_status">Status </label>
                     </div>
                  </div>
               </div>
               <div class="panel-footer clearfix">
                  <button type="button" class="btn btn-info" onclick="ajax_submit();"> <i class="fa fa-search"></i> Search </button>
                  <button type="button" class="btn btn-info btn-outline" onclick="window.location = 'products_resource.php'"> <i class="fa fa-search-plus"></i> View All</button>
                  <button type="button" class="btn red-link" id="export_resource"> <i class="fa fa-download"></i> Export</button>
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
<div id="ajax_loader" class="ajex_loader" style="display: none;">
   <div class="loader"></div>
</div>
<div id="ajax_data" > </div>
<script type="text/javascript">
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
   
   $(document).ready(function() {
    dropdown_pagination('ajax_data')

     $(".se_multiple_select").multipleSelect({
           filter:true
       });
      $(".date_picker").datepicker({
       });
   
       $(document).off('click','.resource_prd_popup');
       $(document).on('click','.resource_prd_popup',function(e){
         $href=$(this).data('href');
         $.colorbox({
           href:$href,
           iframe:true,
           width:'800px',
           height:'500px'
         });
       })
     //  $('.resource_prd_popup').colorbox({iframe:true,width:'800px',height:'500px'});
   
      $(document).keypress(function (e) {
           if (e.which == 13) {
             ajax_submit();
           }
         });
   
         ajax_submit();
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
             $('[data-toggle="tooltip"]').tooltip();
           }
         });
       });
   
    function confirm_active(id, val,old_status) {
    $message="";
    if(val=="Active"){
      $message='<p class="fs14 fw400 m-t-20 m-b-0">Active, is used when a resource is set-up and applied to new sales and renewals.</p>';
    }else if (val=="Inactive"){
      $message='<p class="fs14 fw400 m-t-20 m-b-0">Inactive, is used when a resource is no longer active.</p>';
    }
       swal({
        text: '<br>Change Status: Are you sure?'+ $message,
         showCancelButton: true,
         confirmButtonText: 'Confirm',
         cancelButtonText: 'Cancel',
       }).then(function() {
         window.location = "products_resource.php?res_id=" + id + "&is_active=" + val+"&old_status="+old_status;
       }, function(dismiss) {
         window.location.reload();
       });
   
     }
   
     $(document).off('click', '#export_resource');
     $(document).on('click', '#export_resource', function(e) {
       swal({
         text: '<br>Export Data: Are you sure?',
         showCancelButton: true,
         confirmButtonText: 'Confirm',
         cancelButtonText: 'Cancel',
       }).then(function() {
         $("#export").val('export_resource');
         $('#ajax_loader').show();
         $('#is_ajaxed').val('1');
         var params = $('#frm_search').serialize();
         $.ajax({
           url: $('#frm_search').attr('action'),
           type: 'GET',
           data: params,
           dataType: 'json',
           success: function() {
             $('#ajax_loader').hide();
             $("#export").val('');
           }
         }).done(function(data) {
           var $a = $("<a>");
           $a.attr("href", data.file);
           $("body").append($a);
           $a.attr("download", "Resource_Record.xls");
           $a[0].click();
           $a.remove();
           $('#ajax_loader').hide();
         });
       }, function(dismiss) {
   
       })
     });
   
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
             common_select();
             $('[data-toggle="tooltip"]').tooltip();
           }
         });
         return false;
       }
     function delete_resource(id){
     swal({
       text: '<br>Delete Record: Are you sure?',
       showCancelButton: true,
       confirmButtonText: 'Confirm',
       cancelButtonText: 'Cancel',
     }).then(function() {
       window.location = "products_resource.php?res_id=" + id + "&is_deleted=N";
     }, function(dismiss) {
       window.location.reload();
     });
   }
</script>
<?php } ?>