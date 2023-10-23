<?php

require_once dirname(__DIR__) . '/libs/PHPMailer/PHPMailerAutoload.php';
require_once dirname(__DIR__) . '/libs/twilio-php-master/Twilio/autoload.php';
include_once dirname(__DIR__) . '/libs/sendgrid-php-master/vendor/autoload.php';
include_once dirname(__DIR__) . '/libs/telnyx-php-master/vendor/autoload.php';
include_once dirname(__DIR__) . '/libs/plivo-php-master/vendor/autoload.php';
include_once dirname(__FILE__) . '/template_function.php';
include_once dirname(__FILE__) . '/trigger.class.php';

function trigger_mail($triggerId, $params, $toEmail, $triggerStatus = true, $companyId = 3, $content = '', $subject = "", $templateContent = "", $attachments = array(),$extraParams = array()){

    global $pdo, $emailer_settings,$SENDGRID_API_KEY, $HOST, $STATS_DB, $LOG_DB,$SITE_ENV,$DEFAULT_TRIGGER_EMAIL,$memberLoginPage,$agentLoginPage,$groupLoginPage,$sendEmailLocal;

    $TriggerMailSms = new TriggerMailSms();
    $mailStatus = 'fail';
    $log_id = 0;

    
    $fromEmail = isset($params['EMAILER_SETTING']['from_mailid']) ? $params['EMAILER_SETTING']['from_mailid'] : '';
    $fromName = isset($params['EMAILER_SETTING']['from_mail_name']) ? $params['EMAILER_SETTING']['from_mail_name'] : '';

    $ccEmail = isset($params['EMAILER_SETTING']['cc_email']) ? $params['EMAILER_SETTING']['cc_email'] : '';
    $bccEmail = isset($params['EMAILER_SETTING']['bcc_email']) ? $params['EMAILER_SETTING']['bcc_email'] : '';

    $default_email_from = get_app_settings('default_email_from');
    $default_from_name = get_app_settings('default_from_name');

    if(isset($params['EMAILER_SETTING'])){
        unset($params['EMAILER_SETTING']);
    }

    // fetch trigger data
    $incr = '';
    $triggerWhere = array(':id' => $triggerId);
    $incr .= "id = :id";
    if ($triggerStatus || 1) {
        $triggerWhere[':status'] = 'Active';
        $incr .= " AND status = :status";
    }
    $triggerSql = "SELECT * FROM triggers WHERE $incr";
    $triggerRes = $pdo->selectOne($triggerSql, $triggerWhere);

 
    if ($triggerRes){
        if(empty($ccEmail) && !empty($triggerRes['cc_email_specific'])) {
            $ccEmail = $triggerRes['cc_email_specific'];
        }
        
        if(empty($bccEmail) && !empty($triggerRes['bcc_email_specific'])) {
            $bccEmail = $triggerRes['bcc_email_specific'];
        }

        if(empty($fromEmail) && !empty($triggerRes['from_email'])) {
            $fromEmail = $triggerRes['from_email'];

        } elseif(empty($fromEmail) && !empty($default_email_from)) {
            $fromEmail = $default_email_from;
        
        } elseif(empty($fromEmail)) {
            $fromEmail = $emailer_settings[$companyId]['tg_from_mailid'];
        }

        if(empty($fromName) && !empty($triggerRes['from_name'])) {
            $fromName = $triggerRes['from_name'];

        } elseif(empty($fromName) && !empty($default_from_name)) {
            $fromName = $default_from_name;
        
        } elseif(empty($fromName)) {
            $fromName = $emailer_settings[$companyId]['tg_from_mail_name'];
        }

        if($SITE_ENV != 'Live') {
            $toEmail = $DEFAULT_TRIGGER_EMAIL;
            $ccEmail = !empty($ccEmail) ? $DEFAULT_TRIGGER_EMAIL : "";
            $bccEmail = !empty($bccEmail) ? $DEFAULT_TRIGGER_EMAIL : "";
        }


        $triggerTemplateId = ($triggerRes['template_id'] > 0) ? $triggerRes['template_id'] : 1;
        $templateMsg = htmlspecialchars_decode(generate_trigger_template($triggerTemplateId, $templateContent));

        if ($triggerRes['type'] == 'Email' || $triggerRes['type'] == 'Both') {
            $message = !empty($content) ? $content : $triggerRes['email_content'];
            $message = str_replace("[[msg_content]]", $message, $templateMsg);

            $emailSubject = !empty($subject) ? $subject : $triggerRes['email_subject'];

            $params['MemberPortalLoginPage'] = $memberLoginPage;
            $params['AgentPortalLoginPage'] = $agentLoginPage;
            $params['EmployerGroupPortalLoginPage'] = $groupLoginPage;

            // Trigger SmartTag code
            foreach ($params as $placeholder => $value) {
                if ($placeholder == 'USER_IDENTITY') {
                    continue;
                }

                if ($placeholder == 'link') {
                    if (is_array($value)) {
                        foreach ($value as $linkPlaceholder => $linkValue) {
                            if (is_array($linkValue)) {
                                $linkValueDisplay = '<a href="' . $linkValue['href'] . '">' . $linkValue['title'] . '</a>';
                            } else {
                                $linkValueDisplay = '<a href="' . $linkValue . '">' . $linkValue . '</a>';
                            }
                            $message = str_replace("[[" . $linkPlaceholder . "]]", $linkValueDisplay, $message);
                        }
                    } else {
                        $value = '<a href="' . $value . '">' . $value . '</a>';
                        $message = str_replace("[[" . $placeholder . "]]", $value, $message);
                    }
                } else {
                    $message = str_replace("[[" . $placeholder . "]]", $value, $message);
                    $emailSubject = str_replace("[[" . $placeholder . "]]", $value, $emailSubject);
                }
            }

            $message = preg_replace('/[[:^print:]]/', ' ', $message);

            if($SITE_ENV == 'Live' || $sendEmailLocal == true) {
                $mail = new SendGrid\Mail();
                $fromEmailObj = new SendGrid\Email($fromName, $fromEmail);
                $mail->setFrom($fromEmailObj);

                $mail->setSubject($emailSubject);
                
                $contentObj = new SendGrid\Content("text/html", $message);
                $mail->addContent($contentObj);

                // add attachments to email
                if (!empty($attachments)) {
                    if (is_array($attachments)) {
                        foreach ($attachments as $key => $file) {
                            $fileContent = file_get_contents($file);
                            $fileContent = base64_encode($fileContent);
                            $fileName = pathinfo($file,PATHINFO_BASENAME);
                            $fileType = pathinfo($file,PATHINFO_EXTENSION);
                           
                            $attachmentObj = 'attachment'.$key;
                            $attachmentObj = new SendGrid\Attachment();
                            $attachmentObj->setContent($fileContent);
                            $attachmentObj->setType("application/".$fileType);
                            $attachmentObj->setFilename($fileName);
                            $attachmentObj->setDisposition("attachment");
                            $mail->addAttachment($attachmentObj);
                        }
                    } else {
                        $fileContent = file_get_contents($attachments);
                        $fileContent = base64_encode($fileContent);
                        $fileName = pathinfo($attachments,PATHINFO_BASENAME);
                        $fileType = pathinfo($attachments,PATHINFO_EXTENSION);
           
                        $attachmentObj = new SendGrid\Attachment();
                        $attachmentObj->setContent($fileContent);
                        $attachmentObj->setType("application/".$fileType);
                        $attachmentObj->setFilename($fileName);
                        $attachmentObj->setDisposition("attachment");
                        $mail->addAttachment($attachmentObj);
                    }
                }
                
                $sendEmails = array();
                if (is_array($toEmail) && !empty($toEmail)){
                    foreach ($toEmail as $value){
                        $sendEmails[] = $value;
                    }
                } else {
                    $sendEmails[] = $toEmail;
                }
               
                $personalization = new SendGrid\Personalization();
                // add CC email
                if(!empty($ccEmail)){
                    $toEmailObj = new SendGrid\Email(null,$ccEmail);
                    $personalization->addCc($toEmailObj);
                }
                //add BCC email
                if(!empty($bccEmail)){
                    $toEmailObj = new SendGrid\Email(null,$bccEmail);
                    $personalization->addBcc($toEmailObj);
                }

                if (count($sendEmails) > 0){
                    $logTblIds = array();
                    foreach ($sendEmails as $key => $value) {
                        // Do not send email if email unsubscribed
                        $unSubscribe = $TriggerMailSms->is_unsubscribe('email',$value);
   
                        if(!$unSubscribe){
                            $insLog = array(
                                        'trigger_id' => $triggerId,
                                        'from_email' => $fromEmail,
                                        'to_email' => $value,
                                        'subject' => $emailSubject,
                                        'message' => $message,
                                        'status' => "pending",
                                        'name' => !empty($extraParams['email_name']) ? $extraParams['email_name'] : '',
                                        'user_email' => !empty($extraParams['user_email']) ? $extraParams['user_email'] : '',
                                    );

                            $logId = $pdo->insert("email_log", $insLog);
                            $log_id = $logId;
                            $logTblIds[] = $logId;
                            $custArg = "salvasen_".$logId;

                            $personalMail = clone $personalization;
                            $personalMail->addCustomArg("user_id",$custArg);

                            $toEmailObj = new SendGrid\Email(null, $value);
                            $personalMail->addTo($toEmailObj);
                           
                            $mail->addPersonalization($personalMail);
                        }
                    }

                    if(!empty($logTblIds)){
                        $sendGridObj = new \SendGrid($SENDGRID_API_KEY);
                        try {
                            $statusCode = 202;
                            if($SITE_ENV == 'Live') {
                                $response = $sendGridObj->client->mail()->send()->post($mail);
                                $statusCode = $response->statusCode();
                            }

                            if($statusCode == 202){
                                $mailStatus = "success";
                            }else{
                                $mailStatus = "fail";
                            }
                            $updParams = array("status" => $mailStatus,"status_code" => !empty($statusCode) ? $statusCode : 0);
                            $updWhere = array(
                                            'clause' => "id IN (".implode(",", $logTblIds).")",
                                            'params' => array()
                                        );
                            $pdo->update('email_log', $updParams, $updWhere);
                        } catch (Exception $e) {
                            echo 'Caught exception: '. $e->getMessage() ."\n";
                        }
                    }
                }
            }
        }
    }
    if(isset($params['return_log_id']) && $params['return_log_id'] == "Y") {
        return $log_id;
    }
    return $mailStatus;
}

