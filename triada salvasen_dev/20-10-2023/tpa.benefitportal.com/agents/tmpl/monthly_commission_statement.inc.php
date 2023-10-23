     <form id="frm_MonthlySearch" action="monthly_commission_statement.php" method="GET">
       <?php if ($total_rows > 0) {?>
       <div class="clearfix tbl_filter m-b-15">
        <div class="pull-left">
          <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
            <div class="form-group mn">
              <label for="user_type">Records Per Page </label>
            </div>
            <div class="form-group mn">
              <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);monthly_ajax_submit();">
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
                $monthlyPayPeriod = $commObj->getMonthlyPayPeriod(date('Y-m-d'));

                foreach ($fetch_rows as $rows) { 

                    $startPayPeriod=date('m/01/Y', strtotime($rows['pay_period']));
                    $endPayPeriod=date('m/d/Y', strtotime($rows['pay_period']));

                    $creditAmt = $rows['credit_amount'];
                    $debitAmt = $rows['debit_amount'];
                    $totalAmt = $creditAmt + $debitAmt;

                    $payPeriodStatus = $rows['status'];
                    if(strtotime($monthlyPayPeriod) == strtotime($rows['pay_period'])){
                        $payPeriodStatus = "In Progress";
                    }

            ?>
            <tr>
              <td>
                <a href="javascript:void(0)" class="paidMonthlyCommPopover" data-payPeriod="<?=$rows['pay_period']?>" data-agentId="<?=$rows['customer_id']?>"><?php echo displayDate($startPayPeriod) . " - " . displayDate($endPayPeriod);?></a>
                <div id="monthly_popover_content_wrapper_<?=$rows['customer_id']?>" style="display: none"></div>
              </td>
            <td><?=$payPeriodStatus?></td>
            <td><?=dispCommAmt($creditAmt)?></td>
            <td><?=dispCommAmt($debitAmt)?></td>
            <td><?=dispCommAmt($totalAmt)?></td>
            <td class="icons text-center">
                 <a  data-toggle="tooltip" href="monthly_commission_details_popup.php?pay_period=<?=$rows['pay_period']?>" class="monthly_detail_popup"><i class="fa fa-eye" data-toggle='tooltip' title="View Statements"></i></a>
                 <a href="commission_export_csv.php?commission_duration=monthly&agentIds=<?=$rows['customer_id']?>&pay_period=<?=$rows['pay_period']?>" class="exportCSV" data-toggle="tooltip" id="Export_csv" name="Export_csv" title="" data-original-title="Export CSV"><i class="fa fa-file-excel-o"></i></a>
            </td>
            </tr>
            <?php 
                }
              }else{
            ?>
            <td colspan="6" class="text-center">No record(s) found</td>
            <?php
              }
            ?>
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
</div>   

<script type="text/javascript">

    $(document).ready(function(){
      
      $(document).off('click', '#monthly_commission_statements_div ul.pagination li a');
      $(document).on('click', '#monthly_commission_statements_div ul.pagination li a', function (e) {
          e.preventDefault();
          $('#ajax_loader').show();
          $.ajax({
            url : $(this).attr('href'),
            type : 'GET',
            success: function(res){
              $('#ajax_loader').hide();
              $('#monthly_commission_statements_div').html(res).show();
              $('.monthly_detail_popup').colorbox({iframe:true, width: '85%', height:'90%'});
              common_select();
            }
          });
      });

      monthlyContentCalled = false;
      $('.paidMonthlyCommPopover').popover({
        html: true,
        container: 'body',
        trigger: 'hover',
        template: '<div class="popover monthly_popover"><div class="arrow"></div><div class="popover-content"></div></div>',
        content: function() {
          var $agentId = $(this).attr('data-agentId');
          var $payPeriod = $(this).attr('data-payPeriod');
          getMonthlyPopoverData($agentId,$payPeriod,'monthly');
          return $('#monthly_popover_content_wrapper_' + $agentId).html();
        }
      });
      $('body').on('click', function (e) {
          $('.paidMonthlyCommPopover').each(function () {
              // hide any open popovers when the anywhere else in the body is clicked
              if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.monthly_popover').has(e.target).length === 0) {
                  $(this).popover('hide');
              }
          });
      });

       
    });

    function monthly_ajax_submit(){
        $('#ajax_loader').show();
        $('#monthly_commission_statements_div').hide();

        var params = $('#frm_MonthlySearch').serialize();
        var monthly_cpage = $('#monthly_nav_page').val();
   
        $.ajax({
            url: $('#frm_MonthlySearch').attr('action'),
            type: 'GET',
            data: params,
            success: function (res) {
                $('#ajax_loader').hide();
                $('#monthly_commission_statements_div').html(res).show();
                $('.monthly_detail_popup').colorbox({iframe:true, width: '85%', height:'90%'});
                common_select();
            }
        });
        return false;
    }
    function getMonthlyPopoverData($agentId,$payPeriod,$commissionDuration) {
      if (!weeklyContentCalled) {
        monthlyContentCalled = true;
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
            monthlyContentCalled = false;              
            $('#monthly_popover_content_wrapper_' + $agentId).html(res);
          }
        });
      }
    }
</script>