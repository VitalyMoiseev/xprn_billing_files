<?php

if (!isset($_GET['host'])){
    exit();
}
if (!isset($_GET['comrw'])){
    exit();
}
if (!isset($_GET['onukey'])){
    exit();
}
if (!isset($_GET['pvid'])){
    exit();
}

require '../include/vars.php';
require '../include/database.php';
$scriptmode = true;
require '../include/auth_user.php';

$onukey = $_GET['onukey'];
$port = isset($_GET['port']) ? $_GET['port'] : 1;
$pvid = $_GET['pvid'];

$communityrw = $_GET['comrw'];
$host = $_GET['host'];

if($session = new SNMP(SNMP::VERSION_2C, $host, $communityrw, 2000000, 20)){
    $oid = ".1.3.6.1.4.1.3320.101.12.1.1.3.$onukey.$port";
    if($session->set($oid, 'i', $pvid)){
        echo "<b>PVID successfully changed!</b>\n";
        echo "Do not forget to save OLT configuration!";
        #### log
        $hostlog = $_SERVER['REMOTE_ADDR'];
        $typelog = 35;
        $commentlog = "Change PVID to $pvid ONU $onukey OLT $host via web-billing";
        $query = "INSERT INTO log (datetime, user, host, type, comment) VALUES (NOW(), '$username', '$hostlog', $typelog, '$commentlog');";
        $mysqli_bil->query($query);
        #### log
    }else{
        echo "<b><font color=\"red\">SNMP Error!</font></b>";
    }

    $session->close();
}
