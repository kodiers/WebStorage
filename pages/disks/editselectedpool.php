<?php
 
require_once 'functions.php';

function content_disks_editselectedpool()
{
	// variables
	$physpools = array();							// define pool arra
	$physdisksmembers = array();							//define disk array for members disks
	$physdisksfree = array();						// define disk array for free disks
	$output = array();								// define output array
	$disk_zpools = array();							// define array for working with disk names in $_POST
	$raid_type = NULL;								// define variable for raid type in $_POST
	$pool_create = NULL;							// define variable for create pool command
	$pool_ouput = array();							// define variable for output create pool command
	$pool_retvar = NULL;							// define  returned variable for create pool command
	$pool_red = NULL;
	$disks_remove = array();
	$disks_add = array();
	
	$pool_edit = NULL;
	
	if ($_GET['editselectedpool']) 
	{
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
			$physdisksmembers[] = array(
					'DISK_MEMBER'			=> $disk_name,
					'DISK_INSTANCE'		=> $disk_instance,
					'DISK_SIZE'		=> $disk_size,
					'DISK_LUN_HEX'		=> $disk_lunhex,
					'DISK_LUN_DEC'		=> $disk_lundec,
					'DISK_NUM_PATH'		=> $disk_numpath,
					'DISK_FC_SPEED'		=> $disk_fcspeed,
					'DISK_TYPE'		=> $disk_type,
					'DISK_LABEL'		=> $disk_label,
					'DISK_VENDOR'		=> $disk_vendor,
					'DISK_PRODUCT'		=> $disk_product,
					'DISK_SERIAL' => $disk_serial
			);
			array_push($disks_remove, $disk_name);
		}
		else 
		{
			list($disk_name, $disk_instance, $disk_size, $disk_lunhex, $disk_lundec, $disk_zpool, $disk_numpath, $disk_fcspeed, $disk_type,		// parsing every string in $output array and place every part in corresponding variable
					$disk_label, $disk_vendor, $disk_product, $disk_serial) = explode(":", $tmp_str);
			// show disks not in pool
			if ($disk_zpool === '-') 
			{
				$physdisksfree[] = array(
					'DISK_FREE'			=> $disk_name,
					'DISK_INSTANCE'		=> $disk_instance,
					'DISK_SIZE'		=> $disk_size,
					'DISK_LUN_HEX'		=> $disk_lunhex,
					'DISK_LUN_DEC'		=> $disk_lundec,
					'DISK_NUM_PATH'		=> $disk_numpath,
					'DISK_FC_SPEED'		=> $disk_fcspeed,
					'DISK_TYPE'		=> $disk_type,
					'DISK_LABEL'		=> $disk_label,
					'DISK_VENDOR'		=> $disk_vendor,
					'DISK_PRODUCT'		=> $disk_product,
					'DISK_SERIAL' => $disk_serial
				);
				array_push($disks_add, $disk_name);
			}
		}
	}
		
	// required libraries
 	activate_library('zfs');
 	
 	// show pool redundancy
 	
 	$zpool_status = `sudo zpool status $pool_edit`;
 	if (strpos($zpool_status,'raidz3') !== false)
 	{
 		$redundancy = 'RAID7 (triple parity)';
 		$pool_red = 'raidz3';
 	}
 	elseif (strpos($zpool_status,'raidz2') !== false)
 	{
 		$redundancy = 'RAID6 (double parity)';
 		$pool_red = 'raidz2';
 	}
 	elseif (strpos($zpool_status,'raidz1') !== false)
 	{
 		$redundancy = 'RAID5 (single parity)';
 		$pool_red = 'raidz';
 	}
 	elseif (strpos($zpool_status,'mirror') !== false)
 	{
 		$redundancy = 'RAID1 (mirroring)';
 		$pool_red = 'mirror';
 	}
 	else
 	{
 		$redundancy = 'RAID0 (no redundancy)';
 	}
	
 	// form pool array
 	
	$physpools[] = array(
		'DISK_ZPOOL'	=> $pool_edit,
		'POOL_REDUNDANCY' => $redundancy
	);
	
	// try to remove disk
	//TODO: don't work now error: cannot remove c8t2d0: operation not supported on this type of pool
	//TODO: need to fix
	if(isset($_GET['r_disk']))
	{
		foreach($disks_remove as $rd => $rdv)
		{
			if(isset($_GET[$rdv]))
			{
				if($pool_red === 'mirror')		// TODO:fix cannot remove more than 1 disk from pool 
				{
					$command_rem_disk = 'sudo zpool detach '.$pool_edit.' '.$rdv;
					//var_dump($command_rem_disk);
					exec($command_rem_disk, $pool_output, $pool_retvar);
					redirect_refresh("Error removing disk from pool!", $pool_retvar); // redirect to error page
				}
				else if ($pool_red === 'raidz')
				{
					$command_rem_disk = 'sudo zpool detach '.$pool_edit.' '.$rdv;
					//var_dump($command_rem_disk);
					exec($command_rem_disk, $pool_output, $pool_retvar);
					redirect_refresh("Error removing disk from pool!", $pool_retvar); // redirect to error page
				}
				else if ($pool_red === 'raidz2')
				{
					$command_rem_disk = 'sudo zpool detach '.$pool_edit.' '.$rdv;
					//var_dump($command_rem_disk);
					exec($command_rem_disk, $pool_output, $pool_retvar);
					redirect_refresh("Error removing disk from pool!", $pool_retvar); // redirect to error page
				}
				else if ($pool_red === 'raidz3')
				{
					$command_rem_disk = 'sudo zpool detach '.$pool_edit.' '.$rdv;
					//var_dump($command_rem_disk);
					exec($command_rem_disk, $pool_output, $pool_retvar);
					redirect_refresh("Error removing disk from pool!", $pool_retvar); // redirect to error page
				}
			}
		}
	}
	
	if(isset($_GET['a_disk']))
	{
		//Add disks to mirrored pool
		if($pool_red === 'mirror')
		{	
			$command_add_disk = 'sudo zpool add '.$pool_edit.' mirror';
			foreach ($disks_add as $ad => $adv) 
			{
				if(isset($_GET[$adv]))
				{
					$command_add_disk = $command_add_disk.' '.$adv;
				}
			}
			exec($command_add_disk, $pool_output, $pool_retvar);
			redirect_refresh("Error attaching disk to pool!", $pool_retvar); // redirect to error page
		}
		//TODO: add disk to another type of pool
		else
		{
			foreach ($disks_add as $ad => $adv)
			{
				if(isset($_GET['$adv']))
				{
					if($pool_red === 'raidz')
					{
						$command_add_disk = 'sudo zpool add '.$pool_edit.' mirror '.$adv;
						exec($command_rem_disk, $pool_output, $pool_retvar);
						redirect_refresh("Error attaching disk to pool!", $pool_retvar); // redirect to error page
					}
					else if($pool_red === 'raidz2')
					{
						$command_add_disk = 'sudo zpool add '.$pool_edit.' mirror '.$adv;
						exec($command_rem_disk, $pool_output, $pool_retvar);
						redirect_refresh("Error attaching disk to pool!", $pool_retvar); // redirect to error page
					}
					else if($pool_red === 'raidz3')
					{
						$command_add_disk = 'sudo zpool add '.$pool_edit.' mirror '.$adv;
						exec($command_rem_disk, $pool_output, $pool_retvar);
						redirect_refresh("Error attaching disk to pool!", $pool_retvar); // redirect to error page
					}
				}
			}
		}
	}


	// export new tags
	$newtags = array(
		'TABLE_DISKS_ZPOOLS'	=> $physpools,
		'TABLE_DISKS_MEMBERSDISKS' => $physdisksmembers,
		'TABLE_DISKS_FREEDISKS' => $physdisksfree
	);
	return $newtags;
}
?>