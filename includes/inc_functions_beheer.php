<?php
/**
* Functies in deze pagina: (in voldorde)
*     show_form($message) , isEmail($sWaarde) , selectDag($selected) , selectIDType($selected) , selectPlaats($selected)
*     selectNiveau($selected) , selectSoort($selected) , selectGever($selected) , selectOpleiding($selected)
*     getOpleiding($selected) , generate_menu($IDType) , fetch_rows_beheer($week, $jaar) , file_upload($filename)
*     archieveren($archief,$id) , verwijder_activiteit($id) , verwijderdocent($id) , show_aanmeldingen($sql1,$sql2,$actid,$num2)
*/

/**
* Maakt login formulier
* @param String $message
* @return String
*/
function show_form($message)	
{
  echo '<center>
		 <div id="fout" align="center">';
  echo $message;
  echo '</div>
		 <br />
		 <div id="main">Log hieronder in:</div>
		 <form method="POST" action="">
		   <table id="tabel">
			 <tr>
			   <td> Gebruikersnaam: </td>
			   <td> 
				 <input type="text" name="username" /> 
			   </td>
			 </tr>
			 <tr>
			   <td> Wachtwoord: </td>
			   <td> 
				 <input type="password" name="pwd" /> 
			   </td>
			 </tr>
			 <tr>
			   <td colspan="2"> 
				 <center>
				   <input type="submit" name="submit" value="Inloggen" />
				 </center>
			   </td>
			 </tr>
		   </table>
		 </form>
	   </center>';
}

/**
* Een methode om te kijken of een waarde een correct email adres bevat
* @param String $sWaarde
* @return boolean
*/
function isEmail( $sWaarde ) {
    if( !empty($sWaarde) ) {
        if( function_exists('filter_var') ) {
            return filter_var($sWaarde, FILTER_VALIDATE_EMAIL);
        }
        else {
            return preg_match('/^[a-z0-9_\.-]+@([a-z0-9]+([\-]+[a-z0-9]+)*\.)+[a-z]{2,7}$/i', $sWaarde);
        }
    }
    else {
        return false;
    }
}

/**
* Slectie box maken met Plaatsen
* @return  Selectie box met alle mogelijkheden en eventuele selectie
*/
function selectDag($selected)
{
	$select = '<select name="Dag">';
	for ($i=1;$i<6;$i++)
	{
	  switch ($i)
	  {
		case 1: $day = "Maandag";break;
		case 2: $day = "Dinsdag";break;
		case 3: $day = "Woensdag";break;
		case 4: $day = "Donderdag";break;
		case 5: $day = "Vrijdag";break;
	  }
		$select .= '<option value="'.$i.'"';
		if ( $i == $selected ) {
			$select .= ' selected="selected"';
		}
		$select .= '>'.$day.'</option>';
	}
	$select .= '</select>';

	return $select;
}

/**
* Slectie box maken met IDType en Typenaam
* @return  Selectie box met alle mogelijkheden en eventuele selectie
*/
function selectIDType($selected)
{
	$query = mysql_query("SELECT * FROM tblgeverstype ORDER BY IDType");
	
	$select = '<select name="IDType">';
	if ( mysql_num_rows($query) ) {
		while($fetch = mysql_fetch_array($query))	{
		
			$select .= '<option value="'.$fetch['IDType'].'"';
			if ( $fetch['IDType'] == $selected ) {
				$select .= ' selected="selected"';
			}
			$select .= '>'.$fetch['Typenaam'].'</option>';
		}
	}
	else {
		$select .= '<option value="">Database leeg</option>';
	}
	$select .= '</select>';
	
	return $select;
}

/**
* Slectie box maken met Plaatsen
* @return  Selectie box met alle mogelijkheden en eventuele selectie
*/
function selectPlaats($selected)
{
	$locaties = array ('G', 'Z', 'E');
	
	$aantal_locaties = count($locaties);
	
	$select = '<select name="Plaats">';
	for ($i = 0; $i < $aantal_locaties; $i++) {

		$select .= '<option value="'.$i.'"';
		if ( $i == $selected ) {
			$select .= ' selected="selected"';
		}
		$select .= '>'.$locaties[$i].'</option>';
	}
	$select .= '</select>';
	
	return $select;
}

