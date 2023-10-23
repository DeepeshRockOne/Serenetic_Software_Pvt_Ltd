<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$tz = new UserTimeZone('M d, Y h:i:s A T', $_SESSION['admin']['timezone']);
$order_id = isset($_GET['order_id']) ? md5($_GET['order_id']) : "";

$resOrder = $pdo->selectOne("SELECT o.id as odrId,o.display_id as odrDispId,CONCAT(c.fname,' ',c.lname) as mbrName, 
  			c.id as mbrId,c.rep_id as mbrDispId,c.cell_phone as mbrPhone,c.email as mbrEmail,c.sponsor_id,
                c.user_name as mbrUserName,o.status as odrStatus,o.sub_total as subTotal,o.grand_total as grandTotal,o.created_at as odrDate,o.post_date as odrPostDate,
                ob.address as billAdd,ob.address2 as billAdd2,ob.city as billCity,ob.state as billState,
                ob.zip as billZip,ob.payment_mode as billPayType,ob.card_type as billCardType,ob.last_cc_ach_no as lastPayNo,o.transaction_id as transactionId,DATE_FORMAT(o.created_at,'%m/%d/%Y') as transactionDate,o.payment_processor_res as processorResponse,o.order_comments as odrReason,o.payment_type
          FROM orders o
          LEFT JOIN customer c ON (c.id = o.customer_id)
          LEFT JOIN customer s ON (c.sponsor_id = s.id)
          LEFT JOIN order_billing_info ob ON(ob.order_id=o.id)
          WHERE md5(o.id) = :id",array(':id' => $order_id));

if($resOrder){
	$orderId = checkIsset($resOrder["odrId"]);
	$customer_id = checkIsset($resOrder["mbrId"]);
	$orderDispId = checkIsset($resOrder['odrDispId']);
	$orderStatus = checkIsset($resOrder["odrStatus"]);
	$odrPostDate = !empty($resOrder["odrPostDate"]) ? date("m/d/Y",strtotime($resOrder["odrPostDate"])) : "";
	$transactionDate =  $orderStatus != "Post Payment" ? $resOrder['transactionDate'] : "";
	$transactionId =  $resOrder["transactionId"] > 0 ? $resOrder["transactionId"] : "";
	$subTotal = !empty($resOrder["subTotal"]) ? $resOrder["subTotal"] : 0;
	$grandTotal = !empty($resOrder["grandTotal"]) ? $resOrder["grandTotal"] : 0;
	$stepFeePrice = 0;
	$stepFeeRefund = 'N';
	$serviceFeePrice = 0;
	$serviceFeeRefund = 'N';

	$detSql = "SELECT pm.type,pm.product_type,od.product_name,od.start_coverage_period,od.end_coverage_period,ppt.title as planTitle,od.unit_price as price,od.is_refund
        FROM order_details od
        JOIN prd_main pm ON(pm.id=od.product_id)
        LEFT JOIN prd_plan_type ppt ON(od.prd_plan_type_id=ppt.id)
        WHERE od.order_id = :odrId AND od.is_deleted='N'
        ORDER BY od.product_name ASC";
	$detRes = $pdo->select($detSql, array(':odrId' => makeSafe($orderId)));

	$reversal_reasons = $pdo->select("SELECT name
             FROM termination_reason
             WHERE is_deleted='N'
             ORDER BY name ASC");

	$active_products = $pdo->select("SELECT IF(p.name = '' AND p.product_type = 'ServiceFee','Service Fee',p.name) AS name,p.id as p_id,w.id as w_id,w.website_id,od.plan_id,od.unit_price as price 
		FROM orders o
		JOIN order_details od on(od.order_id = o.id AND od.is_deleted='N')
		JOIN website_subscriptions w on(w.id=od.website_id)
		JOIN prd_main p on(p.id = od.product_id)
		WHERE o.id = :order_id and od.is_chargeback = 'N' AND od.is_refund = 'N' order by p.type DESC,od.product_name ASC",array(':order_id' => $orderId));

	// pre_print($orderId);
	ob_start(); 
?>

<div class="col-md-8">
    <div class="panel panel-default panel-block">
      <div class="panel-body pn transaction_receipt">
        <div class="clearfix">
          <div class="col-sm-3 receipt_left">
            <div class="bg_dark_primary">
              <div class="panel-body">
                <h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);">MEMBER</h4>
                <p class="text-white mn"><span class="fw700  fs18"><?=checkIsset($resOrder["mbrName"])?></span><br><?=checkIsset($resOrder["mbrDispId"])?><br><?=format_telephone(checkIsset($resOrder["mbrPhone"]))?><br> <?=checkIsset($resOrder["mbrEmail"])?></p>
              </div>
            </div>
              <div class="panel-body">
                <h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);">BILLING ADDRESS</h4>
                <p class="text-white mn"><?=checkIsset($resOrder["billAdd"])?><br>
                	<?php if(!empty($resOrder["billAdd2"])){
	                  echo $resOrder["billAdd2"]; ?>
	                  <br/>
	                <?php } ?>
	                <?=checkIsset($resOrder["billCity"])?>, <?=checkIsset($allStateShortName[$resOrder["billState"]])?>
	                <br/> 
	                <?=checkIsset($resOrder["billZip"])?>
                </p>
              </div>
              <div class="panel-body">
                <h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);">Payment</h4>
                <p class="text-white mn"><?=displayAmount($grandTotal)?> (<?=checkIsset($billType)?> *<?=checkIsset($resOrder["lastPayNo"])?>)</p>
              </div>
              <div class="panel-body">
                <h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);">TRANSACTION INFO</h4>
                <p class="text-white mn"><?=checkIsset($transactionId)?><br><?=checkIsset($transactionDate)?></p>
              </div>
              <div class="panel-body">
                <h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);">ORDER ID</h4>
                <p class="text-white mn"><?=$orderDispId?></p>
              </div>
          </div>
          <div class="col-sm-9 receipt_right">
            <div class="p-15">
              <div class="clearfix m-b-30 m-t-10">
                <div class="pull-left">
                  <p class="text-success fw500 mn"><?=$orderStatus?> <i class="fa fa-check-circle" aria-hidden="true"></i></p>
                  <p class="text-gray fs10"><?=$tz->getDate($resOrder["odrDate"])?></p>
                </div>
                <div class="pull-right">
                  <h4>RECEIPT</h4>
                </div>
              </div>
              <p class="fw500"> Summary</p>
              <div class="table-responsive">
                <div class="table-responsive">
            <table class="table table-borderless table-striped <?=$tblClass?>">
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Plan Period</th>
                  <th>Plan</th>
                  <th class="text-right">Total</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                if(!empty($detRes)){
                  $fee_prd_res = array();
                  foreach ($detRes as $key => $order) {
                    if($order["type"] == 'Fees'){
                      if($order["product_type"] == "Healthy Step"){
                        $stepFeePrice = $order["price"];
                        $stepFeeRefund= $order["is_refund"];
                        continue;
                      }
                      if($order["product_type"] == "ServiceFee"){
                        $serviceFeePrice = $order["price"];
                        $serviceFeeRefund = $order["is_refund"];
                        continue;
                      }
                      $fee_prd_res[] = $order;
                      continue;
                    }
                ?>
                 <tr class="<?=$order['is_refund'] == 'Y' ? 'text-danger' : ''?>">
                  <td><?=$order["product_name"]?></td>
                  <td><?=date("m/d/Y",strtotime($order["start_coverage_period"]))?> - <?=date("m/d/Y",strtotime($order["end_coverage_period"]))?></td>
                  <td><?=$order["planTitle"]?></td>
                  <td class="text-right"><?=displayAmount($order["price"])?></td>
                </tr>
                <?php
                  }
                  foreach ($fee_prd_res as $key => $fee_prd_row) {
                    ?>
                    <tr class="<?=$fee_prd_row['is_refund'] == 'Y' ? 'text-danger' : ''?>">
                      <td><?=$fee_prd_row["product_name"]?></td>
                      <td></td>
                      <td>Fees</td>
                      <td class="text-right"><?=displayAmount($fee_prd_row["price"])?></td>
                    </tr>
                    <?php
                  }
                }
                ?>
              </tbody>
            </table>
            <table class="table table-borderless pull-right receipt_table m-t-20" style="max-width: 250px;">
              <tbody>
                <tr class="<?=$orderStatus == 'Refund' ? 'text-action' : ''?>">
                  <td>SubTotal(s)</td>
                  <td class="text-right"><?=displayAmount($subTotal)?></td>
                </tr>
                <tr class="<?=$stepFeeRefund == 'Y' ? 'text-action' : ''?>">
                  <td>Healthy Step(s)</td>
                  <td class="text-right"><?=displayAmount($stepFeePrice)?></td>
                </tr>
                <tr class="<?=$serviceFeeRefund == 'Y' ? 'text-action' : ''?>">
                  <td>Service Fee(s)</td>
                  <td class="text-right"><?=displayAmount($serviceFeePrice)?></td>
                </tr>
                <tr class="<?=$orderStatus == 'Refund' ? 'text-action' : ''?>">
                  <td class="fw500">Total</td>
                  <td class="text-right fw500"><?=displayAmount($grandTotal)?></td>
                </tr>
                <?php if($orderStatus == 'Refund'){ ?>
                 <hr>
                 <tr class="<?=$orderStatus == 'Refund' ? 'text-action' : ''?>">
                  <td class="fw500">Refund</td>
                  <td class="text-right fw500">(<?=displayAmount($grandTotal)?>)</td>
                </tr>
              <?php } ?>
              </tbody>
            </table>
          </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
