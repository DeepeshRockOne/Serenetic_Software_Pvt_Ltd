<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
 
$id = (isset($_GET['id'])?$_GET['id']:'');
$name="";
$short_description="";
$category_image="default_image.jpg";
$arrangeCatProduct = array();
if(!empty($id)){

	$sql="SELECT id,title,short_description,category_image FROM prd_category WHERE is_deleted='N' AND md5(id)=:id";
	$res=$pdo->selectOne($sql,array(":id"=>$id));

	$sqlCatProduct = "SELECT pc.title as categoryName,pc.id as category_id,p.id as product_id,p.name as productName,p.product_code,
				if(pcp.id is not null,pcp.connection_id,'0') as connection,
				if(pcp.id is not null,pcp.order_by,p.order_by) as order_by
				FROM prd_category pc 
				JOIN prd_main p ON (pc.id = p.category_id AND p.is_deleted='N')
				LEFT JOIN prd_connected_products pcp ON (pc.id = pcp.category_id AND pcp.is_deleted='N' AND pcp.product_id = p.id)
				WHERE MD5(pc.id) = :category_id order by p.order_by ASC";
	$resCatProduct = $pdo->select($sqlCatProduct,array(":category_id"=>$id));

	
	if(!empty($resCatProduct)){
		foreach ($resCatProduct as $key => $value) {
			if(empty($value['connection'])){
				$arrangeCatProduct[$key]=$value;
			}else{
				if(!array_key_exists('conn_'.$value['connection'], $arrangeCatProduct)){
					$arrangeCatProduct['conn_'.$value['connection']] = array();
				}
				array_push($arrangeCatProduct['conn_'.$value['connection']], $value);
			}
		}

	}
	
	if($res){
	  $name=$res['title'];
	  $short_description=$res['short_description'];
	  $category_image=!empty($res['category_image']) ? $res['category_image'] : 'default_image.jpg';
	  $old_category_image=!empty($res['category_image']) ? $res['category_image'] : '';
	} 

	$description['ac_message'] =array(
		'ac_red_1'=>array(
			'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
			'title'=>$_SESSION['admin']['display_id'],
		),
		'ac_message_1' =>' Read Product Categorie ',
		'ac_red_2'=>array(
			//'href'=> '',
			'title'=>$res['title'],
		),
	); 
	activity_feed(3, $_SESSION['admin']['id'], 'Admin', $res['id'], 'product_categories','Admin Read Product Categories', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
}

$tmpExJs = array('thirdparty/jquery_ui/js/jquery-ui-1.9.2.custom.min.js');

$exJs = array(
	"thirdparty/ajax_form/jquery.form.js"
);

$template = 'product_categories_edit.inc.php';
$layout="iframe.layout.php";
include_once 'layout/end.inc.php';
?>