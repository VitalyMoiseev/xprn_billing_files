<?php

if (!isset($_GET['id'])){
    exit();
}
if (!isset($_GET['comment'])){
    exit();
}

require '../include/vars.php';
require '../include/database.php';
$scriptmode = true;
require '../include/auth_user.php';

# get old comment
$query = "SELECT comment, mac FROM onu WHERE Id = ".$_GET['id'].";";
$result = $mysqli_wb->query($query);
if($result->num_rows == 0 ){
    $old_comment = null;
}else{
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $old_comment = $row['comment'];
    $mac = $row['mac'];
}

$query = "UPDATE onu SET comment = '".$_GET['comment']."' WHERE Id = ".$_GET['id'].";";
if ($mysqli_wb->query($query)){
    #### log
    $hostlog = $_SERVER['REMOTE_ADDR'];
    $typelog = 35;
    $commentlog = "Change ONU ".$_GET['id']." comment ".$_GET['comment']." via web-billing";
    $query = "INSERT INTO log (datetime, user, host, type, comment) VALUES (NOW(), '$username', '$hostlog', $typelog, '$commentlog');";
    $mysqli_bil->query($query);
    #### log
    if (!is_null($old_comment)){
        $query = "INSERT INTO onu_comment_history (mac, old_comment, new_comment, user, date) VALUE ('$mac', '$old_comment', '".$_GET['comment']."', '$username', NOW());";
        $mysqli_wb->query($query);
    }
    echo "Сохранено";
}else{
    echo "Ошибка";
}
$mysqli_bil->close();
$mysqli_rad->close();
$mysqli_wb->close();