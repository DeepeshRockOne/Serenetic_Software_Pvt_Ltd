<style type="text/css">
@media screen and (max-width: 1515px) and (min-width: 992px){
.tbl_filter .reg_com{margin-bottom: 15px;}
}
</style>
<?php if ($is_ajaxed) { ?>
    <div class="clearfix m-b-20">
  <?php if ($total_rows > 0) {?>
      <div class="pull-left">
          <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
              <div class="form-group mn">
                  <label for="user_type">Records Per Page </label>
              </div>
              <div class="form-group mn">
                  <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);regenerateComm();">
                      <option value="10" <?php echo isset($_GET['pages']) && $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
                      <option value="25" <?php echo (isset($_GET['pages']) && $_GET['pages'] == 25) || (isset($_GET['pages']) && $_GET['pages'] == "") ? 'selected' : ''; ?>>25</option>
                      <option value="50" <?php echo isset($_GET['pages']) && $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
                      <option value="100" <?php echo isset($_GET['pages']) && $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
                  </select>
              </div>
          </div>
      </div>
  <?php } ?>
    </div>

  <div class="table-responsive">
    <table class="<?=$table_class?>">
      <thead>
        <tr>
          <th width="15%">Regeneration Date</th>
          <th class="text-center">Effective Orders </th>
          <th>Admin ID/Name</th>
          <th>Commissions</th>
          <th>Status</th>
          <th width="130px" class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($total_rows > 0) {
          foreach ($fetch_rows as $rows) { ?>
          <tr>
            <td><?=date("m/d/Y",strtotime($rows["regenerationDate"]))?></td>
            <td class="text-center">
              <a href="effective_orders_commission.php?id=<?=md5($rows["id"])?>" class="viewEffectedOrders text-action fw500" data-id="<?=$rows["id"]?>">
                <?=$rows["orderCounts"]?>
              </a>
            </td>
            <td><a href="javascript:void(0)" class="text-action fw500"><?=$rows["adminDispId"]?></a><br><?=$rows["adminName"]?></td>
            <td><?=!empty($rows["commAmt"]) ? dispCommAmt($rows["commAmt"]) : "-"?></td>
            <td><?=$rows["status"]?></td>
            <td class="icons text-right">
              <?php if($rows["status"] == "Pending"){ ?>
              <a href="javascript:void(0)" class="cancelRegenerate" data-id="<?=md5($rows['id'])?>"><i class="fa fa-times-circle"></i></a>
              <?php } ?>
              <a href="effective_orders_commission.php?id=<?=md5($rows["id"])?>" class="viewEffectedOrders"><i class="fa fa-eye"></i></a>
              <?php if($rows["status"] == "Completed"){ ?>
                <a href="javascript:void(0)" class="exportCSV" data-regenerateCommId="<?=md5($rows['id'])?>"><i class="fa fa-download"></i></a>
              <?php } ?>
            </td>
          </tr>
        <?php }
            } else {
        ?>
          <tr>
            <td colspan="6">No record(s) found</td>
          </tr>
        <?php }?>
       
      </tbody>
      <?php if ($total_rows > 0) { ?>
        <tfoot>
        <tr>
                <td colspan="9">
                    <?php echo $paginate->links_html; ?>
                </td>
            </tr>
        </tfoot>
      <?php } ?>
    </table>
  </div>
