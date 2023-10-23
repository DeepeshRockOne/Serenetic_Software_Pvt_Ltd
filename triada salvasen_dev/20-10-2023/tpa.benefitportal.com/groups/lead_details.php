<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Enrollees';
$breadcrumbes[1]['link'] = 'group_enrollees.php';
$breadcrumbes[2]['title'] = 'Enrollees';
$group_id = $_SESSION['groups']['id'];
$tz = new UserTimeZone('m/d/Y @ g:i A T', $_SESSION['groups']['timezone']);
$prdPlanTypeArray = $cacheMainArray['prdPlanTypeArray'];

$id = checkIsset($_GET['id']);
$lead_id = $id;
$user_type = "Lead";

if(!empty($_POST['showing_pass']) || (!empty($_POST['type']) && $_POST['type'] == 'encrypt')){
    $salaryRes = salaryEncryptDecrypt('group','lead');
    header('Content-Type: application/json');
    echo json_encode($salaryRes);
    exit;
}
$select = "SELECT md5(l.id) as id,l.*,c.status as customer_status,
            AES_DECRYPT(l.ssn_itin_num,'" . $CREDIT_CARD_ENC_KEY . "') as ssn_itin_num,s.business_name
		    FROM leads l
		    JOIN customer s on(s.id = l.sponsor_id)
            LEFT JOIN customer c on(c.id = l.customer_id)
		    WHERE md5(l.id)=:id AND l.sponsor_id=:sponsor_id AND l.is_deleted = 'N'";
$where = array(':id' => makeSafe($lead_id),':sponsor_id' => $group_id);
$row = $pdo->selectOne($select, $where);

$sqlBillingType = "SELECT billing_type FROM customer_group_settings where customer_id=:customer_id";
$resBillingType = $pdo->selectOne($sqlBillingType,array(":customer_id"=>$group_id));
$group_billing_method = '';
if(!empty($resBillingType)){
    $group_billing_method = $resBillingType['billing_type'];
}


$sqlCompany = "SELECT id,name,location from group_company where group_id = :group_id AND is_deleted='N'";
$resCompany = $pdo->select($sqlCompany,array(":group_id"=>$group_id));


$sqlClass = "SELECT id,class_name from group_classes where group_id = :group_id AND is_deleted='N'";
$resClass = $pdo->select($sqlClass,array(":group_id"=>$group_id));

