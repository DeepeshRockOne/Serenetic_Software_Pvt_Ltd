  
  <div class="theme-form">
    <div id="primary_member_field_div" class="primary_border_box">
    </div>

    <div id="dependent_field_div" style="display: none;">
      <input type="hidden" name="spouse_products_list" id="spouse_products_list" value="">
      <input type="hidden" name="child_products_list" id="child_products_list" value="">
      <input type="hidden" name="dependent_field_number" id="dependent_field_number" value="">
      <h4 class="m-b-25 m-t-0" id="title_primary_contact">Dependent Information</h4>
      <div id="dependent_spouse_main_div" class="depedent_border_box"></div>
      <div id="dependent_child_main_div"></div>
      <p class="error" id="error_dependent_general"></p>
      <div class="clearfix"></div>
      <div class="btn-group">
        <label class="btn btn-info btn-outline" id="addSpouseField" style="display: none">+ Spouse</label>
        <label class="btn btn-info btn-outline" class="btn btn-info btn-outline" id="addChildField" style="display: none">+ Child</label>
      </div>
    </div>
  </div>
  
  <div id="beneficiary_information_div" style="display: none"  class="m-b-25">
    <input type="hidden" name="principal_beneficiary_field_number" id="principal_beneficiary_field_number" value="">
    <input type="hidden" name="contingent_beneficiary_field_number" id="contingent_beneficiary_field_number" value="">
    <input type="hidden" name="is_principal_beneficiary" id="is_principal_beneficiary" value="">
    <input type="hidden" name="is_contingent_beneficiary" id="is_contingent_beneficiary" value="">
    <h4 class="m-b-25 m-t-25">Beneficiary Information</h4>

    <div id="principal_beneficiary_div" style="display: none;">
      <p class="font-bold m-b-15">Principal Beneficiary</p>
      <p class="m-b-25">I choose the person(s) named below to be the principal beneficiary(ies) of the Life Insurance benefits that may be payable at the time of my death. If any principal beneficiary(ies) is disqualified or dies before me, his/her percentage of this benefit will be paid to the remaining principal beneficiary(ies).</p>
      <p class="m-b-25">*The percentage awarded between all principal beneficiary(ies) must add up to 100%</p>
      <div class="theme-form">  
        <div id="principal_beneficiary_field_div">
        </div>
      </div>
      <a href="javascript:void(0)" class="btn btn-outline btn-info" id="addPrincipalBeneficiaryField">+ Beneficiary</a>
      <p class="error" id="error_principal_beneficiary_general"></p>
      <hr>
    </div>
    <div id="contingent_beneficiary_div" style="display: none;">
      <p class="font-bold m-b-15">Contingent Beneficiary</p>
      <p class="m-t-25">If all principal beneficiaries are disqualified or die before me, I choose the person(s) named below to be my contingent beneficiar(ies).</p>
      <p class="m-t-25">*The percentage awarded between all contingent beneficiary(ies) must add up to 100%</p>
      <div class="theme-form">  
        <div id="contingent_beneficiary_field_div">
        </div>
      </div>
      <a href="javascript:void(0)" class="btn btn-info btn-outline m-t-25" id="addContingentBeneficiaryField">+ Contingent Beneficiary</a>
      <p class="error" id="error_contingent_beneficiary_general"></p>
    </div>


  </div>
   
  <div class="bottom_btn_wrap"> 
      <div class="pull-right">
          <a href="javascript:void(0);" class="btn btn-action next_tab_button" data-step="3"> Continue</a>
          <a href="javascript:void(0);" class="btn red-link cancel_enrollment"> Cancel</a>
      </div>
  </div>
  