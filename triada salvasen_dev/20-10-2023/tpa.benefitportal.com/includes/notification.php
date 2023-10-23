<?php
function setNotifiy($key, $value){
  $_SESSION['notify'][$key] = $value;
}

function getNotify($key){
  $msg = $_SESSION['notify'][$key];
  unset($_SESSION['notify'][$key]);
  return $msg;
}

function hasNotify($key){
  return isset($_SESSION['notify'][$key]);
}

function setNotifySuccess($value,$lang = "PHP"){
  if($lang == "PHP") {
  	setNotifiy('success', $value);
  } else if($lang == "JS") {
		echo '<script>window.parent.setNotifySuccess("' . stripslashes($value) . '")</script>';
	}
}

function setNotifyError($value,$lang = "PHP"){
	if($lang == "PHP") {
  	setNotifiy('error', $value);		
	} else if($lang == "JS") {
		echo '<script>window.parent.setNotifyError("' . stripslashes($value) . '")</script>';
	}
}

function setNotifyAlert($value,$lang = "PHP"){
	if($lang == "PHP") {
  	setNotifiy('alert', $value);
  } else if($lang == "JS") {
		echo '<script>window.parent.setNotifyAlert("' . stripslashes($value) . '")</script>';
	}  
}

?>