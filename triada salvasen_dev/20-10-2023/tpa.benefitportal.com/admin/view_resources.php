<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(75);
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

    $description['ac_message'] =array(
        'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
        'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>'  read '.$res['module_name'].' - '.$res['resource_name'] .' inside '.$res['portal_type'].' on client support resources page ',
        'ac_red_2'=>array(
            'href'=> $ADMIN_HOST.'/resources.php',
            'title'=> 'Resources',
        ),
    );
    $desc=json_encode($description);
    activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $_SESSION['admin']['id'], 'Resources', 'Admin Read Resources.',$_SESSION['admin']['name'],"",$desc);

$template = "view_resources.inc.php";
include_once 'layout/iframe.layout.php';
?>