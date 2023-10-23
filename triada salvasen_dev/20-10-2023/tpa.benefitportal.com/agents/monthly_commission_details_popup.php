<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . "/includes/commission.class.php";

$commObj = new Commission();


$SortBy = "cs.created_at";
$SortDirection = "ASC";
$currSortDirection = "DESC";


$pay_period = $_GET['pay_period'];
$status = checkIsset($_GET['status']);
$order_id = checkIsset($_GET['order_id']);
$payer_id = checkIsset($_GET['payer_id']);
$payer_name = checkIsset($_GET['payer_name']);
$commission_type = checkIsset($_GET['commission_type']);
$agent_id = $_SESSION['agents']['id'];

$commission_types = array();
$commission_types['Earned'] = 'Earned Commission';
$commission_types['Advance'] = 'Advanced Commission';
$commission_types['PMPM'] = 'PMPM Commission';
$commission_types['Fee'] = 'Fees';
$commission_types['Reverse'] = 'Past Reversals';

$agentSql = "SELECT id,CONCAT(fname,' ',lname) as name ,rep_id FROM customer WHERE id = :agent_id and is_deleted='N'";
$agentRow = $pdo->selectOne($agentSql, array(":agent_id" => $agent_id));

$display_commission = $commission_types;
$display_commission_total = $commission_types;

foreach ($display_commission as $key => $value){
	$display_commission[$key] = array();
	$totalDebit[$key] = 0;
	$totalCredit[$key] = 0;
	$totalBalance[$key] = 0;
}

if ($commission_type != ""){
	$display_commission = array();
	$display_commission[$commission_type] = array();
}

