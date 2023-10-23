<style type="text/css">
    .highcharts-tooltip> span> span{
        padding-bottom: 5px;
        width: 80px;
        display: inline-block;
        text-align: right;
    }
    .highcharts-tooltip> span> span.sales_label{
        text-align: left;
    }
</style>
<?php if ($is_ajaxed) { ?>
<div class="table-responsive">
    <table class="<?=$table_class?>">
      <thead>
        <tr>
          <th>ID/Added Date</th>
          <th>ID/User Name</th>
          <th>ID/Assignee Name </th>
          <th>User Type</th>
          <th>Category</th>
          <th class="text-center" width="275px">Script</th>
          <th width="130px">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($total_rows > 0) { ?>
            <?php foreach ($fetch_rows as $key => $rows) { ?>
                <tr>
                    <td>
                        <a href="javascript:void(0);"  class="text-action fw500"><?=$rows['display_id']?></a><br/>
                        <?=displayDate($rows['creation_time'])?>
                    </td>
                    <td><a href="javascript:void(0);"  class="text-action fw500"><?=$rows['userDispId']?></a><br><?=$rows['userName']?></td>
                    <td><a href="javascript:void(0);"  class="text-action fw500"><?=$rows['admin_rep_id']?></a><br><?=$rows['admin_name']?></td>
                    <td><?=($rows['userType'] == "Customer"?"Member":$rows['userType']);?></td>
                    <td><?=(isset($lc_departments[$rows['department']])?$lc_departments[$rows['department']]['name']:'-')?></td>
                    <td class="icons text-center">
                      <table cellpadding="0" cellspacing="0" align="center">
                        <tbody>
                          <tr>
                            <td>
                               <div style="display:none" id="password_popup_<?= $rows['id'] ?>">
                        <div class="phone-control-wrap">
                          <div class="phone-addon">
                            <input type="password" class="form-control" id="showing_pass_<?= $rows['id'] ?>"></div>
                          <div class="phone-addon"><button class="btn btn-info show_password" data-id="<?= $rows['id'] ?>" data-user-id="<?=$rows['user_id']?>">Unlock</button></div>
                        </div>
                      </div>
                            </td>
                            <td> <a href="javascript:void(0)" class="click_to_show" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="View" data-id="<?= $rows['id'] ?>"><i class="fa fa-eye"></i></a></td>
                          </tr>
                        </tbody>
                      </table>
                     
                     
                    </td>
                    <td><?=$LiveChat->get_display_chat_status($rows['status_code'])?></td>
                </tr>
            <?php } ?>
        <?php } else { ?>
          <tr>
              <td colspan="7" align="center">No record(s) found</td>
          </tr>
        <?php } ?>
      </tbody>
      <?php if ($total_rows > 0) { ?>
          <tfoot>
              <tr>
                  <td colspan="7">
                      <?php echo $paginate->links_html; ?>
                  </td>
              </tr>
          </tfoot>
      <?php } ?>
    </table>
</div>
<?php } else { ?>
  <div class="panel panel-default panel-block panel-title-block advance_info_div">
  <div class="panel-body ">
    <div class="phone-control-wrap ">
      <div class="phone-addon w-90"> <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="70px"> </div>
      <div class="phone-addon text-left lato_font">Live chat is available both inside the <?= $DEFAULT_SITE_NAME ?> portals as well as outside on specific sales pages. Below is a summary
        of Live Chat interactions. You may make yourself available to chat by selecting the "GO LIVE" button below.
  <!--       <div class="clearfix m-t-10"> <a href="javascript:void(0);" id="go_live_chat" class="btn btn-action">GO LIVE</a> </div> -->
        <div class="clearfix m-t-10"> <a href="javascript:void(0);" id="go_live_chat_new" class="btn btn-action">GO LIVE</a> </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-3">
    <div class="white-box">
      <h4 class="m-t-0 m-b-15">Summary of Current Activity</h4>
      <div class="sum_activity_wrap">
        <div class="sum_activity_box success"> 
          <a href="javascript:void(0);" data-href="live_chat_activity_popup.php?action=online_admins" class="live_chat_activity_popup"> Admin(s) Online <span class="online_admins sum_activity_addon" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="View"><?=count($online_admins);?></span> 
          </a> </div>
        <div class="sum_activity_box warning"> 
          <a href="javascript:void(0);" data-href="live_chat_activity_popup.php?action=idle_admins" class="live_chat_activity_popup"> Admin(s) Idle <span class="idle_admins sum_activity_addon" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="View"><?=count($idle_admins)?></span> 
          </a> 
        </div>
        <div class="sum_activity_box info"> 
          <a href="javascript:void(0);" data-href="live_chat_activity_popup.php?action=active_chats" class="live_chat_activity_popup"> Active Chat(s) <span class="active_chats sum_activity_addon"><?=count($live_conversations);?></span> 
          </a> 
        </div>
        <div class="sum_activity_box danger"> 
          <a href="javascript:void(0);" data-href="live_chat_activity_popup.php?action=in_queue_chats" class="live_chat_activity_popup"> In Queue <span class="in_queue_chats sum_activity_addon"><?=count($in_queue_conversations);?></span> 
          </a> 
        </div>
        <div class="sum_activity_box default"> 
          <a href="javascript:void(0);" data-href="live_chat_activity_popup.php?action=total_served_chat" class="live_chat_activity_popup"> Total Served <span class="total_served_chat sum_activity_addon"><?=count($served_conversations);?></span> 
          </a> 
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-9">
    <div class="white-box chart_chat_box">
      <h4 class="m-t-0 m-b-15">Chats/Day</h4>
      <div class="flot-chart" >
        <div class="flot-chart-content" id="liveChatChartDiv"></div>
      </div>
    </div>
  </div>
</div>
<div class="panel panel-default panel-block panel-title-block">
  <div class="panel-left">
    <div class="panel-left-nav">
      <ul>
        <li class="active"><a href="javascript:void(0);"><i class="fa fa-search"></i></a></li>
      </ul>
    </div>
  </div>
  <form id="frm_search" action="live_chat_dashboard.php" method="GET" class="theme-form" autocomplete="off">
  <div class="panel-right">
    <div class="panel-heading">
      <div class="panel-search-title"> <span class="clr-light-blk">SEARCH</span> </div>
    </div>
    <div class="panel-wrapper collapse in">
      <div class="panel-body theme-form">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <input type="text" name="display_id" id="display_id" class="form-control listing_search">
              <label>Live Chat ID</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="row">
              <div id="date_range" class="col-md-12">
                  <div class="form-group">
                      <select class="form-control" id="join_range" name="join_range">
                          <option value=""></option>
                          <option value="Range">Range</option>
                          <option value="Exactly">Exactly</option>
                          <option value="Before">Before</option>
                          <option value="After">After</option>
                      </select>
                      <label>Added Date</label>
                  </div>
              </div>
              <div class="select_date_div col-md-9" style="display:none">
                  <div class="form-group">
                      <div id="all_join" class="input-group">
                          <span class="input-group-addon"><i
                                      class="fa fa-calendar"></i></span>
                          <input type="text" name="added_date" id="added_date" value=""
                                 class="form-control date_picker"/>
                      </div>
                      <div id="range_join" style="display:none;">
                          <div class="phone-control-wrap">
                              <div class="phone-addon">
                                  <label class="mn">From</label>
                              </div>
                              <div class="phone-addon">
                                  <div class="input-group">
                                      <span class="input-group-addon"><i
                                                  class="fa fa-calendar"></i></span>
                                      <input type="text" name="fromdate" id="fromdate"
                                             value="" class="form-control date_picker"/>
                                  </div>
                              </div>
                              <div class="phone-addon">
                                  <label class="mn">To</label>
                              </div>
                              <div class="phone-addon">
                                  <div class="input-group">
                                      <span class="input-group-addon"><i
                                                  class="fa fa-calendar"></i></span>
                                      <input type="text" name="todate" id="todate" value=""
                                             class="form-control date_picker"/>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
            </div>
          </div>
          
          <div class="col-sm-6">
            <div class="form-group">
              <select class="form-control listing_search" name="user_type" id="user_type">
                <option></option>
                <option value="Customer">Member</option>
                <option value="Agent">Agent</option>
                <option value="Group">Employer Group</option>
                <option value="Website">Website</option>
              </select>
              <label>User Type</label>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <input type="text" name="user_id" id="user_id" class="form-control listing_search">
              <label>User ID</label>
            </div>
          </div>
        <?php /*
          <div class="col-sm-6">
            <div class="form-group">
              <input type="text" name="assigned_admin_id" id="assigned_admin_id" class="form-control listing_search">
              <label>Assign (Admin Name/ID)</label>
            </div>
          </div>*/          
          ?>
          <div class="col-sm-6">
            <div class="form-group">
              <select class="form-control listing_search" name="department[]" id="department" multiple="multiple">
                <?php 
                  if(!empty($lc_departments)) { 
                    foreach ($lc_departments as $dep_row) {
                      ?><option value="<?=$dep_row['id']?>"><?=$dep_row['name']?></option><?php 
                    }
                  }
                  ?>
              </select>
              <label>Category</label>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <select class="form-control listing_search" name="status_code[]" id="status_code" multiple="multiple">
                <option value="0">Active</option>
                <option value="1">Waiting User</option>
                <option value="2">Waiting Admin</option>
                <option value="3">Archive</option>
                <option value="4">Chat Closed</option>
              </select>
              <label>Status</label>
            </div>
          </div>
        </div>
        <div class="panel-footer clearfix tbl_search_filter">
            <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1"/>
            <input type="hidden" name="pages" id="per_pages" value="<?= $per_page; ?>"/>
            <input type="hidden" name="sort_by" id="sort_by_column" value="<?= $SortBy; ?>"/>
            <input type="hidden" name="sort_direction" id="sort_by_direction"
               value="<?= $SortDirection; ?>"/>
            <div class="pull-left">
              <button type="submit" class="btn btn-info"><i class="fa fa-search"></i> Search</button>
              <button type="button" class="btn btn-info btn-outline" onClick="window.location = 'live_chat_dashboard.php'"><i class="fa fa-search-plus"></i> View All</button>
            </div>
            <div class="pull-right">
                <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
                   <div class="form-group mn height_auto">
                      <label for="">Records Per Page </label>
                   </div>
                   <div class="form-group mn height_auto">
                      <select size="1" id="pages" name="pages"
                         class="form-control select2 placeholder"
                         onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);ajax_submit();">
                         <option value="10" <?php echo isset($_GET['pages']) && $_GET['pages'] == 10 ? 'selected' : ''; ?>>
                            10
                         </option>
                         <option value="25" <?php echo (isset($_GET['pages']) && $_GET['pages'] == 25) || (isset($_GET['pages']) && $_GET['pages'] == "") ? 'selected' : ''; ?>>
                            25
                         </option>
                         <option value="50" <?php echo isset($_GET['pages']) && $_GET['pages'] == 50 ? 'selected' : ''; ?>>
                            50
                         </option>
                         <option value="100" <?php echo isset($_GET['pages']) && $_GET['pages'] == 100 ? 'selected' : ''; ?>>
                            100
                         </option>
                      </select>
                   </div>
                </div>
             </div>
        </div>
      </div>
      
    </div>
  </div>
  <div class="search-handle"> <a href="#" data-perform="panel-collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></a> </div>
  </form>
