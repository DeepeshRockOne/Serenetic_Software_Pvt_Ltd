<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <div class="panel-title">
      <p class="fs18"><strong class="fw500">PMPM Commission Agents</strong></p>
    </div>
  </div>
  <div class="panel-body">
    <div class="table-responsive">
      <table class="<?= $table_class ?>">
        <thead>
          <tr>
            <th>Agent ID</th>
            <th>Agent Name</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if($products){ 
            foreach($products as $product){?>
            <tr>
              <td><?=$product['rep_id']?></td>
              <td><?=$product['agent_name']?></td>
              <td><?=$product['status']?></td>
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
