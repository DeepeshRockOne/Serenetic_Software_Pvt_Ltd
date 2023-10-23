    <form id="frm_WeeklySearch" action="weekly_commission_statement.php" method="GET">
    <?php if ($total_rows > 0) {?>
        <div class="clearfix tbl_filter m-b-15">
          <div class="pull-left">
            <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
              <div class="form-group mn">
                <label for="user_type">Records Per Page </label>
              </div>
              <div class="form-group mn">
                <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);weekly_ajax_submit();">
                  <option value="10" <?php echo isset($_GET['pages']) && $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
                  <option value="25" <?php echo (isset($_GET['pages']) && $_GET['pages'] == 25) || (isset($_GET['pages']) && $_GET['pages'] == "") ? 'selected' : ''; ?>>25</option>
                  <option value="50" <?php echo isset($_GET['pages']) && $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
                  <option value="100" <?php echo isset($_GET['pages']) && $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
                </select>
              </div>
            </div>
          </div>
    </div>
      <?php }?>
      <div class="table-responsive">
          <table class="<?=$table_class?>">
            <thead>
            <tr class="data-head">
                <th ><a href="javascript:void(0);">Earning Period</a></th>
                <th ><a href="javascript:void(0);">Status</a></th>
               
                <th><a href="javascript:void(0);">Credit</a></th>
                <th><a href="javascript:void(0);">Debit</a></th>
                <th><a href="javascript:void(0);">Total</a></th>
                <th class="text-center" width="130px"><a href="javascript:void(0);">Statements</a></th>
            </tr>
        </thead>
        <tbody>
            <?php 
              if ($total_rows > 0){
                $weeklyPayPeriod = $commObj->getWeeklyPayPeriod(date('Y-m-d'));

                foreach ($fetch_rows as $rows) { 
                    $start_pay_period = date('Y-m-d', strtotime('-6 days', strtotime($rows['pay_period'])));
                    $creditAmt = $rows['credit_amount'];
                    $debitAmt = $rows['debit_amount'];
                    $totalAmt = $creditAmt + $debitAmt;
                    
                    $payPeriodStatus = $rows['status'];
                    if(strtotime($weeklyPayPeriod) == strtotime($rows['pay_period'])){
                        $payPeriodStatus = "In Progress";
                    }
            ?>
                <tr>
                    <td><a href="javascript:void(0)" class="paidWeeklyCommPopover" data-payPeriod="<?=$rows['pay_period']?>" data-agentId="<?=$rows['customer_id']?>"><?php echo displayDate($start_pay_period) . " - " . displayDate($rows['pay_period']);?></a>
                      <div id="weekly_popover_content_wrapper_<?=$rows['customer_id']?>" style="display: none"></div>
                    </td>
                    <td><?=$payPeriodStatus?></td>
                    <td><?=dispCommAmt($creditAmt)?></td>
                    <td><?=dispCommAmt($debitAmt)?></td>
                    <td><?=dispCommAmt($totalAmt)?></td>
                    <td class="icons no-wr">
                      <div class="text-center">
                          <a class="weekly_detail_popup" href="weekly_commission_details_popup.php?pay_period=<?=$rows['pay_period']?>"><i class="fa fa-lg fa-eye text-blue" data-toggle='tooltip' title="View Statements"></i></a>
                          <a href="commission_export_csv.php?commission_duration=weekly&agentIds=<?=$rows['customer_id']?>&pay_period=<?=$rows['pay_period']?>" class="exportCSV" data-toggle="tooltip" id="Export_csv" name="Export_csv" title="" data-original-title="Export CSV"><i class="fa fa-file-excel-o"></i></a>
                      </div>
                    </td>
                </tr>
            <?php 
                 }
                }else{
            ?>
                <tr>
                    <td colspan="6" class="text-center">No record(s) found</td>
                </tr>
            <?php } ?>
        </tbody>
                 <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
         <?php if ($total_rows > 0) {?>
           <tfoot>
        <tr>
          <td colspan="6">
            <?php echo $paginate->links_html; ?>
          </td>
        </tr>
      </tfoot>
        <?php }?>
    </table>
    </form>
</div>

<script type="text/javascript">

    $(document).ready(function(){
      $(document).off('click', '#wekkly_commission_statements_div ul.pagination li a');
      $(document).on('click', '#wekkly_commission_statements_div ul.pagination li a', function (e){
          e.preventDefault();
          $('#ajax_loader').show();
          $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#wekkly_commission_statements_div').html(res).show();
                $('.weekly_detail_popup').colorbox({iframe:true, width: '85%', height:'565px'});
                common_select();
                $('[data-toggle="tooltip"]').tooltip();
            }
          });
      });

      weeklyContentCalled = false;
      $('.paidWeeklyCommPopover').popover({
        html: true,
        container: 'body',
        trigger: 'hover',
        template: '<div class="popover weekly_popover"><div class="arrow"></div><div class="popover-content"></div></div>',
        content: function() {
          var $agentId = $(this).attr('data-agentId');
          var $payPeriod = $(this).attr('data-payPeriod');
          getWeeklyPopoverData($agentId,$payPeriod,'weekly');
          return $('#weekly_popover_content_wrapper_' + $agentId).html();
        }
      });
      $('body').on('click', function (e) {
          $('.paidWeeklyCommPopover').each(function () {
              // hide any open popovers when the anywhere else in the body is clicked
              if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.weekly_popover').has(e.target).length === 0) {
                  $(this).popover('hide');
              }
          });
      });
      
    });


function weekly_ajax_submit(){
        $('#ajax_loader').show();
        $('#wekkly_commission_statements_div').hide();

        var params = $('#frm_WeeklySearch').serialize();
        var weekly_cpage = $('#nav_page').val();

        $.ajax({
            url: $('#frm_WeeklySearch').attr('action'),
            type: 'GET',
            data: params,
            success: function (res) {
                $('#ajax_loader').hide();
                $('#wekkly_commission_statements_div').html(res).show();
                $('.weekly_detail_popup').colorbox({iframe:true, width: '85%', height:'565px'});
                common_select();
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
  return false;
}
function getWeeklyPopoverData($agentId,$payPeriod,$commissionDuration) {
  if (!weeklyContentCalled) {
    weeklyContentCalled = true;
    return " ";
  } else {
    $.ajax({
      url: '<?=$HOST?>/get_commission_payment_popover.php',
      data: {
        agentId: $agentId,payPeriod : $payPeriod,commissionDuration : $commissionDuration
      },
      method: 'GET',
      async: false,
      success: function(res) {
        weeklyContentCalled = false;              
        $('#weekly_popover_content_wrapper_' + $agentId).html(res);
      }
    });
  }
}

</script>