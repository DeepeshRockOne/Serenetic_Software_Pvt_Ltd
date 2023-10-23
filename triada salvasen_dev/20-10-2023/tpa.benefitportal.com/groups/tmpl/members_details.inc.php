<div class="section-padding">
  <div class="container"> 
    <div class="member_profile">
       <div class="row">
          <div class="col-md-4">
             <div class="panel panel-default profile-info member <?=$status_class?>">
                <div class="panel-header">
                   <div class="media">
                      <div class="media-body">
                         <h4 class="mn"><?=$row['fname'].' '.$row['lname']?> - <small><?=$row['rep_id']?></small></h4>
                      </div>
                      <div class="media-right">
                         <div class="dropdown">
                            <button class="btn btn-white text-black text-left dropdown-toggle" type="button" data-toggle="dropdown"><?=get_member_display_status($row['status']);?> &nbsp; &nbsp;<span class="fa fa-sort text-red "></span></button>
                            <ul class="dropdown-menu">
                              <li><a href="javascript:void(0);" class="member_status <?=$row['status'] == 'Active' ? 'hidden' : '' ?>" data-status="Active">Active</a></li>
                              <li><a href="javascript:void(0);" class="member_status <?=$row['status'] == 'Hold' ? 'hidden' : '' ?>" data-status="Hold">On Hold</a></li>
                              <?php /*<li><a href="javascript:void(0);" class="member_status <?=$row['status'] == 'Post Payment' ? 'hidden' : '' ?>" data-status="Post Payment">Pending</a></li> */ ?>
                              <li><a href="javascript:void(0);" class="member_status <?=$row['status'] == 'Inactive' ? 'hidden' : '' ?>" data-status="Inactive">Inactive</a></li>
                            </ul>
                         </div>
                      </div>
                   </div>
                </div>
                <div class="panel-body">
                   <div class="table-responsive" >
                      <table width="100%">
                         <tr>
                            <td>Address:</td>
                            <td><?=$row['address'].'<br>'.$row['city'].', '.checkIsset($allStateShortName[$row['state']]).' '.$row['zip']?></td>
                         </tr>
                         <tr>
                            <td>DOB:</td>
                            <td><?=displayDate($row['birth_date'])?> (<?=calculateAge($row['birth_date'])?>)</td>
                         </tr>
                         <tr>
                            <td>Gender:</td>
                            <td><?=$row['gender']?></td>
                         </tr>
                         <tr>
                            <td>Phone:</td>
                            <td><?=format_telephone($row['cell_phone'])?></td>
                         </tr>
                         <tr>
                            <td>Email:</td>
                            <td><?=$row['email']?></td>
                         </tr>
                         <tr>
                            <td>Password:</td>
                            <td>
                               <div class="password_unlock">
                                  <div style="display:none" id="password_popup">
                                     <div class="phone-control-wrap">
                                        <div class="phone-addon"><input type="password" class="form-control" name="" id="showing_pass"></div>
                                        <div class="phone-addon w-65"><button class="btn btn-info" id="show_password">Unlock</button></div>
                                     </div>
                                  </div>
                                  <div>
                                     <input type="password" value="<?=base64_encode($password)?>" id="ad_password" disabled="disabled" class="dot_password" size="12" maxlength="12">
                                     <a href="javascript:void(0);" id="click_to_show"><i class="fa fa-eye fa-lg"></i></a>
                                  </div>
                               </div>
                            </td>
                         </tr>
                      </table>
                   </div>
                </div>
                <div class="panel-footer clearfix "> 
                  <a data-toggle="tooltip" href="switch_login.php?id=<?php echo $row['id']; ?>" target="_blank"  title="Access Member Site">Member Portal&nbsp;&nbsp;<i class="fa fa-lock"></i></a>
                </div>
             </div>
          </div>
          <div class="col-md-8">
             <div class="panel panel-default member_intrection_wrap">
               <div class="ajex_loader" id="intrection_loader" style="display: none;">
                  <div class="loader"></div>
               </div>
                <div class="panel-body">
                   <div class="clearfix ">
                      <ul class="nav nav-tabs tabs customtab  pull-left" role="tablist">
                         <li role="presentation" class="active interaction_class"><a href="#interactions_tab" aria-controls="interactions_tab" role="tab" data-toggle="tab">Interactions</a></li>
                         <li role="presentation" class="note_class" onclick="$('.note_div').show();"><a href="#note_tab" aria-controls="note_tab" role="tab" data-toggle="tab">Notes</a></li>
                         <li role="presentation" class="communication_class" onclick="$('.note_div').hide();"><a href="#communication_tab" aria-controls="communication_tab" role="tab" data-toggle="tab">Communications</a></li>
                      </ul>
                      <div class="text-right interaction_div">
                        <a href="#" class="search_btn " id="srh_btn_interaction"><i class="fa fa-search fa-lg text-blue"></i></a>
                        <a href="#" class="search_btn search_close_btn" id="srh_close_btn_interaction" style="display:none;" ><i class="text-light-gray ">X</i></a>
                        <?php /*<a class="btn btn-action  m-l-8 members_interaction_add" data-href="members_interaction_add.php?memberId=<?=$row['id']?>"><strong>+ Interaction</strong></a>*/ ?>
                        <div class="clearfix"></div>
                        <div class="note_search_wrap" id="search_div" style="display:none;">
                              <div class="phone-control-wrap">
                                 <div class="phone-addon">
                                    <input type="text" class="form-control" id="interaction_search_keyword"  placeholder="Serach Notes, Keywords, Timestamp, etc." />
                                 </div>
                                 <div class="phone-addon w-80">
                                    <button href="javascript:void(0);" class="btn btn-action-o" id="search_btn_interaction">Search</button>
                                 </div>
                              </div>
                        </div>
                      </div>
                      <div class="text-right note_div" style="display:none">
                         <a class="btn btn-action pull-right m-l-8 account_note_popup_new" data-href="account_note.php?id=<?=$_GET['id']?>&type=Customer"><strong>+ Notes</strong></a>
                         <!-- <a href="#" class="search_btn pull-right m-l-8"><i class="fa fa-external-link fa-lg text-blue"></i></a> -->
                         <a href="#" class="search_btn pull-right" id="srh_btn_note"><i class="fa fa-search fa-lg text-blue"></i></a>
                         <a href="#" class="search_btn search_close_btn" id="srh_close_btn_note" style="display:none;" ><i class="text-light-gray ">X</i></a>
                         <div class="clearfix"></div>
                         <div class="note_search_wrap" id="search_note" style="display:none;">
                            <div class="phone-control-wrap">
                               <div class="phone-addon">
                                  <input type="text" class="form-control" id="note_search_keyword" name="note_search_keyword"  placeholder="Serach Notes, Keywords, Timestamp, etc." />
                               </div>
                               <div class="phone-addon w-80">
                                  <button href="javascript:void(0);" class="btn btn-action-o" id="search_btn_note" >Search</button>
                               </div>
                            </div>
                         </div>
                      </div>
                   </div>
                   <div class="tab-content">
                      <div role="tabpanel" class="tab-pane active pn" id="interactions_tab">
                        <div class="activity_wrap">
                           <?php if(!empty($interaction) && count($interaction) > 0){ foreach($interaction as $int) {?>
                           <div class="media">
                                 <div class="media-body fs14 br-n">
                                 <p class="fw500 text-primary mn"><?=ucwords($int['type'])?></p>
                                    <p class="text-light-gray mn"><?=$tz->getDate($int['created_at'],'D., M. d, Y @ h:i A')?></p>
                                    <p class="mn"><?=custom_charecter($int['description'],400,$int['admin_name'],$int['display_id'],$int['admin_id'])?> </p>
                                 </div>
                                 <div class="media-right text-nowrap">
                                 <a href="javascript:void(0)" data-href="<?=$HOST?>/interactions_note.php?id=<?=md5($int['int_id'])?>&type=view&userType=member" class="interactions_note"><i class="fa fa-eye fa-lg"></i></a> &nbsp;
                                 </div>
                           </div>
                           <?php } }else echo '<p class="text-center mn"> Add first interaction by clicking the ‘+ Interaction’ button above </p>'; ?>
                        </div>
                      </div>
                      <div role="tabpanel" class="tab-pane" id="note_tab">
                         <div class="activity_wrap">
                           <?php if(!empty($notes) && count($notes) > 0){ foreach($notes as $note) {?>
                              <div class="media">
                                 <div class="media-body fs14 br-n">
                                       <p class="text-light-gray mn"><?=$tz->getDate($note['created_at'],'D., M. d, Y @ h:i A')?></p>
                                       <p class="mn"><?=note_custom_charecter('group','customer',$note['description'],400,$note['admin_name'],$note['display_id'],$note['admin_id'],$ADMIN_HOST.'/'.$note['url'].'?id='.md5($note['admin_id']))?></p>
                                 </div>
                                 <div class="media-right text-nowrap">
                                    <!-- <a href="javascript:void(0);"><i class="fa fa-edit fa-lg"></i></a> &nbsp;
                                    <a href="javascript:void(0);" ><i class="fa fa-trash fa-lg"></i></a> &nbsp;
                                    <a href="javascript:void(0);" ><i class="fa fa-eye fa-lg"></i></a> -->
                                    <a href="javascript:void(0);" class="" id="edit_note_id" data-original-title="Edit" onclick="edit_note_agent(<?=$note['note_id']?>,'view')" data-value="Customer"><i class="fa fa-eye fa-lg"></i></a> &nbsp;
                                    <?php 
                                       if($_SESSION['groups']['id'] == $note['admin_id']){
                                    ?>
                                    <a href="javascript:void(0);" class="" id="edit_note_id" data-original-title="Edit" onclick="edit_note_agent(<?=$note['note_id']?>)" data-value="Customer"><i class="fa fa-edit fa-lg"></i></a> &nbsp;
                                    <a href="javascript:void(0);" class="" id="delete_note_id" data-original-title="Delete" onclick="delete_note(<?=$note['note_id']?>,<?=$note['ac_id']?>)"><i class="fa fa-trash fa-lg"></i></a>&nbsp;
                                    <?php } ?>
                                 </div>
                              </div>
                           <?php } } else echo '<p class="text-center mn"> Add first note by clicking the ‘+ Claims’ button above </p>'; ?>
                         </div>
                      </div>
                      <div role="tabpanel" class="tab-pane " id="communication_tab">
                         <div class="theme-form">
                            <?php /*<div class="form-group height_a">
                               <select class="form-control" data-live-search="true" name="select_communication" id="select_communication" onchange="changeCommunication($(this))">
                                 <option value=""></option>
                                 <?php if(!empty($email_list)){
                                    $i=1;
                                    foreach($email_list as $email){ ?> 
                                    <option value="<?=$email['id']?>" data-type="<?=$email['type']?>"><?=$email['display_id'].' : '.$email['title']?></option>
                                 <?php $i++; }} ?>
                               </select>
                               <label>Select</label>
                            </div>
                            <div class="form-group">
                               <!-- <a href="members_send_trigger.php" class="btn btn-info members_send_trigger">View</a>
                               <a href="javascript:void(0);" class="btn btn-action">Email</a>
                               <a href="javascript:void(0);" class="btn btn-action-o">Text</a> -->
                              <a href="javascript:void(0);" class="btn btn-info members_send_trigger send_communication">View</a>
                              <a href="javascript:void(0);" class="btn red-link send_communication" id="send_email" onclick="sendEmailSMS($(this),'Email')">Quick Email</a>
                              <a href="javascript:void(0);" class="btn red-link send_communication" id="send_sms" onclick="sendEmailSMS($(this),'SMS')">Quick Text</a>
                            </div> */ ?>
                            <div id="trigger_list">
                            </div>
                         </div>
                      </div>
                   </div>
                </div>
             </div>
          </div>
       </div>
       <div class="panel panel-default panel-block">
          <div class="panel-body">
             <ul class="nav nav-tabs tabs  customtab  fixed_tab_top" role="tablist">
                <li class="active" role="presentation" ><a href="#products_tab" data-toggle="tab" onclick="scrollToDiv($('#products_tab'),0,'','products_tab');" aria-expanded="true">Products</a>
                </li>
                <li role="presentation" ><a href="#policy_tab" data-toggle="tab" onclick="scrollToDiv($('#policy_tab'), 0,'member_policy_tab.php','policy_tab');" aria-expanded="true">Primary Plan Holder</a>
                </li>
                <li role="presentation" ><a href="#dependents_tab" data-toggle="tab" onclick="scrollToDiv($('#dependents_tab'), 0,'member_depedents_tab.php','dependents_tab');">Dependents</a>
                </li>
                <?php if($sponsor_billing_method == "individual") { ?>
                   <li role="presentation" ><a href="#billing_tab" data-toggle="tab" onclick="scrollToDiv($('#billing_tab'), 0,'member_billing_tab.php','billing_tab');" aria-expanded="true">Billing Profile</a>
                   </li>
                   <li role="presentation" ><a href="#orders_tab" data-toggle="tab" onclick="scrollToDiv($('#orders_tab'), 0,'member_orders_tab.php','orders_tab');" aria-expanded="true">Orders</a>
                   </li>
                <?php } ?>
                <?php if($isGapPrd){?>
                   <li role="presentation" ><a href="#hrm_tab" data-toggle="tab" onclick="scrollToDiv($('#hrm_tab'), 0,'member_hrm_tab.php','hrm_tab');" aria-expanded="true">GAP+ Info</a>
                   </li>
                <?php } ?>
                <li role="presentation" ><a href="#health_tab" data-toggle="tab" onclick="scrollToDiv($('#health_tab'), 0,'member_health_tab.php','health_tab');" aria-expanded="true">Health Details</a>
                </li>
             </ul>
             <div class="m-t-20">
                <div role="tabpanel" class="tab-pane active" id="products_tab">
                  <?php include ('tmpl/member_products_tab.inc.php'); ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="policy_tab"></div>
                <div role="tabpanel" class="tab-pane" id="dependents_tab"></div>
                <div role="tabpanel" class="tab-pane" id="billing_tab"></div>
                <div role="tabpanel" class="tab-pane" id="orders_tab"></div>
                <div role="tabpanel" class="tab-pane" id="hrm_tab"></div>
                <div role="tabpanel" class="tab-pane" id="health_tab"></div>
              <?php /*<div role="tabpanel" class="tab-pane" id="activity_history_tab"></div>*/ ?>
             </div>
          </div>
       </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
      if ($(window).width() >= 1171) {
        $(window).scroll(function() {
        if ($(this).scrollTop() > 610) {
           $('.fixed_tab_top').addClass('fixed');
        } else {
           $('.fixed_tab_top').removeClass('fixed');
        }
     });
     }
     changeCommunication();
     $(".send_communication").hide();
     
   //  $(".members_send_trigger").colorbox({iframe: true, width: '1024px', height: '767px'});
    $(".members_request_access").colorbox({iframe: true, width: '615px', height: '350px'});
    $(".id_card_popup").colorbox({iframe: true, width: '1024px', height: '500px'});
    $(".address_change").colorbox({iframe: true, width: '585px', height: '330px'});
    $(".depedents_active_product").colorbox({iframe: true, width: '360px', height: '330px'});
    $(".edit_depedents").colorbox({iframe: true, width: '768px', height: '450px'});
    $(".delete_depedents").colorbox({iframe: true, width: '567px', height: '185px'});
    $(".edit_billing_profile").colorbox({iframe: true, width: '768px', height: '675px'});
    $(".make_payment").colorbox({iframe: true, width: '768px', height: '530px'});


   $('#srh_btn_interaction').click(function(e){
       e.preventDefault(); //to prevent standard click event
           $(this).hide();
           $("#srh_close_btn_interaction").show();
           $("#search_div").slideDown();    
           $('.activity_wrap').addClass('interaction_filter_active');
           $('.activity_wrap').mCustomScrollbar("update");
      });
   $('#srh_close_btn_interaction').click(function(e){
         e.preventDefault(); //to prevent standard click event
         $(this).hide();
            $("#search_div").slideUp();
            $("#srh_close_btn_interaction").hide();
            $("#srh_btn_interaction").show();
            $('.activity_wrap').removeClass('interaction_filter_active');
            $('.activity_wrap').mCustomScrollbar("update");
            $("#interaction_search_keyword").val('');

         $("#ajax_loader").show();
         var id = '<?=$_GET['id']?>';

         $.ajax({
            url:'members_details.php?id='+id,
            data:{interaction_ajax:'Y',id:id},
            method:'post',
            dataType: 'html',
            success:function(res){
               $("#ajax_loader").hide();
               $("#interactions_tab").html(res);
               $(".activity_wrap").mCustomScrollbar({
               theme:"dark"
               });
            }
         });
   });

  $('#srh_btn_note').click(function(e){
      e.preventDefault(); //to prevent standard click event
      $(this).hide();
      $("#srh_close_btn_note").show();
      $("#search_note").slideDown();
      $('.activity_wrap').addClass('interaction_filter_active');
      $('.activity_wrap').mCustomScrollbar("update");     
   });

  $('#srh_close_btn_note').click(function(e){
      e.preventDefault(); //to prevent standard click event
      $("#search_note").slideUp();
      $("#srh_close_btn_note").hide();
      $("#srh_btn_note").show();
      $('.activity_wrap').removeClass('interaction_filter_active');
      $('.activity_wrap').mCustomScrollbar("update");
      $("#note_search_keyword").val('');

      $("#ajax_loader").show();
      var id = '<?=$_GET['id']?>';
      $.ajax({
         url:'members_details.php?id='+id,
         data:{note_ajax:'Y',id:id},
         method:'post',
         dataType: 'html',
         success:function(res){
            $("#ajax_loader").hide();
            $("#note_tab").html(res);
            $(".activity_wrap").mCustomScrollbar({
            theme:"dark"
            });
         }
      });
   });

  $(".activity_wrap").mCustomScrollbar({
      theme:"dark"
  });

   $(document).off('click',".account_note_popup_new");
   $(document).on('click',".account_note_popup_new",function(){
      $href = $(this).attr('data-href');
      var not_win = window.open($href, "myWindow", "width=500,height=600");
   });
});

