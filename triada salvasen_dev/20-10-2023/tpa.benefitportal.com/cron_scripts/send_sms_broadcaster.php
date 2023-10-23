<?php

include_once dirname(__DIR__) . "/cron_scripts/connect.php";
include_once dirname(__DIR__) . "/includes/upload_paths.php";


$brd_sql = "SELECT b.id,b.display_id, b.email_template_id, b.from_address, b.subject, b.mail_content, b.user_type, b.is_for_specific, b.specific_user_ids,b.specific_agent_level,b.agent_status,b.enrolling_agent_ids,b.tree_agent_ids,b.states, b.product_ids, b.product_status, b.admin_level, b.lead_tags,b.lead_status, b.total_users, b.total_sent, b.total_fail, bss.total_users as bss_total_users, bss.total_sent as bss_total_sent, bss.total_fail as bss_total_fail, bss.id as bss_id, bss.pause_after, b.sender_id,b.sender_type, bss.last_user_id
            FROM broadcaster as b
            Join broadcaster_schedule_settings as bss ON (bss.broadcaster_id = b.id)
            JOIN broadcaster_message as bm ON(bm.broadcaster_id = b.id AND bm.is_deleted='N')
            WHERE b.type='sms' AND b.is_deleted = 'N' AND bss.is_deleted = 'N' AND bss.is_sent = 'N' AND b.status IN ('Pending','Processing') AND bss.converted_date <= :converted_date GROUP BY b.id ORDER BY b.id";
$sch_param = array(":converted_date" => date("Y-m-d H:i:s"));
$brd_rows = $pdo->select($brd_sql, $sch_param);

