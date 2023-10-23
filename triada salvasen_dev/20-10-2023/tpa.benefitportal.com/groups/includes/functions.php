<?php
function admin_has_access($check = false) {
	if ($check) {
		if (isset($_SESSION['admin_login'])) {
			redirect('dashboard.php');
		}
	}
}

function group_has_access($access){
    $flag = false;
    if (isset($_SESSION['groups']['access'])) {
        if (is_array($access)) {
            if (count(array_intersect($access, array_values($_SESSION['groups']['access'])))) {
                $flag = true;
            }
        } else {
            if (in_array($access, array_values($_SESSION['groups']['access']))) {
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

 
function group_has_menu_access($access){
    $flag = false;
    if (is_array($access)) {
        if (isset($_SESSION['groups']['access']) && $_SESSION['groups']['access'] != "") {
            if (count(array_intersect($access, array_values($_SESSION['groups']['access'])))) {
                $flag = true;
            }
        } else {
            $flag = false;
        }
    } else {
        if (isset($_SESSION['groups']['access']) && $_SESSION['groups']['access'] != "") {
            if (in_array($access, array_values($_SESSION['groups']['access']))) {
                $flag = true;
            }elseif(is_array($_SESSION['groups']['access']) && in_array($access, $_SESSION['groups']['access'])){
                $flag = true;
            }
        } else {
            $flag = false;
        }
    }
    return $flag;
}
?>