<?php
function getPrevLabelText($type)
{
    if ($type == "All Time" || $type == "Custom Date") {
        return "";
    } else if ($type == "Today") {
        return "Yesterday";
    } else if ($type == "Yesterday") {
        return "Previous Day";
    } else if ($type == "This Week") {
        return "Last Week";
    } else if ($type == "Last Week") {
        return "Previous Week";
    } else if ($type == "Last 7 Days") {
        return "Previous 7 Day";
    } else if ($type == "This Month") {
        return "Last Month";
    } else if ($type == "Last Month") {
        return "Previous Month";
    } else if ($type == "This Year") {
        return "Last Year";
    }
    return "";
}

function getPrevType($type)
{
    if ($type == "All Time" || $type == "Custom Date") {
        return "";
    } else if ($type == "Today") {
        return "Yesterday";
    } else if ($type == "Yesterday") {
        return "Previousday";
    } else if ($type == "This Week") {
        return "Last Week";
    } else if ($type == "Last Week") {
        return "Prev Last Week";
    } else if ($type == "Last 7 Days") {
        return "Last 14 Days";
    } else if ($type == "This Month") {
        return "Last Month";
    } else if ($type == "Last Month") {
        return "Prev Last Month";
    } else if ($type == "This Year") {
        return "Last Year";
    }
    return "";
}

function getSearchArray($type, $from = "", $to = "")
{
    switch ($type) {
        case "All Time":
            $getfromdate = '';
            $gettodate = '';
            break;
        case "Today":
            $getfromdate = date('m/d/Y');
            $gettodate = date('m/d/Y');
            break;
        case "Yesterday":
            $getfromdate = date("m/d/Y", strtotime("-1 days"));
            $gettodate = date('m/d/Y', strtotime("-1 days"));
            break;
        case "Previousday":
            $getfromdate = date("m/d/Y", strtotime("-2 days"));
            $gettodate = date('m/d/Y', strtotime("-2 days"));
            break;
        case "Last 7 Days":
            $getfromdate = date('m/d/Y', strtotime("-7 day"));
            $gettodate = date('m/d/Y', strtotime("-1 day"));
            break;
        case "Last 14 Days":
            $getfromdate = date('m/d/Y', strtotime("-14 day"));
            $gettodate = date('m/d/Y', strtotime("-8 day"));
            break;
        case "This Week":
            $getfromdate = date('m/d/Y', strtotime("last sunday"));
            $gettodate = date('m/d/Y', strtotime("saturday"));
            break;
        case "Last Week":
            $getfromdate = date('m/d/Y', strtotime("last sunday -7 day"));
            $gettodate = date('m/d/Y', strtotime("last sunday -1 day"));
            break;
        case "Prev Last Week":
            $getfromdate = date('m/d/Y', strtotime("last sunday -14 day"));
            $gettodate = date('m/d/Y', strtotime("last sunday -8 day"));
            break;
        case "This Month":
            $getfromdate = date('m/01/Y');
            $gettodate = date('m/d/Y');
            break;
        case "Last Month":
            $getfromdate = date('m/d/Y', strtotime(date('Y-m') . " -1 month"));
            $gettodate = date('m/d/Y', strtotime(date('Y-m') . " last day of -1 month"));
            break;
        case "Prev Last Month":
            $getfromdate = date('m/d/Y', strtotime(date('Y-m') . " -2 month"));
            $gettodate = date('m/d/Y', strtotime(date('Y-m') . " last day of -2 month"));
            break;
        case "This Year":
            $getfromdate = date('01/01/Y');
            $gettodate = date('m/d/Y');
            break;
        case "Last Year":
            $current_year = date("Y");
            $getfromdate = date('m/d/Y', strtotime('1-January-' . $current_year . '- 1 year'));
            $gettodate = date('m/d/Y', strtotime('31-December-' . $current_year . '- 1 year'));
            break;
        case "Custom Date":
            if (!empty($from)) {
                $getfromdate = date('m/d/Y', strtotime($from));
            }
            if (!empty($to)) {
                $gettodate = date('m/d/Y', strtotime($to));
            }
            break;
    }

    return array("getfromdate" => $getfromdate, "gettodate" => $gettodate);
}

