<style type="text/css"> 
.healthcare_section { padding:15px; }
.healthcare_section .expand_box { cursor:pointer; padding:30px; background:#fff; box-shadow: 0 0 14px 0 rgba(0, 0, 0, 0.2); position:relative; margin-bottom:35px; color:#6A6A6A; }
.healthcare_section .expand_box .box_content { height:98px; overflow:hidden; }

.healthcare_section .expand_box.expanded { cursor: inherit; }
.healthcare_section .expand_box.expanded .box_content { height: inherit; overflow:visible; }
.healthcare_section .expand_box.expanded .box_content > h4 { color:#000; }
.healthcare_section .expand_box.expanded .box_content ul{margin:0px; padding:0px;}
.healthcare_section .expand_box.expanded .right_view { color:#fff; }
.healthcare_section .expand_box.expanded .right_view .view { display: none; }
.healthcare_section .expand_box.expanded .right_view .close_panel { display:  block; }
.healthcare_section .expand_box.expanded .right_view .close_panel span { line-height:18px; font-weight:300; }
.healthcare_section .expand_box .box-heading { display: inline-block; width:100%; margin-bottom:0px; }
.healthcare_section .expand_box .box-heading h4 { margin:0 0 25px 0; font-size:22px; color: #434343; font-weight:400; line-height:26px; }
.healthcare_section .expand_box .box-heading h6 { color: #000000; font-family: Lato; font-size: 16px; font-weight: bold; line-height: 24px; }
.healthcare_section .expand_box .right_view { float:right; font-size: 11px; font-weight:bold; color: #747474; text-transform:uppercase; margin-top:5px; position:absolute; top:30px; right:30px; letter-spacing: 1px; }
.healthcare_section .expand_box .right_view span { background-color:#1e293b; width:18px; height:18px; text-align:center; line-height:21px; color:#fff; display:inline-block; border-radius:100%; font-size: 14px; letter-spacing:0; text-transform:lowercase; vertical-align: middle; position: relative; top: -1px; }
.healthcare_section .expand_box .right_view a { color:#747474; }
.healthcare_section .expand_box .right_view .close_panel { display:none; }
.healthcare_section .expand_box p { line-height: 24px; margin-bottom: 21px; font-size:16px; color:#000; }
.healthcare_section .expand_box .line-title { text-align:center; text-transform:uppercase; margin:25px 0; opacity: 0; }
.healthcare_section .expand_box.expanded .line-title { opacity: 1; }
.healthcare_section .expand_box hr.highlight { opacity:0; }
.healthcare_section .expand_box.expanded hr.highlight { opacity:1; }
.healthcare_section .expand_box .line-title span { font-size:18px; font-weight:bold; position:relative; display:inline-block; margin:0 10px; vertical-align:middle; letter-spacing:1px; color:#000; }
.healthcare_section .expand_box .line-title span:after { content:""; width:100px; background:#fff; height:1px; display:inline-block; margin-left:20px; vertical-align:middle; }
.healthcare_section .expand_box .line-title span:before { content:""; width:100px; background:#fff; height:1px; display:inline-block; margin-right:20px; vertical-align:middle; }
.healthcare_section .expand_box h4.list-title { font-size: 24px; margin-bottom:15px; margin-top:0; font-weight: bold; line-height: normal; }
.healthcare_section .expand_box h4.list-title.fs22 { font-size:21px; }
.healthcare_section .expand_box .box_content > ul > li { color:#000; font-size: 16px; line-height: 24px; display:block; }
.healthcare_section .expand_box .box_content > ul > li:before { content:"\f111"; font-family:'FontAwesome'; margin-right:8px; font-size:8px; vertical-align:top; }
hr.highlight { border-bottom: 1px solid #4A4A4A; opacity:0.4; }
.both_btnwrapper { text-align:center; margin-left:-15px; margin-right:-15px; }
.btnsection { margin-bottom: 10px; text-align:center; width: 33%; display:inline-block; border-right:1px solid #fff; }
.btnsection:last-child { border: none; }
.btnsection .btn { min-width: 100px; text-align: center; font-weight:normal; transition: all 0.5s ease-in-out; }
.btnsection .col-sm-6 { padding:0 }
.btnsection .list-title { text-align:center; }
.sect_btn { margin-top:35px; text-align:center; }
.smal_txt { position: relative; z-index: 1; color: #fff; bottom: -200px; }
.sect_btn .btn-details, .sect_btn .btn-details:focus, .dd_enrol .btn_panel .btn-enroll, .dd_enrol .btn_panel .btn-enroll:focus { color:#09c9d7; background:#fff; border-radius:40px; padding:12px 30px; position:relative; text-transform:uppercase; font-size:12px; font-weight:600; }
.sect_btn .btn-details:before, .dd_enrol .btn_panel .btn-enroll:before { content:""; border:1px solid #00cbd6; -moz-border-image: -moz-linear-gradient(left, #47cad9 0%, #379dd6 100%); -webkit-border-image: -webkit-linear-gradient(left, #47cad9 0%, #379dd6 100%); border-image: linear-gradient(to right, #47cad9 0%, #379dd6 100%); border-image:1 40 round; border-radius:40px; position:absolute; left:0; top:0; width:100%; height:100%; }
.sect_btn .btn-details:hover, .dd_enrol .btn_panel .btn-enroll:hover { color:#fff; background-color: #379dd6; background-image: linear-gradient(left, #47cad9 0%, #379dd6 100%); background-image: -moz-linear-gradient(left, #47cad9 0%, #379dd6 100%); background-image: -webkit-linear-gradient(left, #47cad9 0%, #379dd6 100%); background-image: -o-linear-gradient(left, #47cad9 0%, #379dd6 100%); background-image: -webkit-gradient(left, #47cad9 0%, #379dd6 100%); background-size: 100% auto; }
.sect_btn .btn-plan, .contact_section .btn-blue { color: #fff; background-color: #379dd6; background-image: linear-gradient(to right, #379dd6 0%, #47cad9 51%, #379dd6 100%); background-image: -moz-linear-gradient(to right, #379dd6 0%, #47cad9 51%, #379dd6 100%); background-image: -webkit-linear-gradient(to right, #379dd6 0%, #47cad9 51%, #379dd6 100%); background-image: -o-linear-gradient(to right, #379dd6 0%, #47cad9 51%, #379dd6 100%); background-image: -webkit-gradient(to right, #379dd6 0%, #47cad9 51%, #379dd6 100%); background-size: 200% auto; transition: 0.5s; zoom: 1; -webkit-transition: all 0.5s ease; -moz-transition: all 0.5s ease; -o-transition: all 0.5s ease; transition: all 0.5s ease; font-weight: 500; padding: 12px 30px; font-size: 14px; border-radius: 40px; border: none; line-height: normal; }
.sect_btn .btn-plan:hover, .sect_btn .btn-plan:focus, .contact_section .btn-blue:hover, .contact_section .btn-blue:focus { background-color: #47cad9; background-image: linear-gradient(to right, #379dd6 0%, #47cad9 51%, #379dd6 100%); background-image: -moz-linear-gradient(to right, #379dd6 0%, #47cad9 51%, #379dd6 100%); background-image: -webkit-linear-gradient(to right, #379dd6 0%, #47cad9 51%, #379dd6 100%); background-image: -o-linear-gradient(to right, #379dd6 0%, #47cad9 51%, #379dd6 100%); background-image: -webkit-gradient(to right, #379dd6 0%, #47cad9 51%, #379dd6 100%); background-size: 200% auto; transition: 0.5s; background-position: right center; zoom: 1; color: #fff; }
</style>
<?php include('prd_product_js.inc.php'); ?>
<div class="white-box prd_add_wrapper">
  <?php if($manage_product_id){
          if($product_name) { ?>
            <div class="bg_light_primary p-t-5 p-b-5 p-l-10 m-b-5 text-white">
            <?=  $product_name." (".$product_code.")" ?>
            </div>
  <?php } }?>
  <div class="cust_tab_ui">
    <ul class="nav nav-tabs nav-justified nav-noscroll data_tab">
      <li class="active" data-tab="information_tab"> <a data-toggle="tab" href="#information_tab" class="btn_step_heading" data-step="1" data-is_validate="false">
        <div class="column-step ">
          <div class="step-number">1</div>
          <div class="step-title">Information</div>
          <div class="step-info">Fill in product details/settings</div>
        </div>
        </a> </li>
      <li class="<?= ($checkStep >= 2) ? ''  : 'disabled' ?>" data-tab="rules_tab"> <a data-toggle="tab" href="#rules_tab" class="btn_step_heading" data-step="2" data-is_validate="false">
        <div class="column-step">
          <div class="step-number">2</div>
          <div class="step-title">Rules</div>
          <div class="step-info">Set rules for this product</div>
        </div>
        </a> </li>
      <li class="<?= ($checkStep>=3) ? ''  : 'disabled' ?>" data-tab="enrollment_tab"> <a data-toggle="tab" href="#enrollment_tab" class="btn_step_heading" data-step="3" data-is_validate="false">
        <div class="column-step">
          <div class="step-number">3</div>
          <div class="step-title">Application</div>
          <div class="step-info">Fill in details for application </div>
        </div>
        </a> </li>
      <li class="<?= ($checkStep>=4) ? ''  : 'disabled' ?>" data-tab="pricing_tab"> <a data-toggle="tab" href="#pricing_tab" class="btn_step_heading" data-step="4" data-is_validate="false">
        <div class="column-step">
          <div class="step-number">4</div>
          <div class="step-title">Pricing</div>
          <div class="step-info">Set pricing model for product</div>
        </div>
        </a> </li>
    </ul>
  </div>
  <form  method="POST" id="product_management" enctype="multipart/form-data"  autocomplete="off">
    <input type="hidden" name="dataStep" id="dataStep" value="<?= $dataStep ?>">
    <input type="hidden" name="product_id" id="product_id" value="<?= $product_id; ?>">
    <input type="hidden" name="parent_product_id" id="parent_product_id" value="<?= $parent_product_id ?>">
    <input type="hidden" name="manage_product_id" id="manage_product_id" value="<?= $manage_product_id; ?>">
    <input type="hidden" name="is_clone" id="is_clone" value="<?= $is_clone; ?>">
    <input type="hidden" name="submit_type" id="submit_type" value="">
    <input type="hidden" name="matrixID" id="matrixID" value="">
    <input type="hidden" name="keyID" id="keyID" value="">
    <input type="hidden" name="pricingMatrixKey" id="pricingMatrixKey" value='<?= $pricingMatrixKey?>'>
    <input type="hidden" name="productFees" id="productFees" value='<?= $productFees ?>'>
    <input type="hidden" name="record_type" id="record_type" value='<?= $record_type ?>'>
    <input type="hidden" name="allowPricingUpdate" id="allowPricingUpdate" value='<?= $allowPricingUpdate ?>'>
    <!-- For Group Enrollment Product Display Admin Fee -->
    <input type="hidden" name="groupEnrollmentPrd" id="groupEnrollmentPrd" value="<?=$product_type == 'Group Enrollment' ? 'Y' : 'N'?>">
    <div class="tab-content mn">
      <div id="information_tab" class="tab-pane fade in active">
        <?php include ('prd_infomartion.inc.php'); ?>
      </div>
      <div id="rules_tab" class="tab-pane fade">
        <?php include ('prd_rules.inc.php'); ?>
      </div>
      <div id="enrollment_tab" class="tab-pane fade">
        <?php include ('prd_enrollment.inc.php'); ?>
      </div>
      <div id="pricing_tab" class="tab-pane fade ">
        <?php include ('prd_pricing.inc.php'); ?>
      </div>
    </div>
  </form>
</div>
<?php include ('prd_clone_data_file.inc.php'); ?>
