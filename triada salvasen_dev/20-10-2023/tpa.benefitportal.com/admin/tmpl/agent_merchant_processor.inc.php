<?php 
$is_ajaxed = isset($is_ajaxed) ? $is_ajaxed : "";
if($is_ajaxed) { ?>
<div class="clearfix">
    <div class="pull-left">
        <p class="m-b-15"><span class="agp_md_title mn">Merchant Processor -</span> Assign whether ACH or Credit/Debit is active for this individual agent below.</p>
    </div>
    <div class="pull-right m-b-15"> <a href="javascript:void(0);" class="btn btn-action" id="add_processor">+  Processor</a> </div>
</div>
<div class="row theme-form new_merchant_processor" style="display:none"  id="new_merchant_processor">
    <div class="col-md-6">
        <div class="form-group height_auto">
            <div class="phone-control-wrap">
                <div class="phone-addon pn">                
                    <select class="new_processor form-control" name="new_processor" id="new_processor" data-type="" data-old_value="" data-live-search="true">
                    <option value="" selected disabled hidden></option>
                    <?php if(!empty($merchant_processors)) { ?>
                        <?php foreach($merchant_processors as $processor) { ?>
                        <option value="<?=$processor['id']?>" data-type="<?=$processor['type']?>"><?=$processor['name'].' - '.$processor['merchant_id'].' '.$processor['description']?></option>
                    <?php }} ?>
                    </select>
                    <label>Search Name, MID, or Description</label>
                </div>
                <div class="phone-addon w-130">
                <button class="btn btn-action" id="add_new_processor">Add Selected</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 text-right">
        <a href="javascript:void(0);" class="btn red-link" onclick="$('#new_merchant_processor').hide()">Cancel</a>
    </div>
</div>
<div class="table-responsive">
    <table class="<?=$table_class?>">
        <thead>
            <tr>
                <th></th>
                <th>Default</th>
                <th>Name</th>
                <th>MID</th>
                <th>Date Added</th>
                <th>Payments</th>
                <th width="150px">Status</th>
                <th>Description</th>
                <th width="100px">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if($totalProcessor > 0) { $pcount =0; $vcount = $gcount = 0;$idArr = array();$existAch = false; ?>
                <?php foreach($fetchProcessor as $processor) {
                    if($processor['is_assigned_to_all_product'] == 'Y'  && $processor['is_cc_accepted'] == 'Y'){
                        $is_default = 'Standby';
                        if($processor['is_default_for_cc'] == 'Y'){
                            $is_default = 'CC';
                        }
                        $payment_type = '-';
                        if($processor['is_cc_accepted'] == 'Y'){
                            $payment_type ='CC';
                        }
                        $class_value = '';
                        if($gcount % 2 == 0){
                            $class_value = 'table_light_danger';
                            $gcount++;
                        }else{
                            $class_value = 'table_dark_danger';
                            $gcount++;
                        }
                    }else{
                        $is_default = 'Standby';
                        if(($processor['is_default_for_ach'] == 'Y') && ($processor['is_default_for_cc'] == 'Y')){
                            $is_default = 'ACH & CC';
                        }else if($processor['is_default_for_ach'] == 'Y'){
                            $is_default = 'ACH';
                        }else if($processor['is_default_for_cc'] == 'Y'){
                            $is_default = 'CC';
                        }
                        $payment_type = '-';
                        if(($processor['is_ach_accepted'] == 'Y') && ($processor['is_cc_accepted'] == 'Y')){
                            $payment_type = 'ACH & CC';
                        }else if($processor['is_ach_accepted'] == 'Y'){
                            $payment_type ='ACH';
                            $existAch = true;
                        }else if($processor['is_cc_accepted'] == 'Y'){
                            $payment_type ='CC';
                        }
                        $class_value = '';
                        if($gcount % 2 == 0){
                            $class_value = 'table_light_danger';
                            $gcount++;
                        }else{
                            $class_value = 'table_dark_danger';
                            $gcount++;
                        }
                        if($processor['type'] == 'Variation' && $processor['is_assigned_to_all_product'] =='N'){
                            $vcount++;
                        }
                    }

                    if($processor['is_ach_accepted'] =='Y'  && $processor['is_cc_accepted'] == 'N'){
                        $color_class  = $processor['global_accept_ach_status'] == 'Active'  && $processor['is_assigned_to_all_product'] =='Y' ?  $class_value  : ''; 
                    }else{
                        $color_class = $processor['status'] == 'Active'  && $processor['is_assigned_to_all_product'] =='Y' ? $class_value : '' ;
                    }
                    
                ?>
                    <tr class="<?=$is_default != 'Standby' ? $color_class : ''?>">
                        <td class="<?=$processor['type'] == 'Variation' ? 'text-light-gray' : '' ?>"><?= $vcount !=0 ? '<i class="fa fa-ellipsis-v" aria-hidden="true"></i> &nbsp;' . $vcount : '' ?></td>
                        <td class="<?=$processor['type'] == 'Variation' ? 'text-light-gray' : 'text-blue' ?>">
                        <?php if($processor['type'] == 'Variation' && $processor['is_assigned_to_all_product'] =='N'){
                            echo 'Variant';
                        }elseif($processor['type'] == 'Variation' &&  $processor['is_assigned_to_all_product'] =='Y'){
                            echo $payment_type;
                        }else{
                            echo $is_default;
                        }
                        ?>
                        </td>
                        <td><?=$processor['name']?></td>
                        <td><?=$processor['merchant_id']?></td>
                        <td><?=date("m/d/Y",strtotime($processor['created_at']))?></td>
                        <td><?=$payment_type?></td>
                        <td class=""><div class="theme-form pr">
                            <?php if($processor['is_ach_accepted'] == 'Y' && $processor['is_cc_accepted'] == 'N') { ?>
                            <select class="form-control has-value processor_status processor_status_ach" name="processor_status" id="processor_status_<?=$processor['id']?>" data-type="<?=$processor['type']?>" data-ach_status="ach_status" data-old_value="<?=$processor['global_accept_ach_status']?>">
                                <option value="Active" <?=($processor['global_accept_ach_status'] == 'Active') ? 'selected="selected"' : '' ?>>Active</option>
                                <option value="Inactive" <?=($processor['global_accept_ach_status'] == 'Inactive') ? 'selected="selected"' : '' ?>>Inactive</option>
                            </select>
                            <label>Select</label>
                            <?php }else{ ?>
                            <select class="form-control has-value processor_status" name="processor_status" id="processor_status_<?=$processor['id']?>" data-type="<?=$processor['type']?>" data-old_value="<?=$processor['status']?>">
                                <option value="Active" <?=($processor['status'] == 'Active') ? 'selected="selected"' : '' ?>>Active</option>
                                <option value="Inactive" <?=($processor['status'] == 'Inactive') ? 'selected="selected"' : '' ?>>Inactive</option>
                            </select>
                            <label>Select</label>
                            <?php } ?>
                        </div></td>
                        <td><?=$processor['description']?></td>
                        <td class="icons text-right"><a href="add_merchant_processor.php?type=<?=$processor["type"]?>&id=<?=$processor["id"]?>" target="_blank"><i class="fa fa-external-link" aria-hidden="true"></i></a>
                        <?php if($processor['is_assigned_to_all_product'] == 'N') { ?>
                            <a href="javascript:void(0);" onclick='delete_processor("<?=$processor["id"]?>","<?=$processor["type"]?>","<?=$processor["name"]?>")'><i class="fa fa-trash" aria-hidden="true"></i></a>
                        <?php } ?>
                        </td>
                    </tr>
                <?php
                if($processor['is_assigned_to_all_product'] == 'Y'  && $processor['is_ach_accepted'] == 'Y'){ ?>
                    <?php foreach($fetchProcessor as $processor) { 
                        if($processor['is_assigned_to_all_product'] == 'Y'  && $processor['is_ach_accepted'] == 'Y' && !in_array($processor['id'],$idArr) && !$existAch){
                            $idArr[]  = $processor['id'];
                    $is_default = 'Standby';
                    if($processor['is_ach_accepted'] == 'Y'){
                        $is_default = 'ACH';
                    }
                    $payment_type = '-';
                    if($processor['is_ach_accepted'] == 'Y'){
                        $payment_type ='ACH';
                    }
                    $class_value = '';
                    if($gcount % 2 == 0){
                        $class_value = 'table_light_danger';
                        $gcount++;
                    }else{
                        $class_value = 'table_dark_danger';
                        $gcount++;
                    }
                ?>
                    <tr class="<?=$processor['global_accept_ach_status'] == 'Active' && $processor['is_assigned_to_all_product'] =='Y' ? $class_value : '' ?>">
                        <td class=""></td>
                        <td class="text-blue">
                            <?=$is_default?>
                        </td>
                        <td><?=$processor['name']?></td>
                        <td><?=$processor['merchant_id']?></td>
                        <td><?=date("m/d/Y",strtotime($processor['created_at']))?></td>
                        <td><?=$payment_type?></td>
                        <td class=""><div class="theme-form pr">
                            <select class="form-control has-value processor_status processor_status_ach" name="processor_status" id="processor_status_<?=$processor['id']?>" data-type="<?=$processor['type']?>" data-ach_status="ach_status" data-old_value="<?=$processor['global_accept_ach_status']?>">
                                <option value="Active" <?=($processor['global_accept_ach_status'] == 'Active') ? 'selected="selected"' : '' ?>>Active</option>
                                <option value="Inactive" <?=($processor['global_accept_ach_status'] == 'Inactive') ? 'selected="selected"' : '' ?>>Inactive</option>
                            </select>
                            <label>Select</label>
                        </div></td>
                        <td><?=$processor['description']?></td>
                        <td class="icons text-right"><a href="add_merchant_processor.php?type=<?=$processor["type"]?>&id=<?=$processor["id"]?>" target="_blank"><i class="fa fa-external-link" aria-hidden="true"></i></a>
                        </td>
                    </tr>
               <?php break; }  } } } ?>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
            <?php if($totalProcessor > 0 && !empty($fetchProcessor)) { ?>
                <td colspan="9">
                <?php echo $paginate_processor->links_html; ?>
                </td>
            <?php }else{ ?>
                <td colspan="9">
                    No rows found!
                </td>
            <?php } ?>
            </tr>
        </tfoot>
    </table>
</div>
<?php }else {  ?>
<form id="agent_merchant_frm_search" action="agent_merchant_processor.php" method="GET" class="sform">
    <input type="hidden" name="agent_id" id="merchant_agent_id" value="<?=!empty($agent_id) ? $agent_id : $_REQUEST['id']?>"/>
    <input type="hidden" name="is_ajaxed" id="agent_merchant_is_ajaxed" value="1"/>
    <input type="hidden" name="merchant_pages" id="merchant_pages" value="<?=!empty($per_page) ? $per_page : '' ?>"/>
</form>
<div id="agent_merchant_ajax_data"></div>
<hr />
<script type="text/javascript">
$(document).ready(function(e){
    agent_merchant_ajax_submit();
    dropdown_pagination('agent_merchant_ajax_data');
});
$(document).off('click','#add_new_processor');
$(document).on('click','#add_new_processor',function(e){
    var id = $("#new_processor").val();
    var agent_id = $("#merchant_agent_id").val();
    var type = $("#new_processor option:selected").attr('data-type');
    $.ajax({
        url:'ajax_agent_merchant_processor_change_status.php',
        data:{
            processor_id : id,
            agent_id : agent_id,
            operation:'insert',
            type:type,
        },
        type:'get',
        dataType:'json',
        beforeSend : function(e){
            $('#ajax_loader').show();
        },
        success:function(res){
            if(res.status == 'success'){
                setNotifySuccess(res.msg);
            }else if(res.status == 'error'){
                setNotifyError('Something went wrong.');
            }
            agent_merchant_ajax_submit();
        }
    });
    
});

$(document).off('click','#add_processor');
$(document).on('click','#add_processor',function(e){
    $("#new_merchant_processor").show();
});

$(document).off('change','.processor_status');
$(document).on('change','.processor_status',function(e){
    e.stopPropagation();
    var id = $(this).attr('id').replace('processor_status_','');
    var type = $(this).attr('data-type');
    var old_val = $(this).attr('data-old_value');
    var agent_id = $("#merchant_agent_id").val();
    var status = $(this).val();
    var global_ach_status = $(this).attr('data-ach_status');
    parent.swal({
        title: "<h4>Change Processor Status to <span class='text-blue'>"+status+"</span>: Are you sure?</h4>",
        html:'<div class="text-center fs14"><div class="d-inline  text-left"><label class="m-b-10 "><input name="downline" type="checkbox" value="downline" id="downline" class="js-switch" autofocus> <span class="p-l-10"> Apply to downline agents ? </span> </label><div class="clearfix"></div>' + '<label class="m-b-0" ><input name="loa" type="checkbox" value="loa" id="loa" class="js-switch"> <span class="p-l-10"> Apply to LOA agents?</span></label></div></div>',
        showCancelButton : true,
        confirmButtonText: "Confirm",
        cancelButtonText: "Cancel",
    }).then(function () {
        var $downline = '';
        if($("#downline").is(":checked")){
            var $downline = $("#downline").val();
        }
        var $loa = '';
        if($("#loa").is(":checked")){
            $loa = $("#loa").val();
        }
        $.ajax({
            url:'ajax_agent_merchant_processor_change_status.php',
            data:{
                processor_id : id,
                agent_id : agent_id,
                type : type,
                is_status : 'Y',
                status:status,
                downline:$downline,
                loa :$loa,
                global_ach_status:global_ach_status
            },
            type:'get',
            dataType:'json',
            beforeSend : function(e){
                $('#ajax_loader').show();
            },
            success:function(res){
                if(res.status === 'success'){
                    setNotifySuccess(res.msg);
                }else if(res.status === 'error'){
                    setNotifyError('Something went wrong.');
                }
                agent_merchant_ajax_submit();
            }
        });
    }, function (dismiss) {
        if(global_ach_status !== '' && global_ach_status!== undefined){
            $('.processor_status_ach').val(old_val);
            $('.processor_status_ach').selectpicker('refresh');
        }else{
            $('#processor_status_'+id).val(old_val);
            $('#processor_status_'+id).selectpicker('refresh');
        }
    });
});


$(document).off('click', '#agent_merchant_ajax_data ul.pagination li a');
$(document).on('click', '#agent_merchant_ajax_data ul.pagination li a', function (e) {
    e.preventDefault();
    $('#ajax_loader').show();
    $('#agent_merchant_ajax_data').hide();
    $.ajax({
        url: $(this).attr('href'),
        type: 'GET',
        success: function (res) {
            $('#ajax_loader').hide();
            $('#agent_merchant_ajax_data').html(res).show();
            common_select();
        }
    });
});
function agent_merchant_ajax_submit() {
    $('#ajax_loader').show();
    $('#agent_merchant_ajax_data').hide();
    $('#agent_merchant_is_ajaxed').val('1');
    var params = $('#agent_merchant_frm_search').serialize();
    $.ajax({
        url: $('#agent_merchant_frm_search').attr('action'),
        type: 'GET',
        data: params,
        success: function (res) {
            $('#ajax_loader').hide();
            $('#agent_merchant_ajax_data').html(res).show();
            common_select();
        }
    });
    return false;
}
function delete_processor(processor_id,type,name){
    var agent_id = $("#merchant_agent_id").val();
    parent.swal({
        title: "<h4>Delete Processor: Are you sure?</h4>",
        html:'<div class="text-center fs14"><div class="d-inline  text-left"><label class="m-b-10 "><input name="downline" type="checkbox" value="downline" id="downline" class="js-switch" autofocus> <span class="p-l-10"> Apply to downline agents ? </span> </label><div class="clearfix"></div>' + '<label class="m-b-0" ><input name="loa" type="checkbox" value="loa" id="loa" class="js-switch"> <span class="p-l-10"> Apply to LOA agents?</span></label></div></div>',
        showCancelButton : true,
        confirmButtonText: "Confirm",
        cancelButtonText: "Cancel",
    }).then(function () {
        var $downline = '';
        if($("#downline").is(":checked")){
            var $downline = $("#downline").val();
        }
        var $loa = '';
        if($("#loa").is(":checked")){
            $loa = $("#loa").val();
        }
        $.ajax({
            url:'ajax_agent_merchant_processor_change_status.php',
            data:{
                processor_id : processor_id,
                agent_id : agent_id,
                type : type,
                is_deleted : 'Y',
                downline:$downline,
                loa :$loa
            },
            type:'get',
            dataType:'json',
            beforeSend : function(e){
                $('#ajax_loader').show();
            },
            success:function(res){
                if(res.status === 'success'){
                    setNotifySuccess(name+" processor deleted successfully!");
                }else if(res.status === 'error'){
                    setNotifyError('Something went wrong.');
                }
                agent_merchant_ajax_submit();
            }
        });
    }, function (dismiss) {

    });
}
</script>
<?php } ?>