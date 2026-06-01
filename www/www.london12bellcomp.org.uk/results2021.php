<?php
$sTitle = "2021 Competition";
include('include/pagetop.php');
include('include/class.Results.php');
?>
<h1>2021 Competition</h1>
<p>Ten bands took part in the twelfth London Twelve Bell Striking Competition. The competition was held at St. Mary le Bow on Saturday 23rd.October judged by Richard Pullin, Stephanie Pattendedn and Jonathan Agg.</p>

<p>Congratulations to the winning band, representing the St. Paul's Cathedral Guild of Bellringers. Results are given below..</p>

<?php
$oResults = new Results();
$oResults->fetch_year(2021);
$oResults->show_table();
?>

<p class="MsoNormal"><span style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,serif">The Southwark (Red) band did not complete the touch</span></p>
</p>

<p></p>
<p>A copy of the Judges' Comments can be downloaded from <a href="downloads/Judges' Comments 2021.pdf" target="_blank">Judges' Comments.</a></p>

<?php
include('include/pagebottom.php');
?>