<div class="panel panel-default panel-block">
   <div class="panel-heading">
      <div class="panel-title">
         <h4 class="mn">+ Offering 
         </h4>
      </div>
   </div>


   <div class="panel-body theme-form">
      <div class="row ">
         <div class="col-sm-6">
            <p><strong>Select Class</strong></p>
            <div class="form-group">
               <select class="form-control" name="class_list" id="class_list">
                  <option value=""></option>
                  <?php if(!empty($resClass)) { ?>
                     <?php foreach ($resClass as $key => $value) { ?>
                        <option value="<?= $value['id'] ?>" <?= !empty($class_id) && $class_id == $value['id'] ? 'selected' : '' ?> 
                        data-class_id="<?= md5($value['id']) ?>"
                        data-class_name="<?= $value['class_name'] ?>"
                        data-pay_period="<?= $value['pay_period'] ?>"
                        data-existing_member="<?= $value['existing_member_eligible_coverage'] ?>"
                        data-new_member="<?= $value['new_member_eligible_coverage'] ?>"
                        data-renewed_member="<?= $value['renewed_member_eligible_coverage'] ?>"
                         > <?= $value['class_name'] ?></option>
                     <?php } ?>
                  <?php } ?>
                  
               </select>
               <label>Select Class</label>
               <p class="error" id="error_class_list">
            </div>
         </div>
         <div class="col-sm-6" id="class_list_detail" style="<?= !empty($class_id) ? '' : 'display: none' ?>">
            <div class="clearfix">
               <div class="pull-left">
                  <p><strong><span id="cl_class_name"><?= $class_name ?></span> Class Settings</strong></p>
                  <p>Existing Relationship Eligibility: <span id="cl_existing_member"><?= $cl_existing_member ?></span><br>
                     New Relationship Eligibility: <span id="cl_new_member"><?= $cl_new_member ?></span><br>
                     Renewed Relationship Eligibility: <span id="cl_renewed_member"> <?= $cl_renewed_member ?></span><br>
                     Pay Period: <span id="cl_pay_period"><?= $pay_period ?></span>
                  </p>
               </div>
               <div class="pull-right fs18">
                  <a href="group_add_class.php?class=<?= md5($class_id) ?>" id="cl_link" target="_blank"><i class="fa fa-edit "></i></a>
               </div>
            </div>
         </div>
      </div>
      <div id="offering_form_div" style="<?= !empty($class_id) ? '' : 'display: none' ?>">
         <hr>
         <div class="step_tab_wrap">
            <ul class="nav nav-tabs nav-justified nav-noscroll data_tab">
               <li class="active" data-tab="settings_tab" id="li_settings_tab" >
                  <a data-toggle="tab" href="#settings_tab" data-step="1" class="offering_tabs" id="a_settings_tab">Settings </a> 
               </li>
               <li class="<?= $disabled_tabs ? 'disabled' : '' ?> " data-tab="products_tab" id="li_products_tab" > 
                  <a data-toggle="tab" href="#products_tab" data-step="2" class="offering_tabs" id="a_products_tab">
                  Products
                  </a> 
               </li>
               <li class="<?= $disabled_tabs ? 'disabled' : '' ?>" data-tab="contributions_tab" id="li_contributions_tab">
                  <a data-toggle="tab" href="#contributions_tab" data-step="3" class="offering_tabs" id="a_contributions_tab">
                  Contributions
                  </a> 
               </li>
            </ul>
            <div class="subbar-section">
               <div class="tab-subbar">
                  <p class="text-right text-gray mn"><span id="step_counter">1</span> of 3 Steps </p>
               </div>
            </div>
         </div>

      
         <form action="<?=$GROUP_HOST?>/ajax_offering_coverage_periods.php" method="post" name="offering_form" id="offering_form" autocomplete="false" enctype="multipart/form-data" novalidate>
            <input type="hidden" name="group_coverage_period_id" id="group_coverage_period_id" value="<?= $group_coverage_period_id ?>">
            <input type="hidden" name="group_id" id="group_id" value="<?= $group_id ?>">
            <input type="hidden" name="tmp_offering_id" id="tmp_offering_id" value="<?= $tmp_offering_id ?>">
            <input type="hidden" name="offering_id" id="offering_id" value="<?= $offering_id ?>">
            <input type="hidden" name="class_id" id="class_id" value="<?= $class_id ?>">
            <input type="hidden" name="dataStep" id="dataStep" value="0">
            <input type="hidden" name="action" id="action" value="">
            <input type="hidden" name="submit_type" id="submit_type" value="">
            <input type="hidden" name="next_back_product" id="next_back_product" value="">

            <div class="tab-content">
               <!-- Stap 1 Start -->
               <div id="settings_tab" class="tab-pane fade active in">
                  <?php include ('offering_settings_tab.inc.php'); ?>
               </div>
               <div id="products_tab" class="tab-pane fade  in">
                  <?php include ('offering_products_tab.inc.php'); ?>
               </div>
               <div id="contributions_tab" class="tab-pane fade  in">
                  <?php include ('offering_contributions_tab.inc.php'); ?>
               </div>
            </div>
         </form>
      </div>
   </div>


   <div class="panel-footer text-center" id="offering_form_foorter_div" style="<?= !empty($class_id) ? 'display: none' : '' ?>">
      <a href="javascript:void(0);" class="btn btn-action">Next</a>
      <a href="javascript:void(0)" class=" btn red-link cancel_tab_button">Cancel</a>
   </div>
</div>
<?php  //jquery code file; 
include_once __DIR__ . '/offering_coverage_periods_jquery.inc.php'; 
?>
