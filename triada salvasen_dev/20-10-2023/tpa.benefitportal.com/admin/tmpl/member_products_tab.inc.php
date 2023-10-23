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
                      <a href="<?=$HOST?>/id_card_popup.php?ws_id=<?=md5($row['ws_id'])?>&user_type=Admin&user_id=<?=$_SESSION['admin']['id']?>" class="id_card_popup" data-toggle="tooltip" data-placement="top" title="ID Card"><i class="fa fa-address-card-o" aria-hidden="true"></i></a>
                    <?php } ?>
                    <a href="member_product_detail.php?customer_id=<?=$row['customer_id']?>&product_id=<?=$row['p_id']?>&plan_id=<?=$row['matrix_id']?>" class="member_product_detail" data-toggle="tooltip" data-placement="top" title="Product Details"><i class="fa fa-info-circle" aria-hidden="true"></i></i></a>

                    <a href="javascript:void(0);" class="product_name" data-toggle="" data-target="#prd_details_expanded" data-ws_id="<?=md5($row['ws_id'])?>" data-color="<?=$data_color?>"><i class="fa fa-edit" aria-hidden="true"></i></a>

                    <a href="../policy_document.php?userType=Admin&customer_id=<?=md5($row['customer_id'])?>&ws_id=<?=md5($row['ws_id'])?>" data-toggle="tooltip" data-placement="top" title="Plan Doc"><i class="fa fa-download" aria-hidden="true"></i></a>

                    <?php 
                        $resAgreement = $function_list->getProductJoinderAgreement($row['customer_id'],$row['p_id']);
                        if(!empty($resAgreement["id"])){
                    ?>
                        <a href="../joinder_document.php?id=<?=md5($resAgreement['id'])?>" data-title='Joinder Agreement' data-toggle="tooltip"><i class="fa fa-download" aria-hidden="true"></i></a>
                    <?php } ?>
                    <?php $file_name = !empty($row['file_name'])?str_replace(array('"','[',']'),'',$row['file_name']):'';
                      if($row['application_type']=='voice_verification' && file_exists($PHYSICAL_DOCUMENT_DIR.$file_name)){
                    ?>
                      <a href=<?=$PHYSICAL_DOCUMENT_WEB?><?=$file_name?> data-title='VoiceFile Download' data-toggle="tooltip" download><i class="fa fa-download" aria-hidden="true"></i></a>
                    <?php } ?>
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
                           <a href="member_enrollment.php?customer_id=<?=$member_id?>" class="btn btn-action">+ Product</a>
                          <?php } ?>
                           <a href="<?=$HOST?>/term_multiple_products.php?location=admin&member_id=<?=$member_id?>" class="btn btn-info term_multiple_products">Multi-Term</a>
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
    </form>
    <div id="ajax_loader" class="ajex_loader" style="display: none;">
      <div class="loader"></div>
    </div>
    <div id="ajax_prd_data" class=""> </div>

      <div id="prd_details_expanded">
      </div>
      <div class="m-t-30"></div>
      <div id="member_document_dynamic_div" class="custom_drag_control"style="display: none" >
        <div class="memberDocumentDivCount" id="member_document_div_~document_counter~" data-counter="~document_counter~">
          <div class="pull-right m-t-5">
            <a href="javascript:void(0);" class="btn mn pn text-action remove_member_document" id="remove_member_document_~document_counter~" data-id="" data-removeId="~document_counter~">
              <i class="fa fa-times"></i>
            </a>
          </div><div class="clearfix"></div> 
          <div class="phone-control-wrap"> 
            <div class="phone-addon">       
              <div class="custom_drag_control" id="addFile">
                <span class="btn btn-action">Choose File</span>
                <input type="file" class="gui-file" id="member_document_~document_counter~"
                            name="member_document[~document_counter~]" title="&nbsp;">
                <input type="text" class="gui-input"
                                placeholder="Drag or Select File" value="">
              </div>
            </div>
              <div class="phone-addon">
              <?php if(!empty($_POST['member_document'])){ ?>
                  <div class="phone-addon w-30 member_document_div">
                      <a class="text-action fs18" target="blank" download><i
                                  class=" fa-lg fa fa-download"></i></a>
                  </div>
                  <?php } ?>
              </div>
          </div>
          <p ><span id="error_member_document_~document_counter~" class="error"></span></p>
        </div>
    </div>
