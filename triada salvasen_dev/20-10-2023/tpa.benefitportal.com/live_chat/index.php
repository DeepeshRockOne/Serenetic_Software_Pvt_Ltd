<?php

/*
 * ==========================================================
 * ADMINISTRATION PAGE
 * ==========================================================
 *
 * Administration page to manage the settings and reply to the users.
 *
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
global $SB_CONNECTION;
define('SB_PATH', getcwd());
if (!file_exists('config.php')) {
    $raw = str_replace(['[url]', '[name]', '[user]', '[password]', '[host]', '[port]'], '', file_get_contents('resources/config-source.php'));
    $file = fopen('config.php', 'w');
    fwrite($file, $raw);
    fclose($file);
}
require('config.php');
require('include/functions.php');
$connection_check = sb_db_check_connection();
$connection_success = $connection_check === true;
if ($connection_success) {
    sb_updates_check();
}
require('include/components.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no" />
    <meta name="theme-color" content="#566069" />
    <title>
        <?= $DEFAULT_CHAT_SITE_NAME ?>
    </title>
    <script src="<?= $LIVE_CHAT_HOST ?>/js/min/jquery.min.js"></script>
    <script src="<?= $LIVE_CHAT_HOST ?>/js/main.js<?php echo $cache; ?>"></script>
    <script src="<?= $LIVE_CHAT_HOST ?>/js/admin.js<?php echo $cache; ?>"></script>
    <link rel="stylesheet" type="text/css" href="<?= $LIVE_CHAT_HOST ?>/css/min/admin.min.css<?php echo $cache; ?>" media="all" />
    <link rel="shortcut icon" type="image/png" href="<?= $HOST ?>/images/favicon.ico" />
    <link rel="apple-touch-icon" href="<?= $HOST ?>/images/favicon.ico" />
    <link rel="manifest" href="<?= $LIVE_CHAT_HOST ?>/resources/pwa/manifest.json" />

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('sw.js').then(function (registration) {
                    registration.update();
                }).catch(function (error) {
                    console.log('Registration failed with ' + error);
                });
            });
        }
    </script>

    <?php
    if ($connection_success && sb_get_multi_setting('push-notifications', 'push-notifications-active') && sb_get_active_user()) {
        echo '<script src="https://js.pusher.com/beams/1.0/push-notifications-cdn.js"></script><script>window.navigator.serviceWorker.ready.then((serviceWorkerRegistration) => {
            const sb_beams_client = new PusherPushNotifications.Client({
                instanceId: "' . sb_get_multi_setting('push-notifications', 'push-notifications-id') . '",
                serviceWorkerRegistration: serviceWorkerRegistration,
            });
            sb_beams_client.start().then(() => sb_beams_client.setDeviceInterests(["' . sb_get_active_user()['id'] . '", "agents"])).catch(console.error);
        });</script>';
    }
    if ($connection_success) {
        sb_js_global();
        sb_js_admin();
    }
    ?>
</head>

<body>
    <?php
    if (!$connection_success) {
        sb_installation_box($connection_check);
        die();
    }
    sb_component_admin();
    ?>
</body>

</html>