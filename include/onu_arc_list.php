<style type="text/css">
    div.scroll-table {
        width: 100%;
        overflow: auto;
        height: 85%;
}
</style>
<?php

echo '<table class="features-table" width="100%"><thead><tr><td class="grey"><div align="center">';
echo "<b>";
echo $labels['archive_onu_t1'];
echo '</b></td><td class="grey"><a href="/'.$labels['billing'].'/PON">'.$labels['pon04'].'</a></td></tr></thead><tfoot><tr><td class="grey" colspan="2"><div align="left">';
echo '</div></td></tr></tfoot></table>';

$query = "SELECT
  `switches`.`id`,
  `switches`.`name`
FROM
  `switches`
  INNER JOIN `switch_type` ON `switch_type`.`id` = `switches`.`type_id`
WHERE
  `switch_type`.`type` = 2
ORDER BY
  `switches`.`name`";

$result = $mysqli_bil->query($query);
while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
    $olt_names[$row['id']] = $row['name'];
}

$query = "SELECT * FROM onu WHERE present = 0 ORDER BY $OrderOnu";
$result = $mysqli_wb->query($query);

echo '<div class="scroll-table" id="onu_table">';
echo '<table class="features-table" width="100%"><thead>';
echo "<td class=\"grey\"><b><a href=\"\" onclick=\"sortby('olt'); return false;\">OLT<span id=\"sort_olt\">";
if ($OrderOnu == 'olt ASC'){
    echo "&nbsp;&dArr;";
}elseif ($OrderOnu == 'olt DESC'){
    echo "&nbsp;&uArr;";
}
echo "<td class=\"grey\"><b><a href=\"\" onclick=\"sortby('order_id'); return false;\">ONU<span id=\"sort_order_id\">";
if ($OrderOnu == 'order_id ASC'){
    echo "&nbsp;&dArr;";
}elseif ($OrderOnu == 'order_id DESC'){
    echo "&nbsp;&uArr;";
}
echo "</span></a></b></td>\n";
echo "<td class=\"grey\"><b><a href=\"\" onclick=\"sortby('mac'); return false;\">mac<span id=\"sort_mac\">";
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
echo "</tr></thead><tbody>";
while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
    $onu_t = $row;
    echo '<tr>';
    $spl1 = explode("/", $onu_t['onu_name']);
    $spl2 = explode(":", $spl1[1]);
    $onu_n = $spl2[1];
    echo '<td>';
    echo $olt_names[$onu_t['olt']];
    echo '</td>';
    echo '<td>';
    echo $onu_t['onu_name'];
    echo '</td><td>';
    if ($detect->isMobile()){
        echo "<small>";
    }
    echo $onu_t['mac'];
    if ($detect->isMobile()){
        echo "</small>";
    }
    echo '</td><td>'.$onu_t['comment'].'</td><td>'.$onu_t['userid'].'</td><td><b>'.$onu_t['pwr'].'</b></td><td>'.$onu_t['last_act'].'</td></tr>';
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
    document.getElementById('sort_olt').innerHTML = '';
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
    order_par = "pm_ordreonu_arc=" + sort_par1 + "%20" + sort_par2;
    document.cookie = order_par;
    document.location.reload(true);
    return false;
};
</script>

