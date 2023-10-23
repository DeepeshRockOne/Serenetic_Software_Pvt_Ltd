<?php 
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once __DIR__ . '/../includes/function.class.php';
$functionsList = new functionsList();
  $validate = new Validation();
  $result = array();
  $ajax_file = !empty($_POST['ajax_file']) ? $_POST['ajax_file'] : '';
  if($ajax_file == 'file')
  {
    $file_type = checkIsset($_POST['coll_type']);
    $file_id = $_POST['ajax_file_id'];
    $attachments_array = array();
    $length = $_FILES['coll_attachements'];
    foreach($length as $lkey => $lvalue)
    {
      $attachments_array[$lkey]=$lvalue;
      unset($attachments_array[$lkey]['~number~']);
    }

    if($file_type[$file_id] == 'pdf'){
      $pdfExt = array_reverse(explode(".", $attachments_array['name'][$file_id]));
      if(!in_array(strtolower($pdfExt[0]),array('pdf'))){
        $validate->setError('coll_attachements'.$file_id,"Please select only pdf File.");
      }
      if($attachments_array['size'][$file_id] > 10485760){
        $validate->setError('coll_attachements'.$file_id,"Please select only less then 10mb pdf File.");
      }else if(!empty($attachments_array['name'][$file_id]) && empty($attachments_array['size'][$file_id])){
        $validate->setError('coll_attachements'.$file_id,"Please select only less then 10mb pdf File.");
      }
    }
    if($file_type[$file_id] == 'video'){
        $videoExt = array_reverse(explode(".", $attachments_array['name'][$file_id]));
        if(!in_array(strtolower($videoExt[0]),array('mp4','mpeg'))){
          $validate->setError('coll_attachements'.$file_id,"Please select only mpeg/mp4 video File.");
        }
    }
    
    if($validate->isValid()){

      $file_type = explode("/",$attachments_array['type'][$file_id]);
      $file_name = $file_id.'_'.rand(1000, 9999).'_'.$attachments_array['name'][$file_id];
      $file_path = $COLLATERAL_DOCUMENT_DIR."/".'tmp_file'."/". $file_name;
      if (!file_exists($COLLATERAL_DOCUMENT_DIR."/".'tmp_file')) {
        mkdir($COLLATERAL_DOCUMENT_DIR."/".'tmp_file', 0777, true);
      }
      move_uploaded_file($attachments_array['tmp_name'][$file_id], $file_path);
      chmod($file_path, 0777);
      $result['file_name'] = $file_name;
      $result['status'] = "success";
    }else{
      $errors = $validate->getErrors();
      $result['status'] = "fail";
      $result['errors'] = $errors;
    }
    echo json_encode($result); 
    exit;
  }
  $tmp_filename = !empty($_POST['file_name']) ? $_POST['file_name'] : '';

  if($tmp_filename!=='' && !empty($tmp_filename)){

    $ftype = !empty($_POST['file_type']) ? $_POST['file_type'] : '';
    if(!empty($ftype)){
      if(is_dir('..\\uploads\\collateral_document\\'.$ftype)){

        $insert_param['updated_at'] = "msqlfunc_NOW()";
        $insert_param['coll_doc_url'] = "";
        $insert_param['video_type'] = "";
        $uid = $pdo->selectOne('SELECT id from sub_resources where coll_doc_url = :url',array(":url"=>$tmp_filename));
        if(!empty($uid)){
            $upd_where = array(
              'clause' => 'id = :id',
              'params' => array(
                ':id' => $uid['id'],
              ),
            );
            $pdo->update('sub_resources', $insert_param,$upd_where);
            unlink('..\\uploads\\collateral_document\\'.$ftype.'\\'.$tmp_filename);
        }
        $result['status'] = "success";
        $result['message'] = "File was successfully deleted.";
      }else{
        $result['status'] = "fail ";
        $result['message'] = "File was not found or deleted.";
      }
    }else{
      if(unlink('..\\uploads\\collateral_document\\tmp_file\\'.$tmp_filename))
      {
        $result['status'] = "success";
        $result['message'] = "File was successfully deleted.";
      }else{
        $result['status'] = "fail ";
        $result['message'] = "File was not found or deleted.";
      }
    }
    
    echo json_encode($result); 
    exit;
  }
  //variable start
  $resource_name = !empty($_POST['resource_name']) ? $_POST['resource_name'] : '';
  $resId = !empty($_POST['resId']) ? $_POST['resId'] : '';
  $resIdOld = !empty($_POST['resId']) ? $_POST['resId'] : '';
  $resource_id = !empty($_POST['resource_id']) ? $_POST['resource_id'] : '';
  $src_products = !empty($_POST['src_products']) ? $_POST['src_products'] : array();
  $effective_date = !empty($_POST['effective_date']) ? $_POST['effective_date'] : '';
  $termination_date = !empty($_POST['termination_date']) ? $_POST['termination_date'] : '';
  $resources_type = !empty($_POST['resources_type']) ? $_POST['resources_type'] : '';
  $states = !empty($_POST['states']) ? $_POST['states'] : array();
  $opt_certi = !empty($_POST['opt_certi']) ? $_POST['opt_certi'] : array();
  // $url_states = !empty($_POST['url_states']) ? $_POST['url_states'] : array();
  $file_uploaded = !empty($_POST['file_uploaded']) ? $_POST['file_uploaded'] : array();
  //certificates variables
  $dynamicFields = !empty($_POST['dynamicFields']) ? $_POST['dynamicFields'] : array();
  $certi_description = !empty($_POST['certi_description']) ? $_POST['certi_description'] : array();
  // $url_states = !empty($_POST['url_states']) ? $_POST['url_states'] : array();
  //collateral variables
  $dyncollFields = !empty($_POST['dyncollFields']) ? $_POST['dyncollFields'] : array();
  $coll_user_type = !empty($_POST['coll_user_type']) ? $_POST['coll_user_type'] : '';
  $opt_coll = !empty($_POST['opt_coll']) ? $_POST['opt_coll'] : array();
  $coll_type = !empty($_POST['coll_type']) ? $_POST['coll_type'] : array();
  $coll_attachements = !empty($_POST['coll_attachements']) ? $_POST['coll_attachements'] : array();
  $url_videos = !empty($_POST['url_videos']) ? $_POST['url_videos'] : array();
  $link_url = !empty($_POST['link_url']) ? $_POST['link_url'] : array();
  $col_description = !empty($_POST['col_description']) ? $_POST['col_description'] : array();
  $certificate_mem_description = !empty($_POST['certificate_mem_description']) ? $_POST['certificate_mem_description'] : array();
  $video_code_type = !empty($_POST['video_code_type']) ? $_POST['video_code_type'] : array();
  //id card
  $card_descrition =!empty($_POST['card_descrition']) ? $_POST['card_descrition'] : '';
  $is_clone =!empty($_POST['is_clone']) ? $_POST['is_clone'] : 'N';
  //variable end
  $checkRow =array();
  $validate->string(array('required' => true, 'field' => 'resource_name', 'value' => $resource_name), array('required' => 'Resource Name is required.'));
  $validate->string(array('required' => true, 'field' => 'resource_id', 'value' => $resource_id), array('required' => 'Resource ID is required.'));
  if(empty($src_products)){
    $validate->setError("src_products", "Please Select any Product.");
  }
  $validate->string(array('required' => true, 'field' => 'effective_date', 'value' => $effective_date), array('required' => 'Effective Date is required.'));
  // $validate->string(array('required' => true, 'field' => 'termination_date', 'value' => $termination_date), array('required' => 'Termination Date is required.'));
  $validate->string(array('required' => true, 'field' => 'resources_type', 'value' => $resources_type), array('required' => 'Please Select Resource type.'));

  if (!empty($termination_date) && !empty($effective_date)) {
    if(strtotime(date($termination_date)) < strtotime(date($effective_date))){ 
      $validate->setError("termination_date", "Termination date must be greater then or equal to effective date.");
    }
  }
  if (!$validate->getError('resource_id')) {
    $schParams=array(":display_id"=>$resource_id);
    $incr='';
    if(!empty($resId) && $is_clone == 'N'){
      $incr.=" AND id != :id";
      $schParams[':id'] = $resId;
    }
    $checkSql = "SELECT display_id,id FROM resources WHERE display_id=:display_id AND is_deleted='N' $incr";
    $checkRow = $pdo->selectOne($checkSql, $schParams);
      if (!empty($checkRow)) {
      $validate->setError("resource_id", "Resource ID is Already Exists.");
      } 
    }
