<?php

class ProcessTicketReply {
    private $imap_conn;

    public function __construct($username, $password,$host) {
        $this->username = $username;
        $this->password = $password;
        $this->host= $host;
        $this->imap_conn = imap_open($this->host, $this->username, $this->password);
        if (!$this->imap_conn) {
        }
        $this->read_all_mails($username);
    }

    public function read_all_mails($search = "") {
        global $ETICKET_SUPPORT_EMAIL;
        if ($this->imap_conn) {
            /*if ($search) {
              $msgIds = imap_search($this->imap_conn, 'SUBJECT "' . $search . '" UNSEEN');
            } else {
              $msgIds = imap_search($this->imap_conn, 'UNSEEN');
            }*/

            $msgIds = imap_search($this->imap_conn, 'TO "' . $ETICKET_SUPPORT_EMAIL . '" UNSEEN');
            if (!empty($msgIds) && is_array($msgIds)) {
                pre_print($msgIds,false);
                foreach ($msgIds as $msgId) {
                    $overview = imap_headerinfo($this->imap_conn, $msgId);
                    $s = imap_fetchstructure($this->imap_conn, $msgId);
                    if (!empty($overview)) {
                        $mail_data = (array) $overview;
                        if (!isset($mail_data['msg_body'])) {
                            $mail_data['msg_body'] = "";
                        }
                        if (!$s->parts) {
                            $mail_data['msg_body'] .= getpart($this->imap_conn, $msgId, $s, 0);
                        } else {
                            foreach ($s->parts as $partno0 => $p) {
                                $mail_data['msg_body'] = getpart($this->imap_conn, $msgId, $p, $partno0 + 1);
                            }
                        }
                        $mail_data['attachments'] = extract_attachments($this->imap_conn, $msgId);
                        $this->generate_ticket($mail_data);
                    }
                }
            }
        }
    }

    private function generate_ticket($mail_data) {
        global $pdo;

        $REAL_IP_ADDRESS = get_real_ipaddress();
        include_once dirname(__DIR__) . '/libs/EmailReplyParser/vendor/autoload.php';
        $e_r_message = \EmailReplyParser\EmailReplyParser::parseReply($mail_data['msg_body']);
        
        $subject_array = explode("-[", $mail_data['subject']);
        $tracking_id = end($subject_array);
        $tracking_id = rtrim($tracking_id, "]");

        $ticket_row = $pdo->selectOne("SELECT id,user_id,user_type,tracking_id,assigned_admin_id FROM s_ticket WHERE tracking_id=:tracking_id",array(":tracking_id"=>$tracking_id));
        
        if(!empty($ticket_row)) {
            if($ticket_row['user_type'] == 'Admin') {
                $user_row = $pdo->selectOne("SELECT fname,lname,display_id as rep_id,id,email,phone,'admin' as user_type FROM admin WHERE is_deleted='N' AND id=:user_id",array(":user_id"=>$ticket_row['user_id']));
            } else {
                $user_row = $pdo->selectOne("SELECT fname,lname,rep_id,id,email,cell_phone as phone,type as user_type FROM customer WHERE is_deleted='N' AND type=:type AND id=:user_id",array(":type"=>$ticket_row['user_type'],":user_id"=>$ticket_row['user_id']));
            }

            if(!empty($user_row)){
                $upd_param = array(
                    "updated_at" => 'msqlfunc_NOW()',
                );
                $upd_where = array(
                    'clause' => 'id=:id',
                    'params' => array(":id"=>$ticket_row['id'])
                );
                $pdo->update('s_ticket',$upd_param,$upd_where);
                
                $ins_param = array(
                    "ticket_id" => $ticket_row['id'],
                    "user_id" => $user_row['id'],
                    "user_type" => $user_row['user_type'],
                    "type" => 'reply',
                    "message" => htmlspecialchars($e_r_message),
                    "ip_address" => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                );
                $insId = $pdo->insert('s_ticket_message',$ins_param);

                if (count($mail_data['attachments']) > 0) {
                    foreach ($mail_data['attachments'] as $f => $attachment) {
                        $ins_msg_files = array(
                            'ticket_id' =>  $ticket_row['id'],
                            'message_id' => $insId,
                            'file' => $attachment['file'],
                            'file_name' => $attachment['filename'],
                        );
                        $pdo->insert('s_ticket_message_files', $ins_msg_files);
                    }
                }

                if(!empty($ticket_row['assigned_admin_id'])) {
                    $admin_row = $pdo->selectOne("SELECT fname,lname,display_id as rep_id,id,email,phone,'admin' as user_type FROM admin WHERE is_deleted='N' AND id=:user_id",array(":user_id"=>$ticket_row['assigned_admin_id']));
                    if(!empty($admin_row)) {
                        $email_data = array();
                        $email_data['TKT_ID'] = $tracking_id;
                        $email_data['Message'] = nl2br($e_r_message);
                        //T145
                        trigger_mail(4,$email_data,$admin_row['email']);
                    }
                }
            }
        }
        return true;
    }

    public function close() {
        imap_close($this->imap_conn);
    }

}

