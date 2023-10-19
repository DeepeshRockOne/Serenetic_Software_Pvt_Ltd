<?php

/**
 * This class will perform insert, update, delete and select sql statment with PDO.
 * 
 * @author Ritesh Patadiya <ritesh@cyberxllc.com>
 */
class PdoOpt {

  public $dbh;

  public function __construct() {

    global $DBSERVER, $DATABASENAME, $LOG_DB, $STATS_DB, $WEBIM_DB, $USERNAME, $PASSWORD;
    $CHAR_SET='utf8';
    $DB_PORT=3306;
    try {
      $this->dbh = new PDO("mysql:host={$DBSERVER};port={$DB_PORT};dbname={$DATABASENAME};charset={$CHAR_SET}", $USERNAME, $PASSWORD);
      // print_r(get_object_vars($this->dbh));die;
      $this->displayError();
    } catch (Exception $e) {
      $tmp_arr = array();
      $tmp_arr = $e->getTrace()[3];
      $tmp_arr['err_message'] = $e->getMessage();
      $tmp_arr['USER'] = $USERNAME;
      $tmp_arr['NAME'] = $DBSERVER;
      $tmp_arr['DNAME'] = $DATABASENAME;
      $this->errorLog(json_encode($tmp_arr));
      // $tmp_arr = array();
      // $tmp_arr['err_message'] = $e->getMessage();
      // $this->errorLog(json_encode($tmp_arr));
    }
  }

  public function closeConnection() {
    try {
      $this->dbh = null;
    } catch (Exception $e) {
      
    }
  }

  /**
   * Enable display error parameter of PDO.
   */
  public function displayError() {
    $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }

  /**
   * Execute insert query.
   * 
   * $params = array( 
   * 		'id' => 1,
   * 		'field1' => 'abc',
   * 		'created_at' => 'msqlfunc_NOW()' Put "msqlfunc_" prefix before use mysql functions
   * )
   * $pdo->insert('demo_table', $params)
   * 
   * @param string $table
   * @param array $params
   * @return integer
   */
  public function insert($table, $params) {
    try {
      $fields = "";
      foreach ($params as $field => $value) {
        if (strpos($value, 'msqlfunc_') === 0) {
          $value = str_replace('msqlfunc_', '', $value);
          $fields .= $field . ' = ' . $value . ', ';
          unset($params[$field]);
        } else {
          $fields .= $field . ' = :' . $field . ', ';
        }
      }
      $fields = trim($fields, ', ');

      $sql = "INSERT INTO $table SET $fields";

      $stmt = $this->dbh->prepare($sql);
      $stmt->execute($params);
      $last_id = $this->dbh->lastInsertId();
      $stmt = null;
      return $last_id;
    } catch (Exception $e) {
      $tmp_arr = $e->getTrace();
      $tmp_arr['err_message'] = $e->getMessage();
      $this->errorLog(json_encode($tmp_arr));
      //$this->errorLog(json_encode($e->getTrace()));
    }
  }

  /**
   * Execute update query
   * 
   * $params = array(
   * 		'name' => 'new_test',
   * 		'email' => 'new_test@test.com',
   * 		'created_at' => 'msqlfunc_NOW()' Put "msqlfunc_" prefix before use mysql functions
   * 	);
   * 
   * 	$where = array(
   * 		'clause' => 'id=:id',
   * 		'params' => array(
   * 			':id' => 24
   * 		)
   * 	)
   * $pdo->update("demo_table", $params, $where);
   * 
   * @param string $table
   * @param array $params
   * @param array $warr	 
   */
  public function update($table, $params, $warr,$activity_feed=false) {
    try {
      $fields = $where = "";
      $fieldsK  = "";
      foreach ($params as $field => $value) {
        if (strpos($value, 'msqlfunc_') === 0) {
          $value = str_replace('msqlfunc_', '', $value);
          $fields .= $field . ' = ' . $value . ', ';
          unset($params[$field]);
        } else {
          $fields .= $field . ' = :' . $field . ', ';
          $newVal=$value;          
          
          $fieldsK .=' IF('.$field.'="'.$newVal.'","blankField",'.$field.') as '.$field . ', ';
        }
      }
      $fields = trim($fields, ', ');
      $where = $warr['clause'];
      $params = array_merge($params, $warr['params']);

      $paramsK = $warr['params'];
      $fieldsK = trim($fieldsK, ', ');
      
      if($activity_feed){
        $sqlK = "SELECT $fieldsK FROM $table WHERE $where";
        $dataK = $this->select($sqlK,$paramsK);
        $resultK =count($dataK) > 0 ? $dataK[0] : $dataK;
        $filteredResult = array_filter($resultK,function ($var) {
            return (strpos($var, 'blankField') === false);
        });
      }
      
      $sql = "UPDATE $table SET $fields WHERE $where";
      $stmt = $this->dbh->prepare($sql);
      $stmt->execute($params);
      
      $stmt = null;
      if($activity_feed){
        return $filteredResult;
      }
    } catch (Exception $e) {
      $tmp_arr = $e->getTrace();
      $tmp_arr['err_message'] = $e->getMessage();
      $this->errorLog(json_encode($tmp_arr));
      //$this->errorLog(json_encode($e->getTrace()));
    }
  }

