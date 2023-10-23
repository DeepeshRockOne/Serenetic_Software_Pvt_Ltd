<?php if ($total_rows > 0) { ?>
  <div class="clearfix m-b-15">
    <div class="pull-right">
      <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
        <div class="form-group mn height_auto">
          <label for="user_type">Records Per Page </label>
        </div>
        <div class="form-group mn height_auto">
          <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);loadWalletDiv();">
            <option value="10" <?php echo isset($_GET['pages']) && $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
            <option value="25" <?php echo (isset($_GET['pages']) && $_GET['pages'] == 25) || (isset($_GET['pages']) && $_GET['pages'] == "") ? 'selected' : ''; ?>>25</option>
            <option value="50" <?php echo isset($_GET['pages']) && $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
            <option value="100" <?php echo isset($_GET['pages']) && $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
          </select>
        </div>
      </div>
    </div>
  </div>
<?php } ?>
<div class="table-responsive">
  <table class="<?= $table_class ?>">
    <thead>
      <tr>
        <th>As of Date</th>
        <th>Agent ID/Name</th>
        <th class="text-center">Balance</th>
        <th width="90px">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($total_rows > 0) {
        foreach ($fetch_rows as $rows) {
      ?>
          <tr>
            <td><?= date("m/d/Y") ?></td>
            <td><a href="javascript:void(0);" class="fw500 text-action"><?= $rows["agentDispId"] ?></a><br><?= $rows["agentName"] ?></td>
            <td class="text-center"><?=dispCommAmt($rows["balance"],2)?></td>
            <td class="icons">
              <a href="commission_wallet_history.php?agentId=<?= md5($rows["agentId"]) ?>&walletId=<?= md5($rows["walletId"]) ?>" data-toggle="tooltip" data-trigger="hover" title="Wallet" data-placement="top" class="commission_wallet_history"><i class="ti-wallet" aria-hidden="true"></i></a>
              <?php if($rows["balance"]!=0){ ?>
              <a href="javascript:void(0)" data-agentId="<?= md5($rows["agentId"]) ?>" data-walletId="<?= md5($rows["walletId"]) ?>" data-toggle="tooltip" data-trigger="hover" title="Wallet to debit" data-placement="top" class="wallet_transfer"><i class="ti-move" aria-hidden="true"></i></a>
              <?php } ?>
            </td>
          </tr>
        <?php } ?>
      <?php } else { ?>
        <tr>
          <td colspan="4" class="text-center">No record(s) found</td>
        </tr>
      <?php } ?>
    </tbody>
    <?php
    if ($total_rows > 0) { ?>
      <tfoot>
        <tr>
          <td colspan="4">
            <?php echo $paginate->links_html; ?>
          </td>
        </tr>
      </tfoot>
    <?php } ?>
  </table>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $(".commission_wallet_history").colorbox({
      iframe: true,
      width: '990px',
      height: '500px'
    });
  });
  $(document).off('click', '#walletDiv ul.pagination li a');
  $(document).on('click', '#walletDiv ul.pagination li a', function(e) {
    e.preventDefault();
    $('#ajax_loader').show();
    $('#walletDiv').hide();
    $.ajax({
      url: $(this).attr('href'),
      type: 'GET',
      success: function(res) {
        $('#ajax_loader').hide();
        $('#walletDiv').html(res).show();
        common_select();
        $('[data-toggle="tooltip"]').tooltip();
      }
    });
  });

  $(document).off('click', '.wallet_transfer');
  $(document).on('click', '.wallet_transfer', function() {
    var agentId = $(this).attr('data-agentId');
    var walletId = $(this).attr('data-walletId');
    parent.swal({
      text: "Apply to Debit Balance: Are you sure?",
      showCancelButton: true,
      confirmButtonText: "Confirm",
    }).then(function() {
      $.ajax({
        url: 'ajax_wallet_apply_to_debit_balance.php',
        method: 'POST',
        data: {
          agent_id: agentId,
          walletId: walletId,
        },
        dataType: 'json',
        beforeSend: function(e) {
          $("#ajax_loader").show();
        },
        success: function(res) {
          $("#ajax_loader").hide();
          if (res.status == "success") {
            parent.setNotifySuccess('Wallet balance applied to debit balance successfully');
          } else {
            //parent.setNotifyError('Something went wrong');
          }
          window.location.reload();
        }
      });
    }, function(dismiss) {

    });
  });
</script>