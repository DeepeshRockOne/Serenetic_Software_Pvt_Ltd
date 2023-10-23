<?php

/*
 * Class for product settings
 */

class MemberEnrollment
{

  public function getEnrollmentPricingQuestions($sponsor_id)
  {
    global $pdo;
    $responseArray = array();

    $sqlSponsor = "SELECT ppq.*,IF(ppqa.assign_type is null,'Primary',ppqa.assign_type) as assign_type  
        FROM prd_main p 
        JOIN agent_product_rule apr ON (apr.product_id = p.id AND apr.status='Contracted' AND apr.is_deleted='N')
        JOIN prd_pricing_question_assigned ppqa ON (ppqa.product_id = apr.product_id AND ppqa.is_deleted='N')
        JOIN prd_pricing_question ppq ON (ppq.id = ppqa.prd_pricing_question_id)
        WHERE apr.agent_id=:sponsor_id AND p.is_deleted='N' AND p.status='Active' ORDER BY ppq.order_by ASC";
    $resSponsor = $pdo->select($sqlSponsor, array(":sponsor_id" => $sponsor_id));


    $responseArray = array();
    if (count($resSponsor) > 0) {
      foreach ($resSponsor as $key => $value) {
        if (!array_key_exists($value['assign_type'], $responseArray)) {
          $responseArray[$value['assign_type']] = array();
        }
        $responseArray[$value['assign_type']][$value['id']] = $value;
      }
    }

    $sqlDefaultQuestion = "SELECT *,'Primary' as assign_type FROM prd_pricing_question WHERE label in ('quePricingAge','quePricingZipCode') ORDER BY order_by ASC";
    $resDefaultQuestion = $pdo->select($sqlDefaultQuestion);

    if (!empty($resDefaultQuestion)) {
      foreach ($resDefaultQuestion as $key => $value) {
        if (!isset($responseArray['Primary']) || !array_key_exists($value['id'], $responseArray['Primary'])) {
          $responseArray['Primary'][$value['id']] = $value;
        }
      }
    }
    return $responseArray;
  }

  public function isFullLicenseExpired($agent_id = 0, $checkExtended = true)
  {
    global $pdo;
    $licenseExpired = false;
    $licenseExpiredCount = 0;
    $dbLicenseExpiredCount = 0;
    $getLicense = $pdo->select("SELECT license_exp_date,extended_date from agent_license WHERE agent_id=:agent_id AND is_deleted='N'", array(":agent_id" => $agent_id));
    if (!empty($getLicense)) {
      $dbLicenseExpiredCount = count($getLicense);
      foreach ($getLicense as $lkey => $res) {
        $licenseExpDate = $res["license_exp_date"];
        //license is expired
        if (empty($licenseExpDate)  || (strtotime($licenseExpDate) < strtotime(date("Y-m-d")))) {
          $licenseExpired = true;
          if ($checkExtended) {
            $extendedDate = $res["extended_date"];
            if (!empty($extendedDate)) {
              if (strtotime($extendedDate) > strtotime(date("Y-m-d"))) {
                $licenseExpired = false;
              }
            }
          }
          if ($licenseExpired) {
            $licenseExpiredCount++;
          }
        }
      }
    }
    return ($licenseExpiredCount == $dbLicenseExpiredCount ? true : false);
  }

  public function getActiveLicensedStates($agent_id = 0, $license_type = '', $checkExtended = true)
  {
    global $pdo;
    $licenseStates = array();
    $incr = "";
    if (!empty($license_type)) {
      $license_type_arr = explode(",", $license_type);
      if (in_array("Health", $license_type_arr) && in_array("Life", $license_type_arr)) {
        $incr .= " AND (license_auth = 'Life' OR license_auth = 'Health' OR license_auth = 'general_lines')";
      } else if (in_array("Health", $license_type_arr)) {
        $incr .= " AND (license_auth = 'Health' OR license_auth = 'general_lines')";
      } else if (in_array("Life", $license_type_arr)) {
        $incr .= " AND (license_auth = 'Life' OR license_auth = 'general_lines')";
      }
    }
    $getLicense = $pdo->select("SELECT license_exp_date,extended_date,selling_licensed_state from agent_license WHERE agent_id=:agent_id AND is_deleted='N' $incr ", array(":agent_id" => $agent_id));

    if (!empty($getLicense)) {
      foreach ($getLicense as $lkey => $res) {
        $licenseExpDate = $res["license_exp_date"];

        if (empty($licenseExpDate) || (strtotime($licenseExpDate) < strtotime(date("Y-m-d")))) {
          if ($checkExtended) {
            $extendedDate = $res["extended_date"];
            if (!empty($extendedDate)) {

              if (strtotime($extendedDate) > strtotime(date("Y-m-d"))) {

                $state_tmp = !empty($res["selling_licensed_state"]) ? explode(",", $res["selling_licensed_state"]) : array();
                if (!empty($state_tmp)) {
                  foreach ($state_tmp as $t) {
                    $licenseStates[] = $t;
                  }
                }
              }
            }
          }
        } else {
          $state_tmp = !empty($res["selling_licensed_state"]) ? explode(",", $res["selling_licensed_state"]) : array();
          if (!empty($state_tmp)) {
            foreach ($state_tmp as $t) {
              $licenseStates[] = $t;
            }
          }
        }
      }
    }
    return $licenseStates;
  }

  public function getProductLicensedStates($product_id, $license_rule)
  {
    global $pdo, $allStateRes;

    $getLicense = $pdo->select("SELECT state_id,sale_type from prd_license_state WHERE product_id=:product_id AND is_deleted='N' AND license_rule=:license_rule", array(":product_id" => $product_id, ":license_rule" => $license_rule));

    $licenseStates = array();

    if (!empty($getLicense)) {
      foreach ($getLicense as $lkey => $res) {

        if (!array_key_exists($res['sale_type'], $licenseStates)) {
          $licenseStates[$res['sale_type']] = array();
        }
        array_push($licenseStates[$res['sale_type']], $allStateRes[$res['state_id']]['name']);
      }
    }
    return $licenseStates;
  }

  public function getPriceAssignedQuestion($product_id)
  {
    global $pdo;

    $sqlAssigned = "SELECT ppq.*,IF(ppqa.assign_type is null,'Primary',ppqa.assign_type) as assign_type  
        FROM prd_pricing_question_assigned ppqa
        JOIN prd_pricing_question ppq ON (ppq.id = ppqa.prd_pricing_question_id AND ppq.is_deleted='N')
        WHERE ppqa.is_deleted='N' AND ppqa.product_id=:product_id  ORDER BY ppq.order_by ASC";
    $resAssgined = $pdo->select($sqlAssigned, array(":product_id" => $product_id));

    $responseArray = array();
    if (!empty($resAssgined)) {
      foreach ($resAssgined as $key => $value) {
        if (!array_key_exists($value['assign_type'], $responseArray)) {
          $responseArray[$value['assign_type']] = array();
        }
        $responseArray[$value['assign_type']][$value['id']] = $value;
      }
    }

    return $responseArray;
  }
  public function assignedQuestionValue($product_id, $prd_plan_type_id = 0)
  {
    global $pdo;
    $today_date = date('Y-m-d');

    $incr = "";
    $sch_params = array();
    $sch_params[':product_id'] = $product_id;
    $sch_params[':today_date'] = $today_date;
    if (!empty($prd_plan_type_id)) {
      $incr .= " AND pm.plan_type = :prd_plan_type_id";
      $sch_params[':prd_plan_type_id'] = $prd_plan_type_id;
    }
    $sqlAssigned = "SELECT pmc.*,pm.price,IF(pm.plan_type>0,'Primary',pm.enrollee_type) AS assign_type FROM prd_matrix_criteria pmc
      JOIN prd_matrix pm ON (pmc.prd_matrix_id = pm.id AND pm.is_deleted='N')
      WHERE pmc.is_deleted='N' AND pmc.product_id=:product_id AND (pm.pricing_effective_date <= :today_date AND (pm.pricing_termination_date >= :today_date OR pm.pricing_termination_date is null)) $incr order by pmc.benefit_amount desc";
    $resAssgined = $pdo->select($sqlAssigned, $sch_params);

    $responseArray = array();
    if (!empty($resAssgined)) {
      foreach ($resAssgined as $key => $value) {
        if (!array_key_exists($value['assign_type'], $responseArray)) {
          $responseArray[$value['assign_type']] = array();
        }
        foreach ($value as $key1 => $value1) {
          if (!isset($responseArray[$value['assign_type']][$key1])) {
            $responseArray[$value['assign_type']][$key1] = array();
          }
          array_push($responseArray[$value['assign_type']][$key1], $value1);
        }
      }
    }
    return $responseArray;
  }

  public function getProductCoverageOptions($product_id)
  {
    global $pdo, $prdPlanTypeArray;

    $sqlAssigned = "SELECT * FROM prd_coverage_options WHERE is_deleted='N' AND product_id=:product_id";
    $resAssgined = $pdo->select($sqlAssigned, array(":product_id" => $product_id));

    $responseArray = array();
    if (!empty($resAssgined)) {
      foreach ($resAssgined as $key => $value) {
        $responseArray[$value['prd_plan_type_id']]['plan_id'] = $value['prd_plan_type_id'];
        $responseArray[$value['prd_plan_type_id']]['plan_name'] = $prdPlanTypeArray[$value['prd_plan_type_id']]['title'];
      }
    }

    return $responseArray;
  }

  public function getProductDetails($product_price, $product_matrix, $product_plan, $group_coverage_contribution = array())
  {
    global $pdo, $prdPlanTypeArray;
    $product_array = array();
    $product_array['display_total'] = 0;
    $product_array['total'] = 0;
    $product_array['group_price_total'] = 0;
    $product_array['display_group_price_total'] = 0;
    if (!empty($product_matrix)) {
      foreach ($product_matrix as $product_id => $matrix_id) {
        if (!empty($matrix_id)) {
          $actual_product_price = $product_price[$product_id];
          $price = $product_price[$product_id];
          $display_member_price = $product_price[$product_id];
          $prd_plan_id = $product_plan[$product_id];

          $sqlProduct = "SELECT p.id as product_id,p.name as product_name,p.product_code,p.type,p.product_type,p.parent_product_id,p.payment_type_subscription,p.payment_type,p.company_id,p.product_type
            FROM prd_main p          
            WHERE p.id = :product_id AND p.status='Active' AND p.is_deleted='N'";
          $whereProduct = array(":product_id" => $product_id);
          $resProduct = $pdo->selectOne($sqlProduct, $whereProduct);
          $tmp_matrix_id = explode(",", $matrix_id);
          

          $tmp_member_price = 0;
          $tmp_display_member_price = 0;
          if (!empty($resProduct)) {
            $product_array[$product_id] = $resProduct;
            $product_array[$product_id]['plan_name'] = $prdPlanTypeArray[$prd_plan_id]['title'];
            $product_array[$product_id]['group_price'] = 0;
            $product_array[$product_id]['display_group_price'] = 0;
            $product_array[$product_id]['member_price'] = 0;
            $product_array[$product_id]['display_member_price'] = 0;
            if (isset($group_coverage_contribution) && $group_coverage_contribution) {
              if(!empty($tmp_matrix_id)){
                foreach ($tmp_matrix_id as $tmp_key => $tmp_value) {
                  if (isset($group_coverage_contribution[$product_id][$tmp_value]) || !empty($group_coverage_contribution['pay_period'])) {
                    $sqlMatrixPrice = "SELECT price FROM prd_matrix WHERE id=:id";
                    $resMatrixPrice = $pdo->selectOne($sqlMatrixPrice,array(":id"=>$tmp_value));

                    $tmp_group_coverage_contribution = !empty($group_coverage_contribution[$product_id][$tmp_value]) ? $group_coverage_contribution[$product_id][$tmp_value] : $group_coverage_contribution['pay_period'];

                    $calculatedPrice=$this->calculateGroupContributionPrice($resMatrixPrice['price'],$tmp_group_coverage_contribution,false);
                    
                    $product_array[$product_id]['group_price']=$product_array[$product_id]['group_price'] + $calculatedPrice['group_price'];
                    $product_array[$product_id]['display_group_price']=$product_array[$product_id]['display_group_price'] + $calculatedPrice['display_group_price'];

                    $product_array[$product_id]['member_price']=$product_array[$product_id]['member_price'] + $calculatedPrice['member_price'];
                    $product_array[$product_id]['display_member_price']=$product_array[$product_id]['display_member_price'] + $calculatedPrice['display_member_price'];
                    
                    $product_array[$product_id]['contribution_type']=$calculatedPrice['contribution_type'];
                    $product_array[$product_id]['contribution_value']=$calculatedPrice['contribution_value'];
                    
                  }else{
                    $product_array[$product_id]['display_member_price'] = $price;
                  }
                }
              }
            }else{
              $product_array[$product_id]['display_member_price'] = $price;
            }
            $product_array[$product_id]['price'] = $price;
            $product_array[$product_id]['matrix_id'] = $matrix_id;
            $product_array[$product_id]['plan_id'] = $prd_plan_id;
            if($price <= 0){
              $product_array[$product_id]['is_aca_product'] = 'Y';
            }
            $product_array['total'] = $product_array['total'] + $price;
            $product_array['display_total'] = $product_array['display_total'] + $product_array[$product_id]['display_member_price'];
            $product_array['group_price_total'] = $product_array['group_price_total'] + $product_array[$product_id]['group_price'];
            $product_array['display_group_price_total'] = $product_array['display_group_price_total'] + $product_array[$product_id]['display_group_price'];
          }
        }
      }
    }
    return $product_array;
  }
  public function getCombinationProducts($product_matrix, $sponsor_id = 0)
  {
    global $pdo;
    $product_array = array();
    $product_id_array = array();
    $product_id_list = 0;

    if (!empty($product_matrix) && is_array($product_matrix)) {
      foreach ($product_matrix as $product_id => $matrix_id) {
        if (!empty($matrix_id)) {
          array_push($product_id_array, $product_id);
        }
      }
    }

    if (!empty($product_id_array)) {
      $product_id_list = implode(",", $product_id_array);
    } else if (!is_array($product_matrix)) {
      $product_id_list = $product_matrix;
    }

    $sqlProduct = "SELECT pcr.product_id as main_product_id,pcr.combination_type,GROUP_CONCAT(p.id) AS product_id,
          GROUP_CONCAT(p.name SEPARATOR ', ')AS product_name
      FROM prd_combination_rule pcr
      JOIN prd_main p ON (p.id=pcr.combination_product_id AND p.is_deleted='N' AND p.status='Active')        
      WHERE pcr.product_id in ($product_id_list) AND pcr.is_deleted='N' GROUP BY pcr.product_id,pcr.combination_type";
    $resProduct = $pdo->select($sqlProduct);

    if (!empty($resProduct)) {
      foreach ($resProduct as $key => $value) {
        $product_id = $value['main_product_id'];
        $combination_type = $value['combination_type'];

        if (isset($product_array[$product_id][$combination_type]['product_id'])) {
          $comninationArr = explode(",", $product_array[$product_id][$combination_type]['product_id']);
          $comninationNameArr = explode(",", $product_array[$product_id][$combination_type]['product_name']);

          array_push($comninationArr, $value['product_id']);
          array_push($comninationNameArr, $value['product_name']);

          $combination_list = implode(",", $comninationArr);
          $combination_list_name = implode(",", $comninationNameArr);

          $product_array[$product_id][$combination_type]['product_id'] = $combination_list;
          $product_array[$product_id][$combination_type]['product_name'] = $combination_list_name;
        } else {
          $product_array[$product_id][$combination_type]['product_id'] = $value['product_id'];
          $product_array[$product_id][$combination_type]['product_name'] = $value['product_name'];
        }
      }
    }

    if (!empty($sponsor_id)) {
      $sqlProduct = "SELECT aac.product_id as main_product_id,aac.comb_type as combination_type,GROUP_CONCAT(p.id) AS product_id,
      GROUP_CONCAT(p.name SEPARATOR ', ')AS product_name
      FROM agent_assign_combination aac
      JOIN prd_main p ON (p.id=aac.combination_product_id AND p.is_deleted='N' AND p.status='Active')        
      WHERE aac.agent_id=:agent_id AND aac.product_id in ($product_id_list) AND aac.is_deleted='N' GROUP BY aac.product_id,aac.comb_type;";
      $whereProduct = array(":agent_id" => $sponsor_id);
      $resProduct = $pdo->select($sqlProduct, $whereProduct);



      if (!empty($resProduct)) {
        foreach ($resProduct as $key => $value) {
          $product_id = $value['main_product_id'];
          $combination_type = $value['combination_type'];

          if (isset($product_array[$product_id][$combination_type]['product_id'])) {
            $comninationArr = explode(",", $product_array[$product_id][$combination_type]['product_id']);
            $comninationNameArr = explode(",", $product_array[$product_id][$combination_type]['product_name']);

            array_push($comninationArr, $value['product_id']);
            array_push($comninationNameArr, $value['product_name']);

            $combination_list = implode(",", $comninationArr);
            $combination_list_name = implode(",", $comninationNameArr);

            $product_array[$product_id][$combination_type]['product_id'] = $combination_list;
            $product_array[$product_id][$combination_type]['product_name'] = $combination_list_name;
          } else {
            $product_array[$product_id][$combination_type]['product_id'] = $value['product_id'];
            $product_array[$product_id][$combination_type]['product_name'] = $value['product_name'];
          }
        }
      }
    }
    return $product_array;
  }
  public function getHealthyStepFee($product_matrix, $sponsor_id,$zip_code=0,$extra=array())
  {
    global $pdo;
    $product_array = array();
    $today_date = date('Y-m-d');

    $state_name = '';
    if (!empty($zip_code)) {
        $getStateCode = $pdo->selectOne("SELECT state_code from zip_code WHERE zip_code=:zip_code", array(":zip_code" => $zip_code));
        if ($getStateCode) {
          $state_name = getname("states_c", $getStateCode['state_code'], "name", "short_name");
        }
    }

    if($extra){
      if(!empty($extra['customer_id']) && $extra['is_add_product'] == 1){
        $existing_health_steps = $pdo->selectOne("SELECT od.id FROM order_details od JOIN orders o on(od.order_id = o.id) JOIN prd_main p on(p.id = od.product_id) JOIN website_subscriptions w on(w.id = od.website_id) WHERE o.customer_id = :customer_id AND o.status in('Payment Approved','Post Payment') AND p.product_type = 'Healthy Step' AND od.is_deleted='N' AND w.eligibility_date != w.termination_date",array(":customer_id" => $extra['customer_id']));
        if($existing_health_steps){
          return $product_array;
        }
      }
    }

    if (!empty($product_matrix)) {
      foreach ($product_matrix as $product_id => $matrix_id) {
        if (!empty($matrix_id)) {

          $sqlProduct = "SELECT paf.product_id as fee_product_id,apr.agent_id,p.id AS product_id,p.product_code,p.name AS product_name,pm.price,p.is_member_benefits,pmpi.description AS healthy_step_description,pm.plan_type as plan_id,pm.id as matrix_id,p.payment_type_subscription,p.type,p.payment_type,p.company_id,p.product_type,p.is_fee_on_renewal,p.fee_renewal_type,p.fee_renewal_count
          FROM prd_assign_fees paf
          JOIN agent_product_rule apr ON (apr.product_id = paf.fee_id AND apr.is_deleted='N' AND apr.status='Contracted')
          JOIN prd_main p ON (p.id=apr.product_id AND p.is_deleted='N' AND p.product_type='Healthy Step' AND p.parent_product_id != 0 AND p.status='Active')
          JOIN healthy_steps_states hss ON (hss.healthy_steps_fee_id = p.id AND hss.is_deleted='N')
          JOIN prd_matrix pm ON (pm.product_id = p.id AND pm.is_deleted='N' AND pm.pricing_effective_date <= :today_date AND (pm.pricing_termination_date >= :today_date OR pm.pricing_termination_date IS NULL))
          LEFT JOIN prd_member_portal_information pmpi ON (pmpi.product_id = p.id AND pmpi.is_deleted='N')
          WHERE paf.is_deleted='N' AND apr.agent_id=:sponsor_id AND paf.product_id = :product_id AND hss.state=:state_name GROUP BY p.id order by pm.price DESC";
          $whereProduct = array(":product_id" => $product_id, ":sponsor_id" => $sponsor_id, ":today_date" => $today_date,":state_name"=>$state_name);
          $resProduct = $pdo->select($sqlProduct, $whereProduct);

          if (empty($resProduct)) {
            $sqlProduct = "SELECT paf.product_id as fee_product_id,'' as agent_id,p.id AS product_id,p.product_code,p.name AS product_name,pm.price,p.is_member_benefits,pmpi.description AS healthy_step_description,pm.plan_type as plan_id,pm.id as matrix_id,p.payment_type_subscription,p.type,p.payment_type,p.company_id,p.product_type,p.is_fee_on_renewal,p.fee_renewal_type,p.fee_renewal_count
            FROM prd_assign_fees paf
            JOIN prd_main p ON (p.id=paf.fee_id AND p.is_deleted='N' AND p.product_type='Healthy Step' AND p.parent_product_id = 0 AND p.status='Active')
            JOIN healthy_steps_states hss ON (hss.healthy_steps_fee_id = p.id AND hss.is_deleted='N')
            JOIN prd_matrix pm ON (pm.product_id = p.id AND pm.is_deleted='N' AND pm.pricing_effective_date <= :today_date AND (pm.pricing_termination_date >= :today_date OR pm.pricing_termination_date IS NULL))
            LEFT JOIN prd_member_portal_information pmpi ON (pmpi.product_id = p.id AND pmpi.is_deleted='N')
            WHERE paf.is_deleted='N' AND paf.product_id = :product_id AND hss.state=:state_name GROUP BY p.id order by pm.price DESC";
            $whereProduct = array(":product_id" => $product_id, ":today_date" => $today_date,":state_name"=>$state_name);
            $resProduct = $pdo->select($sqlProduct, $whereProduct);
          }

          if (!empty($resProduct)) {
            foreach ($resProduct as $key => $value) {

              if (!empty($value['healthy_step_description'])) {
                $value['healthy_step_description'] = 'Y';
              }
              $product_array[] = $value;
            }
          }
          if(isset($extra['is_add_product']) && isset($extra['enrollmentLocation']) && $extra['enrollmentLocation'] == 'adminSide'){
            // Adding 0 Healthy steps from admin area

            $sqlProduct = "SELECT paf.product_id as fee_product_id,'' as agent_id,p.id AS product_id,p.product_code,p.name AS product_name,pm.price,p.is_member_benefits,pmpi.description AS healthy_step_description,pm.plan_type as plan_id,pm.id as matrix_id,p.payment_type_subscription,p.type,p.payment_type,p.company_id,p.product_type,p.is_fee_on_renewal,p.fee_renewal_type,p.fee_renewal_count
              FROM prd_assign_fees paf
              JOIN prd_main p ON (p.id=paf.fee_id AND p.is_deleted='N' AND p.product_type='Healthy Step' AND p.parent_product_id = 0 AND p.status='Active')
              JOIN healthy_steps_states hss ON (hss.healthy_steps_fee_id = p.id AND hss.is_deleted='N')
              JOIN prd_matrix pm ON (pm.product_id = p.id AND pm.is_deleted='N' AND pm.pricing_effective_date <= :today_date AND (pm.pricing_termination_date >= :today_date OR pm.pricing_termination_date IS NULL))
              LEFT JOIN prd_member_portal_information pmpi ON (pmpi.product_id = p.id AND pmpi.is_deleted='N')
              WHERE paf.is_deleted='N' AND paf.product_id = :product_id AND hss.state=:state_name AND (p.name = 'Health Steps 0' OR p.product_code = 'HS0_ALL') GROUP BY p.id order by pm.price DESC";
              $whereProduct = array(":product_id" => $product_id, ":today_date" => $today_date,":state_name"=>$state_name);
              $resZeroHeathyStep = $pdo->selectOne($sqlProduct, $whereProduct);

              if($resZeroHeathyStep){
                $already_added = false;
                foreach ($product_array as $key => $value) {
                  if($value['product_id'] == $resZeroHeathyStep['product_id']){
                    $already_added = true;
                  }
                }
                if(!$already_added){
                  if (!empty($resZeroHeathyStep['healthy_step_description'])) {
                    $resZeroHeathyStep['healthy_step_description'] = 'Y';
                  }
                  $product_array[] = $resZeroHeathyStep;
                }
              }
          }
        }
      }
    }
    return $product_array;
  }