/**
* Selectie box maken met alle niveaus
* @return  Selectie box met alle mogelijk heden en eventuele selectie
*/
function selectNiveau($selected)
{
	$SQLNiveau = mysql_query("SELECT * FROM tblniveau ORDER BY IDNiveau");
	
	$select = '<select name="IDNiveau">';
	if ( mysql_num_rows($SQLNiveau) ) {
		while($niveau = mysql_fetch_array($SQLNiveau))	{
		
			$select .= '<option value="'.$niveau['IDNiveau'].'"';
			if ( $niveau['IDNiveau'] == $selected ) {
				$select .= ' selected="selected"';
			}
			$select .= '>'.$niveau['Niveau'].'</option>';
		}
	}
	else {
		$select .= '<option value="">Database leeg</option>';
	}
	$select .= '</select>';
	
	return $select;
}

/**
* Selectie box maken met alle soorten activiteiten
* @return  Selectie box met alle mogelijk heden en eventuele selectie
*/
function selectSoort($selected)
{
	$SQLSoort = mysql_query("SELECT * FROM tblsoort ORDER BY IDSoort");
	
	$select = '<select name="IDSoort">';
	if ( mysql_num_rows($SQLSoort) ) {
		while($fetch = mysql_fetch_array($SQLSoort))	{
		
			$select .= '<option value="'.$fetch['IDSoort'].'"';
			if ( $fetch['IDSoort'] == $selected ) {
				$select .= ' selected="selected"';
			}
			$select .= '>'.$fetch['Soort'].'</option>';
		}
	}
	else {
		$select .= '<option value="">Database leeg</option>';
	}
	$select .= '</select>';
	
	return $select;
}

/**
* Selectie box maken met docenten/gevers
* @return  Selectie box met alle mogelijk heden en eventuele selectie
*/
function selectGever($selected)
{
	$SQLGever = mysql_query("SELECT IDGever, Naam, gVoornaam FROM tblgever ORDER BY Naam");
	
	$select = '<select name="IDGever">';
	if ( mysql_num_rows($SQLGever) ) {
		while($fetch = mysql_fetch_array($SQLGever))	{
		
			$select .= '<option value="'.$fetch['IDGever'].'"';
			if ( $fetch['IDGever'] == $selected ) {
				$select .= ' selected="selected"';
			}
			$select .= '>'.$fetch['Naam'].', '.$fetch['gVoornaam'].'</option>';
		}
	}
	else {
		$select .= '<option value="">Database leeg</option>';
	}
	$select .= '</select>';
	
	return $select;
}

/**
* Selectie box maken met opleidingen
* @return  Selectie box met alle mogelijk heden en eventuele selectie
*/
function selectOpleiding($selected)
{

	$SQLOpleiding = mysql_query("SELECT * FROM tblopleiding ORDER BY IDOpleiding");
	
	$select = '<select name="IDOpleiding">';
	if ( mysql_num_rows($SQLOpleiding) ) {
		while($fetch = mysql_fetch_array($SQLOpleiding))	{
		
			$select .= '<option value="'.$fetch['IDOpleiding'].'"';
			if ( $fetch['IDOpleiding'] == $selected ) {
				$select .= ' selected="selected"';
			}
			$select .= '>'.$fetch['Opleiding'].'</option>';
		}
	}
	else {
		$select .= '<option value="">Database leeg</option>';
	}
	$select .= '</select>';
	
	return $select;
}

/**
*  Naam ophalen van de opleiding bij het nummer
* @return  naam van de opleiding
*/
function getOpleiding($selected)
{
	$SQLOpleiding = mysql_query("SELECT * FROM tblopleiding WHERE IDOpleiding = '".quote_smart($selected)."'");
	
	if ( mysql_num_rows($SQLOpleiding) ) {
		$fetch = mysql_fetch_array($SQLOpleiding);
	
		return $fetch['Opleiding'];
	}
	else {
		return 'Opleiding niet gevonden';
	}
}

