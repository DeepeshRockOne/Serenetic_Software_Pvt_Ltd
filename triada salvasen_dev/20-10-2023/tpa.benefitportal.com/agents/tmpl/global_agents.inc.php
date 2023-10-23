<style type="text/css">
    .popover {
        max-width: 600px;
    }
</style>

<?php if($is_ajaxed_agent){ ?> 
    <input type="hidden" name="curr_ajax_url" id="curr_ajax_url" value="<?=$curr_ajax_url['link_url'];?>">
    <div class="table-responsive">
        <table class="<?=$table_class?> ">
            <thead>
            <tr class="data-head">
                <th><a href="javascript:void(0);" data-column="c.joined_date" data-direction="<?php echo $SortBy == 'c.joined_date' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">ID/Added Date ID</a></th>
                <th><a href="javascript:void(0);" data-column="c.fname" data-direction="<?php echo $SortBy == 'c.fname' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Details</a></th>
                <th><a href="javascript:void(0);" data-column="cs.company" data-direction="<?php echo $SortBy == 'cs.company' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Company</a></th>
                <th><a href="javascript:void(0);" data-column="c.type" data-direction="<?php echo $SortBy == 'c.type' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Account Type</a></th>
                <th><a href="javascript:void(0);" data-column="cs.agent_coded_level" data-direction="<?php echo $SortBy == 'cs.agent_coded_level' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Level</a></th>
                <th class="text-center">Tree</th>
                <th><a href="javascript:void(0);" data-column="c.status" data-direction="<?php echo $SortBy == 'c.status' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Status</a></th>
            </tr>
        </thead>
        <tbody>
            <?php
if ($total_rows > 0) {
    foreach ($fetch_rows as $rows) { ?>
                    <tr>
                        <td>
                            <a href="javascript:void(0);" class="text-red">
                            <strong class="fw600"><?php echo $rows['rep_id']; ?></strong></a></br>
                            <?php echo empty($rows['joined_date']) ? date('m/d/Y', strtotime($rows['invite_at']))  : date('m/d/Y', strtotime($rows['joined_date'])); ?>
                        </td>
                        <td>
                            <strong><?php echo stripslashes($rows['fname'] . ' ' . $rows['lname']); ?></strong> <br />
                            <?php echo preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $rows['cell_phone']) ?><br/>
                            <?php echo $rows['email']; ?><br />
                            <?php echo $rows['business_name']; ?>
                        </td>
                        <td><?=$rows['company']?></td>
                        <td><?php
                            if($rows['account_type'] == 'Business'){
                                echo "Agency";
                            }else if($rows['account_type'] == 'Personal'){
                                echo "Agent";
                            }else{
                                echo !empty($rows['account_type']) ? $rows['account_type'] : '-' ;
                            }
                        ?></td>
                        <td class="w-200">
                            <div class="theme-form pr">
                                <select class="form-control has-value agent_level_change" id="agent_level_change_<?=$rows['id']?>" data-old_lvl_id="<?=$rows['agent_coded_id']?>">
                                    <?php if($rows['id'] == md5(1)){ ?>
                                        <option value="root">Root</option>
                                    <?php }else{ ?>
                                        <?php if(!empty($agentCodedRes)){ ?>
                                            <?php foreach($function->get_agent_level_range($rows['id']) as $level){ ?>
                                                <option value="<?=$level['level']?>" data-id="<?=$level['id']?>" <?= ($level['id']==$rows['agent_coded_id']) ? 'selected' : '' ?> disabled="disabled" ><?=$level['level_heading']?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                                <label>Select</label>
                            </div>
                        </td>
                        <td class="text-center">
                            <a href="javascript:void(0);" data-href="agent_tree_popup.php?agent_id=<?=$rows['id']?>" class="agent_tree_popup"><img src="images/icons/icon-tree.svg" width="24px" /></a> 
                        </td>
                        <td class="w-200">
                            <?php if (in_array($rows['status'], array('Invited', 'Pending Approval', 'Pending Contract', 'Pending Documentation', 'Agent Abandon'))) { ?>
                                <?php if($rows['status'] != 'Invited'){ ?>
                                    <a href="javascript:void(0)" class="agent_status" id = "agent_status_<?=$rows['id']?>" data-status="<?=$rows["status"] == 'Active'?'Contracted':$rows["status"]?>" data-toggle="popover" data-user_id ="<?=$rows["id"]?>"  data-trigger="hover" data-content=""> <?=$rows["status"] == 'Active'?'Contracted': $rows["status"]?></a>
                                <?php } else if(($rows['status'] == 'Invited') && ($rows['invite_time_diff'] >168)){ ?>
                                    <label class="agent_status" data-status="<?=$rows["status"]?>" data-toggle="popover" data-user_id ="<?=$rows["id"]?>"  data-trigger="hover" data-content="">Invited</label><br/>
                                    <a href= "reinvite_agent.php?id=<?php echo $rows['id']; ?><?php if ($rows['invite_time_diff'] > 168) {echo '&status=expired';}?>"  class="resend_popup" data-toggle="tooltip" title="Re-invite"  id = "agent_status_<?=$rows['id']?>"  data-id='reinvite_<?=$rows['id']?>' data-content="" style="color: red;"> <?=$rows["invite_time_diff"] > 168 ? 'Expired':'Re-invited'?></a>
                                <?php } else { ?>
                                    <label class="agent_status" id = "agent_status_<?=$rows['id']?>" data-status="<?=$rows["status"]?>" data-toggle="popover" data-user_id ="<?=$rows["id"]?>"  data-trigger="hover" data-content="">Invited</label><br/>
                                    <a href= "reinvite_agent.php?id=<?php echo $rows['id']; ?><?php if ($rows['invite_time_diff'] > 168) {echo '&status=expired';}?>"  class="resend_popup" data-toggle="tooltip" title="Re-invite"  id = "agent_status_<?=$rows['id']?>"  data-id='reinvite_<?=$rows['id']?>' data-content=""> <?=$rows["status"] == 'Active'?'Contracted': 'Re-invite'?></a>
                                <?php } ?>
                                <div id="popover_content_<?=$rows['id']?>" style="display: none">
                                    <h4 class="font-normal"><i class="fa fa-info-circle text-red"></i> Agent Contracting Process</h4>
                                    <hr />
                                    <table>
                                        <tr class="status_div <?=$rows['status']=='Invited'?'text-success':'' ?>">
                                            <td width="180px" valign="top"><strong>Invited</strong></td>
                                            <td class="p-b-10">Agent has been extended an invite but has yet to accept and create account.</td>
                                        </tr>
                                        <tr class="status_div <?=$rows['status']=='Pending Documentation'?'text-success':'' ?>">
                                            <td valign="top"><strong class="text-nowrap">Pending Documentation</strong></td>
                                            <td class="p-b-10"> Agent has accepted the invite, but has yet to submit account documentation (W9, Agent License, E&O Insurance, etc.) for approval by admin </td>
                                        </tr>
                                        <tr class="status_div <?=(($rows['status']=='Pending Approval') || ($rows['status']=='Pending Review')) ?'text-success':'' ?>">
                                            <td valign="top"><strong>Pending Approval</strong></td>
                                            <td class="p-b-10">Agent has submitted documentation for review by admin, but admin has yet to review.</td>
                                        </tr>
                                        <tr class="status_div <?=$rows['status']=='Pending Contract'?'text-success':'' ?>">
                                            <td valign="top"><strong>Pending Contract</strong></td>
                                            <td class="p-b-10">Admin has approved account, but agent has yet to login to account and sign the contract.</td>
                                        </tr>
                                    </table>
                                </div>
                            <?php }else if(in_array($rows['status'], array('Contracted','Suspended','Terminated','Active'))){?>
                                <div class="theme-form pr">
                                    <select name="member_status" class="form-control member_status has-value" id="member_status_<?=$rows['id'];?>">
                                        <option value="Active" <?php if ($rows['status'] == 'Active') { echo "selected='selected'"; } ?> disabled="disabled">Contracted</option>
                                        <option value="Suspended" <?php if ($rows['status'] == 'Suspended') { echo "selected='selected'"; } ?> disabled="disabled">Suspended</option>
                                        <option value="Terminated" <?php if ($rows['status'] == 'Terminated') { echo "selected='selected'"; } ?> disabled="disabled">Terminated</option>
                                    </select>
                                    <label>Status</label>
                                </div>
                            <?php } else {
                                echo $rows['status'];
                            }?>

                            <?php if ($rows['status'] == 'Agent Abandon') { ?>
                                <a data-id='reinvite_<?=$rows['id']?>' id='agent_status_as_<?=$rows['id']?>' href="reinvite_agent.php?id=<?php echo $rows['id']; ?><?php if ($rows['invite_time_diff'] > 168) {echo '&status=expired';}?>" class="resend_popup" data-toggle="tooltip" title="Re-invite" ><i class="fa fa-reply fa-lg" style="padding-left: 7px; <?php if ($rows['invite_time_diff'] > 168) {}?>"></i></a>
                            <?php }else if(in_array($rows['status'], array('Invited')) && empty($rows["stored_password"])){ ?>
                                <a data-id='reinvite_<?=$rows['id']?>'  href="reinvite_agent.php?id=<?php echo $rows['id']; ?><?php if ($rows['invite_time_diff'] > 168) {echo '&status=expired';}?>" class="resend_popup" data-toggle="tooltip" title="Re-invite"><i id='agent_status_as_<?=$rows['id']?>' class="fa fa-reply fa-lg" style=" <?php if ($rows['invite_time_diff'] > 168) {echo 'color:red';}?>" ></i></a>
                            <?php }?>
                        </td>
                    </tr>
                <?php }?>
            <?php } else {?>
                <tr>
                    <td colspan="10">No record(s) found</td>
                </tr>
            <?php }?>
            </tbody>
            <?php if ($total_rows > 0) {?>
                <tfoot>
                <tr>
                    <td colspan="10">
                        <?php echo $paginate->links_html; ?>
                    </td>
                </tr>
                </tfoot>
            <?php }?>
        </table>
    </div>
    <!-- COPY TEXT POPUP CODE START -->
    <div id="copy_alert" style="display:none">
        <div id="copy_popup" class="sweet-overlay" tabindex="-1" style="opacity: 1.07; display: block;"></div>
        <div class="sweet-alert showSweetAlert visible cstm-size" data-custom-class="" data-has-cancel-button="true"
             data-has-confirm-button="true" data-allow-outside-click="false" data-has-done-function="true"
             data-animation="pop" data-timer="null" style="display: block; margin-top: -184px;">
            <div class="sa-icon sa-error" style="display: none;">
                <span class="sa-x-mark">
                    <span class="sa-line sa-left"></span>
                    <span class="sa-line sa-right"></span>
                </span>
            </div>
            <div class="sa-icon sa-warning pulseWarning" style="display: none;">
                <span class="sa-body pulseWarningIns"></span>
                <span class="sa-dot pulseWarningIns"></span>
            </div>
            <div class="sa-icon sa-info" style="display: none;"></div>
            <div class="sa-icon sa-success" style="display: none;">
                <span class="sa-line sa-tip"></span>
                <span class="sa-line sa-long"></span>

                <div class="sa-placeholder"></div>
                <div class="sa-fix"></div>
            </div>
            <div class="sa-icon sa-custom" style="display: none;"></div>
            <h3 style="font-weight:700px;">Your Contract url!</h3>
            <p style="display: block;">Click the "COPY LINK" button below and start sharing!</p>
            <fieldset>
                <input tabindex="3" placeholder="" type="text">
                <div class="sa-input-error"></div>
            </fieldset>
            <div class="sa-error-container">
                <div class="icon">!</div>
                <p>Not valid!</p>
            </div>
            <div class="sa-button-container">
                <input type="text" class="" id="copytext" readonly="readonly" data-clipboard-text="1111" tabindex=""
                       style="display: inline-block; box-shadow: none;background-color:#fff;border:1px solid #999;color:#2b2b2b;"
                       placeholder="display link here" value=""/>
                <textarea id="holdtext" style="display:none;"></textarea>

            </div>
            <div class="sa-button-container">
                <button class="confirm" id="copyingg" data-clipboard-target="#copytext" tabindex="1"
                        style="display: inline-block; background-color: rgb(68, 214, 44); box-shadow: 0px 0px 2px rgba(68, 214, 44, 0.8), 0px 0px 0px 1px rgba(0, 0, 0, 0.05) inset;">
                    Copy Link!
                </button>

                <div class="sa-confirm-button-container">
                    <button class="cancel" tabindex="2" style="display: inline-block; box-shadow: none;">No, Thanks &
                        close
                    </button>
                    <div class="la-ball-fall">
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END OF COPY POPUP CODE -->

    <?php } else { ?>
        <form id="frm_search_agent" action="global_agents.php" method="GET" class="sform" >    
          <input type="hidden" name="search_type" id="search_type" value="" />
          <input type="hidden" name="is_ajaxed_agent" id="is_ajaxed_agent" value="1" />
          <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
          <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
          <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
      </form>
     <div class="panel-body">
        <div id="ajax_loader" class="ajex_loader" style="display: none;">
           <div class="loader"></div>
        </div>
        <div id="ajax_data" style="display: none;"> </div>
     </div>
   
    <script type="text/javascript">
        $(document).ready(function () {
            memberStatusMsg = 'N';
            $("#fromdate").datepicker({
                changeDay: true,
                changeMonth: true,
                changeYear: true
            });

        
            $("#todate").datepicker({
                changeDay: true,
                changeMonth: true,
                changeYear: true
            });
            
            $(document).off('click', '.resend_popup');
            $(document).on('click', '.resend_popup', function (e) {
                e.preventDefault();
                $.colorbox({
                  href: $(this).attr('href'),
                  iframe: true, 
                  width: '768px', 
                  height: '240px'
                })
            });

            $(".rep_popup").colorbox({iframe: true, width: '900px', height: '580px'});
            ajax_submit_agent();

            $(document).off('click', '#ajax_data tr.data-head a');
            $(document).on('click', '#ajax_data tr.data-head a', function (e) {
                e.preventDefault();
                $('#sort_by_column').val($(this).attr('data-column'));
                $('#sort_by_direction').val($(this).attr('data-direction'));
                //$('#frm_search_agent').submit();
                ajax_submit_agent();
            });

            $(document).off('click', '#ajax_data ul.pagination li a');
            $(document).on('click', '#ajax_data ul.pagination li a', function (e) {
                e.preventDefault();
                $('#ajax_loader').show();
                $('#ajax_data').hide();
                $.ajax({
                    url: $(this).attr('href'),
                    type: 'GET',
                    success: function (res) {
                        $('#ajax_loader').hide();
                        $('#ajax_data').html(res).show();
                        common_select();
                    }
                });
            });

            $(document).on("click",".agent_feature_access_popup",function(e) {
                e.preventDefault();
                $.colorbox({
                    href: $(this).attr('href'),
                    iframe: true,
                    width: '900px',
                    height: '90%'
                });
            });
        });

        $(document).off('change', '.member_status');
    $(document).on("change", ".member_status", function(e) {
        e.stopPropagation();
        var id = $(this).attr('id').replace('member_status_', '');
        var member_status = $(this).val();
        swal({
            //title: "Are you sure ",
            text: "Change Status: Are you sure?",
            //type: "warning",
            showCancelButton: true,
            //confirmButtonColor: "#DD6B55",
            confirmButtonText: "Confirm",
            //showCloseButton: true
        }).then(function() {
            if (member_status == 'Terminated' || member_status == 'Suspended') {
                $.colorbox({
                    iframe: true,
                    href: "<?=$ADMIN_HOST?>/reason_change_agent_status.php?id=" + id + "&status=" + member_status,
                    width: '600px',
                    height: '400px',
                    trapFocus: false,
                    closeButton: false,
                    overlayClose: false,
                    escKey: false,
                    onClosed: function() {
                        $.ajax({
                            url: "reason_change_agent_status.php",
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                customer_id: id,
                                action: 'OldStatus'
                            },
                            success: function(data) {
                                if (data.status == 'success') {
                                    $status = data.member_status;
                                    $('.member_status [value=' + $status + ']').attr('selected', 'true');
                                }
                            }
                        });
                    }
                });
            } else {
                $.ajax({
                    url: 'change_agent_status.php',
                    data: {
                        id: id,
                        status: member_status
                    },
                    method: 'POST',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status == "success") {
                            setNotifySuccess(res.msg);
                        }else{
                            setNotifyError(res.msg);
                            ajax_submit();
                        }
                    }
                });
            }
        }, function(dismiss) {
            ajax_submit_agent();
        })
    });

    function get_status($id) {
        $('#agent_status_' + $id).html('Re-Invite');
        $("#agent_status_" + $id).css('color', '#2C4C80');
        $("#agent_status_as_" + $id).css('color', '#2C4C80');
    }
    
        function change_access_type($select_obj) {
            $('#ajax_loader').show();
            var access_type = $select_obj.val();
            var agent_id = $select_obj.data('agent_id');
            $.ajax({
                url: "change_agent_access_type.php?agent_id=" + agent_id + "&access_type=" + access_type,
                type: 'GET',
                data: null,
                success: function (res) {
                    $('#ajax_loader').hide();
                    if (res.status == "success") {
                        setNotifySuccess(res.message);
                        if (access_type == 'limited') {
                            $("#agent_feature_access_link_" + agent_id).removeClass('hidden');
                        } else {
                            $("#agent_feature_access_link_" + agent_id).addClass('hidden');
                        }
                    } else {
                        setNotifyError(res.message);
                    }
                }
            });
        }
        
        function ajax_submit() 
        {
            $('#ajax_loader').show();
            $('#ajax_data').hide();
            $('#is_ajaxed').val('1');
            var params = $('#frm_search_agent').serialize();
            $.ajax({
                url: $('#frm_search_agent').attr('action'),
                type: 'GET',
                data: params,
                success: function (res) {
                    $('#ajax_loader').hide();
                    $('#ajax_data').html(res).show();
                     
                    if(memberStatusMsg == 'Y'){
                        setNotifySuccess("Status changed Successfully");
                        memberStatusMsg = 'N';
                    }
                    $("[data-toggle=popover]").each(function(i, obj) {
                      $(this).popover({
                        html: true,
                        placement:'auto bottom',
                        content: function() {
                          var id = $(this).attr('data-user_id')                          
                          // $('#popover_content_'+id).find('.status_div.text-success').prevAll().addClass("text-success");  
                          return $('#popover_content_'+id).html();
                        }
                      });
                    });
                }
            });
            return false;
        }

        function ajax_submit_agent() {
            $('#ajax_loader').show();
            $('#ajax_data').hide();
            $('#is_ajaxed').val('1');
            var params = $('#frm_search_agent').serialize();
            var all_usersFrm = $('#all_usersFrm').serialize();
            params += '&'+all_usersFrm;
            $.ajax({
                url: $('#frm_search_agent').attr('action'),
                type: 'GET',
                data: params,
                success: function (res) {
                    $('#ajax_loader').hide();
                    $('#ajax_data').html(res).show();
                    $("[data-toggle=popover]").each(function(i, obj) {
                      $(this).popover({
                        html: true,
                        placement:'auto bottom',
                        content: function() {
                          var id = $(this).attr('data-user_id')                          
                          // $('#popover_content_'+id).find('.status_div.text-success').prevAll().addClass("text-success");  
                          return $('#popover_content_'+id).html();
                        }
                      });
                    });
                    common_select();
                }
            });
            return false;
        }

        function delete_agent(agent_id) {
            swal({
                text: 'Delete Agent: Are you sure?',
                showCancelButton: true,
                confirmButtonText: 'Confirm',
                cancelButtonText: 'Cancel',
            }).then(function () {
                $.ajax({
                    url: "ajax_delete_agent.php",
                    type: 'GET',
                    data: {id: agent_id},
                    dataType: 'JSON',
                    success: function (res) {
                        if (res.status == 'success') {
                            // setNotifySuccess(res.msg);
                            window.location.reload();
                            // redirect_after_delete();
                        } else {
                            window.location.reload();
                            // setNotifyError(res.msg);
                            // redirect_after_delete();
                        }
                    }
                });
            }, function (dismiss) {
                window.location.reload();
            })
        }
    </script>
 
    <script type="text/javascript">
        $(document).off('click',".agent_tree_popup");
