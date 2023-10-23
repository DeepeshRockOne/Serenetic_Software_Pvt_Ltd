<?php
include_once 'layout/start.inc.php';
 
$group_id=!empty($_POST['group_id']) ? $_POST['group_id'] : 0;
$disable_change = !empty($_POST['disable_change']) ? $_POST['disable_change'] : "";

$selSql="SELECT id,name,ein,location FROM group_company WHERE group_id=:group_id and is_deleted='N'";
$selRes=$pdo->select($selSql,array(":group_id"=>$group_id));


ob_start();
if(!empty($selRes)) { 
    foreach ($selRes as $key => $value) { ?>
      	<tr>
            <td><?= $value['name'] ?></td>
            <td><?= $value['ein'] ?></td>
            <td><?= $value['location'] ?></td>
           
            <td class="icons">
              <a href="javascript:void(0);" data-id="<?= $value['id'] ?>" class="edit_group_company"><i class="fa fa-edit"></i></a>
              <?php if(!$disable_change) { ?>
                <a href="javascript:void(0);" data-id="<?= $value['id'] ?>" class="delete_group_company"><i class="fa fa-trash"></i></a>
              <?php } ?>
            </td>

      	</tr>
  	<?php } ?>
<?php }else{ ?>
  <tr><td colspan="4" class="text-center">No Record(s) Found</td></tr>
<?php } 
  
$result = array();	
$result['html'] = ob_get_clean();
$result['status'] = "success"; 
$result['company_count'] = count($selRes); 
  
header('Content-type: application/json');
echo json_encode($result);
dbConnectionClose(); 
exit;
?>