$sqlCoverageCheck = "SELECT id,coverage_period_name,coverage_period_start,coverage_period_end FROM group_coverage_period where group_id=:group_id AND is_deleted='N'";
$resCoverageCheck = $pdo->select($sqlCoverageCheck,array(":group_id"=>$group_id));
$assignCoverageArr = array();
if (empty($row)) {
    setNotifyError('Lead does not exist');
    redirect("group_enrollees.php");
} else {
    $group_name = $row['business_name'];
    $employee_id = $row['employee_id'];
    $income = $row['income'];
    $group_company_id = $row['group_company_id'];
    $employee_type = $row['employee_type'];
    $hire_date = !empty($row['hire_date']) ? date('m/d/Y',strtotime($row['hire_date'])) : '';
    $termination_date = !empty($row['termination_date']) ? date('m/d/Y',strtotime($row['termination_date'])) : '';
    $fname = $row['fname'];
    $lname = $row['lname'];
    $address = $row['address'];
    $city = $row['city'];
    $state = $row['state'];
    $zip = $row['zip'];
    $gender = $row['gender'];
    $birth_date = !empty($row['birth_date']) ? date('m/d/Y',strtotime($row['birth_date'])) : '';
    $ssn_itin_num = $row['ssn_itin_num'];
    $last_four_ssn = $row['last_four_ssn'];
    $email = $row['email'];
    $cell_phone = $row['cell_phone'];
    $group_classes_id = $row['group_classes_id'];
    $group_coverage_id = $row['group_coverage_id'];

    $sqlAssignCoverage = "SELECT * FROM leads_assign_coverage where is_deleted='N' AND md5(lead_id)=:lead_id";
    $resAssignCoverage = $pdo->select($sqlAssignCoverage,array(":lead_id"=>$lead_id));

    if(!empty($resAssignCoverage)){
        foreach ($resAssignCoverage as $key => $value) {
            array_push($assignCoverageArr, $value['group_coverage_period_id']);
        }
    }

    $sqlCoverage = "SELECT gc.id,gc.coverage_period_name FROM group_coverage_period gc 
                    JOIN group_coverage_period_offering gco ON (gc.id = gco.group_coverage_period_id AND gco.is_deleted='N')
                    WHERE gco.class_id=:class_id AND gco.status='Active' group by gc.id";
    $resCoverage = $pdo->select($sqlCoverage,array(":class_id"=>$group_classes_id));

    if (!isset($_GET['pages'])) {
        $note_search_keyword = isset($_REQUEST['note_search_keyword']) ? $_REQUEST['note_search_keyword'] : '';
        $note_search_keyword = cleanSearchKeyword($note_search_keyword);
        $note_ajax = isset($_POST['note_ajax']) ? $_POST['note_ajax'] : "";
        $extra_params = array();
        $extra_params['note_search_keyword'] = $note_search_keyword;
        $notes_res = get_note_section_data('admin',$lead_id,'lead',$extra_params);

        if ($note_search_keyword !== '' || $note_ajax == 'Y') {
            $note_desc = " <div class='activity_wrap_note activity_wrap'>";
            if (count($notes_res) > 0) {
                foreach ($notes_res as $note) {
                    $note_desc .= '<div class="media">';
                    $note_desc .= '<div class="media-body fs14 br-n"><p class="text-light-gray mn">';
                    $note_desc .= $tz->getDate($note['created_at'], 'D., M. d, Y @ h:i A') . "</p>";

                    $note_desc .= '<p class="mn">' . note_custom_charecter('admin','lead',$note['description'], 400,$note['added_by_name'],$note['added_by_rep_id'],$note['added_by_id'],$note['added_by_detail_page']) . '</p></div>';

                    $note_desc .= '<div class="media-right text-nowrap">';
                    $note_desc .= '<a href="javascript:void(0);" id="edit_note_id" data-original-title="View" onclick=edit_note(' . $note['note_id'] . ',' . '"view"' . ') data-value="Lead"><i class="fa fa-eye fa-lg"></i></a> &nbsp;';
                    $note_desc .= '<a href="javascript:void(0);" class="" id="edit_note_id" data-original-title="Edit" onclick="edit_note(' . $note['note_id'] . ','."''".')" data-value="Lead"><i class="fa fa-edit fa-lg"></i></a> &nbsp;';
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

    if($group_billing_method=='individual'){
        $enroll_sql = "SELECT lq.*,o.sub_total,o.grand_total,o.status as order_status,o.future_payment,o.post_date
                        FROM leads l 
                        JOIN lead_quote_details lq ON(l.id = lq.lead_id AND lq.is_assisted_enrollment = 'Y') 
                        JOIN orders o ON(o.id = lq.order_ids) 
                        WHERE md5(l.id)=:id AND 
                        lq.status IN ('Pending','Disabled','Completed') AND 
                        (lq.order_ids != '' AND lq.order_ids IS NOT NULL)
                        ORDER BY lq.expire_time DESC LIMIT 2";
        $enroll_where = array(":id"=>$lead_id);
        $enroll_res = $pdo->select($enroll_sql,$enroll_where);
    }else{
        $enroll_sql = "SELECT lq.*,o.sub_total,o.grand_total,o.status as order_status,o.future_payment,o.post_date
                        FROM leads l 
                        JOIN lead_quote_details lq ON(l.id = lq.lead_id AND lq.is_assisted_enrollment = 'Y') 
                        JOIN group_orders o ON(o.id = lq.order_ids) 
                        WHERE md5(l.id)=:id AND 
                        lq.status IN ('Pending','Disabled','Completed') AND 
                        (lq.order_ids != '' AND lq.order_ids IS NOT NULL)
                        ORDER BY lq.expire_time DESC LIMIT 2";
        $enroll_where = array(":id"=>$lead_id);
        $enroll_res = $pdo->select($enroll_sql,$enroll_where);
    }
    

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
    }
    /*---/AAE Detail ---*/


    $description['ac_message'] = array(
        'ac_red_1' => array(
            'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
        	'title'=>$_SESSION['groups']['rep_id'],
        ),
        'ac_message_1' => ' read Enrollee ' . $row['fname'] . ' ' . $row['lname'] . ' (',
        'ac_red_2' => array(
            'href' => 'lead_details.php?id=' . $id,
            'title' => $row['lead_id'],
        ),
        'ac_message_2' => ')',
    );
    $desc = json_encode($description);
    activity_feed(3, $row['id'], 'Lead', $_SESSION['groups']['id'], 'Group', 'Group Read Enrollee Details.', $_SESSION['groups']['fname'], $_SESSION['groups']['lname'], $desc);
    activity_feed(3, $_SESSION['groups']['id'], 'Group', $row['id'], 'Lead', 'Group Read Enrollee Details.', $_SESSION['groups']['fname'], $_SESSION['groups']['lname'], $desc);
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

$exStylesheets = array(
    'thirdparty/multiple-select-master/multiple-select.css'.$cache,
    'thirdparty/bootstrap-datepicker-master/css/datepicker.css'.$cache,
);

$tmpExJs = array('thirdparty/jquery_ui/js/jquery-ui-1.9.2.custom.min.js');

$exJs = array(
    'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
    'thirdparty/jquery-match-height/js/jquery.matchHeight.js',
    'thirdparty/bootstrap-datepicker-master/js/bootstrap-datepicker.js'.$cache,
    'thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache,
    'thirdparty/vue-js/vue.min.js',
    'thirdparty/price_format/jquery.price_format.2.0.js',
);

$template = 'lead_details.inc.php';
include_once 'layout/end.inc.php';
?>
