  <?php if ($total_rows > 0) { ?>
      <div class="clearfix m-b-15">
        <div class="pull-right">
          <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
            <div class="form-group mn height_auto">
              <label for="user_type">Records Per Page </label>
            </div>
            <div class="form-group mn height_auto">
              <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);loadPaidDiv();">
                <option value="10" <?php echo isset($_GET['pages']) && $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
                <option value="25" <?php echo (isset($_GET['pages']) && $_GET['pages'] == 25) || (isset($_GET['pages']) && $_GET['pages'] == "") ? 'selected' : ''; ?>>25</option>
                <option value="50" <?php echo isset($_GET['pages']) && $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
                <option value="100" <?php echo isset($_GET['pages']) && $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
              </select>
            </div>
          </div>
        </div>
      </div>
 <?php } ?>
    <div class="table-responsive">
        <table class="<?=$table_class?>">
            <thead> 
                <tr>
                  <th>Paid Date</th>
                  <th>Admin ID/Name</th>
                  <th>File Name</th>
                  <th>File Type</th>
                  <th>Reversal Date</th>
                  <th width="90px">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total_rows > 0) {
                foreach ($fetch_rows as $rows) { 
                ?>
                <tr>
                  <td><?=date("m/d/Y",strtotime($rows["paidDate"]))?></td>
                  <td><a href="javascript:void(0);" class="fw500 text-action"><?=$rows["adminDispId"]?></a><br><?=$rows["adminName"]?></td>
                  <td><?=$rows['ach_file']?></td>
                  <td><?=$rows['file_type']?></td>
                  <td><?=!empty($rows['reversal_date']) ? date("m/d/Y",strtotime($rows['reversal_date'])) : "-"?></td>
                   <td class="icons">
                      <a href="javascript:void(0);" class="downloadPaidFile" data-achId="<?=$rows['id']?>" data-toggle="tooltip" data-trigger="hover" title="Download" data-placement="top"><i class="fa fa-download" aria-hidden="true"></i></a>
                    <?php if(empty($rows['reversal_date'])){ ?>
                      <a href="javascript:void(0);" class="reinstateFile" data-toggle="tooltip" data-trigger="hover" title="Reverse" data-placement="top" data-ach_id="<?=$rows['id']?>"><i class="fa fa-angle-double-left" aria-hidden="true"></i></a>
                    <?php } ?>
                    </td>
                </tr>
                    <?php }?>
                <?php } else {?>
                    <tr>
                        <td colspan="6" class="text-center">No record(s) found</td>
                    </tr>
                <?php } ?>
            </tbody>
            <?php 
            if ($total_rows > 0) {?>
                <tfoot>
                    <tr>
                        <td colspan="6">
                            <?php echo $paginate->links_html; ?>
                        </td>
                    </tr>
                </tfoot>
            <?php }?>
        </table>
    </div>

<script type="text/javascript">
  $(document).ready(function(){
    $(document).off('click', '#paidDiv ul.pagination li a');
    $(document).on('click', '#paidDiv ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#paidDiv').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#paidDiv').html(res).show();
                common_select();
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    });

    $(".reinstateFile").click(function(){
      $achId = $(this).data("ach_id");
      swal({
         text: '<br>Reverse Commissions: Are you sure?',
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
      }).then(function () {
        $.ajax({
          url: 'commissions_export_pay.php',
          type: 'POST',
          dataType: "json",
          data: {action:'reverseCommFile',achId:$achId},
          beforeSend: function () {
            $("#ajax_loader").show();
          },
          success: function(res) {
            $(".error").html("");
            $("#ajax_loader").hide();
            if (res.status == "success") {
              parent.swal({
                text: '<br>Reverse Commissions: Successful',
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonText: 'Close',
              }).then(function(){
                window.parent.location.reload();
              }, function (dismiss){  
                window.parent.location.reload();
              });
            }else if(res.status == "fail"){
              parent.swal({
                text: '<br>Reverse Commissions: Failed',
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonText: 'Close',
              }).then(function(){
                window.parent.location.reload();
              }, function (dismiss){  
                window.parent.location.reload();
              });
            }
          }
        });
      }, function (dismiss){  
      });
    });

    $(document).off("click",".downloadPaidFile");
    $(document).on('click', '.downloadPaidFile', function(e) {
      e.preventDefault();
      $achId = $(this).attr("data-achId");
      window.location="download_paid_commissions_files.php?achId="+$achId;
    });


  });
    
</script>