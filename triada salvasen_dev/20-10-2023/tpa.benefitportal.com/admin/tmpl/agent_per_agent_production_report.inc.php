<?php if(!$is_ajaxed){ ?>
    <div class="table-responsive">
        <table class="<?=$table_class?> production_report_tbl" >
            <tbody id="agent_production_report">
                <tr>
                    <td class="br-n"></td>
                    <td>
                    <div class="row theme-form">
                        <form action="agent_per_agent_production_report.php" id="frm_search_production">
                            <input type="hidden" name="is_ajaxed_production" value="" id="is_ajaxed_production">
                            <input type="hidden" name="agent_id" value="<?=$agent_id?>" id="agent_id_per">
                        </form>
                    </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
<?php }
if($is_ajaxed) { ?>
            <tr>
            <td class="br-n"></td>
            <td>
                <div class="theme-form">
                        <div class="form-group height_auto m-b-5">
                            <div class="select_group min-w175">
                                <select class="production_select form-control <?=$agent_id_production == $agent_id ? : 'has-value'?>" title="&nbsp;" name="se_select_downline" id="se_select_downline">
                                <option value="" selected hidden disabled></option>
                                    <?php if(!empty($levels)):?>
                                    <?php foreach($levels as $key1 => $value) : ?>
                                        <optgroup label="<?=$key1?>">
                                        <?php foreach($value as $key2 => $val) : ?>
                                            <option value="<?=$val['id']?>" <?=$agent_id==$val['id'] ? 'selected="selected"' : '' ?>><?=$val['name'].' ('.$val['rep_id'].')'?></option>
                                        <?php endforeach; ?>
                                        </optgroup>
                                    <?php endforeach; ?>
                                    <?php else:?>
                                        <option value="" disabled>No downline agent(s)</option>
                                    <?php endif; ?>
                                </select>
                                <label for="se_select_downline">Select Downline Agent</label>
                                <span class="error error_downline">Please select Any agent.</span>
                            </div>
                        </div>
                </div>
            </td>
            </tr>
            <tr>
            <td class="br-n bg_dark_danger text-white fs16">
                <?=checkIsset($agent_name['agency_name'])!='' ? ucfirst($agent_name['agency_name']).'/'.ucfirst($agent_name['name']) : ucfirst(checkIsset($agent_name['name'])) ?> <?= checkIsset($agent_name['rep_id']) ? "(" .checkIsset($agent_name['rep_id']) . ")" : ""?>
                <span class=" fs12 fw600"><?=checkIsset($agent_name['agent_coded_level'])?></span>
            </td>
            <td>
                <div class="row theme-form">
                    <form action="agent_per_agent_production_report.php" id="frm_search_production">
                        <input type="hidden" name="is_ajaxed_production" value="" id="is_ajaxed_production">
                        <input type="hidden" name="agent_id" value="<?=$agent_id?>" id="agent_id_per">
                        <input type="hidden" name="select_downline" value="<?=$select_downline?>" id="select_downline">
                        <input type="hidden" name="agent_id_production" value="<?=$agent_id_production?>" id="agent_id_production">
                        <div class="col-sm-4">
                            <div class="form-group height_auto m-b-5">
                                    <select class="form-control <?=!empty($join_range) ? 'has-value' : ''?>" name="join_range_per_per" title="&nbsp;" id="join_range_per_per">
                                    <option value=""> </option>
                                    <option value="range" <?=!empty($join_range) && $join_range=='range' ? 'selected="selected"' : '' ?>>Range</option>
                                    <option value="exactly" <?=!empty($join_range) && $join_range=='exactly' ? 'selected="selected"' : '' ?> >Exactly</option>
                                    <option value="before" <?=!empty($join_range) && $join_range=='before' ? 'selected="selected"' : '' ?>>Before</option>
                                    <option value="after" <?=!empty($join_range) && $join_range=='after' ? 'selected="selected"' : '' ?>>After</option>
                                    </select>
                                <label for="join_range_per_per">Quick Search:</label>
                                <span class="error error_range_per_per">Please select any option.</span>
                            </div>
                        </div>
                        <div id="dt_range_per_per" class="col-sm-6" style="<?=!empty($join_range) ? '' : 'display:none' ?>">
                            <div class=" phone-control-wrap form-group height_auto m-b-5">
                                <div class="phone-addon" id="from_date_per_per">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <div class="pr">
                                            <input type="text" name="per_per_fromdate" id="per_per_fromdate" value="<?=checkIsset($fromdate)?>" class="date_picker form-control <?=!empty($join_range) ? 'has-value' : '' ?>">
                                            <label for="fromdate_personal">From Date</label>
                                        </div>
                                    </div>
                                    <span class="error error_per_per_from_date">Please enter from date</span>
                                </div>
                                <div class="phone-addon to_date">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <div class="pr">
                                            <input type="text" name="per_per_todate" id="per_per_todate"  value="<?=checkIsset($todate)?>" class="date_picker form-control <?=!empty($join_range) ? 'has-value' : '' ?>">
                                            <label for="todate_personal">To Date</label>
                                        </div>
                                    </div>
                                    <span class="error error_per_per_to_date">Please Enter to date.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-action btn-block m-b-10" id="search_production_btn">Search</button>
                        </div>
                    </form>
                </div>
            </td>
            </td>
            </tr>
            <tr>
            <td>New Business Sales</td>
            <td><?= getPremiums($agent_id, 'N',$searchArray);?></td>
            </tr>
            <tr>
            <td>Renewal Sales</td>
            <td><?= getPremiums($agent_id, 'Y',$searchArray);?></td>
            </tr>
            <tr>
            <td>Total Sales</td>
            <td><?=  getPremiums($agent_id, '',$searchArray); ?></td>
            </tr>
            <tr>
            <td>Total Refund/Voids</td>
            <td class="text-red">(<?=getRefundedPremiums('',$searchArray,array('agent_id'=>$agent_id,'type'=>'Organization','void'=>'true'))?>)</td>
            </tr>
            <tr>
            <td>Total Chargebacks</td>
            <td class="text-red">(<?=getChargebackPremiums('',$searchArray,array('agent_id'=>$agent_id,'type'=>'Organization'))?>)</td>
            </tr>
            <tr>
            <td>New Members Enrolled</td>
            <td><?=getUsers($agent_id,'Customer',$searchArray)?></td>
            </tr>
            <tr>
            <td>New Agents Contracted</td>
            <td><?=getUsers($agent_id, 'Agent',$searchArray)?></td>
            </tr>
            <tr>
            <td>New Groups Enrolled</td>
            <td><?=getUsers($agent_id, 'Group',$searchArray)?></td>
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
                            <td><?= $product['new_members'] ?></td>
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
<?php } else { ?> 
    <div class="text-center m-t-20"><a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Close</a> </div>
<script type="text/javascript">
$(document).ready(function(){
    ajax_production_submit();
});
$(document).on('change','.production_select',function(){
    $("#select_downline").val($(this).val());
    if($(this).val() != ''){
        ajax_production_submit();
    }
});
$(document).off("click","#search_production_btn");
$(document).on("click","#search_production_btn",function(){
    if($("#se_select_downline").val() == '' || $("#se_select_downline").val() == undefined ){
        $(".error_downline").show();
    }else if($("#join_range_per_per").val() === undefined || $("#join_range_per_per").val() === ''){
        $(".error_range_per_per").show();
    }else if($("#join_range_per_per").val() !== 'range'){
        if($("#per_per_fromdate").val() === undefined || $("#per_per_fromdate").val() === ''){
            $(".error_per_per_from_date").show();
        }else{
            ajax_production_submit();
        }
    }else if($("#join_range_per_per").val() === 'range'){
        if($("#per_per_fromdate").val() === undefined || $("#per_per_fromdate").val() === ''){
            $(".error_per_per_from_date").show();
        }else{
            $(".error_per_per_from_date").hide();
        }
        if($("#per_per_todate").val() === undefined || $("#per_per_todate").val() === ''){
            $(".error_per_per_to_date").show();
        }else{
            $(".error_per_per_to_date").hide()
        }
    }
    if(($("#per_per_fromdate").val() !== undefined && $("#per_per_fromdate").val() !== '') && ($("#per_per_todate").val() !== undefined && $("#per_per_todate").val() !== '') && ($("#se_select_downline").val() !== '' && $("#se_select_downline").val() !== undefined)){
        ajax_production_submit();
    }
});

$(document).off("change","#se_select_downline");
$(document).on("change","#se_select_downline",function(){
    $(".error_downline").hide();
    $("#select_downline").val($(this).val());
});

$(document).off("change",'#join_range_per_per');
$(document).on("change",'#join_range_per_per',function(){
    $(".error").hide();
    $("#frm_search_per_per .date_picker").val("");
    if($(this).val() == 'range') {
        $('#dt_range_per_per').css({ display: 'block' });
        $(".to_date").show();
        // $("#from_date_per_per").addClass("col-sm-6").removeClass("col-sm-12");
    } else if($(this).val() == ''){
        $('#dt_range_per_per').css({ display: 'none' });
    }else{
        $('#dt_range_per_per').css({ display: 'block' });
        $(".to_date").hide();
        // $("#from_date_per_per").removeClass("col-sm-6").addClass("col-sm-12");
    }
});

function ajax_production_submit() {
    $('#ajax_loader').show();
    $(".error").hide();
    // $('#agent_production_report').hide();
    $('#is_ajaxed_production').val('1');
    var params = $('#frm_search_production').serialize();
    $.ajax({
        url: $('#frm_search_production').attr('action'),
        type: 'GET',
        dataType : 'html',
        data: params,
        success: function(res) {
            $('#ajax_loader').hide();
            $('#agent_production_report').html(res).show();
            common_select();
            $(".error").hide();
            $(".date_picker").datepicker({
                changeDay: true,
                changeMonth: true,
                changeYear: true
            });
        }
    });
    return false;
}
</script>
<?php } ?>