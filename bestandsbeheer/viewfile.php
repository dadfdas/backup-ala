<?php
session_start();
include("config.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
 <title>Dresa Filemanager</title>
 <script src="popup.js" type="text/javascript"></script>
 <link rel="stylesheet" type="text/css" href="style.css" />
<style type="text/css">
html,body {
height:100%;
width:100%;
overflow:hidden;
}
</style>
</head>
<body>
<div id="popupcenter">
<table cellspacing="0" cellpadding="0" border="0" >
	<tr>
		<td class="heading" colspan="2">
		<?php 
		if (isset($_GET["file"])) {
			echo $_GET["file"]; 
			} else {
				$msg['text'] = 'Er is geen bestandsnaam opgegeven<br />of er is een onbekende error opgetreden'; 
				$msn['title']= alertIcon("exclamation.png").'Fout bij het laden van bestand';
			} ?>
		</td>
	</tr>
	<tr>
		<td class="viewfile_main" colspan="2">
			<div class="viewfile_div">
			<table cellspacing="0" cellpadding="0" border="0">
				<?php
				if (isset($_GET["file"])) {
					$file = $_SESSION['filedir'].$_GET["file"];
					$handle = file($file);
					$numbers = count($handle);
				?>
				<tr><td class="viewfile_linenumbers">
					<code><?php
					for ($i = 1; $i <= $numbers; $i++) {
						echo $i."&nbsp;<br />\n";
					}?>
					</code>
					</td>
					<td class="viewfile_file"><?php highlight_file($file); ?></td>
				</tr>
			</table>
			</div>
		</td>
	</tr>
</table>
<?php
}
//**
// Popup box voor meldingen en invoer.
include('msgpopup.php');
// Aanroep van de functie 
if(!(isset($msg))) {$msg=null;}else{ echo MsgPopup($msg);}

?>
</div>
</body>
</html>

