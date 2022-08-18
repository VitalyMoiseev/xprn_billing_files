<?php

if (!isset($_GET['onu'])){
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
$switch_port = intval($_GET['onu']);
$query = "UPDATE user SET switch_port = $switch_port, switch_id = 0 WHERE id = $userid;";
if ($mysqli_bil->query($query)){
    #### log
    $hostlog = $_SERVER['REMOTE_ADDR'];
    $typelog = 35;
    $commentlog = "Change user $userid  port $switch_port via web-billing";
    $query = "INSERT INTO log (datetime, user, host, type, comment) VALUES (NOW(), '$username', '$hostlog', $typelog, '$commentlog');";
    $mysqli_bil->query($query);
    #### log
?>
<script type='text/javascript'>
alert('Сохранено');
</script>
<?php
}else{
    echo "Ошибка";
}
$mysqli_bil->close();
$mysqli_rad->close();
$mysqli_wb->close();