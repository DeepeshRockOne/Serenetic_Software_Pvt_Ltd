<div class="module_instruction m-b-30">
   <div class="container">
      <div class="clearfix">
         <h4 class="m-t-0 m-b-15 pull-left">Instructions</h4>
         <div class="pull-right">
            <a href="report_export.php" class="btn btn-action">Export Requests</a>
         </div>
      </div>
      <p class="mn">Select a report category below to view certain reports in that category.</p>
   </div>
</div>
<div class=" p-b-30">
   <div class="container">
      <h4 class="m-t-0 m-b-30">Report Categories</h4>
   </div>
</div>
<div class="container">
   <div class="reporting_tab_wrap m-b-30 ">
      <div class="blue_arrow_tab">
         <ul class="nav nav-tabs nav-noscroll nav-justified">
            <?php
               if(!empty($category_res)) {
                  foreach ($category_res as $ckey => $category_row) {
                     ?>
                     <li class="<?=$ckey == 0?"active":""?>">
                        <a data-toggle="tab" href="#rp_cat_<?=$category_row['id']?>" data-category_name="<?=$category_row['title']?>"><?=$category_row['title']?></a>
                     </li>
                     <?php            
                  }
               }
            ?>
         </ul>
      </div>
   </div>
   <div class="tab-content mn">
      <?php 
         if(!empty($category_res)) {
            foreach ($category_res as $key => $category_row) {
               ?>
               <div id="rp_cat_<?=$category_row['id']?>" class="tab-pane fade <?=$key == 0?"in active":""?>">
                  <div id="reporting_table">
                     <div class="panel panel-default panel-block">
                        <div class="panel-body">
                           <h4 class="m-t-0 "><?=$category_row['title']?></h4>
                           <div class="table-responsive">
                              <table class="<?=$table_class?>">
                                 <thead>
                                    <tr>
                                       <th width="20%">Reports</th>
                                       <th class="text-center" >Description</th>
                                       <th width="100px">Action</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php
                                       if(!empty($category_row['reports'])) {
                                          foreach ($category_row['reports'] as $report_row) {
                                             ?>
                                             <tr>
                                                <td>
                                                   <a href="javascript:void(0);" class="fw500 text-action"><?=$report_row['report_name'];?>
                                                </td>
                                                <td class="text-center icons">
                                                   <a href="report_information.php?id=<?=md5($report_row['id']);?>" class="report_information"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
                                                </td>
                                                <td class="icons text-right">
                                                   <?php if($report_row['report_key'] == "agent_quick_sales_summary") { ?>
                                                   <a href="<?=$HOST?>/quick_report_sales_summary.php?user_id=<?=md5($_SESSION['agents']['id'])?>&user_type=Agent" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="View Quick Sales Summary" class="view_quick_report_sales_summary"><i class="fa fa-eye" aria-hidden="true"></i>
                                                   </a>
                                                   <?php } ?>

                                                   <?php if($report_row['is_allow_schedule'] == "Y") { ?>
                                                   <a href="<?=$HOST?>/report_schedule.php?user_id=<?=md5($_SESSION['agents']['id'])?>&user_type=Agent&id=<?=md5($report_row['id']);?>" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Schedule Delivery" class="report_schedule"><i class="fa fa-calendar" aria-hidden="true"></i>
                                                   </a>
                                                   <?php } ?>

                                                   <a href="generate_report.php?id=<?=md5($report_row['id']);?>" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Generate" class="generate_report"><i class="fa fa-download" aria-hidden="true"></i>
                                                   </a>
                                                </td>
                                             </tr>
                                             <?php
                                          }
                                       } else {
                                          ?>
                                         <tr><td colspan="3" class="text-center">No report(s) found!</td></tr>
                                         <?php
                                       }
                                    ?>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <?php
            }
         }
      ?>
   </div>
</div>
<script type="text/javascript">
   $(document).ready(function () {
        $(".view_quick_report_sales_summary").colorbox({iframe: true, width: '900px', height: '600px'});
        $(".report_information").colorbox({iframe: true, width: '900px', height: '600px'});
        $(".report_schedule").colorbox({iframe: true, width: '650px', height: '600px'});
        $(".generate_report").colorbox({iframe: true, width: '700px', height: '385px'});
   });
</script>