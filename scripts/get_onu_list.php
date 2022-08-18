<?php
if (isset($_GET['id']) AND isset($_GET['sfp'])){
    $olt_id = $_GET['id'];
    $sfp_s = $_GET['sfp'];
    require '../include/vars.php';
    require '../include/database.php';
    require '../include/us_functions.php';
    require '../include/pon_functions.php';
    $scriptmode = true;
    require '../include/auth_user.php';

/*    $query = "SELECT
  `switches`.`host`,
  `switches`.`community`,
  `switches`.`snmpver`,
  `switches`.`us_id`,
  `switches`.`name`,
  `switches`.`type_id`
FROM
  `switches`
WHERE
  `switches`.`id` = $olt_id";
    $result = $mysqli_bil->query($query);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $result->close();
    $community = $row['community'];
    $host = $row['host'];
    if ($ar1 = snmp2_real_walk($host, $community,'.1.3.6.1.4.1.3320.101.10.1.1.26', 2000000)){
    #echo "<pre>";
    #echo print_r($ar1);
    #echo "</pre>";
    foreach ($ar1 as $key => $value) {
        $key = end(explode('10.1.1.26.', $key));
        $onu_mac = GetOnuMac($key, $host, $community);
        $onu_name = GetOnuName($key, $host, $community);
        $onu_s = GetOnuPwr($key, $host, $community);
        #echo "$key - $onu_mac";
        $query = "INSERT INTO onu (mac, olt, onu_name, pwr, last_act) VALUES ('$onu_mac', $olt_id, '$onu_name', '$onu_s', NOW()) ON DUPLICATE KEY UPDATE olt=$olt_id, onu_name='$onu_name', pwr='$onu_s', last_act=NOW()";
        echo $query;
        echo "<br>";
        #$mysqli_bil->query($query);
            
            #####
            if ($key > 20){
                break;
            }
        }
    }else{
        echo "id - ";
        var_dump($ar1);
    }
    
    #var_dump($ar1);
    #echo "======\n";
    #$ar1 = snmp2_real_walk($host, $community,'.1.3.6.1.4.1.3320.101.10.1.1.76', 2000000, 10);
    #var_dump($ar1);
    #echo "======\n";
 */

    $query = "SELECT
  `switches`.`name`,
  `switches`.`us_id`,
  `switches`.`host` 
FROM
  `switches`
WHERE
  `switches`.`id` = $olt_id";
    
    $result = $mysqli_bil->query($query);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $result->close();
    
    $olt_name = $row['name'];
    $olt_host = $row['host'];
    $olt_us_id = $row['us_id'];
    
    $ask = "cat=device&action=get_data&object_type=olt&object_id=$olt_us_id";
    $resp = ask_userside($ask);
    $olt_data = $resp['data'][$olt_us_id];
    
    echo '<table width="100%"><tr><td><div align="left">';
    echo "<b>";
    echo $olt_name;
    echo " | ";
    echo $olt_host;
    echo " | ";
    echo $olt_data['nazv'];
    echo " | ";
    echo $olt_data['location'];
    #echo " | ONU: ";
    
    #$query = "SELECT Count(`onu`.`pwr`) AS `Count_pwr` FROM `onu` WHERE `onu`.`olt` = $olt_id GROUP BY `onu`.`olt`";
    #$result = $mysqli_bil->query($query);
    #$row = $result->fetch_array(MYSQLI_ASSOC);
    #$result->close();
    
    #$all_onu = $row['Count_pwr'];
    
    #$query = "SELECT Count(`onu`.`Id`) AS `Count_Id`, `onu`.`pwr` FROM `onu` WHERE `onu`.`pwr` = 'OFFLINE' AND `onu`.`olt` = $olt_id GROUP BY  `onu`.`pwr`";
    
    #    $result = $mysqli_bil->query($query);
    #$row = $result->fetch_array(MYSQLI_ASSOC);
    #$result->close();
    
    #$offline_onu = $row['Count_Id'];
    
    #echo '<font color="green">';
    #echo $all_onu - $offline_onu;
    #echo '</font>/<font color="red">';
    #echo $offline_onu;
    #echo '</font></b></div>';
    echo '</td><td>ONU: ';
    $query = "SELECT sfp, count_onu FROM olt_sfp WHERE olt = $olt_id";
    $result1 = $mysqli_bil->query($query);
    while( $row1 = $result1->fetch_array(MYSQLI_ASSOC) ){
        if ($row1['count_onu'] == 0){
            continue;
        }
        $sfp = str_replace('EPON0/', '', $row1['sfp']);
        echo "| <a href=\"#\" onclick=\"show_onu('".$olt_id."', $sfp);\">SFP $sfp</a>:<b>";
        if ($row1['count_onu'] > 60){
            echo '<font color="red">';
        }else{
            echo '<font color="green">';
        }
        echo $row1['count_onu']. " </font></b>|";
    }
    $result1->close();
    echo '| <a href="#" onclick="show_onu(\''.$olt_id.'\', 0);">Все</a>';
    echo '</td></tr></table>';
    #$str1 = '{'.$olt_data['custom_iface_list'].'}';
    #$str1 = str_replace('a:10', '"int":10', $str0);
    #$iface_list = json_decode($str1);
    #echo $str1;
    #echo '<div align="left"><pre>';
    #var_dump($iface_list);
    #echo '</pre></div>';    
    
    $query = "SELECT
  `onu`.`mac`,
  `onu`.`onu_name`,
  `onu`.`pwr`,
  `onu`.`last_act`,
  `onu`.`comment`,
  `onu`.`status`,
  `onu`.`olt`,
  `onu`.`userid`
FROM
  `onu`
WHERE
  `onu`.`present` = 1 AND `onu`.`olt` = $olt_id";
    
    $result = $mysqli_bil->query($query);
    while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
        $onu[$row['onu_name']] = $row;
    }
    $onu_names = array_keys($onu);
    natsort($onu_names);
    echo '<div id="onu_table">';
    echo '<table class="features-table" width="100%"><thead>';
    echo "<td colspan=\"2\" class=\"grey\">&nbsp;</td><td class=\"grey\"><b>Интнрфейс/MAC</b></td><td class=\"grey\"><b>Описание</b></td><td class=\"grey\"><b>Юзер ИД</b></td><td class=\"grey\"><b>Сигнал</b></td><td class=\"grey\"><b>Посл. активность</b></td></tr></thead><tbody>";
    echo '<pre><div align="left">';
    foreach ($onu_names as $value) {
        $onu_t = $onu[$value];
        $sfp_n = substr($onu_t['onu_name'],6,1);
        if ($sfp_s == '0' OR $sfp_n == $sfp_s){
            $mac_ask = str_replace(':', '', $onu_t['mac']);
            $ask = "cat=device&action=get_ont_data&id=$mac_ask";
            $resp = ask_userside($ask);
            $onu_us = $resp['data'];
            #var_dump($onu_us);
            echo '<tr>';
            if ($onu_us['db'] == '0'){
                echo '<td width="3%" bgcolor="red">';
                $ask = "cat=device&subcat=get_pon_level_history&onu_name=$mac_ask&is_desc=1&limit=1";
                $resp = ask_userside($ask);
                $last_act = $resp['data'][$onu_us['device_id']][$onu_us['iface']][0]['date_to'];
                $pwr = $resp['data'][$onu_us['device_id']][$onu_us['iface']][0]['level'];
            }else{
                echo '<td width="3%" bgcolor="green">';
                $last_act = $olt_data['lastact'];
                $pwr = $onu_us['db'];
            }
            echo '&nbsp;</td><td><a href="#" onclick="show_onu_card(\''.$onu_t['mac'].'\', '.$onu_t['olt'].', \''.$onu_t['onu_name'].'\')">ONU card</td><td><b>'.$onu_t['onu_name'].'</b><br>'.$onu_t['mac'].'</td><td>'.$onu_t['comment'].'</td><td>'.$onu_t['userid'].'</td><td>'.$pwr.'</td><td>'.$last_act.'</td></tr>';
        }
    }
    echo "</tbody><tfoot><tr><td class=\"grey\" colspan=\"7\">&nbsp;</td></tr></tfoot></table></div>";

}
