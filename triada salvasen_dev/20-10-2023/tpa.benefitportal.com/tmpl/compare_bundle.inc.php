<?php

if(!empty($ComparisionApiResponse['data'])){
	$removePrd=  !empty($_POST['removeProduct']) ? json_decode($_POST['removeProduct'],true) :[];
	$removedBundleArr = [];

	if(!empty($removePrd)){
		foreach ($removePrd as $pv) {
			$removedBundleArr[$pv['name']][] = $pv['value'];
		}
	}
	foreach ($ComparisionApiResponse['data']['bundleProductPrice'] as $key => $bundleProductArr) {
		$bundleProductName[$key]['price'] = 0;
		$bundleProductName[$key]['productname'] = '';
		$bundleKey  = array();
		$benefitierArr = !empty($_POST['benefit_tierName']) ? $_POST['benefit_tierName'] :[];

		foreach ($benefitierArr[$key] as $k => $value) {
			if(empty($removedBundleArr[$key]) || !in_array($k, $removedBundleArr[$key])){
					$bundleProductName[$key]['price']  = (int) $value + $bundleProductName[$key]['price'];
			}
		}

		$recordCount = count($bundleProductArr);
		$i = 0;
		foreach($bundleProductArr as $k=> $prd){
			$addComma = ', ';
			if(++$i === $recordCount) {
				$addComma = '';
			}
			if(!empty($removedBundleArr[$key]) && in_array($prd['product_id'],$removedBundleArr[$key])){
				$bundleProductName[$key]['productname'] .= '<span style="text-decoration: line-through;">'.$prd['product_name'].'</span>'.$addComma;
			}else{
				$bundleProductName[$key]['productname'] .= "<span>".$prd['product_name'].$addComma.'</span>';
			}
		}
	}
}

$bundleCompareArr = [];
if(!empty($ComparisionApiResponse['data'])){
	foreach ($ComparisionApiResponse['data']['comparisionLabels'] as $compKey => $compValue) {
		foreach($ComparisionApiResponse['data']['compariosionBundleDetails'] as $bundleKey => $bundleValue){
			$bundleCompareArr[$bundleValue['Heading']][$compValue] =  $bundleValue['Body'][$compValue];
		} 
	}
}
?>

<div class="plan-block">
   <div class="table-responsive">
     <table class="<?=$table_class?>">
        <thead>
           <tr>
              <th class="pn">
                 <div class="bg_dark_danger fs18 font-bold text-white p-15">Compare Bundles
                 </div>
              </th>
			  <?php if(!empty($ComparisionApiResponse['data']['compariosionBundleDetails'])){
				foreach($ComparisionApiResponse['data']['compariosionBundleDetails'] as $value){ ?>
              <th><?=$value['Heading']?></th>
              <?php }  } ?>
           </tr>
        </thead>
        <tbody>
           <tr>
              <td>Product In Bundle</td>
			  <?php if(!empty($bundleProductName)){
				foreach($bundleProductName as $prdvalue){?>
              <td><?=$prdvalue['productname']?></td>
              <?php } }?>
           </tr>
           
		   <?php if(!empty($ComparisionApiResponse['data']['comparisionLabels'])){
			   foreach($ComparisionApiResponse['data']['comparisionLabels'] as $value){ ?>
			   <tr>
                    <td><?=$value?></td>
					<?php foreach($bundleCompareArr as $k => $v){ ?> 
                    <td><span><?=$v[$value]?></span></td>
					<?php } ?>
				</tr>						
			  <?php }  } ?>
           
           <tr>
              <td></td>
			  <?php if(!empty($bundleProductName)){
			     foreach($bundleProductName as $bundleCode => $prdvalue){?>
              <td>
                 <div class="m-b-15">
                    <button type="button" class="btn btn-info btn-block"><?=displayAmount($prdvalue['price']);?> / pay period</button>
                 </div>
                 <div>
				 <button type="button" class="btn btn-info btn-outline btn-block <?= $_REQUEST['is_elected'] =='Y' ? 'disabled' : 'popupelectbundle' ?>" data-bundleid="<?=$bundleCode?>"><?= $_REQUEST['elected_bundle'] == $bundleCode ? 'Elected Bundle' : 'Elect Bundle' ?></button>
                 </div>
              </td>
			  <?php } }?>
           </tr>
        </tbody>
     </table>
   </div>
   <div class="b-t p-15 text-center">
	<?php if($_REQUEST['is_elected'] == 'N'){ ?>
		<a href="javascript:void(0);" class="btn btn-action" onclick="window.parent.$.colorbox.close();">Confirm</a>
		<a href="javascript:void(0);" onclick="document.getElementById('elected_bundle').value = '';window.parent.$.colorbox.close();"  class="btn red-link" data-toggle="tooltip" data-container="body" title="Cancel" >Cancel</a>
	<?php }else{ ?>
		<a href="javascript:void(0);" onclick="window.parent.$.colorbox.close();"  class="btn red-link" data-toggle="tooltip" data-container="body" title="Cancel" >Cancel</a>
	<?php } ?>
	</div>
</div>
<script>
$(document).off('click','.popupelectbundle');
$(document).on('click','.popupelectbundle',function(e){
	e.preventDefault();
	$bundleId = $(this).attr('data-bundleid');
	$('#elected_bundle').val($bundleId);
	$(".popupelectbundle").text('Elect Bundle');
	$(this).text('Elected Bundle');
});
</script>