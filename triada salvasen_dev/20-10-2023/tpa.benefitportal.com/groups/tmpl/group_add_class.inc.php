<style type="text/css">
    .group_add_class .red-link{padding: 6px 20px;}
</style>
<div class="section-padding">
    <div class="container">
        <div class="panel panel-default ">
            <div class="panel-body theme-form">
                <div id="addClassDiv" style="<?= !empty($resData) ? 'display: none' : '' ?>">
                  <h4>+ Classes</h4>
                  <p>Add up to 10 classes below (ie. Executive, Full Time, Part-Time etc.).</p>
                  <div class="bg_light_gray p-10 clearfix">
                      <?php if(!empty($resClass)) { ?>
                        <?php foreach ($resClass as $key => $value) { ?>
                             <a href="<?= $GROUP_HOST ?>/group_add_class.php?class=<?= $value['id'] ?>" class="btn btn-info btn-outline" ><?= $value['class_name'] ?></a>
                        <?php } ?>
                      <?php } ?>
                      <a href="javascript:void(0);" class="btn btn-info btn-outline" id="add_class_text" style="display: none">+ Class</a>
                      <a href="javascript:void(0);" class="btn btn-action-o pull-right" id="add_class">+ Class</a>
                  </div>
                  <div class="p-10 group_add_class">
                      <?php if(!empty($resClass)) { ?>
                        <?php foreach ($resClass as $key => $value) { ?>
                            <a href="javascript:void(0);" class="btn red-link remove_class" data-id="<?= $value['id'] ?>">Remove</a>
                        <?php } ?>
                      <?php } ?>
                      <a href="javascript:void(0);" class="btn red-link" id="remove_class_div" style="display: none">Remove</a>
                  </div>
                  <hr>
                </div>
                
                <div id="add_class_div" style="<?= !empty($resData) ? '' : 'display: none' ?>">
                  <form  method="POST" id="add_class_form" enctype="multipart/form-data"  autocomplete="off">
                    <input type="hidden" name="group_classes_id" id="group_classes_id" value="<?= $group_classes_id ?>">
                    <p class="fs24 fw300">+ Class</p>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <input type="text" name="class_name" id="class_name" class="form-control" value="<?= !empty($class_name) ? $class_name : '' ?>">
                                <label>Name Class</label>
                                <p class="error" id="error_class_name"></p>
                            </div>
                        </div>
                    </div>
                    <p class="fs24 fw300">Enrollee Type Application Rules</p>
                    <div class="col-sm-12">
                      <div class="m-b-30">
                          <p class="fw500">When is an EXISTING member of this class eligible for plan?</p>
                          <div class="m-b-10">
                            <label class="mn"><input type="radio" name="existing_member_eligible_coverage" value="Immediately" <?= !empty($existing_member_eligible_coverage) && $existing_member_eligible_coverage=="Immediately" ? 'checked' : '' ?>> Immediately</label>
                          </div>
                          <div class="form-inline">
                             <div class="form-group height_auto mn">
                              <label><input type="radio" name="existing_member_eligible_coverage" value="SelectDay" <?= !empty($existing_member_eligible_coverage) && $existing_member_eligible_coverage!="Immediately" ? 'checked' : '' ?>> The next effective date following</label>
                             </div>
                             <div class="form-group height_auto mn">
                              <select class="form-control" name="existing_member_eligible_coverage_day" data-live-search="true">
                                  <?php for($i=1;$i<=120;$i++) { ?>
                                    <option value="<?= $i ?>" <?= !empty($existing_member_eligible_coverage) && $existing_member_eligible_coverage!="Immediately" && $existing_member_eligible_coverage == $i ? 'selected' : '' ?>><?= $i ?></option>
                                  <?php } ?>                               
                              </select>
                             </div>
                             <div class="form-group height_auto mn">
                              <label>days from the relationship date.</label>
                             </div>
                            <p class="error" id="error_existing_member_eligible_coverage"></p>
                          </div>
                      </div>
                      <div class="m-b-30">
                          <p class="fw500">When is a NEW member of this class eligible for plan?</p>
                          <div class="m-b-10">
                            <label class="mn"><input type="radio" name="new_member_eligible_coverage" value="Immediately" <?= !empty($new_member_eligible_coverage) && $new_member_eligible_coverage=="Immediately" ? 'checked' : '' ?>> Immediately</label>
                          </div>
                          <div class="form-inline">
                             <div class="form-group height_auto mn">
                              <label><input type="radio" name="new_member_eligible_coverage" value="SelectDay" <?= !empty($new_member_eligible_coverage) && $new_member_eligible_coverage!="Immediately" ? 'checked' : '' ?>> The next effective date following</label>
                             </div>
                             <div class="form-group height_auto mn" >
                              <select class="form-control " name="new_member_eligible_coverage_day" data-live-search="true">
                                  <?php for($i=1;$i<=120;$i++) { ?>
                                    <option value="<?= $i ?>" <?= !empty($new_member_eligible_coverage) && $new_member_eligible_coverage!="Immediately" && $new_member_eligible_coverage == $i ? 'selected' : '' ?>><?= $i ?></option>
                                  <?php } ?>
                              </select>
                             </div>
                             <div class="form-group height_auto mn">
                              <label>days from the relationship date.</label>
                             </div>
                             <p class="error" id="error_new_member_eligible_coverage"></p>
                          </div>
                      </div>
                      <div class="m-b-30">
                          <p class="fw500">When is a RENEWED member of this class eligible for plan?</p>
                          <div class="m-b-10">
                            <label class="mn"><input type="radio" name="renewed_member_eligible_coverage" value="Immediately" <?= !empty($renewed_member_eligible_coverage) && $renewed_member_eligible_coverage=="Immediately" ? 'checked' : '' ?>> Immediately</label>
                          </div>
                          <div class="form-inline">
                             <div class="form-group height_auto mn">
                              <label><input type="radio" name="renewed_member_eligible_coverage"  value="SelectDay" <?= !empty($renewed_member_eligible_coverage) && $renewed_member_eligible_coverage!="Immediately" ? 'checked' : '' ?>> The next effective date following</label>
                             </div>
                             <div class="form-group height_auto mn">
                              <select class="form-control " name="renewed_member_eligible_coverage_day" data-live-search="true">
                                  <?php for($i=1;$i<=120;$i++) { ?>
                                    <option value="<?= $i ?>" <?= !empty($renewed_member_eligible_coverage) && $renewed_member_eligible_coverage!="Immediately" && $renewed_member_eligible_coverage == $i ? 'selected' : '' ?>><?= $i ?></option>
                                  <?php } ?>
                              </select>
                             </div>
                             <div class="form-group height_auto mn">
                              <label>days from the relationship date.</label>
                             </div>
                             <p class="error" id="error_renewed_member_eligible_coverage"></p>
                          </div>
                      </div>
                      <input type="hidden" name="allpaydate"  value ="<?= $respaydate ?>" >
                      <input type="hidden" name="datepayperiod" value="<?= $pay_period ?>" >
                      <input type="hidden" name="group_paydate" id="group_paydate" value="<?= $respaydate ?>">
                      <div class="m-b-30">
                          <p class="fw500">Pay Period</p>
                          <p>Display enrollee contribution amount by selected Pay Period or by Default (Monthly)?</p>
                          <div class="m-b-10">
                            <label class="mn"><input type="radio" name="pay_period_select"  id="pay_period_monthly" value="Monthly" <?= !empty($pay_period) && $pay_period == "Monthly" ? 'checked' : '' ?>>Default (Monthly)</label>
                          </div>
                              <div class="row">
                                  <div class="col-sm-3">
                                      <div class="form-group height_auto"  id="monthly" style="display:none">
                                          <div class="input-group">
                                              <span class="input-group-addon"> <i class="fa fa-calendar" aria-hidden="true"></i></span>
                                              <div class="pr">
                                                <input id="monthlydate" type="text" class="form-control" data-date = "" name="monthly">
                                                <label>monthly Date(MM/DD/YYYY)</label>
                                              </div>  
                                          </div>
                                          <span class="error" id="error_pay_period_monthly_paydate"></span>
                                      </div>
                                  </div>
                              </div>
                          <div class="m-b-10">
                            <label class="mn"><input type="radio" name="pay_period_select" id="pay_periods" value="pay_period" <?= !empty($pay_period) && $pay_period != "Monthly" ? 'checked' : '' ?>>Pay Period</label>
                          </div>
                          <div class="p-l-20" id="pay_period_div" style="<?= !empty($pay_period) && $pay_period != "Monthly" ? '' : 'display: none' ?>">
                               <div class="m-b-10">
                                <label class="mn"><input type="radio" name="pay_period" id="payperiodSemimonthly" value="Semi-Monthly" <?= !empty($pay_period) && $pay_period == "Semi-Monthly" ? 'checked' : '' ?>>Semi-Monthly</label>
                               </div>
                               <div class="row">
                                <div class="col-sm-3">
                                <div class="form-group height_auto"  id="paySemimonthly" style="display:none">
                                    <div class="input-group">
                                          <span class="input-group-addon"> <i class="fa fa-calendar" aria-hidden="true"></i></span>
                                          <div class="pr">
                                              <input id="semimonthlydate" type="text" class="form-control" name="semiMonthlyDate">
                                              <label class="label-wrap">Semimonthly Date(MM/DD/YYYY)</label>
                                          </div>
                                        </div>
                                        <span class="error" id="error_pay_period_semimonthly_paydate"></span>
                                    </div>
                                </div>
                              </div>
                               <div class="m-b-10">
                                <label class="mn"><input type="radio" name="pay_period" id="payperiodWeekly" value="Weekly" <?= !empty($pay_period) && $pay_period == "Weekly" ? 'checked' : '' ?>>Weekly</label>
                               </div>
                               <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group height_auto" id="payweek" style="display:none">
                                          <div class="input-group">
                                              <span class="input-group-addon"> <i class="fa fa-calendar" aria-hidden="true"></i></span>
                                              <div class="pr">
                                                  <input id="weeklydate" type="text" class="form-control" name="weekly" placeholder="">
                                                  <label class="label-wrap">Weekly Date(MM/DD/YYYY)</label>
                                              </div>
                                          </div>
                                              <span class="error" id="error_pay_period_weekly_paydate"></span>
                                    </div>
                                </div>
                              </div>
                               <div class="m-b-10">
                                <label class="mn"><input type="radio" name="pay_period" id="payperiodBiweekly" value="Bi-Weekly" <?= !empty($pay_period) && $pay_period == "Bi-Weekly" ? 'checked' : '' ?>>Bi-Weekly</label>
                               </div>
                               <div class="row">
                                <div class="col-sm-3">
                                <div class="form-group height_auto" id="payBiweek" style="display:none">
                                          <div class="input-group">
                                              <span class="input-group-addon"> <i class="fa fa-calendar" aria-hidden="true"></i></span>
                                              <div class="pr">
                                                  <input id="biweeklydate" type="text" class="form-control" name="BiweeklyDate" placeholder="">
                                                  <label class="label-wrap">Biweekly Date(MM/DD/YYYY)</label>
                                              </div>
                                          </div>
                                              <span class="error" id="error_pay_period_biweekly_paydate"></span>
                                    </div>
                                </div>
                              </div>
                          </div>
                          <p class="error" id="error_pay_period"></p>
                      </div>
                    </div>
                    <div class="text-center">
                        <a href="javascript:void(0);" class="btn btn-action" id="save_class">Save</a>
                        <a href="javascript:void(0);" class="btn red-link" id="cancel_class">Cancel</a>
                    </div>
                  </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

