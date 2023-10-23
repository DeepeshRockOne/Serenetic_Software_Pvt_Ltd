<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
agent_has_access(10);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Book of Business';
$breadcrumbes[2]['title'] = 'Leads';
$breadcrumbes[2]['link'] = 'lead_details.php';
$tz = new UserTimeZone('m/d/Y @ g:i A T', $_SESSION['agents']['timezone']);
$cacheMainArray = unserialize(file_get_contents($CACHE_PATH_DIR.$CACHE_FILE_NAME));
$prdPlanTypeArray = $cacheMainArray['prdPlanTypeArray'];

$id = checkIsset($_GET['id']);
$lead_id = $id;
$user_type = "Lead";
$allow_edit = true;
$sponsor_incr = "";

if(!empty($_SESSION['agents']['id'])){
    $displayDirectEnroll = !empty($_SESSION['agents']['displayDirectEnroll']) ? explode(",", $_SESSION['agents']['displayDirectEnroll']) : array();
    if(!empty($displayDirectEnroll) && in_array('Leads', $displayDirectEnroll)){
        $sponsor_agent_ids = get_direct_loa_agents($_SESSION['agents']['id'],true);
    } else {
        $sponsor_agent_ids = get_downline_agents($_SESSION['agents']['id'],true);
    }

    $sponsor_incr .= " AND l.sponsor_id IN($sponsor_agent_ids)";
}

$select = "SELECT md5(l.id) as id,l.*,c.status as customer_status,cs.agent_coded_level
            FROM leads l
            JOIN customer s on(s.id = l.sponsor_id)
            LEFT JOIN customer_settings cs ON(s.id=cs.customer_id)
            LEFT JOIN customer c on(c.id = l.customer_id)
            WHERE md5(l.id)=:id AND l.is_deleted = 'N' " . $sponsor_incr;
$where = array(':id' => makeSafe($lead_id));
$row = $pdo->selectOne($select, $where);

$age = !empty($row['birth_date']) ? ' ('.calculateAge($row['birth_date']).')' : '';
$customer_id = !empty($row['customer_id']) ? $row['customer_id'] : '';

