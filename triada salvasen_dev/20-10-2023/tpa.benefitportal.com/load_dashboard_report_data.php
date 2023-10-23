<?php
include_once __DIR__ . '/includes/connect.php'; 
include_once __DIR__ . "/includes/reporting_function.php";
include_once __DIR__ . '/admin/includes/admin_functions.php';
include_once __DIR__ . '/includes/redisCache.class.php';
ini_set('max_execution_time', 0);
ini_set('memory_limit', '-1');
$res = array('status' => "success");
$redisCache = new redisCache();

$portal = !empty($_REQUEST['portal'])?$_REQUEST['portal']:"admin";
$report = !empty($_REQUEST['report'])?$_REQUEST['report']:"Exactly";
$join_range = !empty($_REQUEST['join_range'])?$_REQUEST['join_range']:"Exactly";
$fromdate = !empty($_REQUEST["fromdate"])?$_REQUEST["fromdate"]:date("Y-m-d");
$todate = !empty($_REQUEST["todate"])?$_REQUEST["todate"]:date("Y-m-d");
$added_date = !empty($_REQUEST["added_date"])?$_REQUEST["added_date"]:date("Y-m-d");

/*-- Extra Filter --*/
$sales_type = !empty($_REQUEST["sales_type"])?$_REQUEST["sales_type"]:'';
$include_new_business = !empty($_REQUEST["include_new_business"])?true:false;
$include_renewals = !empty($_REQUEST["include_renewals"])?true:false;
$agencies_or_agents = !empty($_REQUEST["agencies_or_agents"])?$_REQUEST["agencies_or_agents"]:'';
$top_agents_short_by = !empty($_REQUEST["top_agents_short_by"])?$_REQUEST["top_agents_short_by"]:'';
$top_state_short_by = !empty($_REQUEST["top_state_short_by"])?$_REQUEST["top_state_short_by"]:'';
$top_products_short_by = !empty($_REQUEST["top_products_short_by"])?$_REQUEST["top_products_short_by"]:'';
$commission_duration = !empty($_REQUEST["commission_duration"])?$_REQUEST["commission_duration"]:'';
$today_date = date("Y-m-d");
$month_start_date = date('Y-m-01',strtotime($today_date));
$month_end_date = date("Y-m-t",strtotime($today_date));
$three_month_ago_date = date('Y-m-d',strtotime('-3 months',strtotime($month_start_date)));

if($join_range == "Range" && $fromdate!='' && $todate!=''){
    $fromdate = date("Y-m-d",strtotime($fromdate));
    $todate = date("Y-m-d",strtotime($todate));

} else if($join_range == "Exactly" && $added_date!=''){
    $fromdate = date("Y-m-d",strtotime($added_date));
    $todate = date("Y-m-d",strtotime($added_date));

} else if($join_range == "Before" && $added_date!=''){
    $todate = date("Y-m-d",strtotime('-1 days',strtotime($added_date)));
    $fromdate = '';

} else if($join_range == "After" && $added_date!=''){
    $todate = '';
    $fromdate = date("Y-m-d",strtotime('+1 days',strtotime($added_date)));
}


if($join_range == "Range"){
    $selected_date_title = date('F j, Y',strtotime($fromdate)).' - '.date('F j, Y',strtotime($todate));
} else {
    if($join_range != 'Exactly') {
        $selected_date_title = $join_range.' '.date('F j, Y',strtotime($added_date));
    } else {
        $selected_date_title = date('F j, Y',strtotime($added_date));
    }
    
}

if($report == "top_header") { //Top Header
    $gross_sales = 0;
    $new_business_members = 0;
    $new_business_sales = 0;
    $renewals_members = 0;
    $renewals_sales = 0;
    $active_members = 0;

    $sch_params = array();
    $incr = "";
    $where_incr = " AND 1 ";

    $upline_incr = "";
    if($portal == 'agent' && isset($_SESSION['agents']['id'])) {
        $upline_incr = " JOIN customer as downline ON (t.customer_id = downline.id AND downline.upline_sponsors LIKE CONCAT('%,',".$_SESSION['agents']['id'].",',%') AND downline.type IN('Customer','Group'))";
    }

    if (strtotime($fromdate) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($fromdate)).' 00:00:00';
        $incr .= " AND t.created_at >= :fcreated_at";
    }
    if (strtotime($todate) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($todate)).' 23:59:59';
        $incr .= " AND t.created_at <= :tcreated_at";
    }

    $sql = "SELECT 
            SUM(t.credit) AS gross_sales,
            SUM(t.renewal_total) AS renewals_sales,
            SUM(t.new_business_total) AS new_business_sales,
            SUM(t.new_business_members) AS new_business_members, 
            SUM(t.renewal_members) AS renewals_members 
            FROM transactions t $upline_incr
            WHERE t.order_id > 0 AND t.transaction_type IN ('New Order','Renewal Order','List Bill Order') $incr";
    $row = $pdo->selectOne($sql, $sch_params);

    if(!empty($row)) {
        $gross_sales = displayAmount($row['gross_sales']);
        $new_business_members = (!empty($row['new_business_members'])?$row['new_business_members']:0);
        $new_business_sales = displayAmount($row['new_business_sales']);
        $renewals_members = (!empty($row['renewals_members'])?$row['renewals_members']:0);
        $renewals_sales = displayAmount($row['renewals_sales']);
        
        $upline_incr = "";
        if($portal == 'agent' && isset($_SESSION['agents']['id'])) {
            $upline_incr = " AND c.upline_sponsors LIKE CONCAT('%,',".$_SESSION['agents']['id'].",',%')";
        }
        $sel_sql = "SELECT COUNT(c.id) AS active_members
            FROM customer c 
            WHERE c.status IN('Active') AND c.type='Customer' AND c.is_deleted = 'N' $upline_incr";
        $row = $pdo->selectOne($sel_sql);
       
        if(!empty($row)) {
            $active_members = $row['active_members'];
        }
    }

    $data = array(
        'gross_sales' => $gross_sales,
        'new_business_members' => $new_business_members,
        'new_business_sales' => $new_business_sales,
        'renewals_members' => $renewals_members,
        'renewals_sales' => $renewals_sales,
        'active_members' => $active_members,
    );
    $res[$report] = $data;
}

if($report == "gross_net_sales") { //Top Header
    $gross_sales = 0;
    $net_sales = 0;
    $gross_net_sales_amt = 0;
    $reversals_amt = 0;
    $pending_settlement_amt = 0;
    $pending_settlement_trans = 0;
    $payment_returned_amt = 0;
    $payment_returned_trans = 0;
    $average_sale = 0;
    $total_member = 0;

    $sch_params = array();
    $incr = "";

    $upline_incr = "";
    if($portal == 'agent' && isset($_SESSION['agents']['id'])) {
        $upline_incr = " JOIN customer as downline ON (t.customer_id = downline.id AND downline.upline_sponsors LIKE CONCAT('%,',".$_SESSION['agents']['id'].",',%') AND downline.type IN('Customer','Group'))";
    }

    if (strtotime($fromdate) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($fromdate)).' 00:00:00';
        $incr .= " AND t.created_at >= :fcreated_at";
    }
    if (strtotime($todate) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($todate)).' 23:59:59';
        $incr .= " AND t.created_at <= :tcreated_at";
    }

    $amt_column = "(t.new_business_total + t.renewal_total)";
    $member_count = "(t.new_business_members + t.renewal_members)";
    if($include_renewals == true && $include_new_business == true) {
        
    } elseif($include_renewals == true) {
        $amt_column = "(t.renewal_total)";
        $member_count = "(t.renewal_members)";

    } elseif($include_new_business == true) {
        $amt_column = "(t.new_business_total)";
        $member_count = "(t.new_business_members)";
    }

    if($sales_type == "Gross Sales") {
        $total_member_clause = "SUM(IF(t.transaction_type IN('Renewal Order','New Order','List Bill Order'),$member_count,0)) AS total_member,";
    } else {
        $total_member_clause = "SUM(IF(t.transaction_type IN('Renewal Order','New Order','List Bill Order'),$member_count,($member_count * -1))) AS total_member,";
    }

    $sql = "SELECT
            SUM(IF(t.transaction_type IN('Renewal Order','New Order','List Bill Order'),$amt_column,0)) AS gross_sales,
            SUM(IF(t.transaction_type IN('Renewal Order','New Order','List Bill Order'),$member_count,0)) AS approved_trans,
            SUM(IF(t.transaction_status = 'Pending Settlement',$amt_column,0)) AS pending_settlement_amt,
            SUM(IF(t.transaction_status = 'Pending Settlement',$member_count,0)) AS pending_settlement_trans,
            SUM(IF(t.transaction_status = 'Payment Returned',$amt_column,0)) AS payment_returned_amt,
            SUM(IF(t.transaction_status = 'Payment Returned',$member_count,0)) AS payment_returned_trans,
            SUM(IF(t.transaction_type IN('Refund Order','Chargeback','Void Order'),$amt_column,0)) AS reversals_amt,
            SUM(IF(t.transaction_type IN('Refund Order','Chargeback','Void Order'),$member_count,0)) AS reversals_trans,
            $total_member_clause
            '' as tmp
            FROM transactions t $upline_incr
            WHERE t.transaction_type IN ('New Order','Renewal Order','List Bill Order','Refund Order','Chargeback','Void Order','Pending','Payment Returned') $incr";

    if($include_renewals == false && $include_new_business == false) {
        $row = array();
    } else {
        $row = $pdo->selectOne($sql,$sch_params);    
    }
    if(!empty($row)) {
        $gross_sales = $row['gross_sales'];
        $net_sales = $row['gross_sales'] - $row['reversals_amt'];

        if($sales_type == "Gross Sales") {
            $gross_net_sales_amt = $gross_sales;
            if($row['approved_trans'] > 0) {
                $average_sale = ($row['gross_sales'] / $row['approved_trans']); 
            }
        } else {
            $gross_net_sales_amt = $net_sales;
            if(($row['approved_trans'] - $row['reversals_trans']) > 0) {
                $average_sale = ($net_sales / ($row['approved_trans'] - $row['reversals_trans']));
            }
        }

        $total_member = !empty($row['total_member']) ? $row['total_member'] : 0;
        
        $reversals_amt = $row['reversals_amt'];
        $reversals_trans = $row['reversals_trans'];
        $pending_settlement_trans = $row['pending_settlement_trans'];
        $pending_settlement_amt = $row['pending_settlement_amt'];
        $payment_returned_trans = $row['payment_returned_trans'];
        $payment_returned_amt = $row['payment_returned_amt'];
    }

    $data = array(
        'row' => $row,
        'selected_date_title' => $selected_date_title,
        'gross_sales' => displayAmount($gross_sales),
        'net_sales' => displayAmount($net_sales),
        'gross_net_sales_amt' => displayAmount($gross_net_sales_amt),
        'reversals_sales' => displayAmount($reversals_amt),
        'pending_settlement_trans' => $pending_settlement_trans,
        'pending_settlement_amt' => displayAmount($pending_settlement_amt),
        'payment_returned_trans' => $payment_returned_trans,
        'payment_returned_amt' => displayAmount($payment_returned_amt),
        'average_sale' => displayAmount($average_sale),
        'new_members' => $total_member,
    );
    $res[$report] = $data;
}

