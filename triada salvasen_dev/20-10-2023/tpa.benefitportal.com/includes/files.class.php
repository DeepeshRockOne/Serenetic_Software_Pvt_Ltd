<?php
include_once dirname(__DIR__) . "/includes/functions.php";
/**
  * Policy Settings
  *
  */
class FilesClass {

    public function getBillingFilePrd($file_id,$is_array = true) {
        global $pdo;
        $prd = array();
        $prd_row = $pdo->selectOne("SELECT GROUP_CONCAT(DISTINCT product_id) as product_ids FROM billing_file_prd WHERE is_deleted='N' AND file_id=:file_id",array(":file_id" => $file_id));
        if(!empty($prd_row['product_ids'])) {
            $prd = explode(',',$prd_row['product_ids']);
        }
        if($is_array == false) {
            $prd = implode(',',$prd);
        }
        return $prd;
    }

    public function updateBillingFilePrd($file_id,$products = array(),$old_prd = array()) {
        global $pdo;
        if(!empty($products)) {
            if(empty($old_prd)) {
                $old_prd = $this->getBillingFilePrd($file_id);
            }            
            foreach ($products as $product_id) {
                if(!in_array($product_id,$old_prd)) {
                    $pdo->insert('billing_file_prd',array('file_id' => $file_id,'product_id' => $product_id,'created_at' => 'msqlfunc_NOW()'));
                }
            }
            $upd_where = array(
                'clause' => "is_deleted='N' AND file_id=:file_id AND product_id NOT IN(".implode(',',$products).")",
                'params' => array(
                    ':file_id' => $file_id,
                ),
            );
            $pdo->update('billing_file_prd',array("is_deleted" => "Y"), $upd_where);
        } else {
            $upd_where = array(
                'clause' => "is_deleted='N' AND file_id=:file_id",
                'params' => array(
                    ':file_id' => $file_id,
                ),
            );
            $pdo->update('billing_file_prd',array("is_deleted" => "Y"), $upd_where);
        }
        return true;
    }
}
?>