$(document).on('click',".agent_tree_popup",function(e){
    $href = $(this).attr('data-href');
    $.colorbox({
        iframe:true,
        href:$href,
        width: '900px',
        height: '650px'
    });
});

$(document).off('click', '.agent_products');
  $(document).on('click', '.agent_products', function(e) {
    e.preventDefault();
    var id = $(this).data('id');
    $.colorbox({
      href: "agent_products_popup.php?id=" + id,
      iframe: true,
      width: '800px',
      height: '450px'
    });
  });

// Agent level change
$(document).off('change', '.agent_level_change');
$(document).on('change', '.agent_level_change', function(e) {
  e.stopPropagation();
  var id = $(this).attr('id').replace('agent_level_change_', '');
  var level = $(this).val();
  var level_id = $("#agent_level_change_"+id+" option:selected").attr('data-id');
  var old_level_id = $(this).attr('data-old_lvl_id');
  console.log(id+" "+level+" "+level_id+" "+old_level_id);
//   return false;
  if(level_id !== old_level_id){
    swal({
      //title: "Are you sure ",
      text: "Change Agent Level: Are you sure?",
      //type: "warning",
      showCancelButton: true,
      //confirmButtonColor: "#DD6B55",
      confirmButtonText: "Confirm",
      //showCloseButton: true
    }).then(function() {
      window.location = 'ajax_agent_level_change.php?agent_id=' + id + '&level_id=' + level_id+"&level="+level;
    });
  }
});
    </script>
<?php } ?>