if($report == "top_performing_agents") { //Top Header
    $response_html = '';
    if($top_agents_short_by == "Sales") {
        if($sales_type == "Gross Sales") {
            $SortBy = "gross_sales_amt desc, total_member desc";
        } else {
            $SortBy = "net_sales_amt desc, total_member desc";
        }
    } else {
        if($sales_type == "Gross Sales") {
            $SortBy = "total_member desc, gross_sales_amt desc";
        } else {
            $SortBy = "total_member desc, net_sales_amt desc";
        }
    }
    
    $sch_params = array();
    $incr = "";
    $where_incr = " AND 1 ";
    $total_member = 0;

    if (strtotime($fromdate) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($fromdate)).' 00:00:00';
        $incr .= " AND t.created_at >= :fcreated_at";
    }
    if (strtotime($todate) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($todate)).' 23:59:59';
        $incr .= " AND t.created_at <= :tcreated_at";
    }

    $amt_column = "(t.new_business_total + t.renewal_total)";
    $member_count = "(t.new_business_members + t.renewal_members)";
    if($include_renewals == true && $include_new_business == true) {
        
    } elseif($include_renewals == true) {
        $where_incr .= " AND (o.is_renewal='Y' OR (o.is_renewal='L' AND od.is_renewal='Y'))";
        $amt_column = "(t.renewal_total)";
        $member_count = "(t.renewal_members)";

    } elseif($include_new_business == true) {
        $where_incr .= " AND (o.is_renewal='N' OR (o.is_renewal='L' AND od.is_renewal='N'))";
        $amt_column = "(t.new_business_total)";
        $member_count = "(t.new_business_members)";
    }

    if($agencies_or_agents == "Agencies") {
        $upline_incr = "";
        if($portal == 'agent' && isset($_SESSION['agents']['id'])) {
            $upline_incr = " JOIN customer as downline ON (t.customer_id = downline.id AND downline.upline_sponsors LIKE CONCAT('%,',".$_SESSION['agents']['id'].",',%') AND downline.type IN('Customer','Group'))";
        }

        if($sales_type == "Gross Sales") {
            $having_clause = "gross_sales_amt != 0";
            $amt_clause = "SUM(IF(t.transaction_type IN('Renewal Order','New Order','List Bill Order'),$amt_column,0)) AS gross_sales_amt,";
            $total_member_clause = "SUM(IF(t.transaction_type IN('Renewal Order','New Order','List Bill Order'),$member_count,0)) AS total_member,";
        } else {
            $having_clause = "net_sales_amt != 0";
            $amt_clause = "SUM(IF(t.transaction_type IN('Renewal Order','New Order','List Bill Order'),$amt_column,($amt_column * -1))) AS net_sales_amt,";
            $total_member_clause = "SUM(IF(t.transaction_type IN('Renewal Order','New Order','List Bill Order'),$member_count,($member_count * -1))) AS total_member,";
        }
        $sql = "SELECT  
                IF(cs.account_type='Business',cs.company_name,CONCAT(s.fname,' ',s.lname)) AS agent_name,
                s.rep_id AS agent_display_id,
                s.id as sponsor_id,
                $amt_clause
                $total_member_clause
                '' as tmp
                FROM customer s
                JOIN customer_settings cs ON(cs.customer_id = s.id AND cs.account_type='Business')
                JOIN customer as c ON (c.upline_sponsors LIKE CONCAT('%,',s.id,',%') AND c.type IN('Customer','Group') AND
                (c.sponsor_id=s.id OR c.sponsor_id IN(
                    SELECT agt.id 
                    FROM customer agt
                    JOIN customer_settings agt_sett ON(agt.id=agt_sett.customer_id AND agt_sett.account_type='Personal')
                    WHERE agt_sett.agency_id = s.id AND agt.type = 'Agent' AND agt.is_deleted='N'
                )))
                JOIN transactions as t ON (c.id = t.customer_id AND t.transaction_type IN ('New Order','Renewal Order','List Bill Order','Refund Order','Chargeback','Void Order') $incr) 
                $upline_incr
                WHERE s.is_deleted='N' 
                GROUP BY s.id 
                HAVING $having_clause 
                ORDER BY  $SortBy";
        
        if($include_renewals == false && $include_new_business == false) {
            $rows = array();
        } else {
            $rows = $pdo->select($sql,$sch_params);
        }
        if(!empty($rows)) {
            foreach($rows as $key => $row) {
                $total_member += $row['total_member'];

                if($sales_type == "Gross Sales") {
                    $sales_amt = displayAmount($row['gross_sales_amt']);
                } else {
                    $sales_amt = displayAmount($row['net_sales_amt']);
                }

                $response_html .= '<tr>';
                
                $tmp_url = 'agent_detail_v1.php?id='.md5($row['sponsor_id']);
                if(module_access_type(5) != "rw") {
                    $tmp_url = 'javascript:void(0);';
                }
                if($portal == 'agent' && isset($_SESSION['agents']['id'])) {
                    $tmp_url = 'javascript:void(0);';
                }
                $response_html .= "<td><a href='".$tmp_url."' class='' target='_blank'> ".$row['agent_name']." (".$row['agent_display_id'].")</a></td>";    
                

                $response_html .= "<td class='text-right'>".$row['total_member']."/".$sales_amt."</td>";

                $response_html .= '</tr>';
            }
        } else {
            $response_html .= "<tr><td colspan='2' style='text-align:center;'>No record(s) found</td></tr>";
        }
    }
    if($agencies_or_agents == "Agents"){
        $upline_incr = "";
        if($portal == 'agent' && isset($_SESSION['agents']['id'])) {
            $upline_incr = " JOIN customer as downline ON (t.customer_id = downline.id AND downline.upline_sponsors LIKE CONCAT('%,',".$_SESSION['agents']['id'].",',%') AND downline.type IN('Customer','Group'))";
        }

        if($sales_type == "Gross Sales") {
            $having_clause = "ord.gross_sales_amt != 0";
            $total_member_clause = "SUM(IF(t.transaction_type IN('Renewal Order','New Order','List Bill Order'),$member_count,0)) AS total_member,";
        } else {
            $having_clause = "net_sales_amt != 0";
            $total_member_clause = "SUM(IF(t.transaction_type IN('Renewal Order','New Order','List Bill Order'),$member_count,($member_count * -1))) AS total_member,";
        }  
        $sql = "SELECT  
                    CONCAT(a.fname,' ',a.lname) AS agent_name,
                    a.rep_id as agent_display_id,
                    ord.gross_sales_amt,
                    ord.reversals_amt,
                    (ord.gross_sales_amt - ord.reversals_amt) as net_sales_amt,
                    ord.total_member,
                    a.id as agent_id
                    FROM customer a
                    JOIN
                    (
                        SELECT 
                        c.sponsor_id as s_id,
                        SUM(IF(t.transaction_type IN('Renewal Order','New Order','List Bill Order'),$amt_column,0)) AS gross_sales_amt,
                        SUM(IF(t.transaction_type IN('Refund Order','Chargeback','Void Order'),$amt_column,0)) AS reversals_amt,
                        $total_member_clause
                        '' as tmp
                        FROM transactions t $upline_incr
                        JOIN customer c ON(c.id = t.customer_id)
                        WHERE t.transaction_type IN ('New Order','Renewal Order','List Bill Order','Refund Order','Chargeback','Void Order') $incr 
                        GROUP BY c.sponsor_id
                    ) as ord ON (ord.s_id = a.id)
                    WHERE a.is_deleted='N' GROUP BY a.id 
                    HAVING $having_clause 
                    ORDER BY $SortBy";
                    
        if($include_renewals == false && $include_new_business == false) {
            $rows = array();
        } else {
            $rows = $pdo->select($sql,$sch_params);
        }
        if(!empty($rows)) {
            foreach($rows as $key => $row) {
                $total_member += $row['total_member'];

                if($sales_type == "Gross Sales") {
                    $sales_amt = displayAmount($row['gross_sales_amt']);
                } else {
                    $sales_amt = displayAmount($row['net_sales_amt']);
                }

                $response_html .= '<tr>';
                $tmp_url = 'agent_detail_v1.php?id='.md5($row['agent_id']);
                if(module_access_type(5) != "rw") {
                    $tmp_url = 'javascript:void(0);';
                }
                if($portal == 'agent' && isset($_SESSION['agents']['id'])) {
                    $tmp_url = 'javascript:void(0);';
                }
                $response_html .= "<td><a href='".$tmp_url."' class='' target='_blank'> ".$row['agent_name']." (".$row['agent_display_id'].")</a></td>";
                $response_html .= "<td class='text-right'>".$row['total_member']."/".$sales_amt."</td>";

                $response_html .= '</tr>';
            }
        } else {
            $response_html .= "<tr><td colspan='2' style='text-align:center;'>No record(s) found</td></tr>";
        }
    }
    $res[$report] = $response_html;
    $res[$report.'_total_member'] = $total_member;
}

