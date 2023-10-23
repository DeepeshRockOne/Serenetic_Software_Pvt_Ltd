<?php

/*
 * Class for product settings
 */

class Product {

  public function get_product_price($product_ids,$plan_id){
    global $pdo;
    $product_array = array();
    
    if(count($product_ids) > 0){
      foreach ($product_ids as $key => $product) {
        $product_id = $product;
        $matrix_id = $plan_id[$product_id];
        $sqlProduct="SELECT p.id,p.name as product_name,p.product_code,pm.id as matrix_id,ppt.title as plan_name,pm.price,p.type,p.product_type,p.parent_product_id 
          FROM prd_main p
          JOIN prd_matrix pm ON (p.id=pm.product_id)
          JOIN prd_plan_type ppt ON (ppt.id=pm.plan_type)
          WHERE p.is_deleted='N' AND pm.is_deleted='N' AND p.status='Active' AND pm.product_id = :product_id AND pm.id = :matrix_id";
        $whereProduct = array(":product_id"=>$product_id,":matrix_id"=>$matrix_id);
        $resProduct=$pdo->selectOne($sqlProduct,$whereProduct);
        
        if($resProduct){
          $product_array[$product_id] = $resProduct;
        }
  
      }
    }
    return $product_array;
  }
  
  public function get_restricted_products($product_ids,$plan_id){
    global $pdo;
    $restricted_product_array = array();
  
    if(count($product_ids) > 0){
      foreach ($product_ids as $key => $product) {
        $product_id = $product;
        $matrix_id = $plan_id[$product_id];
        $sqlProduct="SELECT p.*,pm.price FROM prd_main p
          JOIN prd_matrix pm ON (p.id=pm.product_id)
          WHERE p.is_deleted='N' AND pm.is_deleted='N' AND p.status='Active' AND pm.product_id = :product_id AND pm.id = :matrix_id";
        $whereProduct = array(":product_id"=>$product_id,":matrix_id"=>$matrix_id);
        $resProduct=$pdo->selectOne($sqlProduct,$whereProduct);
        
        if($resProduct){
          if(!empty($resProduct['restricted_products'])){
            $restricted_product_array[$resProduct['id']] =$resProduct['restricted_products'];
            /*$restricted_ids_str="";
            $resPrd=explode(",", $resProduct['restricted_products']);
            if(count($resPrd)>0){
              foreach ($resPrd as $keyp => $valuep) {
                $sqlPrdV="select group_concat(id) as v_ids from prd_main where parent_product_id=:id";
                $resPrdV=$pdo->selectOne($sqlPrdV,array(':id'=>$valuep));
                if($resPrdV && !empty($resPrdV['v_ids'])){
                  $restricted_ids_str.=$resPrdV['v_ids'].',';
                }
              }
            }
            $restricted_ids_str=trim($restricted_ids_str,',');
            //$restricted_product_array = array_merge($restricted_product_array,explode(",", $resProduct['restricted_products']));
            if($restricted_ids_str!=""){
              $restricted_product_array[$resProduct['id']] =$resProduct['restricted_products'].','.$restricted_ids_str;
            }else{
              $restricted_product_array[$resProduct['id']] =$resProduct['restricted_products'];
            }*/
            
          }
        }
      }
    }
    return $restricted_product_array;
  }
  
