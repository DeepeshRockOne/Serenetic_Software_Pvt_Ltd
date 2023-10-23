<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
group_has_access(9);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Manage Websites";
$breadcrumbes[1]['link'] = 'manage_website.php';
$breadcrumbes[2]['title'] = '+ Website';
$breadcrumbes[2]['class'] = "Active";

DEFINE("HEADER_CONTENT_LIMIT", 60);
DEFINE("SUBHEADER_CONTENT_LIMIT", 200);
$group_id = $_SESSION['groups']["id"];
$page_builder_id = isset($_GET["id"]) ? $_GET["id"]:'';
$is_clone = isset($_GET["is_clone"]) ? $_GET["is_clone"]:'';
if (empty($page_builder_id)) {
    $pb_sql = "SELECT id FROM page_builder WHERE agent_id=:group_id AND (cover_image='' OR cover_image IS NULL) AND is_deleted='N' ORDER BY id DESC";
    $pb_where = array(":group_id" => $group_id);
    $pb_row = $pdo->selectOne($pb_sql, $pb_where);
    if(!empty($pb_row)) {
        $page_builder_id = $pb_row['id'];
    } else {
        $page_builder_id = $pdo->insert("page_builder", array(
                "agent_id" => $group_id,
                "created_at" => "msqlfunc_NOW()",
                "updated_at" => "msqlfunc_NOW()",
            )
        );
    }
    redirect($GROUP_HOST."/page_builder.php?id=" . md5($page_builder_id));
    exit();
} else {
    $pb_where = array(":group_id" => $group_id, ":id" => $page_builder_id);
    $pb_sql = "SELECT * FROM page_builder WHERE agent_id=:group_id AND md5(id)=:id AND is_deleted='N'";
    $pb_row = $pdo->selectOne($pb_sql, $pb_where);
    if($is_clone == 'Y' && !empty($pb_row)){
        unset($pb_row['id']);
        unset($pb_row['user_name']);
        unset($pb_row['page_name']);
        unset($pb_row['updated_at']);
        unset($pb_row['created_at']);

        $pb_image_sql = "SELECT * FROM page_builder_images 
                WHERE  
                is_deleted='N' AND 
                (md5(page_builder_id)=:page_builder_id OR page_builder_id=0) 
                ORDER BY id DESC";
        $pb_image_res = $pdo->select($pb_image_sql,array(":page_builder_id" => $page_builder_id));

        $clone_array = array_keys($pb_row);

        $ins_param = array();

        foreach($clone_array as $key){
            $ins_param[$key] = $pb_row[$key];
        }
        $ins_param['status'] = 'Draft';
        $ins_param['agent_id'] = $group_id;
        $ins_param['updated_at'] = 'msqlfunc_NOW()';
        $ins_param['created_at'] = 'msqlfunc_NOW()';
        $page_builder_id = $pdo->insert("page_builder",$ins_param);
        foreach($pb_image_res as $pb_images){
            if (!empty($pb_images["image_name"]) && file_exists($PAGE_COVER_DIR . DIRECTORY_SEPARATOR . $pb_images["image_name"])) {
                $new_cover_image = md5(time() . rand(1, 10000)) . ".png";
                copy($PAGE_COVER_DIR . DIRECTORY_SEPARATOR .$pb_images["image_name"], $PAGE_COVER_DIR . DIRECTORY_SEPARATOR .$new_cover_image);
                $image_id = $pdo->insert("page_builder_images", array(
                    "page_builder_id" => $page_builder_id,
                    "image_name" => $new_cover_image,
                    "updated_at" => "msqlfunc_NOW()",
                    "created_at" => "msqlfunc_NOW()",
                ));
                if($pb_images['id'] == $pb_row['cover_image']){
                    $pdo->update('page_builder',array("cover_image"=>$image_id),array("clause"=>"id=:id","params"=>array(":id"=>$page_builder_id)));
                }
            }
        }
        
        redirect($GROUP_HOST."/page_builder.php?id=" . md5($page_builder_id));
        exit();
    }

    if(empty($pb_row)) {
        setNotifyError("Sorry! You have no rights to access this page.");
        redirect($GROUP_HOST . "/manage_website.php");
        exit();
    }
}

$page_builder_id = $pb_row["id"];

//all images
$pb_image_sql = "SELECT * FROM page_builder_images 
                WHERE  
                is_deleted='N' AND 
                (page_builder_id=:page_builder_id OR page_builder_id=0) 
                ORDER BY id DESC";
$pb_image_res = $pdo->select($pb_image_sql,array(":page_builder_id" => $page_builder_id));
//top hold
$cover_image = $pb_row["cover_image"];
$header_content = $pb_row["header_content"];
$header_subcontent = $pb_row["header_subcontent"];
$logo = $pb_row["logo"];