/**
* Genereer menu
* @param String $IDType
* @return String
*/
function generate_menu($IDType)
{
  switch ($IDType)
  {
    case 1:
	  echo '
	       <b>Activiteit</b><br>
		    -> <a href="index.php?page=actoverzicht">Overzicht</a><br>
		    -> <a href="index.php?page=acttoevoegen">Toevoegen</a><br>
		    -> <a href="index.php?page=actzoek">Zoeken</a><br>
		   <b>Animo</b><br>
		    -> <a href="index.php?page=animooverzicht">Overzicht</a><br>		    
		    -> <a href="index.php?page=actanimo">Toevoegen</a><br>
		   <b>Docent</b><br>
		 	-> <a href="index.php?page=geveroverzicht">Overzicht</a><br>
			-> <a href="index.php?page=gevertoevoegen">Toevoegen</a><br>
		   <b>Student</b><br>
			-> <a href="index.php?page=user">Overzicht</a><br>
			-> <a href="index.php?page=user_add">Toevoegen</a><br>
			-> <a href="index.php?page=user_import">Importeren</a><br>
		   <b>Teksten</b><br>
		 	-> <a href="index.php?page=tekst_edit">Aanpassen</a>
			</div><hr><div id="menu">
			';
			break;
	case 2:
	  echo '<b>Activiteit</b><br>
			 -> <a href="index.php?page=actoverzicht">Overzicht</a><br>
			 -> <a href="index.php?page=actzoek">Zoeken</a><br>
			</div><hr><div id="menu">
			';
			break;
  }
}


/**
* tabel maken
* @return Tabel met allle activiteiten van een week
*/
function fetch_rows_beheer($week, $jaar)
{
	// Controleren of er wel iets in de database staat voor deze week en jaar.
  $result = mysql_query("SELECT * FROM tblactiviteiten where Week='".$week."' AND Jaar='".$jaar."' AND Animo='0' ORDER BY Dag");
  if ( mysql_num_rows($result) ) { 	

	  if (!empty($week) && ctype_digit($week) && !empty($jaar) && ctype_digit($jaar) )
	  {
		echo '<div id="head">Week '.$week.' - '.$jaar.'</div>';
		$bool = 1;
	
		fill_td_beheer($week, $jaar); ## functie vul TD's
		echo '</div>';
		
	  }
	  else 
	  { 
		echo '<div id="head">Fout!</div>
			  <div id="main"> Er is geen weeknummer opgegeven!</div>';
	  }
  }
  else {
		echo '<div id="head">Resultaat</div>
			  <div id="main">Geen activiteit gevonden in de week: '.$week.' en jaar: '.$jaar.'</div>';
  }
}
//
//LEFT JOIN tblgever on tblactiviteiten.IDgever = tblgever.IDgever
//AND tblactiviteit.IDGever = '$gever'

