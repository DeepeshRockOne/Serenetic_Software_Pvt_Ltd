<?php
require_once dirname(__DIR__) . "/includes/config.inc.php";
require_once dirname(__DIR__) . "/includes/config_variables.inc.php";

$cache = '';
if (file_exists($CACHE_PATH_DIR.$CACHE_FILE_NAME)) {
	$cacheMainArray = unserialize(file_get_contents($CACHE_PATH_DIR.$CACHE_FILE_NAME));
	$cache = $cacheMainArray['cache'];
}

/*
 * ==========================================================
 * INITIAL CONFIGURATION FILE
 * ==========================================================
 *
 * Insert here the information for the database connection and for other core settings.
 *
 */

/* Plugin folder url */
define('SITE_CACHE',$cache);

define('DEFAULT_CHAT_HEADER_IMAGE',$HOST.'/'.$DEFAULT_CHAT_LOGO);

define('SB_URL',$LIVE_CHAT_HOST);

/* The name of the database */
define('MAIN_DB_NAME',$DATABASENAME);

define('SB_DB_NAME',$LIVE_CHAT_DB);

/* MySQL database username */
define('SB_DB_USER',$USERNAME);

/* MySQL database password */
define('SB_DB_PASSWORD',$PASSWORD);

/* MySQL hostname */
define('SB_DB_HOST',$DBSERVER);

/* MySQL port (optional) */
define('SB_DB_PORT','');

define('SB_UPLOAD_URL', $LIVE_CHAT_UPLOADS_WEB);
define('SB_UPLOAD_PATH', $LIVE_CHAT_UPLOADS_DIR);

/* [extra] */

?>