function getPremiums($agent_id, $is_renewal = '', $filter = array("getfromdate" => "", "gettodate" => "","type"=>""))
{
    global $pdo;
    $sch_params = array();
    $search_incr = "";

    if($filter["type"]!=""){
        $operator = '';
        if($filter["type"] == "exactly"){
            $operator = "=";
        }else if($filter["type"] == "before"){
            $operator = "<";
        }else if($filter["type"] == "after"){
            $operator = ">";
        }else if($filter["type"] == "range"){
            if ($filter["getfromdate"] != "") {
                $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
                $search_incr .= " AND DATE(t.created_at) >= :fcreated_at";
            }
            if ($filter["gettodate"] != "") {
                $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
                $search_incr .= " AND DATE(t.created_at) <= :tcreated_at";
            }
        }
        if(in_array($filter["type"],array('exactly','before','after'))){
            $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
            $search_incr .= " AND DATE(t.created_at) ".$operator." :tcreated_at";
        }
    }
    // pre_print($sch_params,false);
    $incr = " AND c.sponsor_id =:agent_id" . $search_incr;
    $incrArray = array(":agent_id" => $agent_id);

    // if ($is_renewal == 'Y' || $is_renewal == 'N') {
    //     $incr .= " AND o.is_renewal = :is_renewal";
    //     $incrArray[":is_renewal"] = $is_renewal;
    // }
    if ($is_renewal == 'Y') {
        $incr .= " AND t.transaction_type = :is_renewal";
        $incrArray[":is_renewal"] = 'Renewal Order';
    } else if ($is_renewal == 'N') {
        $incr .= " AND t.transaction_type = :is_renewal";
        $incrArray[":is_renewal"] = 'New Order';
    } else {
        $incr .= " AND t.transaction_type IN ('Renewal Order', 'New Order')";
    }

    $getNBCount = $pdo->selectOne("SELECT sum(t.credit) as total_sales
                    FROM transactions as t
                    JOIN customer as c ON (t.customer_id = c.id)
                    WHERE t.id > 0  $incr group by c.sponsor_id", array_merge($incrArray, $sch_params));
    // $getNBCount = $pdo->selectOne("SELECT sum(od.unit_price*od.qty) as total_sales
    //               FROM order_details od
    //               JOIN orders as o ON(o.id=od.order_id)
    //               JOIN customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
    //               WHERE od.product_id NOT IN($fee_products) AND c.type='Customer' AND o.status IN('Payment Approved') $incr group by c.sponsor_id", array_merge($incrArray, $sch_params));
    if (empty($getNBCount["total_sales"])) {
        $getNBCount["total_sales"] = "0.00";
    }
    // return $getNBCount["total_sales"];
    setlocale(LC_MONETARY, 'en_US.UTF-8');
    //return money_format('%!i', $getNBCount["total_sales"]);
    return displayAmount($getNBCount["total_sales"]);
}

function getSalesPercentage($current, $prev)
{
    $total_sales = 0;
    if (isset($current) || isset($prev)) {
        if ($current > $prev) {
            $total_sales = $current;
        } else if ($prev > $current) {
            $total_sales = $prev;
        }
        $sales_diff = $current - $prev;
        if ($sales_diff != 0 && $total_sales != 0 && $total_sales != '') {
            $sales_per = (($sales_diff) / $total_sales) * 100;
        } else {
            $sales_per = 0;
        }
    } else {
        $sales_per = 0;
    }
    return $sales_per;
}

function getCustomer($agent_id, $filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $sch_params = array();
    $search_incr = "";
    if ($filter["getfromdate"] != "") {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $search_incr .= " AND DATE(o.created_at) >= :fcreated_at";
    }
    if ($filter["gettodate"] != "") {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $search_incr .= " AND DATE(o.created_at) <= :tcreated_at";
    }
    // pre_print($sch_params,false);
    $incr = " AND c.sponsor_id =:agent_id" . $search_incr;
    $incrArray = array(":agent_id" => $agent_id);
    $getNBCount = $pdo->selectOne("SELECT count(DISTINCT c.id) as total_customer
                                    FROM order_details od
                    JOIN orders as o ON(o.id=od.order_id)
                    JOIN customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
                    WHERE od.product_id NOT IN($fee_products) AND od.is_deleted='N' AND 1=1 $incr AND c.type='Customer' AND o.status IN('Payment Approved') AND o.is_renewal='N'
                                    GROUP BY c.sponsor_id", array_merge($incrArray, $sch_params));
    if (empty($getNBCount["total_customer"])) {
        $getNBCount["total_customer"] = 0;
    }
    return $getNBCount["total_customer"];
}


function getTermedCustomer($agent_id, $filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;
    $sch_params = array();
    $search_incr = "";
    if ($filter["getfromdate"] != "") {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $search_incr .= " AND DATE(ws.termination_date) >= :fcreated_at";
    }
    if ($filter["gettodate"] != "") {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $search_incr .= " AND DATE(ws.termination_date) <= :tcreated_at";
    }
    // pre_print($sch_params,false);
    $incr = " AND c.sponsor_id =:agent_id" . $search_incr;
    $incrArray = array(":agent_id" => $agent_id);
    $sqlTermed="SELECT count(DISTINCT c.id) as total_customer
                                    FROM website_subscriptions ws
                                    JOIN customer c ON (c.id=ws.customer_id) 
                    WHERE c.type='Customer' $incr AND ws.termination_date IS NOT NULL group by c.sponsor_id";
    $getNBCount = $pdo->selectOne($sqlTermed, array_merge($incrArray, $sch_params));
    if (empty($getNBCount["total_customer"])) {
        $getNBCount["total_customer"] = 0;
    }
    return $getNBCount["total_customer"];
}

function getUsers($agent_id, $type = "Customer", $filter = array("getfromdate" => "", "gettodate" => "","type"=>""))
{
    global $pdo;
    $sch_params = array();
    $search_incr = "";

    if(!empty($filter["type"])){
        $operator = '';
        if($filter["type"] == "exactly"){
            $operator = "=";
        }else if($filter["type"] == "before"){
            $operator = "<";
        }else if($filter["type"] == "after"){
            $operator = ">";
        }else if($filter["type"] == "range"){
            if ($filter["getfromdate"] != "") {
                $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
                $search_incr .= " AND DATE(o.created_at) >= :fcreated_at";
            }
            if ($filter["gettodate"] != "") {
                $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
                $search_incr .= " AND DATE(o.created_at) <= :tcreated_at";
            }
        }
        if(in_array($filter["type"],array('exactly','before','after'))){
            $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
            $search_incr .= " AND DATE(o.created_at) ".$operator." :tcreated_at";
        }
    }
    if($agent_id==''){
        $agent_id = NULL;
    }
    $incr = " AND o.sponsor_id =:agent_id AND o.type=:type" . $search_incr;
    $incrArray = array(":agent_id" => $agent_id, ":type" => $type);
    /*if($type=="Customer"){
        $incr.=" AND status in('Active','On Hold Failed Billing')";
    }else{*/
    $incr .= " AND status in('Active')";
    // }
    $getUsers = $pdo->selectOne("SELECT count(o.id) as total_customer FROM customer o where 1=1 $incr AND o.is_deleted='N'", array_merge($incrArray, $sch_params));
    if (empty($getUsers["total_customer"])) {
        $getUsers["total_customer"] = 0;
    }
    return  displayNumber($getUsers["total_customer"]);
}

function getTopSelling($agent_id, $filter = array("getfromdate" => "", "gettodate" => "","type"=>""))
{
    global $pdo;

    $sch_params = array();
    $search_incr = "";
    if($filter["type"]!=""){
        $operator = '';
        if($filter["type"] == "exactly"){
            $operator = "=";
        }else if($filter["type"] == "before"){
            $operator = "<";
        }else if($filter["type"] == "after"){
            $operator = ">";
        }else if($filter["type"] == "range"){
            if ($filter["getfromdate"] != "") {
                $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
                $search_incr .= " AND DATE(t.created_at) >= :fcreated_at";
            }
            if ($filter["gettodate"] != "") {
                $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
                $search_incr .= " AND DATE(t.created_at) <= :tcreated_at";
            }
        }
        if(in_array($filter["type"],array('exactly','before','after'))){
            $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
            $search_incr .= " AND DATE(t.created_at) ".$operator." :tcreated_at";
        }
    }

    $tincr = " AND c.sponsor_id =:agent_id";
    $incr =  $search_incr;
    $incrArray = array(":agent_id" => $agent_id);

    $getRec = $pdo->select("SELECT p.parent_product_id,p.id as product_id,p.name as product_name,ord.total_premiums as total_sales, (ord.unit_price*ord.total_policies) as total_pre_price,ord.total_policies as total_sold,p.product_code as product_code,ord.NewBusinessMember as new_members
    FROM prd_main p
        LEFT JOIN
        (SELECT tran.product_id as p_id,sum(t.credit) as total_premiums,tran.unit_price as unit_price,count(DISTINCT tran.cust_id) as total_policy_holders,SUM(tran.total_product) AS total_policies, COUNT(distinct (CASE WHEN t.transaction_type = 'New Order' THEN tran.cust_id END)) as NewBusinessMember
                FROM transactions as t
                LEFT JOIN (
                    SELECT o.id, p.id as product_id, c.id as cust_id, count(od.product_id) as total_product, od.unit_price
                    FROM orders as o
                    JOIN order_details od ON (od.order_id = o.id AND od.is_deleted='N')
                    JOIN prd_main p ON(p.id=od.product_id AND p.type!='Fees')
                    JOIN customer c ON (c.id=o.customer_id) 
                    WHERE p.is_deleted='N' AND o.payment_type IN('CC','ACH') $tincr GROUP BY o.id,p.id
                ) as tran ON (tran.id = t.order_id)
            WHERE t.transaction_type IN ('New Order') $incr GROUP BY tran.product_id
        ) as ord ON ord.p_id = p.id
        WHERE p.is_deleted='N' GROUP BY p.id having total_premiums > 0 ORDER BY total_premiums DESC", array_merge($incrArray, $sch_params));
    // pre_print($getRec);
    return $getRec;
}

function getTopOrganizationSelling($agent_id, $filter = array("getfromdate" => "", "gettodate" => "","type"=>""))
{
    global $pdo;    
    $sch_params = array();
    $search_incr = "";
    if($filter["type"]!=""){
        $operator = '';
        if($filter["type"] == "exactly"){
            $operator = "=";
        }else if($filter["type"] == "before"){
            $operator = "<";
        }else if($filter["type"] == "after"){
            $operator = ">";
        }else if($filter["type"] == "range"){
            if ($filter["getfromdate"] != "") {
                $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
                $search_incr .= " AND DATE(t.created_at) >= :fcreated_at";
            }
            if ($filter["gettodate"] != "") {
                $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
                $search_incr .= " AND DATE(t.created_at) <= :tcreated_at";
            }
        }
        if(in_array($filter["type"],array('exactly','before','after'))){
            $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
            $search_incr .= " AND DATE(t.created_at) ".$operator." :tcreated_at";
        }
    }

   
    $tincr = " AND (c.upline_sponsors LIKE :like_agent_id OR c.sponsor_id =:agent_id)";
    $incr = $search_incr;
    $incrArray = array(":like_agent_id" => '%,' . $agent_id . ',%', ":agent_id" => $agent_id);

    $sql = "SELECT p.parent_product_id,p.id as product_id,p.name as product_name,ord.total_premiums as total_sales, (ord.unit_price*ord.total_policies) as total_pre_price,ord.total_policies as total_sold,ord.NewBusinessMember as new_members,p.product_code as product_code
    FROM prd_main p
        LEFT JOIN
        (SELECT tran.product_id as p_id,sum(t.credit) as total_premiums,tran.unit_price as unit_price, count(DISTINCT tran.cust_id) as total_policy_holders,SUM(tran.total_product) AS total_policies, COUNT(distinct (CASE WHEN t.transaction_type = 'New Order' THEN tran.cust_id END)) as NewBusinessMember
                FROM transactions as t
                LEFT JOIN (
                    SELECT o.id, p.id as product_id, c.id as cust_id, count(od.product_id) as total_product, od.unit_price
                    FROM orders as o
                    JOIN order_details od ON (od.order_id = o.id AND od.is_deleted='N')
                    JOIN prd_main p ON(p.id=od.product_id AND p.type!='Fees')
                    JOIN customer c ON (c.id=o.customer_id) 
                    WHERE p.is_deleted='N' AND o.payment_type IN('CC','ACH') $tincr GROUP BY od.id
                ) as tran ON (tran.id = t.order_id)
            WHERE t.transaction_type IN ('New Order') $incr GROUP BY tran.product_id
        ) as ord ON ord.p_id = p.id
        WHERE p.is_deleted='N' GROUP BY p.id having total_premiums > 0 ORDER BY total_premiums DESC";
        $getRec = $pdo->select($sql, array_merge($incrArray, $sch_params));
    // pre_print($getRec);
    return $getRec;
}

function getTopSellingAgentGroups($agent_id, $filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $sch_params = array();
    $search_incr = "";
    if ($filter["getfromdate"] != "") {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $search_incr .= " AND DATE(o.created_at) >= :fcreated_at";
    }
    if ($filter["gettodate"] != "") {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $search_incr .= " AND DATE(o.created_at) <= :tcreated_at";
    }

    /*$incr = " AND c.sponsor_id =:agent_id" . $search_incr;
    $incrArray = array(":agent_id" => $agent_id);*/
    $incr = " AND (c2.upline_sponsors LIKE :like_agent_id OR c.sponsor_id=:agent_id)" . $search_incr;
    $incrArray = array(":like_agent_id" => '%,' . $agent_id . ',%', ":agent_id" => $agent_id);

    /*$sel = "SELECT GROUP_CONCAT(product_id) as pid FROM agent_product_rule WHERE agent_id=:agent_id AND is_deleted='N'";
    $wr = array(":agent_id" => $agent_id);
    $res = $pdo->selectOne($sel, $wr);

    if ($res['pid'] == "") {
        $res['pid'] = '0';
    }

    if ($res['pid'] != "") {
        $product_incr = " AND p.id IN(" . $res['pid'] . ")";
    }*/
    $sql = "SELECT od.product_id,sum(od.unit_price*od.qty) as total_sales,count(DISTINCT c.id) as total_sold,c2.fname,c2.lname,c2.business_name,c2.public_name
                                    FROM order_details od
                    JOIN orders as o ON(o.id=od.order_id)
                    JOIN customer c ON (c.id=o.customer_id AND c.type='Customer')
                    JOIN customer c2 ON(c2.id=c.sponsor_id AND c2.type in ('Agent'))
                    -- LEFT JOIN customer s ON(s.id=c2.sponsor_id)
                    WHERE od.product_id NOT IN($fee_products) AND od.is_deleted='N' AND 1=1 $incr AND o.status IN('Payment Approved') AND o.is_renewal='N'
                                    GROUP BY c2.id having total_sales>0 ORDER BY total_sales DESC";
    $getRec = $pdo->select($sql, array_merge($incrArray, $sch_params));
    return $getRec;
}

function getTopSellingGroups($agent_id, $filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;
    $sch_params = array();
    $search_incr = "";
    // pre_print($sch_params,false);
    if ($filter["getfromdate"] != "") {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $search_incr .= " AND DATE(ws.eligibility_date) >= :fcreated_at";
    }
    if ($filter["gettodate"] != "") {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $search_incr .= " AND DATE(ws.eligibility_date) <= :tcreated_at";
    }

    /*$incr = " AND c.sponsor_id =:agent_id" . $search_incr;
    $incrArray = array(":agent_id" => $agent_id);*/
    $incr = " AND (c2.upline_sponsors LIKE :like_agent_id OR c2.sponsor_id=:agent_id)" . $search_incr;
    $incrArray = array(":like_agent_id" => '%,' . $agent_id . ',%', ":agent_id" => $agent_id);

    /*$sql="SELECT od.product_id,sum(od.unit_price*od.qty) as total_sales,count(DISTINCT c.id) as total_sold,c2.fname,c2.lname,c2.business_name,c2.public_name
                                    FROM order_details od
                    JOIN orders as o ON(o.id=od.order_id)
                    JOIN customer c ON (c.id=o.customer_id AND c.type='Customer')
                    JOIN customer c2 ON(c2.id=c.sponsor_id AND c2.type in ('Agent'))
                    WHERE 1=1 $incr AND o.status IN('Payment Approved') AND o.is_renewal='N'
                                    GROUP BY c2.id having total_sales>0 ORDER BY total_sales DESC";*/
    $sql = "SELECT ws.product_id,sum(ws.price) as total_sales,
          count(DISTINCT c.id) as total_sold,c2.fname,c2.lname,c2.business_name,c2.public_name
          FROM website_subscriptions ws
          JOIN customer c ON (c.id=ws.customer_id AND c.type='Customer')
          JOIN customer c2 ON(c2.id=c.sponsor_id AND c2.type in ('Group'))
          WHERE c.status IN('Active','Pending') $incr
          GROUP BY c2.id having total_sales>0 ORDER BY total_sales DESC";

    $getRec = $pdo->select($sql, array_merge($incrArray, $sch_params));


    return $getRec;
}

function getOrganizationPremiums($agent_id, $is_renewal = '', $filter = array("getfromdate" => "", "gettodate" => "","type"=>""))
{
    global $pdo;
    $sch_params = array();
    $search_incr = "";

    if($filter["type"]!=""){
        $operator = '';
        if($filter["type"] == "exactly"){
            $operator = "=";
        }else if($filter["type"] == "before"){
            $operator = "<";
        }else if($filter["type"] == "after"){
            $operator = ">";
        }else if($filter["type"] == "range"){
            if ($filter["getfromdate"] != "") {
                $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
                $search_incr .= " AND DATE(t.created_at) >= :fcreated_at";
            }
            if ($filter["gettodate"] != "") {
                $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
                $search_incr .= " AND DATE(t.created_at) <= :tcreated_at";
            }
        }
        if(in_array($filter["type"],array('exactly','before','after'))){
            $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
            $search_incr .= " AND DATE(t.created_at) ".$operator." :tcreated_at";
        }
    }
    // pre_print($sch_params,false);
    // $incr = " AND (c.upline_sponsors LIKE :like_agent_id OR c.sponsor_id=:agent_id)" . $search_incr;
    // $incrArray = array(":like_agent_id" => '%,' . $agent_id . ',%', ":agent_id" => $agent_id);
    $incr = " AND (c.upline_sponsors LIKE :like_agent_id OR c.sponsor_id=:agent_id)" . $search_incr;
    $incrArray = array(":like_agent_id" => '%,' . $agent_id . ',%', ":agent_id" => $agent_id);

    // if ($is_renewal == 'Y' || $is_renewal == 'N') {
    //     $incr .= " AND o.is_renewal = :is_renewal";
    //     $incrArray[":is_renewal"] = $is_renewal;
    // }
    if ($is_renewal == 'Y') {
        $incr .= " AND t.transaction_type = :is_renewal";
        $incrArray[":is_renewal"] = 'Renewal Order';
    } else if ($is_renewal == 'N') {
        $incr .= " AND t.transaction_type = :is_renewal";
        $incrArray[":is_renewal"] = 'New Order';
    } else {
        $incr .= " AND t.transaction_type IN ('Renewal Order', 'New Order')";
    }

    // pre_print(array_merge($incrArray, $sch_params),false);
    // $sql = "SELECT sum(od.unit_price*od.qty) as total_sales
    //               FROM order_details od
    //               JOIN orders as o ON(o.id=od.order_id)
    //               JOIN customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
    //               WHERE od.product_id NOT IN($fee_products) AND c.type='Customer' AND o.status IN('Payment Approved') $incr";
    $sql = "SELECT sum(t.credit) as total_sales
                  FROM transactions t
                  JOIN customer c ON (c.id=t.customer_id)
                  WHERE t.id > 0 $incr";

    $getNBCount = $pdo->selectOne($sql, array_merge($incrArray, $sch_params));
    if (empty($getNBCount["total_sales"])) {
        $getNBCount["total_sales"] = "0.00";
    }
    // return $getNBCount["total_sales"];
    setlocale(LC_MONETARY, 'en_US.UTF-8');
    //return money_format('%!i', $getNBCount["total_sales"]);
    return displayAmount($getNBCount["total_sales"]);
}

function getOrganizationCustomer($agent_id, $filter = array("getfromdate" => "", "gettodate" => "","type"=>""))
{
    global $pdo;
    // $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $sch_params = array();
    $search_incr = "";

    if($filter["type"]!=""){
        $operator = '';
        if($filter["type"] == "exactly"){
            $operator = "=";
        }else if($filter["type"] == "before"){
            $operator = "<";
        }else if($filter["type"] == "after"){
            $operator = ">";
        }else if($filter["type"] == "range"){
            if ($filter["getfromdate"] != "") {
                $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
                $search_incr .= " AND DATE(t.created_at) >= :fcreated_at";
            }
            if ($filter["gettodate"] != "") {
                $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
                $search_incr .= " AND DATE(t.created_at) <= :tcreated_at";
            }
        }
        if(in_array($filter["type"],array('exactly','before','after'))){
            $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
            $search_incr .= " AND DATE(t.created_at) ".$operator." :tcreated_at";
        }
    }
    // pre_print($sch_params,false);
    // $incr = " AND (c.upline_sponsors LIKE :like_agent_id OR c.sponsor_id=:agent_id)" . $search_incr;
    // $incrArray = array(":like_agent_id" => '%,' . $agent_id . ',%', ":agent_id" => $agent_id);
    $incr = " AND (c.upline_sponsors LIKE :like_agent_id OR c.sponsor_id=:agent_id)" . $search_incr;
    $incrArray = array(":like_agent_id" => '%,' . $agent_id . ',%', ":agent_id" => $agent_id);

    // $getNBCount = $pdo->selectOne("SELECT count(DISTINCT c.id) as total_customer
                //                  FROM order_details od
    //                 JOIN orders as o ON(o.id=od.order_id)
    //                 JOIN customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
    //                 WHERE od.product_id NOT IN($fee_products) AND 1=1 $incr AND c.type='Customer' AND o.status IN('Payment Approved') AND o.is_renewal='N'", array_merge($incrArray, $sch_params));

     $getNBCount = $pdo->selectOne("SELECT count(DISTINCT c.id) as total_customer
                                    FROM transactions t
                                    JOIN customer c ON (c.id=t.customer_id)
                                    WHERE 1=1 $incr AND c.type='Customer' AND t.transaction_type IN ('New Order')", array_merge($incrArray, $sch_params));
     
    if (empty($getNBCount["total_customer"])) {
        $getNBCount["total_customer"] = 0;
    }
    return displayNumber($getNBCount["total_customer"]);
}

function getOrganizationTermedCustomer($agent_id, $filter = array("getfromdate" => "", "gettodate" => ""))
{
//print_r($agent_id);
    global $pdo;
    $sch_params = array();
    $search_incr = "";
    if ($filter["getfromdate"] != "") {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $search_incr .= " AND DATE(ws.termination_date) >= :fcreated_at";
    }
    if ($filter["gettodate"] != "") {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $search_incr .= " AND DATE(ws.termination_date) <= :tcreated_at";
    }
        //pre_print($sch_params,false);
    $incr = " AND (c.upline_sponsors LIKE :like_agent_id OR c.sponsor_id=:agent_id)" . $search_incr;
    $incrArray = array(":like_agent_id" => '%,' . $agent_id . ',%', ":agent_id" => $agent_id);
    $sqlTermed="SELECT count(DISTINCT c.id) as total_customer
                                    FROM website_subscriptions ws
                                    JOIN customer c ON (c.id=ws.customer_id) 
                    WHERE c.type='Customer' $incr AND ws.termination_date IS NOT NULL group by c.sponsor_id";
    $getNBCount = $pdo->selectOne($sqlTermed, array_merge($incrArray, $sch_params));
    if (empty($getNBCount["total_customer"])) {
        $getNBCount["total_customer"] = 0;
    } 
    return $getNBCount["total_customer"];
}

function getOrganizationUsers($agent_id, $type = "Customer", $filter = array("getfromdate" => "", "gettodate" => "","type"=>""))
{
    global $pdo;
    $sch_params = array();
    $search_incr = "";

    if($filter["type"]!=""){
        $operator = '';
        if($filter["type"] == "exactly"){
            $operator = "=";
        }else if($filter["type"] == "before"){
            $operator = "<";
        }else if($filter["type"] == "after"){
            $operator = ">";
        }else if($filter["type"] == "range"){
            if ($filter["getfromdate"] != "") {
                $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
                $search_incr .= " AND DATE(c.created_at) >= :fcreated_at";
            }
            if ($filter["gettodate"] != "") {
                $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
                $search_incr .= " AND DATE(c.created_at) <= :tcreated_at";
            }
        }
        if(in_array($filter["type"],array('exactly','before','after'))){
            $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
            $search_incr .= " AND DATE(c.created_at) ".$operator." :tcreated_at";
        }
    }
    // pre_print($sch_params,false);
    $incr = " AND (c.upline_sponsors LIKE :like_agent_id) AND c.type=:type AND status in('Active')" . $search_incr;
    $incrArray = array(":like_agent_id" => '%,' . $agent_id . ',%', ":type" => $type);
    // }
    $sql = "SELECT count(c.id) as total_customer FROM customer c where 1=1 $incr";
    // pre_print($incrArray);
    $getUsers = $pdo->selectOne($sql, array_merge($incrArray, $sch_params));
    if (empty($getUsers["total_customer"])) {
        $getUsers["total_customer"] = 0;
    }
    return displayNumber($getUsers["total_customer"]);
}

function getPremiumsApprovedCCACH($is_renewal = 'N', $filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $sch_params = array();
    $incr = "";
    if ($filter["getfromdate"] != "") {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $incr .= " AND DATE(o.created_at) >= :fcreated_at";
    }
    if ($filter["gettodate"] != "") {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $incr .= " AND DATE(o.created_at) <= :tcreated_at";
    }

    if ($is_renewal == 'Y') {
        $incr .= " AND o.is_renewal = 'Y'";
    } else {
        $incr .= " AND o.is_renewal = 'N'";
    }

    // pre_print(array_merge($incrArray, $sch_params),false);
    $sql = "SELECT sum(od.unit_price*od.qty) as total_sales
                  FROM order_details od
                  JOIN orders as o ON(o.id=od.order_id)
                  JOIN od.product_id NOT IN($fee_products) AND customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
                  WHERE
                  c.type='Customer' AND
                  o.payment_type IN('CC','ACH') AND
                  o.status IN('Payment Approved') AND od.is_deleted='N' $incr";

    $getNBCount = $pdo->selectOne($sql, $sch_params);
    if ($getNBCount["total_sales"] == "") {
        $getNBCount["total_sales"] = "0.00";
    }
    setlocale(LC_MONETARY, 'en_US.UTF-8');
    //return money_format('%!i', $getNBCount["total_sales"]);
    return $getNBCount["total_sales"];
}

function getPremiumsDeclinedCCACH($is_renewal = 'N', $filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $sch_params = array();
    $incr = "";
    if ($filter["getfromdate"] != "") {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $incr .= " AND DATE(o.created_at) >= :fcreated_at";
    }
    if ($filter["gettodate"] != "") {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $incr .= " AND DATE(o.created_at) <= :tcreated_at";
    }

    if ($is_renewal == 'Y') {
        $incr .= " AND o.is_renewal = 'Y'";
    } else {
        $incr .= " AND o.is_renewal = 'N'";
    }

    // pre_print(array_merge($incrArray, $sch_params),false);
    $sql = "SELECT sum(od.unit_price*od.qty) as total_sales
                  FROM order_details od
                  JOIN orders as o ON(o.id=od.order_id)
                  JOIN od.product_id NOT IN($fee_products) AND customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
                  WHERE
                  c.type='Customer' AND
                  o.payment_type IN('CC','ACH') AND
                  o.status IN('Payment Declined') AND od.is_deleted='N' $incr";

    $getNBCount = $pdo->selectOne($sql, $sch_params);
    if ($getNBCount["total_sales"] == "") {
        $getNBCount["total_sales"] = "0.00";
    }
    // return $getNBCount["total_sales"];
    setlocale(LC_MONETARY, 'en_US.UTF-8');
    //return money_format('%!i', $getNBCount["total_sales"]);
    return $getNBCount["total_sales"];
}

function getPremiumsApprovedListBill($is_renewal = 'N', $filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;
    return "0.00";
}

function getPremiumsPostDated($is_renewal = 'N', $filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;
    return "0.00";
}


function getPremiumsTotal($is_renewal = 'N', $filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $sch_params = array();
    $incr = "";
    if ($filter["getfromdate"] != "") {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $incr .= " AND DATE(o.created_at) >= :fcreated_at";
    }
    if ($filter["gettodate"] != "") {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $incr .= " AND DATE(o.created_at) <= :tcreated_at";
    }

    if ($is_renewal == 'Y') {
        $incr .= " AND o.is_renewal = 'Y'";
    } elseif ($is_renewal == 'N') {
        $incr .= " AND o.is_renewal = 'N'";
    }

    // pre_print(array_merge($incrArray, $sch_params),false);
    $sql = "SELECT sum(od.unit_price*od.qty) as total_sales
                  FROM order_details od
                  JOIN orders as o ON(o.id=od.order_id)
                  JOIN od.product_id NOT IN($fee_products) AND customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
                  WHERE
                  c.type='Customer' AND
                  o.status IN('Payment Approved') AND od.is_deleted='N' $incr";

    $getNBCount = $pdo->selectOne($sql, $sch_params);
    if ($getNBCount["total_sales"] == "") {
        $getNBCount["total_sales"] = "0.00";
    }
    // return $getNBCount["total_sales"];
    setlocale(LC_MONETARY, 'en_US.UTF-8');
    //return money_format('%!i', $getNBCount["total_sales"]);
    return $getNBCount["total_sales"];
}

function getPrimaryPolicyHolders($is_renewal = 'N', $filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $sch_params = array();
    $incr = "";
    if ($filter["getfromdate"] != "") {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $incr .= " AND DATE(o.created_at) >= :fcreated_at";
    }
    if ($filter["gettodate"] != "") {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $incr .= " AND DATE(o.created_at) <= :tcreated_at";
    }

    if ($is_renewal == 'Y') {
        $incr .= " AND o.is_renewal = 'Y'";
    } else {
        $incr .= " AND o.is_renewal = 'N'";
    }

    $getNBCount = $pdo->selectOne("SELECT count(DISTINCT c.id) as total_customer
                                    FROM order_details od
                    JOIN orders as o ON(o.id=od.order_id)
                    JOIN customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
                    WHERE od.product_id NOT IN($fee_products) AND od.is_deleted='N' AND 1=1 $incr AND c.type='Customer' AND o.status IN('Payment Approved')", $sch_params);
    if (empty($getNBCount["total_customer"])) {
        $getNBCount["total_customer"] = 0;
    }
    return $getNBCount["total_customer"];
}

function getApprovedPolicies($is_renewal = 'N',$filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $sch_params = array();
    $search_incr = "";
    if ($filter["getfromdate"] != "") {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $search_incr .= " AND DATE(o.created_at) >= :fcreated_at";
    }
    if ($filter["gettodate"] != "") {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $search_incr .= " AND DATE(o.created_at) <= :tcreated_at";
    }

    if ($is_renewal == 'Y') {
        $search_incr .= " AND o.is_renewal = 'Y'";
    } else {
        $search_incr .= " AND o.is_renewal = 'N'";
    }

    $getNBCount = $pdo->selectOne("SELECT SUM(IF(od.product_type = 'KIT',(SELECT COUNT(okd.id) FROM order_kit_detail okd WHERE okd.detail_id = od.id),1)) as total_policies
                                    FROM order_details od
                    JOIN orders as o ON(o.id=od.order_id)
                    JOIN customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
                    WHERE od.product_id NOT IN($fee_products) AND od.is_deleted='N' AND 1=1 $search_incr AND c.type='Customer' AND o.status IN('Payment Approved')", $sch_params);
    if (empty($getNBCount["total_policies"])) {
        $getNBCount["total_policies"] = 0;
    }
    return $getNBCount["total_policies"];
}

function getPoliciesTotal($is_renewal = 'N',$filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $sch_params = array();
    $search_incr = "";
    if ($filter["getfromdate"] != "") {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $search_incr .= " AND DATE(o.created_at) >= :fcreated_at";
    }
    if ($filter["gettodate"] != "") {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $search_incr .= " AND DATE(o.created_at) <= :tcreated_at";
    }

    if ($is_renewal == 'Y') {
        $search_incr .= " AND o.is_renewal = 'Y'";
    } else {
        $search_incr .= " AND o.is_renewal = 'N'";
    }

    /*$getNBCount = $pdo->selectOne("SELECT count(DISTINCT od.id) as total_policies
                                    FROM order_details od
                    JOIN orders as o ON(o.id=od.order_id)
                    JOIN customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
                    WHERE 1=1 $search_incr AND c.type='Customer' AND o.status IN('Payment Approved')", $sch_params);*/

    $getNBCount = $pdo->selectOne("SELECT SUM(IF(od.product_type = 'KIT',(SELECT COUNT(okd.id) FROM order_kit_detail okd WHERE okd.detail_id = od.id),1)) as total_policies
                                    FROM order_details od
                    JOIN orders as o ON(o.id=od.order_id)
                    JOIN customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
                    WHERE od.product_id NOT IN($fee_products) AND od.is_deleted='N' AND 1=1 $search_incr AND c.type='Customer' AND o.status IN('Payment Approved')", $sch_params);
    if (empty($getNBCount["total_policies"])) {
        $getNBCount["total_policies"] = 0;
    }
    return $getNBCount["total_policies"];
}

function getPostDatedPolicies($is_renewal = 'N',$filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;
    return 0;
}

function getProductsSalesSummaryReport($is_renewal = "N", $filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $sch_params = array();
    $search_incr = "";

    if ($is_renewal == "Y") {
        $search_incr .= " AND o.is_renewal='Y'";
    } else {
        $search_incr .= " AND o.is_renewal='N'";
    }

    if ($filter["getfromdate"] != "") {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $search_incr .= " AND DATE(o.created_at) >= :fcreated_at";
    }
    if ($filter["gettodate"] != "") {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $search_incr .= " AND DATE(o.created_at) <= :tcreated_at";
    }

    $getRec = $pdo->select("SELECT p.parent_product_id,p.id as product_id,p.name as product_name,ord.total_sales,ord.total_sold,p.product_code as product_code
                            FROM prd_main p
                            LEFT JOIN
                                (SELECT od.product_id,sum(od.unit_price*od.qty) as total_sales,count(DISTINCT od.id) as total_sold
                                    FROM order_details od
                                    JOIN orders as o ON(o.id=od.order_id)
                                    JOIN customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
                                    WHERE od.product_id NOT IN($fee_products) AND 1=1 $search_incr AND c.type='Customer' AND o.status IN('Payment Approved') AND od.is_deleted='N'
                                    GROUP BY od.product_id
                                )   as ord ON ord.product_id=p.id
                            WHERE p.is_deleted='N' GROUP BY p.id having total_sales>0 ORDER BY total_sales DESC", $sch_params);
    return $getRec;
}


function getAgentsSalesSummaryReport($is_renewal = "N", $filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $sch_params = array();
    $search_incr = "";

    if ($is_renewal == "Y") {
        $search_incr .= " AND o.is_renewal='Y'";
    } else {
        $search_incr .= " AND o.is_renewal='N'";
    }

    if ($filter["getfromdate"] != "") {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $search_incr .= " AND DATE(o.created_at) >= :fcreated_at";
    }
    if ($filter["gettodate"] != "") {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $search_incr .= " AND DATE(o.created_at) <= :tcreated_at";
    }

    $sql = "SELECT od.product_id,sum(od.unit_price*od.qty) as total_sales,count(DISTINCT od.id) as total_sold,
              IF(c2.business_name = '' OR c2.business_name IS NULL,CONCAT(c2.fname,' ',c2.lname),c2.business_name)as agent_name,c2.rep_id as agent_display_id
              FROM order_details od
              JOIN orders as o ON(o.id=od.order_id)
              JOIN customer c ON (c.id=o.customer_id AND c.type='Customer')
              JOIN customer c2 ON(c2.id=c.sponsor_id )
              WHERE od.product_id NOT IN($fee_products) AND od.is_deleted='N' AND 1=1 $search_incr AND o.status IN('Payment Approved')
              GROUP BY c2.id having total_sales>0 ORDER BY total_sales DESC";
    $getRec = $pdo->select($sql, $sch_params);

    /*$getRec1 = $pdo->select("SELECT a.id as agent_id,CONCAT(a.fname,' ',a.lname)as agent_name,ord.total_sales,ord.total_sold,a.rep_id as agent_display_id
                            FROM customer a
                            LEFT JOIN
                                (SELECT c.sponsor_id,sum(od.unit_price*od.qty) as total_sales,count(DISTINCT c.id) as total_sold
                                    FROM order_details od
                                    JOIN orders as o ON(o.id=od.order_id)
                                    JOIN customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
                                    WHERE 1=1 $search_incr AND c.type='Customer' AND o.status IN('Payment Approved') AND o.is_renewal='N'
                                    GROUP BY c.sponsor_id
                                ) as ord ON ord.sponsor_id=a.id
                            WHERE a.is_deleted='N' GROUP BY a.id having total_sales > 0 ORDER BY total_sales DESC", $sch_params);*/
    return $getRec;
}

function getSalesCommissionsSummaryReport($is_renewal = "N", $filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;
    $sch_params = array();
    $incr = "";
    if ($filter["getfromdate"] != "") {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $incr .= " AND DATE(cm.created_at) >= :fcreated_at";
    }
    if ($filter["gettodate"] != "") {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $incr .= " AND DATE(cm.created_at) <= :tcreated_at";
    }

    if ($is_renewal == "Y") {
        $incr .= " AND cm.sub_type in ('Renewals')";
    } else {
        $incr .= " AND cm.sub_type in ('New','Advance')";
    }

    $sql = "SELECT SUM(total_payment) as total_payment, SUM(new_business_payment) as new_business_payment, SUM(advance_payment) as advance_payment,SUM(renewals_payment) as renewals_payment FROM
    (SELECT
     SUM(IF(cm.sub_type='New',cm.amount,0)) AS new_business_payment,
     SUM(IF(cm.sub_type='Advance',cm.amount,0)) AS advance_payment,
     SUM(IF(cm.sub_type='Renewals',cm.amount,0)) AS renewals_payment,
     SUM(cm.amount) as total_payment
    FROM
     commission cm
    WHERE cm.is_deleted='n' $incr
    UNION
    SELECT
     SUM(IF(cm.sub_type='New',cm.amount,0)) AS new_business_payment,
     SUM(IF(cm.sub_type='Advance',cm.amount,0)) AS advance_payment,
     SUM(IF(cm.sub_type='Renewals',cm.amount,0)) AS renewals_payment,
     SUM(cm.amount) as total_payment
    FROM
     commission_monthly cm
    WHERE cm.is_deleted='n' $incr) t";
    // echo $sql;
    return $pdo->selectOne($sql, $sch_params);
}

function getLevelCommissions($is_renewal = "N", $filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;

    $sch_params = array();
    $incr = "";
    if ($filter["getfromdate"] != "") {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $incr .= " AND DATE(cm.created_at) >= :fcreated_at";
    }
    if ($filter["gettodate"] != "") {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $incr .= " AND DATE(cm.created_at) <= :tcreated_at";
    }

    if ($is_renewal == "Y") {
        $incr .= " AND cm.sub_type in ('Renewals')";
    } elseif ($is_renewal == "N") {
        $incr .= " AND cm.sub_type in ('New')";
    } else {
        $incr .= " AND cm.sub_type in ('Renewals','New')";
    }

    $sql = "SELECT SUM(total_payment) as total_payment, SUM(new_business_payment) as new_business_payment, SUM(advance_payment) as advance_payment,SUM(renewals_payment) as renewals_payment FROM
    (SELECT
     SUM(IF(cm.sub_type='New',cm.amount,0)) AS new_business_payment,
     SUM(IF(cm.sub_type='Advance',cm.amount,0)) AS advance_payment,
     SUM(IF(cm.sub_type='Renewals',cm.amount,0)) AS renewals_payment,
     SUM(cm.amount) as total_payment
    FROM
     commission cm
    WHERE cm.is_deleted='n' $incr
    UNION
    SELECT
     SUM(IF(cm.sub_type='New',cm.amount,0)) AS new_business_payment,
     SUM(IF(cm.sub_type='Advance',cm.amount,0)) AS advance_payment,
     SUM(IF(cm.sub_type='Renewals',cm.amount,0)) AS renewals_payment,
     SUM(cm.amount) as total_payment
    FROM
     commission_monthly cm
    WHERE cm.is_deleted='n' $incr) t";
    // echo $sql;
    $CommissionRow = $pdo->selectOne($sql, $sch_params);
    setlocale(LC_MONETARY, 'en_US.UTF-8');
    //return money_format('%!i', $CommissionRow['total_payment']);
    return $CommissionRow['total_payment'];
}

function getAdvanceCommissions($is_renewal = "N", $filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;
    $sch_params = array();
    $incr = "";
    if ($filter["getfromdate"] != "") {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $incr .= " AND DATE(cm.created_at) >= :fcreated_at";
    }
    if ($filter["gettodate"] != "") {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $incr .= " AND DATE(cm.created_at) <= :tcreated_at";
    }

    $incr .= " AND cm.sub_type in ('Advance')";

    $sql = "SELECT SUM(total_payment) as total_payment, SUM(new_business_payment) as new_business_payment, SUM(advance_payment) as advance_payment,SUM(renewals_payment) as renewals_payment FROM
    (SELECT
     SUM(IF(cm.sub_type='New',cm.amount,0)) AS new_business_payment,
     SUM(IF(cm.sub_type='Advance',cm.amount,0)) AS advance_payment,
     SUM(IF(cm.sub_type='Renewals',cm.amount,0)) AS renewals_payment,
     SUM(cm.amount) as total_payment
    FROM
     commission cm
    WHERE cm.is_deleted='n' $incr
    UNION
    SELECT
     SUM(IF(cm.sub_type='New',cm.amount,0)) AS new_business_payment,
     SUM(IF(cm.sub_type='Advance',cm.amount,0)) AS advance_payment,
     SUM(IF(cm.sub_type='Renewals',cm.amount,0)) AS renewals_payment,
     SUM(cm.amount) as total_payment
    FROM
     commission_monthly cm
    WHERE cm.is_deleted='n' $incr) t";
    // echo $sql;
    $CommissionRow = $pdo->selectOne($sql, $sch_params);
    setlocale(LC_MONETARY, 'en_US.UTF-8');
    //return money_format('%!i', $CommissionRow['total_payment']);
    return $CommissionRow['total_payment'];
}

function getSalesCommissionsSummaryReportAgentWise($filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;
    $sch_params = array();
    $incr = "";
    if ($filter["getfromdate"] != "") {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $incr .= " AND DATE(cm.created_at) >= :fcreated_at";
    }
    if ($filter["gettodate"] != "") {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $incr .= " AND DATE(cm.created_at) <= :tcreated_at";
    }

    $sql = "SELECT * FROM (SELECT
     SUM(IF(cm.sub_type='New',cm.amount,0)) AS new_business_payment,
     SUM(IF(cm.sub_type='Advance',cm.amount,0)) AS advance_payment,
     SUM(cm.amount) as total_payment,
     c.id as agent_id,
     CONCAT_WS(' ',c.fname,c.lname) as full_name,
     c.business_name,
     c.public_name
    FROM
     commission cm
    JOIN customer c ON(cm.customer_id=c.id)
    WHERE cm.is_deleted='n' AND cm.sub_type in ('New','Advance') $incr
    UNION
    SELECT
     SUM(IF(cm.sub_type='New',cm.amount,0)) AS new_business_payment,
     SUM(IF(cm.sub_type='Advance',cm.amount,0)) AS advance_payment,
     SUM(cm.amount) as total_payment,
     c.id as agent_id,
     CONCAT_WS(' ',c.fname,c.lname) as full_name,
     c.business_name,
     c.public_name
    FROM
     commission_monthly cm
    JOIN customer c ON(cm.customer_id=c.id)
    WHERE cm.is_deleted='n' AND cm.sub_type in ('New','Advance') $incr)t GROUP BY t.agent_id";
    // echo $sql;
    return $pdo->select($sql, $sch_params);
}

function getRefundedPremiums($is_renewal = '', $filter = array("getfromdate" => "", "gettodate" => "","type"=>""),$extra = array("type"=>'',"agent_id"=>'','void'=>'false'))
{
    global $pdo;
    $sch_params = array();
    $incr = "";

    if(!empty($extra['type']) && $extra['type'] == 'Organization'){
        $incr .= " AND (c.upline_sponsors LIKE :like_agent_id OR c.sponsor_id=:agent_id)";
        $sch_params [':like_agent_id'] = '%,' . $extra['agent_id'] . ',%';
        $sch_params[":agent_id"] = $extra['agent_id'];
    }else if(!empty($extra['agent_id'])){
        $incr .= " AND c.sponsor_id=:agent_id ";
        $sch_params[":agent_id"] = $extra['agent_id'];
    }
    
    if(!empty($filter["type"])){
        $operator = '';
        if($filter["type"] == "exactly"){
            $operator = "=";
        }else if($filter["type"] == "before"){
            $operator = "<";
        }else if($filter["type"] == "after"){
            $operator = ">";
        }else if($filter["type"] == "range"){
            if ($filter["getfromdate"] != "") {
                $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
                $incr .= " AND DATE(o.created_at) >= :fcreated_at";
            }
            if ($filter["gettodate"] != "") {
                $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
                $incr .= " AND DATE(o.created_at) <= :tcreated_at";
            }
        }
        if(in_array($filter["type"],array('exactly','before','after'))){
            $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
            $incr .= " AND DATE(o.created_at) ".$operator." :tcreated_at";
        }
    }

    if ($is_renewal == 'Y') {
        $incr .= " AND o.is_renewal = 'Y'";
    } else if ($is_renewal == 'N') {
        $incr .= " AND o.is_renewal = 'N'";
    }

    $getStatus[] = "'Refund'";
    if(!empty($extra['void']) && $extra['void'] == 'true'){
        $getStatus[] = "'Void'";
    }
    $status = implode(',',$getStatus);
    $sql = "SELECT SUM(ro.refund_amount) as total_sales
                  FROM orders as o
                  JOIN return_orders ro ON(ro.order_id=o.id)
                  JOIN customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
                  WHERE
                  c.type='Customer' AND o.payment_type IN('CC','ACH') AND
                  o.status IN($status) $incr";
    $getNBCount = $pdo->selectOne($sql, $sch_params);

    if ($getNBCount["total_sales"] == "") {
        $getNBCount["total_sales"] = "0.00";
    }
    setlocale(LC_MONETARY, 'en_US.UTF-8');
    return displayAmount($getNBCount["total_sales"]);
}

function getChargebackPremiums($is_renewal = '', $filter = array("getfromdate" => "", "gettodate" => "","type"=>""),$extra = array("type"=>'',"agent_id"=>''))
{
    global $pdo;
    $sch_params = array();
    $incr = "";

    if(!empty($extra['type']) && $extra['type'] == 'Organization'){
        $incr .= " AND (c.upline_sponsors LIKE :like_agent_id OR c.sponsor_id=:agent_id)";
        $sch_params [':like_agent_id'] = '%,' . $extra['agent_id'] . ',%';
        $sch_params[":agent_id"] = $extra['agent_id'];
    }else if(!empty($extra['agent_id'])){
        $incr .= " AND c.sponsor_id=:agent_id ";
        $sch_params[":agent_id"] = $extra['agent_id'];
    }
    if(!empty($filter["type"])){
        $operator = '';
        if($filter["type"] == "exactly"){
            $operator = "=";
        }else if($filter["type"] == "before"){
            $operator = "<";
        }else if($filter["type"] == "after"){
            $operator = ">";
        }else if($filter["type"] == "range"){
            if ($filter["getfromdate"] != "") {
                $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
                $incr .= " AND DATE(o.created_at) >= :fcreated_at";
            }
            if ($filter["gettodate"] != "") {
                $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
                $incr .= " AND DATE(o.created_at) <= :tcreated_at";
            }
        }
        if(in_array($filter["type"],array('exactly','before','after'))){
            $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
            $incr .= " AND DATE(o.created_at) ".$operator." :tcreated_at";
        }
    }

    if ($is_renewal == 'Y') {
        $incr .= " AND o.is_renewal = 'Y'";
    } elseif ($is_renewal == 'N') {
        $incr .= " AND o.is_renewal = 'N'";
    }else{
        $incr.='';
    }

    // pre_print(array_merge($incrArray, $sch_params),false);
    $sql = "SELECT SUM(ro.refund_amount) as total_sales
                  FROM orders as o
                  JOIN return_orders ro ON(ro.order_id=o.id)
                  JOIN customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
                  WHERE
                  c.type='Customer' AND
                  o.payment_type IN('CC','ACH') AND
                  o.status IN('Chargeback') $incr";

    $getNBCount = $pdo->selectOne($sql, $sch_params);
    if ($getNBCount["total_sales"] == "") {
        $getNBCount["total_sales"] = "0.00";
    }
    setlocale(LC_MONETARY, 'en_US.UTF-8');
    return displayAmount($getNBCount["total_sales"]);
}

function getNewSubscribers($filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;
    $sch_params = array();
    $incr = "";
    if ($filter["getfromdate"] != "") {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $incr .= " AND DATE(o.created_at) >= :fcreated_at";
    }
    if ($filter["gettodate"] != "") {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $incr .= " AND DATE(o.created_at) <= :tcreated_at";
    }

    $incr .= " AND o.is_renewal = 'N'";

    $getNBCount = $pdo->selectOne("SELECT count(DISTINCT c.id) as total_customer
                    FROM orders as o
                    JOIN customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
                    WHERE 1=1 $incr AND c.type='Customer' AND c.status IN('Active') AND o.status IN('Payment Approved')", $sch_params);
    if (empty($getNBCount["total_customer"])) {
        $getNBCount["total_customer"] = 0;
    }
    return $getNBCount["total_customer"];
}

function getTotalSubscribers($filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;
    $sch_params = array();
    $incr = "";
    /*if ($filter["getfromdate"] != "") {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $incr .= " AND DATE(c.created_at) >= :fcreated_at";
    }*/
    if ($filter["gettodate"] != "") {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $incr .= " AND DATE(c.created_at) <= :tcreated_at";
    }

    $getNBCount = $pdo->selectOne("SELECT count(DISTINCT c.id) as total_customer
                                    FROM orders as o
                                    JOIN customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
                    WHERE 1=1 $incr AND c.type='Customer' AND c.status IN('Active') AND o.status IN('Payment Approved')", $sch_params);
    if (empty($getNBCount["total_customer"])) {
        $getNBCount["total_customer"] = 0;
    }
    return $getNBCount["total_customer"];
}

function getNewPoliciesWritten($filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $sch_params = array();
    $search_incr = "";
    if ($filter["getfromdate"] != "") {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $search_incr .= " AND DATE(o.created_at) >= :fcreated_at";
    }
    if ($filter["gettodate"] != "") {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $search_incr .= " AND DATE(o.created_at) <= :tcreated_at";
    }

    $search_incr .= " AND o.is_renewal = 'N'";

    $getNBCount = $pdo->selectOne("SELECT SUM(IF(od.product_type = 'KIT',(SELECT COUNT(okd.id) FROM order_kit_detail okd WHERE okd.detail_id = od.id),1)) as total_policies
                                    FROM order_details od
                    JOIN orders as o ON(o.id=od.order_id)
                    JOIN customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
                    WHERE od.product_id NOT IN($fee_products) AND od.is_deleted='N' AND 1=1 $search_incr AND c.type='Customer' AND o.status IN('Payment Approved')", $sch_params);
    if (empty($getNBCount["total_policies"])) {
        $getNBCount["total_policies"] = 0;
    }
    return $getNBCount["total_policies"];
}

function getTotalActivePoliciesWritten($filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $sch_params = array();
    $search_incr = "";
    /*if ($filter["getfromdate"] != "") {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $search_incr .= " AND DATE(o.created_at) >= :fcreated_at";
    }*/

    if ($filter["gettodate"] != "") {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $search_incr .= " AND DATE(o.created_at) <= :tcreated_at";
    }


    $getNBCount = $pdo->selectOne("SELECT SUM(IF(od.product_type = 'KIT',(SELECT COUNT(okd.id) FROM order_kit_detail okd WHERE okd.detail_id = od.id),1)) as total_policies
                    FROM order_details od
                    JOIN orders as o ON(o.id=od.order_id)
                    JOIN customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
                    WHERE od.product_id NOT IN($fee_products) AND od.is_deleted='N' AND 1=1 $search_incr AND c.type='Customer' AND o.status IN('Payment Approved')", $sch_params);
    if (empty($getNBCount["total_policies"])) {
        $getNBCount["total_policies"] = 0;
    }
    return $getNBCount["total_policies"];
}

function getBusinessSummaryData($is_renewal = 'N', $filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $sch_params = array();
    $incr = "";
    if (strtotime($filter["getfromdate"]) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $incr .= " AND DATE(o.created_at) >= :fcreated_at";
    }
    if (strtotime($filter["gettodate"]) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $incr .= " AND DATE(o.created_at) <= :tcreated_at";
    }

    if ($is_renewal == 'Y') {
        $incr .= " AND o.is_renewal = 'Y'";
    } elseif ($is_renewal == 'N') {
        $incr .= " AND o.is_renewal = 'N'";
    }
    $enroll_fee = get_enrollment_fee_prd_ids('string');
    // pre_print(array_merge($incrArray, $sch_params),false);
    $sql = "SELECT sum(od.unit_price*od.qty) as TotalPremium,COUNT(DISTINCT c.id) AS TotalPolicyHolder,COUNT(CASE WHEN od.product_id NOT IN ($enroll_fee) THEN 1 END) AS TotalPolicies
                  FROM order_details od
                  JOIN prd_main p ON(p.id=od.product_id)
                  JOIN orders as o ON(o.id=od.order_id)
                  JOIN customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
                  WHERE p.is_deleted='N' AND od.is_refund = 'N' AND od.product_id NOT IN($fee_products) AND od.is_deleted='N' AND o.payment_type IN('CC','ACH') AND o.status IN('Payment Approved') $incr";
    //pre_print(array($sql,$sch_params));
    $row = $pdo->selectOne($sql, $sch_params);

    if($row["TotalPremium"] > 0) {
        $AvgPremiumPerHolder = $row["TotalPremium"] / $row["TotalPolicyHolder"];
    } else {
        $AvgPremiumPerHolder = 0;
    }

    if($row["TotalPolicies"] > 0) {
        $AvgPoliciesPerHolder = $row["TotalPolicies"] / $row["TotalPolicyHolder"];
    } else {
        $AvgPoliciesPerHolder = 0;
    }
    

    return array(
        'TotalPremium' => $row["TotalPremium"],
        'AvgPremiumPerHolder' => $AvgPremiumPerHolder,
        'TotalPolicyHolder' => $row["TotalPolicyHolder"],
        'TotalPolicies' => $row["TotalPolicies"],
        'AvgPoliciesPerHolder' => number_format((float)$AvgPoliciesPerHolder, 2, '.', ''),
    );
}

function getRenewalsSummaryData($filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $sch_params = array();
    $incr = "";
    if (strtotime($filter["getfromdate"]) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $incr .= " AND DATE(o.created_at) >= :fcreated_at";
    }
    if (strtotime($filter["gettodate"]) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $incr .= " AND DATE(o.created_at) <= :tcreated_at";
    }

    // pre_print(array_merge($incrArray, $sch_params),false);
    $sql = "SELECT sum(od.unit_price*od.qty) as TotalPremium,
                    sum(IF(o.status = 'Payment Approved',(od.unit_price*od.qty),0)) AS TotalApprovedPremium,
                    sum(IF(o.status = 'Payment Declined',(od.unit_price*od.qty),0)) AS TotalDeclinedPremium,
                    COUNT(DISTINCT c.id) AS TotalPolicyHolder,COUNT(od.product_id) AS TotalPolicies
                  FROM order_details od
                  JOIN prd_main p ON(p.id=od.product_id)
                  JOIN orders as o ON(o.id=od.order_id)
                  JOIN customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
                  WHERE p.is_deleted='N' AND od.is_refund = 'N' AND od.product_id NOT IN($fee_products) AND od.is_deleted='N' AND o.payment_type IN('CC','ACH') AND o.is_renewal = 'Y' AND o.status IN('Payment Approved','Payment Declined') $incr";
    //pre_print(array($sql,$sch_params));
    $row = $pdo->selectOne($sql, $sch_params);

    if($row["TotalPremium"] > 0) {
        $AvgPremiumPerHolder = $row["TotalPremium"] / $row["TotalPolicyHolder"];
    } else {
        $AvgPremiumPerHolder = 0;
    }

    if($row["TotalPolicies"] > 0) {
        $AvgPoliciesPerHolder = $row["TotalPolicies"] / $row["TotalPolicyHolder"];
    } else {
        $AvgPoliciesPerHolder = 0;
    }

    if($row["TotalApprovedPremium"] > 0) {
        $PerCollectedPremium = (float) $row["TotalApprovedPremium"] / $row["TotalPremium"] * 100;
    } else {
        $PerCollectedPremium = 0;
    }
    
    //pre_print($PerCollectedPremium);
    return array(
        'TotalPremium' => $row["TotalPremium"],
        'TotalApprovedPremium' => $row["TotalApprovedPremium"],
        'TotalDeclinedPremium' => $row["TotalDeclinedPremium"],
        'PerCollectedPremium' => number_format((float)$PerCollectedPremium, 2, '.', ''),
        'AvgPremiumPerHolder' => $AvgPremiumPerHolder,
        'TotalPolicyHolder' => $row["TotalPolicyHolder"],
        'TotalPolicies' => $row["TotalPolicies"],
        'AvgPoliciesPerHolder' => number_format((float)$AvgPoliciesPerHolder, 2, '.', ''),
    );
}

function getBusinessCommissionsSummaryData($is_renewal = "N", $filter = array("getfromdate" => "", "gettodate" => ""))
{
    global $pdo;
    $sch_params = array();
    $incr = "";
    if (strtotime($filter["getfromdate"]) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $incr .= " AND DATE(cm.created_at) >= :fcreated_at";
    }
    if (strtotime($filter["gettodate"]) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $incr .= " AND DATE(cm.created_at) <= :tcreated_at";
    }

    if ($is_renewal == "Y") {
        $incr .= " AND cm.sub_type in ('Renewals')";
    } else {
        $incr .= " AND cm.sub_type in ('New','Advance')";
    }

    $sql = "SELECT SUM(total_payment) as total_payment, SUM(new_business_payment) as new_business_payment, SUM(advance_payment) as advance_payment,SUM(renewals_payment) as renewals_payment FROM
    (SELECT
     SUM(IF(cm.sub_type='New',cm.amount,0)) AS new_business_payment,
     SUM(IF(cm.sub_type='Advance',cm.amount,0)) AS advance_payment,
     SUM(IF(cm.sub_type='Renewals',cm.amount,0)) AS renewals_payment,
     SUM(cm.amount) as total_payment
    FROM
        commission cm
    WHERE cm.customer_id NOT IN(1,265) AND cm.is_deleted = 'N' AND cm.type NOT IN ('Hooray Health Dialer') AND cm.is_deleted='N' $incr
    UNION
    SELECT
     SUM(IF(cm.sub_type='New',cm.amount,0)) AS new_business_payment,
     SUM(IF(cm.sub_type='Advance',cm.amount,0)) AS advance_payment,
     SUM(IF(cm.sub_type='Renewals',cm.amount,0)) AS renewals_payment,
     SUM(cm.amount) as total_payment
    FROM
        commission_monthly cm
    WHERE cm.customer_id NOT IN(1,265) AND cm.is_deleted = 'N' AND cm.type NOT IN ('Hooray Health Dialer') AND cm.is_deleted='N' $incr) t";
    // echo $sql;
    $row = $pdo->selectOne($sql, $sch_params);
    return array(
        'TotalCommissions' => $row['total_payment'],
        'NewBusinessCommissions' => $row['new_business_payment'],
        'AdvanceCommissions' => $row['advance_payment'],
    );
}
function valid_cell_value($value) 
{
    return str_replace(',','',$value);
}
function new_business_commission_per_agents($searchArray = array())
{
    global $pdo;
    $incr = '';
    $sch_params = array();

    $incr .= " AND cm.sub_type in ('New','Advance')";

    if (strtotime($searchArray["getfromdate"]) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($searchArray["getfromdate"]));
        $incr .= " AND DATE(cm.created_at) >= :fcreated_at";
    }
    if (strtotime($searchArray["gettodate"]) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($searchArray["gettodate"]));
        $incr .= " AND DATE(cm.created_at) <= :tcreated_at";
    }

    $sql = "SELECT IF(a.business_name = '' OR a.business_name IS NULL,CONCAT(a.fname,' ',a.lname),a.business_name)as agent_name,a.rep_id as agent_display_id,ord.total_payment,ord.advance_payment,ord.renewals_payment,ord.new_business_payment
        FROM customer a
        LEFT JOIN
            (SELECT
                cm.customer_id,
                SUM(IF(cm.sub_type='New',cm.amount,0)) AS new_business_payment,
                SUM(IF(cm.sub_type='Advance',cm.amount,0)) AS advance_payment,
                SUM(IF(cm.sub_type='Renewals',cm.amount,0)) AS renewals_payment,
                SUM(cm.amount) as total_payment
            FROM commission cm WHERE cm.customer_id != 265 AND cm.is_deleted = 'N' AND cm.type NOT IN ('Hooray Health Dialer') AND cm.is_deleted='N' $incr GROUP BY cm.customer_id
                UNION
            SELECT
                cm.customer_id,
                SUM(IF(cm.sub_type='New',cm.amount,0)) AS new_business_payment,
                SUM(IF(cm.sub_type='Advance',cm.amount,0)) AS advance_payment,
                SUM(IF(cm.sub_type='Renewals',cm.amount,0)) AS renewals_payment,
                SUM(cm.amount) as total_payment
            FROM commission_monthly cm WHERE cm.customer_id != 265 AND cm.is_deleted = 'N' AND cm.type NOT IN ('Hooray Health Dialer') AND cm.is_deleted='N' $incr GROUP BY cm.customer_id) as ord ON(ord.customer_id=a.id)
        WHERE a.is_deleted='N' GROUP BY a.id HAVING total_payment > 0 ORDER BY total_payment DESC";
    $rows = $pdo->select($sql,$sch_params);
    return $rows;
}

function new_business_summary_per_agents($searchArray = array())
{
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $enroll_products = get_enrollment_fee_prd_ids('string');
    $incr = '';
    $sch_params = array();

    if (strtotime($searchArray["getfromdate"]) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($searchArray["getfromdate"]));
        $incr .= " AND DATE(o.created_at) >= :fcreated_at";
    }
    if (strtotime($searchArray["gettodate"]) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($searchArray["gettodate"]));
        $incr .= " AND DATE(o.created_at) <= :tcreated_at";
    }

    $sql = "SELECT IF(a.business_name = '' OR a.business_name IS NULL,CONCAT(a.fname,' ',a.lname),a.business_name)as agent_name,a.rep_id as agent_display_id,ord.total_premiums,ord.total_policy_holders,ord.total_policies
        FROM customer a
        LEFT JOIN
            (SELECT c.sponsor_id,sum(od.unit_price*od.qty) as total_premiums,count(DISTINCT c.id) as total_policy_holders,COUNT(CASE WHEN od.product_id NOT IN ($enroll_products) THEN 1 END) AS total_policies
                FROM order_details od
                JOIN orders as o ON(o.id=od.order_id)
                JOIN customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
                WHERE od.product_id NOT IN($fee_products) AND od.is_refund = 'N' AND o.is_renewal='N' AND od.is_deleted='N' AND 1=1 $incr AND o.payment_type IN('CC','ACH') AND o.status IN('Payment Approved')
                GROUP BY c.sponsor_id
            ) as ord ON ord.sponsor_id=a.id
        WHERE a.is_deleted='N' GROUP BY a.id having total_premiums > 0 ORDER BY total_premiums DESC";
    $rows = $pdo->select($sql,$sch_params);
    return $rows;
}
function new_business_summary_per_products($searchArray = array())
{
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $incr = '';
    $sch_params = array();

    if (strtotime($searchArray["getfromdate"]) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($searchArray["getfromdate"]));
        $incr .= " AND DATE(o.created_at) >= :fcreated_at";
    }
    if (strtotime($searchArray["gettodate"]) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($searchArray["gettodate"]));
        $incr .= " AND DATE(o.created_at) <= :tcreated_at";
    }

    $sql = "SELECT p.id as product_id,p.name as product_name,ord.total_premiums,ord.policy_holders,p.product_code
            FROM prd_main p
            LEFT JOIN
                (SELECT od.product_id,sum(od.unit_price*od.qty) as total_premiums,count(DISTINCT c.id) as policy_holders
                    FROM order_details od
                    JOIN orders as o ON(o.id=od.order_id)
                    JOIN customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
                    WHERE od.product_id NOT IN($fee_products) AND od.is_deleted='N' AND od.is_refund = 'N' AND o.is_renewal='N' AND 1=1 $incr AND o.payment_type IN('CC','ACH') AND o.status IN('Payment Approved')
                    GROUP BY od.product_id
                ) as ord ON ord.product_id=p.id
            WHERE p.is_deleted='N' GROUP BY p.id having total_premiums > 0 ORDER BY total_premiums DESC";
    $rows = $pdo->select($sql,$sch_params);
    return $rows;
}
function renewals_business_commission_per_agents($searchArray = array())
{
    global $pdo;
    $incr = '';
    $sch_params = array();

    $incr .= " AND cm.sub_type in ('Renewals')";

    if (strtotime($searchArray["getfromdate"]) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($searchArray["getfromdate"]));
        $incr .= " AND DATE(cm.created_at) >= :fcreated_at";
    }
    if (strtotime($searchArray["gettodate"]) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($searchArray["gettodate"]));
        $incr .= " AND DATE(cm.created_at) <= :tcreated_at";
    }

    $sql = "SELECT IF(a.business_name = '' OR a.business_name IS NULL,CONCAT(a.fname,' ',a.lname),a.business_name)as agent_name,a.rep_id as agent_display_id,ord.total_payment,ord.advance_payment,ord.renewals_payment,ord.new_business_payment
        FROM customer a
        LEFT JOIN
            (SELECT
                cm.customer_id,
                SUM(IF(cm.sub_type='New',cm.amount,0)) AS new_business_payment,
                SUM(IF(cm.sub_type='Advance',cm.amount,0)) AS advance_payment,
                SUM(IF(cm.sub_type='Renewals',cm.amount,0)) AS renewals_payment,
                SUM(cm.amount) as total_payment
            FROM commission cm WHERE cm.customer_id != 265 AND cm.is_deleted = 'N' AND cm.type NOT IN ('Hooray Health Dialer') AND cm.is_deleted='N' $incr GROUP BY cm.customer_id
                UNION
            SELECT
                cm.customer_id,
                SUM(IF(cm.sub_type='New',cm.amount,0)) AS new_business_payment,
                SUM(IF(cm.sub_type='Advance',cm.amount,0)) AS advance_payment,
                SUM(IF(cm.sub_type='Renewals',cm.amount,0)) AS renewals_payment,
                SUM(cm.amount) as total_payment
            FROM commission_monthly cm WHERE cm.customer_id != 265 AND cm.is_deleted = 'N' AND cm.type NOT IN ('Hooray Health Dialer') AND cm.is_deleted='N' $incr GROUP BY cm.customer_id) as ord ON(ord.customer_id=a.id)
        WHERE a.is_deleted='N' GROUP BY a.id HAVING total_payment > 0 ORDER BY total_payment DESC";
    $rows = $pdo->select($sql,$sch_params);
    return $rows;
}
function renewals_business_summary_per_agents($searchArray = array())
{
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $incr = '';
    $sch_params = array();

    $incr .= " AND o.is_renewal='Y'";

    if (strtotime($searchArray["getfromdate"]) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($searchArray["getfromdate"]));
        $incr .= " AND DATE(o.created_at) >= :fcreated_at";
    }
    if (strtotime($searchArray["gettodate"]) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($searchArray["gettodate"]));
        $incr .= " AND DATE(o.created_at) <= :tcreated_at";
    }

    $sql = "SELECT a.id as agent_id,IF(a.business_name = '' OR a.business_name IS NULL,CONCAT(a.fname,' ',a.lname),a.business_name)as agent_name,a.rep_id as agent_display_id,ord.total_premiums,ord.total_approved_premiums,ord.total_declined_premiums
            FROM customer a
            LEFT JOIN
                (SELECT c.sponsor_id,
        SUM(od.unit_price*od.qty) as total_premiums,
        SUM(IF(o.status = 'Payment Approved',(od.unit_price*od.qty),0)) AS total_approved_premiums,
        SUM(IF(o.status = 'Payment Declined',(od.unit_price*od.qty),0)) AS total_declined_premiums
                    FROM order_details od
                    JOIN prd_main p ON(p.id=od.product_id)
                    JOIN orders as o ON(o.id=od.order_id)
                    JOIN customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
                    WHERE p.is_deleted='N' AND od.is_deleted='N' AND od.is_refund = 'N' AND od.product_id NOT IN($fee_products) AND 1=1 $incr AND o.payment_type IN('CC','ACH') AND o.status IN('Payment Approved','Payment Declined')
                    GROUP BY c.sponsor_id
                ) as ord ON ord.sponsor_id=a.id
            WHERE a.is_deleted='N' GROUP BY a.id having total_premiums > 0 ORDER BY total_premiums DESC";
    $rows = $pdo->select($sql,$sch_params);
    return $rows;
}

function renewals_business_summary_per_products($searchArray = array())
{
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $incr = '';
    $sch_params = array();

    if (strtotime($searchArray["getfromdate"]) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($searchArray["getfromdate"]));
        $incr .= " AND DATE(o.created_at) >= :fcreated_at";
    }
    if (strtotime($searchArray["gettodate"]) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($searchArray["gettodate"]));
        $incr .= " AND DATE(o.created_at) <= :tcreated_at";
    }

    $sql = "SELECT p.id as product_id,p.name as product_name,ord.total_premiums,ord.total_approved_premiums,ord.total_declined_premiums,p.product_code
            FROM prd_main p
            LEFT JOIN
                (SELECT od.product_id,
        SUM(od.unit_price*od.qty) as total_premiums,
        SUM(IF(o.status = 'Payment Approved',od.unit_price*od.qty,0)) as total_approved_premiums,
        SUM(IF(o.status = 'Payment Declined',od.unit_price*od.qty,0)) as total_declined_premiums
                    FROM order_details od
                    JOIN orders as o ON(o.id=od.order_id)
                    JOIN customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
                    WHERE od.product_id NOT IN($fee_products) AND od.is_refund = 'N' AND o.is_renewal='Y' AND od.is_deleted='N' AND 1=1 $incr AND o.payment_type IN('CC','ACH') AND o.status IN('Payment Approved','Payment Declined')
                    GROUP BY od.product_id
                ) as ord ON ord.product_id=p.id
            WHERE p.is_deleted='N' GROUP BY p.id having total_premiums > 0 ORDER BY total_premiums DESC";
    $rows = $pdo->select($sql,$sch_params);
    return $rows;
}


