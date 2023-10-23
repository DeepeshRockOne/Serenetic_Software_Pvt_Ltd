<?php
include_once __DIR__ . '/includes/connect.php';
$validate = new validation();
$response = array();
$sponsor_id = $_POST['sponsor_id'];
$enrollee_id = $_POST['enrollee_id'];

    if(!empty($sponsor_id)){
        $selLeadSql = "SELECT id,employee_id, fname, lname, lead_id, email, hire_date, birth_date, gender, zip,group_classes_id,group_coverage_id,termination_date,employee_type,group_company_id
                FROM leads 
                WHERE sponsor_id=:sponsor_id and status!='Converted' AND employee_id=:enrollee_id AND is_deleted='N'";
        $whereLeadSql=array(':sponsor_id'=>$sponsor_id,":enrollee_id" => $enrollee_id);
        $resLeadRows = $pdo->selectOne($selLeadSql,$whereLeadSql); 
        if(!empty($resLeadRows)){

            $response["leadID"] = $resLeadRows["id"];
            $response["leadFname"] = $resLeadRows["fname"];
            $response["leadBirth_date"] = date("m/d/Y",strtotime($resLeadRows["birth_date"]));
            $response["leadZip"] = $resLeadRows["zip"];
           
            $response["leadEmail"] = $resLeadRows["email"];
            $response["leadGender"] = $resLeadRows["gender"];
            $response["leadHire_date"] = date("m/d/Y",strtotime($resLeadRows["hire_date"]));
            $response["leadGroup_coverage_id"] = $resLeadRows["group_coverage_id"];
            $response["leadGroup_classes_id"] = $resLeadRows["group_classes_id"];
            $response["leadEmployee_type"] = $resLeadRows["employee_type"];
            $response["leadGroup_company_id"] = $resLeadRows["group_company_id"];
            $response["leadTermination_date"] = date("m/d/Y",strtotime($resLeadRows["termination_date"]));

            $response["status"] = "success";
        }else{
            $response["status"] = "fail";
            $response["message"] = "Please select enrollee to import or invalid enrollee";
        }
    }else{
        $response["status"] = "fail";
        $response["message"] = "Sponsor not found";
    }

echo json_encode($response);
dbConnectionClose();
exit;
?>