if ($header_content == "") {
    $header_content = "Guaranteed issue benefits to protect you and your family";
}

if ($header_subcontent == "") {
    $header_subcontent = "Peace of mind doesn't have to break the bank. Don't wait until it's too late. Help cover yourself and your family with affordable plan today.";
}

//products
$category_ids = $pb_row["category_ids"];
if (!empty($category_ids)) {
    $category_ids = explode(",", $category_ids);
} else {
    $category_ids = array();
}

$product_ids = $pb_row["product_ids"];
if (!empty($product_ids)) {
    $product_ids = explode(",", $product_ids);
} else {
    $product_ids = array();
}

$class_ids = $pb_row["class_ids"];
if (!empty($class_ids)) {
    $class_ids = explode(",", $class_ids);
} else {
    $class_ids = array();
}

//contact us
$page_name = $pb_row["page_name"];
$contact_us_emails = $pb_row["contact_us_emails"];
$user_name = $pb_row["user_name"];
$contact_us_phone_number = $pb_row["contact_us_phone_number"];


$sqlClass = "SELECT class_name,id FROM group_classes WHERE is_deleted='N' AND group_id=:group_id";
$resClass = $pdo->select($sqlClass,array(":group_id"=>$group_id));

$prd_sql = "SELECT p.id as product_id,p.name as product_name,p.category_id,pc.title as category_name,min(pm.price) as price
        FROM prd_main p
        JOIN agent_product_rule apr ON (p.id=apr.product_id AND apr.status ='Contracted' AND apr.is_deleted='N')
        JOIN prd_category pc ON (pc.id=p.category_id)
        JOIN prd_matrix pm ON(pm.product_id = p.id AND pm.is_deleted='N')
        LEFT JOIN prd_descriptions pd ON (pd.product_id = p.id)
        WHERE 
        p.status='Active' AND 
        p.type!='Fees' AND 
        p.is_deleted='N' AND 
        apr.agent_id = :group_id AND
        p.product_type IN ('Group Enrollment')
        GROUP BY p.id 
        ORDER BY category_name,product_name,price ASC";
$prd_where = array(":group_id" => $group_id);
$prd_res = $pdo->select($prd_sql,$prd_where);

$prd_category_res = array();
if(!empty($prd_res)) {
    foreach ($prd_res as $prd_row) {
        if(!isset($prd_category_res[$prd_row['category_id']])) {
            $prd_category_res[$prd_row['category_id']] = array(
                'category_id' => $prd_row['category_id'],
                'category_name' => $prd_row['category_name'],
                'prd_res' => array($prd_row),
            );
        } else {
            $prd_category_res[$prd_row['category_id']]['prd_res'][] = $prd_row;
        }
    }
}

$af_sql = "SELECT * from activity_feed WHERE entity_type='page_builder' AND entity_id=:entity_id AND entity_action='Website Created'";
$af_where = array(':entity_id' => $page_builder_id);
$af_row = $pdo->selectOne($af_sql,$af_where);
if(!empty($af_row)) {
    $desc = array();
    $desc['ac_message'] = array(
        'ac_red_1' => array(
            'href' => 'groups_details.php?id=' . md5($_SESSION['groups']['id']),
            'title' => $_SESSION['groups']['rep_id'],
        ),
        'ac_message_1' => ' read Website ',
        'ac_red_2' => array(
            'href' => 'page_builder.php?id='.md5($pb_row['id']),
            'href' => 'javascript:void(0);',
            'title' => $pb_row['page_name'],
        ),
    );
    $desc = json_encode($desc);
    activity_feed(3, $_SESSION['groups']['id'], 'Group', $pb_row['id'], 'page_builder', 'Read Website', $_SESSION['groups']['fname'], $_SESSION['groups']['lname'], $desc);
}

$exStylesheets = array(
        'thirdparty/multiple-select-master/multiple-select.css',
        'thirdparty/owl-carousel/owl.carousel.min.css',
        'thirdparty/cropper/dist/cropper.css',
        'thirdparty/summernote-master/dist/summernote.css'
    );

$exJs = array(
        'thirdparty/dropzone/dropzone.min.js',
        'thirdparty/masked_inputs/jquery.maskedinput.min.js',
        'thirdparty/multiple-select-master/jquery.multiple.select.js',
        'thirdparty/MaskedPassword/password_validation.js?v=15',
        'thirdparty/owl-carousel/owl.carousel.min.js',
        'js/scrollfix.js',
        'thirdparty/cropper/dist/cropper.js',
        "thirdparty/ckeditor/ckeditor.js",
        'thirdparty/summernote-master/dist/popper.js', 
        'thirdparty/summernote-master/dist/summernote.js'
    );
$template = 'page_builder.inc.php';
include_once 'layout/end.inc.php';
?>

