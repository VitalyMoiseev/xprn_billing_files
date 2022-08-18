<?php
#### debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
###
require '../include/vars.php';
require '../include/database.php';
require '../include/us_functions.php';

if (isset($_GET['olt_check'])){
    $olt_check = " AND `switches`.`id` = ".$_GET['olt_check'];
}else{
    $olt_check = "";
}

if (isset($_GET['web'])){
    $web = true;
}else{
    $web = false;
}
//$log_file = $log_file.date("Y-m-d H:i:s").'.log';
$log_file = $log_file.'_onu.log';
$file_log = "Start ".date("Y-m-d H:i:s")."\n";
file_put_contents($log_file, $file_log, FILE_APPEND);


#read settings
$query = "SELECT * FROM settings";
if ($result = $mysqli_wb->query($query)){
    while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
        $param[$row['parametr']] = $row['value'];
    }
}else{
    $file_log = $mysqli_wb->error."\n";
    file_put_contents($log_file, $file_log, FILE_APPEND);
    exit();
}
$result->close();

#check wrong param
if (($param['check_onu_state_enable'] == 1) AND ($param['onu_check_begin'] == 1)){
    if ((time() - strtotime($param['onu_check_last_start'])) > 1800){
        $query = "UPDATE settings SET value = 0 WHERE parametr = 'onu_check_begin'";
        $mysqli_wb->query($query);
    }
}

