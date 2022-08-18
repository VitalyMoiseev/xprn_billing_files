<?php

$query = "SELECT
  `switches`.`id`,
  `switches`.`name`,
  `switches`.`us_id`,
  `switches`.`community`,
  `switches`.`host`
FROM
  `switches`
  INNER JOIN `switch_type` ON `switch_type`.`id` = `switches`.`type_id`
WHERE
  `switch_type`.`type` > 1
ORDER BY
  `switches`.`name`";

$result = $mysqli_bil->query($query);

$query = "SELECT bil_id, status, last_act FROM olt_status";
$result1 = $mysqli_wb->query($query);
while( $row = $result1->fetch_array(MYSQLI_ASSOC) ){
    $olt_st[$row['bil_id']]['status'] = $row['status'];
    $olt_st[$row['bil_id']]['last_act'] = $row['last_act'];
}
echo '<table class="features-table" width="100%">';
echo '<thead><tr><td class="grey" colspan="6">OLT</td></tr><thead><tbody>';
echo '<tr><td class="grey">№</td><td class="grey">OLT</td>';
echo '<td class="grey">'.$labels['pon01'].'</td>';
echo '<td class="grey">'.$labels['Refresh'].'</td>';
echo '<td class="grey">'.$labels['Last_act'].'</td></tr>';
$num1 = 0;
while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
    $community = $row['community'];
    $host = $row['host'];
    $olt_id = $row['id'];
    if (isset($olt_st[$olt_id])){
        if ($olt_st[$olt_id]['status'] != 1){
            $tdclass = "red";
        }else{
            if ($num1 % 2){
                $tdclass = "green2";
            }else{
                $tdclass = "green";
            }
        }
    }else{
        $tdclass = "red";
    }
    echo '<tr><td width="3%" class="'.$tdclass.'">';
    echo ++$num1;
    echo "</td><td class=\"$tdclass\">";
    echo "<b>".$row['name']."</b> <small><a href=\"http://".$row['host']."\" target=\"_blank\">".$row['host']."</a></small></td><td class=\"$tdclass\">";
    echo show_sfp($olt_id);
    echo '</td><td class="'.$tdclass.'">';
    #echo '<a href="#" onclick="show_onu('.$row['id'].', 0)">Список ONU</a></td><td>';
    echo "<a href=\"\" onclick=\"check_onu($olt_id); return false;\"><img id=\"refresh\" src=\"/img/refresh1.png\" alt=\"".$labels['Refresh']."\" /></a></td><td class=\"$tdclass\">";
    #echo $resp["data"][$row['us_id']]["lastact"];
    if (isset($olt_st[$olt_id])){
        echo $olt_st[$olt_id]['last_act'];
    }
    echo "</td></tr>";
}
echo '</tbody><tfoot><tr><td class="grey" colspan="6"><div id="pon_status"><a href="" onclick="check_onu(0); return false;">'.$labels['pon02'].'</a></div></td></tr></tfoot>';
echo "</table>";
$result->close();
echo '<table class="features-table" width="100%"><thead><tr><td class="grey" colspan="5">'.$labels['Find'].' ONU</td></tr><thead><tbody>';
echo "\n";
echo '<tr><td width="5%" class="grey">&nbsp;</td>';
echo '<td width="40%" style="font-weight: bold; text-align: right;">'.$labels['Com'].':</td>';
echo '<td width="10%"><input id="s_com" size="30"></td>';
echo '<td width="40%" style="font-weight: bold; text-align: left;"><button id = "search_com">'.$labels['Find'].'</button></td>';
echo '<td width="5%" class="grey">&nbsp;</td></tr>';
echo "\n";
echo '<tr><td class="grey">&nbsp;</td><td style="font-weight: bold; text-align: right;">'.$labels['IDKlient'].':</td>';
echo '<td><input id="s_id" size="30"></td>';
echo '<td style="font-weight: bold; text-align: left;"><button id = "search_id">'.$labels['Find'].'</button></td><td class="grey">&nbsp;</td></tr>';
echo "\n";
echo '<tr><tr><td class="grey">&nbsp;</td><td style="font-weight: bold; text-align: right;">MAC / SN:</td>';
echo '<td><input id="s_mac" size="30"></td>';
echo '<td style="font-weight: bold; text-align: left;"><button id = "search_mac">'.$labels['Find'].'</button></td><td class="grey">&nbsp;</td></tr>';
$sdate1 = date("Y-m-d");
echo "\n";
echo '<tr><tr><td class="grey">&nbsp;</td><td style="font-weight: bold; text-align: right;">'.$labels['F_act'].':</td>';
echo '<td><input id="s_date1" type="date" value="'.$sdate1.'">&nbsp;-&nbsp;<input id="s_date2" type="date" value="'.$sdate1.'"></td>';
echo '<td style="font-weight: bold; text-align: left;"><button id = "search_date">'.$labels['Find'].'</button></td><td class="grey">&nbsp;</td></tr>';
echo "\n";
echo '<tr><tr><td class="grey">&nbsp;</td><td style="font-weight: bold; text-align: right;">'.$labels['F_change_comment'].':</td>';
echo '<td><input id="s_date_hc1" type="date" value="'.$sdate1.'">&nbsp;-&nbsp;<input id="s_date_hc2" type="date" value="'.$sdate1.'"></td>';
echo '<td style="font-weight: bold; text-align: left;"><button id = "search_date_hc">'.$labels['Find'].'</button></td><td class="grey">&nbsp;</td></tr>';
echo "\n";
echo '</tbody><tfoot><tr><td class="grey" colspan="5"></td></tr></tfoot>';
echo "\n";
echo "</table>";
echo '<div id="search_status"></div>';
?>
<table class="features-table" width="100%">
    <thead>
