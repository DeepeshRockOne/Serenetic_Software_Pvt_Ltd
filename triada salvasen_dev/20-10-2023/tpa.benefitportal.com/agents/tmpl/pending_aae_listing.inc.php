<?php if($is_ajaxed){ ?>
  <script type="text/javascript">
    $('#pages').on('change',function(){
      $('#per_pages').val($(this).val());
      ajax_submit();        
    });
    $('#lead_product, #lead_validation, #lead_status').popover({
      container: 'body',              
      html: true,
      template: '<div class="popover"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>',
        content: function () {
          var clone = $($(this).data('popover-content')).clone(true).removeClass('hide');
          return clone;
          }
    }).click(function(e) {
      e.preventDefault();
    });
    $(".aae_resend_enrollment_edit_popup").colorbox({iframe: true, width: '515px', height: '225px'});
    $(".edit_post_date").colorbox({iframe: true, width: '515px', height: '375px'});

  </script>
  <div class="panel panel-default panel-block">
    <div class="panel-body">
      <div class="clearfix tbl_filter">
        <div class="pull-left">
          <h4 class="m-t-0 m-b-15">Member Application Summary</h4>
        </div>
      </div>
      <div class="table-responsive">
        <table class="<?=$table_class?> text-center table-danger">
          <thead>
            <tr>
              <th>AAE #</th>
              <th>Converted #</th>
              <th>Post Payment #</th>
              <th>Pending Validation #</th>
              <th>Abandoned #</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><?=$counts['total_lead']?></td>
              <td><?=$counts['converted']?></td>
              <td><?=$counts['post_payment']?></td>
              <td><?=$counts['pending_validation']?></td>
              <td><?=$counts['abandoned']?></td>
            </tr>
          </tbody>
        </table>
      </div>
  </div>
  <div class="panel panel-default panel-block">
    <div class="panel-body">
      <div class="clearfix m-b-15 tbl_filter">
        <div class="pull-left">
          <h4 class="m-t-7">AAE Pending Applications</h4>
        </div>
        <div class="pull-right">
          <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
            <div class="form-group mn">
              <label for="">Records Per Page </label>
            </div>
            <div class="form-group mn">
              <select size="1" id="pages" class="form-control">
                  <option value="10" <?=$per_page == 10 ? "selected='selected'" : ""?>>10</option>
                  <option value="25" <?=$per_page == 25 ? "selected='selected'" : ""?>>25</option>
                  <option value="50" <?=$per_page == 50 ? "selected='selected'" : ""?>>50</option>
                  <option value="100" <?=$per_page == 100 ? "selected='selected'" : ""?>>100</option>
               </select>
            </div>
          </div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="<?=$table_class?>">
          <thead>
            <tr>
              <th >Lead ID/Added Date</th>
              <th >Lead Name</th>
              <th>Enrolling Agent/ID</th>
              <th class="text-center">Products</th>
              <th >Status</th>
              <th >Amount</th>
              <th class="text-center">Delivery</th>
              <th width="90px" class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($total_rows > 0) { ?>
            <?php foreach ($fetch_rows as $rows) {
                $status = "";
                $is_post_date = false; 
                if(checkIsset($rows['mbrStatus']) == "Post Payment" && $rows['status'] == 'Working'){
                  $is_post_date = true;
                }

                if($is_post_date == true){
                  $status = "Post Payment";
                }else if($rows['status'] == 'Working'){
                  $status = "Pending Validation";
                }else{
                  $status = $rows["status"];
                }
              ?>
              <tr>
                <td><a href="lead_details.php?id=<?=md5($rows['id'])?>" class="fw500 text-action" target="_blank"><?=$rows['lead_id']?></a><br><?=displayDate($rows['created_at'])?></td>
                <td><?=$rows['fname'] ." ".$rows['lname'] ?></td>
                <td>
                    <?=$rows['sponsorName']?><br />
                    <a href="javascript:void(0);" class="text-red"><strong><?=$rows['spnsorRepId']?></strong></a>
                </td>
                <td class="text-center">
                  <a href="javascript:void(0);" class="text-action fw500"  id="lead_product"  data-placement="top"  data-popover-content="#lead_product_popover_<?=$rows['id']?>" title="Products"  data-trigger="hover"><?=$rows['total_products']?></a>
                  <?php
                  $products = explode("separator",$rows['products_name']);
                  ?>
                  <div id="lead_product_popover_<?=$rows['id']?>" class="hide">
                    <table class="popover_table">
                      <tbody>
                        <?php if($products){ 
                          foreach ($products as $value) { ?>
                            <tr>
                              <td><?=$value?></td>
                            </tr>
                          <?php }
                           }
                          ?>
                      </tbody>
                    </table>
                  </div>
                </td>
                <td><a href="javascript:void(0);" class="text-black" id="lead_validation" data-placement="top"  data-popover-content="#lead_validation_popover_<?=$rows['id']?>" title="Status Explanation"  data-trigger="hover">
                  <?=$status?>
                  </a>
                  <div id="lead_validation_popover_<?=$rows['id']?>" class="hide">
                    <table class="popover_table">
                      <tbody>
                        <tr>
                          <td class="w-160 fw500 <?=$status == 'Pending Validation' ? 'text-action' : ''?>" valign="top" >Pending Validation</td>
                          <td class="<?=$status == 'Pending Validation' ? 'text-action' : ''?>">Pending validation is used when a lead is sent email/sms validation.</td>
                        </tr>
                        <tr>
                          <td valign="top" class="fw500 <?=$status == 'Abandoned' ? 'text-action' : ''?>">Abandoned</td>
                          <td class="<?=$status == 'Abandoned' ? 'text-action' : ''?>">Abandoned is used when lead has received AAE within past 48 hours without completion.</td>
                        </tr>
                        <tr>
                          <td valign="top" class="fw500 <?=$status == 'Post Payment' ? 'text-action' : ''?>">Post Payment</td>
                          <td class="<?=$status == 'Post Payment' ? 'text-action' : ''?>">Post payment is used when a lead has validated AAE and set payment for future date.</td>
                        </tr>
                        <tr>
                          <td valign="top" class="fw500 <?=$status == 'Converted' ? 'text-action' : ''?>">Converted</td>
                          <td class="<?=$status == 'Converted' ? 'text-action' : ''?>">Converted is used when lead has validated AAE and a successful payment has posted.</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </td>
                <td><?=displayAmount($rows['grand_total'])?></td>
                <td  class="text-center fs18">
                  <?php 

                    //Display Last sent communication details
                    if(isset($phone_trigger_log['+1'.$rows['cell_phone']]) && isset($email_trigger_log[$rows['email']])) {

                      if($phone_trigger_log['+1'.$rows['cell_phone']]['created_at'] > $email_trigger_log[$rows['email']]['created_at']) {
                        $email_res = $phone_trigger_log['+1'.$rows['cell_phone']];
                      } else {
                        $email_res = $email_trigger_log[$rows['email']];
                      }
                      
                    } else if(isset($phone_trigger_log['+1'.$rows['cell_phone']])) {
                      $email_res = $phone_trigger_log['+1'.$rows['cell_phone']];

                    } else if(isset($email_trigger_log[$rows['email']])) {
                      $email_res = $email_trigger_log[$rows['email']];

                    } else {
                      $email_res = array(); 
                    }

                    //$email_res = array();
                    if($email_res){
                      if(in_array($email_res['status'],array('click','delivered','open','processed'))){ ?>
                        <a href="javascript:void(0);" id="lead_status" title="Delivery - <strong style='font-weight:300;'><?=ucfirst($email_res['status'])?></strong>" data-placement="top"  data-popover-content="#lead_status_popover_<?=$rows['id']?>"   data-trigger="hover"><i class="fa fa-check-circle  text-success" aria-hidden="true"></i></a>
                        <div id="lead_status_popover_<?=$rows['id']?>" class="hide">
                          <table class="popover_table">
                            <tbody>
                              <tr>
                                <td><?=$email_res['type']?> was <?=$email_res['status']?> at <?= $tz->getDate($email_res['created_at'], 'h:i A')?> on <br><?= $tz->getDate($email_res['created_at'], 'm/d/Y')?></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      <?php }else{ ?>
                          <?php $reason = json_decode($email_res['response'],true); ?>
                          <a href="javascript:void(0);" id="lead_status" title="Delivery - <strong style='font-weight:300;'><?=ucfirst($email_res['status'])?></strong>" data-placement="top"  data-popover-content="#lead_status_popover_<?=$rows['id']?>"   data-trigger="hover"><i class="fa fa-info-circle  text-action" aria-hidden="true"></i></a>
                        <div id="lead_status_popover_<?=$rows['id']?>" class="hide">
                          <table class="popover_table">
                            <tbody>
                              <tr>
                                <td>Reason : <?=!empty($reason['reason']) ? $reason['reason'] : "-"?></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      <?php }
                    }
                  ?>
                </td>
                <td  class="icons text-center">
                  <?php if($rows['status'] == 'Abandoned'){ ?>  
                    <a href="member_enrollment.php?lead_id=<?=md5($rows['id'])?>" data-toggle="tooltip" title="Edit" target="_blank"><i class="fa fa-edit" aria-hidden="true"></i></a>
                  <?php }else{ ?>
                    <a href="lead_details.php?id=<?=md5($rows['id'])?>" data-toggle="tooltip" title="Edit" target="_blank"><i class="fa fa-edit" aria-hidden="true"></i></a>
                  <?php } ?>
                </td>
            </tr>
            <?php } ?>
          <?php }else {?>
            <tr>
                <td colspan="8">No record(s) found</td>
            </tr>
        <?php }?>
        </tbody>
        <tfoot>
        <tr>
        <?php if ($total_rows > 0) {?>
          <td colspan="8">
            <?php echo $paginate->links_html; ?>
          </td>
        </tr>
        </tfoot>
    <?php }?>
      </table>
    </div>
  </div>
</div>
<?php }else{ ?>
  <div class="container  m-t-30">
  <div class="panel panel-default panel-block panel-title-block">
    <div class="panel-left">
      <div class="panel-left-nav">
        <ul>
          <li class="active"><a href="javascript:void(0);"><i class="fa fa-search"></i></a></li>
        </ul>
      </div>
    </div>
    <div class="panel-right">
      <div class="panel-heading">
        <div class="panel-search-title">
          <span class="clr-light-blk">SEARCH</span>
        </div>
      </div>
      <div class="panel-wrapper collapse in">
        <div class="panel-body theme-form">
          <form name="frm_search" id="frm_search" method="GET" action="">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group ">
                  <input type="text" name="lead_ids" class="form-control">
                  <label>ID Number(s)</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="row">
                  <div id="date_range" class="col-md-12">
                    <div class="form-group">
                      <select class="form-control" id="join_range" name="join_range">
                        <option value=""></option>
                        <option value="Range" <?=isset($_GET['view_all'])?'':'selected=""'?>>Range</option>
                        <option value="Exactly">Exactly</option>
                        <option value="Before">Before</option>
                        <option value="After">After</option>
                      </select>
                      <label>Added Date</label>
                    </div>
                  </div>
                  <div class="select_date_div col-md-9" style="display:none">
                    <div class="form-group">
                      <div id="all_join" class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" name="lead_added_date" id="added_date" value="" class="form-control date_picker" />
                      </div>
                      <div  id="range_join" style="display:none;">
                        <div class="phone-control-wrap">
                          <div class="phone-addon">
                            <label class="mn">From</label>
                          </div>
                          <div class="phone-addon">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              <input type="text" name="lead_fromdate" id="fromdate" value="<?=date('m/d/Y',strtotime('-7 days'))?>" class="form-control date_picker" />
                            </div>
                          </div>
                          <div class="phone-addon">
                            <label class="mn">To</label>
                          </div>
                          <div class="phone-addon">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              <input type="text" name="lead_todate" id="todate" value="<?=$today?>" class="form-control date_picker" />
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="clearfix"></div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" name="lead_name" class="form-control">
                  <label>Name</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="row">
                  <div id="post_date_range" class="col-md-12">
                    <div class="form-group">
                      <select class="form-control" id="post_join_range" name="post_payment_range">
                        <option value=""></option>
                        <option value="Range">Range</option>
                        <option value="Exactly">Exactly</option>
                        <option value="Before">Before</option>
                        <option value="After">After</option>
                      </select>
                      <label>Post Payment Date</label>
                    </div>
                  </div>
                  <div class="post_select_date_div col-md-9" style="display:none">
                    <div class="form-group">
                      <div id="post_all_join" class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" name="post_payment_added_date" id="post_added_date" value="" class="form-control date_picker" />
                      </div>
                      <div  id="post_range_join" style="display:none;">
                        <div class="phone-control-wrap">
                          <div class="phone-addon">
                            <label class="mn">From</label>
                          </div>
                          <div class="phone-addon">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              <input type="text" name="post_payment_fromdate" id="post_fromdate" value="" class="form-control date_picker" />
                            </div>
                          </div>
                          <div class="phone-addon">
                            <label class="mn">To</label>
                          </div>
                          <div class="phone-addon">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              <input type="text" name="post_payment_todate" id="post_todate" value="" class="form-control date_picker" />
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group ">
                  <select class="se_multiple_select" name="status[]" id="status" multiple="multiple">
                    <option value="Abandoned">Abandoned</option>
                    <option value="Converted">Converted</option>
                    <option value="Working">Pending Validation</option>
                    <option value="Post Payment">Post Payment</option>
                  </select>
                  <label>Status</label>
                </div>
              </div>
            </div>
            <div class="panel-footer clearfix">
              <button type="button" class="btn btn-info" name="search" id="search" > <i class="fa fa-search"></i> Search </button>
              <button type="button" onclick="window.location='pending_aae_listing.php?view_all'" class="btn btn-info btn-outline" name="" id=""> <i class="fa fa-search-plus"></i> View All </button>
              <button type="button" name="" id="exportBtn" class="btn red-link"> <i class="fa fa-download"></i> Export </button>
              <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
              <input type="hidden" class="listing_search" name="export" id="export" value="" />
              <input type="hidden" name="pages" id="per_pages" value="<?= $per_page; ?>" />
              <input type="hidden" name="sort_by" id="sort_by_column" value="<?= $SortBy; ?>" />
              <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?= $SortDirection; ?>"/>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="search-handle">
      <a href="#" data-perform="panel-collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></a>
    </div>
  </div>
  <div id="ajax_loader" class="ajex_loader" style="display: none;">
    <div class="loader"></div>
    </div>
    <div id="ajax_data"> </div>
 </div>
<!-- </div> -->

<script type="text/javascript">
$(document).off('change', '#join_range');
$(document).on('change', '#join_range', function(e) {
  e.preventDefault();
      if ($(this).val() == '') {
          $('.select_date_div').hide();
          $('#date_range').removeClass('col-md-3').addClass('col-md-12');
      } else {
          $('#date_range').removeClass('col-md-12').addClass('col-md-3');
          $('.select_date_div').show();
          if ($(this).val() == 'Range') {
              $('#range_join').show();
              $('#all_join').hide();
          } else {
              $('#range_join').hide();
              $('#all_join').show();
          }
      }
});


$(document).off('change', '#post_join_range');
$(document).on('change', '#post_join_range', function(e) {
  e.preventDefault();
      if ($(this).val() == '') {
          $('.post_select_date_div').hide();
          $('#post_date_range').removeClass('col-md-3').addClass('col-md-12');
      } else {
          $('#post_date_range').removeClass('col-md-12').addClass('col-md-3');
          $('.post_select_date_div').show();
          if ($(this).val() == 'Range') {
              $('#post_range_join').show();
              $('#post_all_join').hide();
          } else {
              $('#post_range_join').hide();
              $('#post_all_join').show();
          }
  }
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
             $('[data-toggle="tooltip"]').tooltip();
            $('#ajax_data').html(res).show();
  common_select();
        }
    });
});
$(document).ready(function(){
  dropdown_pagination('ajax_data')
    if($('#join_range').val() != "") {
        $('#join_range').trigger('change');
    }

    ajax_submit();
    $(".date_picker").datepicker({
      changeDay: true,
      changeMonth: true,
      changeYear: true
    });

    $("#status").multipleSelect({
      selectAll: false,
      filter:true
    });
    $(document).off('click', '#exportBtn');
    $(document).on('click', '#exportBtn', function(e) {
      confirm_export_data(function() {
          $("#export").val('export_pending_aae');
          $('#ajax_loader').show();
          $('#is_ajaxed').val('1');
          var params = $('#frm_search').serialize();
          $.ajax({
              url: $('#frm_search').attr('action'),
              type: 'GET',
              data: params,
              dataType: 'json',
              success: function(res) {
                  $('#ajax_loader').hide();
                  $("#export").val('');
                  if(res.status == "success") {
                     $("#export").val('');
                      confirm_view_export_request(true,'agent');
                  } else {
                      setNotifyError(res.message);
                  }
              }
          });
      });
    });

    $(document).on('click', '#search', function (e) {
      // e.preventDefault();
      ajax_submit();
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
       $('[data-toggle="tooltip"]').tooltip();
       common_select();
     }
   });
   return false;
 }    
</script>
<?php } ?>