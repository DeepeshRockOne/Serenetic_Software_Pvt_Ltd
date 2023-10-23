<?php 
class adminCircle{

    public $activityArr , $paramArr;
    public $insertActivity , $deleteActivity , $updateActivity = false;
    public $circleName,$circleId = '';
    public $pdo = '';
    public function __construct(){
        global $pdo;
        $this->pdo = $pdo;
        $this->activityArr = array();
        $this->paramArr = array();
    }

    //Insert Admin circle
    public function insertAdminCircle($insertParam){

        $this->paramArr = $insertParam;
        $this->circleName = $insertParam['name'];
        $admin_arr = $insertParam['invite_admins'];
        unset($insertParam['invite_admins']);
        $circle_id = $this->pdo->insert('admin_circle',$insertParam);
        $this->circleId =  $circle_id;

        $this->insert_update_assigned_admin($circle_id,$admin_arr);
        $this->insertActivity = true;
        $this->adminCircleActivity();
    }
    //update Admin circle
    public function updateAdminCircle($circle_id,$updateArr){

        $this->paramArr = $updateArr;
        $this->circleName = $updateArr['name'];
        $this->circleId =  $circle_id;

        $where = array(
            "clause" => " id = :id ",
            "params" => array(
                    ":id" => $circle_id
            )
        );

        $admin_arr = $updateArr['invite_admins'];
        unset($updateArr['invite_admins']);

        $this->activityArr = $this->pdo->update('admin_circle',$updateArr,$where,true);
        $this->insert_update_assigned_admin($circle_id,$admin_arr);
        $this->updateActivity = true;
        $this->adminCircleActivity();
    }
    /**
     * Update Admin status
     *
     * @param Interger $admin_id
     * @param Array $updateArr
     * @return boolean
     */
    public function updateAdminStatus($admin_id,$updateArr){

        $old_status = 'Active';
        $status = false;
        $this->paramArr = $updateArr;
        $this->circleName = '';
        $this->circleId =  0;

        $selChat = "SELECT id FROM admin_circle_status  WHERE is_deleted='N' AND admin_id=:id";
        $resstatus = $this->pdo->selectOne($selChat,array(":id"=>$admin_id));
        if(!empty($resstatus['id'])){
            $where = array(
                "clause"=>" id=:id " ,
                "params"=>array(
                    ":id"=>$resstatus['id'],
                )
            );
            $this->activityArr = $this->pdo->update("admin_circle_status",array("status"=>$updateArr['status']),$where,true);
            $status = true;
        }else{
            $this->pdo->insert("admin_circle_status",array("status"=>$updateArr['status'],"admin_id"=>$admin_id));
            $status = true;
        }

        if(empty($this->activityArr)){{
            $this->activityArr = array("status"=>'Active');
        }

        }
        $this->updateActivity = true;
        $this->adminCircleActivity();
        return $status;
    }
    
