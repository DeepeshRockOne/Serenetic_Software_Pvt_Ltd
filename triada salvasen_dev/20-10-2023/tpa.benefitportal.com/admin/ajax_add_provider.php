<?php include_once dirname(__FILE__) . '/layout/start.inc.php';
  
  $validate = new Validation();
  $result = array();

  $proId = !empty($_POST['proId']) ? $_POST['proId'] : '';
  $provider_name = !empty($_POST['provider_name']) ? $_POST['provider_name'] : '';
  $display_id = !empty($_POST['display_id']) ? $_POST['display_id'] : '';
  $dynamicFields = !empty($_POST['dynamicFields']) ? $_POST['dynamicFields'] : array();
  $products = !empty($_POST['products']) ? $_POST['products'] : array();
  $url_products = !empty($_POST['url_products']) ? $_POST['url_products'] : array();

  $validate->string(array('required' => true, 'field' => 'provider_name', 'value' => $provider_name), array('required' => 'Provider Name is required'));
  $validate->string(array('required' => true, 'field' => 'display_id', 'value' => $display_id), array('required' => 'Provider ID is required'));

  if(!empty($display_id)){
    $schParams=array(":display_id"=>$display_id);
    $incr='';
    if(!empty($proId)){
      $incr.=" AND id != :id";
      $schParams[':id'] = $proId;
    }
    $sqlProviderRule="SELECT id FROM providers where display_id = :display_id AND is_deleted='N' $incr";
    $resProviderRule=$pdo->selectOne($sqlProviderRule,$schParams);

    if(!empty($resProviderRule)){
      $validate->setError('display_id',"Provider ID Already Exists");
    }
  }

  if(!empty($dynamicFields)){
    foreach ($dynamicFields as $key => $value) {
      if(empty($products[$key])){
        $validate->setError("products_".$key,"Please Select Product");
      } else {
        $validate->url(array('required' => true, 'field' => 'url_products_'.$key, 'value' => $url_products[$key]), array('required' => 'Please Enter URL', 'invalid' => 'Please Enter valid URL'));
      }
    }
  }
  
  if ($validate->isValid()) {

    $pro_insert_param = array(
      "display_id" => $display_id,
      "name" => $provider_name,
      "status" => 'Active',
      "updated_at" => "msqlfunc_NOW()",
      "created_at" => "msqlfunc_NOW()"
    );

    $group_product = array();
    if(empty($proId)){
      $insert_id = $pdo->insert('providers', $pro_insert_param);
    } else {

      $select_product = "SELECT p.id,p.name
                  FROM prd_main as p
                  JOIN prd_category as pc ON (p.category_id = pc.id) 
                  WHERE p.status = 'Active' AND p.is_deleted = 'N' ORDER BY p.name ASC";
      $product_res = $pdo->select($select_product);

      $old_array_value = array();
      $new_array_value = array();
      $sub_provider_old_value_array = array();
      $sub_provider_new_value_array = array();

      $insert_id = $proId;
      $providers_res = $pdo->selectOne("SELECT display_id,name FROM providers WHERE id = :id", array(":id" => $proId));
      if(!empty($providers_res)){
        $old_array_value['display_id'] = $providers_res['display_id'];
        $old_array_value['name'] = $providers_res['name'];
      }

      unset($pro_insert_param['created_at']);
      unset($pro_insert_param['status']);
      $upd_where = array(
        'clause' => 'id = :id',
        'params' => array(
          ':id' => $proId,
        ),
      );

      $new_array_value['display_id'] = $pro_insert_param['display_id'];
      $new_array_value['name'] = $pro_insert_param['name'];

      $pdo->update('providers', $pro_insert_param, $upd_where);

      $sub_providers_res = $pdo->select("SELECT id,product_id,group_id,url FROM sub_provider as sp WHERE providers_id = :provider_id AND is_deleted = 'N'", array(":provider_id" => $proId));
      $sub_provider_arr = array();
      if(!empty($sub_providers_res) && count($sub_providers_res) > 0){
        foreach ($sub_providers_res as $key => $value) {
          $sub_provider_arr[$value['group_id']][$value['product_id']] = $value['id'];

          $old_array_value['product_group_url_'.$value['group_id']] = $value['url'];
          $sub_provider_old_value_array[$value['group_id']][$value['product_id']] = array(
            'product_id' => $value['product_id'],
            'is_deleted' => 'N',
          );
        }
      }
    }

    if(!empty($products) && count($products) > 0){
      $count = 0;
      foreach ($products as $key => $prd) {
        $count++;
        if(!empty($prd) && count($prd) > 0) {
          if(!in_array($count, $group_product)){
            array_push($group_product, $count);
          }
          foreach ($prd as $key1 => $value) {
            $update_id = 0;
            if($key > 0){
              if(isset($sub_provider_arr[$key][$value]) && !empty($sub_provider_arr[$key][$value])){
                $update_id = $sub_provider_arr[$key][$value];
                unset($sub_provider_arr[$key][$value]);
              }
            }
            $sub_pro_insert_param = array(
              "providers_id" => $insert_id,
              "product_id" => $value,
              "group_id" => $count,
              "url" => $url_products[$key],
              "updated_at" => "msqlfunc_NOW()",
              "created_at" => "msqlfunc_NOW()"
            );
            if($update_id > 0) {
              unset($sub_pro_insert_param['created_at']);
              unset($sub_pro_insert_param['providers_id']);
              $upd_where = array(
                'clause' => 'id = :id',
                'params' => array(
                  ':id' => $update_id,
                ),
              );

              $pdo->update('sub_provider', $sub_pro_insert_param, $upd_where);
            } else {
              $pdo->insert('sub_provider', $sub_pro_insert_param);
            }
            $new_array_value['product_group_url_'.$count] = $url_products[$key];
            $sub_provider_new_value_array[$count][$sub_pro_insert_param['product_id']] = array(
              'product_id' => $sub_pro_insert_param['product_id'],
              'is_deleted' => 'N'
            );
          }
        }
      }
    }

    $check_diff_counter = $count;
    if(!empty($sub_provider_arr) && count($sub_provider_arr) > 0){
      foreach ($sub_provider_arr as $sub_key => $sub_value) {
        $delete_param = array(
          "is_deleted" => 'Y',
          "updated_at" => "msqlfunc_NOW()"
        );
        if(!empty($sub_value) && count($sub_value)){
          if(!in_array($sub_key, $group_product)){
            $check_diff_counter++ ;
            array_push($group_product, $check_diff_counter);
          }
          foreach ($sub_value as $key1 => $value1) {
            if($value1 > 0) {
              $upd_where = array(
                'clause' => 'id = :id',
                'params' => array(
                  ':id' => $value1,
                ),
              );

              $sub_provider_new_value_array[$check_diff_counter][$key1] = array(
                'product_id' => $sub_pro_insert_param['product_id'],
                'is_deleted' => 'Y'
              );
              $pdo->update('sub_provider', $delete_param, $upd_where);
            }
          }
        }
      }
    }

    if(empty($proId)) {
      $description['ac_message'] =array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>' Created Providers ',
        'ac_red_2'=>array(
            'href'=>$ADMIN_HOST.'/add_providers.php?providers_id='.md5($insert_id),
            'title'=>$display_id,
        ),
      );

      activity_feed(3, $_SESSION['admin']['id'], 'Admin', $insert_id, 'provider','Admin Created Provider', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
    } else {
      $provider_inc_id = $proId;
      //************* Activity Code Start *************
      $oldVaArray = $old_array_value;
      $NewVaArray = $new_array_value;
      unset($oldVaArray['id']);

      $checkDiff=array_diff_assoc($NewVaArray, $oldVaArray);

      $activity_feed_desc = array();
      if($check_diff_counter > 0){
        for ($i=1; $i <= $check_diff_counter; $i++) { 
          if(!empty($product_res) && count($product_res) > 0){
            $str = '';
            foreach ($product_res as $key => $value) {
              if(!isset($sub_provider_old_value_array[$i][$value['id']])){
                $sub_provider_old_value_array[$i][$value['id']] = array();
              }
              if(!isset($sub_provider_new_value_array[$i][$value['id']])){
                $sub_provider_new_value_array[$i][$value['id']] = array();
              }
              $check_product_diff = array_diff_assoc($sub_provider_new_value_array[$i][$value['id']], $sub_provider_old_value_array[$i][$value['id']]);
              if(!empty($check_product_diff)){
                if(empty($sub_provider_old_value_array[$i][$value['id']]) && !empty($sub_provider_new_value_array[$i][$value['id']])) {
                  if(empty($str)){
                    $str .= 'Added '. $value['name'];
                  } else {
                    $str .= ' and Added '. $value['name'];
                  }
                } else if(!empty($sub_provider_old_value_array[$i][$value['id']]) && empty($sub_provider_new_value_array[$i][$value['id']])) {
                  if(empty($str)){
                    $str .= 'Deleted '. $value['name'];
                  } else {
                    $str .= ' and Deleted '. $value['name'];
                  }
                } else if(!empty($sub_provider_old_value_array[$i][$value['id']]) && !empty($sub_provider_new_value_array[$i][$value['id']])) {
                  if($sub_provider_new_value_array[$i][$value['id']]['is_deleted'] == 'Y'){
                    if(empty($str)){
                      $str .= 'Deleted '. $value['name'];
                    } else {
                      $str .= ' and Deleted '. $value['name'];
                    }
                  } else {
                    if(empty($str)){
                      $str .= 'Added '. $value['name'];
                    } else {
                      $str .= ' and Added '. $value['name'];
                    }
                  }
                } 
              }
            }
          }
          if(!empty($str)){
            $activity_feed_desc['product_group_'.$i] = $str;
          }
        }
      }

      if(!empty($checkDiff) || !empty($activity_feed_desc)){

        $activityFeedDesc['ac_message'] =array(
          'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id']),
          'ac_message_1' =>' Updated Provider ',
        ); 
        
        $extraJson = array();
        foreach ($checkDiff as $key1 => $value1) {
          if(!isset($oldVaArray[$key1]) && isset($NewVaArray[$key1])){
            $activityFeedDesc['key_value']['desc_arr'][$key1] = 'Added '.$NewVaArray[$key1];
          } else if(isset($oldVaArray[$key1]) && !isset($NewVaArray[$key1])){
            $activityFeedDesc['key_value']['desc_arr'][$key1] = 'Deleted '.$oldVaArray[$key1];
          } else if(isset($oldVaArray[$key1]) && isset($NewVaArray[$key1])){
            $activityFeedDesc['key_value']['desc_arr'][$key1] = 'From '.$oldVaArray[$key1].' To '.$NewVaArray[$key1];
          }
        }

        if(!empty($activity_feed_desc)){
          foreach ($activity_feed_desc as $ind => $ele) {
            $activityFeedDesc['key_value']['desc_arr'][$ind] = $ele;
          }
        }

        $activityFeedDesc['ac_message']['ac_red_2']=array(
          'href'=>$ADMIN_HOST.'/add_providers.php?providers_id='.md5($provider_inc_id),
          'title'=>$providers_res['display_id']
        ); 
        
        activity_feed(3, $_SESSION['admin']['id'], 'Admin', $provider_inc_id, 'provider','Admin Updated Provider', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc),'',json_encode($extraJson));
      }
      //************* Activity Code End *************
    }

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