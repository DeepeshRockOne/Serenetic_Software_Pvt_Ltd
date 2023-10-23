<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$action = $_REQUEST['action'];
if($action == "get_agent_products") {
    $agent_id = $_REQUEST['agent_id'];
    $productSql = "SELECT p.*,c.company_name FROM prd_main p
        JOIN agent_product_rule rp ON (rp.product_id=p.id)
              LEFT JOIN prd_company c ON (c.id = p.company_id)
              where p.is_deleted='N' AND p.status='Active' AND rp.is_deleted='N' AND rp.agent_id=:agent_id AND p.category_id NOT IN(3,33) AND rp.status='Contracted' ORDER BY p.name ASC";
    $productRes = $pdo->select($productSql, array(":agent_id" => $agent_id));

    $company_arr = array('Global Products' => array(), 'Group Only Products' => array(), 'Variations' => array());
    if ($productRes) {
        foreach ($productRes as $key => $row) {
            if ($row["parent_product_id"] > 0 && !in_array($row["id"],array(65,67,69))) {
                $company_arr['Variations'][] = $row;
            } else {
              if ($row["allow_sell_to"] == "Agent" || $row["allow_sell_to"] == "All") {
                $company_arr['Global Products'][] = $row;
              } else if ($row["allow_sell_to"] == "Group") {
                $company_arr['Group Only Products'][] = $row;
              }
            }
        }
        if (empty($company_arr['Global Products'])) {
            unset($company_arr['Global Products']);
        }

        if (empty($company_arr['Group Only Products'])){
            unset($company_arr['Group Only Products']);
        }
        if (empty($company_arr['Variations'])) {
            unset($company_arr['Variations']);
        }
    }
    ksort($company_arr);
    echo json_encode($company_arr);
    exit();
}

if($action == "agent_auto_complete") {
    $user_type = 'agent';
    $incr = "";
    if($user_type =="agent")
    {
        $incr.=" AND c.type='Agent' AND cs.agent_coded_level!='LOA' ";   
    }
    $query = $_REQUEST['query'];
    $resArr = array();
    $i=0;
    if ($query != "") {
        $selSql = "SELECT c.id, c.rep_id, c.email, c.fname, c.lname 
                    FROM customer c
                    JOIN customer_settings cs on (cs.customer_id=c.id)
                    WHERE (c.email LIKE ('%" . $query . "%') OR c.rep_id LIKE '" . $query . "%' OR c.user_name LIKE '%" . $query . "%' OR c.fname LIKE '%" . $query . "%' OR c.lname LIKE '%" . $query . "%') $incr";
        $pdo->displayError();
        $rows = $pdo->select($selSql);  
        if (count($rows) > 0) {
            foreach ($rows as $i => $row) {      
                if(!isset($_GET['type']) && empty($_GET['type'])){
                    $resArr[$i]['label'] = $row['rep_id'] .'- '.$row['fname'].' '.$row['lname'].' ('.$row['email'].')';
                }else{
                    $resArr[$i]['label'] = $row['fname'].' '.$row['lname'].' - '.$row['rep_id'];
                }
                $resArr[$i]['val'] = $row['id'];
                $resArr[$i]['rep_id'] = $row['rep_id'];
                $resArr[$i]['fname'] = $row['fname'];
                $resArr[$i]['lname'] = $row['lname'];
                $resArr[$i]['email'] = $row['email'];
                $i++;
            }
        }
    }
    header('Content-Type: application/json');
    echo json_encode($resArr);
    dbConnectionClose();
    exit();
}
?>