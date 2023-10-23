<?php if($is_ajaxed){ ?>
    <div class="table-responsive">
    <table class="<?=$table_class?> ">
        <thead>
            <tr class="data-head">
                <th style="width: 130px;">Added Date</th>
                <th>Admin ID/Name</th>
                <th>Payment Mode</th>
                <th>Amount</th>
                <th>Transaction Status</th>
                <th>Error Text</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($total_rows > 0) { ?>
                <?php foreach ($fetch_rows as $rows) { ?>
                    <tr>
                        <td><?=$tz->getDate($rows['created_at'],'m/d/Y h:i:s A')?></td>
                        <td><strong class="text-action"><?=$rows['display_id']?></strong><br><?=$rows['admin_name']?></td>
                        <td><?=$rows['payment_mode']?></td>
                        <td><?=displayAmount($rows['amount'])?></td>
                        <td><strong class="text-action"><?=$rows['status']?></strong><br><?=$rows['transaction_id']?></td>
                        <td><?=$rows['decline_text']?></td>
                    </tr>
                <?php }?>
            <?php } else {?>
                <tr>
                    <td colspan="6" align="center">No record(s) found</td>
                </tr>
            <?php }?>
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
<?php }else { ?>
<form id="frm_search" action="test_processor_history_popup.php" method="GET" class="theme-form">
    <input type="hidden" name="is_ajaxed" id="is_ajaxed" value=""/>
    <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>"/>
    <input type="hidden" name="pay_id" id="pay_id" value="<?=$payment_master_id;?>"/>
</form>
<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <div class="panel-title">
      <h4 class="mn">Test Processor History - <span class="fw300"><?=$pro_name?></span></h4>
    </div>
  </div>
<div class="panel panel-default panel-block">
    <div id="ajax_loader" class="ajex_loader" style="display: none;">
    <div class="loader"></div>
    </div>
    <div id="ajax_data" class="panel-body"></div>
</div>
</div>
<script type="text/javascript">
$(document).ready(function(e){
    dropdown_pagination('ajax_data')
    ajax_submit();
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
            common_select();
        }
    });
    return false;
}
</script>
<?php } ?>
