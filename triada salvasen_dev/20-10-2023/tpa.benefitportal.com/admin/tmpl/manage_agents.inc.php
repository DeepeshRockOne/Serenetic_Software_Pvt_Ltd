<div class="add_level_panelwrap">
<div class="panel panel-default panel-block ">
  <div class="panel-body">
    <div class="add-level-heading m-b-15 clearfix">
    <div class="pull-left">
      <p class="fs16 fw600 mn">Agent Levels</p>
      <p class="mn">The available levels to assign to agent or agency. Each level name and available agent portal features may be customized.</p>
     </div>
    </div>
    <div class="table-responsive">
      <table class="<?=$table_class?>">
        <thead>
          <tr>
            <th>Access Level Name</th>
            <th class="text-center">Assigned #</th>
            <th>Added Date</th>
            <th width="100px" class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($acl as $ac) {?>
          <tr>
            <td><?=$ac['level_heading']?></td>
            <td class="text-center"><?=$total_ass[$ac['id']]?></td>
            <td><?=$tz->getDate($ac['created_at'])?></td>
             <td class="icons text-center"><a href="edit_agents_level.php?id=<?=$ac['id1']?>&lvl_name=<?=$ac['level_heading']?>" class="edit_new_level"><i class="fa fa-edit"></i></a></td>
          </tr>
        <?php } ?>
        </tbody>
      </table>
    </div>
    <div class="clearfix m-b-20 m-t-30 clearfix">
      <div class="pull-left">
      <p class="fs16 fw600 mn">Agent Agreement</p>
      <p class="mn">The terms an agent will agree to upon setting up an account.</p>
     </div>
      <div class="pull-right"> <a href="javascript:void(0);"  id="edit_terms" data-id="<?=$res_t['id']?>" data-type="Agent" ></i></a> </div>
    </div>
    <textarea rows="13" class="summernote" id="agent_terms" name="agent_terms">
      <?=$res_t['terms']?>
    </textarea>
    <div class="pull-right m-t-15">
        <a data-href="agent_agreement_smart_tag_popup.php" class="btn btn-info btn-outline smart_tag_popup">Smart Tags <i class="fa fa-info-circle" aria-hidden="true"></i></a>
      </div>
  </div>
</div>
<script type="text/javascript">
  
  $(document).ready(function() {
      $("#edit_terms").addClass('fa fa-edit fs18 edit_term');
      initCKEditor("agent_terms",true);
    var not_win = '';
    $(".smart_tag_popup").on('click',function(){
      $href = $(this).attr('data-href');
      var not_win = window.open($href, "myWindow", "width=768,height=600");
      if(not_win.closed) {  
        // alert('closed');  
      } 
    });
  });

  $(document).off('click', '.edit_new_level');
  $(document).on('click', '.edit_new_level', function(e) {
    e.preventDefault();
    $.colorbox({
      href: $(this).attr('href'),
      iframe: true,
      width: '500px',
      height: '600px'
    });
  });

  $(document).off('click', '#edit_terms');
  $(document).on('click', '#edit_terms', function(e) {
    if ($(this).hasClass('edit_term')) {
      CKEDITOR.instances['agent_terms'].setReadOnly(false);
      $("#edit_terms").removeClass('edit_term fa fa-edit fs18 ');
      $("#edit_terms").addClass('btn btn-info save_term ').text('Save');
    } else { 
      $("#edit_terms").removeClass('btn btn-info save_term').text('');;
      $("#edit_terms").addClass('fa fa-edit fs18  edit_term');
      $('#ajax_loader').show();
      var id = $(this).data('id');
      var type = $(this).data('type');
      var terms = CKEDITOR.instances.agent_terms.getData();
      $.ajax({
        url: 'ajax_update_terms.php',
        data: {
          id: id,
          type: type,
          terms: terms
        },
        type: 'POST',
        success: function(res) {
          $('#ajax_loader').hide();
          if(res.status='success'){
            setNotifySuccess(res.msg);
            CKEDITOR.instances['agent_terms'].setReadOnly(true);
          }else{
            setNotifyError(res.msg);
          }
        }
      });
    }
  });

</script>