  public function getLinkedFee($product_matrix, $sponsor_id, $is_new_order = 'Y', $is_renewal = 'N', $renewCount = 0, $original_order_date = '',$is_group_member='N',$extra = array())
  {
    global $pdo;
    $incr = "";
    $sch_params = array();
    $responseArray = array();
    $responseArray['total'] = 0;
    $responseArray['total_single'] = 0;
    $responseArray['total_annually'] = 0;

    if (!empty($original_order_date)) {
      $original_order_date = date('Y-m-d', strtotime($original_order_date));
    } else {
      $original_order_date = date('Y-m-d');
    }

    if($is_group_member =='N'){
      if ($is_new_order == 'Y') {
        $incr .= " AND p.initial_purchase = 'Y'";
      }
  
      if ($is_renewal == 'Y') {
        $incr .= " AND p.is_fee_on_renewal = 'Y' AND (p.fee_renewal_type = 'Continuous' OR p.fee_renewal_count >= :fee_renewal_count)";
        $sch_params[':fee_renewal_count'] = $renewCount;
      }
    }else if($is_group_member =='Y'){
      $incr .= " AND p.product_type='AdminFee'";
    }

    if (!empty($product_matrix)) {
      foreach ($product_matrix as $product_id => $matrix_id) {
        if (!empty($matrix_id)) {
         
          $feeAppliedForProduct = $product_id;
          $globalProduct = getGlobalFeeProductRule($product_id,$matrix_id);

          if(!empty($globalProduct)){
            $product_id = $globalProduct["product_id"];
            $matrix_id = $globalProduct["matrix_id"];
          }
         
          $sch_params[':product_id'] = $product_id;
          $sch_params[':today_date'] = $original_order_date;

          $check_prd_sql = "SELECT p.id,p.name,p.product_code,p.pricing_model FROM prd_main p WHERE p.is_deleted='N' AND p.status='Active' AND p.id = $product_id";
          $check_prd_res = $pdo->selectOne($check_prd_sql);

          if($check_prd_res['pricing_model'] != 'FixedPrice'){
          if($is_group_member =='N'){
            $queryjoin = "JOIN prd_matrix fm ON (pfpm.fee_product_id=fm.product_id AND fm.is_deleted='N' )
                          JOIN prd_matrix pm ON (pfpm.product_id=pm.product_id AND pm.matrix_group=fm.matrix_group AND pm.is_deleted='N')";
          }else{
            $queryjoin = "JOIN prd_matrix fm ON (pfpm.prd_matrix_fee_id=fm.id AND fm.is_deleted='N' )
                          JOIN prd_matrix pm ON (pfpm.prd_matrix_id=pm.id AND pm.is_deleted='N')";
          }
          $sqlFee = "SELECT p.id AS product_id,pfpm.product_id AS fee_product_id ,p.name AS product_name,p.product_code,p.fee_type,p.product_type,fm.price AS price,fm.price_calculated_on,fm.price_calculated_type,pm.price AS retail_price,pm.commission_amount AS commission_amount,pm.non_commission_amount AS non_commission_amount,fm.id AS matrix_id,p.parent_product_id,fm.plan_type AS plan_id,p.payment_type_subscription,p.type,p.payment_type,p.company_id,p.product_type,'0' AS group_price,p.is_benefit_tier,prd.pricing_model,prd.payment_type as prd_payment_type,prd.payment_type_subscription as prd_sub_type
            FROM prd_main p
            JOIN prd_fee_pricing_model pfpm ON (p.id = pfpm.fee_product_id AND pfpm.is_deleted='N')
            $queryjoin
            JOIN prd_main prd ON (prd.id=pm.product_id AND prd.is_deleted='N')
            WHERE p.type = 'Fees' AND p.is_deleted='N' AND p.status='Active' AND p.product_type NOT IN ('Healthy Step','Membership','ServiceFee')  
            AND pfpm.product_id =:product_id AND pm.id IN($matrix_id) AND p.is_benefit_tier='Y' AND (p.product_type='AdminFee' OR (fm.pricing_effective_date <= :today_date AND (fm.pricing_termination_date >= :today_date OR fm.pricing_termination_date IS NULL) $incr))GROUP BY fm.id";
          }else{
          $sqlFee = "SELECT p.id AS product_id,pfpm.product_id as fee_product_id ,p.name AS product_name,p.product_code,p.fee_type,p.product_type,fm.price as price,fm.price_calculated_on,fm.price_calculated_type,pm.price as retail_price,pm.commission_amount as commission_amount,pm.non_commission_amount as non_commission_amount,fm.id as matrix_id,p.parent_product_id,fm.plan_type as plan_id,p.payment_type_subscription,p.type,p.payment_type,p.company_id,p.product_type,'0' as group_price,p.is_benefit_tier,prd.pricing_model,prd.payment_type as prd_payment_type,prd.payment_type_subscription as prd_sub_type
            FROM prd_main p
            JOIN prd_fee_pricing_model pfpm ON (p.id = pfpm.fee_product_id AND pfpm.is_deleted='N')
            JOIN prd_matrix fm ON (fm.product_id=pfpm.fee_product_id AND fm.is_deleted='N')
            JOIN prd_matrix pm ON (pfpm.product_id=pm.product_id AND pm.is_deleted='N' AND pm.id IN ($matrix_id))
            JOIN prd_main prd ON (prd.id=pm.product_id AND prd.is_deleted='N')
            WHERE p.type = 'Fees' AND p.is_deleted='N' AND p.status='Active' AND p.product_type NOT IN ('Healthy Step','Membership','ServiceFee')  
            AND pfpm.product_id = :product_id AND fm.plan_type IN (SELECT plan_type FROM prd_matrix prd_mat WHERE prd_mat.id IN ($matrix_id)) AND p.is_benefit_tier='Y' AND (p.product_type='AdminFee' OR (fm.pricing_effective_date <= :today_date AND (fm.pricing_termination_date >= :today_date OR fm.pricing_termination_date IS NULL) $incr)) GROUP BY p.id";
          }
          $resFee = $pdo->select($sqlFee, $sch_params);
         // pre_print($resFee);
          if (!empty($resFee)) {
            foreach ($resFee as $key => $value) {
              if ($value['price_calculated_on'] == "Percentage") {
                $calclulatedPrice = 0;
                if ($value['price_calculated_type'] == "Retail") {
                  $calclulatedPrice = ($value['retail_price'] * $value['price']) / 100;
                } else if ($value['price_calculated_type'] == "Commissionable") {
                  $calclulatedPrice = ($value['commission_amount'] * $value['price']) / 100;
                } else if ($value['price_calculated_type'] == "NonCommissionable") {
                  $calclulatedPrice = ($value['non_commission_amount'] * $value['price']) / 100;
                }
                $value['price'] = $calclulatedPrice;
              }
              if ($value['fee_type'] == 'Charged') {
                if((isset($extra['enrollmentLocation']) && $extra['enrollmentLocation']=='groupSide') || (isset($extra['is_group_member']) && $extra['is_group_member'] == "Y")){ 
                  $responseArray['total'] = $responseArray['total'] + $value['price'];
                }else{
                  if($value['prd_payment_type'] == 'Single'){
                    $responseArray['total_single'] = $responseArray['total_single'] + $value['price'];
                  }else if($value['prd_payment_type'] == 'Recurring'){
                    if($value['prd_sub_type'] == "Monthly"){
                      $responseArray['total'] = $responseArray['total'] + $value['price'];
                    }else if($value['prd_sub_type'] == "Annually"){
                      $responseArray['total_annually'] = $responseArray['total_annually'] + $value['price'];
                    }
                  }
                }
                $value["fee_product_id"] = $feeAppliedForProduct;
                array_push($responseArray, $value);
              }
            }
          }

          $sqlFee = "SELECT p.id AS product_id,pfpm.product_id as fee_product_id ,p.name AS product_name,p.product_code,p.fee_type,p.product_type,fm.price as price,fm.price_calculated_on,fm.price_calculated_type,sum(pm.price) as retail_price,sum(pm.commission_amount) as commission_amount,sum(pm.non_commission_amount) as non_commission_amount,GROUP_CONCAT(DISTINCT fm.id) as matrix_id,p.parent_product_id,fm.plan_type as plan_id,p.payment_type_subscription,p.type,p.payment_type,p.company_id,p.product_type,'0' as group_price,p.is_benefit_tier,prd.pricing_model,prd.payment_type as prd_payment_type,prd.payment_type_subscription as prd_sub_type
            FROM prd_main p
            JOIN prd_assign_fees pfpm ON (p.id = pfpm.fee_id AND pfpm.is_deleted='N')
            JOIN prd_matrix fm ON (p.id=fm.product_id AND fm.is_deleted='N')
            JOIN prd_matrix pm ON (pfpm.product_id=pm.product_id AND pm.is_deleted='N')
            JOIN prd_main prd ON (prd.id=pm.product_id AND prd.is_deleted='N')
            WHERE p.type = 'Fees' AND p.is_deleted='N' AND p.status='Active' AND p.product_type NOT IN ('Healthy Step','Membership','ServiceFee')  
            AND pfpm.product_id = :product_id AND pm.id IN($matrix_id) AND p.is_benefit_tier='N' AND (p.product_type='AdminFee' OR (fm.pricing_effective_date <= :today_date AND (fm.pricing_termination_date >= :today_date OR fm.pricing_termination_date IS NULL) $incr)) GROUP BY p.id";
          $resFee = $pdo->select($sqlFee, $sch_params);
         
          if (!empty($resFee)) {
            foreach ($resFee as $key => $value) {
              if ($value['price_calculated_on'] == "Percentage") {
                $calclulatedPrice = 0;
                if ($value['price_calculated_type'] == "Retail") {
                  $calclulatedPrice = ($value['retail_price'] * $value['price']) / 100;
                } else if ($value['price_calculated_type'] == "Commissionable") {
                  $calclulatedPrice = ($value['commission_amount'] * $value['price']) / 100;
                } else if ($value['price_calculated_type'] == "NonCommissionable") {
                  $calclulatedPrice = ($value['non_commission_amount'] * $value['price']) / 100;
                }
                $value['price'] = $calclulatedPrice;
              }
              if ($value['fee_type'] == 'Charged') {
                if((isset($extra['enrollmentLocation']) && $extra['enrollmentLocation']=='groupSide') || (isset($extra['is_group_member']) && $extra['is_group_member'] == "Y")){ 
                  $responseArray['total'] = $responseArray['total'] + $value['price'];
                }else{
                  if($value['prd_payment_type'] == 'Single'){
                    $responseArray['total_single'] = $responseArray['total_single'] + $value['price'];
                  }else if($value['prd_payment_type'] == 'Recurring'){
                    if($value['prd_sub_type'] == "Monthly"){
                      $responseArray['total'] = $responseArray['total'] + $value['price'];
                    }else if($value['prd_sub_type'] == "Annually"){
                      $responseArray['total_annually'] = $responseArray['total_annually'] + $value['price'];
                    }
                  }
                }
                $value["fee_product_id"] = $feeAppliedForProduct;
                array_push($responseArray, $value);
              }
            }
          }
        }
      }
    }
    return $responseArray;
  }

  public function getMembershipFee($product_matrix, $customer_id = 0, $zip_code = 0, $is_new_order = 'Y', $is_renewal = 'N', $renewCount = 0, $original_order_date = '')
  {
    global $pdo;
    $type = '';
    $payment_type_subscription = '';
    $product_type = '';
    $payment_type = '';
    $company_id = 0;
    $plan_id = 0;
    $price = 0;
    $product_code = '';
    $feeproduct_name = '';
    $id = 0;
    $matrix_id = 0;

    $incr = "";
    $sch_params = array();
    $fee_product_id = 0;
    $membershipdFee = array();
    $membershipdFee['total'] = 0;
    $product_id = array();

    if (!empty($original_order_date)) {
      $original_order_date = date('Y-m-d', strtotime($original_order_date));
    } else {
      $original_order_date = date('Y-m-d');
    }
    $sch_params[':today_date'] = $original_order_date;
 
    if ($is_new_order == 'Y') {
      $incr .= " AND p.initial_purchase = 'Y'";
    }

    if ($is_renewal == 'Y') {
      $incr .= " AND p.is_fee_on_renewal = 'Y' AND (p.fee_renewal_type = 'Continuous' OR p.fee_renewal_count >= :fee_renewal_count)";
      $sch_params[':fee_renewal_count'] = $renewCount;
    }
    if (!empty($product_matrix) && is_array($product_matrix)) {
      foreach ($product_matrix as $key => $matrix_id) {
        if (!empty($matrix_id)) {
          array_push($product_id, $key);
        }
      }
    }

    if (empty($product_id)) {
      $product_id = $product_matrix;
    }

    if (!empty($product_id)) {

      if (is_array($product_id)) {
        $product_id_list = implode(",", $product_id);
      } else {
        $product_id_list = $product_id;
      }

      //********************** Fee Based on state code start *********************
      $extra_params = array();
      if (!empty($zip_code)) {
        $state_name = '';
        $getStateCode = $pdo->selectOne("SELECT state_code from zip_code WHERE zip_code=:zip_code", array(":zip_code" => $zip_code));
        if ($getStateCode) {
          $state_name = getname("states_c", $getStateCode['state_code'], "name", "short_name");
        }

        if (!empty($state_name)) {
          if (!empty($product_id)) {
            $assignByStateSql = "SELECT GROUP_CONCAT(association_fee_id) as fee_id FROM association_assign_by_state WHERE product_id in ($product_id_list) AND FIND_IN_SET(:state_name,states) AND is_deleted='N'";
            $assignByStateRes = $pdo->selectOne($assignByStateSql, array(":state_name" => $state_name));

            if ($assignByStateRes && !empty($assignByStateRes['fee_id'])) {
              $extra_params['assigned_fee_id'] = $assignByStateRes['fee_id'];
            }
          }
        }
      }

      if (!empty($extra_params)) {
        if (!empty($extra_params['assigned_fee_id'])) {
          $assignedFeeId = $extra_params['assigned_fee_id'];
          $incr .= " AND p.id in ($assignedFeeId)";
        }
      } else {
        $incr .= " AND p.is_assign_by_state ='N'";
      }
      //********************** Fee Based on state code end   *********************

      $sqlProduct = "SELECT pm.price,p.name,p.id as product_id,pm.id as matrixId,p.product_code,p.is_fee_on_renewal,
      p.fee_renewal_type,p.fee_renewal_count,gp.id as fee_product_id,p.product_type,p.type,p.parent_product_id,p.fee_type,pm.plan_type as plan_id,p.payment_type_subscription,p.payment_type,p.company_id,p.product_type
      FROM prd_main p
      JOIN prd_assign_fees paf ON (paf.fee_id = p.id AND paf.is_deleted='N')
      JOIN prd_main gp ON(gp.id=paf.product_id OR gp.parent_product_id=paf.product_id)
      JOIN prd_matrix pm ON (pm.product_id = p.id AND pm.is_deleted='N' AND pm.pricing_effective_date <= :today_date AND (pm.pricing_termination_date >= :today_date OR pm.pricing_termination_date IS NULL))
      WHERE gp.id in ($product_id_list) AND p.product_type='Membership' AND p.status='Active' AND p.is_deleted='N' $incr AND p.is_deleted='N'";
      $resProduct = $pdo->select($sqlProduct, $sch_params);
 
      if (count($resProduct) > 0) {
        foreach ($resProduct as $key => $product) {
          if ($product['price'] > $price && $product['fee_type'] == "Charged") {
            $payment_type_subscription = $product['payment_type_subscription'];
            $product_type = $product['product_type'];
            $payment_type = $product['payment_type'];
            $company_id = $product['company_id'];
            $fee_product_id = $product['fee_product_id'];
            $price = $product['price'];
            $product_name = $product['name'];
            $product_code = $product['product_code'];
            $id = $product['product_id'];
            $matrix_id = $product['matrixId'];
            $parent_product_id = $product['parent_product_id'];
            $plan_id = $product['plan_id'];
            $type = $product['type'];
          }
        }
      }
    }

    if (!empty($customer_id)) {
      $incr = '';
      $sch_params = array();

      $sch_params[':customer_id'] = $customer_id;
      $incr .= " AND customer_id = :customer_id";

      $today = date('Y-m-d');

      $fromdate = date('m/01/Y', strtotime($today));
      $todate = date('m/d/Y', strtotime($today));

      if ($fromdate != "") {
        $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($fromdate));
        $incr .= " AND DATE(o.created_at) >= :fcreated_at";
      }

      if ($todate != "") {
        $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($todate));
        $incr .= " AND DATE(o.created_at) <= :tcreated_at";
      }
      $sqlOrder = "SELECT pm.price,p.name,p.id,pm.id as matrixId,p.product_code,p.is_fee_on_renewal,
        p.fee_renewal_type,p.fee_renewal_count 
        FROM orders as o 
          JOIN order_details as od ON(od.order_id = o.id AND od.is_deleted='N')
          JOIN prd_main as p ON (od.product_id = p.id AND p.product_type='Membership') 
          JOIN prd_matrix as pm ON (pm.product_id = p.id) 
          WHERE o.status = 'Payment Approved' $incr";
      $resOrder = $pdo->select($sqlOrder, $sch_params);

