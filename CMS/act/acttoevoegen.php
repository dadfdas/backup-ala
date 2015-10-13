<?php
/**
*  Activiteit toevoegen.
*  Deze pagina bevat de functies: show_data($session,$fields) , saveit ($fields) , error_msg($msg)
*  Deze pagina gebruikt de functie:
*    selectPlaats() , selectNiveau() , selectSoort()  
*    selectGever() , selectOpleiding()
*    staan in: inc_functions_beheer.php
*/
//Controleer eerst of er al een session is (!)
if (!isset ($_SESSION['user']))
{
  die("Hack attempt");
}
else
{
  switch($_SESSION['usertype'])
  {
    case 'Admin':
	echo '<b>Activiteit Toevoegen</b>';
	
	if(isset($_POST['s1']))
	{
		
		// Maak een errorMessage array aan om de fout berichten in op te slaan
		$errorMessage = Array();
		// errorFound standaard op false
		$errorFound   = false;
	
		if ( empty($_POST['Week']) || empty($_POST['Jaar']) || empty($_POST['Dag']) ) {
			$errorMessage[] = "Week, jaar of dag is niet ingevuld.";
			$errorFound = true;
		}
		if ( empty($_POST['Titel']) ||  empty($_POST['Trefwoord']) ) {
			$errorMessage[] = "Titel of trefwoord is niet ingevuld.";
			$errorFound = true;
		}
		if ( empty($_POST['Tijd']) ) {
			$errorMessage[] = "Tijd is niet ingevuld.";
			$errorFound = true;
		}
		else {
			if ( !isTijd($_POST['Tijd']) ) {
				$errorMessage[] = "Er is een niet geldige tijd ingevoerd. Controleer format.";
				$errorFound = true;	
			}
		}
		if ( !empty($_POST['dag']) || !empty($_POST['maand']) || !empty($_POST['jaar']) ) {
			if ( !ctype_digit($_POST['dag']) || !ctype_digit($_POST['maand']) || !ctype_digit($_POST['jaar']) ) {
				$errorMessage[] = "Aatum van activiteit bestaat niet alleen uit cijfers.";
				$errorFound = true;
			}
			elseif ( strlen($_POST['dag']) != 2 || strlen($_POST['maand']) != 2 || strlen($_POST['jaar']) != 4 ) {
				$errorMessage[] = "De datum invoer is niet juist dit moet zijn: dd - mm - jjjj";
				$errorFound = true;	
			}
		}
		if ( empty($_POST['Lokaal']) ) {
			$errorMessage[] = "Lokaal is niet ingevuld.";
			$errorFound = true;
		}
		if ( isset($_POST["Aanmelden"]) ) {
			if ( empty($_POST['Limiet']) ) {
				if ( !isset($_POST['onbeperkt']) ) {
					$errorMessage[] = "Vul een limiet in.";
					$errorFound = true;
				}
			}
		}
		
	  // Error gevonden tot nu toe ?
	  if ( $errorFound ) {
			$size = count($errorMessage);
			$error_mes = '';
			
			for( $i=0; $i < $size; $i++ ) {
				$error_mes .= $errorMessage[$i].'<br />';
			}
			
			error_msg($error_mes);
	  }
	  else
	  {	
	    $fields = $_POST;
		//-------------------------------//
		if ( empty($fields['IDGever']) )
		{
		  $fields['IDGever'] = 0;
		}
			
		$actnum = mysql_query("SELECT MAX(IDActiviteit) FROM tblactiviteiten") or die (mysql_error());
		$num = mysql_fetch_row($actnum) or die (mysql_error());
		$fields['Num']= $num[0] +1;
		//Controle van de ingevoerde velden tbt de database...
		//------------------------------------------------------------------------------------------------------//
		//controleer tijds notatie------//
		$start = substr($fields['Tijd'],0,5);
		$eind = substr($fields['Tijd'],6,5);
		strtotime($start);
  		strtotime($eind);
			
		if($eind < $start)
		{ 
		  error_msg("De eindtijd is eerder dan de begintijd!");
        }
		else
		{
		  $sT = mysql_query("SELECT * FROM tblactiviteiten 
			                WHERE `Week` = '".$fields['Week']."' AND `Dag` = '".$fields['Dag']."'
			                AND `Jaar` ='".$fields['Jaar']."' AND `Lokaal` = '".$fields['Lokaal']."'
							AND Plaats = '".$fields['Plaats']."' AND AArchief = 0");

		  if( mysql_num_rows($sT) )
		  {
			// Maak een errorMessage array aan om de fout berichten in op te slaan
				$zelfdetijdgevonden = Array();
			while ( $chkT = mysql_fetch_assoc($sT) ) {

				$ostart = substr($chkT['Tijd'],0,5);
				$oeind = substr($chkT['Tijd'],6,5);
				strtotime($ostart);
				strtotime($oeind);
				//Als start > opgeslagen eind, sla op
				
				if(($eind <= $ostart))	{
					// niets doen
				}
				elseif(($oeind <= $start))	{ 
					// niets doen
				}
				else  {
					// niet opslaan
					$zelfdetijdgevonden[]   = "match";
				}
			}
			
			if ( count($zelfdetijdgevonden) >= 1 ) {
				error_msg("Pas de tijd aan!");
			}
			else {
				saveit($fields);
			}
			
		  }
		  else
		  {
		    saveit($fields);
		  }
		}
	  }
	}
    elseif(isset($_POST['cancel']))
    {
	  echo '<meta http-equiv="REFRESH" content="0;URL=index.php"> ';	
    }
	else
	{
	  $fields['Aanmelden'] = 1;
	  $fields['Week'] = date('W');
	  show_data($_SESSION['usertype'],$fields); 
	}
	break;
	
	default: echo 'U heeft geen toegang tot deze pagina.';
	break;
  }
}

//Functions --------------------------------------------------------------------------------------------------------------------------------------------------------

function show_data($session,$fields)
{
  
  $verplicht = "<a style=CURSOR:help; title='Dit veld is verplicht'><img src='images/info_icon_r.gif' border='0' height=
  '13' width='13'></a>";
  $semi = "<a style=CURSOR:help; title='Verplicht veld als controle checkbox is aangevinkt'><img src='images/info_icon.gif
  ' border='0' height='13' width='13'></a>";
  $niet = "<a style=CURSOR:help; title='Dit veld is niet verplicht'><img src='images/info_icon_g.gif' border='0' height= 
  '13' width='13'></a>";
  	
	
	if ( isset($fields['Aanmelden']) && $fields['Aanmelden'] == 1 ) {
		$chkval = 'checked="checked"';
	}
	else {
		$chkval = '';
	}
	
	if ( isset($fields['onbeperkt']) && $fields['onbeperkt'] == 1 ) {
		$chkon = 'checked="checked"';
	}
	else {
		$chkon = '';
	}
	$fields['Titel'] = ( isset ( $fields['Titel'] ) ) ? $fields['Titel'] : '';
	$fields['Trefwoord'] = ( isset ( $fields['Trefwoord'] ) ) ? $fields['Trefwoord'] : '';
	// Datums
	$fields['dag'] = ( isset ( $fields['dag'] ) ) ? $fields['dag'] : '';
	$fields['maand'] = ( isset ( $fields['maand'] ) ) ? $fields['maand'] : '';
	$fields['jaar'] = ( isset ( $fields['jaar'] ) ) ? $fields['jaar'] : '';
	
	echo '<SCRIPT LANGUAGE="JavaScript" ID="js">

    var bcal = new CalendarPopup();
    bcal.setReturnFunction("setMultipleValues1");
    bcal.setMonthNames("Januari","Februari","Maart","April","Mei","Juni","Juli","Augustus","September","Oktober","November","December");
    bcal.showNavigationDropdowns();
    bcal.setDayHeaders("Z","M","D","W","D","V","Z");
    bcal.setWeekStartDay(1);
     ';
	 echo "
    function setMultipleValues1(y,m,d) {
         document.forms['act_add'].maand.value=LZ(m);
         document.forms['act_add'].dag.value=LZ(d);
         document.forms['act_add'].jaar.value=LZ(y);
         }
    </SCRIPT>";
  echo '
  <script language="JavaScript">
    function fillText()
	{
	  document.act_add.Trefwoord.value=document.act_add.Titel.value;
	}
  </Script>
  <form method="POST" action="" name="act_add">
    <table id="tab">
	  <tr>
	    <td colspan="4">
		  <input type="checkbox" value="1" name="Aanmelden" '.$chkval.'>Aanmelden is mogelijk</input>
		</td>
	  </tr>
	  <tr>
	    <td>Titel: '. $verplicht.'</td>
		<td colspan="1"> <input type="text" name="Titel" value="'.$fields['Titel'].'" size="40" onchange="fillText()" maxlength="60"> 
		</td>
		<td width="100"> Trefwoorden: '.$verplicht.'</td>
		<td colspan="1"> <input type="text" value="'.$fields['Trefwoord'].'" size="40" name="Trefwoord"> </td>
	  </tr>
	  <tr>
	    <td colspan="4"> Datum cursus: '.$niet.' 
		<input type="text" id="dag" name="dag" value="'.$fields['dag'].'" size="2" maxlength="2" /> - 
		<input type="text" id="maand" name="maand" value="'.$fields['maand'].'" size="3" maxlength="2" /> - 
		<input type="text" id="jaar" name="jaar" value="'.$fields['jaar'].'" size="4" maxlength="4" />
		<a href="#" onClick="';
		echo "bcal.showCalendar('banchor'); return false;";
		echo '" title="';
		echo "bcal.showCalendar('banchor'); return false;";
		echo '" name="banchor" id="banchor">Selecteer datum</a> <input id="showweeknumber" name="getmyweeknumber" type="button" value="Week nummer ophalen" onClick="getWeekNumber()"><span id="WeekNumber">&nbsp;</span>
		</td>
	  </tr>
	  <tr>						
	  <td> Dag: '.$semi.' </td>
	  <td> 
	    <select name="Dag">';
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
			if ($i == $fields['Dag'])
			{
			  echo '<option value="'.$i.'" selected="selected">'.$day.'</option>';
			}
			else
			{
			  echo '<option value="'.$i.'">'.$day.'</option>';
		    }
		  }
		  
		$fields['Tijd'] = ( isset ( $fields['Tijd'] ) ) ? $fields['Tijd'] : '';
		echo '
		</select>
	  </td>
      <td> Tijd: '.$semi.' </td>
	  <td> <input type="text" title="Format: 00:00-00:00" name="Tijd" value="'.$fields['Tijd'].'" size="40" maxlength="11"></td>
	</tr>
	<tr>
	  <td> Week: '.$semi.' </td>
	  <td> 
	    <select name="Week">';
		  //Genereer de weeknummers
		  for($i=1;$i<=52;$i++)
		  {
		    if ($i <= 9) 
			{
			  $i = "0" . $i;
			}
			$iYear = date('Y');
			if ($i == $fields['Week'])
			{
			  echo '<option value="'.$i.'" selected="selected"><b>Week '.$i.'</b></option>';
			}
			else
			{
			  echo '<option value="'.$i.'"> Week '.$i.'</option>';
			}
		  }
		// Ga weer door...
		$year = date('Y');
		echo '
		</select>	
	  </td>
	  <td> Jaar: '.$semi.'</td>
	  <td> 
	    <select name="Jaar">';
		  for($i=$year;$i<$year+4;$i++)
		  {
		    if ( $i == $fields['Jaar'] ) { 
				echo '<option value="'.$i.'" selected="selected">'.$i.'</option>';
			}
			else {
				echo '<option value="'.$i.'">'.$i.'</option>';
			}
		  }
		  echo '
		</select>
	  </td>
	</tr>
	<tr>
	  <td> Lokaal: '.$semi.'</td>
	  <td>';
	  if ( isset($fields['Plaats']) ) {
	  	echo selectPlaats($fields['Plaats']);
	  }
	  else {
	  	echo selectPlaats(NULL);
	  }
	  
		$fields['Lokaal'] = ( isset ( $fields['Lokaal'] ) ) ? $fields['Lokaal'] : '';
		$fields['Limiet'] = ( isset ( $fields['Limiet'] ) ) ? $fields['Limiet'] : '';
		echo '<input type="text" name="Lokaal"  value="'.$fields['Lokaal'].'" size="10" maxlength="30" /> 
	  </td>
	  <td> Limiet: '.$semi.'</td>
	  <td> 
	    <input type="text" name="Limiet" value="'.$fields['Limiet'].'" size="10" maxlength="3" /> <input name="onbeperkt" type="checkbox" title="Vink aan voor onbeperkte aanmeldingen" value="1" '.$chkon.' /> (onbeperkt)
	  </td>
	</tr>		
	<tr>
	  <td> Niveau: '.$semi.'</td>
	  <td>';
	  
		  if ( isset($fields['IDNiveau']) ) {
			echo selectNiveau($fields['IDNiveau']);
		  }
		  else {
			echo selectNiveau(NULL);
		  }
		echo '
	  </td>		
	  <td> Soort: '.$semi.'</td>
	  <td>';
	  
		  if ( isset($fields['IDSoort']) ) {
			echo selectSoort($fields['IDSoort']);
		  }
		  else {
			echo selectSoort(NULL);
		  }
		echo '
	  </td>
	</tr>
	<tr>
	  <td colspan="1">Docent: '.$semi.' </td>
	  <td>';
	  
		  if ( isset($fields['IDGever']) ) {
			echo selectGever($fields['IDGever']);
		  }
		  else {
			echo selectGever(NULL);
		  }

		echo '
	  </td>
	  <td> Opleiding: '.$semi.' </td>
	  <td>';
	  
		  if ( isset($fields['IDOpleiding']) ) {
			echo selectOpleiding($fields['IDOpleiding']);
		  }
		  else {
			echo selectOpleiding(NULL);
		  }

		$fields['Info'] = ( isset ( $fields['Info'] ) ) ? $fields['Info'] : '';
		echo '
	  </td>
	</tr>
	<tr>
	  <td> Informatie: '.$niet.' </TD>
	  <td colspan="3">
	    <div id="wysiwyg">
	      <textarea cols="100" rows="10" id="info" name="Info">'.$fields['Info'].'</textarea>
		</div>
		<script language="javascript1.2">';
		  echo "generate_wysiwyg('info'); ";
		echo '</script>
	  </td>
    </tr>
  </table>
  <center>
    <input type="submit" name="s1" value="Opslaan">
	<input type="reset" VALUE="Reset">
	<input type="submit" NAME="cancel" value="Annuleren">
  </center>
  ';
}
//Save

