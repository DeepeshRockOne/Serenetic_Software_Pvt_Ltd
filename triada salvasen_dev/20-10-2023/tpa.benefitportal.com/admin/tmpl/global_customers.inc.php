<script>
  $(document).off('click',".member_tree_popup");
    $(document).on('click',".member_tree_popup",function(e){
        $href = $(this).attr('data-href');
        $.colorbox({
            iframe:true,
            href:$href,
            width: '900px',
            height: '650px',
            onClosed :function(e){
              ajax_submit_customers();
            }
        });
    });

    contentCalled = false;
    $('.member_product_popover').popover({
        html: true,
        container: 'body',
        trigger: 'click',
        template: '<div class="popover full_width_popover"><div class="arrow"></div><div class="popover-content"></div></div>',
        placement: 'auto top',
        content: function() {
          var id_val = $(this).attr('data-id');
          getPopoverData(id_val)
          return $('#popover_content_wrapper_' + id_val).html();
        }
      });

      function getPopoverData(id_val) {
        if (!contentCalled) {
          contentCalled = true;
          return " ";
        } else {
          $.ajax({
            url: 'get_member_products_popover.php',
            data: {
              id: id_val
            },
            method: 'GET',
            async: false,
            success: function(res) {
              contentCalled = false;              
              $('#popover_content_wrapper_' + id_val).html(res);
            }
          });
        }
      }

  $('body').on('click', function (e) {
          $('.member_product_popover').each(function () {
              // hide any open popovers when the anywhere else in the body is clicked
              if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                  $(this).popover('hide');
              }
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
          $.ajax({
            url: 'change_member_status.php',
            data: {
              id: id,
              status: member_status
            },
            method: 'POST',
            dataType: 'json',
            success: function(res) {
              if (res.status == "success") {
                setNotifySuccess(res.msg);
              } else {
                setNotifyError(res.msg);
                ajax_submit_customers();
              }
            }
          });
        }, function(dismiss) {
          ajax_submit_customers();
        })
      });
