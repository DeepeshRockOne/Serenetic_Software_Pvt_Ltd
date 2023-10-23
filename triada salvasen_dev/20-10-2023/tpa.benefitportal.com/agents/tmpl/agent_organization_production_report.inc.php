<?php if($is_ajaxed_org){ ?>
    <div class="table-responsive">
        <table class="<?=$table_class?> production_report_tbl">
            <tbody>
            <tr>
                <td class="br-n"></td>
                <td>
                    <div class="row theme-form">
                        <div id="date_range_org" class="<?=!empty($join_range_org) ? 'col-sm-3' : 'col-sm-12'?>">
                            <div class="form-group height_auto m-b-5">
                              <select class="form-control" name="join_range_org" id="join_range_org">
                                <option value="range" <?=checkIsset($join_range_org) =='range' ? 'selected="selected"' : '' ?>>Range</option>
                                <option value="exactly" <?=checkIsset($join_range_org) =='exactly' ? 'selected="selected"' : '' ?> >Exactly</option>
                                <option value="before" <?=checkIsset($join_range_org) =='before' ? 'selected="selected"' : '' ?>>Before</option>
                                <option value="after" <?=checkIsset($join_range_org) =='after' ? 'selected="selected"' : '' ?>>After</option>
                              </select>
                              <label>Select</label>
                              <span class="error error_range_org">Please select any option.</span>
                            </div>
                        </div>

                        <div class="select_date_div_org col-sm-7" style="<?=!empty($join_range_org) ? '' : 'display:none' ?>">
                         <div class="phone-control-wrap">
                            <div class="phone-addon text-left">
                            <div class="form-group height_auto m-b-5">
                              
                                <div id="all_join_org" style="<?=checkIsset($join_range_org) !='range'  ? '' : 'display:none' ?>">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" name="added_date_org" id="added_date_org" value="<?=checkIsset($added_date_org)?>" class="form-control date_picker" />
                                        <span class="error error_added_date_org">Please select date.</span>
                                    </div>
                                </div>
                              
                                <div id="range_join_org" style="<?=checkIsset($join_range_org) =='range'  ? '' : 'display:none' ?>">
                                    <div class="phone-control-wrap">
                                          <div class="phone-addon">
                                            <div class="input-group">
                                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <div class="pr">
                                                   <input type="text" name="fromdate_org" id="fromdate_org" class="date_picker form-control" value="<?=checkIsset($fromdate_org)?>">
                                                   <label>From Date</label>
                                                </div>
                                            </div>
                                             <span class="error error_pr_from_date">Please select from date.</span>
                                          </div>
                                          <div class="phone-addon">
                                            <div class="input-group">
                                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <div class="pr">
                                                   <input type="text" name="todate_org" id="todate_org" class="date_picker form-control" value="<?=checkIsset($todate_org)?>">
                                                   <label>To Date</label>
                                                </div>
                                            </div>
                                             <span class="error error_todate_org">Please select to date.</span>
                                          </div>
                                    </div>
                                </div>
                            </div>
                            </div>

                        </div>
                      </div>
                      <div class="col-sm-2">
                            <div class="form-group height_auto m-b-5">
                                <a href="javascript:void(0);" class="btn btn-action btn-block" id="search_org">Search</a>
                            </div>
                      </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>New Business Sales</td>
                <td><?= getOrganizationPremiums($agent_id, 'N',$searchArray);?></td>
            </tr>
            <tr>
                <td>Renewal Sales</td>
                <td><?= getOrganizationPremiums($agent_id, 'Y',$searchArray);?></td>
            </tr>
            <tr>
                <td>Total Sales</td>
                <td><?= getOrganizationPremiums($agent_id, '',$searchArray); ?></td>
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
                <td><?=getOrganizationCustomer($agent_id, $searchArray)?></td>
            </tr>
            <tr>
                <td>New Agents Contracted</td>
                <td><?=getOrganizationUsers($agent_id, 'Agent', $searchArray)?></td>
            </tr>
            <tr>
                <td>New Groups Enrolled</td>
                <td><?=getOrganizationUsers($agent_id, 'Group', $searchArray)?></td>
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

    <form action="agent_organization_production_report.php" id="frm_search_org">
        <input type="hidden" name="viewOrgSales" id="viewOrgSales" value="<?=$viewOrgSales?>">
        <input type="hidden" name="is_ajaxed_org" id="is_ajaxed_org">
        <input type="hidden" name="agent_id" value="<?=$agent_id?>" id="agent_id_org">
        <div id="agent_org_report"></div>
       
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
        ajax_org_submit();
        $(".error").hide();
    });

    $(document).off("click","#search_org");
    $(document).on("click","#search_org",function(){
        $('#viewOrgSales').val("allSales");
        if($("#join_range_org").val() === undefined || $("#join_range_org").val() === ''){
            $(".error_range_org").show();
        }else if($("#join_range_org").val() !== 'range'){
            if($("#added_date_org").val() === undefined || $("#added_date_org").val() === ''){
                $(".error_added_date_org").show();
            }else{
                ajax_org_submit();
            }
        }else if($("#join_range_org").val() === 'range'){
            if($("#fromdate_org").val() === undefined || $("#fromdate_org").val() === ''){
                $(".error_pr_from_date").show();
            }else{
                $(".error_pr_from_date").hide();
            }
            if($("#todate_org").val() === undefined || $("#todate_org").val() === ''){
                $(".error_pr_to_date").show();
            }else{
                $(".error_pr_to_date").hide()
            }
           
            if(($("#fromdate_org").val() !== undefined && $("#fromdate_org").val() !== '') && ($("#todate_org").val() !== undefined && $("#todate_org").val() !== '')){
                ajax_org_submit();
            }
        }
    });
    


    $(document).off('change', '#join_range_org');
    $(document).on('change', '#join_range_org', function(e) {
        e.preventDefault();
         $("#frm_search_org .date_picker").val("");
        if($(this).val() == ''){
          $('.select_date_div_org').hide();
          $('#date_range_org').removeClass('col-sm-3').addClass('col-sm-12');
        }else{
          $('#date_range_org').removeClass('col-sm-12').addClass('col-sm-3');
          $('.select_date_div_org').show();
          if ($(this).val() == 'range') {
            $('#range_join_org').show();
            $('#all_join_org').hide();
          } else {
            $('#range_join_org').hide();
            $('#all_join_org').show();
          }
        }
        common_select();
        fRefresh();
    });


    function ajax_org_submit() {
        $('#ajax_loader').show();
        $(".error").hide();
        $('#agent_org_report').hide();
        $('#is_ajaxed_org').val('1');
        var params = $('#frm_search_org').serialize();
        $.ajax({
            url: $('#frm_search_org').attr('action'),
            type: 'GET',
            data: params,
            success: function(res) {
                $('#ajax_loader').hide();
                $('#agent_org_report').html(res).show();
                orgTodayReport();
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

    function orgTodayReport(){
        var viewOrgSales = $("#viewOrgSales").val();
        var today = "<?=$today?>";
        if(viewOrgSales == "todaySales"){
          $('#join_range_org').val('exactly').trigger('change');
          $("#added_date_org").val(today);
        }
    }
</script>
<?php } ?>