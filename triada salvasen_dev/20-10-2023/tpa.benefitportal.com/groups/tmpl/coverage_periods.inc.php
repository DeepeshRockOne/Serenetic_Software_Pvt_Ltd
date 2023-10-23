<div class="container">
   <div class="section-padding "> 
        <div class="panel panel-default panel-block">
                <div class="panel-body">
                    <div class="clearfix m-b-10">
                        <div class="pull-left">
                            <h4 class="m-t-7">Plan Periods</h4>
                        </div>
                        <div class="pull-right">
                            <a href="add_coverage_periods.php" class="btn btn-action">+  Plan Period</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="<?=$table_class?>">
                            <thead>
                                <tr>
                                    <th class="text-left">ID/Added</th>
                                    <th>Plan Period Name</th>
                                    <th class="text-center">Classes</th>
                                    <th>Plan Period Start/End</th>
                                    <th class="text-center">Product Contributions</th>
                                    <th>Status</th>
                                    <th width="90px">Actions</th>
                                </tr>
                            </thead> 
                            <tbody>
                                <?php if(!empty($resCoveragePeriod)) { ?>
                                    <?php foreach ($resCoveragePeriod as $key => $value) { ?>
                                        <tr id="tr_<?= $value['id'] ?>">
                                            <td> 
                                                <a href="javascript:void(0);" class="text-action"><strong><?= $value['display_id'] ?></strong></a><br><?= date('m/d/Y',strtotime($value['created_at'])) ?>
                                            </td>
                                            <td><?= $value['coverage_period_name'] ?></td>
                                            <td class="text-center icons"><a href="coverage_periods_view.php?coverage=<?= $value['id'] ?>" class="coverage_periods_view" data-toggle="tooltip" title="View"><i class="fa fa-eye"></i></a></td>
                                            <td ><?= date('m/d/Y',strtotime($value['coverage_period_start'])) ?> - <?= date('m/d/Y',strtotime($value['coverage_period_end'])) ?></td>
                                            <td class="text-center"><?= !empty($value['product_contribution']) ? 'Yes' : 'No' ?></td>
                                            <td class="theme-form">
                                                <div class="theme-form w-200 pr">
                                                    <select class="form-control has-value" name="coverage_status" id="coverage_status_<?= $value['id'] ?>" data-id="<?= $value['id'] ?>" data-status="<?= $value['status'] ?>" onchange="changeStatus('<?= $value['id']; ?>', this.value, '<?=$value['status'];?>')">
                                                        <option></option>
                                                        <option value="Active" <?= $value['status'] == "Active" ? "selected" : '' ?>>Active</option>
                                                        <option value="Inactive" <?= $value['status'] == "Inactive" ? "selected" : '' ?>>Inactive</option>
                                                    </select>
                                                    <label>Select</label>
                                                </div>
                                            </td>
                                            <td class="icons">
                                                <a href="add_coverage_periods.php?coverage=<?= $value['id'] ?>&clone=Y" data-toggle="tooltip" title="Clone"><i class="fa fa-clone"></i></a>
                                                <a href="add_coverage_periods.php?coverage=<?= $value['id'] ?>" data-toggle="tooltip" title="Edit"><i class="fa fa-edit"></i></a>
                                                <a href="javascript:void()" data-id="<?= $value['id'] ?>" data-toggle="tooltip" title="Delete" class="delete_coverage"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php }else{ ?>
                                    <tr><td colspan="7" class="text-center">No Record(s) Found</td></tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $(".coverage_periods_view").colorbox({iframe:true, width:"500px",height:"550px"});
});

function changeStatus(id, coverage_status, old_val) {
    swal({
          text: "Change Status: Are you sure?",
          showCancelButton: true,
          confirmButtonText: "Confirm",
    }).then(function() {
        $.ajax({
              url: 'ajax_change_coverage_status.php',
              data: {
                  id: id,
                  status: coverage_status
              },
              method: 'POST',
              dataType: 'json',
              success: function(res) {
                  if (res.status == "success") {
                      setNotifySuccess(res.msg);
                  }else{
                      setNotifyError(res.msg);
                  }
              }
        });
          
    }, function(dismiss) {
        $('#coverage_status_' + id).val(old_val);
        $('select.form-control').selectpicker('render');
        return false;
    })
}

$(document).off("click",".delete_coverage");
$(document).on("click",".delete_coverage",function(){
    var id = $(this).attr('data-id');
    swal({
          text: "Delete Record: Are you sure?",
          showCancelButton: true,
          confirmButtonText: "Confirm",
    }).then(function() {
        $.ajax({
            url: 'ajax_delete_coverage_period.php',
             data: {
                  id: id,
            },
            method: 'POST',
            dataType: 'json',
            success: function(res) {
                if (res.status == "success") {
                    $("#tr_"+id).remove();
                    setNotifySuccess(res.msg);
                }else{
                    setNotifyError(res.msg);
                }
            }
        });
          
    }, function(dismiss) {
    })
});
</script>

    
  