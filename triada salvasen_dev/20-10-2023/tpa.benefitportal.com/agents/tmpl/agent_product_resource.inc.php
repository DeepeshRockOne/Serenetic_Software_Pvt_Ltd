<div class="table-responsive">
   <table class="<?=$table_class?>">
      <thead>
         <tr>
            <th>Name</th>
            <th>Type</th>
            <th class="text-center">Description</th>
            <th class="text-center" width="50px">Action</th>
         </tr>
      </thead>
      <tbody>
        <?php if(!empty($resResourse)) { ?>
          <?php foreach ($resResourse as $key => $value) { ?>
              <tr>
                <td><?= $value['resourse_name']?></td>
                <td><?= $value['coll_type'] ?></td>
                <td class="text-center icons">
                     <a href="javascript:void(0);" data-toggle="tooltip" title="<?= $value['description'] ?>"  data-placement="top"><i class="fa fa-eye"></i></a>
                </td>
                <td class="text-center icons">
                    <?php if($value['coll_type']=="pdf"){ ?>
                      <a href="<?=$COLLATERAL_DOCUMENT_WEB.'pdf/'.$value['coll_doc_url']?>" download><i class="fa fa-download"></i></a>
                    <?php }else if($value['coll_type']=="link"){ ?>
                      <a href="<?=(substr($value['coll_doc_url'],0,7)=="http://" || substr($value['coll_doc_url'],0,8)=="https://"?$value['coll_doc_url']:'//'.$value['coll_doc_url'])?>" target="_blank"><i class="fa fa-external-link"></i></a>
                    <?php }else if($value['coll_type']=="video"){ ?>
                      <a href="javascript:void(0)" data-href="<?=$HOST?>/play_resource_video.php?resourse=<?= $value['sub_resource_id'] ?>" class="play_video"  target="_BLANK"><i class="fa fa-external-link"></i></a>
                      
                    <?php } ?>
                     
                     
                </td>
             </tr>
          <?php } ?>
        <?php }else{ ?>
          <tr><td colspan="4" class="text-center">No Record(s) Found</td></tr>
        <?php } ?>
      </tbody>
   </table>
</div>

 

