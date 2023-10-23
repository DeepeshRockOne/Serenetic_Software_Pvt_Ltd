<?php if ($is_ajaxed) { ?>
   <div class="clearfix m-b-15">
    <?php if ($total_rows > 0) { ?>
        <div class="pull-left">
          <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
            <div class="form-group mn">
              <label for="user_type">Records Per Page </label>
            </div>
            <div class="form-group mn">
              <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);ajax_submit();">
                <option value="10" <?php echo isset($_GET['pages']) && $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
                <option value="25" <?php echo (isset($_GET['pages']) && $_GET['pages'] == 25) || (isset($_GET['pages']) && $_GET['pages'] == "") ? 'selected' : ''; ?>>25</option>
                <option value="50" <?php echo isset($_GET['pages']) && $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
                <option value="100" <?php echo isset($_GET['pages']) && $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
              </select>
            </div>
          </div>
        </div>
      <?php } ?>
   </div>
   <div class="table-responsive">
      <table class="<?=$table_class?>">
         <thead>
            <tr>
               <th>Type/Added Date</th>
               <th>Details</th>
               <th>Enrolling Agent ID/Name</th>
               <th>Admin Name/ID</th>
               <th width="25%;">Interaction Notes</th>
            </tr>
         </thead>
         <tbody>
         <?php if ($total_rows > 0) { ?>
          <?php foreach ($fetch_rows as $rows) { ?>
            <tr>
               <td><a href="javascript:void(0);" ><strong class="text-action"><?=$rows['intType']?></strong></a><br><?=displayDate($rows['created_at'])?></td>
               <td>
                  <a href="members_details.php?id=<?=md5($rows['mid'])?>" target="_blank" class="fw500 text-action"><?=$rows['memberId']?></a><br>
                  <strong><?=$rows['memberName']?></strong><br>
                  <?=format_telephone($rows['cell_phone'])?><br>
                  <?=$rows['email']?>
               </td>
               <td><a href="javascript:void(0);" class="fw500 text-action"><?=$rows['agentId']?></a><br><?=$rows['agentName']?></td>
               <td><a href="javascript:void(0);" class="fw500 text-action"><?=$rows['adminId']?></a><br><?=$rows['adminName']?></td>
               <td class="icons" style="white-space: normal;">
                  <a href="javascript:void(0);"  class="member_interaction_content text-red" data-toggle="tooltip" data-placement="top" data-id="<?=md5($rows['intId'])?>" title="" data-title="View"><?=custom_charecter($rows['description'],140)?></a>
               </td>
            </tr>
         <?php }} else { ?>
          <tr>
            <td colspan="5" align="center">No record(s) found</td>
          </tr>
        <?php } ?>
         </tbody>
         <?php if ($total_rows > 0) { ?>
         <tfoot>
            <tr>
               <td colspan="5">
               <?php echo $paginate->links_html; ?>
               </td>
            </tr>
         </tfoot>
         <?php } ?>
      </table>
   </div>
