<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
require_once dirname(__DIR__) . '/includes/member_enrollment.class.php';
require_once dirname(__DIR__) . '/includes/function.class.php';
$MemberEnrollment = new MemberEnrollment();
$function_list = new functionsList();
$ws_id = isset($_GET['ws_id']) ? $_GET['ws_id'] : "-1";

$sel_sql = "SELECT IF(s.type='Group',s.business_name,CONCAT(s.fname,' ',s.lname)) as agent_name, s.rep_id as agent_id,IF(p.name = '' AND p.product_type = 'ServiceFee','Service Fee',p.name) AS name,p.product_code,w.eligibility_date,w.termination_date,w.termination_reason,w.website_id,w.next_purchase_date,ppt.title as benefit_tier,w.price,DATE(w.created_at) as added_date,ce.fulfillment_date,p.id as p_id,w.status,p2.name as membership_fee,pm.id as prd_plan_id,ce.id as ce_id,ce.weekly_benefit,ce.monthly_benefit,w.end_coverage_period,w.id as ws_id,w.status as plan_status,ce.new_plan_id,ce.process_status,c.id as c_id,ce.is_request_to_change_coverage_approved,ce.coverage_requested_termination_date,ce.is_request_to_change_coverage,p.reinstate_option,c.status as customer_status,ce.process_status,p.reinstate_within_type,p.reinstate_within,w.active_date,ce.old_coverage_file,ce.first_eligibility_date,ce.most_recent_date,w.prd_plan_type_id,w.customer_id,w.product_id,w.policy_change_reason,w.benefit_amount,CONCAT(c.fname,' ',c.lname) as cust_name,p.product_type,p.main_product_type,s.type as sponsor_type,c.sponsor_id,w.is_cobra_coverage,IF(lbd.id IS NOT NULL,'Y','N') as is_list_bill_generated,w.fee_applied_for_product,w.product_type as sub_prd_type,p.is_benefit_tier
          FROM customer c
          JOIN customer s ON (s.id=c.sponsor_id)
          JOIN website_subscriptions w ON (w.customer_id=c.id)
          JOIN customer_enrollment ce ON (ce.website_id=w.id)
          JOIN prd_main p ON (p.id=w.product_id)
          JOIN prd_matrix pm ON (w.product_id=pm.product_id AND w.plan_id = pm.id)
          left JOIN prd_plan_type ppt ON (w.prd_plan_type_id = ppt.id)
          LEFT JOIN website_subscriptions w2 ON(w.product_id = w2.fee_applied_for_product AND w.customer_id = w2.customer_id)
          LEFT JOIN prd_main p2 ON(w2.product_id=p2.id AND p2.product_type = 'Membership')
          LEFT JOIN list_bill_details lbd ON(lbd.ws_id=w.id)
          WHERE md5(w.id) = :ws_id GROUP BY p.id";

$data = $pdo->selectOne($sel_sql, array(':ws_id' => $ws_id));
$OriginalCoverageFile = substr($data['old_coverage_file'], 10);
$website_id = $data['ws_id'];

$existingMemberData = array();
if(!empty($data)){
    $doc_sql = "SELECT id,member_document,original_file_name FROM customer_document WHERE website_id=:website_id AND is_deleted='N'";
    $existingMemberData = $pdo->select($doc_sql, array(':website_id' => $website_id));
}
$memberId = md5($data['customer_id']);
$product_id = md5($data['product_id']);

$sponsor_billing_method = "individual";
if($data['sponsor_type'] == "Group") {
    $sqlBillingType = "SELECT billing_type FROM customer_group_settings where customer_id=:customer_id";
    $resBillingType = $pdo->selectOne($sqlBillingType,array(":customer_id"=>$data['sponsor_id']));
    if(!empty($resBillingType)){
        $sponsor_billing_method = $resBillingType['billing_type'];
    }
}

$ce_sql = "SELECT * FROM customer_enrollment WHERE md5(website_id)=:website_id";
$ce_row = $pdo->selectOne($ce_sql, array(":website_id" => $ws_id));

if(!empty($ce_row['parent_coverage_id'])) {
    $parent_ce_sql = "SELECT * FROM customer_enrollment WHERE id=:id";
    $parent_ce_row = $pdo->selectOne($parent_ce_sql, array(":id" => $ce_row['parent_coverage_id']));
} else {
    $parent_ce_row = array();
}

$allow_to_reinstate = true;
$tier_change_date = displayDate($ce_row['tier_change_date']);
$new_prd_name = '';
$new_plan_name = '';
$is_requested_date = true;

$new_ws_sql = "SELECT ws.*,pm.name as product_name FROM customer_enrollment ce 
                JOIN website_subscriptions ws ON(ws.id = ce.website_id)
                JOIN prd_main pm ON(pm.id = ws.product_id)
                WHERE (ws.termination_date IS NULL OR ws.termination_date!=ws.eligibility_date) AND ce.parent_coverage_id=:ce_id AND ce.process_status IN('Pending','Active')";
$new_ws_row = $pdo->selectOne($new_ws_sql, array(":ce_id" => $ce_row['id']));
if(!empty($new_ws_row)) {
    $new_prd_name = $new_ws_row['product_name'];
    $new_plan_name = isset($prdPlanTypeArray[$new_ws_row['prd_plan_type_id']])?$prdPlanTypeArray[$new_ws_row['prd_plan_type_id']]['title']:'';    
} else {
    $data['process_status'] = "Active";
}

if (($data['is_request_to_change_coverage_approved'] != 'Y') && ($data['is_request_to_change_coverage'] == 'Y') && ($data['coverage_requested_termination_date'] != "")) {
    $requested_date = date($DATE_FORMAT, strtotime($data['coverage_requested_termination_date']));
    if (strtotime($requested_date) >= strtotime(date($DATE_FORMAT))) {
        $is_requested_date = false;
    }
}


/*--- Check Restricted Prd Active ----*/
$res_restricted_products = $pdo->selectOne("SELECT restricted_products FROM prd_main where id = :id", array(':id' => $data['p_id']));
if (!empty($res_restricted_products) && !empty($res_restricted_products['restricted_products'])) {
    $restricted_products = $res_restricted_products['restricted_products'];
    $check_active_products = $pdo->selectOne("SELECT id FROM website_subscriptions WHERE status IN('Active','Pending Payment') AND product_id in($restricted_products) and customer_id = :id", array(':id' => $data['c_id']));
    if ($check_active_products) {
        $allow_to_reinstate = false;
    }
}
/*---/Check Restricted Prd Active ----*/