if($report == "premiums_chart") {
    $total_premiums = 0;
    $total_premiums_per = 0;
    $total_healthy_steps = 0;
    $total_healthy_steps_per = 0;
    $total_fees = 0;
    $total_fees_per = 0;
    

    $sch_params = array();
    $incr = "";
    $where_incr = " AND 1 ";

    $upline_incr = "";
    if($portal == 'agent' && isset($_SESSION['agents']['id'])) {
        $upline_incr = " JOIN customer as downline ON (t.customer_id = downline.id AND downline.upline_sponsors LIKE CONCAT('%,',".$_SESSION['agents']['id'].",',%') AND downline.type IN('Customer','Group'))";
    }

    if (strtotime($fromdate) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($fromdate)).' 00:00:00';
        $incr .= " AND t.created_at >= :fcreated_at";
    }
    if (strtotime($todate) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($todate)).' 23:59:59';
        $incr .= " AND t.created_at <= :tcreated_at";
    }

    if($include_renewals == true && $include_new_business == true) {
        
    } elseif($include_renewals == true) {
        $where_incr .= " AND (o.is_renewal='Y' OR (o.is_renewal='L' AND od.is_renewal='Y'))";

    } elseif($include_new_business == true) {
        $where_incr .= " AND (o.is_renewal='N' OR (o.is_renewal='L' AND od.is_renewal='N'))";
    }

    //In Select Clause: IF(od.lbd_transaction_type='refund',(od.unit_price * -1),od.unit_price) This is added for list bill detail refund transaction type

    $sql = "SELECT
            SUM(IF(p.type='Normal' AND (t.transaction_type = 'Renewal Order' OR t.transaction_type = 'New Order' OR t.transaction_type = 'List Bill Order'),IF(od.lbd_transaction_type='refund',(od.unit_price * -1),od.unit_price),0)) AS premiums_sales_amt,
            
            SUM(IF(p.type='Normal' AND (o.status='Refund' OR o.status='Void' OR  o.status='Chargeback' OR od.is_refund = 'Y' OR od.is_chargeback = 'Y' OR od.is_payment_return = 'Y') AND (t.transaction_type = 'Refund Order' OR t.transaction_type = 'Chargeback' OR t.transaction_type = 'Void Order'),od.unit_price,0)) AS premiums_reversals_amt,
            
            SUM(IF(p.product_type='Healthy Step' AND (t.transaction_type = 'Renewal Order' OR t.transaction_type = 'New Order' OR t.transaction_type = 'List Bill Order'),IF(od.lbd_transaction_type='refund',(od.unit_price * -1),od.unit_price),0)) AS healthy_step_sales_amt,
            
            SUM(IF(p.product_type='Healthy Step' AND (o.status='Refund' OR o.status='Void' OR  o.status='Chargeback' OR od.is_refund = 'Y' OR od.is_chargeback = 'Y' OR od.is_payment_return = 'Y') AND (t.transaction_type = 'Refund Order' OR t.transaction_type = 'Chargeback' OR t.transaction_type = 'Void Order'),od.unit_price,0)) AS healthy_step_reversals_amt,
            
            SUM(IF(p.type='Fees' AND p.product_type != 'Healthy Step' AND (t.transaction_type = 'Renewal Order' OR t.transaction_type = 'New Order' OR t.transaction_type = 'List Bill Order'),IF(od.lbd_transaction_type='refund',(od.unit_price * -1),od.unit_price),0)) AS fees_sales_amt,
            
            SUM(IF(p.type='Fees' AND p.product_type != 'Healthy Step' AND (o.status='Refund' OR o.status='Void' OR  o.status='Chargeback' OR od.is_refund = 'Y' OR od.is_chargeback = 'Y' OR od.is_payment_return = 'Y') AND (t.transaction_type = 'Refund Order' OR t.transaction_type = 'Chargeback' OR t.transaction_type = 'Void Order'),od.unit_price,0)) AS fees_reversals_amt
            FROM transactions t $upline_incr
            JOIN orders o ON(o.id = t.order_id)
            JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
            JOIN prd_main p ON(p.id=od.product_id AND p.is_deleted='N')
            WHERE t.transaction_type IN ('New Order','Renewal Order','List Bill Order','Refund Order','Chargeback','Void Order') $where_incr $incr";
    
    if($include_renewals == false && $include_new_business == false) {
        $row = array();
    } else {
        $row = $pdo->selectOne($sql,$sch_params);
    }
    if(!empty($row)) {
        if($sales_type == "Gross Sales") {
            $total_premiums = $row['premiums_sales_amt'];
            $total_healthy_steps = $row['healthy_step_sales_amt'];
            $total_fees = $row['fees_sales_amt'];
            $total_sales = ($total_premiums + $total_healthy_steps + $total_fees);
            if($total_sales > 0) {
                $total_premiums_per = (100 * $total_premiums) / $total_sales;
                $total_healthy_steps_per = (100 * $total_healthy_steps) / $total_sales;
                $total_fees_per = (100 * $total_fees) / $total_sales;
            }
        } else {
            $total_premiums = $row['premiums_sales_amt'] - $row['premiums_reversals_amt'];
            $total_healthy_steps = $row['healthy_step_sales_amt'] - $row['healthy_step_reversals_amt'];
            $total_fees = $row['fees_sales_amt'] - $row['fees_reversals_amt'];
            $total_sales = ($total_premiums + $total_healthy_steps + $total_fees);
            if($total_sales > 0) {
                $total_premiums_per = (100 * $total_premiums) / $total_sales;
                $total_healthy_steps_per = (100 * $total_healthy_steps) / $total_sales;
                $total_fees_per = (100 * $total_fees) / $total_sales;
            }
        }
    }

    $chart_data = array();
    $chart_data[] = array(
        'label' => "Premiums",
        'data' => $total_premiums_per,
        'color' => "#d94948",
    );
    $chart_data[] = array(
        'label' => "Healthy Steps",
        'data' => $total_healthy_steps_per,
        'color' => "#0086C2",
    );
    $chart_data[] = array(
        'label' => "Fees",
        'data' => $total_fees_per,
        'color' => "#b8b8b8",
    );
    $data = array(
        "total_premiums" => displayAmount($total_premiums),
        "total_premiums_per" => displaypercentage($total_premiums_per),
        "total_healthy_steps" => displayAmount($total_healthy_steps),
        "total_healthy_steps_per" => displaypercentage($total_healthy_steps_per),
        "total_fees" => displayAmount($total_fees),
        "total_fees_per" => displaypercentage($total_fees_per),
        "chart_data" => $chart_data,
        "row" => $row,
    );
    $res[$report] = $data;
}

