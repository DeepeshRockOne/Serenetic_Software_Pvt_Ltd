<div class="panel panel-default panel-block advance_info_div">
   <div class="panel-body">
      <div class="phone-control-wrap ">
         <div class="phone-addon w-90 v-align-top">
            <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="75px">
         </div>
         <div class="phone-addon text-left">
            <div class="info_box_max_width">
               <p class="fs14 mn">Resources are information available to you as a client to assist others with how to use the system.  You may share the attached link with the affiliated user group.</p>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="panel panel-default panel-block">
   <div class="panel-body">
      <div class="clearfix m-b-15">
         <h4 class="m-t-7">Select Portal</h4>
      </div>
      <div class="blue_arrow_tab m-b-15">
         <ul class="nav nav-tabs nav-noscroll nav-justified">
            <li class="active"><a data-toggle="tab" href="#Admin_Portal">Admin Portal</a></li>
            <li><a data-toggle="tab" href="#Agent_Portal">Agent Portal</a></li>
            <li><a data-toggle="tab" href="#Member_Portal">Member Portal</a></li>
            <li><a data-toggle="tab" href="#Group_Portal">Employer Group Portal</a></li>
         </ul>
      </div>
   </div>
</div>
<div class="panel panel-default panel-block">
   <div class="panel-body">
      <div class="tab-content mn">
         <div id="Admin_Portal" class="tab-pane fade in active">
            <?php include_once 'admin_portal_resources.inc.php'; ?>
         </div>
         <div id="Agent_Portal" class="tab-pane fade">
            <?php include_once 'agent_portal_resources.inc.php'; ?>
         </div>
         <div id="Member_Portal" class="tab-pane fade">
            <?php include_once 'member_portal_resources.inc.php'; ?>
         </div>
         <div id="Group_Portal" class="tab-pane fade">
            <?php include_once 'group_portal_resources.inc.php'; ?>
         </div>
      </div>
   </div>
</div>
<script type="text/javascript">
   //admin
   $(document).off("click", "#admin_search_btn");
   $(document).on("click", "#admin_search_btn", function(e) {
     e.preventDefault();
     $(this).hide();
     $("#admin_search_div").css('display', 'inline-block');
   });

   $(document).off("click", "#admin_search_close_btn");
   $(document).on("click", "#admin_search_close_btn", function(e) {
     e.preventDefault();
     $("#admin_search_div").hide();
     $("#admin_search_btn").show();
     searchResources('admin','admin_div','empty');
     $("#admin_search").val('').change();
   });
   //agent
   $(document).off("click", "#agent_search_btn");
   $(document).on("click", "#agent_search_btn", function(e) {
     e.preventDefault();
     $(this).hide();
     $("#agent_search_div").css('display', 'inline-block');
   });

   $(document).off("click", "#agent_search_close_btn");
   $(document).on("click", "#agent_search_close_btn", function(e) {
     e.preventDefault();
     $("#agent_search_div").hide();
     $("#agent_search_btn").show();
     searchResources('agent','agent_div','empty');
     $("#agent_search").val('').change();
   });
   //member
   $(document).off("click", "#member_search_btn");
   $(document).on("click", "#member_search_btn", function(e) {
     e.preventDefault();
     $(this).hide();
     $("#member_search_div").css('display', 'inline-block');
   });

   $(document).off("click", "#member_search_close_btn");
   $(document).on("click", "#member_search_close_btn", function(e) {
     e.preventDefault();
     $("#member_search_div").hide();
     $("#member_search_btn").show();
     searchResources('member','member_div','empty');
     $("#member_search").val('').change();
   });
   //group
   $(document).off("click", "#group_search_btn");
   $(document).on("click", "#group_search_btn", function(e) {
     e.preventDefault();
     $(this).hide();
     $("#group_search_div").css('display', 'inline-block');
   });

   $(document).off("click", "#group_search_close_btn");
   $(document).on("click", "#group_search_close_btn", function(e) {
     e.preventDefault();
     $("#group_search_div").hide();
     $("#group_search_btn").show();
     searchResources('group','group_div','empty');
     $("#group_search").val('').change();
   });
   
   $(document).off('click','.add_resources');
   $(document).on('click','.add_resources',function(e){
      e.preventDefault();
      $href = $(this).attr('data-href');
      $.colorbox({
         href:$href,
         iframe: true, 
         width: '710px', 
         height: '475px'
      });
   });

   $(document).off('click','.view_resources');
   $(document).on('click','.view_resources',function(e){
      e.preventDefault();
      $href = $(this).attr('data-href');
      $.colorbox({
         href:$href,
         iframe: true, 
         width: '515px', 
         height: '300px'
      });
   });
   function searchResources(type,place_div,empty){
      var $search_type = '';
      if(empty === ''){
         var $search_type = $("#"+type+'_search').val();
         if($search_type === ''){
            alert("Please Select Any Module");
            return false;
         }
      }
      
      $.ajax({
      url:"ajax_portal_resources_search.php",
      data : {type:type,is_ajaxed:1,search:$search_type},
      dataType : 'json',
      type:'post',
      beforeSend : function(e){
         $("#ajax_loader").show();
         $("#"+place_div).html('').hide();
      },
      success :function(res){
         $("#ajax_loader").hide();
            if(res.status =='success'){
               $("#"+place_div).html(res.html).show();
            }
         }
      });
   }
</script>