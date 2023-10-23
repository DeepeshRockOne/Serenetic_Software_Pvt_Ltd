<div class="panel panel-default panel-block panel-title-block">
  <form id="frm_search" action="agent_listing.php" method="GET" class="theme-form">
    <div class="panel-left">
      <div class="panel-left-nav">
        <ul>
          <li class="active"><a href="javascript:void(0);"><i class="fa fa-search"></i></a></li>
        </ul>
      </div>
    </div>
    <div class="panel-right">
      <div class="panel-heading">
        <div class="panel-search-title"> 
          <span class="clr-light-blk">SEARCH</span>
        </div>
      </div>
      <div class="panel-wrapper collapse in">
        <div class="panel-body theme-form">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <input type="text" class="form-control" name="">
                <label>ID Number(s) ex. A123456, A654321</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="row">
                <div id="date_range" class="col-sm-12">
                  <div class="form-group ">
                    <select class="form-control" id="join_range" name="join_range">
                      <option value="" hidden=""> </option>
                      <option value="Range">Range</option>
                      <option value="Exactly">Exactly</option>
                      <option value="Before">Before</option>
                      <option value="After">After</option>
                    </select>
                    <label>Order Date</label>
                  </div>
                </div>
                <div class="select_date_div col-sm-9" style="display:none">
                  <div class="form-group ">
                    <div id="all_join" class="input-group">
                      <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                      <input type="text" name="added_date" id="added_date" value="" class="form-control" />
                    </div>
                    <div  id="range_join" style="display:none;">
                      <div class="phone-control-wrap">
                        <div class="phone-addon">
                          <label class="mn">From</label>
                        </div>
                        <div class="phone-addon">
                          <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" name="fromdate" id="fromdate" value="" class="form-control" />
                          </div>
                        </div>
                        <div class="phone-addon">
                          <label class="mn">To</label>
                        </div>
                        <div class="phone-addon">
                          <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" name="todate" id="todate" value="" class="form-control" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-sm-6" style="clear: left;">
              <div class="form-group">
                <input type="text" class="form-control" name="">
                <label>Name</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <input type="text" class="form-control" name="">
                <label>Phone</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <input type="text" class="form-control" name="">
                <label>Email</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <select class="se_multiple_select" id="product_search_id" multiple="multiple">
                  <optgroup label="Dental">
                    <option>Products 001 ( Product_Dental_1500 )</option>
                    <option>Products 001 ( Product_Dental_1500 )</option>
                    <option>Products 001 ( Product_Dental_1500 )</option>
                  </optgroup>
                  <optgroup label="Vision">
                    <option>Products 001 ( Product_Dental_1500 )</option>
                    <option>Products 001 ( Product_Dental_1500 )</option>
                    <option>Products 001 ( Product_Dental_1500 )</option>
                  </optgroup>
                </select>
                <label>Products</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <select class="form-control">
                  <option hidden=""> </option>
                  <option>Active</option>
                  <option>Inactive</option>
                </select>
                <label>Product Status</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <select class="se_multiple_select" id="tree_agent_id" multiple="multiple">
                  <option>Agent Name - A1234567</option>
                  <option>Agent Name - A1234567</option>
                  <option>Agent Name - A1234567</option>
                  <option>Agent Name - A1234567</option>
                  <option>Agent Name - A1234567</option>
                </select>
                <label>Tree Agent ID(s) ex. A555555, A444444</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <select class="form-control">
                  <option hidden=""> </option>
                  <option>Active</option>
                  <option>pending</option>
                  <option>On Hold</option>
                  <option>Inactive</option>
                </select>
                <label>Status</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="row">
                <div id="reversal_date_range" class="col-sm-12">
                  <div class="form-group ">
                    <select class="form-control" id="reversal_range" name="reversal_range">
                      <option value="" hidden=""> </option>
                      <option value="Range">Range</option>
                      <option value="Exactly">Exactly</option>
                      <option value="Before">Before</option>
                      <option value="After">After</option>
                    </select>
                    <label>Reversal Date</label>
                  </div>
                </div>
                <div class="select_reversaldate_div col-sm-9" style="display:none">
                  <div class="form-group ">
                    <div id="reversal_all_join" class="input-group">
                      <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                      <input type="text" name="added_date" id="reversal_added_date" value="" class="form-control" />
                    </div>
                    <div  id="reversal_range_join" style="display:none;">
                      <div class="phone-control-wrap">
                        <div class="phone-addon">
                          <label class="mn">From</label>
                        </div>
                        <div class="phone-addon">
                          <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" name="fromdate" id="reversal_fromdate" value="" class="form-control" />
                          </div>
                        </div>
                        <div class="phone-addon">
                          <label class="mn">To</label>
                        </div>
                        <div class="phone-addon">
                          <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" name="todate" id="reversal_todate" value="" class="form-control" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-sm-6" style="clear: left;">
              <div class="form-group">
                <select class="form-control">
                  <option hidden=""> </option>
                  <option>123</option>
                </select>
                <label>Enrolling Agent ID</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <select class="form-control">
                  <option hidden=""> </option>
                  <option>123</option>
                </select>
                <label>Status</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <select class="form-control">
                  <option hidden=""> </option>
                  <option>123</option>
                </select>
                <label>Dependent ID</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <select class="form-control">
                  <option hidden=""> </option>
                  <option>123</option>
                </select>
                <label>Dependent Name</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <input type="text" class="form-control" name="">
                <label>Next Billing Date</label>
              </div>
            </div>
          </div>
          <div class="panel-footer">
            <a href="javascript:void(0);" class="btn btn-info"><i class="fa fa-search"></i> Search</a>
            <a href="javascript:void(0)" class="btn btn-info btn-outline"><i class="fa fa-search-plus"></i>View All</a>
            <a href="javascript:void(0);" class="btn text-red"><i class="fa fa-download">  Export </i></a>
          </div>
        </div>
      </div>
    </div>
  </form>
  <div class="search-handle">
    <a href="#" data-perform="panel-collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></a>
  </div>
