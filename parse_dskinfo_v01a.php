<?php
//--------------------------------------------
//			ver 0.1 pre-alpha
//Execute 'dskinfo-listparsable' script and show the output.
//
//by Kodiers. 
//2013
//
//--------------------------------------------
function parsing_string($string) {				// first vrsion of script - don't need 
	$outputstr = '';
	for ($j = 0; $j <= strlen($string); $j++)
	{
		if ($string[$j] === ":") {
			break;
		} 
		else {
			$outputstr = $outputstr.$string[$j];
		}
	}
	return $outputstr;
}

function echo_disks_parametes($param) {			// write $param in html table( need for show output)
	echo "<td>";
	echo $param;
	echo "</td>";
}


$dsk_full = 'sudo dskinfo list-parsable';			// define command variable( 'dskinfo list-parsable' in our case') need root rights to execute
$output = array();								// define output array
exec($dsk_full, $output, $ret);					// execute command( 'dskinfo' must be in '/usr/sbin' directory
echo "<table border=1>";						// Draw table
echo "<tr>
		<td>Disk partition</td>
		<td>Disk name</td>
		<td>Size</td>
		<td>Parametr_1</td>
		<td>Hren_2</td>
		<td>Pool name</td>
		<td>Hren_3</td>
		<td>Hren_4</td> a
		<td>Type</td>
		<td>Hren_5</td>
		<td>Vendor</td>
		<td>Product</td>
		<td>Serial</td>
		</tr>";
for($i = 0; $i < count($output); $i++)
{
	$tmp_str = $output[$i];				// define temp variable for string in $output array
	// Define variables	
	$disk_name = '';					
	$disk_instance = '';
	$disk_size = '';
	$disk_lunhex = '';
	$disk_lundec = '';
	$disk_zpool = '';
	$disk_numpath = '';
	$disk_fcspeed = '';
	$disk_type = '';
	$disk_label = '';
	$disk_vendor = '';
	$disk_product = '';
	//$disk_serial = string();
	$disk_serial = '';
	settype($disk_serial, "string");
	//$disk_var = '';
	list($disk_name, $disk_instance, $disk_size, $disk_lunhex, $disk_lundec, $disk_zpool, $disk_numpath, $disk_fcspeed, $disk_type,		// parsing every string in $output array and place every part in corresponding variable
			$disk_label, $disk_vendor, $disk_product, $disk_serial) = explode(":", $tmp_str);
	// Draw cells in table
	echo "<tr>";
	//TODO: Need optimize this code 
	echo_disks_parametes($disk_part);
	echo_disks_parametes($disk_name);
	echo_disks_parametes($disk_size);
	echo_disks_parametes($disk_hren1);
	echo_disks_parametes($disk_hren2);
	echo_disks_parametes($disk_pool);
	echo_disks_parametes($disk_hren3);
	echo_disks_parametes($disk_hren4);
	echo_disks_parametes($disk_type);
	echo_disks_parametes($disk_hren5);
	echo_disks_parametes($disk_vendor);
	echo_disks_parametes($disk_product);
	echo_disks_parametes($disk_serial); //TODO: why it's write "False"? oO
	//echo_disks_parametes($disk_var);
	echo "</tr>";
}
//system($dsk_full);
?>
