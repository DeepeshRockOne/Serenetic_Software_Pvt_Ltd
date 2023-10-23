<!-- SPECIAL DISPLAY TEXT HTML START  -->
<div class="panel panel-default">
  <div class="media special_display">
  <div class="media-left bg_light_primary">
    <img src="<?= $SPECIAL_DISPLAY_LOGO ?>" class="media-object">
  </div>
  <div class="media-body">
    <h4 class="media-heading text-primary"> <?= $SPECIAL_DISPLAY_TITLE ?> </h4>
    <?php if($sp_res['is_special_text_display'] == 'Y'){ ?>
      <p class="text-primary mn"><?php echo $sp_res['special_text_display']; ?></p>
    <?php } ?>
  </div>
</div>
</div>
<!-- SPECIAL DISPLAY TEXT HTML END  -->
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
                     <tr>
                        <td>Enrolling Agent:</td>
                        <td><a href="javascript:void(0);" data-href = "member_tree_popup.php?agent_id=<?=md5($row['sponsor_id'])?>&member_id=<?=$row['id']?>&type=Member" class="fw500 text-action member_tree_popup"><?=$row['sponsor_name'] . ' (' . $row['sponsor_rep_id'] . ')' ?></a></td>
                     </tr>
                     <?php if($isGapPrd){?>
                     <tr>
                        <td>Compliant</td>
                        <td>
                           <div class="custom-switch">
                              <label class="smart-switch">
                                 <input type="checkbox" class="js-switch" id="gapPlus" <?=$gapResult['is_compliant']=='Y' ? 'checked' : ''?>/>
                                 <div class="smart-slider round"></div>
                              </label>
                           </div>
                        </td>
                     </tr>
                     <?php }?>
                     <?php
                      if ($active_aae_id > 0) { ?>
                       <tr>
                          <td>AAE:</td>
                          <td>
                             <a href="send_aae_link.php?quote_id=<?= md5($active_aae_id) ?>"
                                     class="red-link send_aae_link">Resend Link</a>
                          </td>
                          <!-- <td>
                              <a href="javascript:void(0);" onclick="scrollToDiv($('#aae_section'),0);"
                                 class="btn btn-info btn-outline pull-right">Edit</a>
                          </td> -->
                      </tr>
                    <?php } ?>
                  </table>
               </div>
            </div>
            <div class="panel-footer clearfix <?=$status_class?>"> 
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
                  <ul class="nav nav-tabs tabs customtab pull-left" role="tablist">
                     <li role="presentation" class="active interaction_class"><a href="#interactions_tab" aria-controls="interactions_tab" role="tab" data-toggle="tab">Interactions</a></li>
                     <li role="presentation" class="note_class"><a href="#note_tab" aria-controls="note_tab" role="tab" data-toggle="tab">Notes</a></li>
                     <li role="presentation" class="claim_class"><a href="#claim_tab" aria-controls="claim_tab" role="tab" data-toggle="tab">Claims</a></li>
                     <li role="presentation" class="communication_class"><a href="#communication_tab" aria-controls="communication_tab" role="tab" data-toggle="tab">Communications</a></li>
                     <li role="presentation" class="eticket_class"><a href="#eticket_tab" aria-controls="eticket_tab" role="tab" data-toggle="tab" id="#eticket_tab_list">Etickets</a></li>
                  </ul>
                  <div class="text-right interaction_div">
                      <a href="#" class="search_btn " id="srh_btn_interaction"><i class="fa fa-search fa-lg text-blue"></i></a>
                      <a href="#" class="search_btn search_close_btn" id="srh_close_btn_interaction" style="display:none;" ><i class="text-light-gray ">X</i></a>
                     <a class="btn btn-action  m-l-8 members_interaction_add" data-href="members_interaction_add.php?memberId=<?=$row['id']?>"><strong>+ Interaction</strong></a>
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
                     <a href="#" class="search_btn" id='srh_btn_note'><i class="fa fa-search fa-lg text-blue"></i></a>
                     <a href="#" class="search_btn search_close_btn" id="srh_close_btn_note" style="display:none;"><i class="text-light-gray ">X</i></a>
                     <a data-href="account_note.php?id=<?=$_GET['id']?>&type=Customer" class="btn btn-action account_note_popup_new  m-l-8"><strong>+ Note</strong></a>
                     <div class="clearfix"></div>
                     <div class="note_search_wrap " id="search_note" style="display:none" >
                           <div class="phone-control-wrap">
                              <div class="phone-addon">
                                 <input type="text" class="form-control" id="note_search_keyword" placeholder="Search Keyword(s)">
                              </div>
                              <div class="phone-addon w-80">
                                 <button href="javascript:void(0);" class="btn btn-info btn-outline" id="search_btn_note">Search</button>
                              </div>
                           </div>
                        </div> 
                 </div>
                  <div class="text-right claim_div" style="display:none">
                      <a href="#" class="search_btn " id="srh_btn_claim"><i class="fa fa-search fa-lg text-blue"></i></a>
                      <a href="#" class="search_btn search_close_btn" id="srh_close_btn_claim" style="display:none;" ><i class="text-light-gray ">X</i></a>
                      <a class="btn btn-action  m-l-8 members_claim_add" data-href="members_interaction_add.php?memberId=<?=$row['id']?>&is_claim=Y"><strong>+ Claim</strong></a>
                      <div class="clearfix"></div>
                      <div class="note_search_wrap" id="claim_search_div" style="display:none;">
                           <div class="phone-control-wrap">
                              <div class="phone-addon">
                                 <input type="text" class="form-control" id="claim_search_keyword"  placeholder="Serach Notes, Keywords, Timestamp, etc." />
                              </div>
                              <div class="phone-addon w-80">
                                 <button href="javascript:void(0);" class="btn btn-action-o" id="search_btn_claim">Search</button>
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
                                <a href="javascript:void(0)" data-href="members_interaction_add.php?interaction_detail_id=<?=md5($int['int_id'])?>&type=edit&memberId=<?=$_GET['id']?>" class="account_note_popup_new"><i class="fa fa-edit fa-lg"></i></a> &nbsp;
                                <a href="javascript:void(0)" onclick="delete_interaction(<?=$int['int_id']?>)"><i class="fa fa-trash fa-lg"></i></a>&nbsp;
                              </div>
                          </div>
                        <?php } }else echo '<p class="text-center mn"> Add first interaction by clicking the ‘+ Interaction’ button above </p>'; ?>
                     </div>
                  </div>
                  <div role="tabpanel" class="tab-pane " id="note_tab">
                     <div class="activity_wrap">
                        <?php if(!empty($notes) && count($notes) > 0){ foreach($notes as $note) {?>
                           <div class="media">
                              <div class="media-body fs14 br-n">
                                    <p class="text-light-gray mn"><?=$tz->getDate($note['created_at'],'D., M. d, Y @ h:i A')?></p>
                                    <p class="mn"><?=note_custom_charecter('group','customer',$note['description'],400,$note['admin_name'],$note['display_id'],$note['admin_id'],$ADMIN_HOST.'/'.$note['url'].'?id='.md5($note['admin_id'])) ?></p>
                              </div>
                              <div class="media-right text-nowrap">
                                 <!-- <a href="javascript:void(0);"><i class="fa fa-edit fa-lg"></i></a> &nbsp;
                                 <a href="javascript:void(0);" ><i class="fa fa-trash fa-lg"></i></a> &nbsp;
                                 <a href="javascript:void(0);" ><i class="fa fa-eye fa-lg"></i></a> -->
                                 <a href="javascript:void(0);" class="" id="edit_note_id" data-original-title="View" onclick="edit_note_agent(<?=$note['note_id']?>,'view')" data-value="Customer"><i class="fa fa-eye fa-lg"></i></a> &nbsp;
                                 <?php 
                                    if($_SESSION['admin']['id'] == $note['admin_id']){
                                 ?>
                                 <a href="javascript:void(0);" class="" id="edit_note_id" data-original-title="Edit" onclick="edit_note_agent(<?=$note['note_id']?>)" data-value="Customer"><i class="fa fa-edit fa-lg"></i></a> &nbsp;
                                 <a href="javascript:void(0);" class="" id="delete_note_id" data-original-title="Delete" onclick="delete_note(<?=$note['note_id']?>,<?=$note['ac_id']?>)"><i class="fa fa-trash fa-lg"></i></a>&nbsp;
                                 <?php } ?>
                              </div>
                           </div>
                        <?php } } else echo '<p class="text-center mn"> Add first note by clicking the ‘+ Note’ button above </p>'; ?>
                        </div>
                  </div>
                  <div role="tabpanel" class="tab-pane pn" id="claim_tab">
                     <div class="activity_wrap">
                        <?php if(!empty($claims) && count($claims) > 0){ foreach($claims as $claim) {?>
                          <div class="media">
                              <div class="media-body fs14 br-n">
                                <p class="fw500 text-primary mn"><?=ucwords($claim['type'])?></p>
                                  <p class="text-light-gray mn"><?=$tz->getDate($claim['created_at'],'D., M. d, Y @ h:i A')?></p>
                                  <p class="mn"><?=custom_charecter($claim['description'],400,$claim['admin_name'],$claim['display_id'],$claim['admin_id'])?> </p>
                              </div>
                              <div class="media-right text-nowrap">
                                <a href="javascript:void(0)" data-href="<?=$HOST?>/interactions_note.php?id=<?=md5($claim['int_id'])?>&type=view&userType=member" class="claim_note"><i class="fa fa-eye fa-lg"></i></a> &nbsp;
                                <a href="javascript:void(0)" data-href="members_interaction_add.php?interaction_detail_id=<?=md5($claim['int_id'])?>&type=edit&memberId=<?=$_GET['id']?>&is_claim=Y" class="account_note_popup_new"><i class="fa fa-edit fa-lg"></i></a> &nbsp;
                                <a href="javascript:void(0)" onclick="delete_claim(<?=$claim['int_id']?>)"><i class="fa fa-trash fa-lg"></i></a>&nbsp;
                              </div>
                          </div>
                        <?php } }else echo '<p class="text-center mn"> Add first claim by clicking the ‘+ Claim’ button above </p>'; ?>
                     </div>
                  </div>
                  <div role="tabpanel" class="tab-pane communication_tab" id="communication_tab">
                     <div class="theme-form row">
                        <div class="col-md-6 col-lg-7">
                          <div class="form-group">
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
                        </div>
                        <div class="col-md-6 col-lg-5">
                          <div class="form-group">
                             <a href="javascript:void(0);" class="btn btn-info member_send_custom_email">+ Custom</a>
                             <a href="javascript:void(0);" class="btn btn-action send_communication" id="send_email" onclick="sendEmailSMS($(this),'Email')">Quick Email</a>
                             <a href="javascript:void(0);" class="btn btn-action-o send_communication" id="send_sms" onclick="sendEmailSMS($(this),'SMS')">Quick Text</a>
                          </div>
                        </div>
                        <div id="trigger_list">
                          
                        </div>
                     </div>
                  </div>
                  <div role="tabpanel" class="tab-pane " id="eticket_tab">
                     <div class="theme-form">
                        <div id="eticket_list">
                          
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
         <ul class="nav nav-tabs tabs  customtab fixed_tab_top" role="tablist" id="interaction_tab_wrap">
            <li class="active" role="presentation" ><a href="#products_tab" data-toggle="tab" onclick="scrollToDiv($('#products_tab'),0,'','products_tab');" aria-expanded="true">Products</a>
            </li>
            <li role="presentation" ><a href="#policy_tab" data-toggle="tab" onclick="scrollToDiv($('#policy_tab'), 0,'member_policy_tab.php','policy_tab');" aria-expanded="true">Primary Plan Holder</a>
            </li>
            <li role="presentation" ><a href="#dependents_tab" data-toggle="tab" onclick="scrollToDiv($('#dependents_tab'), 0,'member_depedents_tab.php','dependents_tab');" aria-expanded="true">Dependents</a>
            </li>

            <?php if($sponsor_billing_method == "individual") { ?>
               <li role="presentation" ><a href="#billing_tab" data-toggle="tab" onclick="scrollToDiv($('#billing_tab'), 0,'member_billing_tab.php','billing_tab');" aria-expanded="true">Billing Profile</a>
               </li>
               <li role="presentation" ><a href="#orders_tab" data-toggle="tab" onclick="scrollToDiv($('#orders_tab'), 0,'member_orders_tab.php','orders_tab');" aria-expanded="true">Orders</a>
               </li>
            <?php } ?>
            
            <li role="presentation" ><a href="#health_tab" data-toggle="tab" onclick="scrollToDiv($('#health_tab'), 0,'member_health_tab.php','health_tab');" aria-expanded="true">Health Details</a>
            </li>
            <li role="presentation" ><a href="#payable_tab" data-toggle="tab" onclick="scrollToDiv($('#payable_tab'), 0,'member_payable_tab.php','payable_tab');" aria-expanded="true">Accounts Payable</a>
            </li>
            <?php if($isGapPrd){?>
            <li role="presentation" ><a href="#hrm_tab" data-toggle="tab" onclick="scrollToDiv($('#hrm_tab'), 0,'member_hrm_tab.php','hrm_tab');" aria-expanded="true">GAP+ Info</a>
            </li>
            <?php } ?>
            <li role="presentation" ><a href="#activity_history_tab" data-toggle="tab" onclick="scrollToDiv($('#activity_history_tab'), 0,'tmpl/activity_feed_member.inc.php','activity_history_tab');" aria-expanded="true">Activity History</a>
            </li>
         </ul>
         <div class="m-t-20">
            <div role="tabpanel" class="tab-pane active" id="products_tab">
               <?php include ('tmpl/member_products_tab.inc.php'); ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="policy_tab">
               <?php //include ('tmpl/member_policy_tab.inc.php'); ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="dependents_tab"></div>
            <div role="tabpanel" class="tab-pane" id="billing_tab">
              <?php //include ('tmpl/member_billing_tab.inc.php'); ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="orders_tab">
               <?php //include ('tmpl/member_orders_tab.inc.php'); ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="health_tab">
               <?php //include ('tmpl/member_health_tab.inc.php'); ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="payable_tab">
               <?php //include ('tmpl/member_payable_tab.inc.php'); ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="hrm_tab">
               <?php //include ('tmpl/member_hrm_tab.inc.php'); ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="activity_history_tab">
               <?php //include ('tmpl/member_activity_tab.inc.php'); ?>
            </div>
         </div>
      </div>
   </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
   if ($(window).width() >= 1171) {
      $(window).scroll(function() {
      if ($(this).scrollTop() > 660) {
         $('.fixed_tab_top').addClass('fixed');
      } else {
         $('.fixed_tab_top').removeClass('fixed');
      }
   });
   }
      changeCommunication();
      loadEtickets();
      $(document).off('click',".member_tree_popup");
      $(document).on('click',".member_tree_popup",function(e){
        $href = $(this).attr('data-href');
        $.colorbox({
            iframe:true,
            href:$href,
            width: '900px',
            height: '650px',
            // onClosed :function(e){
            //   ajax_submit();
            // }
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
      $('#srh_btn_interaction').click(function(e){
         e.preventDefault(); //to prevent standard click event
         $(this).hide();
         $("#srh_close_btn_interaction").show();
         $("#search_div").slideDown();    
         $('.activity_wrap').addClass('interaction_filter_active');
         $('.activity_wrap').mCustomScrollbar("update");
      });
      $('#srh_btn_claim').click(function(e){
         e.preventDefault(); //to prevent standard click event
         $(this).hide();
         $("#srh_close_btn_claim").show();
         $("#claim_search_div").slideDown();    
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
            var id = '<?=$_GET['id']?>';
            interactionUpdate(id,'interaction','members_details.php');
      });
      $('#srh_close_btn_claim').click(function(e){
            e.preventDefault(); //to prevent standard click event
            $(this).hide();
            $("#claim_search_div").slideUp();
            $("#srh_close_btn_claim").hide();
            $("#srh_btn_claim").show();
            $('.activity_wrap').removeClass('interaction_filter_active');
            $('.activity_wrap').mCustomScrollbar("update");
            $("#claim_search_keyword").val('');
            var id = '<?=$_GET['id']?>';
            interactionUpdate(id,'claim','members_details.php');
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
            var id = '<?=$_GET['id']?>';
            interactionUpdate(id,'notes','members_details.php');
         });

      $(".activity_wrap").mCustomScrollbar({
            theme:"dark"
      });
      var not_win = '';
      $(".members_interaction_add").on('click',function(){
         $href = $(this).attr('data-href');
         var not_win = window.open($href, "myWindow", "width=500,height=600");
         if(not_win.closed) {  
            alert('closed');  
         } 
      });

      var not_win = '';
      $(".members_claim_add").on('click',function(){
         $href = $(this).attr('data-href');
         var not_win = window.open($href, "myWindow", "width=500,height=600");
         if(not_win.closed) {  
            alert('closed');  
         } 
      });
      $(".send_communication").hide();
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
  $(document).off('click','.member_send_custom_email');
  $(document).on('click','.member_send_custom_email',function(e){
    e.preventDefault();
    var $name = $(this).attr('data-name');
    var $id = $(this).attr('data-id');
    var member_id = '<?=$_GET['id']?>';
    $name = $name != '' && $name != undefined ? $name : '';
    $id = $id != '' && $id != undefined ? $id : '';
    var $href = 'members_send_custom_email.php?member_id='+member_id+'&id='+$id+'&name='+$name;
    var not_win = window.open($href, "myWindow", "width=1024,height=767");
    if(not_win.closed) {  
       alert('closed');  
    } 
  });
// Communication and Interaction/Claims code start
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

   $(document).off('click','.claim_note');
   $(document).on('click','.claim_note',function(e){
      var $href = $(this).attr('data-href');
      $.colorbox({
         href:$href,
         iframe: true,
         width: '800px',
         height: '500px'
         });
   });

   var not_win = '';
   $(document).off('click',".account_note_popup_new");
   $(document).on('click',".account_note_popup_new",function(){
      $href = $(this).attr('data-href');
      var not_win = window.open($href, "myWindow", "width=500,height=600");
      if(not_win.closed) {  
         alert('closed');  
      } 
   });

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

  $(document).off('click', '.send_aae_link');
  $(document).on('click', '.send_aae_link', function (e) {
      e.preventDefault();
      $.colorbox({
          href: $(this).attr('href'),
          iframe: true,
          width: '768px',
          height: '600px'
      })
  });

   $(document).on('click','#show_password',function(){
      $("#password_popup").hide();
   });

   

   $(document).off("click",".interaction_class");
   $(document).on("click",".interaction_class",function(e){
      $(".note_div").hide();
      $(".interaction_div").show();
      $(".claim_div").hide();
   });

   $(document).off("click",".claim_class");
   $(document).on("click",".claim_class",function(e){
      $(".note_div").hide();
      $(".interaction_div").hide();
      $(".claim_div").show();
   });

   $(document).off("click",".communication_class");
   $(document).on("click",".communication_class",function(e){
      $(".note_div").hide();
      $(".interaction_div").hide();
      $(".claim_div").hide();
   });


   $(document).off("click",".note_class");
   $(document).on("click",".note_class",function(e){
      $(".interaction_div").hide();
      $(".note_div").show();
      $(".claim_div").hide();
   });

   $(document).off("click",".eticket_class");
   $(document).on("click",".eticket_class",function(e){
      $(".interaction_div").hide();
      $(".note_div").hide();
      $(".claim_div").hide();
   });

   $(document).off('click','#search_btn_interaction');
   $(document).on('click','#search_btn_interaction',function(){
      $("#ajax_loader").show();
      var interaction_search_keyword = $("#interaction_search_keyword").val();
      var id = '<?=$_GET['id']?>';
      if(interaction_search_keyword!==''){
      $.ajax({
      url:'members_details.php?id='+id,
      data:{interaction_search_keyword:interaction_search_keyword,id:id},
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
      }else{
      alert("Please Enter Search Keyword(s)");
      $("#ajax_loader").hide();
      }
   });

   $(document).off('click','#search_btn_claim');
   $(document).on('click','#search_btn_claim',function(){
      $("#ajax_loader").show();
      var claim_search_keyword = $("#claim_search_keyword").val();
      var id = '<?=$_GET['id']?>';
      if(claim_search_keyword!==''){
      $.ajax({
      url:'members_details.php?id='+id,
      data:{claim_search_keyword:claim_search_keyword,id:id},
      method:'post',
      dataType: 'html',
      success:function(res){
         $("#ajax_loader").hide();
         $("#claim_tab").html(res);
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

   $(document).off('click',".open_conversation_preview");
    $(document).on('click',".open_conversation_preview",function(e){
        var not_win = '';
        $href = $(this).attr('data-href');
        var not_win = window.open($href, "myWindow", "width=1155,height=525");
        if(not_win.closed) {  
          alert('closed');  
        } 
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

   function delete_interaction(interaction_id) {
      var id = '<?=$_GET['id']?>';
      var url = "";
      url = "members_details.php";
      swal({
      text: "Delete Record: Are you sure?",
      showCancelButton: true,
      confirmButtonText: "Confirm",
      }).then(function () {
      $.ajax({
         url: 'ajax_members_interaction_add.php',
         data: {
            type : "delete",
            interaction_detail_id: interaction_id,
            memberId : '<?=$_GET['id']?>'
         },
         dataType: 'json',
         type: 'post',
         success: function (res) {
            if (res.status == "success") {
            // window.location = url + '?id=' + id;
            interactionUpdate(id,'interaction','members_details.php');
            setNotifySuccess('Interaction deleted successfully.');
            }
         }
      });
      }, function (dismiss) {

      });
   }

   function delete_claim(claim_id) {
      var id = '<?=$_GET['id']?>';
      var url = "";
      url = "members_details.php";
      swal({
      text: "Delete Record: Are you sure?",
      showCancelButton: true,
      confirmButtonText: "Confirm",
      }).then(function () {
      $.ajax({
         url: 'ajax_members_interaction_add.php',
         data: {
            type : "delete",
            interaction_detail_id: claim_id,
            is_claim: 'Y',
            memberId : '<?=$_GET['id']?>'
         },
         dataType: 'json',
         type: 'post',
         success: function (res) {
            if (res.status == "success") {
            // window.location = url + '?id=' + id;
            interactionUpdate(id,'claim','members_details.php');
            setNotifySuccess('Claim deleted successfully.');
            }
         }
      });
      }, function (dismiss) {

      });
   }

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
      $.colorbox({
         iframe: true,
         width: '800px',
         height: '400px',
         href: "account_note.php?id=" + customer_id + "&note_id=" + note_id + "&type=" + user_type +"&show="+show
      });
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
            interactionUpdate(id,'notes','members_details.php');
            setNotifySuccess('Note deleted successfully.');
            }
         }
      });
      }, function (dismiss) {

      });
   }
   function changeCommunication(element=''){
      var $value = "";
      if(element){
        var $value = element.val();
      }
      if($value != '' && $value !== undefined){
        $(".member_send_custom_email").text('View Content');
      } else {
        $(".member_send_custom_email").text('+ Custom');
      }
      $("#ajax_loader").show();
      // var note_search_keyword = $("#note_search_keyword").val();
      var id = '<?=$_GET['id']?>';
      // if(note_search_keyword!==''){
        $.ajax({
          url:'members_details.php?id='+id,
          data:{trigger_id:$value,id:id,communication_ajax:'Y'},
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
      // }else{
      //   alert("Please Enter Search Keyword(s)");
      //   $("#ajax_loader").hide();
      // }

      $(".send_communication").hide();
      if(element){
        var $value = element.val();
        var $type = element.find(':selected').attr('data-type');
        var $name = element.find(':selected').text();
        $(".members_send_trigger").show();
        $(".send_communication").attr('data-id',$value);
        $(".send_communication").attr('data-name',$name);
        $(".member_send_custom_email").attr('data-id',$value);
        $(".member_send_custom_email").attr('data-name',$name);
        
        if($type === 'SMS'){
           $("#send_sms").show();
           $("#send_email").hide();
        }else if($type === 'Email'){
           $("#send_email").show();
           $("#send_sms").hide();
        }else if($type =='Both' && $type !== undefined){
           $(".send_communication").show();
        }else{
           $(".send_communication").hide();
           $(".send_communication").removeAttr('data-id');
           $(".send_communication").removeAttr('data-name');
        }
      }
   }

   function loadEtickets(element=''){
      var $value = "";
      if(element){
        var $value = element.val();
      }
      $("#ajax_loader").show();
      var id = '<?=$_GET['id']?>';
        $.ajax({
          url:'members_details.php?id='+id,
          data:{id:id,eticket_ajax:'Y'},
          method:'post',
          dataType: 'html',
          success:function(res){
             $("#ajax_loader").hide();
             $("#eticket_list").html(res);
             $('#eticket_list_table').bootstrapTable().removeClass("table-hover");
             $('#eticket_list_table').bootstrapTable(
                'resetView',{height:365}
              );
             $(".activity_wrap").mCustomScrollbar({
             theme:"dark"
             });
             $('[data-toggle="tooltip"]').tooltip();
             common_select();
          }
        });
   }

   function sendEmailSMS($this,type){
      var  $id = $this.attr('data-id');
      $.ajax({
         url:"<?=$HOST?>/ajax_send_email_sms.php",
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
// Communication and Interaction/Claims code end

// Password show hide and scroll code start
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
$(document).on('focus','#address, #primary_zip',function(){
   $("#is_address_ajaxed").val(1);
});

$(document).off('change','#gapPlus');
$(document).on('change','#gapPlus',function(e){
   if($(this).is(":checked") === true){
      change_gap_plus_settings('Y');
   }else{
      change_gap_plus_settings('N');
   }
});
function change_gap_plus_settings($is_on){
   var id = '<?=$memberId?>';
   swal({
      text: "Change Compliant Status: Are you sure?",
      showCancelButton: true,
      confirmButtonText: "Confirm",
      cancelButtonText: "Cancel",
    }).then(function () {
      $("#ajax_loader").show();
      $.ajax({
        url: 'ajax_member_gap_setting.php',
        data: {
          member_id: id,
          is_on:$is_on,
        },
        dataType: 'json',
        type: 'post',
        success: function (res) {
          if (res.status == "success") {
            window.location = "members_details.php" + '?id=<?=$memberId?>';
          }
        }
      });
    }, function (dismiss) {
      if($is_on == 'Y'){
        $("#gapPlus").prop("checked",false);
      }else{
        $("#gapPlus").prop("checked",true);
      }
    });
}
// $(document).on('blur','#address',function(){
//    if($("#is_valid_address").val() == 'N'){
//       $("#primary_policy_form #address").val("");
//       $("#primary_policy_form #address").attr('placeholder', '');
//    }
// });
// Password show hide and scroll code end
<?php /* 
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
      // alert(address);
         $("#primary_policy_form #primary_zip").val(zip);
         $("#primary_policy_form #address").val(address);
         $("#primary_policy_form #address").addClass('has-value');
         $("#primary_policy_form #city").val(city);
         $("#primary_policy_form #state").val(state).change();
         $("#is_valid_address").val('Y');
        

}
//google map api for address end
*/ ?>
function isNumberKey(evt) {
   var charCode = (evt.which) ? evt.which : event.keyCode
   if (charCode > 31 && (charCode < 48 || charCode > 57)){
      return false;
   }
   return true;
}

$(function() {
     $('.member_intrection_wrap').matchHeight({
         target: $('.profile-info')
     });
});

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