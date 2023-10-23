<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';


$sch_params = array();
$incr = '';
$SortBy = "fr.created_at";
$SortDirection = "DESC";
$currSortDirection = "DESC";

$tz = new UserTimeZone('m/d/Y g:i A T',$_SESSION['admin']['timezone']);

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

if ($status != "") {
  $sch_params[':status'] = makeSafe($status);
  $incr .= " AND fr.status = :status";
}

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
  'url' => 'ajax_get_fulfillment_requests.php?' . $query_string,
  'db_handle' => $pdo->dbh,
  'named_params' => $sch_params,
);

$page = (!empty($_GET['page']) && $_GET['page'] > 0) ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);



$sel_sql = "SELECT fr.*,ff.file_name,fh.file_name as processed_file,fh.id as history_id
        FROM fulfillment_requests fr
        JOIN fulfillment_files ff on(ff.id = fr.file_id)
        LEFT JOIN fulfillment_history fh on(fh.req_id = fr.id)
        WHERE fr.is_deleted='N'" . $incr . " GROUP BY fr.id ORDER BY $SortBy $currSortDirection";

$paginate = new pagination($page, $sel_sql, $options);
if ($paginate->success == true) {
  $fetch_rows = $paginate->resultset->fetchAll();
  $total_rows = count($fetch_rows);
}

ob_start(); ?>

<div class="table-responsive">
	<table class="<?=$table_class?>">
		<thead>
			<tr>
				<th width="25%">Requested</th>
				<th width="25%">Delivery Timestamp</th>
				<th width="25%">Report Name</th>
				<th width="20%">Status</th>
				<th class="text-right">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php if($fetch_rows){
				foreach ($fetch_rows as $k => $rows) { ?>
					<tr>
						<td><?=strtotime($rows['created_at']) > 0 ? $tz->getDate($rows['created_at']) : "-" ?></td>
						<td><?=strtotime($rows['updated_at']) > 0 ? $tz->getDate($rows['updated_at']) : "-" ?></td>
						<!-- <td>ETA - 3 minutes</td> -->
						<td><?=$rows['file_name']?></td>
						<?php if($rows['status'] == 'Running'){ ?>
							<td>
								<!-- <div class="progress mn w-200">
									<div class="progress-bar progress-bar-danger" role="progressbar" style="width: 60%;">60%</div>
								</div> -->Processing (<?=$rows['attempts'] ."/". $rows['total_attempts']?>)
							</td>
						<?php }else{ ?>
							<td>
								<?=$rows['status'];?>
							</td>
						<?php } ?>
							<td class="text-right icons">
								<?php if($SITE_ENV !='Live' && $rows['attempts_over'] == 'Y' && $rows['status'] == 'Running' && $rows['file_merge_requested'] == 'N'){ ?>
								<span class="mergeDiv<?=$rows['id']?>"><a href="javascript:void(0);" class="merge_csv" data-id="<?=$rows['id']?>" data-toggle="tooltip" data-placement="top" title="Merge Export Files"><i class="fa fa-files-o"></i></a>
								</span>
								<?php }else if($SITE_ENV !='Live' && $rows['attempts_over'] == 'Y' && $rows['status'] == 'Running' && $rows['file_merge_requested'] == 'Y'){ ?>
									<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Merging Files"><i class="fa fa-spinner fa-spin"></i></a>
								<?php } ?>
								<?php if($rows['status'] != 'Processed'){ ?>
									<a href="javascript:void(0)" class="remove_request" data-id="<?=$rows['id']?>" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-times-circle-o"></i></a>
								<?php }else{ ?>
									<a href="fulfillment_export_requests.php?is_download=Y&file_name=<?=urlencode($rows['processed_file_name']);?>" data-toggle="tooltip" data-placement="top" title="Download"><i class="fa fa-download"></i></a>
									<a href="javascript:void(0);" class="remove_request" data-id="<?=$rows['id']?>" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-trash"></i></a>
								<?php } ?>
							</td>
					</tr>
				<?php }
			}else{ ?>
				<tr class="text-center"> 
					<td colspan="5">No Records!</td>
				</tr>

			<?php } ?>
			
		</tbody>
		<?php if ($total_rows > 0) { ?>
            <tfoot>
            <tr>
                    <td colspan="9">
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