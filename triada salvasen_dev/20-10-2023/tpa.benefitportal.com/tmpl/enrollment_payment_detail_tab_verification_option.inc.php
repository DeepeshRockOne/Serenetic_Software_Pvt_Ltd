<div class="agent_assisted_modal">
  <div class="verify_opt" >  
    <!-- Paper Application Verification start (option-1 & 4) -->
      <div class="clearfix" style="display: none;" id="physical_file_div">
      
        <div class="row" id="physical_file_input">
          <div class="col-md-8">
           <div class="form-group error_sm_display">
             <label><p class="m-t-10 m-b-0 fs14">A physical copy of this application form must be upload here :<em>*</em></p></label>
             <div class="custom_drag_control"> <span class="btn btn-info btn-sm">Choose File</span>
              <input type="file" class="gui-file" id="physical_upload" name="physical_upload">
              <input type="text" class="gui-input" placeholder="Drag or Select File">
              <p class="error" id="error_physical_upload"></p> </div>
             <div class="clearfix"></div>
           </div>
          </div>
        </div>

        <div class="pr" id="physical_voice_file_input">        
           <div class="error_sm_display" style="display: none">
              <div class="form-group">
               <label class="mn">
                  <input type="radio" name="voice_application_type" class="voice_application_type" value="by_system_code">
                  Verification System Code: 
               </label>
             </div>
             <div class="form-group">
                <input type="text" name="voice_verification_system_code" value="" class="form-control">  
                <p class="error" id="error_voice_verification_system_code"></p>
             </div>
           </div>
           <div class="error_sm_display">
             <div class="form-group" style="display: none">	
              <label class="mn label-input">
                <input type="radio" name="voice_application_type" class="voice_application_type" value="by_file" checked="">
                A Voice recording confirmation of this application must be uploaded here
              </label>
             </div>
             <div class="row">
              <div class="col-sm-4">
                 <div class="form-group">
                    <div class="custom_drag_control"> <span class="btn btn-action">Upload Audio</span>
                    <input type="file" class="gui-file" id="voice_physical_upload_0" name="voice_physical_upload[0]">
                    <input type="text" class="gui-input" placeholder="Choose File" size="40">
                    <p class="error" id="error_voice_physical_upload_0"></p> </div>
                 </div>
              </div>
           </div>
             <div id="additional_voiceRecording_div">
             </div>
              <div id="additional_voiceRecording_btn" style="display: none">
                    <div class="row">
                       <div class="col-md-12 m-b-40">
                      <input type="hidden" name="total_voiceRecord" id="total_voiceRecord"
                         value="0"> 
                        <a href="javascript:void(0)" id="additional_voiceRecording"
                         data-counter="<?= !empty($voice_rec) ? count($voice_rec) - 1 : 0 ?>"
                         class="btn btn-action"> + Additional Voice Recording </a>
                       </div>
                    </div>
              </div> 
           </div>
           <span class="error radio_error bom_inherit bom_inmd" id="error_voice_application_type"></span>
        </div>
      </div>
    <!-- Paper Application Verification End (option-1 & 4) --> 
    
    
    <!-- Email/SMS Verification Start (option-2) -->
    <div class="theme-form">
      <div id="member_submit_button" style="display: none;">
        <div class="row">
          <div class="col-sm-4">
        <div class="form-group">
        <select class="form-control" name="sent_via" id="sent_via">
           <option value="text">Text Message (SMS)</option>
           <option value="email">Email</option>
           <option value="Both">Email & Text Message (SMS)</option>
        </select>
        <label>Select Delivery Method<em>*</em></label>
      </div>
    </div>
    </div>
            <div id="show_send_option" class="clearfix">
              <div class="row">
              <div id="email_tp" class="emailtp tp" style="display: none;"> 
                <div class="col-sm-4">
                  <div class="form-group">
                   <input type="text" readonly name="email_name_to" id="email_name_to" class="form-control" size="35" value="<?= isset($email_name_to) ? $email_name_to : ''  ?>"/>
                   <label>Email </label>
                   <p class="error" id="error_email_name_to"></p> 
                  </div>
                </div>
                 <div class="clearfix"></div>
                  <div class="col-sm-4">
                  <div class="form-group">
                   <input type="text" name="email_subject" id="email_subject" class="form-control" size="35" value="<?= $email_subject ?>"/>
                  <label>Subject </label>
                   <p class="error" id="error_email_subject"></p> 
                  </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-8 m-b-20">
                <div class="m-b-25"> 
                  <textarea id="email_content" name="email_content"><?= $email_content ?></textarea>
                  <span class="error textarea_error"  id="error_email_content"></span>
                  <!-- <div class="tag_text"> <span >Available tags : [[fname]], [[lname]], [[Email]], [[Phone]], [[Agent]], [[ParentAgent]], [[MemberID]], [[ActiveProducts]], [[link]]
                  </span> </div> -->
                </div>
              </div>
              </div>
               <div class="clearfix"></div>
               <div id="sms_tp" class="smstp tp">
               <div class="col-sm-4">
                  <div class="form-group">
                   <input type="text" readonly name="phone_number_to" id="phone_number_to" class="form-control" size="35" value="<?= isset($phone_number_to) ? $phone_number_to : ""?>"/>
                   <label>Phone Number :</label>
                   <p class="error" id="error_phone_number_to"></p> 
                  </div>
                </div>
                <div class="clearfix"></div> 
                <div class="col-sm-8">
                  <div class="form-group height_auto"> 
                   <textarea id="sms_content" name="sms_content" rows="3" class="form-control" maxlength="160"><?= $sms_content ?></textarea>
                    <span class="error textarea_error" id="error_sms_content"></span>
                    <!-- <div class="tag_text"> Character limit : <span id="message1">160</span>  <br><span class="">Available tags: [[fname]], [[lname]], [[link]]</span> </div> -->
                  </div>
                </div>
              </div>
            </div>
              <div id='both_button'>
              </div>
          </div>
        </div>
      </div>
    <!-- Email/SMS Verification End (option-2) --> 
    
    <!-- Electronic Signature by member start (option-3) -->
      <div id="direct_member" style="<?=$is_direct_application=="Y"?'':'display: none'?>;">
        <div id="passwordDiv">          
          <div class="clearfix"></div>
          <h4 class="p-title m-b-20">Electronic Signature</h4>
          <p class="m-b-30">I agree that I have a full and complete understanding of the products for which I am electing and I am the applicant listed above. I accept the following:</p>  
          <div class="clearfix"></div>
          <div id="product_checkbox_signature_div"></div> 
          <div class="clearfix"></div>
          <hr />
          
          <label class="m-b-30 m-t-15 label-input">
          	<input type="checkbox" name="product_term_check[0]" id="product_term_check_0">
              <span class="p-l-10">I acknowledge that I have read and agree to the <a href="javascript:void(0)" class="red-link terms_popup">terms and conditions</a> in this agreement.</span>
            <p class="error" id="error_product_term_check_0"></p>
          </label>

          <div class="m-b-30" id="joinderAgreementDiv" style="display: none;">
            <label class="mn label-input">
              <input type="hidden" name="joinder_agreement" id="joinder_agreement" value="N">
              <input type="checkbox" name="joinder_agreement_check" value="Y"><sapn class="p-l-10">I acknowledge that I have read and agree to the <a href="javascript:void(0);" class="prd_agreement_popup fw500 red-link">Joinder Agreement</a>.</span>
              <p class="error" id="error_joinder_agreement_check"></p>
            </label>
          </div>
          
          <div class="clearfix"></div>
          
          <!--Signature Pad Start  -->
            <p class="error" id="error_signature_data"></p>
            <div id="signature-pad" class="m-signature-pad pr m-b-15"> 
             <div class="m-signature-pad--body">
              <canvas></canvas>
             </div>
             <div class="clearfix"></div>
             <div class="m-signature-pad--footer">
              <div class="description pull-left m-t-10">Draw your signature above </div>
              <div class="pull-right m-t-10 clearfix">
               <div class=""> <a href="javascript:void(0)" class="text-action" data-action="clear">Clear Signature</a> 
                <!-- <button type="button" class="button clear btn btn-default" data-action="clear"> Clear</button> --> 
               </div>
              </div>
             </div>
            </div>
          <!--Signature Pad End  --> 
        </div>
      </div>
    <!-- Electronic Signature by member End (option-3) --> 
  </div>
</div>