    /**
     * Update Circle status
     *
     * @param Integer $circle_id
     * @param Array $updateArr
     */
    public function updateAdminCircleStatus($circle_id,$updateArr){

        $this->paramArr = $updateArr;
        $this->circleName = $updateArr['name'];
        $this->circleId =  $circle_id;

        $where = array(
            "clause" => " id = :id AND is_deleted='N'",
            "params" => array(
                    ":id" => $circle_id
            )
        );

        $this->activityArr = $this->pdo->update('admin_circle',$updateArr,$where,true);
        $this->updateActivity = true;
        $this->adminCircleActivity();
    }
    /**
     * Delete Admin circle And It's chats And file
     *
     * @param Integer $circle_id
     * @return void
     */
    public function deleteAdminCircle($circle_id){

        $checkArr = $this->pdo->selectOne("SELECT name,id from admin_circle where id=:id and is_deleted='N'",array(":id"=>$circle_id));
        if(!empty($checkArr['id'])){

            $this->circleId =  $circle_id;
            $this->circleName = $checkArr['name'];

            $where = array(
                "clause" => " id=:id AND is_deleted='N'",
                "params" => array(
                        ":id" => $circle_id
                )
            );
            $this->delete_admin_circle_message_files($circle_id);
            $this->delete_admin_circle_chat_read($circle_id); 
            $this->delete_admin_circle_chats($circle_id);           
            $this->delete_assigned_admin($circle_id);
            $this->pdo->update('admin_circle',array("is_deleted"=>'Y'),$where);
            
            $this->deleteActivity = true;

            $this->adminCircleActivity();

        }
    }
    /**
     * Delete File releated to chat
     */
    private function delete_admin_circle_message_files($circle_id){
        global $CIRCLE_DOCUMENT_DIR;
        $checkArr = $this->pdo->select("SELECT id,file from admin_circle_message_files where circle_id=:id and is_deleted='N'",array(":id"=>$circle_id));
        if(!empty($checkArr)){
            foreach ($checkArr as $key => $value) {
                if(file_exists($CIRCLE_DOCUMENT_DIR.$value['file'])){
                    unlink($CIRCLE_DOCUMENT_DIR.$value['file']);
                }              
            }
            $del_where = array(
                "clause" => " circle_id=:id ",
                "params" => array(
                    ":id" => $circle_id
                )
            );
            $this->pdo->update("admin_circle_message_files",array("is_deleted"=>'Y'),$del_where);
        }
    }
    /**
     * Delete read message releated to chat
     */
    private function delete_admin_circle_chat_read($circle_id){
        $checkArr = $this->pdo->selectOne("SELECT id from admin_circle_chat_read where circle_id=:id and is_deleted='N'",array(":id"=>$circle_id));
        if(!empty($checkArr['id'])){
            $del_where = array(
                "clause" => " circle_id=:id",
                "params" => array(
                    ":id" => $circle_id
                )
            );
            $this->pdo->update("admin_circle_chat_read",array("is_deleted"=>'Y'),$del_where);
        } 
    }
    /**
     * Delete chat messages releated to chat
     */
    private function delete_admin_circle_chats($circle_id){
        $checkArr = $this->pdo->selectOne("SELECT id from admin_circle_chat where circle_id=:id and is_deleted='N'",array(":id"=>$circle_id));
        if(!empty($checkArr['id'])){
            $del_where = array(
                "clause" => " circle_id=:id",
                "params" => array(
                    ":id" => $circle_id
                )
            );
            $this->pdo->update("admin_circle_chat",array("is_deleted"=>'Y'),$del_where);
        } 
    }
    /**
     * Insert or update assigned Admin in Circle
     */
    private function insert_update_assigned_admin($circle_id,$admin_arr){
        
        $dbArr = $this->check_assigned_admin($circle_id);
        if(!empty($dbArr)){
            $dbAdminArr = $deleteAdminParram = $insertAdminParam = array();

            foreach($dbArr as $admin){
                $dbAdminArr[$admin['id']] = $admin['admin_id'];
            }

            $deleteAdmin = array_diff($dbAdminArr,$admin_arr);
            $insertAdmin = array_diff($admin_arr,$dbAdminArr);

            $this->activityArr['inserted_admin'] = '';
            $this->activityArr['deleted_admin'] = '';

            if(!empty($deleteAdmin)){

                $this->activityArr['deleted_admin'] = $this->pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(display_id,' - ',fname,' ',lname) SEPARATOR '<br>') as admins FROM admin where id IN(".implode(',',$deleteAdmin).")");

                $this->delete_assigned_admin($circle_id,$deleteAdmin);

            }

            if(!empty($insertAdmin)){

                $this->activityArr['inserted_admin'] = $this->pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(display_id,' - ',fname,' ',lname) SEPARATOR '<br>') as admins FROM admin where id IN(".implode(',',$insertAdmin).")");

                $this->insert_assigned_admin($circle_id,$insertAdmin);

            }

        }else{

            $this->insert_assigned_admin($circle_id,$admin_arr);
            
        }
    }

    private function check_assigned_admin($circle_id){

        $checkArr = $this->pdo->select("SELECT id,admin_id from assigned_admin_circle where circle_id=:circle_id AND is_deleted='N'",array(":circle_id"=>$circle_id));
        if(!empty($checkArr)){

            return $checkArr;

        }else{

            return array();

        }

    }
    private function insert_assigned_admin($circle_id,$adminArr){

        $insertParam = array();
        $insertParam['circle_id'] = $circle_id;
        foreach ($adminArr as $key => $value) {

            $insertParam['admin_id'] = $value;
            $this->pdo->insert('assigned_admin_circle',$insertParam);

        }

    }
    private function delete_assigned_admin($circle_id = '',$deleteAdmin = array()){
        
        if(!empty($deleteAdmin)){
            foreach ($deleteAdmin as $key => $value) {

                $updateWhere = array(
                    "clause" => "id = :id and is_deleted='N'",
                    "params" => array(":id"=>$key),
                );
                $this->pdo->update('assigned_admin_circle',array("is_deleted"=>'Y'),$updateWhere);
    
            }
        }else if(!empty($circle_id)){
            $updateWhere = array(
                "clause" => "circle_id = :id and is_deleted='N'",
                "params" => array(":id"=>$circle_id),
            );
            $this->pdo->update('assigned_admin_circle',array("is_deleted"=>'Y'),$updateWhere);
        }
        

    }
    //Admin circle Activity
    private function adminCircleActivity(){

        global $ADMIN_HOST;

        $activityMsg = '';
        if($this->insertActivity){
            $activityMsg = ' created circle ';
        }elseif($this->updateActivity){
            $activityMsg = ' updated circle ';
        }elseif($this->deleteActivity){
            $activityMsg = ' deleted circle ';
        }

        $description['ac_message'] = array(
            'ac_red_1'=>array(
                'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                'title'=>$_SESSION['admin']['display_id'],
            ),
            'ac_message_1' => $activityMsg,
            'ac_red_2'=>array(
                'title'=>$this->circleName,
            ),
        );

        if(!empty($this->activityArr)){
            $i = 0;
            foreach ($this->activityArr as $key => $value) {
                if(in_array($key,array('deleted_admin','inserted_admin')) && !empty($value)){
                    $description['desc_'.$key.$i] = ucfirst((str_replace('_',' ',$key))) ." : <br>". $value['admins'];
                }elseif(!in_array($key,array('deleted_admin','inserted_admin'))){
                    $description['key_value']['desc_arr'][$key] = 'updated from '.$this->activityArr[$key].' to '.$this->paramArr[$key];
                }
                $i++;
            }
        }

        $desc = json_encode($description);
        activity_feed(3,$_SESSION['admin']['id'],'Admin', $this->circleId, 'Circle', 'Admin Circle',$_SESSION['admin']['name'],"",$desc);
    }
    
}
?>