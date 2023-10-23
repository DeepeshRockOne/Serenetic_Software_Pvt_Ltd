<?php if ($popup_is_ajaxed) { ?>
<div class="table-responsive">
	<table class="<?=$table_class?>">
		<thead>
			<tr>
				<th>Product Name</th>
				<th>Product ID</th>  
				<th style="width: 130px;">Current Status</th>
			</tr>
		</thead>
		<tbody>
			<?php if ($total_rows > 0) { ?>
				<?php foreach ($fetch_rows as $rows) { ?>
					<tr>
						<td><?php echo $rows['name'] ?></td>
						<td><?php echo $rows['product_code'] ?></td>  
						<td><?php echo $rows['status'] ?></td>
					</tr>
				<?php } ?>
			<?php } else { ?>
			<tr>
				<td colspan="3">No record(s) found</td>
			</tr>
			<?php } ?>
		</tbody>
		<?php if ($total_rows > 0) { ?>
			<tfoot>
				<tr>
					<td colspan="3">
						<?php echo $paginate->links_html; ?>
					</td>
				</tr>
			</tfoot>
		<?php }?>
	</table>
</div>
<div class="text-center">
	<a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close();">Close</a>
</div>
<?php } else{ ?>
<div class="panel panel-default panel-block">
	<form id="popup_frm_search" action="view_fees_details.php" method="GET">
		<div class="panel-heading">
			<div class="panel-title">
				<p class="fs18">
					 <strong class="fw500"><?=$name . ' ('.$display_id .') - '?>
					</strong> <span class="fw300">(<?= $total_rows ?>) Products</span> 
				</p>
			</div>
		</div>
		<div class="panel-footer clearfix" style="display: none;">
			<input type="hidden" name="export" id="export" value=""/>
			<input type="hidden" name="popup_is_ajaxed" id="popup_is_ajaxed" value="1" />
			<input type="hidden" name="id" id="id" value="<?= $id ?>" />
			<input type="hidden" name="carrier_id" id="carrier_id" value="<?= $carrier_id ?>" />
			<input type="hidden" name="membership_id" id="membership_id" value="<?= $membership_id ?>" />
			<input type="hidden" name="vendor_id" id="vendor_id" value="<?= $vendor_id ?>" />
			<input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
			<input type="hidden" name="page" id="nav_page" value="" />
			<input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
			<input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
		</div>
	</form>
</div>
<div class="panel panel-default panel-block">
	<div class="panel-body">
		<div id="ajax_loader" class="ajex_loader" style="display: none;">
			<div class="loader"></div>
		</div>
		<div id="ajax_data"> </div>
	</div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
		dropdown_pagination('ajax_data')

      popup_ajax_submit();
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

    function popup_ajax_submit() {
      $('#ajax_loader').show();
      $('#ajax_data').hide();
      $('#popup_is_ajaxed').val('1');
      $("#export").val('');
      var params = $('#popup_frm_search').serialize();
      var cpage = $('#nav_page').val();
      $.ajax({
        url: $('#popup_frm_search').attr('action'),
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
<?php }?>