<?php }else{ ?>
  <?php include_once 'notify.inc.php';?>
  <form id="regenerateFrm" action="regenerate_commissions.php" method="GET">
    <input type="hidden" name="is_ajaxed" id="is_ajaxed">
    <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>"> 
    <div class="panel panel-default panel-block">
      <div class="panel-body">
        <div class="clearfix m-b-15 tbl_filter">
          <div class="pull-left">
            <h4 class="m-t-7">Regenerated Commissions</h4>
          </div>
          <div class="pull-right ">
              <div class="note_search_wrap auto_size reg_com" id="search_div" style="display: none; max-width: 100%;">
                <div class="phone-control-wrap theme-form">
                  <div class="phone-addon">
                    <div class="form-group height_auto mn">
                      <a href="javascript:void(0);" class="search_close_btn text-light-gray">X</a>
                    </div>
                  </div>
                  <div class="phone-addon w-130">
                   <div class="form-group height_auto mn">
                     <input type="text" name="orderId" class="form-control" value="<?=$orderId?>">
                     <label>Order ID</label>
                   </div>
                  </div>
                   <div class="phone-addon w-160">
                     <div class="form-group height_auto mn">
                         <input type="text" name="agentIdName" class="form-control" value="<?=$agentIdName?>">
                         <label>Agent ID/Name</label>
                     </div>
                  </div>
                 <div class="phone-addon w-200">
                   <div class="form-group height_auto mn ">
                     <select class="form-control" id="join_range_payment" name="join_range">
                      <option></option>
                      <option value="Range">Range</option>
                      <option value="Exactly">Exactly</option>
                      <option value="Before">Before</option>
                      <option value="After">After</option>
                     </select>
                     <label>Select</label>
                   </div>
                </div>
                 <div class="phone-addon">
                  <div class="select_date_div_payment" style="display:none">
                    <div class="form-group height_auto mn">
                      <div id="all_join_payment" class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" name="added_date" id="added_date" value="" class="form-control date_picker" />
                      </div>
                      <div  id="range_join_payment" style="display:none;">
                        <div class="phone-control-wrap">
                          <div class="phone-addon">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              <div class="pr">
                                <input type="text" name="fromdate" id="fromdate" value="" class="form-control date_picker" />
                                <label>From</label>
                              </div>
                            </div>
                          </div>
                          <div class="phone-addon">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              <div class="pr">
                                <input type="text" name="todate" id="todate" value="" class="form-control date_picker" />
                                <label>To</label>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                 </div>
                </div>
                 
                  <div class="phone-addon w-80">
                    <div class="form-group height_auto mn">
                      <a href="javascript:void(0);" class="btn btn-info search_button btn-block" onclick="regenerateComm();">Search</a>
                    </div>
                  </div>
                </div>
              </div>
              <a href="javascript:void(0)" class="search_btn"><i class="fa fa-search fa-lg text-blue"></i></a>
              <a href="add_regenerate_commissions.php" class="btn btn-action m-l-5">+ Regenerate Commission</a>
          </div>
        </div>
      <div id="ajax_data"> </div>
      </div>
    </div>
  </form>


<script type="text/javascript">
  $(document).ready(function(){
    dropdown_pagination('ajax_data')
    regenerateComm();

    $(".date_picker").datepicker({
      changeDay: true,
      changeMonth: true,
      changeYear: true
    });

    $(document).off("click", ".search_btn");
    $(document).on("click", ".search_btn", function(e) {
      e.preventDefault();
      $(this).hide();
      $("#search_div").css('display', 'inline-block');
    });

    $(document).off("click", ".search_close_btn");
    $(document).on("click", ".search_close_btn", function(e) {
      e.preventDefault();
      $("#search_div").hide();
      $(".search_btn").show();
      window.location.reload();
    });

    $(document).off('change', '#join_range_payment');
    $(document).on('change', '#join_range_payment', function(e) {
      e.preventDefault();
      $('.date_picker').val('');
      if($(this).val() == ''){
        $('.select_date_div_payment').hide();
      }else{
        $('.select_date_div_payment').show();
        if ($(this).val() == 'Range') {
          $('#range_join_payment').show();
          $('#all_join_payment').hide();
        } else {
          $('#range_join_payment').hide();
          $('#all_join_payment').show();
        }
      }
    });

    $(document).off('click', '#ajax_data tr.data-head a');
    $(document).on('click', '#ajax_data tr.data-head a', function(e) {
      e.preventDefault();
      $('#sort_by_column').val($(this).attr('data-column'));
      $('#sort_by_direction').val($(this).attr('data-direction'));
      regenerateComm();
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
          $('[data-toggle="tooltip"]').tooltip();
          $(".viewEffectedOrders").colorbox({ iframe:true, height:"575px", width:"70%", });
          common_select();
          fRefresh();
        }
      });
    });

    $(document).off("click",".cancelRegenerate");
    $(document).on("click",".cancelRegenerate",function(){
        $id= $(this).attr('data-id');
        swal({
            text: 'Cancel: Are you sure?',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel',
        }).then(function () {
            $("#ajax_loader").show();
            $.ajax({
                url:'ajax_cancel_regenerate_commissions.php',
                dataType:'JSON',
                type:'POST',
                data:{id:$id},
                success:function(res){
                   $("#ajax_loader").hide();
                  if(res.status=='success'){
                    setNotifySuccess("Cancelled Commission Regeneration Successfully");
                    setTimeout(function(){
                       window.location.reload();
                    }, 1000);
                  }
                }
            });
        }, function (dismiss) {
        });
    });


    $(document).off("click",".exportCSV");
    $(document).on('click', '.exportCSV', function(e) {
      var regenerateCommId = $(this).attr("data-regenerateCommId");
      
      parent.confirm_export_data(function() {
        $('#ajax_loader').show();
        $.ajax({
            url: "regenerate_commissions_export_csv.php",
            type: 'GET',
            data: {"regenerateCommId":regenerateCommId},
            dataType: 'json',
            success: function(res) {
                $('#ajax_loader').hide();
                if(res.status == "success") {
                    parent.confirm_view_export_request();
                } else {
                    parent.setNotifyError(res.message);
                }
            }
        });
      });
    });
  });
  
  function regenerateComm() {
    $('#ajax_data').hide();
    $('#is_ajaxed').val('1');
    var params = $('#regenerateFrm').serialize();
    $.ajax({
      url: $('#regenerateFrm').attr('action'),
      type: 'GET',
      data: params,
       beforeSend:function(){
        $("#ajax_loader").show();
      },
      success: function(res) {
        $('#ajax_loader').hide();
        $('#ajax_data').html(res).show();
        $(".viewEffectedOrders").colorbox({ iframe:true, height:"575px", width:"70%", });
        common_select();
        fRefresh();
        setTimeout(function(){
          regenerateComm();
        },30000);
      }
    });
    return false;
  }
</script>

<?php } ?>