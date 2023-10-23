<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$resArr = array();
$action = checkIsset($_POST['action']);
$query = checkIsset($_POST['query']); 
$action = checkIsset($_GET['action']);
$i=0;

$query = cleanSearchKeyword($query); 
 
if ($query != "") {
	if($action && $action == 'getaddress'){
	    $selSql = "SELECT address,address_2 
	    			FROM customer 
	    			WHERE (address LIKE ('%" . $query . "%') OR address_2 LIKE '%" . $query . "%')";
	    $pdo->displayError();
	    $rows = $pdo->select($selSql);  
	    if (!empty($rows)) {
	        foreach ($rows as $i => $row) {      
	            $resArr[$i]['label'] = $row['address'] .' '.$row['address_2'];
	            $i++;
	        }
	    }
	}else if($action && $action == 'getmembers'){
		$selSql = "SELECT rep_id 
					FROM customer 
					WHERE type = 'customer' AND rep_id LIKE ('%" . $query . "%')";
	    $pdo->displayError();
	    $rows = $pdo->select($selSql);  
	    if (!empty($rows)) {
	        foreach ($rows as $i => $row) {      
	            $resArr[$i]['label'] = $row['rep_id'];
	            $i++;
	        }
	    }
	}
}

header('Content-Type: application/json');
echo json_encode($resArr);
dbConnectionClose();
exit();
?>