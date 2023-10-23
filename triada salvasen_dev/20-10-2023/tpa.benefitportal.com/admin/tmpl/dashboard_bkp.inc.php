<div class="dashboard_block">
<div class="panel-sales" id="summary_data">
  <div class="panel-heading clearfix">
    <p class="pull-left mn">SALES SUMMARY <span class="br-l text-uppercase sales_summary_from_to_date">OCTOBER 5, 2019</span></p>
    <div class="dropdown pull-right"> <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><span class="searched_type">Today</span> <i class="fa fa-caret-down"></i></a>
      <ul role="menu" class="dropdown-menu pull-right">
        <li><a class="custom_date" href="javascript:void(0)" data-Date="Today" data-val="Today">Today</a></li>
        <li><a class="custom_date" href="javascript:void(0)" data-Date="Yesterday" data-val="Yesterday">Yesterday</a></li>
        <li><a class="custom_date" href="javascript:void(0)" data-Date="Last 7 Days" data-val="Last 7 Days">Last 7 Days</a></li>
        <li><a class="custom_date" href="javascript:void(0)" data-Date="This Month" data-val="This Month">This Month</a></li>
        <li><a class="custom_date" href="javascript:void(0)" data-Date="Last month" data-val="Last Month">Last Month</a></li>
        <li><a class="custom_date" href="javascript:void(0)" data-Date="This Year" data-val="This Year">This Year</a></li>
        <li><a class="custom_date" href="javascript:void(0)" data-Date="Last Year" data-val="Last Year">Last Year</a></li>
        <li><a class="custom_date" href="javascript:void(0)" data-Date="Custom Date" data-val="Custom Date">Custom</a></li>
      </ul>
    </div>
  </div>
  <div class="panel-body">
    <div id="custom_date_div" class="custom_date_filter p-10 text-right" style="display:none;">
      <fieldset>
        <label>From Date</label>
        <input type="text" name="fromdate" id="fromdate" value="" class="datetimepicker-range" size="10" placeholder="">
      </fieldset>
      <fieldset>
        <label>To Date</label>
        <input type="text" name="todate" id="todate" value="" class="datetimepicker-range" size="10" placeholder="">
      </fieldset>
      <input type="hidden" name="fromdate" id="fromdate" value="" class="datetimepicker-range form-control" size="10" placeholder="From Date">
      <input type="hidden" name="todate" id="todate" value="" class="datetimepicker-range form-control" size="10" placeholder="To Date">
      <div class="form-group m-l-10  m-b-0"> <a href="javascript:void(0)" class="btn btn-info" id="submit_btn">Submit</a> </div>
    </div>
    <div class="table-responsive">
      <table width="100%" class="tbl_sales_summery text-center">
        <thead>
          <tr>
            <th>TOTAL SALES</th>
            <th>NEW BUSINESS</th>
            <th>RENEWALS</th>
            <th>AVERAGE SALE</th>
            <th>TOTAL MEMBERS</th>
            <th>TOTAL POLICIES</th>
            <th>AVG PLAN / PER MEMBER</th>
            <th>NET FALLOFF</th>
          </tr>
        </thead>
        <tbody>
          <tr><td colspan="8"><i class="fa fa-spin fa-spinner fa-3x "></i></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
<div class="white-box m-b-20">
  <div class="row">
    <div class="col-sm-4">
      <div class="inner_sect">
        <div class="box-title">TOTAL SALES</div>
      </div>
    </div>
    <div id="legend" class="col-md-8"></div>
  </div>
  <div class="clearfix"></div>
  <div id="morris-area-chart" style="height: 340px;"></div>