function trigger_sms($triggerId, $toPhone, $params = array(), $checkStatus = true, $content = '',$extraParams = array()){
    global $pdo, $smsServiceProvider, $SITE_ENV,$DEFAULT_TRIGGER_SMS,$testSmsPhoneNoArr,$callingCode,$callingCodeReplace;
    $smsStatus = 'fail';
    $log_id = 0;
    $TriggerMailSms = new TriggerMailSms();
    if($SITE_ENV!='Live') {
        $toPhone = $DEFAULT_TRIGGER_SMS;
    }

    $incr = '';
    $triggerWhere = array(':id' => $triggerId);
    $incr .= "id = :id";

    if ($checkStatus || 1) {
        $triggerWhere[':status'] = 'Active';
        $incr .= " AND status = :status";
    }

    // Do not send sms if phone unsubscribed
    $unSubscribe = $TriggerMailSms->is_unsubscribe('sms','',$toPhone);

    $triggerSql = "SELECT id,type,sms_content FROM triggers WHERE  $incr";
    $triggerRes = $pdo->selectOne($triggerSql, $triggerWhere);
    
    if (!empty($triggerRes) && $unSubscribe == false) {
        if ($triggerRes['type'] == 'SMS' || $triggerRes['type'] == 'Both') {
            
            $message = !empty($content) ? $content : $triggerRes['sms_content'];
            // trigger smartTagReplace
            if(!empty($params)){
                foreach ($params as $placeholder => $value) {
                    if ($placeholder == 'USER_IDENTITY') {
                        continue;
                    }

                    if ($placeholder == 'link' && is_array($value)) {
                        foreach ($value as $linkPlaceholder => $linkValue) {
                            $message = str_replace("[[" . $linkPlaceholder . "]]", $linkValue, $message);
                        }
                    } else {
                        $message = str_replace("[[" . $placeholder . "]]", $value, $message);
                    }
                }
            }

            if (!empty($toPhone) && !empty($message)) {
                
                $phone = str_replace($callingCode,$callingCodeReplace, $toPhone);
                if(in_array($phone, $testSmsPhoneNoArr)){
                    $country_code = '+91';
                    $toPhone = $country_code . $phone;
                }
                 
                $serviceParams = array(
                    "trigger_id" => $triggerId,
                    "sms_name" => checkIsset($extraParams['sms_name']),
                    "user_phone" => checkIsset($extraParams['user_phone']),
                );
                if($smsServiceProvider == 'Twilio'){
                    $smsResponse = trigger_sms_twillio($toPhone,$message,$serviceParams);
                }else if($smsServiceProvider == 'Telnyx'){
                    $smsResponse = trigger_sms_telnyx($toPhone,$message,$serviceParams);
                }else if($smsServiceProvider == 'Plivo'){
                    $smsResponse = trigger_sms_plivo($toPhone,$message,$serviceParams);
                }
                $smsStatus = !empty($smsResponse["smsStatus"]) ? strtolower($smsResponse["smsStatus"]) : 'fail';
                $log_id = !empty($smsResponse["log_id"]) ? $smsResponse["log_id"] : 0;
            }
        }
    }
    if(isset($params['return_log_id']) && $params['return_log_id'] == "Y") {
        return $log_id;
    }
    return $smsStatus;
}

