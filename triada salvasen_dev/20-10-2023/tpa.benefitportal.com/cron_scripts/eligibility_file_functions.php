<?php 
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
function csvToArraywithFields($filename) {
   $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
   $csv = array_map('str_getcsv', file($filename));
   $rowsWithKeys = [];
   if(!empty($csv)) {
      $headers = $csv[0];
      if($fileExtension != 'txt'){
         unset($csv[0]);
      }

      foreach ($csv as $row) {
         $newRow = [];
         foreach ($headers as $k => $key) {
            //echo"<br>Key= ".$k." Value=".$key." Data=".$row[$k];
            if (trim($key) != "") {
               $newRow[$key] = $row[$k];
            }
         }
         $rowsWithKeys[] = $newRow;
      }
   }
   return $rowsWithKeys;
}
function compareASHRow($file_row_tmp,$db_row_tmp) {
 $file_row = array();
 $db_row = array();
 $file_row = $file_row_tmp;
 $db_row = $db_row_tmp;
 $flag = false;
 $state = getname("states_c", $db_row_tmp['state'], "short_name", "name");

 if($db_row['fname'] != ''){
     $db_row['fname'] = preg_replace('~[-.,_\\\\/:*?"<>|]~', ' ',  $db_row['fname']);  
 }
 if($db_row['lname'] != ''){
     $db_row['lname'] = preg_replace('~[-.,_\\\\/:*?"<>|]~', ' ',  $db_row['lname']);     
 } 
 if($db_row['mname'] != ''){
    $db_row['mname'] = preg_replace('~[-.,_\\\\/:*?"<>|]~', ' ',  $db_row['mname']);     
 }
 if($file_row['FIRSTNAME'] != ''){
    $file_row['FIRSTNAME'] = preg_replace('~[-.,_\\\\/:*?"<>|]~', ' ',  $file_row['FIRSTNAME']);
 } 
 if($file_row['LASTNAME'] != ''){
    $file_row['LASTNAME'] = preg_replace('~[-.,_\\\\/:*?"<>|]~', ' ',  $file_row['LASTNAME']);
 }
 if(strtolower($file_row['FIRSTNAME']) != strtolower($db_row['fname'])){
    $flag = true;
 }else if(strtolower($file_row['MIDDLEINITIAL']) != strtolower($db_row['mname'])){
    $flag = true;
 }else if(strtolower($file_row['LASTNAME']) != strtolower($db_row['lname'])){
    $flag = true;
 }else if(strtolower($file_row['CITY']) != strtolower($db_row['city'])){
    $flag = true;
 }else if(strtolower($file_row['STATE']) != strtolower($state)){
    $flag = true;
 }else if(strtolower($file_row['ZIPCODE']) != strtolower($db_row['zip'])){
    $flag = true;
 }else if(strtolower($file_row['PHONE_NUMBER']) != strtolower($db_row['cell_phone'])){
    $flag = true;
 }else if(strtolower($file_row['EMAIL_ADDRESS']) != strtolower($db_row['email'])){
    $flag = true;
 }
 return $flag;
}
function dependentsASHRow($file_row_tmp,$db_row_tmp) {
 $file_row = array();
 $db_row = array();
 $file_row = $file_row_tmp;
 $db_row = $db_row_tmp;
 $flag = false;
 $state = getname("states_c", $db_row_tmp['state'], "short_name", "name");
  if($db_row['fname'] != ''){
           $db_row['fname'] = preg_replace('~[-.,_\\\\/:*?"<>|]~', ' ',  $db_row['fname']);
 }
 if($db_row['lname'] != ''){
     $db_row['lname'] = preg_replace('~[-.,_\\\\/:*?"<>|]~', ' ',  $db_row['lname']);
 }
 if($file_row['FIRSTNAME'] != ''){
    $file_row['FIRSTNAME'] = preg_replace('~[-.,_\\\\/:*?"<>|]~', ' ',  $file_row['FIRSTNAME']);
 } 
 if($file_row['LASTNAME'] != ''){
    $file_row['LASTNAME'] = preg_replace('~[-.,_\\\\/:*?"<>|]~', ' ',  $file_row['LASTNAME']);
 }
 if(strtolower($file_row['FIRSTNAME']) != strtolower($db_row['fname'])){
    $flag = true;
 }else if(strtolower($file_row['MIDDLEINITIAL']) != strtolower($db_row['mname'])){
    $flag = true;
 }else if(strtolower($file_row['LASTNAME']) != strtolower($db_row['lname'])){
    $flag = true;
 }else if(strtolower($file_row['PHONE_NUMBER']) != strtolower($db_row['phone'])){
    $flag = true;
 }else if(strtolower($file_row['EMAIL_ADDRESS']) != strtolower($db_row['email'])){
    $flag = true;
 }
 return $flag;
}
function compareLoomisMemberRow($file_row_tmp,$db_row_tmp) {
 $file_row = array();
 $db_row = array();
 $file_row = $file_row_tmp;
 $db_row = $db_row_tmp;
 $flag = false;
 $state = getname("states_c", $db_row_tmp['state'], "short_name", "name");

 if($db_row['fname'] != ''){
     $db_row['fname'] = preg_replace('~[-.,_\\\\/:*?"<>|]~', ' ',  $db_row['fname']);  
 }
 if($db_row['lname'] != ''){
     $db_row['lname'] = preg_replace('~[-.,_\\\\/:*?"<>|]~', ' ',  $db_row['lname']);     
 } 
 if($db_row['mname'] != ''){
    $db_row['mname'] = preg_replace('~[-.,_\\\\/:*?"<>|]~', ' ',  $db_row['mname']);     
 }
 if($file_row['First name'] != ''){
    $file_row['First name'] = preg_replace('~[-.,_\\\\/:*?"<>|]~', ' ',  $file_row['First name']);
 } 
 if($file_row['Last name'] != ''){
    $file_row['Last name'] = preg_replace('~[-.,_\\\\/:*?"<>|]~', ' ',  $file_row['Last name']);
 }
 if(strtolower($file_row['First name']) != strtolower($db_row['fname'])){
    $flag = true;
 }else if(strtolower($file_row['Middle name']) != strtolower($db_row['mname'])){
    $flag = true;
 }else if(strtolower($file_row['Last name']) != strtolower($db_row['lname'])){
    $flag = true;
 }else if(strtolower($file_row['City']) != strtolower($db_row['city'])){
    $flag = true;
 }else if(strtolower($file_row['State']) != strtolower($state)){
    $flag = true;
 }else if(strtolower($file_row['Zip']) != strtolower($db_row['zip'])){
    $flag = true;
 }
 return $flag;
}
function compareLoomisDependentRow($file_row_tmp,$db_row_tmp) {
 $file_row = array();
 $db_row = array();
 $file_row = $file_row_tmp;
 $db_row = $db_row_tmp;
 $flag = false;

 if($db_row['fname'] != ''){
     $db_row['fname'] = preg_replace('~[-.,_\\\\/:*?"<>|]~', ' ',  $db_row['fname']);  
 }
 if($db_row['lname'] != ''){
     $db_row['lname'] = preg_replace('~[-.,_\\\\/:*?"<>|]~', ' ',  $db_row['lname']);     
 } 
 if($db_row['mname'] != ''){
    $db_row['mname'] = preg_replace('~[-.,_\\\\/:*?"<>|]~', ' ',  $db_row['mname']);     
 }
 if($file_row['First name'] != ''){
    $file_row['First name'] = preg_replace('~[-.,_\\\\/:*?"<>|]~', ' ',  $file_row['First name']);
 } 
 if($file_row['Last name'] != ''){
    $file_row['Last name'] = preg_replace('~[-.,_\\\\/:*?"<>|]~', ' ',  $file_row['Last name']);
 }
 if(strtolower($file_row['First name']) != strtolower($db_row['fname'])){
    $flag = true;
 }else if(strtolower($file_row['Middle name']) != strtolower($db_row['mname'])){
    $flag = true;
 }else if(strtolower($file_row['Last name']) != strtolower($db_row['lname'])){
    $flag = true;
 }
 return $flag;
}
?>