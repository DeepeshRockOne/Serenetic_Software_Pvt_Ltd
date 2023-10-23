<h4 class="m-t-0 m-b-20">Group Agreement and Terms and Conditions</h4>
<div class="thumbnail bg_white m-b-30">
   <?= !empty($group_terms) ? $group_terms : '' ?>
</div>
<div class="m-b-30">
   <label class="mn label-input"><input type="checkbox" name="check_agree" value="Y">&nbsp;I agree to the Terms and Conditions and accept the Agreement<span class="text-action">*</span><p class="error" id="error_check_agree"></p></label>
</div>

<!--Signature Pad Start  -->
<p class="error" id="error_signature_data"></p>
<div id="signature-pad" class="m-signature-pad" style="height:300px">
   <div class="m-signature-pad--body">
      <canvas></canvas>
   </div>
   <div class="m-signature-pad--footer">
      <div class="description pull-left m-t-10">Draw your signature above </div>
      <div class="pull-right m-t-10 clearfix">
         <a href="javascript:void(0)" class="text-action" data-action="clear">Erase</a>
      </div>
   </div>
</div>
<div id='both_button'></div>
<!--Signature Pad End  -->
<div class="text-right m-t-30">
   <a href="javascript:void(0);" class="btn btn-action next_tab_button" data-step="3">Submit</a>
   <a href="javascript:void(0);" class="btn red-link cancel_tab_button">Cancel</a>
</div>
<div class="text-right m-t-20">
  <span><small>Last Saved Timestamp : <?=$last_saved?></small></span>
</div>