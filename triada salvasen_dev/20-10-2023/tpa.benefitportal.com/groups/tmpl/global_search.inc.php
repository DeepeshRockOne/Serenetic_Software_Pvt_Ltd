<?php include_once('notify.inc.php'); ?>
<div class="container m-t-30">
<style type="text/css">
  .col-lg-12, .col-lg-6, .col-lg-3 { padding: 0px;}
  .noEdit .form-control {background: #F9F9F9; cursor: not-allowed; color: #CECECE;}
  .noEdit .select2-container .select2-choice { background: #F9F9F9; cursor: not-allowed; color: #CECECE;}
  .fEdit .form-control {background: #fff; cursor: default; color: #555555;}
  .fEdit .select2-container .select2-choice { background: #fff; cursor: inherit; color: #555555;}
  .noEdit .readonly,.fEdit .readonly {background: #F9F9F9; cursor: not-allowed; color: #CECECE;}
</style>  
<div id="ajax_loader" style="display:none;">
  <div class="loader"></div>
</div>
<div class="searchdiv"> 
  <div class="panel panel-default"> 
      <div class="preface">
        <div class="tabbing-tab">
        <div class="clearfix"></div>
        <div class="preface_alluser m-b-20 p-t-30 clearfix">
          <div class="col-md-4 col-md-offset-4 ">
            <form method="GET" action="global_search.php" id="all_usersFrm" name="all_usersFrm" role="search" class=" all_user_srch">
              <div class="input-group">
                <input type="text" tabindex="-1" autofocus="autofocus" name="gsearch" id="gsearch" placeholder="Search" size="5" class="form-control" value="<?=!empty($_REQUEST['gsearch'])?trim($_REQUEST['gsearch']):'';?>">
               <div class="input-group-btn"><button type="submit" class=" btn btn-default" ><i class="fa fa-search"></i></button></div>
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
        <div class="clearfix"></div>
        <?php if ($total_customers > 0  || $total_leads > 0) { ?>
          <ul class="nav nav-tabs customtab" id="gschtab">
            <?php /*<li><a class="gsch_page" href="global_agents.php" data-counter="<?= $total_agents; ?>">Agents (<?= $total_agents; ?>)</a></li>
            <li><a class="gsch_page" href="global_groups.php" data-counter="<?= $total_groups; ?>">Groups (<?= $total_groups; ?>)</a></li> */ ?>
            <li><a class="gsch_page" href="global_enrollees.php" data-counter="<?= $total_leads; ?>">Enrollees (<?= $total_leads; ?>)</a></li>
            <li><a class="gsch_page" href="global_customers.php" data-counter="<?= $total_customers; ?>">Members (<?= $total_customers; ?>)</a></li>
          </ul>
        <?php } else { ?>
        <div class="panel-body " id="sparkline-boxes">
            <h4 class="mn"><i class="icon-warning-sign"></i> &nbsp; No search results found.</h4>
        </div>
        <?php } ?>
        </div>
        <div class="tab-content mn" >
            <div id="global_search_result" class="tab-pane  active ">
            </div>                      
        </div>
      </div>
  </div>
</div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    dropdown_pagination('ajax_data')
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