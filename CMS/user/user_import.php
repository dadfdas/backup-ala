<?php
/**
*  Leerlingen importeren uit bestand.
*  Deze pagina bevat de functie: draw_selectbox()
*  Deze pagina gebruikt de functie:
*     -
*/
//Controleer eerst of er al een session is (!)
if (!isset ($_SESSION['user']))
{
  die("Hack attempt");
}
else
{	## Else[1] (deze wordt onderin afgesloten)

  //$tablenaam    De tabelnaam uit de database
  $tablenaam = "tblstudent";
  //$attributen   Array met daarin de vereiste veldnamen
  $attributen = array("IDStudent","Email","Achternaam","Voornaam","Klas","Archief","locatie", "tutor");
  //$filename    Bestand waarin de importtext tijdelijk opgeslagen wordt
  $filename = "importfile.txt";
  //$img_map     Directorie waar de plaatjes staan.
  function draw_selectbox($veld,$selecteer = "")
  {
    global $attributen;
	echo '<select name="'.$veld.'">';
	echo '<option value=""></option>';
	for ($i = 0; $i < sizeof($attributen); $i++)
	{
	  echo '<option value="'.$attributen[$i].'">'.$attributen[$i].'</option>';
	}
	echo '</select>';
  }
  
  if ( isset($_FILES['importfile']['size']) )
  {
	$_POST['usertoken'] = "\n";
	
	@unlink($filename);
	if ($_FILES['importfile']['size'] || $_FILES['importfile']['type'] != "text/plain")
	{
	  move_uploaded_file($_FILES['importfile']['tmp_name'],$filename);
	  $file = fopen($filename,"r");
	  $fileinhoud = fread($file, filesize($filename));
	  if ($_POST['usertoken'] == $_POST['valuetoken'])
	  {
	    echo "De karakters waarmee het script de gegevens moet kunnen onderscheiden zijn hetzelfde.<BR> Pas deze aan!";
	  }
	  
	  $regels = explode(stripslashes($_POST['usertoken']),$fileinhoud);
	  
	  echo 'Het bestand is succesvol geopend.<br />';
	  echo '<form name="importer" method="post">
	  	    <input type="hidden" name="usertoken" value="'.$_POST['usertoken'].'">
		    <input type="hidden" name="valuetoken" value="'.$_POST['valuetoken'].'">
		    <table id="tabel">';
	  echo '</table>';
	  echo "Er zijn ".sizeof($regels). " gebruiker(s) gevonden in het bestand.";
	  echo '<table id="tabel">';
	  
	  $lengte = 0;
	  $output = '';
	  // Inhoud bestand uitlezen
	  for($i = 0; $i < sizeof($regels); $i++)
	  {
	    $output .= "<tr>";
		$kolommen = explode(stripslashes($_POST['valuetoken']),$regels[$i]);
		
		for($o = 0; $o < sizeof($kolommen); $o++)
		{
		  $output .= "<td>".$kolommen[$o]."</td>";//
		}
		
		if ($lengte < sizeof($kolommen))
		{
		  $lengte = sizeof($kolommen);
		}
		
		$output .= "</tr>";
	  }
	  
	  echo "<tr>";
	
	  for ($i = 0; $i < $lengte; $i++)
	  {
	    echo "<td>";	
		draw_selectbox($i);
		echo "</td>";
	  }
	  
	  echo "</tr>";
	  echo $output;
	  echo '</table>

	  <input type="submit" name="submitknop" value="Importeren"></form>';
	}
	else
	{
	  echo "Geen bestand meegegeven of geen text bestand geupload.";
	}
  }
  elseif ( isset($_POST['submitknop']) )
  {
    $file = fopen($filename,"r");
	$fileinhoud = fread($file, filesize($filename));

	if ($_POST['usertoken'] == $_POST['valuetoken'])
	{
	  echo "De karakters waarmee het script de gegevens moet kunnen onderscheiden zijn hetzelfde.<BR> Pas deze aan!";
	}
	
	$regels = explode("\n",$fileinhoud);
	
	echo "De geselecteerde items worden in de database gezet..<BR><BR>";
	
	$blaat = 0;
	$tabellen_insert = "";

	
	for($i = 0; $i < sizeof($regels); $i++)
	{
		$kolommen = explode(stripslashes($_POST['valuetoken']),$regels[$i]);
	}
	
	for ($i = 0; $i < sizeof($kolommen); $i++)
	{
	  if (isset($_POST[$i]) )
	  {
		  //echo "POST: ".$i." :".$_POST[$i]."<br />";
	    $blaat++;
		$tabellen_insert .=  $_POST[$i];
		
		if ($blaat+1 <= sizeof($kolommen))
		{
		  $tabellen_insert .= ",";
		}
	  }
	}
	//echo "<br />Tabellen_instert: ".$tabellen_insert."<br />";
	$lengte = $blaat;
	//echo "Lengte: ".$lengte."<br />";
	//Truncate table staat voor het weggooien van al de records in tblstudent.
	//mysql_query("TRUNCATE TABLE tblstudent");
	for($i = 0; $i < sizeof($regels); $i++)
	{
      $blaat2 = 0;
	  $sql_values = "";
	  $blaat = explode(stripslashes($_POST['valuetoken']),$regels[$i]);
	  for($o = 0; $o < sizeof($attributen); $o++)
	  {
	    if ( isset($_POST[$o]) )
		{
		  $blaat2++;
		  $sql_values .=  "'".(addslashes($blaat[$o]))."'";
		  if ($blaat2+1 <= $lengte)
		  {
		    $sql_values .= ",";
		  }
		}
	  }
	  
	  $query = "REPLACE INTO ".$tablenaam. " (".$tabellen_insert.") VALUES (".$sql_values.")";
	  //echo 'Controleer query:<p>'.$query.'</p>'; 
	  //echo $query;
	  mysql_query($query) or die('<br /><font color="red">Er is iets fout gegaan.<br />'.mysql_error().'</font>');
	}
	echo "Done...<br /> Er zijn <b>".sizeof($regels)."</b> query's uitgevoerd";
  }
  else
  {
  ?>
  <form enctype="multipart/form-data" method="post">
    <table id="tabel">
      <tr>
	    <td>Alle gebruikers</td>
		<td>	
		  <select name="usertoken">
		    <option value=" " selected="selected">op een nieuwe regel (enter)</option>
		  </select>
        </td>
	  </tr>
	  <tr>
	    <td>Per gebruiker gegevens </td>
		<td>
		  <select name="valuetoken">
		    <option value=";">gescheiden door teken&nbsp;&nbsp;&nbsp;&nbsp;;</option>
		    <option value="|">gescheiden door teken&nbsp;&nbsp;&nbsp;&nbsp;|</option>
		    <option value="||">gescheiden door teken&nbsp;&nbsp;&nbsp;&nbsp;||</option>
		  </select>
		</td>
      </tr>
	  <tr>
	    <td>Bestand</td>
		<td>
		  <input type="file" name="importfile" />
		</td>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
		<td>
		  <input type="submit" Value="Verzenden" />
		</td>
	  </tr>
	</table>
  </form>
  <?php
  }
}
?>


