<?php
include_once __DIR__ . '/includes/connect.php'; 
$id_card_content = '';

if(isset($_GET['ws_id'])) {
	$id_card_id = isset($_GET['id_card_id'])?$_GET['id_card_id']:""; //MD5
	$ws_sql = "SELECT ws.*,p.product_code,p.name as product_name,CONCAT(c.fname,' ',c.lname) as member_name,c.rep_id
	                FROM website_subscriptions ws
	                JOIN prd_main p on(p.id=ws.product_id)
	                JOIN customer c on(c.id=ws.customer_id)
	                WHERE MD5(ws.id)=:ws_id";
	$ws_where = array(":ws_id" => $_GET['ws_id']);
	$ws_row = $pdo->selectOne($ws_sql, $ws_where);

	if(!empty($ws_row)) {
		$group_code = '';
		$plan_code = '';
		$prd_plan_code_sql = "SELECT code.code_no,code.plan_code_value
		                FROM prd_plan_code as code
		                WHERE code.product_id=:product_id AND code.is_deleted = 'N'";
		$prd_plan_code_where = array(":product_id" => $ws_row['product_id']);
		$prd_plan_code_res = $pdo->select($prd_plan_code_sql,$prd_plan_code_where);
		if(!empty($prd_plan_code_res)) {
			foreach ($prd_plan_code_res as $key => $prd_plan_code_row) {
				if($prd_plan_code_row['code_no'] == "GC") {
					if($group_code != '') {
						$group_code .= ', ';
					}
					$group_code .= $prd_plan_code_row['plan_code_value'];
				}
				if($prd_plan_code_row['code_no'] == "PC") {
					if($plan_code != '') {
						$plan_code .= ', ';
					}
					$plan_code .= $prd_plan_code_row['plan_code_value'];	
				}
			}
		}

		$nbOrder = getNBOrderDetails($ws_row['customer_id'],$ws_row['product_id']);
		
		$id_card_where = array(
			":product_id" => $ws_row['product_id'],
			":today_date" => (!empty($nbOrder["orderDate"]) ? date("Y-m-d",strtotime($nbOrder["orderDate"])) : date("Y-m-d"))
		);
		$incr = "";
		if(!empty($id_card_id)) {
			$incr = " AND md5(sr.id)=:id_card_id";
			$id_card_where[':id_card_id'] = $id_card_id;
		}
		$id_card_sql = "SELECT sr.description
				FROM sub_resources sr 
				JOIN resources r ON(sr.res_id = r.id)
				JOIN res_products rp ON(rp.res_id=r.id)
				JOIN prd_main pm ON(pm.id=rp.product_id OR pm.parent_product_id=rp.product_id)
				WHERE 
				sr.is_deleted='N' AND
				r.is_deleted='N' AND 
				r.status='Active' AND 
				r.user_group='Member' AND
				pm.id=:product_id AND 
				r.effective_date <= :today_date AND 
				(r.termination_date = '0000-00-00' OR r.termination_date IS NULL OR r.termination_date >= :today_date) AND 
				r.type = 'id_card' $incr
				GROUP BY sr.id
				ORDER BY sr.group_id";
		$id_card_row = $pdo->selectOne($id_card_sql,$id_card_where);
		if(!empty($id_card_row)) {
			$id_card_content = $id_card_row['description'];
		}

		$dependents_str = '';
	    $dep_sql = "SELECT *,AES_DECRYPT(ssn,'" . $CREDIT_CARD_ENC_KEY . "') as ssn  
	    			FROM customer_dependent 
	    			WHERE 
	    			customer_id=:customer_id AND 
	    			product_id=:product_id AND 
	    			product_plan_id=:product_plan_id 
	    			GROUP BY cd_profile_id 
	    			ORDER BY id DESC";
		$dep_where = array(
			":customer_id" => $ws_row['customer_id'],
			":product_id" => $ws_row['product_id'],
			":product_plan_id" => $ws_row['plan_id']
		);
		$dep_res = $pdo->select($dep_sql,$dep_where);
	    if(count($dep_res) > 0){ 
	    	foreach ($dep_res as $key => $dep_row) {
	    		$dep_eligility_code = (!empty($dep_row['eligibility_code'])?'0'.$dep_row['eligibility_code']:'');
	    		$dependents_str .= $dep_eligility_code.' '.$dep_row['fname'].' '.$dep_row['lname'].' <br/>';
	    	}
	    }

		$id_card_tags = array(
			"position:absolute;" => "position:fixed;",
			"[[member_name]]" => $ws_row['member_name'],
			"[[member_id]]" => $ws_row['rep_id'],
			"[[benifit_tier]]" => (isset($prdPlanTypeArray[$ws_row['prd_plan_type_id']]['title'])?$prdPlanTypeArray[$ws_row['prd_plan_type_id']]['title']:''),
			"[[effective_date’]]" => (strtotime($ws_row['eligibility_date']) ? date('m/d/Y',strtotime($ws_row['eligibility_date'])):''),
			"[[dependents]]" => $dependents_str,
			"[[dep_fname]]" => '',
			"[[dep_lname]]" => '',
			"[[group_code]]" => $group_code,
			"[[plan_code]]" => $plan_code,
			"[[product_id]]" => $ws_row['product_code'],
			"[[product_name]]" => $ws_row['product_name'],
		);
		$id_card_content = str_replace(array_keys($id_card_tags),array_values($id_card_tags),$id_card_content);

		$smart_tags = get_user_smart_tags($ws_row['customer_id'],'member',$ws_row['product_id'],$ws_row['id']);
		if($smart_tags){
			foreach ($smart_tags as $key => $value) {
			  $id_card_content = str_replace("[[" . $key . "]]", $value, $id_card_content);
			}
		}	
	}
}