$(document).off('click','.interactions_note');
$(document).on('click','.interactions_note',function(e){
   var $href = $(this).attr('data-href');
   $.colorbox({
      href:$href,
      iframe: true,
      width: '800px',
      height: '500px'
      });
});
  /*scroll div function start */
  function scrollToDiv(element, navheight,url,ajax_div) {
      var str = $("#"+ajax_div).html().trim();
      if(str === '' && url!==''){
         ajax_get_member_data(url,ajax_div,'');
      }
      if ($(element).length) {
         var offset = element.offset();
         var offsetTop = offset.top;
         var totalScroll = offsetTop - navheight;
         if ($(window).width() >= 1171) {
            var totalScroll = offsetTop - $("nav.navbar-default").outerHeight() - 42
         } else {
            var totalScroll = offsetTop - $("nav.navbar-default ").outerHeight() - 42
         }
         $('body,html').animate({
            scrollTop: totalScroll
         }, 1200);
      }
   }

   ajax_get_member_data = function(url,ajax_div,newid){
      var id = '<?=$_GET['id']?>';
      if(newid !== '' && newid !== undefined){
         id = newid;
      }
    $.ajax({
      url : url,
      type : 'POST',
      data:{
        id:id
      },
      beforeSend :function(e){
        $("#ajax_loader").show();
      },
      success : function(res){
        $("#ajax_loader").hide();
        $("#"+ajax_div).html(res);
        fRefresh();
        common_select();
        $('.change_default').uniform();
      }
    });
  }

