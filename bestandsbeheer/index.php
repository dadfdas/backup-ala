<?php
SESSION_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
 <title>Dresa Filemanager</title>
 <script src="popup.js" type="text/javascript"></script>
 <script src="prototype.js"></script>
 <script>
  function open_win(adres)
   {window.open(adres,"_blank","toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width=800, height=580")}
	function getHTML(p_url, p_pars,cont){
		var url = ''+p_url+'';
		var pars = ''+p_pars+'';
		var myAjax = new Ajax.Updater(''+cont+'',url,{method: 'get',parameters: pars});
		window.focus();
	}

 </script>
 <link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>

<?php
################Config###################
include("config.php");
#########################################

//**
// Root directory uit de config.php wordt nu door gegeven naar de $_SESSION global.
$_SESSION['mainfiledir']                            = $FilePad;
$_SESSION['filedir']                                = $FilePad;


//**
// Controle of er al een subdirectory gedefineerd is. 
// Zo ja dan wordt deze toegevoegd aan $_SESSION['filedir'].
If (isset($_GET['filedir']) AND !empty($_GET['filedir'])){  // Controle of GET['filedir'] bestaat 
    $_SESSION['getdir'] = $_GET['filedir'];
    $_SESSION['filedir']= $_SESSION['mainfiledir'].$_GET['filedir'];
    if (isset($_GET['uploaddir'])){
        $_SESSION['getdir'] = $_GET['uploaddir'];
    }
//**
// Als er geen sub directory gedefineerd is zal de variable $_SESSION['getdir'] gevuld worden met een lege sting.
// deze moet gedefineerd zijn anders een error.
}else{
    $_SESSION['getdir'] = '';
}

//** 
// Functie voor het gemak. Bij elke popup bericht wordt er wel een icon gebruikt.
// voor de leesbaarheid en de beheerbaarheid hier in een functie.. 
function alertIcon($icon){
	return '<img src="'.$_SESSION['fileicons'].$icon.'" width="16" height="16" 
            border="0" alt="alert"" style="margin-bottom:-2px;">';
}

//**
// Maakt lijst met van de opgegeven directory
function GetFileType($file){
// Is het een directory?
    if(is_dir($_SESSION['filedir'].'/'.$file)) {
       $FileInfo[]= "folder.png";
       $FileInfo[]= "Directory";
    } else {
// of is het een bestand?
// onderstaande statement geeft de eerste waarden na de punt van een bestandsnaam. De Extentsie
	$FileExtensie = strstr($file, ".") ? strtolower(substr($file, 1+strrpos($file, "."))) : '';

// Controleerd of extentie bekend is anders default.
	$FileInfo = isset($GLOBALS['FileTypes'][$FileExtensie]) ? $GLOBALS['FileTypes'][$FileExtensie] : $GLOBALS['FileTypes']['default'];

	}
	return $FileInfo;
}

//**
// Wijzigd de notatie van de bestands grote. 
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

//**
// Weer functie voor leesbaarheid. 
// Zoals de naam al zegt :o)
function setDirLink($file){
    $Html ='<a href="'.$_SERVER['PHP_SELF'].'?filedir='.$_SESSION['getdir'].''.$file.'/">'.$file.'</a>';
    return $Html;
}

function randomkeys(){
	$length = 5;
	$number ='';
		for($i=0;$i<$length;$i++){
			$number .= rand(0,9);
		}
	return $number;
}

//**
// Bepaling waar de hyperlink naar moet verwijzen. 
// Hier wordt bepaald welke bestanden door welke viewer worden geopend.
function setFileLink($file){
// Codeviewer extenties
	$CodeFile = array('php','css','html','htm','sql','js');
    $FileExtensie = strstr($file, ".") ? strtolower(substr($file, 1+strrpos($file, "."))) : '';
// als de extenstie voorkomt in $Codelist  zal er een link naar de codeviewer gedefineerd worden 
	
    if (in_array($FileExtensie , $CodeFile)){
		$ContainerId = randomkeys();
        $Html ='<a class="fileinfo" onMouseOver="getHTML(\'fileinfo.php\',\'filename='.$file.'\',\''.$ContainerId.'\');"  href="javascript:onclick=open_win(\'viewfile.php?file='.$file.'\')" >'.$file.'<b id="'.$ContainerId.'"></b></a>';
		
    }else{
// Default geen link.
        $Html =$file;
    }
    return $Html;
}

