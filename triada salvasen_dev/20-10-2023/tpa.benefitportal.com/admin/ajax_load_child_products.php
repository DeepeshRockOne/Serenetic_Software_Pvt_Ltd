<?php
include_once 'layout/start.inc.php';
$result = array();	
$product_id=$_POST['product_id'];
 
$childProductSql="SELECT id,product_name,product_code 
                  FROM sub_products 
                  where status='Active' AND is_deleted='N' 
                  order by id asc";
$childProductRes=$pdo->select($childProductSql,array(":id"=>$product_id));

$child_products_sql="SELECT id,sub_product_id FROM prd_sub_products where is_deleted='N' AND product_id = :product_id";
$child_products=$pdo->select($child_products_sql,array(":product_id"=>$product_id));

ob_start();
 if(!empty($child_products)) { $i=0;?>
  <?php foreach ($child_products as $key => $row) { ?>
    <div class="col-sm-6 col-md-5 child_product_div" id="child_product_div_<?= $row['id'] ?>">
      <div class="phone-control-wrap ">
        <div class="phone-addon">
          <div class="form-group">          
            <select name="child_product[<?= $row['id'] ?>]" id="child_product_<?= $row['id'] ?>" class="form-control">
              <option value="">Select Product</option>
              <?php if(!empty($childProductRes)){?>
                <?php foreach ($childProductRes as $key => $product) { ?>
                    <option value="<?= $product['id'] ?>" <?= $row['sub_product_id']==$product['id'] ? 'selected=selected' :'' ?>>
                      <?php if($product['id']!=1) { ?>
                        <?= $product['product_name'] .' ('. $product['product_code'].')' ?>
                      <?php }else{?>
                        <?= $product['product_name'] ?>
                      <?php } ?>
                    </option>  
                <?php } ?>
              <?php } ?>
            </select>
            <p class="error" id="error_child_product_<?= $row['id'] ?>"></p>     
          </div>
        </div>
         <?php if($i >= 1) { ?>
             <div class="phone-addon"> <div class="form-group"><a href="javascript:void(0);" class="removeChildProduct" data-removeId="<?= $row['id'] ?>" data-id="<?= $row['id'] ?>"><i class="fa fa-times"></i></a></div></div>
         <?php }else{?>
          <div class="phone-addon"> <div class="form-group"><a href="javascript:void(0);" class="removeChildProduct" data-removeId="<?= $row['id'] ?>" data-id="<?= $row['id'] ?>"><i class="fa fa-times"></i></a></div></div>
         <?php } ?>
      </div>
    </div>
  <?php $i++; } 
} 
  
$data=ob_get_clean();

$result['html'] = $data;
$result['status'] = "success"; 
  
header('Content-type: application/json');
echo json_encode($result);
dbConnectionClose(); 
exit;
?>