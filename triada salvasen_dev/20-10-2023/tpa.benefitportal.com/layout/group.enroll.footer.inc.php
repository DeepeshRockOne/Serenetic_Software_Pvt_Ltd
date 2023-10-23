<div class="footer">
    <div class="container-fluid">
        <div class="row footer_help">
            <div class="col-xs-7">
                <h4 class="text-action m-t-0">NEED HELP?</h4>
                <?php if($agent_row['display_in_member'] == "N") { ?>
                    <p class="need_help mn"><span><?=$agent_row['public_name'];?>  </span>  <span><a href="<?='tel:+1'.$agent_row['public_phone']?>"><?=format_telephone($agent_row['public_phone']);?></a></span> <span> <a href="mailto:<?=$agent_row['public_email'];?>"><?=$agent_row['public_email'];?></a></span> </p>
                <?php } else {
                    $setting_keys = array(
                        'group_services_email',
                        'group_services_cell_phone',
                        'enrollment_display_name',
                    );
                    $app_setting = get_app_settings($setting_keys);
                    ?>
                    <p class="need_help mn"><span><?=$app_setting['enrollment_display_name'];?>  </span> <span>  <a href="<?='tel:+1'.$app_setting['group_services_cell_phone']?>"><?=format_telephone($app_setting['group_services_cell_phone']);?></a> </span> <span> <a href="mailto:<?=$app_setting['group_services_email'];?>"><?=$app_setting['group_services_email'];?></a> </span></p>
                <?php } ?>
            </div>
            <div class="col-xs-5 text-right">
                <div class="powered_by_logo">
                    <?php if(!empty($POWERED_BY_LOGO)){ ?>
                        <img width="43px" height="43px" src="<?php echo $POWERED_BY_LOGO; ?>">
                    <?php } ?>
                </div>
              </div>
        </div>
    </div>
</div>
