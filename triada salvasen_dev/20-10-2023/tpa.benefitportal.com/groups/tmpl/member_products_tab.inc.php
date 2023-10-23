<?php if(!empty($is_ajax_prd)){
  $displayNextBillingText = $sponsor_billing_method != 'list_bill' ? 'Next Billing Date' : 'Next Billing List Bill';
  ?>
   <p class="agp_md_title">Products</p>
   <div class="table-responsive">
         <table class="<?=$table_class?>" id="main_products_tbl">
            <thead>
               <th>Added Date/<br>Plan ID</th>
               <th>Product Name<br>Enrolling Agent</th>
               <th>Plan</th>
               <th>Effective Date</th>
               <th>Termination<br>Date</th>
               <th><?=$displayNextBillingText?></th>
               <th>Fulfillment<br>Date</th>
               <th>Total Premium</th>
               <th width="150px">Actions</th>
            </thead>
            <tbody>
               <?php if($fetch_rows){ ?>
                  <?php foreach ($fetch_rows as $k => $row) { ?>
               <tr>
                  <?php 
                        $data_color = 'success'; 
                        if (in_array($row['status'], array('Inactive')) || (!empty($row['termination_date']) && strtotime($row['termination_date']) > 0 && strtotime($row['termination_date']) <= strtotime(date('Y-m-d')))) {
                            $data_color = "danger";

                        } else if (!empty($row['eligibility_date']) && strtotime($row['eligibility_date']) > strtotime(date('Y-m-d'))) {
                            $data_color = "warning";
                        
                        } else if (!empty($row['termination_date']) && strtotime($row['termination_date']) > 0) {
                            $data_color = "success-warning";
                        
                        } else if ($row['process_status'] == 'Pending' && !empty($row['tier_change_date']) && strtotime($row['tier_change_date']) > strtotime(date('Y-m-d'))) {
                            $data_color = "success-warning";
                        }
                  ?>
                  <td>
                      <?=displayDate($row['added_date']);?><br>
                      <a href="javascript:void(0);" class="fw500 text-action"><?=$row['website_id']?></a>
                 </td>
                 <td>
                      <a class="label label-rounded label-<?=$data_color?> product_name" data-toggle="" data-target="#prd_details_expanded" data-ws_id="<?=md5($row['ws_id'])?>" data-color="<?=$data_color?>"><?=$row['name']?></a>
                        <p class="mn fw500 text-action"><?=$row['agent_id']?> - <?=$row['agent_name']?></p>
                 </td>
                 <td><?=$row['benefit_tier']?></td>
                 <td class="effective_td_<?=$row['p_id']?>"><?=date('m/d/Y',strtotime($row['eligibility_date']))?></td>
                 <td><?=displayDate($row['termination_date']);?></td>
                 <td>
                      <?php
                      echo displayPolicyNextBillingDate($row['ws_id'],$row['next_purchase_date'],$row['termination_date'],$row['end_coverage_period'],$row['sponsor_id'],array('sponsor_billing_method' => $sponsor_billing_method)); 
                      ?>
                 </td>
                 <td><?=displayDate($row['fulfillment_date']);?></td>
                 <td>$<?=$row['price']?></td>
                 <td class="icons">
                    <?php if(check_is_card_exist($row['p_id'])){ ?>
                      <a href="<?=$HOST?>/id_card_popup.php?ws_id=<?=md5($row['ws_id'])?>&user_type=Group&user_id=<?=$_SESSION['groups']['id']?>" class="id_card_popup" data-toggle="tooltip" data-placement="top" title="ID Card"><i class="fa fa-address-card-o" aria-hidden="true"></i></a>
                    <?php } ?>
                    <a href="member_product_detail.php?customer_id=<?=$row['customer_id']?>&product_id=<?=$row['p_id']?>&plan_id=<?=$row['matrix_id']?>" class="member_product_detail" data-toggle="tooltip" data-placement="top" title="Product Details"><i class="fa fa-info-circle" aria-hidden="true"></i></i></a>

                    <a href="javascript:void(0);" class="product_name" data-toggle="" data-target="#prd_details_expanded" data-ws_id="<?=md5($row['ws_id'])?>" data-color="<?=$data_color?>"><i class="fa fa-edit" aria-hidden="true"></i></a>

                    <a href="../policy_document.php?userType=Group&customer_id=<?=md5($row['customer_id'])?>&ws_id=<?=md5($row['ws_id'])?>" data-toggle="tooltip" data-placement="top" title="Policy Doc"><i class="fa fa-download" aria-hidden="true"></i></a>

                 </td>
                  </tr>
                  <?php } ?>
               <?php } ?>
            </tbody>
            <tfoot>
            <tr>
               <td colspan="10">
                  <div class="row table-footer-row">
                     <div class="col-sm-12">
                        <div class="pull-left">
                           
                        </div>
                        <div class="pull-right">
                          <?php if($member_status != 'Hold'){ ?>
                           <a href="<?=$HOST?>/group_enroll/<?=$_SESSION['groups']['user_name']?>/<?=$member_rep_id?>" class="btn btn-action">+ Product</a>
                          <?php } ?> 
                           <a href="<?=$HOST?>/term_multiple_products.php?location=group&member_id=<?=$member_id?>" class="btn btn-info term_multiple_products">Multi-Term</a>
                        </div>
                     </div>
                  </div>
               </td>
            </tr>
            </tfoot>
         </table>
   </div>
<?php }else{ ?>
      <?php include_once 'notify.inc.php';?>
    <form id="frm_search_prd" action="member_products_tab.php" method="GET" class="sform">
        <input type="hidden" name="is_ajax_prd" id="is_ajax_prd" value="1" />
        <input type="hidden" name="id" id="id" value="<?=$memberId?>">  
        <input type="hidden" name="member_rep_id" id="member_rep_id" value="<?=$member_rep_id?>">  
    </form>
    <div id="ajax_loader" class="ajex_loader" style="display: none;">
      <div class="loader"></div>
    </div>
    <div id="ajax_prd_data" class=""> </div>



      <div id="prd_details_expanded">
      </div>
      <div class="m-t-30"></div>

<script type="text/javascript">
$(document).ready(function() {
    ajax_load_products();
    
    $('.term_multiple_products').colorbox({iframe: true, width: '450px', height: '600px'});
    $(".member_product_detail").colorbox({iframe: true, width: '980px', height: '980px'});
    $(".id_card_popup").colorbox({iframe: true, width: '1024px', height: '500px'});
    $(".add_term_date").colorbox({iframe: true, width: '435px', height: '430px'});
    $(".reinstate_products").colorbox({iframe: true, width: '768px', height: '530px'});
    $(".assign_depedents").colorbox({iframe: true, width: '450px', height: '300px'});
    $('.edit_benifit_amount').colorbox({iframe: true, width: '470px', height: '750px'});
    $('.prd_detail_edit_depedents').colorbox({iframe: true, width: '768px', height: '285px'});
    $('.billing_date_popup').colorbox({iframe: true, width: '470px', height: '385px'});   
    $('.coverage_popup').colorbox({iframe: true, width: '900px', height: '700px'});   

    $(document).off('change','.benefit_tier_change_drpdwn');
    $(document).on('change','.benefit_tier_change_drpdwn',function(e) {
        if($(this).val() == $(this).attr('data-org_prd_plan')) {
            return false;
        }
        var this_obj = $(this);
        var customer_id = $(this).attr('data-customer_id');
        var product_id = $(this).attr('data-product_id');
        var web_id = $(this).attr('data-web_id');
        var prd_plan = $(this).val();
        var org_prd_plan = $(this).attr('data-org_prd_plan');

        if (prd_plan == 4 || prd_plan == 2 || prd_plan == 3 || prd_plan == 5) {
          $.ajax({
            url: "<?=$HOST?>/ajax_check_dependent.php?product_id=" + product_id + "&customer_id=" + customer_id +"&web_id="+web_id + "&prd_plan="+prd_plan,
            type: 'GET',
            success: function(res) {
               $('#ajax_loader').hide();
               if (res.status == 'not_exist') {
                  swal({
                      text: "To make this change, you must first have qualified dependent(s) added.Click below to add dependent(s).",
                      showCancelButton: true,
                      confirmButtonText: '+ Dependent(s)'
                  }).then(function () {
                      this_obj.val(org_prd_plan).change();
                      $is_change_plan = 0;
                      scrollToDiv($('#dependents_tab'), 0,'member_depedents_tab.php','dependents_tab');
                  }, function (dismiss) {
                      $is_change_plan = 0;
                      this_obj.val(org_prd_plan).change();
                  });
              } else if (res.status == 'exist' || res.status == "exist_in_master") {
                  $is_change_plan = 0;
                  existing_dependant_popup(customer_id,product_id,web_id,prd_plan);
              }
            }
          });
        }else{
          if (prd_plan == 1) {
            $is_change_plan = 0;
            existing_dependant_popup(customer_id,product_id,web_id,prd_plan);
          }
        }
    });

    $(document).off('change','.product_change_action');
    $(document).on('change','.product_change_action', function(e) {
        e.stopImmediatePropagation();
        var new_prd_id = $(this).val();
        var old_prd_id = $(this).data('old_prd_id');
        var ws_id = $(this).data('ws_id');
        if(new_prd_id != old_prd_id){
            $.colorbox({
                href: '<?=$HOST?>/benefit_tier_change.php?location=group&action=policy_change&new_prd_id=' + new_prd_id + '&ws_id=' + ws_id,
                iframe: true,
                height: '500px',
                width: '650px',
                open: true,
                escKey: false,
                overlayClose: false,
                onClosed: function () {
                    location.reload(true);
                }
            });
        }
    });

    $(document).off('click','.btn_product_change');
    $(document).on('click','.btn_product_change', function(e) {
         var ws_id = $(this).data('ws_id');
        $.colorbox({
            href: '<?=$HOST?>/benefit_tier_change.php?location=group&action=policy_change&show_life_event=Y&ws_id=' + ws_id,
            iframe: true,
            height: '500px',
            width: '650px',
            open: true,
            escKey: false,
            overlayClose: false,
            onClosed: function () {
                location.reload(true);
            }
        });
    });
});



$(document).on('click','.product_name',function(){
   $('#ajax_loader').show();
   $('[data-toggle="popover"]').popover('hide');
    var ws_id = $(this).attr('data-ws_id');
    var color_class = $(this).data('color');
    if(typeof(color_class) === "undefined") {
        color_class = $('[data-target="#prd_details_expanded"][data-ws_id="'+ws_id+'"]').attr('data-color');
    }

    $('#prd_details_expanded').html('');
    $('#prd_details_expanded').hide();
    $.ajax({
      url: "ajax_get_product_info.php?ws_id=" + ws_id,
      type: 'GET',
      success: function(res) {
         $('#ajax_loader').hide();
         $('#prd_details_expanded').html(res).slideDown();
         $('#panel_product_details').addClass(color_class);
         $(".reinstate_products").colorbox({iframe: true, width: '768px', height: '530px'});
         $('.edit_benifit_amount').colorbox({iframe: true, width: '470px', height: '750px'});
         $('.billing_date_popup').colorbox({iframe: true, width: '470px', height: '385px'});
         $(".member_product_detail").colorbox({iframe: true, width: '980px', height: '580px'});
         $(".id_card_popup").colorbox({iframe: true, width: '1024px', height: '500px'});
         $(".add_term_date").colorbox({iframe: true, width: '435px', height: '430px'});
         $(".reinstate_products").colorbox({iframe: true, width: '768px', height: '530px'});
         $(".assign_depedents").colorbox({iframe: true, width: '450px', height: '300px'});
         $('.prd_detail_edit_depedents').colorbox({iframe: true, width: '768px', height: '450px'});
         $('.order_receipt').colorbox({iframe: true, width: '900px', height: '700px'});
         $('.coverage_popup').colorbox({iframe: true, width: '900px', height: '700px'});
         $('[data-toggle="tooltip"]').tooltip();
         common_select();
         scrollToDiv($('#prd_details_expanded'), 0,'member_products_tab.php','prd_details_expanded');
         $('[data-toggle=popover]').popover();
         
         $(document).off('click', '.btn_edit_tier_change_date');
         $(document).on('click', '.btn_edit_tier_change_date', function (e) {
            var customer_id = $(this).attr('data-customer_id');
            var ws_id = $(this).attr('data-ws_id');
            $.colorbox({
                "href": '<?=$HOST?>/change_benefit_tier_popup.php?location=group&customer_id=' + customer_id + '&ws_id=' + ws_id,
                height: '400px',
                width: '500px',
                iframe: true,
                onClosed: function () {
                    location.reload(true);
                }
            });
        });
      }
    });
});

$(document).on('click','#cancel',function(e){
  scrollToDiv($('#main_products_tbl'), 0,'member_products_tab.php','main_products_tbl');
  $('#prd_details_expanded').slideUp();

});
$(document).on('submit','#details_form',function(e){
   e.preventDefault();
   var params = $('#details_form').serialize();
   var formData = new FormData(this);
   $('#ajax_loader').show();
   $.ajax({
   url: 'member_products_tab.php',
   type: 'POST',
   data: formData,
   processData: false,
   contentType: false,
   success: function(res) {
     $('#ajax_loader').hide();
     if(res.status == 'fail'){
         $('#error_proof_of_coverage').text(res.error).show();
     }else{
      window.location.reload();
     }
   }
   });
});
function ajax_load_products() {
    $('#ajax_loader').show();
    $('#ajax_prd_data').hide();
    $('#is_ajax_prd').val('1');
    var params = $('#frm_search_prd').serialize();
    $.ajax({
      url: $('#frm_search_prd').attr('action'),
      type: 'GET',
      data: params,
      success: function(res) {
        $('#is_ajax_prd').val('');
        $('#ajax_loader').hide();
        $('#ajax_prd_data').html(res).show();
        $('[data-toggle="tooltip"]').tooltip();

         $('.term_multiple_products').colorbox({iframe: true, width: '450px', height: '600px'});
         $(".member_product_detail").colorbox({iframe: true, width: '980px', height: '580px'});
         $(".id_card_popup").colorbox({iframe: true, width: '1024px', height: '500px'});
         $(".add_term_date").colorbox({iframe: true, width: '435px', height: '430px'});
         $(".reinstate_products").colorbox({iframe: true, width: '768px', height: '530px'});
         $(".assign_depedents").colorbox({iframe: true, width: '450px', height: '300px'});
         $('.edit_benifit_amount').colorbox({iframe: true, width: '470px', height: '750px'});
         $('.prd_detail_edit_depedents').colorbox({iframe: true, width: '768px', height: '285px'});
         $('.billing_date_popup').colorbox({iframe: true, width: '470px', height: '385px'});
         $('.coverage_popup').colorbox({iframe: true, width: '900px', height: '700px'});
      }
    });
    return false;
  }
</script> 
<?php } ?>
<script type="text/javascript">