/*---/Check same product purchased and its Active/Pending START----*/
$check_same_active_products = $pdo->selectOne("SELECT id FROM website_subscriptions WHERE status IN('Active','Pending Payment') AND product_id = :product_id AND id != :website_id and customer_id = :id", array(':id' => $data['c_id'],':product_id' => $data['p_id'],':website_id' => $data['ws_id']));
    if ($check_same_active_products) {
        $allow_to_reinstate = false;
    }
/*---/Check same product purchased and its Active/Pending END----*/

/*--- Fetch Prd Benefit Tiers ---*/
if($data['fee_applied_for_product'] != 0 && $data['sub_prd_type'] == 'Fees' && $data['is_benefit_tier'] == 'Y' && $data['prd_plan_type_id'] != 0){
    $check_fee_product = "SELECT id,pricing_model FROM prd_main WHERE id = :product_id AND pricing_model = 'VariableEnrollee'";
    $res_fee_product = $pdo->selectOne($check_fee_product, array(':product_id' => $data['fee_applied_for_product']));
    if(!empty($res_fee_product)){
        $product_id = md5($res_fee_product['id']);
    }
}
$sqlAssigned = "SELECT * FROM prd_coverage_options WHERE is_deleted='N' AND md5(product_id)=:product_id";
$resAssgined = $pdo->select($sqlAssigned, array(":product_id" => $product_id));
$prd_plan_type_ids = array();
if (!empty($resAssgined)) {
    foreach ($resAssgined as $key => $value) {
        $prd_plan_type_ids[] = $value['prd_plan_type_id'];
    }
}
if (!empty($prd_plan_type_ids)) {
    $productPlan = $pdo->select("SELECT ppt.* FROM prd_plan_type ppt WHERE ppt.id IN(" . implode(',', $prd_plan_type_ids) . ") ORDER BY order_by ASC");
} else {
    $productPlan = $pdo->select("SELECT ppt.* FROM prd_plan_type ppt JOIN prd_matrix pm ON (pm.plan_type = ppt.id) WHERE md5(pm.product_id) = :product_id AND is_deleted = 'N' GROUP BY ppt.id ORDER BY order_by ASC", array(':product_id' => $product_id));
}
/*---/Fetch Prd Benefit Tiers ---*/


/*--- Connecte Products ---*/
$prd_conn_data = $MemberEnrollment->getPolicyUpgradeDowngradeSetting($data['product_id'],$data['eligibility_date'],$data['sponsor_id']);
$conn_row = $prd_conn_data['conn_row'];
$connected_prd = $prd_conn_data['conn_prd'];
$upgrade_downgrade_life_event_icon = $prd_conn_data['display_life_event_icon'];
/*---/Connecte Products ---*/

$reasons = $pdo->select("SELECT name from termination_reason where is_deleted = 'N'");

$policy_change_reason = (!empty($data['policy_change_reason'])?$data['policy_change_reason']:'benefit_tier_change');

$curr_policy_benefit_amount_text = "<table cellpadding='0' cellspacing='0' width='100%'><tbody><tr><td >".$data['cust_name'].":</td> <td class='text-right'>".displayAmount($data['benefit_amount'])."</td></tr>";
$curr_policy_benefit_amount = $data['benefit_amount'];
$dep_benefit_amount = $pdo->select("SELECT relation,benefit_amount,CONCAT(fname,' ',lname) as dep_name,IF(LOWER(relation)IN('husband','wife'),'Spouse','Child') as crelation FROM customer_dependent WHERE website_id=:website_id AND is_deleted = 'N' ORDER BY FIELD (crelation,'Spouse','Child') ASC, created_at DESC", array(':website_id' => $data['ws_id']));
if ($dep_benefit_amount) {
    foreach ($dep_benefit_amount as $amount) {
        $curr_policy_benefit_amount = $curr_policy_benefit_amount + $amount['benefit_amount'];
        $curr_policy_benefit_amount_text .= "<tr><td>".$amount['dep_name'].":</td> <td class='text-right'>".displayAmount($amount['benefit_amount'])."</td></tr>";
    }
}

$curr_policy_benefit_amount_text .= "</tbody></table>";

$new_policy_benefit_amount = 0;
if(!empty($new_ws_row)) {
    $new_policy_benefit_amount = $new_ws_row['benefit_amount'];
    $dep_benefit_amount = $pdo->select("SELECT relation,benefit_amount FROM customer_dependent WHERE website_id=:website_id AND is_deleted = 'N'", array(':website_id' => $new_ws_row['id']));
    if ($dep_benefit_amount) {
        foreach ($dep_benefit_amount as $amount) {
            $new_policy_benefit_amount = $new_policy_benefit_amount + $amount['benefit_amount'];
        }
    }
}

$allow_cancel_termination = true;
if($data['main_product_type'] == "Core Product" && !empty($data['termination_date'])) {
    $core_prd_termination_data = $MemberEnrollment->check_allow_cancel_core_prd_termination($data['ws_id'],$data['c_id'],$data['termination_date']);
    $allow_cancel_termination = $core_prd_termination_data['allow_cancel_termination'];
}

