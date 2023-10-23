<?php
  $desc_incr = "";
  $desc_today_date=date('Y-m-d');
  $desc_sch_params = array();

  $desc_incr.=" AND (apr.agent_id=:agent_id OR p.product_type='Admin Only Product')";

  $desc_sch_params[":agent_id"]=$sponsor_id;

  $desc_incr.= " AND (pm.pricing_effective_date <= :today_date AND (pm.pricing_termination_date >= :today_date OR pm.pricing_termination_date is null))";
  $desc_sch_params[":today_date"]=$desc_today_date;

  if(in_array($enrollmentLocation, array('agentSide','aae_site'))){
    $desc_incr.=" AND p.product_type in ('Direct Sale Product','Add On Only Product')";
  }else if($enrollmentLocation == "groupSide"){
    $desc_incr.=" AND p.product_type in ('Group Enrollment','Add On Only Product')";
  
  } else if($enrollmentLocation == "adminSide") {
    if($is_group_member == 'Y') {
      $desc_incr.=" AND p.product_type in ('Admin Only Product','Group Enrollment','Add On Only Product')";
    } else {
      $desc_incr.=" AND p.product_type in ('Admin Only Product','Direct Sale Product','Add On Only Product')";
    }
  }

  if(!empty($pb_id)) {
    $pb_sql = "SELECT pg.product_ids
            FROM page_builder pg 
            LEFT JOIN page_builder_images pi ON (pi.id = pg.cover_image) 
            WHERE pg.is_deleted='N' AND pg.status='Active' AND pg.id=:id";
    $product_b_row = $pdo->selectOne($pb_sql,array(":id"=>$pb_id));

    if(!empty($pproduct_b_row['product_ids'])) {
      $desc_incr .= " AND p.id IN (".$product_b_row['product_ids'].")";   
    }
  }

  $productsSql="SELECT GROUP_CONCAT(DISTINCT p.id) as product_ids
    FROM prd_main p
    JOIN prd_matrix pm ON(pm.product_id = p.id)
    LEFT JOIN agent_product_rule apr ON (p.id=apr.product_id AND apr.status ='Contracted' AND apr.is_deleted='N')
    JOIN prd_category pc ON (pc.id=p.category_id)
    JOIN prd_fees pf ON (pf.id = p.carrier_id)
    WHERE p.status='Active' AND p.type!='Fees' AND p.is_deleted='N' $desc_incr";
  $prdProductsRes=$pdo->selectOne($productsSql,$desc_sch_params);

  $product_ids = array();
  if(isset($prdProductsRes['product_ids'])){
    $temp_product_ids = explode(',', $prdProductsRes['product_ids']);
    if(!empty($temp_product_ids)){
      $product_ids = $temp_product_ids;
    }
  }
?>

<?php
//*************** Cache Code Start   ***************
if($SITE_ENV == 'Local'){ 
  foreach ($productCacheMainArray as $key => $value) { 
    if(in_array($key, $product_ids)){ ?>
      <div class="tmp_prd_desc_<?=$key?>" style="display: none;">
      <?php echo base64_encode('<div class="clearfix">
          <a href="javascript:void(0)" id="show_details_on_new_tab_'.$key.'" data-category-id="~category_id~" data-product-id="'.$key.'" class="show_details_on_new_tab pull-right text-light-gray" data-details="'.base64_encode($value["description"]).'"><i class="fa fa-external-link fa-lg" aria-hidden="true"></i></a>
          </div>
          <div class="plan_details_bottom_scroll" style="max-height: 400px;">
            <h4 class="m-b-15">'.$value["name"].' Details</h4>
            <div class="fs12">
                '.$value["description"].'
                <div class="text-right">
                  <a href="javascript:void(0)" class="text-action fw500 hide_product_details" data-category-id="~category_id~" data-product-id="'.$key.'"><u>Cancel</u></a>
                </div>
            </div>
          </div>'); 
        ?>          
      </div>
<?php
    } 
  } 
}else{
  require_once dirname(__DIR__) . "/includes/redisCache.class.php";
	$redisCache = new redisCache(); 
  $id = 'All';
  $type = 'Product';
  $productRedisCacheMainArray = $redisCache->getOrGenerateCache('All','Product');
  
  foreach ($productRedisCacheMainArray as $key => $value) { 
    if(in_array($key, $product_ids)){ ?>
      <div class="tmp_prd_desc_<?=$key?>" style="display: none;">
      <?php echo base64_encode('<div class="clearfix">
          <a href="javascript:void(0)" id="show_details_on_new_tab_'.$key.'" data-category-id="~category_id~" data-product-id="'.$key.'" class="show_details_on_new_tab pull-right text-light-gray" data-details="'.base64_encode($value["agent_portal"]).'"><i class="fa fa-external-link fa-lg" aria-hidden="true"></i></a>
          </div>
          <div class="plan_details_bottom_scroll" style="max-height: 400px;">
            <h4 class="m-b-15">'.$value["productName"].' Details</h4>
            <div class="fs12">
                '.$value["agent_portal"].'
                <div class="text-right">
                  <a href="javascript:void(0)" class="text-action fw500 hide_product_details" data-category-id="~category_id~" data-product-id="'.$key.'"><u>Cancel</u></a>
                </div>
            </div>
          </div>'); 
        ?>          
      </div>
<?php
    } 
  } 
}

//*************** Cache Code End   ***************