</div>
<div class="row">
  <div class="col-sm-6 col-md-4">
    <div class="panel panel-colored preform-red" id="top_performing_agent">
      <div class="panel-heading clearfix">
        <h4 class="panel-title pull-left">TOP PERFORMING AGENTS</h4>
      </div>
      <div class="panel-body">
        <div id="custom_date_div_agent" class="custom_date_filter p-10 text-right" style="display:none;">
          <fieldset>
            <label>From Date </label>
            <input type="text" name="fromdate" id="fromdate_agent" value="" class="datetimepicker-range " size="10" placeholder="">
          </fieldset>
          <fieldset>
            <label>To Date</label>
            <input type="text" name="todate" id="todate_agent" value="" class="datetimepicker-range" size="10" placeholder="">
          </fieldset>
          <input type="hidden" name="fromdate" id="fromdate_agent" value="" class="datetimepicker-range form-control" size="10" placeholder="From Date">
          <input type="hidden" name="todate" id="todate_agent" value="" class="datetimepicker-range form-control" size="10" placeholder="To Date">
          <div class="form-group m-l-10  m-b-0"> <a href="javascript:void(0)" class="btn btn-info" id="submit_btn_agent">Submit</a> </div>
        </div>
        <div class="tbl-header">
          <table width="100%" class="tbl_preform">
            <thead>
              <tr class="data-head">
                <th>Agent</th>
                <th class=""><a href="javascript:void(0);" data-column="total_policies" data-direction="DESC">Policies</a></th>
                <th class=""><a href="javascript:void(0);" class="fallOff_agents" data-filter="ASC"  >Falloff</a></th>
                <th><a href="javascript:void(0);"  data-column="total_premiums" data-direction="DESC">Sales</a></th>
              </tr>
            </thead>
          </table>
        </div>
        <div class="tbl-body">
          <table width="100%" class="tbl_preform">
            <tbody class="tbody">
              <tr>
                <td style="text-align:center" colspan="4">Loading...</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="panel-footer">
        <div id="top_performing_agent_fall_off">
            <div class="dropdown pull-left hidden-lg visible-md visible-sm visible-xs visible-xs"> <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><span class="falloff_filter">Falloff</span> <i class="fa fa-angle-right"></i></a>
            <ul role="menu" class="dropdown-menu pull-left">
              <li><a class="fall_off_filter" href="javascript:void(0)" data-filter="Highest(%)" data-val="Highest %">Highest %</a></li>
              <li><a class="fall_off_filter" href="javascript:void(0)" data-filter="Lowest(%)" data-val="Lowest %">Lowest %</a></li>
            </ul>
          </div>
        </div>
        <div class="pull-right"> <a href="<?=$ADMIN_HOST?>/summary_report_v2.php" class="btn btn-default">View all <i class="fa fa-angle-right m-l-5"></i></a> </div>
        <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
        <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
        <input type ="hidden" name="agent_date" id="agent_date" value="Today" />
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-4">
    <div class="panel panel-colored preform-blue" id="top_performing_organization">
      <div class="panel-heading clearfix">
        <h4 class="panel-title pull-left">TOP PERFORMING ORGANIZATIONS</h4>
      </div>
      <div class="panel-body">
        <div id="custom_date_div_org" class="custom_date_filter p-10 text-right" style="display:none;" >
          <fieldset>
            <label>From Date </label>
            <input type="text" name="fromdate" id="fromdate_org" value="" class="datetimepicker-range " size="10" placeholder="">
          </fieldset>
          <fieldset>
            <label>To Date</label>
            <input type="text" name="todate" id="todate_org" value="" class="datetimepicker-range" size="10" placeholder="">
          </fieldset>
          <input type="hidden" name="fromdate" id="fromdate_org" value="" class="datetimepicker-range form-control" size="10" placeholder="From Date">
          <input type="hidden" name="todate" id="todate_org" value="" class="datetimepicker-range form-control" size="10" placeholder="To Date">
          <div class="form-group m-l-10  m-b-0"> <a href="javascript:void(0)" class="btn btn-info" id="submit_btn_org">Submit</a> </div>
        </div>
        <div class="tbl-header">
          <table width="100%" class="tbl_preform">
            <thead>
              <tr class="data-head">
                <th>Agent</th>
                <th class=""><a href="javascript:void(0);"  data-column="policies" data-direction="DESC">Policies</a></th>
                <th class=""><a href="javascript:void(0);" data-filter="ASC" class="fallOff_org" data-val="fallOff_org">Falloff</a></th>
                <th><a href="javascript:void(0);"  data-column="total_premiums" data-direction="DESC">Sales</a></th>
              </tr>
            </thead>
          </table>
        </div>
        <div class="tbl-body">
          <table width="100%" class="tbl_preform">
            <tbody class="tbody">
              <tr>
                <td style="text-align:center" colspan="4">Loading...</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="panel-footer">
        <div id="top_performing_organization_fall_off">
               <div class="dropdown pull-left hidden-lg visible-md visible-sm visible-xs"> <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><span class="falloff_filter">Falloff</span> <i class="fa fa-angle-right"></i></a>
              <ul role="menu" class="dropdown-menu pull-left">
                <li><a class="fall_off_filter" href="javascript:void(0)" data-filter="Highest(%)" data-val="Highest%">Highest %</a></li>
                <li><a class="fall_off_filter" href="javascript:void(0)" data-filter="Lowest(%)" data-val="Lowest%">Lowest %</a></li>
              </ul>
            </div>
        </div>
        <div class="pull-right"> <a href="<?=$ADMIN_HOST?>/summary_report_v2.php" class="btn btn-default">View all <i class="fa fa-angle-right m-l-5"></i> </a> </div>
        <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
        <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
        <input type ="hidden" name="organization_date" id="organization_date" value="Today" />
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-4">
    <div class="panel panel-colored preform-gray" id="top_performing_products">
      <div class="panel-heading clearfix">
        <h4 class="panel-title pull-left">TOP PERFORMING PRODUCTS</h4>
      </div>
      <div class="panel-body">
        <div id="custom_date_div_product" class="custom_date_filter p-10 text-right" style="display:none;" >
          <fieldset>
            <label>From Date </label>
            <input type="text" name="fromdate" id="fromdate_product" value="" class="datetimepicker-range " size="10" placeholder="">
          </fieldset>
          <fieldset>
            <label>To Date</label>
            <input type="text" name="todate" id="todate_product" value="" class="datetimepicker-range" size="10" placeholder="">
          </fieldset>
          <input type="hidden" name="fromdate" id="fromdate_product" value="" class="datetimepicker-range form-control" size="10" placeholder="From Date">
          <input type="hidden" name="todate" id="todate_product" value="" class="datetimepicker-range form-control" size="10" placeholder="To Date">
          <div class="form-group m-l-10  m-b-0"> <a href="javascript:void(0)" class="btn btn-info" id="submit_btn_product">Submit</a> </div>
        </div>
        <div class="tbl-header">
          <table width="100%" class="tbl_preform">
            <thead>
              <tr class="data-head">
                <th>Product</th>
                <th class=""><a href="javascript:void(0);"  data-column="policies" data-direction="DESC">Policies</a></th>
                <th class=""><a href="javascript:void(0);" class="fallOff_prd" data-filter="ASC">Falloff</a></th>
                <th><a href="javascript:void(0);"  data-column="total_premiums" data-direction="DESC">Sales</a></th>
              </tr>
            </thead>
          </table>
        </div>
        <div class="tbl-body">
          <table width="100%" class="tbl_preform">
            <tbody class="tbody">
              <tr>
                <td style="text-align:center" colspan="4">Loading...</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="panel-footer">
          <div id="top_performing_produts_fall_off">
                 <div class="dropdown pull-left hidden-lg visible-md visible-sm visible-xs"> <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><span class="falloff_filter">Falloff</span> <i class="fa fa-angle-right"></i></a>
                <ul role="menu" class="dropdown-menu pull-left">
                  <li><a class="fall_off_filter" href="javascript:void(0)" data-filter="Highest(%)" data-val="Highest%">Highest %</a></li>
                  <li><a class="fall_off_filter" href="javascript:void(0)" data-filter="Lowest(%)" data-val="Lowest%">Lowest %</a></li>
                </ul>
              </div>
          </div>
          <div class="pull-right"> <a href="<?=$ADMIN_HOST?>/summary_report_v2.php" class="btn btn-default">View all <i class="fa fa-angle-right m-l-5"></i></a> </div>
          <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
          <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
          <input type ="hidden" name="product_date" id="product_date" value="Today" />
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
    function callback2() {
      if ($(window).width() < 1170) {
        $('.tooltip_hide_tm').tooltip('enable');
      } else if ($(window).width() > 1170) {
        $("#left_bottomgraydash_info").html('');
      }
    }
    $(window).on('resize', callback2);
    callback2();

    $(window).on("load resize ", function () {
      var scrollWidth = $('.tbl-body').width() - $('.tbl-body table').width();
    }).resize();


    //desktop hide tooltip
    function desk_hide_tooltip() {
      if ($(window).width() >= 1170) {
        $('.tooltip_hide_tm').tooltip('disable').each(function () {
          $(this).data('bs.tooltip').tip().addClass('white-tooltip');
        });
      }
      $('.tooltip-custom').tooltip().each(function () {
        $(this).data('bs.tooltip').tip().addClass('white-tooltip');
      });
    }


    $(document).ready(function () {
      getSalesData();
      // Fall-off for Top Performing agents  starts//
      $(document).on('click', '#top_performing_agent_fall_off .fall_off_filter', function () {
        $date = $("#summary_data .searched_type").html();
        if ($(this).attr('data-filter') == 'Highest %') {
          $("#top_performing_agent_fall_off .falloff_filter").html($(this).attr('data-filter'));
        } else {
          $("#top_performing_agent_fall_off .falloff_filter").html($(this).attr('data-filter'));
        }
        if ($date == "Custom") {
          var getfromdate = $('#fromdate').val();
          var gettodate = $('#todate').val();
          if (fromdate != '' && todate != '') {
            var date = {
              getfromdate: getfromdate,
              gettodate: gettodate
            };
            load_top_performing_agent('Custom Date', '', '', date, $(this).attr('data-filter'));
          }
        } else {
          load_top_performing_agent($date, '', '', '', $(this).attr('data-filter'));
        }


      });

      // Fall-off for Top Performing agents  starts//
      $(document).on('click', '#top_performing_organization_fall_off .fall_off_filter', function () {
        $date = $("#summary_data .searched_type").html();
        if ($(this).attr('data-filter') == 'Highest %') {
          $("#top_performing_organization_fall_off .falloff_filter").html($(this).attr('data-filter'));
        } else {
          $("#top_performing_organization_fall_off .falloff_filter").html($(this).attr('data-filter'));
        }

        if ($date == "Custom") {
          var getfromdate = $('#fromdate').val();
          var gettodate = $('#todate').val();
          if (fromdate != '' && todate != '') {
            var date = {
              getfromdate: getfromdate,
              gettodate: gettodate
            };
            load_top_performing_organization('Custom Date', '', '', date, $(this).attr('data-filter'));
          }
        } else {
          load_top_performing_organization($date, '', '', '', $(this).attr('data-filter'));
        }
      });

      // Fall-off for Top Performing Products  starts//
      $(document).on('click', '#top_performing_produts_fall_off .fall_off_filter', function () {
        $date = $("#summary_data .searched_type").html();
        if ($(this).attr('data-filter') == 'Highest %') {
          $("#top_performing_produts_fall_off .falloff_filter").html($(this).attr('data-filter'));
        } else {
          $("#top_performing_produts_fall_off .falloff_filter").html($(this).attr('data-filter'));
        }
        if ($date == "Custom") {
          var getfromdate = $('#fromdate').val();
          var gettodate = $('#todate').val();
          if (fromdate != '' && todate != '') {
            var date = {
              getfromdate: getfromdate,
              gettodate: gettodate
            };
            load_top_performing_products('Custom Date', '', '', date, $(this).attr('data-filter'));
          }
        } else {
          load_top_performing_products($date, '', '', '', $(this).attr('data-filter'));
        }
      });

      $('.datetimepicker-range').datepicker();
      $('.tbl-body').slimScroll({
        height: '200px',
        width: '100%',
        alwaysVisible: true
      });

      $(document).off('click', '#top_performing_products tr.data-head a');
      $(document).on('click', '#top_performing_products tr.data-head a', function (e) {
        e.preventDefault();
        if ($(this).attr('data-direction') == 'ASC') {
          $(this).attr('data-direction', 'DESC');
        } else {
          $(this).attr('data-direction', 'ASC');
        }

        $filter = $(this).attr('data-filter');
        $filterType = $("#summary_data .searched_type").html();
        $("#product_date").val($filterType);

        if ($filterType == "Custom Date") {
          var getfromdate = $('#fromdate').val();
          var gettodate = $('#todate').val();
          if (fromdate != '' && todate != '') {
            var date = {
              getfromdate: getfromdate,
              gettodate: gettodate
            };
          } else {
            var date = '';
          }
        } else {
          var date = '';
        }

        if ($filter == 'ASC') {
          $(this).attr('data-filter', 'DESC');
          $filter = 'Highest(%)';
          load_top_performing_products($("#product_date").val(), '', '', date, $filter);
        } else if ($filter == 'DESC') {
          $(this).attr('data-filter', 'ASC');
          $filter = 'Lowest(%)';
          load_top_performing_products($("#product_date").val(), '', '', date, $filter);
        } else {
          load_top_performing_products($("#product_date").val(), $(this).attr("data-column"), $(this).attr("data-direction"), date);
        }
      });

      $(document).off('click', '#top_performing_organization tr.data-head a');
      $(document).on('click', '#top_performing_organization tr.data-head a', function (e) {
        e.preventDefault();
        if ($(this).attr('data-direction') == 'ASC') {
          $(this).attr('data-direction', 'DESC');
        } else {
          $(this).attr('data-direction', 'ASC');
        }
        $filter = $(this).attr('data-filter');
        $filterType = $("#summary_data .searched_type").html();

        if ($filterType == "Custom Date") {
          var getfromdate = $('#fromdate').val();
          var gettodate = $('#todate').val();
          if (fromdate != '' && todate != '') {
            var date = {
              getfromdate: getfromdate,
              gettodate: gettodate
            };
          } else {
            var date = '';
          }
        } else {
          var date = '';
        }

        $("#organization_date").val($filterType);
        if ($filter == 'ASC') {
          $(this).attr('data-filter', 'DESC');
          $filter = 'Highest(%)';
          load_top_performing_organization($("#organization_date").val(), '', '', date, $filter);
        } else if ($filter == 'DESC') {
          $(this).attr('data-filter', 'ASC');
          $filter = 'Lowest(%)';
          load_top_performing_organization($("#organization_date").val(), '', '', date, $filter);
        } else {
          load_top_performing_organization($("#organization_date").val(), $(this).attr("data-column"), $(this).attr("data-direction"), date);
        }
      });

      $(document).off('click', '#top_performing_agent tr.data-head a');
      $(document).on('click', '#top_performing_agent tr.data-head a', function (e) {
        e.preventDefault();
        if ($(this).attr('data-direction') == 'ASC') {
          $(this).attr('data-direction', 'DESC');
        } else {
          $(this).attr('data-direction', 'ASC');
        }

        $filter = $(this).attr('data-filter');
        $filterType = $("#summary_data .searched_type").html();

        if ($filterType == "Custom Date") {
          var getfromdate = $('#fromdate').val();
          var gettodate = $('#todate').val();
          if (fromdate != '' && todate != '') {
            var date = {
              getfromdate: getfromdate,
              gettodate: gettodate
            };
          } else {
            var date = '';
          }
        } else {
          var date = '';
        }

        $("#agent_date").val($filterType);
        if ($filter == 'ASC') {
          $(this).attr('data-filter', 'DESC');
          $filter = 'Highest(%)';
          load_top_performing_agent($("#agent_date").val(), '', '', date, $filter);
        } else if ($filter == 'DESC') {
          $(this).attr('data-filter', 'ASC');
          $filter = 'Lowest(%)';
          load_top_performing_agent($("#agent_date").val(), '', '', date, $filter);
        } else {
          load_top_performing_agent($("#agent_date").val(), $(this).attr("data-column"), $(this).attr("data-direction"), date);
        }

      });

      load_top_performing_agent("Today");
      load_top_performing_organization("Today");
      load_top_performing_products("Today");
    });
    load_dashbord_sales_summary_data("Today");
    $(document).on('click', '#summary_data .custom_date', function () {
      if ($(this).attr('data-date') == 'Custom Date') {
        $('#custom_date_div').fadeIn('slow');
        $("#summary_data .searched_type").html($(this).attr('data-date'));
      } else {
        $('#custom_date_div').fadeOut('slow');
        $("#summary_data .searched_type").html($(this).attr('data-date'));
        load_dashbord_sales_summary_data($(this).attr('data-val'));
        load_top_performing_agent($(this).attr('data-val'));
        load_top_performing_organization($(this).attr('data-val'));
        load_top_performing_products($(this).attr('data-val'));
      }
    });

    $(document).on('click', '#submit_btn', function () {
      var getfromdate = $('#fromdate').val();
      var gettodate = $('#todate').val();
      if (fromdate != '' && todate != '') {
        var date = {
          getfromdate: getfromdate,
          gettodate: gettodate
        };
        load_dashbord_sales_summary_data("Custom Date", date);
        load_top_performing_agent("Custom Date", '', '', date);
        load_top_performing_organization("Custom Date", '', '', date);
        load_top_performing_products("Custom Date", '', '', date);
      } else {
        alert("Select Date Range");
      }

    });

    $(document).on('click', '#submit_btn_agent', function () {
      var getfromdate = $('#fromdate_agent').val();
      var gettodate = $('#todate_agent').val();
      if (fromdate != '' && todate != '') {
        var date = {
          getfromdate: getfromdate,
          gettodate: gettodate
        };
        load_top_performing_agent("Custom Date", '', '', date);
      } else {
        alert("Select Date Range");
      }

    });

    $(document).on('click', '#submit_btn_org', function () {
      var getfromdate = $('#fromdate_org').val();
      var gettodate = $('#todate_org').val();
      if (fromdate != '' && todate != '') {
        var date = {
          getfromdate: getfromdate,
          gettodate: gettodate
        };
        load_top_performing_organization("Custom Date", '', '', date);
      } else {
        alert("Select Date Range");
      }

    });
    $(document).on('click', '#submit_btn_product', function () {
      var getfromdate = $('#fromdate_product').val();
      var gettodate = $('#todate_product').val();
      if (fromdate != '' && todate != '') {
        var date = {
          getfromdate: getfromdate,
          gettodate: gettodate
        };
        load_top_performing_products("Custom Date", '', '', date);
      } else {
        alert("Select Date Range");
      }

    });

    function load_dashbord_sales_summary_data(date, data) {
      $.ajax({
        url: "load_dashbord_sales_summary_data.php",
        method: "POST",
        beforeSend: function () {          
          $('#summary_data tbody').html('<tr><td colspan="8"><i class="fa fa-spin fa-spinner fa-3x "></i></td></tr>');
        },
        data: {
          filter_date: date,
          date: data,
        },
        success: function (res) {          
          res = jQuery.parseJSON(res);
          $("#summary_data tbody").html(res['html_string']);
          $('.sales_summary_from_to_date').text(res['sales_summary_from_to_date']);
          desk_hide_tooltip();
          $(window).resize(desk_hide_tooltip);
        }
      });
    }

    function load_top_performing_products(date, data_column, data_direction, data, filter) {
      $.ajax({
        url: "load_dashboard_report_data.php",
        method: "POST",
        beforeSend: function () {
          $("#top_performing_products tbody").html('<tr><td colspan="4" style="text-align:center;"><i class="fa fa-spin fa-spinner fa-lg"></i></td></tr>');
        },
        data: {
          report_type: 'top_performing_products',
          filter_date: date,
          SortBy: data_column,
          SortDirection: data_direction,
          date: data,
          filter: filter,
        },
        success: function (res) {          
          $("#top_performing_products tbody").html(res);
          desk_hide_tooltip();
          $(window).resize(desk_hide_tooltip);
        }
      });
    }

    function load_top_performing_organization(date, data_column, data_direction, data, filter) {
      $.ajax({
        url: "load_dashboard_report_data.php",
        method: "POST",
        beforeSend: function () {          
          $("#top_performing_organization tbody").html('<tr><td colspan="4" style="text-align:center;"><i class="fa fa-spin fa-spinner fa-lg"></i></td></tr>');
        },
        data: {
          report_type: 'top_performing_organization',
          filter_date: date,
          SortBy: data_column,
          SortDirection: data_direction,
          date: data,
          filter: filter,
        },
        success: function (res) {          
          $("#top_performing_organization tbody").html(res);
          desk_hide_tooltip();
          $(window).resize(desk_hide_tooltip);
        }
      });
    }

    function load_top_performing_agent(date, data_column, data_direction, data, filter) {
      $.ajax({
        url: "load_dashboard_report_data.php",
        method: "POST",
        beforeSend: function () {          
          $("#top_performing_agent tbody").html('<tr><td colspan="4" style="text-align:center;"><i class="fa fa-spin fa-spinner fa-lg"></i></td></tr>');
        },
        data: {
          report_type: 'top_performing_agent',
          filter_date: date,
          SortBy: data_column,
          SortDirection: data_direction,
          date: data,
          filter: filter,
        },
        success: function (res) {          
          $("#top_performing_agent tbody").html(res);
          $(".agent_upline_popup").colorbox({
            iframe: true,
            width: "550px",
            height: "500px"
          });
          desk_hide_tooltip();
          $(window).resize(desk_hide_tooltip);
        }
      });
    }

    function getSalesData(date, from_date, to_date) {
      $('#morris-area-chart').html('<div class="text-center" style="line-height: 340px;"><i class="fa fa-spin fa-spinner fa-3x "></i></div>');
      var params = '';
      $.ajax({
          url: "get_sales_chart_data.php",
          method: "GET",
          data: params,
          dataType: "Json",
          success: function (chartData) {
              var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
              $('#morris-area-chart').html('');
             chart= Morris.Area({
                  element: 'morris-area-chart',
                  data: chartData.data,
                  xkey: 'period',
                  ykeys: chartData.y_key,
                  labels: chartData.labels,
                  preUnits: '$',
                  pointSize: 3,
                  fillOpacity: 0.4,
                  pointStrokeColors: chartData.line_color,
                  behaveLikeLine: true,
                  gridLineColor: '#e0e0e0',
                  lineWidth: 2,
                  hideHover: 'auto',
                  lineColors: chartData.line_color,
                  resize: true,
                  xLabelFormat: function (x) {                      
                    return months[new Date(x).getMonth()] + ", " + String(new Date(x).getFullYear()).slice(-2) ;
                  },
                  dateFormat: function (x) {
                    return months[new Date(x).getMonth()] + ", " + String(new Date(x).getFullYear()).slice(-2) ;                      
                  },
              });
              chart.options.labels.forEach(function(label, i){
                var legendItem = $('<span align="center"></span> ').html('<i class="fa fa-square"></i> '+label+' ').css('color', chart.options.lineColors[i])
                $('#legend').append(legendItem)
            });
          }
      });
    }
</script>