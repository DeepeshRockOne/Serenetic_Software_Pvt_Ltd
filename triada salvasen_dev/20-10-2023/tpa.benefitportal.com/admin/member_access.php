<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$member_menu = has_menu_access(8);
$ticket_menu = has_menu_access(25);
$order_menu = has_menu_access(12);

$member_search = isset($_GET['member_search']) ? $_GET['member_search'] : '';
$order_search = isset($_GET['order_search']) ? $_GET['order_search'] : '';
$ticket_search = isset($_GET['ticket_search']) ? $_GET['ticket_search'] : '';
$member_multiple_search = isset($_GET['member_multiple_search']) ? $_GET['member_multiple_search'] : '';
$is_ajax = isset($_GET['is_ajax']) ? $_GET['is_ajax'] : '';
$page_title = "Dashboard";

$admin_reminder = $pdo->selectOne("SELECT * FROM admin_reminder WHERE admin_id = :id AND is_deleted = 'N'", array(":id" => $_SESSION['admin']['id']));

if (isset($_GET["is_ajax"])) {
    // pre_print($_GET, false);
    include 'tmpl/member_access.inc.php';
    exit;
}

$exStylesheets = array();
$exStylesheets = array('thirdparty/bootstrap-tagsinput-master/bootstrap-tagsinput.css');
$exJs = array('thirdparty/bootstrap-tagsinput-master/bootstrap-tagsinput.min.js','thirdparty/clipboard/clipboard.min.js', 'thirdparty/sweetalert/jquery.sweet-alert.custom.js','thirdparty/masked_inputs/jquery.maskedinput.min.js');

$template = 'member_access.inc.php';
include_once 'layout/end.inc.php';
?>