<?php
/**
*  Animo activiteiten toevoegen
*  Deze pagina bevat de functie: show_data($fields) ,  saveit ($fields) ,  error_msg($msg)
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
		echo '<b>Animo</b>';
		
		if(isset($_POST['s1']))
		{
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
		  else
		  {	
			$fields = $_POST;
			//-------------------------------//
			if (empty ($fields['GeverID']) )	{
			  $fields['GeverID'] = 0;
			}
				
			$actnum = mysql_query("SELECT MAX(IDActiviteit) FROM tblactiviteiten") or die (mysql_error());
			$num 	= mysql_fetch_row($actnum) or die (mysql_error());
			$fields['Num']	= $num[0] +1;
				
			saveit($fields);
		  }
		}
		elseif(isset($_POST['cancel']))
		{
		  echo '<meta http-equiv="REFRESH" content="2;URL=index.php">';	
		}
		else
		{
		  $fields['Aanmelden'] = 1;
		  show_data($fields); 
		}
	break;
	default: echo 'U hebt geen toegang tot deze pagina.';
	break;
  }
}

//Functions --------------------------------------------------------------------------------------------------------------------------------------------------------

function show_data($fields)
{
	if ( isset($fields['Aanmelden']) ) {
		$chkval = 'checked="checked"';
	}
	else {
		$chkval = '';
	}
	if ( isset($fields['onbeperkt']) ) {
		$chkon = 'checked="checked"';
	}
	else {
		$chkon = '';
	}
  
  $verplicht = "<a style=CURSOR:help; title='Dit veld is verplicht'><img src='images/info_icon_r.gif' border='0' height=
  '13' width='13'></a>";
  $semi = "<a style=CURSOR:help; title='Verplicht veld als controle checkbox is aangevinkt'><img src='images/info_icon.gif
  ' border='0' height='13' width='13'></a>";
  $niet = "<a style=CURSOR:help; title='Dit veld is niet verplicht'><img src='images/info_icon_g.gif' border='0' height= 
  '13' width='13'></a>";
  
	$fields['Titel'] = ( isset ( $fields['Titel'] ) ) ? $fields['Titel'] : '';
	$fields['Trefwoord'] = ( isset ( $fields['Trefwoord'] ) ) ? $fields['Trefwoord'] : '';
  echo '
  <script Language="JavaScript">
    function fillText()
	{
	  document.act_add.Trefwoord.value=document.act_add.Titel.value;
	}
  </script>
  <form method="POST" action="" name="act_add">
    <table id="tab">
	  <tr>
	    <td colspan="4">
		  <input type="checkbox" name="Aanmelden" '.$chkval.' /> Aanmelden is mogelijk
		</td>
	  </tr>
	  <tr>
	    <td>Titel: '.$verplicht.'</td>
		<td colspan="1"> <input type="text" name="Titel" value="'.$fields['Titel'].'" size="40" onchange="fillText()" maxlength=
		  "60" /> 
		</td>
		<td width="100"> Trefwoorden: '.$verplicht.' </td>
		<td colspan="1"> <input type="text" value="'.$fields['Trefwoord'].'" size="40" name="Trefwoord" /> </td>
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
		  <input type="text" name="Lokaal" value="'.$fields['Lokaal'].'" size="10" maxlength="30" /> 
	    </td>
	    <td> Limiet: '.$semi.'</td>
	    <td> 
	      <input type="text" name="Limiet" value="'.$fields['Limiet'].'" size="10" maxlength="3" />  <input name="onbeperkt" type="checkbox" title="Vink aan voor onbeperkte aanmeldingen" value="1" '.$chkon.' /> (onbeperkt)
	    </td>
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
	    <td colspan="1">Docent: '.$semi.'</td>
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
		  echo '
		  </script>
	    </td>
      </tr>
    </table>
    <center>
      <input type="submit" name="s1" value="Opslaan">
	  <input type="reset" value="Reset">
	  <input type="submit" name="cancel" value="Annuleren">
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
  
  //Sla de activiteit op
  mysql_query("INSERT INTO `tblactiviteiten`
			 (`IDActiviteit`,`Titel`,`Info`,`IDGever`,`IDNiveau`,`IDSoort`,`IDOpleiding`,`Lokaal`,`Plaats`,
			  `Trefwoord`,`Limiet`, `onbeperkt`,`Aanmelden`,`Animo`)
			   VALUES(
			   '".$fields['Num']."',
			   '".$fields['Titel']."',
			   '".$fields['Info']."',
			   '".$fields['IDGever']."',
			   '".$fields['IDNiveau']."',
			   '".$fields['IDSoort']."',
			   '".$fields['IDOpleiding']."',
			   '".$fields['Lokaal']."',
			   '".$fields['Plaats']."',
			   '".$fields['Trefwoord']."',
			   '".$fields['Limiet']."',
			   '".$fields['onbeperkt']."',
			   '".$fields['Aanmelden']."',
			   '1'
			   )") or die ('<div id="fout">'. mysql_error() .'</div>');	

  //Echo dat de gegevens juist zijn opgeslagen..
  echo '<p id="goed"> De animo activiteit is opgeslagen! </p>
  <meta http-equiv="REFRESH" content="1;URL=index.php?page=animooverzicht">';
}

function error_msg($msg)
{
  echo'<div id="fout">'.$msg.'</div>';
  $fields = $_POST;
  
  show_data($fields);
}
?>