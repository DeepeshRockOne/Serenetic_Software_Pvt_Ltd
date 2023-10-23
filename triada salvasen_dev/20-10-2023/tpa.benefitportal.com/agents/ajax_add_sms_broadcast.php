<?php 
  include_once dirname(__FILE__) . '/layout/start.inc.php';
  include_once __DIR__ . '/../includes/function.class.php';
  $functionsList = new functionsList();
  $validate = new Validation();
  $result = array();
  $key_values = array(
    'N' => 'No',
    'Y' => 'Yes',
  );

  $REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
 
  $action_type = isset($_POST['action_type']) ? $_POST['action_type'] : '';
  $updBroadcasterId = checkIsset($_POST['broadcaster_id']);

  $user_group = checkIsset($_POST['user_group']);
  $is_clone = checkIsset($_POST['is_clone']);

    $product_value = checkIsset($_POST['product_ids'],'arr');
    $product_status = checkIsset($_POST['product_status']);
    $admin_level = checkIsset($_POST['admin_level'],'arr');
    $agent_status = isset($_POST['agent_status']) ? $_POST['agent_status'] : array();
    $lead_tags = checkIsset($_POST['lead_tags'],'arr');
    $lead_status = isset($_POST['lead_status']) ? $_POST['lead_status'] : "";
  $specific_user_group = (isset($_POST['specific_user_group']) && $_POST['specific_user_group'] == 'on') ? 'Y' : 'N';


    $specific_agent = checkIsset($_POST['specific_agent'],'arr');
    $specific_group = checkIsset($_POST['specific_group'],'arr');
    $specific_member = checkIsset($_POST['specific_member'],'arr');
    $specific_leads = checkIsset($_POST['specific_leads'],'arr');

  $broadcast_name = isset($_POST['broadcast_name']) ? $_POST['broadcast_name'] : '';
  $messages = checkIsset($_POST['messages'],'arr');

  $future_check_box = (isset($_POST['future_check_box']) && $_POST['future_check_box'] == 'on') ? 'Y' : 'N';

    $dynamic_fields = checkIsset($_POST['dynamic_fields'],'arr');
    $schedule_date = checkIsset($_POST['schedule_date'],'arr');
    $schedule_hour = checkIsset($_POST['schedule_hour'],'arr');
    $schedule_time_zone = checkIsset($_POST['schedule_time_zone'],'arr');

  $send_user_sms = checkIsset($_POST['send_user_sms']);
  $send_user_sms = phoneReplaceMain($send_user_sms); 

  $REAL_IP_ADDRESS = get_real_ipaddress();
  if($action_type == 'send_sms'){
    $validate->string(array('required' => true, 'field' => 'send_user_sms', 'value' => $send_user_sms), array('required' => 'Phone is required'));
  }

  if($action_type != 'send_sms'){

    $validate->string(array('required' => true, 'field' => 'user_group', 'value' => $user_group), array('required' => 'User Group is required'));

    if(!empty($user_group)){
      if($user_group == 'Leads'){
        if($specific_user_group == 'N'){
          if(empty($lead_tags)){
            $validate->setError('lead_tags',"Lead Tag is required");
          }
        } else {
          if(empty($specific_leads)){
            $validate->setError("specific_leads","Specific Lead is required");
          }
        }
      } else {
        if($specific_user_group == 'N'){
          if(empty($product_value)){
            $validate->setError("product_ids","Product(s) is required");
          }
        } else {
          if($user_group == 'Agents') {
            if(empty($specific_agent)){
              $validate->setError("specific_agent","Specific Agent(s) is required");
            }
          } else if($user_group == 'Employer Groups') {
            if(empty($specific_group)){
              $validate->setError("specific_group","Specific Employer Group(s) is required");
            }
          } else {
            if(empty($specific_member)){
              $validate->setError("specific_member","Specific Member(s) is required");
            }
          }
        }
      }
    }

    $validate->string(array('required' => true, 'field' => 'broadcast_name', 'value' => $broadcast_name), array('required' => 'Broadcast Name is required'));
    
     
      if(!empty($messages)){
        foreach ($messages as $key => $value) {
           $validate->string(array('required' => true, 'field' => 'messages_'.$key, 'value' => $value), array('required' => 'Message is required'));
        }
      }

    if($future_check_box == 'Y'){
      if(!empty($dynamic_fields)){
        foreach ($dynamic_fields as $key => $value) {
          if(empty($schedule_date[$key])){
            $validate->setError("schedule_date_".$key,"Schedule date is required");
          } else {
            $schedule_date_error = false;
            list($mm, $dd, $yyyy) = explode('/', $schedule_date[$key]);
            if (!checkdate($mm, $dd, $yyyy)) {
              $schedule_date_error = true;
              $validate->setError("schedule_date_".$key, 'Valid Schedule Date is required');
            } else {
              if(strtotime(date("Y-m-d")) > strtotime(date("Y-m-d",strtotime($schedule_date[$key])))){
              $schedule_date_error = true;
                $validate->setError("schedule_date_".$key, 'Valid Schedule Date is required');
              }
            }
          }
          if(empty($schedule_hour[$key])){
            $validate->setError("schedule_hour_".$key,"Schedule Hour is required");
          }
          if(empty($schedule_time_zone[$key])){
            $validate->setError("schedule_time_zone_".$key,"Time Zone is required");
          }

          if(!empty($schedule_date[$key]) && !$schedule_date_error && !empty($schedule_hour[$key])) {
            if((strtotime(date("Y-m-d", strtotime($schedule_date[$key]))) == strtotime(date("Y-m-d"))) && ($schedule_hour[$key] <= date('H'))){
              $validate->setError("schedule_hour_".$key,"Valid future Schedule Hour is required");
            }
          }
        }
      }
    }
  }

  if ($validate->isValid()) {

    if($action_type != 'send_sms'){

      $insert_param = array(
        'sender_id' => $_SESSION['agents']['id'],
        'sender_type' => "Agent",
        'brodcast_name' => $broadcast_name,
        'type' => "sms",
        'user_type' => $user_group,
        'is_for_specific' => $specific_user_group,
        'is_schedule_in_future' => $future_check_box,
      );

      if($user_group == 'Leads'){
        if($specific_user_group == 'N'){
          if(!empty($lead_tags)){
            $insert_param['admin_level'] = NULL;
            $insert_param['lead_tags'] = implode(',', $lead_tags);
            $insert_param['lead_status'] = checkIsset($lead_status);
            $insert_param['specific_user_ids'] = NULL;
            $insert_param['product_ids'] = NULL;
            $insert_param['product_status'] = NULL;
          }
        } else {
          if(!empty($specific_leads)){
            $insert_param['admin_level'] = NULL;
            $insert_param['lead_tags'] = NULL;
            $insert_param['specific_user_ids'] = implode(',', $specific_leads);
            $insert_param['product_ids'] = NULL;
            $insert_param['product_status'] = NULL;
          }
        }
      } else {
        if($specific_user_group == 'N'){
          $insert_param['admin_level'] = NULL;
          $insert_param['lead_tags'] = NULL;
          $insert_param['specific_user_ids'] = NULL;
          if(!empty($product_value)){
            $insert_param['product_ids'] = implode(',', $product_value);
          }
          if(!empty($product_status)){
            $insert_param['product_status'] = $product_status;
          } else {
            $insert_param['product_status'] = NULL;
          }
          if($user_group == 'Agents' && count($agent_status) > 0){
            $insert_param['agent_status'] = implode(',', $agent_status);
          } else {
            $insert_param['agent_status'] = NULL;
          }
        } else {
          if($user_group == 'Agents'){
            if(!empty($specific_agent)){
              $insert_param['specific_user_ids'] = implode(',', $specific_agent);
            }
          } else if($user_group == 'Employer Groups'){
            if(!empty($specific_group)){
              $insert_param['specific_user_ids'] = implode(',', $specific_group); 
            }
          } else {
            if(!empty($specific_member)){
              $insert_param['specific_user_ids'] = implode(',', $specific_member); 
            }
          }
          $insert_param['admin_level'] = NULL;
          $insert_param['lead_tags'] = NULL;
          $insert_param['product_ids'] = NULL;
          $insert_param['product_status'] = NULL;
        }
      }

      if($action_type == 'draft'){
        $insert_param['status'] = 'Draft';
      } else if($action_type == 'send') {
        $insert_param['status'] = 'Pending';
      }

      if(!empty($updBroadcasterId) && $is_clone == 'N'){

        $broadcaster_res = $pdo->selectOne("SELECT id,display_id,brodcast_name,from_address,subject,mail_content,user_type,is_for_specific,email_template_id,is_schedule_in_future,admin_level,specific_user_ids,lead_tags,product_ids,product_status,status FROM broadcaster WHERE id = :broadcaster_id", array(":broadcaster_id" => $updBroadcasterId));

        $upd_where = array(
          'clause' => 'id = :id',
          'params' => array(
            ':id' => $updBroadcasterId,
          ),
        );
        $pdo->update('broadcaster', $insert_param, $upd_where);

        $broadcaster_id = $updBroadcasterId;

        $broadcaster_schedule_setting_res = $pdo->select("SELECT id,schedule_date,schedule_hour,time_zone FROM broadcaster_schedule_settings WHERE is_deleted = 'N' AND broadcaster_id = :broadcaster_id", array(":broadcaster_id" => $updBroadcasterId));

        $old_schedule_date_array = array();
        $new_schedule_date_array = array();
        $setting_res = array();
        if(!empty($broadcaster_schedule_setting_res) && count($broadcaster_schedule_setting_res) > 0){
          foreach ($broadcaster_schedule_setting_res as $key => $value) {
            if(!in_array($value['id'], $setting_res)){
              array_push($setting_res, $value['id']);
              $broadcaster_res[$value['id']] = date("m/d/Y h:i A", strtotime($value['schedule_date'] .' '. $value['schedule_hour'].':00:00')) . " " . $value['time_zone'];
            }
          }
        }


        // broadcater messgae update
        $msgKeys = array_keys($messages);
        $updMsgid = array();
   
        $broadcasterMsg = $pdo->selectGroup("SELECT id,broadcaster_id,message FROM broadcaster_message WHERE is_deleted='N' AND broadcaster_id = :broadcaster_id", array(":broadcaster_id" => $updBroadcasterId));
          if(!empty($broadcasterMsg)){
            foreach ($broadcasterMsg as $key => $value) {
              if(in_array($value['id'],$msgKeys)){
                array_push($updMsgid, $value['id']);
              }
            }
          }

 
          $insUpdMsgIds = array();
          if(!empty($messages)){
            foreach ($messages as $key => $value) {
              if(in_array($key, $updMsgid)){
                $oldMsg = getname('broadcaster_message',$key,'message','id');

                $updMsgParam = array(
                  'message' => $value,
                );
                $updWhere = array(
                  'clause' => 'id = :id',
                  'params' => array(
                    ':id' => $key,
                  ),
                );
                $pdo->update('broadcaster_message', $updMsgParam, $updWhere);
                array_push($insUpdMsgIds, $key);
                $search_key = array_search($key,$updMsgid);
                if($search_key !== false){
                  unset($updMsgid[$search_key]);
                }
                
                $oldVaArray = array("message" => $oldMsg);
                $NewVaArray = array("message" => $value);
              
                $activity = array_diff_assoc($oldVaArray, $NewVaArray);
              
                $tmp = array();
                $tmp2 = array();
                if(!empty($activity)){
                    $tmp['display_desc_2']=base64_encode($oldMsg);
                    $tmp2['display_desc_2']=base64_encode($value);

                    $link = $AGENT_HOST.'/add_sms_broadcast.php?broadcaster_id='. md5($updBroadcasterId);
                  
                    $actFeed=$functionsList->generalActivityFeed($tmp,$tmp2,$link,$broadcaster_res['display_id'],$key,'broadcaster_message','Agent Updated SMS Broadcaster','updated message on SMS Broadcaster');
                }

              }else{
                $insMsgParam = array(
                  'broadcaster_id' => $updBroadcasterId,
                  'message' => $value,
                );

                $msgId = $pdo->insert('broadcaster_message',$insMsgParam);
                array_push($insUpdMsgIds, $msgId);

                $tmp['display_desc_2']="-";
                $tmp2['display_desc_2']=base64_encode($value);

                $link = $AGENT_HOST.'/add_sms_broadcast.php?broadcaster_id='. md5($updBroadcasterId);
                
                $actFeed=$functionsList->generalActivityFeed($tmp,$tmp2,$link,$broadcaster_res['display_id'],$key,'broadcaster_message','Agent Updated SMS Broadcaster','added message on SMS Broadcaster');

              }
            }
          }
          if(!empty($insUpdMsgIds)){
            $removedMsgIds = implode(",", $insUpdMsgIds);

            $updateParams = array('is_deleted'=>'Y');
            $updateWhere = array(
                            'clause' => 'broadcaster_id=:id AND id NOT IN('.$removedMsgIds.') AND is_deleted="N"',
                            'params' => array(
                              ":id" => $updBroadcasterId,
                            )
                          );
            $pdo->update('broadcaster_message',$updateParams,$updateWhere);
          }

      }else{
        $display_id=$functionsList->generateBroadcasterDisplayID();

        $insert_param['display_id'] = $display_id;
        $broadcaster_id = $pdo->insert('broadcaster',$insert_param);

        if(!empty($messages)){
          foreach ($messages as $key => $msg) {
              $msg_param = array(
                'broadcaster_id' => $broadcaster_id,
                'message' => $msg,
              );

              $msgId = $pdo->insert('broadcaster_message',$msg_param);
          }
        }
      }

      if($future_check_box == 'N'){
        $min = date('i');
        $date = date("Y-m-d");
        $hour = (($min > 0) ? date("H",strtotime("+1 hour")) : date("H")); 
        $convertedDate = $date .' '. $hour.":00:00";
        $insert_setting_param = array(
          'broadcaster_id' => $broadcaster_id,
          'schedule_date' => $date,
          'schedule_hour' => $hour,
          'time_zone' => "CST",
          'converted_date' => date("Y-m-d H:i:s",strtotime($convertedDate)),
          'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
          'updated_at' => 'msqlfunc_NOW()'
        );

        $pdo->insert('broadcaster_schedule_settings', $insert_setting_param);
      } else {
        if(!empty($dynamic_fields)){
          foreach ($dynamic_fields as $key => $value) {
            $insert_param[$key] = date("m/d/Y h:i A", strtotime($schedule_date[$key] .' '. $schedule_hour[$key] . ':00:00')) . " " . $schedule_time_zone[$key];
            if($key > 0){
              if(in_array($key, $setting_res)){
                $check_schedule = $schedule_date[$key] . " " . $schedule_hour[$key] . ":00" ;
                $converted_date = convertTimeZone ($check_schedule, $schedule_time_zone[$key], "CST");
                $insert_setting_param = array(
                  'broadcaster_id' => $broadcaster_id,
                  'schedule_date' => date("Y-m-d", strtotime($schedule_date[$key])),
                  'schedule_hour' => $schedule_hour[$key],
                  'time_zone' => $schedule_time_zone[$key],
                  'converted_date' => $converted_date,
                  'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                  'updated_at' => 'msqlfunc_NOW()'
                );
                $upd_where = array(
                  'clause' => 'id = :id',
                  'params' => array(
                    ':id' => $key,
                  ),
                );
                $pdo->update('broadcaster_schedule_settings', $insert_setting_param, $upd_where);
                $search_key = array_search($key,$setting_res);
                if($search_key !== false){
                  unset($setting_res[$search_key]);
                }
              }
            } else {
              $check_schedule = $schedule_date[$key] . " " . $schedule_hour[$key] . ":00" ;
              $converted_date = convertTimeZone ($check_schedule, $schedule_time_zone[$key], "CST");
              $insert_setting_param = array(
                'broadcaster_id' => $broadcaster_id,
                'schedule_date' => date("Y-m-d", strtotime($schedule_date[$key])),
                'schedule_hour' => $schedule_hour[$key],
                'time_zone' => $schedule_time_zone[$key],
                'converted_date' => $converted_date,
                'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                'updated_at' => 'msqlfunc_NOW()'
              );
              $pdo->insert('broadcaster_schedule_settings', $insert_setting_param);
            }
          }
        }
      }

      if(!empty($setting_res)){
        foreach ($setting_res as $key => $value) {
          $update_setting_param = array(
            'is_deleted' => 'Y',
            'updated_at' => 'msqlfunc_NOW()'
          );
          $upd_where_param = array(
            'clause' => 'id = :id',
            'params' => array(
              ':id' => $value,
            ),
          );

          $insert_param[$value] = '';

          $pdo->update('broadcaster_schedule_settings', $update_setting_param, $upd_where_param);
        }
      }

    // Activity feed code start
      if(isset($updBroadcasterId) && !empty($updBroadcasterId) && $is_clone == 'N'){
        $oldVaArray = $broadcaster_res;
        $NewVaArray = $insert_param;
        unset($oldVaArray['id']);
        unset($NewVaArray['type']);

        $checkDiff=array_diff_assoc($NewVaArray, $oldVaArray);
        if(!empty($checkDiff)){
          $activityFeedDesc['ac_message'] =array(
            'ac_red_1'=>array(
              'href'=>$ADMIN_HOST.'/agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
              'title'=>$_SESSION['agents']['rep_id'],
            'ac_message_1' =>' Updated Email Broadcaster ',
          )); 
          
          $extraJson = array();

          if(isset($checkDiff['updated_at']) && !empty($checkDiff['updated_at'])){
            unset($checkDiff['updated_at']);
          }

          $user_group_array = array();
          $product_ids_array = array();  
          $sel_customer = "SELECT id,rep_id,type,CONCAT(fname,' ', lname) as name FROM customer WHERE is_deleted='N' AND status NOT IN ('Customer Abandon','Pending Quote','Invited') AND type IN ('Agent','Group','Customer')";
          $res_customer = $pdo->select($sel_customer);

          if(!empty($res_customer) && count($res_customer) > 0) {
            foreach ($res_customer as $key => $value) {
              if($value['type'] == 'Agent'){
                $user_type_name = 'Agents';
              } else if($value['type'] == 'Group'){
                $user_type_name = 'Employer Groups';
              } else {
                $user_type_name = 'Members';
              }
              $user_group_array[$user_type_name][$value['id']] = $value['name'] . ' ('. $value['rep_id'].')';
            }
          }
            
          $sel_admins = "SELECT id,display_id,CONCAT(fname,' ', lname) as name FROM admin WHERE is_active='Y' AND is_deleted='N' AND status = 'Active'";
          $res_admins = $pdo->select($sel_admins);
          if(!empty($res_admins) && count($res_admins) > 0) {
            foreach ($res_admins as $key => $value) {
              $user_group_array['Admins'][$value['id']] = $value['name'] . ' ('. $value['display_id'].')';
            }
          }
              
          $lead_tag_sql = "SELECT id,lead_id,CONCAT(fname,' ',lname) as name FROM leads  WHERE is_deleted='N' AND status NOT IN ('Converted',' ','NULL')";
          $lead_res = $pdo->select($lead_tag_sql);
          if(!empty($lead_res) && count($lead_res) > 0){
            foreach ($lead_res as $key => $value) {
              $user_group_array['Leads'][$value['id']] = $value['name'] . ' ('. $value['lead_id'].')';
            }
          }
            
          $select_product = "SELECT id,name,product_code FROM prd_main WHERE status = 'Active' AND is_deleted = 'N' ORDER BY name ASC";
          $product_res = $pdo->select($select_product);

          if(!empty($product_res) && count($product_res) > 0){
            foreach ($product_res as $key => $row) {
              $product_ids_array[$row['id']] = $row['name'] . ' (' . $row['product_code'] . ')';
            }
          }

          $date_counter = 1;
          foreach ($checkDiff as $key1 => $value1) {

            if($key1 == 'admin_id'){
              if(isset($oldVaArray[$key1])){
                if(!empty($user_group_array['Admins'][$oldVaArray[$key1]])) {
                  $oldVaArray[$key1] = $user_group_array['Admins'][$oldVaArray[$key1]];
                }
              }

              if(isset($NewVaArray[$key1])){
                if(!empty($user_group_array['Admins'][$NewVaArray[$key1]])) {
                  $NewVaArray[$key1] = $user_group_array['Admins'][$NewVaArray[$key1]];
                }
              }
            }


            if($key1 == 'product_ids'){
              if(isset($oldVaArray[$key1])){
                $old_value_of_product_ids_array = explode(",", $oldVaArray[$key1]);
                $old_value_of_product_name_array = array();
                if(!empty($old_value_of_product_ids_array) && count($old_value_of_product_ids_array) > 0) {
                  foreach ($old_value_of_product_ids_array as $key => $value) {
                    if(!empty($product_ids_array[$value])){
                      array_push($old_value_of_product_name_array, $product_ids_array[$value]);
                    }
                  }
                }
                if(!empty($old_value_of_product_name_array)){
                  $oldVaArray[$key1] = implode(",", $old_value_of_product_name_array);
                }
              }
              if(isset($NewVaArray[$key1])){
                $new_value_of_product_ids_array = explode(",", $NewVaArray[$key1]);
                $new_value_of_product_name_array = array();
                if(!empty($new_value_of_product_ids_array) && count($new_value_of_product_ids_array) > 0) {
                  foreach ($new_value_of_product_ids_array as $key => $value) {
                    if(!empty($product_ids_array[$value])){
                      array_push($new_value_of_product_name_array, $product_ids_array[$value]);
                    }
                  }
                }
                if(!empty($new_value_of_product_name_array)){
                  $NewVaArray[$key1] = implode(",", $new_value_of_product_name_array);
                }
              }
            }

            if($key1 == 'specific_user_ids'){
              if(isset($oldVaArray[$key1])){
                $old_value_of_specific_user_ids_array = explode(",", $oldVaArray[$key1]);
                $old_value_of_specific_user_name_array = array();
                if(!empty($old_value_of_specific_user_ids_array) && count($old_value_of_specific_user_ids_array) > 0) {
                  foreach ($old_value_of_specific_user_ids_array as $key => $value) {
                    if(!empty($user_group_array[$broadcaster_res['user_type']][$value])){
                      array_push($old_value_of_specific_user_name_array, $user_group_array[$broadcaster_res['user_type']][$value]);
                    }
                  }
                }
                if(!empty($old_value_of_specific_user_name_array)){
                  $oldVaArray[$key1] = implode(",", $old_value_of_specific_user_name_array);
                }
              }
              if(isset($NewVaArray[$key1])){
                $new_value_of_specific_user_ids_array = explode(",", $NewVaArray[$key1]); 
                $new_value_of_specific_user_name_array = array();
                if(!empty($new_value_of_specific_user_ids_array) && count($new_value_of_specific_user_ids_array) > 0) {
                  foreach ($new_value_of_specific_user_ids_array as $key => $value) {
                    if(!empty($user_group_array[$broadcaster_res['user_type']][$value])){
                      array_push($new_value_of_specific_user_name_array, $user_group_array[$broadcaster_res['user_type']][$value]);
                    }
                  }
                }
                if(!empty($new_value_of_specific_user_name_array)){
                  $NewVaArray[$key1] = implode(",", $new_value_of_specific_user_name_array);
                }
              } 
            }

            if(!isset($oldVaArray[$key1]) && isset($NewVaArray[$key1])){
              if(!empty($key_values[$NewVaArray[$key1]])) {
                $NewVaArray[$key1] = $key_values[$NewVaArray[$key1]];
              }
              if(is_numeric($key1)){
                $activityFeedDesc['key_value']['desc_arr']['schedule_date_'.$date_counter] = 'Added '.$NewVaArray[$key1];
                $date_counter++;
              } else {
                $activityFeedDesc['key_value']['desc_arr'][$key1] = 'Added '.$NewVaArray[$key1];
              }
            } else if(isset($oldVaArray[$key1]) && !isset($NewVaArray[$key1])){
              if(!empty($key_values[$oldVaArray[$key1]])) {
                $oldVaArray[$key1] = $key_values[$oldVaArray[$key1]];
              }
              if(is_numeric($key1)){
                $activityFeedDesc['key_value']['desc_arr']['schedule_date_'.$date_counter] = 'Deleted '.$oldVaArray[$key1];
                $date_counter++;
              } else {
                $activityFeedDesc['key_value']['desc_arr'][$key1] = 'Deleted '.$oldVaArray[$key1];
              }
            } else if(isset($oldVaArray[$key1]) && isset($NewVaArray[$key1])){

              if(!empty($NewVaArray[$key1])){
                if(!empty($key_values[$oldVaArray[$key1]])) {
                  $oldVaArray[$key1] = $key_values[$oldVaArray[$key1]];
                }
                if(!empty($key_values[$NewVaArray[$key1]])) {
                  $NewVaArray[$key1] = $key_values[$NewVaArray[$key1]];
                }
                if(is_numeric($key1)){
                  $activityFeedDesc['key_value']['desc_arr']['schedule_date_'.$date_counter] = 'From '.$oldVaArray[$key1].' To '.$NewVaArray[$key1];
                  $date_counter++;
                } else {
                  $activityFeedDesc['key_value']['desc_arr'][$key1] = 'From '.$oldVaArray[$key1].' To '.$NewVaArray[$key1];
                }
              } else {
                if(!empty($key_values[$oldVaArray[$key1]])) {
                  $oldVaArray[$key1] = $key_values[$oldVaArray[$key1]];
                }
                if(is_numeric($key1)){
                  $activityFeedDesc['key_value']['desc_arr']['schedule_date_'.$date_counter] = 'Deleted '.$oldVaArray[$key1];
                  $date_counter++;
                } else {
                  $activityFeedDesc['key_value']['desc_arr'][$key1] = 'Deleted '.$oldVaArray[$key1];
                }
              }
            }
          }

          $activityFeedDesc['ac_message']['ac_red_2']=array(
            'href'=>$AGENT_HOST.'/add_sms_broadcast.php?broadcaster_id='.md5($updBroadcasterId),
            'title'=>$broadcaster_res['display_id']
          ); 

          activity_feed(3, $_SESSION['agents']['id'], 'Agent', $updBroadcasterId, 'broadcaster','Agent Updated SMS Broadcaster', $_SESSION['agents']['fname'],$_SESSION['agents']['lname'],json_encode($activityFeedDesc),'',json_encode($extraJson));
        }
      } else {
        $description['ac_message'] =array(
          'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
            'title'=>$_SESSION['agents']['rep_id'],
          ),
          'ac_message_1' =>' Created SMS Broadcaster ',
          'ac_red_2'=>array(
              'href'=>$AGENT_HOST.'/add_sms_broadcast.php?broadcaster_id='.md5($broadcaster_id),
              'title'=>$display_id,
          ),
        );

        activity_feed(3, $_SESSION['agents']['id'], 'Agent', $broadcaster_id, 'broadcaster','Agent Created SMS Broadcaster', $_SESSION['agents']['fname'],$_SESSION['agents']['lname'],json_encode($description));
      }
    // Activity feed code ends
    }

    if(!empty($action_type) && $action_type == 'send_sms' && !empty($send_user_sms)){
      $smart_tags = get_user_smart_tags($_SESSION['agents']['id'],'agent');
      if(!empty($messages)){
        foreach ($messages as $key => $msg) {
          $country_code = '+1';
          $send_user_sms = $country_code . $send_user_sms;
          send_sms_to_phone($send_user_sms,$msg,$smart_tags);
        }
      }
    }

    $result['action_type'] = $action_type;
    $result['status'] = "success";
  } else {
    $errors = $validate->getErrors();
    $result['errors'] = $errors;
    $result['status'] = "fail";
  }

  header('Content-type: application/json');
  echo json_encode($result);
  dbConnectionClose(); 
  exit;
?>