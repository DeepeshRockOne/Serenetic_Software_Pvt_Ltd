<style type="text/css">
  .popover{
    max-width:600px;
  }
</style>
<style type="text/css">
  .popover {min-width: 295px;}
</style>
<?php if ($is_ajaxed) { ?>
  <div class="table-responsive">
    <table class="<?=$table_class?>">
      <thead>
        <tr class="data-head">
          <th><a href="javascript:void(0);" data-column="c.created_at" data-direction="<?php echo $SortBy == 'c.created_at' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Join Date</a></th>
          <th><a href="javascript:void(0);" data-column="c.rep_id" data-direction="<?php echo $SortBy == 'c.rep_id' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Member ID</a></th>
          <th><a href="javascript:void(0);" data-column="c.fname" data-direction="<?php echo $SortBy == 'c.fname' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Name</a></th>
          <th><a href="javascript:void(0);" data-column="dep_count" data-direction="<?php echo $SortBy == 'dep_count' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Dependents</a></th>
          <th><a href="javascript:void(0);" data-column="c.email" data-direction="<?php echo $SortBy == 'c.email' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Email</a></th>
          <th><a href="javascript:void(0);" data-column="s.fname" data-direction="<?php echo $SortBy == 's.fname' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Enroller</a></th>
          <th><a href="javascript:void(0);" data-column="c.status" data-direction="<?php echo $SortBy == 'c.status' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Status</a></th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($total_rows > 0) {
          foreach ($fetch_rows as $rows) { ?>
            <tr>
              <td><?php echo date('m/d/Y', strtotime($rows['created_at'])); ?></td>
              <td>
                <?php if($member_menu){?>  
                  <a href="member_detail.php?id=<?php echo $rows['id']; ?>&search=Y" target="_parent">
                    <?php echo $rows['rep_id'] ?>
                  </a>
                <?php } else{ ?>
                  <?php echo $rows['rep_id'] ?>
                <?php } ?>
              </td>
              <td><?php echo stripslashes($rows['fname'] . ' ' . $rows['lname']); ?></td>
              <td>
                <?php echo "<a class='detail-popup' href='javascript:void(0);' data-href='dependants_detail_popup_new.php?dependent=".$rows['dependent_display_id']."&id=" . $rows["id"] . "'>" . $rows["dep_count"] . "</a>"; ?>    
              </td>
              <td><?php echo $rows['email']; ?></td>
              <td>
                <?php if ($rows['s_business_name'] != "") {?>
                  <?php if ($rows['s_type'] == 'Agent') {?>
                    <a href="javascript:void(0);" data-id="<?php echo $rows['sponsor_id']; ?>" id="<?=$agent_menu?>" class="agent_link" data-toggle="tooltip" data-toggle="tooltip">
                    <?php echo $rows['s_business_name']; ?></a>
                  <?php } else if ($rows['s_type'] == 'Affiliates') {?>
                    <a href="javascript:void(0);" data-id="<?php echo $rows['sponsor_id']; ?>" id="<?=$affiliates_menu?>" class="affiliates_link" data-toggle="tooltip" data-original-title="<?php echo $rows['sponsor_rep_id']; ?>">
                    <?php echo $rows['s_business_name']; ?></a>
                  <?php } else {?>
                    <a href="javascript:void(0);" data-id="<?php echo $rows['sponsor_id']; ?>" id="<?=$group_menu?>" class="group_link" data-toggle="tooltip" data-original-title="<?php echo $rows['sponsor_rep_id']; ?>">
                    <?php echo $rows['s_business_name']; ?></a>
                  <?php }?>
                <?php } else {?>
                  <?php if ($rows['s_type'] == 'Agent') {?>
                    <a href="javascript:void(0);" data-id="<?php echo $rows['sponsor_id']; ?>" id="<?=$agent_menu?>" class="agent_link" data-toggle="tooltip" data-toggle="tooltip">
                    <?php echo $rows['s_fname'] . " " . $rows['s_lname']; ?></a>
                  <?php } else if ($rows['s_type'] == 'Affiliates') {?>
                    <a href="javascript:void(0);" data-id="<?php echo $rows['sponsor_id']; ?>" id="<?=$affiliates_menu?>" class="affiliates_link" data-toggle="tooltip" data-original-title="<?php echo $rows['sponsor_rep_id']; ?>">
                    <?php echo $rows['s_fname'] . " " . $rows['s_lname']; ?></a>
                  <?php } else {?>
                    <a href="javascript:void(0);" data-id="<?php echo $rows['sponsor_id']; ?>" id="<?=$group_menu?>" class="group_link" data-toggle="tooltip" data-original-title="<?php echo $rows['sponsor_rep_id']; ?>">
                    <?php echo $rows['s_fname'] . " " . $rows['s_lname']; ?></a>
                  <?php }?>
                <?php } ?>
              </td>
              <td><?php echo $rows['status']; ?> </td>
              <td class="icons ">
                <div class="text-left">
                  <a href="javascript:void(0);" data-id="<?php echo $rows['id']; ?>" id="<?=$member_menu?>" class="member_link"><i class="fa fa-edit"></i></a>
                  <a href="mailto:<?php echo $rows['email']; ?>" data-toggle="tooltip" title="<?=(isset($rows['email']) ? $rows['email'] : '-');?>"><i class="fa fa-envelope"></i></a>
                  <?php if ($rows['cell_phone'] != "") {?>
                    <a href="tel:<?php echo "+1" . $rows['cell_phone'] ?>" data-toggle="tooltip" title="<?=(isset($rows['cell_phone']) ? "+1" . $rows['cell_phone'] : '-');?>"><i class="fa fa-phone"></i></a>
                  <?php }?>
                   <a data-toggle="tooltip" target="_blank" href="switch_login.php?id=<?php echo md5($rows['id']); ?>" data-original-title="Access Member Site"><i class="fa fa-lock"></i></a>
                  <?php if (($_SESSION['admin']['id'] == 58) || ($_SESSION['admin']['id'] == 72) ) {?>
                    <a href="javascript:void(0);"  data-toggle="tooltip" title="Delete" onclick="delete_customer(<?=$rows['id']?>)"><i class="fa fa-trash"></i></a>
                  <?php }?>
                </div>
              </td>
            </tr>
          <?php } ?>
        <?php } else {?>
          <tr>
            <td colspan="9">No record(s) found</td>
          </tr>
        <?php }?>
      </tbody>
      <?php if ($total_rows > 0) {?>
        <tfoot style="<?=($recent=='Y') ? 'display: none' : ''?>">
          <tr>
            <td colspan="9"><?php echo $paginate->links_html; ?></td>
          </tr>
        </tfoot>
      <?php }?>
    </table>
  </div>
  <script>
    $(function(){
      $('.detail-popup').click(function(){
        var href = $(this).attr('data-href');
        top.parent.parent_window_colorbox({href: href, iframe: true, width: '900px', height: '500px'});
      // $('.detail-popup').colorbox({iframe:true,width:'75%', height:'80%;'});
      });
    });
  </script>
  <script type="text/javascript">
    $(document).keypress(function (e) {
      if (e.which == 13) {
        ajax_submit();
      }
    });
  </script>
