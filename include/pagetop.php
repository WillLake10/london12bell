<?php
$sTopPath = "";


// Explicitly declare some HTTP header things...
//header('Content-Type: text/html; charset=ISO-8859-1');
header('Content-Type: text/html; charset=UTF-8');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 1) . ' GMT'); // Expiry 1 second from now
header('Cache-Control: no-cache, must-revalidate');

/* <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">		*/

/* <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">				*/

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="EN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <!--<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />-->
    <meta name="keywords" content="London 12 Bell Striking Competition"/>
    <meta name="description" content="London 12 Bell Competition"/>
    <meta name="Author" content="London 12 Bell Competition Committee"/>
    <meta name="Copyright" content="Copyright London 12 Bell Competition Committee 2010"/>
    <script src="<?php print $sTopPath; ?>include/js/jquery-1.4.3.min.js" type="text/javascript"></script>
    <script src="<?php print $sTopPath; ?>include/js/jquery.corner.js" type="text/javascript"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script>
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

        ga('create', 'UA-89092555-1', 'auto');
        ga('send', 'pageview');
    </script>
    <?php
    print "<title>London 12 Bell Striking Competition" . (isset($sTitle) ? " > $sTitle" : "") . "</title>\n";
    print "<link rel=\"stylesheet\" href=\"{$sTopPath}style.css\" type=\"text/css\" />\n";
    ?>
</head>
<body>
<?php
include('header.php');
?>
<!--<div id="maindiv">-->
<div class="container">
    <div class="row">

    <div class="col-sm-3">
        <?php include('menu.php'); ?>
    </div>
    <div class="col-sm-9 pt-5">
        <!--<div id="contentdiv">-->
