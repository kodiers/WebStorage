<?php
//--------------------------------------------
//			ver 0.2 pre-alpha
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
	if(func_get_arg(0) == "disk_name")
	{
		echo "disk_name";
	}
	echo $param;
	echo "</td>";
}


$dsk_full = 'sudo dskinfo list-parsable';			// define command variable( 'dskinfo list-parsable' in our case') need root rights to execute
$output = array();								// define output array
exec($dsk_full, $output, $ret);					// execute command( 'dskinfo' must be in '/usr/sbin' directory
echo "<table border=1>";						// Draw table
echo "<tr>
		<td>Disk name</td>
		<td>Instance</td>
		<td>Size</td>
		<td>LUN HEX</td>
		<td>LUN DEC</td>
		<td>ZPOOL</td>
		<td>Num path</td>
		<td>Fc speed</td> a
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
	echo_disks_parametes($disk_name);
	echo_disks_parametes($disk_instance);
	echo_disks_parametes($disk_sizw);
	echo_disks_parametes($disk_lunhex);
	echo_disks_parametes($disk_lundec);
	echo_disks_parametes($disk_zpool);
	echo_disks_parametes($disk_numpath);
	echo_disks_parametes($disk_fcspeed);
	echo_disks_parametes($disk_type);
	echo_disks_parametes($disk_label);
	echo_disks_parametes($disk_vendor);
	echo_disks_parametes($disk_product);
	echo_disks_parametes($disk_serial); //TODO: why it's write "False"? oO
	//echo_disks_parametes($disk_var);
	echo "</tr>";
}
//system($dsk_full);
?>
