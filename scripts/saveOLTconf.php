<?php

if (!isset($_GET['host'])){
    exit();
}
if (!isset($_GET['comrw'])){
    exit();
}
require '../include/vars.php';
require '../include/database.php';
$scriptmode = true;
require '../include/auth_user.php';

$communityrw = $_GET['comrw'];
$host = $_GET['host'];

if($session = new SNMP(SNMP::VERSION_2C, $host, $communityrw, 2000000, 20)){
    $oid = ".1.3.6.1.4.1.3320.20.15.1.1.0";
    if($session->set($oid, 'i', 1)){
        echo "<b>Command OLT save config was send!</b>\n";
        #### log
        $hostlog = $_SERVER['REMOTE_ADDR'];
        $typelog = 35;
        $commentlog = "OLT $host save conf via web-billing";
        $query = "INSERT INTO log (datetime, user, host, type, comment) VALUES (NOW(), '$username', '$hostlog', $typelog, '$commentlog');";
        $mysqli_bil->query($query);
        #### log
    }else{
        echo "<b><font color=\"red\">SNMP Error!</font></b>";
    }

    $session->close();
}