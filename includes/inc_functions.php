<?php
/**
* Functies in deze pagina: 
*      isTijd($sWaarde), open_page($pagina) , getDaysInWeek ($weekNumber, $year) , maandNaam($nummer) , createRandomURL() , quote_smart($value) ,
*      datum() , fetch_rows($week, $jaar, $locatie) , fill_td($week, $jaar, $locatie) , switch_day($row) , switch_day_full($row) ,
*      search($search) , get_search_query($keywords,$sort_column,$search_columns,$search_method) , send_mail($email,$message,$activity)
* class:  email
*/

/**
* Een methode om te kijken of een waarde een geldige tijd id 00:00
* @param String $sWaarde
* @return boolean
*/
function isTijd( $sWaarde ) {
    if( !empty($sWaarde) ) {
        return preg_match('/^[0-2]{1}[0-9]{1}+:[0-5]{1}[0-9]{1}+-[0-2]{1}[0-9]{1}+:[0-5]{1}[0-9]{1}$/', $sWaarde);
    }
    else {
        return false;
    }
}

##functie om de juiste pagina te openen op de index pagina
function open_page($pagina)
{
  $text = "SELECT Titel,Inhoud FROM tblpagina WHERE IDPagina='".quote_smart($pagina)."'";
  $result = mysql_query($text);
  $numrows = mysql_num_rows($result);	
 
  if ($numrows == 0)
  {
    echo '<div id="head">Fout!</div>
		  <div id="main">Pagina '.$pagina.' kon niet gevonden worden.</div>' ;
  }
  else
  {
		$txt = mysql_fetch_assoc($result);
		
	  echo '<div id="head">'.$txt['Titel'].'</div>';
	  echo '<div id="main">'.$txt['Inhoud'].'</div>';
	
  }
}

/**
* Datum uit week nummer, jaar en dag halen.
* @param 
* @return array
*/
function getDaysInWeek ($weekNumber, $year) {
  // Count from '0104' because January 4th is always in week 1
  // (according to ISO 8601).
  $time = strtotime($year . '0104 +' . ($weekNumber - 1)
                    . ' weeks');
  // Get the time of the first day of the week
  $mondayTime = strtotime('-' . (date('w', $time) - 1) . ' days',
                          $time);
  // Get the times of days 0 -> 6
  $dayTimes = array ();
  for ($i = 0; $i < 7; ++$i) {
    $dayTimes[] = strtotime('+' . $i . ' days', $mondayTime);
  }
  // Return timestamps for mon-sun.
  return $dayTimes;
}
/*
    Hier de gegevens uit de database halen en gebruiken.
$dayTimes = getDaysInWeek($row['Week'], $row['Jaar']);

	dag nummer - 1 omdat de array bij 0 begint.
  echo strftime(' %d-%m-%Y', $dayTimes[$row['Dag']-1]) . "";
*/

/**
* Maand naam terug geven bij maand nummer. De normale php functio date doet dat in het engels.
* @param $nummer, maand nummer
* @return String
*/
function maandNaam($nummer){
// 
	$longMonths = array('januari', 'februari', 'maart', 'april', 'mei', 'juni', 
					  'juli', 'augustus', 'september', 'oktober', 'november', 'december');
	return $longMonths[$nummer-1];
}

/**
* Een functie om de string te controleren voor het de database in gaat
* @param String Random string maken
* @return String
*/
function createRandomURL() 
{
  $chars = "abcdefghijkmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
  srand((double)microtime()*1000000);
  $i = 0;
  $pass = '' ;

  while ($i <= 30) 
  {
    $num = rand() % 61;
    $tmp = substr($chars, $num, 1);
    $pass = $pass . $tmp;
    $i++;
  }

  return $pass;
} 