</div>

<div class="white-box">
  <div class="clearfix m-b-15 tbl_filter">
      <div class="pull-left">
          <h4 class="m-t-7">Live Chat History</h4>
      </div>
      <div class="pull-right">
          <a href="manage_live_chat.php" class="btn btn-default">Manage Live Chat</a>
      </div>
  </div>
  <div id="ajax_data"></div>
</div>

<script type="text/javascript">
$(document).ready(function () {
  dropdown_pagination('ajax_data')

    $(".date_picker").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true
    });

    $(document).off("submit", "#frm_search");
    $(document).on("submit", "#frm_search", function (e) {
        e.preventDefault();
        disable_search();
    });

    $(document).off('click',"#go_live_chat");
    $(document).on('click',"#go_live_chat",function(){
        $("#ajax_loader").show();
        $.ajax({
            method: 'GET',
            url: '<?=$HOST?>/login_chat_account.php?action=login_chat_account&location=admin',
            dataType : 'json',
        }).done((response) => {
            if(response.status == "success") {
                $("#ajax_loader").hide();
                $href = '<?=$LIVE_CHAT_HOST?>';
                window.open($href, "myWindow","width=1280,height=700");
            } else {
                window.location.reload();
            }
        });
        
    });
    $(document).off('click',"#go_live_chat_new");
    $(document).on('click',"#go_live_chat_new",function(){
        $("#ajax_loader").show();
        $.ajax({
            method: 'GET',
            url: '<?=$HOST?>/login_chat_account.php?action=login_chat_account&location=admin',
            dataType : 'json',
        }).done((response) => {
            if(response.status == "success") {
                $("#ajax_loader").hide();
                $href = '<?=$ADMIN_HOST?>/go_live_chat.php';
                window.open($href, "myWindow","width=1280,height=700");
            } else {
                window.location.reload();
            }
        });
        
    });

    $(document).off('click', '#ajax_data tr.data-head a');
    $(document).on('click', '#ajax_data tr.data-head a', function(e) {
        e.preventDefault();
        $('#sort_by_column').val($(this).attr('data-column'));
        $('#sort_by_direction').val($(this).attr('data-direction'));
        ajax_submit();
    });

    $(document).off('click', '#ajax_data ul.pagination li a');
    $(document).on('click', '#ajax_data ul.pagination li a', function(e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#ajax_data').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function(res) {
                $('#ajax_loader').hide();
                $('#ajax_data').html(res).show();
                $('[data-toggle="tooltip"]').tooltip();
                common_select();
            }
        });
    });
    
    $(".live_chat_activity_popup").off('click');
    $(".live_chat_activity_popup").on('click',function(e){
      e.preventDefault();
      $href=$(this).attr('data-href');
      $.colorbox({
        href: $href,
        iframe: true, 
        width: '450px',
        height: '525px',
      });
    });
    load_live_chat_chart();
    ajax_submit();
});

