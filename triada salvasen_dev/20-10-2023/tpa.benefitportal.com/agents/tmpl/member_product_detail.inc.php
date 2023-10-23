\<div class="panel panel-default">
   <div class="panel-heading">
      <h4 class="mn">Product Details - <span class="fw300"><?=getname('prd_main',$product_id,'name','id')?></span></h4>
   </div>
   <div class="panel-body">
         <ul class="nav nav-tabs tabs customtab" role="tablist">
            <li role="presentation" class="active"><a href="#Agent_View" data-toggle="tab" aria-expanded="false">Agent View</a>
         </li>
         <li role="presentation"><a href="#Member_View" data-toggle="tab" aria-expanded="false">Member View</a>
      </li>
   </ul>
<div class="tab-content m-t-20">
   <div role="tabpanel" class="tab-pane active" id="Agent_View">
      <h3><?=getname('prd_main',$product_id,'name','id')?></h3>
      <div class="portal_prdinfo_scroll">
        <?= displayAgentPortalDescriptionInfo($product_id,'N') ?>
        <?=$agent_portal?>
      </div>
   </div>
   <div role="tabpanel" class="tab-pane" id="Member_View">
    <div class="portal_prdinfo_scroll">
      <?php include ('tmpl/product_detail_common.inc.php'); ?>
    </div>
   </div>
</div>
</div>
<div class="br-t text-center p-t-10">
   <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close();">Close</a>
</div>
</div>
<script type="text/javascript">
   $(document).ready(function(){  
      autoResizeNav();
  });
    $(document).off('click', '.id_card_popup');
    $(document).on('click', '.id_card_popup', function (e) {
       e.preventDefault();
       window.parent.$.colorbox({
         href: $(this).attr('data-href'),
         iframe: true, 
         width: '1024px', 
         height: '500px'
       });
    });
   $(document).off('click', '.view_depedents');
        $(document).on('click', '.view_depedents', function (e) {
            e.preventDefault();
            $.colorbox({
                href: $(this).attr('href'),
                iframe: true,
                width: '1024px',
                height: '350px'
            });
        });
          (function($){
            $(window).load(function(){
              $(".portal_prdinfo_scroll").mCustomScrollbar({
                axis: "y",
                theme:"dark-thick",
                scrollbarPosition: "outside",
              });
              $(".mCSB_scrollTools").before("<br>");
            });
          })(jQuery);
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