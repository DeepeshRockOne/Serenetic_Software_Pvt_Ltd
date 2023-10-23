<?php
function redirect($url, $is_parent = false) {
	if ($is_parent) {
		echo "<script>window.parent.location='" . $url . "';</script>";
		exit;
	} else {
		if ($url == "CLOSE_COLORBOX") {
			echo "<script>window.parent.$.colorbox.close();</script>";
		} else {
			echo "<script>window.location='" . $url . "';</script>";
			exit;
		}
	}
}
?>