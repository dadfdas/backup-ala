<?php
if($fp = fopen($_GET['file'],"r")) {
	header("Content-type: application/octet-stream");
	header("Content-Disposition:attachment;filename=\"".$_GET['filename']."\"");
	header("Pragma: no-cache");
	header("Expires: 0");
	@fpassthru($fp);
}
else {
	echo 'Geen bestand';
}
?>