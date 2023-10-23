<?php if ($is_ajaxed) { ?>
    <div class="clearfix">
        <h4 class="m-t-0 pull-left"><?=get_pay_period_range_text($pay_period,'monthly');?></h4>
        <div class="pull-right">
            <p class="text-light-gray">As of <?=$last_comm_script_run_at;?></p>
        </div>
    </div>
    <div class="table-responsive">
         <table class="<?=$table_class?>">
      <thead>
        <tr class="data-head">
          <th width="90px">Date & <br/>Time</th>
          <th>Member Name</th>
          <th>Enrolling Agent</th>
          <th>Order ID</th>
          <th width="160px" class="text-left">Product</th>
          <th>Price</th>
          <th>Override %</th>
          <th>Earned</th>
          <th>Advanced</th>
          <th>PMPM</th>
          <th>Reversed</th>
          <th>Fee</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
         
      <?php
        if ($total_rows > 0) {
          $total = 0;
          foreach ($fetch_rows as $rows) {
      ?>
          <tr>
            <td><?= date('m/d/Y @ h:i a', strtotime($rows['orderDate'])); ?></td>
            <td><?=$rows['memberName']?></td>
            <td><?=$rows['enrollerName']?></td>
            <td><?=$rows['odrDispId']?></td>
            <td class="text-left"><?=$rows['prdName']?></td>
            <td><?=displayAmount($rows['unitPrice']);?></td>
            <td><?=($rows["overidePercentage"] > 0 ? $rows["overidePercentage"].'%' : displayAmount($rows["overideAmount"],2))?></td>
            <td><?=dispCommAmt($rows['earnedTotal'])?></td>
            <td><?=dispCommAmt($rows['advanceTotal'])?></td>
            <td><?=dispCommAmt($rows['pmpmTotal'])?></td>
            <td><?=dispCommAmt($rows['reverseTotal'])?></td>
            <td><?=dispCommAmt($rows['feeTotal'])?></td>
            <td class="text-right"><strong><?=dispCommAmt($rows['commTotal']);?></strong></td>
          </tr>
      <?php 
            $total += $rows['commTotal'];
            } 
        } else { 
      ?>
          <tr>
            <td colspan="13" class="text-center">No record(s) found</td>
          </tr>
      <?php }?>
      </tbody>
      <?php if ($total_rows > 0) {?>
        <tfoot>
          <tr>
            <td colspan="13" class="text-right">
              <strong>Total Commission:</strong>&nbsp; &nbsp; &nbsp; <?=dispCommAmt($total);?>
            </td>
          </tr>
          <tr>
            <td colspan="13">
              <?php echo $paginate->links_html; ?>
            </td>
          </tr>
        </tfoot>
      <?php }?>
    </table>
    </div>
<?php } else { ?>
<form id="frm_search" action="monthly_commission.php" method="GET" class="sform">
    <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
    <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
    <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
    <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
</form>
<div class="panel panel-default">
    <div class="panel panel-heading">
        <div class="panel-title">
            <h4 class="mn">Monthly Commissions - <span class="text-success"><?=dispCommAmt($header_commission_res['monthly']);?></span></h4>
        </div>
    </div>
    <div class="panel-body">
        <div id="ajax_data"></div>
        <div class="clearfix m-t-10 text-center">
            <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close();">Close</a>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
  dropdown_pagination('ajax_data')
    ajax_submit();
    $(document).off('click', '#ajax_data tr.data-head a');
    $(document).on('click', '#ajax_data tr.data-head a', function(e) {
        e.preventDefault();
        $('#sort_by_column').val($(this).attr('data-column'));
        $('#sort_by_direction').val($(this).attr('data-direction'));
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
});
function ajax_submit() {
    $('#ajax_loader').show();
    $('#ajax_data').hide();
    $('#is_ajaxed').val('1');
    var params=$('#frm_search').serialize();
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