function getQuickSalesSummaryData($filter = array("getfromdate" => "", "gettodate" => ""))
{
   
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $sch_params = array();
    $incr = "";
    if (strtotime($filter["getfromdate"]) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $incr .= " AND DATE(o.created_at) >= :fcreated_at";
    }
    if (strtotime($filter["gettodate"]) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $incr .= " AND DATE(o.created_at) <= :tcreated_at";
    }
   
    $sql = "SELECT 
                SUM(od.unit_price*od.qty) AS TotalPremium,
                COUNT(DISTINCT c.id) AS TotalPolicyHolder,

                SUM(IF(o.status = 'Payment Approved' AND od.is_refund = 'N',(od.unit_price*od.qty),0)) AS TotalApprovedPremium,
                COUNT(DISTINCT (CASE WHEN o.status = 'Payment Approved' THEN c.id END)) AS TotalApprovedPolicyHolder,

                SUM(IF(o.status = 'Payment Declined',(od.unit_price*od.qty),0)) AS TotalDeclinedPremium,
                COUNT(DISTINCT (CASE WHEN o.status = 'Payment Declined' THEN c.id END)) AS TotalDeclinedPolicyHolder,

                SUM(IF(o.status = 'Refund' OR od.is_refund = 'Y' ,(od.unit_price*od.qty),0)) AS TotalRefundedPremium,
                COUNT(DISTINCT (CASE WHEN o.status = 'Refund' OR od.is_refund = 'Y' THEN c.id END)) AS TotalRefundedPolicyHolder,

                SUM(IF(o.status = 'Void' OR od.is_refund = 'Y' ,(od.unit_price*od.qty),0)) AS TotalVoidPremium,
                COUNT(DISTINCT (CASE WHEN o.status = 'Void' OR od.is_refund = 'Y' THEN c.id END)) AS TotalVoidPolicyHolder,

                SUM(IF(o.status = 'Chargeback',(od.unit_price*od.qty),0)) AS TotalChargebackedPremium,
                COUNT(DISTINCT (CASE WHEN o.status = 'Chargeback' THEN c.id END)) AS TotalChargebackedPolicyHolder,

                SUM(IF(o.status = 'Cancelled',(od.unit_price*od.qty),0)) AS TotalCancelledPremium,
                COUNT(DISTINCT (CASE WHEN o.status = 'Cancelled' THEN c.id END)) AS TotalCancelledPolicyHolder,

                SUM(IF(o.status = 'Payment Approved' AND od.is_refund = 'N' AND o.payment_type = 'CC',(od.unit_price*od.qty),0)) AS TotalCCApprovedPremium,
                COUNT(DISTINCT (CASE WHEN o.status = 'Payment Approved' AND o.payment_type = 'CC' THEN c.id END)) AS TotalCCApprovedPolicyHolder,

                SUM(IF(o.status = 'Payment Declined' AND o.payment_type = 'CC',(od.unit_price*od.qty),0)) AS TotalCCDeclinedPremium,
                COUNT(DISTINCT (CASE WHEN o.status = 'Payment Declined' AND o.payment_type = 'CC' THEN c.id END)) AS TotalCCDeclinedPolicyHolder,

                SUM(IF(o.status = 'Payment Approved' AND od.is_refund = 'N' AND o.payment_type = 'ACH',(od.unit_price*od.qty),0)) AS TotalACHApprovedPremium,
                COUNT(DISTINCT (CASE WHEN o.status = 'Payment Approved' AND o.payment_type = 'ACH' THEN c.id END)) AS TotalACHApprovedPolicyHolder,

                SUM(IF(o.status = 'Payment Declined' AND o.payment_type = 'ACH',(od.unit_price*od.qty),0)) AS TotalACHDeclinedPremium,
                COUNT(DISTINCT (CASE WHEN o.status = 'Payment Declined' AND o.payment_type = 'ACH' THEN c.id END)) AS TotalACHDeclinedPolicyHolder

                  FROM order_details od
                  JOIN prd_main p ON(p.id=od.product_id)
                  JOIN orders o ON(o.id=od.order_id)
                  JOIN customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
                  WHERE p.is_deleted='N' AND od.product_id NOT IN($fee_products) AND od.is_deleted='N' AND o.payment_type IN('CC','ACH') AND o.status IN('Payment Approved','Payment Declined','Refund','Chargeback','Cancelled') AND c.sponsor_id NOT IN(1) $incr";
    // pre_print(array($sql,$sch_params));
    $row = $pdo->selectOne($sql, $sch_params);    
    // pre_print($row);

    return array(
        'TotalPremium' => $row["TotalPremium"],
        'TotalPolicyHolder' => $row["TotalPolicyHolder"],
        
        'TotalApprovedPremium' => $row["TotalApprovedPremium"],
        'TotalApprovedPolicyHolder' => $row["TotalApprovedPolicyHolder"],

        'TotalDeclinedPremium' => $row["TotalDeclinedPremium"],
        'TotalDeclinedPolicyHolder' => $row["TotalDeclinedPolicyHolder"],

        'TotalRefundedPremium' => $row["TotalRefundedPremium"],
        'TotalRefundedPolicyHolder' => $row["TotalRefundedPolicyHolder"],

        'TotalVoidPremium' => $row["TotalVoidPremium"],
        'TotalVoidPolicyHolder' => $row["TotalVoidPolicyHolder"],

        'TotalChargebackedPremium' => $row["TotalChargebackedPremium"],
        'TotalChargebackedPolicyHolder' => $row["TotalChargebackedPolicyHolder"],
        
        'TotalCancelledPremium' => $row["TotalCancelledPremium"],
        'TotalCancelledPolicyHolder' => $row["TotalCancelledPolicyHolder"],
        
        'TotalCCApprovedPremium' => $row["TotalCCApprovedPremium"],
        'TotalCCApprovedPolicyHolder' => $row["TotalCCApprovedPolicyHolder"],
        
        'TotalCCDeclinedPremium' => $row["TotalCCDeclinedPremium"],
        'TotalCCDeclinedPolicyHolder' => $row["TotalCCDeclinedPolicyHolder"],
        
        'TotalACHApprovedPremium' => $row["TotalACHApprovedPremium"],
        'TotalACHApprovedPolicyHolder' => $row["TotalACHApprovedPolicyHolder"],

        'TotalACHDeclinedPremium' => $row["TotalACHDeclinedPremium"],
        'TotalACHDeclinedPolicyHolder' => $row["TotalACHDeclinedPolicyHolder"],
    );
}

