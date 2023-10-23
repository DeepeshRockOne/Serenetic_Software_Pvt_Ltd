<?php if(!$is_ajaxed){ ?>
    <div class="table-responsive">
        <table class="<?=$table_class?> production_report_tbl" >
            <tbody id="agent_personal_report">
                <tr>
                    <td class="br-n"></td>
                    <td>
                    <div class="row theme-form">
                        <form action="agent_report_personal_production.php" id="frm_search_personal">
                            <input type="hidden" name="is_ajaxed_personal" value="" id="is_ajaxed_personal">
                            <input type="hidden" name="agent_id" value="<?=$agent_id?>" id="agent_id_personal">
                        </form>
                    </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
<?php }
if($is_ajaxed){
    ?>
                <tr>
                    <td class="br-n"></td>
                    <td>
                        <div class="row theme-form">
                        <form action="agent_report_personal_production.php" id="frm_search_personal">
                            <input type="hidden" name="is_ajaxed_personal" value="" id="is_ajaxed_personal">
                            <input type="hidden" name="agent_id" value="<?=$agent_id?>" id="agent_id_personal">
                            <div class="col-sm-4">
                                <div class="form-group height_auto m-b-5">
                                        <select class="form-control <?=!empty($join_range) ? 'has-value' : ''?>" name="join_range_personal" title="&nbsp;" id="join_range_personal">
                                        <option value=""> </option>
                                        <option value="range" <?=!empty($join_range) && $join_range=='range' ? 'selected="selected"' : '' ?>>Range</option>
                                        <option value="exactly" <?=!empty($join_range) && $join_range=='exactly' ? 'selected="selected"' : '' ?> >Exactly</option>
                                        <option value="before" <?=!empty($join_range) && $join_range=='before' ? 'selected="selected"' : '' ?>>Before</option>
                                        <option value="after" <?=!empty($join_range) && $join_range=='after' ? 'selected="selected"' : '' ?>>After</option>
                                        </select>
                                    <label for="join_range_personal">Quick Search:</label>
                                    <span class="error error_range_personal">Please select any option.</span>
                                </div>
                            </div>
                            <div id="dt_range_personal" class="col-sm-6"  style="<?=!empty($join_range) ? '' : 'display:none' ?>">
                                <div class=" phone-control-wrap form-group height_auto m-b-5">
                                    <div class="phone-addon " id="from_date_personal">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <div class="pr">
                                                <input type="text" name="fromdate_personal" id="fromdate_personal" value="<?=checkIsset($fromdate)?>" class="date_picker form-control <?=!empty($join_range) ? 'has-value' : '' ?>">
                                                <label for="fromdate_personal">From Date</label>
                                            </div>    
                                        </div>
                                        <span class="error error_pr_from_date">Please select from date.</span>
                                    </div>
                                    <div class="phone-addon to_date_personal">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <div class="pr">
                                                <input type="text" name="todate_personal" id="todate_personal" value="<?=checkIsset($todate)?>" class="date_picker form-control <?=!empty($join_range) ? 'has-value' : '' ?>">
                                                <label for="todate_personal">To Date</label>
                                            </div>    
                                        </div>
                                        <span class="error error_pr_to_date">Please select to date.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-action btn-block m-b-10" id="search_personal">Search</button>
                            </div>
                        </form>
                        </div>
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
                <td class="text-red">(<?=getRefundedPremiums('',$searchArray,array("agent_id"=>$agent_id,'void'=>'true'))?>)</td>
                </tr>
                <tr>
                <td>Total Chargebacks</td>
                <td class="text-red">(<?=getChargebackPremiums('',$searchArray,array("agent_id"=>$agent_id))?>)</td>
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
                    </td>
                </tr>
<?php } else { ?>
<div class="text-center m-t-20"><a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Close</a> </div>
<script type="text/javascript">
    $(document).ready(function(){
        ajax_personal_submit();
    });

    $(document).off("click","#search_personal");
    $(document).on("click","#search_personal",function(){
        if($("#join_range_personal").val() === undefined || $("#join_range_personal").val() === ''){
            $(".error_range_personal").show();
        }else if($("#join_range_personal").val() !== 'range'){
            if($("#fromdate_personal").val() === undefined || $("#fromdate_personal").val() === ''){
                $(".error_pr_from_date").show();
            }else{
                ajax_personal_submit();
            }
        }else if($("#join_range_personal").val() === 'range'){
            if($("#fromdate_personal").val() === undefined || $("#fromdate_personal").val() === ''){
                $(".error_pr_from_date").show();
            }else{
                $(".error_pr_from_date").hide();
            }
            if($("#todate_personal").val() === undefined || $("#todate_personal").val() === ''){
                $(".error_pr_to_date").show();
            }else{
                $(".error_pr_to_date").hide()
            }
        }
        if(($("#fromdate_personal").val() !== undefined && $("#fromdate_personal").val() !== '') && ($("#todate_personal").val() !== undefined && $("#todate_personal").val() !== '')){
            ajax_personal_submit();
        }
    });
    

    $(document).off("change",'#join_range_personal');
    $(document).on("change",'#join_range_personal',function(){
        $(".error").hide();
        $("#frm_search_personal .date_picker").val("");
        if($('#join_range_personal').val() == 'range') {
            $('#dt_range_personal').css({ display: 'block' });
            $(".to_date_personal").show();
            // $("#from_date_personal").addClass("col-sm-6").removeClass("col-sm-12");
        } else if($('#join_range_personal').val() == ''){
            $('#dt_range_personal').css({ display: 'none' });
        }else{
            $('#dt_range_personal').css({ display: 'block' });
            $(".to_date_personal").hide();
            // $("#from_date_personal").removeClass("col-sm-6").addClass("col-sm-12");
        }
    });

/* updated range code start */
            $(document).off('change', '#join_range');
            $(document).on('change', '#join_range', function(e) {
            e.preventDefault();
            if($(this).val() == ''){
              $('.select_date_div').hide();
              $('#date_range').removeClass('col-sm-3').addClass('col-sm-12');
            }else{
              $('#date_range').removeClass('col-sm-12').addClass('col-sm-3');
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
/* updated range code end */

    function ajax_personal_submit() {
        $('#ajax_loader').show();
        $(".error").hide();
        // $('#agent_personal_report').hide();
        $('#is_ajaxed_personal').val('1');
        var params = $('#frm_search_personal').serialize();
        $.ajax({
            url: $('#frm_search_personal').attr('action'),
            type: 'GET',
            dataType:'html',
            data: params,
            success: function(res) {
                $('#ajax_loader').hide();
                $('#agent_personal_report').html(res);
                common_select();
                $('#join_range_personal').selectpicker('refresh');
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