<?php
$search_email = $_SESSION['admin']['email'];
if($search_email != ''){
  $customer_email = $pdo->select("SELECT id,type,company_id FROM customer WHERE email=:email AND is_deleted = 'N' AND status = 'Active'", array(':email' => $search_email));
}

$short_name = $_SESSION['admin']['fname'][0].$_SESSION['admin']['lname'][0];
?>
<nav class="navbar navbar-default navbar-static-top">
    <div class="navbar-header"> <a class="navbar-toggle hidden-sm hidden-md hidden-lg " href="javascript:void(0)" data-toggle="collapse" data-target=".navbar-collapse"><i class="ti-menu"></i></a>
        <div class="admin_user_wrap">
        	<div class="admin_user_info">
            <a href="javascript:void(0)" class="open-close hidden-xs text-white"><i class="icon-arrow-left-circle ti-menu"></i></a>
            <div class="clearfix"></div>
            <div class="user-profile dropdown clearfix " id="user_info">
            <?php
              $id = $_SESSION['admin']['id'];
              $admin_sql = "select fname,lname from admin where id=$id";
              $admin_res = $pdo->selectOne($admin_sql);
            ?>
            <strong class="fs12 text-white fw600">ADMIN</strong><div class="clearfix"></div>
      <a href="javascript:void(0);" class="dropdown-toggle u-dropdown half_width" data-toggle="dropdown">
    <?=$admin_res['fname'] . " " . $admin_res['lname']?>
      <span class="caret"></span></a>
      <a href="javascript:void(0);" class="dropdown-toggle admin_avatar" data-toggle="dropdown"><?=strtoupper($short_name)?></a>
      <ul class="dropdown-menu dropdown-menu-left">
        <li><a href="admin_profile.php?id=<?=md5($_SESSION['admin']['id'])?>"><i class="ti-user"></i> My Profile</a></li>
        <li><a href="logout.php?previous_page=<?=urlencode($_SERVER['REQUEST_URI'])?>"><i class="fa fa-power-off"></i> Logout</a></li>
      </ul>
    </div>
            </div>
        </div>
        <ul class="nav navbar-top-links navbar-left hidden-xs p-l-10 mn" id="search_widget">
            <!--<li><a href="javascript:void(0)" class="open-close waves-effect waves-light"><i class="icon-arrow-left-circle ti-menu"></i></a></li>-->
            <li>
            	<?php if (isset($breadcrumbes)): ?>
					      	<div id="page">
						       <ol class="breadcrumb breadcrumb-nav">
									<?php if($_SESSION['admin']['type'] == 'Agent Licensing' || $_SESSION['admin']['type'] == 'Agent Support'){
							          $link = 'agent_access.php';
							        } else if($_SESSION['admin']['type'] == 'Member Services'){
							          $link = 'member_access.php';
							        } else {
							          $link = 'dashboard.php';
							        }?>
						       	<li><a href="<?=$link?>"><i class="fa fa-home"></i></a></li>
						         <?php //$breadcrumbes['title'] = '<i class="fa fa-home"></i>';
									// $breadcrumbes['link'] = 'dashboard.php';
						         foreach ($breadcrumbes as $key => $breadcrumbe) { 
						         	if($key == 0)
						         		continue; ?>
						           <li <?php echo isset($breadcrumbe['class']) ? 'class="' . $breadcrumbe['class'] . '"' : '' ?>>
						             <a href="<?= !empty($breadcrumbe['link']) ? $breadcrumbe['link'] : '' ?>"><?php echo isset($breadcrumbe['title']) ? $breadcrumbe['title'] : '' ?></a>
						           </li>
						         <?php } ?>
						       </ol>
						   </div>
					      <?php endif;?>
            </li> 
        </ul>
        
        
        
        <ul class="nav navbar-top-links navbar-right pull-right">
        	<li><a href="javascript:void(0)" id="search-icon"><i class="icon-magnifier fa-lg"></i></a></li>
            <?php if(count($customer_email) > 0) { ?>
              <li>
                <li class="button-dropdown">
                  <a href="javascript:void(0)" class="dropdown-toggle"><i class="icon-lock  fa-lg"></i></a>
                    <ul class="dropdown-menu">
                    	<li class="p-t-10 p-l-10 p-b-10 text-uppercase font-normal" >Toggle Account</li>
                      <li class="divider" ></li>
                      <?php if(count($customer_email) > 0) {
                        foreach ($customer_email as $value) { 
                            if($value['type'] == "Customer") { ?>
                              <li><a href="<?=$ADMIN_HOST?>/switch_login.php?id=<?php echo md5($value['id']); ?>&customer=gobal" target = '_blank'>Member Portal</a></li>
                                <?php 
                                $plans_sql = "SELECT c.short_name as p_code, c.id as company_id
                                              FROM website_subscriptions as ws
                                              LEFT JOIN prd_main as pm ON (ws.product_id = pm.id)
                                              JOIN company as c ON (c.id = pm.company_id)
                                              WHERE customer_id = :customer_id AND c.id = 2 GROUP BY pm.company_id";
                                $plans_where =  array(':customer_id' => $value['id']);
                                $plans_res = $pdo->select($plans_sql, $plans_where);

                                if(count($plans_res) > 0) {?>
                                  <?php foreach ($plans_res as $row) { ?>
                                    <li><a href="<?=$ADMIN_HOST?>/switch_login.php?id=<?php echo md5($value['id']); ?>&company=<?=$value['company_id']?>" target = '_blank'><?=$row['p_code']?> Member Portal</a></li> 
                                <?php } } ?>
                            <?php } else { ?>
                              <li><a href="<?=$ADMIN_HOST?>/switch_login.php?id=<?php echo md5($value['id']); ?>" target = '_blank'><?=$value['type']?> Portal</a></li>
                            <?php }?>
                        <?php } 
                      } ?>
                    </ul>
                  </li>
              </li>
            <?php } ?>
            <?php
            include_once("header_notification.inc.php");
            ?>
            <li class="dropdown msg_notification"> 
              <a class="dropdown-toggle waves-effect waves-light" id="noti_bell" data-toggle="dropdown" href="#"><i class=" icon-bell"></i><span class="badge badge-danger" id="not_counter"></span></a>
               <ul class="dropdown-menu mailbox ">
                  <li><div class="drop-title text-left"><span class="fw500">Notifications</span> <a href="javascript:void(0);" class="pull-right clear_all_noti">Clear all</a></div>  </li>
                    <li>
                      <div class="message-center headerAllNotification" data-count=0>
                        <div class="noti_list">
                          <?=$listNotification?>
                        </div>
                      
                        <div>
                          <div class="text-center notification_loader" style="display: none"><i class="fa fa-spinner fa-spin fa-lg"></i> Loding...</div>
                          <!-- <div class="text-center notification_loader_btn" style="display:<?=$notifications_received_total>$limit?'block':'none'?>"><button class="load_more_btn"><i class="fa fa-chevron-down  fa-lg"></i></button></div> -->
                        </div>
                        
                      </div>
                    </li>                   
               </ul> 
            </li>
			  
            <?php if(isset($_SESSION['admin']['chat']) && $_SESSION['admin']['chat'] == 'true'){ ?>
             <li class="chat_notification">
                <a id="notification_chat" class="chat-access" href="javascript:void(0);">
                    <i class="icon-bubbles fa-lg"></i><span class="menu-counter badge badge-danger"></span>
                </a>
            </li>
            <?php } ?>

            <?php if (has_menu_access(2)) {?>
            <li>
                <a id="activity_history" class="chat-access" href="global_activity_history.php">
                    <i class="icon-clock"></i><span class=""></span>
                </a>
            </li>
            <?php } ?>
              
        </ul>
        <?php if (has_menu_access(2)) {?>
        <div class="searching_panel top-right">
        	<div id="searchbar_wrap">

          <?php 
            $url = 'all_users.php';
            if(!empty(get_admin_dashboard($_SESSION['admin']['id'])) && get_admin_dashboard($_SESSION['admin']['id']) == 'Support Dashboard'){
              $url = 'support_dashboard.php';
            }
          ?>  
        	<form method="GET" action="<?=$url?>" id="global_search" name="global_search" role="search" class="app-search ">
				 			<div class="search-group">
                   <input type="text" name="gsearch" id="gsearch" placeholder="Search..." size="" class="form-control gsearch">
                   <input type="hidden" name="is_ajaxed" id="g_is_ajaxed" value="false" />
                    <input type="hidden" name="pages" id="g_per_pages" value="<?=$per_page;?>" />
                    <input type="hidden" name="sort" id="g_sort_column" value="<?=isset($SortBy) ? $SortBy : '';?>" />
                    <input type="hidden" name="direction" id="g_sort_direction" value="<?=isset($SortDirection) ? $SortDirection : '';?>" />
                    <input type="hidden" name="type" id="g_type" value="" />
                    <input type="hidden" name="rep_id" id="g_rep_id" value="" />
                    <input type="hidden" name="fname" id="g_fname" value="" />
                    <input type="hidden" name="email" id="g_email" value="" />
                    <input type="hidden" name="custom_date" id="g_custom_date" value="" />
                    <input type="hidden" name="fromdate" id="g_fromdate" value="" />
                    <input type="hidden" name="todate" id="g_todate" value="" /> 
                    <button type="submit" id="all_search"><i class="fa fa-search"></i></button>
							</div>
          </form>
          </div>      
        </div>
        <?php } ?>
    </div>