function getDashboardSalesSummaryData($filter = array("getfromdate" => "", "gettodate" => ""))
{
   
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $enroll_products = get_enrollment_fee_prd_ids('string');
    $sch_params = array();
    $incr = "";

    if (strtotime($filter["getfromdate"]) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $incr .= " AND DATE(o.created_at) >= :fcreated_at";
    }
    if (strtotime($filter["gettodate"]) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $incr .= " AND DATE(o.created_at) <= :tcreated_at";
    } 
    if (strtotime($filter["getfromdate"]) > 0) {
        $termParams[':tfcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
         $termedIncr .= " AND DATE(termination_date) >= :tfcreated_at";
    }
    if (strtotime($filter["gettodate"]) > 0) {
        $termParams[':ttcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $termedIncr .= " AND DATE(termination_date) <= :ttcreated_at";
    } 
   
    $sql = "SELECT 
                SUM(od.unit_price*od.qty) AS TotalPremium,
                COUNT(DISTINCT c.id) AS TotalMember,
                COUNT(CASE WHEN od.product_id NOT IN ($enroll_products) THEN 1 END) AS TotalPolicies,
                SUM(IF(o.is_renewal='Y',(od.unit_price*od.qty),0)) AS Renewals,
                count(distinct (case when o.is_renewal='N' then c.id end)) as NewBusinessMember,
                count(distinct (case when o.is_renewal='Y' then c.id end)) as RenewalsMember,
                SUM(IF(o.is_renewal='N',(od.unit_price*od.qty),0)) AS NewBusiness
            FROM order_details od
              JOIN prd_main p ON (p.id=od.product_id)
              JOIN orders o ON (o.id=od.order_id)
              JOIN customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
            WHERE p.is_deleted='N' AND od.is_deleted='N' AND od.is_refund = 'N' AND o.payment_type IN('CC','ACH') AND o.status IN('Payment Approved') $incr";
                 
    // pre_print(array($sql,$sch_params),false);
    $row = $pdo->selectOne($sql, $sch_params);    
    // pre_print($row);
    $TermMemberSql = "SELECT COUNT(DISTINCT customer_id) as TermedMember FROM website_subscriptions WHERE  status='Terminated' $termedIncr";

          $resTermMember = $pdo->selectOne($TermMemberSql, $termParams);
          $TermedMember = $resTermMember['TermedMember'];
    // pre_print($resTermMember);
    $fallOff = 0;
     if($row["NewBusinessMember"] > 0) {
            $fallOffCount = ($TermedMember / $row["NewBusinessMember"]) * 100;
             $fallOff = number_format((float)$fallOffCount, 2, '.', '').'%';
        } else {
           $fallOff = '0.00%';
      }
    
    if($row["TotalPremium"] > 0) {
        $AvgPremiumPerHolder = $row["TotalPremium"] / $row["TotalMember"];
    } else {
        $AvgPremiumPerHolder = 0;
    }

    if($row["TotalPolicies"] > 0) {
        $AvgPoliciesPerMember = $row["TotalPolicies"] / $row["TotalMember"];
    } else {
        $AvgPoliciesPerMember = 0;
    }

    return array(
        'TotalPremium' => $row["TotalPremium"],
        'NewBusiness' => $row["NewBusiness"],
        'NewBusinessMember' => $row["NewBusinessMember"],
        'RenewalsMember' => $row["RenewalsMember"],
        "Renewals" => $row["Renewals"],
        'AvgPremiumPerHolder' => $AvgPremiumPerHolder,
        'TotalMember' => $row["TotalMember"],
        'TotalPolicies' => $row["TotalPolicies"],
        'AvgPoliciesPerMember' => number_format((float)$AvgPoliciesPerMember, 2, '.', ''),
        'NetFallOff' => $fallOff,
        'TermedMember' => $TermedMember
    );
}


//  reporting funcation based on transactions table start

function getDashboardSalesSummaryDataV1($filter = array("getfromdate" => "", "gettodate" => "")) {
   
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $enroll_products = get_enrollment_fee_prd_ids('string');
    $sch_params = array();
    $incr = "";

    if (strtotime($filter["getfromdate"]) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $incr .= " AND DATE(t.created_at) >= :fcreated_at";
    }
    if (strtotime($filter["gettodate"]) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $incr .= " AND DATE(t.created_at) <= :tcreated_at";
    }
    if (strtotime($filter["getfromdate"]) > 0) {
        $termParams[':tfcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
         $termedIncr .= " AND DATE(termination_date) >= :tfcreated_at";
    }
    if (strtotime($filter["gettodate"]) > 0) {
        $termParams[':ttcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $termedIncr .= " AND DATE(termination_date) <= :ttcreated_at";
    } 
    $sql = "SELECT 
            SUM(t.credit) AS TotalPremium,
            SUM(IF(t.transaction_type = 'Renewal Order', t.credit ,0)) AS Renewals,
            SUM(IF(t.transaction_type = 'New Order',t.credit,0)) AS NewBusiness, 
            COUNT(DISTINCT ord.cust_id) AS TotalMember, 
            SUM(ord.total_policies) as TotalPolicies, 
             SUM(IF(t.transaction_type = 'Renewal Order', ord.total_policies ,0)) AS Renewalspolicy,
            SUM(IF(t.transaction_type = 'New Order',ord.total_policies,0)) AS NewBusinesspolicy, 
            COUNT(DISTINCT (CASE WHEN t.transaction_type='New Order' then ord.cust_id end)) as NewBusinessMember, 
            COUNT(DISTINCT (CASE WHEN t.transaction_type='Renewal Order' then ord.cust_id end)) as RenewalsMember
            FROM transactions t
            LEFT JOIN 
            (SELECT o.id,o.is_renewal, c.id as cust_id , COUNT(od.product_id) AS total_policies 
                    FROM orders o 
                    JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
                    JOIN prd_main p ON(p.id=od.product_id) 
                    JOIN customer c ON (c.id=o.customer_id) 
                    WHERE o.payment_type IN('CC','ACH') AND od.product_id NOT IN($fee_products) AND od.product_id NOT IN($enroll_products) GROUP BY o.id 
            ) as ord ON ord.id = t.order_id 
            WHERE t.transaction_type IN ('New Order', 'Renewal Order') $incr";
    
    $row = $pdo->selectOne($sql, $sch_params);    

    $TermMemberSql = "SELECT COUNT(DISTINCT customer_id) as TermedMember FROM website_subscriptions WHERE  status='Terminated' $termedIncr";
    $resTermMember = $pdo->selectOne($TermMemberSql, $termParams);
    $TermedMember = $resTermMember['TermedMember'];
    
    $fallOff = 0;
    if($row["NewBusinessMember"] > 0) {
        $fallOffCount = ($TermedMember / $row["NewBusinessMember"]) * 100;
        $fallOff = number_format((float)$fallOffCount, 2, '.', '').'%';
    } else {
        $fallOff = '0.00%';
    }
    
    if($row["TotalPremium"] > 0) {
        $AvgPremiumPerHolder = $row["TotalPremium"] / $row["TotalMember"];
          
          /*first tooltip data condition start*/ 
          $new_business = $row["NewBusiness"] / $row["NewBusinessMember"];
          if($row["RenewalsMember"] != 0){
          $renewal_business = $row["Renewals"] / $row["RenewalsMember"];
          }else{
            $row["RenewalsMember"] = 0;
          }
          /*tooltip data condition End*/

    } else {
        $AvgPremiumPerHolder = 0;
    }

    if($row["TotalPolicies"] > 0) {
        $AvgPoliciesPerMember = $row["TotalPolicies"] / $row["TotalMember"];
        
        /*last tooltip data condition*/
        if($row["NewBusinessMember"] != 0){
        $AvgnewPolicies = $row['NewBusinesspolicy'] / $row["NewBusinessMember"];
        }
        else{
            $row["NewBusinessMember"] = 0;
        }
        if($row["RenewalsMember"] != 0){
        $AvgRenewalPolicies = $row['Renewalspolicy'] / $row["RenewalsMember"];
        }
        else{
            $row["RenewalsMember"] = 0;
        }
        /*last tooltip data condition*/
    } else {
        $AvgPoliciesPerMember = 0;

        /*last tooltip */
        $AvgnewPolicies = 0;
        $AvgRenewalPolicies = 0;
        /*end*/
    }


    return array(
        'TotalPremium' => $row["TotalPremium"],
        'NewBusiness' => $row["NewBusiness"],
        'NewBusinessMember' => $row["NewBusinessMember"],
        'RenewalsMember' => $row["RenewalsMember"],
        "Renewals" => $row["Renewals"],
        'AvgPremiumPerHolder' => $AvgPremiumPerHolder,
        'TotalMember' => $row["TotalMember"],
        'TotalPolicies' => $row["TotalPolicies"],
        'AvgPoliciesPerMember' => number_format((float)$AvgPoliciesPerMember, 2, '.', ''),
        'NetFallOff' => $fallOff,
        'TermedMember' => $TermedMember,
        'tooltip_NewBusiness' => $new_business,
        'tooltip_renewalBusiness' => $renewal_business,
        'tooltip_avg_newbusiness' => number_format($row['NewBusinesspolicy']),
        'tooltip_avg_renewal_business' => number_format($row['Renewalspolicy']),
        'tooltip_avg_policy_new_business' => number_format((float)$AvgnewPolicies, 2, '.', '')
        ,
        'tooltip_avg_policy_renewal_business' => number_format((float)$AvgRenewalPolicies, 2,
         '.', ''),
    );
}
function getDashboardSalesSummaryDataV2($filter = array("getfromdate" => "", "gettodate" => "")) {
   
    global $pdo;
    $enr_fee_prd_ids = get_enrollment_fee_prd_ids('string');
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    if($enr_fee_prd_ids!=""){
        $enr_fee_prd_ids.=",347";
    }

    $where_incr = " AND 1 ";

    if($enr_fee_prd_ids!='')
    {
        $where_incr.= "AND od.product_id NOT IN($enr_fee_prd_ids)";
    }
    if($fee_products!='')
    {
        $where_incr.= "AND od.product_id NOT IN($fee_products)";   
    }

    $sch_params = array();
    $incr = "";

    if (strtotime($filter["getfromdate"]) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $incr .= " AND DATE(t.created_at) >= :fcreated_at";
    }
    if (strtotime($filter["gettodate"]) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $incr .= " AND DATE(t.created_at) <= :tcreated_at";
    }
    

    $sql = "SELECT 
            SUM(t.credit) AS TotalPremium,
            SUM(IF(t.transaction_type = 'Renewal Order',t.credit,0)) AS Renewals,
            SUM(IF(t.transaction_type = 'New Order',t.credit,0)) AS NewBusiness, 
            SUM(ord.total_policies) AS TotalPolicies, 
            SUM(IF(t.transaction_type = 'Renewal Order',ord.total_policies,0)) AS Renewalspolicy,
            SUM(IF(t.transaction_type = 'New Order',ord.total_policies,0)) AS NewBusinesspolicy, 
            COUNT(DISTINCT t.customer_id) AS TotalMember, 
            COUNT(DISTINCT (CASE WHEN t.transaction_type='New Order' THEN t.customer_id END)) AS NewBusinessMember, 
            COUNT(DISTINCT (CASE WHEN t.transaction_type='Renewal Order' THEN t.customer_id END)) AS RenewalsMember
            FROM transactions t
            JOIN 
            (
                SELECT o.id,COUNT(od.product_id) AS total_policies 
                FROM orders o 
                JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
                JOIN prd_main p ON(p.id=od.product_id)
                WHERE p.is_deleted = 'N' $where_incr GROUP BY o.id 
            ) as ord ON ord.id = t.order_id 
            WHERE ord.id IS NOT NULL AND t.transaction_type IN ('New Order','Renewal Order') $incr";
    $row = $pdo->selectOne($sql, $sch_params);    

    $fall_of_sql = "SELECT 
                IFNULL(SUM(IF(DATE(ws.created_at) >= :fromDate AND DATE(ws.created_at) <= :toDate,1,0)),0) as N,
                IFNULL(SUM(IF(DATE(ws.created_at) < :fromDate AND ws.status = 'Active',1,0)),0) as S,
                IFNULL(SUM(IF(DATE(ws.termination_date) <= :toDate AND DATE(ws.termination_date) >= :fromDate AND ws.status = 'Terminated',1,0)),0) as T
                FROM transactions t
                JOIN order_details od ON(od.order_id = t.order_id AND od.is_deleted='N')
                JOIN customer c ON (t.customer_id = c.id) 
                JOIN website_subscriptions ws ON (ws.customer_id = c.id AND ws.product_id = od.product_id AND ws.id = od.website_id)
                WHERE t.transaction_type IN ('New Order')";

    $fall_of_res = $pdo->selectOne($fall_of_sql,array(':fromDate' => date('Y-m-d', strtotime($filter["getfromdate"])),':toDate' => date('Y-m-d', strtotime($filter["gettodate"]))));

    $S = $fall_of_res['S'];   
    $N = $fall_of_res['N'];   
    $T = $fall_of_res['T'];

    $E = $S + $N - $T;

    $fallOff = "0.00%";

    if($E > 0){
        $fallOffCount = ($T/$E)*100;
        if($fallOffCount > 0){
            $fallOff = number_format((float)$fallOffCount, 2, '.', '').'%';
        }
    }

    $termSql = "SELECT count(DISTINCT customer_id) as termMembers from website_subscriptions where status = 'Terminated' and DATE(termination_date) >= :fromDate AND DATE(termination_date) <= :toDate";

    $termRes = $pdo->selectOne($termSql,array(':fromDate' => date('Y-m-d', strtotime($filter["getfromdate"])),':toDate' => date('Y-m-d', strtotime($filter["gettodate"]))));
    $TermedMember = $termRes['termMembers'];
   
    $new_business =0.0;
    $renewal_business =0.0;
    if($row["TotalPremium"] > 0) {
        $AvgPremiumPerHolder = $row["TotalPremium"] / $row["TotalMember"];
          
          if($row["NewBusinessMember"] != 0){
            $new_business = $row["NewBusiness"] / $row["NewBusinessMember"];
          } else {
            $row["NewBusinessMember"] = 0;
          }
          if($row["RenewalsMember"] != 0){
            $renewal_business = $row["Renewals"] / $row["RenewalsMember"];
          } else {
            $row["RenewalsMember"] = 0;
          }
    } else {
        $AvgPremiumPerHolder = 0;
    }

    if($row["TotalPolicies"] > 0) {
        $AvgPoliciesPerMember = $row["TotalPolicies"] / $row["TotalMember"];
        
        if($row["NewBusinessMember"] != 0){
            $AvgnewPolicies = $row['NewBusinesspolicy'] / $row["NewBusinessMember"];
        } else{
            $row["NewBusinessMember"] = 0;
        }
        if($row["RenewalsMember"] != 0){
            $AvgRenewalPolicies = $row['Renewalspolicy'] / $row["RenewalsMember"];
        } else {
            $row["RenewalsMember"] = 0;
        }
    } else {
        $AvgPoliciesPerMember = 0;

        $AvgnewPolicies = 0;
        $AvgRenewalPolicies = 0;
    }


    return array(
        'fallOff' => $fall_of_res,
        'TotalPremium' => $row["TotalPremium"],
        'NewBusiness' => $row["NewBusiness"],
        'NewBusinessMember' => $row["NewBusinessMember"],
        'RenewalsMember' => $row["RenewalsMember"],
        "Renewals" => $row["Renewals"],
        'AvgPremiumPerHolder' => $AvgPremiumPerHolder,
        'TotalMember' => $row["TotalMember"],
        'TotalPolicies' => $row["TotalPolicies"],
        'AvgPoliciesPerMember' => number_format((float)$AvgPoliciesPerMember, 2, '.', ''),
        'NetFallOff' => $fallOff,
        'TermedMember' => $TermedMember,
        'tooltip_NewBusiness' => $new_business,
        'tooltip_renewalBusiness' => $renewal_business,
        'tooltip_avg_newbusiness' => number_format($row['NewBusinesspolicy']),
        'tooltip_avg_renewal_business' => number_format($row['Renewalspolicy']),
        'tooltip_avg_policy_new_business' => number_format((float)$AvgnewPolicies, 2, '.', '')
        ,
        'tooltip_avg_policy_renewal_business' => number_format((float)$AvgRenewalPolicies, 2,
         '.', ''),
        'fallOffDetails'=>array('S'=>$S,'N'=>$N,'T'=>$T,'E'=>$E)
    );
}

function getQuickSalesSummaryDataV1($filter = array("getfromdate" => "", "gettodate" => "")) {
   
    global $pdo;
    
    $sch_params = array();
    $incr = "";
    if (strtotime($filter["getfromdate"]) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $incr .= " AND DATE(t.created_at) >= :fcreated_at";
    }
    if (strtotime($filter["gettodate"]) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $incr .= " AND DATE(t.created_at) <= :tcreated_at";
    }
   
    $sql = "SELECT 
                SUM(IF(t.credit > 0,t.credit,t.debit)) AS TotalPremium,
                COUNT(DISTINCT t.customer_id) AS TotalPolicyHolder,

                SUM(IF((t.transaction_type = 'New Order'),t.credit,0)) AS TotalNewApprovedPremium,
                COUNT(DISTINCT (CASE WHEN ((t.transaction_type = 'New Order')) THEN t.customer_id END)) AS TotalNewApprovedPolicyHolder,

                SUM(IF((t.transaction_type = 'Renewal Order'),t.credit,0)) AS TotalRenewalApprovedPremium,
                COUNT(DISTINCT (CASE WHEN ((t.transaction_type = 'Renewal Order')) THEN t.customer_id END)) AS TotalRenewalApprovedPolicyHolder,

                SUM(IF((t.transaction_type = 'New Order' OR t.transaction_type = 'Renewal Order'),t.credit,0)) AS TotalApprovedPremium,

                COUNT(DISTINCT (CASE WHEN ((t.transaction_type = 'New Order' OR t.transaction_type = 'Renewal Order')) THEN t.customer_id END)) AS TotalApprovedPolicyHolder,

                SUM(IF(t.transaction_type = 'Payment Declined',t.debit,0)) AS TotalDeclinedPremium,
                COUNT(DISTINCT (CASE WHEN t.transaction_type = 'Payment Declined' THEN t.customer_id END)) AS TotalDeclinedPolicyHolder,

                SUM(IF(t.transaction_type = 'Refund Order',t.debit,0)) AS TotalRefundedPremium,
                COUNT(DISTINCT (CASE WHEN t.transaction_type = 'Refund Order' THEN t.customer_id END)) AS TotalRefundedPolicyHolder,

                SUM(IF(t.transaction_type = 'Void Order',t.debit,0)) AS TotalVoidPremium,
                COUNT(DISTINCT (CASE WHEN t.transaction_type = 'Void Order' THEN t.customer_id END)) AS TotalVoidPolicyHolder,

                SUM(IF(t.transaction_type = 'Chargeback',t.debit,0)) AS TotalChargebackedPremium,
                COUNT(DISTINCT (CASE WHEN t.transaction_type = 'Chargeback' THEN t.customer_id END)) AS TotalChargebackedPolicyHolder,

                SUM(IF(t.transaction_type = 'Cancelled',t.debit,0)) AS TotalCancelledPremium,
                COUNT(DISTINCT (CASE WHEN t.transaction_type = 'Cancelled' THEN t.customer_id END)) AS TotalCancelledPolicyHolder,

                SUM(IF((t.transaction_type = 'New Order' OR t.transaction_type = 'Renewal Order') AND ord.payment_type = 'CC',t.credit,0)) AS TotalCCApprovedPremium,
                COUNT(DISTINCT (CASE WHEN (t.transaction_type = 'New Order' OR t.transaction_type = 'Renewal Order') AND ord.payment_type = 'CC' THEN t.customer_id END)) AS TotalCCApprovedPolicyHolder,

                SUM(IF(t.transaction_type = 'Payment Declined' AND ord.payment_type = 'CC',t.debit,0)) AS TotalCCDeclinedPremium,
                COUNT(DISTINCT (CASE WHEN t.transaction_type = 'Payment Declined' AND ord.payment_type = 'CC' THEN t.customer_id END)) AS TotalCCDeclinedPolicyHolder,

                SUM(IF((t.transaction_type = 'New Order' OR t.transaction_type = 'Renewal Order') AND ord.payment_type = 'ACH',t.credit,0)) AS TotalACHApprovedPremium,
                COUNT(DISTINCT (CASE WHEN (t.transaction_type = 'New Order' OR t.transaction_type = 'Renewal Order') AND ord.payment_type = 'ACH' THEN t.customer_id END)) AS TotalACHApprovedPolicyHolder,

                SUM(IF(t.transaction_type = 'Payment Declined' AND ord.payment_type = 'ACH',t.debit,0)) AS TotalACHDeclinedPremium,
                COUNT(DISTINCT (CASE WHEN t.transaction_type = 'Payment Declined' AND ord.payment_type = 'ACH' THEN t.customer_id END)) AS TotalACHDeclinedPolicyHolder

                FROM transactions t 
                JOIN orders ord ON(ord.id = t.order_id) 
                JOIN customer c ON (c.id=ord.customer_id)
                WHERE t.transaction_type IN ('New Order','Renewal Order','Payment Declined','Refund Order','Chargeback','Cancelled','Void Order') $incr";


    $row = $pdo->selectOne($sql, $sch_params);    

    return array(
        'TotalPremium' => $row["TotalPremium"],
        'TotalPolicyHolder' => $row["TotalPolicyHolder"],
        
        'TotalApprovedPremium' => $row["TotalApprovedPremium"],
        'TotalApprovedPolicyHolder' => $row["TotalApprovedPolicyHolder"],

        'TotalNewApprovedPremium' => $row["TotalNewApprovedPremium"],
        'TotalNewApprovedPolicyHolder' => $row["TotalNewApprovedPolicyHolder"],

        'TotalRenewalApprovedPremium' => $row["TotalRenewalApprovedPremium"],
        'TotalRenewalApprovedPolicyHolder' => $row["TotalRenewalApprovedPolicyHolder"],

        'TotalDeclinedPremium' => $row["TotalDeclinedPremium"],
        'TotalDeclinedPolicyHolder' => $row["TotalDeclinedPolicyHolder"],

        'TotalRefundedPremium' => $row["TotalRefundedPremium"],
        'TotalRefundedPolicyHolder' => $row["TotalRefundedPolicyHolder"],

        'TotalVoidPremium' => $row["TotalVoidPremium"],
        'TotalVoidPolicyHolder' => $row["TotalVoidPolicyHolder"],

        'TotalChargebackedPremium' => $row["TotalChargebackedPremium"],
        'TotalChargebackedPolicyHolder' => $row["TotalChargebackedPolicyHolder"],
        
        'TotalCancelledPremium' => $row["TotalCancelledPremium"],
        'TotalCancelledPolicyHolder' => $row["TotalCancelledPolicyHolder"],
        
        'TotalCCApprovedPremium' => $row["TotalCCApprovedPremium"],
        'TotalCCApprovedPolicyHolder' => $row["TotalCCApprovedPolicyHolder"],
        
        'TotalCCDeclinedPremium' => $row["TotalCCDeclinedPremium"],
        'TotalCCDeclinedPolicyHolder' => $row["TotalCCDeclinedPolicyHolder"],
        
        'TotalACHApprovedPremium' => $row["TotalACHApprovedPremium"],
        'TotalACHApprovedPolicyHolder' => $row["TotalACHApprovedPolicyHolder"],

        'TotalACHDeclinedPremium' => $row["TotalACHDeclinedPremium"],
        'TotalACHDeclinedPolicyHolder' => $row["TotalACHDeclinedPolicyHolder"],
    );
}

function getQuickSalesSummaryDataV1Export($filter = array("getfromdate" => "", "gettodate" => "", "transaction_type" => "ALL", "payment_type" => "ALL")) {
   
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $enroll_products = get_enrollment_fee_prd_ids('string');
    $sch_params = array();
    $incr = "";
    if (strtotime($filter["getfromdate"]) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $incr .= " AND DATE(t.created_at) >= :fcreated_at";
    }
    if (strtotime($filter["gettodate"]) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $incr .= " AND DATE(t.created_at) <= :tcreated_at";
    }

    if($filter['transaction_type'] == "ALL") {
        $incr .= " AND t.transaction_type IN ('New Order','Renewal Order','Payment Declined','Refund Order','Chargeback','Cancelled','Void Order')";
    
    } elseif($filter['transaction_type'] == "Approved") {
        $incr .= " AND t.transaction_type IN ('New Order','Renewal Order')";
    
    } elseif($filter['transaction_type'] == "Declined") {
        $incr .= " AND t.transaction_type IN ('Payment Declined')";
    
    }  elseif($filter['transaction_type'] == "ApprovedDeclined") {
        $incr .= " AND t.transaction_type IN ('New Order','Renewal Order','Payment Declined')";
    
    } elseif($filter['transaction_type'] == "Refund") {
        $incr .= " AND t.transaction_type IN ('Refund Order')";
    
    } elseif($filter['transaction_type'] == "Void") {
        $incr .= " AND t.transaction_type IN ('Void Order')";
    
    } elseif($filter['transaction_type'] == "Chargeback") {
        $incr .= " AND t.transaction_type IN ('Chargeback')";
    
    } elseif($filter['transaction_type'] == "Cancelled") {
        $incr .= " AND t.transaction_type IN ('Cancelled')";
    
    } else {
        $incr .= " AND t.transaction_type IN ('New Order','Renewal Order','Payment Declined','Refund Order','Chargeback','Cancelled','Void Order')";
    }

    if($filter['payment_type'] == "ALL") {
        $incr .= " AND o.payment_type IN ('ACH','CC')";
    
    } elseif($filter['payment_type'] == "ACH") {
        $incr .= " AND o.payment_type IN ('ACH')";
    
    } elseif($filter['payment_type'] == "CC") {
        $incr .= " AND o.payment_type IN ('CC')";
   
    } else {
        $incr .= " AND o.payment_type IN ('ACH','CC')";
    }
   
    $sql = "SELECT 
                t.created_at as  transaction_date_time,
                o.display_id,
                c.rep_id,
                o.payment_type,
                IF(t.transaction_type IN('Renewal Order','New Order'),'Payment Approved',t.transaction_type) as transaction_type,
                IF(o.is_renewal = 'N','NewBusiness','Renewal') as sales_type,
                SUM(IF(t.transaction_type IN('Renewal Order','New Order'),t.credit,t.debit)) as transaction_amount
                FROM transactions t 
                JOIN orders o ON(o.id = t.order_id)
                JOIN customer c ON (c.id=o.customer_id) 
                WHERE 1=1 $incr
                GROUP BY t.id";
    $res = $pdo->select($sql, $sch_params);    
    return $res;
}

function getBusinessSummaryDataV1($is_renewal = 'N', $filter = array("getfromdate" => "", "gettodate" => "")) {
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $enroll_products = get_enrollment_fee_prd_ids("string");
    if($enroll_products!=""){
        $enroll_products.=',347';
    }

    $where_incr = " AND 1 ";

    if($enroll_products!='')
    {
        $where_incr.= "AND od.product_id NOT IN($enroll_products)";
    }
    if($fee_products!='')
    {
        $where_incr.= "AND od.product_id NOT IN($fee_products)";   
    }

    $sch_params = array();
    $incr = "";
    if (strtotime($filter["getfromdate"]) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $incr .= " AND DATE(t.created_at) >= :fcreated_at";
    }
    if (strtotime($filter["gettodate"]) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $incr .= " AND DATE(t.created_at) <= :tcreated_at";
    }

    if ($is_renewal == 'Y') {
        $incr .= " AND t.transaction_type = 'Renewal Order'";
    } elseif ($is_renewal == 'N') {
        $incr .= " AND t.transaction_type = 'New Order'";
    }


    $sql = "SELECT SUM(t.credit) as TotalPremium,
                COUNT(DISTINCT ord.cust_id) AS TotalPolicyHolder,
                SUM(ord.total_policies) AS TotalPolicies
            FROM transactions t
            LEFT JOIN 
                (SELECT o.id,o.is_renewal,o.payment_type, c.id as cust_id , COUNT(od.product_id) AS total_policies
                        FROM orders o 
                        JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
                        JOIN prd_main p ON(p.id=od.product_id) 
                        JOIN customer c ON (c.id=o.customer_id) 
                        WHERE p.is_deleted = 'N' AND o.payment_type IN('CC','ACH') $where_incr GROUP BY o.id 
                ) as ord ON ord.id = t.order_id 
            WHERE ord.id IS NOT NULL $incr";

                // FROM transactions t 
                // LEFT JOIN orders o ON (o.id=t.order_id)
                // LEFT JOIN order_details od ON(od.order_id = o.id)
                // LEFT JOIN prd_main p ON (p.id=od.product_id)
                // LEFT JOIN customer c ON (c.id=o.customer_id)
                // WHERE p.is_deleted='N' AND od.is_refund = 'N' AND o.payment_type IN('CC','ACH') AND p.category_id NOT IN (3) AND od.product_id NOT IN($fee_products) AND od.product_id NOT IN($enroll_products) AND  t.transaction_type IN ('New Order', 'Renewal Order') AND c.sponsor_id NOT IN (1) $incr";
    
    $row = $pdo->selectOne($sql, $sch_params);

    // pre_print($sql);

    if($row["TotalPremium"] > 0) {
        $AvgPremiumPerHolder = $row["TotalPremium"] / $row["TotalPolicyHolder"];
    } else {
        $AvgPremiumPerHolder = 0;
    }

    if($row["TotalPolicies"] > 0) {
        $AvgPoliciesPerHolder = $row["TotalPolicies"] / $row["TotalPolicyHolder"];
    } else {
        $AvgPoliciesPerHolder = 0;
    }
    

    return array(
        'TotalPremium' => $row["TotalPremium"],
        'AvgPremiumPerHolder' => $AvgPremiumPerHolder,
        'TotalPolicyHolder' => $row["TotalPolicyHolder"],
        'TotalPolicies' => $row["TotalPolicies"],
        'AvgPoliciesPerHolder' => number_format((float)$AvgPoliciesPerHolder, 2, '.', ''),
    );
}

function new_business_summary_per_productsV1($searchArray = array()) {
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $enroll_products = get_enrollment_fee_prd_ids('string');
    $incr = '';
    $sch_params = array();

    if (strtotime($searchArray["getfromdate"]) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($searchArray["getfromdate"]));
        $incr .= " AND DATE(t.created_at) >= :fcreated_at";
    }
    if (strtotime($searchArray["gettodate"]) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($searchArray["gettodate"]));
        $incr .= " AND DATE(t.created_at) <= :tcreated_at";
    }

    // $sql = "SELECT p.id as product_id,p.name as product_name,ord.total_premiums,ord.policy_holders,p.product_code
    //         FROM prd_main p
    //         LEFT JOIN
    //             (SELECT od.product_id,sum(t.credit) as total_premiums,count(DISTINCT c.id) as policy_holders
    //                 FROM transactions t
    //                 LEFT JOIN orders o ON (o.id = t.order_id)
    //                 LEFT JOIN order_details od ON(od.order_id = o.id)
    //                 LEFT JOIN customer c ON (c.id=o.customer_id)
    //                 WHERE od.product_id NOT IN($fee_products) AND od.product_id NOT IN($enroll_products) AND od.is_refund = 'N' AND o.is_renewal='N' AND 1=1 $incr AND o.payment_type IN('CC','ACH') AND t.transaction_type='New Order' AND c.sponsor_id NOT IN(1)
    //                 GROUP BY od.product_id
    //             ) as ord ON ord.product_id=p.id
    //         WHERE p.is_deleted='N' GROUP BY p.id having total_premiums > 0 ORDER BY total_premiums DESC";
    $sql = "SELECT p.id as product_id,p.name as product_name,ord.total_premiums,p.product_code,ord.total_premiums,(ord.unit_price*ord.total_policies) as total_pre_price, ord.total_policy_holders as policy_holders ,ord.total_policies as policies,ord.NewBusinessMember
        FROM prd_main p
        LEFT JOIN
        (SELECT tran.product_id as p_id,sum(t.credit) as total_premiums, tran.unit_price as unit_price,count(DISTINCT tran.cust_id) as total_policy_holders,SUM(tran.total_product) AS total_policies, COUNT(distinct (CASE WHEN t.transaction_type = 'New Order' THEN tran.cust_id END)) as NewBusinessMember
                FROM transactions as t
                LEFT JOIN (
                    SELECT o.id, p.id as product_id, c.id as cust_id, count(od.product_id) as total_product, od.unit_price
                    FROM orders as o
                    JOIN order_details od ON (od.order_id = o.id AND od.is_deleted='N')
                    JOIN prd_main p ON(p.id=od.product_id)
                    JOIN customer c ON (c.id=o.customer_id) 
                    WHERE p.is_deleted='N' AND o.payment_type IN('CC','ACH') AND od.product_id NOT IN($fee_products) AND od.product_id NOT IN($enroll_products) GROUP BY o.id,p.id
                ) as tran ON (tran.id = t.order_id)
            WHERE t.transaction_type IN ('New Order') $incr GROUP BY tran.product_id
        ) as ord ON ord.p_id = p.id
        WHERE p.is_deleted='N' GROUP BY p.id having total_premiums > 0 ORDER BY total_premiums DESC";

    $rows = $pdo->select($sql,$sch_params);
    return $rows;
}

function new_business_summary_per_agentsV1($searchArray = array()) {
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $enroll_products = get_enrollment_fee_prd_ids('string');
    $incr = '';
    $sch_params = array();

    if (strtotime($searchArray["getfromdate"]) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($searchArray["getfromdate"]));
        $incr .= " AND DATE(t.created_at) >= :fcreated_at";
    }
    if (strtotime($searchArray["gettodate"]) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($searchArray["gettodate"]));
        $incr .= " AND DATE(t.created_at) <= :tcreated_at";
    }

    // $sql = "SELECT IF(a.business_name = '' OR a.business_name IS NULL,CONCAT(a.fname,' ',a.lname),a.business_name)as agent_name,a.rep_id as agent_display_id,ord.total_premiums,ord.total_policy_holders,ord.total_policies
    //     FROM customer a
    //     LEFT JOIN
    //         (SELECT c.sponsor_id,sum(od.unit_price*od.qty) as total_premiums,count(DISTINCT c.id) as total_policy_holders,COUNT(od.product_id) AS total_policies
    //             FROM transactions t
    //             LEFT JOIN orders as o ON(o.id=t.order_id)
    //             LEFT JOIN order_details od ON(od.order_id = o.id)
    //             LEFT JOIN customer c ON (c.id=o.customer_id)
    //             WHERE od.product_id NOT IN($fee_products) AND od.product_id NOT IN($enroll_products) AND od.is_refund = 'N' AND t.transaction_type = 'New Order' $incr AND o.payment_type IN('CC','ACH')
    //             GROUP BY c.sponsor_id
    //         ) as ord ON ord.sponsor_id=a.id
    //     WHERE a.is_deleted='N' GROUP BY a.id having total_premiums > 0 ORDER BY total_premiums DESC";

    $sql = "SELECT IF(a.business_name = '' OR a.business_name IS NULL,CONCAT(a.fname,' ',a.lname),a.business_name)as agent_name,a.rep_id as agent_display_id,ord.total_premiums,ord.total_policy_holders,ord.total_policies,a.id as agent_id,ord.NewBusinessMember
            FROM customer a
            LEFT JOIN
            (SELECT tran.sponsor_id as s_id,sum(t.credit) as total_premiums,count(DISTINCT tran.cust_id) as total_policy_holders,SUM(tran.total_product) AS total_policies, COUNT(distinct (CASE WHEN t.transaction_type = 'New Order' THEN tran.cust_id END)) as NewBusinessMember
                    FROM transactions as t
                    LEFT JOIN (
                        SELECT o.id, c.sponsor_id, c.id as cust_id, count(od.product_id) as total_product
                        FROM orders as o
                        JOIN order_details od ON (od.order_id = o.id AND od.is_deleted='N')
                        JOIN prd_main p ON(p.id=od.product_id)
                        JOIN customer c ON (c.id=o.customer_id) 
                        WHERE p.is_deleted='N' AND o.payment_type IN('CC','ACH') AND od.product_id NOT IN($fee_products) AND od.product_id NOT IN($enroll_products) GROUP BY o.id
                    ) as tran ON (tran.id = t.order_id)
                WHERE t.transaction_type IN ('New Order') $incr GROUP BY tran.sponsor_id
            ) as ord ON ord.s_id = a.id
            WHERE a.is_deleted='N' GROUP BY a.id having total_premiums > 0 ORDER BY  total_premiums DESC";


    $rows = $pdo->select($sql,$sch_params);
    return $rows;
}

