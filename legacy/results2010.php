<?php
$sTitle = "2010 Competition";
include('include/pagetop.php');
include('include/class.Results.php');
?>
<h1>2010 Competition</h1>
<p>Ten bands representing nine societies or towers took part in the second London Twelve Bell Striking Competition. The competition was held on Saturday 31st October at St Mary-le-Bow, and was judged by Rick Shallcross and Tom Griffiths. The Whitechapel Trophy was won by the Ancient Society of College Youths. Full results are given below.</p>

<?php
$oResults = new Results();
$oResults->fetch_year(2010);
$oResults->show_table();

include('include/pagebottom.php');
?>