/**
* Een functie om de string te controleren voor het de database in gaat
* @param String $value
* @return String
*/
function quote_smart($value) {
	if (get_magic_quotes_gpc()) {
		$value = stripslashes($value);
	}
	if(version_compare(phpversion(),"4.3.0") == "-1") {
		return mysql_escape_string($value);
	} else {
		return mysql_real_escape_string($value);
	}
}

/**
* Huidige datum laten zien
* @return String met huidige datum en week nummer.
*/
function datum()
{
  //Alle maanden in de array
  $strDate = time();

  $longMonths = array('januari', 'februari', 'maart', 'april', 'mei', 'juni', 
					  'juli', 'augustus', 'september', 'oktober', 'november', 'december');
  $intWeek	=	date('W', $strDate); ##weeknummer
  $intDay 	= 	date('j', $strDate); ##Dagnummer
  $intMonth 	= 	date('n', $strDate); ##Maandnummer
  $intYear 	= 	date('Y', $strDate); ##Jaar
  $datum = ("- ".$intDay. " ". $longMonths[$intMonth-1]. " ".$intYear." -");

  echo ("<p class='subsection'>$datum
	     <br>\n  week $intWeek </p>");
}

/**
* functie om de week nummer op te vragen
* @param String $week
* @return String
*/

function fetch_rows($week, $jaar, $locatie)
{

  $week= $_GET['week'];
  $jaar= $_GET['jaar'];

  //$result = mysql_query("SELECT * FROM tblactiviteiten WHERE Week='$week' AND Jaar='$jaar' AND AArchief = '0' AND Animo='0' ORDER BY Dag"); 
  //$numrows=mysql_num_rows($result);	
  
  if ( ctype_digit($week) && ctype_digit($jaar) && ctype_digit($locatie) )
  {
    echo '<div id="head">Week '.$week.' - '.$jaar.'</div>';
	$bool = 1;

	fill_td($week, $jaar, $locatie); ## functie vul TD's
	echo '</div>';
	
  }
  else 
  { 
    echo("<div id='head'>Fout!</div>\n
		  <div id='main'> Er is geen weeknummer opgegeven!</div>");
  }
}

function fill_td($week, $jaar, $locatie)
{
  $blank = date('w') ; 
  
	
  echo '<div id="main">';
  // Locatie 3 betekent alles
  if ( $locatie == 3 ) {
  $query = "SELECT *,
					 		DATE_FORMAT(datum, '%d-%m-%Y') AS show_datum
					 		FROM tblactiviteiten 
	  				      RIGHT JOIN tblgever on tblactiviteiten.IDGever = tblgever.IDGever
	  				      LEFT JOIN tblsoort on tblactiviteiten.IDSoort = tblsoort.IDSoort
	  				      LEFT JOIN tblniveau on tblactiviteiten.IDNiveau = tblniveau.IDNiveau
	  				      LEFT JOIN tblopleiding on tblactiviteiten.IDOpleiding = tblopleiding.IDOpleiding
						  WHERE Animo = '0' AND AArchief = '0' AND Week = '".$week."' AND Jaar = '".$jaar."'
						  ORDER  BY Dag, Tijd";  
  }
  else {
  $query = "SELECT *,
					 		DATE_FORMAT(datum, '%d-%m-%Y') AS show_datum
					 		FROM tblactiviteiten 
	  				      RIGHT JOIN tblgever on tblactiviteiten.IDGever = tblgever.IDGever
	  				      LEFT JOIN tblsoort on tblactiviteiten.IDSoort = tblsoort.IDSoort
	  				      LEFT JOIN tblniveau on tblactiviteiten.IDNiveau = tblniveau.IDNiveau
	  				      LEFT JOIN tblopleiding on tblactiviteiten.IDOpleiding = tblopleiding.IDOpleiding
						  WHERE (Plaats = '".$locatie."' 
									OR Plaats = '2')
						  AND Animo = '0' AND AArchief = '0' AND Week = '".$week."' AND Jaar = '".$jaar."'
						  
						  ORDER  BY Dag, Tijd";
  }
  $sql = mysql_query($query);
  
  $numrows=mysql_num_rows($sql);
  
  if (!empty($numrows))
  {
    echo '
	     <table id="tabel">
		   <th>Dag</th>
		   <th>Titel</th>
		   <th>Type</th>
		   <th>Niveau</th>
		   <th>Tijd</th>
		   <th>Lokatie</th>
		   <th>Docent</th>
		   <th>Limiet</th>
		   <th title="Aantal aanmeldingen">Aan..</th>
		 </tr>
		 ';	

    while ($row= mysql_fetch_array($sql))
	{
	  $id = $row['IDActiviteit'];
	  $Titel = $row['Titel'];
	  $Type = $row['Soort'];
	  $Niveau = $row['Niveau'];
	  $Tijd = $row['Tijd'];
	  $Lokaal = $row['Lokaal'];
	  $voornaam = substr($row['gVoornaam'],0,1);
	  $Gever = $row['Naam'];
	  if ( $row['onbeperkt'] == 1 ) {
		  $Limiet = 'geen';
	  }
	  else {
	  	  $Limiet = $row['Limiet'];
	  }
	  $info = $row['Info'];
	  
	  $sql2 =mysql_query("SELECT * FROM tblinschrijvingen
				          WHERE IDActiviteit = '".$id."' AND Activated = '1'");

	  switch($row['Plaats'])
	  {
	    case 0: $plaats = "G";break;
		case 1: $plaats = "Z";break;
		case 2: $plaats = "E";break;
	  }
	  
	  $aanmeldingen = mysql_num_rows($sql2);
	  
	  // -- Genereer de dag naam -- //
	  switch ($row['Dag'])
	  {
	    case 1: $dag = 'Maandag';break;
	    case 2: $dag = 'Dinsdag';break;
	    case 3: $dag = 'Woensdag';break;
        case 4: $dag = 'Donderdag';break;
	    case 5: $dag = 'Vrijdag';break;
	    case 6: $dag = 'Zaterdag';break;
	    case 7: $dag = 'Zondag';break;
	    default:$dag = 'Geen';break;
	  }
  
      $uur = date("H");
      $actuur = substr("$Tijd",0,2);

	  if ($actuur -2 <= $uur && $row['Dag'] <= $blank && $row['Week'] <= date('W') && $row['Jaar'] <= date('Y') 
                  || $row['Aanmelden'] ==0)
	  {	
	    $url = '<a href="index.php?page=infoact&actid='.$id.'" style="color:red;">'.$Titel.'</a>';
	  }
	  elseif ($actuur -2 <= $uur && $row['Dag'] <= $blank && $row['Week'] <= date('W') && $row['Jaar'] <= date('Y'))
	  {
	    $url = '<a href="index.php?page=infoact&actid='.$id.'" style="color:red;">'.$Titel.'</a>';
	  }
      elseif ($actuur -2 <= $uur && $row['Dag'] >= '6' && $row['Week'] <= date('W') && $row['Jaar'] <= date('Y'))
      {
	    $url = '<a href="index.php?page=infoact&actid='.$id.'" style="color:red;">'.$Titel.'</a>';
      }
	  else
      {
	    $url = '<a href="index.php?page=infoact&actid='.$id.'">'.$Titel.'</a>';
	  }
	  
	    if(empty ($Titel))
		{ 	
		  $Titel="&nbsp;";
		}
		
		if(empty ($Type))
		{ 		
		  $Type="&nbsp;";
		}
		
		if(empty ($Niveau))
		{ 	
		  $Niveau="&nbsp;";
		}
		
		if(empty ($Tijd))
		{ 		
		  $Tijd="&nbsp;";
		}
		
		if(empty ($Lokaal))
		{ 	
		  $Lokaal="&nbsp;";
		}
		
		/*
			$dayTimes = getDaysInWeek($row['Week'], $row['Jaar']);
			'.strftime(' %d-%m-%Y', $dayTimes[$row['Dag']-1]).'
		*/
			
		

		echo '<td>'.$dag.'<br />';
		if ( $row['show_datum'] != '00-00-0000' ) {
			 echo $row['show_datum'];
		}
		echo '</td>
			  <td>'.$url.'</td>
			  <td>'.$Type.'</td>
			  <td>'.$Niveau.'</td>
			  <td>'.$Tijd.'</td>
			  <td>'.$plaats.' '.$Lokaal.'</td>
			  <td>'.$voornaam.' '.$Gever.'</td>
			  <td>'.$Limiet.'</td>
			  <td title="Aantal aanmeldingen">'.$aanmeldingen.'</td>
			</tr>
			';
      }
        echo '</table>';
      }
      else
      {
        echo 'Er zijn geen resultaten gevonden voor deze week.';
      }
    }

//Dag Converter afkorting\
function switch_day($row)	{
	switch($row)	{
		case 1: $dag = 'Ma';break;
		case 2: $dag = 'Di';break;
		case 3: $dag = 'Wo';break;
		case 4: $dag = 'Do';break;
		case 5: $dag = 'Vr';break;
		case 6: $dag = 'Za';break;
		case 7: $dag = 'Zo';break;
		default:$dag = 'Niet ingesteld';break;
	}
	return $dag;
}

//Dag Converter volledige dag naam\
function switch_day_full($row)	{
	switch($row)	{
		case 1: $dag = 'Maandag';break;
		case 2: $dag = 'Dinsdag';break;
		case 3: $dag = 'Woensdag';break;
		case 4: $dag = 'Donderdag';break;
		case 5: $dag = 'Vrijdag';break;
		case 6: $dag = 'Zaterdag';break;
		case 7: $dag = 'Zondag';break;
		default:$dag = 'Niet ingesteld';break;
	}
	return $dag;
}
  
  
/**
* Zoek funtie
* $search is het zoekwoord waarop gezocht moet worden. 
*/
  function search($search)
  {
    $searchlenght = strlen($search);
	if ($searchlenght >2 )
	{
		// zoeken in de volgende kolommen
		$search_columns = array ('Info', 'Titel', 'Trefwoord');
		// query maken met speciale class
		$sq_zoek = get_search_query($search,'Jaar',$search_columns,'AND');
		
	  $SQ = mysql_query($sq_zoek);
	  $numrows = @mysql_num_rows($SQ);
	  
	  echo("<div id='head'>U zocht: $search</div>
			<div id='main'>");
	  
	  if ($numrows==0)
	  {
	    echo(" Er zijn geen resultaten gevonden in huidige of komende activiteiten.<br>\n
			   Wat u kunt doen:<br><div id='margin'>
			   - Zoek via Geadvanceerd Zoeken in archief<br>\n
			 - Controleer uw zoekterm<br>\n
			");
	  }
	  else
	  {
	    echo("<TABLE ID='tab' style=border:1px;>");
		
		  while ($row= mysql_fetch_array($SQ))
		  {
			if ( $row['AArchief'] == 1 ) {
				// Achief niet weergeven. in query verwerken lukte niet :s
			}
			else {
  
			$ID = $row['IDActiviteit'];
			$Titel = $row['Titel'];
			$Info = $row['Info'];
			$Type = $row['Soort'];
			$Week = $row['Week'];
			$Jaar = $row['Jaar'];
			$Tijd = $row['Tijd'];
			
			$dag = switch_day($row['Dag']);
			//Limiteer de infotekst
			if($Info =="<br>" || ($Info =="<BR>") || (empty($Info)))
			{
			  $Info="Geen informatie opgegeven";
			}
			else
			{
			  if(strlen($Info) >= 50)
			  {
				$Info = $Info." ";
				$Info = substr($Info,0,50);
				$Info = substr($Info,0,strrpos($Info,' '));
				$Info = $Info."...";
			  }
			}
			$blank = date('w') ;
			$uur = date("H");
			$actuur = substr("$Tijd",0,2);
	
			if ($actuur -2 <= $uur && $row['Dag'] <= $blank && $row['Week'] <= date('W') && $row['Jaar'] 
						<= date('Y') || $row['Aanmelden'] == 0)
			{
			  $URI = "<B><a href='index.php?page=infoact&actid=$ID' style=color:red;>$Titel</a></B>";
			  $OI = "<font color='red'>Type: $Type</Font></TD>
				 <TD><font color='red'>Jaar: $Jaar</Font></TD>
					 <TD><font color='red'>Week: $Week</Font></TD>
					 <TD><font color='red'>Dag: $dag</Font>";
			}
			elseif ($actuur -2 <= $uur && $row['Dag'] <= $blank && $row['Week'] <= date('W')
					&& $row['Jaar'] <= date('Y'))
			{
			  $URI = "<B><a href='index.php?page=infoact&actid=$ID' style=color:red;>$Titel</a></B>";
			  $OI = "<font color='red'>Type: $Type</Font></TD>
				 <TD><font color='red'>Jaar: $Jaar</Font></TD>
					 <TD><font color='red'>Week: $Week</Font></TD>
					 <TD><font color='red'>Dag: $dag</Font>";
			}
					elseif ($actuur -2 <= $uur && $row['Dag'] >= $blank && $row['Week'] <= date('W') 
							&& $row['Jaar'] <= date('Y'))
					{
			  $URI = "<B><a href='index.php?page=infoact&actid=$ID' style=color:red;>$Titel</a></B>";
			  $OI = "<font color='red'>Type: $Type</Font></TD>
				 <TD><font color='red'>Jaar: $Jaar</Font></TD>
					 <TD><font color='red'>Week: $Week</Font></TD>
					 <TD><font color='red'>Dag: $dag</Font>";
			}
			else
			{
			  $URI = "<B><a href='index.php?page=infoact&actid=$ID'>$Titel</a></B>";
			  $OI = "Type: $Type</TD><TD>Jaar: $Jaar</TD><TD>Week: $Week</TD><TD>Dag: $dag";
			}
			echo("<TR>
					<TD colspan='2'>Activiteit: $URI</TD> 
					<TD colspan='1'> $OI </TD>
				  </TR>
				  <TR>
					<TD colspan='6'>$Info</TD>
				  </TR>
				");
		  
		  
		    }
		  }
		echo("</TABLE>");
	  }
	 }
	 else
	 {
	   echo("<div id='head'>Fout!</div>
			 <div id='main'>
			   De zoekterm -<i> $search </i>- bevat te weinig tekens.<br>\n
			   Minimaal vereist aantal tekens is: 3.</div>");
  }
}

/**
*  Functie om zoek query te maken.
*  Wordt gebruikt in de search functie hierboven.
*/
function get_search_query($keywords,$sort_column,$search_columns,$search_method) {
  /* $search_columns moet een array zijn */
  /* $search_method kan OR of AND        */

  /* Verwijder onnodige spaties en +/- tekens uit de string */
  $keywords          = trim($keywords);
  $keywords          = ereg_replace ('\+', "", $keywords);
  $keywords          = ereg_replace ('\-', "", $keywords);

  while (ereg('  ',$keywords)) {
    $keywords        = ereg_replace('  ',' ',$keywords);
  }

  /* Converteer de keywords naar een array en stel variabelen in */
  $keywords          = explode(' ',$keywords);
  $num_keywords      = count($keywords);
  $num_searchcolumn  = count($search_columns);
  $search_method     = strtoupper($search_method);

  /* Maak de query */
  $query = "SELECT * FROM tblactiviteiten 
  			LEFT JOIN tblsoort on tblactiviteiten.IDSoort = tblsoort.IDSoort
	  	             LEFT JOIN tblopleiding on tblactiviteiten.IDOpleiding = tblopleiding.IDOpleiding 	
				WHERE AArchief = 0 AND";

  for ($i_searchcolumn=0; $i_searchcolumn<$num_searchcolumn; $i_searchcolumn++) {
      $query .= " (";

      for ($i_keyword=0; $i_keyword<$num_keywords; $i_keyword++) {
        if ($i_keyword == 0) {
          $column = $search_columns[$i_searchcolumn];
        }
        else {
          $query .= "$search_method ";
        }
        $query .= "$column LIKE '%$keywords[$i_keyword]%' ";
      }

      $query .= ") OR";
  }
  $query .= "DER BY '$sort_column' DESC, Week DESC,Dag ASC";

  return $query;
}

/**
*  Email verzend functie van eerdere versie van kennisbus
*
*/
function send_mail($email,$message,$activity)
{
  if(strtoupper(substr(PHP_OS,0,3)=='WIN'))
  {
    $eol="\r\n";
    $sol="\n";
  }
  elseif(strtoupper(substr(PHP_OS,0,3)=='MAC'))
  {
    $eol="\r";
  }
  else
  {
    $eol="\n";
  }
  $Servername = "PHPMAILSERVER";
  $Momentn = mktime().".".md5(rand(1000,9999));
  $headers      = 'From: Administrator Kennisbus.nl <noreply@kennisbus.nl>'.$eol;
  $headers      .= "Message-ID: <".$Momentn."@".$Servername.">".$eol;
  $headers      .= 'Date: '.date("r").$eol;
  $headers      .= 'Sender-IP: '.$_SERVER["REMOTE_ADDR"].$eol;
  $headers      .= 'X-Mailser: iPublications Adv.PHP Mailer 1.6'.$eol;
  $headers      .= 'MIME-Version: 1.0'.$eol;
  $headers      .= "Content-Type: text/html; charset=iso-8859-1".$eol;
  $headers      .= "Content-Transfer-Encoding: 8-bit".$eol.$eol;
  mail($email,"Je bent aangemeld voor de activiteit ".$activity,$message,$headers);
}


/**
*  Nieuwe email verzend functie
*
*/
class email {

    /*
     * Variabelen
     */
    var $ontvanger_naam;
    var $ontvanger_email;
    var $verzender_naam;
    var $verzender_email;
    var $onderwerp;
    var $bericht;
    var $bijlage;
    var $boundary;
    var $bijlage_aanwezig;
    var $huidige_bijlage;

    /*
     * Ontvanger instellen
     */
    function set_ontvanger($naam, $email)
    {
        $this->ontvanger_naam  = $naam;
        $this->ontvanger_email = $email;
    }

    /*
     * Verzender instellen
     
    function set_verzender($naam, $email)
    {
        $this->verzender_naam  = $naam;
        $this->verzender_email = $email;
    }*/

    /*
     * Onderwerp instellen
     */
    function set_onderwerp($onderwerp)
    {
        $this->onderwerp = $onderwerp;
    }

    /*
     * Berciht instellen
     */
    function set_bericht($bericht)
    {
        $this->bericht = $bericht;
    }

    /*
     * deze functie verstuurt het bericht
     */
    function send()
    {
        $headers = "From: Administrator Kennisbus.nl <noreply@kennisbus.nl>\r\n";
        $headers .= "Reply-To: Administrator Kennisbus.nl <noreply@kennisbus.nl>\r\n";
        $headers .= "X-Mailer: PHP/.".phpversion()."";
        $headers .= "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1";

        /*
         * De email verzenden
         */
        if ( mail($this->ontvanger_naam." <".$this->ontvanger_email.">", $this->onderwerp, $this->bericht, $headers) ) {
			return true;
		}
		else {
			return false;
		}
    }
}
?>