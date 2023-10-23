<div class="agents_detail_wrap">
  <div class="row">
    <div class="col-md-4">
      <div class="panel panel-default profile-info agent <?=$status_class?>">
        <div class="panel-header">
          <div class="media">
            <div class="media-body">
              <h4 class="mn"><?=$row['fname']." ".$row['lname']?>  - <small><?=$row['rep_id']?></small></h4>
            </div>
            <div class="media-right">
            <div class="<?=in_array($row['status'],array('Suspended','Terminated')) ? '' : '' ?>">
              <div class="dropdown">
                <div class="phone-control-wrap">
                  <div class="phone-addon w-30" id="show_reason" style="<?=in_array($row['status'],array('Suspended','Terminated')) ? 'cursor:pointer' : 'display:none' ?>">
                    <i  class="fa fa-info-circle fs24 m-r-10"></i>
                  </div>
                  <div class="phone-addon text-left">
                    <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" class="btn btn-white text-black" aria-expanded="false" data-status="<?=$row['status']?>"> <?=$row['status']=='Active' ? 'Contracted' : $row['status'] ?> &nbsp;<i class="fa fa-sort text-red"></i> </button>
                    <?php if(!in_array($row['status'],array('Invited', 'Pending Documentation', 'Pending Approval', 'Pending Contract')))  { ?>
                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel">
                      <li><a href="javascript:void(0);" class="member_status <?=$row['status'] == 'Active' ? 'hidden' : '' ?>" data-status="Active">Contracted</a></li>
                      <li><a href="javascript:void(0);" class="member_status <?=$row['status'] == 'Suspended' ? 'hidden' : '' ?>" data-status="Suspended">Suspended</a></li>
                      <li><a href="javascript:void(0);" class="member_status <?=$row['status'] == 'Terminated' ? 'hidden' : '' ?>" data-status="Terminated">Terminated</a></li>
                    </ul>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div>
        </div>
        <div class="panel-body" id="profile_table" >
          <div class="table-responsive" >
            <table width="100%">
              <tbody>
                <tr>
                  <td>Agency:</td>
                  <td><?=checkIsset($row['company_name'])!='' ? $row['company_name'] : '-'?></td>
                </tr>
                <tr>
                  <td>Parent Agent:</td>
                  <td>
                  <?php if($row['id'] == md5(1)){ ?>
                            Root
                  <?php }else { ?>
                  <a data-href="agent_tree_popup.php?agent_id=<?=md5($row['_id'])?>" href="javascript:void(0);"  class="red-link pn fw500 agent_tree_popup"><?=$row['s_rep_id']?></a> - <?=$row['s_fname'].' '.$row['s_lname']?> (<?=$row['sid'] == 1 ? 'Root': $agentCodedRes[$row['s_level_id']]['level_heading']?>)
                  <?php } ?>
                  </td>
                </tr>
                <tr>
                  <td>Level:</td>
                  <td>
                    <div class="theme-form pr">
                      <select class="form-control max-w175" id="agent_level" data-old_lvl_id="<?=$row['agent_coded_id']?>" data-old_value="<?= $agentCodedRes[$row['agent_coded_id']]['level']?>">
                        <?php if($row['id'] == md5(1)){ ?>
                            <option value="root">Root</option>
                        <?php }else{ ?>
                            <?php if(!empty($agentCodedRes)){ ?>
                                <?php foreach($function->get_agent_level_range($row['id']) as $level){ ?>
                                    <option value="<?=$level['level']?>" data-id="<?=$level['id']?>" <?= ($level['id']==$row['agent_coded_id']) ? 'selected' : '' ?> ><?=$level['level_heading']?></option>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>Email:</td>
                  <td><?=$row['email']?></td>
                </tr>
                <tr>
                  <td>Phone:</td>
                  <td><?=format_telephone($row['cell_phone'])?></td>
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
                    <td>Advances: </td>
                    <td>
                     <div class="custom-switch">
                       <label class="smart-switch">
                         <input type="checkbox" class="js-switch" id="advances" <?=$advance_on=='Y' ? 'checked' : ''?> />
                        <div class="smart-slider round"></div>
                       </label>
                       <div class="pull-right">
                       <a href="javascript:void(0);" id="advances_history" class="btn red-link">View History</a>
                     </div>
                      </div>
                    </td>
                </tr>
               
                <?php if($is_new_license_request == "Y") { ?>
                  <tr>
                      <td>License :</td>
                      <td>
                          <a href="javascript:void(0)" class="btn_licensed_approval btn btn-action">Approval Request</a>
                      </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
           
        </div>
         <div class="reason_info" style="display:none">
            <?=$row['term_reason']?>
          </div>
        <div class="p-15" style="cursor:pointer;display:none; text-align: right;"  id="close_reason" ><a href="javascript:void(0);" class="pn red-link fw500">Close</a></div>
        <div class="panel-footer clearfix <?=$status_class?>"> 
          <?php if (!in_array($row['status'], array('Invited')) && $row["stored_password"] != "") { ?>
              <a data-toggle="tooltip" data-trigger="hover" href="switch_login.php?id=<?php echo $row['id']; ?>" target="blank" title="Access Agent Site">Agent Portal&nbsp;&nbsp;<i class="fa fa-lock"></i></a>
          <?php }?>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="panel panel-default agent_intrection_wrap">
        <div class="ajex_loader" id="intrection_loader" style="display: none;">
            <div class="loader"></div>
        </div>
        <div class="panel-body">
            <div class="review_docbox" <?=!empty($status) && $status == 'Pending Approval' ? '' : 'style="display:none;"'?>>
              <div class="review_docbox_inner text-center">
                <p class="m-b-30">This Agent has documentation to be reviewed. Click the button below to review documentation.</p>
                <a href="review_documentation.php?id=<?=$row['id']?>" class="btn btn-action">REVIEW DOCUMENTATION</a>
              </div>
            </div>
            <div class="clearfix" <?=!empty($status) && $status != 'Pending Approval' ? '' : 'style="display:none;"'?>>
              <ul class="nav nav-tabs tabs customtab  pull-left nav-noscroll" id="interaction_notes" role="tablist">
                <li role="presentation" class="active interaction_class"><a href="#interactions_tab" aria-controls="interactions_tab" role="tab" data-toggle="tab">Interactions</a></li>
                  <li role="presentation" class="note_class"><a href="#note_tab" aria-controls="note_tab" role="tab" data-toggle="tab">Notes</a></li>
              </ul>
              <div class="text-right interaction_div">
                <a href="#" class="search_btn" id='srh_btn_interaction'><i class="fa fa-search fa-lg text-blue"></i></a>
                <a href="#" class="search_btn search_close_btn" id="srh_close_btn_interaction" style="display: none;"><i class="text-light-gray ">X</i></a>
                <a data-href="interaction_add.php?agent_id=<?=$row['id']?>" class="btn btn-action account_note_popup_new m-l-5"><strong>+ Interaction</strong></a>
                <div class="clearfix"></div>
                  <div class="note_search_wrap" id="search_interaction" style="display:none" >
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
                <a href="#" class="search_btn search_close_btn" id="srh_close_btn_note" style="display:none"><i class="text-light-gray ">X</i></a>
                <a data-href="account_note.php?id=<?=$_GET['id']?>&type=Agent" class="btn btn-action account_note_popup_new  m-l-5"><strong>+ Note</strong></a>
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
              <div class="tab-content" <?=!empty($status) && $status != 'Pending Approval' ? '' : 'style="display:none;"'?> >
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
                                <a href="javascript:void(0)" data-href="<?=$HOST?>/interactions_note.php?id=<?=md5($int['int_id'])?>&type=view" class="interactions_note"><i class="fa fa-eye fa-lg"></i></a> &nbsp;
                                <a href="javascript:void(0)" data-href="interaction_add.php?interaction_detail_id=<?=md5($int['int_id'])?>&type=edit&agent_id=<?=$_GET['id']?>" class="account_note_popup_new"><i class="fa fa-edit fa-lg"></i></a> &nbsp;
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
                                <a href="javascript:void(0);" class="" id="edit_note_id" data-original-title="Edit" onclick="edit_note_agent(<?=$note['note_id']?>,'view')" data-value="Agent"><i class="fa fa-eye fa-lg"></i></a> &nbsp;
                                <a href="javascript:void(0);" class="" id="edit_note_id" data-original-title="Edit" onclick="edit_note_agent(<?=$note['note_id']?>)" data-value="Agent"><i class="fa fa-edit fa-lg"></i></a> &nbsp;
                                <a href="javascript:void(0);" class="" id="delete_note_id" data-original-title="Delete" onclick="delete_note(<?=$note['note_id']?>,<?=$note['ac_id']?>)"><i class="fa fa-trash fa-lg"></i></a>&nbsp;
                              </div>
                          </div>
                      <?php } } else echo '<p class="text-center mn"> Add first note by clicking the ‘+ Note’ button above </p>'; ?>
                      </div>
                  </div>
              </div>
            
        </div>
      </div>
    </div>
  </div>
  <div class="panel panel-default panel-block">
    <div class="panel-body ">
      <!-- <div class="tabbing-tab mn">  -->
        <!-- Nav tabs -->
        <ul class="nav nav-tabs tabs customtab  fixed_tab_top" role="tablist">
          <li role="presentation" class="active"><a href="#agp_products"  data-toggle="tab" class="ajax_get_agent_data" onclick="scrollToDiv($('#agp_products'), 0,'tmpl/agent_products.inc.php','agp_products','');" aria-expanded="true">Products</a></li>
          <li role="presentation" ><a href="#agent_account_detail_div"  data-toggle="tab" class="ajax_get_agent_data" onclick="scrollToDiv($('#agent_account_detail_div'), 0,'agent_account_detail.php','agent_account_detail_div','');" aria-expanded="false">Account</a></li>
          <li role="presentation" ><a href="#agp_attributes" id="data_agp_attributes" data-toggle="tab" class="ajax_get_agent_data" onclick="scrollToDiv($('#agp_attributes'), 0,'agent_account_detail.php','agent_account_detail_div','agp_attributes');" aria-expanded="false">Attributes</a></li>
          <li role="presentation" ><a href="#agp_brand_links" id="data_agp_brand_links"  data-toggle="tab" class="ajax_get_agent_data" onclick="scrollToDiv($('#agp_brand_links'), 0,'agent_account_detail.php','agent_account_detail_div','agp_brand_links');" aria-expanded="false">Personal Brand & Links</a></li>
          <li role="presentation" ><a href="#agp_commissions"  data-toggle="tab" class="ajax_get_agent_data" onclick="scrollToDiv($('#agp_commissions'), 0,'agent_commissions.php','agp_commissions','');" aria-expanded="false">Commissions</a></li>
          <li role="presentation" ><a href="#agp_merchant_pro"  data-toggle="tab" class="ajax_get_agent_data" onclick="scrollToDiv($('#agp_merchant_pro'), 0,'tmpl/agent_merchant_processor.inc.php','agp_merchant_pro','');" aria-expanded="false">Merchant Processor</a></li>
          <li role="presentation" ><a href="#agp_activity"  data-toggle="tab" class="ajax_get_agent_data" onclick="scrollToDiv($('#agp_activity'), 0,'tmpl/activity_feed_agent.inc.php','agp_activity','');" aria-expanded="false">Activity History</a></li>
        </ul>
      <!-- </div> -->
      <!-- Tab panes -->
      <div class="m-t-20">
        <?php //$display_agent_products = true; ?>
        <div role="tabpanel" class="tab-pane active" id="agp_products">
          <?php //include_once __DIR__ . '/../agent_products.php'; ?>
          <?php include_once 'agent_products.inc.php'; ?>
        </div>
        <div id="agent_account_detail_div" role="tabpanel" class="tab-pane" >
        </div>
        <div role="tabpanel" class="tab-pane" id="agp_commissions">
          <?php //include_once __DIR__ . '/../agent_commissions.php' ?>
        </div>
        <div role="tabpanel" class="tab-pane " id="agp_merchant_pro">
          <?php //include_once 'agent_merchant_processor.inc.php' ?>
        </div>
        <div role="tabpanel" class="tab-pane" id="agp_activity">
        </div>
      </div>
    </div>
  </div>
</div>
<div style="display:none">
  <div class="" id="status_reason">
    <div class="col-sm-8 m-l-30 p-t-30 m-t-10 p-l-20">
      <label for="reason" id="reason_lbl"></label>
      <input type="text" class="form-control has-value reason_input" name="reason_input" id="reason_input" value="">
      <span class="error error_reason" style="display:none"></span>
      <br>
      <button type="button" class="btn btn-action" id="save_reason">Save</button>
      <a href="javascript:void(0);" class="btn red-link" onclick="$.colorbox.close(); return false;">Close</a>
    </div>
  </div>
</div>
<div style="display:none">
  <div id="advance_history_div">
  <div class="panel panel-default panel-block panel-shadowless mn">
    <div class="panel-heading br-b">
      <div class="panel-title ">
        <h4 class="mn">Advance Commission - <span class="fw300"><?=$row['fname']?> <?=$row['lname']?></span> </h4>
      </div>
    </div>
    <div class="panel-body">
      <div class="table-responsive br-n">
        <table class="<?=$table_class?>">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Action</th>
                  </tr>
              </thead>
              <tbody>
              <?php if(!empty($advance_history)) { ?>
                <?php foreach($advance_history as $rows) { ?>
                <tr>
                    <td><?=$tz->getDate($rows['created_at'],'m/d/Y @ h:i A T')?></td>
                    <td><?=$rows['is_on'] == 'Y' ? "On" : "Off" ?></td>
              <?php } } else { ?>
                <tr>
                  <td colspan="2">
                    No rows found!
                  </td>
                </tr>
            <?php } ?>
              </tbody>
          </table>
      </div>
      <div class="text-center m-t-20"> 
      <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Close</a> </div>
    </div>
  </div>
  </div>
</div>
<div style="display:none">
  <div id="graded_history_div">
  <div class="panel panel-default panel-block panel-shadowless mn">
    <div class="panel-heading br-b">
      <div class="panel-title ">
        <h4 class="mn">Graded Commission - <span class="fw300"><?=$row['fname']?> <?=$row['lname']?></span> </h4>
      </div>
    </div>
    <div class="panel-body">
      <div class="table-responsive br-n">
        <table class="<?=$table_class?>">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Action</th>
                  </tr>
              </thead>
              <tbody>
              <?php if(!empty($graded_history)) { ?>
                <?php foreach($graded_history as $rows) { ?>
                <tr>
                    <td><?=$tz->getDate($rows['created_at'],'m/d/Y @ h:i A T')?></td>
                    <td><?=$rows['is_on'] == 'Y' ? "On" : "Off" ?></td>
              <?php } } else { ?>
                <tr>
                  <td colspan="2">
                    No rows found!
                  </td>
                </tr>
            <?php } ?>
              </tbody>
          </table>
      </div>
      <div class="text-center m-t-20"> 
      <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Close</a> </div>
    </div>
  </div>
  </div>
</div>
<?php include_once 'agent_detail_v1_jquery.inc.php';?>
<script type="text/javascript">
 $(document).ready(function(){
   if ($(window).width() >= 1171) {
      $(window).scroll(function() {
      if ($(this).scrollTop() > 531) {
         $('.fixed_tab_top').addClass('fixed');
      } else {
         $('.fixed_tab_top').removeClass('fixed');
      }
   });
   }
}); 

  $(function() {
     $('.agent_intrection_wrap').matchHeight({
         target: $('.profile-info')
     });
});

</script>