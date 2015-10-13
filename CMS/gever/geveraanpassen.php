<?php
/**
*  Docent bewerken
*  Deze pagina bevat de functie: edit_gever($ID, $fields, $error)
*  Deze pagina gebruikt de functie:
*    selectIDtype()   staan in: inc_functions_beheer.php
*     quote_smart()   staat in: inc_functions.php
*/
if(!isset ($_SESSION['user']))
{
  die("Hack attempt");
}
else
{ ## deze else[1] wordt onderaan afgesloten
  ?>
  <p><b>Docent aanpassen</b></p>

<?php
  //controleer of er een gever wordt aangepast, anders: stuur terug naar overzicht
  if(!isset($_POST['submit']) && !isset($_GET['GID']))
  {
	echo("Selecteer een gebruiker uit de lijst..
	<meta http-equiv='REFRESH' content='2;URL=index.php?page=geveroverzicht'>");
  }
  elseif(isset($_POST['cancel']))
  {
	echo("<meta http-equiv='REFRESH' content='0;URL=index.php?page=geveroverzicht'> ");	
  }	
  elseif(isset($_POST['submit']))
  {
	// Maak een errorMessage array aan om de fout berichten in op te slaan
	$errorMessage = Array();
	// errorFound standaard op false
	$errorFound   = false;

	if ( empty($_POST['naam']) ) {
		$errorMessage[] = "Naam is niet ingevuld.";
		$errorFound = true;
	}
	if ( empty($_POST['gebruikersnaam']) ) {
		$errorMessage[] = "Gebruikersnaam is niet ingevuld.";
		$errorFound = true;
	}
	if ( empty($_POST['email']) ||  !isEmail($_POST['email']) ) {
		$errorMessage[] = "Email adres is niet juist.";
		$errorFound = true;
	}
	if ( empty($_POST['IDType']) ) {
		$errorMessage[] = "Logintype niet ingevuld.";
		$errorFound = true;
	}
	
	// Error gevonden?
	if ( $errorFound ) {
			$size = count($errorMessage);
			$error_mes = '';
			
			for( $i=0; $i < $size; $i++ ) {
				$error_mes .= $errorMessage[$i].'<br />';
			}
			
			$fields = $_POST;
			edit_gever('', $fields, $error_mes);
	}
	else {
					
		##SQL Statement
		if ( isset($_POST['Archive']) ) {
			$Archief = 1;
		}
		else {
			$Archief = 0;
		}
		
		if ( !empty($_POST['nieuwwachtwoord']) ) {
			$nieuwwachtwoord = ", Wachtwoord = '".md5($_POST['nieuwwachtwoord'])."'" ;		
		}
		else {
			$nieuwwachtwoord = '';
		}
		
		$Update ="UPDATE `tblgever` SET
				`gebruikersnaam` = '".quote_smart($_POST['gebruikersnaam'])."',
				`Naam` = '".quote_smart($_POST['naam'])."', 
				`gVoornaam` = '".quote_smart($_POST['vnaam'])."',
				`Adres` = '".quote_smart($_POST['adres'])."',
				`Woonplaats` = '".quote_smart($_POST['wplaats'])."',
				`Postcode` = '".quote_smart($_POST['pcode'])."',
				`Telefoon` = '".quote_smart($_POST['phone'])."',
				`Fax` = '".quote_smart($_POST['fax'])."',
				`Email` = '".quote_smart($_POST['email'])."',	
				`Bedrijf` = '".quote_smart($_POST['bedrijf'])."',
				`IDType` = '".quote_smart($_POST['IDType'])."',
				`Archief` = '".$Archief."'
				".$nieuwwachtwoord."
				WHERE `IDGever` ='".quote_smart($_POST['ID'])."' ";
		
		mysql_query($Update) or die("Gegevens konden niet worden opgeslagen: <BR> 
		<div id='fout' style=width:600px;>".mysql_error()."</div>");
		
		echo("Gebruiker is geupdated..<BR />\n
		U wordt nu automatisch teruggestuurd naar het overzicht.<BR>\n
		<meta http-equiv='REFRESH' content='2;URL=index.php?page=geveroverzicht'>");
	}
  }
  elseif(isset($_GET['GID']))
  {
	$ID = $_GET['GID'];
	edit_gever($ID, '', '');
  }

} 
##else[1]

##functions

function edit_gever($ID, $fields, $error)
{
  if(!empty($ID))
  {
	##We maken eerst de SQLQuery welke de gever gaat opzoeken..
	$SQL = mysql_query("SELECT * FROM tblgever WHERE IDGever = '".quote_smart($ID)."'");

	if(mysql_num_rows($SQL))
    {
	  $row = mysql_fetch_array($SQL);
	  	$gebruikersnaam = $row['gebruikersnaam'];
	    $naam 		= $row['Naam']; 
		$vnaam 		= $row['gVoornaam'];
		$adres 		= $row['Adres'];
		$pcode 		= $row['Postcode'];
		$wplaats 	= $row['Woonplaats'];
		$phone 		= $row['Telefoon'];
		$fax 		= $row['Fax'];
		$email 		= $row['Email'];
		$type 		= $row['IDType'];
		$bedrijf 	= $row['Bedrijf'];
		$ID 		= $row['IDGever'];
		
		if ( $row['Archief'] == 1 ) {
			$archief = 'checked="checked"';
		}
		else {
			$archief = '';
		}
    }
	else
	{ 
	  $error = 'Docent kon niet worden gevonden.';
	}
  }
  else
  {
	  	$gebruikersnaam = $fields['gebruikersnaam'];
	    $naam 		= $fields['naam']; 
		$vnaam 		= $fields['vnaam'];
		$adres 		= $fields['adres'];
		$pcode 		= $fields['pcode'];
		$wplaats 	= $fields['wplaats'];
		$phone 		= $fields['phone'];
		$fax 		= $fields['fax'];
		$email 		= $fields['email'];
		$type 		= $fields['IDType'];
		$bedrijf 	= $fields['bedrijf'];
		if ( isset($fields['Archive']) ) {
			$archief = 'checked="checked"';
		}
		else {
			$archief = '';
		}
		$ID			= $fields['ID'];
  }

	  if ( isset($error) ) {
			echo '<div id="fout">'.$error.'</div>';
	  }
      echo '
       <form method="post" action="">
	     <table id="tabel">
           <tr>
		     <td colspan="3">
			   <center>
			     <b>Velden met een <font color="red">*</font> zijn verplichte velden!</b>
				</center>
			 </td>
		   </tr>
		   <tr>
             <td> Gebruikersnaam:</td>
		     <td> <input type="text" name="gebruikersnaam" value="'.$gebruikersnaam.'" size="30"> </td>
			 <td> <font color="red">*</font></td>
		   </tr>
		   <tr>
             <td> Naam: </td>
		     <td> <input type="text" name="naam" value="'.$naam.'" size="30"> </td>
			 <td> <font color="red">*</font></td>
		   </tr>
		   <tr>
		     <td> Voornaam: </td>
			 <td> <input type="text" name="vnaam" value="'.$vnaam.'" size="30"> </td>
			 <td>&nbsp;</td>
		   </tr>
		   <tr>
		     <td> Email: </td>
			 <td> <input type="text" name="email" value="'.$email.'" size="30"> </td>
			 <td> <font color="red">*</font></td>
	  	   </tr>					
		   <tr>
		     <td> Inlogtype: </td>
			 <td> 
			   ';
			  if ( isset($type) ) {
				echo selectIDtype($type);
			  }
			  else {
				echo selectIDtype(NULL);
			  }
	  // Hier gaat het form weer verder
		echo '
		     </td>
		     <td> 
		       <font color="red">*</font>
		     </td>
           </tr>	
	       <tr>
		     <td> Bedrijf: </td>
		     <td> 
		       <input type="text" name="bedrijf" value="'.$bedrijf.'" size="30" /> 
		     </td>
		     <td> 
			   <font color="red">&nbsp;</font>
			 </td>
		   </tr>
		     <td> Adres: </td>
		     <td> 
		       <input type="text" name="adres" value="'.$adres.'" size="30" /> 
		     </td>
		     <td> 
			   <font color="red">&nbsp;</font>
			 </td>
		   </tr>
		   <tr>
		     <td> Postcode: </td>
		     <td> 
		       <input type="text" name="pcode" value="'.$pcode.'" size="30" /> 
		     </td>
		     <td> 
			   <font color="red">&nbsp;</font>
			 </td>
           </tr>
		   <tr>
             <td> Woonplaats: </td>
		     <td> 
		       <input type="text" name="wplaats" value="'.$wplaats.'" size="30" />
		     </td>
		     <td> 
		       <font color="red">&nbsp;</font>
		     </td>
		   </tr>
		   <tr>
		     <td> Telefoon: </td>
	         <td> 
		       <input type="text" name="phone" value="'.$phone.'" size="30" /> 
		     </td>
		     <td> 
		       <font color="red">&nbsp;</font>
		     </td>
		   </tr>
		   <tr>
		     <td> Fax: </td>
		     <td> 
		       <input type="text" name="fax" value="'.$fax.'" size="30" />
		     </td>
		     <td> 
		       <font color="red">&nbsp;</font>
		     </td>
		   </tr>
		   <tr>
		     <td>Verander wachtwoord: </td>
		     <td> 
		       <input type="password" name="nieuwwachtwoord" size="30" />
		     </td>
		     <td>&nbsp;</td>
		   </tr>
		   <tr>
		     <td> Archief: </td>
		     <td> 
		       <center>
			   <input type="checkbox" name="Archive" '.$archief.' /> 
		     </td>
		     <td> 
		       <font color="red">&nbsp;</font>
		     </td>
		   </tr>
		   <tr>
		     <td colspan="3">
		       <center>
			     <input type="submit" name="submit" value="Aanpassen">
			     <input type="reset" value="Reset">
	             <input type="submit" name="cancel" value="Annuleren">
			   </center>
		     </td>
		   </tr>
	     </table>
	     <input type="hidden" value="'.$ID.'" name="ID">
       </form>
      ';
} 
##End function show_form()		
?>