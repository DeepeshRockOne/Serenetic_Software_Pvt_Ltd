<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$report_id = isset($_GET['id']) ? $_GET['id'] : "";
$report_row = $pdo->selectOne("SELECT * FROM $REPORT_DB.rps_reports WHERE md5(id)=:id",array(':id' => $report_id));
$filter_fields = array();
if($report_row['report_key'] == 'agent_quick_sales_summary') { //Quick Sales Summary
	

}elseif($report_row['report_key'] == 'agent_monthly_forecasting'){ //Monthly Forecasting
	
}elseif($report_row['report_key'] == 'agent_product_persistency'){ //Product Persistency
	
}
function generateDateRange($label = '',$id=''){
	if($label == '') {
		$label = 'Added Date';
	}
	$id_label = '';
	if($id !=''){
		$id_label = $id.'_';
	}
echo '<div id="date_range" class="col-xs-4">
	       <div class="form-group height_auto pn">
	          <select class="form-control" id="'.$id_label.'join_range" name="'.$id_label.'join_range">
        			<option></option>
		            <option value="Exactly">Exactly</option>
		            <option value="Before">Before</option>
		            <option value="After">After</option>
		            <option value="Range">Range</option>
	          </select>
	          <label>'.$label.'</label>
	          <p class="error"><span class="error_'.$id_label.'join_range"></span></p>
	       </div>
	    </div>
	    <div class="select_date_div col-xs-8" >
	       	<div class="form-group height_auto pn">
				<div id="'.$id_label.'all_join">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						<div class="pr">
							<input type="text" name="'.$id_label.'added_date" id="'.$id_label.'added_date" value="" class="form-control date_picker" placeholder="MM / DD / YYYY" />
						</div>
					</div>
					<p class="error text-left"><span class="error_'.$id_label.'added_date"></span></p>
				</div>

				<div  id="'.$id_label.'range_join" style="display:none;">
				 	<div class="phone-control-wrap">
					    <div class="phone-addon">
					       <label class="mn">From</label>
					    </div>
					    <div class="phone-addon">
					       <div class="input-group">
					          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					          <div class="pr">
					             <input type="text" name="'.$id_label.'fromdate" id="'.$id_label.'fromdate" value="" class="form-control date_picker" placeholder="MM / DD / YYYY" />
					          </div>
					       </div>
					    	<p class="error text-left"><span class="error_'.$id_label.'fromdate"></span></p>
					    </div>

					    <div class="phone-addon">
					       <label class="mn">To</label>
					    </div>
					    <div class="phone-addon">
					       <div class="input-group">
					          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					          <div class="pr">
					             <input type="text" name="'.$id_label.'todate" id="'.$id_label.'todate" value="" class="form-control date_picker" placeholder="MM / DD / YYYY" />
					          </div>
					       </div>
					    	<p class="error text-left"><span class="error_'.$id_label.'todate"></span></p>
					    </div>
				 	</div>
				</div>
	       	</div>
	    </div>';
}
$exStylesheets = array(
	'thirdparty/multiple-select-master/multiple-select.css'.$cache,
);
$exJs = array(
	'thirdparty/multiple-select-master/multiple-select-old/jquery.multiple.select.js'.$cache,
	'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
	'thirdparty/masked_inputs/jquery.maskedinput.min.js',
);
$template = "generate_report.inc.php";
include_once 'layout/iframe.layout.php';
?>