//**
// Functie voor leesbaarheid.Zoals naam als vertelt ....
function getIcon($icon){
    $Html ='<img src="'.$_SESSION['fileicons'].$icon.'" border="0"/>';
    return $Html;
}
//**
// algemene navigatie. 
function Navi(){
// Het opgegeven pad wordt opgesplits. 
// En dan wordt er van elke directory een link gemaakt
    $dir=explode('/',str_replace($_SESSION['filedir'],"",$_SESSION['getdir']));
    $count = count($dir) -1;
// hier wordt de variable $dir_ gedefineerd. Deze zal op gevuld worden in de foreach-lus.
    $dir_ = '';
// Root directory 
    $Html = '<div class="FileNavi"><a href="'.$_SERVER['PHP_SELF'].'" >'.$_SESSION['rootname'].'</a>  ';

    foreach($dir AS $key =>$value){

        if (!($_SESSION['mainfiledir'] == $value )){
            
            if (!empty($value)){
                $dir_ .= '/'.$value;
                if ($count != $key){
// Subdirectory's 
                $Html.= '<a href="'.$_SERVER['PHP_SELF'].'?filedir='.$dir_.'/"> /'.$value.'</a>  ';
				}else{
// Anders een schuinstreepje voor de moeite. 
                $Html.= '/'.$value;
				}
			}
        }
    }
	$Html .= '</div>';
    return  $Html;
}


if(!empty($_POST['newdir'])){
    if (@mkdir ($_POST['dir'].'/'.$_POST['newdir'], 0777)){
    }else{
        $msg['text'] ='<img src="'.$_SESSION['fileicons'].'cancel.png" width="16" height="16" border="0" alt="cancel"">Er is een fout opgetreden bij het aanmaken van de nieuwe map.<br / >Geen rechten!<br /><a href="?filedir='. $_POST['getdir'].'">Klik hier om terug te gaan. </a>';
		$msg['title']=alertIcon("help.png").'Fout bij uitvoeren';
    }
}
if (isset($_GET['deletefile']) AND isset($_GET['dir']) AND !isset($_GET['ok'])) {
    $msg['text'] = 'Weet je zeker dat '.$_GET['filename'].'<br /> en de inhoud wilt verijderen?<br /><a href="'.$_SERVER['PHP_SELF'].'?deletefile='.$_GET['deletefile'].'&dir=1&ok=1&filedir='.$_SESSION['getdir'].'&filename='.$_GET['filename'].'"> Ja</a> / <a onclick="closepopup();" href="?filedir='.$_SESSION['getdir'].'" title="Venster sluiten" > Nee</a>';
	$msg['title']=alertIcon("help.png").'Bevestig'; 

}elseif (isset($_GET['deletefile']) AND isset($_GET['dir']) AND $_GET['ok']=='1') {

function deleteDirectory($dirname,$only_empty=false) {
    if (!is_dir($dirname))
        return false;

    $dscan = array(realpath($dirname));
    $darr = array();
    while (!empty($dscan)) {
        $dcur = array_pop($dscan);
        $darr[] = $dcur;
        if ($d=opendir($dcur)) {
           while ($f=readdir($d)) {
               if ($f=='.' || $f=='..')
                   continue;
               $f=$dcur.'/'.$f;
               if (is_dir($f))
                   $dscan[] = $f;
               else
                   unlink($f);
           }
           closedir($d);
        }
    }
    $i_until = ($only_empty)? 1 : 0;
        for ($i=count($darr)-1; $i>=$i_until; $i--) {

            if (rmdir($darr[$i])){
            $return = 'De map ['.$_GET['filename'].'] is met succes verwijdert van de server.<br /><a onclick="closepopup();" href="?filedir='.$_SESSION['getdir'].'" title="Venster sluiten" ><img src="'.$_SESSION['fileicons'].'tick.png" width="16" height="16" border="0" alt="Succes""> OK </a>';
            }else{
            $return = '      
            De map ['.$_GET['filename'].'] kon niet verwijderd worden van de server!<img src="'.$_SESSION['fileicons'].'cancel.png" width="16" height="16" border="0" alt="four""><a onclick="closepopup();" href="?filedir='.$_SESSION['getdir'].'" title="Venster sluiten" > OK </a>';
            }
        }
    return $return;
    }
    $msg = deleteDirectory($_GET['deletefile']);
            
} else{
    if (isset($_GET['deletefile'])){
        $msg['text'] = ' Weet je zeker dat <br /> '.$_GET['filename'].'<br /> wilt verijderen? <a href="'.$_SERVER['PHP_SELF'].'?deletefile='.$_GET['deletefile'].'&ok=1&filedir='.$_SESSION['getdir'].'&filename='.$_GET['filename'].'"><br />Ja</a> / <a onclick="closepopup();" href="?filedir='.$_SESSION['getdir'].'" title="Venster sluiten" >Nee';
		$msg['title']= alertIcon("help.png").'Bestand verwijderen';
    }if (isset($_GET['deletefile']) AND isset($_GET['ok']) AND $_GET['ok']== '1'){
        
        if(unlink($_GET['deletefile'])){
            $msg['text'] = 'Het bestand ['.$_GET['filename'].']<br /> is met succes verwijdert van de server.<br /><a onclick="closepopup();" href="?filedir='.$_SESSION['getdir'].'" title="Venster sluiten" ><br /><img src="'.$_SESSION['fileicons'].'tick.png" width="16" height="16" border="0" alt="Succes""> OK </a>';
			$msg['title']=alertIcon("information.png").'Bestand verwijderen';
            }else{
            $msg['text'] = '<img src="'.$_SESSION['fileicons'].'cancel.png" width="16" height="16" border="0" alt="four""><a onclick="closepopup();" href="?filedir='.$_SESSION['getdir'].'" title="Venster sluiten" >Het bestand ['.$_GET['filename'].'] kon niet verwijderdt worden van de server!</a>';
			$msg['title']= alertIcon("exclamation.png").'Fout bij verwijderen';
            }
    }
}

 if (isset($_GET['renamefile']) AND isset($_GET['filepath']) AND !isset($_GET['ok'])){
        $msg['text'] = '<FORM METHOD="post" ACTION="'.$_SERVER['PHP_SELF'].'?renamefile='.$_GET['renamefile'].'&ok=1&filepath='.$_GET['filepath'].'&filedir='.$_SESSION['getdir'].'"><INPUT TYPE="text" NAME="filename" SIZE="20" MAXLENGTH="30" value="'.$_GET['renamefile'].'"><input type="hidden" name="file"value="'.$_GET['filepath'].'"><input type="hidden" name="oldfile" value="'.$_GET['renamefile'].'"><input type="submit" name="Ok" value="Ok"</FORM><br />';
		
		$msg['title']=alertIcon("help.png").'Geef nieuw bestandsnaam op';

    }
		if (isset($_POST['filename']) AND isset($_GET['ok']) AND isset($_GET['filepath'])){
			
		   
			if(rename($_SESSION['filedir'].'/'.$_POST['oldfile'],$_SESSION['filedir'].'/'.$_POST['filename'] )) {

				$msg['text'] = $_POST['oldfile'].' <br /> Naar :<br />'.$_POST['filename'].'<a onclick="closepopup();" href="?filedir='.$_SESSION['getdir'].'" title="Venster sluiten" ><br />'.alertIcon("tick.png").'OK </a>' ;
				$msg['title']=alertIcon("information.png").'Bestand hernoemd';
				}else{
					$msg['text'] = 'Het bestand ['.$_POST['oldfile'].'] kan niet gewijzigt worden!<a onclick="closepopup();" href="?filedir='.$_SESSION['getdir'].'" title="Venster sluiten" ><br />'.alertIcon("cancel.png").'OK </a>' ;
					$msg['title']= alertIcon("exclamation.png").'Fout bij het hernoemen'.$_SESSION['filedir'];
				}

		}


