<form id="triggerForm" name="triggerForm" action="ajax_manage_trigger.php" enctype="multipart/form-data" autocomplete="off">
      <input type="hidden" name="test_trigger" id="testTrigger">
      <input type="hidden" name="test_email" id="testEmailInput">
      <input type="hidden" name="test_sms" id="testSmsInput">
      <input type="hidden" name="triggerId" value="<?=$triggerId?>">
      <input type="hidden" name="action" value="<?=$action?>">
      <input type="hidden" name="upload_type" id="upload_type" value="">
      <input type="hidden" name="email_attachment_id[]" id="email_attachment_id" value="<?= $email_attachment_id ?>">

  <!-- + Trigger section code start -->
  <div class="panel panel-default panel-block advance_info_div">
    <div class="panel-body">
      <div class="phone-control-wrap m-b-30 ">
    <div class="phone-addon w-90">
      <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="75px">
    </div>
    <div class="phone-addon text-left">
        <div class="info_box info_box_max_width br-n" style="background-color: inherit;">
          <p class="fs14 mn">Triggers are automatic communications that are sent from the system when a certain action occurs. Follow the data fields below to create a trigger.</p>
    </div>
  </div>
  </div>
      <h4 class="m-t-n m-b-20">+ Trigger</h4>
      <div class="row theme-form roboto_font">

        <div class="col-sm-4">
          <div class="form-group">
            <input type="text" name="title" id="title" class="form-control" value="<?=$title?>">
            <label>Name Trigger</label>
            <span class="error" id="error_title"></span>
          </div>
        </div>

        <div class="col-sm-4">
          <div class="form-group">
            <select class="form-control" name="company_id" id="companyId">
              <option></option>
              <?php 
                if(!empty($companyRes)){
                  foreach ($companyRes as $company) {
                ?>
                  <option value="<?=$company['id']?>" <?=($company_id==$company['id']) ? 'selected="selected"' : ''?>><?=$company['company_name']?></option>
                <?php
                  }
                }
              ?>
            </select>
            <label>Company</label>
            <span class="error" id="error_company_id"></span>
          </div>
        </div>

        <div class="col-sm-4">
          <div class="form-group">
            <select class="form-control" id="typeSel" name="type">
              <option></option>
              <option value="Email" <?=$type=='Email' ? 'selected="selected"' : ''?>>Email</option>
              <option value="SMS" <?=$type=='SMS' ? 'selected="selected"' : ''?>>Text Message</option>
              <option value="Both" <?=$type=='Both' ? 'selected="selected"' : ''?>> Email & Text Message</option>
            </select>
            <label>Type</label>
            <span class="error" id="error_type"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- + Trigger section code ends -->

  <!-- Target Audience section code start -->
  <div class="panel panel-default panel-block">
    <div class="panel-body">
      <h4 class="m-t-n m-b-15">Target Audience</h4>
      <p class="m-b-20">Select the user group you wish to receive this communication.</p>
      <div class="row theme-form">
        <div class="col-md-4 col-sm-6">
          <div class="form-group">
            <select class="form-control" name="user_group" id="userGroupSel">
              <option></option>
              <option value="agent" <?=($user_group=='agent' ? 'selected="selected"' : '')?>>Agents</option>
              <option value="group" <?=($user_group=='group' ? 'selected="selected"' : '')?>>Groups</option>
              <option value="member" <?=($user_group=='member' ? 'selected="selected"' : '')?>>Members</option>
              <option value="other" <?=($user_group=='other' ? 'selected="selected"' : '')?>>Other</option>
            </select>
            <label>User Group</label>
              <span class="error" id="error_user_group"></span>
          </div>
        </div>
      </div>

      <div class="triggerActionDiv" style="display:<?=!empty($trigger_action) ? 'Block' : 'none';?>">
        <h4 class="m-t-n m-b-15">Trigger Action</h4>
        <p class="m-b-20">Select the action to initiate the trigger to send.</p>
        <div class="row theme-form">
          <div class="col-md-4 col-sm-6">
            <div class="form-group">
              <select class="form-control" name="trigger_action" id="triggerActionSel">
                <option></option>         
              </select>
              <label>Trigger Action</label>
                <span class="error" id="error_trigger_action"></span>
            </div>
          </div>

          <div class="col-md-4 col-sm-6" id="specificallyDiv" style="display: none;">
            <div class="form-group">
              <select class="form-control" name="specifically" id="specificallySel">
                <option></option>
                <option value="added_date" class="specEnrollment" style="display: none;">Added Date</option>
                <option value="effective_date" class="specEnrollment" style="display: none;">Effective Date</option>
                <option value="date_terminated" class="specCancellation" style="display: none;">Date Terminated</option>
                <option value="termination_date" class="specCancellation" style="display: none;">Termination Date</option>
              </select>
              <label>Specifically</label>
              <span class="error" id="error_specifically"></span>
            </div>
          </div>
          <div class="col-md-4 col-sm-6" id="productsDiv" style="display: none;">
            <div class="form-group">
                <select class="se_multiple_select" name="products[]" multiple="multiple" id="productsSel">
                  <?php 
                    if(!empty($companyArr)) {
                      foreach ($companyArr as $key => $company) { 
                  ?>
                      <optgroup label="<?=$key?>">
                        <?php 
                          foreach ($company as $pkey => $row) { 
                        ?>
                          <option value="<?= $row['id'] ?>" <?= in_array($row['id'], $triProductsArr) ? 'selected="selected"' : '' ?>><?= $row['name'] . ' (' . $row['product_code'] . ') ' ?></option>
                        <?php 
                          } 
                        ?>
                      </optgroup>
                  <?php 
                      } 
                    } 
                  ?>
                </select>
                <label>Product(s)</label>
            </div>
          </div>
          <div class="col-md-4 col-sm-6" id="daysPriorDiv" style="display: none;">
            <div class="form-group">
              <select class="form-control" name="days_prior" id="daysPriorSel">
                <option value=""></option>
                <?php for($i=1;$i<=15;$i++){ ?> 
                  <option value="<?=$i?>" <?= !empty($days_prior) && $days_prior == $i ? 'selected="selected"' : '' ?>><?=$i?></option>
                <?php } ?>
              </select>
              <label>Day(s) Prior</label>
              <span class="error" id="error_days_prior"></span>
            </div>
          </div>
          <div class="col-md-4 col-sm-6" id="effectiveDateDiv" style="display: none;">
            <div class="form-group">
              <input type="text" name="effective_date" id="effective_date" class="form-control date_picker" value="<?= checkIsset($effective_date) ?>">
              <label>Effective Date</label>
              <span class="error" id="error_effective_date"></span>
            </div>
          </div>
        </div>
      </div>

      <div class="triggerActionDiv" id="triggerDelayDiv">
        <h4 class="m-t-n m-b-15">Delay Trigger</h4>
        <p class="m-b-20">Select the action to delay the trigger to send.</p>
        <div class="row theme-form">
          <div class="col-md-4">
            <div class="form-group">
              <select class="form-control" name="trigger_delay_type" id="trigger_delay_type">
                <option></option>         
                <option value="None" <?= !empty($trigger_delay_type) && $trigger_delay_type == 'None' ? 'selected="selected"' : '' ?>>None</option>         
                <option value="Relative" <?= !empty($trigger_delay_type) && $trigger_delay_type == 'Relative' ? 'selected="selected"' : '' ?>>Relative</option>         
                <option value="Exact Date" <?= !empty($trigger_delay_type) && $trigger_delay_type == 'Exact Date' ? 'selected="selected"' : '' ?>>Exact Date</option>       
              </select>
              <label>Delay Type</label>
                <span class="error" id="error_trigger_delay_type"></span>
            </div>
          </div>

          <div class="col-md-6" id="delayForDiv" style="display: <?=!empty($trigger_delay_type) && $trigger_delay_type =='Relative' ? 'Block' : 'none';?>">
            <div class="col-md-2">
              <div class="form-group">
                  <label>Delay For</label>
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-group">
                  <select class="form-control" name="numbers_to_delay" id="numbers_to_delay">
                    <option value=""></option>
                    <?php for($i=1;$i<=15;$i++){ ?> 
                      <option value="<?=$i?>" <?= !empty($numbers_to_delay) && $numbers_to_delay == $i ? 'selected="selected"' : '' ?>><?=$i?></option>
                    <?php } ?>
                  </select>
                  <label></label>
                  <span class="error" id="error_numbers_to_delay"></span>
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-group">
                  <select class="form-control" name="time_units" id="time_units">
                    <option value=""></option>
                    <option value="Hours" <?= !empty($time_units) && $time_units == 'Hours' ? 'selected="selected"' : '' ?>>Hours</option>
                    <option value="Days" <?= !empty($time_units) && $time_units == 'Days' ? 'selected="selected"' : '' ?>>Days</option>
                    <option value="Weeks" <?= !empty($time_units) && $time_units == 'Weeks' ? 'selected="selected"' : '' ?>>Weeks</option>
                    <option value="Months" <?= !empty($time_units) && $time_units == 'Months' ? 'selected="selected"' : '' ?>>Months</option>
                  </select>
                  <label></label>
                  <span class="error" id="error_time_units"></span>
              </div>
            </div>
          </div>

          <div class="col-md-6" id="delayUntilDiv" style="display: <?=!empty($trigger_delay_type) && $trigger_delay_type =='Exact Date' ? 'Block' : 'none';?>">
            <div class="col-md-3">
              <div class="form-group">
                  <label>Delay Until</label>
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-group">
                <input type="text" name="delay_until_date" id="delay_until_date" class="form-control delay_until_date" value="<?= checkIsset($delay_until_date) ?>">
                <label>Date</label>
                <span class="error" id="error_delay_until_date"></span>
              </div>
            </div>
          </div>
          
          
        </div>
      </div>


    </div>
  </div>
  <!-- Target Audience section code ends -->

  <!-- Email Recipient section code start -->
  <div class="panel panel-default panel-block theme-form" id="emailRecipientDiv" style="display: none;">
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-4">
           <h4 class="m-t-n m-b-30 p-b-2">Email Recipient(s)</h4>
           <div class="form-group ">
            <input type="text" name="from_email" id="fromEmail" class="form-control no_space" value="<?=$from_email?>">
            <label>From Email</label>
             <span class="error" id="error_from_email"></span>
           </div>
           <div class="form-group ">
            <input type="text" name="from_name" id="fromName" class="form-control" value="<?=$from_name?>">
            <label>From Name</label>
           </div>

           <h4 class="m-t-5 m-b-20">Specifics</h4>
           <div class="form-group ">
            <input type="text" name="to_email_specific" id="toEmailSpecific" class="form-control no_space" value="<?=$to_email_specific?>">
            <label>To: Email</label>
            <span class="error" id="error_to_email_specific"></span>
           </div>
           <div class="form-group">
            <input type="text" name="cc_email_specific" id="ccEmailSpecific" class="form-control no_space" value="<?=$cc_email_specific?>">
            <label>CC: To Email</label>
            <span class="error" id="error_cc_email_specific"></span>
           </div>
           <div class="form-group ">
            <input type="text" name="bcc_email_specific" id="bccEmailSpecific" class="form-control no_space" value="<?=$bcc_email_specific?>">
            <label>Bcc: To Email</label>
            <span class="error" id="error_bcc_email_specific"></span>
           </div>

           <h4 class="m-t-5 m-b-20">Users</h4>
            <div class="form-group">
              <select class="form-control" name="to_email_user" id="toEmailUserSel">
                <option></option>
              </select>
              <label>To: Email</label>
              <span class="error" id="error_to_email_user"></span>
            </div>
            <div class="form-group">
              <select class="form-control" name="cc_email_user" id="ccEmailUserSel">
                <option></option>
              </select>
              <label>CC: To Email</label>
            </div>
            <div class="form-group">
            <select class="form-control" name="bcc_email_user" id="bccEmailUserSel">
              <option></option>
            </select>
            <label>Bcc: To Email</label>
            </div>
        </div>

        <div class="col-sm-8">
          <div class="clearfix">
            <div class="pull-left m-b-20"><h4 class="mn">Content</h4></div>
            <div class="pull-right m-b-20">
              <a href="javascript:void(0);" class="previewBtn btn btn-info btn-outline">Preview</a>
              <a href="javascript:void(0);" class="btn btn-info" id="sendEmailBtn">Send Test</a>
            </div>
          </div>
          <div class="form-group">
              <select name="email_template" class="form-control" id="emailTemplate">
                <option value=""></option>
                <?php  if(!empty($templateRes)) { ?>
                  <?php foreach ($templateRes as $key => $value) { ?>
                    <option value="<?=$value['id']?>" <?=$template_id==$value['id']? 'selected="selected"' : ''?>><?=$value['title']?></option>
                  <?php } ?>
                <?php } ?>
              </select>
              <label>Email Template</label>
              <span class="error" id="error_email_template"></span>
          </div>
          <div class="form-group">
            <input type="text" name="email_subject" id="emailSubject" class="form-control" value="<?=$email_subject?>">
            <label>Subject</label>
            <span class="error" id="error_email_subject"></span>
          </div>
          <textarea class="summernote" name="email_content" id="emailContent"><?=$email_content?></textarea>
          <span class="error" id="error_email_content"></span>
          <div class="clearfix m-t-20">
            <div id="file_div_inner" >
                                  <a href="javascript:void(0);" class="red-link fw500 pull-right m-t-0 m-b-15" id="add_attachment">
                                    + Add attachment
                                  </a>
                                  <input id="attachment" name="attachment[]" type="file" style="display: none;" />
                  <?php if(!empty($attachmentRow)){ ?>
                      <?php foreach ($attachmentRow as $key => $row) { ?>
                          <?php
                          $imageExt=array_reverse(explode(".", $row['file_name']));
                          $is_image=false;
                          if(strtolower($imageExt[0])=="jpg" || strtolower($imageExt[0])=="jpeg" || strtolower($imageExt[0])=="png" || strtolower($imageExt[0])=="gif" || strtolower($imageExt[0])=="tif"){ $is_image=true; }
                          ?>
                          <div class="clearfix"></div>
                          <div id="attachment_file_div_<?= $row['id'] ?>" class="m-b-15">
                              <div class="phone-control-wrap">
                                  <div class="phone-addon">
                                      <input type="text" name="" placeholder="<?= $row['file_name'] ?>" class="form-control" readonly="readonly">
                                  </div>
                                  <div class="phone-addon w-30">
                                      <a href="<?= $ATTACHMENS_WEB.$row['file_name'] ?>" download class="text-info fa-lg"> <i class="fa fa-download"></i></a>
                                    </div>
                                    <div class="phone-addon w-30">
                                      <a href="javascript:void(0)" data-id="<?= $row['id'] ?>" class="text-action fa-lg delete_attachment"><i class="fa fa-trash"></i></a>
                                  </div>
                              </div>
                          </div>
                      <?php } ?>
                  <?php } ?>
            </div>
          </div>
            <div id="attachements_inner_div"></div>
        <!--     <div class="table-responsive email_triger_shadow">
              <table class="table table-borderless mn text-center">
                <tbody>
                  <tr>
                    <td>[[fname]]</td>
                    <td>[[lname]]</td>
                    <td>[[Email]]</td>
                    <td>[[Phone]]</td>
                    <td>[[Agent]]</td>
                    <td>[[ParentAgent]]</td>
                    <td>[[MemberID]]</td>
                    <td>[[ActiveProducts]]</td>
                  </tr>
                </tbody>
              </table>
            </div> -->
            <div class="m-t-5 text-right">
              <a data-href="smart_tag_popup.php" class="btn btn-info btn-outline smart_tag_popup">Available Smart Tags <i class="fa fa-info-circle" aria-hidden="true"></i></a>
            </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Email Recipient section code ends -->

  <!-- SMS Recipient section code start -->
  <div class="panel panel-default panel-block theme-form" id="smsRecipientDiv" style="display: none;">
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-4">
           <h4 class="m-t-n m-b-30 p-b-2">Text Message (SMS) Recipient(s)</h4>
           <div class="form-group ">
            <input type="text" name="to_phone_specific" id="toPhoneSpecific" class="form-control" value="<?=$to_phone_specific?>">
            <label>To Phone (Specific)</label>
            <span class="error" id="error_to_phone_specific"></span>
           </div>
           
           <div class="form-group">
            <select class="form-control" name="to_phone_user" id="toPhoneUserSel">
              <option></option>
            </select>
            <label>To Phone (Users)</label>
            <span class="error" id="error_to_phone_user"></span>
           </div>
        </div>
        <div class="col-sm-8">
          <div class="clearfix m-b-20">
            <div class="pull-left"><h4 class="mn">Content</h4></div>
            <div class="pull-right">
              <a href="javascript:void(0);" class="previewBtn btn btn-info btn-outline">Preview</a>
              <a href="javascript:void(0);" class="btn btn-info" id="sendSmsBtn">Send Test</a>
            </div>
          </div>
          <textarea rows="3" name="sms_content" id="smsContent" class="email_triger_shadow form-control bg_white"><?=$sms_content?></textarea>
          <span class="error" id="error_sms_content"></span>
         <!-- <h4 class="m-t-15 m-b-15">Available Tags:</h4>
         <div class="table-responsive email_triger_shadow">
            <table class="table table-borderless mn text-center">
              <tbody>
                <tr>
                  <td>[[fname]]</td>
                  <td>[[lname]]</td>
                  <td>[[Email]]</td>
                  <td>[[Phone]]</td>
                  <td>[[Agent]]</td>
                  <td>[[Parent Agent]]</td>
                  <td>[[MemberID]]</td>
                  <td>[[ActiveProducts]]</td>
                </tr>
              </tbody>
            </table>
          </div> -->
          <div class="m-t-25 clearfix">
              <div class="pull-left">
                <p class="text-light-gray">Messages over 160 characters will send in multiple SMS messages.</p>
              </div>
              <div class="pull-right">
              <a data-href="smart_tag_popup.php" class="btn btn-info btn-outline smart_tag_popup">Available Smart Tags <i class="fa fa-info-circle" aria-hidden="true"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- SMS Recipient section code ends -->
