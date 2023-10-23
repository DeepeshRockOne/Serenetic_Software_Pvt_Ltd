<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(75);
include_once 'portal_resources.class.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Client Support";
$breadcrumbes[2]['title'] = "Resources";
$breadcrumbes[2]['link'] = 'resources.php';

$adminPortal = $agentPortal = $groupPortal = $memberPortal = $allPortalRsources = array();

$allPortalRsources = $pdo->select("SELECT * from portal_resources where is_deleted='N' 
                    ORDER BY  
                    CASE 
                        WHEN portal_type = 'admin' 
                            THEN FIELD(module_name,'dashboard','User Groups','Communications','Products','Commissions','Payment','Reporting','Eligibility Files','Fullfillment','smartE')
                        WHEN portal_type = 'agent' 
                            THEN FIELD(module_name,'dashboard','Enroll','Website','Book of Business','My Production','Resources')
                        WHEN portal_type = 'member' 
                            THEN FIELD(module_name,'dashboard','add_product','my_account')
                        WHEN portal_type = 'group' 
                            THEN FIELD(module_name,'dashboard','Users','Billing','Resources')
                    END
                        ");

if(!empty($allPortalRsources)){
    foreach($allPortalRsources as $value){

        if($value['portal_type'] == 'admin'){
            array_push($adminPortal,$value);
        }else if($value['portal_type'] == 'agent'){
            array_push($agentPortal,$value);
        }else if($value['portal_type'] == 'member'){
            array_push($memberPortal,$value);
        }else if($value['portal_type'] == 'group'){
            array_push($groupPortal,$value);
        }
    }
}
    $description['ac_message'] =array(
        'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
        'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>'  read Resources Page ',
        'ac_red_2'=>array(
            'href'=> $ADMIN_HOST.'/resources.php',
            'title'=> 'Resources',
        ),
    );
    $desc=json_encode($description);
    activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $_SESSION['admin']['id'], 'Resources', 'Admin Read Resources.',$_SESSION['admin']['name'],"",$desc);

$adminresModule = portalResources::getUserModule('admin');
$agentresModule = portalResources::getUserModule('agent');
$meberresModule = portalResources::getUserModule('member');
$groupresModule = portalResources::getUserModule('group');

$template = 'resources.inc.php';
include_once 'layout/end.inc.php';
?>