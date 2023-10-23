<?php include "notify.inc.php";?>
<style type="text/css">
  .fee_structure .list-group-item{padding: 7px;  margin-top: 5px;}
  .fee_structure .radio{margin-top: 0px;}
  .fee_structure .list-group-item-lights{background: #eeeeee;color: grey}
  .fee_structure .list-group-item-info{color: grey}
</style>
<div class="add_level_panelwrap add_fee_popup">
  <div class="panel panel-default panel-block Font_Roboto ">
    <div class="panel-heading">
      <h4 class="mn fw500">Add Fee</h4>
    </div>
    <div class="panel-body theme-form">
      <p class="fw500">Fee Information</p>
      <div class="row">
        <!--  static fields -->
        <div class="col-xs-4">
          <div class="form-group">
            <input name="" type="text" class="form-control" />
            <label>Fee Name</label>
          </div>  
        </div>
        <div class="col-xs-4">
          <div class="form-group">
            <input name="" type="text" class="form-control" />
            <label>Fee ID(Must Be Unique,ex.F1234</label>
          </div>  
        </div>
        <div class="col-xs-4">
          <div class="form-group">
            <input name="" type="text" class="form-control" />
            <label>Fee Type</label>
          </div>  
        </div>
      </div>

      <div class="row">
        <!--  static fields -->
        <div class="col-xs-6">
          <div class="form-group">
            <input name="" type="text" class="form-control" />
            <label>Products</label>
          </div>  
        </div>
        <div class="col-xs-3">
          <div class="form-group">
            <div id="all_join" class="input-group"> <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            <input type="text" class="form-control date_mask listing_search" placeholder="Effective Date">
            <!-- <label>Effective date</label> -->
            </div>  
          </div>
        </div>
        <div class="col-xs-3">
          <div class="form-group">
            <div id="all_join" class="input-group"> <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            <input type="text" class="form-control date_mask listing_search" placeholder="Termination Date">
            <!-- <label>Effective date</label> -->
            </div>  
          </div>
        </div>
      </div>

      <div class="clearfix"></div>

      <!-- fee staructure -->
      <div class="fee_structure_section">
        <p class="fw500">Fee Structure</p>
        <ul class="list-group fee_structure">
           
           <li class="list-group-item list-group-item-lights">Second item <span class="radio pull-right"><input type="radio" value="yes" />Yes<input type="radio" value="No" />No</li>
            <li class="list-group-item list-group-item-info">Second item <span class="radio pull-right"><input type="radio" value="yes" />Yes<input type="radio" value="No" />No</li>
           <li class="list-group-item list-group-item-lights">Second item <span class="radio pull-right"><input type="radio" value="yes" />Yes<input type="radio" value="No" />No</li>
            <li class="list-group-item list-group-item-info">Second item <span class="radio pull-right"><input type="radio" value="yes" />Yes<input type="radio" value="No" />No</li>
        </ul>
      </div>
      <div class="clearfix"></div>

      <!-- fee price -->
      <div class="fee_price_section">
        <p class="fw500">Fee Price</p>
        <div class="row">
          <div class="col-xs-4">
            <div class="form-group">
              <input name="" type="text" class="form-control" placeholder="$0.00" />
            </div>  
          </div>
        </div>
      </div>
      <div class="form-group text-center m-t-30">
        <!-- static button -->
        <button class="btn btn-action">Add Fee</button>
        <a href="javascript:void(0);" onclick='parent.$.colorbox.close(); return false;' class="btn red-link">Cancel</a>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">

</script>