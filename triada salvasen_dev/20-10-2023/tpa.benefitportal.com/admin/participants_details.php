<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'User Group';
$breadcrumbes[2]['title'] = 'Participants';
$breadcrumbes[2]['link'] = 'participants_listing.php';
// redirect("participants_listing.php");
// exit;
$tz = new UserTimeZone('m/d/Y @ g:i A T', $_SESSION['admin']['timezone']);
$prdPlanTypeArray = $cacheMainArray['prdPlanTypeArray'];

$id = checkIsset($_GET['id']);
$participants_id = $id;
$user_type = "Participants";

$select = "SELECT md5(p.id) as ptId,p.*,AES_DECRYPT(p.ssn,'" . $CREDIT_CARD_ENC_KEY . "') as pssn
		    FROM participants p
		    WHERE md5(p.id)=:id";
$where = array(':id' => makeSafe($participants_id));
$row = $pdo->selectOne($select, $where);

$pssn = checkIsset($row["pssn"]);

if (empty($row)) {
    setNotifyError('Participants does not exist');
    redirect("participants_listing.php");
} else {
    if (!isset($_GET['pages'])) {
        $note_search_keyword = isset($_REQUEST['note_search_keyword']) ? $_REQUEST['note_search_keyword'] : '';
        $note_search_keyword = cleanSearchKeyword($note_search_keyword);  
        $note_ajax = isset($_POST['note_ajax']) ? $_POST['note_ajax'] : "";
        $extra_params = array();
        $extra_params['note_search_keyword'] = $note_search_keyword;
        $notes_res = get_note_section_data('admin',$participants_id,'participants',$extra_params);

        if ($note_search_keyword !== '' || $note_ajax == 'Y') {
            $note_desc = " <div class='activity_wrap_note activity_wrap'>";
            if (count($notes_res) > 0) {
                foreach ($notes_res as $note) {
                    $note_desc .= '<div class="media">';
                    $note_desc .= '<div class="media-body fs14 br-n"><p class="text-light-gray mn">';
                    $note_desc .= $tz->getDate($note['created_at'], 'D., M. d, Y @ h:i A') . "</p>";

                    $note_desc .= '<p class="mn">' . note_custom_charecter('admin','lead',$note['description'], 400,$note['added_by_name'],$note['added_by_rep_id'],$note['added_by_id'],$note['added_by_detail_page']) . '</p></div>';

                    $note_desc .= '<div class="media-right text-nowrap">';
                    $note_desc .= '<a href="javascript:void(0);" id="edit_note_id" data-original-title="Edit" onclick=edit_note(' . $note['note_id'] . ',' . '"view"' . ') data-value="Lead"><i class="fa fa-eye fa-lg"></i></a> &nbsp;';
                    $note_desc .= '<a href="javascript:void(0);" class="" id="edit_note_id" data-original-title="Edit" onclick="edit_note(' . $note['note_id'] . ','."''".')" data-value="Lead"><i class="fa fa-edit fa-lg"></i></a> &nbsp;';
                    $note_desc .= '<a href="javascript:void(0);" class="" id="delete_note_id" data-original-title="Delete" onclick="delete_note(' . $note['note_id'] . ',' . $note['ac_id'] . ')" data-value="Lead"><i class="fa fa-trash fa-lg"></i></a>&nbsp;';
                    $note_desc .= "</div></div>";
                }
            } else {
                $note_desc .= '<p class="text-center mn"> No Notes Found. </p>';
            }
            $note_desc .= "</div>";
            echo $note_desc;
            exit;
        }
    }


    $prdSel = "SELECT pp.*
            FROM participants p
            JOIN participants_products pp ON(p.id=pp.participants_id AND pp.is_deleted='N')
            WHERE md5(p.id)=:id AND p.is_deleted='N'";
    $prdWhere = array(':id' => $row["ptId"]);
    $prdRes = $pdo->select($prdSel, $prdWhere);

    $description['ac_message'] = array(
        'ac_red_1' => array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
        	'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' => ' read Participants ' . $row['fname'] . ' ' . $row['lname'] . ' (',
        'ac_red_2' => array(
            'href' => 'participants_details.php?id=' . $id,
            'title' => $row['participants_id'],
        ),
        'ac_message_2' => ')',
    );
    $desc = json_encode($description);
    activity_feed(3, $row['id'], 'Participants', $_SESSION['admin']['id'], 'Admin', 'Admin Read Participants Details.', $_SESSION['admin']['fname'], $_SESSION['admin']['lname'], $desc);
}

$exStylesheets = array(
    'thirdparty/multiple-select-master/multiple-select.css'.$cache,
);
$exJs = array(
    'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
    'thirdparty/formatCurrency/jquery.formatCurrency-1.4.0.js',
    'thirdparty/jquery-match-height/js/jquery.matchHeight.js',
    'thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache,
);

$template = 'participants_details.inc.php';
include_once 'layout/end.inc.php';
?>