if(!empty($_POST['upload'])) {
    $uploaddir = $_POST['dir'].'/';
    $uploadfile = $uploaddir . $_FILES['upfile']['name'];
    if(move_uploaded_file($_FILES['upfile']['tmp_name'], $uploadfile)){
        $msg['text'] = '<img src="'.$_SESSION['fileicons'].'tick.png" width="16" height="16" border="0" alt="Succes""><a href="javascript:closepopup();" title="Venster sluiten" >Het bestand ['.$_FILES['upfile']['name'].'] is met succes naar de server verstuurd</a>';
		$msg['title']=alertIcon("information.png").'Uploaded naar directory';
    }else{
        $msg['text'] = '<img src="'.$_SESSION['fileicons'].'cancel.png" width="16" height="16" border="0" alt="four""><a href="javascript:closepopup();" title="Venster sluiten" >Het bestand ['.$_FILES['upfile']['name'].'] kon niet verstuurd worden naar de server!</a>';
		$msg['title']= alertIcon("exclamation.png").'Fout bij het versturen';
    }
}

$Html = '';


if (!($_SESSION['filedir'] == $_SESSION['mainfiledir'])){
    $Html .= Navi();
    }else{
        $Html .= '<div class="FileNavi">'.$_SESSION['rootname'].'/</div>'; 
        };
$Html .='<table width="100%" border="0" cellspacing="1" cellpadding="0" class="FileList">
 <tr  class="FileList FileListTop">
  <td style="border-bottom:solid 1px #666666; width:16px;">&nbsp;</td>
  <td style="border-bottom:solid 1px #666666; width:400px;">Naam</td>
  <td style="border-bottom:solid 1px #666666">Type</td>
  <td style="border-bottom:solid 1px #666666">Grote</td>
  <td style="border-bottom:solid 1px #666666; width:160px;">Gewijzigd op</td>
  <td style="border-bottom:solid 1px #666666">Opties</td>
 </tr >
 <tr>
  <td colspan="4">';

$Html .= '</td>
<td align="right">
</td>
';

