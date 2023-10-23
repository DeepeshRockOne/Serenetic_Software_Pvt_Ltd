<script>
   !function($) {
    "use strict";
    var SweetAlert = function() {
    };
    //examples 
    SweetAlert.prototype.init = function() {
      $('.member_status').change(function() {
        var id = $(this).attr('id').replace('member_status_', '');
        var member_status = $(this).val();
        swal({
          text: "Change Status: Are you sure?",
          confirmButtonText: "Confirm",
          showCloseButton: true
        }).then(function() {
          $.ajax({
            url: 'change_global_user_status.php',
            data: {id: id, status: member_status},
            //data: 'status=' + member_status + '&customer_id=' + cust_id,
            method: 'POST',
            dataType: 'json',
            success: function(res) {
              if (res.status == "success") {
                setNotifySuccess("Status changed Successfully");
               // window.location.href = 'global_search.php';

              }
            }
          });
        });
      });
      

    },
    $.SweetAlert = new SweetAlert, $.SweetAlert.Constructor = SweetAlert
  }(window.jQuery),
          //initializing 
                  function($) {
                    "use strict";
                    $.SweetAlert.init()
                  }(window.jQuery);
</script>

<div class="clearfix"></div>
<table class="table table-striped table-small color-table info-table">
  <thead class="">
    <tr>
      <th width="10%"><a href="javascript:;">Join Date</a></th>
      <th width="10%"><a href="javascript:;">User ID</a></th>
      <th width="10%"><a href="javascript:;">Name</a></th>
      <th width="15%"><a href="javascript:;">Email</a></th>
      <th width="10%"><a href="javascript:;">Sponsor</a></th>
      <th width="10%"><a href="javascript:;">Status</a></th>
      <th width="10%">Actions</th>   
    </tr>
  </thead>
  <tbody>
    <?php
    if ($total_rows > 0) {
      foreach ($fetch_rows as $rows) {
        ?>
        <tr>
          <td><?php echo date($DATE_FORMAT, strtotime($rows['created_at'])); ?>
          </td>
          <td>
          
            <a href="agent_detail.php?id=<?php echo $rows['id']; ?>" id="links1" target="_blank"><?php echo $rows['rep_id']; ?></a> 
         

          </td>
          <td><?php echo $rows['fname'] . ' ' . $rows['lname']; ?></td>               
          <td><?php echo $rows['email']; ?></td>
          <td><?php echo $rows['s_fname'] . " " . $rows['s_lname']; ?></td>
          <td><?php echo $rows['status'];?>
                <!-- <select name="member_status" class="form-control member_status" id="member_status_<?= $rows['id']; ?>"   >
                  <option value="Active"      <?php if ($rows['status'] == 'Active') echo "selected='selected'"; ?>>Active</option>
                  <option value="Inactive"   <?php if ($rows['status'] == 'Inactive') echo "selected='selected'"; ?>>Inactive</option>
                </select> -->
              </td> 
          <td class="icons"> 
            <div class="text-left">
                               
              <a href="mailto:<?php echo $rows['email']; ?>" data-toggle="tooltip" title="<?= (isset($rows['email']) ? $rows['email'] : '-'); ?>"><i class="fa fa-envelope"></i></a>    
              <?php if ($rows['cell_phone'] != "") { ?>
                <a href="tel:<?php echo "+1" . $rows['cell_phone'] ?>" data-toggle="tooltip" title="<?= (isset($rows['cell_phone']) ? "+1" . $rows['cell_phone'] : '-'); ?>"><i class="fa fa-phone"></i></a>
                <?php } ?>
            </div>
          </td>
        </tr>
      <?php } ?>
    <tfoot>
      <tr>
        <td colspan="7"><?php echo $paginate->links_html; ?></td>
      </tr>
    </tfoot>
  <?php } else { ?>
    <tr class="text-center">
      <td colspan="7"> No Record(s) </td>
    </tr>
  <?php } ?>
</tbody>
</table> 
