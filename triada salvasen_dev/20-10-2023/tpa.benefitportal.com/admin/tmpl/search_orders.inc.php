<style type="text/css">
  .popover {
    max-width: 600px;
  }
  .payment_pending_error{
    color: #F6BB42;
  }
  .payment_dec_error,.payment_refund_error,.payment_charged_back_error{
    color: red;
  }
  #ajax_loader {
    z-index: 100000;
  }
</style>
<?php if ($is_ajaxed) { ?>
	<div class="table-responsive">
    <br>
    <table class="<?= $table_class ?>">
      <thead>
        <tr class="data-head">
         	<th><a href="javascript:void(0);" data-column="o.display_id" data-direction="<?php echo $SortBy == 'o.display_id' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Order ID</a></th>
          <th><a href="javascript:void(0);" data-column="c.fname" data-direction="<?php echo $SortBy == 'c.fname' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Member Name</a></th>
          <th><a href="javascript:void(0);" data-column="c.rep_id" data-direction="<?php echo $SortBy == 'c.rep_id' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">User ID</a></th>
          <th><a href="javascript:void(0);" data-column="s.fname" data-direction="<?php echo $SortBy == 's.fname' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Referring Agent</a></th>
          <th><a href="javascript:void(0);" data-column="o.status" data-direction="<?php echo $SortBy == 'o.status' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Status</a></th>
          <th><a href="javascript:void(0);" data-column="o.is_renewal" data-direction="<?php echo $SortBy == 'o.is_renewal' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Sale Type</a></th>
          <th><a href="javascript:void(0);" data-column="o.sub_total" data-direction="<?php echo $SortBy == 'o.sub_total' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Total</a></th>
          <th width="10%">Actions</th>
       	</tr>
      </thead>
      <tbody>
        <?php if ($total_rows > 0) {
          foreach ($fetch_rows as $rows) {
            $is_initial = $rows['initial_grand_total'] > 0 ? 'Y' : 'N';
            $tr_bg = '';
            if ($rows['status'] == 'Chargeback' || $rows['status'] == 'Returned' || $rows["status"] == 'Payment Declined') {
              $tr_bg .= 'bg_red';
            }
            $td_background_color = "";?>
            <tr class="<?= $tr_bg; ?>">
            	<td>
            		<a href="javascript:void(0);" id="<?= $all_order_menu ?>" data-id="<?php echo $rows['id']; ?>" class="order_popup all_order_link"> <?php echo $rows['display_id'] ?></a></br><?php echo date('m/d/Y', strtotime($rows['created_at'])); ?>
            	</td>
            	<td>
            		<?php if ($rows['c_type'] == 'Agent') { ?>
                  <a href="javascript:void(0);" data-id="<?php echo $rows['customer_id']; ?>" id="<?= $agent_menu ?>" class="agent_link"><?php echo $rows['c_name']; ?></a>
                <?php } elseif ($rows['c_type'] == 'Affiliates') { ?>
                  <a href="javascript:void(0);" data-id="<?php echo $rows['customer_id']; ?>" id="<?= $affiliates_menu ?>" class="affiliates_link"><?php echo $rows['c_name']; ?></a>
                <?php } else { ?>
                 	<a href="javascript:void(0);" data-id="<?php echo $rows['customer_id']; ?>" id="<?= $member_menu ?>" class="member_link"><?php echo $rows['c_name']; ?></a>
                <?php } ?>
              </td>
              <td <?php echo $td_background_color; ?>>
              	<?php if ($rows['c_type'] == 'Agent') { ?>
              		<a href="javascript:void(0);" data-id="<?php echo $rows['customer_id']; ?>" id="<?= $agent_menu ?>" class="agent_link"><?php echo $rows['c_rep_id']; ?></a><br><?php echo $rows['c_type'] ?>
                <?php } elseif ($rows['c_type'] == 'Affiliates') { ?>
                	<a href="javascript:void(0);" data-id="<?php echo $rows['customer_id']; ?>" id="<?= $affiliates_menu ?>" class="affiliates_link"><?php echo $rows['c_rep_id']; ?></a><br><?php echo $rows['c_type'] ?>
                <?php } elseif ($rows['c_type'] == 'Customer') { ?>
                  <a href="javascript:void(0);" data-id="<?php echo $rows['customer_id']; ?>" id="<?= $member_menu ?>" class="member_link"><?php echo $rows['c_rep_id']; ?></a><br><?php echo "Member"; ?>
                <?php } else { ?>
                	<a href="javascript:void(0);" data-id="<?php echo $rows['customer_id']; ?>" id="<?= $member_menu ?>" class="member_link"><?php echo $rows['c_rep_id']; ?></a><br><?php echo $rows['c_type'] ?>
                <?php } ?>
              </td>
              <td>
              	<?php if ($rows['s_type'] == 'Agent') { ?>
              		<?php echo ($rows['s_business_name'] == '') ? stripslashes($rows['s_name']) : $rows['s_business_name']; ?>
              		<br/><a href="javascript:void(0);" data-id="<?php echo $rows['sponsor_id']; ?>" id="<?= $agent_menu ?>" class="agent_link"><?php echo $rows['s_rep_id']; ?></a>
                <?php } elseif (($rows['s_type'] == 'Affiliates') || ($rows['s_type'] == 'Ambassadors')) { ?>
                  <?php echo ($rows['s_business_name'] == '') ? stripslashes($rows['s_name']) : $rows['s_business_name']; ?>
                 	<br/><a href="javascript:void(0);" data-id="<?php echo $rows['sponsor_id']; ?>" id="<?= $affiliates_menu ?>" class="affiliates_link"><?php echo $rows['s_rep_id']; ?></a>
                <?php } else { ?>
                  <?php echo ($rows['s_business_name'] == '') ? stripslashes($rows['s_name']) : $rows['s_business_name']; ?>
                  <br/>
                  <a href="javascript:void(0);" data-id="<?php echo $rows['sponsor_id']; ?>" id="<?= $member_menu ?>" class="member_link"><?php echo $rows['s_rep_id']; ?></a>
                <?php } ?>
              </td>
              <td><?=$row['status'];?></td>
              <td><?php echo $rows['is_renewal'] == 'Y' ? 'Renewal' : 'New Business'; ?></td>
              <td>
                <?php if ($rows['status'] == 'Payment Declined') { ?>
                  <strong class="payment_dec_error"><?php echo displayAmount($rows['grand_total'], 2); ?></strong>
                <?php } else if (($rows['status'] == "Refund") || ($rows['status'] == "Void") || ($rows['status'] == 'Chargeback')) { ?>
                  <strong class="payment_refund_error"><?php echo displayAmount($rows['grand_total'], 2); ?></strong>
                <?php } else if ($rows['status'] == "Pending"){ ?>
                  <strong class="payment_pending_error"><?php echo displayAmount($rows['grand_total'], 2); ?></strong>
                <?php } else { ?>
                  <strong> <?php echo displayAmount($rows['grand_total'], 2); ?> </strong>
                <?php } ?>
              </td>
              <td class="icons ">
                <div class="text-left">
                	<a href="javascript:void(0);" id="<?= $all_order_menu ?>" data-id="<?php echo $rows['id']; ?>" class="order_popup all_order_link"><i class="fa fa-print"></i></a>&nbsp;
                  <?php if (in_array($rows['status'], array('Payment Approved'))) { ?>
                  	<a href="javascript:void(0);" data-href="return_create.php?order_display_id=<?= $rows['display_id'] ?>&type=Refund" data-toggle="tooltip" data-original-title="Refund Order" id='<?=$retun_order_menu?>' class="retun_order_menu"><i class="fa fa-refresh" aria-hidden="true"></i></a>&nbsp;
                    <a href="javascript:void(0);" data-id="<?php echo $rows['id']; ?>" data-toggle="tooltip" data-original-title="ChargeBack" class="btn_charge_back_order"><i class="fa fa-reply" aria-hidden="true"></i></a>
                  <?php } ?>
                </div>
              </td>
          	</tr>
          <?php } ?>
        <?php } else { ?>
          <tr>
            <td colspan="11">No record(s) found</td>
          </tr>
       	<?php } ?>
      </tbody>
      <?php if ($total_rows > 0) { ?>
        <tfoot>
          <tr>
          	<td colspan="11">
             	<?php echo $paginate->links_html; ?>
            </td>
         	</tr>
        </tfoot>
      <?php } ?>
    </table>
  </div>
<?php } else { ?>
  <?php include_once 'notify.inc.php'; ?>
  <div class="panel panel-default panel-block panel-title-block" style="display: none;">
    <form id="frm_search" action="search_orders.php" method="GET" class="sform">
     	<input type="hidden" name="id" value="<?php echo $id ?>">
   		<div class="panel-wrapper collapse in">
   			<div class="panel-footer clearfix">
          <button type="button" class="btn btn-info" name="search" id="search" onclick="ajax_submit()"><i class="fa fa-search"></i> Search
          </button>
          <button type="button" class="btn btn-info" name="viewall" id="viewall" onClick="window.location = 'search_orders.php'"><i class="fa fa-search-plus"></i> View All
          </button>
          <input type="hidden" name="export" id="export" value=""/>
          <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1"/>
          <input type="hidden" name="order_search" id="order_search" value="<?=$order_search?>"/>
          <input type="hidden" name="pages" id="per_pages" value="<?= $per_page; ?>"/>
          <input type="hidden" name="sort_by" id="sort_by_column" value="<?= $SortBy; ?>"/>
          <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?= $SortDirection; ?>"/>
          <div id="top_paginate_cont" class="pull-right">
            <div class="col-md-12">
              <div class="form-inline text-right" id="DataTables_Table_0_length">
                <div class="form-group">
                 	<label for="user_type">Records Per Page </label>
                </div>
                <div class="form-group">
                  <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value); ajax_submit();">
                    <option value="10" <?php echo $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
                    <option value="25" <?php echo $_GET['pages'] == 25 || $_GET['pages'] == "" ? 'selected' : ''; ?>>25</option>
                    <option value="50" <?php echo $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
                    <option value="100" <?php echo $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
                  </select>
                </div>
             	</div>
            </div>
         	</div>
        </div>
      </div>
    </form>
  </div>
  <div class="panel panel-default panel-block">
    <div class="list-group">
      <div id="ajax_loader" class="ajex_loader" style="display: none;">
        <div class="loader"></div>
      </div>
      <div id="ajax_data" class=""></div>
    </div>
  </div>

 	<script type="text/javascript">
    $(document).ready(function () {
      $(document).on('click', '.btn_charge_back_order', function () {
        var id = $(this).data('id');
        parent.$.colorbox({
            href: 'order_charge_back.php?order_id=' + id + '&order_status=Chargeback',
            iframe: true,
            width: '600px',
            height: '400px'
        });
      });
    });

    $(document).ready(function () {
      ajax_submit();
      $(document).on('click', '.agent_link', function () {
        var id = $(this).attr('id');
        var customer_id = $(this).attr('data-id');
        if (id == 1) {
          if (customer_id == undefined)
            window.top.location = 'agent_listing.php';
          else
            window.top.location = 'agent_detail.php?id=' + customer_id;
        } else {
          parent.swal({
            title: "No Access",
            html: "Access to this area has not been granted. Please inquire with an administrator to view this page",
            type: "warning",
            confirmButtonText: "Ok",
            allowOutsideClick: false,
          }).then(function () {
          }, function (dismiss) {
          });
        }
      });

      $(document).on('click', '.member_link', function () {
        var id = $(this).attr('id');
        var customer_id = $(this).attr('data-id');
        if (id == 1) {
          if (customer_id == undefined)
              window.top.location = 'customer_listing.php';
          else
              window.top.location = 'customer_detail.php?id=' + customer_id;
        } else {
          // parent.setNotifyError("Link is not Active");
          parent.swal({
            title: "No Access",
            html: "Access to this area has not been granted. Please inquire with an administrator to view this page",
            type: "warning",
            confirmButtonText: "Ok",
            allowOutsideClick: false,
          }).then(function () {
          }, function (dismiss) {
          });
        }
      });

      $(document).on('click', '.affiliates_link', function () {
        var id = $(this).attr('id');
        var customer_id = $(this).attr('data-id');
        if (id == 1) {
          if (customer_id == undefined)
              window.top.location = 'affiliate_profile.php';
          else
              window.top.location = 'affiliate_detail.php?id=' + customer_id;
        } else {
          // parent.setNotifyError("Link is not Active");
          parent.swal({
            title: "No Access",
            html: "Access to this area has not been granted. Please inquire with an administrator to view this page",
            type: "warning",
            confirmButtonText: "Ok",
            allowOutsideClick: false,
          }).then(function () {
          }, function (dismiss) {
          });
        }
      });


      $(document).on('click', '.retun_order_menu', function () {
        var id = $(this).attr('id');
        var href = $(this).attr('data-href');
        if (id == 1 && href != '') {
          window.top.location = href;
        } else {
          // parent.setNotifyError("Link is not Active");
          parent.swal({
            title: "No Access",
            html: "Access to this area has not been granted. Please inquire with an administrator to view this page",
            type: "warning",
            confirmButtonText: "Ok",
            allowOutsideClick: false,
          }).then(function () {
          }, function (dismiss) {
          });
        }
      });

      $(document).off('click', '.all_order_link');
      $(document).on('click', '.all_order_link', function () {
        var id = $(this).attr('id');
        var order_id = $(this).attr('data-id');
        if (id == 1) {
          if (order_id == undefined)
            window.location = 'search_orders.php';
          else
            parent.$.colorbox({
              iframe: true,
              width: '70%',
              height: '600px',
              href: 'order_receipts.php?order_id=' + order_id
           	});
        } else {
           // parent.setNotifyError("Link is not Active");
           parent.swal({
            title: "No Access",
            html: "Access to this area has not been granted. Please inquire with an administrator to view this page",
            type: "warning",
            confirmButtonText: "Ok",
            allowOutsideClick: false,
          }).then(function () {
          }, function (dismiss) {
          });
        }
      });

      $(document).off('click', '#ajax_data tr.data-head a');
      $(document).on('click', '#ajax_data tr.data-head a', function (e) {
        e.preventDefault();
        $('#sort_by_column').val($(this).attr('data-column'));
        $('#sort_by_direction').val($(this).attr('data-direction'));
        ajax_submit();
      });

      $(document).off('click', '#ajax_data ul.pagination li a');
      $(document).on('click', '#ajax_data ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#ajax_data').hide();
        $.ajax({
          url: $(this).attr('href'),
          type: 'GET',
          success: function (res) {
            $('#ajax_loader').hide();
            $('#ajax_data').html(res).show();
            parent.resizeIframe($("body").height() + 20, 'order_search_iframe');
          }
        });
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
        success: function (res) {
          $('#ajax_loader').hide();
          $('#ajax_data').html(res).show();
          parent.resizeIframe($("body").height() + 20, 'order_search_iframe');
        }
      });
      return false;
	  }
	</script>
<?php } ?>