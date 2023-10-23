<div class="Settings_detail">
   <h5 class="m-t-0 m-b-20">Open Application Period</h5>
   <div class="row">
      <div class="col-sm-6">
         <div class="form-group">
            <div class="input-group">
               <div class="input-group-addon datePickerIcon" data-applyon="open_enrollment_start"> <i class="material-icons fs16">date_range</i> </div>
               <div class="pr">
                  <input type="text" class="form-control dates" name="open_enrollment_start" id="open_enrollment_start" value="<?= !empty($open_enrollment_start) ? $open_enrollment_start : '' ?>">
                  <label>Open Application Start (MM/DD/YYYY)</label>
               </div>
            </div>
            <p class="error" id="error_open_enrollment_start"></p>
         </div>
      </div>
      <div class="col-sm-6">
         <div class="form-group">
            <div class="input-group">
               <div class="input-group-addon datePickerIcon" data-applyon="open_enrollment_end"> <i class="material-icons fs16">date_range</i> </div>
               <div class="pr">
                  <input type="text" class="form-control dates" name="open_enrollment_end" id="open_enrollment_end" value="<?= !empty($open_enrollment_end) ? $open_enrollment_end : '' ?>">
                  <label>Open Application End (MM/DD/YYYY)</label>
               </div>
            </div>
            <p class="error" id="error_open_enrollment_end"></p>
         </div>
      </div>
   </div>
   <h5 class="m-t-0 m-b-20">Effective Date</h5>
   <div class="row">
      <div class="col-sm-6">
         <div class="form-group">
            <div class="input-group">
               <div class="input-group-addon datePickerIcon" data-applyon="first_coverage_date"> <i class="material-icons fs16">date_range</i> </div>
               <div class="pr">
                  <input type="text" class="form-control has-value dates" id="first_coverage_date" name="first_coverage_date" value="<?= !empty($first_coverage_date) ? $first_coverage_date : ''  ?>">
                  <label>First Plan Date</label>
               </div>
            </div>
            <p class="error" id="error_first_coverage_date"></p>
         </div>
      </div>
   </div>
   <p>Apply effective date waiting restrictions on open application?</p>
   <div class="form-group height_auto">
      <div class="m-b-10">
         <label class="mn"><input type="radio" name="waiting_restriction_on_open_enrollment" value="Y" <?= !empty($waiting_restriction_on_open_enrollment) && $waiting_restriction_on_open_enrollment=='Y' ? 'checked' : '' ?>> Yes</label>
      </div>
      <div class="m-b-10">
         <label class="mn"><input type="radio" name="waiting_restriction_on_open_enrollment" value="N" <?= !empty($waiting_restriction_on_open_enrollment) && $waiting_restriction_on_open_enrollment=='N' ? 'checked' : '' ?>> No</label>
      </div>
      <p class="error" id="error_waiting_restriction_on_open_enrollment"></p>
   </div>
   <p>Allow a future effective date to be selected on application?</p>
   <div class="form-group height_auto">
      <div class="m-b-10">
         <label class="mn"><input type="radio" name="allow_future_effective_date" value="Y" <?= !empty($allow_future_effective_date) && $allow_future_effective_date=='Y' ? 'checked' : '' ?>> Yes</label>
      </div>
      <div class="m-b-10">
         <label class="mn"><input type="radio" name="allow_future_effective_date" value="N" <?= !empty($allow_future_effective_date) && $allow_future_effective_date=='N' ? 'checked' : '' ?>> No</label>
      </div>
      <p class="error" id="error_allow_future_effective_date"></p>
   </div>
   <div id="allow_future_effective_date_div" style="<?= !empty($allow_future_effective_date) && $allow_future_effective_date=='Y' ? '' : 'display: none' ?>">
      <p>Set the post date range allowed:</p>
      <div class="row">
         <div class="col-sm-6">
            <div class="form-group">
               <select class="form-control" name="allowed_range" id="allowed_range" >
                  <option value=""></option>
                  <option value="30" <?= !empty($allowed_range) && $allowed_range=='30' ? 'selected' : '' ?>>30 Days</option>
                  <option value="60" <?= !empty($allowed_range) && $allowed_range=='60' ? 'selected' : '' ?>>60 Days</option>
               </select>
               <label>Set Days</label>
               <p class="error" id="error_allowed_range"></p>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="text-center">
      <a href="javascript:void(0);" class="btn btn-action next_tab_button" data-step="1">Next</a>
      <a href="javascript:void(0);" class="btn red-link cancel_tab_button" >Cancel</a>
</div>