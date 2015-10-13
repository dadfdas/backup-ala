<div id='main'>
<?php
/**
* 	Laat informatie zien van een activiteit met daarbij aanmeld mogelijkheid.
*  Deze pagina gebruikt de functie:
*    send_mail(), quote_smart(), createRandomURL(), switch_day_full()
*    staat in: inc_functions.php
*/
if (!isset($_POST['aanmelden']))
{
  $actid = $_GET['actid'];
  
  $sql =mysql_query("SELECT t1.*, DATE_FORMAT(t1.datum, '%d-%m-%Y') AS show_datum, t2.* FROM tblactiviteiten AS t1, tblgever AS t2
		             WHERE t1.IDActiviteit='".quote_smart($actid)."' AND t1.IDGever = t2.IDGever AND t1.Animo='0'");
					 
  while($row = mysql_fetch_array($sql))
  {
    $sql2 = mysql_query("SELECT 
t1.IDActiviteit, 
t2.IDActiviteit,
t2.Activated
FROM 
tblactiviteiten AS t1,	tblinschrijvingen AS t2
WHERE t1.IDActiviteit = '".quote_smart($actid)."'
AND t1.IDActiviteit = t2.IDActiviteit
AND t2.Activated = 1");

	//display de Activiteit en de info / gever
	echo("<div id='head'>Activiteit - " .$row['Titel']. "</div>");
	echo("<div id='main'>" .$row['Info']. "<br>\n <hr align='left'>");
	$doc = $row['Bestand'];
	
	$limiet = $row['Limiet'];
	$aantal_aanmeldingen = mysql_num_rows($sql2);
	$plaatsen_over = $limiet - $aantal_aanmeldingen; 
	
	$ext = strstr ($doc, '.');
	//Plaats een icon voor het doctype
	switch ($ext)
	{
	  case ".doc": $doctype = "doc";break;
	  case ".docx": $doctype = "doc";break;
	  case ".pdf": $doctype = "pdf";break;
	  case ".xlsx": $doctype = "xls";break;
	  case ".xls": $doctype = "xls";break;
	  case ".zip": $doctype = "zip";break;
	  case ".rar": $doctype = "zip";break;
	  case ".ppt": $doctype = "ppt";break;
	  case ".pptx": $doctype = "ppt";break;
	  case ".pps": $doctype = "ppt";break;
	  case ".ppsx": $doctype = "ppt";break;
	  default: $doctype="";break;
	}
	$lengte = strlen($doc);
	$lengte2 = strlen($ext);
	$lengte3 = $lengte - $lengte2;
	$Bstnaam = substr($doc, $ext , $lengte3);
	$voornaam = substr($row['gVoornaam'],0,1);
	$id = $row['IDActiviteit'];
	$gever = $row['Naam'];
	$Tijd = $row['Tijd'];
	echo '
		<table id="main" padding="0" border="0">
		  <tr>
		    <td align="left" valign="top"> 
			  Docent: '.$voornaam.' '.$gever.'<br>';
			  
		      if (!empty ($row['Bestand']))
			  {
			    echo 'Download:
			          <a href="uploads/' .$row['Bestand']. '">'.$Bstnaam.'</a> 
					  <img src="images/'.$doctype.'.png" border="0" align="top" />
				     <br />';
		      }
					
				$dag = switch_day_full($row['Dag']);
		      
			  echo 'Week: ' . $row['Week'] . ' | Dag: '.$dag;
				if ( $row['show_datum'] == "00-00-0000" ) {
					// Deze functie wil ik nog niet gebruiken omdat deze naar mijn weten niet altijd de juiste datum geeft.
					// Het probleem zit hem vooral in het begin van het jaar. Week 1.
					// $dayTimes = getDaysInWeek($row['Week'], $row['Jaar']);
			  		// echo strftime(' %d-%m-%Y', $dayTimes[$row['Dag']-1]) . "";
				}
				else {
					echo ' '.$row['show_datum'];
				}

			  echo '</td>
			</td>
			<td rowspan="2" width="400" valign="top">
			  <div align="right">
			    <table id="aanmelden">
				  <tr>
				    <td id="aanmelden">
					  ';
					  ## controleer de dag en datum
					  $blank = date('w') ; 
					  
      				  $uur = date("H");
      				  $actuur = substr($Tijd,0,2);

	                  if ($row['Aanmelden'] == 0)
					  {
					    echo '<div align="right">Aanmelden is uitgeschakeld voor deze activiteit.</div>';
					  }
					  elseif ( $row['onbeperkt'] == 0 && $plaatsen_over == 0 ) { 
							echo '<p>Er zijn geen plaatsen meer vrij bij deze activiteit.</p>
							<p>Er was een limiet van '.$limiet.' plaatsen.';
					  }
					  elseif ($actuur -2 <= $uur && $row['Dag'] <= $blank && $row['Week'] <= date('W')
       						  && $row['Jaar'] <= date('Y'))
					  {
					    echo '<div align="right">Je kunt je niet meer aanmelden voor deze activiteit!</div>';
						    if ( $row['onbeperkt'] == 1) {
								echo 'Er zat geen limiet aan deze cursus.';	
							}
							else {
							  	echo 'Er waren nog '.$plaatsen_over.' plaatsen vrij.';
							}
					  }
                      elseif ($actuur -2 <= $uur && $row['Dag'] >= '6' && $row['Week'] >= $blank && $row['Jaar'] <= date('Y'))
                      {
					    echo '<div align="right">Je kunt je niet meer aanmelden voor deze activiteit!</div>';
							if ( $row['onbeperkt'] == 1) {
								echo 'Er zat geen limiet aan deze cursus.';	
							}
							else {
							  	echo 'Er waren nog '.$plaatsen_over.' plaatsen vrij.';
							}
                      }
					  else
					  {
					    echo '
						     <form action="index.php?page=infoact&actid='.$id.'" method="post">
							   <div align="left">
							     <div id="head">Aanmelden</div>
				                         Studentnummer:<br> <input type="text" maxlength="5" size="10" name="stnr"><br>
				                         Commentaar:<br>
				                         <input type="text" name="commentaar" maxlength="20" size="30"><br> 
							             <input type="submit" align="middle" value="Aanmelden" name="aanmelden">';
							if ( $row['onbeperkt'] == 1) {
								echo '<p>Er is geen limiet aan plaatsen.</p>';	
							}
							else {
								echo '<p>Er zijn nog '.$plaatsen_over.' van de '.$limiet.' plaatsen vrij!</p>';
							}
					  }
					  ## hier gaan we weer verder..
					  echo '
					       </td>
						 </form>
					   </tr>
					 </table>';
					 
			  echo "</tr><tr>
					<td width='300'>
					  <a href='#' onclick=\"history.go(-1)\">Vorige pagina</a>
					</td>
				  </tr>
				</table>
				  ";
  }
}
else	{
  if (!empty($_POST['stnr']) )
  {
    $actid = $_GET['actid'];
	$stnr = $_POST['stnr'];
	$commentaar = $_POST['commentaar'];
	$studentSQL = mysql_query("SELECT * from tblstudent where IDStudent= '".quote_smart($stnr)."'");
	
	// Bestaat dit student nummer?
	if ( mysql_num_rows($studentSQL) ) { 
	
		$row = mysql_fetch_array($studentSQL);
		// Controleer of student in archief staat.
		if ($row['Archief']==1) {
		  echo 'Het is niet toegestaan aan te melden met dit leerlingnummer. <br />
				Is dit onterecht? Neem dan contact op met de administrator';
		}
		else
		{
		  $ckSQL = mysql_query("SELECT * FROM tblinschrijvingen WHERE IDStudent = '".quote_smart($stnr)."' AND IDactiviteit = '".quote_smart($actid)."'");
			
		  if ( !mysql_num_rows($ckSQL) ) {
		  
			$actSQL = mysql_query("SELECT * FROM tblactiviteiten WHERE IDActiviteit='".quote_smart($actid)."'");
			
			$rurl = createRandomURL();
			$url = '<a href="http://www.kennisbus.nl/?page=accept&code='.$rurl.'" title="Accepteer URL">
					http://www.kennisbus.nl/?page=accept&code='.$rurl.'</a>';
			// Genereer de Email
			$row = mysql_fetch_assoc($actSQL);
			
			  $title = $row['Titel'];
			  $dag = $row['Dag'];
			  $week = $row['Week'];

			  mysql_query("INSERT INTO `tblinschrijvingen` 
						 ( `IDActiviteit` , `IDStudent` , `Nummer`, `Commentaar`, `Code` , `Activated` ) 
						   VALUES ('".$actid."', '".$stnr."', '0', '".$commentaar."', '".$rurl."', '0')");

			  $srow = mysql_fetch_assoc($studentSQL);
	
				$Voornaam = $srow['Voornaam'];
				$Email = $srow['Email'];
			
				$dag = switch_day_full($dag);
								
			  //Plaats de code, de student en het ActID in de databse...
			  $bericht="					
			  <html> 
				<head> 
				  <title>Aanmelding op de Kennisbus.nl</title> 
				</head> 
				<body>
				  <font face='arial' size='2'>					
					".$Voornaam.",
					<BR>
					Je hebt je aangemeld voor de activiteit '".$title."' op ".$dag." in week ".$week.".<br>
					Als dit correct is, activeer dan je aanmelding door middel van de volgende URL:<br>
					---> ".$url." <---<br><br>
					Met vriendelijke groeten,<br>
					Het kennisbus-team.<br><br>
					<BR>
					<B>PS: Verwijder deze email niet(!)<BR>
					Mocht je je willen uitschrijven voor een activiteit, klik dan op deze:<BR>
					<a href='http://www.kennisbus.nl/?page=uitschrijven&code=".$rurl."' title='Uitschrijven'>
					http://www.kennisbus.nl/?page=uitschrijven&code=".$rurl."</a> 
					<BR> link om je weer uit te schrijven voor de activiteit.
					<hr width='200px' color='green'>
					<sub><i>Als jij deze aanmelding niet zelf hebt gedaan, negeer dan deze email!</i></sub>
				  </font>
				</body> 
			  </html> 
			  ";
			  send_mail($Email,$bericht,$title);
			  echo("<HR width='200px'>Er is een bericht gestuurd naar ".$Voornaam.", op het email adres: ".$Email.".<BR>
				   Het bericht is verstuurd naar je 
				   <a href='http://mail.google.com/a/ict-idcollege.nl/' target='_blank' 
				   title='Ga naar je ICT IDcollege-account!'>ICT-IDcollege</a> gmail adres.");
			  echo "<meta http-equiv='REFRESH' content='100;URL=index.php'>";
		  }
		  else  {
			
				$row = mysql_fetch_array($ckSQL);

				if ($row['Activated'] ==0)	{
				  echo ("Dit studentnummer staat al ingeschreven bij deze activiteit, maar is nog niet geactiveerd. 
						 Controleer je <a href='http://mail.google.com/a/ict-idcollege.nl/' target='_blank' 
						 title='Ga naar je ICT IDcollege-account!'>
						 ICT-IDcollege.nl</a> email!<BR />\n
						 <meta http-equiv='REFRESH' content='2;URL=index.php?page=infoact&actid=".$actid."'>");
				}
				else
				{
				  echo("Dit studentnummer staat al ingeschreven bij deze activiteit
						<meta http-equiv='REFRESH' content='2;URL=index.php?page=infoact&actid=".$actid."'>
						");
				}
		  }
		}
	}
	else {
		echo 'Dit leerling nummer staat niet in de database.<br /><br />
				In 5 seconden gaat u terug naar de vorige pagina.
				<meta http-equiv="REFRESH" content="5;URL=index.php?page=infoact&actid='.$actid.'">';	
	}
  }
}
?>