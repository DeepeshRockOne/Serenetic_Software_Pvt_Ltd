<?php if(!$is_ajaxed){ ?>
    <div class="table-responsive">
        <table class="<?=$table_class?> production_report_tbl" >
            <tbody id="agent_group_report">
                <tr>
                    <td class="br-n"></td>
                    <td>
                    <div class="row theme-form">
                        <form action="group_production_report.php" id="frm_search_group">
                            <input type="hidden" name="is_ajaxed_group" value="" id="is_ajaxed_group">
                            <input type="hidden" name="agent_id" value="<?=$agent_id?>" id="agent_id_group">
                        </form>
                    </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
<?php }
 if($is_ajaxed){ ?>
        <tr>
        <td class="br-n"></td>
        <td>
        <div class="row theme-form">
            <div class="col-sm-6 col-sm-offset-6">
                <div class="form-group height_auto m-b-5">
                        <select class="production_select_group form-control <?=!empty($group_id) ? 'has-value' : '' ?>" title="&nbsp;" name="group_id" id="se_select_group"  >
                        <option value="" selected hidden disabled></option>
                            <?php if(!empty($Group)):?>
                            <?php $i=0; ?>
                                <?php foreach($Group as $key2 => $val) : ?>
                                <?php $i++; ?>
                                    <option value="<?=$val['id']?>" <?=$val['id']==$group_id ? "selected" : ""?>><?=$val['name'].' ('.$val['rep_id'].')' ?></option>
                                <?php endforeach; ?>
                            <?php else:?>
                                <option value="" disabled>No downline group(s)</option>
                            <?php endif; ?>
                        </select>
                        <label for="group_id">Select Per Group</label>
                        <span class="error error_group">Please select Any Group.</span>
                </div>
            </div>
        </div>
        </td>
        <tr>
        <tr>
        <td class="br-n bg_dark_danger text-white fs16"><?=checkIsset($groups['name']) !='' ?  $groups['name'] .' ('.checkIsset($groups['rep_id']) .')' : '' ?></td>
        <td>
            <div class="row theme-form">
                <form action="agent_per_group_production_report.php" id="frm_search_group">
                    <input type="hidden" name="is_ajaxed_group" value="" id="is_ajaxed_group">
                    <input type="hidden" name="agent_id" value="<?=$agent_id?>" id="agent_id_group">
                    <input type="hidden" name="group_id" value="<?=$group_id?>" id="select_group">


                    <div class="col-sm-4">
                        <div class="form-group height_auto m-b-5">
                                <select class="form-control <?=!empty($join_range) ? 'has-value' : ''?>" name="join_range_group" title="&nbsp;" id="join_range_group">
                                <option value=""> </option>
                                <option value="range" <?=!empty($join_range) && $join_range=='range' ? 'selected="selected"' : '' ?>>Range</option>
                                <option value="exactly" <?=!empty($join_range) && $join_range=='exactly' ? 'selected="selected"' : '' ?> >Exactly</option>
                                <option value="before" <?=!empty($join_range) && $join_range=='before' ? 'selected="selected"' : '' ?>>Before</option>
                                <option value="after" <?=!empty($join_range) && $join_range=='after' ? 'selected="selected"' : '' ?>>After</option>
                                </select>
                            <label for="join_range_group">Quick Search:</label>
                            <span class="error error_range_group">Please select any option.</span>
                        </div>
                    </div>
                    <div id="dt_range_group" class="col-sm-6" style="<?=!empty($join_range) ? '' : 'display:none' ?>">
                        <div class="<?=!empty($join_range) && $join_range!='range' ? 'col-sm-12' : 'col-sm-6' ?>" id="from_date_group">
                            <div class="phone-addon">
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <div class="pr">
                                        <input type="text" name="group_fromdate" id="group_fromdate" value="<?=checkIsset($fromdate)?>" class="date_picker form-control <?=!empty($join_range) ? 'has-value' : '' ?>">
                                        <label for="fromdate_personal">From Date</label>
                                    </div>
                                </div>
                            </div>
                            <span class="error error_group_from_date">Please enter from date</span>
                        </div>
                        <div class="to_date col-sm-6" style="<?=!empty($join_range) && $join_range=='range'  ? '' : 'display:none' ?>">
                            <div class="phone-addon">
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <div class="pr">
                                        <input type="text" name="group_todate" id="group_todate"  value="<?=checkIsset($todate)?>" class="date_picker form-control <?=!empty($join_range) ? 'has-value' : '' ?>">
                                        <label for="todate_personal">To Date</label>
                                    </div>
                                </div>
                            </div>
                            <span class="error error_group_to_date">Please Enter to date.</span>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <button type="button" class="btn btn-action" id="search_group">Search</button>
                    </div>
                    <!-- </div> -->
                </form>
            </div>
        </td>
        </tr>
        <tr>
        <td>New Business Sales</td>
        <td><?= getPremiums($group_id, 'N',$searchArray);?></td>
        </tr>
        <tr>
        <td>Renewal Sales</td>
        <td><?= getPremiums($group_id, 'Y',$searchArray);?></td>
        </tr>
        <tr>
        <td>Total Sales</td>
        <td><?= getPremiums($group_id, '',$searchArray); ?></td>
        </tr>
        <tr>
        <td>Total Refund/Voids</td>
        <td class="text-red">($<?=getRefundedPremiums('',$searchArray,array('agent_id'=>$group_id,'type'=>'Organization','void'=>'true'))?>)
        </tr>
        <tr>
        <td>Total Chargebacks</td>
        <td class="text-red">($<?=getChargebackPremiums('',$searchArray,array('agent_id'=>$group_id,'type'=>'Organization'))?>)</td>
        </tr>
        <tr>
        <td>New Members Enrolled</td>
        <td><?=getUsers($group_id,'Customer',$searchArray)?></td>
        </tr>
        <tr>
            <td colspan="2">
            <p class="fw600 m-t-20 lato_font">Top Products (New Business Only)</p>
                <!-- <div class="table-responsive"> -->
                    <table class="<?=$table_class?>">
                    <thead>
                        <tr>
                        <th>Product Name</th>
                        <th>Premiums</th>
                        <th>Policies</th>
                        <th>New Members</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($product_res)) { ?>
                            <?php foreach($product_res as $product) { ?>
                        <tr>
                            <td><?php echo $product['product_name']; ?><?php echo ' (' . $product['product_code'] . ') '; ?></td>
                            <td><?php echo $product['total_sales'] != '' ? displayAmount($product['total_sales']) : '$0'; ?></td>
                            <td><?= $product['total_sold'] ?></td>
                            <td><?= $product['total_sold'] ?></td>
                        </tr>
                        <?php } }else{?>
                        <tr>
                            <td colspan="4" class="text-center">No record(s) found</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    </table>
                <!-- </div> -->
            </td>
        </tr>
<?php } else {?>
    <div class="text-center m-t-20"> <a href="javascript:void(0);" class="btn btn-action">Export</a> <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Close</a> </div>
<script type="text/javascript">
$(document).ready(function(){
    ajax_group_submit();
});
$(document).on('change','.production_select_group',function(){
    $("#select_group").val($(this).val());
    if($(this).val() != ''){
        ajax_group_submit();
    }
});
$(document).off("click","#search_group");
$(document).on("click","#search_group",function(){
    if($("#se_select_group").val() == '' || $("#se_select_group").val() == undefined ){
        $(".error_group").show();
    }else if($("#join_range_group").val() === undefined || $("#join_range_group").val() === ''){
        $(".error_range_group").show();
    }else if($("#join_range_group").val() !== 'range'){
        if($("#group_fromdate").val() === undefined || $("#group_fromdate").val() === ''){
            $(".error_group_from_date").show();
        }else{
            ajax_group_submit();
        }
    }else if($("#join_range_group").val() === 'range'){
        if($("#group_fromdate").val() === undefined || $("#group_fromdate").val() === ''){
            $(".error_group_from_date").show();
        }else{
            $(".error_group_from_date").hide();
        }
        if($("#group_todate").val() === undefined || $("#group_todate").val() === ''){
            $(".error_group_to_date").show();
        }else{
            $(".error_group_to_date").hide()
        }
    }

    if(($("#group_fromdate").val() !== undefined && $("#group_fromdate").val() !== '') && ($("#group_todate").val() !== undefined && $("#group_todate").val() !== '') && ($("#se_select_group").val() !== '' && $("#se_select_group").val() !== undefined)){
        ajax_group_submit();
    }
});

$(document).off("change","#se_select_group");
$(document).on("change","#se_select_group",function(){
    $(".error_group").hide();
    $("#select_group").val($(this).val());
});

$(document).off("change",'#join_range_group');
$(document).on("change",'#join_range_group',function(){
    $(".error").hide();
    $("#frm_search_group .date_picker").val("");
    if($(this).val() == 'range') {
        $('#dt_range_group').css({ display: 'inline-block' });
        $(".to_date").show();
        $("#from_date_group").addClass("col-sm-6").removeClass("col-sm-12");
    } else if($(this).val() == ''){
        $('#dt_range_group').css({ display: 'none' });
    }else{
        $('#dt_range_group').css({ display: 'inline-block' });
        $(".to_date").hide();
        $("#from_date_group").removeClass("col-sm-6").addClass("col-sm-12");
    }
});
function ajax_group_submit() {
    $('#ajax_loader').show();
    $(".error").hide();
    $('#agent_group_report').hide();
    $('#is_ajaxed_group').val('1');
    var params = $('#frm_search_group').serialize();
    $.ajax({
        url: $('#frm_search_group').attr('action'),
        type: 'GET',
        dataType:'html',
        data: params,
        success: function(res) {
            $('#ajax_loader').hide();
            $('#agent_group_report').html(res).show();
            common_select();
            $(".date_picker").datepicker({
                changeDay: true,
                changeMonth: true,
                changeYear: true
            });
            $(".error").hide();
        }
    });
    return false;
}
</script>
<?php } ?>