</script>
<?php if($is_ajaxed_customers){ ?>
<div class="clearfix"></div>
<div class="table-responsive">
<table class="<?= $table_class ?>">
  <thead class="">
    <tr>
      <tr class="data-head">
          <th><a href="javascript:void(0);" data-column="c.joined_date" data-direction="<?php echo $SortBy == 'c.joined_date' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">ID/Added Date</a></th>
          <th><a href="javascript:void(0);" data-column="c.fname" data-direction="<?php echo $SortBy == 'c.fname' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Details</a></th>
          <th width="10%"><a href="javascript:void(0);" data-column="scs.company" data-direction="<?php echo $SortBy == 'scs.company' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Company</a></th>
          <th><a href="javascript:void(0);" data-column="c.sponsor_id" data-direction="<?php echo $SortBy == 'c.sponsor_id' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Enrolling Agent ID/Name</a></th>
          <th width="25%" class="text-center"><a href="javascript:void(0);" data-column="total_products" data-direction="<?php echo $SortBy == 'total_products' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Products</a></th>
          <th><a href="javascript:void(0);" data-column="c.status" data-direction="<?php echo $SortBy == 'c.status' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Status</a></th>
          <th width="130px">Actions</th>
        </tr>   
    </tr>
  </thead>
  <tbody>
    <?php
    if ($total_rows > 0) {
      foreach ($fetch_rows as $rows) {?>
        <tr>
              <td><a href="members_details.php?id=<?php echo $rows['id']; ?>" target="_blank" class="fw500 text-action"><?php echo $rows['rep_id']; ?></a><br><?php echo displayDate($rows['joined_date']); ?></td>
              <td>
                <strong><?php echo $rows['fname'] . " " . $rows['lname']; ?></strong><br>
                <?php echo isset($rows['cell_phone']) ? format_telephone($rows['cell_phone']) : ''; ?><br>
                <?php echo $rows['email']; ?>
              </td>
              <td><?=$rows['company']?></td>
              <td><a href="javascript:void(0)" data-href="member_tree_popup.php?agent_id=<?php echo $rows['sponsor_id']; ?>&member_id=<?=$rows['id']?>&type=Member" class="fw500 text-action member_tree_popup"><?php echo $rows['sponsor_rep_id']; ?></a><br><?php echo $rows['s_fname'] . " " . $rows['s_lname']; ?></td>
              <td class="text-center"><a href="javascript:void(0);" class="member_product_popover fw500 text-action" data-id="<?php echo $rows['id']; ?>"><?php echo $rows['total_products']; ?></a>
                <div id="popover_content_wrapper_<?php echo $rows['id']; ?>" style="display: none">
                </div>

              </td>
              <td>
                <div class="theme-form w-200 pr">
                  <select class="form-control member_status has-value" name="member_status" id="member_status_<?php echo $rows['id']; ?>">
                    <option value="Active" <?php echo $rows['status'] == 'Active' ? 'selected="selected"' : ""; ?>>Active</option>
                    <?php /*<option value="Post Payment" <?php echo $rows['status'] == 'Post Payment' ? 'selected="selected"' : ""; ?>>Pending</option>*/ ?>
                    <option value="Hold" <?php echo $rows['status'] == 'Hold' ? 'selected="selected"' : ""; ?>>On Hold</option>
                    <option value="Inactive" <?php echo $rows['status'] == 'Inactive' ? 'selected="selected"' : ""; ?>>Inactive</option>
                    <?php if($rows['status'] == 'Inactive Failed Billing') {?>
                    <option value="Inactive" <?php echo $rows['status'] == 'Inactive Failed Billing' ? 'selected="selected"' : ""; ?>><?=$rows['status']?></option>
                    <?php } ?>
                  </select>
                  <label>Status</label>
                </div>
              </td>
              <td class="icons">
                <a href="members_details.php?id=<?php echo $rows['id']; ?>" target="_blank" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="View"><i class="fa fa-eye"></i></a>

                <?php if ($rows["stored_password"] != "" && !in_array($rows['status'],array("Pending","Customer Abandon","Pending Validation"))) { ?>
                      <a data-toggle="tooltip" data-trigger="hover" href="switch_login.php?id=<?php echo $rows['id']; ?>" target="blank" title="Access Site"><i class="fa fa-lock"></i></a>
                <?php }?>
              </td>
            </tr>
      <?php } ?>
    <tfoot>
      <tr>
        <td colspan="8"><?php echo $paginate->links_html; ?></td>
      </tr>
    </tfoot>
  <?php } else { ?>
    <tr class="text-center">
      <td colspan="8"> No Record(s) </td>
    </tr>
  <?php } ?>
</tbody>
</table> 
</div>

<?php } else { ?>

    <form id="frm_search_customers" action="global_customers.php" method="GET" class="sform" >    
      <input type="hidden" name="search_type" id="search_type" value="" />
      <input type="hidden" name="is_ajaxed_customers" id="is_ajaxed_customers" value="1" />
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
//$('.detail-popup').colorbox({iframe:true,width:'85%', height:'90%;'});
$(".id_card").colorbox({iframe: true,width:'1100px', height:'600px'});
  
  $(document).on('click','.detail-popup',function(e){
    e.preventDefault();
    $.colorbox({
      href: $(this).attr('href'),
      iframe:true,
      width:'850px', 
      height:'500px;'
    });
  });

  $(document).ready(function() {

    // Copy invitation link in clipboard
    ajax_submit_customers();
    $(document).off('click', '#ajax_data tr.data-head a');
    $(document).on('click', '#ajax_data tr.data-head a', function (e) {
        e.preventDefault();
        $('#sort_by_column').val($(this).attr('data-column'));
        $('#sort_by_direction').val($(this).attr('data-direction'));
        ajax_submit_customers();
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
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    });
  });
  function delete_customer(customer_id) {
              swal({
                text: 'Delete Customer: Are you sure?',
                showCancelButton: true,
                confirmButtonText: 'Confirm',
                cancelButtonText: 'Cancel',
              }).then(function() {
                $.ajax({
                  url: "ajax_delete_customer.php",
                  type: 'GET',
                  data: {id: customer_id},
                  dataType: 'json',
                  success: function(res) {
                    if (res.status == 'success')
                    {
                      // setNotifySuccess(res.msg);
                      window.location.reload();
                      // redirect_after_delete();
                    }
                    else {
                      // setNotifyError(res.msg);
                      window.location.reload();
                      // redirect_after_delete(); 
                    }
                  }
                });
              }, function(dismiss) {
                window.location.reload();
              })
            }

$(document).on('click','.affiliates_link',function(){
                var id = $(this).attr('id');
                var customer_id = $(this).attr('data-id');
                if(id == 1){
                  if(customer_id == undefined)
                    window.location = 'affiliate_profile.php';
                  else
                    window.location = 'affiliate_detail.php?id='+customer_id;
                } else {
                  setNotifyError("Link is not Active");
                }
            });

            $(document).on('click', '.member_link',function(e){
                e.preventDefault();
                var id = $(this).attr('id');
                var customer_id = $(this).attr('data-id');
                if(id == 1){
                  if(customer_id == undefined)
                    //window.location = 'customer_listing.php';
                    window.open("customer_listing.php", "_self");
                  else
                    //window.location = 'customer_detail.php?id=' + customer_id;
                    window.open('member_detail.php?id=' + customer_id, "_self");
                } else {
                  setNotifyError("Link is not Active");
                }
            });

            $(document).on('click','.agent_link',function(){
                var id = $(this).attr('id');
                var customer_id = $(this).attr('data-id');
                if(id == 1){
                  if(customer_id == undefined)
                    window.location = 'agent_listing.php';
                  else
                    window.location = 'agent_detail.php?id='+customer_id;
                } else {
                  setNotifyError("Link is not Active");
                }
            });
            $(document).on('click', '.group_link',function(){
                var id = $(this).attr('id');
                var customer_id = $(this).attr('data-id');
                if(id == 1){
                  if(customer_id == undefined)
                    window.location = 'group_list.php';
                  else
                    window.location = 'group_detail.php?id=' + customer_id;
                } else {
                  setNotifyError("Link is not Active");
                }
            });

  function ajax_submit_customers() {
      $('#ajax_loader').show();
      $('#ajax_data').hide();
      $('#is_ajaxed_customers').val('1');

      var params = $('#frm_search_customers').serialize();
      var all_usersFrm = $('#all_usersFrm').serialize();
      params += '&'+all_usersFrm;
      $.ajax({
          url: $('#frm_search_customers').attr('action'),
          type: 'GET',
          data: params,
          success: function (res) {
              $('#ajax_loader').hide();
              $('#ajax_data').html(res).show();
              common_select();
              $('[data-toggle="tooltip"]').tooltip();
          }
      });
      return false;
   }


</script>
<?php } ?>