if(!empty($id_card_content)) {
	$id_card_content = preg_replace('/[^(\x20-\x7F)\x0A\x0D]*/','', $id_card_content);
	$SITE_LOGO = $HOST . "/images/logo.png";

	ob_start();
	?>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" style="width:580px; font-family:Arial, Helvetica, sans-serif; font-size:12px;">
	   	<tr>
	      	<td>
	      		<div class="id_card_wrap">
      				<?php echo $id_card_content; ?>
      			</div>
	      	</td>
	  	</tr>
	</table>
	<?php
  	$pdf_html_code = ob_get_clean();

	/*require_once "libs/dompdf/dompdf_config.inc.php";
	$dompdf = new DOMPDF();
	$dompdf->load_html($pdf_html_code);
	$dompdf->render();
	$content = $dompdf->output();
	header('Content-type:application/pdf');
	header('Content-disposition: attachment;filename="User_ID_Card_' . date('Ymd') . '.pdf"');
	echo $content;
	exit();	*/

	require_once __DIR__ . '/libs/mpdf/vendor/autoload.php';
	require_once "libs/mpdf/src/Mpdf.php";
	$mpdf = new \Mpdf\Mpdf();
	$stylesheet = file_get_contents('css/mpdf_common_style.css');
    $mpdf->WriteHTML($stylesheet,1);
    $mpdf->WriteHTML($pdf_html_code,2);
	$mpdf->use_kwt = true;
	$mpdf->shrink_tables_to_fit = 1;  
	/*header('Content-type:application/pdf');
	header('Content-disposition: attachment;filename="User_ID_Card_' . date('Ymd') . '.pdf"');*/
	echo $mpdf->Output("User_ID_Card_" . date('Ymd') . ".pdf","D");
} else {
	$id_card_content = '<div class="text-center"><h2>Coming Soon!</h2></div>';
	$template = 'id_card_popup.inc.php';
	$layout = 'iframe.layout.php';
	include_once 'layout/end.inc.php';
}
?>
