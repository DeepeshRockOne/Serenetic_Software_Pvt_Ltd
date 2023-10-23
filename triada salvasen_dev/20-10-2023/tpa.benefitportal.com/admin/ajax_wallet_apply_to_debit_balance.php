<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . "/includes/commission.class.php";
$commObj = new Commission();
$validate = new Validation();

$admin_id = $_SESSION['admin']['id'];
$agent_id = $_POST['agent_id'];
$wallet_id = $_POST['walletId'];
$history_id = checkIsset($_POST['historyId']);
$res = array();


$validate->string(array('required' => true, 'field' => 'agent_id', 'value' => $agent_id), array('required' => 'Please select agent'));
$validate->string(array('required' => true, 'field' => 'wallet_id', 'value' => $wallet_id), array('required' => 'Please select wallet'));

if ($validate->isValid()) {
  $query = "SELECT * FROM commission_wallet WHERE md5(id) =:id and md5(agent_id) =:agent_id";
  $srow = $pdo->selectOne($query, array(':id' => $wallet_id, ':agent_id' => $agent_id));

  if (!empty($srow)) {

    $debit_query = "SELECT * FROM commission_debit_balance WHERE md5(agent_id) =:agent_id";
    $debit_rows = $pdo->selectOne($debit_query, array(':agent_id' => $agent_id));

    $debit_id = 0;
    $debit_balance = 0;
    if ($debit_rows) {
      $debit_id = $debit_rows['id'];
      $debit_balance = $debit_rows['balance'];
    }

    $walletBalance = $srow['balance'];
    $debitBalance = $debit_balance;
    $wallet_history_id = 0;
    if ($history_id != '') {
      $wallet_history_query = "SELECT id, amount FROM commission_wallet_history WHERE md5(id) =:id and md5(agent_id) =:agent_id";
      $wallet_history_row = $pdo->selectOne($wallet_history_query, array(':id' => $history_id, ':agent_id' => $agent_id));
      if (!empty($wallet_history_row)) {
        $walletBalance = $wallet_history_row['amount'];
        $wallet_history_id = $wallet_history_row['id'];
      }
    }

    if ((!empty($debit_rows) && ($debit_balance > 0 || $walletBalance < 0)) || (empty($debit_rows) && $walletBalance < 0)) {

      if ($walletBalance < 0 || $debitBalance >= 0) {
        $commObj->applyWalletToDebitBalance($srow['agent_id'], $srow['id'], $debit_id, $walletBalance, $debitBalance, $wallet_history_id);
        $res['status'] = 'success';
        $res['msg'] = 'Status Changed Successfully';
      } else {
        $res['status'] = 'error';
        $res['msg'] = 'Something went wrong';
        setNotifyError($res['msg'], true);
      }
    } else {
      $res['status'] = 'error';
      $res['msg'] = 'Debit balance is already 0';
      setNotifyError($res['msg'], true);
    }
  } else {
    $res['status'] = 'error';
    $res['msg'] = 'Something went wrong';
    setNotifyError($res['msg'], true);
  }
} else {
  $res['status'] = 'error';
  $res['msg'] = 'Something went wrong';
}

$errors = $validate->getErrors();
if (count($errors)) {
  $res["status"] = "fail";
  $res["error"] = $errors;
}

header('Content-type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
