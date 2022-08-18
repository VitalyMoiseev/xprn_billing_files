<?php
error_reporting(E_ALL);
if (!isset($_GET['onuid'])){
    exit();
}
if (!isset($_GET['onukey'])){
    exit();
}
$port = isset($_GET['port']) ? $_GET['port'] : 1;
$curpvid = isset($_GET['curpvid']) ? $_GET['curpvid'] : 1;

require '../include/vars.php';
require '../include/database.php';
require '../include/select_lang.php';
require '../include/pon_functions.php';
$scriptmode = true;
require '../include/auth_user.php';

$onuId = $_GET['onuid'];
$onukey = $_GET['onukey'];
$onu_name = $_GET['onu_name'];
$thost = $_GET['thost'];
$tport = 23;
$tlog = $_GET['tlog'];
$tpas = $_GET['tpas'];
$communityrw = $_GET['comrw'];
$snmp_host = $_GET['host'];

echo '<table class="features-table" width="100%"><thead>';
echo "<td class=\"grey\">Change PVID on port $port</td></tr></thead><tbody>";
echo "<tr><td>";
if($vlan_allowed = GetAllowedVlans($thost, $tport, $tlog, $tpas, $onu_name)){
    echo "PVID: <select id=\"pvid\">\n";
    foreach ($vlan_allowed as $key => $value) {
        echo "<option ";
        if($value == $curpvid){
            echo "selected ";
        }
        echo "value=$value>$value</option>\n";
    }
    echo "</select>\n";
}
echo '&nbsp;<button onclick="savePVID();">'.$labels['Save'].'</button>';
echo "</td></tr>";
echo '</tbody><tfoot><tr><td class="grey"><div id="editcmdf">&nbsp;</div></td></tr></tfoot></table>';
$mysqli_wb->close();

?>
<script type='text/javascript'>
function savePVID(){
    var curpvid = <?php echo $curpvid ?>;
    var pvid = document.getElementById('pvid').value;
    if(pvid == curpvid){
        var msg = "PVID " + pvid + "is alredy set :)";
        alert(msg);
        return false;
    }
    var msg = "Set " + pvid + " as PVID? Are you shure?";
    if(confirm(msg)){
        document.getElementById('editcmdf').innerHTML = '<b>working...</b>';
        var comrw = encodeURIComponent('<?php echo $communityrw; ?>');
        var host = encodeURIComponent('<?php echo $snmp_host; ?>');
        var url1 = "/scripts/change_PVID.php?onukey=<?php echo $onukey; ?>&port=<?php echo $port; ?>&pvid=" + pvid + "&host=" + host + "&comrw=" + comrw;
        $('#editcmdf').load(url1);
    }
}
</script>