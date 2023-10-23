<div class="panel panel-default">
  <div class="panel-heading">
    <div class="panel-title">
      <h4 class="mn">Personal Production Report - <span class="fw300">Company 001</span></h4>
    </div>
  </div>
  <div class="panel-body">
    <div class="table-responsive">
      <table class="<?=$table_class?> production_report_tbl">
        <tbody>
          <tr>
            <td class="br-n bg_dark_danger text-white fs16">Company 001 (G123456)</td>
            <td>
              <div class="row theme-form">
                <div class="col-sm-6">
                  <div class="form-group height_auto mn">
                    <input type="text" name="" class="form-control">
                    <label>From</label>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group height_auto mn">
                    <input type="text" name="" class="form-control">
                    <label>To</label>
                  </div>
                </div>
              </div>
            </td>
          </tr>
          <tr>
            <td>New Business Sales</td>
            <td>$21,322.65</td>
          </tr>
          <tr>
            <td>Renewal Sales</td>
            <td>$30,000.75</td>
          </tr>
          <tr>
            <td>Total Sales</td>
            <td>$51,323.40</td>
          </tr>
          <tr>
            <td>Total Refunds/Voids</td>
            <td>$30,000.75</td>
          </tr>
          <tr>
            <td>Total Chargebacks</td>
            <td>$51,323.40</td>
          </tr>
          <tr>
            <td>New Members Enrolled</td>
            <td>289</td>
          </tr>
        </tbody>
      </table>
    </div>

    <p class="fw600 m-t-20 lato_font">Top Products (New Business Only)</p>
        <div class="table-responsive">
          <table class="<?=$table_class?>">
            <thead>
              <tr>
                <th>Product Name</th>
                <th>Premiums</th>
                <th >Policies</th>
                <th width="120px">New Members</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Panthera HSP9 (PantheraHSP9_SSBB)</td>
                <td>$12,232.65</td>
                <td>111</td>
                <td>111</td>
              </tr>
              <tr>
                <td>Secure Care Enhanced (SECURECARE_ENHANCED_BM)</td>
                <td>$1,119.75</td>
                <td>10</td>
                <td>10</td>
              </tr>
              <tr>
                <td>Secure Care Enhanced (SECURECARE_STANDARD_BM)</td>
                <td>$1,056.25</td>
                <td>8</td>
                <td>8</td>
              </tr>
              <tr>
                <td>Panthera HSP3 (PantheraHSP3_SSBB)</td>
                <td>$1,008.35</td>
                <td>4</td>
                <td>4</td>
              </tr>
              <tr>
                <td>Dental</td>
                <td>$1,232.65</td>
                <td>40</td>
                <td>40</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="text-center m-t-20"> <a href="javascript:void(0);" class="btn btn-action">Export</a> <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Close</a> </div>
  </div>
</div>