<table class="features-table">
    <thead>
    <tr>
<?php
if($spLevel == 0 OR $spLevel == 1) {
    $trdcs = 8;
}else{
    $trdcs = 7;
}
$col_w = floor(100/$trdcs);
if ($showmode != 1) {
    echo "<td class=\"grey\" width=\"$col_w%\"><a href=\"/".$labels['billing']."/".$labels['status']."\">".mb_ucfirst($labels['status'])."</a></td>\n";
} else {
    echo "<td class=\"blue\" width=\"$col_w%\"><a href=\"/".$labels['billing']."/".$labels['status']."\">".mb_ucfirst($labels['status'])."</a></td>\n";
}
if ($showmode != 2) {
    echo "<td class=\"grey\" width=\"$col_w%\"><a href=\"/".$labels['billing']."/".$labels['clients']."\">".mb_ucfirst($labels['clients'])."</a></td>\n";
} else {
    echo "<td class=\"blue\" width=\"$col_w%\"><a href=\"/".$labels['billing']."/".$labels['clients']."\">".mb_ucfirst($labels['clients'])."</a></td>\n";
}
if ($showmode != 3) {
    echo "<td class=\"grey\" width=\"$col_w%\"><a href=\"/".$labels['billing']."/".$labels['zajavki']."\">".mb_ucfirst($labels['zajavki'])."</a></td>\n";
} else {
    echo "<td class=\"blue\" width=\"$col_w%\"><a href=\"/".$labels['billing']."/".$labels['zajavki']."\">".mb_ucfirst($labels['zajavki'])."</a></td>\n";
}
$l1 = $detect->isMobile() ? $labels['z_add2'] : mb_ucfirst($labels['z_add']);
if ($showmode != 4) {
    echo "<td class=\"grey\" width=\"$col_w%\"><a href=\"/".$labels['billing']."/".$labels['z_add']."\">$l1</a></td>\n";
} else {
    echo "<td class=\"blue\" width=\"$col_w%\"><a href=\"/".$labels['billing']."/".$labels['z_add']."\">$l1</a></td>\n";
}
if ($showmode != 5) {
    echo "<td class=\"grey\" width=\"$col_w%\"><a href=\"/".$labels['billing']."/PPPoE\">PPPoE</a></td>\n";
} else {
    echo "<td class=\"blue\" width=\"$col_w%\"><a href=\"/".$labels['billing']."/PPPoE\">PPPoE</a></td>\n";
}
if ($showmode != 6) {
    echo "<td class=\"grey\" width=\"$col_w%\"><a href=\"/".$labels['billing']."/PON\">PON</a></td>\n";
} else {
    echo "<td class=\"blue\" width=\"$col_w%\"><a href=\"/".$labels['billing']."/PON\">PON</a></td>\n";
}
if ($showmode != 7) {
    echo "<td class=\"grey\" width=\"$col_w%\"><a href=\"/".$labels['billing']."/OTT\">OTT</a></td>\n";
} else {
    echo "<td class=\"blue\" width=\"$col_w%\"><a href=\"/".$labels['billing']."/OTT\">OTT</a></td>\n";
}
if($spLevel == 0 OR $spLevel == 1){
    if ($showmode != 9) {
        echo "<td class=\"grey\" width=\"$col_w%\"><a href=\"/".$labels['billing']."/".$labels['finance']."\">".mb_ucfirst($labels['finance'])."</a></td>\n";
    } else {
        echo "<td class=\"blue\" width=\"$col_w%\"><a href=\"/".$labels['billing']."/".$labels['finance']."\">".mb_ucfirst($labels['finance'])."</a></td>\n";
    }
}
?>
</thead>
<tfoot>
    <tr>
        <td style="vertical-align: center; text-align: right;" class="grey" colspan="<?php echo $trdcs; ?>">
            <?php echo $strdatenow; ?>&nbsp;&DoubleVerticalBar;&nbsp;<?php echo $labels['User']; ?>: <b><?php echo $username; ?></b>&nbsp;&DoubleVerticalBar;&nbsp;
<span class="l1">
<?php
switch ($lang) {
    case 'ru':
        echo "<a href=\"\" onclick=\"set_lang('uk'); return false;\">Укр.</a> - Рус.";
        break;
    case 'uk':
        echo "Укр. - <a href=\"\" onclick=\"set_lang('ru'); return false;\">Рус.</a>";
        break;
}
?>
</span>&nbsp;&DoubleVerticalBar;&nbsp;<b><a href="/<?php echo $labels['exit']; ?>"><?php echo $labels['exit']; ?></a></b>
        </td>
    </tr>
</tfoot>
</table>
