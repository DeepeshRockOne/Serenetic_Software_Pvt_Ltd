<div class="white-box prd_add_wrapper">
	<div class="section_space">
  	<div class="clearfix text-right step_btn_wrap">
    	<a href="prd_summary.php?id=<?= $product_id ?>&generatePdf=1" class="btn btn-info btn-lg downloadPdf" >Download PDF</a> <a href="product_builder.php?id=<?= $product_id ?>" class="btn btn-info btn-lg">Back</a>
    </div>
    <div id="htmlDiv" >
      <h4 class="h4_title fs24">Product Descriptions</h4>
    
      <table cellpadding="0" cellspacing="0" border="0" class="fs16">
      	<tbody>
          <tr>
          	<td><h5 class="h5_title p-b-5 p-r-20">Product Company:</h5></td>
            <td valign="top"><?= $company_name ?></td>
          </tr>
          <tr>
          	<td><h5 class="h5_title p-b-5 p-r-20">Product Name:</h5></td>
            <td valign="top"><?= $product_name ?></td>
          </tr>
          <tr>
          	<td><h5 class="h5_title p-b-5 p-r-20">Product ID:</h5></td>
            <td valign="top"><?= $product_code ?></td>
          </tr>
          <tr>
          	<td><h5 class="h5_title p-b-5 p-r-20">Category:</h5></td>
            <td valign="top"><?= $category_name ?></td>
          </tr>
          <tr>
          	<td><h5 class="h5_title p-b-5 p-r-20">Carrier:</h5></td>
            <td valign="top"><?= $carrier_name ?></td>
          </tr>
          <tr>
          	<td><h5 class="h5_title p-b-5 p-r-20">Product Type:</h5></td>
            <td valign="top"><?= $product_type ?></td>
          </tr>
          <?php if(isset($resProductCode) && !empty($resProductCode)){
              $i=0;
              foreach ($resProductCode as $key => $value) { ?>
                 <tr>
                 <?php  if($value['code_no']=='GC'){ ?>
                    <td><h5 class="h5_title p-b-5 p-r-20">Group Code:</h5></td>
                 <?php  }else{ ?>
                    <td><h5 class="h5_title p-b-5 p-r-20">Plan Code <?php echo $i; ?>:</h5></td>
                 <?php } ?>
                    <td valign="top"><?= $value['plan_code_value']; ?></td>
                </tr>
              <?php  $i++;
               }
          } ?>
        </tbody>
      </table>
      <h5 class="h5_title top_space">Member Portal</h5>
      <div class="form-group">
        <div class="textarea_div_display">
         <?= $member_portal ?>
        </div>
      </div>
      <h5 class="h5_title top_space">Inside Agent Portal</h5>
      <div class="form-group">
        <div class="textarea_div_display"><?= $agent_portal ?></div>
      </div>
      <h5 class="h5_title top_space">Limitations & Exclusions</h5>
      <div class="form-group">
        <div class="textarea_div_display"><?= $limitations_exclusions ?></div>
      </div>
      <h4 class="h4_title fs24">Product Rules</h4>
      <h5 class="h5_title">Effective Date</h5>
      <p class="fs16">
        <strong  class="fw-700">Direct Plan</strong> <br />
        <?= $directPolicyText ?> 
        <?php if($direct_policy=="Select Day Of Month"){ ?>
          <strong  class="fw-700">Day of Month:</strong> <?= $effective_day ?><br />
        <?php }?>
        <?php if($direct_policy=="Select Day Of Month" || $direct_policy=="First Of Month"){ ?>
          <strong  class="fw-700">Can be sold until day of month:</strong> <?= $sold_day ?>
        <?php } ?>
      </p>
      <h5 class="h5_title top_space">Association Requirement</h5>
      <p  class="fs16"><?= ($is_association_require=='Y') ? 'Yes' : 'No' ?> <br />
        <?php if($is_association_require=='Y') {
          if(isset($associationRes) && !empty($associationRes)){
						foreach ($associationRes as $akey => $avalue) { ?>
            <p></p>
          <strong class="fw-700">Association:</strong> <?php echo $avalue['name']; ?> <br />
          <strong class="fw-700">Association Fee Type:</strong> <?php echo $avalue['is_association_fee_included']=='Y'?'Included in Price':"Not Included in Price"; ?> <br />
          <strong class="fw-700">Association Fee Amount:</strong> $<?php echo $avalue['price']; ?>
        <?php }}} ?>
      </p>
      <h5 class="h5_title top_space">Available States</h5>
      <div class="row m-b-15">
        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-6">
          <?php $stateRowCount=0; ?>
          <?php if(!empty($available_state)){ ?>
            <?php foreach ($available_state as $key => $state) { ?>
              <?php if($stateRowCount!=0 && $stateRowCount%13 == 0){?>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-4 col-xs-6">
              <?php } ?>
              <div class="list_label" style="width:100%">
                <?php 
                  $state_name=getname('states_c',$state,'short_name','name');
                  echo $state_name.', '.$state .'</br>';
                ?>
              </div>
              <?php $stateRowCount++; ?>
            <?php } ?>
          <?php } ?>
        </div>
      </div>

      <h5 class="h5_title top_space">Specific Zipcodes Available</h5>
      <div class="form-group">
        <div class="textarea_div_display"><?= $restricted_zipcodes_text ?></div>
      </div>

      <h5 class="h5_title top_space">Plan Options</h5>
      <p class="fs16"><?= $coverage_text?></p>

      <h5 class="h5_title top_space">Age Restrictions</h5>
      <p class="fs16">
        <strong class="fw-700">Primary:</strong> <?= $primary_text ?> <br /> 
        <strong class="fw-700">Spouse:</strong> <?= $spouse_text ?>   <br />      
        <strong class="fw-700">Children:</strong> <?= $children_text ?>
      </p>

      <h5 class="h5_title top_space">Child Products</h5>
      <p class="fs16"><?= $childProduct_text ?></p>
      
      <h5 class="h5_title top_space">Additional Product Combination Rules</h5>
      <p class="fs16"><strong class="fw-700">May Not Have:</strong> <?= $exluded_product_text ?></p>
      <p class="fs16"><strong class="fw-700">Must Have:</strong> <?= $required_product_text ?></p>
      <p class="fs16"><strong class="fw-700">Auto Assign:</strong> <?= $auto_assisgn_product_text ?></p>
      <p class="fs16"><strong class="fw-700">Packaged Product:</strong> <?= $packaged_product_text ?></p>
      
      <h5 class="h5_title top_space">Termination</h5>
      <p class="fs16"><strong class="fw-700">Date Rules:</strong> <?= $termination_rule ?> <br />
        <strong class="fw-700">Reinstate Options:</strong> <?= $reinstate_option_text ?> <br />
        <strong class="fw-700">Reenroll Options:</strong> <?= $reenroll_option_text ?>
      </p>

      <h4 class="h4_title fs24">Application</h4>
      <div class="row">
        <div class="col-sm-6 col-md-3">
          <h5 class="h5_title">Member Details Asked</h5>
          <p class="fs16">
            <?php if(!empty($member_details_asked)){
              foreach ($member_details_asked as $key => $value) { 
                  
                  if(!empty($memberDetailsArray[$value])){
                    echo $memberDetailsArray[$value]."<br />";  
                  }
                  if(!empty($matrixPricingArray[$value])){
                    echo $matrixPricingArray[$value]."<br />";  
                  }
              } 
            } ?>
          </p>           
        </div>
        <div class="col-sm-6 col-md-3">
          <h5 class="h5_title">Member Details Required</h5>
          <p class="fs16">
            <?php if(!empty($member_details_required)){
              foreach ($member_details_required as $key => $value) { 
                  
                  if(!empty($memberDetailsArray[$value])){
                    echo $memberDetailsArray[$value]."<br />";  
                  }
                  if(!empty($matrixPricingArray[$value])){
                    echo $matrixPricingArray[$value]."<br />";  
                  }
              } 
            } ?>
          </p>           
        </div>
        <div class="col-sm-6 col-md-3">
          <h5 class="h5_title">Spouse Details Asked</h5>
          <p class="fs16">
            <?php if(!empty($spouse_details_asked)){
              foreach ($spouse_details_asked as $key => $value) { 
                  
                  if(!empty($spouseDetailsArray[$value])){
                    echo $spouseDetailsArray[$value]."<br />";  
                  }
                  if(!empty($matrixPricingArray[$value])){
                    echo $matrixPricingArray[$value]."<br />";  
                  }
              } 
            } ?>
          </p>           
        </div>
        <div class="col-sm-6 col-md-3">
          <h5 class="h5_title">Spouse Details Required</h5>
          <p class="fs16">
            <?php if(!empty($spouse_details_required)){
              foreach ($spouse_details_required as $key => $value) { 
                  
                  if(!empty($spouseDetailsArray[$value])){
                    echo $spouseDetailsArray[$value]."<br />";  
                  }
                  if(!empty($matrixPricingArray[$value])){
                    echo $matrixPricingArray[$value]."<br />";  
                  }
              } 
            } ?>
          </p>           
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-6 col-md-3">
          <h5 class="h5_title">Dependant Details Asked</h5>
          <p class="fs16">
            <?php if(!empty($dependent_details_asked)){
              foreach ($dependent_details_asked as $key => $value) { 
                  if(!empty($dependantDetailsArray[$value])){
                    echo $dependantDetailsArray[$value]."<br />";  
                  }
                  if(!empty($matrixPricingArray[$value])){
                    echo $matrixPricingArray[$value]."<br />";  
                  }
              } 
            } ?>
          </p>                
        </div>
        <div class="col-sm-6 col-md-3">
          <h5 class="h5_title">Dependant Details Required</h5>
          <p class="fs16">
            <?php if(!empty($dependent_details_required)){
              foreach ($dependent_details_required as $key => $value) { 
                  if(!empty($dependantDetailsArray[$value])){
                    echo $dependantDetailsArray[$value]."<br />";  
                  }
                  if(!empty($matrixPricingArray[$value])){
                    echo $matrixPricingArray[$value]."<br />";  
                  }
              } 
            } ?>
          </p>                
        </div>
      </div>
      <h5 class="h5_title top_space">Verification</h5>
      <p class="fs16"><strong class="fw-700">Options:</strong> <?= $enrollment_verification_options_list ?> <br />
        <strong class="fw-700">E-Sign Special Terms & Conditions:</strong> <?= (in_array("eSign",$enrollment_verification_options) && $is_eSignTermsCondition=='Y') ? 'Required' : 'Not Required'?> 
        <?php if (in_array("eSign",$enrollment_verification_options) && $is_eSignTermsCondition=='Y' && $eSignTermsCondition_doc_old != ''){ ?>
            <a href="<?= $ESIGN_TERMS_CONDITION_WEB.$eSignTermsCondition_doc_old ?>" class="fs18 text-blue" target="_blank">
                    <i class="fa fa-file"></i>
            </a>
        <?php } ?>
      </p>
      <?php if (in_array("eSign",$enrollment_verification_options) && $is_eSignTermsCondition=='Y' && $eSignTermsCondition_desc != ''){ ?>
      <div class="form-group">
        <div class="textarea_div_display"><?= $eSignTermsCondition_desc ?></div>
      </div>
      <?php } ?>

      <h5 class="h5_title top_space">Agent Requirements</h5>
      <p class="fs16"> 
        <strong class="fw-700">Licenses:</strong>  <br />
        <?= ($is_agent_requirement=='Y') ? 'Required' : 'Not Required' ?> <br />
        <?php if($is_agent_requirement=='Y' && !empty($agent_requirement)) { ?>
          <?php foreach ($agent_requirement as $key => $value) {
            if($value=="GeneralLines_Both"){
                echo "General Lines/Both <br />";
            }else{
              echo $value.'<br />';
            } 
          } ?>
        <?php } ?>
        <br />
        <strong class="fw-700">License Rules:</strong><br /> 
        <?= $license_rule ?>
      </p>

      <h4 class="h4_title fs24">Pricing</h4>
      <h5 class="h5_title top_space">Member Payment Subscription Type</h5>
      <p class="fs16"><?= $member_payment ?> Payment <br />
        <?= ($member_payment=='Recurring') ? $member_payment_type : '' ?>
      </p>

      <h5 class="h5_title top_space">Benefit Teir Pricing</h5>
      <?php if(!empty($coverage_options)) { ?>
        <?php foreach ($coverage_options as $key => $value) { ?>
          <?php 
            $coverage_name = getname('prd_plan_type',$value,'title','id');

            if($coverage_name == "Member"){
              $coverage_name='Member Only';
            }

            $prdMatrixSql="SELECT id,price,non_commission_amount,commission_amount FROM prd_matrix where product_id=:product_id AND plan_type=:plan_type AND is_deleted='N'";
            $prdMatrixWhr=array(":product_id"=>$product_id,":plan_type"=>$value);
            $prdMatrixRes=$pdo->select($prdMatrixSql,$prdMatrixWhr);
            if(isset($prdMatrixRes) && !empty($prdMatrixRes)){
              foreach ($prdMatrixRes as $key => $value) {
          ?>
          <p class="fs16"><strong class="fw-700"><?= $coverage_name ?> </strong> <br />
            Price: $<?= $value['price'] ?> <br />
            Non Commissionable: $<?= $value['non_commission_amount'] ?> <br />
            Commissionable: $<?= $value['commission_amount'] ?> <br />
          </p>
        <?php }} } ?>
      <?php } ?>
      <?php if($is_product_commissionable == 'Y'){ ?>
        <div class="row ">
          <?php if(!empty($commission_price_array)){ ?>
            <?php if(!empty($agentCodedLevelRes)){ ?>
              <?php foreach ($agentCodedLevelRes as $key => $codedLevel) { ?>
                <?php if($is_commissionable_by_tier == 'N') { ?>
                  <?php if(!empty($commission_price_array)){ ?>
                    
                      <div class="col-md-4 col-sm-6 m-t-20">
                         <h5 class="h5_title"><?= $codedLevel['level'] ?> : <?= $commission_price_array[$codedLevel['level']]['amount_type'] == "Percentage" ? '%' : '$' ?> <?= $commission_price_array[$codedLevel['level']]['amount'] ?></h5>   
                      </div>
                    
                  <?php } ?>
                <?php }else{ ?>
                  <?php if(!empty($commission_price_array)){ ?>
                    <div class="col-md-4 col-sm-6 m-t-20">
                      <h5 class="h5_title"><?= $codedLevel['level'] ?></h5>   
                      <table border="0" cellpadding="0" cellspacing="0" class="fs16"> 
                        <tbody>
                          <?php foreach ($commission_price_array as $planId => $commission) { ?>
                            <?php 
                              $plan_name='';
                              if($planId==1){
                                $plan_name = 'Member Only';
                              }else if($planId==5){
                                $plan_name = 'Member + One';
                              }else if($planId==2){
                                $plan_name = 'Member & Child(ren)';
                              }else if($planId==3){
                                $plan_name = 'Member & Spouse';
                              }else if($planId==4){
                                $plan_name = 'Family';
                              }
                            ?>
                            <tr>
                               <td><strong class="fw-700"><?= $plan_name ?>:</strong></td>
                               <td> <?= $commission_price_array[$planId][$codedLevel['level']]['amount_type'] == "Percentage" ? '%' : '$' ?> <?= $commission_price_array[$planId][$codedLevel['level']]['amount']  ?></td>
                             </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  <?php } ?>
                <?php } ?>
              <?php } ?>
            <?php } ?>
          <?php } ?>
        </div>
      <?php } ?>
      <div class="visible-lg m-t-30 clearfix p-t-20">&nbsp;</div>
    </div>     
  </div>
  <div class="section_space clearfix text-right step_btn_wrap">
    <a href="prd_summary.php?id=<?= $product_id ?>&generatePdf=1" class="btn btn-info btn-lg downloadPdf">Download PDF</a> <a href="product_builder.php?id=<?= $product_id ?>" class="btn btn-info btn-lg">Back</a>
  </div>
</div>