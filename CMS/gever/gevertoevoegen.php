<?php
/**
*  Docenten toevoegen
*  Deze pagina bevat de functie: show_form_gever()
*  Deze pagina gebruikt de functie:
*     send_mail(), selectIDType()  staat in: inc_functions_beheer.php
*     quote_smart()                staat in: inc_functions.php
*/
//Controleer eerst of er al een session is (!)
if (!isset ($_SESSION['user']))
{
  die ('Hack attempt');
}
else
{	
  ## Else[1] (deze wordt onderin afgesloten)
?>
  <p><b>Docent toevoegen</b></p>
  <?php
  // eerst controleren of er geen submit wordt gedaan
  if (!isset ($_POST['submit']))
  {
    //Als dit niet het geval is: toon formulier
	show_form_gever('','','','','','','','','','','');
  }
  else
  {
	  ## < deze zijn verplicht.
	$gebruikersnaam = $_POST['gebruikersnaam']; ##
    $naam 		= $_POST['naam']; ##
	$vnaam 		= $_POST['vnaam']; 
	$adres 		= $_POST['adres'];
	$pcode 		= $_POST['pcode'];
	$wplaats 	= $_POST['wplaats'];
	$phone 		= $_POST['phone'];
	$fax 		= $_POST['fax'];
	$email 		= $_POST['email']; ##
	$type 		= $_POST['IDType']; ##
	$bedrijf 	= $_POST['bedrijf'];
	
    $pass 	= createRandomPassword();
    $pwd 		= md5($pass);

	//controleer of alle gegevens zijn ingevoerd,
	//Is dit het geval, sla de gegevens op.
	if (!empty ($_POST['naam']))
	{
	  if (!empty ($_POST['gebruikersnaam']))
	  {
	    if (!empty ($_POST['email']))
		{
		  if (!empty ($_POST['IDType']))
          {
		    echo '
			     <p id="goed" style="margin-right:400px;">De volgende gegevens zijn door u ingevoerd: </p>
				 <p id="main">
				 Naam: '.$naam.' <br />
				 Voornaam: '.$vnaam.' <br />
				 ';
			
			if (!empty($adres))
			{ 
			  echo 'Adres: '.$adres.' <br />';
			}
			if (!empty($pcode))
			{ 
			  echo 'Postcode: '.$pcode.' <br />';
			}
			if (!empty($wplaats))
			{ 
			  echo 'Plaats: '.$wplaats.' <br />';
			}
			if (!empty($phone))
			{ 
			  echo'Telefoon nr: '.$phone.' <br />';
			}
			if (!empty($fax))
			{ 
			  echo'Fax: '.$fax.' <br />';
			}
			if (!empty($bedrijf))
			{ 
			  echo'Bedrijf: '.$bedrijf.' <br />';
			}
			  
			echo'
				 <hr>
				 Email adres: '.$email.' <br />
				 Inlogtype: '.$type.' <br />';
				  
			echo'<b>Wachtwoord: '.$pass.' </b><br />';

				 
			//hieronder wordt de Query opgeslagen
            $SQL = "INSERT INTO `tblgever` (`idGever`, `gebruikersnaam`,`Naam`,`gVoornaam`,`Adres`,`Woonplaats`,`Postcode`, 
			        `Telefoon`,`Fax`,`Email`,`Wachtwoord`,`IDtype`,`Bedrijf`) 
			         valueS (NULL , '".quote_smart($gebruikersnaam)."', '".quote_smart($naam)."', '".quote_smart($vnaam)."', 
							'".quote_smart($adres)."', '".quote_smart($wplaats)."', 
							'".quote_smart($pcode)."', '".quote_smart($phone)."', '".quote_smart($fax)."', '".quote_smart($email)."', 
				     		'".quote_smart($pwd)."', '".quote_smart($type)."', '".quote_smart($bedrijf)."');";
				 
			mysql_query($SQL) or die ("Er is een fout opgetreden: <br> " .mysql_error());
				 
			if ($type ==2)
			{
			  $Message = 					
			  $vnaam." ".$naam.",<br>
			  De administrator van Kennisbus.nl heeft u toegevoegd aan de groep intern.<br />
			  U kunt nu inloggen via http://www.kennisbus.nl/CMS/index.php .<br />
			  met de naam: <strong>".$gebruikersnaam."</strong>.<br />
			  en het bijbehorende wachtwoord: <strong>".$pass."</strong>.<br />
			  Vergeet niet uw wachtwoord aan te passen bij de eerste keer aanmelden!<br />
			  Met dit account kunt u informatie toevoegen aan activiteiten waarbij u geplanned staat als docent.<br />
			  Ook kunt u een bestand uploaden bij de desbetreffende activiteit.<br />
	          Tevens is het mogelijk om een presentielijst van leerlingen die zich hebben aangemeld via de website 
			  uit te printen.<br />
			  Vergeet niet het wachtwoord aan te passen via het menu \"Overig\" in het systeem.<br />
			  <br />
			  Met vriendelijke groeten,<br>
			  ".$_SESSION['user'];
				  
			  echo '<hr width="200px">Er is een bericht gestuurd naar '.$naam.', op het email adres: '.$email.'.<br />
				    Het volgende bericht is verstuurd:';
					
			  echo '<table id="tabel">
					 <tr><tr>'.$Message.'</tr></tr></table>';
				   
			  $Subject = " docenten";
			  send_mail($email,$Message,$Subject);
			}
			elseif ($type == 1)
			{
			  $Message =					
			  $vnaam." ".$naam.",<br>
			  De administrator van Kennisbus.nl heeft u toegevoegd aan de groep Administrators. <br />
			  U kunt nu inloggen via http://www.kennisbus.nl/CMS/index.php<br />
			  met de naam: <strong>".$gebruikersnaam.".</strong><br />
			  en het bijbehorende wachtwoord:<strong> ".$pass."</strong><br />
			  Vergeet niet uw wachtwoord aan te passen bij de eerste keer aanmelden!<br />
			  Met dit account kunt u de website 'www.kennisbus.nl' beheren en onderhouden.<br />
			  Vergeet niet het wachtwoord aan te passen via het menu \"Overig\" in het systeem.<br />
			  <br />
			  Met vriendelijke groeten,<br />
			  ".$_SESSION['user'];
			   
			  echo'<hr width="200px">Er is een bericht gestuurd naar '.$naam.', op het email adres: '.$email.'.<br />
			        Het volgende bericht is verstuurd:';
				   
			  echo '<table id="tabel">
					 <tr><tr>'.$Message.'</tr></tr></table>';
				   
			  $Subject = " administrators";
			  send_mail($email,$Message,$Subject);
			}
			else {
			  $Message =					
			  $vnaam." ".$naam.",<br>
			  De administrator van Kennisbus.nl heeft voor u een beheer account aangemaakt voor de Kennisbus. <br />
			  U kunt nu inloggen via http://www.kennisbus.nl/CMS/index.php<br />
			  met de naam: <strong>".$gebruikersnaam.".</strong><br />
			  en het bijbehorende wachtwoord:<strong> ".$pass."</strong><br />
			  Vergeet niet uw wachtwoord aan te passen bij de eerste keer aanmelden! (Links onder via de link Wachtwoord)<br />
			  Met dit account kunt u de activiteiten die op uw naam staan op de kennisbus beheren en onderhouden.<br />
			  <br />
			  Met vriendelijke groeten,<br />
			  ".$_SESSION['user'];
			   
			  echo'<hr width="200px">Er is een bericht gestuurd naar '.$naam.', op het email adres: '.$email.'.<br />
			        Het volgende bericht is verstuurd:';
				   
			  echo '<table id="tabel">
					 <tr><tr>'.$Message.'</tr></tr></table>';
				   
			  $Subject = " docenten";
			  send_mail($email,$Message,$Subject);
			}
		  }
		  else
		  {
		    echo'<div id="fout">Het verplichte veld Type is niet ingevoerd.</div>';
			show_form_gever($gebruikersnaam,$naam, $vnaam, $adres,$pcode,$wplaats,$phone,$fax,$email,$type,$bedrijf);
		  }
		}
		else
		{ 
		  echo'<div id="fout">Het verplichte veld Email is niet ingevoerd.</div>'; 
	      show_form_gever($gebruikersnaam,$naam, $vnaam, $adres,$pcode,$wplaats,$phone,$fax,$email,$type,$bedrijf);
		}
	  }
	  else
	  { 
	    echo'<div id="fout">Het verplichte veld gebruikersnaam is niet ingevoerd.</div>'; 
		show_form_gever($gebruikersnaam,$naam, $vnaam, $adres,$pcode,$wplaats,$phone,$fax,$email,$type,$bedrijf);
	  }
	}
	else
	{ 
	  echo'<div id="fout">Het verplichte veld naam is niet ingevoerd.</div>'; 
	  show_form_gever($gebruikersnaam,$naam, $vnaam, $adres,$pcode,$wplaats,$phone,$fax,$email,$type,$bedrijf);
	}	
  }
  ?>

