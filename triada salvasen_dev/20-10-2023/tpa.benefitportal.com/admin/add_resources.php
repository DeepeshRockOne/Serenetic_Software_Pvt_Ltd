<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(75);
include_once 'portal_resources.class.php';


$is_ajaxed = checkIsset($_POST['is_ajaxed']);
if($is_ajaxed){
    $response = array('status'=>'fail');
    $response['msg'] = 'Something went wrong!';

    $id = checkIsset($_POST['id']);
    $is_delete = checkIsset($_POST['is_delete']);
    if($is_delete && !empty($id)){
        $resource = new portalResources();
       $deleted =  $resource->removeResource($id);
       if($deleted){
        $response['status'] = 'success';
        $response['msg'] = 'Resource Deleted Successfully!';
       }
    }else{
        
        $validate = new Validation();

        $portal_type = checkIsset($_POST['portal_type']);
        $module_name = checkIsset($_POST['module_name']);
        $resource_name = checkIsset($_POST['resource_name']);
        $pdf_file = checkIsset($_FILES['pdf_file']);
    
        if(empty($module_name)){
            $validate->setError("module_name","Please select any module.");
        }

        if(!empty($module_name)){
            $validate->string(array('required' => true, 'field' => 'resource_name', 'value' => $resource_name), array('required' => 'Please enter resource name.'));

            if(empty($pdf_file)){
                $validate->setError("pdf_file","Please select any pdf file");
            }else if(!empty($pdf_file)){
                if($pdf_file['size'] > 10485760){
                    $validate->setError('pdf_file',"Please select pdf file less then 10MB.");
                }else if(!empty($pdf_file)){
                    $type = $pdf_file['type'];
                    $ext = @end((explode("/",$type)));
                    if(!in_array($ext,array('pdf'))){
                        $validate->setError('pdf_file',"Invalid File Please select Only PDF File.");
                    }
                }
            }
        }
        

        if($validate->isValid()){
            $resource = new portalResources();
            $insert_param = array(
                'portal_type' => $portal_type,
                'module_name' => $module_name,
                'resource_name' => $resource_name,
                'pdf_file' => $pdf_file,
            );

            $resource->createResource($insert_param);

            $response['status'] = 'success';
            $response['msg'] = 'You have successfully added a resource to the '.ucfirst($portal_type).'<br>Portal - '.$module_name.' Module.';
        }else{
            $response['status'] = 'error';
            $response['errors'] = $validate->getErrors();
        }
    }

    header("Content-Type: application/json");
    echo json_encode($response);
    exit;
}

$type = checkIsset($_GET['type']);
$selModule ='';
$resModule = array();

$resModule = portalResources::getUserModule($type);

    
$exJs = array("thirdparty/ajax_form/jquery.form.js");

$template = "add_resources.inc.php";
include_once 'layout/iframe.layout.php';
?>