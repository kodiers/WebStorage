<?php
	function content_disks_poolcreatemessages() {
		if(isset($_GET['pool_output'])) {
			for($i = 0; $i < count($pool_output); $i++){
				$err[] = array(
					'ERROR_STRING' => $pool_output[$i]);
			}
		}
		$newtags = array(
				'TABLE_DISKS_ERROR' => $err);
		return $newtags;
	}
?>