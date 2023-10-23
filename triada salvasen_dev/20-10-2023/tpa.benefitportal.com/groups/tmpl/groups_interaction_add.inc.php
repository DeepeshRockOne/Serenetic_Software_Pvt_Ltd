<div class="panel panel-default panel-block add_note_panel">
	<div class="panel-heading">
    	Interaction Details
    </div>
    <div class="panel-body">
    	<div class="text-center mb15">
        	<h4 class="mn">Punit Ladani- <span class="font-light">A123456</span></h4>
          <p class="text-light-gray">Wed., Feb. 14, 2019 @ 2:35 PM</p>
        </div>
        <form class="theme-form">
        	<div class="form-group">
            	<select class="form-control">
                	<option data-hidden="true"></option>
                  <option>application</option>
                  <option>summary report</option>
                </select>
                <label>Type<em>*</em></label>
            </div>
            <div class="form-group height_auto">
            	<textarea rows="6" class="form-control" placeholder="Description"></textarea>
            </div>
            <div class="form-group">
                <div class="group_select">
                    <select name="products[]" id="products" multiple="multiple" class="se_multiple_select">
                        <optgroup label="<?= $DEFAULT_SITE_NAME ?>">
                          <option><?= $DEFAULT_SITE_NAME ?> Main (P306682)</option>
                          <option>variableEmoployee6 (P403144)</option>
                        </optgroup>
                    </select>
                    <label>Associated Product<em>*</em></label>
                </div>
            </div>
       <div class="pull-right">
              <label><input type="checkbox" name=""> Create eTicket</label>
            </div>
            <div class="clearfix"></div>
            <h4 class="m-b-30">+ E Ticket</h4>
            <div class="form-group">
              <select class="form-control">
                <option data-hidden="true"></option>
                <option>Category 1</option>
                <option>Category 2</option>
                <option>Category 3</option>
              </select>
              <label>Category<em>*</em></label>
            </div>
            <div class="form-group">
              <select class="form-control">
                <option data-hidden="true"></option>
                <option>Assignee 1</option>
                <option>Assignee 2</option>
                <option>Assignee 3</option>
              </select>
              <label>Assignee<em>*</em></label>
            </div>
            <div class="form-group">
              <input type="text" class="form-control" name="">
              <label>Subject<em>*</em></label>
            </div>
            <div class="text-center">
              <button name="save" id="save_interaction" type="button" class="btn btn-action" >Save</button>
                <a href="javascript:void(0)" onclick="window.close()" class="btn red-link">Cancel</a>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
    $("#products").multipleSelect({
        width:'100%'
    });
  });
</script>