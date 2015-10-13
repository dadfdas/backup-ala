<?php
/**
*  Animo activiteit aanpassen.
*  Deze pagina bevat de functie: show_data($id,$session,$fields) , saveit ($id,$fields) , error_msg($msg)
*  Deze pagina gebruikt de functie:
*    selectPlaats() , selectNiveau() , selectSoort()  
*    selectGever() , selectOpleiding() , quote_smart()
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
	
		if ( empty($_POST['Titel']) ||  empty($_POST['Trefwoord']) ) {
			$errorMessage[] = "Titel of trefwoord is niet ingevuld.";
			$errorFound = true;
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
		if ( empty($_POST['IDGever']) ) {
			$errorMessage[] = "Geen leraar geselecteerd.";
			$errorFound = true;	
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

	  saveit($_GET['actid'],$fields);
	}
  }
  elseif(isset($_POST['s2']))
  {
    $fields = $_POST;
    
	mysql_query("UPDATE `tblactiviteiten` SET 
	       	    `Info` = '".quote_smart($fields['Info'])."',
			   WHERE `IDActiviteit` = '".quote_smart($fields['IDActiviteit'])."' ") or die ("<DIV ID='fout'>". mysql_error() . "</DIV>");
		   
	  echo '<div id="goed"> De animo is opgeslagen! </div>
			<meta http-equiv="REFRESH" content="2;URL=index.php?page=animooverzicht">';
  }
  elseif(isset($_POST['cancel']))
  {
	echo("<meta http-equiv='REFRESH' content='0;URL=index.php?page=animooverzicht'> ");	
  }	
  else
  {
    $SQL = mysql_query("SELECT * FROM tblactiviteiten WHERE IDActiviteit = '".quote_smart($_GET['actid'])."'");
	$fields = mysql_fetch_array($SQL);
	show_data($_GET['actid'],$_SESSION['usertype'],$fields); 
  }
}

//Functions --------------------------------------------------------------------------------------------------------------------------------------------------------

function show_data($id,$session,$fields)
{
	if ( isset($fields['Aanmelden']) ) {
		$chkval = 'checked="checked" ';
	}
	else {
		$chkval = '';
	}
	if ( isset($fields['onbeperkt']) ) {
		$chkon = 'checked="checked" ';
	}
	else {
		$chkon = '';
	}
  switch ($session)
  {
    case "Admin": //Show form admin 
	  $verplicht = "<a style=CURSOR:help; title='Dit veld is verplicht'>
	                  <img src='images/info_icon_r.gif' border='0'  height='13' width='13'>
					</a>";
	  $semi = "<a style=CURSOR:help; title='Verplicht veld als controle checkbox is aangevinkt'>
	             <img src='images/info_icon.gif' border='0' height='13' width='13'>
			   </a>";
	  $niet = "<a style=CURSOR:help; title='Dit veld is niet verplicht'>
	             <img src='images/info_icon_g.gif' border='0' height='13' width='13'></a>";
			 
	$fields['Titel'] = ( isset ( $fields['Titel'] ) ) ? $fields['Titel'] : '';
	$fields['Trefwoord'] = ( isset ( $fields['Trefwoord'] ) ) ? $fields['Trefwoord'] : '';
	
	  echo '
	  <script language="JavaScript">
	    function fillText()
		{
		  document.animo_edit.Trefwoord.value=document.animo_edit.Titel.value;
		}
	  </script>
	  <form method="POST" action="" name="animo_edit">
	    <table id="tab">
		  <tr>
		    <td colspan="4">
			  <input type="checkbox" name="Aanmelden" '.$chkval.'value="1" />Aanmelden is mogelijk</input>
		    </td>
		  </tr>
		  <tr>
		    <td>Titel: '.$verplicht.'</td>
		    <td colspan="1"> 
		      <input type="text" name="Titel" value="'.$fields['Titel'].'" size="40" onchange="fillText()" maxlength="60"></td>
		    <td width="100"> Trefwoorden: '.$verplicht.'</td>
		    <td colspan="1"> <input type="text" value="'.$fields['Trefwoord'].'" size="40" name="Trefwoord" /></td>
		  </tr>
	      <tr>
	        <td> Lokaal: '.$semi.'</td>
		    <td> 
			';
			  if ( isset($fields['Plaats']) ) {
				echo selectPlaats($fields['Plaats']);
			  }
			  else {
				echo selectPlaats(NULL);
			  }
				  
		$fields['Lokaal'] = ( isset ( $fields['Lokaal'] ) ) ? $fields['Lokaal'] : '';
		$fields['Limiet'] = ( isset ( $fields['Limiet'] ) ) ? $fields['Limiet'] : '';
				  
		  echo '<input type="text" name="Lokaal" value="'.$fields['Lokaal'].'" size="10" maxlength="30"> 
		  </td>
		  <td> Limiet: '.$semi.'</td>
		  <td> <input type="text" name="limiet" value="'.$fields['Limiet'].'" size="10" maxlength="3"> <input name="onbeperkt" type="checkbox" title="Vink aan voor onbeperkte aanmeldingen" value="1" '.$chkon.' /> (onbeperkt)</td>
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
		    echo'
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
	  <input type="reset" value="Reset">
	  <input type="submit" name="cancel" value="Annuleren">
    </center>';  	  
    break;
    default: 
    echo '
    <form method="POST" action="" name="frmIntern">
      <div id="head">Activiteit - '.$fields['Titel'].'<br /></div>
	  <table id="tab">
	    <tr>
	      <td valign="top">Info:</td>
		  <td colspan="5">
		    <div id="wysiwyg">
		      <textarea cols="100" rows="20" id="info" name="Info" />
		  	    '.$fields['Info'].'
		      </textarea>
		    </div>
		  </td>
	    </tr>
	    <tr>
	      <td valign="top">Lokaal:</td>
		  <td colspan="2" valign="top">'.$fields['Lokaal'].'</td>
	    <tr>
	  </table>
	  <script language="javascript1.2">';
	    echo "generate_wysiwyg('info');";
	  echo '</script>
	  <input type="submit" value="verzenden" name="s2" />
	  <input type="reset" value="reset" name="reset" />
	  <input type="hidden" value="'.$fields['Titel'].'" name="Titel" />
	  <input type="hidden" value="'.$fields['IDActiviteit'].'" name="IDActiviteit" />
    </form>
	';
	break;
  }
}

//Save
function saveit ($id,$fields)
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
  
  if(isset($fields['onbepertk']))  {
    // wel aangevinkt
    $fields['onbepertk'] = 1;
  }
  else  {
    // niet aangevinkt
    $fields['onbepertk'] = 0;
  }
  $fields['Limiet'] = ( isset ( $fields['Limiet'] ) ) ? $fields['Limiet'] : '';

  //Sla de activiteit op
  mysql_query("UPDATE `tblactiviteiten` SET 
			  `Titel`		= '".quote_smart( $fields['Titel']		)."',
			  `Info` 		= '".quote_smart( $fields['Info']		)."',
			  `IDGever` 	= '".quote_smart( $fields['IDGever']	)."', 
			  `IDNiveau` 	= '".quote_smart( $fields['IDNiveau']	)."',
			  `IDSoort` 	= '".quote_smart( $fields['IDSoort']	)."',
			  `IDOpleiding` = '".quote_smart( $fields['IDOpleiding'])."',
			  `Lokaal` 		= '".quote_smart( $fields['Lokaal']		)."',
			  `Plaats` 		= '".quote_smart( $fields['Plaats']		)."',
			  `Trefwoord` 	= '".quote_smart( $fields['Trefwoord']	)."',
			  `Limiet` 		= '".quote_smart( $fields['Limiet']		)."',
			  `onbeperkt` 	= '".quote_smart( $fields['onbeperkt']	)."',
			  `Aanmelden` 	= '".quote_smart( $fields['Aanmelden']	)."',
			  `Animo` 		= '1'
			  WHERE `IDActiviteit` = '".quote_smart($id)."' ") or die ("<DIV ID='fout'>". mysql_error() . "</DIV>");

  //Echo dat de gegevens juist zijn opgeslagen..
  echo '<div id="goed"> De animo is opgeslagen! </div>
  <meta http-equiv="REFRESH" content="2;URL=index.php?page=animooverzicht">';
}

// Error bericht
function error_msg($msg){
	echo '<div id="fout">'.$msg.'</div>';
	$fields = $_POST;

	show_data($_GET['actid'],$_SESSION['usertype'],$fields);
}
?>