</form>
 <div class="panel panel-default panel-block">
   <div class="panel-body text-center">
      <a href="javascript:void(0);" class="btn btn-action" id="saveTriggerBtn">Save</a>
      <a href="triggers.php" class="btn red-link">Cancel</a>
    </div>
  </div>

<!-- Trigger Preview Code Start -->
<div style="display: none;">
  <div class="panel panel-default panel-block panel-shadowless mn" id="triggerPreviewPopup">
    <div class="panel-heading br-n">
      <div class="panel-title">
        <h4 class="fs18 mn">Preview -  <span class="fw300">Trigger</span></h4>
      </div>
    </div>
    <div class="panel-body p-t-0">
       <ul class="nav nav-tabs tabs customtab nav-noscroll" role="tablist">
          <li role="presentation" class="active"><a href="#emailPreviewContent" aria-controls="email_content" role="tab" data-toggle="tab">Email Content</a></li>
          <li role="presentation"><a href="#smsPreviewContent" aria-controls="smsContentTab" role="tab" data-toggle="tab">SMS Content</a></li>
        </ul>
      <div class="bg_light_bg p-15">
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="emailPreviewContent">
          </div>
          <div role="tabpanel" class="tab-pane" id="smsPreviewContent">
          </div>
        </div>
      </div>
      <div class="clearfix  text-center m-t-30">
        <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Close</a>
      </div>
    </div>
  </div>