<?php } else { ?>
  <?php include_once 'notify.inc.php';?>
  <div class="panel panel-default panel-block panel-title-block" style="display: none;">
    <form id="frm_search" action="search_members.php" method="GET" class="sform">
      <div class="panel-wrapper collapse in">
        <div class="panel-footer clearfix">
          <button type="submit" class="btn btn-info" name="search" id="search" > <i class="fa fa-search"></i> Search </button>
          <button type="button" class="btn btn-info" name="viewall" id="viewall" onClick="window.location = 'search_members.php'"> <i class="fa fa-search-plus"></i> View All </button>
          <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
          <input type="hidden" name="recent" id="recent" value="<?=$recent?>"/>
          <input type="hidden" name="member_search" id="member_search" value="<?=$member_search?>"/>
          <input type="hidden" name="member_multiple_search" id="member_multiple_search" value="<?=$member_multiple_search?>"/>
          <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
          <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
          <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
          <input type="hidden" name="export" id="export" value=""/>
          <div id="top_paginate_cont" class="pull-right">
            <div class="col-md-12">
              <div class="form-inline text-right" id="DataTables_Table_0_length">
                <div class="form-group">
                  <label for="user_type">Records Per Page </label>
                </div>
                <div class="form-group">
                  <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value); ajax_submit();">
                    <option value="10" <?php echo $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
                    <option value="25" <?php echo $_GET['pages'] == 25 || $_GET['pages'] == "" ? 'selected' : ''; ?>>25</option>
                    <option value="50" <?php echo $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
                    <option value="100" <?php echo $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
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
      <div id="ajax_data" class=""> </div>
    </div>
  </div>

  <script type="text/javascript">
    $(document).ready(function() {
      ajax_submit();

      $(document).on('click','.agent_link',function(){
        var id = $(this).attr('id');
        var customer_id = $(this).attr('data-id');
        if(id == 1){
          if(customer_id == undefined)
            window.top.location = 'agent_listing.php';
          else
            window.top.location = 'agent_detail.php?id='+customer_id;
        } else {
          // setNotifyError("Link is not Active");
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

      $(document).on('click','.affiliates_link',function(){
        var id = $(this).attr('id');
        var customer_id = $(this).attr('data-id');
        if(id == 1){
          if(customer_id == undefined)
            window.top.location = 'affiliate_profile.php';
          else
            window.top.location = 'affiliate_detail.php?id='+customer_id;
        } else {
          // setNotifyError("Link is not Active");
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

      $(document).on('click', '.member_link',function(e){
        e.preventDefault();
        var id = $(this).attr('id');
        var customer_id = $(this).attr('data-id');
        if(id == 1){
          if(customer_id == undefined)
            window.open("member_detail.php", "_parent");
          else
            window.open('member_detail.php?id=' + customer_id + '&search=Y', "_parent");
        } else {
          // setNotifyError("Link is not Active");
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

      $(document).on('click', '.group_link',function(){
        var id = $(this).attr('id');
        var customer_id = $(this).attr('data-id');
        if(id == 1){
          if(customer_id == undefined)
            window.top.location = 'group_list.php';
          else
            window.top.location = 'group_detail.php?id=' + customer_id;
        } else {
          // setNotifyError("Link is not Active");
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
            <?php if($recent=='Y') { ?>
              frame_name = 'recent_member_iframe';
            <?php } else { ?>
              frame_name = 'member_search_iframe';
            <?php } ?>
            parent.resizeIframe($("body").height() + 20, frame_name);
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
        success: function(res) {
          $('#ajax_loader').hide();
          $('#ajax_data').html(res).show();
          <?php if($recent=='Y') { ?>
            frame_name = 'recent_member_iframe';
          <?php } else { ?>
            frame_name = 'member_search_iframe';
          <?php } ?>
          parent.resizeIframe($("body").height() + 20, frame_name);
        }
      });
      return false;
    }

    function delete_customer(customer_id) {
      <?php if($recent=='Y') { ?>
        frame_name = 'recent_member_iframe';
      <?php } else { ?>
        frame_name = 'member_search_iframe';
      <?php } ?>
      parent.resizeIframe($("body").height() + 20, frame_name);
      parent.swal({
        text: 'Delete Customer: Are you sure?',
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
      }).then(function() {
        $.ajax({
          url: "ajax_delete_customer.php",
          type: 'GET',
          data: {id: customer_id, search : 'Y'},
          dataType: 'json',
          success: function(res) {
            if (res.status == 'success') {
              parent.setNotifySuccess(res.msg);
              window.location.reload();
            } else {
              parent.setNotifySuccess(res.msg);
              window.location.reload();
            }
          }
        });
      }, function(dismiss) {
        window.location.reload();
      })
    }
  </script>
<?php }?>