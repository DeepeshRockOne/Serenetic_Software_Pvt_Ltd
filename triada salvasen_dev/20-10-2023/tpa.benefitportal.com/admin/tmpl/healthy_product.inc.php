
<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<h4 class="mn">Product - <span class="fw300">(<?=$res_sql['total_products']?>) Products</span></h4>
	</div>
	<div class="panel-body">
		<div class="table-responsive br-n">
			<table class="<?=$table_class?>">
				<thead>
					<tr>
						<th>Products Name</th>
						<th>Products ID</th>
						<th class="text-center" width="120px">Current Status</th>
					</tr>
				</thead>
				<tbody>
					<?php if($total_rows > 0 && !empty($fetch_rows)) {?>
						<?php foreach($fetch_rows as $row) { ?>
					<tr>
						<td><?=$row['name']?></td>
						<td>(<?=$row['product_code']?>)</td>
						<td class="text-center"><?=$row['status']?></td>
					</tr>
					<?php } } ?>
				</tbody>
				<tfoot>
		          	<tr>
		            <td colspan="3">
					<?php echo $paginate->links_html; ?>
		            </td>
		          </tr>
		        </tfoot>
			</table>
		</div>
		<div class="text-center">
			<a href="javascript:void(0)" class="red-link pn" onclick="window.parent.$.colorbox.close()">Cancel</a>
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