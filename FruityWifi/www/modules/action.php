<?
include_once dirname(__FILE__)."/../config/config.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

$page = @$_GET['page'];
$wait = @$_GET['wait'];

if (!is_numeric($wait)) {
	$wait = 1;
}
?>
<html>
<head>
    <meta http-equiv="refresh" content="1; url=./wait.php?page=<?=$page?>&wait=<?=$wait?>">
</head>
<body bgcolor="black" text="white">
<style>
body {
    background-color:#FCFCFC; /*#EFEFEF*/
    color: #000;
    font-family: monospace, courier;
    font-size: 12px;
    margin: 0px 0px;
}

.module {
    background-color:#F8F8F8; /* #090909 */
    -moz-border-radius: 4px;
    border-radius: 4px;
    border:1px solid;
    border-color:#BAC1C4; /* #E01B46 */
    m-argin-left: 10px;
    margin: 10px;
    padding: 10px;
}

</style>

<div align="" class="module">
<table width="300px">
<tr>
<td>

<pre>
<?php
echo "Please wait...";
?>
</pre>

</td>
</tr>
</table>
</div>
</div>
</body>
</html>