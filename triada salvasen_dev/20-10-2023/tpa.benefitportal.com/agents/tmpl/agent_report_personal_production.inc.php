<?php if($is_ajaxed_personal){ ?>
    <div class="table-responsive">
        <table class="<?=$table_class?> production_report_tbl">
            <tbody>
            <tr>
                <td class="br-n"></td>
                <td>
                    <div class="row theme-form">
                        <div id="date_range_personal" class="<?=!empty($join_range_personal) ? 'col-sm-3' : 'col-sm-12'?>">
                            <div class="form-group height_auto m-b-5">
                              <select class="form-control" name="join_range_personal" id="join_range_personal">
                                <option value="range" <?=checkIsset($join_range_personal) =='range' ? 'selected="selected"' : '' ?>>Range</option>
                                <option value="exactly" <?=checkIsset($join_range_personal) =='exactly' ? 'selected="selected"' : '' ?> >Exactly</option>
                                <option value="before" <?=checkIsset($join_range_personal) =='before' ? 'selected="selected"' : '' ?>>Before</option>
                                <option value="after" <?=checkIsset($join_range_personal) =='after' ? 'selected="selected"' : '' ?>>After</option>
                              </select>
                              <label>Select</label>
                              <span class="error error_range_personal">Please select any option.</span>
                            </div>
                        </div>

                        <div class="select_date_div_personal col-sm-7" style="<?=!empty($join_range_personal) ? '' : 'display:none' ?>">
                         <div class="phone-control-wrap">
                            <div class="phone-addon text-left">
                            <div class="form-group height_auto m-b-5">
                              
                                <div id="all_join_personal" style="<?=checkIsset($join_range_personal) !='range'  ? '' : 'display:none' ?>">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" name="added_date_personal" id="added_date_personal" value="<?=checkIsset($added_date_personal)?>" class="form-control date_picker" />
                                        <span class="error error_added_date_personal">Please select date.</span>
                                    </div>
                                </div>
                              
                                <div id="range_join_personal" style="<?=checkIsset($join_range_personal) =='range'  ? '' : 'display:none' ?>">
                                    <div class="phone-control-wrap">
                                          <div class="phone-addon">
                                            <div class="input-group">
                                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                              <div class="pr">
                                                   <input type="text" name="fromdate_personal" id="fromdate_personal" class="date_picker form-control" value="<?=checkIsset($fromdate_personal)?>">
                                                   <label>From Date</label>
                                                </div>
                                            </div>
                                             <span class="error error_pr_from_date">Please select from date.</span>
                                          </div>
                                          <div class="phone-addon">
                                            <div class="input-group">
                                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                              <div class="pr">
                                                   <input type="text" name="todate_personal" id="todate_personal" class="date_picker form-control" value="<?=checkIsset($todate_personal)?>">
                                                   <label>To Date</label>
                                                </div>
                                            </div>
                                             <span class="error error_todate_personal">Please select to date.</span>
                                          </div>
                                    </div>
                                </div>
                            </div>
                            </div>

                        </div>
                      </div>
                      <div class="col-sm-2">
                            <div class="form-group height_auto m-b-5">
                                <a href="javascript:void(0);" class="btn btn-action btn-block" id="search_personal">Search</a>
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

    <form action="agent_report_personal_production.php" id="frm_search_personal">
        <input type="hidden" name="viewPersonalSales" id="viewPersonalSales" value="<?=$viewPersonalSales?>">
        <input type="hidden" name="is_ajaxed_personal" id="is_ajaxed_personal">
        <input type="hidden" name="agent_id" value="<?=$agent_id?>" id="agent_id_personal">
        <div id="agent_personal_report"></div>
       
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
        ajax_personal_submit();
        $(".error").hide();
    });

    $(document).off("click","#search_personal");
    $(document).on("click","#search_personal",function(){
        $('#viewPersonalSales').val("allSales");
        if($("#join_range_personal").val() === undefined || $("#join_range_personal").val() === ''){
            $(".error_range_personal").show();
        }else if($("#join_range_personal").val() !== 'range'){
            if($("#added_date_personal").val() === undefined || $("#added_date_personal").val() === ''){
                $(".error_added_date_personal").show();
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
           
            if(($("#fromdate_personal").val() !== undefined && $("#fromdate_personal").val() !== '') && ($("#todate_personal").val() !== undefined && $("#todate_personal").val() !== '')){
                ajax_personal_submit();
            }
        }
    });
    


    $(document).off('change', '#join_range_personal');
    $(document).on('change', '#join_range_personal', function(e) {
        e.preventDefault();
         $("#frm_search_personal .date_picker").val("");
        if($(this).val() == ''){
          $('.select_date_div_personal').hide();
          $('#date_range_personal').removeClass('col-sm-3').addClass('col-sm-12');
        }else{
          $('#date_range_personal').removeClass('col-sm-12').addClass('col-sm-3');
          $('.select_date_div_personal').show();
          if ($(this).val() == 'range') {
            $('#range_join_personal').show();
            $('#all_join_personal').hide();
          } else {
            $('#range_join_personal').hide();
            $('#all_join_personal').show();
          }
        }
        common_select();
        fRefresh();
    });


    function ajax_personal_submit() {
        $('#ajax_loader').show();
        $(".error").hide();
        $('#agent_personal_report').hide();
        $('#is_ajaxed_personal').val('1');
        var params = $('#frm_search_personal').serialize();
        $.ajax({
            url: $('#frm_search_personal').attr('action'),
            type: 'GET',
            data: params,
            success: function(res) {
                $('#ajax_loader').hide();
                $('#agent_personal_report').html(res).show();
                personalTodayReport();
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

    function personalTodayReport(){
        var viewPersonalSales = $("#viewPersonalSales").val();
        // alert(O);
        var today = "<?=$today?>";
        if(viewPersonalSales == "todaySales"){
          $('#join_range_personal').val('exactly').trigger('change');
          $("#added_date_personal").val(today);
        }
    }
</script>
<?php } ?>