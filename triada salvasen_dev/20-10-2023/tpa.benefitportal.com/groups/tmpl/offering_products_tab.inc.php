<h5 class="m-t-0">Product(s)</h5>
<p>Select product(s) below.</p>
<div class="form-group">
      <select name="products[]" id="products" class="se_multiple_select" multiple="multiple">
         <?php foreach ($company_arr as $key => $company){
              if($company){ ?>
                  <optgroup label="<?= $key ?>">
                      <?php foreach ($company as $pkey => $row) {
                      $option_display = $row['prdName'].' '. (!empty($row["product_code"]) ? '('.$row["product_code"].')' : ''); ?>
                      <option value="<?= $row["id"] ?>" <?= !empty($products_arr) && in_array($row['id'], $products_arr) ? 'selected' : '' ?>><?= $option_display ?></option>
                      <?php } ?>
                  </optgroup>
              <?php } ?>
          <?php } ?>
      </select>
      <label>Search Product(s)</label>
      <p class="error" id="error_products"></p>
</div>

<p>Would group like to financially contribute toward the products selected above?</p>
<div class="m-b-25">
   <div class="m-b-10">
      <label class="mn"><input type="radio" name="is_contribution" value="Y" <?= !empty($is_contribution) && $is_contribution=='Y' ? 'checked' : '' ?>> Yes</label>
   </div>
   <div class="m-b-0">
      <label class="mn"><input type="radio" name="is_contribution" value="N" <?= !empty($is_contribution) && $is_contribution=='N' ? 'checked' : '' ?>> No</label>
   </div>
   <p class="error" id="error_is_contribution"></p>
</div>

<div id="is_contribution_div" style="<?= !empty($is_contribution) && $is_contribution=='Y' ? '' : 'display: none' ?>">
   <p>Would you like to display the group contributions on member application?</p>
   <div class="m-b-25">
      <div class="m-b-10">
         <label class="mn"><input type="radio" name="display_contribution_on_enrollment" value="Y" <?= !empty($display_contribution_on_enrollment) && $display_contribution_on_enrollment=='Y' ? 'checked' : '' ?>> Yes</label>
      </div>
      <div class="m-b-0">
         <label class="mn"><input type="radio" name="display_contribution_on_enrollment" value="N" <?= !empty($display_contribution_on_enrollment) && $display_contribution_on_enrollment=='N' ? 'checked' : '' ?>> No</label>
      </div>
      <p class="error" id="error_display_contribution_on_enrollment"></p>
   </div>
</div>

<div class="text-center">
      <a href="javascript:void(0);" class="btn btn-action next_tab_button" data-step="2">Next</a>
      <a href="javascript:void(0);" class="btn red-link cancel_tab_button" >Cancel</a>
</div>