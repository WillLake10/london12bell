<?php
$sTitle = "2013 Competition";
include('include/pagetop.php');
include('include/class.Results.php');
?>
<h1>2013 Competition</h1>
<p>Ten bands representing nine societies or towers took part in the fifth London Twelve Bell Striking Competition. The competition was held at St Magnus the Martyr on Saturday October 26th, judged by Martin Whiteley, Maggie Whiteley, and Liz Orme.</p>

<p>Congratulations to the winning band, representing the Society of Royal Cumberland Youths, and many thanks to the University of London Society for organising a very enjoyable day. Results are given below.</p>

<?php
$oResults = new Results();
$oResults->fetch_year(2013);
$oResults->show_table();

include('include/pagebottom.php');
?>
