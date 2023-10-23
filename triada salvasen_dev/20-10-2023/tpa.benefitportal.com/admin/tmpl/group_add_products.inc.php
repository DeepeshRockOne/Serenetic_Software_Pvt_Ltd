<style type="text/css">
.ms-drop ul{ max-height:110px!important; }
</style>
<div class="panel panel-default">
   <div class="panel-heading">
      <h4 class="mn">+ New Products</h4>
   </div>
    <form action="" role="form" method="post" name="user_form" id="user_form" class="form_wrap">
      <input type="hidden" name="group_id" value="<?=$group_id?>" />
      <div class="panel-body">
        <div class="theme-form m-t-15">
          <div class="form-group">
            <select name="products[]" id="products" class="se_multiple_select" multiple="multiple">
                <?php foreach ($company_arr as $key => $company){
                    if($company){ ?>
                        <optgroup label="<?= $key ?>">
                            <?php foreach ($company as $pkey => $row) {
                              $option_display = $row['prdName'].' '. (!empty($row["product_code"]) ? '('.$row["product_code"].')' : ''); ?>
                              <option value="<?= $row["id"] ?>"> <?= $option_display ?></option>
                            <?php } ?>
                        </optgroup>
                    <?php }
                } ?>
              </select>
              <label>Select Products</label>
              <p class="error" id="products"><?=!empty($errors['products']) ? $errors['products']:'' ?></p>
          </div>
           <div class="text-center">
              <button class="btn btn-info" class="btn btn-action"  type="submit" name="save" id="save">Confirm</button>
              <a href="javascript:void(0);" class="btn red-link" id="cancel" onclick='parent.$.colorbox.close()'>Cancel</a>
           </div>
        </div>
      </div>
    </form>
</div>

<script type="text/javascript">
$(document).ready(function() {
  $("#products").multipleSelect({
    selectAll: false,
  });
});
</script>