<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$agent_csv_id = isset($_GET['id']) ? $_GET['id'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

$file_row = $pdo->selectOne("SELECT * FROM csv_agent_leads WHERE md5(id)=:id",array(':id' => $agent_csv_id));

$sch_params = array();
$SortBy = "created_at";
$SortDirection = "DESC";
$currSortDirection = "ASC";
$incr = '';
$has_querystring = false;

if (isset($_GET["sort_by"]) && $_GET["sort_by"] != "") {
    $has_querystring = true;
    $SortBy = $_GET["sort_by"];
}

if (isset($_GET["sort_direction"]) && $_GET["sort_direction"] != "") {
    $has_querystring = true;
    $currSortDirection = $_GET["sort_direction"];
}

if ($agent_csv_id != "") {
    $sch_params[':id'] = makeSafe($agent_csv_id);
    $incr .= " AND md5(agent_csv_id) = :id";
}

if (count($sch_params) > 0) {
    $has_querystring = true;
}

if (isset($_GET['pages']) && $_GET['pages'] > 0) {
    $has_querystring = true;
    $per_page = $_GET['pages'];
}
$query_string = $has_querystring ? (isset($_GET['page']) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
    'show_links_first_last' => false,
    'results_per_page' => 50,
    'url' => 'csv_error_log.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = isset($_GET['page']) &&  $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

try {

    $sel_sql = "SELECT * FROM agent_csv_log WHERE is_deleted = 'N' " . $incr . " ";
    $paginate = new pagination($page, $sel_sql, $options);
    if ($paginate->success == true) {
        $fetch_rows = $paginate->resultset->fetchAll();
        $total_rows = count($fetch_rows);
    }
} catch (paginationException $e) {
    echo $e;
    exit();
}

/****************** Export Code Start ****************** */
if (isset($_GET["export"]) && (trim($_GET["export"]) == "export_excel")) {
    if ($_GET['export'] == 'export_excel') {
        $csv_line = "\n";
        $csv_seprator = ",";

        $sel_sql = "SELECT * FROM agent_csv_log WHERE is_deleted = 'N'  " . $incr . " ";
        $fails_report_rows = $pdo->select($sel_sql, $sch_params);

        if (count($fails_report_rows) > 0) {
            $content = 'Fail Reason' . $csv_seprator .
                'First Name' . $csv_seprator .
                'Last Name' . $csv_seprator .
                'Phone' . $csv_seprator .
                'Email' . $csv_seprator .
                $csv_line;
            foreach ($fails_report_rows as $rows) {
                $content .= $rows['reason'] . $csv_seprator .
                    $rows['fname'] . $csv_seprator .
                    $rows['lname'] . $csv_seprator .
                    $rows['cell_phone'] . $csv_seprator .
                    $rows['email'] . $csv_seprator .
                    $csv_line;
            }
        } else {
            setNotifyError("No record found");
            redirect('leads_import_summary.php', true);
        }

        if ($content) {
            $csv_filename = "lead_error_report_" . date("Ymd", time()) . ".csv";
            header('Content-type: application/csv');
            header('Content-disposition: attachment;filename=' . $csv_filename);
            echo $content;
            exit;
        }
    }
}
/* * ****************    Export Code End ******************** */

function is_value_empty($val, $type = '')
{
    if (!empty($val)) {
        if ($type == 'date' && $val != '0000-00-00') {
            return '-';
        } else {
            return $val;
        }
    }
    return '-';
}

$template = 'csv_error_log.inc.php';
$layout = "iframe.layout.php";
include_once 'layout/end.inc.php';
?>
