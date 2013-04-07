<?php
//--------------------------------------------
//			ver 0.3 pre-alpha
//Execute 'dskinfo-listparsable' script and show the output.
//
//by Kodiers. 
//2013
//
//--------------------------------------------

#pragma_mark Functions

function echo_disks_parameters($param) {			// write $param in html table( need for show output)
	echo "<td>";
	echo $param;
	echo "</td>";
}

function echo_disks_parameters_href($param) {		// write $param as hypertextlink
	echo "<td>";
	if ($param === "-") {		//if $param is "-" write it as usual text
		echo $param;
	}
	else {
		echo "<a href=./parse_dskinfo.php?dskinfo=".urlencode($param).">".$param."</a>";
	}
	echo "</td>";
}

#pragma_mark Code

$dsk_full = 'sudo dskinfo list-parsable';			// define command variable( 'dskinfo list-parsable' in our case') need root rights to execute
$output = array();								// define output array
exec($dsk_full, $output, $ret);					// execute command( 'dskinfo' must be in '/usr/sbin' directory
echo "<form action=./parse_dskinfo.php method=post>"; // define form
echo "<table border=1>";						// Draw table
echo "<tr>
		<td>Disk name</td>
		<td>Instance</td>
		<td>Size</td>
		<td>LUN HEX</td>
		<td>LUN DEC</td>
		<td>ZPOOL</td>
		<td>Num path</td>
		<td>Fc speed</td>
		<td>Type</td>
		<td>Label</td>
		<td>Vendor</td>
		<td>Product</td>
		<td>Serial</td>
		<td>Select</td>
		</tr>";
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
	echo "<tr>";
	echo_disks_parameters_href($disk_name);
	echo_disks_parameters($disk_instance);
	echo_disks_parameters($disk_size);
	echo_disks_parameters($disk_lunhex);
	echo_disks_parameters($disk_lundec);
	echo_disks_parameters_href($disk_zpool);
	echo_disks_parameters($disk_numpath);
	echo_disks_parameters($disk_fcspeed);
	echo_disks_parameters($disk_type);
	echo_disks_parameters($disk_label);
	echo_disks_parameters($disk_vendor);
	echo_disks_parameters($disk_product);
	echo_disks_parameters($disk_serial); 
	echo "<td><input type=checkbox name=".$disk_name."></td>";
	echo "</tr>";
}
echo "</table>";
// Write buttons and close form
echo "<input type=submit name=\"Add disk\" value=add><br><br>";					
echo "<input type=submit name=\"Remove disk\" value=remove><br><br>";			
echo "<input type=submit name=\"Edit disk\" value=edit><br><br>";
echo "</form>";
?>