if (empty($row)) {
    setNotifyError('Lead does not exist');
    redirect("lead_listing.php");
} else {
    if (!isset($_GET['pages'])) {
        $note_search_keyword = isset($_REQUEST['note_search_keyword']) ? $_REQUEST['note_search_keyword'] : '';
        $note_search_keyword = cleanSearchKeyword($note_search_keyword);  
        $note_ajax = isset($_POST['note_ajax']) ? $_POST['note_ajax'] : "";
        $extra_params = array();
        $extra_params['note_search_keyword'] = $note_search_keyword;
        $notes_res = get_note_section_data('agent',$lead_id,'lead',$extra_params);

        if ($note_search_keyword !== '' || $note_ajax == 'Y') {
            $note_desc = " <div class='activity_wrap_note activity_wrap' >";
            if (count($notes_res) > 0) {
                foreach ($notes_res as $note) {
                    $note_desc .= '<div class="media">';
                    $note_desc .= '<div class="media-body fs14 br-n"><p class="text-light-gray mn">';
                    $note_desc .= $tz->getDate($note['created_at'], 'D., M. d, Y @ h:i A') . "</p>";
                    $note_desc .= '<p class="mn">' . note_custom_charecter('agent','lead',$note['description'], 400, $note['added_by_name'], $note['added_by_rep_id'], $note['added_by_id'],$note['added_by_detail_page']) . '</p></div>';
                    $note_desc .= '<div class="media-right text-nowrap">';
                    $note_desc .= '<a href="javascript:void(0);" id="edit_note_id" data-original-title="Edit" onclick=edit_note(' . $note['note_id'] . ',' . '"view"' . ') data-value="Lead"><i class="fa fa-eye fa-lg"></i></a> &nbsp;';
                    $note_desc .= '<a href="javascript:void(0);" class="" id="edit_note_id" data-original-title="Edit" onclick="edit_note(' . $note['note_id'] .','."''". ')" data-value="Lead"><i class="fa fa-edit fa-lg"></i></a> &nbsp;';
                    $note_desc .= '<a href="javascript:void(0);" class="" id="delete_note_id" data-original-title="Delete" onclick="delete_note(' . $note['note_id'] . ',' . $note['ac_id'] . ')" data-value="Lead"><i class="fa fa-trash fa-lg"></i></a>&nbsp;';
                    $note_desc .= "</div></div>";
                }
            } else {
                $note_desc .= '<p class="text-center mn"> No Notes Found. </p>';
            }
            $note_desc .= "</div>";
            echo $note_desc;
            exit;
        }
    }

    /*--- AAE Detail ---*/
    $sponsor_incr = "";
    $agent_id = $_SESSION['agents']['id'];
    $agent_level = getname('customer_settings',$agent_id,'agent_coded_level','customer_id');
    $subAgent = checkIsset($_SESSION['agents']['is_sub_agent']);
    $subAgentId = !empty($_SESSION['agents']['sub_agent_id']) ? $_SESSION['agents']['sub_agent_id'] : 0;

    $resSubAgent = array();
    if($subAgent == "Y" && !empty($subAgentId)){
        $selSubAgent =  "SELECT id FROM sub_agent WHERE id=:id AND is_deleted='N' AND edit_enrollment='Y'";
        $resSubAgent = $pdo->selectOne($selSubAgent,array(":id" => $subAgentId));
    }

    if($row['sponsor_id'] != $agent_id){
        if(!empty($resSubAgent["id"]) && checkIsset($row["agent_coded_level"]) == "LOA"){
            $allow_edit = true;    
        }else{
            $allow_edit = false;
        }
    }
   
    if($agent_level && $agent_level != 'LOA'){
        $LOA_agents = $pdo->selectOne("SELECT GROUP_CONCAT(c.id) as agent_ids 
            FROM customer c 
            JOIN customer_settings cs on(cs.customer_id = c.id)
            WHERE c.type = 'Agent' AND c.sponsor_id = :sponsor_id AND cs.agent_coded_level = 'LOA'
            ",array(':sponsor_id' => $agent_id));

        if(!empty($LOA_agents) && !empty($LOA_agents['agent_ids'])){
            $loa_agent_ids = $agent_id . ',' . $LOA_agents['agent_ids'];
            $sponsor_incr .= " AND lq.agent_id IN($loa_agent_ids)";
        } else {
            $sponsor_incr .= " AND lq.agent_id IN($agent_id)";    
        }
    }else{
        $sponsor_incr .= " AND lq.agent_id IN($agent_id)";
    }

    $enroll_sql = "SELECT lq.*,o.sub_total,o.grand_total,o.status as order_status,o.future_payment,o.post_date
                    FROM leads l 
                    JOIN lead_quote_details lq ON(l.id = lq.lead_id AND lq.is_assisted_enrollment = 'Y') 
                    JOIN orders o ON(o.id = lq.order_ids) 
                    WHERE md5(l.id)=:id AND 
                    lq.status IN ('Pending','Disabled','Completed') AND 
                    (lq.order_ids != '' AND lq.order_ids IS NOT NULL) $sponsor_incr ORDER BY lq.expire_time DESC LIMIT 2";
    $enroll_where = array(":id"=>$lead_id);
    $enroll_res = $pdo->select($enroll_sql,$enroll_where);

    $active_aae_id = 0;
    if(!empty($enroll_res)) {
        foreach ($enroll_res as $key => $enroll_row) {
            if($enroll_row['status'] == "Completed" || in_array($enroll_row['order_status'],array('Payment Approved','Post Payment','Pending Settlement'))) {

            } elseif ($enroll_row['status'] == "Pending") {
                if(strtotime(date('Y-m-d H:i:s')) < strtotime($enroll_row['expire_time'])) {
                    $active_aae_id = $enroll_row['id'];
                }
            }
        }
    } else {
        $ord_sql = "SELECT 'Completed' as status,o.id as order_ids,o.created_at,o.sub_total,o.grand_total,o.status as order_status,o.future_payment,o.post_date
                    FROM orders o 
                    WHERE o.customer_id=:customer_id AND o.is_renewal='N'
                    ORDER BY o.id DESC LIMIT 1";
        $ord_where = array(":customer_id" => $row['customer_id']);
        $enroll_res = $pdo->select($ord_sql,$ord_where);
    }
    /*---/AAE Detail ---*/


    $description['ac_message'] = array(
        'ac_red_1' => array(
            'href' => 'agent_detail_v1.php?id=' . md5($_SESSION['agents']['id']),
            'title' => $_SESSION['agents']['rep_id'],
        ),
        'ac_message_1' => ' read Lead ' . $row['fname'] . ' ' . $row['lname'] . ' (',
        'ac_red_2' => array(
            'href' => 'lead_details.php?id=' . $id,
            'title' => $row['lead_id'],
        ),
        'ac_message_2' => ')',
    );
    $desc = json_encode($description);
    activity_feed(3, $row['id'], 'Lead', $_SESSION['agents']['id'], 'Agent', 'Agent Read Lead Details.', $_SESSION['agents']['fname'], $_SESSION['agents']['lname'], $desc);
}

$group_agent_status = '';
if($row['lead_type'] == "Agent/Group") {
    if($row['status'] == "Converted") {
        $group_agent_status = 'Converted';
    } else {
        if(!empty($row['customer_status'])) {
            $group_agent_status = $row['customer_status'];
        } else {
            $group_agent_status = '';
        }
    }
}

$has_non_approved_order = false;
if(!in_array($row['status'],array("Unqualified")) && !in_array($row['customer_status'],array("Active","Inactive"))  && $row['lead_type'] == "Member" && !empty($row['customer_id'])) {
    $ord_sql = "SELECT o.* 
                FROM orders o 
                WHERE 
                o.is_renewal='N' AND
                o.status IN('Post Payment','Payment Declined') AND 
                o.customer_id=:customer_id
                ORDER BY o.id DESC";
    $ord_where = array(":customer_id" => $row['customer_id']);
    $ord_row = $pdo->selectOne($ord_sql,$ord_where);
    if(!empty($ord_row)) {

        //selecting billing profile
        $cb_sql = "SELECT payment_mode,card_type,last_cc_ach_no FROM customer_billing_profile WHERE is_default = 'Y' AND customer_id=:customer_id AND is_deleted='N' ORDER BY id DESC";
        $cb_where = array(":customer_id" => $row['customer_id']);
        $cb_row = $pdo->selectOne($cb_sql, $cb_where);


        $has_non_approved_order = true;
    }
}

$exStylesheets = array(
    'thirdparty/multiple-select-master/multiple-select.css'.$cache,
);
$exJs = array(
    'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
    'thirdparty/formatCurrency/jquery.formatCurrency-1.4.0.js',
    'thirdparty/jquery-match-height/js/jquery.matchHeight.js',
    'thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache,
    'thirdparty/vue-js/vue.min.js'
);
$template = 'lead_details.inc.php';
include_once 'layout/end.inc.php';
?>