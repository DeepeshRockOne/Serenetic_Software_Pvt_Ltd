<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$sch_params = array();
$incr = '';
$SortBy = "er.updated_at";
$SortDirection = "DESC";
$currSortDirection = "DESC";
$tz = new UserTimeZone('m/d/Y g:i A T',$_SESSION['groups']['timezone']);
$has_querystring = false;
if (!empty($_GET["sort_by"])) {
	$has_querystring = true;
	$SortBy = $_GET["sort_by"];
}

if (!empty($_GET["sort_direction"])) {
	$has_querystring = true;
	$currSortDirection = $_GET["sort_direction"];
}

$is_ajaxed = !empty($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';
$status = !empty($_GET['status']) ? $_GET['status'] : '';

$incr .= " AND er.user_id=:user_id";
$sch_params[':user_id'] = $_SESSION['groups']['id'];

$incr .= " AND er.status=:status";
$sch_params[':status'] = $status;

if (count($sch_params) > 0) {
	$has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
	$has_querystring = true;
	$per_page = $_GET['pages'];
}

$query_string = $has_querystring ? (!empty($_GET['page']) ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';
$options = array(
	'results_per_page' => $per_page,
	'url' => 'ajax_get_export_requests.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = (!empty($_GET['page']) && $_GET['page'] > 0) ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

$sel_sql = "SELECT er.*,ag.rep_id as requester_rep_id,CONCAT(ag.fname,' ',ag.lname) as requester_name,TIMESTAMPDIFF(SECOND,er.created_at,now()) as difference,TIMESTAMPDIFF(SECOND,er.reprocess_at,now()) as rep_difference
        FROM $REPORT_DB.export_requests er
        JOIN customer ag ON(ag.id = er.user_id AND er.user_type='Group')
        WHERE er.is_deleted='N' " . $incr . " 
        ORDER BY $SortBy $currSortDirection";

$paginate = new pagination($page, $sel_sql, $options);
if ($paginate->success == true) {
	$fetch_rows = $paginate->resultset->fetchAll();
	$total_rows = count($fetch_rows);
}
ob_start(); 
?>
<div class="table-responsive">
	<table class="<?=$table_class?>">
		<thead>
			<tr>
				<th >Requested</th>
				<th >Delivery Timestamp</th>
				<th >Report Name</th>
				<?php 
				if($status == "Processed") {
					echo '<th >Requested By</th>';
				} else {
					echo '<th >Status</th>';
				}
				?>
				
				<th width="90px">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php if($fetch_rows){
				foreach ($fetch_rows as $k => $rows) { ?>
					<tr>
						<td><?=strtotime($rows['created_at']) > 0 ? $tz->getDate($rows['created_at']) : "-" ?></td>
						<td>
						<?php 
							if($status == "Processed") {
								echo (strtotime($rows['proceed_at']) > 0 ? $tz->getDate($rows['proceed_at']) : "-");
							} else {
								echo (strtotime($rows['process_datetime']) > 0 ? $tz->getDate($rows['process_datetime']) : "-");
							}
						?>
						</td>
						<td><?=$rows['title']?></td>
						<?php if($status == 'Running'){ ?>
							<td>
								<div class="progress mn w-200">
									<div class="progress-bar progress-bar-danger" role="progressbar" style="width: 60%;">60%</div>
								</div>
							</td>
						<?php }else{ ?>
							<td>
								<?php 
								if($status == "Processed") {
									?>
									<?=$rows['requester_rep_id']?> - <?=$rows['requester_name']?>
									<?php
								} else {
									echo $status;
								}
								?>
							</td>
						<?php } ?>
						<td class="icons">
							<?php if($SITE_ENV !='Live' && $rows['attempts_over'] == 'Y' && $status == 'Running' && $rows['file_merge_requested'] == 'N'){ ?>
								<span class="mergeDiv<?=$rows['id']?>"><a href="javascript:void(0);" class="merge_csv" data-id="<?=$rows['id']?>" data-toggle="tooltip" data-placement="top" title="Merge Export Files"><i class="fa fa-files-o"></i></a>
								</span>
							<?php }else if($SITE_ENV !='Live' && $rows['attempts_over'] == 'Y' && $status == 'Running' && $rows['file_merge_requested'] == 'Y'){ ?>
								<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Merging Files"><i class="fa fa-spinner fa-spin"></i></a>
							<?php } ?>
							
							<?php if($status != 'Processed'){ ?>
								<?php if($status == 'Running' && $rows['difference'] > 600 ){ 
									$spinClass = '';
									$text = 'Reload Request';
									$reloadClass = 'reload_request';
									if($rows['rep_difference'] < 600 && $rows['is_reprocess']=='Y'){
										$spinClass = 'fa-spin';
										$text = 'Processing';
										$reloadClass = '';
									} ?>
									<a href="javascript:void(0)" class="<?=$reloadClass?>" data-id="<?=$rows['id']?>" data-toggle="tooltip" data-placement="top" title="<?=$text?>" ><i class="fa fa-refresh <?=$spinClass?>"></i></a>
								<?php } ?>
								<a href="javascript:void(0)" class="remove_request" data-id="<?=$rows['id']?>" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-times-circle-o"></i></a>
							<?php } else { ?>
								<a href="report_export.php?is_download=Y&file_name=<?=urlencode($rows['filename']);?>" data-toggle="tooltip" data-placement="top" title="Download"><i class="fa fa-download"></i></a>

								<a href="javascript:void(0);" class="remove_request" data-id="<?=$rows['id']?>" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-trash"></i></a>
							<?php } ?>
						</td>
					</tr>
				<?php 
				}
			} else { ?>
				<tr class="text-center"> 
					<td colspan="5">No Records!</td>
				</tr>
		<?php 
			} 
		?>
		</tbody>
		<?php if ($total_rows > 0) { ?>
            <tfoot>
	            <tr>
	                <td colspan="5">
	                    <?php echo $paginate->links_html; ?>
	                </td>
	            </tr>
            </tfoot>
        <?php } ?>
	</table>
</div>
<?php 
echo $content = ob_get_clean();
exit();
?>