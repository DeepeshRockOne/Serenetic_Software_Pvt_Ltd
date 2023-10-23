<style type="text/css">
  .popover .popover-content table tr td, .popover .popover-content table tr th {
    padding: 4px;
}.popover{ min-width: 225px; }
</style>
<div class="panel panel-default">
  <div class="panel-heading font-normal">Add New Product</div>
  <form action="ajax_save_product.php" role="form" method="post" name="user_form" id="user_form" class="form_wrap">
    <input type="hidden" name="agent_id" value="<?=$agent_id?>" />
    <div class="panel-body">
      <div class="row" style="min-height: 350px">
        <div class="main_html_container add_rul_dy">
        </div>
        <div class="clearfix"></div>
        <div class="m-l-5">
            <button class="btn btn-action add-new-product" type="button"><i class='fa fa-plus'></i> Product</button>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="form-group text-center">
        <button class="btn btn-info" type="button" name="save" id="save" tabindex="6">Save</button>
        <button class="btn btn-action" type="button" onclick='parent.$.colorbox.close()' tabindex="6">Cancel</button>
      </div>
    </div>
  </div>
</form>
</div>
<script type="text/javascript">
/*$(document).ready(function() {
$('.variation_commission_select').multipleSelect({
  width: '100%',
  placeholder: "Please select commission rule"
  });
});*/
$(function(){
  $(".main_html_container").append(getHtml(0));
});
$(document).on("click",".add-new-product",function(){
  $last=parseInt($(".main_html_container .main_html:last").attr("data-id"))+1;
  $(".main_html_container").append(getHtml($last));
   $('.commission_level').popover({
    placement: 'bottom',
    html: true
  }); 
});
function getHtml($id){
  $productBox='<div class="col-xs-8"><div class="form-group"><label>Select Product/Variation</label><select name="variation_product_select['+$id+']" class="form-control variation_product_select" id="variation_product_select_'+$id+'"><option value="">Select Product</option><?php foreach ($company_arr as $key => $company) {?><optgroup label="<?=$key?>"><?php foreach ($company as $pkey => $row) {?><option value="<?=$row["id"]?>" <?=(!empty($products) && in_array($row["id"], $products)) ? "selected" : ""?>><?=$row["name"]?> <?=!empty($row["product_code"])?"(".$row["product_code"].")":""?></option><?php }?></optgroup><?php }?></select><ul class="parsley-error-list"><li class="required" id="err_variation_product_select_'+$id+'"></li></ul></div></div>';
  
  $commissionBox='<div class="col-xs-8"><div class="form-group"><label>Select Commission Rule</label><select name="variation_commission_select['+$id+']" class=" form-control variation_commission_select" id="variation_commission_select_'+$id+'"><option value="">Please select commission rule</option></select><ul class="parsley-error-list"><li class="required" id="err_variation_commission_select_'+$id+'"></li></ul></div></div>';

  $commissionLevelBox='<div class="col-xs-3"><span class="commission_level_div" style="display:none"><a href="javascript:void(0);" data-toggle="popover" data-content="" class="commission_level m-t-30 pull-left">Commission</a></span>';
  
  $removeBtn = ' <a class="m-t-30 m-l-20 pull-left delete-div" href="javascript:void(0)"><i class="fa-lg text-danger icon-close"></i></a>';

  if($id==0){
    $removeBtn="";
  }
  $commissionLevelBox += $removeBtn;
  $commissionLevelBox += "</div><div class='clearfix'></div>";
  return '<div class="main_html" data-id="'+$id+'">'+$productBox+$commissionBox+$commissionLevelBox + '</div>';
};
$(document).on("click",".delete-div",function(){
  $(this).parents(".main_html").fadeOut("slow",function(){
    $(this).remove();
  });
});
$(document).ready(function(){
  $('.commission_level').popover({
    placement: 'bottom',
    html: true
  });

  $('body').on('click', function (e) {
    if ($(e.target).data('toggle') !== 'popover'
        && $(e.target).parents('.popover.in').length === 0) { 
        $('[data-toggle="popover"]').popover('hide');
    }
 }); 

});
$(document).on("change",".variation_product_select",function(){
  $prd_id=$(this).val();
  $div_id=$(this).attr("id").replace("variation_product_select_","");
  $comm_link = $(this).parents(".main_html").find(".commission_level_div"); 
  $comm_popover = $(this).parents(".main_html").find(".commission_level"); 
  $($comm_link).hide();
  $($comm_popover).popover('hide');
  $('body').on('hidden.bs.popover', function (e) {
      $(e.target).data("bs.popover").inState.click = false;
  });
  $.ajax({
    url: "ajax_product_rules_commission_get.php",
    data: {product_id: $prd_id,u_type:'<?=$userType?>',profile_id:'<?=$profile_id?>'},
    dataType: 'JSON',
    type: 'POST',
    success: function(res) {
      $("#variation_commission_select_"+$div_id).html(res.html);
    }
  });
});
$(document).on("change",".variation_commission_select",function(){  
  $('.commission_level').popover('hide');  
  $prd_rull_id = $(this).val();
  $prd_id=$(this).closest(".main_html").find(".variation_product_select").val();
  $div_id=$(this).closest(".main_html").find(".commission_level_div");
  $href_ref=$(this).parents(".main_html").find(".commission_level");  
  $($div_id).show();
  $.ajax({
    url: "ajax_product_commission_level_get.php",
    data: {product_id: $prd_id,rule_id: $prd_rull_id,u_type:'<?=$userType?>',agent_level:'<?=$agent_coded_level?>'},
    type: 'POST',
    success: function(res) {      
      // $($href_ref).data('data-content', res);
      $($href_ref).attr('data-content', res);
    }
  });
});
$(document).on("click","#save",function(){
  $formId=$("#user_form");
  $action=$formId.attr("action");
  $.ajax({
    url: $action,
    type: 'POST',
    dataType: 'json',
    data: $formId.serialize(),
    beforeSend:function(){
      $("#ajax_loader").show();
    },
    success:function(data){
      $("#ajax_loader").hide();
      $("ul.parsley-error-list").find("li").html("");
      if(data.status=='success'){
        window.parent.location.href=window.parent.location.href;
      }else{
        $.each(data.error,function($div_id,$div_error_msg){
          $("#err_"+$div_id).html($div_error_msg);
        });
      }
    }
  });
});
$(document).on("change","select",function(){
  $(this).parents(".form-group").find("ul.parsley-error-list li").html("");
});
/*$(document).on("click",function(){
  $('.commission_level').popover().hide();
});*/
</script>