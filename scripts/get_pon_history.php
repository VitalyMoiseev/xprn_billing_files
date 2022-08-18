<?php
if (!isset($_GET['mac'])){
    exit();
}
$mac = $_GET['mac'];
$ofset = isset($_GET['ofset']) ? $_GET['ofset'] : 0;
require '../include/vars.php';
require '../include/database.php';
$scriptmode = true;
require '../include/auth_user.php';
require '../include/select_lang.php';

$query = "SELECT * FROM onu_pwr_history WHERE mac='$mac' ORDER BY starttime DESC LIMIT $ofset, 100";
if ($result = $mysqli_wb->query($query)){
    
?>
<table class="features-table">
    <thead>
        <tr>
            <td class="grey" colspan="4"><?php echo $labels['HistPwr']; ?></td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="grey"></td><td class="grey"><b><?php echo $labels['PwrHist1']; ?>&nbsp;-&nbsp;<?php echo $labels['PwrHist2']; ?></b></td><td class="grey"><b>Db</b></td><td class="grey"></td>
        </tr>

<?php
while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
    $tdclass = "red";
    if($row['pwr'] < 0){
        $tdclass = "green";
    }
    if (is_null($row['stoptime'])){
        $row['stoptime'] = $labels['PwrHist4'];
    }
    echo "<tr width=\"25%\"><td></td><td>".$row['starttime']."&nbsp;-&nbsp;".$row['stoptime']."</td><td class=\"$tdclass\">".$row['pwr']."</td><td width=\"25%\"></td></tr>";
}
?>
    </tbody>
    <tfoot>
        <tr>
            <td class="grey" colspan="4"><div id="scr<?php echo $ofset; ?>">
<?php
echo '|&nbsp;&nbsp;&nbsp;<b><a href="javascript:void();" onclick="show_pwr_history_'.$ofset.'(\''.$mac.'\')">'.$labels['PwrHist3'].'</a></b>&nbsp;&nbsp;&nbsp;|';
?>
</div></td>
        </tr>        
    </tfoot>
</table>
<div id="onu_data_field_<?php echo $ofset; ?>"></div>
<script type='text/javascript'>prototype
function show_pwr_history_<?php echo $ofset; ?>(mac){
        document.getElementById('onu_data_field_<?php echo $ofset; ?>').innerHTML = '<b>working...</b>';
        document.getElementById('scr<?php echo $ofset; ?>').innerHTML = '';
        var url1 = "/scripts/get_pon_history.php?mac=" + mac + "&ofset=<?php echo $ofset + 100; ?>";
        $('#onu_data_field_<?php echo $ofset; ?>').load(url1);
}
</script>
<?php
}else{
    echo "SQL error!";
}
?>