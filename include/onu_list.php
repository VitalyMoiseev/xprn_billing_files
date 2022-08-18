<style type="text/css">
    div.scroll-table {
        width: 100%;
        overflow: auto;
        height: 84%;
}
</style>
<?php

    $query = "SELECT
  `switches`.`name`,
  `switches`.`us_id`,
  `switches`.`host`,
  `switch_type`.`type` 
FROM
  `switches` INNER JOIN `switch_type` ON `switch_type`.`id` = `switches`.`type_id`
WHERE
  `switches`.`id` = $olt_id";
    
    $result = $mysqli_bil->query($query);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $result->close();
    
    $olt_name = $row['name'];
    $olt_host = $row['host'];
    $olt_us_id = $row['us_id'];
    if ($row['type'] == 3){
        $olt_gpon = true;
        $tms1 = "SN";
    }else{
        $olt_gpon = false;
        $tms1 = "mac";
    }
    $ask = "cat=device&action=get_data&object_type=olt&object_id=$olt_us_id";
    $resp = ask_userside($ask);
    $olt_data = $resp['data'][$olt_us_id];
    echo '<table class="features-table" width="100%"><thead><tr><td class="grey"><div align="left">';
    echo "<b>";
    echo $olt_name;
    echo " | ";
    echo "<a href=\"http://$olt_host\" target=\"_blank\">";
    echo $olt_host;
    echo "</a>";
    echo " | ";
    echo $olt_data['nazv'];
    echo " | ";
    echo $olt_data['location'];
    echo '</td><td class="grey"><a href="/'.$labels['billing'].'/PON">'.$labels['pon04'].'</a></td></tr></thead><tfoot><tr><td class="grey" colspan="2"><div align="left">';
    echo show_sfp($olt_id);
    echo '</div></td></tr></tfoot></table>';
    
    $query = "SELECT * FROM onu WHERE present = 1 AND olt = $olt_id ORDER BY $OrderOnu";
    
    $result = $mysqli_wb->query($query);
    echo '<div class="scroll-table" id="onu_table">';
    echo '<table class="features-table" width="100%"><thead>';
    #echo "<td class=\"grey\"><b>ONU</b></td>";
    echo "<td class=\"grey\"><b><a href=\"\" onclick=\"sortby('order_id'); return false;\">ONU<span id=\"sort_order_id\">";
    if ($OrderOnu == 'order_id ASC'){
        echo "&nbsp;&dArr;";
    }elseif ($OrderOnu == 'order_id DESC'){
        echo "&nbsp;&uArr;";
    }
    echo "</span></a></b></td>\n";
    echo "<td class=\"grey\"><b><a href=\"\" onclick=\"sortby('mac'); return false;\">$tms1<span id=\"sort_mac\">";
    if ($OrderOnu == 'mac ASC'){
        echo "&nbsp;&dArr;";
    }elseif ($OrderOnu == 'mac DESC'){
        echo "&nbsp;&uArr;";
    }
    echo "</span></a></b></td>\n";
    echo "<td class=\"grey\"><b><a href=\"\" onclick=\"sortby('comment'); return false;\">".$labels['Com']."<span id=\"sort_comment\">";
    if ($OrderOnu == 'comment ASC'){
        echo "&nbsp;&dArr;";
    }elseif ($OrderOnu == 'comment DESC'){
        echo "&nbsp;&uArr;";
    }
    echo "</span></a></b></td>\n";
    echo "<td class=\"grey\"><b><a href=\"\" onclick=\"sortby('userid'); return false;\">".$labels['IDKlient']."<span id=\"sort_userid\">";
    if ($OrderOnu == 'userid ASC'){
        echo "&nbsp;&dArr;";
    }elseif ($OrderOnu == 'userid DESC'){
        echo "&nbsp;&uArr;";
    }
    echo "</span></a></b></td>\n";
    echo "<td class=\"grey\"><b><a href=\"\" onclick=\"sortby('pwr'); return false;\">".$labels['pon05']."<span id=\"sort_pwr\">";
    if ($OrderOnu == 'pwr ASC'){
        echo "&nbsp;&dArr;";
    }elseif ($OrderOnu == 'pwr DESC'){
        echo "&nbsp;&uArr;";
    }
    echo "</span></a></b></td>\n";
    echo "<td class=\"grey\"><b><a href=\"\" onclick=\"sortby('last_act'); return false;\">".$labels['L_act']."<span id=\"sort_last_act\">";
    if ($OrderOnu == 'last_act ASC'){
        echo "&nbsp;&dArr;";
    }elseif ($OrderOnu == 'last_act DESC'){
        echo "&nbsp;&uArr;";
    }
    echo "</span></a></b></td>\n";
    echo "<td class=\"grey\"><b><small><a href=\"\" onclick=\"sortby('first_act'); return false;\">".$labels['F_act']."<span id=\"sort_first_act\">";
    if ($OrderOnu == 'first_act ASC'){
        echo "&nbsp;&dArr;";
    }elseif ($OrderOnu == 'first_act DESC'){
        echo "&nbsp;&uArr;";
    }
    echo "</span></a></small></b></td>\n";
    echo "</tr></thead><tbody>";
    $cc1 = false;
    while( $onu_t = $result->fetch_array(MYSQLI_ASSOC) ){
        $sfp_n = substr($onu_t['onu_name'],6,1);
        if ($sfp_s == '0' OR $sfp_n == $sfp_s){
            echo '<tr>';
            if ($onu_t['status'] == '0'){
                switch ($onu_t['dereg_reason']) {
                    case 'power-off':
                        $tdclass = "red2";
                        break;
                    default:
                        $tdclass = "red";
                        break;
                }
                $dereg_mes = '<span style="color: red; font-size: smaller;">'.$onu_t['dereg_reason'].'</span> | ';
            }else{
                if ($cc1){
                    $tdclass = "green2";
                    $cc1 = false;
                }else{
                    $tdclass = "green";
                    $cc1 = true;
                }
                $dereg_mes = '';
            }
            $spl1 = explode("/", $onu_t['onu_name']);
            $spl2 = explode(":", $spl1[1]);
            $onu_n = $spl2[1];
            echo '<td class="'.$tdclass.'" style="text-align: left;"><b><a href="/'.$labels['billing'].'/PON/'.$onu_t['olt'].'/'.$sfp_n.'/'.$onu_n.'/'.$onu_t['mac'].'">';
            echo $onu_t['onu_name'];
            echo '</a></b></td><td class="'.$tdclass.'" style="text-align: left; font-family: monospace; font-size: 110%;">';
            if ($detect->isMobile()){
                echo "<small>";
            }
            if ($olt_gpon){
                foreach ($GPON_Vendors as $search => $replace) {
                    $onu_t['mac'] = str_replace($search, $replace, $onu_t['mac']);
                }
            }
            echo $onu_t['mac'];
            echo " <a href=\"$usInvFindStr".urlencode($onu_t['mac'])."\" target=\"_blank\"><sup>скл.</sup></a>";
            if ($detect->isMobile()){
                echo "</small>";
            }
            echo '</td><td class="'.$tdclass.'">'.$onu_t['comment'].'</td>';
            echo '<td class="'.$tdclass.'">';
            if (!is_null($onu_t['userid'])){
                $query = "SELECT name FROM user WHERE Id=".$onu_t['userid'];
                $result1 = $mysqli_bil->query($query);
                if (mysqli_num_rows($result1) > 0) {
                    $row1 = mysqli_fetch_array($result1);
                    $username = $row1['name'];
                    echo "$username <a href=\"#\" onclick=\"openuser(".$onu_t['userid']."); return false;\">".$onu_t['userid']."</a>";
                    $query = "SELECT acctstoptime FROM radacct WHERE username = '$username' ORDER BY acctstarttime DESC LIMIT 1;";
                    $result1 -> close();
                    $result1 = $mysqli_rad->query($query);
                    if (mysqli_num_rows($result1) > 0) {
                        echo " | <a href=\"#\" onclick=\"openuserwork(".$onu_t['userid']."); return false;\">";
                        $row1 = mysqli_fetch_array($result1);
                        if (is_null($row1['acctstoptime'])){
                            echo '<b style="color: green;">online</b>';
                        }else{
                            echo '<b style="color: red;">offline</b> ';
                        }
                        echo "</a>";
                    }
                }
            }
            echo '</td>';
            $s_pwr = floatval($onu_t['pwr']);
            $s_pwr = number_format($s_pwr,1,'.',' ');
            echo '<td class="'.$tdclass.'" style="text-align: right; font-weight: bold">'.$dereg_mes.$s_pwr.'</td>';
            echo '<td class="'.$tdclass.'">'.$onu_t['last_act'].'</td>';
            echo '<td class="'.$tdclass.'"><small>'.$onu_t['first_act'].'</small></td></tr>';
        }
    unset ($onu_us);
    }