</div>
<!-- Trigger Preview Code Ends -->

<!-- Send Email popup Code Start -->
<div style="display: none;">
  <div class="panel panel-default panel-block panel-shadowless mn" id="sendEmailPopup">
      <div class="panel-body">
        <p class="fs16">What Email address would you like to send this test to?</p>
        <div class="theme-form">
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <input type="text" class="form-control no_space" name="sendTestEmail" id="sendTestEmail">
                <label>Email</label>
                <span class="error" id="error_test_email"></span>
              </div>
            </div>
          </div>
        </div>
        <div class="clearfix  text-center">
          <a href="javascript:void(0);" class="btn btn-action" id="sendTestEmailBtn">Send Test</a> 
          <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Cancel</a>
        </div>
      </div>
  </div>
</div>
<!-- Send Email popup Code Start -->

<!-- Send SMS popup Code Start -->
<div style="display: none;">
  <div class="panel panel-default panel-block panel-shadowless mn" id="sendSmsPopup">
      <div class="panel-body">
        <p class="fs16">What Phone would you like to send this test to?</p>
        <div class="theme-form">
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <input type="text" name="sendTestPhone" id="sendTestPhone" class="form-control">
                <label>Phone</label>
                  <span class="error" id="error_test_sms"></span>
              </div>
            </div>
          </div>
        </div>
        <div class="clearfix  text-center">
          <a href="javascript:void(0);" class="btn btn-action" id="sendTestSmsBtn">Send Test</a> 
          <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Cancel</a>
        </div>
      </div>
  </div>
