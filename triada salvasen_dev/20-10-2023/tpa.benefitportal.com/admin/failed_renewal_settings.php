<?php
	include_once dirname(__FILE__) . '/layout/start.inc.php';

	$triggerSql="SELECT id,concat(id,' - ',title) as display_id FROM triggers WHERE is_deleted='N'";
	$resTrigger=$pdo->select($triggerSql);

	$resAttempt = $pdo->select("SELECT id,attempt,attempt_frequency,fail_trigger_id,admin_ticket FROM prd_subscription_attempt WHERE is_deleted='N'");
	$attemptCnt = !empty($resAttempt) ? count($resAttempt) : 1;

  include_once 'tmpl/failed_renewal_settings.inc.php';
  exit;
?>