</nav>
<script type="text/javascript">
    var chat_window = "";
    var chat_counter = 0;
    $(document).ready(function () {
		    $("#search-icon").popover({
            html : true,
            trigger : 'click',
            template : '<div class="popover searchpopover right" role="tooltip"><div class="arrow"></div><div class="popover-content"></div></div>',
            content: function() {
            	str = '<div id="searchbar_wrap_small">';
						  str += '<form method="GET" action="all_users.php" id="global_search_small" name="global_search_small" role="search" class="app-search ">';
							str += '<div class="search-group">';
						  str += '<input type="text" name="gsearch" id="gsearch_small" placeholder="Search..." size="" class="form-control gsearch">';

	            str +=  '<input type="hidden" name="is_ajaxed" id="g_is_ajaxed_small" value="false" />';
	            str +=  '<input type="hidden" name="pages" id="g_per_pages_small" value="<?= isset($per_page)?$per_page:0;?>" />';
	            str +=  '<input type="hidden" name="sort" id="g_sort_column_small" value="<?=isset($SortBy) ? $SortBy:"";?>" />';
	            str +=  '<input type="hidden" name="direction" id="g_sort_direction_small" value="<?=isset($SortDirection)?$SortDirection:"";?>" />';
	            str +=  '<input type="hidden" name="type" id="g_type_small" value="" />';
	            str +=  '<input type="hidden" name="rep_id" id="g_rep_id_small" value="" />';
	            str +=  '<input type="hidden" name="fname" id="g_fname_small" value="" />';
	            str +=  '<input type="hidden" name="email" id="g_email_small" value="" />';
	            str +=  '<input type="hidden" name="custom_date" id="g_custom_date_small" value="" />';
	            str +=  '<input type="hidden" name="fromdate" id="g_fromdate_small" value="" />';
	            str +=  '<input type="hidden" name="todate" id="g_todate_small" value="" />' ;
	            str +=  '<button type="submit" id="all_search_small"><i class="fa fa-search"></i></button>';
							str +=	'</div>';
						  str +=  '</form>';
						  str +=  '</div>';

                return str;
            },
            placement: 'bottom',
            container: '.navbar-right'
        }); 
				
				 $('html').on('click', function (e) {
					 $('#search-icon').each(function () {
					 // hide any open popovers when the anywhere else in the body is clicked
						 if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
							$(this).popover('hide');
						 }
					 });
				 });

        $('#notification_chat').click(function () {

            // var operatorid = '<?=isset($_SESSION['operator']['operatorid']) ? $_SESSION['operator']['operatorid'] : 0?>';
            
            // $.ajax({
            //    url: 'ajax_check_chat_status.php',
            //    type: 'POST',
            //    data: {operatorid: operatorid},
            //    success: function(res) {
            //      if (res.status == "success") {
            //        window.open('new_window_chat_queue.php', "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=0, left=100, width=550, height=480")
            //      }else if(res.status == "fail"){
            //         setNotifyError('You are offline! Please Turn On chat');
            //      }
            //    }
            // });

            window.open('circle_chat.php', "myWindow", "width=900,height=600");
        });

        $("#g_search").click(function () {
            $('#g_search').attr('placeholder', 'Type Name, Id, Email Or Phone to search');
        });

    });
    function notifyMe(user_name,message) {
          // Let's check if the browser supports notifications
          if (!("Notification" in window)) {
            alert("This browser does not support desktop notification");
          }

          // Let's check if the user is okay to get some notification
          else if (Notification.permission === "granted") {
            // If it's okay let's create a notification
          var options = {
                body: message,
                icon: "images/Chat-icon.png",
                dir : "ltr"
            };
          var notification = new Notification(user_name,options);
          }

          // Otherwise, we need to ask the user for permission
          // Note, Chrome does not implement the permission static property
          // So we have to check for NOT 'denied' instead of 'default'
          else if (Notification.permission !== 'denied') {
            Notification.requestPermission(function (permission) {
              // Whatever the user answers, we make sure we store the information
              if (!('permission' in Notification)) {
                Notification.permission = permission;
              }

              // If the user is okay, let's create a notification
              if (permission === "granted") {
                var options = {
                      body: message,
                      icon: "images/Chat-icon.png",
                      dir : "ltr"
                  };
                var notification = new Notification(user_name,options);
              }
            });
          }

          // At last, if the user already denied any notification, and you
          // want to be respectful there is no need to bother them any more.
        }
    setInterval(function () {

        //Get new chat notification

      
    }, 18000);

    $(this).find('input').keypress(function(e) {
        // Enter pressed?
        if(e.which == 10 || e.which == 13) {
            this.form.submit();
        }
    });

    
    $(document).on('click', '#all_search', function () {
        //var g_search = $("#gsearch").val();
        
        var g_search = $("#gsearch").val();
        if (g_search == "" || g_search == null) {
            alert("Enter Name,Id,Email Or Phone to search user");
            return false;
        } else {
            $("#g_is_ajaxed").val("0");
            $("#gsearch").val(g_search);
            $("#global_search").submit();
        }

    });
    $(document).on('click', '#all_search_small', function () {
        //var g_search = $("#gsearch").val();
        
        var g_search = $("#gsearch_small").val();
        if (g_search == "" || g_search == null) {
            alert("Enter Name,Id,Email Or Phone to search user");
            return false;
        } else {
            $("#g_is_ajaxed").val("0");
            $("#gsearch").val(g_search);
            $("#global_search").submit();
        }

    });

