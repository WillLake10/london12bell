<?php
$sTitle = "2011 Competition";
include('include/pagetop.php');
include('include/class.Results.php');
?>
<h1>2011 Competition</h1>
<p>Nine bands representing eight societies or towers took part in the third London Twelve Bell Striking Competition. The competition was held on Saturday 22nd October at St Leonard's Shoreditch by kind invitation of the Society of Royal Cumberland Youths and enthusiastic permission of the Vicar, the Revd. Paul Turp. The chief judge was William Haynes, ably assisted by Alistair Smith. The Whitechapel Trophy was won by the band from St Michael's Cornhill. Full results are given below.</p>

<?php
$oResults = new Results();
$oResults->fetch_year(2011);
$oResults->show_table();

include('include/pagebottom.php');
?>