$(document).ready(function() {
$("#monthlydate").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true,
        clearBtn: true,
        startDate: new Date(),
        // daysOfWeekDisabled: [0,6],
        stepMonths: 0,
});

$("#semimonthlydate").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true,
        clearBtn: true,
        multidate: true,
        startDate: new Date(),
        // daysOfWeekDisabled: [0,6],
});

$('#weeklydate').datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true,
        clearBtn: true,
        startDate: new Date(),
        // daysOfWeekDisabled: [0,6],
});

$('#biweeklydate').datepicker({
    changeDay: true,
    changeMonth: true,
    changeYear: true,
    clearBtn: true,
    startDate: new Date(),
    // daysOfWeekDisabled: [0,6],
});
var groupclasses_id = $('#group_classes_id').val();
if(groupclasses_id != 0 && paydates != 0) {
var pay_period_monthly = $('#pay_period_monthly').is(':checked');
var payperiodWeekly = $('#payperiodWeekly').is(':checked');
var payperiodSemimonthly = $('#payperiodSemimonthly').is(':checked');
var payperiodbiWeekly = $('#payperiodBiweekly').is(':checked');
var paydates = $('#group_paydate').val();
var paydatesArray = paydates.split(',');
paydatesArray.reverse();
      if(pay_period_monthly){
          $("#monthly").show();
          paydatesArray.unshift('update');
                  var $e1 = $('#monthlydate');
                  $e1.datepicker.apply($e1, paydatesArray);
                  $('#group_paydate').val('0');
      }

      if(payperiodWeekly){
          $("#payweek").show();
          paydatesArray.unshift('update');
                  var $e1 = $('#weeklydate');
                  $e1.datepicker.apply($e1, paydatesArray);
                  $('#group_paydate').val('0');
      }

      if(payperiodSemimonthly){
          $("#paySemimonthly").show();
          paydatesArray.unshift('update');
                  var $e1 = $('#semimonthlydate');
                  $e1.datepicker.apply($e1, paydatesArray);
                  $('#group_paydate').val('0');
      }

      if(payperiodbiWeekly){
          $("#payBiweek").show();
                paydatesArray.unshift('update');
                  var $e1 = $('#biweeklydate');
                  $e1.datepicker.apply($e1, paydatesArray);
                  $('#group_paydate').val('0');
      }
}
});
var weekdays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

