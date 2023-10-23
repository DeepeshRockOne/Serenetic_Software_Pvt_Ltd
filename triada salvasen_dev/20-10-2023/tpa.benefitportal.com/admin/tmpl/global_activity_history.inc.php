<?php if (1) { ?>
<div class="preface_global_activity">
    <div class="tabbing-tab">
        <ul class="nav nav-tabs customtab">
            <li><a class="gsch_page"  href="global_activity_admin_tabs.php" data-type="Admin" data-counter="0">Admins</a></li>
            <li><a class="gsch_page"  href="global_activity_agent_tabs.php" data-type="Agent" data-counter="0">Agents</a></li>
            <li><a class="gsch_page"  href="global_activity_group_tabs.php" data-type="Group" data-counter="0">Groups</a></li>
            <li><a class="gsch_page" href="global_activity_lead_tabs.php" data-type="Lead" data-counter="0">Leads</a></li>
            <li><a class="gsch_page" href="global_activity_member_tabs.php" data-type="Member" data-counter="0">Members</a></li>
        </ul>
    </div>
</div>
<div class="tab-content mn">
    <div id="global_search_result" class="tab-pane active">     
        <div id="sec_acct_his">
        </div>
    </div>
</div>
<?php } else { ?>
<div class="panel panel-default panel-block">
    <div class="panel-body">
        <div id="sparkline-boxes">
            <h4><i class="icon-warning-sign"></i> &nbsp; No Record(s).</h4>
        </div>
    </div>
</div>
<?php } ?>
<script type="text/javascript">
var $type='';
var flg=false;
var is_all_activity_popup ='Y';
var flag = 0;
$(document).ready(function() {
    $(document).off('click','a.gsch_page');
    $(document).on('click','a.gsch_page', function(e) {
        e.preventDefault();
        var $obj = $(this);
        $type=$obj.attr('data-type');
        var gsearch = '<?php echo isset($_GET['gsearch'])?$_GET['gsearch']:"_";?>';
        $.ajax({
        url: $obj.attr("href"),
        type: 'GET',
        beforeSend:function(){
            $("#ajax_loader").show();
        },
        data: {gsearch: gsearch,type:$type},
        success: function(res) {
            $("#ajax_loader").hide();
            $("a.gsch_page").parent('li').removeClass("active");
            $("a.gsch_page[href='"+$obj.attr("href")+"']").parent('li').addClass("active");
            $('#sec_acct_his').html(res).fadeIn('slow');
        }
        });
    });    

    $('a.gsch_page[data-type="Admin"]').trigger('click');  

    //code start
    $(window).scroll(function () {
        if(Math.ceil($(window).scrollTop()) == $(document).height() - $(window).height()) {
        if (is_all_activity_popup == 'Y') {
                if (from_limit != 0 && total_rows > 0) {
                $('#ajax_loader').show();
                    var action = "";
                    var params = "";
                    if ($type === 'Admin') {
                        var action = 'global_activity_admin_tabs.php?type=Admin&is_all_activity_popup=Y';
                        var params = $('#frm_search_admins').serialize();
                    }else if ($type === 'Agent') {
                        var action = 'global_activity_agent_tabs.php?type=Agent&is_all_activity_popup=Y';
                        var params = $('#frm_search_agents').serialize();
                    }else if ($type === 'Group') {
                        var action = 'global_activity_group_tabs.php?type=Group&is_all_activity_popup=Y';
                        var params = $('#frm_search_groups').serialize();
                    }else if ($type === 'Lead') {
                        var action = 'global_activity_lead_tabs.php?type=Lead&is_all_activity_popup=Y';
                        var params = $('#frm_search_leads').serialize();
                    }else if ($type === 'Member') {
                        var action = 'global_activity_member_tabs.php?type=Member&is_all_activity_popup=Y';
                        var params = $('#frm_search_members').serialize();
                    }
                    
                    if (flag == 0 && from_limit != 'undefined') {
                        flag = 1;
                        params += '&from_limit='+from_limit;
                        $('#ajax_loader').show();
                        $.ajax({
                            url: action,
                            type: 'POST',
                            data: params,
                            success: function (res) {
                                flag = 0;
                                from_limit = 0;
                                $('#ajax_loader').hide();
                                $("#spinner").hide();
                                $('#ajax_data').append(res);
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

    $(window).on('resize load', function(){
        if ($(window).width() <= 991) {
            $('.nav-tabs:not(.nav-noscroll)').scrollingTabs('destroy');
            autoResizeNav();
        }
    });
});

function email_resend(trigger_id, email,type) {
    if(type == ''){
        type = 'Email';
    }
    swal({
        text: "Are you sure you want to resend "+ type +"!",
        showCancelButton: true,
        confirmButtonText: "Confirm",
        cancelButtonText: "Cancel",
        closeOnConfirm: false,
        closeOnCancel: false
    }).then(function () {
        $.ajax({
            url: 'ajax_trigger_resend.php',
            data: {
                trigger_id: trigger_id,
                email: email,
                type:type
            },
            dataType: 'json',
            type: 'post',
            success: function (res) {
                if (res.status == "success") {
                    swal("Good job!", type + " send successfully!", "success");
                }
            }
        });
    }, function (dismiss) {

    });
}

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