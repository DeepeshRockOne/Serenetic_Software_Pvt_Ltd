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
    <div class="pull-right">
        <div class="m-b-15">
          <a class="btn btn-default" href="prd_edit_options.php">Manage Products</a>
          <a class="btn btn-action" href="product_builder.php">+ Product</a>
        </div>
      </div>
</div>
<div class="table-responsive">
  <table class="<?= $table_class ?>">
    <thead>
      <tr class="data-head">
        <th ><a href="javascript:void(0);" data-column="pro_id" data-direction="<?php echo $SortBy == 'pro_id' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">ID/Added Date</a></th>
        <th><a href="javascript:void(0);" data-column="p.name" data-direction="<?php echo $SortBy == 'p.name' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Product Name</a></th>
        <th><a href="javascript:void(0);" data-column="cl.company_name" data-direction="<?php echo $SortBy == 'cl.company_name' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Company</a></th>
        <th><a href="javascript:void(0);" data-column="cl.company_name" data-direction="<?php echo $SortBy == 'cl.company_name' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Product Type</a></th>
        <th><a href="javascript:void(0);" data-column="p.category_id" data-direction="<?php echo $SortBy == 'p.category_id' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Product Category</a></th>
        <th width="15%"><a href="javascript:void(0);" data-column="status" data-direction="<?php echo $SortBy == 'status' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Status</a></th>
        <th class="text-center" >Variations</th>
        <th width="180px">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($total_rows > 0) { ?>
        <?php foreach ($fetch_rows as $rows) { ?>
          
          <tr>
            <td>
              <a href="product_builder.php?product=<?php echo $rows['encrypted_id'] ?>" data-toggle="tooltip" data-trigger="hover" title="Edit" class="text-action fw500"><?php echo isset($rows['product_code'])?$rows['product_code']:""; ?></a>
              <br />
              <?php echo date('m/d/Y', strtotime($rows['create_date'])); ?>
            </td>
            <td><?php echo isset($rows['product_nm'])?$rows['product_nm']:""; ?></td>
            <td><?php echo isset($rows['company_name'])?$rows['company_name']:""; ?></td>
            <td><?php echo isset($rows['product_type'])?$rows['product_type']:""; ?></td>
            <td><?php echo isset($rows['cat_name'])?$rows['cat_name']:""; ?></td>
            <td >
              <?php if($rows['status'] != 'Archive') {?>
                <div class="theme-form pr w-200">
                  <select id="status_s_<?php echo $rows['encrypted_id']; ?>" class="status_s form-control has-value" style="width:150px;" name="status_s" onchange="changeStatus('<?= $rows['encrypted_id']; ?>', this.value, '<?=$rows['status'];?>')">
                    <option value="Pending" <?=($rows['status'] == "Pending" ? 'selected' : '')?> >Pending</option>
                    <option value="Active" <?=($rows['status'] == "Active" ? 'selected' : '')?> >Active</option>
                    <option value="Suspended" <?=($rows['status'] == "Suspended" ? 'selected' : '')?> >Suspended</option>
                    <option value="Extinct" <?=($rows['status'] == "Extinct" ? 'selected' : '')?> >Extinct</option>
                  </select>
                  <label>Select</label>
                </div>
              <?php } else {
              echo $rows['status'];
              } ?>
            </td>
            <td class="text-center">
              <a class="text-action fw500" href="manage_variation_list.php?product=<?=$rows['encrypted_id'];?>">
                <?php echo $rows['total_variation']; ?>
              </a> 
              <a href="product_builder.php?parentProduct=<?=$rows['encrypted_id'];?>" data-toggle="tooltip" data-trigger="hover" title="Add New Variation" class="text-action fw500" >+</a>
            </td>
            <td class="icons">
                <?php if(isset($rows['product_type']) && $rows['product_type'] != "Admin Only Product") { ?>
                    <a href="add_assign_agents.php?productId=<?=$rows['encrypted_id'];?>" data-toggle="tooltip" data-trigger="hover" title="Assign Agents" class="add_assign_agents"><i class="fa fa-user"></i></a>
                <?php } ?>               
                
                <a href="product_builder.php?product=<?php echo $rows['encrypted_id'] ?>&is_clone=Y" data-toggle="tooltip" data-trigger="hover" title="Clone"><i class="fa fa-clone" ></i></a>
                <a href="product_builder.php?product=<?php echo $rows['encrypted_id'] ?>" data-toggle="tooltip" data-trigger="hover" title="Edit"><i class="fa fa-edit" ></i></a>
                <a href="<?= $ADMIN_HOST ?>/prd_history.php" data-toggle="tooltip" data-trigger="hover" title="History" class="popup_lg" data-product_id="<?= $rows['encrypted_id'] ?>"><i class="fa fa-history"></i></a>
                <a href="javascript:void(0)"  class="delete_product" data-id="<?php echo $rows['encrypted_id'] ?>" data-toggle="tooltip" data-trigger="hover" title="Delete"><i class="fa fa-trash"></i></a>
            </td>
          </tr>
        <?php }?>
      <?php } else {?>
      <tr>
        <td class="text-center" colspan="8">No record(s) found</td>
      </tr>
      <?php }?>
    </tbody>
    <?php if ($total_rows > 0) {?>
      <tfoot>
        <tr>
          <td colspan="8">
            <?php echo $paginate->links_html; ?>
          </td>
        </tr>
      </tfoot>
    <?php }?>
  </table>
