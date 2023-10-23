<?php 
class circleChat{

    public $circleLst;
    public function __construct(){
        $this->pdo = '';
        $this->circleLst = array();
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getchatCircleName($circle_id){

        $selChat = "SELECT ac.name FROM admin_circle ac WHERE ac.is_deleted='N' AND ac.status='Active' AND id=:id";

        return $this->getOneCircle($selChat,array(":id"=>$circle_id));

    }

    public function getAssignedAdminStatus($admin_id=''){

        $status = 'Active';
        $selChat = "SELECT status FROM admin_circle_status  WHERE is_deleted='N' AND admin_id=:id";
        $resstatus = $this->getOneCircle($selChat,array(":id"=>$admin_id));
        if(!empty($resstatus['status'])){
            $status = $resstatus['status'];
        }
        return $status;
    }

    public function getchatCircleList($admin_id = ''){
        
        $incr = '';
        $sch_param = array();
        if(!empty($admin_id)){
            $sch_param[":id"] = $admin_id;
            $incr .= " AND aac.admin_id =:id  ";
        }
       
        $selChat = "SELECT ac.id,ac.id AS ids,ac.name ,ac.status,max(acc.id) as accId,aac.admin_id
                    FROM admin_circle ac 
                    JOIN assigned_admin_circle aac ON(aac.circle_id=ac.id AND aac.is_deleted='N')
                    LEFT JOIN admin_circle_chat acc ON(
                            acc.circle_id=ac.id AND 
                            acc.is_deleted='N'
                        )
                    LEFT JOIN admin a ON(a.id=acc.sender_admin_id)
                    WHERE ac.is_deleted='N' AND ac.status='Active'  $incr GROUP BY ids ORDER BY accId DESC";

       return $this->getCircles($selChat,$sch_param);

    }

    public function getCircleAdminList($circle_id){

        $selChat = "SELECT a.fname,a.lname,a.display_id,a.id,acs.status
                    FROM admin_circle ac 
                    JOIN assigned_admin_circle aac ON(aac.circle_id=ac.id AND aac.is_deleted='N')
                    LEFT JOIN admin_circle_status acs ON(acs.admin_id=aac.admin_id AND acs.is_deleted='N')
                    LEFT JOIN admin a ON(a.id=aac.admin_id)
                    WHERE ac.is_deleted='N' AND ac.status='Active' AND ac.id=:id ";

       return $this->getCircles($selChat,array(":id"=>$circle_id));
    }

    public function getchatCircleChats($circle_id){
        $selChat = "SELECT acc.id as message_id,ac.id as id,sender_admin_id,message,acf.id as file_id,acf.file,acf.file_name,acc.created_at
        FROM admin_circle ac 
        JOIN assigned_admin_circle aac ON(aac.circle_id=ac.id AND aac.is_deleted='N')
        JOIN admin_circle_chat acc ON(
                acc.circle_id=ac.id AND 
                acc.is_deleted='N' AND 
                acc.sender_admin_id IN(aac.admin_id)
                AND acc.id >= (SELECT max(id) - 25 FROM admin_circle_chat where circle_id=:id AND is_deleted='N')
            )
        LEFT JOIN admin_circle_message_files acf ON(acf.circle_id=ac.id AND acf.circle_chat_id=acc.id AND acf.is_deleted='N')
        WHERE ac.is_deleted='N' AND ac.status='Active' AND ac.id=:id order by message_id ASC";

        return $this->getCircles($selChat,array(":id"=>$circle_id));
    }
    /**
     * get Old record by scrolling 
     *
     * @param Integer $circle_id
     * @param Integer $oldest_chat_id
     * @return void
     */
    public function getOldestCircleChat($circle_id,$oldest_chat_id){
        $selChat = "SELECT acc.id as message_id,ac.id as id,sender_admin_id,message,acf.id as file_id,acf.file,acf.file_name,acc.created_at
        FROM admin_circle ac 
        JOIN assigned_admin_circle aac ON(aac.circle_id=ac.id AND aac.is_deleted='N')
        JOIN admin_circle_chat acc ON(
                acc.circle_id=ac.id AND 
                acc.is_deleted='N' AND 
                acc.sender_admin_id IN(aac.admin_id)
                -- AND acc.id >= (SELECT MIN(id) as id FROM admin_circle_chat WHERE circle_id=:id AND is_deleted='N')
                AND acc.id >= (SELECT MIN(id) as id FROM admin_circle_chat WHERE circle_id=:id AND is_deleted='N') AND acc.id < $oldest_chat_id
            )
        LEFT JOIN admin_circle_message_files acf ON(acf.circle_id=ac.id AND acf.circle_chat_id=acc.id AND acf.is_deleted='N')
        WHERE ac.is_deleted='N' AND ac.status='Active' AND ac.id=:id order by message_id ASC LIMIT 25";

        return $this->getCircles($selChat,array(":id"=>$circle_id));
    }

    public function getUnreadCircleChat($circle_id){
        $selChat = "SELECT ar.unread_message
                    FROM admin_circle ac 
                    JOIN assigned_admin_circle aac ON(aac.circle_id=ac.id AND aac.is_deleted='N')
                    JOIN admin_circle_chat acc ON(
                            acc.circle_id=ac.id AND 
                            acc.is_deleted='N' AND 
                            acc.sender_admin_id IN(aac.admin_id)
                        )
                    LEFT JOIN admin_circle_chat_read ar ON(ar.circle_id=ac.id AND ar.admin_id=acc.sender_admin_id AND ar.is_deleted='N')
                    WHERE ac.is_deleted='N' AND ac.status='Active' AND ac.id=:id AND aac.admin_id=:admin_id";

       return $this->getOneCircle($selChat,array(":id"=>$circle_id,":admin_id"=>$_SESSION['admin']['id']));
    }

    public function getNewchatCircleChats($circle_id,$message_id){
        $selChat = "SELECT ar.unread_message,acc.id as message_id,ac.id,ac.name,acc.sender_admin_id,acc.message,acf.id as file_id,acf.file,acf.file_name,acc.created_at
                    FROM admin_circle ac 
                    JOIN assigned_admin_circle aac ON(aac.circle_id=ac.id AND aac.is_deleted='N')
                    JOIN admin_circle_chat acc ON(
                            acc.circle_id=ac.id AND 
                            acc.is_deleted='N' 
                        )
                    LEFT JOIN admin_circle_chat_read ar ON(ar.circle_id=ac.id AND ar.admin_id=acc.sender_admin_id AND ar.is_deleted='N')
                    LEFT JOIN admin_circle_message_files acf ON(acf.circle_id=ac.id AND acf.circle_chat_id=acc.id AND acf.is_deleted='N')
                    WHERE ac.is_deleted='N' AND ac.status='Active' AND ac.id=:id AND acc.id > :msg_id GROUP BY acc.id";

       return $this->getCircles($selChat,array(":id"=>$circle_id,":msg_id"=>$message_id));
    }

    private function getCircles($selQuery,$where=array()){

        $this->circleLst = $this->pdo->select($selQuery,$where);
        return $this->circleLst;

    }

    private function getOneCircle($selQuery,$where=array()){

        $this->circleLst = $this->pdo->selectOne($selQuery,$where);
        return $this->circleLst;

    }

    public function sendCircleMessage($circle_id,$message,$fileArr = array()){

        $ins_param = array(
            "circle_id" => $circle_id,
            "sender_admin_id" => $_SESSION['admin']['id'],
            'message' => htmlspecialchars_decode($message)
        );
        $this->sendUnreadCircleMessage($circle_id);
        $message_id = $this->pdo->insert('admin_circle_chat',$ins_param);

        if(!empty($fileArr)){
            $this->sendCircleChatFile($circle_id,$message_id,$fileArr);
        }
        return $message_id;

    }

    public function sendCircleChatFile($circle_id,$message_id,$fileArr){

        global $CIRCLE_DOCUMENT_DIR;

        $ins_file_param = array(
            "circle_id" => $circle_id,
            "circle_chat_id" => $message_id,
            'file' => date('mdYhisa').$fileArr['name'],
            'file_name' =>$fileArr['name'],
        );
        $name = basename($ins_file_param['file']);
        move_uploaded_file($fileArr['tmp_name'], $CIRCLE_DOCUMENT_DIR.$name);

        $message_id = $this->pdo->insert('admin_circle_message_files',$ins_file_param);

    }

    public function sendUnreadCircleMessage($circle_id){

        $adminList = $this->getCircleAdminList($circle_id);

        if(!empty($adminList)){
            foreach($adminList as $admin){
                $ins_param1 = array(
                    "circle_id" => $circle_id,
                    "admin_id" => $admin['id'],
                    'unread_message' => 'msqlfunc_unread_message + 1'
                );
                $check = $this->getOneCircle("SELECT id from admin_circle_chat_read where circle_id=:circle_id and admin_id=:admin_id AND is_deleted='N'",array(":circle_id"=>$circle_id,":admin_id"=>$admin['id']));
                if($admin['id'] != $_SESSION['admin']['id']){
                    if(!empty($check['id'])){
                        $where = array(
                            "clause"=>"id=:id",
                            "params"=>array(":id"=>$check['id'])
                        );
                        $this->pdo->update("admin_circle_chat_read",$ins_param1,$where);
                    }else{
                        $this->pdo->insert("admin_circle_chat_read",$ins_param1);
                    }
                }
            }
        }

    }
    public function updateCirclemessageToRead($circle_id){
        $where = array(
            "clause"=>" admin_id=:admin_id AND circle_id=:circle_id " ,
            "params"=>array(
                ":admin_id"=>$_SESSION['admin']['id'],
                ":circle_id" => $circle_id
            )
        );
        $this->pdo->update("admin_circle_chat_read",array("unread_message"=>0),$where);
    }

    public function getTotalNewMessage(){
        return $this->getOneCircle("SELECT SUM(unread_message) as totalMes FROM admin_circle_chat_read where is_deleted='N' and admin_id=:admin_id",array(":admin_id"=>$_SESSION['admin']['id']));
    }

    public function getCircleMessageFile($circle_id,$messageId){
        return $this->getOneCircle("SELECT id as file_id,file,file_name FROM admin_circle_message_files where circle_id=:circle_id AND circle_chat_id=:message_id AND is_deleted='N'",array(":circle_id"=>$circle_id,":message_id"=>$messageId));
    }

}
?>