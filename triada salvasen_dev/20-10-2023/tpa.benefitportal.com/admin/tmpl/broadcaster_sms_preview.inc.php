
  <div class="panel panel-default panel-block">

    <div class="panel-heading br-n">
      <div class="panel-title">
        <h4 class="fs18 mn">SMS Content -  <span class="fw300"><?=$broadcastName?></span></h4>
      </div>
    </div>

    <div class="panel-body p-t-0">
      <?php if(!empty($msgRes)){ ?>

        <ul class="nav nav-tabs tabs customtab nav-noscroll" role="tablist">
        <?php 
          $msgCnt = 1;
          foreach ($msgRes as $key => $msg) {
        ?>
          <li role="presentation" <?=($msgCnt == 1 ? 'class="active"' : '')?>><a href="#msg<?=$msg['id']?>" role="tab" data-toggle="tab">Message <?=$msgCnt?></a></li>

        <?php
            $msgCnt++;
          } 
        ?>
        </ul>

        <div class="bg_light_bg p-15">
          <div class="tab-content">
            <?php 
              $msgCnt = 1;
              foreach ($msgRes as $key => $msg) {
            ?>
            <div role="tabpanel" class="tab-pane <?=($msgCnt == 1 ? 'active' : '')?>" id="msg<?=$msg['id']?>"><?=$msg['message']?>
            </div>
            <?php
                $msgCnt++;
              } 
            ?>
          </div>
        </div>

    <?php }else{ ?>
        <h5>No Message(s) Found.</h5>
    <?php } ?>

      <div class="clearfix  text-center m-t-30">
        <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Close</a>
      </div>

    </div>
  </div>


