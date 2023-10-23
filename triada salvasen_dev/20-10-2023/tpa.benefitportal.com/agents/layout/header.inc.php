<?php
$popup_open_res = false;
if ($_SESSION["agents"]['id'] > 0 && in_array($_SESSION['agents']['status'], array('Pending Approval', 'Pending Contract', 'Pending Documentation'))) {
    $popup_open_res = true;
}
$Header_agent_id = $_SESSION['agents']['id'];

$businessNameHeader = !empty($_SESSION['agents']['public_name']) ? $_SESSION['agents']['public_name'] : '';
$short_name = explode(' ',$businessNameHeader);
$short_name_dp = '';
if(!empty($short_name[0])){
    $first_char = $short_name[0][0];
    $short_name_dp = !empty($short_name[1][0]) ? $first_char.$short_name[1][0] : $first_char.$short_name[0][1];
}
$eo_expiration1 = checkEOExpiredOrNot($_SESSION["agents"]["id"]);

?>
<div class="header">
    <div class="container">
        <div class="dropdown agent_dropdown m-r-30">
            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                <div class="agent_circle"><?= ($businessNameHeader != "") ? strtoupper($short_name_dp) : strtoupper($_SESSION['agents']['fname'][0] . $_SESSION['agents']['lname'][0]) ?></div>
                <?= ($businessNameHeader != "") ? $businessNameHeader : $_SESSION['agents']['fname'] . " " . $_SESSION['agents']['lname'] ?>
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-left">
                <?php if (!$popup_open_res) { ?>
                    <?php if($_SESSION['agents']['is_sub_agent'] == 'Y' && $_SESSION['agents']['sub_agent_id'] > 0){
                        if(agent_has_menu_access(27)){ ?>
                            <li><a href="profile.php"><i class="ti-user"></i> My Profile</a></li>
                        <?php }
                        }else{ ?>
                            <li><a href="profile.php"><i class="ti-user"></i> My Profile</a></li>
                    <?php } ?>
                    <?php if($_SESSION["agents"]["is_branding"] == "Y") { ?>
                    <li><a href="personal_branding.php" class="btn_personal_branding"><i class="ti-palette"></i> Personal Branding</a></li>
                    <?php } ?>
                <?php } ?>
                <input type="hidden" name="previous_page" id="previous_page" value="<?=urlencode($_SERVER['REQUEST_URI'])?>">
                <li><a href="javascript:void(0);" class="btn_logout_portal"><i class="fa fa-power-off"></i> Logout</a></li>
            </ul>
        </div>
        <div class="menu">
            <ul id="menu-ul">
                <li><a href="dashboard.php"><i class="material-icons">home</i></a></li>
                <?php if (agent_has_menu_access(1) && !$popup_open_res) { ?>
                <li class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">Enroll <span
                                class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <?php if(agent_has_menu_access(2)) { ?>
                            <?php if(empty($eo_expiration1)){?>
                                <li><a href="member_enrollment.php">Member</a></li>
                            <?php } ?>
                        <?php } ?>
                        <?php if(agent_has_menu_access(3)) { ?>
                            <li><a href="#" data-toggle="modal" data-target="#enroll_model">Agent</a></li>
                        <?php } ?>
                        <?php if(agent_has_menu_access(4)) { ?>
                            <li><a href="invite_group.php">Group</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>

                <?php if (agent_has_menu_access(5) && !$popup_open_res) { ?>
                <li class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">Website <span
                                class="caret"></span></a>
                    <ul class="dropdown-menu website-dropdown">
                        <?php if (agent_has_menu_access(24)) { ?>
                            <li class="title">AAE Enrollment Website</li>
                            <?php if(!empty($_SESSION['agents']['user_name'])) { ?>
                            <li class="link">
                                <div class="pull-left ">
                                    <a href="<?= $AAE_WEBSITE_HOST . '/' . $_SESSION['agents']['user_name']; ?>" class="underline" target="_blank"><?= $AAE_WEBSITE_HOST . '/' . $_SESSION['agents']['user_name']; ?></a>
                                </div>
                                <div class="pull-right">
                                    <div class="icons">
                                        <a href="<?= $AAE_WEBSITE_HOST . '/' . $_SESSION['agents']['user_name']; ?>" target="_blank"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                    </div>
                                </div>
                            </li>
                            <?php } else { ?>
                            <li class="link">AAE enrollment website not set.</li>
                            <?php } ?>
                        <?php } ?>

                        <?php if (agent_has_menu_access(25)) { ?>
                            <li class="title">Self Enrollment Website(s)</li>
                            <?php
                            $website_res = get_websites($_SESSION['agents']['id']);
                            if (!empty($website_res)) {
                                foreach ($website_res as $key => $website_row) {
                                    ?>
                                    <li class="link">
                                        <div class="pull-left ">
                                            <a href="<?= $ENROLLMENT_WEBSITE_HOST . '/' . $website_row['user_name']; ?>"
                                               target="_blank" class="underline"><?= $website_row['page_name']; ?></a>
                                        </div>
                                        <div class="pull-right">
                                            <div class="icons">
                                                <a href="<?= $ENROLLMENT_WEBSITE_HOST . '/' . $website_row['user_name']; ?>"
                                                   target="_blank"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                <a href="javascript:void(0);" data-id="<?= $website_row['id']; ?>" data-site_name="<?= $website_row['page_name']; ?>" data-site_url="<?= $ENROLLMENT_WEBSITE_HOST . '/' . $website_row['user_name']; ?>" class="btn_share_website"><i class="fa fa-share-square-o" aria-hidden="true"></i></a>
                                            </div>
                                        </div>
                                    </li>
                                    <?php
                                }
                            } else {
                                ?>
                                <li class="link">Website(s) not added.</li>
                                <?php
                            }
                            ?>
                            <li class="title"><a href="manage_website.php" class="btn btn-white-o">Manage Websites</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>

                <?php if (agent_has_menu_access(6) && !$popup_open_res) { ?>
                <li class="dropdown ">
                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">Book of Business <span
                                class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <?php if (agent_has_menu_access(7)) { ?><li><a href="agent_listing.php">Agents</a></li><?php } ?>
                        <?php if (agent_has_menu_access(8)) { ?><li><a href="member_listing.php">Members</a></li><?php } ?>
                        <?php if (agent_has_menu_access(9)) { ?><li><a href="groups_listing.php">Groups</a></li><?php } ?>
                        <?php if (agent_has_menu_access(10)) { ?><li><a href="lead_listing.php">Leads</a></li><?php } ?>
                        <?php if (agent_has_menu_access(11)) { ?><li><a href="pending_aae_listing.php">Pending AAE</a></li><?php } ?>
                    </ul>
                </li>
                <?php } ?>
                <?php if (agent_has_menu_access(12) && !$popup_open_res) { ?>
                <li class="dropdown ">
                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">My Production <span
                                class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <?php if (agent_has_menu_access(13)) { ?>
                            <?php if($_SESSION['agents']['agent_coded_level'] !='LOA') { ?>
                            <li><a href="commissions.php">Commissions</a></li>
                            <?php } ?>
                        <?php } ?>
                        <?php if (agent_has_menu_access(14)) { ?><li><a href="product_informations.php">Products</a></li><?php } ?>
                        <?php if (agent_has_menu_access(15)) { ?><li><a href="all_orders.php">Orders</a></li><?php } ?>
                        <?php if (agent_has_menu_access(16)) { ?><li><a href="reporting.php">Reporting</a></li><?php } ?>
                        <?php if (agent_has_menu_access(17)) { ?><li><a href="payment_transaction.php">Transactions</a></li><?php } ?>
                    </ul>
                </li>
                <?php } ?>

                <?php if (agent_has_menu_access(18) && !$popup_open_res) { ?>
                <li class="dropdown ">
                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">Resources <span
                                class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <?php if (agent_has_menu_access(19)) { ?><li><a href="add_email_broadcast.php">Email Broadcaster</a></li><?php } ?>
                        <?php if (agent_has_menu_access(20)) { ?><li><a href="add_sms_broadcast.php">Text Broadcaster</a></li><?php } ?>
                        <?php if (agent_has_menu_access(21)) { ?><li><a href="communications_queue.php">Communications Queue</a></li><?php } ?>
                        <?php if (agent_has_menu_access(22)) { ?><li><a href="training_manuals.php">Training Manuals</a></li><?php } ?>
                        <!-- <li><a href="api_integrations.php">API Integrations</a></li> -->
                        <?php if (agent_has_menu_access(23)) { ?><li><a href="support.php">Support </a></li><?php } ?>
                    </ul>
                </li>
                <?php } ?>
            </ul>
        </div>
        <div class="right-icons m-t-0">
            <?php
                include_once("header_notification.inc.php");
            ?>
            <ul class="nav navbar-top-links">
                <li class="dropdown"> 
                    <a href="javascript:void(0);" class="menu-icon hide" > <span class="bar-1"></span> <span
                            class="bar-2"></span> <span class="bar-3"></span> </a>
                </li>
                <?php if (agent_has_menu_access(6)) { ?>
                <li class="dropdown"> 
                    <a href="javascript:void(0);" class="ricon-link" id="search-icon" > <i class="material-icons">search</i></a>
                </li>
                <?php } ?>            
                <li class="dropdown msg_notification"> 
                    <a href="javascript:void(0);" class="ricon-link dropdown-toggle" id="noti_bell" data-toggle="dropdown" href="#"><i class="material-icons">notifications</i><span class="badge badge-danger" id="not_counter"><?=$notifications_total > 0 ? $notifications_total : ""?></span></a>
                    <ul class="dropdown-menu mailbox dropdown-menu-right">
                        <li><div class="drop-title text-left"><span class="fw500">Notifications</span> <a href="javascript:void(0);" class="pull-right clear_all_noti">Clear all</a></div></li>
                        <li>
                            <div class="message-center headerAllNotification" data-count=0>
                                <div class="noti_list"><?=$listNotification;?></div>
                                <div>
                                    <div class="text-center notification_loader text-action" style="display: none"><i class="fa fa-spinner fa-spin fa-lg"></i> Loding...</div>
                                </div>                    
                            </div>
                        </li>                   
                    </ul>
                </li>
            </ul>
        </div>
        <?php if (agent_has_menu_access(6)) { ?>
        <div class="searching_panel top-right">
            <div id="searchbar_wrap">
                <form method="GET" action="global_search.php" id="global_search" name="global_search" role="search" class="app-search ">
                    <div class="search-group">
                        <input type="text" name="gsearch" id="gsearch" placeholder="Search..." size="" class="form-control gsearch">
                        <input type="hidden" name="is_ajaxed" id="g_is_ajaxed" value="false" />
                        <input type="hidden" name="pages" id="g_per_pages" value="<?=$per_page;?>" />
                        <input type="hidden" name="sort" id="g_sort_column" value="<?=isset($SortBy) ? $SortBy : '';?>" />
                        <input type="hidden" name="direction" id="g_sort_direction" value="<?=isset($SortDirection) ? $SortDirection : '';?>" />
                        <input type="hidden" name="type" id="g_type" value="" />
                        <input type="hidden" name="rep_id" id="g_rep_id" value="" />
                        <input type="hidden" name="fname" id="g_fname" value="" />
                        <input type="hidden" name="email" id="g_email" value="" />
                        <input type="hidden" name="custom_date" id="g_custom_date" value="" />
                        <input type="hidden" name="fromdate" id="g_fromdate" value="" />
                        <input type="hidden" name="todate" id="g_todate" value="" /> 
                        <button type="submit" id="all_search"><i class="fa fa-search"></i></button>
                    </div>
                </form>
            </div>      
        </div>
        <?php } ?>
    </div>
</div>
<!-- Modal -->
<div id="enroll_model" class="modal fade enroll_model" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <div class="text-center">
                    <h4 class="m-b-30 m-t-20">Select the type of agent invite below</h4>
                    <div class="clearfix m-b-20">
                        <a href="invite_agent.php" class="btn btn-info">Personalized Agent Invite</a>
                        <a href="agent_enrollment_urls.php" class="agent_enrollment_urls btn btn-action"
                           data-dismiss="modal"
                        >Generic Agent Invite</a>
                        <a href="javascript:void(0);" class="btn red-link" data-dismiss="modal">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Share website Modal -->
<div id="share_website_model" class="modal fade share_website_model" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Share Website - <span class="fw300"><?=!empty($_SESSION['agents']['public_name']) ? $_SESSION['agents']['public_name'] : $_SESSION['agents']['fname'].' '.$_SESSION['agents']['lname'];?></span></h4>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <h5 class="m-b-20 fs16">Share Your Self Enrollment Website</h5>
                    <p class="m-b-20">Send link via:</p>
                    <div class="clearfix m-b-20">
                        <a href="javascript:void(0);" data-dismiss="modal" class="btn btn-info btn_email_share_website">
                            Email
                        </a>
                        <a href="javascript:void(0);" data-dismiss="modal" class="btn btn-action btn_sms_share_website">
                            SMS Text
                        </a>
                        <?php /*<a href="javascript:void(0);" class="btn btn-cyan">Facebook Messenger</a>*/ ?>
                    </div>
                    <div class="or_line">Or</div>
                    <div class="m-b-20 m-t-20">
                        <div class="input-group">
                            <input type="text" class="form-control" id="swm_site_url" readonly="readonly">
                            <span class="input-group-addon btn btn-info" data-clipboard-target="#swm_site_url" id="swm_copy_site_url">COPY LINK</span>
                        </div>
                    </div>
                    <div class="clearfix">
                        <a href="javascript:void(0);" class="fw500 text-action" data-dismiss="modal">Close</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    /* responsive toggle menu script start */
    $(document).off("click", ".menu-icon");
    $(document).on("click", ".menu-icon", function () {
        $("#menu-ul").slideToggle('300').toggleClass('show');
        $(".menu-icon").toggleClass('active');
        $('body').toggleClass('menu-overlay');
    });
    /* responsive toggle menu script end */
</script>
<div class="sub-header">
    <div class="container">
        <?php if (isset($breadcrumbes)): ?>
            <div id="page">
                <ol class="breadcrumb breadcrumb-nav">
                    <?php $link = 'dashboard.php'; ?>
                    <li><a href="<?= $link ?>"><i class="material-icons">home</i></a></li>
                    <?php //$breadcrumbes['title'] = '<i class="fa fa-home"></i>';
                    // $breadcrumbes['link'] = 'dashboard.php';
                    foreach ($breadcrumbes as $key => $breadcrumbe) {
                        if ($key == 0)
                            continue; ?>
                        <li <?php echo isset($breadcrumbe['class']) ? 'class="' . $breadcrumbe['class'] . '"' : '' ?>>
                            <a href="<?= !empty($breadcrumbe['link']) ? $breadcrumbe['link'] : '' ?>"><?php echo isset($breadcrumbe['title']) ? $breadcrumbe['title'] : '' ?></a>
                        </li>
                    <?php } ?>
                </ol>
            </div>
        <?php endif; ?>

        <?php if (agent_has_menu_access(13)) { ?>
        <?php if($_SESSION['agents']['agent_coded_level'] != 'LOA') { ?>
        <p class="pull-right mn" style="display: <?=($_SESSION['agents']['agent_coded_level']=='LOA')?'none;':''?>">
            <?php 
            $header_commission_res = get_pay_period_commission_totals($_SESSION['agents']['id']);
            ?>
            Commissions - &nbsp;
            Weekly <strong><a href="weekly_commission.php" class="commission_popup text-success"><?=displayAmount($header_commission_res['weekly']);?></a></strong>
            &nbsp;
            Monthly <strong><a href="monthly_commission.php" class="commission_popup text-success"><?=displayAmount($header_commission_res['monthly']);?></a></strong>
        </p>
        <?php } ?>
        <?php } ?>
    </div>
</div>
<div class="clearfix"></div>
<div style="display:none">
    <div id='licensePopupOpenExpired'>
        <div class="panel panel-default panel-shadowless mn height_auto">
        <div class="panel-heading br-b height_auto p-b-10">
            <h3 class="text-action m-t-n fw600"><a href="javascript:void(0)" class="fw500 text-black"><i class="text-action material-icons v-align-middle fs20" style="font-size: inherit;">info</i> License Expired</a></h3>
        </div>
        <div class="panel-body table_light_danger height_auto p-b-10">
            <p class="text-center ">
               One or more license(s) have expired and requires your attention to enroll and earn commissions in that state. Click the update button below to manage your license(s).
            </p>
            
        </div>
        <br />
        <p class="text-center p-t-10">
            <input type="checkbox" name="license_expired" id="dont_show_license_expired"  onclick="update_do_not_show($(this),'license_expired')">
            &nbsp;Don't show this again
        </p>
        <div class="text-center p-t-10" >
            <a href="profile.php" class="btn btn-action" id="update_license_expired">Update</a>
            <a href="javascript:void(0);" onclick='$.colorbox.close(this); return false;' class="btn red-link">Close</a>
        </div>
        </div>
    </div>
    <div id='licensePopupOpenExpiredInThirtyDay' class="">
        <div class="panel panel-default panel-shadowless mn height_auto">
            <div class="panel-heading br-b height_auto p-b-10">
                <h3 class="text-action m-t-n fw600"><a href="javascript:void(0)" class="fw500 text-black"><i class="text-action material-icons v-align-middle fs20" style="font-size: inherit;">info</i> License Expiring</a></h3>
            </div>
            <div class="panel-body bg_light_gray height_auto p-b-10">
                <p class="text-center ">
                    One or more license will expire in next <span id="license_day">30</span> days.Click the update<br>
                    button below to manage your license(s).
                </p>
                
            </div>
            <p class="text-center p-t-10">
                <input type="checkbox" name="license_expired" id="dont_show_license_expired"  onclick="update_do_not_show($(this),'license_expiring')">
                &nbsp;Don't show this again
            </p>
            <div class="text-center p-t-10" >
                <a href="profile.php" class="btn btn-action" id="update_license_expired">Update</a>
                <a href="javascript:void(0);" onclick='$.colorbox.close(this); return false;' class="btn red-link">Close</a>
            </div>
        </div>
    </div>
    <div id='EOPopupOpenExpired' class="">
        <div class="panel panel-default panel-shadowless mn height_auto">
            <div class="panel-heading br-b height_auto p-b-10">
                <h3 class="text-action m-t-n fw600"><a href="javascript:void(0)" class="fw500 text-black"><i class="text-action material-icons v-align-middle fs20" style="font-size: inherit;">info</i> E&O Coverage Expired</a></h3>
            </div>
            <div class="panel-body table_light_danger height_auto p-b-10">
                <p class="text-center ">
                    Your E&O Coverage have expired and requires your attention to enroll new<br>
                    members. Click the update button below to manage your E&O Coverage.
                </p>
            </div>
            <br />
            <p class="text-center p-t-10">
                <input type="checkbox" name="license_expired" id="dont_show_license_expired"  onclick="update_do_not_show($(this),'eo_expired')">
                &nbsp;Don't show this again
            </p>
            <div class="text-center p-t-10" >
                <a href="profile.php" class="btn btn-action" id="update_license_expired">Update</a>
                <a href="javascript:void(0);" onclick='$.colorbox.close(this); return false;' class="btn red-link">Close</a>
            </div>
        </div>
    </div>
    <div id='EOPopupOpenExpiredInThirtyDay' class="">
        <div class="panel panel-default panel-shadowless mn height_auto">
            <div class="panel-heading br-b height_auto p-b-10">
                <h3 class="text-action m-t-n fw600"><a href="javascript:void(0)" class="fw500 text-black"><i class="text-action material-icons v-align-middle fs20" style="font-size: inherit;">info</i>  E&O Coverage Expiring</a></h3>
            </div>
            <div class="panel-body bg_light_gray height_auto p-b-10">
                <p class="text-center ">
                    Your E&O Coverage will expire in the next <span id="eo_day">30</span> days. Click the update button<br>
                    below to manage your E&O Coverage.
                </p>
                
            </div>
            <br />
            <p class="text-center p-t-10">
                <input type="checkbox" name="license_expired_thirty" id="dont_show_license_expired_thirty" onclick="update_do_not_show($(this),'eo_expiring')">
                &nbsp;Don't show this again
            </p>
            <div class="text-center p-t-10" >
                <a href="profile.php" class="btn btn-action" id="update_license_expired">Update</a>
                <a href="javascript:void(0);" onclick='$.colorbox.close(this); return false;' class="btn red-link">Close</a>
            </div>
        </div>
    </div>
    <?php 
    $showLicenseRejectedModal = false;
    $rejection_text = '';
    $rejection_data = $pdo->selectOne("SELECT license_reject_status,license_reject_text FROM customer_settings WHERE customer_id=:id",array(":id"=>$_SESSION["agents"]["id"]));
    if(!empty($rejection_data['license_reject_status']) && !empty($rejection_data['license_reject_text']) && $rejection_data['license_reject_status'] == "Y") {
        $rejection_text = $rejection_data['license_reject_text'];
        
        $rejection_license_row = $pdo->selectOne("SELECT id FROM agent_license WHERE is_rejected='Y' AND agent_id=:agent_id AND is_deleted='N'",array(":agent_id"=>$_SESSION["agents"]["id"]));
        if(!empty($rejection_license_row)) {
            $showLicenseRejectedModal = true;    
        }
    }
    
    ?>
    <div id='licenseRejectedModal' class="">
        <div class="panel panel-default panel-shadowless mn height_auto">
            <div class="panel-heading br-b height_auto p-b-10">
                <h3 class="text-action m-t-n fw600"><a href="javascript:void(0)" class="fw500 text-black"><i class="text-action material-icons v-align-middle fs20" style="font-size: inherit;">info</i> License Approval Rejected</a></h3>
            </div>
            <div class="panel-body bg_light_gray height_auto p-b-10"><?=$rejection_text;?></div>
            <br/>
            <div class="text-center p-t-10" >
                <a href="javascript:void(0);" class="btn btn-action" id="updateRejectedLicense">Update License(s)</a>
                <a href="javascript:void(0);" class="btn red-link" id="rejectModalClose">Close</a>
            </div>
        </div>
    </div>
</div>
<?php
if ((!empty($_REQUEST['branding_popup']) || 1)) {
    $id = $_SESSION['agents']['id'];
    $agents_sql = "SELECT * FROM customer WHERE id=:id";
    $agents_row = $pdo->selectOne($agents_sql, array(":id" => $id));

    if (empty($_SESSION['agents']['show_branding_popup'])) {
        // $_SESSION['agents']['show_branding_popup'] = $agents_row['show_branding_popup'];
    }

    if (!empty($_SESSION['agents']['show_branding_popup']) && $_SESSION['agents']['show_branding_popup'] == "Y") {
        ?>
        <div id="branding_popup" class="modal fade welcome_modal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-body text-center p-l-10">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="m-b-20">
                                    <img src="<?= $AGENT_HOST ?>/images/logo.png<?= $cache ?>" height="80px">
                                </div>
                                <div class="branding-text">
                                    <h4 class="m-t-30">
                                        Welcome, <?= (!empty($businessNameHeader)) ? $businessNameHeader : $_SESSION['agents']['fname'] . " " . $_SESSION['agents']['lname'] ?></h4>
                                    <p class="m-t-20">
                                        Notice something new? We are making some enhancements and rebranding of CyberX
                                        Group, the marketing and technology system serving Agentra Healthcare, Bright
                                        Idea Dental, and MyHealthPass, etc... Rest assured you are in the right place
                                        and will continue to see new and exciting features.
                                    </p>
                                    <p>
                                        If you experience any issues, please do not hesitate to contact us
                                        at <a href="mailto:agents@awisplatform.com">agents@awisplatform.com</a> or <a
                                                href="tel:2087190164">208-719-0164</a>.
                                    </p>
                                </div>
                                <div class="branding-footer-action">
                                    <button class="btn btn-info" id="close_window_branding_popup">Close Window</button>
                                    <div class="clearfix"></div>
                                    <div class="pull-right m-t-20">
                                        <label>
                                            <input type="checkbox" id="chk_do_not_show_branding_popup" value="Y">
                                            Please do not show this message again
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            var other_popup_id = '';
            $(function () {
                $("#branding_popup").modal({backdrop: 'static', keyboard: false});
            });
            $(document).on("click", "#close_window_branding_popup", function () {
                if ($("#chk_do_not_show_branding_popup").prop('checked')) {
                    var show_branding_popup = 'N';
                } else {
                    var show_branding_popup = 'Y';
                }

                $.ajax({
                    url: 'close_branding_popup.php',
                    data: {show_branding_popup: show_branding_popup},
                    dataType: 'json'
                }).done(function () {
                    $("#branding_popup").modal("hide");
                    if (other_popup_id !== '') {
                        $("#" + other_popup_id).modal({backdrop: 'static', keyboard: false});
                    }
                });
            });

        </script>
    <?php }
}
$EOPopupOpenExpired = $EOPopupOpenExpiredInThirtyDay = $licensePopupOpenExpired = $licensePopupOpenExpiredInThirtyDay = false;
$license_day = $eo_day = 0;
if ($popup_open_res) {
    if (basename($_SERVER['PHP_SELF']) != "agent_contract_remaining.php") {
        redirect('agent_contract_remaining.php');
    }
}else if(in_array($_SESSION["agents"]["status"], array("Contracted", "Active"))){
    // For check license expiratio
    $licese_expiration = checkLicenseExpiredOrNot($_SESSION["agents"]["id"]);
    if (!empty($licese_expiration)  && $licese_expiration['expired'] == 'expired' && $_SESSION["agents"]["not_show_license_expired"] =='N') {
        $licensePopupOpenExpired = true;
    }else if(!empty($licese_expiration)  && $licese_expiration['expired'] == 'expire_in_30_days' && $_SESSION["agents"]["not_show_license_expiring"] =='N'){
        $licensePopupOpenExpiredInThirtyDay = true;
        $license_day = $licese_expiration['days'];
    }
    // For Check E&O Exiration 
    $eo_expiration = checkEOExpiredOrNot($_SESSION["agents"]["id"]);
    if (!empty($eo_expiration) && $eo_expiration['expired'] == 'expired' && $_SESSION["agents"]["not_show_eo_expired"] == 'N') {
        $EOPopupOpenExpired = true;
    }else if(!empty($eo_expiration) && $eo_expiration['expired'] == 'expire_in_30_days' && $_SESSION["agents"]["not_show_eo_expiring"] == 'N'){
        $EOPopupOpenExpiredInThirtyDay = true;
        $eo_day = $eo_expiration['days'];
    }

}
//  else if (in_array($_SESSION["agents"]["status"], array("Contracted", "Active")) && !in_array(basename($_SERVER['PHP_SELF']), array("product_contract.php", "recontract.php"))) {
//     //for recontract or product and renew recontract
//     // if (checkRecontractRequired($_SESSION["agents"]["id"]) > 0) {
//     //  if (!isset($_SESSION["agents"]["skip"])) {
//     //    redirect("product_contract.php");
//     //  } else {
//     //    $licensePopupOpen = true;
//     //  }
//     // } else if (checkRenewRecontractRequired($_SESSION["agents"]["id"])) {
//     //  if (!isset($_SESSION["agents"]["skip"])) {
//     //    redirect("recontract.php");
//     //  } else {
//     //    $licensePopupOpen = true;
//     //  }
//     // } else {
//     //  $licensePopupOpen = true;
//     // }
// } else if (in_array($_SESSION["agents"]["status"], array("Contracted", "Active")) && in_array(basename($_SERVER['PHP_SELF']), array("product_contract.php", "recontract.php"))) {
//     $licensePopupOpen = false;
// } else {
//     $licensePopupOpen = true;
// }
?>

<script type="text/javascript">
    $(document).ready(function () {
        /*Header menu active js */
        var e = window.location,
        i = $("#menu-ul li a").filter(function() {
            return this.href == e || 0 == e.href.indexOf(this.href)
        }).addClass("active").parent().parent().parent();
        i.is("li") && i.addClass("active")

        <?php if (agent_has_menu_access(6)) { ?>
        $("#search-icon").popover({
            html : true,
            trigger : 'click',
            template : '<div class="popover searchpopover" role="tooltip"><div class="arrow"></div><div class="popover-content"></div></div>',
            content: function() {
                str = '<div id="searchbar_wrap_small">';
                          str += '<form method="GET" action="global_search.php" id="global_search_small" name="global_search_small" role="search" class="app-search ">';
                            str += '<div class="search-group">';
                          str += '<input type="text" name="gsearch" id="gsearch_small" placeholder="Search..." size="" class="form-control gsearch">';

                str +=  '<input type="hidden" name="is_ajaxed" id="g_is_ajaxed_small" value="false" />';
                str +=  '<input type="hidden" name="pages" id="g_per_pages_small" value="<?= isset($per_page)?$per_page:0;?>" />';
                str +=  '<input type="hidden" name="sort" id="g_sort_column_small" value="<?=isset($SortBy) ? $SortBy:"";?>" />';
                str +=  '<input type="hidden" name="direction" id="g_sort_direction_small" value="<?=isset($SortDirection)?$SortDirection:"";?>" />';
                str +=  '<input type="hidden" name="type" id="g_type_small" value="" />';
                str +=  '<input type="hidden" name="rep_id" id="g_rep_id_small" value="" />';
                str +=  '<input type="hidden" name="fname" id="g_fname_small" value="" />';
                str +=  '<input type="hidden" name="email" id="g_email_small" value="" />';
                str +=  '<input type="hidden" name="custom_date" id="g_custom_date_small" value="" />';
                str +=  '<input type="hidden" name="fromdate" id="g_fromdate_small" value="" />';
                str +=  '<input type="hidden" name="todate" id="g_todate_small" value="" />' ;
                str +=  '<button type="submit" id="all_search_small"><i class="fa fa-search"></i></button>';
                str +=  '</div>';
                str +=  '</form>';
                str +=  '</div>';
                return str;
            },
            placement: 'bottom',
            container: 'body'
        }); 
        <?php } ?>

        $('html').on('click', function (e) {
             $('#search-icon').each(function () {
                 if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                    $(this).popover('hide');
                 }
             });
        });

        $("#g_search").click(function () {
            $('#g_search').attr('placeholder', 'Type Name, Id, Email Or Phone to search');
        });

        $(document).on('click', '#all_search', function () {
            //var g_search = $("#gsearch").val();
            
            var g_search = $("#gsearch").val();
            if (g_search == "" || g_search == null) {
                alert("Enter Name,Id,Email Or Phone to search user");
                return false;
            } else {
                $("#g_is_ajaxed").val("0");
                $("#gsearch").val(g_search);
                $("#global_search").submit();
            }

        });
        $(document).on('click', '#all_search_small', function () {
            //var g_search = $("#gsearch").val();
            
            var g_search = $("#gsearch_small").val();
            if (g_search == "" || g_search == null) {
                alert("Enter Name,Id,Email Or Phone to search user");
                return false;
            } else {
                $("#g_is_ajaxed").val("0");
                $("#gsearch").val(g_search);
                $("#global_search").submit();
            }

        });
        $(".agent_enrollment_urls").colorbox({
            iframe: true,
            width: "780px",
            height: "450px"
        });
        $(".btn_personal_branding").colorbox({iframe: true, width: '620px', height: '580px'});
        $(".commission_popup").colorbox({iframe: true, width: '1200px', height: '600px'});
        $(document).on("click", ".btn_share_website", function () {
            var id = $(this).attr('data-id');
            var site_name = $(this).attr('data-site_name');
            var site_url = $(this).attr('data-site_url');
            $(".btn_email_share_website").attr("href","share_website.php?sent_via=email&id="+id);
            $(".btn_sms_share_website").attr("href","share_website.php?sent_via=text&id="+id);
            //$("#swm_site_name").html(site_name);
            $("#swm_site_url").val(site_url);
            $('#share_website_model').modal('show');
        });        
        $(".btn_email_share_website").colorbox({iframe: true, width: '768px', height: '600px'});
        $(".btn_sms_share_website").colorbox({iframe: true, width: '768px', height: '590px'});

        var swm_copy_site_url = new Clipboard('#swm_copy_site_url');
        swm_copy_site_url.on('success', function (e) {
            setNotifySuccess("Link Copied!");
        });

        <?php 

        if ($licensePopupOpenExpired && !$EOPopupOpenExpired && !$EOPopupOpenExpiredInThirtyDay) { ?>
            $.colorbox({
                href:"#licensePopupOpenExpired",
                inline:true,
                width:"530px;",
                height:"330px;",
                onClosed:function(e){
                    update_do_not_show('','license_expired');
                    showLicenseRejectedModal();
                }
            });
        <?php }else if($licensePopupOpenExpiredInThirtyDay && !$EOPopupOpenExpired && !$EOPopupOpenExpiredInThirtyDay){ ?>
            $("#license_day").text(<?=$license_day?>);
            $.colorbox({
                href:"#licensePopupOpenExpiredInThirtyDay",
                inline:true,
                width:"530px;",
                height:"280px;",
                onClosed:function(e){
                    update_do_not_show('','license_expiring');
                    showLicenseRejectedModal();
                }
            });
        <?php }

        //for license popup code
        if ($licensePopupOpenExpired && $EOPopupOpenExpiredInThirtyDay) { ?>
            $.colorbox({
                href:"#licensePopupOpenExpired",
                inline:true,
                width:"530px;",
                height:"330px;",
                onClosed:function(e){
                    update_do_not_show('','license_expired');
                    EOPopupOpenExpiredInThirtyDayPopup();
                }
            });
        <?php }else if($licensePopupOpenExpiredInThirtyDay && $EOPopupOpenExpiredInThirtyDay){ ?>
            $("#license_day").text(<?=$license_day?>);
            $.colorbox({
                href:"#licensePopupOpenExpiredInThirtyDay",
                inline:true,
                width:"530px;",
                height:"280px;",
                onClosed:function(e){
                    update_do_not_show('','license_expiring');
                    EOPopupOpenExpiredInThirtyDayPopup();
                }
            });
        <?php }

        if ($licensePopupOpenExpired && $EOPopupOpenExpired) { ?>
            $.colorbox({
                href:"#licensePopupOpenExpired",
                inline:true,
                width:"530px;",
                height:"330px;",
                onClosed:function(e){
                    update_do_not_show('','license_expired');
                    EOPopupOpenExpiredPopup();
                }
            });
        <?php }else if($licensePopupOpenExpiredInThirtyDay && $EOPopupOpenExpired){ ?>
            $("#license_day").text(<?=$license_day?>);
            $.colorbox({
                href:"#licensePopupOpenExpiredInThirtyDay",
                inline:true,
                width:"530px;",
                height:"280px;",
                onClosed:function(e){
                    update_do_not_show('','license_expiring');
                    EOPopupOpenExpiredPopup();
                }
            });
        <?php }

        //for E&O popup code
        if ($EOPopupOpenExpired && !$licensePopupOpenExpired && !$licensePopupOpenExpiredInThirtyDay) { ?>
            EOPopupOpenExpiredPopup();
        <?php } else if($EOPopupOpenExpiredInThirtyDay && !$licensePopupOpenExpired && !$licensePopupOpenExpiredInThirtyDay){ ?>
            EOPopupOpenExpiredInThirtyDayPopup();
        <?php } ?>

        <?php if(!$EOPopupOpenExpired && !$EOPopupOpenExpiredInThirtyDay && !$licensePopupOpenExpired && !$licensePopupOpenExpiredInThirtyDay) { ?>
            showLicenseRejectedModal();
        <?php } ?> 

        $(document).off('click','#rejectModalClose');
        $(document).on('click','#rejectModalClose',function(){
            swal({
                text: "<br/>Closing without updating license(s) will result in last submission of updated license(s) to be removed.",
                showCancelButton: true,
                confirmButtonText: 'Do Not Update',
                cancelButtonText: 'Back'
            }).then(function () {
                $.ajax({
                    url: 'ajax_remove_pending_license.php',
                    type:'POST',
                    dataType : 'json',
                    success:function(res){
                        parent.window.location.reload();
                    },
                });
            }, function (dismiss) {
                
            });
        });

        $(document).off('click','#updateRejectedLicense');
        $(document).on('click','#updateRejectedLicense',function(){
            $.colorbox({
                href: "update_rejected_license.php",
                iframe: true,
                width: '700px',
                height: '500px'
            });
        });

        $(document).bind('cbox_open', function() {
            $('body').css({overflow: 'hidden'});
        }).bind('cbox_closed', function() {
            $('body').css({overflow: ''});
        });
    });

    function EOPopupOpenExpiredInThirtyDayPopup(){
        $("#eo_day").text(<?=$eo_day?>);
        $.colorbox({
            href:"#EOPopupOpenExpiredInThirtyDay",
            inline:true,
            width:"530px;",
            height:"330px;",
            onClosed:function(e){
                update_do_not_show('','eo_expiring');
                showLicenseRejectedModal();
            }
        });
    }

    function EOPopupOpenExpiredPopup(){
        $.colorbox({
            href:"#EOPopupOpenExpired",
            inline:true,
            width:"530px;",
            height:"330px;",
            onClosed:function(e){
                update_do_not_show('','eo_expired');
                showLicenseRejectedModal();
            }
        });
    }

    function showLicenseRejectedModal(){
        <?php if($showLicenseRejectedModal == true) { ?>
        $.colorbox({
            href:"#licenseRejectedModal",
            inline:true,
            width:"550px;",
            height:"380px;",
            onClosed:function(e){
                
            }
        });
        <?php } ?>
    }

    function update_do_not_show(element,type){
        if(element !== '')
            var val = element.val();
        var do_not_show = type;
        if(element !== '' && element.is(":checked") === true && do_not_show !== ''){
            $.ajax({
                url: 'do_not_show.php',
                data: {dont_show_chk: 'Y',type:do_not_show},
                type:'post',
                dataType : 'json',
                success:function(res){
                    parent.$.colorbox.close(this);
                },
            });
        }else{
            $.ajax({
                url: 'do_not_show.php',
                data: {dont_show_chk: 'N',type:do_not_show},
                type:'post',
                dataType : 'json',
                success:function(res){
                    $.colorbox.close(this);
                },
            });
        }
    }
</script>
<!-- For notification start-->
<script type="text/javascript">
  trigger = function(e, r, i) {
    "undefined" == typeof i && (i = "click"), $(document).off(i, e), $(document).on(i, e, function(e) {
      r($(this), e)
    })
  };
  $isProcessing=0;
  $isQuoteProcessing=0;
  $isEnrollProcessing=0;
  trigger('#noti_bell', function() {
    $.ajax({
        url: "<?=$AGENT_HOST?>/getnotification.php?noti=1",
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'noti'
        },
      })
      .done(function(data) {
        if ($(".headerAllNotification").length > 0) {
          if (data.code == 200) {
            $(".headerAllNotification .noti_list").html(data.html);
            $(".headerAllNotification").mCustomScrollbar({
              theme:"dark",
              mouseWheel:{ preventDefault: true ,scrollAmount:150},
              callbacks: {
                // onScrollStart:function(){ console.log("on start"); },
                // onScroll:function(){console.log("on scroll"); },
                onTotalScroll:function(){ loadMoreData() },
                // onTotalScrollBack:function(){ console.log("on total scroll back"); },
                onTotalScrollOffset:100,
                // onTotalScrollBackOffset:20,
                // whileScrolling:function(e){ mcs }
              }
            });
            //$('#not_counter').html('');
          }
        }
      });
  });

  loadMoreData=function(){
    $(".notification_loader").show();
    if($isProcessing==0){
      $isProcessing=1;
      // $(".notification_loader_btn").hide();
      $lastId=$(".headerAllNotification .noti_list").find(".notification_full").last().attr("data-noti");
      if($(".headerAllNotification").attr("data-count")<0) {
        $(".notification_loader").hide();
        return;
      }
      $.ajax({
        url: "<?=$AGENT_HOST?>/getnotification.php?loadmore=1",
        type: 'POST',
        dataType: 'json',
        beforeSend:function(){
          // $(".notification_loader_btn").hide();
          $(".notification_loader").show();
          console.log("Show Loader");
        },
        data: {
          action: 'noti_remaining',
          lid:$lastId//pass last id to ajax
        },
      }).done(function(data) {
        $isProcessing=0;
        // $(".notification_loader_btn").show();
        if(data.code==200){
          if(data.count>0){
            $(".headerAllNotification .noti_list").append(data.html);
          }
          if(data.count<data.limit){
            $(".headerAllNotification").attr("data-count",-1);
            // $(".notification_loader_btn").hide();
          }
         }
         setTimeout(function(){
          $(".notification_loader").hide();
         },2000);
         console.log("Hide Loader");
      });
    }
  };
  trigger(".redirectNotification",function($this,e){
    $ref=$this.parents(".notification_full");
    $href=$ref.attr("data-href");
    if ($href.indexOf("license_expiration_notification") > 0) {
      $("#requestNewLicense").modal("show");
      handler("opennotification.php","noti_id="+$ref.attr("data-noti"),function(){})
    }else{
      if($ref.attr("data-colorbox")){
        $.colorbox({
            href: $href,
            iframe: true,
            width: '900px',
            height: '550px'
        });
      }else{
        window.location.href=$href;
      }
    }
  });
  trigger(".clear_all_noti",function($this,e){
    e.stopPropagation();
    $("#not_counter").html('');
    $(".headerAllNotification .noti_list").html('<p class="text-center">You\'re all caught up and have no alerts.</p>');
    $.ajax({
        url: "<?=$AGENT_HOST?>/getnotification.php?allopen=1",
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'allClear'
        },
      })
    .done(function(data) {
        $(".notification_loader").hide();
    });
  });
  trigger('ul.dropdown-menu.mailbox .remove_notification,#dashboard_alert .remove_notification', function($this,e) {
    e.stopPropagation();
    if ($(".headerAllNotification").length > 0) {
        $this.parent(".notification_full").fadeOut("slow",function(){
            if($(".headerAllNotification").find(".notification_full").length==0){
                $(".headerAllNotification .noti_list").append('<p class="text-center">You\'re all caught up and have no alerts.</p>');
                $("#not_counter").html('');
            } else {
                var not_counter = $("#not_counter").html();
                not_counter -= 1;
                if(not_counter < 1) {
                    $(".headerAllNotification .noti_list").append('<p class="text-center">You\'re all caught up and have no alerts.</p>');
                    $("#not_counter").html('')
                } else {
                    $("#not_counter").html(not_counter)    
                }
                
            }
        });
    }
    $.ajax({
        url: "<?=$AGENT_HOST?>/getnotification.php?hidenoti=1",
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'clearNoti',
          id:$this.parents(".notification_full").attr("data-noti")
        },
      })
      .done(function(data) {
        
      });
  });

  $(function(){
    getNoti();
    setInterval(getNoti,20000);
  });

  $ajaxCallNoti=true;
  getNoti=function(){
    if($ajaxCallNoti==false){
      return;
    }
    $.ajax({
      url: "<?=$AGENT_HOST?>/getnotification.php?get=1",
      type: 'POST',
      dataType: 'json',
      beforeSend:function(){
        $ajaxCallNoti=false;
      },
      data: {
        action: 'get'
      },
    })
    .done(function(data) {
      $ajaxCallNoti=true;
      if ($(".headerAllNotification").length > 0) {
        if (data.code == 200) {
          $('#not_counter').html(data.notificationCount>0?data.notificationCount:"");
        }else if(data.code==100){
          window.location.href="<?=$AGENT_HOST?>";
        }
      }
    });
  };
</script>