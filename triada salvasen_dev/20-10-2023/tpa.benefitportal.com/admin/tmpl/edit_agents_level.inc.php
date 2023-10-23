<form id="create_new_admin_level_frm"   method="POST">
  <input type="hidden" name="id" value="<?=$_GET['id']?>">
  <div class="add_level_panelwrap create_admin_popup">
    <div class="panel panel-default panel-block Font_Roboto ">
      <div class="panel-heading">
        <h4 class="mn fw500"><img src="images/icons/add_level_icon.svg" height="20px" />&nbsp;&nbsp; Edit Level</h4>
      </div>
      <div class="panel-body theme-form">
        <div class="form-group has-value">
          <input type="text" name="access_name" class="form-control has-value" value="<?=$_GET['lvl_name']?>" />
          <label>Name Level<sup class="text-red">*</sup></label>
          <p class="error" id="error_access_name"></p>
        </div>
        <p class="text-gray fs12">Choose the modules this agent will have access to<sup class="text-red">*</sup></p>
        <table class="table  access_table text-center theme-form">
          <thead>
            <tr class="blue_head">
              <th></th>
              <th colspan="2" >Features</th>
            </tr>
            <tr>
              <th><label>Modules</label></th>
              <th><label>Read &amp; Write</label></th>
            </tr>
          </thead>
          <tbody>
          <?php if(!empty($features_arr)){ $count = 1; ?>
        <?php foreach ($features_arr as $key => $feature) {?>
          <?php if (isset($feature['id']) && is_numeric($feature['id'])) {?>
            <tr>
              <td>
                <a role="button" data-toggle="collapse" data-parent="#accordion" href=".c<?=$feature["id"]?>" aria-expanded="true" aria-controls="c<?=$feature['id']?>">
                  <span class="link">
                    <label for="name" class="fw500"> <?=$feature["title"]?></label> 
                    <i class="fa fa-sort text-action pull-right"></i>
                  </span> 
                </a>
              </td>
              <td> 
                <input type="checkbox" class="feature_click_<?=$feature['id']?>" id="parent_feature_<?=$feature['id']?>" name="feature[]" value="<?=$feature['id'];?>" data-type="Parent" data-id="<?=$feature['id']?>">
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
                        <input type="checkbox" id="feature_<?=$child['id']?>" name="feature[]" value="<?=$child['id'];?>" data-type="Child" data-id="<?=$child['id']?>" data-parent-id="<?=$feature['id']?>" class="parent_<?=$feature['id']?>"> 
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
        <p class="error" id="error_features"></p>
        <div class="form-group text-center m-t-30"> 
        <input type="hidden" name="update">
        <button type="submit" id="save_btn" name="save_btn" value="update_level" class="btn btn-action m-r-10">Save</button>
        <a href="javascript:void(0);" onclick='parent.$.colorbox.close(); return false;' class="btn red-link ">Cancel</a> 
        </div>
      </div>
    </div>
  </div>
</form>
<script type="text/javascript">
$(document).off("click", "#save_btn");
  $(document).on("click", "#save_btn", function(e) {
    e.preventDefault();
    $("#ajax_loader").show();
    $(".error").html('');
    $.ajax({
      url: 'ajax_edit_agent_level.php',
      dataType: 'JSON',
      data: $("#create_new_admin_level_frm").serialize(),
      type: 'POST',
      success: function(res) {
        $("#ajax_loader").hide();
        if (res.status == "success") {
            window.parent.location.href = "manage_agents.php";
        } else {
          var is_error = true;
          $.each(res.errors, function(index, error) {
            $('#error_' + index).html(error);
            if (is_error) {
              var offset = $('#error_' + index).offset();
              if (typeof(offset) === "undefined") {
                console.log("Not found : " + index);
              } else {
                var offsetTop = offset.top;
                var totalScroll = offsetTop - 195;
                $('body,html').animate({
                  scrollTop: totalScroll
                }, 1200);
                is_error = false;
              }
            }
          });
        }
      }
    });
  });

var acl_name = "<?=checkIsset($acl_name)?>";
  if (acl_name != '') {
    $('input[name*="feature"]').prop('checked', false);
    $('[id^=checked_counter_]').html(0);
    var acl_names = <?=json_encode($acl_names);?>;
    var acl_features = <?=json_encode($acl_features);?>;
    $.each(acl_features[acl_name], function(index, value) {
      $('.feature_click_' + value + '').prop('checked', true);
      $('#feature_' + value + '').prop('checked', true);
    });

    $.each(acl_features[acl_name], function(index, value) {
      $length = $(".parent_" + value + ":checked").length;
      $("#checked_counter_" + value).html($length);
    });
  }

  $(document).on('click', 'input[name*="feature"]', function() {
    var obj = this;
    var parent_form = $(document.getElementById('user_form'));

    $type = $(this).attr('data-type');

    if ($(this).is(":checked")) {
      if ($type == "Parent") {
        $parent_id = $(this).attr('data-id');
        $(".parent_" + $parent_id).prop('checked', true);
        //$(".c" + $parent_id).collapse('hide');
      } else {
        $parent_id = $(this).attr('data-parent-id');
        $("#parent_feature_" + $parent_id).prop('checked', true);
      }
    } else {
      if ($type == "Parent") {
        $parent_id = $(this).attr('data-id');
        $(".parent_" + $parent_id).prop('checked', false);
        //$(".c" + $parent_id).collapse('hide');
      } else {
        $parent_id = $(this).attr('data-parent-id');
      }
    }

    $length = $(".parent_" + $parent_id + ":checked").length;
    $("#checked_counter_" + $parent_id).html($length);

    // if ($length == 0) {
    //   $("#parent_feature_" + $parent_id).prop('checked', false);
    //   //$("#" + $parent_id).collapse('hide');
    // }
    $.uniform.update();
  });
</script>