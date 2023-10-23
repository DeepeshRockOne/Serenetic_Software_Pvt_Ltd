<form  method="POST" id="manage_list_bill" enctype="multipart/form-data"  autocomplete="off">
	<div class="panel panel-default panel-block">
		<div class="panel-body theme-form">
			<div class="clearfix">
				<div class="pull-left">
					<h4 class="m-t-0 m-b-20">Global Setting</h4>
				</div>
			</div>
			<p class="m-b-20">Select Days Prior to Pay Period for Global List Bill Generation.</p>
			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
						<select class="form-control" id="global_day_listbill" name="global_day_listbill" >
							<?php foreach ($days_range as $day) {?>
								<option value="<?=$day?>" <?= ($day==$resListBillOptions['listbillday'])?'selected':'' ?>><?=$day?></option>
							<?php } ?>
						</select>
						<label>Select Days Prior to pay period</label>
						<p class="error" id="error_global_day_listbill"></p>
					</div>
				</div>
			<!-- <div class="col-sm-12">
                <div class="m-b-30">
                    <p>Should system auto set payment received if using a third-party payment system?</p>
                    <div class="m-b-10">
                        <label class="mn"><input type="radio" id="auto_payment" name="set_auto_payment" value="Y" <?=($auto_set_payment_received=='Y') ? 'Checked' :'' ?> >Yes</label>
                    </div>
                    <div class="m-b-10">
                        <label class="mn"><input type="radio" id="auto_payment" name="set_auto_payment" value="N" <?=($auto_set_payment_received=='N') ? 'Checked' : '' ?>>No</label>
                    </div>
                    <p class="error" id="error_set_auto_payment"></p>
                </div>
            </div> -->
            <div class="clearfix"></div>
            <p class="m-b-20">Select Days Prior to Pay Period for Auto Payment.</p>
			<div class="col-sm-4">
                <div class="form-group" id="auto_payment_days_div">
                    <select class="form-control" id="auto_payment_days" name="auto_payment_days" >
                        <option value=""></option>
                        <?php foreach ($auto_pay_day_range as $day) { ?>
                            <option value="<?= $day ?>" <?=($day==$auto_pay_day) ? 'selected' : ''?> ><?= $day ?></option>
                        <?php } ?>
                    </select>
                    <label>Select Days Prior to Pay Period for auto payment.</label>
                    <p class="error" id="error_auto_payment_days"></p>
                </div>
            </div>
			</div>
			<hr>
			<div class="clearfix tbl_filter">
				<h4 class="pull-left m-t-7">Variations Setting</h4>
				<div class="pull-right">
					<div class="m-b-15">
						<div class="note_search_wrap auto_size" id="listibill_variations_search_div" style="display: none; max-width: 100%;">
							<div class="phone-control-wrap theme-form">
								<div class="phone-addon">
									<div class="form-group">
										<a href="javascript:void(0);" class="search_close_btn text-light-gray" data-close="listbill_variations">X</a>
									</div>
								</div>
								<div class="phone-addon w-300">
									<div class="form-group">
										<input type="text"  class="form-control" id="input_listbill_variations" value="" >
										<label>Keywords</label>
									</div>
								</div>
								<div class="phone-addon w-80">
									<div class="form-group">
										<a href="javascript:void(0);" class="btn btn-info search_button" data-search="input_listbill_variations">Search</a>
									</div>
								</div>
							</div>
						</div>
						<a href="javascript:void(0);" class="search_btn" id="search_listbill_variations" ><i class="fa fa-search fa-lg text-blue"></i></a>
						<a href="javascript:void(0)" class="btn btn-action" id="add_listbill_variations">+ Variation</a>
					</div>
				</div>
			</div>
			<div id="listbill_variations_div">
			</div>
			<div class="text-center m-t-10">
				<button type="button" class="btn btn-action save" id="save">Save</button>
				<a href="payment_listbills.php" class="btn red-link cancel">Cancel</a>
			</div>
		</div>
	</div>

	
</form>

<script type="text/javascript"> 

   $(document).ready(function(){
	dropdown_pagination('listbill_variations_div');

	listbill_variations();
   })

   // $(document).off("change", "#auto_payment");
   //  $(document).on("change", "#auto_payment", function() {
   //      $('#auto_payment_days').selectpicker('val','');
   //      $val = $(this).val();
   //      if ($val == "Y") {
   //          $('#auto_payment_days_div').show();
   //      } else {
   //          $('#auto_payment_days_div').hide();
   //      }
   //      fRefresh();
   //  });
	
	listbill_variations = function(search_val) {
		$('#ajax_loader').show();
		$('#listbill_variations_div').hide();
		$.ajax({
		    url: 'listbill_variations.php',
		    type: 'GET',
		    data: {
		        is_ajaxed: 1,
		        search_val: search_val
		      },
		    success: function(res) {
		        $('#ajax_loader').hide();
		        $('#listbill_variations_div').html(res).show();
				common_select();
		      }
		    });
	}

	$(document).on("click", "#add_listbill_variations", function(e) {
		    e.preventDefault();
		    $.colorbox({
		    	href:'add_listbill_variations.php',
			  	iframe:true,
			  	width:"1085px;",
			  	height:"600px;",
			  	onClosed : function(){
					listbill_variations();
			  	}
		  	});
	});

	$(document).on("click","#save",function(){
		$("#ajax_loader").show();
		$(".error").html("");
		$.ajax({
			url:'ajax_listbill_option_global.php',
			dataType:'JSON',
			data:$("#manage_list_bill").serialize(),
			type:"POST",
			success:function(res){
				$("#ajax_loader").hide();
				if(res.status=="success"){
					window.parent.setNotifySuccess(res.msg);
				}else{
					var is_error = true;
	              	$.each(res.errors, function (index, value) {
	                  $('#error_' + index).html(value).show();
	                  if(is_error){
	                      var offset = $('#error_' + index).offset();
	                      var offsetTop = offset.top;
	                      var totalScroll = offsetTop - 50;
	                      $('body,html').animate({scrollTop: totalScroll}, 1200);
	                      is_error = false;
	                  }
	              	});
				}
			}
		});
	});

	$(document).off("click", ".search_btn");
		$(document).on("click", ".search_btn", function(e) {
			e.preventDefault();
			$(this).hide();
			$("#listibill_variations_search_div").css('display', 'inline-block');
			$("#listibill_variations_search_div").show();
	});

	$(document).off("click", ".search_close_btn");
		$(document).on("click", ".search_close_btn", function(e) {
			e.preventDefault();
			$("#listibill_variations_search_div").hide();
			$("#search_listbill_variations").show();
			$('#input_listbill_variations').val('');
			listbill_variations();
		});

		$(document).off("click", ".search_button");
		$(document).on("click", ".search_button", function(e) {
		    e.preventDefault();
		    var search_val = $('#input_listbill_variations').val();
			listbill_variations(search_val);
		});
</script>
