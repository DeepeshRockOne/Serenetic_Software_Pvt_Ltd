<?php
$username = "administration123_myhp";
$password = "ryv2nMrb";
$url      = 'mft.teladoc.com';
// Make our connection
include('Net/SFTP.php');

$sftp = new Net_SFTP($url);
if (!$sftp->login($username, $password)) {
    exit('Login Failed');
}

// outputs the contents of filename.remote to the screen
//echo $sftp->get('filename.remote');
// copies filename.remote to filename.local from the SFTP server
$sftp->get('outbound/MyHealthPass_20171010.csv.results.csv', 'MyHealthPass_20171010.csv.results.csv');
?>
