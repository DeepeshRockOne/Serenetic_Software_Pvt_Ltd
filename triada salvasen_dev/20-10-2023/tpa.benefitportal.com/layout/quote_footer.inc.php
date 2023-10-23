<style type="text/css">
#wrapper { min-height: 97vh; position: relative; }  
</style>
<div class="smarte_footer" style="<?=($enrollmentLocation == "self_enrollment_site")?"padding-top: 0px;":""?>">
    <?php if($enrollmentLocation != "self_enrollment_site") { ?>
  	<div class="container">
    	<div class="row footer_help m-b-15">
            <div class="col-xs-7">
                <h4 class="text-action m-t-0">NEED HELP?</h4>
                <?php if($parent_agent_row['display_in_member'] == "N") { ?>
                    <p class="need_help mn">
                        <span><?=$parent_agent_row['public_name'];?></span>
                        <span><a href="<?='tel:+1'.$parent_agent_row['public_phone']?>"><?=format_telephone($parent_agent_row['public_phone']);?></a></span>
                        <span> <a href="mailto:<?=$parent_agent_row['public_email'];?>"><?=$parent_agent_row['public_email'];?></a></span>
                    </p>
                <?php } else { ?>
                    <?php
                    $agent_services_cell_phone = get_app_settings('agent_services_cell_phone');
                    $agent_services_email = get_app_settings('agent_services_email');
                    ?>
                    <p class="need_help mn">
                        <span>Agent Services</span>
                        <span><a href="<?='tel:+1'.$agent_services_cell_phone?>"><?=format_telephone($agent_services_cell_phone);?></a></span>
                        <span><a href="mailto:<?=$agent_services_email;?>"><?=$agent_services_email;?></a></span>
                    </p>
                <?php } ?>
            </div>
            <?php
                $footer_image_url = $POWERED_BY_LOGO;
                if($parent_agent_row['is_branding'] == "Y") {
                    if(!empty($parent_agent_row['brand_icon']) && file_exists($AGENTS_BRAND_ICON.$parent_agent_row['brand_icon'])) {
                        $footer_image_url = $AGENTS_BRAND_ICON_WEB.$parent_agent_row['brand_icon'];
                    }
                }
            ?>
            <div class="col-xs-5 text-right">
                <div class="powered_by_logo">
                <img src="<?=$footer_image_url?>"  height="43px"/>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    <div class="bottom_footer">
    	<div class="container">
            <ul>
                <li><?= $DEFAULT_SITE_NAME ?> &copy; <?php echo date('Y')?> </li>
            </ul>
        </div>
    </div>
</div>
<script type="text/javascript">
 $(document).ready(function () {
  $('body').addClass('enrollment_page');
 });
</script>