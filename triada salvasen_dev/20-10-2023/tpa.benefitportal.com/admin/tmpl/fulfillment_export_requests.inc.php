<div class="panel panel-block panel-default">
	<div class="panel-body">
		<h4 class="pull-left mn">Export Requests</h4>
		<h5 class="pull-right fw300 text-gray mn">Reports are removed after 30 days.</h5>
	</div>
</div>
<div class="panel panel-block panel-default">
	<div class="panel-body">
		<h4 class="m-t-0 m-b-15">Pending</h4>
		<div id="pending_div" class="export_requests_data"></div>
	</div>
</div>

<div class="panel panel-block panel-default">
	<div class="panel-body">
	    <h4 class="m-t-0 m-b-15">Exporting</h4>
	    <div id="exporting_div" class="export_requests_data"></div>
	</div>
</div>
<div class="panel panel-block panel-default">
	<div class="panel-body">
	    <h4 class="m-t-0 m-b-15">Completed</h4>
	    <div id="completed_div" class="export_requests_data"></div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
  dropdown_pagination('pending_div','exporting_div','completed_div')
	load_pending_requests();
	load_exporting_requests();
	load_completed_requests();

  setTimeout(function(){window.location.reload();},60000);

	$(document).on('click','.remove_request',function(){
		id = $(this).data('id');
		removeRequest(id);
	});

  $(document).off('click', '.export_requests_data ul.pagination li a');
    $(document).on('click', '.export_requests_data ul.pagination li a', function (e) {
        e.preventDefault();
        var section_id = $(this).closest(".export_requests_data").attr('id');
        $('#ajax_loader').show();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#'+section_id).html(res).show();
                $('[data-toggle="tooltip"]').tooltip();
                common_select();
            }
        });
    });

});
function load_pending_requests(){
	$('#ajax_loader').show();
    $.ajax({
      url: "ajax_get_fulfillment_requests.php",
      type: 'GET',
      data: {status : 'Pending'},
      success: function(res) {
        $('#ajax_loader').hide();
        $('#pending_div').html(res);
        common_select();
        $('[data-toggle="tooltip"]').tooltip();
      }
    });
    return false;
}
function load_exporting_requests(){
	$('#ajax_loader').show();
    $.ajax({
      url: "ajax_get_fulfillment_requests.php",
      type: 'GET',
      data: {status : 'Running'},
      success: function(res) {
        $('#ajax_loader').hide();
        $('#exporting_div').html(res);
        common_select();
        $('[data-toggle="tooltip"]').tooltip();
      }
    });
    return false;
}
function load_completed_requests(){
	$('#ajax_loader').show();
    $.ajax({
      url: "ajax_get_fulfillment_requests.php",
      type: 'GET',
      data: {status : 'Processed'},
      success: function(res) {
        $('#ajax_loader').hide();
        $('#completed_div').html(res);
        common_select();
        $('[data-toggle="tooltip"]').tooltip();
      }
    });
    return false;
}

removeRequest = function(id) {
    
    swal({
      text: 'Delete Request: Are you sure?',
      showCancelButton: true,
      confirmButtonText: 'Confirm',
      cancelButtonText: 'Cancel',
      showCloseButton: true,
    }).then(function() {
      $("#ajax_loader").show();
      $.ajax({
        url: "fulfillment_export_requests.php",
        dataType: 'JSON',
        type: 'GET',
        data: {
          id: id,
          action: 'Delete'
        },
        success: function(res) {
          $("#ajax_loader").hide();
          if (res.status == 'success') {
            setNotifySuccess(res.message);
            setTimeout(function(){ window.location.reload(); }, 1000);
          }
        }
      });
    }, function(dismiss) {
      
    });
  }
<?=generateMergeCSVJS()?>  
</script>