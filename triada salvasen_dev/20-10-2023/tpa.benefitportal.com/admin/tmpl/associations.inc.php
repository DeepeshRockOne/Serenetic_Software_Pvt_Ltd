<script type="text/javascript">
    $(document).on('change','.fees_status',function () {
        var id = $(this).attr('id').replace('fees_status_', '');
        var fees_status = $(this).val();

        swal({
            //title: "Are you sure ",
            text: "Change Membership Status to Inactive: Are you sure?",
            //type: "warning",
            showCancelButton: true,
            confirmButtonText: "Confirm",
            showCloseButton: true
        }).then(function () {
            window.location = 'associations.php?fee_id=' + id + '&fees_status=' + fees_status;
        });
    });
</script>
<?php if ($is_ajaxed) {?>
  <div class="table-responsive">
    <table class="<?=$table_class?>">
      <thead>
        <tr class="data-head">
            <th><a href="javascript:void(0);" data-column="id" data-direction="<?php echo $SortBy == 'id' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">ID / Added Date</a></th>
            <th width="20%"><a href="javascript:void(0);" data-column="name" data-direction="<?php echo $SortBy == 'name' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Details</a></th>
            <th width="15%" class="text-center"><a href="javascript:void(0);">Membership Fee</a></th>
            <th width="15%" class="text-center"><a href="javascript:void(0);">Products #</a></th>
            <th width="15%" class="text-center"><a href="javascript:void(0);">Members #</a></th>
            <th width="150px" ><a href="javascript:void(0);" data-column="status" data-direction="<?php echo $SortBy == 'status' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Status</a></th>
            <th width="100px">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($total_rows > 0) { ?>
          <?php foreach ($fetch_rows as $rows) { ?>
            <?php 
              $sqlPrice="SELECT MIN(price) as minPrice,count(id) as totalPrice FROM `prd_matrix` where product_id=:product_id AND is_deleted='N'";
              $resPrice=$pdo->selectOne($sqlPrice,array(":product_id"=>$rows['id']));
              $feeAmount=0;
              $totalPrice=0;
              if($resPrice){
                $feeAmount=$resPrice['minPrice'];
                $totalPrice=$resPrice['totalPrice'];
              }
            ?>
            <tr>
              <td><a href="manage_association.php?id=<?php echo $rows['id']; ?>" class="text-action" target="_blank" id="links1">  <strong><?= $rows['product_code']; ?></strong></a> 
              		<br><?php echo date('m/d/Y',strtotime($rows['create_date'])) ?> 
              </td>
              <td><?= $rows['name']; ?> </td>
              <td  class="text-center"><?= displayAmount($feeAmount,2); ?> <?= ($totalPrice > 1) ? '+' : ''; ?> </td>
              <td  class="text-center"><a href="membership_prd_popup.php" class="fw500 text-action popup"><?= countFeeProduct($rows['id']); ?></a></td>
              <td  class="text-center"><a href="membership_member_popup.php" class="fw500 text-action popup"> 250</a></td>
              <td >                                
                <select name="fees_status" class="form-control fees_status" id="fees_status_<?=$rows['id'];?>">                       
                  <option value="Active" <?= ($rows['status'] == 'Active') ? "selected='selected'" : ''?>>Active</option>
                  <option value="Inactive" <?= ($rows['status'] == 'Inactive') ? "selected='selected'" : ''?>>Inactive </option>
                </select>
              </td>
              <td class="icons">
                <a href="manage_association.php?id=<?php echo $rows['id'] ?>" data-toggle="tooltip" title="Edit"><i class="fa fa-eye"></i></a>
                <a href="javascript:void(0);" data-toggle="tooltip" title="Delete" onclick="delete_fee(<?=$rows['id']?>)"><i class="fa fa-trash"></i></a>
              </td>
            </tr>
          <?php } ?>
        <?php } else {?>
            <tr>
                <td colspan="6">No record(s) found</td>
            </tr>
        <?php }?>
      </tbody>
      <?php if ($total_rows > 0) {?>
         
      <?php }?>
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
             <form id="frm_search" action="associations.php" method="GET">
                <div class="panel-heading">
                    <div class="panel-search-title"><i class="fa fa-search clr-light-blk"></i> <span class="clr-light-blk">SEARCH</span></div>
                </div>
                <div class="panel-wrapper collapse in">
                <div class="panel-body theme-form">
                    <div class="row">                      
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control listing_search" name="product_code" value="<?php echo $product_code ?>">
                                <label>ID Number(s)</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                              <input id="added_date" type="text" class="form-control added_date listing_search" name="added_date" placeholder="" value="<?php echo $added_date?>" />
                              <label>Added Date</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control listing_search" name="name" value="<?php echo $name ?>">
                                <label>Membership Name</label>
                            </div>
                        </div>
                        <!-- Add static input field start -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control" name="name" value="">
                                <label>Contact Name</label>
                            </div>
                        </div>
                       
                        <!-- Add static input field end -->
                                              
                        <div class="col-sm-6">
                          <div class="form-group">
                            <select name="product[]" id="product" multiple="multiple" class="select-multiselect listing_search">  
                              <?php foreach ($company_arr as $key=>$company) { ?>
                                <optgroup label='<?= $key ?>'>
                                  <?php    foreach ($company as $pkey =>$row) { ?>
                                      <option value="<?= $row['id'] ?>" <?=!empty($product_id) && in_array($row['id'],$product_id)?'selected="selected"':''?>><?= $row['name'] .' ('.$row['product_code'].')' ?></option>
                                  <?php } ?>
                                </optgroup>
                              <?php } ?>
                            </select>
                            <label>Products</label>
                          </div>
                        </div>
                        <!-- Add static input field add -->
                         <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control" name="name" value="">
                                <label>Member ID's</label>
                            </div>
                        </div>	
                        <!-- Add static input field end -->
                        <div class="col-sm-6">
                          <div class="form-group">
                            <select name="fee_status" class="form-control select2 placeholder listing_search" id="fee_status" >
                              <option value="" hidden selected="selected">&nbsp;</option>
                              
                              <option value="Active" <?= ($fee_status == 'Active') ? "selected='selected'" : '' ?>>Active</option>
                              <option value="Inactive" <?= ($fee_status == 'Inactive') ? "selected='selected'" : '' ?>>Inactive</option>
                            </select>
                            <label>Status</label>
                          </div>
                        </div>
                    </div>
                    <div class="panel-footer clearfix">
                    <button type="submit" class="btn btn-info" name="search" id="search" > <i class="fa fa-search"></i> Search </button>
                    <button type="button" class="btn btn-info btn-outline" name="viewall" id="viewall" onClick="window.location = 'associations.php'"> <i class="fa fa-search-plus"></i> View All</button>
                    
                    <button type="button" class="btn red-link" > <i class="fa fa-download"></i>  Export </button>
                    
                    <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
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
        	<div id="top_paginate_cont" class="pull-left">
                    <div class="form-inline text-right" id="DataTables_Table_0_length">
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
             <a class="btn btn-action pull-right" href="manage_association.php"><i class="fa fa-plus"></i> Membership</a>        
            <div class="clearfix"></div>        
            <div id="ajax_loader" class="ajex_loader" style="display: none;">
                <div class="loader"></div>
            </div>
            <div id="ajax_data" > </div>
        </div>
    </div>
    <script type="text/javascript">
      $(document).ready(function () {
        $(".added_date").mask("99/99/9999");
        $("#product").multipleSelect({selectableOptgroup: true, width:'100%' });

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

        ajax_submit();

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

        $(document).off("submit","#frm_search");
        $(document).on("submit","#frm_search",function(e){
            e.preventDefault();
            disable_search();
        });

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
				  common_select();
				  $(".popup").colorbox({iframe: true, width: '800px', height: '600px'});
              }
          });
          return false;
      }
      function delete_fee(fee_id) {
          swal({
              text: 'Delete Association: Are you sure?',
              showCancelButton: true,
              confirmButtonText: 'Confirm',
              cancelButtonText: 'Cancel',
          }).then(function () {
              $.ajax({
                  url: "ajax_delete_association_fee.php",
                  type: 'GET',
                  data: {id: fee_id},
                  success: function (res) {
                      if (res.status == 'success')
                      {
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
