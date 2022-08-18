<?php

if (!isset($_GET['id'])){
    exit();
}
if (!isset($_GET['userid'])){
    exit();
}

require '../include/vars.php';
require '../include/database.php';
$scriptmode = true;
require '../include/auth_user.php';
$userid = intval($_GET['userid']);
$query = "UPDATE onu SET userid = '$userid' WHERE Id = ".$_GET['id'].";";
if ($mysqli_wb->query($query)){
    echo "Сохранено";
    #### log
    $hostlog = $_SERVER['REMOTE_ADDR'];
    $typelog = 35;
    $commentlog = "Change ONU ".$_GET['id']." userid $userid via web-billing";
    $query = "INSERT INTO log (datetime, user, host, type, comment) VALUES (NOW(), '$username', '$hostlog', $typelog, '$commentlog');";
    $mysqli_bil->query($query);
    #### log
}else{
    echo "Ошибка";
}
$mysqli_bil->close();
$mysqli_rad->close();
$mysqli_wb->close();