function isDateInArray(needle, haystack) {
        for (var i = 0; i < haystack.length; i++) {
          if (needle.getTime() === haystack[i].getTime()) {
            return true;
          }
        }
        return false;
    }
    $('#monthlydate').on('changeDate',function(){
      var check_class = $('#group_paydate').val();
      if(check_class == 0){
        var from = $('#monthlydate').attr('value');
              var myDate = getDatesFromDateRange(from,"monthlydate");
            myDate.reverse();
            var uniqueDates = [];
                    for (var i = 0; i < myDate.length; i++) {
                          if (!isDateInArray(myDate[i], uniqueDates)) {
                            uniqueDates.push(myDate[i]);
                          }
                    }
                  uniqueDates.unshift('update');
                  var $e1 = $('#monthlydate');
                  $e1.datepicker.apply($e1, uniqueDates);
      }
    });

    $('#semimonthlydate').on("changeDate",function(){
        var check_class = $('#group_paydate').val();
          if(check_class == 0){
                var getfrom = $('#semimonthlydate').attr('value');
                if(getfrom.length > 30){
                  var getfrom = $('#semimonthlydate').attr('value');
                  $('#semimonthlydate').datepicker("update", "");
                }else{
                  var semimonthlydate = [];
                    if(getfrom.indexOf(',') != -1){
                          var from = getfrom.split(",");
                        for (let i = 0; i < from.length; ++i) {
                                var myData = getDatesFromDateRange(from[i],'semimonthlydate');
                              semimonthlydate.push(myData);
                        }
                        $.merge(semimonthlydate[0],semimonthlydate[1]);
                        var semimonthlydatearray = semimonthlydate[0];
                          semimonthlydatearray.sort((a,b) => Date.parse(b) - Date.parse(a));
                          semimonthlydatearray.unshift('update');
                                var $e2 = $('#semimonthlydate');
                              $e2.datepicker.apply($e2, semimonthlydatearray);
                    }
                }
          }
    });

    $('#weeklydate').on('changeDate', function() {
          var from =$('#weeklydate').attr('value');
          var check_class = $('#group_paydate').val();
        if(check_class == 0){
              var myDate = getDatesFromDateRange(from,"weeklydate");
              myDate.reverse();
              myDate.unshift('update');
                  var $el = $('#weeklydate');
                  $el.datepicker.apply($el, myDate);
        }
    });

    $('#biweeklydate').on('changeDate', function() {
          var from =$('#biweeklydate').attr('value');
          var check_class = $('#group_paydate').val();
        if(check_class == 0){
              var myDate = getDatesFromDateRange(from,"biweeklydate");
              myDate.reverse();
              myDate.unshift('update');
                  var $e3 = $('#biweeklydate');
                  $e3.datepicker.apply($e3, myDate);
        }
    });

    function lastmonday(y,m,monthIndex) {
        var dates = new Date(y+'/'+m+'/1') ,currentmonth = m,firstmonday = false;
        if(monthIndex == "thanksgiving[11]"){
           daynum = 4;
        }else{
          daynum = 1;
        }
          while (currentmonth === m){
            firstmonday = dates.getDay() === daynum || firstmonday;
            dates.setDate(dates.getDate()+(firstmonday ? 7 : 1));
            currentmonth = dates.getMonth()+1;
          }
          if(monthIndex == "Janholiday[1]"){
            var holidatdate =  new Date(dates.setDate(dates.getDate() + 14));
          }
          if( monthIndex == "Febholiday[2]"){
               var holidatdate =  new Date(dates.setDate(dates.getDate() + 14));
          }
          if(monthIndex == "Mayholiday[5]"){
            var holidatdate =  new Date(dates.setDate(dates.getDate() -7));
          }
          if(monthIndex == "Laborday[9]"){
            var holidatdate = new Date(dates.setDate(dates.getDate()));
          }
          if(monthIndex == "Columbusday[10]"){
            var holidatdate = new Date(dates.setDate(dates.getDate()+ 7));
          }
          if(monthIndex == "thanksgiving[11]"){
            var holidatdate = new Date(dates.setDate(dates.getDate()+ 21));
          }
          return holidatdate;
      }

  function getDatesFromDateRange(from,type) {
      var start_date = new Date(from);
      var firstselectdate = new Date(from);
      var year = start_date.getFullYear();
      var month = start_date.getMonth();
      var day = start_date.getDate();
      var end_date = new Date(year +1 , month , day);
      var getyears = year+1;
      var getfeb = year-1;
      var holidaydate1 = lastmonday(getfeb,12,"Janholiday[1]");        // Martin Luther King, Jr. Day
      var holidaydate2 = lastmonday(year,1,"Febholiday[2]");              // presidents day
      var holidaydate3 = lastmonday(year,5,"Mayholiday[5]");              // memorial day
      var holidaydate4 = lastmonday(year,8,"Laborday[9]");                // labor day
      var holidaydate5 = lastmonday(year,9,"Columbusday[10]");            // Columbus Day
      var holidaydate6 = lastmonday(year,10,"thanksgiving[11]");          // thanks giving

      var holidaydate11 = lastmonday(year,12,"Janholiday[1]");             // Martin Luther King, Jr. Day
      var holidaydate22 = lastmonday(getyears,1,"Febholiday[2]");          // presidents day
      var holidaydate33 = lastmonday(getyears,5,"Mayholiday[5]");          // memorial day
      var holidaydate44 = lastmonday(getyears,8,"Laborday[9]");            // labor day
      var holidaydate55 = lastmonday(getyears,9,"Columbusday[10]");        // Columbus Day
      var holidaydate66 = lastmonday(getyears,10,"thanksgiving[11]");      // thanks giving

      var holiday = [convert(holidaydate1),convert(holidaydate2),convert(holidaydate3),convert(holidaydate4),convert(holidaydate5),convert(holidaydate6),convert(holidaydate11),convert(holidaydate22),convert(holidaydate33),convert(holidaydate44),convert(holidaydate55),convert(holidaydate66),year+'/01/01',year+'/06/19',year+'/07/04',year+'/11/11',year+'/12/25',getyears+'/01/01',getyears+'/06/19',getyears+'/07/04',getyears+'/11/11',getyears+'/12/25','Saturday','Sunday'];
      var datearray = [];
          while (start_date < end_date) {
              var sdate = new Date(start_date);
              var newdate = convert(sdate);
              var dayOfWeek = weekdays[sdate.getDay()];
                // if(holiday.includes(newdate) || holiday.includes(dayOfWeek)){
                //   var check_holiday = holidayChecked(sdate);
                //   var sdate = check_holiday;
                // }
                datearray.push(new Date(sdate));
                if(type == "weeklydate"){
                  start_date.setDate(start_date.getDate() + 7);
                }
                if(type == "biweeklydate"){
                  start_date.setDate(start_date.getDate() + 14);
                }
                if( type == "monthlydate" || type == "semimonthlydate"){
                  var Year = start_date.getFullYear();
                  var Month = start_date.getMonth();
                  if(Month <= 11){
                    var Month = Month+1;
                  }
                    var firstgetyear = firstselectdate.getFullYear();
                    var firstgetmonth = firstselectdate.getMonth()+1;
                    var firstgetday = firstselectdate.getDate();
                    var firstdate_endofmonth = new Date((new Date(firstgetyear,firstgetmonth,0)));
                    var endof_monthdate = new Date((new Date(Year,Month,0)));
                    if(convert(firstselectdate) == convert(firstdate_endofmonth)){
                        if(convert(start_date) == convert(endof_monthdate)){
                          start_date = new Date((new Date(Year,Month+1,0)));
                        }else{
                          start_date.setMonth(start_date.getMonth() + 1);
                        }
                    }else{
                      var getdateMonth = start_date.getMonth();
                      var getdate = start_date.getDate();   
                          if(getdate > 27 && (getdateMonth == 0 || getdateMonth == 1)){
                                if(getdateMonth == 1){
                                  start_date = new Date((new Date(Year,Month+1,0)));
                                  start_date.setDate(start_date);
                                  var date1 = new Date(Year,Month, day);
                                  start_date = date1;
                                  start_date.setMonth(start_date.getMonth());
                                }else{
                                  start_date = new Date((new Date(Year,Month+1,0)));
                                }
                          }else{
                            start_date.setMonth(start_date.getMonth() + 1);
                          }
                    }
                }
         }
      return datearray;
}