  public function get_service_fee_price($product_id,$renewCount=0){
    global $pdo;
    $price = 0;
    $product_code = '';
    $product_name = '';
    $id = 0;
    $matrix_id = 0;
    $is_fee_on_renewal = '';
    $fee_renewal_type = '';
    $fee_renewal_count = 0;
    $fee_product_id=0;
    $fee_type = '';
    $type = '';
    $incr="";
    $sch_params=array();
    $service_fees_details=array();
  
    if(!empty($renewCount)){
      $incr.=" AND pp.is_fee_on_renewal = 'Y' AND (pp.fee_renewal_type='Continuous' OR :renewCount < pp.fee_renewal_count)";
      $sch_params['renewCount'] = $renewCount;
    }
  
    if(!empty($product_id)){
      $product_id_list=implode(",", $product_id);
  
      $sqlProduct="SELECT pm.price,pp.name,pp.id,pm.id as matrixId,pp.product_code,pp.is_fee_on_renewal,
      pp.fee_renewal_type,pp.fee_renewal_count,p.id as fee_product_id,pp.product_type,pp.type,pp.parent_product_id
      FROM prd_main p
        JOIN prd_main pp ON FIND_IN_SET(pp.id,p.service_fee_ids)
        JOIN prd_matrix pm ON (pp.id = pm.product_id)
      WHERE p.id in ($product_id_list) AND pp.status='Active' AND p.is_deleted='N' $incr AND pp.is_deleted='N'";
      $resProduct=$pdo->select($sqlProduct,$sch_params);
  
      if(count($resProduct) >0){
        foreach ($resProduct as $key => $product) {
          if($product['price'] > $price){
            $fee_product_id = $product['fee_product_id'];
            $price = $product['price'];
            $product_name = $product['name'];
            $product_code = $product['product_code'];
            $id = $product['id'];
            $matrix_id = $product['matrixId'];
            $is_fee_on_renewal = $product['is_fee_on_renewal'];
            $fee_renewal_type = $product['fee_renewal_type'];
            $fee_renewal_count = $product['fee_renewal_count'];
            $fee_type = $product['product_type'];
            $type = $product['type'];
            $parent_product_id = $product['parent_product_id'];
          }
        }
      }
    }
  
    if($id > 0){
      $service_fees_details[$fee_product_id] = array(
        'product_id'=>$fee_product_id,
        'price'=>$price,
        'product_name'=>$product_name,
        'product_code'=>$product_code,
        'id'=>$id,
        'matrix_id'=>$matrix_id,
        'is_fee_on_renewal'=>$is_fee_on_renewal,
        'fee_renewal_type'=>$fee_renewal_type,
        'fee_renewal_count'=>$fee_renewal_count,
        'fee_type'=>$fee_type,
        'type'=>$type,
        'parent_product_id'=>$parent_product_id,
      );
    }
    
  
    return $service_fees_details;
  }

  public function get_association_fee($product_id,$customer_id = 0,$renewCount=0,$zip_code=0){
    global $pdo;
    $price = 0;
    $product_code = '';
    $feeproduct_name = '';
    $id = 0;
    $matrix_id = 0;
    $is_fee_included_in_product = '';
    $is_fee_on_renewal = '';
    $fee_renewal_type = '';
    $fee_renewal_count = 0;
    $fee_type = '';
    $type = '';
    $incr="";
    $sch_params=array();
    $fee_product_id = 0;
    $association_fees_details=array();
  
    if(!empty($renewCount)){
      $incr.=" AND pp.is_fee_on_renewal = 'Y' AND (pp.fee_renewal_type='Continuous' OR :renewCount < pp.fee_renewal_count)";
      $sch_params['renewCount'] = $renewCount;
    }
    
  
    if(!empty($product_id)){
  
      if(is_array($product_id)){
        $product_id_list=implode(",", $product_id);
      }else{
        $product_id_list=$product_id;
      }
      
      //********************** Fee Based on state code start *********************
        $extra_params=array();
        if(!empty($zip_code)){
          $state_name='';
          $getStateCode=$pdo->selectOne("SELECT * from zip_code WHERE zip_code=:zip_code",array(":zip_code"=>$zip_code));
          if($getStateCode){
            $state_name = getname("states_c",$getStateCode['state_code'],"name","short_name");
          }
          if(!empty($state_name)){
            if(!empty($product_id)){
              $assignByStateSql="SELECT GROUP_CONCAT(association_fee_id) as fee_id FROM association_assign_by_state WHERE product_id in ($product_id_list) AND FIND_IN_SET(:state_name,states) AND is_deleted='N'";
              $assignByStateRes=$pdo->selectOne($assignByStateSql,array(":state_name"=>$state_name));
  
              if($assignByStateRes && !empty($assignByStateRes['fee_id'])){
                $extra_params['assigned_fee_id'] = $assignByStateRes['fee_id'];
              }
            }
          }
        }
        
        if(!empty($extra_params)){
          if(!empty($extra_params['assigned_fee_id'])){
            $assignedFeeId=$extra_params['assigned_fee_id'];
            $incr.=" AND pp.id in ($assignedFeeId)";
          }
        }else{
          $incr.=" AND pp.is_assign_by_state ='N'";
        }
      //********************** Fee Based on state code end   *********************
  
      $sqlProduct="SELECT pm.price,pp.name,pp.id,pm.id as matrixId,pp.product_code,pp.is_fee_on_renewal,
      pp.fee_renewal_type,pp.fee_renewal_count,pp.is_association_fee_included,p.id as fee_product_id,pp.product_type,pp.type,pp.parent_product_id
      FROM prd_main p
        JOIN prd_main pp ON FIND_IN_SET(pp.id,p.association_ids)
        JOIN prd_matrix pm ON (pp.id = pm.product_id)
      WHERE p.id in ($product_id_list) AND pp.status='Active' AND p.is_deleted='N' $incr AND pp.is_deleted='N'";
      $resProduct=$pdo->select($sqlProduct,$sch_params);
  
      if(count($resProduct) >0){
        foreach ($resProduct as $key => $product) {
          if($product['price'] > $price){
            $fee_product_id = $product['fee_product_id'];
            $price = $product['price'];
            $product_name = $product['name'];
            $product_code = $product['product_code'];
            $id = $product['id'];
            $matrix_id = $product['matrixId'];
            $is_fee_on_renewal = $product['is_fee_on_renewal'];
            $is_fee_included_in_product = $product['is_association_fee_included'];
            $fee_renewal_type = $product['fee_renewal_type'];
            $fee_renewal_count = $product['fee_renewal_count'];
            $fee_type = $product['product_type'];
            $type = $product['type'];
            $parent_product_id = $product['parent_product_id'];
          }
        }
      }
    }
  
    if(!empty($customer_id)){
        $incr = '';
        $sch_params = array();
  
        $sch_params[':customer_id'] = $customer_id;
        $incr .= " AND customer_id = :customer_id";
  
        $today = date('Y-m-d');
        
        $fromdate = date('m/01/Y',strtotime($today));
        $todate = date('m/d/Y',strtotime($today));
  
        if ($fromdate != "") {
            $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($fromdate));
            $incr .= " AND DATE(o.created_at) >= :fcreated_at";
            //$incr .= " AND DATE(od.start_coverage_period) >= :fcreated_at";
        }
  
        if ($todate != "") {
            $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($todate));
            $incr .= " AND DATE(o.created_at) <= :tcreated_at";
            //$incr .= " AND DATE(od.end_coverage_period) <= :tcreated_at";
        }
        $sqlOrder="SELECT pm.price,p.name,p.id,pm.id as matrixId,p.product_code,p.is_fee_on_renewal,
      p.fee_renewal_type,p.fee_renewal_count,p.is_association_fee_included FROM orders as o 
                                    JOIN order_details as od ON(od.order_id = o.id AND od.is_deleted='N')
                                    JOIN prd_main as p ON (od.product_id = p.id) 
                                    JOIN prd_matrix as pm ON (pm.product_id = p.id) 
                                    WHERE o.status = 'Payment Approved' $incr AND p.product_type='Association'";
        $resOrder = $pdo->select($sqlOrder, $sch_params);
  
