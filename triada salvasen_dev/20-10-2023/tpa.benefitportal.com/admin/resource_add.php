<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Products";
$breadcrumbes[2]['title'] = "Resources";
$breadcrumbes[2]['link'] = 'products_resource.php';
$breadcrumbes[3]['title'] = "+ Resource";
$page_title = "Resource";
$user_groups = "active";
$cert_type_name = '';
$is_clone = (isset($_GET['is_clone']) && !empty($_GET['is_clone']) ? $_GET['is_clone'] : 'N');
$resource_res = array();
if(isset($_GET['resource_id']) && !empty($_GET['resource_id'])){
    $resource_id = $_GET['resource_id'];
    $resource_res = $pdo->selectOne("SELECT r.id, name,effective_date,termination_date,type, display_id,group_concat(rp.product_id) as product_id,user_group FROM resources r LEFT JOIN res_products rp ON(rp.res_id=r.id)   WHERE md5(r.id) = :resource_id AND r.is_deleted='N' ", array(":resource_id" => $resource_id));
    
    if(!empty($resource_res) && $resource_res['id']!=''){
      $sub_resource_res = $pdo->select("SELECT r.id,sr.id, display_id, name, type,user_group,coll_doc_url,coll_type,video_type , GROUP_CONCAT(DISTINCT(rs.state_id)) AS state_id,sr.group_id,sr.state_url,member_description,description,status, r.created_at
      FROM resources r 
      LEFT JOIN sub_resources sr ON(sr.res_id = r.id)
      LEFT JOIN res_states rs ON(rs.sub_res_id = sr.id)
      WHERE r.is_deleted='N' and sr.is_deleted='N' AND r.id=:id GROUP BY sr.id ",array(":id"=>$resource_res['id']));

      $resource_name = $resource_res['name'];
      $display_id = $resource_res['display_id'];

        $description['ac_message'] =array(
          'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.$_SESSION['admin']['id'],
            'title'=>$_SESSION['admin']['display_id'],
          ),
          'ac_message_1' =>' Read Resources',
          'ac_red_2'=>array(
              'href'=>$ADMIN_HOST.'/resource_add.php?resource_id='.md5($resource_res['id']),
              'title'=>$resource_res['display_id'],
          ),
        ); 
        activity_feed(3, $_SESSION['admin']['id'], 'Admin', $resource_res['id'], 'resource','Read Resources', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
    }else {
      setNotifyError("No record Found!");
      redirect("products_resource.php");
    }
}
$sel_prd_arr = array();
if(!empty($resource_res)){
  $sel_prd_arr = explode(',', $resource_res['product_id']); 
}

$product_res = get_active_global_products_for_filter();
ob_start();
?>
<div style='position:relative; width:550px;height:350px;'>
                              <img src='' style='width:100%; height:100%;' />
                              <div style='left:25px; top:25px; position:absolute;'>
                                <div style='position:relative; top:0px; left:0px; width:100%;'>Member ID: [[MemberID]]<br /></div>
                                <div style='position:relative; top:0px; left:0px; width:100%;'>Name: [[MemberName]]<br /></div>
                                <div style='position:relative; top:0px; left:0px; width:100%;'>Effective On: [[EffectiveDate]]<br /></div>
                                <div style='position:relative; top:0px; left:0px; width:100%;'>Tier: [[BenefitTier]]</div>
                              </div>
                              </div>
<?php

$default_id_card_template = ob_get_clean();

if(empty($resource_res) || $is_clone == 'Y'){
    include_once __DIR__ . '/../includes/function.class.php';
    $functionsList = new functionsList();
    $display_id=$functionsList->generateResourceDisplayID();
 }

 // $summernote=true;
$exStylesheets = array('
thirdparty/select2/css/select2.css', 
'thirdparty/multiple-select-master/multiple-select.css', 
'thirdparty/bootstrap-datepicker-master/css/datepicker.css' , 
'thirdparty/summernote-master/dist/summernote.css'
);
$exJs = array(
"thirdparty/ajax_form/jquery.form.js",
'thirdparty/select2/js/select2.full.min.js', 
'thirdparty/multiple-select-master/jquery.multiple.select.js', 
'thirdparty/bootstrap-datepicker-master/js/bootstrap-datepicker.js' , 
'thirdparty/wistia/E-v1.js',
'thirdparty/malihu_scroll/js/jquery.mCustomScrollbar.min.js',
'thirdparty/ckeditor/ckeditor.js',
'thirdparty/summernote-master/dist/popper.js', 
'thirdparty/summernote-master/dist/summernote.js'
);

$template = 'resource_add.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
