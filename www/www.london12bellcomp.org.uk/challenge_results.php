<?php

$embargoDate = "2017-01-20";
$embargoTimeStamp = strtotime($embargoDate);
$currentTimeStamp = time();
$currentDate = date("Y-m-d", $currentTimeStamp);
$embargoed = $currentTimeStamp < $embargoTimeStamp;

include('include/pagetop.php');
?>

<? if (isset($_GET['sneakpeak']) && $_GET['sneakpeak'] == "underwraps"): ?>
    <dl>
        <dt>Current timestamp:</dt><dd><?= $currentTimeStamp ?></dd>
        <dt>Current date:</dt><dd><?= $currentDate ?></dd>
        <dt>Embargo timestamp:</dt><dd><?= $embargoTimeStamp ?></dd>
        <dt>Embargo date:</dt><dd><?= $embargoDate ?></dd>
        <dt>Embargoed:</dt><dd><?php echo($embargoed ? "Yes" : "No") ?></dd>
    </dl>
<? endif; ?>

<? if (!$embargoed || (isset($_GET['sneakpeak']) && $_GET['sneakpeak'] == "underwraps")): ?>
<h1>Ringing World Christmas Judging Challenge Results</h1>
<table class="resultstable">
<tr><th>Position</th><th>Band</th><th>Mark</th><th>Name</th></tr>
<tr><td>1</td><td>D</td><td>88%</td><td>Ancient Society of College Youths</td></tr>
<tr><td>2</td><td>B</td><td>81%</td><td>Society of Royal Cumberland Youths</td></tr>
<tr><td>3</td><td>E</td><td>63%</td><td>Southwark Cathedral</td></tr>
<tr><td>4</td><td>A</td><td>61%</td><td>University of London Society of Change Ringers</td></tr>
<tr><td>5</td><td>C</td><td>60%</td><td>Middlesex County Association & London Diocesan Guild</td></tr>
</table>
<? else: ?>

<h1>Christmas Judging Challenge</h1>
<p>The results of the challenge will be available from January 20th.</p>

<? endif; ?>


<?php
include('include/pagebottom.php');
?>