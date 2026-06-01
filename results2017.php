<?php
$sTitle = "2017 Competition";
include('include/pagetop.php');
include('include/class.Results.php');
?>
<h1>2017 Competition</h1>
<p>Eight bands took part in the ninth London Twelve Bell Striking Competition. The competition was held at St Leonard, Shoreditch on Saturday October 22nd, judged by Simon Linford, Jack Page, and Alistair Cherry.</p>

<p>Congratulations to the winning band, representing the Society of Royal Cumberland Youths. Results are given below.</p>

<?php
$oResults = new Results();
$oResults->fetch_year(2017);
$oResults->show_table();

include('include/pagebottom.php');
?>
