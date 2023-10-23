<p class="agp_md_title">Accounts Payable</p>
<div class="row">
    <?php
        if(!empty($ws_res)) {
            foreach ($ws_res as $key => $ws_row) {
                $pay_sel = "SELECT p.id as id,p.id as payable_id,od.renew_count as period_count,o.is_renewal,o.display_id,od.start_coverage_period,od.end_coverage_period,o.transaction_id,o.created_at,od.order_id,o.status,o.grand_total
                FROM payable p
                JOIN order_details od ON (od.id = p.order_detail_id AND od.is_deleted='N')
                JOIN orders o ON (p.order_id = o.id)
                JOIN prd_main pm ON (pm.id=p.product_id)
                WHERE od.website_id=:website_id ORDER BY od.start_coverage_period ASC";
              $payable_order_where=array(
                ":website_id"=>$ws_row['id'],
              );
              $pay_arr=$pdo->select($pay_sel,$payable_order_where);
                ?>
                <div class="col-sm-12">
                    <div class="payable_product_box" id="payable_product_box_<?=$ws_row['id']?>">
                        <h4 class="text-blue"> <?=$ws_row['product_name']?></h4>
                        <p><strong>Plan:</strong> <?=$ws_row['website_id']?></p>
                        <p class="mn"><strong>Coverage:</strong> <?=$prdPlanTypeArray[$ws_row['prd_plan_type_id']]['title']?></p>
                        <div class="clearfix">
                            <a data-toggle="collapse" href="#collapse_<?=$ws_row['id']?>" id="view_detail_<?=$ws_row['id']?>" class="btn btn-action view_detail">View Details</a>
                        </div>
                    </div>
                 </div>
                 <div class="col-sm-12">
                    <div class="panel-group payable_wrap">
                        <div class="panel panel-default">
                            <div id="collapse_<?=$ws_row['id']?>" class="panel-collapse collapse">
                                <div class="panel-body pn">
                                    <div class="table-responsive">
                                        <table class="table-striped table">
                                            <tbody>
                                                <?php 
                                                    if(!empty($pay_arr)) {
                                                        ?>
                                                        <tr>
                                                            <td class="fw500">Period</td>
                                                            <td class="fw500" colspan="3">Number of Payables</td>
                                                         </tr>
                                                        <?php
                                                        foreach ($pay_arr as $payable_order_key => $payable_order_value) {
                                                            $start_coverage=date("m/d/Y",strtotime($payable_order_value['start_coverage_period']));
                                                            $end_coverage=date("m/d/Y",strtotime($payable_order_value['end_coverage_period']));
                                                            $display_id = $payable_order_value['display_id'];
                                                            $transaction_id = $payable_order_value['transaction_id'];
                                                            $order_date = date("m/d/Y h:ia",strtotime($payable_order_value['created_at']));
                                                            $order_status = $payable_order_value['status'];
                                                            $grand_total = $payable_order_value['grand_total'];
                                                            $payable_id = $payable_order_value['payable_id'];
                                                            $period_count = $payable_order_value['period_count'];
                                                            
                                                            $sqlSum="SELECT  SUM(pd.debit) as total_debit,SUM(pd.credit) as total_credit FROM payable_details pd WHERE pd.payable_id=:payable_id";
                                                            $resSum=$pdo->selectOne($sqlSum,array(":payable_id"=>$payable_id));

                                                            $total_payable_debit = abs($resSum['total_debit']);
                                                            $total_payable_credit = abs($resSum['total_credit']);
                                                            $total_payable_amt = $total_payable_debit - $total_payable_credit;

                                                            $uid = $ws_row['id'].'_'.$payable_order_key;

                                                            $payablesArr=get_payables_new($payable_id);
                                                            ?>
                                                            <tr class="tr_expand" data-id="tr_expand_<?=$uid?>" data-pr_id="<?=$uid?>">
                                                                <td><?php if(!$is_list_bill){ ?><strong class="text-blue">P<?=$period_count?></strong> &nbsp; <?php } ?> <?=getCustomDate($start_coverage)?> - <?=getCustomDate($end_coverage)?></td>
                                                                <td width="100px"><strong class="text-blue"><?=count($payablesArr)?></strong> <strong class="m-l-5 label label-info">$</strong></td>
                                                                <td width="100px" class="fw500 text-red"><?=$total_payable_amt >= 0?displayAmount($total_payable_amt):'('.displayAmount(abs($total_payable_amt)).')'; ?></td>
                                                                <td width="30px" id="icon_tr_expand_<?=$uid?>"><i class="ti-plus fw700"></i></td>
                                                            </tr>
                                                            <tr id="tr_expand_<?=$uid?>" style="display: none">
                                                                <td colspan="5" class="pn">
                                                                   <div class="blue-box m-b-15">
                                                                      <div class="row">
                                                                         <div class="col-sm-6">
                                                                            <p class="fs16"><strong class="text-blue">Order #: </strong><?=$display_id?></p>
                                                                            <p class="m-b-15"><strong class="text-blue">Transaction ID:</strong> <?=$transaction_id?><br><strong class="text-blue">Order Date: </strong><?=$tz->getDate($order_date)?></p>
                                                                         </div>
                                                                         <div class="col-sm-6 text-right">
                                                                            <p class="fs16"><strong class="text-blue">Retail:</strong> <?=displayAmount($grand_total)?></p>
                                                                            <p class="m-b-15"><strong class="text-blue">Order Status:<br></strong> <?=$order_status?></p>
                                                                         </div>
                                                                      </div>
                                                                      <div class="table-responsive ">
                                                                         <table class="<?=$table_class?> payee_table">
                                                                            <thead>
                                                                               <tr>
                                                                                  <th>Added Date</th>
                                                                                  <th>Payee Type</th>
                                                                                  <th>Payee</th>
                                                                                  <th>Fee</th>
                                                                                  <th>Product Name/Id</th>
                                                                                  <th>Payout</th>
                                                                                  <th width="6%">&nbsp;</th>
                                                                                  <th width="100px">Debit</th>
                                                                                  <th width="100px">Credit</th>
                                                                               </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                               <?php if(!empty($payablesArr) && count($payablesArr)>0 ){
                                                                                     $exist_payable = array();
                                                                                     foreach($payablesArr as $payables){
                                                                                        $payable_type = "";
                                                                                        if(empty($exist_payable) || !in_array($payables['PAYEE_TYPE'],$exist_payable)){
                                                                                           $payable_type = $payables['PAYEE_TYPE'];
                                                                                           $total_credit = $total_debit = 0;
                                                                                           foreach($payablesArr as $payables_new){
                                                                                              if($payable_type == $payables_new['PAYEE_TYPE']){

                                                                                              $total_credit += abs($payables_new['CREDIT']);
                                                                                              $total_debit += abs($payables_new['DEBIT']);

                                                                                        ?>
                                                                                     <tr>
                                                                                        <td><?=getCustomDate($payables_new['ADDED_DATE'])?></td>
                                                                                        <td>
                                                                                        <?=ucfirst($payables_new['PAYEE_TYPE'])?>
                                                                                        </td>
                                                                                        <td>
                                                                                        <?=$payables_new['PAYEE']?><br><a href="javascript:void(0);"  class="fw500 text-action"><?=$payables_new['PAYEE_ID']?></a>
                                                                                        </td>
                                                                                        <td>
                                                                                        <?=$payables_new['FEE_NAME']?><br><a href="javascript:void(0);"  class="fw500 text-action"><?=$payables_new['FEE_CODE']?></a>
                                                                                        </td>
                                                                                        <td>
                                                                                        <?=$payables_new['PRODUCT_NAME']?><br><a href="javascript:void(0);"  class="fw500 text-action"><?=$payables_new['PRODUCT_ID']?></a>
                                                                                        </td>
                                                                                        <td><?=$payables_new['PAYOUT']?></td>
                                                                                        <td width="6%">&nbsp;</td>
                                                                                        <td><?=displayAmount(abs($payables_new['DEBIT']))?></td>
                                                                                        <td class="text-red">(<?=displayAmount(abs($payables_new['CREDIT']))?>)</td>
                                                                                     </tr>
                                                                               <?php }   array_push($exist_payable,$payables['PAYEE_TYPE']); 
                                                                                      } ?>
                                                                                  <tr>
                                                                                     <td width="6%" colspan="7" class="text-right text-black fw500">SUBTOTAL</td>
                                                                                     <td class="text-black fw500"><?=displayAmount($total_debit)?></td>
                                                                                     <td class="text-red fw500">(<?=displayAmount($total_credit)?>)</td>
                                                                                  </tr>  
                                                                                  <?php }} ?>
                                                                                  <tr>
                                                                                     <td colspan="7" class="text-right text-black fw500">TOTAL</td>
                                                                                     <td class="text-black fw500"><?=displayAmount($total_payable_debit)?></td>
                                                                                     <td class="text-red fw500">(<?=displayAmount($total_payable_credit)?>)</td>
                                                                                  </tr>
                                                                               <?php }else{ echo "<tr><td colspan='3'>No Record Found!</td></tr>";} ?>
                                                                            </tbody>
                                                                         </table>
                                                                      </div>
                                                                   </div>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        }
                                                    } else { 
                                                        echo "<tr><td colspan='3'>No Record Found!</td></tr>";
                                                    } 
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    <?php
                }
            }
        ?>
    </div>
    <hr>
    <script type="text/javascript">
       $(document).on('click',".tr_expand",function(){
        var id = $(this).attr('data-pr_id');
        if($(this).closest('tr').hasClass('active')){
            $(this).closest('tr').removeClass('active');
            $("#icon_tr_expand_"+id).html('<i class="ti-plus fw700"></i>');
            $("#"+$(this).attr('data-id')).hide();
        }else{
            $(this).closest('tr').addClass('active');
            $("#icon_tr_expand_"+id).html('<i class="ti-minus fw700"></i>');
            $("#"+$(this).attr('data-id')).show();
        }
    });
    $(document).on('click',".view_detail",function(){
        var id = $(this).attr('id').replace("view_detail_",'');
        $("#payable_product_box_"+id).toggleClass('active');
    });
</script>