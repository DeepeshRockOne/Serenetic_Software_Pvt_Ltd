<?php if($report_row['report_key'] == 'agent_debit_balance' || $report_row['report_key'] == 'agent_debit_ledger') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
	    <div id="date_range" class="col-xs-4">
	       <div class="form-group height_auto pn">
	          <select class="form-control" id="join_range" name="join_range">
		            <option value="Before" selected>Before</option>
	          </select>
	          <label>As of Date</label>
	          <p class="error"><span class="error_join_range"></span></p>
	       </div>
	    </div>
	    <div class="select_date_div col-xs-8" >
	       	<div class="form-group height_auto pn">
				<div id="all_join">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						<div class="pr">
							<input type="text" name="added_date" id="added_date" value="" class="form-control date_picker" placeholder="MM / DD / YYYY" />
						</div>
					</div>
					<p class="error text-left"><span class="error_added_date"></span></p>
				</div>
	       	</div>
	    </div>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
		    $(".date_picker").datepicker({
				changeDay: true,
				changeMonth: true,
				changeYear: true,
	            autoclose:true,
			});
		});
	</script>
<?php } ?>