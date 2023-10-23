<?php
$sch_params = array();
$incr = '';
$generate_change_file = false;
$generate_error_file = false;

$incr .= "AND pf.id=:prd_fee_id";
$sch_params[':prd_fee_id'] = $req_row['carrier_id'];

$period_type = 'Coverage Period';
if($req_row['period_type'] == 'pay_period'){
    $period_type = 'Payment Period';
}


if($req_row['is_manual'] == 'N'){
    if(!empty($filter_options) && is_array($filter_options)){
        if(isset($filter_options['added_date'])) {
            if($filter_options['added_date'] == "prior_month") {
                $start_date = date("Y-m-d", strtotime(date("Y-m") . " -1 month"));
                $end_date = date("Y-m-d", strtotime(date("Y-m") . " last day of -1 month"));
            }
        }
    }
    if(empty($start_date) || empty($end_date)) {
        $start_date = date('Y-m-d', strtotime('first day of last month'));
        $end_date = date("Y-m-d", strtotime(date("Y-m") . " last day of -1 month"));
    }
}

if($period_type == "Payment Period") {
    
    if($join_range != ""){
        if($join_range == "range" && $fromdate!='' && $todate!=''){
          $sch_params[':from_date'] = date("Y-m-d",strtotime($fromdate));
          $sch_params[':to_date'] = date("Y-m-d",strtotime($todate));
          $incr .= " AND DATE(pd.created_at) >= :from_date AND DATE(pd.created_at) <= :to_date";
        }else if($join_range == "exactly" && $added_date!=''){
          $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
          $incr.=" AND DATE(pd.created_at) = :added_date";
        }else if($join_range == "before" && $added_date!=''){
          $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
          $incr.=" AND DATE(pd.created_at) < :added_date";
        }else if($join_range == "after" && $added_date!=''){
          $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
          $incr.=" AND DATE(pd.created_at) > :added_date";
        }
      }else{
        $sch_params[":from_date"] = date('Y-m-d', strtotime($start_date));
        $sch_params[":to_date"] = date('Y-m-d', strtotime($end_date));
        $incr .= " AND DATE(pd.created_at) >= :from_date AND DATE(pd.created_at) <= :to_date";
      }

} else {
        if($join_range != ""){
            if($join_range == "range" && $fromdate!='' && $todate!=''){

                $sch_params[':from_date'] = date("Y-m-d",strtotime($fromdate));
                $sch_params[':to_date'] = date("Y-m-d",strtotime($todate));
                $incr .= " AND (DATE(od.start_coverage_period) >= :from_date AND DATE(od.start_coverage_period) <= :to_date)";

            }else if($join_range == "exactly" && $added_date!=''){

                $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
                $incr .= " AND (DATE(od.start_coverage_period) = :added_date)";

            }else if($join_range == "before" && $added_date!=''){

                $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
                $incr .= " AND (DATE(od.start_coverage_period) < :added_date)";

            }else if($join_range == "after" && $added_date!=''){

                $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
                $incr .= " AND (DATE(od.start_coverage_period) > :added_date)";

            }
        }else{
            $sch_params[":from_date"] = date('Y-m-d', strtotime($start_date));
            $sch_params[":to_date"] = date('Y-m-d', strtotime($end_date));
            $incr .= " AND (DATE(od.start_coverage_period) >= :from_date AND DATE(od.start_coverage_period) <= :to_date)";
        }
}
$periodText = '';
if($join_range!=''){
    if($join_range == "range" && $fromdate!='' && $todate!=''){
        $periodText = date("m/d/Y",strtotime($fromdate)) . " - ". date("m/d/Y",strtotime($todate));
    }else if(in_array(strtolower($join_range),array('exactly','before','after')) && $added_date!=''){
        $periodText = ucfirst($join_range).' - '.date("m/d/Y",strtotime($added_date));
    }
}else{
    $periodText = date("m/d/Y",strtotime($start_date)) . " - ". date("m/d/Y",strtotime($end_date));
}
$selSql = "SELECT 
                pd.created_at AS ADDED_DATE,
                od.start_coverage_period AS COVERAGE_START,
                od.end_coverage_period AS COVERAGE_END,
                c.rep_id AS MEMBER_ID,
                c.last_four_ssn as LAST_4_SSN,
                c.birth_date as BIRTH_DATE,
                ws.website_id AS POLICY_ID,
                pm.product_code AS PRODUCT_ID,
                pm.name AS PRODUCT_NAME,
                ppt.title AS BENEFIT_TIER,
                ws.eligibility_date AS EFFECTIVE_DATE,
                ws.termination_date AS TERMINATION_DATE,
                o.display_id AS ORDER_ID,
                t.transaction_status AS TRANSACTION_STATUS,
                t.transaction_id AS TRANSACTION_ID,
                pd.payee_type AS PAYEE_TYPE,
                pf.name AS PAYEE_NAME,
                fee_prd.name AS FEE_NAME,
                pd.credit AS CREDIT,
                pd.debit AS DEBIT,
                ppc.plan_code_value as GROUP_CODE,
                ws.product_id AS product_id_org,
                px.price AS RETAIL_PRICE,
                px.non_commission_amount AS NON_COMM_PRICE,
                px.commission_amount AS COMMISSIONABLE_PRICE,
                pmc.*
            FROM payable py 
            JOIN payable_details pd ON(pd.payable_id = py.id)
            JOIN customer c ON(c.id=py.customer_id)
            JOIN transactions t ON(t.id=pd.transaction_tbl_id)
            JOIN orders o ON(o.id=py.order_id)
            JOIN order_details od ON(od.order_id=py.order_id AND od.product_id=py.product_id AND od.is_deleted='N')
            JOIN prd_matrix px ON (FIND_IN_SET(px.id,od.plan_id))
            JOIN website_subscriptions ws ON(ws.id = od.website_id)
            JOIN prd_main pm ON(pm.id = ws.product_id)
            LEFT JOIN prd_matrix_criteria pmc ON(pmc.prd_matrix_id = px.id AND pmc.is_deleted='N')
            LEFT JOIN prd_plan_type ppt ON (ws.prd_plan_type_id = ppt.id)
            LEFT JOIN prd_fees pf ON(pf.id=pd.payee_id AND pf.is_deleted='N')
            LEFT JOIN prd_main fee_prd ON (fee_prd.id = pd.fee_price_id)
            LEFT JOIN prd_matrix fee_matrix ON(fee_matrix.product_id=fee_prd.id AND fee_matrix.is_deleted='N')
            LEFT JOIN customer ag ON (ag.id = pd.payee_id AND pd.payee_type='Agent')
            LEFT JOIN prd_plan_code ppc ON(ppc.product_id = pm.id AND ppc.is_deleted = 'N' AND ppc.code_no = 'GC')
            WHERE pd.commission_id = 0 AND c.is_deleted='N' AND c.status IN ('Active','Inactive') AND c.type='Customer' AND ws.status NOT IN('Pending Declined','Pending Payment') AND (pm.id IN ($prd_ids) OR pm.parent_product_id IN($prd_ids)) $incr
            GROUP BY pd.id,IF(pmc.id IS NOT NULL,pmc.id,px.id)
            ORDER BY pd.created_at DESC";
  $orderRows = $pdo->select($selSql,$sch_params);

