<?php
$sTitle = "2019 Competition";
include('include/pagetop.php');
include('include/class.Results.php');
?>
<h1>2019 Competition</h1>
<p>Ten bands took part in the eleventh London Twelve Bell Striking Competition. The competition was held at All Saints, Kingston upon Thames on Saturday 26th.October judged by Jack Page and Anna Sherwood.</p>

<p>Congratulations to the winning band, representing the Society of Royal Cumberland Youths. Results are given below..</p>

<?php
$oResults = new Results();
$oResults->fetch_year(2019);
$oResults->show_table();
?>

<?php
include('include/pagebottom.php');
?>