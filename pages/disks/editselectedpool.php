<?php
 
require_once 'functions.php';

function content_disks_editselectedpool(){
	// variables
	$physpools = array();							// define pool arra
	$physdisks = array();							//define disk array
	$output = array();								// define output array

	$disk_zpools = array();							// define array for working with disk names in $_POST
	$raid_type = NULL;								// define variable for raid type in $_POST
	$pool_create = NULL;							// define variable for create pool command
	$pool_ouput = array();							// define variable for output create pool command
	$pool_retvar = NULL;							// define  returned variable for create pool command
	
	$pool_edit = NULL;
	
	if ($_GET['editselectedpool']) {
		//var_dump($_GET['editselectedpool']);
		$pool_edit = $_GET['editselectedpool'];
	}

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
		
		// check for edit show info only for needed pool
		if(strpos($tmp_str, $pool_edit))
		{
			list($disk_name, $disk_instance, $disk_size, $disk_lunhex, $disk_lundec, $disk_zpool, $disk_numpath, $disk_fcspeed, $disk_type,		// parsing every string in $output array and place every part in corresponding variable
				$disk_label, $disk_vendor, $disk_product, $disk_serial) = explode(":", $tmp_str);
			$physdisks[] = array(
					'DISK_NAME'			=> $disk_name,
					'DISK_INSTANCE'		=> $disk_instance,
					'DISK_SIZE'		=> $disk_size,
					'DISK_LUN_HEX'		=> $disk_lunhex,
					'DISK_LUN_DEC'		=> $disk_lundec,
					'DISK_ZPOOL'		=> $disk_zpool,
					'DISK_NUM_PATH'		=> $disk_numpath,
					'DISK_FC_SPEED'		=> $disk_fcspeed,
					'DISK_TYPE'		=> $disk_type,
					'DISK_LABEL'		=> $disk_label,
					'DISK_VENDOR'		=> $disk_vendor,
					'DISK_PRODUCT'		=> $disk_product,
					'DISK_SERIAL' => $disk_serial
			);
		}
	}
		$physpools[] = array(
				'DISK_ZPOOL'	=> $pool_edit
		);


	// export new tags
	$newtags = array(
			'TABLE_DISKS_ZPOOLS'	=> $physpools,
			'TABLE_DISKS_PARSEDISKS' => $physdisks
	);
	return $newtags;
}
?>