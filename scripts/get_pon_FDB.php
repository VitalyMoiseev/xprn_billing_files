<?php
error_reporting(E_ALL);
if (!isset($_GET['us_id'])){
    exit();
}
if (!isset($_GET['onu'])){
    exit();
}
if (!isset($_GET['onu_mac'])){
    exit();
}
if (!isset($_GET['key'])){
    exit();
}
if (!isset($_GET['onuid'])){
    exit();
}
$olt_us_id = $_GET['us_id'];
$onu = $_GET['onu'];
$onu_mac = $_GET['onu_mac'];
$key = $_GET['key'];
$onuId = $_GET['onuid'];

require '../include/vars.php';
require '../include/us_functions.php';
require '../include/pon_functions.php';
require '../include/database.php';
$scriptmode = true;
require '../include/auth_user.php';
require '../include/select_lang.php';

$ask = "cat=device&action=get_data&object_type=olt&object_id=$olt_us_id";
$resp = ask_userside($ask);
$olt_data = $resp['data'][$olt_us_id];
#$query_uuu = "SELECT * FROM olt WHERE us_id=$olt_us_id";
#$result_uuu = $mysqli_wb->query($query_uuu);
#$resp['data'][$olt_us_id] = $result_uuu->fetch_array(MYSQLI_ASSOC);
#$result_uuu->close();

$host = $resp['data'][$olt_us_id]['host'];
$tlog = $resp['data'][$olt_us_id]['telnet_login'];
$tpas = $resp['data'][$olt_us_id]['telnet_pass'];
$community = $resp['data'][$olt_us_id]['com_public'];

