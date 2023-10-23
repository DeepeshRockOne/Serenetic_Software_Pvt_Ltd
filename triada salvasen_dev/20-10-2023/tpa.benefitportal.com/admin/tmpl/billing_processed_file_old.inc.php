
<div class="panel panel-default panel-block">
  <div class="panel-body">
    <h4 class=" pull-left m-t-7">ASH File - <span class="fw300">Processed Files</span></h4>
    <div class="pull-right">
      <div class="m-b-15">
        <div class="note_search_wrap auto_size" id="search_div" style="display: none; max-width: 100%;">
          <div class="phone-control-wrap theme-form">
            <div class="phone-addon">
              <div class="form-group height_auto mn">
                <a href="javascript:void(0);" class="search_close_btn text-light-gray">X</a>
              </div>
            </div>
            <div class="phone-addon w-130">
                 <div class="form-group height_auto mn">
                   <input type="text" name="" class="form-control">
                   <label>Member ID</label>
                 </div>
            </div>
            <div class="phone-addon">
                 <div class="form-group height_auto mn w-130">
                   <select class="form-control">
                     <option></option>
                     <option>Range</option>
                     <option>Exactly</option>
                     <option>Before</option>
                     <option>After</option>
                   </select>
                   <label>Added Date</label>
                 </div>
            </div>
            <div class="phone-addon">
               <div class="form-group height_auto mn">
                 <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <div class="pr">
                    <input id="" type="text" class="form-control" name="" placeholder="">
                    <label>Date</label>
                    </div>
                  </div>
               </div>
            </div>
            <div class="phone-addon w-80">
              <div class="form-group height_auto mn">
                <a href="javascript:void(0);" class="btn btn-info search_button">Search</a>
              </div>
            </div>
          </div>
        </div>
        <a href="javascript:void(0);" class="search_btn" ><i class="fa fa-search fa-lg text-blue"></i></a>
      </div>
    </div>
    <div class="clearfix"></div>
    <div class="table-responsive">
      <table class="<?=$table_class?>">
        <thead>
          <tr>
            <th>Added Date</th>
            <th>File Name</th>
            <th>Processed By</th>
            <th class="text-center" width="30%">Records</th>
            <th width="70px">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>12/10/2019</td>
            <td>ASH_ROLLOVER_File_201912091930.csv</td>
            <td><a href="javascript:void(0);" class="fw500 text-action">AD12345</a><br>Laura Greene</td>
            <td class="text-center">91</td>
            <td class="icons text-center">
              <a href="javascript:void(0);" data-toggle="tooltip" title="Download"><i class="fa fa-download"></i></a>
            </td>
          </tr>
          <tr>
            <td>12/10/2019</td>
            <td>ASH_ROLLOVER_File_201912091930.csv</td>
            <td><a href="javascript:void(0);" class="fw500 text-action">AD12345</a><br>Laura Greene</td>
            <td class="text-center">38</td>
            <td class="icons text-center">
              <a href="javascript:void(0);" data-toggle="tooltip" title="Download"><i class="fa fa-download"></i></a>
            </td>
          </tr>
          <tr>
            <td>12/10/2019</td>
            <td>ASH_ROLLOVER_File_201912091930.csv</td>
            <td><a href="javascript:void(0);" class="fw500 text-action">AD12345</a><br>Laura Greene</td>
            <td class="text-center">96</td>
            <td class="icons text-center">
              <a href="javascript:void(0);" data-toggle="tooltip" title="Download"><i class="fa fa-download"></i></a>
            </td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="5">
                <div class="row table-footer-row">
                <div class="col-sm-12">
                  <div class="pull-left">
                    <div class="dataTables_info">1 to 3 of 3 Records</div>
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



<script type="text/javascript">
$(document).ready(function(){
  $("#products").multipleSelect({
  });
  $(document).on("click", ".search_btn", function(e) {
    e.preventDefault();
    $(this).hide();
    $("#search_div").css('display', 'inline-block');
  });
  $(document).on("click", ".search_close_btn", function(e) {
    e.preventDefault();
    $("#search_div").hide();
    $(".search_btn").show();
  });
});
</script>