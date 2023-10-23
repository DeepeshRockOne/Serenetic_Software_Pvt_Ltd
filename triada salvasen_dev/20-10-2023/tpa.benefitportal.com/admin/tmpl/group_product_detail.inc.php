<div class="panel panel-default">
   <div class="panel-heading">
      <h4 class="mn">Product Details - <span class="fw300"><?= $product_name ?></span></h4>
   </div>
   <div class="panel-body">
         <ul class="nav nav-tabs tabs  nav-noscroll customtab" role="tablist">
            <li role="presentation" class="active"><a href="#Agent_View" data-toggle="tab" aria-expanded="false">Group Information</a>
         </li>
         <li role="presentation" class=""><a href="#Resources" data-toggle="tab" aria-expanded="false">Resources</a>
      </li>
   </ul>
<div class="tab-content m-t-20">
   <div role="tabpanel" class="tab-pane active" id="Agent_View">
      <h3><?= $product_name ?></h3>
       <div class="portal_prdinfo_scroll">
        <?= displayAgentPortalDescriptionInfo($product_id,'Y') ?>
        <?= $product_description ?>
      </div>
   </div>
   <div role="tabpanel" class="tab-pane" id="Resources">
      <div class="clearfix m-b-15">
         <div class="pull-left">
            <!-- <h4 class="m-t-5">Pending Approval</h4> -->
         </div>
         <div class="pull-right">
            <div class="note_search_wrap auto_size" id="prd_search_div" style="display: none; max-width: 100%;">
               <div class="phone-control-wrap theme-form">
                  <div class="phone-addon">
                     <div class="form-group height_auto mn">
                        <a href="javascript:void(0);" class="search_close_btn text-light-gray" id="prd_search_close_btn">X</a>
                     </div>
                  </div>
                  <div class="phone-addon">
                     <div class="form-group height_auto mn">
                        <input type="text"  class="form-control" id="searched_keyword" name="searched_keyword" value="" >
                        <label>Keyword</label>
                     </div>
                  </div>
                  <div class="phone-addon w-80">
                     <div class="form-group height_auto mn">
                        <a href="javascript:void(0);" class="btn btn-info search_button" id="search_btn">Search</a>
                     </div>
                  </div>
               </div>
            </div>
            <a href="javascript:void(0);" class="search_btn" id="prd_search_btn"><i class="fa fa-search fa-lg text-blue"></i></a>
         </div>
      </div>
      <div id="resource_table_div"></div>
   </div>
</div>
</div>
<div class="br-t text-center p-t-10">
   <a href="javascript:void(0);" class="btn red-link" id="close_product_detail">Close</a>
</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
  
    load_resource();
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
  $(document).off('click',"#close_product_detail");
  $(document).on("click", "#close_product_detail", function(e) {
      e.preventDefault();
      window.parent.$.colorbox.close();
  });
  
  $(document).off('click',"#prd_search_btn");
  $(document).on("click", "#prd_search_btn", function(e) {
  e.preventDefault();
      $(this).hide();
      $("#prd_search_div").css('display', 'inline-block');
  });
  $(document).off('click',"#prd_search_close_btn");
  $(document).on("click", "#prd_search_close_btn", function(e) {
      e.preventDefault();
      $("#prd_search_div").hide();
      $("#prd_search_btn").show();

      load_resource();
  });

  var not_win = '';
  $(document).off('click',".play_video");
  $(document).on('click',".play_video",function(e){
    $href = $(this).attr('data-href');
    var not_win = window.open($href, "myWindow", "width=600,height=450");
    if(not_win.closed) {  
        alert('closed');  
    } 
  });

  $(document).off('click','#search_btn');
  $(document).on('click','#search_btn',function(e){
    e.preventDefault();
    
    var searched_keyword = $("#searched_keyword").val();
    
    if(searched_keyword!==''){
      load_resource(searched_keyword);
    }else{
      alert("Please Enter Search Keyword(s)");
    }
  });

load_resource = function(search_val){
  $product_id = '<?= $_GET['product_id'] ?>';
  $('#ajax_loader').show();
  $.ajax({
    url:'group_product_resource.php?product_id='+$product_id,
    type: 'GET',
    data: {
      is_ajaxed: 1,
      search_val: search_val
    },
    success: function(res) {
      $('#ajax_loader').hide();
      $('#resource_table_div').html(res);
      $('[data-toggle="tooltip"]').tooltip();
      if ($("[rel=tooltip]").length) {
            $("[rel=tooltip]").tooltip();
      }
    }
  });
}
</script>