function holidayChecked(holidaydate){
    var getyear = holidaydate.getFullYear();
    var getfeb = getyear-1;
    var getyears = getyear+1;
      var holidaydate1 = lastmonday(getfeb,12,"Janholiday[1]");        // Martin Luther King, Jr. Day
      var holidaydate2 = lastmonday(getyear,1,"Febholiday[2]");              // presidents day
      var holidaydate3 = lastmonday(getyear,5,"Mayholiday[5]");              // memorial day
      var holidaydate4 = lastmonday(getyear,8,"Laborday[9]");                // labor day
      var holidaydate5 = lastmonday(getyear,9,"Columbusday[10]");            // Columbus Day
      var holidaydate6 = lastmonday(getyear,10,"thanksgiving[11]");          // thanks giving

      var holidaydate11 = lastmonday(getyear,12,"Janholiday[1]");             // Martin Luther King, Jr. Day
      var holidaydate22 = lastmonday(getyears,1,"Febholiday[2]");          // presidents day
      var holidaydate33 = lastmonday(getyears,5,"Mayholiday[5]");          // memorial day
      var holidaydate44 = lastmonday(getyears,8,"Laborday[9]");            // labor day
      var holidaydate55 = lastmonday(getyears,9,"Columbusday[10]");        // Columbus Day
      var holidaydate66 = lastmonday(getyears,10,"thanksgiving[11]");      // thanks giving

    var getholiday_year = getyear+1;
    var holiday = [convert(holidaydate1),convert(holidaydate2),convert(holidaydate3),convert(holidaydate4),convert(holidaydate5),convert(holidaydate6),convert(holidaydate11),convert(holidaydate22),convert(holidaydate33),convert(holidaydate44),convert(holidaydate55),convert(holidaydate66),getyear+'/01/01',getyear+'/06/19',getyear+'/07/04',getyear+'/11/11',getyear+'/12/25',getyears+'/01/01',getyears+'/06/19',getyears+'/07/04',getyears+'/11/11',getyears+'/12/25','Saturday','Sunday'];
  var holiday_date = new Date(holidaydate);
      holiday_date.setDate(holiday_date.getDate() - 1);
    var dayOfWeek = weekdays[holiday_date.getDay()];
        if(holiday.includes(dayOfWeek)){
              return holidayChecked(holiday_date);
        }
        if(holiday.includes(convert(holiday_date))){
              return holidayChecked(holiday_date);
        }else{
              return holiday_date;
        }
}

