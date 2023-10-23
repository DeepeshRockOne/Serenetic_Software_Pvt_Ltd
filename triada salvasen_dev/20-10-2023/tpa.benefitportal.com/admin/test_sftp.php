<?php 
	error_reporting(E_ALL);
	include_once __DIR__ . '/includes/connect.php';
	require_once dirname(__DIR__) . '/libs/php_sftp_libs/Net/SFTP.php';
		set_include_path(dirname(__DIR__) . '/libs/php_sftp_libs/');

    $ftp_server = "efx-uat.pnc.com";
    $ftp_username = "trihealth0t";
    $ftp_userpass = "c3!5aexe";

    $sftp = new Net_SFTP($ftp_server,30022);
    try {
     	$res = $sftp->login($ftp_username, $ftp_userpass);
     	echo "connected";
   	} catch (Exception $e) {
   		echo "fail";
        pre_print($e,false);
        $sftp_errors = $sftp->getSFTPErrors();
		$ssh_errors = $sftp->getErrors();
		pre_print($sftp_errors,false);
		pre_print($ssh_errors,false);
        
    }
    $resTest = $sftp->get("/outbound/test.txt");
    pre_print($res,false);
    pre_print($resTest,false);

    /*if ($sftp->login($ftp_username, $ftp_userpass)) {
    	echo "success";
    }else{
    	$sftp_errors = $sftp->getSFTPErrors();
		$ssh_errors = $sftp->getErrors();
    	$sftp_error = $sftp->getLastSFTPError();
    	$sftp_log = $sftp->getSFTPLog();
		echo "Login failed: ";
		pre_print($sftp_errors,false);
		pre_print($ssh_errors,false);
		pre_print($sftp_error,false);
		pre_print($sftp_log,false);
    }*/
 echo "EOF";
          
