<div class="panel panel-default">
   <div class="panel-body advance_info_div">
      <div class="phone-control-wrap ">
         <div class="phone-addon w-90 v-align-top">
            <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="65px">
         </div>
         <div class="phone-addon text-left">
               <p class="fs14 mn">Email and SMS Broadcasts must be approved by an admin before the agent can send to their book of business. Under actions in Pending Approval you can view the broadcast and determine to edit, reject or approve.</p>
         </div>
      </div>
   </div>
</div>
<div class="panel panel-default">
   <div class="panel-body">
      <h4 class="m-t-0">Pending Approval</h4>
      <div class="clearfix m-b-15 tbl_filter">
         <div class="pull-left">
            <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
               <div class="form-group mn">
                  <label for="user_type">Records Per Page </label>
               </div>
               <div class="form-group mn">
                  <select size="1" id="pages" name="pages" class="form-control" >
                     <option value="10">10</option>
                     <option value="25">25</option>
                     <option value="50">50</option>
                     <option value="100">100</option>
                  </select>
               </div>
            </div>
         </div>
         <div class="pull-right">
            <div class="note_search_wrap auto_size" id="approval_search_div" style="display: none; max-width: 100%;">
               <div class="phone-control-wrap theme-form">
                  <div class="phone-addon">
                     <div class="form-group height_auto mn">
                        <a href="javascript:void(0);" class="search_close_btn text-light-gray" id="approval_search_close_btn">X</a>
                     </div>
                  </div>
                  <div class="phone-addon">
                     <div class="form-group height_auto mn">
                        <input type="text"  class="form-control" value="" >
                        <label>Keyword</label>
                     </div>
                  </div>
                  <div class="phone-addon w-80">
                     <div class="form-group height_auto mn">
                        <a href="javascript:void(0);" class="btn btn-info search_button">Search</a>
                     </div>
                  </div>
               </div>
            </div>
            <a href="javascript:void(0);" class="search_btn" id="approval_search_btn"><i class="fa fa-search fa-lg text-blue"></i></a>
         </div>
      </div>
      <div class="table-responsive">
         <table class="<?=$table_class?>">
            <thead>
               <tr>
                  <th>Submitted By</th>
                  <th>Broadcast Name</th>
                  <th>Sent</th>
                  <th>User Group</th>
                  <th>Status</th>
                  <th width="100px">Actions</th>
               </tr>
            </thead>
            <tbody>
               <tr>
                  <td>Group Name<br><a href="javascript:void(0);"  class="fw500 text-action">G12233</a></td>
                  <td>Email Broadcast 001</td>
                  <td>09/01/2019</td>
                  <td>Members</td>
                  <td>Waiting for Approval
                     <a class="m-l-5 text-info" href="javascript:void(0);"><span class="material-icons fs16"> send </span></a>
                  </td>
                  <td>
                     <a href="add_email_broadcast.php" data-toggle="tooltip" title="View" class="btn btn-action">View</a>
                  </td>
               </tr>
            </tbody>
            <tfoot>
               <tr>
                  <td colspan="6">
                     <div class="row table-footer-row">
                        <div class="col-sm-12">
                           <div class="pull-left">
                              <div class="dataTables_info">1 to 2 of 2 Records</div>
                           </div>
                           <div class="pull-right">
                              <div class="dataTables_paginate paging_bs_normal">
                                 <ul class="pagination pagination-md">
                                    <li class="prev disabled"><span>&lt;</span></li>
                                    <li class="live-link active"><a href="javascript:void(0);">1</a></li>
                                    <li class="disabled"><span>&gt;</span></li>
                                 </ul>
                              </div>
                           </div>
                        </div>
                     </div>
                  </td>
               </tr>
            </tfoot>
         </table>
      </div>
   </div>
</div>

<div class="panel panel-default">
   <div class="panel-body">
      <h4 class="m-t-0">Approved</h4>
      <div class="clearfix m-b-15 tbl_filter">
         <div class="pull-left">
            <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
               <div class="form-group mn">
                  <label for="user_type">Records Per Page </label>
               </div>
               <div class="form-group mn">
                  <select size="1" id="pages" name="pages" class="form-control" >
                     <option value="10">10</option>
                     <option value="25">25</option>
                     <option value="50">50</option>
                     <option value="100">100</option>
                  </select>
               </div>
            </div>
         </div>
         <div class="pull-right">
            <div class="note_search_wrap auto_size" id="approval_search_div" style="display: none; max-width: 100%;">
               <div class="phone-control-wrap theme-form">
                  <div class="phone-addon">
                     <div class="form-group height_auto mn">
                        <a href="javascript:void(0);" class="search_close_btn text-light-gray">X</a>
                     </div>
                  </div>
                  <div class="phone-addon">
                     <div class="form-group height_auto mn">
                        <input type="text"  class="form-control" value="" >
                        <label>Keyword</label>
                     </div>
                  </div>
                  <div class="phone-addon w-80">
                     <div class="form-group height_auto mn">
                        <a href="javascript:void(0);" class="btn btn-info search_button">Search</a>
                     </div>
                  </div>
               </div>
            </div>
            <a href="javascript:void(0);" class="search_btn"><i class="fa fa-search fa-lg text-blue"></i></a>
         </div>
      </div>
      <div class="table-responsive">
         <table class="<?=$table_class?>">
            <thead>
               <tr>
                  <th>Admin Approved</th>
                  <th>Broadcast Name</th>
                  <th>Approved</th>
                  <th>User Group</th>
                  <th>Status</th>
                  <th width="100px">Actions</th>
               </tr>
            </thead>
            <tbody>
               <tr>
                  <td>Rosetta Franklin<br><a href="javascript:void(0);"  class="fw500 text-action">AD1223</a></td>
                  <td>Broadcast Name</td>
                  <td>09/01/2019</td>
                  <td>Members</td>
                  <td>Approved</td>
                  <td class="icons">
                     <a href="add_email_broadcast.php" data-toggle="tooltip" title="View"><i class="fa fa-eye"></i></a>
                  </td>
               </tr>
            </tbody>
            <tfoot>
               <tr>
                  <td colspan="6">
                     <div class="row table-footer-row">
                        <div class="col-sm-12">
                           <div class="pull-left">
                              <div class="dataTables_info">1 to 2 of 2 Records</div>
                           </div>
                           <div class="pull-right">
                              <div class="dataTables_paginate paging_bs_normal">
                                 <ul class="pagination pagination-md">
                                    <li class="prev disabled"><span>&lt;</span></li>
                                    <li class="live-link active"><a href="javascript:void(0);">1</a></li>
                                    <li class="disabled"><span>&gt;</span></li>
                                 </ul>
                              </div>
                           </div>
                        </div>
                     </div>
                  </td>
               </tr>
            </tfoot>
         </table>
      </div>
   </div>
