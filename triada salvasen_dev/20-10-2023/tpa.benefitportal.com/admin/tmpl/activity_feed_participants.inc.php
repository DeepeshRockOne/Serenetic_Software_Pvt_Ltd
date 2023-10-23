<?php $user_type = "Participants";?>
<p class="agp_md_title pull-left">Activity History</p>
<div class="clearfix"></div>
<div id="open_account_search">
    <form action="ajax_activity_feed_participants.php?id=<?php echo $_REQUEST['id']; ?>&for=Participants" method="POST" id="acc_feed_search_form" class="theme-form">
        <input type="hidden" name="allNumRows" id="allNumRows" value="">
        <input type="hidden" name="maxid" id="maxid" value="">
       <div class="m-b-10" >
            <a href="#" class="search_btn" id="srh_btn"><i class="fa fa-search fa-lg text-blue"></i></a>
            <a href="#" class="search_btn search_close_btn" id="srh_close_btn" style="display:none;"><i
                        class="text-light-gray">X</i></a>
        </div>
        <div id="search_activty" class="flex_row activity_filter">
            <div class="form-group">
                <input type="text" name="keyword_search" id="keyword_search" class="form-control">
                <label>Keyword Search</label>
            </div>
            <div class="form-group min-w175">
                <select class="form-control " name="acc_his_custom_date" id="acc_his_custom_date">
                    <option value="" disabled selected hidden></option>
                    <option value="Range">Range</option>
                    <option value="exactly">Exactly</option>
                    <option value="before">Before</option>
                    <option value="after">After</option>
                </select>
                <label>Quick Search</label>
            </div>
          <div class="form-group height_auto mn" id="dt_range">
                <div class="dt_range">
                <div class="phone-control-wrap">
                  <div class="phone-addon from_label">
                <div class="form-group" >
                   <label class="mn">From </label>
                </div>
            </div>
            <div class="phone-addon w-160">
                <div class="form-group">
                    <div class="input-group">
                      <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="acc_his_fromdate" id="acc_his_fromdate" value="" class="date_picker form-control">
                    </div>
                </div>
              </div>
              <div class="phone-addon to_label">
                <div class="form-group mn">
                  <label>To </label>
              </div>
            </div>
            <div class="phone-addon w-160" id="to_date">
                <div class="form-group">
                  <div class="input-group">
                      <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                      <input type="text" name="acc_his_todate" id="acc_his_todate" value="" class="date_picker form-control">
                   </div>
                 </div>
               </div>
                </div>
            </div>
          </div>
            <div class="form-group">
                <button id="pay_search" name="search" type="button" class="btn btn-primary-o"
                        onclick="ajax_load_activity_feed()"> Search
                </button>
                <button id="pay_viewall" name="viewall" type="button" class="btn btn-primary-o"
                        onclick="ajax_reload_activity_feed()"> View All
                </button>
            </div>
        </div>
    </form>

    <div class="clearfix"></div>
    <hr
    / style="margin-top:15px;">
