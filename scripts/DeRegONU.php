<?php

if (!isset($_GET['thost'])){
    exit();
}
if (!isset($_GET['onu_name'])){
    exit();
}

if ($_GET['olt_type'] == 'GPON'){
    $command = "no gpon bind-onu";
}else{
    $command = "no epon bind-onu";
}

require '../include/vars.php';
require '../include/database.php';
$scriptmode = true;
require '../include/auth_user.php';

if($spLevel > 0){
    echo "You do not have permission for this operation.";
}else{

    $onu_name = $_GET['onu_name'];
    $ar1 = explode(':', $onu_name);
    $sfp_n = $ar1[0];
    $onu_n = $ar1[1];

    $host = $_GET['thost'];
    $port = isset($_GET['tport']) ? $_GET['tport'] : 23;
    $tlog = $_GET['tlog'];
    $tpas = $_GET['tpas'];

    if ($con = pfsockopen($host, $port, $errno, $errstr, 10)){
        $s1 = $tlog."\r\n";
        fwrite($con, $s1);
        $s1 = $tpas."\r\n";
        fwrite($con, $s1);
        sleep(1);
        $s1 = "enable\r\n";
        fwrite($con, $s1);
        fwrite($con, "config \r\n");
        $delim1 = "interface $sfp_n \r\n";
        fwrite($con, $delim1);
        fwrite($con, "$command sequence $onu_n \r\n");
        sleep(2);
        $out = fread($con, 16536);
        $out = explode($delim1, $out);
        $out = end($out);
        fclose($con);
        echo "<div style=\"text-align:left; padding:0 10px; margin:1% 15% 1% 1%; border: 1px solid black; border-radius: 3px;\">";
        echo "<pre>\n";
        $arr_out = explode("\r\n", $out);
        array_pop($arr_out);
        foreach ($arr_out as $value) {
            echo "$value\n";
        }
        echo "</pre>";
        echo "</div>\n";
        #### log
        $hostlog = $_SERVER['REMOTE_ADDR'];
        $typelog = 35;
        $commentlog = "DeReg ONU $onu_name OLT $host via web-billing";
        $query = "INSERT INTO log (datetime, user, host, type, comment) VALUES (NOW(), '$username', '$hostlog', $typelog, '$commentlog');";
        $mysqli_bil->query($query);
        #### log
    }else{
        echo "OLT offline!";
    }
    $mysqli_wb->close();
}
?>
