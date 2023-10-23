<?php if($is_ajaxed_group){ ?>
    <div class="table-responsive">
        <table class="<?=$table_class?> production_report_tbl">
            <tbody>
            <tr>
                <td class="br-n"></td>
                <td>
                    <div class="theme-form">
                        <div class="form-group height_auto m-b-5">
                            <select class="production_select_group form-control <?=!empty($group_id) ? 'has-value' : '' ?>" title="&nbsp;" name="group_id" id="se_select_group" data-live-search="true">
                                <option value="" selected hidden disabled></option>
                                <?php if(!empty($group)){ ?>
                                        <?php foreach($group as $key => $val){ ?>
                                        <option value="<?=$val['id']?>" <?=$val['id']==$group_id ? "selected" : ""?>><?=$val['rep_id'].' - '.$val['business_name']?></option>
                                        <?php }
                                        }else{ ?>
                                        <option value="" disabled>No downline group(s)</option>
                                    <?php } ?>
                                     <span class="error error_downline">Please select Any Group.</span>
                            </select>
                            <label>Select Group</label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="br-n bg_dark_danger text-white fs16">
                    <?=checkIsset($groups['name']) !='' ?  $groups['name'] .' ('.checkIsset($groups['rep_id']) .')' : '' ?>
                </td>
                <td>
                    <div class="row theme-form">
                        <div id="date_range_group" class="<?=!empty($join_range_group) ? 'col-sm-3' : 'col-sm-12'?>">
                            <div class="form-group height_auto m-b-5">
                              <select class="form-control" name="join_range_group" id="join_range_group">
                                <option value="range" <?=checkIsset($join_range_group) =='range' ? 'selected="selected"' : '' ?>>Range</option>
                                <option value="exactly" <?=checkIsset($join_range_group) =='exactly' ? 'selected="selected"' : '' ?> >Exactly</option>
                                <option value="before" <?=checkIsset($join_range_group) =='before' ? 'selected="selected"' : '' ?>>Before</option>
                                <option value="after" <?=checkIsset($join_range_group) =='after' ? 'selected="selected"' : '' ?>>After</option>
                              </select>
                              <label>Select</label>
                              <span class="error error_range_group">Please select any option.</span>
                            </div>
                        </div>

                        <div class="select_date_div_group col-sm-7" style="<?=!empty($join_range_group) ? '' : 'display:none' ?>">
                         <div class="phone-control-wrap">
                            <div class="phone-addon text-left">
                            <div class="form-group height_auto m-b-5">
                              
                                <div id="all_join_group" style="<?=checkIsset($join_range_group) !='range'  ? '' : 'display:none' ?>">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" name="added_date_group" id="added_date_group" value="<?=checkIsset($added_date_group)?>" class="form-control date_picker" />
                                        <span class="error error_added_date_group">Please select date.</span>
                                    </div>
                                </div>
                              
                                <div id="range_join_group" style="<?=checkIsset($join_range_group) =='range'  ? '' : 'display:none' ?>">
                                    <div class="phone-control-wrap">
                                          <div class="phone-addon">
                                            <div class="input-group">
                                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                              <div class="pr">
                                                   <input type="text" name="fromdate_group" id="fromdate_group" class="date_picker form-control" value="<?=checkIsset($fromdate_group)?>">
                                                   <label>From Date</label>
                                                </div>
                                            </div>
                                             <span class="error error_group_from_date">Please select from date.</span>
                                          </div>
                                          <div class="phone-addon">
                                            <div class="input-group">
                                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                              <div class="pr">
                                                   <input type="text" name="todate_group" id="todate_group" class="date_picker form-control" value="<?=checkIsset($todate_group)?>">
                                                   <label>To Date</label>
                                                </div>
                                            </div>
                                             <span class="error error_group_to_date">Please select to date.</span>
                                          </div>
                                    </div>
                                </div>
                            </div>
                            </div>

                        </div>
                      </div>
                      <div class="col-sm-2">
                            <div class="form-group height_auto m-b-5">
                                <a href="javascript:void(0);" class="btn btn-action btn-block" id="search_group">Search</a>
                            </div>
                       </div>
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
                <td class="text-red">(<?=getRefundedPremiums('',$searchArray,array('agent_id'=>$group_id,'type'=>'Organization','void'=>'true'))?>)</td>
            </tr>
            <tr>
                <td>Total Chargebacks</td>
                <td class="text-red">(<?=getChargebackPremiums('',$searchArray,array('agent_id'=>$group_id,'type'=>'Organization'))?>)</td>
            </tr>
            <tr>
                <td>New Members Enrolled</td>
                <td><?=getUsers($group_id,'Customer',$searchArray)?></td>
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

    <form action="agent_per_group_production_report.php" id="frm_search_group">
        <input type="hidden" name="viewGroupSales" id="viewGroupSales" value="<?=$viewGroupSales?>">
        <input type="hidden" name="is_ajaxed_group" id="is_ajaxed_group">
        <input type="hidden" name="agent_id" value="<?=$agent_id?>" id="agent_id_group">
        <input type="hidden" name="group_id" value="<?=$group_id?>" id="select_group">
        <div id="agent_group_report"></div>
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
        ajax_group_submit();
        $(".error").hide();
    });

    $(document).on('change','.production_select_group',function(){
        $("#select_group").val($(this).val());
        if($(this).val() != ''){
            ajax_group_submit();
        }
    });

    $(document).off("click","#search_group");
    $(document).on("click","#search_group",function(){
        $('#viewGroupSales').val("allSales");
        if($("#se_select_group").val() == '' || $("#se_select_group").val() == undefined ){
        $(".error_downline").show();
        }else if($("#join_range_group").val() === undefined || $("#join_range_group").val() === ''){
            $(".error_range_group").show();
        }else if($("#join_range_group").val() !== 'range'){
            if($("#added_date_group").val() === undefined || $("#added_date_group").val() === ''){
                $(".error_added_date_group").show();
            }else{
                ajax_group_submit();
            }
        }else if($("#join_range_group").val() === 'range'){
            if($("#fromdate_group").val() === undefined || $("#fromdate_group").val() === ''){
                $(".error_group_from_date").show();
            }else{
                $(".error_group_from_date").hide();
            }
            if($("#todate_group").val() === undefined || $("#todate_group").val() === ''){
                $(".error_group_to_date").show();
            }else{
                $(".error_group_to_date").hide()
            }
           
            if(($("#fromdate_group").val() !== undefined && $("#fromdate_group").val() !== '') && ($("#todate_group").val() !== undefined && $("#todate_group").val() !== '') && ($("#se_select_group").val() !== '' && $("#se_select_group").val() !== undefined)){
                ajax_group_submit();
            }
        }
    });

    $(document).off("change","#se_select_group");
    $(document).on("change","#se_select_group",function(){
        $(".error_downline").hide();
        $("#select_group").val($(this).val());
    });
        


    $(document).off('change', '#join_range_group');
    $(document).on('change', '#join_range_group', function(e) {
        e.preventDefault();
         $("#frm_search_group .date_picker").val("");
        if($(this).val() == ''){
          $('.select_date_div_group').hide();
          $('#date_range_group').removeClass('col-sm-3').addClass('col-sm-12');
        }else{
          $('#date_range_group').removeClass('col-sm-12').addClass('col-sm-3');
          $('.select_date_div_group').show();
          if ($(this).val() == 'range') {
            $('#range_join_group').show();
            $('#all_join_group').hide();
          } else {
            $('#range_join_group').hide();
            $('#all_join_group').show();
          }
        }
        common_select();
        fRefresh();
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
            data: params,
            success: function(res) {
                $('#ajax_loader').hide();
                $('#agent_group_report').html(res).show();
                groupTodayReport();
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

    function groupTodayReport(){
        var viewGroupSales = $("#viewGroupSales").val();
        var today = "<?=$today?>";
        if(viewGroupSales == "todaySales"){
          $('#join_range_group').val('exactly').trigger('change');
          $("#added_date_group").val(today);
        }
    }
</script>
<?php } ?>