function trigger_mail_to_email($params, $toEmail, $emailSubject, $otherParams = array(), $companyId = 3, $messageHead = "",$triggerTemplateId = 1){
    global $pdo, $emailer_settings,$SENDGRID_API_KEY,$HOST, $STATS_DB, $LOG_DB,$SITE_ENV,$DEFAULT_TRIGGER_EMAIL,$sendEmailLocal;

    $TriggerMailSms = new TriggerMailSms();
    $mailStatus = 'fail';

    $fromEmail = isset($params['EMAILER_SETTING']['from_mailid']) ? $params['EMAILER_SETTING']['from_mailid'] : '';

    $fromName = isset($params['EMAILER_SETTING']['from_mail_name']) ? $params['EMAILER_SETTING']['from_mail_name'] : '';

    $default_email_from = get_app_settings('default_email_from');
    $default_from_name = get_app_settings('default_from_name');
    
    if(empty($fromEmail) && !empty($default_email_from)) {
        $fromEmail = $default_email_from;
    
    } elseif(empty($fromEmail)) {
        $fromEmail = $emailer_settings[$companyId]['tg_from_mailid'];
    }

    if(empty($fromName) && !empty($default_from_name)) {
        $fromName = $default_from_name;
    
    } elseif(empty($fromName)) {
        $fromName = $emailer_settings[$companyId]['tg_from_mail_name'];
    }

    if(isset($params['EMAILER_SETTING'])){
        unset($params['EMAILER_SETTING']);
    }

    $templateMsg = htmlspecialchars_decode(generate_trigger_template($triggerTemplateId));
     
    $message = $messageHead;
    $tableHead = "";
    $tableData = "";
    $hasCopied = false;
    if (is_array($params)) {
        foreach ($params as $placeholder => $value) {
            if (is_array($value)) {
                if (!$hasCopied) {
                    $tableHead .= "\r\n" . "<tr>";
                    foreach ($value as $p => $v) {
                        $str_key = ucwords(str_replace('_', ' ', $p));
                        $tableHead .= "\r\n" . "<th style='border-left:1px solid #ddd; padding:4px 0px;'>" . $str_key . "</th> ";
                    }
                    $tableHead .= "\r\n" . "</tr>";
                    $hasCopied = true;
                }
                $tableData .= "\r\n" . "<tr> ";
                foreach ($value as $p => $v) {
                    $tableData .= "\r\n" . "<td style='border-top:1px solid #ddd; border-left:1px solid #ddd; padding:1px 4px;' >" . $v . "</td> ";
                }
                $tableData .= "\r\n" . "</tr> ";
            } else {
                $str_key = ucwords(str_replace('_', ' ', $placeholder));
                $message .= "\r\n" . "<p><strong>" . $str_key . "</strong> : " . $value . "</p> ";
            }
        }
        if (!empty($tableHead)) {
            $message .= "\r\n" . '<table border=0 cellspacing=0 cellpadding=0 width="100%" style="border:1px solid #ddd; font-size:13px;  border-collapse: collapse;" > ' . "\r\n" . $tableHead . "\r\n" . $tableData . "\r\n" . ' </table>';
        }
    } else {
        $message .= $params;
    }
    $message = str_replace("[[msg_content]]", $message, $templateMsg);

    $message = preg_replace('/[[:^print:]]/', ' ', $message);

    if($SITE_ENV != 'Live') {
        $toEmail = $DEFAULT_TRIGGER_EMAIL;
        $ccEmail = !empty($ccEmail) ? $DEFAULT_TRIGGER_EMAIL : "";
        $bccEmail = !empty($bccEmail) ? $DEFAULT_TRIGGER_EMAIL : "";
    }

    if($SITE_ENV == 'Live' || $sendEmailLocal == true) {
        $mail = new SendGrid\Mail();
        $fromEmailObj = new SendGrid\Email($fromName, $fromEmail);
        $mail->setFrom($fromEmailObj);

        $mail->setSubject($emailSubject);
        
        $contentObj = new SendGrid\Content("text/html", $message);
        $mail->addContent($contentObj);

        $personalization = new SendGrid\Personalization();

        $logTblIds = array();
        if (is_array($toEmail)){
            foreach ($toEmail as $value){
                $personalMail = clone $personalization;
            
                $toEmailObj = new SendGrid\Email(null, $value);
                $personalMail->addTo($toEmailObj);
               
                $mail->addPersonalization($personalMail);

                $insLog = array(
                    'trigger_title' => makeSafe($emailSubject),
                    'email' => $value,
                    'status' => 'fail',
                    'created_at' => 'msqlfunc_NOW()',
                );
                $logTblIds[] = $pdo->insert("$LOG_DB.trigger_log_admin", $insLog);
            }

        } else {
            $personalMail = clone $personalization;

            $toEmailObj = new SendGrid\Email(null, $toEmail);
            $personalMail->addTo($toEmailObj);
           
            $mail->addPersonalization($personalMail);

            $insLog = array(
                'trigger_title' => makeSafe($emailSubject),
                'email' => $toEmail,
                'status' => 'fail',
                'created_at' => 'msqlfunc_NOW()',
            );
            $logTblIds[] = $pdo->insert("$LOG_DB.trigger_log_admin", $insLog);
        }

        if(!empty($logTblIds)){
            $sendGridObj = new \SendGrid($SENDGRID_API_KEY);
            try {
                $statusCode = 202;
                if($SITE_ENV == 'Live') {
                    $response = $sendGridObj->client->mail()->send()->post($mail);
                    $statusCode = $response->statusCode();
                }

                if($statusCode == 202){
                    $mailStatus = "success";
                } else {
                    $mailStatus = "fail";
                }
                $updParams = array("status" => $mailStatus);
                $updWhere = array(
                    'clause' => "id IN (".implode(",", $logTblIds).")",
                    'params' => array()
                );
                $pdo->update("$LOG_DB.trigger_log_admin", $updParams, $updWhere);
            } catch (Exception $e) {
                echo 'Caught exception: '. $e->getMessage() ."\n";
            }
        }
    }
    return $mailStatus;
}