function fill_td_beheer($week, $jaar)
{
  echo '<div id="main">';
   
	if ($_SESSION['IDType'] == 1 )
	{
	  $SQL = mysql_query("SELECT * FROM tblactiviteiten 
	  				      RIGHT JOIN tblgever on tblactiviteiten.IDGever = tblgever.IDGever
	  				      LEFT JOIN tblsoort on tblactiviteiten.IDSoort = tblsoort.IDSoort
	  				      LEFT JOIN tblniveau on tblactiviteiten.IDNiveau = tblniveau.IDNiveau
	  				      LEFT JOIN tblopleiding on tblactiviteiten.IDOpleiding = tblopleiding.IDOpleiding
						  WHERE Week = '".$week."' AND Jaar = '".$jaar."' AND Animo = '0'
						  ORDER  BY Dag,Tijd"); //
	} 
	else 
	{
	  $gever = $_SESSION['IDGever'];
	  $SQL = mysql_query("SELECT * FROM tblactiviteiten 
	  					  RIGHT JOIN tblgever on tblactiviteiten.IDgever = tblgever.IDgever
	  				      LEFT JOIN tblsoort on tblactiviteiten.IDSoort = tblsoort.IDSoort
	  				      LEFT JOIN tblniveau on tblactiviteiten.IDNiveau = tblniveau.IDNiveau
	  				      LEFT JOIN tblopleiding on tblactiviteiten.IDOpleiding = tblopleiding.IDOpleiding
						  WHERE Week = '".$week."' AND Jaar = '".$jaar."' AND Animo = '0'
						  ORDER  BY Dag,Tijd");
	}

  if ( mysql_num_rows($SQL) )
  {
    echo '
	 	 <table id="tab">
		  <tr>
		   <th>Dag</th>
		   <th>Titel</th>
		   <th>Type</th>
		   <th>Niveau</th>
		   <th>Tijd</th>
		   <th>Lokaal</th>
		   <th>Docent</th>
		   <th>Limiet</th>
		   <th>Aangemeld</th>
		   <th>Presentie</th>
		 ';					
	
	if($_SESSION['IDType'] == 1)	{				
	  echo '<th>Archiveren</th>
	        <th>Verwijderen</th>';
	}
	echo '</tr>';
	
	while ($row= mysql_fetch_array($SQL))
	{
      $id = $row['IDActiviteit'];
	  $Titel = $row['Titel'];
	  $Type = $row['Soort'];
	  $Niveau = $row['Niveau'];
	  $Tijd = $row['Tijd'];
	  $Lokaal = $row['Lokaal'];
	  $voornaam = substr($row['gVoornaam'],0,1);
	  $Gever = $row['Naam'];
	  $Docent = $row['IDGever'];
	  $Limiet = $row['Limiet'];
	  $Archief = $row['AArchief'];
	  
	  $InlogID = $_SESSION['IDGever'];
	  
		if ( $row['onbeperkt'] == 1 ) { 
			$Limiet = 'n.v.t.';
		}
			// Aantal geactiveerde inschrijvingen ophalen
			  $SQL2 =mysql_query("SELECT * FROM tblinschrijvingen WHERE tblinschrijvingen.IDActiviteit = " . quote_smart($id) . " 
								  AND tblinschrijvingen.Activated = '1' ORDER BY Nummer ASC") or die (mysql_error());	
			  $nums1 = mysql_num_rows($SQL2);

	  		// Aantal niet geactiveerde inschrijvingen ophalen
			$SQL3 =mysql_query("SELECT * FROM tblinschrijvingen WHERE tblinschrijvingen.IDActiviteit = " . quote_smart($id) . "
							  AND tblinschrijvingen.Activated = '0' ORDER BY Nummer ASC") or die (mysql_error());
			
			$nums4 = '';
			if (mysql_num_rows($SQL3)) 
			{
				$nums4 .= " (".mysql_num_rows($SQL3).")";
			}

	  $ourl = "<u>
	             <a href=\"javascript:popUp('act/aanmeldingen.php?actid=".$id."')\">
				   <img src='images/lijst.png' border ='0' />
				 </a>
			   </u>";
	  
	  // -- Genereer de dag naam -- //
	  $dag = switch_day($row['Dag']);
	  
	  if (empty($Type))
	  {
	  	$Type = "&nbsp;";
	  }	
	  if (empty($Niveau))
	  {
	  	$Niveau = "&nbsp;";
	  }		  		  
	  if (empty($Lokaal))
	  {
	  	$Lokaal = "&nbsp;";
	  }									
	  if (empty($Limiet))
	  {
	  	$Limiet = "&nbsp;";
	  }
	  
	  switch ($row['Plaats'])
	  {
	    case 0: $plaats = "G";break;
		case 1: $plaats = "Z";break;
		case 2: $plaats = "E";break;
	  }
	  
	    echo '
			 <tr>
			   <td>'.$dag.'</td>
			   <td>
			     <u>
			      ';
	  		if ($Archief == "1")  { echo '<font style="font-style:italic;">'; }
			       if ($Docent == $InlogID || $_SESSION['IDType'] == 1)
			       {
				     echo '<a href="index.php?page=actaanpassen&actid='.$id.'&week='.$week.'&jaar='.$jaar.'">'.$Titel.'</a>';
				   }
				   else
				   {
					 echo $Titel;
				   }
			if ($Archief == "1")  { echo '</font>'; }
				   echo '
				 </u>
			   </td>
			   
			   <td>'.$Type.'</td>
			   <td><center>'.$Niveau.'<center></td>
			   <td>'.$Tijd.'</td>
			   <td>'.$plaats.' '.$Lokaal.'</td>
			   <td>'.$voornaam.' '.$Gever.'</td>
			   <td><center>'.$Limiet.'</center></td>
			   <td title="Geactiveerd (niet geactiveerd)"><center>'.$nums1.' '.$nums4.'</center></td>
			   <td><center>'.$ourl.'</center></td>
			 ';
			   
		if($_SESSION['IDType'] == 1)
		{
		  echo '<td>
		          <center>';
				if ($Archief == "1") {
					echo '<a href="index.php?page=actoverzicht&archief=n&id='.$id.'&week='.$week.'&jaar='.$jaar.'" title="Al reeds gearchiveerd.">
				     <img src="images/ico_archive2.gif" alt="Al reeds gearchiveerd." border="0">
				   </a>';
				}
				else {
					echo '<a href="index.php?page=actoverzicht&archief=y&id='.$id.'&week='.$week.'&jaar='.$jaar.'" title="Archieveren?">
					  <img src="images/ico_archive.gif" alt="Archieveren?" border="0" />
					</a>';
				}
				echo '
				  </center>
				</td>
			    <td>
		          <center>
					<a href="index.php?page=actoverzicht&amp;verwijderen=y&id='.$id.'&week='.$week.'&jaar='.$jaar.'" onCLick="return confirm(';
																												echo "'Weet u het zeker? De aanmeldingen worden ook verwijderd.')";
					echo '" title="Verwijderen?">
					  <img src="images/delete_icon.gif" alt="Verwijderen?" border="0" />
					</a>
				  </center>
				</td>';
	    }
        
		$nums1 = "";
		$nums4 = "";
	}
	echo '</tr>
		</table>';
  }
  else
  {
    echo 'Er zijn geen resultaten gevonden voor deze week.'; 
  }
}

/**
* Functie om bestanden te uploaden.
*/	  
function file_upload($filename)
{
  global $_FILES; 
	
  $filename = $_FILES['bestand']['name'];  
	
  $allow[0] = ".doc";
  $allow[1] = ".docx";
  $allow[2] = ".xls";
  $allow[3] = ".xlsx";
  $allow[4] = ".ppt";
  $allow[5] = ".pptx";
  $allow[6] = ".pps";
  $allow[7] = ".ppsx";
  $allow[8] = ".pdf";
  $allow[9] = ".rar";
  $allow[10] = ".zip";
  // $allow[11] = "exe"; enz.
	
  if ($_POST["newname"])
  {
    $uploadname = $_POST['newname'];
  } 
  else 
  { 
    $uploadname = $_FILES['bestand']['name'];
  }
  
  $extentie = strstr($uploadname, '.');
	
  for ($i = 0; $i < count($allow); $i++)
  {
    if ($extentie == $allow[$i])
	{
	  $extentie_check = "ok";
	  $i = count($allow) + 10; // om loop te beindigen
	}
  }
	
  if ($extentie_check)
  {
    if (is_uploaded_file($_FILES['bestand']['tmp_name'])) 
	{  
	  move_uploaded_file($_FILES['bestand']['tmp_name'], "../uploads/" . $uploadname);  
	}
  } 
  else 
  {
    echo "Er is een fout met het uploaden van het bestand.<br />\n
		  Waarschijnlijk is het bestand te groot of heeft u een verkeerde bestandstype gebruikt.<br />\n
		  De volgende bestandstypen zijn toegestaan:<br />\n
		  .doc, .docx<br />\n
		  .xls, .xlsx<br />\n
		  .ppt, .pptx<br />\n
		  .pps, .ppsx<br />\n
		  .pdf<br />\n
		  .rar<br />\n
		  .zip";
  }
}

/**
* Activiteit in archief zetten of eruit.
*/
function archieveren($archief,$id,$week,$jaar) 
{
  if ($archief == 'y') 
  {
    $archief = 1;
	$query=	"UPDATE `tblactiviteiten` SET
			 `AArchief` = '".quote_smart($archief)."'
			  WHERE `IDActiviteit` = '".quote_smart($id)."'";
		      //Voer de Query uit
	
	mysql_query($query) or die (mysql_error());
	echo ("<p id='goed'> De activiteit is gearchiveerd ! </p>
		   <meta http-equiv='REFRESH' content='3;URL=index.php?page=actoverzicht&week=".$week."&jaar=" .$jaar."'>"); 
  } 
  elseif ($archief == 'n') 
  {
    $archief = 0;
	$query=	"UPDATE `tblactiviteiten` SET
			 `AArchief` = '".quote_smart($archief)."'
			 WHERE `IDActiviteit` = '".quote_smart($id)."'";
		     //Voer de Query uit
	mysql_query($query) or die (mysql_error());
	
	echo ("<p id='goed'> De activiteit is uit het archief gehaald! </p>
		   <meta http-equiv='REFRESH' content='3;URL=index.php?page=actoverzicht&week=" .$week."&jaar=" .$jaar." '>");
  } 
  else 
  {
    echo ("Er is iets fout gegaan");  
  }
}

/**
* Activiteit verwijderen
* Activiteit + aanmeldingen verwijderen.
*/
function verwijder_activiteit($id,$week,$jaar) 
{
	$query =	"DELETE FROM tblactiviteiten WHERE IDActiviteit = '".quote_smart($id)."'";
			  //Voer de Query uit
	
	mysql_query($query) or die (mysql_error());
	
	$query_aan = "DELETE FROM tblinschrijvingen WHERE IDActiviteit = '".quote_smart($id)."'";
	mysql_query($query_aan) or die (mysql_error());
	
	echo "Activiteit en aanmeldingen zijn verwijderd.";
	if ( $week == 0 && $jaar == 0 ) {
		echo "<meta http-equiv='REFRESH' content='3;URL=index.php?page=animooverzicht'>";
	}
	else {
		echo "<meta http-equiv='REFRESH' content='3;URL=index.php?page=actoverzicht&week=" .$week."&jaar=" .$jaar." '>";	
	}
}

/**
* Docent verwijderen
* 
*/
function verwijderdocent($id) 
{
  $query=	"DELETE FROM tblgever WHERE tblgever.IDGever = '".quote_smart($id)."'";
		      //Voer de Query uit
	
  mysql_query($query) or die (mysql_error());
  
  echo "Docent verwijderd.<meta http-equiv='REFRESH' content='3;URL=index.php?page=geveroverzicht'>";
}

/**
* Aanmeldingen lijst maken
* voor: CMS/act/aanmeldingen.php
*/
function show_aanmeldingen($sql1,$sql2,$actid,$num2)
{
  echo '
    <table id="tab">
	  <th> Leerlingnr </th>
	  <th> Naam </th>
	  <th> Klas </th>
	  <th> Commentaar </th>
	  <th> Handtekening </th>
	  </tr>
	  <tr>
	  ';
  while ($row = mysql_fetch_array($sql1))
  {
	$llnr = $row['IDStudent'];
	$klas = $row['Klas'];
	$llnaam = $row['Achternaam'] . ", " . $row['Voornaam']; 
	$commentaar = $row['Commentaar'];
	$hand= "&nbsp;";
	
	if($commentaar == "")
	{
	 $commentaar = "&nbsp;";	
	}
	if($klas == "")
	{
	  $klas = "&nbsp;";	
	}
		
	echo '
		<td height="30">'.$llnr.' </td>
		<td width="200" height="30">'.$llnaam.' </td>
		<td width="150" height="30"> '.$klas.' </td>
		<td width="550" height="30"> '.$commentaar.' </td>
		<td width="200" height="30"> '.$hand.' </td>
		</tr>';		
  }

  echo '<tr><td style="border:0px;" colspan="4"><b>Niet ingeschreven, geactiveerd of reserve, wel aanwezig: ('.$num2.')</b>';
  
  while ($row = mysql_fetch_array($sql2))
  {

	$llnr = $row['IDStudent'];
	$klas = $row['Klas'];
	$llnaam = $row['Achternaam'] . ", " . $row['Voornaam'];
	$commentaar = $row['Commentaar']; 
	$hand= "&nbsp;";
	
	if($commentaar == "")
	{
	 $commentaar = "&nbsp;";	
	}	
	if($klas == "")
	{
	  $klas = "&nbsp;";	
	}
	
	echo '
		<tr>
		<td height="30">'.$llnr.'</td>
		<td width="200" height="30">'.$llnaam.'</td>
		<td width="150" height="30">'.$klas.'</td>
		<td width="550" height="30">'.$commentaar.'</td>
		<td width="200" height="30">'.$hand.'</td>
		</tr>';
  }

  for ($i = 0; $i <2; $i++)
  {
    echo '
		<TR>
		  <TD height="30"> &nbsp; </TD>
		  <TD height="30"> &nbsp; </TD>
		  <TD height="30"> &nbsp; </TD>
		  <TD height="30"> &nbsp; </TD>
		  <TD height="30"> &nbsp; </TD>
		</TR>
		';
  }
}
?>