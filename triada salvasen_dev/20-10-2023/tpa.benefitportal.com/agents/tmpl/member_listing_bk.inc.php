<div class="container m-t-30">
<div class="panel panel-default panel-block panel-title-block">
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
              <input type="text" name="" class="form-control">
              <label>ID Number(s)</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="row">
              <div id="date_range" class="col-md-12">
                <div class="form-group">
                  <select class="form-control" id="join_range" name="join_range">
                    <option value=""> </option>
                    <option value="Range">Range</option>
                    <option value="Exactly">Exactly</option>
                    <option value="Before">Before</option>
                    <option value="After">After</option>
                  </select>
                  <label>Added Date</label>
                </div>
              </div>
              <div class="select_date_div col-md-9" style="display:none">
                <div class="form-group">
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
          <div class="col-sm-6">
            <div class="form-group height_auto">
              <select class="se_multiple_select" name=""  id="member_name" multiple="multiple" >
                <option>M000000 - John Doe</option>
              </select>
              <label>Name</label>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <input type="text" name="" class="form-control">
              <label>Phone</label>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <input type="text" name="" class="form-control">
              <label>Email</label>
            </div>
          </div>
              <div class="col-sm-6">
            <div class="form-group">
              <select class="form-control" data-live-search="true">
                <option data-hidden="true"></option>
                <option>Alabama</option>
                <option>Alaska</option>
                <option>Arizona</option>
              </select>
              <label>State</label>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group height_auto">
              <div class="group_select">
                <select class="se_multiple_select" name="products"  id="products" multiple="multiple" >
                  <optgroup label="Product1">
                    <option>Sub Product</option>
                    <option>Sub Product</option>
                    <option>Sub Product</option>
                  </optgroup>
                </select>
                <label>Products</label>
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group height_auto">
              <select class="form-control">
                <option></option>
                <option>Invited</option>
                <option>Pending Documentation</option>
                <option>Pending Approval</option>
                <option>Pending Contract</option>
                <option>Contracted</option>
                <option>Suspended</option>
                <option>Terminated</option>
              </select>
              <label>Product Status</label>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group height_auto">
              <select class="form-control" data-live-search="true">
                <option data-hidden="true"></option>
                <option>Agent Name - A1234567</option>
                <option>Agent Name - A1234567</option>
                <option>Agent Name - A1234567</option>
                <option>Agent Name - A1234567</option>
                <option>Agent Name - A1234567</option>
              </select>
              <label>Tree Agents</label>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group height_auto">
              <select class="se_multiple_select" name="status"  id="status" multiple="multiple" >
                <option>Active</option>
                <option>Pending</option>
                <option>On Hold</option>
                <option>Inactive</option>
              </select>
              <label>Status</label>
            </div>
          </div>
             <div class="col-sm-6">
            <div class="form-group">
              <select class="form-control">
                <option data-hidden="true"></option>
                <option>E&O Expired</option>
                <option>License Expired</option>
              </select>
              <label>Alerts</label>
            </div>
          </div>
        </div>
        <div class="panel-footer clearfix">
          <button type="submit" class="btn btn-info" name="" id="" > <i class="fa fa-search"></i> Search </button>
          <button type="button" class="btn btn-info btn-outline" name="" id=""> <i class="fa fa-search-plus"></i> View All </button>
          <button type="button" name="" id="" class="btn red-link"> <i class="fa fa-download"></i> Export </button>
        </div>
      </div>
    </div>
  </div>
  <div class="search-handle">
    <a href="#" data-perform="panel-collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></a>
  </div>
