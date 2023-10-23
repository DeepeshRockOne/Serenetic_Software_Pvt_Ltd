<?php if (checkIsset($is_ajaxed)) {?>

<!-- table code start -->
  <div class="table-responsive">
     <table class="<?= $table_class ?>">
        <thead>
           <tr>
              <th>Name</th>
              <th>Category</th>
              <th class="text-center" width="50px">Action</th>
           </tr>
        </thead>
        <tbody>
            <?php if($totalResources > 0) { ?>
                <?php foreach($fetchResources as $resources) { ?>
            <tr>
              <td>
                 <a href="javascript:void(0);" class="text-action fw500"><?=$resources["name"]?></a>
              </td>
              <td><?=$resources["category"]?></td>
              <td class="text-center icons">
                <?php 
                if(!empty($resources['file_name']) && file_exists($SYSTEM_RESOURCES_DIR.$resources['file_name'])){ ?>
                 <a href="<?=$SYSTEM_RESOURCES_WEB.$resources['file_name']?>" data-toggle="tooltip" data-title="Download" download><i class="fa fa-file-pdf-o"
                    aria-hidden="true"></i></a>
                <?php } ?>
              </td>
            </tr>
            <?php } }else{ ?>
                <tr>
                    <td colspan="3">Resource(s) not found</td>
                </tr>
            <?php } ?>
        </tbody>
         <?php if($totalResources > 0) { ?>
            <tfoot>
            <tr>
                <td colspan="3">
                <?php echo $paginate_resource->links_html; ?>
                </td>
            </tr>
            </tfoot>
        <?php } ?>
     </table>
  </div>
<!-- table code ends -->
<?php } else { ?>

<div id="reporting_table">
    <form id="resourceListingFrm" action="system_resources_listing.php" method="GET" class="sform">
         <input type="hidden" name="is_ajaxed" id="listing_is_ajaxed" value="1"/>
        <input type="hidden" name="pages" id="per_pages" value="<?=checkIsset($per_page)?>"/>
    <div class="panel panel-default panel-block">
       <div class="panel-body">
            <div class="clearfix m-b-15 tbl_filter">
            <div class="pull-left">
                <h4 class="m-t-7">Resources</h4>
            </div>
             <div class="pull-right">
                <div class="note_search_wrap auto_size" id="search_div" style="display: none; max-width: 100%;">
                   <div class="phone-control-wrap theme-form">
                      <div class="phone-addon">
                         <div class="form-group height_auto mn">
                            <a href="javascript:void(0);" class="search_close_btn text-light-gray"
                               id="search_close_btn">X</a>
                         </div>
                      </div>
                      <div class="phone-addon w-200">
                         <div class="form-group height_auto mn ">
                            <input type="text" class="form-control" name="resource_name" value="<?=checkIsset($resource_name)?>">
                            <label>Resource Name</label>
                         </div>
                      </div>
                      <div class="phone-addon  w-200">
                         <div class="form-group height_auto mn">
                            <select class="form-control" name="resource_category">
                               <option data-hidden="true"></option>
                               <option value="API">API</option>
                               <option value="Payment Authorization">Payment Authorization</option>
                               <option value="ACH Information">ACH Information</option>
                               <option value="Credit Card Information">Credit Card Information</option>
                               <option value="Compliance">Compliance</option>
                            </select>
                            <label>Category</label>
                         </div>
                      </div>
                      <div class="phone-addon w-55">
                         <div class="form-group height_auto mn">
                            <a href="javascript:void(0);" class="btn btn-info btn-block" id="resourceSearch"><i
                               class="fa fa-search fa-lg"></i></a>
                         </div>
                      </div>
                   </div>
                </div>
                <a href="javascript:void(0);" class="search_btn" id="search_btn"><i
                   class="fa fa-search fa-lg text-blue"></i></a>
             </div>
            </div>
       
            <div id="resource_listing_ajax_data"></div>
       </div>
    </div>
    </form>
 </div>

  


<script type="text/javascript">
if(typeof($) !== 'undefined') {
    $(document).ready(function () {
    dropdown_pagination('resource_listing_ajax_data')

        resource_listing_ajax_submit();
    });
}

$(document).off('click', '#resource_listing_ajax_data ul.pagination li a');
$(document).on('click', '#resource_listing_ajax_data ul.pagination li a', function (e) {
    e.preventDefault();
    $('#ajax_loader').show();
    $('#resource_listing_ajax_data').hide();
    $.ajax({
        url: $(this).attr('href'),
        type: 'GET',
        success: function (res) {
            $('#ajax_loader').hide();
            $('#resource_listing_ajax_data').html(res).show();
            common_select();
        }
    });
});


$(document).off("click", "#search_btn");
$(document).on("click", "#search_btn", function (e) {
    e.preventDefault();
    $(this).hide();
    $("#search_div").css('display', 'inline-block');
});

$(document).off("click", "#search_close_btn");
$(document).on("click", "#search_close_btn", function (e) {
    e.preventDefault();
    $("#search_div").hide();
    $("#search_btn").show();
    window.location.reload();
});

$(document).off('click','#resourceSearch');
$(document).on('click','#resourceSearch',function(e){
    e.preventDefault();
    resource_listing_ajax_submit();
});



function resource_listing_ajax_submit() {
    $('#ajax_loader').show();
    $('#resource_listing_ajax_data').hide();
    $('#listing_is_ajaxed').val('1');
    var params = $('#resourceListingFrm').serialize();
    $.ajax({
        url: $('#resourceListingFrm').attr('action'),
        type: 'GET',
        data: params,
        success: function (res) {
            $('#ajax_loader').hide();
            $('#resource_listing_ajax_data').html(res).show();
            common_select();
        }
    });
    return false;
}
</script>
<?php } ?>