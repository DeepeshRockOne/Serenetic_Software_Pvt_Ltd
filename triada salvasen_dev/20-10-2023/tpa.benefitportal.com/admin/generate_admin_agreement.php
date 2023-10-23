<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(3);
$admin_sql = "SELECT a.*,af.ip_address 
			FROM admin a 
			LEFT JOIN activity_feed af ON(af.user_id = a.id AND af.user_type = 'Admin' AND af.entity_action='Admin Invite Accepted') 
			WHERE md5(a.id)=:id";
$admin_row =$pdo->selectOne($admin_sql,array(':id' => $_GET['id']));
$res_t =$pdo->selectOne("SELECT md5(id) as id,type,terms FROM terms WHERE type='Admin' and status='Active'");
if($res_t['terms']){

	$terms = trim($res_t['terms']);
	$pdf_html_code = '';
	define("DOMPDF_ENABLE_HTML5PARSER", true);
	require_once "../libs/dompdf/dompdf_config.inc.php";
	ob_start();
	$pdf_html_code .= 
          '<!DOCTYPE html>
            <html>
            <head>
              <title></title>
            </head>
          <body style="font-family:Roboto, Helvetica, sans-serif;background-color:#fff;">';
  	$pdf_html_code .= '<div style="display:inline-block; text-align:right;">
                      <h4>ADMIN AGREEMENT</h4>
                      <p>'.$admin_row['fname'].' '.$admin_row['lname'].' ('.$admin_row['display_id'].')'.'</p>
                      <p><a href="mailto:'.$admin_row['email'].'">'.$admin_row['email'].'</a></p>
                      <p>'.format_telephone($admin_row['phone']).'</p>
                      '.(!empty($admin_row['ip_address'])?'<p>'.$admin_row['ip_address'].'</p>':'').'
                      <p>'.date('m/d/Y',strtotime($admin_row['created_at'])).'</p>
                  </div>';
	$pdf_html_code .= $terms;
  	$pdf_html_code .= '</body></html>';
	$html = ob_get_clean();
	$dompdf = new DOMPDF();
	$dompdf->set_paper('a4', 'portrait');
	$dompdf->load_html($pdf_html_code);
	$dompdf->render();
	$content = $dompdf->output();
	header('Content-type:application/pdf');
	header('Content-disposition: attachment;filename="AdminAgreement_' . date('Ymd') . '.pdf"');
	echo $content;
	exit;
}
?>