
  <div class="panel panel-default panel-block">

    <div class="panel-heading br-n">
      <div class="panel-title">
        <h4 class="fs18 mn">Preview -  <span class="fw300">Trigger</span></h4>
      </div>
    </div>

    <div class="panel-body p-t-0">

       <ul class="nav nav-tabs tabs customtab nav-noscroll" role="tablist">
          <li role="presentation" class="active"><a href="#emailPreviewContent" aria-controls="email_content" role="tab" data-toggle="tab">Email Content</a></li>
          <li role="presentation"><a href="#smsPreviewContent" aria-controls="smsContentTab" role="tab" data-toggle="tab">SMS Content</a></li>
        </ul>

      <div class="bg_light_bg p-15">
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="emailPreviewContent"><?=$prevEmailContent?>
          </div>
          <div role="tabpanel" class="tab-pane" id="smsPreviewContent"><?=$prevSMSContent?>
          </div>
        </div>
      </div>

      <div class="clearfix  text-center m-t-30">
        <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Close</a>
      </div>

    </div>
  </div>