// request access 
$("#request_access_popup").click(function(){
   swal({
          //title: "Are you sure ",
          text: "Unlock Section: Are you sure?",
          //type: "warning",
          showCancelButton: true,
          //confirmButtonColor: "#DD6B55",
          confirmButtonText: "Confirm",
          //showCloseButton: true
        });
});


//member status code start
$(document).off('click', '.member_status');
$(document).on("click", ".member_status", function(e) {
   e.stopPropagation();
   var id = '<?=$_GET['id']?>';
   var member_status = $(this).attr('data-status');
   swal({
      text: "Change Status: Are you sure?",
      showCancelButton: true,
      confirmButtonText: "Confirm",
   }).then(function() {
      $.ajax({
         url: 'change_member_status.php',
         data: {
            id: id,
            status: member_status
         },
         method: 'POST',
         dataType: 'json',
         beforeSend : function(e){
            $("#ajax_loader").show();
         },
         success: function(res) {
            $("#ajax_loader").hide();
            if (res.status == "success") {
               setNotifySuccess(res.msg);
               location.reload();
            }else{
               setNotifyError(res.msg);
            }
         }
      });
   }, function(dismiss) {
   })
});
//member status code end


$(document).off('click','#click_to_show');
$(document).on('click','#click_to_show',function(){
   if($("#ad_password").attr('type') === 'password')
      $("#password_popup").show();
   else{
      $("#password_popup").hide();
      $("#ad_password").attr('type','password');
      $("#ad_password").val('<?=base64_encode($password)?>');
   }
});

