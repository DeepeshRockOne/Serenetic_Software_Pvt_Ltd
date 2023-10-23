<style type="text/css">
.steamline .sl-date {  font-size: 12px;}
</style>
<script>
$(function () {
        $('.email_content_popup').colorbox({iframe: true, width: '800px;', height: '600px;'});
        $('.commissionJson').colorbox({iframe: true, width: '800px;', height: '500px;'});
        $('.descriptionPopup').colorbox({iframe: true, width: '800px;', height: '500px;'});
        $(document).on("click",".descriptionPopup",function(){
            $data=$(this).attr('data-desc');
            $encode=$(this).attr('data-encode');
            if($data=="-"){
                $("#activityPageBody").html("");
            }else{
                if($encode == '' || $encode == undefined){
                    $aMyUTF8Output = base64DecToArr($data);
                    $data = UTF8ArrToStr($aMyUTF8Output);
                }
                $("#activityPageBody").html($data);
            }
            $.colorbox({
                inline: true , 
                href: '#activityPageColorbox',
                width: '70%', 
                height: '80%',
            });
        });
    });
var from_limit = "<?=checkIsset($from_limit)?>";
var total_rows = "<?=checkIsset($total_rows)?>";
</script>
<?php
$tz = new UserTimeZone('m/d/Y g:i A T',$_SESSION['groups']['timezone']);

$iconArr = array(
    'admin' => array('class' => 'type-e', 'color' => 'primary'),
    'note' => array('class' => 'icon-note', 'color' => 'danger'),
    'triggers' => array('class' => 'icon-envelope', 'color' => 'success'),
    'sms' => array('class' => 'icon-screen-smartphone', 'color' => 'success'),
    'customer' => array('class' => 'type-e', 'color' => 'success'),
);

