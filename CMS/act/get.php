<?php
# get.php
# Auteur : Cynthia Fridsma <cynthia@heathernova.info>

// verzend het gewenste bestand naar de eind gebruiker
// om welk bestand gaat het?
$filecontent .= trim($_GET['start']);
$file_name = trim($_GET['file_name']);
$size = filesize ($filecontent);

// We gebruiken een kleine buffer zodat de 
// download vrijwel meteen begint.

$bufsize = 2000;

// We gebruiker een header zodat gebruiker in zijn/haar
// browser een venster krijgt om het bestand op te slaan.

header("HTTP/1.1 200 OK");
header("Content-Length: $size");
header("Content-disposition: attachment; filename=$file_name");
header("Content-Type: application/force-download");
header("Content-Disposition: attachment; filename=$file_name");
header("Content-Transfer-Encoding: binary");

//  Open het bestand in binary mode en stuur de output naar
//  het scherm. Hierdoor krijgt de gebruiker het bestand op zijn/haar
//  computer.

$fd = fopen($filecontent, "rb") or die ("no such file");

while (!feof($fd)) 
{
  $contents = fread($fd, $bufsize);
  echo $contents;                   
}

fclose ($fd);

// n.b. in plaats van echo mag je ook print gebruiker...
?> 