if($report == "sales_bar_chart") {
    if(isset($_SESSION['agents']['id'])){
        $curr_year = array();
        $prev_year = array();
        $months = array();

        for ($i = 0; $i < 12; $i++) {
            $months[] = date("M", strtotime(date('Y-01-01')." + $i months"));
            $curr_year[$i] = array(
                'month' => date("Y-m-d", strtotime(date('Y-01-01')." + $i months")),
                'new_business_sales_amt' => 0,
                'renewal_sales_amt' => 0,
                'gross_sales_amt' => 0,
                'reversals_amt' => 0,
                'net_sales_amt' => 0,
            );

            $prev_year[$i] = array(
                'month' => date("Y-m-d", strtotime(date('Y-01-01')." + $i months -1 year")),
                'new_business_sales_amt' => 0,
                'renewal_sales_amt' => 0,
                'gross_sales_amt' => 0,
                'reversals_amt' => 0,
                'net_sales_amt' => 0,
            );
        }

        $sch_params = array();
        $incr = $select_col = "";
        $where_incr = " AND 1 ";

        $upline_incr = "";
        if($portal == 'agent' && isset($_SESSION['agents']['id'])) {
            $upline_incr = " JOIN customer as downline ON (t.customer_id = downline.id AND downline.upline_sponsors LIKE CONCAT('%,',".$_SESSION['agents']['id'].",',%') AND downline.type IN('Customer','Group'))";
        }

        $amt_column = "(t.new_business_total + t.renewal_total)";
        if($include_renewals == true && $include_new_business == true) {
            $select_col = "SUM(IF((t.transaction_type = 'New Order' OR t.transaction_type = 'List Bill Order'),t.new_business_total,0)) AS new_business_sales_amt,
            SUM(IF((t.transaction_type = 'Renewal Order' OR t.transaction_type = 'List Bill Order'),t.renewal_total,0)) AS renewal_sales_amt";
        } elseif($include_renewals == true) {
            $where_incr .= " AND (o.is_renewal='Y' OR (o.is_renewal='L' AND od.is_renewal='Y'))";
            $amt_column = "(t.renewal_total)";
            $select_col = "0.00 AS new_business_sales_amt,
            SUM(IF((t.transaction_type = 'Renewal Order' OR t.transaction_type = 'List Bill Order'),t.renewal_total,0)) AS renewal_sales_amt";
        } elseif($include_new_business == true) {
            $where_incr .= " AND (o.is_renewal='N' OR (o.is_renewal='L' AND od.is_renewal='N'))";
            $amt_column = "(t.new_business_total)";
            $select_col = "SUM(IF((t.transaction_type = 'New Order' OR t.transaction_type = 'List Bill Order'),t.new_business_total,0)) AS new_business_sales_amt,
            0.00 AS renewal_sales_amt";
        }

        $sql = "SELECT
                $select_col,
                SUM(IF(t.transaction_type = 'Renewal Order' OR t.transaction_type = 'New Order' OR t.transaction_type = 'List Bill Order',$amt_column,0)) AS gross_sales_amt,
                SUM(IF(t.transaction_type = 'Refund Order' OR t.transaction_type = 'Chargeback' OR t.transaction_type = 'Void Order',$amt_column,0)) AS reversals_amt,
                SUM(IF(t.transaction_type = 'Renewal Order' OR t.transaction_type = 'New Order' OR t.transaction_type = 'List Bill Order',$amt_column,($amt_column * -1))) AS net_sales_amt,
                DATE_FORMAT(t.created_at,'%m')as o_month,YEAR(t.created_at)as o_year,DATE_FORMAT(t.created_at,'%d')as o_day
                FROM transactions t $upline_incr
                JOIN 
                (
                    SELECT o.id,COUNT(od.product_id) AS total_policies 
                    FROM orders o 
                    JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
                    WHERE 1 $where_incr GROUP BY o.id 
                ) as ord ON ord.id = t.order_id 
                WHERE ord.id IS NOT NULL AND t.transaction_type IN ('New Order','Renewal Order','List Bill Order','Refund Order','Chargeback','Void Order') $incr
                GROUP BY YEAR(t.created_at),MONTH(t.created_at)";

        if($include_renewals == false && $include_new_business == false) {
            $rows = array();
        } else {
            $rows = $pdo->select($sql,$sch_params);
            foreach ($rows as $key => $order) {
                foreach ($curr_year as $skey => $value) {
                    if(date('Y-m',strtotime($curr_year[$skey]['month'])) == $order['o_year'] . '-' . $order['o_month']) {
                        $curr_year[$skey]['new_business_sales_amt'] = $order['new_business_sales_amt'];
                        $curr_year[$skey]['renewal_sales_amt'] = $order['renewal_sales_amt'];
                        $curr_year[$skey]['gross_sales_amt'] = $order['gross_sales_amt'];
                        $curr_year[$skey]['reversals_amt'] = $order['reversals_amt'];
                        $curr_year[$skey]['net_sales_amt'] = $order['net_sales_amt'];
                    }
                }
                foreach ($prev_year as $skey => $value) {
                    if(date('Y-m',strtotime($prev_year[$skey]['month'])) == $order['o_year'] . '-' . $order['o_month']) {
                        $prev_year[$skey]['new_business_sales_amt'] = $order['new_business_sales_amt'];
                        $prev_year[$skey]['renewal_sales_amt'] = $order['renewal_sales_amt'];
                        $prev_year[$skey]['gross_sales_amt'] = $order['gross_sales_amt'];
                        $prev_year[$skey]['reversals_amt'] = $order['reversals_amt'];
                        $prev_year[$skey]['net_sales_amt'] = $order['net_sales_amt'];
                    }
                }
            }
        }

        $curr_year_sales = array();
        $pre_year_sales = array();
        foreach ($curr_year as $key => $value) {
            $curr_year_sales[] = array(
                "year" => date('Y',strtotime($curr_year[$key]["month"])),
                "name" => date('F',strtotime($curr_year[$key]["month"])),
                "y" => (int) ($sales_type == "Gross Sales"?$curr_year[$key]["gross_sales_amt"]:$curr_year[$key]["net_sales_amt"]),
                "new_business_sales_amt" => displayAmount($curr_year[$key]["new_business_sales_amt"]),
                "renewal_sales_amt" => displayAmount($curr_year[$key]["renewal_sales_amt"]),
                "gross_sales_amt" => displayAmount($curr_year[$key]["gross_sales_amt"]),
                "reversals_amt" => displayAmount($curr_year[$key]["reversals_amt"]),
                "net_sales_amt" => displayAmount($curr_year[$key]["net_sales_amt"]),
            );

            $pre_year_sales[] = array(
                "year" => date('Y',strtotime($prev_year[$key]["month"])),
                "name" => date('F',strtotime($prev_year[$key]["month"])),
                "y" => (int) ($sales_type == "Gross Sales"?$prev_year[$key]["gross_sales_amt"]:$prev_year[$key]["net_sales_amt"]),
                "new_business_sales_amt" => displayAmount($prev_year[$key]["new_business_sales_amt"]),
                "renewal_sales_amt" => displayAmount($prev_year[$key]["renewal_sales_amt"]),
                "gross_sales_amt" => displayAmount($prev_year[$key]["gross_sales_amt"]),
                "reversals_amt" => displayAmount($prev_year[$key]["reversals_amt"]),
                "net_sales_amt" => displayAmount($prev_year[$key]["net_sales_amt"]),
            );
        }
        $res[$report] = array('curr_year_sales' => $curr_year_sales,'pre_year_sales' => $pre_year_sales,'months' => $months);
    }else{
        if($include_renewals == false && $include_new_business == false) {
                $curr_year = array();
                $prev_year = array();
                $months = array();
                $rows = array();
                for ($i = 0; $i < 12; $i++) {
                    $months[] = date("M", strtotime(date('Y-01-01')." + $i months"));
                    $curr_year[$i] = array(
                        'month' => date("Y-m-d", strtotime(date('Y-01-01')." + $i months")),
                        'new_business_sales_amt' => 0,
                        'renewal_sales_amt' => 0,
                        'gross_sales_amt' => 0,
                        'reversals_amt' => 0,
                        'net_sales_amt' => 0,
                    );
        
                    $prev_year[$i] = array(
                        'month' => date("Y-m-d", strtotime(date('Y-01-01')." + $i months -1 year")),
                        'new_business_sales_amt' => 0,
                        'renewal_sales_amt' => 0,
                        'gross_sales_amt' => 0,
                        'reversals_amt' => 0,
                        'net_sales_amt' => 0,
                    );
                }
                $curr_year_sales = array();
                $pre_year_sales = array();
                foreach ($curr_year as $key => $value) {
                    $curr_year_sales[] = array(
                        "year" => date('Y',strtotime($curr_year[$key]["month"])),
                        "name" => date('F',strtotime($curr_year[$key]["month"])),
                        "y" => (int) ($sales_type == "Gross Sales"?$curr_year[$key]["gross_sales_amt"]:$curr_year[$key]["net_sales_amt"]),
                        "new_business_sales_amt" => displayAmount($curr_year[$key]["new_business_sales_amt"]),
                        "renewal_sales_amt" => displayAmount($curr_year[$key]["renewal_sales_amt"]),
                        "gross_sales_amt" => displayAmount($curr_year[$key]["gross_sales_amt"]),
                        "reversals_amt" => displayAmount($curr_year[$key]["reversals_amt"]),
                        "net_sales_amt" => displayAmount($curr_year[$key]["net_sales_amt"]),
                    );
        
                    $pre_year_sales[] = array(
                        "year" => date('Y',strtotime($prev_year[$key]["month"])),
                        "name" => date('F',strtotime($prev_year[$key]["month"])),
                        "y" => (int) ($sales_type == "Gross Sales"?$prev_year[$key]["gross_sales_amt"]:$prev_year[$key]["net_sales_amt"]),
                        "new_business_sales_amt" => displayAmount($prev_year[$key]["new_business_sales_amt"]),
                        "renewal_sales_amt" => displayAmount($prev_year[$key]["renewal_sales_amt"]),
                        "gross_sales_amt" => displayAmount($prev_year[$key]["gross_sales_amt"]),
                        "reversals_amt" => displayAmount($prev_year[$key]["reversals_amt"]),
                        "net_sales_amt" => displayAmount($prev_year[$key]["net_sales_amt"]),
                    );
                }
                $data = array('curr_year_sales' => $curr_year_sales,'pre_year_sales' => $pre_year_sales,'months' => $months);
        }else{
            if($include_renewals == true && $include_new_business == true) {
                $data = $redisCache->getOrGenerateCache('All','sales_bar_chart_data_admin','get');
            } elseif($include_new_business== true) {
                $data = $redisCache->getOrGenerateCache('All','sales_bar_chart_data_new_business','get');
            } elseif($include_renewals == true) {
                $data = $redisCache->getOrGenerateCache('All','sales_bar_chart_data_renewal','get');
            }
            foreach ($data as $year_key => $year_data) {
                if($year_key == "curr_year_sales" || $year_key == "pre_year_sales"){
                    foreach ($year_data as $key => $value) {
                        if($sales_type == 'Gross Sales'){
                            $amount = $value['gross_sales_temp_amt'];
                        }else{
                            $amount = $value['net_sales_temp_amt'];
                        }
                        $data[$year_key][$key]['y'] =(int) $amount;
                        unset($data[$year_key][$key]['gross_sales_temp_amt']);
                        unset($data[$year_key][$key]['net_sales_temp_amt']);
                    }
                }
            }
        }
        $res[$report] = $data;
    }
}

