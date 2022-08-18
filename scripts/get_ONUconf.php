<?php

if (!isset($_GET['thost'])){
    exit();
}
if (!isset($_GET['onu_name'])){
    exit();
}

require '../include/vars.php';
require '../include/database.php';
$scriptmode = true;
require '../include/auth_user.php';

$onu_name = $_GET['onu_name'];

$host = $_GET['thost'];
$port = 23;
$tlog = $_GET['tlog'];
$tpas = $_GET['tpas'];

if ($con = pfsockopen($host, $port, $errno, $errstr, 10)){
    $s1 = $tlog."\r\n";
    fwrite($con, $s1);
    $s1 = $tpas."\r\n";
    fwrite($con, $s1);
    sleep(1);
    $s1 = "su\r\n";
    fwrite($con, $s1);
    fwrite($con, "show running-config interface $onu_name \r\n");
    sleep(2);
    $out = fread($con, 16536);
    $out = explode("Current configuration:\r\n!\r\n", $out);
    $out = end($out);
    fclose($con);
    echo "<div style=\"text-align:left; padding:0 10px; margin:1% 15% 1% 1%; border: 1px solid black; border-radius: 3px;\">";
    echo "<pre>\n";
    $arr_out = explode("\r\n", $out);
    array_pop($arr_out);
    foreach ($arr_out as $value) {
        echo "$value\n";
    }
    echo "</pre>";
    echo "</div>\n";
}else{
    echo "OLT offline!";
}
$mysqli_wb->close();
?>
