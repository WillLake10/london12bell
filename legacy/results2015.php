<?php
$sTitle = "2015 Competition";
include('include/pagetop.php');
include('include/class.Results.php');
?>
<h1>2015 Competition</h1>
<p>Ten bands representing nine societies or towers took part in the seventh London Twelve Bell Striking Competition. The competition was held at St Giles Cripplegate on Saturday October 24th, judged by Andrew and Sharon Mills.</p>

<p>Congratulations to the winning band, representing the Ancient Society of College Youths. Results are given below.</p>

<?php
$oResults = new Results();
$oResults->fetch_year(2015);
$oResults->show_table();

include('include/pagebottom.php');
?>