function getRenewalsSummaryDataV1($filter = array("getfromdate" => "", "gettodate" => "")) {
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $enroll_products = get_enrollment_fee_prd_ids('string');
    if($enroll_products!=''){
        $enroll_products.=",347";
    }
    $where_incr = " AND 1 ";

    if($enroll_products!='')
    {
        $where_incr.= "AND od.product_id NOT IN($enroll_products)";
    }
    if($fee_products!='')
    {
        $where_incr.= "AND od.product_id NOT IN($fee_products)";   
    }
    $sch_params = array();
    $incr = "";
    if (strtotime($filter["getfromdate"]) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($filter["getfromdate"]));
        $incr .= " AND DATE(t.created_at) >= :fcreated_at";
    }
    if (strtotime($filter["gettodate"]) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($filter["gettodate"]));
        $incr .= " AND DATE(t.created_at) <= :tcreated_at";
    }

    // SUM(t.credit)  as TotalPremium,
    $sql = "SELECT (SUM(IF(t.transaction_type = 'Renewal Order',t.credit,0)) +  SUM(IF(t.transaction_type = 'Payment Declined',t.debit,0))) as TotalPremium,
                SUM(IF(t.transaction_type = 'Renewal Order',t.credit,0)) AS TotalApprovedPremium,
                SUM(IF(t.transaction_type = 'Payment Declined',t.debit,0)) AS TotalDeclinedPremium,
                COUNT(DISTINCT ord.cust_id) AS TotalPolicyHolder,SUM(ord.total_policies) AS TotalPolicies
            FROM transactions t
            JOIN 
                (SELECT o.id,o.is_renewal,o.payment_type, c.id as cust_id , COUNT(od.product_id) AS total_policies
                        FROM orders o 
                        JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
                        JOIN prd_main p ON(p.id=od.product_id) 
                        JOIN customer c ON (c.id=o.customer_id) 
                        WHERE p.is_deleted = 'N' AND o.payment_type IN('CC','ACH') AND o.is_renewal = 'Y' $where_incr GROUP BY o.id 
                ) as ord ON ord.id = t.order_id 
            WHERE t.transaction_type IN ('Renewal Order','Payment Declined') $incr";


    // $sql = "SELECT sum(od.unit_price*od.qty) as TotalPremium,
    //                 sum(IF(t.transaction_type = 'Renewal Order',(od.unit_price*od.qty),0)) AS TotalApprovedPremium,
    //                 sum(IF(t.transaction_type = 'Payment Declined',(od.unit_price*od.qty),0)) AS TotalDeclinedPremium,
    //                 COUNT(DISTINCT c.id) AS TotalPolicyHolder,COUNT(od.product_id) AS TotalPolicies
    //             FROM transactions t
    //             LEFT JOIN orders as o ON(o.id=t.order_id)
    //             LEFT JOIN order_details od ON(od.order_id = o.id)
    //             LEFT JOIN prd_main p ON(p.id=od.product_id)
    //             LEFT JOIN customer c ON (c.id=o.customer_id)
    //             WHERE p.is_deleted='N' AND od.is_refund = 'N' AND od.product_id NOT IN($fee_products) AND od.product_id NOT IN($enroll_products) AND o.payment_type IN('CC','ACH') AND o.is_renewal = 'Y' AND t.transaction_type IN('Renewal Order','Payment Declined')  AND c.sponsor_id NOT IN(1) $incr";
    //pre_print(array($sql,$sch_params));
    $row = $pdo->selectOne($sql, $sch_params);

    if($row["TotalPremium"] > 0) {
        $AvgPremiumPerHolder = $row["TotalPremium"] / $row["TotalPolicyHolder"];
    } else {
        $AvgPremiumPerHolder = 0;
    }

    if($row["TotalPolicies"] > 0) {
        $AvgPoliciesPerHolder = $row["TotalPolicies"] / $row["TotalPolicyHolder"];
    } else {
        $AvgPoliciesPerHolder = 0;
    }

    if($row["TotalApprovedPremium"] > 0) {
        $PerCollectedPremium = (float) $row["TotalApprovedPremium"] / $row["TotalPremium"] * 100;
    } else {
        $PerCollectedPremium = 0;
    }
    
    //pre_print($PerCollectedPremium);
    return array(
        'TotalPremium' => $row["TotalPremium"],
        'TotalApprovedPremium' => $row["TotalApprovedPremium"],
        'TotalDeclinedPremium' => $row["TotalDeclinedPremium"],
        'PerCollectedPremium' => number_format((float)$PerCollectedPremium, 2, '.', ''),
        'AvgPremiumPerHolder' => $AvgPremiumPerHolder,
        'TotalPolicyHolder' => $row["TotalPolicyHolder"],
        'TotalPolicies' => $row["TotalPolicies"],
        'AvgPoliciesPerHolder' => number_format((float)$AvgPoliciesPerHolder, 2, '.', ''),
    );
}