if ($dir = @opendir($_SESSION['filedir'])) {
  while (($file = readdir($dir)) !== false) {
    if($file != ".") {
        if ($file != "..") {
            if(is_dir($_SESSION['filedir'].'/'.$file)){

                $FileInfo = GetFileType($file);

                $Html .= '<tr onMouseOver="this.style.backgroundColor=\'#E8E8E8\'" onMouseOut="this.style.backgroundColor=\'#FFFFFF\'">';
                $Html .= '<td><img src="'.$_SESSION['fileicons'].''.$FileInfo[0].'" width="16" height="16" /></td>';
                $Html .= '<td>'.setDirLink($file).'</td>';
                $Html .= '<td>'.$FileInfo[1].'</td>';
                $Html .= '<td>--</td>';
                $Html .= '<td>'.date("d/m/Y H:i",filemtime($_SESSION['filedir'].'/'.$file)).'</td>';
                $Html .= '<td><a href="'.$_SERVER['PHP_SELF'].'?deletefile='.$_SESSION['filedir'].'/'.$file.'&dir=1&filedir='.$_SESSION['getdir'].'&filename='.$file.'" title="Delete '.$file.'"><img src="'.$_SESSION['fileicons'].'delete.png" width="16" height="16" border="0"/></a>';
				$Html .= '<a href="'.$_SERVER['PHP_SELF'].'?renamefile='.$file.'&filepath='.$_SESSION['filedir'].'&filedir='.$_SESSION['getdir'].'" title="Rename '.$file.'">
                <img src="'.$_SESSION['fileicons'].'textfield_rename.png" width="16" height="16" border="0"/></a>
                </td>';
                $Html .= '</tr>';
            }
        }
    }
  }
}
if ($dir = @opendir($_SESSION['filedir'])) {
  while (($file = readdir($dir)) !== false) {
     if(is_file($_SESSION['filedir'].'/'.$file)){

         $FileInfo = GetFileType($file);

         $Html .= '<tr onMouseOver="this.style.backgroundColor=\'#E8E8E8\'" onMouseOut="this.style.backgroundColor=\'#FFFFFF\'">';
         $Html .= '<td><img src="'.$_SESSION['fileicons'].''.$FileInfo[0].'" width="16" height="16" /></td>';
         $Html .= '<td>'.setFileLink($file).'</td>';
         $Html .= '<td>'.$FileInfo[1].'</td>';
         $Html .= '<td>'.FormatBytes(filesize($_SESSION['filedir'].'/'.$file)).'</td>';
         $Html .= '<td>'.date("d/m/Y H:i",filemtime($_SESSION['filedir'].'/'.$file)).'</td>';
        $Html .= '<td><a href="'.$_SERVER['PHP_SELF'].'?deletefile='.$_SESSION['filedir'].'/'.$file.'&filedir='.$_SESSION['getdir'].'&filename='.$file.'" title="Verwijder '.$file.'">
        <img src="'.$_SESSION['fileicons'].'bin.png" width="16" height="16" border="0"/></a>';
        $Html .= '<a href="'.$_SERVER['PHP_SELF'].'?renamefile='.$file.'&filepath='.$_SESSION['filedir'].'&filedir='.$_SESSION['getdir'].'" title="Rename '.$file.'">
		<img src="'.$_SESSION['fileicons'].'textfield_rename.png" width="16" height="16" border="0"/></a>
		<a href="download.php?file='.$_SESSION['filedir'].''.$file.'&filename='.$file.'" title="Download '.$file.'">
		<img src="'.$_SESSION['fileicons'].'page_white_go.png" width="16" height="16" border="0"/></a>
		</td>';
        $Html .= '</tr>';
     }
  }


  closedir($dir);
}


$Html .='</table><u onclick="showid(\'upload\')" title="Bestand uploaden" Style= "cursor: hand; float:none;">'.getIcon('folder_go.png').'</u>';
$Html .='<div id="upload" style="display:none; float:left;"><form enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'?filedir='.$_SESSION['getdir'].'" method="POST" class="FileList">
<input type="hidden" name="dir" value="'.$_SESSION['filedir'].'">
<input type="hidden" name="getdir" value="'.$_SESSION['getdir'].'">
<input type="hidden" name="upload" value="1">
<input type="hidden" name=MAX_FILE_SIZE" value="50000"> 
<input name="upfile" type="file">
<input type="submit" value="Uploaden" >
<u onclick="hideid(\'upload\')" title="Annuleren" Style= "cursor: hand;"><img src="'.$_SESSION['fileicons'].'cancel.png" width="16" height="16" border="0"/></u>
</form></div>';



echo $Html;

//**
// Popup box voor meldingen en invoer.
include('msgpopup.php');
// Aanroep van de functie 
if(!(isset($msg))) {$msg=null;}else{ echo MsgPopup($msg);}

?>        
</body>
</html>

