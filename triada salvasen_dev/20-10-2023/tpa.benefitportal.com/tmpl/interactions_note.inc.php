<div class="panel panel-default">
  <div class="panel-body">
    <p class="fs18 mn"><strong class="fw600"> <?=$interaction['customer_name']?> </strong> - <span class="red-link"> <?=$interaction['rep_id']?> </span></p>
    <p class="fs12 mn text-blue"> <?=$tz->getDate($interaction['created_at'])?></p>
  </div>
</div>
<div class="panel panel-default panel-block add_note_panel">
  <div class="panel-body" style="padding-top:0px;">
    <div class="panel-heading"><?=$interaction['type']?></div>
    <div class="custom_edit_notes_area">
      <div class="panel panel-default">
        <div class="panel-body">
          <div class="form-group">
          <p><strong>Type :</strong> <span class="text-light-gray"><?=$interaction['type']?></span></p>
          <p class="m-b-20"><strong>Associated Product(s) :</strong> <span class="text-light-gray"><?=$interaction['products']?></span></p>
            <textarea class="form-control" rows="7" placeholder="Notes" readonly="readonly" ><?=$interaction['description']?></textarea>
          </div>
          <div class="clearfix">
            <div class="pull-right">
              <div class=" fs12 "><em><strong class="fw600">Created by : </strong></em> <?=$interaction['admin_name']?> - <?=$interaction['display_id']?></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
