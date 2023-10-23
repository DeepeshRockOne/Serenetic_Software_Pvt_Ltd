<?php if($is_ajaxed_leads){ ?>
<div class="table-responsive">
        <table class="<?= $table_class ?>">
            <thead>
            <tr class="data-head">
                <th>
                    <a href="javascript:void(0);" data-column="l.created_at"
                       data-direction="<?php echo $SortBy == 'l.created_at' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">ID/Added
                        Date</a>
                </th>
                <th>
                    <a href="javascript:void(0);" data-column="l.fname"
                       data-direction="<?php echo $SortBy == 'l.fname' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Details</a>
                </th>
                <th >
                    <a href="javascript:void(0);" data-column="l.lead_type"
                       data-direction="<?php echo $SortBy == 'l.lead_type' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Lead
                        Type</a></th>
                <th width="15%">
                    <a href="javascript:void(0);" data-column="sponsor_name"
                       data-direction="<?php echo $SortBy == 'sponsor_name' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Added
                        By/ID</a>
                </th>
                <th >
                    <a href="javascript:void(0);" data-column="l.opt_in_type"
                       data-direction="<?php echo $SortBy == 'l.opt_in_type' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Lead
                        Tag</a>
                </th>
                <th>
                    <a href="javascript:void(0);" data-column="l.status"
                       data-direction="<?php echo $SortBy == 'l.status' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Status</a>
                </th>
                <th width="90px" class="text-center">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($total_rows > 0) { ?>
                <?php foreach ($fetch_rows as $key => $rows) { ?>
                    <tr>
                        <td>
                            <a href="lead_details.php?id=<?= $rows['id'] ?>" target="_blank" class="text-red">
                                <strong class="fw600"><?php echo $rows['lead_id']; ?></strong></a><br/>
                            <?php echo empty($rows['created_at']) ? date('m/d/Y', strtotime($rows['invite_at'])) : date('m/d/Y', strtotime($rows['created_at'])); ?>
                        </td>
                        <td>
                            <strong><?php echo stripslashes($rows['fname'] . ' ' . $rows['lname']); ?></strong><br>
                            <?php
                            $format_telephone = format_telephone($rows['cell_phone']);
                            if (!empty($format_telephone)) {
                                echo $format_telephone . '<br/>';
                            }
                            echo $rows['email'];
                            ?>
                        </td>
                        <td><?php echo $rows['lead_type']; ?></td>
                        <td>
                            <a href="javascript:void(0);" class="text-red"><strong class="fw600"><?php echo $rows['sponsor_rep_id']; ?></strong></a>
                            <br/><?php echo $rows['sponsor_name']; ?>
                        </td>
                        <td>
                            <div class="theme-form pr w-200">
                                <select class="form-control lead_tag listing_search <?= !empty($rows['opt_in_type']) ? 'has-value' : '' ?>"
                                        id="lead_tag_<?= $rows['id']; ?>">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($lead_tag_res)) {
                                        foreach ($lead_tag_res as $key => $lead_tag_row) {
                                            ?>
                                            <option value="<?= $lead_tag_row['tag'] ?>" <?php echo $rows['opt_in_type'] == $lead_tag_row['tag'] ? "selected" : ""; ?>><?= $lead_tag_row['tag'] ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label>Select</label>
                            </div>
                        </td>
                        <td>
                            <div class="theme-form pr w-130">
                                <select class="form-control lead_status <?php echo in_array($rows['status'], array("New", "Working", "Open", "Unqualified", "Abandoned", "Converted")) ? "has-value" : ""; ?>"
                                        id="lead_status_<?= $rows['id']; ?>">
                                    <option></option>
                                    <option value="New" <?php echo $rows['status'] == "New" ? "selected" : ""; ?>>New
                                    </option>
                                    <option value="Working" <?php echo $rows['status'] == "Working" ? "selected" : ""; ?>>
                                        Working
                                    </option>
                                    <option value="Open" <?php echo $rows['status'] == "Open" ? "selected" : ""; ?>>
                                        Open
                                    </option>
                                    <option value="Unqualified" <?php echo $rows['status'] == "Unqualified" ? "selected" : ""; ?>>
                                        Unqualified
                                    </option>
                                    <option value="Abandoned" <?php echo $rows['status'] == "Abandoned" ? "selected" : ""; ?>>
                                        Abandoned
                                    </option>
                                    <option value="Converted" <?php echo $rows['status'] == "Converted" ? "selected" : ""; ?>>
                                        Converted
                                    </option>
                                </select>
                                <label>Select</label>
                            </div>
                        </td>
                        <td class="icons text-left">
                            <a href="lead_details.php?id=<?= $rows['id'] ?>" target="_blank" data-toggle="tooltip" title="Details">
                                <i class="fa fa-eye"></i>
                            </a>
                            <?php 
                                if($rows['lead_type'] == "Member" && in_array($rows['status'],array("New","Working","Open","Abandoned")))  {
                                    ?>
                                    <a href="member_enrollment.php?lead_id=<?=$rows['id'];?>" data-toggle="tooltip" title="Member Enrollment"><img src="<?=$HOST?>/images/icons/lead_aae.svg" height="20px"></a>
                                    <?php
                                }
                            ?>
                            <?php 
                                if($rows['lead_type'] == "Agent/Group" && in_array($rows['status'],array("New","Working","Open")) && empty($rows['customer_id']))  {
                                    ?>
                                    <a href="javascript:void(0)" data-id="<?=$rows['id'];?>" data-toggle="tooltip" title="Invite" class="btn_invite_agent_group"><i class="fa fa-user-plus"></i></a>
                                    <?php
                                }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="8" align="center">No record(s) found</td>
                </tr>
            <?php } ?>
            </tbody>
            <?php if ($total_rows > 0) { ?>
                <tfoot>
                <tr>
                    <td colspan="8">
                        <?php echo $paginate->links_html; ?>
                    </td>
                </tr>
                </tfoot>
            <?php } ?>
        </table>
    </div>
<?php } else { ?>
<form id="frm_search_leads" action="global_leads.php" method="GET" class="sform" >
<input type="hidden" name="search_type" id="search_type" value="" />
<input type="hidden" name="is_ajaxed_leads" id="is_ajaxed_leads" value="1" />
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

  $(document).off('change', '.lead_tag');
  $(document).on("change", ".lead_tag", function (e) {
      e.stopPropagation();
      var id = $(this).attr('id').replace('lead_tag_', '');
      var lead_tag = $(this).val();
      swal({
          text: "Change Tag: Are you sure?",
          showCancelButton: true,
          confirmButtonText: "Confirm"
      }).then(function () {
          $.ajax({
              url: 'change_lead_tag.php',
              data: {
                  id: id,
                  lead_tag: lead_tag
              },
              method: 'POST',
              dataType: 'json',
              success: function (res) {
                  if (res.status == "success") {
                      setNotifySuccess(res.msg);
                  } else {
                      setNotifyError(res.msg);
                      ajax_submit_leads();
                  }
              }
          });
      }, function (dismiss) {
          ajax_submit_leads();
      })
  });

  $(document).off('change', '.lead_status');
            $(document).on("change", ".lead_status", function (e) {
                e.stopPropagation();
                var id = $(this).attr('id').replace('lead_status_', '');
                var lead_status = $(this).val();
                swal({
                    text: "Change Status: Are you sure?",
                    showCancelButton: true,
                    confirmButtonText: "Confirm"
                }).then(function () {
                    $.ajax({
                        url: 'change_lead_status.php',
                        data: {
                            id: id,
                            status: lead_status
                        },
                        method: 'POST',
                        dataType: 'json',
                        success: function (res) {
                            if (res.status == "success") {
                                setNotifySuccess(res.msg);
                            } else {
                                setNotifyError(res.msg);
                                ajax_submit_leads();
                            }
                        }
                    });
                }, function (dismiss) {
                    ajax_submit_leads();
                })
            });

  $(document).off('click', '.btn_delete');
  $(document).on("click", ".btn_delete", function (e) {
      var id = $(this).attr('data-id');
      swal({
          text: "Delete Lead: Are you sure?",
          showCancelButton: true,
          confirmButtonText: "Confirm"
      }).then(function () {
          $.ajax({
              url: 'delete_lead.php',
              data: {
                  id: id,
              },
              method: 'POST',
              dataType: 'json',
              success: function (res) {
                  if (res.status == "success") {
                      setNotifySuccess(res.msg);
                  } else {
                      setNotifyError(res.msg);
                  }
                  ajax_submit_leads();
              }
          });
      }, function (dismiss) {

      });
  });

  $(document).off('click', '#chk_all');
  $(document).on('click', '#chk_all', function () {
      if ($(this).prop('checked') == true) {
          $(".check_record").prop("checked", true);
          $("#lead_operation").show();

      } else {
          $(".check_record").prop("checked", false);
          $("#lead_operation").hide();
          $("#assigned_to").hide('slow');
          $("#assigned_to_multiple_td").show('slow');

      }

  });
  $(document).off('click', '.check_record');
  $(document).on('click', '.check_record', function () {
      var id = $(this).data('id');
      var len = $('[name="check_record[]"]:checked').length;

      if (len > 0) {
          $("#lead_operation").show();
      } else {
          $("#lead_operation").hide();
          $("#assigned_to").hide('slow');
          $("#assigned_to_multiple_td").show('slow');

      }
  });

  $(document).off('click', '#btn_delete_multiple_lead');
  $(document).on('click', '#btn_delete_multiple_lead', function () {
      var lead_ids = [];
      $("input[name='check_record[]']:checked").each(function () {
          lead_ids.push($(this).val());
      });

      swal({
          text: "Delete Selected Leads: Are you sure?",
          showCancelButton: true,
          confirmButtonText: "Confirm"
      }).then(function () {
          $('#ajax_loader').show();
          $.ajax({
              url: "delete_lead.php",
              type: 'POST',
              data: {lead_ids: lead_ids},
              dataType: 'json',
              success: function (res) {
                  $('#ajax_loader').hide();
                  if (res.status == 'success') {
                      setNotifySuccess(res.msg);
                      ajax_submit_leads();
                  }
              }
          });
      }, function (dismiss) {
          ajax_submit_leads();
      });
  });

  $(document).off('click', '#assigned_to_multiple');
  $(document).on('click', "#assigned_to_multiple", function () {
      $("#assigned_to_multiple_td").hide('slow');
      $("#assign_user").val("");
      $("#assigned_to").show('slow');
  });

  $(document).off('click', '#assigned_btn');
  $(document).on('click', "#assigned_btn", function () {
      sponsor_id = $("#assign_user").val();
      if (sponsor_id != '') {
          var curr_ajax_url = $("#curr_ajax_url").val();
          var lead_ids = [];
          $("input[name='check_record[]']:checked").each(function () {
              lead_ids.push($(this).val());
          });
          
          swal({
              text: "Assign Selected Leads: Are you sure?",
              showCancelButton: true,
              confirmButtonText: "Confirm"
          }).then(function () {
              $('#ajax_loader').show();
              $.ajax({
                  url: "ajax_assigne_lead.php",
                  type: 'POST',
                  data: {lead_ids: lead_ids,sponsor_id:sponsor_id},
                  dataType: 'json',
                  success: function (res) {
                      $('#ajax_loader').hide();
                      if (res.status == 'success') {
                          setNotifySuccess(res.msg);
                          ajax_submit_leads();
                      }
                  }
              });
          }, function (dismiss) {
              ajax_submit_leads();
          })
      } else {
          swal("Oops!", "Select Agent To Assign Lead", "warning");
      }
  });
  

  $(document).on('click', '.agent_link', function() {
    var id = $(this).attr('id');
    var customer_id = $(this).attr('data-id');
    if (id == 1) {
      if (customer_id == undefined)
        window.location = 'agent_listing.php';
      else
        window.location = 'agent_detail.php?id=' + customer_id;
    } else {
      setNotifyError("Link is not Active");
    }
  });

  $(document).on('click', '.affiliates_link', function() {
    var id = $(this).attr('id');
    var customer_id = $(this).attr('data-id');
    if (id == 1) {
      if (customer_id == undefined)
        window.location = 'affiliate_profile.php';
      else
        window.location = 'affiliate_detail.php?id=' + customer_id;
    } else {
      setNotifyError("Link is not Active");
    }
  });

  $(document).on('click', '.member_link', function() {
    var id = $(this).attr('id');
    var customer_id = $(this).attr('data-id');
    if (id == 1) {
      if (customer_id == undefined)
        window.location = 'customer_listing.php';
      else
        window.location = 'customer_detail.php?id=' + customer_id;
    } else {
      setNotifyError("Link is not Active");
    }
  });

  $(document).on("click", ".email_unsubscribe", function() {
    $checkObj = $(this);
    if ($(this).prop("checked") == true) {
      $email_unsubscribe = 'Y';
      $text = 'you want to Unsubscribe this Email ?';
      $oldVal = "checked";
    } else {
      $email_unsubscribe = 'N';
      $text = 'you want to Subscribe this Email ?';
      $oldVal = "unchecked";
    }
    $id = $(this).attr('data-id');
    swal({
      title: "Are you sure",
      text: $text,
      type: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, unsubscribe it!",
      showCloseButton: true
    }).then(function() {
      $.ajax({
        url: 'leads.php',
        type: 'POST',
        dataType: 'json',
        data: {
          id: $id,
          type: 'is_email_unsubscribe',
          val: $email_unsubscribe
        },
        success: function(data) {
          //window.location.reload();
        }
      });
    }, function(dismiss) {
      if ($oldVal == "checked") {
        $checkObj.prop('checked', false);
      } else {
        $checkObj.prop('checked', true);
      }
    });
  });
  $(document).on("click", ".sms_unsubscribe", function() {
    $checkObj = $(this);
    if ($(this).prop("checked") == true) {
      $sms_unsubscribe = 'Y';
      $text = 'you want to Unsubscribe this SMS ?';
      $oldVal = "checked";
    } else {
      $sms_unsubscribe = 'N';
      $text = 'you want to Subscribe this SMS ?';
      $oldVal = "unchecked";
    }
    $id = $(this).attr('data-id');
    swal({
      title: "Are you sure ",
      text: $text,
      type: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, unsubscribe it!",
      showCloseButton: true
    }).then(function() {
      $.ajax({
        url: 'leads.php',
        type: 'POST',
        dataType: 'json',
        data: {
          id: $id,
          type: 'is_sms_unsubscribe',
          val: $sms_unsubscribe
        },
        success: function(data) {
          //window.location.reload();
        }
      });
    }, function(dismiss) {
      if ($oldVal == "checked") {
        $checkObj.prop('checked', false);
      } else {
        $checkObj.prop('checked', true);
      }
    });
  });
  $(document).on('click', '#chk_all', function() {
    if ($(this).prop('checked') == true) {
      $(".check_record").prop("checked", true);
      $("#delete_multiple_row").show();
    } else {
      $(".check_record").prop("checked", false);
      $("#delete_multiple_row").hide();
      $("#assigned_to").hide('slow');
      $("#assigned_to_multiple_td").show('slow');
    }
  });
  $(document).keypress(function(e) {
    if (e.which == 13) {
      ajax_submit_leads();
    }
  });
  $(document).on('click', '.check_record', function() {
    var id = $(this).data('id');
    var len = $('[name="check_record[]"]:checked').length;
    if ($(this).prop('checked') == true) {
      $("#delete_multiple_row").show();
    } else {
      $("#delete_multiple_row").hide();
    }
    if (len > 0) {
      $("#delete_multiple_row").show();
    } else {
      $("#delete_multiple_row").hide();
      $("#assigned_to").hide('slow');
      $("#assigned_to_multiple_td").show('slow');
    }
  });
  $(document).on('click', '#delete_multiple', function() {
    var curr_ajax_url = $("#curr_ajax_url").val();
    var checked = [];
    $("input[name='check_record[]']:checked").each(function() {
      checked.push(parseInt($(this).val()));
    });
    swal({
      text: 'Delete Lead: Are you sure?',
      showCancelButton: true,
      confirmButtonText: 'Confirm',
      cancelButtonText: 'Cancel',
    }).then(function() {
      $.ajax({
        url: "ajax_delete_lead.php",
        type: 'GET',
        data: {
          multiple_id: checked,
          curr_ajax_url: curr_ajax_url
        },
        dataType: 'json',
        success: function(res) {
          if (res.status == 'success') {
            setNotifySuccess(res.msg);
            //window.location.reload();
            //window.location.href = 'leads.php';
            ajax_submit_leads();
          }
        }
      });
    }, function(dismiss) {
      window.location.reload();
    })
  });
  $(document).on('click', "#assigned_to_multiple", function() {
    $("#assigned_to_multiple_td").hide('slow');
    $("#assign_user").val("");
    $("#assigned_to").show('slow');
  });
  $(document).on('click', "#assigned_btn", function() {
    $sponsorId = $("#assign_user").val();
    if ($sponsorId != '') {
      var curr_ajax_url = $("#curr_ajax_url").val();
      var checked = [];
      $("input[name='check_record[]']:checked").each(function() {
        checked.push(parseInt($(this).val()));
      });
      swal({
        text: 'Assigne Lead: Are you sure?',
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
      }).then(function() {
        $("#assigned_to").hide('slow');
        $("#assigned_to_multiple_td").show('slow');
        $.ajax({
          url: "ajax_assigne_lead.php",
          type: 'POST',
          data: {
            multiple_id: checked,
            curr_ajax_url: curr_ajax_url,
            sponsorId: $sponsorId
          },
          dataType: 'json',
          success: function(res) {
            if (res.status == 'success') {
              setNotifySuccess(res.msg);
              window.location.reload();
            }
          }
        });
      }, function(dismiss) {
        window.location.reload();
      })
    } else {
      swal("Oops!", "Select Agent/Call Center To Assign Lead", "warning");
    }
  });

  
  $(document).ready(function() {
    // Copy invitation link in clipboard
    ajax_submit_leads();
    $(document).off('click', '#ajax_data tr.data-head a');
    $(document).on('click', '#ajax_data tr.data-head a', function(e) {
      e.preventDefault();
      $('#sort_by_column').val($(this).attr('data-column'));
      $('#sort_by_direction').val($(this).attr('data-direction'));
      ajax_submit_leads();
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
          common_select();
        }
      });
    });
  });

  function delete_lead(lead_id) {
    swal({
      text: 'Delete Lead: Are you sure?',
      showCancelButton: true,
      confirmButtonText: 'Confirm',
      cancelButtonText: 'Cancel',
    }).then(function() {
      $.ajax({
        url: "ajax_delete_lead.php",
        type: 'GET',
        data: {
          id: lead_id
        },
        dataType: 'json',
        success: function(res) {
          if (res.status == 'success') {
            // setNotifySuccess(res.msg);
            window.location.reload();
            //window.location.href = 'leads.php';
            // redirect_after_delete();
          } else {
            window.location.reload();
          }
        }
      });
    }, function(dismiss) {
      window.location.reload();
    })
  }

  function ajax_submit_leads() {
    $('#ajax_loader').show();
    $('#ajax_data').hide();
    $('#is_ajaxed_leads').val('1');
    var params = $('#frm_search_leads').serialize();
    var all_usersFrm = $('#all_usersFrm').serialize();
    params += '&' + all_usersFrm;
    $.ajax({
      url: $('#frm_search_leads').attr('action'),
      type: 'GET',
      data: params,
      success: function(res) {
        $('#ajax_loader').hide();
        $('#ajax_data').html(res).show();
        common_select();
      }
    });
    return false;
}
</script>
<?php } ?>