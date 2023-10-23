<?php if($action == "online_admins"){ ?>
  <div class="panel panel-default panel-block">
     <div class="panel-heading">
        <div class="panel-title">
           <h4 class="mn">
              <i class="fa fa-circle text-success" aria-hidden="true"></i>&nbsp;
              Admin<span class="text-lowercase">(s)</span> Online - <span class="fw300">(<?=count($online_admins)?>)</span>
           </h4>
        </div>
     </div>
     <div class="panel-body">
       <table class="<?=$table_class?> table-success" data-toggle="table" data-height="350" data-mobile-responsive="true">
          <thead>
             <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Status</th>
             </tr>
          </thead>
          <tbody>
            <?php if(!empty($online_admins)){
                foreach ($online_admins as $row) {
            ?>
              <tr>
                <td><?=checkIsset($adminsArr[$row["app_user_id"]]["display_id"])?></td>
                <td><?=checkIsset($adminsArr[$row["app_user_id"]]["name"])?></td>
                <td>Active</td>
              </tr>
            <?php      
                }
              }
            ?>
          </tbody>
       </table>
       <div class="text-center m-t-30">
          <a href="javascript:void(0);" class="btn red-link pn" onclick='parent.$.colorbox.close(); return false;'>Close</a>
       </div>
     </div>
  </div>

<?php }else if($action == "idle_admins"){ ?>
  <div class="panel panel-default">
     <div class="panel-heading">
        <div class="panel-title">
           <h4 class="mn">
              <i class="fa fa-circle text-warning" aria-hidden="true"></i>&nbsp;
              Admin<span class="text-lowercase">(s)</span> Idle - <span class="fw300">(<?=count($idle_admins)?>)</span>
           </h4>
        </div>
     </div>
     <div class="panel-body">
       <table class="<?=$table_class?> table-warning" data-toggle="table" data-height="350" data-mobile-responsive="true">
          <thead>
             <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Status</th>
             </tr>
          </thead>
          <tbody>
            <?php if(!empty($idle_admins)){
                foreach ($idle_admins as $row) {
            ?>
              <tr>
                <td><?=checkIsset($adminsArr[$row["app_user_id"]]["display_id"])?></td>
                <td><?=checkIsset($adminsArr[$row["app_user_id"]]["name"])?></td>
                <td>Idle</td>
              </tr>
            <?php      
                }
              }
            ?>
          </tbody>
       </table>
       <div class="text-center m-t-30">
          <a href="javascript:void(0);" class="btn red-link pn" onclick='parent.$.colorbox.close(); return false;'>Close</a>
       </div>
     </div>
  </div>

<?php }else if($action == "active_chats"){ ?>
    <div class="panel panel-default">
     <div class="panel-heading">
        <div class="panel-title">
           <h4 class="mn">
              <i class="fa fa-circle text-info" aria-hidden="true"></i>&nbsp;
              Active Chats - <span class="fw300">(<?=count($live_conversations)?>)</span>
           </h4>
        </div>
     </div>
     <div class="panel-body">
       <table class="<?=$table_class?> table-info" data-toggle="table" data-height="350" data-mobile-responsive="true">
          <thead>
             <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Active Chats</th>
             </tr>
          </thead>
          <tbody>
            <?php if(!empty($livechatArr)){
                foreach ($livechatArr as $row) {
                  $selAdmin = "SELECT a.display_id,CONCAT(a.fname,' ',a.lname) as name
                  FROM $LIVE_CHAT_DB.sb_users u
                  JOIN admin a ON(a.id=u.app_user_id)
                  WHERE u.id=:userId";
                  $resAdmin = $pdo->selectOne($selAdmin,array(":userId" => $row["assignedId"]));
                  
            ?>
              <tr>
                <td><?=checkIsset($resAdmin["display_id"])?></td>
                <td><?=checkIsset($resAdmin["name"])?></td>
                <td><?=$row["chatCount"]?></td>
              </tr>
            <?php      
                }
              }
            ?>
          </tbody>
       </table>
       <div class="text-center m-t-30">
          <a href="javascript:void(0);" class="btn red-link pn" onclick='parent.$.colorbox.close(); return false;'>Close</a>
       </div>
     </div>
  </div>
<?php }else if($action == "in_queue_chats"){ ?>
  <div class="panel panel-default panel-block">
     <div class="panel-heading">
        <div class="panel-title">
           <h4 class="mn">
              <i class="fa fa-circle text-danger" aria-hidden="true"></i>&nbsp;
              Live Chat Queue - <span class="fw300">(<?=count($in_queue_conversations)?>)</span>
           </h4>
        </div>
     </div>
     <div class="panel-body">
       <table class="<?=$table_class?> table-danger">
          <thead>
             <tr>
                <th class="text-center">Members</th>
                <th class="text-center">Agents</th>
                <th class="text-center">Groups</th>
                <th class="text-center">Website</th>
             </tr>
          </thead>
          <tbody>
              <tr>
                <td class="text-center"><?=$membersQueue?></td>
                <td class="text-center"><?=$agentsQueue?></td>
                <td class="text-center"><?=$groupsQueue?></td>
                <td class="text-center"><?=$websiteQueue?></td>
              </tr>
          </tbody>
       </table>
       <div class="text-center m-t-30">
          <a href="javascript:void(0);" class="btn red-link pn" onclick='parent.$.colorbox.close(); return false;'>Close</a>
       </div>
     </div>
  </div>
  <script>
    $(document).ready(function(){
      parent.$.colorbox.resize({
        height:250,
        width:400,
      });
    });
  </script>

<?php }else if($action == "total_served_chat"){ ?>
  <div class="panel panel-default panel-block">
     <div class="panel-heading">
        <div class="panel-title">
           <h4 class="mn">
              <i class="fa fa-circle text-default" aria-hidden="true"></i>&nbsp;
              Total Served - <span class="fw300">(<?=count($served_conversations)?>)</span>
           </h4>
        </div>
     </div>
     <div class="panel-body">
       <table class="<?=$table_class?> table-default">
          <thead>
             <tr>
                <th class="text-center">Members</th>
                <th class="text-center">Agents</th>
                <th class="text-center">Groups</th>
                <th class="text-center">Website</th>
             </tr>
          </thead>
          <tbody>
              <tr>
                <td class="text-center"><?=$membersServed?></td>
                <td class="text-center"><?=$agentsServed?></td>
                <td class="text-center"><?=$groupsServed?></td>
                <td class="text-center"><?=$websiteServed?></td>
              </tr>
          </tbody>
       </table>
       <div class="text-center m-t-30">
          <a href="javascript:void(0);" class="btn red-link pn" onclick='parent.$.colorbox.close(); return false;'>Close</a>
       </div>
     </div>
  </div>
  <script>
    $(document).ready(function(){
      parent.$.colorbox.resize({
        height:250,
        width:400,
      });
    });
  </script>

<?php } ?>

