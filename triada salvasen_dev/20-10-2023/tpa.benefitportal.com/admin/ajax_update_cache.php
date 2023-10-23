<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
require_once dirname(__DIR__) . '/includes/redisCache.class.php';
	$update_where = array(
			'clause' => 'id=:id',
			'params' => array(':id' => 1),
		);
	$update_params=array("version"=>"msqlfunc_version+0.01");
	$pdo->update("cache_management",$update_params,$update_where);
	unlink($CACHE_PATH_DIR.$CACHE_FILE_NAME);
	
	if($SITE_ENV != 'Local'){
		$redisCache = new redisCache();
		$redisCache->getOrGenerateCache('All','Product','add');
	}else{
		unlink($CACHE_PATH_DIR.$PRODUCT_CACHE_FILE_NAME);
	}
$cache_row = $pdo->selectOne("SELECT * from cache_management where id=1");
if (!$cache_row) {
	$pdo->insert("cache_management", array("id" => 1, "version" => 1, "updated_at" => "msqlfunc_NOW()"));
	$cache_row = $pdo->selectOne("SELECT * from cache_management where id=1");
}
echo 'Completed';
exit;
?>