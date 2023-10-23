<?php

if (!isset($features_arr)) {
    $features_arr = get_admin_feature_access_options();
}
?>
<div id="acl">
  <p class="text-gray fs12">Choose the modules of what this admin will have access to<sup class="text-red">*</sup></p>
  <div class="table-responsive br-n">
  <table class="table access_table text-center theme-form">
    <thead>
      <tr class="blue_head">
        <th></th>
        <th colspan="2" id="lvl_name">Admin Level Name</th>
      </tr>
      <tr>
        <th><label>Modules</label></th>
        <th><label>Read Write</label></th>
        <th><label>Read Only</label></th>
      </tr>
    </thead>
    <tbody>
      <?php if(!empty($features_arr)){ $count = 1; ?>
        <?php foreach ($features_arr as $key => $feature) {?>
          <?php if (isset($feature['id']) && is_numeric($feature['id'])) {?>
            <?php $flabel = $feature["title"] == 'Eligibility Files' ? 'Eligibility' : $feature["title"];?>
            <tr>
              <td>
                <a role="button" data-toggle="collapse" data-parent="#accordion" href=".c<?=$feature["id"]?>" aria-expanded="true" aria-controls="c<?=$feature['id']?>">
                  <span class="link">
                    <label for="name" class="fw500"> <?=$flabel;?></label> 
                    <i class="fa fa-sort text-action pull-right"></i>
                  </span> 
                </a>
              </td>
              <td> 
                <input type="checkbox" class="feature_click_<?=$feature['id']?>" id="parent_feature_<?=$feature['id']?>" name="feature[]" value="<?=$feature['id'];?>" data-type="Parent" data-id="<?=$feature['id']?>" data-role_type="rw">
              </td>
              <td>
                <?php if(in_array($feature['id'],array(1,5,8,12,11,60))) { ?> 
                  <input type="checkbox" class="feature_click_ro_<?=$feature['id']?>" id="ro_parent_feature_<?=$feature['id']?>" name="feature[]" value="ro_<?=$feature['id'];?>" data-type="Parent" data-id="<?=$feature['id']?>" data-role_type="ro">
                <?php } ?> 
              </td>
            </tr>
            <?php if (!empty($feature['child'])) { ?>
              <?php foreach ($feature['child'] as $child) {?>
                <?php if (isset($child['id']) && is_numeric($child['id'])) {?>
                  <?php $label = $child["title"] == 'Types' ? 'Categories' : $child["title"]; ?>
                  <?php $label = $label == 'Pending Eligibility' ? 'Generator' : $label; ?>
                  <?php $label = $label == 'Eligibility History' ? 'History' : $label; ?>
                  <tr id="<?=$feature['id']?>" class="panel-collapse collapse c<?=$feature['id']?>">
                    <td class="text-right">
                      <label><?=$label;?></label>
                    </td>
                    <td class="feature_click_<?=$child['id']?>"> 
                      <input type="checkbox" id="feature_<?=$child['id']?>" name="feature[]" value="<?=$child['id'];?>" data-type="Child" data-id="<?=$child['id']?>" data-parent-id="<?=$feature['id']?>" class="parent_<?=$feature['id']?>" data-role_type="rw"> 
                    </td>
                    <td class="feature_click_ro_<?=$child['id']?>">
                      <?php if(in_array($child['id'],array(1,5,8,12,11,60))) { ?> 
                      <input type="checkbox" id="feature_ro_<?=$child['id']?>" name="feature[]" value="ro_<?=$child['id'];?>" data-type="Child" data-id="<?=$child['id']?>" data-parent-id="<?=$feature['id']?>" class="ro_parent_<?=$feature['id']?>" data-role_type="ro">
                      <?php } ?>
                    </td>
                  </tr>
                <?php }?>
              <?php }?>
            <?php } ?>
          <?php } ?>
        <?php $count++; } ?>
      <?php } ?>
    </tbody>
  </table>
</div>
</div>
<p class="error" id="error_features"></p>
<script type="text/javascript"> 
  var acl_name = "<?=checkIsset($acl_name)?>";
  if (acl_name != '') {
    $('input[name*="feature"]').prop('checked', false);
    var acl_names = <?=json_encode($acl_names);?>;
    var acl_features = <?=json_encode($acl_features);?>;
    $.each(acl_features[acl_name], function(index, value) {
      $('.feature_click_' + value + '').prop('checked', true);
      $('#feature_' + value + '').prop('checked', true);
    });
  }

  $(document).on('click', 'input[name*="feature"]', function() {
      $type = $(this).attr('data-type');
      $role_type = $(this).attr('data-role_type');
      $feature_id = $(this).attr('data-id');

      if ($(this).is(":checked")) {
        if($role_type == "ro") {
          $("[data-id='"+$feature_id+"'][data-role_type='rw']").prop('checked', false);

          if ($type == "Parent") {
            $parent_id = $(this).attr('data-id');
            $(".ro_parent_" + $parent_id).prop('checked', true);

            $(".parent_" + $parent_id).prop('checked', false);
          } else {
            $parent_id = $(this).attr('data-parent-id');
            $("#ro_parent_feature_" + $parent_id).prop('checked', true);

            if(!($(".parent_" + $parent_id + ":checked").length > 0)) {
                $("#parent_feature_" + $parent_id).prop('checked', false);
            }
          }
        } else {
          $("[data-id='"+$feature_id+"'][data-role_type='ro']").prop('checked', false);

          if ($type == "Parent") {
            $parent_id = $(this).attr('data-id');
            $(".parent_" + $parent_id).prop('checked', true);

            $(".ro_parent_" + $parent_id).prop('checked', false);
          } else {
            $parent_id = $(this).attr('data-parent-id');
            $("#parent_feature_" + $parent_id).prop('checked', true);

            if(!($(".ro_parent_" + $parent_id + ":checked").length > 0)) {
                $("#ro_parent_feature_" + $parent_id).prop('checked', false);
            }
          }
        }        
      } else {
        if($role_type == "ro") {
          if ($type == "Parent") {
            $parent_id = $(this).attr('data-id');
            $(".ro_parent_" + $parent_id).prop('checked', false);
          } else {
            $parent_id = $(this).attr('data-parent-id');

            if(!($(".ro_parent_" + $parent_id + ":checked").length > 0)) {
                $("#ro_parent_feature_" + $parent_id).prop('checked', false);
            }
            if(!($(".parent_" + $parent_id + ":checked").length > 0)) {
                $("#parent_feature_" + $parent_id).prop('checked', false);
            }
          }
        } else {
          if ($type == "Parent") {
            $parent_id = $(this).attr('data-id');
            $(".parent_" + $parent_id).prop('checked', false);
          } else {
            $parent_id = $(this).attr('data-parent-id');

            if(!($(".ro_parent_" + $parent_id + ":checked").length > 0)) {
                $("#ro_parent_feature_" + $parent_id).prop('checked', false);
            }
            if(!($(".parent_" + $parent_id + ":checked").length > 0)) {
                $("#parent_feature_" + $parent_id).prop('checked', false);
            }   
          }
        }        
      }
      $.uniform.update();
  });
</script>