<?php if ($is_ajaxed) { ?>
  <div class="table-responsive">
      <table class="<?=$table_class?>">
        <thead>
          <tr>
            <th>Transaction Date</th>
            <th>Direct Deposit</th>
            <th>Description</th>
            <th>Pay Period</th>
            <th>Type</th>
            <th>Credit</th>
            <th >Debit</th>
            <th class="text-right">Balance</th>
          </tr>
        </thead>
         <tbody>
                <?php if ($total_rows > 0) {
                foreach ($fetch_rows as $rows) { 
                  $pay_period = $rows["pay_period"];

                  if($rows["commission_duration"] == "weekly"){
                    $startPayPeriod=date('m/d/Y', strtotime('-6 days', strtotime($pay_period)));;
                    $endPayPeriod=date('m/d/Y', strtotime($pay_period));
                  }else{
                    $startPayPeriod=date('m/01/Y', strtotime($pay_period));
                    $endPayPeriod=date('m/d/Y', strtotime($pay_period));
                  }
                  
                  if(!empty($rows['deposit_detail'])){
                    $deposit_detail = json_decode($rows['deposit_detail'],true);
                    if($deposit_detail['account_number']){
                        $rows['account_number'] = $deposit_detail['account_number'];
                    }
                  }
                ?>
                <tr>
                  <td><?=date("m/d/Y",strtotime($rows["transDate"]))?></td>
                   <td><?=isset($rows['account_number']) ? '*' . substr($rows['account_number'], -4) : '-'?></td>
                   <td><?=$rows["message"]?></td>
                   <td><?=$startPayPeriod .' - '. $endPayPeriod?></td>
                   <td>
                   <?php
                    $comm_type=$rows["paid_to_agent"]!=0?'Earned':"";
                    $comm_type.=$rows["pmpm_to_agent"]!=0? '/PMPM':"";
                    echo trim($comm_type,'/');

                    ?>
                   </td>
                   <td><?=$rows["creditAmt"] != '-' ? dispCommAmt($rows["creditAmt"],2) : "-"?></td>
                   <td><?=$rows["debitAmt"] != '-' ? dispCommAmt($rows["debitAmt"],2) : "-"?></td>
                   <td><?=dispCommAmt($rows["current_balance"],2)?></td>
                  </tr>
                    <?php }?>
                <?php } else {?>
                    <tr>
                        <td colspan="7" class="text-center">No record(s) found</td>
                    </tr>
                <?php } ?>
            </tbody>
            <?php if ($total_rows > 0) {?>
            <tfoot>
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
  <form id="frm_search" action="commission_wallet_history.php" method="GET">
    <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
    <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
    <input type="hidden" name="agentId" id="agentId" value="<?=$agentId;?>" />
</form>

<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<h4 class="mn">Commission Wallet History -<span class="fw300"> <?=$agentRes["agentName"]?> (<?=$agentRes["agentDispId"]?>)</span></h4>
	</div>
	<div class="panel-body">
		<div id="ajax_data">
    </div>
    <div class="text-center m-t-20">
      <a href="javascript:void(0);" class="btn btn-action" data-agentId="<?=$agentId?>" id="exportBtn">Export</a>
       <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Close</a>
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


    $(document).off("click","#exportBtn");
    $(document).on('click', '#exportBtn', function(e) {
      e.preventDefault();
      $agentId = $(this).attr("data-agentId");
      window.location="commission_wallet_history_export_csv.php?agentId="+$agentId;
      setTimeout(function(){ window.parent.location.reload(); }, 3000);
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
