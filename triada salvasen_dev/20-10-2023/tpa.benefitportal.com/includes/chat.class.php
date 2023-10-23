<?php
include_once dirname(__DIR__) . "/live_chat/include/functions.php";

class LiveChat
{ 
  public function addLiveChatUser($id='',$type){
    global $pdo,$CREDIT_CARD_ENC_KEY,$LIVE_CHAT_HOST;
    
    if($type=="Admin"){
      $sqlUser = "SELECT a.id,a.fname,a.lname,a.email,
                  AES_DECRYPT(a.password,'".$CREDIT_CARD_ENC_KEY."') as password,'admin' as user_type,
                  a.id as app_user_id,'Admin' as app_user_type
                  FROM admin a 
                  WHERE a.id=:id";
    }else{
      $sqlUser = "SELECT c.fname,c.lname,c.email,AES_DECRYPT(c.password,'".$CREDIT_CARD_ENC_KEY."') as password,c.type as user_type,c.id as app_user_id,c.type as app_user_type
                FROM customer c 
                WHERE c.id=:id";
    }
    $resUser = $pdo->selectOne($sqlUser,array(":id"=>$id));
    
    $status = false;

    if(!empty($resUser)){
      if($resUser['user_type']=="admin"){
        $user_type = 'admin';
      }else if(in_array($resUser['user_type'],array("Agent","Group"))){
        $user_type = 'agent';
      }else{
        $user_type = 'user';
      }
      $profile_image = $LIVE_CHAT_HOST.'/media/user.svg';
        
      $password = $resUser['password'];
      $first_name = $resUser['fname'];
      $last_name = $resUser['lname'];
      $email = $resUser['email'];

      $app_user_id = !empty($resUser["app_user_id"]) ? $resUser["app_user_id"] : 0;
      $app_user_type = !empty($resUser["app_user_type"]) ? $resUser["app_user_type"] : "Website";
      
      $settings = array(
        'profile_image'=>$profile_image,
        'first_name'=>$first_name,
        'last_name'=>$last_name,
        'password'=>$password,
        'email'=>$email,
        'user_type'=>$user_type,
        'app_user_id'=>$app_user_id,
        'app_user_type'=>$app_user_type,
      );
      $status = sb_add_user($settings);
    }
    return $status;
  }

  public function login_to_chat_account($user_id,$user_type) {
      global $pdo,$CREDIT_CARD_ENC_KEY,$LIVE_CHAT_HOST;

      if(isset($_SESSION['sb-session']) && $_SESSION['sb-session']['app_user_id'] == $user_id && $_SESSION['sb-session']['app_user_type'] == $user_type) {
          return true;
      }

      if($user_type=="Admin"){
          $user_sql = "SELECT a.id,a.fname,a.lname,a.email,
                    AES_DECRYPT(a.password,'".$CREDIT_CARD_ENC_KEY."') as password,'admin' as user_type,
                    a.id as app_user_id,'Admin' as app_user_type
                    FROM admin a 
                    WHERE a.id=:id";
      } else {
          $user_sql = "SELECT c.fname,c.lname,c.email,AES_DECRYPT(c.password,'".$CREDIT_CARD_ENC_KEY."') as password,c.type as user_type,c.id as app_user_id,c.type as app_user_type,c.business_name as GroupName
                  FROM customer c 
                  WHERE c.id=:id";
      }
      $user_row = $pdo->selectOne($user_sql,array(":id" => $user_id));
      $status = false;
      if(!empty($user_row)) {

          if($user_row['user_type'] == "admin") {
              $lc_user_type = 'admin';
          } else {
              $lc_user_type = 'user';
          }
          $profile_image = $LIVE_CHAT_HOST.'/media/user.svg';

          $password = $user_row['password'];
          $first_name = $user_row['fname'];
          $last_name = $user_row['lname'];
          $email = $user_row['email'];
          $app_user_id = $user_row["app_user_id"];
          $app_user_type = $user_row["app_user_type"];
          if($app_user_type == 'Group'){
             $first_name = $user_row['GroupName'];
             $last_name = '';
          }
          $settings = array(
            'profile_image'=>$profile_image,
            'first_name'=>$first_name,
            'last_name'=>$last_name,
            'password'=>$password,
            'email'=>$email,
            'user_type'=>$lc_user_type,
            'app_user_id'=>$app_user_id,
            'app_user_type'=>$app_user_type,
          );
          $status = sb_login_custom($app_user_id,$app_user_type,$settings);
      }
      return $status;
  }

  public function chatLogin($id,$type){
    global $pdo,$CREDIT_CARD_ENC_KEY;
    $email = '';
    $password = '';
    if($type=="Admin"){
      $sqlUser="SELECT email,AES_DECRYPT(password,'".$CREDIT_CARD_ENC_KEY."') as password FROM admin where id=:id";
    }else{

    }
    $resUser=$pdo->selectOne($sqlUser,array(":id"=>$id));
    if(!empty($resUser)){
      $email=$resUser['email'];
      $password = $resUser['password'];
    }
    $status = sb_login($email, $password,'', '');
    return $status;
  }
  
  public function chatLogout(){
    $status = sb_logout();

    return $status;
  }
  
  public function get_online_admins(){
    $online_users = sb_get_online_admins();
    return $online_users;
  }
  
  public function get_idle_admins(){
    $online_users = sb_get_idle_admins();
    return $online_users;
  }
  
  public function get_live_conversations(){
    $online_users = sb_get_live_conversations();
    return $online_users;
  }
  
