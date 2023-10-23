<?php

//include_once dirname(__DIR__) . "/includes/xml2array.php";
/*
 * Class for processing payments
 */

class CyberxPaymentAPI {

  //Payment API type like payeezy,gpgElepreneur,gpgElevacity
  private $APIType = "";
  //Payment API mode sandbox/live
  private $APIMode = "";
  // Payeezy Elepreneur API Live and Sandbox credentials
  private $APICredentials = array();

  function __construct($payment_id = 1, $mode = "live") {
    global $SITE_ENV;
    if ($SITE_ENV!='Live') {
      $mode = 'sandbox';
    }
    $this->APIMode = $mode;
  }

  public function setAPIType($type) {
    $this->APIType = $type;
  }

  public function setAPIMode($mode) {
    $this->APIMode = $mode;
  }

  public function processPayment($params = array(), $payment_id = 1) {
    global $pdo;
    if ($params['ccnumber'] == '4701321007136819') {
        $params['amount'] = 1.00;
    }
    //Selecting Payment API details
    $selSql = "SELECT * FROM payment_master WHERE id=:id";
    $paymentRow = $pdo->selectOne($selSql, array(":id" => $payment_id));
    //pre_print($paymentRow,false);
    if ($paymentRow) {
      if ($paymentRow['live_details'] != '') {
        $this->APICredentials['live'] = json_decode($paymentRow['live_details'], true);
      }
      if ($paymentRow['sandbox_details'] != '') {
        $this->APICredentials['sandbox'] = json_decode($paymentRow['sandbox_details'], true);
      }
      $this->APIType = $paymentRow['name'];
      $name = "process_" . $paymentRow['processor_id'] . "_payment";
      $API_res = self::$name($params);
      return $API_res;
    } else {
      return array('status' => 'Fail', 'message' => 'Error in configuration');
    }
  }

  public function processPaymentACH($params = array(), $payment_id = 1) {
    global $pdo;
    //Selecting Payment API details
    $selSql = "SELECT * FROM payment_master WHERE id=:id";
    $paymentRow = $pdo->selectOne($selSql, array(":id" => $payment_id));
    //pre_print($paymentRow,false);
    if ($paymentRow) {
      if ($paymentRow['live_details'] != '') {
        $this->APICredentials['live'] = json_decode($paymentRow['live_details'], true);
      }
      if ($paymentRow['sandbox_details'] != '') {
        $this->APICredentials['sandbox'] = json_decode($paymentRow['sandbox_details'], true);
      }
      $this->APIType = $paymentRow['name'];
      $name = "process_" . $paymentRow['processor_id'] . "_payment_ach";
      $API_res = self::$name($params);
      return $API_res;
    } else {
      return array('status' => 'Fail', 'message' => 'Error in configuration');
    }
  }

  public function processRefund($params = array(), $payment_id = 1) {
    global $pdo;
    //Selecting Payment API details
    $selSql = "select * from payment_master where id=:id";
    $paymentRow = $pdo->selectOne($selSql, array(":id" => $payment_id));
    //pre_print($paymentRow,false);
    if ($paymentRow) {
      if ($paymentRow['live_details'] != '') {
        $this->APICredentials['live'] = json_decode($paymentRow['live_details'], true);
      }
      if ($paymentRow['sandbox_details'] != '') {
        $this->APICredentials['sandbox'] = json_decode($paymentRow['sandbox_details'], true);
      }
      $this->APIType = $paymentRow['name'];
      $name = "process_" . $paymentRow['processor_id'] . "_refund";
      $API_res = self::$name($params);
      return $API_res;
    } else {
      return array('status' => 'Fail', 'message' => 'Error in configuration');
    }
  }

  public function processRefundACH($params = array(), $payment_id = 1) {
    global $pdo;
    //Selecting Payment API details
    $selSql = "select * from payment_master where id=:id";
    $paymentRow = $pdo->selectOne($selSql, array(":id" => $payment_id));
    //pre_print($paymentRow,false);
    if ($paymentRow) {
      if ($paymentRow['live_details'] != '') {
        $this->APICredentials['live'] = json_decode($paymentRow['live_details'], true);
      }
      if ($paymentRow['sandbox_details'] != '') {
        $this->APICredentials['sandbox'] = json_decode($paymentRow['sandbox_details'], true);
      }
      $this->APIType = $paymentRow['name'];
      $name = "process_" . $paymentRow['processor_id'] . "_refund_ach";
      $API_res = self::$name($params);
      return $API_res;
    } else {
      return array('status' => 'Fail', 'message' => 'Error in configuration');
    }
  }

  public function processVoid($params = array(),$payment_id=1) {
    global $pdo;
    //Selecting Payment API details
    $selSql = "select * from payment_master where id=:id";
    $paymentRow = $pdo->selectOne($selSql, array(":id" => $payment_id));
    //pre_print($paymentRow,false);
    if ($paymentRow) {
      if ($paymentRow['live_details'] != '') {
        $this->APICredentials['live'] = json_decode($paymentRow['live_details'], true);
      }
      if ($paymentRow['sandbox_details'] != '') {
        $this->APICredentials['sandbox'] = json_decode($paymentRow['sandbox_details'], true);
      }
      $this->APIType = $paymentRow['name'];
      $name = "process_" . $paymentRow['processor_id'] . "_void";
      $API_res = self::$name($params);
      return $API_res;
    } else {
      return array('status' => 'Fail', 'message' => 'Error in configuration');
    }
  }

  public function processVoidACH($params = array(),$payment_id=1) {
    global $pdo;
    //Selecting Payment API details
    $selSql = "select * from payment_master where id=:id";
    $paymentRow = $pdo->selectOne($selSql, array(":id" => $payment_id));
    //pre_print($paymentRow,false);
    if ($paymentRow) {
      if ($paymentRow['live_details'] != '') {
        $this->APICredentials['live'] = json_decode($paymentRow['live_details'], true);
      }
      if ($paymentRow['sandbox_details'] != '') {
        $this->APICredentials['sandbox'] = json_decode($paymentRow['sandbox_details'], true);
      }
      $this->APIType = $paymentRow['name'];
      $name = "process_" . $paymentRow['processor_id'] . "_void_ach";
      $API_res = self::$name($params);
      return $API_res;
    } else {
      return array('status' => 'Fail', 'message' => 'Error in configuration');
    }
  }

  public function getTransactionDetail($params = array(),$payment_id=1) {
    global $pdo;
    //Selecting Payment API details
    $selSql = "SELECT * from payment_master where id=:id";
    $paymentRow = $pdo->selectOne($selSql, array(":id" => $payment_id));
    //pre_print($paymentRow,false);
    if ($paymentRow) {
      if ($paymentRow['live_details'] != '') {
        $this->APICredentials['live'] = json_decode($paymentRow['live_details'], true);
      }
      if ($paymentRow['sandbox_details'] != '') {
        $this->APICredentials['sandbox'] = json_decode($paymentRow['sandbox_details'], true);
      }
      $this->APIType = $paymentRow['name'];
      $name = "get_" . $paymentRow['processor_id'] . "_Transaction";
      $API_res = self::$name($params);
      return $API_res;
    } else {
      return array('status' => 'Fail', 'message' => 'Error in configuration');
    }
  }
  /* ************************************************
    * Authorize.net Payment API code START
  * *********************************************** */

