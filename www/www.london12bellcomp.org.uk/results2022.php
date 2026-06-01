<?php
$sTitle = "2022 Competition";
include('include/pagetop.php');
include('include/class.Results.php');
?>
<h1>2022 Competition</h1>
<p>Ten bands took part in the thirteenth London Twelve Bell Striking Competition. The competition was held at St. Paul's 
Cathedral on Saturday 22nd. October judged by Chris Kippin and Andrew Meyer.</p>

<p>Congratulations to the winning band, representing the Ancient Sociey of College Youths. Results are given below..</p>

<?php
$oResults = new Results();
$oResults->fetch_year(2022);
$oResults->show_table();
?>

<?php
include('include/pagebottom.php');
?>