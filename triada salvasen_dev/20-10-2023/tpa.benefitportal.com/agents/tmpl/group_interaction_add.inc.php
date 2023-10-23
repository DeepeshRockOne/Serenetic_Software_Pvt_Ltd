<div class="panel panel-default panel-block add_note_panel">
	<div class="panel-heading">
    	Interaction Details
    </div>
    <div class="panel-body">
    	<div class="text-center mb15">
        	<h4 class="mn"><?=ucfirst($group_res['business_name'])?> - <span class="font-light"><?=$group_res['rep_id']?></span></h4>
            <p class="text-light-gray"><?=$tz->getDate($group_res['date']);?></p>
        </div>
        <form class="theme-form" name="interaction_form" id="interaction_form">
            <input type="hidden" name="group_id" id="group_id" value="<?=$group_res['id']?>">
            <input type="hidden" name="group_name" id="group_name" value="<?=ucfirst($group_res['business_name'])?>">
            <input type="hidden" name="rep_id" id="rep_id" value="<?=$group_res['rep_id']?>">
            <input type="hidden" name="interaction_detail_id" id="interaction_detail_id" value="<?=checkIsset($interaction_detail_id)?>">
        	<div class="form-group">
            	<select class="form-control" name="interaction_id" id="interaction_id" data-old_type='<?=$interaction_detail['type']?>'>
                	<option value="" selected="selected" hidden></option>
                    <?php if(!empty($interaction_type)) { 
                        foreach($interaction_type as $type) {?>
                         <option value="<?=$type['id']?>" <?=checkIsset($interaction_detail['int_id'])==$type['id'] ? "selected" : ''?> data-type='<?=$type['type']?>'><?=$type['type']?></option>
                    <?php } } ?>
                </select>
                <label>Type<em>*</em></label>
                <p class="error"><span id="err_interaction_id"></span></p>
            </div>
            <div class="form-group height_auto">
            	<textarea name="description" cols="" rows="6" class="form-control"><?=checkIsset($interaction_detail['description'])?></textarea>
                <label>Description<em>*</em></label>
                <p class="error"><span id="err_description"></span></p>
            </div>
            <div class="clearfix"></div>
            <div class="form-group m-t-10">
                    <select name="products[]" id="products" multiple="multiple" class="se_multiple_select">
                        <?=$drop_down_html?>
                    </select>
                    <label>Associated Product</label>
                    <p class="error"><span id="err_products"></span></p>
            </div>
            <div class="pull-right">
              <label><input type="checkbox" name="create_etickets" id="create_etickets"> Create eTicket</label>
            </div>
            <div class="clearfix" id="e_tickets_div" style="display: none;">
              <h4 class="m-b-30">+ E Ticket</h4>
              <div class="form-group">
                <select class="form-control" id="category_id" name="category_id">
                  <option data-hidden="true"></option>
                  <?php if(!empty($category)) {
                    foreach($category as $cat){?>
                    <option value="<?=$cat['id']?>"><?=$cat['title']?></option>
                  <?php } } ?>
                </select>
                <label>Category<em>*</em></label>
                <p class="error"><span id="err_category_id"></span></p>
              </div>
              <div id="assignee_div"></div>
              <div class="form-group">
                <input type="text" class="form-control" name="subject">
                <label>Subject<em>*</em></label>
                <p class="error"><span id="err_subject"></span></p>
              </div>
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
    }).multipleSelect('refresh');
  });

  $(document).off("click","#save_interaction");
  $(document).on("click","#save_interaction",function(e){

    $('.error span').html('');
    $("#ajax_loader").show();
      var $data = $("#interaction_form").serializeArray();
      var $type = $("#interaction_id :selected").attr("data-type");
      var $oldtype = $("#interaction_id").attr("data-old_type");
      $data.push({'name':'interaction_type','value':$type});
      $data.push({'name':'interaction_old_type','value':$oldtype});
      $.ajax({
        url:"ajax_group_interaction_add.php",
        data:$data,
        type:"POST",
        dataType:"JSON",
        success:function(res){
            $("#ajax_loader").hide();
            if(res.status === 'success'){
                window.onunload = refreshParent;
                window.close();
            }else if(res.status === 'fail'){
                var is_error = true;
                $('.error span').html('');
               $.each(res.errors,function(index,value){
                $('#err_' + index).html(value).show();
                      if(is_error){
                          var offset = $('#err_' + index).offset();
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
  
  $(document).off("change","#create_etickets");
  $(document).on("change","#create_etickets",function(e){
    var $checked =  $(this).is(":checked");
    if($checked){
      $("#e_tickets_div").show();
    }else{
      $("#e_tickets_div").hide();
    }
  });

  $(document).off('change','#category_id');
  $(document).on('change','#category_id',function(e){
    $("#assignee_div").html("");
    $("#assignee_div").removeClass('form-group');
    $.ajax({
      url:"add_etickets.php",
      data:{
        is_ajaxed :1,
        categoryId : $(this).val(),
      },
      dataType:'json',
      type:'post',
      beforeSend :function(e){
        $("#ajax_loader").show();
      },
      success:function(res){
        $("#ajax_loader").hide();
        if(res.status == 'success'){
            $("#assignee_div").html(res.data_html);
        }else{
          $("#assignee_div").html(res.data_html);
        }
        $("#assignee_div").addClass('form-group');
        common_select();
      }
    });
  });
function refreshParent() {
    window.opener.location.reload();
}
</script>