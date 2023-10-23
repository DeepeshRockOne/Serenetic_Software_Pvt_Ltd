<form name="generate_listbill_form" id="generate_listbill_form" method="POST">
    <div class="panel panel-default panel-block theme-form">
        <div class="panel-heading">
            <div class="panel-title ">
            <h4 class="mn text-black">Generate List Bill</h4>
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group height_auto mn">
                        <input type="text"  class="form-control" id="groupId" name="groupId" value="" >
                        <label>Group ID</label>
                        <p class="error text-left" id="error_groupId"></p>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group  height_auto mn">
                        <div class="input-group"> 
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <div class="pr">
                                <input type="text" name="lbDate" id="lbDate" value="" class="form-control date_picker" />
                                <label>List Bill Date</label>
                            </div>
                        </div>
                        <p class="error" id="error_lbDate"></p>
                    </div>
                </div>
                <div class="col-sm-12 text-center">
                    <a class="btn btn-action" id ="generate" href="javascript:void(0);"> Generate List Bill</a>
                    <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close();">Cancel</a>
                </div>
            </div>
        </div>
    </div>
<form>
<script type="text/javascript">
    $(document).ready(function() { 
        common_select();
        $(".date_picker").datepicker({
		    changeDay: true,
		    changeMonth: true,
		    changeYear: true
		});
    });

    //******************** Button Code Start **********************
	$(document).on("click","#generate",function(){
		$("#ajax_loader").show();
		$(".error").html("");
		$.ajax({
			url:'ajax_generate_manually_listbill.php',
			dataType:'JSON',
			data:$("#generate_listbill_form").serialize(),
			type:"POST",
			success:function(res){
				$("#ajax_loader").hide();
				if(res.status=="success"){
					window.parent.setNotifySuccess("Generate List Bill Request Successfully");
					window.parent.$.colorbox.close();
                    window.parent.location.reload();
				}else{
					var is_error = true;
	              	$.each(res.errors, function (index, value) {
	                  $('#error_' + index).html(value).show();
	                  if(is_error){
	                      var offset = $('#error_' + index).offset();
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
//******************** Button Code End   **********************
</script>