function renewals_business_summary_per_productsV1($searchArray = array()) {
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $enroll_products = get_enrollment_fee_prd_ids('string');
    $incr = '';
    $sch_params = array();

    if (strtotime($searchArray["getfromdate"]) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($searchArray["getfromdate"]));
        $incr .= " AND DATE(t.created_at) >= :fcreated_at";
    }
    if (strtotime($searchArray["gettodate"]) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($searchArray["gettodate"]));
        $incr .= " AND DATE(t.created_at) <= :tcreated_at";
    }

    // $sql = "SELECT p.id as product_id,p.name as product_name,ord.total_premiums,ord.total_approved_premiums,ord.total_declined_premiums,p.product_code
    //         FROM prd_main p
    //         LEFT JOIN
    //             (SELECT od.product_id,
    //                 SUM(od.unit_price*od.qty) as total_premiums,
    //                 SUM(IF(t.transaction_type = 'Renewal Order',od.unit_price*od.qty,0)) as total_approved_premiums,
    //                 SUM(IF(t.transaction_type = 'Payment Declined',od.unit_price*od.qty,0)) as total_declined_premiums
    //             FROM transactions t
    //             LEFT JOIN orders as o ON(o.id=t.order_id)
    //             LEFT JOIN order_details od ON(o.id=od.order_id)
    //             LEFT JOIN customer c ON (c.id=o.customer_id)
    //             WHERE od.product_id NOT IN($fee_products) AND od.product_id NOT IN($enroll_products) AND od.is_refund = 'N' AND o.is_renewal='Y' $incr AND o.payment_type IN('CC','ACH') AND t.transaction_type IN('Renewal Order','Payment Declined')  AND c.sponsor_id NOT IN(1)
    //             GROUP BY od.product_id
    //         ) as ord ON ord.product_id=p.id
    //     WHERE p.is_deleted='N' GROUP BY p.id having total_premiums > 0 ORDER BY total_premiums DESC";

    $sql = "SELECT p.id as product_id,p.name as product_name,ord.total_premiums,p.product_code,ord.total_approved_premiums as total_approved_premiums ,ord.total_declined_premiums
        FROM prd_main p
        LEFT JOIN
        (SELECT tran.product_id as p_id,(SUM(IF(t.transaction_type = 'Payment Declined',t.debit,0)) + SUM(IF(t.transaction_type = 'Renewal Order',t.credit,0))) as total_premiums,SUM(IF(t.transaction_type = 'Renewal Order',t.credit,0)) as total_approved_premiums,SUM(IF(t.transaction_type = 'Payment Declined',t.debit,0)) as total_declined_premiums
                FROM transactions as t
                LEFT JOIN (
                    SELECT o.id, o.is_renewal, p.id as product_id, c.id as cust_id, count(od.product_id) as total_product
                    FROM orders as o
                    JOIN order_details od ON (od.order_id = o.id AND od.is_deleted='N')
                    JOIN prd_main p ON(p.id=od.product_id)
                    JOIN customer c ON (c.id=o.customer_id) 
                    WHERE p.is_deleted='N' AND o.payment_type IN('CC','ACH') AND od.product_id NOT IN($fee_products) AND od.product_id NOT IN($enroll_products) GROUP BY o.id
                ) as tran ON (tran.id = t.order_id)
            WHERE t.transaction_type IN ('Renewal Order','Payment Declined') $incr GROUP BY tran.product_id
        ) as ord ON ord.p_id = p.id
        WHERE p.is_deleted='N' GROUP BY p.id having total_premiums > 0 ORDER BY total_premiums DESC";


    $rows = $pdo->select($sql,$sch_params);
    return $rows;
}

