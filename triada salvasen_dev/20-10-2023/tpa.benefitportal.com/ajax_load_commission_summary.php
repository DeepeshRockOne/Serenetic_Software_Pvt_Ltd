<?php
    include_once 'includes/connect.php';
    include_once 'admin/includes/admin_functions.php';

    $res = array('status' => "success");
    $incr = '';
    $extVar = '&viewPayable=allPayable';
    $schParams = array();

    $location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';

    $agency_ids = checkIsset($_REQUEST['agency_ids'],'arr');
    $agents_ids = checkIsset($_REQUEST['agents_ids'],'arr');

    $includeDownline = !empty($_REQUEST['includeDownline']) ? "Y" : "N";
    $accountType = checkIsset($_REQUEST['accountType']);
    if($accountType == "Business") {
      $agents = $agency_ids;
    }else {
      $agents = $agents_ids;
    }
    $commType = checkIsset($_REQUEST['commType'],'arr');

    $join_range = checkIsset($_REQUEST['join_range']);
    $fromdate = checkIsset($_REQUEST["fromdate"]);
    $todate = checkIsset($_REQUEST["todate"]);
    $today = date("m/d/Y");
    $added_date = !empty($_REQUEST["added_date"]) ? $_REQUEST["added_date"] : $today;

    if(!empty($join_range)){
      $extVar.='&join_range='.$join_range;
      if($join_range == "Range" && $fromdate!='' && $todate!=''){
        $schParams[':fromdate'] = date("Y-m-d",strtotime($fromdate));
        $schParams[':todate'] = date("Y-m-d",strtotime($todate));
        $incr.=" AND DATE(cs.pay_period) >= :fromdate AND DATE(cs.pay_period) <= :todate";
        $extVar.='&fromdate='.$fromdate.'&todate='.$todate;
      }else if($join_range == "Exactly" && $added_date!=''){
        $schParams[':added_date'] = date("Y-m-d",strtotime($added_date));
        $incr.=" AND DATE(cs.pay_period) = :added_date";
      }else if($join_range == "Before" && $added_date!=''){
        $schParams[':added_date'] = date("Y-m-d",strtotime($added_date));
        $incr.=" AND DATE(cs.pay_period) < :added_date";
      }else if($join_range == "After" && $added_date!=''){
        $schParams[':added_date'] = date("Y-m-d",strtotime($added_date));
        $incr.=" AND DATE(cs.pay_period) > :added_date";
      }
    }

    if(!empty($added_date)){
      $extVar.='&added_date='.$added_date;
    }
    $tbl_join = "";
    if(checkIsset($accountType) == 'Business'){
        $tbl_join = "LEFT JOIN (
                    SELECT dl_agn.sponsor_id,GROUP_CONCAT(dl_agn2.id) AS dl_agn_agent_ids
                    FROM customer dl_agn
                    JOIN customer_settings dl_agn_cs ON(dl_agn_cs.customer_id = dl_agn.id AND dl_agn_cs.account_type='Business')
                    JOIN (
                        SELECT dl_agn1.id,dl_agn1.upline_sponsors
                        FROM customer dl_agn1
                        WHERE dl_agn1.type='Agent' AND dl_agn1.is_deleted='N'
                    ) AS dl_agn2 ON(dl_agn2.id=dl_agn.id OR dl_agn2.upline_sponsors LIKE CONCAT('%,',dl_agn.id,',%'))
                    WHERE dl_agn.type IN('Agent') AND dl_agn.is_deleted='N'
                    GROUP BY dl_agn.sponsor_id
                ) dl_agn ON(dl_agn.sponsor_id=s.id)";
        $incr .= " AND ss.account_type = 'Business' AND (dl_agn.sponsor_id IS NULL OR NOT FIND_IN_SET(cs.customer_id,dl_agn.dl_agn_agent_ids))";
    }

    if(!empty($agents)){
        $tree_incr = "";
        if($includeDownline == "Y"){
          $tree_incr .= " AND (s.id IN (".implode(",", $agents).") OR ";
          foreach ($agents as $key => $agentId) {
            if($key > 0) {
              $tree_incr .= " OR ";
            }
            $tree_incr .= "s.upline_sponsors LIKE CONCAT('%,',".$agentId.",',%')";
          }
          $tree_incr .= ")";
         
          $incr .= $tree_incr;
        }else{
          $incr .= " AND s.id IN (".implode(",", $agents).")";
        }
    }

    if(!in_array('earnedComm', $commType)){
      $incr .= " AND cs.is_advance='Y'";
    }

    if(!in_array('advanceComm', $commType)){
      $incr .= " AND cs.is_advance='N'";
    }
    

    $pageLink = "<a href='javascript:void(0)'>";
    $titleHtml = '<span>Credit : __credit__</span><br><span class="text-danger fw500">Debit : __debit__</span>';

    if(has_menu_access(88) || $location == "agent") { //OP29-566 updates
      $pageLink = "<a href='account_payable.php?payee_id=__agentRepid__".$extVar."' target='_blank' data-toggle='tooltip' data-html='true' data-container='body' title='".$titleHtml."'>";
    } else {
      $pageLink = "<a href='javascript:void(0);'>";
    }

    $resCommAgents = array();

    $totalCommAmt = 0;
    $totalCommCnt = 0;
    $totalCreditsAmt = 0;
    $totalCreditsCnt = 0;
    $totalDebitsAmt = 0;
    $totalDebitsCnt = 0;

    if(!empty($agents)){
      $sqlCommAgents = "SELECT 
                s.rep_id as agentDispId,CONCAT(s.fname,' ',s.lname) as agentName,ss.company_name as companyName,

                SUM(IF(cs.is_advance='N' AND ((cs.sub_type!='Reverse' AND cs.is_fee_comm='N') OR (cs.type='Adjustment' AND cs.balance_type='addCredit') OR (cs.is_fee_comm='Y' AND cs.sub_type='Reverse')),cs.amount,0)) AS earnedCreditsAmt,

                 SUM(IF(cs.is_advance='N' AND ((cs.sub_type='Reverse' AND cs.is_fee_comm='N') OR (cs.type='Adjustment' AND cs.balance_type='revCredit') OR (cs.is_fee_comm='Y' AND cs.sub_type='Fee')),cs.amount,0)) AS earnedDebitsAmt,

                SUM(IF(cs.is_advance='Y' AND cs.sub_type!='Reverse',cs.amount,0)) AS advCreditsAmt,
                SUM(IF(cs.is_advance='Y' AND cs.sub_type='Reverse',cs.amount,0)) AS advDebitsAmt,

                COUNT(DISTINCT(CASE WHEN (cs.is_advance='N' AND ((cs.sub_type!='Reverse' AND cs.is_fee_comm='N') 
                OR (cs.type='Adjustment' AND cs.balance_type='addCredit') 
                OR (cs.is_fee_comm='Y' AND cs.sub_type='Reverse'))) THEN cs.id END)) AS earnedCreditsCnt,

                COUNT(DISTINCT(CASE WHEN (cs.is_advance='N' AND ((cs.sub_type='Reverse' AND cs.is_fee_comm='N') OR (cs.type='Adjustment' AND cs.balance_type='revCredit') OR (cs.is_fee_comm='Y' AND cs.sub_type='Fee'))) THEN cs.id END)) AS earnedDebitsCnt,

                COUNT(DISTINCT(CASE WHEN (cs.is_advance='Y' AND cs.sub_type!='Reverse') THEN cs.id END)) AS advCreditsCnt,

                COUNT(DISTINCT(CASE WHEN (cs.is_advance='Y' AND cs.sub_type='Reverse') THEN cs.id END)) AS advDebitsCnt,

                COUNT(DISTINCT(cs.id)) as totalCommCnt,
                SUM(cs.amount) as totalCommAmt
                FROM commission cs
                JOIN customer s ON(cs.customer_id=s.id)
                $tbl_join
                JOIN customer_settings ss ON(ss.customer_id=s.id)
                WHERE cs.commission_duration='weekly' AND  cs.is_deleted = 'N'" . $incr . " group by s.id";
      $resCommAgents = $pdo->select($sqlCommAgents,$schParams);
    }

  // Commission table summary code start
    $commTblSumm = "";
    $commTblSumm .= "<div class='table-responsive'>
        <table class='".$table_class."'>
          <thead>
            <tr class='data-head'>
              <th>Agent ID</th>
              <th>Agency/Agent Name</th>";


          if(in_array('earnedComm', $commType)){
            $commTblSumm .= "<th>Earned Credits</th>
              <th>Earned Debits</th>";
          }

          if(in_array('advanceComm', $commType)){
            $commTblSumm .= "<th>Advanced Credits</th>
              <th>Advanced Debits</th>";
          }
    $commTblSumm .= "<th>".$pageLink."Total</a></th>
          </tr>
        </thead>
         <tbody>";

    if(!empty($resCommAgents)){
        foreach ($resCommAgents as $agent) {
            $totalRowAmt = 0;
            $totalRowCnt = 0;

            $commTblSumm .= "<tr>
                            <td>".$agent['agentDispId']."</td>";

            if(checkIsset($accountType) == 'Business'){
              $commTblSumm .= "<td>".$agent['companyName']."</td>";
            }else{
              $commTblSumm .= "<td>".$agent['agentName']."</td>";
            }
            
            if(in_array('earnedComm', $commType)){
              $commTblSumm .= "<td>".$agent['earnedCreditsCnt']."/".dispCommAmt($agent['earnedCreditsAmt'])."</td>";
              if($agent['earnedDebitsAmt'] < 0){
                 $commTblSumm .= "<td><span class='text-danger fw500'>".$agent['earnedDebitsCnt']."/(".displayAmount(abs($agent['earnedDebitsAmt'])).")</span>";
              }else{
                $commTblSumm .= "<td>".$agent['earnedDebitsCnt']."/".displayAmount($agent['earnedDebitsAmt'])."</span>";
              }
            }

            if(in_array('advanceComm', $commType)){
              $commTblSumm .= "<td>".$agent['advCreditsCnt']."/".dispCommAmt($agent['advCreditsAmt'])."</td>";
              if($agent['advDebitsAmt'] < 0){
                 $commTblSumm .= "<td><span class='text-danger fw500'>".$agent['advDebitsCnt']."/(".displayAmount(abs($agent['advDebitsAmt'])).")</span>";
              }else{
                $commTblSumm .= "<td>".$agent['advDebitsCnt']."/".displayAmount($agent['advDebitsAmt'])."</span>";
              }
            }

            $totalRowAmt = $agent['earnedCreditsAmt'] + $agent['earnedDebitsAmt'] + $agent['advCreditsAmt'] + $agent['advDebitsAmt'];
            $totalRowCnt = $agent['earnedCreditsCnt'] + $agent['earnedDebitsCnt'] + $agent['advCreditsCnt'] + $agent['advDebitsCnt'];

            $tmpPageLink = str_replace(
            array('__agentRepid__','__credit__','__debit__'),
            array($agent['agentDispId'],displayAmount($agent['earnedCreditsAmt']+$agent['advCreditsAmt']),'('.displayAmount(abs($agent['earnedDebitsAmt'])+abs($agent['advDebitsAmt'])).')'),
            $pageLink);

            if($totalRowAmt < 0){
                 $commTblSumm .= "<td>".$tmpPageLink."<span class='text-danger fw500'>".$totalRowCnt."/(".displayAmount(abs($totalRowAmt)).")</span></a></td>";
            }else{
              $commTblSumm .= "<td>".$tmpPageLink."<span>".$totalRowCnt."/".displayAmount($totalRowAmt)."</span></a></td>";
            }
                            
            $commTblSumm .= "</tr>";

            $totalCreditsAmt += $agent['earnedCreditsAmt'];
            $totalCreditsAmt += $agent['advCreditsAmt'];

            $totalCreditsCnt += $agent['earnedCreditsCnt'];
            $totalCreditsCnt += $agent['advCreditsCnt'];
            
            $totalDebitsAmt += $agent['earnedDebitsAmt'];
            $totalDebitsAmt += $agent['advDebitsAmt'];

            $totalDebitsCnt += $agent['earnedDebitsCnt'];
            $totalDebitsCnt += $agent['advDebitsCnt'];
        }
    }else{
        $commTblSumm .= "<tr><td colspan='7'>No Record(s) Found</td></tr>";
    }

    $commTblSumm .= "</tbody>
        </table>
      </div>";
  // Commission table summary code ends

    $res["commTblSumm"] = $commTblSumm;


  // Commission top table summary code start
    $totalCommCnt = $totalCreditsCnt + $totalDebitsCnt;
    $totalCommAmt = $totalCreditsAmt + $totalDebitsAmt;
    $res["totalCommAmt"] = $totalCommCnt.'/'.dispCommAmt($totalCommAmt);

    if($totalCreditsAmt < 0){
      // OP29-447 updates
      if(in_array('earnedComm', $commType) && in_array('advanceComm', $commType)){
        $res["totalCredits"] = "<span class='text-danger fw500'>(".displayAmount(abs($totalCreditsAmt)).")</span>";
      }else{
        $res["totalCredits"] = "<span class='text-danger fw500'>".$totalCreditsCnt."/(".displayAmount(abs($totalCreditsAmt)).")</span>";
      }
    }else{
      // OP29-447 updates
      if(in_array('earnedComm', $commType) && in_array('advanceComm', $commType)){
        $res["totalCredits"] = "<span>".displayAmount($totalCreditsAmt)."</span>";
      }else{
        $res["totalCredits"] = "<span>".$totalCreditsCnt."/".displayAmount($totalCreditsAmt)."</span>";
      }
    }

    if($totalDebitsAmt < 0){
      // OP29-447 updates
      if(in_array('earnedComm', $commType) && in_array('advanceComm', $commType)){
        $res["totalDebits"] = "<span class='text-danger fw500'>(".displayAmount(abs($totalDebitsAmt)).")</span>";
      }else{
        $res["totalDebits"] = "<span class='text-danger fw500'>".$totalDebitsCnt."/(".displayAmount(abs($totalDebitsAmt)).")</span>";
      }
    }else{
      // OP29-447 updates
      if(in_array('earnedComm', $commType) && in_array('advanceComm', $commType)){
        $res["totalDebits"] = "<span>".displayAmount($totalDebitsAmt)."</span>";
      }else{
        $res["totalDebits"] = "<span>".$totalDebitsCnt."/".displayAmount($totalDebitsAmt)."</span>";
      }
    }

  // Commission top table summary code ends

echo json_encode($res);
dbConnectionClose();
exit;
?>