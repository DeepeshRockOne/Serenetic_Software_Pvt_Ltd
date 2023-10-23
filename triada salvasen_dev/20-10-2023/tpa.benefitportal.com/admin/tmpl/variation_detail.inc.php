<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <div class="panel-title">
      <p class="fs18 fw500 mn">Commission Variation(s) <span class="fw300">(<?= $variation_res['name'] ?>)</span> <a href="commission_builder.php" class="btn red-link pull-right">Back</a></p>
    </div>
  </div>
</div>
<div class="panel panel-default panel-block">
  <div class="panel-body">
    <div class="table-responsive">
      <table class="<?= $table_class ?>">
        <thead>
          <div id="top_paginate_cont" class="pull-left">
            <div class="col-md-12">
              <div class="form-inline" id="DataTables_Table_0_length">
                <div class="form-group">
                  <label for="user_type">Records Per Page </label>
                </div>
                <div class="form-group">
                  <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);
                        ajax_submit();">
                    <option value="10" <?= !empty($_GET['pages']) && $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
                    <option value="25" <?= (!empty($_GET['pages']) && $_GET['pages'] == 25) || empty($_GET['pages']) ? 'selected' : ''; ?>>25</option>
                    <option value="50" <?= !empty($_GET['pages']) && $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
                    <option value="100" <?= !empty($_GET['pages']) && $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="btn-group pull-right"> 
            <a href="add_commission_rule.php?parentCommission=<?= $commission ?>" class="btn btn-action">+ Variation</a> </div>
          <tr class="data-head">
            <th>ID/Added Date</th>
            <th>ID/Product Name</th>
            <th class="text-center">Commissions</th>
            <th class="text-center">Agents #</th>
            <th width="150px">Status</th>
            <th width="100px">Actions</th>
          </tr>
        </thead>
        
        <tbody>
          <?php if(!empty($fetch_rows)) { ?>
            <?php foreach ($fetch_rows as $variationKey => $rows) { ?>
              <tr>
                <td>
                  <a href="add_commission_rule.php?commission=<?= $rows['id'] ?>&parentCommission=<?= $commission ?>" class="text-red fw600">
                    <?= $rows['rule_code'] ?>
                  </a><br />
                  <?= date($DATE_FORMAT,strtotime($rows['created_at'])) ?>
                </td>
                <td>
                  <a href="javascript:void(0);" data-id="<?= $rows['prod_id'] ?>" class="product_add_link text-red fw600">
                    <?php echo $rows['product_code']; ?>
                  </a><br />
                  <?php echo $rows['name']; ?>
                </td>
                <td class="text-center">
                  <a href="commission_per_level.php?commission=<?= $rows['id'] ?>" data-toggle="tooltip" title="View" class="commission_per_level_popup">
                    <i class="fa fa-eye fs18"></i>
                  </a>
                </td>
                <td class="text-center">
                  <a href="commission_agents_assigned.php?commission=<?= $rows['id'] ?>&total_agents=<?= $rows['agent_total'] ?>" class="text-red fw600 commission_agents_assigned">
                    <?= $rows['agent_total'] ?>
                  </a>
                </td>
                <td>
                  <div class="theme-form pr">
                    <select name="member_status" class="form-control member_status has-value" id="<?= $rows['id']; ?>" data-oldVal="<?= $rows['status'] ?>">
                      <option value="Active" <?php echo $rows['status'] == 'Active'?"selected":'';?>>Active</option>
                      <option value="Inactive" <?php echo $rows['status'] == 'Inactive'?"selected":'';?>>Inactive</option>
                    </select>
                    <label>Select</label>
                  </div>
                </td>
                <td class="icons text-right">
                    <a href="add_commission_rule.php?commission=<?= $rows['id'] ?>&parentCommission=<?= $commission ?>&is_clone=Y" data-toggle="tooltip" title="Duplicate"><i class="fa fa-clone" ></i></a>
                    <a href="add_commission_rule.php?commission=<?= $rows['id'] ?>&parentCommission=<?= $commission ?>" data-toggle="tooltip" title="Edit"><i class="fa fa-edit" ></i></a>
                    
                    <a href="javascript:void(0)" data-id="<?= $rows['id'] ?>" data-parent="<?= $commission ?>" class="delete_rule" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>
                </td>
              </tr>
            <?php } ?>
          <?php } else { ?>
            <tr>
              <td colspan="7">No record(s) found</td>
            </tr>
          <?php } ?>
        </tbody>
        <?php if(!empty($fetch_rows)) { ?>
          <tfoot>
            <tr>
              <td colspan="7"><?php echo $paginate->links_html; ?></td>
            </tr>
          </tfoot>
        <?php } ?>
      </table>
    </div>
  </div>
</div>
<script type="text/javascript">

    $(document).off('change', '.pagination_select');
    $(document).on('change', '.pagination_select', function(e) {
        e.preventDefault();
        var page_url = $(this).find('option:selected').attr('data-page_url');
        window.location.href=page_url
    });


    $(document).ready(function(){
      common_select();
      $('[data-toggle="popover"]').popover({html:true});
      $('[data-toggle="tooltip"]').tooltip();
      $(".commission_per_level_popup").colorbox({iframe: true, width: '800px', height: '400px'});
      $(".commission_agents_assigned").colorbox({iframe: true, width: '800px', height: '400px'});
    });
    $(document).off('click', '.product_add_link');
    $(document).on('click', '.product_add_link', function(e) {
        e.preventDefault();
        var product_id = $(this).attr('data-id');       
        window.location="product_builder.php?product_id=" + product_id;
    });

    $(document).off('click', '.delete_rule');
    $(document).on('click', '.delete_rule', function(e) {
      e.preventDefault();

      $commission = $(this).attr('data-id');
      $parentCommission = $(this).attr('data-parent');

      swal({
        text: "Delete Commission Rule: Are you sure?",
        showCancelButton: true,
        confirmButtonText: "Confirm",
      }).then(function() {
        $.ajax({
          url: '<?= $ADMIN_HOST ?>/ajax_delete_commission_rule.php',
          data: {commission:$commission},
          dataType:'JSON',
          method: 'POST',
          success: function(data) {
            if (data.status == 'success') {
              window.location = 'variation_detail.php?commission='+$parentCommission;
            } else {
              setNotifyError("Something went wrong");

            }
          }
        });
      });
    });


    $(document).off('change', '.member_status');
    $(document).on('change', '.member_status', function(e) {
      e.stopPropagation();
      $commission = $(this).attr('id');
      $status = $(this).val();
      $oldStatus = $(this).attr('data-oldVal');
      swal({
        text: "Change Commission Status to <strong class='text-blue'>"+$status+"</strong>: Are you sure?",
        showCancelButton: true,
        confirmButtonText: "Confirm",
      }).then(function() {
        $.ajax({
          url: "<?= $ADMIN_HOST ?>/ajax_commission_status_change.php",
          data: {commission:$commission,status:$status},
          dataType:'JSON',
          type: 'POST',
          success: function(res) {
            if(res.status=='success'){
                setNotifySuccess(res.msg);
            }
          }
        });
      }, function(dismiss) {
          $("#"+$commission).val($oldStatus);
          $("#"+$commission).selectpicker('refresh');
      });
    });
  </script>