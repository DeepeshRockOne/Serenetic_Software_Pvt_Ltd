<?php
include_once dirname(__FILE__) . '/includes/connect.php';
$sub_resource_id = !empty($_GET['resourse']) ? $_GET['resourse'] : '';

$sqlResourse = "SELECT sr.coll_doc_url,video_type FROM sub_resources sr WHERE md5(sr.id) =:sub_resource_id";
$resResourse = $pdo->selectOne($sqlResourse,array(":sub_resource_id"=>$sub_resource_id));

if(!empty($resResourse)){
?>
<div id="view_video">
	<div class="panel panel-default panel-block panel-shadowless mn">
	<div class="panel-heading br-b">
		<div class="panel-title">
			<p class="fs18 mn">&nbsp;</p>
		</div>
	</div>
	<div id="viewPageBody" style="line-height: 0px;">
	</div>
	<div  id="copyPageBodyWistia" <?=$resResourse['video_type'] == 'Wistia' ? '' : 'style="display:none"'?>>
		<div class="wistia_responsive_padding" style="padding:56.25% 0 0 0;position:relative;">
			<div class="wistia_responsive_wrapper" style="height:100%;left:0;position:absolute;top:0;width:100%;">
				<div class="wistia_embed wistia_async_<?=$resResourse['coll_doc_url']?> seo=false videoFoam=true" style="height:100%;width:100%">&nbsp;
				</div>
			</div>
		</div>
	</div>
	<div id="copyPageBodyTube" <?=$resResourse['video_type'] == 'Youtube' ? '' : 'style="display:none"'?> >
		<iframe  width="100%" height="515" src="https://www.youtube.com/embed/<?=$resResourse['coll_doc_url']?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</div>
	</div>
</div>
<script type="text/javascript" src="<?=$HOST?>/thirdparty/wistia/wistia.js"></script>
<?php }else echo "Url Not found!"?>