$(document).off("click",".interaction_class");
$(document).on("click",".interaction_class",function(e){
   $(".note_div").hide();
   $(".interaction_div").show();
});

$(document).off("click",".note_class");
$(document).on("click",".note_class",function(e){
   $(".interaction_div").hide();
   $(".note_div").show();
});

$(document).off('click','#show_password');
$(document).on('click','#show_password',function(){
      if($("#showing_pass").val() === '5401')
      {
      $("#ajax_loader").show();
      $("#showing_pass").val("");
      $("#password_popup").hide();
      var id = '<?=$_GET['id']?>';
      $.ajax({
         url:'members_details.php',
         method : 'POST',
         data : {id:id,show_pass:"show_pass"},
         success:function(){
            $("#ajax_loader").hide();
            $("#ad_password").attr('type','text');
            $("#ad_password").val('<?=$password?>');
         }
      });
      }else{
         $("#password_popup").hide();
      }
   });


$(function() {
     $('.member_intrection_wrap').matchHeight({
         target: $('.profile-info')
     });
});

$(document).off('click','#search_btn_note');
$(document).on('click','#search_btn_note',function(){
   $("#ajax_loader").show();
   var note_search_keyword = $("#note_search_keyword").val();
   var id = '<?=$_GET['id']?>';
   if(note_search_keyword!==''){
   $.ajax({
   url:'members_details.php?id='+id,
   data:{note_search_keyword:note_search_keyword,id:id},
   method:'post',
   dataType: 'html',
   success:function(res){
      $("#ajax_loader").hide();
      $("#note_tab").html(res);
      $(".activity_wrap").mCustomScrollbar({
      theme:"dark"
      });
   }
   });
   }else{
   alert("Please Enter Search Keyword(s)");
   $("#ajax_loader").hide();
   }
});

