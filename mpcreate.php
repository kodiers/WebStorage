<?php

function content_pools_mpcreate()
{

 // variables
 $physdisks = array();
 $output = array();								// define output array
 
 $disk_names = array();							// define array for working with disk names in $_POST
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
 	array_push($disk_names, $disk_name);
 }

 // export new tags
 $newtags = array(
  'PAGE_ACTIVETAB'		=> 'My create',
  'PAGE_TITLE'			=> 'CREATE POOL',
  'TABLE_POOLS_PARSEDISKS'	=> $physdisks,
  'DISKS_DISKCOUNT'		=> $diskcount,
 );
 // Handle form
 if(isset($_POST['add'])){
 	echo "Add pressed!";
 // check raid type
 	if(isset($_POST['raid'])) {
 		switch ($_POST['raid']) {
 			case "RAID0":
 				$raid_type = '';
 				break;
 			case "RAID1":
 				$raid_type = 'mirror';
 				break;
 			case "RAIDZ1":
 				$raid_type = 'raidz';
 				break;
 			case "RAIDZ2":
 				$raid_type = 'raidz2';
 				break;
 			case "RAIDZ3":
 				$raid_type = 'raidz3';
 				break;
 		}
 	}
 	$pool_create = "sudo zpool create ".$_POST['poolname']." ".$raid_type." ";
 	// check members and forming command
 	foreach ($disk_names as $dname => $dname_val){
 		if (isset($_POST[$dname_val])) {
 			$pool_create = $pool_create.$dname_val." ";
 		}
 	}
 	// execute command
 	// TODO: thinck about check + work with execute
 	if(isset($_POST['raid'])) {
 			exec($pool_create, $pool_output, $pool_retvar);
 			// var_dump($pool_ouput);
 			//TODO: try to handle error when creating pool
 			if ($pool_retvar != 0){
 				$querystr = "Error creating pool!";
 				//for ($j = 0; $j < count($pool_ouput); $j++)
 				//{
 				//	$querystr = $querystr.$pool_ouput[$j];
 				//}
 				header("Location:./disks.php?poolcreatemessages=$querystr");
 			}
 			else {
 			// redirect to main page
 				$page = $_SERVER['PHP_SELF'];
 				$sec = 1;
 				header("Refresh: $sec; url=$page");
 			}
 	}
 		
 }
 return $newtags;
}

function submit_pools_createpool()
{
 // required libraries
 activate_library('guru');
 activate_library('zfs');

 // POST variables
 sanitize(@$_POST['new_zpool_name'], null, $new_zpool, 24);
 $mountpoint = @$_POST['new_zpool_mountpoint'];
 $sectorsize = @$_POST['new_zpool_sectorsize'];
 $url = 'pools.php?mpcreate';
 $url2 = 'pools.php?query='.$new_zpool;

 // sanity
 if ($new_zpool != @$_POST['new_zpool_name'])
  friendlyerror('please use only alphanumerical characters for the pool name',
   $url);
 if (strlen($new_zpool) < 1)
  friendlyerror('please enter a name for your new pool', $url);

 // mountpoint
 if ((strlen($mountpoint) > 1) AND ($mountpoint{0} == '/'))
  $options_str = '-m '.$mountpoint.' ';
 else
 {
  // use default mountpoint if not explicitly defined
  $mountpoint = '/' . $new_zpool;
  $options_str = '';
 }

 // filesystem version
 $spa = (int)@$_POST['new_zpool_spa'];
 $zpl = (int)@$_POST['new_zpool_zpl'];
 $sys = guru_zfsversion();
 if (($spa > 0) AND ($spa <= $sys['spa']))
  $options_str .= '-o version='.$spa.' ';
 if (($zpl > 0) AND ($zpl <= $sys['zpl']))
  $options_str .= '-O version='.$zpl.' ';
 $options_str .= '-O atime=off ';

 // extract and format submitted disks to add
 if (is_numeric($sectorsize))
  $vdev = zfs_extractsubmittedvdevs($url, true);
 else
  $vdev = zfs_extractsubmittedvdevs($url, false);
 $redundancy = zfs_extractsubmittedredundancy($_POST['new_zpool_redundancy'],
  $vdev['member_count'], $url);

 // check for member disks
 if ($vdev['member_count'] < 1)
  error('vdev member count zero');

 // warn for RAID0 with more than 1 disk (could be a mistake)
 if (($vdev['member_count'] > 1) AND ($redundancy == ''))
  page_feedback('you selected RAID0 with more than one disk; are you sure '
   .'that is what you wanted?', 'a_warning');

 // array with commands to execute
 $commands = array();

 // handle sector size overrides
 // we do this by creating GNOP providers which override the sector size
 // this will force ashift to be different (inspect using zdb)
 // this also works across reboots, and the .nop providers are only needed once
 if (is_numeric($sectorsize))
  if (is_array($vdev['member_disks']))
   foreach ($vdev['member_disks'] as $vdevdisk)
    $commands[] = '/sbin/gnop create -S '.(int)$sectorsize.' /dev/'.$vdevdisk;

 // TODO: SECURITY
 // assemble zpool create command
 $commands[] = '/sbin/zpool create '.$options_str.$new_zpool.' '.$redundancy.' '
  .$vdev['member_str'];
 $commands[] = '/usr/sbin/chown -R nfs:nfs '.$mountpoint;

 // defer to dangerouscommand function
 dangerouscommand($commands, $url2);
}

?>
