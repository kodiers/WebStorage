<?php

function content_disks_mydisk()
{

 // variables
 $physdisks = array();
 $output = array();								// define output array
 
 $dsk_full = 'sudo dskinfo list-parsable';			// define command variable( 'dskinfo list-parsable' in our case') need root rights to execute
 exec($dsk_full, $output, $ret);					// execute command( 'dskinfo' must be in '/usr/sbin' directory
 $diskcount = count($output);
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
 	// Draw cells in table
 	if ($disk_zpool === '-')
 	{
 		$disk_zpool = NULL;
 	}
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

 // export new tags
 $newtags = array(
  'PAGE_ACTIVETAB'		=> 'DSK INFO',
  'PAGE_TITLE'			=> 'DSK INFO',
  'TABLE_DISKS_PARSEDISKS'	=> $physdisks,
  'DISKS_DISKCOUNT'		=> $diskcount,
 );
 return $newtags;
}
?>