$(document).on('click','.remove_proof_of_coverage',function(){
  id = $(this).data('ce_id');
  parent.swal({
        text: "Delete Document: Are you sure?",
        showCancelButton: true,
        confirmButtonText: 'Confirm'
    }).then(function () {
        $('#ajax_loader').show();
        $.ajax({
          url: '<?=$HOST?>/ajax_delete_coverage_doc.php?location=group',
          type: 'POST',
          data: {ce_id:id},
          dataType: 'JSON',
          success: function(res) {
            $('#ajax_loader').hide();
            if(res.status == 'success'){
              $('.old_coverage_action_div').hide();
              setNotifySuccess(res.message);
            }else if(res.status == 'fail'){
              setNotifyError(res.message);
            }
          }
        });

    }, function (dismiss) {

    });
});

$(document).on('click','.dependent_delete',function(){
  id = $(this).data('id');
  parent.swal({
        text: "Delete Dependent: Are you sure?",
        showCancelButton: true,
        confirmButtonText: 'Confirm'
    }).then(function () {
        $('#ajax_loader').show();
        $.ajax({
          url: '<?=$HOST?>/ajax_delete_dependent.php?location=group',
          type: 'POST',
          data: {id:id},
          dataType: 'JSON',
          success: function(res) {
            $('#ajax_loader').hide();
            if(res.status == 'success'){
              $('.depedents_tr_' + res.id).hide();
              setNotifySuccess(res.message);
            }else if(res.status == 'fail'){
              if(res.allow == 'N'){
                parent.swal({
                    text: res.message,
                    showCancelButton: false,
                    confirmButtonText: 'Confirm'
                }).then(function () {

                });
              }else{
                setNotifyError(res.message);
              }
            }
          }
        });

    }, function (dismiss) {

    });
});

