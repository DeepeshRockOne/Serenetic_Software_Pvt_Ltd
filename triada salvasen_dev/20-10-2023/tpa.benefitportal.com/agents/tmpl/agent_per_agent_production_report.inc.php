<?php if($is_ajaxed_per_agent){ ?>
    <div class="table-responsive">
        <table class="<?=$table_class?> production_report_tbl">
            <tbody>
            <tr>
                <td class="br-n"></td>
                <td>
                    <div class="theme-form">
                        <div class="form-group height_auto m-b-5">
                            <select class="production_select form-control <?=$agent_id_production == $agent_id ? : 'has-value'?>" title="&nbsp;" name="se_select_downline" id="se_select_downline"   
                                data-live-search="true">
                                <option value="" selected hidden disabled></option>
                                <?php if(!empty($levels)){ ?>
                                    <?php foreach($levels as $key1 => $value){ ?>
                                        <optgroup label="<?=$key1?>">
                                        <?php foreach($value as $key2 => $val){ ?>
                                            <option value="<?=$val['id']?>" <?=$agent_id==$val['id'] ? 'selected="selected"' : '' ?>><?=$val['rep_id'].' - '.$val['name']?></option>
                                        <?php } ?>
                                        </optgroup>
                                    <?php }
                                        }else{ ?>
                                        <option value="" disabled>No downline agent(s)</option>
                                    <?php } ?>
                                     <span class="error error_downline">Please select Any agent.</span>
                            </select>
                            <label>Select Agent</label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="br-n bg_dark_danger text-white fs16">
                    <?=checkIsset($agent_name['agency_name'])!='' ? ucfirst($agent_name['agency_name']).'/'.ucfirst($agent_name['name']) : ucfirst(checkIsset($agent_name['name'])) ?> 
                    <?=checkIsset($agent_name['rep_id']) != '' ? '('.$agent_name['rep_id'].')' : ''?>
                    <span class=" fs12 fw600"><?=checkIsset($agent_name['agent_coded_level'])?></span>
                </td>
                <td>
                    <div class="row theme-form">
                        <div id="date_range_per_agent" class="<?=!empty($join_range_per_agent) ? 'col-sm-3' : 'col-sm-12'?>">
                            <div class="form-group height_auto m-b-5">
                              <select class="form-control" name="join_range_per_agent" id="join_range_per_agent">
                                <option value="range" <?=checkIsset($join_range_per_agent) =='range' ? 'selected="selected"' : '' ?>>Range</option>
                                <option value="exactly" <?=checkIsset($join_range_per_agent) =='exactly' ? 'selected="selected"' : '' ?> >Exactly</option>
                                <option value="before" <?=checkIsset($join_range_per_agent) =='before' ? 'selected="selected"' : '' ?>>Before</option>
                                <option value="after" <?=checkIsset($join_range_per_agent) =='after' ? 'selected="selected"' : '' ?>>After</option>
                              </select>
                              <label>Select</label>
                              <span class="error error_range_per_agent">Please select any option.</span>
                            </div>
                        </div>

                        <div class="select_date_div_per_agent col-sm-7" style="<?=!empty($join_range_per_agent) ? '' : 'display:none' ?>">
                         <div class="phone-control-wrap">
                            <div class="phone-addon text-left">
                            <div class="form-group height_auto m-b-5">
                              
                                <div id="all_join_per_agent" style="<?=checkIsset($join_range_per_agent) !='range'  ? '' : 'display:none' ?>">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" name="added_date_per_agent" id="added_date_per_agent" value="<?=checkIsset($added_date_per_agent)?>" class="form-control date_picker" />
                                        <span class="error error_added_date_per_agent">Please select date.</span>
                                    </div>
                                </div>
                              
                                <div id="range_join_per_agent" style="<?=checkIsset($join_range_per_agent) =='range'  ? '' : 'display:none' ?>">
                                    <div class="phone-control-wrap">
                                          <div class="phone-addon">
                                            <div class="input-group">
                                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                              <div class="pr">
                                                   <input type="text" name="fromdate_per_agent" id="fromdate_per_agent" class="date_picker form-control" value="<?=checkIsset($fromdate_per_agent)?>">
                                                   <label>From Date</label>
                                               </div>
                                            </div>
                                             <span class="error error_per_agent_from_date">Please select from date.</span>
                                          </div>
                                          <div class="phone-addon">
                                            <div class="input-group">
                                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                              <div class="pr">
                                                   <input type="text" name="todate_per_agent" id="todate_per_agent" class="date_picker form-control" value="<?=checkIsset($todate_per_agent)?>">
                                                   <label>To Date</label>
                                               </div>
                                            </div>
                                             <span class="error error_per_agent_to_date">Please select to date.</span>
                                          </div>
                                    </div>
                                </div>
                            </div>
                            </div>

                        </div>
                      </div>
                      <div class="col-sm-2">
                            <div class="form-group height_auto m-b-5">
                                <a href="javascript:void(0);" class="btn btn-action btn-block" id="search_per_agent">Search</a>
                            </div>
                      </div>
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
                <td><?= getPremiums($agent_id, '',$searchArray); ?></td>
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
            </tbody>
        </table>
    </div>
    <p class="fw600 m-t-20 lato_font">Top Products (New Business Only)</p>
    <div class="table-responsive">
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
                <td><?=displayAmount($product['total_pre_price'])?></td>
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
    </div>
<?php } else { ?>

    <form action="agent_per_agent_production_report.php" id="frm_search_per_agent">
        <input type="hidden" name="viewPerAgentSales" id="viewPerAgentSales" value="<?=$viewPerAgentSales?>">
        <input type="hidden" name="is_ajaxed_per_agent" id="is_ajaxed_per_agent">
        <input type="hidden" name="agent_id" value="<?=$agent_id?>" id="agent_id_per_agent">
        <input type="hidden" name="select_downline" value="<?=checkIsset($select_downline)?>" id="select_downline">
        <input type="hidden" name="agent_id_production" value="<?=checkIsset($agent_id_production)?>" id="agent_id_production">
        <div id="agent_per_agent_report"></div>
    </form>

    <div class="text-center m-t-20"> 
        <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Close</a> 
    </div>
<script type="text/javascript">
    $(document).ready(function(){
        $(".date_picker").datepicker({
            changeDay: true,
            changeMonth: true,
            changeYear: true
        });
        ajax_per_agent_submit();
        $(".error").hide();
    });

    $(document).on('change','.production_select',function(){
        $("#select_downline").val($(this).val());
        if($(this).val() != ''){
            ajax_per_agent_submit();
        }
    });

    $(document).off("click","#search_per_agent");
    $(document).on("click","#search_per_agent",function(){
        $('#viewPerAgentSales').val("allSales");
        if($("#se_select_downline").val() == '' || $("#se_select_downline").val() == undefined ){
        $(".error_downline").show();
        }else if($("#join_range_per_agent").val() === undefined || $("#join_range_per_agent").val() === ''){
            $(".error_range_per_agent").show();
        }else if($("#join_range_per_agent").val() !== 'range'){
            if($("#added_date_per_agent").val() === undefined || $("#added_date_per_agent").val() === ''){
                $(".error_added_date_per_agent").show();
            }else{
                $("#select_downline").val($("#se_select_downline").val());
                ajax_per_agent_submit();
            }
        }else if($("#join_range_per_agent").val() === 'range'){
            if($("#fromdate_per_agent").val() === undefined || $("#fromdate_per_agent").val() === ''){
                $(".error_per_agent_from_date").show();
            }else{
                $(".error_per_agent_from_date").hide();
            }
            if($("#todate_per_agent").val() === undefined || $("#todate_per_agent").val() === ''){
                $(".error_per_agent_to_date").show();
            }else{
                $(".error_per_agent_to_date").hide()
            }
           
            if(($("#fromdate_per_agent").val() !== undefined && $("#fromdate_per_agent").val() !== '') && ($("#todate_per_agent").val() !== undefined && $("#todate_per_agent").val() !== '') && ($("#se_select_downline").val() !== '' && $("#se_select_downline").val() !== undefined)){
                $("#select_downline").val($("#se_select_downline").val());
                ajax_per_agent_submit();
            }
        }
    });

    $(document).off("change","#se_select_downline");
    $(document).on("change","#se_select_downline",function(){
        $(".error_downline").hide();
        $("#select_downline").val($(this).val());
    });
        


    $(document).off('change', '#join_range_per_agent');
    $(document).on('change', '#join_range_per_agent', function(e) {
        e.preventDefault();
         $("#frm_search_per_agent .date_picker").val("");
        if($(this).val() == ''){
          $('.select_date_div_per_agent').hide();
          $('#date_range_per_agent').removeClass('col-sm-3').addClass('col-sm-12');
        }else{
          $('#date_range_per_agent').removeClass('col-sm-12').addClass('col-sm-3');
          $('.select_date_div_per_agent').show();
          if ($(this).val() == 'range') {
            $('#range_join_per_agent').show();
            $('#all_join_per_agent').hide();
          } else {
            $('#range_join_per_agent').hide();
            $('#all_join_per_agent').show();
          }
        }
        common_select();
        fRefresh();
    });


    function ajax_per_agent_submit() {
        $('#ajax_loader').show();
        $(".error").hide();
        $('#agent_per_agent_report').hide();
        $('#is_ajaxed_per_agent').val('1');
        var params = $('#frm_search_per_agent').serialize();
        $.ajax({
            url: $('#frm_search_per_agent').attr('action'),
            type: 'GET',
            data: params,
            success: function(res) {
                $('#ajax_loader').hide();
                $('#agent_per_agent_report').html(res).show();
                perAgentTodayReport();
                $(".date_picker").datepicker({
                    changeDay: true,
                    changeMonth: true,
                    changeYear: true
                });
                common_select();
                fRefresh();
                $(".error").hide();
            }
        });
        return false;
    }

    function perAgentTodayReport(){
        var viewPerAgentSales = $("#viewPerAgentSales").val();
        var today = "<?=$today?>";
        if(viewPerAgentSales == "todaySales"){
          $('#join_range_per_agent').val('exactly').trigger('change');
          $("#added_date_per_agent").val(today);
        }
    }
</script>
<?php } ?>