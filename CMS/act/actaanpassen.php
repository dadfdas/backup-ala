<?php
/**
*  Activiteit aanpassen
*  Deze pagina bevat de functie: 
        show_data($session,$fields) , 
        saveit ($id,$fields)  <- Update database en emails verzenden als het een animo activiteit was.
		insertit($id,$fields) ,  cancelactiviteit() , error_msg($msg)
*  Deze pagina gebruikt de functie:
*    selectPlaats(), selectNiveau(), selectSoort() , 
*    selectGever(), selectOpleiding(), selectDag()
*    staan in: inc_functions_beheer.php
*/

//Controleer eerst of er al een session is (!)
if (!isset ($_SESSION['user']))
{
  die("Hack attempt");
}
else
{
  if(isset($_POST['s1']))
  {
	  //// OPSLAAN ADMINISTRATOR
	  
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
	else {
		
	  $fields = $_POST;
	  //-------------------------------//
	  if (empty ($fields['IDGever']))
	  {
	  	$fields['IDGever'] = 0;
	  }

	  if(isset($_POST["Aanmelden"]))
	  {
        // wel aangevinkt
        $checked = 1;
	  }
	  else
	  {
        // niet aangevinkt
        $checked = 0;
	  }

	  $ActID = $_GET['actid'];
	  //Controle van de ingevoerde velden tbt de database...
	  //------------------------------------------------------------------------------------------------------//
	  //controleer tijds notatie------//
	  $start = substr($fields['Tijd'],0,5);
	  $eind = substr($fields['Tijd'],6,5);
	  strtotime($start);
	  strtotime($eind);
	  $start = ereg_replace(':', '.', $start);
	  $eind = ereg_replace(':', '.', $eind);

	  if($eind < $start)
	  {
	    error_msg("De eindtijd is eerder dan de begintijd!");
	  }
	  else
	  {
		$qsT = "SELECT * FROM tblactiviteiten
		             	  WHERE `Week` = '".$fields['Week']."' AND `Dag` = '".$fields['Dag']."'
				          AND `Jaar` ='".$fields['Jaar']."' AND `Lokaal` = '".$fields['Lokaal']."'
						  AND Plaats = '".$fields['Plaats']."' AND AArchief = 0";
	    $sT = mysql_query($qsT);

		if(mysql_num_rows($sT))
		{
			// Maak een errorMessage array aan om de fout berichten in op te slaan
				$zelfdetijdgevonden = Array();
			while ( $chkT = mysql_fetch_assoc($sT) ) {
				if ( $chkT['IDActiviteit'] == $_GET['actid'] ) {
					// negeer
				}
				else {
				  $ostart = substr($chkT['Tijd'],0,5);
				  $oeind = substr($chkT['Tijd'],6,5);
				  strtotime($ostart);
				  strtotime($oeind);
				  $ostart = ereg_replace(':', '.', $ostart);
				  $oeind = ereg_replace(':', '.', $oeind);
				  //Als start > opgeslagen eind, sla op

				  if(($eind <= $ostart))  {
					// niets doen
				  }
				  elseif(($oeind <= $start))  {
					// niets doen
				  }
				  /*elseif(($start == $ostart && $eind == $oeind && $checked == 0))  {
					// niets doen
				  }
				  elseif(($start == $ostart && $eind == $oeind && $checked == 1))  {
					// niets doen
				  }*/
				  else
				  {
					// niet opslaan
					$zelfdetijdgevonden[]   = "match";
				  }
				}
			}
			
			if ( count($zelfdetijdgevonden) >= 1 ) {
				error_msg("Pas de tijd aan!");
			}
			else {
				// geen match dus opslaan
				saveit($_GET['actid'],$fields);
			}
		}
		else
		{
		  saveit($_GET['actid'],$fields);
		}
	  }
	}
  }
  elseif(isset($_POST['s2']))
  {
	  //// OPSLAAN VOOR Interne docenten
/*    $fields = $_POST;
	if(1==9)
	{ // Hier komt het upload gedoe :P
	}
	else
	{*/
    //Sla de activiteit op
	  mysql_query("UPDATE `tblactiviteiten` SET
	        	  `Info` = '".$fields['Info']."'
				   WHERE `IDActiviteit` = '".$fields['IDActiviteit']."' 
				   ") or die ('<div id="fout">'. mysql_error() . '</div>');
	  
	    $week = $_GET['week'];
	    $jaar = $_GET['jaar'];
	    echo '<div id="goed"> De activiteit is opgeslagen! </div>
			<meta http-equiv="REFRESH" content="0;URL=index.php?page=actoverzicht&week='.$week.'&jaar='.$jaar.'">';
  }
  elseif(isset($_POST['s3']))
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
	  $start = ereg_replace(':', '.', $start);
	  $eind = ereg_replace(':', '.', $eind);

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

		if(mysql_num_rows($sT))
		{
			// Maak een errorMessage array aan om de fout berichten in op te slaan
				$zelfdetijdgevonden = Array();
			while ( $chkT = mysql_fetch_assoc($sT) ) {
				  $ostart = substr($chkT['Tijd'],0,5);
				  $oeind = substr($chkT['Tijd'],6,5);
				  strtotime($ostart);
				  strtotime($oeind);
				  strtotime($oeind);
				  //Als start > opgeslagen eind, sla op

				  if(($eind <= $ostart))  {
					// niets doen
				  }
				  elseif(($oeind <= $start))  {
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
				insertit($_GET['actid'],$fields);
			}
		}
		else
		{
		  insertit($_GET['actid'],$fields);
		}
	  }
	}
  }
  elseif(isset($_POST['cancel']))
  {
  	cancelactiviteit();
  }
  else
  {
    $SQL = mysql_query("SELECT *, 
					   			DATE_FORMAT(datum, '%d') AS dag,
								DATE_FORMAT(datum, '%m') AS maand,
								DATE_FORMAT(datum, '%Y') AS jaar
					   FROM 
					   		tblactiviteiten 
					   WHERE IDActiviteit = ".$_GET['actid']);
	$fields = mysql_fetch_array($SQL);
	//show_data($_GET['actid'],$_SESSION['usertype'],$fields);
	show_data($_SESSION['usertype'],$fields);
  }
}