  /**
   * Execute delete query.
   * 
   * $query = "DELETE FROM demo_table WHERE id = :id"
   * $params = array( 
   * 		':id' => 1
   * )
   * $pdo->delete($query, $params)
   * 
   * @param string query
   * @param array $params
   * @return integer
   */
  public function delete($query, $params = array()) {
    try {
      $stmt = $this->dbh->prepare($query);
      if (!$stmt && $this->error_display) {
        echo "Error = <pre>";
        print_R($this->dbh->errorInfo());
        exit;
      }
      $stmt->execute($params);
      $row_count = $stmt->rowCount();
      $stmt = null;
      return $row_count;
    } catch (Exception $e) {
      $tmp_arr = $e->getTrace();
      $tmp_arr['err_message'] = $e->getMessage();
      $this->errorLog(json_encode($tmp_arr));
      //$this->errorLog(json_encode($e->getTrace()));
    }
  }

  /**
   * Fetch one record
   * 
   * $query = "SELECT * FROM demo_tables WHERE id=:id"
   * $params = array(
   * 		':id' => '1',
   * 	);
   * 
   * $pdo->selectOne($query, $params);
   * 
   * @param string $query
   * @param array $params
   * @param string $fetch_method
   * @return array
   */
  public function selectOne($query, $params = array(), $fetch_method = PDO::FETCH_ASSOC) {
    $data = $this->select($query, $params, $fetch_method);
    return count($data) > 0 ? $data[0] : $data;
  }

  /**
   * Fetch record set 
   * 
   * $query = "SELECT * FROM demo_tables WHERE id=:id"
   * $params = array(
   * 		':id' => '1'
   * 	);
   * 
   * $pdo->select($query, $params);
   * 
   * @param string $query
   * @param array $params
   * @param string $fetch_method
   * @return array
   */
  public function select($query, $params = array(), $fetch_method = PDO::FETCH_ASSOC) {
    $data = array();
    try {
      $stmt = $this->dbh->prepare($query);
      $stmt->setFetchMode($fetch_method);

      if (count($params) > 0) {
        $stmt->execute($params);
      } else {
        $stmt->execute();
      }
      $rows = $stmt->rowCount();
      if ($rows > 0) {
        while ($row = $stmt->fetch($fetch_method)) {
          $data[] = $row;
        }
      }
      $stmt = null;
      return $data;
    } catch (Exception $e) {
      $tmp_arr = $e->getTrace();
      $tmp_arr['err_message'] = $e->getMessage();
      $this->errorLog(json_encode($tmp_arr));
      //$this->errorLog(json_encode($e->getTrace()));
    }
  }

  public function selectGroup($query, $params = array(),$key = '', $fetch_method = PDO::FETCH_ASSOC) {
    $data = array();
    try {
      $stmt = $this->dbh->prepare($query);
      $stmt->setFetchMode($fetch_method);

      if (count($params) > 0) {
        $stmt->execute($params);
      } else {
        $stmt->execute();
      }
      $rows = $stmt->rowCount();
      if ($rows > 0) {
        while ($row = $stmt->fetch($fetch_method)) {
          if(!empty($key) && isset($row[$key])){
            $data[$row[$key]][] = $row;
          }else{
            $data[] = $row;
          }
        }
      }
      $stmt = null;
      return $data;
    } catch (Exception $e) {
      $tmp_arr = $e->getTrace();
      $tmp_arr['err_message'] = $e->getMessage();
      $this->errorLog(json_encode($tmp_arr));
      //$this->errorLog(json_encode($e->getTrace()));
    }
  }

  protected function errorLog($message) {
    global $SITE_ENV,$DEFAULT_SITE_NAME,$NOREPLY_EMAIL,$SENDGRID_API_KEY;
    if ($SITE_ENV=='Local') {
        echo "<pre>";
        print_r(json_decode($message, true));
        echo "</pre>";
        exit();
    }

    $message1 = '';
    $message1 .= "<pre>";
    $message1 .= print_r(json_decode($message,true),true);
    $message1 .= "</pre>";
    $message = $message1;


    $mail = new SendGrid\Mail();
    $fromEmailObj = new SendGrid\Email($DEFAULT_SITE_NAME, $NOREPLY_EMAIL);
    $mail->setFrom($fromEmailObj);
    $mail->setSubject($DEFAULT_SITE_NAME.': sql error occur');
        
    $contentObj = new SendGrid\Content("text/html", $message);
    $mail->addContent($contentObj);

    $personalization = new SendGrid\Personalization();

    $toEmailObj = new SendGrid\Email(null, 'sqlnotify@cyberxllc.com');
    $personalization->addTo($toEmailObj);
   
    $mail->addPersonalization($personalization);

    $sendGridObj = new \SendGrid($SENDGRID_API_KEY);
    try {
        $response = $sendGridObj->client->mail()->send()->post($mail);
    } catch (Exception $e) {
    }
    exit;
  }

}

?>