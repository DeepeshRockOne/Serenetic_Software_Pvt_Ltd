<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <div class="panel-title">
      <h4 class="mn">Generate - <span class="fw300">All  Report 001</span></h4>
    </div>
  </div>
  <div class="panel-body p-b-0">
    
        <p>This report will show data of <?= $DEFAULT_SITE_NAME ?> related to <?= $DEFAULT_SITE_NAME ?> reporting. Lorem ispumt cinc el ispum.</p>

        <div class="row theme-form" id="show_date">
          <div id="date_range" class="col-xs-12">
            <div class="form-group">
              <select class="form-control" id="join_range" name="join_range">
                <option value=""> </option>
                <option value="Exactly">Exactly</option>
              </select>
              <label>Date</label>
            </div>
          </div>
          <div class="select_date_div col-xs-9" style="display:none">
            <div class="form-group">
              <div id="all_join" class="input-group"> 
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="text" name="added_date" id="added_date" value="" class="form-control date_picker" />
              </div>
              <div  id="range_join" style="display:none;">
                <div class="phone-control-wrap">
                  <div class="phone-addon">
                    <label class="mn">From</label>
                  </div>
                  <div class="phone-addon">
                    <div class="input-group"> 
                      <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                      <input type="text" name="fromdate" id="fromdate" value="" class="form-control date_picker" />
                    </div>
                  </div>
                  <div class="phone-addon">
                    <label class="mn">To</label>
                  </div>
                  <div class="phone-addon">
                    <div class="input-group"> 
                      <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                      <input type="text" name="todate" id="todate" value="" class="form-control date_picker" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    <div class="p-15 text-right">
    <a href="javascript:void(0);" class="btn btn-action">Export</a>
    <a href="javascript:void(0);" class="btn red-link">Cancel</a>
  </div>
</div>            
<script type="text/javascript">
  $(document).ready(function(){
    $(document).off('change', '#join_range');
$(document).on('change', '#join_range', function(e) {
  e.preventDefault();
  $('.date_picker').val('');
  if($(this).val() == ''){
    $('.select_date_div').hide();
    $('#date_range').removeClass('col-xs-3').addClass('col-xs-12');
  }else{
    $('#date_range').removeClass('col-xs-12').addClass('col-xs-3');
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
  })
</script>