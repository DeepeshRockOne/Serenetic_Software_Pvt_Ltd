<?php if(!$is_ajaxed){ ?>
    <div class="table-responsive">
        <table class="<?=$table_class?> production_report_tbl" >
            <tbody id="agent_organization_report">
                <tr>
                    <td class="br-n"></td>
                    <td>
                    <div class="row theme-form">
                        <form action="agent_organization_production_report.php" id="frm_search_org">
                            <input type="hidden" name="is_ajaxed_org" value="" id="is_ajaxed_org">
                            <input type="hidden" name="agent_id" value="<?=$agent_id?>" id="agent_id_org">
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
                    <div class="row theme-form">
                    <form action="agent_organization_production_report.php" id="frm_search_org">
                        <input type="hidden" name="is_ajaxed_org" value="" id="is_ajaxed_org">
                        <input type="hidden" name="agent_id" value="<?=$agent_id?>" id="agent_id_org">
                        <div class="col-sm-4">
                            <div class="form-group height_auto m-b-5">
                                    <select class="form-control <?=!empty($join_range) ? 'has-value' : ''?>" name="join_range_org" title="&nbsp;" id="join_range_org">
                                    <option value=""> </option>
                                    <option value="range" <?=!empty($join_range) && $join_range=='range' ? 'selected="selected"' : '' ?>>Range</option>
                                    <option value="exactly" <?=!empty($join_range) && $join_range=='exactly' ? 'selected="selected"' : '' ?> >Exactly</option>
                                    <option value="before" <?=!empty($join_range) && $join_range=='before' ? 'selected="selected"' : '' ?>>Before</option>
                                    <option value="after" <?=!empty($join_range) && $join_range=='after' ? 'selected="selected"' : '' ?>>After</option>
                                    </select>
                                <label for="join_range_org">Quick Search:</label>
                                <span class="error error_range_org">Please select any option.</span>
                            </div>
                        </div>
                        <div id="dt_range_org" class="col-sm-6" style="<?=!empty($join_range) ? '' : 'display:none' ?>">
                            <div class=" phone-control-wrap form-group height_auto m-b-5">
                                <div class="phone-addon" id="from_date_org">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <div class="pr">
                                            <input type="text" name="org_fromdate" id="org_fromdate" value="<?=checkIsset($fromdate)?>" class="date_picker form-control <?=!empty($join_range) ? 'has-value' : '' ?>">
                                            <label for="fromdate_personal">From Date</label>
                                        </div>
                                    </div>
                                    <span class="error error_org_from_date">Please enter from date</span>
                                </div>
                                <div class="phone-addon to_date">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <div class="pr">
                                            <input type="text" name="org_todate" id="org_todate"  value="<?=checkIsset($todate)?>" class="date_picker form-control <?=!empty($join_range) ? 'has-value' : '' ?>">
                                            <label for="todate_personal">To Date</label>
                                        </div>
                                    </div>
                                    <span class="error error_org_to_date">Please Enter to date.</span>
                                </div>
                            </div>
                        </div>    
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-action btn-block m-b-10" id="org_search">Search</button>
                        </div>
                    </form>
                    </div>
                </td>
            </tr>
            <tr>
                <td>New Business Sales</td>
                <td><?= getOrganizationPremiums($agent_id, 'N', $searchArray); ?></td>
            </tr>
            <tr>
                <td>Renewal Sales</td>
                <td><?= getOrganizationPremiums($agent_id, 'Y', $searchArray); ?></td>
            </tr>
            <tr>
                <td>Total Sales</td>
                <td><?= getOrganizationPremiums($agent_id, '', $searchArray); ?></td>
            </tr>
            <tr>
                <td>Total Refund/Voids</td>
                <td class="text-red">(<?= getRefundedPremiums('', $searchArray,array("type"=>'Organization',"agent_id"=>$agent_id,"void"=>'true')) ?>)</td>
            </tr>
            <tr>
                <td>Total Chargebacks</td>
                <td class="text-red">(<?= getChargebackPremiums('', $searchArray,array("type"=>'Organization',"agent_id"=>$agent_id)) ?>)</td>
            </tr>
            <tr>
                <td>New Members Enrolled</td>
                <td><?= getOrganizationCustomer($agent_id, $searchArray); ?></td>
            </tr>
            <tr>
                <td>New Agents Contracted</td>
                <td><?= getOrganizationUsers($agent_id, 'Agent', $searchArray); ?></td>
            </tr>
            <tr>
                <td>New Groups Enrolled</td>
                <td><?= getOrganizationUsers($agent_id, 'Group', $searchArray) ?></td>
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
            <?php
                if (count($product_res) > 0) {
                    foreach ($product_res as $key => $rows) {
                        ?>
                        <tr>
                            <td><?php echo $rows['product_name']; ?><?php echo ' (' . $rows['product_code'] . ') '; ?></td>
                            <td><?php echo $rows['total_sales'] != '' ? displayAmount($rows['total_sales']) : '$0'; ?></td>
                            <td><?= $rows['total_sold'] ?></td>
                            <td><?= $rows['new_members'] ?></td>
                        </tr>
                <?php } } else { ?>
                    <tr>
                        <td colspan="5" class="text-center">No record(s) found</td>
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
        ajax_organization_submit();
    });

    $(document).off("click","#org_search");
    $(document).on("click","#org_search",function(){
        if($("#join_range_org").val() === undefined || $("#join_range_org").val() === ''){
            $(".error_range_org").show();
        }else if($("#join_range_org").val() !== 'range'){
            if($("#org_fromdate").val() === undefined || $("#org_fromdate").val() === ''){
                $(".error_org_from_date").show();
            }else{
                ajax_organization_submit();
            }
        }else if($("#join_range_org").val() === 'range'){
            if($("#org_fromdate").val() === undefined || $("#org_fromdate").val() === ''){
                $(".error_org_from_date").show();
            }else{
                $(".error_org_from_date").hide();
            }
            if($("#org_todate").val() === undefined || $("#org_todate").val() === ''){
                $(".error_org_to_date").show();
            }else{
                $(".error_org_to_date").hide()
            }
        }
        if(($("#org_fromdate").val() !== undefined && $("#org_fromdate").val() !== '') && ($("#org_todate").val() !== undefined && $("#org_todate").val() !== '')){
            ajax_organization_submit();
        }
    });
    
    $(document).off("change",'#join_range_org');
    $(document).on("change",'#join_range_org',function(){
        $(".error").hide();
        $("#frm_search_org .date_picker").val("");
        if($(this).val() == 'range') {
            $('#dt_range_org').css({ display: 'block' });
            $(".to_date").show();
            // $("#from_date_org").addClass("col-sm-6").removeClass("col-sm-12");
        } else if($(this).val() == ''){
            $('#dt_range_org').css({ display: 'none' });
        }else{
            $('#dt_range_org').css({ display: 'block' });
            $(".to_date").hide();
            // $("#from_date_org").removeClass("col-sm-6").addClass("col-sm-12");
        }
    });

    function ajax_organization_submit() {
        $('#ajax_loader').show();
        // $('#agent_organization_report').hide();
        $('#is_ajaxed_org').val('1');
        $(".error").hide();
        var params = $('#frm_search_org').serialize();
        $.ajax({
            url: $('#frm_search_org').attr('action'),
            type: 'GET',
            dataType :'html',
            data: params,
            success: function(res) {
                $('#ajax_loader').hide();
                $('#agent_organization_report').html(res).show();
                common_select();
                $('#join_range_org').selectpicker('refresh');
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