<?php if ($is_ajaxed) { ?> 
<div class="clearfix tbl_filter m-b-15">
    <?php if ($total_rows > 0) {?>
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
        <?php }?>
</div>
<div class="table-responsive">
    <table class="<?=$table_class?> ">
        <thead>
            <tr class="data-head">
                <th>Agent Id</th>
                <th>Agent Name</th>
                <th>Email</th>
                <th>Phone No</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($total_rows > 0) { ?>
                <?php foreach ($fetch_rows as $rows) { ?>
                    <tr>
                       <td><a href="payment_commissions.php?agent_id=<?=md5($rows['agentId'])?>"  class="text-red">
                                <strong class="fw600"><?php echo $rows['agentDispId']; ?></strong></a></td>
                       <td><?=$rows['agentName']?></td>
                       <td><?=$rows['agentemail']?></td>
                       <td><?=$rows['agentephone']?></td>
                    </tr>
                <?php }?>
            <?php } else {?>
                <tr>
                    <?php if($module_access_type == "rw") { ?>
                    <td colspan="10" align="center">No record(s) found</td>
                    <?php } else {?>
                    <td colspan="8" align="center">No record(s) found</td>
                    <?php }?>
                </tr>
            <?php }?>
        </tbody>
        <?php if ($total_rows > 0) { ?>
            <tfoot>
            <tr>
                <?php if($module_access_type == "rw") { ?>
                <td colspan="10"><?php echo $paginate->links_html; ?></td>
                <?php } else {?>
                <td colspan="8"><?php echo $paginate->links_html; ?></td>
                <?php }?>
            </tr>
            </tfoot>
        <?php } ?>
    </table>
</div>
<?php } else { ?>
<div class="panel panel-default panel-block panel-title-block">
    <form id="frm_search" action="agents_commissions.php" method="GET" class="theme-form" autocomplete="off">
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
                        <div class="col-md-6">
                            <div class="form-group height_auto">
                            <select class="se_multiple_select listing_search" name="agents_ids[]" id="agents_ids" multiple="multiple">
                                <?php if(!empty($resCommAgent)){ ?>
                                    <?php foreach($resCommAgent as $value){ ?>
                                        <option value="<?=$value['agentId']?>"><?=$value['agentDispId'].' - '.$value['agentName']?></option>
                                    <?php } ?>
                                <?php } ?>
                              </select>
                            <label>Agent ID/Name(s)</label>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <button type="submit" class="btn btn-info" name="search" id="search"><i class="fa fa-search"></i> Search
                        </button>
                        <button type="button" class="btn btn-info btn-outline" name="viewall" id="viewall" onClick="window.location = 'agents_commissions.php'"><i class="fa fa-search-plus"></i>  View All
                        </button>
                       
                        <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1"/>
                        <input type="hidden" name="dis_id" id="dis_id" value="<?=$dis_id?>"/>
                        <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>"/>
                        <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>"/>
                        <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>"/>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="search-handle">
        <a href="#" data-perform="panel-collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></a>
    </div>
</div>
<div class="panel panel-default panel-block">
        <div id="ajax_loader" class="ajex_loader" style="display: none;">
            <div class="loader"></div>
        </div>
        <div id="ajax_data" class="panel-body"></div>
</div>
 
<script type="text/javascript">
    $(document).ready(function() {
        ajax_submit();
        dropdown_pagination('ajax_data');

        $("#agents_ids").multipleSelect({
            selectAll: false,
            filter:true
        });
    });

    $(document).off('click', '#ajax_data tr.data-head a');
    $(document).on('click', '#ajax_data tr.data-head a', function(e) {
        e.preventDefault();
        $('#sort_by_column').val($(this).attr('data-column'));
        $('#sort_by_direction').val($(this).attr('data-direction'));
        ajax_submit();
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
                $('[data-toggle="tooltip"]').tooltip();
                common_select();
            }
        });
    });

    $(document).off("submit","#frm_search");
    $(document).on("submit","#frm_search",function(e){
        e.preventDefault();
        disable_search();
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
                $('#ajax_loader').hide();
                $('#ajax_data').html(res).show();
                $('[data-toggle="tooltip"]').tooltip();
                common_select();
                $("[data-toggle=popover]").each(function(i, obj) {
                    $(this).popover({
                        html: true,
                        placement: 'auto bottom',
                        content: function() {
                            var id = $(this).attr('data-user_id')
                            return $('#popover_content_' + id).html();
                        }
                    });
                });
            }
        });
        return false;
    }
</script>
<?php } ?>