echo "</tbody><tfoot><tr><td class=\"grey\" colspan=\"7\">&nbsp;</td></tr></tfoot></table></div>";
?>
<script type='text/javascript'>
<?php
$sort_par = explode(' ', $OrderOnu);

echo "var sort_par1 = '".$sort_par[0]."';";
echo "var sort_par2 = '".$sort_par[1]."';";

?>    
function sortby(par1){
    if(par1 == sort_par1){
        if(sort_par2 == 'ASC'){
            sort_par2 = 'DESC';
        }else{
            sort_par2 = 'ASC';
        }
    }else{
        sort_par1 = par1;
        sort_par2 = 'ASC';
    }
    document.getElementById('sort_order_id').innerHTML = '';
    document.getElementById('sort_comment').innerHTML = '';
    document.getElementById('sort_userid').innerHTML = '';
    document.getElementById('sort_mac').innerHTML = '';
    document.getElementById('sort_pwr').innerHTML = '';
    document.getElementById('sort_last_act').innerHTML = '';
    if(sort_par2 == 'ASC'){
        document.getElementById('sort_' + par1).innerHTML = '&nbsp;&dArr;';
    }else{
        document.getElementById('sort_' + par1).innerHTML = '&nbsp;&uArr;';
    }
    order_par = "pm_ordreonu=" + sort_par1 + "%20" + sort_par2;
    document.cookie = order_par;
    document.location.reload(true);
    return false;
};
</script>