function trigger_mail_to_mail($params, $toEmail,$companyId = 3, $emailSubject = "", $templateContent = "",$triggerTemplateId=1,$attachments=array(),$extraParams = array()){

    global $pdo, $emailer_settings,$SENDGRID_API_KEY, $HOST, $STATS_DB, $LOG_DB,$SITE_ENV,$DEFAULT_TRIGGER_EMAIL,$memberLoginPage,$agentLoginPage,$groupLoginPage,$sendEmailLocal;
    
    $TriggerMailSms = new TriggerMailSms();
    $mailStatus = 'fail';

    $fromEmail = !empty($params['EMAILER_SETTING']['from_mailid']) ? $params['EMAILER_SETTING']['from_mailid'] : '';

    $fromName = !empty($params['EMAILER_SETTING']['from_mail_name']) ? $params['EMAILER_SETTING']['from_mail_name'] : '';

    $ccEmail = !empty($params['EMAILER_SETTING']['cc_email']) ? $params['EMAILER_SETTING']['cc_email'] : '';
    $bccEmail = !empty($params['EMAILER_SETTING']['bcc_email']) ? $params['EMAILER_SETTING']['bcc_email'] : '';

    if(isset($params['EMAILER_SETTING'])){
        unset($params['EMAILER_SETTING']);
    }

    $default_email_from = get_app_settings('default_email_from');
    $default_from_name = get_app_settings('default_from_name');

    if(empty($fromEmail) && !empty($default_email_from)) {
        $fromEmail = $default_email_from;
    
    } elseif(empty($fromEmail)) {
        $fromEmail = $emailer_settings[$companyId]['tg_from_mailid'];
    }

    if(empty($fromName) && !empty($default_from_name)) {
        $fromName = $default_from_name;
    
    } elseif(empty($fromName)) {
        $fromName = $emailer_settings[$companyId]['tg_from_mail_name'];
    }

    $templateMsg = (empty($triggerTemplateId) ? "[[msg_content]]" : htmlspecialchars_decode(generate_trigger_template($triggerTemplateId)));

    $message = str_replace('[[msg_content]]',$templateContent, $templateMsg);

    $params['MemberPortalLoginPage'] = $memberLoginPage;
    $params['AgentPortalLoginPage'] = $agentLoginPage;
    $params['EmployerGroupPortalLoginPage'] = $groupLoginPage;

    if($SITE_ENV != 'Live') {
        $toEmail = $DEFAULT_TRIGGER_EMAIL;
        $ccEmail = !empty($ccEmail) ? $DEFAULT_TRIGGER_EMAIL : "";
        $bccEmail = !empty($bccEmail) ? $DEFAULT_TRIGGER_EMAIL : "";
    }

      // Smart Tag code
        foreach ($params as $placeholder => $value) {
            if ($placeholder == 'USER_IDENTITY') {
                continue;
            }

            if ($placeholder == 'link') {
                if (is_array($value)) {
                    foreach ($value as $linkPlaceholder => $linkValue) {
                        if (is_array($linkValue)) {
                            $linkValueDisplay = '<a href="' . $linkValue['href'] . '">' . $linkValue['title'] . '</a>';
                        } else {
                            $linkValueDisplay = '<a href="' . $linkValue . '">' . $linkValue . '</a>';
                        }
                        $message = str_replace("[[" . $linkPlaceholder . "]]", $linkValueDisplay, $message);
                    }
                } else {
                    $value = '<a href="' . $value . '">' . $value . '</a>';
                    $message = str_replace("[[" . $placeholder . "]]", $value, $message);
                }
            } else {
                $message = str_replace("[[" . $placeholder . "]]", $value, $message);
                $emailSubject = str_replace("[[" . $placeholder . "]]", $value, $emailSubject);
            }
        }

        $message = preg_replace('/[[:^print:]]/', ' ', $message);

        if($SITE_ENV == 'Live' || $sendEmailLocal == true){

            $mail = new SendGrid\Mail();
            $fromEmailObj = new SendGrid\Email($fromName, $fromEmail);
            $mail->setFrom($fromEmailObj);
            $mail->setSubject($emailSubject);
                
            $contentObj = new SendGrid\Content("text/html", $message);
            $mail->addContent($contentObj);

            // add attachments to email
            if (!empty($attachments)) {
                if (is_array($attachments)) {
                    foreach ($attachments as $key => $file) {
                        $fileContent = file_get_contents($file);
                        $fileContent = base64_encode($fileContent);
                        $fileName = pathinfo($file,PATHINFO_BASENAME);
                        $fileType = pathinfo($file,PATHINFO_EXTENSION);
                       
                        $attachmentObj = 'attachment'.$key;
                        $attachmentObj = new SendGrid\Attachment();
                        $attachmentObj->setContent($fileContent);
                        $attachmentObj->setType("application/".$fileType);
                        $attachmentObj->setFilename($fileName);
                        $attachmentObj->setDisposition("attachment");
                        $mail->addAttachment($attachmentObj);
                    }
                } else {
                    $fileContent = file_get_contents($attachments);
                    $fileContent = base64_encode($fileContent);
                    $fileName = pathinfo($attachments,PATHINFO_BASENAME);
                    $fileType = pathinfo($attachments,PATHINFO_EXTENSION);
       
                    $attachmentObj = new SendGrid\Attachment();
                    $attachmentObj->setContent($fileContent);
                    $attachmentObj->setType("application/".$fileType);
                    $attachmentObj->setFilename($fileName);
                    $attachmentObj->setDisposition("attachment");
                    $mail->addAttachment($attachmentObj);
                }
            }
            
            $sendEmails = array();
            if (is_array($toEmail) && !empty($toEmail)){
                foreach ($toEmail as $value){
                    $sendEmails[] = $value;
                }
            } else {
                $sendEmails[] = $toEmail;
            }


            $personalization = new SendGrid\Personalization();
            // add CC email
                if(!empty($ccEmail)){
                    $personalization->addCc($ccEmail);
                }
            // add BCC email
                if(!empty($bccEmail)){
                    $personalization->addBcc($bccEmail);
                }
            
            if (count($sendEmails) > 0){
                $logTblIds = array();
                foreach ($sendEmails as $key => $value) {
                    // Do not send email if email unsubscribed
                    $unSubscribe = $TriggerMailSms->is_unsubscribe('email',$value);
                    if(!$unSubscribe){
                        $insLog = array(
                                    'from_email' => $fromEmail,
                                    'to_email' => $value,
                                    'subject' => $emailSubject,
                                    'message' => $message,
                                    'status' => "pending",
                                    'name' => !empty($extraParams['email_name']) ? $extraParams['email_name'] : '',
                                    'user_email' => !empty($extraParams['user_email']) ? $extraParams['user_email'] : '',
                                );

                        $logId = $pdo->insert("email_log", $insLog);

                        $logTblIds[] = $logId;
                        $custArg = "salvasen_".$logId;

                        $personalMail = clone $personalization;
                        $personalMail->addCustomArg("user_id",$custArg);

                        $toEmailObj = new SendGrid\Email(null, $value);
                        $personalMail->addTo($toEmailObj);
                       
                        $mail->addPersonalization($personalMail);
                    }
                }

                if(!empty($logTblIds)){
                    $sendGridObj = new \SendGrid($SENDGRID_API_KEY);
                    try {
                        $statusCode = 202;
                        if($SITE_ENV == 'Live') {
                            $response = $sendGridObj->client->mail()->send()->post($mail);
                            $statusCode = $response->statusCode();
                        }

                        if($statusCode == 202){
                            $mailStatus = "success";
                        }else{
                            $mailStatus = "fail";
                        }
                        $updParams = array("status" => $mailStatus,"status_code" => !empty($statusCode) ? $statusCode : 0);
                        $updWhere = array(
                                        'clause' => "id IN (".implode(",", $logTblIds).")",
                                        'params' => array()
                                    );
                        $pdo->update('email_log', $updParams, $updWhere);
                    } catch (Exception $e) {
                        echo 'Caught exception: '. $e->getMessage() ."\n";
                    }
                }
            }
        }
    return $mailStatus;
}