</div>
<div class="panel panel-block panel-default">
  <div class="panel-body">
    <div class="table-responsive">
      <table class="<?=$table_class?>">
        <thead>
          <tr>
            <th>ID/Added Date</th>
            <th>Details</th>
            <th>Enrolling Agent ID/Name</th>
            <th class="text-center">Products</th>
            <th>Status</th>
            <th width="80px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><a href="javascript:void(0)" class="fw500 text-red">M12345678</a><br>06/03/2020</td>
            <td>Derrict Larson<br>(274) 284-8451<br>derrict@hoeger.net</td>
            <td><a href="javascript:void(0)" class="fw500 text-red">A123456</a><br>Jimmy Mathis</td>
            <td class="text-center text-red">4</td>
            <td>
              <select class="form-control">
                <option>123</option>
              </select>
            </td>
            <td class="icons">
              <a href="javascript:void(0)"><i class="fa fa-eye"></i></a>
              <a href="javascript:void(0)"><i class="fa fa-lock"></i></a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
  $("#product_search_id, #tree_agent_id").multipleSelect({
       selectAll: false,
  });

  $(document).off('change', '#join_range');
  $(document).on('change', '#join_range', function(e) {
    e.preventDefault();
    if($(this).val() == ''){
      $('.select_date_div').hide();
      $('#date_range').removeClass('col-sm-3').addClass('col-sm-12');
    }else{
      $('#date_range').removeClass('col-sm-12').addClass('col-sm-3');
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

$(document).off('change', '#reversal_range');
$(document).ready(function(){
  $(document).on('change', '#reversal_range', function(e) {
    e.preventDefault();
    if($(this).val() == ''){
      $('.select_reversaldate_div').hide();
      $('#reversal_date_range').removeClass('col-sm-3').addClass('col-sm-12');
    }else{
      $('#reversal_date_range').removeClass('col-sm-12').addClass('col-sm-3');
      $('.select_reversaldate_div').show();
      if ($(this).val() == 'Range') {
        $('#reversal_range_join').show();
        $('#reversal_all_join').hide();
      } else {
        $('#reversal_range_join').hide();
        $('#reversal_all_join').show();
      }
    }
  });


  $("#ticket_group_id, #ticket_status_id").multipleSelect({
       selectAll: false,
  });

  function chargeBack(id) {
    $.colorbox({
      href: 'eticket_accounting.php',
      iframe: true,
      width: '500px',
      height: '280px',
      fastIframe: false,
    });
  }

  $(document).on('change', '.status_action', function(e) {
    e.preventDefault();
    var id = $(this).attr('data-id');
    var this_val = $(this).val();

    if (this_val == 'accounting_popup') {
      chargeBack(id);
    } 
  });

  $('.add_ticket').colorbox({
      iframe:true, width:"500px", height:"590px",
    });
  });
});
</script>