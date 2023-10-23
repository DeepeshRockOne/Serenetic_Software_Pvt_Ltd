<style type="text/css">
  .text-declined {
    color: #e53535 !important;
  }  
</style>
<?php if ($is_ajaxed) { ?>
<div class="table-responsive white-box">
  <table class="<?= $table_class ?>">
    <thead>
      <tr class="data-head">   
        <th width="200">Requested At</th>
        <th width="200">Requested By</th>
        <th>Title</th>
        <th width="150">Actions</th>
      </tr>
    </thead>          
    <tbody>
      <?php
      if ($total_rows > 0) {
          foreach ($fetch_rows as $rows) {
              $request_status_class = "";
              if($rows['is_proceed'] == "Y" && $rows['is_declined'] == "Y") {
                $request_status_class = "text-declined";
              } else if($rows['is_process_active'] == "Y") {
                $request_status_class = "text-warning";
              } else if ($rows['is_proceed'] == "N") {
                $request_status_class = "text-danger";
              }
          ?>
            <tr class="<?= $request_status_class?>">
              <td><?= date("m/d/Y H:i:s",strtotime($rows['created_at'])) ?></td>
              <td><?= $rows['requester_rep_id'] ?><br/><?= $rows['requester_name'] ?></td>
              <td><?= $rows['title'] ?></td>
              <td>
                  <?php if($rows['is_proceed'] == "Y" && $rows['is_declined'] == 'N') { ?>
                    <?php
                      $file_name=$rows['filename'];
                    ?>
                      <a href="export_requests.php?is_download=Y&file_name=<?=urlencode($file_name);?>" data-toggle="tooltip" data-title="Click to Download"><i class="fa fa-download"></i></a>
                  
                  <?php } else { ?>
                    <?php if($rows['is_proceed'] == "N" && $rows['is_cancelled'] == "N"){ ?>
                        <a href="javascript:void(0);" class="cancel_request" data-id="<?=$rows['id']?>" data-toggle="tooltip" data-title="Click to Cancel Export Request"><i class="fa fa-close"></i></a>
                    <?php }else{ ?>
                        -
                <?php } } ?>
              </td>
            </tr>
        <?php 
          }
        ?>
      <?php } else { ?>
        <tr>
          <td colspan="8" class="text-center">No record(s) found</td>
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
<form id="ex_rpt_frm_search" action="export_requests.php" method="GET" class="sform">
    <input type="hidden" name="is_ajaxed" id="ex_rpt_is_ajaxed" value="1"/>
    <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>"/>
</form>
<label><i class="fa fa-square fa-lg text-danger"></i> Pending </label>
&nbsp; &nbsp; <label><i class="fa fa-square fa-lg text-warning"></i> Exporting </label>
&nbsp; &nbsp; <label><i class="fa fa-square fa-lg text-declined"></i> Declined</label>
<div id="ex_rpt_ajax_data"></div>
<script type="text/javascript">
  $(document).ready(function () {
    dropdown_pagination('ex_rpt_ajax_data')

      ex_rpt_ajax_submit();
      
      setInterval(function () {
        ex_rpt_ajax_submit();
      },30000);

      $(document).off('click', '#ex_rpt_ajax_data ul.pagination li a');
      $(document).on('click', '#ex_rpt_ajax_data ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#ex_rpt_ajax_data').hide();
        $.ajax({
          url: $(this).attr('href'),
          type: 'GET',
          success: function (res) {
            $('#ajax_loader').hide();
            $('#ex_rpt_ajax_data').html(res).show();
            common_select();
          }
        });
      });

        $(document).on("click",".cancel_request",function(){
        $request_id=$(this).attr('data-id');
        swal({
              text: "Cancel Export: Are you sure?",
              showCancelButton: true,
              confirmButtonText: "Confirm",
        }).then(function() {
            $.ajax({
                url: 'ajax_cancel_export_requests.php',
                data: {request_id: $request_id},
                method: 'POST',
                dataType: 'JSON',
                success: function (res) {
                    if (res.status == "success") {
                        setNotifySuccess(res.msg); 
                    }else if(res.status == "fail"){
                      setNotifyError(res.msg);
                    }
                    ex_rpt_ajax_submit();
                }
            });
        }, function (dismiss) {
            //window.location.reload();
        })
    });

  });
  function ex_rpt_ajax_submit() {
    $('#ajax_loader').show();
    $('#ex_rpt_ajax_data').hide();
    $('#ex_rpt_is_ajaxed').val('1');
    var params = $('#ex_rpt_frm_search').serialize();
    $.ajax({
        url: $('#ex_rpt_frm_search').attr('action'),
        type: 'GET',
        data: params,
        success: function (res) {
            $('#ajax_loader').hide();
            $('#ex_rpt_ajax_data').html(res).show();
            common_select();
        }
    });
    return false;
  }
</script>
<?php } ?>