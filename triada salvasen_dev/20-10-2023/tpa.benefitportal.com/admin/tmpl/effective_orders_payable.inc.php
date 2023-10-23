<?php if ($is_ajaxed) { ?>
    <div class="table-responsive">
    <table class="<?=$table_class?>">
	      <thead>
	        <tr>
	          	<th>ID/Added Date</th>
				<th>Payee Type</th>
				<th>Payee</th>
				<th>Fee</th>
				<th>Product Name/ID</th>
				<th>Plan ID</th>
				<th>Status/Transaction ID</th>
				<th>Debit</th>
				<th>Credit</th>
	        </tr>
	      </thead>
      	<tbody>
         
		    <?php
				if ($total_rows > 0) {
		        foreach ($fetch_rows as $rows) {
		    ?>
	      		<tr>
	      			<td>
                        <a href="javascript:void(0)" data-href="transaction_receipt.php?transId=<?=md5($rows['ai_transaction_id'])?>" class="text-red fw500 transReceipt">
                            <?=$rows['ORDER_ID']?></a>
                        <br>
                        <?=getCustomDate($rows['ADDED_DATE'])?>
                    </td>
                    <td><?=ucfirst($rows['PAYEE_TYPE'])?></td>
                    <td><?=ucfirst($rows['PAYEE'])?><br><a href="javascript:void(0);" class="fw500 text-action"><?=$rows['PAYEE_ID']?></a></td>
                    <td>
                        <?php if(!empty($rows['FEE_NAME'])) { ?>
                        <?=$rows['FEE_NAME']?><br><a href="javascript:void(0);" class="fw500 text-action">
                            <?=$rows['FEE_CODE']?></a>
                        <?php } else { echo "-"; }?>
                    </td>
                    <td width="15%">
                        <?=$rows['PRODUCT_NAME']?><br><a href="javascript:void(0);" class="fw500 text-action">
                            <?=$rows['PRODUCT_ID']?></a>
                    </td>
                    <td width="10%" class="text-center">
                    	<a href="javascript:void(0);" class="fw500 text-action">
                            <?=$rows['POLICY_ID']?></a></td>
                    <td>
                        <?=$rows['TRANSACTION_STATUS']?><br><a href="javascript:void(0);" class="fw500 text-action">
                            <?=$rows['TRANSACTION_ID']?></a>
                    </td>
                    
                    <td><?=$rows['DEBIT'] != 0 ? displayAmount(abs($rows['DEBIT'])) : '-';?>
                    </td>
                    <td class="text-action"><strong><?=$rows['CREDIT'] != 0 ? '('.displayAmount(abs($rows['CREDIT'])) .')' : '-';?></strong></td>
				</tr>
	          <?php } ?>
	        <?php } else {?>
	          <tr>
	            <td colspan="11" class="text-center">No record(s) found</td>
	          </tr>
	        <?php }?>
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
<div class="panel panel-default">
    <div class="panel-heading">
		<div class="panel-title">
			<p class="fw500 fs18">Regenerated Payable - Effective Orders</p>
		</div>
	</div>
    <div class="panel-body">
		<form id="frm_search" action="effective_orders_payable.php" method="GET">
			<input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
			<input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
			<input type="hidden" name="id" id="id" value="<?=$id;?>" />
			<div class="row theme-form">
				<div class="col-md-6">
					<div class="row" id="show_date">
						<div id="date_range" class="col-md-12">
							<div class="form-group">
								<select class="form-control" id="join_range" name="join_range">
									<option value=""> </option>
									<option value="Range">Range</option>
									<option value="Exactly">Exactly</option>
									<option value="Before">Before</option>
									<option value="After">After</option>
								</select>
								<label>Added Date</label>
							</div>
						</div>
						<div class="select_date_div col-md-9" style="display:none">
							<div class="form-group">
								<div id="all_join" class="input-group"> 
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									<input type="text" name="added_date" id="added_date" value="" class="form-control date_picker" />
								</div>
								<div  id="range_join" style="display:none;">
									<div class="phone-control-wrap">
										<div class="phone-addon">
											<label class="mn">From</label>
										</div>
										<div class="phone-addon">
											<div class="input-group"> 
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
												<input type="text" name="fromdate" id="fromdate" value="" class="form-control date_picker" />
											</div>
										</div>
										<div class="phone-addon">
											<label class="mn">To</label>
										</div>
										<div class="phone-addon">
											<div class="input-group"> 
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
												<input type="text" name="todate" id="todate" value="" class="form-control date_picker" />
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
                   	<div class="form-group">
                        <select name="products[]" id="products" multiple="multiple" class="listing_search se_multiple_select">
                            <?php foreach ($company_arr as $key=>$company) { ?>
                            <optgroup label='<?= $key ?>'>
                               <?php foreach ($company as $pkey =>$row) { ?>
                               <option value="<?= $row['id'] ?>"><?= $row['name'] .' ('.$row['product_code'].')' ?></option>
                               <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                        <label>Products</label>
                   	</div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" name="order_id" id="order_id" value="<?=$order_id?>" class="form-control listing_search">
                        <label>Order ID(s)</label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" name="transaction_id" id="transaction_id" class="form-control listing_search">
                        <label>Transaction ID(s)</label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <select class="se_multiple_select listing_search" name="payee_type[]" id="payee_type" multiple="multiple">
                            <option value="Advance Commission">Advance Commission</option>
                            <option value="Carrier">Carrier</option>
                            <option value="Commission">Commission</option>
                            <option value="Fee Commission">Fee Commission</option>
                            <option value="Membership">Membership</option>
                            <option value="PMPM">PMPM</option>
                            <option value="Vendor">Vendor</option>
                        </select>
                        <label>Payee Type</label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" name="payee_id" id="payee_id" class="form-control listing_search">
                        <label>Payee ID(s)</label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" name="fee_id" id="fee_id" class="form-control listing_search">
                        <label>Fee ID(s)</label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group ">
                        <input type="text" name="policy_id" id="policy_id" class="form-control listing_search">
                        <label>Plan ID(s)</label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group ">
                        <select class="se_multiple_select listing_search" name="coverage_period[]" id="coverage_period" multiple="multiple">
                            <?php for($i=1;$i<=50;$i++){ ?>
                            <option value="<?=$i?>">P
                                <?=$i?>
                            </option>
                            <?php } ?>
                        </select>
                        <label>Plan Period(s)</label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group ">
                        <select class="form-control listing_search" name="paymentType" id="paymentType">
                            <option value=""></option>
                            <option value="CC">Credit Card</option>
                            <option value="ACH">ACH</option>
                        </select>
                        <label>Payment Type</label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <select class="se_multiple_select listing_search" name="order_status[]" id="order_status" multiple="multiple">
                            <option value="Cancelled">Cancelled</option>
                            <option value="Chargeback">Chargeback</option>
                            <option value="Payment Approved">Payment Approved</option>
                            <option value="Payment Declined">Payment Declined</option>
                            <option value="Payment Returned">Payment Returned</option>
                            <option value="Pending Settlement">Pending Settlement</option>
                            <option value="Post Payment">Post Payment</option>
                            <option value="Refund">Refund</option>
                            <option value="Void">Void</option>
                        </select>
                        <label>Order Status</label>
                    </div>
                </div>
				<div class="col-sm-6">
				<button type="submit" class="btn btn-info" name="search" id="search"><i class="fa fa-search"></i> Search
				</button>
				<button type="button" class="btn btn-info btn-outline" name="viewall" id="viewall" onClick="location.reload()"><i class="fa fa-search-plus"></i>  View All
				</button>
				</div>
			</div>
		</form>
		<br>
        <div id="ajax_data">
            
        </div>
        <div class="clearfix m-t-10 text-center">
            <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close();">Close</a>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
    dropdown_pagination('ajax_data')
	$("#order_status").multipleSelect({
        selectAll: false
    });
    $("#products,#payee_type,#coverage_period").multipleSelect({});
	$(".date_picker").datepicker({
		changeDay: true,
		changeMonth: true,
		changeYear: true
	});
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

$(document).off("submit","#frm_search");
$(document).on("submit","#frm_search",function(e){
	e.preventDefault();
	disable_search();
});

$(document).off('change', '#join_range');
$(document).on('change', '#join_range', function(e) {
	e.preventDefault();
	$('.date_picker').val('');
	if ($(this).val() == '') {
		$('.select_date_div').hide();
		$('#date_range').removeClass('col-md-3').addClass('col-md-12');
	} else {
		$('#date_range').removeClass('col-md-12').addClass('col-md-3');
		$('.select_date_div').show();
		if ($(this).val() == 'Range') {
			$('#range_join').show();
			$('#all_join').hide();
		} else {
			$('#range_join').hide();
			$('#all_join').show();
		}
	}
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