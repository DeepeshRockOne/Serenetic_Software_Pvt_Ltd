<?php if (checkIsset($is_ajaxed)) {?>
    <p class="agp_md_title">Products</p>
    <div class="row">
        <div class="col-md-4" id="change_status" style="display:none">
            <div class="theme-form pr">
                <select class="sel_status" id="chg_status">
                    <option value="" selected disabled hidden></option>
                    <option value="Contracted">Active</option>
                    <option value="Suspended">Suspended</option>
                    <option value="Extinct">Extinct</option>
                </select>
                <label>Select Action</label>
            </div>
            <br />
        </div>
    </div>
    <div class="table-responsive">
        <table class="<?=$table_class?> mn">
            <thead>
                <!-- <th width="70px">
                    <input type="checkbox" name="chk_all" id="chk_all"  />
                </th> -->
                <th width="250px">Added Date</th>
                <th width="250px">Status / As of Date</th>
                <th>Product Name/ID</th>
                <?php if(!empty($resGroupProduct) && $resGroupProduct['billing_type']=='list_bill') { ?>
                    <th>List Bill/TPA</th>
                <?php } ?>
                <th>Pricing</th>
                <th class="text-center" width="70px">Actions</th>
            </thead>
            <tbody>
            <?php if($totalProduct > 0 && !empty($fetchProduct)) { ?>
                <?php foreach($fetchProduct as $product) { ?>
                    <tr>
                    <!-- <td>
                        <?php if(!in_array($product['product_status'],array('Extinct','Suspended','Pending'))) { ?>
                            <input type="checkbox" name="prd_checkbox[<?=$product['product_id']?>]" class="prd_checkbox" id="prd_checkbox_<?=$product['product_id']?>" value="<?=$product['product_id']?>" data-id='<?=$product['product_id']?>' class="js-switch" />
                        <?php } else {
                                echo '';
                        } ?>
                    </td> -->
                    <td><?=$tz->getDate($product['created_at'],'m/d/Y')?></td>
                    <td><div class="theme-form pr w-160 text-center">
                        <?php if(!in_array($product['product_status'],array('Extinct','Suspended','Pending')) && !in_array($product['status'],array('Extinct','Suspended'))) { ?>
                            <select class="has-value sel_status product_status" name="product_status[<?=$product['product_id']?>]" id="product_status_<?=$product['product_id']?>" data-id="<?=$product['product_id']?>" data-old_status="<?=$product['status']?>" disabled>
                                <option value="Contracted" <?=$product['status'] == 'Contracted' ? 'selected="selected"' : ''?>>Active</option>
                                <option value="Pending Approval" <?=$product['status'] == 'Pending Approval' ? 'selected="selected"' : ''?>>Pending</option>
                                <option value="Suspended" <?=$product['status'] == 'Suspended' ? 'selected="selected"' : ''?>>Suspended</option>
                                <option value="Extinct" <?=$product['status'] == 'Extinct' ? 'selected="selected"' : ''?>>Extinct</option>
                            </select>
                            <div class="clearfix"></div>
                            <span class="fs12"><?=$tz->getDate($product['updated_at'],'m/d/Y g:i A T')?></span>
                        <?php }else { ?>
                            <?php if(in_array($product['product_status'],array('Extinct','Suspended','Pending'))){ ?>
                                <p class="text-red"><?=$product['product_status']?></p>
                            <?php }else { ?>
                                <p class="text-red"><?=$product['status']?></p>
                            <?php } ?>
                            <span class="fs12"><?=$tz->getDate($product['updated_at'],'m/d/Y g:i A T')?></span>
                        <?php }  ?>
                        </div>
                        </td>
                    <td><p class="m-b-5"><?=$product['name']?></p>
                        <label class="label label-rounded <?php echo  in_array($product['product_status'],array('Extinct','Suspended','Pending')) ? 'label-danger' : 'label-success'?>"><?=$product['product_code']?></label></td>
                    <?php if(!empty($resGroupProduct) && $resGroupProduct['billing_type']=='list_bill') { ?>
                        <td>
                            <?php echo 'List Bill'; ?>
                            <!-- <div class="custom-switch listbill-switch">
                                <label class="smart-switch">
                                    <input type="checkbox" class="js-switch product_billing_type" data-id="<?= $product['apr_id'] ?>" id="product_billing_type_<?= $product['apr_id'] ?>"  <?=$product['product_billing_type']=='list_bill' ? 'checked' : ''?> />
                                    <div class="smart-slider round"></div>
                                </label>
                            </div> -->
                        </td>
                    <?php } ?>
                    <td><a href="javascript:void(0)" data-href="<?=$HOST?>/agents_pricing.php?agent_id=<?=$_GET['id']?>&product_id=<?=$product['pid']?>" class="red-link agents_pricing"><strong>View</strong></a></td>
                    <td class="text-center icons"><a href="javascript:void(0)" data-href="group_product_detail.php?product_id=<?=md5($product['product_id'])?>&user_id=<?=$_GET['id']?>" class="group_product_detail"><i class="fa fa-info-circle" aria-hidden="true"></i></i></a></td>
                    </tr>
            <?php } }else{?>
                <tr>
                    <td colspan="7">
                    No rows Found!
                    </td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
            <?php if($totalProduct > 0 && !empty($fetchProduct)) { ?>
                <td colspan="7">
                <?php echo $paginate_product->links_html; ?>
                </td>
            <?php } ?>
            </tr>
            <tr>
               <td colspan="7">
                  <div class="row table-footer-row">
                     <div class="col-sm-12">
                        <!-- <div class="pull-right">
                           <a href="javascript:void()" data-href="group_add_products.php?group_id=<?=$_GET['id']?>" class="btn btn-action group_add_products">+ Product</a>
                        </div> -->
                     </div>
                  </div>
               </td>
            </tr>
            </tfoot>
        </table>
    </div>
    <hr />
<?php } else { ?>
<div class="">
    <form id="group_product_frm_search" action="group_products.php" method="GET" class="sform">
        <input type="hidden" name="id" id="id" value="<?=!empty($group_id) ? $group_id : $_REQUEST['id']?>"/>
        <input type="hidden" name="is_ajaxed" id="group_product_is_ajaxed" value="1"/>
        <input type="hidden" name="pages" id="per_pages" value="<?=checkIsset($per_page)?>"/>
    </form>
<div id="group_product_ajax_data"></div>

<script type="text/javascript">
if(typeof($) !== 'undefined') {
    $(document).ready(function () {
        var execute=function(){
            refreshControl('.sel_status');
        }
        dropdown_pagination(execute,'group_product_ajax_data');
        group_product_ajax_submit();
    });
}

$(document).off('click','.group_product_detail');
$(document).on('click','.group_product_detail',function(e){
    $href = $(this).data('href');
    $.colorbox({iframe: true,href:$href, width: '768px', height: '685px'});
});
$(document).off('click','.group_add_products');
$(document).on('click','.group_add_products',function(e){
    $href = $(this).data('href');
    $.colorbox({iframe: true,href:$href, width: '515px', height: '300px'});
});

$(document).off('click','#chk_all');
$(document).on('click','#chk_all',function(e){
    if($(this).is(":checked")){
        $(".prd_checkbox").prop('checked',true);
        $("#change_status").show();
    }else{
        $(".prd_checkbox").prop('checked',false);
        $("#change_status").hide();
    }
});

$(document).on('change',".prd_checkbox",function(){
    if($('.prd_checkbox:checked').length == $('.prd_checkbox').length){
        $('#chk_all').prop('checked',true);        
    }else{
        $('#chk_all').prop('checked',false);
        $("#change_status").hide();
    }
    $('.prd_checkbox').each(function(e){
        if($(this).is(":checked")){
            $("#change_status").show();
        }
    });
});

$(document).off('click','.agents_pricing');
$(document).on('click','.agents_pricing',function(e){
    $href = $(this).data('href');
    $.colorbox({iframe: true,href:$href, width: '730px', height: '550px'});
});


$(document).off('click', '#group_product_ajax_data ul.pagination li a');
$(document).on('click', '#group_product_ajax_data ul.pagination li a', function (e) {
    e.preventDefault();
    $('#ajax_loader').show();
    $('#group_product_ajax_data').hide();
    $.ajax({
        url: $(this).attr('href'),
        type: 'GET',
        success: function (res) {
            $('#ajax_loader').hide();
            $('#group_product_ajax_data').html(res).show();
            refreshControl('.sel_status');
            common_select();
        }
    });
});


$(document).off('change', '.sel_status');
$(document).on('change', '.sel_status', function (e) {
    var $old_val = $(this).attr('data-old_status');
    var $val = $(this).val();
    var pid = $(this).attr('id').replace('product_status_','');
    var $txt = '';
    if($val === 'Contracted'){
        $txt = 'Active status allows for new sales and renewals based on the rules of the product.';
    }else if($val === 'Suspended'){
        $txt = 'Suspended status allows renewals to continue and ability to receive commissions, but stops new enrollments.'
    }else if($val === 'Extinct'){
        $txt = 'Extinct status allows renewals to continue but the group does not receive renewal commissions and stops new enrollments.';
    }
    swal({
        title: "<h4>Change Product Status to <span class='text-blue'>"+$val+"</span>: Are you sure?</h4>",
        showCancelButton : true,
        confirmButtonText: "Confirm",
        cancelButtonText: "Cancel",
    }).then(function(e1){
        if(e1){
            var group_id = $("#id").val();
            
                
            var params = $('#group_product_frm_search').serialize();
            var values = [];
            $(".prd_checkbox").each(function(e){
                if($(this).is(":checked")){
                    values.push($(this).val());
                }
            });
            if(values.length == 0){
                values.push(pid);
            }
            $.ajax({
                url : 'ajax_group_product_status_change.php',
                data : {product_ids:values,product_status:$val,group_id:group_id},
                type: 'POST',
                dataType: 'json',
                beforeSend:function(){
                    parent.$("#ajax_loader").show();
                },
                success: function(res_status) {
                    parent.$("#ajax_loader").hide();
                    if(res_status.status == 'done'){
                        setNotifySuccess("Product Status Changed Successfully");
                        group_product_ajax_submit();
                    }else{
                        parent.$("#ajax_loader").hide();
                    }
                }
            });
        }
    },function(dismiss){
        $('#product_status_'+pid).val($old_val);
        $('#product_status_'+pid).selectpicker('render');
        return false;
    });
    //$("input[type='checkbox']").uniform();
});
function group_product_ajax_submit() {
    $('#ajax_loader').show();
    $('#group_product_ajax_data').hide();
    $('#group_product_is_ajaxed').val('1');
    var params = $('#group_product_frm_search').serialize();
    $.ajax({
        url: $('#group_product_frm_search').attr('action'),
        type: 'GET',
        data: params,
        success: function (res) {
            $('#ajax_loader').hide();
            $('#group_product_ajax_data').html(res).show();
            refreshControl('.sel_status');
            common_select();
        }
    });
    return false;
}

    $(document).off('change','.product_billing_type');
    $(document).on('change','.product_billing_type',function(e){
        $id=$(this).attr("data-id");

      if($(this).is(":checked") === true){
        product_billing_type('list_bill',$id);
      }else{
        product_billing_type('TPA',$id);
      }
    });

    function product_billing_type($billing_type,$id) {
        $txt = "TPA";
         if($billing_type == "list_bill"){
            $txt = "List Bill";
        }else if($billing_type == "TPA"){
            $txt = " be managed by an external entity";
        }
        swal({
          text: "Include Product on "+$txt+": Are you sure?",
          showCancelButton: true,
          confirmButtonText: "Confirm",
          cancelButtonText: "Cancel",
        }).then(function () {
          $("#ajax_loader").show();
          $.ajax({
            url: 'ajax_group_product_billing_type.php',
            data: {
              id: $id,
              billing_type:$billing_type,
            },
            dataType: 'json',
            type: 'post',
            success: function (res) {
               $("#ajax_loader").hide();
              if (res.status == "success") {
                setNotifySuccess(res.msg);
              }else{
                setNotifyError(res.msg);
              }
            }
          });
        }, function (dismiss) {

          if($billing_type == "list_bill"){
            $("#product_billing_type_"+$id).prop("checked",false);
          }else{
            $("#product_billing_type_"+$id).prop("checked",true);
          }
        });
      }
</script>
<?php } ?>