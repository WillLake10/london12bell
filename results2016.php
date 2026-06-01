<?php
$sTitle = "2016 Competition";
include('include/pagetop.php');
include('include/class.Results.php');
?>
<h1>2016 Competition</h1>
<p>Nine bands representing eight societies or towers took part in the eighth London Twelve Bell Striking Competition. The competition was held at St Sepulchre-without-Newgate on Saturday October 22nd, judged by Linda Garton, John Loveless, and Simon Read.</p>

<p>Congratulations to the winning band, representing St Paul's Cathedral. Results are given below.</p>

<?php
$oResults = new Results();
$oResults->fetch_year(2016);
$oResults->show_table();

include('include/pagebottom.php');
?>
