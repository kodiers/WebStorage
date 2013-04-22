<?php
	function content_disks_poolcreatemessages() {
		if(isset($_GET['poolcreatemessages'])) {
				$err = $_GET['poolcreatemessages'];
		}
		$newtags = array(
				'ERROR_STRING' => $err);
		return $newtags;
	}
?>