<?php if ($is_ajaxed) {?>
<div class="panel panel-default panel-block">
  <div class="panel-body">
 <div class="clearfix tbl_filter">
  <div class="pull-left">
    <h4 class="m-t-7">Templates</h4>
  </div>
    <div class="pull-right">
      <div class="m-b-15">
        <a href="add_emailer_template.php" class="btn btn-action" >+ Template</a>
      </div>
    </div>
  </div>
    <div class="table-responsive">
      <table class="<?= $table_class ?>">
        <thead>
          <tr>
            <th><a href="javascript:void(0);" data-column="t.created_at" data-direction="<?php echo $SortBy == 't.created_at' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">ID/Added Date</a></th>
            <th><a href="javascript:void(0);" data-column="t.title" data-direction="<?php echo $SortBy == 't.title' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Name</a></th>
            <th><a href="javascript:void(0);">Company</a></th>
            <th class="text-center"><a href="javascript:void(0);">Triggers</a></th>
            <th class="text-center">Preview</th>
            <th width="130px" class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            if ($total_rows > 0) { 
            foreach ($fetch_rows as $rows) { 
          ?>
          <tr>
            <td>
                <a href="add_emailer_template.php?id=<?=$rows['id']?>"  class="fw500 text-red"><?=$rows['display_id']?></a><br><?=date("m/d/Y",strtotime($rows['created_at']))?>
            </td>
            <td><?=$rows['title']?></td>
            <td><?=!empty($rows['company_name']) ? $rows['company_name'] : '-'?></td>
            <td class="text-center"><?=!empty($rows['triggerCnt']) ? $rows['triggerCnt'] : '0'?></td>
            <td class="text-center icons">
               <a href="emailer_template_preview.php?id=<?=$rows['id']?>" class="previewTemplate"><i class="fa fa-eye"></i></a>
            </td>
            <td class="icons text-right">
              <a href="add_emailer_template.php?id=<?=$rows['id']?>&action=Clone" target="_blank" title="Duplicate Template" data-toggle="tooltip" data-trigger="hover"><i class="fa fa-clone" aria-hidden="true"></i></a>
              <a href="add_emailer_template.php?id=<?=$rows['id']?>&action=Edit" title="Edit Template" data-toggle="tooltip" data-trigger="hover"><i class="fa fa-edit"></i></a>
              <?php if($rows['is_default'] == 'N'){ ?>
              <a href="javascript:void(0);" title="Delete" data-toggle="tooltip" data-trigger="hover" onclick="delTemplate('<?=$rows['id']?>')"><i class="fa fa-trash"></i></a>
              <?php } ?>
            </td>
          </tr>
            <?php }
              } else {
          ?>
            <tr>
                <td colspan="6" class="text-center">No record(s) found</td>
            </tr>
        <?php }?>
        </tbody>
        <?php if ($total_rows > 0) { ?>
          <tfoot>
            <tr>
              <td colspan="8">
                <?php echo $paginate->links_html; ?>
              </td>
            </tr>
          </tfoot>
        <?php } ?>
      </table>
    </div>
  </div>
</div>
<?php } else {
  ?>

  <?php include_once 'notify.inc.php';?>
   <form id="templateFrm" action="emailer_template.php" method="GET"> <div class="panel-body theme-form">
      <input type="hidden" name="is_ajaxed" id="is_ajaxed" />
      <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
      <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
      <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
   </form>
  <div id="ajaxData"></div>

<script type="text/javascript">
  $(document).ready(function(){
    dropdown_pagination('ajaxData')
    templateList();

    $(document).off('click', '#ajaxData tr.data-head a');
    $(document).on('click', '#ajaxData tr.data-head a', function (e) {
      e.preventDefault();
      $('#sort_by_column').val($(this).attr('data-column'));
      $('#sort_by_direction').val($(this).attr('data-direction'));
      templateList();
    });

    $(document).off('click', '#ajaxData ul.pagination li a');
    $(document).on('click', '#ajaxData ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#ajaxData').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#ajaxData').html(res).show();
                $('[data-toggle="tooltip"]').tooltip();
                common_select();
            }
        });
    });

  });
  
  templateList = function(){
        $('#ajax_loader').show();
        $('#ajaxData').hide();
        $('#is_ajaxed').val('1');
        var params = $('#templateFrm').serialize();
        $.ajax({
            url: $('#templateFrm').attr('action'),
            type: 'GET',
            data: params,
            success: function (res) {
                $('#ajax_loader').hide();
                $('#ajaxData').html(res).show();
                $('[data-toggle="tooltip"]').tooltip();
                common_select();
                $('.previewTemplate').colorbox({
                  iframe:true,
                  height:"590px",
                  width:"685px",
                });
            }
        });
        return false;
  }
  
  delTemplate = function(templateId) {
      swal({
          text: '<br>Delete Record: Are you sure?',
          showCancelButton: true,
          confirmButtonText: "Confirm",
      }).then(function () {
          window.location = 'emailer_template.php?action=delTemplate&id=' + templateId;
      });
  }


</script>
<?php }?>