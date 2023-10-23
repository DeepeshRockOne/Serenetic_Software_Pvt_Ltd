<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <div class="panel-title">
      <p class="fs18"><strong class="fw500">Product History  - </strong> <span class="fw300"><?= $type ?> </span></p>
    </div>
  </div>
  <div class="panel-body">
    <div id="open_account_search">
  <form action="ajax_prd_history.php?product=<?= $_REQUEST['product'] ?>&type=<?= $_REQUEST['type'] ?>" method="POST" id="acc_feed_search_form" class="theme-form">
   <div class="form-group pull-left" >
       <a href="#" class="search_btn" id="srh_btn"><i class="fa fa-search fa-lg text-blue"></i></a>
       <a href="#" class="search_btn search_close_btn" id="srh_close_btn" style="display:none;" ><i class="text-light-gray">X</i></a>
      </div>
      <div id="search_activty" class="flex_row activity_filter">
      <div class="form-group">
        <input type="text" name="keyword_search" id="keyword_search" class="form-control">
         <label>Keyword Search</label>
        <div class="clearfix"></div>
      </div>
      <div class="form-group mw-125">
        
        <select class="form-control" name="acc_his_custom_date" id="acc_his_custom_date">
        <option value=""> </option>
          <option value="Range">Range</option>
          <option value="exactly">Exactly</option>
          <option value="before">Before</option>
          <option value="after">After</option>
        </select>
        <label>Quick Search:</label>
      </div>
    <div id="dt_range" class="form-group height_auto">
        <div class="form-group">
          <label id="from_date">From </label>
            <div class="input-group">
              <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
            <input type="text" name="acc_his_fromdate" id="acc_his_fromdate" value="" class="date_picker form-control">
            </div>
          <div class="clearfix"></div>
        </div>
        <div class="form-group to_date">
          <label>To </label>
          <div class="input-group">
              <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
              <input type="text" name="acc_his_todate" id="acc_his_todate" value="" class="date_picker form-control">
           </div>
        </div>
    </div>
      <div class="form-group">
        <button id="pay_search" name="search" type="button" class="btn btn-primary-o" onclick="ajax_load_activity_feed()">  Search </button>
        <button id="pay_viewall" name="viewall" type="button" class="btn btn-primary-o" onclick="window.location.reload()">  View All </button>
      </div>
      </div>
  </form>
  
  <div class="clearfix"></div>
  <hr / style="margin-top:0px;">
</div>
<div class="inner_accor" id="sec_acct_his"> Loading... </div>

  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
     $(".date_picker").datepicker({
      changeDay: true,
      changeMonth: true,
      changeYear: true,
      autoclose: true
    });
    $("#keyword_search").on("keyup", function() {
      delay(function(){
        ajax_load_activity_feed();
      }, 1000 );
    });
  $('#srh_btn').click(function(e){
    e.preventDefault(); //to prevent standard click event
    $("#srh_btn").hide();
    $("#srh_close_btn").show();
    $("#search_activty").fadeIn();    
  });
  $('#srh_close_btn').click(function(e){
    e.preventDefault(); //to prevent standard click event
    $('#srh_btn').hide();
    $("#srh_close_btn").hide();
    $("#search_activty").fadeOut();
    $("#srh_btn").show();
  });
  
    $('#dt_range').css({ display: 'none' });
  $('#search_activty').css({ display: 'none' });
  
    $('#acc_his_custom_date').change(function(){
      if($('#acc_his_custom_date').val() == 'Range') {
        $('#dt_range').css({ display: 'inline-block' });
        $("#from_date").show();
        $(".to_date").show();
      } else if($('#acc_his_custom_date').val() == ''){
        $('#dt_range').css({ display: 'none' });
      }else{
        $("#from_date").hide();
        $('#dt_range').css({ display: 'inline-block' });
        $(".to_date").hide();
      }
    });
  });

  $(document).ready(function() {
     ajax_load_activity_feed();
     $('.account_note_popup').colorbox({iframe: true, width: '750', height: '350;'});
  });
  function ajax_load_activity_feed() {
    var activity_type = $("#activity_type").val();
    var acc_his_custom_date = $("#acc_his_custom_date").val();
    var acc_his_fromdate = $("#acc_his_fromdate").val(); 
    var acc_his_todate = $("#acc_his_todate").val();
    var keyword_search = $("#keyword_search").val();
    $('#ajax_loader').show();
    $.ajax({
      url: "ajax_prd_history.php",
      data: {product: '<?= $_REQUEST['product'] ?>',type: '<?= $_REQUEST['type'] ?>',activity_type:activity_type,keyword_search:keyword_search,acc_his_custom_date:acc_his_custom_date,acc_his_fromdate:acc_his_fromdate,acc_his_todate:acc_his_todate,for:'Admin'},
      type: 'POST',
      success: function(res) {
        $('#ajax_loader').hide();
        $("#sec_acct_his").html(res);
      }
    });
  }
 
var flag = 0;
$(window).scroll(function () {        
    if(Math.ceil($(window).scrollTop()) == $(document).height() - $(window).height()) {
      if (from_limit != 0 && total_rows > 0) {
          $('#ajax_loader').show();
          if (flag == 0 && from_limit != 'undefined') {
              flag = 1;
              $('#ajax_loader').show();
              var activity_type = $("#activity_type").val();
              var acc_his_custom_date = $("#acc_his_custom_date").val();
              var acc_his_fromdate = $("#acc_his_fromdate").val(); 
              var acc_his_todate = $("#acc_his_todate").val();
              var keyword_search = $("#keyword_search").val();
              $.ajax({
                  url: "ajax_prd_history.php",
                  type: 'POST',
                  data: {product: '<?= $_REQUEST['product'] ?>',type: '<?= $_REQUEST['type'] ?>',activity_type:activity_type,keyword_search:keyword_search,acc_his_custom_date:acc_his_custom_date,acc_his_fromdate:acc_his_fromdate,acc_his_todate:acc_his_todate,for:'Admin','from_limit':from_limit
                  },
                  success: function (res) {
                      flag = 0;
                      from_limit = 0;
                      $('#ajax_loader').hide();
                      $("#spinner").hide();
                      $('.steamline').append(res);
                      $(".popup").colorbox({iframe: true, width: '1000px', height: '600px'});                                  
                      $(".trigger_popup").colorbox({iframe: true, width: '60%', height: '70%'});
                      $(".confirm_email_popup").colorbox({iframe: true, width: '60%', height: '70%'});
                  }
              });
          }

      }
        
    }
});

var delay = (function(){
  var timer = 0;
  return function(callback, ms){
    clearTimeout (timer);
    timer = setTimeout(callback, ms);
  };
})();


</script> 
<script type="text/javascript">
  $(document).on('click','.lead_opt_action',function(e){
    //$('.lead_opt_action').click(function(e){
        e.preventDefault();
        var obj = $(this);
        $.ajax({
          url: $(obj).attr('href'),
          type: 'GET',
          success: function (res) {
            $('#leadcontainer').fadeOut('slow', function () {
              $('#leadcontainer').html(res).fadeIn('slow');
            });
          }
        });
    });
</script>
