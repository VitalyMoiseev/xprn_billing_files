<?php
#### debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
###
require '../include/vars.php';
require '../include/database.php';
require '../include/select_lang.php';
$scriptmode = true;
$entermode = false;
require '../include/auth_user.php';

if(isset($_GET['s'])){
    $s_type = $_GET['s'];
    $s_com = $_GET['pat'];
    if (strlen($s_com) > 1){
        $query = "SELECT
  `switches`.`id`,
  `switches`.`name`,
  `switches`.`us_id`,
  `switches`.`community`,
  `switches`.`host`,
  `switch_type`.`type`
FROM
  `switches`
  INNER JOIN `switch_type` ON `switch_type`.`id` = `switches`.`type_id`
WHERE
  `switch_type`.`type` > 1
ORDER BY
  `switches`.`name`";

        $result = $mysqli_bil->query($query);
        while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
            $olt[$row['id']] = $row;
        }
        //$result->close;
        $s_com = $mysqli_wb->real_escape_string($s_com);
        $s_type = $mysqli_wb->real_escape_string($s_type);
        
        switch ($s_type) {
            case 'com':
                $query = "SELECT * FROM onu WHERE present=1 AND comment LIKE '%$s_com%' LIMIT 50";
                break;
            case 'userid':
                $query = "SELECT * FROM onu WHERE present=1 AND userid = $s_com LIMIT 50";
                break;
            case 'date':
                $s_com1 = $mysqli_wb->real_escape_string($_GET['pat2']);
                $s_com = "$s_com 00:00:00";
                $s_com1 = "$s_com1 23:59:59";
                $query = "SELECT * FROM onu WHERE present=1 AND first_act > '$s_com' AND first_act < '$s_com1' LIMIT 50";
                break;
            default :
                $query = "SELECT * FROM onu WHERE present=1 AND mac LIKE '%$s_com%' LIMIT 50";
                break;
        }
        $result = $mysqli_wb->query($query);
        echo '<table class="features-table" width="100%"><thead>';
        echo "<td class=\"grey\"><b>OLT</b></td><td class=\"grey\"><b>ONU</b></td><td class=\"grey\"><b>mac / SN</b></td><td class=\"grey\"><b>".$labels['Com']."</b></td><td class=\"grey\"><b>".$labels['IDKlient']."</b></td><td class=\"grey\"><b>".$labels['pon05']."</b></td><td class=\"grey\"><b>".$labels['L_act']."</b></td><td class=\"grey\"><b><small>".$labels['F_act']."</small></b></td></tr></thead><tbody>";
        $cc1 = false;
        while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
            $onu_t = $row;
            $sfp_n = substr($onu_t['onu_name'],6,1);
            echo '<tr>';
            if ($onu_t['status'] == '0'){
                $tdclass = "red";
            }else{
                if ($cc1){
                    $tdclass = "green2";
                    $cc1 = false;
                }else{
                    $tdclass = "green";
                    $cc1 = true;
                }
            }
            $spl1 = explode("/", $onu_t['onu_name']);
            $spl2 = explode(":", $spl1[1]);
            $onu_n = $spl2[1];
            #echo '<td class="'.$tdclass.'"><a href="/биллинг/PON/'.$onu_t['olt'].'/'.$sfp_n.'/'.$onu_n.'/'.$onu_t['mac'].'">ONU card</td>';
            echo '<td class="'.$tdclass.'"><b>'.$olt[$onu_t['olt']]['name'].'</b></td>';
            echo '<td class="'.$tdclass.'"><b><a href="/биллинг/PON/'.$onu_t['olt'].'/'.$sfp_n.'/'.$onu_n.'/'.$onu_t['mac'].'">'.$onu_t['onu_name'].'</a></b></td>';
            if ($olt[$onu_t['olt']]['type'] == 3){
                foreach ($GPON_Vendors as $search => $replace) {
                    $onu_t['mac'] = str_replace($search, $replace, $onu_t['mac']);
                }
            }
            echo '<td class="'.$tdclass.'">'.$onu_t['mac'];
            echo '<a href="'.$usInvFindStr.urlencode($onu_t['mac']).'" target="_blank"><sup>склад</sup></a></td><td class="'.$tdclass.'">'.$onu_t['comment'].'</td><td class="'.$tdclass.'">';
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
            echo '</td><td class="'.$tdclass.'">'.$onu_t['pwr'].'</td><td class="'.$tdclass.'">'.$onu_t['last_act'].'</td><td class="'.$tdclass.'"><small>'.$onu_t['first_act'].'</small></td></tr>';
        }
        echo '</tbody><tfoot><tr><td class="grey" colspan="8"><button id = "reset_res">Очистить результаты</button></td></tr></tfoot>';
        echo "</table>";
        ?>
<script type='text/javascript'>
$(document).ready(function(){
    $('#reset_res').click(function(){
        document.getElementById('search_status').innerHTML = '';
    })
});
</script>
<?php
    }else{
        echo "Минимум 2 символа для поиска!";
    }
    
}