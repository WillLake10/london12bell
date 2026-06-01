<?php
$sTitle = "Band Details";
include('include/pagetop.php');
include('include/class.Results.php');
$iYear = $_GET['year'];
$iBand = $_GET['band'];
$oResults = new Results();
$oResults->fetch_year($iYear);

$sBandName = $oResults->get_band_name($iBand);
$sBandPhoto = $oResults->get_band_photo($iBand);

if($sBandName) {
	print "<h2>$iYear Band Details: $sBandName</h2>";
}
else {
	print "<h2>Band Details</h2>";
}

if($sBandPhoto) {
	print "<img class='bandphoto' src='{$sTopPath}images/bands/$iYear/$sBandPhoto' />";
	$sCaption = $oResults->get_band_caption($iBand);
	if($sCaption) {
			print $sCaption;
	} else {
		print "<p>&nbsp;</p>";
	}
}
else {
	$sTable = $oResults->get_band_table($iBand);
	if($sTable) {
			print $sTable;
	}
}

include('include/pagebottom.php');
?>
