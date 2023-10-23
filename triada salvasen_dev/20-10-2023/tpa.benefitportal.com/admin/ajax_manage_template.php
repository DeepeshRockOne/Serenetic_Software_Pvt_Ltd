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

    $getTemplateId = isset($_POST['templateId']) ? $_POST['templateId'] : '';
    $action = isset($_POST['action']) ? $_POST['action'] : '';

  	$title = isset($_POST['title']) ? $_POST['title'] : '';
  	$companyId = isset($_POST['company_id']) ? $_POST['company_id'] : '';
  	$content = isset($_POST['content']) ? $_POST['content'] : '';

  /* ------------------- Get Variables Code Ends ------------------- */

  /* ------------------- Validation Code Start ------------------- */
  	$validate->string(array('required' => true, 'field' => 'title', 'value' => $title), array('required' => 'Title is required'));
  	$validate->string(array('required' => true, 'field' => 'company_id', 'value' => $companyId), array('required' => 'Company is required'));
  	$validate->string(array('required' => true, 'field' => 'content', 'value' => $content), array('required' => 'Content is required'));

    if(!empty($content)){
      if(strpos($content, "[[msg_content]]") == ""){
        $validate->setError('content','[[msg_content]] is required');
      }
    }
  /* ------------------- Validation Code Start ------------------- */

  	if($validate->isValid()){

      /*------- Add,Edit or Clone Template Code Start -------*/
        $templateRes = array();
        $displayId = $TriggerMailSms->get_emailer_template_display_id();
      
        $insParams= array(
                "company_id" => $companyId,
                "title" => $title,
                "content" => $content,
              );

          $insertParamsKey =  implode(",", array_keys($insParams));

          if(!empty($getTemplateId)){
            $templateSql = "SELECT id,display_id,$insertParamsKey FROM trigger_template WHERE md5(id)=:id AND is_deleted='N'";
            $templateParams = array(":id" => $getTemplateId);
            $templateRes = $pdo->selectOne($templateSql,$templateParams);
            $oldVaArray = $templateRes;
          }

          $templateId = !empty($templateRes['id']) ? $templateRes['id'] : '';

          if(!empty($templateId)){

            if($action == 'Clone'){
              $insParams["display_id"] = $displayId;
              $insParams["trg_footer_id"] = 2;
              $templateId = $pdo->insert("trigger_template",$insParams);

              /* ------ Activity Code Start -------- */
              $activityFeedDesc['ac_message'] =array(
                'ac_red_1'=>array(
                  'href'=>$ADMIN_HOST.'/admin_profile.php?id='. md5($_SESSION['admin']['id']),
                  'title'=>$_SESSION['admin']['display_id'],
                ),
                'ac_message_1' =>' Created Template ',
                'ac_red_2'=>array(
                    'href'=>$ADMIN_HOST.'/add_emailer_template.php?id='. $templateId,
                    'title'=>$displayId,
                ),
              ); 
              activity_feed(3, $_SESSION['admin']['id'], 'Admin', $templateId, 'trigger_template','Added Template', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
              /* ------ Activity Code Ends -------- */
            }else{
              $templateId = $templateRes['id'];
              $displayId = $templateRes['display_id'];
            
              $update_where = array(
                'clause' => 'id = :id',
                'params' => array(
                  ':id' => $templateId
                )
              );
              $pdo->update('trigger_template', $insParams, $update_where);

                $NewVaArray = $insParams;
                unset($oldVaArray['id']);
                unset($oldVaArray['display_id']);

                $activity=array_diff_assoc($oldVaArray,$NewVaArray);

                $tmp = array();
                $tmp2 = array();
                if(!empty($activity)){
                  if(array_key_exists('content',$activity)){
                    $tmp['display_desc']=base64_encode($activity['content']);
                    $tmp2['display_desc']=base64_encode($insParams['content']);
                  }
                  if(array_key_exists('title',$activity)){
                    $tmp['Template Name']=$activity['title'];
                    $tmp2['Template Name']=$insParams['title'];
                  }
                  if(array_key_exists('company_id',$activity)){
                    $tmp['company_id'] = $activity['company_id'];
                    $tmp2['company_id'] = $insParams['company_id'];
                  }

                  $link = $ADMIN_HOST.'/add_emailer_template.php?id='.md5($templateId);
                  
                  $actFeed=$functionsList->generalActivityFeed($tmp,$tmp2,$link,$displayId,$templateId,'trigger_template','Admin Updated Template','Updated Trigger Template');
                }
            }
          }else{
            $insParams["display_id"] = $displayId;
            $insParams["trg_footer_id"] = 2;
            $templateId = $pdo->insert("trigger_template",$insParams);

            /* ------------------- Activity Code Start ------------------- */
              $activityFeedDesc['ac_message'] =array(
                'ac_red_1'=>array(
                  'href'=>$ADMIN_HOST.'/admin_profile.php?id='. md5($_SESSION['admin']['id']),
                  'title'=>$_SESSION['admin']['display_id'],
                ),
                'ac_message_1' =>' Created Template ',
                'ac_red_2'=>array(
                   'href'=>$ADMIN_HOST.'/add_emailer_template.php?id='. $templateId,
                  'title'=>$displayId,
                ),
              ); 
              activity_feed(3, $_SESSION['admin']['id'], 'Admin', $templateId, 'trigger_template','Added Template', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
            /* ------------------- Activity Code Ends ------------------- */
          }
      /*------- Add,Edit or Clone Trigger Code Ends -------*/

      $res['actionType'] = 'savedTemplate';
      $res['msg'] = "Template Saved Successfully!";
  		$res['status'] = "success";
  	}else{
	    $errors = $validate->getErrors();
	    $res['errors'] = $errors;
	    $res['status'] = "fail";
  	}

  header('Content-type: application/json');
  echo json_encode($res);
  dbConnectionClose(); 
  exit;
?>