<div id='main'>
<?php
/**
* 	Laat informatie zien voor een animo activiteit met daarbij aanmeld mogelijkheid.
*  Deze pagina gebruikt de functie:
*    send_mail(), quote_smart(), createRandomURL(), switch_day_full()
*    staat in: inc_functions.php
*/
if ( !isset($_POST['aanmelden']) )
{
  $actid = $_GET['actid'];
  
  $sql =mysql_query("SELECT t1.*, t2.* FROM tblactiviteiten AS t1, tblgever AS t2
		             WHERE t1.IDActiviteit='".quote_smart($actid)."' AND t1.IDGever = t2.IDGever");
  while($row = mysql_fetch_array($sql))
  {
    $sql2 =mysql_query("SELECT t1.*, t2.*, t3.* FROM tblactiviteiten AS t1, tblinschrijvingen AS t2, tblstudent AS t3
				        WHERE t1.IDActiviteit = '".quote_smart($actid)."' AND t1.IDActiviteit = t2.IDActiviteit
				        AND t2.IDStudent = t3.IDStudent AND t2.Activated = 1");


	//display de Activiteit en de info / gever
	echo '<div id="head">Animo - ' .$row['Titel']. '</div>';
	echo '<div id="main">'.$row['Info']. '<br /> <hr align="left">';

	$limiet = $row['Limiet'];
	$aantal_aanmeldingen = mysql_num_rows($sql2);
	$plaatsen_over = $limiet - $aantal_aanmeldingen; 
		 
	$voornaam = substr($row['gVoornaam'],0,1);
	$id = $row['IDActiviteit'];
	$gever = $row['Naam'];
	echo '
		<table id="main" padding="0" border="0">
		  <tr>
		    <td align="left" valign="top"> 
			  Docent: '.$voornaam .' '. $gever.'<br>';
		      echo '</td>
			</td>
			<td rowspan="2" width="400" valign="top">
			  <div align="right">
			    <table id="aanmelden">
				  <tr>
				    <td id="aanmelden">
					  ';
	                  if ($row['Aanmelden'] == 0)
					  {
					    echo '<div align="right">Aanmelden is uitgeschakeld voor deze activiteit.</div>';
					  }
					  elseif ( $row['onbeperkt'] == 0 && $plaatsen_over == 0 ) { 
							echo '<p>Er zijn geen plaatsen meer vrij bij deze activiteit.</p>
							<p>Er was een limiet van '.$limiet.' plaatsen.';
					  }
					  else
					  {
					    echo '<form action="index.php?page=infoanimo&actid='.$id.'" method="post">
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
					 </table>
				          ';
		              echo '</tr><tr>
			                <td width="300">
							  <a href="#" onclick="history.go(-1)">Vorige pagina</a>
							</td>
			              </tr>
		                </table>
		                  ';
  }
}
else
{
  if (!empty($_POST['stnr']))
  {
    $actid = $_GET['actid'];
	$stnr = $_POST['stnr'];
	$commentaar = $_POST['commentaar'];
	$studentSQL = mysql_query("SELECT * from tblstudent where IDStudent= '".quote_smart($stnr)."'");
	$row = mysql_fetch_array($studentSQL);
	if ($row['Archief']==1)
	{
	  echo ("Het is niet toegestaan aan te melden met dit leerlingnummer. <BR>
	    	 Is dit onterecht? Neem dan contact op met de administrator");}
	else
	{
	  $ckSQL = mysql_query("SELECT * FROM tblinschrijvingen where IDStudent= '".quote_smart($stnr)."' AND IDactiviteit = '".quote_smart($actid)."'");
	  $check = mysql_num_rows($ckSQL);					
	  if ($check <=0)
	  {
	    $studentSQL = mysql_query("SELECT * from tblstudent where IDStudent='".quote_smart($stnr)."'");
		$actSQL = mysql_query("SELECT * FROM tblactiviteiten where IDActiviteit='".quote_smart($actid)."'");
		$rurl = createRandomURL();
		$url = '<a href="http://www.kennisbus.nl/?page=accept&code='.$rurl.'" title="Accepteer URL">
				http://www.kennisbus.nl/?page=accept&code='.$rurl.'</a>';
		// Genereer de Email
		$row = mysql_fetch_assoc($actSQL);
		
		$title = $row['Titel'];
		
		if( mysql_num_rows($studentSQL) )
		{ 
			echo $actid;
		
		  mysql_query("INSERT INTO `tblinschrijvingen` 
					 ( `IDActiviteit` , `IDStudent` , `Nummer`, `Commentaar`, `Code` , `Activated` ) 
					   VALUES ('".$actid."', '".$stnr."', '0', '".$commentaar."', '".$rurl."', '0')");
					
			$srow = mysql_fetch_assoc($studentSQL);
			
		    $Voornaam = $srow['Voornaam'];
			$Email = $srow['Email'];

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
			    Je hebt je aangemeld voor de activiteit '".$title."'.<br>
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
		  echo("<meta http-equiv='REFRESH' content='100;URL=index.php'>");
		}
		else
		{ 
		  echo("Student nummer onbekend..
				<meta http-equiv='REFRESH' content='2;URL=index.php?page=infoanimo&actid=".$actid."'>");}
		}
		else
		{
          while ($row = mysql_fetch_array($ckSQL))
		  {
		    if ($row['Activated'] ==0)
			{
			  echo ("Dit studentnummer staat al ingeschreven bij deze activiteit, maar is nog niet geactiveerd. 
					 Controleer je <a href='http://mail.google.com/a/ict-idcollege.nl/' target='_blank' 
					 title='Ga naar je ICT IDcollege-account!'>
					 ICT-IDcollege.nl</a> email!<BR />\n
					 <meta http-equiv='REFRESH' content='2;URL=index.php?page=infoanimo&actid=".$actid."'>");
			}
			else
			{
			  echo("Dit studentnummer staat al ingeschreven bij deze activiteit
					<meta http-equiv='REFRESH' content='2;URL=index.php?page=infoanimo&actid=".$actid."'>
					");
			}
		  }
		}
	  }
	}
  }
?>