<style type="text/css">
.move_resource:hover{cursor:move;}
</style>
<div class="panel panel-default panel-block ">
    <div class="panel-heading">
        <div class="panel-title">
            <h4 class="mn">
        <?php if(!empty($_GET['id'])){ ?>
            Edit Category  - <span class="fw300"><?= $name ?></span>
        <?php }else{ ?>
            + Category
        </h4>
        <?php } ?>
        </div>
    </div>
    <div class="panel-body prd_cat_edit">
        <form action="ajax_product_categories_edit.php" method="post" name="edit_category_form" id="edit_category_form" class="theme-form">
            <input type="hidden" name="category_id" id="category_id" value="<?= $id ?>">
            <input type="hidden" name="old_category_image" id="old_category_image" value="<?= $old_category_image ?>">
            <div class="form-group">
                <input type="text" name="category_name" id="category_name" class="form-control" value="<?= (!empty($name))?$name:'' ?>">
                <label>Name Category</label>
                <p class="error" id="error_category_name"></p>
            </div>
            <div class="form-group">
                <div class="custom_drag_control">
                    <span class="btn btn-action" style="border-radius:0px;">Upload Image</span>
                    <input type="file" class="gui-file" id="category_image" name="category_image">
                    <input type="text" class="gui-input" placeholder="Choose File">
                    <p class="error" id="error_category_image"></p>
                </div>
           </div>
           <div class="phone-control-wrap">
               <div class="phone-addon">
                   <div class="form-group height_auto">
                       <textarea class="form-control" id="short_description" name="short_description" rows="4" placeholder="Short Description of Category" maxlength="160"><?= $short_description ?></textarea>
                       <p class="pull-right text-light-gray fs12">
                        <span id="character_count"><?= !empty($short_description) ? 160 - strlen($short_description) : 160; ?>
                        </span> Characters left
                        </p>
                        <p class="error pull-left" id="error_short_description"></p>
                   </div>
               </div>
               <div class="phone-addon height_auto w-130 v-align-top">
                   <div class="prd_cat_thumb">
                       <img src="<?=$CATEGORY_IMAGE_WEB.$category_image?>" height="130px" class="img-responsive">
                       <p>Current</p>
                   </div>
               </div>
           </div>
        <div class="table-responsive m-t-10" id="rearrange">
            <table class="<?=$table_class?>" >
                <thead>
                    <tr>
                        <th width="45px"></th>
                        <th>Set the display order for products in this category</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($arrangeCatProduct)) { ?>
                        <?php foreach ($arrangeCatProduct as $key => $value) { ?>
                            <?php if(isset($value['connection']) && $value['connection'] == 0) { ?>
                                    <tr class="reorder" id="main_<?= $value['product_id'] ?>" >
                                       <td >
                                          <div class="move_resource" >

                                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                            </div>
                                        </td>
                                        <td >
                                          <?= $value['productName'].' ('.$value['product_code'].')' ?>
                                        </td>
                                    </tr>
                                
                            <?php }else{ ?>
                                <tr class="reorder" id="connected_<?= $key ?>">
                                    <td class="v-align-top">
                                        <div class="move_resource">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                        </div>
                                    </td>
                                    <td class="pn" width="100%">
                                    <table class="<?=$table_class?>" width="100%">
                                        <tbody>

                                            <?php foreach ($value as $connKey => $connValue) { ?>
                                                <tr>
                                                    <td class="table_dark_danger ">
                                                        <?= $connValue['productName'].' ('.$connValue['product_code'].')' ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                        
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <p class="text-right text-action"><em class="fw600 fs12">Top product will show first</em></p>
</form>
        <div class="text-center m-t-20">
                <button class="btn btn-action" type="button" name="save" id="save">Save</button>
                <button class="btn red-link" type="button" onclick='parent.$.colorbox.close()' >Close</button>
            </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
	   $('#rearrange').sortable({
		  axis: 'y',
		  helper: fixHelper,
		  cursor: 'move',
		  items: 'tr.reorder',
		  placeholder: 'tr-placerholder',
		  handle: '.move_resource',
          update: function () {
                var data = $(this).sortable('toArray');
                
                $("#ajax_loader").show();
                $.ajax({
                    url: '<?= $ADMIN_HOST ?>/ajax_product_order_change.php',
                    dataType:'JSON',
                    data: {data:data},
                    type: 'POST',
                    success:function(res){
                        $("#ajax_loader").hide();
                    }
                });
            }
		});
  var fixHelper = function (e, ui) {
    ui.children().each(function () {
      $(this).width($(this).width());
    });
    return ui;
  };

        $('#edit_category_form').ajaxForm({
            beforeSend:function(){
              $("#ajax_loader").show();
            },
            type: 'POST',
            dataType: 'json',
            success: function (res) {
                $(".error").html('');
                $("#ajax_loader").hide();
                if(res.status=='success'){
                    parent.$.colorbox.close();
                    window.parent.setNotifySuccess(res.msg);
                }else{
                    $.each(res.errors, function (index, error) {
                        $('#error_' + index).html(error);
                        var offset = $('#error_' + index).offset();
                        if(typeof(offset) === "undefined"){
                            console.log("Not found : "+index);
                        }else{
                            var offsetTop = offset.top;
                            var totalScroll = offsetTop - 195;
                            $('body,html').animate({
                                scrollTop: totalScroll
                            }, 1200);
                        }                        
                    });
                }
            }
        });
    });
    $(document).on("click","#save",function(){
        $("#edit_category_form").submit();
    });
    $(document).on('keyup', '#short_description', function (e) {
        var chars = $("#short_description").val().length;
        if(160 - chars<=0){
            $("#character_count").parent("span").addClass("text-danger");
        }else{
            $("#character_count").parent("span").removeClass("text-danger");
        }
        $("#character_count").text(160 - chars);
    });
	
	
</script>