function edit_note_agent(note_id, t) {
   var user_type = $("#edit_note_id").attr("data-value");
   var show = "";
   if(t === 'view')
   {
   show = "show";
   }
   var customer_id = '<?=$_GET['id']?>';
   url = "members_details.php";
   if (user_type == 'View' || user_type == 'Customer') {
   // $.colorbox({
   //    iframe: true,
   //    width: '800px',
   //    height: '400px',
   //    href: "account_note.php?id=" + customer_id + "&note_id=" + note_id + "&type=" + user_type +"&show="+show
   // });
   var $href = "account_note.php?id=" + customer_id + "&note_id=" + note_id + "&type=" + user_type + "&show=" + show
   window.open($href, "myWindow", "width=500,height=580");
   } else {
   window.location.href = url + "?id=" + '<?=$_GET['id']?>' +"&note_id=" + note_id;
   }
}
function delete_note(note_id, activity_feed_id) {
   var id = '<?=$_REQUEST['id']?>';
   var url = "";
   url = "members_details.php";
   swal({
   text: "Delete Record: Are you sure?",
   showCancelButton: true,
   confirmButtonText: "Confirm",
   }).then(function () {
   $.ajax({
      url: 'ajax_general_note_delete.php',
      data: {
         note_id: note_id,
         activity_feed_id: activity_feed_id,
         usertype:'Customer',
         user_id :id,
      },
      dataType: 'json',
      type: 'post',
      success: function (res) {
         if (res.status == "success") {
         // window.location = url + '?id=' + id;
         interactionUpdate(id,'notes','members_details.php','groups');
         setNotifySuccess('Note deleted successfully.');
         }
      }
   });
   }, function (dismiss) {

   });
}

