<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(10);

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Emailar Dashboard';
$breadcrumbes[1]['link'] = 'emailer_dashboard.php';
$breadcrumbes[2]['title'] = 'Trigger Images';

$validate = new Validation();

$company_sql = "SELECT * FROM company";
$company_res = $pdo->select($company_sql);

$mode = "ADD";
$id = $_GET['id'];
if ($id) {
    $selSql = "SELECT * FROM trigger_images WHERE id = :id ";
    $params = array(
        ':id' => makeSafe($id)
    );
    $row = $pdo->selectOne($selSql, $params);

    if (count($row) == 0) {
        redirect('trigger_images.php');
    }
    $title = $row['title'];
    $company_id = $row['company_id'];
    $old_img = $row['src'];
    $mode = "EDIT";
} else {
    $old_img = "";
}
if (isset($_POST['save'])) {
    $title = $_POST['title'];
    $company_id = $_POST['company_id'];
    $t_img = $_FILES['t_img']['name'];
    $tmpName1 = $_FILES['t_img']['tmp_name'];
    $image_file = $_FILES['t_img'];

    if (!$id || $t_img != '') {
        $validate->image(array('required' => true, 'field' => 't_img', 'value' => $_FILES['t_img']), array('required' => 'Please browse an image'));
    }
    $validate->string(array('required' => true, 'field' => 'title', 'value' => $title), array('required' => 'Title is required'));
    $validate->string(array('required' => true, 'field' => 'company_id', 'value' => $company_id), array('required' => 'Company name is required'));

    if ($validate->isValid()) {

        if ($t_img != '') {
            $t_img_sql = "SELECT * FROM trigger_images WHERE id=:id";
            $t_img_res = $pdo->selectOne($t_img_sql,array(':id'=>$id));
            
            $old_img = "";
            if($t_img_res)
            {
                $old_img = $t_img_res['src'];
            }
            $image_name = time() . '_' . $t_img;
            $TRIGGER_IMAGE_DIR = $SITE_SETTINGS[$company_id]['TRIGGER_IMAGE']['upload'];
            remote_move_uploaded_file($TRIGGER_IMAGE_DIR, $image_file, $image_name, $company_id,$old_img);
        }
        if ($id) {

            $params = array(
                'title' => makeSafe($title),
                'company_id' => $company_id,
                'updated_at' => 'msqlfunc_NOW()'
            );
            if ($image_name != '') {
                $params['src'] = $image_name;
            }
            $where = array(
                'clause' => 'id=:id',
                'params' => array(
                    ':id' => makeSafe($id)
                )
            );

            /* Code for audit log */

            $update_params_new = $params;
            unset($update_params_new['updated_at']);
            foreach ($update_params_new as $key_audit => $up_params) {
                $extra_column .= "," . $key_audit;
            }
            if ($extra_column != '') {
                $extra_column = trim($extra_column, ',');

                $select_customer_data = "SELECT " . $extra_column . " FROM trigger_images WHERE id=:id";
                $select_customer_where = array(':id' => $id);

                $result_audit_customer_data = $pdo->selectOne($select_customer_data, $select_customer_where);
            } 

            /* End Code for audit log */
            $pdo->update('trigger_images', $params, $where);


            /* Code for audit log */
            $user_data = get_user_data($_SESSION['admin']);
            audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Trigger Image Updated Id is " . $id, $result_audit_customer_data, $update_params_new, 'trigger image updated by admin');

            /* End Code for audit log */
            if (file_exists($TRIGGER_IMAGE_DIR . $old_img) && $t_img != '') {
                unlink($TRIGGER_IMAGE_DIR . $old_img);
            }
            setNotifySuccess('Image updated successfully.');
        } else {
            $params = array(
                'title' => makeSafe($title),
                'company_id' => $company_id,
                'src' => makeSafe($image_name),
                'created_at' => 'msqlfunc_NOW()'
            );
            $triger_cat_id = $pdo->insert('trigger_images', $params);
            /* Code for audit log */
            $user_data = get_user_data($_SESSION['admin']);
            audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Trigger Image Insert And ID is :" . $triger_cat_id, '', $params, 'Trigger image created by admin');

            /* End Code for audit log */

            setNotifySuccess('Image added successfully.');
        }
        redirect('trigger_images.php');
    }
}
$errors = $validate->getErrors();

$strQuery = "SELECT tm.*,count(DISTINCT ttm.id) as total_used FROM trigger_images tm LEFT JOIN trigger_template_images ttm ON(tm.id=ttm.trg_image_id) WHERE tm.is_deleted='N' GROUP BY tm.id ORDER BY tm.id DESC";
$rows = $pdo->select($strQuery);

$page_title = "Trigger images";
$template = "trigger_images.inc.php";
$layout = "main.layout.php";
include_once 'layout/end.inc.php';
?>
