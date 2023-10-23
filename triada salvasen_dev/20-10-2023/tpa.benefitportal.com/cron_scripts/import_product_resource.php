<?php
$IMPORTDB='BENEFITS_PORTAL';

include_once dirname(__DIR__) . "/includes/other_connect.php";
error_reporting(E_ALL);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);

$sql = "SELECT res.*,GROUP_CONCAT(p.product_code ORDER BY p.product_code ASC) as resProducts
    FROM resources res 
    JOIN res_products rp ON(rp.res_id=res.id)
    JOIN prd_main p ON(p.id=rp.product_id AND p.is_deleted='N')
    WHERE p.product_code IN ('".implode("','",$productIDArray)."') AND res.is_deleted='N' GROUP BY res.id";
$res = $OtherPdo->select($sql);
if(!empty($res)){
	foreach ($res as $key => $value) {
        $resExist = $pdo->selectOne("SELECT id FROM resources WHERE is_deleted='N' AND display_id=:display_id",array(":display_id"=>$value['display_id']));

        if(empty($resExist['id'])){
            $insert_param = array(
                "display_id" => $value['display_id'],
                "name" => $value['name'],
                "type" => $value['type'],
                "effective_date" => date('Y-m-d',strtotime($value['effective_date'])),
                "termination_date" => !empty($value['termination_date']) ? date('Y-m-d',strtotime($value['termination_date'])) : '',
                "user_group" => $value['user_group'],
                "status" => $value['status'],
                "created_at" => "msqlfunc_NOW()"
            );
            $insertedID = $pdo->insert('resources',$insert_param);
        }else{
            $insertedID = $resExist['id'];
        }
        
        if($insertedID){
            $resProducts = !empty($value['resProducts']) ? explode(',',$value['resProducts']) : array();
            if(empty($resProducts)){
                continue;
            }
           
            $productIDs = $pdo->selectOne("SELECT GROUP_CONCAT(id) as ids FROM prd_main WHERE product_code IN('".implode("','",$resProducts)."') AND is_deleted='N'");
            if(!empty($productIDs['ids'])){
                $prdIDs = explode(',',$productIDs['ids']);
                $res_product_insert = array("res_id"=>$insertedID,'created_at' => "msqlfunc_NOW()");
                foreach($prdIDs as $id){
                    $srRes = $pdo->selectOne("SELECT id from res_products where product_id = :product_id AND res_id=:res_id",array(":product_id"=>$id,":res_id"=>$insertedID));
                    if(empty($srRes['id'])){
                        $res_product_insert['product_id'] = $id;
                        $pdo->insert('res_products',$res_product_insert);
                    }
                }
            }
            $sel_sub_resources = "SELECT * FROM sub_resources WHERE res_id=:res_id AND is_deleted='N'";
            $res_sub_resources = $OtherPdo->select($sel_sub_resources,array(":res_id"=>$value['id']));
            if(!empty($res_sub_resources)){
                $sub_incr = '';
                $schSubParam = array(":res_id"=>$insertedID);
                if($value['type'] != 'id_card'){
                    $sub_incr = ' AND group_id=:group_id ';
                }
                foreach($res_sub_resources as $subres){
                    if($value['type'] != 'id_card'){
                        $schSubParam[':group_id'] = $subres['group_id'];
                    }
                    $exitsSubRes = $pdo->selectOne("SELECT id from sub_resources WHERE res_id=:res_id AND is_deleted='N' $sub_incr ",$schSubParam);
                    if(!empty($exitsSubRes['id'])){
                        continue;
                    }
                    $insert_sub_res_param = array(
                        "res_id" => $insertedID,
                        "coll_type" => $subres['coll_type'],
                        "description" => $subres['description'],
                        "state_url" => $subres['state_url'],
                        "coll_doc_url" => $subres['coll_doc_url'] ,
                        "video_type" => $subres['video_type'],
                        "group_id" => $subres['group_id'],
                        "created_at" => "msqlfunc_NOW()"
                    );
                    $pdo->insert('sub_resources',$insert_sub_res_param);
                    if(!empty($subres['video_type']) && $subres['video_type'] == 'file' && !empty($subres['coll_doc_url'])){
                        $file  = $OTHER_HOST . '/uploads/collateral_document/'.$subres['coll_type']."/".$subres['coll_doc_url'];
                        $file_headers = get_headers($file);//Check file from another server
                        if ($file_headers[0] == 'HTTP/1.1 200 OK') {
                            //File is exists then it will be copy
                            if (!file_exists($COLLATERAL_DOCUMENT_DIR."/".$subres['coll_type'])) {
                                mkdir($COLLATERAL_DOCUMENT_DIR."/".$subres['coll_type'], 0777, true);
                            }
                            copy($file,$COLLATERAL_DOCUMENT_DIR . DIRECTORY_SEPARATOR .$subres['coll_type']. DIRECTORY_SEPARATOR . $subres['coll_doc_url']);
                        }
                    }
                }
            }
        }
    }
}

echo "Import Porduct resource->Completed";
dbConnectionClose();
exit;
?>