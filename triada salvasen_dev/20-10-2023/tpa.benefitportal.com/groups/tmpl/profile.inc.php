<script src="https://maps.googleapis.com/maps/api/js?key=<?=$GOOGLE_MAP_KEY?>&libraries=places" async defer></script>
<div class="container m-t-30">
  <div class="group_profile">
     <div class="panel panel-default panel-block">
        <div class="panel-body">
           <ul class="nav nav-tabs tabs  customtab  fixed_tab_top" role="tablist">
              <li role="presentation" class="active"><a href="#group_account_detail_div"  data-toggle="tab" class="ajax_get_group_data" onclick="scrollToDiv($('#group_account_detail_div'), 0,'group_account_detail.php','group_account_detail_div');" aria-expanded="false">Account</a></li>
              <li role="presentation" ><a href="#gp_attributes" id="data_gp_attributes" data-toggle="tab" class="ajax_get_group_data" onclick="scrollToDiv($('#gp_attributes'), 0,'group_account_detail.php','group_account_detail_div','gp_attributes');" aria-expanded="false">Attributes</a></li>

              <li role="presentation"><a href="#gp_products"  data-toggle="tab" class="ajax_get_group_data" onclick="scrollToDiv($('#gp_products'), 0,'group_products.php','gp_products');" aria-expanded="true">Products</a></li>
              

              <li role="presentation" ><a href="#gp_brand_links"  data-toggle="tab" class="ajax_get_group_data" onclick="scrollToDiv($('#gp_brand_links'), 0,'group_personal_brand_link.php','gp_brand_links');" aria-expanded="false">Personal Brand & Links</a></li>
              <?php if(!in_array($row['status'],array('Invited', 'Pending Documentation', 'Pending Approval', 'Pending Contract')))  { ?>
              <li role="presentation" ><a href="#gp_billing"  data-toggle="tab" class="ajax_get_group_data" onclick="scrollToDiv($('#gp_billing'), 0,'group_billing_profile.php','gp_billing');" aria-expanded="false">Billing</a></li>
              <?php } ?>
              <!-- <li role="presentation" ><a href="#gp_activity"  data-toggle="tab" class="ajax_get_group_data" onclick="scrollToDiv($('#gp_activity'), 0,'tmpl/activity_feed_group.inc.php','gp_activity');" aria-expanded="false">Activity History</a></li> -->
              </li>
           </ul>
           <div class="m-t-20">
              <div id="group_account_detail_div" role="tabpanel" class="tab-pane active" >
                <?php include_once './group_account_detail.php'; ?>
              </div>
              <div role="tabpanel" class="tab-pane" id="gp_products">
                  
              </div>
              <div role="tabpanel" class="tab-pane" id="gp_brand_links"></div>
              <div role="tabpanel" class="tab-pane" id="gp_billing"></div>
              <div role="tabpanel" class="tab-pane" id="gp_activity"></div>
           </div>
        </div>
     </div>
  </div>
</div>
<?php include_once 'groups_details_jquery.inc.php';?>
<script type="text/javascript">
  $(document).ready(function(){
      if ($(window).width() >= 1171) {
         $(window).scroll(function() {
         if ($(this).scrollTop() > 519) {
            $('.fixed_tab_top').addClass('fixed');
         } else {
            $('.fixed_tab_top').removeClass('fixed');
         }
         });
      }
   });

   $(function() {
        $('.group_intrection_wrap').matchHeight({
            target: $('.profile-info')
        });
   });


$(window).on('resize load', function(){
   if ($(window).width() <= 1170) {
      $('.nav-tabs:not(.nav-noscroll)').scrollingTabs('destroy');
      autoResizeNav();
   }
});

function autoResizeNav(){
   if ($('.nav-tabs:not(.nav-noscroll)').length){
      ;(function() {
        'use strict';
         $(activate);
         function activate() {
         $('.nav-tabs:not(.nav-noscroll)')
           .scrollingTabs({
               scrollToTabEdge: true,
               enableSwiping: true  
            })
        }
      }());
   }
}
</script>
