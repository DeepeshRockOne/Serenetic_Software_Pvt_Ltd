<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="mn">New Coverage Details</h4>
	</div>
	<div class="panel-body">
		<form name="frm_policy" id="frm_policy" method="POST" action="">
		  <input type="hidden" id="ws_id" name="ws_id" value="<?=$ws_row['id']?>">
		  <input type="hidden" id="effective_date" name="effective_date" value="<?=$effective_date?>">
		  <input type="hidden" id="location" name="location" value="<?=$location?>">
		  <div  class="white-box">
		    <div class="clearfix m-b-15">
		      <h4 class="mn">Plan Details</h4>
		    </div>
		    <div class="table-responsive">
		      <table class="<?=$table_class?>">
		        <tbody>
		        	<tr>
	                  <td class="fw500">Subscription ID</td>
	                  <td><a href="javascript:void(0);" class="text-action fw500"><?=$ws_row['website_id']?></a></td>
	                </tr>
	                <tr>
	                  <td class="fw500">Product/ID</td>
	                  <td><?=$ws_row['name']?><br><a href="javascript:void(0);" class="text-action fw500"><?=$ws_row['product_code']?></a></td>
	                </tr>
	                <tr>
	                  <td class="fw500">Current Start Coverage</td>
	                  <td><?=displayDate($startCoveragePeriod)?></td>
	                </tr>
	                <tr>
	                  <td class="fw500">Current End Coverage</td>
	                  <td><?=displayDate($endCoveragePeriod)?></td>
	                </tr>
	                <tr>
	                  <td class="fw500">Effective Date</td>
	                  <td><?=displayDate($effective_date)?></td>
	                </tr>
	                <tr>
	                  <td class="fw500">Next Purchase Date</td>
	                  <td><?=displayDate($next_billing_date)?></td>
	                </tr>
		        </tbody>
		      </table>
		    </div>
		  </div>
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default panel-block panel-title-block">
		        <div class="panel-body ">
		          <div class="clearfix m-b-15">
		            <h4 class="mn">Coverage Periods</h4>
		          </div>
		          <div class="table-responsive">
		            <table class="<?=$table_class?>">
		              <thead>
		                <tr>
						<?php if($sponsor_billing_method != 'list_bill'){ ?>
		                  <th>Period</th>
						<?php } ?>
		                  <th>Start Coverage</th>
		                  <th>End Coverage</th>
		                </tr>
		              </thead>
		              <tbody>
		                <?php if($new_coverage_periods){ ?>
		                <?php foreach ($new_coverage_periods as $coverage_period) { ?>
		                <tr>
		                  <?php
		                  $last_coverage = $coverage_period['renew_count'] + 1;  
		                  $start_coverage_period = strtotime($coverage_period['start_coverage_period']) > 0 ? displayDate($coverage_period['start_coverage_period']) : "";
		                      $end_coverage_period = strtotime($coverage_period['end_coverage_period']) > 0 ? displayDate($coverage_period['end_coverage_period']) : "";

		                  ?>
		                  <?php if($sponsor_billing_method != 'list_bill'){ ?>
		                  <td class="fw500">P<?=$coverage_period['renew_count']?>:</td>
						  <?php } ?>  
		                  <td><?=$start_coverage_period?></td>
		                  <td><?=$end_coverage_period?></td>
		                </tr>  
		                <?php } ?>
		                <?php } ?>
		                
		              </tbody>
		            </table>
		          </div>
		        </div>
		      </div>
		    </div>
		  </div>
		  <hr>
          <div class="clearfix m-b-15">
            <h4 class="mn">Dependents</h4>
          </div>
          <div class="table-responsive">
            <table class="<?=$table_class?>">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Relation</th>
                  <th width="25%">New Effective Date</th>
                </tr>
              </thead>
              <tbody>
                <?php if($dependents){ ?>
                <?php foreach ($dependents as $dependent) {?>
                    <tr>
                      <td><?=$dependent['fname'] . ' ' . $dependent['lname']?></td>
                      <?php $relation = "";
                      if (in_array(ucfirst($dependent['relation']), array('Wife', 'Husband'))) {
                          $relation = 'Spouse';
                      } else {
                          $relation = 'Child';
                      }
                      ?>
                      <td><?=$relation?></td>
                      <td><?=displayDate($effective_date)?></td>
                    </tr>
                <?php } ?>
                <?php }else{ ?>
                  <tr><td colspan="4">No Records.</td></tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
          <div class="text-center">
			<button id="submit" class="btn btn-action">Confirm</button>
			<a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close();">Cancel</a>
		  </div>
		</form>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$("#submit").off('click');
		$("#submit").on('click',function(e){
			e.preventDefault();
			parent.disableButton($(this));
			$new_effective_date = $('#effective_date').val();
			$ws_id = $('#ws_id').val();
			$location = $('#location').val();
			$('#ajax_loader').show();
			   $.ajax({
			   url: 'ajax_change_effective_date.php',
			   type: 'POST',
			   data: {ws_id:$ws_id,effective_date:$new_effective_date,location:$location},
			   dataType: 'JSON',
			   success: function(res) {
			     $('#ajax_loader').hide();
			     if(res.status == 'fail'){
			     	parent.setNotifySuccess(res.message);
			    	parent.$.colorbox.close();
			         // $('#err_effective_date').text(res.error);
			        parent.location.reload(); 
			     }else{
			     	// window.parent.$('.effective_td_' + res.product_id).html(res.effective_date);
			     	parent.setNotifySuccess(res.message);
			    	parent.$.colorbox.close();
			    	parent.location.reload();
				 }
				 parent.enableButton($("#submit"));
			   }
			});
		});
	});
</script>