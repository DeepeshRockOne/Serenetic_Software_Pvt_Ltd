<?php if($is_ajaxed){?>
    <div class="panel panel-default panel-block">
        <div class="panel-body">
            <h4 class="m-t-0 m-b-15">Payable Summary</h4>
            <div class="table-responsive">
                <table class="<?=$table_class?> ">
                    <thead>
                        <tr>
                            <th>Total</th>
                            <th>Debit</th>
                            <th width="130px">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($total_rows > 0){ ?>
                        <tr>
                            <td>
                                <?php if($TotalAmt < 0){?>
                                    <a href="javascript:void(0);" class="fw500 text-action"><?=$TotalCnt?>/(<?=displayAmount(abs($TotalAmt))?>)</a>
                                <?php } else { ?>
                                    <a href="javascript:void(0);" class="fw500 text-action"><?=$TotalCnt?>/<?=displayAmount($TotalAmt)?></a>
                                <?php } ?>
                            </td>
                            <td>
                                <a href="javascript:void(0);" class="fw500 text-action">
                                    <?=$DebitCnt?>/<?=displayAmount(abs($DebitAmt))?></a>
                            </td>
                            <td>
                                <a href="javascript:void(0);" class="fw500 text-action">
                                    <?=$CreditCnt?>/(<?=displayAmount(abs($CreditAmt))?>)
                                </a>
                            </td>
                        </tr>
                        <?php } else echo "<tr><td colspan='3'>No record found</td></tr>" ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="panel panel-default panel-block">
        <div class="panel-body">
            <div class="clearfix m-b-15">
                <h4 class="pull-left m-t-7">Payables</h4>
            </div>
            <div class="table-responsive">
                <table class="<?=$table_class?>">
                    <thead>
                        <tr>
                            <th>ID/Added Date</th>
                            <th>Payee Type</th>
                            <th width="15%">Member Name/ID</th>
                            <th>Enrolling Agent/Company</th>
                            <th width="15%">Product Name/ID</th>
                            <th width="10%" class="text-center">Plan ID</th>
                            <th>Status/Transaction ID</th>
                            <th>Payout</th>
                            <th>Debit</th>
                            <th>Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($total_rows > 0){
                foreach($fetch_rows as $rows){ ?>
                        <tr>
                            <td>
                                <a href="javascript:void(0)" data-href="transaction_receipt.php?transId=<?=md5($rows['ai_transaction_id'])?>" class="text-red fw500 transReceipt">
                                    <?=$rows['ORDER_ID']?></a>
                                <br>
                                <?=getCustomDate($rows['ADDED_DATE'])?>
                            </td>
                            <td>
                                <?=ucfirst($rows['PAYEE_TYPE'])?>
                            </td>
                            <td width="15%">
                                <?=$rows['MEMBER_NAME']?><br><a href="members_details.php?id=<?=$rows['member_id']?>" target="_blank" class="fw500 text-action">
                                    <?=$rows['MEMBER_REP_ID']?></a>
                            </td>
                            <td>
                                <?=ucfirst($rows['PAYEE'])?><br><a href="javascript:void(0);" class="fw500 text-action">
                                    <?=$rows['agencyNameDis']?></a>
                            </td>
                            <td width="15%">
                                <?=$rows['PRODUCT_NAME']?><br><a href="javascript:void(0);" class="fw500 text-action">
                                    <?=$rows['PRODUCT_ID']?></a>
                            </td>
                            <td width="10%" class="text-center"><a href="javascript:void(0);" class="fw500 text-action">
                                    <?=$rows['POLICY_ID']?></a></td>
                            <td>
                                <?=$rows['TRANSACTION_STATUS']?><br><a href="javascript:void(0);" class="fw500 text-action">
                                    <?=$rows['TRANSACTION_ID']?></a>
                            </td>
                            <td class="text-nowrap"><?=$rows['PAYOUT'];?></td>
                            <td>
                                <?=$rows['DEBIT'] != 0 ? displayAmount(abs($rows['DEBIT'])) : '-';?>
                            </td>
                            <td class="text-action"><strong><?=$rows['CREDIT'] != 0 ? '('.displayAmount(abs($rows['CREDIT'])) .')' : '-';?></strong></td>
                        </tr>
                        <?php }}else echo "<tr><td colspan='10'>No record found!</td></tr>"; ?>
                    </tbody>
                    <?php if ($total_rows > 0) { ?>
                    <tfoot>
                        <tr>
                            <td colspan="10">
                                <?php echo $paginate->links_html; ?>
                            </td>
                        </tr>
                    </tfoot>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
    <script type="text/javascript">
    var not_win = '';
    $(".transReceipt").on('click', function() {
        $href = $(this).attr('data-href');
        var not_win = window.open($href, "myWindow", "width=1024,height=630");
    });
    </script>
<?php } else { ?>
    <div class="container m-t-30">
        <div class="panel panel-default panel-block advance_info_div">
            <div class="panel-body">
                <div class="phone-control-wrap ">
                    <div class="phone-addon w-90 v-align-top">
                        <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="70px">
                    </div>
                    <div class="phone-addon text-left">
                        <p class=" mn">For every successful or reversal order, a payable is generated for both the debit (incoming monies) and credit (outgoing monies) as they relate to commissions, vendors, carriers, and memberships on product set-up. Each payable will be listed in a separate record and can be searched using the criteria below. By default, only the current month’s payables are displaying.</p>
                        <div class="clearfix m-t-15">
                            <a href="javascript:void(0);" class="btn btn-info" id="viewallPayable">All Payables</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-default panel-block panel-title-block">
            <div class="panel-left">
                <div class="panel-left-nav">
                    <ul>
                        <li class="active"><a href="javascript:void(0);"><i class="fa fa-search"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-right">
                <div class="panel-heading">
                    <div class="panel-search-title">
                        <span class="clr-light-blk">SEARCH</span>
                    </div>
                </div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body theme-form">
                        <form action="account_payable.php" name="payableFrm" id="payableFrm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div id="date_range" class="<?=!empty($join_range) ? 'col-md-3' : 'col-md-12' ?>">
                                            <div class="form-group">
                                                <select class="form-control listing_search" id="join_range" name="join_range">
                                                    <option value=""> </option>
                                                    <option value="Range" <?= checkIsset($join_range) =='Range' ? 'selected="selected"' : '' ?>>Range</option>
                                                    <option value="Exactly" <?= checkIsset($join_range) =='Exactly' ? 'selected="selected"' : '' ?>>Exactly</option>
                                                    <option value="Before" <?= checkIsset($join_range) =='Before' ? 'selected="selected"' : '' ?>>Before</option>
                                                    <option value="After" <?= checkIsset($join_range) =='After' ? 'selected="selected"' : '' ?>>After</option>
                                                </select>
                                                <label>Added Date</label>
                                            </div>
                                        </div>
                                        <div class="select_date_div col-md-9" style="<?=!empty($join_range)? '' : 'display:none' ?>">
                                            <div class="form-group">
                                                <div id="all_join" class="input-group" style="<?=!empty($join_range) && in_array($join_range,array('Before','Exactly','After')) ? '' : 'display:none' ?>">
                                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                    <input type="text" name="added_date" id="added_date" value="<?=checkIsset($added_date)?>" class="form-control listing_search date_picker" />
                                                </div>
                                                <div id="range_join" style="<?=!empty($join_range) && $join_range=='Range' ? '' : 'display:none' ?>">
                                                    <div class="phone-control-wrap">
                                                        <div class="phone-addon">
                                                            <label class="mn">From</label>
                                                        </div>
                                                        <div class="phone-addon">
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                                <input type="text" name="fromdate" id="fromdate" value="<?=checkIsset($fromdate)?>" class="form-control listing_search date_picker" />
                                                            </div>
                                                        </div>
                                                        <div class="phone-addon">
                                                            <label class="mn">To</label>
                                                        </div>
                                                        <div class="phone-addon">
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                                <input type="text" name="todate" id="todate" value="<?=checkIsset($todate)?>" class="form-control listing_search date_picker" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                <div class="form-group">
                                        <select name="products[]" id="products" multiple="multiple" class="listing_search se_multiple_select">
                                            <?php foreach ($company_arr as $key=>$company) { ?>
                                            <optgroup label='<?= $key ?>'>
                                            <?php foreach ($company as $pkey =>$row) { ?>
                                            <option value="<?= $row['id'] ?>"><?= $row['name'] .' ('.$row['product_code'].')' ?></option>
                                            <?php } ?>
                                            </optgroup>
                                            <?php } ?>
                                        </select>
                                        <label>Products</label>
                                </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="text" name="order_id" id="order_id" value="<?=checkIsset($order_id)?>" class="form-control listing_search">
                                        <label>Order ID(s)</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="text" name="transaction_id" id="transaction_id" class="form-control listing_search">
                                        <label>Transaction ID(s)</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <select class="se_multiple_select listing_search" name="payee_type[]" id="payee_type" multiple="multiple">
                                            <option value="Advance Commission">Advance Commission</option>
                                            <option value="Carrier">Carrier</option>
                                            <option value="Commission">Commission</option>
                                            <option value="Fee Commission">Fee Commission</option>
                                            <option value="Membership">Membership</option>
                                            <option value="PMPM">PMPM</option>
                                            <option value="Vendor">Vendor</option>
                                        </select>
                                        <label>Payee Type</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group ">
                                            <select class="se_multiple_select listing_search" name="payee_id[]" id="payee_id" multiple="multiple">
                                                <?php if(!empty($tree_agent_res)){ ?>
                                                    <?php foreach($tree_agent_res as $value){ ?>
                                                        <option value="<?=$value['rep_id']?>" <?=!empty($payee_id) && in_array($value['rep_id'],$payee_id) ? 'selected="selected"': ''?>><?=$value['rep_id'].' - '.$value['fname'].' '.$value['lname']?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                            <label>Enrolling Agent</label>
                                    </div>
                                </div>
                                <?=getAgencySelect('tree_agent_id',$_SESSION['agents']['id'],'agent');?>
                                <div class="col-sm-6">
                                    <div class="form-group height_auto">
                                    <input name="member_id" id="member_id" type="text" class="listing_search" value="<?= checkIsset($member_id) ?>"/>
                                    <label>Member ID/Name(s)</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group ">
                                        <input type="text" name="policy_id" id="policy_id" class="form-control listing_search">
                                        <label>Plan ID(s)</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group ">
                                        <select class="se_multiple_select listing_search" name="coverage_period[]" id="coverage_period" multiple="multiple">
                                            <?php for($i=1;$i<=50;$i++){ ?>
                                            <option value="<?=$i?>">P
                                                <?=$i?>
                                            </option>
                                            <?php } ?>
                                        </select>
                                        <label>Plan Period(s)</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group ">
                                        <select class="form-control listing_search" name="paymentType" id="paymentType">
                                            <option value=""></option>
                                            <option value="CC">Credit Card</option>
                                            <option value="ACH">ACH</option>
                                        </select>
                                        <label>Payment Type</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <select class="se_multiple_select listing_search" name="order_status[]" id="order_status" multiple="multiple">
                                            <option value="Cancelled">Cancelled</option>
                                            <option value="Chargeback">Chargeback</option>
                                            <option value="Payment Approved">Payment Approved</option>
                                            <option value="Payment Declined">Payment Declined</option>
                                            <option value="Payment Returned">Payment Returned</option>
                                            <option value="Pending Settlement">Pending Settlement</option>
                                            <option value="Post Payment">Post Payment</option>
                                            <option value="Refund">Refund</option>
                                            <option value="Void">Void</option>
                                        </select>
                                        <label>Order Status</label>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer clearfix">
                                <button type="submit" class="btn btn-info" name="" id=""> <i class="fa fa-search"></i> Search </button>
                                <button type="button" class="btn btn-info btn-outline" name="viewAll" id="viewAll" onClick="window.location.href = 'account_payable.php?viewPayable=allPayable'"> <i class="fa fa-search-plus"></i> View All </button>
                                <button type="button" name="" id="btn_export" class="btn red-link"> <i class="fa fa-download"></i> Export </button>
                            </div>
                            <input type="hidden" name="export" id="export" value="" />
                            <input type="hidden" name="viewPayable" id="viewPayable" value="<?=checkIsset($viewPayable)?>">
                            <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
                            <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
                            <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
                            <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
                        </form>
                    </div>
                </div>
            </div>
            <div class="search-handle">
                <a href="javascript:void(0);" data-perform="panel-collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></a>
            </div>
        </div>
        <div id="ajax_payable_data"></div>
    </div>
    <script type="text/javascript">
    $(document).ready(function() {
    dropdown_pagination('ajax_payable_data');

        $("#order_status").multipleSelect({
            selectAll: false
        });
        $("#products,#payee_type,#coverage_period,#payee_id,#tree_agent_id").multipleSelect({});
        $(".date_picker").datepicker({
            changeDay: true,
            changeMonth: true,
            changeYear: true
        });
        ajax_submit();
        initSelectize('member_id','MemberID',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>,'<?=$agent_id?>');
        $(document).off('click', '#btn_export');
        $(document).on('click', '#btn_export', function(e) {
            confirm_export_data(function() {
                $("#export").val('payables_export');
                $('#ajax_loader').show();
                $('#is_ajaxed').val('1');
                var params = $('#payableFrm').serialize();
                $.ajax({
                    url: $('#payableFrm').attr('action'),
                    type: 'GET',
                    data: params,
                    dataType: 'json',
                    success: function(res) {
                        $('#ajax_loader').hide();
                        $("#export").val('');
                        if (res.status == "success") {
                            confirm_view_export_request(true,'agent');
                        } else {
                            setNotifyError(res.message);
                        }
                    }
                });
            });
        });
    });

    $(document).off("submit", "#payableFrm");
    $(document).on("submit", "#payableFrm", function(e) {
        e.preventDefault();
        $('#viewPayable').val("allPayable");
        disable_search();
    });

    $(document).off('click', '#viewallPayable');
    $(document).on('click', '#viewallPayable', function(e) {
        $('#viewPayable').val("allPayable");
        $('#join_range').val('').trigger('change');
        $("#added_date").val();
        ajax_submit();
    });

    $(document).off('change', '#join_range');
    $(document).on('change', '#join_range', function(e) {
        e.preventDefault();
        if ($(this).val() == '') {
            $('.select_date_div').hide();
            $('#date_range').removeClass('col-md-3').addClass('col-md-12');
        } else {
            $('#viewPayable').val("");
            $('#date_range').removeClass('col-md-12').addClass('col-md-3');
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


    $(document).off('click', '#ajax_payable_data ul.pagination li a');
    $(document).on('click', '#ajax_payable_data ul.pagination li a', function(e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#ajax_payable_data').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function(res) {
                $('#ajax_loader').hide();
                $('#ajax_payable_data').html(res).show();
                common_select();
            }
        });
    });

    function ajax_submit() {
        $('#ajax_loader').show();
        $('#ajax_payable_data').hide();
        $('#is_ajaxed').val('1');
        var params = $('#payableFrm').serialize();
        $.ajax({
            url: $('#payableFrm').attr('action'),
            type: 'GET',
            data: params,
            success: function(res) {
                $('#ajax_loader').hide();
                $('#ajax_payable_data').html(res).show();
                viewPayable();
                common_select();
                fRefresh();
            }
        });
        return false;
    }

    function viewPayable() {
        var odrDisplay = $("#viewPayable").val();
        var today = "<?=$added_date?>";
        if (odrDisplay == "dailyPayable") {//OP29-566 updates
            $('#join_range').val('Exactly').trigger('change');
            $("#payableFrm #added_date").val(today);
        }
    }
    </script>
<?php }?>