</div>
<!-- Send SMS popup Code Start -->
<div  id="attachements_dynamic_div" style="display: none">
    <div class="m-b-20" id="attachment_file_div_~file_id~">
        <div class="phone-control-wrap">
            <div class="phone-addon">
                <input type="text" name="" placeholder="~file_name~" class="form-control" readonly="readonly">
            </div>
            <div class="phone-addon w-30">
                <a href="<?= $ATTACHMENS_WEB ?>~file_name~" download class="text-info fa-lg"> <i class="fa fa-download"></i></a>
              </div>
              <div class="phone-addon w-30">
                <a href="javascript:void(0)" data-id="~file_id~" class="text-action fa-lg  delete_attachment"><i class="fa fa-trash"></i></a>
            </div>
        </div>
    </div>
<script type="text/javascript">
$(document).ready(function() {
  $(".date_picker").datepicker({
    changeDay: true,
    changeMonth: true,
    changeYear: true
  });

  $(".delay_until_date").datepicker({
    changeDay: true,
    changeMonth: true,
    changeYear: true,
    "startDate": new Date()
  });

  checkEmail();
  initCKEditor("emailContent",false,"360px");
  // initialization of plugins 
  var not_win = '';
      $(".smart_tag_popup").on('click',function(){
      $href = $(this).attr('data-href');
      var not_win = window.open($href, "myWindow", "width=768,height=600");
      if(not_win.closed) {  
        alert('closed');  
      } 
    });
    $("#fromPhone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
    $("#toPhoneSpecific").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
    $("#productsSel").multipleSelect({
      width:"100%",
      filter:true
    });

    $(function(){
      $("#add_attachment").on('click', function(e){
          e.preventDefault();
          $("#attachment:hidden").trigger('click');
      });
    });

    $(document).off('change', '#attachment');
    $(document).on("change", "#attachment", function(e) {
        e.preventDefault();
        var filename = $('#attachment').val();
        if (filename != '') {
            $("#upload_type").val("file");
            $("#triggerForm").submit();
        } else {
            $("#upload_type").val("");
        }
    });

    $(document).off('click', '.delete_attachment');
    $(document).on("click", ".delete_attachment", function(e) {
        e.stopPropagation();
        var id = $(this).attr('data-id');
        swal({
            text: 'Delete Attachment: Are you sure?',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel',
        }).then(function() {
            $("#ajax_loader").show();
            $.ajax({
                url: '<?= $ADMIN_HOST ?>/ajax_delete_email_attachment.php',
                dataType: 'JSON',
                type: 'POST',
                data: {id: id,type:"trigger"},
                success: function(res) {
                    $("#ajax_loader").hide();
                    if (res.status == "success") {
                        $("#attachment_file_div_" + id).remove();
                        setNotifySuccess(res.message);
                    } else {
                        setNotifyError(res.message);
                    }
                }
            });
        }, function(dismiss) {

        });
    });
  // Edit & Clone Triggers
    var user_group = $("#userGroupSel option:selected").val();
    var triggerActionVal = "<?=$trigger_action?>";
    var specificallyVal = "<?=$specifically?>";
    var type = $("#typeSel option:selected").val();
    var toEmailUserVal = "<?=$to_email_user?>";
    var ccEmailUserVal = "<?=$cc_email_user?>";
    var bccEmailUserVal = "<?=$bcc_email_user?>";
    var toPhoneUserVal = "<?=$to_phone_user?>";
    emailSmsDiv(type);

    if(user_group != '' && user_group != 'other'){
      $(".triggerActionDiv").show();
      $("#triggerDelayDiv").show();
      selected_user_group(user_group);
      $("#triggerActionSel").val(triggerActionVal);
      $('#triggerActionSel').selectpicker('refresh');

      $("#toEmailUserSel").val(toEmailUserVal);
      $('#toEmailUserSel').selectpicker('refresh');
      $("#ccEmailUserSel").val(ccEmailUserVal);
      $('#ccEmailUserSel').selectpicker('refresh');
      $("#bccEmailUserSel").val(bccEmailUserVal);
      $('#bccEmailUserSel').selectpicker('refresh');

      $("#toPhoneUserSel").val(toPhoneUserVal);
      $('#toPhoneUserSel').selectpicker('refresh');
         
      $('#specificallySel').val('');
      if(triggerActionVal == "member_enrollment"){
        $("#specificallyDiv").show();
        $("#productsDiv").show();
        $(".specEnrollment").show();
        $(".specCancellation").hide();
        $("#daysPriorDiv").hide();
        $("#effectiveDateDiv").hide();
      }else if(triggerActionVal == "member_cancellation"){
        $("#specificallyDiv").show();
        $("#productsDiv").show();
        $(".specEnrollment").hide();
        $(".specCancellation").show();
        $("#daysPriorDiv").hide();
        $("#effectiveDateDiv").hide();
      }else if(triggerActionVal == "renewal_payment"){
        $("#daysPriorDiv").show();
        $("#effectiveDateDiv").show();
        $("#specificallyDiv").hide();
        $("#productsDiv").hide();
        $(".specEnrollment").hide();
        $(".specCancellation").hide();
        $("#triggerDelayDiv").hide();
      }else{
        $(".specEnrollment").hide();
        $(".specCancellation").hide();
        $("#specificallyDiv").hide();
        $("#productsDiv").hide();
        $("#daysPriorDiv").hide();
        $("#effectiveDateDiv").hide();
      }
       $("#specificallySel").val(specificallyVal);
      $('#specificallySel').selectpicker('refresh');
    }

  // Trigger Preview Code Start
    $(document).off("click",".previewBtn");
    $(document).on("click",".previewBtn", function(){
      var emailContent = CKEDITOR.instances.emailContent.getData();
      var smsContent = $("#smsContent").val();
      $("#emailPreviewContent").html(emailContent);
      $("#smsPreviewContent").html(smsContent);
      $.colorbox({
        inline:true,
        href:"#triggerPreviewPopup",
        height:"590px",
        width:"685px",
      });
    });

  // Email & SMS block hide/show code
    $(document).on("change","#typeSel",function(e){
      var type = $("#typeSel option:selected").val();
      emailSmsDiv(type);
    });

  // User Group Trigger Action Code
    $(document).on("change","#userGroupSel",function(e){
      var user_group = $("#userGroupSel option:selected").val();
      $(".triggerActionDiv").hide();
      $("#triggerDelayDiv").hide();
      if(user_group != '' && user_group != 'other'){
        $(".triggerActionDiv").show();
        $("#triggerDelayDiv").show();
        selected_user_group(user_group);
      }else{
        $("#toEmailUserSel").html("<option></option>");  
        $('#toEmailUserSel').selectpicker('refresh');
        $("#ccEmailUserSel").html("<option></option>");  
        $('#ccEmailUserSel').selectpicker('refresh');
        $("#bccEmailUserSel").html("<option></option>");  
        $('#bccEmailUserSel').selectpicker('refresh');
        $("#toPhoneUserSel").html("<option></option>");  
        $('#toPhoneUserSel').selectpicker('refresh');
      }
    });

    $(document).on("change","#trigger_delay_type",function(e){
      var trigger_delay_type = $("#trigger_delay_type option:selected").val();
      if(trigger_delay_type == 'Relative'){
        $("#delayForDiv").show();
        $("#delayUntilDiv").hide();
      }else if(trigger_delay_type == 'Exact Date'){
        $("#delayUntilDiv").show();
        $("#delayForDiv").hide();
      }else{
        $("#delayForDiv").hide();
        $("#delayUntilDiv").hide();
      }
    });

  // Trigger action code start
    $(document).on("change","#triggerActionSel",function(e){
      var trigger_action_val = $("#triggerActionSel option:selected").val();
       $('#specificallySel').val('');
       $("#triggerDelayDiv").show();
      if(trigger_action_val == "member_enrollment"){
        $("#specificallyDiv").show();
        $("#productsDiv").show();
        $(".specEnrollment").show();
        $(".specCancellation").hide();
        $("#daysPriorDiv").hide();
        $("#effectiveDateDiv").hide();
      }else if(trigger_action_val == "member_cancellation"){
        $("#specificallyDiv").show();
        $("#productsDiv").show();
        $(".specEnrollment").hide();
        $(".specCancellation").show();
        $("#daysPriorDiv").hide();
        $("#effectiveDateDiv").hide();
      }else if(trigger_action_val == "renewal_payment"){
        $("#daysPriorDiv").show();
        $("#effectiveDateDiv").show();
        $("#specificallyDiv").hide();
        $("#productsDiv").hide();
        $(".specEnrollment").hide();
        $(".specCancellation").hide();
        $("#triggerDelayDiv").hide();
      }else{
        $(".specEnrollment").hide();
        $(".specCancellation").hide();
        $("#specificallyDiv").hide();
        $("#productsDiv").hide();
        $("#daysPriorDiv").hide();
        $("#effectiveDateDiv").hide();
      }
      $('#specificallySel').selectpicker('refresh');
    });  

  // Save trigger Code Start
    $(document).off("click","#saveTriggerBtn");
    $(document).on("click","#saveTriggerBtn",function(e){
      $("#upload_type").val("form");
      $("#emailContent").val(CKEDITOR.instances.emailContent.getData());
      $("#triggerForm").submit();
    });

  // Test Email & SMS code start
    $('#sendEmailBtn').colorbox({
      inline:true,
      href:"#sendEmailPopup",
      height:"215px",
      width:"450px",
    });
    $('#sendSmsBtn').colorbox({
      inline:true,
      href:"#sendSmsPopup",
      height:"215px",
      width:"450px",
    });
    $("#sendTestPhone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
    $('#sendTestSMS').colorbox({
      inline:true,
      href:"#sendSmsPopup",
      height:"215px",
      width:"400px",
    });

    // sendTestEmail Code Start
      $(document).off("click","#sendTestEmailBtn");
      $(document).on("click","#sendTestEmailBtn", function(){
        $("#testEmailInput").val($("#sendTestEmail").val());
        $("#testTrigger").val("testEmail");
        $("#upload_type").val("form");
        $("#emailContent").val(CKEDITOR.instances.emailContent.getData());
        $("#triggerForm").submit();
        $("#testTrigger").val('');
      });

    // sendTestSms Code Start
      $(document).off("click","#sendTestSmsBtn");
      $(document).on("click","#sendTestSmsBtn", function(){
        $("#testSmsInput").val($("#sendTestPhone").val());
        $("#testTrigger").val("testSms");
        $("#upload_type").val("form");
        $("#triggerForm").submit();
        $("#testTrigger").val('');
      });
    
});

selected_user_group  = function(user_group){
  if(user_group == 'agent'){
    // Trigger Action Dropdown value code
    var trgActionOpt = "<option></option><option value='agent_onboarding'>Agent Onboarding</option><option value='member_enrollment'>Member Application</option><option value='member_cancellation'>Member Cancellation</option>";
    $("#triggerActionSel").html(trgActionOpt);

    // Email Users Dropdown code start
    var userOpt = "<option></option><option value='agent'>Agent</option><option value='parent_agent'>Parent Agent</option><option value='highest_upline_agent'>Highest Upline Agent</option>";
    $("#toEmailUserSel").html(userOpt);
    $("#ccEmailUserSel").html(userOpt);
    $("#bccEmailUserSel").html(userOpt);
    $("#toPhoneUserSel").html(userOpt);
  }else if(user_group == 'group'){
    // Trigger Action Dropdown value code
    var trgActionOpt = "<option></option><option value='group_onboarding'>Group Onboarding</option><option value='member_enrollment'>Member Application</option><option value='member_cancellation'>Member Cancellation</option>";
    $("#triggerActionSel").html(trgActionOpt);

     // Email Users Dropdown code start
    var userOpt = "<option></option><option value='group'>Group</option><option value='billing_contact'>Group Contact</option><option value='parent_agent'>Parent Agent</option><option value='highest_upline_agent'>Highest Upline Agent</option>";
    $("#toEmailUserSel").html(userOpt);
    $("#ccEmailUserSel").html(userOpt);
    $("#bccEmailUserSel").html(userOpt);
    $("#toPhoneUserSel").html(userOpt);
  }else if(user_group == 'member'){
    var trgActionOpt = "<option></option><option value='member_enrollment'>Member Application</option><option value='member_cancellation'>Member Cancellation</option><option value='renewal_payment'>Renewal Payment</option>";
    $("#triggerActionSel").html(trgActionOpt);

     // Email Users Dropdown code start
    var userOpt = "<option></option><option value='member'>Member</option><option value='mbr_enrolle'>Enrolling Agent/Group</option><option value='parent_agent'>Parent Agent</option><option value='highest_upline_agent'>Highest Upline Agent</option>";
    $("#toEmailUserSel").html(userOpt);
    $("#ccEmailUserSel").html(userOpt);
    $("#bccEmailUserSel").html(userOpt);
    $("#toPhoneUserSel").html(userOpt);
  }
  
  $('#triggerActionSel').selectpicker('refresh');
  $('#toEmailUserSel').selectpicker('refresh');
  $('#ccEmailUserSel').selectpicker('refresh');
  $('#bccEmailUserSel').selectpicker('refresh');
  $('#toPhoneUserSel').selectpicker('refresh');
  common_select();
}

$('#triggerForm').ajaxForm({
      beforeSubmit: function(arr, $form, options) {
          $("#ajax_loader").show();
          $("#emailContent").val(CKEDITOR.instances.emailContent.getData());
      },
      url: $('#triggerForm').attr('action'),
      type: 'POST',
      dataType : 'json',
      success: function(res) {
        $(".error").html('');
        $('#ajax_loader').hide();
        if (res.status == 'success') {
          setNotifySuccess(res.msg);
          if(res.actionType == 'savedTrigger'){
            setTimeout(function(){ 
              window.location.href = 'triggers.php';
            }, 1000);  
          }else{
            parent.$.colorbox.close(); 
          }
        }else if (res.status == "success_file") {
          $("#email_attachment_id").val(res.attachment);
          if (res.files_info.length > 0) {
              $.each(res.files_info, function($k, $v) {
                  $html_append = $('#attachements_dynamic_div').html();
                  $html_append = $html_append.replace(/~file_name~/g, $v.file_name);
                  $html_append = $html_append.replace(/~file_display_name~/g, $v.file_display_name);
                  $html_append = $html_append.replace(/~file_id~/g, $v.file_id);
                  $('#attachements_inner_div').append($html_append);
              });
          }
          // $("#file_div_inner").html($file_div_inner);
          setNotifySuccess(res.message);
        } else if (res.status == 'fail') {
          if(typeof res.msg !== 'undefined'){
           setNotifyError(res.msg);
          }
           var is_error = true;
          $.each(res.errors, function (index, error) {
            $('#error_' + index).html(error).show();
            if(is_error){
                var offset = $('#error_' + index).offset();
                var offsetTop = offset.top;
                var totalScroll = offsetTop - 150;
                $('body,html').animate({scrollTop: totalScroll}, 1200);
                is_error = false;
            }
          });
        }
      }
    });

emailSmsDiv = function(type){
    $("#emailRecipientDiv").hide();
    $("#smsRecipientDiv").hide();
    if(type == 'Both'){
      $("#emailRecipientDiv").show();
      $("#smsRecipientDiv").show();
    }else if(type == 'Email'){
      $("#emailRecipientDiv").show();
    }else if(type == 'SMS'){
      $("#smsRecipientDiv").show();
    }
}
</script>