function convert(str) {
    var date = new Date(str);
    var mnth = ("0" + (date.getMonth() + 1)).slice(-2);
    var day = ("0" + date.getDate()).slice(-2);
  return [date.getFullYear(),mnth,day].join("/");
}

  $(".dates").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
  $(document).on("click","#add_class",function(){
    $("#add_class_text").show();
    $("#remove_class_div").show();
    $("#add_class_div").show();
    $("#add_class").hide();
  });

  $(document).on("click","#remove_class_div",function(){
    $("#add_class_text").hide();
    $("#remove_class_div").hide();
    $("#add_class_div").hide();
    $("#add_class").show();
    $val = $("#class_name").val();

    if($val == ''){
      $val = "+ Class";
    }
    $("#add_class_text").html($val);
  });

  $(document).on("keyup","#class_name",function(){
    $val= $(this).val();
    $("#add_class_text").html($val);
  });

  
  $(document).on("change","input[name=pay_period_select]",function(){
      $val=$(this).val();
      $("#pay_period_div").hide();
      if($val=="pay_period"){
        $("#pay_period_div").show();
      }
  });

  $(document).on("click",".datePickerIcon",function(){
      $id=$(this).attr('data-applyon');
      var today = new Date();
      $("#"+$id).datepicker({endDate : today});
      $("#"+$id).datepicker('show');
      $("#"+$id).trigger("blur");
  });

  $(document).on("click","#save_class",function(){
    $(".error").html('');
    $("#ajax_loader").show();
    $.ajax({
      url:'ajax_group_add_class.php',
      dataType:'JSON',
      data:$("#add_class_form").serialize(),
      type:'POST',
      success:function(res){
        $("#ajax_loader").hide();
        if(res.status=="success"){
          window.location.href="group_classes.php";     
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

  $(document).on("click",".remove_class",function(){
    $id=$(this).attr('data-id');
    swal({
        text: "Delete Class: Are you sure?",
        showCancelButton: true,
        confirmButtonText: "Confirm",
    }).then(function() {
       $("#ajax_loader").show();
        $.ajax({
          url:'ajax_group_remove_class.php',
          dataType:'JSON',
          data:{id:$id},
          type:'POST',
          success:function(res){
            $("#ajax_loader").hide();
            if(res.status=="success"){
              window.location.reload();     
            }else{
              setNotifyError(res.msg);
            }
          }
        });
    }, function (dismiss) {
    });
    
  });

  $(document).on("click","#cancel_class",function(){
     window.location.href="group_classes.php";
  });

    $(document).on("click","#payperiodSemimonthly",function(){
        $("#paySemimonthly").show();
                $("#payweek").hide();
                    $("#payBiweek").hide();
                            $("#payMonthly").hide();
                                $("#monthly").hide();
		});

    $(document).on("click","#payperiodWeekly",function(){
        $("#paySemimonthly").hide();
                  $("#payweek").show();
                      $("#payBiweek").hide();
                              $("#payMonthly").hide();
                                  $("#monthly").hide();
		});

    $(document).on("click","#payperiodBiweekly",function(){
        $("#paySemimonthly").hide();
                $("#payweek").hide();
                    $("#payBiweek").show();
                            $("#payMonthly").hide();
                                $("#monthly").hide();
    });

    $(document).on("click","#pay_periods",function(){
        $("#monthly").hide();
    });
    
    $(document).on("click","#pay_period_monthly",function(){
        $("#monthly").show();
    });

</script>