</div>
<div class="panel panel-default panel-block">
    <div class="panel-body">
        <h4 class="m-t-0 m-b-15">Agents Summary</h4>
        <div class="table-responsive">
            <table class="<?=$table_class?> text-center">
                <thead>
                    <tr>
                        <th>Members #</th>
                        <th>Active #</th>
                        <th>Pending #</th>
                        <th>Inactive</th>
                        <th>Hold</th>
                        <th width="90px">Production</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>6</td>
                        <td>3</td>
                        <td>1</td>
                        <td>1</td>
                        <td>1</td>
                        <td class="icons">
                            <a href="member_personal_production_report.php" class="member_personal_production_report"><img src="<?=$HOST?>/images/icons/production-icon.svg" width="30px"></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="panel panel-default panel-block">
  <div class="panel-body">
    <div class="clearfix m-b-15">
      <div class="pull-left">
        <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
          <div class="form-group mn">
            <label for="user_type">Records Per Page </label>
          </div>
          <div class="form-group mn">
            <select size="1" id="pages" name="pages" class="form-control" >
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
          </div>
        </div>
      </div>
      <div class="pull-right">
        <a href="javascript:void(0);" class="btn btn-action">+ Members</a>
      </div>
    </div>
    <div class="table-responsive">
      <table class="<?=$table_class?>">
        <thead>
          <tr>
            <th>ID/Added Date</th>
            <th>Details</th>
            <th>Enrolling Agent ID/Name</th>
            <th  class="text-center">Products</th>
            <th class="text-center">Status</th>
            <th class="text-center">Alerts</th>
            <th width="90px">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><a href="members_details.php" target="_blank" class="fw500 text-action">M12345678</a><br>06/03/2019</td>
            <td>
              <strong>Derrick Larson</strong><br>
              (274) 287-8451<br>
              derrick@hoeger.net
            </td>
            <td><a href="javascript:void(0);" class="fw500 text-action">A123456</a><br>Jimmy Mathis</td>
            <td class="text-center"><a href="javascript:void(0);" class="member_product_popover fw500 text-action">4</a>
            <div id="popover_content_wrapper" style="display: none">
              <div class="table-responsive">
                <table class="<?=$table_class?> fs12">
                  <thead>
                    <tr>
                      <th>Added Date<br>Subscription ID</th>
                      <th>Product Name<br>Enrolling Agent</th>
                      <th>Plan</th>
                      <th>Effective Date</th>
                      <th>Termination<br>Date</th>
                      <th>Next Billing Date</th>
                      <th>Fulfillment <br>Date</th>
                      <th>Total Premium</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>02/07/2019<br><a href="javascript:void(0);" class="fw500 text-action">W2345738</a></td>
                      <td><label class="label label-rounded label-success">HSA DB6550</label><p class="mn text-action">A123456</p></td>
                      <td>Member + Spouse</td>
                      <td>03/01/2019</td>
                      <td>-</td>
                      <td>03/24/2019</td>
                      <td>-</td>
                      <td>$50.00</td>
                    </tr>
                    <tr>
                      <td>02/07/2019<br><a href="javascript:void(0);" class="fw500 text-action">W2345738</a></td>
                      <td><label class="label label-rounded label-success">HSA DB6550</label><p class="mn text-action">A123456</p></td>
                      <td>Member + Spouse</td>
                      <td>03/01/2019</td>
                      <td>-</td>
                      <td>03/24/2019</td>
                      <td>-</td>
                      <td>$50.00</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </td>
          <td class="text-center">
           Active
          </td>
          <td class="text-center">
                          <a data-toggle="tooltip" href="javascript:void(0);" title="License Expired"><img src="images/icons/icon-alert.svg" /></a>
                        </td>
          <td class="icons">
            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="View"><i class="fa fa-eye"></i></a>
            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Add Products"><i class="fa fa-plus"></i></a>
          </td>
        </tr>
      </tbody>
      <tfoot>
      <tr>
        <td colspan="7"><div class="row table-footer-row">
          <div class="col-sm-12">
            <div class="pull-left">
              <div class="dataTables_info">1 to 2 of 2 Records</div>
            </div>
            <div class="pull-right">
              <div class="dataTables_paginate paging_bs_normal">
                <ul class="pagination pagination-md">
                  <li class="prev disabled"><span>&lt;</span></li>
                  <li class="live-link active"><a href="javascript:void(0);">1</a></li>
                  <li class="disabled"><span>&gt;</span></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </td>
    </tr>
    </tfoot>
  </table>
</div>
</div>
</div>
</div>
<script type="text/javascript">
  $(document).off('click', '.member_personal_production_report');
    $(document).on('click', '.member_personal_production_report', function (e) {
        e.preventDefault();
        $.colorbox({
          href: $(this).attr('href'),
          iframe: true, 
          width: '900px', 
          height: '500px'
        })
    });

  $(document).off('change', '#join_range');
  $(document).on('change', '#join_range', function(e) {
    e.preventDefault();
    if($(this).val() == ''){
      $('.select_date_div').hide();
      $('#date_range').removeClass('col-md-3').addClass('col-md-12');
    }else{
      $('#date_range').removeClass('col-md-12').addClass('col-md-3');
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


$(document).ready(function() {
  $("#status, #dependent_id, #member_name").multipleSelect({
       selectAll: false
  });
  $("#products").multipleSelect({
  });
   $('.member_product_popover').popover({ 
    html : true,
    container: 'body',
    trigger: 'click',
    template: '<div class="popover full_width_popover"><div class="arrow"></div><div class="popover-content"></div></div>',
    placement: 'auto top',
    content: function() {
      return $('#popover_content_wrapper').html();
    }
  });
});
</script>