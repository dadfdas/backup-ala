<?php
include_once('inc_functions.php');
include_once('inc_functions_beheer.php');

//============= database connectie =======
//$mysqldb=mysql_connect("sql8.pcextreme.nl","18232Bus","w8woord") or die ("FOUT! Database niet gevonden, of wachtwoord incorrect!");
//$db= mysql_select_db("18232Bus",$mysqldb) or die ("FOUT: openen database mislukt :(");

// Connectie naar usbwebserver
$mysqldb=mysql_connect("localhost","root","usbw") or die ("FOUT! Database op de usbwebserver niet gevonden, of wachtwoord incorrect!");
$db= mysql_select_db("kennisbus",$mysqldb) or die ("FOUT: openen database op usbwebserver mislukt");
//========================================
?>