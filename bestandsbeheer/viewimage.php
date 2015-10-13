<?php
session_start();
if(isset($_SESSION["login"]) && $_SESSION["login"] == 1) {

include("config.inc.php");
$nofilemessage = "No file specified.";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/2002/REC-xhtml1-20020801/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>babbaExplorer - <?php if (isset($_GET["file"])) { echo $_GET["file"]; } else { echo $nofilemessage; } ?></title>
<link rel="stylesheet" href="gfx/style.css" type="text/css" />
</head>
<body>
<table cellspacing="0" cellpadding="0" border="0" class="maintable">
	<tr>
		<td class="toptitle">babbaExplorer</td>
		<td class="topmenu"><a href="index.php?dir=<?php echo $_GET['dir']; ?>"><img src="gfx/button_back.png" alt="Go back" class="imagebutton" /></a></td>
	</tr>
	<tr>
		<td class="heading" colspan="2"><?php if (isset($_GET["file"])) { echo $_GET["file"]; } else { echo $nofilemessage; } ?></td>
	</tr>
	<tr>
		<td class="viewfile_main" colspan="2">
			<div class="viewfile_div">
			<table cellspacing="0" cellpadding="0" border="0">
				<?php
				if (isset($_GET["file"])&&isset($_GET["dir"])) {
					$file = $maindir."/".$_GET["dir"].$_GET["file"];
				?>
				<tr>
					<td class="viewfile_image"><img src="img.php?dir=<?php echo $_GET["dir"]; ?>&amp;file=<?php echo $_GET["file"]; ?>" alt="Image preview" /></td>
				</tr>
			</table>
			</div>
		</td>
	</tr>
</table>
<?php
}
else {
	echo "<code>".$nofilemessage."</code>";
}
?>
</body>
</html>
<?php
}
else {
	echo "You are not logged in.";
}
?>