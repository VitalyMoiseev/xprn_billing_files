<?php
if (!isset($_GET['mac'])){
    exit();
}
$mac = $_GET['mac'];
require '../include/vars.php';
require '../include/database.php';
$scriptmode = true;
require '../include/auth_user.php';
require '../include/select_lang.php';

if ($mac == 'date'){
    $s_com = $mysqli_wb->real_escape_string($_GET['pat']);
    $s_com1 = $mysqli_wb->real_escape_string($_GET['pat2']);
    $s_com = "$s_com 00:00:00";
    $s_com1 = "$s_com1 23:59:59";
    $query = "SELECT * FROM onu_comment_history WHERE date > '$s_com' AND date < '$s_com1'";
}else{
    $query = "SELECT * FROM onu_comment_history WHERE mac = '$mac'";
}
if ($result = $mysqli_wb->query($query)){
    
?>
<table class="features-table">
    <thead>
        <tr>
            <td class="grey" colspan="5"><?php echo $labels['F_change_comment']; ?></td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="grey"><b>Дата</b></td><td class="grey"><b><?php echo $labels['User']; ?></b></td><td class="grey"><b><?php echo $labels['old_comment']; ?></b></td><td class="grey"><b><?php echo $labels['new_comment']; ?></b></td><td class="grey"><b>MAC / SN</b></td>
        </tr>

<?php
    while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
        foreach ($GPON_Vendors as $search => $replace) {
            $mac_show = str_replace($search, $replace, $row['mac']);
        }
        echo "<tr><td>".$row['date']."</td><td>".$row['user']."</td><td>".$row['old_comment']."</td><td>".$row['new_comment']."</td><td><a href='/redirect.php?r=1&mac=".$row['mac']."'>$mac_show</a></td></tr>";
    }
    echo '</tbody><tfoot><tr><td class="grey" colspan="5">&nbsp;</td></tr></tfoot></table>';
}