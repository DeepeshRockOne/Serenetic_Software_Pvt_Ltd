<div class="panel panel-default">
  <div class="panel-heading">
    <h4 class="mn">Number of Members - <span class="fw300"><?= count($resMembers) ?></span></h4>
  </div>
  <div class="panel-body">
    <div class="table-responsive">
      <table class="<?=$table_class?> table-action"> 
        <thead>
          <th>ID</th>
          <th>Name</th>
          <th>User Type</th>
        </thead>
        <tbody>
            <?php if(!empty($resMembers)) { ?>
              <?php foreach ($resMembers as $key => $value) { ?>
                <tr>
                    <td><a href="members_details.php?id=<?php echo $rows['id']; ?>" target="_blank" class="text-action"><strong><?= $value['rep_id'] ?></strong></a></td>
                    <td><?= $value['fname'] .' '.$value['lname'] ?></td>
                    <td>Member</td>
                </tr>
              <?php } ?>
            <?php }else{ ?>
              <tr><td colspan="3" class="text-center">No Record(s) Found</td></tr>
            <?php } ?>
        </tbody>
      </table>
    </div> 
    <div class="text-center">
        <a href="javascript:void(0);" class="btn red-link" id="close_popup">Close</a>
    </div>
  </div>
</div>

<script type="text/javascript">
 $(document).ready(function() { 
   $('select.form-control').selectpicker({ 
    container: 'body', 
    style:'btn-select',
    noneSelectedText: '',
    dropupAuto:true
  });
  $('select.form-control').selectpicker('refresh');
  });

  $(document).on("click","#close_popup",function(){
    window.parent.$.colorbox.close();
  });
</script>