    private function process_Authorize_payment($params) {

      $LOGIN_ID = $this->APICredentials[$this->APIMode]['login_id'];
      $TRANSACTION_KEY = $this->APICredentials[$this->APIMode]['transaction_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];

      include dirname(__DIR__) . '/libs/sdk-php-master-new/vendor/autoload.php';

      $merchantAuthentication = new net\authorize\api\contract\v1\MerchantAuthenticationType();
      $merchantAuthentication->setName($LOGIN_ID);
      $merchantAuthentication->setTransactionKey($TRANSACTION_KEY);

      $creditCard = new net\authorize\api\contract\v1\CreditCardType();
      if ($this->APIMode == 'sandbox') {
        $creditCard->setCardNumber("4007000000027");
        $creditCard->setExpirationDate("0425");
      } else {
        $creditCard->setCardNumber($params['ccnumber']);
        $creditCard->setExpirationDate($params['ccexp']);
      }
      if (self::hasValue($params['cvv'])) {
        $params['cvv'] = trim($params['cvv']);
        $creditCard->setCardCode($params['cvv']);
      }
      $paymentOne = new net\authorize\api\contract\v1\PaymentType();
      $paymentOne->setCreditCard($creditCard);

      $order = new net\authorize\api\contract\v1\OrderType();
      if (self::hasValue($params['order_id'])) {
        $order->setInvoiceNumber($params['order_id']);
      }
      if (self::hasValue($params['description'])) {
        $order->setDescription($params['description']);
      }
      // Create the Bill To info for new payment type
      $billto = new net\authorize\api\contract\v1\CustomerAddressType();
      if (self::hasValue($params['phone'])) {
        $billto->setPhoneNumber($params["phone"]);
      }
      if (self::hasValue($params['firstname'])) {
        $billto->setFirstName($params["firstname"]);
      }
      if (self::hasValue($params['lastname'])) {
        $billto->setLastName($params["lastname"]);
      }
      if (self::hasValue($params['address1'])) {
        $billto->setAddress($params["address1"]);
      }
      if (self::hasValue($params['city'])) {
        $billto->setCity($params["city"]);
      }
      if (self::hasValue($params['state'])) {
        $billto->setState($params["state"]);
      }
      if (self::hasValue($params['zip'])) {
        $billto->setZip($params["zip"]);
      }
      if (self::hasValue($params['country'])) {
        $billto->setCountry($params["country"]);
      }
      if (self::hasValue($params['email'])) {
        $billto->setEmail($params["email"]);
      }

      //create customer info
      $customerInfo = new net\authorize\api\contract\v1\CustomerDataType();
      if (self::hasValue($params['email'])) {
        $customerInfo->setEmail($params["email"]);
      }
      if (self::hasValue($params['customer_id'])) {
        $customerInfo->setId($params["customer_id"]);
      }

      //create a transaction
      $transactionRequestType = new net\authorize\api\contract\v1\TransactionRequestType();
      $transactionRequestType->setTransactionType("authCaptureTransaction");
      $transactionRequestType->setAmount(number_format($params['amount'], 2, '.', '') . "");
      $transactionRequestType->setOrder($order);
      $transactionRequestType->setCustomer($customerInfo);
      $transactionRequestType->setBillTo($billto);
      $transactionRequestType->setPayment($paymentOne);

      $request = new net\authorize\api\contract\v1\CreateTransactionRequest();
      $request->setMerchantAuthentication($merchantAuthentication);
      $request->setRefId($params['order_id']);
      $request->setTransactionRequest($transactionRequestType);
      $controller = new net\authorize\api\controller\CreateTransactionController($request);

      $response = $controller->executeWithApiResponse($API_URL);
      //pre_print($response);
      $payResArr = array();
      if($this->APIMode == 'sandbox' && $params['ccnumber'] == '4111111111111113'){
        $payResArr['status'] = "Fail";
        $payResArr['error_code'] = 'MANUAL_DECLINED';
        $payResArr['error_message'] = 'Transaction Declined';
        $payResArr['txn_id'] = 0;
      } else {
        if ($response != null) {
          if ($response->getMessages()->getResultCode() == "Ok") {
            $tresponse = $response->getTransactionResponse();
            if ($tresponse != null && $tresponse->getMessages() != null) {
              $payResArr['status'] = "Success";
              $payResArr['response_code'] = $tresponse->getResponseCode();
              $payResArr['auth_code'] = $tresponse->getAuthCode();
              $payResArr['txn_id'] = $tresponse->getTransId();
              $payResArr['code'] = $tresponse->getMessages()[0]->getCode();
              $payResArr['description'] = $tresponse->getMessages()[0]->getDescription();
              $payResArr['review_require'] = $tresponse->getResponseCode() == 4 ? 'Y' : 'N';
            } else {
              $payResArr['status'] = "Fail";
              //echo "Transaction Failed \n";
              if ($tresponse->getErrors() != null) {
                $payResArr['error_code'] = $tresponse->getErrors()[0]->getErrorCode();
                $payResArr['error_message'] = $tresponse->getErrors()[0]->getErrorText();
                $payResArr['txn_id'] = $tresponse->getTransId();
              }
            }
          } else {
            $payResArr['status'] = "Fail";
            //echo "Transaction Failed \n";
            $tresponse = $response->getTransactionResponse();
            if ($tresponse != null && $tresponse->getErrors() != null) {
              $payResArr['error_code'] = $tresponse->getErrors()[0]->getErrorCode();
              $payResArr['error_message'] = $tresponse->getErrors()[0]->getErrorText();
              $payResArr['txn_id'] = $tresponse->getTransId();
            } else {
              $payResArr['error_code'] = $response->getMessages()->getMessage()[0]->getCode();
              $payResArr['error_message'] = $response->getMessages()->getMessage()[0]->getText();
            }
          }
        } else {
          $payResArr['status'] = "Fail";
          $payResArr['error_message'] = "We can not process your payment request.";
          //echo "No response returned \n";
        }
      }
      $cyberx_response = array();
      $cyberx_response['status'] = $payResArr['status'];
      $cyberx_response['transaction_id'] = isset($payResArr['txn_id']) ? $payResArr['txn_id'] : '';
      $cyberx_response['message'] = isset($payResArr['error_message']) ? $payResArr['error_message'] : '';
      $cyberx_response['API_Type'] = $this->APIType;
      $cyberx_response['API_Mode'] = $this->APIMode;
      $cyberx_response['API_response'] = $payResArr;
      return $cyberx_response;
    }

    private function process_Authorize_payment_ach($params) {
      global $SITE_ENV;

      //****** to by pass payment processor code start ******
      /* $cyberx_response = array();
        $cyberx_response['status'] = "Success";
        $cyberx_response['transaction_id'] = "1234567AWIS";
        $cyberx_response['txn_id'] = "1234567AWIS";
        $cyberx_response['message'] = "By Passing Payment Processor";
        return $cyberx_response;*/
      //****** to by pass payment processor code start ******

      $LOGIN_ID = $this->APICredentials[$this->APIMode]['login_id'];
      $TRANSACTION_KEY = $this->APICredentials[$this->APIMode]['transaction_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];

      include dirname(__DIR__) . '/libs/sdk-php-master-new/vendor/autoload.php';

      $merchantAuthentication = new net\authorize\api\contract\v1\MerchantAuthenticationType();
      $merchantAuthentication->setName($LOGIN_ID);
      $merchantAuthentication->setTransactionKey($TRANSACTION_KEY);


      $bankAccount = new net\authorize\api\contract\v1\BankAccountType();
      $bankAccount->setEcheckType("WEB");
      $bankAccount->setAccountType(strtolower($params['ach_account_type']));
      if ($SITE_ENV=='Local') {
        $bankAccount->setRoutingNumber("091000019");
        $bankAccount->setAccountNumber("00001234");
      } else {
        $bankAccount->setRoutingNumber($params['ach_routing_number']);
        $bankAccount->setAccountNumber($params['ach_account_number']);
      }
      $bankAccount->setNameOnAccount($params['name_on_account']);
      $bankAccount->setBankName($params['bankname']);

      $paymentOne = new net\authorize\api\contract\v1\PaymentType();
      $paymentOne->setBankAccount($bankAccount);

      $order = new net\authorize\api\contract\v1\OrderType();
      if (self::hasValue($params['order_id'])) {
        $order->setInvoiceNumber($params['order_id']);
      }
      if (self::hasValue($params['description'])) {
        $order->setDescription($params['description']);
      }
      // Create the Bill To info for new payment type
      $billto = new net\authorize\api\contract\v1\CustomerAddressType();
      if (self::hasValue($params['phone'])) {
        $billto->setPhoneNumber($params["phone"]);
      }
      if (self::hasValue($params['firstname'])) {
        $billto->setFirstName($params["firstname"]);
      }
      if (self::hasValue($params['lastname'])) {
        $billto->setLastName($params["lastname"]);
      }
      if (self::hasValue($params['address1'])) {
        $billto->setAddress($params["address1"]);
      }
      if (self::hasValue($params['city'])) {
        $billto->setCity($params["city"]);
      }
      if (self::hasValue($params['state'])) {
        $billto->setState($params["state"]);
      }
      if (self::hasValue($params['zip'])) {
        $billto->setZip($params["zip"]);
      }
      if (self::hasValue($params['country'])) {
        $billto->setCountry($params["country"]);
      }
      if (self::hasValue($params['email'])) {
        $billto->setEmail($params["email"]);
      }

      //create customer info
      $customerInfo = new net\authorize\api\contract\v1\CustomerDataType();
      if (self::hasValue($params['email'])) {
        $customerInfo->setEmail($params["email"]);
      }
      if (self::hasValue($params['customer_id'])) {
        $customerInfo->setId($params["customer_id"]);
      }

      //create a transaction
      $transactionRequestType = new net\authorize\api\contract\v1\TransactionRequestType();
      $transactionRequestType->setTransactionType("authCaptureTransaction");
      $transactionRequestType->setAmount(number_format($params['amount'], 2, '.', '') . "");
      $transactionRequestType->setOrder($order);
      $transactionRequestType->setCustomer($customerInfo);
      $transactionRequestType->setBillTo($billto);
      $transactionRequestType->setPayment($paymentOne);

      $request = new net\authorize\api\contract\v1\CreateTransactionRequest();
      $request->setMerchantAuthentication($merchantAuthentication);
      $request->setRefId($params['order_id']);
      $request->setTransactionRequest($transactionRequestType);
      $controller = new net\authorize\api\controller\CreateTransactionController($request);

      $response = $controller->executeWithApiResponse($API_URL);
      //pre_print($response);
      $payResArr = array();
      if ($response != null) {
        if ($response->getMessages()->getResultCode() == "Ok") {
          $tresponse = $response->getTransactionResponse();
          if ($tresponse != null && $tresponse->getMessages() != null) {
            $payResArr['status'] = "Success";
            $payResArr['response_code'] = $tresponse->getResponseCode();
            $payResArr['auth_code'] = $tresponse->getAuthCode();
            $payResArr['txn_id'] = $tresponse->getTransId();
            $payResArr['code'] = $tresponse->getMessages()[0]->getCode();
            $payResArr['description'] = $tresponse->getMessages()[0]->getDescription();
          } else {
            $payResArr['status'] = "Fail";
            //echo "Transaction Failed \n";
            if ($tresponse->getErrors() != null) {
              $payResArr['error_code'] = $tresponse->getErrors()[0]->getErrorCode();
              $payResArr['error_message'] = $tresponse->getErrors()[0]->getErrorText();
              $payResArr['txn_id'] = $tresponse->getTransId();
            }
          }
        } else {
          $payResArr['status'] = "Fail";
          //echo "Transaction Failed \n";
          $tresponse = $response->getTransactionResponse();
          if ($tresponse != null && $tresponse->getErrors() != null) {
            $payResArr['error_code'] = $tresponse->getErrors()[0]->getErrorCode();
            $payResArr['error_message'] = $tresponse->getErrors()[0]->getErrorText();
            $payResArr['txn_id'] = $tresponse->getTransId();
          } else {
            $payResArr['error_code'] = $response->getMessages()->getMessage()[0]->getCode();
            $payResArr['error_message'] = $response->getMessages()->getMessage()[0]->getText();
          }
        }
      } else {
        $payResArr['status'] = "Fail";
        $payResArr['error_message'] = "We can not process your payment request.";
        //echo "No response returned \n";
      }
      $cyberx_response = array();
      $cyberx_response['status'] = $payResArr['status'];
      $cyberx_response['transaction_id'] = $payResArr['txn_id'] ? $payResArr['txn_id'] : '';
      $cyberx_response['message'] = $payResArr['error_message'] ? $payResArr['error_message'] : '';
      $cyberx_response['API_Type'] = $this->APIType;
      $cyberx_response['API_Mode'] = $this->APIMode;
      $cyberx_response['API_response'] = $payResArr;
      return $cyberx_response;
    }

    private function process_Authorize_refund($params) {
      $LOGIN_ID = $this->APICredentials[$this->APIMode]['login_id'];
      $TRANSACTION_KEY = $this->APICredentials[$this->APIMode]['transaction_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];

      include dirname(__DIR__) . '/libs/sdk-php-master-new/vendor/autoload.php';

      $merchantAuthentication = new net\authorize\api\contract\v1\MerchantAuthenticationType();
      $merchantAuthentication->setName($LOGIN_ID);
      $merchantAuthentication->setTransactionKey($TRANSACTION_KEY);

      $refId = 'refund_ref_' . $params['order_id'];

      // Create the payment data for a credit card
      $creditCard = new net\authorize\api\contract\v1\CreditCardType();
      if ($this->APIMode == 'sandbox') {
        //sendbox account detail
        $creditCard->setCardNumber(substr("4007000000027", -4));
        $creditCard->setExpirationDate("0418");
      } else {
        $creditCard->setCardNumber(substr($params['ccnumber'], -4));
        $creditCard->setExpirationDate($params['ccexp']);
      }

      $paymentOne = new net\authorize\api\contract\v1\PaymentType();
      $paymentOne->setCreditCard($creditCard);

      //create a transaction
      $transactionRequest = new net\authorize\api\contract\v1\TransactionRequestType();
      $transactionRequest->setTransactionType("refundTransaction");
      $transactionRequest->setAmount(number_format($params['amount'], 2, '.', '') . "");
      $transactionRequest->setRefTransId($params['transaction_id']);
      $transactionRequest->setPayment($paymentOne);

      $request = new net\authorize\api\contract\v1\CreateTransactionRequest();
      $request->setMerchantAuthentication($merchantAuthentication);
      $request->setRefId($refId);
      $request->setTransactionRequest($transactionRequest);

      $controller = new net\authorize\api\controller\CreateTransactionController($request);
      $response = $controller->executeWithApiResponse($API_URL);

      $payResArr = array();
      if ($response != null) {
        if ($response->getMessages()->getResultCode() == "Ok") {
          $tresponse = $response->getTransactionResponse();
          if ($tresponse != null && $tresponse->getMessages() != null) {
            $payResArr['status'] = "Success";
            $payResArr['response_code'] = $tresponse->getResponseCode();
            $payResArr['txn_id'] = $tresponse->getTransId();
            $payResArr['code'] = $tresponse->getMessages()[0]->getCode();
            $payResArr['description'] = $tresponse->getMessages()[0]->getDescription();
          } else {
            $payResArr['status'] = "Fail";
            //echo "Transaction Failed \n";
            if ($tresponse->getErrors() != null) {
              $payResArr['error_code'] = $tresponse->getErrors()[0]->getErrorCode();
              $payResArr['error_message'] = $tresponse->getErrors()[0]->getErrorText();
            }
          }
        } else {
          $payResArr['status'] = "Fail";
          //echo "Transaction Failed \n";
          $tresponse = $response->getTransactionResponse();
          if ($tresponse != null && $tresponse->getErrors() != null) {
            $payResArr['error_code'] = $tresponse->getErrors()[0]->getErrorCode();
            $payResArr['error_message'] = $tresponse->getErrors()[0]->getErrorText();
          } else {
            $payResArr['error_code'] = $response->getMessages()->getMessage()[0]->getCode();
            $payResArr['error_message'] = $response->getMessages()->getMessage()[0]->getText();
          }
        }
      } else {
        $payResArr['status'] = "Fail";
        $payResArr['error_message'] = "We can not process your refund request.";
      }
      $cyberx_response = array();
      $cyberx_response['status'] = $payResArr['status'];
      $cyberx_response['transaction_id'] = $payResArr['txn_id'] ? $payResArr['txn_id'] : '';
      $cyberx_response['message'] = $payResArr['error_message'] ? $payResArr['error_message'] : '';
      $cyberx_response['API_Type'] = $this->APIType;
      $cyberx_response['API_Mode'] = $this->APIMode;
      $cyberx_response['API_response'] = $payResArr;
      return $cyberx_response;
    }

    private function process_Authorize_refund_ach($params) {
      $LOGIN_ID = $this->APICredentials[$this->APIMode]['login_id'];
      $TRANSACTION_KEY = $this->APICredentials[$this->APIMode]['transaction_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];

      include dirname(__DIR__) . '/libs/sdk-php-master-new/vendor/autoload.php';

      $merchantAuthentication = new net\authorize\api\contract\v1\MerchantAuthenticationType();
      $merchantAuthentication->setName($LOGIN_ID);
      $merchantAuthentication->setTransactionKey($TRANSACTION_KEY);

      $refId = 'refund_ref_' . $params['order_id'];

      $bankAccount = new net\authorize\api\contract\v1\BankAccountType();
      $bankAccount->setEcheckType("PPD");
      $bankAccount->setAccountType(strtolower($params['ach_account_type']));
      $bankAccount->setRoutingNumber($params['ach_routing_number']);
      $bankAccount->setAccountNumber($params['ach_account_number']);
      $bankAccount->setNameOnAccount($params['name_on_account']);
      $bankAccount->setBankName($params['bankname']);

      $paymentOne = new net\authorize\api\contract\v1\PaymentType();
      $paymentOne->setBankAccount($bankAccount);

      //create a transaction
      $transactionRequest = new net\authorize\api\contract\v1\TransactionRequestType();
      $transactionRequest->setTransactionType("refundTransaction");
      $transactionRequest->setAmount(number_format($params['amount'], 2, '.', '') . "");
      $transactionRequest->setRefTransId($params['transaction_id']);
      $transactionRequest->setPayment($paymentOne);

      $request = new net\authorize\api\contract\v1\CreateTransactionRequest();
      $request->setMerchantAuthentication($merchantAuthentication);
      $request->setRefId($refId);
      $request->setTransactionRequest($transactionRequest);

      $controller = new net\authorize\api\controller\CreateTransactionController($request);
      $response = $controller->executeWithApiResponse($API_URL);

      $payResArr = array();
      if ($response != null) {
        if ($response->getMessages()->getResultCode() == "Ok") {
          $tresponse = $response->getTransactionResponse();
          if ($tresponse != null && $tresponse->getMessages() != null) {
            $payResArr['status'] = "Success";
            $payResArr['response_code'] = $tresponse->getResponseCode();
            $payResArr['txn_id'] = $tresponse->getTransId();
            $payResArr['code'] = $tresponse->getMessages()[0]->getCode();
            $payResArr['description'] = $tresponse->getMessages()[0]->getDescription();
          } else {
            $payResArr['status'] = "Fail";
            //echo "Transaction Failed \n";
            if ($tresponse->getErrors() != null) {
              $payResArr['error_code'] = $tresponse->getErrors()[0]->getErrorCode();
              $payResArr['error_message'] = $tresponse->getErrors()[0]->getErrorText();
            }
          }
        } else {
          $payResArr['status'] = "Fail";
          //echo "Transaction Failed \n";
          $tresponse = $response->getTransactionResponse();
          if ($tresponse != null && $tresponse->getErrors() != null) {
            $payResArr['error_code'] = $tresponse->getErrors()[0]->getErrorCode();
            $payResArr['error_message'] = $tresponse->getErrors()[0]->getErrorText();
          } else {
            $payResArr['error_code'] = $response->getMessages()->getMessage()[0]->getCode();
            $payResArr['error_message'] = $response->getMessages()->getMessage()[0]->getText();
          }
        }
      } else {
        $payResArr['status'] = "Fail";
        $payResArr['error_message'] = "We can not process your refund request.";
      }
      $cyberx_response = array();
      $cyberx_response['status'] = $payResArr['status'];
      $cyberx_response['transaction_id'] = $payResArr['txn_id'] ? $payResArr['txn_id'] : '';
      $cyberx_response['message'] = $payResArr['error_message'] ? $payResArr['error_message'] : '';
      $cyberx_response['API_Type'] = $this->APIType;
      $cyberx_response['API_Mode'] = $this->APIMode;
      $cyberx_response['API_response'] = $payResArr;
      return $cyberx_response;
    }

    private function process_Authorize_void($params) {
      $LOGIN_ID = $this->APICredentials[$this->APIMode]['login_id'];
      $TRANSACTION_KEY = $this->APICredentials[$this->APIMode]['transaction_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];

      include dirname(__DIR__) . '/libs/sdk-php-master-new/vendor/autoload.php';

      $merchantAuthentication = new net\authorize\api\contract\v1\MerchantAuthenticationType();
      $merchantAuthentication->setName($LOGIN_ID);
      $merchantAuthentication->setTransactionKey($TRANSACTION_KEY);

      $refId = 'refund_ref_' . $params['order_id'];

      $transactionRequestType = new net\authorize\api\contract\v1\TransactionRequestType();
      $transactionRequestType->setTransactionType("voidTransaction");
      $transactionRequestType->setRefTransId($params["transaction_id"]);

      $request = new net\authorize\api\contract\v1\CreateTransactionRequest();
      $request->setMerchantAuthentication($merchantAuthentication);
      $request->setRefId($refId);
      $request->setTransactionRequest($transactionRequestType);
      $controller = new net\authorize\api\controller\CreateTransactionController($request);
      $response = $controller->executeWithApiResponse($API_URL);
      $payResArr = array();
      if ($response != null) {
        if ($response->getMessages()->getResultCode() == "Ok") {
          $tresponse = $response->getTransactionResponse();
          if ($tresponse != null && $tresponse->getMessages() != null) {
            $payResArr['status'] = "Success";
            $payResArr['response_code'] = $tresponse->getResponseCode();
            $payResArr['txn_id'] = $tresponse->getTransId();
            $payResArr['code'] = $tresponse->getMessages()[0]->getCode();
            $payResArr['description'] = $tresponse->getMessages()[0]->getDescription();
          } else {
            $payResArr['status'] = "Fail";
            //echo "Transaction Failed \n";
            if ($tresponse->getErrors() != null) {
              $payResArr['error_code'] = $tresponse->getErrors()[0]->getErrorCode();
              $payResArr['error_message'] = $tresponse->getErrors()[0]->getErrorText();
            }
          }
        } else {
          $payResArr['status'] = "Fail";
          //echo "Transaction Failed \n";
          $tresponse = $response->getTransactionResponse();
          if ($tresponse != null && $tresponse->getErrors() != null) {
            $payResArr['error_code'] = $tresponse->getErrors()[0]->getErrorCode();
            $payResArr['error_message'] = $tresponse->getErrors()[0]->getErrorText();
          } else {
            $payResArr['error_code'] = $response->getMessages()->getMessage()[0]->getCode();
            $payResArr['error_message'] = $response->getMessages()->getMessage()[0]->getText();
          }
        }
      } else {
        $payResArr['status'] = "Fail";
        $payResArr['error_message'] = "We can not process your void request.";
      }
      $cyberx_response = array();
      $cyberx_response['status'] = $payResArr['status'];
      $cyberx_response['transaction_id'] = $payResArr['txn_id'] ? $payResArr['txn_id'] : '';
      $cyberx_response['message'] = $payResArr['error_message'] ? $payResArr['error_message'] : '';
      $cyberx_response['API_Type'] = $this->APIType;
      $cyberx_response['API_Mode'] = $this->APIMode;
      $cyberx_response['API_response'] = $payResArr;
      return $cyberx_response;
    }

    private function process_Authorize_void_ach($params) {
      $LOGIN_ID = $this->APICredentials[$this->APIMode]['login_id'];
      $TRANSACTION_KEY = $this->APICredentials[$this->APIMode]['transaction_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];

      include dirname(__DIR__) . '/libs/sdk-php-master-new/vendor/autoload.php';

      $merchantAuthentication = new net\authorize\api\contract\v1\MerchantAuthenticationType();
      $merchantAuthentication->setName($LOGIN_ID);
      $merchantAuthentication->setTransactionKey($TRANSACTION_KEY);

      $refId = 'refund_ref_' . $params['order_id'];

      $transactionRequestType = new net\authorize\api\contract\v1\TransactionRequestType();
      $transactionRequestType->setTransactionType("voidTransaction");
      $transactionRequestType->setRefTransId($params["transaction_id"]);

      $request = new net\authorize\api\contract\v1\CreateTransactionRequest();
      $request->setMerchantAuthentication($merchantAuthentication);
      $request->setRefId($refId);
      $request->setTransactionRequest($transactionRequestType);
      $controller = new net\authorize\api\controller\CreateTransactionController($request);
      $response = $controller->executeWithApiResponse($API_URL);
      $payResArr = array();
      if ($response != null) {
        if ($response->getMessages()->getResultCode() == "Ok") {
          $tresponse = $response->getTransactionResponse();
          if ($tresponse != null && $tresponse->getMessages() != null) {
            $payResArr['status'] = "Success";
            $payResArr['response_code'] = $tresponse->getResponseCode();
            $payResArr['txn_id'] = $tresponse->getTransId();
            $payResArr['code'] = $tresponse->getMessages()[0]->getCode();
            $payResArr['description'] = $tresponse->getMessages()[0]->getDescription();
          } else {
            $payResArr['status'] = "Fail";
            //echo "Transaction Failed \n";
            if ($tresponse->getErrors() != null) {
              $payResArr['error_code'] = $tresponse->getErrors()[0]->getErrorCode();
              $payResArr['error_message'] = $tresponse->getErrors()[0]->getErrorText();
            }
          }
        } else {
          $payResArr['status'] = "Fail";
          //echo "Transaction Failed \n";
          $tresponse = $response->getTransactionResponse();
          if ($tresponse != null && $tresponse->getErrors() != null) {
            $payResArr['error_code'] = $tresponse->getErrors()[0]->getErrorCode();
            $payResArr['error_message'] = $tresponse->getErrors()[0]->getErrorText();
          } else {
            $payResArr['error_code'] = $response->getMessages()->getMessage()[0]->getCode();
            $payResArr['error_message'] = $response->getMessages()->getMessage()[0]->getText();
          }
        }
      } else {
        $payResArr['status'] = "Fail";
        $payResArr['error_message'] = "We can not process your void request.";
      }
      $cyberx_response = array();
      $cyberx_response['status'] = $payResArr['status'];
      $cyberx_response['transaction_id'] = $payResArr['txn_id'] ? $payResArr['txn_id'] : '';
      $cyberx_response['message'] = $payResArr['error_message'] ? $payResArr['error_message'] : '';
      $cyberx_response['API_Type'] = $this->APIType;
      $cyberx_response['API_Mode'] = $this->APIMode;
      $cyberx_response['API_response'] = $payResArr;
      return $cyberx_response;
    }

    private function get_Authorize_Transaction($params) {

      $LOGIN_ID = $this->APICredentials[$this->APIMode]['login_id'];
      $TRANSACTION_KEY = $this->APICredentials[$this->APIMode]['transaction_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];

      include dirname(__DIR__) . '/libs/sdk-php-master-new/vendor/autoload.php';

      $merchantAuthentication = new net\authorize\api\contract\v1\MerchantAuthenticationType();
      $merchantAuthentication->setName($LOGIN_ID);
      $merchantAuthentication->setTransactionKey($TRANSACTION_KEY);

      $request = new net\authorize\api\contract\v1\GetTransactionDetailsRequest();
      $request->setMerchantAuthentication($merchantAuthentication);
      $request->setTransId($params['transaction_id']);

      $controller = new net\authorize\api\controller\GetTransactionDetailsController($request);

      $response = $controller->executeWithApiResponse($API_URL);
      
      $payResArr = array();
      if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
        $tresponse = $response->getTransaction();
        $payResArr['status'] = "Success";
        $payResArr['auth_amount'] = $tresponse->getAuthAmount();
        $payResArr['api_status'] = $tresponse->getTransactionStatus();
        $payResArr['response_code'] = $tresponse->getResponseCode();
        $payResArr['txn_id'] = $tresponse->getTransId();
      } else {
        $payResArr['status'] = "Fail";
        $errorMessages = $response->getMessages()->getMessage();

        $payResArr['error_code'] = $errorMessages[0]->getCode();
        $payResArr['error_message'] = $errorMessages[0]->getText();
      }
      $cyberx_response = array();
      $cyberx_response['status'] = $payResArr['status'];
      $cyberx_response['api_status'] = $payResArr['api_status'] ? $payResArr['api_status'] : '';
      $cyberx_response['transaction_id'] = $payResArr['txn_id'] ? $payResArr['txn_id'] : '';
      $cyberx_response['message'] = $payResArr['error_message'] ? $payResArr['error_message'] : '';
      $cyberx_response['API_Type'] = $this->APIType;
      $cyberx_response['API_Mode'] = $this->APIMode;
      $cyberx_response['API_response'] = $payResArr;
      return $cyberx_response;
    }

    function hasValue(&$var) {
      return (isset($var) && trim($var) != "") ? true : false;
    }

  /*   * ***********************************************
   * Authorize.net Payment API code OVER
  * *********************************************** */

  /*   * ***********************************************
   * NMI API code Start
  * *********************************************** */

    private function process_NMI_payment($params) {

      $USER_NAME = $this->APICredentials[$this->APIMode]['user_name'];
      $PASSWORD = $this->APICredentials[$this->APIMode]['password'];
      $API_KEY = $this->APICredentials[$this->APIMode]['api_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];
      $params['cvv'] = trim($params['cvv']);
      $fields = array(
        'username' => urlencode($USER_NAME),
        'password' => urlencode($PASSWORD),
        'type' => urlencode('sale'),
        'ccnumber' => urlencode($params['ccnumber']),
        'ccexp' => urlencode($params['ccexp']),
        'cvv' => urlencode($params['cvv']),
        'amount' => $params['amount'],
        'ipaddress' => $params['ip_address'],
        'orderid' => $params['order_id'],
        'orderdescription' => $params['description'],
        'first_name' => !empty($params['firstname']) ? $params['firstname'] : $params['fname'],
        'last_name' => !empty($params['lastname']) ? $params['lastname'] : $params['lname'],
        'address1' => $params['address1'],
        'address2' => $params['address2'],
        'city' => $params['city'],
        'state' => $params['state'],
        'zip' => $params['zip'],
        'country' => $params['country'],
        'phone' => $params['phone'],
        'email' => $params['email'],
      );

      $fields_string = '';
      foreach ($fields as $key => $value) {
        if (empty($value)) {
          continue; //skipping blank parameters
        }
        $fields_string .= $key . '=' . $value . '&';
      }
      $fields_string = rtrim($fields_string, '&');

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $API_URL);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
      curl_setopt($ch, CURLOPT_POST, 1);
      $cyberx_response = array();
      if (!($data = curl_exec($ch))) {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = 'Error in processing payment';
        return $cyberx_response;
      }
      curl_close($ch);
      unset($ch);
      //pre_print($data);
      $data = explode("&", $data);
      $response = array();
      for ($i = 0; $i < count($data); $i++) {
        $rdata = explode("=", $data[$i]);
        $response[$rdata[0]] = $rdata[1];
      }
      //pre_print($response);


      if (($response['response'] == 1) || $response['responsetext'] == 'SUCCESS') {
        $cyberx_response['status'] = 'Success';
        $cyberx_response['transaction_id'] = $response['transactionid'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
      } else {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = $response['responsetext'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['transaction_id'] = $response['transactionid'];
      }
      return $cyberx_response;
    }

    private function process_NMI_payment_ach($params) {
      global $pdo;
      //****** to by pass payment processor code start ******
        /*$cyberx_response = array();
        $cyberx_response['status'] = "Success";
        $cyberx_response['transaction_id'] = "1234567AWIS";
        $cyberx_response['txn_id'] = "1234567AWIS";
        $cyberx_response['message'] = "By Passing Payment Processor";
        return $cyberx_response;*/
      //****** to by pass payment processor code start ******

      $USER_NAME = $this->APICredentials[$this->APIMode]['user_name'];
      $PASSWORD = $this->APICredentials[$this->APIMode]['password'];
      $API_KEY = $this->APICredentials[$this->APIMode]['api_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];

      $state = $pdo->selectOne("SELECT short_name FROM states_c where name = :name", array(':name' => $params['state']));
      if($state){
        $params['state'] = $state['short_name'];
      }

      $fields = array(
        'username' => urlencode($USER_NAME),
        'password' => urlencode($PASSWORD),
        'type' => urlencode('sale'),
        'checkname' => urlencode($params['name_on_account']),
        'checkaba' => urlencode($params['ach_routing_number']),
        'checkaccount' => urlencode($params['ach_account_number']),
        'account_type' => urlencode(strtolower($params['ach_account_type'])),
        'payment' => 'check',
        'amount' => $params['amount'],
        'ipaddress' => $params['ip_address'],
        'orderid' => $params['order_id'],
        'orderdescription' => $params['description'],
        'first_name' => !empty($params['firstname']) ? $params['firstname'] : $params['fname'],
        'last_name' => !empty($params['lastname']) ? $params['lastname'] : $params['lname'],
        'address1' => $params['address1'],
        'address2' => $params['address2'],
        'city' => $params['city'],
        'state' => $params['state'],
        'zip' => $params['zip'],
        'country' => $params['country'],
        'phone' => $params['phone'],
        'email' => $params['email'],
      );

      $fields_string = '';
      foreach ($fields as $key => $value) {
        if (empty($value)) {
          continue; //skipping blank parameters
        }
        $fields_string .= $key . '=' . $value . '&';
      }
      $fields_string = rtrim($fields_string, '&');

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $API_URL);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
      curl_setopt($ch, CURLOPT_POST, 1);
      $cyberx_response = array();
      if (!($data = curl_exec($ch))) {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = 'Error in processing payment';
        return $cyberx_response;
      }
      curl_close($ch);
      unset($ch);
      //pre_print($data);
      $data = explode("&", $data);
      $response = array();
      for ($i = 0; $i < count($data); $i++) {
        $rdata = explode("=", $data[$i]);
        $response[$rdata[0]] = $rdata[1];
      }
      //pre_print($response);


      if (($response['response'] == 1) || $response['responsetext'] == 'SUCCESS') {
        $cyberx_response['status'] = 'Success';
        $cyberx_response['transaction_id'] = $response['transactionid'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
      } else {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = $response['responsetext'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['transaction_id'] = $response['transactionid'];
      }
      return $cyberx_response;
    }

    private function process_NMI_refund($params) {
      $USER_NAME = $this->APICredentials[$this->APIMode]['user_name'];
      $PASSWORD = $this->APICredentials[$this->APIMode]['password'];
      $API_KEY = $this->APICredentials[$this->APIMode]['api_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];

      $fields = array(
        'username' => urlencode($USER_NAME),
        'password' => urlencode($PASSWORD),
        'type' => urlencode('refund'),
        'transactionid' => urlencode($params['transaction_id']),
        'amount' => $params['amount'],
      );

      //url ify the data for the POST
      $fields_string = '';
      foreach ($fields as $key => $value) {
        if (empty($value)) {
          continue; //skipping blank parameters
        }
        $fields_string .= $key . '=' . $value . '&';
      }
      $fields_string = rtrim($fields_string, '&');
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $API_URL);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
      curl_setopt($ch, CURLOPT_POST, 1);
      $cyberx_response = array();
      if (!($data = curl_exec($ch))) {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = 'Error in processing payment';
        return $cyberx_response;
      }
      curl_close($ch);
      unset($ch);
      //pre_print($data);
      $data = explode("&", $data);
      $response = array();
      for ($i = 0; $i < count($data); $i++) {
        $rdata = explode("=", $data[$i]);
        $response[$rdata[0]] = $rdata[1];
      }
      //pre_print($response);

      if (($response['response'] == 1) || $response['responsetext'] == 'SUCCESS') {
        $cyberx_response['status'] = 'Success';
        $cyberx_response['transaction_id'] = $response['transactionid'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
      } else {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = $response['responsetext'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['transaction_id'] = $response['transactionid'];
      }
      return $cyberx_response;
    }

    private function process_NMI_refund_ach($params) {
      $USER_NAME = $this->APICredentials[$this->APIMode]['user_name'];
      $PASSWORD = $this->APICredentials[$this->APIMode]['password'];
      $API_KEY = $this->APICredentials[$this->APIMode]['api_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];

      $fields = array(
        'username' => urlencode($USER_NAME),
        'password' => urlencode($PASSWORD),
        'type' => urlencode('refund'),
        'transactionid' => urlencode($params['transaction_id']),
        'amount' => $params['amount'],
        'payment' => 'check',
      );

      //url ify the data for the POST
      $fields_string = '';
      foreach ($fields as $key => $value) {
        if (empty($value)) {
          continue; //skipping blank parameters
        }
        $fields_string .= $key . '=' . $value . '&';
      }
      $fields_string = rtrim($fields_string, '&');
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $API_URL);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
      curl_setopt($ch, CURLOPT_POST, 1);
      $cyberx_response = array();
      if (!($data = curl_exec($ch))) {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = 'Error in processing payment';
        return $cyberx_response;
      }
      curl_close($ch);
      unset($ch);
      //pre_print($data);
      $data = explode("&", $data);
      $response = array();
      for ($i = 0; $i < count($data); $i++) {
        $rdata = explode("=", $data[$i]);
        $response[$rdata[0]] = $rdata[1];
      }
      //pre_print($response);

      if (($response['response'] == 1) || $response['responsetext'] == 'SUCCESS') {
        $cyberx_response['status'] = 'Success';
        $cyberx_response['transaction_id'] = $response['transactionid'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
      } else {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = $response['responsetext'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['transaction_id'] = $response['transactionid'];
      }
      return $cyberx_response;
    }

    private function process_NMI_void($params) {

      $USER_NAME = $this->APICredentials[$this->APIMode]['user_name'];
      $PASSWORD = $this->APICredentials[$this->APIMode]['password'];
      $API_KEY = $this->APICredentials[$this->APIMode]['api_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];

      $fields = array(
        'username' => urlencode($USER_NAME),
        'password' => urlencode($PASSWORD),
        'type' => urlencode('void'),
        'transactionid' => urlencode($params['transaction_id']),
      );

      //url ify the data for the POST
      $fields_string = '';
      foreach ($fields as $key => $value) {
        if (empty($value)) {
          continue; //skipping blank parameters
        }
        $fields_string .= $key . '=' . $value . '&';
      }
      $fields_string = rtrim($fields_string, '&');
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $API_URL);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
      curl_setopt($ch, CURLOPT_POST, 1);
      $cyberx_response = array();
      if (!($data = curl_exec($ch))) {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = 'Error in processing payment';
        return $cyberx_response;
      }
      curl_close($ch);
      unset($ch);
      //pre_print($data);
      $data = explode("&", $data);
      $response = array();
      for ($i = 0; $i < count($data); $i++) {
        $rdata = explode("=", $data[$i]);
        $response[$rdata[0]] = $rdata[1];
      }
      //pre_print($response);

      if (($response['response'] == 1) || $response['responsetext'] == 'SUCCESS') {
        $cyberx_response['status'] = 'Success';
        $cyberx_response['transaction_id'] = $response['transactionid'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
      } else {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = $response['responsetext'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['transaction_id'] = $response['transactionid'];
      }
      return $cyberx_response;
    }

    private function process_NMI_void_ach($params) {

      $USER_NAME = $this->APICredentials[$this->APIMode]['user_name'];
      $PASSWORD = $this->APICredentials[$this->APIMode]['password'];
      $API_KEY = $this->APICredentials[$this->APIMode]['api_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];

      $fields = array(
        'username' => urlencode($USER_NAME),
        'password' => urlencode($PASSWORD),
        'type' => urlencode('void'),
        'transactionid' => urlencode($params['transaction_id']),
        'payment' => 'check',
      );

      //url ify the data for the POST
      $fields_string = '';
      foreach ($fields as $key => $value) {
        if (empty($value)) {
          continue; //skipping blank parameters
        }
        $fields_string .= $key . '=' . $value . '&';
      }
      $fields_string = rtrim($fields_string, '&');
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $API_URL);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
      curl_setopt($ch, CURLOPT_POST, 1);
      $cyberx_response = array();
      if (!($data = curl_exec($ch))) {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = 'Error in processing payment';
        return $cyberx_response;
      }
      curl_close($ch);
      unset($ch);
      //pre_print($data);
      $data = explode("&", $data);
      $response = array();
      for ($i = 0; $i < count($data); $i++) {
        $rdata = explode("=", $data[$i]);
        $response[$rdata[0]] = $rdata[1];
      }
      //pre_print($response);

      if (($response['response'] == 1) || $response['responsetext'] == 'SUCCESS') {
        $cyberx_response['status'] = 'Success';
        $cyberx_response['transaction_id'] = $response['transactionid'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
      } else {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = $response['responsetext'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['transaction_id'] = $response['transactionid'];
      }
      return $cyberx_response;
    }

    private function get_NMI_Transaction($params) {

      $USER_NAME = $this->APICredentials[$this->APIMode]['user_name'];
      $PASSWORD = $this->APICredentials[$this->APIMode]['password'];
      $API_KEY = $this->APICredentials[$this->APIMode]['api_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];
      $API_URL = "https://secure.networkmerchants.com/api/query.php";

      $fields = array(
        'username' => urlencode($USER_NAME),
        'password' => urlencode($PASSWORD),
        'transaction_id' => $params['transaction_id'],
      );
      
      //url ify the data for the POST
      $fields_string = '';
      foreach ($fields as $key => $value) {
        if (empty($value)) {
          continue; //skipping blank parameters
        }
        $fields_string .= $key . '=' . $value . '&';
      }
      $fields_string = rtrim($fields_string, '&');
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $API_URL);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
      curl_setopt($ch, CURLOPT_POST, 1);
      $cyberx_response = array();
      if (!($data = curl_exec($ch))) {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = 'Error in processing payment';
        return $cyberx_response;
      }
      curl_close($ch);
      unset($ch);
      $simpleXml = simplexml_load_string($data);
      $json = json_encode($simpleXml);
      $response=json_decode($json,true);
      
      

      if (!empty($response['transaction'])) {
        if(isset($response['transaction'][0]['transaction_id'])) {
            $transaction_id = 0;
            $api_status = "";
            foreach ($response['transaction'] as $value) {
                if($value['transaction_id'] == $params['transaction_id']) {
                  $transaction_id = $value['transaction_id'];
                  $api_status = $value['condition'];
                }
            }
        } else {
            $transaction_id = $response['transaction']['transaction_id'];
            $api_status = $response['transaction']['condition'];
        }
        $cyberx_response['status'] = 'Success';
        $cyberx_response['transaction_id'] = $transaction_id;
        $cyberx_response['txn_id'] = $transaction_id;
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['api_status'] = $api_status;
        $cyberx_response['message'] = (isset($response['responsetext'])?$response['responsetext']:'');
      } else {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = (isset($response['responsetext'])?$response['responsetext']:'');
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
      }
      if(empty($cyberx_response['message']) && isset($response['transaction']['action'])){
          if($response['transaction']['action']){
              foreach ($response['transaction']['action'] as $value) {
                  if($value['action_type'] == 'check_return' || $value['action_type'] == 'check_late_return'){
                      $cyberx_response['message'] = $value['response_text'];
                  }
              }
          }
      }
      return $cyberx_response;
    }
  /*   * ***********************************************
   * NMI API code Start
  * *********************************************** */


  /*   * ***********************************************
    * USEPAY API code Start
  * *********************************************** */
    private function process_USAEPAY_payment($params) {
      
      $USER_NAME = $this->APICredentials[$this->APIMode]['user_name'];
      $API_PIN = $this->APICredentials[$this->APIMode]['api_pin'];
      $API_KEY = $this->APICredentials[$this->APIMode]['api_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];

     
      $prehash = $API_KEY . $USER_NAME . $API_PIN;
      $apihash = "s2/" . $USER_NAME . "/" . hash("sha256",$prehash);
      $authKey = base64_encode($API_KEY . ':' . $apihash);
      

      $fields = array(
        'command' => 'cc:sale',
        'amount' => $params['amount'],
        'invoice' => $params['order_id'],
        'orderid' => $params['order_id'],
        'description' => $params['description'],
        'email' => $params['email'],
      );
      if(!empty($params['customer_id'])){
        $fields['custid']=$params['customer_id'];
      }
      $fields['creditcard']['cardholder']=(!empty($params['firstname']) ? $params['firstname'] : $params['fname']) .' '.(!empty($params['lastname']) ? $params['lastname'] : $params['lname']);
      $fields['creditcard']['number']=$params['ccnumber'];
      $fields['creditcard']['expiration']=$params['ccexp'];
      $fields['creditcard']['cvc']=trim($params['cvv']);

      $fields['billing_address']['firstname']=!empty($params['firstname']) ? $params['firstname'] : $params['fname'];
      $fields['billing_address']['lastname']=!empty($params['lastname']) ? $params['lastname'] : $params['lname'];
      $fields['billing_address']['street']=$params['address1'];
      $fields['billing_address']['street2']=$params['address2'];
      $fields['billing_address']['city']=$params['city'];
      $fields['billing_address']['state']=$params['state'];
      $fields['billing_address']['postalcode']=$params['zip'];
      $fields['billing_address']['country']='USA';
      $fields['billing_address']['phone']=$params['phone'];

      $fields_string = json_encode($fields);
      
      $header = array(
        'Content-Type: application/json',
        'Authorization: Basic '. $authKey,
      );

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $API_URL);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
      $cyberx_response = array();
      if (!($data = curl_exec($ch))) {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = 'Error in processing payment';
        return $cyberx_response;
      }
      curl_close($ch);
      unset($ch);
      
      $response=json_decode($data,true);
      /* demo response
        {
          "type":"transaction",
          "key":"pnf51mzbtk4v28h",
          "refnum":"3105138425",
          "is_duplicate":"N",
          "result_code":"A",
          "result":"Approved",
          "authcode":"962323",
          "creditcard":{
            "type":"V",
            "number":"4000xxxxxxxx2224",
            "cardholder":"test3",
            "category_code":"A",
            "entry_mode":"Card Not Present, Manually Keyed"
          },
          "invoice":7021606,
          "avs":{"result_code":"YYY","result":"Address: Match & 5 Digit Zip: Match"},
          "cvc":{"result_code":"M","result":"Match"},
          "batch":{"type":"batch","key":"2t1kvz7n57rq8xd","batchrefnum":"414416","sequence":"1"},
          "auth_amount":"145.00",
          "trantype":"Credit Card Sale",
          "receipts":{"customer":"Mail Sent Successfully"}
        }
      */

      /* Result code (
          'A' = Approved, 
          'P' = Partial Approval, 
          'D' = Declined, 
          'E' = Error,  
          'V' = Verification Required
        )
      */

      if (in_array($response['result_code'],array('A','P','V'))) {
        $cyberx_response['status'] = 'Success';
        $cyberx_response['transaction_id'] = $response['refnum'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['txn_id'] = $response['refnum'];
        $cyberx_response['api_status'] = $response['result_code'];
      } else {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = $response['error'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['transaction_id'] = $response['refnum'];
        $cyberx_response['txn_id'] = $response['refnum'];
        $cyberx_response['api_status'] = $response['result_code'];
      }
      return $cyberx_response;
    }
    
    private function process_USAEPAY_payment_ach($params) {
      
      $USER_NAME = $this->APICredentials[$this->APIMode]['user_name'];
      $API_PIN = $this->APICredentials[$this->APIMode]['api_pin'];
      $API_KEY = $this->APICredentials[$this->APIMode]['api_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];

     
      $prehash = $API_KEY . $USER_NAME . $API_PIN;
      $apihash = "s2/" . $USER_NAME . "/" . hash("sha256",$prehash);
      $authKey = base64_encode($API_KEY . ':' . $apihash);
      

      $fields = array(
        'command' => 'check:sale',
        'amount' => $params['amount'],
        'invoice' => $params['order_id'],
        'orderid' => $params['order_id'],
        'description' => $params['description'],
        'email' => $params['email'],
      );
      if(!empty($params['customer_id'])){
        $fields['custid']=$params['customer_id'];
      }
      $fields['check']['accountholder']=$params['name_on_account'];
      $fields['check']['account']=$params['ach_account_number'];
      $fields['check']['routing']=$params['ach_routing_number'];

      $fields['billing_address']['firstname']=!empty($params['firstname']) ? $params['firstname'] : $params['fname'];
      $fields['billing_address']['lastname']=!empty($params['lastname']) ? $params['lastname'] : $params['lname'];
      $fields['billing_address']['street']=$params['address1'];
      $fields['billing_address']['street2']=$params['address2'];
      $fields['billing_address']['city']=$params['city'];
      $fields['billing_address']['state']=$params['state'];
      $fields['billing_address']['postalcode']=$params['zip'];
      $fields['billing_address']['country']='USA';
      $fields['billing_address']['phone']=$params['phone'];

      $fields_string = json_encode($fields);
      
      $header = array(
        'Content-Type: application/json',
        'Authorization: Basic '. $authKey,
      );

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $API_URL);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
      $cyberx_response = array();
      if (!($data = curl_exec($ch))) {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = 'Error in processing payment';
        return $cyberx_response;
      }
      curl_close($ch);
      unset($ch);
      
      $response=json_decode($data,true);

      /* Result code (
          'A' = Approved, 
          'P' = Partial Approval, 
          'D' = Declined, 
          'E' = Error,  
          'V' = Verification Required
        )
      */

      if (in_array($response['result_code'],array('A','P','V'))) {
        $cyberx_response['status'] = 'Success';
        $cyberx_response['transaction_id'] = $response['refnum'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['txn_id'] = $response['refnum'];
        $cyberx_response['api_status'] = $response['result_code'];
      } else {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = $response['error'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['transaction_id'] = $response['refnum'];
        $cyberx_response['txn_id'] = $response['refnum'];
        $cyberx_response['api_status'] = $response['result_code'];
      }
      return $cyberx_response;
    }

    private function process_USAEPAY_refund($params) {
      $USER_NAME = $this->APICredentials[$this->APIMode]['user_name'];
      $API_PIN = $this->APICredentials[$this->APIMode]['api_pin'];
      $API_KEY = $this->APICredentials[$this->APIMode]['api_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];

     
      $prehash = $API_KEY . $USER_NAME . $API_PIN;
      $apihash = "s2/" . $USER_NAME . "/" . hash("sha256",$prehash);
      $authKey = base64_encode($API_KEY . ':' . $apihash);
      

      $fields = array(
        'command' => 'cc:refund',
        'amount' => $params['amount'],
        'refnum' => $params['transaction_id'],
      );

      $fields_string = json_encode($fields);
      $header = array(
        'Content-Type: application/json',
        'Authorization: Basic '. $authKey,
      );

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $API_URL);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
      $cyberx_response = array();
      if (!($data = curl_exec($ch))) {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = 'Error in processing payment';
        return $cyberx_response;
      }
      curl_close($ch);
      unset($ch);
      
      $response=json_decode($data,true);

      /* Result code (
          'A' = Approved, 
          'P' = Partial Approval, 
          'D' = Declined, 
          'E' = Error,  
          'V' = Verification Required
        )
      */

      if (in_array($response['result_code'],array('A','P','V'))) {
        $cyberx_response['status'] = 'Success';
        $cyberx_response['transaction_id'] = $response['refnum'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['txn_id'] = $response['refnum'];
        $cyberx_response['api_status'] = $response['result_code'];
      } else {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = $response['error'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['transaction_id'] = $response['refnum'];
        $cyberx_response['txn_id'] = $response['refnum'];
        $cyberx_response['api_status'] = $response['result_code'];
      }
      return $cyberx_response;
    }

    private function process_USAEPAY_refund_ach($params) {
      $USER_NAME = $this->APICredentials[$this->APIMode]['user_name'];
      $API_PIN = $this->APICredentials[$this->APIMode]['api_pin'];
      $API_KEY = $this->APICredentials[$this->APIMode]['api_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];

     
      $prehash = $API_KEY . $USER_NAME . $API_PIN;
      $apihash = "s2/" . $USER_NAME . "/" . hash("sha256",$prehash);
      $authKey = base64_encode($API_KEY . ':' . $apihash);
      

      $fields = array(
        'command' => 'check:refund',
        'amount' => $params['amount'],
        'refnum' => $params['transaction_id'],
      );

      $fields_string = json_encode($fields);
      $header = array(
        'Content-Type: application/json',
        'Authorization: Basic '. $authKey,
      );

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $API_URL);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
      $cyberx_response = array();
      if (!($data = curl_exec($ch))) {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = 'Error in processing payment';
        return $cyberx_response;
      }
      curl_close($ch);
      unset($ch);
      
      $response=json_decode($data,true);

      /* Result code (
          'A' = Approved, 
          'P' = Partial Approval, 
          'D' = Declined, 
          'E' = Error,  
          'V' = Verification Required
        )
      */

      if (in_array($response['result_code'],array('A','P','V'))) {
        $cyberx_response['status'] = 'Success';
        $cyberx_response['transaction_id'] = $response['refnum'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['txn_id'] = $response['refnum'];
        $cyberx_response['api_status'] = $response['result_code'];
      } else {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = $response['error'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['transaction_id'] = $response['refnum'];
        $cyberx_response['txn_id'] = $response['refnum'];
        $cyberx_response['api_status'] = $response['result_code'];
      }
      return $cyberx_response;
    }

    private function process_USAEPAY_void($params) {
      $USER_NAME = $this->APICredentials[$this->APIMode]['user_name'];
      $API_PIN = $this->APICredentials[$this->APIMode]['api_pin'];
      $API_KEY = $this->APICredentials[$this->APIMode]['api_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];

     
      $prehash = $API_KEY . $USER_NAME . $API_PIN;
      $apihash = "s2/" . $USER_NAME . "/" . hash("sha256",$prehash);
      $authKey = base64_encode($API_KEY . ':' . $apihash);
      

      $fields = array(
        'command' => 'void',
        'refnum' => $params['transaction_id'],
      );

      $fields_string = json_encode($fields);
      $header = array(
        'Content-Type: application/json',
        'Authorization: Basic '. $authKey,
      );

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $API_URL);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
      $cyberx_response = array();
      if (!($data = curl_exec($ch))) {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = 'Error in processing payment';
        return $cyberx_response;
      }
      curl_close($ch);
      unset($ch);
      
      $response=json_decode($data,true);

      /* Result code (
          'A' = Approved, 
          'P' = Partial Approval, 
          'D' = Declined, 
          'E' = Error,  
          'V' = Verification Required
        )
      */

      if (in_array($response['result_code'],array('A','P','V'))) {
        $cyberx_response['status'] = 'Success';
        $cyberx_response['transaction_id'] = $response['refnum'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['txn_id'] = $response['refnum'];
        $cyberx_response['api_status'] = $response['result_code'];
      } else {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = $response['error'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['transaction_id'] = $response['refnum'];
        $cyberx_response['txn_id'] = $response['refnum'];
        $cyberx_response['api_status'] = $response['result_code'];
      }
      return $cyberx_response;
    }

    private function process_USAEPAY_void_ach($params) {
      $USER_NAME = $this->APICredentials[$this->APIMode]['user_name'];
      $API_PIN = $this->APICredentials[$this->APIMode]['api_pin'];
      $API_KEY = $this->APICredentials[$this->APIMode]['api_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];

     
      $prehash = $API_KEY . $USER_NAME . $API_PIN;
      $apihash = "s2/" . $USER_NAME . "/" . hash("sha256",$prehash);
      $authKey = base64_encode($API_KEY . ':' . $apihash);
      

      $fields = array(
        'command' => 'void',
        'refnum' => $params['transaction_id'],
      );

      $fields_string = json_encode($fields);
      $header = array(
        'Content-Type: application/json',
        'Authorization: Basic '. $authKey,
      );

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $API_URL);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
      $cyberx_response = array();
      if (!($data = curl_exec($ch))) {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = 'Error in processing payment';
        return $cyberx_response;
      }
      curl_close($ch);
      unset($ch);
      
      $response=json_decode($data,true);

      /* Result code (
          'A' = Approved, 
          'P' = Partial Approval, 
          'D' = Declined, 
          'E' = Error,  
          'V' = Verification Required
        )
      */

      if (in_array($response['result_code'],array('A','P','V'))) {
        $cyberx_response['status'] = 'Success';
        $cyberx_response['transaction_id'] = $response['refnum'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['txn_id'] = $response['refnum'];
        $cyberx_response['api_status'] = $response['result_code'];
      } else {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = $response['error'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['transaction_id'] = $response['refnum'];
        $cyberx_response['txn_id'] = $response['refnum'];
        $cyberx_response['api_status'] = $response['result_code'];
      }
      return $cyberx_response;
    }

    private function get_USAEPAY_Transaction($params) {

      $USER_NAME = $this->APICredentials[$this->APIMode]['user_name'];
      $API_PIN = $this->APICredentials[$this->APIMode]['api_pin'];
      $API_KEY = $this->APICredentials[$this->APIMode]['api_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];
      $API_URL = $API_URL.'/'.$params['transaction_id'];

      $prehash = $API_KEY . $USER_NAME . $API_PIN;
      $apihash = "s2/" . $USER_NAME . "/" . hash("sha256",$prehash);
      $authKey = base64_encode($API_KEY . ':' . $apihash);
     
      $header = array(
        'Content-Type: application/json',
        'Authorization: Basic '. $authKey,
      );

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $API_URL);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
      $cyberx_response = array();
      if (!($data = curl_exec($ch))) {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = 'Error in processing payment';
        return $cyberx_response;
      }
      curl_close($ch);
      unset($ch);
      
      
      $response=json_decode($data,true);

      /* Result code (
          'A' = Approved, 
          'P' = Partial Approval, 
          'D' = Declined, 
          'E' = Error,  
          'V' = Verification Required
        )

        Transaction Status Code (
          N = New Transaction (has not been processed yet)
          P = Pending (credit cards: batch has not closed / checks: has not been sent to fed yet)
          B = Submitted (checks: sent to fed, void no longer available)
          F = Funded (checks: date the money left the account)
          S = Settled (credit cards: batch has been closed / checks: transaction has cleared)
          E = Error
          V = Voided (checks)
          R = Returned (checks), Authorization Released (credit cards)
          T = Timed Out (checks: no update from processor for 5 days)
          M = On Hold, Manager Approval Required (checks)
          H = On Hold, Pending Processor Review (checks)
        )
      */
      if (in_array($response['result_code'],array('A','P','V'))) {
        //Check For Transaction Status Code
        $api_status = "";
        if(in_array($response['status_code'],array('S'))) {
          $api_status = "settledSuccessfully";
        
        } else if(in_array($response['status_code'],array('N','P','B','F','M','H'))) {
          $api_status = "pendingSettlement";
        
        } else if(in_array($response['status_code'],array('R','T','V','E'))) {
          $api_status = "settlementError";    
        }
      } else {
        $api_status = "";
      }

      if (in_array($api_status,array("settledSuccessfully","pendingSettlement"))) {
        $cyberx_response['status'] = 'Success';
        $cyberx_response['transaction_id'] = $response['refnum'];
        $cyberx_response['txn_id'] = $response['refnum'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['api_status'] = $api_status;
        $cyberx_response['message'] = $response['status'];
      } else {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['transaction_id'] = $response['refnum'];
        $cyberx_response['txn_id'] = $response['refnum'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['api_status'] = $api_status;
        $cyberx_response['message'] = $response['status'];
      }
      return $cyberx_response;
    }

  /*   * ***********************************************
    * USEPAY API code Start
  * *********************************************** */

  /*************************************************
  * PayByCliq API code Start
    Sandbox
    Merchant ID: 10001
    ServiceKey: Tb53JdVf6x4X2GoOw19Mhs0N8FeIr73S
    Amount: 0.01
    CustomerBankAccountNumber: 001
    CustomerBankRoutingNumber: Any Valid Routing Number
  ************************************************ */
    private function process_PayByCliq_payment_ach($params) {
      global $pdo;
      $MERCHANT_ID = $this->APICredentials[$this->APIMode]['merchant_id'];
      $SERVICE_KEY = $this->APICredentials[$this->APIMode]['service_key'];
      $USER_NAME = $this->APICredentials[$this->APIMode]['user_name'];
      $PASSWORD = $this->APICredentials[$this->APIMode]['password'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];
      $API_URL .= "/AchSale"; //https://wswest.cfinc.com/ach/ACHOnlineServices.svc/AchSale

      $state = $pdo->selectOne("SELECT short_name FROM states_c where name = :name", array(':name' => $params['state']));
      if($state){
        $params['state'] = $state['short_name'];
      }
      
      $ach_account_type = ucfirst($params['ach_account_type']);
      if(strtolower($ach_account_type) == "saving") {
          $ach_account_type = "Savings";
      }

      $request_params = "<AchWebSaleRequest xmlns='https://webservices.cfinc.com/ach/data/' xmlns:i='http://www.w3.org/2001/XMLSchema-instance'>
          <MerchantId>".$MERCHANT_ID."</MerchantId>
          <ServiceKey>".$SERVICE_KEY."</ServiceKey>
          <ReferenceId>".$params['order_id']."</ReferenceId>
          <CustomerName>".urlencode($params['name_on_account'])."</CustomerName>
          <TransactionAmount>".$params['amount']."</TransactionAmount>
          <CustomerBankAccountType>".urlencode($ach_account_type)."</CustomerBankAccountType>
          <CustomerBankAccountNumber>".urlencode($params['ach_account_number'])."</CustomerBankAccountNumber>
          <CustomerBankRoutingNumber>".urlencode($params['ach_routing_number'])."</CustomerBankRoutingNumber>
          <CustomerAddress>".$params['address1']."</CustomerAddress>
          <CustomerCity>".$params['city']."</CustomerCity>
          <CustomerState>".$params['state']."</CustomerState>
          <CustomerZipCode>".$params['zip']."</CustomerZipCode>
          <CustomerPhone>".$params['phone']."</CustomerPhone>
          <PaymentRelatedInfo>".$params['description']."</PaymentRelatedInfo>
          <MerchantDescriptor>Family Care Card</MerchantDescriptor>
          <MerchantPhone>+1-800-323-4057</MerchantPhone>
        </AchWebSaleRequest>";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $API_URL);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $request_params);
      curl_setopt($ch, CURLOPT_POST, 1);
      $cyberx_response = array();
      if (!($data = curl_exec($ch))) {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = 'Error in processing payment';
        return $cyberx_response;
      }
      curl_close($ch);
      unset($ch);
      $response = json_decode(json_encode(simplexml_load_string($data)), true);

      if ($response['ReasonCode'] == 'A00') {
        $cyberx_response['status'] = 'Success';
        $cyberx_response['transaction_id'] = $response['TransactionId'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['message'] = $response['Message'];
      } else {
        $response['error_code'] = $response['ReasonCode'];
        $response['error_message'] = $response['Message'];

        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = $response['Message'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['transaction_id'] = $response['TransactionId'];
      }
      return $cyberx_response;
    }

    private function process_PayByCliq_void_ach($params) {
      $MERCHANT_ID = $this->APICredentials[$this->APIMode]['merchant_id'];
      $SERVICE_KEY = $this->APICredentials[$this->APIMode]['service_key'];
      $USER_NAME = $this->APICredentials[$this->APIMode]['user_name'];
      $PASSWORD = $this->APICredentials[$this->APIMode]['password'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];
      $API_URL .= "/AchVoid"; //https://wswest.cfinc.com/ach/ACHOnlineServices.svc/AchVoid

      $params['order_id'] = (isset($params['order_id'])?$params['order_id']:'');

      $request_params = "<AchWebVoidRequest xmlns='https://webservices.cfinc.com/ach/data/' xmlns:i='http://www.w3.org/2001/XMLSchema-instance'>
          <MerchantId>".$MERCHANT_ID."</MerchantId>
          <ServiceKey>".$SERVICE_KEY."</ServiceKey>
          <TransactionId>".urlencode($params['transaction_id'])."</TransactionId>
          <ReferenceId>".urlencode($params['order_id'])."</ReferenceId>
        </AchWebVoidRequest>";

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $API_URL);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $request_params);
      curl_setopt($ch, CURLOPT_POST, 1);
      $cyberx_response = array();
      if (!($data = curl_exec($ch))) {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = 'Error in processing payment';
        return $cyberx_response;
      }
      curl_close($ch);
      unset($ch);
      $response = json_decode(json_encode(simplexml_load_string($data)), true);

      if ($response['ReasonCode'] == 'A00') {
        $cyberx_response['status'] = 'Success';
        $cyberx_response['transaction_id'] = $response['TransactionId'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['message'] = $response['Message'];
      } else {
        $response['error_code'] = $response['ReasonCode'];
        $response['error_message'] = $response['Message'];

        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = $response['Message'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['transaction_id'] = $response['TransactionId'];
      }
      return $cyberx_response;
    }

    private function process_PayByCliq_refund_ach($params) {
      $MERCHANT_ID = $this->APICredentials[$this->APIMode]['merchant_id'];
      $SERVICE_KEY = $this->APICredentials[$this->APIMode]['service_key'];
      $USER_NAME = $this->APICredentials[$this->APIMode]['user_name'];
      $PASSWORD = $this->APICredentials[$this->APIMode]['password'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];
      $API_URL .= "/AchRefund"; //https://wswest.cfinc.com/ach/ACHOnlineServices.svc/AchRefund

      $params['order_id'] = (isset($params['order_id'])?$params['order_id']:'');

      $request_params = "<AchWebRefundRequest xmlns='https://webservices.cfinc.com/ach/data/' xmlns:i='http://www.w3.org/2001/XMLSchema-instance'>
          <MerchantId>".$MERCHANT_ID."</MerchantId>
          <ServiceKey>".$SERVICE_KEY."</ServiceKey>
          <TransactionId>".urlencode($params['transaction_id'])."</TransactionId>
          <ReferenceId>".urlencode($params['order_id'])."</ReferenceId>
          <TransactionAmount>".$params['amount']."</TransactionAmount>
          <PaymentRelatedInfo>".(isset($params['description'])?$params['description']:"")."</PaymentRelatedInfo>
        </AchWebRefundRequest>";
      
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $API_URL);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $request_params);
      curl_setopt($ch, CURLOPT_POST, 1);
      $cyberx_response = array();
      if (!($data = curl_exec($ch))) {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = 'Error in processing payment';
        return $cyberx_response;
      }
      curl_close($ch);
      unset($ch);
      $response = json_decode(json_encode(simplexml_load_string($data)), true);

      if ($response['ReasonCode'] == 'A00') {
        $cyberx_response['status'] = 'Success';
        $cyberx_response['transaction_id'] = $response['TransactionId'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['message'] = $response['Message'];
      } else {
        $response['error_code'] = $response['ReasonCode'];
        $response['error_message'] = $response['Message'];

        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = $response['Message'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['transaction_id'] = $response['TransactionId'];
      }
      return $cyberx_response;
    }

    private function get_PayByCliq_Transaction($params) {
      $MERCHANT_ID = $this->APICredentials[$this->APIMode]['merchant_id'];
      $SERVICE_KEY = $this->APICredentials[$this->APIMode]['service_key'];
      $USER_NAME = $this->APICredentials[$this->APIMode]['user_name'];
      $PASSWORD = $this->APICredentials[$this->APIMode]['password'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];

      $API_URL = "https://gateway.cfinc.com/achapi/CFAchApi.svc?wsdl";

      $request_params = "<TransactionIDSearch xmlns='http://tempuri.org/'>
          <request xmlns:d4p1='http://schemas.datacontract.org/2004/07/CF.AchAPI.Client.Dto' xmlns:i='http://www.w3.org/2001/XMLSchema-instance'>
          <d4p1:TranID>".$params['transaction_id']."</d4p1:TranID>
          </request>
          <merchantId>".$MERCHANT_ID."</merchantId>
          <serviceKey>".$SERVICE_KEY."</serviceKey>
          <groupSearch>false</groupSearch>
          </TransactionIDSearch>";

      $soapClient = new SoapClient($API_URL);
      $error = 0;
      $response = array();
      $faultcode = "";
      $faultstring = "";
      try {
          $args = array(new SoapVar($request_params,XSD_ANYXML)); 
          $response = $soapClient->__call("TransactionIDSearch",$args);
          $response = json_decode(json_encode($response),true);
          if(!empty($response['TransactionIDSearchResult']['TransactionList'])) {
              $response = $response['TransactionIDSearchResult']['TransactionList']['ResponseTrans'];
          }
      } catch (SoapFault $fault) {
          $faultcode = $fault->faultcode;
          $faultstring = $fault->faultstring;
      }

      $api_status = "";
      $message = "";

      if (!empty($response['Status'])) {
        /*
        A => Approved
        S => Settled
        V => Voided
        C => Transmitted to Bank
        R => Rejected
        T => Return
        U => NOC
        N => In Transit
        From @Troy: OP29-608 / Email
        * Our Pending Settlement status if A => Approved
        * Our Payment Approved status if S => Settled, N => In Transit and C => Transmitted to Bank  and U => NOC   
        * Our Payment Returned status if  T => Return 
        * Our Void Status if V => Voided
        * Our Declined Status if R => Rejected
        */
        if(in_array($response['Status'],array('S','N','C','U'))) {
          $api_status = "settledSuccessfully";
        
        } else if(in_array($response['Status'],array('A'))) {
          $api_status = "pendingSettlement";
        
        } else if(in_array($response['Status'],array('T','V','R'))) {
          $api_status = "settlementError";    
        }

        $cyberx_response['status'] = 'Success';
        $cyberx_response['transaction_id'] = $response['TransactionID'];
        $cyberx_response['txn_id'] = $response['TransactionID'];
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = $response;
        $cyberx_response['api_status'] = $api_status;
        $cyberx_response['message'] = $message;
      } else {
        $cyberx_response['status'] = 'Fail';
        $cyberx_response['message'] = $message;
        $cyberx_response['API_Type'] = $this->APIType;
        $cyberx_response['API_Mode'] = $this->APIMode;
        $cyberx_response['API_response'] = array("success" => false,"faultcode"=>$faultcode,"faultstring"=>$faultstring,'response' => $response);
      }
      $cyberx_response['MERCHANT_ID'] = $MERCHANT_ID;
      $cyberx_response['SERVICE_KEY'] = $SERVICE_KEY;
      return $cyberx_response;
    }
  /*************************************************
  * PayByCliq API code End
  ************************************************ */

 /*************************************************
  * CyberSource Payment API code START
  ************************************************ */
    private function get_CyberSource_card_code($card_type) {
        $card_codes = array(
            "Amex" => "003",
            "Discover" => "004",
            "MasterCard" => "002",
            "Visa" => "001",
        );
        return (isset($card_codes[$card_type])?$card_codes[$card_type]:"");
    }

    private function process_CyberSource_payment($params) {
      global $allStateShortName,$pdo;
      $MERCHANT_ID = $this->APICredentials[$this->APIMode]['merchant_id'];
      $API_KEY = $this->APICredentials[$this->APIMode]['api_key'];
      $SECRET_KEY = $this->APICredentials[$this->APIMode]['secret_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];
      $API_URL = str_replace("https://","",$API_URL);

      include_once dirname(__DIR__) . '/libs/cybersource-rest-samples-php-master/vendor/autoload.php';
      include_once dirname(__DIR__) . '/libs/cybersource-rest-samples-php-master/Resources/ExternalConfiguration.php';
      
      if(isset($allStateShortName[$params['state']])) {
          $params['state'] = $allStateShortName[$params['state']];
      } else {
          if(!empty($params['zip'])) {
              $sql="SELECT z.state_code FROM zip_code z 
                      JOIN states_c s ON (s.short_name = z.state_code)
                      WHERE s.country_id=231 AND z.zip_code=:zip_code";
              $row=$pdo->selectOne($sql,array(":zip_code" => $params['zip']));
              if(!empty($row['state_code'])) {
                  $params['state'] = $row['state_code'];
              }
          }
      }
      if(isset($params['cvv'])) {
          $params['cvv'] = trim($params['cvv']);
      }
      $clientReferenceInformationArr = array(
          "code" => $params['order_id'],
      );
      if (self::hasValue($params['description'])) {
          $clientReferenceInformationArr['comments'] = $params['description'];
      }

      $clientReferenceInformation = new CyberSource\Model\Ptsv2paymentsClientReferenceInformation($clientReferenceInformationArr);

      $authorizationOptionsInfo = array(
          "ignoreAvsResult" => "true",
          "declineAvsFlags" => "F,G,I,L,P,T,V"
      );
      $authorizationOptions = new CyberSource\Model\Ptsv2paymentsProcessingInformationAuthorizationOptions($authorizationOptionsInfo);
      $processingInformationArr = array(
          "capture" => "true",
          "authorizationOptions" => $authorizationOptions
      );
      $processingInformation = new CyberSource\Model\Ptsv2paymentsProcessingInformation($processingInformationArr);

      if ($this->APIMode == 'sandbox') {
          $paymentInformationCardArr = array(
              "number" => "4111111111111111",
              "expirationMonth" => "12",
              "expirationYear" => "2031",
              "type" => "001"
          );
      } else {
          $paymentInformationCardArr = array(
              "number" => $params['ccnumber'],
              "expirationMonth" => substr($params['ccexp'],0,2),
              "expirationYear" => "20".substr($params['ccexp'],2,2),
              "type" => $this->get_CyberSource_card_code($params['card_type']),
          );
          if(isset($params['cvv'])) {
            $paymentInformationCardArr['securityCode'] = $params['cvv'];
          }
      }
      $paymentInformationCard = new CyberSource\Model\Ptsv2paymentsPaymentInformationCard($paymentInformationCardArr);

      $paymentInformationArr = array(
          "card" => $paymentInformationCard
      );
      $paymentInformation = new CyberSource\Model\Ptsv2paymentsPaymentInformation($paymentInformationArr);

      $orderInformationAmountDetailsArr = array(
          "totalAmount" => number_format($params['amount'], 2, '.', '') . "",
          "currency" => "USD"
      );
      $orderInformationAmountDetails = new CyberSource\Model\Ptsv2paymentsOrderInformationAmountDetails($orderInformationAmountDetailsArr);

      $orderInformationBillToArr = array(
          "firstName" => (!empty($params['firstname']) ? $params['firstname'] : (isset($params['fname'])?$params['fname']:'')),
          "lastName" => (!empty($params['lastname']) ? $params['lastname'] : (isset($params['lname'])?$params['lname']:'')),
          "address1" => (isset($params['address1'])?$params['address1']:''),
          "address2" => (isset($params['address2'])?$params['address2']:''),
          "locality" => (isset($params['city'])?$params['city']:''),
          "administrativeArea" => (isset($params['state'])?$params['state']:''),
          "postalCode" => (isset($params['zip'])?$params['zip']:''),
          "country" => "US",
          "email" => (isset($params['email'])?$params['email']:''),
          "phoneNumber" => (isset($params['phone'])?$params['phone']:'')
      );
      if(empty($orderInformationBillToArr['lastName'])) {
          $tmp_name = explode(' ',$orderInformationBillToArr['firstName']);
          if(!empty($tmp_name[0])) {
              $orderInformationBillToArr['firstName'] = $tmp_name[0];
          }
          if(!empty($tmp_name[1])) {
              $orderInformationBillToArr['lastName'] = $tmp_name[1];
          } else {
              $orderInformationBillToArr['lastName'] = $orderInformationBillToArr['firstName'];
          }
      }
      $orderInformationBillTo = new CyberSource\Model\Ptsv2paymentsOrderInformationBillTo($orderInformationBillToArr);

      $orderInformationArr = array(
          "amountDetails" => $orderInformationAmountDetails,
          "billTo" => $orderInformationBillTo
      );
      $orderInformation = new CyberSource\Model\Ptsv2paymentsOrderInformation($orderInformationArr);

      $requestObjArr = array(
          "clientReferenceInformation" => $clientReferenceInformation,
          "processingInformation" => $processingInformation,
          "paymentInformation" => $paymentInformation,
          "orderInformation" => $orderInformation
      );
      $requestObj = new CyberSource\Model\CreatePaymentRequest($requestObjArr);

      $merchant_cred = array(
          'merchantID' => $MERCHANT_ID,
          'apiKeyID' => $API_KEY,
          'secretKey' => $SECRET_KEY,
          'runEnv' => $API_URL,
      );
      $commonElement = new CyberSource\ExternalConfiguration($merchant_cred);
      $config = $commonElement->ConnectionHost();
      $merchantConfig = $commonElement->merchantConfigObject();

      $api_client = new CyberSource\ApiClient($config, $merchantConfig);
      $api_instance = new CyberSource\Api\PaymentsApi($api_client);

      $payResArr = array();
      if($this->APIMode == 'sandbox' && $params['ccnumber'] == '4111111111111113'){
        $payResArr['status'] = "Fail";
        $payResArr['error_code'] = 'MANUAL_DECLINED';
        $payResArr['error_message'] = 'Transaction Declined';
        $payResArr['txn_id'] = 0;
      } else {
        try {
          $apiResponse = $api_instance->createPayment($requestObj);
          $resObj = $apiResponse[0];
          $errorInformation = $apiResponse[0]->getErrorInformation();
          $tran = $apiResponse[0]->getId();
          if(!empty($errorInformation)) {
            $payResArr['status'] = "Fail";
            $payResArr['error_code'] = $errorInformation->getReason();
            $payResArr['error_message'] = $errorInformation->getMessage();
            $payResArr['txn_id'] = $resObj->getId();
          } else {
            if($resObj->getStatus() == "AUTHORIZED") {
              $payResArr['status'] = "Success";
              $payResArr['txn_id'] = $resObj->getId();
              $payResArr['txn_status'] = $resObj->getStatus();
              $payResArr['txn_submitTimeUtc'] = $resObj->getSubmitTimeUtc();
            } else {
              $payResArr['status'] = "Fail";
              $payResArr['error_code'] = $resObj->getStatus();
              $payResArr['error_message'] = 'Transaction Declined';
              $payResArr['txn_id'] = $resObj->getId();
              $payResArr['txn_status'] = $resObj->getStatus();
              $payResArr['txn_submitTimeUtc'] = $resObj->getSubmitTimeUtc();
            }
          }
        } catch (Cybersource\ApiException $e) {
          $apiResponse = $e->getResponseBody();
          $payResArr = json_decode(json_encode($apiResponse),true);
          $payResArr['status'] = "Fail";
          $payResArr['error_code'] = (isset($apiResponse->reason)?$apiResponse->reason:'');
          $payResArr['error_message'] = (isset($apiResponse->message)?$apiResponse->message:'');
          $payResArr['txn_id'] = (isset($apiResponse->id)?$apiResponse->id:'');
        }
      }
      $cyberx_response = array();
      $cyberx_response['status'] = $payResArr['status'];
      $cyberx_response['transaction_id'] = isset($payResArr['txn_id']) ? $payResArr['txn_id'] : '';
      $cyberx_response['message'] = isset($payResArr['error_message']) ? $payResArr['error_message'] : '';
      $cyberx_response['API_Type'] = $this->APIType;
      $cyberx_response['API_Mode'] = $this->APIMode;
      $cyberx_response['API_response'] = $payResArr;
      return $cyberx_response;
    }

    private function process_CyberSource_payment_ach($params) {
      global $allStateShortName,$pdo;
      $MERCHANT_ID = $this->APICredentials[$this->APIMode]['merchant_id'];
      $API_KEY = $this->APICredentials[$this->APIMode]['api_key'];
      $SECRET_KEY = $this->APICredentials[$this->APIMode]['secret_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];
      $API_URL = str_replace("https://","",$API_URL);

      include_once dirname(__DIR__) . '/libs/cybersource-rest-samples-php-master/vendor/autoload.php';
      include_once dirname(__DIR__) . '/libs/cybersource-rest-samples-php-master/Resources/ExternalConfiguration.php';
      
      if(isset($allStateShortName[$params['state']])) {
          $params['state'] = $allStateShortName[$params['state']];
      } else {
          if(!empty($params['zip'])) {
              $sql="SELECT z.state_code FROM zip_code z 
                      JOIN states_c s ON (s.short_name = z.state_code)
                      WHERE s.country_id=231 AND z.zip_code=:zip_code";
              $row=$pdo->selectOne($sql,array(":zip_code" => $params['zip']));
              if(!empty($row['state_code'])) {
                  $params['state'] = $row['state_code'];
              }
          }
      }

      $clientReferenceInformationArr = array(
          "code" => $params['order_id'],
      );
      if (self::hasValue($params['description'])) {
          $clientReferenceInformationArr['comments'] = $params['description'];
      }
      $clientReferenceInformation = new CyberSource\Model\Ptsv2paymentsClientReferenceInformation($clientReferenceInformationArr);

      $authorizationOptionsInfo = array(
          "ignoreAvsResult" => "true",
          "declineAvsFlags" => "F,G,I,L,P,T,V"
      );
      $authorizationOptions = new CyberSource\Model\Ptsv2paymentsProcessingInformationAuthorizationOptions($authorizationOptionsInfo);
      $processingInformationArr = array(
          "capture" => "true",
          "authorizationOptions" => $authorizationOptions
      );
      $processingInformation = new CyberSource\Model\Ptsv2paymentsProcessingInformation($processingInformationArr);

      if ($this->APIMode == 'sandbox') {
          $account_type = "Checking";
          $account_number = "4100";
          $routing_number = "071923284";
      } else {
          $account_type = $params['ach_account_type'];
          $account_number = $params['ach_account_number'];
          $routing_number = $params['ach_routing_number'];
      }
      
      if(strtolower($account_type) == "saving") {
          $account_type = "S";
      } else {
          $account_type = "C";
      }

      $paymentInformationBankAccountArr = [
          "type" => $account_type, //C=Checking / S=Savings
          "number" => $account_number //Account number
      ];
      $paymentInformationBankAccount = new CyberSource\Model\Ptsv2paymentsPaymentInformationBankAccount($paymentInformationBankAccountArr);

      $paymentInformationBankArr = [
          "account" => $paymentInformationBankAccount,
          "routingNumber" => $routing_number
      ];
      $paymentInformationBank = new CyberSource\Model\Ptsv2paymentsPaymentInformationBank($paymentInformationBankArr);

      $paymentInformationPaymentTypeArr = [
          "name" => "CHECK"
      ];
      $paymentInformationPaymentType = new CyberSource\Model\Ptsv2paymentsPaymentInformationPaymentType($paymentInformationPaymentTypeArr);

      $paymentInformationArr = [
          "bank" => $paymentInformationBank,
          "paymentType" => $paymentInformationPaymentType
      ];
      $paymentInformation = new CyberSource\Model\Ptsv2paymentsPaymentInformation($paymentInformationArr);

      $orderInformationAmountDetailsArr = array(
          "totalAmount" => number_format($params['amount'], 2, '.', '') . "",
          "currency" => "USD"
      );
      $orderInformationAmountDetails = new CyberSource\Model\Ptsv2paymentsOrderInformationAmountDetails($orderInformationAmountDetailsArr);

      $orderInformationBillToArr = array(
          "firstName" => (!empty($params['firstname']) ? $params['firstname'] : (isset($params['fname'])?$params['fname']:'')),
          "lastName" => (!empty($params['lastname']) ? $params['lastname'] : (isset($params['lname'])?$params['lname']:'')),
          "address1" => (isset($params['address1'])?$params['address1']:''),
          "address2" => (isset($params['address2'])?$params['address2']:''),
          "locality" => (isset($params['city'])?$params['city']:''),
          "administrativeArea" => (isset($params['state'])?$params['state']:''),
          "postalCode" => (isset($params['zip'])?$params['zip']:''),
          "country" => "US",
          "email" => (isset($params['email'])?$params['email']:''),
          "phoneNumber" => (isset($params['phone'])?$params['phone']:'')
      );
      if(empty($orderInformationBillToArr['lastName'])) {
          $tmp_name = explode(' ',$orderInformationBillToArr['firstName']);
          if(!empty($tmp_name[0])) {
              $orderInformationBillToArr['firstName'] = $tmp_name[0];
          }
          if(!empty($tmp_name[1])) {
              $orderInformationBillToArr['lastName'] = $tmp_name[1];
          } else {
              $orderInformationBillToArr['lastName'] = $orderInformationBillToArr['firstName'];
          }
      }
      $orderInformationBillTo = new CyberSource\Model\Ptsv2paymentsOrderInformationBillTo($orderInformationBillToArr);

      $orderInformationArr = array(
          "amountDetails" => $orderInformationAmountDetails,
          "billTo" => $orderInformationBillTo
      );
      $orderInformation = new CyberSource\Model\Ptsv2paymentsOrderInformation($orderInformationArr);

      $requestObjArr = [
          "clientReferenceInformation" => $clientReferenceInformation,
          "processingInformation" => $processingInformation,
          "paymentInformation" => $paymentInformation,
          "orderInformation" => $orderInformation
      ];
      $requestObj = new CyberSource\Model\CreatePaymentRequest($requestObjArr);

      $merchant_cred = array(
          'merchantID' => $MERCHANT_ID,
          'apiKeyID' => $API_KEY,
          'secretKey' => $SECRET_KEY,
          'runEnv' => $API_URL,
      );
      $commonElement = new CyberSource\ExternalConfiguration($merchant_cred);
      $config = $commonElement->ConnectionHost();
      $merchantConfig = $commonElement->merchantConfigObject();

      $api_client = new CyberSource\ApiClient($config, $merchantConfig);
      $api_instance = new CyberSource\Api\PaymentsApi($api_client);

      $payResArr = array();
      try {
        $apiResponse = $api_instance->createPayment($requestObj);
        $resObj = $apiResponse[0];
        $errorInformation = $apiResponse[0]->getErrorInformation();
        $tran = $apiResponse[0]->getId();
        if(!empty($errorInformation)) {
          $payResArr['status'] = "Fail";
          $payResArr['error_code'] = $errorInformation->getReason();
          $payResArr['error_message'] = $errorInformation->getMessage();
          $payResArr['txn_id'] = $resObj->getId();
        } else {
          if($resObj->getStatus() == "AUTHORIZED") {
            $payResArr['status'] = "Success";
            $payResArr['txn_id'] = $resObj->getId();
            $payResArr['txn_status'] = $resObj->getStatus();
            $payResArr['txn_submitTimeUtc'] = $resObj->getSubmitTimeUtc();
          } else {
            $payResArr['status'] = "Fail";
            $payResArr['error_code'] = $resObj->getStatus();
            $payResArr['error_message'] = 'Transaction Declined';
            $payResArr['txn_id'] = $resObj->getId();
            $payResArr['txn_status'] = $resObj->getStatus();
            $payResArr['txn_submitTimeUtc'] = $resObj->getSubmitTimeUtc();
          }
        }
      } catch (Cybersource\ApiException $e) {
        $apiResponse = $e->getResponseBody();
        $payResArr = json_decode(json_encode($apiResponse),true);
        $payResArr['status'] = "Fail";
        $payResArr['error_code'] = (isset($apiResponse->reason)?$apiResponse->reason:'');
        $payResArr['error_message'] = (isset($apiResponse->message)?$apiResponse->message:'');
        $payResArr['txn_id'] = (isset($apiResponse->id)?$apiResponse->id:'');
      }
      $cyberx_response = array();
      $cyberx_response['status'] = $payResArr['status'];
      $cyberx_response['transaction_id'] = isset($payResArr['txn_id']) ? $payResArr['txn_id'] : '';
      $cyberx_response['message'] = isset($payResArr['error_message']) ? $payResArr['error_message'] : '';
      $cyberx_response['API_Type'] = $this->APIType;
      $cyberx_response['API_Mode'] = $this->APIMode;
      $cyberx_response['API_response'] = $payResArr;
      return $cyberx_response;
    }

    private function process_CyberSource_refund($params) {
      $MERCHANT_ID = $this->APICredentials[$this->APIMode]['merchant_id'];
      $API_KEY = $this->APICredentials[$this->APIMode]['api_key'];
      $SECRET_KEY = $this->APICredentials[$this->APIMode]['secret_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];
      $API_URL = str_replace("https://","",$API_URL);

      include_once dirname(__DIR__) . '/libs/cybersource-rest-samples-php-master/vendor/autoload.php';
      include_once dirname(__DIR__) . '/libs/cybersource-rest-samples-php-master/Resources/ExternalConfiguration.php';

      //$params['transaction_id'] = SimpleAuthorizationInternet("true")[0]['id'];

      $clientReferenceInformationArr = array(
          "code" => 'refund_' .$params['order_id'],
      );
      if (self::hasValue($params['description'])) {
          $clientReferenceInformationArr['comments'] = $params['description'];
      }
      $clientReferenceInformation = new CyberSource\Model\Ptsv2paymentsClientReferenceInformation($clientReferenceInformationArr);

      $orderInformationAmountDetailsArr = array(
          "totalAmount" => number_format($params['amount'], 2, '.', '') . "",
          "currency" => "USD"
      );
      $orderInformationAmountDetails = new CyberSource\Model\Ptsv2paymentsidcapturesOrderInformationAmountDetails($orderInformationAmountDetailsArr);

      $orderInformationArr = array(
          "amountDetails" => $orderInformationAmountDetails,
      );
      $orderInformation = new CyberSource\Model\Ptsv2paymentsidrefundsOrderInformation($orderInformationArr);

      $requestObjArr = array(
          "clientReferenceInformation" => $clientReferenceInformation,
          "orderInformation" => $orderInformation
      );
      $requestObj = new CyberSource\Model\RefundPaymentRequest($requestObjArr);

      $merchant_cred = array(
          'merchantID' => $MERCHANT_ID,
          'apiKeyID' => $API_KEY,
          'secretKey' => $SECRET_KEY,
          'runEnv' => $API_URL,
      );
      $commonElement = new CyberSource\ExternalConfiguration($merchant_cred);
      $config = $commonElement->ConnectionHost();
      $merchantConfig = $commonElement->merchantConfigObject();

      $api_client = new CyberSource\ApiClient($config, $merchantConfig);
      $api_instance = new CyberSource\Api\RefundApi($api_client);

      $payResArr = array();
      try {
        $apiResponse = $api_instance->refundPayment($requestObj, $params['transaction_id']);
        $resObj = $apiResponse[0];
        $payResArr['status'] = "Success";
        $payResArr['txn_id'] = $resObj->getId();
        $payResArr['txn_status'] = $resObj->getStatus();
      } catch (Cybersource\ApiException $e) {
        $apiResponse = $e->getResponseBody();
        $payResArr = json_decode(json_encode($apiResponse),true);
        $payResArr['status'] = "Fail";
        $payResArr['error_code'] = (isset($apiResponse->reason)?$apiResponse->reason:'');
        $payResArr['error_message'] = (isset($apiResponse->message)?$apiResponse->message:'');
        $payResArr['txn_id'] = (isset($apiResponse->id)?$apiResponse->id:'');
      }
      $cyberx_response = array();
      $cyberx_response['status'] = $payResArr['status'];
      $cyberx_response['transaction_id'] = isset($payResArr['txn_id']) ? $payResArr['txn_id'] : '';
      $cyberx_response['message'] = isset($payResArr['error_message']) ? $payResArr['error_message'] : '';
      $cyberx_response['API_Type'] = $this->APIType;
      $cyberx_response['API_Mode'] = $this->APIMode;
      $cyberx_response['API_response'] = $payResArr;
      return $cyberx_response;
    }

    private function process_CyberSource_refund_ach($params) {
      $MERCHANT_ID = $this->APICredentials[$this->APIMode]['merchant_id'];
      $API_KEY = $this->APICredentials[$this->APIMode]['api_key'];
      $SECRET_KEY = $this->APICredentials[$this->APIMode]['secret_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];
      $API_URL = str_replace("https://","",$API_URL);

      include_once dirname(__DIR__) . '/libs/cybersource-rest-samples-php-master/vendor/autoload.php';
      include_once dirname(__DIR__) . '/libs/cybersource-rest-samples-php-master/Resources/ExternalConfiguration.php';

      $clientReferenceInformationArr = array(
          "code" => 'refund_' .$params['order_id'],
      );
      if (self::hasValue($params['description'])) {
          $clientReferenceInformationArr['comments'] = $params['description'];
      }
      $clientReferenceInformation = new CyberSource\Model\Ptsv2paymentsClientReferenceInformation($clientReferenceInformationArr);

      $paymentInformationPaymentTypeArr = array(
          "name" => "CHECK"
      );
      $paymentInformationPaymentType = new CyberSource\Model\Ptsv2paymentsPaymentInformationPaymentType($paymentInformationPaymentTypeArr);

      $paymentInformationArr = array(
          "paymentType" => $paymentInformationPaymentType
      );
      $paymentInformation = new CyberSource\Model\Ptsv2paymentsidrefundsPaymentInformation($paymentInformationArr);

      $orderInformationAmountDetailsArr = array(
          "totalAmount" => number_format($params['amount'], 2, '.', '') . "",
          "currency" => "USD"
      );
      $orderInformationAmountDetails = new CyberSource\Model\Ptsv2paymentsidcapturesOrderInformationAmountDetails($orderInformationAmountDetailsArr);

      $orderInformationArr = array(
          "amountDetails" => $orderInformationAmountDetails
      );
      $orderInformation = new CyberSource\Model\Ptsv2paymentsidrefundsOrderInformation($orderInformationArr);

      $requestObjArr = array(
          "clientReferenceInformation" => $clientReferenceInformation,
          "paymentInformation" => $paymentInformation,
          "orderInformation" => $orderInformation
      );
      $requestObj = new CyberSource\Model\RefundPaymentRequest($requestObjArr);

      $merchant_cred = array(
          'merchantID' => $MERCHANT_ID,
          'apiKeyID' => $API_KEY,
          'secretKey' => $SECRET_KEY,
          'runEnv' => $API_URL,
      );
      $commonElement = new CyberSource\ExternalConfiguration($merchant_cred);
      $config = $commonElement->ConnectionHost();
      $merchantConfig = $commonElement->merchantConfigObject();

      $api_client = new CyberSource\ApiClient($config, $merchantConfig);
      $api_instance = new CyberSource\Api\RefundApi($api_client);


      $payResArr = array();
      try {
        $apiResponse = $api_instance->refundPayment($requestObj, $params['transaction_id']);
        $resObj = $apiResponse[0];
        $payResArr['status'] = "Success";
        $payResArr['txn_id'] = $resObj->getId();
        $payResArr['txn_status'] = $resObj->getStatus();
      } catch (Cybersource\ApiException $e) {
        $apiResponse = $e->getResponseBody();
        $payResArr = json_decode(json_encode($apiResponse),true);
        $payResArr['status'] = "Fail";
        $payResArr['error_code'] = (isset($apiResponse->reason)?$apiResponse->reason:'');
        $payResArr['error_message'] = (isset($apiResponse->message)?$apiResponse->message:'');
        $payResArr['txn_id'] = (isset($apiResponse->id)?$apiResponse->id:'');
      }
      $cyberx_response = array();
      $cyberx_response['status'] = $payResArr['status'];
      $cyberx_response['transaction_id'] = isset($payResArr['txn_id']) ? $payResArr['txn_id'] : '';
      $cyberx_response['message'] = isset($payResArr['error_message']) ? $payResArr['error_message'] : '';
      $cyberx_response['API_Type'] = $this->APIType;
      $cyberx_response['API_Mode'] = $this->APIMode;
      $cyberx_response['API_response'] = $payResArr;
      return $cyberx_response;
    }

    private function process_CyberSource_void($params) {
      $MERCHANT_ID = $this->APICredentials[$this->APIMode]['merchant_id'];
      $API_KEY = $this->APICredentials[$this->APIMode]['api_key'];
      $SECRET_KEY = $this->APICredentials[$this->APIMode]['secret_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];
      $API_URL = str_replace("https://","",$API_URL);

      include_once dirname(__DIR__) . '/libs/cybersource-rest-samples-php-master/vendor/autoload.php';
      include_once dirname(__DIR__) . '/libs/cybersource-rest-samples-php-master/Resources/ExternalConfiguration.php';

      $clientReferenceInformationArr = array(
          "code" => "void_".$params['order_id']
      );
      $clientReferenceInformation = new CyberSource\Model\Ptsv2paymentsidreversalsClientReferenceInformation($clientReferenceInformationArr);

      $requestObjArr = [
          "clientReferenceInformation" => $clientReferenceInformation
      ];
      $requestObj = new CyberSource\Model\VoidPaymentRequest($requestObjArr);

      $merchant_cred = array(
          'merchantID' => $MERCHANT_ID,
          'apiKeyID' => $API_KEY,
          'secretKey' => $SECRET_KEY,
          'runEnv' => $API_URL,
      );
      $commonElement = new CyberSource\ExternalConfiguration($merchant_cred);
      $config = $commonElement->ConnectionHost();
      $merchantConfig = $commonElement->merchantConfigObject();

      $api_client = new CyberSource\ApiClient($config, $merchantConfig);
      $api_instance = new CyberSource\Api\VoidApi($api_client);

      $payResArr = array();
      try {
        $apiResponse = $api_instance->voidPayment($requestObj, $params['transaction_id']);
        $resObj = $apiResponse[0];
        $payResArr['status'] = "Success";
        $payResArr['txn_id'] = $resObj->getId();
        $payResArr['txn_status'] = $resObj->getStatus();
      } catch (Cybersource\ApiException $e) {
        $apiResponse = $e->getResponseBody();
        $payResArr = json_decode(json_encode($apiResponse),true);
        $payResArr['status'] = "Fail";
        $payResArr['error_code'] = (isset($apiResponse->reason)?$apiResponse->reason:'');
        $payResArr['error_message'] = (isset($apiResponse->message)?$apiResponse->message:'');
        $payResArr['txn_id'] = (isset($apiResponse->id)?$apiResponse->id:'');
      }
      $cyberx_response = array();
      $cyberx_response['status'] = $payResArr['status'];
      $cyberx_response['transaction_id'] = isset($payResArr['txn_id']) ? $payResArr['txn_id'] : '';
      $cyberx_response['message'] = isset($payResArr['error_message']) ? $payResArr['error_message'] : '';
      $cyberx_response['API_Type'] = $this->APIType;
      $cyberx_response['API_Mode'] = $this->APIMode;
      $cyberx_response['API_response'] = $payResArr;
      return $cyberx_response;
    }

    private function process_CyberSource_void_ach($params) {
      $MERCHANT_ID = $this->APICredentials[$this->APIMode]['merchant_id'];
      $API_KEY = $this->APICredentials[$this->APIMode]['api_key'];
      $SECRET_KEY = $this->APICredentials[$this->APIMode]['secret_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];
      $API_URL = str_replace("https://","",$API_URL);

      include_once dirname(__DIR__) . '/libs/cybersource-rest-samples-php-master/vendor/autoload.php';
      include_once dirname(__DIR__) . '/libs/cybersource-rest-samples-php-master/Resources/ExternalConfiguration.php';

      $clientReferenceInformationArr = array(
          "code" => "void_".$params['order_id']
      );
      $clientReferenceInformation = new CyberSource\Model\Ptsv2paymentsidreversalsClientReferenceInformation($clientReferenceInformationArr);

      $requestObjArr = [
          "clientReferenceInformation" => $clientReferenceInformation
      ];
      $requestObj = new CyberSource\Model\VoidPaymentRequest($requestObjArr);

      $merchant_cred = array(
          'merchantID' => $MERCHANT_ID,
          'apiKeyID' => $API_KEY,
          'secretKey' => $SECRET_KEY,
          'runEnv' => $API_URL,
      );
      $commonElement = new CyberSource\ExternalConfiguration($merchant_cred);
      $config = $commonElement->ConnectionHost();
      $merchantConfig = $commonElement->merchantConfigObject();

      $api_client = new CyberSource\ApiClient($config, $merchantConfig);
      $api_instance = new CyberSource\Api\VoidApi($api_client);

      $payResArr = array();
      try {
        $apiResponse = $api_instance->voidPayment($requestObj, $params['transaction_id']);
        $resObj = $apiResponse[0];
        $payResArr['status'] = "Success";
        $payResArr['txn_id'] = $resObj->getId();
        $payResArr['txn_status'] = $resObj->getStatus();
      } catch (Cybersource\ApiException $e) {
        $apiResponse = $e->getResponseBody();
        $payResArr = json_decode(json_encode($apiResponse),true);
        $payResArr['status'] = "Fail";
        $payResArr['error_code'] = (isset($apiResponse->reason)?$apiResponse->reason:'');
        $payResArr['error_message'] = (isset($apiResponse->message)?$apiResponse->message:'');
        $payResArr['txn_id'] = (isset($apiResponse->id)?$apiResponse->id:'');
      }
      $cyberx_response = array();
      $cyberx_response['status'] = $payResArr['status'];
      $cyberx_response['transaction_id'] = isset($payResArr['txn_id']) ? $payResArr['txn_id'] : '';
      $cyberx_response['message'] = isset($payResArr['error_message']) ? $payResArr['error_message'] : '';
      $cyberx_response['API_Type'] = $this->APIType;
      $cyberx_response['API_Mode'] = $this->APIMode;
      $cyberx_response['API_response'] = $payResArr;
      return $cyberx_response;
    }

    private function get_CyberSource_Transaction($params) {
      $MERCHANT_ID = $this->APICredentials[$this->APIMode]['merchant_id'];
      $API_KEY = $this->APICredentials[$this->APIMode]['api_key'];
      $SECRET_KEY = $this->APICredentials[$this->APIMode]['secret_key'];
      $API_URL = $this->APICredentials[$this->APIMode]['url'];
      $API_URL = str_replace("https://","",$API_URL);

      include_once dirname(__DIR__) . '/libs/cybersource-rest-samples-php-master/vendor/autoload.php';
      include_once dirname(__DIR__) . '/libs/cybersource-rest-samples-php-master/Resources/ExternalConfiguration.php';
      $merchant_cred = array(
          'merchantID' => $MERCHANT_ID,
          'apiKeyID' => $API_KEY,
          'secretKey' => $SECRET_KEY,
          'runEnv' => $API_URL,
      );
      $commonElement = new CyberSource\ExternalConfiguration($merchant_cred);
      $config = $commonElement->ConnectionHost();
      $merchantConfig = $commonElement->merchantConfigObject();

      $api_client = new CyberSource\ApiClient($config, $merchantConfig);
      $api_instance = new CyberSource\Api\TransactionDetailsApi($api_client);

      $payResArr = array();
      $api_status = "pendingSettlement";
      try {
        $apiResponse = $api_instance->getTransaction($params['transaction_id']);
        $resObj = $apiResponse[0];
        $applicationInformation = $apiResponse[0]->getApplicationInformation();
        if(!empty($applicationInformation['status'])) {
          if(in_array($applicationInformation['status'],array("PENDING"))) {
            $api_status = "pendingSettlement";

          } else if(in_array($applicationInformation['status'],array("PAYMENT","TRANSMITTED"))) {
            $api_status = "settledSuccessfully";

          } else if(in_array($applicationInformation['status'],array("CANCELLED","ERROR"))) {
            $api_status = "settlementError";
          }
        }
        $payResArr['status'] = "Success";
        $payResArr['txn_id'] = $params['transaction_id'];
        $payResArr['applicationInformation'] = $applicationInformation;
      } catch (Cybersource\ApiException $e) {
        $apiResponse = $e->getResponseBody();
        $payResArr = json_decode(json_encode($apiResponse),true);
        $payResArr['status'] = "Fail";
        $payResArr['error_code'] = (isset($apiResponse->reason)?$apiResponse->reason:'');
        $payResArr['error_message'] = (isset($apiResponse->message)?$apiResponse->message:'');
        $payResArr['txn_id'] = (isset($apiResponse->id)?$apiResponse->id:'');
      }
      $cyberx_response = array();
      $cyberx_response['status'] = $payResArr['status'];
      $cyberx_response['api_status'] = $api_status;
      $cyberx_response['transaction_id'] = isset($payResArr['txn_id']) ? $payResArr['txn_id'] : '';
      $cyberx_response['message'] = isset($payResArr['error_message']) ? $payResArr['error_message'] : '';
      $cyberx_response['API_Type'] = $this->APIType;
      $cyberx_response['API_Mode'] = $this->APIMode;
      $cyberx_response['API_response'] = $payResArr;
      return $cyberx_response;
    }
 /*************************************************
  * CyberSource Payment API code END
  ************************************************ */
}

?>