$global_heading = [
    'fname' => 'First Name',
    'lname' => 'Last Name',
    'phone' => 'Mobile Number',
    'email' => 'Email',
    'password' => 'Password',
    'old_fname' => 'Old First Name',
    'old_lname' => 'Old Last Name',
    'old_phone' => 'Old Mobile Number',
    'old_email' => 'Old Email',
    'old_password' => 'Old Password',
    'rule_code' => 'Commission ID',
    'status' => 'Status',
    'commission_on' => 'Commission Type',
    'change_commission_rate_after' => 'Change Commission Rate',
    'calculate_by' => 'Commission Calculate By',
    'commission_duration' => 'Commission Duration',
    'commission_json' => 'Commission Rule',
    'stop_commission_after' => 'Stop Commission Rule After',
    'new_business_commission_duration' => 'New Business Duration',
    'renewal_commission_duration' => 'Renewal Duration',
    'from_renewal' => 'Commission Rate After',
    'display_id' => 'Display Id',
    'name' => 'Name',
    'product_group_1' => 'Product Group 1',
    'product_group_2' => 'Product Group 2',
    'product_group_3' => 'Product Group 3',
    'product_group_4' => 'Product Group 4',
    'product_group_5' => 'Product Group 5',
    'product_group_6' => 'Product Group 6',
    'product_group_7' => 'Product Group 7',
    'product_group_8' => 'Product Group 8',
    'product_group_9' => 'Product Group 9',
    'product_group_10' => 'Product Group 10',
    'product_group_url_1' => 'Product Group Url 1',
    'product_group_url_2' => 'Product Group Url 2',
    'product_group_url_3' => 'Product Group Url 3',
    'product_group_url_4' => 'Product Group Url 4',
    'product_group_url_5' => 'Product Group Url 5',
    'product_group_url_6' => 'Product Group Url 6',
    'product_group_url_7' => 'Product Group Url 7',
    'product_group_url_8' => 'Product Group Url 8',
    'product_group_url_9' => 'Product Group Url 9',
    'product_group_url_10' => 'Product Group Url 10',
    'company_name' => 'Company Name',
    'contact_fname' => 'Contact First Name',
    'contact_lname' => 'Contact Last Name',
    'short_name' => 'Short Name',
    'carrier_id' => 'Product Carrier',
    'product_code' => 'Product ID',
    'product_name' => 'Product Name',
    'title' => 'Category Name',
    'category_id' => 'Product Category',
    'company_id' => 'Company Name',
    'fee_type' => 'Fee Type',
    'pricing_effective_date' => 'Effective Date',
    'pricing_termination_date' => 'Termination Date',
    'initial_purchase' => 'Is this fee charged on initial purchase?',
    'is_fee_on_renewal' => 'Does this fee apply on renewals?',
    'fee_renewal_type' => 'Set the number of renewals',
    'fee_renewal_count' => 'Set the number of renewals Count',
    'is_benefit_tier' => 'Vary by benefit tier?',
    'price' => 'Price',
    'price_calculated_on' => 'How is the fee calculated?',
    'price_calculated_type' => 'How is the % calculated?',
    'tax_id' => 'Tax ID',
    'zipcode' => 'Zip Code',
    'state' => 'State',
    'city' => 'City',
    'address' => 'Address',
    'address2' => 'Address2',
    'coll_doc_url' => 'Url',
    'coll_type' => 'Type',
    'video_type' => 'Video Type',
    'user_group' => 'User Group',
    'effective_date' => 'Effective Date',
    'termination_date' => 'Termination Date',
    'description' => 'Description',
    'earned_on_new_business' => 'Earned on new business?',
    'earned_on_renewal' => 'Earned on renewals?',
    'is_fee_by_benefit_tier' => 'Amount vary by benefit tier?',
    'amount_calculated_on' => 'How is the amount calculated?',
    'fee_per_calculate_on' => 'How is the % calculated?',
    'brodcast_name' => 'Brodcast Name',
    'from_address' => 'From Address',
    'subject' => 'Subject',
    'user_type' => 'User Type',
    'is_for_specific' => 'Is Brodcast for Specific Group?',
    'email_template_id' => 'Template Name',
    'mail_content' => 'Mail Content',
    'is_schedule_in_future' => 'Is scheduled ?',
    'admin_level' => 'Admin Level',
    'specific_user_ids' => 'Specific users',
    'lead_tags' => 'Lead tags',
    'product_ids' => 'Products ID',
    'product_status' => 'Product Status',
    'schedule_date_1' => 'Schedule Date 1',
    'schedule_date_2' => 'Schedule Date 2',
    'schedule_date_3' => 'Schedule Date 3',
    'schedule_date_4' => 'Schedule Date 4',
    'schedule_date_5' => 'Schedule Date 5',
    'schedule_date_6' => 'Schedule Date 6',
    'schedule_date_7' => 'Schedule Date 7',
    'schedule_date_8' => 'Schedule Date 8',
    'schedule_date_9' => 'Schedule Date 9',
    'schedule_date_10' => 'Schedule Date 10',
    'admin_id' => "Admins",
    'per_calculated_by' => 'percentage be calculated by',
    'amount_type' => 'Processing Fee',
    'is_fee_on_new_business' => 'Apply fee on new business?',
    'number_of_renewal' => 'Number of renewals',
    'advance_month' => 'Advance Months',
    'fee' => 'Fee',
    'renewal_type' => 'Set the number of renewals',
    'product_type' => 'Application Method',
    'plan_code_value' => 'Plan Code',
    'agent_info' => 'Information displayed above benefits',
    'membership_ids' => 'Memberships',
    'direct_product' => 'Direct Plan',
    'effective_day' => 'Effective Day',
    'sold_day' => 'Sold Day',
    'term_automatically_within'=>'Term After',
    'term_automatically_within_type'=>'Term After Period',
    'reinstate_option'=>'Reinstate Option',
    'reinstate_within'=>'Reinstate After',
    'reinstate_within_type'=>'Reinstate After Period',
    'reenroll_options'=>'Reenroll Option',
    'reenroll_within'=>'Reenroll After',
    'reenroll_within_type'=>'Reenroll After Period',
    'term_back_to_effective'=>'Can product be termed back to effective to eliminate plan from ever being active',
    'term_automatically'=>'Does this product terminate automatically after a set period of time',
    'is_specific_zipcode'=>'Is Specific Zipcode',
    'no_sale_state_coverage_continue'=>'No Sale State Plan Continue',
    'family_plan_rule'=>'Family Plan Options',
    'is_primary_age_restrictions'=>'Primary Age Restrictions',
    'primary_age_restrictions_from'=>'Primary Age Restrictions From',
    'primary_age_restrictions_to'=>'Primary Age Restrictions To',
    'is_children_age_restrictions'=>'Child Age Restrictions',
    'children_age_restrictions_from'=>'Child Age Restrictions From',
    'children_age_restrictions_to'=>'Child Age Restrictions To',
    'is_spouse_age_restrictions'=>'Spouse Age Restrictions',
    'spouse_age_restrictions_from'=>'Spouse Age Restrictions From',
    'spouse_age_restrictions_to'=>'Spouse Age Restrictions To',
    'maxAgeAutoTermed'=>'Max Age Auto Termed',
    'allowedBeyoundAge'=>'Allow Beyond Age Restriction',
    'is_member_asked'=>'Member Asked',
    'is_member_required'=>'Member Required',
    'is_spouse_asked'=>'Spouse Asked',
    'is_spouse_required'=>'Spouse Required',
    'is_child_asked'=>'Child Asked',
    'is_child_required'=>'Child Required',
    'is_beneficiary_required'=>'Beneficiary Required',
    'is_principal_beneficiary_asked'=>'Principal Beneficiary Asked',
    'is_principal_beneficiary_required'=>'Principal Beneficiary Required',
    'is_contingent_beneficiary_asked'=>'Contingent Beneficiary Asked',
    'is_contingent_beneficiary_required'=>'Contingent Beneficiary Required',
    'is_license_require'=>'License Require',
    'license_type'=>'License Type',
    'license_rule'=>'License Rule',
    'payment_type'=>'Payment Type',
    'payment_type_subscription'=>'Payment Type Subscription',
    'pricing_model'=>'Pricing Model',
    'match_globals'=>'Match Globals',
    'non_commission_amount'=>'Non Commissionable Amount',
    'commission_amount'=>'Commissionable Amount',
    'is_new_price_on_renewal'=>'New Price On Renewal',
    'age_from'=>'Age From',
    'age_to'=>'Age To',
    'gender'=>'Gender',
    'smoking_status'=>'Smoking Status',
    'tobacco_status'=>'Tobacco Status',
    'height_feet'=>'Height Feet',
    'height_inch'=>'Height Inch',
    'weight'=>'Weight',
    'weight_type'=>'Weight Type',
    'no_of_children'=>'No Of Children',
    'has_spouse'=>'Has Spouse',
    'spouse_age_from'=>'Spouse Age From',
    'spouse_age_to'=>'Spouse Age To',
    'spouse_gender'=>'Spouse Gender',
    'spouse_smoking_status'=>'Spouse Smoking Status',
    'spouse_tobacco_status'=>'Spouse Tobacco Status',
    'spouse_height_feet'=>'Spouse Height Feet',
    'spouse_height_inch'=>'Spouse Height Inch',
    'spouse_weight'=>'Spouse Weight',
    'spouse_weight_type'=>'Spouse Weight Type',
    'benefit_amount'=>'Benefit Amount',
    'plan_type'=>'Benefit Tier',
    'rate_change_within'=>'Banded Rate Change',
    'rate_change_within_type'=>'Banded Rate Change Period',
    'rate_change_trigger'=>'Banded Rate Change Trigger',
    'rate_change_range'=>'Banded Rate Change Range',
    'child_dependent_rate_calculation'=>'Child Dependent Rate Calculation',
    'allowed_child'=>"#of child allowed",
    'is_banded_rates'=>"Banded Rates",
    'banded_rate_change_after'=>"Change Banded Rate Following",
    'is_banded_criteria'=>"Banded Criteria",
    'is_primary_eldest'=>'Primary Reuired To Be Eldest',
    'is_rider_for_enrolles'=>"product offer a rider for enrollees",
    'offer_rider_for'=>'which enrollee offer a rider for',
    'rider_rate'=>'Rider Rate',
    'rider_type'=>'Rider Type',
    'rider_product_id'=>'Rider Product',
    'rider_product'=>'Rider Product',
    'rider_question'=>'Rider Question',
    'public_name' => 'Public Name',
    'public_email' => 'Public Email',
    'public_phone' => 'Public Phone',
    'user_name' =>  'User Name',
    'cell_phone' =>'Cell Phone',
    'last_four_ssn' =>'Last Four SSN',
    'company_name' => 'Agency Legal Name',
    'company_address' => 'Agency Address',
    'company_address_2' => 'Agency Address 2',
    'company_city' => 'Agency City',
    'company_state' => 'Agency State',
    'company_zip' => "Agency Zipcode",
    'npn' => 'NPN',
    'display_in_member' =>  'Display Name',
    'is_branding' =>'Is Branding',
    'e_o_coverage' =>'E&O Plan',
    'e_o_amount' => 'E&O Amount',
    'writing_number' => 'Writing Number',
    'agent_writing_states' => 'Agent Writing State',
    'agent_writing_number' => 'Agent Writing Number',
    'license_not_expire' => 'License Not Expire',
    'license_active_date' => 'License Active Date',
    'license_auth' => 'License Authoruty',
    'license_num' => 'License Number',
    'selling_licensed_state' => 'Selling License State',
    'e_o_expiration' => 'E&O Expiration',
    'account_type' =>'Account Type',
    'ach_account_type' =>'ACH Account Type',
    'payment_mode' =>'Payment mode',
    'is_default' =>'Default',
    'is_contract_approved' =>'Contract',
    'by_parent' => 'E&O By Parent',
    'is_fee_on_commissionable' => 'Fee Commissionable',
    'is_member_benefits' => 'Member Benefits',
    'is_member_portal' => 'Display in Member Portal',
    'merchant_id' => 'Merchant Id',
    'sandbox_details' => 'Sandbox Details',
    'gateway_name' => 'Gateway Name',
    'is_ach_accepted' => 'Is ACH Accepted',
    'is_cc_accepted' => 'Is CC Accepted',
    'monthly_threshold_sale' => 'Monthly threshold sale',
    'is_ach_accepted' => 'Is ACH Accepted',
    'sales_threshold_value' => 'Sales Threshold Value ',
    'refund_threshold_value' => 'Refund Threshold Value',
    'chargeback_threshold_value' => 'Chargeback Threshold Value',
    'assigned_agent_selected' => 'Assigned Agent Selected',
    'assigned_agent_unselected' => 'Assigned Agent Unselected',
    'assigned_product_selected' => 'Assigned Product Selected',
    'assigned_product_unselected' => 'Assigned Product Unselected',
    'products_variation_selected' => 'Products Variation Selected',
    'products_variation_unselected' => 'Products Variation Unselected',
    'is_sales_threshold' => 'Is Sales Threshold',
    'is_refund_threshold' => 'Is Refund Threshold',
    'is_chargeback_threshold' => 'Is Chargeback Threshold',
    'agents_downline_selected' => 'Agents Downline Selected',
    'agents_loa_selected' => 'Agents LOA Selected',
    'agents_downline_unselected' => 'Agents Downline Unselected',
    'agents_loa_unselected' => 'Agents LOA Unselected',
    'merchant_user_name' => 'Merchant Username',
    'is_assigned_to_all_product' => 'Assigned to all Product',
    'is_assigned_to_all_agent' => 'Assigned to all Agent',
    'is_default_for_ach' => 'Is default for ach',
    'is_default_for_cc' => 'Is default for cc',
    'acceptable_cc' => 'Acceptable Credit Card',
    'card_type' => 'Card Type',
    'exp_month' => 'Expiration month',
    'exp_year' => 'Expiration Year',
    'ip_address' => 'IP Address',
    'last_four_digit' => 'Last Four Digit',
    'payment_status' => 'Payment Status',
    'file_type' => 'File Type', 
    'add_change_file' => 'Add Change File', 
    'full_file' => 'Full File', 
    'schedule_end_type' => 'End Repeat', 
    'on_date' => 'On Date', 
    'never' => 'Never', 
    'schedule_end_val' => 'End Repeat Date', 
    'generate_via' => 'Generate Via', 
    'schedule_frequency' => 'Schedule Frequency Day', 
    'last_cc_ach_no' => 'Last Four ACH or CC Number'
];
?> 

