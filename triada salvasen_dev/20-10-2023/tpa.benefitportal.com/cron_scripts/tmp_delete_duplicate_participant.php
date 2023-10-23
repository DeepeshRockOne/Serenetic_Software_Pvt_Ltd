<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__DIR__) . "/cron_scripts/connect.php";

if(!function_exists('delete_participants_cron')){
    function delete_participants_cron($participantArr,$type){
        global $pdo;
        $count = 0;
        foreach($participantArr as $participant){
            $updateParticipantParams = array(
                'is_deleted' => 'Y',
                'updated_at' => 'msqlfunc_NOW()'
            );
            $whereParticipant = array(
                'clause' => "id=:participant_id AND is_deleted='N'",
                'params' => array(
                    ":participant_id" => $participant['id']
                )
            );
    
            $pdo->update('participants',$updateParticipantParams,$whereParticipant);
    
            $participantProducts = !empty($participant['products_id']) ? $participant['products_id'] : '';
    
            if(!empty($participantProducts)){
    
                $updateProductParams = array(
                    'is_deleted' => 'Y',
                    'updated_at' => 'msqlfunc_NOW()'
                );
    
                $whereProduct = array(
                    'clause' => "is_deleted='N' AND participants_id =:participant_id AND id IN(".$participantProducts.")",
                    'params' => array(
                        ":participant_id" => $participant['id']
                    )
                );
                
                $pdo->update('participants_products',$updateProductParams,$whereProduct);
            }
    
            $insertParam = [
                'participant_id' => $participant['id'],
                'participants_product_ids' => !empty($participantProducts) ? $participantProducts : '',
                'type' => $type
            ];
            $pdo->insert("deleted_participants",$insertParam);

            if($count == 5000){
                sleep(1);
                $count=0;
            }
            $count++;
        }
    }
}

$sel_participant = "SELECT p2.id,GROUP_CONCAT(pp.id) AS products_id
                    FROM `participants` p1 
                    JOIN `participants` p2 ON(p2.employee_id = p1.employee_id AND CONCAT('0',p2.person_code) = p1.person_code AND p1.id != p2.id AND p2.is_deleted='N') 
                    LEFT JOIN participants_products pp ON(pp.participants_id=p2.id)
                    WHERE p1.is_deleted='N' 
                    GROUP BY p1.id";
$res_participant = $pdo->select($sel_participant);

if(!empty($res_participant)){
    delete_participants_cron($res_participant,'duplicate');
    echo "Completed Duplicate Participants.<br><br>";
}

//****************** Delete Participant for reseller code IN ("NAP","NAPP","AOBG","IMG","PHS") 
$sel_participant_reseller = "SELECT p.id,GROUP_CONCAT(pp.id) AS products_id 
                            FROM participants p 
                            LEFT JOIN participants_products pp ON(pp.participants_id=p.id AND pp.is_deleted='N')
                            WHERE  p.is_deleted = 'N' AND p.reseller_number IN('NAP','NAPP','AOBG','IMG','PHS') GROUP BY p.id";
$res_participant_reseller = $pdo->select($sel_participant_reseller);
if(!empty($res_participant_reseller)){
    delete_participants_cron($res_participant_reseller,'reseller');
    echo "Completed Duplicate Reseller Participants.<br><br>";
}

dbConnectionClose();
echo "Completed";
exit;
?>