function renewals_business_summary_per_agentsV1($searchArray = array()) {
    global $pdo;
    $fee_products = get_enrollment_with_associate_fee_prd_ids("string");
    $enroll_products = get_enrollment_fee_prd_ids('string');
    $incr = '';
    $sch_params = array();

    // $incr .= " AND o.is_renewal='Y'";

    if (strtotime($searchArray["getfromdate"]) > 0) {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($searchArray["getfromdate"]));
        $incr .= " AND DATE(t.created_at) >= :fcreated_at";
    }
    if (strtotime($searchArray["gettodate"]) > 0) {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($searchArray["gettodate"]));
        $incr .= " AND DATE(t.created_at) <= :tcreated_at";
    }

    // $sql = "SELECT a.id as agent_id,IF(a.business_name = '' OR a.business_name IS NULL,CONCAT(a.fname,' ',a.lname),a.business_name)as agent_name,a.rep_id as agent_display_id,ord.total_premiums,ord.total_approved_premiums,ord.total_declined_premiums
    //         FROM customer a
    //         LEFT JOIN
    //             (SELECT c.sponsor_id,
    //                 SUM(od.unit_price*od.qty) as total_premiums,
    //                 SUM(IF(t.transaction_type = 'Renewal Order',(od.unit_price*od.qty),0)) AS total_approved_premiums,
    //                 SUM(IF(t.transaction_type = 'Payment Declined',(od.unit_price*od.qty),0)) AS total_declined_premiums
    //                 FROM transactions as t 
    //                 LEFT JOIN orders as o ON(o.id=t.order_id)
    //                 LEFT JOIN order_details od ON(o.id=od.order_id)
    //                 LEFT JOIN prd_main p ON(p.id=od.product_id)
    //                 LEFT JOIN customer c ON (c.id=o.customer_id AND c.sponsor_id NOT IN(1))
    //                 WHERE p.is_deleted='N' AND od.is_refund = 'N' AND od.product_id NOT IN($fee_products) AND od.product_id NOT IN($enroll_products) $incr AND o.payment_type IN('CC','ACH') AND t.transaction_type IN('Renewal Order','Payment Declined')
    //                 GROUP BY c.sponsor_id
    //             ) as ord ON ord.sponsor_id=a.id
    //         WHERE a.is_deleted='N' AND a.id NOT IN (1) GROUP BY a.id having total_premiums > 0 ORDER BY total_premiums DESC";
    
    $sql = "SELECT IF(a.business_name = '' OR a.business_name IS NULL,CONCAT(a.fname,' ',a.lname),a.business_name)as agent_name,a.rep_id as agent_display_id,ord.total_premiums,a.id as agent_id,ord.total_approved_premiums, ord.total_declined_premiums
        FROM customer a
        LEFT JOIN
        (SELECT tran.sponsor_id as s_id,(SUM(IF(t.transaction_type = 'Payment Declined',t.debit,0)) + SUM(IF(t.transaction_type = 'Renewal Order',t.credit,0))) as total_premiums, SUM(IF(t.transaction_type = 'Renewal Order',t.credit,0)) as total_approved_premiums, SUM(IF(t.transaction_type = 'Payment Declined',t.debit,0)) as total_declined_premiums
                FROM transactions as t
                LEFT JOIN (
                    SELECT o.id, o.is_renewal, c.sponsor_id, c.id as cust_id, count(od.product_id) as total_product
                    FROM orders as o
                    JOIN order_details od ON (od.order_id = o.id AND od.is_deleted='N')
                    JOIN prd_main p ON(p.id=od.product_id)
                    JOIN customer c ON (c.id=o.customer_id) 
                    WHERE p.is_deleted='N' AND o.payment_type IN('CC','ACH') AND od.product_id NOT IN($fee_products) AND od.product_id NOT IN($enroll_products) GROUP BY o.id
                ) as tran ON (tran.id = t.order_id)
            WHERE t.transaction_type IN ('Renewal Order', 'Payment Declined') $incr GROUP BY tran.sponsor_id
        ) as ord ON ord.s_id = a.id
        WHERE a.is_deleted='N' GROUP BY a.id having total_premiums > 0 ORDER BY total_premiums DESC";

    $rows = $pdo->select($sql,$sch_params);
    return $rows;
}

