<?php
$sTitle = "2009 Competition";
include('include/pagetop.php');
include('include/class.Results.php');
?>
<h1>2009 Competition</h1>
<p>Ten bands, representing eight societies or towers, took part in the inaugural London Twelve Bell Striking Competition. The competition was held on Saturday 24th October on the new ring at St Magnus the Martyr, and was judged by David Pipe and Philip Earis. The Whitechapel Trophy was won by the Ancient Society of College Youths. Full results are given below.</p>

<?php
$oResults = new Results();
$oResults->fetch_year(2009);
$oResults->show_table();

include('include/pagebottom.php');
?>