// Communication code start
   $(document).off('click','.members_send_trigger');
   $(document).on('click','.members_send_trigger',function(e){
      e.preventDefault();
      var $name = $(this).attr('data-name');
      var $id = $(this).attr('data-id');
      var member_id = '<?=$_GET['id']?>';
      var $href = 'members_send_trigger.php?member_id='+member_id+'&id='+$id+'&name='+$name;
      $.colorbox({
         iframe: true,
         href:$href,
         width: '1024px', 
         height: '767px'
      });
   });

   $(document).off('click', '.emailer_content');
   $(document).on('click', '.emailer_content', function (e) {
      e.preventDefault();
      $.colorbox({
         href: $(this).attr('href'),
         iframe: true, 
         width: '900px', 
         height: '580px'
      });
   });
   
   function changeCommunication(){
      $("#ajax_loader").show();
      var id = '<?=$_GET['id']?>';
      $.ajax({
         url:'members_details.php?id='+id,
         data:{id:id,communication_ajax:'Y'},
         method:'post',
         dataType: 'html',
         success:function(res){
            $("#ajax_loader").hide();
            $("#trigger_list").html(res);
            $(".activity_wrap").mCustomScrollbar({
            theme:"dark"
            });
            $('[data-toggle="tooltip"]').tooltip();
            $('#communication_table').bootstrapTable().removeClass("table-hover");
            $('#communication_table').bootstrapTable(
               'resetView',{height:310}
            );
         }
      });
      // var $value = element.val();
      // var $type = element.find(':selected').attr('data-type');
      // var $name = element.find(':selected').text();
      // $(".members_send_trigger").show();
      // $(".send_communication").attr('data-id',$value);
      // $(".send_communication").attr('data-name',$name);

      // if($type === 'SMS'){
      //    $("#send_sms").show();
      //    $("#send_email").hide();
      // }else if($type === 'Email'){
      //    $("#send_email").show();
      //    $("#send_sms").hide();
      // }else if($type =='Both' && $type !== undefined){
      //    $(".send_communication").show();
      // }else{
      //    $(".send_communication").hide();
      //    $(".send_communication").removeAttr('data-id');
      //    $(".send_communication").removeAttr('data-name');
      // }
   }
   function sendEmailSMS($this,type){
      var  $id = $this.attr('data-id');
      $.ajax({
         url:"<?=$HOST?>/ajax_send_email_sms.php?location=group",
         data : {
            is_direct : 1,
            sent_via : type,
            trigger_id : $id,
            customer_id :'<?=$_GET['id']?>',
         },
         dataType : 'json',
         type:'post',
         beforeSend : function(e){
            $("#ajax_loader").show();
         },
         success :function(res){
            $("#ajax_loader").hide();
            console.log(res);

            if(res.status =='success'){
               setNotifySuccess(res.msg);
            }else if(res.status == 'fail'){
               setNotifyError(res.msg);
            }
         }
      });
   }
