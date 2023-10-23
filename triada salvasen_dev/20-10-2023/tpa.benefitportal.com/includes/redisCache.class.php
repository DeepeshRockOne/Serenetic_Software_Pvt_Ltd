<?php
include dirname(__DIR__) . '/libs/predis-main/autoload.php';
Predis\Autoloader::register();
class redisCache {
  
    function __construct() {
        global $REDIS_URL,$REDIS_PORT;
        
        $this->redis = new Predis\Client(array(
            "host" => $REDIS_URL,
            "port" => $REDIS_PORT,
        ));
    }

    public function checkCacheExist($id,$type){
        global $pdo,$SITE_ENV,$CACHE_SITE_NAME;

        if(empty($id) || empty($type)){
            return $this->failResponse("Required Data Missing");
        }else{
            $redis = $this->redis;

            if($redis->exists($CACHE_SITE_NAME .'_'. $SITE_ENV . '_' . $type . '_' . $id)) {            
                return true;
            }else{
                return false;
            }
        }
    }

    public function generateCache($id,$type){
        global $pdo,$SITE_ENV,$CACHE_SITE_NAME;
        
        $redis = $this->redis;
        if(empty($id) || empty($type)){
            return $this->failResponse("Required Data Missing");
        }else{
            $cacheExist = $this->checkCacheExist($id,$type);
            
            

            if($cacheExist){
                $json = $this->getGeneratedCache($id,$type);
            }else{
                $data = [];

                try {
                    if($type == "Customer"){
                        $incr = "";
                        $whrParam = array();
                        if($id != "All"){
                            $incr .= " AND rep_id =:id";
                            $whrParam[":id"] = $id;
                        }

                        $query = "SELECT id,fname,lname FROM customer where is_deleted = 'N' $incr";
                        $result= $pdo->select($query,$whrParam);
                        
                    }else if($type == "Product"){
                        $incr = "";
                        $whrParam = array();
                        if($id != "All"){
                            $incr .= " AND p.id =:id";
                            $whrParam[":id"] = $id;
                        }
                        $query = "SELECT p.id,p.name as productName,p.product_code,
                            pd.product_id,pd.agent_portal 
                            FROM prd_descriptions pd 
                            JOIN prd_main p ON(p.id = pd.product_id)
                             WHERE p.is_deleted = 'N' $incr";
                        $result= $pdo->select($query,$whrParam);
                    }else if($type == 'sales_bar_chart_data_admin'){
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
                        $incr = "";
                        $where_incr = " AND 1 ";

                        $upline_incr = "";

                        $amt_column = "(t.new_business_total + t.renewal_total)";

                        $sql = "SELECT
                        SUM(IF((t.transaction_type = 'New Order' OR t.transaction_type = 'List Bill Order'),t.new_business_total,0)) AS new_business_sales_amt,
                        SUM(IF((t.transaction_type = 'Renewal Order' OR t.transaction_type = 'List Bill Order'),t.renewal_total,0)) AS renewal_sales_amt,
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

                        $curr_year_sales = array();
                        $pre_year_sales = array();
                        foreach ($curr_year as $key => $value) {
                            $curr_year_sales[] = array(
                                "year" => date('Y',strtotime($curr_year[$key]["month"])),
                                "name" => date('F',strtotime($curr_year[$key]["month"])),
                                "y" => '',
                                "gross_sales_temp_amt" => (int) $curr_year[$key]["gross_sales_amt"],
                                "net_sales_temp_amt" => (int) $curr_year[$key]["net_sales_amt"],
                                "new_business_sales_amt" => displayAmount($curr_year[$key]["new_business_sales_amt"]),
                                "renewal_sales_amt" => displayAmount($curr_year[$key]["renewal_sales_amt"]),
                                "gross_sales_amt" => displayAmount($curr_year[$key]["gross_sales_amt"]),
                                "reversals_amt" => displayAmount($curr_year[$key]["reversals_amt"]),
                                "net_sales_amt" => displayAmount($curr_year[$key]["net_sales_amt"]),
                            );

                            $pre_year_sales[] = array(
                                "year" => date('Y',strtotime($prev_year[$key]["month"])),
                                "name" => date('F',strtotime($prev_year[$key]["month"])),
                                "y" => '',
                                "gross_sales_temp_amt" => (int) $prev_year[$key]["gross_sales_amt"],
                                "net_sales_temp_amt" => (int) $prev_year[$key]["net_sales_amt"],
                                "new_business_sales_amt" => displayAmount($prev_year[$key]["new_business_sales_amt"]),
                                "renewal_sales_amt" => displayAmount($prev_year[$key]["renewal_sales_amt"]),
                                "gross_sales_amt" => displayAmount($prev_year[$key]["gross_sales_amt"]),
                                "reversals_amt" => displayAmount($prev_year[$key]["reversals_amt"]),
                                "net_sales_amt" => displayAmount($prev_year[$key]["net_sales_amt"]),
                            );
                        }
                        $result = array('curr_year_sales' => $curr_year_sales,'pre_year_sales' => $pre_year_sales,'months' => $months);
                    }else if($type == 'sales_bar_chart_data_new_business'){
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
                        $incr = "";
                        $where_incr = " AND 1 ";

                        $where_incr .= " AND (o.is_renewal='N' OR (o.is_renewal='L' AND od.is_renewal='N'))";
                        $amt_column = "(t.new_business_total)";

                        $sql = "SELECT
                        SUM(IF((t.transaction_type = 'New Order' OR t.transaction_type = 'List Bill Order'),t.new_business_total,0)) AS new_business_sales_amt,
                        0.00 AS renewal_sales_amt,
                        SUM(IF(t.transaction_type = 'Renewal Order' OR t.transaction_type = 'New Order' OR t.transaction_type = 'List Bill Order',$amt_column,0)) AS gross_sales_amt,
                        SUM(IF(t.transaction_type = 'Refund Order' OR t.transaction_type = 'Chargeback' OR t.transaction_type = 'Void Order',$amt_column,0)) AS reversals_amt,
                        SUM(IF(t.transaction_type = 'Renewal Order' OR t.transaction_type = 'New Order' OR t.transaction_type = 'List Bill Order',$amt_column,($amt_column * -1))) AS net_sales_amt,
                        DATE_FORMAT(t.created_at,'%m')as o_month,YEAR(t.created_at)as o_year,DATE_FORMAT(t.created_at,'%d')as o_day
                        FROM transactions t
                        JOIN
                        (
                            SELECT o.id,COUNT(od.product_id) AS total_policies
                            FROM orders o
                            JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
                            WHERE 1 $where_incr GROUP BY o.id
                        ) as ord ON ord.id = t.order_id
                        WHERE ord.id IS NOT NULL AND t.transaction_type IN ('New Order','Renewal Order','List Bill Order','Refund Order','Chargeback','Void Order') $incr
                        GROUP BY YEAR(t.created_at),MONTH(t.created_at)";

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

                        $curr_year_sales = array();
                        $pre_year_sales = array();
                        foreach ($curr_year as $key => $value) {
                            $curr_year_sales[] = array(
                                "year" => date('Y',strtotime($curr_year[$key]["month"])),
                                "name" => date('F',strtotime($curr_year[$key]["month"])),
                                "y" => '',
                                "gross_sales_temp_amt" => (int) $curr_year[$key]["gross_sales_amt"],
                                "net_sales_temp_amt" => (int) $curr_year[$key]["net_sales_amt"],
                                "new_business_sales_amt" => displayAmount($curr_year[$key]["new_business_sales_amt"]),
                                "renewal_sales_amt" => displayAmount($curr_year[$key]["renewal_sales_amt"]),
                                "gross_sales_amt" => displayAmount($curr_year[$key]["gross_sales_amt"]),
                                "reversals_amt" => displayAmount($curr_year[$key]["reversals_amt"]),
                                "net_sales_amt" => displayAmount($curr_year[$key]["net_sales_amt"]),
                            );

                            $pre_year_sales[] = array(
                                "year" => date('Y',strtotime($prev_year[$key]["month"])),
                                "name" => date('F',strtotime($prev_year[$key]["month"])),
                                "y" => '',
                                "gross_sales_temp_amt" => (int) $prev_year[$key]["gross_sales_amt"],
                                "net_sales_temp_amt" => (int) $prev_year[$key]["net_sales_amt"],
                                "new_business_sales_amt" => displayAmount($prev_year[$key]["new_business_sales_amt"]),
                                "renewal_sales_amt" => displayAmount($prev_year[$key]["renewal_sales_amt"]),
                                "gross_sales_amt" => displayAmount($prev_year[$key]["gross_sales_amt"]),
                                "reversals_amt" => displayAmount($prev_year[$key]["reversals_amt"]),
                                "net_sales_amt" => displayAmount($prev_year[$key]["net_sales_amt"]),
                            );
                        }
                        $result = array('curr_year_sales' => $curr_year_sales,'pre_year_sales' => $pre_year_sales,'months' => $months);
                    }else if($type == 'sales_bar_chart_data_renewal'){
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
                        $incr = "";
                        $where_incr = " AND 1 ";

                        $where_incr .= " AND (o.is_renewal='Y' OR (o.is_renewal='L' AND od.is_renewal='Y'))";
                        $amt_column = "(t.renewal_total)";

                        $sql = "SELECT
                        0.00 AS new_business_sales_amt,
                        SUM(IF((t.transaction_type = 'Renewal Order' OR t.transaction_type = 'List Bill Order'),t.renewal_total,0)) AS renewal_sales_amt,
                        SUM(IF(t.transaction_type = 'Renewal Order' OR t.transaction_type = 'New Order' OR t.transaction_type = 'List Bill Order',$amt_column,0)) AS gross_sales_amt,
                        SUM(IF(t.transaction_type = 'Refund Order' OR t.transaction_type = 'Chargeback' OR t.transaction_type = 'Void Order',$amt_column,0)) AS reversals_amt,
                        SUM(IF(t.transaction_type = 'Renewal Order' OR t.transaction_type = 'New Order' OR t.transaction_type = 'List Bill Order',$amt_column,($amt_column * -1))) AS net_sales_amt,
                        DATE_FORMAT(t.created_at,'%m')as o_month,YEAR(t.created_at)as o_year,DATE_FORMAT(t.created_at,'%d')as o_day
                        FROM transactions t
                        JOIN
                        (
                            SELECT o.id,COUNT(od.product_id) AS total_policies
                            FROM orders o
                            JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
                            WHERE 1 $where_incr GROUP BY o.id
                        ) as ord ON ord.id = t.order_id
                        WHERE ord.id IS NOT NULL AND t.transaction_type IN ('New Order','Renewal Order','List Bill Order','Refund Order','Chargeback','Void Order') $incr
                        GROUP BY YEAR(t.created_at),MONTH(t.created_at)";

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

                        $curr_year_sales = array();
                        $pre_year_sales = array();
                        foreach ($curr_year as $key => $value) {
                            $curr_year_sales[] = array(
                                "year" => date('Y',strtotime($curr_year[$key]["month"])),
                                "name" => date('F',strtotime($curr_year[$key]["month"])),
                                "y" => '',
                                "gross_sales_temp_amt" => (int) $curr_year[$key]["gross_sales_amt"],
                                "net_sales_temp_amt" => (int) $curr_year[$key]["net_sales_amt"],
                                "new_business_sales_amt" => displayAmount($curr_year[$key]["new_business_sales_amt"]),
                                "renewal_sales_amt" => displayAmount($curr_year[$key]["renewal_sales_amt"]),
                                "gross_sales_amt" => displayAmount($curr_year[$key]["gross_sales_amt"]),
                                "reversals_amt" => displayAmount($curr_year[$key]["reversals_amt"]),
                                "net_sales_amt" => displayAmount($curr_year[$key]["net_sales_amt"]),
                            );

                            $pre_year_sales[] = array(
                                "year" => date('Y',strtotime($prev_year[$key]["month"])),
                                "name" => date('F',strtotime($prev_year[$key]["month"])),
                                "y" => '',
                                "gross_sales_temp_amt" => (int) $prev_year[$key]["gross_sales_amt"],
                                "net_sales_temp_amt" => (int) $prev_year[$key]["net_sales_amt"],
                                "new_business_sales_amt" => displayAmount($prev_year[$key]["new_business_sales_amt"]),
                                "renewal_sales_amt" => displayAmount($prev_year[$key]["renewal_sales_amt"]),
                                "gross_sales_amt" => displayAmount($prev_year[$key]["gross_sales_amt"]),
                                "reversals_amt" => displayAmount($prev_year[$key]["reversals_amt"]),
                                "net_sales_amt" => displayAmount($prev_year[$key]["net_sales_amt"]),
                            );
                        }
                        $result = array('curr_year_sales' => $curr_year_sales,'pre_year_sales' => $pre_year_sales,'months' => $months);
                    }else if($type == 'renewal_summary_admin'){
                        $upline_incr = "";
                        if($type == 'renewal_summary_agent') {
                            $upline_incr = " AND c.upline_sponsors LIKE CONCAT('%,',".$_SESSION['agents']['id'].",',%') AND c.type='Customer'";
                        }

                        // Remaining Renewals : This is the amount and number of renewal transactions that remain from the tomorrow to end of month.
                        $remaining_renewals_amt = 0;
                        $remaining_renewals_trans = 0;
                        $today_date = date("Y-m-d");
                        $month_start_date = date('Y-m-01',strtotime($today_date));
                        $month_end_date = date("Y-m-t",strtotime($today_date));
                        $three_month_ago_date = date('Y-m-d',strtotime('-3 months',strtotime($month_start_date)));

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
                        if($type == "renewal_summary_agent") {
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

                        $result = array(
                            'remaining_renewals_amt' => displayAmount($remaining_renewals_amt),
                            'remaining_renewals_trans' => round($remaining_renewals_trans),
                            'avg_daily_collection' => displaypercentage($avg_daily_collection),
                            'ren_proj_collection_amt' => displayAmount($ren_proj_collection_amt),
                            'ren_proj_collection_trans' => round($ren_proj_collection_trans),
                            'ren_proj_monthly_total_amt' => displayAmount($ren_proj_monthly_total_amt),
                            'ren_proj_monthly_total_trans' => round($ren_proj_monthly_total_trans),
                        );
                        
                    }
                    
                    if(!empty($result)) {
                        if($type == 'sales_bar_chart_data_admin' ||$type == 'sales_bar_chart_data_new_business'||$type == 'sales_bar_chart_data_renewal'|| $type == 'renewal_summary_admin'){
                            $cacheData =  json_encode($result);
                        }else{
                            foreach($result as $key=>$row){
                                $product_detail_info = displayAgentPortalDescriptionInfo($row['id'],'N');
                                $row['agent_portal'] = $product_detail_info."</br>".$row['agent_portal'];
                                if(!isset($data[$row['id']])){
                                    $data[$row['id']] = array();
                                }
                                $data[$row['id']] = $row;
                            }

                            $cacheData =  json_encode($data);
                        }
                        $redis->set($CACHE_SITE_NAME .'_'. $SITE_ENV . '_' . $type . '_' . $id, $cacheData);
                        
                        return $this->successResponse($data,'');
                    }else{
                        return $this->failResponse("No Data Found");
                    }
                } catch (Exception $e){
                    return $this->failResponse($e->getMessage());
                }
            }
        }
        return $json;
    }

    public function getGeneratedCache($id,$type){
        global $pdo,$SITE_ENV,$CACHE_SITE_NAME;

        if(empty($id) || empty($type)){
            $this->failResponse("Required Data Missing");
        }else{
            $redis = $this->redis;

            $response = $redis->get($CACHE_SITE_NAME .'_'. $SITE_ENV . '_' . $type . '_' . $id);
            return $response;
        }
    }

    public function getAllGeneratedCache(){
        global $pdo,$SITE_ENV,$CACHE_SITE_NAME;

        $redis = $this->redis;

        $response = array();
        $keys = $redis->keys('*');
        $response["Keys"] = $keys;
        if(!empty($keys)){
            foreach($keys as $k => $redisCache){
                $tmpData = json_decode($redis->get($redisCache),true);
                $totalCache = count($tmpData);

                if(!isset($response[$redisCache])){
                    $response[$redisCache] = array();
                }
                array_push($response[$redisCache],$tmpData);
                $response[$redisCache]['TotalCacheData'] = $totalCache;
            }
        }
        return json_encode($response);
    }

    public function deleteGeneratedCache($cacheID,$type){
        global $pdo,$SITE_ENV,$CACHE_SITE_NAME;

        $redis = $this->redis;
        $message = "";
        if(empty($cacheID)){
            $message = "Cache Clear";
            $status = $redis->flushAll();
        }else{
            $message = $cacheID ." Delte from cache";
            $status = $redis->del($CACHE_SITE_NAME .'_'. $SITE_ENV . '_' . $type . '_' . $cacheID);
        }

        return $this->successResponse('',$message);
    }

    public function successResponse($data,$message){
        $responseMessage = !empty($message) ? $message : 'Data added in cache';
        $responseData = !empty($data) ? $data : array();

        $response = [];
        $response['status'] = "success";
        $response['code'] = 200;
        $response['message'] = $message;
        $response['data'] = $responseData;

        return json_encode($response);
    }

    public function failResponse($message){
        $responseMessage = !empty($message) ? $message : '';
        
        $response = [];
        $response['status'] = "fail";
        $response['code'] = 201;
        $response['message'] = $responseMessage;
        $response['data'] = array();

        return json_encode($response);
    }

    public function getOrGenerateCache($id,$type,$action='get'){
        $cacheExists = $this->checkCacheExist($id,$type); 
        $productRedisCacheMainArray = array();

        if($action === 'get'){ 
            if($cacheExists){
                $response = $this->getGeneratedCache($id,$type);
                $productRedisCacheMainArray = json_decode($response,true);
            }else{
                $response = $this->generateCache($id,$type);
                $response_data = json_decode($response,true);
                $productRedisCacheMainArray = $response_data['data'];
            }
            return $productRedisCacheMainArray;
            
        }else if($action === 'add'){
            if($cacheExists){
                $response_cache = $this->deleteGeneratedCache($id,$type);
                $res = json_decode($response_cache,true);
                if($res['status'] == 'success'){
                    $this->generateCache($id,$type);
                }
            }else{
                $this->generateCache($id,$type);
            }
            return true;
        }
    }
}
?>