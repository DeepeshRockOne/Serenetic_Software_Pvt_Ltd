<div class="support_areabox">
   <div class="row">
      <?php if (has_menu_access(80)) { ?>
      <div class="<?=$display_class?>">
         <div class="support_box cyan_bg">
            <a href="etickets.php">
               <p class="mn fs18"><strong>eTickets</strong></p>
               <p class="mn fs14">Click to manage eTickets</p>
            </a>
         </div>
      </div>
      <?php } ?>
      <?php /*?><?php if (has_menu_access(81)) {?>
      <div class="<?=$display_class?>">
         <div class="support_box danger_bg">
            <a href="live_chat_dashboard.php">
               <p class="mn fs18"><strong>Live Chat</strong></p>
               <p class="mn fs14">Click to manage live chat</p>
            </a>
         </div>
      </div>
      <?php } ?><?php */?>
      <?php if (has_menu_access(11)) {?>
      <div class="<?=$display_class?>">
         <div class="support_box blue_bg">
            <a href="all_orders.php">
               <p class="mn fs18"><strong>Orders</strong></p>
               <p class="mn fs14">Click to manage orders</p>
            </a>
         </div>
      </div>
      <?php } ?>
   </div>
</div>
<div class="panel panel-default panel-block support">
   <div class="support_search searchdiv">
      <div class="row">
         <div class="col-md-6 col-md-offset-3 searchdiv m-b-30">
            <form method="GET" action="support_dashboard.php" id="all_usersFrm" name="all_usersFrm" role="search" class=" all_user_srch p-l-15 p-r-15">
               <h3 class="m-b-20 text-center fw500 fs18 m-t-30">Search by business, name, email, or phone</h3>
               <div class="phone-control-wrap">
                  <div class="phone-addon" >
                     <div class="input-group">
                        <input type="text" tabindex="-1" autofocus="autofocus" name="gsearch" id="gsearch" placeholder="Search" size="5" class="form-control" value="<?=!empty($_REQUEST['gsearch'])?trim($_REQUEST['gsearch']):'';?>">
                        <div class="input-group-btn">
                           <button type="submit" class="btn btn-info btn-outline" ><i class="fa fa-search"></i></button>
                        </div>
                     </div>
                  </div>
                  <div class="phone-addon w-70" >
                     <button type="button" class="btn btn-action btn-block" id="btn_clear" >Clear</button>
                  </div>
               </div>
               <input type="hidden" name="is_ajaxed" id="g_is_ajaxed" value="false" />
               <input type="hidden" name="type" id="g_type" value="" />
               <input type="hidden" name="rep_id" id="g_rep_id" value="" />
               <input type="hidden" name="fname" id="g_fname" value="" />
               <input type="hidden" name="email" id="g_email" value="" />
               <input type="hidden" name="custom_date" id="g_custom_date" value="" />
               <input type="hidden" name="fromdate" id="g_fromdate" value="" />
               <input type="hidden" name="todate" id="g_todate" value="" /> 
            </form>
         </div>
      </div>
      <?php if($gsearch){ ?>
        <div class="preface">
           <div class="tabbing-tab">
              <ul class="nav nav-tabs customtab" id="gschtab">
                 <?php if (has_menu_access(3)) {?>       
                 <li><a class="gsch_page" id="admin_li" href="global_admins.php"  data-counter="<?= $total_admins; ?>">Admins (<?= $total_admins; ?>)</a></li>
                 <?php } ?>
                 <?php if (has_menu_access(5)) {?>
                 <li><a class="gsch_page" href="global_agents.php" data-counter="<?= $total_agents; ?>">Agents (<?= $total_agents; ?>)</a></li>
                 <?php } ?>
                 <?php if (has_menu_access(6)) {?>
                 <li><a class="gsch_page" href="global_groups.php" data-counter="<?= $total_groups; ?>">Groups (<?= $total_groups; ?>)</a></li>
                 <?php } ?>
                 <?php if (has_menu_access(7)) {?>
                 <li><a class="gsch_page" href="global_leads.php" data-counter="<?= $total_leads; ?>">Leads (<?= $total_leads; ?>)</a></li>
                 <?php } ?>
                 <?php if (has_menu_access(8)) {?>
                 <li><a class="gsch_page" href="global_customers.php" data-counter="<?= $total_customers; ?>">Members (<?= $total_customers; ?>)</a></li>
                 <?php } ?>
                 <?php if (has_menu_access(89)) {?>
                 <li><a class="gsch_page" href="global_participants.php" id="participants_id" data-counter="<?= $total_participants; ?>">Participants (<?= $total_participants; ?>)</a></li>
                 <?php } ?>
                 
              </ul>
           </div>
        </div>
      <?php } ?>
   </div>
   <?php if($gsearch){ ?>
     <?php if ($total_admins > 0 || $total_agents > 0  || $total_groups > 0 || $total_customers > 0  || $total_leads > 0 || $total_users > 0 || $total_participants > 0) { ?>
     <div class="tab-content mn">
        <div id="global_search_result" class="tab-pane active">
        </div>
     </div>
     <?php } else { ?>
     <div class="panel-body">
        <div id="sparkline-boxes">
           <h4><i class="icon-warning-sign"></i> &nbsp; No Results Found.</h4>
        </div>
     </div>
     <?php } ?>
   <?php } ?>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    $('.nav-tabs').scrollingTabs('refresh');
    $('#grefresh').click(function() {
      window.location = 'dashboard.php';
    });
    $('#grefresh').show();
    var $obj=null;
    $(document).off('click','a.gsch_page');
    $(document).on('click','a.gsch_page', function(e) {
      e.preventDefault();
      $obj = $(this);
      var gsearch = '<?php echo isset($_GET['gsearch'])?$_GET['gsearch']:"";?>';
      $.ajax({
        url: $obj.attr("href"),
        type: 'GET',
        beforeSend:function(){
          $("#ajax_loader").show();
        },
        data: {gsearch: gsearch},
        success: function(res) {
         $("#ajax_loader").hide();
         $("a.gsch_page").removeClass("active");
         // $obj.addClass("active");
         $("a.gsch_page[href='"+$obj.attr("href")+"']").addClass("active")
         $('#global_search_result').html(res).fadeIn('slow');
        }
      });
    });
    var flg=false;
    $('a.gsch_page').each(function(k,v){
        var counter = $(this).attr('data-counter');
  
        if(parseInt(counter) > 0 && flg==false){
          flg=true;
          $(this).trigger('click');  
        }
    }); 

    $(document).off('click','#btn_clear');
    $(document).on('click','#btn_clear', function(e) {
      window.location = 'support_dashboard.php';
    });
  });
if ($(window).width() <= 991) {
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