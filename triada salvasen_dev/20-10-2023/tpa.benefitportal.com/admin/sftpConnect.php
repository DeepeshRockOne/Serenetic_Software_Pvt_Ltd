<?php
require 'vendor/autoload.php';
use phpseclib3\Net\SFTP;

$server = 'efx-uat.pnc.com';
$port= 30022;
$username= 'trihealth0t';
$password= 'c3!5aexe';

try {
    $sftp = new SFTP($server, $port);
    
    if (!$sftp->login($username, $password)) {
        die('Login failed');
    }

    $remoteDir = '/outbound';
    $files = $sftp->nlist($remoteDir);

    if (!empty($files)) {
        echo "Files in $remoteDir:" . PHP_EOL;
        foreach ($files as $file) {
            echo "$file" . PHP_EOL;
        }
    } else {
        echo "No files found in $remoteDir." . PHP_EOL;
    }
    $sftp->disconnect();
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