if (count($brd_rows) > 0) {
  foreach ($brd_rows as $brd_row) {
    $brd_row['specific_user_ids'] = trim($brd_row['specific_user_ids'],',');
    $brd_row['specific_agent_level'] = trim($brd_row['specific_agent_level'],',');
    $brd_row['agent_status'] = trim($brd_row['agent_status'],',');
    $brd_row['enrolling_agent_ids'] = trim($brd_row['enrolling_agent_ids'],',');
    $brd_row['tree_agent_ids'] = trim($brd_row['tree_agent_ids'],',');
    $brd_row['states'] = trim($brd_row['states'],',');
    $brd_row['product_ids'] = trim($brd_row['product_ids'],',');
    $brd_row['product_status'] = trim($brd_row['product_status'],',');
    $brd_row['admin_level'] = trim($brd_row['admin_level'],',');
    $brd_row['lead_tags'] = trim($brd_row['lead_tags'],',');
    $brd_row['lead_status'] = trim($brd_row['lead_status'],',');

    $upd_bd_params = array('is_running' => 'Y');
    $upd_bd_wh = array('clause' => 'id = :id', 'params' => array(':id' => $brd_row['bss_id']));
    $pdo->update('broadcaster_schedule_settings', $upd_bd_params, $upd_bd_wh);

    $upd_bd_params = array('status' => 'Processing');
    $upd_bd_wh = array('clause' => 'id = :id', 'params' => array(':id' => $brd_row['id']));
    $pdo->update('broadcaster', $upd_bd_params, $upd_bd_wh);

    $user_group_incr = '';
    if(!empty($brd_row['last_user_id']) && $brd_row['last_user_id'] > 0){
      $user_group_incr = " AND c.id > " .$brd_row['last_user_id'] . " ";
    }

    $sponsor_incr = '';
    if(!empty($brd_row["sender_id"]) && ($brd_row["sender_type"] == "Agent" || $brd_row["sender_type"] == "Group")){
      $sponsor_incr = " AND s.id=".$brd_row["sender_id"];
    }

    $specific_user_ids = $brd_row['specific_user_ids'];
    if($brd_row['user_type'] == 'Admins') {
      if($brd_row['is_for_specific'] == 'Y') {
        if(!empty($specific_user_ids)){
          $user_type_res = $pdo->select("SELECT c.id,c.fname,c.lname,c.email,c.phone,c.display_id as userDispId FROM admin as c WHERE c.is_active='Y' AND c.is_deleted='N' AND c.status = 'Active' AND c.id IN(".$specific_user_ids.") ".$user_group_incr." AND c.is_deleted = 'N' ORDER BY c.id ASC");

          pre_print("------------Admin Specific Start------------", false);
          pre_print($user_type_res, false);
          pre_print("------------Admin Specific End------------", false);
        }
      } else {
        if(!empty($brd_row['admin_level'])){
          $admin_level_arr = explode(",", $brd_row['admin_level']);
          $in_incr = "";
          if(count($admin_level_arr) > 0){
            $in_incr = "('".implode("','",$admin_level_arr)."')";
          }

          if(!empty($in_incr)){
            $user_type_sql = "SELECT c.id,c.fname,c.lname,c.email,c.phone,c.display_id as userDispId FROM admin as c WHERE c.is_active='Y' AND c.is_deleted='N' AND c.status = 'Active' ".$user_group_incr." AND c.type IN " .$in_incr ." ORDER BY c.id ASC";
            $user_type_res = $pdo->select($user_type_sql);
            pre_print("------------Admin not Specific Start------------", false);
            pre_print($user_type_res, false);
            pre_print("------------Admin not Specific End------------", false);
          }
        }
      }
    } else if($brd_row['user_type'] == 'Leads') {
      if($brd_row['is_for_specific'] == 'Y') {
        if(!empty($specific_user_ids)){
          $user_type_res = $pdo->select("SELECT c.id,c.fname,c.lname,c.email,c.cell_phone as phone, CONCAT(s.fname, ' ', s.lname, ' (', s.rep_id, ') ') as agent_name,c.lead_id as userDispId
            FROM leads as c 
            JOIN customer as s ON (s.id = c.sponsor_id)
            WHERE c.is_deleted='N' AND c.status NOT IN ('Converted',' ','NULL') AND c.id IN(".$specific_user_ids.") ".$user_group_incr."".$sponsor_incr." ORDER BY c.id ASC");
          pre_print("------------Leads Specific Start------------", false);
          pre_print($user_type_res, false);
          pre_print("------------Leads Specific End------------", false);
        }
      } else {
        $spon_incr = "";
        $in_incr = "";
        $state_incr = "";
        if(!empty($brd_row['lead_tags'])){
          $lead_tags_arr = explode(",", $brd_row['lead_tags']);
          if(count($lead_tags_arr) > 0){
            $in_incr = "('".implode("','",$lead_tags_arr)."')";
          }

          if(!empty($brd_row['states'])){
            $state_incr .= " AND c.state IN (" . $brd_row['states'] .")";          
          }
          if(!empty($brd_row['enrolling_agent_ids'])){

            $spon_incr .= " AND s.id IN (" . $brd_row['enrolling_agent_ids'] .")";

          }
          if(!empty($brd_row['tree_agent_ids'])){

            $spon_incr .= " AND s.id IN (" . $brd_row['tree_agent_ids'] .")";
      
          }
          if(!empty($brd_row['sender_id']) && ($brd_row["sender_type"] == "Agent" || $brd_row["sender_type"] == "Group")){

            $spon_incr .= " AND s.id IN (" . $brd_row['sender_id'] .")";
      
          }
          if(!empty($brd_row['lead_status'])){

            $spon_incr .= " AND c.status IN ('" . $brd_row['lead_status'] ."')";
      
          }

          $user_type_res = $pdo->select("SELECT c.id,c.fname,c.lname,c.email,c.cell_phone as phone, CONCAT(s.fname, ' ', s.lname, ' (', s.rep_id, ') ') as agent_name,c.lead_id as userDispId 
            FROM leads as c 
            JOIN customer as s ON (s.id = c.sponsor_id)
            WHERE c.is_deleted='N' AND c.status NOT IN ('Converted',' ','NULL') AND c.opt_in_type IN ".$in_incr." ".$user_group_incr." ".$spon_incr." ORDER BY c.id ASC");

          pre_print("------------Leads not Specific Start------------", false);
          pre_print($user_type_res, false);
          pre_print("------------Leads not Specific End------------", false);
        }
      }
    } else {
      $user_incr = '';
      if($brd_row['user_type'] == 'Agents') {
        $user_incr .= " AND c.type IN ('Agent')";
      } else if($brd_row['user_type'] == 'Employer Groups') {
        $user_incr .= " AND c.type IN ('Group')";
      } else {
        $user_incr .= " AND c.type IN ('Customer')";
      }

      if($brd_row['is_for_specific'] == 'Y') {
        if(!empty($specific_user_ids)){
          $user_type_res = $pdo->select("SELECT c.id,c.fname,c.lname,c.email,c.cell_phone as phone, CONCAT(s.fname, ' ', s.lname, ' (', s.rep_id, ') ') as agent_name,c.rep_id as userDispId  
            FROM customer as c 
            LEFT JOIN customer as s ON (s.id = c.sponsor_id)
            WHERE c.is_deleted='N' AND c.status NOT IN ('Customer Abandon','Pending Quote','Invited') ".$sponsor_incr." AND c.id IN(".$specific_user_ids.") ".$user_incr."  ".$user_group_incr." ORDER BY c.id ASC");
          pre_print("------------Customer, Group, Agent Specific Start------------", false);
          pre_print($user_type_res, false);
          pre_print("------------Customer, Group, Agent Specific End------------", false);
        }
      } else {
        if($brd_row['user_type'] == 'Members') {
          $excute_query = false;
          if(!empty($brd_row['product_ids'])){
            $excute_query = true;
            $user_incr .= " AND w.product_id IN (" . $brd_row['product_ids']. ") ";
          }
          if(!empty($brd_row['product_status'])){
            $excute_query = true;
            $user_incr .= " AND w.status IN ('" . $brd_row['product_status']. "') ";
          }
          $sp_incr = '';
          if(!empty($brd_row['enrolling_agent_ids'])){
            $sp_incr .=  " AND c.sponsor_id IN (" . $brd_row['enrolling_agent_ids'] .") ";
            $excute_query = true;
          }
          if(!empty($brd_row['tree_agent_ids'])){
            $tree_agent_id = explode(',',$brd_row['tree_agent_ids']);
            if(!empty($tree_agent_id) && count($tree_agent_id) > 0){
                $sp_incr .= " AND (";
                foreach ($tree_agent_id as $key => $value) {
                    if (end($tree_agent_id) == $value) {
                        $sp_incr .= " c.upline_sponsors LIKE '%," . $value . ",%'";
                    } else {
                        $sp_incr .= " c.upline_sponsors LIKE '%," . $value . ",%' OR";
                    }
                }
                $sp_incr .= ")";
                $excute_query = true;
            }
          }
          if(!empty($brd_row['states'])){
            $states = explode(',', $brd_row['states']);
            $states = "'" . implode("','", $states) . "'";
            $sp_incr .= " AND c.state IN (" . $states .") ";
            $excute_query = true;
          }
          if($excute_query){
            $user_type_res = $pdo->select("SELECT c.id,c.fname,c.lname,c.email,c.cell_phone as phone, CONCAT(s.fname, ' ', s.lname, ' (', s.rep_id, ') ') as agent_name,c.rep_id as userDispId  
              FROM customer as c
              JOIN customer as s ON (s.id = c.sponsor_id)
              JOIN website_subscriptions as w ON (w.customer_id = c.id)
              WHERE c.is_deleted = 'N' AND c.status NOT IN ('Customer Abandon','Pending Quote','Invited') ".$user_incr." ". $sp_incr." ".$user_group_incr." ".$sponsor_incr." GROUP BY c.id ORDER BY c.id ASC");
            pre_print("------------Customer not Specific Start------------", false);
            pre_print($user_type_res, false);
            pre_print("------------Customer not Specific End------------", false);
          }
        } else {
          $excute_query = false;
          $temp_incr = "";
          $tbl_incr = "";
          if(!empty($brd_row['states'])){
            $states = explode(',', $brd_row['states']);
            $states = "'" . implode("','", $states) . "'";
            $temp_incr .= " AND c.state IN (" . $states .") ";
            $excute_query = true;
          }
          if(!empty($brd_row['specific_agent_level'])){
            $temp_incr .= " AND cs.agent_coded_id IN (" . $brd_row['specific_agent_level'] .") ";
            $tbl_incr .=" LEFT JOIN customer_settings cs ON(cs.customer_id=c.id) ";
            $excute_query = true;
          }
          if(!empty($brd_row['agent_status'])){
            $agentStatus = explode(',', $brd_row['agent_status']);
            $agentStatus = "'" . implode("','", $agentStatus) . "'";
            $temp_incr .= " AND c.status IN (" . $agentStatus .") ";
            $excute_query = true;
          }
          if(!empty($brd_row['enrolling_agent_ids']) || !empty($brd_row['tree_agent_ids'])){
            if(!empty($brd_row['enrolling_agent_ids'])){
              $temp_incr .=  " AND c.sponsor_id IN (" . $brd_row['enrolling_agent_ids'] .") ";
              $excute_query = true;
            }
            if(!empty($brd_row['tree_agent_ids'])){
              $tree_agent_id = explode(',',$brd_row['tree_agent_ids']);
              if(!empty($tree_agent_id) && count($tree_agent_id) > 0){
                  $temp_incr .= " AND (";
                  foreach ($tree_agent_id as $key => $value) {
                      if (end($tree_agent_id) == $value) {
                          $temp_incr .= " c.upline_sponsors LIKE '%," . $value . ",%'";
                      } else {
                          $temp_incr .= " c.upline_sponsors LIKE '%," . $value . ",%' OR";
                      }
                  }
                  $temp_incr .= ")";
                  $excute_query = true;
              }
            }
          }
          if(!empty($brd_row['product_ids'])){
            $excute_query = true;
            $user_incr .= " AND apr.product_id IN (" . $brd_row['product_ids']. ") ";
          }
          if(!empty($brd_row['product_status'])){
            $excute_query = true;
            if($brd_row['product_status'] == 'Active'){
              $agent_status = 'Contracted';
            } else if($brd_row['product_status'] == 'Pending'){
              $agent_status = 'Pending Approval';
            } else {
              $agent_status = $brd_row['product_status'];
            }
            $user_incr .= " AND apr.status IN ('" . $agent_status. "') ";
          }
          if($excute_query){
            $user_type_res = $pdo->select("SELECT c.id,c.fname,c.lname,c.email,c.cell_phone as phone, CONCAT(s.fname, ' ', s.lname, ' (', s.rep_id, ') ') as agent_name,c.rep_id as userDispId  
              FROM customer as c
              LEFT JOIN customer as s ON (s.id = c.sponsor_id)
              JOIN agent_product_rule as apr ON (apr.agent_id = c.id)
              $tbl_incr
              WHERE c.is_deleted = 'N' AND c.status NOT IN ('Invited') AND apr.is_deleted = 'N' ".$user_incr." ".$user_group_incr." ".$temp_incr." ".$sponsor_incr." GROUP BY c.id ORDER BY c.id ASC");
            pre_print("------------Group and Agents not Specific Start------------", false);
            pre_print($user_type_res, false);
            pre_print("------------Group and Agents not Specific End------------", false);
          }
        }
      }
    }
    
    $check_total_users = $brd_row['bss_total_users'];
    $total_users = !empty($user_type_res) ? count($user_type_res) : 0;
    $total_sent = $brd_row['bss_total_sent'];
    $total_fail = $brd_row['bss_total_fail'];

    pre_print("-----------total_users-----------", false);
    pre_print($total_users, false);
    pre_print("-----------total_users-----------", false);

    pre_print("-----------total_sent-----------", false);
    pre_print($total_sent, false);
    pre_print("-----------total_sent-----------", false);

    pre_print("-----------total_fail-----------", false);
    pre_print($total_fail, false);
    pre_print("-----------total_fail-----------", false);

    if(empty($brd_row['bss_total_users']) || ((!empty($brd_row['bss_total_users']) && !empty($total_users) && $brd_row['bss_total_users'] != $total_users) && (empty($brd_row['last_user_id']) || (!empty($brd_row['last_user_id']) && $brd_row['last_user_id'] == 0)))){
      $upd_bd_params = array('total_users' => $total_users);
      $upd_bd_wh = array('clause' => 'id = :id', 'params' => array(':id' => $brd_row['bss_id']));
      $pdo->update('broadcaster_schedule_settings', $upd_bd_params, $upd_bd_wh);
      $check_total_users = $total_users;
    }

    $broadcaster_setting_res = $pdo->selectOne("SELECT SUM(total_users) as total_user_count, SUM(total_sent) as total_sent_count, SUM(total_fail) as total_fail_count FROM broadcaster_schedule_settings WHERE broadcaster_id = :broadcaster_id AND is_deleted = 'N' AND id != :id", array(":broadcaster_id" => $brd_row['id'], ":id" => $brd_row['bss_id']));
    $broadcaster_sent_count = $broadcaster_setting_res['total_sent_count'];
    $broadcaster_fail_count = $broadcaster_setting_res['total_fail_count'];

    pre_print("---------------broadcaster_setting_res---------------", false);
    pre_print($broadcaster_setting_res, false);
    pre_print("---------------broadcaster_setting_res---------------", false);

    $broadcaster_total_user_count = $check_total_users + $broadcaster_setting_res['total_user_count'];
    if(empty($brd_row['total_users']) || ((!empty($brd_row['total_users']) && !empty($broadcaster_total_user_count) && $brd_row['total_users'] != $broadcaster_total_user_count) && (empty($brd_row['last_user_id']) || (!empty($brd_row['last_user_id']) && $brd_row['last_user_id'] == 0)))){
      $upd_bd_params = array('total_users' => $broadcaster_total_user_count);
      $upd_bd_wh = array('clause' => 'id = :id', 'params' => array(':id' => $brd_row['id']));
      $pdo->update('broadcaster', $upd_bd_params, $upd_bd_wh);
    }

    $join_tables = "";
    $pauseCount = 0;
    $isCountPause = false;

    if ($brd_row['pause_after'] > 0) {
      $isCountPause = true;
      $pauseCount = $brd_row['pause_after'];
    }

    $brdMessageRes = $pdo->select("SELECT id,broadcaster_id,message FROM broadcaster_message WHERE broadcaster_id = :broadcasterId AND is_deleted = 'N'", array(":broadcasterId" => $brd_row['id']));
    

    if (count($user_type_res) > 0 && !empty($brdMessageRes)) {
      foreach ($user_type_res as $smsRow) {
        
        if ($isCountPause) {
          if ($pauseCount <= 0) {
            break;
          }
          $pauseCount--;
        }

        $b_status = getname('broadcaster', $brd_row['id'], "status");
        if (in_array($b_status, array('Completed', 'Cancelled'))) {
          break;
        }

        // SEND SMS CODE START

        $params = array();
      
        $params['fname'] = $smsRow['fname'];
        $params['lname'] = $smsRow['lname'];
        $params['email'] = $smsRow['email'];
        $params['phone'] = $smsRow['phone'];
        $params['ParentAgent'] = !empty($smsRow['agent_name']) ? $smsRow['agent_name'] : '';

        $smart_tags = array();

        if($brd_row['user_type'] == 'Admins'){
          $smart_tags = get_user_smart_tags($smsRow['id'],'admin');  
        }else if ($brd_row['user_type'] == 'Agents') {
          $smart_tags = get_user_smart_tags($smsRow['id'],'agent',$brd_row['product_ids']);
        }else if ($brd_row['user_type'] == 'Employer Groups') {
          $smart_tags = get_user_smart_tags($smsRow['id'],'group',$brd_row['product_ids']);
        }else if ($brd_row['user_type'] == 'Members') {
          $smart_tags = get_user_smart_tags($smsRow['id'],'member',$brd_row['product_ids']);
        }else if ($brd_row['user_type'] == 'Leads') {
          $smart_tags = get_user_smart_tags($smsRow['id'],'lead');
        }

        if($smart_tags){
            $params = array_merge($params,$smart_tags);
        }

        $smsStatus = 'fail';
        if(!empty($brdMessageRes)){
          foreach ($brdMessageRes as $msgRow) {
            $country_code = '+1';
            $toPhone = $country_code . $smsRow['phone'];
            $smsStatus = send_sms_to_phone($toPhone,$msgRow['message'],$params);
          }
        }
      
        // Insert record in customer broadcast history
        $ch_params = array(
            'sender_id' => makeSafe($brd_row['sender_id']),
            'sender_type' => makeSafe($brd_row['sender_type']),
            'user_type' => makeSafe($brd_row['user_type']),
            'global_id' => makeSafe($brd_row['id']),
            'level' => 'Global',
            'type' => 'SMS',
            'recepient' => $smsRow['phone'],
            'send_at' => 'msqlfunc_NOW()'
          );
     
        if($smsStatus == 'fail') {
          $total_fail++;
          $ch_params['is_sent'] = 'N';
        } else {

          $entityLink = $ADMIN_HOST.'/add_sms_broadcast.php?broadcaster_id='.md5($brd_row['id']);
          $entityTitle = "Broadcast ".$brd_row['display_id'];

          if($brd_row['user_type'] == 'Admins'){
              $description['ac_message'] =array(
                'ac_red_1'=>array(
                  'href'=> $entityLink,
                  'title'=>$entityTitle,
                ),
                'ac_message_1' =>' sent to',
                'ac_red_2'=>array(
                  'href'=> $ADMIN_HOST.'/admin_profile.php?id='.md5($smsRow['id']),
                  'title'=> $smsRow['userDispId'],
                ),
              );
              $desc=json_encode($description);
              activity_feed(3,$smsRow['id'], 'Admin',$brd_row['id'], 'broadcaster' , 'Broadcast Sent','','',$desc);
          }else if($brd_row['user_type'] == 'Agents'){
              $description['ac_message'] =array(
                'ac_red_1'=>array(
                  'href'=> $entityLink,
                  'title'=>$entityTitle,
                ),
                'ac_message_1' =>' sent to',
                'ac_red_2'=>array(
                  'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($smsRow['id']),
                  'title'=> $smsRow['userDispId'],
                ),
              );
              $desc=json_encode($description);
              activity_feed(3,$smsRow['id'], 'Agent',$brd_row['id'], 'broadcaster' , 'Broadcast Sent','','',$desc);
          }else if($brd_row['user_type'] == 'Employer Groups'){
               $description['ac_message'] =array(
                'ac_red_1'=>array(
                  'href'=> $entityLink,
                  'title'=>$entityTitle,
                ),
                'ac_message_1' =>' sent to',
                'ac_red_2'=>array(
                  'title'=> $smsRow['userDispId'],
                ),
              );
              $desc=json_encode($description);
              activity_feed(3,$smsRow['id'], 'Group',$brd_row['id'], 'broadcaster' , 'Broadcast Sent','','',$desc);
          }else if($brd_row['user_type'] == 'Members'){
              $description['ac_message'] =array(
                'ac_red_1'=>array(
                  'href'=> $entityLink,
                  'title'=>$entityTitle,
                ),
                'ac_message_1' =>' sent to',
                'ac_red_2'=>array(
                  'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($smsRow['id']),
                  'title'=> $smsRow['userDispId'],
                ),
              );
              $desc=json_encode($description);
              activity_feed(3,$smsRow['id'], 'Customer',$brd_row['id'], 'broadcaster' , 'Broadcast Sent','','',$desc);
          }else if($brd_row['user_type'] == 'Leads'){
             $description['ac_message'] =array(
                'ac_red_1'=>array(
                  'href'=> $entityLink,
                  'title'=>$entityTitle,
                ),
                'ac_message_1' =>' sent to',
                'ac_red_2'=>array(
                  'href'=> $ADMIN_HOST.'/lead_details.php?id='.md5($smsRow['id']),
                  'title'=> $smsRow['userDispId'],
                ),
              );
              $desc=json_encode($description);
              activity_feed(3,$smsRow['id'], 'Lead',$brd_row['id'], 'broadcaster' , 'Broadcast Sent','','',$desc);
          }

         

          $total_sent++;
          $ch_params['is_sent'] = 'Y';
        }

        $pdo->insert("communication_history", $ch_params);

        $upd_bds_params = array();
        // Update broadcaster counter and status
        if ($check_total_users <= ($total_sent + $total_fail)) {
          $upd_bds_params['is_running'] = 'N';
          $upd_bds_params['is_sent'] = 'Y';
          $broadcaster_setting_res = $pdo->selectOne("SELECT count(id) as setting_count FROM broadcaster_schedule_settings WHERE broadcaster_id = :broadcaster_id AND is_deleted = 'N' AND is_sent = 'N' AND id != :id", array(":broadcaster_id" => $brd_row['id'], ":id" => $brd_row['bss_id']));
          if(!empty($broadcaster_setting_res) && (empty($broadcaster_setting_res['setting_count']) || (!empty($broadcaster_setting_res['setting_count']) && ($broadcaster_setting_res['setting_count'] == 0)))) {
            $upd_bd_params['status'] = 'Completed';
          }
        }

        $upd_bd_wh = array('clause' => 'id = :id', 'params' => array(':id' => $brd_row['id']));
        $pdo->update('broadcaster', $upd_bd_params, $upd_bd_wh);

        $upd_bds_params['last_user_id'] = $email['id'];
        $upd_bd_wh = array('clause' => 'id = :id', 'params' => array(':id' => $brd_row['bss_id']));
        $pdo->update('broadcaster_schedule_settings', $upd_bds_params, $upd_bd_wh);
        usleep(500000);
      } // END FOR EACH LOOP

      $upd_bd_params = array('total_sent' => ($broadcaster_sent_count + $total_sent), 'total_fail' => ($broadcaster_fail_count + $total_fail));
      $upd_bd_wh = array('clause' => 'id = :id', 'params' => array(':id' => $brd_row['id']));
      $pdo->update('broadcaster', $upd_bd_params, $upd_bd_wh);
      $upd_bd_params = array('total_sent' => $total_sent, 'total_fail' => $total_fail);
      $upd_bd_wh = array('clause' => 'id = :id', 'params' => array(':id' => $brd_row['bss_id']));
      $pdo->update('broadcaster_schedule_settings', $upd_bd_params, $upd_bd_wh);
    } else {
      $broadcaster_setting_res = $pdo->selectOne("SELECT count(id) as setting_count FROM broadcaster_schedule_settings WHERE broadcaster_id = :broadcaster_id AND is_deleted = 'N' AND is_sent = 'N' AND id != :id", array(":broadcaster_id" => $brd_row['id'], ":id" => $brd_row['bss_id']));
      if(!empty($broadcaster_setting_res) && (empty($broadcaster_setting_res['setting_count']) || (!empty($broadcaster_setting_res['setting_count']) && ($broadcaster_setting_res['setting_count'] == 0)))) {
        $upd_bd_params = array('status' => 'Completed');
        $upd_bd_wh = array('clause' => 'id = :id', 'params' => array(':id' => $brd_row['id']));
        $pdo->update('broadcaster', $upd_bd_params, $upd_bd_wh);
      }

      $upd_bd_params = array('is_running' => 'N', 'is_sent' => 'Y');
      $upd_bd_wh = array('clause' => 'id = :id', 'params' => array(':id' => $brd_row['bss_id']));
      $pdo->update('broadcaster_schedule_settings', $upd_bd_params, $upd_bd_wh);
    }
  }
}
echo "<br>Completed";
dbConnectionClose();
exit;
?>