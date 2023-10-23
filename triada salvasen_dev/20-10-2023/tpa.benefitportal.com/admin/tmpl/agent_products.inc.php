<?php if (checkIsset($is_ajaxed)) {?>
<p class="agp_md_title">Products</p>
<div class="row">
    <div class="col-md-4" >
        <div class="theme-form pr" id="change_status" style="display:none">
            <select class="sel_status" id="chg_status" data-old_status="">
                <option value="" selected disabled hidden></option>
                <option value="Contracted">Active</option>
                <option value="Suspended">Suspended</option>
                <option value="Extinct">Extinct</option>
            </select>
            <label>Select Action</label>
        </div>
        <br />
    </div>
    <?php if(!empty($fetchProduct)) { ?>
        <div class="col-md-8">
            <div class="pull-right m-b-20">
                <div class="form-inline " id="DataTables_Table_0_length top_paginate_cont">
                    <div class="form-group mn">
                        <label for="user_type">Records Per Page </label>
                    </div>
                    <div class="form-group mn">
                        <select size="1" id="p_pages" name="p_pages" class="sel_record" onchange="$('#per_p_pages').val(this.value);$('#nav_page').val(1);agent_product_ajax_submit();">
                            <option value="10" <?= (isset($_GET['p_pages']) && $_GET['p_pages'] == 10 ) || (empty($_GET['p_pages'])) ? 'selected' : ''; ?>>10</option>
                            <option value="25" <?= (isset($_GET['p_pages']) && $_GET['p_pages'] == 25) ? 'selected' : ''; ?>>25</option>
                            <option value="50" <?= (isset($_GET['p_pages']) && $_GET['p_pages'] == 50 ) ? 'selected' : ''; ?>>50</option>
                            <option value="100" <?= (isset($_GET['p_pages']) && $_GET['p_pages'] == 100) ? 'selected' : ''; ?>>100</option>
                            <option value="250" <?= (isset($_GET['p_pages']) && $_GET['p_pages'] == 250) ? 'selected' : ''; ?>>250</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<div class="table-responsive">
<table class="<?=$table_class?> mn">
    <thead>
    <th width="50px">
        <div class="checkbox checkbox-custom checkbox-table">
             <input type="checkbox" class="js-switch" name="chk_all" id='chk_all'/>
            <label for="chk_all"></label>
        </div>
    </th>
    <th width="130px">Added Date</th>
    <th width="200px">Status / As of Date</th>
    <th width="180px">Product Name/ID</th>
    <th width="180px">Product Type</th>
    <th class="text-center">Pricing</th>
    <th class="text-center">Commission</th>
    <th class="text-center" width="185px">PMPMs / As of Date</th>
    <th class="text-center" width="185px">Advance / As of Date</th>
    <th class="text-center" width="70px">Actions</th>
    </thead>
    <tbody>
    <?php if($totalProduct > 0 && !empty($fetchProduct)) { ?>
        <?php foreach($fetchProduct as $product) { ?>
            <tr>
            <td>
            <?php if(!in_array($product['product_status'],array('Extinct','Suspended','Pending'))) { ?>
                <div class="checkbox checkbox-custom mn">
                    <input type="checkbox" name="prd_checkbox[<?=$product['product_id']?>]" class="prd_checkbox" id="prd_checkbox_<?=$product['product_id']?>" value="<?=$product['product_id']?>" data-id='<?=$product['product_id']?>' class="js-switch" />
                    <label for="prd_checkbox_<?=$product['product_id']?>"></label>
                </div>
            <?php } else {
                    echo '';
                } ?>
            </td>
            <td><?=$tz->getDate($product['created_at'],'m/d/Y')?></td>
            <td><div class="theme-form pr w-160 text-center">
                <?php if(!in_array($product['product_status'],array('Extinct','Suspended','Pending')) && !in_array($product['status'],array('Extinct','Suspended'))) { ?>
                <select class="has-value sel_status product_status" name="product_status[<?=$product['product_id']?>]" id="product_status_<?=$product['product_id']?>" data-id="<?=$product['product_id']?>" data-old_status="<?=$product['status']?>">
                    <option value="Contracted" <?=$product['status'] == 'Contracted' ? 'selected="selected"' : ''?>>Active</option>
                    <option value="Pending Approval" <?=$product['status'] == 'Pending Approval' ? 'selected="selected"' : ''?>>Pending</option>
                    <option value="Suspended" <?=$product['status'] == 'Suspended' ? 'selected="selected"' : ''?>>Suspended</option>
                    <!-- <option value="Terminated" <?=$product['status'] == 'Terminated' ? 'selected="selected"' : ''?>>Terminated</option> -->
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
            <td><?=$product["product_type"]?></td>
            <td class="text-center"><a href="javascript:void(0)" data-href="<?=$HOST?>/agents_pricing.php?agent_id=<?=$_GET['id']?>&product_id=<?=$product['pid']?>" class="red-link agents_pricing"><strong>View</strong></a></td>
            <td class="text-center">
                <?php 
                    $comm_json = json_decode($product['commission_json'],true);
                    $commission_amt = '0.00';
                    if($product['commission_on'] == 'Plan'){
                        $commission_arr = isset($comm_json[$product['min_plans']][$level]) ? $comm_json[$product['min_plans']][$level] : array("amount_type"=>"Percentage","amount"=>0);
                        $commission_amt = $commission_arr['amount_type'] == 'Percentage' ? $commission_arr['amount'] .'%' : displayAmount($commission_arr['amount']);
                    }else{
                        $commission_arr = isset($comm_json[$level]) ? $comm_json[$level] : array("amount_type"=>"Percentage","amount"=>0);
                        $commission_amt = $commission_arr['amount_type'] == 'Percentage' ? $commission_arr['amount'] .'%' : displayAmount($commission_arr['amount']);
                    }
                ?>
                <?php if($product['pricing_model'] != 'VariableEnrollee'){  ?>
                <a href="javascript:void(0)" data-href="agents_commission_level.php?product_id=<?=$product['product_id']?>&agent_id=<?=$_GET['id']?>&level_id=<?=$level_id?>" class="agents_commission_level red-link"><strong><?=$commission_amt?></strong></a>
                <?php }else {?>
                    <p><?=$commission_amt?></p>
                <?php } ?>
                <!-- <a href="javascript:void(0)" data-href="agents_commission_level.php?product_id=<?=$product['product_id']?>&agent_id=<?=$_GET['id']?>&level=<?=$level?>" class="agents_commission_level red-link"><strong><?=$commission_amt?></strong></a> -->
            </td>
            <td class="text-center">
                <?php
                    $ext_plus = $product['ext_plus'] > 1 ? "+" : '';
                    $pmpm = '-';
                    if(!empty($product['pm_amt_type'])){
                        if($product['pm_amt_type'] == 'Percentage'){
                            $pmpm = $product['amount']."%".$ext_plus;
                        }else{
                            $pmpm = displayAmount($product['amount']).$ext_plus;
                        }
                    }
                    $pmdate  = '';
                    if(!empty($product['pm_amt_type']) && !empty($product['pm_as_of_date']) && $product['pm_as_of_date']!='0000-00-00'){
                        $pmdate = date('m/d/Y',strtotime($product['pm_as_of_date']));
                    }
                    if(!empty($product['pm_amt_type'])){
                ?>
                <a href="javascript:void(0)" data-href="agents_pmpm.php?id=<?=$product['pmid']?>&agent_id=<?=$_GET['id']?>&product_id=<?=$product['product_id']?>" class="agents_pmpm red-link"><strong><?=$pmpm?></strong></a> <br /><?=$pmdate?>
                <?php } else { ?>
                    -
                <?php } ?>
            </td>
            <td class="text-center">
            <?php 
               if($level != 'LOA'){ 
                if(!empty($product['advFeeId'])) { ?>
                    <a href="javascript:void(0)" data-href="agents_adv_com.php?charged=<?=$product['charged_to']?>&type=<?=$product['advRuleType']?>&agent_id=<?=$_GET['id']?>&product_id=<?=$product['product_id']?>" class="agents_adv_com red-link">
                    <?php                
                        echo "<strong>". $product['advance_month'] ." Month(s)</strong>";
                        $advRuleCreatedAt = checkIsset($product['advRuleCreatedAt']) !='' && $product['advRuleCreatedAt'] !='0000-00-00'  ? date('m/d/Y',strtotime($product['advRuleCreatedAt'])) : '' ;
                        
                    ?>
                    </a> <br /><?=$advRuleCreatedAt?>
                <?php } else echo '-';?>
            <?php } else echo '-';?>    
            </td>
            <td class="text-center icons"><a href="javascript:void(0)" data-href="agents_product_edit.php?product_id=<?=md5($product['product_id'])?>&rule_id=<?=md5($product['apr_id'])?>&agent_id=<?=$_GET['id']?>" class="agents_product_edit"><i class="fa fa-edit"></i></a></td>
            </tr>
    <?php } }else{?>
        <tr>
            <td colspan="10">
            No rows Found!
            </td>
        </tr>
    <?php } ?>
    </tbody>
    <tfoot>
    <tr>
    <?php if($totalProduct > 0 && !empty($fetchProduct)) { ?>
        <td colspan="10">
        <?php echo $paginate_product->links_html; ?>
        </td>
    <?php } ?>
    </tr>
    </tfoot>
</table>
</div>
<hr />
<?php } else { ?>
<div class="">
    <form id="agent_product_frm_search" action="agent_products.php" method="GET" class="sform">
        <input type="hidden" name="id" id="id" value="<?=!empty($agent_id) ? $agent_id : $_REQUEST['id']?>"/>
        <input type="hidden" name="is_ajaxed" id="agent_product_is_ajaxed" value="1"/>
        <input type="hidden" name="p_pages" id="per_p_pages" value="<?=checkIsset($p_per_page)?>"/>
         <?php if($agent_id==md5(1)){
            $tmpLevelName = "Root";
        }else{
            $tmpLevelName = $agentCodedRes[$row['agent_coded_id']]['level'];
        }
        ?>
        <input type="hidden" name="level" id="level" value="<?= $tmpLevelName ?>"/>
        <input type="hidden" name="level_id" id="level_id" value="<?=$row['agent_coded_id'] ?>"/>
    </form>
<div id="agent_product_ajax_data"></div>

<script type="text/javascript">
if(typeof($) !== 'undefined') {
    $(document).ready(function () {
        agent_product_ajax_submit();
        var execute=function(){
            refreshControl('.sel_status');
            refreshControl('.sel_record');
        }
        dropdown_pagination(execute,'agent_product_ajax_data');
    });
}

$(document).off('click','.agents_product_edit');
$(document).on('click','.agents_product_edit',function(e){
    $href = $(this).data('href');
   $.ajax({
        url: "ajax_agent_prd_edit.php",
        type: "POST",
        dataType:"json",
        success: function (res) {
            if(res.status == 'not_running'){
                $.colorbox({iframe: true,href:$href, width: '1170px', height: '768px'});
            }else{
                $href = "warning.php";
                $.colorbox({iframe: true,href:$href, width: '500px', height: '310px', closeButton: false});
            }
        }
   });
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
    //$("input[type='checkbox']").uniform();
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
    //$("input[type='checkbox']").uniform();
});

$(document).off('click','.agents_pricing');
$(document).on('click','.agents_pricing',function(e){
    $href = $(this).data('href');
    $.colorbox({iframe: true,href:$href, width: '530px', height: '350px'});
});

$(document).off('click','.agents_commission_level');
$(document).on('click','.agents_commission_level',function(e){
    $href = $(this).data('href');
    $.colorbox({iframe: true,href:$href, width: '900px', height: '500px'});
});

$(document).off('click','.agents_pmpm');
$(document).on('click','.agents_pmpm',function(e){
    $href = $(this).data('href');
    $.colorbox({iframe: true,href:$href, width: '900px', height: '400px'});
});

$(document).off('click','.agents_adv_com');
$(document).on('click','.agents_adv_com',function(e){
    $href = $(this).data('href');
    $.colorbox({iframe: true,href:$href, width: '900px', height: '400px'});
});

$(document).off('click', '#agent_product_ajax_data ul.pagination li a');
$(document).on('click', '#agent_product_ajax_data ul.pagination li a', function (e) {
    e.preventDefault();
    $('#ajax_loader').show();
    $('#agent_product_ajax_data').hide();
    $.ajax({
        url: $(this).attr('href'),
        type: 'GET',
        success: function (res) {
            $('#ajax_loader').hide();
            $('#agent_product_ajax_data').html(res).show();
            refreshControl('.sel_status');
            refreshControl('.sel_record');
            common_select();
        }
    });
});

$(document).off('change', '.sel_status');
$(document).on('change', '.sel_status', function (e) {
    e.stopPropagation();
    var $old_val = $(this).attr('data-old_status');
    var $val = $(this).val();
    var pid = $(this).attr('id').replace('product_status_','');
    var $txt = '';
    $controlID = $(this).attr('id');
    if($val === 'Contracted'){
        $txt = 'Active status allows for new sales and renewals based on the rules of the product.';
    }else if($val === 'Suspended'){
        $txt = 'Suspended status allows renewals to continue and ability to receive commissions, but stops new applications.'
    }else if($val === 'Extinct'){
        $txt = 'Extinct status allows renewals to continue but the agent does not receive renewal commissions and stops new applications.';
    }
    swal({
        title: "<h4>Change Product Status to <span class='text-blue'>"+$val+"</span>: Are you sure?</h4>",
        html:'<p class="fs14 m-b-15">'+$txt+'</p><div class="text-center fs14"><div class="d-inline  text-left"><label class="m-b-10 "><input name="downline" type="checkbox" value="downline" id="downline" class="js-switch" autofocus> <span class="p-l-10"> Apply to downline agents? </span> </label><div class="clearfix"></div>' + '<label class="m-b-0" ><input name="loa" type="checkbox" value="loa" id="loa" class="js-switch"> <span class="p-l-10"> Apply to LOA agents?</span></label></div></div>',
        showCancelButton : true,
        confirmButtonText: "Confirm",
        cancelButtonText: "Cancel",
    }).then(function(e1){
        if(e1){
            var agent_id = $("#id").val();
            var $downline = '';
            if($("#downline").is(":checked")){
                var $downline = $("#downline").val();
            }
            var $loa = '';
            if($("#loa").is(":checked")){
                $loa = $("#loa").val();
            }
                
            var params = $('#agent_product_frm_search').serialize();
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
                url : 'ajax_product_status_change.php',
                data : {product_ids:values,downline:$downline,loa:$loa,product_status:$val,agent_id:agent_id},
                type: 'POST',
                dataType: 'json',
                beforeSend:function(){
                    parent.$("#ajax_loader").show();
                },
                success: function(res_status) {
                    parent.$("#ajax_loader").hide();
                    if(res_status.status == 'done'){
                        setNotifySuccess("Product Status Changed Successfully");
                        agent_product_ajax_submit();
                    }else{
                        parent.$("#ajax_loader").hide();
                    }
                }
            });
        }
    },function(dismiss){
       $('#'+$controlID).val($old_val);
       $('#'+$controlID).selectpicker('render');
        fRefresh();
        return false;
    });
    //$("input[type='checkbox']").uniform();
});
function agent_product_ajax_submit() {
    $('#ajax_loader').show();
    $('#agent_product_ajax_data').hide();
    $('#agent_product_is_ajaxed').val('1');
    var params = $('#agent_product_frm_search').serialize();
    $.ajax({
        url: $('#agent_product_frm_search').attr('action'),
        type: 'GET',
        data: params,
        success: function (res) {
            $('#ajax_loader').hide();
            $('#agent_product_ajax_data').html(res).show();
            refreshControl('.sel_status');
            refreshControl('.sel_record');
            common_select();
        }
    });
    return false;
}
</script>
<?php } ?>