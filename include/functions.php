<?php

function snmp_uptime($param) {
    if(is_null($param)){
        return 'no link';
    }
    #$ar1 = explode(') ', $param);
    return $param;
}
function snmp_rep($param) {
    if(is_null($param)){
        return 'no link';
    }
    #$ar1 = explode(': ', $param);
    return $param;
}
function send_telegram_old($telegrama, $chatID, $parse_mode = 'HTML'){
    global $telegramtoken;
    error_reporting(~E_WARNING);
    $postdata = http_build_query(
    array(
        'parse_mode' => $parse_mode,
        'chat_id' => $chatID,
        'text' => $telegrama
        )
    );
    $opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
        )
        //,
        //'socket' => array(
        //    'bindto' => '0:0',
        //)
    );
    $context = stream_context_create($opts);
    $result = file_get_contents("https://api.telegram.org/bot$telegramtoken/sendMessage", false, $context);
    return TRUE;
}

#function send2Telegram($id, $msg, $token = '', $silent = false) {
function send_telegram($msg, $id, $parse_mode = 'HTML') {
    global $telegramtoken;
    $data = array(
        'chat_id' => $id,
        'text' => $msg,
        'parse_mode' => 'html',
        'disable_web_page_preview' => false,
        'disable_notification' => false
    );
    if($telegramtoken != '') {
      $ch = curl_init('https://api.telegram.org/bot'.$telegramtoken.'/sendMessage');
      curl_setopt_array($ch, array(
          CURLOPT_HEADER => 0,
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_POST => 1,
          CURLOPT_POSTFIELDS => $data
      ));
      curl_exec($ch);
      curl_close($ch);
    }
}

function send_sms($text, $number, $line = 2){
    global $smsuser;
    global $smspass;
    $postdata = http_build_query(
    array(
        'line'    => $line,
        'action'  => 'SMS',
        'telnum'  => $number,
        'smscontent' => $text,
        'send'    => 'Send'
        )
    );
    $opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
        )
    );
    $context = stream_context_create($opts);
    $result = file_get_contents("http://$smsuser:$smspass@192.168.111.20/default/en_US/sms_info.html?type=sms", false, $context);
    return TRUE;
}

function show_sfp($olt_id){
    global $mysqli_wb;
    global $labels;
    $query = "SELECT * FROM olt_sfp WHERE olt = $olt_id";
    $result = $mysqli_wb->query($query);
    $resp = 'SFP: ';
    $search = array('EPON0/', 'GPON0/');
    while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
        $sfp = str_replace($search, '', $row['sfp']);
        if ($row['count_onu'] == 0){
            continue;
        }
        $resp .= ' <a href="/'.$labels['billing'].'/PON/'.$olt_id.'/'.$sfp.'"><b>'.$sfp.':&nbsp;';
        if ($row['count_onu'] > 60){
            $resp .= '<font color="red">';
        }else{
            $resp .= '<font color="black">';
        }
        $offline_c = $row['count_onu'] - $row['online_count'];
        $resp .= $row['count_onu']."</b></font><small>(<font color=\"green\">".$row['online_count']."</font>/<font color=\"red\">$offline_c</font>)</small></a> | ";
    }
    $resp .= ' <a href="/'.$labels['billing'].'/PON/'.$olt_id.'"><b>'.$labels['All'].'</b></a>';
    $result->close();
    return $resp;
}

?>