//Functions --------------------------------------------------------------------------------------------------------------------------------------------------------


function show_data($session,$fields)
{
		
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

  switch ($session)
  {
    case "Admin": //Show form admin
	  $verplicht =  '<a style="CURSOR:help;" title="Dit veld is verplicht">
	                  <img src="images/info_icon_r.gif" border="0"  height="13" width="13">
					</a>';
	  $semi = '<a style="CURSOR:help;" title="Verplicht veld als controle checkbox is aangevinkt">
	             <img src="images/info_icon.gif" border="0" height="13" width="13">
			   </a>';
	  $niet = '<a style="CURSOR:help;" title="Dit veld is niet verplicht">
	             <img src="images/info_icon_g.gif" border="0" height="13" width="13"></a>';
		$fields['Bestand'] = ( isset ( $fields['Bestand'] ) ) ? $fields['Bestand'] : '';
	  $bestand23 = $fields['Bestand'];

	  if(!empty($bestand23))
	  {
	    $bestand23 = '<i>'.$bestand23.' is nu gekoppelt aan deze activiteit.</i>';
	  }
	  
	$fields['Titel'] = ( isset ( $fields['Titel'] ) ) ? $fields['Titel'] : '';
	$fields['Trefwoord'] = ( isset ( $fields['Trefwoord'] ) ) ? $fields['Trefwoord'] : '';
	$fields['IDActiviteit'] = ( isset ( $fields['IDActiviteit'] ) ) ? $fields['IDActiviteit'] : '';	
	
	// Juiste datum's invullen
	// Als de aanvraag vanaf een forumulier komt dan zijn de volgnden dingen niet leeg. 
	if ( !empty($fields['dag']) && !empty($fields['maand']) && !empty($fields['jaar']) ) {
		$datum = array();
		$datum['dag'] 		= $fields['dag'] ;
		$datum['maand'] 	= $fields['maand'] ;
		$datum['jaar']		= $fields['jaar'] ;		
	}
	
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
         document.forms['act_edit'].maand.value=LZ(m);
         document.forms['act_edit'].dag.value=LZ(d);
         document.forms['act_edit'].jaar.value=LZ(y);
         }
    </SCRIPT>";
	  echo '
	  <script Language="JavaScript">
	    function fillText()
		{
		  document.act_edit.Trefwoord.value=document.act_edit.Titel.value;
		}
	  </script>
	  <form method="POST" action="" name="act_edit">
	  	<input type="hidden" name="Animo" value="'.$fields['Animo'].'" size="40" />
	    <table id="tab">
		  <tr>
		    <td colspan="2">
			  <input type="checkbox" name="Aanmelden" value="1" '.$chkval.'>Aanmelden is mogelijk</input>
		    </td>
			<td colspan="2">
			  <a href="act/files.php?ID='.$fields['IDActiviteit'].'" onclick="';
			  echo "return popitup('act/files.php?ID=".$fields['IDActiviteit']."')";
			  echo '">Bestand</a>
			  '.$bestand23.'
			</td>
		  </tr>
		  <tr>
		    <td>Titel: '.$verplicht.'</td>
		    <td colspan="1">
		      <input type="text" name="Titel" value="'.$fields['Titel'].'" size="40" onchange="fillText()" maxlength="60">                  </td>
		    <td width="100"> Trefwoorden: '.$verplicht.'</td>
		    <td colspan="1"> <input type="text" value="'.$fields['Trefwoord'].'" size="40" name="Trefwoord"></td>
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
	        <td> Dag: '.$semi.'</td>
	        <td>
		      ';
				  if ( isset($fields['Dag']) ) {
				  	echo selectDag($fields['Dag']);
				  }
				  else {
				  	echo selectDag(NULL);
				  }
		      echo '
	        </td>
			<td> Tijd:
			  <a style="CURSOR:help;" title="Notatie: 12:00-14:00 Dit veld is verplicht als de controle
			    checkbox is aangevinkt"><img src="images/info_icon.gif" border="0" height="13" width="13" />
			  </a>
			</td>
			<td>
		      <input type="text" name="Tijd" title="Format: 00:00-00:00" value="'.$fields['Tijd'].'" size="40" maxlength="11">
			</td>
	      </tr>
		  <tr>
		    <td> Week: '.$semi.' </td>
		    <td>
		      <select name="Week">
			  ';
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
						echo '<option value="'.$i.'"> Week '.$i.'</OPTION>';
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
			      //echo("<OPTION VALUE='$i'>$i</OPTION>\t\n\n\t");
				  if ($i == $fields['Jaar'])
				  {
				    echo '<option value="'.$i.'" selected="selected"><b>'.$i.'</b></option>';
				  }
				  else
				  {
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
			  echo '
		    <input type="text" name="Lokaal" value="'.$fields['Lokaal'].'" size="10" maxlength="30">
		  </td>
		  <td> Limiet: '.$semi.'</td>
		  <td> <input type="text" name="Limiet" value="'.$fields['Limiet'].'" size="10" maxlength="3"> <input name="onbeperkt" type="checkbox" title="Vink aan voor onbeperkte aanmeldingen" value="1" '.$chkon.' /> (onbeperkt)</td>
	    </tr>
	    <tr>
	      <td> Niveau:  '.$semi.'</td>
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
		    echo'
	    </td>
      </tr>
	  <tr>
	    <td> Informatie: '.$niet.' </td>
	    <td colspan="3">
	      <div id="wysiwyg">
	 	    <textarea cols="100" rows="10" id="info" name="Info">'.$fields['Info'].'</textarea>
		  </div>
		  <script language="javascript1.2">';
		    echo "generate_wysiwyg('info');";
		  echo '</script>
	    </td>
	  </tr>
    </table>
    <center>
      <input type="submit" name="s1" value="Aanpassen">
	  ';
	  if ( $fields['Animo'] == 0 ) {
	  echo'<input type="submit" name="s3" value="Invoeren">';
	  }
	  echo '
	  <input type="reset" value="Reset">
	  <input type="submit" name="cancel" value="Annuleren">
    </center>';
    break;
    default:
		$fields['Dag'] = ( isset ( $fields['Dag'] ) ) ? $fields['Dag'] : '';
		$fields['Tijd'] = ( isset ( $fields['Tijd'] ) ) ? $fields['Tijd'] : '';
		$fields['Week'] = ( isset ( $fields['Week'] ) ) ? $fields['Week'] : '';
		$fields['Jaar'] = ( isset ( $fields['Jaar'] ) ) ? $fields['Jaar'] : '';
		$fields['Lokaal'] = ( isset ( $fields['Lokaal'] ) ) ? $fields['Lokaal'] : '';
		$fields['Info'] = ( isset ( $fields['Info'] ) ) ? $fields['Info'] : '';
		$fields['IDActiviteit'] = ( isset ( $fields['IDActiviteit'] ) ) ? $fields['IDActiviteit'] : '';
    echo '
    <form method="POST" action="" name="frmIntern">
      <div id="head">Activiteit - '.$fields['Titel'].'<br /></div>
	  <table id="tab">
	    <tr>
	      <td valign="top">Dag:</td>
		  <td width="273">'.$fields['Dag'].'</td>
		  <td valign="top">Tijd:</td>
		  <td width="273">'.$fields['Tijd'].'</td>
	    </tr>
	    <tr>
	      <td valign="top">Week:</td>
		  <td width="273">'.$fields['Week'].'</td>
		  <td valign="top">Jaar:</td>
		  <td width="273">'.$fields['Jaar'].'</td>
	    </tr>
	  </table>
	  <table id="tab">
	    <tr>
	      <td valign="top">Info:</td>
		  <td cols pan="5">
		    <div id="wysiwyg">
		      <textarea cols="100" rows="20" id="info" name="Info" />
		  	    '.$fields['Info'].'
		      </textarea>
		    </div>
		  </td>
	    </tr>
	  </table>
	  <table id="tab">
	    <tr>
	      <td valign="top">Lokaal:</td>
		  <td width="273">'.$fields['Lokaal'].'</td>
		  <td valign="top">Bestand:</td>
		  <td width="246" valign="top">
		    <a href="act/files.php?ID='.$fields['IDActiviteit'].'" onclick="';
			echo "return popitup('act/files.php?ID=".$fields['IDActiviteit']."')";
			echo '">Bestand</a>';
		    if (!empty($fields['Bestand']))
		    {
		      echo '<br /><i><a href="../uploads/'.$fields['Bestand'].'">'.$fields['bestand'].'</i></a>
	              is nu gekoppelt aan deze activiteit.<BR />Als er een nieuw bestand wordt geupload, wordt dit
				  bestand in de datbase overschreven.';
		    }
		  echo '</td>
	    <tr>
	  </table>
	  <script language="javascript1.2">';
	    echo "generate_wysiwyg('info');";
	  echo '</script>
	  <input type="submit" value="Verzenden" name="s2" />
	  <input type="reset" value="reset" name="reset" />
	  <input type="submit" name="cancel" value="Annuleren">
	  <input type="hidden" value="'.$fields['Titel'].'" name="Titel" />
	  <input type="hidden" value="'.$fields['Week'].'" name="Week" />
	  <input type="hidden" value="'.$fields['Jaar'].'" name="Week" />
	  <input type="hidden" value="'.$fields['IDActiviteit'].'" name="IDActiviteit" />
    </form>
	';
	break;
  }
}