  public function get_in_queue_conversations(){
    $online_users = sb_get_in_queue_conversations();
    return $online_users;
  }
  
  public function get_served_conversations(){
    $online_users = sb_get_served_conversations();
    return $online_users;
  }
  
  public function get_saved_replies(){
    $saved_replies = sb_get_setting('saved-replies');
    return $saved_replies;
  }
  
  public function get_departments(){
    $departments = sb_get_departments('departments');
    return $departments;
  }
  
  public function get_departments_by_name(){
    $departments = sb_get_departments('departments');
    $tmp_departments = array();
    foreach ($departments as $dep_key => $dep_row) {
        $tmp_departments[$dep_row['name']] = array(
            'id' => $dep_key,
            'name' => $dep_row['name'],
        );
    }
    ksort($tmp_departments);
    return $tmp_departments;
  }
  
  public function update_saved_replies($saved_replies) {
    $settings = sb_get_settings();
    $settings['saved-replies'][0] = $saved_replies;
    sb_save_settings($settings);
    return true;
  }

  public function update_departments($department_id,$department_data) {
    
    $settings = sb_get_settings();
    $departments = $settings['departments'][0];
    if($department_id > 0) {
        if(!empty($departments)) {
            foreach ($departments as $key => $dep_row) {
                if($dep_row['department-id'] == $department_data['department-id']) {
                    $departments[$key]['department-name'] = $department_data['department-name'];
                    break;
                }
            }
        } else {
          $departments = array();
          $departments[] = array(
              'department-id' => $department_id,
              'department-name' => $department_data['department-name'],
              'department-color' => '',
              'department-image' => '',
          );
        }
    } else {
        if(!empty($departments)) {
            $department_id = max(array_column($departments,'department-id'));
            $department_id = $department_id + 1;
            $departments[] = array(
                'department-id' => $department_id,
                'department-name' => $department_data['department-name'],
                'department-color' => '',
                'department-image' => '',
            );
        } else {
            $departments = array();
            $departments[] = array(
                'department-id' => 1,
                'department-name' => $department_data['department-name'],
                'department-color' => '',
                'department-image' => '',
            );
        }
    }
    $settings['departments'][0] = $departments;
    sb_save_settings($settings);
    return true;
  }
  
  public function delete_department($department_id) {
    global $pdo;
    $settings = sb_get_settings();
    $departments = $settings['departments'][0];
    if($department_id > 0) {
        $department_tmp = array();
        if(!empty($departments)) {
            foreach ($departments as $key => $dep_row) {
                if($dep_row['department-id'] == $department_id) {
                        
                    /*----- Set Department to null for users and convesation ----- */
                    sb_db_query('UPDATE sb_users SET department=NULL WHERE department="'.$department_id.'"');
                    sb_db_query('UPDATE sb_conversations SET department=NULL WHERE department="'.$department_id.'"');
                    /*-----/Set Department to null for users and convesation ----- */
                    
                    unset($departments[$key]);
                    break;
                }
            }
            if(!empty($departments)) {
                foreach ($departments as $dep_row) {
                    $department_tmp[] = $dep_row;
                }
            }
        }
        $settings['departments'][0] = $department_tmp;
        sb_save_settings($settings);
    }
    return true;
  }
  
  public function conversation_messages($user_id,$id) {
    $messages = sb_get_agent_conversation($user_id,$id);
    return $messages;
  }
  
  public function get_display_chat_status($status_code) {
    $display_status = '-';
    if($status_code == 0) {
      $display_status = 'Active';

    } else if($status_code == 1) {
      $display_status = 'Waiting User';

    } else if($status_code == 2) {
      $display_status = 'Waiting Admin';

    } else if($status_code == 3) {
      $display_status = 'Archive';

    } else if($status_code == 4) {
      $display_status = 'Chat Closed';
    }

    return $display_status;
  }

  public function get_chats_per_day($date){
      $incr = 'DATE(sb.creation_time) = "'. date("Y-m-d",strtotime($date)) .'"';
      $query = "SELECT COUNT(sb.id) AS totalServed,
                      COUNT(DISTINCT(CASE WHEN su.app_user_type = 'Customer' THEN sb.id END)) AS totalMembers, 
                      COUNT(DISTINCT(CASE WHEN su.app_user_type = 'Agent' THEN sb.id END)) AS totalAgents, 
                      COUNT(DISTINCT(CASE WHEN su.app_user_type = 'Group' THEN sb.id END)) AS totalGroups, 
                      COUNT(DISTINCT(CASE WHEN su.app_user_type = 'Website' THEN sb.id END)) AS totalWebsites
      FROM sb_conversations sb 
      JOIN sb_users su ON(sb.user_id=su.id AND su.app_user_type !='Admin') 
      WHERE sb.id > 0 AND ". $incr;
      $result = sb_db_get($query);
      return $result;
  }

  public function getConversation($user_id,$conversation_id){
     $result = sb_get_conversation($user_id,$conversation_id);

     return $result;
  }
  public function updateConversation($conversation_id,$status,$userId,$message=""){
     $result = sb_update_conversation_status($conversation_id,$status);
     if($status == 4){
       $this->sendMessage($conversation_id,$status,$userId,$message);
     }
     return $result;
  }
  public function sendMessage($conversation_id,$status,$userId,$message){
     $result = sb_send_message($userId,$conversation_id,$message);
     return $result;
  }
  public function adminSendMessage($conversation_id,$status,$userId,$message){
     $result = sb_send_message($userId,$conversation_id,$message,array(),$status);
     return $result;
  }
}