//  reporting funcation based on transactions table End
//get commission for agent
function get_agent_renewal_commission($sponsor_id,$order_id, $product_id, $parent_product_id, $sponsor_type,$total_discount,$qty,$unit_price){
	$CODED_ARR = array();
	global $pdo;
	$fee_prds_ids = get_enrollment_fee_prd_ids();
	$codedSql = "select * FROM agent_coded_level";
	$codedRow = $pdo->select($codedSql);
	if ($sponsor_type == 'Agent') {
		// $sponsor_id = $order['sponsor_id'];
		$paylevels = getAgentPayLevels($sponsor_id);
		$tmp_paylevels = $paylevels;
		if(in_array($product_id,$fee_prds_ids)) {
				$is_found_agent = false;
				foreach ($tmp_paylevels as $key => $value) {
						if($is_found_agent == false && $value > 0) {
								$paylevels[$key] = $value;
								$is_found_agent = true;
						} else {
								$paylevels[$key] = 0;
						}
				}
		}
		foreach ($codedRow as $cr) {
				$CODED_ARR[$cr['id']] = $cr['level'];
		}
 		}

	if (count($paylevels) > 0) {
		// pre_print("1 . ".$paylevels,false);
	$comm_rules = getCommRules($sponsor_id, $order_id, $product_id, $parent_product_id, $sponsor_type);
	// pre_print($comm_rules, false);

	$total_prec_paid = 0;
	// pre_print('com_rukle'.$comm_rules,false);
	if (!empty($comm_rules)) {
		foreach ($paylevels as $level => $agent_id) {
			$amount = 0;
			$percentage = 0;
			$original_percentage = 0;
			//pre_print($comm_rules);
			if ($comm_rules['commission_on'] == 'Product') {
				$comm_amount_arr = json_decode($comm_rules['commission_level_json'], true);
			} else {
				$comm_amount_arr = json_decode($comm_rules['commission_plan_level_json'], true);
				$comm_amount_arr = $comm_amount_arr[$plan_id];
			}
			//pre_print($comm_amount_arr[$CODED_ARR[$level]]);
			// pre_print($comm_amount_arr[$CODED_ARR[$level]], false);

			if (!isset($comm_amount_arr[$CODED_ARR[$level]]) || $comm_amount_arr[$CODED_ARR[$level]] == 0 || $agent_id <= 1) {
				continue;
			}
			if (count($comm_amount_arr) > 0) {
				// pre_print($comm_amount_arr,false);
				// pre_print("no commission",false);
				if ($comm_amount_arr[$CODED_ARR[$level]]['amount_type'] == 'Percentage') {
					// echo "<br>Per : " . $original_percentage = $comm_amount_arr[$CODED_ARR[$level]]['amount'];
					$percentage = $original_percentage - $total_prec_paid;
					$total_prec_paid = $total_prec_paid + $percentage;
					$order_total = ($unit_price * $qty) - $total_discount;
					// pre_print('if.'.$amount,false);
					 return $amount = ($order_total) * ($percentage / 100);
				} else {
					// pre_print('else'.$qty * $comm_amount_arr[$CODED_ARR[$level]]['amount'],false);
					return $amount = ($qty * $comm_amount_arr[$CODED_ARR[$level]]['amount']);
					}
				}
			}
		}else{
			// pre_print('no commission',false);
		}
	}
}

function getCommRules($sponsor_id, $order_id, $product_id, $parent_product_id = 0, $sponsor_type = '') {
  global $pdo,$Rpdo;
  $res_flag = 'N';
  if ($sponsor_type == '') {
    return array();
  }
  /*   * ************************************************************** */
  //selecting commission rule assigned to agent/sponsor
	$fee_prds_ids = get_enrollment_fee_prd_ids();
	// pre_print($fee_prds_ids,false);
  if(in_array($product_id,$fee_prds_ids)) {
      $prd_Sql = "SELECT c.* FROM commission_rule c WHERE c.product_id=:product_id LIMIT 1";
      $prd_row = $pdo->selectOne($prd_Sql, array(":product_id" => $product_id));
  } else {
      $prd_Sql = "SELECT c.* FROM commission_rule c
      JOIN agent_commission_rule r ON r.commission_rule_id=c.id
      WHERE r.agent_id=:sponsor_id AND r.product_id=:product_id
      ORDER BY r.id DESC LIMIT 1";  
      $prd_row = $pdo->selectOne($prd_Sql, array(':sponsor_id' => $sponsor_id, ":product_id" => $product_id));
  }  
  

  if ($prd_row) {
    $res_flag = 'Y';
  }
  /*   * ************************************************************** */

  if (count($prd_row) > 0) {
    if ($prd_row['duration_commission'] == 'Indefinitely' && $res_flag == 'Y') {
      return $prd_row;
    } else if ($prd_row['duration_commission'] == 'Change Commission' && $res_flag == 'Y') {
      $subscription_sql = "SELECT w.renew_count FROM website_subscriptions_history wh
                                JOIN website_subscriptions w ON(w.id = wh.website_id)
                                WHERE wh.order_id = :order_id";
      $where_order_id = array(':order_id' => $order_id);
      $subscription_res = $pdo->selectOne($subscription_sql, $where_order_id);
      $renewCount = $subscription_res['renew_count'];
      if ($subscription_res && $renewCount > 0) {
        $range_sql = "SELECT * FROM commission_rule_range WHERE commission_rule_id = :commission_rule_id AND (from_renewal<=$renewCount AND to_renewal>$renewCount)";
        $where_rule_id = array(':commission_rule_id' => $prd_row['id']);
        $range_res = $Rpdo->selectOne($range_sql, $where_rule_id);
        if ($range_res && $subscription_res) {
          if ($range_res['from_renewal'] >= $subscription_res['renew_count']) {
            // pre_print($prd_row);
            return $prd_row;
          } else {
            $prd_row['commission_level_json'] = $range_res['range_level_json'];
            $prd_row['commission_plan_level_json'] = $range_res['plan_range_level_json'];
            $prd_row['commission_on'] = $range_res['commission_on'];
            // pre_print($prd_row);
            return $prd_row;
          }
        }else {
          return $prd_row;
        }
      } else {
        return $prd_row;
      }
    } else if ($prd_row['duration_commission'] == 'Stop Paying' && $res_flag == 'Y') {

      $range_sql = "SELECT * FROM commission_rule_range WHERE commission_rule_id = :commission_rule_id";
      $where_rule_id = array(':commission_rule_id' => $prd_row['id']);
      $range_res = $Rpdo->selectOne($range_sql, $where_rule_id);
      $subscription_sql = "SELECT w.renew_count FROM website_subscriptions_history wh
                                JOIN website_subscriptions w ON(w.id = wh.website_id)
                                WHERE wh.order_id = :order_id";
      $where_order_id = array(':order_id' => $order_id);
      $subscription_res = $pdo->selectOne($subscription_sql, $where_order_id);

      //pre_print($range_res);
      if ($range_res && $subscription_res) {
        if ($range_res['from_renewal'] >= $subscription_res['renew_count']) {
          return $prd_row;
        } else {
          return array();
        }
      }
    } else {
      return array();
    }
  } else {
    return array();
  }
}
function getAgentPayLevels($agent_id, $levelArr = array()) {
  global $pdo,$Rpdo;

  if (count($levelArr) == 0) {
    $levelArr = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0);
  }

  $selSql = "SELECT id,agent_coded_id,type,sponsor_id
    FROM customer WHERE id=:id and status='Active'";
  $Row = $pdo->selectOne($selSql, array(":id" => $agent_id));

  if ($Row) {
    if ($Row['type'] == 'Agent') {
      if ($Row['agent_coded_id'] == 1 && $levelArr[1] == 0) {
        $levelArr[1] = $Row['id'];
      }
      if ($Row['agent_coded_id'] == 2 && $levelArr[2] == 0) {
        $levelArr[2] = $Row['id'];
      }
      if ($Row['agent_coded_id'] == 3 && $levelArr[3] == 0) {
        $levelArr[3] = $Row['id'];
      }
      if ($Row['agent_coded_id'] == 4 && $levelArr[4] == 0) {
        $levelArr[4] = $Row['id'];
      }
      if ($Row['agent_coded_id'] == 5 && $levelArr[5] == 0) {
        $levelArr[5] = $Row['id'];
      }
      if ($Row['agent_coded_id'] == 6 && $levelArr[6] == 0) {
        $levelArr[6] = $Row['id'];
      }
      if ($Row['agent_coded_id'] == 7 && $levelArr[7] == 0) {
        $levelArr[7] = $Row['id'];
      }
      if ($Row['agent_coded_id'] == 8 && $levelArr[8] == 0) {
        $levelArr[8] = $Row['id'];
      }
      if ($Row['agent_coded_id'] == 9 && $levelArr[9] == 0) {
        $levelArr[9] = $Row['id'];
      }
      if ($Row['agent_coded_id'] == 10 && $levelArr[10] == 0) {
        $levelArr[10] = $Row['id'];
      }
    }
    if ($levelArr[10] == 0) {
      return getAgentPayLevels($Row['sponsor_id'], $levelArr);
    }
    return $levelArr;
  } else {
    return $levelArr;
  }
}
//get commission for agent