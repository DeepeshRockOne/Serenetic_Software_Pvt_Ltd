<?php
include dirname(__DIR__) . "/includes/connect.php";
include dirname(__DIR__) . "/includes/redisCache.class.php";

$redisCache = new redisCache();

$redisCache->getOrGenerateCache('All','sales_bar_chart_data_admin','add');
$redisCache->getOrGenerateCache('All','sales_bar_chart_data_new_business','add');
$redisCache->getOrGenerateCache('All','sales_bar_chart_data_renewal','add');
$redisCache->getOrGenerateCache('All','renewal_summary_admin','add');

echo "completed";
exit;
?>