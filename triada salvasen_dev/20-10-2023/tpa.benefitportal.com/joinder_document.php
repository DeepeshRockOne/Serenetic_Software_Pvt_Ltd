<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once __DIR__ . '/includes/connect.php';
require __DIR__ . '/libs/awsSDK/vendor/autoload.php';
include_once __DIR__ . '/includes/function.class.php';

global $S3_KEY,$S3_SECRET,$S3_REGION,$S3_BUCKET_NAME;

require __DIR__ . '/libs/awsSDK/vendor/autoload.php';

use Aws\S3\S3Client;  
use Aws\Exception\AwsException;

$function_list = new functionsList();

$s3Client = new S3Client([
          'version' => 'latest',
          'region'  => $S3_REGION,
          'credentials'=>array(
              'key'=> $S3_KEY,
              'secret'=> $S3_SECRET
          )
      ]);
$s3Client->registerStreamWrapper();

$id = checkIsset($_GET['id']);
if(empty($id)){
  redirect($HOST);
}

$selAgreement = "SELECT id,agreement_file,signature_img,created_at,application_type,ip_address FROM joinder_agreements WHERE md5(id)=:id AND is_generated='Y' AND is_deleted='N'";
$agreementParams = array(":id" => $id);
$resAgreement = $pdo->selectOne($selAgreement,$agreementParams);

if(!empty($resAgreement['signature_img'])){
  $signature_data = $function_list->getSignatureFromS3Bucket($resAgreement['signature_img']);
}

$agreementContent = '';
if($resAgreement['agreement_file']){
  $result = $s3Client->getObject(array(
    'Bucket' => $S3_BUCKET_NAME,
    'Key'    => '/joinder_agreement'.$resAgreement['agreement_file']
  ));

  $agreementContent = htmlspecialchars_decode($result['Body']);
}


ob_start();
include_once 'tmpl/joinder_document.inc.php';

$pdf_html_code = ob_get_clean();
  
  require_once __DIR__ . '/libs/mpdf/vendor/autoload.php';
  $mpdf = new \Mpdf\Mpdf();
  $stylesheet = file_get_contents('css/mpdf_common_style.css');
  $mpdf->WriteHTML($stylesheet,1);
  $mpdf->WriteHTML($pdf_html_code,2);
  $mpdf->use_kwt = true;
  $mpdf->shrink_tables_to_fit = 1;
// Output a PDF file directly to the browser
  
  $filename = "Joinder_Agreement_" . $resAgreement["id"] . date('Ymd') . ".pdf";
  header('Content-type:application/pdf');
  header('Content-disposition: attachment;filename="' . $filename . '"');
  echo $mpdf->Output($filename,"D");
exit;
?>