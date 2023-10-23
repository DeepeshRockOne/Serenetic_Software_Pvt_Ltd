<div class="panel panel-default panel-block enrollment_status">
	<div class="panel-heading">
		<div class="panel-title">
			<h4 class="mn">Enrollment Verification - 
				<?php if($is_add_product == 'N'){ ?>
				<span class="fw300">(<?=$lead_quote_row['lead_rep_id'];?>) <?=$lead_quote_row['fname'].' '.$lead_quote_row['lname'];?></span>
				<?php }else{ ?>
					<span class="fw300">(<?=$customer_row['rep_id'];?>) <?=$customer_row['fname'].' '.$customer_row['lname'];?></span>
				<?php } ?>
				<button class="btn btn-action pull-right" id="btn_close" type="button">End Enrollment</button>
			</h4>
		</div>
	</div>
	<div class="panel-body">
		<div class="">
			<p class="fs16 m-b-20">Watch live as 
				<?php if($is_add_product == 'N'){ ?>
					<strong class="text-blue"><?=$lead_quote_row['lead_rep_id'];?> - <?=$lead_quote_row['fname'].' '.$lead_quote_row['lname'];?></strong>
				<?php }else{ ?>
					<strong class="text-blue"><?=$customer_row['rep_id'];?> - <?=$customer_row['fname'].' '.$customer_row['lname'];?></strong>
				<?php } ?>	
				 completes the enrollment verification process…</p>
			<ul>
				<li><i class="material-icons text-success">check_circle</i><?=strtolower($sent_via) == "both"?"Email/Text":ucfirst($sent_via)?> Message Verification Delivered</li>
				<?php if($lead_quote_row['is_opened'] == "N") { ?>
					<li><i class="fa fa-spinner fa-pulse"></i><span class="sr-only">Loading...</span>Enrollment Link Opened</li>
				<?php } else { ?>
					<li><i class="material-icons text-success">check_circle</i>Enrollment Link Opened</li>
					<?php if($sponsor_billing_method != "individual") { ?>
						<?php if(count($group_ord_row) == 0) { ?>
							<li><i class="fa fa-spinner fa-pulse"></i><span class="sr-only">Loading...</span>Pending Review and Payment</li>
						<?php } else { 
							$is_approved_tran = true;
							?>
							<li><i class="material-icons text-success">check_circle</i>Pending Review and Payment</li>
							<li><i class="material-icons text-success">check_circle</i>Enrollment Verification Completed</li>
						<?php } ?>
					<?php } else { ?>
						<?php if(count($tran_res) == 0) { ?>
							<li><i class="fa fa-spinner fa-pulse"></i><span class="sr-only">Loading...</span>Pending Review and Payment</li>
						<?php } else { ?>
							<li><i class="material-icons text-success">check_circle</i>Pending Review and Payment</li>

							<?php if($ord_row['future_payment'] == "Y") { $is_approved_tran = true; ?>
								<li><i class="material-icons text-success">check_circle</i>Post Payment Set</li>

							<?php } else { ?>
								<li><i class="material-icons text-success">check_circle</i>Payment Attempted</li>
								<?php
									foreach ($tran_res as $key => $tran_row) {
										if($tran_row['transaction_type'] == "New Order") { //CC
											echo '<li class="sub_status text-success"><i class="material-icons">check_circle</i>Payment Approved</li>';
											$is_approved_tran = true;
											?>
											<?php if(in_array($enrollmentLocation,array("agentSide","aae_site","self_enrollment_site"))) { ?>
												<li><i class="material-icons text-success">check_circle</i>Enrollment Completed - <a href="javascript:void(0);" class="btn red-link pn odrReceipt" data-odrId="<?=md5($ord_row["id"])?>"><?=$ord_row['display_id']?></a>
													<?php if($customer_row['is_password_set'] == "N") { ?> 
														<strong class=""><?=$customer_row['rep_id']?> - <?=$customer_row['fname']?> <?=$customer_row['lname']?> <br/>
														<p class="m-l-30">A temporary password will be sent to <?=$customer_row['email']?>. Please encourage the member to check their spam/junk mail.</p></strong>
													<?php } ?> 
												</li>
											<?php } else { ?>
												<li><i class="material-icons text-success">check_circle</i>Enrollment Completed!
													<?php if($customer_row['is_password_set'] == "N") { ?> 
														<strong class=""><?=$customer_row['rep_id']?> - <?=$customer_row['fname']?> <?=$customer_row['lname']?> <br/>
														<p class="m-l-30">A temporary password will be sent to <?=$customer_row['email']?>. Please encourage the member to check your spam/junk folder.</p></strong>
													<?php } ?> 
												</li>
											<?php } ?>
											<?php

										} elseif($tran_row['transaction_type'] == "Pending") { //ACH Pending Settlement
											echo '<li class="sub_status text-warning"><i class="material-icons text-success">check_circle</i>Payment Approved</li>';
											$is_approved_tran = true;

										} elseif($tran_row['transaction_type'] == "Post Payment") { //ACH Pending Settlement
											echo '<li class="sub_status text-warning"><i class="material-icons text-success">check_circle</i>Post Payment Set</li>';
											$is_approved_tran = true;

										} else {
											$declined_reason = get_declined_reason_from_tran_response($tran_row['transaction_response']);
											echo '<li class="sub_status text-action"><i class="material-icons">cancel</i><strong>Payment Declined </strong> (Reason : '.$declined_reason.')</li>';
										}

										if($is_approved_tran == true) {
											break;
											//Stop foreach after approved trans found
										}
									}
								?>
							<?php } ?>						
						<?php } ?>
					<?php } ?>
				<?php } ?>
			</ul>
		</div>
	</div>
	
	<?php if($is_approved_tran == false) { ?>
	<div class="panel-footer text-center">
		<a href="javascript:void(0);" class="btn btn-action btn_update_enrollment" onclick="parent.$.colorbox.close();">Update Enrollment</a>
	</div>
	<?php } ?>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('#cboxClose').remove();
		setTimeout(function(){
			window.location.reload();
		},10000);

		$(document).off("click","#btn_close");
		$(document).on("click","#btn_close",function(){
			<?php if($enrollmentLocation == "adminSide") { ?>
				parent.reload_page();
			<?php } else { ?>
				parent.reload_page(true);
			<?php } ?>
		});

		$(document).off('click', '.odrReceipt');
	    $(document).on('click', '.odrReceipt', function(e) {
	      var odrId = $(this).attr("data-odrId");
	      $href = "<?=$HOST?>/order_receipt.php?orderId="+odrId;
	      window.open($href, "myWindow", "width=1024,height=630");
	    });
	});
</script>