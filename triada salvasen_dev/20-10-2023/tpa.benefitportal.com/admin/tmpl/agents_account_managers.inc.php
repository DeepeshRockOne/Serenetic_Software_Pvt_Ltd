  <div class="panel panel-default panel-block  ">
    <div class="panel-heading">
      <div class="panel-title ">
        <h4 class="mn">Account Managers</h4>
      </div>
    </div>
    <div class="panel-body">
    <a href="agents_add_account_managers.php?agent_id=<?=$_GET['agent_id']?>" class="pull-right m-b-10 btn btn-action agents_add_account_managers">+ Account Manager</a>
    <div class="clearfix"></div>
      <div class="table-responsive">
          <table class="<?=$table_class?>">
              <thead>
                  <tr>
                      <th>ID/Added Date</th>
                        <th>Account Manager</th>
                        <th>Assigning Admin</th>
                        <th>Status</th>
                        <th width="130px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                  <?php if(!empty($res_acc) && count($res_acc) > 0) {
                        foreach($res_acc as $acc) {
                      ?>
                    <tr>
                      <td>
                        <a href="javascript:void(0);" class="text-red"><strong class="fw600"><?=$acc['account_manager_id']?></strong></a><br/><?=$tz->getDate($acc['created_at'],'m/d/Y')?>                          
                      </td>
                      <td><?=$acc['sa_name']?></td>
                      <td><?=!empty($acc['display_id'])?$acc['afname']." ".$acc['alname']." (<a href='admin_profile.php?id=".$acc['aid']."' target='_blank' class='red-link'>".$acc['display_id'].")":""?></td>
                      <td><?=$acc['status']?></td>
                      <td class="icons">
                        <a href="agents_add_account_managers.php?agent_id=<?=$_GET['agent_id']?>&edit=<?=md5('edit')?>&sa_id=<?=$acc['sa_id']?>" class=""><i class="fa fa-edit fa-lg"></i></a>
                        <a href="javascript:void(0)" class="delete_account_manager" data-href="agents_add_account_managers.php?agent_id=<?=$_GET['agent_id']?>&delete=<?=md5('delete')?>&sa_id=<?=$acc['sa_id']?>" class=""><i class="fa fa-trash fa-lg"></i></a>
                      </td>
                    </tr>
                  <?php } }else{?>
                    <tr>
                      <td colspan="5">
                        No rows found!
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="text-center m-t-10">
          <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Close</a>
        </div>
    </div>
  </div>
  

<script type="text/javascript">
$(document).ready(function() {
   $(document).off('click', '.agents_add_account_managers');
      $(document).on('click', '.agents_add_account_managers', function (e) {
        e.preventDefault();
        window.parent.$.colorbox({
          href: $(this).attr('href'),
          iframe: true, 
          width: '900px', 
          height: '570px'
    });
    });

    $(document).off('click', '.delete_account_manager');
      $(document).on('click', '.delete_account_manager', function (e) {
        e.preventDefault();
        var href = $(this).attr('data-href');
        swal({
          text:"Delete Account Manager: Are you sure?",
          showCancelButton : true,
          confirmButtonText: "Confirm",
          cancelButtonText: "Cancel",
        }).then(function(e1){
          $("#ajax_loader").show();
          $.ajax({
            url:href,
            type:"post",
            dataType :"json",
            success : function(res){
              $("#ajax_loader").hide();
              if(res.status == 'success'){
                window.location="agents_account_managers.php?agent_id=<?=$_GET['agent_id']?>";
                setNotifySuccess("Account deleted Successfully!");
              }else{
                setNotifyError("No agent found!");
              }
            }
          });
        },function(dismiss){

        });
    });
});
</script>