        $is_association_fee_applied = array();
        if(count($resOrder) > 0) {
          //array_push($is_association_fee_applied,'yes');
            foreach ($resOrder as $key => $value) {
              /*if($associationPrice > $value['price']){
  
              }else{
                array_push($is_association_fee_applied,'yes');
              }*/
              array_push($is_association_fee_applied,'yes');
            }
        }
    }
  
    if(!empty($is_association_fee_applied) && in_array('yes', $is_association_fee_applied)){
      $fee_product_id = 0;
      $price = 0;
      $product_name = '';
      $product_code = '';
      $id = 0;
      $matrix_id = 0;
      $is_fee_on_renewal = '';
      $is_fee_included_in_product = '';
      $fee_renewal_type = '';
      $fee_renewal_count = 0;
      $fee_type = '';
      $type = '';
      $parent_product_id = 0;
    }
  
    if($id>0){
      $association_fees_details[$fee_product_id] = array(
        'product_id'=>$fee_product_id,
        'price'=>$price,
        'product_name'=>$product_name,
        'product_code'=>$product_code,
        'id'=>$id,
        'matrix_id'=>$matrix_id,
        'is_fee_on_renewal'=>$is_fee_on_renewal,
        'is_fee_included_in_product'=>$is_fee_included_in_product,
        'fee_renewal_type'=>$fee_renewal_type,
        'fee_renewal_count'=>$fee_renewal_count,
        'fee_type'=>$fee_type,
        'type'=>$type,
        'parent_product_id'=>$parent_product_id,
      );
    }
  
    return $association_fees_details;
  }
  public function get_admin_fee($product_id,$renewCount=0){
    global $pdo;
    $incr="";
    $sch_params=array();
    $admin_fees_details=array();
  
    if(!empty($renewCount)){
      $incr.=" AND pp.is_fee_on_renewal = 'Y' AND (pp.fee_renewal_type='Continuous' OR :renewCount < pp.fee_renewal_count)";
      $sch_params['renewCount'] = $renewCount;
    }
  
    if(!empty($product_id)){
      $product_id_list=implode(",", $product_id);
  
      $sqlProduct="SELECT pm.price,pp.name,pp.id,pm.id as matrixId,pp.product_code,pp.is_fee_on_renewal,
      pp.fee_renewal_type,pp.fee_renewal_count,p.id as product_id,pp.product_type,pp.type,pp.parent_product_id
      FROM prd_main p
        JOIN prd_main pp ON FIND_IN_SET(pp.id,p.admin_fee_ids)
        JOIN prd_matrix pm ON (pp.id = pm.product_id)
      WHERE p.id in ($product_id_list) AND pp.status='Active' AND p.is_deleted='N' $incr AND pp.is_deleted='N'";
      $resProduct=$pdo->select($sqlProduct,$sch_params);
  
      if(count($resProduct) >0){
        foreach ($resProduct as $key => $product) {
          if(isset($admin_fees_details[$product['product_id']])){
            if($admin_fees_details[$product['product_id']]['price']<=$product['price']){
              $admin_fees_details[$product['product_id']] = array(
                'product_id'=>$product['product_id'],
                'price'=>$product['price'],
                'product_name'=>$product['name'],
                'product_code'=>$product['product_code'],
                'id'=>$product['id'],
                'matrix_id'=>$product['matrixId'],
                'is_fee_on_renewal'=>$product['is_fee_on_renewal'],
                'fee_renewal_type'=>$product['fee_renewal_type'],
                'fee_renewal_count'=>$product['fee_renewal_count'],
                'fee_type'=>$product['product_type'],
                'type'=>$product['type'],
                'parent_product_id'=>$product['parent_product_id'],
              );
            }
          }else{
            $admin_fees_details[$product['product_id']] = array(
              'product_id'=>$product['product_id'],
              'price'=>$product['price'],
              'product_name'=>$product['name'],
              'product_code'=>$product['product_code'],
              'id'=>$product['id'],
              'matrix_id'=>$product['matrixId'],
              'is_fee_on_renewal'=>$product['is_fee_on_renewal'],
              'fee_renewal_type'=>$product['fee_renewal_type'],
              'fee_renewal_count'=>$product['fee_renewal_count'],
              'fee_type'=>$product['product_type'],
              'type'=>$product['type'],
              'parent_product_id'=>$product['parent_product_id'],
            );
          }       
        }
      }
    }
    return $admin_fees_details;
  }
  public function get_enrollment_fee($product_id,$renewCount=0){
    global $pdo;
    $incr="";
    $sch_params=array();
    $enrollment_fees_details=array();
  
    if(!empty($renewCount)){
      $incr.=" AND pp.is_fee_on_renewal = 'Y' AND (pp.fee_renewal_type='Continuous' OR :renewCount < pp.fee_renewal_count)";
      $sch_params['renewCount'] = $renewCount;
    }
  
    if(!empty($product_id)){
      $product_id_list=implode(",", $product_id);
  
      $sqlProduct="SELECT pm.price,pp.name,pp.id,pm.id as matrixId,pp.product_code,pp.is_fee_on_renewal,
      pp.fee_renewal_type,pp.fee_renewal_count,p.id as product_id,pp.product_type,pp.type,pp.parent_product_id
      FROM prd_main p
        JOIN prd_main pp ON FIND_IN_SET(pp.id,p.enrollment_fee_ids)
        JOIN prd_matrix pm ON (pp.id = pm.product_id)
      WHERE p.id in ($product_id_list) AND pp.status='Active' AND p.is_deleted='N' $incr AND pp.is_deleted='N'";
      $resProduct=$pdo->select($sqlProduct,$sch_params);
  
      if(count($resProduct) >0){
        foreach ($resProduct as $key => $product) {
          if(isset($enrollment_fees_details[$product['product_id']])){
            if($enrollment_fees_details[$product['product_id']]['price']<=$product['price']){
              $enrollment_fees_details[$product['product_id']] = array(
                'product_id'=>$product['product_id'],
                'price'=>$product['price'],
                'product_name'=>$product['name'],
                'product_code'=>$product['product_code'],
                'id'=>$product['id'],
                'matrix_id'=>$product['matrixId'],
                'is_fee_on_renewal'=>$product['is_fee_on_renewal'],
                'fee_renewal_type'=>$product['fee_renewal_type'],
                'fee_renewal_count'=>$product['fee_renewal_count'],
                'fee_type'=>$product['product_type'],
                'type'=>$product['type'],
                'parent_product_id'=>$product['parent_product_id'],
              );
            }
          }else{
            $enrollment_fees_details[$product['product_id']] = array(
              'product_id'=>$product['product_id'],
              'price'=>$product['price'],
              'product_name'=>$product['name'],
              'product_code'=>$product['product_code'],
              'id'=>$product['id'],
              'matrix_id'=>$product['matrixId'],
              'is_fee_on_renewal'=>$product['is_fee_on_renewal'],
              'fee_renewal_type'=>$product['fee_renewal_type'],
              'fee_renewal_count'=>$product['fee_renewal_count'],
              'fee_type'=>$product['product_type'],
              'type'=>$product['type'],
              'parent_product_id'=>$product['parent_product_id'],
            );
          }       
        }
      }
    }
    return $enrollment_fees_details;
  }

}

?>