$style_red_font = array(
    'font'  => array(
        'color' => array('rgb' => 'FF0000'),
    )
);

$index = 0;
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex($index);$index++;
$objPHPExcel->createSheet();
$objPHPExcel->getActiveSheet()->setTitle("Billing File");

$i = 11;
/*-------- Heading ----------*/

$carrier_name = getname('prd_fees',$req_row['carrier_id'],'name','id');
$admin_name = $pdo->selectOne("SELECT display_id,CONCAT(fname,' ',lname) as full_name FROM admin WHERE id = :id",array(':id' => $req_row['user_id']));

$total_credits = 0;
$total_debits = 0;
$total_payments = 0;

$row_ind = 1;
$col_ind=0;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'Admin');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $admin_name['full_name'] . ' ('.$admin_name['display_id'] . ')');$col_ind++;

$row_ind++;
$col_ind=0;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'Created Date');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, date("m/d/Y",strtotime($req_row['created_at'])));$col_ind++;

$row_ind++;

$row_ind++;
$col_ind=0;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'File Name');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $req_row['file_name']);$col_ind++;

$row_ind++;
$col_ind=0;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'Recipient');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $carrier_name);$col_ind++;

$row_ind++;
$col_ind=0;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'Period Type');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $period_type);$col_ind++;

$row_ind++;
$col_ind=0;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'Period');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $periodText);$col_ind++;

$row_ind++;
$summary_row_ind = $row_ind;

$row_ind += 4;

$col_ind=0;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'ADDED_DATE');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'COVERAGE_START');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'COVERAGE_END');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'MEMBER_ID');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'BIRTH_DATE');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'AGE');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'LAST_4_SSN');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'POLICY_ID');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'PRODUCT_ID');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'PRODUCT_NAME');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'BENEFIT_TIER');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'EFFECTIVE_DATE');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'TERMINATION_DATE');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'ORDER_ID');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'TRANSACTION_STATUS');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'TRANSACTION_ID');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'PAYEE_TYPE');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'PAYEE_NAME');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'FEE_NAME');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'DEBIT');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'CREDIT');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'RETAIL_PRICE');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'NON_COMM_PRICE');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'COMMISSIONABLE_PRICE');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'AGE');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'GENDER');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'HEIGHT');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'WEIGHT');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'HAS_SPOUSE');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'STATE');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'SMOKING');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'TOBACCO');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'BENEFIT_AMOUNT');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'ZIP_CODE');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, '#_OF_CHILDREN');$col_ind++;

$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'GROUP_CODE');$col_ind++;

$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'PLAN_CODE1');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'PLAN_CODE2');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'PLAN_CODE3');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'PLAN_CODE4');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'PLAN_CODE5');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'PLAN_CODE6');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'PLAN_CODE7');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'PLAN_CODE8');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'PLAN_CODE9');$col_ind++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, 'PLAN_CODE10');$col_ind++;

