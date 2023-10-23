<?php
class portalResources{

    public $activityArr , $paramArr;
    public $insertActivity , $deleteActivity , $updateActivity = false;
    public $resourceName,$resourceId,$portalType = '';
    public $pdo = '';

    public function __construct(){
        global $pdo;
        $this->pdo = $pdo;
        $this->activityArr = array();
        $this->paramArr = array();
    }

    public static function getUserModule($userType){
        global $pdo;
        $selModule = '';
        if($userType == 'admin'){
            $selModule = "SELECT * from `feature_access` where parent_id=0 AND id NOT in(21,38,70,25) AND is_deleted='N' order by order_by";
        }else if($userType == 'agent'){
            $selModule = "SELECT * FROM agent_feature_access WHERE parent_id=0";
        }else if($userType == 'group'){
            $selModule = "SELECT * FROM group_feature_access WHERE parent_id=0";
        }else if($userType == 'member'){
            return $resModule = array(
                "add_product"=>"+ Product",
                "my_account"=>"My Account"
            );
        }
        if($selModule != ''){
            $moduleArr = array();
            $resModule = $pdo->select($selModule);
            if(!empty($resModule)){
                foreach ($resModule as $key => $value) {
                    $moduleArr[$value['title']] = $value;
                }
            }
            return $moduleArr;
        }
    }

    public function createResource($insertParam){
        global $RESOURCE_DOCUMENT_DIR ;

        $this->portalType = $insertParam['portal_type'];
        $fileArr = $insertParam['pdf_file'];
        $insertParam['file'] = date('mdYhisa').$fileArr['name'];
        $insertParam['file_name'] = $fileArr['name'];
        unset($insertParam['pdf_file']);
        
        $name = basename($insertParam['file']);
        move_uploaded_file($fileArr['tmp_name'], $RESOURCE_DOCUMENT_DIR.$name);

        $this->paramArr = $insertParam;

        $this->resourceId = $this->pdo->insert('portal_resources',$insertParam);
        $this->insertActivity = true;

        $this->resourceActivity();

    }

    /**
     * remove resource and releated Files
     *
     * @param Encrypted $resourceId
     * @return boolean
     */
    public function removeResource($resourceId){
        global $RESOURCE_DOCUMENT_DIR;
        
        $resResource = $this->pdo->selectOne("SELECT * from portal_resources where md5(id)=:id and is_deleted='N'",array(":id"=>$resourceId));
        if(!empty($resResource['id'])){

            $this->paramArr = $resResource;
            $this->resourceId = $resResource['id'];
            $this->portalType = $resResource['portal_type'];

            $where = array(
                "clause" => "id=:id",
                "params" => array(":id"=>$resResource['id'])
            );

            if(file_exists($RESOURCE_DOCUMENT_DIR.$resResource['file'])){
                unlink($RESOURCE_DOCUMENT_DIR.$resResource['file']);
            }

            $this->pdo->update('portal_resources',array("is_deleted"=>'Y'),$where);
            $this->deleteActivity = true;
            $this->resourceActivity();
            return true;
        }else{
            return false;
        }
        
    }

    //Resources Activity
    private function resourceActivity(){

        global $ADMIN_HOST;

        $activityMsg = '';
        if($this->insertActivity){
            $activityMsg = ' created resources In '.ucfirst($this->portalType).' Portal on client support resources page';
        }elseif($this->updateActivity){
            $activityMsg = ' updated resources In '.ucfirst($this->portalType).' Portal on client support resources page';
        }elseif($this->deleteActivity){
            $activityMsg = ' deleted resources In '.ucfirst($this->portalType).' Portal on client support resources page';
        }

        $description['ac_message'] = array(
            'ac_red_1'=>array(
                'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                'title'=>$_SESSION['admin']['display_id'],
            ),
            'ac_message_2' => $activityMsg,
            'ac_red_2'=>array(
                'href'=>$ADMIN_HOST.'/resources.php',
                'title'=>"Resources",
            ),
        );

        $description['desc_message_desc'] = "Module : ".ucfirst($this->paramArr['module_name']).'<br>Resource Name : '.$this->paramArr['resource_name'];
        if(!empty($this->activityArr)){
            foreach ($this->activityArr as $key => $value) {
                $description['key_value']['desc_arr'][$key] = 'updated from '.$this->activityArr[$key].' to '.$this->paramArr[$key];
            }
        }

        $desc = json_encode($description);
        activity_feed(3,$_SESSION['admin']['id'],'Admin', $this->resourceId, 'Resource Portal', 'Resource Portal',$_SESSION['admin']['name'],"",$desc);
    }

}
?>