</div>
<?php } else { ?>
<div class="panel panel-default panel-block panel-title-block " >
  <form id="frm_search" action="manage_product.php" method="GET" autocomplete="off">
    <div class="panel-left">
      <div class="panel-left-nav">
        <ul><li class="active"><a href="javascript:void(0);"><i class="fa fa-search"></i></a></li></ul>
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
              <div class="form-group">
                <input type="text" name="product_code" id="product_code" value="<?php echo $product_code ?>" class="form-control listing_search" />
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
                      <input type="text" name="added_date" id="added_date" value="" class="form-control date_picker listing_search" />
                    </div>
                    <div  id="range_join" style="display:none;">
                      <div class="phone-control-wrap">
                        <div class="phone-addon">
                          <label class="mn">From</label>
                        </div>
                        <div class="phone-addon">
                          <div class="input-group"> 
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" name="fromdate" id="fromdate" value="" class="form-control date_picker listing_search" />
                          </div>
                        </div>
                        <div class="phone-addon">
                          <label class="mn">To</label>
                        </div>
                        <div class="phone-addon">
                          <div class="input-group"> 
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" name="todate" id="todate" value="" class="form-control date_picker listing_search" />
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
                <input type="text" name="title" id="title" value="<?php echo $title ?>" class="form-control listing_search" />
                <label>Product Name</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                  <select name="company[]" id="company" class="listing_search se_multiple_select searchMultipleSelect" multiple="multiple">
                    <?php if(!empty($res_company)){ ?>
                      <?php foreach ($res_company as $cat) {?>
                        <option value="<?=$cat['id']?>"><?=$cat['company_name']?></option>
                      <?php }?>
                    <?php }?>
                  </select>
                  <label>Company Name</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                  <select name="products_type[]" id="products_type" class="listing_search se_multiple_select searchMultipleSelect" multiple="multiple">
                    <option value="Direct Sale Product" <?= isset($product_type) && $product_type=="Direct Sale Product" ? 'selected=selected' : '' ?>>Direct Sale Product</option>
                    <option value="Group Enrollment" <?= isset($product_type) && $product_type=="Group Enrollment" ? 'selected=selected' : '' ?>>Group Application</option>
                    <option value="Admin Only Product" <?= isset($product_type) && $product_type=="Admin Only Product" ? 'selected=selected' : '' ?>>Admin Only Product</option>
                    <option value="Add On Only Product" <?= isset($product_type) && $product_type=="Add On Only Product" ? 'selected=selected' : '' ?>>Add-On Only Product</option>
                  </select>
                  <label>Products Type</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                  <select name="product_category[]" id="product_category" class="listing_search se_multiple_select searchMultipleSelect" multiple="multiple">
                    
                    <?php if(!empty($res_cat)){ ?>
                      <?php foreach ($res_cat as $cat) {?>
                        <option value="<?=$cat['id']?>"><?=$cat['title']?></option>
                      <?php } ?>
                    <?php } ?>
                  </select>
                  <label>Product Category</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <div class="group_select">
                  <select name="s_status[]" id="s_status" class="listing_search se_multiple_select searchMultipleSelect" multiple="multiple">
                    <option value="Pending" <?=($s_status == "Pending" ? 'selected' : '')?> >Pending</option>
                    <option value="Active" <?=($s_status == "Active" ? 'selected' : '')?> >Active</option>
                    <option value="Suspended" <?=($s_status == "Suspended" ? 'selected' : '')?> >Suspended</option>
                    <option value="Extinct" <?=($s_status == "Extinct" ? 'selected' : '')?> >Extinct</option>
                  </select>
                  <label>Status</label>
                </div>
              </div>
            </div>
          </div>
          <div class="panel-footer clearfix">
            <button type="submit" class="btn btn-info" name="search" id="search" > <i class="fa fa-search"></i> Search </button>
            <button type="button" class="btn btn-info btn-outline" name="viewall" id="viewall" onClick="window.location = 'manage_product.php'"> <i class="fa fa-search-plus"></i> View All</button>
            <button type="button" id="export" class="btn red-link"> <i class="fa fa-download"></i> Export</button>
            <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
            <input type="hidden" name="is_export" id="is_export" value="" />
            <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
            <input type="hidden" name="sort" id="sort_column" value="<?=$SortBy;?>" />
            <input type="hidden" name="direction" id="sort_direction" value="<?=$SortDirection;?>" />
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
  <div class="panel-body">
    <div  class="ajex_loader" style="display:none"></div>
    <div id="ajax_data"></div>
  </div>
</div>
<script type="text/javascript">

  $(document).ready(function() {
    dropdown_pagination('ajax_data')
    $(".searchMultipleSelect").multipleSelect();
    $(".date_picker").datepicker({
      changeDay: true,
      changeMonth: true,
      changeYear: true
    });

    ajax_submit();

    $(document).keypress(function (e) {
      if (e.which == 13) {
        ajax_submit();
      }
    });
  });

  $(document).off("submit", "#frm_search");
  $(document).on("submit", "#frm_search", function(e) {
    $('#nav_page').val(1);
    e.preventDefault();
    disable_search();
  });

  $(document).off('click', '.add_assign_agents');
    $(document).on('click', '.add_assign_agents', function (e) {
        e.preventDefault();
        $.colorbox({
          href: $(this).attr('href'),
          iframe: true, 
          width: '600px', 
          trapFocus: true,
          height: '700px'
    })
    });

  $(document).off("click", ".popup_lg");    
  $(document).on("click",".popup_lg",function(e){
    e.preventDefault();
    $href=$(this).attr('href');
    $product=$(this).attr('data-product_id');
    $.colorbox({
      href: $href+"?type=all&product="+$product,
      iframe: true, 
      width: '900px',
      height: '580px',
    });
  });

  $(document).off('click', '#ajax_data tr.data-head a');
  $(document).on('click', '#ajax_data tr.data-head a', function(e) {
    e.preventDefault();
    $('#sort_column').val($(this).attr('data-column'));
    $('#sort_direction').val($(this).attr('data-direction'));
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
      success: function(res) {
        $('#ajax_loader').hide();
        $('#ajax_data').html(res).show();
        common_select();
        $('[data-toggle="tooltip"]').tooltip();
      }
    });
  });

  $(document).off('click', '.delete_product');
  $(document).on('click', '.delete_product', function(e) {

    var id = $(this).attr('data-id');
    
    swal({
        text: "Delete Product and All Variations: Are you sure?",
        showCancelButton: true,
        confirmButtonText: "Confirm",
    }).then(function() {
      $.ajax({
        url: '<?=$ADMIN_HOST?>/manage_product_delete.php',
        data: {id: id},
        method: 'POST',
        dataType: 'JSON',
        success: function(data) {
          if (data.status == 'success') {
            setNotifySuccess(data.msg);
            ajax_submit();
          } else if(data.status == 'fail'){
            setNotifyError(data.msg);   
          } else {
            setNotifyError(data.msg);
          }
        }
      });
    });
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

  $(document).off('click', '#export');
  $(document).on('click', '#export', function (e) {
      e.stopPropagation();

      confirm_export_data(function() {
          $("#is_export").val(1);
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
                  $("#export_val").val('');
                  if(res.status == "success") {
                      confirm_view_export_request();
                  } else {
                      setNotifyError(res.message);
                  }
              }
          });
      });
  });

  function ajax_submit() {
    $('#ajax_loader').show();
    $('#ajax_data').hide();
    $('#is_ajaxed').val('1');
    $('#is_export').val('');
    var cpage = $('#nav_page').val();
    $.ajax({
      url: $(this).attr('action'),
      type: 'GET',
      data: $('#frm_search').serialize(),
      success: function(res) {
        $('#ajax_loader').hide();
        $('#ajax_data').html(res).show();
        $('[data-toggle="tooltip"]').tooltip();
        common_select();
      }
    });
    return false;
  }
  function changeStatus(id, val, old_val) {
    $message="";
    if(val=="Pending"){
      $message='<p class="fs14 fw400 m-t-20 m-b-0">Pending, is used when a product is created but not ready for new sales.</p>';
    }else if (val=="Active"){
      $message='<p class="fs14 fw400 m-t-20 m-b-0">Active, is used when a product is active for both new business and renewal sales.</p>';
    }else if (val=="Suspended"){
      $message='<p class="fs14 fw400 m-t-20 m-b-0">Suspended, is used when a product is NOT active for new sales, yet renewals will continue.</p>';
    }else if (val=="Extinct"){
      $message='<p class="fs14 fw400 m-t-20 m-b-0">Extinct, is used when a product is NOT active for new sales nor renewals and a termination date would occur at end of current plan period on active plans.</p>';
    }
    swal({
      text: 'Change Product Status to <strong class="text-blue">'+val+'</strong>: Are you sure?'+ $message,
      showCancelButton: true,
      confirmButtonText: "Confirm",
    }).then(function() {
    
      $.ajax({
        url: '<?=$ADMIN_HOST?>/ajax_product_builder_change_status.php',
        data: 'status=' + val + '&id=' + id,
        type: 'GET',
        dataType: "json",
        success: function(res) {
          if (res.status == "success") {
            setNotifySuccess(res.msg);
          } else {
            swal({
              title: "Incomplete Setup",
              showCancelButton: true,
              cancelButtonText: "Close",
              text: "<br>Before updating the product status, the following areas must be completed: <div class='require_label_wrap m-t-15'>" + res.error_fields + '</div>',
              
            }).then(function() {
              $('#status_s_' + id).val(old_val);
              $('select.form-control').selectpicker('render');
              return false;
            }, function(dismiss) {
              $('#status_s_' + id).val(old_val);
              $('select.form-control').selectpicker('render');
              return false;
            });
          }
        }
      });
      return false;
    }, function(dismiss) {
      $('#status_s_' + id).val(old_val);
      $('select.form-control').selectpicker('render');
      return false;
    });
  }
</script>
<?php } ?>
