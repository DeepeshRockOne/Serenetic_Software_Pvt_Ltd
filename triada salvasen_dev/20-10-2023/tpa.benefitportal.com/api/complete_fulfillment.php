<?php
  $fulfill_sql = "SELECT 
          c.rep_id,c.fname,c.mname,c.lname,c.birth_date,c.gender,AES_DECRYPT(c.ssn,'" . $CREDIT_CARD_ENC_KEY . "')as ssn,c.email,c.cell_phone,c.address,c.address_2,sc.name as state,sc.short_name,c.city,c.zip,
          ws.id as ws_id,ws.prd_plan_type_id as plan_type,ws.eligibility_date,
          ce.id as ce_id,DATE(ws.created_at) as created_at,ce.is_fulfillment,
          s.rep_id as sponsor_rep_id,
          p.product_code,p.id as productId,
          ppt.title as plan_type_title,
          pc.title as product_category,
          car.name as carrier_name,car.display_id as carrierDispId

          FROM website_subscriptions as ws
          JOIN customer_enrollment as ce ON(ws.id=ce.website_id)
          JOIN customer as c ON(c.id=ws.customer_id AND c.is_deleted='N')
          JOIN customer as s ON(s.id=c.sponsor_id AND s.is_deleted='N')
          JOIN states_c as sc ON((sc.name=c.state OR sc.short_name=c.state) AND sc.country_id = '231')
          JOIN prd_main as p ON(p.id=ws.product_id)
          JOIN prd_category as pc ON(pc.id=p.category_id)
          JOIN prd_plan_type as ppt ON(ws.prd_plan_type_id=ppt.id)
          JOIN prd_fees as car ON(car.id=p.carrier_id AND car.setting_type='Carrier')
          WHERE ws.id=:ws_id AND ws.status IN ('Active','Pending') 
          AND c.type='Customer' AND c.status='Active' 
          GROUP BY ws.id ORDER BY ws.id ASC";
  $fulfill_row = $pdo->selectOne($fulfill_sql,array(":ws_id"=>$fulfillment_id));

  if(count($fulfill_row) > 0) {
      $row = $fulfill_row;

      if($row["is_fulfillment"] == "Y") {
          $response = array(
              'success' => $fail_value,
              'message' => 'This fulfillment is already processed',
          );
          return_response($fail_value,$response);
      } 

      $update_data = array(
          'is_fulfillment' => 'Y',
          'fulfillment_date' => 'msqlfunc_NOW()',
      );
      $where_data = array(
          "clause"=>"id=:id",
          "params"=>array(
              ":id" => $row["ce_id"]
          )
      );
      $pdo->update("customer_enrollment", $update_data,$where_data);

      /*-------- Fetch Dependent Detail ---------*/
      $dependents = array();
      $dependents[] = array(
        'memberId' => $row['rep_id'],
        'relationship' => "Employee",
        'firstName' => $row['fname'],
        'middleName' => $row['mname'],
        'lastName' => $row['lname'],
        'dateOfBirth' => $row['birth_date'],
        'ssn' => str_replace('_','',$row['ssn']),
        'gender' => $row['gender'], 
      );

      if($row['plan_type'] > 1) {
        $dependent_sql = "SELECT 
                cp.id,cp.display_id,cp.relation,cp.fname,cp.mname,cp.lname,cp.birth_date,cp.ssn,cp.gender
                FROM  customer_dependent_profile cp 
                JOIN customer_dependent cd ON(cd.cd_profile_id = cp.id and cd.is_deleted='N') 
                WHERE cp.is_deleted='N' AND
                cd.website_id=:ws_id GROUP BY cp.display_id";
        $dependent_params = array(':ws_id' => $row['ws_id']);
        $dependent_res = $pdo->select($dependent_sql,$dependent_params);

        if(count($dependent_res) > 0) {
          foreach ($dependent_res as $key => $value) {
            $dependents[] = array(
              'dependentId' => $value['display_id'],
              'relationship' => $value['relation'],
              'firstName' => valid_csv_cell_value($value['fname']),
              'middleName' => valid_csv_cell_value($value['mname']),
              'lastName' => valid_csv_cell_value($value['lname']),
              'dateOfBirth' => $value['birth_date'],
              'ssn' => str_replace('_','',$value['ssn']),
              'gender' => $value['gender'],
            );
          }
        }
      }

      $data = array(
          'id' => $row['ws_id'],
          'memberId' => $row['rep_id'],
          'agentId' => $row['sponsor_rep_id'],
          'email' => $row['email'],
          'phone' => str_replace(array("(",")","-"," "),array("","","",""),$row['cell_phone']),
          'productId' => $row['product_code'],
          'productBenefit' => $row['plan_type_title'],
          'productCategory' => $row['product_category'],
          'carrierId' => $row['carrierDispId'],
          'carrierName' => $row['carrier_name'],
          'enrollmentDate' => $row['created_at'],
          'effectiveDate' => $row['eligibility_date'],
          'fulfillmentStatus' => 'Completed',
          'fulfillmentDate' => date('Y-m-d'),
          'productCodes' => get_product_codes($row['productId']),
          'residenceAddress' => array(
            'line1' => $row['address'],
            'line2' => $row['address_2'],
            'city' => $row['city'],
            'state' => $row['short_name'],
            'zip' => $row['zip'],
          ),
          'participants' => $dependents,
      );

      $response = array(
          'success' => $success_value,
          'message' => 'Fulfillment completed',
          'data' => $data,
      );
      return_response($success_value,$response);
  } else {
      $response = array(
          'success' => $fail_value,
          'message' => 'Extract has no fulfillment',
      );
      return_response($fail_value,$response);
  }
?>