<?php }else{ ?>
<div class="container m-t-30">
   <div class="panel panel-default panel-block panel-title-block">
      <form  class="theme-form" name="frm_search" id="frm_search" autocomplete="off">
         <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="">
         <input type="hidden" name="pages" id="per_pages" value="<?= $per_page; ?>" />
         <input type="hidden" name="sort_by" id="sort_by_column" value="<?= $SortBy; ?>" />
         <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?= $SortDirection; ?>" />
         <div class="panel-left">
            <div class="panel-left-nav">
               <ul>
                  <li class="active"><a href="javascript:void(0);"><i class="fa fa-search"></i></a></li>
               </ul>
            </div>
         </div>
         <div class="panel-right">
            <div class="panel-heading">
               <div class="panel-search-title">
                  <span class="clr-light-blk">SEARCH</span>
               </div>
            </div>
            <div class="panel-wrapper collapse in">
               <div class="panel-body theme-form">
                  <div class="row">
                     <div class="col-md-6 col-sm-12">
                        <div class="row">
                           <div id="date_range" class="col-md-12 col-sm-12">
                           <div class="form-group">
                              <select class="form-control listing_search" id="join_range" name="join_range">
                                 <option value=""> </option>
                                 <option value="Range">Range</option>
                                 <option value="Exactly">Exactly</option>
                                 <option value="Before">Before</option>
                                 <option value="After">After</option>
                              </select>
                              <label>Added Date</label>
                           </div>
                           </div>
                           <div class="select_date_div col-md-9 col-sm-12" style="display:none">
                           <div class="form-group">
                              <div id="all_join" class="input-group">
                                 <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                 <input type="text" name="added_date" id="added_date" value="" class="form-control date_picker listing_search" />
                              </div>
                              <div id="range_join" style="display:none;">
                                 <div class="phone-control-wrap">
                                 <div class="phone-addon">
                                    <label class="mn">From</label>
                                 </div>
                                 <div class="phone-addon">
                                    <div class="input-group">
                                       <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                       <input type="text" name="fromdate" id="fromdate" value="" class="form-control date_picker listing_search" />
                                    </div>
                                 </div>
                                 <div class="phone-addon">
                                    <label class="mn">To</label>
                                 </div>
                                 <div class="phone-addon">
                                    <div class="input-group">
                                       <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                       <input type="text" name="todate" id="todate" value="" class="form-control date_picker listing_search" />
                                    </div>
                                 </div>
                                 </div>
                              </div>
                           </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6 col-sm-12">
                        <div class="form-group ">
                           <select class="se_multiple_select listing_search" id="enroll_agent" name="enroll_agent[]" multiple="multiple">
                              <?php if(!empty($enroll_agent_res)){ 
                                 foreach($enroll_agent_res as $ageRes){
                                 ?>
                                 <option value="<?=$ageRes['id']?>"><?=$ageRes['rep_id']?> - <?=$ageRes['name']?></option>
                              <?php }} ?>
                           </select>
                           <label>Enrolling Agent</label>
                        </div>
                     </div>
                     <div class="col-sm-6">
                        <div class="form-group ">
                           <select class="se_multiple_select listing_search" id="agency_name" name="agency_name[]"  multiple="multiple">
                              <?php if(!empty($agency_res)){ 
                                 foreach($agency_res as $acRes){
                                 ?>
                                 <option value="<?=$acRes['repIds']?>"><?=$acRes['agencyNameDis']?></option>
                              <?php }} ?>
                           </select>
                           <label>Agency</label>
                        </div>
                     </div>
                     <div class="col-sm-6">
                        <div class="form-group ">
                           <select class="se_multiple_select listing_search" id="member_id" name="member_id[]"  multiple="multiple">
                              <?php if(!empty($memberRes)){ 
                                 foreach($memberRes as $memRes){
                                 ?>
                                 <option value="<?=$memRes['id']?>"><?=$memRes['rep_id']?> - <?=$memRes['name']?></option>
                              <?php }} ?>
                           </select>
                           <label>Member ID</label>
                        </div>
                     </div>
                     <div class="col-sm-6">
                        <div class="form-group ">
                           <select class="se_multiple_select listing_search" id="interaction_type" name="interaction_type[]"  multiple="multiple">
                              <?php if(!empty($interactionrRes)){ 
                                 foreach($interactionrRes as $acRes){
                                 ?>
                                 <option value="<?=$acRes['id']?>"><?=$acRes['type']?></option>
                              <?php }} ?>
                           </select>
                           <label>Type</label>
                        </div>
                     </div>
                  </div>
                  <div class="panel-footer clearfix">
                     <button type="submit" id="btn_submit" class="btn btn-info" ><i class="fa fa-search"></i> Search
                     </button>
                     <button type="button" class="btn btn-info btn-outline" name="viewall" id="viewall" onclick="window.location.href='member_interactions.php'" ><i class="fa fa-search-plus"></i> View All
              </button>
                  </div>
               </div>
            </div>
         </div>
         <div class="search-handle">
            <a href="#" data-perform="panel-collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></a>
         </div>
      </form>
   </div>
   <div class="panel panel-default">
      <div id="ajax_loader" class="ajex_loader" style="display: none;">
      <div class="loader"></div>
      </div>
      <div id="ajax_data" class="panel-body"></div>
   </div>
</div>
<script type="text/javascript">
   $(document).off('change', '#join_range');
   $(document).on('change', '#join_range', function(e) {
     e.preventDefault();
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
   
   $(document).ready(function() {
    dropdown_pagination('ajax_data')
      $("#enroll_agent, #agency_name, #member_id, #interaction_type").multipleSelect({
         selectAll: false,
         filter: true
      });
      ajax_submit();
      $(".date_picker").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true
      });
   });
   $(document).off('click', '.member_interaction_content');
   $(document).on('click', '.member_interaction_content', function (e) {
      e.preventDefault();
      var id=$(this).data('id');
      var $href="<?=$HOST?>/member_interaction_content.php?id="+id;
      $.colorbox({
         href: $href,
         iframe: true, 
         width: '768px', 
         height: '500px'
      });
   });

   $(document).off("submit", "#frm_search");
   $(document).on("submit", "#frm_search", function(e) {
   e.preventDefault();
   $('#viewMember').val("allMember");
   disable_search();
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
      }
   });
   });

   function ajax_submit() {
      disableButton($("#btn_submit"));
      $('#ajax_loader').show();
      $('#ajax_data').hide();
      $('#is_ajaxed').val('1');
      var params = $('#frm_search').serialize();
      $.ajax({
        url: $('#frm_search').attr('action'),
        type: 'GET',
        data: params,
        success: function(res) {
          $('#ajax_loader').hide();
          $('#ajax_data').html(res).show();
          common_select();
          enableButton($("#btn_submit"));
        }
      });
      return false;
   }

</script>
<?php } ?>