</div>
<div class="inner_accor" id="sec_acct_his"> Loading...</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#keyword_search").on("keyup", function () {
            delay(function(){
                ajax_load_activity_feed();
            }, 1000 );
        });
        $('#srh_btn').click(function (e) {
            e.preventDefault(); //to prevent standard click event
            $("#srh_btn").hide();
            $("#srh_close_btn").show();
            $("#search_activty").slideDown();
        });
        $('#srh_close_btn').click(function (e) {
            e.preventDefault(); //to prevent standard click event
            $('#srh_btn').hide();
            $("#srh_close_btn").hide();
            $("#search_activty").slideUp();
            $("#srh_btn").show();
        });

        $('#dt_range').hide();
        $('#search_activty').hide();
      
        $('#acc_his_custom_date').change(function(){
          if($('#acc_his_custom_date').val() == 'Range') {
            $('#dt_range').show();
            $(".from_label").show();
            $("#to_date").show();
          } else if($('#acc_his_custom_date').val() == ''){
            $('#dt_range').hide();
          }else{
            $(".from_label").hide();
            $('#dt_range').show();
            $('.to_label').hide();
            $("#to_date").hide();
          }
        });
      
        $('#ajax_loader').show();
        ajax_load_activity_feed();
        $('.account_note_popup').colorbox({iframe: true, width: '750', height: '350;'});
        var is_all_activity_popup = 'Y';
        var flag = 0;
        $(window).scroll(function () {
            if (Math.ceil($(window).scrollTop()) == $(document).height() - $(window).height()) {

                if (is_all_activity_popup == 'Y') {
                    var activity_type = $("#acc_feed_search_form #activity_type").val();
                    var acc_his_custom_date = $("#acc_feed_search_form #acc_his_custom_date").val();
                    var acc_his_fromdate = $("#acc_feed_search_form #acc_his_fromdate").val();
                    var acc_his_todate = $("#acc_feed_search_form #acc_his_todate").val();
                    var keyword_search = $("#acc_feed_search_form #keyword_search").val();
                    if (maxid != 0 && allNumRows > 0) {
                        $('#ajax_loader').show();

                        if (flag == 0 && maxid != 'undefined') {
                            flag = 1;
                            $('#ajax_loader').show();
                            $.ajax({
                                url: 'ajax_activity_feed_participants.php',
                                type: 'POST',
                                data: {
                                    maxid: maxid,
                                    id: '<?= $_REQUEST['id'] ?>',
                                    user_type: '<?=$user_type?>',
                                    activity_type: activity_type,
                                    keyword_search: keyword_search,
                                    acc_his_custom_date: acc_his_custom_date,
                                    acc_his_fromdate: acc_his_fromdate,
                                    acc_his_todate: acc_his_todate,
                                },
                                success: function (res) {
                                    flag = 0;
                                    maxid = 0;
                                    $('#ajax_loader').hide();
                                    $("#spinner").hide();
                                    $('#sec_acct_his').append(res);
                                    $(".popup").colorbox({iframe: true, width: '1000px', height: '600px'});
                                    $(".trigger_popup").colorbox({iframe: true, width: '60%', height: '70%'});
                                    $(".confirm_email_popup").colorbox({iframe: true, width: '60%', height: '70%'});
                                }
                            });
                        }

                    }
                }
            }
        });
    });

    function ajax_load_activity_feed() {
        var activity_type = $("#activity_type").val();
        var acc_his_custom_date = $("#acc_his_custom_date").val();
        var acc_his_fromdate = $("#acc_his_fromdate").val();
        var acc_his_todate = $("#acc_his_todate").val();
        var keyword_search = $("#keyword_search").val();
        $('#ajax_loader').show();
        $.ajax({
            url: "ajax_activity_feed_participants.php",
            data: {
                id: '<?= $_REQUEST['id'] ?>',
                user_type: '<?=$user_type?>',
                activity_type: activity_type,
                keyword_search: keyword_search,
                acc_his_custom_date: acc_his_custom_date,
                acc_his_fromdate: acc_his_fromdate,
                acc_his_todate: acc_his_todate,
                for: 'Participants'
            },
            type: 'POST',
            success: function (res) {
                $('#ajax_loader').hide();
                $("#sec_acct_his").html(res);
                $('.date_picker').datepicker({
                    format: 'mm/dd/yyyy',
                    minView: 'month',
                    maxView: 'month',
                    orientation: 'top auto',
                    autoclose: true
                });
            }
        });
    }

    function ajax_reload_activity_feed() {
        $("#activity_type").val("");
        $("#acc_his_custom_date").val("");
        $("#acc_his_fromdate").val("");
        $("#acc_his_todate").val("");
        $('#ajax_loader').show();
        $.ajax({
            url: "ajax_activity_feed_participants.php",
            data: {id: '<?= $_REQUEST['id'] ?>', user_type: '<?=$user_type?>', for: 'Participants'},
            type: 'POST',
            success: function (res) {
                $('#ajax_loader').hide();
                $("#sec_acct_his").html(res);
                $('.date_picker').datepicker({
                    format: 'mm/dd/yyyy',
                    minView: 'month',
                    maxView: 'month',
                    orientation: 'top auto',
                    autoclose: true
                });
            }
        });
    }

    var delay = (function(){
        var timer = 0;
        return function(callback, ms){
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
        };
    })();
</script>