<?php
$id = $_SESSION['admin']['id'];
$admin_sql = "select * from admin where id=$id";
$admin_res = $pdo->selectOne($admin_sql);
$profile = $admin_res['photo'];
?>

<div class="navbar-default sidebar" role="navigation">
  <div class="sidebar-nav navbar-collapse slimscrollsidebar">
    
    <ul id="side-menu" class="nav in">
      <?php if($_SESSION['admin']['type'] !='Call Center Executives'){ ?>      
           
      <li> 
        <?php  $link = 'dashboard.php'; ?>
        <a href="<?=$link;?>" class="waves-effect <?= !empty($dashboard) ? $dashboard : '' ?>"><i class="fa fa-fw"><img src="<?=$HOST?>/images/menu_icon/dashboard.svg" width="15px" height="15px"></i> <span class="hide-menu"> Dashboard</span></a>
      </li>
      <?php } ?>
      <?php if (has_menu_access(1)) {?>
        <li> <a href="javascript:void(0);" class="waves-effect <?= !empty($user_groups) ? $user_groups : '' ?>"><i class="fa fa-fw"><img src="<?=$HOST?>/images/menu_icon/user_group.svg" width="15px" height="15px"></i> <span class="hide-menu">User Groups <span class="fa arrow"></span></span></a>
          <ul class="nav nav-second-level">
          <?php if (has_menu_access(2)) {?><li><a href="all_users.php">All Users</a></li><?php }?>
            <?php if (has_menu_access(3)) {?><li><a href="admins.php">Admins</a></li><?php }?>
            <?php if (has_menu_access(5)) {?><li><a href="agent_listing.php">Agents</a></li> <?php }?>
            <?php if (has_menu_access(6)) {?><li><a href="groups_listing.php">Groups</a></li> <?php }?>
            <!-- <li><a href="groups_listing.php">Groups</a></li> -->
            <?php  if (has_menu_access(7)) {?><li><a href="lead_listing.php">Leads</a></li><?php } ?>
            <?php if (has_menu_access(8)) {?><li><a href="member_listing.php">Members</a></li><?php }?>
            <?php  if (has_menu_access(89)) {?><li><a href="participants_listing.php">Participants</a></li><?php } ?>
          </ul>
        </li>
      <?php }?>
      <?php if (has_menu_access(9)) {?>
        <li> <a href="javascript:void(0);" class="waves-effect <?= !empty($golbal_communications) ? $golbal_communications : '' ?>"><i class="fa fa-fw"><img src="<?=$HOST?>/images/menu_icon/comunications.svg" width="15px" height="15px"></i> <span class="hide-menu">Communications <span class="fa arrow"></span></span> </a>
          <ul class="nav nav-second-level">
            <?php if (has_menu_access(66)) {?><li><a href="communication_circle.php">Circles</a></li><?php }?>
            <?php if (has_menu_access(10)) {?><li><a href="emailer_dashboard.php">Emails</a></li><?php }?>
            <?php if (has_menu_access(58)) {?><li><a href="sms_dashboard.php">Text Messages</a></li><?php }?>
            <?php if (has_menu_access(26)) {?><li><a href="interaction_dashboard.php">Interactions</a></li><?php }?>
            <?php if (has_menu_access(67)) {?><li><a href="unsubscribes_dashboard.php">Unsubscribes</a></li><?php }?>
          </ul>
        </li>
      <?php }?>
      <?php if (has_menu_access(15)) {?>
        <li> <a href="javascript:void(0);" class="waves-effect <?= !empty($manage_products) ? $manage_products : '' ?>"><i class="fa fa-fw"><img src="<?=$HOST?>/images/menu_icon/products.svg" width="15px" height="15px"></i> <span class="hide-menu">Products <span class="fa arrow"></span></span> </a>
          <ul class="nav nav-second-level">
          <?php if (has_menu_access(17)) {?><li><a href="manage_product.php">Builder</a></li><?php } ?>
            <?php if (has_menu_access(54)) {?><li><a href="carrier.php">Carriers</a></li><?php } ?>
            <?php if (has_menu_access(53)) {?><li><a href="memberships.php">Memberships</a></li><?php } ?>
            <?php if (has_menu_access(42)) {?><li><a href="providers.php">Providers</a></li><?php } ?>            
            <?php if (has_menu_access(69)) {?><li><a href="products_resource.php">Resources</a></li><?php } ?>
            <?php if (has_menu_access(65)) {?><li><a href="vendors.php">Vendors</a></li><?php }?>          
          </ul>
        </li>
      <?php }?>

      <?php if (has_menu_access(27)) {?>
        <li> <a href="javascript:void(0);" class="waves-effect <?= !empty($manage_commission) ? $manage_commission : '' ?>"><i class="fa fa-fw"><img src="<?=$HOST?>/images/menu_icon/commissions.svg" width="15px" height="15px"></i> <span class="hide-menu">Commissions<span class="fa arrow"></span></span> </a>
          <ul class="nav nav-second-level">
            <?php if (has_menu_access(28)) {?>
              <li><a href="commission_builder.php">Builder</a></li>
               <?php } ?>
               <?php if (has_menu_access(62)) {?>
                <li><a href="advances_commission.php">Advances</a></li>
               <?php }?>
               <?php if (has_menu_access(64)) {?>
                <li><a href="pmpm_commission.php">PMPMs</a></li>
               <?php }?>
          </ul>
        </li>
      <?php }?>

      <?php if (has_menu_access(12)) {?>
        <li> <a href="javascript:void(0);" class="waves-effect"><i class="fa fa-file fa-fw"></i> <span class="hide-menu">Payment<span class="fa arrow"></span></span> </a>
          <ul class="nav nav-second-level">
            <?php if (has_menu_access(71)) {?><li><a href="billing_files.php">Billing Files</a></li><?php }?>
            <?php if (has_menu_access(90)) {?><li><a href="agents_commissions.php">Agent Commission</a></li><?php }?>
            <?php if (has_menu_access(60)) {?><li><a href="payment_commissions.php">Commissions</a></li><?php }?>
            <?php if (has_menu_access(77)) {?><li><a href="healthy_steps.php">Healthy Steps</a></li><?php }?>
            <?php if (has_menu_access(91)) {?><li><a href="hrm_payments.php">HRM Payments</a></li><?php }?>
            <?php if (has_menu_access(49)) {?><li><a href="payment_listbills.php">List Bills</a></li><?php }?>
            <?php if (has_menu_access(78)) {?><li><a href="merchant_processor.php">Merchant Processing</a></li><?php }?>
            <?php if (has_menu_access(11)) {?><li><a href="all_orders.php">Orders</a></li><?php }?>
            <?php if (has_menu_access(88)) {?><li><a href="account_payable.php">Payables</a></li><?php }?>
            <?php if (has_menu_access(79)) {?><li><a href="payment_policies.php">Policies</a></li><?php }?>
            <?php if (has_menu_access(13)) {?><li><a href="payment_reversal.php">Reversals</a></li><?php }?>
            <?php if (has_menu_access(72)) {?><li><a href="payment_setting.php">Settings</a></li>
            <?php }?>
            <?php if (has_menu_access(73)) {?><li><a href="payment_transaction.php">Transactions</a></li><?php }?>
          </ul>
        </li>
      <?php }?>

      <?php if (has_menu_access(25)) {?>
        <li> <a href="javascript:void(0);" class="waves-effect <?= !empty($support_management) ? $support_management : '' ?>"><i class="fa fa-envelope fa-fw"><!--<img src="<?=$HOST?>/images/menu_icon/comunications.svg" width="15px" height="15px">--></i> <span class="hide-menu">Client Support <span class="fa arrow"></span></span> </a>
          <ul class="nav nav-second-level">
            <?php if (has_menu_access(80)) {?><li><a href="etickets.php">eTickets</a></li><?php }?>
            <?php if (has_menu_access(81)) {?><li><a href="live_chat_dashboard.php">Live Chat</a></li><?php }?>
            <!-- <?php if (has_menu_access(78)) {?><li><a href="javascript:void(0);">Live</a></li><?php }?> -->
            <?php if (has_menu_access(85)) { /*?><li><a href="javascript:void(0);">FAQ</a></li><?php */ }?>
            <?php if (has_menu_access(75)) {?><li><a href="resources.php">Resources</a></li><?php }?>
          </ul>
        </li>
      <?php }?>
      
    <?php if (has_menu_access(33) /*&& !in_array($_SESSION['admin']['id'],array(39,86,99,100,101,102,108,109,110))*/) {?>
        <li> <a href="javascript:void(0);" class="waves-effect <?php echo $reporting; ?>"><i class="fa fa-fw"><img src="<?=$HOST?>/images/menu_icon/reporting.svg" width="15px" height="15px"></i> <span class="hide-menu">Reporting<span class="fa arrow"></span></span> </a>
          <ul class="nav nav-second-level">
            <?php if (has_menu_access(34)) {?><li><a href="set_reports.php">Set Reports</a></li><?php }?>
            <?php if (has_menu_access(92)) {?><li><a href="active_member_of_month.php">Active Members</a></li><?php }?>
          </ul>
        </li>
      <?php }?>

      <?php if (has_menu_access(35)) {?>
        <li> <a href="javascript:void(0);" class="waves-effect <?= !empty($eligibility_files) ? $eligibility_files : '' ?>"><i class="fa fa-fw"><img src="<?=$HOST?>/images/menu_icon/eligibity.svg" width="15px" height="15px"></i> <span class="hide-menu">Eligibility <span class="fa arrow"></span></span> </a>
          <ul class="nav nav-second-level">
            <?php if (has_menu_access(82)) {?><li><a href="eligibility_generator.php">Generator</a></li><?php }?>
            <?php if (has_menu_access(37)) {?><li><a href="eligibility_history.php">History</a></li><?php }?>          
          </ul>
        </li>
      <?php }?>

      <?php if (has_menu_access(44)) {?>
        <li> <a href="javascript:void(0);" class="waves-effect <?= !empty($fullfillment) ? $fullfillment : '' ?>"><i class="fa fa-fw"><img src="<?=$HOST?>/images/menu_icon/fulfillment.svg" width="15px" height="15px"></i> <span class="hide-menu">Fulfillment<span class="fa arrow"></span></span> </a>
          <ul class="nav nav-second-level">
            <?php if (has_menu_access(83)) {?><li><a href="fulfillment_generator.php" class="<?= !empty($fullfillment) ? $fullfillment : ''; ?>">Generator</a></li><?php } ?>
            <?php if (has_menu_access(84)) {?><li><a href="fulfillment_history.php">History</a></li><?php }?>
          </ul>
        </li>
      <?php }?>

      <?php /*if (has_menu_access(74)) {?>
        <li> <a href="javascript:void(0);" class="waves-effect <?= !empty($eligibility_files) ? $eligibility_files : '' ?>"><i data-icon=")" class="fa fa-fw fa-gear"></i> <span class="hide-menu"><?= $DEFAULT_SITE_NAME ?> <span class="fa arrow"></span></span> </a>
          <ul class="nav nav-second-level">
            <?php if (has_menu_access(75)) {?><li><a href="javascript:void(0);">Resources</a></li><?php }?>
            <?php if (has_menu_access(76)) {?><li><a href="javascript:void(0);">Contact Us</a></li><?php }?>          
          </ul>
        </li>
      <?php } */?>

      <?php if (has_menu_access(74)) {?>
        <li> <a href="system_resources.php" class="waves-effect <?= !empty($eligibility_files) ? $eligibility_files : '' ?>"><i data-icon=")" class="fa fa-fw fa-gear"></i> <span class="hide-menu"><!-- <?= $DEFAULT_SITE_NAME ?> --> System</span> </a>
        </li>
      <?php } ?>
      
      <li><a href="logout.php?previous_page=<?=urlencode($_SERVER['REQUEST_URI'])?>" class="waves-effect"><i class="fa fa-fw"><img src="<?=$HOST?>/images/menu_icon/logout.svg" width="15px" height="15px"></i> <span class="hide-menu">Log out</span></a></li>
    </ul>
  </div>
</div>
