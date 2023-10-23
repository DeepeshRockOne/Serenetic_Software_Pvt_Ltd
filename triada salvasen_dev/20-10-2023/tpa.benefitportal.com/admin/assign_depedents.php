<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$tier_change_required = false;
$validate = new Validation();
$ws_id = isset($_GET['ws_id']) ? $_GET['ws_id'] : "";
$ws_sql = "SELECT ws.* FROM website_subscriptions ws WHERE md5(ws.id)=:id";
$ws_row = $pdo->selectOne($ws_sql,array(":id" => $ws_id));
if(empty($ws_row)) {
	setNotifyError("Please try again");
	echo '<script type="text/javascript">parent.$.fn.colorbox.close();parent.location.reload();</script>';
	exit();
}

//pre_print($ws_row);
$coverage_periods = get_effective_date_selection_options($ws_row['id']);

$existing_dep_sql = "SELECT cd_profile_id,LOWER(relation) as relation,terminationDate,status FROM customer_dependent WHERE website_id=:ws_id AND is_deleted='N'"; // AND status NOT IN('Termed','Inactive')
$existing_dep_res = $pdo->select($existing_dep_sql,array(":ws_id" => $ws_row['id']));

if(!empty($existing_dep_res)) {
	$cdp_ids = array_column($existing_dep_res,'cd_profile_id');
	$dep_sql = "SELECT cd.id,CONCAT(cd.fname,' ',cd.lname) as name FROM customer_dependent_profile cd WHERE cd.is_deleted = 'N' AND cd.relation IN('Daughter','Son','daughter','son') AND cd.customer_id=:customer_id AND id NOT IN (".implode(',',$cdp_ids).")";
	$dep_where = array(":customer_id" => $ws_row['customer_id']);
	$child_dep_res = $pdo->select($dep_sql,$dep_where);


	$dep_sql = "SELECT cd.id,CONCAT(cd.fname,' ',cd.lname) as name FROM customer_dependent_profile cd WHERE cd.is_deleted = 'N' AND cd.relation IN('Wife','Husband','wife','husband') AND cd.customer_id=:customer_id AND id NOT IN (".implode(',',$cdp_ids).")";
	$dep_where = array(":customer_id" => $ws_row['customer_id']);
	$spouse_dep_res = $pdo->select($dep_sql,$dep_where);

} else {
	$dep_sql = "SELECT cd.id,CONCAT(cd.fname,' ',cd.lname) as name FROM customer_dependent_profile cd WHERE cd.is_deleted = 'N' AND cd.relation IN('Daughter','Son','daughter','son') AND cd.customer_id=:customer_id";	
	$dep_where = array(":customer_id" => $ws_row['customer_id']);
	$child_dep_res = $pdo->select($dep_sql,$dep_where);

	$dep_sql = "SELECT cd.id,CONCAT(cd.fname,' ',cd.lname) as name FROM customer_dependent_profile cd WHERE cd.is_deleted = 'N' AND cd.relation IN('Wife','Husband','wife','husband') AND cd.customer_id=:customer_id";	
	$dep_where = array(":customer_id" => $ws_row['customer_id']);
	$spouse_dep_res = $pdo->select($dep_sql,$dep_where);
}

$dep_res = array();

if($ws_row['prd_plan_type_id'] == 1) { //Member Only
	if(!empty($spouse_dep_res) || !empty($child_dep_res)) {
		$tier_change_required = true;

		if(!empty($child_dep_res)) {
			$dep_res = $child_dep_res;
		}
		
		if(!empty($spouse_dep_res)) {
			$dep_res = array_merge($dep_res,$spouse_dep_res);
		}
	}
} elseif($ws_row['prd_plan_type_id'] == 2) { //Member + Child(ren)
	if(!empty($child_dep_res)) {
		$dep_res = $child_dep_res;
	} else {
		if(!empty($spouse_dep_res)) {
			$dep_res = $spouse_dep_res;
			$tier_change_required = true;
		}
	}
} elseif($ws_row['prd_plan_type_id'] == 3) { //Member + Spouse
	if(!empty($spouse_dep_res)) {
		$dep_res = $spouse_dep_res;

		if(!empty($existing_dep_res)) {
			$termination_dates = array_column($existing_dep_res,'terminationDate');
			foreach ($termination_dates as $key => $termination_date) {
				if(strtotime($termination_date) > 0) {
					foreach ($coverage_periods as $ckey => $coverage) {
						if(strtotime($coverage['value']) <= strtotime($termination_date)) {
							unset($coverage_periods[$ckey]);
						}
					}
				} else {
					$tier_change_required = true;
					break;
				}
			}
		}
	} else {
		if(!empty($child_dep_res)) {
			$dep_res = $child_dep_res;
			$tier_change_required = true;
		}
	}

} elseif($ws_row['prd_plan_type_id'] == 4) { //Family
	if(!empty($child_dep_res)) {
		$dep_res = $child_dep_res;
	}

	if(!empty($spouse_dep_res)) {
		if(!empty($existing_dep_res)) {

			// Check if alread spouse exist and term date set
			$relation_arr = array_column($existing_dep_res,'relation');
			if(in_array('wife',$relation_arr) || in_array('husband',$relation_arr)) {
				$is_term_date_set = true;
				foreach ($existing_dep_res as $key => $existing_dep_row) {
					if(in_array($existing_dep_row['relation'],array('wife','husband'))) {
						if(strtotime($existing_dep_row['terminationDate']) > 0) {
							foreach ($coverage_periods as $ckey => $coverage) {
								if(strtotime($coverage['value']) <= strtotime($termination_date)) {
									unset($coverage_periods[$ckey]);
								}
							}
						} else {
							$is_term_date_set = false;
						}
					}
				}
				if($is_term_date_set == true) {
					$dep_res = array_merge($dep_res,$spouse_dep_res);	
				}
			} else {
				$dep_res = array_merge($dep_res,$spouse_dep_res);
			}
		} else {
			$dep_res = array_merge($dep_res,$spouse_dep_res);
		}
	}

} elseif($ws_row['prd_plan_type_id'] == 5) { //Member + One
	if(!empty($spouse_dep_res) || !empty($child_dep_res)) {
		if(!empty($child_dep_res)) {
			$dep_res = $child_dep_res;
		}
		if(!empty($spouse_dep_res)) {
			$dep_res = array_merge($dep_res,$spouse_dep_res);
		}

		if(!empty($existing_dep_res)) {
			$is_term_date_set = false;
			$termination_dates = array_column($existing_dep_res,'terminationDate');
			foreach ($termination_dates as $key => $termination_date) {
				if(strtotime($termination_date) > 0) {
					foreach ($coverage_periods as $ckey => $coverage) {
						if(strtotime($coverage['value']) <= strtotime($termination_date)) {
							unset($coverage_periods[$ckey]);
						}
					}
				} else {
					$tier_change_required = true;
					break;
				}
			}
		}
	}
}


if(empty($coverage_periods)) {
	$tier_change_required = true;
}

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);
$template = 'assign_depedents.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>