function getpart($imap_conn, $msgno, $p, $partno) {
//  echo "in function <br />";
// $partno = '1', '2', '2.1', '2.1.3', etc for multipart, 0 if simple

  global $htmlmsg, $plainmsg, $charset, $attachments;

// DECODE DATA
  $mail_data['msg_body'] = ($partno) ?
          imap_fetchbody($imap_conn, $msgno, $partno) : // multipart
          imap_body($imap_conn, $msgno);  // simple
// Any part may be encoded, even plain text messages, so check everything.
  if ($p->encoding == 4)
    $mail_data['msg_body'] = quoted_printable_decode($mail_data['msg_body']);
  elseif ($p->encoding == 3)
    $mail_data['msg_body'] = base64_decode($mail_data['msg_body']);

// PARAMETERS
// get all parameters, like charset, filenames of attachments, etc.
  $params = array();
  if ($p->parameters)
    foreach ($p->parameters as $x)
      $params[strtolower($x->attribute)] = $x->value;
  if (isset($p->dparameters))
    foreach ($p->dparameters as $x)
      $params[strtolower($x->attribute)] = $x->value;

// ATTACHMENT
// Any part with a filename is an attachment,
// so an attached text file (type 0) is not mistaken as the message.
  if (isset($params['filename']) || isset($params['name'])) {
// filename may be given as 'Filename' or 'Name' or both
    $filename = ($params['filename']) ? $params['filename'] : $params['name'];
// filename may be encoded, so see imap_mime_header_decode()
    $attachments[$filename] = $mail_data['msg_body'];  // this is a problem if two files have same name
  }

// TEXT
  if ($p->type == 0 && $mail_data['msg_body']) {
// Messages may be split in different parts because of inline attachments,
// so append parts together with blank row.
    if (strtolower($p->subtype) == 'plain')
      $plainmsg .= trim($mail_data['msg_body']) . "\n\n";
    else
      $htmlmsg .= $mail_data['msg_body'] . "<br><br>";
    $charset = $params['charset'];  // assume all parts are same charset
  }

// EMBEDDED MESSAGE
// Many bounce notifications embed the original message as type 2,
// but AOL uses type 1 (multipart), which is not handled here.
// There are no PHP functions to parse embedded messages,
// so this just appends the raw source to the main message.
  elseif ($p->type == 2 && $mail_data['msg_body']) {
    $plainmsg .= $mail_data['msg_body'] . "\n\n";
  }

// SUBPART RECURSION
  if (isset($p->parts)) {
    foreach ($p->parts as $partno0 => $p2) {
      return getpart($imap_conn, $msgno, $p2, $partno . '.' . ($partno0 + 1));  // 1.2, 1.2.1, etc.
    }
  }

  $msg_body = "";

  if ($htmlmsg) {
    $msg_body = $htmlmsg;
  }

  if ($plainmsg) {
    $msg_body = $plainmsg;
  }

  return $msg_body;
}

function extract_attachments($inbox, $email_number) {
  global $ETICKET_DOCUMENT_DIR;
  $attachments = array();

  /* get mail structure */
  $structure = imap_fetchstructure($inbox, $email_number);

  /* if any attachments found... */
  if(isset($structure->parts) && count($structure->parts)) 
  {
      for($i = 0; $i < count($structure->parts); $i++) 
      {
          $attachments[$i] = array(
              'is_attachment' => false,
              'filename' => '',
              'name' => '',
              'attachment' => ''
          );

          if($structure->parts[$i]->ifdparameters) 
          {
              foreach($structure->parts[$i]->dparameters as $object) 
              {
                  if(strtolower($object->attribute) == 'filename') 
                  {
                      $attachments[$i]['is_attachment'] = true;
                      $attachments[$i]['filename'] = $object->value;
                  }
              }
          }

          if($structure->parts[$i]->ifparameters) 
          {
              foreach($structure->parts[$i]->parameters as $object) 
              {
                  if(strtolower($object->attribute) == 'name') 
                  {
                      $attachments[$i]['is_attachment'] = true;
                      $attachments[$i]['name'] = $object->value;
                  }
              }
          }

          if($attachments[$i]['is_attachment']) 
          {
              $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i+1);

              /* 3 = BASE64 encoding */
              if($structure->parts[$i]->encoding == 3) 
              { 
                  $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
              }
              /* 4 = QUOTED-PRINTABLE encoding */
              elseif($structure->parts[$i]->encoding == 4) 
              { 
                  $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
              }
          }
      }
  }

  $atta_res = array();
  /* iterate through each attachment and save it */
  foreach($attachments as $attachment) {
      if($attachment['is_attachment'] == 1) {
          $filename = $attachment['name'];
          if(empty($filename)) $filename = $attachment['filename'];

          if ($filename != "") {
              $file = time() . "_" . $filename;
              $fp = fopen($ETICKET_DOCUMENT_DIR.$file,'w+');
              fwrite($fp, $attachment['attachment']);
              fclose($fp);
              chmod($ETICKET_DOCUMENT_DIR.$file, 0777);
              $atta_res[] = array(
                  'file' => $file,
                  'filename' => $filename,
              );
          }
      }
  }
  return $atta_res;
}
?>