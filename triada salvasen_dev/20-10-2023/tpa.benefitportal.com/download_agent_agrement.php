<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/function.class.php';
$function_list = new functionsList();

$agent_id = checkIsset($_GET['agent_id']);
if(!empty($agent_id)){
    $agentInfo = $pdo->selectOne("SELECT c.id,c.rep_id,c.fname,c.lname,c.cell_phone,c.email,cs.ip_address,c.joined_date,cs.signature_file FROM customer c JOIN customer_settings cs ON(cs.customer_id=c.id AND cs.allow_download_agreement='Y')
    WHERE c.is_deleted='N' AND type='Agent' AND md5(c.id)=:id
    ",array(":id"=>$agent_id));
    $res_t =$pdo->selectOne('SELECT md5(id) as id,type,terms FROM terms WHERE type=:type and status=:status',array(":type"=>'Agent',":status"=>'Active')); 
    if(empty($agentInfo['id'])){
        setNotifyError("Agreement Not Found!");
        exit;
    }
    $terms = trim($res_t['terms']);

    $smart_tags = get_user_smart_tags($agentInfo['id'],'Agent');
    if($smart_tags){
      foreach ($smart_tags as $key => $value) {
        $terms = str_replace("[[" . $key . "]]", $value, $terms);
      }
    }

    if(!empty($agentInfo['signature_file'])){
        $signature_data = $function_list->getSignatureFromS3Bucket($agentInfo['signature_file']);
    }
  $pdf_html_code = '';

  ob_start();
  include_once 'tmpl/agent_agreement.inc.php';
  $pdf_html_code = ob_get_clean();

  require_once __DIR__ . '/libs/mpdf/vendor/autoload.php';
  // require_once "libs/mpdf/src/Mpdf.php";
  $mpdf = new \Mpdf\Mpdf();
  $stylesheet = file_get_contents('css/mpdf_common_style.css');
  $mpdf->WriteHTML($stylesheet,1);
  $mpdf->WriteHTML($pdf_html_code,2);
  $mpdf->use_kwt = true;
  $mpdf->shrink_tables_to_fit = 1;
  // Output a PDF file directly to the browser

  header('Content-type:application/pdf');
  header('Content-disposition: attachment;filename="AgentAgreement_' . date('Ymd') . '.pdf"');
  echo $mpdf->Output("AgentAgreement_" . date('Ymd') . ".pdf","D");
  exit;

}else{
    setNotifyError("Agreement Not Found!");
    exit;
}
?>