if($report == "top_performing_state") {
    $response_html = '';
    $state_res = array();

    if($top_state_short_by == "Sales") {
        if($sales_type == "Gross Sales") {
            $SortBy = "gross_sales_amt desc, total_member desc";
        } else {
            $SortBy = "net_sales_amt desc, total_member desc";
        }
    } else {
        if($sales_type == "Gross Sales") {
            $SortBy = "total_member desc, gross_sales_amt desc";
        } else {
            $SortBy = "total_member desc, net_sales_amt desc";
        }
    }

    $sch_params = array();
    $incr = "";
    $where_incr = " AND 1 ";
    
    $upline_incr = "";
    if($portal == 'agent' && isset($_SESSION['agents']['id'])) {
        $upline_incr = " JOIN customer as downline ON (t.customer_id = downline.id AND downline.upline_sponsors LIKE CONCAT('%,',".$_SESSION['agents']['id'].",',%') AND downline.type IN('Customer','Group'))";
    }

    if (strtotime($fromdate) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($fromdate)).' 00:00:00';
        $incr .= " AND t.created_at >= :fcreated_at";
    }
    if (strtotime($todate) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($todate)).' 23:59:59';
        $incr .= " AND t.created_at <= :tcreated_at";
    }

    $amt_column = "(t.new_business_total + t.renewal_total)";
    $member_count = "(t.new_business_members + t.renewal_members)";
    if($include_renewals == true && $include_new_business == true) {
        
    } elseif($include_renewals == true) {
        $where_incr .= " AND (o.is_renewal='Y' OR (o.is_renewal='L' AND od.is_renewal='Y'))";
        $amt_column = "(t.renewal_total)";
        $member_count = "(t.renewal_members)";

    } elseif($include_new_business == true) {
        $where_incr .= " AND (o.is_renewal='N' OR (o.is_renewal='L' AND od.is_renewal='N'))";
        $amt_column = "(t.new_business_total)";
        $member_count = "(t.new_business_members)";
    }

    if($sales_type == "Gross Sales") {
        $having_clause = "gross_sales_amt != 0";
    } else {
        $having_clause = "net_sales_amt != 0";
    }
    $sql = "SELECT 
            ord.state as state_name,
            ord.gross_sales_amt,
            ord.reversals_amt,
            (ord.gross_sales_amt - ord.reversals_amt) as net_sales_amt,
            ord.total_member,           
            s.id as state_id               
            FROM states_c s
            JOIN (
                SELECT c.state,
                SUM(IF(t.transaction_type = 'Renewal Order' OR t.transaction_type = 'New Order' OR t.transaction_type = 'List Bill Order',IF(od.lbd_transaction_type='refund',(od.unit_price * -1),od.unit_price),0)) AS gross_sales_amt,
                SUM(IF((o.status='Refund' OR o.status='Void' OR o.status='Chargeback' OR od.is_refund = 'Y' OR od.is_chargeback = 'Y' OR od.is_payment_return = 'Y') AND (t.transaction_type = 'Refund Order' OR t.transaction_type = 'Chargeback' OR t.transaction_type = 'Void Order'),od.unit_price,0)) AS reversals_amt,
                COUNT(DISTINCT (CASE WHEN t.transaction_type = 'Renewal Order' OR t.transaction_type = 'New Order' OR t.transaction_type = 'List Bill Order' THEN ws.customer_id END)) AS total_member
                FROM transactions as t $upline_incr
                JOIN orders o ON (o.id = t.order_id)
                JOIN order_details od ON (od.order_id = o.id AND od.is_deleted='N')
                JOIN website_subscriptions ws ON (ws.id = od.website_id)
                JOIN customer as c ON (c.id = ws.customer_id AND c.state != '' AND c.state IS NOT NULL)
                WHERE 1 $where_incr  AND t.transaction_type IN ('New Order','Renewal Order','Refund Order','List Bill Order','Chargeback','Void Order') $incr
                GROUP BY c.state
            ) as ord ON (ord.state = s.name)            
            WHERE s.is_deleted='N' AND s.country_id = 231 
            GROUP BY s.id 
            HAVING $having_clause 
            ORDER BY  $SortBy";

    if($include_renewals == false && $include_new_business == false) {
        $rows = array();
    } else {
        $rows = $pdo->select($sql,$sch_params);
    }
    if(!empty($rows)) {
        foreach($rows as $key => $row) {
            $state_res[$row['state_name']] = array(
                'state_name' => $row['state_name'],
            );

            if($sales_type == "Gross Sales") {
                $sales_amt = displayAmount($row['gross_sales_amt']);
            } else {
                $sales_amt = displayAmount($row['net_sales_amt']);
            }
            
            $response_html .= '<tr id="top_performing_state_'.$row['state_name'].'">';
            $response_html .= "<td><a href='javascript:void(0);' class=''>".$row['state_name']."</a></td>";
            $response_html .= "<td class='text-right fw600'>".$row['total_member']."/".$sales_amt."</td>";
            $response_html .= '</tr>';
        }
    } else {
        $response_html .= "<tr><td colspan='2' style='text-align:center;'>No record(s) found</td></tr>";
    }

    $state_options = '<option value="">State</option>';    
    if(!empty($state_res)) {
        ksort($state_res);
        foreach ($state_res as $key => $row) {
            $state_options .= '<option value="'.$row['state_name'].'">'.$row['state_name'].'</option>';
        }
    }
    $res['state_options'] = $state_options;
    $res[$report] = $response_html;
}

