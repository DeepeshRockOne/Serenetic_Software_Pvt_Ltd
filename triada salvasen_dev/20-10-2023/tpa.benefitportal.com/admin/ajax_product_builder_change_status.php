<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once __DIR__ . '/../includes/function.class.php';
include_once __DIR__ . '/../includes/policy_setting.class.php';
$policySetting = new policySetting();
$functionsList = new functionsList();
 
if (isset($_GET["status"])) {
  $status = isset($_GET["status"])?$_GET["status"]:'';
  $id = isset($_GET["id"])?$_GET["id"]:'';

  $sqlProduct="SELECT * FROM prd_product_builder_validation where md5(product_id)=:id";
  $resProduct=$pdo->selectOne($sqlProduct,array(":id"=>$id));



  if($resProduct){
    $product_id = $resProduct['product_id'];
    $errors = !empty($resProduct['errorJson']) ? json_decode($resProduct['errorJson'],true) : array();
    $sqlP=$pdo->selectOne("SELECT id,status,parent_product_id,product_code FROM prd_main WHERE id=:id",array(":id"=>$product_id));

    if (empty($errors)) {
        $update_params = array(
            'status' => makeSafe($status)
        );
        $update_where = array(
            'clause' => 'id = :id',
            'params' => array(
                ':id' => makeSafe($product_id)
            )
        );
        $pdo->update("prd_main",$update_params,$update_where);

        $oldVaArray = $sqlP;
        $NewVaArray = $update_params;
        unset($oldVaArray['id']);
        unset($oldVaArray['parent_product_id']);
        unset($oldVaArray['product_code']);

        $activity=array_diff_assoc($oldVaArray,$NewVaArray);
      
        $tmp = array();
        $tmp2 = array();
        if(!empty($activity)){
          $tmp['Product Status']=$activity['status'];
          $tmp2['Product Status']=$NewVaArray['status'];
          
          $actFeed=$functionsList->prdUpdActtivityFeed($product_id,$sqlP['parent_product_id'],$tmp,$tmp2,'Status',$sqlP['product_code']);
        }
        
        $res['status'] = "success";
        $res['msg'] = "Status changed successfully";

        if($status=="Extinct"){
          //set members termination date who has this product
          $website_subscriptions = $pdo->select("SELECT id,customer_id,end_coverage_period,plan_id,prd_plan_type_id FROM website_subscriptions WHERE product_id = :product_id and termination_date IS NULL",array(':product_id' => $product_id));
          
          if($website_subscriptions){

            foreach ($website_subscriptions as $key => $value) {
                $extra_params = array();
                $extra_params['location'] = "change_product_status";
                $termination_reason = "Extinct Product";
                $policySetting->setTerminationDate($value['id'],$value['end_coverage_period'],$termination_reason,$extra_params);
            }

          }

        }
    }else{
      
      $error_fields='';
      foreach ($errors as $key => $value) {
        $field = str_replace(array("Please", "Select"),array("",""), $value);
        $error_fields .= '<label class="label label-danger fs12 fw300" title="'.$value.'" data>'.$field.'</label> ';
      }
      $res['errors'] = $errors;
      $res['error_fields'] = $error_fields;
      $res['status'] = "fail";
    }
  }else{
      $res['errors'] = 'No Record Found';
      $res['error_fields'] = '<label class="label label-danger fs12 fw300" title="No Record Found" data>No Record Found</label> ';
      $res['status'] = "fail";
  }
  header('Content-type: application/json');
  echo json_encode($res);
  dbConnectionClose();
  exit;
}

?>

