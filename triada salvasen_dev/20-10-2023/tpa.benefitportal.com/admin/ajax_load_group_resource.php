<?php
include_once 'layout/start.inc.php';
 
$group_id=!empty($_POST['group_id']) ? $_POST['group_id'] : 0;

$selSql="SELECT id,label,url FROM group_resource_link WHERE group_id=:group_id and is_deleted='N'";
$selRes=$pdo->select($selSql,array(":group_id"=>$group_id));


ob_start();
if(!empty($selRes)) { 
    foreach ($selRes as $key => $value) { ?>
      	<tr>
            <td><?= $value['label'] ?></td>
            <td><?= $value['url'] ?></td>
           
            <td class="icons">
              <a href="javascript:void(0);" data-id="<?= $value['id'] ?>" class="edit_group_resource"><i class="fa fa-edit"></i></a>
              <a href="javascript:void(0);" data-id="<?= $value['id'] ?>" class="delete_group_resource"><i class="fa fa-trash"></i></a>
            </td>

      	</tr>
  	<?php } ?>
<?php }else{ ?>
  <tr><td colspan="4" class="text-center">No Record(s) Found</td></tr>
<?php } 
  
$result = array();	
$result['html'] = ob_get_clean();
$result['status'] = "success"; 
$result['resource_count'] = count($selRes); 
  
header('Content-type: application/json');
echo json_encode($result);
dbConnectionClose(); 
exit;
?>