$(document).on('click','#btn_cancel_termination',function(){
    var ws_id = $(this).data('ws_id');
    parent.swal({
        text: "Remove Termination Date: Are you sure?",
        showCancelButton: true,
        confirmButtonText: 'Confirm'
    }).then(function () {
        $('#ajax_loader').show();
        $.ajax({
          url: '<?=$HOST?>/ajax_cancel_termination_date.php?location=group',
          type: 'POST',
          data: {ws_id:ws_id},
          dataType: 'JSON',
          success: function(res) {
            $('#ajax_loader').hide();
            $('.termination_date_td').remove();
            $('.add_term_date').show();
            $('#btn_cancel_termination').hide();
            setNotifySuccess("Termination date removed successfully");
            window.location.reload();
          }
        });

    }, function (dismiss) {

    });
});  

$(document).off('change','.select_term_reason');
$(document).on('change','.select_term_reason', function(e) {
  e.stopImmediatePropagation();
  var reason = $(this).val();
  ws_id = $(this).data('ws_id');
  parent.swal({
        text: "Change Reason: Are you sure?",
        showCancelButton: true,
        confirmButtonText: 'Confirm'
    }).then(function () {
        $('#ajax_loader').show();
        $.ajax({
          url: '<?=$HOST?>/ajax_change_term_reason.php?location=group',
          type: 'POST',
          data: {ws_id:ws_id,reason:reason},
          dataType: 'JSON',
          success: function(res) {
            $('#ajax_loader').hide();
            if(res.status == 'success'){
              setNotifySuccess(res.message);
            }else if(res.status == 'fail'){
              setNotifyError(res.message);
            }
          }
        });

    }, function (dismiss) {

    });


});

existing_dependant_popup = function (customer_id,product_id,web_id,plan_id) {
    window.parent.$.colorbox({
        href: '<?=$HOST?>/benefit_tier_change.php?location=group&new_plan_type=' + plan_id + '&ws_id=' + web_id,
        iframe: true,
        height: '500px',
        width: '650px',
        open: true,
        escKey: false,
        overlayClose: false,
        onClosed: function () {
            parent.$("#is_tier_changed").val('');
            location.reload(true);
        }
    });
}   
</script>