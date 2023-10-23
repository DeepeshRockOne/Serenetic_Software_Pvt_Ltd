<div class="section-padding">
    <div class="container"> 
        <div class="panel panel-default">
                <div class="panel-body">
                    <div class="clearfix m-b-15 tbl_filter">
                        <h4>Classes</h4>
                        <div class="pull-left">
                            <p>Create classes to assign members and enrollees (ie., Full Time, CSS Division, etc)</p>
                        </div>
                        <div class="pull-right">
                            <a href="group_add_class.php" class="btn btn-action">+ Class</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="<?=$table_class?>">
                            <thead>
                                <tr>
                                    <th >Class</th>
                                    <th class="text-center"># Assigned</th>
                                    <th>Added Date</th>
                                    <th width="90px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($resClass)) { ?>
                                    <?php foreach ($resClass as $key => $value) { ?>
                                        <tr>
                                            <td><?= $value['class_name'] ?></td>
                                            <td class="text-center"> 
                                                <a href="javascript:void(0)" data-href="members_popup.php?class=<?= $value['id'] ?>" class="members_popup text-action"><strong><?= $value['total_assigned'] ?></strong></a> 
                                            </td> 
                                            <td><a href="javascript:void(0);"><?= date('m/d/Y',strtotime($value['created_at'])) ?></td>   
                                            <td class="icons">
                                                <a href="group_add_class.php?class=<?= $value['id'] ?>" data-toggle="tooltip" title="Edit" class="edit_class" data-id="<?= $value['id'] ?>"><i class="fa fa-edit"></i></a>
                                                <?php if(empty($value['total_assigned'])) { ?>
                                                    <a href="javascript:void(0)" data-toggle="tooltip" title="Delete" class="remove_class" data-id="<?= $value['id'] ?>"><i class="fa fa-trash"></i></a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php }else{?>
                                    <tr><td colspan="4" class="text-center">No Record(s) Found</td></tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <hr>
                </div>
            </div>
    </div>
</div>
  <script type="text/javascript">
    $(document).ready(function(){
        
    });

    $(document).off("click",".members_popup");
    $(document).on("click",".members_popup",function(){
        $href=$(this).attr('data-href');
        $.colorbox({href:$href,iframe:true, width:"400px", height:"500px"})
    });

    $(document).on("click",".remove_class",function(){
        $id=$(this).attr('data-id');
        swal({
            text: "Delete Record: Are you sure?",
            showCancelButton: true,
            confirmButtonText: "Confirm",
        }).then(function() {
           $("#ajax_loader").show();
            $.ajax({
              url:'ajax_group_remove_class.php',
              dataType:'JSON',
              data:{id:$id},
              type:'POST',
              success:function(res){
                $("#ajax_loader").hide();
                if(res.status=="success"){
                  window.location.reload();     
                }else{
                  setNotifyError(res.msg);
                }
              }
            });
        }, function (dismiss) {
        });
        
      });
  </script>
