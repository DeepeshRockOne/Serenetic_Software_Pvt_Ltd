<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/function.class.php';
require dirname(__DIR__) . '/libs/awsSDK/vendor/autoload.php';

use Aws\S3\S3Client;

$functionClass = new functionsList();
$response = array();
$response['status'] = 'fail';
$validate = new Validation();

$pay_period = checkIsset($_REQUEST["pay_period"]) ? date("Y-m-d", strtotime($_REQUEST["pay_period"])) : '';
$payDate = checkIsset($_REQUEST["payDate"])!='' ? $_REQUEST["payDate"] : '';
$hrm_payment_duration = checkIsset($_REQUEST["hrm_payment_duration"]);
$memberIds = checkIsset($_REQUEST["memberIds"], 'arr');
$groupIds = checkIsset($_REQUEST["groupIds"], 'arr');
$admin_id = $_SESSION['admin']['id'];

if ((!empty($groupIds) || !empty($memberIds)) && !empty($hrm_payment_duration) && !empty($pay_period)) {

    $incr = "";

    if (!empty($groupIds)) {
        $incr .= " hrmp.group_id IN (" . implode(",", $groupIds) . ") ";
    }

    if (!empty($memberIds)) {
        $incr .= !empty($groupIds) ? ' OR ' : '';
        $incr .= " hrmp.payer_id IN (" . implode(",", $memberIds) . ") ";
    }

    if (!empty($payDate)) {
        $incr .= " AND hrmp.pay_date IN ('" . implode("','", $payDate) . "') ";
    }

    $selHRMPayment = "SELECT hrmp.id,SUM(hrmp.amount) as total_amount,CONCAT(a.fname,' ',a.lname) AS account_name,a.rep_id,a.id AS groupId,
                        AES_DECRYPT(cbp.ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "') as account_number,
                        AES_DECRYPT(cbp.ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "') as routing_number,
                        cbp.ach_account_type as account_type,o.display_id as order_display_id,hrmp.pay_date
                        FROM hrm_payment hrmp
                        JOIN customer a ON(hrmp.payer_id=a.id AND a.type='Customer')
                        JOIN orders o ON (hrmp.order_id=o.id)
                        LEFT JOIN customer_billing_profile cbp ON (a.sponsor_id=cbp.customer_id)
                        WHERE hrmp.is_deleted='N' AND hrmp.status='Completed' AND ( " . $incr . " ) AND hrmp.pay_period=:payPeriod AND hrmp.hrm_payment_duration = :hrm_payment_duration GROUP BY hrmp.payer_id";
    $sch_params = array(
        ':payPeriod' => $pay_period,
        ':hrm_payment_duration' => $hrm_payment_duration,
    );
    $resHRMPaymentRecords = $pdo->select($selHRMPayment, $sch_params);

    $content = $functionClass->generate_ach_betch_file($resHRMPaymentRecords);

    if (!empty($content)) {

        $file_name = str_replace('.', '', microtime(true)) . '_' . 'NACHA';
        $file_type = 'txt';

        $s3Client = new S3Client([
            'version' => 'latest',
            'region'  => $S3_REGION,
            'credentials' => array(
                'key' => $S3_KEY,
                'secret' => $S3_SECRET
            )
        ]);

        $result = $s3Client->putObject(array(
            'Bucket' => $S3_BUCKET_NAME,
            'Key'    => $NACHA_FILES_PATH . $file_name . '.' . $file_type,
            'Body' => $content,
        ));

        $code = $result['@metadata']['statusCode'];

        if ($code === 200) {
            $REAL_IP_ADDRESS = get_real_ipaddress();
            $insSql = array(
                'admin_id' => $admin_id,
                'file_name' => $file_name,
                'file_type' => $file_type,
                'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? makesafe($REAL_IP_ADDRESS['original_ip_address']) : makeSafe($REAL_IP_ADDRESS['ip_address']),
            );

            $file_id = $pdo->insert("nacha_file_export", $insSql);

            // Activity Feed Code Start
            foreach ($resHRMPaymentRecords as $grpRes) {
                $description['ac_message'] = array(
                    'ac_red_1' => array(
                        'href' => $ADMIN_HOST . '/admin_profile.php?id=' . md5($_SESSION['admin']['id']),
                        'title' => $_SESSION['admin']['display_id'],
                    ),
                    'ac_message_1' => ' Generated NACHA File for ',
                    'ac_red_2' => array(
                        'href' => $ADMIN_HOST . '/groups_details.php.php?id=' . md5($grpRes['groupId']),
                        'title' => $grpRes['rep_id'],
                    ),
                );
                activity_feed(3, $_SESSION['admin']['id'], 'Admin', $grpRes['groupId'], 'Group', "Generated NACHA File", $_SESSION['admin']['fname'], $_SESSION['admin']['lname'], json_encode($description));
            }
            // Activity Feed Code Ends  

            $response['status'] = 'success';
            $response['message'] = "NACHA File Generated successfully";
        } else {
            $response['status'] = 'fail';
            $response['message'] = "Something Went Wrong";
        }
    } else {
        $response['status'] = 'fail';
        $response['message'] = "Something Went Wrong";
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