<div class="col-md-4">
  	<form id="refund_form" method="POST" action="">
  		<input type="hidden" name="customer_id" value="<?=$customer_id?>">
  		<input type="hidden" name="order_id" value="<?=$orderId?>">
	    <div class="panel panel-default panel-block">
	      <div class="panel-body">
	        <h4 class="m-t-0 m-b-20">Select Reversal</h4>
	        <div class="theme-form">
	          <div class="form-group">
	            <select class="form-control" id="reversal_type" name="reversal_type">
	              <option></option>
                <?php if($resOrder['odrStatus'] == 'Pending Settlement'){ ?>
                  <option value="Void">Void</option>
                <?php }else if($resOrder['payment_type'] == 'ACH'){?>
                  <option value="Refund">Refund</option>
                  <option value="Chargeback">Chargeback</option>
                  <option value="Payment Return">Payment Return</option>
                <?php }else{ ?>
  	              <option value="Void">Void</option>
  	              <option value="Refund">Refund</option>
  	              <option value="Chargeback">Chargeback</option>
  	              <option value="Payment Return">Payment Return</option>
                <?php }?>
	            </select>
	            <label>Select Reversal Type</label>
	          </div>
	          <div class="form-group">
	            <select class="form-control" name="refund_reason">
	              <option></option>

	              <?php if($reversal_reasons){
	              	foreach ($reversal_reasons as $value) { ?>
	              		<option value="<?=$value['name']?>"><?=$value['name']?></option>
	              	<?php }
	              } ?>
	            </select>
	            <label>Reason</label>
	            <p class="error"><span id="err_refund_reason"></span></p>
	          </div>
	          <div class="reversal_void m-b-20" style="display: none;">
	            <div class="bg_light_gray p-10">
	              <ul class="list-unstyled mn">
	                <li>
	                  <div class="phone-control-wrap">
	                    <div class="phone-addon w-25"><i class="text-success fs18 fa fa-check-circle"></i></div>
	                    <div class="phone-addon text-left">Reverse commissions for all products</div>
	                  </div>
	                </li>
	              </ul>
	            </div>
	            <h4 class="m-t-20 m-b-20">Void Options</h4>
	            <div class="phone-control-wrap">
                <div class="phone-addon w-25 v-align-top">
                  <input type="checkbox" name="inactive_member_void" class="chk_set_term_date_void" value="Y" id="chk_set_term_date_void" checked="checked">
                </div>
                <div class="phone-addon text-left">
                  <label class="mn" id="inactive_member_void" for="chk_set_term_date_void"> Set a termination date for the voided product(s)?</label>
                </div>
	            </div>
	            <div class="void_products m-t-20">
	              <!-- <h4 class="m-t-0 m-b-20">Choose Products to Refund</h4> -->
	              <p class="error"><span id="err_void_prd_common"></span></p>
		          <div class="reversal_refund m-b-20">

		          	<?php if($active_products){
		          		foreach ($active_products as $product) {
		          			$coverage_periods = get_termination_date_selection_options($product['w_id']); ?>
		          			<div class="reversal_refund_row">
				              <div class="thumbnail" id="void_product_wrapper_<?=$product['plan_id']?>">
				                <div class="pull-left">
				                <div class="checkbox checkbox-custom mn">
				                  <input type="checkbox" id="void_term_chk[<?=$product['plan_id']?>]" class="void_term_checkbox js-switch" name="void_term_chk[<?=$product['plan_id']?>]" data-plan_id="<?=$product['plan_id']?>" data-unitprice="<?=$product['price']?>" value="<?=$product['plan_id']?>" />
				                  <label for="void_term_chk[<?=$product['plan_id']?>]"><?=$product['name']?></label>
				                  <p class="error"><span id="err_term_check_<?=$product['plan_id']?>"></span></p>
				                  
				                </div>
				               </div>
				                <div class="pull-right">$<?=$product['price']?></div>
				              </div>
				              <div class="m-t-10 termination_date_wrapper" id="void_termination_date_<?=$product['plan_id']?>" style="display: none;">
				                <div class="form-group height_auto mn">
				                  <select class="form-control" name="void_termination_date[<?=$product['plan_id']?>]">
				                    <option></option>
				                    <?php if($coverage_periods){
					                    foreach ($coverage_periods as $coverage) { ?>
					                        <option value="<?=$coverage['value']?>"><?=$coverage['text']?></option>
					                  <?php }
					                  } ?>
				                  </select>
				                  <label>Set Termination</label>
				                  <p class="error"><span id="err_void_termination_date_<?=$product['plan_id']?>"></span></p>
				                </div>
				              </div>
				            </div>
		          		<?php }
		          	} ?>
		          </div>
	            </div>
	          </div>
	          <div class="reversal_chargeback m-b-20" style="display: none;">
	            <div class="bg_light_gray p-10">
	              <ul class="list-unstyled mn">
	                <li>
	                  <div class="phone-control-wrap m-b-25">
	                    <div class="phone-addon w-25"><i class="text-success fs18 fa fa-check-circle"></i></div>
	                    <div class="phone-addon text-left">Reverse commissions for all products</div>
	                  </div>
	                </li>
	                <li>
	                  <div class="phone-control-wrap m-b-25">
	                    <div class="phone-addon w-25"><i class="text-success fs18 fa fa-check-circle"></i></div>
	                    <div class="phone-addon text-left">Set termination date for all products</div>
	                  </div>
	                </li>
	              </ul>
	            </div>
	          </div>

	          <div class="payment_return m-b-20" style="display: none;">
	            <div class="bg_light_gray p-10">
	              <ul class="list-unstyled mn">
	                <li>
	                  <div class="phone-control-wrap m-b-25">
	                    <div class="phone-addon w-25"><i class="text-success fs18 fa fa-check-circle"></i></div>
	                    <div class="phone-addon text-left">Reverse commissions for all products</div>
	                  </div>
	                </li>
	                <li>
	                  <div class="phone-control-wrap m-b-25">
	                    <div class="phone-addon w-25"><i class="text-success fs18 fa fa-check-circle"></i></div>
	                    <div class="phone-addon text-left">Set termination date for all products</div>
	                  </div>
	                </li>
	              </ul>
	            </div>
	          </div>

	          <div class="refund_div" style="display: none;">
		          <h4 class="m-t-0 m-b-20">Choose Products to Refund</h4>
		          <p class="error"><span id="err_prd_common"></span></p>
		          <div class="reversal_refund m-b-20">

		          	<?php if($active_products){
		          		foreach ($active_products as $product) {
		          			$coverage_periods = get_termination_date_selection_options($product['w_id']); ?>
		          			<div class="reversal_refund_row">
				              <div class="thumbnail" id="product_wrapper_<?=$product['plan_id']?>">
				                <div class="pull-left">
				                <div class="checkbox checkbox-custom mn">
				                  <input type="checkbox" id="term_chk[<?=$product['plan_id']?>]" data-plan_id ="<?=$product['plan_id']?>" class="term_checkbox js-switch" name="term_chk[<?=$product['plan_id']?>]" data-unitprice="<?=$product['price']?>" value="<?=$product['plan_id']?>" />
				                  <label for="term_chk[<?=$product['plan_id']?>]"><?=$product['name']?></label>
				                  <p class="error"><span id="err_term_check_<?=$product['plan_id']?>"></span></p>
				                  
				                </div>
				               </div>
				                <div class="pull-right">$<?=$product['price']?></div>
				              </div>
				              <div class="m-t-10 termination_date_wrapper" id="termination_date_<?=$product['plan_id']?>" style="display: none;">
				                <div class="form-group height_auto mn">
				                  <select class="form-control" name="termination_date[<?=$product['plan_id']?>]">
				                    <option></option>
				                    <?php if($coverage_periods){
					                    foreach ($coverage_periods as $coverage) { ?>
					                        <option value="<?=$coverage['value']?>"><?=$coverage['text']?></option>
					                  <?php }
					                  } ?>
				                  </select>
				                  <label>Set Termination</label>
				                  <p class="error"><span id="err_termination_date_<?=$product['plan_id']?>"></span></p>
				                </div>
				              </div>
				            </div>

		          		<?php }
		          	} ?>
		            

		          </div>

		          <h4 class="p-t-10 m-b-20">Refund Options</h4>
		          <div class="refund_option">
                <div class="phone-control-wrap m-b-25">
                  <div class="phone-addon w-25 v-align-top">
                    <input type="checkbox" id="chk_reverse_comm" class="chk_refund_options" value="Y" name="reverse_commission">
                  </div>
                  <div class="phone-addon text-left">
		              <label class="mn" for="chk_reverse_comm">Reverse commissions for the refunded product(s)? <div id="pwd_reverse_comm"></div></label>
                  </div>
                </div>
                 <!-- <div class="phone-control-wrap m-b-25">
                  <div class="phone-addon w-25 v-align-top">
                    <input type="checkbox" id="chk_cancel_future_billing" class="chk_refund_options" name="future_billing" value="Y">
                  </div>
                  <div class="phone-addon text-left">
		              <label class="mn" for="chk_cancel_future_billing"> Cancel future billings for the refunded product(s)?</label>
                  </div>
                </div> -->
                 <div class="phone-control-wrap m-b-25">
                   <div class="phone-addon w-25 v-align-top">
                    <input type="checkbox" name="inactive_member" class="chk_refund_options" value="Y" id="chk_set_term_date"> 
                   </div>
                   <div class="phone-addon text-left">
  		              <label class="mn" for="chk_set_term_date">Set a termination date for the refunded product(s)?</label>
                   </div>
                </div>
                <div class="phone-control-wrap m-b-25">
                   <div class="phone-addon w-25 v-align-top">
                    <input type="checkbox" name="chk_refund_by_check" class="chk_refund_options" value="Y" id="chk_refund_by_check"> 
                   </div>
                   <div class="phone-addon text-left">
                    <label class="mn" for="chk_refund_by_check">Refund by check?</label>
                    <input type="text" name="check_id" class="form-control m-t-20 input_check_id" placeholder="Transaction/Check ID" style="display: none;">
                    <p class="error"><span id="err_check_id"></span></p>
                   </div>
                </div>
		          <div class="form-group height_auto">
		            <p>Refund Amount</p>
		            <div class="input-group">
		              <span class="input-group-addon"><i class="fa fa-usd"></i></span>
		              <input id="refund_amount" name="refund_amount" type="text" class="form-control" name="" placeholder="$0.00" value="0.00" readonly>
		            </div>
		          </div>
		          </div>
		          <hr>
		      </div>

		      <div class="text-center action_button" style="display: none;">
	              <button class="btn btn-action" id="refund_submit">Void</button>
	              <a href="javascript:void(0);" onclick="window.location.href='payment_reversal.php'" class="btn red-link">Cancel</a>
	           </div>
	        </div>
	      </div>
	    </div>
	</form>    
</div>

<script type="text/javascript">
   $(function() {
   $('.receipt_left').matchHeight({
         target: $('.receipt_right'),
         property: 'min-height' 
     });
   });
</script>
<?php }

$html = ob_get_contents();
ob_get_clean();
echo $html;

?> 
