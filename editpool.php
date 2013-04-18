<?php

require_once 'functions.php';

function content_disks_editpool(){
	// variables
	$physdisks = array();
	$output = array();								// define output array
	
	$disk_zpools = array();							// define array for working with disk names in $_POST
	$raid_type = NULL;								// define variable for raid type in $_POST
	$pool_create = NULL;							// define variable for create pool command
	$pool_ouput = array();							// define variable for output create pool command
	$pool_retvar = NULL;							// define  returned variable for create pool command
	
	$dsk_full = 'sudo dskinfo list-parsable';			// define command variable( 'dskinfo list-parsable' in our case') need root rights to execute
	exec($dsk_full, $output, $ret);					// execute command( 'dskinfo' must be in '/usr/sbin' directory
	for($i = 0; $i < count($output); $i++)
	{
		// Define variables
		$tmp_str = $output[$i];				// define temp variable for string in $output array
		$disk_name = '';					// Disk name
		$disk_instance = '';				// Disk instance
		$disk_size = '';					// Disk size
		$disk_lunhex = '';					// Logical Unit Number in hex
		$disk_lundec = '';					// Logical Unit Number in decimal
		$disk_zpool = '';					// Used in imported, exported pool
		$disk_numpath = '';					// Number of paths to disk
		$disk_fcspeed = '';					// Speed of Fibre Channel link to disk
		$disk_type = '';					// Driver type
		$disk_label = '';					// Is the disk labeled (y/n)
		$disk_vendor = '';					// The disk vendor field from the disk
		$disk_product = '';					// The product field from the disk
		$disk_serial = '';					// The disk serial number if availabe
		list($disk_name, $disk_instance, $disk_size, $disk_lunhex, $disk_lundec, $disk_zpool, $disk_numpath, $disk_fcspeed, $disk_type,		// parsing every string in $output array and place every part in corresponding variable
		$disk_label, $disk_vendor, $disk_product, $disk_serial) = explode(":", $tmp_str);
		// Delete '-' and fullfil temporary array
		if ($disk_zpool === '-')
		{
			$disk_zpool = NULL;
		}
		else {
 			array_push($disk_zpools, $disk_zpool);
		}
	}
	
	// delete all nonunique values
	$uniq_vdisk = array_unique($disk_zpools);
	foreach ($uniq_vdisk as $udisk => $vudisk){
		$physdisks[] = array(
				'DISK_ZPOOL'	=> $vudisk
				);
	}
	
	// forming command
	if (isset($_POST['edit']))
	{
		// edit pool
		/*foreach ($uniq_vdisk as $disk => $vdisk){
			if(isset($_POST[$vdisk])){
				$pool_remove = "sudo zpool destroy ".$vdisk;
				exec($pool_remove, $pool_ouput, $pool_retvar);
				if($pool_retvar != 0) {
					redirect_refresh("Error removing pool!", $pool_retvar); // redirect to error page
					break;
				}
				redirect_refresh(NULL, 0);
			}
		}*/
	}

	// export new tags
	$newtags = array(
  		'TABLE_DISKS_ZPOOLS'	=> $physdisks
		);
	return $newtags;
}
?>