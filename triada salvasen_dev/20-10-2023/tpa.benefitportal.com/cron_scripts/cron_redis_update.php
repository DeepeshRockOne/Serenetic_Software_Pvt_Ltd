<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time',0);
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
require_once dirname(__DIR__) . "/includes/redisCache.class.php";

if($SITE_ENV != 'Local'){
    $redisCache = new redisCache();
    $redisCache->getOrGenerateCache('All','Product','add');
}

echo "Completed";
exit;