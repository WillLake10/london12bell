<?php
$sTitle = "2018 Competition";
include('include/pagetop.php');
include('include/class.Results.php');
?>
<h1>2018 Competition</h1>
<p>Nine bands took part in the tenth London Twelve Bell Striking Competition. The competition was held at St Michael's, Cornhill on Saturday 27th.October judged by David Brown and Cathy Hughes D'Aeth.</p>

<p>Congratulations to the winning band, representing the Society of Royal Cumberland Youths. Results are given below..</p>

<?php
$oResults = new Results();
$oResults->fetch_year(2018);
$oResults->show_table();
?>

<p>Ancient Society of College Youths (e) did not complete the test piece. </p>

<?php
include('include/pagebottom.php');
?>