foreach ($display_commission as $ckey => $cvalue){
	
	$pincr = "";
	$psch_params = array();

	if (!empty($commission_type)){
		if($commission_type == "Earned"){
			$pincr .= " AND (cs.sub_type IN('New','Renewals') OR (cs.sub_type='Reverse' AND cs.is_advance='N' AND cs.is_pmpm_comm='N' AND cs.is_fee_comm='N'))";
		}else if($commission_type == "Advance"){
			$pincr .= " AND cs.is_advance='Y'";
		}else if($commission_type == "PMPM"){
			$pincr .= " AND cs.is_pmpm_comm='Y'";
		}else if($commission_type == "Fee"){
			$pincr .= " AND cs.is_fee_comm='Y'";
		}else if($commission_type == "Reverse"){
			$pincr .= " AND cs.sub_type='Reverse' AND cs.initial_period_reverse='N'";
		}
	}

	$psch_params[':agent_id'] = $agentRow['id'] ;

    if ($pay_period != "") {
        $psch_params[':pay_period'] = date('Y-m-d', strtotime($pay_period));
        $pincr .= " AND cs.pay_period = :pay_period";
    }

    if ($payer_id != "") {
        $psch_params[':payer_id'] = $payer_id;
        $pincr .= " AND c.rep_id = :payer_id";
    }
    if ($payer_name != "") {
        $psch_params[':payer_name'] = "%" . $payer_name . "%";
        $pincr .= ' AND (CONCAT(c.fname, " ", c.lname) LIKE :payer_name OR c.fname LIKE :payer_name OR c.lname LIKE :payer_name)';
    }


    if ($order_id != "") {
		$psch_params[':order_id'] = $order_id;
		$pincr .= " AND o.display_id = :order_id";
	}

	if ($ckey == "Reverse") {
		$pincr .= " AND (cs.sub_type = :commission_type AND cs.initial_period_reverse='N')";
		$psch_params[':commission_type'] = $ckey;
	}else if($ckey == "Advance"){
		$pincr .= " AND (cs.sub_type = :commission_type OR (cs.sub_type='Reverse' AND cs.is_advance='Y' AND cs.initial_period_reverse='Y'))";
		$psch_params[':commission_type'] = $ckey;
	}else if($ckey == "PMPM"){
		$pincr .= " AND (cs.sub_type = :commission_type OR (cs.sub_type='Reverse' AND cs.is_pmpm_comm='Y' AND cs.initial_period_reverse='Y'))";
		$psch_params[':commission_type'] = $ckey;
	}else if($ckey == "Fee"){
		$pincr .= " AND (cs.sub_type = :commission_type OR (cs.sub_type='Reverse' AND cs.is_fee_comm='Y' AND cs.initial_period_reverse='Y'))";
		$psch_params[':commission_type'] = $ckey;
	}else if($ckey == "Earned"){
		$pincr .= " AND (cs.sub_type IN('New','Renewals') OR (cs.sub_type='Reverse' AND cs.is_advance='N' AND cs.is_pmpm_comm='N' AND cs.initial_period_reverse='Y' AND cs.is_fee_comm='N'))";
	}else{
		$pincr .= " AND cs.sub_type = :commission_type";
		$psch_params[':commission_type'] = $ckey;
	}

	$pdf_sel_sql = "SELECT cs.id as commId,c.rep_id as mbrRepId,c.fname,c.lname,CONCAT(c.fname,' ',c.lname) as mbrName,
					od.product_name as prdName,od.product_code as prdCode,
					o.display_id as ordDisplayId,o.created_at as ordDate,o.is_renewal as ordType,o.status as ordStatus,
					
					cs.created_at as commDate,cs.level, cs.percentage, cs.note, cs.sub_type, cs.type as cm_type,
					cs.commissionable_unit_price as unitPrice,
					IF(cs.earned_amount != '',cs.earned_amount,cs.amount) as earnedComm,
					IF(cs.graded_percentage != '',cs.graded_percentage,100) as gradedPercentage,

					IF((cs.sub_type='Reverse' AND cs.is_fee_comm='N') OR (cs.is_fee_comm='Y' AND cs.sub_type='Fee'),cs.amount,0) AS debitAmt,
					IF((cs.sub_type!='Reverse' AND cs.is_fee_comm='N') OR (cs.is_fee_comm='Y' AND cs.sub_type='Reverse'),cs.amount,0) AS creditAmt,

					if(cs.sub_type='Reverse' AND cs.is_pmpm_comm='N' AND cs.is_advance='N',cs.amount,0) as earnedRev, 
					if(cs.sub_type='Reverse' AND cs.is_advance='Y',cs.amount,0) as advanceRev, 
					if(cs.sub_type='Reverse' AND cs.is_pmpm_comm='Y',cs.amount,0) as pmpmRev, 
					if(cs.sub_type='Reverse',cs.amount,0) as totalRev,
					CASE
	                WHEN cs.is_advance = 'Y' THEN 'Advance'
	                WHEN cs.is_pmpm_comm = 'Y' THEN 'PMPM'
	                ELSE 'Earned'
	                END AS revType
					
					FROM commission cs
					JOIN orders o ON (o.id = cs.order_id)
             		JOIN order_details od ON (od.id = cs.order_detail_id AND od.is_deleted='N')
					LEFT JOIN customer c ON (c.id = cs.payer_id)
					WHERE cs.is_deleted='N' AND cs.customer_id =:agent_id AND cs.commission_duration='monthly' 
					AND cs.amount != 0 
					$pincr
					ORDER BY cs.created_at DESC";
	$fetch_pdf_rows = $pdo->select($pdf_sel_sql, $psch_params);
	
	if (count($fetch_pdf_rows) > 0) {
		foreach ($fetch_pdf_rows as $key => $row){
			
			$tmp = array();
			$tmp['ordDisplayId'] = $row['ordDisplayId'];
			$tmp['ordDate'] = $row['ordDate'];
			$tmp['ordType'] = $row['ordType'];
			$tmp['ordStatus'] = $row['ordStatus'];
			$tmp['revType'] = $row['revType'];

			$tmp['mbrName'] = $row['mbrName'];
			$tmp['mbrRepId'] = $row['mbrRepId'];

			$tmp['prdName'] = $row['prdName'];
			$tmp['prdCode'] = $row['prdCode'];

			$tmp['gradedPercentage'] = $row['gradedPercentage'];
			$tmp['earnedComm'] = $row['earnedComm'];
			$tmp['prdPrice'] = $row['unitPrice'];
			$tmp['percentage'] = $row['percentage'];

			$tmp['commDate'] = $row['commDate'];
			$tmp['sub_type'] = $row['sub_type'];

			$tmp['earnedRev'] = $row['earnedRev'];
			$tmp['advanceRev'] = $row['advanceRev'];
			$tmp['pmpmRev'] = $row['pmpmRev'];
            $tmp['totalRev'] = $row['totalRev'];

			$tmp['debitAmt'] = $row['debitAmt'];
			$tmp['creditAmt'] = $row['creditAmt'];
			$tmp['note'] = $row['note'];
			$display_commission[$ckey][] = $tmp;

			$totalCredit[$ckey] += $row['creditAmt'];
			$totalDebit[$ckey] += $row['debitAmt'];
		}
	} else {
		$display_commission[$ckey] = array();
	}
}

