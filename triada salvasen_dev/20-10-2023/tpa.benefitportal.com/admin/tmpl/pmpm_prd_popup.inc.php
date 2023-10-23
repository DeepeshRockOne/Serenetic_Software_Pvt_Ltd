<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <div class="panel-title">
      <p class="fs18"><strong class="fw500">PMPM Commission Products - </strong> <span class="fw300"><?=$agent_details['name']?> (<?=$agent_details['rep_id']?>)</span></p>
    </div>
  </div>
  <div class="panel-body">
    <div class="table-responsive">
      <table class="<?= $table_class ?>">
        <thead>
          <tr>
            <th>Product Name</th>
            <th>Product ID</th>
          </tr>
        </thead>
        <tbody>
          <?php if($products){ 
            foreach($products as $product){?>
            <tr>
              <td><?=$product['name']?></td>
              <td><?=$product['product_code']?></td>
            </tr>
          <?php 
            }
          } ?>
        </tbody>
        <tfoot>
      </tfoot>
      </table>
      <div class="text-center">
        <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Close</a>
      </div>
    </div>
  </div>
</div>
