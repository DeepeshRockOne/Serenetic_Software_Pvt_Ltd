<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <div class="panel-title">
      <h4 class="mn">Advance Comission Products - <span class="fw300"> (<?=count($prdRes)?>)</span></h4>
    </div>
  </div>
  <div class="panel-body">
    <div class="table-responsive">
    	<table class="<?= $table_class ?>">
    		<thead>
    			<tr>
    				<th>Product Name</th>
    				<th>Product ID</th>
    				<th class="text-right" width="150px">Advance Months</th>
    			</tr>
    		</thead>
    		<tbody>
                <?php if($prdRes){ ?>
                    <?php foreach ($prdRes as $key => $value) { ?>
            			<tr>
            				<td><?=$value['name']?></td>
            				<td><?=$value['product_code']?></td>
            				<td class="text-center"><?=$value['advance_month']?></td>
            			</tr>
                    <?php } ?>
                <?php } ?>
    		</tbody>
    	</table>
    	<div class="bottom_close_btn text-center m-t-20">
    		<a href="javascript:void(0);" class="btn red-link m-l-15 fs1adv" onclick="window.parent.$.colorbox.close()">Cancel</a>
    	</div>
    </div>
  </div>
</div>