<div id="activity_inline_colorbox_div" style="display: none;">
  <div id="activityPageColorbox">
    <div class="panel panel-default">
      <div class="panel-heading br-b">Description</div>
      <div class="panel-body panel-shadowless" id="activityPageBody"></div>
    </div>
  </div>
</div>
  <ul class="timeline left-timeline lead_activity steamline">
      <?php
      if ($total_rows > 0) {
      foreach ($fetch_rows as $row) {?>
        <li class="timeline-inverted" data-user_type="<?=$row['user_type']?>" data-action="<?=$row['entity_action']?>">
        <div class="timeline-badge  <?php echo isset($iconArr[$row['entity_type']]) ? $iconArr[$row['entity_type']]['color'] : "info"; ?>"><i class="<?php echo isset($iconArr[$row['entity_type']]) ? $iconArr[$row['entity_type']]['class'] : "type-e"; ?>"></i>
        </div>
        <div class="timeline-panel">
            <div class="timeline-heading">
                <h4 class="timeline-title"><?=$row['entity_action']?></h4>
                <p><small class="text-muted"><?php echo $tz->getDate($row['changed_at']); ?></small></p>
            </div>
            <div class="timeline-body">
                <?php $content = json_decode($row['description'],true); ?>
                <?php $extra_content = json_decode($row['extra'],true); ?>
                <?php
                    if(!empty($content))
                    {
                        foreach($content as $key => $value)
                        {
                            if($key=='ac_link') { ?>
                                <?php foreach($value as $ekey => $evalue){ ?>
                                    <p>
                                <?=$evalue['text']?><a href="<?= $evalue['href'] ?>" class="<?= $evalue['class'] ?>" title="<?=$evalue['title']?>" id="<?=$evalue['id']?>" onclick="<?=$evalue['on_click']?>" data-toggle="<?=$evalue['data_toggle']?>" ><?= $evalue['label'] ?></a> 
                                    </p>
                                <?php } ?>
                            <?php }else if($key == 'ac_email_link'){?>
                                <p>
                                <?php foreach($value as $ekey => $evalue){ ?>
                                <?=$evalue['text']?><a href="<?= $evalue['href'] ?>" class="<?= $evalue['class'] ?>" title="<?=$evalue['title']?>" id="<?=$evalue['id']?>" onclick="<?=$evalue['on_click']?>" data-toggle="<?=$evalue['data_toggle']?>" ><?= $evalue['label'] ?></a> 
                                <?php } ?>
                                </p>
                            <?php }else if($key == 'ac_sms_link'){ ?>
                                <p>
                                <?php foreach($value as $skey => $svalue){ ?>
                                <?=$svalue['text']?><a href="<?= $svalue['href'] ?>" class="<?= $svalue['class'] ?>" title="<?=$svalue['title']?>" id="<?=$svalue['id']?>" onclick="<?=$svalue['on_click']?>" data-toggle="<?=$svalue['data_toggle']?>" ><?= $svalue['label'] ?></a>
                                <?php } ?>
                                </p>
                            <?php }else if($key == 'key_value'){ ?>
                                <?php foreach($value['desc_arr'] as $kkey => $kvalue){ ?>
                                        <?php if(preg_match('/blank_\d/',$kkey)){?>
                                            <p></p>
                                        <?php }else{ ?>
                                            <p><?= !empty($global_heading[$kkey]) ? $global_heading[$kkey] : (is_numeric($kkey)?' ':ucfirst($kkey).' :') ?> <?= $kvalue ?></p>
                                        <?php } ?>
                                <?php } ?>
                            <?php }else if($key == 'ac_message'){ ?>
                                <p>
                                    <?php foreach($value as $ekey => $evalue){ ?>
                                        <?php if(preg_match('/ac_red_\d/',$ekey)){?>
                                            <?php if(!empty($evalue['href'])) { ?>
                                                <a href="<?= $evalue['href'] ?>" data-desc="<?= checkIsset($evalue['data-desc']) ?>" class="text-red <?= checkIsset($evalue['class']) ?>" data-encode="<?= checkIsset($evalue['data-encode']) ?>"><?= $evalue['title'] ?></a>
                                            <?php }else{ ?>
                                                <span class="text-red"><?= $evalue['title'] ?></span>
                                            <?php } ?>
                                        <?php }else if(preg_match('/ac_message_\d/',$ekey)){?>
                                            <span><?= $evalue ?></span>
                                        <?php } ?>
                                    <?php } ?>
                                </p>
                            <?php }else if(preg_match('/ac_description_link_\d/',$key) || $key == 'ac_description_link'){ ?>
                                <p>
                                    <?php foreach ($value as $desHeading => $descValue) { ?>
                                        <?= $desHeading ?> <a href="#javascript:void(0)" data-desc="<?= $descValue['data-desc'] ?>" class="text-red <?= $descValue['class'] ?>" data-encode="<?= checkIsset($descValue['data-encode']) ?>"><?= $descValue['title'] ?></a>
                                    <?php } ?>
                                </p>
                            <?php }else if($key == 'ac_commission_link'){ ?>
                                <p>
                                    <?php foreach ($value as $commHeading => $commValue) { ?>
                                        <?= $commHeading ?> <a href="<?= $ADMIN_HOST ?>/activity_commission_view.php?activity=<?= md5($row['id']) ?>&type=<?= $commHeading ?>" class="<?= $commValue['class'] ?>"><?= $commValue['title'] ?></a>
                                    <?php } ?>
                                </p>
                            <?php }else { ?>
                                <?php if(!is_array($value)) {?>
                                    <p><?= $value ?></p>
                                <?php }else{
                                    foreach($value as $val) { ?>
                                    <p><?= $val ?></p>
                                <?php } } ?>
                            <?php }
                        }
                    }
                ?>
                <div class="timeline_ip_right text-gray"><?=$row['ip_address'];?></div>
            </div>
        </div>
        </li>
      <?php
      }
    }else { ?>
        <li  class="timeline-inverted" >
            <div class="timeline-panel br-n">
                <div class="timeline-body text-center">
                    <p class="m-t-20 m-b-0">No more Records Found!</p>
                </div>
            </div>
        </li>
  <?php  }
?>
</ul>