$(document).off('change', '#join_range');
$(document).on('change', '#join_range', function(e) {
  e.preventDefault();
  if($(this).val() == ''){
    $('.select_date_div').hide();
    $('#date_range').removeClass('col-md-3').addClass('col-md-12');
  }else{
    $('#date_range').removeClass('col-md-12').addClass('col-md-3');
    $('.select_date_div').show();
    if ($(this).val() == 'Range') {
      $('#range_join').show();
      $('#all_join').hide();
    } else {
      $('#range_join').hide();
      $('#all_join').show();
    }
  }
});

function load_live_chat_chart(){
  $.ajax({
      url: "ajax_get_livechat_chart.php",
        method: "POST",
        data: {},
        dataType:'json',
        success: function (res) {   
          if(typeof(res.livechat_bar_chart) !== "undefined") {
            draw_livechat_bar_chart(res.livechat_bar_chart);
          }
        }
  });
}

function draw_livechat_bar_chart(chart_data){
  var days = chart_data.days;
  var daysData = chart_data.daysData;

  Highcharts.chart('liveChatChartDiv', {
    chart: {
        type: 'column'
    },
    title: false,
    subtitle: false,
    credits: {
        enabled: false
    },
    legend: {
            enabled: false,
            floating: true
        },
    xAxis: {
        categories: days,
        crosshair: true
    },
    yAxis: {
        min: 0,
        title:false
    },
    tooltip: {
        shared: true,
        backgroundColor: 'rgba(0,0,0,0.72)',
        borderRadius: 3,
        shadow: false,
        borderWidth: 0,
        useHTML: true,
        outside:false,
        VerticalAlignValue :  "top",
        formatter: function () {
            var tooltip_html = '<span class="sales_label" style="font-size: 16px; font-weight:bold;">'+this.points[0].point.name+'</span><br/>';

             tooltip_html += '<span class="sales_label">Total Served </span> <span>'+this.points[0].point.total_served+'</span><br/>';
            tooltip_html += '<span class="sales_label">Members </span> <span>'+this.points[0].point.total_members+'</span> <br/>';
             tooltip_html += '<span class="sales_label">Agents </span> <span>'+this.points[0].point.total_agents+'</span><br/>';
             tooltip_html += '<span class="sales_label">Groups </span> <span>'+this.points[0].point.total_groups+'</span><br/>';
             tooltip_html += '<span class="sales_label">External Website </span> <span>'+this.points[0].point.total_websites+'</span><br/>';
            return tooltip_html;
        },
        style: {
            color: '#fff',
            fontFamily: 'Roboto'
        }
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: [{
      data: daysData.slice(),
    }]
  });
}

function ajax_submit() {
    $('#ajax_loader').show();
    $('#ajax_data').hide();
    $('#is_ajaxed').val('1');
    var params = $('#frm_search').serialize();
    $.ajax({
        url: $('#frm_search').attr('action'),
        type: 'GET',
        data: params,
        success: function (res) {
            $('#ajax_loader').hide();
            $('#ajax_data').html(res).show();
            common_select();
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
    return false;
}

$(document).on('click','.click_to_show',function(){
    $id = $(this).attr('data-id');
    $("#password_popup_"+$id).show();
    
});

$(document).on('click','.show_password',function(){
    $id = $(this).attr('data-id');
    $user_id = $(this).attr('data-user-id');
    if($("#showing_pass_"+$id).val() === '5401')
    {
      $url="live_chat_script_popup.php?id="+$id+"&user_id="+$user_id;
      
      $.colorbox({
        href:$url,
        iframe: true, 
        width: '550px', 
        height: '550px'
      });
    }
    $("#showing_pass_"+$id).val('');
    $("#password_popup_"+$id).hide();
  });

</script>
<?php } ?>