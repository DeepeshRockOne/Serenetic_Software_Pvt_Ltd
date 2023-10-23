<div class="module_instruction">
   <div class="container">
      <div class="clearfix tbl_filter m-b-15">
        <div class="pull-left">
            <h4 class="m-t-0 ">Instructions</h4>
        </div>
         <div class="pull-right">
            <a href="report_export.php" class="btn btn-action">Export Requests</a>
         </div>
      </div>
      <p class="mn">Select a report category below to view certain reports in that category.</p>
   </div>
</div>
<div class="panel panel-default panel-block report_panel">
   <div class="panel-body">
      <div class="container">
      <h4 class="m-t-0 m-b-20">Report Categories</h4>
      <div class="row">
         <div class="col-sm-3">
            <div class="report_tab_wrap">
               <div class="vtabs">
                  <ul class="nav tabs-vertical">
                        <li class="tab active">
                            <a class="btn_load_category_report" data-toggle="tab" href="#all_reports" aria-expanded="true" data-category_name="All Reports">All</a>
                        </li>
                        <?php 
                            if(!empty($category_res)) {
                                foreach ($category_res as $key => $category_row) {
                                ?>
                                <li class="tab">
                                    <a class="btn_load_category_report" data-toggle="tab" href="#rp_cat_<?=$category_row['id']?>" aria-expanded="true" data-category_name="<?=$category_row['title']?>"><?=$category_row['title']?></a>
                                </li>
                                <?php
                                }
                            }
                        ?>
                  </ul>
               </div>
            </div>
         </div>
         <div class="col-sm-9">
            <div class="tab-content">
                    <div class="white-box mn p-b-0">
                        <div class="clearfix tbl_filter">
                            <div class="pull-left">
                                <h4 class="m-t-7 active_category_name">All Reports</h4>
                            </div>
                            <div class="pull-right">
                               <div class="m-b-15">
                                  <div class="note_search_wrap auto_size" id="search_div" style="display: none; max-width: 100%;">
                                     <div class="phone-control-wrap theme-form">
                                        <div class="phone-addon">
                                           <div class="form-group height_auto mn">
                                              <a href="javascript:void(0);" id="search_close_btn" class="search_close_btn text-light-gray">X</a>
                                           </div>
                                        </div>
                                        <div class="phone-addon w-300">
                                           <div class="form-group height_auto mn">
                                              <input type="text" id="search_input" class="form-control">
                                              <label>Search Keywords</label>
                                           </div>
                                        </div>
                                        <div class="phone-addon w-80">
                                           <div class="form-group height_auto mn">
                                              <a href="javascript:void(0);" class="btn btn-info submit_search_btn">Search</a>
                                           </div>
                                        </div>
                                     </div>
                                  </div>
                                  <a href="javascript:void(0);" class="search_btn" id="search_btn" ><i class="fa fa-search fa-lg text-blue"></i></a>
                               </div>
                            </div>
                        </div>
                    </div>
                    <div id="all_reports" class="tab-pane active">
                        <div class="white-box mn p-t-0">
                        <div class="table-responsive">
                            <table class="<?=$table_class?> reports_table">
                               <thead>
                                  <tr>
                                     <th width="25%">Reports</th>
                                     <th class="text-center">Description</th>
                                     <th width="90px">Actions</th>
                                  </tr>
                               </thead>
                               <tbody>
                                  <?php
                                    $report_found = false;
                                    if(!empty($category_res)) {
                                        foreach ($category_res as $key => $category_row) {
                                            if(!empty($category_row['reports'])) {
                                                foreach ($category_row['reports'] as $report_row) {
                                                    $report_found = true;
                                                    ?>
                                                    <tr>
                                                        <td><a href="javascript:void(0);" class="fw500 text-action"><?=$report_row['report_name'];?></a></td>
                                                        <td class="text-center icons">
                                                            <a href="report_information.php?id=<?=md5($report_row['id']);?>" class="report_information"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
                                                         </td>
                                                         <td class="icons text-right">
                                                            <?php if($report_row['is_allow_schedule'] == "Y") { ?>
                                                            <a href="<?=$HOST?>/report_schedule.php?user_id=<?=md5($_SESSION['groups']['id'])?>&user_type=Group&id=<?=md5($report_row['id']);?>" data-toggle="tooltip" data-placement="top" title="Schedule Delivery" class="report_schedule"><i class="fa fa-calendar" aria-hidden="true"></i>
                                                            </a>
                                                            <?php } ?>

                                                            <a href="generate_report.php?id=<?=md5($report_row['id']);?>" data-toggle="tooltip" data-placement="top" title="Generate" class="generate_report"><i class="fa fa-download" aria-hidden="true"></i>
                                                            </a>
                                                         </td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                        }
                                    }
                                    if($report_found == false) {
                                        ?>
                                        <tr><td colspan="3">No report(s) found!</td></tr>
                                        <?php
                                    }
                                    ?>
                               </tbody>
                            </table>
                        </div>
                        </div>
                    </div>
                    <?php 
                    if(!empty($category_res)) {
                        foreach ($category_res as $key => $category_row) {
                        ?>
                        <div id="rp_cat_<?=$category_row['id']?>" class="tab-pane">
                            <div class="white-box mn p-t-0">
                            <div class="table-responsive">
                                <table class="<?=$table_class?> reports_table">
                                    <thead>
                                        <tr>
                                            <th width="25%">Reports</th>
                                            <th class="text-center">Description</th>
                                            <th width="90px">Actions</th>
                                        </tr>
                                    </thead>
                                   <tbody>
                                    <?php 
                                        if(!empty($category_row['reports'])) {
                                            foreach ($category_row['reports'] as $report_row) {
                                                ?>
                                                <tr>
                                                    <td><a href="javascript:void(0);" class="fw500 text-action"><?=$report_row['report_name'];?></a></td>
                                                    <td class="text-center icons">
                                                        <a href="report_information.php?id=<?=md5($report_row['id']);?>" class="report_information"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
                                                     </td>
                                                     <td class="icons text-right">
                                                        <?php if($report_row['is_allow_schedule'] == "Y") { ?>
                                                        <a href="<?=$HOST?>/report_schedule.php?user_id=<?=md5($_SESSION['groups']['id'])?>&user_type=Group&id=<?=md5($report_row['id']);?>" data-toggle="tooltip" data-placement="top" title="Schedule Delivery" class="report_schedule"><i class="fa fa-calendar" aria-hidden="true"></i>
                                                        </a>
                                                        <?php } ?>

                                                        <a href="generate_report.php?id=<?=md5($report_row['id']);?>" data-toggle="tooltip" data-placement="top" title="Generate" class="generate_report"><i class="fa fa-download" aria-hidden="true"></i>
                                                        </a>
                                                     </td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            ?>
                                            <tr><td colspan="3">No report(s) found!</td></tr>
                                            <?php
                                        }
                                    ?>
                                   </tbody>
                                </table>
                             </div>
                             </div>
                        </div>
                        <?php
                        }
                    }
                ?>
             </div>
         </div>
      </div>
      </div>
   </div>
</div>
<script type="text/javascript">
   $(document).ready(function () {
        $('a.btn_load_category_report[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var category_name = $(e.target).attr('data-category_name');
            $(".active_category_name").html(category_name);
        });

        $(document).off("click", "#search_btn");
        $(document).on("click", "#search_btn", function(e) {
            e.preventDefault();
            $(this).hide();
            $("#search_div").css('display', 'inline-block');
        });

        $(document).off("click", "#search_close_btn");
        $(document).on("click", "#search_close_btn", function(e) {
            e.preventDefault();
            $("#search_div").hide();
            $("#search_btn").show();

            $("#search_input").val('');
            var value = $("#search_input").val();
            $("table.reports_table tbody").each(function(index,element){
                $(this).find("tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });
        });

        $(document).off("click", ".submit_search_btn");
        $(document).on("click", ".submit_search_btn", function(e) {
            //alert('search');
            var value = $("#search_input").val().toLowerCase();
            $("table.reports_table tbody").each(function(index,element){
                $(this).find("tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });            
        });

        $(".report_information").colorbox({iframe: true, width: '900px', height: '600px'});
        $(".report_schedule").colorbox({iframe: true, width: '650px', height: '600px'});
        $(".generate_report").colorbox({iframe: true, width: '700px', height: '385px'});
   });
</script>