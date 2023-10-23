<div class="panel panel-default panel-block">
    <div class="panel-heading">
        <div class="panel-title">
        <h4 class="mn">
         Products - <span class="fw300"><?= $class_name ?></span>
        </h4>
       </div>
    </div>
    <div class="panel-body">
        <h4  class="fs16 m-t-0 m-b-15">Products & Contributions</h4>
        <div class="table-responsive br-n">
            <table class="<?=$table_class?>">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-center" width="100px">Group Contributions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($selRes)) { ?>
                        <?php foreach ($selRes as $key => $value) { ?>
                            <tr>
                                <td><?= $value['product_name'] ?></td>
                                <td class="text-center"><?= $value['is_contribution'] == 'Y' ? 'Yes' : 'No' ?></td>
                            </tr>
                        <?php } ?>
                    <?php }else{ ?>
                        <tr><td colspan="2" class="text-center">No Record(s) Found</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
       
        <div class="text-center">
             <a href="javascript:void(0)" class="fw500 red-link" onclick="window.parent.$.colorbox.close()">Close</a>
        </div>
    </div>
</div>
