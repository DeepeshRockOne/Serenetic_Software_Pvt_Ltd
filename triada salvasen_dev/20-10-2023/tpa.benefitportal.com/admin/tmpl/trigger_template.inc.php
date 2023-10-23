<?php include("notify.inc.php"); ?>
<div class="trigger_template">
  <div class="tabbing-tab">
    <ul class="nav nav-tabs customtab nav-noscroll">
       <?php
      $trigger = "active";
      include("br_broadcaster_tabs.inc.php");
      ?> 
    </ul>
  </div>
  <div class="panel panel-default panel-block ">
    <div class="panel-heading">
      <div>
        <i class="fa fa-envelope"></i>
        <h1><span>Manage Trigger Templates</span></h1>
      </div>
    </div>
  </div>
  <div class="tabbing-tab">
    <ul class="nav nav-tabs customtab">
      <?php $trigger_template = 'active'; ?>
      <?php include_once('triggers_tabs.inc.php'); ?>  
    </ul>
  </div>  
  <div class="panel panel-default panel-block ">
    <div class="list-group">
      <div class="list-group-item">
        <h4>Add New Template                    
          <a href="add_trigger_template.php" class="fa fa-plus" title="" data-toggle="tooltip" data-original-title="Add New Template"><i></i></a>                     
        </h4>
        <div class="table-responsive">  
          <table class="<?=$table_class ?>">
            <thead>
              <tr>
                <th style="width: 5%;" class="index">ID</th>
                <th style="width: 55%;">Title</th>              
                <th style="width: 20%;">Date/Time</th>             
                <th style="width: 20%;" class="icons">Manage</th>             
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($rows)) { ?>
                <?php foreach ($rows as $key => $row) { ?>        
                    <td class="index"><?php echo $row['id']; ?></td>
                    <td class=""><?php echo html_entity_decode($row['title']); ?></td>
                    <td class=""><?php echo retrieveDate($row['created_at']); ?></td>             
                    <td class="icons">
                      <a href="edit_trigger_template.php?id=<?= $row['id']; ?>" title="" data-toggle="tooltip" data-original-title="Edit Template" ><i class="fa fa-edit"></i></a>
                      <a href="delete_trigger_template.php?id=<?= $row['id']; ?>" title="" data-toggle="tooltip" class="popup" data-original-title="Delete Template" ><i class="fa fa-trash"></i></a> 
                      <a href="view_template.php?id=<?= $row['id']; ?>" title="" data-toggle="tooltip" data-original-title="View Template" ><i class="fa fa-search"></i></a> 
                    </td>             
                  </tr>              
                <?php } ?>
              <?php } else { ?>
                <tr>
                  <td colspan="4">No record(s) found</td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script language="javascript" type="text/javascript">
$(document).ready(function() {

  $('.popup').colorbox({iframe: true, width: '500px', height: '280px'});
});
</script>
