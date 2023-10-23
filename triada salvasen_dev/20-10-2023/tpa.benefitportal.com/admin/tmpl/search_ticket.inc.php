<?php if ($is_ajaxed) { ?>
  <div class="table-responsive">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="<?=$table_class?>">
      <thead>
        <tr class="data-head">
          <th class="fix_width_t">Ticket ID</th>
          <th width="10%">Name</th>
          <th width="10%">Phone</th>
          <th width="5%">Category</th>
          <th width="15%">Status</th>
          <th>Opened By</th>
          <th>Last Replied</th>
          <th width="10%">Assigned</th>
          <th>Last Replier</th>
          <th>Notes</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($fetch_rows) > 0) {
          foreach ($fetch_rows as $rows) { ?>
            <tr>
              <td class="fix_width_t">
                <a href="javascript:void(0);" class="ticket_notify_ul" data-ticket-id=<?php echo $rows['id']; ?>><?php echo $rows['tracking_id']; ?></a><br/><?php echo date($DATE_FORMAT, strtotime($rows['created_at'])); ?>
              </td>
              <td>
                <?php if ($rows['user_id'] > 0) {
                  echo $rows['cust_fname'] . " " . $rows['cust_lname'] . "<br/>" . $rows['cust_email'];
                  if ($_SESSION['admin']['type'] == 'Support') {
                    echo "<br/><a href='javascript:void(0);' data-href=support_member_details.php?user_id=" . $rows['user_id'] . "&type=" . $rows['cust_type'] . " class=member_popup>" . $rows['rep_id'] . "</a>";
                  } else {
                    if ($rows['cust_type'] == 'Affiliates') {?>
                      <br/><a href="javascript:void(0);" data-id="<?php echo $rows['user_id']; ?>" id="<?=$affiliates_menu?>" class="affiliates_link"><?=$rows['rep_id']?></a>
                    <?php } else if ($rows['cust_type'] == 'Customer') {?>
                      <br/><a href="javascript:void(0);" data-id="<?php echo $rows['user_id']; ?>" id="<?=$member_menu?>" class="member_link"><?=$rows['rep_id']?></a>
                    <?php } else if ($rows['cust_type'] == 'Agent') {?>
                      <br/><a href="javascript:void(0);" data-id="<?php echo $rows['user_id']; ?>" id="<?=$agent_menu?>" class="agent_link"><?=$rows['rep_id']?></a>
                    <?php } else if ($rows['cust_type'] == 'Group') {?>
                      <br/><a href="javascript:void(0)" id="<?=$group_menu?>" data-id="<?=$rows['user_id']?>" class="group_link"> <?=$rows['rep_id']?> </a>
                    <?php }
                  }
                } else {
                  echo $rows['name'] . "<br/>" . $rows['email'];
                  echo "<br/><a href='javascript:void(0)'>Guest</a>";
                }?>
              </td>
              <td>
                <?php if(!empty($rows['phone'])){                           
                    $phone1 = substr($rows['phone'], 0, 3);
                    $phone2 = substr($rows['phone'], 3, 3);
                    $phone3 = substr($rows['phone'], 6, 4);
                  } else {
                    $phone1 = substr($rows['cell_phone'], 0, 3);
                    $phone2 = substr($rows['cell_phone'], 3, 3);
                    $phone3 = substr($rows['cell_phone'], 6, 4);
                  }
                ?>
                <a href="tel:<?php echo !empty($rows['phone']) ? $rows['phone'] : $rows['cell_phone']; ?>"><?php echo "(" .$phone1.") " .$phone2. "-".$phone3  ?></a>
              </td>
              <td><?php echo $rows['cat_name']; ?></td>
              <td><?=$rows['status'];?></td>
              <td>
                <?php if ($rows['opened_by'] > 0) {
                  echo $name = getname('admin', $rows['opened_by'], 'fname');
                } else {
                  echo '-';
                } ?>
              </td>
              <td><?php echo $rows['last_replied'] != '0000-00-00 00:00:00' ? date($DATE_FORMAT, strtotime($rows['last_replied'])) : ''; ?></td>
              <td><?php 
                if(!empty($rows['tc_admin_id'])){
                  $admin_res = $pdo->selectOne("SELECT fname, lname FROM admin WHERE id = :id AND status ='Active' AND is_active = 'Y'", array(":id" => $rows['tc_admin_id']));
                  if(!empty($admin_res)){
                    echo $admin_res['fname'] . ' '. $admin_res['lname'];
                  } else {
                    echo '-';
                  }
                } else {
                  echo '-';
                }?></td>
              <td>
                <?php if ($rows['user_type'] == 'Affiliates') {
                  echo "<i class='fa fa-eject fa-rotate-90 green pull-left'></i>" . getname('admin', $rows['user_id'], 'fname');
                } else {
                  if ($rows['user_id'] > 0) {
                    echo "<i class='fa fa-eject fa-rotate-270 red pull-left'></i>" . "&nbsp;" . $rows['cust_fname'] . " " . $rows['cust_lname'];
                  } else {
                    echo "<i class='fa fa-eject fa-rotate-270 red pull-left'></i>" . "&nbsp;" . $rows['name'];
                  }
                } ?>
              </td>
              <td class="icons"> <a href="javascript:void(0)" data-href="support_note.php?ticket_id=<?=$rows['id']?>" title="Notes" data-toggle="tooltip" class="note_popup"><i class="fa fa-sticky-note-o"></i></a></td>
            </tr>
          <?php } ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="11">
            <?php echo $paginate->links_html; ?>
          </td>
        </tr>
      </tfoot>
      <?php } else { ?>
        <tfoot>
          <tr>
            <td colspan="11" class="text-center">
              No Record(s)
            </td>
          </tr>
        </tfoot>
      <?php }?>
    </table>
  </div>