if($report == "top_performing_products") {
    $response_html = '';
    $product_res = array();

    if($top_products_short_by == "Sales") {
        if($sales_type == "Gross Sales") {
            $SortBy = "gross_sales_amt desc, total_member desc";
        } else {
            $SortBy = "net_sales_amt desc, total_member desc";
        }
    } else {
        if($sales_type == "Gross Sales") {
            $SortBy = "total_member desc, gross_sales_amt desc";
        } else {
            $SortBy = "total_member desc, net_sales_amt desc";
        }
    }
    $sch_params = array();
    $incr = "";
    $where_incr = " AND 1 ";
    
    $upline_incr = "";
    if($portal == 'agent' && isset($_SESSION['agents']['id'])) {
        $upline_incr = " JOIN customer as downline ON (t.customer_id = downline.id AND downline.upline_sponsors LIKE CONCAT('%,',".$_SESSION['agents']['id'].",',%') AND downline.type IN('Customer','Group'))";
    }

    if (strtotime($fromdate) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($fromdate)).' 00:00:00';
        $incr .= " AND t.created_at >= :fcreated_at";
    }
    if (strtotime($todate) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($todate)).' 23:59:59';
        $incr .= " AND t.created_at <= :tcreated_at";
    }

    $amt_column = "(t.new_business_total + t.renewal_total)";
    $member_count = "(t.new_business_members + t.renewal_members)";
    if($include_renewals == true && $include_new_business == true) {
        
    } elseif($include_renewals == true) {
        $where_incr .= " AND (o.is_renewal='Y' OR (o.is_renewal='L' AND od.is_renewal='Y'))";
        $amt_column = "(t.renewal_total)";
        $member_count = "(t.renewal_members)";

    } elseif($include_new_business == true) {
        $where_incr .= " AND (o.is_renewal='N' OR (o.is_renewal='L' AND od.is_renewal='N'))";
        $amt_column = "(t.new_business_total)";
        $member_count = "(t.new_business_members)";
    }
    
    if($sales_type == "Gross Sales") {
        $having_clause = "ord.gross_sales_amt != 0";
    } else {
        $having_clause = "net_sales_amt != 0";
    }
    $sql = "SELECT 
            p.name AS product_name,
            p.product_code,
            ord.gross_sales_amt,
            ord.reversals_amt,
            (ord.gross_sales_amt - ord.reversals_amt) as net_sales_amt,
            ord.total_member,
            p.id AS product_id  
            FROM prd_main p
            JOIN (
                SELECT 
                    SUM(IF(t.transaction_type = 'Renewal Order' OR t.transaction_type = 'New Order' OR t.transaction_type = 'List Bill Order',IF(od.lbd_transaction_type='refund',(od.unit_price * -1),od.unit_price),0)) AS gross_sales_amt,

                    SUM(IF((o.status='Refund' OR o.status='Void' OR o.status='Chargeback' OR od.is_refund = 'Y' OR od.is_chargeback = 'Y' OR od.is_payment_return = 'Y') AND (t.transaction_type = 'Refund Order' OR t.transaction_type = 'Chargeback' OR t.transaction_type = 'Void Order'),od.unit_price,0)) AS reversals_amt,

                    COUNT(DISTINCT (CASE WHEN t.transaction_type = 'Renewal Order' OR t.transaction_type = 'New Order' OR t.transaction_type = 'List Bill Order' THEN ws.customer_id END)) AS total_member,
                    IF(p.parent_product_id = 0,p.id,p.parent_product_id) AS parent_product_id
                FROM prd_main p 
                JOIN order_details od ON(p.id = od.product_id AND od.is_deleted='N')
                JOIN website_subscriptions ws ON(ws.id = od.website_id)
                JOIN orders as o ON (od.order_id = o.id $where_incr)
                JOIN transactions as t ON (t.order_id = o.id) $upline_incr
                WHERE p.is_deleted='N' AND p.type = 'Normal' AND t.transaction_type IN ('New Order','Renewal Order','List Bill Order','Refund Order','Chargeback','Void Order') $incr 
                GROUP BY p.id
            ) as ord ON (ord.parent_product_id = p.id)
            WHERE p.is_deleted='N' AND p.parent_product_id = 0 AND p.type = 'Normal'
            GROUP BY p.id
            HAVING $having_clause  
            ORDER BY  $SortBy";
    
    if($include_renewals == false && $include_new_business == false) {
        $rows = array();
    } else {
        $rows = $pdo->select($sql,$sch_params);
    }
    if(!empty($rows)) {
        $total_sales = 0;
        foreach($rows as $key => $row) {
            if($sales_type == "Gross Sales") {
                $total_sales += $row['gross_sales_amt'];
            } else {
                $total_sales += $row['net_sales_amt'];
            }
        }

        foreach($rows as $key => $row) {
            $product_res[$row['product_name']] = array(
                'product_id' => $row['product_id'],
                'product_name' => $row['product_name'],
                'product_code' => $row['product_code'],
            );

            if($sales_type == "Gross Sales") {
                $sales_amt = $row['gross_sales_amt'];
            } else {
                $sales_amt = $row['net_sales_amt'];
            }

            $prd_sales_per = 0;
            if($total_sales > 0) {
                $prd_sales_per = (100 * $sales_amt) / $total_sales;
            }

            $sales_amt = displayAmount($sales_amt);
            $response_html .= '<tr id="top_performing_product_'.$row['product_id'].'">';
            $response_html .= '<td><p class="mn">'.$row['product_name'].'</p><div class="progress mn"><div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="'.$prd_sales_per.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$prd_sales_per.'%"></div></div></td>';
            $response_html .= "<td class='w-110 text-right fw600'>".$row['total_member']."/".$sales_amt."</td>";
            $response_html .= '</tr>';
        }
    } else {
        $response_html .= "<tr><td colspan='2' style='text-align:center;'>No record(s) found</td></tr>";
    }

    $product_options = '<option value="">Products</option>';
    if(!empty($product_res)) {
        ksort($product_res);
        foreach ($product_res as $key => $row) {
            $product_options .= '<option value="'.$row['product_id'].'">'.$row['product_name'].' ('.$row['product_code'].')</option>';
        }
    }

    $res['product_options'] = $product_options;
    $res[$report] = $response_html;
}

if($report == "renewal_summary") {
    if(isset($_SESSION['agents']['id'])){
        $upline_incr = "";
        if($portal == 'agent' && isset($_SESSION['agents']['id'])) {
            $upline_incr = " AND c.upline_sponsors LIKE CONCAT('%,',".$_SESSION['agents']['id'].",',%') AND c.type='Customer'";
        }

        // Remaining Renewals : This is the amount and number of renewal transactions that remain from the tomorrow to end of month.
        $remaining_renewals_amt = 0;
        $remaining_renewals_trans = 0;
        
        $sql = "SELECT COUNT(DISTINCT c.id, w.next_purchase_date) as remaining_renewals_trans,SUM(w.price) as remaining_renewals_amt
                FROM website_subscriptions w
                JOIN prd_main pm ON(pm.id=w.product_id AND pm.product_type != 'Healthy Step')
                JOIN customer c on c.id=w.customer_id $upline_incr
                JOIN orders o ON(o.customer_id=w.customer_id AND o.is_renewal='N' AND o.status='Payment Approved')
                WHERE (w.termination_date IS NULL OR w.termination_date > DATE_ADD(w.start_coverage_period, INTERVAL 1 MONTH)) AND c.status IN ('Active','Pending') AND c.type='Customer' AND
                (
                    (DATE(w.next_purchase_date) > :today_date AND DATE(w.next_purchase_date) <= :month_end_date)
                )
                AND w.status in('Active','Pending') AND w.is_onetime='N'";
        // OR (DATE(w.next_attempt_at) > :today_date AND DATE(w.next_attempt_at) <= :month_end_date AND w.total_attempts>0)
        $row = $pdo->selectOne($sql,array(":today_date" => $today_date,":month_end_date" => $month_end_date));
        if(!empty($row)) {
            $remaining_renewals_amt = $row['remaining_renewals_amt'];
            $remaining_renewals_trans = $row['remaining_renewals_trans'];
        }

        //Average Daily Collection : This is the average percentage amount of renewals collected for the past 3 months.
        $avg_daily_collection = 100;

        $upline_incr = "";
        if($portal == 'agent' && isset($_SESSION['agents']['id'])) {
            $upline_incr = " JOIN customer as downline ON (t.customer_id = downline.id AND downline.upline_sponsors LIKE CONCAT('%,',".$_SESSION['agents']['id'].",',%') AND downline.type='Customer')";
        }

        $sel_sql = "SELECT COUNT(DISTINCT o.id) AS total_renewal_trans,COUNT(DISTINCT (CASE WHEN t.transaction_type='Renewal Order' THEN o.id END)) AS approved_renewal_trans
            FROM transactions t $upline_incr
            JOIN orders o ON(o.id = t.order_id)
            WHERE o.is_renewal='Y' AND t.transaction_type IN ('Renewal Order','Payment Declined') AND DATE(t.created_at) >= :three_month_ago_date";
        $row = $pdo->selectOne($sel_sql,array(":three_month_ago_date" => $three_month_ago_date));

        if(!empty($row)) {
            if($row['total_renewal_trans'] > 0) {
                $avg_daily_collection = (100 * $row['approved_renewal_trans']) / $row['total_renewal_trans'];
                $avg_daily_collection = number_format((float)$avg_daily_collection,2,'.','');
            }
        }

        //Projected Collection : This is the remaining renewals amount and numbers multiplied by the average daily collection.
        $ren_proj_collection_trans = 0;
        $ren_proj_collection_amt = 0;

        if($avg_daily_collection > 0) {
            $ren_proj_collection_trans = ($remaining_renewals_trans * $avg_daily_collection) / 100;
            $ren_proj_collection_amt = ($remaining_renewals_amt * $avg_daily_collection) / 100;
        }
        $ren_proj_collection_trans = round($ren_proj_collection_trans,2);

        //Projected Monthly Total : This is the current projected collection amount added to the already collected renewal transactions for this month.
        $ren_proj_monthly_total_trans = $ren_proj_collection_trans;
        $ren_proj_monthly_total_amt = $ren_proj_collection_amt;

        $sel_sql = "SELECT COUNT(DISTINCT t.id) AS collected_renewal_trans,SUM(t.credit) AS collected_renewal_sales_amt
            FROM transactions t $upline_incr
            WHERE t.transaction_type IN ('Renewal Order') AND DATE(t.created_at) >= :month_start_date AND DATE(t.created_at) <= :today_date";
        $where = array(
            ":month_start_date" => $month_start_date,
            ":today_date" => $today_date
        );
        $row = $pdo->selectOne($sel_sql,$where);
        if(!empty($row)) {
            $ren_proj_monthly_total_trans += $row['collected_renewal_trans'];
            $ren_proj_monthly_total_amt += $row['collected_renewal_sales_amt'];
        }

        $data = array(
            'remaining_renewals_amt' => displayAmount($remaining_renewals_amt),
            'remaining_renewals_trans' => round($remaining_renewals_trans),
            'avg_daily_collection' => displaypercentage($avg_daily_collection),
            'ren_proj_collection_amt' => displayAmount($ren_proj_collection_amt),
            'ren_proj_collection_trans' => round($ren_proj_collection_trans),
            'ren_proj_monthly_total_amt' => displayAmount($ren_proj_monthly_total_amt),
            'ren_proj_monthly_total_trans' => round($ren_proj_monthly_total_trans),
        );
    }else{
        $data = $redisCache->getOrGenerateCache('All','renewal_summary_admin','get');
    }
    $res[$report] = $data;
}

