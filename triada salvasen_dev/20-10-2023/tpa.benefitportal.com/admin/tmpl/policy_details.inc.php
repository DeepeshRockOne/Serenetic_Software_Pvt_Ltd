<form name="frm_policy" id="frm_policy" method="POST" action="">
  <input type="hidden" name="ws_id" value="<?=$ws_row['id']?>">
  <div  class="white-box">
    <div class="clearfix m-b-15">
      <h4 class="mn">Details</h4>
    </div>
    <div class="table-responsive">
      <table class="<?=$table_class?>">
        <thead>
          <tr>
            <th>Subscription ID</th>
            <th>Product/ID</th>
            <th>Status</th>
            <th width="15%">Effective Date</th>
            <th width="15%">Termination Date</th>
            <th width="100px">Plan Doc</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><a href="javascript:void(0);" class="text-action fw500"><?=$ws_row['website_id']?></a></td>
            <td><?=$ws_row['name']?><br><a href="javascript:void(0);" class="text-action fw500"><?=$ws_row['product_code']?></a></td>
            <td><?=get_policy_display_status($ws_row['status'])?></td>
            <td>
              <div class="theme-form">
                <div class="input-group w-200">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  <div class="pr">
                    <input id="tmp_effective_date" type="text" class="form-control" name="primary_effective_date" placeholder="" value="<?=$effective_date?>" <?=$effective_date_enable ? "" : "readonly"?> onkeydown="return false">
                    <label>MM/DD/YYYY</label>
                  </div>
                </div>
                <p class="error"><span id="err_primary_effective_date"></span></p>
              </div>
            </td>
            <td>
              <div class="theme-form pr">
                <?php $term_periods = get_termination_date_selection_options($ws_row['id']); ?>
                   <select class="form-control" name="primary_termination_date" id="primary_termination_date">
                      <?php if($allow_cancel_termination == true) { ?>
                        <option value=""></option>
                      <?php } ?>
                      <?php if($term_periods){
                        foreach ($term_periods as $coverage) { 
                          if($allow_cancel_termination == false && !empty($max_termination_date)) {
                            if(strtotime($coverage['value']) > strtotime($max_termination_date)) {
                              continue;
                            }
                          }
                      ?>
                            <option value="<?=$coverage['value']?>" <?=strtotime($coverage['value']) == strtotime($ws_row['termination_date']) ? "selected='selected'" : ""?>><?=$coverage['text']?></option>
                      <?php }
                      } ?>
                   </select>
                   <label>Select</label>
              </div>
            </td>
            <td class="icons text-right">
              <a href="../policy_document.php?userType=Policy&customer_id=<?=md5($ws_row['customer_id'])?>&ws_id=<?=md5($ws_row['id'])?>" data-title='Plan Document' data-toggle="tooltip" data-trigger="hover"><i class="fa fa-download" aria-hidden="true"></i></a>
              <?php if(!empty($resAgreement["id"])){ ?>
                <a href="../joinder_document.php?id=<?=md5($resAgreement['id'])?>" data-title='Joinder Agreement' data-toggle="tooltip" data-trigger="hover"><i class="fa fa-download" aria-hidden="true"></i></a>
              <?php } ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6">
      <div class="panel panel-default panel-block panel-title-block">
        <div class="panel-body ">
          <div class="clearfix m-b-15">
            <h4 class="mn">Plan Information</h4>
          </div>
          <div class="table-responsive">
            <table class="<?=$table_class?>">
              <tbody>
                <tr>
                  <td class="fw500">Member ID</td>
                  <td class="text-right"><?=$ws_row['rep_id']?></td>
                </tr>
                <tr>
                  <td class="fw500">Plan Holder</td>
                  <td class="text-right"><?=$ws_row['member_name']?></td>
                </tr>
                <tr>
                  <td class="fw500">Current Plan Start</td>
                  <td class="text-right"><?=displayDate($ws_row['start_coverage_period'])?></td>
                </tr>
                <tr>
                  <td class="fw500">Current Plan End</td>
                  <td class="text-right"><?=displayDate($ws_row['end_coverage_period'])?></td>
                </tr>
                <tr>
                  <td class="fw500">Next Billing Date</td>
                  <td class="text-right"><?=displayDate($ws_row['next_purchase_date'])?></td>
                </tr>
                <tr>
                  <td class="fw500">Effective Date</td>
                  <td class="text-right"><?=displayDate($ws_row['eligibility_date'])?></td>
                </tr>
                <tr>
                  <td class="fw500">Termination Date</td>
                  <td class="text-right"><?=displayDate($ws_row['termination_date'])?></td>
                </tr>
                <tr>
                  <td class="fw500">Date Terminated</td>
                  <td class="text-right"><?=displayDate($date_terminated)?></td>
                </tr>
                <tr>
                  <td class="fw500">Termination Reason</td>
                  <td class="text-right">
                    <select name="term_reason" class="form-control" id="term_reason">
                      <?php if($allow_cancel_termination == true) { ?>
                        <option value=""></option>
                      <?php } ?>
                      <?php 
                        if($term_reasons){
                          foreach ($term_reasons as $reason) { ?>
                            <option value="<?=$reason['name']?>" <?=$reason['name'] == $ws_row['termination_reason'] ? "selected = 'selected'" : ""?>><?=$reason['name']?></option>
                         <?php }
                        }
                      ?>
                    </select>

                  </td>
                </tr>
                <tr>
                  <td class="fw500">Plan Tier</td>
                  <td class="text-right"><?=$ws_row['benefit_tier']?></td>
                </tr>
                <tr>
                  <td class="fw500">Retail Price</td>
                  <td class="text-right"><?=displayAmount($ws_row['price'],2)?>/month</td>
                </tr>
              </tbody>
            </table>
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
                  <th width="25%">Effective Date</th>
                  <th width="25%">Termination Date</th>
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

                      $dep_effective_date = strtotime($dependent['eligibility_date']) > 0 ? displayDate($dependent['eligibility_date']) : "";
                      $dep_termination_date = strtotime($dependent['terminationDate']) > 0 ? displayDate($dependent['terminationDate']) : "";

                      ?>
                      <td><?=$relation?></td>
                      <input type="hidden" name="dependent_ids[]" value="<?=$dependent['id']?>">
                      <td>
                        <div class="theme-form">
                          <div class="input-group mw150">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <div class="pr">
                              <input id="" type="text" class="form-control date_picker" name="dependent_effective_date[<?=$dependent['id']?>]" value="<?=$dep_effective_date?>" placeholder="">
                              <label>MM/DD/YYYY</label>
                            </div>
                          </div>
                          <p class="error"><span id="err_dependet_effective_date_<?=$dependent['id']?>"></span></p>
                        </div>
                      </td>
                      <td>
                    <div class="theme-form">
                      <select class="form-control" name="dependent_termination_date[<?=$dependent['id']?>]">
                          <option value=""></option>
                          <?php if($term_periods){
                            foreach ($term_periods as $coverage) { ?>
                                <option value="<?=$coverage['value']?>" <?=strtotime($coverage['value']) == strtotime($dependent['terminationDate']) ? "selected='selected'" : ""?>><?=$coverage['text']?></option>
                          <?php }
                          } ?>
                       </select>
                       <p class="error"><span id="err_dependet_termination_date_<?=$dependent['id']?>"></span></p>
                    </div>
                  </td>
                    </tr>
                <?php } ?>
                <?php }else{ ?>
                  <tr><td colspan="4">No Records.</td></tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="panel panel-default panel-block panel-title-block">
        <div class="panel-body ">
          <div class="clearfix m-b-15">
            <h4 class="mn">Plan Periods</h4>
          </div>
          <div class="table-responsive">
            <table class="<?=$table_class?>">
              <thead>
                <tr>
                  <th <?=($is_list_bill) ? "hidden" : '' ?>>Period</th>
                  <th>Start</th>
                  <th></th>
                  <th>End</th>
                  <th>ID/Status</th>
                  <th>Trans Date</th>
                </tr>
              </thead>
              <tbody>
                <?php if($coverage_periods){ ?>
                <?php foreach ($coverage_periods as $coverage_period) { ?>
                <tr>
                  <?php
                  $last_coverage = $coverage_period['renew_count'] + 1;  
                  $start_coverage_period = strtotime($coverage_period['start_coverage_period']) > 0 ? displayDate($coverage_period['start_coverage_period']) : "";
                      $end_coverage_period = strtotime($coverage_period['end_coverage_period']) > 0 ? displayDate($coverage_period['end_coverage_period']) : "";

                  ?>
                  <input type="hidden" name="order_detail_ids[]" value="<?=$coverage_period['id']?>">
                  <td class="fw500" <?=($is_list_bill) ? "hidden" : '' ?>>
                    <select name="renew_count[<?=$coverage_period['id']?>]" class="form-control">
                      <?php
                        for ($rc=1; $rc <= $renew_count_limit; $rc++) {
                          echo '<option value="'.$rc.'" '.($coverage_period['renew_count'] == $rc?'selected':'').'>P'.$rc.'</option>';    
                        }
                      ?>
                    </select>
                  </td>
                  <td>
                    <div class="theme-form">
                      <div class="input-group w-160">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <div class="pr">
                          <input id="" type="text" class="form-control date_picker" name="start_coverage_period[<?=$coverage_period['id']?>]" value="<?=$start_coverage_period?>" placeholder="">
                          <label>MM/DD/YYYY</label>
                        </div>
                      </div>
                        <p class="error"><span id="err_start_coverage_period_<?=$coverage_period['id']?>"></span></p>
                    </div>
                  </td>
                  <td>-</td>
                  <td>
                    <div class="theme-form">
                      <div class="input-group w-160">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <div class="pr">
                          <input id="" type="text" class="form-control date_picker" name="end_coverage_period[<?=$coverage_period['id']?>]" placeholder="" value="<?=$end_coverage_period?>">
                          <label>MM/DD/YYYY</label>
                        </div>
                      </div>
                        <p class="error"><span id="err_end_coverage_period_<?=$coverage_period['id']?>"></span></p>
                    </div>
                  </td>
                  <td class="text-nowrap"><a class="text-action"><?=$coverage_period['display_id']?></a><br>
                    <?=$coverage_period['status']?></td>
                  <td><?=displayDate($coverage_period['created_at'])?></td>
                </tr>  
                <?php } ?>
                <?php } ?>
                
              </tbody>
            </table>
          </div>
          <hr class="m-t-0">
          <div class="theme-form">
            <p>Next Billing Date</p>
            <div class="form-group ">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <div class="pr">
                  <input id="next_billing_date" type="text" class="form-control date_picker" name="next_billing_date" placeholder="" <?=$is_allow_next_billing == false ? 'disabled' : ""?> value="<?=$next_purchase_date?>">
                  <label>MM/DD/YYYY</label>
                </div>
              </div>
              <p class="error"><span id="err_next_billing_date"></span></p>
            </div>
          </div>
          <div class="text-center m-b-15">
            <button type="button" id="submit" class="btn btn-action">Save</button>
            <a href="javascript:void(0);" class="btn red-link">Cancel</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

<script type="text/javascript">
$(document).ready(function(){
  $(".date_picker").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true
      });

  <?php if(!empty($coverage_period_data)) { ?>
    $disableDays = <?=json_encode($disableDays)?>;
      $("#tmp_effective_date").datepicker({
        beforeShowDay: function(date){
          if($disableDays.length > 0){
            pickerDate = date.getDate();
            pickerDate = pickerDate.toString();
            if (pickerDate.length == 1){
                  pickerDate = '0' + pickerDate;
            }
            if ($.inArray(pickerDate , $disableDays) !== -1) {
              return false;
            }else{
              return true;
            }
          }
        },
        startView: <?=$coverage_period_data['startView']?>,
        minViewMode: <?=$coverage_period_data['minViewMode']?>,
        <?php 
        if(!empty($earliest_effective_date)) {
        echo "startDate:'".date("m/d/Y",strtotime($earliest_effective_date))."',";
        }
        ?>
      }).on('changeDate', function(){
        if($(this).val() == ''){
          return false;
        }
        var effective_date = $(this).val();
        $.colorbox({
            iframe:true,
            href:"<?=$HOST?>/coverage_details.php?ws_id=<?=$_GET['ws_id']?>&effective_date=" + effective_date,
            width: '700px',
            height: '500px',
        });
      });;
		<?php } else { ?>
      $("#tmp_effective_date").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true,
         <?php 
          if(!empty($earliest_effective_date)) {
          echo "startDate:'".date("m/d/Y",strtotime($earliest_effective_date))."',";
          }
        ?>
      }).on('changeDate', function(){
        if($(this).val() == ''){
          return false;
        }
        var effective_date = $(this).val();
        $.colorbox({
            iframe:true,
            href:"<?=$HOST?>/coverage_details.php?ws_id=<?=$_GET['ws_id']?>&effective_date=" + effective_date,
            width: '700px',
            height: '500px',
        });
      });
  <?php } ?>


  $(document).on('change','#primary_termination_date',function(){
    if($(this).val() != ""){
      var current_date = new Date();
      var new_term_date = new Date($(this).val());
      if(current_date < new_term_date){
        $('#next_billing_date').prop('disabled', false);
      }else{
        $('#next_billing_date').prop('disabled', true);
      }
    }else{
       $('#next_billing_date').prop('disabled', false);
    }
  });
});  
$(document).on('click','#submit',function(){

      $('#ajax_loader').show();
      // $('#ajax_data').hide();
      $('.error span').html('');
      var params = $('#frm_policy').serialize();
      $.ajax({
        url: 'ajax_update_policy.php',
        type: 'POST',
        data: params,
        dataType: 'JSON', 
        success: function(res) {
          if(res.status == 'success'){
            $('#is_ajax').val('');
            $('#ajax_loader').hide();
            window.location.reload();

          }else{
            $('#ajax_loader').hide();
            var is_error = true;
            $.each(res.errors, function(index, error) {
              console.log('#err_' + index);
              $('#err_' + index).html(error);
              if (is_error) {
                var offset = $('#err_' + index).offset();
                if (typeof(offset) === "undefined") {
                  console.log("Not found : " + index);
                } else {
                  var offsetTop = offset.top;
                  var totalScroll = offsetTop - 195;
                  $('body,html').animate({
                    scrollTop: totalScroll
                  }, 1200);
                  is_error = false;
                }
              }
            });
          }

          common_select();
        }
      });
      return false;

  });

</script>  