<?php } else { ?>
  <?php include_once 'notify.inc.php';?>
  <div class="panel panel-default panel-block panel-title-block" style="display: none;">
    <form id="frm_search" action="search_ticket.php" method="GET" class="sform">
      <div class="panel-wrapper collapse in">
        <div class="panel-footer clearfix">
          <button type="submit" class="btn btn-info" name="search" id="search"><i class="fa fa-search"></i> Search
          </button>
          <button type="button" class="btn btn-info" name="viewall" id="viewall" onClick="window.location = 'search_ticket.php'"><i class="fa fa-search-plus"></i> View All
          </button>
          <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1"/>
          <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>"/>
          <input type="hidden" name="ticket_search" id="ticket_search" value="<?=$ticket_search;?>"/>
          <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>"/>
          <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>"/>
          <div id="top_paginate_cont" class="pull-right">
            <div class="col-md-12">
              <div class="form-inline text-right" id="DataTables_Table_0_length">
                <div class="form-group">
                  <label for="user_type">Records Per Page </label>
                </div>
                <div class="form-group">
                  <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value); ajax_submit();">
                    <option value="10" <?php echo $_GET['pages'] == 10 ? 'selected' : ''; ?>>10
                    </option>
                    <option value="25" <?php echo $_GET['pages'] == 25 || $_GET['pages'] == "" ? 'selected' : ''; ?>>
                        25
                    </option>
                    <option value="50" <?php echo $_GET['pages'] == 50 ? 'selected' : ''; ?>>50
                    </option>
                    <option value="100" <?php echo $_GET['pages'] == 100 ? 'selected' : ''; ?>>100
                    </option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
  <div class="panel panel-default panel-block">
    <div class="list-group">
      <div id="ajax_loader" class="ajex_loader" style="display: none;">
        <div class="loader"></div>
      </div>
      <div id="ajax_data" class=""></div>
    </div>
  </div>

  <script type="text/javascript">
    $(document).on("submit","#frm_search",function(e){
      e.preventDefault();
      ajax_submit();
    });

    $(document).ready(function () {
      ajax_submit();

      $(document).off('click', '#ajax_data tr.data-head a');
      $(document).on('click', '#ajax_data tr.data-head a', function (e) {
        e.preventDefault();
        $('#sort_by_column').val($(this).attr('data-column'));
        $('#sort_by_direction').val($(this).attr('data-direction'));
        ajax_submit();
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
            parent.resizeIframe($("body").outerHeight(), 'ticket_search_iframe');
          }
        });
      });
    });

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
          parent.resizeIframe($("body").height() + 20, 'ticket_search_iframe');
          $("[data-toggle=popover]").each(function(i, obj) {
            $(this).popover({
              html: true,
              placement:'auto bottom',
              content: function() {
                var id = $(this).attr('data-user_id') 
                return $('#popover_content_'+id).html();
              }
            });
          });
        }
      });
      return false;
    }

    $(document).on('click','.member_popup', function(){
    link = $(this).attr('data-href');
    parent.$.colorbox({
      href: link,
      iframe: true,
      width: '800px',
      height: '600px'
    });
  });

  $(document).on('click','.note_popup', function(){
    link = $(this).attr('data-href');
    parent.$.colorbox({
      href: link,
      iframe: true,
      width: '900px',
      height: '600px'
    });
  });

  $(document).on('click', '.agent_link', function() {
    var id = $(this).attr('id');
    var customer_id = $(this).attr('data-id');
    if(id == 1){
      if(customer_id == undefined)
        window.top.location = 'agent_listing.php';
      else
        window.top.location = 'agent_detail.php?id='+customer_id;
    } else {
      // parent.setNotifyError("Link is not Active");
      parent.swal({
        title: "No Access",
        html: "Access to this area has not been granted. Please inquire with an administrator to view this page",
        type: "warning",
        confirmButtonText: "Ok",
        allowOutsideClick: false,
      }).then(function () {
      }, function (dismiss) {
      });
    }
  });

  $(document).on('click', '.group_link', function() {
    var id = $(this).attr('id');
    var customer_id = $(this).attr('data-id');
    if(id == 1){
      if(customer_id == undefined)
        window.top.location = 'group_list.php';
      else
        window.top.location = 'group_detail.php?id='+customer_id;
    } else {
      // parent.setNotifyError("Link is not Active");
      parent.swal({
        title: "No Access",
        html: "Access to this area has not been granted. Please inquire with an administrator to view this page",
        type: "warning",
        confirmButtonText: "Ok",
        allowOutsideClick: false,
      }).then(function () {
      }, function (dismiss) {
      });
    }
  });

  $(document).on('click', '.member_link', function() {
    var id = $(this).attr('id');
    var customer_id = $(this).attr('data-id');
    if(id == 1){
      if(customer_id == undefined)
        window.top.location = 'customer_listing.php';
      else
        window.top.location = 'customer_detail.php?id=' + customer_id;
    } else {
      // parent.setNotifyError("Link is not Active");
      parent.swal({
        title: "No Access",
        html: "Access to this area has not been granted. Please inquire with an administrator to view this page",
        type: "warning",
        confirmButtonText: "Ok",
        allowOutsideClick: false,
      }).then(function () {
      }, function (dismiss) {
      });
    }
  });

  $(document).on('click', '.affiliates_link', function() {
    var id = $(this).attr('id');
    var customer_id = $(this).attr('data-id');
    if(id == 1){
      if(customer_id == undefined)
        window.top.location = 'affiliate_profile.php';
      else
        window.top.location = 'affiliate_detail.php?id='+customer_id;
    } else {
      // parent.setNotifyError("Link is not Active");
      parent.swal({
        title: "No Access",
        html: "Access to this area has not been granted. Please inquire with an administrator to view this page",
        type: "warning",
        confirmButtonText: "Ok",
        allowOutsideClick: false,
      }).then(function () {
      }, function (dismiss) {
      });
    }
  });

  var ticket_window = ""
  $(document).off('click', '.ticket_notify_ul');
  $(document).on('click', '.ticket_notify_ul', function() {
    url = 'ticket_details.php?id=' + $(this).attr('data-ticket-id');
    if (!ticket_window.opener) {
      ticket_window = window.open(url, "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=0, left=100, width=900, height=800");
    } else {
      ticket_window.load_new_tab(url);
    }
    ticket_window.focus();
  });
  // setInterval(function() {    
  //   // Get new ticket notification
  //   $.ajax({
  //     url: "ajax_ticket_notifcation.php",
  //     method: 'POST',
  //     success: function(data) {
  //       if (data.status == "success") {
  //         if (data.count == 0) {
  //           $('#notification_ticket .menu-counter').hide();
  //         } else {
  //           $('#notification_ticket .menu-counter').html(data.count).show();
  //         }
  //         $('#ticket_notify_ul_data').html(data.html);
  //       }
  //     }
  //   });
  // }, 18000);

  </script>
<?php } ?>