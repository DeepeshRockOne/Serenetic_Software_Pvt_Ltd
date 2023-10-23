<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <h4 class="mn fw500">Notify Agents and Admins</h4>
  </div>
  <div class="panel-body">
    <form  method="POST" id="notifyForm" enctype="multipart/form-data"  autocomplete="off">
      <input type="hidden" name="product" id="product" value="<?= $product_id ?>">
      <div class="text-center">
        <p class="m-b-20"><strong>You are saving a product at one time was an active product. All changes are saved to the product, but would you like to communicate changes to the field of admins/agents?</strong></p>
        <div class="clearfix m-b-20 text-left">
          <label class="label-input"><input type="checkbox" name="notifyAgents" value="Y"> Email Agents contracted with this Product Listing</label>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-9">
          <textarea class="summernote" name="notifyContent" id="notifyContent">
          </textarea>
        </div>
        <div class="col-sm-3">
          <div class="editor_tag_wrap">
            <div class="tag_head"><h4>AVAILABLE TAGS&nbsp;<span class="fa fa-info-circle"></span></h4>
            </div>
            <div><label>[[fname]]</label></div>
            <div><label>[[lname]]</label></div>
            <div><label>[[email]]</label></div>
            <div><label>[[displayID]]</label></div>
            <div><label>[[productCode]]</label></div>
            <div><label>[[productName]]</label></div>
          </div>
        </div>
      </div>
      <div class="clearfix m-t-20">
          <label><input type="checkbox" name="notifyAdmins" value="Y"> Email all Admins in this Portal</label>
      </div>
      <p class="error" id="error_notifyError"></p>
      <div class="clearfix m-t-20 text-center">
          <a href="javascript:void(0);" class="btn btn-action" id="notifyEmail">Email</a>
          <a href="javascript:void(0);" class="btn red-link" onclick='window.parent.$.colorbox.close(); return false;'>Close</a>
      </div>
    </form>
  </div>
</div>


<script type="text/javascript">
  $(document).ready(function() {
    initCKEditor("notifyContent",false,"325px");
  });

  $(document).on("click","#notifyEmail",function(){
    $("#ajax_loader").show();
    $("#notifyContent").val(CKEDITOR.instances.notifyContent.getData());
    $.ajax({
      url: '<?= $ADMIN_HOST ?>/ajax_prd_add_notify.php',
      data:$("#notifyForm").serialize(),
      dataType:'JSON',
      type:"POST",
      success:function(res){
        $("#ajax_loader").hide();
        if(res.status=="Success"){
          window.parent.$.colorbox.close();
        }else{
          var is_error = true;
          $.each(res.errors, function (index, error) {
              $('#error_' + index).html(error);
              if (is_error) {
                  var offset = $('#error_' + index).offset();
                  if(typeof(offset) === "undefined"){
                      console.log("Not found : "+index);
                  }else{
                      var offsetTop = offset.top;
                      var totalScroll = offsetTop - 195;
                      $('body,html').animate({
                          scrollTop: totalScroll
                      }, 1200);
                      is_error = false;
                  }
              }
          });
        }
      }
    });
  });
</script>