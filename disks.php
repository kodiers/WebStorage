<?php

// import main lib
require('includes/main.php');

// navtabs
$tabs = array(
 'DSK INFO' => 'disks.php?mydisk',
 'Physical disks' => 'disks.php?disks',
 'SMART' => 'disks.php?smart',
 'Advanced' => 'disks.php?advanced',
 'I/O monitor' => 'disks.php?monitor',
 'Benchmark' => 'disks.php?benchmark');
if (@$guru['preferences']['advanced_mode'] !== true)
 unset($tabs['Benchmark']);

// select page
if (isset($_GET['smart']))
 $content = content_handle('disks', 'smart');
elseif (isset($_GET['advanced']))
 $content = content_handle('disks', 'advanced');
elseif (isset($_GET['monitor']))
 $content = content_handle('disks', 'monitor');
elseif (isset($_GET['benchmark']))
 $content = content_handle('disks', 'benchmark');
elseif (@isset($_GET['query']))
 $content = content_handle('disks', 'query');
elseif (@isset($_GET['disks']))
 $content = content_handle('disks', 'disks');
elseif (@isset($_GET['poolcreatemessages']))
 $content = content_handle('disks', 'poolcreatemessages');
elseif (@isset($_GET['removepool']))
 $content = content_handle('disks', 'removepool');
elseif (@isset($_GET['editpool']))
 $content = content_handle('disks', 'editpool');
elseif (@isset($_GET['editselectedpool']))
 $content = content_handle('disks', 'editselectedpool');
else
 $content = content_handle('disks', 'mydisk');

// serve page
page_handle($content);
?>
