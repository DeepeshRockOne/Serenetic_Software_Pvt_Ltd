<?php include_once dirname(__FILE__) . '/layout/start.inc.php';
  $validate = new Validation();
  $result = array();
  $key_values = array(
    'N' => 'No',
    'Y' => 'Yes',
  );

  $REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
  $is_clone = isset($_POST['is_clone']) ? $_POST['is_clone'] : 'N';

  $update_broadcaster_id = isset($_POST['broadcaster_id']) ? $_POST['broadcaster_id'] : '';
  $email_attachment_id = isset($_POST['email_attachment_id']) ? $_POST['email_attachment_id'] : array();

  $user_group = isset($_POST['user_group']) ? $_POST['user_group'] : '';
  $broadcast_name = isset($_POST['broadcast_name']) ? $_POST['broadcast_name'] : '';
  $broadcast_from_address = isset($_POST['broadcast_from_address']) ? $_POST['broadcast_from_address'] : '';
  $broadcast_from_name = isset($_POST['broadcast_from_name']) ? $_POST['broadcast_from_name'] : '';
  $broadcast_subject = isset($_POST['broadcast_subject']) ? $_POST['broadcast_subject'] : '';
  $broadcast_content = isset($_POST['broadcast_content']) ? trim($_POST['broadcast_content']) : '';
  $specific_user_group = (isset($_POST['specific_user_group']) && $_POST['specific_user_group'] == 'on') ? 'Y' : 'N';
  $future_check_box = (isset($_POST['future_check_box']) && $_POST['future_check_box'] == 'on') ? 'Y' : 'N';
  $action_type = isset($_POST['action_type']) ? $_POST['action_type'] : '';
  $email_template = isset($_POST['email_template']) ? $_POST['email_template'] : '';

  $product_value = isset($_POST['product_value']) ? $_POST['product_value'] : array();
  $product_status = isset($_POST['product_status']) ? $_POST['product_status'] : '';
  $admin_level = isset($_POST['admin_level']) ? $_POST['admin_level'] : array();
  $lead_tags = isset($_POST['lead_tags']) ? $_POST['lead_tags'] : array();
  $agent_level = isset($_POST['agent_level']) ? $_POST['agent_level'] : array();
  $agent_status = isset($_POST['agent_status']) ? $_POST['agent_status'] : array();
  $group_agent_ids = isset($_POST['group_agent_ids']) ? $_POST['group_agent_ids'] : array();
  $group_agent_tree_ids = isset($_POST['group_agent_tree_ids']) ? $_POST['group_agent_tree_ids'] : array();
  $lead_agent_ids = isset($_POST['lead_agent_ids']) ? $_POST['lead_agent_ids'] : array();
  $lead_agent_tree_ids = isset($_POST['lead_agent_tree_ids']) ? $_POST['lead_agent_tree_ids'] : array();
  $member_state = isset($_POST['member_state']) ? $_POST['member_state'] : array();
  $lead_state = isset($_POST['lead_state']) ? $_POST['lead_state'] : array();

  // pre_print($agent_level);

  $specific_admin = isset($_POST['specific_admin']) ? implode(',',$_POST['specific_admin']) : array();
  $specific_agents = isset($_POST['specific_agents']) ? implode(',',$_POST['specific_agents']) : array();
  $specific_group = isset($_POST['specific_group']) ? implode(',',$_POST['specific_group']) : array();
  $specific_member = isset($_POST['specific_member']) ? implode(',',$_POST['specific_member']) : array();
  $specific_leads = isset($_POST['specific_leads']) ? implode(',',$_POST['specific_leads']) : array();

  $dynamic_fields = !empty($_POST['dynamic_fields']) ? $_POST['dynamic_fields'] : array();
  $schedule_date = isset($_POST['schedule_date']) ? $_POST['schedule_date'] : array();
  $schedule_hour = isset($_POST['schedule_hour']) ? $_POST['schedule_hour'] : array();
  $schedule_time_zone = isset($_POST['schedule_time_zone']) ? $_POST['schedule_time_zone'] : array();

  $send_user_email = checkIsset($_POST['send_user_email']);
  $upload_type = checkIsset($_POST['upload_type']);
  $attachments = checkIsset($_FILES['attachment'],'arr');

  $REAL_IP_ADDRESS = get_real_ipaddress();
  if($upload_type=="file"){
    if(!empty($attachments)){
      $length = count($attachments['name']);
          
      $attachments_array = array();
      for($i=0; $i<$length; $i++){
        $attachments_array[$i]['name'] = $attachments['name'][$i];
        $attachments_array[$i]['type'] = $attachments['type'][$i];
        $attachments_array[$i]['tmp_name'] = $attachments['tmp_name'][$i];
        $attachments_array[$i]['error'] = $attachments['error'][$i];
        $attachments_array[$i]['size'] = $attachments['size'][$i];
      }
        
      if(!empty($attachments_array)){
        $files_info=array();
        $i=0;
        foreach($attachments_array as $file){
          $ticket_file = $file['name'];
          if (!empty($ticket_file)) {
            $file_type = explode("/",$file['type']);

            $file_name = $update_broadcaster_id.'_'.rand(1000, 9999).'_'.$file['name'];
            $file_path = $ATTACHMENS_DIR . $file_name;
            if (!file_exists($ATTACHMENS_DIR)) {
              mkdir($ATTACHMENS_DIR, 0777, true);
            }
            move_uploaded_file($file['tmp_name'], $file_path);
            chmod($file_path, 0777);

            $attachment_params = array(
              'broadcast_id' => ($update_broadcaster_id && $is_clone == 'N')?$update_broadcaster_id:0,
              'file_name' => $file_name,
              'file_path' => $file_path,
              'file_type' => $file['type'],
              'is_deleted' => 'N', 
            );
            $attachment_id = $pdo->insert("email_attachment", $attachment_params);
            array_push($email_attachment_id, $attachment_id);
              
            $imageExt=array_reverse(explode(".", $file_name));
            if(strtolower($imageExt[0])=="jpg" || strtolower($imageExt[0])=="jpeg" || strtolower($imageExt[0])=="png" || strtolower($imageExt[0])=="gif" || strtolower($imageExt[0])=="tif"){
              $file_display_name = $file_name;
            }else{
              $file_display_name="img_placeholder.jpg";
            }

            $file_id = $attachment_id;
            $files_info[$i]['file_display_name']=$file_display_name;
            $files_info[$i]['file_name']=$file_name;
            $files_info[$i]['file_id']=$file_id;
            $i++;
            $response['attachment']=implode(",", $email_attachment_id);

          }
        }
        $response['status']="success_file";
        $response['files_info']=$files_info;
        $response['message']="Attachment Added successfully";
      }
      echo json_encode($response);
      exit();
    }
  }else{

    $validate->string(array('required' => true, 'field' => 'broadcast_content', 'value' => $broadcast_content), array('required' => 'Content is required'));
    $validate->string(array('required' => true, 'field' => 'broadcast_subject', 'value' => $broadcast_subject), array('required' => 'Subject is required'));
    $validate->string(array('required' => true, 'field' => 'email_template', 'value' => $email_template), array('required' => 'Template is required'));

    if(!empty($action_type) && $action_type == 'send_email'){
      $validate->email(array('required' => true, 'field' => 'send_user_email', 'value' => $send_user_email), array('required' => 'Email is required','invalid'=>'Valid email is required'));
    }

    if(empty($action_type) || (!empty($action_type)) && $action_type != 'send_email'){
      $validate->string(array('required' => true, 'field' => 'user_group', 'value' => $user_group), array('required' => 'User Group is required'));
      $validate->string(array('required' => true, 'field' => 'broadcast_name', 'value' => $broadcast_name), array('required' => 'Broadcast Name is required'));
      $validate->string(array('required' => true, 'field' => 'broadcast_from_address', 'value' => $broadcast_from_address), array('required' => 'From Address is required'));
      $validate->string(array('required' => true, 'field' => 'broadcast_from_name', 'value' => $broadcast_from_name), array('required' => 'From Name is required'));

      if(!$validate->getError('broadcast_from_address')){
        if(!preg_match('/^([a-z0-9]+)([\._-][a-z0-9]+)*@([a-z0-9]+)(\.)+[a-z]{2,6}$/ix', trim($broadcast_from_address))) {
              $validate->setError("broadcast_from_address", "Please enter valid from address");
        }
      }

      if(!empty($user_group)){
        if($user_group == 'Admins'){
          if($specific_user_group == 'N'){
            if(empty($admin_level)){
              $validate->setError('admin_level',"Admin Level is required");
            }
          } else {
            if(empty($specific_admin)){
              $validate->setError("specific_admin","Specific Admin is required");
            }
          }
        } else if($user_group == 'Leads'){
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
              $validate->setError("product_value","Product(s) is required");
            }
            // $validate->string(array('required' => true, 'field' => 'product_status', 'value' => $product_status), array('required' => 'Product Status is required'));
          } else {
            if($user_group == 'Agents') {
              if(empty($specific_agents)){
                $validate->setError("specific_agents","Specific Agent(s) is required");
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
      if(empty($action_type) || (!empty($action_type)) && $action_type != 'send_email'){

        $insert_param = array(
          'sender_id' => $_SESSION['admin']['id'],
          'sender_type' => "Admin",
          'type' => 'email',
          'brodcast_name' => $broadcast_name,
          'from_address' => $broadcast_from_address,
          'from_name' => $broadcast_from_name,
          'subject' => $broadcast_subject,
          'mail_content' => $broadcast_content,
          'user_type' => $user_group,
          'is_for_specific' => $specific_user_group,
          'email_template_id' => $email_template,
          'is_schedule_in_future' => $future_check_box,
          'updated_at' => 'msqlfunc_NOW()'
        );

        if($user_group == 'Admins'){
          if($specific_user_group == 'N'){
            if(!empty($admin_level)){
              $insert_param['admin_level'] = implode(',', $admin_level);
              $insert_param['lead_tags'] = NULL;
              $insert_param['specific_user_ids'] = NULL;
              $insert_param['product_ids'] = NULL;
              $insert_param['product_status'] = NULL;
            }
          } else {
            if(!empty($specific_admin)){
              $insert_param['admin_level'] = NULL;
              $insert_param['lead_tags'] = NULL;
              $insert_param['specific_user_ids'] = $specific_admin;
              $insert_param['product_ids'] = NULL;
              $insert_param['product_status'] = NULL;
            }
          }
        } else if($user_group == 'Leads'){
          if($specific_user_group == 'N'){
            if(!empty($lead_tags)){
              $insert_param['admin_level'] = NULL;
              $insert_param['lead_tags'] = implode(',', $lead_tags);
              $insert_param['specific_user_ids'] = NULL;
              $insert_param['product_ids'] = NULL;
              $insert_param['product_status'] = NULL;
            }
            if(count($lead_agent_ids) > 0){
              $insert_param['enrolling_agent_ids'] = implode(',', $lead_agent_ids);  
            }
            if(count($lead_agent_tree_ids) > 0){
              $insert_param['tree_agent_ids'] = implode(',', $lead_agent_tree_ids);
            }
            if(count($lead_state) > 0){
              $insert_param['states'] = implode(',', $lead_state);
            }
          } else {
            if(!empty($specific_leads)){
              $insert_param['admin_level'] = NULL;
              $insert_param['lead_tags'] = NULL;
              $insert_param['specific_user_ids'] = $specific_leads;
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
            if($user_group == 'Agents' && count($agent_level) > 0){
              $insert_param['specific_agent_level'] = implode(',', $agent_level);  
            }
            if($user_group == 'Agents' && count($agent_status) > 0){
              $insert_param['agent_status'] = implode(',', $agent_status);  
            } else {
              $insert_param['agent_status'] = NULL;
            }
            if(in_array($user_group, array('Employer Groups','Members')) && count($group_agent_ids) > 0){
              $insert_param['enrolling_agent_ids'] = implode(',', $group_agent_ids);  
            }
            if(in_array($user_group, array('Employer Groups','Members')) && count($group_agent_tree_ids) > 0){
              $insert_param['tree_agent_ids'] = implode(',', $group_agent_tree_ids);  
            }
            if($user_group == 'Members' && count($member_state) > 0){
              $insert_param['states'] = implode(',', $member_state);
            }
          } else {
            if($user_group == 'Agents'){
              if(!empty($specific_agents)){
                $insert_param['specific_user_ids'] = $specific_agents;
              }
            } else if($user_group == 'Employer Groups'){
              if(!empty($specific_group)){
                $insert_param['specific_user_ids'] = $specific_group; 
              }
            } else {
              if(!empty($specific_member)){
                $insert_param['specific_user_ids'] = $specific_member; 
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

        if(!empty($update_broadcaster_id) && $is_clone == 'N'){

          $broadcaster_res = $pdo->selectOne("SELECT id,display_id,brodcast_name,from_address,subject,mail_content,user_type,is_for_specific,email_template_id,is_schedule_in_future,admin_level,specific_user_ids,lead_tags,product_ids,product_status,status FROM broadcaster WHERE id = :broadcaster_id", array(":broadcaster_id" => $update_broadcaster_id));

          $upd_where = array(
            'clause' => 'id = :id',
            'params' => array(
              ':id' => $update_broadcaster_id,
            ),
          );
          $pdo->update('broadcaster', $insert_param, $upd_where);

          $broadcaster_id = $update_broadcaster_id;

          if(!empty($email_attachment_id)){
            foreach ($email_attachment_id as $key => $value) {
              $updParam = array('broadcast_id'=>$broadcaster_id);
              $updWhere = array(
                'clause' => 'id = :id',
                'params' => array(':id' => $value)
              );
              $pdo->update('email_attachment', $updParam, $updWhere);
            }
          }

          $broadcaster_schedule_setting_res = $pdo->select("SELECT id,schedule_date,schedule_hour,time_zone FROM broadcaster_schedule_settings WHERE is_deleted = 'N' AND broadcaster_id = :broadcaster_id", array(":broadcaster_id" => $update_broadcaster_id));

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
        } else {
          include_once __DIR__ . '/../includes/function.class.php';
          $functionsList = new functionsList();
          $display_id=$functionsList->generateBroadcasterDisplayID();

          $insert_param['display_id'] = $display_id;

          $broadcaster_id = $pdo->insert('broadcaster',$insert_param);

          if(!empty($email_attachment_id)){
            $email_attachment_id = explode(',', $email_attachment_id[0]);
            foreach ($email_attachment_id as $key => $value) {
              $updParam = array('broadcast_id'=>$broadcaster_id);
              $updWhere = array(
                'clause' => 'id = :id',
                'params' => array(':id' => $value)
              );
              $pdo->update('email_attachment', $updParam, $updWhere);
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
            'converted_date' => $convertedDate,
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

        if(isset($update_broadcaster_id) && !empty($update_broadcaster_id) && $is_clone == 'N'){
          $oldVaArray = $broadcaster_res;
          $NewVaArray = $insert_param;
          unset($oldVaArray['id']);

          $checkDiff=array_diff_assoc($NewVaArray, $oldVaArray);
          if(!empty($checkDiff)){
            $activityFeedDesc['ac_message'] =array(
              'ac_red_1'=>array(
                'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                'title'=>$_SESSION['admin']['display_id']),
              'ac_message_1' =>' Updated Email Broadcaster ',
            ); 
            
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

              if($key1 == 'email_template_id'){
                $template_id_array = array();
                if(isset($oldVaArray[$key1])){
                  if(!empty($oldVaArray[$key1]) && !in_array($oldVaArray[$key1], $template_id_array)){
                    array_push($template_id_array, $oldVaArray[$key1]);
                  }
                }

                if(isset($NewVaArray[$key1])){
                  if(!empty($NewVaArray[$key1]) && !in_array($NewVaArray[$key1], $template_id_array)){
                    array_push($template_id_array, $NewVaArray[$key1]);
                  }
                }

                if(!empty($template_id_array) && count($template_id_array) > 0){
                  $templateSql = "SELECT id,title FROM trigger_template WHERE is_deleted='N' AND id IN (". implode(",", $template_id_array) .") ";
                  $templatedata = $pdo->select($templateSql);
                  if(!empty($templatedata) && count($templatedata) > 0){
                    foreach ($templatedata as $key => $value) {
                      if($value['id'] == $oldVaArray[$key1]) {
                        $oldVaArray[$key1] = $value['title'];
                      } else if($value['id'] == $NewVaArray[$key1]) {
                        $NewVaArray[$key1] = $value['title'];
                      }
                    }
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
              'href'=>$ADMIN_HOST.'/add_email_broadcast.php?broadcaster_id='.md5($update_broadcaster_id),
              'title'=>$broadcaster_res['display_id']
            ); 
            
            activity_feed(3, $_SESSION['admin']['id'], 'Admin', $update_broadcaster_id, 'email_broadcaster','Admin Updated Email Broadcaster', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc),'',json_encode($extraJson));
          }
          
        } else {
          $description['ac_message'] =array(
            'ac_red_1'=>array(
              'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
              'title'=>$_SESSION['admin']['display_id'],
            ),
            'ac_message_1' =>' Created Email Broadcaster ',
            'ac_red_2'=>array(
                'href'=>$ADMIN_HOST.'/add_email_broadcast.php?broadcaster_id='.md5($broadcaster_id),
                'title'=>$display_id,
            ),
          );

          activity_feed(3, $_SESSION['admin']['id'], 'Admin', $broadcaster_id, 'email_broadcaster','Admin Created Email Broadcaster', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
        }
      }

      if(!empty($action_type) && $action_type == 'send_email'){

        $params = array();
        $params['EMAILER_SETTING']['from_mailid'] = $broadcast_from_address;
        $params['EMAILER_SETTING']['from_mail_name'] = $broadcast_from_name;
        $emailSubject = !empty($broadcast_subject) ? $broadcast_subject : 'Test Send Mail';
        $attachments = array();
        if($email_attachment_id){

          foreach ($email_attachment_id as $key => $value) {
            $updParam = array('broadcast_id'=>$broadcaster_id);
            $updWhere = array(
              'clause' => 'id = :id',
              'params' => array(':id' => $value)
            );
            $pdo->update('email_attachment', $updParam, $updWhere);
          }

          $email_attachment_id = implode(',', $email_attachment_id);
          $selectAttachment = $pdo->select("SELECT * FROM email_attachment WHERE id IN($email_attachment_id) AND is_deleted = 'N'");
          if($selectAttachment){
            foreach ($selectAttachment as $k => $v) {
              if($v['file_name'] != ''){
                array_push($attachments, $ATTACHMENS_WEB . $v['file_name']);
              }
            }
          }  
        }
        

        $mailStatus = trigger_mail_to_mail($params,$send_user_email,3,$emailSubject,trim($broadcast_content),$email_template,$attachments);

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
}
?>