if(count($orderRows) > 0) {
    $row_ind++;
    foreach ($orderRows as $row) {
        $col_ind=0;
        /*
        Debit column (Negative format - ($0.00)-)
            Opposite of what is in payables
        Credit column (Positive format - $0.00 -)
            Opposite of what is in payables
        */
        $TotalDebits += abs($row['DEBIT']);
        $TotalCredits += abs($row['CREDIT']);

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, displayDate($row['ADDED_DATE']));$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, displayDate($row['COVERAGE_START']));$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, displayDate($row['COVERAGE_END']));$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $row['MEMBER_ID']);$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, displayDate($row['BIRTH_DATE']));$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind,calculateAge($row['BIRTH_DATE']));$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $row['LAST_4_SSN']);$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $row['POLICY_ID']);$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $row['PRODUCT_ID']);$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $row['PRODUCT_NAME']);$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $row['BENEFIT_TIER']);$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, displayDate($row['EFFECTIVE_DATE']));$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, displayDate($row['TERMINATION_DATE']));$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $row['ORDER_ID']);$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $row['TRANSACTION_STATUS']);$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $row['TRANSACTION_ID']);$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, ucwords($row['PAYEE_TYPE']));$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $row['PAYEE_NAME']);$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $row['FEE_NAME']);$col_ind++;

        if($row['DEBIT'] != 0) {
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($col_ind,$row_ind)->getStyle()->applyFromArray($style_red_font);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, '('.displayAmount(abs($row['DEBIT'])).')');$col_ind++;
        } else {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, displayAmount(0));$col_ind++;
        }      
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, displayAmount(abs($row['CREDIT'])));$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $row['RETAIL_PRICE']);$col_ind++;
        $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col_ind, $row_ind)
        ->getNumberFormat()->setFormatCode('"($"#,##0.00")"');
        $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col_ind, $row_ind)->getFont()->getColor()->setRGB('FF0000');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $row['NON_COMM_PRICE']);$col_ind++;
        $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col_ind, $row_ind)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $row['COMMISSIONABLE_PRICE']);$col_ind++;
        $ageFromTo = $row['age_from']!='' ? $row['age_from'].'-'.$row['age_to']  : '';
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $ageFromTo);$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $row['gender']);$col_ind++;
        $height_feet = $row['height_feet']!='' && $row['height_inch']!=0 ? $row['height_feet'].'Ft ' : '' ;
        $height_in = $row['height_inch'] !='' ? $row['height_inch'].'In': '';
        $height = !empty($height_feet) && !empty($height_in) ? $height_feet.$height_in : '';
        $weight = $row['weight'] !=0 && $row['weight']!='' ? $row['weight'] : '';
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $height);$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $weight);$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $row['has_spouse']);$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $row['state']);$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $row['smoking_status']);$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $row['tobacco_status']);$col_ind++;
        if($row['benefit_amount'] !=0){
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col_ind, $row_ind)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        }else{
            $row['benefit_amount'] = '';
        }
        $no_of_child = !empty($row['no_of_children']) ? $row['no_of_children'] : "" ;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $row['benefit_amount']);$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $row['zipcode']);$col_ind++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $no_of_child);$col_ind++;


        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $row_ind, $row['GROUP_CODE']);$col_ind++;       

        $plan_code_count = 0;
        $plan_code_details = $pdo->select("SELECT plan_code_value FROM prd_plan_code WHERE product_id = :product_id AND is_deleted = 'N' AND code_no = 'PC' AND plan_code_value != ''",array(':product_id' => $row['product_id_org']));
        if($plan_code_details){
            foreach ($plan_code_details as $k => $v) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind,$row_ind,$v['plan_code_value']);$col_ind++;
                $plan_code_count++;
            }
        }
        for ($k=0; $k <= (10 - $plan_code_count); $k++) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind,$row_ind,"");$col_ind++;
        }

        $row_ind++;
    }

    $TotalPayment = $TotalCredits - $TotalDebits;

    $col_ind=0;
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $summary_row_ind, 'Total Credits');$col_ind++;
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $summary_row_ind, displayAmount($TotalCredits));$col_ind++;

    $summary_row_ind++;
    $col_ind=0;
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $summary_row_ind, 'Total Debits');$col_ind++;
    if($TotalDebits != 0) {
        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($col_ind, $summary_row_ind)->getStyle()->applyFromArray($style_red_font);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $summary_row_ind, '('.displayAmount($TotalDebits).')');$col_ind++;
    } else {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $summary_row_ind,displayAmount(0));$col_ind++;
    }

    $summary_row_ind++;
    $col_ind=0;
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $summary_row_ind, 'Total Payment');$col_ind++;
    if($TotalPayment < 0) {
        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($col_ind,$summary_row_ind)->getStyle()->applyFromArray($style_red_font);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $summary_row_ind, '('.displayAmount(abs($TotalPayment)).')');$col_ind++;
    } else {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_ind, $summary_row_ind, displayAmount(abs($TotalPayment)));$col_ind++;
    }  
}
?>