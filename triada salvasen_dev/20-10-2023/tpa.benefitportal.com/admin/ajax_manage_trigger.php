<?php 
	include_once dirname(__FILE__) . '/layout/start.inc.php';
  include_once __DIR__ . '/../includes/function.class.php';
  $functionsList = new functionsList();
  include_once __DIR__ . '/../includes/trigger.class.php';
  $TriggerMailSms = new TriggerMailSms();

	$validate = new Validation();
	$res = array();

  /* ------------------- Get Variables Code Start ------------------- */
    $REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);

    $getTriggerId = isset($_POST['triggerId']) ? $_POST['triggerId'] : '';
    $action = isset($_POST['action']) ? $_POST['action'] : '';

  	$title = isset($_POST['title']) ? $_POST['title'] : '';
  	$companyId = isset($_POST['company_id']) ? $_POST['company_id'] : '';
  	$type = isset($_POST['type']) ? $_POST['type'] : '';

  	$userGroup = isset($_POST['user_group']) ? $_POST['user_group'] : '';
  	$triggerAction = isset($_POST['trigger_action']) ? $_POST['trigger_action'] : '';
  	$specifically = isset($_POST['specifically']) ? $_POST['specifically'] : '';
  	$products = isset($_POST['products']) ? $_POST['products'] : '';
    $days_prior = isset($_POST['days_prior']) ? $_POST['days_prior'] : '';
    $effective_date = isset($_POST['effective_date']) ? $_POST['effective_date'] : '';

    $trigger_delay_type = isset($_POST['trigger_delay_type']) ? $_POST['trigger_delay_type'] : '';
    $numbers_to_delay = isset($_POST['numbers_to_delay']) ? $_POST['numbers_to_delay'] : '';
    $time_units = isset($_POST['time_units']) ? $_POST['time_units'] : '';
    $delay_until_date = isset($_POST['delay_until_date']) ? $_POST['delay_until_date'] : '';

    $fromEmail = checkIsset($_POST['from_email']);
    $fromName = isset($_POST['from_name']) ? $_POST['from_name'] : '';
    $emailSubject = isset($_POST['email_subject']) ? stripslashes($_POST['email_subject']) : '';
    $emailContent = isset($_POST['email_content']) ? stripslashes($_POST['email_content']) : '';
    $emailTemplate = isset($_POST['email_template']) ? $_POST['email_template'] : '';

  	$toEmailSpecific = checkIsset($_POST['to_email_specific']);
  	$ccEmailSpecific = checkIsset($_POST['cc_email_specific']);
  	$bccEmailSpecific = checkIsset($_POST['bcc_email_specific']);
  	
    $toEmailUser = isset($_POST['to_email_user']) ? $_POST['to_email_user'] : '';
  	$ccEmailUser = isset($_POST['cc_email_user']) ? $_POST['cc_email_user'] : '';
  	$bccEmailUser = isset($_POST['bcc_email_user']) ? $_POST['bcc_email_user'] : '';
    

  	$toPhoneSpecific = isset($_POST['to_phone_specific']) ? $_POST['to_phone_specific'] : '';
    $toPhoneSpecific = phoneReplaceMain($toPhoneSpecific); 
  	$toPhoneUser = isset($_POST['to_phone_user']) ? $_POST['to_phone_user'] : '';
  	$smsContent = isset($_POST['sms_content']) ? stripslashes($_POST['sms_content']) : '';

    $testTrigger = isset($_POST['test_trigger']) ? $_POST['test_trigger'] : '';
    $testEmail = checkIsset($_POST['test_email']);
    $testSms = isset($_POST['test_sms']) ? $_POST['test_sms'] : '';
    $testSms = phoneReplaceMain($testSms); 

    $email_attachment_id = isset($_POST['email_attachment_id']) ? $_POST['email_attachment_id'] : array();
    $upload_type = checkIsset($_POST['upload_type']);
    $attachments = checkIsset($_FILES['attachment'],'arr');
    $REAL_IP_ADDRESS = get_real_ipaddress();
  /* ------------------- Get Variables Code Ends ------------------- */
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

            $file_name = $getTriggerId.'_'.rand(1000, 9999).'_'.$file['name'];
            $file_path = $ATTACHMENS_DIR . $file_name;
            if (!file_exists($ATTACHMENS_DIR)) {
              mkdir($ATTACHMENS_DIR, 0777, true);
            }
            move_uploaded_file($file['tmp_name'], $file_path);
            chmod($file_path, 0777);

            $attachment_params = array(
              'file_name' => $file_name,
              'file_path' => $file_path,
              'file_type' => $file['type'],
              'is_deleted' => 'N', 
            );
            $attachment_id = $pdo->insert("trigger_attachment", $attachment_params);
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
    /* ------------------- Validation Code Start ------------------- */
  	$validate->string(array('required' => true, 'field' => 'title', 'value' => $title), array('required' => 'Title is required'));
  	$validate->string(array('required' => true, 'field' => 'company_id', 'value' => $companyId), array('required' => 'Company is required'));
  	$validate->string(array('required' => true, 'field' => 'type', 'value' => $type), array('required' => 'Type is required'));

  	if(!empty($userGroup) && $userGroup!='other'){
  		$validate->string(array('required' => true, 'field' => 'trigger_action', 'value' => $triggerAction), array('required' => 'Trigger Action is required'));  	
  		if(!$validate->getError('trigger_action') && !in_array($triggerAction, array('agent_onboarding','group_onboarding','renewal_payment'))){
  			$validate->string(array('required' => true, 'field' => 'specifically', 'value' => $specifically), array('required' => 'Specifically is required'));  	
  		} else if(!$validate->getError('trigger_action') && in_array($triggerAction, array('renewal_payment'))){
          $validate->string(array('required' => true, 'field' => 'days_prior', 'value' => $days_prior), array('required' => 'Day(s) Prior is required')); 
          $validate->string(array('required' => true, 'field' => 'effective_date', 'value' => $effective_date), array('required' => 'Enter Effective Date')); 
          if(!empty($effective_date)){
            $checkEffectiveDate=validateDate($effective_date,"m/d/Y");
            if(!$checkEffectiveDate){
              $step2Validation->setError("effective_date","Enter Valid Date");
            }
          }
      }
  	}

      if(!$validate->getError('trigger_action') && !in_array($triggerAction, array('renewal_payment'))){

        $validate->string(array('required' => true, 'field' => 'trigger_delay_type', 'value' => $trigger_delay_type), array('required' => 'Trigger Delay is required'));

        if(!empty($trigger_delay_type) && $trigger_delay_type == 'Relative'){
          $validate->string(array('required' => true, 'field' => 'numbers_to_delay', 'value' => $numbers_to_delay), array('required' => 'Please select numbers'));
          $validate->string(array('required' => true, 'field' => 'time_units', 'value' => $time_units), array('required' => 'Please select any units'));
        }
        if(!empty($trigger_delay_type) && $trigger_delay_type == 'Exact Date'){
          $validate->string(array('required' => true, 'field' => 'delay_until_date', 'value' => $delay_until_date), array('required' => 'Please select numbers'));
        }
      }


    if($type == 'Email' || $type == 'Both'){
        if(!empty($fromEmail)){
          $validate->string(array('required' => true, 'field' => 'from_email', 'value' => $fromEmail), array('required' => 'Email is required'));
          if(!$validate->getError('from_email')){
            if(!preg_match('/^([a-z0-9]+)([\._-][a-z0-9]+)*@([a-z0-9]+)(\.)+[a-z]{2,6}$/ix', trim($fromEmail))) {
                  $validate->setError("from_email", "Valid Email is required");
            }
          }
        }
    		if(!empty($toEmailSpecific)){
    			$validate->email(array('required' => true, 'field' => 'to_email_specific', 'value' => $toEmailSpecific), array('required' => 'Email is required', 'invalid' => 'Valid email is required'));
        }
        if(!empty($ccEmailSpecific)){
          $validate->email(array('required' => true, 'field' => 'cc_email_specific', 'value' => $ccEmailSpecific), array('required' => 'Email is required', 'invalid' => 'Valid email is required'));
        }     
        if(!empty($bccEmailSpecific)){
    			$validate->email(array('required' => true, 'field' => 'bcc_email_specific', 'value' => $bccEmailSpecific), array('required' => 'Email is required', 'invalid' => 'Valid email is required'));
        }

  	  	$validate->string(array('required' => true, 'field' => 'email_subject', 'value' => $emailSubject), array('required' => 'Email subject is required'));
  	  	$validate->string(array('required' => true, 'field' => 'email_content', 'value' => $emailContent), array('required' => 'Email content is required'));
        $validate->string(array('required' => true, 'field' => 'email_template', 'value' => $emailTemplate), array('required' => 'Email Template is required'));
  	}

  	if($type == 'SMS' || $type == 'Both'){
	  	$validate->string(array('required' => true, 'field' => 'sms_content', 'value' => $smsContent), array('required' => 'SMS content is required'));
  	}


    if(!empty($userGroup) && $userGroup!='other'){
      if($type == 'Email' || $type == 'Both'){
        if(empty($toEmailSpecific) && empty($toEmailUser)){
          $validate->setError("to_email_specific","At least one specifics or users must be set.");
          $validate->setError("to_email_user","At least one specifics or users must be set.");
        }
      }

      if($type == 'SMS' || $type == 'Both'){
        if(empty($toPhoneUser) && empty($toPhoneSpecific)){
          $validate->setError("to_phone_user","At least one specifics or users must be set.");
          $validate->setError("to_phone_specific","At least one specifics or users must be set.");
        }
      }
    }

    if(!empty($testTrigger)){
        if($testTrigger == 'testEmail'){
          $validate->email(array('required' => true, 'field' => 'test_email', 'value' => $testEmail), array('required' => 'Email is required', 'invalid' => 'Valid email is required'));
        }
        if($testTrigger == 'testSms'){
          $validate->string(array('required' => true, 'field' => 'test_sms', 'value' => $testSms), array('required' => 'Phone is required'));
        }
    }
    /* ------------------- Validation Code Start ------------------- */

  	if($validate->isValid()){

    /*------- Add,Edit or Clone Trigger Code Start -------*/
      if(empty($testTrigger)){

        $triggerRes = array();
        $triProductRes = array();
        $displayId = $TriggerMailSms->get_trigger_display_id();
        
        $insParams= array(
            
                "company_id" => $companyId,
                "title" => $title,
                "type" => $type,
              );

          $insParams['template_id'] = !empty($emailTemplate) ? $emailTemplate : '';
          $insParams['user_group'] = !empty($userGroup) ? $userGroup : '';
          $insParams['trigger_action'] = !empty($triggerAction) ? $triggerAction : '';
          $insParams['specifically'] = !empty($specifically) ? $specifically : '';
          if(!empty($userGroup) && $userGroup =='member' && !empty($triggerAction) && $triggerAction == 'renewal_payment'){
            $insParams['days_prior'] = !empty($days_prior) ? $days_prior : '';
            $insParams['effective_date'] = !empty($effective_date) ? date('Y-m-d',strtotime($effective_date)) : '';
          }

          $insParams['trigger_delay_type'] = !empty($trigger_delay_type) ? $trigger_delay_type : '';
          $insParams['numbers_to_delay'] = !empty($numbers_to_delay) ? $numbers_to_delay : '';
          $insParams['time_units'] = !empty($time_units) ? $time_units : '';
          $insParams['delay_until_date'] = !empty($delay_until_date) ? date('Y-m-d',strtotime($delay_until_date)) : '';

          if($triggerAction == 'renewal_payment'){
            $insParams['trigger_delay_type'] = 'None';
            $insParams['numbers_to_delay'] = '';
            $insParams['time_units'] = '';
            $insParams['delay_until_date'] = '';
          }
          
          if($type=='Email' || $type=='Both'){
              $insParams['from_email'] = !empty($fromEmail) ? $fromEmail : '';
              $insParams['from_name'] = !empty($fromName) ? $fromName : '';

              $insParams['email_subject'] = !empty($emailSubject) ? $emailSubject : '';
              $insParams['email_content'] = !empty($emailContent) ? $emailContent : '';
              
              $insParams['to_email_specific'] = !empty($toEmailSpecific) ? $toEmailSpecific : '';
              $insParams['cc_email_specific'] = !empty($ccEmailSpecific) ? $ccEmailSpecific : '';
              $insParams['bcc_email_specific'] = !empty($bccEmailSpecific) ? $bccEmailSpecific : '';
              
              $insParams['to_email_user'] = !empty($toEmailUser) ? $toEmailUser : '';
              $insParams['cc_email_user'] = !empty($ccEmailUser) ? $ccEmailUser : '';
              $insParams['bcc_email_user'] = !empty($bccEmailUser) ? $bccEmailUser : '';
          }
          if($type=='SMS' || $type=='Both'){
            $insParams['to_phone_specific'] = !empty($toPhoneSpecific) ? $toPhoneSpecific : '';       
            $insParams['to_phone_user'] = !empty($toPhoneUser) ? $toPhoneUser : '';       

            $insParams['sms_content'] = !empty($smsContent) ? $smsContent : '';       
          }

          $insertParamsKey =  implode(",", array_keys($insParams));

          if(!empty($getTriggerId)){
            $triggerSql = "SELECT id,status,display_id,$insertParamsKey FROM triggers WHERE md5(id)=:id AND is_deleted='N'";
            $triggerParams = array(":id" => $getTriggerId);
            $triggerRes = $pdo->selectOne($triggerSql,$triggerParams);

            $triProductSql = "SELECT GROUP_CONCAT(product_id) as product_ids FROM trigger_products WHERE md5(trigger_id)=:id AND is_deleted='N'";
            $triProductParams = array(":id" => $getTriggerId);
            $triProductRes = $pdo->selectOne($triProductSql,$triProductParams);
          }

          $triggerId = !empty($triggerRes['id']) ? $triggerRes['id'] : '';

          if(!empty($triggerId)){
            if($action == 'Clone'){
              $insParams["display_id"] = $displayId;
              $insParams["status"] = $triggerRes['status'];
              $insParams["admin_id"] = $_SESSION['admin']['id'];
              $insParams["ip_address"] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];

              $triggerId = $pdo->insert("triggers",$insParams);

              if($email_attachment_id){
                $email_attachment_id = explode(',',$email_attachment_id[0]);
                foreach ($email_attachment_id as $key => $value) {
                  $updParam = array('trigger_id'=>$triggerId);
                  $updWhere = array(
                    'clause' => 'id = :id',
                    'params' => array(':id' => $value)
                  );
                  $pdo->update('trigger_attachment', $updParam, $updWhere);
                }
              }
              /* ------ Activity Code Start -------- */
              $activityFeedDesc['ac_message'] =array(
                'ac_red_1'=>array(
                  'href'=>$ADMIN_HOST.'/admin_profile.php?id='. $_SESSION['admin']['id'],
                  'title'=>$_SESSION['admin']['display_id'],
                ),
                'ac_message_1' =>' Created Trigger ',
                'ac_red_2'=>array(
                  'title'=>$displayId,
                ),
              ); 
              activity_feed(3, $_SESSION['admin']['id'], 'Admin', $triggerId, 'triggers','Added Trigger', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
              /* ------ Activity Code Ends -------- */

              /* Add trigger products code start */
                if(!empty($products)){
                  foreach ($products as $key => $prdId) {
                    $insPrd = array("trigger_id" => $triggerId,
                            "product_id" => $prdId);
                    $pdo->insert("trigger_products",$insPrd);         
                  }
                }
              /* Add trigger products code ends */
            }else{
              $triggerId = $triggerRes['id'];
              $displayId = $triggerRes['display_id'];
            
              $update_where = array(
                'clause' => 'id = :id',
                'params' => array(
                  ':id' => $triggerId
                )
              );
              $pdo->update('triggers', $insParams, $update_where);

              if($email_attachment_id){
                $email_attachment_id = explode(',',$email_attachment_id[0]);
                foreach ($email_attachment_id as $key => $value) {
                  $updParam = array('trigger_id'=>$triggerId);
                  $updWhere = array(
                    'clause' => 'id = :id',
                    'params' => array(':id' => $value)
                  );
                  $pdo->update('trigger_attachment', $updParam, $updWhere);
                }
              }
              /*------- Activity Feed Code Start -------*/
                $oldVaArray = $triggerRes;
                $NewVaArray = $insParams;
                
                unset($oldVaArray['id']);
                unset($oldVaArray['status']);
                unset($oldVaArray['display_id']);

                $activity = array_diff_assoc($oldVaArray, $NewVaArray);

                $tmp = array();
                $tmp2 = array();
                if(!empty($activity)){

                  if(array_key_exists('email_content',$activity)){
                    $tmp['display_desc_1']=base64_encode($activity['email_content']);
                    $tmp2['display_desc_1']=base64_encode($insParams['email_content']);
                  }

                  if(array_key_exists('sms_content',$activity)){
                    $tmp['display_desc_2']=base64_encode($activity['sms_content']);
                    $tmp2['display_desc_2']=base64_encode($insParams['sms_content']);
                  }

                  if(array_key_exists('company_id',$activity)){
                    $tmp['company_id'] = $activity['company_id'];
                    $tmp2['company_id'] = $insParams['company_id'];
                  }
                  if(array_key_exists('title',$activity)){
                    $tmp['Trigger Name']=$activity['title'];
                    $tmp2['Trigger Name']=$insParams['title'];
                  }
                  if(array_key_exists('type',$activity)){
                    $tmp['Type']=$activity['type'];
                    $tmp2['Type']=$insParams['type'];
                  }
                  if(array_key_exists('template_id',$activity)){
                    $tmp['Email Template']=$activity['template_id'];
                    $tmp2['Email Template']=$insParams['template_id'];
                  }
                  if(array_key_exists('user_group',$activity)){
                    $tmp['User Group']=ucfirst($activity['user_group']);
                    $tmp2['User Group']=ucfirst($insParams['user_group']);
                  }
                  if(array_key_exists('trigger_action',$activity)){
                    $tmp['Trigger Action']= ucfirst(str_replace("_"," ",$activity['trigger_action']));
                    $tmp2['Trigger Action']= ucfirst(str_replace("_"," ",$insParams['trigger_action']));
                  }
                  if(array_key_exists('from_email',$activity)){
                    $tmp['From Email']=$activity['from_email'];
                    $tmp2['From Email']=$insParams['from_email'];
                  }
                  if(array_key_exists('from_name',$activity)){
                    $tmp['From Name']=$activity['from_name'];
                    $tmp2['From Name']=$insParams['from_name'];
                  }
                  if(array_key_exists('email_subject',$activity)){
                    $tmp['Email Subject']=$activity['email_subject'];
                    $tmp2['Email Subject']=$insParams['email_subject'];
                  }
                  if(array_key_exists('cc_email_specific',$activity)){
                    $tmp['CC Email Specific']=$activity['cc_email_specific'];
                    $tmp2['CC Email Specific']=$insParams['cc_email_specific'];
                  }
                  if(array_key_exists('bcc_email_specific',$activity)){
                    $tmp['BCC Email Specific']=$activity['bcc_email_specific'];
                    $tmp2['BCC Email Specific']=$insParams['bcc_email_specific'];
                  }
                  if(array_key_exists('to_email_user',$activity)){

                    $tmp['To Email User']=ucfirst(str_replace("_"," ",$activity['to_email_user']));
                    $tmp2['To Email User']=ucfirst(str_replace("_"," ",$insParams['to_email_user']));
                  }
                  if(array_key_exists('cc_email_user',$activity)){
                    $tmp['CC Email User']=ucfirst(str_replace("_"," ",$activity['cc_email_user']));
                    $tmp2['CC Email User']=ucfirst(str_replace("_"," ",$insParams['cc_email_user']));
                  }
                  if(array_key_exists('bcc_email_user',$activity)){
                    $tmp['BCC Email User']=ucfirst(str_replace("_"," ",$activity['bcc_email_user']));
                    $tmp2['BCC Email User']=ucfirst(str_replace("_"," ",$insParams['bcc_email_user']));
                  }
                  if(array_key_exists('to_phone_specific',$activity)){
                    $tmp['To Phone Specific']=$activity['to_phone_specific'];
                    $tmp2['To Phone Specific']=$insParams['to_phone_specific'];
                  }
                  if(array_key_exists('to_phone_user',$activity)){
                    $tmp['To Phone User']=ucfirst(str_replace("_"," ",$activity['to_phone_user']));
                    $tmp2['To Phone User']=ucfirst(str_replace("_"," ",$insParams['to_phone_user']));
                  }

                  $link = $ADMIN_HOST.'/manage_trigger.php?id='. md5($triggerId);
                  
                  $actFeed=$functionsList->generalActivityFeed($tmp,$tmp2,$link,$displayId,$triggerId,'triggers','Admin Updated Trigger','Updated Trigger');
                }

              /*------- Activity Feed Code Ends -------*/

              /* Trigger products code start */
                $extProducts = !empty($triProductRes['product_ids']) ? $triProductRes['product_ids'] : '';
                $newProducts = !empty($products) ? implode(",", $products) : '';

                $oldPrdArr = !empty($extProducts) ? explode(",",$extProducts) : array();
                $newPrdArr = !empty($newProducts) ? explode(",",$newProducts) : array();

                $addedPrd = array_diff($newPrdArr,$oldPrdArr);
                $removedPrd = array_diff($oldPrdArr,$newPrdArr);
                
                $activityText = '';
                /*--- Added trigger products code start ---*/
                if(!empty($addedPrd)){
                  foreach ($addedPrd as $key => $prdId) {
                    $insPrd = array("trigger_id" => $triggerRes['id'],
                            "product_id" => $prdId);
                    $pdo->insert("trigger_products",$insPrd);         
                  }

                  $products = $pdo->select("SELECT name,product_code from prd_main where id IN(".implode(",",$addedPrd).")");
                  if(!empty($products)){
                    $activityText.=" Admin added ";
                    foreach ($products as $value) {
                        $activityText.=$value['name']." (". $value['product_code'] .")";
                        if(count($products) > 1){
                          $activityText.=" ,";
                        }
                    }
                      $activityText.=" on Trigger ".$triggerRes['display_id']."<br>";
                  }
                }
                /*--- Added trigger products code ends ---*/

                /*--- Deleted trigger products code start ---*/
                if(!empty($removedPrd)){
                  $removedPrdIds = implode(",", $removedPrd);

                  $updateParams = array('is_deleted'=>'Y');
                  $updateWhere = array(
                      'clause' => 'trigger_id=:id AND product_id IN('.$removedPrdIds.') AND is_deleted="N"',
                      'params' => array(
                        ":id" => $triggerId,
                      )
                    );
                  $pdo->update('trigger_products',$updateParams,$updateWhere);

                  /*--- Deleted trigger products code start ---*/
                    $products = $pdo->select("SELECT name,product_code from prd_main where id IN($removedPrdIds)");
                    if(!empty($products)){
                      $activityText.=" Admin deleted ";
                      foreach ($products as $value) {
                          $activityText.=$value['name']." (". $value['product_code'] .")";
                          if(count($products) > 1){
                            $activityText.=" ,";
                          }
                      }
                        $activityText.=" on Trigger ".$triggerRes['display_id']."<br>";
                    }
                  /*--- Deleted trigger products code ends ---*/
                }
                /*--- Deleted trigger products code ends ---*/

                if(!empty($activityText)){
                  $activityFeedDesc['key_value']['desc_arr']['Products']=$activityText;
                }
                if(!empty($activityFeedDesc)){
                 activity_feed(3, $_SESSION['admin']['id'], 'Admin', $triggerRes['id'], 'prd_main','Admin Updated Trigger', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
                }

              /* Trigger products code ends */
            }
          }else{
            $insParams["display_id"] = $displayId;
            $insParams["status"] = "Active";
            $insParams["admin_id"] = $_SESSION['admin']['id'];
            $insParams["ip_address"] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
          
            $triggerId = $pdo->insert("triggers",$insParams);

            /* ------------------- Activity Code Start ------------------- */
              $activityFeedDesc['ac_message'] =array(
                'ac_red_1'=>array(
                  'href'=>$ADMIN_HOST.'/admin_profile.php?id='. $_SESSION['admin']['id'],
                  'title'=>$_SESSION['admin']['display_id'],
                ),
                'ac_message_1' =>' Created trigger ',
                'ac_red_2'=>array(
                  'title'=>$displayId,
                ),
              ); 
              activity_feed(3, $_SESSION['admin']['id'], 'Admin', $triggerId, 'triggers','Added Trigger', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
            /* ------------------- Activity Code Ends ------------------- */

            /* Add trigger products code start */
                if(!empty($products)){
                  foreach ($products as $key => $prdId) {
                    $insPrd = array("trigger_id" => $triggerId,
                            "product_id" => $prdId);
                    $pdo->insert("trigger_products",$insPrd);         
                  }
                }
            /* Add trigger products code ends */
          }

        $res['actionType'] = 'savedTrigger';
        $res['msg'] = "Trigger Saved Successfully!";
      }
    /*------- Add,Edit or Clone Trigger Code Ends -------*/

    /*------- Test Send Trigger Email & SMS Code Start -------*/
      if(!empty($testTrigger)){
        if($testTrigger == "testEmail" && !empty($testEmail)){
          $params = array();

          $params['EMAILER_SETTING']['from_mailid'] = $fromEmail;
          $params['EMAILER_SETTING']['from_mail_name'] = $fromName;

          $emailSubject = !empty($emailSubject) ? $emailSubject : 'Test Send Mail';

          $attachments = array();
          if($email_attachment_id){

            $email_attachment_id = $email_attachment_id[0];
            $selectAttachment = $pdo->select("SELECT * FROM trigger_attachment WHERE id IN($email_attachment_id) AND is_deleted = 'N'");

            if($selectAttachment){
              foreach ($selectAttachment as $k => $v) {
                if($v['file_name'] != ''){
                  array_push($attachments, $ATTACHMENS_WEB . $v['file_name']);
                }
              }
            }  
          }

          $mailStatus = trigger_mail_to_mail($params,$testEmail,3,$emailSubject,$emailContent,$emailTemplate,$attachments);

            if($mailStatus == 'success'){
              $res['status'] = 'success';
              $res['msg'] = "Test Email Sent Successfully!";
            }else{
              $res['msg'] = "Something went wrong!";
              $res['status'] = "fail";
            }
            $res['actionType'] = 'sendEmail';
        } 

        if($testTrigger == "testSms" && !empty($testSms)){

            $country_code = '+1';
            $toPhone = $country_code . $testSms;
            $message = $smsContent;

            $smsStatus = send_sms_to_phone($toPhone,$message);
            
            if($smsStatus == 'success'){
              $res['msg'] = "Test SMS Sent Successfully!";
              $res['status'] = "success";
            }else{
              $res['msg'] = "Something went wrong!";
              $res['status'] = "fail";
            }
            $res['actionType'] = 'sendSms';
        }
        header('Content-type: application/json');
        echo json_encode($res); 
        exit;
      }
    /*------- Test Send Trigger Email & SMS Code Ends -------*/

  		$res['status'] = "success";
  	}else{
	    $errors = $validate->getErrors();
	    $res['errors'] = $errors;
	    $res['status'] = "fail";
  	}

    header('Content-type: application/json');
    echo json_encode($res); 
    exit;
  }
    dbConnectionClose();
?>