if($resources_type == 'Collateral' || $resources_type == 'Certificate' ){
    $validate->string(array('required' => true, 'field' => 'coll_user_type', 'value' => $coll_user_type), array('required' => 'Please Select User Group.'));
   
    if(!empty($dyncollFields) && !empty($coll_user_type)){
      unset($dyncollFields['~number~']);
        foreach ($dyncollFields as $key => $value) {
          if(!empty($opt_coll[$key]) && $opt_coll[$key] == 'yes') { 
            if(empty($states[$key])){
              $validate->setError("states_".$key,"Please Select States");
            } 
          }else{
            if(empty($opt_coll[$key]))
              $validate->setError("opt_coll".$key, "Please Select any option.");
          }
          if(empty($coll_type[$key])){
            $validate->string(array('required' => true, 'field' => 'coll_type'.$key, 'value' => $coll_type[$key]), array('required' => 'Please Select '.$resources_type.' Type.'));
          }else{
          if(empty($coll_attachements[$key])){
            if($coll_type[$key] == 'pdf' && empty($coll_attachements[$key])){
              if($file_uploaded[$key] == "false")
                {
                  $validate->string(array('required' => true, 'field' => 'coll_attachements'.$key, 'value' => $coll_attachements[$key]), array('required' => 'Please Select any File.'));
                }

            }else if($coll_type[$key] == 'video'){
              $video_type = !empty($_POST['video_type']) ? $_POST['video_type'] : array();
              if($video_type[$key] == 'file' && $file_uploaded[$key] == "false"){
                $validate->string(array('required' => true, 'field' => 'coll_attachements'.$key, 'value' => $coll_attachements[$key]), array('required' => 'Please Select File.'));
              }else if($video_type[$key] == 'url'){
                $validate->string(array('required' => true, 'field' => 'url_videos'.$key, 'value' => $url_videos[$key]), array('required' => 'Please Enter embed Code from Youtube or Wistia'));
              }
            }else if($coll_type[$key] == 'link'){
              $validate->url_no_http(array('required' => true, 'field' => 'link_url'.$key, 'value' => $link_url[$key]), array('required' => 'Please Enter URL', 'invalid' => 'Please Enter valid URL'));
            }else if($coll_type[$key] == 'html'){
              if(empty(trim($certificate_mem_description[$key]))){
                $validate->setError('certificate_mem_description_'.$key,"Please Enter Certificate Description.");
              }
              if(!$validate->getError('certificate_mem_description')) {
                if($functionsList->hasExternalJsCss($card_descrition)) {
                  $validate->setError('certificate_mem_description_'.$key,"Please remove external JavaScript/CSS from HTML");
                }
              }
            }
          }else{
              if($file_uploaded[$key] == "false" || !empty($coll_attachements[$key])){
                $attachments_array = array();
                $length = $_FILES['coll_attachements'];
                foreach($length as $lkey => $lvalue)
                {
                  $attachments_array[$lkey]=$lvalue;
                  unset($attachments_array[$lkey]['~number~']);
                }

                if($coll_type[$key] == 'video')
                  {
                      $videoExt = array_reverse(explode(".", $attachments_array['name'][$key]));
                      if(!in_array(strtolower($videoExt[0]),array('mp4','mpeg'))){
                        $validate->setError('coll_attachements'.$key,"Please select only mpeg/mp4 video File.");
                      }
                  }else{
                    $pdfExt = array_reverse(explode(".", $attachments_array['name'][$key]));
                    if(!in_array(strtolower($pdfExt[0]),array('pdf'))){
                      $validate->setError('coll_attachements'.$key,"Please select only pdf File.");
                    }
                    if($attachments_array['size'][$key] > 10485760){
                      $validate->setError('coll_attachements'.$key,"Please select only less then 10mb pdf File.");
                    }
                  }
                }
            }
          }
          // if(empty($col_description[$key])){
          //   $validate->string(array('required' => true, 'field' => 'col_description'.$key, 'value' => $col_description[$key]), array('required' => 'Please Enter Description.'));
          // }
        }
      }
}else if($resources_type == 'id_card'){
    if(empty(trim($card_descrition))){
      $validate->string(array('required' => true, 'field' => 'card_descrition', 'value' => $card_descrition), array('required' => 'Please Enter Id card description.'));
    }
    if(!$validate->getError('card_descrition')) {
      if($functionsList->hasExternalJsCss($card_descrition)) {
        $validate->setError('card_descrition',"Please remove external JavaScript/CSS from HTML");
      }
    }
}

  if ($validate->isValid()) {

    if(is_dir('..\\uploads\\collateral_document\\tmp_file')){
      deleteDir('..\\uploads\\collateral_document\\tmp_file');
    }
   if($resources_type == 'Collateral' || $resources_type == 'Certificate'){
    $insert_id ='';
    $new_group = array();
    $insert_param = array(
      "display_id" => $resource_id,
      "name" => $resource_name,
      "type" => $resources_type,
      "user_group" => $coll_user_type,
      "effective_date" => date('Y-m-d',strtotime($effective_date)),
      "termination_date" => !empty($termination_date) ? date('Y-m-d',strtotime($termination_date)) : '',
      "status" => 'Active',
      "created_at" => "msqlfunc_NOW()"
    );
    if(empty($resId) || $is_clone == 'Y'){
      $insert_id = $pdo->insert('resources', $insert_param);
    }else{

      $old_array_value = array();
      $new_array_value = array();
      $sub_resource_old_value_array = array();
      $sub_resource_new_value_array = array();
      $old_prd = '';
      $new_prd = array();
      $old_state = array();
      $new_state = array();
      $check_diff_counter = 0;
      $resource_res = $pdo->selectOne("SELECT r.id,display_id,name,user_group,effective_date,termination_date, GROUP_CONCAT(rp.product_id) as product_ids FROM resources r LEFT JOIN res_products rp ON(rp.res_id=r.id) where r.id=:id",array(':id'=>$resId));
      if(!empty($resource_res)){
        $old_prd = $resource_res['product_ids'];
        $old_array_value['display_id'] = $resource_res['display_id'];
        $old_array_value['name'] = $resource_res['name'];
        $old_array_value['user_group'] = $resource_res['user_group'];
        $old_array_value['effective_date'] = date('m/d/Y',strtotime($resource_res['effective_date']));
        $old_array_value['termination_date'] = !empty($resource_res['termination_date']) && $resource_res['termination_date']!='0000-00-00' ? date('m/d/Y',strtotime($resource_res['termination_date'])) : '';
      }

      unset($insert_param['created_at']);
      unset($insert_param['status']);
      $insert_param['updated_at'] = 'msqlfunc_NOW()';
      $upd_where = array(
        'clause' => 'id = :id',
        'params' => array(
          ':id' => $resId,
        ),
      );
      $insert_id = $resId;

      $new_array_value['display_id'] = $insert_param['display_id'];
      $new_array_value['name'] = $insert_param['name'];
      $new_array_value['user_group'] = $insert_param['user_group'];
      $new_array_value['effective_date'] = date('m/d/Y',strtotime($insert_param['effective_date']));
      $new_array_value['termination_date'] = !empty($insert_param['termination_date']) && $insert_param['termination_date']!='0000-00-00' ? date('m/d/Y',strtotime($insert_param['termination_date'])) : '';

      $pdo->update('resources', $insert_param,$upd_where);
      $pdo->delete('DELETE FROM res_products where res_id=:id',array(':id' => $resId));
      
      $resSubRec = $pdo->select("SELECT sr.id,coll_type,description,member_description,coll_doc_url,video_type,group_id,GROUP_CONCAT(rs.state_id) as state_ids from sub_resources sr left join res_states rs ON(rs.sub_res_id=sr.id)  where is_deleted='N' and res_id = :resource_id GROUP BY sr.id",array(":resource_id" => $resId));

      $sub_resource_arr = array();
      // $cn=0;
          if(!empty($resSubRec) && count($resSubRec) > 0){
            foreach ($resSubRec as $key => $value1) {
              // if($value1['state_ids'] !='')
                $check_diff_counter++;
              $old_state[$value1['group_id']] = $value1['state_ids'];
              $sub_resource_arr[$value1['group_id']]['group_id'] = $value1['id'];
              $sub_resource_arr[$value1['group_id']]['coll_doc_url'] = $value1['coll_doc_url'];
              $sub_resource_arr[$value1['group_id']]['video_type'] = $value1['video_type'];

              $sub_resource_old_value_array[$value1['group_id']]['description'] = $value1['description'];
              $sub_resource_old_value_array[$value1['group_id']]['member_description'] = $value1['member_description'];
              $sub_resource_old_value_array[$value1['group_id']]['coll_type'] = $value1['coll_type'];
              $sub_resource_old_value_array[$value1['group_id']]['coll_doc_url'] = $value1['coll_doc_url'];
              $sub_resource_old_value_array[$value1['group_id']]['video_type'] = $value1['video_type'];
            }
          }

        $res_rec = $pdo->selectOne("SELECT group_concat(id) as id FROM sub_resources WHERE res_id = :resource_id AND is_deleted = 'N'", array(":resource_id" => $resId));
        if(count($res_rec) > 0 && !empty($res_rec)){        
            $pdo->delete('DELETE FROM res_states where sub_res_id IN('.$res_rec['id'].')');
        }
      
    }
    if(!empty($src_products)){
      foreach($src_products as $prd){
        $ins_param = array(
          'res_id'=> $insert_id,
          'product_id' => $prd,
          'created_at' => "msqlfunc_NOW()"
        );
        $pdo->insert('res_products',$ins_param);
      }
      $new_prd = $pdo->selectOne("SELECT group_concat(product_id) as product_ids from res_products where res_id=:id",array(":id"=>$insert_id));
    }

    unset($dyncollFields['~number~']);
    if(!empty($dyncollFields)){
      $count = 0;
      foreach ($dyncollFields as $key => $value) {
        $count++;
        $update_id = 0;
        $file_name = '';
        $video_type = '';
        if($key > 0){
          if(isset($sub_resource_arr[$key]) && !empty($sub_resource_arr[$key])){
            $update_id = $sub_resource_arr[$key]['group_id'];
            $file_name = $sub_resource_arr[$key]['coll_doc_url'];
            $video_type = $sub_resource_arr[$key]['video_type'];
            unset($sub_resource_arr[$key]);
            unset($sub_resource_arr[$key]['coll_doc_url']);
            unset($sub_resource_arr[$key]['video_type']);
          }
        }
        if($key < 0){
          array_push($new_group,$key);
        }
          $length = !empty($_FILES['coll_attachements']) ? $_FILES['coll_attachements'] : '' ;
          if($coll_type[$key] == 'pdf' && !empty($length['name'][$key])){
              $attachments_array = array();
              foreach($length as $lkey => $lvalue)
              {
                $attachments_array[$lkey]=$lvalue;
                unset($attachments_array[$lkey]['~number~']);
              }
              if(!empty($attachments_array['name'][$key])){
                  $ticket_file = $attachments_array['name'][$key];
                  if (!empty($ticket_file)) {
                    $file_type = explode("/",$attachments_array['type'][$key]);
          
                    $file_name = $resource_id.'_'.rand(1000, 9999).'_'.$attachments_array['name'][$key];
                    $file_path = $COLLATERAL_DOCUMENT_DIR."/".$coll_type[$key]."/". $file_name;
                    if (!file_exists($COLLATERAL_DOCUMENT_DIR."/".$coll_type[$key])) {
                      mkdir($COLLATERAL_DOCUMENT_DIR."/".$coll_type[$key], 0777, true);
                    }
                    move_uploaded_file($attachments_array['tmp_name'][$key], $file_path);
                    chmod($file_path, 0777);
                  }
              }
          } elseif($is_clone == "Y" && $coll_type[$key] == 'pdf' && isset($_POST['file_uploaded_url'][$key])) {
            $old_resource_id = getname('resources',$resIdOld,'display_id','id');
              if(!empty($old_resource_id)) {
                  $old_file_name = $_POST['file_uploaded_url'][$key];
                  $old_file_path = $COLLATERAL_DOCUMENT_DIR."/".$coll_type[$key]."/". $old_file_name;

                  $new_file_name = str_replace($old_resource_id,$resource_id,$old_file_name);
                  $new_file_path = $COLLATERAL_DOCUMENT_DIR."/".$coll_type[$key]."/". $new_file_name;

                  if (!file_exists($COLLATERAL_DOCUMENT_DIR."/".$coll_type[$key])) {
                    mkdir($COLLATERAL_DOCUMENT_DIR."/".$coll_type[$key], 0777, true);
                  }
                  copy($old_file_path,$new_file_path);
                  chmod($new_file_path, 0777);

                  $file_name = $new_file_name;
              }
          }
          
          $col_doc_url = '';
          if(!empty($file_name) && $coll_type[$key]=='pdf')
            {
              $col_doc_url = $file_name;
              $video_type = 'file';
            } else if(!empty($url_videos[$key]) && $coll_type[$key] == 'video' && $url_videos[$key]!='' ){
              $col_doc_url = $url_videos[$key];
              $video_type =  $video_code_type[$key];
            }else if(!empty($link_url[$key])){
              $col_doc_url = $link_url[$key];
              $video_type = '';
            }else if($coll_type[$key]=='video' && $url_videos[$key]=='' && empty($url_videos[$key])){
                $col_doc_url = $file_name;
                $video_type = 'file';
            }
          $insert_param = array(
            "res_id" => $insert_id,
            "coll_type" => $coll_type[$key],
            "description" => !empty($col_description[$key]) ? $col_description[$key] : '',
            "member_description" => !empty($certificate_mem_description[$key]) ? $certificate_mem_description[$key] : '',
            "coll_doc_url" => $col_doc_url ,
            "video_type" => $video_type,
            "group_id" => $count,
            "created_at" => "msqlfunc_NOW()"
          );
          if($update_id > 0 && $is_clone == 'N'){
            $sub_ins_id =$update_id;
            unset($insert_param['created_at']);
            $insert_param['updated_at'] = "msqlfunc_NOW()";
            $upd_where = array(
              'clause' => 'id = :id',
              'params' => array(
                ':id' => $update_id,
              ),
            );
            // $sub_resource_new_value_array[]
            // $sub_resource_old_value_array[$key]['group_id'] = $insert_param['id'];
            $sub_resource_new_value_array[$key]['description'] = $insert_param['description'];
            $sub_resource_new_value_array[$key]['member_description'] = $insert_param['member_description'];
            $sub_resource_new_value_array[$key]['coll_type'] = $insert_param['coll_type'];
            $sub_resource_new_value_array[$key]['coll_doc_url'] = $insert_param['coll_doc_url'];
            $sub_resource_new_value_array[$key]['video_type'] = $insert_param['video_type'];
            $pdo->update('sub_resources', $insert_param,$upd_where);
          }else{
            $sub_ins_id = $pdo->insert('sub_resources', $insert_param); 
          }
          
          if(!empty($states[$key])){
            foreach($states[$key] as $value){
              $ins_param = array(
                'sub_res_id'=> $sub_ins_id,
                'state_id' => $value,
                'created_at' => "msqlfunc_NOW()"
              );
              $pdo->insert('res_states',$ins_param);
            }
            $new_state[$key] = $pdo->selectOne("SELECT group_concat(state_id) as state_ids from res_states where sub_res_id=:id",array(":id"=>$sub_ins_id));
          } 
      }
      $del_res = array();
      if(!empty($sub_resource_arr) && count($sub_resource_arr) > 0 && $is_clone == 'N'){
        foreach ($sub_resource_arr as $key => $value1) {
          array_push($del_res,$value1['group_id']);
          $delete_param = array(
            "is_deleted" => 'Y',
            "updated_at" => "msqlfunc_NOW()"
          );
          $upd_where = array(
            'clause' => 'id = :id',
            'params' => array(
              ':id' => $value1['group_id'],
            ),
          );
          $pdo->update('sub_resources', $delete_param,$upd_where);
        }
      }
    }
  }else if($resources_type == 'id_card'){
      $insert_param = array(
        "display_id" => $resource_id,
        "name" => $resource_name,
        "type" => $resources_type,
        "effective_date" => date('Y-m-d',strtotime($effective_date)),
        "termination_date" => !empty($termination_date) ? date('Y-m-d',strtotime($termination_date)) : '',
        "user_group" => 'Member',
        "status" => 'Active',
        "created_at" => "msqlfunc_NOW()"
      );
      

      if(empty($resId) || $is_clone == 'Y'){
        $insert_id = $pdo->insert('resources', $insert_param);
      }else{
        $old_array_value = array();
        $new_array_value = array();
        $resource_res = $pdo->selectOne("SELECT r.id,display_id,name,effective_date,termination_date, GROUP_CONCAT(rp.product_id) as product_ids FROM resources r LEFT JOIN res_products rp ON(rp.res_id=r.id) where r.id=:id",array(':id'=>$resId));
        if(!empty($resource_res)){
          $old_prd = $resource_res['product_ids'];
          $old_array_value['display_id'] = $resource_res['display_id'];
          $old_array_value['name'] = $resource_res['name'];
          // $old_array_value['user_group'] = $resource_res['user_group'];
          $old_array_value['effective_date'] = date('m/d/Y',strtotime($resource_res['effective_date']));
          $old_array_value['termination_date'] = !empty($resource_res['effective_date']) && $resource_res['effective_date']!='0000-00-00' ? date('m/d/Y',strtotime($resource_res['termination_date'])) : '';
        }
        unset($insert_param['created_at']);
        $insert_param['updated_at'] = 'msqlfunc_NOW()';

        $new_array_value['display_id'] = $insert_param['display_id'];
        $new_array_value['name'] = $insert_param['name'];
        // $new_array_value['user_group'] = $insert_param['user_group'];
        $new_array_value['effective_date'] = date('m/d/Y',strtotime($insert_param['effective_date']));
        $new_array_value['termination_date'] = !empty($insert_param['termination_date']) && $insert_param['termination_date']!='0000-00-00' ? date('m/d/Y',strtotime($insert_param['termination_date'])) : '';

        $upd_where = array(
          'clause' => 'id = :id',
          'params' => array(
            ':id' => $resId,
          ),
        );
        $insert_id = $resId;
        $pdo->update('resources', $insert_param,$upd_where);
        $id_res = $pdo->selectOne('SELECT id from sub_resources where res_id=:id',array(":id"=>$resId));
        $pdo->delete('DELETE FROM res_products where res_id=:id',array(':id' => $resId));
      }

      if(!empty($src_products)){
        foreach($src_products as $prd){
          $ins_param = array(
            'res_id'=> $insert_id,
            'product_id' => $prd,
            'created_at' => "msqlfunc_NOW()"
          );
          $pdo->insert('res_products',$ins_param);
        }
        $new_prd = $pdo->selectOne("SELECT group_concat(product_id) as product_ids from res_products where res_id=:id",array(":id"=>$insert_id));
      }
      $insert_sub_param = array(
        "res_id" => $insert_id,
        "description" => $card_descrition,
        "created_at" => "msqlfunc_NOW()"
      );
      if(empty($resId) || $is_clone == 'Y'){
        $pdo->insert('sub_resources', $insert_sub_param);
      }else{
        unset($insert_sub_param['created_at']);
        $insert_sub_param['updated_at'] = "msqlfunc_NOW()";
        $upd_where = array(
          'clause' => 'id = :id',
          'params' => array(
            ':id' => $id_res['id'],
          ),
        );
        $pdo->update('sub_resources', $insert_sub_param,$upd_where);
      }
      
  }
    if(empty($resId) || $is_clone == 'Y'){
    $r_type = $resources_type == 'id_card' ? "Id Card" : $resources_type;
    $description['ac_message'] =array(
      'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
        'title'=>$_SESSION['admin']['display_id'],
      ),
      
      'ac_message_1' =>' Created Resources '.$r_type." ",
      'ac_red_2'=>array(
          'href'=>$ADMIN_HOST.'/resource_add.php?resource_id='.md5($insert_id),
          'title'=>$resource_id,
      ),
    );
    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $insert_id, 'resources','Admin Created Resources', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
    $result['form'] = "form_added";
    }else{
      $resource_ins_id = $resId;
      /** Activity code start */

        $r_type = $resources_type == 'id_card' ? "Id Card" : $resources_type;
        $description['ac_message'] =array(
          'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
          ),
          'ac_message_1' =>' Update Resources '.$r_type." ",
          'ac_red_2'=>array(
              'href'=>$ADMIN_HOST.'/resource_add.php?resource_id='.md5($insert_id),
              'title'=>$resource_id,
          ),
        );        

        $oldVaArray = $old_array_value;
        $NewVaArray = $new_array_value;
        $checkDiff=array_diff_assoc($NewVaArray, $oldVaArray);
        foreach ($checkDiff as $key1 => $value1) {
          if(isset($oldVaArray[$key1]) && isset($NewVaArray[$key1])){
            $description['key_value']['desc_arr'][$key1]=' updated From '.$oldVaArray[$key1].' To '.$NewVaArray[$key1].". <br>";
          }
        }
        
        $str = '';
        if(!empty($old_prd)){
          $old_prd_array = explode(",",$old_prd);
          $new_prd_array = explode(",",$new_prd['product_ids']);
          $prd_diff = array_diff($new_prd_array,$old_prd_array);
        if(count($prd_diff) > 0 && !empty($prd_diff)){
          $products = $pdo->select("SELECT name from prd_main where id IN(".implode(",",$prd_diff).")");
          if(count($new_prd_array) > count($old_prd_array)){
            $str.="Products : <br>";
            foreach ($products as $value) {
                $str.=$value['name'];
                if(count($products) > 1)
                $str.=" ,";
            }
              $str.=" added.<br>";
          }else{
            $str.="Products : <br>";
            $old_products = $pdo->select("SELECT name from prd_main where id IN(".implode(",",$old_prd_array).")");
            $new_products = $pdo->select("SELECT name from prd_main where id IN(".implode(",",$new_prd_array).")");
            foreach($old_products as $op){
              $str.=$op['name'];
            }
              $str.=" deleted.<br>";
            foreach($new_products as $np){
              $str.=$np['name'];
            }
              $str.=" added.<br>";
          }        
        }else{
          $prd_diff = array_diff($old_prd_array,$new_prd_array);
          if(count($prd_diff) > 0){
            $str.="Products : <br>";
            $products = $pdo->select("SELECT name from prd_main where id IN(".implode(",",$prd_diff).")");
            foreach ($products as $value) {
              $str.=$value['name'];
                if(count($products) > 1)
                $str.=", ";
            }
              $str.=" deleted.<br>";
          }
        }
      }
      if($resources_type != 'id_card' && $check_diff_counter > 0 ) {
      
      //array difference for states
        for($i=1;$i<=$check_diff_counter;$i++)
        {
          $flg_check = true;
          if(!empty($new_state[$i]) && !empty($old_state[$i])) {
          $state_diff = array_diff(explode(",",$new_state[$i]['state_ids']),explode(",",$old_state[$i]));
          if(count($state_diff) > 0 && !empty($state_diff)){
            $str.="States : <br>";
            if(count(explode(",",$new_state[$i]['state_ids'])) > count(explode(",",$old_state[$i]))){
              $states_c = $pdo->selectOne("SELECT group_concat(name) as name from states_c where id in(".implode(',',$state_diff).")");
                foreach (explode(",",$states_c['name']) as $value) {
                  $str.=$value;
                  if(count($state_diff) > 1)
                    $str.=", ";
              }
                $str.=" added in group ".$i.".<br>";
            }else{
              $state_diff = array_diff(explode(",",$old_state[$i]),explode(",",$new_state[$i]['state_ids']));
                if(count($state_diff) > 0){
                $states_c = $pdo->selectOne("SELECT group_concat(name) as name from states_c where id in(".implode(',',$state_diff).")");
                if(!empty($states_c)){
                  foreach (explode(",",$states_c['name'])  as $value) {
                      $str.=$value;
                        if(count(explode(",",$states_c['name'])) > 1)
                          $str.=", ";
                    }
                      $str.=" deleted in group ".$i.".<br>";
                      $flg_check = false;
                  }
                }
                $state_diff_new = array_diff(explode(",",$new_state[$i]['state_ids']),explode(",",$old_state[$i]));
                if(!empty($state_diff_new) && count($state_diff_new) > 0){
                $states_c = $pdo->selectOne("SELECT group_concat(name) as name from states_c where id in(".implode(',',$state_diff_new).")");
                if(!empty($states_c)){
                  foreach (explode(",",$states_c['name']) as $value) {
                    $str.=$value;
                    if(count($state_diff) > 1)
                      $str.=" ,";
                  }
                  $str.=" added in group ".$i.".<br>"; 
                }
              }
            }
          }
          $state_diff_ne = array_diff(explode(",",$old_state[$i]),explode(",",$new_state[$i]['state_ids']));
          if(!empty($state_diff_ne) && count($state_diff_ne) > 0 && $flg_check){
            if(count($state_diff_ne) > 0){
              $states_c = $pdo->selectOne("SELECT group_concat(name) as name from states_c where id in(".implode(',',$state_diff_ne).")");
              if(!empty($states_c)){
                foreach (explode(",",$states_c['name'])  as $value) {
                  $str.=$value;
                    if(count(explode(",",$states_c['name'])) > 1)
                      $str.=", ";
                }
                  $str.=" deleted in group ".$i."<br>";
              }
            }
          }
        }else{
          if(!empty($new_state[$i])){
            $states_c = $pdo->selectOne("SELECT group_concat(name) as name from states_c where id in(".$new_state[$i]['state_ids'].")");
            if(!empty($states_c)){
              foreach (explode(",",$states_c['name']) as $value) {
                $str.=$value;
                  if(count(explode(",",$states_c['name'])) > 1)
                    $str.=" ,";
              }
              $str.=" added in group ".$i."<br>";
            }
          }else if(!empty($old_state[$i])){
              $str.="States : <br>";
              $states_c = $pdo->selectOne("SELECT group_concat(name) as name from states_c where id in(".$old_state[$i].")");
              if(!empty($states_c)){
                foreach (explode(",",$states_c['name'])  as $value) {
                  $str.=$value;
                    if(count(explode(",",$states_c['name'])) > 1)
                      $str.=", ";
                }
                  $str.=" deleted in group ".$i.".<br>";
            }
          }
        }     
      }

      $checkSubDiff = array();
      $subNewVaArray = $sub_resource_new_value_array;
      $subOldVaArray = $sub_resource_old_value_array;
      $str_key = '';
      for($i=1;$i<=count($subNewVaArray);$i++){
        if(!empty($subNewVaArray[$i]) && !empty($subOldVaArray[$i])){
          $checkSubDiff = array_diff($subNewVaArray[$i],$subOldVaArray[$i]);
          if(!empty($checkSubDiff)) {
            foreach ($checkSubDiff as $key1 => $value1) {$count++;
              if(isset($subOldVaArray[$i][$key1]) && isset($subNewVaArray[$i][$key1])){
                $str_key.='<br> updated From '.$subOldVaArray[$i][$key1].' To '.$subNewVaArray[$i][$key1]." In Group ".$i;
                $description['key_value']['desc_arr'][$key1] = $str_key;
              }
            }
          }
        }
      }

      if(!empty($new_group)){
          $str .= "Added ".count($new_group)." new Group In ".$resources_type;
      }
      if(!empty($del_res)){
          $str .= "Deleted ".count($del_res)." Groups In ".$resources_type;
      }
    }
    $description['description'] = $str;

    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $insert_id, 'resources','Admin Updated Resources', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
    /** Activity code end */
    $result['form'] = "form_updated";
    }

    $result['status'] = "success";
    $result['message'] = "form_submited";
  } else {
    $errors = $validate->getErrors();
    $result['errors'] = $errors;
    $result['status'] = "fail";
  }

  function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}

  header('Content-type: application/json');
	echo json_encode($result); 
  dbConnectionClose();
  exit;
?>