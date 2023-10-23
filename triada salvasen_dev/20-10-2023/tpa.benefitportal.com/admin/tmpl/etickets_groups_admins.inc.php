<?php if($is_ajaxed) { ?>
		<?php /*<div class="clearfix">
			<div class="pull-right">
				<div class="form-inline">
					<div class="form-group">
						<label>Records per page</label>
					</div>
					<div class="form-group">
						<select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);ajax_submit();">
							<option value="10" <?php echo isset($_GET['pages']) && $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
							<option value="25" <?php echo (isset($_GET['pages']) && $_GET['pages'] == 25) || (isset($_GET['pages']) && $_GET['pages'] == "") ? 'selected' : ''; ?>>25</option>
							<option value="50" <?php echo isset($_GET['pages']) && $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
							<option value="100" <?php echo isset($_GET['pages']) && $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
						</select>
					</div>
				</div>
			</div>
		</div> */ ?>
			<div class="table-responsive">
			<table class="<?=$table_class?>">
				<thead>
					<tr>
						<th>Admin ID</th>
						<th>Admin Name</th>
						<th width="100px" >Status</th>
					</tr>
				</thead>
				<tbody>
				<?php if(!empty($fetch_rows) && $total_rows > 0) {
                	foreach($fetch_rows as $rows) {?>
					<tr>
						<td><a href="javascript:void(0);" class="fw500 text-action"><?=$rows['display_id']?></a></td>
						<td><?=$rows['fname'].' '.$rows['lname']?></td>
						<td><?=$rows['status']?></td>
					</tr>
				<?php }} ?>
				</tbody>
				<?php if($total_rows > 250) {?>
				<tfoot>
					<tr>
					<?php if($total_rows > 0 && !empty($fetch_rows)) { ?>
						<td colspan="3">
						<?php echo $paginate->links_html; ?>
						</td>
					<?php }else echo "<td colspan='3'>No record found!</td>"; ?>
					</tr>
				</tfoot> 
				<?php } ?>
			</table>
		</div>
		<div class="text-center m-b-20">
			<a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Close</a>
		</div>

<?php }else{ ?>
<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<h4 class="mn">Group - <span class="fw300"><?=$_GET['name']?> (<?= $_GET['total_admin'] ?> Admins)</span></h4>
	</div>
	<form id="frm_search" action="etickets_groups_admins.php" method="GET" class="theme-form">
		<input type="hidden" name="is_ajaxed" id="is_ajaxed" value=""/>
		<input type="hidden" name="pages" id="per_pages" value="<?=checkIsset($per_page)?>"/>
		<input type="hidden" name="id" id="category_id" value="<?=$category_id;?>"/>
	</form>
	<div class="panel-body" id="group_div_admin">
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(e){
    dropdown_pagination('group_div_admin')
	ajax_submit();
});
$(document).off('click', '#group_div_admin ul.pagination li a');
$(document).on('click', '#group_div_admin ul.pagination li a', function(e) {
    e.preventDefault();
    $('#ajax_loader').show();
    $("#is_ajaxed").val(1);
    $('#group_div_admin').hide();
    $.ajax({
        url: $(this).attr('href'),
        type: 'GET',
        data:{is_ajaxed:1},
        success: function(res) {
            $('#ajax_loader').hide();
            $('#group_div_admin').html(res).show();
			common_select();
        }
    });
});

function ajax_submit() {
	$('#ajax_loader').show();
	$('#group_div_admin').hide();
	$('#is_ajaxed').val('1');
	var params = $('#frm_search').serialize();
	$.ajax({
		url: $('#frm_search').attr('action'),
		type: 'GET',
		data: params,
		success: function(res) {
			$('#ajax_loader').hide();
			$('#group_div_admin').html(res).show();
			common_select();
		}
	});
	return false;
}
</script>
<?php } ?>
