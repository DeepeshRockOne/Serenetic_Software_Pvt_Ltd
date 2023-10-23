<?php if($is_ajaxed) { ?>
    <div class="table-responsive">
    <table class="<?=$table_class?>">
    <thead>
        <tr>
            <th>ID/Added Date</th>
            <th>Agent Name/ID</th>
            <th class="text-center" >Healthy Step(s)</th>
            <th class="text-center">Commissionable</th>
            <th width="200px">Status</th>
            <th width="130px">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if($totalRecords > 0){ ?>
            <?php foreach($fetchRecords as $row){ ?>
            <tr>
                <td><a href="javascript:void(0);" class="text-action fw500"><?=$row['display_id']?></a></br><?=$tz->getDate($row['created_at'],'m/d/Y')?></td>
                <td><?=$row['fname'].' '.$row['lname']?><br>
                    <a href="javascript:void(0);" class="text-action fw500"><?=$row['rep_id']?></a> 
                </td>
                <td class="text-center"><a href="javascript:void(0)" data-href="healthy_steps_popup.php?fee_ids=<?=$row['product_ids']?>&agent_id=<?=$row['agent_id']?>&type=show&agent_name=<?=$row['fname'].' '.$row['lname']?>" class="text-action fw500 healthy_steps_popup"><strong><?=$row['total_fees']?></strong></a></td>
                <td class="text-center"><?=in_array("Y",explode(',',$row['fee_commissionable'])) ? "Yes" : "No"?></td>
                <td>
                    <div class="theme-form pr w-130">
                    <select class="change_status_v form-control" name="variation_status" id="variation_status_<?=$row['pfid']?>" data-old_val="<?=$row['status']?>" title="<?=$row['status']?>">
                        <option data-hidden="true"></option>
                        <option value="Active" <?=$row['status'] == 'Active' ? 'selected="selected"'  : '';?>>Active</option>
                        <option value="Inactive" <?=$row['status'] == 'Inactive' ? 'selected="selected"'  : '';?>>Inactive</option>
                    </select>
                    <label>Select</label>
                    </div>
                </td>
                <td class="icons">
                    <a href="variation_healthy_steps.php?fee_id=<?=$row['pf_id']?>&agent_id=<?=md5($row['agent_id'])?>&is_clone=Y" data-toggle="tooltip" data-title="Duplicate"><i class="fa fa-clone"></i></a>
                    <a href="variation_healthy_steps.php?fee_id=<?=$row['pf_id']?>&agent_id=<?=md5($row['agent_id'])?>" data-toggle="tooltip" data-title="Edit"><i class="fa fa-edit"></i></a>
                    <a href="javascript:void(0)" data-toggle="tooltip" onclick="delete_variation_fee('<?=md5($row['agent_id'])?>','<?=$row['pf_id']?>')" data-title="Delete"><i class="fa fa-trash"></i></a>
                </td>
            </tr>
    <?php } }else echo "<tr><td colspan='6'>No record found!</td></tr>" ?>
    </tbody>
    <tfoot>
        <tr>
        <?php if($totalRecords > 0 && !empty($fetchRecords)) { ?>
            <td colspan="6">
            <?php echo $paginate_records->links_html; ?>
            </td>
        <?php } ?>
        </tr>
    </tfoot>
    </table>
</div>
<?php }else { ?>
<div class="clearfix tbl_filter">
    <div class="pull-left">
        <h4 class="m-t-7">Variation Healthy Steps</h4>
    </div>
<div class="pull-right">
    <form id="frm_search" action="get_variation_healthy_step.php" method="GET">
    <input type="hidden" name="is_ajaxed" value="" id="is_ajaxed">
    <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>"> 
    <div class="m-b-15">
        <div class="note_search_wrap auto_size" id="search_div" style="display: none; max-width: 100%;">
            <div class="phone-control-wrap theme-form">
                <div class="phone-addon">
                    <div class="form-group height_auto mn">
                    <a href="javascript:void(0);" class="search_close_btn text-light-gray">X</a>
                    </div>
                </div>
                <div class="phone-addon">
                    <div class="form-group height_auto mn">
                    <input type="text"  class="form-control" name="rep_id" value="" >
                    <label>Agent ID</label>
                    </div>
                </div>
                <!-- <div class="phone-addon w-200">
                    <div class="theme-form pr ">
                    <select class="form-control" id="healthy_steps_variation" name="healthy_steps_variation" title="&nbsp;">
                       <option data-hidden="true"></option>
                        <?php if(!empty($varition_healthy_step)){
                                foreach($varition_healthy_step as $var){
                                    echo "<option value=".$var['id'].">".$var['display_id']."</option>";
                                }
                            }
                        ?>
                    </select>
                    <label>Healthy Steps</label>
                    </div>
                </div> -->
                <div class="phone-addon w-80">
                    <div class="form-group height_auto mn">
                    <a href="javascript:void(0);" class="btn btn-info search_button">Search</a>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" class="search_btn" ><i class="fa fa-search fa-lg text-blue"></i></button>
        <a href="variation_healthy_steps.php" class="btn btn-action m-l-5" style="display:inline-block;" >+ Variation</a>
    </form>
    </div>
</div>
</div>
<div id="variation_data"></div>
<script type="text/javascript">
$(document).ready(function(e){
  dropdown_pagination('variation_data')
    ajax_get_data();
});

$(document).off("change",'.change_status_v');
$(document).on("change",'.change_status_v',function(e){
    var $val = $(this).val();
    var $id  = $(this).attr('id').replace("variation_status_",'');
    var old_val = $(this).attr('data-old_val');

    swal({
        text: 'Change Status: Are you sure?',
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
        }).then(function () {
        $("#ajax_loader").show();
        $.ajax({
            url: "get_variation_healthy_step.php",
            dataType:'JSON',
            type: 'GET',
            data: {product_id: $id,status:$val,status_change:'Y'},
            success: function (res) {
                    $("#ajax_loader").hide();
                    if (res.status == 'success'){
                    setNotifySuccess(res.message,true);
                    ajax_get_data();
                    }
            }
        });
        }, function (dismiss) {
        $('#variation_status_'+$id).val(old_val);
        });
});


$(document).off('click','.healthy_steps_popup');
$(document).on('click','.healthy_steps_popup',function(e){
    var $href = $(this).attr('data-href');
    $.colorbox({href:$href,iframe: true, width: '800px', height: '450px'});
});
$(document).off("click",'.search_button');
$(document).on("click",'.search_button',function(e){
    ajax_get_data();
});
 function ajax_get_data()
 {
    $("#is_ajaxed").val(1);
    $("#ajax_loader").show();
    $.ajax({
        url: $("#frm_search").attr('action'),
        type:"get",
        data:$("#frm_search").serialize(),
        success:function(res){
            $("#ajax_loader").hide();
            $("#variation_data").html(res);
            fRefresh();
            common_select();
        }

    });
}

$(document).off('click', '#variation_data ul.pagination li a');
$(document).on('click', '#variation_data ul.pagination li a', function(e) {
    e.preventDefault();
    $('#ajax_loader').show();
    $("#is_ajaxed").val(1);
    $('#variation_data').hide();
    $.ajax({
        url: $(this).attr('href'),
        type: 'GET',
        data:{is_ajaxed:1},
        success: function(res) {
            $('#ajax_loader').hide();
            $('#variation_data').html(res).show();
            fRefresh();
            common_select();
        }
    });
});

function delete_variation_fee(agent_id,fee_id) {
    swal({
        text: 'Delete Record: Are you sure?',
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
    }).then(function () {
        $('#ajax_loader').show();
        $.ajax({
            url: "variation_healthy_steps.php",
            dataType:'json',
            type: 'GET',
            data: {agent_id:agent_id,fee_id:fee_id,delete:'Y'},
            success: function (res) {
                $("#ajax_loader").hide();
                if (res.status == 'success'){
                    setNotifySuccess(res.message,true);
                    window.location.reload();
                }else if(res.status == 'fail' ){
                    setNotifyError(res.message,true);
                    window.location.reload();
                }
            }
        });
    }, function (dismiss) {
        window.location.reload();
    })

}

</script>
<?php } ?>