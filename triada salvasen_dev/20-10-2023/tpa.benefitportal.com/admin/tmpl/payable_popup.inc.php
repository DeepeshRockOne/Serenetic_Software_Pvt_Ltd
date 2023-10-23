<div class="panel">
  <div class="panel-heading">
    <h4 class="panel-title">Commissions</h4>
  </div>
  <div class="panel-body">
    <div class="table-responsive">
      <table class="<?= $table_class ?> fs14">
          <thead>
              <tr>
                  <th>Member ID</th>
                  <th>Agent ID</th>
                  <th>Pay Period</th>
                  <th>Duration</th>
                  <th>Product</th>
                  <th>Earned</th>
                  <th>Advanced</th>
                  <th>PMPM</th>
                  <th>Past Reversal</th>
                  <th>Fees</th>
                  <th>Adjustment</th>
                  <th>Total</th>
              </tr>
          </thead>
          <tbody>
             <?php if($commissions){ ?>
                <?php foreach ($commissions as $k => $row) { 
                  $startPayPeriod = $endPayPeriod = ""; 
                  if($row['commission_duration'] == 'monthly'){
                    $startPayPeriod=date('m/01/Y', strtotime($row['pay_period']));
                      $endPayPeriod=date('m/d/Y', strtotime($row['pay_period']));
                  }else{
                    $startPayPeriod=date('m/d/Y', strtotime('-6 days', strtotime($row['pay_period'])));;
                      $endPayPeriod=date('m/d/Y', strtotime($row['pay_period']));
                  }

                ?>
                <tr>
                  <td><a href="javascript:void(0);"><?=$row['customer_id']?></a></td>
                  <td><a href="javascript:void(0);"><?=$row['rep_id']?></a></td>
                  <td><?=$startPayPeriod?><br><?=$endPayPeriod?></td>
                  <td><?=ucfirst($row['commission_duration']);?></td>
                  <td><?=$row['name'] . " (".$row['product_code'].")"?></td>
                  <td><?=dispCommAmt($row['earnedComm']); ?></td>
                  <td><?=dispCommAmt($row['advanceComm']); ?></td>
                  <td><?=dispCommAmt($row['pmpmComm']); ?></td>
                  <td><?=dispCommAmt($row['reverseComm'])?></td>
                  <td><?=dispCommAmt($row['feeComm'])?></td>
                  <td><?=dispCommAmt($row['adjustComm'])?></td>
                  <td><?=dispCommAmt($row['totalComm'])?></td>
                </tr>
                <?php } ?>
             <?php }else{ ?>
              <tr>
                <td colspan="12" class="text-center">No Records</td>
              </tr>
             <?php } ?>
          </tbody>
          <tfoot>
            <tr>
            <?php if($totalCommissions > 0 && !empty($commissions)) { ?>
                <td colspan="12">
                <?php echo $paginate->links_html; ?>
                </td>
            <?php } ?>
            </tr>
          </tfoot>
      </table>
      <br>
    </div>
    <div class="text-center m-t-10 m-b-10">
      Click here to view &nbsp;<a href="account_payable.php?order_id=<?=getname('orders',$order_id,'display_id','id')?>&is_from_all_orders=Y" class="red-link" target="_blank">Payables</a>
    </div>
  </div>
</div>

<script>

  $(document).ready(function(){
    common_select()
  });
      $(document).off('change', '.pagination_select');
    $(document).on('change', '.pagination_select', function(e) {
        e.preventDefault();
        $('panel-body').html('');
        var page_url = $(this).find('option:selected').attr('data-page_url');
        window.location.href=page_url
        common_select();
    });
  </script>