if($report == "commission_chart") {
    if($commission_duration == 'monthly') {
        $curr_year = array();
        $prev_year = array();
        $months = array();

        for ($i = 0; $i < 12; $i++) {
            $months[] = date("M", strtotime(date('Y-01-01')." + $i months"));
            $curr_year[$i] = array(
                'month' => date("Y-m-d", strtotime(date('Y-01-01')." + $i months")),
                'new_business_total' => 0,
                'renewal_total' => 0,
                'pmpm_total' => 0,
                'advance_total' => 0,
                'gross_total' => 0,
                'reversals_total' => 0,
                'fee_total' => 0,
                'net_total' => 0,
            );

            $prev_year[$i] = array(
                'month' => date("Y-m-d", strtotime(date('Y-01-01')." + $i months -1 year")),
                'new_business_total' => 0,
                'renewal_total' => 0,
                'pmpm_total' => 0,
                'advance_total' => 0,
                'gross_total' => 0,
                'reversals_total' => 0,
                'fee_total' => 0,
                'net_total' => 0,
            );
        }

        $sch_params = array();
        $sch_params[':customer_id'] = $_SESSION['agents']['id'];

        $sql = "SELECT
                    IFNULL(SUM(IF(cs.sub_type='New',cs.amount,0)),0) as new_business_total,
                    IFNULL(SUM(IF(cs.sub_type='Renewals',cs.amount,0)),0) as renewal_total,
                    IFNULL(SUM(IF(cs.sub_type='PMPM',cs.amount,0)),0) as pmpm_total,
                    IFNULL(SUM(IF(cs.sub_type='Advance',cs.amount,0)),0) as advance_total,
                    IFNULL(SUM(IF(cs.sub_type='Reverse',cs.amount,0)),0) as reversals_total,
                    IFNULL(SUM(IF(cs.sub_type='Fee',cs.amount,0)),0) as fee_total,
                    DATE_FORMAT(cs.created_at,'%m')as o_month,YEAR(cs.created_at)as o_year,DATE_FORMAT(cs.created_at,'%d')as o_day
                 FROM commission cs
                 JOIN customer c ON(c.id=cs.customer_id)
                 WHERE 
                 cs.commission_duration='monthly' AND 
                 cs.customer_id=:customer_id AND 
                 cs.status IN ('Approved','Pending') AND 
                 cs.is_deleted='N' 
                 GROUP BY YEAR(cs.created_at),MONTH(cs.created_at)";

        $res = $pdo->select($sql,$sch_params);
        foreach ($res as $key => $row) {
            foreach ($curr_year as $skey => $value) {
                if(date('Y-m',strtotime($curr_year[$skey]['month'])) == $row['o_year'] . '-' . $row['o_month']) {
                    $curr_year[$skey]['new_business_total'] = $row['new_business_total'];
                    $curr_year[$skey]['renewal_total'] = $row['renewal_total'];
                    $curr_year[$skey]['pmpm_total'] = $row['pmpm_total'];
                    $curr_year[$skey]['advance_total'] = $row['advance_total'];
                    $curr_year[$skey]['gross_total'] = ($row['new_business_total'] + $row['renewal_total'] + $row['pmpm_total'] + $row['advance_total']);
                    $curr_year[$skey]['reversals_total'] = abs($row['reversals_total']);
                    $curr_year[$skey]['fee_total'] = abs($row['fee_total']);
                    $curr_year[$skey]['net_total'] = (($row['new_business_total'] + $row['renewal_total'] + $row['pmpm_total'] + $row['advance_total']) - abs($row['reversals_total']) - abs($row['fee_total']));
                }
            }
            foreach ($prev_year as $skey => $value) {
                if(date('Y-m',strtotime($prev_year[$skey]['month'])) == $row['o_year'] . '-' . $row['o_month']) {
                    $prev_year[$skey]['new_business_total'] = $row['new_business_total'];
                    $prev_year[$skey]['renewal_total'] = $row['renewal_total'];
                    $prev_year[$skey]['pmpm_total'] = $row['pmpm_total'];
                    $prev_year[$skey]['advance_total'] = $row['advance_total'];
                    $prev_year[$skey]['gross_total'] = ($row['new_business_total'] + $row['renewal_total'] + $row['pmpm_total'] + $row['advance_total']);
                    $prev_year[$skey]['reversals_total'] = abs($row['reversals_total']);
                    $prev_year[$skey]['fee_total'] = abs($row['fee_total']);
                    $prev_year[$skey]['net_total'] = (($row['new_business_total'] + $row['renewal_total'] + $row['pmpm_total'] + $row['advance_total']) - abs($row['reversals_total']) - abs($row['fee_total']));
                }
            }
        }
        $curr_year_sales = array();
        $pre_year_sales = array();
        foreach ($curr_year as $key => $value) {
            $curr_year_sales[] = array(
                "year" => date('Y',strtotime($curr_year[$key]["month"])),
                "name" => date('F',strtotime($curr_year[$key]["month"])),
                "y" => (int) $curr_year[$key]["net_total"],
                "new_business_total" => displayAmount($curr_year[$key]["new_business_total"]),
                "renewal_total" => displayAmount($curr_year[$key]["renewal_total"]),
                "pmpm_total" => displayAmount($curr_year[$key]["pmpm_total"]),
                "advance_total" => displayAmount($curr_year[$key]["advance_total"]),
                "gross_total" => displayAmount($curr_year[$key]["gross_total"]),
                "reversals_total" => displayAmount($curr_year[$key]["reversals_total"]),
                "fee_total" => displayAmount($curr_year[$key]["fee_total"]),
                "net_total" => displayAmount($curr_year[$key]["net_total"]),
            );

            $pre_year_sales[] = array(
                "year" => date('Y',strtotime($prev_year[$key]["month"])),
                "name" => date('F',strtotime($prev_year[$key]["month"])),
                "y" => (int) $prev_year[$key]["net_total"],
                "new_business_total" => displayAmount($prev_year[$key]["new_business_total"]),
                "renewal_total" => displayAmount($prev_year[$key]["renewal_total"]),
                "pmpm_total" => displayAmount($prev_year[$key]["pmpm_total"]),
                "advance_total" => displayAmount($prev_year[$key]["advance_total"]),
                "gross_total" => displayAmount($prev_year[$key]["gross_total"]),
                "reversals_total" => displayAmount($prev_year[$key]["reversals_total"]),
                "fee_total" => displayAmount($prev_year[$key]["fee_total"]),
                "net_total" => displayAmount($prev_year[$key]["net_total"]),
            );
        }
        $res[$report] = array('curr_year_sales' => $curr_year_sales,'pre_year_sales' => $pre_year_sales,'x_axis_values' => $months);
    } else {
        $curr_year = array();
        $prev_year = array();
        $weeks = array();

        include_once __DIR__ . "/includes/commission.class.php";
        $commObj = new Commission();


        $tmp_today = date('Y-m-d');
        $pay_period = $commObj->getWeeklyPayPeriod($tmp_today);
        $tmp_pay_period = $pay_period;

        for ($i = 0; $i < 12; $i++) {

            $weeks[$i] = date("m/d",strtotime('-6 days',strtotime($tmp_pay_period))).'-'.date("m/d",strtotime($tmp_pay_period));

            $curr_year[$i] = array(
                'index' => $i,
                'pay_period' => date("Y-m-d", strtotime($tmp_pay_period)),
                'label' => date("m/d",strtotime('-6 days',strtotime($tmp_pay_period))).'-'.date("m/d",strtotime($tmp_pay_period)),
                'new_business_total' => 0,
                'renewal_total' => 0,
                'pmpm_total' => 0,
                'advance_total' => 0,
                'gross_total' => 0,
                'reversals_total' => 0,
                'fee_total' => 0,
                'net_total' => 0,
            );
            $prev_year[$i] = array(
                'index' => $i,
                'pay_period' => date("Y-m-d", strtotime('-1 year',strtotime($tmp_pay_period))),
                'label' => date("m/d",strtotime('-6 days',strtotime($tmp_pay_period))).'-'.date("m/d",strtotime($tmp_pay_period)),
                'new_business_total' => 0,
                'renewal_total' => 0,
                'pmpm_total' => 0,
                'advance_total' => 0,
                'gross_total' => 0,
                'reversals_total' => 0,
                'fee_total' => 0,
                'net_total' => 0,
            );
            $tmp_pay_period = date('Y-m-d',strtotime('-7 days',strtotime($tmp_pay_period)));
        }

        krsort($curr_year);
        krsort($prev_year);
        $weeks = array_reverse($weeks);

        $sch_params = array();
        $sch_params[':customer_id'] = $_SESSION['agents']['id'];

        foreach ($curr_year as $tmp_key => $tmp_value) {
            $sch_params[':pay_period'] = $curr_year[$tmp_key]['pay_period'];

            $sql = "SELECT
                    IFNULL(SUM(IF(cs.sub_type='New',cs.amount,0)),0) as new_business_total,
                    IFNULL(SUM(IF(cs.sub_type='Renewals',cs.amount,0)),0) as renewal_total,
                    IFNULL(SUM(IF(cs.sub_type='PMPM',cs.amount,0)),0) as pmpm_total,
                    IFNULL(SUM(IF(cs.sub_type='Advance',cs.amount,0)),0) as advance_total,
                    IFNULL(SUM(IF(cs.sub_type='Reverse',cs.amount,0)),0) as reversals_total,
                    IFNULL(SUM(IF(cs.sub_type='Fee',cs.amount,0)),0) as fee_total
                 FROM commission cs
                 JOIN customer c ON(c.id=cs.customer_id)
                 WHERE 
                 cs.commission_duration='weekly' AND 
                 cs.customer_id=:customer_id AND 
                 cs.status IN ('Approved','Pending') AND 
                 cs.is_deleted='N' AND cs.pay_period=:pay_period
                 GROUP BY cs.pay_period";
            $curr_row = $pdo->selectOne($sql,$sch_params);
            if(!empty($curr_row)) {
                $curr_year[$tmp_key]['new_business_total'] = $curr_row['new_business_total'];
                $curr_year[$tmp_key]['renewal_total'] = $curr_row['renewal_total'];
                $curr_year[$tmp_key]['pmpm_total'] = $curr_row['pmpm_total'];
                $curr_year[$tmp_key]['advance_total'] = $curr_row['advance_total'];
                $curr_year[$tmp_key]['gross_total'] = ($curr_row['new_business_total'] + $curr_row['renewal_total'] + $curr_row['pmpm_total'] + $curr_row['advance_total']);
                $curr_year[$tmp_key]['reversals_total'] = abs($curr_row['reversals_total']);
                $curr_year[$tmp_key]['fee_total'] = abs($curr_row['fee_total']);
                $curr_year[$tmp_key]['net_total'] = (($curr_row['new_business_total'] + $curr_row['renewal_total'] + $curr_row['pmpm_total'] + $curr_row['advance_total']) - abs($curr_row['reversals_total']) - abs($curr_row['fee_total']));
            }

            $sch_params[':pay_period'] = $prev_year[$tmp_key]['pay_period'];
            $sql = "SELECT
                    IFNULL(SUM(IF(cs.sub_type='New',cs.amount,0)),0) as new_business_total,
                    IFNULL(SUM(IF(cs.sub_type='Renewals',cs.amount,0)),0) as renewal_total,
                    IFNULL(SUM(IF(cs.sub_type='PMPM',cs.amount,0)),0) as pmpm_total,
                    IFNULL(SUM(IF(cs.sub_type='Advance',cs.amount,0)),0) as advance_total,
                    IFNULL(SUM(IF(cs.sub_type='Reverse',cs.amount,0)),0) as reversals_total,
                    IFNULL(SUM(IF(cs.sub_type='Fee',cs.amount,0)),0) as fee_total
                 FROM commission cs
                 JOIN customer c ON(c.id=cs.customer_id)
                 WHERE 
                 cs.commission_duration='weekly' AND 
                 cs.customer_id=:customer_id AND 
                 cs.status IN ('Approved','Pending') AND 
                 cs.is_deleted='N' AND cs.pay_period=:pay_period
                 GROUP BY cs.pay_period";
            $prev_row = $pdo->selectOne($sql,$sch_params);
            if(!empty($prev_row)) {
                $prev_year[$tmp_key]['new_business_total'] = $prev_row['new_business_total'];
                $prev_year[$tmp_key]['renewal_total'] = $prev_row['renewal_total'];
                $prev_year[$tmp_key]['pmpm_total'] = $prev_row['pmpm_total'];
                $prev_year[$tmp_key]['advance_total'] = $prev_row['advance_total'];
                $prev_year[$tmp_key]['gross_total'] = ($prev_row['new_business_total'] + $prev_row['renewal_total'] + $prev_row['pmpm_total'] + $prev_row['advance_total']);
                $prev_year[$tmp_key]['reversals_total'] = abs($prev_row['reversals_total']);
                $prev_year[$tmp_key]['fee_total'] = abs($prev_row['fee_total']);
                $prev_year[$tmp_key]['net_total'] = (($prev_row['new_business_total'] + $prev_row['renewal_total'] + $prev_row['pmpm_total'] + $prev_row['advance_total']) - abs($prev_row['reversals_total']) - abs($prev_row['fee_total']));
            }
        }

        $curr_year_sales = array();
        $pre_year_sales = array();
        foreach ($curr_year as $key => $value) {
            $curr_year_sales[] = array(
                "year" => date('Y',strtotime($curr_year[$key]["pay_period"])),
                "name" => $curr_year[$key]["label"],
                "y" => (int) $curr_year[$key]["net_total"],
                "new_business_total" => displayAmount($curr_year[$key]["new_business_total"]),
                "renewal_total" => displayAmount($curr_year[$key]["renewal_total"]),
                "pmpm_total" => displayAmount($curr_year[$key]["pmpm_total"]),
                "advance_total" => displayAmount($curr_year[$key]["advance_total"]),
                "gross_total" => displayAmount($curr_year[$key]["gross_total"]),
                "reversals_total" => displayAmount($curr_year[$key]["reversals_total"]),
                "fee_total" => displayAmount($curr_year[$key]["fee_total"]),
                "net_total" => displayAmount($curr_year[$key]["net_total"]),
            );

            $pre_year_sales[] = array(
                "year" => date('Y',strtotime($prev_year[$key]["pay_period"])),
                "name" => $prev_year[$key]["label"],
                "y" => (int) $prev_year[$key]["net_total"],
                "new_business_total" => displayAmount($prev_year[$key]["new_business_total"]),
                "renewal_total" => displayAmount($prev_year[$key]["renewal_total"]),
                "pmpm_total" => displayAmount($prev_year[$key]["pmpm_total"]),
                "advance_total" => displayAmount($prev_year[$key]["advance_total"]),
                "gross_total" => displayAmount($prev_year[$key]["gross_total"]),
                "reversals_total" => displayAmount($prev_year[$key]["reversals_total"]),
                "fee_total" => displayAmount($prev_year[$key]["fee_total"]),
                "net_total" => displayAmount($prev_year[$key]["net_total"]),
            );
        }
        $res[$report] = array('curr_year' => $curr_year,'prev_year'=>$prev_year,'curr_year_sales' => $curr_year_sales,'pre_year_sales' => $pre_year_sales,'x_axis_values' => $weeks);
    }
}

