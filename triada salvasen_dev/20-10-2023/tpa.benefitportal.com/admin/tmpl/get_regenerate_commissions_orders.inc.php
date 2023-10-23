<div class="white-box">
    <div class="clearfix m-b-15">
        <h4 class="m-t-0 pull-left">Effected Orders</h4>
        <?php if ($total_rows > 0) { ?>
            <div class="pull-right">
              <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
                <div class="form-group mn height_auto">
                  <label for="user_type">Records Per Page </label>
                </div>
                <div class="form-group mn height_auto">
                  <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);getEffectedOrders();">
                    <option value="10" <?php echo isset($_GET['pages']) && $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
                    <option value="25" <?php echo (isset($_GET['pages']) && $_GET['pages'] == 25) || (isset($_GET['pages']) && $_GET['pages'] == "") ? 'selected' : ''; ?>>25</option>
                    <option value="50" <?php echo isset($_GET['pages']) && $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
                    <option value="100" <?php echo isset($_GET['pages']) && $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
                  </select>
                </div>
              </div>
            </div>
        <?php } ?>
    </div>
  <div class="table-responsive">
    <table class="<?=$table_class?>">
      <thead>
        <tr>
          <th>Order ID/Added Date</th>
          <th>Member Name/ID</th>
          <th>Enrolling Agent/ID</th>
          <th>Sale Type</th>
          <th>Status/Transaction ID</th>
          <th class="text-center">Plan Period</th>
          <th>Merchant Processor</th>
          <th>Order Total</th>
          <th class="text-center" width="100px">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($total_rows > 0) {
            foreach ($fetch_rows as $order) { 
        ?>
        <tr>
          <td><a href="javascript:void(0);" class="text-red fw500 odrReceipt" data-odrId="<?=md5($order["odrID"])?>"><?=$order['odrDispId']?></a><br><?=date("m/d/Y",strtotime($order['odrDate']))?></td>
              <td><?=$order['mbrName']?><br> <a href="members_details.php?id=<?=md5($order['mbrId'])?>" target="_blank" class="text-red fw500"><?=$order['mbrDispId']?></a></td>
              <td><?=$order['agentDispId']?><br> <a href="agent_detail_v1.php?id=<?=md5($order['agentId'])?>" target="_blank" class="text-red fw500"><?=$order['agentName']?></a></td>
              <td><?=$order['saleType'] == 'Y' ? "Renewal" : "New Business"?></td>
              <td><?=$order['odrStatus']?><br><?=$order['transactionId']?></td>
              <td class="text-center">
                <?php if($order["minCov"] != $order["maxCov"]){
                  echo "P".$order["minCov"]." +";
                }else{
                  echo "P".$order["minCov"];
                }?>
              </td>  
              <td><?=$order["processorName"]?></td>
              <td><?=displayAmount($order["odrTotal"],2)?></td>

    
          <td class="icons text-center">
            <a href="javascript:void(0);" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Receipt" class="odrReceipt"  data-odrId="<?=md5($order["odrID"])?>"><i class="fa fa-file-text" aria-hidden="true"></i></a>
          </td>
        </tr>
        <?php 
                } 
            } 
        ?>
      </tbody>
        <?php 
            if ($total_rows > 0) {?>
                <tfoot>
                    <tr>
                        <td colspan="9">
                            <?php echo $paginate->links_html; ?>
                        </td>
                    </tr>
                </tfoot>
        <?php }?>
  </table>
</div>
<hr class="m-t-0">
  <div class="text-center ">
      <a href="javascript:void(0);" class="btn btn-action" id="regenerateCommissionBtn">Regenerate</a>
      <a href="javascript:void(0);" class="btn red-link" onclick="$('#effOrdersDiv').hide();">Cancel</a>
    </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){

    $(document).off('click', '.odrReceipt');
    $(document).on('click', '.odrReceipt', function(e) {
      var odrId = $(this).attr("data-odrId");
      openOdrReceipt(odrId);
    });

    $(document).off('click', '#effOrdersDiv ul.pagination li a');
    $(document).on('click', '#effOrdersDiv ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#effOrdersDiv').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#effOrdersDiv').html(res).show();
                common_select();
            }
        });
    });

  });


  openOdrReceipt = function(odrId){
    $href = "order_receipt.php?orderId="+odrId;
    var not_win = window.open($href, "myWindow", "width=1024,height=630");
    if(not_win.closed) {  
      alert('closed');  
    } 
  }
</script>