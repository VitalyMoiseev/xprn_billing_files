<?php

if (!isset($_GET['host'])){
    exit();
}
if (!isset($_GET['onu_key'])){
    exit();
}
require '../include/vars.php';
require '../include/database.php';
$scriptmode = true;
require '../include/auth_user.php';

$onu_key = $_GET['onu_key'];

$communityrw = $_GET['comrw'];
$host = $_GET['host'];
if($session = new SNMP(SNMP::VERSION_2C, $host, $communityrw, 2000000, 20)){
    $oid = ".1.3.6.1.4.1.3320.101.10.1.1.29.$onu_key";
    if($session->set($oid, 'i', 0)){
        echo "<b>Command ONU reboot was send!</b>\n";
    }else{
        echo "<b><font color=\"red\">SNMP Error!</font></b>";
    }

    $session->close();
}