if($report == "gross_net_sales_more") { //Top Header
    $avg_policy_per_member = 0;
    $total_policies = 0;
    $total_member = 0;

    $sch_params = array();
    $incr = "";
    $where_incr = " AND 1 ";

    $upline_incr = "";
    if($portal == 'agent' && isset($_SESSION['agents']['id'])) {
        $upline_incr = " JOIN customer as downline ON (t.customer_id = downline.id AND downline.upline_sponsors LIKE CONCAT('%,',".$_SESSION['agents']['id'].",',%') AND downline.type IN('Customer','Group'))";
    }

    if (strtotime($fromdate) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($fromdate)).' 00:00:00';
        $incr .= " AND t.created_at >= :fcreated_at";
    }
    if (strtotime($todate) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($todate)).' 23:59:59';
        $incr .= " AND t.created_at <= :tcreated_at";
    }

    $amt_column = "(t.new_business_total + t.renewal_total)";
    $member_count = "(t.new_business_members + t.renewal_members)";
    if($include_renewals == true && $include_new_business == true) {
        
    } elseif($include_renewals == true) {
        $where_incr .= " AND (o.is_renewal='Y' OR (o.is_renewal='L' AND od.is_renewal='Y'))";
        $amt_column = "(t.renewal_total)";
        $member_count = "(t.renewal_members)";

    } elseif($include_new_business == true) {
        $where_incr .= " AND (o.is_renewal='N' OR (o.is_renewal='L' AND od.is_renewal='N'))";
        $amt_column = "(t.new_business_total)";
        $member_count = "(t.new_business_members)";
    }

    if($sales_type == "Gross Sales") {
        $total_member_clause = "SUM(IF(t.transaction_type IN('Renewal Order','New Order','List Bill Order'),$member_count,0)) AS total_member,";
        $total_policies_clause = "SUM(IF(t.transaction_type IN('Renewal Order','New Order','List Bill Order'),ord.total_policies,0)) AS total_policies,";
    } else {
        $total_member_clause = "SUM(IF(t.transaction_type IN('Renewal Order','New Order','List Bill Order'),$member_count,($member_count * -1))) AS total_member,";
        $total_policies_clause = "SUM(IF(t.transaction_type IN('Renewal Order','New Order','List Bill Order'),ord.total_policies,(ord.total_policies * -1))) AS total_policies,";
    }

    $sql = "SELECT
            $total_member_clause
            $total_policies_clause
            '' as tmp
            FROM transactions t $upline_incr
            JOIN 
            (
                SELECT o.id,SUM(IF(od.product_type!='Fees',1,0)) AS total_policies,sb.transaction_id
                FROM orders o 
                JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N' AND od.product_type!='Fees')
                JOIN sub_transactions sb ON(sb.order_detail_id = od.id)
                WHERE 1 $where_incr GROUP BY sb.transaction_id
            ) as ord ON (ord.id = t.order_id AND ord.transaction_id=t.id)
            WHERE ord.id IS NOT NULL AND t.transaction_type IN ('New Order','Renewal Order','List Bill Order','Refund Order','Chargeback','Void Order') $incr";

    if($include_renewals == false && $include_new_business == false) {
        $row = array();
    } else {
        $row = $pdo->selectOne($sql,$sch_params);    
    }
    if(!empty($row)) {
        if($row['total_policies'] > 0 && $row['total_member'] > 0) {
            $total_policies = $row['total_policies'];
            $total_member = $row['total_member'];
            $avg_policy_per_member = $row['total_policies'] / $row['total_member'];
        }
    }

    $data = array(
        'row' => $row,
        'selected_date_title' => $selected_date_title,
        'avg_policy_per_member' => number_format((float)$avg_policy_per_member,2,'.',''),
        'new_policies' => $total_policies,
    );
    $res[$report] = $data;
}

echo json_encode($res);
?>