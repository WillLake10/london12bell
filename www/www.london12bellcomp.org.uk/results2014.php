<?php
$sTitle = "2014 Competition";
include('include/pagetop.php');
include('include/class.Results.php');
?>
<h1>2014 Competition</h1>
<p>Ten bands representing eight societies or towers took part in the sixth London Twelve Bell Striking Competition. The competition was held at St Mary-le-Bow on Saturday October 25th, judged by David Dearnley, Jennie Town, and David House.</p>

<p>Congratulations to the winning band, representing the Ancient Society of College Youths. Results are given below.</p>

<?php
$oResults = new Results();
$oResults->fetch_year(2014);
$oResults->show_table();

include('include/pagebottom.php');
?>