if (($param['check_onu_state_enable'] == 1) AND ($param['onu_check_begin'] == 0) AND ((time() - strtotime($param['onu_check_last_start'])) > ($param['check_onu_state_interval']) * 60 - 1)){
    $query = "UPDATE settings SET value = 1 WHERE parametr = 'onu_check_begin'";
    $mysqli_wb->query($query);
    $query = "UPDATE settings SET value = NOW() WHERE parametr = 'onu_check_last_start'";
    $mysqli_wb->query($query);

    #get list OLT

    $query = "SELECT `switches`.`id`, `switches`.`host`, `switches`.`community`, `switches`.`type_id`, `switches`.`us_id`, `switch_type`.`type` 
FROM `switches` 
INNER JOIN `switch_type` ON `switch_type`.`id` = `switches`.`type_id` 
WHERE  `switch_type`.`type` > 1 AND `switches`.`check_onu` = 1 $olt_check 
ORDER BY
  `switches`.`name`";

    if (!$result = $mysqli_bil->query($query)){
        $file_log = $mysqli_bil->error."\n";
        echo $file_log;
        file_put_contents($log_file, $file_log, FILE_APPEND);
        exit();
    }
    $query = '';
    while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
        unset($sfp_count, $onu_macs, $onu_names, $onu_pwrs, $sfp_online_count);
        $onu_macs = array();
        $onu_names = array();
        $onu_pwrs = array();
        $er1 = false;
        $er2 = false;
        $er3 = false;
        $community = $row['community'];
        $host = $row['host'];
        if ($row['type'] == 3){
            $olt_gpon = true;
        }else{
            $olt_gpon = false;
        }
        $file_log = $row['id']." $host ".date("H:i:s");
        echo $file_log;
        file_put_contents($log_file, $file_log, FILE_APPEND);
        $olt_id = $row['id'];
        $olt_us_id = $row['us_id'];
        $session = new SNMP(SNMP::VERSION_2C, $host, $community, 2000000, 20);
        $session->quick_print = 1;
       
        for ($i = 1; $i <= $param['max_snmp_try']; $i++) {
            if ($onu_macs = $session->get("SNMPv2-MIB::sysName.0")){
                $snmperr = false;
                break;
            }else{
                $snmperr = true;
            }
        }
        if ($snmperr){
            $file_log = $session->getError()."\n";
            echo $file_log;
            file_put_contents($log_file, $file_log, FILE_APPEND);
            $session->close();
            $query .= "UPDATE olt_status SET status=0 WHERE bil_id = $olt_id;\n";
            continue;
        }
        $dtnow = date("Y-m-d H:i:s");
        $query .= "INSERT INTO olt_status (bil_id, us_id, last_act, status) VALUES ($olt_id, $olt_us_id, '$dtnow', 1) ON DUPLICATE KEY UPDATE status=1, last_act='$dtnow';\n";
        if ($olt_gpon){
            $macs_oid = ".1.3.6.1.4.1.3320.10.3.3.1.2";
            $macs_replace_str = 'STRING:';
            $pwrs_oid = ".1.3.6.1.4.1.3320.10.3.4.1.2";
            $telnet_inact = "show gpon inact \r\n";
            $sfp_name = 'GPON0/';
        }else{
            $macs_oid = ".1.3.6.1.4.1.3320.101.10.1.1.3";
            $macs_replace_str = 'Hex-STRING:';
            $pwrs_oid = ".1.3.6.1.4.1.3320.101.10.5.1.5";
            $telnet_inact = "show epon inact \r\n";
            $sfp_name = 'EPON0/';
        }
        for ($i = 1; $i <= $param['max_snmp_try']; $i++) {
            if ($onu_macs = $session->walk($macs_oid, true)){
                $snmperr = false;
                $onu_macs = str_replace($macs_replace_str, '', $onu_macs);
                break;
            }elseif($session->getErrno() == 8){
                    $snmperr = false;
                    $onu_macs = null;
                }else{
                    $snmperr = true;
            }
        }
        if ($snmperr){
            $file_log = $session->getError()."\n";
            echo $file_log;
            file_put_contents($log_file, $file_log, FILE_APPEND);
            $session->close();
            continue;
        }
        $file_log = " mac:".count($onu_macs)." ".date("H:i:s").", ";
        echo $file_log;
        file_put_contents($log_file, $file_log, FILE_APPEND);
        
        for ($i = 1; $i <= $param['max_snmp_try']; $i++) {
            if ($onu_names = $session->walk(".1.3.6.1.2.1.2.2.1.2", true)){
                $snmperr = false;
                break;
            }elseif($session->getErrno() == 8){
                    $snmperr = false;
                    $onu_names = null;
                }else{
                    $snmperr = true;
            }
        }
        if ($snmperr){
            $file_log = $session->getError()."\n";
            echo $file_log;
            file_put_contents($log_file, $file_log, FILE_APPEND);
            $session->close();
            continue;
        }
        $file_log = " names:".count($onu_names)." ".date("H:i:s").", ";
        echo $file_log;
        file_put_contents($log_file, $file_log, FILE_APPEND);
        for ($i = 1; $i <= $param['max_snmp_try']; $i++) {
            if ($onu_pwrs = $session->walk($pwrs_oid, true)){
                $snmperr = false;
                break;
            }elseif($session->getErrno() == 8){
                    $snmperr = false;
                    $onu_pwrs = null;
                }else{
                    $snmperr = true;
            }
        }
        if ($snmperr){
            $file_log = $session->getError()."\n";
            echo $file_log;
            file_put_contents($log_file, $file_log, FILE_APPEND);
            $session->close();
            continue;
        }
        $file_log = " powers:".count($onu_pwrs)." ".date("H:i:s")."\n";
        echo $file_log;
        file_put_contents($log_file, $file_log, FILE_APPEND);
            
        
        $session->close();
                 ####
        # Get dereg_reasons
        $onu_deregreasons = array();
        $ask = "cat=device&action=get_data&object_type=olt&object_id=$olt_us_id";
        $resp = ask_userside($ask);
        $olt_data = $resp['data'][$olt_us_id];
        #$ask = "cat=device&action=get_data&object_type=olt&object_id=$olt_us_id";
        //$resp = ask_userside($ask);
        //$olt_data = $resp['data'][$olt_us_id];
        #$query_uuu = "SELECT * FROM olt WHERE us_id=$olt_us_id";
        #$result_uuu = $mysqli_wb->query($query_uuu);
        #$olt_data = $result_uuu->fetch_array(MYSQLI_ASSOC);
        #$result_uuu->close();
        ##########
        $out = '';
        if ($con = pfsockopen($host, 23, $errno, $errstr, 10)){
            $s1 = $olt_data['telnet_login']."\r\n";
            fwrite($con, $s1);
            $s1 = $olt_data['telnet_pass']."\r\n";
            fwrite($con, $s1);
            sleep(1);
            $s1 = "su\r\nterminal length 0\r\nterminal width 0\r\n";
            fwrite($con, $s1);
            fwrite($con, $telnet_inact);
            sleep(2);
            $s1 = "exit\r\n";
            fwrite($con, $s1);
            fwrite($con, $s1);
            while (!feof($con)) {
                $out .= fread($con, 8192);
            }
            fclose($con);
            $out = explode("\r\n", $out);
            foreach ($out as $value) {
                if(strncmp($value, 'EPON0/', 6) == 0){
                    $arr_out = preg_split("/[\s,]+/",$value);
                    if (count($arr_out) > 7){
                        $value = str_replace('N/A', 'not applicable', $value);
                    }
                    $arr_out = preg_split("/[\s,]+/",$value);
                    if (count($arr_out) > 7){
                        $onu_deregreasons[$arr_out[0]] = $arr_out[7];
                    }else{
                        $onu_deregreasons[$arr_out[0]] = $arr_out[5];
                    }
                    
                }elseif(strncmp($value, 'GPON0/', 6) == 0){
                    $arr_out = preg_split("/[\s,]+/",$value);
                    if (count($arr_out) > 7){
                        $value = str_replace('N/A', 'not applicable', $value);
                    }
                    $arr_out = preg_split("/[\s,]+/",$value);
                    var_dump(count($arr_out));
                    if (count($arr_out) > 9){
                        $onu_deregreasons[$arr_out[0]] = $arr_out[7]." ".$arr_out[8];
                    }else{
                        $onu_deregreasons[$arr_out[0]] = $arr_out[7];
                    }
                }
            }
            unset($out, $arr_out);
        }
        ####     
        $query .= "UPDATE onu SET present = 0 WHERE olt = $olt_id;\n";
#        foreach ($onu_names as $key => $onu_name) {
        foreach ($onu_macs as $key => $onu_mac) {
            #$int_t = substr($onu_names[$key], 7, 1);
            $onu_names[$key] = trim($onu_names[$key],'"');
            $sfp_er = substr($onu_names[$key], 0, 6);
            $nam_ar = explode(':', $onu_names[$key]);
            if ((count($nam_ar) == 2) AND ($sfp_er == $sfp_name)){
                $onu_name = $onu_names[$key];
                #$sfp = substr($onu_name, 0, 7);
                $sfp = $nam_ar[0];
                if(!isset($sfp_count[$sfp])){
                    $sfp_count[$sfp] = 0;
                }
                ++$sfp_count[$sfp];
                #$onu_n = explode('/', $onu_name);
                #$onu_n = end($onu_n);
                #$onu_n = explode(':', $onu_n);
                $sfp_ar = explode('/', $sfp);
                #$onu_order_id = $onu_n[0] * 1000 + $onu_n[1];
                $onu_order_id = $sfp_ar[1] * 1000 + $nam_ar[1];
                #$onu_mac = $onu_macs[$key];
                $onu_mac = trim($onu_mac,'"');
                $onu_mac = trim($onu_mac);
                $onu_mac = str_replace (" ", ":", $onu_mac);
                if (array_key_exists($key, $onu_pwrs)){
                    if(!isset($sfp_online_count[$sfp])){
                        $sfp_online_count[$sfp] = 0;
                    }
                    ++$sfp_online_count[$sfp];
                    $pwr = $onu_pwrs[$key];
                    $pwr = $pwr / 10;
                    $dtnow = date("Y-m-d H:i:s");
                    $query .= "INSERT INTO onu (mac, olt, onu_name, present, status, pwr, last_act, order_id, first_act) VALUES ('$onu_mac', $olt_id, '$onu_name', 1, 1, '$pwr', '$dtnow', $onu_order_id, '$dtnow') ON DUPLICATE KEY UPDATE olt=$olt_id, onu_name='$onu_name', present=1, status=1, pwr='$pwr', last_act='$dtnow', order_id=$onu_order_id;\n";
                }else{
                    if (array_key_exists($onu_name, $onu_deregreasons)){
                        $dreason = $onu_deregreasons[$onu_name];
                    }else{
                        $dreason = '';
                    }
                    $query .= "INSERT INTO onu (mac, olt, onu_name, present, status, order_id, dereg_reason, first_act) VALUES ('$onu_mac', $olt_id, '$onu_name', 1, 0, $onu_order_id, '$dreason', '$dtnow') ON DUPLICATE KEY UPDATE olt=$olt_id, onu_name='$onu_name', present=1, status=0, order_id=$onu_order_id, dereg_reason='$dreason';\n";
                    $pwr = 0;
                    if(!isset($sfp_online_count[$sfp])){
                        $sfp_online_count[$sfp] = 0;
                    }
                }
                $pwrs_macs[$onu_mac] = floatval($pwr);
                
            }
        }
        unset($onu_deregreasons);
        $query .= "UPDATE olt_sfp SET count_onu = 0, online_count=0 WHERE olt=$olt_id;\n";
        foreach ($sfp_count as $sfp => $value){
            if(!isset($sfp_online_count[$sfp])){
                $sfp_online_count[$sfp] = 0;
            }
            $onlc = $sfp_online_count[$sfp];
            $query .= "INSERT INTO olt_sfp (olt, sfp, count_onu, online_count) VALUES ($olt_id, '$sfp', $value, $onlc) ON DUPLICATE KEY UPDATE count_onu=$value, online_count=$onlc;\n";
        }
    }
    
    $query2 = "UPDATE settings SET value = 0 WHERE parametr = 'onu_check_begin';";
    if (!$mysqli_wb->query($query2)){
        $file_log = $mysqli_wb->error."\n";
        file_put_contents($log_file, $file_log, FILE_APPEND);
    }
    
