<?php
include_once dirname(__DIR__) . "/includes/functions.php";
include_once dirname(__DIR__) . '/includes/function.class.php';
require dirname(__DIR__) . '/libs/awsSDK/vendor/autoload.php';

use Aws\S3\S3Client;  
use Aws\Exception\AwsException;
/* This class contains hrm payments functions */

class HRMPayment
{
    private $pdo;

    public function __construct()
    {
        global $pdo;

        $this->pdo = $pdo;
    }
    /*--------------------- Get Weekly pay period Code START ------------*/
    public function getWeeklyPayPeriod($date = "")
    {
        global $pdo;
        $weekDayRes = $pdo->selectOne("SELECT commission_day FROM commission_periods_settings WHERE commission_type='weekly'");
        $commDay = !empty($weekDayRes["commission_day"]) ? $weekDayRes["commission_day"] : "Sunday";

        $date = !empty($date) ? date("Y-m-d", strtotime($date)) : date('Y-m-d');

        if (date('l', strtotime($date)) == $commDay) {
            $payPeriod = $date;
        } else {
            $payPeriod = date('Y-m-d', strtotime("next $commDay", strtotime($date)));
        }
        return $payPeriod;
    }
    /*--------------------- Get Weekly pay period Code ENDS -------------*/

    /*-------------------------- Member HRM Payment Code START  ----------*/
    public function memberHRMPayment($type, $hrmPaymentDuration, $groupId, $payer_id, $weeklyPayPeriod, $amount, $weeklyHRMPaymentId = 0, $payDate, $flag, $message = '', $extra_params = array())
    {
        $weeklyPayPeriod = date("Y-m-d", strtotime($weeklyPayPeriod));
        $creditArr = array("addCredit", "revCredit");
        if (!empty($type)) {
            if (in_array($type, $creditArr)) {
                $extra_params['message'] = $message;
                $this->hrmCreditBalance($type, $hrmPaymentDuration, $groupId, $payer_id, $weeklyPayPeriod, $amount, $weeklyHRMPaymentId, $extra_params, $payDate, $flag);
            }
        }
    }
    /*-------------------------- Member HRM Payments Code ENDS  ----------*/

    /*-------------------------- Member HRM Credit Balance Code START  ----------*/
    public function hrmCreditBalance($type, $hrmPaymentDuration, $groupId, $payer_id, $weeklyPayPeriod, $amount, $weeklyHRMPaymentId = 0, $extra_params = array(), $payDate, $flag)
    {
        global $pdo;

        $earnedCredit = array("addCredit", "revCredit");

        $creditSql = "SELECT id,credit FROM hrm_payment_credit_balance WHERE group_id=:group_id AND pay_period=:pay_period AND hrm_payment_duration=:hrm_payment_duration AND is_deleted='N'";
        $params = array(":group_id" => $groupId, ":pay_period" => $weeklyPayPeriod, ":hrm_payment_duration" => $hrmPaymentDuration);
        $creditRes = $pdo->selectOne($creditSql, $params);

        if (empty($creditRes)) {
            $insCreditParams = array(
                "group_id" => $groupId,
                "hrm_payment_duration" => $hrmPaymentDuration,
                "pay_period" => $weeklyPayPeriod,
                "status" => $flag == 'Payment Approved' ? 'Paid' : 'Open',
                "pay_date" => $payDate,
            );
            if (in_array($type, $earnedCredit)) {
                $insCreditParams["credit"] = $amount;
                $earnedBalance = $amount;
            } else {
                $earnedBalance = 0;
            }
            $credit_id = $pdo->insert("hrm_payment_credit_balance", $insCreditParams);
        } else {
            $earnedBalance = $creditRes['credit'];

            $credit = 0;
            if (in_array($type, $earnedCredit)) {
                $earnedBalance = $earnedBalance + $amount;
                $credit = $amount;
            }
            $updateCreditParams = array(
                "credit" => "msqlfunc_credit + $credit",
            );
            $updWhere = array(
                "clause" => "id=:id",
                "params" => array(":id" => $creditRes['id']),
            );
            $pdo->update("hrm_payment_credit_balance", $updateCreditParams, $updWhere);

            $credit_id = $creditRes['id'];
        }
        if ($weeklyHRMPaymentId > 0) {
            // inserting new record in HRM Payment history starts
            $insParams = array(
                "hrm_credit_id" => $credit_id,
                "group_id" => $groupId,
                "payer_id" => $payer_id,
                "hrm_payment_id" => $weeklyHRMPaymentId,
                "admin_id" => (isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : 0),
                "type" => $type,
                "amount" => $amount,
                "credit" => $earnedBalance,
                "pay_period" => $weeklyPayPeriod,
                "pay_date" => $payDate,
                "hrm_payment_duration" => $hrmPaymentDuration,
                "transaction_type" => $type,
                "message" => (isset($extra_params['message']) ? $extra_params['message'] : ''),
                "req_url" => (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''),
                "ip_address" => (isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : ''),
            );
            $pdo->insert("hrm_payment_credit_balance_history", $insParams);
            // inserting new record in HRM Payment history ends

        } else if ($weeklyHRMPaymentId == 0) {
            // Select Data For Each payment Reverse 
            $selHRMPayments = "SELECT GROUP_CONCAT(hrmp.id) AS hrmPaymentId FROM hrm_payment hrmp WHERE hrmp.is_deleted='N' AND hrmp.group_id=:groupId AND hrmp.pay_period = :pay_period AND hrmp.hrm_payment_duration = :hrm_payment_duration AND hrmp.payer_id IN(" . $payer_id . ")";
            $wparam = array(
                ":pay_period" => $weeklyPayPeriod,
                ":hrm_payment_duration" => $hrmPaymentDuration,
                ":groupId" => $groupId
            );
            $resHRMPaymeentRow = $pdo->selectOne($selHRMPayments, $wparam);
            $hrmPaymentId = explode(',', $resHRMPaymeentRow['hrmPaymentId']);
            if (!empty($hrmPaymentId)) {
                foreach ($hrmPaymentId as $hrmId) {
                    $hrmPaymentSql = "SELECT hrm_unit_price,payer_id FROM hrm_payment WHERE id = $hrmId AND is_deleted='N'";
                    $hrmPaymentData = $pdo->selectOne($hrmPaymentSql);
                    // inserting new record in HRM Payment history with reverse payment starts
                    $insParams = array(
                        "hrm_credit_id" => $credit_id,
                        "group_id" => $groupId,
                        "payer_id" => $hrmPaymentData['payer_id'],
                        "hrm_payment_id" => $hrmId,
                        "admin_id" => (isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : 0),
                        "type" => $type,
                        "amount" => $hrmPaymentData['hrm_unit_price'],
                        "credit" => $hrmPaymentData['hrm_unit_price'],
                        "pay_period" => $weeklyPayPeriod,
                        "pay_date" => $payDate,
                        "hrm_payment_duration" => $hrmPaymentDuration,
                        "transaction_type" => (isset($extra_params['transaction_type']) ? $extra_params['transaction_type'] : ''),
                        "message" => (isset($extra_params['message']) ? $extra_params['message'] : ''),
                        "req_url" => (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''),
                        "ip_address" => (isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : ''),
                    );
                    $pdo->insert("hrm_payment_credit_balance_history", $insParams);

                    // inserting new record in HRM Payment history with reverse payment ends
                }
            }
        }
    }
    /*-------------------------- Member HRM Credit Balance Code ENDS  ----------*/
}
