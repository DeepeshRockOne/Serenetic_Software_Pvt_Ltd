<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(53);
unset($_SESSION['temp_fee_products']);
unset($_SESSION['temp_fees']);

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Products';
$breadcrumbes[2]['title'] = 'Memberships';
$breadcrumbes[1]['link'] = 'memberships.php';
$breadcrumbes[3]['title'] = "+ Membership";
$breadcrumbes[2]['class'] = "Active";
$manage_products = "active";

// $summernote = true;

$fee_id = 0;
$membership_fee_id = 0;
$is_clone = isset($_GET['is_clone']) ? $_GET['is_clone'] : "N";
$state_res = $pdo->select("SELECT name FROM states_c WHERE country_id = 231");

$membership_name = "";
$membership_id = get_membership_id();
$membership_state = "";
$contact_fname = "";
$contact_lname = "";
$phone = "";
$email = "";
$address = "";
$address2 = "";
$city = "";
$state = "";
$zip = "";
$content = "";
if(isset($_GET['id']) && $_GET['id'] != ""){
	$fee_id = $_GET['id'];
	$fee_res = $pdo->selectOne("SELECT id FROM prd_fees WHERE md5(id) = :id",array(':id' => $fee_id));
	if(!$fee_res){
		setNotifyError('No Membership Found');
		redirect('memberships.php');
	}
	$feeSql = "SELECT pf.*,GROUP_CONCAT(DISTINCT paf.fee_id) as membership_fee_ids 
				FROM prd_fees pf
                LEFT JOIN prd_assign_fees paf on (pf.id = paf.prd_fee_id and paf.is_deleted = 'N')
                LEFT JOIN prd_main p on (p.id = paf.fee_id)
                LEFT JOIN prd_matrix pm on pm.product_id = p.id
                WHERE md5(pf.id) = :fee_id";

    $feeRow = $pdo->selectOne($feeSql, array(":fee_id" => $fee_id));
    if(!empty($feeRow)){
      	$membership_fee_id= $feeRow['membership_fee_ids'];
      	$fee_id = $feeRow['id'];
      	$membership_name = $feeRow['name'];
		$membership_id = $feeRow['display_id'];
		$contact_fname = $feeRow['contact_fname'];
		$contact_lname = $feeRow['contact_lname'];
		$phone = $feeRow['phone'];
		$email = $feeRow['email'];
		$address = $feeRow['address'];
		$address2 = $feeRow['address2'];
		$city = $feeRow['city'];
		$membership_state = $feeRow['state'];
		$zip = $feeRow['zipcode'];
		$content = $feeRow['benefits'];

		if($is_clone == 'Y'){
			$membership_name = "";
			$membership_id = get_membership_id();
		}

		$description['ac_message'] =array(
	      'ac_red_1'=>array(
	        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.$_SESSION['admin']['id'],
	        'title'=>$_SESSION['admin']['display_id'],
	      ),
	      'ac_message_1' =>' Read Membership ',
	      'ac_red_2'=>array(
	          'href'=>$ADMIN_HOST.'/memberships_mange.php?id='.md5($feeRow['id']),
	          'title'=>$feeRow['display_id'],
	      ),
	    ); 

	    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $feeRow['id'], 'provider','Read Membership', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
    }
}else{
	if(isset($_SESSION['temp_fees'])){
    $membership_fee_id = implode(',', $_SESSION['temp_fees']);
  }
}



$page_title = "+ Membership";
// $exStylesheets = array('thirdparty/summernote-master/dist/summernote.css');
$exJs = array('thirdparty/masked_inputs/jquery.maskedinput.min.js', 'thirdparty/ckeditor/ckeditor.js');
$template = 'memberships_mange.inc.php';
include_once 'layout/end.inc.php';
?>