<tr>
    <td colspan="3" class="grey"><?php echo $labels['archive']; ?> ONU</td>
</tr>
    <thead>
    <tbody>
<tr>
    <td width="5%" class="grey">&nbsp;</td><td width="90%"><a href="/<?php echo $labels['billing'].'/PON/arc'; ?>"><?php echo $labels['archive_onu_t1']; ?></a></td><td width="5%" class="grey">&nbsp;</td>
</tr>
</tbody><tfoot><tr><td class="grey" colspan="3"></td></tr></tfoot>
</table>
<?php
if ($detect->isMobile()){
?>
<table class="features-table" width="100%">
    <thead>
<tr>
    <td colspan="3" class="grey"><?php echo $labels['Splitters']; ?></td>
</tr>
    <thead>
    <tbody>
<tr>
    <td class="green">1x2: 3.17dB</td>
    <td class="green">1x4: 7.4dB</td>
    <td class="green">1x8: 10.7dB</td>
</tr>
<tr>
    <td class="green">1x16: 13.9dB</td>
    <td class="green">1x32: 17.2dB</td>
    <td class="green">1x64: 21.5dB</td>
</tr>
<tr>
    <td colspan="3" class="grey"><b><?php echo $labels['Couplers']; ?></b></td>
</tr>
<tr>
<td class="green">5%: 13.7dB</td><td class="green">10%: 10.0dB</td><td class="green">15%: 8.16dB</td></tr><tr>
<td class="green">20%: 7.11dB</td><td class="green">25%: 6.29dB</td><td class="green">30%: 5.39dB</td></tr><tr>
<td class="green">35%: 4.56dB</td><td class="green">40%: 4.01dB</td><td class="green">45%: 3.73dB</td></tr><tr>
<td class="green">50%: 3.17dB</td><td class="green">55%: 2.71dB</td><td class="green">60%: 2.34dB</td></tr><tr>
<td class="green">65%: 1.93dB</td><td class="green">70%: 1.56dB</td><td class="green">75%: 1.42dB</td></tr><tr>
<td class="green">80%: 1.06dB</td><td class="green">85%: 0.76dB</td><td class="green">90%: 0.49dB</td></tr><tr>
<td class="green" colspan="3">95%: 0.32dB</td></tr>
</tbody><tfoot><tr><td class="grey" colspan="3"></td></tr></tfoot>
</table>
<?php
    
}else{
?>
<table class="features-table" width="100%">
    <thead>
<tr>
    <td colspan="6" class="grey"><?php echo $labels['Splitters']; ?></td>
</tr>
    <thead>
    <tbody>
<tr>
    <td class="green">1x2: 3.17dB</td>
    <td class="green">1x4: 7.4dB</td>
    <td class="green">1x8: 10.7dB</td>
    <td class="green">1x16: 13.9dB</td>
    <td class="green">1x32: 17.2dB</td>
    <td class="green">1x64: 21.5dB</td>
</tr>
    </tbody></table>
<table class="features-table" width="100%"><tbody>
<tr>
    <td colspan="19" class="grey"><b><?php echo $labels['Couplers']; ?></b></td>
</tr>
<tr>
<td class="green">5%</td>
<td class="green">10%</td>
<td class="green">15%</td>
<td class="green">20%</td>
<td class="green">25%</td>
<td class="green">30%</td>
<td class="green">35%</td>
<td class="green">40%</td>
<td class="green">45%</td>
<td class="green">50%</td>
<td class="green">55%</td>
<td class="green">60%</td>
<td class="green">65%</td>
<td class="green">70%</td>
<td class="green">75%</td>
<td class="green">80%</td>
<td class="green">85%</td>
<td class="green">90%</td>
<td class="green">95%</td>
</tr>
<tr>
<td class="green">13.7dB</td>
<td class="green">10.0dB</td>
<td class="green">8.16dB</td>
<td class="green">7.11dB</td>
<td class="green">6.29dB</td>
<td class="green">5.39dB</td>
<td class="green">4.56dB</td>
<td class="green">4.01dB</td>
<td class="green">3.73dB</td>
<td class="green">3.17dB</td>
<td class="green">2.71dB</td>
<td class="green">2.34dB</td>
<td class="green">1.93dB</td>
<td class="green">1.56dB</td>
<td class="green">1.42dB</td>
<td class="green">1.06dB</td>
<td class="green">0.76dB</td>
<td class="green">0.49dB</td>
<td class="green">0.32dB</td>
</tr>
</tbody><tfoot><tr><td class="grey" colspan="19"></td></tr></tfoot>
</table>

<?php
}
?>
<script type='text/javascript'>
$(document).ready(function(){
    $('#search_com').click(function(){
        document.getElementById('search_status').innerHTML = '<b>working...</b>';
        var s_com = document.getElementById('s_com').value;
        s_com = encodeURIComponent(s_com);
        var url1 = "/scripts/search_onu.php?s=com&pat=" + s_com;
        $('#search_status').load(url1);
    });
    $('#search_id').click(function(){
        document.getElementById('search_status').innerHTML = '<b>working...</b>';
        var s_com = document.getElementById('s_id').value;
        s_com = encodeURIComponent(s_com);
        var url1 = "/scripts/search_onu.php?s=userid&pat=" + s_com;
        $('#search_status').load(url1);
    });
    $('#search_mac').click(function(){
        document.getElementById('search_status').innerHTML = '<b>working...</b>';
        var s_com = document.getElementById('s_mac').value;
        s_com = encodeURIComponent(s_com);
        var url1 = "/scripts/search_onu.php?s=mac&pat=" + s_com;
        $('#search_status').load(url1);
    });
    $('#search_date').click(function(){
        document.getElementById('search_status').innerHTML = '<b>working...</b>';
        var s_com1 = document.getElementById('s_date1').value;
        var s_com2 = document.getElementById('s_date2').value;
        s_com1 = encodeURIComponent(s_com1);
        s_com2 = encodeURIComponent(s_com2);
        var url1 = "/scripts/search_onu.php?s=date&pat=" + s_com1 + "&pat2=" + s_com2;
        $('#search_status').load(url1);
    });
    $('#search_date_hc').click(function(){
        document.getElementById('search_status').innerHTML = '<b>working...</b>';
        var s_com1 = document.getElementById('s_date_hc1').value;
        var s_com2 = document.getElementById('s_date_hc2').value;
        s_com1 = encodeURIComponent(s_com1);
        s_com2 = encodeURIComponent(s_com2);
        var url1 = "/scripts/get_onu_comment_history.php?mac=date&pat=" + s_com1 + "&pat2=" + s_com2;
        $('#search_status').load(url1);
    });
    $('#s_com').bind("enterKey",function(e){
     document.getElementById("search_com").click();
    });
    $('#s_com').keyup(function(e){
     if(e.keyCode == 13)
     {
        $(this).trigger("enterKey");
     }
    });
    $('#s_id').bind("enterKey",function(e){
     document.getElementById("search_id").click();
    });
    $('#s_id').keyup(function(e){
     if(e.keyCode == 13)
     {
        $(this).trigger("enterKey");
     }
    });
    $('#s_mac').bind("enterKey",function(e){
     document.getElementById("search_mac").click();
    });
    $('#s_mac').keyup(function(e){
     if(e.keyCode == 13)
     {
        $(this).trigger("enterKey");
     }
    });
});
function check_onu(olt){
    if (olt === 0){
        if (window.confirm('<?php echo $labels['pon03']; ?>')){
            var url1 = "/scripts/check_onu.php?web=1";
        }else{
            return;
        }
    }else{
        var url1 = "/scripts/check_onu.php?web=1&olt_check=" + olt;
    }
    document.getElementById('pon_status').innerHTML = '<b>working...</b>';
    $('#pon_status').load(url1);
}
</script>