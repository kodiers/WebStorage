<?php


// function for check error and redirect to needed page
function redirect_refresh($str, $var){
	// if error redirect to error page
	if ($var != 0){
		$querystr = $str;
		header("Location:./disks.php?poolcreatemessages=$querystr");
	}
	else {
		// redirect to main page
		$page = $_SERVER['PHP_SELF'];
		$sec = 1;
		header("Refresh: $sec; url=$page");
	}
}


?>