<?php /*
    function change_company(company_id) {
        if (company_id == 2) {
            $("#hd_company_id").val("");
            var admin_id = '<?php echo md5($_SESSION['admin']['id']) ?>';
            var myhealthhost = '<?php echo isset($MYHEALTH_HOST)?$MYHEALTH_HOST:""; ?>';
            window.open(myhealthhost + "/admin/switch_admin_login.php?id=" + admin_id, "_blank");
        } else if (company_id == 4) {
            $("#hd_company_id").val("");
            var admin_id = '<?php echo md5($_SESSION['admin']['id']) ?>';
            var seniorhealthhost = '<?php echo isset($MYSENIORHEALTH_HOST)?$MYSENIORHEALTH_HOST:""; ?>';
            window.open(seniorhealthhost + "/admin/switch_admin_login.php?id=" + admin_id, "_blank");
        } else if (company_id == 5) {
            $("#hd_company_id").val("");
            var admin_id = '<?php echo md5($_SESSION['admin']['id']) ?>';
            var hoorayhealthhost = '<?php echo isset($HOORAYHEALTH_HOST)?$HOORAYHEALTH_HOST:""; ?>';
            window.open(hoorayhealthhost + "/admin/switch_admin_login.php?id=" + admin_id, "_blank");
        } else if (company_id == 6) {
            $("#hd_company_id").val("");
            var admin_id = '<?php echo md5($_SESSION['admin']['id']) ?>';
            var agentrahealthhost = '<?php echo isset($AGENTRAHEALTH_HOST)?$AGENTRAHEALTH_HOST:""; ?>';
            window.open(agentrahealthhost + "/admin/switch_admin_login.php?id=" + admin_id, "_blank");
        }

        /* $.ajax({
         url: 'ajax_change_company.php',
         data: {"company_id":company_id},
         type: 'post',
         success:function(data){
         window.location.reload();
         }
         //}); 
    } 
    */ ?>
    function open_chat_window(url) {
        if (!chat_window.opener) {
            chat_window = window.open(url, "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=0, left=100, width=900, height=600");
        } else {
            chat_window.load_new_tab(url);
        }
        chat_window.focus();
    }


    jQuery(document).ready(function (e) {
      function t(t) {
        e(t).bind("click", function (t) {
          t.preventDefault();
          e(this).parent().fadeOut()
        })
      }
      e(".dropdown-toggle").click(function () {
        var t = e(this).parents(".button-dropdown").children(".dropdown-menu").is(":hidden");
        e(".button-dropdown .dropdown-menu").hide();
        e(".button-dropdown .dropdown-toggle").removeClass("active");
        if (t) {
          e(this).parents(".button-dropdown").children(".dropdown-menu").toggle().parents(".button-dropdown").children(".dropdown-toggle").addClass("active")
        }
      });
      e(document).bind("click", function (t) {
        var n = e(t.target);
        if (!n.parents().hasClass("button-dropdown")) e(".button-dropdown .dropdown-menu").hide();
      });
      e(document).bind("click", function (t) {
        var n = e(t.target);
        if (!n.parents().hasClass("button-dropdown")) e(".button-dropdown .dropdown-toggle").removeClass("active");
      })
    });