function send_sms_to_phone($toPhone,$message = '',$params = array(),$extraParams = array()){
    global $pdo, $smsServiceProvider, $SITE_ENV,$DEFAULT_TRIGGER_SMS,$testSmsPhoneNoArr,$callingCode,$callingCodeReplace;

    $smsStatus = 'fail';
    $TriggerMailSms = new TriggerMailSms();

    if($SITE_ENV!='Live') {
        $toPhone = $DEFAULT_TRIGGER_SMS;
    }

    // Do not send sms if phone unsubscribed
    $unSubscribe = $TriggerMailSms->is_unsubscribe('sms','',$toPhone);

    // trigger smartTagReplace
    if(!empty($params)){
        foreach ($params as $placeholder => $value) {
            if ($placeholder == 'USER_IDENTITY') {
                continue;
            }

            if ($placeholder == 'link' && is_array($value)) {
                foreach ($value as $linkPlaceholder => $linkValue) {
                    $message = str_replace("[[" . $linkPlaceholder . "]]", $linkValue, $message);
                }
            } else {
                $message = str_replace("[[" . $placeholder . "]]", $value, $message);
            }
        }
    }

    if(!empty($toPhone) && !empty($message) && $unSubscribe == false){
        
        $phone = str_replace($callingCode,$callingCodeReplace, $toPhone);
        if(in_array($phone, $testSmsPhoneNoArr)){
            $country_code = '+91';
            $toPhone = $country_code . $phone;
        }

        $serviceParams = array(
            "sms_name" => checkIsset($extraParams['sms_name']),
            "user_phone" => checkIsset($extraParams['user_phone']),
        );

        if($smsServiceProvider == 'Twilio'){
            $smsResponse = trigger_sms_twillio($toPhone,$message,$serviceParams);
        }else if($smsServiceProvider == 'Telnyx'){
            $smsResponse = trigger_sms_telnyx($toPhone,$message,$serviceParams);
        }else if($smsServiceProvider == 'Plivo'){
            $smsResponse = trigger_sms_plivo($toPhone,$message,$serviceParams);
        }
        $smsStatus = !empty($smsResponse["smsStatus"]) ? strtolower($smsResponse["smsStatus"]) : 'fail';

    }
    return $smsStatus;
}
function trigger_sms_twillio($toPhone,$message,$params = array()){
    global $pdo, $TwilioNumber, $TwilioAccountSid, $TwilioAuthToken, $HOST, $SITE_ENV;
    $response = array("smsStatus" => "fail","log_id" => 0);
   
    $client = new Twilio\Rest\Client($TwilioAccountSid, $TwilioAuthToken);
    try {
        $smsParams =  array('from' => $TwilioNumber, 'body' => $message);
        $smsId = "0";
        if($SITE_ENV == 'Live') {
            $url = $HOST."/twilio_api/twilioSmsStatusActivity.php";
            $smsParams['statusCallback'] = $url;
            $msgResponse = $client->messages->create($toPhone,$smsParams);
            $smsId = $msgResponse->sid;
        }
        $smsStatus = 'Success';
    } catch (Exception $ex) {
        $smsStatus = 'Fail';
    }
     
    if(!empty($smsStatus)){
        $insLog = array(
            'trigger_id' => checkIsset($params["trigger_id"]),
            'message_id' => checkIsset($smsId), 
            'from_number' => $TwilioNumber,
            'to_number' => $toPhone,
            'message' => $message,
            'status' => $smsStatus,
            'name' =>  checkIsset($params["sms_name"]),
            'user_phone' => checkIsset($params["user_phone"]),
        );
        $log_id = $pdo->insert("sms_log", $insLog);
        $response["smsStatus"] = $smsStatus;
        $response["log_id"] = $log_id;
    }
    return $response;
}
function trigger_sms_telnyx($toPhone,$message,$params = array()){
    global $pdo, $TelnyxNumber, $TelnyxApiKey, $HOST, $SITE_ENV;
    
    $response = array("smsStatus" => "fail","log_id" => 0);
    $error_code = '';
    $error_message = '';
    \Telnyx\Telnyx::setApiKey($TelnyxApiKey);
    try {
        $smsParams =  array('from' => $TelnyxNumber, 'to' => $toPhone, 'text' => $message);
        $smsId = "0";
        if($SITE_ENV == 'Live') {
            $url = $HOST."/telnyxReadSms.php";
            $smsParams['webhook_url'] = $url;
            $msgResponse = \Telnyx\Message::Create($smsParams);
            $smsId = $msgResponse->id;
        }
        $smsStatus = 'Success';
    } catch (\Telnyx\Exception\PermissionException $err) {
        $getErr = $err->getError();
        if(!empty($getErr)){
            $error_code = checkIsset($getErr->code);
            $error_message = checkIsset($getErr->title);
        }
        $smsStatus = 'Fail';
    } catch (Exception $e){
        $smsStatus = 'Fail';
    }
    if(!empty($smsStatus)){
        $insLog = array(
            'trigger_id' => checkIsset($params["trigger_id"]),
            'message_id' => checkIsset($smsId), 
            'from_number' => $TelnyxNumber,
            'to_number' => $toPhone,
            'message' => $message,
            'status' => $smsStatus,
            'name' =>  checkIsset($params["sms_name"]),
            'user_phone' => checkIsset($params["user_phone"]),
        );
        $log_id = $pdo->insert("sms_log", $insLog);
        //Check is message not delivered then update status
        if(!empty($error_code)) {
            $selSmsCode = "SELECT id FROM sms_error_codes WHERE service='Telnyx' AND code=:code";
            $codeParams = array(":code" => $error_code);
            $resSmsCode = $pdo->selectOne($selSmsCode,$codeParams);
            if(empty($resSmsCode["id"])) {
                $code_data = array(
                    'service' => 'Telnyx',
                    'code' => $error_code,
                    'error_message' => $error_message,
                );
                $pdo->insert("sms_error_codes", $code_data);
            }
            $insLogDet = array(
                'service' => 'Telnyx',
                'log_id' => $log_id,
                'status' => $smsStatus,
                'error_code' => $error_code,
            );
            $pdo->insert("sms_log_details", $insLogDet);
        }
        $response["smsStatus"] = $smsStatus;
        $response["log_id"] = $log_id;
    }
    return $response;
}
function trigger_sms_plivo($toPhone,$message,$params = array()){
    global $pdo, $PlivoNumber, $PlivoApiKey, $PlivoAuthToken, $HOST, $SITE_ENV;
    
    $service = 'Plivo';
    $response = array("smsStatus" => "fail","log_id" => 0);
    $error_code = '';
    $error_message = '';
 
    $client = new \Plivo\RestClient($PlivoApiKey, $PlivoAuthToken);
    try {
        $smsId = "0";
        if($SITE_ENV == 'Live') {
            $url = $HOST."/plivoSmsStatusActivity.php";
            $msgResponse = $client->messages->create([  
                    "src" => $PlivoNumber, // Sender's phone number with country code
                    "dst" => $toPhone, // receiver's phone number with country code
                    "text"  => $message, // Your SMS text message
                    "url"=> $url, // Callback url
                ]);
            $smsId = $msgResponse->messageUuid[0];
        }
        $smsStatus = 'Success';
    }  catch (\Plivo\Exceptions\PlivoResponseException $ex) {
        $error_code = $ex->getStatusCode();
        $error_message = $ex->getErrorMessage();
        $smsStatus = 'Fail';
    }
    if(!empty($smsStatus)){
        $insLog = array(
            'trigger_id' => checkIsset($params["trigger_id"]),
            'message_id' => checkIsset($smsId),
            'from_number' => $PlivoNumber,
            'to_number' => $toPhone,
            'message' => $message,
            'status' => $smsStatus,
            'name' =>  checkIsset($params["sms_name"]),
            'user_phone' => checkIsset($params["user_phone"]),
        );
        $log_id = $pdo->insert("sms_log", $insLog);
        //Check is message not delivered then update status
        if(!empty($error_code)) {
            $errorCodeWhere = array(
                ":code" => $error_code,
                ":service" => $service,
            );
            $errorCodes = $pdo->selectOne("SELECT id FROM sms_error_codes WHERE code=:code AND service=:service",$errorCodeWhere);
            if(empty($errorCodes['id'])) {
                $code_data = array(
                    'service' => $service,
                    'code' => $error_code,
                    'error_message' => $error_message,
                );
                $pdo->insert("sms_error_codes", $code_data);
            }
            $insLogDet = array(
                'service' => $service,
                'log_id' => $log_id,
                'status' => $smsStatus,
                'error_code' => $error_code,
            );
            $pdo->insert("sms_log_details", $insLogDet);
        }
        $response["smsStatus"] = $smsStatus;
        $response["log_id"] = $log_id;
    }
    return $response;
}

?>