      $is_membership_fee_applied = array();
      if (count($resOrder) > 0) {
        foreach ($resOrder as $key => $value) {
          array_push($is_membership_fee_applied, 'yes');
        }
      }
    }


    if (!empty($is_membership_fee_applied) && in_array('yes', $is_membership_fee_applied)) {
      $product_type = '';
      $payment_type_subscription = '';
      $payment_type = '';
      $company_id = 0;
      $fee_product_id = 0;
      $price = 0;
      $product_name = '';
      $product_code = '';
      $type = '';
      $id = 0;
      $matrix_id = 0;
      $parent_product_id = 0;
      $plan_id = 0;
    }

    if ($id > 0) {
      $membershipdFee[$fee_product_id] = array(
        'product_type' => $product_type,
        'payment_type_subscription' => $payment_type_subscription,
        'payment_type' => $payment_type,
        'company_id' => $company_id,
        'fee_product_id' => $fee_product_id,
        'product_id' => $id,
        'product_name' => $product_name,
        'product_code' => $product_code,
        'price' => $price,
        'group_price' => 0,
        'matrix_id' => $matrix_id,
        'parent_product_id' => $parent_product_id,
        'plan_id' => $plan_id,
        'type' => $type,
      );
      $membershipdFee['total'] = $membershipdFee['total'] + $price;
    }
    return $membershipdFee;
  }

  public function getServiceFee($product_matrix, $sponsor_id, $order_total = 0, $charged_to = 'Members', $is_new_order = 'Y', $is_renewal = 'N', $renewCount = 0, $original_order_date = '')
  {
    global $pdo;

    $incr = "";
    $sch_params = array();
    $responseArray = array();
    $responseArray['total'] = 0;
    $price = 0;
    $product_id_list = 0;
    if (!empty($original_order_date)) {
      $original_order_date = date('Y-m-d', strtotime($original_order_date));
    } else {
      $original_order_date = date('Y-m-d');
    }
    $sch_params[':today_date'] = $original_order_date;
    if ($is_new_order == 'Y') {
      $incr .= " AND p.initial_purchase = 'Y'";
    }

    if ($is_renewal == 'Y') {
      $incr .= " AND p.is_fee_on_renewal = 'Y' AND (p.fee_renewal_type = 'Continuous' OR p.fee_renewal_count >= :fee_renewal_count)";
      $sch_params[':fee_renewal_count'] = $renewCount;
    }

    $incr .= " AND pf.charged_to = :charged_to";
    $sch_params[':charged_to'] = $charged_to;

    $product_id_array = array();

    if (!empty($product_matrix) && is_array($product_matrix)) {
      foreach ($product_matrix as $product_id => $matrix_id) {
        if (!empty($matrix_id)) {
          array_push($product_id_array, $product_id);
        }
      }
    }

    if (!empty($product_id_array)) {
      $product_id_list = implode(",", $product_id_array);
    } else if (!is_array($product_matrix)) {
      $product_id_list = $product_matrix;
    }

    $sqlRule = "SELECT c.sponsor_id,cs.agent_coded_level,cs.advance_on,cs.graded_on FROM customer c JOIN customer_settings cs ON (c.id=cs.customer_id)
    where c.id=:id";
    $resRule = $pdo->selectOne($sqlRule, array(":id" => $sponsor_id));

    if (!empty($resRule)) {
      if ($resRule['agent_coded_level'] == 'LOA') {
        $this->getServiceFee($product_matrix, $resRule['sponsor_id'], $order_total, $charged_to, $is_new_order, $is_renewal, $renewCount, $original_order_date);
      }
      $advance_on = 'N';
      $graded_on = 'N';
      /*----- Check Advances ON ------*/
        $adv_his_row = $pdo->selectOne("SELECT h.id,h.is_on FROM advance_comm_rule_history h WHERE h.is_deleted='N' AND h.agent_id=:agent_id AND DATE(created_at) <= :original_order_date ORDER BY h.id DESC LIMIT 1",array(":agent_id" => $sponsor_id, ":original_order_date" => $original_order_date));

        if(!empty($adv_his_row['is_on'])) {
            $advance_on = $adv_his_row['is_on'];
        } else {
          if($resRule['advance_on'] == "Y") {
            $adv_his_row = $pdo->selectOne("SELECT h.id,h.is_on FROM advance_comm_rule_history h WHERE h.is_deleted='N' AND h.agent_id=:agent_id ORDER BY h.id DESC LIMIT 1",array(":agent_id" => $sponsor_id));
            if(empty($adv_his_row['is_on'])) {
                $advance_on = $resRule['advance_on'];
            }
          }
        }
      /*-----/Check Advances ON ------*/

      /*----- Check Graded ON ------*/
        $graded_his_row = $pdo->selectOne("SELECT h.id,h.is_on FROM graded_comm_rule_history h WHERE h.is_deleted='N' AND h.agent_id=:agent_id AND DATE(created_at) <= :original_order_date ORDER BY h.id DESC LIMIT 1",array(":agent_id" => $sponsor_id, ":original_order_date" => $original_order_date));
        if(!empty($graded_his_row['is_on'])) {
            $graded_on = $graded_his_row['is_on'];
        } else {
          if($resRule['graded_on'] == "Y") {
            $graded_his_row = $pdo->selectOne("SELECT h.id,h.is_on FROM graded_comm_rule_history h WHERE h.is_deleted='N' AND h.agent_id=:agent_id ORDER BY h.id DESC LIMIT 1",array(":agent_id" => $sponsor_id));
            if(empty($graded_his_row['is_on'])) {
                $graded_on = $resRule['graded_on'];
            }
          }
        }
      /*-----/Check Graded ON ------*/

      if ($advance_on == 'Y') {
        $tmpSchParams = $sch_params;
        $tmpSchParams[':agent_id'] = $sponsor_id;
        $sqlProduct = "SELECT paf.product_id AS fee_product_id,apr.agent_id,p.id AS product_id,p.product_code,p.name AS product_name,pm.price,p.is_member_benefits,pm.plan_type AS plan_id,pm.id AS matrix_id,p.payment_type_subscription,p.type,p.payment_type,p.company_id,p.product_type,pmc.min_total,pmc.max_total,pm.pricing_model
          FROM prd_fees pf
          JOIN prd_assign_fees paf ON(paf.prd_fee_id=pf.id AND paf.is_deleted='N')
          JOIN agent_product_rule apr ON (apr.product_id = paf.fee_id AND apr.is_deleted='N' AND apr.status='Contracted')
          JOIN prd_main p ON (p.id=apr.product_id AND p.is_deleted='N' AND p.product_type='ServiceFee' AND p.status='Active')
          JOIN prd_matrix pm ON (pm.product_id = p.id AND pm.is_deleted='N' AND pm.pricing_effective_date <= :today_date AND (pm.pricing_termination_date >= :today_date OR pm.pricing_termination_date IS NULL))
          LEFT JOIN prd_matrix_criteria pmc ON (pmc.prd_matrix_id = pm.id AND pmc.product_id = p.id)
          WHERE paf.is_deleted='N' AND apr.agent_id=:agent_id AND paf.product_id in ($product_id_list) $incr AND pf.rule_type='Variation' AND pf.setting_type='ServiceFee' AND pf.is_deleted='N' AND pf.status='Active'";
        $resProduct = $pdo->select($sqlProduct, $tmpSchParams);
        if (empty($resProduct)) {
          $sqlProduct = "SELECT gp.id AS fee_product_id,'' AS agent_id,p.id AS product_id,p.product_code,p.name AS product_name,pm.price,p.is_member_benefits,pm.plan_type AS plan_id,pm.id AS matrix_id,p.payment_type_subscription,p.type,p.payment_type,p.company_id,p.product_type,pmc.min_total,pmc.max_total,pm.pricing_model
            FROM prd_fees pf
              JOIN prd_assign_fees paf ON(paf.prd_fee_id=pf.id AND paf.is_deleted='N')
              JOIN prd_main gp ON(gp.id=paf.product_id OR gp.parent_product_id=paf.product_id)
              JOIN prd_main p ON (p.id=paf.fee_id AND p.is_deleted='N' AND p.product_type='ServiceFee' AND p.status='Active')
              JOIN prd_matrix pm ON (pm.product_id = p.id AND pm.is_deleted='N' AND pm.pricing_effective_date <= :today_date AND (pm.pricing_termination_date >= :today_date OR pm.pricing_termination_date IS NULL))
              LEFT JOIN prd_matrix_criteria pmc ON (pmc.prd_matrix_id = pm.id AND pmc.product_id = p.id)
            WHERE paf.is_deleted='N' AND gp.id IN ($product_id_list) AND gp.is_deleted='N' $incr AND pf.rule_type='Global' 
            AND pf.setting_type='ServiceFee' AND pf.is_deleted='N' AND pf.status='Active'";
          $resProduct = $pdo->select($sqlProduct, $sch_params);
        }
        if (!empty($resProduct)) {
          foreach ($resProduct as $key => $value) {
            if ($value['price'] > $price) {
              if ($value['pricing_model'] == 'FixedPrice') {
                $price = $value['price'];
                $responseArray[0] = $value;
                $responseArray['total'] = $value['price'];
              } else {
                if ($order_total >= $value['min_total'] && $order_total <= $value['max_total']) {
                  $price = $value['price'];
                  $responseArray[0] = $value;
                  $responseArray['total'] = $value['price'];
                }
              }
            }
          }
        }
      }else if($graded_on=='Y'){
        $tmpSchParams = $sch_params;
        $tmpSchParams[':agent_id']=$sponsor_id;
        $sqlProduct="SELECT paf.product_id AS fee_product_id,apr.agent_id,p.id AS product_id,p.product_code,p.name AS product_name,pm.price,p.is_member_benefits,pm.plan_type AS plan_id,pm.id AS matrix_id,p.payment_type_subscription,p.type,p.payment_type,p.company_id,p.product_type,pmc.min_total,pmc.max_total,pm.pricing_model
          FROM prd_fees pf
          JOIN prd_assign_fees paf ON(paf.prd_fee_id=pf.id AND paf.is_deleted='N')
          JOIN agent_product_rule apr ON (apr.product_id = paf.fee_id AND apr.is_deleted='N' AND apr.status='Contracted')
          JOIN prd_main p ON (p.id=apr.product_id AND p.is_deleted='N' AND p.product_type='ServiceFee' AND p.status='Active')
          JOIN prd_matrix pm ON (pm.product_id = p.id AND pm.is_deleted='N' AND pm.pricing_effective_date <= :today_date AND (pm.pricing_termination_date >= :today_date OR pm.pricing_termination_date IS NULL))
          LEFT JOIN prd_matrix_criteria pmc ON (pmc.prd_matrix_id = pm.id AND pmc.product_id = p.id)
          WHERE paf.is_deleted='N' AND apr.agent_id=:agent_id AND paf.product_id in ($product_id_list) $incr AND pf.rule_type='Variation' AND pf.setting_type='GradedFee' AND pf.is_deleted='N' AND pf.status='Active'";
        $resProduct=$pdo->select($sqlProduct,$tmpSchParams);
        if(empty($resProduct)){
          $sqlProduct = "SELECT paf.product_id AS fee_product_id,'' AS agent_id,p.id AS product_id,p.product_code,p.name AS product_name,pm.price,p.is_member_benefits,pm.plan_type AS plan_id,pm.id AS matrix_id,p.payment_type_subscription,p.type,p.payment_type,p.company_id,p.product_type,pmc.min_total,pmc.max_total,pm.pricing_model
            FROM prd_fees pf
              JOIN prd_assign_fees paf ON(paf.prd_fee_id=pf.id AND paf.is_deleted='N')
              JOIN prd_main gp ON(gp.id=paf.product_id OR gp.parent_product_id=paf.product_id)
              JOIN prd_main p ON (p.id=paf.fee_id AND p.is_deleted='N' AND p.product_type='ServiceFee' AND p.status='Active')
              JOIN prd_matrix pm ON (pm.product_id = p.id AND pm.is_deleted='N' AND pm.pricing_effective_date <= :today_date AND (pm.pricing_termination_date >= :today_date OR pm.pricing_termination_date IS NULL))
              LEFT JOIN prd_matrix_criteria pmc ON (pmc.prd_matrix_id = pm.id AND pmc.product_id = p.id)
            WHERE paf.is_deleted='N' AND gp.id IN ($product_id_list) AND gp.is_deleted='N' $incr 
            AND pf.rule_type='Global' AND pf.setting_type='GradedFee' AND pf.is_deleted='N' AND pf.status='Active'";
          $resProduct=$pdo->select($sqlProduct,$sch_params);
        }
        if(!empty($resProduct)){
          foreach ($resProduct as $key => $value) {
             if($value['price'] > $price){
                if($value['pricing_model']=='FixedPrice'){
                  $price = $value['price'];
                  $responseArray[0]=$value;
                  $responseArray['total']=$value['price'];
                }else{
                  if($order_total >= $value['min_total'] && $order_total<= $value['max_total']){
                    $price = $value['price'];
                    $responseArray[0]=$value;
                    $responseArray['total']=$value['price'];
                  }
                }
             }
          }
        }
      }
    }
    return $responseArray;
  }

  public function get_primary_member_field($product_id_list = array())
  {
    global $pdo;
    $incr = "";
    if (count($product_id_list) > 0) {
      $product_id = implode(",", $product_id_list);
      $incr .= " AND peqa.product_id in (" . $product_id . ")";
    }
    $sqlProducts = "SELECT peq.*,peqa.product_id,peqa.is_member_asked AS asked,peqa.is_member_required AS required 
    FROM prd_enrollment_questions peq
    JOIN prd_enrollment_questions_assigned peqa ON (peq.id = peqa.prd_question_id AND peqa.is_deleted='N' AND peqa.is_member_asked='Y') 
    WHERE peq.is_deleted='N'  $incr ORDER BY  FIELD(questionType,'Default','Custom') ASC, order_by ASC";
    $resProducts = $pdo->select($sqlProducts);

    $details = array();

    if (count($resProducts) > 0) {
      foreach ($resProducts as $key => $row) {
        if (!isset($details[$row['label']]) || $row['required'] == 'Y') {
          $product_ids = array();

          if (isset($details[$row['label']]['product_ids'])) {
            $product_ids  = explode(",", $details[$row['label']]['product_ids']);
          }
          if (!in_array($row['product_id'], $product_ids)) {
            array_push($product_ids, $row['product_id']);
          }
          $product_id_list = !empty($product_ids) ? implode(",", $product_ids) : '';
          $details[$row['label']] = $row;
          $details[$row['label']]['product_ids'] = $product_id_list;
        }
      }
    }
    return $details;
  }

  public function get_primary_member_field_all($product_id_list = array())
  {
    global $pdo;
    $incr = "";
    if (count($product_id_list) > 0) {
      $product_id = implode(",", $product_id_list);
      $incr .= " AND peqa.product_id in (" . $product_id . ")";
    }
    $sqlProducts = "SELECT peq.*,peqa.product_id,peqa.is_member_asked AS asked,peqa.is_member_required AS required 
    FROM prd_enrollment_questions peq
    LEFT JOIN prd_enrollment_questions_assigned peqa ON (peq.id = peqa.prd_question_id AND peqa.is_deleted='N' AND peqa.is_member_asked='Y' $incr) 
    WHERE peq.is_deleted='N' ORDER BY  FIELD(questionType,'Default','Custom') ASC, order_by ASC";
    $resProducts = $pdo->select($sqlProducts);

    $details = array();

    if (count($resProducts) > 0) {
      foreach ($resProducts as $key => $row) {
        if (!isset($details[$row['label']]) || $row['required'] == 'Y') {
          $product_ids = array();

          if (isset($details[$row['label']]['product_ids'])) {
            $product_ids  = explode(",", $details[$row['label']]['product_ids']);
          }
          if (!in_array($row['product_id'], $product_ids)) {
            array_push($product_ids, $row['product_id']);
          }
          $product_id_list = !empty($product_ids) ? implode(",", $product_ids) : '';
          $details[$row['label']] = $row;
          $details[$row['label']]['product_ids'] = $product_id_list;
        }
      }
    }
    return $details;
  }

  public function get_spouse_field($product_id_list = array())
  {
    global $pdo;
    $incr = "";
    if (count($product_id_list) > 0) {
      $product_id = implode(",", $product_id_list);
      $incr .= " AND peqa.product_id in (" . $product_id . ")";
    }
    $sqlProducts = "SELECT peq.*,peqa.product_id,peqa.is_spouse_asked AS asked,peqa.is_spouse_required AS required 
    FROM prd_enrollment_questions peq
    JOIN prd_enrollment_questions_assigned peqa ON (peq.id = peqa.prd_question_id AND peqa.is_deleted='N' AND peqa.is_spouse_asked='Y') WHERE peq.is_deleted='N'  $incr ORDER BY  FIELD(questionType,'Default','Custom') ASC, order_by ASC";
    $resProducts = $pdo->select($sqlProducts);

    $details = array();

    if (count($resProducts) > 0) {
      foreach ($resProducts as $key => $row) {
        if (!isset($details[$row['label']]) || $row['required'] == 'Y') {
          $product_ids = array();

          if (isset($details[$row['label']]['product_ids'])) {
            $product_ids  = explode(",", $details[$row['label']]['product_ids']);
          }
          if (!in_array($row['product_id'], $product_ids)) {
            array_push($product_ids, $row['product_id']);
          }
          $product_id_list = !empty($product_ids) ? implode(",", $product_ids) : '';
          $details[$row['label']] = $row;
          $details[$row['label']]['product_ids'] = $product_id_list;
        }
      }
    }
    return $details;
  }
  
  public function get_spouse_field_all($product_id_list = array())
  {
    global $pdo;
    $incr = "";
    if (count($product_id_list) > 0) {
      $product_id = implode(",", $product_id_list);
      $incr .= " AND peqa.product_id in (" . $product_id . ")";
    }
    $sqlProducts = "SELECT peq.*,peqa.product_id,peqa.is_spouse_asked AS asked,peqa.is_spouse_required AS required 
    FROM prd_enrollment_questions peq
    LEFT JOIN prd_enrollment_questions_assigned peqa ON (peq.id = peqa.prd_question_id AND peqa.is_deleted='N' AND peqa.is_spouse_asked='Y' $incr) WHERE peq.is_spouse='Y' AND peq.is_deleted='N'  ORDER BY  FIELD(questionType,'Default','Custom') ASC, order_by ASC";
    $resProducts = $pdo->select($sqlProducts);

    $details = array();

    if (count($resProducts) > 0) {
      foreach ($resProducts as $key => $row) {
        if (!isset($details[$row['label']]) || $row['required'] == 'Y') {
          $product_ids = array();

          if (isset($details[$row['label']]['product_ids'])) {
            $product_ids  = explode(",", $details[$row['label']]['product_ids']);
          }
          if (!in_array($row['product_id'], $product_ids)) {
            array_push($product_ids, $row['product_id']);
          }
          $product_id_list = !empty($product_ids) ? implode(",", $product_ids) : '';
          $details[$row['label']] = $row;
          $details[$row['label']]['product_ids'] = $product_id_list;
        }
      }
    }
    return $details;
  }

  public function get_child_field($product_id_list = array())
  {
    global $pdo;
    $incr = "";
    if (count($product_id_list) > 0) {
      $product_id = implode(",", $product_id_list);
      $incr .= " AND peqa.product_id in (" . $product_id . ")";
    }
    $sqlProducts = "SELECT peq.*,peqa.product_id,peqa.is_child_asked AS asked,peqa.is_child_required AS required 
    FROM prd_enrollment_questions peq
    JOIN prd_enrollment_questions_assigned peqa ON (peq.id = peqa.prd_question_id AND peqa.is_deleted='N' AND peqa.is_child_asked='Y') WHERE peq.is_deleted='N'  $incr ORDER BY  FIELD(questionType,'Default','Custom') ASC, order_by ASC";
    $resProducts = $pdo->select($sqlProducts);

    $details = array();

    if (count($resProducts) > 0) {
      foreach ($resProducts as $key => $row) {
        if (!isset($details[$row['label']]) || $row['required'] == 'Y') {
          $product_ids = array();

          if (isset($details[$row['label']]['product_ids'])) {
            $product_ids  = explode(",", $details[$row['label']]['product_ids']);
          }
          if (!in_array($row['product_id'], $product_ids)) {
            array_push($product_ids, $row['product_id']);
          }
          $product_id_list = !empty($product_ids) ? implode(",", $product_ids) : '';
          $details[$row['label']] = $row;
          $details[$row['label']]['product_ids'] = $product_id_list;
        }
      }
    }
    return $details;
  }

  public function get_child_field_all($product_id_list = array())
  {
    global $pdo;
    $incr = "";
    if (count($product_id_list) > 0) {
      $product_id = implode(",", $product_id_list);
      $incr .= " AND peqa.product_id in (" . $product_id . ")";
    }
    $sqlProducts = "SELECT peq.*,peqa.product_id,peqa.is_child_asked AS asked,peqa.is_child_required AS required 
    FROM prd_enrollment_questions peq
    LEFT JOIN prd_enrollment_questions_assigned peqa ON (peq.id = peqa.prd_question_id AND peqa.is_deleted='N' AND peqa.is_child_asked='Y' $incr) WHERE peq.is_child='Y' AND peq.is_deleted='N' ORDER BY  FIELD(questionType,'Default','Custom') ASC, order_by ASC";
    $resProducts = $pdo->select($sqlProducts);

    $details = array();

    if (count($resProducts) > 0) {
      foreach ($resProducts as $key => $row) {
        if (!isset($details[$row['label']]) || $row['required'] == 'Y') {
          $product_ids = array();

          if (isset($details[$row['label']]['product_ids'])) {
            $product_ids  = explode(",", $details[$row['label']]['product_ids']);
          }
          if (!in_array($row['product_id'], $product_ids)) {
            array_push($product_ids, $row['product_id']);
          }
          $product_id_list = !empty($product_ids) ? implode(",", $product_ids) : '';
          $details[$row['label']] = $row;
          $details[$row['label']]['product_ids'] = $product_id_list;
        }
      }
    }
    return $details;
  }

  public function get_principal_beneficiary_field($product_id_list = array())
  {
    global $pdo;
    $incr = "";
    if (count($product_id_list) > 0) {
      $product_id = implode(",", $product_id_list);
      $incr .= " AND pbqa.product_id in (" . $product_id . ")";
    }
    $sqlProducts = "SELECT pbq.*,pbqa.is_principal_beneficiary_asked AS asked,pbqa.is_principal_beneficiary_required AS required,pbqa.product_id 
    FROM prd_beneficiary_questions pbq
    JOIN prd_beneficiary_questions_assigned pbqa ON (pbq.id = pbqa.prd_beneficiary_question_id AND pbqa.is_deleted='N' AND pbqa.is_principal_beneficiary_asked='Y') WHERE pbq.is_deleted='N' $incr order by pbq.order_by ASC";
    $resProducts = $pdo->select($sqlProducts);

    $response = array();
    $details = array();
    $beneficiaryProducts = array();

    if (count($resProducts) > 0) {
      foreach ($resProducts as $key => $row) {
        if (!isset($details[$row['label']]) || $row['required'] == 'Y') {
          $details[$row['label']] = $row;
          if(!in_array($row['product_id'], $beneficiaryProducts)){
            array_push($beneficiaryProducts, $row['product_id']);
          }
        }
      }
    }
    if(!empty($details) && !empty($beneficiaryProducts)){
      $response['products']['product_ids']= $beneficiaryProducts;
      $response['products']['control_type']= 'select_multiple';
      $response['products']['label']= 'product';
      $response['products']['display_label']= 'Product';
      $response['products']['required']= 'Y';
      $response['products']['id']= 0;
      $response['products']['control_class']= '';
      $response['products']['control_maxlength']= '';
      $response['products']['control_attribute']= '';
      $response['products']['questionType']= 'Default';
    }
    $response = array_merge($response,$details);
    return $response;
  }

  public function get_principal_beneficiary_field_all($product_id_list = array())
  {
    global $pdo;
    $incr = "";
    if (count($product_id_list) > 0) {
      $product_id = implode(",", $product_id_list);
      $incr .= " AND pbqa.product_id in (" . $product_id . ")";
    }
    $sqlProducts = "SELECT pbq.*,pbqa.is_principal_beneficiary_asked AS asked,pbqa.is_principal_beneficiary_required AS required ,pbqa.product_id
    FROM prd_beneficiary_questions pbq
    LEFT JOIN prd_beneficiary_questions_assigned pbqa ON (pbq.id = pbqa.prd_beneficiary_question_id AND pbqa.is_deleted='N' AND pbqa.is_principal_beneficiary_asked='Y') WHERE pbq.is_deleted='N' $incr order by pbq.order_by ASC";
    $resProducts = $pdo->select($sqlProducts);

    $details = array();

    if (count($resProducts) > 0) {
      foreach ($resProducts as $key => $row) {
        if (!isset($details[$row['label']]) || $row['required'] == 'Y') {
          $details[$row['label']] = $row;
        }
      }
    }
    return $details;
  }
  public function get_contingent_beneficiary_field($product_id_list = array())
  {
    global $pdo;
    $incr = "";
    if (count($product_id_list) > 0) {
      $product_id = implode(",", $product_id_list);
      $incr .= " AND pbqa.product_id in (" . $product_id . ")";
    }
    $sqlProducts = "SELECT pbq.*,pbqa.is_contingent_beneficiary_asked AS asked,pbqa.is_contingent_beneficiary_required AS required,pbqa.product_id
    FROM prd_beneficiary_questions pbq
    JOIN prd_beneficiary_questions_assigned pbqa ON (pbq.id = pbqa.prd_beneficiary_question_id AND pbqa.is_deleted='N' AND pbqa.is_contingent_beneficiary_asked='Y') WHERE pbq.is_deleted='N' $incr order by pbq.order_by ASC";
    $resProducts = $pdo->select($sqlProducts);

    $response = array();
    $details = array();
    $beneficiaryProducts = array();
    if (count($resProducts) > 0) {
      foreach ($resProducts as $key => $row) {
        if (!isset($details[$row['label']]) || $row['required'] == 'Y') {
          $details[$row['label']] = $row;
          if(!in_array($row['product_id'], $beneficiaryProducts)){
            array_push($beneficiaryProducts, $row['product_id']);
          }
        }
      }
    }

    if(!empty($details) && !empty($beneficiaryProducts)){
      $response['products']['product_ids']= $beneficiaryProducts;
      $response['products']['control_type']= 'select_multiple';
      $response['products']['label']= 'product';
      $response['products']['display_label']= 'Product';
      $response['products']['required']= 'Y';
      $response['products']['id']= 0;
      $response['products']['control_class']= '';
      $response['products']['control_maxlength']= '';
      $response['products']['control_attribute']= '';
      $response['products']['questionType']= 'Default';
    }
    $response = array_merge($response,$details);
    return $response;
  }
  public function get_contingent_beneficiary_field_all($product_id_list = array())
  {
    global $pdo;
    $incr = "";
    if (count($product_id_list) > 0) {
      $product_id = implode(",", $product_id_list);
      $incr .= " AND pbqa.product_id in (" . $product_id . ")";
    }
    $sqlProducts = "SELECT pbq.*,pbqa.is_contingent_beneficiary_asked AS asked,pbqa.is_contingent_beneficiary_required AS required,pbqa.product_id
    FROM prd_beneficiary_questions pbq
    LEFT JOIN prd_beneficiary_questions_assigned pbqa ON (pbq.id = pbqa.prd_beneficiary_question_id AND pbqa.is_deleted='N' AND pbqa.is_contingent_beneficiary_asked='Y') WHERE pbq.is_deleted='N' $incr order by pbq.order_by ASC";
    $resProducts = $pdo->select($sqlProducts);

    $details = array();

    if (count($resProducts) > 0) {
      foreach ($resProducts as $key => $row) {
        if (!isset($details[$row['label']]) || $row['required'] == 'Y') {
          $details[$row['label']] = $row;
        }
      }
    }
    return $details;
  }

  public function get_enrollment_verification_option($product_id_list = array())
  {
    global $pdo;
    $incr = "";
    if (count($product_id_list) > 0) {
      $product_id = implode(",", $product_id_list);
      $incr .= " AND p.product_id in (" . $product_id . ")";
    }
    $sqlProducts = "SELECT p.product_id,p.verification_type FROM prd_enrollment_verification p WHERE p.is_deleted='N' $incr";
    $resProducts = $pdo->select($sqlProducts);

    $verification_option = array();

    if (!empty($resProducts)) {
      foreach ($resProducts as $key => $row) {
        $option = !empty($row['verification_type']) ? $row['verification_type'] : array();

        if (!in_array($option, $verification_option)) {
          array_push($verification_option, $option);
        }
      }
    }
    return $verification_option;
  }
  public function getPurchasedEffectiveDate($product_id_list = array(), $customer_id = 0){
    global $pdo,$ALLOWED_SUBSCRIPTION_STATUS,$MEMBER_STATUS;
    $allowedSubscriptionStatus = implode("','", $ALLOWED_SUBSCRIPTION_STATUS);
    $allowedMemberStatus = implode("','", $MEMBER_STATUS);
    $earliest_effective_arr = array();
    $core_earliest_effective = date('Y-m-d');
    if(!empty($product_id_list) && !empty($customer_id)){
      $sqlCoreWebsite="SELECT p.id,p.main_product_type,w.termination_date,w.eligibility_date,p.reenroll_options,p.reenroll_within,p.reenroll_within_type  
            FROM website_subscriptions w 
            JOIN customer c ON (c.id = w.customer_id)
            JOIN prd_main p ON (p.id = w.product_id)
            where w.customer_id = :customer_id AND 
            w.status IN('".$allowedSubscriptionStatus."') AND 
            c.status IN('".$allowedMemberStatus."')
            ";
      $resCoreWebsite=$pdo->select($sqlCoreWebsite,array(":customer_id"=>$customer_id));

      if(!empty($resCoreWebsite)){
        foreach ($resCoreWebsite as $coreValue) {
          $earliest_effective = date('Y-m-d');
          if(!empty($coreValue['termination_date']) && ($coreValue['eligibility_date'] != $coreValue['termination_date']) && (strtotime(date('Y-m-d')) < strtotime($coreValue['termination_date']))){
            if($coreValue['main_product_type'] == 'Core Product'){
              $core_earliest_effective = date('Y-m-d',strtotime($coreValue['termination_date']));
            }
            if($coreValue['reenroll_options'] == 'Available After Specific Time Frame'){
              if($coreValue['reenroll_within_type'] == 'Years'){
                $abr = 'year';
              } else if($coreValue['reenroll_within_type'] == 'Months'){
                $abr = 'month';
              } else if($coreValue['reenroll_within_type'] == 'Weeks'){
                $abr = 'week';
              } else {
                $abr = 'day';
              }
              $earliest_effective = date('Y-m-d',strtotime($coreValue['termination_date'].' + '.$coreValue['reenroll_within'].' '.$abr));
            } else {
              $earliest_effective = date('Y-m-d',strtotime($coreValue['termination_date']));
            }

            $earliest_effective_arr[$coreValue['id']] = $earliest_effective;
          } else {
            $earliest_effective_arr[$coreValue['id']] = $earliest_effective;
          }
        }
      } 

      $product_id = implode(",", $product_id_list);

      $product_sql = "SELECT p.id,p.main_product_type,p.reenroll_options,p.reenroll_within,p.reenroll_within_type FROM prd_main p WHERE p.id in (" . $product_id . ")";
      $product_res = $pdo->select($product_sql);
      
      if(!empty($product_res)){
        foreach ($product_res as $key => $prdRow) {
          if(!array_key_exists($prdRow['id'], $earliest_effective_arr)){
            if($prdRow['main_product_type'] == 'Core Product'){
              $earliest_effective_arr[$prdRow['id']] = $core_earliest_effective;
            } else { 
              $earliest_effective_arr[$prdRow['id']] = date('Y-m-d');
            }
          }
        }
      }
    }

    return $earliest_effective_arr;
  }
 
  public function get_coverage_period($product_id_list = array(), $sponsor_id = 0,$extra=array())
  {
    global $pdo;
    $incr = "";
    $group_coverage_date = '';
    $coverage_date = '';
    $coverage_end_date = '';
    $allow_future_effective_date = 'Y';
    $allowed_range = '+32 days';
    $allowEffectiveRange = 32;
    $is_allow_31 = 'Y';
    if (count($product_id_list) > 0) {
      $product_id = implode(",", $product_id_list);
      $incr .= " AND p.id in (" . $product_id . ")";
    }
    $core_earliest_effective = date('Y-m-d');
    if(!empty($extra['customer_id']) && !empty($extra['is_add_product']) && $extra['is_add_product'] == 1) {
        $purchasedProductData = $this->getPurchasedEffectiveDate($product_id_list,$extra['customer_id']);
    }

    $endCoverageRes = $pdo->selectOne("SELECT id,is_open_enrollment,end_coverage_date FROM end_coverage_periods_settings WHERE is_deleted='N'");

    $sqlProducts = "SELECT p.id,p.product_code,p.name,p.direct_product,p.effective_day,p.effective_day2,p.sold_day,pf.name as carrier_name,p.main_product_type,p.reenroll_options,p.reenroll_within,p.reenroll_within_type,IF(p.payment_type='Recurring',p.payment_type_subscription,'One Time') as member_payment_type
      FROM prd_main p
      JOIN prd_fees pf ON (pf.id = p.carrier_id)
      WHERE p.is_deleted='N' AND p.status in('Active','Suspended') $incr";
    $resProducts = $pdo->select($sqlProducts);

    $coverage_period_array = array();
    if(!empty($extra)){
      if(isset($extra['enrollmentLocation']) && $extra['enrollmentLocation']=='groupSide' || (isset($extra['is_group_member']) && $extra['is_group_member'] == "Y")){
        $enrolle_class = $extra['enrolle_class'];
        $coverage_period = $extra['coverage_period'];
        $sql="SELECT group_coverage_period_id,class_id,first_coverage_date,waiting_restriction_on_open_enrollment,allow_future_effective_date,allowed_range
        FROM group_coverage_period_offering WHERE group_coverage_period_id=:coverage_period AND class_id=:class_id";
        $res=$pdo->selectOne($sql,array(":class_id"=>$enrolle_class,":coverage_period"=>$coverage_period));

        if(!empty($res)){
          $group_coverage_date = date('Y-m-d', strtotime($res['first_coverage_date']));
          if($res['waiting_restriction_on_open_enrollment']=='Y'){
            $sqlClass="SELECT * FROM group_classes where id=:id";
            $resClass=$pdo->selectOne($sqlClass,array(":id"=>$enrolle_class));

            if(!empty($resClass)){
              if($extra['relationship_to_group']=="Existing"){
                if($resClass['existing_member_eligible_coverage']!='Immediately'){
                  $group_coverage_date = date('Y-m-d',strtotime($extra['relationship_date'] . "+".$resClass['existing_member_eligible_coverage']." days"));
                }
              }else if($extra['relationship_to_group']=="New"){
                if($resClass['new_member_eligible_coverage']!='Immediately'){
                  $group_coverage_date = date('Y-m-d',strtotime($extra['relationship_date'] . "+".$resClass['new_member_eligible_coverage']." days"));
                }
              }else if($extra['relationship_to_group']=="Renew"){
                if($resClass['renewed_member_eligible_coverage']!='Immediately'){
                  $group_coverage_date = date('Y-m-d',strtotime($extra['relationship_date'] . "+".$resClass['renewed_member_eligible_coverage']." days"));
                }
              }
            }
          }
          if($res['allow_future_effective_date']=='N'){
            $allow_future_effective_date ='N';
          }else{
            $allowEffectiveRange = $res['allowed_range'];
            $allowed_range = "+".$res['allowed_range']." days";
          }
        }
      }
    }
    if (count($resProducts) > 0) {
      foreach ($resProducts as $key => $row) {
        $today = date('Y-m-d');
        $next_day = date('Y-m-d', strtotime("+1 day",strtotime($today)));
        $current_day = date('j');
        if(!empty($purchasedProductData)){
          if(!empty($purchasedProductData[$row['id']])){
            $today = date('Y-m-d',strtotime($purchasedProductData[$row['id']]));
            $next_day = date('Y-m-d',strtotime("+1 day",strtotime($purchasedProductData[$row['id']])));
            $current_day = date('j',strtotime($purchasedProductData[$row['id']]));
          }
        } 

        $coverage_period = !empty($row['direct_product']) ? $row['direct_product'] : '';
        $sold_day = !empty($row['sold_day']) ?  $row['sold_day'] : 0;
        $effective_day = !empty($row['effective_day']) ? $row['effective_day'] : 0;
        $effective_day2 = !empty($row['effective_day2']) ? $row['effective_day2'] : 0;
        $member_payment_type = !empty($row['member_payment_type']) ? $row['member_payment_type'] : '';

        

        $startView = 0;
        $minViewMode = 0;

        $datesDisabled = array();
        
        
        if (!empty($coverage_period)) {
          
          if ($coverage_period == 'Next Day') {
            if(!empty($group_coverage_date) && strtotime($group_coverage_date) >= strtotime(date('Y-m-d',strtotime($today)))){
              $coverage_date = date('Y-m-d', strtotime($group_coverage_date."+1 day"));

              if($allow_future_effective_date=='N'){
                $coverage_end_date = $coverage_date;
              }else{
                $coverage_end_date = date("m/d/Y", strtotime($coverage_date . $allowed_range));
              }
              
            }else{
              $coverage_date = date('Y-m-d', strtotime($next_day));
              $coverage_end_date = date("m/d/Y", strtotime($coverage_date . "+".$allowEffectiveRange." days"));   
            }
            
          } else if ($coverage_period == 'First Of Month') {
            if(!empty($group_coverage_date) && strtotime($group_coverage_date) >= strtotime(date('Y-m-d',strtotime($today)))){
              $coverage_date = date('Y-m-01', strtotime($group_coverage_date));
              while (strtotime($group_coverage_date) > strtotime($coverage_date)) {
                  $coverage_date = date('Y-m-01', strtotime("+1 month",strtotime($coverage_date)));                    
              }
              if($allow_future_effective_date=='N'){
                $coverage_end_date = $coverage_date;
              }else{
                $coverage_end_date = date("m/d/Y", strtotime($coverage_date . $allowed_range));
              }
            }else{
              $effective_date1 = date('Y-m-01', strtotime("first day of next month"));
              if(strtotime($effective_date1) < strtotime($today)) {
                $effective_date1 = date('Y-m-01', strtotime("first day of this month",strtotime($today)));
                if(strtotime($effective_date1) < strtotime($today)) {
                  $effective_date1 = date('Y-m-01', strtotime("first day of next month",strtotime($today)));
                }
              }              
              $effective_date1_prior = date('Y-m-d',strtotime("- ".$sold_day." days",strtotime($effective_date1)));
              if(strtotime($today) <= strtotime($effective_date1_prior)) {
                  $coverage_date = $effective_date1;
              } else {
                  $coverage_date = date('Y-m-d',strtotime("+1 month",strtotime($effective_date1)));
              }              
              $coverage_end_date = date("m/d/Y", strtotime($coverage_date . "+".$allowEffectiveRange." days"));
            }
            

            $startView = 1;
            $minViewMode = 1;
          } else if ($coverage_period == 'Select Day Of Month') {

            $day_differnce = 0;
            $day_differnce2 = 0;

            if($effective_day != 'LastDayOfMonth'){
              $day_differnce = $current_day - $effective_day;
            }
            if(!empty($effective_day2) && $effective_day2 != 'LastDayOfMonth'){
              $day_differnce2 = $current_day - $effective_day2;
            }
            if($effective_day == 'LastDayOfMonth'){
              $effective_day ='t';
            } 
            if(!empty($effective_day2) && $effective_day2 == 'LastDayOfMonth'){
              $effective_day2 ='t';
            }
            
            if(!empty($group_coverage_date) && strtotime($group_coverage_date) >= strtotime(date('Y-m-d',strtotime($today)))){
              $effective_date1 = date('Y-m-' . $effective_day,strtotime($today));
              $effective_date1_prior = date('Y-m-d',strtotime("- ".$sold_day." days",strtotime($effective_date1)));
              $effective_date2 = date('Y-m-' . $effective_day2,strtotime($today));
              $effective_date2_prior = date('Y-m-d',strtotime("- ".$sold_day." days",strtotime($effective_date2)));

              if(strtotime($effective_date1_prior) < strtotime($today) && strtotime($effective_date2_prior) < strtotime($today)) {
                $effective_date1 = date('Y-m-' . $effective_day,strtotime("first day of next month",strtotime($today)));
                $effective_date1_prior = date('Y-m-d',strtotime("- ".$sold_day." days",strtotime($effective_date1)));

                $effective_date2 = date('Y-m-' . $effective_day2,strtotime("first day of next month",strtotime($today)));
                $effective_date2_prior = date('Y-m-d',strtotime("- ".$sold_day." days",strtotime($effective_date2)));
              }

              if(strtotime($today) <= strtotime($effective_date1_prior)) {
                  $coverage_date = $effective_date1;

              } else if(strtotime($today) <= strtotime($effective_date2_prior)) {
                  $coverage_date = $effective_date2;

              } else {
                  $coverage_date = date('Y-m-d',strtotime("+1 month",strtotime($effective_date1)));
                  if(!empty($effective_day2) && ($effective_day > $effective_day2)){
                    $coverage_date = date('Y-m-d',strtotime("+1 month",strtotime($effective_date2)));
                  }
              }
              
              if($allow_future_effective_date=='N'){
                $coverage_end_date = $coverage_date;
              }else{
                $coverage_end_date = date("m/d/Y", strtotime($coverage_date . $allowed_range));
              }

              for ($i = 1; $i <= $allowEffectiveRange; $i++) {
                $tmpDate = date('Y-m-d', strtotime($coverage_date . "+" . $i . " day"));
                $tmpDay = date('d', strtotime($tmpDate));
                if (($tmpDay != $effective_day && $tmpDay != $effective_day2)) {
                  $disabled_date = date("m/d/Y", strtotime($tmpDate));
                  array_push($datesDisabled, $disabled_date);
                }
              }
            }else{
              $effective_date1 = date('Y-m-' . $effective_day,strtotime($today));
              $effective_date1_prior = date('Y-m-d',strtotime("- ".$sold_day." days",strtotime($effective_date1)));
              $effective_date2 = date('Y-m-' . $effective_day2,strtotime($today));
              $effective_date2_prior = date('Y-m-d',strtotime("- ".$sold_day." days",strtotime($effective_date2)));

              if(strtotime($effective_date1_prior) < strtotime($today) && strtotime($effective_date2_prior) < strtotime($today)) {
                  $effective_date1 = date('Y-m-' . $effective_day,strtotime("first day of next month",strtotime($today)));
                  $effective_date1_prior = date('Y-m-d',strtotime("- ".$sold_day." days",strtotime($effective_date1)));

                  $effective_date2 = date('Y-m-' . $effective_day2,strtotime("first day of next month",strtotime($today)));
                  $effective_date2_prior = date('Y-m-d',strtotime("- ".$sold_day." days",strtotime($effective_date2)));
              }

              if(strtotime($today) <= strtotime($effective_date1_prior)) {
                  $coverage_date = $effective_date1;

              } else if(strtotime($today) <= strtotime($effective_date2_prior)) {
                  $coverage_date = $effective_date2;

              } else {
                  $coverage_date = date('Y-m-d',strtotime("+1 month",strtotime($effective_date1)));
                  if(!empty($effective_day2) && ($effective_day > $effective_day2)){
                    $coverage_date = date('Y-m-d',strtotime("+1 month",strtotime($effective_date2)));
                  }
              }
              
              $coverage_end_date = date("m/d/Y", strtotime($coverage_date . "+".$allowEffectiveRange." days"));

              for ($i = 1; $i <= $allowEffectiveRange; $i++) {
                $tmpDate = date('Y-m-d', strtotime($coverage_date . "+" . $i . " day"));
                $tmpDay = date('d', strtotime($tmpDate));
                if (($tmpDay != $effective_day && $tmpDay != $effective_day2)) {
                  $disabled_date = date("m/d/Y", strtotime($tmpDate));
                  array_push($datesDisabled, $disabled_date);
                }
              }
              if((31 != $effective_day && 31 != $effective_day2)) {
                $is_allow_31 = 'N';
              }
            }
            
          }
        }

        if($allow_future_effective_date=='N'){
          $coverage_end_date = $coverage_date;
        }

        if(!empty($endCoverageRes) && $endCoverageRes['is_open_enrollment']=='Y' && !empty($endCoverageRes['end_coverage_date']) 
          && (strtotime(date('Y-m-d',strtotime($endCoverageRes['end_coverage_date']))) > strtotime(date('Y-m-d',strtotime($coverage_date))))
          && (strtotime(date('Y-m-d',strtotime($endCoverageRes['end_coverage_date']))) > strtotime(date('Y-m-d')))
        ){
          $coverage_end_date = date('Y-m-d',strtotime($endCoverageRes['end_coverage_date']));


          if($coverage_period == 'Select Day Of Month'){
            $datesDisabled = array();

            $tmpDate1 = date_create($coverage_date);
            $tmpDate2 = date_create($coverage_end_date);
           
            $tmpDateDiff = date_diff($tmpDate1, $tmpDate2);
            $allowEffectiveRange = $tmpDateDiff->format('%a');

            for ($i = 1; $i <= $allowEffectiveRange; $i++) {
              $tmpDate = date('Y-m-d', strtotime($coverage_date . "+" . $i . " day"));
              $tmpDay = date('d', strtotime($tmpDate));
              if (($tmpDay != $effective_day && $tmpDay != $effective_day2)) {
                $disabled_date = date("m/d/Y", strtotime($tmpDate));
                array_push($datesDisabled, $disabled_date);
              }
            }

          }
        }

        $coverage_period_array[$row['id']]['product_id'] = $row['id'];
        $coverage_period_array[$row['id']]['product_name'] = $row['name'];
        $coverage_period_array[$row['id']]['carrier_name'] = $row['carrier_name'];
        $coverage_period_array[$row['id']]['product_code'] = $row['product_code'];
        $coverage_period_array[$row['id']]['main_product_type'] = $row['main_product_type'];
        $coverage_period_array[$row['id']]['coverage_date'] = !empty($coverage_date) ? date('m/d/Y', strtotime($coverage_date)) : '';
        $coverage_period_array[$row['id']]['coverage_end_date'] = !empty($coverage_end_date) ? date('m/d/Y', strtotime($coverage_end_date)) : '';
        $coverage_period_array[$row['id']]['coverage_period'] = $coverage_period;
        $coverage_period_array[$row['id']]['sold_day'] = $sold_day;
        $coverage_period_array[$row['id']]['effective_day'] = $effective_day;
        $coverage_period_array[$row['id']]['current_day'] = $current_day;
        $coverage_period_array[$row['id']]['startView'] = $startView;
        $coverage_period_array[$row['id']]['minViewMode'] = $minViewMode;
        $coverage_period_array[$row['id']]['datesDisabled'] = $datesDisabled;
        $coverage_period_array[$row['id']]['is_allow_31'] = $is_allow_31;
        $coverage_period_array[$row['id']]['member_payment_type'] = $member_payment_type;
      }
    }
    return $coverage_period_array;
  }

  // public function checkRoutingNumber($routingNumber = 0) {
  //   if (!preg_match('/^[0-9]{9}$/', $routingNumber)) {
  //     return false;
  //   }

  //   $checkSum = 0;
  //   for ($i = 0, $j = strlen($routingNumber); $i < $j; $i += 3) {
  //     //loop through routingNumber character by character
  //     $checkSum += ($routingNumber[$i] * 3);
  //     $checkSum += ($routingNumber[$i + 1] * 7);
  //     $checkSum += ($routingNumber[$i + 2]);
  //   }

  //   if ($checkSum != 0 and ($checkSum % 10) == 0) {
  //     return true;
  //   } else {
  //     return false;
  //   }
  // }

  public function get_customer_id()
  {
    global $pdo;
    $cust_id = rand(100000, 999999);

    $sql = "SELECT count(*) as total FROM customer WHERE rep_id ='M3" . $cust_id . "' OR rep_id ='" . $cust_id . "'";
    $res = $pdo->selectOne($sql);

    if (!empty($res['total'])) {
      return $this->get_customer_id();
    } else {
      return "M3" . $cust_id;
    }
  }

  public function variableEnrolleeOptions($product_id)
  {
    global $pdo;

    $sqlEnrollee = "SELECT * FROM prd_variable_by_enrollee WHERE is_deleted='N' AND product_id=:product_id";
    $resEnrollee = $pdo->selectOne($sqlEnrollee, array(":product_id" => $product_id));

    $responseArray = array();
    if (!empty($resEnrollee)) {
      $responseArray = $resEnrollee;
    }
    return $responseArray;
  }

  public function benefitAmountSetting($product_id)
  {
    global $pdo;

    $sqlSetting = "SELECT primary_issue_amount,spouse_issue_amount,is_spouse_issue_amount_larger,child_issue_amount FROM prd_main WHERE is_deleted='N' AND id=:product_id AND is_life_insurance_product ='Y'";
    $resSetting = $pdo->selectOne($sqlSetting, array(":product_id" => $product_id));

    $responseArray = array();
    if (!empty($resSetting)) {
      $responseArray = $resSetting;
    }
    return $responseArray;
  }
  public function getRiderProducts($product_matrix, $sponsor_id = 0)
  {
    global $pdo;
    $product_array = array();
    $product_id_array = array();
    $product_id_list = 0;

    if (!empty($product_matrix) && is_array($product_matrix)) {
      foreach ($product_matrix as $product_id => $matrix_id) {
        if (!empty($matrix_id)) {
          array_push($product_id_array, $product_id);
        }
      }
    }

    if (!empty($product_id_array)) {
      $product_id_list = implode(",", $product_id_array);
    } else if (!is_array($product_matrix)) {
      $product_id_list = $product_matrix;
    }

    $sqlProduct = "SELECT pcr.product_id as main_product_id,GROUP_CONCAT(p.id) AS product_id,
          GROUP_CONCAT(p.name SEPARATOR ', ')AS product_name
      FROM prd_rider_information pcr
      JOIN prd_main p ON (p.id=pcr.rider_product_id AND p.is_deleted='N' AND p.status='Active')        
      WHERE pcr.product_id in ($product_id_list) AND pcr.is_deleted='N' GROUP BY pcr.product_id";
    $resProduct = $pdo->select($sqlProduct);

    if (!empty($resProduct)) {
      foreach ($resProduct as $key => $value) {
        $product_id = $value['main_product_id'];
        $combination_type = 'Rider';

        if (isset($product_array[$product_id][$combination_type]['product_id'])) {
          $comninationArr = explode(",", $product_array[$product_id][$combination_type]['product_id']);
          $comninationNameArr = explode(",", $product_array[$product_id][$combination_type]['product_name']);

          array_push($comninationArr, $value['product_id']);
          array_push($comninationNameArr, $value['product_name']);

          $combination_list = implode(",", $comninationArr);
          $combination_list_name = implode(",", $comninationNameArr);

          $product_array[$product_id][$combination_type]['product_id'] = $combination_list;
          $product_array[$product_id][$combination_type]['product_name'] = $combination_list_name;
        } else {
          $product_array[$product_id][$combination_type]['product_id'] = $value['product_id'];
          $product_array[$product_id][$combination_type]['product_name'] = $value['product_name'];
        }
      }
    }


    return $product_array;
  }

  public function calculateGroupContributionPrice($price, $contributionArr, $is_price_calulated = false)
  {
    $productPrices=array(
      'member_price'=>0,
      'group_price'=>0
    );
    $type = !empty($contributionArr['type']) ? $contributionArr['type'] : 'Percentage';
    $contribution = !empty($contributionArr['contribution']) ? $contributionArr['contribution'] : 0;
    $pay_period = $contributionArr['pay_period'];

    if ($type == 'Amount') {
      $groupPrice = $contribution;
    } else if ($type == 'Percentage') {
      $groupPrice = ($price * $contribution) / 100;
    } else {
      $groupPrice = 0;
    }

    $productPrices['member_price'] = $price - $groupPrice;
    $productPrices['group_price'] = $groupPrice;
    $productPrices['contribution_type'] = $type;
    $productPrices['contribution_value'] = $contribution;
    $productPrices['pay_period'] = $pay_period;
    
    if ($pay_period == 'Weekly') {
      $productPrices['display_member_price'] = $productPrices['member_price']>0?(($productPrices['member_price']*12)/52):$productPrices['member_price'];
      $productPrices['display_group_price'] = $productPrices['group_price']>0?(($productPrices['group_price']*12)/52):$productPrices['group_price'];
    }else if($pay_period == 'Bi-Weekly'){
      $productPrices['display_member_price'] = $productPrices['member_price']>0?(($productPrices['member_price']*12)/26):$productPrices['member_price'];
      $productPrices['display_group_price'] = $productPrices['group_price']>0?(($productPrices['group_price']*12)/26):$productPrices['group_price'];
    }else if($pay_period == 'Semi-Monthly'){
      $productPrices['display_member_price'] = $productPrices['member_price']>0?(($productPrices['member_price']*12)/24):$productPrices['member_price'];
      $productPrices['display_group_price'] = $productPrices['group_price']>0?(($productPrices['group_price']*12)/24):$productPrices['group_price'];
    }else if($pay_period== 'Monthly'){
      $productPrices['display_member_price'] = $productPrices['member_price'];
      $productPrices['display_group_price'] = $productPrices['group_price'];
    }
    return $productPrices;
  }
  public function getGroupWaiveProductList($waive_checkbox,$group_waive_product){
    global $pdo;
    $group_product_list=array();
    if(!empty($waive_checkbox)){
      if(!empty($waive_checkbox)){
        foreach ($waive_checkbox as $key => $value) {
          $group_product_list = array_merge($group_product_list,$group_waive_product[$value]);
        }
      }
    }
    return $group_product_list;
  }
  public function waive_coverage_insert($sponsor_id,$sponsor_type,$waive_checkbox,$waive_coverage_reason,$waive_coverage_other_reason,$customer_id,$fname,$lname){
    global $pdo;

    foreach ($waive_checkbox as $key => $value) {

        $insert_wavied = true;
        $deleted_wavied = false;
        $waive_coverage_res = $pdo->selectOne("SELECT id,reason,other_reason FROM customer_waive_coverage WHERE customer_id = :customer_id AND category_id = :category_id AND is_deleted = 'N'", array(":customer_id" => $customer_id, ":category_id" => $value));

        if($waive_coverage_res){
          if($waive_coverage_res['reason'] == $waive_coverage_reason[$value]){
            if($waive_coverage_res['reason'] != 'Other'){
              $insert_wavied = false;
            } else {
              if($waive_coverage_res['other_reason'] == $waive_coverage_other_reason[$value]){
                $insert_wavied = false;
              } else {
                $deleted_wavied = true;    
              }
            }
          } else {
            $deleted_wavied = true;
          }
        }

        if($deleted_wavied) {
          $updat_waive_param = array(
            'is_deleted' => 'Y',
            'updated_at' => 'msqlfunc_NOW()'
          );

          $updat_waive_param_where = array(
            'clause' => 'id = :id',
            'params' => array(
              ':id' => $waive_coverage_res['id'],
            ),
          );

          $pdo->update('customer_waive_coverage', $updat_waive_param, $updat_waive_param_where);
        }

        if($insert_wavied){
          $waive_param = array(
            'customer_id' => $customer_id,
            'category_id' => $value,
            'reason' => $waive_coverage_reason[$value],
            'other_reason' => $waive_coverage_other_reason[$value],
          );

          $waive_insert_id = $pdo->insert('customer_waive_coverage', $waive_param);

          $category = "SELECT title FROM prd_category WHERE id = :id AND status='Active' ";
          $where = array( ":id" => $value );
          $product_category = $pdo->selectOne($category, $where); 

          $activity_feed_data = array(
            'Category' => $product_category['title'],
            'Reason' => $waive_coverage_reason[$value],
            'Other reason' => $waive_coverage_other_reason[$value],
          );
          activity_feed(3, $sponsor_id,$sponsor_type, $customer_id, 'Customer', 'Waive Coverage', $fname, $lname, json_encode($activity_feed_data)); 
        }
    }
  }
  public function getRenewalServiceFee($product_matrix,$customer_id, $sponsor_id, $order_total = 0, $charged_to = 'Members', $is_new_order = 'N', $is_renewal = 'Y',$renewalCountsArr)
  {
    global $pdo;
    $maxServiceFeeTotal = 0;
    $serviceFeeRow = array();

    $product_id_array = array();

    if (!empty($product_matrix) && is_array($product_matrix)) {
      foreach ($product_matrix as $product_id => $matrix_id) {
        if (!empty($matrix_id)) {
          array_push($product_id_array, $product_id);
        }
      }
    }
    
    if (empty($product_id_array) && !is_array($product_matrix)) {
      $product_id_array = explode(",", $product_matrix);
    }

    if(!empty($product_id_array)){
      foreach ($product_id_array as $key => $product_id) {
        $nbOrder = getNBOrderDetails($customer_id,$product_id);
        $nbOrderDate = !empty($nbOrder["orderDate"]) ? date("Y-m-d",strtotime($nbOrder["orderDate"])) : date("Y-m-d");

        $renewCount = $renewalCountsArr[$product_id];
        $serviceFee = $this->getServiceFee($product_id,$sponsor_id,$order_total,$charged_to,$is_new_order,$is_renewal,$renewCount,$nbOrderDate);

        $service_fee_total = $serviceFee['total'];

        if($service_fee_total > $maxServiceFeeTotal){
          $maxServiceFeeTotal = $service_fee_total;
          $serviceFeeRow = $serviceFee;
        }
      }
    }
    return $serviceFeeRow;
  }
  public function getRenewalLinkedFee($product_matrix,$customer_id, $sponsor_id, $is_new_order = 'N', $is_renewal = 'Y',
    $renewalCountsArr)
  {
    global $pdo;
    $linkedFeeTotal = 0;
    $linkedFeeRow = array();
    $is_group_member = 'N';

    $sponser_type = getname("customer",$sponsor_id,"type","id");
    if($sponser_type == 'Group'){
      $is_group_member = 'Y';
    }

    if (!empty($product_matrix)) {
      $i = 0;
      foreach ($product_matrix as $product_id => $matrix_id) {
          $feePrdId = '';
          $prdArr = array($product_id => $matrix_id);

          $nbOrder = getNBOrderDetails($customer_id,$product_id);
          $nbOrderDate = !empty($nbOrder["orderDate"]) ? date("Y-m-d",strtotime($nbOrder["orderDate"])) : date("Y-m-d");

          $renewCount = $renewalCountsArr[$product_id];
          $linkedFeeRule = $this->getLinkedFee($prdArr,$sponsor_id,$is_new_order,$is_renewal,$renewCount,$nbOrderDate,$is_group_member);
      
          if(!empty($linkedFeeRule)){
            $linkedFeeTotal += $linkedFeeRule["total"];
            unset($linkedFeeRule["total"]);
            unset($linkedFeeRule['total_single']);
            unset($linkedFeeRule['total_annually']);
            if(!empty($linkedFeeRule)){
              foreach ($linkedFeeRule as $fee) {
                if($fee['pricing_model']== 'VariableEnrollee' && $feePrdId==$fee['product_id']){
                  $linkedFeeRow[$i]['price'] += $fee['price'];
                  $str = ','.$fee['matrix_id'];
                  $linkedFeeRow[$i]['matrix_id'] .= $str;
                  continue;
                }
                $feePrdId = $fee['product_id'];
                array_push($linkedFeeRow, $fee);
                $i = array_key_last($linkedFeeRow);
              }
            }
          }
      }
      if(!empty($linkedFeeTotal)){
        $linkedFeeRow["total"] = $linkedFeeTotal;
      }
    }
    return $linkedFeeRow;
  }
  public function getRenewalMembershipFee($product_matrix,$customer_id, $zip_code,$is_new_order = 'N', $is_renewal = 'Y',
    $renewalCountsArr)
  {
    global $pdo;
    $maxMembershipTotal = 0;
    $membershipFeeRow = array();

    $product_id_array = array();

    if (!empty($product_matrix) && is_array($product_matrix)) {
      foreach ($product_matrix as $product_id => $matrix_id) {
        if (!empty($matrix_id)) {
          array_push($product_id_array, $product_id);
        }
      }
    }
    
    if (empty($product_id_array) && !is_array($product_matrix)) {
      $product_id_array = explode(",", $product_matrix);
    }

    if(!empty($product_id_array)){
      foreach ($product_id_array as $key => $product_id) {
        $nbOrder = getNBOrderDetails($customer_id,$product_id);
        $nbOrderDate = !empty($nbOrder["orderDate"]) ? date("Y-m-d",strtotime($nbOrder["orderDate"])) : date("Y-m-d");

        $renewCount = $renewalCountsArr[$product_id];
        $membershipFee = $this->getMembershipFee($product_id,$customer_id, $zip_code,$is_new_order,$is_renewal,$renewCount,$nbOrderDate);

        $membership_fee_total = $membershipFee['total'];

        if($membership_fee_total > $maxServiceFeeTotal){
          $maxServiceFeeTotal = $membership_fee_total;
          $membershipFeeRow = $membershipFee;
        }
      }
    }
    return $membershipFeeRow;
  }
  public function getCoreProducts($product_matrix, $added_product='',$combination_products = array())
  {
    global $pdo;
    $product_array = array();
    $product_id_array = array();
    $product_id_list = 0;



    if (!empty($product_matrix) && is_array($product_matrix)) {
      foreach ($product_matrix as $product_id => $matrix_id) {
          array_push($product_id_array, $product_id);
      }
    }

    if (!empty($product_id_array)) {
      $product_id_list = implode(",", $product_id_array);
    } else if (!is_array($product_matrix)) {
      $product_id_list = $product_matrix;
    }
    $added_product_arr = explode(",", $added_product);
    

    if(!empty($added_product_arr)){
      foreach ($added_product_arr as $addedKey => $addedProduct) {
        $sqlAddedProduct = "SELECT id,name,type FROM prd_main where id=:id AND type='Normal' AND main_product_type='Core Product'";
        $resAddedProduct = $pdo->selectOne($sqlAddedProduct,array(":id"=>$addedProduct));

        if(!empty($resAddedProduct)){
          
          if(!empty($combination_products[$addedProduct]['Excludes'])) {
              $product_array[$addedProduct]['Excludes']['product_id'] = $combination_products[$addedProduct]['Excludes']['product_id'];
              $product_array[$addedProduct]['Excludes']['product_name'] = $combination_products[$addedProduct]['Excludes']['product_name'];
          }

          $sqlProduct = "SELECT 'Excludes' as combination_type,GROUP_CONCAT(p.id) AS product_id,
                GROUP_CONCAT(p.name SEPARATOR ', ') AS product_name
            FROM prd_main p    
            WHERE p.is_deleted='N' AND p.main_product_type='Core Product' AND p.id !=:id AND p.status='Active' AND p.id in ($product_id_list) GROUP BY p.id";
          $resProduct = $pdo->select($sqlProduct,array(":id"=>$addedProduct));

          if (!empty($resProduct)) {
            foreach ($resProduct as $key => $value) {
              $product_id = $resAddedProduct['id'];
              $combination_type = $value['combination_type'];

              if (isset($product_array[$product_id][$combination_type]['product_id'])) {
                $comninationArr = explode(",", $product_array[$product_id][$combination_type]['product_id']);
                $comninationNameArr = explode(",", $product_array[$product_id][$combination_type]['product_name']);

                array_push($comninationArr, $value['product_id']);
                array_push($comninationNameArr, $value['product_name']);

                $combination_list = implode(",", $comninationArr);
                $combination_list_name = implode(",", $comninationNameArr);

                $product_array[$product_id][$combination_type]['product_id'] = $combination_list;
                $product_array[$product_id][$combination_type]['product_name'] = $combination_list_name;
              } else {
                $product_array[$product_id][$combination_type]['product_id'] = $value['product_id'];
                $product_array[$product_id][$combination_type]['product_name'] = $value['product_name'];
              }
            }
          }
        }
      }
    }
    return $product_array;
  }

  public function getAlreadyPurchasedCoreProducts($customer_id){
    global $pdo,$ALLOWED_SUBSCRIPTION_STATUS,$MEMBER_STATUS;
    $allowedSubscriptionStatus = implode("','", $ALLOWED_SUBSCRIPTION_STATUS);
    $allowedMemberStatus = implode("','", $MEMBER_STATUS);

    $sqlCoreWebsite="SELECT p.id,w.termination_date,w.eligibility_date 
          FROM website_subscriptions w 
          JOIN customer c ON (c.id = w.customer_id)
          JOIN prd_main p ON (p.id = w.product_id AND p.type='Normal' AND p.main_product_type='Core Product')
          where 
          w.customer_id = :customer_id AND 
          w.status IN('".$allowedSubscriptionStatus."') AND 
          c.status IN('".$allowedMemberStatus."')";
    $resCoreWebsite=$pdo->select($sqlCoreWebsite,array(":customer_id"=>$customer_id));

    $earliest_effective = date('Y-m-d');
    $purchaseCoreProducts = array();
    if(!empty($resCoreWebsite)){
      foreach ($resCoreWebsite as $coreValue) {
        if(empty($coreValue['termination_date'])) {
          array_push($purchaseCoreProducts, $coreValue['id']);
        } else {
          if($coreValue['eligibility_date'] != $coreValue['termination_date']) {
            if(strtotime(date('Y-m-d')) < strtotime($coreValue['termination_date'])) {
              //array_push($purchaseCoreProducts, $coreValue['id']);
              // $earliest_effective = date('Y-m-d',strtotime('+1 day',strtotime($coreValue['termination_date'])));
              $earliest_effective = date('Y-m-d',strtotime($coreValue['termination_date']));
            }
          }
        }
      }
    }
    return array('purchaseCoreProducts' => $purchaseCoreProducts,'earliest_effective' => $earliest_effective);
  }

  public function get_core_prd_earliest_effective_date($ws_id)
  {
      global $pdo,$ALLOWED_SUBSCRIPTION_STATUS;
      $allowedSubscriptionStatus = implode("','", $ALLOWED_SUBSCRIPTION_STATUS);
      $earliest_effective_date = '';
      $ws_sql = "SELECT ws.id,ws.customer_id,ws.eligibility_date
                      FROM website_subscriptions ws
                      WHERE ws.id=:id";
      $ws_row = $pdo->selectOne($ws_sql,array(":id"=>$ws_id));
      $core_prd_sql = "SELECT w.id,MAX(w.termination_date) as termination_date
                  FROM website_subscriptions w
                  JOIN prd_main p ON (p.id=w.product_id AND p.main_product_type='Core Product')
                  WHERE
                  w.id !=:id AND 
                  w.customer_id=:customer_id AND 
                  w.termination_date != w.eligibility_date AND
                  w.eligibility_date <= :eligibility_date AND
                  w.status IN('".$allowedSubscriptionStatus."')";
      $core_prd_where = array(
          ":id"=>$ws_row['id'],
          ":customer_id"=>$ws_row['customer_id'],
          ":eligibility_date"=>$ws_row['eligibility_date'],
      );
      $core_prd_row = $pdo->selectOne($core_prd_sql,$core_prd_where);
      if(!empty($core_prd_row) && !empty($core_prd_row['termination_date'])) {
          $earliest_effective_date = date('Y-m-d',strtotime('+1 day',strtotime($core_prd_row['termination_date'])));
      }
      return $earliest_effective_date;
  }

  public function check_allow_cancel_core_prd_termination($ws_id,$customer_id,$termination_date = '')
  {
      global $pdo,$ALLOWED_SUBSCRIPTION_STATUS;
      $allowedSubscriptionStatus = implode("','", $ALLOWED_SUBSCRIPTION_STATUS);
      $allow_cancel_termination = true;
      $max_termination_date = '';
      if(empty($termination_date)) {
          $ws_sql = "SELECT ws.id,ws.termination_date
                      FROM website_subscriptions ws
                      WHERE ws.id=:id";
          $ws_row = $pdo->selectOne($ws_sql,array(":id"=>$ws_id));
          $termination_date = $ws_row['termination_date'];
      }

      $core_prd_sql = "SELECT w.id,w.eligibility_date
                  FROM website_subscriptions w
                  JOIN prd_main p ON (p.id=w.product_id AND p.main_product_type='Core Product')
                  WHERE
                  w.id!=:id AND 
                  w.customer_id=:customer_id AND 
                  (w.termination_date IS NULL OR ((w.eligibility_date >= :termination_date OR w.termination_date >= :termination_date) AND w.termination_date != w.eligibility_date)) AND
                  w.status IN('".$allowedSubscriptionStatus."')";
      $core_prd_where = array(
          ":id"=>$ws_id,
          ":customer_id"=>$customer_id,
          ":termination_date" => $termination_date
      );
      $core_prd_row = $pdo->selectOne($core_prd_sql,$core_prd_where);
      if(!empty($core_prd_row)) {
        $allow_cancel_termination = false;
        $max_termination_date = date('Y-m-d',strtotime('-1 day',strtotime($core_prd_row['eligibility_date'])));
      }
      return array('allow_cancel_termination' => $allow_cancel_termination,'max_termination_date' => $max_termination_date);
  }
  public function getAlreadyPurchasedProducts($customer_id){
    global $pdo,$ALLOWED_SUBSCRIPTION_STATUS,$MEMBER_STATUS;
    $allowedSubscriptionStatus = implode("','", $ALLOWED_SUBSCRIPTION_STATUS);
    $allowedMemberStatus = implode("','", $MEMBER_STATUS);

    $already_puchase_product = array();

    $sqlWebsite="SELECT w.id,w.product_id,w.plan_id,w.status,w.termination_date,p.type as product_type,p.reenroll_options,p.reenroll_within,p.reenroll_within_type,w.eligibility_date 
          FROM website_subscriptions w 
          JOIN customer c ON (c.id = w.customer_id)
          JOIN prd_main p ON (p.id = w.product_id)
          where w.customer_id = :customer_id AND w.status IN('"
          .$allowedSubscriptionStatus."') AND c.status IN('".$allowedMemberStatus."')";
    $resWebsite=$pdo->select($sqlWebsite,array(":customer_id"=>$customer_id));

    if(!empty($resWebsite)){
      foreach ($resWebsite as $key => $productRow) {
        if(!empty($productRow['termination_date']) && $productRow['termination_date'] == $productRow['eligibility_date']){
          continue;
        }
        if($productRow['reenroll_options']=='Available After Specific Time Frame'){
          $termination_date = $productRow['termination_date'];

          if(isset($termination_date) && !empty($termination_date)){
            
            $currentDateTime = new DateTime();
            $dateTimeInTheFuture = new DateTime($termination_date);
            $dateInterval = $dateTimeInTheFuture->diff($currentDateTime);
            
            $is_reenroll_option_within=false;
            if($productRow['reenroll_within_type']=='Days'){
              if($dateInterval->days>=$productRow['reenroll_within']){
                $is_reenroll_option_within=true;
              }
            }elseif($productRow['reenroll_within_type']=='Weeks'){
              if(($dateInterval->days/7)>=$productRow['reenroll_within']){
                $is_reenroll_option_within=true;
              }
            }elseif($productRow['reenroll_within_type']=='Months'){
              $totalMonths=0;
              if(!empty($dateInterval->y)){
                $totalMonths=$dateInterval->y*12;
              }
              $totalMonths = $totalMonths + $dateInterval->m;
              if($totalMonths>=$productRow['reenroll_within']){
                $is_reenroll_option_within=true;
              }
            }elseif($productRow['reenroll_within_type']=='Years'){
              if($dateInterval->y>=$productRow['reenroll_within']){
                $is_reenroll_option_within=true;
              }
            }

            if(strtotime(date('Y-m-d')) < strtotime($termination_date)){
              $is_reenroll_option_within=false;
            }

            if(!$is_reenroll_option_within){
              //array_push($already_puchase_product, $productRow['product_id']);
            }
          }else{
            array_push($already_puchase_product, $productRow['product_id']);
          }
        }else if($productRow['reenroll_options'] == 'Available Without Restrictions'){
          $termination_date = $productRow['termination_date'];

          if(isset($termination_date) && !empty($termination_date)){
            if(strtotime(date('Y-m-d')) < strtotime($termination_date)){
              //array_push($already_puchase_product, $productRow['product_id']);
            }
          } else {
            array_push($already_puchase_product, $productRow['product_id']);  
          }
        }else{
          array_push($already_puchase_product, $productRow['product_id']);
        }
      }
    }

    return $already_puchase_product;
  }

  public function addOnDisplay($customer_id = 0){
    global $pdo,$ALLOWED_SUBSCRIPTION_STATUS,$MEMBER_STATUS;
    $allowedSubscriptionStatus = implode("','", $ALLOWED_SUBSCRIPTION_STATUS);
    $allowedMemberStatus = implode("','", $MEMBER_STATUS);

    $addOnDisplay = 'true';
    $notDisplay = 0;
    $resWebsite = array();

    $sqlWebsite="SELECT w.termination_date,w.eligibility_date 
          FROM website_subscriptions w 
          JOIN customer c ON (c.id = w.customer_id)
          JOIN prd_main p ON (p.id = w.product_id)
          where p.type = 'Normal' AND p.main_product_type='Core Product' AND w.customer_id = :customer_id AND w.status IN('"
          .$allowedSubscriptionStatus."') AND c.status IN('".$allowedMemberStatus."')";
    $resWebsite=$pdo->select($sqlWebsite,array(":customer_id"=>$customer_id));

    if(!empty($resWebsite)){
      foreach ($resWebsite as $key => $value) {
        if($value['termination_date'] == $value['eligibility_date']){
          $notDisplay++;
        }
      }
    } 

    if(isset($resWebsite) && count($resWebsite) == $notDisplay){
      $addOnDisplay = 'false';
    }

    return $addOnDisplay;
  }

  public function validate_existing_email($email,$sponsor_id,$customer_id = 0,$lead_id = 0,$extra_params = array()) {
      global $pdo,$AAE_WEBSITE_HOST;
      $location = (isset($extra_params['location'])?$extra_params['location']:'');
      $site_user_name = (isset($extra_params['site_user_name'])?$extra_params['site_user_name']:'');
      $response = array();
      $cust_where = array(':email' => $email);
      $incr = "";

      if(!isset($extra_params['is_add_product']) || (isset($extra_params['is_add_product']) && $extra_params['is_add_product'] != 1)) {
          $customer_id = 0;
      }

      if(!empty($customer_id)){
          $incr .= " AND id != :id";
          $cust_where[":id"] = $customer_id;
      }

      /*--- Check Member Already Exist ---*/
      $cust_sql = "SELECT id,email,sponsor_id 
                      FROM customer 
                      WHERE 
                      email=:email AND 
                      type='Customer' AND 
                      is_deleted='N' AND 
                      status NOT IN('Customer Abandon','Pending Quote','Pending Validation')
                      $incr
                      ";
      $cust_row = $pdo->selectOne($cust_sql,$cust_where);

      //Member Already Exist
      if(!empty($cust_row)) {
          if($cust_row['sponsor_id'] == $sponsor_id) {
              $response['status'] = "fail";
              $response['existing_status'] = "bob_member";
              $response['message'] = "This member already exists and is part of your book of business.";
              $response['enrollment_url'] = 'member_enrollment.php?customer_id='.md5($cust_row['id']);

              if((in_array($location,array("aae_site","self_enrollment_site")) && !empty($site_user_name)) || ($location == 'groupSide' && !empty($site_user_name))) {
                  $response['enrollment_url'] = $AAE_WEBSITE_HOST . '/' . $site_user_name . '/' . md5($cust_row['id']) . '/m';
              }
          } else {
              $check_direct_loa = $pdo->selectOne("SELECT c.id FROM customer c LEFT JOIN customer_settings cs ON(cs.customer_id=c.id) WHERE cs.agent_coded_level=:type AND c.sponsor_id=:sponsor_id AND c.type='Agent' AND c.is_deleted='N' AND c.id=:id",array(":type"=>'LOA',":sponsor_id"=>$sponsor_id,":id"=>$cust_row['sponsor_id']));
              if(!empty($check_direct_loa)) {
                  $response['status'] = "fail";
                  $response['existing_status'] = "bob_member";
                  $response['message'] = "This member already exists and is part of your book of business.";
                  $response['enrollment_url'] = 'member_enrollment.php?customer_id='.md5($cust_row['id']);
                  if(in_array($location,array("aae_site","self_enrollment_site")) && !empty($site_user_name)) {
                      $response['enrollment_url'] = $AAE_WEBSITE_HOST . '/' . $site_user_name . '/' . md5($cust_row['id']) . '/m';
                  }
              } else {
                  $response['status'] = "fail";
                  $response['existing_status'] = "none_bob_member"; //member not part of your book of business
                  $response['message'] = "This member already exists."; //member not part of your book of business
                  $response['enrollment_url'] = 'javascript:void(0);';
              }
          }

          if(isset($extra_params['unqualified_leads'])) {
              $this->unqualified_leads_with_duplicate_email($email,$cust_row['id']);
          }
      } else {
          if(!empty($customer_id) && empty($lead_id)) {
              $lead_id = getname("leads",$customer_id,"id","customer_id");
          }
          /*--- Check Lead Already Exist ---*/
          $lead_where = array(':email' => $email);
          $incr = "";
          if(!empty($lead_id)){
              $incr .= " AND id != :id";
              $lead_where[":id"] = $lead_id;
          }
          $lead_sql = "SELECT id,email,sponsor_id 
                          FROM leads 
                          WHERE 
                          status!='Unqualified' AND 
                          email=:email AND 
                          lead_type='Member' AND 
                          is_deleted='N' 
                          $incr";
          $lead_row = $pdo->selectOne($lead_sql, $lead_where);

          if (!empty($lead_row)) {
              if($lead_row['sponsor_id'] == $sponsor_id) {
                  $response['status'] = "fail";
                  $response['existing_status'] = "bob_lead";
                  $response['message'] = "This lead already exists and is part of your book of business.";
                  $response['enrollment_url'] = "member_enrollment.php?lead_id=".md5($lead_row['id']);
                  if((in_array($location,array("aae_site","self_enrollment_site")) && !empty($site_user_name)) || ($location == 'groupSide' && !empty($site_user_name))) {
                      $response['enrollment_url'] = $AAE_WEBSITE_HOST . '/' . $site_user_name . '/' . md5($lead_row['id']) . '/l';
                  }
              } else {
                  $check_direct_loa = $pdo->selectOne("SELECT c.id FROM customer c LEFT JOIN customer_settings cs ON(cs.customer_id=c.id) WHERE cs.agent_coded_level=:type AND c.sponsor_id=:sponsor_id AND c.type='Agent' AND c.is_deleted='N' AND c.id=:id",array(":type"=>'LOA',":sponsor_id"=>$sponsor_id,":id"=>$lead_row['sponsor_id']));
                  if(!empty($check_direct_loa)) {
                      $response['status'] = "fail";
                      $response['existing_status'] = "bob_lead";
                      $response['message'] = "This lead already exists and is part of your book of business.";
                      $response['enrollment_url'] = "member_enrollment.php?lead_id=".md5($lead_row['id']);
                      if(in_array($location,array("aae_site","self_enrollment_site")) && !empty($site_user_name)) {
                          $response['enrollment_url'] = $AAE_WEBSITE_HOST . '/' . $site_user_name . '/' . md5($lead_row['id']) . '/l';
                      }
                  } else {
                      $response['status'] = "success";        
                  }
              }
          } else {
              $response['status'] = "success";
          }
      }
      return $response;
  }

  public function unqualified_leads_with_duplicate_email($email,$customer_id,$extra_params = array()) {
    global $pdo;

    $lead_row = $pdo->selectOne("SELECT id,email,lead_type FROM leads WHERE customer_id=:customer_id",array(":customer_id" => $customer_id));
    if(!empty($customer_id) && !empty($lead_row)) {
      $lead_where = array(
        ':email' => $email,
        ':lead_type' => $lead_row['lead_type'],
        ":id" => $lead_row['id'],
      );
      $lead_sql = "SELECT id,customer_id,status,fname,lname,lead_id
                        FROM leads 
                        WHERE 
                        email!='Unqualified' AND 
                        email=:email AND 
                        lead_type=:lead_type AND 
                        is_deleted='N' AND
                        id!=:id";
      $lead_res = $pdo->select($lead_sql,$lead_where);
      if(!empty($lead_res)) {
          foreach ($lead_res as $un_lead_row) {
            $lead_where = array(
              "clause" => "id=:id",
              "params" => array(
                ":id" => $un_lead_row['id'],
              ),
            );
            $pdo->update("leads", array('status' =>'Unqualified'), $lead_where);

            $lead_quote_detail_where = array(
              "clause" => "customer_ids=:id",
              "params" => array(
                ":id" => $un_lead_row['customer_id'],
              ),
            );
            $pdo->update("lead_quote_details", array('status' => 'Disabled', 'updated_at' => 'msqlfunc_NOW()'), $lead_quote_detail_where);

            $old_status = $un_lead_row['status'];
            $new_status = "Unqualified";

            $desc = array();
            $desc['ac_message'] =array(
              'ac_message_1' =>'System updated Lead '.($un_lead_row['fname'].' '.$un_lead_row['lname']).'(',
              'ac_red_2'=>array(
                  'href'=> 'lead_details.php?id='.md5($un_lead_row['id']),
                  'title'=> $un_lead_row['lead_id'],
              ),
              'ac_message_2'=>') status from '.$old_status.' to '.$new_status,
            );
            $desc = json_encode($desc);
            activity_feed(3,$un_lead_row['id'],'Lead',$un_lead_row['id'],'Lead','Status Updated','','',$desc);

            if(!empty($un_lead_row['customer_id'])) {
                $cust_where = array(
                  "clause" => "type='Customer' AND status IN('Customer Abandon', 'Pending Quote', 'Pending Validation') AND id=:id",
                  "params" => array(
                      ":id" => $un_lead_row['customer_id'],
                  ),
                );
                $pdo->update("customer", array('is_deleted' =>'Y'), $cust_where);
            }
          }
      }
    }
  }

  public function send_temporary_password_mail($customer_id,$extra_params = array())
  {
      global $pdo,$CREDIT_CARD_ENC_KEY;
      $cust_sql = "SELECT id,rep_id,fname,lname,email,cell_phone,IF(password = '' OR password IS NULL,'',AES_DECRYPT(password,'" . $CREDIT_CARD_ENC_KEY . "')) as password FROM customer WHERE id=:id AND is_password_set='N'";
      $cust_where = array(":id" => $customer_id);
      $cust_row = $pdo->selectOne($cust_sql,array(":id" => $customer_id));
      if(!empty($cust_row)) {

          if(!empty($cust_row['password'])) {
              $password = $cust_row['password'];
          } else {
              $password = generate_chat_password(10);
          }

          $upd_data = array(
              'is_password_set' => "N",
              'password' => "msqlfunc_AES_ENCRYPT('" . $password . "','" . $CREDIT_CARD_ENC_KEY . "')",
          );
          $upd_where = array(
            'clause' => 'id = :id',
            'params' => array(
              ':id' => $customer_id,
            ),
          );
          $pdo->update('customer',$upd_data,$upd_where);
          
          $trigger_row = $pdo->selectOne("SELECT id,email_content,display_id from triggers where display_id='T843'");
          if(!empty($trigger_row)) {

            $trigger_id = $trigger_row['id'];

            $mail_data = array();
            $mail_data['MemberName'] = $cust_row['fname'].' '.$cust_row['lname'];
            $mail_data['TempPassword'] = $password;
            $smart_tags = get_user_smart_tags($customer_id,'member');
            if($smart_tags){
                $mail_data = array_merge($mail_data,$smart_tags);
            }
            trigger_mail($trigger_id, $mail_data,$cust_row['email'],array(),3);


            $email_activity = array();
            $email_cn = $trigger_row['email_content'];
            foreach ($mail_data as $placeholder => $value) {
              $email_cn = str_replace("[[" . $placeholder . "]]", $value, $email_cn);
            }
            $email_activity["ac_description_link"] = array(
              'Trigger :' => array(
                'href'=>'#javascript:void(0)',
                'class'=>'descriptionPopup',
                'title'=>$trigger_row['display_id'],
                'data-desc'=>htmlspecialchars($email_cn),
                'data-encode'=>'no'
              ),
              '<br>Email :' => array(
                'href' => '#javascript:void(0)',
                'title' => $cust_row['email'],
              )
            );
            activity_feed(3, $customer_id, "Customer", $trigger_id, 'triggers', 'Temporary password email delivered','','',json_encode($email_activity));
          }
      }
  }

  /*---- getConnectedProducts ----*/
  public function getPolicyUpgradeDowngradeSetting($product_id,$effective_date,$agent_id){
      global $pdo;
      $conn_data = array();
      $conn_sql = "SELECT pcp.connection_id,pcp.order_by,pc.* 
                  FROM prd_connected_products pcp 
                  JOIN prd_connections pc ON(pc.id = pcp.connection_id AND pc.is_deleted='N') 
                  JOIN prd_main p ON (p.id=pcp.product_id)
                  WHERE pcp.product_id=:product_id AND pcp.is_deleted='N'";
      $conn_row = $pdo->selectOne($conn_sql,array(":product_id" => $product_id));
      if(!empty($conn_row)) {
          $currentDateTime = new DateTime();
          $dateTimeInTheFuture = new DateTime($effective_date);
          $dateInterval = $dateTimeInTheFuture->diff($currentDateTime);

          $conn_data['conn_row'] = $conn_row;

          $is_allow_upgrade = false;
          $is_allow_downgrade = false;
          $display_life_event_icon = 'N';

          if($conn_row['upgrade_option'] == "Available Without Restrictions") {
              $is_allow_upgrade = true;

          } else if($conn_row['upgrade_option'] == "Available Within Specific Time Frame") {
              if ($conn_row['upgrade_within_type'] == 'Days') {
                  if ($dateInterval->days <= $conn_row['upgrade_within']) {
                      $is_allow_upgrade = true;
                  }
              } elseif ($conn_row['upgrade_within_type'] == 'Weeks') {
                  if (($dateInterval->days / 7) <= $conn_row['upgrade_within']) {
                      $is_allow_upgrade = true;
                  }
              } elseif ($conn_row['upgrade_within_type'] == 'Months') {
                  if ($dateInterval->m <= $conn_row['upgrade_within']) {
                      $is_allow_upgrade = true;
                  }
              } elseif ($conn_row['upgrade_within_type'] == 'Years') {
                  if ($dateInterval->y <= $conn_row['upgrade_within']) {
                      $is_allow_upgrade = true;
                  }
              }
          }

          if($is_allow_upgrade == false) {
              if($conn_row['is_allow_upgrade_life_event'] == "Y") {
                  $display_life_event_icon = 'Y';
              }
          }

          if($conn_row['downgrade_option'] == "Available Without Restrictions") {
              $is_allow_downgrade = true;

          } else if($conn_row['downgrade_option'] == "Available Within Specific Time Frame") {
              if ($conn_row['downgrade_within_type'] == 'Days') {
                  if ($dateInterval->days <= $conn_row['downgrade_within']) {
                      $is_allow_downgrade = true;
                  }
              } elseif ($conn_row['downgrade_within_type'] == 'Weeks') {
                  if (($dateInterval->days / 7) <= $conn_row['downgrade_within']) {
                      $is_allow_downgrade = true;
                  }
              } elseif ($conn_row['downgrade_within_type'] == 'Months') {
                  if ($dateInterval->m <= $conn_row['downgrade_within']) {
                      $is_allow_downgrade = true;
                  }
              } elseif ($conn_row['downgrade_within_type'] == 'Years') {
                  if ($dateInterval->y <= $conn_row['downgrade_within']) {
                      $is_allow_downgrade = true;
                  }
              }
          }

          if($is_allow_downgrade == false) {
              if($conn_row['is_allow_downgrade_life_event'] == "Y") {
                  $display_life_event_icon = 'Y';
              }
          }

          $apr_str = "";
          if(!empty($agent_id)) {
              $apr_str = " JOIN agent_product_rule apr ON (apr.product_id = p.id AND apr.status='Contracted' AND apr.is_deleted='N' AND apr.agent_id='$agent_id')";
          }

          if($is_allow_upgrade == true && $is_allow_downgrade == true) {
              $conn_data['conn_prd'] = $pdo->select("SELECT p.name,p.product_code,p.id FROM prd_connected_products pcp 
               JOIN prd_main p ON (p.id=pcp.product_id) $apr_str
               WHERE pcp.connection_id=:connection_id AND pcp.is_deleted='N' ORDER BY pcp.order_by ASC",array(':connection_id' => $conn_row['connection_id']));

          } else if($is_allow_upgrade == true) {
              $conn_data['conn_prd'] = $pdo->select("SELECT p.name,p.product_code,p.id FROM prd_connected_products pcp 
               JOIN prd_main p ON (p.id=pcp.product_id) $apr_str
               WHERE pcp.connection_id=:connection_id AND pcp.is_deleted='N' AND pcp.order_by <= :order_by ORDER BY pcp.order_by ASC",array(':connection_id' => $conn_row['connection_id'],':order_by' => $conn_row['order_by']));

          } else if($is_allow_downgrade == true) {
              $conn_data['conn_prd'] = $pdo->select("SELECT p.name,p.product_code,p.id FROM prd_connected_products pcp 
               JOIN prd_main p ON (p.id=pcp.product_id) $apr_str
               WHERE pcp.connection_id=:connection_id AND pcp.is_deleted='N' AND pcp.order_by >= :order_by ORDER BY pcp.order_by ASC",array(':connection_id' => $conn_row['connection_id'],':order_by' => $conn_row['order_by']));

          } else {
              $prd_row = $pdo->selectOne("SELECT p.id,p.product_code,p.name FROM prd_main p WHERE p.id=:id",array(":id" => $product_id));
              $conn_data['conn_prd'][0] =  array(
                  'id' => $prd_row['id'],
                  'name' => $prd_row['name'],
                  'product_code' => $prd_row['product_code']
              );
          }

          if(empty($conn_data['conn_prd'])) {
              $prd_row = $pdo->selectOne("SELECT p.id,p.product_code,p.name FROM prd_main p WHERE p.id=:id",array(":id" => $product_id));
              $conn_data['conn_prd'][0] =  array(
                  'id' => $prd_row['id'],
                  'name' => $prd_row['name'],
                  'product_code' => $prd_row['product_code']
              );
          }

          if(!empty($apr_str) && $display_life_event_icon == "Y") {
              $tmp_conn_prd = $pdo->select("SELECT p.name,p.product_code,p.id FROM prd_connected_products pcp 
               JOIN prd_main p ON (p.id=pcp.product_id) $apr_str
               WHERE pcp.connection_id=:connection_id AND pcp.is_deleted='N' AND p.id !=:id ORDER BY pcp.order_by ASC",array(':connection_id' => $conn_row['connection_id'],":id" => $product_id));
              if(empty($tmp_conn_prd)) {
                  $display_life_event_icon = 'N';
              }
          }

          $conn_data['display_life_event_icon'] = $display_life_event_icon;
      } else {
          $prd_row = $pdo->selectOne("SELECT p.id,p.product_code,p.name FROM prd_main p WHERE p.id=:id",array(":id" => $product_id));
          $conn_data['display_life_event_icon'] = "N";
          $conn_data['conn_row'] = array();
          $conn_data['conn_prd'][0] =  array(
              'id' => $prd_row['id'],
              'name' => $prd_row['name'],
              'product_code' => $prd_row['product_code']
          );
      }
      return $conn_data;
  }

  /*---- getConnectedProducts ----*/
  public function getPolicyUpgradeDowngradeLifeEventPrds($product_id,$effective_date,$agent_id){
      global $pdo;
      $prd_conn_data = $this->getPolicyUpgradeDowngradeSetting($product_id,$effective_date,$agent_id);
      $conn_row = $prd_conn_data['conn_row'];
      if(!empty($conn_row)) {
        $prd_conn_data['order_by'] = $conn_row['order_by'];
        $prd_conn_data['upgrade_life_event_options'] = (!empty($conn_row['upgrade_life_event_options'])?json_decode($conn_row['upgrade_life_event_options'],true):array());
        $prd_conn_data['downgrade_life_event_options'] = (!empty($conn_row['downgrade_life_event_options'])?json_decode($conn_row['downgrade_life_event_options'],true):array());

        $prd_conn_data['life_event_options'] = array_merge($prd_conn_data['upgrade_life_event_options'],$prd_conn_data['downgrade_life_event_options']);
        $prd_conn_data['life_event_options'] = array_unique($prd_conn_data['life_event_options']);
        sort($prd_conn_data['life_event_options']);

        $apr_str = "";
        if(!empty($agent_id)) {
            $apr_str = " JOIN agent_product_rule apr ON (apr.product_id = p.id AND apr.status='Contracted' AND apr.is_deleted='N' AND apr.agent_id='$agent_id')";
        }

        if($conn_row['is_allow_upgrade_life_event'] == "Y" && $conn_row['is_allow_downgrade_life_event'] == "Y") {
            $prd_conn_data['conn_prd'] = $pdo->select("SELECT p.name,p.product_code,p.id,pcp.order_by FROM prd_connected_products pcp 
                 JOIN prd_main p ON (p.id=pcp.product_id) $apr_str
                 WHERE pcp.connection_id=:connection_id AND pcp.is_deleted='N' AND pcp.order_by != :order_by ORDER BY pcp.order_by ASC",array(':connection_id' => $conn_row['connection_id'],':order_by' => $conn_row['order_by']));

        } else if($conn_row['is_allow_upgrade_life_event'] == "Y") {
            $prd_conn_data['conn_prd'] = $pdo->select("SELECT p.name,p.product_code,p.id,pcp.order_by FROM prd_connected_products pcp 
                 JOIN prd_main p ON (p.id=pcp.product_id) $apr_str
                 WHERE pcp.connection_id=:connection_id AND pcp.is_deleted='N' AND pcp.order_by < :order_by ORDER BY pcp.order_by ASC",array(':connection_id' => $conn_row['connection_id'],':order_by' => $conn_row['order_by']));

        } else if($conn_row['is_allow_downgrade_life_event'] == "Y") {
            $prd_conn_data['conn_prd'] = $pdo->select("SELECT p.name,p.product_code,p.id,pcp.order_by FROM prd_connected_products pcp 
                 JOIN prd_main p ON (p.id=pcp.product_id) $apr_str
                 WHERE pcp.connection_id=:connection_id AND pcp.is_deleted='N' AND pcp.order_by > :order_by ORDER BY pcp.order_by ASC",array(':connection_id' => $conn_row['connection_id'],':order_by' => $conn_row['order_by']));
        }
        if(empty($prd_conn_data['conn_prd'])) {
            $prd_conn_data['conn_prd'] = array();
        }
      } else {
        $prd_conn_data = array();
      }
      return $prd_conn_data;
  }

  public function shortTermDisabilityProductDetails($product_id){
    global $pdo;
    $productRes = array();
    if($product_id){
      $productRes = $pdo->selectOne("SELECT is_short_term_disablity_product,monthly_benefit_allowed,percentage_of_salary FROM prd_main WHERE id = :id",array(":id" => $product_id));
    }
    return $productRes;
  }

  public function calculateSTDRate($product_price,$annual_salary,$salary_percentage,$accepted='N',$percentage_of_salary_db=0,$monthly_benefit_manual=0){
    $response = array();
    $monthly_salary = str_replace(',','',$annual_salary) / 12;
    $monthly_benefit = $monthly_salary * $salary_percentage / 100;    
    if($monthly_benefit_manual > 0){
      $monthly_benefit = str_replace(',','',$monthly_benefit_manual);
    }
    $multiplier = $monthly_benefit / 100;
    $plan_cost = $multiplier * $product_price;
    $response['rate'] = round($plan_cost,2);
    $response['monthly_benefit'] = round($monthly_benefit,2);
    // pre_print($monthly_benefit);
    if($percentage_of_salary_db > 0){
      $allowed_benefit_amount = ($monthly_salary * $percentage_of_salary_db) / 100;
      $response['allowed_benefit_amount'] = round($allowed_benefit_amount,2);
    }
    return $response;
  }

  public function calculateSTDPercentage($annual_salary,$monthly_benefit_db){
      $percentage = 0;
      if($annual_salary>0 && $monthly_benefit_db>0){
        $monthly_salary = str_replace(',','',$annual_salary) / 12;
        $percentage = $monthly_benefit_db / $monthly_salary * 100;
        $percentage = floor($percentage * 100) / 100;
        return $percentage;
      }
      return $percentage;
  }

  public function calculateTakeHomePay($params = array(),$product_id,$extra_params = array()){
    global $pdo,$SYMMETRYAPIKEY,$SYMMETRYAPIURL;
    $response = array();
    $voluntaryDeductionsArr = array();
    $getStateCode=$pdo->selectOne("SELECT state_code from zip_code WHERE zip_code=:zip_code",array(":zip_code"=>$params["primary_zip"]));
    $state_name = $getStateCode['state_code'];
    // pre_print($params,false);
    $pre_tax_deductions_arr = array();
    $post_tax_deductions_arr = array();

    $claim_payment = $this->calculateGapClaimPayment($extra_params);
    // $extra_params['with_gap_premium'] = 310;
    // pre_print($claim_payment,false);
    // pre_print($extra_params);

    $rates = array();
    if($params["gap_payroll_type_primary_{$product_id}"] == 'Salary'){
      $grossPayment = str_replace(array('$',','), array('',''),$params["gap_payroll_type_salary_primary_{$product_id}"]);
      $grossPayType = "ANNUALLY";
      $SYMMETRYAPIURL .= "/salary";
    } else {
      //HOURLY
      $hourly_wage = $params["gap_payroll_type_hourly_wage_primary_{$product_id}"];
      $hours = $params["gap_payroll_type_hours_primary_{$product_id}"];
      $grossPayment = 0;
      foreach($hourly_wage as $hourly_key => $hourly_value) {
          $tmp_hourly_wage = str_replace(array('$',','), array('',''),$hourly_wage[$hourly_key]);
          $tmp_hours = str_replace(array('$',','), array('',''),$hours[$hourly_key]);   
          $rates[] =  array("payRate" => $tmp_hourly_wage,"hours" => $tmp_hours);
          $grossPayment += $tmp_hourly_wage * $tmp_hours;
      }   
      $grossPayType = "PAY_PER_PERIOD";
      $SYMMETRYAPIURL .= "/hourly";
    }

    if(isset($params["pre_tax_deductions_primary_{$product_id}"]) && !empty($params["pre_tax_deductions_primary_{$product_id}"])){
      $pre_tax_deduction = json_decode($params["pre_tax_deductions_primary_{$product_id}"],true);
      foreach ($pre_tax_deduction as $key => $value) {
        $tmp_arr = array(
          "deductionName" => $value['deduction_name'],
          "deductionAmount" => str_replace(array('$',','), array('',''),$value['deduction_amount']),
          "deductionMethodType"=> ($value['deduction_method'] == "fixed_amount"?"FIXED_AMOUNT":"PERCENT_OF_GROSS"),
          "exemptFederal"=> "true",
          "exemptFica"=> "true",
          "exemptState"=> "true",
          "exemptLocal"=> "true",
          "benefitType"=> "_Custom",
          "ytdAmount"=> "0"
        );
        $pre_tax_deductions_arr[] = $value['deduction_name'];
        array_push($voluntaryDeductionsArr, $tmp_arr);
      }
    }


    if(isset($params["post_tax_deductions_primary_{$product_id}"]) && !empty($params["post_tax_deductions_primary_{$product_id}"])){
      $post_tax_deduction = json_decode($params["post_tax_deductions_primary_{$product_id}"],true);
      foreach ($post_tax_deduction as $key => $value) {
        $tmp_arr = array(
          "deductionName" => $value['deduction_name'],
          "deductionAmount" => str_replace(array('$',','), array('',''),$value['deduction_amount']),
          "deductionMethodType"=> ($value['deduction_method'] == "fixed_amount"?"FIXED_AMOUNT":"PERCENT_OF_GROSS"),
          "exemptFederal"=> "false",
          "exemptFica"=> "false",
          "exemptState"=> "false",
          "exemptLocal"=> "false",
          "benefitType"=> "_Custom",
          "ytdAmount"=> "0"
        );
        $post_tax_deductions_arr[] = $value['deduction_name'];
        array_push($voluntaryDeductionsArr, $tmp_arr);
      }
    }
    /*"stateInfo"=> array(
          "parms"=> array(
              array(
                  "name"=> "PERCENTSTATE",
                  "value"=> $params["gap_default_allowances_state_primary_{$product_id}"]
              ),
              array(
                  "name"=> "stateExemption",
                  "value"=> ($params["gap_default_allowances_state_primary_{$product_id}"] == 0?"true":"false")
              ),
              array(
                  "name"=> "additionalStateWithholding",
                  "value"=> "0"
              )
          )
      ),*/
    $SymmetryApiParams1 = array(
      "checkDate"=> strtotime('first day of next month'),
      "state"=> $state_name,
      "grossPay"=> $grossPayment,
      "grossPayType"=> $grossPayType,
      "grossPayYTD"=> "0",
      "payFrequency"=> strtoupper($params["gap_pay_frequency_primary_{$product_id}"]),
      "exemptFederal"=> "false",
      "exemptFica"=> "false",
      "exemptMedicare"=> "false",
      "w42020" => "false",
      "federalFilingStatusType2020" => strtoupper($params["gap_marital_status_primary_{$product_id}"]),
      "federalFilingStatusType" => strtoupper($params["gap_marital_status_primary_{$product_id}"]),
      "twoJobs2020"=> "false",
      "federalAllowances"=> round($params["gap_default_allowances_federal_primary_{$product_id}"]),
      "dependents2020"=> "0",
      "otherIncome2020"=> "0",
      "deductions2020"=> "0",
      "additionalFederalWithholding"=> "0",
      "roundFederalWithholding"=> "false",

      "voluntaryDeductions"=> $voluntaryDeductionsArr,

      "otherIncome" => array(),
      "rates"=> $rates,
      "payCodes" => array(),
      "stockOptions" => array(),
      
      "presetDeductions" => array(),
      "presetImputed" => array(),
      "print"=> array(
        "id"=> "",
        "employeeName"=> "",
        "employeeAddressLine1"=> "",
        "employeeAddressLine2"=> "",
        "employeeAddressLine3"=> "",
        "checkNumber"=> "",
        "checkNumberOnCheck"=> "false",
        "checkDate"=> strtotime('first day of next month'),
        "remarks"=> "",
        "companyNameOnCheck"=> "false",
        "companyName"=> "",
        "companyAddressLine1"=> "",
        "companyAddressLine2"=> "",
        "companyAddressLine3"=> "",
        "emailReports"=> "false"
      )
    );
    $SymmetryApiParamsStr = json_encode($SymmetryApiParams1);
    $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => $SYMMETRYAPIURL,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $SymmetryApiParamsStr,
        CURLOPT_HTTPHEADER => array(
            "content-type: application/json",
            "pcc-api-key: ".$SYMMETRYAPIKEY,
        ),
    ));
    $curl_response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    $response = array();
    if(!empty($curl_response)) {
      $curl_response = json_decode($curl_response,true);
      $response['curl_response'] = $curl_response;
      if(isset($curl_response['content'])){
        $gapCalculationRes = $curl_response['content'];

        $federal = $gapCalculationRes['federal'];
        $medicare = $gapCalculationRes['medicare'];
        $fica = $gapCalculationRes['fica'];
        $eic = $gapCalculationRes['eic'];
        $state = $gapCalculationRes['state'];
        $netPay = $gapCalculationRes['netPay'];
        $grossPay = $gapCalculationRes['grossPay'];
        $voluntaryDeductions = $gapCalculationRes['voluntaryDeductions'];
        $pre_tax_deductions_total = 0;
        $post_tax_deductions_total = 0;
        $pre_tax_deductions_line_items_names = '';
        $pre_tax_deductions_line_items_totals = '';
        $post_tax_deductions_line_items_names = '';
        $post_tax_deductions_line_items_totals = '';
        if(!empty($voluntaryDeductions) && is_array($voluntaryDeductions)) {
          foreach($pre_tax_deductions_arr as $ded_name) {
            foreach($voluntaryDeductions as $deduction) {
              if($deduction['name'] == $ded_name) {
                $pre_tax_deductions_total += $deduction['value']; 
                $pre_tax_deductions_line_items_names .= '<p class="p-l-30"><i>'.str_replace("'",'',$deduction['name']).'</i></p>';
                $pre_tax_deductions_line_items_totals .= '<p class="text-danger">'.displayAmount($deduction['value']).'</p>';
              }
            }
          }

          foreach($post_tax_deductions_arr as $ded_name) {
            foreach($voluntaryDeductions as $deduction) {
              if($deduction['name'] == $ded_name) {
                $post_tax_deductions_total += $deduction['value']; 
                $post_tax_deductions_line_items_names .= '<p class="p-l-30"><i>'.str_replace("'",'',$deduction['name']).'</i></p>';
                $post_tax_deductions_line_items_totals .= '<p class="text-danger">'.displayAmount($deduction['value']).'</p>';
              }
            }
          }
        }   

        $gross_income = $grossPay;

        $without_gap_federal_taxes = $federal;
        $without_gap_state_taxes = $state;
        $without_gap_fica = $fica;
        $without_gap_medicare = $medicare;
        $without_gap_premium = 0;
        $without_gap_pre_tax_deductions = $pre_tax_deductions_total;
        $without_gap_post_tax_deductions = $post_tax_deductions_total;
        $without_gap_claim_payment = 0;
        $without_gap_take_home = $grossPay - ($without_gap_federal_taxes + $without_gap_state_taxes + $without_gap_pre_tax_deductions + $without_gap_post_tax_deductions) + $without_gap_claim_payment;

        $with_gap_federal_taxes = $federal;
        $with_gap_state_taxes = $state;
        $with_gap_fica = $fica;
        $with_gap_medicare = $medicare;
        $with_gap_premium = $extra_params['with_gap_premium']; //Product Price
        $with_gap_pre_tax_deductions = $pre_tax_deductions_total;
        $with_gap_post_tax_deductions = $post_tax_deductions_total;
        $with_gap_claim_payment = $claim_payment; //Need to calculate
        $with_gap_take_home = $grossPay - ($with_gap_federal_taxes + $with_gap_state_taxes + $with_gap_pre_tax_deductions + $with_gap_post_tax_deductions) + $with_gap_claim_payment;

        $response['calculation_data'] = array(
          'gross_income' => displayAmount($gross_income,2),
          'pre_tax_deductions_total' => displayAmount($pre_tax_deductions_total,2),
          'post_tax_deductions_total' => displayAmount($post_tax_deductions_total,2),

          'without_gap_federal_taxes' => displayAmount($without_gap_federal_taxes,2), 
          'with_gap_federal_taxes' => displayAmount($with_gap_federal_taxes,2), 

          'without_gap_state_taxes' => displayAmount($without_gap_state_taxes,2), 
          'with_gap_state_taxes' => displayAmount($with_gap_state_taxes,2), 

          'without_gap_fica' => displayAmount($without_gap_fica,2), 
          'with_gap_fica' => displayAmount($with_gap_fica,2), 

          'without_gap_medicare' => displayAmount($without_gap_medicare,2), 
          'with_gap_medicare' => displayAmount($with_gap_medicare,2), 

          'without_gap_pre_tax_deductions' => displayAmount($without_gap_pre_tax_deductions,2), 
          'with_gap_pre_tax_deductions' => displayAmount($with_gap_pre_tax_deductions,2), 

          'without_gap_premium' => displayAmount($without_gap_premium,2), 
          'with_gap_premium' => displayAmount($with_gap_premium,2), 

          'without_gap_post_tax_deductions' => displayAmount($without_gap_post_tax_deductions,2), 
          'with_gap_post_tax_deductions' => displayAmount($with_gap_post_tax_deductions,2),

          'without_gap_claim_payment' => displayAmount($without_gap_claim_payment,2), 
          'with_gap_claim_payment' => displayAmount($with_gap_claim_payment,2), 
          
          'without_gap_take_home' => displayAmount($without_gap_take_home,2), 
          'with_gap_take_home' => displayAmount($with_gap_take_home,2), 

          'pre_tax_deductions_line_items_names' => $pre_tax_deductions_line_items_names, 
          'pre_tax_deductions_line_items_totals' => $pre_tax_deductions_line_items_totals, 
          'post_tax_deductions_line_items_names' => $post_tax_deductions_line_items_names, 
          'post_tax_deductions_line_items_totals' => $post_tax_deductions_line_items_totals, 
        );
      }
    }

      // pre_print($extra_params['with_gap_premium']);
    if($extra_params['with_gap_premium'] > 0){

      $tmp_arr = array(
        "deductionName" => "GAP Premium",
        "deductionAmount" => str_replace(array('$',','), array('',''),$extra_params['with_gap_premium']),
        "deductionMethodType"=> "FIXED_AMOUNT",
        "exemptFederal"=> "true",
        "exemptFica"=> "true",
        "exemptState"=> "true",
        "exemptLocal"=> "true",
        "benefitType"=> "_Custom",
        "ytdAmount"=> "0"
      );
      $pre_tax_deductions_arr[] = $tmp_arr['deductionName'];
      array_push($voluntaryDeductionsArr, $tmp_arr);
      // pre_print($voluntaryDeductionsArr);

      $SymmetryApiParams2 = array(
        "checkDate"=> strtotime('first day of next month'),
        "state"=> $state_name,
        "grossPay"=> $grossPayment,
        "grossPayType"=> $grossPayType,
        "grossPayYTD"=> "0",
        "payFrequency"=> strtoupper($params["gap_pay_frequency_primary_{$product_id}"]),
        "exemptFederal"=> "false",
        "exemptFica"=> "false",
        "exemptMedicare"=> "false",
        "w42020" => "false",
        "federalFilingStatusType2020" => strtoupper($params["gap_marital_status_primary_{$product_id}"]),
        "federalFilingStatusType" => strtoupper($params["gap_marital_status_primary_{$product_id}"]),
        "twoJobs2020"=> "false",
        "federalAllowances"=> round($params["gap_default_allowances_federal_primary_{$product_id}"]),
        "dependents2020"=> "0",
        "otherIncome2020"=> "0",
        "deductions2020"=> "0",
        "additionalFederalWithholding"=> "0",
        "roundFederalWithholding"=> "false",

        "voluntaryDeductions"=> $voluntaryDeductionsArr,

        "otherIncome" => array(),
        "rates"=> $rates,
        "payCodes" => array(),
        "stockOptions" => array(),
        "presetDeductions" => array(),
        "presetImputed" => array(),
        "print"=> array(
          "id"=> "",
          "employeeName"=> "",
          "employeeAddressLine1"=> "",
          "employeeAddressLine2"=> "",
          "employeeAddressLine3"=> "",
          "checkNumber"=> "",
          "checkNumberOnCheck"=> "false",
          "checkDate"=> strtotime('first day of next month'),
          "remarks"=> "",
          "companyNameOnCheck"=> "false",
          "companyName"=> "",
          "companyAddressLine1"=> "",
          "companyAddressLine2"=> "",
          "companyAddressLine3"=> "",
          "emailReports"=> "false"
        )
      );

      // pre_print($SymmetryApiParams);

      $SymmetryApiParamsStr = json_encode($SymmetryApiParams2);
      // pre_print($SymmetryApiParamsStr);
      $curl2 = curl_init();
        curl_setopt_array($curl2, array(
          CURLOPT_URL => $SYMMETRYAPIURL,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_TIMEOUT => 10,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $SymmetryApiParamsStr,
          CURLOPT_HTTPHEADER => array(
              "content-type: application/json",
              "pcc-api-key: ".$SYMMETRYAPIKEY,
          ),
      ));
      $curl_response2 = curl_exec($curl2);
      $err2 = curl_error($curl2);
      curl_close($curl2);

      if(!empty($curl_response2)) {
        $curl_response2 = json_decode($curl_response2,true);
        $response['curl_response2'] = $curl_response2;
        if(isset($curl_response2['content'])){
          $gapCalculationRes = $curl_response2['content'];

          $federal = $gapCalculationRes['federal'];
          $medicare = $gapCalculationRes['medicare'];
          $fica = $gapCalculationRes['fica'];
          $eic = $gapCalculationRes['eic'];
          $state = $gapCalculationRes['state'];
          $netPay = $gapCalculationRes['netPay'];
          $grossPay = $gapCalculationRes['grossPay'];
          $voluntaryDeductions = $gapCalculationRes['voluntaryDeductions'];
          $pre_tax_deductions_total = 0;
          $post_tax_deductions_total = 0;
          $pre_tax_deductions_line_items_names = '';
          $pre_tax_deductions_line_items_totals = '';
          $post_tax_deductions_line_items_names = '';
          $post_tax_deductions_line_items_totals = '';
          if(!empty($voluntaryDeductions) && is_array($voluntaryDeductions)) {
            foreach($pre_tax_deductions_arr as $ded_name) {
              foreach($voluntaryDeductions as $deduction) {
                if($deduction['name'] == $ded_name) {
                  $pre_tax_deductions_total += $deduction['value'];
                  if($deduction['name'] == 'GAP Premium'){
                    continue;
                  } 
                  $pre_tax_deductions_line_items_names .= '<p class="p-l-30"><i>'.str_replace("'",'',$deduction['name']).'</i></p>';
                  $pre_tax_deductions_line_items_totals .= '<p class="text-danger">'.displayAmount($deduction['value']).'</p>';
                }
              }
            }

            foreach($post_tax_deductions_arr as $ded_name) {
              foreach($voluntaryDeductions as $deduction) {
                if($deduction['name'] == $ded_name) {
                  $post_tax_deductions_total += $deduction['value']; 
                  $post_tax_deductions_line_items_names .= '<p class="p-l-30"><i>'.str_replace("'",'',$deduction['name']).'</i></p>';
                  $post_tax_deductions_line_items_totals .= '<p class="text-danger">'.displayAmount($deduction['value']).'</p>';
                }
              }
            }
          }   

          $gross_income = $grossPay;

          $with_gap_federal_taxes = $federal;
          $with_gap_state_taxes = $state;
          $with_gap_fica = $fica;
          $with_gap_medicare = $medicare;
          $with_gap_premium = $extra_params['with_gap_premium']; //Product Price
          $with_gap_pre_tax_deductions = $pre_tax_deductions_total;
          $with_gap_post_tax_deductions = $post_tax_deductions_total;
          $with_gap_claim_payment = $claim_payment; //Need to calculate
          $with_gap_take_home = $grossPay - ($with_gap_federal_taxes + $with_gap_state_taxes + $with_gap_pre_tax_deductions + $with_gap_post_tax_deductions) + $with_gap_claim_payment;

          
          $response['calculation_data']['with_gap_federal_taxes'] = displayAmount($with_gap_federal_taxes,2);
          $response['calculation_data']['with_gap_state_taxes'] = displayAmount($with_gap_state_taxes,2);
          $response['calculation_data']['with_gap_fica'] = displayAmount($with_gap_fica,2);
          $response['calculation_data']['with_gap_medicare'] = displayAmount($with_gap_medicare,2);
          $response['calculation_data']['with_gap_pre_tax_deductions'] = displayAmount($with_gap_pre_tax_deductions,2);
          $response['calculation_data']['with_gap_premium'] = displayAmount($with_gap_premium,2);
          $response['calculation_data']['with_gap_post_tax_deductions'] = displayAmount($with_gap_post_tax_deductions,2);
          $response['calculation_data']['with_gap_claim_payment'] = displayAmount($with_gap_claim_payment,2);
          $response['calculation_data']['with_gap_take_home'] = displayAmount($with_gap_take_home,2);

          $response['calculation_data']['pre_tax_deductions_line_items_names'] = $pre_tax_deductions_line_items_names; 
          $response['calculation_data']['pre_tax_deductions_line_items_totals'] = $pre_tax_deductions_line_items_totals; 
          $response['calculation_data']['post_tax_deductions_line_items_names'] = $post_tax_deductions_line_items_names;
          $response['calculation_data']['post_tax_deductions_line_items_totals'] = $post_tax_deductions_line_items_totals;
        }
      }

    }


    $response['voluntaryDeductions'] = $voluntaryDeductions;
    $response['extra_params'] = $extra_params;
    return $response;
  }

  public function calculateGapClaimPayment($params){
    $rate = 0;
    if($params){
      $plan_id = $params['plan_type'];
      $pay_frequency = $params['member_pay_frequency'];
      $price = $params['annual_hrm_payment'][$plan_id];

      if($pay_frequency == 'WEEKLY'){
        $rate = $price / 52;
      }else if($pay_frequency == 'SEMI_MONTHLY'){
        $rate = $price / 24;
      }else if($pay_frequency == 'MONTHLY'){
        $rate = $price / 12;
      }else if($pay_frequency == 'BI_WEEKLY'){
        $rate = $price / 26;
      }else if($pay_frequency == 'DAILY'){
        $rate = $price/365;
      }else if($pay_frequency == 'QUARTERLY'){
        $rate = $price / 3;
      }else if($pay_frequency == 'SEMI_ANNUAL'){
        $rate = $price / 2;
      }else if($pay_frequency == 'ANNUAL'){
        $rate = $price;
      }
    }
    return round($rate,2);
  }
  public function getAcaProducts($premium_products){
    global $pdo;
    $acaProductsArr = [];
    if (!empty($premium_products)) {
      foreach ($premium_products as $productKey => $productVal) {
        if (isset($productVal['is_aca_product']) && $productVal['is_aca_product']=='Y') {
           $acaProductsArr[] = $productVal;
        }
      }
    }
    return $acaProductsArr;
  }
  function getMinMaxCostlyProduct($costTye="",$prd_matrix=array(),$costText=""){
    global $pdo;
    if($prd_matrix){
      $orderby = "ASC";
      if($costTye == 'least_expensive'){
        $orderby = "ASC";
      }else if($costTye == 'most_expensive'){
        $orderby = "DESC";
      }

      $prd_ids = array_keys($prd_matrix);
      $price = $pdo->selectOne("SELECT pm.product_id 
                    FROM prd_matrix pm
                    JOIN prd_main p on(p.id = pm.product_id) 
                    where pm.product_id in(".implode(',', $prd_ids).") AND pm.plan_type=1 AND pm.is_deleted = 'N' order by pm.price $orderby");
      if($price){
          $price2 = $pdo->selectOne("SELECT pm.price,p.name 
                    FROM prd_matrix pm
                    JOIN prd_main p on(p.id = pm.product_id) 
                    where pm.product_id in(".$price['product_id'].") AND pm.plan_type=1 AND pm.is_deleted = 'N' order by pm.price ASC");
          if($price2) {
              return array("saving_prd_price" => displayAmount($price2['price'],2),"saving_prd_name" => $price2['name']);
          }
      }
    }
    return array();
  }
}