#echo $file_log;
    #file_put_contents($log_file, $file_log, FILE_APPEND);
    if ($mysqli_wb->multi_query($query)){
        $i = 0;
        do {
            $i++;
        } while ($mysqli_wb->next_result()); 
        $mysqli_wb->next_result();
        if ($mysqli_wb->errno) {
            echo "Batch execution prematurely ended on row $i.\n";
            $qar = explode("\n", $query);
            $file_log = $qar[$i]." - ".$mysqli_wb->error."\n";
            echo $file_log;
            file_put_contents($log_file, $file_log, FILE_APPEND);
        }
    }

    ####### ONU pwr history
    # read last powers
    $query = "SELECT * FROM onu_pwr_history WHERE stoptime IS NULL";
    if (!$result = $mysqli_wb->query($query)){
        $file_log = $mysqli_wb->error."\n";
        echo $file_log;
        file_put_contents($log_file, $file_log, FILE_APPEND);
        exit();
    }
    $query = "SELECT 1;\n";
    if($result->num_rows == 0 ){
        foreach ($pwrs_macs as $mac => $pwr) {
            $pwr = str_replace(",", ".", $pwr);
            $query .= "INSERT INTO onu_pwr_history (mac, pwr, starttime) VALUES ('$mac', $pwr, NOW());\n";
        }
    }else{
        while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
            $pwr_old[$row['mac']] = floatval($row['pwr']);
            $hist_ids[$row['mac']] = $row['Id'];
        }
        foreach ($pwrs_macs as $mac => $pwr) {
            if (array_key_exists($mac, $pwr_old)){
                $pwr = $pwr + 0;
                #if($pwr_old[$mac] != $pwr){
                $pwrdif = $pwr_old[$mac] - $pwr;
                $pwrdif = abs($pwrdif);
                if ($pwrdif > 0.2){
                    $h_id = $hist_ids[$mac];
                    $pwr = str_replace(",", ".", $pwr);
                    $query .= "UPDATE onu_pwr_history SET stoptime=NOW() WHERE Id=$h_id;\n";
                    $query .= "INSERT INTO onu_pwr_history (mac, pwr, starttime) VALUES ('$mac', $pwr, NOW());\n";
                }
            }else{
                $pwr = str_replace(",", ".", $pwr);
                $query .= "INSERT INTO onu_pwr_history (mac, pwr, starttime) VALUES ('$mac', $pwr, NOW());\n";
            }
        }
    }
    echo "<pre>$query</pre>";
    if ($mysqli_wb->multi_query($query)){
        $i = 0;
        do {
            $i++;
        } while ($mysqli_wb->next_result());   
        if ($mysqli_wb->errno) {
            echo "Batch execution prematurely ended on row $i.\n";
            $qar = explode("\n", $query);
            $file_log = $qar[$i]." - ".$mysqli_wb->error."\n";
            echo $file_log;
            file_put_contents($log_file, $file_log, FILE_APPEND);
        }
    }
}else{
    $ttt = time() - strtotime($param['onu_check_last_start']);
    $file_log = "$ttt sec from last start, exit\n";
    echo $file_log;
    file_put_contents($log_file, $file_log, FILE_APPEND);
}


if ($web){
    echo "<script type='text/javascript'>\n";
    echo "location.reload();\n";
    echo "</script>";
}


$mysqli_bil->close();
$mysqli_rad->close();
$mysqli_wb->close();