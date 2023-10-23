<div class="group_profile">
   <div class="row">
      <div class="col-md-4">
         <div class="panel panel-default profile-info member <?=$status_class?>">
            <div class="panel-header">
               <div class="media">
                  <div class="media-body">
                     <h4 class="mn"><?= $row['business_name']  ?> - <small><?= $row['rep_id']  ?></small></h4>
                  </div>
                  <div class="media-right">
                    <div class="dropdown">
                      <div class="phone-control-wrap">
                        <div class="phone-addon w-30" id="show_reason" style="<?=in_array($row['status'],array('Suspended','Terminated')) ? 'cursor:pointer' : 'display:none' ?>" >
                          <i class="fa fa-info-circle fs24 m-r-10"></i>
                        </div>
                        <div class="phone-addon text-left">
                          <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" class="btn btn-white text-black" aria-expanded="false" data-status="<?=$row['status']?>"> <?=$row['status']=='Active' ? 'Contracted' : $row['status'] ?> &nbsp;<i class="fa fa-sort text-red"></i> </button>
                          <?php if(!in_array($row['status'],array('Invited', 'Pending Documentation', 'Pending Approval', 'Pending Contract')))  { ?>
                          <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel">
                            <li><a href="javascript:void(0);" class="group_status  <?=$row['status'] == 'Active' ? 'hidden' : '' ?>" data-status="Active">Contracted</a></li>
                            <li><a href="javascript:void(0);" class="group_status  <?=$row['status'] == 'Suspended' ? 'hidden' : '' ?>" data-status="Suspended">Suspended</a></li>
                            <li><a href="javascript:void(0);" class="group_status  <?=$row['status'] == 'Terminated' ? 'hidden' : '' ?>" data-status="Terminated">Terminated</a></li>
                          </ul>
                          <?php } ?>
                        </div>
                        </div>
                    </div>
               </div>
            </div>
          </div>
            <div class="panel-body" id="profile_table">
               <div class="table-responsive" >
                  <table width="100%">
                     <tr>
                        <td>Enrolling Agent:</td>
                        <!-- <td><a href="agent_detail_v1.php?id=<?= md5($row['sponsor_id']) ?>" class="red-link" target="_blank"><?= $row['s_rep_id'] ?></a> - <?= $row['s_fname'] .' '.$row['s_lname'] ?></td> -->
                        <td><a data-href="agent_tree_popup.php?agent_id=<?=md5($row['_id'])?>&is_group=Y" href="javascript:void(0);"  class="red-link pn fw500 agent_tree_popup"><?=$row['s_rep_id']?></a> - <?=$row['s_fname'].' '.$row['s_lname']?></td>
                     </tr>
                     <tr>
                        <td>Contact Name:</td>
                        <td><?= $row['fname'] .' '.$row['lname']?></td>
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
            <div class="reason_info" style="display:none">
               <?=$row['term_reason']?>
            </div>
            <div class="p-15" style="cursor:pointer;display:none; text-align: right;"  id="close_reason" ><a href="javascript:void(0);" class="pn red-link fw500">Close</a></div>

            <div class="panel-footer clearfix <?=$status_class?>"> 
                <?php if (!in_array($row['status'], array('Invited')) && $row["stored_password"] != "") { ?>
                    <a data-toggle="tooltip" href="switch_login.php?id=<?php echo $row['id']; ?>" target="blank" title="Access Group Site">Group Portal&nbsp;&nbsp;<i class="fa fa-lock"></i></a>
                <?php }?>
            </div>   
         </div>
      </div>
      <div class="col-md-8">
         <div class="panel panel-default group_intrection_wrap">
            <div class="ajex_loader" id="intrection_loader" style="display: none;">
               <div class="loader"></div>
            </div>
            <div class="panel-body" >
               <div class="clearfix" style="<?=!empty($status) && $status != 'Pending Approval' ? '' : 'display:none;'?>">
                  <ul class="nav nav-tabs tabs customtab  pull-left nav-noscroll" role="tablist" id="interaction_notes" >
                     <li role="presentation" class="active interaction_class"><a href="#interactions_tab" aria-controls="interactions_tab" role="tab" data-toggle="tab">Interactions</a></li>
                     <li role="presentation" class="note_class"><a href="#note_tab" aria-controls="note_tab" role="tab" data-toggle="tab">Notes</a></li>
                     <li role="presentation" class="communication_class"><a href="#communication_tab" aria-controls="communication_tab" role="tab" data-toggle="tab">Communications</a></li>
                  </ul>
                  <div class="text-right interaction_div">
                     <a href="#" class="search_btn" id='srh_btn_interaction'><i class="fa fa-search fa-lg text-blue"></i></a>
                     <a href="#" class="search_btn search_close_btn" style="display: none;" id="srh_close_btn_interaction"><i class="text-light-gray ">X</i></a>
                     <a data-href="group_interaction_add.php?group_id=<?=$row['id']?>" class="btn btn-action account_note_popup_new m-l-5"><strong>+ Interaction</strong></a>
                     <div class="clearfix"></div>
                     <div class="note_search_wrap " id="search_interaction" style="display:none" >
                        <div class="phone-control-wrap">
                            <div class="phone-addon">
                            <input type="text" class="form-control" id="interaction_search_keyword" placeholder="Search Keyword(s)">
                            </div>
                            <div class="phone-addon w-80">
                            <button href="javascript:void(0);" class="btn btn-info btn-outline" id="search_btn_interaction">Search</button>
                            </div>
                        </div>
                     </div>
                  </div>
                  <div class="text-right note_div" style="display:none">
                      <a href="#" class="search_btn" id='srh_btn_note'><i class="fa fa-search fa-lg text-blue"></i></a>
                      <a href="#" class="search_btn search_close_btn" style="display:none" id="srh_close_btn_note"><i class="text-light-gray ">X</i></a>
                      <a data-href="account_note.php?id=<?=$_GET['id']?>&type=Group" class="btn btn-action account_note_popup_new  m-l-5"><strong>+ Note</strong></a>
                      <div class="clearfix"></div>
                      <div class="note_search_wrap" id="search_note" style="display:none" >
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
                </div>
                  <div class="tab-content">
                     <div role="tabpanel" class="tab-pane active pn" id="interactions_tab">
                        <div class="activity_wrap activity_wrap_interaction">
                              <?php if(!empty($interaction) && count($interaction) > 0){ foreach($interaction as $int) {?>
                                <div class="media">
                                    <div class="media-body fs14 br-n">
                                      <p class="fw500 text-primary mn"><?=ucwords($int['type'])?></p>
                                        <p class="text-light-gray mn"><?=$tz->getDate($int['created_at'],'D., M. d, Y @ h:i A')?></p>
                                        <p class="mn"><?=custom_charecter($int['description'],400,$int['admin_name'],$int['display_id'],$int['admin_id'])?> </p>
                                    </div>
                                    <div class="media-right text-nowrap">
                                      <a href="javascript:void(0)" data-href="<?=$HOST?>/interactions_note.php?id=<?=md5($int['int_id'])?>&type=view&user_type=Group" class="interactions_note"><i class="fa fa-eye fa-lg"></i></a> &nbsp;
                                      <a href="javascript:void(0)" data-href="group_interaction_add.php?interaction_detail_id=<?=md5($int['int_id'])?>&type=edit&group_id=<?=$_GET['id']?>" class="account_note_popup_new"><i class="fa fa-edit fa-lg"></i></a> &nbsp;
                                      <a href="javascript:void(0)" onclick="delete_interaction(<?=$int['int_id']?>)"><i class="fa fa-trash fa-lg"></i></a>&nbsp;
                                    </div>
                                </div>
                              <?php } }else echo '<p class="text-center mn"> Add first interaction by clicking the ‘+ Interaction’ button above </p>'; ?>
                        </div>
                     </div>
                     <div role="tabpanel" class="tab-pane pn" id="note_tab">
                        <div class="activity_wrap activity_wrap_note">
                            <?php if(!empty($notes) && count($notes) > 0){ foreach($notes as $note) {?>
                                <div class="media">
                                    <div class="media-body fs14 br-n">
                                        <p class="text-light-gray mn"><?=$tz->getDate($note['created_at'],'D., M. d, Y @ h:i A')?></p>
                                        <p class="mn"><?=custom_charecter($note['description'],400,$note['admin_name'],$note['display_id'],$note['admin_id'])?></p>
                                    </div>
                                    <div class="media-right text-nowrap">
                                      <!-- <a href="javascript:void(0);"><i class="fa fa-edit fa-lg"></i></a> &nbsp;
                                      <a href="javascript:void(0);" ><i class="fa fa-trash fa-lg"></i></a> &nbsp;
                                      <a href="javascript:void(0);" ><i class="fa fa-eye fa-lg"></i></a> -->
                                      <a href="javascript:void(0);" class="" id="edit_note_id" data-original-title="Edit" onclick="edit_note_agent(<?=$note['note_id']?>,'view')" data-value="Group"><i class="fa fa-eye fa-lg"></i></a> &nbsp;
                                      <a href="javascript:void(0);" class="" id="edit_note_id" data-original-title="Edit" onclick="edit_note_agent(<?=$note['note_id']?>)" data-value="Group"><i class="fa fa-edit fa-lg"></i></a> &nbsp;
                                      <a href="javascript:void(0);" class="" id="delete_note_id" data-original-title="Delete" onclick="delete_note(<?=$note['note_id']?>,<?=$note['ac_id']?>)"><i class="fa fa-trash fa-lg"></i></a>&nbsp;
                                    </div>
                                </div>
                            <?php } } else echo '<p class="text-center mn"> Add first note by clicking the ‘+ Note’ button above </p>'; ?>
                        </div>
                     </div>
                     <div role="tabpanel" class="tab-pane " id="communication_tab">
                        <div class="theme-form">
                           <div class="form-group height_auto">
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
                              <a href="javascript:void(0);" class="btn btn-info group_send_trigger send_communication">View</a>
                              <a href="javascript:void(0);" class="btn btn-action send_communication" id="send_email" onclick="sendEmailSMS('Email')">Email</a>
                              <a href="javascript:void(0);" class="btn btn-action-o send_communication" id="send_sms" onclick="sendEmailSMS('SMS')">Text</a>
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
            <li role="presentation" class="active"><a href="#gp_products"  data-toggle="tab" class="ajax_get_group_data" onclick="scrollToDiv($('#gp_products'), 0,'tmpl/group_products.inc.php','gp_products','');" aria-expanded="true">Products</a></li>
            
            <li role="presentation" ><a href="#group_account_detail_div"  data-toggle="tab" class="ajax_get_group_data" onclick="scrollToDiv($('#group_account_detail_div'), 0,'group_account_detail.php','group_account_detail_div','');" aria-expanded="false">Account</a></li>
            
            <li role="presentation" ><a href="#gp_attributes" id="data_gp_attributes" data-toggle="tab" class="ajax_get_group_data" onclick="scrollToDiv($('#gp_attributes'), 0,'group_account_detail.php','group_account_detail_div','gp_attributes');" aria-expanded="false">Attributes</a></li>

            <li role="presentation" ><a href="#gp_brand_links"  data-toggle="tab" class="ajax_get_group_data" onclick="scrollToDiv($('#gp_brand_links'), 0,'group_personal_brand_link.php','gp_brand_links','');" aria-expanded="false">Personal Brand & Links</a></li>
            <?php if(!in_array($row['status'],array('Invited', 'Pending Documentation', 'Pending Approval', 'Pending Contract')))  { ?>
            <li role="presentation" ><a href="#gp_billing"  data-toggle="tab" class="ajax_get_group_data" onclick="scrollToDiv($('#gp_billing'), 0,'group_billing.php','gp_billing','');" aria-expanded="false">Billing</a></li>
            <?php } ?>
            <li role="presentation" ><a href="#gp_activity"  data-toggle="tab" class="ajax_get_group_data" onclick="scrollToDiv($('#gp_activity'), 0,'tmpl/activity_feed_group.inc.php','gp_activity','');" aria-expanded="false">Activity History</a></li>
         </ul>
         <div class="m-t-20">
            <div role="tabpanel" class="tab-pane active" id="gp_products">
                <?php include_once 'group_products.inc.php'; ?>
            </div>
            <div id="group_account_detail_div" role="tabpanel" class="tab-pane" ></div>
            <div role="tabpanel" class="tab-pane" id="gp_brand_links"></div>
            <div role="tabpanel" class="tab-pane" id="gp_billing"></div>
            <div role="tabpanel" class="tab-pane" id="gp_activity"></div>
         </div>
      </div>
   </div>
</div>
<script src="https://maps.googleapis.com/maps/api/js?key=<?=$GOOGLE_MAP_KEY?>&libraries=places" async defer></script>
<?php include_once 'groups_details_jquery.inc.php';?>
<script type="text/javascript">
  $(document).ready(function(){
      if ($(window).width() >= 1171) {
         $(window).scroll(function() {
         if ($(this).scrollTop() > 430) {
            $('.fixed_tab_top').addClass('fixed');
         } else {
            $('.fixed_tab_top').removeClass('fixed');
         }
         });
      }
   });

   $(function() {
        $('.group_intrection_wrap').matchHeight({
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
