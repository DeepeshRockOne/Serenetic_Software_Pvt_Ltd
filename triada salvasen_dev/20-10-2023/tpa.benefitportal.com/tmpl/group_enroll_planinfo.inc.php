<div class="panel panel-default theme-form">
	<div class="panel-heading">
		<h4 class="panel-title"><?=$productDetails['Product Name'];?> - Details</h4>
	</div>
	<div class="panel-body b-b">
                <div class="clearfix">
                    <h5 class="fw700">Effective Date</h5>
                    <p class="mn"><?=$productDetails['Effective Date'];?></p>
                </div>
                <div class="m-t-30">
                    <h5 class="m-t-0 fw700">Available States</h5>
                    <p class="mn"><?=empty($productDetails['Available State']) ? 'None' : $productDetails['Available State'];?></p>
                </div>
                <div class="m-t-30">
                    <h5 class="m-t-0 fw700">Products Required</h5>
                    <p class="mn"><?=empty($productDetails['Required Product']) ? 'None' : $productDetails['Required Product'];?></p>
                </div>
                <div class="m-t-30">
                    <h5 class="m-t-0 fw700">Products Excluded</h5>
                    <p class="mn"><?=empty($productDetails['Excluded Product']) ? 'None' : $productDetails['Excluded Product'];?></p>
                </div>
            </div>
            <div class="panel-body">
                <div class="clearfix">
                    <h5 class="fw700">Association Benefits</h5>
                </div>
                <div class="m-t-30">
                    <p class="m-t-0 "><?=empty($productDetails['Product Description']) ?'None':$productDetails['Product Description'];?></p>
                </div>
            </div>
</div>