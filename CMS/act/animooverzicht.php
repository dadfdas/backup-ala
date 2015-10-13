<?php
/**
*  Animo overzicht
*  Deze pagina bevat de functie: overzicht($SQL)
*  Deze pagina gebruikt de functie:
*    verwijder_activiteit()
*    staan in: inc_functions_beheer.php
*/

  //$result = mysql_query("SELECT * FROM tblactiviteiten where Animo = '1' ORDER BY IDActiviteit");
  //$numrows = mysql_num_rows($result);	

  echo '<div id="head">Animo</div><br />';
  
	if ( isset($_GET['verwijderen']) && $_GET['verwijderen'] == "y" && ctype_digit($_GET['id']) ) {
		if ( $_SESSION['usertype'] == "Admin" ) {
			verwijder_activiteit($_GET['id'],0,0) ;		
		}
		else {
			echo "Niet de juiste rechten om te mogen verwijderen.";
		}
  	}
	else {
		  $bool = 1;
		  
		  if ($_SESSION['IDType'] == 1 )
		  {
			$SQL = mysql_query("SELECT 
							   		act.IDActiviteit,
									act.Titel,
							   		act.Animo,
									act.Limiet,
									act.Lokaal,
									act.AArchief,
									act.Plaats,
									act.onbeperkt,
									so.Soort,
									opl.Opleiding,
									gev.Naam,
									gev.gVoornaam,
									niv.Niveau,
									so.Soort
								FROM 
							   		tblactiviteiten AS act
								LEFT JOIN tblgever AS gev ON act.IDGever = gev.IDGever
								LEFT JOIN tblniveau AS niv ON act.IDNiveau = niv.IDNiveau
								LEFT JOIN tblopleiding AS opl ON act.IDOpleiding = opl.IDOpleiding
								LEFT JOIN tblsoort AS so ON act.IDSoort = so.IDSoort
								WHERE
									act.Animo = 1
								ORDER BY
									act.IDActiviteit
								"); //
		  } 
		  else 
		  {
			$gever = $_SESSION['IDGever'];
			$SQL = mysql_query("SELECT 
							   		act.IDActiviteit,
									act.Titel,
							   		act.Animo,
									act.Limiet,
									act.Lokaal,
									act.AArchief,
									act.Plaats,
									act.onbeperkt,
									so.Soort,
									opl.Opleiding,
									gev.Naam,
									gev.gVoornaam,
									niv.Niveau,
									so.Soort
								FROM 
							   		tblactiviteiten AS act
								LEFT JOIN tblgever AS gev ON act.IDGever = gev.IDGever
								LEFT JOIN tblniveau AS niv ON act.IDNiveau = niv.IDNiveau
								LEFT JOIN tblopleiding AS opl ON act.IDOpleiding = opl.IDOpleiding
								LEFT JOIN tblsoort AS so ON act.IDSoort = so.IDSoort
								WHERE
									act.Animo = 1
								AND 
									gev.IDGever = '".$gever."'
								ORDER BY
									act.IDActiviteit");
			
		  }	
	  overzicht($SQL); ## functie vul TD's		  
	}
	echo '</div>';

	function overzicht($sql)
	{
	  $strDate = time();
	  echo '<div id="main">';			
	  $numrows=mysql_num_rows($sql);
	
	  $tijd = date("H:i");
	
	  if (!empty($numrows))
	  {
		echo '
			 <table id="tab">
			   <th>Titel</th>
			   <th>Type</th>
			   <th>Niveau</th>
			   <th>Lokaal</th>
			   <th>Docent</th>
			   <th>Limiet</th>
			   <th>Aangemeld</th>
			   <th>Presentie</th>
			   <th>Maak Activiteit</th>
			   <th>Verwijderen</th>
			  </tr> 
			 ';					
	
		while ($row= mysql_fetch_assoc($sql))
		{
		  $id = $row['IDActiviteit'];
		  $Titel = $row['Titel'];
		  $Type = $row['Soort'];
		  $Niveau = $row['Niveau'];
		  $Lokaal = $row['Lokaal'];
		  $voornaam = substr($row['gVoornaam'],0,1);
		  $Gever = $row['Naam'];
		  $Limiet = $row['Limiet'];
		  $Archief = $row['AArchief'];
		  
		  $SQL2 =mysql_query("SELECT * FROM tblinschrijvingen WHERE tblinschrijvingen.IDActiviteit = '" . quote_smart($id) . "'
							  AND tblinschrijvingen.Activated = '1' ORDER BY Nummer ASC") or die (mysql_error());	
		  $nums1 = mysql_num_rows($SQL2);
		  
		  $SQL3 =mysql_query("SELECT * FROM tblinschrijvingen WHERE tblinschrijvingen.IDActiviteit = '" . quote_smart($id) . "'
							  AND tblinschrijvingen.Activated = '0' ORDER BY Nummer ASC") or die (mysql_error());	
		  $nums3 = mysql_num_rows($SQL3);
	
		  $nums4 = '';
		  if ($nums3 != 0) {
			$nums4 .= " (".$nums3.")";
		  }

		  $ourl = '<u>
					 <a href="#" onclick="';
			$ourl .= "javascript:popUp('act/aanmeldingen.php?actid=";
			$ourl .= $id;
			$ourl .= "')";
				 $ourl .= '">
				   <img src="images/lijst.png" border="0">
				 </a>
			   </u>';
	
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
		  if ( $row['onbeperkt'] == 1 ) {
			  $Limiet = 'N.v.t.';
		  }
		  
		  switch ($row['Plaats'])
		  {
			case 0: $plaats = "G";break;
			case 1: $plaats = "Z";break;
			case 2: $plaats = "E";break;
		  }
	
			echo '
				 <tr>
				   <td>
					 <U>
					 <a href="index.php?page=animoaanpassen&actid='.$id.'">'.$Titel.'</a>
					 </U>
				   </td>
				   <td>'.$Type.'</td>
				   <td><center>'.$Niveau.'<center></td>
				   <td>'.$Lokaal.'</td>
				   <td>'.$voornaam.' '.$Gever.'</td>
				   <td><center>'.$Limiet.'</center></td>
				   <td><center>'.$nums1.' '.$nums4.'</center></td>
				   <td><center>'.$ourl.'</center></td>
				 ';
			
			if($_SESSION['IDType'] == 1)
			{
		  echo '<td>
		          <center>
				    <a href="index.php?page=actaanpassen&actid='.$id.'" title="Omzetten naar activiteit">
					 <img src="images/aanpassen.ico" height="20" width="20" alt="Omzetten naar activiteit" border="0">
					</a>
				  </center>
				</td>
			   ';
			}
			
			echo '
				   <td>
					 <center>
					   <a href="index.php?page=animooverzicht&verwijderen=y&id='.$id.'" onCLick="return confirm(';
																												echo "'Weet u het zeker? De aanmeldingen worden ook verwijderd.')";
					echo '" title="Verwijderen?">
						 <img src="images/delete_icon.gif" alt="Verwijderen?" border="0">
					   </a>
					 </center>
				   </td>		
				 </tr>
				 ';
			
			$nums1 = "";
			$nums4 = "";
		  }
		echo '</table>';
	  }
	  else
	  {
		echo 'Er zijn geen animo resultaten gevonden.'; 
	  }
	}
?>