#if($resp['data'][$olt_us_id]['nazv'] == 'BDCOM OLT P3608B'){
#    $FDB_method = 'telnet';
#}
$FDB_method = 'telnet';
switch ($FDB_method) {
    case 'SNMP':
        if($fdb = GetOnuFDBByKey($key, $host, $community, $onu_mac)){
            echo '<table class="features-table" width="100%"><thead>';
            echo "<td class=\"grey\">vlan</td><td class=\"grey\">MAC</td><td class=\"grey\">Юзер ИД</td></tr></thead><tbody>";
            foreach ($fdb as $vmac => $mac) {
                $vlan = explode('.', $vmac);
                $vlan = $vlan[0];
                echo "<tr><td class=\"green\">$vlan</td><td class=\"green\" style=\"font-family: monospace; font-weight: bold; font-size: 125%;\">$mac</td><td class=\"green\">";
                $query = "SELECT username as name, acctstoptime FROM radacct WHERE callingstationid = '$mac' ORDER BY acctstarttime DESC LIMIT 1;";
                $result = $mysqli_rad->query($query);
                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_array($result);
                    $result->close();
                    $query = "SELECT `user`.`id`, `user`.`fio`, `street`.`name` AS `street`, `user`.`num_hous`, `user`.`num_hous1`, `user`.`num_flat` 
                        FROM `user` LEFT JOIN `street` ON `user`.`kod_stree` = `street`.`id` 
                        WHERE `user`.`name` = '".$row['name']."'";
                    $result1 = $mysqli_bil->query($query);
                    if (mysqli_num_rows($result1) > 0) {
                        $row1 = $result1->fetch_array(MYSQLI_ASSOC);
                        $userid = $row1['id'];
                        $adr1 = $row1['street']." ".$row1['num_hous'];
                        if (!empty($row1['num_hous1'])){
                            $adr1 = $adr1."/".$row1['num_hous1'];
                        }
                        if (!empty($row1['num_flat'])){
                            $adr1 = $adr1." кв. ".$row1['num_flat'];
                        }
                    }
                    $result1->close();
                    echo "PPPoE: <a href=\"\" onclick=\"openuser($userid); return false;\">".$row['name']." ($userid)</a />";
                    echo " &nbsp;|&nbsp; $adr1 | ";
                    if (is_null($row['acctstoptime'])){
                        echo '<b style="color: green;">online</b>';
                    }else{
                        echo '<b style="color: red;">offline</b> '.$row['acctstoptime']."";
                    }
                    echo "</tr><tr><td></td><td></td><td>";
                    echo '<small><a href="javascript:void();" onclick="savecom_f(\''.$row['name'].'\');">[login &rarr; комментарий]</a>';
                    echo '&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void();" onclick="saveuid_f('.$userid.');">[ID &rarr; '.$labels['IDKlient'].']</a>';
                    echo '&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void();" onclick="saveonutouser('.$userid.');">[ONU &rarr; Юзер]</a>';
                    echo '&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void();" onclick="savecom_f(\''.$adr1.'\');">[Адрес &rarr; комментарий]</a></small></td></tr>';
                }
                mysqli_free_result($result);
                echo "</td></tr>";
            }
            echo '</tbody><tfoot><tr><td class="grey" colspan="3"><div id="editcmdf">&nbsp;</div></td></tr></tfoot></table>';
        }else{
            echo "olt offline";
        }
        break;

    default:
        #get mac from olt by telnet
        
        if ($con = pfsockopen($host, 23, $errno, $errstr, 10)){
            $s1 = $tlog."\r\n";
            fwrite($con, $s1);
            $s1 = $tpas."\r\n";
            fwrite($con, $s1);
            sleep(1);
            $s1 = "su\r\nterminal length 0\r\nterminal width 0\r\n";
            fwrite($con, $s1);
            fwrite($con, "show mac address-table interface $onu \r\n");
            sleep(2);
            $s1 = "exit\r\n";
            fwrite($con, $s1);
            fwrite($con, $s1);
            while (!feof($con)) {
                $out .= fread($con, 8192);
            }
            fclose($con);
            $out = explode(' -----', $out);
            $out = end($out);
            $arr_out = explode("\n", $out);
            echo '<table class="features-table" width="100%"><thead>';
            echo "<td class=\"grey\">vlan</td><td class=\"grey\">MAC</td><td class=\"grey\">Юзер ИД</td></tr></thead><tbody>";
            foreach ($arr_out as $out_mac){
                if(stristr($out_mac, 'exit')){
                    break;
                }
                $out_mac = str_word_count($out_mac, 1, '0123456789.');
                if (is_null($out_mac[1])){
                    continue;
                }
                $vlan = $out_mac[0];
                $mac = $out_mac[1];
                $mac = strtoupper($mac);
                $mac = str_split($mac);
                $mac = $mac[0].$mac[1].':'.$mac[2].$mac[3].':'.$mac[5].$mac[6].':'.$mac[7].$mac[8].':'.$mac[10].$mac[11].':'.$mac[12].$mac[13];
                if ($mac == $onu_mac){
                    continue;
                }
                echo "<tr><td class=\"green\">$vlan</td><td class=\"green\" style=\"font-family: monospace; font-size: 110%;\">$mac</td><td class=\"green\">";
                $query = "SELECT username as name, acctstoptime FROM radacct WHERE callingstationid = '$mac' ORDER BY acctstarttime DESC LIMIT 1;";
                $result = $mysqli_rad->query($query);
                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_array($result);
                    $query = "SELECT `user`.`id`, `user`.`fio`, `street`.`name` AS `street`, `user`.`num_hous`, `user`.`num_hous1`, `user`.`num_flat` 
                        FROM `user` LEFT JOIN `street` ON `user`.`kod_stree` = `street`.`id` 
                        WHERE `user`.`name` = '".$row['name']."'";
                    $result1 = $mysqli_bil->query($query);
                    if (mysqli_num_rows($result1) > 0) {
                        $row1 = $result1->fetch_array(MYSQLI_ASSOC);
                        $userid = $row1['id'];
                        $adr1 = $row1['street']." ".$row1['num_hous'];
                        if (!empty($row1['num_hous1'])){
                            $adr1 = $adr1."/".$row1['num_hous1'];
                        }
                        if (!empty($row1['num_flat'])){
                            $adr1 = $adr1." кв. ".$row1['num_flat'];
                        }
                    }
                    $result1->close();
                    echo "PPPoE: <a href=\"\" onclick=\"openuser($userid); return false;\">".$row['name']." ($userid)</a />";
                    echo " &nbsp;|&nbsp; $adr1 | <a href=\"\" onclick=\"openuserwork($userid); return false;\">";
                    if (is_null($row['acctstoptime'])){
                        echo '<b style="color: green;">online</b>';
                    }else{
                        echo '<b style="color: red;">OFFLINE</b> '.$row['acctstoptime']."";
                    }
                    echo "</a /></tr><tr><td></td><td></td><td>";
                    echo '<small><a href="javascript:void();" onclick="savecom_f(\''.$row['name'].'\');">[login &rarr; '.$labels['Com'].']</a>';
                    echo '&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void();" onclick="saveuid_f('.$userid.');">[ID &rarr; '.$labels['IDKlient'].']</a>';
                    echo '&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void();" onclick="saveonutouser('.$userid.');">[ONU &rarr; Юзер]</a>';
                    echo '&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void();" onclick="savecom_f(\''.$adr1.'\');">[Адрес &rarr; '.$labels['Com'].']</a></small></td></tr>';
                }
                mysqli_free_result($result);
                echo "</td></tr>";
            }
            echo '</tbody><tfoot><tr><td class="grey" colspan="3"><div id="editcmdf">&nbsp;</div></td></tr></tfoot></table>';
        }else{
            echo "olt offline (telnet)";
        }
        break;
}
$mactouser = str_replace(":", "", $onu_mac);
$mactouser = hexdec($mactouser);
?>
<script type='text/javascript'>
function savecom_f(new_comm){
    ask1 = "Записать новый комментарий " + new_comm + "?";
    if (window.confirm(ask1)){
        document.getElementById('comment_t').innerHTML = new_comm;
        new_comm = encodeURIComponent(new_comm);
        var url1 = "/scripts/change_onu_comment.php?id=<?php echo $onuId ?>&comment=" + new_comm;
        $('#editcmdf').load(url1);
    }
}
function saveuid_f(new_userid){
    ask1 = "Записать новый UserID " + new_userid + "?";
    if (window.confirm(ask1)){
        document.getElementById('userid_t').innerHTML = new_userid;
        new_userid = encodeURIComponent(new_userid);
        var url1 = "/scripts/change_onu_userid.php?id=<?php echo $onuId ?>&userid=" + new_userid;
        $('#editcmdf').load(url1);
    }
}
</script>