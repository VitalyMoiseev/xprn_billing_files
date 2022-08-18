<?php

if (!isset($_GET['olt_id'])){
    exit();
}
if (!isset($_GET['onu_key'])){
    exit();
}
if (!isset($_GET['onu_name'])){
    exit();
}
$thost = $_GET['thost'];
$tlog = $_GET['tlog'];
$tpas = $_GET['tpas'];
$communityrw = $_GET['comrw'];
$snmp_host = $_GET['host'];
$olt_type = $_GET['type'];
require '../include/vars.php';
require '../include/database.php';
require '../include/select_lang.php';
require '../include/pon_functions.php';
$scriptmode = true;
require '../include/auth_user.php';

$olt_id = $_GET['olt_id'];
$onu_key = $_GET['onu_key'];
$onu_name = $_GET['onu_name'];

echo '<table class="features-table" width="100%"><thead>';
echo '<td class="grey" colspan="9">'.$labels['Manage'].' ONU '.$onu_name.'</td><td class="grey" colspan="3">'.$labels['Manage'].' OLT</td></tr></thead><tbody>';
echo '<tr><td class="grey"></td>';
echo "<td class=\"green\"><button onclick=\"rebootONU($olt_id, $onu_key);\">Reboot ONU</button></td>";
echo '<td class="grey"></td>';
echo '<td class="grey"></td>';
echo "<td class=\"green\"><button onclick=\"showONUconf($olt_id, '$onu_name');\">Show ONU config</button></td>";
echo '<td class="grey"></td>';
echo '<td class="grey"></td>';
echo "<td class=\"green\"><button onclick=\"DeRegONU($olt_id, '$onu_name', '$olt_type');\">DeRegistration ONU</button></td>";
echo '<td class="grey"></td>';
echo '<td class="grey"></td>';
echo "<td class=\"green\"><button onclick=\"saveOLTconf($olt_id);\">Save OLT config</button></td>";
echo '<td class="grey"></td></tr>';
echo '</tbody><tfoot><tr><td class="grey" colspan="12"><div id="cmdrep">&nbsp;</div></td></tr></tfoot></table>';

?>
<script type='text/javascript'>
function rebootONU(olt_id, onu_key){
    var msg = "Reboot ONU? Are you shure?";
    if(confirm(msg)){
        document.getElementById('cmdrep').innerHTML = '<b>working...</b>';
        var comrw = encodeURIComponent('<?php echo $communityrw; ?>');
        var host = encodeURIComponent('<?php echo $snmp_host; ?>');
        var url1 = "/scripts/rebootONU.php?olt_id=" + olt_id + "&onu_key=" + onu_key + "&host=" + host + "&comrw=" + comrw;
        $('#cmdrep').load(url1);
    }
}
function saveOLTconf(olt_id){
    var msg = "Save OLT config? Are you shure?";
    if(confirm(msg)){
        document.getElementById('cmdrep').innerHTML = '<b>working...</b>';
        var comrw = encodeURIComponent('<?php echo $communityrw; ?>');
        var host = encodeURIComponent('<?php echo $snmp_host; ?>');
        var url1 = "/scripts/saveOLTconf.php?host=" + host + "&comrw=" + comrw;
        $('#cmdrep').load(url1);
    }
}
function showONUconf(olt_id, onu_name){
    document.getElementById('cmdrep').innerHTML = '<b>working...</b>';
    var thost = encodeURIComponent('<?php echo $thost; ?>');
    var tlog = encodeURIComponent('<?php echo $tlog; ?>');
    var tpas = encodeURIComponent('<?php echo $tpas; ?>');
    var url1 = "/scripts/get_ONUconf.php?olt_id=" + olt_id + "&onu_name=" + encodeURIComponent(onu_name) + "&tlog=" + tlog + "&tpas=" + tpas + "&thost=" + thost;
    $('#cmdrep').load(url1);
}
function DeRegONU(olt_id, onu_name, olt_type){
    var msg = "Delete? Are you sure? The operation cannot be undone!";
    if(confirm(msg)){
        document.getElementById('cmdrep').innerHTML = '<b>working...</b>';
        var thost = encodeURIComponent('<?php echo $thost; ?>');
        var tlog = encodeURIComponent('<?php echo $tlog; ?>');
        var tpas = encodeURIComponent('<?php echo $tpas; ?>');
        var url1 = "/scripts/DeRegONU.php?olt_id=" + olt_id + "&onu_name=" + encodeURIComponent(onu_name) + "&tlog=" + tlog + "&tpas=" + tpas + "&thost=" + thost + "&olt_type=" + encodeURIComponent(olt_type);
        alert(url1);
    $('#cmdrep').load(url1);
    }
}
</script>