</div>

<div class="panel panel-default">
   <div class="panel-body">
      <h4 class="m-t-0">Rejected</h4>
      <div class="clearfix m-b-15 tbl_filter">
         <div class="pull-left">
            <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
               <div class="form-group mn">
                  <label for="user_type">Records Per Page </label>
               </div>
               <div class="form-group mn">
                  <select size="1" id="pages" name="pages" class="form-control" >
                     <option value="10">10</option>
                     <option value="25">25</option>
                     <option value="50">50</option>
                     <option value="100">100</option>
                  </select>
               </div>
            </div>
         </div>
         <div class="pull-right">
            <div class="note_search_wrap auto_size" id="approval_search_div" style="display: none; max-width: 100%;">
               <div class="phone-control-wrap theme-form">
                  <div class="phone-addon">
                     <div class="form-group height_auto mn">
                        <a href="javascript:void(0);" class="search_close_btn text-light-gray">X</a>
                     </div>
                  </div>
                  <div class="phone-addon">
                     <div class="form-group height_auto mn">
                        <input type="text"  class="form-control" value="" >
                        <label>Keyword</label>
                     </div>
                  </div>
                  <div class="phone-addon w-80">
                     <div class="form-group height_auto mn">
                        <a href="javascript:void(0);" class="btn btn-info search_button">Search</a>
                     </div>
                  </div>
               </div>
            </div>
            <a href="javascript:void(0);" class="search_btn"><i class="fa fa-search fa-lg text-blue"></i></a>
         </div>
      </div>
      <div class="table-responsive">
         <table class="<?=$table_class?> table-action">
            <thead>
               <tr>
                  <th>Admin Rejected</th>
                  <th>Broadcast Name</th>
                  <th>Rejected</th>
                  <th class="text-center">Reason</th>
                  <th>User Group</th>
                  <th>Status</th>
                  <th width="100px">Actions</th>
               </tr>
            </thead>
            <tbody>
               <tr>
                  <td>Rosetta Franklin<br><a href="javascript:void(0);"  class="fw500 text-action">AD1223</a></td>
                  <td>Broadcast Name</td>
                  <td>09/01/2019</td>
                  <td class="icons text-center">
                     <a href="rejected_approval_reason.php" data-toggle="tooltip" title="Reason" class="rejected_approval_reason"><i class="fa fa-eye"></i></a>
                  </td>
                  <td>Agents</td>
                  <td>Members</td>
                  <td class="icons">
                     <a href="add_email_broadcast.php" data-toggle="tooltip" title="View"><i class="fa fa-eye"></i></a>
                  </td>
               </tr>
            </tbody>
            <tfoot>
               <tr>
                  <td colspan="7">
                     <div class="row table-footer-row">
                        <div class="col-sm-12">
                           <div class="pull-left">
                              <div class="dataTables_info">1 to 2 of 2 Records</div>
                           </div>
                           <div class="pull-right">
                              <div class="dataTables_paginate paging_bs_normal">
                                 <ul class="pagination pagination-md">
                                    <li class="prev disabled"><span>&lt;</span></li>
                                    <li class="live-link active"><a href="javascript:void(0);">1</a></li>
                                    <li class="disabled"><span>&gt;</span></li>
                                 </ul>
                              </div>
                           </div>
                        </div>
                     </div>
                  </td>
               </tr>
            </tfoot>
         </table>
      </div>
   </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
$(document).on("click", "#approval_search_btn", function(e) {
e.preventDefault();
    $(this).hide();
    $("#approval_search_div").css('display', 'inline-block');
});
$(document).on("click", "#approval_search_close_btn", function(e) {
    e.preventDefault();
    $("#approval_search_div").hide();
    $("#approval_search_btn").show();
});
 $(".rejected_approval_reason").colorbox({iframe: true, width: '685px', height: '425px'})
});
</script>