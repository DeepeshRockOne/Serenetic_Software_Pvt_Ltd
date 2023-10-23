<?php if ($is_ajaxed) { ?>
    <div class="table-responsive">
    <table class="<?=$table_class?>">
	      <thead>
	        <tr>
	          <th>ID/Order Date</th>
				<th>ID/Member Name</th>
				<th>ID/Payee Agent</th>
				<th>Product</th>
				<th>Commission Price</th>
				<th>Override</th>
				<th>Earned</th>
				<th>Advanced</th>
				<th>PMPM</th>
				<th>Reversed</th>
				<th>Total</th>
	        </tr>
	      </thead>
      	<tbody>
         
		    <?php
		  		$totalComm = 0;
				if ($total_rows > 0) {
		        foreach ($fetch_rows as $rows) {
		        	$totalComm += $rows['totalComm'];
		    ?>
	      		<tr>
					<td><a href="javascript:void(0);" class="text-action fw500"><?=$rows['orderDispId']?></a><br><?=date("m/d/Y",strtotime($rows['orderDate']))?></td>
					<td><a href="javascript:void(0);" class="text-action fw500"><?=$rows['memberDispId']?></a><br><?=$rows['memberName']?></td>
					<td><a href="javascript:void(0);" class="text-action fw500"><?=$rows['payeeAgentId']?></a><br><?=$rows['payeeAgentName']?></td>
					<td><?=$rows['productName']?></td>
					<td><?=displayAmount($rows['commUnitPrice'],2)?></td>
					<td><?=($rows["commOverideType"] == "percentage" ? $rows["commOveride"].'%' : displayAmount($rows["commOveride"],2))?></td>
					<td><?=dispCommAmt($rows['earnedComm'])?></td>
					       <td><?=dispCommAmt($rows['advanceComm'])?></td>
	                <td><?=dispCommAmt($rows['pmpmComm'])?></td>
	                <td><?=dispCommAmt($rows['reverseComm'])?></td>
	                <td class="fw500"><?=dispCommAmt($rows['totalComm'])?></td>
				</tr>
	          <?php } ?>
	        <?php } else {?>
	          <tr>
	            <td colspan="11" class="text-center">No record(s) found</td>
	          </tr>
	        <?php }?>
	          <input type="hidden" name="commTotal" id="commTotal" value="<?=dispCommAmt($totalComm)?>">
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
			<p class="fw500 fs18">Commissions - <span class="text-success" id="dispTotal"> </span></p>
		</div>
	</div>
    <div class="panel-body">
		<form id="frm_search" action="effective_orders_commission.php" method="GET">
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
						<input type="text" class="form-control listing_search" name="agent_id" id="agent_id" value="">
						<label>Agent ID</label>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<input type="text" class="form-control listing_search" name="member_id" id="member_id" value="">
						<label>Member ID</label>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<input type="text" class="form-control listing_search" name="order_id" id="order_id" value="">
						<label>Order ID</label>
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
            $("#dispTotal").html($("#commTotal").val());
            common_select();
        }
    });
    return false;
}
</script>
<?php } ?>