function saveit ($fields)
{
  //Zet de checkbox Aanmelden om naar 1 of 0
  if(isset($fields['Aanmelden']))  {
    // wel aangevinkt
    $fields['Aanmelden'] = 1;
  }
  else  {
    // niet aangevinkt
    $fields['Aanmelden'] = 0;
  }
  
  if(isset($fields['onbeperkt']))  {
    // wel aangevinkt
    $fields['onbeperkt'] = 1;
  }
  else  {
    // niet aangevinkt
    $fields['onbeperkt'] = 0;
  } 
  
	$fields['Bestand'] = ( isset ( $fields['Bestand'] ) ) ? $fields['Bestand'] : '';

  //Sla de activiteit op
  mysql_query("INSERT INTO `tblactiviteiten`
			 (`IDActiviteit`,`Week`,`Jaar`,`Dag`,`datum`,`Titel`,`Info`,`IDGever`,`IDNiveau`,`IDSoort`,
			 `IDOpleiding`,`Tijd`,`Lokaal`,`Plaats`,`Trefwoord`,`Bestand`, `Limiet`, `onbeperkt`,`Aanmelden` )
			   VALUES(
			   '".quote_smart($fields['Num']	)."',
			   '".quote_smart($fields['Week']	)."',
			   '".quote_smart($fields['Jaar']	)."',
			   '".quote_smart($fields['Dag']	)."',
			   '".$_POST['jaar']."-".$_POST['maand']."-".$_POST['dag']."',
			   '".quote_smart($fields['Titel']	)."',
			   '".quote_smart($fields['Info']	)."',
			   '".quote_smart($fields['IDGever']	)."',
			   '".quote_smart($fields['IDNiveau']	)."',
			   '".quote_smart($fields['IDSoort']	)."',
			   '".quote_smart($fields['IDOpleiding'])."',
			   '".quote_smart($fields['Tijd']		)."',
			   '".quote_smart($fields['Lokaal']		)."',
			   '".quote_smart($fields['Plaats']		)."',
			   '".quote_smart($fields['Trefwoord']	)."',
			   '".quote_smart($fields['Bestand']	)."',
			   '".quote_smart($fields['Limiet']		)."',
			   '".quote_smart($fields['onbeperkt']	)."',
			   '".quote_smart($fields['Aanmelden']	)."'
			   ) ") or die ("<div id='fout'>". mysql_error() . "</div>");	

  //Echo dat de gegevens juist zijn opgeslagen..
  echo '<p id="goed"> De activiteit is opgeslagen! </p>
  <meta http-equiv="REFRESH" content="2;URL=index.php?page=actoverzicht&week='.$fields['Week'].'&jaar='.$fields['Jaar'].'">';
}

function error_msg($msg)
{
  echo '<div id="fout">'.$msg.'</div>';
  $fields = $_POST;
  
  show_data($_SESSION['usertype'],$fields);
}
?>