// current monthly commission period summary
$monthlyCommSql = "SELECT count(DISTINCT cs.customer_id) as total_count,cs.pay_period,
					SUM(cs.amount) as commTotal,
					SUM(if(cs.type='Adjustment',cs.amount,0)) as adjustComm
					FROM commission cs
					JOIN customer c ON(c.id=cs.customer_id)
					WHERE cs.commission_duration='monthly' AND c.type !='Customer' 
					AND cs.is_deleted='N' AND cs.customer_id =:id AND cs.pay_period=:pay_period 
					GROUP BY cs.pay_period ORDER BY cs.pay_period DESC";
$monthlyCommParams = array(":pay_period" => date("Y-m-d",strtotime($pay_period)),':id'=>$agentRow['id']);
$resMonthlyCommission = $pdo->selectOne($monthlyCommSql,$monthlyCommParams);

$earnedCommMonthly = $totalCredit['Earned'];
$earnedCommRevMonthly = $totalDebit['Earned'];
$earnedNetCommMonthly = $earnedCommMonthly + $earnedCommRevMonthly;

$advanceCommMonthly = $totalCredit['Advance'];
$advanceCommRevMonthly = $totalDebit['Advance'];
$advanceNetCommMonthly = $advanceCommMonthly + $advanceCommRevMonthly;

$pmpmCommMonthly = $totalCredit['PMPM'];
$pmpmCommRevMonthly = $totalDebit['PMPM'];
$pmpmNetCommMonthly = $pmpmCommMonthly + $pmpmCommRevMonthly;

$totalEarnedCommMonthly = $earnedCommMonthly + $advanceCommMonthly + $pmpmCommMonthly;
$totalRevCommMonthly = $earnedCommRevMonthly + $advanceCommRevMonthly + $pmpmCommRevMonthly;
$totalNetCommMonthly =  $earnedNetCommMonthly + $advanceNetCommMonthly + $pmpmNetCommMonthly;

$pastCommRevMonthly = $totalCredit['Reverse'] + $totalDebit['Reverse'];
$feeCommMonthly =  $totalCredit['Fee'] + $totalDebit['Fee'];
$adjustmentCommMonthly =  !empty($resMonthlyCommission['adjustComm']) ? $resMonthlyCommission['adjustComm'] : 0;
$otherTotalCommMonthly = $pastCommRevMonthly + $feeCommMonthly + $adjustmentCommMonthly;

$totaladjustCommission = $adjustmentCommMonthly;


// get debit balance 
$debitBalance = $commObj->getAgentDebitBalance($agentRow['id']);

$template = "monthly_commission_details_popup.inc.php";
$layout = "iframe.layout.php";
include_once 'layout/end.inc.php';
?>