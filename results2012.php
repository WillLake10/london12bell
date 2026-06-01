<?php
$sTitle = "2012 Competition";
include('include/pagetop.php');
include('include/class.Results.php');
?>
<h1>2012 Competition</h1>
<p>Ten bands representing nine societies or towers took part in the fourth London Twelve Bell Striking Competition. The competition was held Croydon Minster on Saturday October 27th, judged by Robert and Patricia Newton and Tom Hinks.</p>

<p>Congratulations to the winning band, representing St Paul&apos;s Cathedral, and many thanks to Shirley McGill and all at Croydon Minster for organising a very enjoyable day. Results are given below.</p>

<?php
$oResults = new Results();
$oResults->fetch_year(2012);
$oResults->show_table();

include('include/pagebottom.php');
?>