<script type="text/javascript">
$(document).ready(function() {
    ajax_load_products();
    
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
            url: "ajax_check_dependent.php?product_id=" + product_id + "&customer_id=" + customer_id +"&web_id="+web_id + "&prd_plan="+prd_plan,
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
                href: '<?=$HOST?>/benefit_tier_change.php?location=admin&action=policy_change&new_prd_id=' + new_prd_id + '&ws_id=' + ws_id,
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
            href: '<?=$HOST?>/benefit_tier_change.php?location=admin&action=policy_change&show_life_event=Y&ws_id=' + ws_id,
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
                "href": '<?=$HOST?>/change_benefit_tier_popup.php?location=admin&customer_id=' + customer_id + '&ws_id=' + ws_id,
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
$(document).off('submit','#details_form');
$(document).on('submit','#details_form',function(e){
    $('.error').html("");
    e.preventDefault();
    $count=$("#member_document_div .memberDocumentDivCount").length;
    $("#count").val($count);
   var params = $('#details_form').serialize();
   var formData = new FormData(this);
   $('#ajax_loader').show();
   $.ajax({
   url: 'member_products_tab.php',
   type: 'POST',
   dataType: 'JSON',
   data: formData,
   processData: false,
   contentType: false,
   success: function(response) {
     $('#ajax_loader').hide();
     if(response.status == 'fail'){
        $.each(response.error,function(index,error){
          $("#error_"+index).html(error).show();
        });
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
          url: '<?=$HOST?>/ajax_delete_coverage_doc.php?location=admin',
          type: 'POST',
          data: {ce_id:id},
          dataType: 'JSON',
          success: function(res) {
            $('#ajax_loader').hide();
            if(res.status == 'success'){
              $('#coverageFile').val("");
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
          url: '<?=$HOST?>/ajax_delete_dependent.php?location=admin',
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
        text: "<br>Delete Record: Are you sure?",
        showCancelButton: true,
        confirmButtonText: 'Confirm'
    }).then(function () {
        $('#ajax_loader').show();
        $.ajax({
          url: '<?=$HOST?>/ajax_cancel_termination_date.php?location=admin',
          type: 'POST',
          data: {ws_id:ws_id},
          dataType: 'JSON',
          success: function(res) {
            $('#ajax_loader').hide();
            $('.termination_date_td').remove();
            $('.add_term_date').show();
            $('#btn_cancel_termination').hide();
            

            if(res.future_next_billing_date == 'Y'){
              parent.swal({
                  text: "<br>Product termination date removed, next bill date will be: " + res.next_billing_date,
                  showCancelButton: true,
                  confirmButtonText: 'Confirm'
              }).then(function () {
                setNotifySuccess("Termination date removed successfully");
                window.location.reload();

              }, function (dismiss) {
                window.location.reload();
              });
            }else if(res.future_next_billing_date == 'N'){

              parent.swal({
                  text: "<br>Product termination date removed, please set next bill date",
                  showCancelButton: true,
                  confirmButtonText: 'Confirm'
              }).then(function () {
                $.colorbox({iframe: true,href:'<?=$HOST?>/edit_next_billing_date.php?location=admin&ws_id='+res.ws_id, width: '470px', height: '385px'});
              }, function (dismiss) {
                window.location.reload();
              });

            }

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
          url: '<?=$HOST?>/ajax_change_term_reason.php?location=admin',
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
        href: '<?=$HOST?>/benefit_tier_change.php?location=admin&new_plan_type=' + plan_id + '&ws_id=' + web_id,
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

$(document).off("click","#addDocumentBtn");   
$(document).on("click","#addDocumentBtn",function(){
  
			$count=$("#member_document_div .memberDocumentDivCount").length;
			$number=$count+1;
       
			$display_plan_code_counter = $number;
			html = $('#member_document_dynamic_div').html();
			html = html.replace(/~document_counter~/g,"-"+$number)
			html = html.replace(/~Choose File~/g,$display_plan_code_counter)
            $('#member_document_div').append(html);
            fRefresh();
});
$(document).off("click",".remove_member_document");
$(document).on("click",".remove_member_document",function(){
  $removeId=$(this).attr("data-removeId");
  $member_id = $('#member_id').val();
  
  if($removeId > 0){
    swal({
        text: "<br>Delete Member Document: Are you sure?",
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel'
        
    }).then(function(){
      $.ajax({
          url: 'ajax_delete_member_document.php',
          type: 'POST',
          data: {
          removeId: $removeId,
          member_id: $member_id,
          delete: 'Y'
        },
          dataType: 'JSON',
          success: function(res){
              if(res.status == 'success'){
                if($("#remove_member_document_"+$removeId).attr("data-id") == 0){
                  window.location.reload();
                  $("#dispalyFileName_"+$removeId).val("");
                  $("#old_member_document"+$removeId).val("");
                  $("#member_document_action_div_0").hide();
                  $("#member_document_trash_div_0").hide();
                }else{
                  $("#member_document_div_"+$removeId).remove();
                }
                window.setNotifySuccess("Removed Member Document successfully");
              }
          }
      })
    }); 
     }else{
      $("#member_document_div_"+$removeId).remove();
     } 
  });
</script>