<?php
$http_accept_lang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : "uk";
$http_accept_lang = is_null($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? "uk" : $_SERVER['HTTP_ACCEPT_LANGUAGE'];
if(isset($_COOKIE['bil_s_lang'])){
    $lang = $_COOKIE['bil_s_lang'];
}else{
    if(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'ru'){
        $lang = 'ru';
    }else{
        $lang = 'uk';
    }
}
switch ($lang) {
    case 'ru':
        setlocale(LC_ALL, "ru_RU.UTF-8");
        $query = "SELECT label, label_ru AS text FROM bil_texts;";
        $slocale = "ru_RU";
	break;
    default:
        // укр
        setlocale(LC_ALL, "uk_UA.UTF-8");
        $query = "SELECT label, label_uk AS text FROM bil_texts;";
        $slocale = "uk_UA";
}

$result = $mysqli_wb->query($query);
while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
    $labels[$row['label']] = $row['text'];
}
$result->close();
        
mb_internal_encoding("UTF-8");
function mb_ucfirst($text) {
    return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
}