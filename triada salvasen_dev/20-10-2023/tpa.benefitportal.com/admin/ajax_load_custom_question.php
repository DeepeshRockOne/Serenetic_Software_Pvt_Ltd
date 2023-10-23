<?php
include_once 'layout/start.inc.php';
 
$product_id=!empty($_POST['manage_product_id']) ? $_POST['manage_product_id'] : 0;
$record_type=!empty($_POST['record_type']) ? $_POST['record_type'] : 0;

$customQuestionSql="SELECT id,display_label,
is_member_asked,is_member_required,
is_spouse_asked,is_spouse_required,
is_child_asked,is_child_required,
md5(id) as secureID FROM prd_enrollment_questions WHERE questionType='Custom' and is_deleted='N'";
$customQuestionRes=$pdo->select($customQuestionSql);


$sqlProduct="SELECT id,prd_question_id,is_member_agreement,
is_member_asked,is_member_required,
is_spouse_asked,is_spouse_required,
is_child_asked,is_child_required
FROM prd_enrollment_questions_assigned where md5(product_id)=:id and is_deleted='N'";
$resProduct=$pdo->selectGroup($sqlProduct,array(":id"=>$product_id),'prd_question_id');


ob_start();
if(!empty($customQuestionRes)) { 
    foreach ($customQuestionRes as $key => $value) { ?>
    	<?php
    		$prd_question_id = $value['id'];
      $is_member_agreement = !empty($resProduct[$prd_question_id][0]['is_member_agreement']) ? $resProduct[$prd_question_id][0]['is_member_agreement'] : 'N';
			$is_member_asked = !empty($resProduct[$prd_question_id][0]['is_member_asked']) ? $resProduct[$prd_question_id][0]['is_member_asked'] : 'N';
			$is_member_required = !empty($resProduct[$prd_question_id][0]['is_member_required']) ? $resProduct[$prd_question_id][0]['is_member_required'] : 'N';
			$is_spouse_asked = !empty($resProduct[$prd_question_id][0]['is_spouse_asked']) ? $resProduct[$prd_question_id][0]['is_spouse_asked'] : 'N';
			$is_spouse_required = !empty($resProduct[$prd_question_id][0]['is_spouse_required']) ? $resProduct[$prd_question_id][0]['is_spouse_required'] : 'N';
			$is_child_asked = !empty($resProduct[$prd_question_id][0]['is_child_asked']) ? $resProduct[$prd_question_id][0]['is_child_asked'] : 'N';
			$is_child_required = !empty($resProduct[$prd_question_id][0]['is_child_required']) ? $resProduct[$prd_question_id][0]['is_child_required'] : 'N';
    	?>
      	<tr>
            <td class="text-center table_light_danger">
              <label class="mn red_checkbox">
                <input data-que-id="<?= $prd_question_id ?>" id="agreementCustomQuestion_<?= $prd_question_id ?>" class="customQuestion <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="MemberEnrollmentInformation" name="agreementCustomQuestion[<?= $prd_question_id ?>][agreement]" type="checkbox" value="<?= $value['id'] ?>" <?= $is_member_agreement=='Y' ? 'checked' : '' ?> />
              </label>
            </td>
            <td class="bg_white pn"></td>
            <td class="text-center table_light_danger">
              <label class="mn red_checkbox">
                <input data-que-id="<?= $prd_question_id ?>" id="memberCustomQuestion_<?= $prd_question_id ?>_asked" class="customQuestion <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="MemberEnrollmentInformation" name="memberCustomQuestion[<?= $prd_question_id ?>][asked]" type="checkbox" value="<?= $value['id'] ?>" <?= $is_member_asked=='Y' ? 'checked' : '' ?> />
              </label>
            </td>
            <td class="text-center table_light_danger">
              <label class="mn red_checkbox">
                <input data-que-id="<?= $prd_question_id ?>" id="memberCustomQuestion_<?= $prd_question_id ?>_required" class="memberCustom_details_required customQuestion <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="MemberEnrollmentInformation" name="memberCustomQuestion[<?= $prd_question_id ?>][required]" type="checkbox" value="<?= $value['id'] ?>" <?= $is_member_required=='Y' ? 'checked' : '' ?> />
              </label>
            </td>
            <td class="bg_white pn"></td>

            <td class="text-center table_light_danger">
              <label class="mn red_checkbox">
                <input data-que-id="<?= $prd_question_id ?>" id="spouseCustomQuestion_<?= $prd_question_id ?>_asked" class="customQuestion <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="MemberEnrollmentInformation" name="spouseCustomQuestion[<?= $prd_question_id ?>][asked]" type="checkbox" value="<?= $value['id'] ?>" <?= $is_spouse_asked=='Y' ? 'checked' : '' ?>/>
              </label>
            </td>
            <td class="text-center table_light_danger">
              <label class="mn red_checkbox">
                <input data-que-id="<?= $prd_question_id ?>" id="spouseCustomQuestion_<?= $prd_question_id ?>_required" class="spouseCustom_details_required customQuestion <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="MemberEnrollmentInformation" name="spouseCustomQuestion[<?= $prd_question_id ?>][required]" type="checkbox" value="<?= $value['id'] ?>" <?= $is_spouse_required=='Y' ? 'checked' : '' ?>/>
              </label>
            </td>
              
            <td class="bg_white pn"></td>

            <td class="text-center table_light_danger">
              <label class="mn red_checkbox">
                <input data-que-id="<?= $prd_question_id ?>" id="childCustomQuestion_<?= $prd_question_id ?>_asked" class="customQuestion <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="MemberEnrollmentInformation" name="childCustomQuestion[<?= $prd_question_id ?>][asked]" type="checkbox" value="<?= $value['id'] ?>" <?= $is_child_asked=='Y' ? 'checked' : '' ?> />
              </label>
            </td>
            <td class="text-center table_light_danger">
              <label class="mn red_checkbox">
                <input data-que-id="<?= $prd_question_id ?>" id="childCustomQuestion_<?= $prd_question_id ?>_required" class="dependentCustom_details_required customQuestion <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="MemberEnrollmentInformation" name="childCustomQuestion[<?= $prd_question_id ?>][required]" type="checkbox" value="<?= $value['id'] ?>" <?= $is_child_required=='Y' ? 'checked' : '' ?>/>
              </label>
            </td>
            <td>
                <?= $value['display_label'] ?>
            </td>
            <td class="icons text-center">
                <a href="javascript:void(0);" data-id="<?= $value['secureID'] ?>" class="view_custom_question_answers"><i class="fa fa-eye"></i></a>
            </td>
            <td class="icons text-right">
              <a href="javascript:void(0);" data-id="<?= $value['secureID'] ?>" class="add_custom_question"><i class="fa fa-edit"></i></a>
              <a href="javascript:void(0);" data-id="<?= $value['secureID'] ?>" class="delete_custom_question"><i class="fa fa-trash"></i></a>
            </td>

      	</tr>
  	<?php } ?>
<?php }else{ ?>
  <tr><td colspan="11" class="text-center">No Question Added</td></tr>
<?php } 
  
$result = array();	
$result['html'] = ob_get_clean();
$result['status'] = "success"; 
  
header('Content-type: application/json');
echo json_encode($result);
dbConnectionClose(); 
exit;
?>