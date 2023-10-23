<?php
include_once __DIR__ . '/includes/connect.php';

$breadcrumbes[0]['title'] = 'Quoting Engine';
$breadcrumbes[0]['link'] = 'quoting_engine.php';
$breadcrumbes[1]['title'] = 'Add Quote';
$breadcrumbes[1]['class'] = 'Active';

/*$template = 'lead_quote_enrollment_response.inc.php';*/
$template = 'lead_quote_expired.inc.php';
$layout = "iframe.layout.php";
include_once 'layout/end.inc.php';
?>