</script>
<script type="text/javascript">
  trigger = function(e, r, i) {
    "undefined" == typeof i && (i = "click"), $(document).off(i, e), $(document).on(i, e, function(e) {
      r($(this), e)
    })
  };
  $isProcessing = 0;
  trigger('#noti_bell', function() {
    $.ajax({
        url: "<?=$ADMIN_HOST?>/getnotification.php?noti=1",
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'noti'
        },
      })
      .done(function(data) {
        if ($(".headerAllNotification").length > 0) {
          if (data.code == 200) {
            $(".headerAllNotification .noti_list").html(data.html);
            $(".headerAllNotification").mCustomScrollbar({
              theme: "dark",
              mouseWheel: {
                preventDefault: true,
                scrollAmount: 150
              },
              callbacks: {
                // onScrollStart:function(){ console.log("on start"); },
                // onScroll:function(){console.log("on scroll"); },
                onTotalScroll: function() {
                  loadMoreData()
                },
                // onTotalScrollBack:function(){ console.log("on total scroll back"); },
                onTotalScrollOffset: 100,
                // onTotalScrollBackOffset:20,
                // whileScrolling:function(e){ mcs } 
              }
            });
            //$('#not_counter').html('');
          }
        }
      });
  });
  loadMoreData = function() {
    $(".notification_loader").show();
    if ($isProcessing == 0) {
      $isProcessing = 1;
      // $(".notification_loader_btn").hide();
      $lastId = $(".headerAllNotification .noti_list").find(".notification_full").last().attr("data-noti");
      if ($(".headerAllNotification").attr("data-count") < 0) return;
      $.ajax({
        url: "<?=$ADMIN_HOST?>/getnotification.php?loadmore=1",
        type: 'POST',
        dataType: 'json',
        beforeSend: function() {
          // $(".notification_loader_btn").hide();
          $(".notification_loader").show();
          console.log("Show Loader");
        },
        data: {
          action: 'noti_remaining',
          lid: $lastId //pass last id to ajax
        },
      }).done(function(data) {
        $isProcessing = 0;
        // $(".notification_loader_btn").show();
        if (data.code == 200) {
          if (data.count > 0) {
            $(".headerAllNotification .noti_list").append(data.html);
          }
          if (data.count < data.limit) {
            $(".headerAllNotification").attr("data-count", -1);
            // $(".notification_loader_btn").hide();
          }
        }
        setTimeout(function() {
          $(".notification_loader").hide();
        }, 2000);
        console.log("Hide Loader");
      });
    }
  };
  trigger(".redirectNotification", function($this, e) {
    $ref = $this.parents(".notification_full");
    $href = $ref.attr("data-href");
    if ($ref.attr("data-colorbox")) {
      $.colorbox({
        href: $href,
        iframe: true,
        width: '900px',
        height: '550px'
      });
    } else {
      window.location.href = $href;
    }
  });
  trigger(".clear_all_noti", function($this, e) {
    e.stopPropagation();
    $.ajax({
        url: "<?=$ADMIN_HOST?>/getnotification.php?allopen=1",
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'allClear'
        },
      })
      .done(function(data) {
        $(".headerAllNotification .noti_list").html('<p class="text-center">You\'re all caught up and have no alerts.</p>');
        // $(".notification_loader_btn").hide();
        $(".notification_loader").hide();
      });
  });
  trigger('ul.dropdown-menu.mailbox .remove_notification', function($this, e) {
    e.stopPropagation();
    // alert($this.parents(".notification_full").attr("data-noti"));
    $.ajax({
        url: "<?=$ADMIN_HOST?>/getnotification.php?hidenoti=1",
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'clearNoti',
          id: $this.parents(".notification_full").attr("data-noti")
        },
      })
      .done(function(data) {
        if ($(".headerAllNotification").length > 0) {
          if (data.code == 200) {
            $this.parent(".notification_full").fadeOut("slow", function() {
              if ($(".headerAllNotification").find(".notification_full").length == 0) {
                $(".headerAllNotification .noti_list").append('<p class="text-center">You\'re all caught up and have no alerts.</p>');
              }
            });
          }
        }
      });
  });
  $(function() {
    getNoti();
    setInterval(getNoti, 40000);
    <?php if(has_menu_access(66)) { ?>
      getStateOfChatHeader();
      setInterval(getStateOfChatHeader, 45000);
    <?php } ?>
  });
  $ajaxCallNoti = true;
  getNoti = function() {
    if ($ajaxCallNoti == false) {
      return;
    }
    $.ajax({
        url: "<?=$ADMIN_HOST?>/getnotification.php?get=1",
        type: 'POST',
        dataType: 'json',
        beforeSend: function() {
          $ajaxCallNoti = false;
        },
        data: {
          action: 'get'
        },
        success:function(data){
          $ajaxCallNoti = true;
          if ($(".headerAllNotification").length > 0) {
            if (data.code == 200) {
              $('#not_counter').html(data.notificationCount > 0 ? data.notificationCount : "");
            }
          }
        }
      });
  };
<?php if(has_menu_access(66)) { ?>
  function getStateOfChatHeader() {
    $.ajax({
      type: "POST",
      url: "circle_chat.php",
      data: {is_get_chat_message:1},
      dataType: "json",	
      success: function(res) {
        // if(res.status === "true"){
          $(".menu-counter").html(res.total_message);
        // }
      }
    });
  }
<?php } ?>
</script>