$policy_change_op = true;
$tier_change_op = true;
$termination_date_op = true;
$assign_dep_op = true;
$active_since_date_op = true;
$benefit_amount_change_op = true;
$coverage_proof_op = true;
$next_billing_date_op = true;
$save_button_op = true;
if ($ce_row['process_status'] == 'Cancelled' || ($ce_row['process_status'] == 'Pending' && !empty($parent_ce_row) && $parent_ce_row['new_plan_id'] > 0 && $parent_ce_row['process_status'] == "Pending" && $parent_ce_row['id'] == $ce_row['parent_coverage_id']) || ($ce_row['new_plan_id'] > 0 && $ce_row['process_status'] == "Pending" && !empty($new_ws_row))) {

    $policy_change_op = false;
    $tier_change_op = false;
    $termination_date_op = false;
    $assign_dep_op = false;
    $active_since_date_op = false;
    $benefit_amount_change_op = false;
    $coverage_proof_op = false;
    $next_billing_date_op = false;
    $save_button_op = false;
}
if($data['status'] == "Inactive" && !empty($new_ws_row)) {
    $policy_change_op = false;
    $tier_change_op = false;
    $termination_date_op = false;
    $assign_dep_op = false;
    $active_since_date_op = false;
    $benefit_amount_change_op = false;
    $coverage_proof_op = false;
    $next_billing_date_op = false;
    $save_button_op = false;
}
if ($data) {
    ob_start();
    ?>
    <div class="product_detail_wrap">
        <div class="panel panel-default" id="panel_product_details">
            <div class="panel-heading">
                <h4><?= $data['name'] ?></h4>
            </div>
            <div class="panel-body">
                <p class="mdp_title">Product Details</p>
                <div id="product_section">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="table-responsive mdp_info_table">
                                <table cellpadding="0" cellspacing="0" width="100%">
                                    <tbody>
                                    <tr>
                                        <td>Product Name:</td>
                                        <td>
                                            <div class="theme-form pr">
                                                <?php if($policy_change_op == false) { ?>
                                                    <?= $data['name'] ?>
                                                    <?php if ($policy_change_reason == "policy_change" && $ce_row['new_plan_id'] > 0 && $ce_row['process_status'] == "Pending" && !empty($new_ws_row)) { ?>
                                                        &nbsp;
                                                        <a href="javascript:void(0)" data-container="body" style="font-weight: 700"
                                                           data-html="true"
                                                           data-template='<div class="popover popover-danger" ><div class="arrow"></div><div class="popover-inner"><div class="popover-content"></div></div></div>'
                                                           data-toggle="popover"
                                                           data-placement="top"
                                                           data-content="<table cellpadding='0' cellspacing='0' width='100%' class='text-action popover_table'>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>
                                                                                Future Policy Update:
                                                                            </td>
                                                                            <td class='text-right'>
                                                                            <a href='javascript:void(0);' 
                                                                            data-customer_id='<?=$memberId;?>'
                                                                            data-ws_id='<?=$ws_id;?>'
                                                                            class='btn_edit_tier_change_date text-action'><i class='fa fa-edit'></i><a/>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                Plan : 
                                                                            </td>
                                                                             <td>
                                                                                <?= $new_prd_name ?>
                                                                             </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                Plan Tier : 
                                                                            </td>
                                                                            <td>
                                                                                <?= $new_plan_name ?>
                                                                             </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                            Effective From : 
                                                                            </td>
                                                                            <td>
                                                                                <?= $tier_change_date ?>
                                                                             </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>"
                                                        ><i class="fa fa-exclamation-circle fa-lg"></i></a>
                                                    <?php } ?>

                                                    <?php if ($policy_change_reason == "policy_change" && $ce_row['process_status'] == 'Pending' && !empty($parent_ce_row) && $parent_ce_row['new_plan_id'] > 0 && $parent_ce_row['process_status'] == "Pending" && $parent_ce_row['id'] == $ce_row['parent_coverage_id']) { ?>
                                                        &nbsp;
                                                        <a href="javascript:void(0)" data-container="body" style="font-weight: 700"
                                                           data-html="true"
                                                           data-template='<div class="popover popover-danger" ><div class="arrow"></div><div class="popover-inner"><div class="popover-content"></div></div></div>'
                                                           data-toggle="popover"
                                                           data-placement="top"
                                                           data-content="<table cellpadding='0' cellspacing='0' width='100%' class='text-action popover_table'>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class='text-center'>
                                                                               To make any changes to this plan,<br/> you must update current active plan.
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class='text-center'>
                                                                               <a href='javascript:void(0);' data-ws_id='<?=md5($parent_ce_row['website_id'])?>' class='btn btn-icon btn-action product_name'>CLICK HERE</a>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>"><i class="fa fa-exclamation-circle fa-lg"></i></a>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <div class="phone-control-wrap">
                                                        <div class="phone-addon">
                                                            <select class="form-control product_change_action has-value "
                                                            data-old_prd_id="<?= $data['p_id'] ?>"
                                                            data-connection_id="<?= !empty($conn_row) ? $conn_row['connection_id'] : 0 ?>"
                                                            data-customer_id="<?= $data['c_id'] ?>"
                                                            data-ws_id="<?= $data['ws_id'] ?>">
                                                            <option data-hidden="true"></option>
                                                            <?php if ($connected_prd) {
                                                                foreach ($connected_prd as $product) { ?>
                                                                    <option value="<?= $product['id'] ?>" <?= $data['p_id'] == $product['id'] ? "selected='selected'" : "" ?>><?= $product['name'] ?></option>
                                                                <?php }
                                                            } ?>
                                                            </select>
                                                            <label>Product</label>
                                                        </div>
                                                        <div class="phone-addon">
                                                            <?php if($upgrade_downgrade_life_event_icon == "Y") { ?>
                                                            <span class="icons">
                                                                <a href="javascript:void(0);" class="text-action btn_product_change" data-customer_id="<?= $data['c_id'] ?>" data-ws_id="<?= $data['ws_id'] ?>"><i class="fa fa-male fa-lg" aria-hidden="true"></i></a>
                                                            </span>
                                                            <?php } ?>  
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Product ID:</td>
                                        <td><?= $data['product_code'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Price:</td>
                                        <td>$<?= $data['price'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Status:</td>
                                        <td><?= get_policy_display_status($data['status']) ?></td>
                                    </tr>
                                    <tr>
                                        <td>Enrolling Agent :</td>
                                        <td><?= $data['agent_name'] ?> - <a href="javascript:void(0);"
                                                                            class="fw500 text-action"><?= $data['agent_id'] ?> </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Plan Tier:</td>
                                        <td>
                                            <?php if ($tier_change_op == false) {
                                                echo $data['benefit_tier'];
                                                ?>
                                                <?php if ($policy_change_reason == "benefit_tier_change" && $ce_row['new_plan_id'] > 0 && $ce_row['process_status'] == "Pending" && !empty($new_ws_row)) { ?>
                                                    &nbsp;
                                                    <a href="javascript:void(0)" data-container="body" style="font-weight: 700"
                                                       data-html="true"
                                                       data-template='<div class="popover popover-danger" ><div class="arrow"></div><div class="popover-inner"><div class="popover-content"></div></div></div>'
                                                       data-toggle="popover"
                                                       data-placement="top"
                                                       data-content="<table cellpadding='0' cellspacing='0' width='100%' class='text-action popover_table'>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>
                                                                               Future Plan Update:
                                                                            </td>
                                                                             <td class='text-right'>
                                                                               <a href='javascript:void(0);' 
                                                                        data-customer_id='<?=$memberId;?>'
                                                                        data-ws_id='<?=$ws_id;?>'
                                                                        class='btn_edit_tier_change_date text-action'><i class='fa fa-edit'></i><a/>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                Plan Tier : 
                                                                            </td>
                                                                            <td><?= $new_plan_name ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                               Effective from : 
                                                                            </td>
                                                                            <td><?= $tier_change_date ?></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>"
                                                    ><i class="fa fa-exclamation-circle fa-lg"></i></a>
                                                <?php } ?>

                                                <?php if ($policy_change_reason == "benefit_tier_change" && $ce_row['process_status'] == 'Pending' && !empty($parent_ce_row) && $parent_ce_row['new_plan_id'] > 0 && $parent_ce_row['process_status'] == "Pending" && $parent_ce_row['id'] == $ce_row['parent_coverage_id']) { ?>
                                                    &nbsp;
                                                    <a href="javascript:void(0)" data-container="body" style="font-weight: 700"
                                                       data-html="true"
                                                       data-template='<div class="popover popover-danger" ><div class="arrow"></div><div class="popover-inner"><div class="popover-content"></div></div></div>'
                                                       data-toggle="popover"
                                                       data-placement="top"
                                                       data-content="
                                                       <table cellpadding='0' cellspacing='0' width='100%' class='text-action popover_table'>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class='text-center'>
                                                                               To make any changes to this plan,<br/> you must update current active plan.
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class='text-center'>
                                                                              <a href='javascript:void(0);' data-ws_id='<?=md5($parent_ce_row['website_id'])?>' class='btn btn-icon btn-action product_name'>CLICK HERE</a>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>"><i class="fa fa-exclamation-circle fa-lg"></i></a>
                                                <?php } ?>
                                                <?php
                                            } else { ?>
                                                <div class="theme-form pr">
                                                    <select class="form-control benefit_tier_change_drpdwn <?= $data['benefit_tier'] != '' ? 'has-value' : '' ?>"
                                                            data-product_id="<?= $data['p_id'] ?>"
                                                            data-customer_id="<?= $data['c_id'] ?>"
                                                            data-web_id="<?= $data['ws_id'] ?>"
                                                            data-org_prd_plan="<?= $data['prd_plan_type_id'] ?>">
                                                        <option data-hidden="true"></option>
                                                        <?php foreach ($productPlan as $row) { ?>
                                                            <option
                                                                    value="<?php echo $row["id"]; ?>" <?= $row['id'] == $data['prd_plan_type_id'] ? 'selected' : '' ?>><?php echo $row["title"]; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <label>Plan Tier</label>
                                                    <p class="error"><span id="error_benefit_tier"></span></p>
                                                </div>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-offset-1 col-md-7">
                            <div class="table-responsive mdp_info_table">
                                <table cellpadding="0" cellspacing="0" width="100%">
                                    <tbody>
                                    <?php if (check_is_card_exist($data['p_id'])) { ?>
                                        <tr>
                                            <td>ID Card:</td>
                                            <td class="icons">
                                                <a href="<?= $HOST ?>/id_card_popup.php?ws_id=<?= md5($data['ws_id']) ?>&user_type=Admin&user_id=<?=$_SESSION['admin']['id']?>"
                                                   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="ID Card"
                                                   class="id_card_popup"><i class="fa fa-eye fa-lg"
                                                                            aria-hidden="true"></i></a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td>Added Date:</td>
                                        <td><?= displayDate($data['added_date']) ?></td>
                                    </tr>
                                    <tr>
                                        <td>Effective Date:</td>
                                        <td class="icons">
                                            <span class="effective_td_<?= $data['p_id'] ?>"><?= displayDate($data['eligibility_date']) ?></span>

                                            <?php if ($policy_change_op == true && $tier_change_op == true) { ?>
                                                <?php if (!in_array($data['plan_status'], array("Inactive", "Cancel")) && !($data['new_plan_id'] > 0 && $data['process_status'] == "Pending") && empty($data['termination_date'])) { ?>
                                                    <a href="<?=$HOST?>/effective_billing_date.php?location=admin&ws_id=<?= md5($data['ws_id']) ?>"
                                                       data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit"
                                                       class="billing_date_popup m-l-10"><i class="fa fa-edit fa-lg"
                                                                                            aria-hidden="true"></i></a>
                                                <?php } ?>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Termination Date:</td>
                                        <td>
                                            <span class="termination_date_td" style="display:<?= !$data['termination_date'] ? 'none' : '' ?>">
                                                <?= displayDate($data['termination_date']) ?>
                                                &nbsp; &nbsp;
                                                <?php if($termination_date_op == true){ ?>
                                                <?php if(!empty($data['termination_reason'])){ ?>
                                                    <select class="form-control max-w150 select_term_reason" data-ws_id="<?=$data['ws_id']?>">
                                                        <?php foreach ($reasons as $reason) { ?>
                                                        <option value="<?=$reason['name'] ?>" <?=$data['termination_reason'] == $reason['name'] ? "selected = 'selected'" : ""?>><?=$reason['name']?></option>
                                                    <?php } ?>
                                                    </select>
                                                <?php } ?>
                                                <?php } else { ?>
                                                    <select class="form-control max-w150" data-ws_id="<?=$data['ws_id']?>">
                                                        <option><?=$data['termination_reason'];?></option>
                                                    </select>
                                                <?php } ?>
                                            </span>
                                            <span class="icons">
                                            <?php if($termination_date_op == true){ ?>
                                                <a href="javascript:void(0);" class="text-action"
                                                   style="<?= (empty($data['termination_date']) && $is_requested_date) ? '' : 'display: none;' ?>"><i class="fa fa-info-circle" aria-hidden="true"></i>
                                                </a>
                                                
                                                <a href="<?=$HOST?>/add_term_date.php?location=admin&ws_id=<?= md5($data['ws_id']) ?>"
                                                   class="add_term_date text-action m-l-10 fw500"
                                                   style="<?= (empty($data['termination_date']) && $is_requested_date) ? '' : 'display: none;' ?>">+ Term</i></a>
                                                <?php if($allow_cancel_termination == true) { ?>
                                                <a href="javascript:void(0);" data-ws_id="<?= md5($data['ws_id']) ?>" class="text-action"
                                                   id="btn_cancel_termination" data-by="super_admin"
                                                   style="<?= !empty($data['termination_date']) && strtotime($data['termination_date']) > strtotime(date('m/d/Y')) && $is_requested_date ? '' : 'display: none;' ?>"><strong>Cancel</strong></a>
                                                <?php } ?>
                                                <?php
                                                $currentDateTime = new DateTime();
                                                $dateTimeInTheFuture = new DateTime($data['termination_date']);
                                                $dateInterval = $dateTimeInTheFuture->diff($currentDateTime);
                                                $terminated_months_ago = 12 * $dateInterval->y + $dateInterval->m;

                                                if ($allow_cancel_termination == true && !in_array($data['product_type'],array('Healthy Step','ServiceFee')) && $data['process_status'] != "Pending" && strtotime($data['termination_date']) > 0 && in_array($data['customer_status'], array('Active','Inactive')) && $allow_to_reinstate) {
                                                    $is_reinstate_option_within = false;
                                                    if ($data['reinstate_option'] == 'Available Within Specific Time Frame') {
                                                        if ($data['reinstate_within_type'] == 'Days') {
                                                            if ($dateInterval->days <= $data['reinstate_within']) {
                                                                $is_reinstate_option_within = true;
                                                            }
                                                        } elseif ($data['reinstate_within_type'] == 'Weeks') {
                                                            if (($dateInterval->days / 7) <= $data['reinstate_within']) {
                                                                $is_reinstate_option_within = true;
                                                            }
                                                        } elseif ($data['reinstate_within_type'] == 'Months') {
                                                            if ($dateInterval->m <= $data['reinstate_within']) {
                                                                $is_reinstate_option_within = true;
                                                            }
                                                        } elseif ($data['reinstate_within_type'] == 'Years') {
                                                            if ($dateInterval->y <= $data['reinstate_within']) {
                                                                $is_reinstate_option_within = true;
                                                            }
                                                        }
                                                    }
                                                    if (in_array($data['reinstate_option'], array('Available Without Restrictions')) || $is_reinstate_option_within == true) { ?>
                                                        <?php if($data['is_cobra_coverage'] == 'Y'){ ?>
                                                            <a href="<?=$HOST?>/cobra_reinstate_products.php?location=admin&customer_id=<?= $data['c_id'] ?>&ws_id=<?= $data['ws_id'] ?>&reinstate=Y"
                                                           class="reinstate_products btn btn-action m-l-10">Reinstate</i></a>
                                                        <?php }else{ ?>
                                                            <a href="<?=$HOST?>/reinstate_products.php?location=admin&customer_id=<?= $data['c_id'] ?>&ws_id=<?= $data['ws_id'] ?>"
                                                            class="reinstate_products btn btn-action m-l-10">Reinstate</i></a>
                                                           <?php } ?>
                                                    <?php }
                                                }
                                                ?>
                                            <?php } ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Fullfillment Date:</td>
                                        <?php if ($data['fulfillment_date']) { ?>
                                            <td><?= displayDate($data['fulfillment_date']) ?></td>
                                        <?php } else { ?>
                                            <td>-</td>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <td>Membership:</td>
                                        <td><a href="javascript:void(0);"
                                               class="text-action"><?= isset($data['membership_fee']) ? $data['membership_fee'] : "" ?></a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
                <?php
                $dependents = $pdo->select("SELECT cd.*,CONCAT(cdp.fname,' ',cdp.lname) as dependent_name 
                  FROM customer_dependent cd
                  JOIN customer_dependent_profile cdp on(cdp.id = cd.cd_profile_id)
                  WHERE cd.website_id=:website_id AND cd.is_deleted = 'N' GROUP BY cdp.id", array(":website_id" => $data['ws_id']));

                ?>
                <div id="dependents_section">
                    <p class="mdp_title">Dependents</p>
                    <div class="table-responsive">
                        <table class="<?= $table_class ?>">
                            <thead>
                            <th>ID/Added Date</th>
                            <th>Name</th>
                            <th>Relation</th>
                            <th>Age</th>
                            <th>Effective Date</th>
                            <th>Termination Date</th>
                            <th width="50px">Actions</th>
                            </thead>
                            <tbody>
                            <?php if ($dependents) {
                                foreach ($dependents as $key => $value) { ?>
                                    <tr class="depedents_tr_<?= $value['id'] ?>">
                                        <td>
                                            <?= strtotime($value['hire_date']) > 0 ? date('m/d/Y', strtotime($value['hire_date'])) : date('m/d/Y', strtotime($value['created_at'])) ?>
                                            <br>
                                            <a href="javascript:void(0);" class="fw500 text-action">
                                                <?= $value['display_id'] ?></a>
                                        </td>
                                        <td><?= $value['dependent_name'] ?></td>
                                        <?php $relation = "";
                                        if (in_array(ucfirst($value['relation']), array('Wife', 'Husband'))) {
                                            $relation = 'Spouse';
                                        } else {
                                            $relation = 'Child';
                                        }

                                        ?>
                                        <td><?= $relation ?></td>
                                        <td><?= calculateAge($value['birth_date']); ?></td>
                                        <td> <?= displayDate($value['eligibility_date']) ?></td>
                                        <td> <?= displayDate($value['terminationDate']) ?></td>
                                        <td class="icons">
                                            <a href="prd_detail_edit_depedents.php?id=<?= $value['id'] ?>"
                                               data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="Edit"
                                               class="prd_detail_edit_depedents"><i class="fa fa-edit"
                                                                                    aria-hidden="true"></i></a>
                                            <a href="javascript:void(0);" class="dependent_delete"
                                               data-id="<?= $value['id'] ?>" data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                               data-title="Delete"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                        </td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="7">No Records</td>
                                </tr>
                            <?php } ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="7">
                                    <div class="row table-footer-row">
                                        <div class="col-sm-12">
                                            <div class="pull-left">
                                                <div class="dataTables_info"><?= count($dependents) > 0 ? "1 to " . count($dependents) . " of " . count($dependents) . " Records" : "" ?></div>
                                            </div>
                                            <?php if($assign_dep_op == true) { ?>
                                            <div class="pull-right">
                                                <a href="assign_depedents.php?ws_id=<?= md5($data['ws_id']) ?>"
                                                   class="assign_depedents btn btn-action">Assign Dependent(s)</a>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <hr>
                </div>

                <form name="details_form" id="details_form" enctype="multipart/form-data">
                    <div id="eligibility_section">
                        <p class="mdp_title">Eligibility File</p>

                        <input type="hidden" name="member_id" id="member_id" value="<?= $memberId ?>">
                        <input type="hidden" name="ce_id" value="<?= $data['ce_id'] ?>">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="table-responsive mdp_info_table">
                                    <table cellpadding="0" cellspacing="0" width="100%">
                                        <tbody>
                                        <tr>
                                            <td>First Sent Date:</td>
                                            <td><?= displayDate($data['first_eligibility_date']) ?></td>
                                        </tr>
                                        <tr>
                                            <td>Most Recent Sent Date:</td>
                                            <td><?= displayDate($data['most_recent_date']) ?></td>
                                        </tr>
                                        <tr>
                                            <td>Active Member Since Date:</td>
                                            <td class="icons">
                                                <span class="fw500 active_member_td"><?= strtotime($data['eligibility_date']) > 0 ? date('m/d/Y', strtotime($data['eligibility_date'])) : date('m/d/Y', strtotime($data['added_date'])) ?></span>
                                                <?php if($active_since_date_op == true) { ?>
                                                <a href="<?=$HOST?>/active_member_since.php?location=admin&ws_id=<?= md5($data['ws_id']) ?>"
                                                   data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="Edit"
                                                   class="billing_date_popup m-l-10"><i class="fa fa-edit fa-lg"
                                                                                        aria-hidden="true"></i></a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                Proof of Prior Plan:<br>
                                                <div class="phone-control-wrap m-t-5">
                                                <input type="hidden" name="website_id" value="<?= $website_id?>">
                                                    <?php if($coverage_proof_op == true) { ?>
                                                    <div class="phone-addon">
                                                        <div class="custom_drag_control ">
                                                            <span class="btn btn-action">Choose File</span>
                                                            <input type="file" class="gui-file" id=""
                                                                   name="proof_of_coverage" title="&nbsp;">
                                                            <input type="text" class="gui-input" id="coverageFile"
                                                                   placeholder="Drag or Select File" value="<?= $OriginalCoverageFile ?>">
                                                        </div>
                                                    </div>
                                                    <?php } else { ?>
                                                    <div class="phone-addon">
                                                        <div class="custom_drag_control ">
                                                            <span class="btn btn-action">Choose File</span>
                                                            <input type="file" class="gui-file" id=""
                                                                   name="proof_of_coverage"  title="&nbsp;" disabled="">
                                                            <input type="text" class="gui-input"
                                                                   placeholder="Drag or Select File"  title="&nbsp;" value="<?= $OriginalCoverageFile ?>">
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                    
                                                    <?php if ($data['old_coverage_file'] != '' && file_exists($OLD_COVERAGE_DIR . $data['old_coverage_file'])) { ?>
                                                        <div class="phone-addon w-30 old_coverage_action_div">
                                                            <a href="<?= $OLD_COVERAGE_WEB . $data['old_coverage_file'] ?>"
                                                               class="text-action fs18" target="blank"><i
                                                                        class=" fa-lg fa fa-download"></i></a>
                                                        </div>
                                                        <div class="phone-addon w-30 old_coverage_action_div">
                                                            <a href="javascript:void(0);"
                                                               class="text-action fs18 remove_proof_of_coverage"
                                                               data-ce_id="<?= $data['ce_id'] ?>"><i
                                                                        class=" fa-lg fa fa-trash"></i></a>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <p class="error"><span id="error_proof_of_coverage"></span></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <?php if($data['sub_prd_type']!= 'Fees'){ ?>
                                                    Member Document:<br>
                                                    <input type="hidden" name="count[]" id="count" value="">
                                                    <div id="member_document_div">
                                                    <?php if(!empty($existingMemberData)){
                                                            foreach($existingMemberData as $existingKey=>$existingRow){ ?>
                                                                <div class="memberDocumentDivCount" id="member_document_div_<?= $existingRow['id']?>" data-counter="<?= $existingRow['id']?>">
                                                                    <input type="hidden" name="old_member_document[<?= $existingRow['id'] ?>]" id="old_member_document<?= $existingRow['id']?>" value="<?= $existingRow['member_document'] ?>">
                                                                    <?php if($existingKey != 0){ ?>
                                                                        <div class="pull-right m-t-5">
                                                                            <a href="javascript:void(0);" class="btn mn pn text-action remove_member_document" id="remove_member_document_<?= $existingRow['id']?>" data-id="<?= $existingKey ?>" data-removeId="<?= $existingRow['id']?>">
                                                                                <i class="fa fa-times"></i>
                                                                            </a>
                                                                        </div>
                                                                    <?php } ?>
                                                                    <div class="clearfix"></div> 
                                                                    <div class="phone-control-wrap"> 
                                                                        <div class="phone-addon">       
                                                                            <div class="custom_drag_control" id="addFile">
                                                                                <span class="btn btn-action">Choose File</span>
                                                                                <input type="file" class="gui-file" id="member_document_<?= $existingRow['id']?>"
                                                                                            name="member_document[<?= $existingRow['id']?>]" title="&nbsp;">
                                                                                <input type="text" class="gui-input" id="dispalyFileName_<?= $existingRow['id']?>"
                                                                                                placeholder="Drag or Select File" value="<?= $existingRow['original_file_name'] ?>">
                                                                            </div>
                                                                        </div>
                                                                                <?php if(isset($existingRow['member_document'])){ ?>                                                                                
                                                                                <div class="phone-addon w-30" id="member_document_action_div_<?= $existingKey ?>">
                                                                                    <a href="../downloads3bucketfile.php?file_path=<?=$MEMBER_DOCUMENT_PATH?>&file_name=<?= $existingRow['member_document'] ?>"
                                                                                    class="text-action fs18"><i
                                                                                    class=" fa-lg fa fa-download"></i></a>
                                                                                </div>
                                                                                <?php if($existingKey == 0){ ?>
                                                                                <div class="phone-addon w-30">
                                                                                    <a href="javascript:void(0);" class="btn mn pn text-action fs18 remove_member_document" id="remove_member_document_<?= $existingRow['id']?>" data-id="<?= $existingKey ?>" data-removeId="<?= $existingRow['id']?>" data-div-id=<?= $existingKey ?>>
                                                                                    <i class=" fa-lg fa fa-trash" id="member_document_trash_div_0"></i></a>
                                                                                </div>
                                                                            <?php } ?>
                                                                            <?php } ?>
                                                                    </div>
                                                                    <p ><span id="error_member_document_<?= $existingRow['id']?>" class="error"></span></p>
                                                                </div>
                                                            <?php } ?>
                                                        <?php }else{ ?>
                                                            <div class="phone-control-wrap">
                                                                <div class="phone-addon">
                                                                    <div class="custom_drag_control" id="addFile">
                                                                        <span class="btn btn-action">Choose File</span>
                                                                        <input type="file" class="gui-file" id=""
                                                                                    name="member_document[0]" title="&nbsp;">
                                                                        <input type="text" class="gui-input"
                                                                                    placeholder="Drag or Select File" value="" >
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <p><span id="error_member_document_0" class="error"></span></p>
                                                        <?php } ?>
                                                    </div>
                                            </td>
                                        </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <button class="btn btn-info documents" id="addDocumentBtn" type="button">
                                                    + Files
                                                    </button>
                                                </td>
                                            </tr>
                                <?php } ?>
                                    </tbody>
                                </table>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-offset-1 col-md-3">
                                <div class="table-responsive mdp_info_table">
                                    <table cellpadding="0" cellspacing="0" width="100%">
                                        <tbody>
                                        <tr>
                                            <td>Benefit Amount:</td>
                                            <?php if ($curr_policy_benefit_amount > 0) { ?>
                                                <td>
                                                    <?= displayAmount($curr_policy_benefit_amount) ?>
                                                    <?php if($benefit_amount_change_op == true) { ?>
                                                    <a href="<?=$HOST?>/benefit_tier_change.php?location=admin&action=benefit_amount_change&ws_id=<?=$data['ws_id']?>"
                                                       data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="Edit"
                                                       class="m-l-10 edit_benifit_amount"><i class="fa fa-edit fa-lg"
                                                                                             aria-hidden="true"></i></a>
                                                    <?php } else { ?>
                                                        <?php if ($policy_change_reason == "benefit_amount_change" && $ce_row['new_plan_id'] > 0 && $ce_row['process_status'] == "Pending" && !empty($new_ws_row)) { ?>
                                                            &nbsp;
                                                            <a href="javascript:void(0)" data-container="body" style="font-weight: 700"
                                                               data-html="true"
                                                               data-template='<div class="popover popover-danger" ><div class="arrow" ></div><div class="popover-inner"><div class="popover-content"></div></div></div>'
                                                               data-toggle="popover"
                                                               data-placement="top"
                                                               data-content="
                                                               <table cellpadding='0' cellspacing='0' width='100%' class='text-action popover_table'>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>
                                                                                Future Plan Update:
                                                                            </td>
                                                                            <td class='text-right'>
                                                                            <a href='javascript:void(0);' 
                                                                            data-customer_id='<?=$memberId;?>'
                                                                            data-ws_id='<?=$ws_id;?>'
                                                                            class='btn_edit_tier_change_date text-action'><i class='fa fa-edit'></i><a/>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                Plan :
                                                                            </td>
                                                                            <td>
                                                                               <?= $new_prd_name ?>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                Plan Tier : 
                                                                            </td>
                                                                            <td><?= $new_plan_name ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                            Effective From : 
                                                                            </td>
                                                                            <td><?= $tier_change_date ?></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>"
                                                            ><i class="fa fa-exclamation-circle fa-lg"></i></a>
                                                        <?php } ?>

                                                        <?php if ($policy_change_reason == "benefit_amount_change" && $ce_row['process_status'] == 'Pending' && !empty($parent_ce_row) && $parent_ce_row['new_plan_id'] > 0 && $parent_ce_row['process_status'] == "Pending" && $parent_ce_row['id'] == $ce_row['parent_coverage_id']) { ?>
                                                            &nbsp;
                                                            <a href="javascript:void(0)" data-container="body" style="font-weight: 700"
                                                               data-html="true"
                                                               data-template='<div class="popover popover-danger"><div class="arrow"></div><div class="popover-inner"><div class="popover-content"></div></div></div>'
                                                               data-toggle="popover"
                                                               data-placement="top"
                                                               data-content="<table cellpadding='0' cellspacing='0' width='100%' class='text-action popover_table'>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>
                                                                                <?=$curr_policy_benefit_amount_text;?>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class='text-center'>
                                                                               To make any changes to this plan,<br/> you must update current active plan.
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class='text-center'>
                                                                             <a href='javascript:void(0);' data-ws_id='<?=md5($parent_ce_row['website_id'])?>' class='btn btn-icon btn-action product_name'>CLICK HERE</a>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>"><i class="fa fa-exclamation-circle fa-lg"></i></a>
                                                        <?php } ?>

                                                    <?php } ?>
                                                </td>
                                            <?php } else { ?>
                                                <td>$0.00</td>
                                            <?php } ?>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <hr>
                    </div>
                    
                    <div id="billing_section">
                        <p class="mdp_title">Billing</p>
                        <div class="table-responsive mdp_info_table m-b-20">
                            <table cellpadding="0" cellspacing="0" width="100%">
                                <tbody>
                                <tr>
                                    <td>Billing Method:</td>
                                    <td><?php
                                        $display_text = '';
                                        if($sponsor_billing_method == "individual") {
                                            $customer_billing_profile = $pdo->selectOne("SELECT last_cc_ach_no,payment_mode FROM customer_billing_profile WHERE md5(customer_id) = :customer_id AND is_default = 'Y'", array(':customer_id' => $memberId));

                                            echo strtoupper($customer_billing_profile['payment_mode'])." *".$customer_billing_profile['last_cc_ach_no'];
                                            $display_text = 'Next Billing Date:';
                                        } else {
                                            echo display_billing_type($sponsor_billing_method);
                                            $display_text = $sponsor_billing_method != 'list_bill' ? 'Next Billing Date:' : 'Next List Bill Generation Date:';
                                        }
                                        ?></td>
                                </tr>
                                <tr>
                                    <td><?=$display_text?></td>
                                    <td class="icons">
                                        <span class="next_billing_td">
                                        <?php
                                        $displayPolicyNextBillingDate = displayPolicyNextBillingDate($data['ws_id'],$data['next_purchase_date'],$data['termination_date'],$data['end_coverage_period'],$data['sponsor_id'],array('sponsor_billing_method' => $sponsor_billing_method));
                                        echo $displayPolicyNextBillingDate;
                                        ?>    
                                        </span>
                                        <?php if($sponsor_billing_method == "individual" && $next_billing_date_op == true && strtotime($displayPolicyNextBillingDate) > 0){ ?>
                                        <a href="<?=$HOST?>/edit_next_billing_date.php?location=admin&ws_id=<?= md5($data['ws_id']) ?>"
                                           data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="Edit"
                                           class="billing_date_popup m-l-10"><i class="fa fa-edit fa-lg"
                                                                                aria-hidden="true"></i></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Order History:</td>
                                    <?php  
                                        $res = $function_list->getPaymentFailedCoverages(md5($data['c_id']),md5($data['ws_id']),array("checkFailCov" => "Y")); 
                                    ?>
                                    <td>
                                        <?php if(!empty($res) && $res["failCoverage"] == 'Y'){ ?>
                                            Missing an Approved Payment <span class="icons"><a href="<?=$HOST?>/payment_failed_coverages_details.php?websiteId=<?=md5($data['ws_id'])?>&customerId=<?=md5($data['c_id'])?>" data-toggle="tooltip" data-trigger="hover" data-original-title="Coverage Details" class="coverage_popup"><i class="fa fa-exclamation-circle fa-lg"></i></a></span>
                                        <?php }else{ ?>
                                            <span id="paid_date_td">
                                                <?php 
                                                if($data['sponsor_type'] == 'Group'){
                                                    if($data['is_list_bill_generated'] !='' && $data['is_list_bill_generated'] == 'Y'){
                                                        echo 'Paid Through '.displayDate($data['end_coverage_period']);
                                                    }else{
                                                        echo '-';
                                                    }
                                                }else{
                                                    echo 'Paid Through '.displayDate($data['end_coverage_period']);
                                                } ?>
                                            </span> 
                                        <?php } ?>
                                        
                                    <?php 
                                        if($data['is_cobra_coverage'] == 'Y'){ ?>
                                            <a href="edit_paid_through_date.php?ws_id=<?= md5($data['ws_id']) ?>"
                                           data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="Edit"
                                           class="billing_date_popup m-l-10"><i class="fa fa-edit fa-lg"
                                                                                aria-hidden="true"></i></a>
                                       <?php } 
                                    ?>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <?php if($sponsor_billing_method == "TPA") { ?>
                            <!-- If TPA bill, This will be blank -->

                        <?php } elseif($sponsor_billing_method == "list_bill") { ?>
                            <!-- Same details in each column as we have for direct sale member except “Status” will be status of list bill -->
                            <?php
                            $order_details = $pdo->select("SELECT lb.id,lb.list_bill_no,lb.status,lb.created_at,lbd.start_coverage_date,lbd.end_coverage_date
                                FROM list_bills lb 
                                JOIN list_bill_details lbd on(lbd.list_bill_id = lb.id) 
                                JOIN website_subscriptions w on(w.id = lbd.ws_id) 
                                WHERE lb.is_deleted='N' AND lbd.ws_id=:ws_id ORDER BY lbd.start_coverage_date ASC", array(':ws_id' => $data['ws_id']));
                            ?>
                            <div class="table-responsive">
                                <table class="<?= $table_class ?>">
                                    <thead>
                                    <th>List Bill ID</th>
                                    <th>Plan</th>
                                    <th>Status</th>
                                    <th width="130px">Billing Date</th>
                                    </thead>
                                    <tbody>
                                    <?php if ($order_details) {
                                        $count = 1;
                                        $tmp_start_coverage_date = $order_details[0]['start_coverage_date'];
                                        foreach ($order_details as $order) { 
                                            if($tmp_start_coverage_date != $order['start_coverage_date'] ) {
                                                $tmp_start_coverage_date = $order['start_coverage_date'];
                                                $count++;
                                            }   
                                        ?>
                                            <tr>
                                                <td><a href="view_listbill_statement.php?list_bill=<?= md5($order['id']) ?>" target="_blank" class="fw500 red-link pn"><?= $order['list_bill_no'] ?></a>
                                                </td>
                                                <td><?= date('m/d/Y', strtotime($order['start_coverage_date'])) ?>
                                                    - <?= date('m/d/Y', strtotime($order['end_coverage_date'])) ?></td>
                                                <td><?= ucwords($order['status']) ?></td>
                                                <td><?= date('m/d/Y', strtotime($order['created_at'])) ?></td>
                                            </tr>
                                        <?php
                                        }
                                    } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } elseif($sponsor_billing_method == "individual") { ?>
                            <?php
                            $order_details = $pdo->select("SELECT o.id,od.start_coverage_period,od.end_coverage_period,o.display_id,o.status,o.created_at,od.renew_count,w.id as w_id,od.is_refund,od.is_chargeback,od.is_payment_return 
                                FROM orders o 
                                JOIN order_details od on(o.id = od.order_id AND od.is_deleted='N') 
                                JOIN website_subscriptions w on(w.product_id = od.product_id AND w.plan_id = od.plan_id) 
                                WHERE md5(o.customer_id) = :customer_id AND od.website_id = :website_id GROUP BY o.id 
                                ORDER BY od.start_coverage_period,o.created_at ASC", array(':customer_id' => $memberId, ':website_id' => $data['ws_id']));
                            ?>
                            <div class="table-responsive">
                                <table class="<?= $table_class ?>">
                                    <thead>
                                    <th>Period</th>
                                    <th>Order ID</th>
                                    <th>Plan</th>
                                    <th>Status</th>
                                    <th width="130px">Billing Date</th>
                                    </thead>
                                    <tbody>
                                    <?php if ($order_details) {
                                        $count = 0;
                                        foreach ($order_details as $order) { ?>
                                            <tr>
                                                <td class="fw500">
                                                    <?= "P" .$order['renew_count'] ?>
                                                </td>
                                                <td><a href="order_receipt.php?orderId=<?= md5($order['id']) ?>"
                                                       class="fw500 red-link pn order_receipt"><?= $order['display_id'] ?></a>
                                                </td>
                                                <td><?= date('m/d/Y', strtotime($order['start_coverage_period'])) ?>
                                                    - <?= date('m/d/Y', strtotime($order['end_coverage_period'])) ?></td>
                                                <td>
                                                    <?php
                                                        $order_status = $order['status']; 
                                                        if($order_status == 'Void'){
                                                            $order_status ='Void';
                                                        }else if($order['is_refund'] == 'Y' || $order_status == 'Refund'){
                                                            $order_status = 'Refund';
                                                        }else if($order['is_chargeback'] == 'Y'){
                                                            $order_status = "Chargeback";
                                                        }else if($order['is_payment_return'] == 'Y'){
                                                            $order_status = "Payment Returned";
                                                        }

                                                        echo $order_status; 
                                                    ?>
                                                </td>
                                                <td><?= date('m/d/Y', strtotime($order['created_at'])) ?></td>
                                            </tr>
                                        <?php }
                                    } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                    </div>
                    <?php if($save_button_op == true) { ?>
                    <div class="m-t-20 text-center">
                        <button type="submit" name="submit" id="btn_save_member_details" class="btn btn-info">Save
                        </button>
                        <a href="javascript:void(0);" id="cancel" class="btn red-link">Cancel</a>
                    </div>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>
<?php }
$html = ob_get_contents();
ob_get_clean();
echo $html;
?>
