<?php if ($is_ajaxed) { ?>
    <input type="hidden" name="curr_ajax_url" id="curr_ajax_url" value="<?=$curr_page_url;?>">
     <div class="clearfix text-right m-t-7">
                <div class="m-b-15">
                    <a href="javascript:void(0);" data-href="add_report.php" class="add_report btn btn-info m-l-10">+ Report</a>
                </div>
            </div>
    <div class="table-responsive">
        <table class="<?= $table_class ?>">
            <thead>
            <tr class="data-head">
                <th>Name</th>
                <th>Category</th>
                <th width="15%">Portal</th>
                <th width="90px" class="text-center">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($total_rows > 0) { ?>
                <?php foreach ($fetch_rows as $key => $rows) { ?>
                    <tr>
                        <td>
                            <?php echo $rows['report_name']; ?>
                        </td>
                        <td>
                            <?php echo $rows['category_name']; ?>
                        </td>
                        <td>
                            <?php echo $rows['portal']; ?>
                        </td>
                        <td class="icons">
                            <a href="javascript:void(0);" data-href="add_report.php?id=<?=md5($rows['id']);?>" class="add_report" data-toggle="tooltip" title="Edit"><i class="fa fa-edit"></i></a>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="4" align="center">No record(s) found</td>
                </tr>
            <?php } ?>
            </tbody>
            <?php if ($total_rows > 0) { ?>
                <tfoot>
                <tr>
                    <td colspan="4">
                        <?php echo $paginate->links_html; ?>
                    </td>
                </tr>
                </tfoot>
            <?php } ?>
        </table>
    </div>
<?php } else { ?>
    <div class="panel panel-default panel-block panel-title-block">
        <form id="frm_search" action="report_listing.php" method="GET" class="theme-form">
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
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <input type="text" name="report_name" value="<?php echo $report_name ?>"
                                           class="form-control listing_search">
                                    <label>Report Name</label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <select class="se_multiple_select listing_search" name="category_id[]" id="category_id" multiple="multiple">
                                        <?php
                                        if (!empty($category_res)) {
                                            foreach ($category_res as $key => $category_row) {
                                                ?>
                                                <option value="<?= $category_row['id'] ?>"><?= $category_row['title']?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <label>Report Category</label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <select class="se_multiple_select listing_search" name="portal[]" id="portal" multiple="multiple">
                                        <option value="Admin">Admin</option>
                                        <option value="Agent">Agent</option>
                                        <option value="Group">Group</option>
                                    </select>
                                    <label>Portal</label>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer clearfix">
                            <div class="clearfix">
                                <div class="pull-left">
                                    <button type="submit" class="btn btn-info">
                                    <i class="fa fa-search"></i> Search
                                    </button>
                                    <button type="button" class="btn btn-info btn-outline"
                                       onClick="window.location = 'report_listing.php'">
                                    <i class="fa fa-search-plus"></i> View All
                                    </button>
                                   <input type="hidden" name="export" id="export" value=""/>
                                    <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1"/>
                                    <input type="hidden" name="pages" id="per_pages" value="<?= $per_page; ?>"/>
                                    <input type="hidden" name="sort_by" id="sort_by_column" value="<?= $SortBy; ?>"/>
                                    <input type="hidden" name="sort_direction" id="sort_by_direction"
                                       value="<?= $SortDirection; ?>"/>
                                </div>
                                <div class="pull-right">
                                    <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
                                       <div class="form-group mn ">
                                          <label for="">Records Per Page </label>
                                       </div>
                                       <div class="form-group mn ">
                                          <select size="1" id="pages" name="pages"
                                             class="form-control select2 placeholder"
                                             onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);ajax_submit();">
                                             <option value="10" <?php echo isset($_GET['pages']) && $_GET['pages'] == 10 ? 'selected' : ''; ?>>
                                                10
                                             </option>
                                             <option value="25" <?php echo (isset($_GET['pages']) && $_GET['pages'] == 25) || (isset($_GET['pages']) && $_GET['pages'] == "") ? 'selected' : ''; ?>>
                                                25
                                             </option>
                                             <option value="50" <?php echo isset($_GET['pages']) && $_GET['pages'] == 50 ? 'selected' : ''; ?>>
                                                50
                                             </option>
                                             <option value="100" <?php echo isset($_GET['pages']) && $_GET['pages'] == 100 ? 'selected' : ''; ?>>
                                                100
                                             </option>
                                          </select>
                                       </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="search-handle">
                <a href="#" data-perform="panel-collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></a>
            </div>
        </form>
    </div>
    <div class="panel panel-default panel-block">
        <div class="panel-body">
            <div id="ajax_loader" class="ajex_loader" style="display: none;">
                <div class="loader"></div>
            </div>
            <div id="ajax_data"></div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function () {
    dropdown_pagination('ajax_data')
            $(document).off('click', '.add_report');
            $(document).on('click', '.add_report', function (e) {
                var href = $(this).attr('data-href');
                $.colorbox({href:href,iframe: true, width: '900px', height: '600px'});
            });
            ajax_submit();
            $("#category_id").multipleSelect({
                selectAll: false
            });
            $("#portal").multipleSelect({
                selectAll: false,
                filter: false
            });

            $(document).off('click', '#ajax_data tr.data-head a');
            $(document).on('click', '#ajax_data tr.data-head a', function (e) {
                e.preventDefault();
                $('#sort_by_column').val($(this).attr('data-column'));
                $('#sort_by_direction').val($(this).attr('data-direction'));
                ajax_submit();
            });

            $(document).off('click', '#ajax_data ul.pagination li a');
            $(document).on('click', '#ajax_data ul.pagination li a', function (e) {
                e.preventDefault();
                $('#ajax_loader').show();
                $('#ajax_data').hide();
                $.ajax({
                    url: $(this).attr('href'),
                    type: 'GET',
                    success: function (res) {
                        $('#ajax_loader').hide();
                        $('#ajax_data').html(res).show();
                        $('[data-toggle="tooltip"]').tooltip();
                        common_select();
                    }
                });
            });

            $(document).off("submit", "#frm_search");
            $(document).on("submit", "#frm_search", function (e) {
                e.preventDefault();
                disable_search();
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
                success: function (res) {
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