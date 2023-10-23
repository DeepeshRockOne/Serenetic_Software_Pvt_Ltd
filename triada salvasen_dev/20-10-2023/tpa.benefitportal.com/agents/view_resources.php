<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$id = checkIsset($_GET['id']);
$res = $pdo->selectOne("SELECT * from portal_resources where is_deleted='N' AND md5(id)=:id",array(":id"=>$id));
if(empty($id) || empty($res['id'])){
    echo "<script type='text/javascript'>
            parent.$.colorbox.close();
            parent.location.reload();
            parent.setNotifyError('Something went wrong!');
            </script>
        ";
}

$template = "view_resources.inc.php";
include_once 'layout/iframe.layout.php';
?>