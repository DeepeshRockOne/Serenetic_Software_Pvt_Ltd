<?php if($is_ajaxed){ ?>
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
          <?php if ($total_rows > 0) {
            foreach ($fetch_rows as $rows) { ?>
              <tr>
                <td><?=date('m/d/Y',strtotime($rows['created_at']))?></td>
                <td><?=$rows['file_name']?></td>
                <td><a href="javascript:void(0);" class="fw500 text-action"><?=$rows['display_id']?></a><br><?=$rows['admin_name']?></td>
                <td class="text-center"><?=$rows['records']?></td>
                <td class="icons text-center">
                  <?php 
                    if(file_exists($ELIGIBILITY_FILES_DIR . $rows['processed_file'])) {
                      ?>
                      <a href="<?=$ELIGIBILITY_FILES_WEB . $rows['processed_file']?>" data-toggle="tooltip" data-trigger="hover" title="Download" download><i class="fa fa-download"></i></a>
                      <?php
                    } else {
                      ?>
                      <a href="eligibility_export_requests.php?is_download=Y&file_name=<?=urlencode($rows['processed_file']);?>&location=eligibility_processed_file&file_id=<?=$file_id?>" data-toggle="tooltip" data-trigger="hover" title="Download" ><i class="fa fa-download"></i></a>
                      <?php
                    }
                  ?>
                </td>
               </tr> 
            <?php } ?>
          <?php }else{ ?>
            <tr>
              <td colspan="5">No Records.</td>
            </tr>
          <?php } ?>
        </tbody>
        <?php if ($total_rows > 0) { ?>
            <tfoot>
            <tr>
                    <td colspan="9">
                        <?php echo $paginate->links_html; ?>
                    </td>
                </tr>
            </tfoot>
        <?php } ?>
      </table>
    </div>
<?php }else{ ?>
  <?php include_once 'notify.inc.php';?>
    <form id="frm_search" action="eligibility_processed_file.php" method="GET" class="sform">
       <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
       <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
       <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
       <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>">  
       <input type="hidden" name="id" id="id" value="<?=$file_id?>">  
       <div class="panel panel-default panel-block">
       <div class="panel-body">
       <div class="clearfix tbl_filter">
          <h4 class="pull-left m-t-7 m-b-0"><?=$file_name?> - <span class="fw300">Eligibility Files</span></h4>
          <div class="pull-right">
             <div class="m-b-15">
                <a href="javascript:void(0);" class="search_btn" ><i class="fa fa-search fa-lg text-blue"></i></a>
             </div>
          </div>
      
       <div class="m-b-0">
          <div class="note_search_wrap" id="search_div" style="display: none; max-width: 100%;">
             <div class="row theme-form">
                <div class="col-md-3 col-lg-4 text-right">
                   <div class="form-group height_auto m-b-15">
                      <a href="javascript:void(0);" class="search_close_btn text-light-gray">X</a>
                   </div>
                </div>
                <div class="col-md-7 col-lg-7">
                   <div class="row">
                      <div id="date_range" class="col-md-12">
                         <div class="form-group height_auto m-b-15">
                            <select class="form-control" id="join_range" name="join_range">
                               <option></option>
                               <option value="Range">Range</option>
                               <option value="Exactly">Exactly</option>
                               <option value="Before">Before</option>
                               <option value="After">After</option>
                            </select>
                            <label>Added Date</label>
                         </div>
                      </div>
                      <div class="select_date_div col-md-9" style="display:none">
                         <div class="form-group height_auto m-b-15">
                            <div id="all_join" class="input-group"> 
                               <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                               <input type="text" name="added_date" id="added_date" value="" class="form-control date_picker" />
                            </div>
                            <div  id="range_join" style="display:none;">
                               <div class="phone-control-wrap">
                                  <div class="phone-addon">
                                     <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <div class="pr">
                                           <input type="text" name="fromdate" id="fromdate" value="" class="form-control date_picker" />
                                           <label>From</label>
                                        </div>
                                     </div>
                                  </div>
                                  <div class="phone-addon">
                                     <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <div class="pr">
                                           <input type="text" name="todate" id="todate" value="" class="form-control date_picker" />
                                           <label>To</label>
                                        </div>
                                     </div>
                                  </div>
                               </div>
                            </div>
                         </div>
                      </div>
                   </div>
                </div>
                <div class="col-md-2 col-lg-1">
                   <div class="form-group height_auto m-b-15">
                      <a href="javascript:void(0);" class="btn btn-info search_button btn-block">Search</a>
                   </div>
                </div>
                 </div>
             </div>
          </div>
       </div>
    </form>
    <div id="ajax_loader" class="ajex_loader" style="display: none;">
      <div class="loader"></div>
    </div>
    <div id="ajax_data" class=""> </div>
    </div>
  </div>
<script type="text/javascript">
$(document).ready(function(){
  dropdown_pagination('ajax_data')
  ajax_submit();
  $("#products").multipleSelect({
  });
  $(".date_picker").datepicker({
    changeDay: true,
    changeMonth: true,
    changeYear: true
  });
  $(document).on("click", ".search_btn", function(e) {
    e.preventDefault();
    $(this).hide();
    $("#search_div").css('display', 'inline-block');
  });

  $(document).on("click", ".search_button", function(e) {
    ajax_submit();
  });

  $(document).on("click", ".search_close_btn", function(e) {
    e.preventDefault();
    $("#search_div").hide();
    $(".search_btn").show();
  });

$(document).off('click', '#ajax_data ul.pagination li a');
    $(document).on('click', '#ajax_data ul.pagination li a', function(e) {
      e.preventDefault();
      $('#ajax_loader').show();
      $('#ajax_data').hide();
      $.ajax({
        url: $(this).attr('href'),
        type: 'GET',
        success: function(res) {
          $('#ajax_loader').hide();
          $('#ajax_data').html(res).show();
          common_select();
          $('[data-toggle="tooltip"]').tooltip();
        }
      });
    });
    
  $(document).off('change', '#join_range');
    $(document).on('change', '#join_range', function(e) {
        e.preventDefault();
        $('.date_picker').val('');
        if ($(this).val() == '') {
            $('.select_date_div').hide();
            $('#date_range').removeClass('col-md-3').addClass('col-md-12');
        } else {
            $('#date_range').removeClass('col-md-12').addClass('col-md-3');
            $('.select_date_div').show();
            if ($(this).val() == 'Range') {
                $('#range_join').show();
                $('#all_join').hide();
            } else {
                $('#range_join').hide();
                $('#all_join').show();
            }
        }
    });
});
function ajax_submit() {
    $('#ajax_loader').show();
    $('#ajax_data').hide();
    $('#is_ajaxed').val('1');
    var params = $('#frm_search').serialize();
    $.ajax({
      url: $('#frm_search').attr('action'),
      type: 'GET',
      data: params,
      success: function(res) {
        $('#is_ajaxed').val('');
        $('#ajax_loader').hide();
        $('#ajax_data').html(res).show();
        common_select();
        $('[data-toggle="tooltip"]').tooltip();
      }
    });
    return false;
  }
</script>

<?php } ?>