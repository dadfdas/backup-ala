<?php
function FormatBytes($size) {
    if ($size >= 1048576) {
        $mbsize = round($size/1048576,0);
        return $mbsize." MB";
    }
    elseif ($size >= 1024) {
        $kbsize = round($size/1024,0);
        return $kbsize." KB";
    }
    else {
        return $size." bytes";
    }
}


$filename = $_GET['filename'];

$info ='Bestand informatie<br />';
$info .='Naam : '.$filename.'<br />';
//$info .='Grote : '.FormatBytes(filesize($filename)).'<br />';
//$info .= 'Gewijzigd op: '.date("d/m/Y H:i",filemtime($filename));


echo $info;

?>
