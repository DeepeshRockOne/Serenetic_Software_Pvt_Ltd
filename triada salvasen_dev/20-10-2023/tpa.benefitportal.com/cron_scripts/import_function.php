<?php
function get_available_products($params)
{
    global $pdo,$ALLOWED_SUBSCRIPTION_STATUS,$prdPlanTypeArray;
    include_once dirname(__DIR__) . '/includes/member_enrollment.class.php';
    $MemberEnrollment = new MemberEnrollment();

    $response = array();

    $enrollmentLocation = !empty($params['enrollmentLocation']) ? $params['enrollmentLocation'] : '';
    // $pb_id = !empty($params['pb_id']) ? $params['pb_id'] : 0;
    $allowedSubscriptionStatus = implode("','", $ALLOWED_SUBSCRIPTION_STATUS);

    $today_date = date('Y-m-d');
    $sponsor_id = $params['sponsor_id'];
    $primary_fname = !empty($params['primary_fname']) ? $params['primary_fname'] : '';
    $primary_zip = !empty($params['primary_zip']) ? $params['primary_zip'] : '';
    $primary_gender = !empty($params['primary_gender']) ? $params['primary_gender'] : '';
    $primary_birthdate = !empty($params['primary_birthdate']) ? $params['primary_birthdate'] : '';
    $primary_email = !empty($params['primary_email']) ? $params['primary_email'] : '';
    $customer_id = !empty($params['customer_id']) ? $params['customer_id'] : 0;
    $lead_id = !empty($params['lead_id']) ? $params['lead_id'] : 0;

    $spouse_dependent = isset($params['spouse_fname']) ? $params['spouse_fname'] : NULL;
    $child_dependent = isset($params['tmp_child_fname']) ? $params['tmp_child_fname'] : array();

    $already_puchase_product = array();
    $inactive_products = array();
    $restricted_products = array();
    $rule_error_array = array();
    $combination_products = array();

    $found_state_id = 0;

    if ($params)
    {

        $incr = "";
        $incr .= " AND (pm.pricing_effective_date <= :today_date AND (pm.pricing_termination_date >= :today_date OR pm.pricing_termination_date is null))";
        $sch_params[":today_date"] = $today_date;

        $incr .= " AND apr.agent_id = :agent_id";
        $sch_params[":agent_id"] = $sponsor_id;

        if (!empty($customer_id))
        {
            //************** Already Purchase Product will  not display **************
            $sqlWebsite = "SELECT w.id,w.product_id,w.plan_id,w.status,w.termination_date,p.type as product_type,p.reenroll_options,p.reenroll_within,p.reenroll_within_type 
							FROM website_subscriptions w 
							JOIN prd_main p ON (p.id = w.product_id)
							where w.customer_id = :customer_id AND w.status IN('" . $allowedSubscriptionStatus . "')";
            $resWebsite = $pdo->select($sqlWebsite, array(
                ":customer_id" => $customer_id
            ));

            if (!empty($resWebsite))
            {
                foreach ($resWebsite as $key => $productRow)
                {
                    if ($productRow['reenroll_options'] == 'Available After Specific Time Frame')
                    {
                        $termination_date = $productRow['termination_date'];

                        if (isset($termination_date) && !empty($termination_date))
                        {

                            $currentDateTime = new DateTime();
                            $dateTimeInTheFuture = new DateTime($termination_date);
                            $dateInterval = $dateTimeInTheFuture->diff($currentDateTime);

                            $is_reenroll_option_within = false;
                            if ($productRow['reenroll_within_type'] == 'Days')
                            {
                                if ($dateInterval->days >= $productRow['reenroll_within'])
                                {
                                    $is_reenroll_option_within = true;
                                }
                            }
                            elseif ($productRow['reenroll_within_type'] == 'Weeks')
                            {
                                if (($dateInterval->days / 7) >= $productRow['reenroll_within'])
                                {
                                    $is_reenroll_option_within = true;
                                }
                            }
                            elseif ($productRow['reenroll_within_type'] == 'Months')
                            {
                                $totalMonths = 0;
                                if (!empty($dateInterval->y))
                                {
                                    $totalMonths = $dateInterval->y * 12;
                                }
                                $totalMonths = $totalMonths + $dateInterval->m;
                                if ($totalMonths >= $productRow['reenroll_within'])
                                {
                                    $is_reenroll_option_within = true;
                                }
                            }
                            elseif ($productRow['reenroll_within_type'] == 'Years')
                            {
                                if ($dateInterval->y >= $productRow['reenroll_within'])
                                {
                                    $is_reenroll_option_within = true;
                                }
                            }

                            if (strtotime(date('Y-m-d')) < strtotime($termination_date))
                            {
                                $is_reenroll_option_within = false;
                            }

                            if (!$is_reenroll_option_within)
                            {
                                array_push($already_puchase_product, $productRow['product_id']);
                            }
                        }
                        else
                        {
                            array_push($already_puchase_product, $productRow['product_id']);
                        }
                    }
                    else
                    {
                        array_push($already_puchase_product, $productRow['product_id']);
                    }
                }
            }
            //************** Already Purchase Product will  not display **************
            //******** Already Purchase Products Restricted Product will  not display ********
            if (!empty($already_puchase_product))
            {
                foreach ($already_puchase_product as $key => $productRow)
                {

                    $sqlProduct = "SELECT combination_product_id as restricted_products FROM prd_combination_rule WHERE product_id=:product_id AND combination_type='Excludes' AND is_deleted='N'";
                    $resProduct = $pdo->select($sqlProduct, array(
                        ":product_id" => $productRow
                    ));

                    if (!empty($resProduct))
                    {
                        foreach ($resProduct as $key => $row)
                        {
                            array_push($restricted_products, $row['restricted_products']);
                        }
                    }

                }
            }
            //******** Already Purchase Products Restricted Product will  not display ********
            if (count($already_puchase_product) > 0)
            {
                $list_already_puchase_product = implode(",", $already_puchase_product);
                $incr .= " AND p.id not in ($list_already_puchase_product)";
                $response['already_puchase_product'] = $already_puchase_product;
            }

            if (count($restricted_products) > 0)
            {
                $list_restricted_products = implode(",", $restricted_products);
                $incr .= " AND p.id not in ($list_restricted_products)";
                $response['restricted_products'] = $restricted_products;
            }
        }

        if ($enrollmentLocation == "agentSide")
        {
            $incr .= " AND p.product_type in ('Direct Sale Product','Add On Only Product')";
        }
        else if ($enrollmentLocation == "groupSide")
        {
            $incr .= " AND p.product_type in ('Group Enrollment','Add On Only Product')";
        }
        else if ($enrollmentLocation == "adminSide")
        {
            $incr .= " AND p.product_type in ('Admin Only Product','Add On Only Product')";
        }

        if (!empty($pb_id))
        {
            $pb_sql = "SELECT pg.product_ids
	            FROM page_builder pg 
	            LEFT JOIN page_builder_images pi ON (pi.id = pg.cover_image) 
	            WHERE pg.is_deleted='N' AND pg.status='Active' AND pg.id=:id";
            $pb_row = $pdo->selectOne($pb_sql, array(
                ":id" => $pb_id
            ));

            if (!empty($pb_row['product_ids']))
            {
                $incr .= " AND p.id IN (" . $pb_row['product_ids'] . ")";
            }
        }

        $productsSql = "SELECT p.id as p_product_id,p.name as product_name,p.company_id as product_company,p.parent_product_id as primary_product_id,p.product_code,p.is_license_require,p.is_primary_age_restrictions,p.primary_age_restrictions_from,p.primary_age_restrictions_to,p.is_specific_zipcode,
			p.type as product_type,p.is_add_on_product,p.license_type,p.license_rule,p.reenroll_options,p.reenroll_within,p.reenroll_within_type,p.family_plan_rule, pmc.*,
			pm.id as matrix_id,pm.plan_type,pm.enrollee_type,pm.pricing_model,pm.pricing_effective_date,pm.pricing_termination_date,pm.price,pm.commission_amount,pm.non_commission_amount,
			if(p.is_add_on_product='Y','Add-On Only',pc.title) as category_name,
			if(p.is_add_on_product='Y','addOnCategory',p.category_id) as product_category_id,
			pf.name as carrier_name,pd.agent_portal as product_detail
			FROM prd_main p
			JOIN prd_matrix pm on (p.id = pm.product_id AND pm.is_deleted='N')
			LEFT JOIN prd_matrix_criteria pmc ON (pmc.prd_matrix_id = pm.id AND pmc.is_deleted='N')
			JOIN agent_product_rule apr ON (p.id=apr.product_id AND apr.status ='Contracted' AND apr.is_deleted='N')
			JOIN prd_category pc ON (pc.id=p.category_id)
			JOIN prd_fees pf ON (pf.id = p.carrier_id)
			LEFT JOIN prd_descriptions pd ON (pd.product_id = p.id)
			WHERE p.status='Active' AND p.type!='Fees' AND p.is_deleted='N' $incr order by category_name,p.order_by ASC";
        $productsRes = $pdo->select($productsSql, $sch_params);

        $licenseFullExpired = $MemberEnrollment->isFullLicenseExpired($sponsor_id);

        $product_list = array();
        if (!empty($productsRes))
        {

            $getStateCode = $pdo->selectOne("SELECT state_code from zip_code WHERE zip_code=:zip_code", array(
                ":zip_code" => $primary_zip
            ));
            if ($getStateCode)
            {
                $pricing_control_State = getname("states_c", $getStateCode['state_code'], "name", "short_name");
            }

            foreach ($productsRes as $key => $product_row)
            {

                $is_rule_valid = true;
                $rule_error = "";

                $assignedQuestion = $MemberEnrollment->getPriceAssignedQuestion($product_row['p_product_id']);

                $checkExtraQuestion = array();
                if (!empty($assignedQuestion) && !empty($assignedQuestion['Primary']))
                {
                    $keys = array_keys($assignedQuestion['Primary']);
                    $checkExtraQuestion = array_fill_keys($keys, '');
                    unset($checkExtraQuestion[1]);
                    unset($checkExtraQuestion[2]);
                    unset($checkExtraQuestion[3]);
                    unset($checkExtraQuestion[4]);
                }

                //**************************** License Rule Code Start **********************
                if ($product_row['is_license_require'] == 'Y' && ($enrollmentLocation != "groupSide" && $is_group_member == 'N'))
                {
                    $getActiveLicensedStates = $MemberEnrollment->getActiveLicensedStates($sponsor_id, $product_row['license_type']);

                    if ($product_row['license_rule'] == 'Licensed Only')
                    {
                        if (empty($getActiveLicensedStates))
                        {
                            $is_rule_valid = false;
                            $rule_error = "Licensed Only Error";
                        }
                    }
                    else if ($product_row['license_rule'] == 'Licensed in Sale State')
                    {
                        if (!empty($pricing_control_State))
                        {
                            if (!in_array($pricing_control_State, $getActiveLicensedStates))
                            {
                                $is_rule_valid = false;
                                $rule_error = "Licensed in Sale State Error";
                            }
                        }
                        else
                        {
                            $is_rule_valid = false;
                            $rule_error = "License Error";
                        }
                    }
                    else if ($product_row['license_rule'] == 'Licensed and Appointed')
                    {
                        if (!empty($pricing_control_State))
                        {
                            if (!in_array($pricing_control_State, $getActiveLicensedStates))
                            {
                                $is_rule_valid = false;
                                $rule_error = "Licensed and Appointed Error";
                            }
                        }
                        else
                        {
                            $is_rule_valid = false;
                            $rule_error = "License Error";
                        }

                        $getProductLicense = $MemberEnrollment->getProductLicensedStates($product_row['p_product_id'], $product_row['license_rule']);

                        $licensed_in_PreSale = !(empty($getProductLicense)) ? $getProductLicense['Pre-Sale'] : array();

                        if (!empty($licensed_in_PreSale) && in_array($pricing_control_State, $licensed_in_PreSale))
                        {
                            $sqlSetting = "SELECT aws.state FROM agent_writing_number awn
								JOIN agent_writing_states aws ON (awn.id = aws.writing_id AND aws.is_deleted='N')
								WHERE awn.is_deleted='N' and awn.agent_id=:agent_id AND aws.state=:state_name";
                            $resSetting = $pdo->select($sqlSetting, array(
                                ":agent_id" => $sponsor_id,
                                ":state_name" => $pricing_control_State
                            ));

                            if (empty($resSetting))
                            {
                                $is_rule_valid = false;
                                $rule_error = "is_writing_number Error";
                            }
                        }
                    }
                    else if ($product_row['license_rule'] == 'Licensed in Specific States Only')
                    {

                        $getProductLicense = $MemberEnrollment->getProductLicensedStates($product_row['p_product_id'], $product_row['license_rule']);

                        $licensed_in_specific_state = !(empty($getProductLicense)) ? $getProductLicense['Specific'] : array();

                        $isLicenseInSpecific = array();
                        if (!empty($licensed_in_specific_state))
                        {
                            foreach ($licensed_in_specific_state as $lKey => $lValue)
                            {
                                if (in_array($lValue, $getActiveLicensedStates))
                                {
                                    array_push($isLicenseInSpecific, "true");
                                }
                                else
                                {
                                    array_push($isLicenseInSpecific, "false");
                                }
                            }
                        }
                        else
                        {
                            array_push($isLicenseInSpecific, "false");
                        }

                        if (!in_array("true", $isLicenseInSpecific))
                        {
                            $is_rule_valid = false;
                            $rule_error = "Licensed in Specific State Error";
                        }
                    }
                }
                //**************************** License Rule Code End   **********************
                //**************************** Age Rule Code Start **********************
                if (!empty($primary_birthdate))
                {
                    $age_from_birthdate = calculateAge($primary_birthdate);
                    if (in_array($product_row['pricing_model'], array(
                        'VariableEnrollee',
                        'VariablePrice'
                    )) && !empty($assignedQuestion['Primary']) && array_key_exists(1, $assignedQuestion['Primary']))
                    {
                        if ($product_row['pricing_model'] == "VariablePrice" && $product_row['age_from'] >= 0 && $product_row['age_to'] > 0 && ($product_row['age_from'] > $age_from_birthdate || $product_row['age_to'] < $age_from_birthdate))
                        {
                            $is_rule_valid = false;
                            $rule_error = "Age Error";
                        }
                        if ($product_row['pricing_model'] == "VariableEnrollee" && $product_row['enrollee_type'] == "Primary" && $product_row['age_from'] >= 0 && $product_row['age_to'] > 0 && ($product_row['age_from'] > $age_from_birthdate || $product_row['age_to'] < $age_from_birthdate))
                        {
                            $is_rule_valid = false;
                            $rule_error = "Age Error";
                        }
                    }
                    if ($product_row['is_primary_age_restrictions'] == 'Y' && ($product_row['primary_age_restrictions_from'] > $age_from_birthdate || $product_row['primary_age_restrictions_to'] < $age_from_birthdate))
                    {
                        $is_rule_valid = false;
                        $rule_error = "is_primary_age_restrictions Error";
                    }
                }
                //**************************** Age Rule Code End   **********************
                //**************************** state Rule Code Start **********************
                if (!empty($pricing_control_State))
                {
                    if (in_array($product_row['pricing_model'], array(
                        'VariableEnrollee',
                        'VariablePrice'
                    )) && !empty($assignedQuestion['Primary']) && array_key_exists(2, $assignedQuestion['Primary']))
                    {
                        if ($product_row['pricing_model'] == "VariablePrice" && $product_row['state'] != "" && $product_row['state'] != $pricing_control_State)
                        {
                            $is_rule_valid = false;
                            $rule_error = "state Error";
                        }
                        if ($product_row['pricing_model'] == "VariableEnrollee" && $product_row['enrollee_type'] == "Primary" && $product_row['state'] != "" && $product_row['state'] != $pricing_control_State)
                        {
                            $is_rule_valid = false;
                            $rule_error = "state Error";
                        }
                    }

                    $restricted_state_date = date('Y-m-d');

                    $restrictedStateSql = "SELECT GROUP_CONCAT(distinct product_id) as restrictedStateProduct FROM prd_no_sale_states WHERE state_name=:state AND is_deleted='N' AND effective_date <= :restricted_state_date AND (termination_date >= :restricted_state_date OR termination_date IS NULL) AND product_id = :product_id";
                    $restrictedStateRes = $pdo->selectOne($restrictedStateSql, array(
                        ":state" => $pricing_control_State,
                        ":restricted_state_date" => $restricted_state_date,
                        ':product_id' => $product_row['p_product_id']
                    ));

                    if (!empty($restrictedStateRes['restrictedStateProduct']))
                    {

                        $restrictedStateArray = explode(",", $restrictedStateRes['restrictedStateProduct']);

                        if (in_array($product_row['p_product_id'], $restrictedStateArray))
                        {
                            $is_rule_valid = false;
                            $rule_error = "restricted state Error";
                        }
                    }

                    $sqlAgentState = "SELECT id FROM agent_assign_state where agent_id = :sponsor_id AND product_id=:product_id AND state=:state AND is_deleted='N'";
                    $resAgentState = $pdo->selectOne($sqlAgentState, array(
                        ":state" => $pricing_control_State,
                        ":product_id" => $product_row['p_product_id'],
                        ":sponsor_id" => $sponsor_id
                    ));

                    if (!empty($resAgentState))
                    {
                        $is_rule_valid = false;
                        $rule_error = "restricted agent state Error";
                    }
                }
                //**************************** state Rule Code End   **********************
                //**************************** zip code Rule Code Start **********************
                if (!empty($primary_zip))
                {
                    if (in_array($product_row['pricing_model'], array(
                        'VariableEnrollee',
                        'VariablePrice'
                    )) && !empty($assignedQuestion['Primary']) && array_key_exists(3, $assignedQuestion['Primary']))
                    {

                        if ($product_row['pricing_model'] == "VariablePrice" && $product_row['zipcode'] != "" && $product_row['zipcode'] != $primary_zip)
                        {
                            $is_rule_valid = false;
                            $rule_error = "Zip Error";
                        }
                        if ($product_row['pricing_model'] == "VariableEnrollee" && $product_row['enrollee_type'] == "Primary" && $product_row['zipcode'] != "" && $product_row['zipcode'] != $primary_zip)
                        {
                            $is_rule_valid = false;
                            $rule_error = "Zip Error";
                        }
                    }

                    if ($product_row['is_specific_zipcode'] == 'Y')
                    {
                        $zipCodeSql = "SELECT id FROM prd_specific_zipcode WHERE zipcode=:zipcode AND product_id =:product_id AND is_deleted='N'";
                        $zipCodeRes = $pdo->select($zipCodeSql, array(
                            ":zipcode" => $primary_zip,
                            ":product_id" => $product_row['p_product_id']
                        ));

                        if (empty($zipCodeRes))
                        {
                            $is_rule_valid = false;
                            $rule_error = "Specific Zip Error";
                        }
                    }
                }
                //**************************** zip code Rule Code End   **********************
                //**************************** Gender Rule Code start **********************
                if (!empty($primary_gender))
                {
                    if (in_array($product_row['pricing_model'], array(
                        'VariableEnrollee',
                        'VariablePrice'
                    )) && !empty($assignedQuestion['Primary']) && array_key_exists(4, $assignedQuestion['Primary']))
                    {
                        if ($product_row['pricing_model'] == "VariablePrice" && $product_row['gender'] != "" && $product_row['gender'] != $primary_gender)
                        {
                            $is_rule_valid = false;
                            $rule_error = "Gender Error";
                        }
                        if ($product_row['pricing_model'] == "VariableEnrollee" && $product_row['enrollee_type'] == "Primary" && $product_row['gender'] != "" && $product_row['gender'] != $primary_gender)
                        {
                            $is_rule_valid = false;
                            $rule_error = "Gender Error";
                        }
                    }
                }
                //**************************** Gender Rule Code End   **********************
                $response['is_rule_valid'] = $is_rule_valid;
                $rule_error_array[$product_row['matrix_id']] = $rule_error;

                if ($is_rule_valid)
                {
                    $defaultPlan = '';
                    $family_plan_rule = $product_row['family_plan_rule'];
                    if (!isset($spouse_dependent) && empty($child_dependent))
                    {
                        $defaultPlan = 1;
                    }
                    else if (isset($spouse_dependent) && empty($child_dependent))
                    {
                        $defaultPlan = 3;
                        if ($product_row['plan_type'] == 5 && $product_list[$product_row['p_product_id']]['default_plan_id'] != 4)
                        {
                            $defaultPlan = 5;
                        }
                        if ($family_plan_rule == "Minimum One Dependent")
                        {
                            $defaultPlan = 4;
                        }
                    }
                    else if (!isset($spouse_dependent) && !empty($child_dependent) && count($child_dependent) == 1)
                    {
                        $defaultPlan = 2;
                        if ($product_row['plan_type'] == 5 && $product_list[$product_row['p_product_id']]['default_plan_id'] != 4)
                        {
                            $defaultPlan = 5;
                        }
                        if ($family_plan_rule == "Minimum One Dependent")
                        {
                            $defaultPlan = 4;
                        }
                    }
                    else if (isset($spouse_dependent) && !empty($child_dependent) && count($child_dependent) == 1)
                    {
                        $defaultPlan = 4;
                    }
                    else if (isset($spouse_dependent) && !empty($child_dependent) && count($child_dependent) >= 2)
                    {
                        $defaultPlan = 4;
                    }
                    else if (!isset($spouse_dependent) && !empty($child_dependent) && count($child_dependent) >= 2)
                    {
                        $defaultPlan = 2;
                        if ($family_plan_rule == "Minimum Two Dependent" || $family_plan_rule == "Minimum One Dependent")
                        {
                            $defaultPlan = 4;
                        }
                    }
                    $tmpCombinationProducts = $MemberEnrollment->getCombinationProducts($product_row['p_product_id'], $sponsor_id);

                    if (isset($tmpCombinationProducts[$product_row['p_product_id']]['Packaged']))
                    {
                        $packaged_prd_not_purchased = true;
                        if (count($already_puchase_product) > 0)
                        {
                            $tmp_packaged_prd_ids = explode(',', $tmpCombinationProducts[$product_row['p_product_id']]['Packaged']['product_id']);
                            foreach ($tmp_packaged_prd_ids as $packaged_prd_id)
                            {
                                if (in_array($packaged_prd_id, $already_puchase_product))
                                {
                                    $packaged_prd_not_purchased = false;
                                    break;
                                }
                            }
                        }

                        if ($packaged_prd_not_purchased == true)
                        {
                            $combination_products[$product_row['p_product_id']]['Packaged'] = $tmpCombinationProducts[$product_row['p_product_id']]['Packaged'];
                        }
                    }

                    $product_code = $product_row['product_code'];
                    $category_id = $product_row['product_category_id'];
                    $company_id = $product_row['product_company'];
                    $category_name = $product_row['category_name'];
                    $product_id = $product_row['p_product_id'];
                    $product_type = $product_row['product_type'];
                    $product_name = $product_row['product_name'];
                    $parent_product_id = $product_row['primary_product_id'];
                    $matrix_id = $product_row['matrix_id'];
                    $product_price = $product_row['price'];
                    $plan_id = $product_row['plan_type'];
                    $enrollee_type = !empty($product_row['enrollee_type']) ? $product_row['enrollee_type'] : '';
                    $carrier_name = $product_row['carrier_name'];
                    $pricing_model = $product_row['pricing_model'];
                    if (empty($checkExtraQuestion))
                    {
                        $pricing_model = "FixedPrice";
                    }
                    $product_detail = base64_encode($product_row['product_detail']);
                    $plan_name = !empty($plan_id) ? $prdPlanTypeArray[$plan_id]['title'] : '';
                    $is_default_plan = 'N';
                    $priceType = !empty($plan_id) ? $plan_id : '';

                    if ($plan_id == $defaultPlan)
                    {
                        $is_default_plan = 'Y';
                    }
                    $is_add_on_product = isset($product_row['is_add_on_product']) ? $product_row['is_add_on_product'] : "";

                    $product_list[$product_id]['category_id'] = $category_id;
                    $product_list[$product_id]['category_name'] = $category_name;
                    $product_list[$product_id]['default_plan_id'] = $defaultPlan;
                    $product_list[$product_id]['product_id'] = $product_id;
                    $product_list[$product_id]['product_name'] = $product_name;
                    $product_list[$product_id]['product_code'] = $product_code;
                    $product_list[$product_id]['parent_product_id'] = $product_name;
                    $product_list[$product_id]['company_id'] = $company_id;
                    $product_list[$product_id]['product_type'] = $product_type;
                    $product_list[$product_id]['is_add_on_product'] = $is_add_on_product;

                    $product_list[$product_id]['carrier_name'] = $carrier_name;
                    $product_list[$product_id]['product_detail'] = $product_detail;
                    $product_list[$product_id]['pricing_model'] = $pricing_model;

                    if (!empty($enrollee_type))
                    {
                        if (!isset($product_list[$product_id]['Enrollee_Matrix']))
                        {

                            $enrolleeCoverage = $MemberEnrollment->getProductCoverageOptions($product_row['p_product_id']);

                            if (!empty($enrolleeCoverage))
                            {
                                foreach ($enrolleeCoverage as $ecKey => $ecValue)
                                {
                                    $product_list[$product_id]['Enrollee_Matrix'][$ecValue['plan_id']]['matrix_id'] = $ecValue['plan_id'];
                                    $product_list[$product_id]['Enrollee_Matrix'][$ecValue['plan_id']]['product_price'] = '0.00';
                                    $product_list[$product_id]['Enrollee_Matrix'][$ecValue['plan_id']]['plan_id'] = $ecValue['plan_id'];
                                    $product_list[$product_id]['Enrollee_Matrix'][$ecValue['plan_id']]['plan_name'] = $ecValue['plan_name'];
                                }
                            }
                        }
                    }
                    if (!empty($plan_id) && (!isset($product_list[$product_id]['Matrix'][$plan_id]['product_price']) || $product_list[$product_id]['Matrix'][$plan_id]['product_price'] < $product_price))
                    {
                        $product_list[$product_id]['Matrix'][$plan_id]['matrix_id'] = $matrix_id;
                        $product_list[$product_id]['Matrix'][$plan_id]['product_price'] = $product_price;
                        $product_list[$product_id]['Matrix'][$plan_id]['plan_id'] = $plan_id;
                        $product_list[$product_id]['Matrix'][$plan_id]['plan_name'] = $plan_name;
                    }
                }
            }
        }
        $response['rule_error'] = $rule_error_array;

        if (!empty($product_list))
        {
            $response['product_list'] = $product_list;
        }
        if (!empty($combination_products))
        {
            $response['combination_products'] = $combination_products;
        }
        $response['primary_email'] = $primary_email;
        $response['primary_state'] = $found_state_id;
        $response['status'] = 'success';
    }
    else
    {
        $response['status'] = 'fail';
        // $errors = $validate->getErrors();
        // $response['errors'] = $errors;
        
    }

    return $response;

}
function enrollee_detail($cust_row = array(),$product_id = 0,$dep_profiles = array(),$other_params = array()){
    global $pdo;
	$enrollee = array();
	$child_data = $spouse_data = $primary_data = array();

	if(!empty($cust_row)) {
		$height = $cust_row['height'];

		$benefit_amount = '';
		if(isset($cust_row['benefit_amount'][$product_id])) {
			$benefit_amount = $cust_row['benefit_amount'][$product_id];
		}
        $in_patient_benefit = '';
		if(isset($cust_row['in_patient_benefit'][$product_id])) {
			$in_patient_benefit = $cust_row['in_patient_benefit'][$product_id];
		}
        $out_patient_benefit = '';
		if(isset($cust_row['out_patient_benefit'][$product_id])) {
			$out_patient_benefit = $cust_row['out_patient_benefit'][$product_id];
		}
        $monthly_income = '';
		if(isset($cust_row['monthly_income'][$product_id])) {
			$monthly_income = $cust_row['monthly_income'][$product_id];
		}

 		$primary_data[1] = array(
			"fname" => $cust_row['fname'],
            "lname" => $cust_row['lname'],
			"gender" => $cust_row['gender'],
			"birthdate" => $cust_row['birthdate'],
			"zip" => $cust_row['zip'],
			"smoking_status" => $cust_row['smoking_status'],
			"tobacco_status" => $cust_row['tobacco_status'],
			"height" => $height,
			"weight" => $cust_row['weight'],
			"no_of_children" => $cust_row['no_of_children'],
			"has_spouse" => $cust_row['has_spouse'],
			"benefit_amount" => $benefit_amount,
			"in_patient_benefit" => $in_patient_benefit,
			"out_patient_benefit" => $out_patient_benefit,
			"monthly_income" => $monthly_income,
			"benefit_level" => $cust_row['benefit_level'],
			"employmentStatus" => $cust_row['employment_status'],
			"salary" => $cust_row['salary'],
			"hire_date" => $cust_row['hire_date'],
			"hours_per_week" => $cust_row['hours_per_week'],
			"pay_frequency" => $cust_row['pay_frequency'],
			"us_citizen" => $cust_row['us_citizen'],
		);
	}

    if(!empty($dep_profiles)) {
        $child_cnt = 1;
        foreach ($dep_profiles as $key => $cd_row) {
            $height = $cd_row['height'];

            $benefit_amount = '';
            if(!empty($cd_row['benefit_amount']) && isset($cd_row['benefit_amount'])) {
                $benefit_amount = $cd_row['benefit_amount'];
            } 
            $in_patient_benefit_amount = '';
            if(!empty($cd_row['in_patient_benefit']) && isset($cd_row['in_patient_benefit'])) {
                $in_patient_benefit_amount = $cd_row['in_patient_benefit'];
            }
            $out_patient_benefit_amount = '';
            if(!empty($cd_row['out_patient_benefit']) && isset($cd_row['out_patient_benefit'])) {
                $out_patient_benefit_amount = $cd_row['out_patient_benefit'];
            }
            $monthly_income = '';
            if(!empty($cd_row['monthly_income']) && isset($cd_row['monthly_income'])) {
                $monthly_income = $cd_row['monthly_income'];
            }

            if(in_array(strtolower($cd_row['relation']),array('child'))) {
                $child_data[$child_cnt] = array(
                    "display_id" => $cd_row['display_id'],
                    "fname" => $cd_row['fname'],
                    "lname" => $cd_row['lname'],
                    "gender" => $cd_row['gender'],
                    "birthdate" => $cd_row['birthdate'],
                    "zip" => $cd_row['zip_code'],
                    "smoking_status" => $cd_row['smoking_status'],
                    "tobacco_status" => $cd_row['tobacco_status'],
                    "height" => $height,
                    "weight" => $cd_row['weight'],
                    "no_of_children" => "",
                    "has_spouse" => "",
                    "benefit_amount" => $benefit_amount,
                    "in_patient_benefit" => $in_patient_benefit_amount,
                    "out_patient_benefit" => $out_patient_benefit_amount,
                    "monthly_income" => $monthly_income,
                    "benefit_level" => $cd_row['benefit_level'],
                    "employmentStatus" => $cd_row['employment_status'],
                    "salary" => $cd_row['salary'],
                    "hire_date" => $cd_row['hire_date'],
                    "hours_per_week" => $cd_row['hours_per_week'],
                    "pay_frequency" => $cd_row['pay_frequency'],
                    "us_citizen" => $cd_row['us_citizen'],
                );
                $child_cnt++;
            } else {
                $spouse_data[1] = array(
                    "display_id" => $cd_row['display_id'],
                    "fname" => $cd_row['fname'],
                    "lname" => $cd_row['lname'],
                    "gender" => $cd_row['gender'],
                    "birthdate" => $cd_row['birthdate'],
                    "zip" => $cd_row['zip_code'],
                    "smoking_status" => $cd_row['smoking_status'],
                    "tobacco_status" => $cd_row['tobacco_status'],
                    "height" => $height,
                    "weight" => $cd_row['weight'],
                    "no_of_children" => "",
                    "has_spouse" => "",
                    "benefit_amount" => $benefit_amount,
                    "in_patient_benefit" => $in_patient_benefit_amount,
                    "out_patient_benefit" => $out_patient_benefit_amount,
                    "monthly_income" => $monthly_income,
                    "benefit_level" => $cd_row['benefit_level'],
                    "employmentStatus" => $cd_row['employment_status'],
                    "salary" => $cd_row['salary'],
                    "hire_date" => $cd_row['hire_date'],
                    "hours_per_week" => $cd_row['hours_per_week'],
                    "pay_frequency" => $cd_row['pay_frequency'],
                    "us_citizen" => $cd_row['us_citizen'],
                );
            }
        }
    }
	$enrollee = array();
	if(!empty($primary_data)) {
		$enrollee['Primary'] =  $primary_data;
	}
	if(!empty($spouse_data)) {
		$enrollee['Spouse'] =  $spouse_data;
	}
	if(!empty($child_data)) {
		$enrollee['Child'] =  $child_data;
	}
	return $enrollee;
}
function product_price_detail($cust_row = array(),$product_id,$plan_type,$ws_id=0,$other_params = array(),$is_cobra='N'){
    global $pdo;
	include_once dirname(__DIR__) . '/includes/member_enrollment.class.php';
	$MemberEnrollment = new MemberEnrollment();
	$missing_pricing_criteria = array();
	$pricing_criteria_not_match = array();
	$plan_id = 0;
	$price = 0.0;
	$member_price = 0.0;
	$display_member_price = 0.0;
	$group_price = 0.0;
	$display_group_price = 0.0;
	$is_group_member = 'N';
	$contribution_type = '';
	$contribution_value = '';
	$groupCoverageContributionArr = array();
	$today_date = date('Y-m-d');
	$error_display = "";

	$shortTermProductDetails = $MemberEnrollment->shortTermDisabilityProductDetails($product_id);

	$is_short_term_disability_product = 'N';
	$monthly_benefit_allowed_db = "";
	$percentage_of_salary_db = "";
	$prd_matrix_id = 0;
	$annual_salary = !empty($other_params['annual_salary']) ? $other_params['annual_salary'] : 0;
	$accepted = 'N';
	$benefit_amount_percentage = "";

    if($shortTermProductDetails){
		$is_short_term_disability_product = $shortTermProductDetails['is_short_term_disablity_product'];
		$monthly_benefit_allowed_db = $shortTermProductDetails['monthly_benefit_allowed'];
		$percentage_of_salary_db = $shortTermProductDetails['percentage_of_salary'];
	}

    if(strtolower($cust_row['sponsor_type']) == "group" && $is_cobra == 'N') {
		$is_group_member = 'Y';	

		$sqlCoveragePeriod="SELECT gcc.*,gc.pay_period 
			FROM group_coverage_period_offering gco 
			JOIN group_classes gc ON (gc.id=gco.class_id and gc.is_deleted='N') 
			LEFT JOIN group_coverage_period_contributions gcc on(gcc.group_coverage_period_offering_id=gco.id AND gcc.is_deleted='N')
			where gco.is_deleted='N' AND gco.status='Active' AND gco.group_coverage_period_id=:group_coverage_period_id AND gco.group_id=:group_id AND gco.class_id=:class_id";
		$sqlCoveragePeriodWhere=array(':group_id'=>$cust_row['sponsor_id'],':class_id'=>$cust_row['class_id'],':group_coverage_period_id'=>$cust_row['group_coverage_period_id']);
		$resCovergaePeriod=$pdo->select($sqlCoveragePeriod,$sqlCoveragePeriodWhere);
		
		foreach ($resCovergaePeriod as $key => $value) {
			$groupCoverageContributionArr[$value['product_id']][$value['plan_id']]['type']=$value['type'];
			$groupCoverageContributionArr[$value['product_id']][$value['plan_id']]['contribution']=$value['con_value'];
			$groupCoverageContributionArr[$value['product_id']][$value['plan_id']]['pay_period']=$value['pay_period'];
            $groupCoverageContributionArr['pay_period']['pay_period']=$value['pay_period'];
		}
	}

    $prd_sql = "SELECT pricing_model FROM prd_main p WHERE p.id=:id";
	$prd_row = $pdo->selectOne($prd_sql,array(":id"=>$product_id));
	$orig_pricing_model = $prd_row['pricing_model'];
	if(!empty($prd_row)) {
		if($prd_row['pricing_model'] == "FixedPrice") {
			$plan_sql = "SELECT id,price 
						FROM prd_matrix 
						WHERE 
						is_deleted='N' AND 
						(pricing_effective_date <= :today_date AND (pricing_termination_date >= :today_date OR pricing_termination_date is null)) AND
						product_id=:product_id AND 
						plan_type=:plan_type";
			$plan_row = $pdo->selectOne($plan_sql,array(":product_id"=>$product_id,":plan_type"=>$plan_type,":today_date"=>$today_date));
			if(!empty($plan_row)) {
				$plan_id = $plan_row['id'];
				$prd_matrix_id = $plan_row['id'];
				$price = $plan_row['price'];
				$display_member_price = $plan_row['price'];

				if(isset($groupCoverageContributionArr) && $groupCoverageContributionArr){
                    $tmp_contribution_value = isset($groupCoverageContributionArr[$product_id][$plan_id]) ? $groupCoverageContributionArr[$product_id][$plan_id] : null;
					if(isset($tmp_contribution_value) || !empty($groupCoverageContributionArr['pay_period'])){
                        $tmp_group_coverage_contribution = !empty($tmp_contribution_value) ? $tmp_contribution_value : $groupCoverageContributionArr['pay_period'];
						$calculatedPrice = $MemberEnrollment->calculateGroupContributionPrice($price,$tmp_group_coverage_contribution,false);
						$member_price = $calculatedPrice['member_price'];
						$display_member_price = $calculatedPrice['display_member_price'];
						$group_price = $calculatedPrice['group_price'];
						$display_group_price = $calculatedPrice['display_group_price'];
						$contribution_type = $calculatedPrice['contribution_type'];
						$contribution_value = $calculatedPrice['contribution_value'];
					}
				}

				if($is_short_term_disability_product == 'Y' && isset($other_params['primary_benefit_amount'])){
					if($prd_matrix_id){
						$price = getname('prd_matrix',$prd_matrix_id,'price','id');
						if($price){
							$adjusted_percentage = $MemberEnrollment->calculateSTDPercentage($annual_salary,$other_params['primary_benefit_amount']);
							$rate_details = $MemberEnrollment->calculateSTDRate($price,$annual_salary,$adjusted_percentage,$accepted,$percentage_of_salary_db);

							if($accepted == 'Y'){
								$rate_details = $MemberEnrollment->calculateSTDRate($price,$annual_salary,$adjusted_percentage,$accepted,$percentage_of_salary_db);
							}

							$rate = $rate_details['rate'];
							$monthly_benefit = $rate_details['monthly_benefit'];

							
							if($rate_details['allowed_benefit_amount'] < $other_params['primary_benefit_amount']){
								$error_display .= " <br> Maximum benefit percentage is ".$percentage_of_salary_db."% of monthly salary for this product";
								$plan_id = 0;
							}else if(($monthly_benefit_allowed_db < $other_params['primary_benefit_amount'])){
								$error_display .= " <br> Maximum benefit amount is ".displayAmount($monthly_benefit_allowed_db,2)." for this product";
								$plan_id = 0;
							}

							if(empty($error_display)){
								$display_member_price = $rate;
								$price = $rate;
								$tmp_plan_id = $prd_matrix_id;
								$group_price = 0;
								$display_group_price = 0;
								$member_price = 0;
								$benefit_amount_percentage = $adjusted_percentage;
							}
						}
					}
				}
			}
		} else {
			$assignedQuestionValue = $MemberEnrollment->assignedQuestionValue($product_id);
			$benefitAmountSetting = $MemberEnrollment->benefitAmountSetting($product_id);
			$variableEnrolleeOptions = array();

			$pricing_model = $prd_row['pricing_model'];
			if($pricing_model == "VariablePrice"){
				$assignedQuestionValue=$MemberEnrollment->assignedQuestionValue($product_id,$plan_type);
			}
			if($pricing_model=="VariableEnrollee"){
				$variableEnrolleeOptions=$MemberEnrollment->variableEnrolleeOptions($product_id);
			}
			$dep_profiles = array();
			if(!empty($other_params['dep_profiles'])) {
				$dep_profiles = $other_params['dep_profiles'];
			}
			$enrollee = enrollee_detail($cust_row,$product_id,$dep_profiles,$other_params);
			$productDetails  = array();
			$largestChild = array();
            $valid_rule_id = array();
			if(!empty($enrollee)){
				foreach ($enrollee as $enrolleeType => $enrolleeArr) {

					if(isset($assignedQuestionValue[$enrolleeType]['id'])){
						foreach ($assignedQuestionValue[$enrolleeType]['id'] as $key => $value) {
							if(!empty($enrolleeArr)){
								foreach ($enrolleeArr as $fieldKey => $fieldName) {
									$is_rule_valid=true;

									if(isset($fieldName["gender"])){
										$criteriaGender = $assignedQuestionValue[$enrolleeType]['gender'][$key];
										if($criteriaGender!='' && $fieldName["gender"] != $criteriaGender){
											$is_rule_valid = false;
											if(empty($fieldName["gender"])) {
												$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Gender";
											} else {
												$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Gender";
											}
										}
									}
									if(isset($fieldName["birthdate"])){
										$age_from_birthdate=calculateAge($fieldName["birthdate"]);
										$criteriaAgeFrom = $assignedQuestionValue[$enrolleeType]['age_from'][$key];
										$criteriaAgeTo = $assignedQuestionValue[$enrolleeType]['age_to'][$key];
										
										if($criteriaAgeFrom>=0 &&  $criteriaAgeTo>0 && ($criteriaAgeFrom > $age_from_birthdate || $criteriaAgeTo < $age_from_birthdate)){
											$is_rule_valid = false;
											if(empty($fieldName["birthdate"])) {
												$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Birthdate";
											} else {
												$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Birthdate";
											}
										}
										if($enrolleeType == 'Child'){
											if(empty($largestChild)){
												$largestChild['age'] = $age_from_birthdate;
												$largestChild['id'] = $fieldKey;
											}else{
												if($age_from_birthdate > $largestChild['age']){
													$largestChild['age'] = $age_from_birthdate;
													$largestChild['id'] = $fieldKey;
												}
											}
										}
									}else{
										if($enrolleeType == 'Child' && empty($largestChild)){
											$largestChild['age'] = 0;
											$largestChild['id'] = $fieldKey;
										}
										
									}
									if(isset($fieldName["zip"])){
										$criteriaZip = $assignedQuestionValue[$enrolleeType]['zipcode'][$key];
										if($criteriaZip != '' && $fieldName["zip"] != $criteriaZip){
											$is_rule_valid = false;

											if(empty($fieldName["zip"])) {
												$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Zip";
											} else {
												$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Zip";
											}
										}
										$criteriaStateName = $assignedQuestionValue[$enrolleeType]['state'][$key];
										if(!empty($criteriaStateName)){
											$getStateCode=$pdo->selectOne("SELECT state_code from zip_code WHERE zip_code=:zip_code",array(":zip_code"=>$fieldName["zip"]));
											$pricing_control_State = '';
											if($getStateCode){
												$pricing_control_State = getname("states_c",$getStateCode['state_code'],"name","short_name");
											}

											if($criteriaStateName != $pricing_control_State){
												$is_rule_valid = false;
											}
											$restricted_state_date = date('Y-m-d');

											$restrictedStateSql="SELECT GROUP_CONCAT(distinct product_id) as restrictedStateProduct FROM prd_no_sale_states WHERE state_name=:state AND is_deleted='N' AND effective_date <= :restricted_state_date AND (termination_date >= :restricted_state_date OR termination_date IS NULL) AND product_id = :product_id";
											$restrictedStateRes=$pdo->selectOne($restrictedStateSql,array(":state"=>$pricing_control_State,":restricted_state_date"=>$restricted_state_date,':product_id' => $product_id));
											
											if(!empty($restrictedStateRes['restrictedStateProduct'])){
												$restrictedStateArray = explode(",", $restrictedStateRes['restrictedStateProduct']);

												if(in_array($product_id,$restrictedStateArray)){
													$is_rule_valid = false;
												}
											}
										}
									}
									if(isset($fieldName["smoking_status"])){
										$criteriaSmoking = $assignedQuestionValue[$enrolleeType]['smoking_status'][$key];
										if($criteriaSmoking != '' && $fieldName["smoking_status"] != $criteriaSmoking){
											$is_rule_valid = false;
											
											if(empty($fieldName["smoking_status"])) {
												$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Smoking Status";
											} else {
												$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Smoking Status";
											}
										}
									}
									if(isset($fieldName["tobacco_status"])){
										$criteriaTobacco = $assignedQuestionValue[$enrolleeType]['tobacco_status'][$key];
										if($criteriaTobacco !='' && $fieldName["tobacco_status"] != $criteriaTobacco){
											$is_rule_valid = false;
											
											if(empty($fieldName["tobacco_status"])) {
												$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Tobacco Status";
											} else {
												$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Tobacco Status";
											}
										}
									}
									if(isset($fieldName["height"])){
										$height=$fieldName["height"];
										
										$heightBy=$assignedQuestionValue[$enrolleeType]['height_by'][$key];
										$criteriaHeight = $assignedQuestionValue[$enrolleeType]['height_feet'][$key].".".$assignedQuestionValue[$enrolleeType]['height_inch'][$key];
										$criteriaHeightTo = $assignedQuestionValue[$enrolleeType]['height_feet_to'][$key].".".$assignedQuestionValue[$enrolleeType]['height_inch_to'][$key];

                                      if($criteriaHeight !=0 || $criteriaHeightTo!=0 ){
										if($heightBy=="Exactly"){
											if($criteriaHeight!='' && $height != $criteriaHeight){
												$is_rule_valid = false;

												if(empty($fieldName["height"])) {
													$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Height";
												} else {
													$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Height";
												}
											}
										}else if($heightBy=="Less Than"){
											if($criteriaHeight!='' && $height >= $criteriaHeight){
												$is_rule_valid = false;

												if(empty($fieldName["height"])) {
													$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Height";
												} else {
													$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Height";
												}
											}
										}else if($heightBy=="Greater Than"){
											if($criteriaHeight!='' && $height <= $criteriaHeight){
												$is_rule_valid = false;
												
												if(empty($fieldName["height"])) {
													$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Height";
												} else {
													$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Height";
												}
											}
										}else if($heightBy=="Range"){
											if($criteriaHeight!='' && $criteriaHeightTo!='' && ($criteriaHeight > $height || $criteriaHeightTo < $height)){
												$is_rule_valid = false;
												
												if(empty($fieldName["height"])) {
													$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Height";
												} else {
													$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Height";
												}
											}
										}
                                      }
									}
									if(isset($fieldName["weight"])){
										$weight=$fieldName["weight"];
										
										$weightBy=$assignedQuestionValue[$enrolleeType]['weight_by'][$key];
										$criteriaWeight = $assignedQuestionValue[$enrolleeType]['weight'][$key];
										$criteriaWeightTo = $assignedQuestionValue[$enrolleeType]['weight_to'][$key];

                                      if($criteriaWeight !=0 || $criteriaWeightTo!=0 ){
										if($weightBy=="Exactly"){
											if($criteriaWeight!='' && $weight != $criteriaWeight){
												$is_rule_valid = false;
												
												if(empty($fieldName["weight"])) {
													$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Weight";
												} else {
													$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Weight";
												}
											}
										}else if($weightBy=="Less Than"){
											if($criteriaWeight!='' && $weight >= $criteriaWeight){
												$is_rule_valid = false;
												
												if(empty($fieldName["weight"])) {
													$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Weight";
												} else {
													$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Weight";
												}
											}
										}else if($weightBy=="Greater Than"){
											if($criteriaWeight!='' && $weight <= $criteriaWeight){
												$is_rule_valid = false;
												
												if(empty($fieldName["weight"])) {
													$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Weight";
												} else {
													$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Weight";
												}
											}
										}else if($weightBy=="Range"){
											if($criteriaWeight!='' && $criteriaWeightTo!='' && ($criteriaWeight > $weight || $criteriaWeightTo < $weight)){
												$is_rule_valid = false;
												
												if(empty($fieldName["weight"])) {
													$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Weight";
												}		 else {
													$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Weight";
												}
											}
										}
                                      }
									}
									if(isset($fieldName["no_of_children"])){
										$no_of_children=$fieldName["no_of_children"];
										
										$noOfChildrenBy=$assignedQuestionValue[$enrolleeType]['no_of_children_by'][$key];
										$criteriaNoOfChildren = $assignedQuestionValue[$enrolleeType]['no_of_children'][$key];
										$criteriaNoOfChildrenTo = $assignedQuestionValue[$enrolleeType]['no_of_children_to'][$key];

                                      if($criteriaNoOfChildren !=0 || $criteriaNoOfChildrenTo!=0 ){
										if($noOfChildrenBy=="Exactly"){
											if($criteriaNoOfChildren!='' && $no_of_children != $criteriaNoOfChildren){
												$is_rule_valid = false;

												if(empty($fieldName["no_of_children"])) {
													$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "No of children";
												} else {
													$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." No of children";
												}
											}
										}else if($noOfChildrenBy=="Less Than"){
											if($criteriaNoOfChildren!='' && $no_of_children >= $criteriaNoOfChildren){
												$is_rule_valid = false;
												
												if(empty($fieldName["no_of_children"])) {
													$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "No of children";
												} else {
													$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." No of children";
												}
											}
										}else if($noOfChildrenBy=="Greater Than"){
											if($criteriaNoOfChildren!='' && $no_of_children <= $criteriaNoOfChildren){
												$is_rule_valid = false;
												
												if(empty($fieldName["no_of_children"])) {
													$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "No of children";
												} else {
													$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." No of children";
												}
											}
										}else if($noOfChildrenBy=="Range"){
											if($criteriaNoOfChildren!='' && $criteriaNoOfChildrenTo!='' && ($criteriaNoOfChildren > $no_of_children || $criteriaNoOfChildrenTo < $no_of_children)){
												$is_rule_valid = false;
												
												if(empty($fieldName["no_of_children"])) {
													$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "No of children";
												} else {
													$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." No of children";
												}
											}
										}
                                      }
									}
									if(isset($fieldName["has_spouse"])){
										$criteriaHasSpouse = $assignedQuestionValue[$enrolleeType]['has_spouse'][$key];
										if($criteriaHasSpouse!='' && $fieldName["has_spouse"] != $criteriaHasSpouse){
											$is_rule_valid = false;
											
											if(empty($fieldName["has_spouse"])) {
												$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Has Spouse";
											} else {
												$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Has Spouse";
											}
										}
									}
									if(isset($fieldName["benefit_amount"])){
										$criteriaBenefit = $assignedQuestionValue[$enrolleeType]['benefit_amount'][$key];
										if($criteriaBenefit !='0.00' && $fieldName["benefit_amount"] != $criteriaBenefit){
											$is_rule_valid = false;
											
											if(!empty($criteriaBenefit) && $criteriaBenefit !='0.00' && empty($fieldName["benefit_amount"])) {
												$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Benefit Amount";
											} else {
												$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Benefit Amount";
											}
										}

										if(!empty($benefitAmountSetting) ){
											if(isset($enrollee['Primary']) && isset($enrollee['Spouse'])){
												if($benefitAmountSetting['is_spouse_issue_amount_larger']=='N' && $enrollee['Spouse']['1']['benefit_amount'] > $enrollee['Primary']['1']['benefit_amount']){
													$is_rule_valid = false;
													$error_display = 'Spouse issue amount can not be larger than primary';
												}
											}
											
											
											/*if($enrolleeType == 'Primary' && !empty($benefitAmountSetting['primary_issue_amount']) && $fieldName["benefit_amount"] > $benefitAmountSetting['primary_issue_amount']){

												$is_rule_valid = false;
												$error_display = 'Guarantee Issue amount for Primary is $'.$benefitAmountSetting['primary_issue_amount'].', please select this benefit level';
												
											}
											
											if($enrolleeType == 'Spouse' && !empty($benefitAmountSetting['spouse_issue_amount']) && $fieldName["benefit_amount"] > $benefitAmountSetting['spouse_issue_amount']){
												$is_rule_valid = false;
												$error_display = 'Guarantee Issue amount for Spouse is $'.$benefitAmountSetting['spouse_issue_amount'].', please select this benefit level';
												
											}
											if($enrolleeType == 'Child' && !empty($benefitAmountSetting['child_issue_amount']) && $fieldName["benefit_amount"] > $benefitAmountSetting['child_issue_amount']){
												$is_rule_valid = false;
												$error_display = 'Guarantee Issue amount for Child(ren) is $'.$benefitAmountSetting['child_issue_amount'].', please select this benefit level';
												
											}*/
										}
									}
									if(isset($fieldName["in_patient_benefit"])){
										$criteriaInPatientBenefit = $assignedQuestionValue[$enrolleeType]['in_patient_benefit'][$key];
										if($criteriaInPatientBenefit !='0.00' && $fieldName["in_patient_benefit"] != $criteriaInPatientBenefit){
											$is_rule_valid = false;
											if(!empty($criteriaInPatientBenefit) && $criteriaInPatientBenefit !='0.00' && empty($fieldName["in_patient_benefit"])) {
												$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "InPatient Benefit";
											} else {
												$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." InPatient Benefit";
											}
										}
									}
									if(isset($fieldName["out_patient_benefit"])){
										$criteriaOutPatientBenefit = $assignedQuestionValue[$enrolleeType]['out_patient_benefit'][$key];
										if($criteriaOutPatientBenefit !='0.00' && $fieldName["out_patient_benefit"] != $criteriaOutPatientBenefit){
											$is_rule_valid = false;
											if(!empty($criteriaOutPatientBenefit) && $criteriaOutPatientBenefit !='0.00' && empty($fieldName["out_patient_benefit"])) {
												$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "OutPatient Benefit";
											} else {
												$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." OutPatient Benefit";
											}
										}
									}
									if(isset($fieldName["monthly_income"])){
										$criteriaMonthlyIncome = $assignedQuestionValue[$enrolleeType]['monthly_income'][$key];
										if($criteriaMonthlyIncome !='0.00' && $fieldName["monthly_income"] != $criteriaMonthlyIncome){
											$is_rule_valid = false;
											if(!empty($criteriaMonthlyIncome) && $criteriaMonthlyIncome !='0.00' && empty($fieldName["monthly_income"])) {
												$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Monthly Income";
											} else {
												$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Monthly Income";
											}
										}
									}
									/*
									if(isset($fieldName["benefit_percentage"])){
										$criteriaBenefitPercentage = $assignedQuestionValue[$enrolleeType]['benefit_percentage'][$key];
										if($criteriaBenefitPercentage !='0.00' && $fieldName["benefit_percentage"] != $criteriaBenefitPercentage){
											$is_rule_valid = false;
											if(!empty($criteriaBenefitPercentage) && $criteriaBenefitPercentage !='0.00' && empty($fieldName["benefit_percentage"])) {
												$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Benefit Percentage";
											} else {
												$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Benefit Percentage";
											}
										}
									}*/
									if($is_rule_valid){
										if(!empty($valid_rule_id[$fieldKey])){
											$prevID = $valid_rule_id[$fieldKey];
											$newID  = $key;

											if($assignedQuestionValue[$enrolleeType]['price'][$newID] > $assignedQuestionValue[$enrolleeType]['price'][$prevID]){
												$valid_rule_id[$fieldKey]=$key;
											}

										}else{
											$valid_rule_id[$fieldKey]=$key;
										}
									}
								}
							}
						}
					}
					
					if(!empty($valid_rule_id)){
						foreach ($valid_rule_id as $fieldKey => $value) {

							if($enrolleeType=='Child' && !empty($variableEnrolleeOptions) && $variableEnrolleeOptions['child_dependent_rate_calculation']=='Single Rate based on Eldest Child'){
								if(!empty($largestChild) && $fieldKey == $largestChild['id']){
									$productDetails[$enrolleeType][$fieldKey]['matrix_id']=$assignedQuestionValue[$enrolleeType]['prd_matrix_id'][$value];
							
									if(isset($groupCoverageContributionArr) && !empty($groupCoverageContributionArr)){
                                        $tmp_contribution_value = isset($groupCoverageContributionArr[$assignedQuestionValue[$enrolleeType]['product_id'][$value]][$assignedQuestionValue[$enrolleeType]['prd_matrix_id'][$value]]) ? $groupCoverageContributionArr[$assignedQuestionValue[$enrolleeType]['product_id'][$value]][$assignedQuestionValue[$enrolleeType]['prd_matrix_id'][$value]] : null;
										if(isset($tmp_contribution_value) || !empty($groupCoverageContributionArr['pay_period'])){
                                            $tmp_group_coverage_contribution = !empty($tmp_contribution_value) ? $tmp_contribution_value : $groupCoverageContributionArr['pay_period'];
											$calculatedPrice=$MemberEnrollment->calculateGroupContributionPrice($assignedQuestionValue[$enrolleeType]['price'][$value],$tmp_group_coverage_contribution,false);
											$productDetails[$enrolleeType][$fieldKey]['price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
											$productDetails[$enrolleeType][$fieldKey]['member_price']=$calculatedPrice['member_price'];
											$productDetails[$enrolleeType][$fieldKey]['display_member_price']=$calculatedPrice['display_member_price'];
											$productDetails[$enrolleeType][$fieldKey]['group_price']=$calculatedPrice['group_price'];
											$productDetails[$enrolleeType][$fieldKey]['display_group_price']=$calculatedPrice['display_group_price'];
											$productDetails[$enrolleeType][$fieldKey]['contribution_type']=$calculatedPrice['contribution_type'];
											$productDetails[$enrolleeType][$fieldKey]['contribution_value']=$calculatedPrice['contribution_value'];
										} else {
											$productDetails[$enrolleeType][$fieldKey]['price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
											$productDetails[$enrolleeType][$fieldKey]['display_member_price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
											$productDetails[$enrolleeType][$fieldKey]['group_price']=0;
											$productDetails[$enrolleeType][$fieldKey]['display_group_price']=0;
											$productDetails[$enrolleeType][$fieldKey]['member_price']=0;
											
										}
									} else {
										$productDetails[$enrolleeType][$fieldKey]['price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
										$productDetails[$enrolleeType][$fieldKey]['display_member_price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
										$productDetails[$enrolleeType][$fieldKey]['group_price']=0;
										$productDetails[$enrolleeType][$fieldKey]['display_group_price']=0;
										$productDetails[$enrolleeType][$fieldKey]['member_price']=0;

									}
								}
							}else{
								$productDetails[$enrolleeType][$fieldKey]['matrix_id']=$assignedQuestionValue[$enrolleeType]['prd_matrix_id'][$value];
								
								if(isset($groupCoverageContributionArr[$product_id]) && !empty($groupCoverageContributionArr[$product_id])){
                                    $tmp_contribution_value = isset($groupCoverageContributionArr[$assignedQuestionValue[$enrolleeType]['product_id'][$value]][$assignedQuestionValue[$enrolleeType]['prd_matrix_id'][$value]]) ? $groupCoverageContributionArr[$assignedQuestionValue[$enrolleeType]['product_id'][$value]][$assignedQuestionValue[$enrolleeType]['prd_matrix_id'][$value]] : null;
									if(isset($tmp_contribution_value) || !empty($groupCoverageContributionArr['pay_period'])){
                                        $tmp_group_coverage_contribution = !empty($tmp_contribution_value) ? $tmp_contribution_value : $groupCoverageContributionArr['pay_period'];
										$calculatedPrice=$MemberEnrollment->calculateGroupContributionPrice($assignedQuestionValue[$enrolleeType]['price'][$value],$tmp_group_coverage_contribution,false);
										$productDetails[$enrolleeType][$fieldKey]['price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
										$productDetails[$enrolleeType][$fieldKey]['member_price']=$calculatedPrice['member_price'];
										$productDetails[$enrolleeType][$fieldKey]['display_member_price']=$calculatedPrice['display_member_price'];
										$productDetails[$enrolleeType][$fieldKey]['group_price']=$calculatedPrice['group_price'];
										$productDetails[$enrolleeType][$fieldKey]['display_group_price']=$calculatedPrice['display_group_price'];
										$productDetails[$enrolleeType][$fieldKey]['contribution_type']=$calculatedPrice['contribution_type'];
										$productDetails[$enrolleeType][$fieldKey]['contribution_value']=$calculatedPrice['contribution_value'];
									}else{
										$productDetails[$enrolleeType][$fieldKey]['price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
										$productDetails[$enrolleeType][$fieldKey]['display_member_price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
										$productDetails[$enrolleeType][$fieldKey]['group_price']=0;
										$productDetails[$enrolleeType][$fieldKey]['display_group_price']=0;
										$productDetails[$enrolleeType][$fieldKey]['member_price']=0;
										
									}
								}else{
									$productDetails[$enrolleeType][$fieldKey]['price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
									$productDetails[$enrolleeType][$fieldKey]['display_member_price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
									$productDetails[$enrolleeType][$fieldKey]['group_price']=0;
									$productDetails[$enrolleeType][$fieldKey]['display_group_price']=0;
									$productDetails[$enrolleeType][$fieldKey]['member_price']=0;
									if($is_short_term_disability_product == 'Y' && isset($other_params['primary_benefit_amount'])){
										$price = $assignedQuestionValue[$enrolleeType]['price'][$value];
										if($price){
											$adjusted_percentage = $MemberEnrollment->calculateSTDPercentage($annual_salary,$other_params['primary_benefit_amount']);
											$rate_details = $MemberEnrollment->calculateSTDRate($price,$annual_salary,$adjusted_percentage,$accepted,$percentage_of_salary_db);

											if($accepted == 'Y'){
												$rate_details = $MemberEnrollment->calculateSTDRate($price,$annual_salary,$adjusted_percentage,$accepted);
											}

											$rate = $rate_details['rate'];
											$monthly_benefit = $rate_details['monthly_benefit'];
											$benefit_amount_percentage = $adjusted_percentage;
											$productDetails['Primary'][1]['display_member_price'] = $rate;
											$productDetails['Primary'][1]['price'] = $rate;
											// $productDetails['Primary'][1]['matrix_id'] = $prd_matrix_id;
											// $productDetails['Primary'][1]['group_price']=0;
											// $productDetails['Primary'][1]['display_group_price']=0;
											// $productDetails['Primary'][1]['member_price']=0;
											$productDetails['Primary'][1]['monthly_benefit']=$monthly_benefit;

											if($rate_details['allowed_benefit_amount'] < $other_params['primary_benefit_amount']){
												$error_display .= " <br> Maximum benefit percentage is ".$percentage_of_salary_db."% of monthly salary for this product";
												$plan_id = 0;
											}else if(($monthly_benefit_allowed_db < $other_params['primary_benefit_amount']) && $accepted == 'N'){
												$error_display .= " <br> Maximum benefit amount is ".displayAmount($monthly_benefit_allowed_db,2)." for this product";
											}
										}
									}
								}
							}
						}
					}
				}
			}

			if(!empty($enrollee)){
				foreach ($enrollee as $enrolleeType => $enrolleeArr) {
					if(!isset($productDetails[$enrolleeType])){
						$productDetails[$enrolleeType] = array();
					}
				}
			}
			if(!empty($productDetails) && empty($error_display)){
				$tmp_plan_id = '';
				$price = 0;
				$group_price = 0;
				$member_price = 0;
				$display_group_price = 0;
				$display_member_price = 0;
				foreach ($productDetails as $key1 => $value1) {
					if(!empty($value1)) {
						foreach ($value1 as $key2 => $value2) {
							if(!empty($tmp_plan_id)) {
								$tmp_plan_id .= ',';
							}
							$tmp_plan_id .= $value2['matrix_id'];
							$price += $value2['price'];
							$group_price += $value2['group_price'];
							$member_price += $value2['member_price'];
							$display_group_price += $value2['display_group_price'];
							$display_member_price += $value2['display_member_price'];

							if(!empty($value2['contribution_type']) && !empty($value2['contribution_value'])) {
								$contribution_type = $value2['contribution_type'];
								$contribution_value = $value2['contribution_value'];
							}
						}
					}
				}
				$plan_id = $tmp_plan_id;
			}

			//Remove duplicate errors
			if(!empty($missing_pricing_criteria)) {
				$org_missing_pricing_criteria = $missing_pricing_criteria;
				foreach ($missing_pricing_criteria as $key1 => $value1) {
					foreach ($value1 as $key2 => $value2) {
						$missing_pricing_criteria[$key1][$key2] = array_unique($missing_pricing_criteria[$key1][$key2]);
						if(isset($enrollee[$key1][$key2]['display_id'])) {
							$tmp_display_id = $enrollee[$key1][$key2]['display_id'];
							$missing_pricing_criteria[$key1][$tmp_display_id] = $missing_pricing_criteria[$key1][$key2];
							unset($missing_pricing_criteria[$key1][$key2]);
						}
					}
				}
			}
		}
	}

	return array(
		'customer_id' => $customer_id,
		'product_id' => $product_id,
		'prd_plan_type_id' => $plan_type,
		'ws_id' => $ws_id,
		'other_params' => $other_params,
		'productDetails' => !empty($productDetails)?$productDetails:array(),
		'enrollee' => !empty($enrollee)?$enrollee:array(),
		'error_display' => !empty($error_display)?$error_display:'',
		'plan_id' => $plan_id,
		'price' => $price,
		'member_price' => $member_price,
		'group_price' => $group_price,
		'display_member_price' => $display_member_price,
		'display_group_price' => $display_group_price,
		'contribution_type' => $contribution_type,
		'contribution_value' => $contribution_value,
		'missing_pricing_criteria' => $missing_pricing_criteria,
		'org_missing_pricing_criteria' => isset($org_missing_pricing_criteria)?$org_missing_pricing_criteria:'',
		'pricing_criteria_not_match' => isset($pricing_criteria_not_match)?$pricing_criteria_not_match:'',
		'valid_rule_id' => isset($valid_rule_id)?$valid_rule_id:'',
		'pricing_model' => $prd_row['pricing_model'],
		'benefit_amount_percentage' => $benefit_amount_percentage,
	);

}
function member_enrollment($params, $errorArr = array())
{
    global $pdo, $CREDIT_CARD_ENC_KEY, $CUSTOMER_HOST ,$SITE_ENV ,$AGENT_HOST, $ADMIN_HOST;

    include_once dirname(__DIR__) . '/includes/cyberx_payment_class.php';
    include_once dirname(__DIR__) . '/includes/member_enrollment.class.php';
    include_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
    include_once dirname(__DIR__) . '/includes/function.class.php';
    include_once dirname(__DIR__) . '/includes/member_setting.class.php';
    $memberSetting = new memberSetting();
    $MemberEnrollment = new MemberEnrollment();
    $enrollDate = new enrollmentDate();
    $function_list = new functionsList();
    $customer_rep_id = '';
    $BROWSER = getBrowser();
    $OS = getOS($_SERVER['HTTP_USER_AGENT']);
    $REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);

    $is_valid = true;

    $REAL_IP_ADDRESS = get_real_ipaddress();
    $response = array();

    $today_date = date('Y-m-d');
    $customer_id = 0;
    $lead_id = 0;
    $lead_display_id = '';
    $order_id = 0;
    $is_assisted_enrollment = 'N';
    $enrollment_type = isset($params['enrollment_type'])?$params['enrollment_type']:"";
    

    $customer_id = isset($params["customer_id"]) ? $params["customer_id"] : 0;
    $lead_id = isset($params["lead_id"]) ? $params["lead_id"] : 0;
    $existing_customer_id = 0;
    if (!empty($customer_id))
    {
        $existing_customer_id = $customer_id;
    }
    $sponsor_id = isset($params['sponsor_id']) ? $params['sponsor_id'] : "";
    // $submit_type = isset($params['submit_type'])?$params['submit_type']:"";
    $enrollmentLocation = isset($params['enrollmentLocation'])?$params['enrollmentLocation']:"";
    // $pb_id = isset($params['pb_id'])?$params['pb_id']:0; //Page Builder ID
    // $site_user_name = isset($params['site_user_name'])?$params['site_user_name']:''; //Page Builder ID
    // $action = isset($params['action'])?$params['action']:"";
    // $step = isset($params['dataStep'])?$params['dataStep']:"";
    // $from_admin = isset($params['from_admin'])?$params['from_admin']:"";
    $from_admin = '';
    $sponsor_sql = "SELECT id,type,upline_sponsors,level,payment_master_id,ach_master_id,fname,lname,user_name,rep_id,sponsor_id FROM customer WHERE type!='Customer' AND id = :id ";
    $sponsor_row = $pdo->selectOne($sponsor_sql, array(
        ':id' => $sponsor_id
    ));

    $is_group_member = 'N';

    if(strtolower($sponsor_row['type']) == "group") {
        $is_group_member = 'Y';
        if(empty($sponsor_billing_method)) {
            $sponsor_billing_method = "individual";
            
            $sqlBillingType = "SELECT billing_type FROM customer_group_settings where customer_id=:customer_id";
            $resBillingType = $pdo->selectOne($sqlBillingType,array(":customer_id"=>$sponsor_id));
            if(!empty($resBillingType)){
                $sponsor_billing_method = $resBillingType['billing_type'];
            }

            if(!in_array($sponsor_billing_method,array('TPA','list_bill'))){
                $error_reporting_arr[] = "Other payment mode not allow except list bill or TPA bill for import member's products";
                $is_valid = false;
            }
        }
    } else {
        $sponsor_billing_method = "individual";
        
    }

    /*--- enrollment_url ----*/

    $enrollment_url = 'cron_scripts/import_requests.php';

    /*---/enrollment_url ----*/

    // $response['step']=$step;
    // $response['submit_type']=$submit_type;
    // $response['action']=$action;
    $is_add_product = isset($params['is_add_product']) ? $params['is_add_product'] : 0;
    $already_puchase_product = isset($params['already_puchase_product']) ? $params['already_puchase_product'] : '';
    $added_product = isset($params['added_product']) ? $params['added_product'] : 0;
    $product_price = isset($params['product_price']) ? $params['product_price'] : array();
    $product_matrix = isset($params['product_matrix']) ? $params['product_matrix'] : array();
    $product_plan = isset($params['product_plan']) ? $params['product_plan'] : array();
    $healthy_step_fee = isset($params['healthy_step_fee']) ? $params['healthy_step_fee'] : 0;
    $primary_zip = isset($params['primary_zip']) ? $params['primary_zip'] : '';

    $product_list = array();
    $tmpWaive_product_list = array();

    if (!empty($product_matrix))
    {
        $am = array_map('trim', array_keys($product_matrix));
        $bm = array_map(function($v){
            return implode(',',array_filter(explode(',',$v)));
        },$product_matrix);
        $product_matrix = array_combine($am, $bm);
        
        foreach ($product_matrix as $product_id => $matrix_id)
        {
            if (!empty($matrix_id))
            {
                array_push($product_list, $product_id);
            }
        }
    }
    $response['product_list'] = !empty($product_list) ? implode(",", $product_list) : '';

    $only_waive_products = false;
    if(strtolower($sponsor_row['type'])=='group'){
        $waive_checkbox = !empty($params['waive_coverage']) ? $params['waive_coverage'] : array();
        $waive_coverage_reason = !empty($params['waive_coverage_reason']) ? $params['waive_coverage_reason'] : array();
        $waive_coverage_other_reason = !empty($params['waive_coverage_other_reason']) ? $params['waive_coverage_other_reason'] : array();
        if(!empty($waive_checkbox)){

            $group_waive_product=isset($params['waive_products'])?$params['waive_products']:array();

            if(!empty($group_waive_product)){
                $group_product_list= $MemberEnrollment->getGroupWaiveProductList($waive_checkbox,$group_waive_product);
                if(!empty($group_waive_product)  && count($product_list) == 0){
                    $only_waive_products = true;
                }
                $product_list = array_merge($product_list,$group_product_list); 
                $tmpWaive_product_list = array_merge($tmpWaive_product_list,$group_product_list); 
            }
            
        }
    }

    $combination_products = $MemberEnrollment->getCombinationProducts($product_matrix, $sponsor_id);

    //$riderProduct = $MemberEnrollment->getRiderProducts($product_matrix,$sponsor_id);
    $riderProduct = array();

    if (isset($_GET['quote_prds']))
    {
        if (!empty($added_product))
        {
            $added_product = explode(',', $added_product);
            foreach ($added_product as $key => $prd_id)
            {
                if (!empty($prd_id) && !empty($combination_products[$prd_id]))
                {
                    $response['combination_products'][$prd_id] = $combination_products[$prd_id];
                }
            }
        }
    }
    else
    {
        if (!empty($added_product) && !empty($combination_products[$added_product]))
        {
            $response['combination_products'] = $combination_products[$added_product];
        }
    }

    if (!empty($added_product) && !empty($riderProduct) && !empty($riderProduct[$added_product]))
    {
        $response['riderProduct'] = $riderProduct[$added_product];
    }

    $display_contribution='N';
    $groupCoverageContributionArr=array();

if($is_group_member == 'Y') {
    $group_coverage_period_id = !empty($params['coverage_period']) ? $params['coverage_period'] : '';
    $enrolle_class = !empty($params['enrolle_class']) ? $params['enrolle_class'] : '';
    $relationship_to_group = !empty($params['relationship_to_group']) ? $params['relationship_to_group'] : '';
    $relationship_date = !empty($params['relationship_date']) ? $params['relationship_date'] : '';
    $termination_date = !empty($params['termination_date']) ? $params['termination_date'] : '';

    $sqlCoveragePeriod="SELECT gcc.*,gc.pay_period, gco.display_contribution_on_enrollment 
        FROM group_coverage_period_offering gco 
        JOIN group_classes gc ON (gc.id=gco.class_id and gc.is_deleted='N')
        LEFT JOIN group_coverage_period_contributions gcc on(gcc.group_coverage_period_offering_id=gco.id AND gcc.is_deleted='N') 
        where gco.is_deleted='N' AND gco.group_coverage_period_id=:group_coverage_period_id AND gco.group_id=:group_id AND gco.class_id=:class_id";
    $sqlCoveragePeriodWhere=array(':group_id'=>$sponsor_id,':class_id'=>$enrolle_class,':group_coverage_period_id'=>$group_coverage_period_id);
    $resCovergaePeriod=$pdo->select($sqlCoveragePeriod,$sqlCoveragePeriodWhere);

    
    if($resCovergaePeriod){
        foreach ($resCovergaePeriod as $key => $value) {
            $display_contribution=$value['display_contribution_on_enrollment'];
            $groupCoverageContributionArr[$value['product_id']][$value['plan_id']]['type']=$value['type'];
            $groupCoverageContributionArr[$value['product_id']][$value['plan_id']]['contribution']=$value['con_value'];
            $groupCoverageContributionArr[$value['product_id']][$value['plan_id']]['pay_period']=$value['pay_period'];
            $groupCoverageContributionArr['pay_period']['pay_period']=$value['pay_period'];
        }
    }
}
    // pre_print($groupCoverageContributionArr);
    $premium_products = $MemberEnrollment->getProductDetails($product_price,$product_matrix,$product_plan,$groupCoverageContributionArr);
    $premium_products_total = $premium_products['total'];
    unset($premium_products['total']);

    $linked_Fee = $MemberEnrollment->getLinkedFee($product_matrix, $sponsor_id);

    $linked_Fee_total = $linked_Fee['total'];
    unset($linked_Fee['total']);

    $membership_Fee = $MemberEnrollment->getMembershipFee($product_matrix, $customer_id, $primary_zip);
    $membership_Fee_total = $membership_Fee['total'];
    unset($membership_Fee['total']);

    if ($is_add_product == 1 || $enrollmentLocation == "self_enrollment_site")
    {
        $healthyStepFee = array();
    }
    else
    {
        $healthyStepFee = $MemberEnrollment->getHealthyStepFee($product_matrix, $sponsor_id);
    }

    $healthy_step_fee_total = 0;
    $healthy_step_fee_detail = '';
    $addedHealthyStep = array();

    if($is_group_member == 'Y'){
        $healthyStepFee=array();
    }
    if (!empty($healthyStepFee))
    {
        foreach ($healthyStepFee as $key => $value)
        {
            if ($value['product_id'] == $healthy_step_fee)
            {
                $healthy_step_fee_total = $value['price'];
                $healthy_step_fee_detail = $value['product_name'];
                $addedHealthyStep[$value['product_id']] = $value;
            }
        }
    }

    $sub_total = $premium_products_total + $linked_Fee_total + $membership_Fee_total;
    $sub_products_count = count($premium_products) + count($linked_Fee) + count($membership_Fee);

    $serviceFee = $MemberEnrollment->getServiceFee($product_matrix, $sponsor_id, $sub_total);
    $service_fee_total = $serviceFee['total'];
    unset($serviceFee['total']);

    if($is_group_member == 'Y'){
        $service_fee_total = 0;
        $serviceFee=array();
    }

    $order_total = $sub_total + $service_fee_total + $healthy_step_fee_total;

    $order_total = array(
        "premium_products_total" => $premium_products_total,
        "linked_Fee_total" => $linked_Fee_total,
        "membership_Fee_total" => $membership_Fee_total,
        "sub_total" => $sub_total,
        "service_fee" => $service_fee_total,
        "healthy_step_fee" => $healthy_step_fee_total,
        "grand_total" => $order_total,
    );

    $purchase_products_array = array();

    if (!empty($premium_products))
    {
        foreach ($premium_products as $product_id => $row)
        {

            if (!isset($purchase_products_array[$row['product_id']]))
            {
                if($row['product_id'] != ''){
                    $purchase_products_array[$row['product_id']] = $row;
                    $purchase_products_array[$row['product_id']]['qty'] = 1;
                }
            }
            else
            {
                $purchase_products_array[$row['product_id']]['qty'] = $purchase_products_array[$row['product_id']]['qty'] + 1;
            }
        }
    }
    if (!empty($linked_Fee))
    {
        foreach ($linked_Fee as $key => $row)
        {
            if (!isset($purchase_products_array[$row['product_id']]))
            {
                if($row['product_id'] != ''){    
                    $purchase_products_array[$row['product_id']] = $row;
                    $purchase_products_array[$row['product_id']]['qty'] = 1;
                }
            }
            else
            {
                $purchase_products_array[$row['product_id']]['qty'] = $purchase_products_array[$row['product_id']]['qty'] + 1;
            }
        }
    }
    if (!empty($membership_Fee))
    {
        foreach ($membership_Fee as $key => $row)
        {
            if (!isset($purchase_products_array[$row['product_id']]))
            {   
                if($row['product_id'] != ''){
                    $purchase_products_array[$row['product_id']] = $row;
                    $purchase_products_array[$row['product_id']]['qty'] = 1;
                }
            }
            else
            {
                $purchase_products_array[$row['product_id']]['qty'] = $purchase_products_array[$row['product_id']]['qty'] + 1;
            }
        }
    }

    if (!empty($addedHealthyStep))
    {
        foreach ($addedHealthyStep as $key => $row)
        {
            if ($key == $healthy_step_fee)
            {
                if (!isset($purchase_products_array[$row['product_id']]))
                {
                    if($row['product_id'] != ''){
                        $purchase_products_array[$row['product_id']] = $row;
                        $purchase_products_array[$row['product_id']]['qty'] = 1;
                    }
                }
                else
                {
                    $purchase_products_array[$row['product_id']]['qty'] = $purchase_products_array[$row['product_id']]['qty'] + 1;
                }
            }

        }
    }
    if (!empty($serviceFee))
    {
        foreach ($serviceFee as $key => $row)
        {
            if (!isset($purchase_products_array[$row['product_id']]))
            {   
                if($row['product_id'] != ''){
                    $purchase_products_array[$row['product_id']] = $row;
                    $purchase_products_array[$row['product_id']]['qty'] = 1;
                }
            }
            else
            {
                $purchase_products_array[$row['product_id']]['qty'] = $purchase_products_array[$row['product_id']]['qty'] + 1;
            }
        }
    }

    //********** step3 varible intialization code start **********************
    $primary_fname = isset($params['primary_fname']) ? $params['primary_fname'] : '';
    $primary_lname = isset($params['primary_lname']) ? $params['primary_lname'] : '';
    $primary_SSN = isset($params['primary_SSN']) ? $params['primary_SSN'] : '';
    $primary_phone = isset($params['primary_phone']) ? $params['primary_phone'] : '';
    $primary_address1 = isset($params['primary_address']) ? $params['primary_address'] : '';
    $primary_address2 = isset($params['primary_address2']) ? $params['primary_address2'] : '';
    $primary_city = isset($params['primary_city']) ? $params['primary_city'] : '';
    $primary_state = isset($params['primary_state']) ? $params['primary_state'] : '';
    $primary_zip = isset($params['primary_zip']) ? $params['primary_zip'] : '';
    $primary_email = isset($params['primary_email']) ? $params['primary_email'] : '';
    $primary_birthdate = isset($params['primary_birthdate']) ? $params['primary_birthdate'] : '';
    $primary_gender = isset($params['primary_gender']) ? $params['primary_gender'] : '';
    $primary_benefit_amount_arr = isset($params['primary_benefit_amount']) ? $params['primary_benefit_amount'] : array();
    $primary_annual_salary = isset($params['primary_annual_salary']) ? $params['primary_annual_salary'] : array();
    $primary_monthly_benefit_amount_arr = isset($params['primary_monthly_benefit_amount']) ? $params['primary_monthly_benefit_amount'] : array();
    $primary_monthly_salary_percentage_arr = isset($params['primary_monthly_salary_percentage']) ? $params['primary_monthly_salary_percentage'] : array();
    $primary_in_patient_benefit_arr = isset($params['primary_in_patient_benefit']) ? $params['primary_in_patient_benefit'] : array();
	$primary_out_patient_benefit_arr = isset($params['primary_out_patient_benefit']) ? $params['primary_out_patient_benefit'] : array();
	$primary_monthly_income_arr = isset($params['primary_monthly_income']) ? $params['primary_monthly_income'] : array();
    $primary_benefit_percentage_arr = isset($params['primary_benefit_percentage']) ? $params['primary_benefit_percentage'] : array();
    $spouse_benefit_amount_arr = isset($params['spouse_benefit_amount']) ? $params['spouse_benefit_amount'] : array();
    $spouse_in_patient_benefit_arr = isset($params['spouse_in_patient_benefit']) ? $params['spouse_in_patient_benefit'] : array();
	$spouse_out_patient_benefit_arr = isset($params['spouse_out_patient_benefit']) ? $params['spouse_out_patient_benefit'] : array();
	$spouse_monthly_income_arr = isset($params['spouse_monthly_income']) ? $params['spouse_monthly_income'] : array();
    $spouse_benefit_percentage_arr = isset($params['spouse_benefit_percentage']) ? $params['spouse_benefit_percentage'] : array();
    $child_benefit_amount_arr = isset($params['child_benefit_amount']) ? $params['child_benefit_amount'] : array();
    $child_in_patient_benefit_arr = isset($params['child_in_patient_benefit']) ? $params['child_in_patient_benefit'] : array();
	$child_out_patient_benefit_arr = isset($params['child_out_patient_benefit']) ? $params['child_out_patient_benefit'] : array();
	$child_monthly_income_arr = isset($params['child_monthly_income']) ? $params['child_monthly_income'] : array();
    $child_benefit_percentage_arr = isset($params['child_benefit_percentage']) ? $params['child_benefit_percentage'] : array();

    $primary_member_field = $MemberEnrollment->get_primary_member_field($product_list);
    if (!empty($primary_member_field))
    {
        foreach ($primary_member_field as $key => $row)
        {
            ${'primary_'.$row['label']} = isset($params['primary_' . $row['label']]) ? $params['primary_' . $row['label']] : "";
        }
    }

    $spouse_fname = !empty($params['spouse_fname']) ? $params['spouse_fname'] : array();
    $child_fname = !empty($params['child_fname']) ? $params['child_fname'] : array();
    $spouse_assign_products = !empty($params['spouse_assign_products']) ? $params['spouse_assign_products'] : array();
    $combined_spouse_assign_products = array();

    $child_assign_products = !empty($params['child_assign_products']) ? $params['child_assign_products'] : array();
    $combined_child_assign_products = array();

    $spouse_products_list = !empty($params['spouse_products_list']) ? explode(",", $params['spouse_products_list']) : array();
    $spouse_field = array();
    if (!empty($spouse_products_list))
    {
        $spouse_field = $MemberEnrollment->get_spouse_field($spouse_products_list);
    }

    $child_products_list = !empty($params['child_products_list']) ? explode(",", $params['child_products_list']) : array();
    $child_field = array();
    if (!empty($child_products_list))
    {
        $child_field = $MemberEnrollment->get_child_field($child_products_list);
    }

    $is_principal_beneficiary = isset($params['is_principal_beneficiary']) ? $params['is_principal_beneficiary'] : '';
    $is_contingent_beneficiary = isset($params['is_contingent_beneficiary']) ? $params['is_contingent_beneficiary'] : '';

    $productWiseDependentCount = array();
    //********** step3 varible intialization code end   **********************
    //********** step4 varible intialization code start *********************
    $temp_password = '';
    $payment_mode = isset($params['payment_mode']) && $sponsor_billing_method == 'individual' ? $params['payment_mode'] : "";

    $bill_address = isset($params['bill_address']) ? $params['bill_address'] : "";
    $bill_city = isset($params['bill_city']) ? $params['bill_city'] : "";
    $bill_country = 231;
    $bill_state = isset($params['bill_state']) ? $params['bill_state'] : "";
    $bill_zip = isset($params['bill_zip']) ? $params['bill_zip'] : "";

    $web_payment_type = $payment_mode == 'ACH' ? 'ACH' : 'CC';
    if($sponsor_billing_method != 'individual'){
        $web_payment_type = $sponsor_billing_method;
    }

    if($sponsor_billing_method == 'individual' && !$only_waive_products){
    if ($payment_mode == 'CC')
    {
        $name_on_card = isset($params['name_on_card']) ? $params['name_on_card'] : "";
        $card_number = isset($params['card_number']) ? $params['card_number'] : "";
        $card_type = isset($params['card_type']) ? $params['card_type'] : "";
        $expiration = isset($params['expiration']) ? $params['expiration'] : "";
        $expiry_month = '';
        $expiry_year = '';
        if (!empty($expiration))
        {
            $expirtation_details = explode("/", $expiration);
            $expiry_month = $expirtation_details[0];
            $expiry_year = $expirtation_details[1];
        }
        $cvv_no = isset($params['cvv_no']) ? $params['cvv_no'] : "";
        $full_card_number = isset($params['full_card_number']) ? $params['full_card_number'] : "";

        if (empty($card_number) && !empty($full_card_number))
        {
            $card_number = $full_card_number;
        }

    }
    else
    {
        $ach_bill_fname = isset($params['ach_bill_fname']) ? $params['ach_bill_fname'] : "";
        $ach_bill_lname = isset($params['ach_bill_lname']) ? $params['ach_bill_lname'] : "";
        $bankname = isset($params['bankname']) ? $params['bankname'] : "";
        $ach_account_type = isset($params['ach_account_type']) ? $params['ach_account_type'] : "";
        $routing_number = isset($params['routing_number']) ? $params['routing_number'] : "";
        $account_number = isset($params['account_number']) ? $params['account_number'] : "";
        $confirm_account_number = isset($params['confirm_account_number']) ? $params['confirm_account_number'] : "";

        $entered_routing_number = isset($params['entered_routing_number']) ? $params['entered_routing_number'] : "";
        $entered_account_number = isset($params['entered_account_number']) ? $params['entered_account_number'] : "";

        if (empty($account_number) && !empty($entered_account_number))
        {
            $account_number = $entered_account_number;
            $confirm_account_number = $entered_account_number;
        }

        if (empty($routing_number) && !empty($entered_routing_number))
        {
            $routing_number = $entered_routing_number;
        }
    }
    }
    $coverage_dates = isset($params['coverage_date']) ? $params['coverage_date'] : array();
    $lowest_coverage_date = '';
    if (!empty($coverage_dates))
    {
        $lowest_coverage_date = $enrollDate->getLowestCoverageDate($coverage_dates);
    }
    $enroll_withparams_date = isset($params['enroll_withparams_date']) ? $params['enroll_withparams_date'] : "";
    if ($enroll_withparams_date == 'yes')
    {
        $post_date = isset($params['post_date']) ? $params['post_date'] : "";
    }
    $application_type = "imported";

   
    if (isset($bill_country) && $bill_country != '')
    {
        $country_sql = "SELECT * FROM `country` WHERE country_id in($bill_country) ORDER BY country_id DESC";
        $country_res = $pdo->select($country_sql);
    }
    if (isset($country_res) && count($country_res) > 0)
    {
        foreach ($country_res AS $key => $value)
        {
            $countries[$value['country_id']] = $value;
            $country_name[$value['country_id']] = $value['short_name'];
        }
    }
    //********** step4 varible intialization code end   **********************
    //********* step2 validation code start ********************
    if (1)
    {

        if (empty($product_list))
        {
            if($is_group_member == 'Y'){
                if(empty($waive_checkbox)){
                    $error_reporting_arr[] = "Products Missing";
                    // $pdo->insert("import_csv_log", $error_reporting_arr);
                    $is_valid = false;
                }
            }else{
                $error_reporting_arr[] = "Products Missing";
                // $pdo->insert("import_csv_log", $error_reporting_arr);
                $is_valid = false;
            }
        }
        else
        {
            $required_products_error = array();
            $packagedProductArr = array();
            $exclude_products_error = array();
            foreach ($product_list as $key => $product_id)
            {
                if (empty($product_plan[$product_id]))
                {
                    // $validate->setError("product_plan_" . $product_id, "Please select plan");
                    $error_reporting_arr[] = "Products Plans Missing";
                    // $pdo->insert("import_csv_log", $error_reporting_arr);
                    $is_valid = false;
                }

                if (!empty($combination_products[$product_id]))
                {
                    $tmpCombinationProduct = $combination_products[$product_id];
                    if (!empty($tmpCombinationProduct['Required']['product_id']))
                    {
                        $required_product = explode(",", $tmpCombinationProduct['Required']['product_id']);
                        if (!empty($required_product))
                        {
                            foreach ($required_product as $key => $required)
                            {

                                if (!in_array($required, $product_list))
                                {
                                    $requiredProductName = getname('prd_main', $required, 'name', 'id');
                                    $productName = getname('prd_main', $product_id, 'name', 'id');

                                    $required_products_error[$required]['productName'] = $productName;
                                    $required_products_error[$required]['product_id'] = $product_id;
                                    $required_products_error[$required]['requiredProductName'] = $requiredProductName;
                                    $required_products_error[$required]['required_product_id'] = $required;

                                    $requiredProductArr[$product_id][] = $requiredProductName;

                                    // $validate->setError("product_plan_".$product_id, implode(", ",$requiredProductArr[$product_id]) ." is Required for this product");
                                    $error_reporting_arr[] = implode(", ", $requiredProductArr[$product_id]) . " is Required for product ".$productName;
                                    // $pdo->insert("import_csv_log", $error_reporting_arr);
                                    $is_valid = false;
                                }
                            }
                        }
                    }
                    if (!empty($tmpCombinationProduct['Packaged']['product_id']))
                    {
                        $packaged_product = explode(",", $tmpCombinationProduct['Packaged']['product_id']);
                        if (!empty($packaged_product))
                        {
                            $productName = getname('prd_main', $product_id, 'name', 'id');
                            $is_package_prd_found_counter = 0;
                            foreach ($packaged_product as $key => $packaged)
                            {
                                if (in_array($packaged, $product_list))
                                {
                                    $is_package_prd_found_counter++;
                                }
                                else
                                {
                                    $packaged_products_error[$product_id][] = getname('prd_main', $packaged, 'name', 'id');
                                }
                            }
                            if ($is_package_prd_found_counter == 0)
                            {
                                // $validate->setError("product_plan_".$product_id, "This product required at least one packaged product from these products: ".implode(", ",$packaged_products_error[$product_id]));
                                $error_reporting_arr[] = $productName . " product required at least one packaged product from these products: " . implode(", ", $packaged_products_error[$product_id]);
                                // $pdo->insert("import_csv_log", $error_reporting_arr);
                                $is_valid = false;
                            }
                        }
                    }
                    if (!empty($tmpCombinationProduct['Excludes']['product_id']))
                    {
                        $excludes_product = explode(",", $tmpCombinationProduct['Excludes']['product_id']);
                        if (!empty($excludes_product))
                        {
                            $productName = getname('prd_main', $product_id, 'name', 'id');
                            foreach ($excludes_product as $key => $exclude)
                            {
                                if (in_array($exclude, $product_list) && !in_array($exclude,$tmpWaive_product_list))
                                {
                                    $exclude_products_error[$product_id][] = getname('prd_main', $exclude, 'name', 'id');
                                }
                            }
                            if (!empty($exclude_products_error)){
                                $error_reporting_arr[] = implode(", ", $exclude_products_error[$product_id]) . " products are excluded because you added product " . $productName;
                                $is_valid = false;
                            }
                        }
                    }
                }
            }
        }

        if (!empty($healthyStepFee) && !array_key_exists($healthy_step_fee, $addedHealthyStep))
        {
            // $validate->setError("product_cart", "Please select healthy step");
            
        }

        // if (count($validate->getErrors()) > 0 && empty($div_step_error))
        // {
        //     $div_step_error = "products_detail";
        // }
    }
    //********* step2 validation code end   ********************
    //********* step3 validation code start ********************
    if (1)
    {

        if (!empty($primary_member_field))
        {
            $primary_benefit_arr = array('primary_benefit_amount','primary_in_patient_benefit','primary_out_patient_benefit','primary_monthly_income','primary_benefit_percentage');
            foreach ($primary_member_field as $key => $row)
            {
                $prd_question_id = $row['id'];
                $is_required = $row['required'];
                $control_name = 'primary_' . $row['label'];

                $label = $row['display_label'];
                $type = $row['questionType'];
                $control_class = $row['control_class'];
                $questionType = $row['questionType'];
                $product_ids = $row['product_ids'];

                if (in_array($control_name,$primary_benefit_arr))
                {
                    continue;
                }

                $control_value = isset($params[$control_name]) ? $params[$control_name] : "";

                if ($questionType == 'Custom')
                {
                    // $custom_control_name = str_replace($prd_question_id, "", $control_name);
                    $custom_control_name = $control_name;
                    $custom_control_value = isset($params[$custom_control_name]) ? $params[$custom_control_name] : "";
                    $tmpControlName = $custom_control_name;
                    $tmpControlValue = $custom_control_value;
                    ${$tmpControlName} = $custom_control_value;
                }
                else
                {
                    $tmpControlName = $control_name;
                    $tmpControlValue = $control_value;
                    ${$tmpControlName} = $control_value;
                }
                if ($is_required == 'Y')
                {
                    if (is_array(${$tmpControlName}))
                    {
                        if (empty($tmpControlValue))
                        {
                            // $validate->setError($control_name,$label.' is required');
                            $error_reporting_arr[] = 'Primary '. $label . ' is required';
                            // $pdo->insert("import_csv_log", $error_reporting_arr);
                            $is_valid = false;
                        }
                    }
                    else
                    {
                        // $validate->string(array('required' => true, 'field' => $control_name, 'value' => $tmpControlValue), array('required' => $label.' is required'));
                        if (empty($control_name) || empty($tmpControlValue)){
	                        $error_reporting_arr[] = 'Primary '. $label . ' is required';
	                        // $pdo->insert("import_csv_log", $error_reporting_arr);
	                        $is_valid = false;
	                    }
                    }
                }

                // if($control_name == "primary_address1" && !empty($control_value) && $params['is_valid_address'] !='Y'){

                // 	$validate->setError("primary_address1","Valid Address is required");
                // }
                // if($control_class == "dob" && !empty($control_value)){
                // 	if (!$validate->getError($control_name)) {
                // 		list($mm, $dd, $yyyy) = explode('/', $control_value);
                // 		if (!checkdate($mm, $dd, $yyyy)) {
                // 			$validate->setError($control_name, 'Valid Date is required');
                // 		}
                // 	}
                // }
                if ($questionType == 'Custom')
                {
                    $productNames = "";
                    if (!empty($product_ids))
                    {
                        $sqlProduct = "SELECT GROUP_CONCAT(name) as productNames FROM prd_main where id in ($product_ids)";
                        $resProduct = $pdo->selectOne($sqlProduct);

                        if (!empty($resProduct) && !empty($resProduct['productNames']))
                        {
                            $productNames = $resProduct['productNames'];
                        }
                    }
                    // $custom_control_name = str_replace($prd_question_id, "", $control_name);
                    $custom_control_name = $control_name;
                    $custom_control_value = isset($params[$custom_control_name]) ? $params[$custom_control_name] : "";
                    if (!empty($custom_control_value))
                    {
                        if (is_array($custom_control_value))
                        {
                            $tmpIncr = " AND answer in ('" . implode("','", $custom_control_value) . "')";
                        }
                        else
                        {
                            $tmpIncr = " AND answer = '" . $custom_control_value . "'";
                        }

                        $sqlAnswer = "SELECT answer,answer_eligible FROM prd_enrollment_answers where prd_question_id=:prd_question_id AND is_deleted='N' $tmpIncr";
                        $cresAnswer = $pdo->selectOne($sqlAnswer, array(
                            ":prd_question_id" => $prd_question_id
                        ));

                        $sqlAnswer = "SELECT answer,answer_eligible FROM prd_enrollment_answers where prd_question_id=:prd_question_id AND is_deleted='N' AND answer_eligible = 'N' $tmpIncr";
                        $resAnswer = $pdo->select($sqlAnswer, array(
                            ":prd_question_id" => $prd_question_id
                        ));

                        if (!empty($resAnswer) || empty($cresAnswer))
                        {
                            // $validate->setError($control_name,"Answer is not eligible For <b>".$productNames."</b>");
                            $error_reporting_arr[] = "Primary Custom Question: ". $label. ", Answer is not eligible For <b>" . $productNames . "</b>";
                            // $pdo->insert("import_csv_log", $error_reporting_arr);
                            $is_valid = false;
                        }
                    }

                }
            }
            /*if(!$validate->getError('primary_phone')){
            $tmp_primary_phone = phoneReplaceMain($primary_phone);
            $response['tmp_primary_phone'] = $tmp_primary_phone;
            $where_select_phone = array(':cell_phone' => $tmp_primary_phone);
            $incr = "";
            if(!empty($customer_id)){
            $incr .= " AND id!=:id";
            $where_select_phone[":id"] = $customer_id;
            }
            $selectPhone = "SELECT id,cell_phone FROM customer WHERE cell_phone=:cell_phone $incr AND type='Customer' AND is_deleted='N'";
            $resultPhone = $pdo->selectOne($selectPhone, $where_select_phone);
            if (!empty($resultPhone)) {
            $validate->setError("primary_phone", "This phone is already exist");
            }
            
            if(!$validate->getError('primary_phone')){
            $where_select_phone = array(':cell_phone' => $tmp_primary_phone);
            $incr = "";
            if(!empty($lead_id)){
            $incr .= " AND id!=:id";
            $where_select_phone[":id"] = $lead_id;
            }
            $selectPhone = "SELECT id,cell_phone FROM leads WHERE cell_phone=:cell_phone $incr AND is_deleted='N' ";
            $resultPhone = $pdo->selectOne($selectPhone, $where_select_phone);
            if (!empty($resultPhone)) {
            $validate->setError("primary_phone", "This phone is already exist.");
            }
            }
            }*/
        }

        if (!empty($spouse_fname))
        {
            foreach ($spouse_fname as $countKey => $spouseArr)
            {
                if (empty($spouse_assign_products[$countKey]))
                {
                    // $validate->setError("spouse_assign_products","Please Select Product");
                    $error_reporting_arr[] = "Product is missing for spouse";
                    // $pdo->insert("import_csv_log", $error_reporting_arr);
                    $is_valid = false;
                }
                else
                {
                    foreach ($spouse_assign_products[$countKey] as $key => $product_id)
                    {
                        array_push($combined_spouse_assign_products, $product_id);
                        $productWiseDependentCount[$product_id]['Spouse'] = isset($productWiseDependentCount[$product_id]['Spouse']) ? $productWiseDependentCount[$product_id]['Spouse'] + 1 : 1;
                    }
                }
            }
        }


        if (!empty($child_fname))
        {
            foreach ($child_fname as $countKey => $childArr)
            {
                if (empty($child_assign_products[$countKey]))
                {
                    // $validate->setError("child_assign_products_".$countKey,"Please Select Product");
                    $error_reporting_arr[] = "Product is missing for child";
                    // $pdo->insert("import_csv_log", $error_reporting_arr);
                    $is_valid = false;
                }
                else
                {
                    foreach ($child_assign_products[$countKey] as $key => $product_id)
                    {
                        array_push($combined_child_assign_products, $product_id);
                        $productWiseDependentCount[$product_id]['Child'] = isset($productWiseDependentCount[$product_id]['Child']) ? $productWiseDependentCount[$product_id]['Child'] + 1 : 1;
                    }
                }
            }
        }

        $dependent_final_array = array();
        //********* Dependent Validation  code start ********************
        if (!empty($product_list))
        {
            foreach ($product_list as $key => $productID)
            {
                $tmpDependent = array();
                $product_plan_id = $product_plan[$productID];
                $product_matrix_id = $product_matrix[$productID];

                $sqlProducts = "SELECT name,family_plan_rule,is_children_age_restrictions,children_age_restrictions_from,children_age_restrictions_to,is_spouse_age_restrictions,spouse_age_restrictions_from,spouse_age_restrictions_to FROM prd_main where id=:id";
                $resProducts = $pdo->selectOne($sqlProducts, array(
                    ":id" => $productID
                ));

                $product_name = $resProducts['name'];
                $family_plan_rule = $resProducts['family_plan_rule'];

                $spouse_dependent = !empty($productWiseDependentCount[$productID]['Spouse']) ? $productWiseDependentCount[$productID]['Spouse'] : 0;
                $child_dependent = !empty($productWiseDependentCount[$productID]['Child']) ? $productWiseDependentCount[$productID]['Child'] : 0;
                $totalDependent = $spouse_dependent + $child_dependent;

                if ($product_plan_id == 2)
                {
                    if (!in_array($productID, $combined_child_assign_products))
                    {
                        // $validate->setError("dependent_general", "Add Child For Product <b>".$product_name."</b>");
                        $error_reporting_arr[] = "Add Child For Product <b>" . $product_name . "</b>";
                        // $pdo->insert("import_csv_log", $error_reporting_arr);
                        $is_valid = false;
                    }
                }
                else if ($product_plan_id == 3)
                {

                    if (!in_array($productID, $combined_spouse_assign_products))
                    {
                        // $validate->setError("dependent_general", "Add Spouse For Product <b>".$product_name."</b>");
                        $error_reporting_arr[] = "Add Spouse For Product <b>" . $product_name . "</b>";
                        // $pdo->insert("import_csv_log", $error_reporting_arr);
                        $is_valid = false;
                    }
                }
                else if ($product_plan_id == 4)
                {
                    if ($family_plan_rule == "Spouse And Child")
                    {
                        if ($spouse_dependent == 0)
                        {
                            // $validate->setError('dependent_general', "Add Spouse For Product <b>".$product_name."</b>");
                            $error_reporting_arr[] = "Add Spouse For Product <b>" . $product_name . "</b>";
                            // $pdo->insert("import_csv_log", $error_reporting_arr);
                            $is_valid = false;
                        }
                        if ($child_dependent == 0)
                        {
                            // $validate->setError('dependent_general', "Add Child For Product <b>".$product_name."</b>");
                            $error_reporting_arr[] = "Add Child For Product <b>" . $product_name . "</b>";
                            // $pdo->insert("import_csv_log", $error_reporting_arr);
                            $is_valid = false;
                        }

                    }
                    else if ($family_plan_rule == "Minimum One Dependent")
                    {
                        if ($spouse_dependent == 0 && $child_dependent == 0)
                        {
                            // $validate->setError('dependent_general', "Any One Dependent is required For <b>".$product_name."</b>");
                            $error_reporting_arr[] = "Any One Dependent is required For <b>" . $product_name . "</b>";
                            // $pdo->insert("import_csv_log", $error_reporting_arr);
                            $is_valid = false;
                        }

                    }
                    else if ($family_plan_rule == "Minimum Two Dependent")
                    {
                        if ($totalDependent < 2)
                        {
                            // $validate->setError('dependent_general', "Minimum Two Dependent is required For <b>".$product_name."</b>");
                            $error_reporting_arr[] = "Minimum Two Dependent is required For <b>" . $product_name . "</b>";
                            // $pdo->insert("import_csv_log", $error_reporting_arr);
                            $is_valid = false;
                        }
                    }
                }
                else if ($product_plan_id == 5)
                {
                    if ($spouse_dependent == 0 && $child_dependent == 0)
                    {
                        // $validate->setError('dependent_general', "Any One Dependent is required For <b>".$product_name."</b>");
                        $error_reporting_arr[] = "Any One Dependent is required For <b>" . $product_name . "</b>";
                        // $pdo->insert("import_csv_log", $error_reporting_arr);
                        $is_valid = false;
                    }
                    if ($totalDependent > 1)
                    {
                        // $validate->setError('dependent_general', "Only One Dependent is required For <b>".$product_name."</b>");
                        $error_reporting_arr[] = "Only One Dependent is required For <b>" . $product_name . "</b>";
                        // $pdo->insert("import_csv_log", $error_reporting_arr);
                        $is_valid = false;
                    }
                }

                if (!empty($spouse_dependent))
                {
                    if (!empty($spouse_assign_products))
                    {
                        foreach ($spouse_assign_products as $spouseKey => $spouseArr)
                        {
                            if (is_array($spouseArr) && !in_array($productID, $spouseArr))
                            {
                                continue;
                            }

                            $tmpDependent[$spouseKey]['dependent_product_list'] = $spouse_products_list;
                            $tmpDependent[$spouseKey]['dependent_relation_input'] = 'spouse';
                            $tmpDependent[$spouseKey]['relation'] = 'Spouse';
                            $tmpDependent[$spouseKey]['dependent_id'] = $spouseKey;
                            $tmpDependent[$spouseKey]['cd_profile_id'] = isset($params['spouse_cd_profile_id'][$spouseKey]) ? $params['spouse_cd_profile_id'][$spouseKey] : 0;

                            if (!empty($spouse_field))
                            {
                                $spouse_benefit_arr = array('spouse_benefit_amount','spouse_in_patient_benefit','spouse_out_patient_benefit','spouse_monthly_income','spouse_benefit_percentage');
                                foreach ($spouse_field as $field_key => $row)
                                {
                                    $prd_question_id = $row['id'];
                                    $is_required = $row['required'];
                                    $control_name = 'spouse_' . $row['label'];
                                    $label = $row['display_label'];
                                    $control_value = isset($params[$control_name][$spouseKey]) ? $params[$control_name][$spouseKey] : "";

                                    ${$control_name} = $control_value;
                                    $control_class = $row['control_class'];
                                    $questionType = $row['questionType'];

                                    if (in_array($control_name,$spouse_benefit_arr))
                                    {
                                        continue;
                                    }

                                    if ($questionType == 'Custom')
                                    {
                                        // $custom_control_name = str_replace($prd_question_id, "", $control_name);
                                        $custom_control_name = $control_name;
                                        $custom_control_value = isset($params[$custom_control_name][$spouseKey]) ? $params[$custom_control_name][$spouseKey] : "";
                                        $tmpControlName = $custom_control_name;
                                        $tmpControlValue = $custom_control_value;
                                        ${$tmpControlName} = $custom_control_value;
                                    }
                                    else
                                    {
                                        $tmpControlName = $control_name;
                                        $tmpControlValue = $control_value;
                                        ${$tmpControlName} = $control_value;
                                    }

                                    if ($is_required == 'Y')
                                    {
                                        if (is_array(${$tmpControlName}))
                                        {
                                            if (empty($tmpControlValue))
                                            {
                                                // $validate->setError($control_name,$label.' is required');
                                                $error_reporting_arr[] = 'Spouse '. $label . ' is required';
                                                // $pdo->insert("import_csv_log", $error_reporting_arr);
                                                $is_valid = false;

                                            }
                                        }
                                        else
                                        {
                                            // $validate->string(array('required' => true, 'field' => $control_name, 'value' => $tmpControlValue), array('required' => $label.' is required'));
                                            if (empty($control_name) || empty($tmpControlValue))
                                            {
                                                $error_reporting_arr[] = 'Spouse '. $label . ' is required';
                                                // $pdo->insert("import_csv_log", $error_reporting_arr);
                                                $is_valid = false;
                                            }

                                        }
                                    }

                                    if ($control_class == "dob" && !empty($control_value))
                                    {
                                        if (!empty($control_name))
                                        {
                                            list($mm, $dd, $yyyy) = explode('/', $control_value);

                                            if (!checkdate($mm, $dd, $yyyy))
                                            {
                                                // $validate->setError($control_name, 'Valid Date is required');
                                                $error_reporting_arr[] = 'Valid Birth Date is required';
                                                // $pdo->insert("import_csv_log", $error_reporting_arr);
                                                $is_valid = false;
                                            }
                                        }
                                    }

                                    if ($control_name == "spouse_gender" && !empty($control_value))
                                    {
                                        $tmpDependent[$spouseKey]['dependent_relation'] = getRelation('spouse', $control_value);
                                    }
                                    if ($control_name == 'spouse_birthdate' && !empty($control_value))
                                    {
                                        if (strtotime($control_value) >= strtotime($today_date))
                                        {
                                            // $validate->setError($control_name,"Please Enter Valid Birthdate");
                                            $error_reporting_arr[] = 'Valid Spouse Birth Date is required';
                                            // $pdo->insert("import_csv_log", $error_reporting_arr);
                                            $is_valid = false;
                                        }
                                    }
                                    if ($control_name == 'spouse_email' && !empty($control_value))
                                    {
                                        if (!filter_var($control_value, FILTER_VALIDATE_EMAIL))
                                        {
                                            // $validate->setError($control_name, "Valid Email is required");
                                            $error_reporting_arr[] = 'Valid Spouse Email is required';
                                            // $pdo->insert("import_csv_log", $error_reporting_arr);
                                            $is_valid = false;
                                        }
                                    }

                                    $tmpDependent[$spouseKey][$control_name] = $control_value;

                                    if ($questionType == 'Custom')
                                    {
                                        $productNames = "";
                                        if (!empty($product_ids))
                                        {
                                            $sqlProduct = "SELECT GROUP_CONCAT(name) as productNames FROM prd_main where id in ($product_ids)";
                                            $resProduct = $pdo->selectOne($sqlProduct);

                                            if (!empty($resProduct) && !empty($resProduct['productNames']))
                                            {
                                                $productNames = $resProduct['productNames'];
                                            }
                                        }
                                        // $custom_control_name = str_replace($prd_question_id, "", $control_name);
                                        $custom_control_name = $control_name;
                                        $custom_control_value = isset($params[$custom_control_name][$spouseKey]) ? $params[$custom_control_name][$spouseKey] : "";
                                        if (!empty($custom_control_value))
                                        {
                                            if (is_array($custom_control_value))
                                            {
                                                $tmpIncr = " AND answer in ('" . implode("','", $custom_control_value) . "')";
                                            }
                                            else
                                            {
                                                $tmpIncr = " AND answer = '" . $custom_control_value . "'";
                                            }

                                            $sqlAnswer = "SELECT answer,answer_eligible FROM prd_enrollment_answers where prd_question_id=:prd_question_id AND is_deleted='N' $tmpIncr";
                                            $cresAnswer = $pdo->selectOne($sqlAnswer, array(
                                                ":prd_question_id" => $prd_question_id
                                            ));

                                            $sqlAnswer = "SELECT answer,answer_eligible FROM prd_enrollment_answers where prd_question_id=:prd_question_id AND is_deleted='N' AND answer_eligible = 'N' $tmpIncr";
                                            $resAnswer = $pdo->select($sqlAnswer, array(
                                                ":prd_question_id" => $prd_question_id
                                            ));

                                            if (!empty($resAnswer) || empty($cresAnswer))
                                            {
                                                // $validate->setError($control_name,"Answer is not eligible For <b>".$productNames."</b>");
                                                $error_reporting_arr[] =  "Spouse Custom Question: ".$label.", Answer is not eligible For <b>" . $productNames . "</b>";
                                                // $pdo->insert("import_csv_log", $error_reporting_arr);
                                                $is_valid = false;
                                            }
                                        }

                                    }

                                    if ($control_name == "spouse_birthdate" && !empty($control_value) && $resProducts['is_spouse_age_restrictions'] == 'Y')
                                    {
                                        $ageFrom = $resProducts['spouse_age_restrictions_from'];
                                        $ageTo = $resProducts['spouse_age_restrictions_to'];

                                        $dependentAge = calculateAge(date('Y-m-d', strtotime($control_value)));

                                        if ($dependentAge < $ageFrom)
                                        {
                                            // $validate->setError('dependent_general', 'Spouse must be '.$ageFrom.' years of age for product <b>'.$product_name.'</b>');
                                            $error_reporting_arr[] = 'Spouse must be ' . $ageFrom . ' years of age for product <b>' . $product_name . '</b>';
                                            // $pdo->insert("import_csv_log", $error_reporting_arr);
                                            $is_valid = false;
                                        }
                                        else if ($dependentAge > $ageTo)
                                        {
                                            // $validate->setError('dependent_general', 'Spouse must be younger then '.$ageTo.' years of age for product <b>'.$product_name.'</b>');
                                            $error_reporting_arr[] = 'Spouse must be younger then ' . $ageTo . ' years of age for product <b>' . $product_name . '</b>';
                                            // $pdo->insert("import_csv_log", $error_reporting_arr);
                                            $is_valid = false;
                                        }

                                    }
                                }
                            }
                        }
                    }
                }

                if (!empty($child_dependent))
                {

                    if (!empty($child_assign_products))
                    {
                        foreach ($child_assign_products as $childKey => $childArr)
                        {
                            if (is_array($childArr) && !in_array($productID, $childArr))
                            {
                                continue;
                            }

                            $tmpDependent[$childKey]['dependent_product_list'] = $child_products_list;
                            $tmpDependent[$childKey]['dependent_relation_input'] = 'child';
                            $tmpDependent[$childKey]['relation'] = 'Child';
                            $tmpDependent[$childKey]['dependent_id'] = $childKey;
                            $tmpDependent[$childKey]['cd_profile_id'] = isset($params['child_cd_profile_id'][$childKey]) ? $params['child_cd_profile_id'][$childKey] : 0;

                            if (!empty($child_field))
                            {
                                $child_benefit_arr = array('child_benefit_amount','child_in_patient_benefit','child_out_patient_benefit','child_monthly_income','child_benefit_percentage');
                                foreach ($child_field as $field_key => $row)
                                {
                                    $prd_question_id = $row['id'];
                                    $is_required = $row['required'];
                                    $control_name = 'child_' . $row['label'];
                                    $label = $row['display_label'];
                                    $control_value = isset($params[$control_name][$childKey]) ? $params[$control_name][$childKey] : "";
                                    ${$control_name} = $control_value;
                                    $control_class = $row['control_class'];
                                    $questionType = $row['questionType'];

                                    if(in_array($control_name,$child_benefit_arr))
                                    {
                                        continue;
                                    }

                                    if ($questionType == 'Custom')
                                    {
                                        // $custom_control_name = str_replace($prd_question_id, "", $control_name);
                                        $custom_control_name = $control_name;
                                        $custom_control_value = isset($params[$custom_control_name][$childKey]) ? $params[$custom_control_name][$childKey] : "";
                                        $tmpControlName = $custom_control_name;
                                        $tmpControlValue = $custom_control_value;
                                        ${$tmpControlName} = $custom_control_value;
                                    }
                                    else
                                    {
                                        $tmpControlName = $control_name;
                                        $tmpControlValue = $control_value;
                                        ${$tmpControlName} = $control_value;
                                    }

                                    if ($is_required == 'Y')
                                    {
                                        if (is_array(${$tmpControlName}))
                                        {
                                            if (empty($custom_control_value))
                                            {
                                                // $validate->setError($control_name."_".$childKey,$label.' is required');
                                                $error_reporting_arr[] = 'Child '. $label . ' is required';
                                                // $pdo->insert("import_csv_log", $error_reporting_arr);
                                                $is_valid = false;
                                            }
                                        }
                                        else
                                        {
                                            // $validate->string(array(
                                            //     'required' => true,
                                            //     'field' => $control_name . "_" . $childKey,
                                            //     'value' => $tmpControlValue
                                            // ) , array(
                                            //     'required' => $label . ' is required'
                                            // ));
                                            if (empty($control_name)  || empty($tmpControlValue))
                                            {

                                                $error_reporting_arr[] = 'Child '. $label . ' is required';
                                                // $pdo->insert("import_csv_log", $error_reporting_arr);
                                                $is_valid = false;
                                            }
                                        }
                                    }

                                    if ($control_class == "dob" && !empty($control_value))
                                    {
                                        if (!empty($control_name . "_" . $childKey))
                                        {
                                            list($mm, $dd, $yyyy) = explode('/', $control_value);

                                            if (!checkdate($mm, $dd, $yyyy))
                                            {
                                                // $validate->setError($control_name."_".$childKey, 'Valid Date is required');
                                                $error_reporting_arr[] = 'Valid Birthdate is required';
                                                // $pdo->insert("import_csv_log", $error_reporting_arr);
                                                $is_valid = false;
                                            }
                                        }
                                    }

                                    if ($control_name == "child_gender" && !empty($control_value))
                                    {
                                        $tmpDependent[$childKey]['dependent_relation'] = getRelation('child', $control_value);
                                    }
                                    if ($control_name == 'child_email' && !empty($control_value))
                                    {
                                        if (!filter_var($control_value, FILTER_VALIDATE_EMAIL))
                                        {
                                            // $validate->setError($control_name.'_'.$childKey, "Valid Email is required");
                                            $error_reporting_arr[] = 'Valid Email is required';
                                            // $pdo->insert("import_csv_log", $error_reporting_arr);
                                            $is_valid = false;
                                        }
                                    }
                                    if ($control_name == 'child_birthdate' && !empty($control_value))
                                    {
                                        if (strtotime($control_value) >= strtotime($today_date))
                                        {
                                            // $validate->setError($control_name.'_'.$childKey,"Please Enter Valid Birthdate");
                                            $error_reporting_arr[] = 'Valid Birthdate is required';
                                            // $pdo->insert("import_csv_log", $error_reporting_arr);
                                            $is_valid = false;
                                        }
                                    }

                                    $tmpDependent[$childKey][$control_name] = $control_value;

                                    if ($questionType == 'Custom')
                                    {
                                        $productNames = "";
                                        if (!empty($product_ids))
                                        {
                                            $sqlProduct = "SELECT GROUP_CONCAT(name) as productNames FROM prd_main where id in ($product_ids)";
                                            $resProduct = $pdo->selectOne($sqlProduct);

                                            if (!empty($resProduct) && !empty($resProduct['productNames']))
                                            {
                                                $productNames = $resProduct['productNames'];
                                            }
                                        }

                                        // $custom_control_name = str_replace($prd_question_id, "", $control_name);
                                        $custom_control_name = $control_name;
                                        $custom_control_value = isset($params[$custom_control_name][$childKey]) ? $params[$custom_control_name][$childKey] : "";
                                        if (!empty($custom_control_value))
                                        {
                                            if (is_array($custom_control_value))
                                            {
                                                $tmpIncr = " AND answer in ('" . implode("','", $custom_control_value) . "')";
                                            }
                                            else
                                            {
                                                $tmpIncr = " AND answer = '" . $custom_control_value . "'";
                                            }

                                            $sqlAnswer = "SELECT answer,answer_eligible FROM prd_enrollment_answers where prd_question_id=:prd_question_id AND is_deleted='N' $tmpIncr";
                                            $cresAnswer = $pdo->selectOne($sqlAnswer, array(
                                                ":prd_question_id" => $prd_question_id
                                            ));

                                            $sqlAnswer = "SELECT answer,answer_eligible FROM prd_enrollment_answers where prd_question_id=:prd_question_id AND is_deleted='N' AND answer_eligible = 'N' $tmpIncr";
                                            $resAnswer = $pdo->select($sqlAnswer, array(
                                                ":prd_question_id" => $prd_question_id
                                            ));

                                            if (!empty($resAnswer) || empty($cresAnswer))
                                            {
                                                // $validate->setError($control_name."_".$childKey,"Answer is not eligible For For <b>".$productNames."</b>");
                                                $error_reporting_arr[] =  "Child Custom Question: ".$label. 'Answer is not eligible For For <b>".$productNames."</b>';
                                                // $pdo->insert("import_csv_log", $error_reporting_arr);
                                                $is_valid = false;
                                            }
                                        }

                                    }

                                    if ($control_name == "child_birthdate" && !empty($control_value) && $resProducts['is_children_age_restrictions'] == 'Y')
                                    {
                                        $ageFrom = $resProducts['children_age_restrictions_from'];
                                        $ageTo = $resProducts['children_age_restrictions_to'];

                                        $dependentAge = calculateAge(date('Y-m-d', strtotime($control_value)));
                                        if ($dependentAge < $ageFrom)
                                        {
                                            // $validate->setError('dependent_general', 'Child must be '.$ageFrom.' years of age for product <b>'.$product_name.'</b>');
                                            $error_reporting_arr[] = 'Child must be ' . $ageFrom . ' years of age for product <b>' . $product_name . '</b>';
                                            // $pdo->insert("import_csv_log", $error_reporting_arr);
                                            $is_valid = false;
                                        }
                                        else if ($dependentAge > $ageTo)
                                        {
                                            // $validate->setError('dependent_general', 'Child must be younger then '.$ageTo.' years of age for product <b>'.$product_name.'</b>');
                                            $error_reporting_arr[] = 'Child must be younger then ' . $ageTo . ' years of age for product <b>' . $product_name . '</b>';
                                            // $pdo->insert("import_csv_log", $error_reporting_arr);
                                            $is_valid = false;
                                        }

                                    }
                                }
                            }
                        }
                    }
                }

                if (!empty($tmpDependent))
                {
                    $dependent_final_array[$productID] = array(
                        "product_id" => $productID,
                        "plan_id" => $product_plan_id,
                        "matrix_id" => $product_matrix_id,
                        "child_dependent" => $child_dependent,
                        "spouse_dependent" => $spouse_dependent,
                        "dependent" => $tmpDependent,
                    );
                }

            }
        }
        //********* Dependent Validation  code end   ********************
        //********* Beneficiery Validation  code Start   ********************
        $contingent_beneficiary_percentage = 0;
        if ($is_contingent_beneficiary == 'displayed')
        {
            $contingent_beneficiary_field = $MemberEnrollment->get_contingent_beneficiary_field($product_list);
            $tmpContingent = !empty($params['contingent_queBeneficiaryFullName']) ? $params['contingent_queBeneficiaryFullName'] : array();

            if (!empty($tmpContingent))
            {
                $countCbenfi = 0;
                foreach ($tmpContingent as $contingentKey => $childArr)
                {
                    $countCbenfi++;
                    if (!empty($contingent_beneficiary_field))
                    {
                        foreach ($contingent_beneficiary_field as $field_key => $row)
                        {

                            $is_required = $row['required'];
                            $control_name = 'contingent_' . $row['label'];
                            $label = $row['display_label'];
                            $control_value = isset($params[$control_name][$contingentKey]) ? $params[$control_name][$contingentKey] : "";
                            ${$control_name} = $control_value;
                            $control_class = $row['control_class'];

                            if ($control_name == "contingent_queBeneficiaryAllow3")
                            {
                                continue;
                            }

                            if ($is_required == "Y")
                            {
                                if (is_array($
                                {
                                    $control_name
                                }))
                                {
                                    if (empty($control_value))
                                    {
                                        // $validate->setError($control_name."_".$contingentKey,$label.' is required');
                                        $error_reporting_arr[] = $label . ' is required';
                                        // $pdo->insert("import_csv_log", $error_reporting_arr);
                                        $is_valid = false;
                                    }
                                }
                                else
                                {
                                    // $validate->string(array('required' => true, 'field' => $control_name."_".$contingentKey, 'value' => $control_value), array('required' => $label.' is required'));
                                    if (empty($control_name . "_" . $contingentKey) || empty(${$control_name}))
                                    {
                                        $error_reporting_arr[] = $label . ' is required';
                                        // $pdo->insert("import_csv_log", $error_reporting_arr);
                                        $is_valid = false;
                                    }
                                }
                            }

                            if ($control_class == "dob" && !empty($control_value))
                            {
                                if (!empty($control_name . "_" . $contingentKey))
                                {
                                    list($mm, $dd, $yyyy) = explode('/', $control_value);

                                    if (!checkdate($mm, $dd, $yyyy))
                                    {
                                        // $validate->setError($control_name."_".$contingentKey, 'Valid Date is required');
                                        $error_reporting_arr[] = 'Valid Birthdate is required';
                                        // $pdo->insert("import_csv_log", $error_reporting_arr);
                                        $is_valid = false;
                                    }
                                }
                            }
                            if ($control_name == "contingent_queBeneficiaryPercentage" && $control_value != '')
                            {

                                $contingent_beneficiary_percentage = $contingent_beneficiary_percentage + $control_value;

                            }
                        }
                    }
                }
            }
            if($countCbenfi > 3){
                $error_reporting_arr[] = 'Maximum 3 beneficiary allowed for contingent.';
                $is_valid = false;
            }

            if ($contingent_beneficiary_percentage != 100)
            {
                // $validate->setError("contingent_beneficiary_general", 'Sum of all Contingent Beneficiary percentages must equal 100%');
                $error_reporting_arr[] = 'Sum of all Contingent Beneficiary percentages must equal 100% for member '.$params['rep_id'];;
                // $pdo->insert("import_csv_log", $error_reporting_arr);
                $is_valid = false;
            }
        }
        else if ($is_contingent_beneficiary == "not_displayed")
        {
            //$validate->setError("contingent_beneficiary_general","Add Contingent Beneficiery");
            
        }

        $principal_beneficiary_percentage = 0;
        if ($is_principal_beneficiary == 'displayed')
        {
            $principal_beneficiary_field = $MemberEnrollment->get_principal_beneficiary_field($product_list);
            $tmpPrincipal = !empty($params['principal_queBeneficiaryFullName']) ? $params['principal_queBeneficiaryFullName'] : array();

            if (!empty($tmpPrincipal))
            {
                $countPbenfi = 0;
                foreach ($tmpPrincipal as $principalKey => $childArr)
                {
                    $countPbenfi++;
                    if (!empty($principal_beneficiary_field))
                    {
                        foreach ($principal_beneficiary_field as $field_key => $row)
                        {

                            $is_required = $row['required'];
                            $control_name = 'principal_' . $row['label'];
                            $label = $row['display_label'];
                            $control_value = isset($params[$control_name][$principalKey]) ? $params[$control_name][$principalKey] : "";
                            ${$control_name} = $control_value;
                            $control_class = $row['control_class'];

                            if ($control_name == "principal_queBeneficiaryAllow3")
                            {
                                continue;
                            }
                            if ($is_required == 'Y')
                            {
                                if (is_array(${$control_name}))
                                {
                                    if (empty($control_value))
                                    {
                                        // $validate->setError($control_name."_".$principalKey,$label.' is required');
                                        $error_reporting_arr[] = $label . ' is required';
                                        // $pdo->insert("import_csv_log", $error_reporting_arr);
                                        $is_valid = false;
                                    }
                                }
                                else
                                {
                                    // $validate->string(array('required' => true, 'field' => $control_name."_".$principalKey, 'value' => $control_value), array('required' => $label.' is required'));
                                    if (empty($control_name . "_" . $principalKey) || empty(${$control_name}))
                                    {
                                        $error_reporting_arr[] = $label . ' is required';
                                        // $pdo->insert("import_csv_log", $error_reporting_arr);
                                        $is_valid = false;
                                    }
                                }
                            }

                            if ($control_class == "dob" && !empty($control_value))
                            {
                                if (!empty($control_name . "_" . $principalKey))
                                {
                                    list($mm, $dd, $yyyy) = explode('/', $control_value);

                                    if (!checkdate($mm, $dd, $yyyy))
                                    {
                                        // $validate->setError($control_name."_".$principalKey, 'Valid Date is required');
                                        $error_reporting_arr[] = 'Valid Birthdate is required';
                                        // $pdo->insert("import_csv_log", $error_reporting_arr);
                                        $is_valid = false;
                                    }
                                }
                            }
                            if ($control_name == "principal_queBeneficiaryPercentage" && $control_value != '')
                            {
                                $principal_beneficiary_percentage = $principal_beneficiary_percentage + $control_value;

                            }

                        }
                    }
                }
            }
            if($countPbenfi > 3){
                $error_reporting_arr[] = 'Maximum 3 beneficiary allowed for primary.';
                $is_valid = false;
            }
            if ($principal_beneficiary_percentage != 100)
            {
                // $validate->setError('principal_beneficiary_general', 'Sum of all Principal Beneficiary percentages must equal 100%');
                $error_reporting_arr[] = 'Sum of all Principal Beneficiary percentages must equal 100% for member '.$params['rep_id'];
                // $pdo->insert("import_csv_log", $error_reporting_arr);
                $is_valid = false;
            }
        }
        else if ($is_principal_beneficiary == "not_displayed")
        {

            // $validate->setError("principal_beneficiary_general","Add Principal Beneficiery");
            $error_reporting_arr[] = 'Add Principal Beneficiery';
            // $pdo->insert("import_csv_log", $error_reporting_arr);
            $is_valid = false;
        }
        //********* Beneficiery Validation  code end   ********************
        // if (count($validate->getErrors()) > 0 && empty($div_step_error))
        // {
        //     $div_step_error = "basic_detail";
        // }
    }
    //********* step3 validation code end   ********************
    $product_wise_dependents = array();

    if (isset($dependent_final_array) && !empty($dependent_final_array))
    {
        foreach ($dependent_final_array as $dp)
        {
            $product_wise_dependents[$dp["product_id"]] = $dp["dependent"];
        }
    }
    $PlanIdArr = array();

    if (!empty($purchase_products_array))
    {
        foreach ($purchase_products_array as $key => $product)
        {
            $PlanIdArr[] = $product['matrix_id'];
        }
    }
    // pre_print($purchase_products_array);

    //********* step4 validation code start ********************
    // if ($step >= 4)
    // {

        // $validate->string(array(
        //     'required' => true,
        //     'field' => 'payment_mode',
        //     'value' => $payment_mode
        // ) , array(
        //     'required' => 'Payment Mode is required'
        // ));
        if (empty($payment_mode) && $sponsor_billing_method == 'individual')
            {
                $error_reporting_arr[] = 'Payment Mode is empty';
                // $pdo->insert("import_csv_log", $error_reporting_arr);
                $is_valid = false;
            }
        $sale_type_params = array();
        $sale_type_params['is_renewal'] = 'N';
        if($is_group_member == 'Y'){
            if(!$only_waive_products){
                $payment_master_id = $function_list->get_agent_merchant_detail($PlanIdArr, $sponsor_row['sponsor_id'], "CC",$sale_type_params);
            }
        }else{
            $payment_master_id = $function_list->get_agent_merchant_detail($PlanIdArr, $sponsor_id, $payment_mode, $sale_type_params);
        }
        if($sponsor_billing_method == 'individual' && !$only_waive_products){
        if ($payment_mode == "CC")
        {
            // $validate->string(array('required' => true, 'field' => 'name_on_card', 'value' => $name_on_card), array('required' => 'Name is required'));
            if (empty($name_on_card))
            {
                $error_reporting_arr[] = 'Name on Card is empty';
                // $pdo->insert("import_csv_log", $error_reporting_arr);
                $is_valid = false;
            }

            // $validate->string(array('required' => true, 'field' => 'card_type', 'value' => $card_type), array('required' => 'Select Card Type'));
            if (empty($card_type))
            {
                $error_reporting_arr[] = 'Card Type is empty';
                // $pdo->insert("import_csv_log", $error_reporting_arr);
                $is_valid = false;
            }
            // $validate->digit(array('required' => true, 'field' => 'card_number', 'value' => $card_number), array('required' => 'Card is required', 'invalid' => "Enter valid Card Number","max"=>$MAX_CARD_NUMBER,"min"=>$MIN_CARD_NUMBER));
            if (empty($card_number))
            {
                $error_reporting_arr[] = 'Card Number is empty';
                // $pdo->insert("import_csv_log", $error_reporting_arr);
                $is_valid = false;
            }

            if(!empty($card_number) && !is_valid_luhn($card_number,$card_type)){
                $error_reporting_arr[] = 'Credit Card Number is Invalid';
                $is_valid = false;
            }

            // $validate->string(array('required' => true, 'field' => 'expiration', 'value' => $expiration), array('required' => 'Please select expiration month and year'));
            if (empty($expiration))
            {
                $error_reporting_arr[] = 'expiration date is empty';
                // $pdo->insert("import_csv_log", $error_reporting_arr);
                $is_valid = false;
            }
            $cvv_required = "N";
            if (!empty($payment_master_id))
            {
                $sqlProcessor = "SELECT require_cvv FROM payment_master where id=:id";
                $resProcessor = $pdo->selectOne($sqlProcessor, array(
                    ":id" => $payment_master_id
                ));

                if (!empty($resProcessor))
                {
                    $cvv_required = $resProcessor['require_cvv'];
                }
            }
            if ($cvv_required == 'Y')
            {
                // $validate->digit(array('required' => true, 'field' => 'cvv_no', 'value' => $cvv_no,'min'=>3,'max'=>4), array('required' => 'CVV is required', 'invalid' => "Enter valid CVV"));
                if (empty($cvv_no))
                {
                    $error_reporting_arr[] = 'CVV is empty';
                    // $pdo->insert("import_csv_log", $error_reporting_arr);
                    $is_valid = false;
                }
                
                if(!empty($cvv_no) && !cvv_type_pair($cvv_no,$card_type)){
                    $error_reporting_arr[] = 'Invalid CVV Number';
                    $is_valid = false;
                }
            }

            // if (!$validate->getError("name_on_card") && !ctype_alnum(str_replace(" ","",$name_on_card))) {
            // 	$validate->setError("name_on_card","Enter Valid Name");
            // }
            

            if (!empty($expiration))
            {
                $expirty_date = $expiry_year . '-' . $expiry_month . '-01';

                if (strtotime($expirty_date) <= strtotime($today_date))
                {
                    // $validate->setError("expiration","Valid Expiry Date is required");
                    $error_reporting_arr[] = 'Valid Expiry Date is required';
                    // $pdo->insert("import_csv_log", $error_reporting_arr);
                    $is_valid = false;

                }
            }
        }

        if ($payment_mode == "ACH")
        {
            // $validate->string(array('required' => true, 'field' => 'ach_bill_fname', 'value' => $ach_bill_fname), array('required' => 'First Name is required'));
            if (empty($ach_bill_fname))
            {
                $error_reporting_arr[] = 'ACH First name is empty';
                // $pdo->insert("import_csv_log", $error_reporting_arr);
                $is_valid = false;
            }
            // $validate->string(array('required' => true, 'field' => 'ach_bill_lname', 'value' => $ach_bill_lname), array('required' => 'Last Name is required'));
            // if (empty($ach_bill_lname))
            // {
            //     $error_reporting_arr[] = 'ACH Last name is empty';
                // $pdo->insert("import_csv_log", $error_reporting_arr);
            //     $is_valid = false;
            // }

            // $validate->digit(array('required' => true, 'field' => 'account_number', 'value' => $account_number,'min'=>$MIN_ACCOUNT_NUMBER,'max'=>$MAX_ACCOUNT_NUMBER), array('required' => 'Account number is required', 'invalid' => "Enter valid Account number"));
            if (empty($account_number))
            {
                $error_reporting_arr[] = 'ACH Account Number is empty';
                // $pdo->insert("import_csv_log", $error_reporting_arr);
                $is_valid = false;
            }
            // $validate->digit(array('required' => true, 'field' => 'confirm_account_number', 'value' => $confirm_account_number,'min'=>$MIN_ACCOUNT_NUMBER,'max'=>$MAX_ACCOUNT_NUMBER), array('required' => 'Confirm Account number is required', 'invalid' => "Enter valid Account number"));
            // $validate->digit(array('required' => true, 'field' => 'routing_number', 'value' => $routing_number), array('required' => 'Routing number is required', 'invalid' => "Enter valid Routing number"));
            if (empty($routing_number))
            {
                $error_reporting_arr[] = 'ACH Routing Number is empty';
                // $pdo->insert("import_csv_log", $error_reporting_arr);
                $is_valid = false;
            }

            if (!empty($routing_number))
            {
                if (checkRoutingNumber($routing_number) == false)
                {
                    $error_reporting_arr[] = 'ACH Routing Number is empty';
                    // $pdo->insert("import_csv_log", $error_reporting_arr);
                    $is_valid = false;
                }
            }
            // if($account_number != $confirm_account_number){
            // 	$validate->setError("confirm_account_number", "account number not matched");
            // }
            // $validate->string(array('required' => true, 'field' => 'ach_account_type', 'value' => $ach_account_type), array('required' => 'Account Type is required'));
            if (empty($ach_account_type))
            {
                $error_reporting_arr[] = 'ACH Account Type is empty';
                // $pdo->insert("import_csv_log", $error_reporting_arr);
                $is_valid = false;
            }
            // $validate->string(array('required' => true, 'field' => 'bankname', 'value' => $bankname), array('required' => 'Bank Name is required'));
            if (empty($bankname))
            {
                $error_reporting_arr[] = 'ACH Bank Name is empty';
                // $pdo->insert("import_csv_log", $error_reporting_arr);
                $is_valid = false;
            }
        }

        // $validate->string(array('required' => true, 'field' => 'billing_address', 'value' => $bill_address), array('required' => 'Address is required'));
        if (empty($bill_address))
        {
            $error_reporting_arr[] = 'Billing Address is empty';
            // $pdo->insert("import_csv_log", $error_reporting_arr);
            $is_valid = false;
        }
        // $validate->string(array('required' => true, 'field' => 'billing_address', 'value' => $bill_city), array('required' => 'City is required'));
        if (empty($bill_city))
        {
            $error_reporting_arr[] = 'Billing City is empty';
            // $pdo->insert("import_csv_log", $error_reporting_arr);
            $is_valid = false;
        }
        // $validate->string(array('required' => true, 'field' => 'billing_address', 'value' => $bill_state), array('required' => 'State is required'));
        if (empty($bill_state))
        {
            $error_reporting_arr[] = 'Billing State is empty';
            // $pdo->insert("import_csv_log", $error_reporting_arr);
            $is_valid = false;
        }
        // $validate->string(array('required' => true, 'field' => 'billing_address', 'value' => $bill_zip), array('required' => 'Zip is required'));
        if (empty($bill_zip))
        {
            $error_reporting_arr[] = 'Billing Zip is empty';
            // $pdo->insert("import_csv_log", $error_reporting_arr);
            $is_valid = false;
        }
        }

        //********* Coverage Date Validation  code start ********************
        $extra = array();
        if($is_group_member == 'Y'){
            $extra['is_group_member']=$is_group_member;
            $extra['enrollmentLocation']=$enrollmentLocation;
            $extra['enrolle_class']=$enrolle_class;
            $extra['coverage_period']=$group_coverage_period_id;
            $extra['relationship_to_group']=$relationship_to_group;
            $extra['relationship_date']=$relationship_date;
        }
        $tmpProduct_list=array_diff($product_list, $tmpWaive_product_list);
        $coverage_period = $MemberEnrollment->get_coverage_period($tmpProduct_list, $sponsor_id,$extra);
        if (!empty($coverage_period) && !$only_waive_products)
        {
            foreach ($coverage_period as $key => $coverage)
            {
                if ((empty($coverage_dates[$coverage['product_id']]) || strtotime($coverage_dates[$coverage['product_id']]) < 0))
                {
                    // $validate->setError('coverage_date_'.$coverage['product_id'],'Please select coverage date');
                    $error_reporting_arr[] = 'Coverage Date is required';
                    // $pdo->insert("import_csv_log", $error_reporting_arr);
                    $is_valid = false;
                }
                else
                {
                    $effective_detail = $coverage['coverage_date'];
                    if (strtotime($coverage_dates[$coverage['product_id']]) < strtotime($effective_detail))
                    {
                        // $validate->setError('coverage_date_'.$coverage['product_id'],$coverage_dates[$coverage['product_id']]. 'Coverage date must be greater than or equal to ' . date('m/d/Y', strtotime($effective_detail)));
                        $error_reporting_arr[] = 'Coverage date must be greater than or equal to ' . date('m/d/Y', strtotime($effective_detail));
                        // $pdo->insert("import_csv_log", $error_reporting_arr);
                        $is_valid = false;
                    }
                }
            }
        }
        if(!$is_valid && !empty($error_reporting_arr)){
            $errorArr['reason'] = implode(' or<br>',$error_reporting_arr);
            $pdo->insert("import_csv_log", $errorArr);
        }
        //********* Coverage Date Validation  code end   ********************
        if ($is_valid)
        {

            // if ($step >= 2) {
            //   	$response['status']="success";
            //   	$response['order_total']=$order_total;
            //   	$response['purchase_products_array']=$purchase_products_array;
            //  	}
            //  	if ($step >= 3) {
            //   	$response['order_total']=$order_total;
            //   	$response['purchase_products_array']=$purchase_products_array;
            //   	$response['dependent_array']=json_encode($dependent_final_array);
            //  	}
            if (1)
            {

                // $payment_master_id = 0;

                if (!empty($customer_id))
                {
                    $checkCustomerExistSql = "SELECT id,rep_id FROM customer WHERE id = :id AND is_deleted='N'";
                    $checkCustomerExist = $pdo->selectOne($checkCustomerExistSql, array(
                        ':id' => makeSafe($customer_id)
                    ));
                }
                else
                {
                    $checkCustomerExistSql = "SELECT id,rep_id FROM customer WHERE email = :email AND status IN ('Customer Abandon', 'Pending Quote', 'Pending Validation', 'Post Payment') AND is_deleted='N'";
                    $checkCustomerExist = $pdo->selectOne($checkCustomerExistSql, array(
                        ':email' => makeSafe($primary_email)
                    ));
                }

                if ($enrollment_type == "quote" && !empty($lead_quote_detail_id))
                {
                    $str_plan_ids = implode(",", array_unique($PlanIdArr));
                    $lead_quote_detail_where = array(
                        "clause" => "id=:id",
                        "params" => array(
                            ":id" => $lead_quote_detail_id,
                        ) ,
                    );
                    $pdo->update("lead_quote_details", array(
                        'updated_at' => 'msqlfunc_NOW()',
                        'plan_ids' => $str_plan_ids
                    ) , $lead_quote_detail_where);
                }
                if ($checkCustomerExist)
                {
                    $customer_id = $checkCustomerExist["id"];
                    $customer_rep_id = $checkCustomerExist["rep_id"];
                }

                //********* File Upload code start ********************
                // if (in_array($application_type,array('admin')) && $physical_file_name != '') {
                // 	$physical_file_name = time() . $physical_file_name;
                // 	move_uploaded_file($physical_file_tmp_name, $PHYSICAL_DOCUMENT_DIR . $physical_file_name);
                // }
                // if(in_array($application_type,array('voice_verification')) && !empty($voice_physical_name)){
                // 	foreach($voice_physical_name as $key => $name){
                // 		$file_name = $voice_physical_name[$key];
                // 		if ($file_name != '') {
                // 			$voice_physical_file_name = time() . $file_name;
                // 			move_uploaded_file($voice_physical_tmp_name[$key], $PHYSICAL_DOCUMENT_DIR . $voice_physical_file_name);
                // 			$voice_uploaded_fileName[] = $voice_physical_file_name;
                // 		}
                //    	}
                //    }
                //********* File Upload code end   ********************
                $primary_phone = phoneReplaceMain($primary_phone);

                //********* Customer Table code start ********************
                $customerInfo = array(
                    'fname' => $primary_fname,
                    'lname' => $primary_lname,
                    'email' => $primary_email,
                    'type' => 'Customer',
                    'country_id' => 231,
                    'country_name' => "United States",
                    'cell_phone' => $primary_phone,
                    'birth_date' => date('Y-m-d', strtotime($primary_birthdate)) ,
                    'gender' => $primary_gender,
                    'address' => $primary_address1,
                    'address_2' => $primary_address2,
                    'city' => $primary_city,
                    'state' => $primary_state,
                    'zip' => $primary_zip,

                    'updated_at' => 'msqlfunc_NOW()',
                    'sponsor_id' => $sponsor_row['id'],
                    'level' => ($sponsor_row['level'] + 1) ,
                    'upline_sponsors' => ($sponsor_row['upline_sponsors'] . $sponsor_row['id'] . ",") ,
                    'group_company_id' => (!empty($params['group_company_id']) ? $params['group_company_id'] : 0)
                );
                if (!empty($primary_SSN))
                {
                    $customerInfo['ssn'] = "msqlfunc_AES_ENCRYPT('" . str_replace("-", "", $primary_SSN) . "','" . $CREDIT_CARD_ENC_KEY . "')";
                    $customerInfo['last_four_ssn'] = substr(str_replace("-", "", $primary_SSN) , -4);
                }

                // if (!empty($Signature_data)) {
                // 	$data = $Signature_data;
                // 	list($type, $data) = explode(';', $data);
                // 	list(, $data) = explode(',', $data);
                // 	$data = base64_decode($data);
                // 	$signature_file_name = $primary_fname . time() . '.png';
                // 	file_put_contents($SIGNATURE_DIR . $signature_file_name, $data);
                // }
                

                $lead_quote_details_param = array();

                if ($customer_id > 0)
                {
                    $upd_where = array(
                        'clause' => 'id = :id',
                        'params' => array(
                            ':id' => $customer_id,
                        ) ,
                    );
                    $pdo->update('customer', $customerInfo, $upd_where);
                }
                else
                {
                    $customer_rep_id = $MemberEnrollment->get_customer_id();
                    $alternate_id = $params['rep_id'];
                    $customerInfo = array_merge($customerInfo, array(
                        'rep_id' => $customer_rep_id,
                        'display_id' => get_display_id('customer') ,
                        'alternate_id' => $alternate_id ,
                        'status' => "Customer Abandon",
                        "created_at" => "msqlfunc_NOW()",
                        "joined_date" => "msqlfunc_NOW()",
                        "invite_at" => "msqlfunc_NOW()"
                    ));
                    $customer_id = $pdo->insert('customer', $customerInfo);
                }

                $customerSettingParams = array(
                    'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                );
                // if (!empty($signature_file_name)) {
                // 	$customerSettingParams['signature_file'] = $signature_file_name;
                // 	$customerSettingParams['signature_date'] = 'msqlfunc_NOW()';
                // }
                

                if (!empty($primary_height))
                {
                    $primary_height_array = explode(".", $primary_height);
                    $customerSettingParams['height_feet'] = $primary_height_array[0];
                    $customerSettingParams['height_inch'] = $primary_height_array[1];
                }

                if (!empty($primary_weight))
                {
                    $customerSettingParams['weight'] = $primary_weight;
                }
                if (!empty($primary_smoking_status))
                {
                    $customerSettingParams['smoke_use'] = $primary_smoking_status;
                }
                if (!empty($primary_tobacco_status))
                {
                    $customerSettingParams['tobacco_use'] = $primary_tobacco_status;
                }
                if (!empty($primary_benefit_level))
                {
                    $customerSettingParams['benefit_level'] = $primary_benefit_level;
                }
                if (!empty($primary_employment_status))
                {
                    $customerSettingParams['employmentStatus'] = $primary_employment_status;
                }
                if (!empty($primary_salary))
                {
                    $customerSettingParams['salary'] = $primary_salary;
                }
                if (!empty($primary_date_of_hire))
                {
                    $customerSettingParams['hire_date'] = date('Y-m-d', strtotime($primary_date_of_hire));
                }
                if (!empty($primary_hours_per_week))
                {
                    $customerSettingParams['hours_per_week'] = $primary_hours_per_week;
                }
                if (!empty($primary_pay_frequency))
                {
                    $customerSettingParams['pay_frequency'] = $primary_pay_frequency;
                }
                if (!empty($primary_us_citizen))
                {
                    $customerSettingParams['us_citizen'] = $primary_us_citizen;
                }
                if (!empty($primary_no_of_children))
                {
                    $customerSettingParams['no_of_children'] = $primary_no_of_children;
                }
                if (!empty($primary_has_spouse))
                {
                    $customerSettingParams['has_spouse'] = $primary_has_spouse;
                }
                if (!empty($group_coverage_period_id)) {
                    $customerSettingParams['group_coverage_period_id']=$group_coverage_period_id;
                }
                if (!empty($enrolle_class)) {
                    $customerSettingParams['class_id']=$enrolle_class;
                }
                if (!empty($relationship_to_group)) {
                    $customerSettingParams['relationship_to_group']=$relationship_to_group;
                }
                if (!empty($relationship_date)) {
                    $customerSettingParams['relationship_date']=date('Y-m-d',strtotime($relationship_date));
                }

                $sqlCustomerSetting = "SELECT * FROM customer_settings where customer_id=:customer_id";
                $resCustomerSetting = $pdo->selectOne($sqlCustomerSetting, array(
                    ":customer_id" => $customer_id
                ));
                if ($resCustomerSetting)
                {
                    $upd_where = array(
                        'clause' => 'id = :id',
                        'params' => array(
                            ':id' => $resCustomerSetting['id'],
                        ) ,
                    );
                    $pdo->update('customer_settings', $customerSettingParams, $upd_where);
                }
                else
                {
                    $customerSettingParams['customer_id'] = $customer_id;
                    $pdo->insert('customer_settings', $customerSettingParams);
                }

                $primary_queCustom = !empty($params['primary_queCustom']) ? $params['primary_queCustom'] : array();

                if (!empty($primary_queCustom))
                {
                    foreach ($primary_queCustom as $key => $value)
                    {
                        $sqlQue = "SELECT id FROM customer_custom_questions WHERE is_deleted='N' AND question_id=:question_id AND customer_id =:customer_id AND enrollee_type='primary'";
                        $resQue = $pdo->selectOne($sqlQue, array(
                            ":customer_id" => $customer_id,
                            ":question_id" => $key
                        ));

                        if (is_array($value))
                        {
                            $answer = implode(",", $value);
                        }
                        else
                        {
                            $answer = $value;
                        }
                        if(empty($answer)){
                            continue;
                        }
                        $queInsParams = array(
                            "enrollee_type" => 'primary',
                            "customer_id" => $customer_id,
                            "question_id" => $key,
                            "answer" => $answer,
                        );
                        if (!empty($resQue))
                        {
                            $queInswhere = array(
                                "clause" => "id=:id",
                                "params" => array(
                                    ":id" => $resQue['id'],
                                ) ,
                            );
                            $pdo->update("customer_custom_questions", $queInsParams, $queInswhere);
                        }
                        else
                        {
                            $pdo->insert("customer_custom_questions", $queInsParams);
                        }
                    }
                }
                //********* Customer Table code end   ********************
                //********* Lead Table code start ********************
                $leadInfo = array(
                    'customer_id' => $customer_id,
                    'fname' => $primary_fname,
                    'lname' => $primary_lname,
                    'email' => $primary_email,
                    'birth_date' => date('Y-m-d', strtotime($primary_birthdate)) ,
                    'cell_phone' => $primary_phone,
                    'address' => $primary_address1,
                    'city' => $primary_city,
                    'state' => $primary_state,
                    'zip' => $primary_zip,
                    'gender' => $primary_gender,
                    'updated_at' => 'msqlfunc_NOW()',
                );

                if (!empty($group_coverage_period_id)) {
                    $leadInfo['group_coverage_id']=$group_coverage_period_id;
                }
                if (!empty($enrolle_class)) {
                    $leadInfo['group_classes_id']=$enrolle_class;
                }
                if (!empty($relationship_to_group)) {
                    $leadInfo['employee_type']=$relationship_to_group;
                }
                if (!empty($relationship_date)) {
                    $leadInfo['hire_date']=date('Y-m-d',strtotime($relationship_date));
                }

                if (!empty($primary_SSN)) {
                    $leadInfo['ssn_itin_num'] = "msqlfunc_AES_ENCRYPT('" . str_replace("-", "", $primary_SSN) . "','" . $CREDIT_CARD_ENC_KEY . "')";
                    $leadInfo['last_four_ssn'] = substr(str_replace("-", "", $primary_SSN), -4);
                }

                if ($lead_id > 0)
                {
                    $where = array(
                        "clause" => "id=:id",
                        "params" => array(
                            ":id" => $lead_id,
                        ) ,
                    );
                    $pdo->update("leads", $leadInfo, $where);

                    $lead_track = array(
                        'status' => 'Edit Enrollment',
                        'description' => 'Basic Info added',
                    );
    
                    lead_tracking($lead_id,$customer_id,$lead_track);
                }
                else
                {
                    $leadSql = $pdo->selectOne("SELECT id,status FROM leads WHERE lead_type='Member' AND email=:email AND sponsor_id=:sponsor_id AND is_deleted='N'", array(
                        ":email" => $primary_email,
                        ":sponsor_id" => $sponsor_id
                    ));
                    if ($leadSql)
                    {
                        $lead_id = $leadSql['id'];

                        if ($leadSql['status'] != "Converted")
                        {
                            $leadInfo['status'] = "Working";
                        }
                        $where = array(
                            "clause" => "id=:id",
                            "params" => array(
                                ":id" => $lead_id,
                            ) ,
                        );
                        $pdo->update("leads", $leadInfo, $where);

                        $lead_track = array(
                            'status' => 'Exitsting',
                            'description' => 'Existing lead found and basic info updated',
                        );
                    
                        lead_tracking($lead_id,$customer_id,$lead_track);
                    }
                    else
                    {
                        $tempDesc = array(
                            'agent_id' => $sponsor_id,
                            'ip_address' => $_SERVER['SERVER_ADDR'],
                            'note' => 'Lead must complete process'
                        );

                        if (isset($_FILES['physical_upload']['name']) && $_FILES['physical_upload']['name'] != '')
                        {
                            $tempDesc['Document'] = $physical_file_name;
                        }

                        if (isset($voice_physical_name) && !empty($voice_physical_name))
                        {
                            $tempDesc['Document'] = json_encode($voice_uploaded_fileName);
                        }

                        if (isset($voice_verification_system_code) && !empty($voice_verification_system_code))
                        {
                            $tempDesc['system_code'] = $voice_verification_system_code;
                        }

                        $leadInfo = array_merge($leadInfo, array(
                            "customer_id" => $customer_id,
                            "lead_id" => get_lead_id() ,
                            'sponsor_id' => $sponsor_id,
                            'status' => "Working",
                            'lead_type' => 'Member',
                            'generate_type' => "Manual",
                            'opt_in_type' => "Import",
                            'ip_address' => $_SERVER['SERVER_ADDR'],
                            'created_at' => "msqlfunc_NOW()",
                        ));
                        $lead_id = $pdo->insert("leads", $leadInfo);

                        $lead_track = array(
                            'status' => 'Created',
                            'description' => 'New lead created',
                        );
                    
                        lead_tracking($lead_id,$customer_id,$lead_track);

                        $desc = array();
                        $desc['ac_message'] = array(
                            'ac_red_1' => array(
                                'href' => 'lead_details.php?id=' . md5($lead_id) ,
                                'title' => $leadInfo['lead_id'],
                            ) ,
                            'ac_message_1' => ' added by Agent ',
                            'ac_red_2' => array(
                                'href' => 'agent_detail_v1.php?id=' . md5($sponsor_id) ,
                                'title' => $sponsor_row['rep_id'],
                            ) ,
                            'ac_message_2' => ' via Import',
                        );
                        activity_feed(3, $sponsor_id, 'Agent', $lead_id, 'Lead', 'Lead added by Import', $primary_fname, $primary_lname, json_encode($desc) , $REQ_URL);

                        /*
                        $desc = array(
                        'Name' => $primary_fname .' '.$primary_lname,
                        'Email' => $primary_email,
                        'Phone' => $primary_phone,
                        'Added By' => ucfirst($sponsor_row['fname']) . ' '.ucfirst($sponsor_row['lname']) . ' (' . $sponsor_row['rep_id'] . ')',
                        'Ip Address' => $_SERVER['REMOTE_ADDR']
                        );
                        activity_feed(3, $sponsor_id, $sponsor_row["type"], $lead_id, 'leads', 'Lead added by agent assisted enrollment', $primary_fname, $primary_lname,json_encode($desc,true));
                        activity_feed(3, $sponsor_id, 'Agent', $lead_id, 'Lead', 'Lead added by Agent', $primary_fname, $primary_lname, json_encode($tempDesc), $REQ_URL);*/
                    }

                    /*$leadInfo = array_merge($leadInfo, array(
                    "customer_id" => $customer_id,
                    "lead_id" => get_lead_id(),
                    'sponsor_id' => $sponsor_id,
                    'status' => "Working",
                    'lead_type'=>'Member',
                    'generate_type' => "Manual",
                    'opt_in_type' => "Agent Assisted Enrollment",
                    'ip_address' => $_SERVER['SERVER_ADDR'],
                    'created_at' => "msqlfunc_NOW()",
                    ));
                    $lead_id = $pdo->insert("leads", $leadInfo);
                    
                    */
                }
                $lead_sql = "SELECT * FROM leads WHERE id=:id";
                $lead_row = $pdo->selectOne($lead_sql, array(
                    ":id" => $lead_id
                ));
                //********* Lead Table code end   ********************
                //********* Waive Coverage Table code Start   ********************
                if(!empty($waive_checkbox)){
                    $sponsor_type = $sponsor_row["type"];
                    $waive_coverage_id=$MemberEnrollment->waive_coverage_insert($sponsor_id,$sponsor_type,$waive_checkbox,$waive_coverage_reason,$waive_coverage_other_reason,$customer_id,$primary_fname,$primary_lname);
                }
                //********* Waive Coverage Table code end   ******************** 
                $order_display_id = 0;
                if ($order_id > 0)
                {
                    $order_display_id = $order_res['display_id'];
                }
                else
                {
                    $order_display_id = $function_list->get_order_id();
                }

                //********* Payment code start ********************
                $paymentApproved = false;
                $payment_processor = "";
                $decline_log_id = "";
                if($sponsor_billing_method == 'individual'){
                    if (!empty($payment_master_id))
                    {
                        $payment_processor = getname('payment_master', $payment_master_id, 'processor_id');
                    }
                    if (in_array($application_type, array(
                        'imported'
                    )))
                    {

                        if ($enroll_withparams_date == "yes")
                        {
                            $paymentApproved = true;
                            $txn_id = 0;
                        }
                        else if($is_group_member == 'Y' && $only_waive_products){
                            $paymentApproved = true;
                            $txn_id = 0;
                        }
                        else
                        {

                            $api = new CyberxPaymentAPI();

                            if($SITE_ENV!='Live'){
                                $payment_mode = "CC";
                                $card_number = "4111111111111114";
                            }
                            

                            $cc_params = array();
                            $cc_params['customer_id'] = $customer_rep_id;
                            $cc_params['order_id'] = $order_display_id;
                            $cc_params['amount'] = $order_total['grand_total'];
                            $cc_params['description'] = "Product Purchase";
                            $cc_params['firstname'] = ($payment_mode == 'CC' ? $name_on_card : $ach_bill_fname);
                            $cc_params['lastname'] = $payment_mode == 'CC' ? '' : $ach_bill_lname;
                            $cc_params['address1'] = $bill_address;
                            $cc_params['city'] = $bill_city;
                            $cc_params['state'] = $bill_state;
                            $cc_params['zip'] = $bill_zip;
                            $cc_params['country'] = $bill_country;
                            $cc_params['phone'] = $primary_phone;
                            $cc_params['email'] = $primary_email;
                            $cc_params['ipaddress'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
                            $cc_params['processor'] = $payment_processor;

                            if ($payment_mode == "ACH")
                            {
                                $cc_params['firstname'] = $primary_fname;
                                $cc_params['lastname'] = $primary_lname;
                                $cc_params['address1'] = $primary_address1;
                                $cc_params['city'] = $primary_city;
                                $cc_params['state'] = $primary_state;
                                $cc_params['zip'] = $primary_zip;
                                $cc_params['country'] = 'USA';
                                $cc_params['ach_account_type'] = $ach_account_type;
                                $cc_params['ach_routing_number'] = !empty($routing_number) ? $routing_number : $entered_routing_number;
                                $cc_params['ach_account_number'] = !empty($account_number) ? $account_number : $entered_account_number;
                                $cc_params['name_on_account'] = $ach_bill_fname . ' ' . $ach_bill_lname;
                                $cc_params['bankname'] = $bankname;

                                $lead_track = array(
                                    'status' => 'Calling Processor',
                                    'description' => 'Attempt to take charge with ACH Payment Method',
                                );
                            
                                lead_tracking($lead_id,$customer_id,$lead_track);

                                $payment_res = $api->processPaymentACH($cc_params, $payment_master_id);

                                if ($payment_res['status'] == 'Success')
                                {
                                    $paymentApproved = true;
                                    $txn_id = $payment_res['transaction_id'];
                                }
                                else
                                {
                                    $paymentApproved = false;
                                    $txn_id = $payment_res['transaction_id'];
                                    $payment_error = $payment_res['message'];
                                    $cc_params['order_type'] = 'Quote';
                                    $cc_params['browser'] = $BROWSER;
                                    $cc_params['os'] = $OS;
                                    $cc_params['req_url'] = $REQ_URL;
                                    $cc_params['err_text'] = $payment_error;
                                    $decline_log_id = $function_list->credit_card_decline_log($customer_id, $cc_params, $payment_res);
                                }

                                $lead_track = array(
                                    'status' => 'Processor Call End',
                                    'description' => 'Payment status - ' . ($payment_res['status'] == 'Success' ? 'Success' : 'Fail'),
                                );
                            
                                lead_tracking($lead_id,$customer_id,$lead_track);
                            }
                            elseif ($payment_mode == "CC")
                            {
                                $cc_params['ccnumber'] = !empty($card_number) ? $card_number : $full_card_number;
                                $cc_params['card_type'] = $card_type;
                                $cc_params['ccexp'] = str_pad($expiry_month, 2, "0", STR_PAD_LEFT) . substr($expiry_year, -2);

                                if ($cc_params['ccnumber'] == '4111111111111114')
                                {
                                    $paymentApproved = true;
                                    $txn_id = 0;
                                    $payment_res = array(
                                        "status" => "Success",
                                        "transaction_id" => 0,
                                        "message" => "Manual Approved"
                                    );
                                    $lead_track = array(
                                        'status' => 'Manually Approved',
                                        'description' => 'Payment Approved using fake card',
                                    );
                                
                                    lead_tracking($lead_id,$customer_id,$lead_track);
                                }
                                else
                                {
                                    $lead_track = array(
										'status' => 'Calling Processor',
										'description' => 'Attempt to take charge with CC Payment Method',
									);
								
									lead_tracking($lead_id,$customer_id,$lead_track);

                                    $payment_res = $api->processPayment($cc_params, $payment_master_id);
                                    if ($payment_res['status'] == 'Success')
                                    {
                                        $paymentApproved = true;
                                        $txn_id = $payment_res['transaction_id'];
                                    }
                                    else
                                    {
                                        $paymentApproved = false;
                                        $txn_id = $payment_res['transaction_id'];
                                        $payment_error = $payment_res['message'];
                                        $cc_params['order_type'] = 'Quote';
                                        $cc_params['browser'] = $BROWSER;
                                        $cc_params['os'] = $OS;
                                        $cc_params['req_url'] = $REQ_URL;
                                        $cc_params['err_text'] = $payment_error;
                                        $decline_log_id = credit_card_decline_log($customer_id, $cc_params, $payment_res);
                                    }

                                    $lead_track = array(
                                        'status' => 'Processor Call End',
                                        'description' => 'Payment status - ' . ($payment_res['status'] == 'Success' ? 'Success' : 'Fail'),
                                    );
                                
                                    lead_tracking($lead_id,$customer_id,$lead_track);
                                }
                            }
                        }
                    }
                }else if(in_array($sponsor_billing_method,array('TPA','list_bill'))){
                    $paymentApproved = true;
                    $txn_id = 0;

                    $lead_track = array(
						'status' => 'Payment Approved',
						'description' => 'Payment Approved using application type .' . $application_type,
					);
				
					lead_tracking($lead_id,$customer_id,$lead_track);

                }
                //********* Payment code end   ********************
                //********* Order Table code start ********************
                $orderParams = array(
                    'payment_type' => ($sponsor_billing_method == 'individual' ? ($payment_mode == 'ACH' ? 'ACH' : 'CC') : $sponsor_billing_method ),
                    'payment_master_id' => $payment_master_id,
                    'payment_processor' => $payment_processor,
                    'type' => ",Customer Enrollment,",
                    'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                    'browser' => $BROWSER,
                    'os' => $OS,
                    'req_url' => $REQ_URL,
                    'updated_at' => 'msqlfunc_NOW()',
                    'created_at' => 'msqlfunc_NOW()',
                    'product_total' => $order_total['sub_total'],
                    'sub_total' => $order_total['sub_total'],
                    'grand_total' => $order_total['grand_total'],
                    // 'status' => $newStatus,
                    'status' => 'Pending Validation',
                    'order_count' => 1,
                );
                if ($enroll_withparams_date == "yes")
                {
                    $orderParams['post_date'] = date("Y-m-d", strtotime($post_date));
                    $orderParams['future_payment'] = 'Y';
                }
                else
                {
                    //if post date is not setup then add coverage date in post_date field
                    $orderParams['post_date'] = date("Y-m-d", strtotime($lowest_coverage_date));
                    $orderParams['future_payment'] = 'N';
                }

                if (in_array($application_type, array(
                    'imported'
                )))
                {
                    $orderParams['transaction_id'] = $txn_id;
                    $orderParams['payment_processor_res'] = isset($payment_res) ? json_encode($payment_res) : "";

                    $orderParams['status'] = ($payment_mode == "ACH") ? 'Pending Settlement' : 'Payment Approved';
                    if (!$paymentApproved)
                    {
                        $orderParams['status'] = 'Payment Declined';
                    }
                    if ($enroll_withparams_date == "yes")
                    {
                        $orderParams['status'] = 'Post Payment';
                    }
                }

                if (isset($payment_res['review_require']) && $payment_res['review_require'] == 'Y')
                {
                    $orderParams['review_require'] = 'Y';
                }

                if ($order_id > 0)
                {
                    $order_where = array(
                        "clause" => "id=:id",
                        "params" => array(
                            ":id" => $order_id
                        )
                    );
                    if($sponsor_billing_method == 'individual' && !$only_waive_products){
                        $pdo->update("orders", $orderParams, $order_where);
                        $lead_track = array(
                            'status' => 'Existing Order Updated',
                            'description' => 'Existing order found',
                        );
                    
                        lead_tracking($lead_id,$customer_id,$lead_track);
                    }else if($is_group_member == 'Y' && !$only_waive_products && in_array($application_type,array('member'))){
                        $pdo->update("group_orders", $orderParams, $order_where);
                        
                        $lead_track = array(
                            'status' => 'Existing Order Updated',
                            'description' => 'Existing order found',
                        );
                    
                        lead_tracking($lead_id,$customer_id,$lead_track);
                    }
                }
                else
                {
                    $orderParams = array_merge($orderParams, array(
                        'display_id' => $order_display_id,
                        'customer_id' => $customer_id,
                        'created_at' => 'msqlfunc_NOW()',
                        'original_order_date' => 'msqlfunc_NOW()',
                    ));
                    if($sponsor_billing_method == 'individual' && !$only_waive_products){
                        $order_id = $pdo->insert("orders", $orderParams);
                        $lead_track = array(
                            'status' => 'Order Created',
                            'description' => 'New order Created',
                        );
                    
                        lead_tracking($lead_id,$customer_id,$lead_track);
                    }else if($is_group_member == 'Y' && !$only_waive_products && in_array($application_type,array('member'))){
                        $order_id = $pdo->insert("group_orders", $orderParams);

                        $lead_track = array(
                            'status' => 'Order Created',
                            'description' => 'New order Created',
                        );
                    
                        lead_tracking($lead_id,$customer_id,$lead_track);
                    }
                }

                //********* Order Table code end   ********************
                //********* Billing Profile Table code start ********************
                $orderBillingId = 0;
                if($sponsor_billing_method == 'individual' && !$only_waive_products){
                if ($payment_mode == "CC")
                {
                    $billParams = array(
                        'order_id' => $order_id,
                        'customer_id' => $customer_id,
                        'fname' => makeSafe($name_on_card) ,
                        'lname' => '',
                        'email' => makeSafe($primary_email) ,
                        'country_id' => 231,
                        'country' => 'United States',
                        'state' => makeSafe($bill_state) ,
                        'city' => makeSafe($bill_city) ,
                        'zip' => makeSafe($bill_zip) ,
                        'address' => makeSafe($bill_address) ,
                        'cvv_no' => makeSafe($cvv_no) ,
                        'card_no' => makeSafe(substr($card_number, -4)) ,
                        'last_cc_ach_no' => makeSafe(substr($card_number, -4)) ,
                        'card_no_full' => "msqlfunc_AES_ENCRYPT('" . $card_number . "','" . $CREDIT_CARD_ENC_KEY . "')",
                        'card_type' => makeSafe($card_type) ,
                        'expiry_month' => makeSafe($expiry_month) ,
                        'expiry_year' => makeSafe($expiry_year) ,
                        'created_at' => 'msqlfunc_NOW()',
                        'payment_mode' => 'CC',
                    );

                    if (!empty($order_res['billing_id']))
                    {
                        unset($billParams['created_at']);
                        $orderBillingId = $order_res['billing_id'];
                        $pdo->update("order_billing_info", $billParams, array(
                            "clause" => "id=:id",
                            "params" => array(
                                ":id" => $order_res['billing_id']
                            )
                        ));
                        $lead_track = array(
							'status' => 'Billing Info Updated',
							'description' => 'Billing Info Updated',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);
                    }
                    else
                    {
                        
                        $orderBillingId = $pdo->insert("order_billing_info", $billParams);

                        $lead_track = array(
                            'status' => 'Billing Info Created',
                            'description' => 'Billing Info Created',
                        );
                    
                        lead_tracking($lead_id,$customer_id,$lead_track);
                        
                    }

                    $defaultBillingSql = "SELECT id FROM customer_billing_profile WHERE customer_id=:customer_id AND is_default='Y'";
                    $defaultBillingWhere = array(":customer_id" => $customer_id);
                    $default_cb_row = $pdo->selectOne($defaultBillingSql, $defaultBillingWhere);

                    if(empty($default_cb_row)){
                        $billParams['is_default'] = 'Y';
                    }

                    $billParams['ip_address'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
                    $billParams['updated_at'] = 'msqlfunc_NOW()';

                    /*--- We are get billing from this table when verify email/sms ---*/
                    unset($billParams['order_id']);
                    $isCustomerBillingExists = $pdo->selectOne("SELECT * FROM customer_billing_profile WHERE customer_id = :customer_id and is_deleted='N'", array(
                        ':customer_id' => $customer_id
                    ));
                    if (empty($isCustomerBillingExists))
                    {
                        $pdo->insert("customer_billing_profile", $billParams);

                        $lead_track = array(
                            'status' => 'Billing Profile Created',
                            'description' => 'Billing Profile Created',
                        );
                    
                        lead_tracking($lead_id,$customer_id,$lead_track);
                    }
                    else
                    {
                        unset($billParams['created_at']);
                        if(!empty($isCustomerBillingExists['id'])){
                            $pdo->update("customer_billing_profile", $billParams, array(
                                "clause" => "customer_id=:customer_id AND id=:id",
                                "params" => array(
                                    ":customer_id" => $customer_id,
                                    ":id" => $isCustomerBillingExists['id'],
                                )
                            ));
                        } else {
                            $pdo->update("customer_billing_profile", $billParams, array(
                                "clause" => "customer_id=:customer_id",
                                "params" => array(
                                    ":customer_id" => $customer_id,
                                )
                            ));
                        }

                        $lead_track = array(
                            'status' => 'Billing Profile Updated',
                            'description' => 'Billing Profile Updated',
                        );
                    
                        lead_tracking($lead_id,$customer_id,$lead_track);
                    }

                }
                else
                {
                    $billParams = array(
                        'order_id' => $order_id,
                        'customer_id' => $customer_id,
                        'fname' => makeSafe($ach_bill_fname) ,
                        'lname' => makeSafe($ach_bill_lname) ,
                        'email' => makeSafe($primary_email) ,
                        'country_id' => '231',
                        'country' => 'United States',
                        'state' => makeSafe($primary_state) ,
                        'city' => makeSafe($primary_city) ,
                        'zip' => makeSafe($primary_zip) ,
                        'address' => makeSafe($primary_address1) ,
                        'created_at' => 'msqlfunc_NOW()',
                        'payment_mode' => 'ACH',
                        'ach_account_type' => $ach_account_type,
                        'bankname' => $bankname,
                        'last_cc_ach_no' => makeSafe(substr($account_number, -4)) ,
                        'ach_account_number' => "msqlfunc_AES_ENCRYPT('" . $account_number . "','" . $CREDIT_CARD_ENC_KEY . "')",
                        'ach_routing_number' => "msqlfunc_AES_ENCRYPT('" . $routing_number . "','" . $CREDIT_CARD_ENC_KEY . "')",
                    );
                    if (!empty($order_res['billing_id']))
                    {
                        unset($billParams['created_at']);
                        $orderBillingId = $order_res['billing_id'];
                        $pdo->update("order_billing_info", $billParams, array(
                            "clause" => "id=:id",
                            "params" => array(
                                ":id" => $order_res['billing_id']
                            )
                        ));

                        $lead_track = array(
							'status' => 'Billing Info updated',
							'description' => 'Billing Info updated',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);

                    }
                    else
                    {
                        
                        $orderBillingId = $pdo->insert("order_billing_info", $billParams);

                        $lead_track = array(
                            'status' => 'Billing Info Created',
                            'description' => 'Billing Info Created',
                        );
                    
                        lead_tracking($lead_id,$customer_id,$lead_track);
                        
                    }
                    
                    $defaultBillingSql1 = "SELECT id FROM customer_billing_profile WHERE customer_id=:customer_id AND is_default='Y'";
                    $defaultBillingWhere1 = array(":customer_id" => $customer_id);
                    $default_cb_row1 = $pdo->selectOne($defaultBillingSql1, $defaultBillingWhere1);

                    if(empty($default_cb_row1)){
                        $billParams['is_default'] = 'Y';
                    }

                    $billParams['ip_address'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
                    $billParams['updated_at'] = 'msqlfunc_NOW()';

                    /*--- We are get billing from this table when verify email/sms ---*/
                    unset($billParams['order_id']);
                    $isCustomerBillingExists = $pdo->selectOne("SELECT * FROM customer_billing_profile WHERE customer_id = :customer_id and is_deleted='N'", array(
                        "customer_id" => $customer_id
                    ));
                    if (empty($isCustomerBillingExists))
                    {
                        $pdo->insert("customer_billing_profile", $billParams);

                        $lead_track = array(
                            'status' => 'Billing Profile Created',
                            'description' => 'Billing Profile Created',
                        );
                    
                        lead_tracking($lead_id,$customer_id,$lead_track);
                    }
                    else
                    {
                        unset($billParams['created_at']);
                        if(!empty($isCustomerBillingExists['id'])){
                            $pdo->update("customer_billing_profile", $billParams, array(
                                "clause" => "customer_id=:customer_id AND id=:id",
                                "params" => array(
                                    ":customer_id" => $customer_id,
                                    ":id" => $isCustomerBillingExists['id'],
                                )
                            ));
                        } else {
                            $pdo->update("customer_billing_profile", $billParams, array(
                                "clause" => "customer_id=:customer_id",
                                "params" => array(
                                    ":customer_id" => $customer_id
                                )
                            ));
                        }

                        $lead_track = array(
                            'status' => 'Billing Profile Updated',
                            'description' => 'Billing Profile Updated',
                        );
                    
                        lead_tracking($lead_id,$customer_id,$lead_track);
                    }
                }
                }
                
                //********* Physical File code end   ********************
                //********* Order Detail Table code start ********************
                
                
                //***** get minimum end coverage ********
                $endCoverageDateArr = array();
                foreach ($purchase_products_array as $key => $product)
                {
                    if ($product['type'] == 'Fees')
                    {
                        $member_payment_type = $product['payment_type_subscription'];
                        $start_coverage_date = $coverage_dates[$product['fee_product_id']];
                    }
                    else
                    {
                        $member_payment_type = $product['payment_type_subscription'];
                        $start_coverage_date = $coverage_dates[$product['product_id']];
                    }
                    $product_dates = $enrollDate->getCoveragePeriod($start_coverage_date, $member_payment_type);

                    $endCoveragePeriod = date('Y-m-d', strtotime($product_dates['endCoveragePeriod']));
                    array_push($endCoverageDateArr, $endCoveragePeriod);
                }
                //***** get minimum end coverage ********
                

                foreach ($purchase_products_array as $key => $product)
                {
                    $website_id = 0;

                    if ($product['type'] == 'Fees')
                    {
                        $member_payment_type = $product['payment_type_subscription'];
                        $start_coverage_date = $coverage_dates[$product['fee_product_id']];
                    }
                    else
                    {
                        $member_payment_type = $product['payment_type_subscription'];
                        $start_coverage_date = $coverage_dates[$product['product_id']];
                    }
                    $product_dates = $enrollDate->getCoveragePeriod($start_coverage_date, $member_payment_type);

                    $startCoveragePeriod = date('Y-m-d', strtotime($product_dates['startCoveragePeriod']));
                    $endCoveragePeriod = date('Y-m-d', strtotime($product_dates['endCoveragePeriod']));
                    $eligibility_date = !empty($params['effective_date'][$product['product_id']]) ? date('Y-m-d', strtotime($params['effective_date'][$product['product_id']]))  : date('Y-m-d', strtotime($product_dates['eligibility_date']));

                    $shortTermProductDetails = $MemberEnrollment->shortTermDisabilityProductDetails($product['product_id']);
                    $is_short_term_disability_product = 'N';
                    if($shortTermProductDetails){
                        $is_short_term_disability_product = $shortTermProductDetails['is_short_term_disablity_product'];
                    }

                    //********* Website Subcription,Customer enrollment Table code start *********
                    if (in_array($application_type, array(
                        'imported'
                    )))
                    {
                        $web_subscription_data = array(
                            'product_id' => $product['product_id'],
                            'fee_applied_for_product' => !empty($product['fee_product_id']) ? $product['fee_product_id'] : 0,
                            'plan_id' => $product['matrix_id'],
                            'prd_plan_type_id' => $product['plan_id'],
                            'product_code' => $product['product_code'],
                            'product_type' => makeSafe($product['type']) ,
                            'last_purchase_date' => 'msqlfunc_NOW()',
                            'last_order_id' => $order_id,
                            'total_attempts' => 0,
                            'price' => $product['price'],
                            'qty' => $product['qty'],
                            'payment_type' => ($sponsor_billing_method == 'individual' ? ($payment_mode == 'ACH' ? 'ACH' : 'CC') : $sponsor_billing_method ),
                            'updated_at' => 'msqlfunc_NOW()',
                            'termination_date' => NULL,
                            'term_date_set' => NULL,
                            'admin_id' => ($from_admin == 'Y' ? $_SESSION['admin']['id'] : 0) ,
                        );
                        if (!empty($primary_benefit_amount_arr[$product['product_id']]))
                        {
                            $web_subscription_data['benefit_amount'] = $primary_benefit_amount_arr[$product['product_id']];
                        }
                        if ($is_short_term_disability_product == 'Y' && !empty($primary_monthly_benefit_amount_arr[$product['product_id']]))
                        {
                            $web_subscription_data['benefit_amount'] = $primary_monthly_benefit_amount_arr[$product['product_id']];
                        }
                        if(isset($primary_annual_salary)){
							$web_subscription_data['annual_salary'] = $primary_annual_salary;
                        }
                        if(isset($primary_monthly_salary_percentage_arr[$product['product_id']])){
							$web_subscription_data['monthly_benefit_percentage'] = $primary_monthly_salary_percentage_arr[$product['product_id']];
						}
                        if(!empty($primary_in_patient_benefit_arr[$product['product_id']])){
							$web_subscription_data['in_patient_benefit'] = $primary_in_patient_benefit_arr[$product['product_id']];
						}
						if(!empty($primary_out_patient_benefit_arr[$product['product_id']])){
							$web_subscription_data['out_patient_benefit'] = $primary_out_patient_benefit_arr[$product['product_id']];
						}
						if(!empty($primary_monthly_income_arr[$product['product_id']])){
							$web_subscription_data['monthly_income'] = $primary_monthly_income_arr[$product['product_id']];
						}
                        if(!empty($primary_benefit_percentage_arr[$product['product_id']])){
							$web_subscription_data['benefit_percentage'] = $primary_benefit_percentage_arr[$product['product_id']];
						}
                        $member_setting = $memberSetting->get_status_by_payment($paymentApproved,"",($enroll_withparams_date == "yes" ? true : false));

						$web_subscription_data["status"] = $member_setting['policy_status'];
                        // $web_subscription_data["status"] = 'Active';

                        if (!$paymentApproved)
                        {
                            $web_subscription_data['status'] = 'Payment Declined';
                            // if($params['termination_dates'][$product['product_id']]){
                            //     $web_subscription_data['termination_date'] = date('Y-m-d',strtotime($params['termination_dates'][$product['product_id']]));
                            // }
                        }

                        if ($enroll_withparams_date == "yes")
                        {
                            $web_subscription_data['status'] = "Post Payment";
                        }

                        if ($product['payment_type'] == 'Recurring')
                        {
                            $next_purchase_date = $enrollDate->getNextBillingDateFromCoverageList($endCoverageDateArr, $startCoveragePeriod,$customer_id);
                            $web_subscription_data['next_purchase_date'] = date('Y-m-d', strtotime($next_purchase_date));
                        }
                        else
                        {
                            $web_subscription_data['is_onetime'] = 'Y';
                            $web_subscription_data['next_purchase_date'] = date('Y-m-d');
                        }
                        $web_subscription_data['eligibility_date'] = $eligibility_date;

                        $web_subscription_data['start_coverage_period'] = $startCoveragePeriod;
                        $web_subscription_data['end_coverage_period'] = $endCoveragePeriod;

                        if (!empty($primary_state))
                        {
                            $web_subscription_data['issued_state'] = $primary_state;
                        }
                        if($is_group_member == 'Y' && isset($product['member_price'])){
                            $web_subscription_data['member_price'] = $product['member_price'];
                            $web_subscription_data['group_price'] = $product['group_price'];
                            $web_subscription_data['contribution_type'] = checkIsset($product['contribution_type']);
                            $web_subscription_data['contribution_value'] = checkIsset($product['contribution_value']);

                        }

                        /*------ Set Termination Date for Healthy Step ------*/
                        if($product['product_type'] == "Healthy Step") {
                            if($product['is_member_benefits'] == "Y" && $product['is_fee_on_renewal'] == "Y" && $product['fee_renewal_type'] == "Renewals" && $product['fee_renewal_count'] > 0) {
                                $tmp_fee_renewal_count = $product['fee_renewal_count'];
                                $tmp_start_coverage_date = $startCoveragePeriod;
                                $tmp_termination_date = $endCoveragePeriod;
                                while ($tmp_fee_renewal_count > 0) {
                                    $product_dates = $enrollDate->getCoveragePeriod($tmp_start_coverage_date,$member_payment_type);
                                    $tmp_start_coverage_date = date("Y-m-d",strtotime('+1 day',strtotime($product_dates['endCoveragePeriod'])));
                                    $tmp_termination_date = date("Y-m-d",strtotime($product_dates['endCoveragePeriod']));
                                    $tmp_fee_renewal_count--;
                                }
                                $web_subscription_data['termination_date'] = $tmp_termination_date;
                                $web_subscription_data['term_date_set'] = date('Y-m-d');
                                $web_subscription_data['termination_reason'] = 'Policy Change';
                            }
                        }
                        /*------/Set Termination Date for Healthy Step ------*/

                        $web_subscription_data = array_merge($web_subscription_data, array(
                            // 'website_id' => $params['website_ids'][$product['product_id']] ? $params['website_ids'][$product['product_id']] : $function_list->get_website_id() ,
                            'website_id' => $function_list->get_website_id() ,
                            'customer_id' => $customer_id,
                            'created_at' => 'msqlfunc_NOW()',
                            'purchase_date' => 'msqlfunc_NOW()',
                        ));
                        $website_id = $pdo->insert("website_subscriptions", $web_subscription_data);

                        $lead_track = array(
							'status' => 'Created Policy',
							'description' => 'Created Plan : ' . $web_subscription_data['website_id'],
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);

                        if($is_group_member == 'Y' && $web_payment_type=='TPA'){
                            $subscription_param = array();
                            $subscription_param['start_coverage_period'] = $startCoveragePeriod;
                            $subscription_param['end_coverage_period'] = $endCoveragePeriod;
                            $subscription_param['renew_count'] = 1;
                            $subscription_param['created_at'] = 'msqlfunc_NOW()';
                            $subscription_param['updated_at'] = 'msqlfunc_NOW()';
                            $subscription_param['subscription_id'] = $website_id;
                            $member_coverage = $pdo->insert("tpa_member_coverage", $subscription_param);

                            $lead_track = array(
								'status' => 'TPA Member Coverage Added',
								'description' => 'TPA Member Coverage Added',
							);
						
							lead_tracking($lead_id,$customer_id,$lead_track);

                        }

                        $subscription_ids[] = $website_id;

                        $website_subscriptions_history_msg = 'Initial Setup Successful' . ($enroll_withparams_date == "yes" ? " With Post Date " . date("m/d/Y", strtotime($post_date)) : "");

                        if (in_array($application_type, array(
                            'imported'
                        )))
                        {
                            $website_subscriptions_history_msg .= (!$paymentApproved ? "(Declined)" : "");
                        }

                        $web_history_data = array(
                            'customer_id' => $customer_id,
                            'website_id' => $website_id,
                            'product_id' => $product['product_id'],
                            'fee_applied_for_product' => !empty($product['fee_product_id']) ? $product['fee_product_id'] : 0,
                            'plan_id' => $product['matrix_id'],
                            'prd_plan_type_id' => $product['plan_id'],
                            'order_id' => $order_id,
                            'status' => 'Setup',
                            'message' => $website_subscriptions_history_msg,
                            'authorize_id' => makeSafe($txn_id) ,
                            'processed_at' => 'msqlfunc_NOW()',
                            'created_at' => 'msqlfunc_NOW()',
                            'admin_id' => ($from_admin == 'Y' ? $_SESSION['admin']['id'] : 0) ,
                        );
                        $pdo->insert("website_subscriptions_history", $web_history_data);

                        $lead_track = array(
							'status' => 'History added',
							'description' => 'Inserted in website subscription history',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);

                        $sub_products = $function_list->get_sub_product($product['product_id']);
                        $enrollParams = array(
                            'website_id' => $website_id,
                            'company_id' => $product['company_id'],
                            'sub_product' => $sub_products,
                            'sponsor_id' => $sponsor_row['id'],
                            'upline_sponsors' => $sponsor_row['upline_sponsors'] . $sponsor_row['id'] . ",",
                            'level' => $sponsor_row['level'] + 1,
                        );
                        $customer_enrollment_id = $pdo->insert("customer_enrollment", $enrollParams);

                        $lead_track = array(
							'status' => 'Customer Enrollment Table',
							'description' => 'Inserted in customer enrollment table',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);

                        $websiteSubscriptionArr[] = array(
                            'eligibility_date' => $eligibility_date,
                            'website_id' => $website_id,
                            'customer_id' => $customer_id,
                            'product_id' => $product['product_id'],
                            'plan_id' => $product['matrix_id'],
                            'prd_plan_type_id' => $product['plan_id'],
                        );

                    }
                    //********* Website Subcription,Customer enrollment Table code end   *********
                    $insOrderDetailSql = array(
                        'website_id' => $website_id,
                        'order_id' => $order_id,
                        'product_id' => $product['product_id'],
                        'fee_applied_for_product' => !empty($product['fee_product_id']) ? $product['fee_product_id'] : 0,
                        'plan_id' => $product['matrix_id'],
                        'prd_plan_type_id' => $product['plan_id'],
                        'product_type' => $product['type'],
                        'product_name' => $product['product_name'],
                        'product_code' => $product['product_code'],
                        'start_coverage_period' => $startCoveragePeriod,
                        'end_coverage_period' => $endCoveragePeriod,
                        'qty' => $product['qty'],
                        'renew_count' => '1',
                    );

                    if (isset($product_wise_dependents[$product['product_id']]) && !empty($product_wise_dependents[$product['product_id']]))
                    {
                        $insOrderDetailSql['family_member'] = count($product_wise_dependents[$product['product_id']]);
                    }
                    $insOrderDetailSql['unit_price'] = $product['price'];

                    if($is_group_member == 'Y' && isset($product['member_price'])) {
                        $insOrderDetailSql['member_price'] = $product['member_price'];
                        $insOrderDetailSql['group_price'] = $product['group_price'];
                        $insOrderDetailSql['contribution_type'] = $product['contribution_type'];
                        $insOrderDetailSql['contribution_value'] = $product['contribution_value'];
                    }
                    if($sponsor_billing_method == 'individual' && !$only_waive_products){
                        $detail_insert_id = $pdo->insert("order_details", $insOrderDetailSql);
                        $lead_track = array(
                            'status' => 'Inserted',
                            'description' => 'Inserted in order details table',
                        );
                    
                        lead_tracking($lead_id,$customer_id,$lead_track);
                    }else if($is_group_member == 'Y' && !$only_waive_products){
                        $detail_insert_id = $pdo->insert("group_order_details", $insOrderDetailSql);

                        $lead_track = array(
                            'status' => 'Inserted',
                            'description' => 'Inserted in group order details table',
                        );
                    
                        lead_tracking($lead_id,$customer_id,$lead_track);
                    }

                    $insert_customer_benefit_amount = false;
                    $benefitAmountParams = array(
                        'customer_id' => $customer_id,
                        'product_id' =>$product['product_id'],
                        'type'=>'Primary',
                    );
                    if(!empty($primary_benefit_amount_arr[$product['product_id']])){
                        $benefitAmountParams['amount'] = $primary_benefit_amount_arr[$product['product_id']];
                        $insert_customer_benefit_amount = true;
                    }
                    if($is_short_term_disability_product == 'Y' && !empty($primary_monthly_benefit_amount_arr[$product['product_id']])){
                        $benefitAmountParams['amount'] = $primary_monthly_benefit_amount_arr[$product['product_id']];
                        $insert_customer_benefit_amount = true;
                    }
                    if(!empty($primary_in_patient_benefit_arr[$product['product_id']])){
                        $benefitAmountParams['in_patient_benefit'] = $primary_in_patient_benefit_arr[$product['product_id']];
                        $insert_customer_benefit_amount = true;
                    }
                    if(!empty($primary_out_patient_benefit_arr[$product['product_id']])){
                        $benefitAmountParams['out_patient_benefit'] = $primary_out_patient_benefit_arr[$product['product_id']];
                        $insert_customer_benefit_amount = true;
                    }
                    if(!empty($primary_monthly_income_arr[$product['product_id']])){
                        $benefitAmountParams['monthly_income'] = $primary_monthly_income_arr[$product['product_id']];
                        $insert_customer_benefit_amount = true;
                    }
                    if(!empty($primary_benefit_percentage_arr[$product['product_id']])){
                        $benefitAmountParams['benefit_percentage'] = $primary_benefit_percentage_arr[$product['product_id']];
                        $insert_customer_benefit_amount = true;
                    }
                    if(!empty($primary_monthly_benefit_amount_arr[$product['product_id']])){
                        $benefitAmountParams['monthly_benefit'] = $primary_monthly_benefit_amount_arr[$product['product_id']];
                        $insert_customer_benefit_amount = true;
                    }
                    if ($insert_customer_benefit_amount)
                    {
                        $sqlAmount = "SELECT id FROM customer_benefit_amount where is_deleted='N' AND customer_id=:customer_id AND product_id=:product_id AND type='Primary'";
                        $resAmount = $pdo->selectOne($sqlAmount, array(
                            ":customer_id" => $customer_id,
                            ":product_id" => $product['product_id']
                        ));

                        if (!empty($resAmount))
                        {
                            $benefitAmountWhere = array(
                                "clause" => "id=:id",
                                "params" => array(
                                    ":id" => $resAmount['id']
                                )
                            );
                            $pdo->update("customer_benefit_amount", $benefitAmountParams, $benefitAmountWhere);
                            $lead_track = array(
                                'status' => 'Updated',
                                'description' => 'Updated customer benefit amount table',
                            );
                        
                            lead_tracking($lead_id,$customer_id,$lead_track);
                        }
                        else
                        {
                            $benefit_amount_id = $pdo->insert("customer_benefit_amount", $benefitAmountParams);
                            $lead_track = array(
                                'status' => 'Inserted',
                                'description' => 'Inserted in customer benefit amount table',
                            );
                        
                            lead_tracking($lead_id,$customer_id,$lead_track);
                        }
                    }

                }
                //********* Order Detail Table code end   ********************

                //********** Transaction Table code start **************
                    if($sponsor_billing_method == 'individual' && !$only_waive_products){
                        if ($enroll_withparams_date != "yes" && in_array($application_type, array(
                            'imported'
                        )))
                        {
                            $other_params = array(
                                "transaction_id" => $txn_id,
                                'transaction_response' => $payment_res
                            );
                            if ($paymentApproved)
                            {
                                if ($payment_mode != "ACH")
                                {
                                    //************* insert transaction code start ***********************
                                    $transactionInsId = $function_list->transaction_insert($order_id, 'Credit', 'New Order', 'Transaction Approved', 0, $other_params);
                                    $lead_track = array(
                                        'status' => 'Inserted',
                                        'description' => 'Inserted in transaction table',
                                    );
                                
                                    lead_tracking($lead_id,$customer_id,$lead_track);
                                    //**************** insert transaction code end ***********************
                                    
                                }
                                else
                                {
                                    $transactionInsId = $function_list->transaction_insert($order_id, 'Credit', 'Pending', 'Settlement Transaction', 0, $other_params);

                                    $lead_track = array(
                                        'status' => 'Inserted',
                                        'description' => 'Inserted in transaction table',
                                    );
                                
                                    lead_tracking($lead_id,$customer_id,$lead_track);
                                }

                            }
                            else
                            {
                                //************************ insert transaction code start ***********************
                                $other_params["reason"] = checkIsset($payment_error);
                                $other_params["cc_decline_log_id"] = checkIsset($decline_log_id);
                                $transactionInsId = $function_list->transaction_insert($order_id, 'Failed', 'Payment Declined', 'Transaction Declined', 0, $other_params);

                                $lead_track = array(
                                    'status' => 'Inserted',
                                    'description' => 'Inserted in transaction table',
                                );
                            
                                lead_tracking($lead_id,$customer_id,$lead_track);
                                //************************ insert transaction code end ***********************
                                
                            }
                        }
                        else if ($enroll_withparams_date == "yes" && in_array($application_type, array(
                            'imported'
                        )))
                        {
                            $other_params = array();
                            $transactionInsId = $function_list->transaction_insert($order_id, 'Credit', 'Post Payment', 'Post Transaction', 0, $other_params);

                            $lead_track = array(
                                'status' => 'Inserted',
                                'description' => 'Inserted in transaction table',
                            );
                        
                            lead_tracking($lead_id,$customer_id,$lead_track);
                        }
                    }
                //********** Transaction Table code end **************

                //********* Order Table update subscription id code start ********************
                if($sponsor_billing_method == 'individual' && !$only_waive_products){
                    if (!empty($subscription_ids))
                    {
                        $pdo->update("orders", array(
                            'subscription_ids' => implode(',', $subscription_ids)
                        ) , array(
                            "clause" => "id=:id",
                            "params" => array(
                                ":id" => $order_id
                            )
                        ));
                    }
                }
                //********* Order Table update subscription id code end   ********************
                //********* dependent table code start ********************
                if (count($dependent_final_array) > 0)
                {

                    foreach ($dependent_final_array as $dp)
                    {

                        $prd_id = $dp["product_id"];
                        $plan_id = $dp["plan_id"];
                        $prd_mat_id = $dp["matrix_id"];

                        $sqlWeb = "SELECT id FROM website_subscriptions where customer_id=:customer_id AND product_id=:product_id  AND prd_plan_type_id=:prd_plan_type_id";
                        $resWeb = $pdo->selectOne($sqlWeb, array(
                            ":customer_id" => $customer_id,
                            ":product_id" => $prd_id,
                            ":prd_plan_type_id" => $plan_id
                        ));
                        $dep_website_id = 0;
                        if (!empty($resWeb))
                        {
                            $dep_website_id = $resWeb['id'];
                        }

                        $product_wise_dependents[$dp["product_id"]] = $dp["dependent"];
                        foreach ($dp["dependent"] as $d)
                        {
                            $cust_dep_sql = "SELECT id FROM customer_dependent WHERE product_plan_id=:product_plan_id AND cd_profile_id=:cd_profile_id AND customer_id=:customer_id";
                            $cust_dep_where = array(
                                ":product_plan_id" => $prd_mat_id,
                                ":cd_profile_id" => $d['cd_profile_id'],
                                ":customer_id" => $customer_id
                            );
                            $cust_dep_res = $pdo->selectOne($cust_dep_sql, $cust_dep_where);

                            $relation = $d['dependent_relation_input'];
                            $dependent_params = array(
                                'website_id' => $dep_website_id,
                                'customer_id' => $customer_id,
                                'order_id' => (isset($order_id) && $order_id != 0) ? $order_id : 0,
                                'product_id' => (isset($prd_id)) ? $prd_id : 0,
                                'product_plan_id' => (isset($prd_mat_id)) ? $prd_mat_id : 0,
                                'prd_plan_type_id' => (isset($plan_id)) ? $plan_id : 0,
                                'relation' => $d["dependent_relation_input"],
                                'fname' => $d[$relation . "_fname"],
                                'lname' => $d[$relation . "_lname"],
                                'birth_date' => date('Y-m-d', strtotime($d[$relation . "_birthdate"])) ,
                                'gender' => $d[$relation . "_gender"],
                                'status' => ($paymentApproved ? 'Active' : 'Pending Payment') ,
                                'is_deleted' => 'N',
                                'updated_at' => 'msqlfunc_NOW()',
                            );

                            if ($d['relation'] == "Spouse")
                            {
                                $dependent_params['benefit_amount'] = isset($spouse_benefit_amount_arr[$d['dependent_id']]) ? $spouse_benefit_amount_arr[$d['dependent_id']] : 0;
                                $dependent_params['in_patient_benefit'] = isset($spouse_in_patient_benefit_arr[$d['dependent_id']]) ? $spouse_in_patient_benefit_arr[$d['dependent_id']] : 0;
                                $dependent_params['out_patient_benefit'] = isset($spouse_out_patient_benefit_arr[$d['dependent_id']]) ? $spouse_out_patient_benefit_arr[$d['dependent_id']] : 0;
                                $dependent_params['monthly_income'] = isset($spouse_monthly_income_arr[$d['dependent_id']]) ? $spouse_monthly_income_arr[$d['dependent_id']] : 0;
                                $dependent_params['benefit_percentage'] = isset($spouse_benefit_percentage_arr[$d['dependent_id']]) ? $spouse_benefit_percentage_arr[$d['dependent_id']] : 0;
                            }
                            else if ($d['relation'] == "Child")
                            {
                                $dependent_params['benefit_amount'] = isset($child_benefit_amount_arr[$d['dependent_id']]) ? $child_benefit_amount_arr[$d['dependent_id']] : 0;
                                $dependent_params['in_patient_benefit'] = isset($child_in_patient_benefit_arr[$d['dependent_id']]) ? $child_in_patient_benefit_arr[$d['dependent_id']] : 0;
                                $dependent_params['out_patient_benefit'] = isset($child_out_patient_benefit_arr[$d['dependent_id']]) ? $child_out_patient_benefit_arr[$d['dependent_id']] : 0;
                                $dependent_params['monthly_income'] = isset($child_monthly_income_arr[$d['dependent_id']]) ? $child_monthly_income_arr[$d['dependent_id']] : 0;
                                $dependent_params['benefit_percentage'] = isset($child_benefit_percentage_arr[$d['dependent_id']]) ? $child_benefit_percentage_arr[$d['dependent_id']] : 0;
                            }
                            if (isset($d[$relation . "_mname"]) && $d[$relation . "_mname"] != "")
                            {
                                $dependent_params['mname'] = $d[$relation . "_mname"];
                            }
                            else
                            {
                                $dependent_params['mname'] = "";
                            }
                            if (isset($d[$relation . "_email"]) && $d[$relation . "_email"] != "")
                            {
                                $dependent_params['email'] = $d[$relation . "_email"];
                            }
                            else
                            {
                                $dependent_params['email'] = "";
                            }
                            if (isset($d[$relation . "_phone"]) && $d[$relation . "_phone"] != "")
                            {
                                $dependent_params['phone'] = phoneReplaceMain($d[$relation . "_phone"]);
                            }
                            else
                            {
                                $dependent_params['phone'] = "";
                            }
                            if (isset($d[$relation . "_address1"]) && $d[$relation . "_address1"] != "")
                            {
                                $dependent_params['address'] = $d[$relation . "_address1"];
                            }
                            else
                            {
                                $dependent_params['address'] = $primary_address1;
                            }
                            if (isset($d[$relation . "_city"]) && $d[$relation . "_city"] != "")
                            {
                                $dependent_params['city'] = $d[$relation . "_city"];
                            }
                            else
                            {
                                $dependent_params['city'] = $primary_city;
                            }
                            if (isset($d[$relation . "_state"]) && $d[$relation . "_state"] != "")
                            {
                                $dependent_params['state'] = $d[$relation . "_state"];
                            }
                            else
                            {
                                $dependent_params['state'] = $primary_state;
                            }
                            if (isset($d[$relation . "_zip"]) && $d[$relation . "_zip"] != "")
                            {
                                $dependent_params['zip_code'] = $d[$relation . "_zip"];
                            }
                            else
                            {
                                $dependent_params['zip_code'] = $primary_zip;
                            }
                            if (isset($d[$relation . "_salary"]) && $d[$relation . "_salary"] != "")
                            {
                                $dependent_params['salary'] = $d[$relation . "_salary"];
                            }
                            else
                            {
                                $dependent_params['salary'] = "";
                            }
                            if (isset($d[$relation . "_employment_status"]) && $d[$relation . "_employment_status"] != "")
                            {
                                $dependent_params['employmentStatus'] = $d[$relation . "_employment_status"];
                            }
                            else
                            {
                                $dependent_params['employmentStatus'] = "";
                            }
                            if (isset($d[$relation . "_tobacco_status"]) && $d[$relation . "_tobacco_status"] != "")
                            {
                                $dependent_params['tobacco_use'] = $d[$relation . "_tobacco_status"];
                            }
                            else
                            {
                                $dependent_params['tobacco_use'] = "";
                            }
                            if (isset($d[$relation . "_smoking_status"]) && $d[$relation . "_smoking_status"] != "")
                            {
                                $dependent_params['smoke_use'] = $d[$relation . "_smoking_status"];
                            }
                            else
                            {
                                $dependent_params['smoke_use'] = "";
                            }
                            if (isset($d[$relation . "_height"]) && $d[$relation . "_height"] != "")
                            {
                                $dependent_height_array = explode(".", $d[$relation . "_height"]);
                                $dependent_params['height_feet'] = $dependent_height_array[0];
                                $dependent_params['height_inches'] = $dependent_height_array[1];
                            }
                            else
                            {
                                $dependent_params['height_feet'] = '';
                                $dependent_params['height_inches'] = '';
                            }
                            if (isset($d[$relation . "_weight"]) && $d[$relation . "_weight"] != "")
                            {
                                $dependent_params['weight'] = $d[$relation . "_weight"];
                            }
                            else
                            {
                                $dependent_params['weight'] = "";
                            }
                            if (isset($d[$relation . "_SSN"]) && $d[$relation . "_SSN"] != '')
                            {
                                $dependent_params['ssn'] = str_replace("-", "", $d[$relation . "_SSN"]);
                                $dependent_params['last_four_ssn'] = substr(str_replace("-", "", $d[$relation . "_SSN"]) , -4);
                            }
                            else
                            {
                                $dependent_params['ssn'] = "";
                                $dependent_params['last_four_ssn'] = "";
                            }

                            if (isset($d[$relation . "_benefit_level"]) && $d[$relation . "_benefit_level"] != "")
                            {
                                $dependent_params['benefit_level'] = $d[$relation . "_benefit_level"];
                            }
                            else
                            {
                                $dependent_params['benefit_level'] = "";
                            }
                            if (isset($d[$relation . "_hours_per_week"]) && $d[$relation . "_hours_per_week"] != "")
                            {
                                $dependent_params['hours_per_week'] = $d[$relation . "_hours_per_week"];
                            }
                            else
                            {
                                $dependent_params['hours_per_week'] = NULL;
                            }

                            if (isset($d[$relation . "_pay_frequency"]) && $d[$relation . "_pay_frequency"] != "")
                            {
                                $dependent_params['pay_frequency'] = $d[$relation . "_pay_frequency"];
                            }
                            else
                            {
                                $dependent_params['pay_frequency'] = "";
                            }

                            if (isset($d[$relation . "_us_citizen"]) && $d[$relation . "_us_citizen"] != "")
                            {
                                $dependent_params['us_citizen'] = $d[$relation . "_us_citizen"];
                            }
                            else
                            {
                                $dependent_params['us_citizen'] = "";
                            }

                            if (isset($d[$relation . "_date_of_hire"]) && $d[$relation . "_date_of_hire"] != "")
                            {
                                $dependent_params['hire_date'] = date('Y-m-d', strtotime($d[$relation . "_date_of_hire"]));
                            }
                            else
                            {
                                $dependent_params['hire_date'] = NULL;
                            }
                            if (in_array($application_type, array(
                                'imported'
                            )))
                            {
                                if (!$paymentApproved)
                                {
                                    $dependent_params['status'] = 'Payment Declined';
                                }
                            }
                            if ($enroll_withparams_date == "yes")
                            {
                                $dependent_params['status'] = "Post Payment";
                            }

                            if (count($cust_dep_res) > 0)
                            {
                                $cdp_param = array(
                                    'customer_id' => $customer_id,
                                    'relation' => $dependent_params['relation'],
                                    'fname' => $dependent_params['fname'],
                                    'lname' => $dependent_params['lname'],
                                    'birth_date' => $dependent_params['birth_date'],
                                    'gender' => $dependent_params['gender'],
                                    'email' => $dependent_params['email'],
                                    'phone' => $dependent_params['phone'],
                                    'mname' => $dependent_params['mname'],
                                    'address' => $dependent_params['address'],
                                    'city' => $dependent_params['city'],
                                    'state' => $dependent_params['state'],
                                    'zip_code' => $dependent_params['zip_code'],
                                    'salary' => $dependent_params['salary'],
                                    'employmentStatus' => $dependent_params['employmentStatus'],
                                    'tobacco_use' => $dependent_params['tobacco_use'],
                                    'smoke_use' => $dependent_params['smoke_use'],
                                    'height_feet' => $dependent_params['height_feet'],
                                    'height_inches' => $dependent_params['height_inches'],
                                    'weight' => $dependent_params['weight'],
                                    'ssn' => $dependent_params['ssn'],
                                    'last_four_ssn' => $dependent_params['last_four_ssn'],
                                    'benefit_level' => $dependent_params['benefit_level'],
                                    'hours_per_week' => $dependent_params['hours_per_week'],
                                    'pay_frequency' => $dependent_params['pay_frequency'],
                                    'us_citizen' => $dependent_params['us_citizen'],
                                    'hire_date' => $dependent_params['hire_date'],
                                );
                                $dependent_profile_where = array(
                                    "clause" => "id = :id",
                                    "params" => array(
                                        ":id" => $d['cd_profile_id']
                                    ) ,
                                );
                                $dependent_where = array(
                                    "clause" => "id=:id",
                                    "params" => array(
                                        ":id" => $cust_dep_res['id'],
                                    ) ,
                                );
                                $pdo->update("customer_dependent_profile", $cdp_param, $dependent_profile_where);

                                $lead_track = array(
                                    'status' => 'Updated',
                                    'description' => 'Updated in customer dependent profile table',
                                );
                            
                                lead_tracking($lead_id,$customer_id,$lead_track);

                                $pdo->update("customer_dependent", $dependent_params, $dependent_where);

                                $lead_track = array(
                                    'status' => 'Updated',
                                    'description' => 'Updated in customer dependent table',
                                );
                            
                                lead_tracking($lead_id,$customer_id,$lead_track);

                                $dep_id = $cust_dep_res['id'];

                                //Store Dependent Profile Benefit Amount
                                if(!empty($dependent_params['benefit_amount']) || !empty($dependent_params['in_patient_benefit']) || !empty($dependent_params['out_patient_benefit']) || !empty($dependent_params['monthly_income']) || !empty($dependent_params['benefit_percentage'])) {
                                    $dep_benefit_param = array(
                                        "benefit_amount" => $dependent_params['benefit_amount'],
                                        "in_patient_benefit" => $dependent_params['in_patient_benefit'],
                                        "out_patient_benefit" => $dependent_params['out_patient_benefit'],
                                        "monthly_income" => $dependent_params['monthly_income'],
                                        "benefit_percentage" => $dependent_params['benefit_percentage'],
                                    );
                                    save_customer_dependent_profile_benefit_amount($d['cd_profile_id'], $dependent_params['product_id'], $dep_benefit_param);
                                }
                            }
                            else
                            {
                                $dep_id = $function_list->insert_dependent($dependent_params, $prd_mat_id);

                                $lead_track = array(
                                    'status' => 'Inserted',
                                    'description' => 'Inserted in customer dependent table',
                                );
                            
                                lead_tracking($lead_id,$customer_id,$lead_track);
                            }

                            $queCustom = !empty($params[$relation . '_queCustom'][$d['dependent_id']]) ? $params[$relation . '_queCustom'][$d['dependent_id']] : array();

                            if (!empty($queCustom))
                            {
                                foreach ($queCustom as $key => $value)
                                {
                                    $sqlQue = "SELECT id FROM customer_custom_questions WHERE is_deleted='N' AND question_id=:question_id AND customer_id =:customer_id AND enrollee_type=:enrollee_type AND dependent_id=:dependent_id";
                                    $resQue = $pdo->selectOne($sqlQue, array(
                                        ":customer_id" => $customer_id,
                                        ":question_id" => $key,
                                        ":enrollee_type" => $relation,
                                        ":dependent_id" => $dep_id
                                    ));

                                    if (is_array($value))
                                    {
                                        $answer = implode(",", $value);
                                    }
                                    else
                                    {
                                        $answer = $value;
                                    }
                                    if(empty($answer)){
                                        continue;
                                    }            
                                    $queInsParams = array(
                                        "enrollee_type" => $relation,
                                        "customer_id" => $customer_id,
                                        "question_id" => $key,
                                        "dependent_id" => $dep_id,
                                        "answer" => $answer,
                                    );
                                    if (!empty($resQue))
                                    {
                                        $queInswhere = array(
                                            "clause" => "id=:id",
                                            "params" => array(
                                                ":id" => $resQue['id'],
                                            ) ,
                                        );
                                        $pdo->update("customer_custom_questions", $queInsParams, $queInswhere);

                                        $lead_track = array(
                                            'status' => 'Updated',
                                            'description' => 'Updated in customer custom question table',
                                        );
                                    
                                        lead_tracking($lead_id,$customer_id,$lead_track);
                                    }
                                    else
                                    {
                                        $pdo->insert("customer_custom_questions", $queInsParams);

                                        $lead_track = array(
                                            'status' => 'Inserted',
                                            'description' => 'Inserted in customer custom question table',
                                        );
                                    
                                        lead_tracking($lead_id,$customer_id,$lead_track);
                                    }
                                }
                            }
                        }
                    }

                    //********* Update cust_enrollment_id To customer_dependent code start ********************
                    if (!empty($websiteSubscriptionArr)) {
                        $incr = "";
                        if($is_add_product == 1){
                            $incr = " AND terminationDate IS NULL";
                        }
                        foreach ($websiteSubscriptionArr as $ws_row) {
                            $dependent_where = array(
                                "clause" => "customer_id=:customer_id AND product_id=:product_id AND product_plan_id=:plan_id AND prd_plan_type_id=:prd_plan_type_id $incr",
                                "params" => array(
                                    ":customer_id" => $ws_row['customer_id'],
                                    ":product_id" => $ws_row['product_id'],
                                    ":plan_id" => $ws_row['plan_id'],
                                    ":prd_plan_type_id" => $ws_row['prd_plan_type_id'],
                                ),
                            );
                            $pdo->update("customer_dependent", array('website_id' => $ws_row['website_id'],'eligibility_date'=>$ws_row['eligibility_date']), $dependent_where);
                        }

                        $lead_track = array(
                            'status' => 'Updated',
                            'description' => 'Updated customer dependent table',
                        );
                    
                        lead_tracking($lead_id,$customer_id,$lead_track);
                    }
                    //********* Update cust_enrollment_id To customer_dependent code end   ********************
                
                }
                //********* dependent table code end   ********************
                

                //********* insert terms and agreement of member code start ********************
                if (in_array($application_type, array(
                    'imported'
                )))
                {
                    if($sponsor_billing_method == 'individual' && !$only_waive_products){
                        $function_list->insert_member_terms($customer_id, $order_id);

                        $lead_track = array(
                            'status' => 'Inserted',
                            'description' => 'Inserted in member term agreement table',
                        );
                    
                        lead_tracking($lead_id,$customer_id,$lead_track);
                    }
                }
                $function_list->insert_dpg_agreements($customer_id, $order_id);

                $lead_track = array(
                    'status' => 'Inserted',
                    'description' => 'Inserted dpg agreement',
                );
            
                lead_tracking($lead_id,$customer_id,$lead_track);
                $function_list->insert_joinder_agreements($customer_id,$order_id,$application_type);

                $lead_track = array(
                    'status' => 'Inserted',
                    'description' => 'Inserted joinder agreements',
                );
            
                lead_tracking($lead_id,$customer_id,$lead_track);
                //********* insert terms and agreement of member code end   ********************
                //********* Update Lead and Customer Status code start ********************
                $member_setting = $memberSetting->get_status_by_payment($paymentApproved,"",($enroll_withparams_date == "yes" ? true : false));
                if ($paymentApproved && in_array($application_type, array(
                    'imported'
                )))
                {
                    if ($enroll_withparams_date != "yes")
                    {
                        $lead_where = array(
                            "clause" => "id=:id",
                            "params" => array(
                                ":id" => $lead_id,
                            ) ,
                        );
                        $pdo->update("leads", array(
                            'status' => 'Converted',
                            'updated_at' => 'msqlfunc_NOW()'
                        ) , $lead_where);

                        $update_lead_param = array(
                            'customer_id' => $customer_id,
                            'email' => $primary_email,
                            'cell_phone' => $primary_phone,
                        );
                        $function_list->update_leads_and_details($update_lead_param);

                        $lead_track = array(
                            'status' => 'Updated',
                            'description' => 'Updated lead quote details',
                        );
                    
                        lead_tracking($lead_id,$customer_id,$lead_track);
                    }

                    if (isset($quote_id) && !empty($quote_id))
                    {
                        $c_quote_param = array(
                            'status' => 'Completed',
                            'updated_at' => 'msqlfunc_NOW()'
                        );
                        $quote_where = array(
                            "clause" => "id=:id",
                            "params" => array(
                                ":id" => $quote_id
                            )
                        );
                        $pdo->update("lead_quote_details", $c_quote_param, $quote_where);

                        $lead_track = array(
                            'status' => 'Updated',
                            'description' => 'Updated lead quote details',
                        );
                    
                        lead_tracking($lead_id,$customer_id,$lead_track);
                    }
                    else
                    {
                        $str_plan_ids = implode(",", array_unique($PlanIdArr));
                        $lead_quote_details_response = $pdo->selectOne("SELECT * FROM lead_quote_details WHERE agent_id = :agent_id AND customer_ids = :customer_ids AND lead_id = :lead_id AND plan_ids LIKE :plan_id", array(
                            ":agent_id" => $sponsor_id,
                            ":customer_ids" => $customer_id,
                            ":lead_id" => $lead_id,
                            ":plan_id" => $str_plan_ids
                        ));
                        $currentQuoteId = isset($lead_quote_details_response['id']) ? $lead_quote_details_response['id'] : 0;

                        if ($lead_quote_details_response)
                        {
                            $c_quote_param = array(
                                'status' => 'Completed',
                                'updated_at' => 'msqlfunc_NOW()'
                            );
                            $quote_where = array(
                                "clause" => "id=:id",
                                "params" => array(
                                    ":id" => $lead_quote_details_response['id']
                                )
                            );
                            $pdo->update("lead_quote_details", $c_quote_param, $quote_where);

                            $lead_track = array(
                                'status' => 'Updated',
                                'description' => 'Updated lead quote details',
                            );
                        
                            lead_tracking($lead_id,$customer_id,$lead_track);
                        }
                    }

                    $customer_where = array(
                        'clause' => 'id = :id',
                        'params' => array(
                            ':id' => $customer_id,
                        ) ,
                    );

                    if (!empty($customer_id))
                    {

                        if (in_array($application_type, array(
                            'imported'
                        )))
                        {
                            
                        }

                        $updateCustomer = array(
                            // 'status' => 'Active',
                            'status' => $member_setting['member_status'],
                            'updated_at' => 'msqlfunc_NOW()',
                            "invite_at" => "msqlfunc_NOW()"
                        );
                        // if ($enroll_withparams_date == "yes")
                        // {
                        //     $updateCustomer['status'] = 'Post Payment';
                        // }
                        $pdo->update('customer', $updateCustomer, $customer_where);

                        $lead_track = array(
                            'status' => 'Updated',
                            'description' => 'Updated customer table',
                        );
                    
                        lead_tracking($lead_id,$customer_id,$lead_track);
                    }
                    /*--------------------- Start Final Script ---------------------------------*/
                    /*--------- Send Welcome Mail ---------*/
                    $agent_detail = $function_list->get_sponsor_detail_for_mail($customer_id, $sponsor_row['id']);

                    $trigger_id = 39;
                    if($is_group_member == 'Y'){
                        $trigger_id = 109;
                    }
                    $mail_data = array();
                    $mail_data['fname'] = $primary_fname;
                    $mail_data['lname'] = $primary_lname;
                    $mail_data['Email'] = $primary_email;
                    $mail_data['Phone'] = $primary_phone;
                    $mail_data['link'] = $CUSTOMER_HOST;
                    $mail_data['Agent'] = checkIsset($agent_detail['agent_name']);
                    $mail_data['order_id'] = "#" . $order_display_id;
                    $mail_data['order_date'] = date("m/d/Y");
                    $mail_data['MemberID'] = $customer_rep_id;

                    if (!empty($sponsor_row['sponsor_id']))
                    {
                        $parent_agent_detail = $function_list->get_sponsor_detail_for_mail($customer_id, $sponsor_row['sponsor_id']);
                        $mail_data['ParentAgent'] = $parent_agent_detail['agent_name'];
                    }

                    //********* Confirm summary code start ********************
                    $summary = "";
                    $summary .= '<table width="100%" cellspacing="0" cellpadding="5" border="0" align="center" style="margin-bottom:15px; text-align:left; font-size:14px; margin-top:10px" >
			            <thead>
			                <tr style="background-color:#f1f1f1; text-align:left;">
			                    <th width="5%">No.</th>
			                    <th width="50%">Description</th>
			                    <th width="10%">Qty</th>
			                    <th width="">Unit Price</th>
			                    <th width="12%" style="text-align:right">Total</th>
			                </tr>
			            </thead>
			            <tbody>';
                    $i = 1;

                    foreach ($purchase_products_array as $key => $product)
                    {
                        if (in_array($product['product_type'], array(
                            'Healthy Step',
                            'ServiceFee'
                        )))
                        {
                            continue;
                        }
                        $summary_price = 0;
                        $summary_price = $product['price'];

                        $plan_name = isset($product['plan_name']) ? $product['plan_name'] : "";
                        $product_name = $product['product_name'];
                        if ($product['type'] == 'Fees')
                        {
                            $plan_name = $product['product_type'] . ' Fee';

                        }
                        $count = $i;

                        $summary .= '<tr>
				                <td>' . $count . '</td>
				                <td>' . $product_name . ' (' . $plan_name . ')' . '</td>
				                <td>' . $product['qty'] . '</td>
				                <td>' . displayAmount($summary_price, 2, 'USA') . '</td>
				                <td style="text-align:right">' . displayAmount($summary_price * $product['qty'], 2, 'USA') . '</td>
			            	</tr>';

                        $i++;

                    }

                    $summary .= '</tbody> </table>
			            <table cellspacing="0" cellpadding="5" border="0" style="float:right; width:290px; font-size:14px;">
			            <tr>
			                <td>Sub Total : </td>
			                <td style="text-align:right">' . displayAmount($order_total['sub_total'], 2, "USA") . '</td>
			            </tr>';
                    if ($order_total['service_fee'] > 0)
                    {
                        $summary .= '<tr>
			                    <td>Service Fee</td>
			                    <td align="right">' . displayAmount($order_total['service_fee'], 2, 'USA') . ' </td>
			                </tr>';
                    }
                    if ($order_total['healthy_step_fee'] > 0)
                    {
                        $summary .= '<tr>
			                    <td>Healthy Step </td>
			                    <td align="right">' . displayAmount($order_total['healthy_step_fee'], 2, 'USA') . ' </td>
			                </tr>';
                    }
                    $summary .= '<tr style="background-color:#f1f1f1; font-size: 16px;">
			                <td><strong>Grand Total</strong></td>
			                <td style="text-align:right"><strong>' . displayAmount($order_total['grand_total'], 2, "USA") . '</strong></td>
			            </tr>
			            </table>
			            <div style="clear:both"></div>';
                    //********* Confirm summary code end ********************
                    $mail_data['order_summary'] = $summary;

                    if($sponsor_billing_method == 'individual'){
                    if ($payment_mode == "CC")
                    {
                        $cd_number = !empty($card_number) ? $card_number : $full_card_number;
                        $mail_data['billing_detail'] = "Billed to: $card_type *" . substr($cd_number, -4);
                    }
                    else
                    {
                        $r_number = !empty($routing_number) ? $routing_number : $entered_routing_number;
                        $mail_data['billing_detail'] = "Billed to: ACH *" . substr($r_number, -4);
                    }
                    }

                    if ($SITE_ENV == 'Local')
                    {
                        $primary_email = "kamlesh@cyberxllc.com";
                    }

                    if (!empty($customer_id))
                    {
                        if (!empty($agent_detail))
                        {
                            $mail_data['agent_name'] = checkIsset($agent_detail['agent_name']);
                            $mail_data['agent_email'] = checkIsset($agent_detail['agent_email']);
                            $mail_data['agent_phone'] = !empty($agent_detail['agent_phone']) ? format_telephone($agent_detail['agent_phone']) : '';
                            $mail_data['agent_id'] = checkIsset($agent_detail['agent_id']);
                            $mail_data['is_public_info'] = $agent_detail['is_public_info'];
                        }
                        else
                        {
                            $mail_data['is_public_info'] = 'display:none';
                        }

                        $smart_tags = get_user_smart_tags($customer_id,'member');
                
                        if($smart_tags){
                            $mail_data = array_merge($mail_data,$smart_tags);
                        }
                        if ($enroll_withparams_date != "yes") {
                            trigger_mail(107, $mail_data, $primary_email, array() , 3);   //Member                         
                            trigger_mail(106, $mail_data, $primary_email, array() , 3);  // Admin    
                            
                            $lead_track = array(
                                'status' => 'Sent',
                                'description' => 'Member - Member Import Complete',
                            );
                        
                            lead_tracking($lead_id,$customer_id,$lead_track);

                        }

                        // trigger_mail($trigger_id, $mail_data, $primary_email, array() , 3);
                        /*---------/Send Welcome Mail ---------*/

                        //********* Activity Feed code start ********************
                        $message_3 = $payment_mode == 'CC' ? ' Approved on Order ' : 'PENDING SETTLEMENT on Order';
                        $leadRes = $pdo->selectOne("SELECT lead_id FROM leads WHERE id = :lead_id", array(
                            ":lead_id" => $lead_id
                        ));
                        $activity_feed_data_member['ac_message'] = array(
                            'ac_red_1' => array(
                                'href' => $AGENT_HOST . '/lead_details.php?id=' . md5($lead_id) ,
                                'title' => $leadRes['lead_id'],
                            ) ,
                            'ac_message_2' => '  transaction  ',
                            'ac_red_2' => array(
                                // 'href'=>$ADMIN_HOST.'/all_orders.php?id='.md5($order_id),
                                'title' => $txn_id,
                            ) ,
                            'ac_message_3' => $message_3,
                            'ac_red_3' => array(
                                'href' => $ADMIN_HOST . '/all_orders.php?id=' . md5($order_id) ,
                                'title' => $order_display_id,
                            ) ,
                            'ac_message_4' => ' and became Member ',
                            'ac_red_4' => array(
                                'href' => $ADMIN_HOST . '/members_details.php?id=' . md5($customer_id) ,
                                'title' => $customer_rep_id,
                            ) ,
                            'ac_message_5' => '  Member : ' . $primary_fname . ' ' . $primary_lname . ' <br>',
                        );
                        $activity_feed_data_member['key_value'] = array(
                            'desc_arr' => array(
                                'url' => $enrollment_url,
                                'email' => $primary_email,
                                'phone' => $primary_phone,
                            )
                        );

                        if (in_array($application_type, array(
                            'voice_verification'
                        )))
                        {
                            $activity_feed_data = array(
                                'file_name' => json_encode($voice_uploaded_fileName) ,
                                'voice_application_type' => $voice_application_type,
                                'system_code' => $voice_verification_system_code,
                                'is_voice_msg' => 'Y',
                            );
                        }
                        else
                        {
                            $activity_feed_data = array();
                        }

                        activity_feed(3, $sponsor_row['id'], $sponsor_row['type'], $sponsor_row['id'], $sponsor_row['type'], 'Imported A New Member', $primary_fname, $primary_lname, json_encode($activity_feed_data_member));

                        $lead_track = array(
							'status' => 'Activity Feed',
							'description' => 'Imported A New Member',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);

                        activity_feed(3, $customer_id, "Customer", $order_id, 'orders', 'Joined', $primary_fname, $primary_lname, json_encode($activity_feed_data_member));

                        $lead_track = array(
							'status' => 'Activity Feed',
							'description' => 'Activity added - Joined',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);

                        $trigger_id = 38;
						if($is_group_member == 'Y'){
							$trigger_id = 110;
						}
                        $mail_content = $pdo->selectOne("SELECT id,email_content,display_id from triggers where id=:id", array(
                            ":id" => $trigger_id
                        ));
                        $email_activity = array();
                        if (!empty($mail_data) && !empty($mail_content['id']))
                        {
                            $email_cn = $mail_content['email_content'];
                            foreach ($mail_data as $placeholder => $value)
                            {
                                $email_cn = str_replace("[[" . $placeholder . "]]", $value, $email_cn);
                            }

                            $email_activity["ac_description_link"] = array(
                                'Trigger :' => array(
                                    'href' => '#javascript:void(0)',
                                    'class' => 'descriptionPopup',
                                    'title' => $mail_content['display_id'],
                                    'data-desc' => htmlspecialchars($email_cn) ,
                                    'data-encode' => 'no'
                                ) ,
                                '<br>Email :' => array(
                                    'href' => '#javascript:void(0)',
                                    'title' => $primary_email,
                                )
                            );
                        }
                        if ($enroll_withparams_date != "yes") {
                            activity_feed(3, $customer_id, "Customer", $trigger_id, 'triggers', 'Welcome email delivered', $primary_fname, $primary_lname, json_encode($email_activity));

                            $MemberEnrollment->send_temporary_password_mail($customer_id);

                            $lead_track = array(
                                'status' => 'Sent',
                                'description' => 'Email Sent - Temporary Password',
                            );
                        
                            lead_tracking($lead_id,$customer_id,$lead_track);
                        }
                        //********* Activity Feed code end ********************
                        if (in_array($application_type, array(
                            'voice_verification',
                            'admin'
                        )))
                        {
                            $mail_data['fname'] = $primary_fname;
                            $mail_data['Email'] = $primary_email;

                        }
                        else
                        {
                            $mail_data['fname'] = $primary_fname;
                            $mail_data['Email'] = $primary_email;
                        }

                        if (!empty($agent_detail))
                        {
                            $mail_data['agent_name'] = checkIsset($agent_detail['agent_name']);
                            $mail_data['agent_email'] = checkIsset($agent_detail['agent_email']);
                            $mail_data['agent_phone'] = !empty($agent_detail['agent_phone']) ? format_telephone($agent_detail['agent_phone']) : '';
                            $mail_data['agent_id'] = checkIsset($agent_detail['agent_id']);
                            $mail_data['is_public_info'] = $agent_detail['is_public_info'];
                        }
                        else
                        {
                            $mail_data['is_public_info'] = 'display:none';
                        }
                        // trigger_mail(38, $mail_data, $primary_email, array() , 3);

                        $activity_feed_data = array();
                        if (in_array($application_type, array(
                            'voice_verification'
                        )))
                        {
                            $activity_feed_data['ac_message'] = array(
                                'ac_red_1' => array(
                                    'href' => $ADMIN_HOST . '/members_details.php?id=' . md5($customer_id) ,
                                    'title' => $customer_rep_id,
                                ) ,
                                'ac_message_1' => ' used Voice Recording Uploading to complete enrollment'
                            );
                        }
                        else
                        {
                            $activity_feed_data['ac_message'] = array(
                                'ac_red_1' => array(
                                    'href' => $ADMIN_HOST . '/members_details.php?id=' . md5($customer_id) ,
                                    'title' => $customer_rep_id,
                                ) ,
                                'ac_message_1' => ' enrollment complete by import'
                            );
                        }
                        activity_feed(3, $customer_id, 'Customer', $customer_id, 'customer', 'Application is Approved', $primary_fname, $primary_lname, json_encode($activity_feed_data));

                        $lead_track = array(
							'status' => 'Activity Feed',
							'description' => 'Activity added - Application is Approved',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);
                    }

                    
                    $response['status'] = 'account_approved';
                    if ($from_admin)
                    {
                        // setNotifySuccess("Congratulations.. Product added Successfully");
                    }
                    else
                    {
                        // setNotifySuccess("Congratulations.. Member Enrolled Successfully");
                    }
                }
                else if (!$paymentApproved && !in_array($application_type, array(
                    'imported'
                )))
                {

                    $response['status'] = "payment_fail";
                    $error_reporting_arr[] = "Payment Failed : " . $payment_error;
                    $errorArr['reason'] = implode(' or<br>',$error_reporting_arr);
                    $pdo->insert("import_csv_log", $errorArr);

                }
                

                //********* Update Lead and Customer Status code end   ********************
                //********* Payable Insert Code Start ********************
                if($sponsor_billing_method == 'individual' && !$only_waive_products){
                    if ($paymentApproved == true && $enroll_withparams_date != "yes")
                    {
                        if ($payment_mode != "ACH")
                        {

                            $payable_params = array(
                                'payable_type' => 'Vendor',
                                'type' => 'Vendor',
                                'transaction_tbl_id' => $transactionInsId['id'],
                            );
                            $payable = $function_list->payable_insert($order_id, 0, 0, 0, $payable_params);

                            $lead_track = array(
                                'status' => 'Inserted',
                                'description' => 'Inserted in Payable table',
                            );
                        
                            lead_tracking($lead_id,$customer_id,$lead_track);
                        }
                    }
                }
                //********* Payable Insert Code End   ********************
                //********* Beneficiery Insert Code Start ********************
                $tmpPrincipal = !empty($params['principal_queBeneficiaryFullName']) ? $params['principal_queBeneficiaryFullName'] : array();
                if (!empty($tmpPrincipal))
                {
                    $saved_principal_ids = array();
                    foreach ($tmpPrincipal as $key => $value)
                    {
                        $principal_beneficiary_id = !empty($params['principal_beneficiary_id'][$key]) ? $params['principal_beneficiary_id'][$key] : 0;
                        if (!empty($principal_beneficiary_id))
                        {
                            $sqlBeneficiery = "SELECT id FROM customer_beneficiary where id=:id";
                            $resBeneficiery = $pdo->selectOne($sqlBeneficiery, array(
                                ":id" => $principal_beneficiary_id
                            ));
                        }
                        else
                        {
                            $resBeneficiery = array();
                        }

                        $name = !empty($params['principal_queBeneficiaryFullName'][$key]) ? $params['principal_queBeneficiaryFullName'][$key] : '';
                        $address = !empty($params['principal_queBeneficiaryAddress'][$key]) ? $params['principal_queBeneficiaryAddress'][$key] : '';
                        $cell_phone = !empty(phoneReplaceMain($params['principal_queBeneficiaryPhone'][$key])) ? phoneReplaceMain($params['principal_queBeneficiaryPhone'][$key]) : '';
                        $email = !empty($params['principal_queBeneficiaryEmail'][$key]) ? $params['principal_queBeneficiaryEmail'][$key] : '';
                        $ssn = !empty($params['principal_queBeneficiarySSN'][$key]) ? $params['principal_queBeneficiarySSN'][$key] : '';
                        $relationship = !empty($params['principal_queBeneficiaryRelationship'][$key]) ? $params['principal_queBeneficiaryRelationship'][$key] : '';
                        $percentage = !empty($params['principal_queBeneficiaryPercentage'][$key]) ? $params['principal_queBeneficiaryPercentage'][$key] : '';
                        $principalbenficiaryProduct = !empty($params['principal_product'][$key]) ? $params['principal_product'][$key] : '';
                        // $principalbenficiaryProduct = !empty($params['product_id_code_wise'][$principalbenficiaryProduct]) ? $params['product_id_code_wise'][$principalbenficiaryProduct] : '';
                        $insParams = array(
                            'beneficiary_type' => 'Principal',
                            'customer_id' => $customer_id,
                            'product_ids' =>$principalbenficiaryProduct,
                            'name' => $name,
                            'address' => $address,
                            'cell_phone' => $cell_phone,
                            'email' => $email,
                            'relationship' => $relationship,
                            'percentage' => $percentage,
                        );
                        if (!empty($ssn))
                        {
                            $insParams['ssn'] = "msqlfunc_AES_ENCRYPT('" . str_replace("-", "", $ssn) . "','" . $CREDIT_CARD_ENC_KEY . "')";
                            $insParams['last_four_ssn'] = substr(str_replace("-", "", $ssn) , -4);
                        }

                        if (!empty($resBeneficiery))
                        {
                            $updWhr = array(
                                'clause' => 'id = :id',
                                'params' => array(
                                    ':id' => $resBeneficiery['id'],
                                ) ,
                            );
                            $pdo->update("customer_beneficiary", $insParams, $updWhr);
                            $saved_principal_ids[] = $resBeneficiery['id'];

                            $lead_track = array(
                                'status' => 'Updated',
                                'description' => 'Updated customer Principal beneficiary table',
                            );
                        
                            lead_tracking($lead_id,$customer_id,$lead_track);
                        }
                        else
                        {
                            $saved_principal_ids[] = $pdo->insert("customer_beneficiary", $insParams);

                            $lead_track = array(
                                'status' => 'Inserted',
                                'description' => 'Inserted customer Principal beneficiary table',
                            );
                        
                            lead_tracking($lead_id,$customer_id,$lead_track);
                        }
                    }
                    if (count($saved_principal_ids) > 0)
                    {
                        $updWhr = array(
                            'clause' => 'customer_id=:customer_id AND id NOT IN(' . implode(',', $saved_principal_ids) . ') AND beneficiary_type="Principal"',
                            'params' => array(
                                ':customer_id' => $customer_id,
                            ) ,
                        );
                        $pdo->update('customer_beneficiary', array(
                            'is_deleted' => 'Y'
                        ) , $updWhr);
                    }
                }
                else
                {
                    $updWhr = array(
                        'clause' => 'customer_id=:customer_id AND beneficiary_type="Principal"',
                        'params' => array(
                            ':customer_id' => $customer_id,
                        ) ,
                    );
                    $pdo->update('customer_beneficiary', array(
                        'is_deleted' => 'Y'
                    ) , $updWhr);
                }

                $tmpContingent = !empty($params['contingent_queBeneficiaryFullName']) ? $params['contingent_queBeneficiaryFullName'] : array();
                if (!empty($tmpContingent))
                {
                    $saved_principal_ids = array();
                    foreach ($tmpContingent as $key => $value)
                    {
                        $contingent_beneficiary_id = !empty($params['contingent_beneficiary_id'][$key]) ? $params['contingent_beneficiary_id'][$key] : 0;
                        if (!empty($contingent_beneficiary_id))
                        {
                            $sqlBeneficiery = "SELECT id FROM customer_beneficiary where id=:id";
                            $resBeneficiery = $pdo->selectOne($sqlBeneficiery, array(
                                ":id" => $contingent_beneficiary_id
                            ));
                        }
                        else
                        {
                            $resBeneficiery = array();
                        }

                        $name = !empty($params['contingent_queBeneficiaryFullName'][$key]) ? $params['contingent_queBeneficiaryFullName'][$key] : '';
                        $address = !empty($params['contingent_queBeneficiaryAddress'][$key]) ? $params['contingent_queBeneficiaryAddress'][$key] : '';
                        $cell_phone = !empty(phoneReplaceMain($params['contingent_queBeneficiaryPhone'][$key])) ? phoneReplaceMain($params['contingent_queBeneficiaryPhone'][$key]) : '';
                        $email = !empty($params['contingent_queBeneficiaryEmail'][$key]) ? $params['contingent_queBeneficiaryEmail'][$key] : '';
                        $ssn = !empty($params['contingent_queBeneficiarySSN'][$key]) ? $params['contingent_queBeneficiarySSN'][$key] : '';
                        $relationship = !empty($params['contingent_queBeneficiaryRelationship'][$key]) ? $params['contingent_queBeneficiaryRelationship'][$key] : '';
                        $percentage = !empty($params['contingent_queBeneficiaryPercentage'][$key]) ? $params['contingent_queBeneficiaryPercentage'][$key] : '';
                        $contigentbenficiaryProduct = !empty($params['contingent_product'][$key]) ? $params['contingent_product'][$key] : '';
                        // $contigentbenficiaryProduct = !empty($params['product_id_code_wise'][$contigentbenficiaryProduct]) ? $params['product_id_code_wise'][$contigentbenficiaryProduct] : '';
                        $insParams = array(
                            'beneficiary_type' => 'Contingent',
                            'customer_id' => $customer_id,
                            'product_ids' =>$contigentbenficiaryProduct,
                            'name' => $name,
                            'address' => $address,
                            'cell_phone' => $cell_phone,
                            'email' => $email,
                            'relationship' => $relationship,
                            'percentage' => $percentage,
                        );
                        if (!empty($ssn))
                        {
                            $insParams['ssn'] = "msqlfunc_AES_ENCRYPT('" . str_replace("-", "", $ssn) . "','" . $CREDIT_CARD_ENC_KEY . "')";
                            $insParams['last_four_ssn'] = substr(str_replace("-", "", $ssn) , -4);
                        }
                        if (!empty($resBeneficiery))
                        {
                            $updWhr = array(
                                'clause' => 'id = :id',
                                'params' => array(
                                    ':id' => $resBeneficiery['id'],
                                ) ,
                            );
                            $pdo->update("customer_beneficiary", $insParams, $updWhr);
                            $saved_principal_ids[] = $resBeneficiery['id'];

                            $lead_track = array(
                                'status' => 'Updated',
                                'description' => 'Updated customer contingent beneficiary table',
                            );
                        
                            lead_tracking($lead_id,$customer_id,$lead_track);
                        }
                        else
                        {
                            $saved_principal_ids[] = $pdo->insert("customer_beneficiary", $insParams);

                            $lead_track = array(
                                'status' => 'Inserted',
                                'description' => 'Inserted customer contingent beneficiary table',
                            );
                        
                            lead_tracking($lead_id,$customer_id,$lead_track);
                        }
                    }
                    if (count($saved_principal_ids))
                    {
                        $updWhr = array(
                            'clause' => 'customer_id=:customer_id AND id NOT IN(' . implode(',', $saved_principal_ids) . ') AND beneficiary_type="Contingent"',
                            'params' => array(
                                ':customer_id' => $customer_id,
                            ) ,
                        );
                        $pdo->update('customer_beneficiary', array(
                            'is_deleted' => 'Y'
                        ) , $updWhr);
                    }
                }
                else
                {
                    $updWhr = array(
                        'clause' => 'customer_id=:customer_id AND beneficiary_type="Contingent"',
                        'params' => array(
                            ':customer_id' => $customer_id,
                        ) ,
                    );
                    $pdo->update('customer_beneficiary', array(
                        'is_deleted' => 'Y'
                    ) , $updWhr);
                }
                //********* Beneficiery Insert Code End   ********************
                
            }
        }
        else
        {
            $response['status'] = "fail";
            // $response['errors'] = $validate->getErrors();
            // $response['div_step_error'] = $div_step_error;
        }
        return $response;

    }

?>