// Communication code end
$(document).on('focus','#address,#primary_zip',function(){
   $("#is_address_ajaxed").val(1);
});

function initAutocomplete() {

   var input = document.getElementById('address');
   var options = {
      types: ['geocode'],
      componentRestrictions: {country: 'us'}
   };

   autocomplete = new google.maps.places.Autocomplete(input, options);

   autocomplete.setFields(['address_component']);

   autocomplete.addListener('place_changed', fillInAddress);
}
//google map api for address start
   function fillInAddress() {
      $("#is_valid_address").val('N');
      var place = autocomplete.getPlace();
      var address = "";
      var zip = "";
      var city = "";
      var state = "";
      var defaultZip = $("#primary_policy_form #primary_zip").val();
      $(".error").html('');
      for (var i = 0; i < place.address_components.length; i++) {
         var addressType = place.address_components[i].types[0];
         if(addressType == "street_number"){
         var val = place.address_components[i]["short_name"];
            address = address + " "+ val;
         }else if(addressType=="route"){
         var val = place.address_components[i]["long_name"];
         address = address + " "+ val;
         }else if(addressType=="postal_code"){
         zip = place.address_components[i]["short_name"];
         }else if(addressType=="locality"){
         city = place.address_components[i]["short_name"];
         }else if(addressType == "administrative_area_level_1"){
         state = place.address_components[i]["long_name"];
         }
      }
      // if(zip != defaultZip){
      //    $("#primary_policy_form #address").val('');
      //    $("#primary_policy_form #error_address").html("Address Not Match with zipcode");
      // }else{
         $("#primary_policy_form #primary_zip").val(zip);
         $("#primary_policy_form #address").val(address);
         $("#primary_policy_form #address").addClass('has-value');
         $("#primary_policy_form #city").val(city);
         $("#primary_policy_form #state").val(state).change();
         $("#is_valid_address").val('Y');
      // }

   }
   //google map api for address end

$(window).on('resize load', function(){
   if ($(window).width() <= 1170) {
      $('.nav-tabs:not(.nav-noscroll)').scrollingTabs('destroy');
      autoResizeNav();
   }
});

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
<script src="https://maps.googleapis.com/maps/api/js?key=<?=$GOOGLE_MAP_KEY?>&libraries=places" async defer></script>