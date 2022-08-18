<?php
if (!isset($_GET['olt'])){
    exit();
}
if (!isset($_GET['mac'])){
    exit();
}
if (!isset($_GET['name'])){
    exit();
}
error_reporting(E_ALL & ~E_NOTICE);
require '../include/vars.php';
require '../include/database.php';
require '../include/functions.php';
require '../include/pon_functions.php';
require '../include/us_functions.php';
$scriptmode = true;
require '../include/auth_user.php';

$olt = $_GET['olt'];
$mac = $_GET['mac'];
$onu_name = $_GET['name'];

    $query = "SELECT
  `switches`.`name`,
  `switches`.`community`,
  `switches`.`us_id`,
  `switches`.`host` 
FROM
  `switches`
WHERE
  `switches`.`id` = $olt";
    
    $result = $mysqli_bil->query($query);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $result->close();
    
    $community = $row['community'];
    $host = $row['host'];
    $olt_name = $row['name'];
    $olt_us_id = $row['us_id'];
    
    $ask = "cat=device&action=get_ont_data&id=".str_replace(':', '', $mac);
    $resp = ask_userside($ask);
    $onu_us = $resp['data'];
    
    $query = "SELECT
  `onu`.`comment`,
  `onu`.`userid`
FROM
  `onu`
WHERE
  `onu`.`mac` = '$mac'";
    
    $result = $mysqli_bil->query($query);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $comment = $row['comment'];

$key = intval($onu_us['iface_number']);
$onu_s = GetOnuPwr($key, $host, $community);
if (!$onu_s == 'OFFLINE'){
$onu_eth_ena = GetOnuEthEna($key, $host, $community);
$onu_eth_state = GetOnuEthState($key, $host, $community);
$onu_s = $onu_s." Db";
}else{
    $onu_s = '<font color="red">'.$onu_s.'</font>';
}
echo '<table width="100%"><tr><td>';
echo "<b>$onu_name</b></td><td><b>$mac</b></td><td><b>$onu_s</b><br>";
echo '<small><a href="#" onclick="show_onu_card(\''.$mac.'\', '.$olt.', \''.$onu_name.'\')">[Обновить]</a></small>';
echo "</td>";
if ($onu_eth_ena == '2'){
    echo '<td width="5%" bgcolor="red">';
}else{
    if ($onu_eth_state == '2'){
        echo '<td width="5%" bgcolor="green">';
    } else {
        echo '<td width="5%" bgcolor="grey">';
    }
}
echo '&nbsp;</td>';
echo "<td><b>$comment</b> <input id=\"com_new\" type=\"text\" value=\"$comment\" hidden><br><small>";
echo "<div id=\"editcmd\"><a href=\"#\" onclick=\"editcomment();\">[Редактировать]</a></div></small></td><td>";
echo "<td width=\"30%\">&nbsp;</td><td>";
echo '<b><a href="#" onclick="show_signal_history(\''.$mac.'\','.$key.')">История сигналов</a></b></td>';
if ($onu_s !='OFFLINE'){
    echo '<td><b><a href="#" onclick="show_FDB(\''.$olt_us_id.'\',\''.$onu_name.'\', \''.$mac.'\', '.$key.')">FDB таблица</a></b></td>';
}
echo '</td></tr></table><div id="onu_data_field"><p>&nbsp;</div>';

?>
<script type='text/javascript'>
function editcomment(){
    $('#com_new').show();
    document.getElementById('editcmd').innerHTML = '<a href="#" onclick="save_com();">[Сохранить]</a> | <a href="#" onclick="reset_com();">[Отмена]</a>';
}
function save_com(){
    $('#com_new').hide();
    var new_comm = document.getElementById('com_new').value;
    new_comm = encodeURIComponent(new_comm);
    var url1 = "/scripts/change_onu_comment.php?mac=<?php echo $mac ?>&comment=" + new_comm;
    $('#editcmd').load(url1);
    //sleep(1000);
    show_onu_card('<?php echo $mac; ?>', <?php echo $olt; ?>, '<?php echo $onu_name; ?>');
}
function reset_com(){
    $('#com_new').hide();
    document.getElementById('editcmd').innerHTML = '<a href="#" onclick="editcomment();">[Редактировать]</a>';
}
</script>