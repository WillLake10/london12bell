<?php
$sTitle = "2023 Competition";
include('include/pagetop.php');
include('include/class.Results.php');
?>
<h1>2023 Competition</h1>
<p>Ten bands took part in the fourteenth London Twelve Bell Striking Competition. The competition was held at St. Magnus
the Martyr, London Bridge on Saturday 28th. October judged by Alex Riley and Catherine Morley.</p>

<p>Congratulations to the winning band, representing the Society of Royal Cumberland Youths. Results are given below..</p>

<?php
$oResults = new Results();
$oResults->fetch_year(2023);
$oResults->show_table();
?>

<?php
include('include/pagebottom.php');
?>