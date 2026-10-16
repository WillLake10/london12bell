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
<h1>Ringing World Christmas Judging Challenge</h1>
<p>Have you every fancied being a judge at a 12 Bell competition? Have a go at marking five touches from a past London 12 Bell Competition and see how well your scores compare with the official results. You can use our ready-made <a href="marking-sheet.pdf" target="_blank">marking sheet</a> if you wish, which is available to download and print.</p>
<table class="resultstable">
<tr><th>Name</th><th>Duration</th><th>Touch</th></tr>
<tr><td>Team A</td><td>13'36"</td><td><audio year="challenge" band="A" controls preload="metadata"><source src="http://london12bellcompetition.org/touches/challenge/RW-CJC_teamA.mp3" type="audio/mpeg" /><a href="http://london12bellcompetition.org/touches/challenge/RW-CJC_teamA.mp3">touch</a></td></tr>
<tr><td>Team B</td><td>13'45"</td><td><audio year="challenge" band="B" controls preload="metadata"><source src="http://london12bellcompetition.org/touches/challenge/RW-CJC_teamB.mp3" type="audio/mpeg" /><a href="http://london12bellcompetition.org/touches/challenge/RW-CJC_teamB.mp3">touch</a></td></tr>
<tr><td>Team C</td><td>13'29"</td><td><audio year="challenge" band="C" controls preload="metadata"><source src="http://london12bellcompetition.org/touches/challenge/RW-CJC_teamC.mp3" type="audio/mpeg" /><a href="http://london12bellcompetition.org/touches/challenge/RW-CJC_teamC.mp3">touch</a></td></tr>
<tr><td>Team D</td><td>13'51"</td><td><audio year="challenge" band="D" controls preload="metadata"><source src="http://london12bellcompetition.org/touches/challenge/RW-CJC_teamD.mp3" type="audio/mpeg" /><a href="http://london12bellcompetition.org/touches/challenge/RW-CJC_teamD.mp3">touch</a></td></tr>
<tr><td>Team E</td><td>13'27"</td><td><audio year="challenge" band="E" controls preload="metadata"><source src="http://london12bellcompetition.org/touches/challenge/RW-CJC_teamE.mp3" type="audio/mpeg" /><a href="http://london12bellcompetition.org/touches/challenge/RW-CJC_teamE.mp3">touch</a></td></tr>
</table>

<h2>Results</h2>

<p>The results of the challenge and the bonus challenge to name the teams are now available <a href="/challenge_results.php">here.</a></p>

<? else: ?>

<h1>Ringing World Christmas Judging Challenge</h1>
<p>Have you every fancied being a judge at a 12 Bell competition? Have a go at marking five touches from a past London 12 Bell Competition and see how well your scores compare with the official results. You can use our ready-made <a href="marking-sheet.pdf" target="_blank">marking sheet</a> if you wish, which is available to download and print.</p>
<table class="resultstable">
<tr><th>Name</th><th>Duration</th><th>Touch</th></tr>
<tr><td>Team A</td><td>13'36"</td><td><audio year="challenge" band="A" controls preload="metadata"><source src="http://london12bellcompetition.org/touches/challenge/RW-CJC_teamA.mp3" type="audio/mpeg" /><a href="http://london12bellcompetition.org/touches/challenge/RW-CJC_teamA.mp3">touch</a></td></tr>
<tr><td>Team B</td><td>13'45"</td><td><audio year="challenge" band="B" controls preload="metadata"><source src="http://london12bellcompetition.org/touches/challenge/RW-CJC_teamB.mp3" type="audio/mpeg" /><a href="http://london12bellcompetition.org/touches/challenge/RW-CJC_teamB.mp3">touch</a></td></tr>
<tr><td>Team C</td><td>13'29"</td><td><audio year="challenge" band="C" controls preload="metadata"><source src="http://london12bellcompetition.org/touches/challenge/RW-CJC_teamC.mp3" type="audio/mpeg" /><a href="http://london12bellcompetition.org/touches/challenge/RW-CJC_teamC.mp3">touch</a></td></tr>
<tr><td>Team D</td><td>13'51"</td><td><audio year="challenge" band="D" controls preload="metadata"><source src="http://london12bellcompetition.org/touches/challenge/RW-CJC_teamD.mp3" type="audio/mpeg" /><a href="http://london12bellcompetition.org/touches/challenge/RW-CJC_teamD.mp3">touch</a></td></tr>
<tr><td>Team E</td><td>13'27"</td><td><audio year="challenge" band="E" controls preload="metadata"><source src="http://london12bellcompetition.org/touches/challenge/RW-CJC_teamE.mp3" type="audio/mpeg" /><a href="http://london12bellcompetition.org/touches/challenge/RW-CJC_teamE.mp3">touch</a></td></tr>
</table>

<p>The official results for the selected test-pieces will be published here in late January 2017.</p>

<p>For full details of how to enter the Challenge see page 1327 of the Christmas double-issue of <cite>The Ringing World</cite>, published on Friday 23rd December 2016.</p>


<? endif; ?>


<?php
include('include/pagebottom.php');
?>