<?php
} ## hier wordt Else[1] afgesloten

function show_form_gever($gebruikersnaam, $naam, $vnaam, $adres,$pcode,$wplaats,$phone,$fax,$email,$type,$bedrijf)
{
    echo'
		 <div id="main">
		   <form method="POST" action="">
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
				 <td><font color="red">*</font></td>
			   </tr>
			   <tr>
			     <td> Naam: </td>
				 <td> 
				   <input type="text" name="naam" value="'.$naam.'" size="30"> 
				 </td>
				 <td> 
				   <font color="red">*</font>
				 </td>
			   </tr>
			   <tr>
			     <td> Voornaam: </td>
				 <td> 
				   <input type="text" name="vnaam" value="'.$vnaam.'" size="30"> 
				 </td>
				 <td> 
				   <font color="red">*</font>
				 </td>
			   </tr>
			   <tr>
			     <td> Bedrijf: </td>
				 <td> 
				   <input type="text" name="bedrijf" value="'.$bedrijf.'" size="30"> 
				 </td>
				 <td> 
				   &nbsp;
				 </td>
			   </tr>
			   <tr>
			     <td> Adres: </td>
				 <td> 
				   <input type="text" name="adres" value="'.$adres.'" size="30"> 
				 </td>
				 <td> 
				   &nbsp;
				 </td>
			   </tr>
			   <tr>
			     <td> Postcode: </td>
				 <td> 
				   <input type="text" name="pcode" value="'.$pcode.'" maxlength="6" size="30"> 
				 </td>
				 <td> 
				   &nbsp;
				 </td>
			   </tr>
			   <tr>
			     <td> Woonplaats: </td>
				 <td> 
				   <input type="text" name="wplaats" value="'.$wplaats.'" size="30"> 
				 </td>
				 <td> 
				   &nbsp;
				 </td>
			   </tr>
			   <tr>
			     <td> Telefoon: </td>
				 <td> 
				   <input type="text" name="phone" value="'.$phone.'" maxlength="11" size="30"> 
				 </td>
				 <td> 
				   &nbsp;
				 </td>
			   </tr>
			   <tr>
			     <td> Fax: </td>
				 <td> 
				   <input type="text" name="fax" value="'.$fax.'" maxlength="11" size="30"> 
				 </td>
				 <td> 
				   &nbsp;
				 </td>
			   </tr>
			   <tr>
			     <td> Email: </td>
				 <td> 
				   <input type="text" name="email" value="'.$email.'" size="30"> 
				 </td>
				 <td> 
				   <font color="red">*</font>
				 </td>
			   </tr>					
			   <tr>
				 <td> Inlogtype: </td>
				 <td> 
				   ';
				  if ( isset($type) ) {
					echo selectIDType($type);
				  }
				  else {
					echo selectIDType(NULL);
				  }
		  // Hier gaat het form weer verder
			echo '
				 </td>
				 <td> 
				   <font color="red">*</font>
				 </td>
			   </tr>	
			   <tr>
			     <td> Wachtwoord: </td>
				 <td> 
				   <input type="text" name="pwd" value="Automatisch" disabled="disabled" size="30"> 
				 </td>
				 <td> 
				   &nbsp;
				 </td>
			   </tr>
			   <tr>
			     <td colspan="3">
				   <center>
				     <input type="submit" name="submit" value="Toevoegen">
					 <input type="reset" value="Reset">
				   </center>
				 </td>
			   </tr>
			 </table>
			 <br />
			</form>
		   ';
} ##End function show_form()

?>