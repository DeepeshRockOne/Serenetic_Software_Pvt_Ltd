<?php
function admin_has_access($check = false) {
	if ($check) {
		if (isset($_SESSION['admin_login'])) {
			redirect('dashboard.php');
		}
	}
}

function agent_has_access($access){
    $flag = false;
    if (isset($_SESSION['agents']['access'])) {
        if (is_array($access)) {
            if (count(array_intersect($access, array_values($_SESSION['agents']['access'])))) {
                $flag = true;
            }
        } else {
            if (in_array($access, array_values($_SESSION['agents']['access']))) {
                $flag = true;
            }
        }
    }
    if (!$flag) {
        setNotifyError("you dont have access to this page");        
        echo "<script>window.parent.location = 'dashboard.php'; window.location = 'dashboard.php'; </script>";
        exit;
    }
}

 
function agent_has_menu_access($access){
    $flag = false;
    if (is_array($access)) {
        if (isset($_SESSION['agents']['access']) && $_SESSION['agents']['access'] != "") {
            if (count(array_intersect($access, array_values($_SESSION['agents']['access'])))) {
                $flag = true;
            }
        } else {
            $flag = false;
        }
    } else {
        if (isset($_SESSION['agents']['access']) && $_SESSION['agents']['access'] != "") {
            if (in_array($access, array_values($_SESSION['agents']['access']))) {
                $flag = true;
            }elseif(is_array($_SESSION['agents']['access']) && in_array($access, $_SESSION['agents']['access'])){
                $flag = true;
            }
        } else {
            $flag = false;
        }
    }
    //$flag = true;
    return $flag;
}
function agent_has_additional_access($access){
    $flag = false;
    if (isset($_SESSION['agents']['additionalAccess']) && $_SESSION['agents']['additionalAccess'] != "") {
        $additionalAccess = explode(',', $_SESSION['agents']['additionalAccess']);
        if(in_array($access, $additionalAccess)){
            $flag = true;
        }
    }
    return $flag;
}
?>