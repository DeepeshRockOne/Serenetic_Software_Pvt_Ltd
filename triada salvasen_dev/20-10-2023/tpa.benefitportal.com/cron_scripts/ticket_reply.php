<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time',0);
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
include_once dirname(__DIR__) . '/includes/ticket_functions.php';
error_reporting(E_ALL);
/*
host:- https://mail.cyberxllc.biz:7443

email : support@operation29.com
YwP7F5bA

email : support@benadmin.net
Password:- WUtExP64R

email : support@hikadmin.com
IamThird123

email : support@benefitsportal.net
password:- R4Q9YTvS

email : eticket@operation29.com
password : fFhAf2a28kLX6Wy
*/
$ticket_read = array();
$mail_array1 = array(
	'username' => 'eticket@operation29.com',
	'password' => 'fFhAf2a28kLX6Wy',
	'host' => '{mail.cyberxllc.biz:993/imap/ssl/novalidate-cert}',
);
array_push($ticket_read, $mail_array1);
echo "Start<br/>";
if(count($ticket_read)){
	foreach ($ticket_read as $value) {
		$replies = new ProcessTicketReply($value['username'],$value['password'],$value['host']);
        $replies->close();
	}
}
echo "Completed";
dbConnectionClose();
?>