// Gegevens opslaan
function saveit ($id,$fields)
{

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
  mysql_query("UPDATE `tblactiviteiten` SET
			  `Week` 		= '".quote_smart($fields['Week']		)."',
			  `Jaar` 		= '".quote_smart($fields['Jaar']		)."',
			  `Dag` 		= '".quote_smart($fields['Dag']			)."',
			  `datum`		= '".$_POST['jaar']."-".$_POST['maand']."-".$_POST['dag']."',
			  `Titel`		= '".quote_smart($fields['Titel']		)."',
			  `Info` 		= '".quote_smart($fields['Info']		)."',
			  `IDGever` 	= '".quote_smart($fields['IDGever']		)."',
			  `IDNiveau` 	= '".quote_smart($fields['IDNiveau']	)."',
			  `IDSoort` 	= '".quote_smart($fields['IDSoort']		)."',
			  `IDOpleiding` = '".quote_smart($fields['IDOpleiding']	)."',
			  `Tijd` 		= '".quote_smart($fields['Tijd']		)."',
			  `Lokaal` 		= '".quote_smart($fields['Lokaal']		)."',
			  `Plaats` 		= '".quote_smart($fields['Plaats']		)."',
			  `Trefwoord` 	= '".quote_smart($fields['Trefwoord']	)."',
			  `Bestand` 	= '".quote_smart($fields['Bestand']		)."',
			  `Limiet` 		= '".quote_smart($fields['Limiet']		)."',
			  `onbeperkt`	= '".quote_smart($fields['onbeperkt']	)."',
			  `Aanmelden` 	= '".quote_smart($fields['Aanmelden']	)."',
			  `Animo` 		= '0'
			  WHERE `IDActiviteit` = '".quote_smart($id)."' 
			  ") or die ("<div id='fout'>". mysql_error() . "</div>");

		// Als het eerst een animo activiteit was emails versturen naar aangemelde studenten dat de activiteit door gaat.
		if ( $fields['Animo'] == 1 ) {
			 // Email adressen ophalen
			$q_act = mysql_query("SELECT *
								 	FROM tblactiviteiten
									WHERE IDActiviteit = '".quote_smart($id)."'
								 ") or die ("<div id='fout'>". mysql_error() . "</div>");
			
			$f_act = mysql_fetch_assoc($q_act);
			
				switch ($f_act['Plaats'])
				  {
					case 0: $plaats = "Gouda";break;
					case 1: $plaats = "Zoetermeer";break;
					case 2: $plaats = "Extern";break;
				  }
			 // Bericht maken
			$bericht = 'Beste student,<br /><br />'; 
			$bericht.= 'De animo activiteit <strong>'.$f_act['Titel'].'</strong> waarvoor u zich heeft aangemeld zal binnenkort gaan plaatsvinden.<br />';
			$bericht.= 'Meer informatie op: http://www.kennisbus.nl/index.php?page=infoact&actid='.$f_act['IDActiviteit'].'<br /><br />';
			$bericht.= 'Met vriendelijk groet,<br> De Kennisbus administrator';
				
			// Inschrijvingen ophalen
			$q_ins = mysql_query("SELECT 
								 	i.IDActiviteit, i.IDStudent, s.Voornaam,
									s.Achternaam, s.Email
								 FROM tblinschrijvingen i 
								 LEFT JOIN tblstudent s ON (i.IDStudent = s.IDStudent)
							  	WHERE i.IDActiviteit = '".quote_smart($id)."'") or die ("<div id='fout'>". mysql_error() . "</div>");
			
			echo 'Er zijn naar '.mysql_num_rows($q_ins).' studenten herinering mails verstuurd.<br />' ;
			
			while ( $f_ins = mysql_fetch_assoc($q_ins) ) {

				//// EMAIL VERSTUREN
				$sendMail = new email();
				$sendMail->set_ontvanger($f_ins['Voornaam'].' '.$f_ins['Achternaam'], $f_ins['Email']);
				$sendMail->set_onderwerp("Kennisbus activiteit: ".$f_act['Titel']);
				$sendMail->set_bericht($bericht);
				
				if ( $sendMail->send() ) {
						echo '<br />Verzenden naar '.$f_ins['Email'].' Gelukt';	
				}
				else {
						echo '<br />Verzenden naar '.$f_ins['Email'].' Mislukt';	
				} 
			}
		}


  //Echo dat de gegevens juist zijn opgeslagen..
  echo '<div id="goed"> De activiteit is opgeslagen! </div>
  <meta http-equiv="REFRESH" content="2;URL=index.php?page=actoverzicht&week='.$fields['Week'].'&jaar='.$fields['Jaar'].'">	';
}

//Insert

function insertit ($id,$fields)
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

  //Sla de activiteit op
  mysql_query("INSERT INTO `tblactiviteiten`
	(`IDActiviteit`,`Week`,`Jaar`,`Dag`,`Titel`,`Info`,`IDGever`,`IDNiveau`,`IDSoort`,`IDOpleiding`,`Tijd`,
	`Lokaal`,`Plaats`,`Trefwoord`,`Bestand`, `Limiet`,`onbeperkt`,`Aanmelden`,`Animo`)
			   VALUES(
			   '".quote_smart($fields['Num'])."',
			   '".quote_smart($fields['Week'])."',
			   '".quote_smart($fields['Jaar'])."',
			   '".quote_smart($fields['Dag'])."',
			   '".quote_smart($fields['Titel'])."',
			   '".quote_smart($fields['Info'])."',
			   '".quote_smart($fields['IDGever'])."',
			   '".quote_smart($fields['IDNiveau'])."',
			   '".quote_smart($fields['IDSoort'])."',
			   '".quote_smart($fields['IDOpleiding'])."',
			   '".quote_smart($fields['Tijd'])."',
			   '".quote_smart($fields['Lokaal'])."',
			   '".quote_smart($fields['Plaats'])."',
			   '".quote_smart($fields['Trefwoord'])."',
			   '".quote_smart($fields['Bestand'])."',
			   '".quote_smart($fields['Limiet'])."',
			   '".quote_smart($fields['onbeperkt'])."',
			   '".quote_smart($fields['Aanmelden'])."',
			   '0')") or die ("<DIV id='fout'>". mysql_error() . "</DIV>");

  //Echo dat de gegevens juist zijn opgeslagen..
  echo '<p id="goed"> De activiteit is opgeslagen! </p>
  <meta http-equiv="REFRESH" content="2;URL=index.php?page=actoverzicht&week='.$fields['Week'].'&jaar='.$fields['Jaar'].'">';

}

function cancelactiviteit()
{
	$fields = $_POST;
    echo '<meta http-equiv="REFRESH" content="0;URL=index.php?page=actoverzicht&week='.$fields['Week'].'
	&jaar='.$fields['Jaar']. '">';
}

function error_msg($msg){
	echo '<DIV ID="fout">'.$msg.'</DIV>';
	$fields = $_POST;

	show_data($_SESSION['usertype'],$fields);
}
?>