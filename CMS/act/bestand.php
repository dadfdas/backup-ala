<?php
session_start();
include_once('../../includes/inc_data.php');

if (!isset ($_SESSION['user']))
{
  die("Hack attempt");
}
else
{
  $id = $_GET['ID'];
  echo ("
  <html>
  <head>
    <title>Uploadbestand</title>
    <link href='../inc/default.css' type='text/css' rel='stylesheet'>
  </head>
  <body>
    <table border='0'>
      <tr>
        <td> 
          <b>Bestanden uploaden.</b>
        </td>
        <td align='right'>
	      <a href='files.php?ID=$id' alt='terug'>Terug naar koppelen bestand.</a>
	    </td>
      </tr>
      <tr>
        <td colspan='2' align='center'>
          <iframe src='http://www.kennisbus-nl.nl04.members.pcextreme.nl/bestandsbeheer/index.php' height=\"500\" width=\"900\"></iframe>
        </td>
      </tr>
    </table>
  </body>
  </html>");
}
?>