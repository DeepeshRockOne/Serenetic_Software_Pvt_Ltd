<?php
include_once __DIR__ . '/includes/connect.php';

$response = array();
$response['status'] = 'fail';
$adminIds = !empty($_POST['adminIds']) ? $_POST['adminIds'] : array();
if(!empty($adminIds)){
    $incr = '';
    $incr .= ' AND id IN ('.implode(',', $adminIds).')';
    $adminRes = $pdo->select("SELECT id,fname,lname,display_id,status FROM admin WHERE is_deleted = 'N' " .$incr. " ");
    if(!empty($adminRes) && count($adminRes)>0){
        $table_data = '<div class="table-responsive">
              <table class="'.$table_class.'">
                <tbody>
                  <thead>
                    <tr>
                      <th>Admin ID</th>
                      <th>Name</th>
                      <th class="text-center">Status</th>
                      <th width="70px">Action</th>
                    </tr>
                  </thead>
                  <tbody>';
        foreach ($adminRes as $key => $value) {
        $table_data .= '
                <tr>
                    <td >'.$value['display_id'].'</td>
                    <td >'.$value['fname'].' '.$value['lname'] .'</td>
                    <td class="text-center">'.$value['status'].'</td>
                    <td class="icons"><a href="javascript:void(0);" class="adminSelected" data-id="'.$value['id'].'"><i class="fa fa-times-circle"></i></a></td>
                </tr>';
        }

        $table_data .= '</tbody>
                </tbody>
              </table>
              </div>';
        $response['status'] = 'success';
        $response['data_html'] = $table_data;

    }
}
echo json_encode($response);
dbConnectionClose();
?>